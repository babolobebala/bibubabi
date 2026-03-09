# PR-002: Portal Publik & Arsitektur Single Sign-On (SSO)
**Tanggal**: 9 Maret 2026  
**Modul**: Umum  
**Tipe**: Feature

## 📝 Tujuan
Mengembangkan gerbang masuk (*Landing Page*) publik untuk menyambut pengguna anonim, sekaligus membangun arsitektur login terapung (*Floating Modal Login*) dan menyambut benih integrasi *Single Sign-On* milik ekosistem terpusat Badan Pusat Statistik (OASIS BPS).

## 🛠️ Rincian Perubahan Terkategorisasi

### 🌐 Backend & API (Rute & Controller)
- Terbuka Bebas: Mendaftarkan rute awal `/` pada `web.php` milik modul Umum tanpa penjagaan *middleware auth*. Modul ini diperkenankan melayani *request* dari *Guest*.
- Arus OASIS: Deklarasi penempatan rel rute *Single Sign-On* masa depan (`/sso-bps` & `/callback/sso-bps`).

### 🧩 Frontend & UI (Komponen Vue)
- **Halaman Beranda (`WelcomePage.vue`)**: Etalase statis pembuka sistem berbasis Vue Inertia yang ramah-publik tanpa menembak peringatan otorisasi.
- **Komponen Autentikator Moduler**: 
  - `LoginDialog.vue`: Mengonversi form login tradisional berbasis halaman menjadi entitas antarmuka *pop-up* yang anggun, responsif, dan bisa dipantik dari tombol manapun di *landing page*.
  - `SharedLoginContent.vue`: Mengekstrak inti formulir ke file agnostik untuk memupuk *Reusability*.

### 🛡️ Keamanan & Penetrasi Pengguna
- **Integritas *CSRF* & Sesi**: Mempertahankan perlindungan sesi token otentikasi konvensional (`@csrf`) Laravel yang disemburkan dari dalam jalinan komponen *Single Page Application* milik Vue, menekan rasio pencurian masuk tak berizin.
