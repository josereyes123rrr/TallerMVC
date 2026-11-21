@extends('layouts.app')

@section('titulo', 'Plataforma de Encuestas')

@section('content')
<h2 class="mb-3">Plataforma de Encuestas</h2>

@if (session('error_voto'))
    <div class="alert alert-danger">{{ session('error_voto') }}</div>
@endif

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold">Crear encuesta</div>
            <div class="card-body">
                <form method="POST" action="{{ route('encuestas.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Pregunta</label>
                        <input type="text" name="pregunta" class="form-control" value="{{ old('pregunta') }}" maxlength="150" required>
                    </div>
                    <label class="form-label">Opciones (mínimo 2, máximo 8)</label>
                    <div id="opciones">
                        @foreach (old('opciones', ['', '']) as $op)
                            <input type="text" name="opciones[]" class="form-control mb-2" value="{{ $op }}" maxlength="80" placeholder="Opción">
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary mb-3" id="agregarOpcion">+ Agregar opción</button>
                    <button class="btn btn-primary w-100">Crear encuesta</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        @forelse ($encuestas as $e)
            @php
                $total = array_sum(array_column($e['opciones'], 'votos'));
            @endphp
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h5 class="card-title">{{ $e['pregunta'] }}</h5>
                        <small class="text-muted">{{ $total }} voto(s)</small>
                    </div>

                    <form method="POST" action="{{ route('encuestas.votar', $e['id']) }}">
                        @csrf
                        @foreach ($e['opciones'] as $i => $op)
                            @php $pct = $total > 0 ? round($op['votos'] / $total * 100, 1) : 0; @endphp
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="opcion" value="{{ $i }}" id="op-{{ $e['id'] }}-{{ $i }}" required>
                                    <label class="form-check-label d-flex justify-content-between" for="op-{{ $e['id'] }}-{{ $i }}">
                                        <span>{{ $op['texto'] }}</span>
                                        <span class="text-muted small ms-3">{{ $op['votos'] }} ({{ $pct }}%)</span>
                                    </label>
                                </div>
                                <div class="progress mt-1" style="height: 14px;">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $pct }}%" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">{{ $pct > 0 ? $pct . '%' : '' }}</div>
                                </div>
                            </div>
                        @endforeach
                        <button class="btn btn-primary btn-sm">Votar</button>
                    </form>
                </div>
                <div class="card-footer bg-white text-end">
                    <form method="POST" action="{{ route('encuestas.destroy', $e['id']) }}" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Eliminar encuesta</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="alert alert-light text-center">Aún no hay encuestas. ¡Crea la primera!</div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('agregarOpcion').addEventListener('click', () => {
        const cont = document.getElementById('opciones');
        if (cont.querySelectorAll('input').length >= 8) return;
        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'opciones[]';
        input.maxLength = 80;
        input.placeholder = 'Opción';
        input.className = 'form-control mb-2';
        cont.appendChild(input);
    });
</script>
@endpush
