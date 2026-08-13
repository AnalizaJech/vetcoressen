<div align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel" />
  <img src="https://img.shields.io/badge/Livewire-4e56a6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS" />
  <img src="https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
  <br>
  <h1>🐾 VetCoressen</h1>
  <p><strong>Comprehensive Veterinary Clinic Management System</strong></p>
</div>

<br>

## 📖 About the Project

**VetCoressen** is a modern, scalable, and fully automated management system engineered specifically for veterinary clinics. Built on the robust **Laravel** framework and the reactive **Livewire** stack, it streamlines day-to-day clinical operations. 

From managing electronic clinical histories to automating client reminders and handling complex point-of-sale transactions with electronic invoicing (SUNAT-compliant), VetCoressen ensures that veterinary professionals can focus entirely on patient care rather than administrative overhead.

---

## ✨ Key Features

- 🏥 **Clinical Records (Historias Clínicas)**: Maintain highly detailed, electronic medical histories for each pet. Track diagnoses, long-term treatments, vaccines, and medical progress seamlessly.
- 📅 **Smart Appointments (Citas)**: Schedule, reschedule, and manage veterinary appointments through an interactive calendar interface. Clients receive automated email reminders to reduce no-shows.
- 🐶 **Patient & Customer Management (Mascotas & Clientes)**: Organize detailed profiles for pet owners and their companions. Cross-reference them instantly with medical records, appointments, and billing histories.
- 📦 **Advanced Inventory (Inventario)**: Stay on top of your product stock, medications, and food supplies. The system automatically tracks **product batches (lotes)** and **expiry dates**, triggering alerts for critical stock to guarantee patient safety.
- 💳 **Point of Sale & Billing (Caja)**: A fully featured POS system featuring a dynamic cart, real-time IGV (tax) calculation, cash register reconciliation (arqueo de caja), and detailed receipt generation.
- 🧾 **Electronic Invoicing**: Built-in, direct integration with **Nubefact** for issuing legally compliant electronic invoices (*boletas* and *facturas*) directly to SUNAT (Peruvian Tax Authority).
- 🏢 **Multi-Branch Support (Sucursales)**: Ready to scale. Manage operations, inventory, and staff across multiple physical clinic locations from a centralized dashboard.
- 📊 **Settings & Analytics (Ajustes & Reportes)**: Configure clinic information, assign granular user roles, and monitor business health through dynamic analytics and reports.

---

## 🛠️ Technology Stack

**VetCoressen** leverages a cutting-edge, highly performant tech stack:

- **Backend Architecture**: PHP 8.2+, Laravel 12.x, Livewire 4.x, Livewire Flux 2.x
- **Frontend & UI**: Tailwind CSS 4.0, Vite 7.0, FullCalendar 6.1
- **Third-Party Integrations**: 
  - 📧 **Resend**: Transactional emails and appointment reminders.
  - 📄 **Nubefact**: Seamless SUNAT electronic invoicing.
  - 🆔 **PeruAPI**: Automated DNI and RUC verification.
  - 💬 **Twilio**: SMS and WhatsApp messaging capabilities.
- **Development Tooling**: Laravel Pail, Sail, Pint, and PestPHP.

---

## ⚙️ Getting Started

### Prerequisites

Ensure your local development environment meets the following requirements:
- **PHP** >= 8.2
- **Composer** (Dependency Manager)
- **Node.js** & **npm**
- **MySQL** or MariaDB

### Installation

The project provides a fully automated setup script defined in `composer.json` for rapid deployment. Run a single command to get everything up and running:

```bash
composer run setup
```

#### Manual Installation

If you prefer to configure the environment step-by-step:

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd vetcoressen
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Configure the Environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Note: Open your `.env` file and configure your database connection (`DB_DATABASE`, etc.) along with any required API keys (`RESEND_API_KEY`, `NUBEFACT_TOKEN`, `PERUAPI_KEY`).*

4. **Run Database Migrations:**
   ```bash
   php artisan migrate --force
   ```

5. **Compile Frontend Assets:**
   ```bash
   npm install
   npm run build
   ```

---

## ▶️ Usage & Development

To launch the local development environment—which concurrently starts the PHP server, the queue listener, the log viewer, and the Vite hot-module replacement server—run:

```bash
composer run dev
```

---

## 🗂️ Project Architecture

A quick overview of the core directories driving the application logic:

- `app/Livewire/` — Contains all reactive, frontend interactive components (e.g., POS, Inventory, Appointments).
- `app/Models/` — Eloquent ORM models representing the database schema.
- `app/Services/` — Core business logic and third-party integration classes (e.g., `InventoryService`, `NubefactService`).
- `app/Mail/` — Mailable classes responsible for dispatching beautiful email notifications and digital receipts.
- `app/Jobs/` — Background, asynchronous queued jobs for heavy processing.

---

## 🌐 Localization & Security

- **Localization**: Native multi-language support. The platform is fully localized in both **English (`en`)** and **Spanish (`es`)**.
- **Roles & Permissions**: Access control is strictly enforced using `spatie/laravel-permission`. 
- **Audit Trails**: Critical system and user activities are meticulously logged via `spatie/laravel-activitylog`.

---

## 👤 Development & Academic Context

**Author & Project Coordinator**:  
**Jorge Enrique Caceres Hernandez**  
*Systems Engineering Student, Universidad Nacional de Cañete*

**Academic Advisor**:  
**Alex Abelardo Pacheco-Pumaleque**

*This software was developed within the framework of the research project **"Innovation in Veterinary Management: Automated Reminder System to Optimize Clinical History Management in San Vicente de Cañete, 2025"**.*

### 💰 Grant Information
This project was proudly funded by the **Vice Presidency for Research (Vicepresidencia de Investigación)** at UNDC, awarded during the *First Research Competition for the Development of Innovations and Intellectual Property*, under grant contract **N° 021-2024-UNDC/CO/P/DGA**.

---

## 📄 License

This project is open-source and distributed under the terms of the **[MIT License](LICENSE)**.
