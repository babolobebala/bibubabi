<?php

namespace Modules\Notulensi\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class NotulensiController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('notulensi::NotulensiPage');
    }
}
