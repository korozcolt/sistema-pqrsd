<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Enums\TicketStatus;
use App\Enums\Priority;
use App\Enums\StatusTicket;
use App\Enums\TicketType;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LatestTickets extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2; // Posición en el dashboard

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
                    ->label('Ticket')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->limit(30)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        StatusTicket::Pending->value => 'warning',
                        StatusTicket::In_Progress->value => 'info',
                        StatusTicket::Resolved->value => 'success',
                        StatusTicket::Rejected->value => 'danger',
                        StatusTicket::Closed->value => 'gray',
                        StatusTicket::Reopened->value => 'warning',
                        default => 'primary',
                    }),

                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Priority::Low->value => 'info',
                        Priority::Medium->value => 'warning',
                        Priority::High->value => 'danger',
                        Priority::Urgent->value => 'danger',
                        default => 'primary',
                    }),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Created By')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (Ticket $record): string => route('filament.admin.resources.tickets.view', $record))
                    ->icon('heroicon-m-eye'),
            ])
            ->paginated(false)
            ->defaultSort('created_at', 'desc');
    }

    protected function getTableHeading(): string
    {
        return 'Latest Tickets';
    }
}
