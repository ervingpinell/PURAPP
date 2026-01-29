{{-- resources/views/admin/tours/wizard/partials/leave-warning.blade.php --}}

@php
    // ID del formulario que queremos vigilar
    $formId = $formId ?? 'wizard-form';

    // Mensaje de advertencia al salir con cambios sin guardar
    $warningMessage = $warningMessage
        ?? __('m_tours.product.wizard.leave_warning')
        ?? 'Tienes cambios sin guardar en este product. ¿Seguro que quieres salir?';
@endphp

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById(@json($formId));
    if (!form) return;

    let isDirty = false;
    let isSubmitting = false;
    const leaveWarning = @json($warningMessage);

    // Marcar el formulario como "sucio" cuando algo cambie
    const inputs = form.querySelectorAll('input, textarea, select');

    inputs.forEach(function (el) {
        el.addEventListener('change', function () {
            isDirty = true;
        });
        el.addEventListener('input', function () {
            isDirty = true;
        });
    });

    // Al enviar el form ya no queremos warnings
    form.addEventListener('submit', function () {
        isSubmitting = true;
        isDirty = false;
    });

    // ==========
    // 1) beforeunload nativo (cerrar pestaña / recargar)
    // ==========
    window.addEventListener('beforeunload', function (e) {
        if (!isDirty || isSubmitting) return;

        // Los navegadores modernos ignoran el texto custom,
        // pero hay que setearlo igual.
        e.preventDefault();
        e.returnValue = leaveWarning;
        return leaveWarning;
    });

    // ==========
    // 2) SweetAlert para navegación dentro de la página
    //    (clicks en <a> que cambian de URL)
    // ==========
    function handlePotentialLeave(event, href, target) {
        if (!isDirty || isSubmitting) {
            // No hay cambios, dejar continuar
            return;
        }

        // Abrir en nueva pestaña no rompe nada, no bloqueamos
        if (target === '_blank') {
            return;
        }

        // Si no hay URL real, salir
        if (!href || href === '#' || href.startsWith('javascript:')) {
            return;
        }

        // Si el link pide explícitamente NO mostrar warning
        const link = event.target.closest('a');
        if (link && link.dataset.leaveWarning === 'false') {
            return;
        }

        // Hay cambios sin guardar -> prevenimos y mostramos SweetAlert
        event.preventDefault();

        // Si no existe Swal, fallback sin confirmación extra
        if (typeof Swal === 'undefined') {
            isDirty = false;
            window.location.href = href;
            return;
        }

        Swal.fire({
            title: '{{ __('m_tours.common.warning') ?? 'Atención' }}',
            text:  leaveWarning,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{ __('m_tours.common.yes_continue') ?? 'Salir de todos modos' }}',
            cancelButtonText: '{{ __('m_tours.common.cancel') ?? 'Cancelar' }}',
            reverseButtons: true,
        }).then(function (result) {
            if (result.isConfirmed) {
                // Permitimos salir sin más warnings
                isDirty = false;
                window.location.href = href;
            }
        });
    }

    // Delegación global en el body para todos los <a>
    document.body.addEventListener('click', function (e) {
        const link = e.target.closest('a');
        if (!link) return;

        const href   = link.getAttribute('href');
        const target = link.getAttribute('target') || '';

        handlePotentialLeave(e, href, target);
    });
});
</script>
@endpush
