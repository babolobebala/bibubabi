<?php

namespace Modules\Tool\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ToolController extends Controller
{

    public function cobacoba()
    {
        return Inertia::render(
            'tool::CobaCoba'
        );
    }
}
