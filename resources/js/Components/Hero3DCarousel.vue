<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, Eye, BookOpen, Sparkles } from '@lucide/vue';

const props = defineProps({
    mangas: {
        type: Array,
        default: () => []
    }
});

const imageErrors = ref({});
const getMangaCover = (manga) => manga?.cover_url || manga?.cover_image || null;
const handleImgError = (id) => { imageErrors.value[id] = true; };

// Default active index (middle item: index 1)
const activeIndex = ref(1);

// Active card mouse tilt state
const cardTilts = ref({});

const carouselMangas = computed(() => {
    if (props.mangas.length >= 3) {
        return props.mangas.slice(0, 3);
    }
    return props.mangas;
});

const nextSlide = () => {
    if (carouselMangas.value.length === 0) return;
    activeIndex.value = (activeIndex.value + 1) % carouselMangas.value.length;
};

const prevSlide = () => {
    if (carouselMangas.value.length === 0) return;
    activeIndex.value = (activeIndex.value - 1 + carouselMangas.value.length) % carouselMangas.value.length;
};

const selectCard = (index) => {
    activeIndex.value = index;
};

// Compute 3D position & transformation matrix relative to activeIndex
const getCardStyle = (index) => {
    const total = carouselMangas.value.length;
    if (total === 0) return {};

    // Relative position offset: -1 (left), 0 (center), 1 (right)
    let diff = index - activeIndex.value;
    if (diff < -1) diff += total;
    if (diff > 1) diff -= total;

    const tilt = cardTilts.value[index] || { x: 0, y: 0 };

    if (diff === 0) {
        // Active / Center Card - Brought forward, facing user directly
        return {
            transform: `perspective(1200px) rotateY(${tilt.y}deg) rotateX(${tilt.x}deg) translateZ(90px) scale(1.06)`,
            opacity: 1,
            filter: 'blur(0px)',
            zIndex: 30,
            boxShadow: '0 30px 60px -15px rgba(188, 0, 45, 0.35), 0 0 25px rgba(188, 0, 45, 0.2)',
            transition: tilt.x === 0 && tilt.y === 0 ? 'all 0.5s cubic-bezier(0.25, 1, 0.5, 1)' : 'transform 0.1s ease-out',
        };
    } else if (diff === -1 || (diff === 2 && total === 3)) {
        // Left Card - Angled away to the left, pushed back and slightly blurred
        return {
            transform: 'perspective(1200px) rotateY(35deg) translateX(-110px) translateZ(-80px) scale(0.85)',
            opacity: 0.7,
            filter: 'blur(2px)',
            zIndex: 10,
            boxShadow: '0 15px 30px rgba(0,0,0,0.15)',
            transition: 'all 0.5s cubic-bezier(0.25, 1, 0.5, 1)',
        };
    } else {
        // Right Card - Angled away to the right, pushed back and slightly blurred
        return {
            transform: 'perspective(1200px) rotateY(-35deg) translateX(110px) translateZ(-80px) scale(0.85)',
            opacity: 0.7,
            filter: 'blur(2px)',
            zIndex: 10,
            boxShadow: '0 15px 30px rgba(0,0,0,0.15)',
            transition: 'all 0.5s cubic-bezier(0.25, 1, 0.5, 1)',
        };
    }
};

// 3D Parallax tilt tracking on individual card hover
const handleMouseMove = (e, index) => {
    if (index !== activeIndex.value) return; // Only apply interactive tilt to active card
    const card = e.currentTarget;
    const rect = card.getBoundingClientRect();
    const x = e.clientX - rect.left - rect.width / 2;
    const y = e.clientY - rect.top - rect.height / 2;

    const rotateX = (-y / (rect.height / 2)) * 14;
    const rotateY = (x / (rect.width / 2)) * 14;

    cardTilts.value[index] = { x: rotateX, y: rotateY };
};

const handleMouseLeave = (index) => {
    cardTilts.value[index] = { x: 0, y: 0 };
};
</script>

<template>
    <div class="relative w-full max-w-lg mx-auto py-8 px-4 flex flex-col items-center">
        <!-- 3D Perspective Stage Container -->
        <div class="relative w-full h-[360px] sm:h-[400px] flex items-center justify-center perspective-stage">
            
            <!-- Cards 3D Object Group -->
            <div 
                v-for="(manga, index) in carouselMangas" 
                :key="manga.id"
                @click="selectCard(index)"
                @mousemove="e => handleMouseMove(e, index)"
                @mouseleave="handleMouseLeave(index)"
                :style="getCardStyle(index)"
                class="absolute w-[210px] sm:w-[240px] aspect-[3/4] rounded-2xl overflow-hidden cursor-pointer select-none bg-white border-2 border-white/80 transform-gpu group"
            >
                <!-- Inner Multi-Layered 3D Depth Card -->
                <div class="relative w-full h-full transform-style-3d">
                    <!-- Cover Book Layer -->
                    <div class="w-full h-full bg-gradient-to-br from-[#1c1c24] via-[#121217] to-[#0a0a0e] flex flex-col items-center justify-between p-5 text-center select-none rounded-2xl relative">
                        <div class="w-2.5 absolute inset-y-0 left-0 bg-gradient-to-r from-black/60 to-transparent"></div>
                        <span class="text-[9px] font-extrabold uppercase tracking-widest text-primary">Édition Prestige</span>
                        <div class="my-auto px-2">
                            <div class="font-gang text-white text-lg sm:text-xl font-bold line-clamp-2 leading-tight mb-2">
                                {{ manga.title }}
                            </div>
                            <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider block">
                                {{ manga.author }}
                            </span>
                        </div>
                        <span class="text-[10px] font-bold text-white uppercase tracking-widest bg-white/10 px-3 py-1 rounded-full border border-white/20">
                            {{ manga.genre }}
                        </span>
                    </div>

                    <!-- Dark Vignette Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-950/90 via-gray-950/20 to-transparent"></div>

                    <!-- Genre Badge Floating in 3D Layer -->
                    <div class="absolute top-3 left-3 bg-primary text-white text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-md shadow-md transform-3d-front">
                        {{ manga.genre }}
                    </div>

                    <!-- Floating Content Info Layer (pushed forward in 3D: translateZ 25px) -->
                    <div class="absolute inset-x-0 bottom-0 p-4 flex flex-col justify-end text-white transform-3d-front space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-gray-300">Par {{ manga.author }}</span>
                            <span class="text-[10px] bg-white/20 backdrop-blur-md px-2 py-0.5 rounded font-bold">
                                {{ manga.volumes ? manga.volumes.length : 0 }} Tomes
                            </span>
                        </div>

                        <h3 class="font-gang text-xl font-extrabold tracking-wide text-white drop-shadow-md line-clamp-1">
                            {{ manga.title }}
                        </h3>

                        <!-- Action Button on Active Card -->
                        <div class="pt-1 flex items-center gap-2">
                            <Link 
                                :href="`/manga/${manga.id}`"
                                class="w-full bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2 px-3 rounded-xl shadow-lg shadow-primary/40 flex items-center justify-center gap-1.5 transition"
                            >
                                <Eye class="w-3.5 h-3.5" />
                                Découvrir en 3D
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3D Navigation Controls & Indicator Dots -->
        <div class="flex items-center gap-4 mt-6 z-40">
            <button 
                @click="prevSlide"
                class="p-2.5 rounded-full bg-white hover:bg-gray-100 border border-gray-200 shadow-md text-gray-700 hover:text-primary transition hover:scale-110 active:scale-95"
                title="Manga Précédent"
            >
                <ChevronLeft class="w-5 h-5" />
            </button>

            <!-- Dots -->
            <div class="flex items-center gap-2">
                <button 
                    v-for="(manga, idx) in carouselMangas" 
                    :key="manga.id"
                    @click="selectCard(idx)"
                    class="h-2.5 rounded-full transition-all duration-300"
                    :class="idx === activeIndex ? 'w-8 bg-primary shadow-sm' : 'w-2.5 bg-gray-300 hover:bg-gray-400'"
                ></button>
            </div>

            <button 
                @click="nextSlide"
                class="p-2.5 rounded-full bg-white hover:bg-gray-100 border border-gray-200 shadow-md text-gray-700 hover:text-primary transition hover:scale-110 active:scale-95"
                title="Manga Suivant"
            >
                <ChevronRight class="w-5 h-5" />
            </button>
        </div>
    </div>
</template>

<style scoped>
.perspective-stage {
    perspective: 1200px;
    transform-style: preserve-3d;
}

.transform-style-3d {
    transform-style: preserve-3d;
}

.transform-3d-front {
    transform: translateZ(25px);
}
</style>
