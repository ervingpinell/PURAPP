<?php

namespace App\Http\Controllers\Admin\Cart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Tour;
use App\Models\TourLanguage;
use App\Models\HotelList;
use App\Models\PromoCode;
use App\Models\TourExcludedDate;
use App\Models\MeetingPoint;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // VISTA PÚBLICA DEL CARRITO
        if ($request->routeIs('public.cart.index')) {
            $cart = $user->cart()
                ->where('is_active', true)
                ->with([
                    'items.tour.schedules',   // horarios
                    'items.tour.languages',   // idiomas
                    'items.schedule',
                    'items.language',
                    'items.hotel',
                    'items.meetingPoint',     // meeting point
                ])
                ->first();

            $hotels = HotelList::where('is_active', true)->orderBy('name')->get();

            return view('public.cart', [
                'cart'   => $cart,
                'client' => $user,
                'hotels' => $hotels,
            ]);
        }

        // VISTA ADMIN
        $languages = TourLanguage::all();
        $hotels    = HotelList::where('is_active', true)->orderBy('name')->get();

        $cart = $user->cart()->where('is_active', true)->first();

        if (!$cart) {
            $emptyCart = new \stdClass;
            $emptyCart->items = collect();
            return view('admin.Cart.cart', compact('languages', 'hotels') + ['cart' => $emptyCart]);
        }

        $itemsQuery = CartItem::with([
                'tour',
                'schedule',
                'language',
                'hotel',
                'meetingPoint',
            ])
            ->where('cart_id', $cart->cart_id);

        if ($request->filled('estado')) {
            $itemsQuery->where('is_active', $request->estado);
        }

        $cart->items = $itemsQuery->get();

        return view('admin.Cart.cart', compact('cart', 'languages', 'hotels'));
    }

    public function store(Request $request)
    {
        // -------------------------
        // SANEOS PREVIOS AL VALIDADOR
        // -------------------------
        $in = $request->all();

        // Normaliza boolean
        $in['is_other_hotel'] = (bool)($in['is_other_hotel'] ?? false);

        // hotel_id: si llega "other", "__custom__" o cualquier no-entero => null
        if (array_key_exists('hotel_id', $in)) {
            $raw = $in['hotel_id'];
            if ($raw === 'other' || $raw === '__custom__' || (isset($raw) && !ctype_digit((string)$raw))) {
                // si explícitamente eligió "otro", marcamos el flag
                if ($raw === 'other' || $raw === '__custom__') {
                    $in['is_other_hotel'] = true;
                }
                $in['hotel_id'] = null;
            }
        } else {
            $in['hotel_id'] = null;
        }

        // si marcó "otro hotel" y puso nombre, nos aseguramos de que hotel_id sea null
        if ($in['is_other_hotel'] && !empty($in['other_hotel_name'])) {
            $in['hotel_id'] = null;
        }

        $request->replace($in);

        // -------------------------
        // VALIDACIÓN
        // -------------------------
        $request->validate([
            'tour_id'                 => 'required|exists:tours,tour_id',
            'tour_date'               => 'required|date|after_or_equal:today',
            'schedule_id'             => 'required|exists:schedules,schedule_id',
            'tour_language_id'        => 'required|exists:tour_languages,tour_language_id',

            // bail evita correr exists si falla integer; se excluye si is_other_hotel=1
            'hotel_id'                => 'bail|nullable|integer|exists:hotels_list,hotel_id|exclude_if:is_other_hotel,1',
            'is_other_hotel'          => 'boolean',
            // SOLO si realmente se marcó “otro hotel”
            'other_hotel_name'        => 'nullable|string|max:255|required_if:is_other_hotel,1',

            'adults_quantity'         => 'required|integer|min:1',
            'kids_quantity'           => 'nullable|integer|min:0|max:2',

            // Meeting point (desde el form)
            'selected_meeting_point'  => 'nullable|integer|exists:meeting_points,id',
        ]);

        $user = Auth::user();
        $cart = $user->cart()->where('is_active', true)->first();
        if (!$cart) {
            $cart = Cart::create(['user_id' => $user->user_id, 'is_active' => true]);
        }

        $tour = Tour::findOrFail($request->tour_id);

        // Schedule debe pertenecer al tour y estar activo (y el pivot activo)
        $schedule = $tour->schedules()
            ->where('schedules.schedule_id', $request->schedule_id)
            ->where('schedules.is_active', true)
            ->wherePivot('is_active', true)
            ->first();

        if (!$schedule) {
            return back()->withErrors([
                'schedule_id' => 'El horario seleccionado no está disponible para este tour (inactivo o no asignado).'
            ]);
        }

        // Fecha bloqueada (bloqueo por día completo o por horario específico)
        $isBlocked = TourExcludedDate::where('tour_id', $tour->tour_id)
            ->where(function ($q) use ($request) {
                $q->whereNull('schedule_id')
                  ->orWhere('schedule_id', $request->schedule_id);
            })
            ->where('start_date', '<=', $request->tour_date)
            ->where(function ($q) use ($request) {
                $q->where('end_date', '>=', $request->tour_date)->orWhereNull('end_date');
            })
            ->exists();

        if ($isBlocked) {
            return back()->with('error', __('adminlte::adminlte.blocked_date_for_tour', [
                'date' => $request->tour_date,
                'tour' => $tour->name,
            ]));
        }

        // Capacidad
        $reserved = DB::table('booking_details')
            ->where('tour_id', $request->tour_id)
            ->where('schedule_id', $request->schedule_id)
            ->where('tour_date', $request->tour_date)
            ->sum(DB::raw('adults_quantity + kids_quantity'));

        $requested = $request->adults_quantity + ($request->kids_quantity ?? 0);

        if ($reserved + $requested > $schedule->max_capacity) {
            return back()->with('error', __('adminlte::adminlte.tourCapacityFull'));
        }

        // --- MEETING POINT (snapshot) ---
        $mpId = $request->integer('selected_meeting_point') ?: null;
        $mp   = $mpId ? MeetingPoint::find($mpId) : null;

        CartItem::create([
            'cart_id'          => $cart->cart_id,
            'tour_id'          => $request->tour_id,
            'tour_date'        => $request->tour_date,
            'schedule_id'      => $request->schedule_id,
            'tour_language_id' => $request->tour_language_id,

            'hotel_id'         => $request->boolean('is_other_hotel') ? null : $request->hotel_id,
            'is_other_hotel'   => $request->boolean('is_other_hotel'),
            'other_hotel_name' => $request->boolean('is_other_hotel') ? $request->other_hotel_name : null,

            'adults_quantity'  => $request->adults_quantity,
            'kids_quantity'    => $request->kids_quantity ?? 0,
            'is_active'        => true,

            // Snapshot Meeting Point
            'meeting_point_id'          => $mp?->id,
            'meeting_point_name'        => $mp?->name,
            'meeting_point_pickup_time' => $mp?->pickup_time,
            'meeting_point_address'     => $mp?->address,
            'meeting_point_map_url'     => $mp?->map_url,
        ]);

        return $request->ajax()
            ? response()->json(['message' => __('adminlte::adminlte.cartItemAdded')])
            : back()->with('success', __('adminlte::adminlte.cartItemAdded'));
    }

    public function getReserved(Request $request)
    {
        $reserved = DB::table('booking_details')
            ->where('tour_id', $request->tour_id)
            ->where('schedule_id', $request->schedule_id)
            ->where('tour_date', $request->tour_date)
            ->sum(DB::raw('adults_quantity + kids_quantity'));

        return response()->json(['reserved' => $reserved]);
    }

    public function update(Request $request, CartItem $item)
    {
        // --- Normalización previa (aceptar "other"/"__custom__" y no enteros) ---
        $in = $request->all();
        $in['is_other_hotel'] = (bool)($in['is_other_hotel'] ?? false);

        $raw = $in['hotel_id'] ?? null;
        if ($in['is_other_hotel'] === true || $raw === 'other' || $raw === '__custom__' || (isset($raw) && !ctype_digit((string)$raw))) {
            $in['hotel_id'] = null;
        }
        $request->replace($in);

        // Validación
        $data = $request->validate([
            'tour_date'        => ['required','date','after_or_equal:today'],
            'adults_quantity'  => ['required','integer','min:1'],
            'kids_quantity'    => ['nullable','integer','min:0','max:2'],
            'schedule_id'      => ['nullable','exists:schedules,schedule_id'],
            'tour_language_id' => ['required','exists:tour_languages,tour_language_id'],
            'is_active'        => ['nullable','boolean'],

            // bail + integer; y no exigirlo si is_other_hotel=1
            'hotel_id'         => ['bail','nullable','integer','exists:hotels_list,hotel_id','exclude_if:is_other_hotel,1'],
            'is_other_hotel'   => ['boolean'],
            // SOLO si is_other_hotel = 1
            'other_hotel_name' => ['nullable','string','max:255','required_if:is_other_hotel,1'],

            // Meeting point (edición)
            'meeting_point_id' => ['nullable','integer','exists:meeting_points,id'],
        ]);

        // Reglas de negocio (capacidad/fechas)
        $tour = $item->tour;
        $scheduleId = $data['schedule_id'] ?? $item->schedule_id;

        if ($scheduleId) {
            $schedule = $tour->schedules()
                ->where('schedules.schedule_id', $scheduleId)
                ->where('schedules.is_active', true)
                ->wherePivot('is_active', true)
                ->first();

            if (!$schedule) {
                return back()->withErrors([
                    'schedule_id' => 'El horario seleccionado no está disponible para este tour (inactivo o no asignado).'
                ]);
            }

            // Fecha bloqueada (día o horario)
            $isBlocked = TourExcludedDate::where('tour_id', $tour->tour_id)
                ->where(function ($q) use ($scheduleId) {
                    $q->whereNull('schedule_id')
                      ->orWhere('schedule_id', $scheduleId);
                })
                ->where('start_date', '<=', $data['tour_date'])
                ->where(function ($q) use ($data) {
                    $q->where('end_date', '>=', $data['tour_date'])->orWhereNull('end_date');
                })
                ->exists();

            if ($isBlocked) {
                return back()->with('error', __('adminlte::adminlte.blocked_date_for_tour', [
                    'date' => $data['tour_date'],
                    'tour' => $tour->name,
                ]));
            }

            $reserved = DB::table('booking_details')
                ->where('tour_id', $tour->tour_id)
                ->where('schedule_id', $scheduleId)
                ->where('tour_date', $data['tour_date'])
                ->sum(DB::raw('adults_quantity + kids_quantity'));

            $requested = (int)$data['adults_quantity'] + (int)($data['kids_quantity'] ?? 0);

            if ($reserved + $requested > $schedule->max_capacity) {
                return back()->with('error', __('adminlte::adminlte.tourCapacityFull'));
            }
        }

        // Persistencia
        $item->tour_date        = $data['tour_date'];
        $item->adults_quantity  = (int)$data['adults_quantity'];
        $item->kids_quantity    = (int)($data['kids_quantity'] ?? 0);
        $item->schedule_id      = $data['schedule_id'] ?? $item->schedule_id;
        $item->tour_language_id = (int)$data['tour_language_id'];
        $item->is_active        = $request->boolean('is_active');

        if ($request->boolean('is_other_hotel')) {
            $item->is_other_hotel   = true;
            $item->other_hotel_name = $data['other_hotel_name'] ?? null;
            $item->hotel_id         = null;
        } else {
            $item->is_other_hotel   = false;
            $item->other_hotel_name = null;
            $item->hotel_id         = $data['hotel_id'] ?? null; // ya viene saneado
        }

        // MEETING POINT (si se envía en la edición del carrito)
        if (array_key_exists('meeting_point_id', $data)) {
            $mpId = $request->integer('meeting_point_id') ?: null;
            $mp   = $mpId ? MeetingPoint::find($mpId) : null;

            $item->meeting_point_id          = $mp?->id;
            $item->meeting_point_name        = $mp?->name;
            $item->meeting_point_pickup_time = $mp?->pickup_time;
            $item->meeting_point_address     = $mp?->address;
            $item->meeting_point_map_url     = $mp?->map_url;
        }

        $item->save();

        return back()->with('success', __('adminlte::adminlte.itemUpdated'));
    }

    public function updateFromPost(Request $request, CartItem $item)
    {
        $validated = $request->validate([
            'tour_date'       => ['required','date','after_or_equal:today'],
            'adults_quantity' => ['required','integer','min:1'],
            'kids_quantity'   => ['nullable','integer','min:0','max:2'],
            'schedule_id'     => ['sometimes','nullable','exists:schedules,schedule_id'],
        ]);

        if (!$request->has('is_active')) {
            $item->delete();
            return back()->with('success', __('adminlte::adminlte.cartItemDeleted'));
        }

        $item->update([
            'tour_date'       => $validated['tour_date'],
            'adults_quantity' => (int)$validated['adults_quantity'],
            'kids_quantity'   => array_key_exists('kids_quantity', $validated) ? (int)$validated['kids_quantity'] : 0,
            'schedule_id'     => array_key_exists('schedule_id', $validated) ? $validated['schedule_id'] : $item->schedule_id,
            'is_active'       => true,
        ]);

        return back()->with('success', __('adminlte::adminlte.itemUpdated'));
    }

    public function destroy(CartItem $item)
    {
        $item->delete();
        return back()->with('success', __('adminlte::adminlte.cartItemDeleted'));
    }

    public function destroyCart(Cart $cart)
    {
        DB::transaction(function () use ($cart) {
            $cart->items()->delete();
            $cart->delete();
        });

        return back()->with('success', __('adminlte::adminlte.cartDeleted') ?? 'Carrito eliminado correctamente.');
    }

    private function adminCartSubtotal(Cart $cart): float
    {
        return (float)$cart->items->sum(function ($it) {
            $ap = (float)($it->tour->adult_price ?? 0);
            $kp = (float)($it->tour->kid_price   ?? 0);
            $aq = (int)($it->adults_quantity ?? 0);
            $kq = (int)($it->kids_quantity   ?? 0);
            return ($ap * $aq) + ($kp * $kq);
        });
    }

    public function applyPromoAdmin(Request $request)
    {
        $request->validate(['code' => ['required','string','max:50']]);

        $user = Auth::user();
        $cart = $user->cart()->where('is_active', true)
            ->with('items.tour')->first();

        if (!$cart || !$cart->items->count()) {
            return response()->json(['ok' => false, 'message' => 'No hay ítems en el carrito.'], 422);
        }

        $code  = strtoupper(trim($request->code));
        $promo = PromoCode::whereRaw('UPPER(code) = ?', [$code])->first();

        if (!$promo) {
            return response()->json(['ok' => false, 'message' => 'Código inválido.'], 422);
        }
        if ($promo->is_used) {
            return response()->json(['ok' => false, 'message' => 'Este código ya fue utilizado.'], 422);
        }

        $subtotal = $this->adminCartSubtotal($cart);

        $discountFixed    = max(0.0, (float)($promo->discount_amount ?? 0));
        $discountPerc     = max(0.0, (float)($promo->discount_percent ?? 0));
        $discountFromPerc = round($subtotal * ($discountPerc / 100), 2);

        $discount = min($subtotal, round($discountFixed + $discountFromPerc, 2));
        $newTotal = max(0, round($subtotal - $discount, 2));

        session([
            'admin_cart_promo' => [
                'code'       => $promo->code,
                'amount'     => $discountFixed,
                'percent'    => $discountPerc,
                'discount'   => $discount,
                'subtotal'   => $subtotal,
                'new_total'  => $newTotal,
                'applied_at' => now()->toISOString(),
            ]
        ]);

        $parts = [];
        if ($discountFixed > 0) $parts[] = '$'.number_format($discountFixed, 2);
        if ($discountPerc  > 0) $parts[] = $discountPerc.'%';
        $label = implode(' + ', $parts);

        return response()->json([
            'ok'        => true,
            'message'   => 'Código aplicado.',
            'code'      => $promo->code,
            'label'     => $label ?: 'Descuento',
            'discount'  => number_format($discount, 2),
            'subtotal'  => number_format($subtotal, 2),
            'new_total' => number_format($newTotal, 2),
        ]);
    }

    public function removePromoAdmin(Request $request)
    {
        $request->session()->forget('admin_cart_promo');
        return response()->json(['ok' => true, 'message' => 'Cupón eliminado.']);
    }

    public function allCarts(Request $request)
    {
        $estado = $request->query('estado');

        $query = Cart::query()
            ->with([
                'user',
                'items.tour',
                'items.language',
                'items.schedule',
                'items.meetingPoint', // meeting point
            ])
            ->withCount('items')
            ->whereHas('user', function ($q) use ($request) {
                if ($request->filled('correo')) {
                    $q->where('email', 'ilike', '%' . $request->correo . '%');
                }
            })
            ->whereHas('items');

        if ($request->has('estado') && in_array($estado, ['0','1'], true)) {
            $query->where('is_active', (bool)$estado);
        }

        $carritos = $query->orderByDesc('updated_at')->get();

        foreach ($carritos as $cart) {
            $cart->total_usd = $cart->items->sum(function ($it) {
                $ap = (float)($it->tour->adult_price ?? 0);
                $kp = (float)($it->tour->kid_price   ?? 0);
                $aq = (int)($it->adults_quantity ?? 0);
                $kq = (int)($it->kids_quantity   ?? 0);
                return ($ap * $aq) + ($kp * $kq);
            });
        }

        // Ajusta esta vista si tu plantilla se llama distinto
        return view('admin.Cart.general', compact('carritos'));
    }

    public function toggleActive(Cart $cart)
    {
        $cart->update(['is_active' => !$cart->is_active]);
        return back()->with('success', 'Estado del carrito actualizado correctamente.');
    }

    public function count()
    {
        if (!auth()->check()) {
            return response()->json(['count' => 0]);
        }
        $cart  = auth()->user()->cart;
        $count = $cart ? $cart->items()->where('is_active', true)->count() : 0;
        return response()->json(['count' => $count]);
    }
}
