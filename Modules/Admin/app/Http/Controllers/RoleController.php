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
            ->with(['users' => function ($query) {
                $query->orderBy('nip_baru', 'asc');
            }])
            ->withCount('users')
            ->orderBy('name')
            ->get()
            ->map(fn (Role $role) => [
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description,
                'users_count' => $role->users_count,
                'users' => $role->users->map(fn ($user) => [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'nip_baru' => $user->nip_baru,
                    'email_bps' => $user->email_bps,
                    'email_gmail' => $user->email_gmail,
                    'status_pegawai' => $user->status_pegawai,
                    'url_foto' => $user->url_foto,
                ]),
            ]);

        return Inertia::render('admin::RoleIndexPage', [
            'roles' => $roles,
        ]);
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:255',
        ]);

        Role::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'guard_name' => 'web',
        ]);

        return back()->with('success', 'Role berhasil ditambahkan');
    }
}
