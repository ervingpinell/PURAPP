<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class PayLaterSettingsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $settings = [
            // Reserve Now & Pay Later
            [
                'category' => 'payment',
                'key' => 'booking.pay_later.enabled',
                'value' => '0',
                'type' => 'boolean',
                'label' => 'Habilitar Reserva Ahora y Paga Después',
                'description' => 'Permite a los clientes registrados reservar sin pago inmediato. Deben pagar manualmente antes del tour.',
                'validation_rules' => ['boolean'],
                'sort_order' => 100,
            ],
            [
                'category' => 'payment',
                'key' => 'booking.pay_later.cancel_hours_before_tour',
                'value' => '24',
                'type' => 'integer',
                'label' => 'Horas antes del tour para cancelar',
                'description' => 'Cancelar automáticamente reservas sin pagar X horas antes del tour (1-72 horas).',
                'validation_rules' => ['integer', 'min:1', 'max:72'],
                'sort_order' => 101,
            ],
            [
                'category' => 'payment',
                'key' => 'booking.pay_later.reminder_days_before',
                'value' => '3',
                'type' => 'integer',
                'label' => 'Días antes para enviar recordatorio',
                'description' => 'Número de días antes del tour para enviar email recordatorio de pago al cliente (1-30 días).',
                'validation_rules' => ['integer', 'min:1', 'max:30'],
                'sort_order' => 102,
            ],
            [
                'category' => 'payment',
                'key' => 'booking.pay_later.link_expires_hours',
                'value' => '72',
                'type' => 'integer',
                'label' => 'Expiración del link de pago (horas)',
                'description' => 'Horas de validez del link de pago enviado al cliente (24-168 horas).',
                'validation_rules' => ['integer', 'min:24', 'max:168'],
                'sort_order' => 103,
            ],
            [
                'category' => 'payment',
                'key' => 'booking.operations_email',
                'value' => 'info@greenvacationscr.com',
                'type' => 'email',
                'label' => 'Email de Operaciones',
                'description' => 'Email para recibir el reporte diario de operaciones.',
                'validation_rules' => ['email'],
                'sort_order' => 104,
            ],
            [
                'category' => 'payment',
                'key' => 'booking.operations_report_time',
                'value' => '06:00',
                'type' => 'time',
                'label' => 'Hora del Reporte Diario',
                'description' => 'Hora para enviar el reporte diario de operaciones (formato 24h: HH:MM).',
                'validation_rules' => ['regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/'],
                'sort_order' => 105,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Pay-later settings created/updated successfully!');
    }
}
