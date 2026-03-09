# PR-003: Hub Sistem Kendali Role (RBAC) & Pendelegasian
**Tanggal**: 9 Maret 2026  
**Modul**: Admin  
**Tipe**: Feature & Enhancement

## 📝 Tujuan
Menyediakan pusat kendali utuh untuk pengelolaan jabatan/peran (*Role*) aplikasi. Mengakomodasi mekanisme CUD (*Create, Update, Delete*) serta sistem pendelegasian massal, di mana administrator dapat mencopot atau mengangkat puluhan pegawai ke sebuah *Role* dalam satu kali klik.

## 🛠️ Rincian Perubahan Terkategorisasi

### 🌐 Backend & API (Rute & Controller)
- `routes/web.php`: Melengkapi trio rute esensial untuk `roles.store`, `roles.update`, dan `roles.destroy`.
- `RoleController.php`:
  - Menambahkan *query* optimalisasi `withCount('users')` untuk mengetahui populasi setiap *Role*.
  - Mengirim pasokan data mentah agregat `$availableUsers` sebagai kamus opsi pegawai.
  - Menerima dan mengeksekusi perintah `$role->users()->sync()` dari susunan *array user ID* tatkala operasi *Update* dijalankan.

### 🧩 Frontend & UI (Komponen Vue)
- Baris Ekspansif (`role-columns.ts`): Memperkenalkan logika *Toggle Row Expansion* bervisual tombol (*Plus/Minus*). Baris yang diekspansi menyibak subsistem `RoleExpandedRow.vue` berisi grid penyandang *Role* yang diurutkan by NIP Baru.
- Evolusi *Dropdown* (`TanStackCombobox.vue`): Melakukan pemutakhiran drastis komponen dasar sistem agar mampu membedakan dan merender struktur variabel ganda objek `{label, value}` (Tampil Nama, Simpan ID).
- Antarmuka Operasional (Modals): 
  - `CreateRoleDialog.vue` (Tambah)
  - `UpdateRoleDialog.vue` (Ubah Data & *Assign User*)
  - `DeleteRoleDialog.vue` (Konfirmasi native UI)

### 🛡️ Keamanan & Integrasi Kendali
- **Foreign-Key Logical Shield**: Menerapkan blokade pemusnahan mutlak di lapis *Controller* (`destroy`). Jika variabel `$role->users()->count()` mendeteksi masih ada sel terikat, operasi penghapusan akan dipatahkan secara paksa dan memantulkan status *Error Validasi* untuk mencegah korupsi arsitektur data *user*.
