<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CronometroController extends Controller
{
    public function index()
    {
        return view('ejercicios.cronometro', ['vueltas' => session('vueltas', [])]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'tiempo_ms' => 'required|integer|min:0|max:359999999',
        ]);

        $vueltas = session('vueltas', []);
        $ultimo = count($vueltas) ? $vueltas[count($vueltas) - 1]['total_ms'] : 0;
        $total = (int) $datos['tiempo_ms'];

        $vueltas[] = [
            'n' => count($vueltas) + 1,
            'total_ms' => $total,
            'parcial_ms' => max(0, $total - $ultimo),
        ];
        session(['vueltas' => $vueltas]);

        return response()->json(['vueltas' => $vueltas]);
    }

    public function limpiar()
    {
        session()->forget('vueltas');

        return response()->json(['vueltas' => []]);
    }
}
