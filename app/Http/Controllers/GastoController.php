<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GastoController extends Controller
{
    public const CATEGORIAS = ['Alimentación', 'Transporte', 'Entretenimiento', 'Salud', 'Educación', 'Servicios', 'Otros'];

    public function index()
    {
        $gastos = session('gastos', []);
        usort($gastos, fn ($a, $b) => strcmp($b['fecha'], $a['fecha']));

        $total = array_sum(array_column($gastos, 'monto'));

        $porCategoria = [];
        foreach ($gastos as $g) {
            $porCategoria[$g['categoria']] = ($porCategoria[$g['categoria']] ?? 0) + $g['monto'];
        }
        arsort($porCategoria);

        return view('ejercicios.gastos', [
            'gastos' => $gastos,
            'total' => $total,
            'porCategoria' => $porCategoria,
            'categorias' => self::CATEGORIAS,
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'descripcion' => 'required|string|max:120',
            'monto' => 'required|numeric|min:0.01|max:999999999',
            'categoria' => 'required|in:' . implode(',', self::CATEGORIAS),
            'fecha' => 'required|date',
        ]);

        $gastos = session('gastos', []);
        $gastos[] = [
            'id' => uniqid(),
            'descripcion' => trim($datos['descripcion']),
            'monto' => round((float) $datos['monto'], 2),
            'categoria' => $datos['categoria'],
            'fecha' => $datos['fecha'],
        ];
        session(['gastos' => $gastos]);

        return redirect()->route('gastos.index')->with('ok', 'Gasto registrado.');
    }

    public function destroy(string $id)
    {
        $gastos = array_values(array_filter(session('gastos', []), fn ($g) => $g['id'] !== $id));
        session(['gastos' => $gastos]);

        return redirect()->route('gastos.index')->with('ok', 'Gasto eliminado.');
    }
}
