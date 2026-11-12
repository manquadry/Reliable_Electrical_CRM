# Reliable Electrics Nigeria Limited - Enterprise Field Sales & Commercial Distribution CRM

An enterprise-grade CRM application engineered in **Laravel 13** and tailored for **Reliable Electrics Nigeria Limited** (Phase 1, 32B Oguntona Crescent, Gbagada, Lagos), an electrical appliances, lighting fixtures, and industrial switchgear distributor.

---

## 🌟 Key Capabilities

### 1. 👥 Field Sales Executive Onboarding & Management
- **Agent Profile & ID**: Unique agent codes (e.g., `REL-FE-101`), full contact credentials, and employment status.
- **Territory & Route Assignment**: Assigned operational hubs across Lagos (e.g. *Ikeja & Alausa Hub*, *Lekki & VI Corridor*, *Alaba International Market*, *Gbagada Headquarters Corridor*).
- **Transport Mode**: Logistics tracking (Company Motorbike, Field Sedan, etc.).
- **KPI Quotas**: Daily visit target (e.g. 7 visits/day), monthly customer onboarding goal (e.g. 15 stores/month), and monthly revenue quota in Nigerian Naira (₦).
- **Agent Detail Audit**: Complete profile showcasing target gauges, all-time customers onboarded, visit timelines, and sales performance.

### 2. 🏪 Customer Onboarding by Field Agents
- **Store Identity**: Business name, contact person, primary phone, alternative phone, and email.
- **Category Classification**: Retail Appliance Shop, Wholesale Distributor, Electrical Contractor, Luxury Lighting Showroom, Supermarket Chain, and Real Estate Developer.
- **Onboarding Metadata**:
  - **Day Onboarded**: The exact calendar date the customer was registered and verified.
  - **Onboarded By**: Direct attribution to the Field Sales Executive who acquired the account.
- **Commercial Terms**: Approved Credit Limit (₦) and Payment Terms (Cash on Delivery, Net 7, Net 14, Net 30).
- **Geolocation**: Physical address, landmark, and **one-click GPS Coordinate Capture** using browser geolocation.
- **Automatic Log**: Onboarding a customer instantly logs a `new_customer_onboarding` activity for the sales agent in the daily tracker.

### 3. 📅 Admin Date-by-Date Field Activity Tracker (`/activities/daily`)
- **Arbitrary Date Inspection**: Select any calendar date (Today, Yesterday, past dates) using the interactive date picker or quick pills.
- **Daily Performance Banner**: Active agents in field, total visits logged, new customers onboarded on that date, orders secured (₦), and cash/bank collections (₦).
- **Agent-by-Agent Audit Cards**:
  - Time in field (first check-in to last check-in).
  - Target compliance progress meter (e.g., 3 of 7 visits • 43%).
  - Chronological visit stream with exact check-in timestamps, customer name, purpose, feedback notes, outcome badge, and GPS map pin.
  - Highlighted cards for any customers onboarded on that date.
  - Summary of orders booked and payments received by that agent on that date.
- **Non-Compliance Alerts**: Highlights any field executives who logged 0 activities on that date for management accountability.

### 4. 📊 Activity & Performance Reports (`/activities/report`)
- Filter across **Date Ranges**, **Field Executives**, **Activity Types**, and **Outcomes**.
- Executive metric summary cards for the selected period.
- **Field Agent Comparison Scorecard**: Leaderboard comparing completed visits, customers onboarded, orders count, revenue secured, and collections.
- **1-Click CSV Export**: Downloads complete data file for Excel and board presentations.
- **Printable Report Format**: Clean print stylesheet for physical executive meetings.

### 5. 📦 Commercial Orders, Collections & Catalog
- **Electrical Appliances Catalog (`/products`)**: Commercial LED floodlights, modern chandeliers, changeover switches, distribution boards, Coleman copper cables, solar streetlights, rechargeable mist fans.
- **Order Booking (`/orders/create`)**: Interactive line-item calculator with wholesale pricing, quantity controls, discounts, and automatic inventory stock deduction.
- **Printable Waybill / Invoice (`/orders/{id}`)**: Standard commercial waybill layout with customer bill to/ship to, itemized table, balance due, and signature blocks.
- **Payments & Collections (`/payments`)**: Record cash, POS receipts, bank transfers, and cheques with automatic order balance reconciliation.
- **Mobile-First Experience**: Optimized interface for field agents operating from smartphones in the field.

---

## 🚀 Instant Demo Credentials & One-Click Login

The CRM includes a **Quick Role Switcher** dropdown in the top header and on the login page:

| Role | Name | Territory | Credentials |
| :--- | :--- | :--- | :--- |
| **Managing Director (Admin)** | Engr. Adekunle Lawal | Lagos Headquarters | `admin@reliableelectrics.ng` / `password` |
| **Field Executive (Ikeja Hub)** | Chinedu Okeke | Ikeja & Alausa Hub | `chinedu@reliableelectrics.ng` / `password` |
| **Field Executive (Lekki / VI)** | Fatima Bello | Lekki & VI Corridor | `fatima@reliableelectrics.ng` / `password` |
| **Field Executive (Alaba Market)** | Emeka Nnamdi | Alaba International | `emeka@reliableelectrics.ng` / `password` |
| **Field Executive (Gbagada Zone)** | Tunde Balogun | Gbagada & Ogudu | `tunde@reliableelectrics.ng` / `password` |

---

## 💻 Running the Application

Start the Laravel development server:

```powershell
php artisan serve
```

Visit the application in your browser:
**`http://localhost:8000`**

### Running the Test Suite:

```powershell
php artisan test
```
All 11 Feature Tests pass with 100% green coverage.
