<?php

namespace App\Listeners;

use App\Services\LogSanitizer;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Fortify\Events\TwoFactorAuthenticationChallenged;
use Laravel\Fortify\Events\TwoFactorAuthenticationEnabled;
use Laravel\Fortify\Events\TwoFactorAuthenticationDisabled;

class AuthAuditSubscriber
{
    /** Util para recortar strings largos (UA, paths, etc.) */
    private function clamp(?string $s, int $max = 180): ?string
    {
        if ($s === null) return null;
        return mb_strlen($s) > $max ? (mb_substr($s, 0, $max) . '…') : $s;
    }

    /** Contexto común (respetando LogContext si ya inyectó X-Request-Id) */
    private function baseContext($request): array
    {
        return [
            'request_id' => $request?->headers->get('X-Request-Id'),
            'ip'         => $request?->ip(),
            'ua'         => $this->clamp((string) $request?->userAgent(), 180),
            'path'       => $this->clamp('/' . ltrim((string) $request?->path(), '/'), 300),
        ];
    }

    /** Sanitiza email/tokens si vienen en el evento */
    private function scrub(array $ctx): array
    {
        return LogSanitizer::scrubArray($ctx, 140);
    }

    public function onLogin(Login $event): void
    {
        $req = request();
        $ctx = $this->scrub(array_filter(array_merge($this->baseContext($req), [
            'user_id' => $event->user?->getAuthIdentifier(),
            'guard'   => $event->guard,
            'remember'=> (bool) ($event->remember ?? false),
        ])));
        Log::channel('security')->info('auth.login.success', $ctx);
    }

    public function onFailed(Failed $event): void
    {
        $req = request();
        // No guardamos contraseña. El email se redacta.
        $ctx = $this->scrub(array_filter(array_merge($this->baseContext($req), [
            'email'  => is_object($event->user) ? $event->user?->email : ($event->credentials['email'] ?? null),
            'guard'  => $event->guard,
        ])));
        Log::channel('security')->warning('auth.login.failed', $ctx);
    }

    public function onLogout(Logout $event): void
    {
        $req = request();
        $ctx = $this->scrub(array_filter(array_merge($this->baseContext($req), [
            'user_id' => $event->user?->getAuthIdentifier(),
            'guard'   => $event->guard,
        ])));
        Log::channel('security')->info('auth.logout', $ctx);
    }

    public function onRegistered(Registered $event): void
    {
        $req = request();
        $ctx = $this->scrub(array_filter(array_merge($this->baseContext($req), [
            'user_id' => $event->user?->getAuthIdentifier(),
        ])));
        Log::channel('security')->info('auth.registered', $ctx);
    }

    public function onPasswordReset(PasswordReset $event): void
    {
        $req = request();
        $ctx = $this->scrub(array_filter(array_merge($this->baseContext($req), [
            'user_id' => $event->user?->getAuthIdentifier(),
        ])));
        Log::channel('security')->notice('auth.password.reset', $ctx);
    }

    public function onVerified(Verified $event): void
    {
        $req = request();
        $ctx = $this->scrub(array_filter(array_merge($this->baseContext($req), [
            'user_id' => $event->user?->getAuthIdentifier(),
        ])));
        Log::channel('security')->info('auth.email.verified', $ctx);
    }

    public function onLockout(Lockout $event): void
    {
        $req = request();
        $ctx = $this->scrub(array_filter(array_merge($this->baseContext($req), [
            // El throttle de Laravel suele pasar $event->request
            'throttle_key' => $event->request?->ip() . '|' . Str::lower((string) $event->request?->input('email')),
        ])));
        Log::channel('security')->warning('auth.lockout', $ctx);
    }

    public function onTwoFAChallenged(TwoFactorAuthenticationChallenged $event): void
    {
        $req = request();
        $ctx = $this->scrub(array_filter(array_merge($this->baseContext($req), [
            'user_id' => $event->user?->getAuthIdentifier(),
        ])));
        Log::channel('security')->info('auth.2fa.challenged', $ctx);
    }

    public function onTwoFAEnabled(TwoFactorAuthenticationEnabled $event): void
    {
        $req = request();
        $ctx = $this->scrub(array_filter(array_merge($this->baseContext($req), [
            'user_id' => $event->user?->getAuthIdentifier(),
        ])));
        Log::channel('security')->notice('auth.2fa.enabled', $ctx);
    }

    public function onTwoFADisabled(TwoFactorAuthenticationDisabled $event): void
    {
        $req = request();
        $ctx = $this->scrub(array_filter(array_merge($this->baseContext($req), [
            'user_id' => $event->user?->getAuthIdentifier(),
        ])));
        Log::channel('security')->notice('auth.2fa.disabled', $ctx);
    }

    /** Registro de los listeners del subscriber */
    public function subscribe($events): array
    {
        return [
            Login::class   => 'onLogin',
            Failed::class  => 'onFailed',
            Logout::class  => 'onLogout',
            Registered::class => 'onRegistered',
            PasswordReset::class => 'onPasswordReset',
            Verified::class => 'onVerified',
            Lockout::class  => 'onLockout',

            TwoFactorAuthenticationChallenged::class => 'onTwoFAChallenged',
            TwoFactorAuthenticationEnabled::class    => 'onTwoFAEnabled',
            TwoFactorAuthenticationDisabled::class   => 'onTwoFADisabled',
        ];
    }
}
