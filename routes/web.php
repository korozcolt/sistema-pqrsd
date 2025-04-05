<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;


Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

Route::get('/tracking')->name('tracking');

//using SupportController the method verifyStatus to get the status of the ticket
Route::get('/support/{id}/verify')->name('support.verify');
Route::get('/support')->name('support.store');

require __DIR__.'/auth.php';
