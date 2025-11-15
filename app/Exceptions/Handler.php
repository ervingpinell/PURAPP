<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        // ==============================
        // 413 – Archivos muy grandes (ya lo tenías)
        // ==============================
        $this->renderable(function (PostTooLargeException $e, $request) {
            $title = __('m_tours.image.ui.error_title');
            $text  = __('m_tours.image.errors.too_large');

            if ($request->wantsJson()) {
                return response()->json([
                    'ok'   => false,
                    'swal' => [
                        'icon'  => 'error',
                        'title' => $title,
                        'text'  => $text,
                    ],
                ], 413);
            }

            return back()->with('swal', [
                'icon'  => 'error',
                'title' => $title,
                'text'  => $text,
            ]);
        });

        // ==============================
        // 429 – Demasiadas solicitudes
        // SOLO ajustamos el formulario de contacto
        // ==============================
        $this->renderable(function (ThrottleRequestsException $e, $request) {

            // Rutas localizadas: es.contact.send, en.contact.send, etc.
            if (
                $request->routeIs('*.contact.send') ||
                $request->routeIs('contact.send')
            ) {
                // Si algún día usas el contacto vía AJAX
                if ($request->wantsJson()) {
                    return response()->json([
                        'ok'    => false,
                        'error' => __('adminlte::adminlte.contact_throttled'),
                    ], 429);
                }

                // Volver al formulario con un error amigable
                return back()
                    ->withInput()
                    ->withErrors([
                        'message' => __('adminlte::adminlte.contact_throttled'),
                    ]);
            }

            // Para todo lo demás, dejamos que Laravel siga con su flujo normal
            // (incluyendo tu /auth/throttled que usa errors/429.blade.php)
        });
    }
}
