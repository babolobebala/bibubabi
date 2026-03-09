# PR-002: Implementasi Manajemen Pengguna Berbasis Tabel Dinamis
**Tanggal**: [Draf]  
**Modul**: Admin  

## 📝 Deskripsi Eksekutif
Dalam tahap ini, Modul Admin diperkaya dengan antarmuka manajemen pengguna skala penuh yang menggunakan teknologi `DataTable` interaktif. Fokus utamanya adalah menyajikan ratusan ribu data pegawai secara *seamless* sekaligus menyediakan instrumen cepat bagi administrator untuk melakukan modifikasi krusial pada level personalia tanpa harus meninggalkan panel utama.

## 🚀 Fitur & Perubahan Teknis

### 1. Panel Resolusi Pengguna (`UserIndexPage.vue`)
- **Tabel Canggih (DataTable)**: Menampilkan seluruh data pengguna BPS (Nama, NIP, Email, Status Pegawai) dalam satu grid dengan fitur pencarian dan paginasi bawaan.
- **Render Status Warna**: Impelementasi lencana (*Badge*) visual untuk kolom `Status Pegawai` (Misal: Hijau untuk Aktif) demi kemudahan *scanning* secara cepat (Skimming UX).

### 2. Dialog Modifikasi Terpusat
Alih-alih mengalihkan form ke halaman terpisah, pengubahan data dilakukan via *pop-up modal* mengambang yang langsung menyimpan data ke *backend* tanpa *reload*:
- **Modal `UpdateProfileDialog.vue`**: Mengizinkan penggantian `email_gmail`, penyesuaian status kepegawaian, serta manipulasi *Role* yang dipegang pengguna menggunakan `TanStackCombobox`.
- **Modal `UpdatePasswordDialog.vue`**: Antarmuka riset kata sandi dengan validasi keamanan. Dilengkapi indikator aksi `isSubmitting` untuk mencegah *double-click* dari sisi admin.

### 3. Arsitektur Komponen (Pemisahan Logika)
- **Definisi Kolom Dinamis (`user-columns.ts`)**: Menerapkan arsitektur *Clean Code* di mana struktur *header*, fungsi *render* sel, dan pelekatan aksi `onEditDetail` maupun `onEditPassword` didefinisikan secara konstan dan *decoupled* dari rangka utama Vue. 

## 🛡️ Aspek Keamanan
- Penambahan fungsi pelurusan (*clearing*) tumpukan pesan *error backend* milik Inertia (`usePage().props.errors = {}`) otomatis ketika dialog modal tertutup atau dibuka kembali, meminimalisir potensi kebingungan sistem dari sisa validasi sebelumnya.
