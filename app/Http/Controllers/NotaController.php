<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotaController extends Controller
{
    public const CATEGORIAS = ['Estudio', 'Trabajo', 'Personal'];

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $categoria = (string) $request->query('categoria', '');

        $todas = session('notas', []);
        $notas = array_values(array_filter($todas, function ($n) use ($q, $categoria) {
            if ($categoria !== '' && $n['categoria'] !== $categoria) {
                return false;
            }
            if ($q !== '' && mb_stripos($n['titulo'] . ' ' . $n['contenido'], $q) === false) {
                return false;
            }
            return true;
        }));
        $notas = array_reverse($notas);

        return view('ejercicios.notas', [
            'notas' => $notas,
            'total' => count($todas),
            'q' => $q,
            'categoriaSel' => $categoria,
            'categorias' => self::CATEGORIAS,
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'titulo' => 'required|string|max:100',
            'contenido' => 'required|string|max:2000',
            'categoria' => 'required|in:' . implode(',', self::CATEGORIAS),
        ]);

        $notas = session('notas', []);
        $notas[] = [
            'id' => uniqid(),
            'titulo' => trim($datos['titulo']),
            'contenido' => trim($datos['contenido']),
            'categoria' => $datos['categoria'],
            'creada' => date('Y-m-d H:i'),
        ];
        session(['notas' => $notas]);

        return redirect()->route('notas.index')->with('ok', 'Nota guardada.');
    }

    public function destroy(string $id)
    {
        $notas = array_values(array_filter(session('notas', []), fn ($n) => $n['id'] !== $id));
        session(['notas' => $notas]);

        return redirect()->route('notas.index')->with('ok', 'Nota eliminada.');
    }
}
