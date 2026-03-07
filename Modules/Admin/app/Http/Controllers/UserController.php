<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
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
                'nip_baru' => $user->nip_baru,
                'username' => $user->username,
                'email_bps' => $user->email_bps,
                'email_gmail' => $user->email_gmail,
                'golongan' => $user->golongan,
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

    public function updatePassword(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', Password::min(8)->letters()->numbers(), 'confirmed'],
        ]);

        $user->update(['password' => $validated['password']]);

        return back()->with('success', "Password {$user->nama} berhasil diubah.");
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        // Ganti semua role user dengan role yang baru dipilih
        $user->syncRoles([$validated['role']]);

        return back()->with('success', "Role {$user->nama} berhasil diubah menjadi {$validated['role']}.");
    }
}
