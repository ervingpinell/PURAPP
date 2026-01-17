@extends('emails.layouts.base')

@section('content')
<div style="font-family: 'Segoe UI', sans-serif; text-align: center;">
    <h1 style="color: #6b7280; font-size: 24px; margin-bottom: 20px;">{{ __('adminlte::adminlte.email.booking_expired.title') }}</h1>

    <div style="background-color: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 25px; text-align: left; display: inline-block; width: 100%; box-sizing: border-box;">
        <p style="text-align: center; color: #374151; font-weight: 500; font-size: 16px; margin-bottom: 20px;">
            {{ __('adminlte::adminlte.email.booking_expired.intro') }}
        </p>

        <h3 style="border-bottom: 1px solid #d1d5db; padding-bottom: 10px; color: #374151; margin-top: 0; font-size: 18px;">
            {{ __('adminlte::adminlte.email.booking_details') }}
        </h3>
        
        <p style="margin-bottom: 0; font-size: 15px; line-height: 1.8;">
            <strong>{{ __('adminlte::adminlte.email.reference') }}:</strong> {{ $booking->booking_reference }}<br>
            <strong>{{ __('adminlte::adminlte.email.tour') }}:</strong> {{ $booking->tour->title }}<br>
            <strong>{{ __('adminlte::adminlte.email.date') }}:</strong> {{ $booking->details->first()->tour_date ?? 'N/A' }}
        </p>
    </div>

    <p style="margin-bottom: 25px; font-size: 16px; color: #374151;">
        {{ __('adminlte::adminlte.email.booking_expired.rebook_message') }}
    </p>

    <div style="margin-bottom: 30px;">
        <a href="{{ localized_route('tours.show', $booking->tour) }}" style="background-color: #4b5563; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
            {{ __('adminlte::adminlte.email.booking_expired.book_again') }}
        </a>
    </div>


</div>
@endsection