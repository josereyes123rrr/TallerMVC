@extends('layouts.app')

@section('titulo', 'Lista de Tareas')

@section('content')
<h2 class="mb-3">Lista de Tareas</h2>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form method="POST" action="{{ route('tareas.store') }}" class="row g-2">
            @csrf
            <div class="col">
                <input type="text" name="titulo" class="form-control" placeholder="¿Qué tienes que hacer?" value="{{ old('titulo') }}" maxlength="120" required>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary">Agregar</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <ul class="list-group list-group-flush">
        @forelse ($tareas as $tarea)
            <li class="list-group-item d-flex align-items-center justify-content-between">
                <form method="POST" action="{{ route('tareas.toggle', $tarea['id']) }}" class="flex-grow-1 m-0">
                    @csrf
<button class="btn btn-link text-decoration-none text-start p-0 {{ $tarea['completada'] ? 'text-muted' : 'text-dark' }}">
    {{ $tarea['completada'] ? '[x]' : '[ ]' }}
    <span class="{{ $tarea['completada'] ? 'text-decoration-line-through' : '' }}">{{ $tarea['titulo'] }}</span>
</button>
                </form>
                <form method="POST" action="{{ route('tareas.destroy', $tarea['id']) }}" class="m-0">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                </form>
            </li>
        @empty
            <li class="list-group-item text-center text-muted py-4">No hay tareas. ¡Agrega la primera!</li>
        @endforelse
    </ul>
    @if (count($tareas))
        <div class="card-footer text-muted small">
            {{ collect($tareas)->where('completada', true)->count() }} de {{ count($tareas) }} completadas
        </div>
    @endif
</div>
@endsection
