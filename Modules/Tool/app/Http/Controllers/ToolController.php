<?php

namespace Modules\Tool\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ToolController extends Controller
{
    public function index()
    {
        return Inertia::render(
            'tool::ToolHubPage'
        );
    }

    public function geotagging()
    {
        return Inertia::render(
            'tool::CobaCoba'
        );
    }

    public function cobacoba()
    {
        return $this->geotagging();
    }
}
