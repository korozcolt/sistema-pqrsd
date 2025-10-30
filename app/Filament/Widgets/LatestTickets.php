<?php

namespace App\Filament\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
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
                    // Filtrar solo tickets abiertos y en proceso
                    ->whereNotIn('status', [
                        StatusTicket::Closed,
                        StatusTicket::Resolved,
                        StatusTicket::Rejected
                    ])
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('ticket_number')
                    ->label('Número')
                    ->searchable()
                    ->sortable()
                    ->copyable() // Permite copiar el número de ticket
                    ->copyMessage('Número de ticket copiado')
                    ->copyMessageDuration(1500),

                TextColumn::make('title')
                    ->label('Título')
                    ->limit(30)
                    ->searchable()
                    ->sortable()
                    ->tooltip(fn (Ticket $record): string => $record->title), // Muestra el título completo al pasar el mouse

                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge(), // Ya usa las traducciones del enum

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge(), // Ya usa las traducciones del enum

                TextColumn::make('priority')
                    ->label('Prioridad')
                    ->badge(), // Ya usa las traducciones del enum

                TextColumn::make('department.name')
                    ->label('Departamento')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Creado por')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime('d/m/Y H:i') // Formato de fecha en español
                    ->description(fn (Ticket $record): string => $record->created_at->diffForHumans()) // Tiempo relativo
                    ->sortable()
                    ->toggleable(),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('Ver Ticket')
                    ->url(fn (Ticket $record): string => route('filament.admin.resources.tickets.view', $record))
                    ->icon('heroicon-m-eye')
                    ->tooltip('Ver detalles del ticket'),
            ])
            ->emptyStateHeading('No hay tickets activos')
            ->emptyStateDescription(
                Auth::user()->role === UserRole::UserWeb
                    ? 'No tienes tickets abiertos en este momento.'
                    : 'No hay tickets activos que requieran atención.'
            )
            ->emptyStateIcon('heroicon-o-ticket')
            ->paginated(false)
            ->defaultSort('created_at', 'desc')
            ->poll('30s'); // Actualización automática cada 30 segundos
    }

    protected function getTableHeading(): string
    {
        return Auth::user()->role === UserRole::UserWeb
            ? 'Mis Tickets Activos'
            : 'Tickets Activos';
    }

    protected function getTableDescription(): ?string
    {
        return Auth::user()->role === UserRole::UserWeb
            ? 'Tus tickets abiertos o en proceso'
            : 'Los tickets que requieren atención (excluye cerrados, resueltos y rechazados)';
    }
}
