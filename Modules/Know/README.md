# Module Know

## Fungsi
Module `Know` adalah module knowledge / halaman contoh konten yang saat ini dipakai untuk uji tampilan dan integrasi shared layout.

## Route Web
Module ini saat ini masih menggunakan resource route:
- `Route::resource('knows', KnowController::class)->names('know')`

File:
- `Modules/Know/routes/web.php`
- `Modules/Know/app/Http/Controllers/KnowController.php`

## Inertia Page
- `know::KnowPage` -> `Modules/Know/resources/js/pages/KnowPage.vue`

## Catatan
- `KnowPage` sekarang tidak perlu membungkus `SharedModuleLayout` secara manual.
- Layout internal dipasang otomatis via persistent layout (`resources/js/app.ts`).
- Halaman ini bisa dijadikan referensi untuk konten tengah pada shell layout internal.
