<?php

namespace App\Http\Controllers;

use App\Models\Electivelist;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ElectivelistsController extends Controller
{

    public function getElectivelistfeed(Request $request)
    {
        $this->validate($request, [
            'query' => 'required|string',
        ]);

        $electivelists = Electivelist::filterName($request->input('query'))->get();

        $resource = new Collection();
        foreach ($electivelists as $electivelist) {
            $resource->push([
                'value' => $electivelist->name,
                'data' => $electivelist->id,
            ]);
        }

        return response()->jsonApi($resource);
    }
}
