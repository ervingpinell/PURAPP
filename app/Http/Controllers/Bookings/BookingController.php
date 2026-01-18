<?php

namespace App\Http\Controllers\Bookings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\PromoCode;
use App\Models\Cart;
use App\Models\Tour;

use App\Services\Bookings\{BookingCreator, BookingCapacityService};

/**
 * BookingController
 *
 * Handles booking management and display.
 */
class BookingController extends Controller
{
    public function __construct(
        private BookingCreator $creator,
        private BookingCapacityService $capacity,
        private \App\Services\Auth\PasswordSetupService $passwordSetupService
    ) {}

    /** Checkout desde carrito (público) */
    public function storeFromCart(Request $request)
    {
        $user = Auth::user();

        // 👉 CORREGIDO: Leer notas desde input O sesión
        $notes = trim((string) ($request->input('notes') ?? session('checkout_notes', '')));

        // Rate limit para evitar doble submit
        $key = 'booking:cart:' . $user->user_id;
        if (RateLimiter::tooManyAttempts($key, 1)) {
            return redirect()->route(app()->getLocale() . '.home')
                ->with('cart_expired', true);
        }
        RateLimiter::hit($key, 10);

        // ===== Crear reservas dentro de transacción =====
        $createdBookings = DB::transaction(function () use ($user, $request, $notes) {
            $cart = \App\Models\Cart::with(['items.tour.prices.category', 'items.schedule'])
                ->where('user_id', $user->user_id)
                ->where('is_active', true)
                ->orderByDesc('cart_id')
                ->first();

            if (!$cart || $cart->items->isEmpty()) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'cart' => __('carts.messages.cart_empty')
                ]);
            }

            if ($cart->isExpired()) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'cart' => __('carts.messages.cart_expired')
                ]);
            }

            // Pre-validación por grupos (tour+fecha+horario)
            $groups = $cart->items->groupBy(fn($i) => $i->tour_id . '_' . $i->tour_date . '_' . $i->schedule_id);

            foreach ($groups as $items) {
                $first      = $items->first();
                $tour       = $first->tour;
                $tourDate   = $first->tour_date;
                $scheduleId = $first->schedule_id;

                $schedule = $tour->schedules()
                    ->where('schedules.schedule_id', $scheduleId)
                    ->where('schedules.is_active', true)
                    ->wherePivot('is_active', true)
                    ->first();

                if (!$schedule) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'schedule_id' => __("carts.messages.schedule_unavailable"),
                    ]);
                }

                // Pax total desde JSON categories del carrito
                $totalPax = $items->sum(function ($item) {
                    return collect($item->categories ?? [])->sum('quantity');
                });

                $remaining = $this->capacity->remainingCapacity(
                    $tour,
                    $schedule,
                    $tourDate,
                    excludeBookingId: null,
                    countHolds: true,
                    excludeCartId: (int) $cart->cart_id
                );

                if ($totalPax > $remaining) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'capacity' => __("m_bookings.messages.limited_seats_available", [
                            'available' => $remaining,
                            'tour'      => $tour->getTranslatedName(),
                            'date'      => \Carbon\Carbon::parse($tourDate)->translatedFormat('M d, Y')
                        ]),
                    ]);
                }
            }

            // Promo code (de input o sesión pública)
            $promoCodeValue   = $request->input('promo_code') ?? $request->session()->get('public_cart_promo.code');
            $promoCodeToApply = null;

            if ($promoCodeValue) {
                $clean = \App\Models\PromoCode::normalize($promoCodeValue);
                $promoCodeToApply = \App\Models\PromoCode::whereRaw("TRIM(REPLACE(code, ' ', '')) = ?", [$clean])
                    ->where(function ($q) {
                        $q->where('is_used', false)->orWhereNull('is_used');
                    })
                    ->lockForUpdate()
                    ->first();

                if ($promoCodeToApply && method_exists($promoCodeToApply, 'isValidToday') && !$promoCodeToApply->isValidToday()) {
                    $promoCodeToApply = null;
                }
                if ($promoCodeToApply && method_exists($promoCodeToApply, 'hasRemainingUses') && !$promoCodeToApply->hasRemainingUses()) {
                    $promoCodeToApply = null;
                }
            }

            $created      = [];
            $promoApplied = false;

            // Crear cada booking desde cada ítem del carrito
            foreach ($cart->items as $item) {
                // categories (snapshot JSON) -> mapa id => qty
                $quantities = [];
                foreach ((array) ($item->categories ?? []) as $cat) {
                    $qid = (int)($cat['category_id'] ?? 0);
                    $qq  = (int)($cat['quantity'] ?? 0);
                    if ($qid > 0 && $qq > 0) {
                        $quantities[$qid] = $qq;
                    }
                }

                if (empty($quantities)) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'categories' => __('m_bookings.validation.no_active_categories'),
                    ]);
                }

                $payload = [
                    'user_id'           => $user->user_id,
                    'tour_id'           => $item->tour_id,
                    'schedule_id'       => $item->schedule_id,
                    'tour_language_id'  => $item->tour_language_id,
                    'tour_date'         => $item->tour_date,
                    'booking_date'      => now(),
                    'categories'        => $quantities,
                    // Pickup
                    'hotel_id'          => $item->is_other_hotel ? null : $item->hotel_id,
                    'is_other_hotel'    => (bool) $item->is_other_hotel,
                    'other_hotel_name'  => $item->is_other_hotel ? $item->other_hotel_name : null,
                    // Otros
                    'status'            => 'pending',
                    'meeting_point_id'  => $item->meeting_point_id,
                    // 👉 CORREGIDO: Usar las notas capturadas
                    'notes'             => $notes !== '' ? $notes : null,
                    'promo_code'        => $promoApplied ? null : ($promoCodeToApply?->code),
                    'exclude_cart_id'   => (int) $cart->cart_id,
                ];

                $booking = $this->creator->create($payload, validateCapacity: true, countHolds: true);

                if (!$promoApplied && $promoCodeToApply) {
                    $promoCodeToApply->redeemForBooking($booking->booking_id, $user->user_id);
                    $promoApplied = true;
                }

                $created[] = $booking;
            }

            // 🔄 NUEVO FLUJO: Marcar items como reservados en lugar de eliminarlos
            // Esto permite que el usuario pueda volver si abandona el pago
            $reservationToken = \Illuminate\Support\Str::random(32);
            $cart->items()->update([
                'is_reserved' => true,
                'reserved_at' => now(),
                'reservation_token' => $reservationToken,
            ]);

            // Guardar token de reserva en sesión para limpieza posterior
            $request->session()->put('cart_reservation_token', $reservationToken);

            // NO desactivar el carrito aún - se hará después del pago exitoso
            // $cart->update(['is_active' => false, 'expires_at' => now()]);

            $request->session()->forget('public_cart_promo');
            // 👉 NUEVO: Limpiar notas de sesión después de crear bookings
            $request->session()->forget('checkout_notes');

            return $created;
        });

        // ===== Store booking IDs in session for payment =====
        $bookingIds = collect($createdBookings)->pluck('booking_id')->toArray();

        // Store in session for payment page
        $request->session()->put('pending_booking_ids', $bookingIds);

        // 🔄 NUEVO: Limpiar rate limiter después de crear bookings exitosamente
        $key = 'booking:cart:' . $user->user_id;
        RateLimiter::clear($key);

        // Redirect to payment page
        return redirect()->route('payment.show')
            ->with('info', __('Please complete payment to confirm your booking'));
    }

    /** Mis reservas (público) */
    public function myBookings()
    {
        $bookings = Booking::with([
            'user',
            'tour.prices.category',
            'detail.hotel',
            'detail.meetingPoint.translations',
            'detail.schedule',
            'payments'
        ])
            ->where('user_id', Auth::id())
            ->orderByDesc('booking_date')
            ->get();

        return view('customer.bookings.index', compact('bookings'));
    }

    /** Recibo PDF (público) */
    public function downloadReceiptPdf(Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        $booking->load([
            'detail.schedule',
            'detail.hotel',
            'detail.meetingPoint',
            'user',
            'tour.prices.category',
            'payments'
        ]);

        $locale = app()->getLocale();

        // Mapa de nombres de categorías traducidos para el PDF
        $categoryNamesById = [];
        if ($booking->tour && $booking->tour->relationLoaded('prices')) {
            $categoryNamesById = $booking->tour->prices->mapWithKeys(function ($p) use ($locale) {
                $cat  = $p->category;
                $name = method_exists($cat, 'getTranslatedName')
                    ? ($cat->getTranslatedName($locale) ?: ($cat->name ?? null))
                    : ($cat->name ?? null);

                if (!$name && ($slug = $cat->slug ?? null)) {
                    foreach (
                        [
                            "customer_categories.labels.$slug",
                            "m_tours.customer_categories.labels.$slug",
                        ] as $key
                    ) {
                        $tr = __($key);
                        if ($tr !== $key) {
                            $name = $tr;
                            break;
                        }
                    }
                }

                if (!$name) {
                    $name = 'Category #' . (int)$p->category_id;
                }

                return [$p->category_id => $name];
            })->toArray();
        }

        // Calcular totales desde snapshot de categorías
        $detail       = $booking->detail;
        $cats         = collect($detail->categories ?? []);
        $totalPersons = $cats->sum('quantity');

        $client = preg_replace('/[^A-Za-z0-9_]/', '_', $booking->user->full_name ?? 'Client');
        $code   = $booking->booking_reference;

        $pdf = Pdf::loadView('admin.bookings.receipt', compact(
            'booking',
            'categoryNamesById',
            'totalPersons'
        ));

        return $pdf->download("Receipt_{$client}_{$code}.pdf");
    }

    /** Update de una reserva por el cliente (público) */
    public function update(Request $request, Booking $booking)
    {
        // 1) Seguridad: solo el dueño puede editar
        abort_unless($booking->user_id === Auth::id(), 403);

        // 2) Reglas globales de modificación
        $allowMod  = (bool) config('booking.allow_modification', true);
        $hoursBefore = (int) config('booking.modification_hours_before', 48);
        if (!$allowMod) {
            return back()->withInput()->withErrors(['update' => __('m_bookings.bookings.errors.modifications_disabled')]);
        }

        // Cargar detalle + tour
        $booking->loadMissing(['detail', 'tour.prices.category']);
        $detail = $booking->detail;
        if (!$detail) {
            return back()->withErrors(['detail' => __('m_bookings.bookings.errors.detail_not_found')]);
        }

        // 3) Evitar cambios demasiado cerca del inicio (si conocemos el start)
        $scheduleStart = null;
        if ($detail->schedule_id) {
            $scheduleStart = \App\Models\Schedule::find($detail->schedule_id)?->start_time;
        }
        $startDateTime = null;
        if ($detail->tour_date && $scheduleStart) {
            try {
                $startDateTime = \Carbon\Carbon::parse($detail->tour_date . ' ' . $scheduleStart);
            } catch (\Throwable $e) {
                $startDateTime = null;
            }
        } elseif ($detail->tour_date) {
            $startDateTime = \Carbon\Carbon::parse($detail->tour_date)->startOfDay();
        }

        if ($startDateTime && now()->diffInHours($startDateTime, false) < $hoursBefore) {
            return back()->withInput()->withErrors([
                'tour_date' => __('m_bookings.bookings.errors.modification_window_passed', ['hours' => $hoursBefore])
            ]);
        }

        // 4) Validación de input (similar a admin, pero sin permitir cambio de user_id)
        $validated = $request->validate([
            'tour_id'           => 'required|exists:tours,tour_id',
            'schedule_id'       => 'required|exists:schedules,schedule_id',
            'tour_language_id'  => 'required|exists:tour_languages,tour_language_id',
            'tour_date'         => 'required|date|after:today',
            'categories'        => 'required|array|min:1',
            'categories.*'      => 'required|integer|min:0',
            'meeting_point_id'  => 'nullable|integer|exists:meeting_points,id',
            'hotel_id'          => 'nullable|integer|exists:hotels_list,hotel_id',
            'is_other_hotel'    => 'nullable|boolean',
            'other_hotel_name'  => 'nullable|string|max:255|required_if:is_other_hotel,1',
            'notes'             => 'nullable|string|max:1000',
            'promo_code'        => 'nullable|string|max:100',
        ]);

        // Normalizar pickup
        $in = $validated;
        $in['is_other_hotel'] = (bool)($in['is_other_hotel'] ?? false);
        if (!empty($in['meeting_point_id'])) {
            $in['is_other_hotel']   = false;
            $in['other_hotel_name'] = null;
            $in['hotel_id']         = null;
        } elseif (!empty($in['other_hotel_name'])) {
            $in['is_other_hotel'] = true;
            $in['hotel_id']       = null;
        }

        // 5) Validación cuantitativa + capacidad
        $newTour = Tour::with('prices.category')->findOrFail((int)$in['tour_id']);
        $newSchedule = $newTour->schedules()
            ->where('schedules.schedule_id', (int)$in['schedule_id'])
            ->where('schedules.is_active', true)
            ->wherePivot('is_active', true)
            ->first();

        if (!$newSchedule) {
            return back()->withInput()->withErrors(['schedule_id' => __('carts.messages.schedule_unavailable')]);
        }

        $validation = app(\App\Services\Bookings\BookingValidationService::class)->validateQuantities($newTour, $in['categories']);
        if (!$validation['valid']) {
            return back()->withInput()->withErrors(['categories' => implode(' ', $validation['errors'])]);
        }

        $totalPax = array_sum($in['categories']);
        $maxTotal = (int) config('booking.max_persons_per_booking', 12);
        if ($totalPax > $maxTotal) {
            return back()->withInput()->withErrors([
                'categories' => __('m_bookings.bookings.validation.max_persons_total', ['max' => $maxTotal])
            ]);
        }

        $cap = app(BookingCapacityService::class)->capacitySnapshot(
            $newTour,
            $newSchedule,
            $in['tour_date'],
            excludeBookingId: (int)$booking->booking_id,
            countHolds: true
        );
        if ($totalPax > $cap['available']) {
            return back()->withInput()->withErrors([
                'capacity' => __('m_bookings.bookings.errors.insufficient_capacity', [
                    'tour'      => $newTour->name,
                    'date'      => \Carbon\Carbon::parse($in['tour_date'])->translatedFormat('M d, Y'),
                    'time'      => \Carbon\Carbon::parse($newSchedule->start_time)->format('g:i A'),
                    'requested' => $totalPax,
                    'available' => $cap['available'],
                    'max'       => $cap['max'],
                ])
            ]);
        }

        // 6) Snapshot + totales + promo
        $pricing = app(\App\Services\Bookings\BookingPricingService::class);
        $categoriesSnapshot = $pricing->buildCategoriesSnapshot($newTour, $in['categories']);
        $detailSubtotal     = $pricing->calculateSubtotal($categoriesSnapshot);

        $promo = null;
        $promoInput = trim((string)($in['promo_code'] ?? ''));
        if ($promoInput !== '') {
            $clean = PromoCode::normalize($promoInput);
            $promo = PromoCode::whereRaw("TRIM(REPLACE(code,' ','')) = ?", [$clean])->first();
            if ($promo && method_exists($promo, 'isValidToday') && !$promo->isValidToday())   $promo = null;
            if ($promo && method_exists($promo, 'hasRemainingUses') && !$promo->hasRemainingUses()) $promo = null;
        }
        $total = $pricing->applyPromo($detailSubtotal, $promo);

        // 7) Persistencia
        DB::beginTransaction();
        try {
            // Cabecera: status se mantiene (no permito que el cliente cambie status directo)
            $booking->update([
                'tour_id'          => (int)$in['tour_id'],
                'tour_language_id' => (int)$in['tour_language_id'],
                'total'            => $total,
                'notes'            => $in['notes'] ?? $booking->notes,
            ]);

            $detail->update([
                'tour_id'           => (int)$in['tour_id'],
                'schedule_id'       => (int)$in['schedule_id'],
                'tour_date'         => $in['tour_date'],
                'tour_language_id'  => (int)$in['tour_language_id'],
                'categories'        => $categoriesSnapshot,
                'total'             => $detailSubtotal,
                'hotel_id'          => !empty($in['is_other_hotel']) ? null : ($in['hotel_id'] ?? null),
                'is_other_hotel'    => (bool)($in['is_other_hotel'] ?? false),
                'other_hotel_name'  => $in['other_hotel_name'] ?? null,
                'meeting_point_id'  => $in['meeting_point_id'] ?? null,
            ]);

            // Redención (similar a admin: actualizar/crear/eliminar)
            $currentRedemption = $booking->redemption()->lockForUpdate()->first();
            if ($promo) {
                $appliedAmount = 0.0;
                if ($promo->discount_percent)    $appliedAmount = round($detailSubtotal * ($promo->discount_percent / 100), 2);
                elseif ($promo->discount_amount) $appliedAmount = (float)$promo->discount_amount;

                if ($currentRedemption) {
                    $wasPromoId = (int)$currentRedemption->promo_code_id;

                    $currentRedemption->update([
                        'promo_code_id'      => (int)$promo->promo_code_id,
                        'applied_amount'     => $appliedAmount,
                        'operation_snapshot' => $promo->operation ?? 'subtract',
                        'percent_snapshot'   => $promo->discount_percent,
                        'amount_snapshot'    => $promo->discount_amount,
                        'used_at'            => now(),
                        'user_id'            => (int)Auth::id(),
                    ]);

                    if ($wasPromoId !== (int)$promo->promo_code_id) {
                        if ($prev = PromoCode::lockForUpdate()->find($wasPromoId)) {
                            $prev->usage_count = max(0, (int)$prev->usage_count - 1);
                            if (is_null($prev->usage_limit) || $prev->usage_count < $prev->usage_limit) {
                                $prev->is_used = false;
                                $prev->used_by_booking_id = null;
                            }
                            $prev->save();
                        }
                        $promo->usage_count = (int)$promo->usage_count + 1;
                        if (!is_null($promo->usage_limit) && $promo->usage_count >= $promo->usage_limit) {
                            $promo->is_used = true;
                            $promo->used_at = now();
                        }
                        $promo->save();
                    }
                } else {
                    $booking->redemption()->create([
                        'promo_code_id'      => (int)$promo->promo_code_id,
                        'applied_amount'     => $appliedAmount,
                        'operation_snapshot' => $promo->operation ?? 'subtract',
                        'percent_snapshot'   => $promo->discount_percent,
                        'amount_snapshot'    => $promo->discount_amount,
                        'used_at'            => now(),
                        'user_id'            => (int)Auth::id(),
                    ]);

                    $promo->usage_count = (int)$promo->usage_count + 1;
                    if (!is_null($promo->usage_limit) && $promo->usage_count >= $promo->usage_limit) {
                        $promo->is_used = true;
                        $promo->used_at = now();
                    }
                    $promo->save();
                }
            } else {
                if ($currentRedemption) {
                    $code = PromoCode::lockForUpdate()->find($currentRedemption->promo_code_id);
                    $currentRedemption->delete();

                    if ($code) {
                        $code->usage_count = max(0, (int)$code->usage_count - 1);
                        if (is_null($code->usage_limit) || $code->usage_count < $code->usage_limit) {
                            $code->is_used = false;
                            $code->used_by_booking_id = null;
                        }
                        $code->save();
                    }
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error("Public booking update error #{$booking->booking_id}: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->with('error', __('m_bookings.bookings.errors.update'));
        }

        // 8) Email (cliente + admins)
        try {
            $notify   = $this->notifyEmails();
            $userMail = optional($booking->user)->email;

            $mailable = (new \App\Mail\BookingUpdatedMail($booking))
                ->onQueue('mail')
                ->afterCommit();

            if ($userMail) {
                $pending = \Mail::to($userMail);
                if (!empty($notify)) $pending->bcc($notify);
                $pending->queue($mailable);
            } else {
                if (!empty($notify)) {
                    \Mail::to($notify[0])->bcc(array_slice($notify, 1))->queue($mailable);
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('BookingUpdatedMail (public) failed: ' . $e->getMessage(), ['booking_id' => $booking->booking_id]);
        }

        return redirect()->route('my-bookings')
            ->with('success', __('m_bookings.bookings.success.updated'));
    }

    /**
     * Show booking confirmation page
     */
    public function confirmation(Booking $booking)
    {
        // Verificar que el usuario tenga acceso a esta booking
        // Si hay usuario autenticado, verificamos email o rol admin
        // Si no hay usuario autenticado (guest flow), verificamos session booking id? 
        // Por ahora seguimos la logica del user pero adaptada a guest si es necesario

        $user = Auth::user();

        // Permitir acceso si es el dueño de la reserva
        if ($user && $booking->user_id === $user->user_id) {
            // Authorized
        }
        // Permitir acceso a admins
        elseif ($user && $user->hasRole('admin')) {
            // Authorized
        }
        // Guest access logic: Permitir si la booking acaba de ser creada en esta sesión
        elseif (session()->has('guest_payment_id') || session('recent_booking_id') == $booking->booking_id) {
            // Authorized (we might need to store recent_booking_id in session during creation)
        } else {
            // Fallback: Check email matching if needed, or simple 403
            // For now using the user provided logic but strictly for auth users + admin
            if (($booking->user_id !== ($user?->user_id)) && !($user?->hasRole('admin'))) {
                // Si es un guest que acaba de pagar, deberíamos permitirle verlo.
                // Como el PaymentController hace login del usuario (restoreSession), 
                // el chequeo de user_id debería pasar si el usuario fue logueado/restaurado.
                abort(403);
            }
        }

        // Check if user needs to setup password (e.g. guest checkout created account)
        $passwordSetupUrl = null;
        $bookingUser = $booking->user;

        if ($bookingUser && $this->passwordSetupService->needsPasswordSetup($bookingUser)) {
            // Check if current logged in user is the booking user (to avoid showing setup link to admin viewing confirmation)
            if ($user && $user->user_id === $bookingUser->user_id) {
                // Generate a fresh token for immediate use
                $tokenData = $this->passwordSetupService->generateSetupToken($bookingUser);
                $passwordSetupUrl = route('password.setup', ['token' => $tokenData['plain_token']]);
            }
        }

        return view('public.payment-confirmation', compact('booking', 'passwordSetupUrl'));
    }

    /**
     * Destinatarios de notificación (admins), desde .env:
     * BOOKING_NOTIFY y/o MAIL_NOTIFICATIONS (coma-separado).
     */
    private function notifyEmails(): array
    {
        return collect([setting('email.booking_notifications')])
            ->filter()
            ->flatMap(fn($v) => array_map('trim', explode(',', $v)))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
