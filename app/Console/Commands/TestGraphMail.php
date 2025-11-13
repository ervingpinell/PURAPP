<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Services\Mail\GraphAuthService;

class TestGraphMail extends Command
{
    protected $signature = 'mail:test-graph {email?}';

    protected $description = 'Prueba el envío de correos mediante Microsoft Graph API';

    public function handle()
    {
        $this->info('==============================================');
        $this->info('  Test de Microsoft Graph Mail');
        $this->info('==============================================');
        $this->newLine();

        // Verificar configuración
        $this->info('1. Verificando configuración...');

        $mailer = config('mail.default');
        $tenantId = config('services.microsoft.tenant_id');
        $clientId = config('services.microsoft.client_id');
        $clientSecret = config('services.microsoft.client_secret');
        $senderUpn = config('services.microsoft.sender_upn');
        $replyTo = config('services.microsoft.reply_to');

        $this->table(
            ['Config', 'Valor'],
            [
                ['MAIL_MAILER', $mailer],
                ['MSFT_TENANT_ID', $tenantId ? substr($tenantId, 0, 8) . '...' : '❌ NO CONFIGURADO'],
                ['MSFT_CLIENT_ID', $clientId ? substr($clientId, 0, 8) . '...' : '❌ NO CONFIGURADO'],
                ['MSFT_CLIENT_SECRET', $clientSecret ? '✓ Configurado' : '❌ NO CONFIGURADO'],
                ['MSFT_SENDER_UPN', $senderUpn ?: '❌ NO CONFIGURADO'],
                ['MSFT_REPLY_TO', $replyTo ?: '(ninguno)'],
            ]
        );
        $this->newLine();

        // Validar configuración
        if (!$tenantId || !$clientId || !$clientSecret || !$senderUpn) {
            $this->error('❌ Configuración incompleta. Revisa tu .env');
            return Command::FAILURE;
        }

        if ($mailer !== 'graph') {
            $this->warn("⚠ MAIL_MAILER está configurado como '{$mailer}', no como 'graph'");
            $this->newLine();
            if (!$this->confirm('¿Deseas continuar de todas formas?', false)) {
                return Command::FAILURE;
            }
        }

        // Probar autenticación
        $this->info('2. Probando autenticación con Microsoft...');

        try {
            $authService = app(GraphAuthService::class);
            $token = $authService->getAccessToken();
            $this->info('✓ Token obtenido exitosamente: ' . substr($token, 0, 20) . '...');
        } catch (\Throwable $e) {
            $this->error('❌ Error al obtener token: ' . $e->getMessage());
            return Command::FAILURE;
        }
        $this->newLine();

        // Enviar correo de prueba
        $this->info('3. Enviando correo de prueba...');

        $recipientEmail = $this->argument('email')
            ?: $this->ask('Email del destinatario', config('mail.from.address'));

        if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            $this->error('❌ Email inválido');
            return Command::FAILURE;
        }

        try {
            Mail::mailer('graph')->raw(
                'Este es un correo de prueba enviado desde Green Vacations CR usando Microsoft Graph API.' . PHP_EOL . PHP_EOL .
                'Si recibes este mensaje, significa que la configuración está funcionando correctamente.' . PHP_EOL . PHP_EOL .
                'Detalles técnicos:' . PHP_EOL .
                '- Mailer: graph' . PHP_EOL .
                '- Sender UPN: ' . $senderUpn . PHP_EOL .
                '- Tenant ID: ' . substr($tenantId, 0, 8) . '...' . PHP_EOL .
                '- Fecha: ' . now()->format('Y-m-d H:i:s'),
                function ($message) use ($recipientEmail, $senderUpn, $replyTo) {
                    $message->to($recipientEmail)
                            ->from($senderUpn, config('mail.from.name', 'Green Vacations CR'))
                            ->subject('Test de Microsoft Graph Mail - ' . now()->format('Y-m-d H:i:s'));

                    if ($replyTo) {
                        $message->replyTo($replyTo);
                    }
                }
            );

            $this->info('✓ Correo enviado exitosamente a: ' . $recipientEmail);
            $this->newLine();
            $this->info('Revisa tu bandeja de entrada (y spam) en los próximos minutos.');

            return Command::SUCCESS;

        } catch (\Throwable $e) {
            $this->error('❌ Error al enviar correo: ' . $e->getMessage());
            $this->newLine();

            if ($this->option('verbose')) {
                $this->error('Stack trace:');
                $this->line($e->getTraceAsString());
            }

            return Command::FAILURE;
        }
    }
}
