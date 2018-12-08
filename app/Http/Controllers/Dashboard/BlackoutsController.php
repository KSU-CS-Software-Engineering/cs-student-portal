<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Blackout;
use Illuminate\Http\Request;

class BlackoutsController extends Controller
{
    public function getBlackouts(Request $request, $id = -1)
    {
        if ($id < 0) {
            $blackouts = Blackout::with('advisor', 'events')->get();
            return view('dashboard.blackouts')->with('blackouts', $blackouts)->with('page_title', "Blackouts");
        } else {
            $blackout = Blackout::findOrFail($id);
            $blackout->load('events', 'advisor');
            return view('dashboard.blackoutedit')->with('blackout', $blackout)->with('page_title', "Edit Blackout");
        }
    }

    public function postDeleteblackout(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:blackouts',
        ]);
        $blackout = Blackout::findOrFail($request->input('id'));
        $blackout->delete();
        $request->session()->put('message', trans('messages.item_deleted'));
        $request->session()->put('type', 'success');
        return response()->json(trans('messages.item_deleted'), 200);
    }

}
