@extends('layouts.app')

@section('titulo', 'Sistema de Reservas')

@section('content')
<h2 class="mb-3">Sistema de Reservas</h2>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white fw-semibold">1. Consulta horarios por fecha</div>
            <div class="card-body">
                <form method="GET" action="{{ route('reservas.index') }}" class="row g-2">
                    <div class="col">
                        <input type="date" name="fecha" class="form-control" value="{{ $fecha }}" required>
                    </div>
                    <div class="col-auto"><button class="btn btn-outline-primary">Ver disponibilidad</button></div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold">2. Reserva para el {{ $fecha }}</div>
            <div class="card-body">
                <form method="POST" action="{{ route('reservas.store') }}">
                    @csrf
                    <input type="hidden" name="fecha" value="{{ $fecha }}">
                    <div class="mb-2">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" maxlength="80" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Servicio</label>
                        <select name="servicio" class="form-select" required>
                            @foreach ($servicios as $s)
                                <option value="{{ $s }}" @selected(old('servicio') === $s)>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Horario disponible</label>
                        <select name="hora" class="form-select" required>
                            <option value="">Selecciona...</option>
                            @foreach ($horarios as $h)
                                <option value="{{ $h['hora'] }}" @disabled($h['reserva']) @selected(old('hora') === $h['hora'])>
                                    {{ $h['hora'] }} {{ $h['reserva'] ? '(ocupado)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary w-100">Confirmar reserva</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white fw-semibold">Disponibilidad del {{ $fecha }}</div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach ($horarios as $h)
                        <div class="col-4 col-md-3">
                            <div class="text-center p-2 rounded {{ $h['reserva'] ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">
                                <div class="fw-bold">{{ $h['hora'] }}</div>
                                <small>{{ $h['reserva'] ? 'Ocupado' : 'Libre' }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold">Mis reservas</div>
            <ul class="list-group list-group-flush">
                @forelse ($reservas as $r)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $r['fecha'] }} · {{ $r['hora'] }}</strong><br>
                            <small class="text-muted">{{ $r['nombre'] }} — {{ $r['servicio'] }}</small>
                        </div>
                        <form method="POST" action="{{ route('reservas.destroy', $r['id']) }}" class="m-0">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="fecha" value="{{ $fecha }}">
                            <button class="btn btn-sm btn-outline-danger">Cancelar</button>
                        </form>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted py-4">No tienes reservas.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
