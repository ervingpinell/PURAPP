<?php

namespace App\Services\Mail;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GraphAuthService
{
    protected string $tenantId;
    protected string $clientId;
    protected string $clientSecret;
    protected string $cacheKey = 'msgraph_access_token';

    public function __construct()
    {
        $this->tenantId     = config('services.microsoft.tenant_id');
        $this->clientId     = config('services.microsoft.client_id');
        $this->clientSecret = config('services.microsoft.client_secret');
    }

    /**
     * Obtiene un access token válido (usa caché si está disponible)
     */
    public function getAccessToken(): string
    {
        return Cache::remember($this->cacheKey, now()->addMinutes(55), function () {
            return $this->requestAccessToken();
        });
    }

    /**
     * Solicita un nuevo access token a Microsoft
     */
    protected function requestAccessToken(): string
    {
        $url = "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token";

        try {
            $response = Http::asForm()->post($url, [
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope'         => 'https://graph.microsoft.com/.default',
                'grant_type'    => 'client_credentials',
            ]);

            if ($response->failed()) {
                Log::error('Graph API: Error obteniendo access token', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                throw new \RuntimeException('No se pudo obtener access token de Microsoft Graph');
            }

            $data = $response->json();

            if (empty($data['access_token'])) {
                throw new \RuntimeException('Access token vacío en respuesta de Microsoft');
            }

            Log::info('Graph API: Access token obtenido exitosamente');

            return $data['access_token'];

        } catch (\Throwable $e) {
            Log::error('Graph API: Excepción al obtener access token', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Limpia el token en caché (útil si hay error 401)
     */
    public function clearToken(): void
    {
        Cache::forget($this->cacheKey);
    }
}
