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
        $agent = User::where('role', 'field_agent')->where('status', 'active')->first()
            ?? User::factory()->create(['role' => 'field_agent', 'status' => 'active']);
        $today = Carbon::today()->toDateString();

        if (!FieldActivity::whereDate('activity_date', $today)->where('user_id', $agent->id)->exists()) {
            FieldActivity::create([
                'user_id' => $agent->id,
                'activity_date' => $today,
                'check_in_time' => '09:00:00',
                'activity_type' => 'store_visit',
                'purpose' => 'Daily tracker baseline check-in',
                'outcome' => 'prospect_interested',
            ]);
        }

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
        $customer = Customer::where('onboarded_by', $agent->id)->first() ?? Customer::first();
        if ($customer->onboarded_by !== $agent->id) {
            $customer->update(['onboarded_by' => $agent->id]);
        }

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
        $customer = Customer::where('onboarded_by', $agent->id)->first() ?? Customer::first();
        if ($customer->onboarded_by !== $agent->id) {
            $customer->update(['onboarded_by' => $agent->id]);
        }
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

    public function test_field_executive_only_sees_their_own_customers_and_cannot_view_others(): void
    {
        $agentA = User::where('role', 'field_agent')->first() ?? User::factory()->create(['role' => 'field_agent']);
        $agentB = User::factory()->create(['role' => 'field_agent', 'email' => 'agentB_' . time() . '@test.ng']);

        $customerA = Customer::create([
            'company_name' => 'Agent A Exclusive Retailer ' . time(),
            'customer_code' => 'CUST-A-' . time(),
            'contact_person' => 'Proprietor A',
            'phone' => '08012341234',
            'category' => 'retailer',
            'territory' => 'Ikeja',
            'address' => 'Ikeja Mall Store 1',
            'city' => 'Lagos',
            'state' => 'Lagos State',
            'credit_limit' => 500000,
            'payment_terms' => 'cash_on_delivery',
            'onboarded_by' => $agentA->id,
            'onboarded_date' => Carbon::now()->toDateString(),
        ]);

        $customerB = Customer::create([
            'company_name' => 'Agent B Secret Wholesaler ' . time(),
            'customer_code' => 'CUST-B-' . time(),
            'contact_person' => 'Proprietor B',
            'phone' => '08056785678',
            'category' => 'wholesaler',
            'territory' => 'Lekki',
            'address' => 'Lekki Plaza Shop 2',
            'city' => 'Lagos',
            'state' => 'Lagos State',
            'credit_limit' => 1000000,
            'payment_terms' => 'net_14',
            'onboarded_by' => $agentB->id,
            'onboarded_date' => Carbon::now()->toDateString(),
        ]);

        // Agent A on /customers directory can only see customer A, NOT customer B
        $responseA = $this->actingAs($agentA)->get('/customers');
        $responseA->assertStatus(200);
        $responseA->assertSee($customerA->company_name);
        $responseA->assertDontSee($customerB->company_name);

        // Agent A cannot access customer B profile (403 Forbidden)
        $forbiddenResponse = $this->actingAs($agentA)->get("/customers/{$customerB->id}");
        $forbiddenResponse->assertStatus(403);

        // Agent A can access their own customer A profile (200 OK)
        $ownResponse = $this->actingAs($agentA)->get("/customers/{$customerA->id}");
        $ownResponse->assertStatus(200);
        $ownResponse->assertSee($customerA->company_name);

        // HOD & Admin can see both customer A and customer B
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $adminResponse = $this->actingAs($admin)->get('/customers');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee($customerA->company_name);
        $adminResponse->assertSee($customerB->company_name);
    }

    public function test_daily_tracker_is_scoped_for_field_executive_and_full_view_for_admin(): void
    {
        $today = Carbon::today()->toDateString();
        $agentA = User::where('role', 'field_agent')->first() ?? User::factory()->create(['role' => 'field_agent']);
        $agentB = User::factory()->create(['role' => 'field_agent', 'email' => 'agentB_act_' . time() . '@test.ng']);

        // Log check-in for Agent A and Agent B
        $actA = \App\Models\FieldActivity::create([
            'user_id' => $agentA->id,
            'activity_date' => $today,
            'check_in_time' => '09:15:00',
            'activity_type' => 'routine_sales_visit',
            'purpose' => 'Agent A Store Discussion ' . time(),
            'notes' => 'Agent A routine check',
            'outcome' => 'order_booked',
        ]);

        $actB = \App\Models\FieldActivity::create([
            'user_id' => $agentB->id,
            'activity_date' => $today,
            'check_in_time' => '10:30:00',
            'activity_type' => 'routine_sales_visit',
            'purpose' => 'Agent B Special Meeting ' . time(),
            'notes' => 'Agent B meeting notes',
            'outcome' => 'prospect_interested',
        ]);

        // Agent A visits daily tracker: only sees their own activity
        $agentResponse = $this->actingAs($agentA)->get('/activities/daily');
        $agentResponse->assertStatus(200);
        $agentResponse->assertSee($actA->purpose);
        $agentResponse->assertDontSee($actB->purpose);

        // Admin visits daily tracker: sees both activities in real time
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $adminResponse = $this->actingAs($admin)->get('/activities/daily');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee($actA->purpose);
        $adminResponse->assertSee($actB->purpose);

        // Admin filters by Agent A: only sees Agent A
        $filterResponse = $this->actingAs($admin)->get("/activities/daily?agent_id={$agentA->id}");
        $filterResponse->assertStatus(200);
        $filterResponse->assertSee($actA->purpose);
        $filterResponse->assertDontSee($actB->purpose);
    }

    public function test_field_agent_cross_office_authorization_locks(): void
    {
        $agentA = User::where('role', 'field_agent')->first() ?? User::factory()->create(['role' => 'field_agent']);
        $agentB = User::factory()->create(['role' => 'field_agent', 'email' => 'agentB_lock_' . time() . '@test.ng']);

        // Create Customer assigned to Agent B
        $customerB = Customer::create([
            'company_name' => 'Agent B Dedicated Shop ' . time(),
            'customer_code' => 'CUST-LOCK-' . time(),
            'contact_person' => 'Proprietor B',
            'phone' => '+234 809 111 2233',
            'category' => 'retailer',
            'territory' => 'Alaba Market',
            'address' => 'Alaba International Market, Lagos',
            'city' => 'Lagos',
            'state' => 'Lagos State',
            'credit_limit' => 2000000,
            'payment_terms' => 'net_7',
            'onboarded_by' => $agentB->id,
            'onboarded_date' => Carbon::today()->toDateString(),
            'verification_status' => 'verified',
            'status' => 'active',
        ]);

        // Agent A attempts to view Customer B: 403 Forbidden
        $responseShow = $this->actingAs($agentA)->get("/customers/{$customerB->id}");
        $responseShow->assertStatus(403);

        // Agent A attempts to edit Customer B: 403 Forbidden
        $responseEdit = $this->actingAs($agentA)->get("/customers/{$customerB->id}/edit");
        $responseEdit->assertStatus(403);

        // Agent A attempts to log a check-in for Customer B: 403 Forbidden
        $responseCheckin = $this->actingAs($agentA)->post('/activities/check-in', [
            'customer_id' => $customerB->id,
            'activity_date' => Carbon::today()->toDateString(),
            'check_in_time' => '11:00',
            'activity_type' => 'store_visit',
            'purpose' => 'Illegal cross-office check-in',
            'outcome' => 'prospect_interested',
        ]);
        $responseCheckin->assertStatus(403);

        // Agent A attempts to book an order for Customer B: 403 Forbidden
        $product = Product::first();
        $responseOrder = $this->actingAs($agentA)->post('/orders', [
            'order_number' => 'ORD-LOCK-' . time(),
            'customer_id' => $customerB->id,
            'order_date' => Carbon::today()->toDateString(),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'unit_price' => $product->wholesale_price,
                ],
            ],
        ]);
        $responseOrder->assertStatus(403);

        // Agent A attempts to record a payment for Customer B: 403 Forbidden
        $responsePayment = $this->actingAs($agentA)->post('/payments', [
            'payment_reference' => 'PAY-LOCK-' . time(),
            'customer_id' => $customerB->id,
            'amount' => 50000,
            'payment_date' => Carbon::today()->toDateString(),
            'payment_method' => 'bank_transfer',
        ]);
        $responsePayment->assertStatus(403);
    }

    public function test_payment_recording_accurately_sums_and_updates_order_status(): void
    {
        $agent = User::where('role', 'field_agent')->first() ?? User::factory()->create(['role' => 'field_agent']);

        $customer = Customer::create([
            'company_name' => 'Payment Test Electricals ' . time(),
            'customer_code' => 'CUST-PAY-' . time(),
            'contact_person' => 'Mr. Audu',
            'phone' => '+234 803 999 0011',
            'category' => 'wholesaler',
            'territory' => 'Ikeja',
            'address' => 'Ikeja Commercial Zone, Lagos',
            'city' => 'Lagos',
            'state' => 'Lagos State',
            'credit_limit' => 5000000,
            'payment_terms' => 'net_14',
            'onboarded_by' => $agent->id,
            'onboarded_date' => Carbon::today()->toDateString(),
            'verification_status' => 'verified',
            'status' => 'active',
        ]);

        $product = Product::first();

        // Create an order of 100,000 NGN
        $order = Order::create([
            'order_number' => 'ORD-SUM-' . time(),
            'customer_id' => $customer->id,
            'user_id' => $agent->id,
            'order_date' => Carbon::today()->toDateString(),
            'subtotal' => 100000,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'total_amount' => 100000,
            'paid_amount' => 0,
            'payment_status' => 'unpaid',
            'order_status' => 'approved',
        ]);

        // Record 1st payment of 40,000 NGN
        $pay1Response = $this->actingAs($agent)->post('/payments', [
            'payment_reference' => 'PAY-SUM-1-' . time(),
            'customer_id' => $customer->id,
            'order_id' => $order->id,
            'amount' => 40000,
            'payment_date' => Carbon::today()->toDateString(),
            'payment_method' => 'bank_transfer',
            'reference_note' => 'Part payment 1',
        ]);
        $pay1Response->assertRedirect('/payments');

        $order->refresh();
        $this->assertEquals(40000, (float)$order->paid_amount);
        $this->assertEquals('partially_paid', $order->payment_status);

        // Record 2nd payment of 60,000 NGN (completes total)
        $pay2Response = $this->actingAs($agent)->post('/payments', [
            'payment_reference' => 'PAY-SUM-2-' . time(),
            'customer_id' => $customer->id,
            'order_id' => $order->id,
            'amount' => 60000,
            'payment_date' => Carbon::today()->toDateString(),
            'payment_method' => 'pos_card',
            'reference_note' => 'Final payment 2',
        ]);
        $pay2Response->assertRedirect('/payments');

        $order->refresh();
        $this->assertEquals(100000, (float)$order->paid_amount);
        $this->assertEquals('fully_paid', $order->payment_status);
    }

    public function test_dashboard_and_activity_report_include_todays_records_inclusively(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $agent = User::where('role', 'field_agent')->where('status', 'active')->first()
            ?? User::factory()->create(['role' => 'field_agent', 'status' => 'active']);
        $today = Carbon::today()->toDateString();

        $purpose = 'Inclusive End-Date Audit Visit ' . uniqid();
        FieldActivity::create([
            'user_id' => $agent->id,
            'activity_date' => $today,
            'check_in_time' => '10:15:00',
            'activity_type' => 'store_visit',
            'purpose' => $purpose,
            'outcome' => 'order_taken',
        ]);

        // Dashboard shows non-zero today visits
        $dashAdmin = $this->actingAs($admin)->get('/dashboard');
        $dashAdmin->assertStatus(200);
        $dashAdmin->assertSee('GPS Verified');

        // Agent dashboard includes today's check-in
        $dashAgent = $this->actingAs($agent)->get('/dashboard');
        $dashAgent->assertStatus(200);
        $dashAgent->assertSee($purpose);

        // Activity report ending today includes today's check-in
        $report = $this->actingAs($admin)->get('/activities/report?start_date=' . Carbon::today()->startOfMonth()->toDateString() . '&end_date=' . $today);
        $report->assertStatus(200);
        $report->assertSee($purpose);
    }
}
