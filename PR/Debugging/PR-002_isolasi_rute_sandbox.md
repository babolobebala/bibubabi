# PR-002: Infrastruktur Sandbox & Evakuasi Rute Pengujian
**Tanggal**: 9 Maret 2026  
**Modul**: Debugging  
**Tipe**: Refactoring & DevOps Prep

## 📝 Tujuan
Menciptakan zona karantina kode khusus guna memfasilitasi kebutuhan rute dan eksperimentasi liar pengembang aplikasi. Perombakan radikal ini akan menjamin modul utama *Core* selalu steril (Clean Code) dari ceceran kode riset yang berpotensi *vulnerable* bagi keamanan *Production*.

## 🛠️ Rincian Perubahan Terkategorisasi

### 🌐 Backend & API (Routing Architecture)
- Eksekusi Isolasi: Membedol ratusan rute uji coba sisa-sisa pekerjaan lepas dari jangkar aslinya (`routes/web.php` *Root*) dan menjebloskan spesifikasinya jauh ke dalam partisi isolasi di dalam Modul Debugging.
- Relasi Cerdas: Menata rute baru agar tetap terbaca kompilator dengan *Prefix* terkendali (misal: `/debug/*`) 

### ⚙️ Infrastruktur Logika & Debug
- Deklarasi Pengendali: Penerapan pengurus khusus, agar tiap kali programmer membuat logika rute percobaan baru (seperti cek format excel, cek *dump* notifikasi email), mereka diwajibkan menyuntikkannya lewat *Sandbox Controller* milik modul ini, tanpa merusak *Controller* komersil.
- Kesiapan Ekstensi Observabilitas: Dasar instalasi disiapkan untuk pengintegrasian kakas penganalisa kerja internal secara mendalam (e.g. Laravel Telescope) bilamana kelak dibutuhkan untuk evaluasi *Query Execution Time*.
