<div>
    {{-- resources/views/livewire/sections/testimonials.blade.php --}}
    <section class="py-16 {{ $settings['background'] === 'gray' ? 'bg-gray-50' : 'bg-white' }}">
        <div class="container mx-auto px-4">
            @if ($title || $description)
                <div class="text-center mb-12">
                    @if ($title)
                        <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ $title }}</h2>
                    @endif
                    @if ($description)
                        <p class="text-xl text-gray-600">{{ $description }}</p>
                    @endif
                </div>
            @endif

            <div class="relative" x-data="{
                activeSlide: 0,
                slides: {{ count($testimonials) }},
                autoplay: {{ $settings['autoplay'] ? 'true' : 'false' }},
                interval: {{ $settings['interval'] }},
                init() {
                    if (this.autoplay) {
                        setInterval(() => this.nextSlide(), this.interval)
                    }
                },
                nextSlide() {
                    this.activeSlide = (this.activeSlide + 1) % this.slides
                },
                prevSlide() {
                    this.activeSlide = (this.activeSlide - 1 + this.slides) % this.slides
                }
            }">

                <div class="overflow-hidden">
                    <div class="flex transition-transform duration-500"
                        :style="{ transform: `translateX(-${activeSlide * 100}%)` }">
                        @foreach ($testimonials as $testimonial)
                            <div class="w-full flex-shrink-0 px-4">
                                <div class="{{ $testimonialClasses }}">
                                    <div class="mb-6">
                                        <i class="bx bxs-quote-left text-4xl text-primary-600"></i>
                                    </div>

                                    <blockquote class="text-lg text-gray-600 mb-6">
                                        {{ $testimonial['content'] }}
                                    </blockquote>

                                    <div class="flex items-center">
                                        @if ($settings['show_images'] && isset($testimonial['image']))
                                            <img src="{{ Storage::url($testimonial['image']) }}"
                                                alt="{{ $testimonial['name'] }}"
                                                class="w-12 h-12 rounded-full object-cover mr-4">
                                        @endif
                                        <div>
                                            <div class="font-semibold text-gray-900">
                                                {{ $testimonial['name'] }}
                                            </div>
                                            @if (isset($testimonial['position']))
                                                <div class="text-gray-500 text-sm">
                                                    {{ $testimonial['position'] }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Navigation Buttons --}}
                <button @click="prevSlide"
                    class="absolute left-0 top-1/2 -translate-y-1/2 bg-white rounded-full p-2 shadow-lg hover:bg-gray-100">
                    <i class="bx bx-chevron-left text-2xl"></i>
                </button>

                <button @click="nextSlide"
                    class="absolute right-0 top-1/2 -translate-y-1/2 bg-white rounded-full p-2 shadow-lg hover:bg-gray-100">
                    <i class="bx bx-chevron-right text-2xl"></i>
                </button>

                {{-- Indicators --}}
                <div class="flex justify-center mt-8 space-x-2">
                    @foreach ($testimonials as $index => $testimonial)
                        <button @click="activeSlide = {{ $index }}"
                            :class="{ 'bg-primary-600': activeSlide === {{ $index }}, 'bg-gray-300': activeSlide !==
                                    {{ $index }} }"
                            class="w-3 h-3 rounded-full transition-colors duration-300"></button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</div>
