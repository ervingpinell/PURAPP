<?php

namespace App\Http\Controllers\Reviews;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PublicReviewController extends Controller
{
    /**
     * Muestra el formulario público de reseña a partir del token.
     */
    public function show(string $token)
    {
        /** @var \App\Models\ReviewRequest|null $rr */
        $rr = ReviewRequest::with(['booking.tour', 'user'])
            ->where('token', $token)
            ->first();

        if (!$rr) {
            abort(404, __('reviews.public.not_found'));
        }

        // Si expiró, actualiza estado y muestra vista de expirado
        if ($rr->expires_at && $rr->expires_at->isPast()) {
            if ($rr->status !== 'expired') {
                $rr->status = 'expired';
                $rr->save();
            }
            return view('reviews.request-expired');
        }

        // Si ya fue usada/cumplida, muestra vista de usado
        if (in_array($rr->status, ['fulfilled'], true)) {
            return view('reviews.request-used');
        }

        // Mostrar formulario
        return view('reviews.request-form', [
            'rr'    => $rr,
            'tour'  => optional($rr->booking)->tour,
            'user'  => $rr->user,
            'token' => $token,
        ]);
    }

    /**
     * Recibe el POST del formulario público de reseñas.
     * - Valida inputs
     * - Evita duplicados (mismo tour + mismo user_id o misma booking_id)
     * - Crea la reseña "local" como pendiente/no pública
     * - Marca la ReviewRequest como cumplida
     */
    public function submit(Request $request, string $token)
    {
        /** @var \App\Models\ReviewRequest|null $rr */
        $rr = ReviewRequest::with(['booking.tour', 'user'])
            ->where('token', $token)
            ->first();

        if (!$rr) {
            abort(404, __('reviews.public.not_found'));
        }

        // Si el enlace está vencido, redirige suave a "gracias"
        if ($rr->expires_at && $rr->expires_at->isPast()) {
            if ($rr->status !== 'expired') {
                $rr->status = 'expired';
                $rr->save();
            }
            return redirect()
                ->route('reviews.thanks')
                ->with('ok', __('reviews.public.expired'));
        }

        // Validación del formulario
        $validated = $request->validate([
            'rating'       => 'required|integer|min:1|max:5',
            'title'        => 'nullable|string|max:150',
            'body'         => 'required|string|min:5|max:5000',
            'author_name'  => 'nullable|string|max:100',
            'language'     => 'nullable|string|max:8',
        ]);

        // Anti-duplicados: misma reserva o mismo user+tour
        $already = Review::query()
            ->where('provider', 'local')
            ->where('tour_id', $rr->tour_id)
            ->where(function ($q) use ($rr) {
                if ($rr->booking_id) {
                    $q->orWhere('booking_id', $rr->booking_id);
                }
                if ($rr->user_id) {
                    $q->orWhere('user_id', $rr->user_id);
                }
            })
            ->exists();

        if ($already) {
            // Marca la solicitud como cumplida para no insistir
            if ($rr->status !== 'fulfilled') {
                $rr->status = 'fulfilled';
                $rr->save();
            }
            return redirect()
                ->route('reviews.thanks')
                ->with('ok', __('reviews.public.thanks_dup'));
        }

        // Construye datos de la reseña (no pública y pendiente)
        $authorName = $validated['author_name']
            ?? optional($rr->user)->full_name
            ?? optional($rr->booking)->customer_name
            ?? null; // si quieres un texto, crea la clave reviews.public.guest y úsala aquí

        $language = $validated['language'] ?? app()->getLocale() ?? 'es';

        $payload = [
            'provider'    => 'local',
            'tour_id'     => (int) $rr->tour_id,
            'booking_id'  => $rr->booking_id,       // vínculo para notificaciones futuras
            'user_id'     => $rr->user_id,          // puede ser null
            'rating'      => (int) $validated['rating'],
            'title'       => $validated['title'] ?? null,
            'body'        => $validated['body'],
            'language'    => $language,
            'author_name' => $authorName,
            'is_public'   => false,                 // admin decide publicar
            'status'      => 'pending',             // en moderación
        ];

        // Si tu esquema tiene is_verified, marcamos como verificada por booking
        if (Schema::hasColumn('reviews', 'is_verified')) {
            $payload['is_verified'] = true;
        }

        Review::create($payload);

        // Marcar la solicitud como cumplida
        if ($rr->status !== 'fulfilled') {
            $rr->status = 'fulfilled';
            $rr->save();
        }

        return redirect()
            ->route('reviews.thanks')
            ->with('ok', __('reviews.public.thanks'));
    }
}
