# Module Core

## Fungsi
Module `Core` berperan sebagai **hub utama internal app** (`/app`) untuk menampilkan menu module aktif dan submenu (level-2) secara terpusat.

Pola UX saat ini:
- `GET /app` -> menu level-1 (daftar module)
- `GET /app#tools`, `GET /app#know` -> section submenu level-2 via hash (tetap dirender `CorePage`)
- klik submenu -> masuk ke URL page fitur/module masing-masing

## Route Web
- `GET /app` -> `CoreController@index`

File:
- `Modules/Core/routes/web.php`
- `Modules/Core/app/Http/Controllers/CoreController.php`

## Inertia Page
- `core::CorePage` -> `Modules/Core/resources/js/pages/CorePage.vue`

## Catatan Implementasi
- Layout internal tidak dibungkus manual di page.
- `SharedModuleLayout` dipasang otomatis via persistent layout di `resources/js/app.ts` (kecuali module `Umum`).
- UI menu sekarang dirender langsung di `CorePage` (search + grid/list + cards).
- Orkestrasi data menu + hash section dipisah ke `Modules/Core/resources/js/lib/core-menu.ts`.
- `Core` membaca `module-navigation.json` dari module aktif secara otomatis via `import.meta.glob`.
