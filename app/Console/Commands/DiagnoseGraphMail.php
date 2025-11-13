<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Services\Mail\GraphAuthService;

class DiagnoseGraphMail extends Command
{
    protected $signature = 'mail:diagnose-graph';

    protected $description = 'Diagnóstico completo de la configuración de Microsoft Graph Mail';

    public function handle()
    {
        $this->info('==============================================');
        $this->info('  Diagnóstico de Microsoft Graph Mail');
        $this->info('==============================================');
        $this->newLine();

        $hasErrors = false;

        // 1. Verificar archivos
        $hasErrors = $this->checkFiles() || $hasErrors;
        $this->newLine();

        // 2. Verificar configuración
        $hasErrors = $this->checkConfiguration() || $hasErrors;
        $this->newLine();

        // 3. Verificar Service Provider
        $hasErrors = $this->checkServiceProvider() || $hasErrors;
        $this->newLine();

        // 4. Verificar conectividad
        $hasErrors = $this->checkConnectivity() || $hasErrors;
        $this->newLine();

        // 5. Verificar autenticación
        $hasErrors = $this->checkAuthentication() || $hasErrors;
        $this->newLine();

        // 6. Verificar permisos de buzón (si es posible)
        $this->checkMailboxPermissions();
        $this->newLine();

        // Resumen
        $this->info('==============================================');
        if ($hasErrors) {
            $this->error('❌ Se encontraron problemas en el diagnóstico');
            $this->newLine();
            $this->warn('Revisa los errores anteriores y consulta la documentación.');
            return Command::FAILURE;
        } else {
            $this->info('✅ Todas las verificaciones pasaron correctamente');
            $this->newLine();
            $this->info('Tu sistema está listo para enviar correos mediante Graph API.');
            return Command::SUCCESS;
        }
    }

    protected function checkFiles(): bool
    {
        $this->info('1. Verificando archivos necesarios...');

        $files = [
            'app/Services/Mail/GraphAuthService.php' => 'GraphAuthService',
            'app/Services/Mail/GraphMailTransport.php' => 'GraphMailTransport',
            'app/Providers/GraphMailServiceProvider.php' => 'GraphMailServiceProvider',
            'app/Console/Commands/TestGraphMail.php' => 'TestGraphMail',
        ];

        $hasErrors = false;

        foreach ($files as $path => $name) {
            if (file_exists(base_path($path))) {
                $this->line("  ✓ {$name} - OK");
            } else {
                $this->error("  ✗ {$name} - NO ENCONTRADO: {$path}");
                $hasErrors = true;
            }
        }

        return $hasErrors;
    }

    protected function checkConfiguration(): bool
    {
        $this->info('2. Verificando configuración...');

        $checks = [
            'MAIL_MAILER' => config('mail.default'),
            'MSFT_TENANT_ID' => config('services.microsoft.tenant_id'),
            'MSFT_CLIENT_ID' => config('services.microsoft.client_id'),
            'MSFT_CLIENT_SECRET' => config('services.microsoft.client_secret'),
            'MSFT_SENDER_UPN' => config('services.microsoft.sender_upn'),
        ];

        $hasErrors = false;

        foreach ($checks as $key => $value) {
            if ($value) {
                if ($key === 'MSFT_CLIENT_SECRET') {
                    $this->line("  ✓ {$key} - Configurado");
                } elseif (in_array($key, ['MSFT_TENANT_ID', 'MSFT_CLIENT_ID'])) {
                    $this->line("  ✓ {$key} - " . substr($value, 0, 8) . '...');
                } else {
                    $this->line("  ✓ {$key} - {$value}");
                }
            } else {
                $this->error("  ✗ {$key} - NO CONFIGURADO");
                $hasErrors = true;
            }
        }

        // Verificar config/services.php
        if (!config('services.microsoft')) {
            $this->error('  ✗ Sección "microsoft" no encontrada en config/services.php');
            $hasErrors = true;
        } else {
            $this->line('  ✓ config/services.php - Configurado correctamente');
        }

        // Verificar config/mail.php
        $mailers = config('mail.mailers');
        if (isset($mailers['graph'])) {
            $this->line('  ✓ config/mail.php - Mailer "graph" registrado');
        } else {
            $this->error('  ✗ config/mail.php - Mailer "graph" NO registrado');
            $hasErrors = true;
        }

        return $hasErrors;
    }

    protected function checkServiceProvider(): bool
    {
        $this->info('3. Verificando Service Provider...');

        $providers = config('app.providers');
        $found = false;

        foreach ($providers as $provider) {
            if (str_contains($provider, 'GraphMailServiceProvider')) {
                $found = true;
                break;
            }
        }

        if ($found) {
            $this->line('  ✓ GraphMailServiceProvider registrado en config/app.php');
            return false;
        } else {
            $this->error('  ✗ GraphMailServiceProvider NO registrado en config/app.php');
            $this->warn('    Agrega: App\Providers\GraphMailServiceProvider::class');
            return true;
        }
    }

    protected function checkConnectivity(): bool
    {
        $this->info('4. Verificando conectividad con Microsoft...');

        try {
            $response = Http::timeout(10)->get('https://login.microsoftonline.com');

            if ($response->successful() || $response->status() < 500) {
                $this->line('  ✓ Conectividad con Microsoft - OK');
                return false;
            } else {
                $this->error('  ✗ Problemas de conectividad con Microsoft');
                $this->warn("    Status: {$response->status()}");
                return true;
            }
        } catch (\Throwable $e) {
            $this->error('  ✗ Error de red: ' . $e->getMessage());
            $this->warn('    Verifica tu conexión a internet');
            return true;
        }
    }

    protected function checkAuthentication(): bool
    {
        $this->info('5. Verificando autenticación...');

        try {
            $authService = app(GraphAuthService::class);
            $token = $authService->getAccessToken();

            $this->line('  ✓ Token obtenido exitosamente');
            $this->line('    Token: ' . substr($token, 0, 20) . '...');

            // Verificar estructura del token (JWT básico)
            $parts = explode('.', $token);
            if (count($parts) === 3) {
                $this->line('  ✓ Token JWT válido (3 partes)');
            } else {
                $this->warn('  ⚠ Token no parece ser un JWT válido');
            }

            return false;

        } catch (\Throwable $e) {
            $this->error('  ✗ Error al obtener token');
            $this->error('    ' . $e->getMessage());

            $errorMsg = strtolower($e->getMessage());

            if (str_contains($errorMsg, 'unauthorized') || str_contains($errorMsg, '401')) {
                $this->newLine();
                $this->warn('Posibles causas:');
                $this->line('  • Client Secret incorrecto o expirado');
                $this->line('  • Client ID o Tenant ID incorrectos');
                $this->line('  • Aplicación no encontrada en Azure AD');
            } elseif (str_contains($errorMsg, 'forbidden') || str_contains($errorMsg, '403')) {
                $this->newLine();
                $this->warn('Posibles causas:');
                $this->line('  • Permisos insuficientes en Azure AD');
                $this->line('  • Falta Admin Consent para los permisos');
            }

            return true;
        }
    }

    protected function checkMailboxPermissions(): void
    {
        $this->info('6. Verificando permisos de buzón...');

        $senderUpn = config('services.microsoft.sender_upn');

        if (!$senderUpn) {
            $this->warn('  ⚠ MSFT_SENDER_UPN no configurado');
            $this->line('    Configura esta variable para verificar permisos');
            return;
        }

        try {
            $authService = app(GraphAuthService::class);
            $token = $authService->getAccessToken();

            // Intentar obtener info del buzón
            $response = Http::withToken($token)
                ->timeout(10)
                ->get("https://graph.microsoft.com/v1.0/users/{$senderUpn}");

            if ($response->successful()) {
                $data = $response->json();
                $this->line("  ✓ Buzón accesible: {$senderUpn}");

                if (isset($data['displayName'])) {
                    $this->line("    Nombre: {$data['displayName']}");
                }
                if (isset($data['mail'])) {
                    $this->line("    Email: {$data['mail']}");
                }
            } elseif ($response->status() === 403) {
                $this->error("  ✗ Acceso denegado al buzón: {$senderUpn}");
                $this->newLine();
                $this->warn('Posibles causas:');
                $this->line('  • La Application Access Policy no se ha propagado (espera 30-60 min)');
                $this->line('  • El buzón no está en el grupo de seguridad');
                $this->line('  • Ejecuta el script de PowerShell: manage-exchange-aac.ps1');
            } elseif ($response->status() === 404) {
                $this->error("  ✗ Buzón no encontrado: {$senderUpn}");
                $this->warn('  Verifica que el UPN sea correcto (case-sensitive)');
            } else {
                $this->warn("  ⚠ No se pudo verificar el buzón (Status: {$response->status()})");
            }

        } catch (\Throwable $e) {
            $this->warn('  ⚠ No se pudo verificar permisos: ' . $e->getMessage());
        }
    }
}
