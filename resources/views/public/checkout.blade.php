@extends('layouts.app')

@section('title', __('m_checkout.title'))

@push('styles')
<style>
:root{
  --p:  var(--primary-color);
  --p2: var(--primary-dark);
  --ok: var(--primary-color);
  --hdr-dark: var(--primary-header);
  --danger: var(--primary-red);
  --g50:#f9fafb;--g100:#f3f4f6;--g200:#e5e7eb;--g300:#d1d5db;--g400:#9ca3af;--g600:#4b5563;--g700:#374151;--g800:#1f2937;--g900:#111827
}

/* ===== Layout general ===== */
.checkout-container{max-width:1400px;margin:2rem auto;padding:0 1rem}
.progress-steps{display:flex;justify-content:center;align-items:center;gap:1rem;margin-bottom:3rem;padding:1.5rem;background:#fff;border-radius:1rem;box-shadow:0 4px 6px -1px #0000001a}
.step{display:flex;align-items:center;gap:.5rem;padding:.75rem 1.5rem;border-radius:.75rem;background:var(--g100);color:var(--g600);font-weight:600;transition:.3s}
.step .num{width:2rem;height:2rem;border-radius:50%;background:#ffffff33;display:flex;align-items:center;justify-content:center;font-weight:700}
.step.active{background:linear-gradient(135deg,var(--p),var(--p2));color:#fff;box-shadow:0 4px 6px -1px color-mix(in srgb, var(--p) 30%, transparent)}
.step-connector{width:3rem;height:2px;background:var(--g300)}
.checkout-grid{display:grid;grid-template-columns:1fr 400px;gap:2rem;align-items:start}

/* ===== Panel izquierdo (términos) ===== */
.form-panel,.summary-panel{background:#fff;border-radius:1rem;box-shadow:0 4px 6px -1px #0000001a}
.form-panel{padding:2rem}
.panel-title{font-size:1.5rem;font-weight:700;color:var(--g900);margin-bottom:.5rem;display:flex;align-items:center;gap:.75rem}
.panel-subtitle{color:var(--g600);font-size:.95rem;margin-bottom:2rem;display:flex;align-items:center;gap:.5rem}
.panel-subtitle i{color:var(--ok)}
.required-notice{background:linear-gradient(135deg,#fef3c7,#fde68a);border-left:4px solid #f59e0b;padding:1rem;border-radius:.5rem;margin-bottom:1.5rem;color:var(--g700);font-size:.9rem}
.policy-section{margin-bottom:2rem}
.policy-header{background:var(--g50);padding:1rem 1.25rem;border-radius:.75rem;margin-bottom:1rem;display:flex;justify-content:space-between;align-items:center;border:2px solid var(--g200)}
.policy-header h3{font-size:1.125rem;font-weight:600;color:var(--g800);margin:0}
.policy-version{font-size:.75rem;color:var(--g600);background:#fff;padding:.25rem .75rem;border-radius:.5rem;border:1px solid var(--g300)}
.policy-content{max-height:350px;overflow-y:auto;padding:1.5rem;background:var(--g50);border-radius:.75rem;border:2px solid var(--g200);line-height:1.8;color:var(--g700);margin-bottom:1.5rem}
.policy-content h4{color:var(--g900);font-size:1rem;font-weight:600;margin:1.5rem 0 .75rem}
.policy-content p{margin-bottom:1rem}.policy-content ul{margin:0 0 1rem 1.5rem}.policy-content li{margin-bottom:.5rem}
.policy-content::-webkit-scrollbar{width:8px}.policy-content::-webkit-scrollbar-track{background:var(--g200);border-radius:4px}.policy-content::-webkit-scrollbar-thumb{background:var(--g400);border-radius:4px}

/* ===== Aceptación de términos ===== */
.acceptance-box{background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:2px solid var(--ok);border-radius:.75rem;padding:1.5rem;margin-bottom:2rem;transition:.3s}
.acceptance-box.disabled{background:var(--g100);border-color:var(--g300);opacity:.6}
.acceptance-checkbox{display:flex;align-items:start;gap:1rem}
.acceptance-checkbox input{margin-top:.25rem}
.acceptance-checkbox input:disabled{cursor:not-allowed;opacity:.5}
.acceptance-checkbox label{flex:1;cursor:pointer;color:var(--g800);font-weight:500;line-height:1.6;user-select:none}
.acceptance-checkbox label strong{color:var(--p)}

/* ===== Panel derecho (resumen) con SCROLL en los ítems ===== */
.summary-panel{
  overflow:hidden;
  position:sticky; top:2rem;
  border:1px solid var(--g200);
  display:flex; flex-direction:column;
  max-height: calc(100vh - 3rem);
}
.summary-header{
  background:linear-gradient(135deg,var(--hdr-dark), #111);color:#fff;padding:1rem 1.25rem;
  position:sticky; top:0; z-index:2;
  display:flex; justify-content:space-between; align-items:center; gap:.5rem;
}
.summary-header h3{font-size:1.05rem;font-weight:600;margin:0}
.summary-header .count{background:#ffffff22;border:1px solid #ffffff40;padding:.1rem .5rem;border-radius:.5rem;font-size:.8rem}

/* Scroll de items */
.items-scroll{
  padding:1rem 1.25rem;
  overflow-y:auto;
  max-height: 48vh;
  border-bottom:1px dashed var(--g200);
}
.items-scroll::-webkit-scrollbar{width:8px}
.items-scroll::-webkit-scrollbar-track{background:var(--g200);border-radius:4px}
.items-scroll::-webkit-scrollbar-thumb{background:var(--g400);border-radius:4px}

/* Totales + botones (fuera del scroll) */
.summary-footer{padding:1rem 1.25rem 1.25rem}

.tour-item{padding:1.0rem;background:var(--g50);border-radius:.75rem;margin-bottom:.75rem;border:1px solid var(--g200)}
.tour-name{font-weight:600;color:var(--g900);font-size:1.0rem;margin-bottom:.5rem}
.tour-details{display:flex;flex-direction:column;gap:.35rem;font-size:.86rem;color:var(--g600);margin-bottom:.5rem}
.tour-details span{display:flex;align-items:center;gap:.5rem}
.tour-details i{color:var(--p);width:1rem}
.tour-price{font-size:1.1rem;font-weight:700;color:var(--p);text-align:right;margin-top:.25rem}
.secondary-block{background:#fff;border:1px solid var(--g200);padding:.75rem;border-radius:.6rem;margin:.5rem 0}
.block-title{display:flex;align-items:center;gap:.5rem;margin-bottom:.35rem;font-weight:700;color:var(--g800)}
.block-title i{color:var(--p)}
.badge-ghost{background:#fff;border:1px solid var(--g300);color:var(--g800);border-radius:.4rem;padding:.1rem .45rem;font-size:.7rem}

/* Totales */
.totals-section{background:var(--g50);padding:1rem;border-radius:.75rem;margin-top:1rem}
.total-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem;color:var(--g700)}
.total-row.promo{color:var(--ok);font-weight:500}
.promo-code{background:#fff;padding:.2rem .45rem;border-radius:.375rem;font-family:monospace;font-size:.85rem;border:1px solid var(--ok)}
.total-row.final{padding-top:.6rem;margin-top:.6rem;border-top:2px solid var(--g300);font-size:1.25rem;font-weight:700;color:var(--g900)}
.total-note{margin-top:.35rem;text-align:right;color:var(--g600);font-size:.8rem}

/* === Etiqueta verde de cancelación (restaurada) === */
.cancellation-badge{
  background:linear-gradient(135deg,#dcfce7,#bbf7d0);
  border:1px solid #16a34a33;
  padding:.75rem 1rem;border-radius:.75rem;margin:.75rem 0 0;
  display:flex;align-items:flex-start;gap:.75rem
}
.cancellation-badge i{color:#16a34a;font-size:1.05rem;margin-top:.15rem}
.cancellation-badge div{flex:1}
.cancellation-badge strong{display:block;color:var(--g900);font-weight:700;margin-bottom:.15rem}
.cancellation-badge small{color:var(--g700)}

/* Botones */
.btn{padding:.9rem 1.25rem;border-radius:.65rem;font-weight:600;border:0;cursor:pointer;transition:.3s;display:inline-flex;align-items:center;justify-content:center;gap:.6rem;text-decoration:none;font-size:1rem}
.btn-back{background:#fff;color:var(--g700);border:2px solid var(--g300)}
.btn-back:hover{background:var(--g50);border-color:var(--g400)}
.btn-payment{flex:1;background:linear-gradient(135deg,var(--p),var(--p2));color:#fff;box-shadow:0 4px 6px -1px color-mix(in srgb, var(--p) 30%, transparent)}
.btn-payment:hover:not(:disabled){transform:translateY(-2px);box-shadow:0 10px 15px -3px color-mix(in srgb, var(--p) 40%, transparent)}
.btn-payment:disabled{background:var(--g300);cursor:not-allowed;transform:none;box-shadow:none;opacity:.6}
.btn-details,.btn-edit{width:100%}
.btn-details{background:#fff;color:var(--p);border:2px solid var(--p);margin-top:1rem}
.btn-details:hover{background:var(--p);color:#fff}
.btn-edit{background:var(--g100);color:var(--g700);border:2px solid var(--g300);margin-top:.75rem}
.btn-edit:hover{background:var(--g200);border-color:var(--g400)}

/* Modal */
.modal-content{border-radius:1rem;border:none;box-shadow:0 20px 25px -5px #00000033}
.modal-header{background:linear-gradient(135deg,var(--hdr-dark), #111);color:#fff;border-bottom:none;padding:1.2rem 1.5rem;border-radius:1rem 1rem 0 0}
.modal-title{font-weight:700;font-size:1.1rem}
.modal-body{padding:1.5rem;max-height:70vh;overflow-y:auto}
.modal-footer{background:var(--g50);border-top:2px solid var(--g200);padding:1rem 1.5rem;border-radius:0 0 1rem 1rem}

/* Categorías */
.categories-section{background:var(--g50);padding:.9rem;border-radius:.75rem;border:1px solid var(--g200)}
.category-line{display:flex;justify-content:space-between;align-items:center;padding:.45rem 0}
.category-left{display:flex;align-items:center;gap:.6rem;color:var(--g800)}
.category-left i{color:var(--p)}
.qty-badge{background:var(--g200);color:var(--g900);padding:.2rem .45rem;border-radius:.5rem;font-size:.72rem;font-weight:600}
.price-detail{color:var(--g600);font-size:.85rem;margin-left:.4rem}
.category-total{font-weight:700;color:var(--g900)}

/* Responsivo */
@media (max-width:1400px){ .items-scroll{max-height:44vh} }
@media (max-width:1200px){ .items-scroll{max-height:42vh} }
@media (max-width:1024px){
  .checkout-grid{grid-template-columns:1fr}
  .summary-panel{position:relative;top:0;max-height:none}
  .items-scroll{max-height:40vh}
  .summary-header{position:sticky;top:0}
}
@media (max-width:768px){
  .progress-steps{flex-direction:column;gap:.75rem}
  .step-connector{display:none}
  .step{width:100%}
  .btn{width:100%}
  .items-scroll{max-height:36vh}
}
</style>
@endpush

@section('content')
@php
  use Illuminate\Support\Str;
  use Illuminate\Support\Facades\Lang;

  $fmt = fn($n)=>number_format((float)$n,2,'.','');
  $promo = session('public_cart_promo');

  // Subtotal por ítem
  $itemSub = function($it){
    $s=0;$c=collect($it->categories??[]);
    if($c->isNotEmpty()){
      foreach($c as $x){ $s+=(int)($x['quantity']??0)*(float)($x['price']??0); }
      return (float)$s;
    }
    $s+=(int)($it->adults_quantity??0)*(float)($it->tour->adult_price??0);
    $s+=(int)($it->kids_quantity??0)*(float)($it->tour->kid_price??0);
    return (float)$s;
  };

  // Mapa category_id -> nombre traducido
  $loc = app()->getLocale();
  $fb  = config('app.fallback_locale','es');

  $categoryIdsInCart = collect($cart?->items ?? [])
      ->flatMap(fn($it) => collect($it->categories ?? [])->pluck('category_id')->filter())
      ->unique()->values();

  $categoryNamesById = collect();
  if ($categoryIdsInCart->isNotEmpty()) {
    $catModels = \App\Models\CustomerCategory::whereIn('category_id', $categoryIdsInCart)
      ->with('translations')->get();

    $categoryNamesById = $catModels->mapWithKeys(function($c) use ($loc, $fb) {
      $name = method_exists($c, 'getTranslated')
        ? ($c->getTranslated('name') ?? $c->name)
        : (optional($c->translations->firstWhere('locale', $loc))->name
          ?? optional($c->translations->firstWhere('locale', $fb))->name
          ?? $c->name);
      return [$c->category_id => $name];
    });
  }

  // Resolutor label de categoría
  $resolveCatLabel = function(array $cat) use ($categoryNamesById) {
    $name = data_get($cat,'i18n_name')
        ?? data_get($cat,'name')
        ?? data_get($cat,'label')
        ?? data_get($cat,'category_name')
        ?? data_get($cat,'category.name');

    $cid = (int) (data_get($cat,'category_id') ?? data_get($cat,'id') ?? 0);
    if (!$name && $cid && $categoryNamesById->has($cid)) {
      $name = $categoryNamesById->get($cid);
    }

    if (!$name) {
      $code = Str::lower((string) data_get($cat,'code',''));
      if (in_array($code,['adult','adults'])) {
        $name = __('adminlte::adminlte.adult');
      } elseif (in_array($code,['kid','kids','child','children'])) {
        $name = __('adminlte::adminlte.kid');
      } elseif ($code !== '') {
        $tr = __($code);
        $name = ($tr === $code) ? $code : $tr;
      }
    }

    if (!$name) {
      $slug = (string) (data_get($cat,'category_slug') ?? data_get($cat,'slug') ?? '');
      if ($slug) $name = Str::of($slug)->replace(['_','-'],' ')->title();
    }

    return $name ?: __('adminlte::adminlte.category');
  };

  $raw=(float)$cart->items->sum(fn($it)=>$itemSub($it));
  $total=$raw; if($promo){$op=(($promo['operation']??'subtract')==='add')?1:-1;$total=max(0,round($raw+$op*(float)($promo['adjustment']??0),2));}

  // ===== Texto "Cancelación gratuita hasta :time el :date" (debajo del total)
$freeCancelText = null;
if (!empty($freeCancelUntil)) {
    $tz = config('app.timezone', 'America/Costa_Rica');
    $locCut = $freeCancelUntil->copy()->setTimezone($tz)->locale(app()->getLocale());

    // Usa Moment/ICU tokens válidos con isoFormat
    $cutTime = $locCut->isoFormat('LT'); // ej: 3:15 p. m.
    $cutDate = $locCut->isoFormat('LL'); // ej: 9 de noviembre de 2025

    $key = 'policies.checkout.free_cancellation_until';
    if (\Illuminate\Support\Facades\Lang::has($key)) {
        $freeCancelText = __($key, ['time' => $cutTime, 'date' => $cutDate]);
    } else {
        // Fallback legible si falta la traducción
        $freeCancelText = __('m_checkout.summary.free_cancellation') . ' — ' . $cutTime . ' · ' . $cutDate;
    }
}

@endphp

<div class="checkout-container">
  <div class="progress-steps">
    <div class="step active"><div class="num">!</div><span>{{ __('m_checkout.title') }}</span></div>
  </div>

  <div class="checkout-grid">
    {{-- Left: Terms --}}
    <div class="form-panel">
      <div class="panel-title"><i class="fas fa-shield-check"></i>{{ __('m_checkout.panels.terms_title') }}</div>
      <div class="panel-subtitle"><i class="fas fa-lock"></i>{{ __('m_checkout.panels.secure_subtitle') }}</div>

      <div class="required-notice">
        <strong>* {{ __('m_checkout.panels.required_title') }}</strong><br>
        {{ __('m_checkout.panels.required_read_accept') }}
      </div>

      <div class="policy-section">
        <div class="policy-header">
          <h3>{{ __('m_checkout.panels.terms_block_title') }}</h3>
          <span class="policy-version">{{ $termsVersion ?? 'v1' }}</span>
        </div>

        <div class="policy-content" tabindex="0" data-scroll-guard>
          @include('policies.checkout.content')
        </div>

        <form method="POST" action="{{ route('public.checkout.process') }}" id="checkout-form">
          @csrf
          <input type="hidden" name="scroll_ok" id="scroll_ok" value="0">

          <div class="acceptance-box disabled" id="accept-box">
            <div class="acceptance-checkbox">
              <input type="checkbox" id="accept_terms" name="accept_terms" value="1" {{ old('accept_terms')?'checked':'' }} disabled>
              <label for="accept_terms">{!! __('m_checkout.accept.label_html') !!}</label>
            </div>
            @error('accept_terms')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
          </div>

          <div class="d-flex gap-3 mt-3">
            <a href="{{ route('public.carts.index') }}" class="btn btn-back">
              <i class="fas fa-arrow-left"></i>{{ __('m_checkout.buttons.back') }}
            </a>
            <button type="submit" id="btn-proceed" class="btn btn-payment" {{ old('accept_terms')?'':'disabled' }}>
              {{ __('m_checkout.buttons.go_to_payment') }} <i class="fas fa-arrow-right"></i>
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- Right: Summary with internal scroll --}}
    <div class="summary-panel">
      <div class="summary-header">
        <h3>{{ __('m_checkout.summary.title') }}</h3>
        @php $cnt = $cart->items->count(); @endphp
        <span class="count">{{ $cnt }} {{ $cnt===1 ? __('m_checkout.summary.item') : __('m_checkout.summary.items') }}</span>
      </div>

      <div class="items-scroll">
        @foreach($cart->items as $it)
          @php
            $s=$itemSub($it);
            $cats=collect($it->categories??[]);
            $totalPax=$cats->isNotEmpty()? $cats->sum(fn($c)=>(int)data_get($c,'quantity',0)) : (int)($it->adults_quantity??0)+(int)($it->kids_quantity??0);

            $ref = $cart->public_reference ?? $cart->id ?? null;
            $line = $it->id ?? $it->cart_item_id ?? null;
            $mp = data_get($it,'meetingPoint');
            $hotel = data_get($it,'hotel');
            $pickupAt = data_get($it,'pickup_time');
            $addons = collect(data_get($it,'addons',[]));
            $duration = data_get($it,'tour.length') ?? data_get($it,'duration');
            $guide = data_get($it,'guide.name');
            $notes = data_get($it,'notes') ?? data_get($it,'special_requests');
            $tz = config('app.timezone','America/Costa_Rica');
          @endphp

          <div class="tour-item">
            <div class="tour-name">{{ $it->tour->getTranslatedName() ?? $it->tour->name }}</div>

            @if($ref || $line)
              <div class="d-flex flex-wrap" style="gap:.4rem; margin-bottom:.35rem">
                @if($ref)<span class="badge-ghost"><i class="fas fa-hashtag me-1"></i>{{ __('m_checkout.blocks.ref') }}: {{ $ref }}</span>@endif
                @if($line)<span class="badge-ghost"><i class="fas fa-stream me-1"></i>{{ __('m_checkout.blocks.item') }}: {{ $line }}</span>@endif
              </div>
            @endif

            <div class="tour-details">
              <span><i class="far fa-calendar-alt"></i>{{ \Carbon\Carbon::parse($it->tour_date)->format('l, F d, Y') }}</span>
              @if($it->schedule)
                <span><i class="far fa-clock"></i>{{ __('m_checkout.misc.at') }} {{ \Carbon\Carbon::parse($it->schedule->start_time)->format('g:i A') }}</span>
              @endif
              @if($totalPax>0)
                <span><i class="fas fa-user"></i>{{ $totalPax }} {{ $totalPax===1?__('m_checkout.misc.participant'):__('m_checkout.misc.participants') }}</span>
              @endif
              @if($it->language)
                <span><i class="fas fa-language"></i>{{ $it->language->name }}</span>
              @endif
            </div>

            @if($hotel || $mp || $pickupAt)
              <div class="secondary-block">
                <div class="block-title">
                  <i class="fas fa-map-marker-alt"></i>
                  <span>{{ __('m_checkout.blocks.pickup_meeting') }}</span>
                </div>
                <div class="small" style="color:var(--g700)">
                  @if($hotel)
                    <div class="mb-1"><strong>{{ __('m_checkout.blocks.hotel') }}:</strong> {{ $hotel->name ?? '' }} @if(data_get($hotel,'address')) — {{ $hotel->address }} @endif</div>
                  @endif
                  @if($mp)
                    <div class="mb-1"><strong>{{ __('m_checkout.blocks.meeting_point') }}:</strong> {{ $mp->name ?? '' }} @if(data_get($mp,'address')) — {{ $mp->address }} @endif</div>
                    @if(data_get($mp,'notes'))<div class="text-muted">{{ $mp->notes }}</div>@endif
                  @endif
                  @if($pickupAt)
                    <div class="mt-1"><i class="far fa-clock"></i>
                      <strong>{{ __('m_checkout.blocks.pickup_time') }}:</strong> {{ \Carbon\Carbon::parse($pickupAt)->format('g:i A') }} ({{ $tz }})
                    </div>
                  @endif
                </div>
              </div>
            @endif

            @if($addons->isNotEmpty())
              <div class="secondary-block">
                <div class="block-title"><i class="fas fa-plus-circle"></i><span>{{ __('m_checkout.blocks.add_ons') }}</span></div>
                @foreach($addons as $ad)
                  @php $aq=(int)data_get($ad,'quantity',0); $ap=(float)data_get($ad,'price',0); $at=$aq*$ap; @endphp
                  @if($aq>0)
                    <div class="d-flex justify-content-between align-items-center" style="gap:.5rem;padding:.25rem 0">
                      <div class="d-flex align-items-center" style="gap:.5rem">
                        <i class="fas fa-tag" style="color:var(--p)"></i>
                        <span class="small">{{ data_get($ad,'name','Extra') }}</span>
                        <span class="qty-badge">{{ $aq }}x</span>
                        <span class="price-detail small">(${{ $fmt($ap) }} × {{ $aq }})</span>
                      </div>
                      <div class="fw-bold">${{ $fmt($at) }}</div>
                    </div>
                  @endif
                @endforeach
              </div>
            @endif

            @if($duration || $guide)
              <div class="secondary-block">
                <div class="d-flex flex-wrap gap-3 small" style="color:var(--g700)">
                  @if($duration)
                    <div><i class="far fa-hourglass" style="color:var(--p)"></i>
                      <strong>{{ __('m_checkout.blocks.duration') }}:</strong> {{ $duration }} {{ __('m_checkout.blocks.hours') }}
                    </div>
                  @endif
                  @if($guide)
                    <div><i class="fas fa-user-tie" style="color:var(--p)"></i>
                      <strong>{{ __('m_checkout.blocks.guide') }}:</strong> {{ $guide }}
                    </div>
                  @endif
                </div>
              </div>
            @endif

            @if($notes)
              <div class="secondary-block">
                <div class="block-title"><i class="fas fa-sticky-note"></i><span>{{ __('m_checkout.blocks.notes') }}</span></div>
                <div class="small" style="color:var(--g700)">{{ $notes }}</div>
              </div>
            @endif

            @php $showCats = $cats->isNotEmpty() || (int)($it->adults_quantity??0)>0 || (int)($it->kids_quantity??0)>0; @endphp
            @if($showCats)
              <div class="categories-section" style="margin-top:.5rem">
                @if($cats->isNotEmpty())
                  @foreach($cats as $c)
                    @php
                      $q   = (int)   data_get($c,'quantity',0);
                      $u   = (float) data_get($c,'price',0);
                      $sub = $q * $u;
                      $lab = $resolveCatLabel((array)$c);
                      $code = Str::lower((string) data_get($c,'code',''));
                      $isAdult = in_array($code,['adult','adults']);
                      $isKid   = in_array($code,['kid','kids','child','children']);
                    @endphp
                    @if($q>0)
                      <div class="category-line">
                        <div class="category-left">
                          @if($isAdult)
                            <i class="fas fa-user"></i><strong>{{ __('m_checkout.categories.adult') }}</strong>
                          @elseif($isKid)
                            <i class="fas fa-child"></i><strong>{{ __('m_checkout.categories.kid') }}</strong>
                          @else
                            <i class="fas fa-user-friends"></i><span>{{ $lab }}</span>
                          @endif
                          <span class="qty-badge">{{ $q }}x</span><span class="price-detail">(${{ $fmt($u) }} × {{ $q }})</span>
                        </div>
                        <div class="category-total">${{ $fmt($sub) }}</div>
                      </div>
                    @endif
                  @endforeach
                @else
                  @if((int)($it->adults_quantity??0)>0)
                    @php $q=(int)$it->adults_quantity; $u=(float)($it->tour->adult_price??0); @endphp
                    <div class="category-line">
                      <div class="category-left">
                        <i class="fas fa-user"></i><strong>{{ __('m_checkout.categories.adult') }}</strong>
                        <span class="qty-badge">{{ $q }}x</span><span class="price-detail">(${{ $fmt($u) }} × {{ $q }})</span>
                      </div>
                      <div class="category-total">${{ $fmt($q*$u) }}</div>
                    </div>
                  @endif
                  @if((int)($it->kids_quantity??0)>0)
                    @php $q=(int)$it->kids_quantity; $u=(float)($it->tour->kid_price??0); @endphp
                    <div class="category-line">
                      <div class="category-left">
                        <i class="fas fa-child"></i><strong>{{ __('m_checkout.categories.kid') }}</strong>
                        <span class="qty-badge">{{ $q }}x</span><span class="price-detail">(${{ $fmt($u) }} × {{ $q }})</span>
                      </div>
                      <div class="category-total">${{ $fmt($q*$u) }}</div>
                    </div>
                  @endif
                @endif
              </div>
            @endif

            <div class="tour-price">${{ $fmt($s) }}</div>
          </div>
        @endforeach
      </div>

      <div class="summary-footer">
        <div class="totals-section">
          <div class="total-row"><span>{{ __('m_checkout.summary.subtotal') }}</span><span>${{ $fmt($raw) }}</span></div>
          @if($promo)
            <div class="total-row promo">
              <span>{{ __('m_checkout.summary.promo_code') }} <span class="promo-code">{{ $promo['code'] }}</span></span>
              <span>{{ ($promo['operation']??'subtract')==='subtract'?'-':'+' }}${{ $fmt($promo['adjustment']??0) }}</span>
            </div>
          @endif
          @php $total=$raw; if($promo){$op=(($promo['operation']??'subtract')==='add')?1:-1;$total=max(0,round($raw+$op*(float)($promo['adjustment']??0),2));} @endphp
          <div class="total-row final"><span>{{ __('m_checkout.summary.total') }}</span><span>${{ $fmt($total) }}</span></div>
          <div class="total-note">{{ __('m_checkout.summary.taxes_included') }}</div>

          {{-- === Etiqueta VERDE (restaurada) debajo del total === --}}
          @if($freeCancelText)
            <div class="cancellation-badge">
              <i class="fas fa-check-circle"></i>
              <div>
                <strong>{{ __('m_checkout.summary.free_cancellation') }}</strong>
                <small>{{ $freeCancelText }}</small>
              </div>
            </div>
          @endif
        </div>

        <button class="btn btn-details" data-bs-toggle="modal" data-bs-target="#orderModal">
          <i class="fas fa-list"></i>{{ __('m_checkout.buttons.view_details') }}
        </button>
        <a href="{{ route('public.carts.index') }}" class="btn btn-edit">
          <i class="fas fa-edit"></i>{{ __('m_checkout.buttons.edit') }}
        </a>
      </div>
    </div>
  </div>
</div>

{{-- Modal de detalle --}}
<div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-receipt me-2"></i>{{ __('m_checkout.summary.order_details') }}
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('m_checkout.buttons.close') }}"></button>
      </div>
      <div class="modal-body">
        @foreach($cart->items as $it)
          @php $s=$itemSub($it); @endphp
          <div class="item-detail">
            <div class="item-header d-flex justify-content-between align-items-center">
              <strong>{{ $it->tour->getTranslatedName() ?? $it->tour->name }}</strong>
              <span class="price">${{ $fmt($s) }}</span>
            </div>
            <div class="item-meta d-flex flex-wrap gap-3">
              <span><i class="far fa-calendar-alt"></i>{{ \Carbon\Carbon::parse($it->tour_date)->format('l, F d, Y') }}</span>
              @if($it->schedule)<span><i class="far fa-clock"></i>{{ \Carbon\Carbon::parse($it->schedule->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($it->schedule->end_time)->format('g:i A') }}</span>@endif
              @if($it->language)<span><i class="fas fa-language"></i>{{ $it->language->name }}</span>@endif
            </div>

            @php
              $cats=collect($it->categories??[]);
              $ref = $cart->public_reference ?? $cart->id ?? null;
              $line = $it->id ?? $it->cart_item_id ?? null;
              $mp = data_get($it,'meetingPoint');  $hotel = data_get($it,'hotel');
              $pickupAt = data_get($it,'pickup_time'); $addons = collect(data_get($it,'addons',[]));
              $duration = data_get($it,'tour.length') ?? data_get($it,'duration'); $guide = data_get($it,'guide.name');
              $notes = data_get($it,'notes') ?? data_get($it,'special_requests'); $tz = config('app.timezone','America/Costa_Rica');
            @endphp

            @if($ref || $line)
              <div class="d-flex flex-wrap gap-2 mt-2">
                @if($ref)<span class="badge-ghost"><i class="fas fa-hashtag me-1"></i>{{ __('m_checkout.blocks.ref') }}: {{ $ref }}</span>@endif
                @if($line)<span class="badge-ghost"><i class="fas fa-stream me-1"></i>{{ __('m_checkout.blocks.item') }}: {{ $line }}</span>@endif
              </div>
            @endif

            @if($hotel || $mp || $pickupAt)
              <div class="categories-section mt-2">
                <div class="d-flex align-items-center mb-2" style="gap:.5rem">
                  <i class="fas fa-map-marker-alt" style="color:var(--p)"></i><strong>{{ __('m_checkout.blocks.pickup_meeting') }}</strong>
                </div>
                <div class="small" style="color:var(--g700)">
                  @if($hotel)<div class="mb-1"><strong>{{ __('m_checkout.blocks.hotel') }}:</strong> {{ $hotel->name ?? '' }} @if(data_get($hotel,'address')) — {{ $hotel->address }} @endif</div>@endif
                  @if($mp)<div class="mb-1"><strong>{{ __('m_checkout.blocks.meeting_point') }}:</strong> {{ $mp->name ?? '' }} @if(data_get($mp,'address')) — {{ $mp->address }} @endif</div>@endif
                  @if(data_get($mp,'notes'))<div class="text-muted">{{ $mp->notes }}</div>@endif
                  @if($pickupAt)<div class="mt-1"><i class="far fa-clock"></i> <strong>{{ __('m_checkout.blocks.pickup_time') }}:</strong> {{ \Carbon\Carbon::parse($pickupAt)->format('g:i A') }} ({{ $tz }})</div>@endif
                </div>
              </div>
            @endif

            @if($addons->isNotEmpty())
              <div class="categories-section mt-2">
                <div class="d-flex align-items-center mb-2" style="gap:.5rem">
                  <i class="fas fa-plus-circle" style="color:var(--p)"></i><strong>{{ __('m_checkout.blocks.add_ons') }}</strong>
                </div>
                @foreach($addons as $ad)
                  @php $aq=(int)data_get($ad,'quantity',0); $ap=(float)data_get($ad,'price',0); $at=$aq*$ap; @endphp
                  @if($aq>0)
                    <div class="category-line">
                      <div class="category-left"><i class="fas fa-tag"></i>
                        <span>{{ data_get($ad,'name','Extra') }}</span>
                        <span class="qty-badge">{{ $aq }}x</span>
                        <span class="price-detail">(${{ $fmt($ap) }} × {{ $aq }})</span>
                      </div>
                      <div class="category-total">${{ $fmt($at) }}</div>
                    </div>
                  @endif
                @endforeach
              </div>
            @endif

            @if($duration || $guide)
              <div class="d-flex flex-wrap gap-3 mt-2 small" style="color:var(--g700)">
                @if($duration)<div><i class="far fa-hourglass"></i> <strong>{{ __('m_checkout.blocks.duration') }}:</strong> {{ $duration }} {{ __('m_checkout.blocks.hours') }}</div>@endif
                @if($guide)<div><i class="fas fa-user-tie"></i> <strong>{{ __('m_checkout.blocks.guide') }}:</strong> {{ $guide }}</div>@endif
              </div>
            @endif

            @if($cats->isNotEmpty())
              <div class="categories-section">
                @foreach($cats as $c)
                  @php
                    $q   = (int)   data_get($c,'quantity',0);
                    $u   = (float) data_get($c,'price',0);
                    $sub = $q * $u;
                    $lab = $resolveCatLabel((array)$c);
                    $code = Str::lower((string) data_get($c,'code',''));
                    $isAdult = in_array($code,['adult','adults']);
                    $isKid   = in_array($code,['kid','kids','child','children']);
                  @endphp
                  @if($q>0)
                    <div class="category-line">
                      <div class="category-left">
                        @if($isAdult)
                          <i class="fas fa-user"></i><strong>{{ __('m_checkout.categories.adult') }}</strong>
                        @elseif($isKid)
                          <i class="fas fa-child"></i><strong>{{ __('m_checkout.categories.kid') }}</strong>
                        @else
                          <i class="fas fa-user-friends"></i><span>{{ $lab }}</span>
                        @endif
                        <span class="qty-badge">{{ $q }}x</span><span class="price-detail">(${{ $fmt($u) }} × {{ $q }})</span>
                      </div>
                      <div class="category-total">${{ $fmt($sub) }}</div>
                    </div>
                  @endif
                @endforeach
              </div>
            @else
              <div class="categories-section">
                @if((int)($it->adults_quantity??0)>0)
                  @php $q=(int)$it->adults_quantity; $u=(float)($it->tour->adult_price??0); @endphp
                  <div class="category-line">
                    <div class="category-left">
                      <i class="fas fa-user"></i><strong>{{ __('m_checkout.categories.adult') }}</strong>
                      <span class="qty-badge">{{ $q }}x</span><span class="price-detail">(${{ $fmt($u) }} × {{ $q }})</span>
                    </div>
                    <div class="category-total">${{ $fmt($q*$u) }}</div>
                  </div>
                @endif
                @if((int)($it->kids_quantity??0)>0)
                  @php $q=(int)$it->kids_quantity; $u=(float)($it->tour->kid_price??0); @endphp
                  <div class="category-line">
                    <div class="category-left">
                      <i class="fas fa-child"></i><strong>{{ __('m_checkout.categories.kid') }}</strong>
                      <span class="qty-badge">{{ $q }}x</span><span class="price-detail">(${{ $fmt($u) }} × {{ $q }})</span>
                    </div>
                    <div class="category-total">${{ $fmt($q*$u) }}</div>
                  </div>
                @endif
              </div>
            @endif
          </div>
        @endforeach
      </div>

      <div class="modal-footer">
        <div class="w-100">
          <div class="total-row d-flex justify-content-between">
            <span>{{ __('m_checkout.summary.subtotal') }}</span><span style="font-weight:600">${{ $fmt($raw) }}</span>
          </div>
          @if($promo)
            <div class="total-row promo d-flex justify-content-between">
              <span>{{ __('m_checkout.summary.promo_code') }} <span class="promo-code">{{ $promo['code'] }}</span></span>
              <span style="font-weight:600">{{ ($promo['operation']??'subtract')==='subtract' ? '-' : '+' }}${{ $fmt($promo['adjustment']??0) }}</span>
            </div>
          @endif
          <div class="total-row final d-flex justify-content-between">
            <span>{{ __('m_checkout.summary.total') }}</span><span>${{ $fmt($total) }}</span>
          </div>

          @if($freeCancelText)
            <div class="cancellation-badge mt-2">
              <i class="fas fa-check-circle"></i>
              <div>
                <strong>{{ __('m_checkout.summary.free_cancellation') }}</strong>
                <small>{{ $freeCancelText }}</small>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded',()=>{
  /* 1) Limpia títulos duplicados en el include */
  const cont = document.querySelector('.policy-content');
  if (cont){
    const norm = s => (s||'')
      .replace(/\b(vers(i|í)on|version|v)\s*\d+(\.\d+)?/ig,'')
      .replace(/[^\p{L}\p{N}\s]/gu,'')
      .trim().toLowerCase();
    const hs = [...cont.querySelectorAll('h1,h2,h3')];
    for (let i=0;i<hs.length-1;i++){
      const a=hs[i], b=hs[i+1];
      if (['H1','H2','H3'].includes(a.tagName) && ['H1','H2','H3'].includes(b.tagName)){
        if (norm(a.textContent) && norm(a.textContent) === norm(b.textContent)){
          b.remove();
        }
      }
    }
  }

  /* 2) Desbloquear checkbox al leer hasta el final */
  const box=document.querySelector('[data-scroll-guard]'),
        chk=document.getElementById('accept_terms'),
        btn=document.getElementById('btn-proceed'),
        flag=document.getElementById('scroll_ok'),
        abox=document.getElementById('accept-box');
  if(box && chk && btn && flag){
    const unlock=()=>{chk.disabled=false;flag.value='1';abox?.classList.remove('disabled')};
    const atBottom=el=>el.scrollTop+el.clientHeight>=el.scrollHeight-6;
    if(chk.checked||box.scrollHeight<=box.clientHeight||atBottom(box)) unlock();
    else box.addEventListener('scroll',function onS(){ if(atBottom(box)){ unlock(); box.removeEventListener('scroll',onS);} });
    chk.addEventListener('change',()=>{btn.disabled=!chk.checked});
  }
});
</script>
@endpush
