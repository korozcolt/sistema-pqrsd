<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Ticket;
use App\Models\SLA;
use App\Enums\StatusTicket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SLACompliance extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        // Calcular cumplimiento de SLA de respuesta
        $responseCompliance = $this->calculateResponseCompliance();

        // Calcular cumplimiento de SLA de resolución
        $resolutionCompliance = $this->calculateResolutionCompliance();

        // Calcular tiempo promedio de resolución
        $avgResolutionTimes = $this->calculateAverageResolutionTimes();

        return [
            Stat::make('SLA Response Compliance', number_format($responseCompliance['percentage'], 1) . '%')
                ->description($responseCompliance['description'])
                ->descriptionIcon('heroicon-m-clock')  // Cambiado
                ->color($this->getColorForPercentage($responseCompliance['percentage']))
                ->chart($responseCompliance['chart']),

            Stat::make('SLA Resolution Compliance', number_format($resolutionCompliance['percentage'], 1) . '%')
                ->description($resolutionCompliance['description'])
                ->descriptionIcon('heroicon-m-check-circle')  // Este está bien
                ->color($this->getColorForPercentage($resolutionCompliance['percentage']))
                ->chart($resolutionCompliance['chart']),

            Stat::make('Average Resolution Time', $avgResolutionTimes['overall_formatted'])
                ->description($avgResolutionTimes['description'])
                ->descriptionIcon('heroicon-m-clock')  // Cambiado
                ->chart($avgResolutionTimes['chart']),
        ];
    }

    private function calculateResponseCompliance(): array
    {
        $thirtyDaysAgo = now()->subDays(30);

        $tickets = Ticket::where('created_at', '>=', $thirtyDaysAgo)
            ->whereNotNull('first_response_at')
            ->with('sla')
            ->get();

        if ($tickets->isEmpty()) {
            return [
                'percentage' => 100,
                'description' => 'No tickets to measure',
                'chart' => array_fill(0, 7, 0)
            ];
        }

        $compliantTickets = $tickets->filter(function ($ticket) {
            if (!$ticket->sla) return false;
            $responseTime = Carbon::parse($ticket->first_response_at)
                ->diffInHours($ticket->created_at);
            return $responseTime <= ($ticket->sla->response_time * 24);
        });

        $percentage = ($compliantTickets->count() / $tickets->count()) * 100;

        // Generar datos para el gráfico de los últimos 7 días
        $chart = collect(range(6, 0))->map(function ($daysAgo) use ($tickets) {
            $date = now()->subDays($daysAgo)->format('Y-m-d');
            $dayTickets = $tickets->filter(function ($ticket) use ($date) {
                return $ticket->created_at->format('Y-m-d') === $date;
            });

            if ($dayTickets->isEmpty()) {
                return 0;
            }

            $dayCompliant = $dayTickets->filter(function ($ticket) {
                if (!$ticket->sla) return false;
                $responseTime = Carbon::parse($ticket->first_response_at)
                    ->diffInHours($ticket->created_at);
                return $responseTime <= ($ticket->sla->response_time * 24);
            });

            return ($dayCompliant->count() / $dayTickets->count()) * 100;
        })->toArray();

        return [
            'percentage' => $percentage,
            'description' => "{$compliantTickets->count()} of {$tickets->count()} tickets within SLA",
            'chart' => $chart
        ];
    }

    private function calculateResolutionCompliance(): array
    {
        $thirtyDaysAgo = now()->subDays(30);

        $tickets = Ticket::whereIn('status', [StatusTicket::Resolved, StatusTicket::Closed])
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->whereNotNull('resolution_at')
            ->with('sla')
            ->get();

        if ($tickets->isEmpty()) {
            return [
                'percentage' => 100,
                'description' => 'No tickets to measure',
                'chart' => array_fill(0, 7, 0)
            ];
        }

        $compliantTickets = $tickets->filter(function ($ticket) {
            if (!$ticket->sla) return false;
            $resolutionTime = Carbon::parse($ticket->resolution_at)
                ->diffInHours($ticket->created_at);
            return $resolutionTime <= ($ticket->sla->resolution_time * 24);
        });

        $percentage = ($compliantTickets->count() / $tickets->count()) * 100;

        // Generar datos para el gráfico de los últimos 7 días
        $chart = collect(range(6, 0))->map(function ($daysAgo) use ($tickets) {
            $date = now()->subDays($daysAgo)->format('Y-m-d');
            $dayTickets = $tickets->filter(function ($ticket) use ($date) {
                return $ticket->resolution_at->format('Y-m-d') === $date;
            });

            if ($dayTickets->isEmpty()) {
                return 0;
            }

            $dayCompliant = $dayTickets->filter(function ($ticket) {
                if (!$ticket->sla) return false;
                $resolutionTime = Carbon::parse($ticket->resolution_at)
                    ->diffInHours($ticket->created_at);
                return $resolutionTime <= ($ticket->sla->resolution_time * 24);
            });

            return ($dayCompliant->count() / $dayTickets->count()) * 100;
        })->toArray();

        return [
            'percentage' => $percentage,
            'description' => "{$compliantTickets->count()} of {$tickets->count()} tickets resolved within SLA",
            'chart' => $chart
        ];
    }

    private function calculateAverageResolutionTimes(): array
    {
        $thirtyDaysAgo = now()->subDays(30);

        $avgTimes = Ticket::whereIn('status', [StatusTicket::Resolved, StatusTicket::Closed])
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->whereNotNull('resolution_at')
            ->select('type', DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolution_at)) as avg_hours'))
            ->groupBy('type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->type->value => round($item->avg_hours ?? 0, 1)];
            });

        $overallAvg = $avgTimes->average() ?? 0;

        // Preparar datos para el gráfico
        $chart = $avgTimes->values()->map(function ($value) {
            return $value ?? 0;
        })->toArray();

        return [
            'overall_formatted' => $this->formatHours($overallAvg),
            'description' => 'Last 30 days average',
            'byType' => $avgTimes->toArray(),
            'chart' => array_pad($chart, 7, 0) // Asegurar que siempre hay 7 valores
        ];
    }

    private function formatHours(?float $hours): string
    {
        // Si es nulo o 0, retornar N/A
        if ($hours === null || $hours === 0) {
            return 'N/A';
        }

        if ($hours < 24) {
            return round($hours, 1) . ' hrs';
        }

        $days = floor($hours / 24);
        $remainingHours = $hours % 24;

        if ($remainingHours < 1) {
            return $days . ' days';
        }

        return $days . ' days ' . round($remainingHours, 1) . ' hrs';
    }

    private function getColorForPercentage(float $percentage): string
    {
        if ($percentage >= 90) return 'success';
        if ($percentage >= 75) return 'warning';
        return 'danger';
    }
}
