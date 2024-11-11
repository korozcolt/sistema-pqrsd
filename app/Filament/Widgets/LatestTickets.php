<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Enums\StatusTicket;
use App\Enums\Priority;
use App\Enums\TicketType;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LatestTickets extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Ticket::query()
                    ->when(Auth::user()->role === UserRole::UserWeb, function (Builder $query) {
                        $query->where('user_id', Auth::id());
                    })
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->label('Número')
                    ->searchable()
                    ->sortable()
                    ->copyable() // Permite copiar el número de ticket
                    ->copyMessage('Número de ticket copiado')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->limit(30)
                    ->searchable()
                    ->sortable()
                    ->tooltip(fn (Ticket $record): string => $record->title), // Muestra el título completo al pasar el mouse

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge(), // Ya usa las traducciones del enum

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge(), // Ya usa las traducciones del enum

                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridad')
                    ->badge(), // Ya usa las traducciones del enum

                Tables\Columns\TextColumn::make('department.name')
                    ->label('Departamento')
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Creado por')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime('d/m/Y H:i') // Formato de fecha en español
                    ->description(fn (Ticket $record): string => $record->created_at->diffForHumans()) // Tiempo relativo
                    ->sortable()
                    ->toggleable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Ver Ticket')
                    ->url(fn (Ticket $record): string => route('filament.admin.resources.tickets.view', $record))
                    ->icon('heroicon-m-eye')
                    ->tooltip('Ver detalles del ticket'),
            ])
            ->emptyStateHeading('No hay tickets recientes')
            ->emptyStateDescription(
                Auth::user()->role === UserRole::UserWeb
                    ? 'No has creado ningún ticket todavía.'
                    : 'No hay tickets registrados en el sistema.'
            )
            ->emptyStateIcon('heroicon-o-ticket')
            ->paginated(false)
            ->defaultSort('created_at', 'desc')
            ->poll('30s'); // Actualización automática cada 30 segundos
    }

    protected function getTableHeading(): string
    {
        return Auth::user()->role === UserRole::UserWeb
            ? 'Mis Últimos Tickets'
            : 'Tickets Recientes';
    }

    protected function getTableDescription(): ?string
    {
        return Auth::user()->role === UserRole::UserWeb
            ? 'Tus tickets más recientes'
            : 'Los últimos 5 tickets registrados en el sistema';
    }
}
