<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TareaController extends Controller
{
    public function index()
    {
        return view('ejercicios.tareas', ['tareas' => session('tareas', [])]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'titulo' => 'required|string|max:120',
        ]);

        $tareas = session('tareas', []);
        $tareas[] = [
            'id' => uniqid(),
            'titulo' => trim($datos['titulo']),
            'completada' => false,
        ];
        session(['tareas' => $tareas]);

        return redirect()->route('tareas.index')->with('ok', 'Tarea agregada.');
    }

    public function toggle(string $id)
    {
        $tareas = array_map(function ($t) use ($id) {
            if ($t['id'] === $id) {
                $t['completada'] = ! $t['completada'];
            }
            return $t;
        }, session('tareas', []));
        session(['tareas' => $tareas]);

        return redirect()->route('tareas.index');
    }

    public function destroy(string $id)
    {
        $tareas = array_values(array_filter(session('tareas', []), fn ($t) => $t['id'] !== $id));
        session(['tareas' => $tareas]);

        return redirect()->route('tareas.index')->with('ok', 'Tarea eliminada.');
    }
}
