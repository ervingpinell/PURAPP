{{-- Payment Countdown Timer Component --}}
<div class="countdown-timer" id="countdown-timer">
    <div class="countdown-icon">
        <i class="fas fa-clock"></i>
    </div>
    <div class="countdown-content">
        <div class="countdown-label">{{ __('payment.time_remaining') }}</div>
        <div class="countdown-display" id="countdown-display">
            <span id="minutes">--</span>:<span id="seconds">--</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const minutesSpan = document.getElementById('minutes');
        const secondsSpan = document.getElementById('seconds');
        const countdownTimer = document.getElementById('countdown-timer');

        if (!minutesSpan || !secondsSpan || !countdownTimer) {
            console.warn('Countdown elements not found');
            return;
        }

        function updateCountdown() {
            // Use global cartCountdown if available
            if (!window.cartCountdown) {
                console.warn('Global cartCountdown not available');
                return;
            }

            const remainingSeconds = window.cartCountdown.getRemainingSeconds();

            if (remainingSeconds <= 0) {
                // Time's up - redirect to cart
                clearInterval(countdownInterval);
                window.location.href = '{{ route("public.carts.index") }}';
                return;
            }

            const minutes = Math.floor(remainingSeconds / 60);
            const seconds = remainingSeconds % 60;

            minutesSpan.textContent = String(minutes).padStart(2, '0');
            secondsSpan.textContent = String(seconds).padStart(2, '0');

            // Add warning class when less than 2 minutes remaining
            if (remainingSeconds < 120) { // 2 minutes
                countdownTimer.classList.add('warning');
            } else {
                countdownTimer.classList.remove('warning');
            }
        }

        // Wait a bit for cartCountdown to be initialized
        setTimeout(() => {
            if (window.cartCountdown) {
                // Update immediately
                updateCountdown();

                // Update every second
                const countdownInterval = setInterval(updateCountdown, 1000);

                // Store interval ID to clear it later if needed
                window.paymentCountdownInterval = countdownInterval;
            } else {
                console.error('window.cartCountdown not initialized');
            }
        }, 100);
    });
</script>
@endpush
