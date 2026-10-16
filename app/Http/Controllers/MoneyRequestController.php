<?php

namespace App\Http\Controllers;

use App\Models\MoneyRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MoneyRequestController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required','in:deposit,withdrawal'],
            'amount' => ['required','numeric','min:1','max:1000000'],
            'reference' => ['nullable','string','max:120'],
        ]);
        MoneyRequest::create([
            'user_id' => $request->user()->id,
            'type' => $data['type'],
            'amount_mxn_cents' => (int) round(((float) $data['amount']) * 100),
            'status' => 'pending',
            'reference' => $data['reference'] ?? null,
        ]);
        return back()->with('status', 'Solicitud recibida y pendiente de revisión. No se ha movido dinero ni actualizado el saldo.');
    }
}
