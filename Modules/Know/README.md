# Module Know

## Fungsi
Module `Know` adalah module knowledge (contoh awal) yang menyediakan page list/create dan konfigurasi menu untuk `Core`.

## Route Web
Module ini saat ini masih menggunakan resource route:
- `Route::resource('knows', KnowController::class)->names('know')`

File:
- `Modules/Know/routes/web.php`
- `Modules/Know/app/Http/Controllers/KnowController.php`

## Inertia Pages
- `know::KnowPage` -> `Modules/Know/resources/js/pages/KnowPage.vue`
- `know::KnowCreatePage` -> `Modules/Know/resources/js/pages/KnowCreatePage.vue`

## Catatan
- `KnowPage` sekarang tidak perlu membungkus `SharedModuleLayout` secara manual.
- Layout internal dipasang otomatis via persistent layout (`resources/js/app.ts`).
- Menu `Know` (level-2: lihat/buat knowledge) ditampilkan di `Core` via `/app#know`.
- Konfigurasi menu + metadata module ada di `Modules/Know/resources/js/config/module-navigation.json`.
- Helper lokal akses config ada di `Modules/Know/resources/js/lib/navigation.ts`.
