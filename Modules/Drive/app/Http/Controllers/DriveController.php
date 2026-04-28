<?php

namespace Modules\Drive\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Drive\Http\Requests\StoreDriveRequest;
use Modules\Drive\Http\Requests\UpdateDriveRequest;
use Modules\Drive\Models\Drive;
use Spatie\Permission\Models\Role;

class DriveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $user = Auth::user();
        $roleIds = $user->roles->pluck('id');

        $drives = Drive::query()
            ->where(function ($query) use ($user, $roleIds) {
                $query->where(function ($q) use ($user) {
                    $q->where('jenis', 'personal')
                        ->where('personal', $user->id);
                })->orWhere(function ($q) use ($roleIds) {
                    $q->where('jenis', 'tim')
                        ->whereIn('tim', $roleIds);
                });
            })
            ->latest()
            ->get();

        return Inertia::render('Drive::DriveIndexPage', [
            'drives' => $drives,
        ]);
    }

    /**
     * Display the admin page.
     */
    public function admin(): Response
    {
        $drives = Drive::with(['personalUser', 'timRole'])->latest()->get();

        $availableUsers = User::orderBy('nama')->get()->map(fn ($user) => [
            'value' => (string) $user->id,
            'label' => $user->nama,
        ]);

        $availableRoles = Role::orderBy('name')->get()->map(fn ($role) => [
            'value' => (string) $role->id,
            'label' => $role->name,
        ]);

        return Inertia::render('Drive::DriveAdminPage', [
            'drives' => $drives,
            'availableUsers' => $availableUsers,
            'availableRoles' => $availableRoles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDriveRequest $request)
    {
        $validated = $request->validated();

        Drive::create([
            'nama' => $validated['nama'],
            'link' => $validated['link'],
            'jenis' => $validated['jenis'],
            'personal' => $validated['personal'] ?? null,
            'tim' => $validated['tim'] ?? null,
            'akses' => $validated['akses'],
            'status' => $validated['status'] ?? 'success',
            'catatan' => $validated['catatan'] ?? null,
            'created_by' => Auth::user()?->username ?? 'unknown',
        ]);

        return back()->with('success', 'Drive berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDriveRequest $request, Drive $drive)
    {
        $validated = $request->validated();

        $drive->update([
            'nama' => $validated['nama'],
            'link' => $validated['link'],
            'jenis' => $validated['jenis'],
            'personal' => $validated['personal'] ?? null,
            'tim' => $validated['tim'] ?? null,
            'akses' => $validated['akses'],
            'status' => $validated['status'],
            'catatan' => $validated['catatan'] ?? null,
            'updated_by' => Auth::user()?->username ?? 'unknown',
        ]);

        return back()->with('success', 'Drive berhasil diperbarui');
    }

    /**
     * Remove the specified resource in storage.
     */
    public function destroy(Drive $drive)
    {
        $drive->delete();

        return back()->with('success', 'Drive berhasil dihapus');
    }
}
