@component('mail::message')
# Daily Operations Report - {{ $date }}

Here's your daily bookings summary:

@component('mail::panel')
**Summary:**
- ✅ **Confirmed:** {{ $confirmedCount }} bookings
- ⏳ **Pending:** {{ $pendingCount }} bookings
- ❌ **Cancelled:** {{ $cancelledCount }} bookings
@endcomponent

**Total:** {{ $confirmedCount + $pendingCount + $cancelledCount }} bookings for today

Please find the detailed Excel report attached to this email.

**Excel Contents:**
- **Sheet 1:** CONFIRMADAS (sorted by pickup time)
- **Sheet 2:** PENDIENTES (with payment links and expiry)
- **Sheet 3:** CANCELADAS (with cancellation reason)

Thanks,<br>
{{ config('app.name') }} - Operations Team
@endcomponent