<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
class TestController extends Controller
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
