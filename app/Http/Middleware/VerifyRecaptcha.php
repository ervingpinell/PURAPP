<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use ReCaptcha\ReCaptcha;
use App\Services\LoggerHelper;

class VerifyRecaptcha
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $action = 'submit'): Response
    {
        // Skip if reCAPTCHA is disabled
        if (!config('services.recaptcha.enabled', false)) {
            return $next($request);
        }

        $token = $request->input('recaptcha_token');

        if (!$token) {
            LoggerHelper::warning('RecaptchaMiddleware', 'verify', 'Missing reCAPTCHA token', [
                'ip' => $request->ip(),
                'action' => $action,
                'url' => $request->fullUrl()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bot verification required. Please refresh the page and try again.'
            ], 422);
        }

        try {
            $recaptcha = new ReCaptcha(config('services.recaptcha.secret_key'));
            $resp = $recaptcha->setExpectedHostname($request->getHost())
                ->setExpectedAction($action)
                ->setScoreThreshold(config('services.recaptcha.score_threshold', 0.5))
                ->verify($token, $request->ip());

            if (!$resp->isSuccess()) {
                $errors = $resp->getErrorCodes();

                LoggerHelper::warning('RecaptchaMiddleware', 'verify', 'reCAPTCHA verification failed', [
                    'ip' => $request->ip(),
                    'action' => $action,
                    'error_codes' => $errors
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Bot verification failed. Please try again or contact support if the problem persists.'
                ], 422);
            }

            // Check score
            $score = $resp->getScore();
            $threshold = config('services.recaptcha.score_threshold', 0.5);

            if ($score < $threshold) {
                LoggerHelper::warning('RecaptchaMiddleware', 'verify', 'Low reCAPTCHA score detected', [
                    'ip' => $request->ip(),
                    'score' => $score,
                    'threshold' => $threshold,
                    'action' => $action,
                    'user_agent' => $request->userAgent()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Suspicious activity detected. Please contact support if you believe this is an error.',
                    'code' => 'LOW_SCORE'
                ], 422);
            }

            // Verify action matches (optional but recommended)
            $receivedAction = $resp->getAction();
            if ($receivedAction && $receivedAction !== $action) {
                LoggerHelper::warning('RecaptchaMiddleware', 'verify', 'Action mismatch', [
                    'expected' => $action,
                    'received' => $receivedAction,
                    'ip' => $request->ip()
                ]);
            }

            LoggerHelper::info('RecaptchaMiddleware', 'verify', 'reCAPTCHA verification passed', [
                'score' => $score,
                'action' => $action,
                'ip' => $request->ip()
            ]);
        } catch (\Exception $e) {
            LoggerHelper::exception('RecaptchaMiddleware', 'verify', 'reCAPTCHA', null, $e, [
                'action' => $action,
                'ip' => $request->ip()
            ]);

            // Fail open in case of reCAPTCHA service issues (configurable)
            if (config('services.recaptcha.fail_open', false)) {
                return $next($request);
            }

            return response()->json([
                'success' => false,
                'message' => 'Verification service temporarily unavailable. Please try again in a moment.'
            ], 503);
        }

        return $next($request);
    }
}
