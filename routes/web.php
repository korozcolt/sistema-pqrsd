<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

Route::get('/tracking')->name('tracking');

//using SupportController the method verifyStatus to get the status of the ticket
Route::get('/support/{id}/verify')->name('support.verify');
Route::get('/support')->name('support.store');

Route::get('/publish-log-viewer', function () {
    // Verifica si el usuario está autenticado y es administrador
    if (!Auth::check() || Auth::user()->role !== 'superadmin') {
        abort(403, 'Acceso no autorizado');
    }

    // Ejecuta el comando Artisan
    try {
        Artisan::call('log-viewer:publish');
        return 'Log viewer publicado correctamente: ' . Artisan::output();
    } catch (\Exception $e) {
        return 'Error al ejecutar el comando: ' . $e->getMessage();
    }
})->middleware(['auth'])->name('publish.log-viewer');

require __DIR__.'/auth.php';
