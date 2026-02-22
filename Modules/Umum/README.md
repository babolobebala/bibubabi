# Module Umum

## Fungsi
Module `Umum` berisi halaman publik / landing page (non-internal app shell).

Module ini **dikecualikan** dari auto persistent layout `SharedModuleLayout`.

## Route Web
- `GET /welcome` -> `UmumController@welcome_page`

File:
- `Modules/Umum/routes/web.php`
- `Modules/Umum/app/Http/Controllers/UmumController.php`

## Inertia Page
- `umum::WelcomePage` -> `Modules/Umum/resources/js/pages/WelcomePage.vue`

## Catatan
- Karena sifatnya halaman publik/landing, layout dan navigasi visualnya berdiri sendiri.
- Jika ingin memakai shell internal, lakukan secara eksplisit (jangan mengandalkan auto-layout default).
