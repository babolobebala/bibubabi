# PR-001: Inisialisasi Modul Core
**Tanggal**: 9 Maret 2026  
**Modul**: Core  

## 📝 Deskripsi Singkat
Inisialisasi `Core` modul sebagai hub utama aplikasi dan pemilik rute beranda (`/app`). Modul ini bertugas menampilkan dashboard (*Hub*) dan membaca menu navigasi dinamis dari modul-modul turunan lainnya di sistem.

## 🚀 Perubahan (Changelog)
- **Hub Utama Aplikasi**: Meletakkan halaman terpusat `CorePage.vue` untuk memutar kerangka *shell* dashboard.
- **Pemindaian Modul Otomatis**: Membangun algoritma pembacaan menu dinamis (`import.meta.glob`) untuk memindai berkas `module-navigation.json` dari seluruh sub-modul lain di tingkat klien.
- **Proteksi Akses**: Mengintegrasikan `Role-Based Visibility` sehingga tile modul yang muncul pada *Hub* hanya relevan dengan *Role* yang dipegang pengguna.
