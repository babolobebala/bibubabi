# PR-001: Inisialisasi Modul Shared
**Tanggal**: 9 Maret 2026  
**Modul**: Shared  

## 📝 Deskripsi Singkat
Modul sentral komponen yang tidak memiliki rute fitur mandiri, melainkan berfungsi meletakkan kerangka aplikasi utama (*Layout Shell*) serta kumpulan komponen antarmuka yang sifatnya *reusable* untuk seluruh modul di aplikasi.

## 🚀 Perubahan (Changelog)
- **Shared Layout Shell**: Pembuatan `SharedModuleLayout.vue` sebagai pembungkus utama antarmuka (Sidebar + Navbar Header + Content).
- **Core Helper Functions**: Penyimpanan kumpulan logika sistem antarmuka inti (seperti `lib/module-navigation.ts` untuk fungsi parsing modul).
- **Komponen Turunan Global**: Menyiapkan elemen komponen seperti `ModuleContentShell` dan `ModuleHubContent`.
- **Modal Profil & Keamanan**: Pembuatan form ganti password personal dan perubahan kredensial Gmail dalam sub-komponen dialog interaktif global.
