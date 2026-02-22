# Module Core

## Fungsi
Module `Core` berperan sebagai **hub utama internal app** (`/home`) untuk menampilkan daftar module yang tersedia.

Saat ini `CorePage` menggunakan template hub reusable dari module `Shared`.

## Route Web
- `GET /home` -> `CoreController@index`

File:
- `Modules/Core/routes/web.php`
- `Modules/Core/app/Http/Controllers/CoreController.php`

## Inertia Page
- `core::CorePage` -> `Modules/Core/resources/js/pages/CorePage.vue`

## Catatan Implementasi
- Layout internal tidak dibungkus manual di page.
- `SharedModuleLayout` dipasang otomatis via persistent layout di `resources/js/app.ts` (kecuali module `Umum`).
- Konten hub memakai `ModuleHubContent` dari `Shared`.
