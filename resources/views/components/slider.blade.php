<div
    x-data="{
        activeSlide: 0,
        slides: {{ json_encode($slides) }},
        interval: null,
        autoplay: true,
        start() {
            this.interval = setInterval(() => {
                if (this.autoplay) {
                    this.activeSlide = (this.activeSlide + 1) % this.slides.length;
                }
            }, 4000);
        },
        pauseAutoplay() {
            this.autoplay = false;
            setTimeout(() => {
                this.autoplay = true;
            }, 10000);
        }
    }"
    x-init="start()"
    class="relative w-full overflow-hidden rounded-lg shadow-xl"
>
    <!-- Slides -->
    <div
        class="flex transition-transform duration-700 ease-in-out"
        :style="`transform: translateX(-${activeSlide * 100}%)`"
    >
        <template x-for="(slide, index) in slides" :key="index">
            <div
                class="min-w-full h-64 sm:h-80 md:h-96 lg:h-[28rem] bg-cover bg-center relative"
                :style="`background-image: url('${slide.image}')`"
            >
                <div class="h-full w-full flex items-center justify-center">
                    <h2
                        x-text="slide.text"
                        class="px-8 py-4 text-center text-white text-2xl sm:text-3xl md:text-4xl font-bold font-serif tracking-wide leading-relaxed shadow-text animate-fadeIn"
                        style="text-shadow: 2px 2px 4px rgba(0,0,0,0.8);"
                    ></h2>
                </div>
            </div>
        </template>
    </div>

    <!-- Controles prev/next -->
    <button 
        @click="pauseAutoplay(); activeSlide = (activeSlide - 1 + slides.length) % slides.length"
        class="absolute top-1/2 left-4 -translate-y-1/2 bg-white/70 hover:bg-white text-[#FF0596] hover:text-[#FF0596] p-3 rounded-full shadow-lg transition-all duration-300 z-20 focus:outline-none focus:ring-2 focus:ring-[#FF0596]"
    >
        <span class="text-2xl font-bold">‹</span>
    </button>

    <button 
        @click="pauseAutoplay(); activeSlide = (activeSlide + 1) % slides.length"
        class="absolute top-1/2 right-4 -translate-y-1/2 bg-white/70 hover:bg-white text-[#FF0596] hover:text-[#FF0596] p-3 rounded-full shadow-lg transition-all duration-300 z-20 focus:outline-none focus:ring-2 focus:ring-[#FF0596]"
    >
        <span class="text-2xl font-bold">›</span>
    </button>

    <!-- Indicadores -->
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex space-x-3 z-20">
        <template x-for="(slide, index) in slides" :key="index">
            <button
                class="w-4 h-4 rounded-full transition-all duration-300 border-2 border-white/70"
                :class="index === activeSlide ? 'bg-[#FF0596] scale-125' : 'bg-white/50 hover:bg-white/80'"
                @click="pauseAutoplay(); activeSlide = index"
            ></button>
        </template>
    </div>
</div>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
    animation: fadeIn 0.5s ease-in-out;
}
</style>
