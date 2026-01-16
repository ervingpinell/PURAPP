@extends('emails.layouts.base')

@section('content')
<div style="font-family: 'Segoe UI', sans-serif;">
    <h1 style="color: #111827; font-size: 24px; margin-bottom: 20px; text-align: center;">
        @lang('emails.daily_report.subject', ['date' => $date])
    </h1>

    <p style="text-align: center; margin-bottom: 25px;">
        @lang('emails.daily_report.intro')
    </p>

    <div style="background-color: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
        <h3 style="margin-top: 0; color: #374151; border-bottom: 1px solid #d1d5db; padding-bottom: 10px;">
            @lang('emails.daily_report.summary_title')
        </h3>
        <ul style="list-style-type: none; padding: 0; margin: 0;">
            <li style="margin-bottom: 10px; font-size: 15px;">
                <span style="display: inline-block; width: 24px;">✅</span> 
                <strong>@lang('emails.daily_report.confirmed'):</strong> {{ $confirmedCount }} bookings
            </li>
            <li style="margin-bottom: 10px; font-size: 15px;">
                <span style="display: inline-block; width: 24px;">⏳</span> 
                <strong>@lang('emails.daily_report.pending'):</strong> {{ $pendingCount }} bookings
            </li>
            <li style="margin-bottom: 10px; font-size: 15px;">
                <span style="display: inline-block; width: 24px;">❌</span> 
                <strong>@lang('emails.daily_report.cancelled'):</strong> {{ $cancelledCount }} bookings
            </li>
        </ul>
    </div>

    <div style="text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 30px; color: #111827;">
        Total: {{ $confirmedCount + $pendingCount + $cancelledCount }} @lang('emails.daily_report.total')
    </div>

    <p style="margin-bottom: 15px; text-align: center;">
        @lang('emails.daily_report.attachment_info')
    </p>

    <div style="border-top: 1px solid #e5e7eb; padding-top: 20px; font-size: 14px;">
        <strong>@lang('emails.daily_report.excel_contents')</strong>
        <ul style="margin-top: 10px; color: #4b5563;">
            <li><strong>@lang('emails.daily_report.sheet_1'):</strong> @lang('emails.daily_report.sheet_1_desc')</li>
            <li><strong>@lang('emails.daily_report.sheet_2'):</strong> @lang('emails.daily_report.sheet_2_desc')</li>
            <li><strong>@lang('emails.daily_report.sheet_3'):</strong> @lang('emails.daily_report.sheet_3_desc')</li>
        </ul>
    </div>

    <p style="color: #6b7280; font-size: 14px; margin-top: 30px; text-align: center;">
        @lang('emails.common.thanks'),<br>
        {{ config('app.name') }} - @lang('emails.common.operations_team')
    </p>
</div>
@endsection