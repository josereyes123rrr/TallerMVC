@extends('layouts.app')

@section('titulo', 'Juego de Memoria')

@section('content')
<h2 class="mb-3">Juego de Memoria</h2>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div class="btn-group" role="group" id="niveles">
                        <button type="button" class="btn btn-outline-primary active" data-nivel="facil">Fácil (4 pares)</button>
                        <button type="button" class="btn btn-outline-primary" data-nivel="medio">Medio (6 pares)</button>
                        <button type="button" class="btn btn-outline-primary" data-nivel="dificil">Difícil (8 pares)</button>
                    </div>
                    <div class="fs-5">Intentos: <strong id="intentos">0</strong></div>
                    <button type="button" class="btn btn-secondary" id="reiniciar">Reiniciar</button>
                </div>

                <div id="tablero" class="row g-2 justify-content-center"></div>

                <div id="victoria" class="alert alert-success mt-4 d-none">
                    <h5 class="alert-heading">¡Ganaste en <span id="intentosFinal"></span> intentos!</h5>
                    <form method="POST" action="{{ route('memoria.guardar') }}" class="row g-2">
                        @csrf
                        <input type="hidden" name="nivel" id="campoNivel">
                        <input type="hidden" name="intentos" id="campoIntentos">
                        <div class="col-md-8">
                            <input type="text" name="nombre" class="form-control" placeholder="Tu nombre" maxlength="40" required>
                        </div>
                        <div class="col-md-4"><button class="btn btn-success w-100">Guardar puntaje</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Puntajes (sesión)</span>
                @if (count($puntajes))
                    <form method="POST" action="{{ route('memoria.limpiar') }}" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Limpiar</button>
                    </form>
                @endif
            </div>
            <ul class="list-group list-group-flush">
                @forelse ($puntajes as $p)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $p['nombre'] }} <span class="badge text-bg-secondary">{{ ucfirst($p['nivel']) }}</span></span>
                        <strong>{{ $p['intentos'] }} int.</strong>
                    </li>
                @empty
                    <li class="list-group-item text-muted text-center py-3">Aún no hay puntajes.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .carta { height: 90px; font-size: 2.2rem; user-select: none; }
    .carta.oculta { background: #0d6efd; color: #fff; }
    .carta.abierta { background: #fff; border: 2px solid #0d6efd; }
    .carta.encontrada { background: #d1e7dd; border: 2px solid #198754; }
</style>
<script>
    const PARES = { facil: 4, medio: 6, dificil: 8 };
    const EMOJIS = ['🍎', '🐶', '🚀', '🎸', '🌵', '🍕', '⚽', '🐱', '🌈', '🎲'];

    let nivel = 'facil', cartas = [], abiertas = [], bloqueo = false, intentos = 0, encontrados = 0;

    const tablero = document.getElementById('tablero');
    const marcador = document.getElementById('intentos');
    const victoria = document.getElementById('victoria');

    function mezclar(arr) {
        for (let i = arr.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [arr[i], arr[j]] = [arr[j], arr[i]];
        }
        return arr;
    }

    function iniciar() {
        const emojis = mezclar([...EMOJIS]).slice(0, PARES[nivel]);
        cartas = mezclar([...emojis, ...emojis]).map((emoji, i) => ({ id: i, emoji, estado: 'oculta' }));
        abiertas = []; bloqueo = false; intentos = 0; encontrados = 0;
        marcador.textContent = 0;
        victoria.classList.add('d-none');
        dibujar();
    }

    function dibujar() {
        tablero.innerHTML = '';
        cartas.forEach(c => {
            const col = document.createElement('div');
            col.className = 'col-3';
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn w-100 carta ' + c.estado;
            btn.textContent = c.estado === 'oculta' ? '❓' : c.emoji;
            btn.addEventListener('click', () => voltear(c));
            col.appendChild(btn);
            tablero.appendChild(col);
        });
    }

    function voltear(c) {
        if (bloqueo || c.estado !== 'oculta') return;
        c.estado = 'abierta';
        abiertas.push(c);
        dibujar();

        if (abiertas.length === 2) {
            intentos++;
            marcador.textContent = intentos;
            const [a, b] = abiertas;
            if (a.emoji === b.emoji) {
                a.estado = b.estado = 'encontrada';
                encontrados++;
                abiertas = [];
                dibujar();
                if (encontrados === PARES[nivel]) ganar();
            } else {
                bloqueo = true;
                setTimeout(() => {
                    a.estado = b.estado = 'oculta';
                    abiertas = [];
                    bloqueo = false;
                    dibujar();
                }, 800);
            }
        }
    }

    function ganar() {
        document.getElementById('intentosFinal').textContent = intentos;
        document.getElementById('campoNivel').value = nivel;
        document.getElementById('campoIntentos').value = intentos;
        victoria.classList.remove('d-none');
    }

    document.querySelectorAll('#niveles button').forEach(b => {
        b.addEventListener('click', () => {
            document.querySelectorAll('#niveles button').forEach(x => x.classList.remove('active'));
            b.classList.add('active');
            nivel = b.dataset.nivel;
            iniciar();
        });
    });
    document.getElementById('reiniciar').addEventListener('click', iniciar);

    iniciar();
</script>
@endpush
