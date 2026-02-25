# Module Shared

## Fungsi
Module `Shared` berisi komponen UI/layout reusable yang dipakai lintas module internal.

Fokus utama saat ini:
- shell layout internal (sidebar + navbar + content slot)
- shell konten module (breadcrumb + body wrapper)
- helper parsing/generator `module-navigation`

## Komponen Penting
- `Modules/Shared/resources/js/components/layouts/SharedModuleLayout.vue`
  - layout internal aplikasi
  - dipasang otomatis sebagai persistent layout (via `resources/js/app.ts`)
- `Modules/Shared/resources/js/components/modules/ModuleContentShell.vue`
  - shell reusable untuk breadcrumb + wrapper body
  - mendukung preset `body-variant` (`hub` / `page`)
- `Modules/Shared/resources/js/components/modules/ModuleHubContent.vue`
  - komponen menu hub reusable (masih tersedia)
  - mendukung breadcrumb, search, grid/list view, dan item navigation (`href` / `onClick`)
- `Modules/Shared/resources/js/lib/module-navigation.ts`
  - schema & helper `module-navigation.json`
  - generator menu level-2 + breadcrumb page otomatis dari `module + pages`

## Catatan Implementasi
- Layout internal hanya diterapkan ke module page non-`Umum`.
- Navigasi internal memakai `Link` dari Inertia.
- Active state nav sidebar/bottom nav dibaca dari `usePage().url`.
- `module-navigation` sekarang disederhanakan: `module` + `pages` (dengan `level`), tanpa blok `hubs` di config.

## Route / Controller Shared
Scaffolding route/controller module `Shared` masih ada (default module skeleton), tetapi saat ini fungsi utamanya berada di komponen frontend reusable.
