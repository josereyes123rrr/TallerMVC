<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContrasenaController extends Controller
{
    private const CONJUNTOS = [
        'mayusculas' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        'minusculas' => 'abcdefghijklmnopqrstuvwxyz',
        'numeros' => '0123456789',
        'simbolos' => '!@#$%^&*()-_=+[]{};:,.<>?',
    ];

    public function index()
    {
        return view('ejercicios.contrasenas', ['historial' => session('contrasenas_historial', [])]);
    }

    public function generar(Request $request)
    {
        $datos = $request->validate([
            'longitud' => 'required|integer|min:6|max:32',
            'mayusculas' => 'nullable|boolean',
            'minusculas' => 'nullable|boolean',
            'numeros' => 'nullable|boolean',
            'simbolos' => 'nullable|boolean',
        ]);

        $seleccion = [];
        foreach (self::CONJUNTOS as $clave => $caracteres) {
            if ($request->boolean($clave)) {
                $seleccion[] = $caracteres;
            }
        }

        if (empty($seleccion)) {
            return redirect()->route('contrasenas.index')
                ->withInput()
                ->withErrors(['tipos' => 'Selecciona al menos un tipo de caracter.']);
        }

        $longitud = (int) $datos['longitud'];
        $todos = implode('', $seleccion);

        // Al menos un caracter de cada tipo elegido
        $chars = [];
        foreach ($seleccion as $conjunto) {
            $chars[] = $conjunto[random_int(0, strlen($conjunto) - 1)];
        }
        while (count($chars) < $longitud) {
            $chars[] = $todos[random_int(0, strlen($todos) - 1)];
        }

        // Mezcla Fisher-Yates con random_int
        for ($i = count($chars) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$chars[$i], $chars[$j]] = [$chars[$j], $chars[$i]];
        }
        $contrasena = implode('', array_slice($chars, 0, $longitud));

        $historial = session('contrasenas_historial', []);
        array_unshift($historial, $contrasena);
        session(['contrasenas_historial' => array_slice($historial, 0, 5)]);

        return redirect()->route('contrasenas.index')
            ->withInput()
            ->with('contrasena', $contrasena);
    }
}
