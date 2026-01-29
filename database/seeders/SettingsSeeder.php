<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ==================== CART ====================
            [
                'key' => 'cart.expiration_minutes',
                'value' => '20',
                'type' => 'integer',
                'category' => 'cart',
                'label' => 'Tiempo de expiración para confirmar reserva (minutos)',
                'description' => 'Minutos disponibles para completar el pago desde que se agrega el primer item al carrito',
                'validation_rules' => json_encode(['required', 'integer', 'min:5', 'max:120']),
                'sort_order' => 1,
            ],

            // ==================== BOOKING ====================
            [
                'key' => 'booking.max_persons_per_booking',
                'value' => '12',
                'type' => 'integer',
                'category' => 'booking',
                'label' => 'Máximo de personas por reserva',
                'description' => 'Número máximo de personas permitidas en una sola reserva',
                'validation_rules' => json_encode(['integer', 'min:1', 'max:50']),
                'sort_order' => 1,
            ],
            [
                'key' => 'booking.auto_confirm',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'booking',
                'label' => 'Auto-confirmar reservas',
                'description' => 'Confirmar automáticamente las reservas al recibir pago exitoso',
                'validation_rules' => json_encode(['required', 'boolean']),
                'sort_order' => 4,
            ],


            [
                'key' => 'booking.cancellation_hours',
                'value' => '24',
                'type' => 'integer',
                'category' => 'booking',
                'label' => 'Horas antes para cancelar',
                'description' => 'Horas de anticipación requeridas para cancelación gratuita',
                'validation_rules' => json_encode(['required', 'integer', 'min:0', 'max:168']),
                'sort_order' => 7,
            ],
            [
                'key' => 'booking.cancellation_refund_percent',
                'value' => '100',
                'type' => 'integer',
                'category' => 'booking',
                'label' => 'Porcentaje de reembolso',
                'description' => 'Porcentaje de reembolso en cancelaciones dentro del plazo',
                'validation_rules' => json_encode(['required', 'integer', 'min:0', 'max:100']),
                'sort_order' => 8,
            ],
            [
                'key' => 'booking.allow_cancellation',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'booking',
                'label' => 'Permitir cancelaciones',
                'description' => 'Permitir a los clientes cancelar sus reservas',
                'validation_rules' => json_encode(['required', 'boolean']),
                'sort_order' => 9,
            ],
            [
                'key' => 'booking.allow_modification',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'booking',
                'label' => 'Permitir modificaciones',
                'description' => 'Permitir a los clientes modificar sus reservas',
                'validation_rules' => json_encode(['required', 'boolean']),
                'sort_order' => 10,
            ],
            [
                'key' => 'booking.modification_hours',
                'value' => '48',
                'type' => 'integer',
                'category' => 'booking',
                'label' => 'Horas antes para modificar',
                'description' => 'Horas de anticipación requeridas para modificar una reserva',
                'validation_rules' => json_encode(['required', 'integer', 'min:0', 'max:168']),
                'sort_order' => 11,
            ],
            [
                'key' => 'booking.payment_link_expiration_hours',
                'value' => '2',
                'type' => 'integer',
                'category' => 'booking',
                'label' => 'Duración del enlace de pago (horas)',
                'description' => 'Horas de validez del enlace de pago antes de expirar',
                'validation_rules' => json_encode(['required', 'integer', 'min:1', 'max:168']),
                'sort_order' => 12,
            ],

            // ==================== EMAIL ====================
            [
                'key' => 'email.booking_notifications',
                'value' => 'notifications@example.com',
                'type' => 'email',
                'category' => 'email',
                'label' => 'Email de notificaciones',
                'description' => 'Email donde se reciben notificaciones de nuevas reservas - Configure during deployment',
                'validation_rules' => json_encode(['required', 'email']),
                'sort_order' => 1,
            ],
            [
                'key' => 'email.send_confirmation',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'email',
                'label' => 'Enviar confirmaciones',
                'description' => 'Enviar email de confirmación al cliente después de la reserva',
                'validation_rules' => json_encode(['required', 'boolean']),
                'sort_order' => 2,
            ],
            [
                'key' => 'email.send_reminder',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'email',
                'label' => 'Enviar recordatorios',
                'description' => 'Enviar email de recordatorio antes del product',
                'validation_rules' => json_encode(['required', 'boolean']),
                'sort_order' => 3,
            ],
            [
                'key' => 'email.reminder_hours',
                'value' => '24',
                'type' => 'integer',
                'category' => 'email',
                'label' => 'Horas antes del recordatorio',
                'description' => 'Horas antes del product para enviar recordatorio',
                'validation_rules' => json_encode(['required', 'integer', 'min:1', 'max:168']),
                'sort_order' => 4,
            ],

            // ==================== PAYMENT GATEWAYS ====================
            [
                'key' => 'payment.gateway.stripe',
                'value' => '0',
                'type' => 'boolean',
                'category' => 'payment',
                'label' => 'Stripe',
                'description' => 'Habilitar pagos con Stripe (tarjetas internacionales)',
                'validation_rules' => json_encode(['boolean']),
                'sort_order' => 3,
            ],
            [
                'key' => 'payment.gateway.alignet',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'payment',
                'label' => 'Alignet (Banco Nacional - VPOS2)',
                'description' => 'Habilitar pagos con Alignet/Payme (Pasarela VPOS2 de Banco Nacional)',
                'validation_rules' => json_encode(['boolean']),
                'sort_order' => 1,
            ],
            [
                'key' => 'payment.gateway.paypal',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'payment',
                'label' => 'PayPal',
                'description' => 'Habilitar pagos con PayPal (Internacional)',
                'validation_rules' => json_encode(['boolean']),
                'sort_order' => 6,
            ],

            // ==================== PAYMENT CURRENCIES ====================
            [
                'key' => 'payment.currency.usd',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'payment',
                'label' => 'Dólares (USD)',
                'description' => 'Habilitar pagos en dólares estadounidenses',
                'validation_rules' => json_encode(['boolean']),
                'sort_order' => 10,
            ],
            [
                'key' => 'payment.currency.crc',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'payment',
                'label' => 'Colones (CRC)',
                'description' => 'Habilitar pagos en colones costarricenses',
                'validation_rules' => json_encode(['boolean']),
                'sort_order' => 11,
            ],
            [
                'key' => 'payment.currency.eur',
                'value' => '0',
                'type' => 'boolean',
                'category' => 'payment',
                'label' => 'Euros (EUR)',
                'description' => 'Habilitar pagos en euros',
                'validation_rules' => json_encode(['boolean']),
                'sort_order' => 12,
            ],

            // ==================== EXCHANGE RATES ====================
            [
                'key' => 'payment.exchange_rate.usd_to_crc',
                'value' => '520',
                'type' => 'integer',
                'category' => 'payment',
                'label' => 'Tipo de cambio USD → CRC',
                'description' => 'Colones por cada dólar (ejemplo: 520 significa 1 USD = 520 CRC)',
                'validation_rules' => json_encode(['required', 'integer', 'min:1']),
                'sort_order' => 20,
            ],
            [
                'key' => 'payment.exchange_rate.eur_to_usd',
                'value' => '1.10',
                'type' => 'string',
                'category' => 'payment',
                'label' => 'Tipo de cambio EUR → USD',
                'description' => 'Dólares por cada euro (ejemplo: 1.10 significa 1 EUR = 1.10 USD)',
                'validation_rules' => json_encode(['required', 'numeric', 'min:0']),
                'sort_order' => 21,
            ],

            // ==================== PAYMENT DEFAULTS ====================
            [
                'key' => 'payment.default_gateway',
                'value' => 'stripe',
                'type' => 'string',
                'category' => 'payment',
                'label' => 'Gateway por defecto',
                'description' => 'Gateway preseleccionado al iniciar el pago',
                'validation_rules' => json_encode(['required', 'string']),
                'sort_order' => 3,
            ],
            [
                'key' => 'payment.default_currency',
                'value' => 'USD',
                'type' => 'string',
                'category' => 'payment',
                'label' => 'Moneda por defecto',
                'description' => 'Moneda preseleccionada al iniciar el pago',
                'validation_rules' => json_encode(['required', 'string']),
                'sort_order' => 4,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
