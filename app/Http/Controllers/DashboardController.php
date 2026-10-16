<?php

namespace App\Http\Controllers;

use App\Models\ChargingStation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $stations = ChargingStation::where('status', 'active')->orderBy('price_mxn_cents')->get();
        return view('dashboard', ['stations' => $stations, 'user' => $request->user()]);
    }
}
