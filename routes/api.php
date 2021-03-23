<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JuegoController;
use App\Http\Controllers\LigaController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\PartidoController;
use App\Http\Controllers\NoticiaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('users')->group(function () {

        Route::post('/registro',[UserController::class, 'createUser']);
        Route::post('/login',[UserController::class, 'Login']);
        Route::post('/cambiarContrasena',[UserController::class, 'changePassword']);
        Route::post('/recuperarcontrasena',[UserController::class, 'resetPassword']);
        Route::get('/datos',[UserController::class, 'mostrarDatos']);
        Route::get('/equipoUser',[UserController::class, 'equipoUsuario']);
});
Route::prefix('games')->group(function () {

        Route::post('/create',[JuegoController::class, 'createJuego']);
        Route::get('/listaJuegos',[JuegoController::class, 'listaJuegos']);

});
Route::prefix('ligas')->group(function () {

        Route::post('/create',[LigaController::class, 'createLiga']);
        Route::get('/listaLigas',[LigaController::class, 'listaLigas']);
        Route::get('/clasificacionLec',[PartidoController::class, 'clasificatoriaLec']);
        Route::get('/clasificacionLcs',[PartidoController::class, 'clasificatoriaLcs']);


});
Route::prefix('equipos')->group(function () {

        Route::post('/create',[EquipoController::class, 'createEquipo']);
        Route::post('/lista',[EquipoController::class, 'listaEquipos']);
        Route::post('/elegirEquipo',[EquipoController::class, 'elegirEquipo']);

});
Route::prefix('partidos')->group(function () {

        Route::post('/create',[PartidoController::class, 'createPartido']);
        Route::post('/updatePartido',[PartidoController::class, 'updatePartido']);
        Route::get('/listaPartidosLec',[PartidoController::class, 'listaPartidosLec']);
        Route::get('/listaPartidosLcs',[PartidoController::class, 'listaPartidosLcs']);
        Route::get('/proximoPartido',[PartidoController::class, 'proximoPartido']);
        Route::get('/partidosEquipo',[PartidoController::class, 'partidosEquipo']);
        Route::get('/ultimoPartido',[PartidoController::class, 'ultimoPartido']);


});
Route::prefix('noticias')->group(function () {

        Route::post('/create',[NoticiaController::class, 'createNoticia']);
        Route::get('/lista',[NoticiaController::class, 'listaNoticias']);
        Route::get('/listaFiltrada',[NoticiaController::class, 'listaFiltrada']);

});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
