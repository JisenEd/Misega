<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useCartStore } from '@/stores/useCartStore';
import { ShoppingCart, Eye, BookOpen, Star } from '@lucide/vue';
import { formatPrice } from '@/utils/formatPrice';
import { requireAuthAndVerified } from '@/utils/authGuard';

const props = defineProps({
    manga: { type: Object, required: true }
});

const cartStore = useCartStore();
const cardRef = ref(null);
const isHovered = ref(false);
const hasImageError = ref(false);

const imageSource = computed(() => props.manga.cover_url || props.manga.cover_image || null);

// --- Tracking state ---
const rotX = ref(0);
const rotY = ref(0);
const glowX = ref(50); // cursor % within card
const glowY = ref(50);
const imgTransX = ref(0);
const imgTransY = ref(0);

const handleMouseMove = (e) => {
    if (!cardRef.value) return;
    const rect = cardRef.value.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;

    // Normalized -1 → 1
    const nx = (x / rect.width) * 2 - 1;
    const ny = (y / rect.height) * 2 - 1;

    // Card tilt: max ±12 deg
    rotX.value = -ny * 12;
    rotY.value = nx * 12;

    // Foreground image parallax: image moves MORE than card tilt (deeper effect)
    imgTransX.value = nx * 18;
    imgTransY.value = ny * 18;

    // Glow follows cursor (as %)
    glowX.value = (x / rect.width) * 100;
    glowY.value = (y / rect.height) * 100;
};

const handleMouseEnter = () => { isHovered.value = true; };

const handleMouseLeave = () => {
    isHovered.value = false;
    rotX.value = 0;
    rotY.value = 0;
    imgTransX.value = 0;
    imgTransY.value = 0;
};

// Computed style for the outer card 3D tilt
const cardStyle = computed(() => ({
    transform: isHovered.value
        ? `perspective(900px) rotateX(${rotX.value.toFixed(2)}deg) rotateY(${rotY.value.toFixed(2)}deg) scale3d(1.04, 1.04, 1.04)`
        : 'perspective(900px) rotateX(0deg) rotateY(0deg) scale3d(1,1,1)',
    transition: isHovered.value
        ? 'transform 0.12s ease-out'
        : 'transform 0.5s cubic-bezier(0.25,1,0.5,1)',
}));

// Parallax: cover image moves faster than card (foreground)
const imgStyle = computed(() => ({
    transform: isHovered.value
        ? `scale(1.15) translateX(${imgTransX.value.toFixed(2)}px) translateY(${imgTransY.value.toFixed(2)}px)`
        : 'scale(1.0) translateX(0px) translateY(0px)',
    transition: isHovered.value
        ? 'transform 0.12s ease-out'
        : 'transform 0.5s cubic-bezier(0.25,1,0.5,1)',
}));

// Cursor-following red radial glow
const glowStyle = computed(() => ({
    background: `radial-gradient(circle 130px at ${glowX.value}% ${glowY.value}%, rgba(188,0,45,0.18) 0%, transparent 75%)`,
    opacity: isHovered.value ? 1 : 0,
    transition: 'opacity 0.3s ease',
}));

// Badge 3D rotation: they "tilt to face the user" slightly
const badgeStyle = computed(() => ({
    transform: isHovered.value
        ? `perspective(400px) rotateX(${(rotX.value * 0.6).toFixed(2)}deg) rotateY(${(-rotY.value * 0.6).toFixed(2)}deg) translateZ(16px)`
        : 'perspective(400px) rotateX(0deg) rotateY(0deg) translateZ(0px)',
    transition: isHovered.value
        ? 'transform 0.12s ease-out'
        : 'transform 0.5s cubic-bezier(0.25,1,0.5,1)',
}));

const firstVolume = computed(() =>
    props.manga.volumes?.length > 0 ? props.manga.volumes[0] : null
);

const handleQuickAdd = (e) => {
    e.preventDefault();
    e.stopPropagation();

    if (!requireAuthAndVerified()) {
        return;
    }

    if (firstVolume.value) {
        cartStore.addItem(props.manga, firstVolume.value, 1);
    }
};

const shortSynopsis = computed(() => {
    const s = props.manga.synopsis || '';
    return s.length > 72 ? s.slice(0, 72).trimEnd() + '…' : s;
});
</script>

<template>
    <div
        ref="cardRef"
        @mousemove="handleMouseMove"
        @mouseenter="handleMouseEnter"
        @mouseleave="handleMouseLeave"
        :style="cardStyle"
        class="group relative bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-md flex flex-col h-full cursor-pointer select-none transform-gpu"
        style="transform-style: preserve-3d;"
    >
        <!-- ─── Cursor-following red glow overlay ─── -->
        <div
            class="absolute inset-0 pointer-events-none z-30 rounded-2xl"
            :style="glowStyle"
        ></div>

        <!-- ─── White specular glare shimmer ─── -->
        <div
            class="absolute inset-0 pointer-events-none z-20 rounded-2xl"
            :style="{
                background: `radial-gradient(ellipse 80px 60px at ${glowX}% ${glowY}%, rgba(255,255,255,0.22), transparent 80%)`,
                opacity: isHovered ? 1 : 0,
                transition: 'opacity 0.25s',
            }"
        ></div>

        <!-- ─── Cover Book Container ─── -->
        <div class="relative aspect-[3/4] overflow-hidden bg-gray-950 cursor-pointer">
            <Link :href="`/manga/${manga.id}`" class="block w-full h-full">
                <div class="w-full h-full bg-gradient-to-br from-[#1c1c24] via-[#121217] to-[#0a0a0e] flex flex-col items-center justify-between p-4 sm:p-5 text-center select-none relative border-b border-gray-800">
                    <!-- Spine Crease Shadow -->
                    <div class="w-2 absolute inset-y-0 left-0 bg-gradient-to-r from-black/60 to-transparent"></div>
                    
                    <!-- Top Edition Tag -->
                    <span class="text-[8px] sm:text-[9px] font-extrabold uppercase tracking-widest text-primary">Édition Misega</span>

                    <!-- Center Title & Author -->
                    <div class="my-auto px-1">
                        <div class="font-gang text-white text-base sm:text-lg font-bold line-clamp-3 leading-tight mb-1.5 drop-shadow">
                            {{ manga.title }}
                        </div>
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block line-clamp-1">
                            {{ manga.author }}
                        </span>
                    </div>

                    <!-- Bottom Genre Pill -->
                    <span class="text-[9px] sm:text-[10px] font-extrabold text-white uppercase tracking-wider bg-white/10 px-2.5 py-0.5 rounded-full border border-white/20">
                        {{ manga.genre }}
                    </span>
                </div>
            </Link>

            <!-- ─── Genre Badge — slides up + 3D rotates to face user ─── -->
            <div
                :style="badgeStyle"
                class="absolute top-3 left-3 z-10 transform-gpu"
            >
                <!-- Default genre label -->
                <div
                    class="bg-gray-900/90 backdrop-blur-md text-white text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-md shadow transition-all duration-300"
                    :class="isHovered ? '-translate-y-1 opacity-0 pointer-events-none h-0 overflow-hidden' : 'opacity-100'"
                >
                    {{ manga.genre }}
                </div>
                <!-- Synopsis revealed on hover — slides in below genre -->
                <div
                    class="bg-primary text-white text-[9px] font-semibold leading-snug px-2.5 py-1.5 rounded-md shadow-lg shadow-primary/30 max-w-[130px] transition-all duration-400"
                    :class="isHovered ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2 pointer-events-none'"
                >
                    <span class="font-extrabold uppercase tracking-wider block mb-0.5">{{ manga.genre }}</span>
                    {{ shortSynopsis }}
                </div>
            </div>

            <!-- ─── Status Badge — also 3D-rotates ─── -->
            <div
                :style="badgeStyle"
                class="absolute top-3 right-3 z-10 transform-gpu"
            >
                <div
                    class="text-[10px] font-bold px-2 py-0.5 rounded shadow transition-all duration-300"
                    :class="[
                        manga.status === 'Terminé'
                            ? 'bg-amber-100 text-amber-800 border border-amber-300'
                            : 'bg-emerald-100 text-emerald-800 border border-emerald-300',
                        isHovered ? 'scale-105' : 'scale-100'
                    ]"
                >
                    {{ manga.status }}
                </div>
            </div>

            <!-- ─── Hover Overlay: Depth gradient + Action Buttons ─── -->
            <div
                class="absolute inset-0 flex flex-col justify-end p-3 gap-2 z-10 transition-opacity duration-300"
                :class="isHovered ? 'opacity-100' : 'opacity-0'"
                style="background: linear-gradient(to top, rgba(0,0,0,0.88) 0%, rgba(0,0,0,0.38) 50%, transparent 100%);"
            >
                <!-- Floating depth action strip -->
                <div
                    class="flex gap-2 transition-transform duration-300"
                    :style="{ transform: isHovered ? 'translateY(0) translateZ(24px)' : 'translateY(12px)' }"
                    style="transform-style: preserve-3d;"
                >
                    <Link
                        :href="`/manga/${manga.id}`"
                        class="flex-1 bg-white/15 hover:bg-white/25 backdrop-blur-md text-white text-xs font-bold py-2 px-3 rounded-xl border border-white/30 flex items-center justify-center gap-1.5 transition shadow-xl"
                    >
                        <Eye class="w-3.5 h-3.5" />
                        Voir la fiche
                    </Link>
                    <button
                        v-if="firstVolume && firstVolume.stock > 0"
                        @click="handleQuickAdd"
                        class="bg-primary hover:bg-primary-hover text-white text-xs font-bold p-2.5 rounded-xl shadow-lg shadow-primary/50 transition hover:scale-110 active:scale-95"
                        title="Ajouter le Tome 1 au panier"
                    >
                        <ShoppingCart class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>

        <!-- ─── Card Info Footer ─── -->
        <div class="p-4 flex flex-col flex-1 justify-between bg-white transition-colors duration-300">
            <div>
                <span class="text-[10px] font-semibold text-gray-400 block mb-0.5 uppercase tracking-wider">
                    {{ manga.author }}
                </span>
                <Link :href="`/manga/${manga.id}`">
                    <h3 class="font-gang text-gray-900 text-base group-hover:text-primary transition-colors duration-300 line-clamp-1 leading-tight">
                        {{ manga.title }}
                    </h3>
                </Link>
            </div>

            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-1 text-xs text-gray-500 font-medium">
                    <BookOpen class="w-3.5 h-3.5 text-primary" />
                    <span>{{ manga.volumes?.length ?? 0 }} Tomes</span>
                </div>
                <div class="text-right">
                    <span class="text-[10px] text-gray-400 block">À partir de</span>
                    <span
                        class="font-bold text-sm transition-colors duration-300"
                        :class="isHovered ? 'text-primary' : 'text-gray-900'"
                    >
                        {{ firstVolume ? formatPrice(firstVolume.price) : '4 500 FCFA' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- ─── Red bottom border accent revealed on hover ─── -->
        <div
            class="absolute bottom-0 inset-x-0 h-0.5 bg-gradient-to-r from-primary via-primary-light to-primary transition-opacity duration-300 rounded-b-2xl"
            :class="isHovered ? 'opacity-100' : 'opacity-0'"
        ></div>
    </div>
</template>

<style scoped>
.will-change-transform {
    will-change: transform;
}
</style>
