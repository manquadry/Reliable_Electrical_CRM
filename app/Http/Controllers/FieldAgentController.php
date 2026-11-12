<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\FieldActivity;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class FieldAgentController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', 'field_agent');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('agent_code', 'like', "%{$search}%")
                  ->orWhere('territory', 'like', "%{$search}%");
            });
        }

        if ($request->filled('territory')) {
            $query->where('territory', 'like', '%' . $request->input('territory') . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $agents = $query->withCount(['onboardedCustomers', 'activities', 'orders'])
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->withQueryString();

        $territories = User::where('role', 'field_agent')
            ->whereNotNull('territory')
            ->distinct()
            ->pluck('territory');

        return view('agents.index', compact('agents', 'territories'));
    }

    public function create(): View
    {
        // Generate next suggested agent code
        $latestId = User::where('role', 'field_agent')->max('id') ?? 0;
        $suggestedCode = 'REL-FE-' . str_pad((string)($latestId + 101), 3, '0', STR_PAD_LEFT);

        return view('agents.create', compact('suggestedCode'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['nullable', 'string', 'min:6'],
            'agent_code' => ['required', 'string', 'max:50', 'unique:users,agent_code'],
            'phone' => ['required', 'string', 'max:30'],
            'territory' => ['required', 'string', 'max:255'],
            'vehicle_type' => ['nullable', 'string', 'max:100'],
            'monthly_revenue_target' => ['required', 'numeric', 'min:0'],
            'daily_visit_target' => ['required', 'integer', 'min:1'],
            'monthly_onboard_target' => ['required', 'integer', 'min:1'],
            'onboarded_date' => ['required', 'date'],
            'status' => ['required', 'in:active,inactive,suspended'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['role'] = 'field_agent';
        $validated['password'] = Hash::make($request->filled('password') ? $request->input('password') : 'password');
        $validated['onboarded_by_admin_id'] = Auth::id();

        $agent = User::create($validated);

        return redirect()->route('agents.show', $agent)
            ->with('success', 'Field Executive ' . $agent->name . ' (' . $agent->agent_code . ') onboarded successfully!');
    }

    public function show(User $agent): View
    {
        $today = Carbon::today()->toDateString();
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        // Load customers onboarded by this agent
        $customers = Customer::where('onboarded_by', $agent->id)
            ->orderBy('onboarded_date', 'desc')
            ->paginate(10);

        // Load activities
        $activities = FieldActivity::where('user_id', $agent->id)
            ->with('customer')
            ->orderBy('activity_date', 'desc')
            ->orderBy('check_in_time', 'desc')
            ->paginate(15);

        // Load sales orders
        $orders = Order::where('user_id', $agent->id)
            ->with('customer')
            ->orderBy('order_date', 'desc')
            ->take(10)
            ->get();

        // Performance Calculations
        $monthRevenue = (float) Order::where('user_id', $agent->id)
            ->whereBetween('order_date', [$startOfMonth, $endOfMonth])
            ->sum('total_amount');

        $monthOnboardCount = Customer::where('onboarded_by', $agent->id)
            ->whereBetween('onboarded_date', [$startOfMonth, $endOfMonth])
            ->count();

        $todayVisitsCount = FieldActivity::where('user_id', $agent->id)
            ->where('activity_date', $today)
            ->count();

        $revenuePercent = $agent->monthly_revenue_target > 0
            ? min(100, round(($monthRevenue / $agent->monthly_revenue_target) * 100))
            : 0;

        $onboardPercent = $agent->monthly_onboard_target > 0
            ? min(100, round(($monthOnboardCount / $agent->monthly_onboard_target) * 100))
            : 0;

        return view('agents.show', compact(
            'agent',
            'customers',
            'activities',
            'orders',
            'monthRevenue',
            'monthOnboardCount',
            'todayVisitsCount',
            'revenuePercent',
            'onboardPercent',
            'today'
        ));
    }

    public function edit(User $agent): View
    {
        return view('agents.edit', compact('agent'));
    }

    public function update(Request $request, User $agent): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $agent->id],
            'phone' => ['required', 'string', 'max:30'],
            'territory' => ['required', 'string', 'max:255'],
            'vehicle_type' => ['nullable', 'string', 'max:100'],
            'monthly_revenue_target' => ['required', 'numeric', 'min:0'],
            'daily_visit_target' => ['required', 'integer', 'min:1'],
            'monthly_onboard_target' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:active,inactive,suspended'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->input('password'));
        }

        $agent->update($validated);

        return redirect()->route('agents.show', $agent)
            ->with('success', 'Agent profile updated successfully.');
    }

    public function destroy(User $agent): RedirectResponse
    {
        $agent->update(['status' => 'inactive']);

        return redirect()->route('agents.index')
            ->with('info', 'Agent marked as inactive.');
    }
}

