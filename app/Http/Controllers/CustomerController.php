<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\FieldActivity;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\CustomerImportService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::with('onboardedBy');

        // Field agents strictly only see the customers they have created / assigned to their field office
        if (Auth::user()->isFieldAgent()) {
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

        if ($request->filled('agent_id') && !Auth::user()->isFieldAgent()) {
            if ($request->input('agent_id') === 'unassigned') {
                $query->whereNull('onboarded_by');
            } else {
                $query->where('onboarded_by', $request->input('agent_id'));
            }
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
        $unassignedCount = Customer::whereNull('onboarded_by')->count();

        return view('customers.index', compact('customers', 'agents', 'territories', 'unassignedCount'));
    }

    public function create(): View
    {
        $agents = User::where('role', 'field_agent')->where('status', 'active')->orderBy('name')->get();
        $latestId = Customer::max('id') ?? 0;
        $codeCounter = $latestId + 101;
        $suggestedCode = 'CUST-REL-' . str_pad((string)$codeCounter, 4, '0', STR_PAD_LEFT);
        while (Customer::where('customer_code', $suggestedCode)->exists()) {
            $codeCounter++;
            $suggestedCode = 'CUST-REL-' . str_pad((string)$codeCounter, 4, '0', STR_PAD_LEFT);
        }

        return view('customers.create', compact('agents', 'suggestedCode'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Field agents always onboard customers under their own field account
        if (Auth::user()->isFieldAgent()) {
            $request->merge(['onboarded_by' => Auth::id()]);
        }

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'customer_code' => ['required', 'string', 'max:50', 'unique:customers,customer_code'],
            'contact_person' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'alt_phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'category' => ['required', 'in:retailer,wholesaler,electrical_contractor,lighting_showroom,supermarket,real_estate_developer,walk_in'],
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
            'onboarded_by' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['verification_status'] = 'verified';
        $validated['status'] = 'active';

        $customer = Customer::create($validated);

        // Automatically log an onboarding activity if attributed to an agent or creator
        $onboardUserId = $customer->onboarded_by ?: Auth::id();
        if ($onboardUserId) {
            FieldActivity::create([
                'user_id' => $onboardUserId,
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
        }

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Customer ' . $customer->company_name . ' onboarded successfully on ' . Carbon::parse($customer->onboarded_date)->format('M d, Y') . '!');
    }

    public function show(Customer $customer): View
    {
        // Field agents can strictly only view customers assigned to their field office
        if (Auth::user()->isFieldAgent() && $customer->onboarded_by !== Auth::id()) {
            abort(403, 'Unauthorized access: You are only authorized to view customers assigned to your field office.');
        }

        $customer->load(['onboardedBy', 'activities.user', 'orders.items.product', 'payments']);

        $activities = $customer->activities()->with('user')->orderBy('activity_date', 'desc')->paginate(10);
        $orders = $customer->orders()->with('items.product')->orderBy('order_date', 'desc')->get();
        $payments = $customer->payments()->orderBy('payment_date', 'desc')->get();

        return view('customers.show', compact('customer', 'activities', 'orders', 'payments'));
    }

    public function edit(Customer $customer): View
    {
        // Field agents can strictly only edit customers assigned to their field office
        if (Auth::user()->isFieldAgent() && $customer->onboarded_by !== Auth::id()) {
            abort(403, 'Unauthorized access: You can only edit customers assigned to your field office.');
        }

        $agents = User::where('role', 'field_agent')->orderBy('name')->get();

        return view('customers.edit', compact('customer', 'agents'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        // Field agents can strictly only update customers assigned to their field office
        if (Auth::user()->isFieldAgent() && $customer->onboarded_by !== Auth::id()) {
            abort(403, 'Unauthorized access: You can only update customers assigned to your field office.');
        }

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'alt_phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'category' => ['required', 'in:retailer,wholesaler,electrical_contractor,lighting_showroom,supermarket,real_estate_developer,walk_in'],
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

    public function importForm(): View
    {
        $agents = User::where('role', 'field_agent')->where('status', 'active')->orderBy('name')->get();
        $unassignedCount = Customer::whereNull('onboarded_by')->count();

        return view('customers.import', compact('agents', 'unassignedCount'));
    }

    public function downloadTemplate(CustomerImportService $importer): Response
    {
        $csvContent = $importer->generateSampleCsv();

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="reliable_customers_import_template.csv"',
        ]);
    }

    public function importProcess(Request $request, CustomerImportService $importer): RedirectResponse
    {
        $request->validate([
            'spreadsheet' => ['required', 'file', 'max:10240', 'mimes:csv,txt,xlsx'],
            'default_agent_id' => ['nullable', 'exists:users,id'],
        ]);

        $defaultAgentId = $request->filled('default_agent_id') ? (int) $request->input('default_agent_id') : null;

        try {
            $parsedRows = $importer->parseFile($request->file('spreadsheet'));

            if (empty($parsedRows)) {
                return back()->withErrors(['spreadsheet' => 'The uploaded file does not contain any readable customer data rows. Please use the provided template.']);
            }

            $result = $importer->importRows($parsedRows, $defaultAgentId);

            $msg = "Spreadsheet imported successfully! {$result['created']} created, {$result['updated']} updated.";
            if ($result['unassigned'] > 0) {
                $msg .= " ({$result['unassigned']} customers left unassigned for territory allocation).";
            }

            if (!empty($result['errors'])) {
                session()->flash('import_warnings', $result['errors']);
            }

            return redirect()->route('customers.index')->with('success', $msg);
        } catch (\Throwable $e) {
            return back()->withErrors(['spreadsheet' => 'Error processing spreadsheet: ' . $e->getMessage()]);
        }
    }

    public function reassignAgent(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'agent_id' => ['nullable'],
        ]);

        $agentId = !empty($validated['agent_id']) ? (int) $validated['agent_id'] : null;
        if ($agentId && !User::where('id', $agentId)->exists()) {
            return back()->withErrors(['agent_id' => 'Selected field sales rep does not exist.']);
        }

        $customer->assignTo($agentId);

        if ($agentId) {
            $agent = User::find($agentId);
            $msg = "Customer '{$customer->company_name}' successfully attached to Field Sales Rep {$agent->name} ({$agent->agent_code}).";
        } else {
            $msg = "Customer '{$customer->company_name}' has been unassigned and moved to the central HQ pool.";
        }

        return back()->with('success', $msg);
    }

    public function bulkAssign(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_ids' => ['required', 'array', 'min:1'],
            'customer_ids.*' => ['exists:customers,id'],
            'agent_id' => ['nullable'],
        ]);

        $agentId = !empty($validated['agent_id']) ? (int) $validated['agent_id'] : null;
        if ($agentId && !User::where('id', $agentId)->exists()) {
            return back()->withErrors(['agent_id' => 'Selected field sales rep does not exist.']);
        }

        Customer::whereIn('id', $validated['customer_ids'])->update(['onboarded_by' => $agentId]);

        $count = count($validated['customer_ids']);
        if ($agentId) {
            $agent = User::find($agentId);
            $msg = "{$count} customers successfully attached to Field Sales Rep {$agent->name} ({$agent->agent_code}).";
        } else {
            $msg = "{$count} customers have been unassigned and moved to the central HQ pool.";
        }

        return back()->with('success', $msg);
    }
}

