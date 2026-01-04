<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('password_setup.email_subject') }}</title>
</head>

<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f3f4f6;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 40px 20px;">
                <table role="presentation" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #60a862, #256d1b); padding: 40px 30px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700;">
                                {{ __('password_setup.title') }}
                            </h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="margin: 0 0 20px; color: #374151; font-size: 16px; line-height: 1.6;">
                                {{ __('password_setup.welcome', ['name' => $user->full_name]) }}
                            </p>

                            @if($bookingReference)
                            <div style="background-color: #f0fdf4; border-left: 4px solid #10b981; padding: 16px; margin-bottom: 24px; border-radius: 4px;">
                                <p style="margin: 0; color: #065f46; font-size: 14px;">
                                    <strong>{{ __('password_setup.booking_confirmed', ['reference' => $bookingReference]) }}</strong>
                                </p>
                            </div>
                            @endif

                            <p style="margin: 0 0 20px; color: #374151; font-size: 16px; line-height: 1.6;">
                                {{ __('password_setup.create_password') }}
                            </p>

                            <!-- Benefits List -->
                            <table role="presentation" style="width: 100%; margin: 24px 0;">
                                <tr>
                                    <td style="padding: 8px 0;">
                                        <table role="presentation">
                                            <tr>
                                                <td style="width: 24px; vertical-align: top;">
                                                    <div style="width: 20px; height: 20px; background-color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                        <span style="color: #ffffff; font-size: 12px; font-weight: bold;">✓</span>
                                                    </div>
                                                </td>
                                                <td style="padding-left: 12px; color: #4b5563; font-size: 14px;">
                                                    {{ __('password_setup.benefits.view_bookings') }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0;">
                                        <table role="presentation">
                                            <tr>
                                                <td style="width: 24px; vertical-align: top;">
                                                    <div style="width: 20px; height: 20px; background-color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                        <span style="color: #ffffff; font-size: 12px; font-weight: bold;">✓</span>
                                                    </div>
                                                </td>
                                                <td style="padding-left: 12px; color: #4b5563; font-size: 14px;">
                                                    {{ __('password_setup.benefits.manage_profile') }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0;">
                                        <table role="presentation">
                                            <tr>
                                                <td style="width: 24px; vertical-align: top;">
                                                    <div style="width: 20px; height: 20px; background-color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                        <span style="color: #ffffff; font-size: 12px; font-weight: bold;">✓</span>
                                                    </div>
                                                </td>
                                                <td style="padding-left: 12px; color: #4b5563; font-size: 14px;">
                                                    {{ __('password_setup.benefits.exclusive_offers') }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table role="presentation" style="width: 100%; margin: 32px 0;">
                                <tr>
                                    <td style="text-align: center;">
                                        <a href="{{ $setupUrl }}" style="display: inline-block; padding: 16px 32px; background: linear-gradient(135deg, #60a862, #256d1b); color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px;">
                                            {{ __('password_setup.submit_button') }}
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Expiration Notice -->
                            <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 16px; margin-top: 24px; border-radius: 4px;">
                                <p style="margin: 0; color: #92400e; font-size: 14px;">
                                    <strong>⏰ {{ __('Este enlace expira en :days días', ['days' => $expiresInDays]) }}</strong>
                                </p>
                            </div>

                            <!-- Alternative Link -->
                            <p style="margin: 24px 0 0; color: #6b7280; font-size: 14px; line-height: 1.6;">
                                {{ __('Si el botón no funciona, copia y pega este enlace en tu navegador:') }}
                            </p>
                            <p style="margin: 8px 0 0; word-break: break-all;">
                                <a href="{{ $setupUrl }}" style="color: #60a862; text-decoration: none; font-size: 14px;">{{ $setupUrl }}</a>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 8px; color: #6b7280; font-size: 14px;">
                                {{ config('app.name') }}
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                {{ __('Si no solicitaste crear una cuenta, puedes ignorar este mensaje.') }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>