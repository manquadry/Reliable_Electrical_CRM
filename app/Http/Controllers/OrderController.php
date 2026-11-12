<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\FieldActivity;
use App\Models\Order;
use App\Models\OrderItem;
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
        $customerId = $request->input('customer_id');
        $selectedCustomer = $customerId ? Customer::find($customerId) : null;

        $customers = Customer::where('status', 'active')->orderBy('company_name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        $latestId = Order::max('id') ?? 0;
        $suggestedOrderNumber = 'ORD-' . date('Y') . '-' . str_pad((string)($latestId + 101), 4, '0', STR_PAD_LEFT);

        return view('orders.create', compact('customers', 'selectedCustomer', 'products', 'suggestedOrderNumber'));
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

            $order = Order::create([
                'order_number' => $request->input('order_number'),
                'customer_id' => $request->input('customer_id'),
                'user_id' => Auth::id(),
                'order_date' => $request->input('order_date'),
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'tax_amount' => 0.00,
                'total_amount' => $total,
                'paid_amount' => 0.00,
                'payment_status' => 'unpaid',
                'order_status' => 'pending_approval',
                'delivery_address' => $request->input('delivery_address'),
                'notes' => $request->input('notes'),
            ]);

            foreach ($itemsData as $item) {
                $order->items()->create($item);

                // Reduce inventory stock
                Product::where('id', $item['product_id'])->decrement('stock_quantity', $item['quantity']);
            }

            // Also record an activity entry for this field order
            FieldActivity::create([
                'user_id' => Auth::id(),
                'customer_id' => $order->customer_id,
                'activity_date' => $order->order_date,
                'check_in_time' => Carbon::now()->format('H:i:s'),
                'check_out_time' => Carbon::now()->addMinutes(20)->format('H:i:s'),
                'activity_type' => 'order_booking',
                'purpose' => 'Sales Order Booking #' . $order->order_number,
                'notes' => 'Generated new order of ₦' . number_format($order->total_amount, 2) . ' with ' . count($itemsData) . ' line items.',
                'outcome' => 'order_taken',
                'location_address' => $order->customer?->address,
            ]);

            return $order;
        });

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order ' . $order->order_number . ' booked successfully!');
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

