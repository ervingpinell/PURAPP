<!DOCTYPE html>
<html>

<head>
    <title>Stripe Test</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>

<body>
    <h1>Stripe Payment Test</h1>

    <div id="payment-element" style="border: 2px solid red; min-height: 300px; padding: 20px;"></div>

    <button id="submit">Pay</button>

    <div id="error-message"></div>

    <script>
        const stripe = Stripe('{{ $stripeKey }}');
        const clientSecret = '{{ $clientSecret }}';

        console.log('Stripe Key:', '{{ $stripeKey }}');
        console.log('Client Secret:', clientSecret);

        const elements = stripe.elements({
            clientSecret
        });
        const paymentElement = elements.create('payment');

        console.log('Mounting payment element...');
        paymentElement.mount('#payment-element');

        paymentElement.on('ready', () => {
            console.log('Payment element is READY!');
        });

        paymentElement.on('loaderror', (event) => {
            console.error('Loader error:', event);
            document.getElementById('error-message').textContent = 'Error: ' + JSON.stringify(event);
        });

        document.getElementById('submit').addEventListener('click', async () => {
            const {
                error
            } = await stripe.confirmPayment({
                elements,
                confirmParams: {
                    return_url: 'http://localhost:8000/payment/return',
                },
            });

            if (error) {
                document.getElementById('error-message').textContent = error.message;
            }
        });
    </script>
</body>

</html>