<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::insert([
            ['email_bps' => 'fatihwisesa@bps.go.id', 'username' => 'fatihwisesa', 'nip' => '340063164', 'created_at' => now()],
            ['email_bps' => 'tihul@bps.go.id', 'username' => 'tihul', 'nip' => 'ahay', 'created_at' => now()],
        ]);
    }
}
