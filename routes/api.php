<?php

use App\Http\Controllers\GameController;
use App\Models\Game;
use Illuminate\Support\Facades\Route;

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

Route::model('game', Game::class);

Route::prefix('game')->group(function () {
    Route::get('opened', [GameController::class, 'getListOpened']);
    Route::get('finished', [GameController::class, 'getListFinished']);
    Route::post('create', [GameController::class, 'postCreate']);
    Route::post('{game}/enter', [GameController::class, 'postEnter'])->name('game.enter');
    Route::post('{game}/turn', [GameController::class, 'postTurn'])->name('game.turn');
    Route::get('{id}/info', [GameController::class, 'getInfo'])->name('game.info');
});
