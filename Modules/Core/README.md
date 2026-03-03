# Module Core

## Fungsi
Module `Core` berperan sebagai **hub utama internal app** (`/app`) dan owner route publik `welcome`.

Pola UX saat ini:
- `GET /welcome` -> halaman publik, dirender oleh `Core`, kontennya di-load dari module `Umum` bila aktif
- `GET /app` -> menu level-1 (daftar module)
- `GET /app#tools`, `GET /app#know` -> section submenu level-2 via hash (tetap dirender `CorePage`)
- klik submenu -> masuk ke URL page fitur/module masing-masing

## Route Web
- `GET /welcome` -> `CoreController@welcome`
- `GET /app` -> `CoreController@index`

File:
- `Modules/Core/routes/web.php`
- `Modules/Core/app/Http/Controllers/CoreController.php`

## Inertia Page
- `core::WelcomePage` -> `Modules/Core/resources/js/pages/WelcomePage.vue`
- `core::CorePage` -> `Modules/Core/resources/js/pages/CorePage.vue`

## Catatan Implementasi
- Layout internal tidak dibungkus manual di page.
- `SharedModuleLayout` dipasang otomatis via persistent layout di `resources/js/app.ts`.
- Page tertentu bisa opt-out layout secara eksplisit dengan `layout: null`.
- `WelcomePage` memakai `loadModuleComponent()` untuk me-load `Modules/Umum/resources/js/components/landing-page/WelcomeContent.vue` hanya jika module `Umum` aktif.
- Jika module `Umum` nonaktif, fallback `WelcomePage` memakai komponen login dari module `Shared`.
- UI menu sekarang dirender langsung di `CorePage` (search + grid/list + cards).
- Orkestrasi data menu + hash section dipisah ke `Modules/Core/resources/js/lib/core-menu.ts`.
- `Core` membaca `module-navigation.json` dari module aktif secara otomatis via `import.meta.glob`.
