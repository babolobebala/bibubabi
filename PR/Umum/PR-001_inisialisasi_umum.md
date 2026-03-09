# PR-001: Inisialisasi Modul Umum
**Tanggal**: 9 Maret 2026  
**Modul**: Umum  

## 📝 Deskripsi Singkat
Inisialisasi modul Umum yang dikhususkan untuk menampilkan antarmuka *Landing Page* publik bagi pengunjung sistem yang belum melalui proses otorisasi (*unauthenticated state*).

## 🚀 Perubahan (Changelog)
- **Tampilan Utama**: Membangun antarmuka halaman selamat datang sebagai jendela utama sistem (`/`).
- **Autentikasi Internal (Login)**: Implementasi global komponen `LoginDialog.vue` untuk akses masuk kredensial ke area internal aplikasi dengan validasi sesi.
- **SSO BPS Integration**: Menyiapkan fitur *Single Sign-On* untuk sistem BPS secara terpusat di halaman muka situs.
