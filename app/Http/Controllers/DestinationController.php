<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\View\View;

class DestinationController extends Controller
{
    public function show(Destination $destination): View
    {
        abort_unless($destination->is_active, 404);

        $tours = $destination->tours()
            ->active()
            ->with(['destination', 'category'])
            ->ordered()
            ->paginate(9);

        return view('website.destinations.show', [
            'destination' => $destination,
            'tours' => $tours,
        ]);
    }
}
