<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): Response
    {
        $roles = Role::query()
            ->withCount('users')
            ->orderBy('name')
            ->get()
            ->map(fn (Role $role) => [
                'id' => $role->id,
                'name' => $role->name,
                'users_count' => $role->users_count,
            ]);

        return Inertia::render('admin::RoleIndexPage', [
            'roles' => $roles,
        ]);
    }
}
