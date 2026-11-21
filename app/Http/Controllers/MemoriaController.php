<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MemoriaController extends Controller
{
    public const PARES = ['facil' => 4, 'medio' => 6, 'dificil' => 8];

    public function index()
    {
        $orden = array_flip(array_keys(self::PARES));
        $puntajes = session('memoria_puntajes', []);
        usort($puntajes, fn ($a, $b) => [$orden[$a['nivel']], $a['intentos']] <=> [$orden[$b['nivel']], $b['intentos']]);

        return view('ejercicios.memoria', ['puntajes' => $puntajes, 'pares' => self::PARES]);
    }

    public function guardar(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:40',
            'nivel' => 'required|in:' . implode(',', array_keys(self::PARES)),
            'intentos' => 'required|integer|min:1|max:1000',
        ]);

        // Un jugador no puede ganar con menos intentos que pares existentes
        if ((int) $datos['intentos'] < self::PARES[$datos['nivel']]) {
            return redirect()->route('memoria.index')->withErrors(['intentos' => 'Puntaje no válido para ese nivel.']);
        }

        $puntajes = session('memoria_puntajes', []);
        $puntajes[] = [
            'nombre' => trim($datos['nombre']),
            'nivel' => $datos['nivel'],
            'intentos' => (int) $datos['intentos'],
            'fecha' => date('Y-m-d H:i'),
        ];
        session(['memoria_puntajes' => $puntajes]);

        return redirect()->route('memoria.index')->with('ok', '¡Puntaje guardado!');
    }

    public function limpiar()
    {
        session()->forget('memoria_puntajes');

        return redirect()->route('memoria.index')->with('ok', 'Puntajes borrados.');
    }
}
