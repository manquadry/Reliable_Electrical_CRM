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

        $newCustomer = Customer::where('customer_code', 'CUST-TEST-888')->firstOrFail();

        // Verify that the customer show page renders without errors for field agent
        $showResponseAgent = $this->actingAs($agent)->get("/customers/{$newCustomer->id}");
        $showResponseAgent->assertStatus(200);
        $showResponseAgent->assertSee('Lekki Smart Homes Electrical Depot');

        // Verify that the customer show page renders without errors for admin with territory allocation card
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $showResponseAdmin = $this->actingAs($admin)->get("/customers/{$newCustomer->id}");
        $showResponseAdmin->assertStatus(200);
        $showResponseAdmin->assertSee('Field Sales Rep Territory Allocation');

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

    public function test_field_agent_cannot_access_agent_creation(): void
    {
        $agent = User::where('role', 'field_agent')->first() ?? User::factory()->create(['role' => 'field_agent']);

        $response = $this->actingAs($agent)->get('/agents/create');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_agent_creation(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/agents/create');
        $response->assertStatus(200);
    }

    public function test_field_agent_cannot_access_product_creation(): void
    {
        $agent = User::where('role', 'field_agent')->first() ?? User::factory()->create(['role' => 'field_agent']);

        $response = $this->actingAs($agent)->get('/products/create');
        $response->assertStatus(403);
    }

    public function test_field_agent_cannot_update_order_status(): void
    {
        $agent = User::where('role', 'field_agent')->first() ?? User::factory()->create(['role' => 'field_agent']);
        $order = Order::first();

        $response = $this->actingAs($agent)->patch('/orders/' . $order->id . '/status', [
            'order_status' => 'delivered',
        ]);
        $response->assertStatus(403);
    }

    public function test_admin_can_update_order_status(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $order = Order::first();

        $response = $this->actingAs($admin)->patch('/orders/' . $order->id . '/status', [
            'order_status' => 'delivered',
        ]);
        $response->assertRedirect();
        $this->assertEquals('delivered', $order->fresh()->order_status);
    }

    public function test_admin_sees_executive_navigation_menus(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Daily Activity Tracker');
        $response->assertSee('Field Executives');
    }

    public function test_field_agent_does_not_see_executive_menus(): void
    {
        $agent = User::where('role', 'field_agent')->first() ?? User::factory()->create(['role' => 'field_agent']);

        $response = $this->actingAs($agent)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertDontSee('Activity Reports & Audit');
        $response->assertDontSee('Field Executives');
        $response->assertSee('Log Field Check-In');
        $response->assertSee('My Daily Schedule');
    }

    public function test_field_agent_cannot_access_executive_reports(): void
    {
        $agent = User::where('role', 'field_agent')->first() ?? User::factory()->create(['role' => 'field_agent']);

        $response = $this->actingAs($agent)->get('/activities/report');
        $response->assertStatus(403);
    }

    public function test_sales_lead_can_access_leadership_dashboard_and_menus(): void
    {
        $lead = User::where('role', 'sales_manager')->first() ?? User::factory()->create(['role' => 'sales_manager']);

        $response = $this->actingAs($lead)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Team Leadership Dashboard');
        $response->assertSee('Daily Activity Tracker');
        $response->assertSee('Team Performance & Audit', false);
        $response->assertSee('Onboard New Agent');
    }

    public function test_sales_lead_can_onboard_new_field_agent(): void
    {
        $lead = User::where('role', 'sales_manager')->first() ?? User::factory()->create(['role' => 'sales_manager']);

        $newAgentData = [
            'name' => 'Babatunde Fashola',
            'email' => 'babatunde.fashola@reliableelectrics.ng',
            'password' => 'secret123',
            'agent_code' => 'REL-FE-777',
            'phone' => '+234 803 777 6655',
            'territory' => 'Ikorodu Commercial Belt',
            'vehicle_type' => 'Company Motorbike',
            'monthly_revenue_target' => 5000000.00,
            'daily_visit_target' => 7,
            'monthly_onboard_target' => 15,
            'onboarded_date' => Carbon::today()->toDateString(),
            'status' => 'active',
            'notes' => 'Onboarded by HOD to cover Ikorodu hardware markets.',
        ];

        $response = $this->actingAs($lead)->post('/agents', $newAgentData);
        $response->assertRedirect();

        $created = User::where('email', 'babatunde.fashola@reliableelectrics.ng')->first();
        $this->assertNotNull($created);
        $this->assertEquals('field_agent', $created->role);
        $this->assertEquals($lead->id, $created->onboarded_by_admin_id);
    }

    public function test_sales_lead_can_view_activity_reports_and_scorecards(): void
    {
        $lead = User::where('role', 'sales_manager')->first() ?? User::factory()->create(['role' => 'sales_manager']);

        $response = $this->actingAs($lead)->get('/activities/report');
        $response->assertStatus(200);
        $response->assertSee('Performance Scorecard');
    }

    public function test_sales_lead_can_approve_order_status(): void
    {
        $lead = User::where('role', 'sales_manager')->first() ?? User::factory()->create(['role' => 'sales_manager']);
        $order = Order::first();

        $response = $this->actingAs($lead)->patch('/orders/' . $order->id . '/status', [
            'order_status' => 'dispatched',
        ]);
        $response->assertRedirect();
        $this->assertEquals('dispatched', $order->fresh()->order_status);
    }

    public function test_sales_lead_cannot_delete_field_agent(): void
    {
        $lead = User::where('role', 'sales_manager')->first() ?? User::factory()->create(['role' => 'sales_manager']);
        $agent = User::where('role', 'field_agent')->first() ?? User::factory()->create(['role' => 'field_agent']);

        $response = $this->actingAs($lead)->delete('/agents/' . $agent->id);
        $response->assertStatus(403);
    }

    public function test_field_agent_cannot_access_agent_management(): void
    {
        $agent = User::where('role', 'field_agent')->first() ?? User::factory()->create(['role' => 'field_agent']);

        $responseIndex = $this->actingAs($agent)->get('/agents');
        $responseIndex->assertStatus(403);

        $responseCreate = $this->actingAs($agent)->get('/agents/create');
        $responseCreate->assertStatus(403);
    }

    public function test_walk_in_customer_is_accessible_and_correctly_categorized(): void
    {
        $walkIn = Customer::getWalkInCustomer();

        $this->assertEquals('CUST-WALKIN-HQ', $walkIn->customer_code);
        $this->assertEquals('walk_in', $walkIn->category);
        $this->assertEquals('Walk-In / Counter Sales', $walkIn->category_label);
        $this->assertTrue($walkIn->isWalkIn());
    }

    public function test_quick_counter_sale_page_renders_with_walk_in_preset(): void
    {
        $lead = User::where('role', 'sales_manager')->first() ?? User::factory()->create(['role' => 'sales_manager']);

        $response = $this->actingAs($lead)->get('/orders/create?walk_in=1');
        $response->assertStatus(200);
        $response->assertSee('Quick Counter Sale');
        $response->assertSee('Walk-In Individual Buyer Details');
        $response->assertSee('Instant Payment Received at Counter');
    }

    public function test_instant_counter_sale_books_order_deducts_stock_and_creates_confirmed_payment(): void
    {
        $lead = User::where('role', 'sales_manager')->first() ?? User::factory()->create(['role' => 'sales_manager']);
        $walkIn = Customer::getWalkInCustomer();
        $product = Product::where('is_active', true)->where('stock_quantity', '>', 5)->first();
        $initialStock = $product->stock_quantity;

        $orderNumber = 'ORD-TEST-WALKIN-' . time();

        $response = $this->actingAs($lead)->post('/orders', [
            'order_number' => $orderNumber,
            'customer_id' => $walkIn->id,
            'order_date' => Carbon::today()->toDateString(),
            'walkin_buyer_name' => 'Engr. Taiwo Adeleke',
            'walkin_buyer_phone' => '+234 803 999 8877',
            'delivery_address' => 'Gbagada Showroom Counter Collection',
            'instant_payment' => '1',
            'payment_method' => 'pos_card',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'unit_price' => $product->wholesale_price,
                ],
            ],
            'notes' => 'Urgent site installation in Anthony Village',
        ]);

        $response->assertSessionHasNoErrors();

        $order = Order::where('order_number', $orderNumber)->first();
        $this->assertNotNull($order);
        $response->assertRedirect('/orders/' . $order->id);

        // Assert order is approved and fully paid immediately
        $this->assertEquals('approved', $order->order_status);
        $this->assertEquals('fully_paid', $order->payment_status);
        $this->assertEquals($order->total_amount, $order->paid_amount);

        // Assert inventory stock was decremented
        $this->assertEquals($initialStock - 2, $product->fresh()->stock_quantity);

        // Assert buyer info recorded in notes
        $this->assertStringContainsString('Engr. Taiwo Adeleke', $order->notes);
        $this->assertStringContainsString('+234 803 999 8877', $order->notes);

        // Assert Payment record was automatically generated
        $payment = Payment::where('order_id', $order->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('pos_card', $payment->payment_method);
        $this->assertEquals('confirmed', $payment->status);
        $this->assertEquals($order->total_amount, $payment->amount);

        // Assert order detail page renders with Walk-in badge
        $showResponse = $this->actingAs($lead)->get('/orders/' . $order->id);
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Walk-in Counter Sale');
        $showResponse->assertSee('Engr. Taiwo Adeleke');
    }

    public function test_admin_and_sales_lead_can_access_customer_import_page(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $lead = User::where('role', 'sales_manager')->first() ?? User::factory()->create(['role' => 'sales_manager']);

        $responseAdmin = $this->actingAs($admin)->get('/customers/import');
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('Import Customers from Excel / CSV Sheet');

        $responseLead = $this->actingAs($lead)->get('/customers/import');
        $responseLead->assertStatus(200);
        $responseLead->assertSee('Import Customers from Excel / CSV Sheet');
    }

    public function test_field_agent_cannot_access_customer_import_page(): void
    {
        $agent = User::where('role', 'field_agent')->first() ?? User::factory()->create(['role' => 'field_agent']);

        $response = $this->actingAs($agent)->get('/customers/import');
        $response->assertStatus(403);
    }

    public function test_download_sample_customer_import_template_csv(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/customers/template/download');
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename="reliable_customers_import_template.csv"');
        $this->assertStringContainsString('Company / Store Name', $response->getContent());
        $this->assertStringContainsString('Primary Phone', $response->getContent());
    }

    public function test_import_customers_from_csv_spreadsheet(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $agent = User::where('role', 'field_agent')->first() ?? User::factory()->create(['role' => 'field_agent']);

        $csvContent = "Company Name,Contact Person,Phone,Category,Territory,Address,Assigned Agent Code\n" .
                      "Konga Electro Hub,Engr. Femi,08099887766,retailer,Yaba Corridor,12 Commercial Ave Yaba,{$agent->agent_code}\n" .
                      "Alaba Direct Solar,Chief Obi,08122334455,wholesaler,Alaba Market,Block 5 Alaba Market,\n";

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('test_customers.csv', $csvContent);

        $response = $this->actingAs($admin)->post('/customers/import', [
            'spreadsheet' => $file,
            'default_agent_id' => $agent->id,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/customers');

        $this->assertDatabaseHas('customers', [
            'company_name' => 'Konga Electro Hub',
            'phone' => '08099887766',
            'onboarded_by' => $agent->id,
        ]);

        $this->assertDatabaseHas('customers', [
            'company_name' => 'Alaba Direct Solar',
            'phone' => '08122334455',
            'onboarded_by' => $agent->id, // Defaulted to $agent
        ]);
    }

    public function test_admin_can_reassign_and_remove_customer_from_field_sales_rep(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $agent1 = User::where('role', 'field_agent')->first() ?? User::factory()->create(['role' => 'field_agent']);
        $agent2 = User::where('role', 'field_agent')->skip(1)->first() ?? User::factory()->create(['role' => 'field_agent', 'email' => 'agent2@reliable.ng']);

        $customer = Customer::create([
            'company_name' => 'Reassignment Test Store',
            'customer_code' => 'CUST-REASSIGN-' . time(),
            'contact_person' => 'Proprietor',
            'phone' => '08011223344',
            'category' => 'retailer',
            'territory' => 'Ikeja',
            'address' => 'Test Address',
            'city' => 'Lagos',
            'state' => 'Lagos State',
            'credit_limit' => 0,
            'payment_terms' => 'cash_on_delivery',
            'onboarded_by' => $agent1->id,
            'onboarded_date' => Carbon::now()->toDateString(),
        ]);

        $this->assertEquals($agent1->id, $customer->fresh()->onboarded_by);

        // 1. Reassign to Agent 2
        $response1 = $this->actingAs($admin)->patch("/customers/{$customer->id}/reassign", [
            'agent_id' => $agent2->id,
        ]);
        $response1->assertRedirect();
        $this->assertEquals($agent2->id, $customer->fresh()->onboarded_by);

        // 2. Remove from Agent (Move to unassigned pool)
        $response2 = $this->actingAs($admin)->patch("/customers/{$customer->id}/reassign", [
            'agent_id' => '',
        ]);
        $response2->assertRedirect();
        $this->assertNull($customer->fresh()->onboarded_by);
        $this->assertFalse($customer->fresh()->isAssigned());
    }

    public function test_admin_can_bulk_assign_customers_to_rep(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $agent = User::where('role', 'field_agent')->first() ?? User::factory()->create(['role' => 'field_agent']);

        $c1 = Customer::create([
            'company_name' => 'Bulk Store 1',
            'customer_code' => 'CUST-B1-' . time(),
            'contact_person' => 'Person 1',
            'phone' => '08011112222',
            'category' => 'retailer',
            'territory' => 'Lekki',
            'address' => 'Lekki Address 1',
            'city' => 'Lagos',
            'state' => 'Lagos State',
            'credit_limit' => 0,
            'payment_terms' => 'cash_on_delivery',
            'onboarded_by' => null,
            'onboarded_date' => Carbon::now()->toDateString(),
        ]);

        $c2 = Customer::create([
            'company_name' => 'Bulk Store 2',
            'customer_code' => 'CUST-B2-' . time(),
            'contact_person' => 'Person 2',
            'phone' => '08033334444',
            'category' => 'wholesaler',
            'territory' => 'Lekki',
            'address' => 'Lekki Address 2',
            'city' => 'Lagos',
            'state' => 'Lagos State',
            'credit_limit' => 0,
            'payment_terms' => 'cash_on_delivery',
            'onboarded_by' => null,
            'onboarded_date' => Carbon::now()->toDateString(),
        ]);

        $this->assertNull($c1->fresh()->onboarded_by);
        $this->assertNull($c2->fresh()->onboarded_by);

        // Bulk assign both to $agent
        $response = $this->actingAs($admin)->post('/customers/bulk-assign', [
            'customer_ids' => [$c1->id, $c2->id],
            'agent_id' => $agent->id,
        ]);

        $response->assertRedirect();
        $this->assertEquals($agent->id, $c1->fresh()->onboarded_by);
        $this->assertEquals($agent->id, $c2->fresh()->onboarded_by);
    }
}
