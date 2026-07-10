<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\SessionTimeout;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VentaController;

use App\Http\Controllers\CompraController;

//Route::get('/compras', [CompraController::class, 'index']);
Route::resource('compras', CompraController::class);

Route::resource('productos', ProductoController::class)
    ->middleware(['auth']);


Route::middleware(['auth', SessionTimeout::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::get('/', [AuthController::class, 'showLogin'])->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


    Route::middleware(['auth', 'role:ADMINISTRADOR'])->group(function () {
    Route::get('/admin', function () {
        return "Panel Admin";
    });
});

});

Route::post('/compras/{id}/aprobar', [CompraController::class, 'aprobar'])->name('compras.aprobar');

Route::resource('clientes', ClienteController::class);

Route::resource('usuarios', UsuarioController::class);
    Route::get(
    '/usuarios/{id}/password',
    [UsuarioController::class, 'passwordForm']
    )->name('usuarios.password');

    Route::put(
        '/usuarios/{id}/password',
        [UsuarioController::class, 'updatePassword']
    )->name('usuarios.password.update');

Route::resource('productos', ProductoController::class);

Route::get('/ventas', [VentaController::class, 'index'])
    ->name('ventas.index')
    ->middleware('auth');

Route::post('/ventas', [VentaController::class, 'procesar'])
    ->name('ventas.procesar')
    ->middleware('auth');

Route::get('/ventas/historial', [VentaController::class, 'historial'])
    ->name('ventas.historial')
    ->middleware('auth');

Route::get('/ventas/{id}', [VentaController::class, 'show'])
    ->name('ventas.show')
    ->middleware('auth');

Route::put('/ventas/{id}/anular', [VentaController::class, 'anular'])
    ->name('ventas.anular')
    ->middleware('auth');
