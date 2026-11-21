@extends('layouts.app')

@section('titulo', 'Inicio')

@section('content')
@php
    $ejercicios = [
        ['1', 'Lista de Tareas', 'Gestión de tareas pendientes y completadas', 'tareas.index'],
        ['2', 'Calculadora de Propinas', 'Cálculo de propina y total de cuenta', 'propinas.index'],
        ['3', 'Generador de Contraseñas', 'Generación aleatoria de contraseñas', 'contrasenas.index'],
        ['4', 'Gestor de Gastos', 'Registro y resumen mensual de gastos', 'gastos.index'],
        ['5', 'Sistema de Reservas', 'Disponibilidad y confirmación de citas', 'reservas.index'],
        ['6', 'Gestor de Notas', 'Notas organizadas por categoría y búsqueda', 'notas.index'],
        ['7', 'Calendario de Eventos', 'Administración de eventos y recordatorios', 'eventos.index'],
        ['8', 'Plataforma de Recetas', 'Búsqueda, guardado y filtro de recetas', 'recetas.index'],
        ['9', 'Juego de Memoria', 'Emparejamiento de cartas por niveles', 'memoria.index'],
        ['10', 'Plataforma de Encuestas', 'Creación, votación y resultados de encuestas', 'encuestas.index'],
        ['11', 'Cronómetro Online', 'Control de tiempo y registro de vueltas', 'cronometro.index'],
    ];
@endphp

<h3 class="mb-4">Taller MVC</h3>

<div class="row g-3">
    @foreach ($ejercicios as $ej)
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ $ej[0] }}. {{ $ej[1] }}</h5>
                    <p class="card-text text-muted flex-grow-1">{{ $ej[2] }}</p>
                    <a href="{{ route($ej[3]) }}" class="btn btn-primary mt-2">Abrir</a>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
