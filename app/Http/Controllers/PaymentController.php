<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\FieldActivity;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Payment::with(['customer', 'order', 'user']);

        // Field agents strictly only see collections recorded by their field office
        if (Auth::user()->isFieldAgent()) {
            $query->where('user_id', Auth::id());
        }

        if ($request->filled('method')) {
            $query->where('payment_method', $request->input('method'));
        }

        if ($request->filled('agent_id') && !Auth::user()->isFieldAgent()) {
            $query->where('user_id', $request->input('agent_id'));
        }

        $payments = $query->orderBy('payment_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->withQueryString();

        $totalCollectedQuery = Payment::where('status', 'confirmed');
        if (Auth::user()->isFieldAgent()) {
            $totalCollectedQuery->where('user_id', Auth::id());
        }
        $totalCollected = $totalCollectedQuery->sum('amount');
        $agents = User::where('role', 'field_agent')->orderBy('name')->get();

        return view('payments.index', compact('payments', 'totalCollected', 'agents'));
    }

    public function create(Request $request): View
    {
        $customerId = $request->input('customer_id');
        $orderId = $request->input('order_id');

        $selectedCustomer = $customerId ? Customer::find($customerId) : null;
        $selectedOrder = $orderId ? Order::find($orderId) : null;

        $customersQuery = Customer::where('status', 'active');
        $ordersQuery = Order::where('payment_status', '!=', 'fully_paid')->with('customer');

        if (Auth::user()->isFieldAgent()) {
            $customersQuery->where('onboarded_by', Auth::id());
            $ordersQuery->where('user_id', Auth::id());
        }

        $customers = $customersQuery->orderBy('company_name')->get();
        $orders = $ordersQuery->orderBy('order_date', 'desc')->get();

        $latestId = Payment::max('id') ?? 0;
        $payCounter = $latestId + 101;
        $suggestedReference = 'PAY-' . date('Y') . '-' . str_pad((string)$payCounter, 4, '0', STR_PAD_LEFT);
        while (Payment::where('payment_reference', $suggestedReference)->exists()) {
            $payCounter++;
            $suggestedReference = 'PAY-' . date('Y') . '-' . str_pad((string)$payCounter, 4, '0', STR_PAD_LEFT);
        }

        return view('payments.create', compact('customers', 'orders', 'selectedCustomer', 'selectedOrder', 'suggestedReference'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payment_reference' => ['required', 'string', 'unique:payments,payment_reference'],
            'customer_id' => ['required', 'exists:customers,id'],
            'order_id' => ['nullable', 'exists:orders,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:bank_transfer,pos_card,cash,cheque'],
            'reference_note' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        if (Auth::user()->isFieldAgent()) {
            $customer = Customer::find($validated['customer_id']);
            if ($customer && $customer->onboarded_by !== Auth::id() && $customer->customer_code !== 'CUST-WALKIN-HQ') {
                abort(403, 'Unauthorized access: You can only record payments for stores assigned to your field office.');
            }
        }

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'confirmed';

        DB::transaction(function () use ($validated) {
            $payment = Payment::create($validated);

            // Update order if linked by accurately recalculating confirmed total
            if (!empty($payment->order_id)) {
                $order = Order::find($payment->order_id);
                if ($order) {
                    $totalPaid = (float) Payment::where('order_id', $order->id)
                        ->where('status', 'confirmed')
                        ->sum('amount');
                    $status = 'unpaid';
                    if ($totalPaid >= (float) $order->total_amount) {
                        $status = 'fully_paid';
                    } elseif ($totalPaid > 0) {
                        $status = 'partially_paid';
                    }
                    $order->update([
                        'paid_amount' => $totalPaid,
                        'payment_status' => $status,
                    ]);
                }
            }

            // Log activity for the field agent
            FieldActivity::create([
                'user_id' => Auth::id(),
                'customer_id' => $payment->customer_id,
                'activity_date' => $payment->payment_date,
                'check_in_time' => Carbon::now()->format('H:i:s'),
                'check_out_time' => Carbon::now()->addMinutes(15)->format('H:i:s'),
                'activity_type' => 'payment_collection',
                'purpose' => 'Payment Collection Reference #' . $payment->payment_reference,
                'notes' => 'Received payment of ₦' . number_format($payment->amount, 2) . ' via ' . $payment->getMethodLabelAttribute() . ' (' . ($payment->reference_note ?: 'No Ref') . ').',
                'outcome' => 'payment_received',
                'location_address' => $payment->customer?->address,
            ]);
        });

        return redirect()->route('payments.index')
            ->with('success', 'Payment collection of ₦' . number_format($validated['amount'], 2) . ' recorded successfully!');
    }
}

