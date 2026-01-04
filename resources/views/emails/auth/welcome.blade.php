<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('password_setup.email_welcome_subject') }}</title>
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
                                {{ __('password_setup.email_welcome_title', ['name' => $user->name]) }}
                            </h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="margin: 0 0 20px; color: #374151; font-size: 16px; line-height: 1.6;">
                                {{ __('password_setup.email_welcome_text') }}
                            </p>

                            <!-- Logic: Benefits or simple text? Simple text is enough for welcome -->

                            <!-- CTA Button -->
                            <table role="presentation" style="width: 100%; margin: 32px 0;">
                                <tr>
                                    <td style="text-align: center;">
                                        <a href="{{ route('my-bookings') }}" style="display: inline-block; padding: 16px 32px; background: linear-gradient(135deg, #60a862, #256d1b); color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px;">
                                            {{ __('password_setup.email_action_button') }}
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 24px 0 0; color: #6b7280; font-size: 14px; line-height: 1.6;">
                                {{ __('Si el botón no funciona, copia y pega este enlace en tu navegador:') }}
                            </p>
                            <p style="margin: 8px 0 0; word-break: break-all;">
                                <a href="{{ route('my-bookings') }}" style="color: #60a862; text-decoration: none; font-size: 14px;">{{ route('my-bookings') }}</a>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 8px; color: #6b7280; font-size: 14px;">
                                {{ config('app.name') }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>