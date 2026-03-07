<?php

namespace Modules\Debugging\Http\Controllers;

use App\Http\Controllers\Controller;

use Inertia\Inertia;
use Illuminate\Http\Request;
class TestController extends Controller
{
    public function TestPage(Request $request)
    {
        return Inertia::render(
            'debugging::TestPage'
        );
    }
    public function TestAuthPage(Request $request)
    {
        return Inertia::render(
            'debugging::TestAuthPage'
        );
    }
}
