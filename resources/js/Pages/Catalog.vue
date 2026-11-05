<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import MisegaLayout from '@/Layouts/MisegaLayout.vue';
import MangaCard from '@/Components/MangaCard.vue';
import Hero3DCarousel from '@/Components/Hero3DCarousel.vue';
import MangaSceneBackground from '@/Components/MangaSceneBackground.vue';
import { Search, X, BookOpen, Layers } from '@lucide/vue';

const props = defineProps({
    mangas: { type: Array, default: () => [] },
    genres: { type: Array, default: () => [] },
    authors: { type: Array, default: () => [] },
    featuredMangas: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({ search: '', genre: '', author: '' }) }
});

const selectedGenre = ref(props.filters.genre || '');
const selectedAuthor = ref(props.filters.author || '');
const searchInput = ref(props.filters.search || '');

// 3D Parallax Lift state for Hero Text
const heroTransform = ref('perspective(1000px) rotateX(0deg) rotateY(0deg) translateZ(0px)');
const heroShadow = ref('0 10px 30px rgba(0,0,0,0.05)');

const handleHeroMouseMove = (e) => {
    const card = e.currentTarget;
    const rect = card.getBoundingClientRect();
    const x = e.clientX - rect.left - rect.width / 2;
    const y = e.clientY - rect.top - rect.height / 2;
    
    const rotateX = (-y / (rect.height / 2)) * 8; // Max 8 deg X rotation
    const rotateY = (x / (rect.width / 2)) * 8;   // Max 8 deg Y rotation
    const liftZ = 35; // Elevates by 35px in 3D

    heroTransform.value = `perspective(1000px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg) translateZ(${liftZ}px) translateY(-6px)`;
    heroShadow.value = `0 25px 50px -12px rgba(188, 0, 45, 0.25)`;
};

const handleHeroMouseLeave = () => {
    heroTransform.value = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateZ(0px) translateY(0px)';
    heroShadow.value = '0 10px 30px rgba(0,0,0,0.05)';
};

const applyFilters = () => {
    router.get('/catalog', {
        search: searchInput.value || undefined,
        genre: selectedGenre.value || undefined,
        author: selectedAuthor.value || undefined,
    }, { preserveState: true, replace: true });
};

const filterByGenre = (genre) => {
    selectedGenre.value = genre === selectedGenre.value ? '' : genre;
    applyFilters();
};

const resetFilters = () => {
    selectedGenre.value = '';
    selectedAuthor.value = '';
    searchInput.value = '';
    applyFilters();
};
</script>

<template>
    <Head title="Catalogue de Mangas - MISEGA" />

    <MisegaLayout>
        <!-- Hero Section with 3D Parallax & Calligraphy Animation -->
        <div class="relative bg-gradient-to-b from-gray-100 via-white to-gray-50 border-b border-gray-200 py-12 sm:py-16 overflow-hidden">
            <!-- 3D Floating Manga Pages Background Scene -->
            <MangaSceneBackground />

            <!-- Ambient glow accents (above 3D scene) -->
            <div class="absolute -top-20 right-1/4 w-96 h-96 bg-primary/8 blur-3xl rounded-full pointer-events-none z-0"></div>
            <div class="absolute top-1/2 left-5 -translate-y-1/2 text-gray-200/30 font-gang text-9xl select-none pointer-events-none z-0">
                漫画
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
                    
                    <!-- 3D Interactive Text Container -->
                    <div 
                        @mousemove="handleHeroMouseMove"
                        @mouseleave="handleHeroMouseLeave"
                        :style="{ transform: heroTransform, boxShadow: heroShadow, transition: 'transform 0.15s ease-out, box-shadow 0.3s ease' }"
                        class="md:col-span-7 bg-white/80 backdrop-blur-md p-6 sm:p-8 rounded-3xl border border-gray-200/80 space-y-5 cursor-pointer transform-gpu"
                    >
                        <div class="inline-flex items-center px-3.5 py-1.5 rounded-full bg-primary/10 border border-primary/20 text-primary text-xs font-extrabold uppercase tracking-wider">
                            Collection Officielle MISEGA 2026
                        </div>

                        <!-- Animated Calligraphy Title SVG + Text -->
                        <div class="relative py-2">
                            <!-- Animated SVG Stroke Calligraphy Line -->
                            <svg class="w-full h-12 mb-2 overflow-visible" viewBox="0 0 500 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path 
                                    d="M 10,25 C 100,5 200,45 300,20 C 380,0 450,30 490,25" 
                                    stroke="#BC002D" 
                                    stroke-width="4" 
                                    stroke-linecap="round" 
                                    class="animate-path-draw"
                                />
                            </svg>

                            <h1 class="font-gang text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 tracking-wide leading-tight">
                                <span class="block animate-fade-in-left">VIVEZ VOTRE PASSION</span>
                                <span class="text-primary block mt-1 drop-shadow-sm animate-fade-in-left-delay">MANGA SANS LIMITE</span>
                            </h1>
                        </div>

                        <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                            Découvrez les meilleurs tomes de vos séries favorites. Mangas récents, éditions collector et classiques incontournables disponibles immédiatement avec livraison rapide.
                        </p>

                        <div class="pt-2 flex flex-wrap items-center gap-3">
                            <a href="#catalog-grid" class="bg-primary hover:bg-primary-hover text-white text-xs font-bold px-6 py-3 rounded-xl shadow-lg shadow-primary/25 transition hover:scale-105">
                                Explorer la collection
                            </a>
                            <span class="text-xs font-semibold text-gray-400 flex items-center gap-1">
                                <Layers class="w-4 h-4 text-primary" /> +500 volumes en stock
                            </span>
                        </div>
                    </div>

                    <!-- Hero 3D Revolving Perspective Carousel -->
                    <div class="md:col-span-5 flex items-center justify-center">
                        <Hero3DCarousel :mangas="featuredMangas" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Catalog Container -->
        <!-- Catalog Container with 3D background -->
        <div class="relative overflow-hidden">
            <MangaSceneBackground />
        <div id="catalog-grid" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative z-10">
            <!-- Filter Bar & Search -->
            <div class="space-y-6 mb-10">
                <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-gray-200 shadow-sm">
                    <!-- Search Input -->
                    <div class="relative flex-1">
                        <input 
                            v-model="searchInput"
                            @keyup.enter="applyFilters"
                            type="text" 
                            placeholder="Rechercher par titre, auteur ou éditeur..." 
                            class="w-full bg-gray-50 text-sm text-gray-800 placeholder-gray-400 rounded-xl py-2.5 pl-10 pr-10 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition"
                        />
                        <Search class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" />
                        <button 
                            v-if="searchInput" 
                            @click="searchInput = ''; applyFilters()"
                            class="absolute right-3 top-3 text-gray-400 hover:text-gray-700"
                        >
                            <X class="w-4 h-4" />
                        </button>
                    </div>

                    <!-- Author Dropdown Filter -->
                    <div class="flex items-center gap-3">
                        <select 
                            v-model="selectedAuthor"
                            @change="applyFilters"
                            class="bg-gray-50 text-sm text-gray-700 rounded-xl px-4 py-2.5 border border-gray-200 focus:border-primary focus:outline-none cursor-pointer"
                        >
                            <option value="">Tous les auteurs</option>
                            <option v-for="author in authors" :key="author" :value="author">
                                {{ author }}
                            </option>
                        </select>

                        <button 
                            v-if="selectedGenre || selectedAuthor || searchInput"
                            @click="resetFilters"
                            class="text-xs font-semibold text-primary hover:text-primary-hover flex items-center gap-1 px-3 py-2 bg-primary/10 rounded-xl border border-primary/20 transition"
                        >
                            <X class="w-3.5 h-3.5" />
                            Réinitialiser
                        </button>
                    </div>
                </div>

                <!-- Genre Quick Filter Pills -->
                <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                    <button 
                        @click="filterByGenre('')"
                        class="px-4 py-2 rounded-xl text-xs font-bold tracking-wide uppercase transition border shrink-0"
                        :class="!selectedGenre ? 'bg-primary text-white border-primary shadow-md shadow-primary/30' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900'"
                    >
                        Tous les genres
                    </button>
                    <button 
                        v-for="genre in genres" 
                        :key="genre"
                        @click="filterByGenre(genre)"
                        class="px-4 py-2 rounded-xl text-xs font-bold tracking-wide uppercase transition border shrink-0"
                        :class="selectedGenre === genre ? 'bg-primary text-white border-primary shadow-md shadow-primary/30' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900'"
                    >
                        {{ genre }}
                    </button>
                </div>
            </div>

            <!-- Manga Grid -->
            <div v-if="mangas.length > 0">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="font-gang text-2xl text-gray-900 tracking-wide flex items-center gap-2">
                        <BookOpen class="w-5 h-5 text-primary" />
                        Manga disponibles ({{ mangas.length }})
                    </h2>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 sm:gap-6">
                    <MangaCard 
                        v-for="manga in mangas" 
                        :key="manga.id" 
                        :manga="manga" 
                    />
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-20 bg-white rounded-2xl border border-gray-200 p-8 space-y-4 shadow-sm">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto text-primary">
                    <Search class="w-8 h-8" />
                </div>
                <h3 class="font-gang text-xl text-gray-900">Aucun manga trouvé</h3>
                <p class="text-gray-500 text-sm max-w-md mx-auto">
                    Nous n'avons trouvé aucun résultat correspondant à vos critères de recherche. Essayer de modifier vos filtres.
                </p>
                <button 
                    @click="resetFilters" 
                    class="bg-primary hover:bg-primary-hover text-white text-xs font-bold px-6 py-2.5 rounded-xl transition shadow-md shadow-primary/20"
                >
                    Voir tous les mangas
                </button>
            </div>
        </div>
        </div><!-- end catalog-grid wrapper -->
    </MisegaLayout>
</template>

<style scoped>
@keyframes pathDraw {
    0% { stroke-dasharray: 1000; stroke-dashoffset: 1000; opacity: 0; }
    20% { opacity: 1; }
    100% { stroke-dasharray: 1000; stroke-dashoffset: 0; opacity: 1; }
}

.animate-path-draw {
    animation: pathDraw 2s cubic-bezier(0.25, 1, 0.5, 1) forwards;
}

@keyframes fadeInLeft {
    from { opacity: 0; transform: translateX(-20px); }
    to { opacity: 1; transform: translateX(0); }
}

.animate-fade-in-left {
    animation: fadeInLeft 0.8s ease-out forwards;
}

.animate-fade-in-left-delay {
    animation: fadeInLeft 0.8s ease-out 0.3s forwards;
    opacity: 0;
    animation-fill-mode: forwards;
}
</style>
