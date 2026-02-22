<?php

namespace Modules\Tool\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class ToolController extends Controller
{
    public function geotagging()
    {
        return Inertia::render(
            'tool::GeoTagging'
        );
    }
}
