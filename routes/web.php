<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::view('/', 'welcome');

Route::prefix('/')->group(function () {
    // ---------* PAGE STATIC *----------------- //
    Route::get('/',[HomeController::class,'index'])->name('page._home');
    // ---------* PAGE DYNAMIC *---------------- //
    Route::get('/{page}', HomeController::class)->name('page')->where('page','about|faq|contact|_home|service|api|policy');
});

Route::get('/tracking')->name('tracking');

//using SupportController the method verifyStatus to get the status of the ticket
Route::get('/support/{id}/verify')->name('support.verify');
Route::get('/support', 'SupportController@verifyStatus')->name('support.store');

require __DIR__.'/auth.php';
