# PR-002: Cetak Biru Micro-Frontend Utilitas (Toolbox)
**Tanggal**: 9 Maret 2026  
**Modul**: Tool  
**Tipe**: Feature Architecture

## 📝 Tujuan
Menyulap rute perkakas (misalnya: Format Data, Pengganda Kode, Hitung Pajak, dsb) agar beroperasi bagai ekosistem pelengkap aplikasi independen layaknya "Aplikasi Mikro" (Micro-Frontend). Arsitektur ini tidak membenarkan ikatan erat (*tight coupling*) utilitas mini dengan pangkalan data master aplikasi.

## 🛠️ Rincian Perubahan Terkategorisasi

### 🌐 Backend & API (Moda Stateless)
- Pembatasan Pihak Ketiga: Mewajibkan *Controller* alat utilitas tidak melakukan operasi *Create/Update* radikal terhadap Tabel Inti (*Users, Roles*) kecuali dibutuhkan instrumen mutlak.
- Konfigurasi Relasi Hub: Rute fungsional ini hanya di-*expose* ujung jarinya via *manifest JSON* kepada peramban `Core Hub`.

### 🧩 Frontend & UI (Sistem Layar Terbagi)
- Kebebasan Form: Mengizinkan *Engineer* ke depan menyisipkan halaman `.vue` fungsional murni form matematika/eksperimen alat tanpa harus mewarisi *overhead* muatan besar dari komponen modul lain.
- Visual Identitas: Pembuatan *tile navigasi* khas yang beridentitas warna-warna pelengkap (di luar parameter warna bisnis utama).
