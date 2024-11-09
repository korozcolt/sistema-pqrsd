<?php

namespace App\Filament\Widgets;

use App\Enums\StatusTicket;
use App\Enums\UserRole;
use App\Models\Ticket;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TicketStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Tickets by Status';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    protected static bool $isLazy = true;

    public ?string $filter = 'today';

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last 7 days',
            'month' => 'Last 30 days',
            'year' => 'This year',
        ];
    }

    protected function getData(): array
    {
        $query = Ticket::query();

        // Filtrar por usuario si es user_web
        if (Auth::user()->role === UserRole::UserWeb) {
            $query->where('user_id', Auth::id());
        }

        // Aplicar filtro de tiempo
        $query->when($this->filter === 'today', fn($q) => $q->whereDate('created_at', today()))
            ->when($this->filter === 'week', fn($q) => $q->where('created_at', '>=', now()->subDays(7)))
            ->when($this->filter === 'month', fn($q) => $q->where('created_at', '>=', now()->subDays(30)))
            ->when($this->filter === 'year', fn($q) => $q->whereYear('created_at', now()->year));

        // Obtener datos agrupados por status
        $data = $query->get()
            ->groupBy('status')
            ->map(fn($tickets) => $tickets->count());

        // Asegurar que todos los estados estén representados
        $allStatuses = collect(StatusTicket::cases())->map(fn($status) => $status->value);
        $filledData = $allStatuses->mapWithKeys(fn($status) => [$status => $data[$status] ?? 0]);

        // Colores para cada estado
        $colors = [
            StatusTicket::Pending->value => '#fbbf24',    // warning
            StatusTicket::In_Progress->value => '#60a5fa', // info
            StatusTicket::Closed->value => '#6b7280',      // gray
            StatusTicket::Resolved->value => '#34d399',    // success
            StatusTicket::Rejected->value => '#ef4444',    // danger
            StatusTicket::Reopened->value => '#f97316',    // orange
        ];

        // Etiquetas personalizadas para el gráfico
        $labels = [
            StatusTicket::Pending->value => 'Pending',
            StatusTicket::In_Progress->value => 'In Progress',
            StatusTicket::Closed->value => 'Closed',
            StatusTicket::Resolved->value => 'Resolved',
            StatusTicket::Rejected->value => 'Rejected',
            StatusTicket::Reopened->value => 'Reopened',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Tickets by Status',
                    'data' => $filledData->values()->toArray(),
                    'backgroundColor' => $allStatuses->map(fn($status) => $colors[$status])->toArray(),
                ],
            ],
            'labels' => $allStatuses->map(fn($status) => $labels[$status])->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
                'tooltips' => [
                    'enabled' => true,
                ],
            ],
            'maintainAspectRatio' => false,
            'responsive' => true,
            'cutout' => '70%',
        ];
    }
}
