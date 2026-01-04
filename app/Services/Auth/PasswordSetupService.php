<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\PasswordSetupToken;
use App\Mail\PasswordSetupMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PasswordSetupService
{
    /**
     * Generate a password setup token for a user
     * Returns array with ['model' => PasswordSetupToken, 'plain_token' => string]
     */
    public function generateSetupToken(User $user): array
    {
        // Invalidate any existing unused tokens for this user
        // Invalidate any existing unused tokens for this user
        // We removed this to allow concurrent tokens (e.g. one sent in email, one generated for success view)
        // PasswordSetupToken::where('user_id', $user->user_id)
        //     ->whereNull('used_at')
        //     ->update(['used_at' => now()]);

        // Generate plain token
        $plainToken = Str::random(64);

        // Create new token with hashed version
        $token = PasswordSetupToken::create([
            'user_id' => $user->user_id,
            'token' => hash('sha256', $plainToken),
            'expires_at' => now()->addMinutes(config('auth.password_setup.expire', 10080)), // 7 days default
        ]);

        Log::info('Password setup token generated', [
            'user_id' => $user->user_id,
            'email' => $user->email,
            'expires_at' => $token->expires_at,
        ]);

        return [
            'model' => $token,
            'plain_token' => $plainToken,
        ];
    }

    /**
     * Validate a setup token
     */
    public function validateToken(string $token): ?PasswordSetupToken
    {
        $hashedToken = hash('sha256', $token);

        $setupToken = PasswordSetupToken::where('token', $hashedToken)
            ->with('user')
            ->first();

        if (!$setupToken) {
            Log::warning('Password setup token not found', ['token_hash' => substr($hashedToken, 0, 10) . '...']);
            return null;
        }

        if (!$setupToken->isValid()) {
            Log::warning('Password setup token invalid', [
                'user_id' => $setupToken->user_id,
                'expired' => $setupToken->isExpired(),
                'used' => $setupToken->isUsed(),
            ]);
            return null;
        }

        return $setupToken;
    }

    /**
     * Setup password for a user using a token
     */
    public function setupPassword(string $token, string $password): bool
    {
        $setupToken = $this->validateToken($token);

        if (!$setupToken) {
            return false;
        }

        $user = $setupToken->user;

        // Update user password
        $user->update([
            'password' => Hash::make($password),
        ]);

        // Mark token as used
        $setupToken->markAsUsed();

        Log::info('Password setup completed', [
            'user_id' => $user->user_id,
            'email' => $user->email,
        ]);

        return true;
    }

    /**
     * Send password setup email to user
     */
    public function sendSetupEmail(User $user, ?string $bookingReference = null): void
    {
        // Generate token (returns both model and plain token)
        $tokenData = $this->generateSetupToken($user);

        // Send email with plain token
        try {
            Mail::to($user->email)
                ->queue(new PasswordSetupMail($user, $tokenData['plain_token'], $bookingReference));

            Log::info('Password setup email queued', [
                'user_id' => $user->user_id,
                'email' => $user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to queue password setup email', [
                'user_id' => $user->user_id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Check if user needs password setup
     */
    public function needsPasswordSetup(User $user): bool
    {
        return empty($user->password) || $user->password === null;
    }

    /**
     * Get active setup token for user
     */
    public function getActiveToken(User $user): ?PasswordSetupToken
    {
        return PasswordSetupToken::where('user_id', $user->user_id)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }
}
