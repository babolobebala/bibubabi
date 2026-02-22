# Module Tool

## Fungsi
Module `Tool` berisi halaman hub Tools dan fitur-fitur utilitas internal.

Contoh fitur aktif saat ini:
- `Geotagging Gambar`

## Route Web
- `GET /tools` -> halaman hub Tools
- `GET /tools/geotagging-gambar` -> halaman fitur geotagging

File:
- `Modules/Tool/routes/web.php`
- `Modules/Tool/app/Http/Controllers/ToolController.php`

## Inertia Pages
- `tool::ToolHubPage` -> `Modules/Tool/resources/js/pages/ToolHubPage.vue`
- `tool::GeoTagging` -> `Modules/Tool/resources/js/pages/GeoTagging.vue`

## Pola UX yang Dipakai
- `/tools` = daftar menu fitur dalam module Tools (hub)
- klik item menu -> masuk ke subpage fitur (`/tools/{feature}`)
- layout kiri/kanan/topbar tetap, konten tengah yang berubah

## Catatan
- Navigasi internal dihubungkan memakai `Link` dari Inertia agar transisi lebih seamless.
- `CobaCoba.vue` lama sudah digantikan oleh `GeoTagging.vue` sebagai page feature aktif.
