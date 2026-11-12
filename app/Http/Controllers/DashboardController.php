<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\FieldActivity;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        if ($user->isLeadOrAdmin()) {
            // EXECUTIVE ADMIN & FIELD SALES LEAD DASHBOARD METRICS
            $totalAgents = User::where('role', 'field_agent')->count();
            $totalActiveAgents = User::where('role', 'field_agent')->where('status', 'active')->count();
            $totalCustomers = Customer::count();
            $newCustomersThisMonth = Customer::whereBetween('onboarded_date', [$startOfMonth, $endOfMonth])->count();
            $visitsToday = FieldActivity::where('activity_date', $today)->count();
            $ordersThisMonth = Order::whereBetween('order_date', [$startOfMonth, $endOfMonth])->sum('total_amount');
            $collectionsThisMonth = Payment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])->where('status', 'confirmed')->sum('amount');

            // Today's activities
            $recentActivities = FieldActivity::with(['user', 'customer'])
                ->orderBy('activity_date', 'desc')
                ->orderBy('check_in_time', 'desc')
                ->take(10)
                ->get();

            // Field Agent Leaderboard / Today's compliance
            $fieldAgents = User::where('role', 'field_agent')
                ->withCount([
                    'activities as today_visits_count' => function ($query) use ($today) {
                        $query->where('activity_date', $today);
                    },
                    'onboardedCustomers as month_onboards_count' => function ($query) use ($startOfMonth, $endOfMonth) {
                        $query->whereBetween('onboarded_date', [$startOfMonth, $endOfMonth]);
                    },
                ])
                ->get()
                ->map(function ($agent) use ($startOfMonth, $endOfMonth) {
                    $agent->month_revenue = (float) Order::where('user_id', $agent->id)
                        ->whereBetween('order_date', [$startOfMonth, $endOfMonth])
                        ->sum('total_amount');
                    $agent->revenue_target_percent = $agent->monthly_revenue_target > 0
                        ? min(100, round(($agent->month_revenue / $agent->monthly_revenue_target) * 100))
                        : 0;
                    $agent->visit_target_percent = $agent->daily_visit_target > 0
                        ? min(100, round(($agent->today_visits_count / $agent->daily_visit_target) * 100))
                        : 0;
                    return $agent;
                });

            // Territory Breakdown
            $territoryStats = Customer::selectRaw('territory, count(*) as count')
                ->groupBy('territory')
                ->orderByDesc('count')
                ->get();

            // Category Breakdown
            $categoryStats = Customer::selectRaw('category, count(*) as count')
                ->groupBy('category')
                ->get();

            return view('dashboard.admin', compact(
                'totalAgents',
                'totalActiveAgents',
                'totalCustomers',
                'newCustomersThisMonth',
                'visitsToday',
                'ordersThisMonth',
                'collectionsThisMonth',
                'recentActivities',
                'fieldAgents',
                'territoryStats',
                'categoryStats',
                'today'
            ));
        }

        // FIELD AGENT DASHBOARD METRICS
        $myTodayVisits = FieldActivity::where('user_id', $user->id)
            ->where('activity_date', $today)
            ->with('customer')
            ->orderBy('check_in_time', 'desc')
            ->get();

        $myMonthOnboardedCount = Customer::where('onboarded_by', $user->id)
            ->whereBetween('onboarded_date', [$startOfMonth, $endOfMonth])
            ->count();

        $myTotalCustomers = Customer::where('onboarded_by', $user->id)->count();

        $myMonthRevenue = (float) Order::where('user_id', $user->id)
            ->whereBetween('order_date', [$startOfMonth, $endOfMonth])
            ->sum('total_amount');

        $myRecentOnboards = Customer::where('onboarded_by', $user->id)
            ->orderBy('onboarded_date', 'desc')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $myRecentOrders = Order::where('user_id', $user->id)
            ->with('customer')
            ->orderBy('order_date', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.agent', compact(
            'user',
            'myTodayVisits',
            'myMonthOnboardedCount',
            'myTotalCustomers',
            'myMonthRevenue',
            'myRecentOnboards',
            'myRecentOrders',
            'today'
        ));
    }
}

