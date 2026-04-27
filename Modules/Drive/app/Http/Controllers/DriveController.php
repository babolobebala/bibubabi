<?php

namespace Modules\Drive\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DriveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Drive::DriveIndexPage');
    }

    /**
     * Display the admin page.
     */
    public function admin(): Response
    {
        return Inertia::render('Drive::DriveAdminPage');
    }
}
