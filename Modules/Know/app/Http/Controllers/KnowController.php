<?php

namespace Modules\Know\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class KnowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render(
            'know::KnowPage'
        );
    }

    public function create()
    {
        return Inertia::render(
            'know::KnowCreatePage'
        );
    }
}
