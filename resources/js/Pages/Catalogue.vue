<script setup>
import { ref, computed, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import MisegaLayout from '@/Layouts/MisegaLayout.vue';
import MangaCard from '@/Components/MangaCard.vue';
import MangaSceneBackground from '@/Components/MangaSceneBackground.vue';
import { Search, X, BookOpen, Filter, RotateCcw } from '@lucide/vue';

const props = defineProps({
    mangas: { type: Array, default: () => [] },
    genres: { type: Array, default: () => [] },
    authors: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({ search: '', genre: '', author: '' }) }
});

const selectedGenre = ref(props.filters.genre || '');
const selectedAuthor = ref(props.filters.author || '');
const searchInput = ref(props.filters.search || '');

// Sync props if changed from navigation
watch(() => props.filters, (newFilters) => {
    selectedGenre.value = newFilters.genre || '';
    selectedAuthor.value = newFilters.author || '';
    searchInput.value = newFilters.search || '';
}, { deep: true });

const applyFilters = () => {
    router.get('/catalogue', {
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

const activeFilterCount = computed(() => {
    let count = 0;
    if (selectedGenre.value) count++;
    if (selectedAuthor.value) count++;
    if (searchInput.value) count++;
    return count;
});
</script>

<template>
    <Head title="Catalogue de Mangas — MISEGA" />

    <MisegaLayout>
        <!-- ─── Catalog Header Banner ─── -->
        <div class="relative bg-gradient-to-b from-gray-100 via-white to-gray-50 border-b border-gray-200 pt-24 pb-10 sm:pt-28 sm:pb-14 overflow-hidden">
            <MangaSceneBackground />

            <!-- Ambient glow -->
            <div class="absolute -top-10 right-1/3 w-80 h-80 bg-primary/8 blur-3xl rounded-full pointer-events-none z-0"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="max-w-3xl space-y-3">
                    <div class="inline-flex items-center px-3.5 py-1.5 rounded-full bg-primary/10 border border-primary/20 text-primary text-xs font-extrabold uppercase tracking-wider">
                        Explorez notre collection
                    </div>
                    <h1 class="font-gang text-3xl sm:text-4xl lg:text-5xl text-gray-900 font-extrabold tracking-wide">
                        Catalogue <span class="text-primary">Mangas</span>
                    </h1>
                    <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                        Parcourez l'ensemble de notre catalogue. Utilisez les filtres ci-dessous pour trouver vos séries, genres et auteurs préférés.
                    </p>
                </div>
            </div>
        </div>

        <!-- ─── Main Catalog Content ─── -->
        <div class="relative overflow-hidden min-h-[60vh]">
            <MangaSceneBackground />

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative z-10 space-y-8">

                <!-- ─── Filter & Search Bar ─── -->
                <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm space-y-4">
                    <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                        
                        <!-- Search Input -->
                        <div class="relative flex-1">
                            <input 
                                v-model="searchInput"
                                @keyup.enter="applyFilters"
                                type="text" 
                                placeholder="Rechercher par titre, auteur ou éditeur..." 
                                class="w-full bg-gray-50 text-sm text-gray-900 placeholder-gray-400 rounded-xl py-3 pl-11 pr-10 border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:bg-white transition"
                            />
                            <Search class="w-4 h-4 text-gray-400 absolute left-4 top-3.5" />
                            <button 
                                v-if="searchInput" 
                                @click="searchInput = ''; applyFilters()"
                                class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-600 p-0.5 rounded-full hover:bg-gray-200 transition"
                            >
                                <X class="w-4 h-4" />
                            </button>
                        </div>

                        <!-- Author Filter Select -->
                        <div class="w-full md:w-56">
                            <select 
                                v-model="selectedAuthor"
                                @change="applyFilters"
                                class="w-full bg-gray-50 text-sm text-gray-700 rounded-xl py-3 px-4 border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:bg-white transition"
                            >
                                <option value="">Tous les auteurs</option>
                                <option v-for="author in authors" :key="author" :value="author">
                                    {{ author }}
                                </option>
                            </select>
                        </div>

                        <!-- Apply Search Button -->
                        <button
                            @click="applyFilters"
                            class="bg-primary hover:bg-primary-hover text-white text-xs font-bold px-6 py-3 rounded-xl shadow-md shadow-primary/20 transition flex items-center justify-center gap-2"
                        >
                            <Search class="w-4 h-4" />
                            Rechercher
                        </button>
                    </div>

                    <!-- Genre Quick Filter Pills -->
                    <div class="pt-2 border-t border-gray-100 flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mr-2 flex items-center gap-1">
                            <Filter class="w-3.5 h-3.5" /> Genres :
                        </span>
                        <button 
                            @click="filterByGenre('')"
                            class="text-xs font-bold px-3.5 py-1.5 rounded-lg border transition"
                            :class="!selectedGenre ? 'bg-primary text-white border-primary shadow-sm' : 'bg-gray-100 text-gray-600 border-gray-200 hover:bg-gray-200'"
                        >
                            Tous
                        </button>
                        <button 
                            v-for="genre in genres" 
                            :key="genre"
                            @click="filterByGenre(genre)"
                            class="text-xs font-bold px-3.5 py-1.5 rounded-lg border transition"
                            :class="selectedGenre === genre ? 'bg-primary text-white border-primary shadow-sm' : 'bg-gray-100 text-gray-600 border-gray-200 hover:bg-gray-200'"
                        >
                            {{ genre }}
                        </button>

                        <button 
                            v-if="activeFilterCount > 0"
                            @click="resetFilters"
                            class="text-xs font-semibold text-gray-400 hover:text-primary flex items-center gap-1 ml-auto transition"
                        >
                            <RotateCcw class="w-3.5 h-3.5" /> Réinitialiser
                        </button>
                    </div>
                </div>

                <!-- Active Filters & Count Header -->
                <div class="flex items-center justify-between text-xs font-semibold text-gray-500">
                    <div>
                        Affichage de <span class="font-extrabold text-gray-900">{{ mangas.length }}</span> manga{{ mangas.length > 1 ? 's' : '' }}
                    </div>
                    <div v-if="activeFilterCount > 0" class="flex items-center gap-2">
                        <span>Filtres actifs :</span>
                        <span v-if="selectedGenre" class="bg-primary/10 text-primary px-2.5 py-0.5 rounded-md border border-primary/20">
                            Genre: {{ selectedGenre }}
                        </span>
                        <span v-if="selectedAuthor" class="bg-primary/10 text-primary px-2.5 py-0.5 rounded-md border border-primary/20">
                            Auteur: {{ selectedAuthor }}
                        </span>
                        <span v-if="searchInput" class="bg-primary/10 text-primary px-2.5 py-0.5 rounded-md border border-primary/20">
                            "{{ searchInput }}"
                        </span>
                    </div>
                </div>

                <!-- ─── Manga Cards Grid ─── -->
                <div v-if="mangas.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <MangaCard 
                        v-for="manga in mangas" 
                        :key="manga.id" 
                        :manga="manga" 
                    />
                </div>

                <!-- ─── Empty State ─── -->
                <div v-else class="text-center py-20 bg-white rounded-2xl border border-gray-200 p-8 space-y-4 shadow-sm">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto text-primary">
                        <Search class="w-8 h-8" />
                    </div>
                    <h3 class="font-gang text-xl text-gray-900">Aucun manga trouvé</h3>
                    <p class="text-gray-500 text-sm max-w-md mx-auto">
                        Nous n'avons trouvé aucun résultat correspondant à vos critères. Essayez de modifier votre recherche ou de réinitialiser les filtres.
                    </p>
                    <button 
                        @click="resetFilters" 
                        class="bg-primary hover:bg-primary-hover text-white text-xs font-bold px-6 py-2.5 rounded-xl transition shadow-md shadow-primary/20 inline-flex items-center gap-2"
                    >
                        <RotateCcw class="w-4 h-4" /> Voir tous les mangas
                    </button>
                </div>
            </div>
        </div>
    </MisegaLayout>
</template>
