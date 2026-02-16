<?php

namespace Modules\Umum\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class UmumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function welcome_page()
    {
        return Inertia::render(
            'umum::WelcomePage'
        );
    }
}
