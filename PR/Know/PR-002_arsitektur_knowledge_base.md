# PR-002: Arsitektur Pangkalan Pengetahuan Berbasis Direktori
**Tanggal**: 9 Maret 2026  
**Modul**: Know  
**Tipe**: Feature Architecture

## 📝 Tujuan
Pondasi awal sistem *Knowledge Management* BPS untuk menampung ratusan *Standard Operating Procedures* (SOP) dan pedoman manajerial. Sistem dirangkai menggunakan arsitektur perpustakaan *Nested Tree-View* navigasi.

## 🛠️ Rincian Perubahan Terkategorisasi

### 🌐 Backend & API (Tree Structure Schema)
- Mengklasifikasikan skenario basis data yang condong pada *Self-Referencing Table* berlapis (misal: Pola struktur direktori induk-anak ala folder *Windows*) guna memecah kategori pedoman organisasi.

### 🧩 Frontend & UI (Tipografi Sentris)
- Ekosistem Tanpa Tabel: Modul yang direkayasa sedari awal untuk menghindar pemborosan *import* pustaka *DataTable* berat seperti modul Admin, sebab *Know* berfokus mutlak pada presentasi teks (*Typography*).
- Rancangan Navigasi (*Tree View*): Desain arsitektur pentalan (*routing*) bersarang berjenjang, memungkinkan para pekerja merekam jejak URL buku saku spesifik.
