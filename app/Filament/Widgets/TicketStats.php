<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Ticket;
use App\Enums\StatusTicket;
use App\Enums\Priority;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TicketStats extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $query = Ticket::query();

        // Si es un usuario web, solo ve sus tickets
        if (Auth::user()->role === UserRole::UserWeb) {
            $query->where('user_id', Auth::id());
        }

        // Estadísticas generales
        $totalTickets = $query->count();
        $pendingTickets = $query->clone()->where('status', StatusTicket::Pending)->count();
        $inProgressTickets = $query->clone()->where('status', StatusTicket::In_Progress)->count();
        $urgentTickets = $query->clone()
            ->where('priority', Priority::Urgent)
            ->whereIn('status', [StatusTicket::Pending, StatusTicket::In_Progress])
            ->count();

        // Obtener tendencia de los últimos 7 días
        $last7Days = collect(range(6, 0))->map(function ($daysAgo) use ($query) {
            $date = now()->subDays($daysAgo)->format('Y-m-d');
            return $query->clone()
                ->whereDate('created_at', $date)
                ->count();
        })->toArray();

        // Obtener tendencia de resolución de los últimos 7 días
        $resolutionTrend = collect(range(6, 0))->map(function ($daysAgo) use ($query) {
            $date = now()->subDays($daysAgo)->format('Y-m-d');
            return $query->clone()
                ->where('status', StatusTicket::Resolved)
                ->whereDate('resolution_at', $date)
                ->count();
        })->toArray();

        return [
            Stat::make('Total Tickets', $totalTickets)
                ->description($this->getTicketTrend())
                ->descriptionIcon($this->getTicketTrendIcon())
                ->chart($last7Days)
                ->color('gray'),

            Stat::make('Pending', $pendingTickets)
                ->description('Awaiting response')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('In Progress', $inProgressTickets)
                ->description('Being processed')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->chart($this->getProgressChart())
                ->color('info'),

            Stat::make('Urgent', $urgentTickets)
                ->description('High priority tickets')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make('Resolution Rate', $this->getResolutionRate() . '%')
                ->description('Last 30 days')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->chart($resolutionTrend)
                ->color('success'),

            Stat::make('Avg Response Time', $this->getAverageResponseTime())
                ->description('Last 30 days')
                ->descriptionIcon('heroicon-m-clock')
                ->color('gray'),
        ];
    }

    private function getTicketTrend(): string
    {
        $previousPeriod = Ticket::query()
            ->whereBetween('created_at', [
                now()->subDays(14),
                now()->subDays(7)
            ])
            ->count();

        $currentPeriod = Ticket::query()
            ->whereBetween('created_at', [
                now()->subDays(7),
                now()
            ])
            ->count();

        if ($previousPeriod === 0) {
            return 'No previous data';
        }

        $percentageChange = (($currentPeriod - $previousPeriod) / $previousPeriod) * 100;

        return number_format(abs($percentageChange), 1) . '% ' .
               ($percentageChange >= 0 ? 'increase' : 'decrease');
    }

    private function getTicketTrendIcon(): string
    {
        $previousPeriod = Ticket::query()
            ->whereBetween('created_at', [
                now()->subDays(14),
                now()->subDays(7)
            ])
            ->count();

        $currentPeriod = Ticket::query()
            ->whereBetween('created_at', [
                now()->subDays(7),
                now()
            ])
            ->count();

        return $currentPeriod >= $previousPeriod
            ? 'heroicon-m-arrow-trending-up'
            : 'heroicon-m-arrow-trending-down';
    }

    private function getProgressChart(): array
    {
        return collect(range(6, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo)->format('Y-m-d');
            return Ticket::where('status', StatusTicket::In_Progress)
                ->whereDate('updated_at', $date)
                ->count();
        })->toArray();
    }

    private function getResolutionRate(): float
    {
        $thirtyDaysAgo = now()->subDays(30);

        $totalTickets = Ticket::where('created_at', '>=', $thirtyDaysAgo)->count();

        if ($totalTickets === 0) {
            return 0;
        }

        $resolvedTickets = Ticket::where('status', StatusTicket::Resolved)
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->count();

        return round(($resolvedTickets / $totalTickets) * 100, 1);
    }

    private function getAverageResponseTime(): string
    {
        $thirtyDaysAgo = now()->subDays(30);

        $tickets = Ticket::whereNotNull('first_response_at')
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->get();

        if ($tickets->isEmpty()) {
            return 'N/A';
        }

        $totalResponseTime = $tickets->sum(function ($ticket) {
            return Carbon::parse($ticket->first_response_at)
                ->diffInMinutes($ticket->created_at);
        });

        $averageMinutes = $totalResponseTime / $tickets->count();

        if ($averageMinutes < 60) {
            return round($averageMinutes) . ' min';
        }

        if ($averageMinutes < 1440) { // 24 hours
            return round($averageMinutes / 60, 1) . ' hours';
        }

        return round($averageMinutes / 1440, 1) . ' days';
    }
}
