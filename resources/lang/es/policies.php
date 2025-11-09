<?php

return [

    // =========================================================
    // [00] GENÉRICO
    // =========================================================
    'page_title'  => 'Políticas',
    'no_policies' => 'No hay políticas disponibles por el momento.',
    'no_sections' => 'No hay secciones disponibles por el momento.',
    'propagate_to_all_langs' => 'Propagar este cambio a todos los idiomas (EN, FR, DE, PT)',
    'propagate_hint'         => 'Se traducirá automáticamente desde el texto actual y se sobrescribirán las traducciones existentes en esos idiomas.',
    'update_base_es'         => 'Actualizar también la base (ES)',
    'update_base_hint'       => 'Sobrescribe el nombre y el contenido de la política en la tabla base (español). Úsalo solo si quieres que el texto original también cambie.',


    // =========================================================
    // [01] CHECKOUT
    // =========================================================
    'checkout' => [
        'card_title'  => 'Tu pedido',
        'details'     => 'Detalle',
        'must_accept' => 'Debes leer y aceptar todas las políticas para continuar con el pago.',
        'accept_label_html' =>
            'He leído y acepto los <strong>Términos y Condiciones</strong>, la <strong>Política de Privacidad</strong> y todas las <strong>Políticas de Cancelación, Devoluciones y Garantía</strong>.',
        'back'       => 'Volver',
        'pay'        => 'Procesar pago',
        'order_full' => 'Detalle completo del pedido',

        'version' => [
            'terms'   => 'v1',
            'privacy' => 'v1',
        ],

        'titles' => [
            'terms'        => 'Términos y Condiciones',
            'privacy'      => 'Política de Privacidad',
            'cancellation' => 'Política de Cancelación',
            'refunds'      => 'Política de Devoluciones',
            'warranty'     => 'Política de Garantía',
            'payments'     => 'Métodos de Pago',
        ],

        'bodies' => [
    'terms_html' => <<<HTML
    <p>Estos términos regulan la compra de tours y servicios ofrecidos por Green Vacations CR.</p>
    <ul>
      <li><strong>Objeto:</strong> La compra aplica exclusivamente a los servicios listados para las fechas y horarios seleccionados.</li>
      <li><strong>Precios y cargos:</strong> Los precios se muestran en USD e incluyen impuestos cuando corresponda. Cualquier cargo adicional se informará antes del pago.</li>
      <li><strong>Capacidad y disponibilidad:</strong> Las reservas están sujetas a disponibilidad y a validaciones de capacidad.</li>
      <li><strong>Modificaciones:</strong> Los cambios de fecha/horario están sujetos a disponibilidad y podrían generar diferencias tarifarias.</li>
      <li><strong>Responsabilidad:</strong> La empresa presta los servicios conforme a la normativa costarricense aplicable.</li>
    </ul>
    HTML,

    'privacy_html' => <<<HTML
    <p>Tratamos datos personales conforme a la normativa aplicable. Recopilamos los datos necesarios para gestionar reservas, pagos y la comunicación con el cliente.</p>
    <ul>
      <li><strong>Uso de la información:</strong> Gestión de la compra, atención al cliente, notificaciones operativas y cumplimiento legal.</li>
      <li><strong>Compartición:</strong> <u>No vendemos ni comerciamos datos personales.</u></li>
      <li><strong>Derechos:</strong> Puede ejercer derechos de acceso, rectificación, oposición y supresión a través de nuestros canales de contacto.</li>
    </ul>
    HTML,

    'cancellation_html' => <<<HTML
    <p>El cliente puede solicitar la anulación de la compra antes del inicio del servicio según los siguientes plazos:</p>
    <ul>
      <li>Hasta 2 h antes: <strong>reembolso completo</strong>.</li>
      <li>Entre 2 h y 1 h antes: <strong>reembolso del 50%</strong>.</li>
      <li>Menos de 1 h: <strong>no reembolsable</strong>.</li>
    </ul>
    <p>Las devoluciones de dinero se realizan a la <strong>misma tarjeta</strong> utilizada en la compra. El tiempo de acreditación depende del banco emisor.</p>
    <p>Indique su <strong>número de pedido</strong> y <strong>nombre completo</strong> al solicitar la cancelación. Los plazos pueden variar por tour si así se indica en la ficha del producto.</p>
    HTML,

    'refunds_html' => <<<HTML
    <p>Cuando corresponda, el reembolso se realiza a la <strong>misma tarjeta</strong> con la que se efectuó la compra. Los tiempos dependen del emisor del medio de pago.</p>
    <p>Para gestionar devoluciones: info@greenvacationscr.com / (+506) 2479 1471.</p>
    HTML,

    'warranty_html' => <<<HTML
    <p>Aplica a servicios no prestados o prestados de forma sustancialmente distinta a la ofrecida. El cliente cuenta con <strong>7 días</strong> para reportar incidencias. La garantía aplica a servicios turísticos comercializados por Green Vacations CR.</p>
    HTML,

    'payments_html' => <<<HTML
    <p>El pago se realiza mediante Link de Pago por Alignet con tarjetas Visa/Mastercard/Amex habilitadas para compras en línea.</p>
    HTML,
],

                ],

    // =========================================================
    // [02] CAMPOS
    // =========================================================
    'fields' => [
        'title'       => 'Título',
        'description' => 'Descripción',
        'type'        => 'Tipo',
        'is_active'   => 'Activo',
    ],

    // =========================================================
    // [03] TIPOS
    // =========================================================
    'types' => [
        'cancellation' => 'Política de Cancelación',
        'refund'       => 'Política de Reembolso',
        'terms'        => 'Términos y Condiciones',
    ],

    // =========================================================
    // [04] MENSAJES
    // =========================================================
    'success' => [
        'created' => 'Política creada con éxito.',
        'updated' => 'Política actualizada con éxito.',
        'deleted' => 'Política eliminada con éxito.',
    ],

    'error' => [
        'create' => 'No se pudo crear la política.',
        'update' => 'No se pudo actualizar la política.',
        'delete' => 'No se pudo eliminar la política.',
    ],
];
