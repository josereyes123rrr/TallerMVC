@extends('layouts.app')

@section('titulo', 'Generador de Contraseñas')

@section('content')
<h2 class="mb-3"> Generador de Contraseñas Seguras</h2>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form method="POST" action="{{ route('contrasenas.generar') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Longitud: <strong id="valorLongitud">{{ old('longitud', 16) }}</strong></label>
                        <input type="range" class="form-range" min="6" max="32" name="longitud" id="longitud" value="{{ old('longitud', 16) }}">
                    </div>

                    @php $marcado = fn ($n) => old('longitud') ? old($n) : true; @endphp
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="mayusculas" value="1" id="may" @checked($marcado('mayusculas'))><label class="form-check-label" for="may">Mayúsculas (A-Z)</label></div>
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="minusculas" value="1" id="min" @checked($marcado('minusculas'))><label class="form-check-label" for="min">Minúsculas (a-z)</label></div>
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="numeros" value="1" id="num" @checked($marcado('numeros'))><label class="form-check-label" for="num">Números (0-9)</label></div>
                    <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="simbolos" value="1" id="sim" @checked($marcado('simbolos'))><label class="form-check-label" for="sim">Símbolos (!@#$...)</label></div>

                    <button class="btn btn-primary w-100">Generar contraseña</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        @if (session('contrasena'))
            <div class="card shadow-sm border-0 border-start border-4 border-success mb-3">
                <div class="card-body">
                    <h6 class="text-muted">Tu contraseña</h6>
                    <div class="input-group">
                        <input type="text" class="form-control font-monospace" id="resultado" value="{{ session('contrasena') }}" readonly>
                        <button class="btn btn-outline-secondary" type="button" id="copiar">Copiar</button>
                    </div>
                </div>
            </div>
        @endif

        @if (count($historial))
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">Últimas generadas (sesión)</div>
                <ul class="list-group list-group-flush">
                    @foreach ($historial as $h)
                        <li class="list-group-item font-monospace small">{{ $h }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    const rango = document.getElementById('longitud');
    rango.addEventListener('input', () => document.getElementById('valorLongitud').textContent = rango.value);

    const copiar = document.getElementById('copiar');
    if (copiar) {
        copiar.addEventListener('click', () => {
            const campo = document.getElementById('resultado');
            campo.select();
            navigator.clipboard.writeText(campo.value).then(() => {
                copiar.textContent = '¡Copiada!';
                setTimeout(() => copiar.textContent = 'Copiar', 1500);
            });
        });
    }
</script>
@endpush
