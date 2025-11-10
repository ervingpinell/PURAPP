<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Policy;
use App\Models\PolicySection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\Policies\PolicySnapshotService;

class PublicCheckoutController extends Controller
{
    /** ===================== Helpers ===================== */

    private function findActiveCartForUser(?int $userId): ?Cart
    {
        if (!$userId) return null;

        return Cart::query()
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->with([
                'items.tour',
                'items.schedule',
                'items.language',
                'items.hotel',
                'items.meetingPoint',
            ])
            ->latest('updated_at')
            ->first();
    }

    /**
     * Devuelve el primer registro de traducción por locale con fallback.
     * Acepta colecciones Eloquent de traducciones que tengan campo 'locale'.
     */
    private function pickTranslation($translations, string $locale, string $fallback)
    {
        $norm = fn ($v) => str_replace('-', '_', strtolower((string)$v));

        $bag = collect($translations ?? []);
        $locNorm = $norm($locale);
        $fbNorm  = $norm($fallback);

        // exacta
        if ($t = $bag->first(fn($x) => $norm($x->locale) === $locNorm)) return $t;

        // variantes comunes (en, pt_BR, etc.)
        $alts = [$locale, str_replace('_','-',$locale), substr($locale,0,2), 'pt_BR', 'pt-br'];
        foreach ($alts as $alt) {
            if ($t = $bag->first(fn($x) => $norm($x->locale) === $norm($alt))) return $t;
        }

        // fallback y fallback corto
        if ($t = $bag->first(fn($x) => $norm($x->locale) === $fbNorm)) return $t;
        if ($t = $bag->first(fn($x) => $norm($x->locale) === $norm(substr($fallback,0,2)))) return $t;

        // primero disponible
        return $bag->first();
    }

    /**
     * Convierte un slug a una "key" canónica para el bloque (terms, privacy, etc.).
     */
    private function canonicalKeyFromSlug(?string $slug): ?string
    {
        if (!$slug) return null;
        $s = Str::of($slug)->lower()->replace(' ', '-')->replace('_', '-')->toString();

        // Normalizaciones típicas
        $map = [
            'terms'             => ['terms', 'terminos', 't-and-c', 'tyc', 'terms-conditions', 'terms-and-conditions'],
            'privacy'           => ['privacy', 'privacidad', 'privacy-policy'],
            'cancellation'      => ['cancellation', 'cancelacion', 'cancellation-policy'],
            'refunds'           => ['refunds', 'devoluciones', 'refund-policy'],
            'warranty'          => ['warranty', 'garantia', 'guarantee'],
            'payments'          => ['payments', 'metodos-de-pago', 'payment-methods'],
        ];
        foreach ($map as $key => $alts) {
            foreach ($alts as $alt) {
                if ($s === $alt || Str::contains($s, $alt)) {
                    return $key;
                }
            }
        }
        // Si no coincide, regresa un slug “limpio” como key
        return Str::slug($s);
    }

    /**
     * Construye bloques de políticas directamente desde BD.
     * Retorna [ 'blocks' => array, 'versions' => ['terms'=>?, 'privacy'=>?] ]
     */
    private function buildPolicyBlocksFromDB(string $locale, string $fallback): array
    {
        // Traer políticas activas con sus secciones activas + traducciones
        $policies = Policy::query()
            ->with([
                'translations',
                'sections' => function ($q) {
                    $q->orderBy('sort_order')->orderBy('section_id');
                },
                'sections.translations',
            ])
            ->where('is_active', true)
            ->orderBy('policy_id')
            ->get();

        $blocks = [];
        $versions = ['terms' => null, 'privacy' => null];

        foreach ($policies as $p) {
            $pTr = $this->pickTranslation($p->translations, $locale, $fallback);

            // HTML armado con secciones activas
            $htmlSections = [];
            foreach ($p->sections ?? [] as $sec) {
                if (!$sec->is_active) continue;
                $sTr = $this->pickTranslation($sec->translations, $locale, $fallback);
                $title   = trim((string)($sTr->name ?? ''));
                $content = (string)($sTr->content ?? '');

                if ($title !== '') {
                    // título como <h4> para consistencia con tu estilo
                    $htmlSections[] = '<h4>' . e($title) . '</h4>';
                }
                if ($content !== '') {
                    $htmlSections[] = $content; // ya viene HTML administrado
                }
            }

            // Si no hubo secciones con contenido, saltamos
            if (empty($htmlSections)) continue;

            // Título del bloque (el de la policy)
            $blockTitle = (string)($pTr->name ?? ($p->slug ?? ''));
            // Versionado simple: si la policy tiene rango de vigencia, arma una "v" legible
            $version = null;
            if (!empty($p->effective_from) || !empty($p->effective_to)) {
                $from = $p->effective_from ? Carbon::parse($p->effective_from)->format('Y-m-d') : '—';
                $to   = $p->effective_to   ? Carbon::parse($p->effective_to)->format('Y-m-d') : '—';
                $version = "v {$from} → {$to}";
            }

            $key = $this->canonicalKeyFromSlug($p->slug);
            // si no hubo forma de deducir, crea algo estable
            if (!$key) $key = 'policy_' . $p->policy_id;

            $blocks[] = [
                'key'     => $key,
                'title'   => $blockTitle ?: Str::title(Str::of($key)->replace('-', ' ')),
                'version' => $version ?: 'v1',
                'html'    => implode("\n", $htmlSections),
            ];

            // Guardamos versiones “oficiales” para terms/privacy si aplica
            if ($key === 'terms'  && !$versions['terms'])   $versions['terms']   = $version ?: 'v1';
            if ($key === 'privacy'&& !$versions['privacy']) $versions['privacy'] = $version ?: 'v1';
        }

        return [
            'blocks'   => $blocks,
            'versions' => $versions,
        ];
    }

    /**
     * Calcula cutoff de cancelación gratuita: 24 h antes del inicio más cercano.
     */
    private function computeFreeCancelUntil(Cart $cart): ?Carbon
    {
        $tz = config('app.timezone', 'America/Costa_Rica');

        $starts = $cart->items->map(function ($it) use ($tz) {
            $date = $it->tour_date ?? null;
            $time = optional($it->schedule)->start_time;
            if (!$date || !$time) return null;
            return Carbon::parse("{$date} {$time}", $tz);
        })->filter();

        return $starts->isNotEmpty() ? $starts->min()->copy()->subHours(24) : null;
    }

    /** ===================== Acciones ===================== */

    public function show(Request $request, PolicySnapshotService $svc)
    {
        $userId = Auth::id();
        if (!$userId) return redirect()->route('login');

        $cart = $this->findActiveCartForUser($userId);
        $itemsCount = $cart ? $cart->items()->count() : 0;
        if (!$cart || $itemsCount === 0) {
            return redirect()->route('public.carts.index')
                ->with('error', __('adminlte::adminlte.emptyCart'));
        }

        $locale   = app()->getLocale();
        $fallback = (string) config('app.fallback_locale', 'es');

        // 1) Intentar armar desde BD
        $dbPack   = $this->buildPolicyBlocksFromDB($locale, $fallback);
        $blocks   = $dbPack['blocks'] ?? [];
        $versions = $dbPack['versions'] ?? ['terms'=>null, 'privacy'=>null];

        // 2) Snapshot de config como fallback
        $cfgPack = $svc->make();

        // Usamos BD si hay algo, si no, usamos config
        $useDB = !empty($blocks);

        // Calcular cutoff de cancelación
        $freeCancelUntil = $this->computeFreeCancelUntil($cart);

        return view('public.checkout', [
            'cart'            => $cart,

            // Para el include de content.blade.php:
            'policyBlocks'    => $useDB ? $blocks : null,

            // Versiones visibles
            'termsVersion'    => $useDB
                                  ? ($versions['terms']   ?? 'v1')
                                  : ($cfgPack['versions']['terms']   ?? 'v1'),
            'privacyVersion'  => $useDB
                                  ? ($versions['privacy'] ?? 'v1')
                                  : ($cfgPack['versions']['privacy'] ?? 'v1'),

            // Para el chip dinámico "Cancelación gratuita hasta :time el :date"
            'freeCancelUntil' => $freeCancelUntil,

            // También pasamos el snapshot por compatibilidad (no lo usará el include si hay policyBlocks)
            'policies'        => $cfgPack['snapshot'] ?? [],
        ]);
    }

    public function process(Request $request, PolicySnapshotService $svc)
    {
        $userId = Auth::id();
        if (!$userId) return redirect()->route('login');

        $cart = $this->findActiveCartForUser($userId);
        if (!$cart || $cart->items()->count() === 0) {
            return redirect()->route('public.carts.index')
                ->with('error', __('adminlte::adminlte.emptyCart'));
        }

        $request->validate([
            'accept_terms' => ['required', 'accepted'],
            'scroll_ok'    => ['required','in:1'],
        ], [
            'accept_terms.required' => __('Debes aceptar los Términos y Políticas para continuar'),
            'accept_terms.accepted' => __('Debes aceptar los Términos y Políticas para continuar'),
        ]);

        $locale   = app()->getLocale();
        $fallback = (string) config('app.fallback_locale', 'es');

        // Intentar snapshot desde BD
        $dbPack   = $this->buildPolicyBlocksFromDB($locale, $fallback);
        $blocks   = $dbPack['blocks'] ?? [];
        $versions = $dbPack['versions'] ?? ['terms'=>null, 'privacy'=>null];

        // Fallback a config si BD vacío
        $cfgPack  = $svc->make();
        $useDB    = !empty($blocks);

        // Lo que persistimos como snapshot:
        // - Si hay BD: guardamos los bloques renderizados (array)
        // - Si no hay BD: guardamos el snapshot de config (strings)
        $persistSnapshot = $useDB ? $blocks : ($cfgPack['snapshot'] ?? []);
        // Para versiones “oficiales”
        $termsVersion   = $useDB ? ($versions['terms']   ?? 'v1') : ($cfgPack['versions']['terms']   ?? 'v1');
        $privacyVersion = $useDB ? ($versions['privacy'] ?? 'v1') : ($cfgPack['versions']['privacy'] ?? 'v1');

        // Hash determinístico del contenido persistido
        $normalized = is_array($persistSnapshot)
            ? preg_replace('/\s+/', ' ', json_encode($persistSnapshot, JSON_UNESCAPED_UNICODE) ?: '')
            : preg_replace('/\s+/', ' ', (string) implode('|', (array) $persistSnapshot));
        $sha = hash('sha256', (string) $normalized);

        $cart->forceFill([
            'terms_accepted_at' => now(),
            'terms_version'     => $termsVersion,
            'privacy_version'   => $privacyVersion,
            'terms_ip'          => $request->ip(),
            'policies_snapshot' => $persistSnapshot,
            'policies_sha256'   => $sha,
        ])->save();

        DB::table('terms_acceptances')->insert([
            'user_id'           => $userId,
            'cart_ref'          => $cart->cart_id ?? $cart->id ?? null,
            'booking_ref'       => null,
            'accepted_at'       => now(),
            'terms_version'     => $termsVersion,
            'privacy_version'   => $privacyVersion,
            'policies_snapshot' => is_array($persistSnapshot)
                                    ? json_encode($persistSnapshot, JSON_UNESCAPED_UNICODE)
                                    : json_encode((array) $persistSnapshot, JSON_UNESCAPED_UNICODE),
            'policies_sha256'   => $sha,
            'ip_address'        => $request->ip(),
            'user_agent'        => (string) $request->userAgent(),
            'locale'            => $locale,
            'timezone'          => config('app.timezone'),
            'consent_source'    => 'checkout',
            'referrer'          => $request->headers->get('referer'),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // Continuar con la reserva
        return app(\App\Http\Controllers\Bookings\BookingController::class)->storeFromCart($request);
    }
}
