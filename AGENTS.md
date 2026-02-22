<laravel-boost-guidelines>

# AGENTS (Optimized / Token-Saving)

Gunakan aturan ini sebagai ringkasan operasional untuk project ini.

## Stack & Context (Wajib Diingat)
- Laravel 12, PHP 8.3
- Inertia v2 (`@inertiajs/vue3`)
- Vue 3 + Tailwind CSS v4
- Pest / PHPUnit
- Laravel Modules (`Modules/*`)
- shadcn-vue components di `resources/js/components/ui`

## Prinsip Utama
- Ikuti konvensi file/struktur yang sudah ada (cek sibling file dulu).
- Jangan ubah dependency tanpa izin.
- Jangan buat folder base baru tanpa izin.
- Prioritaskan reuse komponen sebelum bikin baru.
- Jawaban singkat dan fokus.

## Dokumentasi
- Buat/update dokumentasi hanya jika diminta user.

## Laravel / Backend Rules
- Gunakan cara Laravel (Form Request untuk validasi, Eloquent relationship, named routes).
- Hindari `DB::` jika Eloquent/Query Builder cukup.
- Gunakan `php artisan make:* --no-interaction` untuk file baru bila relevan.
- Jangan pakai `env()` di luar file config.
- PHP:
  - wajib type hints + return types
  - pakai curly braces untuk control structures
  - constructor property promotion jika cocok
  - prefer PHPDoc daripada inline comment

## Laravel 12 Notes
- Middleware/config routing utama ada di `bootstrap/app.php`
- Struktur Laravel 12 streamlined (tidak pakai `app/Http/Kernel.php` lama)
- Saat modify kolom migration, definisikan ulang atribut kolom lengkap

## Inertia / Vue Rules
- Gunakan `Inertia::render()` untuk page module/internal app.
- Vue component wajib single root element.
- Internal navigation gunakan `Link` dari `@inertiajs/vue3` (bukan `<a>`) untuk route internal.
- Pattern app internal:
  - `/home` = hub global
  - `/{module}` = hub module
  - `/{module}/{feature}` = feature page
- `SharedModuleLayout` adalah shell internal reusable.
- `Umum` dikecualikan dari internal persistent layout (landing/public).

## Tailwind / Frontend Rules
- Ikuti token/theme semantic yang ada (`background`, `card`, `primary`, dll).
- Prefer komponen shadcn-vue untuk UI pieces; layout shell boleh HTML + Tailwind.
- Jangan hardcode warna jika token semantic sudah tersedia.

## Testing / Verification
- Prioritaskan test dibanding script verifikasi ad-hoc.
- Jika user tidak melihat perubahan frontend, ingatkan `pnpm run dev` / `pnpm run build`.
- Jika ubah PHP:
  - jalankan `vendor/bin/pint --dirty --format agent`
- Gunakan lint/test secukupnya pada file yang diubah.

## Laravel Boost / MCP (Jika Tersedia)
- Pakai `search-docs` untuk referensi Laravel/Inertia/Tailwind/Pest (version-aware).
- Pakai tools Boost untuk artisan/db/browser logs jika task membutuhkannya.
- Gunakan query docs yang sederhana dan broad (tanpa nama package di query).

## Route / URL / Wayfinder
- Prefer named routes + `route()`.
- Saat frontend butuh route TS, gunakan Wayfinder sesuai pola project.

## Praktik Modular (Project Ini)
- `Core`: hub menu module (`/home`)
- `Tool`: hub module + feature pages (`/tools`, `/tools/geotagging-gambar`)
- `Shared`: shared layout/components
- `Umum`: public/landing page

</laravel-boost-guidelines>
