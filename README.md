<div align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel" />
  <img src="https://img.shields.io/badge/Livewire-4e56a6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS" />
  <img src="https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
  <br>
  <h1>🐾 VetCoressen</h1>
  <p><strong>Sistema Integral de Gestión de Clínicas Veterinarias</strong></p>
</div>

<br>

## 📌 Sobre el Proyecto

**VetCoressen** es un sistema de gestión moderno, escalable y completamente automatizado, diseñado específicamente para clínicas veterinarias. Construido sobre el robusto framework **Laravel** y la pila reactiva **Livewire**, el sistema optimiza y agiliza las operaciones clínicas diarias.

Desde la gestión de historias clínicas electrónicas hasta la automatización de recordatorios para clientes y el manejo integral de punto de venta con facturación electrónica (integrada con SUNAT), VetCoressen garantiza que los profesionales veterinarios puedan enfocarse enteramente en el cuidado de los pacientes en lugar de la carga administrativa.

---

## 🚀 Características Principales

- 🏥 **Historias Clínicas Electrónicas**: Mantenga registros médicos electrónicos altamente detallados para cada mascota. Realice un seguimiento continuo de diagnósticos, tratamientos, vacunas y evolución médica sin complicaciones.
- 📅 **Gestión Inteligente de Citas**: Programe, reprograme y administre citas veterinarias mediante una interfaz de calendario interactiva. Los clientes reciben recordatorios automáticos por correo electrónico para reducir el ausentismo.
- 🐶 **Gestión de Clientes y Mascotas**: Organice perfiles detallados de propietarios y sus mascotas. Acceda al instante a sus historias clínicas, próximas citas y un historial completo de facturación.
- 📦 **Inventario Avanzado**: Mantenga el control absoluto de su stock de productos, medicamentos y alimentos. El sistema rastrea automáticamente **lotes de productos** y **fechas de caducidad**, generando alertas críticas para garantizar la seguridad de los pacientes.
- 💳 **Caja y Punto de Venta (POS)**: Un sistema de Punto de Venta completo con carrito dinámico, cálculo de IGV en tiempo real, arqueo de caja (aperturas y cierres) y generación detallada de recibos.
- 🧾 **Facturación Electrónica**: Integración nativa y directa con **Nubefact** para emitir boletas y facturas electrónicas con total validez legal ante la SUNAT.
- 🏢 **Soporte Multi-Sucursal**: Listo para escalar. Administre las operaciones, el inventario y el personal a través de múltiples sedes físicas desde un panel de control centralizado.
- 📊 **Reportes y Analíticas**: Configure la información de la clínica, asigne roles y permisos granulares a los usuarios, y supervise la salud del negocio a través de reportes y métricas dinámicas.

---

## 💻 Stack Tecnológico

**VetCoressen** está desarrollado utilizando un stack tecnológico moderno y de alto rendimiento:

- **Arquitectura Backend**: PHP 8.2+, Laravel 12.x, Livewire 4.x, Livewire Flux 2.x
- **Frontend y UI**: Tailwind CSS 4.0, Vite 7.0, AlpineJS, FullCalendar 6.1
- **Integraciones de Terceros**: 
  - 📧 **Resend**: Para correos transaccionales y recordatorios de citas.
  - 🧾 **Nubefact**: Para facturación electrónica transparente con SUNAT.
  - 🔎 **PeruAPI**: Verificación automática de DNI y RUC en tiempo real.
  - 📱 **Twilio**: Capacidades de mensajería SMS y WhatsApp.
- **Herramientas de Desarrollo**: Laravel Pail, Sail, Pint y PestPHP.

---

## 🛠️ Guía de Instalación

### Requisitos Previos

Asegúrese de que su entorno de desarrollo local cumpla con los siguientes requisitos:
- **PHP** >= 8.2
- **Composer** (Gestor de dependencias)
- **Node.js** y **npm**
- **MySQL** o MariaDB

### Instalación Rápida

El proyecto proporciona un script de configuración totalmente automatizado definido en `composer.json` para un despliegue rápido. Ejecute un solo comando para preparar todo el entorno:

```bash
composer run setup
```

### Instalación Manual

Si prefiere configurar el entorno paso a paso:

1. **Clonar el repositorio:**
   ```bash
   git clone <repository-url>
   cd vetcoressen
   ```

2. **Instalar dependencias de PHP y Frontend:**
   ```bash
   composer install
   npm install
   ```

3. **Configurar el entorno:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Nota: Abra el archivo `.env` y configure la conexión a su base de datos (`DB_DATABASE`, etc.), junto con las claves API requeridas (`RESEND_API_KEY`, `NUBEFACT_TOKEN`, `PERUAPI_KEY`).*

4. **Ejecutar Migraciones de Base de Datos y Seeders:**
   ```bash
   php artisan migrate --seed
   ```

5. **Compilar recursos del Frontend:**
   ```bash
   npm run build
   ```

---

## 👨‍💻 Uso y Desarrollo

Para iniciar el entorno de desarrollo local (que ejecuta simultáneamente el servidor PHP, el worker de colas, el visor de logs y el servidor Vite con Hot-Module Replacement), ejecute:

```bash
composer run dev
```

---

## 📁 Arquitectura del Proyecto

Un resumen de los directorios principales que impulsan la lógica de la aplicación:

- `app/Livewire/` – Contiene todos los componentes interactivos y reactivos del frontend (ej. POS, Inventario, Citas).
- `app/Models/` – Modelos ORM de Eloquent que representan el esquema de la base de datos.
- `app/Services/` – Clases base para la lógica de negocio y las integraciones de terceros (ej. `InventoryService`, `NubefactService`).
- `app/Mail/` – Clases *Mailable* responsables de enviar notificaciones por correo y recibos digitales.
- `app/Jobs/` – Trabajos en cola asíncronos para procesos pesados en segundo plano.
- `resources/views/` – Vistas de Blade y componentes UI (con integración de i18n para internacionalización).

---

## 🔐 Localización y Seguridad

- **Localización**: Soporte multi-idioma nativo mediante AlpineJS y Blade. La plataforma está completamente traducida al **Inglés (`en`)** y **Español (`es`)**.
- **Roles y Permisos**: El control de acceso se aplica estrictamente utilizando `spatie/laravel-permission`.
- **Trazabilidad (Audit Trails)**: Las actividades críticas del sistema y de los usuarios se registran meticulosamente mediante `spatie/laravel-activitylog`.

---

## 🎓 Contexto Académico y Desarrollo

**Autor y Coordinador del Proyecto**:  
**Jorge Enrique Caceres Hernandez**  
*Estudiante de Ingeniería de Sistemas, Universidad Nacional de Cañete*

**Asesor Académico**:  
**Alex Abelardo Pacheco-Pumaleque**

*Este software fue desarrollado en el marco del proyecto de investigación **"Innovación en Gestión Veterinaria: Sistema de Recordatorios Automatizados para Optimizar el Manejo de Historias Clínicas en San Vicente de Cañete, 2025"**.*

### 🏛️ Financiación
Este proyecto fue orgullosamente financiado por la **Vicepresidencia de Investigación** de la UNDC, galardonado durante el *I Concurso de Investigación para el Desarrollo de Innovaciones y Propiedad Intelectual*, bajo el contrato de subvención **N° 021-2024-UNDC/CO/P/DGA**.

---

## 📄 Licencia

Este proyecto es de código abierto y se distribuye bajo los términos de la **[Licencia MIT](LICENSE)**.
