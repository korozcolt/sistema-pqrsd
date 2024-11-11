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
    protected static ?string $heading = 'Distribución de Tickets';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    protected static bool $isLazy = true;

    public ?string $filter = 'today';

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Hoy',
            'week' => 'Últimos 7 días',
            'month' => 'Últimos 30 días',
            'year' => 'Este año',
        ];
    }

    protected function getData(): array
    {
        $query = Ticket::query();

        if (Auth::user()->role === UserRole::UserWeb) {
            $query->where('user_id', Auth::id());
        }

        $query->when($this->filter === 'today', fn($q) => $q->whereDate('created_at', today()))
            ->when($this->filter === 'week', fn($q) => $q->where('created_at', '>=', now()->startOfWeek()))
            ->when($this->filter === 'month', fn($q) => $q->where('created_at', '>=', now()->startOfMonth()))
            ->when($this->filter === 'year', fn($q) => $q->where('created_at', '>=', now()->startOfYear()));

        $total = $query->count();

        $data = $query->get()
            ->groupBy('status')
            ->map(function($tickets) use ($total) {
                $count = $tickets->count();
                return [
                    'count' => $count,
                    'percentage' => $total > 0 ? round(($count / $total) * 100, 1) : 0
                ];
            });

        $allStatuses = collect(StatusTicket::cases());
        $filledData = $allStatuses->mapWithKeys(function($status) use ($data) {
            $statusData = $data[$status->value] ?? ['count' => 0, 'percentage' => 0];
            return [$status->value => $statusData['count']];
        });

        $colors = [
            StatusTicket::Pending->value => '#fbbf24',
            StatusTicket::In_Progress->value => '#3b82f6',
            StatusTicket::Closed->value => '#4b5563',
            StatusTicket::Resolved->value => '#10b981',
            StatusTicket::Rejected->value => '#dc2626',
            StatusTicket::Reopened->value => '#ea580c',
        ];

        $labels = [
            StatusTicket::Pending->value => 'Pendientes',
            StatusTicket::In_Progress->value => 'En Proceso',
            StatusTicket::Closed->value => 'Cerrados',
            StatusTicket::Resolved->value => 'Resueltos',
            StatusTicket::Rejected->value => 'Rechazados',
            StatusTicket::Reopened->value => 'Reabiertos',
        ];

        $labelsWithPercentages = $allStatuses->mapWithKeys(function($status) use ($labels, $data) {
            $statusData = $data[$status->value] ?? ['count' => 0, 'percentage' => 0];
            $count = $statusData['count'];
            $percentage = $statusData['percentage'];
            return [
                $status->value => "{$labels[$status->value]}: $count ($percentage%)"
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Tickets por Estado',
                    'data' => $filledData->values()->toArray(),
                    'backgroundColor' => $allStatuses->map(fn($status) => $colors[$status->value])->toArray(),
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                    'hoverOffset' => 15,
                ],
            ],
            'labels' => $labelsWithPercentages->values()->toArray(),
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
                    'labels' => [
                        'padding' => 20,
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                        'font' => [
                            'size' => 12,
                            'weight' => 'bold',
                        ],
                    ],
                ],
                'tooltip' => [
                    'enabled' => true,
                    'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                    'padding' => 12,
                    'titleFont' => [
                        'size' => 14,
                    ],
                    'bodyFont' => [
                        'size' => 13,
                    ],
                ],
            ],
            'maintainAspectRatio' => false,
            'responsive' => true,
            'cutout' => '70%',
            'animation' => [
                'animateScale' => true,
                'animateRotate' => true,
            ],
        ];
    }

    public static function canView(): bool
    {
        return true;
    }
}
