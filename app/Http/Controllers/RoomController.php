<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;

class RoomController extends Controller
{
    public function search(Request $request)
    {
        $results = Room::where('name', 'like', '%' . $request->search . '%')->limit(config('app.display_limit'));
        if ($results->count() > 0) {
            $results = $results->get();
            return ['results' => $results];
        } else {
            return false;
        }
    }
}
