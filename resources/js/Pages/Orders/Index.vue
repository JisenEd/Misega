<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MisegaLayout from '@/Layouts/MisegaLayout.vue';
import { Package, Calendar } from '@lucide/vue';
import { formatPrice } from '@/utils/formatPrice';

const props = defineProps({
    orders: { type: Array, default: () => [] }
});

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head title="Mes Commandes - MISEGA" />

    <MisegaLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex items-center justify-between mb-8 border-b border-gray-200 pb-4">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-primary/10 rounded-2xl border border-primary/20 text-primary">
                        <Package class="w-6 h-6" />
                    </div>
                    <div>
                        <h1 class="font-gang text-3xl font-extrabold text-gray-900">Mes Commandes</h1>
                        <p class="text-xs text-gray-500 mt-0.5">Historique de vos achats sur MISEGA</p>
                    </div>
                </div>
            </div>

            <!-- Orders list -->
            <div v-if="orders.length > 0" class="space-y-4">
                <div 
                    v-for="order in orders" 
                    :key="order.id"
                    class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm transition hover:shadow-md space-y-4"
                >
                    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-100 pb-4">
                        <div>
                            <span class="font-mono text-sm font-bold text-gray-900 block">Commande #MSG-{{ order.id }}</span>
                            <span class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                <Calendar class="w-3.5 h-3.5" />
                                {{ formatDate(order.created_at) }}
                            </span>
                        </div>

                        <div class="flex items-center gap-4">
                            <span 
                                class="text-xs font-bold px-3 py-1 rounded-full border"
                                :class="order.status === 'completed' ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-amber-100 text-amber-800 border-amber-300'"
                            >
                                {{ order.status === 'completed' ? 'Payée / En Cours' : order.status }}
                            </span>
                            <span class="font-gang text-xl text-primary font-extrabold">{{ formatPrice(order.total_amount) }}</span>
                        </div>
                    </div>

                    <!-- Items Preview -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <div 
                            v-for="item in order.items" 
                            :key="item.id"
                            class="bg-gray-50 p-3 rounded-xl border border-gray-200 flex items-center gap-3"
                        >
                            <div class="w-10 h-14 rounded bg-gradient-to-br from-gray-900 to-black text-white flex flex-col items-center justify-center shrink-0 border border-gray-200 shadow-sm">
                                <span class="font-gang text-[9px] font-bold text-primary">T.{{ item.volume?.volume_number }}</span>
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-bold text-xs text-gray-900 truncate">{{ item.volume?.manga?.title }}</h4>
                                <span class="text-[11px] text-gray-500 block">Tome {{ item.volume?.volume_number }} (x{{ item.quantity }})</span>
                                <span class="text-[11px] text-primary font-bold">{{ formatPrice(item.unit_price) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty Orders State -->
            <div v-else class="text-center py-20 bg-white rounded-2xl border border-gray-200 p-8 space-y-4 max-w-lg mx-auto shadow-sm">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto text-primary">
                    <Package class="w-8 h-8" />
                </div>
                <h3 class="font-gang text-xl text-gray-900">Aucune commande enregistrée</h3>
                <p class="text-gray-500 text-sm">
                    Vous n'avez pas encore passé de commande. Découvrez notre catalogue pour faire votre premier achat !
                </p>
                <Link 
                    href="/catalog" 
                    class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white text-xs font-bold px-6 py-2.5 rounded-xl transition shadow-md shadow-primary/20"
                >
                    Explorer le catalogue
                </Link>
            </div>
        </div>
    </MisegaLayout>
</template>
