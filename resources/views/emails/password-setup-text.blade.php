{{ __('password_setup.welcome', ['name' => $user->full_name]) }}

@if($bookingReference)
{{ __('password_setup.booking_confirmed', ['reference' => $bookingReference]) }}
@endif

{{ __('password_setup.create_password') }}

✓ {{ __('password_setup.benefits.view_bookings') }}
✓ {{ __('password_setup.benefits.manage_profile') }}
✓ {{ __('password_setup.benefits.exclusive_offers') }}

{{ __('password_setup.submit_button') }}:
{{ $setupUrl }}

⏰ {{ __('Este enlace expira en :days días', ['days' => $expiresInDays]) }}

---

{{ config('app.name') }}

{{ __('Si no solicitaste crear una cuenta, puedes ignorar este mensaje.') }}