@extends('layouts.app')

@section('titulo', 'Cronómetro Online')

@section('content')
<h2 class="mb-3">Cronómetro Online</h2>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 text-center">
            <div class="card-body py-5">
                <div id="pantalla" class="display-1 font-monospace mb-4">00:00.00</div>
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <button class="btn btn-success btn-lg" id="iniciar">Iniciar</button>
                    <button class="btn btn-warning btn-lg" id="pausar" disabled>Pausar</button>
                    <button class="btn btn-secondary btn-lg" id="reiniciar">Reiniciar</button>
                    <button class="btn btn-primary btn-lg" id="vuelta" disabled>Guardar vuelta</button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Vueltas guardadas (servidor)</span>
                <button class="btn btn-sm btn-outline-danger" id="limpiar">Limpiar historial</button>
            </div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light"><tr><th>#</th><th>Parcial</th><th>Total</th></tr></thead>
                    <tbody id="listaVueltas"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const URL_VUELTAS = @json(route('cronometro.store'));
    const URL_LIMPIAR = @json(route('cronometro.limpiar'));
    const TOKEN = document.querySelector('meta[name="csrf-token"]').content;

    const pantalla = document.getElementById('pantalla');
    const btnIniciar = document.getElementById('iniciar');
    const btnPausar = document.getElementById('pausar');
    const btnVuelta = document.getElementById('vuelta');
    const lista = document.getElementById('listaVueltas');

    let acumulado = 0, inicio = 0, intervalo = null;

    const pad = (n, l = 2) => String(n).padStart(l, '0');
    function formato(t) {
        const m = Math.floor(t / 60000), s = Math.floor(t / 1000) % 60, c = Math.floor(t / 10) % 100;
        return pad(m) + ':' + pad(s) + '.' + pad(c);
    }
    const tiempo = () => acumulado + (intervalo ? Date.now() - inicio : 0);
    const pintar = () => { pantalla.textContent = formato(tiempo()); };

    btnIniciar.addEventListener('click', () => {
        if (intervalo) return;
        inicio = Date.now();
        intervalo = setInterval(pintar, 30);
        btnIniciar.disabled = true;
        btnPausar.disabled = false;
        btnVuelta.disabled = false;
    });

    btnPausar.addEventListener('click', () => {
        if (!intervalo) return;
        acumulado += Date.now() - inicio;
        clearInterval(intervalo);
        intervalo = null;
        pintar();
        btnIniciar.disabled = false;
        btnIniciar.textContent = 'Reanudar';
        btnPausar.disabled = true;
    });

    document.getElementById('reiniciar').addEventListener('click', () => {
        clearInterval(intervalo);
        intervalo = null;
        acumulado = 0;
        pintar();
        btnIniciar.disabled = false;
        btnIniciar.textContent = 'Iniciar';
        btnPausar.disabled = true;
        btnVuelta.disabled = true;
    });

    async function peticion(url, metodo, cuerpo) {
        const r = await fetch(url, {
            method: metodo,
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': TOKEN },
            body: cuerpo ? JSON.stringify(cuerpo) : undefined,
        });
        if (!r.ok) throw new Error('Error ' + r.status);
        return r.json();
    }

    function pintarVueltas(vueltas) {
        if (!vueltas.length) {
            lista.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">Sin vueltas guardadas.</td></tr>';
            return;
        }
        lista.innerHTML = vueltas.slice().reverse().map(v =>
            `<tr><td>${v.n}</td><td class="font-monospace">${formato(v.parcial_ms)}</td><td class="font-monospace">${formato(v.total_ms)}</td></tr>`
        ).join('');
    }

    btnVuelta.addEventListener('click', async () => {
        try {
            const datos = await peticion(URL_VUELTAS, 'POST', { tiempo_ms: Math.round(tiempo()) });
            pintarVueltas(datos.vueltas);
        } catch (e) { alert('No se pudo guardar la vuelta.'); }
    });

    document.getElementById('limpiar').addEventListener('click', async () => {
        try {
            const datos = await peticion(URL_LIMPIAR, 'DELETE');
            pintarVueltas(datos.vueltas);
        } catch (e) { alert('No se pudo limpiar el historial.'); }
    });

    pintarVueltas(@json($vueltas));
</script>
@endpush
