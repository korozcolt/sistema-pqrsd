<div>
    <div class="ticket-system p-2">
        <div class="tab-navigation mb-6">
            <div class="flex space-x-4">
                <button wire:click="changeTab('create')"
                    class="px-4 py-2 rounded-md font-medium {{ $activeTab === 'create' ? 'bg-blue-600 text-white' : 'bg-white hover:bg-gray-50' }}">
                    Crear Ticket
                </button>
                <button wire:click="changeTab('search')"
                    class="px-4 py-2 rounded-md font-medium {{ $activeTab === 'search' ? 'bg-blue-600 text-white' : 'bg-white hover:bg-gray-50' }}">
                    Buscar Ticket
                </button>
            </div>
        </div>

        <div class="tab-content">
            @if ($activeTab === 'create')
                <div class="bg-white rounded-lg p-6">
                    <form wire:submit="createTicket">
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                                    <input type="text" wire:model="name"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @error('name')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" wire:model="email"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @error('email')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Asunto</label>
                                <input type="text" wire:model="title"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @error('title')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipo</label>
                                <select wire:model="type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @foreach (App\Enums\TicketType::cases() as $ticketType)
                                        <option value="{{ $ticketType->value }}">{{ $ticketType->getLabel() }}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Descripción</label>
                                <textarea wire:model="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                                @error('description')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Archivos Adjuntos</label>
                                <input type="file" wire:model="attachments" multiple class="mt-1 block w-full">
                                <p class="text-sm text-gray-500 mt-1">Máximo 5 archivos, 10MB por archivo</p>
                                @error('attachments.*')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="submit"
                                class="w-full bg-primary-600 text-white py-2 px-4 rounded-md hover:bg-primary-700">
                                Crear Ticket
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="bg-white rounded-lg p-6">
                    <form wire:submit="searchTicket" class="mb-6">
                        <div class="flex space-x-4">
                            <div class="flex-1">
                                <input type="text" wire:model="ticketNumber" placeholder="Ingrese número de ticket"
                                    class="w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700">
                                Buscar
                            </button>
                        </div>
                    </form>

                    @if ($selectedTicket)
                        <div class="mt-6 space-y-6">
                            <div class="border-b pb-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-medium">Ticket #{{ $selectedTicket->ticket_number }}
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            {{ $selectedTicket->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <span
                                        class="px-3 py-1 rounded-full text-sm font-medium {{ $selectedTicket->status->getColorHtml() }}">
                                        {{ $selectedTicket->status->getLabel() }}
                                    </span>
                                </div>
                                <div class="mt-4 prose max-w-none">
                                    <h4 class="text-md font-medium">{{ $selectedTicket->title }}</h4>
                                    <p>{{ $selectedTicket->description }}</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <h4 class="font-medium">Comentarios</h4>
                                @foreach ($selectedTicket->comments as $comment)
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <div class="flex justify-between items-start">
                                            <span class="font-medium">{{ $comment->user->name }}</span>
                                            <span
                                                class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="mt-2">{{ $comment->content }}</p>
                                    </div>
                                @endforeach

                                <form wire:submit="addComment" class="mt-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Agregar
                                            comentario</label>
                                        <textarea wire:model="newComment" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                                    </div>
                                    <button type="submit"
                                        class="mt-2 px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
                                        Enviar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
