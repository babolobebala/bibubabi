<?php

namespace Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class CoreController extends Controller
{
    public function welcome(): Response
    {
        return Inertia::render('core::WelcomePage');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render(
            'core::CorePage'
        );
    }

    public function quickMenu(): Response
    {
        return Inertia::render('core::QuickMenuPage');
    }

    public function notification(): Response
    {
        return Inertia::render('core::NotificationPage');
    }
}
