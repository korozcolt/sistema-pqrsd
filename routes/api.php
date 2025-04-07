<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PublicTicketController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas públicas para el sistema de tickets
Route::prefix('public')->group(function () {
    // Crear nuevo ticket
    Route::post('/tickets', [PublicTicketController::class, 'store']);
    
    // Consultar ticket por número y token
    Route::get('/tickets/{ticket_number}', [PublicTicketController::class, 'show']);
    
    // Verificar existencia de ticket (para validación de formulario)
    Route::get('/tickets/verify/{ticket_number}', [PublicTicketController::class, 'verify']);
    
    // Añadir comentario a un ticket
    Route::post('/tickets/{ticket_number}/comments', [PublicTicketController::class, 'addComment']);
    
    // Cerrar un ticket
    Route::post('/tickets/{ticket_number}/close', [PublicTicketController::class, 'closeTicket']);
    
    // Obtener tipos de tickets (para formulario dinámico)
    Route::get('/ticket-types', [PublicTicketController::class, 'getTicketTypes']);
});
