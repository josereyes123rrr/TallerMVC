@extends('layouts.app')

@section('titulo', 'Plataforma de Recetas')

@section('content')
<h2 class="mb-3">Plataforma de Recetas</h2>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold">Publicar receta</div>
            <div class="card-body">
                <form method="POST" action="{{ route('recetas.store') }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" maxlength="100" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Tipo de comida</label>
                        <select name="tipo" class="form-select" required>
                            @foreach ($tipos as $t)
                                <option value="{{ $t }}" @selected(old('tipo') === $t)>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Ingredientes (uno por línea)</label>
                        <textarea name="ingredientes" rows="4" class="form-control" required>{{ old('ingredientes') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Preparación</label>
                        <textarea name="preparacion" rows="4" class="form-control" required>{{ old('preparacion') }}</textarea>
                    </div>
                    <button class="btn btn-primary w-100">Publicar</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <form method="GET" action="{{ route('recetas.index') }}" class="row g-2 mb-3 align-items-center">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control" placeholder="Nombre o ingrediente..." value="{{ $q }}">
            </div>
            <div class="col-md-3">
                <select name="tipo" class="form-select">
                    <option value="">Todos los tipos</option>
                    @foreach ($tipos as $t)
                        <option value="{{ $t }}" @selected($tipoSel === $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="favoritas" value="1" id="fav" @checked($soloFavoritas)>
                    <label class="form-check-label" for="fav">Guardadas</label>
                </div>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-outline-primary flex-grow-1">Filtrar</button>
                <a href="{{ route('recetas.index') }}" class="btn btn-outline-secondary">Limpiar</a>
            </div>
        </form>

        <div class="row g-3">
            @forelse ($recetas as $r)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="card-title">{{ $r['nombre'] }}</h5>
                                <span class="badge text-bg-info">{{ $r['tipo'] }}</span>
                            </div>
                            <h6 class="text-muted mt-2">Ingredientes</h6>
                            <ul class="small">
                                @foreach (preg_split('/\r\n|\r|\n/', $r['ingredientes']) as $ing)
                                    @if (trim($ing) !== '') <li>{{ $ing }}</li> @endif
                                @endforeach
                            </ul>
                            <h6 class="text-muted">Preparación</h6>
                            <p class="small mb-0" style="white-space: pre-line;">{{ $r['preparacion'] }}</p>
                        </div>
                        <div class="card-footer bg-white d-flex justify-content-between">
                            <form method="POST" action="{{ route('recetas.favorita', $r['id']) }}" class="m-0">
                                @csrf
                                <button class="btn btn-sm {{ $r['favorita'] ? 'btn-danger' : 'btn-outline-danger' }}">
                                    {{ $r['favorita'] ? '♥ Guardada' : '♡ Guardar' }}
                                </button>
                            </form>
                            @if ($r['propia'])
                                <form method="POST" action="{{ route('recetas.destroy', $r['id']) }}" class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-secondary">Eliminar</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12"><div class="alert alert-light text-center">No se encontraron recetas.</div></div>
            @endforelse
        </div>
    </div>
</div>
@endsection
