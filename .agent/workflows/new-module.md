---
description: membuat module Laravel baru dengan nwidart, routing, Inertia page, dan module-navigation.json
---

## Konteks

Module baru akan ditambahkan ke `Modules/` menggunakan `nwidart/laravel-modules`.
Sebelum mulai, tanyakan user:
1. Nama module (PascalCase, contoh: `Report`)
2. Slug / key module (lowercase-dash, contoh: `report`)
3. Deskripsi singkat module (untuk `module-navigation.json`)
4. Apakah module perlu backend (Model, Controller, migration) atau hanya frontend?

---

## Langkah

### 1. Generate scaffold module

```bash
php artisan module:make {Name} --no-interaction
```

### 2. Bersihkan file stub yang tidak diperlukan

Hapus atau kosongkan controller default jika tidak relevan. Sesuaikan `module.json` dengan metadata yang benar:
- `name`, `description`, `keyword`, `providers`

### 3. Buat `module-navigation.json`

Buat file `Modules/{Name}/resources/js/config/module-navigation.json`:

```json
{
    "module": {
        "key": "{slug}",
        "name": "{Name}",
        "title": "{Judul Tampilan}",
        "anchor": "{slug}",
        "description": "{deskripsi module}",
        "iconImage": "img/logo/logo.png"
    },
    "pages": []
}
```

### 4. Buat `navigation.ts` helper

Buat `Modules/{Name}/resources/js/lib/navigation.ts` mengacu pada pola modul `Tool` atau `Know`:
- Import config dari `../config/module-navigation.json`
- Export fungsi `getModulePages()` dan `getModuleBreadcrumb()`

### 5. Buat route web

Edit `Modules/{Name}/routes/web.php`:

```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('app/{slug}')->name('{slug}.')->group(function () {
        Route::get('/', [{Name}Controller::class, 'index'])->name('index');
    });
});
```

### 6. Update Controller

Edit `Modules/{Name}/app/Http/Controllers/{Name}Controller.php` untuk menambahkan method `index`:

```php
public function index(): \Inertia\Response
{
    return Inertia::render('{name}::{Name}Page');
}
```

### 7. Buat Inertia page utama

Buat `Modules/{Name}/resources/js/pages/{Name}Page.vue`:

```vue
<script setup lang="ts">
// import komponen yang dibutuhkan
</script>

<template>
  <ModuleContentShell :module="{slug}" body-variant="hub">
    <!-- konten -->
  </ModuleContentShell>
</template>
```

Ingat: **jangan set layout manual** — `SharedModuleLayout` otomatis dipasang via `app.ts`.

### 8. Daftarkan module di Vite (jika ada asset frontend)

Cek `vite.config.ts` root dan pastikan module baru tercakup oleh glob include pattern. Lihat pola module `Tool` sebagai referensi `Modules/{Name}/vite.config.js`.

### 9. Jalankan migrate (jika ada migration baru)

```bash
php artisan migrate
```

### 10. Build / restart dev server

Minta user untuk restart dev server atau jalankan:

```bash
pnpm run build
```

---

## Checklist Akhir

- [ ] `module.json` sudah diisi dengan benar
- [ ] `module-navigation.json` sudah ada dan valid
- [ ] Route terdaftar dengan middleware `auth` + `verified`
- [ ] Controller mereturn `Inertia::render()`
- [ ] Page Inertia ada di `resources/js/pages/`
- [ ] Layout **tidak** di-set manual di page
- [ ] Module muncul di menu `/app` setelah build
