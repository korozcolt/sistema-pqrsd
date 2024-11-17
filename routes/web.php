<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::prefix('/')->group(function () {
    // ---------* PAGE STATIC *----------------- //
    Route::get('/',[HomeController::class,'index'])->name('page._home');
    // ---------* PAGE DYNAMIC *---------------- //
    Route::get('/{page}', HomeController::class)->name('page')->where('page','about|faq|contact|_home|service|api|policy');
});

Route::get('/tickets', function () {
    return view('pages.tickets', [
        'metaTitle' => 'Sistema de Tickets',
        'info' => app(HomeController::class)->pageInfo()
    ]);
})->name('tickets');

Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

Route::get('/tracking')->name('tracking');

//using SupportController the method verifyStatus to get the status of the ticket
Route::get('/support/{id}/verify')->name('support.verify');
Route::get('/support', 'SupportController@verifyStatus')->name('support.store');

require __DIR__.'/auth.php';
