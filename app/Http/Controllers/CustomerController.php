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
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::with('onboardedBy');

        // Field agents by default see their own or can filter all
        if (Auth::user()->isFieldAgent() && !$request->has('view_all')) {
            $query->where('onboarded_by', Auth::id());
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('customer_code', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('territory')) {
            $query->where('territory', 'like', '%' . $request->input('territory') . '%');
        }

        if ($request->filled('agent_id')) {
            $query->where('onboarded_by', $request->input('agent_id'));
        }

        if ($request->filled('onboarded_date')) {
            $query->whereDate('onboarded_date', $request->input('onboarded_date'));
        }

        $customers = $query->orderBy('onboarded_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->withQueryString();

        $agents = User::where('role', 'field_agent')->orderBy('name')->get();
        $territories = Customer::distinct()->pluck('territory');

        return view('customers.index', compact('customers', 'agents', 'territories'));
    }

    public function create(): View
    {
        $agents = User::where('role', 'field_agent')->where('status', 'active')->orderBy('name')->get();
        $latestId = Customer::max('id') ?? 0;
        $suggestedCode = 'CUST-REL-' . str_pad((string)($latestId + 101), 4, '0', STR_PAD_LEFT);

        return view('customers.create', compact('agents', 'suggestedCode'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'customer_code' => ['required', 'string', 'max:50', 'unique:customers,customer_code'],
            'contact_person' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'alt_phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'category' => ['required', 'in:retailer,wholesaler,electrical_contractor,lighting_showroom,supermarket,real_estate_developer'],
            'territory' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'landmark' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'credit_limit' => ['required', 'numeric', 'min:0'],
            'payment_terms' => ['required', 'in:cash_on_delivery,net_7,net_14,net_30'],
            'onboarded_date' => ['required', 'date'],
            'onboarded_by' => ['required', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['verification_status'] = 'verified';
        $validated['status'] = 'active';

        $customer = Customer::create($validated);

        // Automatically log an onboarding activity for the field agent
        FieldActivity::create([
            'user_id' => $customer->onboarded_by,
            'customer_id' => $customer->id,
            'activity_date' => $customer->onboarded_date,
            'check_in_time' => Carbon::now()->format('H:i:s'),
            'check_out_time' => Carbon::now()->addMinutes(30)->format('H:i:s'),
            'activity_type' => 'new_customer_onboarding',
            'purpose' => 'New Customer Registration & Shop Verification',
            'notes' => 'Successfully registered and verified ' . $customer->company_name . ' (' . $customer->getCategoryLabelAttribute() . ') in ' . $customer->territory . '.',
            'outcome' => 'prospect_interested',
            'latitude' => $customer->latitude,
            'longitude' => $customer->longitude,
            'location_address' => $customer->address . ', ' . $customer->city,
        ]);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Customer ' . $customer->company_name . ' onboarded successfully on ' . Carbon::parse($customer->onboarded_date)->format('M d, Y') . '!');
    }

    public function show(Customer $customer): View
    {
        $customer->load(['onboardedBy', 'activities.user', 'orders.items.product', 'payments']);

        $activities = $customer->activities()->with('user')->orderBy('activity_date', 'desc')->paginate(10);
        $orders = $customer->orders()->with('items.product')->orderBy('order_date', 'desc')->get();
        $payments = $customer->payments()->orderBy('payment_date', 'desc')->get();

        return view('customers.show', compact('customer', 'activities', 'orders', 'payments'));
    }

    public function edit(Customer $customer): View
    {
        $agents = User::where('role', 'field_agent')->orderBy('name')->get();

        return view('customers.edit', compact('customer', 'agents'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'alt_phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'category' => ['required', 'in:retailer,wholesaler,electrical_contractor,lighting_showroom,supermarket,real_estate_developer'],
            'territory' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'landmark' => ['nullable', 'string', 'max:255'],
            'credit_limit' => ['required', 'numeric', 'min:0'],
            'payment_terms' => ['required', 'in:cash_on_delivery,net_7,net_14,net_30'],
            'status' => ['required', 'in:active,inactive,blacklisted'],
            'notes' => ['nullable', 'string'],
        ]);

        $customer->update($validated);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Customer details updated successfully.');
    }
}

