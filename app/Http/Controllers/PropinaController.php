<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PropinaController extends Controller
{
    public const PORCENTAJES = [0, 5, 10, 15, 18, 20, 25];

    public function index()
    {
        return view('ejercicios.propinas', ['porcentajes' => self::PORCENTAJES]);
    }

    public function calcular(Request $request)
    {
        $datos = $request->validate([
            'monto' => 'required|numeric|min:0.01|max:999999999',
            'porcentaje' => 'required|numeric|in:' . implode(',', self::PORCENTAJES),
            'personas' => 'nullable|integer|min:1|max:100',
        ]);

        $monto = (float) $datos['monto'];
        $porcentaje = (float) $datos['porcentaje'];
        $personas = (int) ($datos['personas'] ?? 1);

        $propina = round($monto * $porcentaje / 100, 2);
        $total = round($monto + $propina, 2);

        return redirect()->route('propinas.index')
            ->withInput()
            ->with('resultado', [
                'monto' => $monto,
                'porcentaje' => $porcentaje,
                'propina' => $propina,
                'total' => $total,
                'personas' => $personas,
                'porPersona' => round($total / $personas, 2),
            ]);
    }
}
