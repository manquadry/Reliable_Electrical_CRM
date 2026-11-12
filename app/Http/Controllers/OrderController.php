<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\FieldActivity;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with(['customer', 'user', 'items.product']);

        if (Auth::user()->isFieldAgent() && !$request->has('view_all')) {
            $query->where('user_id', Auth::id());
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('company_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('order_status', $request->input('status'));
        }

        if ($request->filled('agent_id')) {
            $query->where('user_id', $request->input('agent_id'));
        }

        $orders = $query->orderBy('order_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->withQueryString();

        $agents = User::where('role', 'field_agent')->orderBy('name')->get();

        return view('orders.index', compact('orders', 'agents'));
    }

    public function create(Request $request): View
    {
        $isWalkIn = $request->boolean('walk_in') || $request->input('type') === 'walk_in';
        $walkInCustomer = Customer::getWalkInCustomer();

        if ($isWalkIn) {
            $selectedCustomer = $walkInCustomer;
        } else {
            $customerId = $request->input('customer_id');
            $selectedCustomer = $customerId ? Customer::find($customerId) : null;
        }

        $customers = Customer::where('status', 'active')
            ->orderByRaw("CASE WHEN customer_code = 'CUST-WALKIN-HQ' THEN 0 ELSE 1 END")
            ->orderBy('company_name')
            ->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        $latestId = Order::max('id') ?? 0;
        $suggestedOrderNumber = 'ORD-' . date('Y') . '-' . str_pad((string)($latestId + 101), 4, '0', STR_PAD_LEFT);

        return view('orders.create', compact('customers', 'selectedCustomer', 'products', 'suggestedOrderNumber', 'isWalkIn', 'walkInCustomer'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'order_number' => ['required', 'string', 'unique:orders,order_number'],
            'customer_id' => ['required', 'exists:customers,id'],
            'order_date' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'delivery_address' => ['nullable', 'string'],
            'walkin_buyer_name' => ['nullable', 'string', 'max:255'],
            'walkin_buyer_phone' => ['nullable', 'string', 'max:50'],
            'instant_payment' => ['nullable'],
            'payment_method' => ['nullable', 'in:pos_card,cash,bank_transfer'],
            'notes' => ['nullable', 'string'],
        ]);

        $order = DB::transaction(function () use ($request) {
            $subtotal = 0;
            $itemsData = [];

            foreach ($request->input('items') as $itemInput) {
                $qty = (int) $itemInput['quantity'];
                $price = (float) $itemInput['unit_price'];
                $lineTotal = $qty * $price;
                $subtotal += $lineTotal;

                $itemsData[] = [
                    'product_id' => $itemInput['product_id'],
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'total_price' => $lineTotal,
                ];
            }

            $discount = (float) ($request->input('discount_amount') ?? 0);
            $total = max(0, $subtotal - $discount);

            $notes = $request->input('notes');
            if ($request->filled('walkin_buyer_name')) {
                $buyerInfo = "Walk-in Counter Buyer: " . $request->input('walkin_buyer_name');
                if ($request->filled('walkin_buyer_phone')) {
                    $buyerInfo .= " (" . $request->input('walkin_buyer_phone') . ")";
                }
                $notes = $buyerInfo . ($notes ? " | " . $notes : "");
            }

            $isInstantPaid = $request->boolean('instant_payment');
            $orderStatus = $isInstantPaid ? 'approved' : 'pending_approval';
            $paymentStatus = $isInstantPaid ? 'fully_paid' : 'unpaid';
            $paidAmount = $isInstantPaid ? $total : 0.00;

            $order = Order::create([
                'order_number' => $request->input('order_number'),
                'customer_id' => $request->input('customer_id'),
                'user_id' => Auth::id(),
                'order_date' => $request->input('order_date'),
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'tax_amount' => 0.00,
                'total_amount' => $total,
                'paid_amount' => $paidAmount,
                'payment_status' => $paymentStatus,
                'order_status' => $orderStatus,
                'delivery_address' => $request->input('delivery_address'),
                'notes' => $notes,
            ]);

            foreach ($itemsData as $item) {
                $order->items()->create($item);

                // Reduce inventory stock
                Product::where('id', $item['product_id'])->decrement('stock_quantity', $item['quantity']);
            }

            // If instant counter payment was received, record the Payment entry immediately
            if ($isInstantPaid && $total > 0) {
                $method = $request->input('payment_method', 'pos_card');
                $latestPayId = Payment::max('id') ?? 0;
                $payRef = 'PAY-' . date('Y') . '-' . str_pad((string)($latestPayId + 101), 4, '0', STR_PAD_LEFT);

                Payment::create([
                    'payment_reference' => $payRef,
                    'customer_id' => $order->customer_id,
                    'order_id' => $order->id,
                    'user_id' => Auth::id(),
                    'amount' => $total,
                    'payment_date' => $order->order_date,
                    'payment_method' => $method,
                    'reference_note' => 'Instant Counter Payment for Order #' . $order->order_number,
                    'status' => 'confirmed',
                    'notes' => 'Immediate walk-in counter settlement at Gbagada Showroom via ' . strtoupper(str_replace('_', ' ', $method)) . '.',
                ]);
            }

            // Also record an activity entry for this order
            FieldActivity::create([
                'user_id' => Auth::id(),
                'customer_id' => $order->customer_id,
                'activity_date' => $order->order_date,
                'check_in_time' => Carbon::now()->format('H:i:s'),
                'check_out_time' => Carbon::now()->addMinutes(20)->format('H:i:s'),
                'activity_type' => 'order_booking',
                'purpose' => ($isInstantPaid ? 'Counter Walk-in Sale #' : 'Sales Order Booking #') . $order->order_number,
                'notes' => 'Generated order of ₦' . number_format($order->total_amount, 2) . ' with ' . count($itemsData) . ' line items.' . ($isInstantPaid ? ' Paid immediately at counter.' : ''),
                'outcome' => 'order_taken',
                'location_address' => $order->customer?->address,
            ]);

            return $order;
        });

        $successMsg = $request->boolean('instant_payment')
            ? '⚡ Walk-in counter sale completed! Order approved, payment confirmed, and delivery waybill ready to print.'
            : 'Order ' . $order->order_number . ' booked successfully!';

        return redirect()->route('orders.show', $order)->with('success', $successMsg);
    }

    public function show(Order $order): View
    {
        $order->load(['customer', 'user', 'items.product', 'payments']);

        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'order_status' => ['required', 'in:draft,pending_approval,approved,dispatched,delivered,cancelled'],
        ]);

        $order->update($validated);

        return back()->with('success', 'Order status updated to ' . ucfirst(str_replace('_', ' ', $order->order_status)));
    }
}

