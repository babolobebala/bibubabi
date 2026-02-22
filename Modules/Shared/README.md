# Module Shared

## Fungsi
Module `Shared` berisi komponen UI/layout reusable yang dipakai lintas module internal.

Fokus utama saat ini:
- shell layout internal (sidebar + navbar + content slot)
- template hub module (search + toggle grid/list + breadcrumb)

## Komponen Penting
- `Modules/Shared/resources/js/components/layouts/SharedModuleLayout.vue`
  - layout internal aplikasi
  - dipasang otomatis sebagai persistent layout (via `resources/js/app.ts`)
- `Modules/Shared/resources/js/components/modules/ModuleHubContent.vue`
  - template konten tengah untuk halaman hub module
  - mendukung breadcrumb, search, grid/list view, dan item navigation

## Catatan Implementasi
- Layout internal hanya diterapkan ke module page non-`Umum`.
- Navigasi internal memakai `Link` dari Inertia.
- Active state nav sidebar/bottom nav dibaca dari `usePage().url`.

## Route / Controller Shared
Scaffolding route/controller module `Shared` masih ada (default module skeleton), tetapi saat ini fungsi utamanya berada di komponen frontend reusable.
