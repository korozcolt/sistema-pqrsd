{{-- resources/views/livewire/sections/contact.blade.php --}}
<div>
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            @if ($title || $description)
                <div class="text-center max-w-3xl mx-auto mb-12">
                    @if ($title)
                        <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ $title }}</h2>
                    @endif

                    @if ($description)
                        <div class="text-gray-600 mb-8">{!! $description !!}</div>
                    @endif
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Formulario -->
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <form wire:submit.prevent="submitForm">
                        @if (session()->has('success'))
                            <div class="bg-green-100 text-green-700 p-4 rounded mb-6">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                                <input type="text" wire:model="name" class="w-full rounded-lg border-gray-300">
                                @error('name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" wire:model="email" class="w-full rounded-lg border-gray-300">
                                @error('email')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                                <input type="text" wire:model="phone" class="w-full rounded-lg border-gray-300">
                                @error('phone')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Asunto</label>
                                <input type="text" wire:model="subject" class="w-full rounded-lg border-gray-300">
                                @error('subject')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Mensaje</label>
                                <textarea wire:model="message" rows="4" class="w-full rounded-lg border-gray-300"></textarea>
                                @error('message')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit"
                                class="w-full bg-primary-600 text-white py-3 px-6 rounded-lg hover:bg-primary-700">
                                Enviar Mensaje
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Información de Contacto -->
                @if ($settings['show_info'])
                    <div class="bg-white rounded-lg shadow-lg p-8">
                        <div class="mb-8">
                            <h3 class="text-2xl font-semibold mb-4">Información de Contacto</h3>
                            <div class="space-y-4">
                                @if ($address)
                                    <div class="flex items-start">
                                        <i class='bx bxs-map text-primary-600 text-2xl mr-4'></i>
                                        <p>{{ $address }}</p>
                                    </div>
                                @endif

                                @if ($phone)
                                    <div class="flex items-center">
                                        <i class='bx bxs-phone text-primary-600 text-2xl mr-4'></i>
                                        <p>{{ $phone }}</p>
                                    </div>
                                @endif

                                @if ($email)
                                    <div class="flex items-center">
                                        <i class='bx bxs-envelope text-primary-600 text-2xl mr-4'></i>
                                        <p>{{ $email }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($settings['show_map'])
                            <div class="h-64 rounded-lg overflow-hidden">
                                <iframe
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3924.6257651187584!2d-75.39171388520015!3d9.302680593316742!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e5915c1c117b6ef%3A0x1c6f3c7c7c7c7c7c!2sTorcoroma!5e0!3m2!1ses!2sco!4v1621436426789!5m2!1ses!2sco"
                                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy">
                                </iframe>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
