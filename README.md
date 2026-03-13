<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# SIMP - Sistem Informasi Manajemen Puskesmas

## Toast Notification System

Aplikasi ini menggunakan Vue Toastification untuk menampilkan notifikasi dengan warna yang soft dan cantik. Notifikasi akan muncul di sudut kanan bawah halaman.

### Cara Penggunaan Toast

```javascript
import { useToast } from 'vue-toastification'

const toast = useToast()

// Success notification
toast.success('Data berhasil disimpan!', 'Berhasil')

// Error notification  
toast.error('Terjadi kesalahan!', 'Gagal')

// Warning notification
toast.warning('Harap lengkapi semua field!', 'Peringatan')

// Info notification
toast.info('Fitur ini hanya untuk admin.', 'Informasi')
```

### Konfirmasi dengan SweetAlert2

Untuk konfirmasi, gunakan SweetAlert2 yang akan muncul di tengah halaman:

```javascript
import { useConfirm } from '@/composables/useConfirm.js'

const confirm = useConfirm()

const confirmed = await confirm({
  title: 'Konfirmasi Hapus',
  text: 'Apakah Anda yakin ingin menghapus data ini?',
  icon: 'warning',
  confirmButtonText: 'Ya, Hapus',
  cancelButtonText: 'Batal'
})

if (confirmed) {
  // Proses hapus data
}
```

### Warna Toast

- **Success**: Hijau soft dengan gradient
- **Error**: Merah soft dengan gradient  
- **Warning**: Kuning soft dengan gradient
- **Info**: Biru soft dengan gradient

Semua toast memiliki efek hover dan animasi slide-in dari kanan bawah.

## Input Styling

Aplikasi menggunakan input fields dengan styling yang rapi dan konsisten:

### Form Controls
- **Spacing**: 16px margin bottom antar form control
- **Labels**: 14px font, medium weight, gray-700
- **Helper Text**: 12px font, gray-500

### Buttons
- **Default**: 14px font, medium weight
- **Small**: 32px height, 12px font
- **Large**: 48px height, 16px font

### Tables
- **Font Size**: 14px untuk konten
- **Headers**: 12px font, semibold, gray-700
- **Padding**: 8px vertical, 12px horizontal
- **Background**: Gray-50 untuk header

### Modal
- **Max Width**: Medium size untuk tampilan yang rapi
- **Responsive**: Menyesuaikan dengan konten
- **Consistent Layout**: Form controls tersusun rapi

Semua input fields menggunakan styling default dari DaisyUI dengan penyesuaian spacing dan typography yang konsisten.
