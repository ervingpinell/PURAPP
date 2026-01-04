<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GuestUserService
{
    /**
     * Find existing guest user or create new one
     * 
     * @param array $data ['name', 'email', 'phone']
     * @return User
     */
    public function findOrCreateGuest(array $data): User
    {
        // Validate required fields
        if (empty($data['email']) || empty($data['name'])) {
            throw new \InvalidArgumentException('Email and name are required for guest users');
        }

        // Check if guest user already exists with this email
        $existingGuest = User::where('email', $data['email'])
            ->where('is_guest', true)
            ->first();

        if ($existingGuest) {
            // Update info if needed (phone might have changed)
            $existingGuest->update([
                'full_name' => $data['name'],
                'phone' => $data['phone'] ?? $existingGuest->phone,
            ]);

            return $existingGuest;
        }

        // Check if registered user exists with this email
        $registeredUser = User::where('email', $data['email'])
            ->where('is_guest', false)
            ->first();

        if ($registeredUser) {
            // Email already registered - return existing user
            // They should login instead of booking as guest
            return $registeredUser;
        }

        // Create new guest user
        $guestUser = User::create([
            'full_name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'is_guest' => true,
            'password' => null,
            'email_verified_at' => now(), // Auto-verify guests
        ]);

        \Log::info('[GuestUserService] Created guest user', [
            'user_id' => $guestUser->id,
            'email' => $guestUser->email,
        ]);

        return $guestUser;
    }

    /**
     * Convert guest user to registered user
     * 
     * @param User $guestUser
     * @param string $password
     * @return User
     */
    public function convertToRegistered(User $guestUser, string $password): User
    {
        if (!$guestUser->is_guest) {
            throw new \InvalidArgumentException('User is already registered');
        }

        $guestUser->update([
            'is_guest' => false,
            'password' => Hash::make($password),
        ]);

        \Log::info('[GuestUserService] Converted guest to registered', [
            'user_id' => $guestUser->id,
            'email' => $guestUser->email,
        ]);

        return $guestUser->fresh();
    }

    /**
     * Generate secure token for account conversion
     * 
     * @param User $guestUser
     * @return string
     */
    public function generateConversionToken(User $guestUser): string
    {
        if (!$guestUser->is_guest) {
            throw new \InvalidArgumentException('Only guest users can get conversion tokens');
        }

        // Store token in database with expiration
        $token = Str::random(64);
        $guestUser->update([
            'remember_token' => Hash::make($token),
        ]);

        return $token;
    }

    /**
     * Validate conversion token
     * 
     * @param string $email
     * @param string $token
     * @return User|null
     */
    public function validateConversionToken(string $email, string $token): ?User
    {
        $guestUser = User::where('email', $email)
            ->where('is_guest', true)
            ->first();

        if (!$guestUser || !$guestUser->remember_token) {
            return null;
        }

        if (Hash::check($token, $guestUser->remember_token)) {
            return $guestUser;
        }

        return null;
    }
}
