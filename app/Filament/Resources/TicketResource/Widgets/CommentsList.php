<?php

namespace App\Filament\Resources\TicketResource\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;

class CommentsList extends Widget
{
    protected string $view = 'filament.resources.ticket-resource.widgets.comments-list';

    protected int | string | array $columnSpan = 'full';

    public ?Model $record = null;

    /**
     * Determine if the widget should be displayed.
     */
    public static function canView(): bool
    {
        return true;
    }

    protected function getViewData(): array
    {
        return [
            'comments' => $this->record->comments()
                ->when(Auth::user()->role === UserRole::UserWeb, function ($query) {
                    $query->where('is_internal', false);
                })
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get(),
            'canAddComment' => !in_array($this->record->status, ['closed', 'rejected']) &&
                in_array(Auth::user()->role, [
                    UserRole::SuperAdmin,
                    UserRole::Admin,
                    UserRole::Receptionist
                ]),
        ];
    }
}
