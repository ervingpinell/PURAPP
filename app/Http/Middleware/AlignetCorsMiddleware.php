<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AlignetCorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Dominios permitidos de Alignet
        $allowedOrigins = [
            'https://integracion.alignetsac.com',
            'https://test2.alignetsac.com',
            'https://vpos2.alignet.com', // Producción (por si acaso)
        ];

        $origin = $request->header('Origin');

        // Si no hay Origin, puede ser un request directo de servidor a servidor, lo dejamos pasar.
        // Si hay Origin, verificamos si está en la lista.
        if ($origin && in_array($origin, $allowedOrigins)) {
            $response = $next($request);

            // Asegurarnos que sea una instancia de Response para añadir headers
            if (!$response instanceof Response) {
                // Si es un view o string, ya debería estar envuelto, pero por seguridad:
                return $response;
            }

            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, X-Requested-With, Authorization');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');

            return $response;
        }

        // Manejo de Preflight OPTIONS (antes de llegar al controlador)
        if ($request->isMethod('OPTIONS') && in_array($request->header('Origin'), $allowedOrigins)) {
            $response = response('', 200);
            $response->headers->set('Access-Control-Allow-Origin', $request->header('Origin'));
            $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, X-Requested-With, Authorization');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            return $response;
        }

        return $next($request);
    }
}
