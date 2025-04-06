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
    try {
        Artisan::call('log-viewer:publish');
        return 'Log viewer publicado correctamente: ' . Artisan::output();
    } catch (\Exception $e) {
        return 'Error al ejecutar el comando: ' . $e->getMessage();
    }
})->name('publish.log-viewer');

require __DIR__.'/auth.php';
