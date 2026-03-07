<?php

namespace Modules\Debugging\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DebuggingController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('debugging::DebuggingPage');
    }
}
