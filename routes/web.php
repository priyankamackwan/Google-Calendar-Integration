<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('events');
});

Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');
Route::group(['middleware' => ['auth']], function () {
    Route::get('events', [EventController::class, 'index'])->name('events.index');
    Route::get('events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('events', [EventController::class, 'store'])->name('events.store');
    Route::get('events/{eventId}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('events/{eventId}', [EventController::class, 'update'])->name('events.update');
    Route::delete('events/{eventId}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::get('events/datatable', [EventController::class, 'datatable'])->name('events.datatable');
    Route::get('callback/google', [EventController::class, 'callbackGoogle'])->name('callback.google');
});