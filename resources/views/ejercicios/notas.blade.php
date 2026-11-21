@extends('layouts.app')

@section('titulo', 'Gestor de Notas')

@section('content')
<h2 class="mb-3">Gestor de Notas</h2>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold">Nueva nota</div>
            <div class="card-body">
                <form method="POST" action="{{ route('notas.store') }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">Título</label>
                        <input type="text" name="titulo" class="form-control" value="{{ old('titulo') }}" maxlength="100" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Categoría</label>
                        <select name="categoria" class="form-select" required>
                            @foreach ($categorias as $c)
                                <option value="{{ $c }}" @selected(old('categoria') === $c)>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contenido</label>
                        <textarea name="contenido" rows="5" class="form-control" maxlength="2000" required>{{ old('contenido') }}</textarea>
                    </div>
                    <button class="btn btn-primary w-100">Guardar nota</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <form method="GET" action="{{ route('notas.index') }}" class="row g-2 mb-3">
            <div class="col-md-6">
                <input type="text" name="q" class="form-control" placeholder="Buscar por texto..." value="{{ $q }}">
            </div>
            <div class="col-md-3">
                <select name="categoria" class="form-select">
                    <option value="">Todas las categorías</option>
                    @foreach ($categorias as $c)
                        <option value="{{ $c }}" @selected($categoriaSel === $c)>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-outline-primary flex-grow-1">Filtrar</button>
                <a href="{{ route('notas.index') }}" class="btn btn-outline-secondary">Limpiar</a>
            </div>
        </form>

        <p class="text-muted small">Mostrando {{ count($notas) }} de {{ $total }} notas.</p>

        <div class="row g-3">
            @forelse ($notas as $n)
                @php $color = ['Estudio' => 'primary', 'Trabajo' => 'warning', 'Personal' => 'success'][$n['categoria']] ?? 'secondary'; @endphp
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100 border-top border-4 border-{{ $color }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title">{{ $n['titulo'] }}</h5>
                                <span class="badge text-bg-{{ $color }} align-self-start">{{ $n['categoria'] }}</span>
                            </div>
                            <p class="card-text" style="white-space: pre-line;">{{ $n['contenido'] }}</p>
                        </div>
                        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                            <small class="text-muted">{{ $n['creada'] }}</small>
                            <form method="POST" action="{{ route('notas.destroy', $n['id']) }}" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12"><div class="alert alert-light text-center">No hay notas que mostrar.</div></div>
            @endforelse
        </div>
    </div>
</div>
@endsection
