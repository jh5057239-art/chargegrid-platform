<?php

namespace App\Http\Controllers;

use App\Models\ChargingStation;
use App\Models\MoneyRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $stations = ChargingStation::where('status', 'active')->orderBy('price_mxn_cents')->get();
        $requests = MoneyRequest::where('user_id', $user->id)->latest()->limit(20)->get();
        $referralCount = User::where('referred_by', $user->invite_code)->count();

        return view('dashboard', [
            'stations' => $stations,
            'user' => $user,
            'requests' => $requests,
            'referralCount' => $referralCount,
            'referralLink' => route('register', ['invite' => $user->invite_code]),
        ]);
    }
}
