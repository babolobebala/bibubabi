---
description: menambahkan feature page baru di dalam module yang sudah ada
---

## Konteks

Workflow ini digunakan untuk menambahkan halaman fitur baru ke dalam module yang sudah ada (bukan membuat module baru).
Sebelum mulai, tanyakan user:
1. Module tujuan (contoh: `Tool`, `Know`)
2. Nama fitur (contoh: `Generator Dokumen`)
3. Slug fitur (lowercase-dash, contoh: `generator-dokumen`)
4. Apakah fitur perlu backend (Model, Controller method baru) atau hanya frontend page?
5. Deskripsi singkat untuk menu

---

## Langkah

### 1. Tambah entry ke `module-navigation.json`

Edit `Modules/{Name}/resources/js/config/module-navigation.json`, tambahkan entry baru di `pages[]`:

```json
{
    "key": "{slug-fitur}",
    "title": "{Judul Fitur}",
    "level": 2,
    "href": "/app/{module-slug}/{slug-fitur}",
    "componentKey": "{module-key}.{slug-fitur}",
    "description": "{deskripsi fitur}",
    "iconImage": "img/logo/logo.png"
}
```

### 2. Tambah route baru

Edit `Modules/{Name}/routes/web.php`, tambahkan route di dalam group yang sudah ada:

```php
Route::get('/{slug-fitur}', [{Name}Controller::class, '{methodName}'])->name('{slug-fitur}');
```

### 3. Tambah method di Controller

Edit `Modules/{Name}/app/Http/Controllers/{Name}Controller.php`:

```php
public function {methodName}(): \Inertia\Response
{
    return Inertia::render('{name}::{FeatureName}', [
        // props jika ada
    ]);
}
```

### 4. Buat Inertia page component

Buat `Modules/{Name}/resources/js/pages/{FeatureName}.vue`.

Pola yang dianjurkan — pisahkan konten ke komponen terpisah:
```
Modules/{Name}/resources/js/pages/{FeatureName}.vue       ← wrapper tipis
Modules/{Name}/resources/js/components/{FeatureName}Content.vue  ← konten utama
```

Page wrapper (tipis):
```vue
<script setup lang="ts">
import { ModuleContentShell } from '@shared/components'
import {FeatureName}Content from '../components/{FeatureName}Content.vue'
</script>

<template>
  <ModuleContentShell :module="{module-slug}" :page="'{slug-fitur}'" body-variant="page">
    <{FeatureName}Content />
  </ModuleContentShell>
</template>
```

Ingat: **jangan set layout manual** di page.

### 5. Cek komponen Shared yang bisa dipakai ulang

Sebelum membuat komponen baru, cek:
- `Modules/Shared/resources/js/components/` untuk komponen UI reusable
- Gunakan komponen `shadcn-vue` yang tersedia (lihat `components.json`)

### 6. Jika fitur perlu Model / data backend

Buat migration dan model:
```bash
php artisan module:make-model {ModelName} {ModuleName} --migration --factory --no-interaction
```

Pastikan factory dan seeder juga dibuat untuk keperluan testing.

### 7. Restart dev server / build

Setelah selesai, minta user untuk me-refresh dev server atau:
```bash
pnpm run build
```

---

## Checklist Akhir

- [ ] Entry baru sudah ada di `pages[]` `module-navigation.json`
- [ ] Route baru sudah terdaftar dengan benar
- [ ] Controller method mereturn `Inertia::render()`
- [ ] Page component ada di `resources/js/pages/`
- [ ] Konten utama dipisah ke `components/` jika kompleks
- [ ] Layout **tidak** di-set manual di page
- [ ] Breadcrumb tampil benar via `ModuleContentShell`
- [ ] Fitur muncul di menu hub module setelah reload
