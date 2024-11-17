<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Ticket;
use App\Models\User;
use App\Enums\TicketType;
use App\Enums\Priority;
use App\Enums\StatusTicket;
use Illuminate\Support\Str;

class TicketSystem extends Component
{
    use WithFileUploads;

    public $activeTab = 'create';
    public $name;
    public $email;
    public $title;
    public $type;
    public $description;
    public $attachments = [];
    public $ticketNumber;
    public $searchResults;
    public $newComment;
    public $selectedTicket;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'title' => 'required|string|max:255',
        'type' => 'required|string',
        'description' => 'required|string',
        'attachments.*' => 'nullable|file|max:10240',
    ];

    public function mount()
    {
        $this->type = TicketType::Petition->value;
    }

    public function createTicket()
    {
        $this->validate();

        // Crear o recuperar usuario web
        $user = User::firstOrCreate(
            ['email' => $this->email],
            [
                'name' => $this->name,
                'password' => bcrypt(Str::random(16)),
                'role' => 'user_web'
            ]
        );

        $ticket = Ticket::create([
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'user_id' => $user->id,
            'status' => StatusTicket::Pending,
            'priority' => Priority::Medium,
        ]);

        if (!empty($this->attachments)) {
            foreach ($this->attachments as $attachment) {
                $path = $attachment->store('ticket-attachments');
                $ticket->attachments()->create([
                    'file_name' => $attachment->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $attachment->getMimeType(),
                    'file_size' => $attachment->getSize(),
                    'uploaded_by' => $user->id,
                ]);
            }
        }

        $this->reset(['name', 'email', 'title', 'description', 'attachments']);
        $this->dispatch('ticket-created', $ticket->ticket_number);
    }

    public function searchTicket()
    {
        $this->validate([
            'ticketNumber' => 'required|string'
        ]);

        $this->selectedTicket = Ticket::with(['comments.user', 'attachments', 'user'])
            ->where('ticket_number', $this->ticketNumber)
            ->first();
    }

    public function addComment()
    {
        if (!$this->selectedTicket) return;

        $this->validate([
            'newComment' => 'required|string'
        ]);

        $this->selectedTicket->comments()->create([
            'content' => $this->newComment,
            'user_id' => $this->selectedTicket->user_id,
            'is_internal' => false,
        ]);

        $this->newComment = '';
        $this->searchTicket(); // Refrescar ticket
    }

    public function changeTab($tab)
    {
        $this->activeTab = $tab;
        $this->reset(['selectedTicket', 'ticketNumber', 'newComment']);
    }

    public function render()
    {
        return view('livewire.ticket-system');
    }
}
