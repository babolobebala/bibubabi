<?php

namespace Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;

class CoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render(
            'core::CorePage'
        );
    }
}
