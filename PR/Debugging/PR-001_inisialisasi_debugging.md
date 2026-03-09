# PR-001: Inisialisasi Modul Debugging
**Tanggal**: 9 Maret 2026  
**Modul**: Debugging  

## 📝 Deskripsi Singkat
Pembuatan modul debugging terpisah dengan spesifikasi khusus. Modul ini difungsikan untuk memisahkan route pengujian, modifikasi *developer mode*, dan *file dump* kotor agar kode rute utama pada modul *Core* di masa depan tetap dalam keadaan bersih (Clean Code).

## 🚀 Perubahan (Changelog)
- **Pemisahan Logika (Decoupling)**: Secara masif memindahkan serta me-refactor seluruh *hardcoded routes debugging* yang berada di `routes/web.php` *base root*.
- **Endpoint Uji Coba**: Integrasi halaman percobaan komponen spesifik *developer tools* tanpa membebani memori rute komersial aplikasi.
