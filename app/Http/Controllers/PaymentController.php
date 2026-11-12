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

        if (Auth::user()->isFieldAgent() && !$request->has('view_all')) {
            $query->where('user_id', Auth::id());
        }

        if ($request->filled('method')) {
            $query->where('payment_method', $request->input('method'));
        }

        if ($request->filled('agent_id')) {
            $query->where('user_id', $request->input('agent_id'));
        }

        $payments = $query->orderBy('payment_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->withQueryString();

        $totalCollected = Payment::where('status', 'confirmed')->sum('amount');
        $agents = User::where('role', 'field_agent')->orderBy('name')->get();

        return view('payments.index', compact('payments', 'totalCollected', 'agents'));
    }

    public function create(Request $request): View
    {
        $customerId = $request->input('customer_id');
        $orderId = $request->input('order_id');

        $selectedCustomer = $customerId ? Customer::find($customerId) : null;
        $selectedOrder = $orderId ? Order::find($orderId) : null;

        $customers = Customer::where('status', 'active')->orderBy('company_name')->get();
        $orders = Order::where('payment_status', '!=', 'fully_paid')->with('customer')->orderBy('order_date', 'desc')->get();

        $latestId = Payment::max('id') ?? 0;
        $suggestedReference = 'PAY-' . date('Y') . '-' . str_pad((string)($latestId + 101), 4, '0', STR_PAD_LEFT);

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

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'confirmed';

        DB::transaction(function () use ($validated) {
            $payment = Payment::create($validated);

            // Update order if linked
            if (!empty($payment->order_id)) {
                $order = Order::find($payment->order_id);
                if ($order) {
                    $newPaid = (float)$order->paid_amount + (float)$payment->amount;
                    $status = 'partially_paid';
                    if ($newPaid >= (float)$order->total_amount) {
                        $status = 'fully_paid';
                    }
                    $order->update([
                        'paid_amount' => $newPaid,
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

