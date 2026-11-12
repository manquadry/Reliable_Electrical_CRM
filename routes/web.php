<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FieldAgentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Quick Login switcher for testing roles/agents
Route::get('/quick-login/{user}', [AuthController::class, 'quickLogin'])->name('quick-login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated CRM Routes
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Field Agent Onboarding & Management
    Route::prefix('agents')->name('agents.')->group(function () {
        Route::get('/', [FieldAgentController::class, 'index'])->name('index');
        Route::get('/create', [FieldAgentController::class, 'create'])->name('create');
        Route::post('/', [FieldAgentController::class, 'store'])->name('store');
        Route::get('/{agent}', [FieldAgentController::class, 'show'])->name('show');
        Route::get('/{agent}/edit', [FieldAgentController::class, 'edit'])->name('edit');
        Route::put('/{agent}', [FieldAgentController::class, 'update'])->name('update');
        Route::delete('/{agent}', [FieldAgentController::class, 'destroy'])->name('destroy');
    });

    // Customer Onboarding by Field Agent & Directory
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
    });

    // Reverse Geocoding API for GPS coordinates to address/landmark
    Route::get('/reverse-geocode', [ActivityController::class, 'reverseGeocode'])->name('reverse_geocode');

    // Admin Date-by-Date Daily Activity Tracker & Reports
    Route::prefix('activities')->name('activities.')->group(function () {
        Route::get('/daily', [ActivityController::class, 'dailyTracker'])->name('daily');
        Route::get('/report', [ActivityController::class, 'report'])->name('report');
        Route::get('/check-in', [ActivityController::class, 'createCheckIn'])->name('create_checkin');
        Route::post('/check-in', [ActivityController::class, 'storeCheckIn'])->name('store_checkin');
    });

    // Sales Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->name('update_status');
    });

    // Payments & Collections
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/create', [PaymentController::class, 'create'])->name('create');
        Route::post('/', [PaymentController::class, 'store'])->name('store');
    });

    // Products Catalog
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
    });
});
