<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NormalizeEmail
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->has('email')) {
            $email = $request->input('email');
            if (is_string($email)) {
                $request->merge([
                    'email' => mb_strtolower(trim($email)),
                ]);
            }
        }

        return $next($request);
    }
}
