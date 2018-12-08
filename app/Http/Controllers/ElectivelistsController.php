<?php

namespace App\Http\Controllers;

use App\Models\Electivelist;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class ElectivelistsController extends Controller
{

    public function __construct()
    {
        $this->fractal = new Manager();
    }

    public function getElectivelistfeed(Request $request)
    {
        $this->validate($request, [
            'query' => 'required|string',
        ]);

        $electivelists = Electivelist::filterName($request->input('query'))->get();

        $resource = new Collection($electivelists, function ($electivelist) {
            return [
                'value' => $electivelist->name,
                'data' => $electivelist->id,
            ];
        });

        return $this->fractal->createData($resource)->toJson();
    }
}
