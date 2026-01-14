<?php

/**
 * Alignet Response Codes Configuration
 *
 * Comprehensive mapping of all Alignet VPOS2 return codes with:
 * - Classification: authorized, denied, rejected, cancelled, error
 * - Spanish and English descriptions
 *
 * @see https://docs.alignet.com/es/modal/Working-version/consideraciones-y-recomendaciones
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Response Code Classifications
    |--------------------------------------------------------------------------
    |
    | authorized: Transaction approved successfully
    | denied: Bank/issuer denied - customer should use different card
    | rejected: System/security rejected - may retry or contact support
    | cancelled: User cancelled the transaction
    | error: System/communication error
    |
    */

    'codes' => [
        // ========================================
        // AUTHORIZED CODES (Transaction Approved)
        // ========================================
        '00' => [
            'classification' => 'authorized',
            'es' => 'Transacción autorizada',
            'en' => 'Successful approval/completion',
        ],
        '09' => [
            'classification' => 'authorized',
            'es' => 'Transacción autorizada',
            'en' => 'Successful approval/completion',
        ],
        '10' => [
            'classification' => 'authorized',
            'es' => 'Aprobación parcial',
            'en' => 'Partial approval',
        ],
        '11' => [
            'classification' => 'authorized',
            'es' => 'Aprobación VIP',
            'en' => 'VIP approval',
        ],

        // ========================================
        // DENIED CODES (Bank/Issuer Denied)
        // ========================================
        '01' => [
            'classification' => 'denied',
            'es' => 'Consultar con el emisor de la tarjeta',
            'en' => 'Refer to card issuer',
        ],
        '02' => [
            'classification' => 'denied',
            'es' => 'Consultar con el emisor, condición especial',
            'en' => 'Refer to card issuer, special condition',
        ],
        '03' => [
            'classification' => 'denied',
            'es' => 'Comercio/proveedor inválido',
            'en' => 'Invalid merchant/service provider',
        ],
        '04' => [
            'classification' => 'denied',
            'es' => 'Retener tarjeta',
            'en' => 'Pickup card',
        ],
        '05' => [
            'classification' => 'denied',
            'es' => 'Transacción denegada',
            'en' => 'Do not honor',
        ],
        '06' => [
            'classification' => 'error',
            'es' => 'Error',
            'en' => 'Error',
        ],
        '07' => [
            'classification' => 'denied',
            'es' => 'Retener tarjeta, condición especial',
            'en' => 'Pick up card, special condition',
        ],
        '12' => [
            'classification' => 'rejected',
            'es' => 'Transacción inválida',
            'en' => 'Invalid transaction',
        ],
        '13' => [
            'classification' => 'rejected',
            'es' => 'Monto inválido',
            'en' => 'Invalid amount',
        ],
        '14' => [
            'classification' => 'denied',
            'es' => 'Número de cuenta inválido',
            'en' => 'Invalid account number (no such number)',
        ],
        '15' => [
            'classification' => 'denied',
            'es' => 'Emisor no existe',
            'en' => 'No such issuer',
        ],
        '19' => [
            'classification' => 'error',
            'es' => 'Reintentar la transacción',
            'en' => 'Re-enter transaction',
        ],
        '21' => [
            'classification' => 'error',
            'es' => 'Sin acción tomada',
            'en' => 'No action taken',
        ],
        '25' => [
            'classification' => 'error',
            'es' => 'No se pudo localizar el registro',
            'en' => 'Unable to locate record in file',
        ],
        '28' => [
            'classification' => 'error',
            'es' => 'Archivo temporalmente no disponible',
            'en' => 'File is temporarily unavailable',
        ],
        '30' => [
            'classification' => 'rejected',
            'es' => 'Error en formato',
            'en' => 'Format error',
        ],
        '38' => [
            'classification' => 'denied',
            'es' => 'Excede ingresos de PIN',
            'en' => 'Allowable number of PIN tries exceeded',
        ],
        '41' => [
            'classification' => 'denied',
            'es' => 'Tarjeta perdida',
            'en' => 'Pickup card (lost card)',
        ],
        '43' => [
            'classification' => 'denied',
            'es' => 'Tarjeta robada',
            'en' => 'Pickup card (stolen card)',
        ],
        '45' => [
            'classification' => 'rejected',
            'es' => 'No opera en cuotas',
            'en' => 'Installments not allowed',
        ],
        '46' => [
            'classification' => 'denied',
            'es' => 'Tarjeta no vigente',
            'en' => 'Card not valid',
        ],
        '47' => [
            'classification' => 'rejected',
            'es' => 'PIN requerido',
            'en' => 'PIN required',
        ],
        '48' => [
            'classification' => 'rejected',
            'es' => 'Excede máximo de cuotas',
            'en' => 'Maximum installments exceeded',
        ],
        '49' => [
            'classification' => 'rejected',
            'es' => 'Error en fecha de vencimiento',
            'en' => 'Expiry date error',
        ],
        '51' => [
            'classification' => 'denied',
            'es' => 'Fondos insuficientes',
            'en' => 'Not sufficient funds',
        ],
        '52' => [
            'classification' => 'denied',
            'es' => 'Sin cuenta corriente',
            'en' => 'No checking account',
        ],
        '53' => [
            'classification' => 'denied',
            'es' => 'Sin cuenta de ahorros',
            'en' => 'No saving account',
        ],
        '54' => [
            'classification' => 'denied',
            'es' => 'Tarjeta expirada',
            'en' => 'Expired card',
        ],
        '55' => [
            'classification' => 'denied',
            'es' => 'PIN incorrecto',
            'en' => 'Incorrect PIN',
        ],
        '56' => [
            'classification' => 'denied',
            'es' => 'Tarjeta no habilitada',
            'en' => 'Card not enabled',
        ],
        '57' => [
            'classification' => 'denied',
            'es' => 'Transacción no permitida para el titular',
            'en' => 'Transaction not permitted to cardholder',
        ],
        '58' => [
            'classification' => 'rejected',
            'es' => 'Transacción no permitida en terminal',
            'en' => 'Transaction not allowed at terminal',
        ],
        '61' => [
            'classification' => 'denied',
            'es' => 'Excede límite de retiro',
            'en' => 'Exceeds withdrawal amount limit',
        ],
        '62' => [
            'classification' => 'denied',
            'es' => 'Tarjeta restringida',
            'en' => 'Restricted card',
        ],
        '63' => [
            'classification' => 'denied',
            'es' => 'Violación de seguridad',
            'en' => 'Security violation',
        ],
        '65' => [
            'classification' => 'denied',
            'es' => 'Excede límite de actividad',
            'en' => 'Activity count limit exceeded',
        ],
        '75' => [
            'classification' => 'denied',
            'es' => 'Intentos de PIN excedidos',
            'en' => 'Allowable number of PIN-entry tries exceeded',
        ],
        '76' => [
            'classification' => 'error',
            'es' => 'No se pudo localizar mensaje previo',
            'en' => 'Unable to locate previous message',
        ],
        '77' => [
            'classification' => 'error',
            'es' => 'Datos inconsistentes con mensaje original',
            'en' => 'Data inconsistent with original message',
        ],
        '80' => [
            'classification' => 'rejected',
            'es' => 'Fecha inválida',
            'en' => 'Invalid date',
        ],
        '81' => [
            'classification' => 'rejected',
            'es' => 'Error criptográfico PIN/CVV',
            'en' => 'PIN or CVV cryptographic error',
        ],
        '82' => [
            'classification' => 'denied',
            'es' => 'CVV incorrecto',
            'en' => 'Incorrect CVV',
        ],
        '83' => [
            'classification' => 'rejected',
            'es' => 'No se pudo verificar PIN',
            'en' => 'Unable to verify PIN',
        ],
        '85' => [
            'classification' => 'authorized',
            'es' => 'Verificación exitosa',
            'en' => 'No reason to decline verification',
        ],
        '89' => [
            'classification' => 'denied',
            'es' => 'Tarjeta inválida',
            'en' => 'Invalid card',
        ],
        '91' => [
            'classification' => 'error',
            'es' => 'Emisor o switch no disponible',
            'en' => 'Issuer or switch inoperative',
        ],
        '92' => [
            'classification' => 'error',
            'es' => 'Destino no encontrado para ruteo',
            'en' => 'Destination cannot be found for routing',
        ],
        '93' => [
            'classification' => 'rejected',
            'es' => 'Transacción no permitida, violación de ley',
            'en' => 'Transaction cannot be completed; violation of law',
        ],
        '94' => [
            'classification' => 'error',
            'es' => 'Número de secuencia duplicado',
            'en' => 'Duplicate sequence number',
        ],
        '95' => [
            'classification' => 'error',
            'es' => 'Re-transmitiendo',
            'en' => 'Re-transmitting',
        ],
        '96' => [
            'classification' => 'error',
            'es' => 'Error del sistema',
            'en' => 'System malfunction',
        ],

        // ========================================
        // 1000 SERIES - Commerce/Data Errors
        // ========================================
        '1001' => [
            'classification' => 'rejected',
            'es' => 'Datos inválidos enviados por comercio',
            'en' => 'Invalid Data sent by Commerce',
        ],
        '1002' => [
            'classification' => 'rejected',
            'es' => 'Moneda inválida para comercio',
            'en' => 'Invalid Currency for Commerce',
        ],
        '1003' => [
            'classification' => 'rejected',
            'es' => 'Código de comercio inválido',
            'en' => 'Invalid Commerce Code',
        ],
        '1004' => [
            'classification' => 'rejected',
            'es' => 'Número de orden duplicado',
            'en' => 'Duplicated order number',
        ],
        '1101' => [
            'classification' => 'error',
            'es' => 'Problema de comunicación',
            'en' => 'Communication Problem',
        ],
        '1102' => [
            'classification' => 'error',
            'es' => 'Problema de procesamiento',
            'en' => 'Processing Problem',
        ],
        '1103' => [
            'classification' => 'error',
            'es' => 'Problema de comunicación',
            'en' => 'Communication Problem',
        ],
        '1104' => [
            'classification' => 'rejected',
            'es' => 'Datos inválidos',
            'en' => 'Invalid Data',
        ],
        '1105' => [
            'classification' => 'rejected',
            'es' => 'Control de flujo no válido',
            'en' => 'Flow Control Not Valid',
        ],
        '1107' => [
            'classification' => 'rejected',
            'es' => 'VCI inválido',
            'en' => 'Invalid VCI',
        ],
        '1108' => [
            'classification' => 'rejected',
            'es' => 'URL de retorno de comercio inválida',
            'en' => 'Invalid return commerce URL',
        ],
        '1114' => [
            'classification' => 'rejected',
            'es' => 'Número de tarjeta no pertenece a marca seleccionada',
            'en' => 'Card Number does not belong to selected brand',
        ],
        '1115' => [
            'classification' => 'rejected',
            'es' => 'Reversión rechazada',
            'en' => 'Reverse rejected',
        ],
        '1117' => [
            'classification' => 'authorized',
            'es' => 'Reversión automática aprobada',
            'en' => 'Automatic reverse approved',
        ],

        // ========================================
        // 2000 SERIES - System/Configuration/Rules
        // ========================================
        '2003' => [
            'classification' => 'error',
            'es' => 'Código de acción no existe',
            'en' => 'Action code doesn\'t exist',
        ],
        '2100' => [
            'classification' => 'error',
            'es' => 'Clase implementadora no existe',
            'en' => 'Implementor Class not existent',
        ],
        '2101' => [
            'classification' => 'error',
            'es' => 'Plan de cuotas por defecto inexistente',
            'en' => 'Default Plan Quotas inexistent',
        ],
        '2102' => [
            'classification' => 'error',
            'es' => 'BIN existente pero sin plan de cuotas asignado',
            'en' => 'Existing BIN but no Plan Quotas assigned',
        ],
        '2200' => [
            'classification' => 'rejected',
            'es' => 'Adquirente necesita más datos del Plug-In',
            'en' => 'Acquirer needs more data from Plug-In',
        ],
        '2201' => [
            'classification' => 'rejected',
            'es' => 'Plan de cuotas no enviado por comercio',
            'en' => 'Plan Quotas not sent by Commerce',
        ],
        '2202' => [
            'classification' => 'rejected',
            'es' => 'Número de orden duplicado',
            'en' => 'Duplicated order number',
        ],
        '2300' => [
            'classification' => 'cancelled',
            'es' => 'Usuario canceló en paso 1',
            'en' => 'User Cancelled in PASS 1',
        ],
        '2301' => [
            'classification' => 'cancelled',
            'es' => 'Usuario canceló en paso 2',
            'en' => 'User Cancelled in PASS 2',
        ],
        '2302' => [
            'classification' => 'cancelled',
            'es' => 'Usuario canceló en paso 3',
            'en' => 'User Cancelled in PASS 3',
        ],
        '2303' => [
            'classification' => 'rejected',
            'es' => 'Código de operación excede 12 caracteres',
            'en' => 'CodigoOperacion Field must not be greater than 12 characters',
        ],
        '2304' => [
            'classification' => 'rejected',
            'es' => 'Comercio no existe',
            'en' => 'Commerce not existent',
        ],
        '2305' => [
            'classification' => 'rejected',
            'es' => 'Comercio no está activo',
            'en' => 'Commerce is not active',
        ],
        '2306' => [
            'classification' => 'rejected',
            'es' => 'Adquirente no está activo',
            'en' => 'Acquirer is not active',
        ],
        '2307' => [
            'classification' => 'rejected',
            'es' => 'Transacción ya fue procesada',
            'en' => 'Transaction has been processed',
        ],
        '2308' => [
            'classification' => 'rejected',
            'es' => 'Marca de tarjeta incorrecta',
            'en' => 'Card Brand is not correct',
        ],
        '2309' => [
            'classification' => 'rejected',
            'es' => 'Número de tarjeta incorrecto',
            'en' => 'Card Number is not correct',
        ],
        '2310' => [
            'classification' => 'rejected',
            'es' => 'Fecha de expiración incorrecta',
            'en' => 'Card Expiry Date is not correct',
        ],
        '2311' => [
            'classification' => 'rejected',
            'es' => 'Código de seguridad incorrecto',
            'en' => 'Card Security Code is not correct',
        ],
        '2312' => [
            'classification' => 'rejected',
            'es' => 'Número de tarjeta requerido',
            'en' => 'Card Number is not present and is required',
        ],
        '2313' => [
            'classification' => 'rejected',
            'es' => 'Comercio mal configurado',
            'en' => 'Commerce not well configured',
        ],
        '2314' => [
            'classification' => 'rejected',
            'es' => 'Adquirente mal configurado',
            'en' => 'Acquirer not well configured',
        ],
        '2315' => [
            'classification' => 'rejected',
            'es' => 'Rechazado por reglas de pre-autorización',
            'en' => 'Will not go to authorization due to Pre-Authorization Rules',
        ],
        '2316' => [
            'classification' => 'rejected',
            'es' => 'Rechazado por reglas post-autenticación',
            'en' => 'Authorized but Post-Authentication Rules refused it',
        ],
        '2317' => [
            'classification' => 'rejected',
            'es' => 'Reversado por reglas post-autenticación',
            'en' => 'Authorized but reversed by Post-Authentication Rules',
        ],
        '2318' => [
            'classification' => 'rejected',
            'es' => 'Número de operación excede 8 caracteres',
            'en' => 'PurchaseOperationNumber exceeds 8 characters',
        ],
        '2319' => [
            'classification' => 'rejected',
            'es' => 'Número de operación excede 9 caracteres',
            'en' => 'PurchaseOperationNumber exceeds 9 characters',
        ],
        '2320' => [
            'classification' => 'rejected',
            'es' => 'Comercio no autorizado para enviar PAN',
            'en' => 'Commerce not authorized to send PAN number',
        ],
        '2330' => [
            'classification' => 'rejected',
            'es' => 'Número de operación no numérico',
            'en' => 'PurchaseOperationNumber not numeric',
        ],
        '2400' => [
            'classification' => 'rejected',
            'es' => 'Rechazado por reglas pre-autorización',
            'en' => 'Transaction rejected due to pre-authorization rules',
        ],
        '2401' => [
            'classification' => 'rejected',
            'es' => 'Reglas de pre-autenticación no aprobadas',
            'en' => 'Pre Authentication rules not approved',
        ],
        '2402' => [
            'classification' => 'rejected',
            'es' => 'Reglas post-autenticación no aprobadas',
            'en' => 'Post Authentication rules not approved',
        ],
        '2403' => [
            'classification' => 'denied',
            'es' => 'Monto mensual acumulado máximo alcanzado',
            'en' => 'Maximum Monthly accumulated amount has been reached',
        ],
        '2404' => [
            'classification' => 'denied',
            'es' => 'Monto diario acumulado máximo alcanzado',
            'en' => 'Maximum Daily accumulated amount has been reached',
        ],
        '2405' => [
            'classification' => 'denied',
            'es' => 'Número de órdenes diarias máximo alcanzado',
            'en' => 'Maximum Daily order number has been reached',
        ],
        '2406' => [
            'classification' => 'denied',
            'es' => 'Número de órdenes mensuales máximo alcanzado',
            'en' => 'Maximum Monthly order number has been reached',
        ],
        '2407' => [
            'classification' => 'rejected',
            'es' => 'Rechazado por reglas post-autorización',
            'en' => 'Transaction rejected due to post-authorization rules',
        ],
        '2408' => [
            'classification' => 'rejected',
            'es' => 'Rechazado por Cybersource',
            'en' => 'Transaction rejected by Cybersource',
        ],
        '2409' => [
            'classification' => 'rejected',
            'es' => 'E-ticket no válido',
            'en' => 'E-ticket is not valid',
        ],
        '2410' => [
            'classification' => 'rejected',
            'es' => 'Operación no permitida',
            'en' => 'Operation not allowed',
        ],
        '2411' => [
            'classification' => 'rejected',
            'es' => 'Shipper requerido pero no encontrado',
            'en' => 'Shipper is needed but not found',
        ],
        '2412' => [
            'classification' => 'rejected',
            'es' => 'Monto de envío no válido',
            'en' => 'Shipping amount is not valid',
        ],
        '2413' => [
            'classification' => 'rejected',
            'es' => 'Monto de compra no válido',
            'en' => 'Purchase amount is not valid',
        ],
        '2414' => [
            'classification' => 'rejected',
            'es' => 'Monto de comercio asociado no válido',
            'en' => 'Associated Commerce amount is not valid',
        ],
        '2415' => [
            'classification' => 'rejected',
            'es' => 'Montos de compra y comercio asociado no válidos',
            'en' => 'Purchase Amount and Associated Commerce amount are not valid',
        ],
        '2416' => [
            'classification' => 'rejected',
            'es' => 'Número de tarjeta no cumple algoritmo de verificación',
            'en' => 'Card Number does not comply with Check Digit Algorithm',
        ],
        '2500' => [
            'classification' => 'rejected',
            'es' => 'Formato de monto inválido',
            'en' => 'Invalid amount format',
        ],
        '2501' => [
            'classification' => 'rejected',
            'es' => 'Datos ingresados muy largos',
            'en' => 'Entered data is too large',
        ],
        '2600' => [
            'classification' => 'rejected',
            'es' => 'Adquirente no existe',
            'en' => 'Acquirer not existent',
        ],
        '2601' => [
            'classification' => 'rejected',
            'es' => 'Llaves de adquirente no existen',
            'en' => 'Acquirer Keys not existent',
        ],
        '2602' => [
            'classification' => 'rejected',
            'es' => 'Proceso no existe',
            'en' => 'Process not existent',
        ],
        '2603' => [
            'classification' => 'rejected',
            'es' => 'Tipo de comercio inválido para operación',
            'en' => 'Type commerce invalid for operation',
        ],
        '2604' => [
            'classification' => 'rejected',
            'es' => 'Verificación no existe',
            'en' => 'Verification not existent',
        ],
        '2605' => [
            'classification' => 'rejected',
            'es' => 'Datos inválidos',
            'en' => 'Invalid Data',
        ],
        '2606' => [
            'classification' => 'rejected',
            'es' => 'Verificación inválida',
            'en' => 'Verification Invalid',
        ],

        // ========================================
        // 3000 SERIES - Ecommerce Rules
        // ========================================
        '3001' => [
            'classification' => 'rejected',
            'es' => 'Rechazado por regla de ecommerce',
            'en' => 'Rejected by Ecommerce rule',
        ],
        '3002' => [
            'classification' => 'rejected',
            'es' => 'Error en regla de ecommerce',
            'en' => 'Rejected by Ecommerce rule Error',
        ],
        '3003' => [
            'classification' => 'error',
            'es' => 'Error de comunicación en regla de ecommerce',
            'en' => 'Rejected by Ecommerce rule Comm. Error',
        ],

        // ========================================
        // 4000 SERIES - Transaction State Issues
        // ========================================
        '4000' => [
            'classification' => 'rejected',
            'es' => 'Transacción no existe o no tiene estado autorizado',
            'en' => 'Transaction neither exists nor has authorized state',
        ],
        '4001' => [
            'classification' => 'error',
            'es' => 'Error en ejecución de reversión, contactar proveedor',
            'en' => 'Execution error in reversal messaging, contact provider',
        ],
        '4002' => [
            'classification' => 'error',
            'es' => 'Error de comunicación con el procesador',
            'en' => 'Error in communication with the processor',
        ],
        '4003' => [
            'classification' => 'error',
            'es' => 'Error de comunicación con el procesador',
            'en' => 'Error in communication with the processor',
        ],
        '4004' => [
            'classification' => 'error',
            'es' => 'Respuesta incorrecta del procesador',
            'en' => 'Incorrect response from processor',
        ],

        // ========================================
        // 6000 SERIES - Refund Operations
        // ========================================
        '6000' => [
            'classification' => 'authorized',
            'es' => 'Reembolso exitoso',
            'en' => 'Refund successful',
        ],
        '6001' => [
            'classification' => 'authorized',
            'es' => 'Solicitud de reembolso recibida',
            'en' => 'Refund\'s Request received',
        ],
        '6002' => [
            'classification' => 'rejected',
            'es' => 'Número de orden no existe',
            'en' => 'Order Number doesn\'t exist',
        ],
        '6003' => [
            'classification' => 'rejected',
            'es' => 'Estado de orden no es liquidado',
            'en' => 'Order state is not liquidated',
        ],
        '6004' => [
            'classification' => 'rejected',
            'es' => 'Adquirente de orden inválido o no encontrado',
            'en' => 'Order acquirer is invalid or not found',
        ],
        '6005' => [
            'classification' => 'rejected',
            'es' => 'Adquirente no registrado o inactivo',
            'en' => 'Acquirer is not registered or is inactive',
        ],
        '6006' => [
            'classification' => 'rejected',
            'es' => 'Adquirente no maneja reembolsos',
            'en' => 'Acquirer doesn\'t manage refund',
        ],
        '6007' => [
            'classification' => 'rejected',
            'es' => 'Adquirente no maneja reembolsos parciales',
            'en' => 'Acquirer doesn\'t manage partial refund',
        ],
        '6008' => [
            'classification' => 'rejected',
            'es' => 'Adquirente excede número máximo de reembolsos',
            'en' => 'Acquirer exceeds maximum number of refunds',
        ],
        '6009' => [
            'classification' => 'rejected',
            'es' => 'Adquirente excede tiempo máximo de reembolso',
            'en' => 'Acquirer exceeds time maximum of refund',
        ],
        '6010' => [
            'classification' => 'rejected',
            'es' => 'Monto excede el original',
            'en' => 'Amount exceeds the original',
        ],
        '6011' => [
            'classification' => 'rejected',
            'es' => 'Monto no es el mismo que el original',
            'en' => 'Amount is not the same of the original',
        ],
        '6012' => [
            'classification' => 'rejected',
            'es' => 'No hay lote abierto',
            'en' => 'There is not open lot',
        ],
        '6013' => [
            'classification' => 'error',
            'es' => 'No se puede registrar pago',
            'en' => 'Can\'t register Payment',
        ],
        '6014' => [
            'classification' => 'rejected',
            'es' => 'Datos enviados incorrectos',
            'en' => 'Data sent are incorrect',
        ],
        '6015' => [
            'classification' => 'error',
            'es' => 'Estado de orden nulo',
            'en' => 'Order state null',
        ],
        '6020' => [
            'classification' => 'authorized',
            'es' => 'Liquidación exitosa',
            'en' => 'Liquidation successful',
        ],
        '6021' => [
            'classification' => 'error',
            'es' => 'Error de comunicación con el procesador',
            'en' => 'Error in communication with the processor',
        ],
        '6022' => [
            'classification' => 'error',
            'es' => 'Error de comunicación con el procesador',
            'en' => 'Error in communication with the processor',
        ],
        '6023' => [
            'classification' => 'error',
            'es' => 'Respuesta incorrecta del procesador',
            'en' => 'Incorrect response from processor',
        ],
        '6024' => [
            'classification' => 'error',
            'es' => 'Error interno en el proceso',
            'en' => 'Internal error in the process',
        ],
        '6025' => [
            'classification' => 'rejected',
            'es' => 'Orden no está en estado autorizado',
            'en' => 'Order is not in a state authorized',
        ],
        '6026' => [
            'classification' => 'rejected',
            'es' => 'Operación no permitida',
            'en' => 'Error Operation not permitted',
        ],

        // ========================================
        // 7000 SERIES - Query/Inquiry Operations
        // ========================================
        '7000' => [
            'classification' => 'rejected',
            'es' => 'Datos enviados incorrectos',
            'en' => 'Data sent are incorrect',
        ],
        '7001' => [
            'classification' => 'rejected',
            'es' => 'Adquirente no registrado o inactivo',
            'en' => 'Acquirer is not registered or is inactive',
        ],
        '7002' => [
            'classification' => 'rejected',
            'es' => 'Llaves PGP no registradas',
            'en' => 'PGP Keys unregistered',
        ],
        '7003' => [
            'classification' => 'rejected',
            'es' => 'Orden no registrada',
            'en' => 'Order is not registered',
        ],
        '7004' => [
            'classification' => 'rejected',
            'es' => 'Número de referencia no registrado',
            'en' => 'Reference number is not registered',
        ],
        '7005' => [
            'classification' => 'error',
            'es' => 'Ocurrió un error general',
            'en' => 'A general error occurred',
        ],
        '7006' => [
            'classification' => 'rejected',
            'es' => 'Proceso de autorización iniciado pero no exitoso',
            'en' => 'Authorization process was initiated but was unsuccessful',
        ],
        '7007' => [
            'classification' => 'error',
            'es' => 'Autorización en proceso',
            'en' => 'The authorization is in process',
        ],
        '7008' => [
            'classification' => 'rejected',
            'es' => 'Transacción rechazada por timeout',
            'en' => 'Transaction rejected by timeout',
        ],

        // ========================================
        // 8000 SERIES - Reversal Operations
        // ========================================
        '8000' => [
            'classification' => 'rejected',
            'es' => 'Orden debe no ser nula o estar en estado autorizado',
            'en' => 'The order must not be null or must be in the authorized state',
        ],
        '8001' => [
            'classification' => 'rejected',
            'es' => 'Procesador no configurado para reversar',
            'en' => 'Processor not set to reverse',
        ],
        '8002' => [
            'classification' => 'rejected',
            'es' => 'Adquirente no existe',
            'en' => 'Acquirer not existent',
        ],
        '8003' => [
            'classification' => 'rejected',
            'es' => 'AcquirerHandler no existe',
            'en' => 'AcquirerHandler not existent',
        ],
        '8004' => [
            'classification' => 'rejected',
            'es' => 'AcquirerHandler mal configurado',
            'en' => 'AcquirerHandler not well configured',
        ],
        '8005' => [
            'classification' => 'error',
            'es' => 'Error interno en el proceso',
            'en' => 'Internal error in the process',
        ],
        '8006' => [
            'classification' => 'error',
            'es' => 'Problemas conectando a VisaNet',
            'en' => 'There are problems connecting to VisaNet',
        ],
        '8007' => [
            'classification' => 'error',
            'es' => 'Problemas conectando a Credibanco',
            'en' => 'There are problems connecting to Credibanco',
        ],
        '8008' => [
            'classification' => 'error',
            'es' => 'Problemas conectando a STP',
            'en' => 'There are problems connecting to STP',
        ],
        '8009' => [
            'classification' => 'rejected',
            'es' => 'IdAcquirer es requerido',
            'en' => 'IdAcquirer is required',
        ],
        '8010' => [
            'classification' => 'rejected',
            'es' => 'IdCommerce es requerido',
            'en' => 'IdCommerce is required',
        ],
        '8011' => [
            'classification' => 'rejected',
            'es' => 'IdProcess es requerido',
            'en' => 'IdProcess is required',
        ],
        '8012' => [
            'classification' => 'rejected',
            'es' => 'OperationNumber es requerido',
            'en' => 'OperationNumber is required',
        ],
        '8013' => [
            'classification' => 'rejected',
            'es' => 'Orden debe no ser nula o debe estar depositada',
            'en' => 'The order must not be null or must be deposited',
        ],
        '8014' => [
            'classification' => 'error',
            'es' => 'Error de conexión con el procesador',
            'en' => 'Connection error with the processor',
        ],
        '8015' => [
            'classification' => 'rejected',
            'es' => 'Código Mac incorrecto',
            'en' => 'Mac code is incorrect',
        ],

        // ========================================
        // 9000 SERIES - Installments Service
        // ========================================
        '9000' => [
            'classification' => 'error',
            'es' => 'Error de llamada en servicio de cuotas',
            'en' => 'Call error in installments service',
        ],
        '9001' => [
            'classification' => 'rejected',
            'es' => 'Comercio inválido en servicio de cuotas',
            'en' => 'Invalid commerce in installments service',
        ],
        '9002' => [
            'classification' => 'rejected',
            'es' => 'Adquirente inválido en servicio de cuotas',
            'en' => 'Invalid acquirer in installments service',
        ],
        '9003' => [
            'classification' => 'rejected',
            'es' => 'BIN inválido en servicio de cuotas',
            'en' => 'Invalid bin in installments service',
        ],
        '9004' => [
            'classification' => 'rejected',
            'es' => 'Datos inválidos en servicio de cuotas',
            'en' => 'Invalid data in installments service',
        ],
        '9005' => [
            'classification' => 'rejected',
            'es' => 'Lista vacía en servicio de cuotas',
            'en' => 'Empty list in installments service',
        ],

        // ========================================
        // PUNTO-WEB CODES (Letter Codes)
        // ========================================
        'CC' => [
            'classification' => 'denied',
            'es' => 'Tarjeta de crédito inválida',
            'en' => 'Punto-Web Invalid Credit Card',
        ],
        'EC' => [
            'classification' => 'error',
            'es' => 'Error de criptografía',
            'en' => 'Punto-Web Cryptography Error',
        ],
        'FI' => [
            'classification' => 'rejected',
            'es' => 'Fecha inválida',
            'en' => 'Punto-Web Invalid Date',
        ],
        'HI' => [
            'classification' => 'rejected',
            'es' => 'Hora inválida',
            'en' => 'Punto-Web Invalid Time',
        ],
        'HM' => [
            'classification' => 'rejected',
            'es' => 'Hash inválido',
            'en' => 'Punto-Web Invalid Hash',
        ],
        'IA' => [
            'classification' => 'rejected',
            'es' => 'Monto inválido',
            'en' => 'Punto-Web Invalid Amount',
        ],
        'IC' => [
            'classification' => 'rejected',
            'es' => 'Cuota inválida',
            'en' => 'Punto-Web Invalid Installment',
        ],
        'ID' => [
            'classification' => 'rejected',
            'es' => 'Diferido inválido',
            'en' => 'Punto-Web Invalid Deferred',
        ],
        'IM' => [
            'classification' => 'rejected',
            'es' => 'Moneda inválida',
            'en' => 'Punto-Web Invalid Currency',
        ],
        'KD' => [
            'classification' => 'denied',
            'es' => 'Llave denegada',
            'en' => 'Punto-Web Key Denied',
        ],
        'MC' => [
            'classification' => 'rejected',
            'es' => 'Comercio inválido',
            'en' => 'Punto-Web Invalid Commerce',
        ],
        'N0' => [
            'classification' => 'error',
            'es' => 'Forzar STIP',
            'en' => 'Force STIP',
        ],
        'N3' => [
            'classification' => 'rejected',
            'es' => 'Servicio de efectivo no disponible',
            'en' => 'Cash service not available',
        ],
        'N4' => [
            'classification' => 'denied',
            'es' => 'Solicitud de efectivo excede límite del emisor',
            'en' => 'Cash request exceeds issuer limit',
        ],
        'N7' => [
            'classification' => 'denied',
            'es' => 'Rechazado por falla de CVV2',
            'en' => 'Decline for CVV2 failure',
        ],
        'NC' => [
            'classification' => 'denied',
            'es' => 'CVC2 inválido',
            'en' => 'Punto-Web Invalid CVC2',
        ],
        'ND' => [
            'classification' => 'error',
            'es' => 'Sistema no disponible',
            'en' => 'Punto-Web System Not Available',
        ],
        'NE' => [
            'classification' => 'denied',
            'es' => 'Fecha de expiración inválida',
            'en' => 'Punto-Web Invalid Expiry Date',
        ],
        'NR' => [
            'classification' => 'rejected',
            'es' => 'Número de referencia inválido',
            'en' => 'Punto-Web Invalid Reference Number',
        ],
        'OC' => [
            'classification' => 'cancelled',
            'es' => 'Orden cancelada',
            'en' => 'Punto-Web Order Cancelled',
        ],
        'ON' => [
            'classification' => 'denied',
            'es' => 'Clave dinámica SMS incorrecta',
            'en' => 'Punto-Web Incorrect SMS Dynamic Key',
        ],
        'OV' => [
            'classification' => 'denied',
            'es' => 'Clave dinámica SMS vencida',
            'en' => 'Punto-Web SMS Dynamic Key Expired',
        ],
        'P2' => [
            'classification' => 'rejected',
            'es' => 'Información de facturador inválida',
            'en' => 'Invalid biller information',
        ],
        'P5' => [
            'classification' => 'denied',
            'es' => 'Solicitud de cambio/desbloqueo de PIN rechazada',
            'en' => 'PIN Change/Unblock request declined',
        ],
        'P6' => [
            'classification' => 'denied',
            'es' => 'PIN inseguro',
            'en' => 'Unsafe PIN',
        ],
        'PD' => [
            'classification' => 'denied',
            'es' => 'Permiso denegado',
            'en' => 'Punto-Web Permission Denied',
        ],
        'Q1' => [
            'classification' => 'denied',
            'es' => 'Autenticación de tarjeta falló',
            'en' => 'Card Authentication failed',
        ],
        'R0' => [
            'classification' => 'denied',
            'es' => 'Orden de detención de pago',
            'en' => 'Stop Payment Order',
        ],
        'R1' => [
            'classification' => 'denied',
            'es' => 'Orden de revocación de autorización',
            'en' => 'Revocation of Authorization Order',
        ],
        'R3' => [
            'classification' => 'denied',
            'es' => 'Orden de revocación de todas las autorizaciones',
            'en' => 'Revocation of All Authorizations Order',
        ],
        'SM' => [
            'classification' => 'error',
            'es' => 'Mensaje de Secure Code',
            'en' => 'Punto-Web Secure Code Message',
        ],
        'XA' => [
            'classification' => 'error',
            'es' => 'Reenviar al emisor',
            'en' => 'Forward to issuer',
        ],
        'XD' => [
            'classification' => 'error',
            'es' => 'Reenviar al emisor',
            'en' => 'Forward to issuer',
        ],
        'Z3' => [
            'classification' => 'error',
            'es' => 'No se puede conectar; rechazado',
            'en' => 'Unable to go online; declined',
        ],

        // ========================================
        // SPECIAL CODES
        // ========================================
        '99' => [
            'classification' => 'cancelled',
            'es' => 'Transacción cancelada por el usuario',
            'en' => 'Transaction cancelled by user',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Classification User Messages
    |--------------------------------------------------------------------------
    |
    | Default messages to show users based on classification type.
    | These can be overridden by translation files.
    |
    */
    'messages' => [
        'authorized' => [
            'es' => 'Operación Autorizada',
            'en' => 'Transaction Authorized',
        ],
        'denied' => [
            'es' => 'Operación Denegada',
            'en' => 'Transaction Denied',
        ],
        'rejected' => [
            'es' => 'Operación Rechazada',
            'en' => 'Transaction Rejected',
        ],
        'cancelled' => [
            'es' => 'Operación Cancelada',
            'en' => 'Transaction Cancelled',
        ],
        'error' => [
            'es' => 'Error del Sistema',
            'en' => 'System Error',
        ],
    ],
];
