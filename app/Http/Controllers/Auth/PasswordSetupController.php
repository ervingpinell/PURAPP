<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\PasswordSetupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class PasswordSetupController extends Controller
{
    public function __construct(
        protected PasswordSetupService $passwordSetupService
    ) {}

    /**
     * Show the password setup form
     */
    public function showSetupForm(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return redirect()->route('login')
                ->with('error', __('password_setup.token_invalid'));
        }

        // Validate token
        $setupToken = $this->passwordSetupService->validateToken($token);

        if (!$setupToken) {
            return view('auth.password-setup-expired');
        }

        return view('auth.password-setup', [
            'token' => $token,
            'user' => $setupToken->user,
        ]);
    }

    /**
     * Process password setup
     */
    public function setupPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                // Custom password rules: 1 number, 1 special char
                'regex:/[0-9]/',      // At least 1 number
                'regex:/[.¡!@#$%^&*()_+\-]/', // At least 1 special character
            ],
        ], [
            'password.regex' => __('password_setup.password_requirements'),
            'password.min' => __('password_setup.password_min_length'),
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate token first to get user
        $setupToken = $this->passwordSetupService->validateToken($request->token);

        if (!$setupToken) {
            return back()
                ->with('error', __('password_setup.token_invalid'))
                ->withInput();
        }

        $user = $setupToken->user;

        // Setup password (consumes token)
        $success = $this->passwordSetupService->setupPassword(
            $request->token,
            $request->password
        );

        if (!$success) {
            return back()
                ->with('error', __('password_setup.token_invalid'))
                ->withInput();
        }

        // Log user in (DISABLED per user request)
        // Auth::login($user);

        // Send verification email instead of welcome email
        try {
            $user->sendEmailVerificationNotification();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send verification email', ['error' => $e->getMessage()]);
        }

        return redirect()->route('login')
            ->with('success', __('auth.account_created_verify_email'));
    }

    /**
     * Resend setup email
     */
    public function resendSetupEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('password_setup.user_not_found'),
            ], 404);
        }

        // Check if user needs password setup
        if (!$this->passwordSetupService->needsPasswordSetup($user)) {
            return response()->json([
                'success' => false,
                'message' => __('password_setup.already_has_password'),
            ], 400);
        }

        // Check rate limiting (max 3 per hour)
        $recentTokens = $user->passwordSetupTokens()
            ->where('created_at', '>', now()->subHour())
            ->count();

        if ($recentTokens >= config('auth.password_setup.throttle', 3)) {
            return response()->json([
                'success' => false,
                'message' => __('password_setup.too_many_requests'),
            ], 429);
        }

        // Send setup email
        try {
            $this->passwordSetupService->sendSetupEmail($user);

            return response()->json([
                'success' => true,
                'message' => __('auth.setup_link_sent'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('password_setup.send_failed'),
            ], 500);
        }
    }
}
