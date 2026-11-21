<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    private const ZONA = 'America/Bogota';

    public function index()
    {
        $ahora = Carbon::now(self::ZONA);

        $eventos = array_map(function ($e) use ($ahora) {
            $e['momento'] = Carbon::parse($e['fecha'] . ' ' . $e['hora'], self::ZONA);
            $e['pasado'] = $e['momento']->lt($ahora);
            return $e;
        }, session('eventos', []));

        usort($eventos, fn ($a, $b) => $a['momento'] <=> $b['momento']);

        $proximos = array_values(array_filter($eventos, fn ($e) => $e['recordatorio'] && ! $e['pasado']));
        $proximos = array_slice($proximos, 0, 5);

        return view('ejercicios.eventos', [
            'eventos' => $eventos,
            'proximos' => $proximos,
            'ahora' => $ahora,
        ]);
    }

    private function reglas(): array
    {
        return [
            'titulo' => 'required|string|max:100',
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'recordatorio' => 'nullable|boolean',
        ];
    }

    public function store(Request $request)
    {
        $datos = $request->validate($this->reglas());

        $eventos = session('eventos', []);
        $eventos[] = [
            'id' => uniqid(),
            'titulo' => trim($datos['titulo']),
            'fecha' => $datos['fecha'],
            'hora' => $datos['hora'],
            'recordatorio' => $request->boolean('recordatorio'),
        ];
        session(['eventos' => $eventos]);

        return redirect()->route('eventos.index')->with('ok', 'Evento agregado.');
    }

    public function update(Request $request, string $id)
    {
        $datos = $request->validate($this->reglas());

        $eventos = array_map(function ($e) use ($id, $datos, $request) {
            if ($e['id'] === $id) {
                $e['titulo'] = trim($datos['titulo']);
                $e['fecha'] = $datos['fecha'];
                $e['hora'] = $datos['hora'];
                $e['recordatorio'] = $request->boolean('recordatorio');
            }
            return $e;
        }, session('eventos', []));
        session(['eventos' => $eventos]);

        return redirect()->route('eventos.index')->with('ok', 'Evento actualizado.');
    }

    public function destroy(string $id)
    {
        $eventos = array_values(array_filter(session('eventos', []), fn ($e) => $e['id'] !== $id));
        session(['eventos' => $eventos]);

        return redirect()->route('eventos.index')->with('ok', 'Evento eliminado.');
    }
}
