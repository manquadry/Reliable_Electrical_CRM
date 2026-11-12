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
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class ActivityController extends Controller
{
    /**
     * Admin Daily Field Activity Tracker (Date-by-Date View)
     */
    public function dailyTracker(Request $request): View
    {
        $selectedDate = $request->input('date', Carbon::today()->toDateString());
        $carbonDate = Carbon::parse($selectedDate);

        // All field agents
        $allAgents = User::where('role', 'field_agent')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Activities on selected date with customer and agent
        $activities = FieldActivity::where('activity_date', $selectedDate)
            ->with(['user', 'customer'])
            ->orderBy('check_in_time', 'asc')
            ->get();

        // Group activities by agent
        $activitiesByAgent = $activities->groupBy('user_id');

        // Customers onboarded on that exact date
        $onboardedOnDate = Customer::whereDate('onboarded_date', $selectedDate)
            ->with('onboardedBy')
            ->get()
            ->groupBy('onboarded_by');

        // Orders booked on that exact date
        $ordersOnDate = Order::whereDate('order_date', $selectedDate)
            ->with('customer')
            ->get()
            ->groupBy('user_id');

        // Payments collected on that exact date
        $paymentsOnDate = Payment::whereDate('payment_date', $selectedDate)
            ->with('customer')
            ->get()
            ->groupBy('user_id');

        // Compute summary metrics for the day
        $activeAgentCount = $activitiesByAgent->count();
        $totalVisits = $activities->count();
        $totalNewCustomers = Customer::whereDate('onboarded_date', $selectedDate)->count();
        $totalOrdersAmount = Order::whereDate('order_date', $selectedDate)->sum('total_amount');
        $totalCollectionsAmount = Payment::whereDate('payment_date', $selectedDate)->sum('amount');

        // Build structured agent breakdown for the day
        $agentDaySummaries = $allAgents->map(function ($agent) use ($activitiesByAgent, $onboardedOnDate, $ordersOnDate, $paymentsOnDate) {
            $agentActivities = $activitiesByAgent->get($agent->id, collect());
            $agentOnboards = $onboardedOnDate->get($agent->id, collect());
            $agentOrders = $ordersOnDate->get($agent->id, collect());
            $agentPayments = $paymentsOnDate->get($agent->id, collect());

            $visitsCount = $agentActivities->count();
            $target = $agent->daily_visit_target ?: 6;
            $compliancePercent = min(100, round(($visitsCount / $target) * 100));

            return [
                'agent' => $agent,
                'visits_count' => $visitsCount,
                'target' => $target,
                'compliance_percent' => $compliancePercent,
                'is_active' => $visitsCount > 0 || $agentOnboards->count() > 0,
                'activities' => $agentActivities,
                'onboarded_customers' => $agentOnboards,
                'orders' => $agentOrders,
                'orders_total' => $agentOrders->sum('total_amount'),
                'payments' => $agentPayments,
                'payments_total' => $agentPayments->sum('amount'),
                'first_checkin' => $agentActivities->first()?->check_in_time,
                'last_checkin' => $agentActivities->last()?->check_in_time,
            ];
        });

        // Split into active and inactive agents on this date
        $activeAgentsList = $agentDaySummaries->filter(fn($s) => $s['is_active'])->values();
        $inactiveAgentsList = $agentDaySummaries->filter(fn($s) => !$s['is_active'])->values();

        return view('activities.daily_tracker', compact(
            'selectedDate',
            'carbonDate',
            'activeAgentCount',
            'totalVisits',
            'totalNewCustomers',
            'totalOrdersAmount',
            'totalCollectionsAmount',
            'activeAgentsList',
            'inactiveAgentsList',
            'allAgents'
        ));
    }

    /**
     * Activity & Performance Report (Comprehensive Filters & CSV Export)
     */
    public function report(Request $request): View|Response|StreamedResponse
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        $agentId = $request->input('agent_id');
        $activityType = $request->input('activity_type');
        $outcome = $request->input('outcome');

        $query = FieldActivity::with(['user', 'customer'])
            ->whereBetween('activity_date', [$startDate, $endDate]);

        if ($agentId) {
            $query->where('user_id', $agentId);
        }

        if ($activityType) {
            $query->where('activity_type', $activityType);
        }

        if ($outcome) {
            $query->where('outcome', $outcome);
        }

        // CSV Export Handler
        if ($request->input('export') === 'csv') {
            $exportRecords = $query->orderBy('activity_date', 'desc')
                ->orderBy('check_in_time', 'desc')
                ->get();

            $csvHeaders = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="reliable_electrics_activity_report_' . $startDate . '_to_' . $endDate . '.csv"',
            ];

            $callback = function () use ($exportRecords) {
                $file = fopen('php://output', 'w');
                fputcsv($file, [
                    'Activity ID',
                    'Date',
                    'Check-In Time',
                    'Check-Out Time',
                    'Field Agent Name',
                    'Agent Code',
                    'Customer Name',
                    'Customer Code',
                    'Activity Type',
                    'Purpose',
                    'Outcome',
                    'Next Follow-Up',
                    'Location Address',
                    'Latitude',
                    'Longitude',
                    'Notes',
                ]);

                foreach ($exportRecords as $r) {
                    fputcsv($file, [
                        $r->id,
                        $r->activity_date->format('Y-m-d'),
                        $r->check_in_time,
                        $r->check_out_time ?: '-',
                        $r->user?->name ?: 'N/A',
                        $r->user?->agent_code ?: '-',
                        $r->customer?->company_name ?: 'General / Prospecting',
                        $r->customer?->customer_code ?: '-',
                        $r->getActivityTypeLabelAttribute(),
                        $r->purpose,
                        $r->getOutcomeLabelAttribute(),
                        $r->next_followup_date ? $r->next_followup_date->format('Y-m-d') : '-',
                        $r->location_address ?: '-',
                        $r->latitude ?: '-',
                        $r->longitude ?: '-',
                        $r->notes,
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $csvHeaders);
        }

        $activities = $query->orderBy('activity_date', 'desc')
            ->orderBy('check_in_time', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Aggregations for the filter period
        $stats = [
            'total_activities' => FieldActivity::whereBetween('activity_date', [$startDate, $endDate])
                ->when($agentId, fn($q) => $q->where('user_id', $agentId))
                ->count(),
            'store_visits' => FieldActivity::whereBetween('activity_date', [$startDate, $endDate])
                ->when($agentId, fn($q) => $q->where('user_id', $agentId))
                ->where('activity_type', 'store_visit')
                ->count(),
            'new_onboardings' => Customer::whereBetween('onboarded_date', [$startDate, $endDate])
                ->when($agentId, fn($q) => $q->where('onboarded_by', $agentId))
                ->count(),
            'orders_count' => Order::whereBetween('order_date', [$startDate, $endDate])
                ->when($agentId, fn($q) => $q->where('user_id', $agentId))
                ->count(),
            'orders_total_amount' => Order::whereBetween('order_date', [$startDate, $endDate])
                ->when($agentId, fn($q) => $q->where('user_id', $agentId))
                ->sum('total_amount'),
            'collections_total' => Payment::whereBetween('payment_date', [$startDate, $endDate])
                ->when($agentId, fn($q) => $q->where('user_id', $agentId))
                ->sum('amount'),
        ];

        // Agent Performance Scorecard
        $agentScorecards = User::where('role', 'field_agent')
            ->when($agentId, fn($q) => $q->where('id', $agentId))
            ->withCount([
                'activities as period_visits_count' => function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('activity_date', [$startDate, $endDate]);
                },
                'onboardedCustomers as period_onboard_count' => function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('onboarded_date', [$startDate, $endDate]);
                },
                'orders as period_orders_count' => function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('order_date', [$startDate, $endDate]);
                },
            ])
            ->get()
            ->map(function ($agent) use ($startDate, $endDate) {
                $agent->period_revenue = (float) Order::where('user_id', $agent->id)
                    ->whereBetween('order_date', [$startDate, $endDate])
                    ->sum('total_amount');
                $agent->period_collections = (float) Payment::where('user_id', $agent->id)
                    ->whereBetween('payment_date', [$startDate, $endDate])
                    ->sum('amount');
                return $agent;
            });

        $allAgents = User::where('role', 'field_agent')->orderBy('name')->get();

        return view('activities.report', compact(
            'activities',
            'stats',
            'agentScorecards',
            'startDate',
            'endDate',
            'agentId',
            'activityType',
            'outcome',
            'allAgents'
        ));
    }

    /**
     * Show form for logging a field check-in
     */
    public function createCheckIn(Request $request): View
    {
        $customerId = $request->input('customer_id');
        $selectedCustomer = $customerId ? Customer::find($customerId) : null;

        $customers = Customer::where('status', 'active')
            ->orderBy('company_name')
            ->get();

        $today = Carbon::today()->toDateString();
        $currentTime = Carbon::now()->format('H:i');

        return view('activities.create_checkin', compact('customers', 'selectedCustomer', 'today', 'currentTime'));
    }

    /**
     * Store field check-in / activity
     */
    public function storeCheckIn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'activity_date' => ['required', 'date'],
            'check_in_time' => ['required'],
            'check_out_time' => ['nullable'],
            'activity_type' => ['required', 'in:store_visit,new_customer_onboarding,order_booking,payment_collection,product_demo,complaint_resolution'],
            'purpose' => ['required', 'string', 'max:255'],
            'outcome' => ['required', 'in:order_taken,payment_received,followup_scheduled,prospect_interested,not_interested,closed'],
            'notes' => ['nullable', 'string'],
            'next_followup_date' => ['nullable', 'date'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'location_address' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['user_id'] = Auth::id();

        // If location address is empty but customer is selected, inherit customer address
        if (empty($validated['location_address']) && !empty($validated['customer_id'])) {
            $c = Customer::find($validated['customer_id']);
            if ($c) {
                $validated['location_address'] = $c->address . ', ' . $c->city;
                if (empty($validated['latitude'])) {
                    $validated['latitude'] = $c->latitude;
                    $validated['longitude'] = $c->longitude;
                }
            }
        }

        $activity = FieldActivity::create($validated);

        return redirect()->route('activities.daily')
            ->with('success', 'Field activity logged successfully for ' . Carbon::parse($activity->activity_date)->format('M d, Y') . '!');
    }

    /**
     * Live reverse-geocode latitude and longitude into physical address/landmark.
     * Caches requests to prevent duplicate network calls.
     */
    public function reverseGeocode(Request $request): \Illuminate\Http\JsonResponse
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');

        if (!is_numeric($lat) || !is_numeric($lng)) {
            return response()->json(['success' => false, 'message' => 'Invalid coordinates'], 422);
        }

        $latRound = round((float) $lat, 4);
        $lngRound = round((float) $lng, 4);
        $cacheKey = "revgeo_{$latRound}_{$lngRound}";

        $result = \Illuminate\Support\Facades\Cache::remember($cacheKey, 86400, function () use ($latRound, $lngRound) {
            try {
                // Provider 1: OpenStreetMap Nominatim
                $response = \Illuminate\Support\Facades\Http::timeout(4)
                    ->withUserAgent('ReliableElectricsCRM/1.0 (admin@reliableelectrics.ng)')
                    ->get('https://nominatim.openstreetmap.org/reverse', [
                        'format' => 'jsonv2',
                        'lat' => $latRound,
                        'lon' => $lngRound,
                        'addressdetails' => 1,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $addr = $data['address'] ?? [];

                    $parts = [];
                    if (!empty($addr['house_number']) && !empty($addr['road'])) {
                        $parts[] = $addr['house_number'] . ' ' . $addr['road'];
                    } elseif (!empty($addr['road'])) {
                        $parts[] = $addr['road'];
                    } elseif (!empty($addr['neighbourhood'])) {
                        $parts[] = $addr['neighbourhood'];
                    }

                    if (!empty($addr['suburb']) && !in_array($addr['suburb'], $parts)) {
                        $parts[] = $addr['suburb'];
                    }
                    if (!empty($addr['town']) && !in_array($addr['town'], $parts)) {
                        $parts[] = $addr['town'];
                    } elseif (!empty($addr['city']) && !in_array($addr['city'], $parts)) {
                        $parts[] = $addr['city'];
                    } elseif (!empty($addr['county']) && !in_array($addr['county'], $parts)) {
                        $parts[] = $addr['county'];
                    }
                    if (!empty($addr['state'])) {
                        $parts[] = $addr['state'];
                    }

                    $formatted = !empty($parts) ? implode(', ', $parts) : ($data['display_name'] ?? '');

                    return [
                        'success' => true,
                        'address' => $formatted,
                        'road' => $addr['road'] ?? '',
                        'suburb' => $addr['suburb'] ?? $addr['neighbourhood'] ?? '',
                        'city' => $addr['town'] ?? $addr['city'] ?? $addr['county'] ?? 'Lagos',
                        'state' => $addr['state'] ?? 'Lagos State',
                        'source' => 'nominatim',
                    ];
                }
            } catch (\Throwable $e) {
                // Continue to fallback
            }

            try {
                // Provider 2 Fallback: BigDataCloud Reverse Geocoding Client API
                $response = \Illuminate\Support\Facades\Http::timeout(4)
                    ->withUserAgent('ReliableElectricsCRM/1.0')
                    ->get('https://api.bigdatacloud.net/data/reverse-geocode-client', [
                        'latitude' => $latRound,
                        'longitude' => $lngRound,
                        'localityLanguage' => 'en',
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $parts = array_filter([
                        $data['locality'] ?? '',
                        $data['city'] ?? '',
                        $data['principalSubdivision'] ?? '',
                        $data['countryName'] ?? 'Nigeria',
                    ]);

                    return [
                        'success' => true,
                        'address' => implode(', ', array_unique($parts)),
                        'road' => '',
                        'suburb' => $data['locality'] ?? '',
                        'city' => $data['city'] ?? $data['locality'] ?? 'Lagos',
                        'state' => $data['principalSubdivision'] ?? 'Lagos',
                        'source' => 'bigdatacloud',
                    ];
                }
            } catch (\Throwable $e) {
                // Fallback to coordinates
            }

            return [
                'success' => true,
                'address' => "Near Coordinates ({$latRound}, {$lngRound})",
                'road' => '',
                'suburb' => '',
                'city' => 'Lagos',
                'state' => 'Lagos State',
                'source' => 'fallback',
            ];
        });

        return response()->json($result);
    }
}
