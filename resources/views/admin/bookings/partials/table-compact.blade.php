{{-- resources/views/admin/bookings/partials/table-compact.blade.php --}}
@php
use App\Models\CustomerCategory;

$locale = app()->getLocale();
$currency = config('app.currency_symbol', '$');

// Mapa de nombres traducidos por category_id y por slug para resolver rápido
$allCats = CustomerCategory::active()
->get();

$catNameById = $allCats->mapWithKeys(function($c) use ($locale) {
return [$c->category_id => ($c->getTranslatedName($locale) ?: $c->slug ?: '')];
})->all();

$catNameBySlug = $allCats->filter(fn($c) => $c->slug)
->mapWithKeys(function($c) use ($locale) {
$label = $c->getTranslatedName($locale);
// fallback a archivo de idioma por slug
if (!$label && $c->slug) {
$try = __('customer_categories.labels.' . $c->slug);
if ($try !== 'customer_categories.labels.' . $c->slug) $label = $try;
if (!$label) {
$try2 = __('m_tours.customer_categories.labels.' . $c->slug);
if ($try2 !== 'm_tours.customer_categories.labels.' . $c->slug) $label = $try2;
}
}
return [$c->slug => ($label ?: $c->slug)];
})->all();
@endphp

<div class="d-none d-md-block">
<table class="table table-bordered table-striped table-hover table-compact">
  <thead class="bg-primary text-white">
    <tr>
      <th>{{ __('m_bookings.bookings.fields.reference') }}</th>
      <th>{{ __('m_bookings.bookings.fields.status') }}</th>
      <th>{{ __('m_bookings.bookings.fields.customer') }}</th>
      <th>{{ __('m_bookings.bookings.fields.email') }}</th>
      <th>{{ __('m_bookings.bookings.fields.tour') }}</th>
      <th>{{ __('m_bookings.bookings.fields.tour_date') }}</th>
      <th>{{ __('m_bookings.bookings.fields.schedule') }}</th>
      <th>{{ __('m_bookings.bookings.fields.pickup_place') }}</th>
      <th>{{ __('m_bookings.bookings.fields.travelers') }}</th>
      <th>{{ __('m_bookings.bookings.fields.total') }}</th>
      <th>{{ __('m_bookings.bookings.ui.actions') }}</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($bookings as $booking)
    @php
    $detail = $booking->detail;

    // ===== Tour name con fallback =====
    $liveName = optional($detail?->tour)->name;
    $snapName = $detail->tour_name_snapshot ?: ($booking->product_name_snapshot ?? null);
    $tourCellText = $liveName
    ?? ($snapName
    ? __('m_bookings.bookings.messages.deleted_tour_snapshot', ['name' => $snapName])
    : __('m_bookings.bookings.messages.deleted_tour'));
    $tourDisplay = mb_strlen($tourCellText) > 30 ? (mb_substr($tourCellText, 0, 30) . '…') : $tourCellText;

    // ===== Horario =====
    $scheduleLabel = $detail?->schedule
    ? \Carbon\Carbon::parse($detail->schedule->start_time)->format('g:i A')
    : '—';

    // ===== Pickup Place (Hotel, Other Hotel o Meeting Point) =====
    // prioridad: other_hotel_name → hotel → snapshot → meeting point → snapshot
    $hotelName = $detail?->other_hotel_name
    ?: optional($detail?->hotel)->name
    ?: ($detail->hotel_name_snapshot ?? $booking->hotel_name_snapshot ?? null);

    $mpName = optional($detail?->meetingPoint)->name_localized
    ?: ($detail->meeting_point_name_snapshot ?? $booking->meeting_point_name_snapshot ?? null);

    $pickupSnap = $detail->pickup_place_snapshot ?? $booking->pickup_place_snapshot ?? null;

    $pickupLabel = null;
    $pickupIcon = null;

    if ($hotelName) {
    $pickupLabel = $hotelName;
    $pickupIcon = 'fa-hotel';
    } elseif ($mpName) {
    $pickupLabel = $mpName;
    $pickupIcon = 'fa-map-marker-alt';
    } elseif ($pickupSnap) {
    $pickupLabel = $pickupSnap;
    $pickupIcon = 'fa-map-marker-alt';
    } else {
    $pickupLabel = '—';
    }

    $pickupDisplay = $pickupLabel && $pickupLabel !== '—'
    ? (mb_strlen($pickupLabel) > 34 ? (mb_substr($pickupLabel, 0, 34) . '…') : $pickupLabel)
    : '—';

    // ===== Categorías =====
    $categoriesData = [];
    $totalPersons = 0;

    if ($detail?->categories && is_string($detail->categories)) {
    try { $categoriesData = json_decode($detail->categories, true) ?: []; }
    catch (\Exception $e) { \Log::warning('Error parsing categories in table', ['booking_id' => $booking->booking_id]); }
    } elseif (is_array($detail?->categories)) {
    $categoriesData = $detail->categories;
    }

    // Función helper para resolver nombre traducido de categoría desde array del detalle
    $resolveCatName = function(array $cat) use ($catNameById, $catNameBySlug) {
    // 1) por id
    $id = $cat['category_id'] ?? $cat['id'] ?? null;
    if ($id && isset($catNameById[$id]) && $catNameById[$id]) {
    return $catNameById[$id];
    }
    // 2) por slug
    $slug = $cat['slug'] ?? null;
    if ($slug && isset($catNameBySlug[$slug]) && $catNameBySlug[$slug]) {
    return $catNameBySlug[$slug];
    }
    // 3) por archivos de idioma (si vino el slug pero no está en catNameBySlug)
    if ($slug) {
    $tr = __('customer_categories.labels.' . $slug);
    if ($tr !== 'customer_categories.labels.' . $slug) return $tr;
    $tr2 = __('m_tours.customer_categories.labels.' . $slug);
    if ($tr2 !== 'm_tours.customer_categories.labels.' . $slug) return $tr2;
    }
    // 4) fallback al nombre en el snapshot
    return $cat['name'] ?? $cat['category_name'] ?? 'N/A';
    };

    $categoriesRendered = [];
    if (!empty($categoriesData)) {
    // Soportar dos formatos (lista y mapa)
    if (isset($categoriesData[0]) && is_array($categoriesData[0])) {
    foreach ($categoriesData as $cat) {
    $qty = (int)($cat['quantity'] ?? 0);
    if ($qty > 0) {
    $name = $resolveCatName($cat);
    $categoriesRendered[] = ['name' => $name, 'quantity' => $qty];
    $totalPersons += $qty;
    }
    }
    } else {
    foreach ($categoriesData as $catId => $cat) {
    $qty = (int)($cat['quantity'] ?? 0);
    if ($qty > 0) {
    // inyectar id si la clave lo es
    if (!isset($cat['category_id']) && is_numeric($catId)) {
    $cat['category_id'] = (int)$catId;
    }
    $name = $resolveCatName($cat);
    $categoriesRendered[] = ['name' => $name, 'quantity' => $qty];
    $totalPersons += $qty;
    }
    }
    }
    }

    // Fallback legacy (adults/kids)
    if (empty($categoriesRendered)) {
    $adults = (int)($detail->adults_quantity ?? 0);
    $kids = (int)($detail->kids_quantity ?? 0);
    if ($adults > 0) { $categoriesRendered[] = ['name' => __('customer_categories.labels.adult') !== 'customer_categories.labels.adult' ? __('customer_categories.labels.adult') : 'Adults', 'quantity' => $adults]; }
    if ($kids > 0) { $categoriesRendered[] = ['name' => __('customer_categories.labels.child') !== 'customer_categories.labels.child' ? __('customer_categories.labels.child') : 'Kids', 'quantity' => $kids]; }
    $totalPersons = $adults + $kids;
    }

    // Tooltip con todo el desglose
    $catsTitle = '';
    if (!empty($categoriesRendered)) {
    $titleParts = [];
    foreach ($categoriesRendered as $c) {
    $titleParts[] = ($c['name'] ?? 'N/A') . ' ×' . (int)($c['quantity'] ?? 0);
    }
    $catsTitle = implode(' · ', $titleParts);
    }
    @endphp

    <tr>
      <td><strong>{{ $booking->booking_reference }}</strong></td>

      <td>
        <a href="{{ route('admin.bookings.show', $booking) }}" class="badge badge-compact badge-interactive
          {{ $booking->status === 'pending'   ? 'bg-warning text-dark' : '' }}
          {{ $booking->status === 'confirmed' ? 'bg-success text-white' : '' }}
          {{ $booking->status === 'cancelled' ? 'bg-danger text-white'  : '' }}"
          style="text-decoration: none;"
          title="{{ __('m_bookings.bookings.ui.click_to_view') }}">
          <i class="fas fa-eye mr-1"></i>
          {{ __('m_bookings.bookings.statuses.' . $booking->status) }}
        </a>

        @php
        // Determinar estado de pago
        $paymentBadgeClass = 'secondary';
        $paymentText = __('Pending');

        if ($booking->relationLoaded('payments') && $booking->payments->isNotEmpty()) {
        $latestPayment = $booking->payments->sortByDesc('created_at')->first();
        if ($latestPayment && $latestPayment->status === 'completed') {
        $paymentBadgeClass = 'success';
        $paymentText = __('Paid');
        }
        }
        @endphp

        <br>
        <small class="badge bg-{{ $paymentBadgeClass }} mt-1">
          <i class="fas fa-credit-card me-1"></i>{{ $paymentText }}
        </small>
      </td>

      <td>
        {{ $booking->getUserDisplayName() }}
        @if(!$booking->userExists())
        <span class="badge bg-secondary ms-1" style="font-size: 0.65rem;">{{ __('m_bookings.bookings.messages.deleted_user') }}</span>
        @endif
      </td>

      <td>
        {{ $booking->getUserEmail() ?? '—' }}
      </td>

      <td title="{{ $tourCellText }}">{{ $tourDisplay }}</td>

      <td>{{ optional($detail?->tour_date)->format('d-M-Y') ?? '—' }}</td>

      <td>{{ $scheduleLabel }}</td>

      {{-- Pickup Place --}}
      <td title="{{ $pickupLabel }}">
        @if($pickupLabel !== '—')
        <span class="chip chip-muted">
          @if($pickupIcon)<i class="fas {{ $pickupIcon }} me-1"></i>@endif
          {{ $pickupDisplay }}
        </span>
        @else
        —
        @endif
      </td>

      {{-- Categorías (mostrar todas en chips, sin agrupar) --}}
      <td>
        @if($totalPersons > 0)
        <div class="cats-inline" title="{{ $catsTitle }}">
          @foreach($categoriesRendered as $c)
          <span class="cat-chip" title="{{ $c['name'] }} ×{{ (int)($c['quantity'] ?? 0) }}">
            {{ $c['name'] }} ×{{ (int)($c['quantity'] ?? 0) }}
          </span>
          @endforeach

          <span class="cat-total ms-auto" title="{{ __('m_bookings.bookings.fields.total_travelers') }}">
            <i class="fas fa-users me-1"></i>{{ $totalPersons }}
          </span>
        </div>
        @else
        <span class="badge bg-secondary">0</span>
        @endif
      </td>

      <td><strong>{{ $currency }}{{ number_format((float)$booking->total, 2) }}</strong></td>

      <td class="text-nowrap">
        {{-- View Details --}}
        <a href="{{ route('admin.bookings.show', $booking) }}"
          class="btn btn-sm btn-info btn-details"
          title="{{ __('m_bookings.bookings.ui.view_details') }}">
          <i class="fas fa-eye"></i>
        </a>



        {{-- Download Receipt --}}
        <a href="{{ route('admin.bookings.receipt', $booking->booking_id) }}"
          class="btn btn-primary btn-sm"
          title="{{ __('m_bookings.bookings.ui.download_receipt') }}">
          <i class="fas fa-file-download"></i>
        </a>

        {{-- Edit --}}
        <a href="{{ route('admin.bookings.edit', $booking) }}"
          class="btn btn-sm btn-edit"
          title="{{ __('m_bookings.bookings.buttons.edit') }}">
          <i class="fas fa-edit"></i>
        </a>

        {{-- Delete or Restore --}}
        @if($booking->trashed())
        {{-- Restore Button --}}
        <form action="{{ route('admin.bookings.restore', $booking->booking_id) }}"
          method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-sm btn-success"
            title="{{ __('m_bookings.bookings.trash.restore_booking') }}">
            <i class="fas fa-undo"></i>
          </button>
        </form>

        {{-- Force Delete Button (Permanent) --}}
        <form action="{{ route('admin.bookings.forceDelete', $booking->booking_id) }}"
          method="POST" class="d-inline delete-form"
          id="force-delete-form-{{ $booking->booking_id }}">
          @csrf
          @method('DELETE')
          <button type="button" class="btn btn-sm btn-danger btn-force-delete"
            data-form-id="force-delete-form-{{ $booking->booking_id }}"
            data-booking-ref="{{ $booking->booking_reference }}"
            title="{{ __('m_bookings.bookings.trash.permanently_delete') }}">
            <i class="fas fa-trash"></i>
          </button>
        </form>
        @else
        {{-- Delete Button with SweetAlert --}}
        <form action="{{ route('admin.bookings.destroy', $booking->booking_id) }}"
          method="POST" class="d-inline delete-form"
          id="delete-form-{{ $booking->booking_id }}">
          @csrf
          @method('DELETE')
          <button type="button" class="btn btn-sm btn-danger btn-delete"
            data-form-id="delete-form-{{ $booking->booking_id }}"
            data-booking-ref="{{ $booking->booking_reference }}"
            title="{{ __('m_bookings.bookings.buttons.delete') }}">
            <i class="fas fa-trash-alt"></i>
          </button>
        </form>
        @endif
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
</div>

{{-- VISTA MÓVIL (Cards) --}}
<div class="d-md-none">
  @forelse ($bookings as $booking)
  @php
  // Re-calcular variables para móvil porque el scope del foreach anterior terminó
  $detail = $booking->detail;
  $liveName = optional($detail?->tour)->name;
  $snapName = $detail->tour_name_snapshot ?: ($booking->product_name_snapshot ?? null);
  $tourCellText = $liveName ?? ($snapName ? __('m_bookings.bookings.messages.deleted_tour_snapshot', ['name' => $snapName]) : __('m_bookings.bookings.messages.deleted_tour'));
  
  $scheduleLabel = $detail?->schedule ? \Carbon\Carbon::parse($detail->schedule->start_time)->format('g:i A') : '—';
  
  // Status badge logic
  $statusClass = match($booking->status) {
      'confirmed' => 'success',
      'cancelled' => 'danger',
      'pending'   => 'warning',
      default     => 'secondary'
  };
  @endphp

  <div class="card shadow-sm mb-3">
    <div class="card-header d-flex justify-content-between align-items-center py-2">
      <span class="fw-bold">#{{ $booking->booking_reference }}</span>
      <span class="badge bg-{{ $statusClass }}">{{ __('m_bookings.bookings.statuses.' . $booking->status) }}</span>
    </div>
    
    <div class="card-body py-2">
      <div class="mb-2">
        <i class="fas fa-user text-muted me-1"></i>
        <span class="fw-semibold">{{ $booking->getUserDisplayName() }}</span>
      </div>
      
      <div class="mb-2">
        <i class="fas fa-map text-muted me-1"></i>
        <span class="text-secondary">{{ $tourCellText }}</span>
      </div>
      
      <div class="d-flex justify-content-between mb-2 text-sm text-secondary">
        <span><i class="far fa-calendar me-1"></i> {{ optional($detail?->tour_date)->format('d/M/Y') ?? '—' }}</span>
        <span><i class="far fa-clock me-1"></i> {{ $scheduleLabel }}</span>
      </div>

      <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
        <span class="fw-bold text-success">{{ $currency }}{{ number_format((float)$booking->total, 2) }}</span>
        
        <div class="d-flex gap-1">
            <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-info text-dark">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-sm btn-edit text-white">
                <i class="fas fa-edit"></i>
            </a>
            
            @if($booking->trashed())
                <form action="{{ route('admin.bookings.restore', $booking->booking_id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success">
                        <i class="fas fa-undo"></i>
                    </button>
                </form>
            @else
                <button type="button" class="btn btn-sm btn-danger btn-delete" 
                    data-form-id="delete-form-mobile-{{ $booking->booking_id }}"
                    data-booking-ref="{{ $booking->booking_reference }}">
                    <i class="fas fa-trash-alt"></i>
                </button>
                <form action="{{ route('admin.bookings.destroy', $booking->booking_id) }}"
                  method="POST" class="d-none delete-form"
                  id="delete-form-mobile-{{ $booking->booking_id }}">
                  @csrf @method('DELETE')
                </form>
            @endif
        </div>
      </div>
    </div>
  </div>
  @empty
  <div class="text-center py-4 text-muted">
    <i class="fas fa-inbox fa-2x mb-2"></i>
    <p>{{ __('m_bookings.bookings.ui.no_bookings_found') }}</p>
  </div>
  @endforelse
</div>

{{-- SweetAlert2 CDN --}}
@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
  // Soft Delete confirmation with SweetAlert2
  document.querySelectorAll('.btn-delete').forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      const formId = this.getAttribute('data-form-id');
      const bookingRef = this.getAttribute('data-booking-ref');

      Swal.fire({
        title: '{{ __("Are you sure?") }}',
        text: `{{ __("Delete booking") }} #${bookingRef}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '{{ __("Yes, delete it!") }}',
        cancelButtonText: '{{ __("Cancel") }}',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById(formId).submit();
        }
      });
    });
  });

  // Force Delete confirmation with STRONGER warning
  document.querySelectorAll('.btn-force-delete').forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      const formId = this.getAttribute('data-form-id');
      const bookingRef = this.getAttribute('data-booking-ref');

      console.log('Force delete clicked for:', bookingRef, 'Form ID:', formId);

      Swal.fire({
        title: '{{ __("m_bookings.bookings.trash.force_delete_title") }}',
        html: `<p><strong>{{ __("m_bookings.bookings.trash.force_delete_warning") }}</strong></p>
                 <p>{{ __("m_bookings.bookings.fields.reference") }} #${bookingRef} {{ __("m_bookings.bookings.trash.force_delete_message") }}</p>
                 <p class="text-danger">{{ __("m_bookings.bookings.trash.force_delete_data_loss") }}</p>`,
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '{{ __("m_bookings.bookings.trash.force_delete_confirm") }}',
        cancelButtonText: '{{ __("m_bookings.bookings.buttons.cancel") }}',
        reverseButtons: true,
        focusCancel: true
      }).then((result) => {
        if (result.isConfirmed) {
          console.log('Confirmed! Submitting form:', formId);
          const form = document.getElementById(formId);
          if (form) {
            console.log('Form found, submitting...');
            form.submit();
          } else {
            console.error('Form not found:', formId);
            Swal.fire('Error', 'Form not found. Please refresh and try again.', 'error');
          }
        } else {
          console.log('Cancelled');
        }
      });
    });
  });
  });

  // Helper function for copying payment link (global scope)
  window.copyPaymentLink = function() {
    const copyText = document.getElementById("payment-link-input");
    if (copyText) {
      copyText.select();
      copyText.setSelectionRange(0, 99999);
      navigator.clipboard.writeText(copyText.value).then(() => {
        const btn = document.querySelector('.swal2-popup .btn-outline-secondary i');
        if (btn) {
          btn.className = 'fas fa-check text-success';
          setTimeout(() => btn.className = 'fas fa-copy', 2000);
        }
      }).catch(err => {
        console.error('Failed to copy:', err);
        alert('Failed to copy link');
      });
    }
  };

  // Payment Link Handler
  document.querySelectorAll('.btn-payment-link').forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      const url = this.getAttribute('data-url');

      Swal.fire({
        title: '{{ __("m_bookings.bookings.ui.loading") ?? "Loading..." }}',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            Swal.fire({
              title: '{{ __("m_bookings.bookings.ui.payment_link") ?? "Payment Link" }}',
              html: `
                  <div class="input-group mb-3">
                    <input type="text" class="form-control" value="${data.url}" id="payment-link-input" readonly>
                    <button class="btn btn-outline-secondary" type="button" onclick="copyPaymentLink()">
                      <i class="fas fa-copy"></i>
                    </button>
                  </div>
                  <div class="d-flex justify-content-center gap-2">
                    <a href="https://wa.me/?text=${encodeURIComponent(data.url)}" target="_blank" class="btn btn-success">
                      <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                    <a href="mailto:?body=${encodeURIComponent(data.url)}" class="btn btn-secondary">
                      <i class="fas fa-envelope"></i> Email
                    </a>
                  </div>
                `,
              showConfirmButton: false,
              showCloseButton: true
            });
          } else {
            Swal.fire('Error', data.message || 'Could not generate link', 'error');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          Swal.fire('Error', 'An error occurred', 'error');
        });
    });
  });

  // Regenerate Payment Link Handler (with confirmation)
  document.querySelectorAll('.btn-regenerate-payment-link').forEach(button => {
  button.addEventListener('click', function(e) {
    e.preventDefault();
    const url = this.getAttribute('data-url');

    Swal.fire({
      title: '{{ __("m_bookings.bookings.ui.regenerate_confirm_title") ?? "Regenerate Payment Link?" }}',
      html: '{{ __("m_bookings.bookings.ui.regenerate_confirm_text") ?? "This will invalidate the old link and create a new one. The old link will no longer work." }}',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#f59e0b',
      cancelButtonColor: '#6b7280',
      confirmButtonText: '{{ __("m_bookings.bookings.ui.regenerate_confirm") ?? "Yes, regenerate" }}',
      cancelButtonText: '{{ __("m_bookings.bookings.ui.cancel") ?? "Cancel" }}'
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: '{{ __("m_bookings.bookings.ui.loading") ?? "Loading..." }}',
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });

        fetch(url, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Content-Type': 'application/json'
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire({
                title: '{{ __("m_bookings.bookings.ui.payment_link_regenerated") ?? "Payment Link Regenerated" }}',
                html: `
                      <div class="alert alert-warning mb-3" style="font-size:0.875rem;">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ __("m_bookings.bookings.ui.old_link_invalid") ?? "The old link is now invalid" }}
                      </div>
                      <div class="input-group mb-3">
                        <input type="text" class="form-control" value="${data.url}" id="payment-link-input" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyPaymentLink()">
                          <i class="fas fa-copy"></i>
                        </button>
                      </div>
                      <div class="d-flex justify-content-center gap-2">
                        <a href="https://wa.me/?text=${encodeURIComponent(data.url)}" target="_blank" class="btn btn-success">
                          <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                        <a href="mailto:?body=${encodeURIComponent(data.url)}" class="btn btn-secondary">
                          <i class="fas fa-envelope"></i> Email
                        </a>
                      </div>
                    `,
                showConfirmButton: false,
                showCloseButton: true
              });
            } else {
              Swal.fire('Error', data.message || 'Could not regenerate link', 'error');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'An error occurred', 'error');
          });
      }
    });
  });

</script>
@endpush

<style>
  .table-compact .gap-1 {
    gap: 0.25rem;
  }

  /* Chips en línea para categorías */
  .cats-inline {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: .25rem .5rem;
  }

  .cat-chip {
    display: inline-flex;
    align-items: center;
    font-size: .75rem;
    line-height: 1;
    padding: .125rem .4rem;
    border-radius: .6rem;
    background: #3f6791;
    border: 1px solid var(--bs-border-color, #dee2e6);
    color: #fff;
    white-space: nowrap;
    max-width: 100%;
  }

  .cat-total {
    display: inline-flex;
    align-items: center;
    font-weight: 700;
    font-size: .78rem;
    line-height: 1;
    padding: .125rem .45rem;
    border-radius: .6rem;
    background: #60a862;
    color: #fff;
    white-space: nowrap;
  }

  /* Pickup chip */
  .chip {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    font-size: .8rem;
    line-height: 1.1;
    padding: .2rem .45rem;
    border-radius: .5rem;
    border: 1px solid var(--bs-border-color, #dee2e6);
    max-width: 260px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .chip-muted {
    background: var(--bs-secondary-bg, #f1f3f5);
    color: var(--bs-body-color, #212529);
  }
</style>