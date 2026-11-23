<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DestinationController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $filter = $request->string('filter')->trim()->toString();

        $destinations = Destination::query()
            ->active()
            ->withCount(['tours' => fn (Builder $q): Builder => $q->active()])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $sub) use ($search): void {
                    $sub->where('name', 'like', '%'.$search.'%')
                        ->orWhere('country', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->when($filter === 'popular', fn (Builder $query): Builder => $query->popular())
            ->ordered()
            ->get();

        $featuredDestination = $destinations->firstWhere('is_popular', true) ?? $destinations->first();

        return view('website.destinations.index', [
            'destinations' => $destinations,
            'featuredDestination' => $featuredDestination,
            'filters' => [
                'search' => $search,
                'filter' => $filter,
            ],
        ]);
    }

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
