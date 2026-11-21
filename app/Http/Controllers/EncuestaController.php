<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EncuestaController extends Controller
{
    public function index()
    {
        return view('ejercicios.encuestas', ['encuestas' => array_reverse(session('encuestas', []))]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pregunta' => 'required|string|max:150',
            'opciones' => 'required|array|min:2|max:8',
            'opciones.*' => 'nullable|string|max:80',
        ]);

        $opciones = [];
        $vistas = [];
        foreach ($request->input('opciones', []) as $texto) {
            $texto = trim((string) $texto);
            $clave = mb_strtolower($texto);
            if ($texto === '' || isset($vistas[$clave])) {
                continue;
            }
            $vistas[$clave] = true;
            $opciones[] = ['texto' => $texto, 'votos' => 0];
        }

        if (count($opciones) < 2) {
            return redirect()->route('encuestas.index')
                ->withInput()
                ->withErrors(['opciones' => 'Escribe al menos 2 opciones distintas.']);
        }

        $encuestas = session('encuestas', []);
        $encuestas[] = [
            'id' => uniqid(),
            'pregunta' => trim($request->input('pregunta')),
            'opciones' => $opciones,
        ];
        session(['encuestas' => $encuestas]);

        return redirect()->route('encuestas.index')->with('ok', 'Encuesta creada.');
    }

    public function votar(Request $request, string $id)
    {
        $request->validate(['opcion' => 'required|integer|min:0']);
        $indice = (int) $request->input('opcion');
        $votado = false;

        $encuestas = array_map(function ($e) use ($id, $indice, &$votado) {
            if ($e['id'] === $id && isset($e['opciones'][$indice])) {
                $e['opciones'][$indice]['votos']++;
                $votado = true;
            }
            return $e;
        }, session('encuestas', []));
        session(['encuestas' => $encuestas]);

        return redirect()->route('encuestas.index')->with(
            $votado ? 'ok' : 'error_voto',
            $votado ? '¡Voto registrado!' : 'Opción no válida.'
        );
    }

    public function destroy(string $id)
    {
        $encuestas = array_values(array_filter(session('encuestas', []), fn ($e) => $e['id'] !== $id));
        session(['encuestas' => $encuestas]);

        return redirect()->route('encuestas.index')->with('ok', 'Encuesta eliminada.');
    }
}
