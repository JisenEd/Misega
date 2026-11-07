<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import MisegaLayout from '@/Layouts/MisegaLayout.vue';
import MangaCard from '@/Components/MangaCard.vue';
import Hero3DCarousel from '@/Components/Hero3DCarousel.vue';
import MangaSceneBackground from '@/Components/MangaSceneBackground.vue';
import { ArrowRight, BookOpen, Layers, Compass } from '@lucide/vue';

const props = defineProps({
    featuredMangas: { type: Array, default: () => [] },
    latestMangas: { type: Array, default: () => [] },
    genres: { type: Array, default: () => [] },
});

// 3D Parallax Lift state for Hero Text
const heroTransform = ref('perspective(1000px) rotateX(0deg) rotateY(0deg) translateZ(0px)');
const heroShadow = ref('0 10px 30px rgba(0,0,0,0.05)');

const handleHeroMouseMove = (e) => {
    const card = e.currentTarget;
    const rect = card.getBoundingClientRect();
    const x = e.clientX - rect.left - rect.width / 2;
    const y = e.clientY - rect.top - rect.height / 2;
    
    const rotateX = (-y / (rect.height / 2)) * 8;
    const rotateY = (x / (rect.width / 2)) * 8;
    const liftZ = 35;

    heroTransform.value = `perspective(1000px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg) translateZ(${liftZ}px) translateY(-6px)`;
    heroShadow.value = `0 25px 50px -12px rgba(188, 0, 45, 0.25)`;
};

const handleHeroMouseLeave = () => {
    heroTransform.value = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateZ(0px) translateY(0px)';
    heroShadow.value = '0 10px 30px rgba(0,0,0,0.05)';
};
</script>

<template>
    <Head title="Accueil — MISEGA | Votre Boutique Manga Premium" />

    <MisegaLayout>
        <!-- ─── Hero Section with 3D Parallax & Calligraphy Animation ─── -->
        <div class="relative bg-gradient-to-b from-gray-100 via-white to-gray-50 border-b border-gray-200 pt-24 pb-12 sm:pt-28 sm:pb-16 lg:pt-32 overflow-hidden">
            <!-- 3D Floating Manga Pages Background Scene -->
            <MangaSceneBackground />

            <!-- Ambient glow accents -->
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

                        <div class="pt-2 flex flex-wrap items-center gap-4">
                            <Link href="/catalogue" class="bg-primary hover:bg-primary-hover text-white text-xs font-bold px-6 py-3.5 rounded-xl shadow-lg shadow-primary/25 transition hover:scale-105 flex items-center gap-2">
                                Explorer la collection
                                <ArrowRight class="w-4 h-4" />
                            </Link>
                            <span class="text-xs font-semibold text-gray-400 flex items-center gap-1.5">
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

        <!-- ─── Featured Mangas Section ─── -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <div class="text-xs font-bold text-primary uppercase tracking-wider mb-1">
                        Sélection Populaire
                    </div>
                    <h2 class="font-gang text-2xl sm:text-3xl text-gray-900">Mangas à la Une</h2>
                </div>
                <Link href="/catalogue" class="text-xs font-bold text-primary hover:text-primary-hover flex items-center gap-1 transition">
                    Voir tout <ArrowRight class="w-3.5 h-3.5" />
                </Link>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <MangaCard 
                    v-for="manga in featuredMangas.slice(0, 4)" 
                    :key="manga.id" 
                    :manga="manga" 
                />
            </div>
        </div>

        <!-- ─── Genre Discovery Cards ─── -->
        <div class="bg-gray-50 border-y border-gray-200 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-xl mx-auto mb-10">
                    <div class="flex items-center justify-center gap-2 text-xs font-bold text-primary uppercase tracking-wider mb-1">
                        <Compass class="w-4 h-4" /> Explorer par Genre
                    </div>
                    <h2 class="font-gang text-2xl sm:text-3xl text-gray-900">Trouvez votre style</h2>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                    <Link
                        v-for="g in ['Shonen', 'Seinen', 'Shojo', 'Dark Fantasy', 'Action']"
                        :key="g"
                        :href="`/catalogue?genre=${g}`"
                        class="group bg-white p-5 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md hover:border-primary/40 transition-all text-center flex flex-col items-center gap-2 cursor-pointer"
                    >
                        <span class="font-gang text-lg text-gray-900 group-hover:text-primary transition-colors">{{ g }}</span>
                        <span class="text-[11px] font-semibold text-gray-400 group-hover:text-gray-600">Découvrir →</span>
                    </Link>
                </div>
            </div>
        </div>

        <!-- ─── Bottom CTA Banner ─── -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="bg-gradient-to-br from-white via-gray-50 to-rose-50/40 rounded-3xl p-8 sm:p-12 border border-gray-200/80 text-gray-900 flex flex-col md:flex-row items-center justify-between gap-8 relative overflow-hidden shadow-xl shadow-gray-200/50">
                <div class="space-y-3 max-w-xl z-10">
                    <span class="inline-block text-xs font-extrabold uppercase tracking-widest text-primary bg-primary/10 px-3 py-1 rounded-full border border-primary/20">
                        Catalogue Complet
                    </span>
                    <h3 class="font-gang text-3xl sm:text-4xl text-gray-900">Plus de 500 tomes prêts à expédier</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Recherchez par titre, auteur ou genre et complétez votre collection dès aujourd'hui.
                    </p>
                </div>
                <Link
                    href="/catalogue"
                    class="z-10 bg-primary hover:bg-primary-hover text-white text-sm font-bold px-8 py-4 rounded-xl shadow-lg shadow-primary/40 transition hover:scale-105 shrink-0 flex items-center gap-2"
                >
                    Accéder au Catalogue <ArrowRight class="w-4 h-4" />
                </Link>
            </div>
        </div>
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
