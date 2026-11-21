@extends('layouts.app')

@section('titulo', 'Calculadora de Propinas')

@section('content')
<h2 class="mb-3">Calculadora de Propinas</h2>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form method="POST" action="{{ route('propinas.calcular') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Monto total de la cuenta</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0.01" name="monto" class="form-control" value="{{ old('monto') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Porcentaje de propina</label>
                        <select name="porcentaje" class="form-select" required>
                            @foreach ($porcentajes as $p)
                                <option value="{{ $p }}" @selected((string) old('porcentaje', 10) === (string) $p)>{{ $p }}%</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Número de personas (opcional)</label>
                        <input type="number" min="1" max="100" name="personas" class="form-control" value="{{ old('personas', 1) }}">
                    </div>
                    <button class="btn btn-primary w-100">Calcular</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        @if (session('resultado'))
            @php $r = session('resultado'); @endphp
            <div class="card shadow-sm border-0 border-start border-4 border-success">
                <div class="card-body">
                    <h5 class="card-title">Resultado</h5>
                    <table class="table mb-0">
                        <tr><td>Cuenta</td><td class="text-end">${{ number_format($r['monto'], 2) }}</td></tr>
                        <tr><td>Propina ({{ $r['porcentaje'] }}%)</td><td class="text-end">${{ number_format($r['propina'], 2) }}</td></tr>
                        <tr class="table-success fw-bold"><td>Total a pagar</td><td class="text-end">${{ number_format($r['total'], 2) }}</td></tr>
                        @if ($r['personas'] > 1)
                            <tr><td>Por persona ({{ $r['personas'] }})</td><td class="text-end">${{ number_format($r['porPersona'], 2) }}</td></tr>
                        @endif
                    </table>
                </div>
            </div>
        @else
            <div class="alert alert-info">Ingresa el monto y elige un porcentaje para ver el resultado.</div>
        @endif
    </div>
</div>
@endsection
