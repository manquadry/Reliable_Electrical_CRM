<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add field agent & CRM columns to users
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('field_agent')->after('email'); // admin, sales_manager, field_agent
            $table->string('agent_code')->nullable()->unique()->after('role');
            $table->string('phone')->nullable()->after('agent_code');
            $table->string('territory')->nullable()->after('phone');
            $table->string('vehicle_type')->nullable()->after('territory');
            $table->decimal('monthly_revenue_target', 14, 2)->default(5000000.00)->after('vehicle_type');
            $table->integer('daily_visit_target')->default(6)->after('monthly_revenue_target');
            $table->integer('monthly_onboard_target')->default(15)->after('daily_visit_target');
            $table->date('onboarded_date')->nullable()->after('monthly_onboard_target');
            $table->foreignId('onboarded_by_admin_id')->nullable()->after('onboarded_date')->constrained('users')->nullOnDelete();
            $table->string('status')->default('active')->after('onboarded_by_admin_id'); // active, inactive, suspended
            $table->string('avatar')->nullable()->after('status');
            $table->text('notes')->nullable()->after('avatar');
        });

        // Customers Table
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('customer_code')->unique();
            $table->string('contact_person');
            $table->string('phone');
            $table->string('alt_phone')->nullable();
            $table->string('email')->nullable();
            $table->string('category')->default('retailer'); // retailer, wholesaler, electrical_contractor, lighting_showroom, supermarket, real_estate_developer
            $table->string('territory'); // e.g. Ikeja, Gbagada, Alaba, Lekki, Victoria Island
            $table->text('address');
            $table->string('city')->default('Lagos');
            $table->string('state')->default('Lagos State');
            $table->string('landmark')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('credit_limit', 14, 2)->default(0.00);
            $table->string('payment_terms')->default('cash_on_delivery'); // cash_on_delivery, net_7, net_14, net_30
            $table->foreignId('onboarded_by')->constrained('users')->cascadeOnDelete();
            $table->date('onboarded_date'); // The day they are onboarded
            $table->string('verification_status')->default('verified'); // verified, pending, rejected
            $table->string('status')->default('active'); // active, inactive, blacklisted
            $table->string('storefront_image')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Products Catalog (Electrical appliances, lighting, switchgear, cables)
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->string('category'); // lighting, switchgear_protection, cables_wiring, solar_power, home_appliances, switches_sockets
            $table->text('description')->nullable();
            $table->decimal('unit_price', 14, 2); // Retail price
            $table->decimal('wholesale_price', 14, 2); // Bulk/wholesaler price
            $table->integer('minimum_order_quantity')->default(1);
            $table->integer('stock_quantity')->default(100);
            $table->string('unit_measure')->default('pcs'); // pcs, carton, roll, set
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Field Agent Daily Activities & Check-ins
        Schema::create('field_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Field agent
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->date('activity_date')->index(); // Date of activity
            $table->time('check_in_time');
            $table->time('check_out_time')->nullable();
            $table->string('activity_type'); // store_visit, new_customer_onboarding, order_booking, payment_collection, product_demo, complaint_resolution
            $table->string('purpose');
            $table->text('notes')->nullable();
            $table->string('outcome'); // order_taken, payment_received, followup_scheduled, prospect_interested, not_interested, closed
            $table->date('next_followup_date')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('location_address')->nullable();
            $table->timestamps();
        });

        // Sales Orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Field agent who took the order
            $table->date('order_date');
            $table->decimal('subtotal', 14, 2);
            $table->decimal('discount_amount', 14, 2)->default(0.00);
            $table->decimal('tax_amount', 14, 2)->default(0.00);
            $table->decimal('total_amount', 14, 2);
            $table->decimal('paid_amount', 14, 2)->default(0.00);
            $table->string('payment_status')->default('unpaid'); // unpaid, partially_paid, fully_paid
            $table->string('order_status')->default('pending_approval'); // draft, pending_approval, approved, dispatched, delivered, cancelled
            $table->text('delivery_address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Order Line Items
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price', 14, 2);
            $table->decimal('total_price', 14, 2);
            $table->timestamps();
        });

        // Payments & Collections
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_reference')->unique();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Field agent who collected
            $table->decimal('amount', 14, 2);
            $table->date('payment_date');
            $table->string('payment_method')->default('bank_transfer'); // bank_transfer, pos_card, cash, cheque
            $table->string('reference_note')->nullable(); // Bank ref or cheque number
            $table->string('status')->default('confirmed'); // confirmed, pending_verification
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('field_activities');
        Schema::dropIfExists('products');
        Schema::dropIfExists('customers');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['onboarded_by_admin_id']);
            $table->dropColumn([
                'role',
                'agent_code',
                'phone',
                'territory',
                'vehicle_type',
                'monthly_revenue_target',
                'daily_visit_target',
                'monthly_onboard_target',
                'onboarded_date',
                'onboarded_by_admin_id',
                'status',
                'avatar',
                'notes',
            ]);
        });
    }
};
