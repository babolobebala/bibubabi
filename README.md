# Saku NG

Project modular Laravel 12 + Inertia v2 + Vue 3 + Vite untuk aplikasi internal dengan pola navigasi berbasis module.

## Stack
- Laravel 12
- Inertia.js v2 (`@inertiajs/vue3`)
- Vue 3
- Tailwind CSS v4 + shadcn-vue components
- `nwidart/laravel-modules`

## Requirements
- PHP 8.3+ (project ini disiapkan untuk PHP 8.3)
- Composer 2
- Node.js 22
- pnpm 10.x

## Menjalankan Project
1. Install dependency PHP dan JS.
2. Jalankan server Laravel + Vite dev server.

```bash
composer install
pnpm install
php artisan key:generate
php artisan serve
pnpm run dev
```

Catatan:
- Jika perubahan frontend tidak muncul, pastikan `pnpm run dev` aktif.
- Jika memakai Laravel Herd, akses project via domain `.test` yang sesuai.

## Arsitektur Module (Ringkas)
Project memakai folder `Modules/`:
- `Core` -> hub utama (`/home`) untuk daftar module internal
- `Tool` -> hub module Tools (`/tools`) + feature pages (contoh: geotagging)
- `Know` -> module halaman knowledge (contoh page)
- `Shared` -> shared UI/layout reusable antar module
- `Umum` -> halaman publik/landing (dikecualikan dari internal persistent layout)

## Pola Routing yang Dipakai
Pola yang dipakai untuk UX modular:
- `/{module}` = halaman hub module
- `/{module}/{feature}` = halaman fitur di dalam module

Contoh:
- `/home` -> hub global module
- `/tools` -> hub module Tools
- `/tools/geotagging-gambar` -> fitur geotagging gambar

## Layout & Navigasi Internal
- Layout internal utama menggunakan `SharedModuleLayout`
- Persistent layout dipasang otomatis dari `resources/js/app.ts` untuk semua module page **kecuali `Umum`**
- Navigasi internal menggunakan `Link` dari Inertia (bukan `<a>`) agar transisi lebih seamless

## File Penting
- `resources/js/app.ts` -> resolver Inertia + auto persistent layout module
- `Modules/Shared/resources/js/components/layouts/SharedModuleLayout.vue` -> shell layout internal
- `Modules/Shared/resources/js/components/modules/ModuleHubContent.vue` -> template reusable halaman hub module (grid/list + search)

## Dokumentasi Module
Lihat `README.md` di masing-masing folder module:
- `Modules/Core/README.md`
- `Modules/Know/README.md`
- `Modules/Shared/README.md`
- `Modules/Tool/README.md`
- `Modules/Umum/README.md`
