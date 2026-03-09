# PR-001: Inisialisasi Modul Admin
**Tanggal**: 9 Maret 2026  
**Modul**: Admin  

## 📝 Deskripsi Singkat
Pembentukan awal Modul Admin untuk menangani manajemen pengguna (*User*) dan kontrol akses berbasis peran (*Role & Permission*). Modul ini diproteksi oleh lapisan *middleware auth* dan *verified*.

## 🚀 Perubahan (Changelog)
- **Pembuatan Struktur Modul**: Inisialisasi menggunakan `laravel-modules` untuk modul Admin.
- **Konfigurasi Navigasi**: Implementasi `module-navigation.json` yang menampilkan menu "Admin" di Hub aplikasi, dengan prasyarat *roles* `Superadmin`.
- **Manajemen User**: Kerangka awal `RoleController` & `UserController` dengan rute terproteksi di bawah prefix `/app/admin`.
- **Pages**: Antarmuka awal untuk halaman tabel Pengguna dan Role menggunakan `DataTable`.
