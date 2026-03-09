# PR-002: Ekosistem Menu Dinamis (Dynamic Hub Navigator)
**Tanggal**: 9 Maret 2026  
**Modul**: Core  
**Tipe**: Architectural Core Feature

## 📝 Tujuan
Membebaskan Core Hub dari beban pengaturan menu yang terpasung (*hard-coded*). Aplikasi secara ajaib dapat "meraba" keberadaan modul fungsional di pelbagai pelosok direktori *project* dan menyusun panel menunya sendiri, lengkap degan filtrasi tingkat sekuritas.

## 🛠️ Rincian Perubahan Terkategorisasi

### ⚙️ Engine Layer (Parsing & Aggregation)
- Integrasi Vite Bundler API (`import.meta.glob`): Mengatur Core agar senantiasa mensintesis berkas `module-navigation.json` yang dimiliki subsistem modul eksternal (Admin, Tool, Know) layaknya penarikan *manifest plugin*.
- `module-navigation.ts`: Menyimpan abstraksi algoritma pembacaan hierarki.  

### 🧩 Frontend & UI (Komponen Vue)
- Sentralisasi Panel (`CorePage.vue`): Merender sekumpulan *Card Modules* (Tile kotak-kotak raksasa) berbasis data dinamis hasil ekstraksi `import.meta.glob`, dipecah menjadi kelompok fitur utama.
- Layanan Sekunder: Pembangunan `QuickMenuPage.vue` (tombol *shortcut*) dan `NotificationPage.vue` (notifikasi komunal).

### 🛡️ Keamanan & Visibility Matriks
- **Role-Based Visibility Filter**: Menciptakan fungsi penyaring yang mencocokkan konfigurasi *required Role* milik modul eksternal dengan manifest *User Role* di sesi saat ini. Menjamin modul sensitif (spesifik *Superadmin/Approval*) secara gaib hilang dari pandangan pengguna biasa/operator sebelum sempat tersentuh.
