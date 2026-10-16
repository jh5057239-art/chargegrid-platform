<?php

namespace App\Http\Controllers;

use App\Models\ChargingStation;
use App\Models\MoneyRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        return view('admin.index', [
            'users' => User::latest()->limit(100)->get(),
            'stations' => ChargingStation::latest()->get(),
            'requests' => MoneyRequest::with('user')->latest()->limit(100)->get(),
        ]);
    }

    public function updateRequest(Request $request, MoneyRequest $moneyRequest): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);
        $moneyRequest->update($data);
        return back()->with('status', 'Estado de la solicitud actualizado. El saldo no se modifica automáticamente.');
    }

    public function storeStation(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'model' => ['nullable', 'string', 'max:120'],
            'charging_type' => ['required', 'in:AC,DC'],
            'power_kw' => ['required', 'integer', 'min:1', 'max:1000'],
            'price_mxn' => ['required', 'numeric', 'min:0', 'max:10000000'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:draft,active,inactive'],
        ]);
        ChargingStation::create([
            'name' => $data['name'],
            'model' => $data['model'] ?? null,
            'charging_type' => $data['charging_type'],
            'power_kw' => $data['power_kw'],
            'price_mxn_cents' => (int) round(((float) $data['price_mxn']) * 100),
            'description' => $data['description'] ?? null,
            'status' => $data['status'],
        ]);
        return back()->with('status', 'Estación guardada en el catálogo.');
    }
}
