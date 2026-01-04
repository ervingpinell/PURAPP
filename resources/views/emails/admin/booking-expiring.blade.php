@component('mail::message')
# ⚠️ Booking Expiring Soon

**Booking Reference:** {{ $booking->booking_reference }}
**Customer:** {{ $booking->user->name }}
**Email:** {{ $booking->user->email }}
**Tour:** {{ $booking->tour->title }}
**Date:** {{ $booking->details->first()->tour_date ?? 'N/A' }}
**Amount:** ${{ number_format($booking->total, 2) }}
**Expires:** {{ $booking->pending_expires_at->format('Y-m-d H:i:s') }} ({{ $booking->pending_expires_at->diffForHumans() }})

This unpaid booking will be automatically cancelled if payment is not received.

@component('mail::button', ['url' => $extendUrl])
Extend Booking (12h)
@endcomponent

@component('mail::button', ['url' => route('admin.bookings.show', $booking->booking_id)])
View Booking
@endcomponent

Thanks,<br>
{{ config('app.name') }} - Booking System
@endcomponent