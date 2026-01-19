@extends('adminlte::page')

@section('title', __('customer_categories.ui.page_title_index'))

@section('content_header')
    <h1>{{ __('customer_categories.ui.header_index') }}</h1>
@stop

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    {{ session('error') }}
</div>
@endif

{{-- Tabs --}}
<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('admin.customer_categories.index') }}">
            {{ __('customer_categories.states.active') }}
        </a>
    </li>
    @can('restore-customer-categories')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.customer_categories.trash') }}">
            {{ __('customer_categories.ui.trash_title') }}
            @if(isset($trashedCount) && $trashedCount > 0)
            <span class="badge badge-danger ml-1">{{ $trashedCount }}</span>
            @endif
        </a>
    </li>
    @endcan
</ul>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ __('customer_categories.ui.list_title') }}</h3>
        <div class="card-tools">
            <button class="btn btn-primary btn-sm" onclick="$('#create-row').toggle(); $('#new-name').focus();">
                <i class="fas fa-plus"></i> {{ __('customer_categories.buttons.new_category') }}
            </button>
        </div>
    </div>

    <div class="card-body p-0 table-responsive">
        <table class="table table-striped table-hover text-nowrap">
            <thead>
                <tr>
                    <th style="width: 10px"></th>
                    <th>{{ __('customer_categories.table.name') }} <small class="text-muted">({{ strtoupper(app()->getLocale()) }})</small></th>
                    <th>Slug</th>
                    <th>{{ __('customer_categories.table.range') }}</th>
                    <th class="text-center" style="width: 100px">{{ __('customer_categories.table.active') }}</th>
                    <th class="text-center" style="width: 180px">{{ __('customer_categories.table.actions') }}</th>
                </tr>
            </thead>
            <tbody id="categories-table-body">
                {{-- Create Row --}}
                <tr id="create-row" style="display:none;" class="bg-light">
                    <td class="align-middle text-center"><i class="fas fa-plus text-muted"></i></td>
                    <td>
                        <input type="text" id="new-name" class="form-control" placeholder="Nombre (Ej: Infant)">
                    </td>
                    <td>
                        <input type="text" id="new-slug" class="form-control" placeholder="slug-auto">
                    </td>
                    <td>
                        <div class="d-flex align-items-center" style="gap: 5px;">
                            <input type="number" id="new-age-from" class="form-control form-control-sm text-center" placeholder="Min" style="width: 70px;">
                            <span>-</span>
                            <input type="number" id="new-age-to" class="form-control form-control-sm text-center" placeholder="Max" style="width: 70px;">
                        </div>
                    </td>
                    <td class="align-middle text-center">
                        <span class="badge badge-secondary">Inactivo</span>
                    </td>
                    <td class="align-middle text-center">
                        <button class="btn btn-success btn-sm" onclick="saveNewCategory()">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </td>
                </tr>

                {{-- Existing Rows --}}
                @forelse($categories as $category)
                <tr data-id="{{ $category->category_id }}" id="row-{{ $category->category_id }}">
                    <td class="align-middle text-center">
                        <i class="fas fa-bars text-muted sortable-handle" style="cursor: move; padding: 15px; display: block;"></i>
                    </td>
                    <td class="align-middle">
                        <input type="text" 
                               class="form-control form-control-sm" 
                               id="name-{{ $category->category_id }}"
                               value="{{ $category->getTranslatedName() }}">
                    </td>
                    <td class="align-middle">
                        <input type="text" 
                               class="form-control form-control-sm" 
                               id="slug-{{ $category->category_id }}"
                               value="{{ $category->slug }}">
                    </td>
                    <td class="align-middle">
                        <div class="d-flex align-items-center" style="gap: 5px;">
                            <input type="number" 
                                   class="form-control form-control-sm text-center" 
                                   id="age_from-{{ $category->category_id }}"
                                   value="{{ $category->age_from }}" 
                                   min="0"
                                   style="width: 70px;">
                            <span>-</span>
                            <input type="number" 
                                   class="form-control form-control-sm text-center" 
                                   id="age_to-{{ $category->category_id }}"
                                   value="{{ $category->age_to }}" 
                                   placeholder="∞" 
                                   min="0"
                                   style="width: 70px;">
                        </div>
                    </td>
                    <td class="align-middle text-center">
                        <span class="badge {{ $category->is_active ? 'badge-success' : 'badge-secondary' }}">
                            {{ $category->is_active ? __('customer_categories.states.active') : __('customer_categories.states.inactive') }}
                        </span>
                    </td>
                    <td class="align-middle text-center">
                        <div class="btn-group">
                            {{-- Save Button --}}
                            <button type="button" class="btn btn-sm btn-success" onclick="saveRow({{ $category->category_id }})" title="{{ __('customer_categories.buttons.save') }}">
                                <i class="fas fa-save"></i>
                            </button>

                            {{-- Toggle Button --}}
                             @can('publish-customer-categories')
                            <form action="{{ route('admin.customer_categories.toggle', $category) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit"
                                    class="btn btn-sm {{ $category->is_active ? 'btn-warning' : 'btn-secondary' }}"
                                    title="{{ $category->is_active ? __('customer_categories.states.deactivate') : __('customer_categories.states.activate') }}">
                                    <i class="fas fa-toggle-{{ $category->is_active ? 'on' : 'off' }}"></i>
                                </button>
                            </form>
                            @endcan

                            {{-- Translations Button --}}
                            <button type="button" class="btn btn-sm btn-info" onclick="openTranslations({{ $category->category_id }})" title="{{ __('customer_categories.form.translations.title') }}">
                                <i class="fas fa-language"></i>
                            </button>

                             @can('soft-delete-customer-categories')
                            <button type="button"
                                class="btn btn-sm btn-danger"
                                data-toggle="modal"
                                data-target="#deleteModal{{ $category->category_id }}"
                                title="{{ __('customer_categories.buttons.delete') }}">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endcan
                        </div>

                         {{-- Modal de Confirmación --}}
                         <div class="modal fade" id="deleteModal{{ $category->category_id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <h5 class="modal-title">{{ __('customer_categories.dialogs.delete.title') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body text-left white-space-normal">
                                        <p>{!! __('customer_categories.dialogs.delete.text', ['name' => '<strong>'.e($category->getTranslatedName()).'</strong>']) !!}</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('customer_categories.buttons.cancel') }}</button>
                                        <form action="{{ route('admin.customer_categories.destroy', $category) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger">{{ __('customer_categories.buttons.delete') }}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center p-4">Sin datos</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card card-info">
    <div class="card-header"><h3 class="card-title"><i class="fas fa-info-circle"></i> {{ __('customer_categories.rules.title') }}</h3></div>
    <div class="card-body">
        <ul class="mb-0">
            <li>{{ __('customer_categories.rules.no_overlap') }}</li>
            <li>{{ __('customer_categories.rules.drag_to_order') }}</li>
            <li>{{ __('customer_categories.rules.manual_save') }}</li>
        </ul>
    </div>
</div>

{{-- Translations Modal --}}
<div class="modal fade" id="translationsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-language"></i> Editar Traducciones</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="trans-category-id">
                <ul class="nav nav-tabs" id="transTabs" role="tablist">
                    @foreach(supported_locales() as $locale)
                    <li class="nav-item">
                        <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $locale }}" data-toggle="tab" href="#pane-{{ $locale }}" role="tab">{{ strtoupper($locale) }}</a>
                    </li>
                    @endforeach
                </ul>
                <div class="tab-content pt-3" id="transTabsContent">
                    @foreach(supported_locales() as $locale)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="pane-{{ $locale }}" role="tabpanel">
                        <div class="form-group">
                            <label>Nombre ({{ strtoupper($locale) }})</label>
                            <input type="text" class="form-control trans-input" data-locale="{{ $locale }}" id="trans-input-{{ $locale }}">
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="saveTranslations()">Guardar Traducciones</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    function saveNewCategory() {
        let name = $('#new-name').val();
        let slug = $('#new-slug').val();
        let ageFrom = $('#new-age-from').val();
        let ageTo = $('#new-age-to').val();

        if(!name) { toastr.error('{{ __('customer_categories.validation.required_name') }}'); return; }

        $.ajax({
            url: '{{ route("admin.customer_categories.store") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                initial_name: name,
                slug: slug,
                age_from: ageFrom,
                age_to: ageTo,
                is_active: 0,
                auto_translate: 1 // Default to auto-translate for new items
            },
            success: function(res) {
                toastr.success('{{ __('customer_categories.messages.created') }}');
                window.location.reload();
            },
            error: function(xhr) {
                let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : '{{ __('customer_categories.messages.error_create') }}';
                toastr.error(msg);
            }
        });
    }

    function saveRow(id) {
        let name = $('#name-' + id).val();
        let slug = $('#slug-' + id).val();
        let ageFrom = $('#age_from-' + id).val();
        let ageTo = $('#age_to-' + id).val();
        let btn = $('#row-' + id).find('.btn-info');
        
        // UI Feedback
        let originalIcon = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin"></i>');
        btn.prop('disabled', true);

        $.ajax({
            url: '/admin/customer_categories/' + id + '/quick-update',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                name: name,
                slug: slug,
                age_from: ageFrom,
                age_to: ageTo,
                smart_translate: 1 // Trigger smart detection logic
            },
            success: function(res) {
                toastr.success(res.message);
                btn.html('<i class="fas fa-check"></i>');
                setTimeout(() => {
                    btn.html('<i class="fas fa-save"></i>');
                    btn.prop('disabled', false);
                }, 2000);
                
                // Update Name input if it changed (due to translation correction)
                if(res.name) {
                    $('#name-' + id).val(res.name);
                }
            },
            error: function(xhr) {
                toastr.error('{{ __('customer_categories.messages.error_save') }}');
                btn.html('<i class="fas fa-times"></i>');
                setTimeout(() => {
                    btn.html('<i class="fas fa-save"></i>');
                    btn.prop('disabled', false);
                }, 2000);
            }
        });
    }

    function openTranslations(id) {
        $('#trans-category-id').val(id);
        // Load translations
        $.get('/admin/customer_categories/' + id + '/translations', function(data) {
            $('.trans-input').val(''); // Clear
            $.each(data, function(loc, val) {
                $('#trans-input-' + loc).val(val);
            });
            $('#translationsModal').modal('show');
        });
    }

    function saveTranslations() {
        let id = $('#trans-category-id').val();
        let translations = {};
        $('.trans-input').each(function() {
            let loc = $(this).data('locale');
            let val = $(this).val();
            translations[loc] = val;
        });

        $.ajax({
            url: '/admin/customer_categories/' + id + '/update-translations',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                translations: translations
            },
            success: function(res) {
                toastr.success('{{ __('customer_categories.messages.translations_saved') }}');
                $('#translationsModal').modal('hide');
                // Update the main row name if current locale changed
                if(res.current_locale_name) {
                    $('#name-' + id).val(res.current_locale_name);
                }
            },
            error: function() {
                toastr.error('{{ __('customer_categories.messages.translations_error') }}');
            }
        });
    }

    $(document).ready(function() {
        // Auto slug creation
        $('#new-name').on('keyup', function() {
            let val = $(this).val();
            let slug = val.toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
            $('#new-slug').val(slug);
        });

        // Sortable
        let el = document.getElementById('categories-table-body');
        if(el) {
            Sortable.create(el, {
                handle: '.sortable-handle',
                animation: 150,
                filter: '#create-row, input', // Ignore create row and inputs
                preventOnFilter: false,       // Allow input focus
                onEnd: function (evt) {
                    let order = [];
                    $('#categories-table-body tr').each(function() {
                        let id = $(this).data('id');
                        if(id) order.push(id);
                    });
                    $.ajax({
                        url: '{{ route("admin.customer_categories.reorder") }}',
                        method: 'POST',
                        data: { _token: '{{ csrf_token() }}', order: order },
                        success: function() { toastr.success('Orden actualizado'); }
                    });
                }
            });
        }
    });
</script>
@stop