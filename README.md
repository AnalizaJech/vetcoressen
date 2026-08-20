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

## 📌 About the Project

**VetCoressen** is a modern, scalable, and fully automated management system designed specifically for veterinary clinics. Built on top of the robust **Laravel** framework and the reactive **Livewire** stack, the system optimizes and streamlines daily clinical operations.

From managing electronic health records and automating client reminders to comprehensive Point of Sale (POS) operations with integrated electronic invoicing (direct integration with SUNAT via Nubefact), VetCoressen ensures that veterinary professionals can focus entirely on patient care instead of administrative burdens.

---

## 🚀 Key Features

- 🏥 **Electronic Medical Records**: Maintain highly detailed electronic medical records for each pet. Keep continuous track of diagnoses, treatments, vaccines, prescriptions, and medical evolution.
- 📅 **Smart Appointment Management**: Schedule, reschedule, and manage veterinary appointments using an interactive calendar interface. Clients receive automated email reminders to reduce no-shows.
- 🐶 **Client & Pet Management**: Organize detailed profiles of owners and their pets. Instantly access medical histories, upcoming appointments, and complete billing histories.
- 📦 **Advanced Inventory Control**: Maintain absolute control over your stock of products, medicines, and food. The system automatically tracks **product batches** and **expiration dates**, generating critical alerts to ensure patient safety.
- 💳 **Cashier & Point of Sale (POS)**: A complete Point of Sale system with a dynamic cart, real-time tax (IGV) calculation, cash register management (openings and closings), and detailed receipt generation.
- 🧾 **Electronic Invoicing**: Native and direct integration with **Nubefact** to issue legally valid electronic receipts (boletas and facturas) for SUNAT.
- 🏢 **Multi-Branch Support**: Ready to scale. Manage operations, inventory, and staff across multiple physical locations from a centralized control panel.
- 📊 **Dynamic Reports & Analytics**: Monitor business health through interactive charts, check top recurring diseases/symptoms with predictive stock planning, and manage user roles/permissions.
- 🌐 **Native Bilingual Support**: Fully internationalized with on-the-fly language switching between **English (`en`)** and **Spanish (`es`)**.

---

## 💻 Technological Stack

**VetCoressen** is developed using a modern, high-performance tech stack:

- **Backend Architecture**: PHP 8.2+, Laravel 11.x, Livewire 3.x, Livewire Flux 2.x
- **Frontend & UI**: Tailwind CSS 4.0, Vite 7.0, AlpineJS, FullCalendar 6.1
- **Third-Party Integrations**: 
  - 📧 **Resend**: For transactional emails and appointment reminders.
  - 🧾 **Nubefact**: For seamless electronic invoicing with SUNAT.
  - 🔎 **PeruAPI**: Automatic real-time DNI and RUC verification.
  - 📱 **Twilio**: SMS and WhatsApp messaging capabilities.
- **Development Tools**: Laravel Pail, Sail, Pint, and PestPHP.

---

## 🛠️ Installation Guide

### Prerequisites

Ensure your local development environment meets the following requirements:
- **PHP** >= 8.2
- **Composer** (PHP dependency manager)
- **Node.js** & **npm**
- **MySQL** or MariaDB

### Fast Installation

The project provides an automated setup script defined in `composer.json` for rapid deployment. Run a single command to prepare the entire environment:

```bash
composer run setup
```

### Manual Installation

If you prefer setting up the environment step-by-step:

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd vetcoressen
   ```

2. **Install PHP and Frontend dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Configure the environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Note: Open the `.env` file and configure your database connection (`DB_DATABASE`, etc.), along with the required API keys (`RESEND_API_KEY`, `NUBEFACT_TOKEN`, `PERUAPI_KEY`).*

4. **Run Database Migrations and Seeders:**
   ```bash
   php artisan migrate:fresh --seed
   ```
   *This command will recreate the database tables and populate the system with a complete year and a half of realistic clinical data (200 clients, 320 pets, 2100 appointments, medical records, prescriptions, inventory batches, and 1800 sales) up to next month.*

5. **Build Frontend assets:**
   ```bash
   npm run build
   ```

---

## 👨‍💻 Usage & Development

To start the local development environment (which runs the PHP server, queue worker, log viewer, and Vite server with Hot-Module Replacement simultaneously), execute:

```bash
composer run dev
```

---

## 📁 Project Architecture

A summary of the main directories driving the application logic:

- `app/Livewire/` – Contains all interactive and reactive frontend components (e.g., POS, Inventory, Appointments).
- `app/Models/` – Eloquent ORM models representing the database schema.
- `app/Services/` – Service classes for business logic and third-party integrations (e.g., `InventoryService`, `NubefactService`).
- `app/Mail/` – Mailable classes responsible for sending email notifications and digital receipts.
- `app/Jobs/` – Asynchronous queued jobs for background processing.
- `resources/views/` – Blade views and UI components (integrated with AlpineJS i18n for internationalization).
- `public/locales/` – Dynamic translation dictionaries for bilingual support.

---

## 🔐 Localization & Security

- **Localization**: Native multi-language support powered by AlpineJS and Blade. The platform is translated into **English (`en`)** and **Spanish (`es`)**.
- **Roles & Permissions**: Access control is strictly enforced using `spatie/laravel-permission`.
- **Traceability (Audit Trails)**: Critical system and user activities are meticulously logged using `spatie/laravel-activitylog`.

---

## 🎓 Academic Context & Development

**Project Author & Coordinator**:  
**Jorge Enrique Caceres Hernandez**  
*Systems Engineering Student, Universidad Nacional de Cañete*

**Academic Advisor**:  
**Alex Abelardo Pacheco-Pumaleque**

*This software was developed within the framework of the research project **"Innovation in Veterinary Management: Automated Reminder System to Optimize Clinical Record Management in San Vicente de Cañete, 2025"**.*

### 🏛️ Funding
This project was proudly funded by the **Vice-Presidency of Research** of the UNDC, awarded during the *I Research Contest for the Development of Innovations and Intellectual Property*, under grant contract **N° 021-2024-UNDC/CO/P/DGA**.

---

## 📄 License

This project is open-source and distributed under the terms of the **[MIT License](LICENSE)**.
