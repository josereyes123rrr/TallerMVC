@extends('layouts.app')

@section('titulo', 'Calendario de Eventos')

@section('content')
<h2 class="mb-3">Calendario de Eventos</h2>

@if (count($proximos))
    <div class="alert alert-warning shadow-sm">
        <h6 class="alert-heading">Próximos eventos con recordatorio</h6>
        <ul class="mb-0">
            @foreach ($proximos as $p)
                <li>
                    <strong>{{ $p['titulo'] }}</strong> —
                    {{ $p['momento']->format('d/m/Y H:i') }}
                    <span class="badge text-bg-warning">
                        @if ($p['momento']->isSameDay($ahora)) Hoy
                        @elseif ($p['momento']->isSameDay($ahora->copy()->addDay())) Mañana
                        @else {{ $p['momento']->copy()->locale('es')->diffForHumans() }}
                        @endif
                    </span>
                </li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white fw-semibold">Nuevo evento</div>
    <div class="card-body">
        <form method="POST" action="{{ route('eventos.store') }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-4">
                <label class="form-label">Título</label>
                <input type="text" name="titulo" class="form-control" value="{{ old('titulo') }}" maxlength="100" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" value="{{ old('fecha', $ahora->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Hora</label>
                <input type="time" name="hora" class="form-control" value="{{ old('hora', '09:00') }}" required>
            </div>
            <div class="col-md-2">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="recordatorio" value="1" id="rec" checked>
                    <label class="form-check-label" for="rec">Recordatorio</label>
                </div>
            </div>
            <div class="col-md-1"><button class="btn btn-primary w-100">+</button></div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <ul class="list-group list-group-flush">
        @forelse ($eventos as $e)
            <li class="list-group-item">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <strong class="{{ $e['pasado'] ? 'text-muted text-decoration-line-through' : '' }}">{{ $e['titulo'] }}</strong>
                        <div class="small text-muted">
                            {{ $e['momento']->format('d/m/Y H:i') }}
                            @if ($e['recordatorio']) <span class="badge text-bg-warning">Recordatorio</span> @endif
                            @if ($e['pasado']) <span class="badge text-bg-secondary">Finalizado</span> @endif
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#editar-{{ $e['id'] }}">Editar</button>
                        <form method="POST" action="{{ route('eventos.destroy', $e['id']) }}" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    </div>
                </div>

                <div class="collapse mt-3" id="editar-{{ $e['id'] }}">
                    <form method="POST" action="{{ route('eventos.update', $e['id']) }}" class="row g-2 align-items-end bg-light rounded p-2">
                        @csrf
                        @method('PUT')
                        <div class="col-md-4"><input type="text" name="titulo" class="form-control" value="{{ $e['titulo'] }}" maxlength="100" required></div>
                        <div class="col-md-3"><input type="date" name="fecha" class="form-control" value="{{ $e['fecha'] }}" required></div>
                        <div class="col-md-2"><input type="time" name="hora" class="form-control" value="{{ $e['hora'] }}" required></div>
                        <div class="col-md-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="recordatorio" value="1" id="rec-{{ $e['id'] }}" @checked($e['recordatorio'])>
                                <label class="form-check-label" for="rec-{{ $e['id'] }}">Recordar</label>
                            </div>
                        </div>
                        <div class="col-md-1"><button class="btn btn-success w-100">✔</button></div>
                    </form>
                </div>
            </li>
        @empty
            <li class="list-group-item text-center text-muted py-4">No hay eventos registrados.</li>
        @endforelse
    </ul>
</div>
@endsection
