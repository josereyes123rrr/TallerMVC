@extends('layouts.app')

@section('titulo', 'Gestor de Gastos')

@section('content')
<h2 class="mb-3">Gestor de Gastos</h2>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold">Nuevo gasto</div>
            <div class="card-body">
                <form method="POST" action="{{ route('gastos.store') }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">Descripción</label>
                        <input type="text" name="descripcion" class="form-control" value="{{ old('descripcion') }}" maxlength="120" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Monto</label>
                        <input type="number" step="0.01" min="0.01" name="monto" class="form-control" value="{{ old('monto') }}" required>
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
                        <label class="form-label">Fecha</label>
                        <input type="date" name="fecha" class="form-control" value="{{ old('fecha', date('Y-m-d')) }}" required>
                    </div>
                    <button class="btn btn-primary w-100">Registrar gasto</button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-white fw-semibold">Resumen</div>
            <div class="card-body">
                <div class="fs-4 fw-bold mb-3">Total: ${{ number_format($total, 2) }}</div>
                @forelse ($porCategoria as $cat => $monto)
                    @php $pct = $total > 0 ? round($monto / $total * 100, 1) : 0; @endphp
                    <div class="mb-2">
                        <div class="d-flex justify-content-between small">
                            <span>{{ $cat }}</span>
                            <span>${{ number_format($monto, 2) }} ({{ $pct }}%)</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Sin gastos registrados.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Fecha</th><th>Descripción</th><th>Categoría</th><th class="text-end">Monto</th><th></th></tr>
                    </thead>
                    <tbody>
                        @forelse ($gastos as $g)
                            <tr>
                                <td>{{ $g['fecha'] }}</td>
                                <td>{{ $g['descripcion'] }}</td>
                                <td><span class="badge text-bg-secondary">{{ $g['categoria'] }}</span></td>
                                <td class="text-end">${{ number_format($g['monto'], 2) }}</td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('gastos.destroy', $g['id']) }}" class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Aún no hay gastos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
