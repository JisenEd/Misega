<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import MisegaLayout from '@/Layouts/MisegaLayout.vue';
import { useCartStore } from '@/stores/useCartStore';
import { ShieldCheck, CreditCard, Lock, ArrowLeft, Loader2 } from '@lucide/vue';
import { formatPrice } from '@/utils/formatPrice';

const cartStore = useCartStore();
const page = usePage();
const user = computed(() => page.props.auth?.user);

const paymentMethod = ref('card');

const form = useForm({
    shipping_name: user.value?.name || '',
    shipping_email: user.value?.email || '',
    shipping_address: '12 Rue des Mangas, 75001 Paris',
    payment_method: 'simulated',
    items: [],
});

onMounted(() => {
    if (cartStore.items.length === 0) {
        router.visit('/cart');
    }
});

const FREE_SHIPPING_THRESHOLD = 25000;
const SHIPPING_COST = 3000;

const shippingCost = computed(() => cartStore.totalPrice >= FREE_SHIPPING_THRESHOLD ? 0 : SHIPPING_COST);
const grandTotal = computed(() => cartStore.totalPrice + shippingCost.value);

const isSubmitting = ref(false);

const submitOrder = () => {
    form.items = cartStore.items.map(item => ({
        volume_id: item.volume_id,
        quantity: item.quantity,
    }));

    isSubmitting.value = true;

    form.post('/checkout', {
        onSuccess: () => {
            cartStore.clearCart();
        },
        onFinish: () => {
            isSubmitting.value = false;
        }
    });
};
</script>

<template>
    <Head title="Validation de votre commande - MISEGA" />

    <MisegaLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-10 sm:pt-28 sm:pb-12">
            <div class="flex items-center justify-between mb-8 border-b border-gray-200 pb-4">
                <h1 class="font-gang text-3xl font-extrabold text-gray-900">Tunnel de Commande</h1>
                <Link href="/cart" class="text-xs font-bold text-gray-500 hover:text-gray-900 flex items-center gap-1">
                    <ArrowLeft class="w-4 h-4" />
                    Modifier le panier
                </Link>
            </div>

            <form @submit.prevent="submitOrder" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Left: Shipping & Payment Form -->
                <div class="lg:col-span-7 space-y-6">
                    <!-- Step 1: Shipping info -->
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 space-y-4 shadow-sm">
                        <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                            <div class="w-8 h-8 rounded-full bg-primary/10 border border-primary/20 text-primary font-bold flex items-center justify-center text-sm">
                                1
                            </div>
                            <h2 class="font-gang text-xl text-gray-900">Adresse de livraison</h2>
                        </div>

                        <div class="space-y-4 pt-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Nom Complet</label>
                                <input 
                                    v-model="form.shipping_name"
                                    type="text" 
                                    required 
                                    placeholder="Ex: Jean Dupont" 
                                    class="w-full bg-gray-50 text-sm text-gray-900 rounded-xl px-4 py-2.5 border border-gray-200 focus:border-primary focus:outline-none"
                                />
                                <span v-if="form.errors.shipping_name" class="text-xs text-red-500 mt-1 block">{{ form.errors.shipping_name }}</span>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Adresse Email de confirmation</label>
                                <input 
                                    v-model="form.shipping_email"
                                    type="email" 
                                    required 
                                    placeholder="jean.dupont@example.com" 
                                    class="w-full bg-gray-50 text-sm text-gray-900 rounded-xl px-4 py-2.5 border border-gray-200 focus:border-primary focus:outline-none"
                                />
                                <span v-if="form.errors.shipping_email" class="text-xs text-red-500 mt-1 block">{{ form.errors.shipping_email }}</span>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Adresse complète de livraison</label>
                                <textarea 
                                    v-model="form.shipping_address"
                                    rows="3"
                                    required 
                                    placeholder="12 Rue des Mangas, 75001 Paris, France" 
                                    class="w-full bg-gray-50 text-sm text-gray-900 rounded-xl px-4 py-2.5 border border-gray-200 focus:border-primary focus:outline-none"
                                ></textarea>
                                <span v-if="form.errors.shipping_address" class="text-xs text-red-500 mt-1 block">{{ form.errors.shipping_address }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Payment Method (Simulated) -->
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 space-y-4 shadow-sm">
                        <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                            <div class="w-8 h-8 rounded-full bg-primary/10 border border-primary/20 text-primary font-bold flex items-center justify-center text-sm">
                                2
                            </div>
                            <h2 class="font-gang text-xl text-gray-900">Mode de paiement (Simulé)</h2>
                        </div>

                        <div class="space-y-3 pt-2">
                            <label 
                                class="flex items-center justify-between p-4 rounded-xl border cursor-pointer transition"
                                :class="paymentMethod === 'card' ? 'bg-primary/5 border-primary text-gray-900' : 'bg-gray-50 border-gray-200 text-gray-600 hover:border-gray-300'"
                            >
                                <div class="flex items-center gap-3">
                                    <input type="radio" v-model="paymentMethod" value="card" class="text-primary focus:ring-primary" />
                                    <CreditCard class="w-5 h-5 text-primary" />
                                    <div>
                                        <span class="text-xs font-bold block text-gray-900">Carte Bancaire (Simulé)</span>
                                        <span class="text-[10px] text-gray-500">Paiement instantané sécurisé par SSL</span>
                                    </div>
                                </div>
                                <div class="flex gap-1 text-[10px] font-bold text-gray-400">
                                    <span>VISA</span>
                                    <span>MC</span>
                                </div>
                            </label>

                            <!-- Fake Card Form fields when card is chosen -->
                            <div v-if="paymentMethod === 'card'" class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-3">
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">Numéro de carte fictif</label>
                                    <input 
                                        type="text" 
                                        value="4242 •••• •••• 4242" 
                                        disabled
                                        class="w-full bg-white text-xs text-gray-700 rounded-lg px-3 py-2 border border-gray-200 font-mono"
                                    />
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">Expiration</label>
                                        <input type="text" value="12 / 28" disabled class="w-full bg-white text-xs text-gray-700 rounded-lg px-3 py-2 border border-gray-200 text-center font-mono" />
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">CVV</label>
                                        <input type="text" value="888" disabled class="w-full bg-white text-xs text-gray-700 rounded-lg px-3 py-2 border border-gray-200 text-center font-mono" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Order Review -->
                <div class="lg:col-span-5 bg-white border border-gray-200 rounded-2xl p-6 space-y-6 sticky top-28 shadow-lg">
                    <h3 class="font-gang text-xl text-gray-900 border-b border-gray-100 pb-4">Récapitulatif ({{ cartStore.totalItems }} tomes)</h3>

                    <!-- Mini item list -->
                    <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                        <div 
                            v-for="item in cartStore.items" 
                            :key="item.volume_id"
                            class="flex items-center justify-between gap-3 text-xs border-b border-gray-100 pb-2"
                        >
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-8 h-10 rounded bg-gradient-to-br from-gray-900 to-black text-white flex flex-col items-center justify-center shrink-0 border border-gray-200 shadow-sm">
                                    <span class="font-gang text-[8px] font-bold text-primary">T.{{ item.volume_number }}</span>
                                </div>
                                <div class="min-w-0">
                                    <span class="font-bold text-gray-900 block truncate">{{ item.manga_title }}</span>
                                    <span class="text-gray-500 text-[10px]">Tome {{ item.volume_number }} (x{{ item.quantity }})</span>
                                </div>
                            </div>
                            <span class="font-bold text-gray-900 shrink-0">{{ formatPrice(item.price * item.quantity) }}</span>
                        </div>
                    </div>

                    <div class="space-y-2 text-xs text-gray-600 border-t border-gray-100 pt-4">
                        <div class="flex justify-between">
                            <span>Sous-total</span>
                            <span class="font-bold text-gray-900">{{ formatPrice(cartStore.totalPrice) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Livraison</span>
                            <span :class="shippingCost === 0 ? 'text-emerald-600 font-bold' : 'text-gray-900 font-bold'">
                                {{ shippingCost === 0 ? 'Offerte' : formatPrice(shippingCost) }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-between items-center">
                        <span class="font-bold text-gray-900">Total à payer</span>
                        <span class="font-gang text-2xl text-primary font-extrabold">{{ formatPrice(grandTotal) }}</span>
                    </div>

                    <!-- Confirm Order Button -->
                    <button 
                        type="submit"
                        :disabled="isSubmitting"
                        class="w-full bg-primary hover:bg-primary-hover text-white text-sm font-bold py-3.5 px-4 rounded-xl shadow-md shadow-primary/20 flex items-center justify-center gap-2 transition hover:scale-[1.02] disabled:opacity-50"
                    >
                        <Loader2 v-if="isSubmitting" class="w-5 h-5 animate-spin" />
                        <Lock v-else class="w-4 h-4" />
                        {{ isSubmitting ? 'Traitement en cours...' : 'Confirmer et Payer ma commande' }}
                    </button>

                    <div class="flex items-center justify-center gap-2 text-[11px] text-gray-500 pt-2">
                        <ShieldCheck class="w-4 h-4 text-emerald-600" />
                        Garantie satisfait ou remboursé sous 14 jours
                    </div>
                </div>
            </form>
        </div>
    </MisegaLayout>
</template>
