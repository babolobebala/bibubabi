# Module Tool

## Fungsi
Module `Tool` berisi fitur-fitur utilitas internal dan konfigurasi menu untuk ditampilkan di `Core`.

Contoh fitur aktif saat ini:
- `Geotagging Gambar`

## Route Web
- `GET /app/tools/geotagging-gambar` -> halaman fitur geotagging

File:
- `Modules/Tool/routes/web.php`
- `Modules/Tool/app/Http/Controllers/ToolController.php`

## Inertia Pages
- `tool::GeoTagging` -> `Modules/Tool/resources/js/pages/GeoTagging.vue`

## Pola UX yang Dipakai
- Daftar menu `Tool` (level-2) tidak lagi dirender di route hub terpisah.
- Menu `Tool` ditampilkan di `Core` melalui `/app#tools` berdasarkan `module-navigation.json`.
- Klik item menu di `Core` -> masuk ke page fitur (`/app/tools/geotagging-gambar`).

## Catatan
- Navigasi internal dihubungkan memakai `Link` dari Inertia agar transisi lebih seamless.
- `GeoTagging.vue` sekarang wrapper page tipis; konten utama berada di `Modules/Tool/resources/js/components/GeoTaggingContent.vue`.
- Konfigurasi menu + breadcrumb didefinisikan di `Modules/Tool/resources/js/config/module-navigation.json`.
- Helper lokal akses config ada di `Modules/Tool/resources/js/lib/navigation.ts`.
