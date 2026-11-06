<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import MisegaLayout from '@/Layouts/MisegaLayout.vue';
import MangaCard from '@/Components/MangaCard.vue';
import { useCartStore } from '@/stores/useCartStore';
import { ShoppingCart, Check, BookOpen, AlertCircle, ArrowLeft, Building2, User } from '@lucide/vue';
import { formatPrice } from '@/utils/formatPrice';
import { requireAuthAndVerified } from '@/utils/authGuard';

const props = defineProps({
    manga: { type: Object, required: true },
    relatedMangas: { type: Array, default: () => [] }
});

const cartStore = useCartStore();
const quantities = ref({});
const addedVolumeId = ref(null);
const hasImageError = ref(false);

const imageSource = computed(() => props.manga.cover_url || props.manga.cover_image || null);

const getQty = (volumeId) => quantities.value[volumeId] || 1;
const setQty = (volumeId, delta, maxStock) => {
    const current = getQty(volumeId);
    const next = current + delta;
    if (next >= 1 && next <= maxStock) {
        quantities.value[volumeId] = next;
    }
};

const handleAddToCart = (volume) => {
    if (!requireAuthAndVerified()) {
        return;
    }

    const qty = getQty(volume.id);
    cartStore.addItem(props.manga, volume, qty);
    
    // Toast notification state
    addedVolumeId.value = volume.id;
    setTimeout(() => {
        addedVolumeId.value = null;
    }, 2000);
};
</script>

<template>
    <Head :title="`${manga.title} - Fiche Produit`" />

    <MisegaLayout>
        <!-- Hero Header -->
        <div class="relative bg-gradient-to-b from-gray-100 via-white to-gray-50 border-b border-gray-200 pt-24 pb-10 sm:pt-28 sm:pb-14 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <!-- Back Link -->
                <Link href="/catalog" class="inline-flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-gray-900 mb-6 transition">
                    <ArrowLeft class="w-4 h-4" />
                    Retour au catalogue
                </Link>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                    <!-- Left: Main Cover Poster -->
                    <div class="lg:col-span-4 max-w-xs mx-auto lg:mx-0 w-full">
                        <div class="aspect-[3/4] rounded-2xl overflow-hidden shadow-2xl border-2 border-white bg-gradient-to-br from-[#1c1c24] via-[#121217] to-[#0a0a0e] flex flex-col items-center justify-between p-6 text-center select-none relative">
                            <div class="w-2.5 absolute inset-y-0 left-0 bg-gradient-to-r from-black/60 to-transparent"></div>
                            <span class="text-[9px] font-extrabold uppercase tracking-widest text-primary">Éditions Misega</span>
                            <div class="my-auto px-2">
                                <div class="font-gang text-white text-2xl sm:text-3xl font-bold line-clamp-3 leading-tight mb-3">
                                    {{ manga.title }}
                                </div>
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest block">
                                    {{ manga.author }}
                                </span>
                            </div>
                            <span class="text-xs font-bold text-white uppercase tracking-widest bg-white/10 px-3.5 py-1 rounded-full border border-white/20">
                                {{ manga.genre }}
                            </span>
                        </div>
                    </div>

                    <!-- Right: Details & Synopsis -->
                    <div class="lg:col-span-8 space-y-6">
                        <div class="space-y-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="bg-primary/10 text-primary border border-primary/20 text-xs font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
                                    {{ manga.genre }}
                                </span>
                                <span 
                                    class="text-xs font-bold px-3 py-1 rounded-full border"
                                    :class="manga.status === 'Terminé' ? 'bg-amber-100 text-amber-800 border-amber-300' : 'bg-emerald-100 text-emerald-800 border-emerald-300'"
                                >
                                    {{ manga.status }}
                                </span>
                            </div>

                            <h1 class="font-gang text-4xl sm:text-5xl font-extrabold text-gray-900 tracking-wide">
                                {{ manga.title }}
                            </h1>
                        </div>

                        <!-- Meta Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-2">
                            <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-sm flex items-center gap-3">
                                <User class="w-5 h-5 text-primary shrink-0" />
                                <div>
                                    <span class="text-[10px] text-gray-400 block font-semibold">Auteur</span>
                                    <span class="text-xs font-bold text-gray-900">{{ manga.author }}</span>
                                </div>
                            </div>
                            <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-sm flex items-center gap-3">
                                <Building2 class="w-5 h-5 text-primary shrink-0" />
                                <div>
                                    <span class="text-[10px] text-gray-400 block font-semibold">Éditeur</span>
                                    <span class="text-xs font-bold text-gray-900">{{ manga.publisher }}</span>
                                </div>
                            </div>
                            <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-sm flex items-center gap-3 col-span-2 sm:col-span-1">
                                <BookOpen class="w-5 h-5 text-primary shrink-0" />
                                <div>
                                    <span class="text-[10px] text-gray-400 block font-semibold">Tomes Disponibles</span>
                                    <span class="text-xs font-bold text-gray-900">{{ manga.volumes ? manga.volumes.length : 0 }} tomes</span>
                                </div>
                            </div>
                        </div>

                        <!-- Synopsis -->
                        <div class="space-y-2 pt-2">
                            <h3 class="font-gang text-lg text-gray-900">Synopsis</h3>
                            <p class="text-gray-700 text-sm sm:text-base leading-relaxed bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
                                {{ manga.synopsis }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Volumes Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex items-center justify-between mb-8 border-b border-gray-200 pb-4">
                <div>
                    <h2 class="font-gang text-2xl sm:text-3xl text-gray-900 tracking-wide">
                        Tomes disponibles
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">Sélectionnez et ajoutez les volumes de votre choix au panier</p>
                </div>
            </div>

            <div v-if="manga.volumes && manga.volumes.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div 
                    v-for="volume in manga.volumes" 
                    :key="volume.id"
                    class="bg-white border border-gray-200 rounded-2xl p-5 flex gap-4 transition hover:shadow-xl relative"
                >
                    <!-- Volume Cover -->
                    <div class="w-24 aspect-[3/4] rounded-xl overflow-hidden bg-gradient-to-br from-gray-900 via-gray-950 to-black shrink-0 border border-gray-200 flex flex-col items-center justify-between p-2.5 text-center select-none shadow-sm relative">
                        <div class="w-1.5 absolute inset-y-0 left-0 bg-gradient-to-r from-black/60 to-transparent"></div>
                        <span class="text-[8px] font-extrabold uppercase tracking-widest text-primary">MISEGA</span>
                        <div class="my-auto">
                            <span class="font-gang text-white text-base font-bold block leading-tight">T.{{ volume.volume_number }}</span>
                            <span class="text-[8px] text-gray-400 line-clamp-1 mt-0.5">{{ manga.title }}</span>
                        </div>
                        <span class="text-[7px] text-gray-500 font-mono tracking-wider">VOL. {{ volume.volume_number }}</span>
                    </div>

                    <!-- Volume Info & Add to Cart -->
                    <div class="flex flex-col justify-between flex-1">
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="font-gang text-xl text-gray-900">Tome {{ volume.volume_number }}</h3>
                                <span class="font-bold text-primary text-base">{{ formatPrice(volume.price) }}</span>
                            </div>
                            <span v-if="volume.isbn" class="text-[10px] text-gray-400 font-mono block mt-0.5">ISBN: {{ volume.isbn }}</span>

                            <!-- Stock Badge -->
                            <div class="mt-2">
                                <span 
                                    v-if="volume.stock > 5" 
                                    class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600"
                                >
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    En stock ({{ volume.stock }})
                                </span>
                                <span 
                                    v-else-if="volume.stock > 0" 
                                    class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-600"
                                >
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                    Derniers exempl. ({{ volume.stock }})
                                </span>
                                <span 
                                    v-else 
                                    class="inline-flex items-center gap-1 text-[11px] font-bold text-red-600"
                                >
                                    <AlertCircle class="w-3 h-3" />
                                    Rupture de stock
                                </span>
                            </div>
                        </div>

                        <!-- Quantity Selector + Button -->
                        <div v-if="volume.stock > 0" class="mt-4 space-y-2">
                            <div class="flex items-center gap-2">
                                <!-- Qty controls -->
                                <div class="flex items-center border border-gray-200 rounded-xl bg-gray-50 overflow-hidden">
                                    <button 
                                        @click="setQty(volume.id, -1, volume.stock)"
                                        class="px-3 py-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-200 text-sm font-bold"
                                    >
                                        -
                                    </button>
                                    <span class="px-3 py-1 text-xs font-bold text-gray-900 min-w-[28px] text-center">
                                        {{ getQty(volume.id) }}
                                    </span>
                                    <button 
                                        @click="setQty(volume.id, 1, volume.stock)"
                                        class="px-3 py-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-200 text-sm font-bold"
                                    >
                                        +
                                    </button>
                                </div>

                                <!-- Add Button -->
                                <button 
                                    @click="handleAddToCart(volume)"
                                    class="flex-1 bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2 px-3 rounded-xl transition shadow-md shadow-primary/20 flex items-center justify-center gap-1.5"
                                    :class="addedVolumeId === volume.id ? 'bg-emerald-600 hover:bg-emerald-600' : ''"
                                >
                                    <Check v-if="addedVolumeId === volume.id" class="w-4 h-4" />
                                    <ShoppingCart v-else class="w-4 h-4" />
                                    {{ addedVolumeId === volume.id ? 'Ajouté !' : 'Ajouter' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Mangas -->
            <div v-if="relatedMangas.length > 0" class="mt-16 pt-10 border-t border-gray-200">
                <h3 class="font-gang text-2xl text-gray-900 mb-6">Dans le même genre ({{ manga.genre }})</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6">
                    <MangaCard v-for="rManga in relatedMangas" :key="rManga.id" :manga="rManga" />
                </div>
            </div>
        </div>
    </MisegaLayout>
</template>
