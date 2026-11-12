<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\FieldActivity;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CrmDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admins and Field Sales Executives
        $admin = User::firstOrCreate(
            ['email' => 'admin@reliableelectrics.ng'],
            [
                'name' => 'Engr. Adekunle Lawal',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'agent_code' => 'HQ-ADM-01',
                'phone' => '+234 803 555 0192',
                'territory' => 'Headquarters - Lagos Operations',
                'vehicle_type' => 'Official SUV',
                'monthly_revenue_target' => 50000000.00,
                'daily_visit_target' => 0,
                'monthly_onboard_target' => 0,
                'onboarded_date' => Carbon::now()->subMonths(12)->toDateString(),
                'status' => 'active',
                'notes' => 'Managing Director & National Distribution Head at Reliable Electrics Nigeria Limited.',
            ]
        );

        $agent1 = User::firstOrCreate(
            ['email' => 'chinedu@reliableelectrics.ng'],
            [
                'name' => 'Chinedu Okeke',
                'password' => Hash::make('password'),
                'role' => 'field_agent',
                'agent_code' => 'REL-FE-101',
                'phone' => '+234 802 334 1188',
                'territory' => 'Ikeja, Alausa & Oregun Commercial Hub',
                'vehicle_type' => 'Company Motorbike (Yamaha Crux)',
                'monthly_revenue_target' => 7500000.00,
                'daily_visit_target' => 7,
                'monthly_onboard_target' => 15,
                'onboarded_date' => Carbon::now()->subMonths(6)->toDateString(),
                'onboarded_by_admin_id' => $admin->id,
                'status' => 'active',
                'notes' => 'Senior Field Executive specializing in commercial contractors and high-street electronics retailers.',
            ]
        );

        $agent2 = User::firstOrCreate(
            ['email' => 'fatima@reliableelectrics.ng'],
            [
                'name' => 'Fatima Bello',
                'password' => Hash::make('password'),
                'role' => 'field_agent',
                'agent_code' => 'REL-FE-102',
                'phone' => '+234 814 990 4422',
                'territory' => 'Lekki Phase 1, Victoria Island & Ikoyi Corridor',
                'vehicle_type' => 'Field Sedan (Toyota Corolla)',
                'monthly_revenue_target' => 10000000.00,
                'daily_visit_target' => 6,
                'monthly_onboard_target' => 12,
                'onboarded_date' => Carbon::now()->subMonths(4)->toDateString(),
                'onboarded_by_admin_id' => $admin->id,
                'status' => 'active',
                'notes' => 'Handles premium lighting showrooms, interior architecture firms, and luxury estate developers.',
            ]
        );

        $agent3 = User::firstOrCreate(
            ['email' => 'emeka@reliableelectrics.ng'],
            [
                'name' => 'Emeka Nnamdi',
                'password' => Hash::make('password'),
                'role' => 'field_agent',
                'agent_code' => 'REL-FE-103',
                'phone' => '+234 703 112 8844',
                'territory' => 'Alaba International Market & Trade Fair Complex',
                'vehicle_type' => 'Company Motorbike (Bajaj Boxer)',
                'monthly_revenue_target' => 15000000.00,
                'daily_visit_target' => 9,
                'monthly_onboard_target' => 22,
                'onboarded_date' => Carbon::now()->subMonths(8)->toDateString(),
                'onboarded_by_admin_id' => $admin->id,
                'status' => 'active',
                'notes' => 'Wholesale distribution ace with high volume turnover in West Africa electronics trade hubs.',
            ]
        );

        $agent4 = User::firstOrCreate(
            ['email' => 'tunde@reliableelectrics.ng'],
            [
                'name' => 'Tunde Balogun',
                'password' => Hash::make('password'),
                'role' => 'field_agent',
                'agent_code' => 'REL-FE-104',
                'phone' => '+234 809 667 3355',
                'territory' => 'Gbagada, Ogudu, Anthony & Shomolu Zone',
                'vehicle_type' => 'Company Motorbike (Suzuki TF125)',
                'monthly_revenue_target' => 6000000.00,
                'daily_visit_target' => 6,
                'monthly_onboard_target' => 14,
                'onboarded_date' => Carbon::now()->subMonths(2)->toDateString(),
                'onboarded_by_admin_id' => $admin->id,
                'status' => 'active',
                'notes' => 'Territory executive stationed near our Gbagada headquarters covering Mainland builders and retail hubs.',
            ]
        );

        // 2. Create Products Catalog
        $productsData = [
            [
                'name' => 'Reliable 50W LED Commercial Floodlight IP66',
                'sku' => 'REL-FL-050W-IP66',
                'category' => 'lighting',
                'description' => 'Heavy-duty die-cast aluminum LED floodlight, 6500K daylight, 5500 lumens, surge protection 4kV.',
                'unit_price' => 28500.00,
                'wholesale_price' => 22000.00,
                'minimum_order_quantity' => 5,
                'stock_quantity' => 340,
                'unit_measure' => 'pcs',
                'is_active' => true,
            ],
            [
                'name' => 'Reliable 100W Heavy Duty Stadium/Compound Floodlight',
                'sku' => 'REL-FL-100W-HD',
                'category' => 'lighting',
                'description' => '11,000 lumens high efficiency lighting fixture with tempered glass lens, IP67 waterproof rating.',
                'unit_price' => 49500.00,
                'wholesale_price' => 39500.00,
                'minimum_order_quantity' => 4,
                'stock_quantity' => 210,
                'unit_measure' => 'pcs',
                'is_active' => true,
            ],
            [
                'name' => '12-Light Nordic Modern Ring Chandelier (Gold Brass)',
                'sku' => 'REL-CH-ND12G',
                'category' => 'lighting',
                'description' => 'Luxury dimmable LED ring pendant chandelier with remote & app control for residential living and hotel lobbies.',
                'unit_price' => 285000.00,
                'wholesale_price' => 225000.00,
                'minimum_order_quantity' => 1,
                'stock_quantity' => 38,
                'unit_measure' => 'set',
                'is_active' => true,
            ],
            [
                'name' => 'Reliable 60x60 Backlit LED Ceiling Panel 48W (Warm/Cool)',
                'sku' => 'REL-PN-6060-48W',
                'category' => 'lighting',
                'description' => 'Ultra-thin architectural recessed panel light for modern offices and hospitals. Flicker-free driver.',
                'unit_price' => 18500.00,
                'wholesale_price' => 14500.00,
                'minimum_order_quantity' => 10,
                'stock_quantity' => 620,
                'unit_measure' => 'pcs',
                'is_active' => true,
            ],
            [
                'name' => 'Schneider Type 63A Double Pole Automatic Changeover Switch',
                'sku' => 'REL-SW-DP63A-ATS',
                'category' => 'switchgear_protection',
                'description' => 'DIN rail mount generator-to-mains changeover switch with LED phase indicator and fireproof casing.',
                'unit_price' => 42000.00,
                'wholesale_price' => 33500.00,
                'minimum_order_quantity' => 5,
                'stock_quantity' => 180,
                'unit_measure' => 'pcs',
                'is_active' => true,
            ],
            [
                'name' => 'Reliable 8-Way Metal Enclosed Consumer Distribution Board',
                'sku' => 'REL-DB-08WM',
                'category' => 'switchgear_protection',
                'description' => 'Flush/surface metal DB with 100A main isolator, neutral & earth bars, IP40 certified.',
                'unit_price' => 34500.00,
                'wholesale_price' => 27000.00,
                'minimum_order_quantity' => 3,
                'stock_quantity' => 145,
                'unit_measure' => 'pcs',
                'is_active' => true,
            ],
            [
                'name' => 'Coleman Spec 2.5mm² Pure Copper Single Core Cable (100m Roll)',
                'sku' => 'REL-CB-25MM-CU100',
                'category' => 'cables_wiring',
                'description' => 'SON certified 100% pure electrolytic annealed copper wire, flame retardant PVC insulation for domestic conduit wiring.',
                'unit_price' => 48500.00,
                'wholesale_price' => 39500.00,
                'minimum_order_quantity' => 5,
                'stock_quantity' => 450,
                'unit_measure' => 'roll',
                'is_active' => true,
            ],
            [
                'name' => 'Pure Copper 4.0mm² Twin & Earth Surface Cable (100m Roll)',
                'sku' => 'REL-CB-40MM-TE100',
                'category' => 'cables_wiring',
                'description' => 'Heavy duty air conditioner and cooker circuit wiring cable with bare copper earth wire.',
                'unit_price' => 92000.00,
                'wholesale_price' => 76500.00,
                'minimum_order_quantity' => 3,
                'stock_quantity' => 280,
                'unit_measure' => 'roll',
                'is_active' => true,
            ],
            [
                'name' => 'Reliable 300W All-In-One Solar Street Light (Radar Sensor)',
                'sku' => 'REL-SL-300W-AIO',
                'category' => 'solar_power',
                'description' => 'Integrated LiFePO4 battery, monocrystalline solar panel, dusk-to-dawn remote controller with mounting arm.',
                'unit_price' => 68000.00,
                'wholesale_price' => 54000.00,
                'minimum_order_quantity' => 2,
                'stock_quantity' => 165,
                'unit_measure' => 'pcs',
                'is_active' => true,
            ],
            [
                'name' => 'Reliable 16-Inch Rechargeable Solar/AC Mist Fan with Remote',
                'sku' => 'REL-FAN-16MST-RCH',
                'category' => 'home_appliances',
                'description' => '12V 4.5Ah rechargeable battery with 8 hours runtime, 2.5L water mist humidifier, USB phone charger port.',
                'unit_price' => 74500.00,
                'wholesale_price' => 59000.00,
                'minimum_order_quantity' => 3,
                'stock_quantity' => 110,
                'unit_measure' => 'pcs',
                'is_active' => true,
            ],
            [
                'name' => '13A British Standard Twin Socket with Dual Fast USB Ports',
                'sku' => 'REL-SK-13A2USB-BS',
                'category' => 'switches_sockets',
                'description' => 'Brushed metallic finish flush double socket with Type-A & Type-C 20W fast power delivery charger.',
                'unit_price' => 7800.00,
                'wholesale_price' => 5900.00,
                'minimum_order_quantity' => 10,
                'stock_quantity' => 890,
                'unit_measure' => 'pcs',
                'is_active' => true,
            ],
            [
                'name' => 'Smart Tuya Wi-Fi 4-Gang Touch Light Switch (Tempered Glass)',
                'sku' => 'REL-SW-TY4G-WIFI',
                'category' => 'switches_sockets',
                'description' => 'Capacitive touch switch compatible with Google Assistant, Alexa, and Tuya Smart Life smartphone app.',
                'unit_price' => 16500.00,
                'wholesale_price' => 12800.00,
                'minimum_order_quantity' => 5,
                'stock_quantity' => 230,
                'unit_measure' => 'pcs',
                'is_active' => true,
            ],
        ];

        $createdProducts = [];
        foreach ($productsData as $pData) {
            $createdProducts[] = Product::firstOrCreate(['sku' => $pData['sku']], $pData);
        }

        // 3. Create Customers Onboarded by Field Agents
        $customersData = [
            [
                'company_name' => 'Apex Mega Lighting Showroom',
                'customer_code' => 'CUST-VI-001',
                'contact_person' => 'Chief Kenneth Nwachukwu',
                'phone' => '+234 803 445 2291',
                'alt_phone' => '+234 818 902 1100',
                'email' => 'apexlighting.vi@gmail.com',
                'category' => 'lighting_showroom',
                'territory' => 'Victoria Island & Lekki Corridor',
                'address' => 'Plot 14B Adetokunbo Ademola Street, Victoria Island',
                'city' => 'Lagos',
                'state' => 'Lagos State',
                'landmark' => 'Near Eko Hotel Roundabout',
                'latitude' => 6.4281,
                'longitude' => 3.4219,
                'credit_limit' => 10000000.00,
                'payment_terms' => 'net_14',
                'onboarded_by' => $agent2->id,
                'onboarded_date' => Carbon::now()->subDays(45)->toDateString(),
                'verification_status' => 'verified',
                'status' => 'active',
                'notes' => 'High-end lighting distributor supplying luxury high-rise penthouses and hotels in Victoria Island.',
            ],
            [
                'company_name' => 'De-Royal Electricals & Construction Supplies',
                'customer_code' => 'CUST-IKJ-002',
                'contact_person' => 'Alhaji Rasheed Adeleke',
                'phone' => '+234 802 887 6655',
                'alt_phone' => '+234 805 112 3344',
                'email' => 'deroyal.elect@yahoo.com',
                'category' => 'electrical_contractor',
                'territory' => 'Ikeja, Alausa & Oregun Commercial Hub',
                'address' => '42 Allen Avenue, Ikeja',
                'city' => 'Ikeja',
                'state' => 'Lagos State',
                'landmark' => 'Opposite Oshopey Plaza',
                'latitude' => 6.5997,
                'longitude' => 3.3533,
                'credit_limit' => 5000000.00,
                'payment_terms' => 'net_7',
                'onboarded_by' => $agent1->id,
                'onboarded_date' => Carbon::now()->subDays(30)->toDateString(),
                'verification_status' => 'verified',
                'status' => 'active',
                'notes' => 'Key commercial contractor executing MEP installations for government secretariats and bank branches.',
            ],
            [
                'company_name' => 'Alaba International Switchgear Hub',
                'customer_code' => 'CUST-ALB-003',
                'contact_person' => 'Mazi Sunday Okafor',
                'phone' => '+234 812 776 5432',
                'alt_phone' => '+234 901 889 0011',
                'email' => 'okafor.switchgear@gmail.com',
                'category' => 'wholesaler',
                'territory' => 'Alaba International Market & Trade Fair Complex',
                'address' => 'Line 4, Shop 28 Electronics Section, Alaba International Market, Ojo',
                'city' => 'Ojo',
                'state' => 'Lagos State',
                'landmark' => 'Gate 2 Entrance, First Bank Axis',
                'latitude' => 6.4608,
                'longitude' => 3.1912,
                'credit_limit' => 20000000.00,
                'payment_terms' => 'cash_on_delivery',
                'onboarded_by' => $agent3->id,
                'onboarded_date' => Carbon::now()->subDays(60)->toDateString(),
                'verification_status' => 'verified',
                'status' => 'active',
                'notes' => 'Major market leader in Alaba distributing switchgears, distribution boxes and heavy cables across Eastern Nigeria.',
            ],
            [
                'company_name' => 'Gbagada Power Light & Wiring Depot',
                'customer_code' => 'CUST-GBG-004',
                'contact_person' => 'Mrs. Folake Salami',
                'phone' => '+234 809 333 7812',
                'alt_phone' => null,
                'email' => 'gbagada.power@gmail.com',
                'category' => 'retailer',
                'territory' => 'Gbagada, Ogudu, Anthony & Shomolu Zone',
                'address' => '18 Diya Street, Ifako-Gbagada',
                'city' => 'Gbagada',
                'state' => 'Lagos State',
                'landmark' => 'Near Gbagada General Hospital Junction',
                'latitude' => 6.5542,
                'longitude' => 3.3884,
                'credit_limit' => 2500000.00,
                'payment_terms' => 'net_7',
                'onboarded_by' => $agent4->id,
                'onboarded_date' => Carbon::now()->subDays(14)->toDateString(),
                'verification_status' => 'verified',
                'status' => 'active',
                'notes' => 'Retail shop with fast turnaround on residential LED lights, extension boxes and domestic conduit cables.',
            ],
            [
                'company_name' => 'Lekki Peninsula Real Estate Buildmart',
                'customer_code' => 'CUST-LKK-005',
                'contact_person' => 'Arch. Femi Babalola',
                'phone' => '+234 805 444 9900',
                'alt_phone' => '+234 802 121 2121',
                'email' => 'femi.babalola@peninsulabuild.ng',
                'category' => 'real_estate_developer',
                'territory' => 'Victoria Island & Lekki Corridor',
                'address' => 'Kilometer 14 Lekki-Epe Expressway, Ikate Elegushi',
                'city' => 'Lekki',
                'state' => 'Lagos State',
                'landmark' => 'Beside World Oil Filling Station',
                'latitude' => 6.4474,
                'longitude' => 3.4912,
                'credit_limit' => 15000000.00,
                'payment_terms' => 'net_30',
                'onboarded_by' => $agent2->id,
                'onboarded_date' => Carbon::now()->subDays(7)->toDateString(),
                'verification_status' => 'verified',
                'status' => 'active',
                'notes' => 'Large estate developer currently building 60 units of luxury terrace duplexes in Ikate and Chevron Drive.',
            ],
            [
                'company_name' => 'Oregun Industrial Spares & Appliances Hub',
                'customer_code' => 'CUST-ORG-006',
                'contact_person' => 'Engr. Sylvester Umeh',
                'phone' => '+234 803 765 1902',
                'alt_phone' => null,
                'email' => 'oregun.spares@yahoo.com',
                'category' => 'wholesaler',
                'territory' => 'Ikeja, Alausa & Oregun Commercial Hub',
                'address' => '85 Kudirat Abiola Way, Oregun, Ikeja',
                'city' => 'Ikeja',
                'state' => 'Lagos State',
                'landmark' => 'Close to Daystar Christian Centre',
                'latitude' => 6.6110,
                'longitude' => 3.3644,
                'credit_limit' => 8000000.00,
                'payment_terms' => 'net_14',
                'onboarded_by' => $agent1->id,
                'onboarded_date' => Carbon::now()->subDays(2)->toDateString(),
                'verification_status' => 'verified',
                'status' => 'active',
                'notes' => 'Bulk buyer of industrial breaker boxes, automatic changeovers and outdoor compound floodlights.',
            ],
            [
                'company_name' => 'Ogudu GRA Electrical Solutions Ltd',
                'customer_code' => 'CUST-OGD-007',
                'contact_person' => 'Pastor Gbenga Akintola',
                'phone' => '+234 816 889 4433',
                'alt_phone' => null,
                'email' => 'ogudu.electricals@gmail.com',
                'category' => 'retailer',
                'territory' => 'Gbagada, Ogudu, Anthony & Shomolu Zone',
                'address' => 'Plot 5 Ogudu Road, Opposite Ogudu Mall',
                'city' => 'Ogudu',
                'state' => 'Lagos State',
                'landmark' => 'Opposite Ogudu Mall Entrance',
                'latitude' => 6.5780,
                'longitude' => 3.3912,
                'credit_limit' => 3000000.00,
                'payment_terms' => 'net_7',
                'onboarded_by' => $agent4->id,
                'onboarded_date' => Carbon::now()->toDateString(), // Onboarded TODAY!
                'verification_status' => 'verified',
                'status' => 'active',
                'notes' => 'New client onboarded today during morning route! Interested in rechargeable mist fans and smart Wi-Fi touch switches.',
            ],
            [
                'company_name' => 'Trade Fair Central Electronics Co.',
                'customer_code' => 'CUST-TFC-008',
                'contact_person' => 'Chief Innocent Eze',
                'phone' => '+234 803 999 1234',
                'alt_phone' => '+234 808 777 6543',
                'email' => 'innocent.eze@tradefair.ng',
                'category' => 'wholesaler',
                'territory' => 'Alaba International Market & Trade Fair Complex',
                'address' => 'B-Block Suite 10, ASPAMDA Plaza, Trade Fair Complex, Badagry Expressway',
                'city' => 'Trade Fair',
                'state' => 'Lagos State',
                'landmark' => 'ASPAMDA Trade Fair Gate 1',
                'latitude' => 6.4520,
                'longitude' => 3.2350,
                'credit_limit' => 25000000.00,
                'payment_terms' => 'cash_on_delivery',
                'onboarded_by' => $agent3->id,
                'onboarded_date' => Carbon::now()->toDateString(), // Onboarded TODAY!
                'verification_status' => 'verified',
                'status' => 'active',
                'notes' => 'Major importer-distributor onboarded today! Requested catalog pricing for full container loads of solar streetlights.',
            ],
        ];

        $createdCustomers = [];
        foreach ($customersData as $cData) {
            $createdCustomers[] = Customer::firstOrCreate(['customer_code' => $cData['customer_code']], $cData);
        }

        // 4. Create Historical and Today Field Activities for each Field Agent
        $today = Carbon::now()->toDateString();
        $yesterday = Carbon::now()->subDay()->toDateString();
        $twoDaysAgo = Carbon::now()->subDays(2)->toDateString();
        $threeDaysAgo = Carbon::now()->subDays(3)->toDateString();

        $activities = [
            // TODAY: Agent 1 (Chinedu Okeke) in Ikeja & Oregun
            [
                'user_id' => $agent1->id,
                'customer_id' => $createdCustomers[1]->id, // De-Royal
                'activity_date' => $today,
                'check_in_time' => '09:15:00',
                'check_out_time' => '10:05:00',
                'activity_type' => 'store_visit',
                'purpose' => 'Routine weekly inventory audit & take replenishment order for building project.',
                'notes' => 'Alhaji Adeleke confirmed project mobilization for 4-story commercial building on Allen Ave. Client requires 25 rolls of 2.5mm and 4.0mm cables.',
                'outcome' => 'order_taken',
                'next_followup_date' => Carbon::now()->addDays(5)->toDateString(),
                'latitude' => 6.5997,
                'longitude' => 3.3533,
                'location_address' => '42 Allen Avenue, Ikeja, Lagos',
            ],
            [
                'user_id' => $agent1->id,
                'customer_id' => $createdCustomers[5]->id, // Oregun Spares
                'activity_date' => $today,
                'check_in_time' => '11:20:00',
                'check_out_time' => '12:10:00',
                'activity_type' => 'payment_collection',
                'purpose' => 'Collect outstanding balance for previous shipment of changeover switches.',
                'notes' => 'Met with finance controller. Received bank draft of ₦650,000 for invoice #INV-882. Uploaded receipt confirmation.',
                'outcome' => 'payment_received',
                'next_followup_date' => Carbon::now()->addDays(7)->toDateString(),
                'latitude' => 6.6110,
                'longitude' => 3.3644,
                'location_address' => '85 Kudirat Abiola Way, Oregun, Ikeja',
            ],
            [
                'user_id' => $agent1->id,
                'customer_id' => null,
                'activity_date' => $today,
                'check_in_time' => '14:00:00',
                'check_out_time' => '14:45:00',
                'activity_type' => 'product_demo',
                'purpose' => 'Prospecting new electrical retail complex at Computer Village / Awolowo Way.',
                'notes' => 'Demonstrated the Tuya Wi-Fi Smart 4-Gang Touch Switch to 3 shop proprietors. Left brochures and wholesale price lists. High interest shown.',
                'outcome' => 'prospect_interested',
                'next_followup_date' => Carbon::now()->addDays(3)->toDateString(),
                'latitude' => 6.5962,
                'longitude' => 3.3421,
                'location_address' => 'Awolowo Way, Ikeja Commercial Hub',
            ],

            // TODAY: Agent 2 (Fatima Bello) in Lekki & VI
            [
                'user_id' => $agent2->id,
                'customer_id' => $createdCustomers[0]->id, // Apex Mega
                'activity_date' => $today,
                'check_in_time' => '10:00:00',
                'check_out_time' => '11:15:00',
                'activity_type' => 'store_visit',
                'purpose' => 'Present newly arrived Nordic Ring Luxury Chandeliers to Chief Kenneth.',
                'notes' => 'Chief Kenneth reviewed samples and placed booking for 4 sets of 12-Light Nordic Modern Ring Chandelier for an ongoing Lekki Phase 1 mansion contract.',
                'outcome' => 'order_taken',
                'next_followup_date' => Carbon::now()->addDays(4)->toDateString(),
                'latitude' => 6.4281,
                'longitude' => 3.4219,
                'location_address' => 'Plot 14B Adetokunbo Ademola St, Victoria Island',
            ],
            [
                'user_id' => $agent2->id,
                'customer_id' => $createdCustomers[4]->id, // Lekki Peninsula
                'activity_date' => $today,
                'check_in_time' => '12:30:00',
                'check_out_time' => '13:30:00',
                'activity_type' => 'store_visit',
                'purpose' => 'Site meeting with site engineers on lighting layout specification.',
                'notes' => 'Inspected sample unit at Ikate site. Builder approved 60x60 Backlit LED panels for all office blocks. Requested formal quotation with delivery schedule.',
                'outcome' => 'followup_scheduled',
                'next_followup_date' => Carbon::now()->addDays(2)->toDateString(),
                'latitude' => 6.4474,
                'longitude' => 3.4912,
                'location_address' => 'Km 14 Lekki-Epe Expressway, Ikate Elegushi',
            ],

            // TODAY: Agent 3 (Emeka Nnamdi) in Alaba & Trade Fair
            [
                'user_id' => $agent3->id,
                'customer_id' => $createdCustomers[7]->id, // Trade Fair Central
                'activity_date' => $today,
                'check_in_time' => '08:45:00',
                'check_out_time' => '10:15:00',
                'activity_type' => 'new_customer_onboarding',
                'purpose' => 'Complete onboarding documentation and physical shop verification for Chief Innocent Eze.',
                'notes' => 'Verified ASPAMDA Trade Fair premises. Collected business card and CAC registration details. Client verified and onboarded into CRM today.',
                'outcome' => 'order_taken',
                'next_followup_date' => Carbon::now()->addDays(4)->toDateString(),
                'latitude' => 6.4520,
                'longitude' => 3.2350,
                'location_address' => 'ASPAMDA Plaza, Trade Fair Complex, Badagry Exp.',
            ],
            [
                'user_id' => $agent3->id,
                'customer_id' => $createdCustomers[2]->id, // Alaba Hub
                'activity_date' => $today,
                'check_in_time' => '11:00:00',
                'check_out_time' => '12:45:00',
                'activity_type' => 'order_booking',
                'purpose' => 'High-volume monthly bulk order booking for distribution boards & floodlights.',
                'notes' => 'Mazi Sunday placed substantial bulk restocking order for 40 units of 100W floodlights and 50 units of 8-way metal distribution boards.',
                'outcome' => 'order_taken',
                'next_followup_date' => Carbon::now()->addDays(7)->toDateString(),
                'latitude' => 6.4608,
                'longitude' => 3.1912,
                'location_address' => 'Electronics Section, Alaba International Market',
            ],

            // TODAY: Agent 4 (Tunde Balogun) in Gbagada & Ogudu
            [
                'user_id' => $agent4->id,
                'customer_id' => $createdCustomers[6]->id, // Ogudu GRA
                'activity_date' => $today,
                'check_in_time' => '09:30:00',
                'check_out_time' => '10:45:00',
                'activity_type' => 'new_customer_onboarding',
                'purpose' => 'Onboarding new retail outlet in Ogudu GRA corridor.',
                'notes' => 'Onboarded Ogudu GRA Electrical Solutions Ltd today. Took opening trial order for rechargeable mist fans and 13A dual USB sockets.',
                'outcome' => 'order_taken',
                'next_followup_date' => Carbon::now()->addDays(3)->toDateString(),
                'latitude' => 6.5780,
                'longitude' => 3.3912,
                'location_address' => 'Plot 5 Ogudu Road, Opposite Ogudu Mall',
            ],
            [
                'user_id' => $agent4->id,
                'customer_id' => $createdCustomers[3]->id, // Gbagada Power
                'activity_date' => $today,
                'check_in_time' => '11:30:00',
                'check_out_time' => '12:15:00',
                'activity_type' => 'payment_collection',
                'purpose' => 'Collect weekly cash proceeds and delivery receipt sign-off.',
                'notes' => 'Mrs. Salami transferred ₦280,000 for previous cable supply. Verified payment alert.',
                'outcome' => 'payment_received',
                'next_followup_date' => Carbon::now()->addDays(7)->toDateString(),
                'latitude' => 6.5542,
                'longitude' => 3.3884,
                'location_address' => '18 Diya Street, Ifako-Gbagada, Lagos',
            ],

            // YESTERDAY ACTIVITIES:
            [
                'user_id' => $agent1->id,
                'customer_id' => $createdCustomers[1]->id,
                'activity_date' => $yesterday,
                'check_in_time' => '10:00:00',
                'check_out_time' => '10:50:00',
                'activity_type' => 'store_visit',
                'purpose' => 'Quarterly account review and introduction of solar streetlight line.',
                'notes' => 'Discussed supplying 300W all-in-one solar lights for state housing estate project.',
                'outcome' => 'prospect_interested',
                'next_followup_date' => $today,
                'latitude' => 6.5997,
                'longitude' => 3.3533,
                'location_address' => '42 Allen Avenue, Ikeja',
            ],
            [
                'user_id' => $agent2->id,
                'customer_id' => $createdCustomers[0]->id,
                'activity_date' => $yesterday,
                'check_in_time' => '14:00:00',
                'check_out_time' => '15:10:00',
                'activity_type' => 'store_visit',
                'purpose' => 'Showroom display merchandising check.',
                'notes' => 'Re-arranged Reliable Electrics spotlight stand in the front showroom. Replaced burnt demo bulb.',
                'outcome' => 'closed',
                'next_followup_date' => Carbon::now()->addDays(6)->toDateString(),
                'latitude' => 6.4281,
                'longitude' => 3.4219,
                'location_address' => 'Adetokunbo Ademola St, VI',
            ],
            [
                'user_id' => $agent3->id,
                'customer_id' => $createdCustomers[2]->id,
                'activity_date' => $yesterday,
                'check_in_time' => '13:00:00',
                'check_out_time' => '14:20:00',
                'activity_type' => 'payment_collection',
                'purpose' => 'Collect bulk purchase settlement.',
                'notes' => 'Received direct bank transfer of ₦2,500,000 against account balance.',
                'outcome' => 'payment_received',
                'next_followup_date' => $today,
                'latitude' => 6.4608,
                'longitude' => 3.1912,
                'location_address' => 'Line 4, Alaba Market, Ojo',
            ],
        ];

        foreach ($activities as $act) {
            FieldActivity::create($act);
        }

        // 5. Create Sample Sales Orders
        $order1 = Order::create([
            'order_number' => 'ORD-2026-0081',
            'customer_id' => $createdCustomers[0]->id, // Apex Mega
            'user_id' => $agent2->id,
            'order_date' => $today,
            'subtotal' => 900000.00,
            'discount_amount' => 45000.00,
            'tax_amount' => 0.00,
            'total_amount' => 855000.00,
            'paid_amount' => 855000.00,
            'payment_status' => 'fully_paid',
            'order_status' => 'approved',
            'delivery_address' => 'Plot 14B Adetokunbo Ademola St, Victoria Island, Lagos',
            'notes' => 'VIP order for Nordic Chandeliers. Urgent dispatch requested.',
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $createdProducts[2]->id, // Nordic chandelier
            'quantity' => 4,
            'unit_price' => 225000.00,
            'total_price' => 900000.00,
        ]);

        Payment::create([
            'payment_reference' => 'PAY-2026-0091',
            'customer_id' => $createdCustomers[0]->id,
            'order_id' => $order1->id,
            'user_id' => $agent2->id,
            'amount' => 855000.00,
            'payment_date' => $today,
            'payment_method' => 'bank_transfer',
            'reference_note' => 'GTB-TRF-99210044',
            'status' => 'confirmed',
            'notes' => 'Full advance transfer confirmed via GTBank corporate internet banking.',
        ]);

        $order2 = Order::create([
            'order_number' => 'ORD-2026-0082',
            'customer_id' => $createdCustomers[2]->id, // Alaba Hub
            'user_id' => $agent3->id,
            'order_date' => $today,
            'subtotal' => 2930000.00,
            'discount_amount' => 130000.00,
            'tax_amount' => 0.00,
            'total_amount' => 2800000.00,
            'paid_amount' => 1500000.00,
            'payment_status' => 'partially_paid',
            'order_status' => 'approved',
            'delivery_address' => 'Line 4, Shop 28 Electronics Section, Alaba Market, Lagos',
            'notes' => 'Wholesale order for 100W floodlights and 8-way distribution boards.',
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $createdProducts[1]->id, // 100W floodlight
            'quantity' => 40,
            'unit_price' => 39500.00,
            'total_price' => 1580000.00,
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $createdProducts[5]->id, // 8-way DB
            'quantity' => 50,
            'unit_price' => 27000.00,
            'total_price' => 1350000.00,
        ]);

        Payment::create([
            'payment_reference' => 'PAY-2026-0092',
            'customer_id' => $createdCustomers[2]->id,
            'order_id' => $order2->id,
            'user_id' => $agent3->id,
            'amount' => 1500000.00,
            'payment_date' => $today,
            'payment_method' => 'bank_transfer',
            'reference_note' => 'ZENITH-TRF-5582910',
            'status' => 'confirmed',
            'notes' => '50% initial deposit paid, balance due upon warehouse dispatch.',
        ]);

        $order3 = Order::create([
            'order_number' => 'ORD-2026-0083',
            'customer_id' => $createdCustomers[6]->id, // Ogudu GRA (onboarded today)
            'user_id' => $agent4->id,
            'order_date' => $today,
            'subtotal' => 236000.00,
            'discount_amount' => 10000.00,
            'tax_amount' => 0.00,
            'total_amount' => 226000.00,
            'paid_amount' => 0.00,
            'payment_status' => 'unpaid',
            'order_status' => 'pending_approval',
            'delivery_address' => 'Plot 5 Ogudu Road, Opposite Ogudu Mall, Lagos',
            'notes' => 'First opening trial order for newly onboarded retail outlet.',
        ]);

        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $createdProducts[9]->id, // Rechargeable mist fan
            'quantity' => 3,
            'unit_price' => 59000.00,
            'total_price' => 177000.00,
        ]);

        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $createdProducts[10]->id, // 13A dual USB socket
            'quantity' => 10,
            'unit_price' => 5900.00,
            'total_price' => 59000.00,
        ]);
    }
}

