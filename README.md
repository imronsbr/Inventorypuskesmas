# SIMP - Inventorypuskesmas

**SIMP (Sistem Informasi Manajemen Puskesmas)** is a modern Inventory Management System specifically designed for Public Health Centers (Puskesmas). Built with the latest web technologies, it provides a robust, efficient, and user-friendly platform for tracking medical supplies, medications, and equipment.

[![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.4-4FC08D?style=for-the-badge&logo=vue.js)](https://vuejs.org)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-2.0-9553E9?style=for-the-badge&logo=inertia)](https://inertiajs.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css)](https://tailwindcss.com)

---

## 🚀 Technology Stack

- **Backup / Core**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue 3 with Inertia.js 2.0 (The Modern Monolith)
- **Styling**: Tailwind CSS & DaisyUI 5
- **State Management**: Pinia
- **Database**: MySQL / MariaDB
- **Key Libraries**:
  - **Data Visualization**: Chart.js, ECharts, ApexCharts
  - **Permissions**: Spatie Laravel Permission
  - **Logging**: Spatie Laravel Activitylog
  - **Exporting**: Laravel Excel
  - **Identification**: QR Code & Barcode support (JsBarcode, qrcode, jsqr)

---

## ✨ Key Features

- **Comprehensive Inventory Tracking**: Manage items, stock levels, and expiration dates.
- **Interactive Dashboard**: Real-time data visualization with beautiful charts and metrics.
- **Role-Based Access Control (RBAC)**: Manage users and permissions efficiently.
- **Activity Logging**: Track every change made within the system for audit purposes.
- **QR & Barcode Support**: Integrated scanning and generation for easy item identification.
- **Excel Reports**: Export inventory data and reports to Excel format.
- **Responsive Design**: Fully optimized for various devices and screen sizes.

---

## 🛠️ Development Setup

### Prerequisites

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL / MariaDB

### Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/imronsbr/Inventorypuskesmas.git
   cd Inventorypuskesmas
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Install JS dependencies**:
   ```bash
   npm install
   ```

4. **Setup environment variables**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure the database**:
   Edit the `.env` file with your database credentials.

6. **Run migrations and seeders**:
   ```bash
   php artisan migrate --seed
   ```

7. **Compile assets**:
   ```bash
   npm run dev
   ```

8. **Start the server**:
   ```bash
   php artisan serve
   ```

---

## 🎨 UI Components & Usage

### Toast Notifications

The application uses `vue-toastification` for sleek, non-intrusive notifications.

```javascript
import { useToast } from 'vue-toastification'
const toast = useToast()

// Examples
toast.success('Data saved successfully!')
toast.error('An error occurred!')
toast.warning('Please check your input.')
toast.info('Feature restricted to admins.')
```

### Confirmation Dialogs

Built-in support for `SweetAlert2` confirmation prompts.

```javascript
import { useConfirm } from '@/composables/useConfirm.js'
const confirm = useConfirm()

const confirmed = await confirm({
  title: 'Delete Confirmation',
  text: 'Are you sure you want to delete this record?',
  icon: 'warning',
  confirmButtonText: 'Yes, Delete',
  cancelButtonText: 'Cancel'
})

if (confirmed) {
  // Proceed with deletion logic
}
```

### Standard UI Guidelines

- **Typography**: Optimized font sizes (12px labels, 14px content).
- **Consistency**: Unified spacing (16px form control margins) and DaisyUI themes.
- **Performance**: High-performance data tables using TanStack Table.

---

## 📄 License

This project is licensed under the [MIT License](LICENSE).
