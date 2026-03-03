# Module Umum

## Fungsi
Module `Umum` berisi komponen penyusun halaman publik / landing page.

Ownership route/page publik utama sekarang berada di module `Core`, tetapi konten landing page tetap dideklarasikan di module ini.

## Komponen Penting
- `Modules/Umum/resources/js/components/landing-page/WelcomeContent.vue`
  - wrapper konten landing page publik
- `Modules/Umum/resources/js/components/landing-page/LoginDialog.vue`
  - wrapper dialog login untuk navbar landing page
  - isi kontennya memakai komponen shared

## Catatan
- Module ini tidak lagi menjadi owner route `/welcome`.
- Jika ada page dari module ini yang tidak boleh memakai auto layout default, nonaktifkan secara eksplisit di page tersebut.
- Module ini umumnya tidak perlu ikut `module-navigation.json` untuk menu internal `Core`.
