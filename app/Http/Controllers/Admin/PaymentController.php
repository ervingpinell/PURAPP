<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Setting;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\LoggerHelper;



/**
 * PaymentController
 *
 * Manages payment processing and transactions.
 */
class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * Display a listing of payments
     */

    public function index(Request $request)
    {
        // Include soft-deleted bookings so payments remain visible
        $query = Payment::with([
            'booking' => function ($q) {
                $q->withTrashed();
            },
            'booking.user',
            'booking.tour'
        ])
            ->orderByDesc('created_at');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by gateway
        if ($request->filled('gateway')) {
            $query->where('gateway', $request->gateway);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by booking reference or user email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('booking', function ($bq) use ($search) {
                    $bq->withTrashed() // Include soft-deleted bookings in search
                        ->where('booking_reference', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('email', 'like', "%{$search}%")
                                ->orWhere('full_name', 'like', "%{$search}%");
                        });
                });
            });
        }

        // Paginate with fewer items per page for better performance
        $payments = $query->paginate(15)->appends($request->query());

        // Statistics (use cached values or optimize)
        $stats = [
            'total_amount' => Payment::where('status', 'completed')->sum('amount'),
            'total_count' => Payment::where('status', 'completed')->count(),
            'pending_count' => Payment::where('status', 'pending')->count(),
            'failed_count' => Payment::where('status', 'failed')->count(),
        ];

        // Get enabled gateways from settings
        $enabledGateways = [];
        $gatewaySettings = [
            'stripe' => 'payment.gateway.stripe',
            'paypal' => 'payment.gateway.paypal',
            'tilopay' => 'payment.gateway.tilopay',
            'banco_nacional' => 'payment.gateway.banco_nacional',
            'bac' => 'payment.gateway.bac',
            'bcr' => 'payment.gateway.bcr',
        ];

        foreach ($gatewaySettings as $gateway => $settingKey) {
            $setting = Setting::where('key', $settingKey)->first();
            $isEnabled = $setting ? filter_var($setting->value, FILTER_VALIDATE_BOOL) : false;

            if ($isEnabled) {
                $enabledGateways[] = [
                    'id' => $gateway,
                    'name' => ucfirst(str_replace('_', ' ', $gateway)),
                ];
            }
        }

        return view('admin.payments.index', compact('payments', 'stats', 'enabledGateways'));
    }

    /**
     * Display the specified payment
     */
    public function show(Payment $payment)
    {
        $payment->load(['booking.user', 'booking.tour', 'booking.detail']);

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Process refund
     */
    public function refund(Request $request, Payment $payment)
    {
        $request->validate([
            'amount' => 'nullable|numeric|min:0.01|max:' . $payment->net_amount,
            'reason' => 'required|string|max:500',
        ]);

        try {
            $amount = $request->amount ?? $payment->net_amount;

            $result = $this->paymentService->processRefund($payment, $amount, [
                'reason' => $request->reason,
                'refunded_by' => auth()->id(),
            ]);

            LoggerHelper::mutated('PaymentController', 'refund', 'Payment', $payment->payment_id, ['amount' => $amount, 'reason' => $request->reason]);

            return redirect()
                ->route('admin.payments.show', $payment)
                ->with('success', __('Refund processed successfully. Amount: $' . number_format($amount, 2)));
        } catch (\Exception $e) {
            LoggerHelper::exception('PaymentController', 'refund', 'Payment', $payment->payment_id, $e);

            return redirect()
                ->back()
                ->with('error', __('Refund failed: ' . $e->getMessage()));
        }
    }

    /**
     * Export payments to CSV
     */
    public function export(Request $request)
    {
        $query = Payment::with(['booking.user', 'booking.tour'])
            ->orderByDesc('created_at');

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('gateway')) {
            $query->where('gateway', $request->gateway);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->get();

        $filename = 'payments_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($payments) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'Payment ID',
                'Booking Reference',
                'Customer Name',
                'Customer Email',
                'Tour',
                'Amount',
                'Currency',
                'Status',
                'Gateway',
                'Payment Method',
                'Created At',
                'Paid At',
            ]);

            // Data
            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->payment_id,
                    $payment->booking->booking_reference ?? 'N/A',
                    $payment->booking->user->full_name ?? 'N/A',
                    $payment->booking->user->email ?? 'N/A',
                    $payment->booking->tour->name ?? 'N/A',
                    $payment->amount,
                    $payment->currency,
                    $payment->status,
                    $payment->gateway,
                    $payment->payment_method_type ?? 'N/A',
                    $payment->created_at->format('Y-m-d H:i:s'),
                    $payment->paid_at?->format('Y-m-d H:i:s') ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
