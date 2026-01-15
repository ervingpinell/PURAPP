@extends('adminlte::page')

@section('title', __('m_config.faq.trash_title'))

@section('content_header')
<h1><i class="fas fa-trash"></i> {{ __('m_config.faq.trash_title') }}</h1>
@stop

@section('content')
<a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary mb-3">
    <i class="fas fa-arrow-left"></i> {{ __('m_config.buttons.back') }}
</a>

@if($faqs->isEmpty())
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> {{ __('m_config.faq.trash_empty') }}
</div>
@else
<table class="table table-bordered">
    <thead class="table-dark">
        <tr>
            <th>{{ __('m_config.faq.question') }}</th>
            <th>{{ __('m_config.faq.answer') }}</th>
            <th>{{ __('m_config.faq.deleted_by') }}</th>
            <th>{{ __('m_config.faq.deleted_at') }}</th>
            <th style="width: 120px;">{{ __('m_config.faq.actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($faqs as $faq)
        <tr>
            <td>{{ $faq->question }}</td>
            <td>
                <div class="faq-answer position-relative" style="max-height: 3.5em; overflow: hidden;" data-expanded="false">
                    <div class="answer-content">{{ strip_tags($faq->answer) }}</div>
                    <div class="fade-overlay position-absolute bottom-0 start-0 w-100"
                        style="height: 1.5em; background: linear-gradient(to bottom, transparent, white);"></div>
                </div>
                <button class="btn btn-link p-0 mt-1 toggle-answer d-none" style="font-size: 0.85em;">
                    {{ __('m_config.faq.read_more') }}
                </button>
            </td>
            <td>{{ optional($faq->deletedBy)->name ?? 'N/A' }}</td>
            <td>{{ $faq->deleted_at ? $faq->deleted_at->format('d/m/Y H:i') : 'N/A' }}</td>
            <td>
                @can('restore-faqs')
                <form action="{{ route('admin.faqs.restore', $faq->faq_id) }}" method="POST"
                    class="d-inline js-confirm-restore">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-success" title="{{ __('m_config.faq.restore') }}" data-bs-toggle="tooltip">
                        <i class="fas fa-undo"></i>
                    </button>
                </form>
                @endcan

                @can('force-delete-faqs')
                <form action="{{ route('admin.faqs.forceDelete', $faq->faq_id) }}" method="POST"
                    class="d-inline js-confirm-force-delete">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" title="{{ __('m_config.faq.force_delete') }}" data-bs-toggle="tooltip">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
                @endcan
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@stop

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Éxito / Error --}}
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: @json(__(session('success'))),
        showConfirmButton: false,
        timer: 2000
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: @json(__('m_config.faq.error_title')),
        text: @json(__(session('error')))
    });
</script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.bootstrap && bootstrap.Tooltip) {
            [...document.querySelectorAll('[data-bs-toggle="tooltip"]')].forEach(el => new bootstrap.Tooltip(el));
        }

        // Confirmación RESTAURAR
        document.querySelectorAll('.js-confirm-restore').forEach(form => {
            form.addEventListener('submit', (ev) => {
                ev.preventDefault();
                Swal.fire({
                    title: @json(__('m_config.faq.confirm_restore')),
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: @json(__('m_config.faq.restore')),
                    cancelButtonText: @json(__('m_config.faq.cancel')),
                }).then((res) => {
                    if (res.isConfirmed) form.submit();
                });
            });
        });

        // Confirmación ELIMINAR PERMANENTEMENTE
        document.querySelectorAll('.js-confirm-force-delete').forEach(form => {
            form.addEventListener('submit', (ev) => {
                ev.preventDefault();
                Swal.fire({
                    title: @json(__('m_config.faq.confirm_force_delete')),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: @json(__('m_config.faq.force_delete')),
                    cancelButtonText: @json(__('m_config.faq.cancel')),
                }).then((res) => {
                    if (res.isConfirmed) form.submit();
                });
            });
        });

        // Leer más / Leer menos
        document.querySelectorAll('.faq-answer').forEach(container => {
            const content = container.querySelector('.answer-content');
            const toggleBtn = container.parentElement.querySelector('.toggle-answer');
            const fadeOverlay = container.querySelector('.fade-overlay');
            if (!content || !toggleBtn) return;

            const contentHeight = content.scrollHeight;
            const maxHeight = parseFloat(getComputedStyle(container).maxHeight);
            if (contentHeight > maxHeight + 5) toggleBtn.classList.remove('d-none');

            toggleBtn.addEventListener('click', function() {
                const isExpanded = container.getAttribute('data-expanded') === 'true';
                container.style.maxHeight = isExpanded ? '3.5em' : 'none';
                if (fadeOverlay) fadeOverlay.style.display = isExpanded ? '' : 'none';
                container.setAttribute('data-expanded', (!isExpanded).toString());
                this.textContent = isExpanded ? @json(__('m_config.faq.read_more')) : @json(__('m_config.faq.read_less'));
            });
        });
    });
</script>
@endpush