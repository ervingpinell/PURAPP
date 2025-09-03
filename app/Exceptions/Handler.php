<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
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
    }
}
