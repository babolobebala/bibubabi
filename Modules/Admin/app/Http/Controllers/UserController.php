<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(): Response
    {
        $users = User::query()
            ->with('roles')
            ->orderBy('nama')
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'nama' => $user->nama,
                'nip' => $user->nip,
                'username' => $user->username,
                'email_bps' => $user->email_bps,
                'jabatan' => $user->jabatan,
                'status_pegawai' => $user->status_pegawai,
                'url_foto' => $user->url_foto,
                'roles' => $user->getRoleNames()->values()->all(),
            ]);

        $roles = Role::query()->orderBy('name')->pluck('name');

        return Inertia::render('admin::UserIndexPage', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }
}
