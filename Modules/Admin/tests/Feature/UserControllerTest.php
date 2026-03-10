<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;

it('can display the user index page', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'Superadmin', 'guard_name' => 'web']);
    $user->assignRole($role);

    actingAs($user)
        ->get('/app/admin/users')
        ->assertOk();
});
