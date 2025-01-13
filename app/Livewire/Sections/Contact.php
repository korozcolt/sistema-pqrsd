<?php

namespace App\Livewire\Sections;

use Livewire\Component;
use App\Models\Section;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactForm;

class Contact extends Component
{
    public Section $section;
    public array $content;
    public array $settings;

    // Form fields
    public $name = '';
    public $email = '';
    public $phone = '';
    public $message = '';
    public $subject = '';

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'phone' => 'required',
        'subject' => 'required',
        'message' => 'required|min:10'
    ];

    public function mount(Section $section)
    {
        $this->section = $section;
        $this->content = $section->content ?? [];
        $this->settings = $section->settings ?? [
            'show_map' => true,
            'show_info' => true
        ];
    }

    public function submitForm()
    {
        $this->validate();

        // Enviar email
        Mail::to(config('mail.from.address'))
            ->send(new ContactForm([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'subject' => $this->subject,
                'message' => $this->message
            ]));

        // Limpiar formulario
        $this->reset(['name', 'email', 'phone', 'subject', 'message']);

        // Notificar éxito
        session()->flash('success', 'Mensaje enviado correctamente');
    }

    public function render()
    {
        return view('livewire.sections.contact', [
            'title' => $this->content['title'] ?? '',
            'description' => $this->content['description'] ?? '',
            'address' => config('site.company.contact.address'),
            'phone' => config('site.company.contact.phones.main'),
            'email' => config('site.company.contact.emails.main')
        ]);
    }
}
