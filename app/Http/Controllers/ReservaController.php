<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    public const HORARIOS = ['08:00', '09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00'];
    public const SERVICIOS = ['Consulta general', 'Corte de cabello', 'Asesoría académica', 'Masaje relajante'];

    public function index(Request $request)
    {
        $fecha = $request->query('fecha');
        if (! $fecha || ! strtotime($fecha)) {
            $fecha = date('Y-m-d');
        }
        $fecha = Carbon::parse($fecha)->format('Y-m-d');

        $reservas = session('reservas', []);
        $ocupados = array_column(array_filter($reservas, fn ($r) => $r['fecha'] === $fecha), null, 'hora');

        $horarios = [];
        foreach (self::HORARIOS as $hora) {
            $horarios[] = ['hora' => $hora, 'reserva' => $ocupados[$hora] ?? null];
        }

        usort($reservas, fn ($a, $b) => strcmp($a['fecha'] . $a['hora'], $b['fecha'] . $b['hora']));

        return view('ejercicios.reservas', [
            'fecha' => $fecha,
            'horarios' => $horarios,
            'reservas' => $reservas,
            'servicios' => self::SERVICIOS,
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:80',
            'servicio' => 'required|in:' . implode(',', self::SERVICIOS),
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => 'required|in:' . implode(',', self::HORARIOS),
        ]);

        $fecha = Carbon::parse($datos['fecha'])->format('Y-m-d');
        $reservas = session('reservas', []);

        foreach ($reservas as $r) {
            if ($r['fecha'] === $fecha && $r['hora'] === $datos['hora']) {
                return redirect()->route('reservas.index', ['fecha' => $fecha])
                    ->withErrors(['hora' => 'Ese horario ya está ocupado. Elige otro.'])
                    ->withInput();
            }
        }

        $reservas[] = [
            'id' => uniqid(),
            'nombre' => trim($datos['nombre']),
            'servicio' => $datos['servicio'],
            'fecha' => $fecha,
            'hora' => $datos['hora'],
        ];
        session(['reservas' => $reservas]);

        return redirect()->route('reservas.index', ['fecha' => $fecha])
            ->with('ok', 'Reserva confirmada para el ' . $fecha . ' a las ' . $datos['hora'] . '.');
    }

    public function destroy(Request $request, string $id)
    {
        $reservas = array_values(array_filter(session('reservas', []), fn ($r) => $r['id'] !== $id));
        session(['reservas' => $reservas]);

        return redirect()->route('reservas.index', ['fecha' => $request->input('fecha', date('Y-m-d'))])
            ->with('ok', 'Reserva cancelada.');
    }
}
