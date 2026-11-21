<?php

use App\Http\Controllers\ContrasenaController;
use App\Http\Controllers\CronometroController;
use App\Http\Controllers\EncuestaController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\MemoriaController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\PropinaController;
use App\Http\Controllers\RecetaController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\TareaController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('inicio'))->name('inicio');

// 1. Lista de tareas
Route::get('/tareas', [TareaController::class, 'index'])->name('tareas.index');
Route::post('/tareas', [TareaController::class, 'store'])->name('tareas.store');
Route::post('/tareas/{id}/toggle', [TareaController::class, 'toggle'])->name('tareas.toggle');
Route::delete('/tareas/{id}', [TareaController::class, 'destroy'])->name('tareas.destroy');

// 2. Calculadora de propinas
Route::get('/propinas', [PropinaController::class, 'index'])->name('propinas.index');
Route::post('/propinas', [PropinaController::class, 'calcular'])->name('propinas.calcular');

// 3. Generador de contraseñas
Route::get('/contrasenas', [ContrasenaController::class, 'index'])->name('contrasenas.index');
Route::post('/contrasenas', [ContrasenaController::class, 'generar'])->name('contrasenas.generar');

// 4. Gestor de gastos
Route::get('/gastos', [GastoController::class, 'index'])->name('gastos.index');
Route::post('/gastos', [GastoController::class, 'store'])->name('gastos.store');
Route::delete('/gastos/{id}', [GastoController::class, 'destroy'])->name('gastos.destroy');

// 5. Sistema de reservas
Route::get('/reservas', [ReservaController::class, 'index'])->name('reservas.index');
Route::post('/reservas', [ReservaController::class, 'store'])->name('reservas.store');
Route::delete('/reservas/{id}', [ReservaController::class, 'destroy'])->name('reservas.destroy');

// 6. Gestor de notas
Route::get('/notas', [NotaController::class, 'index'])->name('notas.index');
Route::post('/notas', [NotaController::class, 'store'])->name('notas.store');
Route::delete('/notas/{id}', [NotaController::class, 'destroy'])->name('notas.destroy');

// 7. Calendario de eventos
Route::get('/eventos', [EventoController::class, 'index'])->name('eventos.index');
Route::post('/eventos', [EventoController::class, 'store'])->name('eventos.store');
Route::put('/eventos/{id}', [EventoController::class, 'update'])->name('eventos.update');
Route::delete('/eventos/{id}', [EventoController::class, 'destroy'])->name('eventos.destroy');

// 8. Plataforma de recetas
Route::get('/recetas', [RecetaController::class, 'index'])->name('recetas.index');
Route::post('/recetas', [RecetaController::class, 'store'])->name('recetas.store');
Route::post('/recetas/{id}/favorita', [RecetaController::class, 'favorita'])->name('recetas.favorita');
Route::delete('/recetas/{id}', [RecetaController::class, 'destroy'])->name('recetas.destroy');

// 9. Juego de memoria
Route::get('/memoria', [MemoriaController::class, 'index'])->name('memoria.index');
Route::post('/memoria/puntaje', [MemoriaController::class, 'guardar'])->name('memoria.guardar');
Route::delete('/memoria/puntajes', [MemoriaController::class, 'limpiar'])->name('memoria.limpiar');

// 10. Plataforma de encuestas
Route::get('/encuestas', [EncuestaController::class, 'index'])->name('encuestas.index');
Route::post('/encuestas', [EncuestaController::class, 'store'])->name('encuestas.store');
Route::post('/encuestas/{id}/votar', [EncuestaController::class, 'votar'])->name('encuestas.votar');
Route::delete('/encuestas/{id}', [EncuestaController::class, 'destroy'])->name('encuestas.destroy');

// 11. Cronómetro online
Route::get('/cronometro', [CronometroController::class, 'index'])->name('cronometro.index');
Route::post('/cronometro/vueltas', [CronometroController::class, 'store'])->name('cronometro.store');
Route::delete('/cronometro/vueltas', [CronometroController::class, 'limpiar'])->name('cronometro.limpiar');
