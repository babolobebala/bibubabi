# PR-002: Standarisasi Kerangka Tata Letak Sentral (The App Shell)
**Tanggal**: 9 Maret 2026  
**Modul**: Shared  
**Tipe**: Architecture & Layout

## 📝 Tujuan
Mendefinisikan kerangka eksterior yang akan menopang semua modul di sepanjang umur aplikasi Saku, mulai dari bilah navigasi samping (*Sidebar*), tajuk profil, hingga komponen wadah penyangga konstan. Fitur personalisasi pengaturan kredensial rahasia pengguna login juga dilokalisasi dalam lapisan netral ini.

## 🛠️ Rincian Perubahan Terkategorisasi

### 🧩 Layout Global (Shell UI)
- `SharedModuleLayout.vue`: Kerangka induk komprehensif penampung rute aplikasi yang menyediakan *Sidebar Navigation*, pengaturan mode gelap-terang (*Dark Mode*), menu hamburger (*Mobile Drawer*), dan konfigurasi profil pojok.
- `ModuleContentShell.vue` & `ModuleHubContent.vue`: Mendirikan komponen lapis dua sebagai kanvas bagi modul-modul lain (*Slot Children*) agar konsisten mewarisi ruang napas *padding* dan *Breadcrumbs* yang diseragamkan.

### ⚙️ Modifikasi Sentral (Personal Credential Modals)
Melingkupi komponen antarmuka swakelola yang dijangkarkan ke profil navigasi Sidebar:
- **Dialog Profil Sederhana**: Menyembunyikan himpunan data rumit NIP dari panel *Sidebar* sempit ke sebuah moda *Dialog Expandable* agar panel utama tetap minimalis.
- **Kustomisasi Kontak**: `UpdateMyEmailDialog.vue` 
- **Keamanan Lapis Pengguna**: `UpdateMyPasswordDialog.vue`

### 📱 Responsibilitas Antarmuka
- Menjamin setiap pergerakan bilah menu bersifat luwes di mode *Desktop* maupun tersembunyi (*Hamburger Swipe*) layaknya aplikasi *Native* ketika diakses melalui kapabilitas proyektor gawai bergerak (*Mobile View*).
