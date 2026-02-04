<?php

namespace Modules\Main\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class TestController
{
    public function TestPage(Request $request)
    {
        return Inertia::render(
            'TestPage'
        );
    }
    public function TestAuthPage(Request $request)
    {
        return Inertia::render(
            'TestAuthPage'
        );
    }
}
