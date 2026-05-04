<?php

namespace Modules\Pst\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class PstController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('pst::PstPage');
    }

    public function admin(): Response
    {
        return Inertia::render('pst::PstAdminPage');
    }
}
