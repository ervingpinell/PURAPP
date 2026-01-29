<?php

namespace App\Http\Controllers\Admin\Reviews;

use App\Http\Controllers\Controller;
use App\Mail\ReviewReplyNotification;
use App\Models\Booking;
use App\Models\Review;
use App\Models\ReviewReply;
use App\Models\ReviewRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

/**
 * ReviewReplyController
 *
 * Handles reviewreply operations.
 */
class ReviewReplyController extends Controller
{
    /**
     * Form para responder (muestra aviso si no hay destinatario).
     */
    public function create(Review $review)
    {
        $this->authorize('update', $review);

        // Carga segura de relaciones útiles
        $review->loadMissing('user', 'product', 'booking.user', 'booking.product');

        // Resolver destinatario con la misma lógica del store
        [$to, $customerName] = $this->resolveRecipientAndName($review);

        return view('admin.reviews.replies.create', compact('review', 'to'));
    }

    /**
     * Guarda la respuesta y notifica (si hay email).
     */
    public function store(Request $request, Review $review)
    {
        $this->authorize('update', $review);

        $data = $request->validate([
            'body'      => 'required|string|min:2|max:5000',
            'is_public' => 'nullable|boolean',
            'notify'    => 'nullable|boolean',
        ]);

        $admin = $request->user() ?? Auth::user();

        // Pre-carga relaciones útiles
        $review->loadMissing('user', 'product', 'booking.user', 'booking.product');

        // Crear la respuesta (tu tabla usa 'public', NO 'is_public')
        $reply = new ReviewReply();
        $reply->review_id     = $review->getKey();
        $reply->body          = $data['body'];
        $reply->admin_user_id = $admin?->getKey(); // NOT NULL en tu migración

        if (Schema::hasColumn($reply->getTable(), 'public')) {
            $reply->public = (bool)($data['is_public'] ?? true);
        } elseif (Schema::hasColumn($reply->getTable(), 'is_public')) {
            $reply->is_public = (bool)($data['is_public'] ?? true);
        }
        $reply->save();

        // Resolver destinatario y nombre del cliente
        [$to, $customerName] = $this->resolveRecipientAndName($review);

        // Enviar notificación si procede
        $shouldNotify = array_key_exists('notify', $data) ? (bool)$data['notify'] : true;

        if ($shouldNotify && $to) {
            // Resolver idioma desde la reserva (Booking -> TourLanguage)
            $locale = null;
            if ($review->booking_id) {
                // Asegurar que la reserva y su idioma estén cargados
                $bk = $review->relationLoaded('booking')
                    ? $review->booking
                    : Booking::with('productLanguage')->find($review->booking_id);

                if ($bk && $bk->productLanguage) {
                    $langName = strtolower($bk->productLanguage->name);

                    // Mapeo simple de nombres a códigos ISO
                    if (str_contains($langName, 'esp') || str_contains($langName, 'span')) {
                        $locale = 'es';
                    } elseif (str_contains($langName, 'ing') || str_contains($langName, 'eng')) {
                        $locale = 'en';
                    } elseif (str_contains($langName, 'fra') || str_contains($langName, 'fre')) {
                        $locale = 'fr';
                    } elseif (str_contains($langName, 'ale') || str_contains($langName, 'ger') || str_contains($langName, 'deu')) {
                        $locale = 'de';
                    } elseif (str_contains($langName, 'por')) {
                        $locale = 'pt';
                    }
                }
            }

            // Fallback: idioma guardado en la reseña (si existe la columna)
            if (!$locale && Schema::hasColumn($review->getTable(), 'language')) {
                $locale = $review->language;
            }

            // Definir nombre de quien responde (User pidió usar Company Name)
            // Se usa config o string fijo
            $adminName = config('app.name', 'Company Name');

            // Resolver nombre del producto TRADUCIDO
            // 1. Intentar traducción con el locale detectado
            // 2. Fallback al nombre default del producto
            $productName = null;
            if ($review->product) {
                $productTranslation = $review->product->translate($locale);
                $productName = optional($productTranslation)->name ?? $review->product->name;
            } elseif ($review->booking && $review->booking->product) {
                // Caso raro donde review no tiene product_id directo pero booking sí (aunque normalmente van juntos)
                $productTranslation = $review->booking->product->translate($locale);
                $productName = optional($productTranslation)->name ?? $review->booking->product->name;
            }

            Mail::to(new Address($to, $customerName ?: null))
                ->queue(new ReviewReplyNotification($reply, $adminName, $productName, $customerName, $locale));

            return redirect()
                ->route('admin.reviews.replies.thread', $review)
                ->with('ok', __('reviews.replies.saved_notified', ['email' => $to]));
        }

        return redirect()
            ->route('admin.reviews.replies.thread', $review)
            ->with('ok', __('reviews.replies.saved_no_email'));
    }

    /**
     * Hilo de la reseña (review + respuestas).
     */
    public function thread(Review $review)
    {
        $this->authorize('view', $review);

        $review->load([
            'replies' => fn($q) => $q->orderBy('created_at'),
            'replies.admin:user_id,first_name,last_name',
            'user:user_id,first_name,last_name,email',
            'booking.product',
        ]);

        return view('admin.reviews.replies.thread', compact('review'));
    }

    public function destroy(Review $review, ReviewReply $reply)
    {
        $this->authorize('delete', $reply);
        $reply->delete();

        return back()->with('ok', __('reviews.replies.deleted'));
    }

    public function toggle(Review $review, ReviewReply $reply)
    {
        $this->authorize('update', $reply);

        if (Schema::hasColumn($reply->getTable(), 'public')) {
            $reply->public = ! (bool) $reply->public;
        } else {
            $reply->is_public = ! (bool) $reply->is_public;
        }
        $reply->save();

        return back()->with('ok', __('reviews.replies.visibility_ok'));
    }

    /**
     * ===============================
     * Helper: resuelve destinatario y nombre del cliente
     * ===============================
     * Orden de resolución del email:
     * 1) author_email / email en la review (si existen)
     * 2) email del usuario asociado a la review
     * 3) email de la reserva asociada (user->email o customer_email/email)
     * 4) email de la última ReviewRequest del mismo user/product
     *
     * Orden de resolución del nombre:
     * 1) author_name de la review
     * 2) full_name/name del usuario de la review
     * 3) customer_name o user->full_name/name de la reserva
     * 4) full_name del user o customer_name de la ReviewRequest
     */
    private function resolveRecipientAndName(Review $review): array
    {
        $email = null;
        $name  = null;

        // 1) Email directo en review (columna flexible)
        if (Schema::hasColumn($review->getTable(), 'author_email') && filled($review->author_email)) {
            $email = $review->author_email;
        } elseif (Schema::hasColumn($review->getTable(), 'email') && filled($review->email)) {
            $email = $review->email;
        }

        // 2) Usuario asociado
        if (!$email && $review->user_id) {
            $email = optional($review->user)->email ?: optional(User::find($review->user_id))->email;
        }

        // 3) Reserva asociada
        if (!$email && $review->booking_id) {
            $bk = $review->relationLoaded('booking') ? $review->booking : Booking::with('user')->find($review->booking_id);
            if ($bk) {
                $email = optional($bk->user)->email ?: ($bk->customer_email ?? $bk->email ?? null);
            }
        }

        // 4) Última ReviewRequest del mismo user/tour
        if (!$email) {
            $rr = ReviewRequest::query()
                ->when($review->user_id, fn($q) => $q->where('user_id', $review->user_id))
                ->when($review->product_id, fn($q) => $q->where('product_id', $review->product_id))
                ->orderByDesc('created_at')
                ->first();
            if ($rr && $rr->email) {
                $email = $rr->email;
            }
        }

        // ===== Nombre =====
        if (Schema::hasColumn($review->getTable(), 'author_name') && filled($review->author_name)) {
            $name = $review->author_name;
        }

        if (!$name && $review->user_id) {
            $name = optional($review->user)->full_name
                ?? optional(User::find($review->user_id))->full_name
                ?? optional($review->user)->name;
        }

        if (!$name && $review->booking_id) {
            $bk = $review->relationLoaded('booking') ? $review->booking : Booking::with('user')->find($review->booking_id);
            if ($bk) {
                $name = $bk->customer_name
                    ?? optional($bk->user)->full_name
                    ?? optional($bk->user)->name;
            }
        }

        if (!$name) {
            $rr = ReviewRequest::query()
                ->when($review->user_id, fn($q) => $q->where('user_id', $review->user_id))
                ->when($review->product_id, fn($q) => $q->where('product_id', $review->product_id))
                ->orderByDesc('created_at')
                ->first();
            if ($rr) {
                $name = optional($rr->user)->full_name ?: $rr->customer_name;
            }
        }

        return [$email, $name];
    }
}
