@extends('adminlte::page')

@section('title', 'Ordenar Tours por Categoría')

@section('content_header')
  <h1 class="mb-0">Ordenar Tours por Categoría</h1>
@stop

@push('css')
<style>
  :root{
    --card-bg: #ffffff;
    --card-text: #212529;
    --muted: #6c757d;
    --surface: #f8f9fa;
    --border: #e3e6ea;
    --accent: #0d6efd;
  }

  .order-wrap{
    width: 100%;
    max-width: 980px;
    margin: 0 auto;
  }

  @media (max-width: 992px){
    .order-wrap{ max-width: 860px; }
  }
  @media (max-width: 768px){
    .order-wrap{ max-width: 680px; }
  }
  @media (max-width: 576px){
    .order-wrap{ max-width: 100%; padding: 0 .25rem; }
  }

  .helper-text{
    color: var(--border);
    font-size: .95rem;
    margin-bottom: .75rem;
  }

  .list{ min-height: 48px; }

  .tour-item{
    display: grid;
    grid-template-columns: 56px 1fr 28px;
    align-items: center;
    gap: .5rem;
    padding: .7rem .85rem .7rem .7rem;
    margin-bottom: .6rem;
    border: 1px solid var(--border);
    border-radius: .75rem;
    background: none;
    color: var(--card-bg);
    box-shadow: 0 2px 6px rgba(0,0,0,.05);
    transition: transform .12s ease, box-shadow .12s ease, background .12s ease;
    cursor: grab;
  }
  .tour-item:hover{ box-shadow: 0 4px 14px rgba(0,0,0,.08); }
  .tour-item.dragging{ opacity:.75; transform: scale(.997); cursor: grabbing; }

  .tour-index{
    width: 40px; height: 40px;
    display:flex; align-items:center; justify-content:center;
    border-radius: .6rem;
    background: var(--surface);
    border: 1px solid var(--border);
    font-weight: 700;
    color: var(--accent);
    user-select: none;
  }

  .tour-name{
    font-weight: 600;
    line-height: 1.25;
    word-break: break-word;
  }
  .tour-meta{
    font-size: .85rem;
    color: var(--muted);
    margin-top: .15rem;
  }
  .badge-inactive{
    display:inline-block;
    font-size:.72rem;
    background:#f5d7d7;
    color:#8a2b2b;
    padding:.15rem .45rem;
    border-radius:.4rem;
    margin-left:.4rem;
    vertical-align: middle;
  }

  .handle{
    color: var(--muted);
    font-size: 1.15rem;
    text-align: right;
    user-select: none;
  }

  .ghost{
    border: 2px dashed #9aa0a6;
    background: #f1f3f5;
    color: var(--card-text);
    box-shadow: none;
  }

  /* Responsive tweaks */
  @media (max-width: 576px){
    .tour-item{
      grid-template-columns: 48px 1fr 28px;
      padding: .6rem .7rem;
    }
    .tour-index{ width: 36px; height: 36px; font-size: .95rem; }
    .tour-name{ font-size: .95rem; }
  }
</style>
@endpush

@section('content')
<div class="order-wrap">

  <form method="get" action="{{ route('admin.products.order.index') }}" class="mb-3">
    <label class="form-label fw-semibold">Categoría</label>
    <select name="product_type_id" class="form-control" onchange="this.form.submit()">
      <option value="">— Selecciona una categoría —</option>
      @foreach ($types as $t)
        <option value="{{ $t->product_type_id }}" @selected(optional($selected)->product_type_id === $t->product_type_id)>
          {{ $t->name }}
        </option>
      @endforeach
    </select>
  </form>

  @if ($selected)
    <div class="helper-text">
      Arrastra para reordenar. Luego pulsa <strong>Guardar</strong>.
    </div>

    <div id="list" class="list" data-type="{{ $selected->product_type_id }}">
      @foreach ($products as $i => $row)
        <div class="tour-item" data-id="{{ $row->product_id }}">
          <div class="tour-index">{{ $i + 1 }}</div>

          <div>
            <div class="tour-name">
              {{ $row->name ?? '— Sin nombre —' }}
              @unless($row->is_active)
                <span class="badge-inactive">inactivo</span>
              @endunless
            </div>
          </div>

          <div class="handle" title="Arrastrar">
            <i class="fas fa-grip-vertical"></i>
          </div>
        </div>
      @endforeach
    </div>

    <button id="saveBtn" class="btn btn-primary mt-3">
      <i class="fas fa-save"></i> <span class="btn-text">Guardar</span>
    </button>
  @endif

</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
(function(){
  const list = document.getElementById('list');
  if(!list) return;

  // Initialize SortableJS
  new Sortable(list, {
      animation: 150,    // Smooth animation
      ghostClass: 'ghost', // Class for the placeholder
      onSort: function() {
          renumber();
      }
  });

  function getItems(){
    return Array.from(list.querySelectorAll('.tour-item'));
  }

  function renumber(){
    getItems().forEach((el, idx) => {
      const badge = el.querySelector('.tour-index');
      if(badge) badge.textContent = String(idx + 1);
    });
  }

  const saveBtn = document.getElementById('saveBtn');
  saveBtn?.addEventListener('click', async function(){
    const btn = this;
    const btnText = btn.querySelector('.btn-text');
    const prev = btnText ? btnText.textContent : 'Guardar';

    const typeId = list.dataset.type;
    const order = getItems().map(el => parseInt(el.dataset.id, 10));

    btn.disabled = true;
    if(btnText) btnText.textContent = 'Guardando…';

    const res = await fetch(`{{ url('admin/products/order') }}/${typeId}/save`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ order })
    });

    btn.disabled = false;
    if(btnText) btnText.textContent = prev;

    if(res.ok){
      if(window.Swal){
        Swal.fire({ icon:'success', title:'Orden guardado', timer:1300, showConfirmButton:false });
      } else {
        alert('Orden guardado');
      }
    } else {
      const txt = await res.text();
      // Try to parse JSON error if possible
      let errMsg = txt;
      try {
          const json = JSON.parse(txt);
          if(json.error) errMsg = json.error;
      } catch(e){}
      
      if(window.Swal){
        Swal.fire({ icon:'error', title:'Error al guardar', text: errMsg || 'Intenta de nuevo' });
      } else {
        alert('Error al guardar: ' + errMsg);
      }
    }
  });
})();
</script>
@endpush
