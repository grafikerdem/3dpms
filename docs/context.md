# 🧩 3D Print Studio Management System (3DPMS)
> **Fontext-Over Context Document — v1.0 (MVP)**  
> Framework: PHP (Object Oriented)  
> Frontend: Bootstrap 5 (Light/Dark Theme)  
> Database: MySQL  
> Target: Professional 3D print studios & small production labs

---

## 🎯 Project Goal

A lightweight, modular web system designed for **professional 3D printing studios**, focusing on:
- Fast and accurate **quoting (tekliflendirme)**
- **Material, printer, and job tracking**
- Simple yet expandable **production planning**
- Real-time **cost calculation**
- Minimal setup and **zero-dependency installation**

---

## 🧱 System Architecture

/3dprintstudio
├── /assets
│ ├── /css
│ ├── /js
│ └── /images
├── /includes
│ ├── database.php
│ ├── functions.php → cost calculation, depreciation, energy
│ ├── auth.php → login/session control
│ └── helpers.php
├── /modules
│ ├── dashboard.php → overview, alerts, graphs
│ ├── projects.php → jobs & quoting
│ ├── printers.php → printer management
│ ├── materials.php → stock & consumables
│ ├── production.php → MES-lite planning
│ └── reports.php → analytics, profitability
├── /install
│ ├── index.php → setup wizard
│ └── create_tables.php
├── settings.php
├── login.php
├── index.php
└── .htaccess


---

## 💾 Database Schema (Simplified)

| Table | Description |
|-------|--------------|
| `projects` | Stores all projects, customers, statuses |
| `project_parts` | Each project’s detailed parts and cost data |
| `printers` | Printer database with depreciation and energy data |
| `materials` | Filament/resin inventory with auto-stock logic |
| `consumables` | Workshop materials (IPA, gloves, etc.) |
| `settings` | Global preferences (rates, power cost, currency) |
| *(optional)* `maintenance_log` | Printer maintenance records |
| *(optional)* `activity_log` | System event tracking |

---

## 🔧 Installation Flow (Install Wizard)

### Step 1. Server Check
- PHP version ≥ 8.0
- MySQL extension enabled
- Writable `/includes` and `/install` folders

### Step 2. Database Configuration
- Host, Username, Password, Database name
- Connection test → if OK → proceed

### Step 3. Table Creation
- Runs `/install/create_tables.php`
- Populates default settings:
  - `currency = ₺`
  - `designer_rate = 150`
  - `operator_rate = 100`
  - `electricity_cost = 4.2`
  - `markup = 15`

### Step 4. Admin Account Setup
- Username, Password creation
- Stored in `users` table (hashed)

### Step 5. Ready Screen
- Redirects to `/login.php`

---

## 🧩 MODULE 1: Projects & Quoting

### 🔹 1.1 Project Management
- **Auto Project Number:** `PROJE-2025-001`
- **Customer Name:** Free text field
- **Status Options:**
  - Teklif • Onaylandı • Üretimde • Kalite Kontrol • İptal Edildi • Tamamlandı
- **Editable Notes:** Internal comments per project

### 🔹 1.2 Advanced Part Cost Engine
Each project may include multiple parts:
| Field | Input Type | Example |
|-------|-------------|---------|
| Part Name | text | braket_v1 |
| Printer | dropdown | Bambu P1S |
| Material | dropdown | PLA eSun Black |
| Material Amount | number (g) | 230 |
| Print Time | number (hour) | 6.5 |

**Dynamic Cost Preview:**
Auto-updates every time a field changes.

### 🔹 1.3 Labor & Setup Cost
- Two labor categories:
  - **Designer/Slicer** → 150 ₺/hour  
  - **Operator** → 100 ₺/hour  
- Time fields per part:
  - Design Time (min)  
  - Setup Time (min)  
  - Postprocess Time (min)

**Formula:**
Total Cost =
(Material_gram × Material_price) +
(Print_hours × (Amortization + Energy)) +
(Design_min × Designer_rate/60) +
((Setup_min + Postprocess_min) × Operator_rate/60)


### 🔹 1.4 Quotation Output (PDF)
- Includes project, parts, costs, markup
- Logo and letterhead from `/settings`
- PDF generated via **dompdf/tcpdf**
- Export filename: `Teklif_PROJE-2025-001.pdf`

---

## 🖨️ MODULE 2: Printer Fleet Management

### 🔹 2.1 Printer Database
Fields:
- Name, Brand, Model, Technology (FDM/SLA)
- Purchase Price, Purchase Date
- Power Consumption (W)
- Lifespan (hours)
- Amortization auto-calculated

### 🔹 2.2 Automatic Depreciation & Energy
Amortization = Purchase_Price / Lifespan
Energy = (Power_W × Electricity_Cost) / 1000
Machine_Hour_Cost = Amortization + Energy


### 🔹 2.3 Maintenance
- Manual entries like: *01.03.2025 – nozzle replaced*
- **Alert system:** when total print hours exceed 500 h → dashboard warning

---

## 📦 MODULE 3: Inventory & Stock

### 🔹 3.1 Filament / Resin Database
- Brand, Type (PLA/PETG/Resin), Color, Diameter, Unit Price
- Stock amount (kg, L, pcs)
- Automatic stock deduction on project completion
- Low stock alert below threshold (e.g. 2 spools)

### 🔹 3.2 Consumables
- IPA, gloves, sandpaper, screws, adhesives, boxes
- Added manually as “extra expenses” in cost engine

---

## ⚙️ MODULE 4: Production Planning (MES-Lite)

### 🔹 4.1 Job Queue
- “Approved” projects appear in a **Waiting Jobs** table
- Operator assigns jobs to printers via dropdown or drag-and-drop
- Visual representation via **FullCalendar.js**

### 🔹 4.2 Job Tickets
- Once assigned → system creates a job entry with:
  - Start time / end time
  - Printer ID
  - Job status (Baskıda / Başarısız / Bitti / QC Bekliyor)

### 🔹 4.3 Failed Print Tracking
- Record failure reason (“nozzle jam”, “bed lift”)
- Wasted material/time deducted in “Actual Cost” reports

### 🔹 4.4 Quality Control
- Simple checklist (dimensions OK? surface clean?)
- Status → “QC Passed / QC Failed”
- Logged to `project_parts` table

---

## 📊 MODULE 5: Reporting & Analytics

### 🔹 5.1 Dashboard Overview
- Active Jobs count
- Low Stock alerts
- Maintenance alerts
- Charts (via Chart.js):
  - Monthly Profit
  - Material Usage
  - Printer utilization

### 🔹 5.2 Project Profitability
- Compare Estimated vs Actual cost
- List top 10 most profitable projects

### 🔹 5.3 Machine Performance
- Utilization ratio per printer (%)
- Failure rate tracking

### 🔹 5.4 Material Statistics
- Most used material types & colors
- Remaining stock summary

---

## ⚙️ MODULE 6: General Settings & System Features

### 🔹 6.1 UI & Accessibility
- **Dark/Light Mode toggle**
- **Responsive** across PC, tablet, mobile

### 🔹 6.2 Data Management
- Export/Import (JSON or CSV)
- Backup all tables with a single click

### 🔹 6.3 User Roles (Future-Ready)
| Role | Permissions |
|------|--------------|
| Admin | All modules, cost & reports |
| Operator | Production planning only, no cost access |

### 🔹 6.4 Global Currency
- Select from ₺, $, €  
- Purely visual (no FX conversion)
- Applies globally to UI & reports

---

## 🚀 MVP Features Checklist

| Feature | Included |
|----------|-----------|
| Dashboard & cost calculator | ✅ |
| Project & multi-part system | ✅ |
| Real-time cost engine | ✅ |
| Printer amortization & energy | ✅ |
| Material & consumable tracking | ✅ |
| PDF quotation output | ✅ |
| Maintenance & low-stock alerts | ✅ |
| Basic MES (job queue) | ✅ |
| Reports & charts | ✅ |
| Install wizard | ✅ |

---

## ❌ Out of Scope (for MVP)
- Automatic slicer or G-code analysis  
- 3D viewer (.stl visualization)  
- CRM, invoicing or tax management  
- API integrations or multi-user access  

---

## 🧮 Example Cost Snapshot

| Item | Value |
|------|--------|
| Material | 220 g × 0.6 ₺/g = 132 ₺ |
| Machine | 6 h × (2.4 ₺ amort. + 1.2 ₺ energy) = 21.6 ₺ |
| Labor | (20 min design + 30 min post) = 83.3 ₺ |
| Total | 236.9 ₺ |
| + Markup 15% | **272.4 ₺ (final quote)** |

---

## 🪄 Future Expansion Ideas
- User account system with permissions  
- Automatic machine usage sync (OctoPrint API)  
- Maintenance schedule notifications (email)  
- REST API for mobile app integration  
- AI-based pricing suggestion  

---

> **Summary:**  
> This system unifies cost management, production follow-up, and inventory control into a compact PHP application.  
> Its modularity ensures scalability — from single-user studios to multi-printer labs.  
> It’s practical, transparent, and fully customizable to any 3D printing workflow.

