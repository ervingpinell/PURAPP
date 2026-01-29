{{-- resources/views/admin/products/wizard/steps/prices.blade.php --}}
{{-- NUEVO SISTEMA DE PRICING FLEXIBLE --}}

@extends('adminlte::page')

@section('title', __('m_tours.tour.wizard.steps.prices'))

@push('css')
<style>
    /* ==========================================
       ESTRATEGIA SELECTOR
       ========================================== */
    .strategy-selector {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .strategy-card {
        background: #343a40;
        border: 2px solid #454d55;
        border-radius: 0.75rem;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }

    .strategy-card:hover {
        border-color: #667eea;
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
    }

    .strategy-card.active {
        border-color: #667eea;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    }

    .strategy-card.active::before {
        content: '✓';
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        background: #667eea;
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.875rem;
    }

    .strategy-icon {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        display: block;
    }

    .strategy-name {
        font-weight: 600;
        font-size: 0.9375rem;
        color: #c2c7d0;
        margin-bottom: 0.25rem;
    }

    .strategy-description {
        font-size: 0.75rem;
        color: #6c757d;
        line-height: 1.4;
    }

    /* ==========================================
       CONFIGURADORES
       ========================================== */
    .configurator-container {
        background: #343a40;
        border: 1px solid #454d55;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .configurator-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #454d55;
    }

    .configurator-header h4 {
        margin: 0;
        color: #c2c7d0;
        font-size: 1.125rem;
    }

    .rule-row {
        background: #3d444b;
        border: 1px solid #454d55;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
        display: grid;
        gap: 1rem;
        align-items: end;
    }

    .rule-row.flat-rate-grid {
        grid-template-columns: 1fr 1fr 1fr auto;
    }

    .rule-row.tiered-grid {
        grid-template-columns: 1fr 1fr 1fr 1fr auto;
    }

    .rule-row.category-grid {
        grid-template-columns: 2fr 1fr auto;
    }

    .rule-row.tiered-category-grid {
        grid-template-columns: 1.5fr 1fr 1fr 1fr auto;
    }

    .form-group-inline {
        margin-bottom: 0;
    }

    .form-group-inline label {
        font-size: 0.8125rem;
        color: #c2c7d0;
        margin-bottom: 0.25rem;
        display: block;
    }

    .form-group-inline input,
    .form-group-inline select {
        background: #343a40;
        border: 1px solid #6c757d;
        color: #fff;
        font-size: 0.875rem;
        padding: 0.5rem;
        border-radius: 0.25rem;
        width: 100%;
    }

    .form-group-inline input:focus,
    .form-group-inline select:focus {
        border-color: #667eea;
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .btn-add-rule {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        border: none;
        padding: 0.625rem 1.25rem;
        border-radius: 0.5rem;
        font-weight: 600;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-add-rule:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(72, 187, 120, 0.4);
        color: white;
    }

    .btn-remove-rule {
        background: #dc3545;
        color: white;
        border: none;
        padding: 0.5rem 0.75rem;
        border-radius: 0.25rem;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-remove-rule:hover {
        background: #c82333;
    }

    /* ==========================================
       PREVIEW
       ========================================== */
    .preview-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 0.75rem;
        padding: 1.5rem;
        color: white;
    }

    .preview-header {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .preview-inputs {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .preview-inputs input {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 0.5rem;
        border-radius: 0.25rem;
    }

    .preview-inputs input::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    .preview-result {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 0.5rem;
        padding: 1rem;
        margin-top: 1rem;
    }

    .preview-total {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .preview-breakdown {
        font-size: 0.875rem;
        opacity: 0.9;
    }

    /* ==========================================
       UTILITIES
       ========================================== */
    .hidden {
        display: none !important;
    }

    .alert-info-custom {
        background: #17a2b8;
        color: white;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .strategy-selector {
            grid-template-columns: 1fr;
        }

        .rule-row.flat-rate-grid,
        .rule-row.tiered-grid,
        .rule-row.category-grid,
        .rule-row.tiered-category-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Wizard Stepper --}}
    @include('admin.products.wizard.partials.stepper', ['currentStep' => $step])

    {{-- Header --}}
    <div class="card mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
        <div class="card-body">
            <h1 class="mb-2" style="font-size: 1.75rem; font-weight: 600;">
                <i class="fas fa-dollar-sign mr-2"></i>
                {{ __('m_tours.tour.wizard.steps.prices') }}
            </h1>
            <p class="mb-0" style="opacity: 0.9;">
                Configura la estrategia de precios para tu producto
            </p>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h5><i class="icon fas fa-ban"></i> {{ __('m_tours.common.validation_errors') }}</h5>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.products.wizard.store.prices', $product) }}" method="POST" id="pricing-form">
        @csrf

        {{-- Strategy Selector --}}
        <div class="card mb-4">
            <div class="card-header bg-dark">
                <h5 class="mb-0">
                    <i class="fas fa-list-ul mr-2"></i>
                    Selecciona la Estrategia de Pricing
                </h5>
            </div>
            <div class="card-body">
                <div class="strategy-selector">
                    <div class="strategy-card" data-strategy="flat_rate">
                        <span class="strategy-icon">💵</span>
                        <div class="strategy-name">Flat Rate</div>
                        <div class="strategy-description">Precio fijo por grupo según tamaño</div>
                    </div>

                    <div class="strategy-card" data-strategy="per_person">
                        <span class="strategy-icon">👤</span>
                        <div class="strategy-name">Per Person</div>
                        <div class="strategy-description">Precio único por persona</div>
                    </div>

                    <div class="strategy-card" data-strategy="per_category">
                        <span class="strategy-icon">🏷️</span>
                        <div class="strategy-name">Per Category</div>
                        <div class="strategy-description">Precio diferente por categoría</div>
                    </div>

                    <div class="strategy-card" data-strategy="tiered">
                        <span class="strategy-icon">📊</span>
                        <div class="strategy-name">Tiered</div>
                        <div class="strategy-description">Precio escalonado por tamaño</div>
                    </div>

                    <div class="strategy-card" data-strategy="tiered_per_category">
                        <span class="strategy-icon">📈</span>
                        <div class="strategy-name">Tiered + Category</div>
                        <div class="strategy-description">Escalonado con categorías</div>
                    </div>
                </div>

                <input type="hidden" name="strategy_type" id="strategy_type" value="{{ old('strategy_type', $activeStrategy->strategy_type ?? '') }}">
            </div>
        </div>

        {{-- Configurators (Dynamic) --}}
        <div id="configurators-container">
            {{-- Flat Rate Configurator --}}
            <div class="configurator-container hidden" data-configurator="flat_rate">
                <div class="configurator-header">
                    <span style="font-size: 1.5rem;">💵</span>
                    <h4>Configuración: Precio por Grupo (Flat Rate)</h4>
                </div>
                <div class="alert-info-custom">
                    <i class="fas fa-info-circle mr-2"></i>
                    Define precios fijos para diferentes tamaños de grupo. Ejemplo: 1-9 personas = $300, 10-20 personas = $500
                </div>
                <div id="flat-rate-rules"></div>
                <button type="button" class="btn-add-rule" data-add-rule="flat_rate">
                    <i class="fas fa-plus-circle"></i>
                    Agregar Rango
                </button>
            </div>

            {{-- Per Person Configurator --}}
            <div class="configurator-container hidden" data-configurator="per_person">
                <div class="configurator-header">
                    <span style="font-size: 1.5rem;">👤</span>
                    <h4>Configuración: Precio por Persona</h4>
                </div>
                <div class="alert-info-custom">
                    <i class="fas fa-info-circle mr-2"></i>
                    Define un precio único por persona, sin importar el tamaño del grupo.
                </div>
                <div class="form-group-inline">
                    <label>Precio por Persona (USD)</label>
                    <input type="number" name="per_person_price" step="0.01" min="0" class="form-control" placeholder="50.00">
                </div>
            </div>

            {{-- Per Category Configurator --}}
            <div class="configurator-container hidden" data-configurator="per_category">
                <div class="configurator-header">
                    <span style="font-size: 1.5rem;">🏷️</span>
                    <h4>Configuración: Precio por Categoría</h4>
                </div>
                <div class="alert-info-custom">
                    <i class="fas fa-info-circle mr-2"></i>
                    Define precios diferentes para cada categoría de cliente (Adulto, Niño, etc.)
                </div>
                <div id="per-category-rules"></div>
                <button type="button" class="btn-add-rule" data-add-rule="per_category">
                    <i class="fas fa-plus-circle"></i>
                    Agregar Categoría
                </button>
            </div>

            {{-- Tiered Configurator --}}
            <div class="configurator-container hidden" data-configurator="tiered">
                <div class="configurator-header">
                    <span style="font-size: 1.5rem;">📊</span>
                    <h4>Configuración: Precio Escalonado (Tiered)</h4>
                </div>
                <div class="alert-info-custom">
                    <i class="fas fa-info-circle mr-2"></i>
                    El precio por persona varía según el tamaño total del grupo. Ejemplo: 2-6 personas = $100/persona, 7-10 = $80/persona
                </div>
                <div id="tiered-rules"></div>
                <button type="button" class="btn-add-rule" data-add-rule="tiered">
                    <i class="fas fa-plus-circle"></i>
                    Agregar Tier
                </button>
            </div>

            {{-- Tiered Per Category Configurator --}}
            <div class="configurator-container hidden" data-configurator="tiered_per_category">
                <div class="configurator-header">
                    <span style="font-size: 1.5rem;">📈</span>
                    <h4>Configuración: Tiered + Categorías</h4>
                </div>
                <div class="alert-info-custom">
                    <i class="fas fa-info-circle mr-2"></i>
                    Combina precios escalonados con categorías. Cada categoría tiene precios diferentes según el tamaño del grupo.
                </div>
                <div id="tiered-category-rules"></div>
                <button type="button" class="btn-add-rule" data-add-rule="tiered_per_category">
                    <i class="fas fa-plus-circle"></i>
                    Agregar Regla
                </button>
            </div>
        </div>

        {{-- Preview --}}
        <div class="preview-container">
            <div class="preview-header">
                <i class="fas fa-calculator"></i>
                Preview de Precios
            </div>
            <div class="preview-inputs">
                <input type="number" id="preview-total-passengers" min="1" value="5" placeholder="Total pasajeros">
                <input type="number" id="preview-adults" min="0" value="3" placeholder="Adultos">
                <input type="number" id="preview-kids" min="0" value="2" placeholder="Niños">
            </div>
            <div class="preview-result" id="preview-result">
                <div class="preview-total">Total: $0.00</div>
                <div class="preview-breakdown">Selecciona una estrategia para ver el cálculo</div>
            </div>
        </div>

        {{-- Navigation Buttons --}}
        <div class="d-flex justify-content-between mt-4 mb-5">
            <a href="{{ route('admin.products.wizard.step', ['product' => $product, 'step' => 4]) }}"
                class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>
                {{ __('m_tours.tour.wizard.previous') }}
            </a>
            <button type="submit" class="btn btn-success btn-lg" id="submit-btn">
                {{ __('m_tours.tour.wizard.save_and_continue') }}
                <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </div>
    </form>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    console.log('Pricing wizard loaded');

    const categories = @json($categories ?? []);
    let ruleCounters = {
        flat_rate: 0,
        per_category: 0,
        tiered: 0,
        tiered_per_category: 0
    };

    // ==========================================
    // STRATEGY SELECTION
    // ==========================================
    $('.strategy-card').on('click', function() {
        const strategy = $(this).data('strategy');
        
        // Update UI
        $('.strategy-card').removeClass('active');
        $(this).addClass('active');
        
        // Update hidden input
        $('#strategy_type').val(strategy);
        
        // Show corresponding configurator
        $('.configurator-container').addClass('hidden');
        $(`[data-configurator="${strategy}"]`).removeClass('hidden');
        
        // Update preview
        updatePreview();
    });

    // Initialize if there's an active strategy
    const activeStrategy = '{{ $activeStrategy->strategy_type ?? '' }}';
    if (activeStrategy) {
        $(`.strategy-card[data-strategy="${activeStrategy}"]`).click();
    }

    // ==========================================
    // RULE TEMPLATES
    // ==========================================
    function getFlatRateRuleTemplate(index) {
        return `
            <div class="rule-row flat-rate-grid" data-rule-index="${index}">
                <div class="form-group-inline">
                    <label>Mín. Pasajeros</label>
                    <input type="number" name="rules[${index}][min_passengers]" min="1" class="form-control" required>
                </div>
                <div class="form-group-inline">
                    <label>Máx. Pasajeros</label>
                    <input type="number" name="rules[${index}][max_passengers]" min="1" class="form-control" required>
                </div>
                <div class="form-group-inline">
                    <label>Precio Total (USD)</label>
                    <input type="number" name="rules[${index}][price]" step="0.01" min="0" class="form-control" required>
                </div>
                <div>
                    <button type="button" class="btn-remove-rule" onclick="$(this).closest('.rule-row').remove(); updatePreview();">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
    }

    function getPerCategoryRuleTemplate(index) {
        let categoryOptions = categories.map(cat => {
            // cat.name is a JSON string, parse it and get the current locale
            let name = cat.slug || 'Unknown';
            try {
                if (cat.name) {
                    const nameObj = typeof cat.name === 'string' ? JSON.parse(cat.name) : cat.name;
                    name = nameObj['{{ app()->getLocale() }}'] || nameObj['es'] || cat.slug;
                }
            } catch (e) {
                console.error('Error parsing category name:', e);
            }
            return `<option value="${cat.category_id}">${name}</option>`;
        }).join('');

        return `
            <div class="rule-row category-grid" data-rule-index="${index}">
                <div class="form-group-inline">
                    <label>Categoría</label>
                    <select name="rules[${index}][category_id]" class="form-control" required>
                        <option value="">Selecciona...</option>
                        ${categoryOptions}
                    </select>
                </div>
                <div class="form-group-inline">
                    <label>Precio (USD)</label>
                    <input type="number" name="rules[${index}][price]" step="0.01" min="0" class="form-control" required>
                </div>
                <div>
                    <button type="button" class="btn-remove-rule" onclick="$(this).closest('.rule-row').remove(); updatePreview();">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
    }

    function getTieredRuleTemplate(index) {
        return `
            <div class="rule-row tiered-grid" data-rule-index="${index}">
                <div class="form-group-inline">
                    <label>Mín. Pasajeros</label>
                    <input type="number" name="rules[${index}][min_passengers]" min="1" class="form-control" required>
                </div>
                <div class="form-group-inline">
                    <label>Máx. Pasajeros</label>
                    <input type="number" name="rules[${index}][max_passengers]" min="1" class="form-control" required>
                </div>
                <div class="form-group-inline">
                    <label>Precio/Persona (USD)</label>
                    <input type="number" name="rules[${index}][price]" step="0.01" min="0" class="form-control" required>
                </div>
                <div class="form-group-inline">
                    <label>Etiqueta</label>
                    <input type="text" name="rules[${index}][label]" class="form-control" placeholder="Ej: Grupo Pequeño">
                </div>
                <div>
                    <button type="button" class="btn-remove-rule" onclick="$(this).closest('.rule-row').remove(); updatePreview();">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
    }

    function getTieredCategoryRuleTemplate(index) {
        let categoryOptions = categories.map(cat => {
            // cat.name is a JSON string, parse it and get the current locale
            let name = cat.slug || 'Unknown';
            try {
                if (cat.name) {
                    const nameObj = typeof cat.name === 'string' ? JSON.parse(cat.name) : cat.name;
                    name = nameObj['{{ app()->getLocale() }}'] || nameObj['es'] || cat.slug;
                }
            } catch (e) {
                console.error('Error parsing category name:', e);
            }
            return `<option value="${cat.category_id}">${name}</option>`;
        }).join('');

        return `
            <div class="rule-row tiered-category-grid" data-rule-index="${index}">
                <div class="form-group-inline">
                    <label>Categoría</label>
                    <select name="rules[${index}][category_id]" class="form-control" required>
                        <option value="">Selecciona...</option>
                        ${categoryOptions}
                    </select>
                </div>
                <div class="form-group-inline">
                    <label>Mín. Pasajeros</label>
                    <input type="number" name="rules[${index}][min_passengers]" min="1" class="form-control" required>
                </div>
                <div class="form-group-inline">
                    <label>Máx. Pasajeros</label>
                    <input type="number" name="rules[${index}][max_passengers]" min="1" class="form-control" required>
                </div>
                <div class="form-group-inline">
                    <label>Precio/Persona (USD)</label>
                    <input type="number" name="rules[${index}][price]" step="0.01" min="0" class="form-control" required>
                </div>
                <div>
                    <button type="button" class="btn-remove-rule" onclick="$(this).closest('.rule-row').remove(); updatePreview();">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
    }

    // ==========================================
    // ADD RULE HANDLERS
    // ==========================================
    $('[data-add-rule]').on('click', function() {
        const strategy = $(this).data('add-rule');
        const index = ruleCounters[strategy]++;
        let template = '';

        switch(strategy) {
            case 'flat_rate':
                template = getFlatRateRuleTemplate(index);
                $('#flat-rate-rules').append(template);
                break;
            case 'per_category':
                template = getPerCategoryRuleTemplate(index);
                $('#per-category-rules').append(template);
                break;
            case 'tiered':
                template = getTieredRuleTemplate(index);
                $('#tiered-rules').append(template);
                break;
            case 'tiered_per_category':
                template = getTieredCategoryRuleTemplate(index);
                $('#tiered-category-rules').append(template);
                break;
        }

        updatePreview();
    });

    // ==========================================
    // PREVIEW UPDATE
    // ==========================================
    function updatePreview() {
        // TODO: Implement real-time preview calculation
        const strategy = $('#strategy_type').val();
        const totalPassengers = parseInt($('#preview-total-passengers').val()) || 0;
        
        if (!strategy) {
            $('#preview-result').html(`
                <div class="preview-total">Total: $0.00</div>
                <div class="preview-breakdown">Selecciona una estrategia para ver el cálculo</div>
            `);
            return;
        }

        $('#preview-result').html(`
            <div class="preview-total">Total: $0.00</div>
            <div class="preview-breakdown">Estrategia: ${strategy} | ${totalPassengers} pasajeros</div>
        `);
    }

    // Update preview on input change
    $('#preview-total-passengers, #preview-adults, #preview-kids').on('input', updatePreview);
    $(document).on('input', 'input[type="number"]', updatePreview);

    // ==========================================
    // FORM VALIDATION
    // ==========================================
    $('#pricing-form').on('submit', function(e) {
        const strategy = $('#strategy_type').val();
        
        if (!strategy) {
            e.preventDefault();
            alert('Por favor selecciona una estrategia de pricing');
            return false;
        }

        // Additional validation per strategy
        // TODO: Add specific validation rules
    });
});
</script>
@endpush
