---
description: create a new Laravel module with nwidart, routing, Inertia page, and module-navigation.json
---

## Context

A new module will be added to `Modules/` using `nwidart/laravel-modules` (v13 compatible).
Before starting, ask the user for:
1. Module name (PascalCase, e.g., `Report`)
2. Module slug / key (lowercase-dash, e.g., `report`)
3. Brief description of the module (for `module-navigation.json`)
4. Does the module need a backend (Model, Controller, migration) or only a frontend?

---

## Steps

### 1. Generate scaffold module

```bash
php artisan module:make {Name} --no-interaction
```
*(Note: Do not use the `--inertia` flag provided by v13, as we need to maintain our custom `SharedModuleLayout` hub/page setup and specific directory structures.)*

### 2. Clean up unnecessary stub files

Delete or empty the default controller if not relevant. Update `module.json` with the correct metadata:
- `name`, `description`, `keyword`, `providers`

### 3. Create `module-navigation.json`

Create the file `Modules/{Name}/resources/js/config/module-navigation.json`:

```json
{
    "module": {
        "key": "{slug}",
        "name": "{Name}",
        "title": "{Display Title}",
        "anchor": "{slug}",
        "description": "{module description}",
        "iconImage": "img/logo/logo.png"
    },
    "pages": []
}
```

### 4. Create web route

Edit `Modules/{Name}/routes/web.php`:

```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('app/{slug}')->name('{slug}.')->group(function () {
        Route::get('/', [{Name}Controller::class, 'index'])->name('index');
    });
});
```

### 5. Update Controller

Edit `Modules/{Name}/app/Http/Controllers/{Name}Controller.php` to add the `index` method:

```php
public function index(): \Inertia\Response
{
    return Inertia::render('{name}::{Name}Page');
}
```

### 6. Create main Inertia page

Create `Modules/{Name}/resources/js/pages/{Name}Page.vue`:

```vue
<script setup lang="ts">
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';
import {
    getModulePageBreadcrumbs,
    type ModuleNavigationConfig,
} from '../../../../Shared/resources/js/lib/module-navigation';
import moduleNavigation from '../config/module-navigation.json';

const pageBreadcrumbs = getModulePageBreadcrumbs(moduleNavigation as ModuleNavigationConfig, '');
</script>

<template>
  <ModuleContentShell :module="{slug}" body-variant="hub" :breadcrumbs="pageBreadcrumbs">
    <!-- content -->
  </ModuleContentShell>
</template>
```

Remember: **do not set the layout manually** — `SharedModuleLayout` is automatically applied via `app.ts`.

### 7. Register module in Vite (if there are frontend assets)

Check the root `vite.config.ts` (or `vite.config.js`) and ensure the new module is covered by the glob include pattern. See the pattern from the `Tool` module as a reference for `Modules/{Name}/vite.config.js` or `vite.config.ts`.

### 8. Run migrate (if there's a new migration)

```bash
php artisan migrate
```

### 9. Build / restart dev server

Ask the user to restart the dev server or run:

```bash
pnpm run build
```

---

## Final Checklist

- [ ] `module.json` is correctly filled out
- [ ] `module-navigation.json` exists and is valid
- [ ] Route is registered with `auth` + `verified` middleware
- [ ] Controller returns `Inertia::render()`
- [ ] Inertia page is located in `resources/js/pages/`
- [ ] Layout is **not** manually set on the page
- [ ] Module appears in the `/app` menu after build
