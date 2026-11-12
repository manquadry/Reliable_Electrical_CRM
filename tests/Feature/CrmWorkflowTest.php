<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\FieldActivity;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrmWorkflowTest extends TestCase
{
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_login_screen_renders_with_demo_accounts(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Reliable Electrics Nigeria Ltd');
    }

    public function test_admin_can_access_dashboard_and_see_metrics(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Reliable Electrics Nigeria Limited');
        $response->assertSee('Field Executives');
    }

    public function test_admin_daily_activity_tracker_renders_by_date(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $today = Carbon::today()->toDateString();

        $response = $this->actingAs($admin)->get('/activities/daily?date=' . $today);
        $response->assertStatus(200);
        $response->assertSee('Field Operations Date Audit');
        $response->assertSee('Field Activity Timeline');
    }

    public function test_activity_report_and_csv_export(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/activities/report');
        $response->assertStatus(200);
        $response->assertSee('Activity Reports & Audit', false);

        // Test CSV Export
        $csvResponse = $this->actingAs($admin)->get('/activities/report?export=csv');
        $csvResponse->assertStatus(200);
        $this->assertEquals('text/csv; charset=UTF-8', $csvResponse->headers->get('Content-Type'));
    }

    public function test_field_agent_onboarding_by_admin(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        $agentData = [
            'name' => 'Kelechi Madu',
            'email' => 'kelechi@reliableelectrics.ng',
            'password' => 'secret123',
            'agent_code' => 'REL-FE-999',
            'phone' => '+234 802 999 8877',
            'territory' => 'Ajah & Sangotedo Axis',
            'vehicle_type' => 'Company Motorbike',
            'monthly_revenue_target' => 4500000.00,
            'daily_visit_target' => 6,
            'monthly_onboard_target' => 12,
            'onboarded_date' => Carbon::today()->toDateString(),
            'status' => 'active',
            'notes' => 'New field rep onboarded for Lagos East expansion.',
        ];

        $response = $this->actingAs($admin)->post('/agents', $agentData);
        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'kelechi@reliableelectrics.ng',
            'agent_code' => 'REL-FE-999',
            'territory' => 'Ajah & Sangotedo Axis',
        ]);
    }

    public function test_customer_onboarding_by_field_agent(): void
    {
        $agent = User::where('role', 'field_agent')->first() ?? User::factory()->create(['role' => 'field_agent']);

        $customerData = [
            'company_name' => 'Lekki Smart Homes Electrical Depot',
            'customer_code' => 'CUST-TEST-888',
            'contact_person' => 'Mr. Dapo Williams',
            'phone' => '+234 813 444 5566',
            'category' => 'lighting_showroom',
            'territory' => 'Lekki Phase 1',
            'address' => 'Plot 22 Admiralty Way, Lekki',
            'city' => 'Lagos',
            'state' => 'Lagos State',
            'credit_limit' => 3500000.00,
            'payment_terms' => 'net_14',
            'onboarded_date' => Carbon::today()->toDateString(),
            'onboarded_by' => $agent->id,
            'notes' => 'Newly onboarded showroom with high commercial turnover.',
        ];

        $response = $this->actingAs($agent)->post('/customers', $customerData);
        $response->assertRedirect();

        $this->assertDatabaseHas('customers', [
            'customer_code' => 'CUST-TEST-888',
            'onboarded_by' => $agent->id,
            'company_name' => 'Lekki Smart Homes Electrical Depot',
        ]);

        // Assert that an onboarding activity was automatically logged for the agent!
        $this->assertDatabaseHas('field_activities', [
            'user_id' => $agent->id,
            'activity_type' => 'new_customer_onboarding',
        ]);
    }

    public function test_field_agent_can_log_checkin_visit(): void
    {
        $agent = User::where('role', 'field_agent')->first() ?? User::factory()->create(['role' => 'field_agent']);
        $customer = Customer::first();

        $checkInData = [
            'customer_id' => $customer->id,
            'activity_date' => Carbon::today()->toDateString(),
            'check_in_time' => '10:30',
            'activity_type' => 'store_visit',
            'purpose' => 'Check stock of distribution boards and take reorder',
            'outcome' => 'order_taken',
            'notes' => 'Customer requested next day dispatch for 20 units.',
        ];

        $response = $this->actingAs($agent)->post('/activities/check-in', $checkInData);
        $response->assertRedirect('/activities/daily');

        $this->assertDatabaseHas('field_activities', [
            'user_id' => $agent->id,
            'customer_id' => $customer->id,
            'purpose' => 'Check stock of distribution boards and take reorder',
        ]);
    }

    public function test_order_creation_reduces_stock_and_creates_items(): void
    {
        $agent = User::where('role', 'field_agent')->first() ?? User::factory()->create(['role' => 'field_agent']);
        $customer = Customer::first();
        $product = Product::first();
        $initialStock = $product->stock_quantity;

        $orderData = [
            'order_number' => 'ORD-TEST-' . time(),
            'customer_id' => $customer->id,
            'order_date' => Carbon::today()->toDateString(),
            'delivery_address' => 'Customer Store Address, Lagos',
            'discount_amount' => 5000,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'unit_price' => $product->wholesale_price,
                ]
            ],
        ];

        $response = $this->actingAs($agent)->post('/orders', $orderData);
        $response->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'order_number' => $orderData['order_number'],
            'customer_id' => $customer->id,
        ]);

        // Verify stock deducted
        $product->refresh();
        $this->assertEquals($initialStock - 2, $product->stock_quantity);
    }

    public function test_reverse_geocode_requires_authentication(): void
    {
        $response = $this->getJson('/reverse-geocode?lat=6.5539&lng=3.3888');
        $response->assertStatus(401);
    }

    public function test_reverse_geocode_validates_coordinates(): void
    {
        $agent = User::where('role', 'field_agent')->first() ?? User::factory()->create();

        $response = $this->actingAs($agent)->getJson('/reverse-geocode?lat=invalid&lng=invalid');
        $response->assertStatus(422);
    }

    public function test_reverse_geocode_returns_clean_address_structure(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            'nominatim.openstreetmap.org/*' => \Illuminate\Support\Facades\Http::response([
                'display_name' => 'Akerele Street, Ifako, Gbagada, Kosofe, Lagos, Nigeria',
                'address' => [
                    'road' => 'Akerele Street',
                    'suburb' => 'Ifako',
                    'town' => 'Gbagada',
                    'county' => 'Kosofe',
                    'state' => 'Lagos',
                    'country' => 'Nigeria',
                ],
            ], 200),
        ]);

        $agent = User::where('role', 'field_agent')->first() ?? User::factory()->create();

        $response = $this->actingAs($agent)->getJson('/reverse-geocode?lat=6.5539&lng=3.3888');
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'road' => 'Akerele Street',
            'city' => 'Gbagada',
            'state' => 'Lagos',
        ]);
    }
}
