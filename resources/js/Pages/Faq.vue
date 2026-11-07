<script setup>
import { ref, computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import MisegaLayout from '@/Layouts/MisegaLayout.vue';
import MangaSceneBackground from '@/Components/MangaSceneBackground.vue';
import {
    Search,
    ChevronDown,
    Package,
    BookOpen,
    CreditCard,
    RotateCcw,
    Send,
    CheckCircle2,
    MessageSquare,
    Clock,
    ShieldCheck,
} from '@lucide/vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);

const searchQuery = ref('');
const activeCategory = ref('all');
const openId = ref(1);

const categories = [
    { id: 'all', label: 'Toutes les questions' },
    { id: 'shipping', label: 'Livraison & Suivi', icon: Package },
    { id: 'editions', label: 'Tomes & Éditions', icon: BookOpen },
    { id: 'payment', label: 'Paiement', icon: CreditCard },
    { id: 'returns', label: 'Retours & SAV', icon: RotateCcw },
];

const faqs = [
    {
        id: 1,
        category: 'shipping',
        question: 'Quels sont les délais de préparation et de livraison ?',
        answer:
            'Toutes les commandes passées avant 14h sont préparées et expédiées sous 24h ouvrées. La livraison prend ensuite entre 24h et 72h selon votre localité. Vous recevez une notification dès que votre colis quitte notre entrepôt.',
    },
    {
        id: 2,
        category: 'shipping',
        question: 'Comment mes mangas sont-ils protégés pendant le transport ?',
        answer:
            'Chaque tome est enveloppé individuellement sous film protecteur anti-humidité, puis calé dans un carton rigide à double cannelure avec coins renforcés afin d’éviter tout pli ou choc sur les couvertures et les coiffes.',
    },
    {
        id: 3,
        category: 'shipping',
        question: 'Comment suivre l’état de ma commande en temps réel ?',
        answer:
            'Rendez-vous dans votre espace « Mes Commandes » accessible depuis la barre de navigation ou le pied de page. Vous y trouverez le statut détaillé (En attente, Confirmée, Expédiée, Livrée) ainsi que le récapitulatif tome par tome.',
    },
    {
        id: 4,
        category: 'editions',
        question: 'Les mangas vendus sur MISEGA sont-ils tous neufs et officiels ?',
        answer:
            'Oui, 100 % de notre catalogue provient directement des distributeurs et éditeurs officiels francophones. Aucun tome d’occasion n’est vendu sur MISEGA : chaque volume est neuf, complet et en édition originale.',
    },
    {
        id: 5,
        category: 'editions',
        question: 'Un tome est affiché en rupture de stock, quand sera-t-il de retour ?',
        answer:
            'Les réassorts sur les séries Shōnen et Seinen majeures ont lieu chaque semaine. Si un tome spécifique ou un coffret collector que vous recherchez est épuisé, écrivez-nous via le formulaire ci-contre pour être prévenu en priorité.',
    },
    {
        id: 6,
        category: 'payment',
        question: 'Quels moyens de paiement acceptez-vous ?',
        answer:
            'Nous acceptons les paiements sécurisés par carte bancaire (Visa, Mastercard), Mobile Money ainsi que le règlement à la livraison selon les zones éligibles. Toutes les transactions sont chiffrées de bout en bout.',
    },
    {
        id: 7,
        category: 'returns',
        question: 'Que faire si un tome arrive abîmé ou présente un défaut d’impression ?',
        answer:
            'Vous disposez de 14 jours après réception pour nous signaler tout défaut (pages inversées, couverture cornée durant le transport). Envoyez-nous votre numéro de commande via le support : nous procédons à un échange à nos frais.',
    },
    {
        id: 8,
        category: 'returns',
        question: 'Puis-je modifier ou annuler ma commande après validation ?',
        answer:
            'Tant que votre commande affiche le statut « En attente » ou « Confirmée » (avant son expédition), vous pouvez demander une modification d’adresse ou une annulation immédiate en nous contactant via le formulaire de support.',
    },
];

const filteredFaqs = computed(() => {
    const q = searchQuery.value.trim().toLowerCase();
    return faqs.filter((item) => {
        const matchCat = activeCategory.value === 'all' || item.category === activeCategory.value;
        const matchQuery =
            !q ||
            item.question.toLowerCase().includes(q) ||
            item.answer.toLowerCase().includes(q);
        return matchCat && matchQuery;
    });
});

const toggleFaq = (id) => {
    openId.value = openId.value === id ? null : id;
};

// Formulaire de support
const contactForm = ref({
    name: user.value?.name || '',
    email: user.value?.email || '',
    orderNumber: '',
    subject: 'Suivi de commande',
    message: '',
});

const isSubmitting = ref(false);
const submitted = ref(false);

const submitSupport = () => {
    if (!contactForm.value.message.trim() || !contactForm.value.email.trim()) return;
    isSubmitting.value = true;
    setTimeout(() => {
        isSubmitting.value = false;
        submitted.value = true;
        contactForm.value.message = '';
        contactForm.value.orderNumber = '';
    }, 550);
};
</script>

<template>
    <Head title="FAQ & Support — MISEGA" />

    <MisegaLayout>
        <!-- ─── En-tête de la page FAQ & Support ─── -->
        <div class="relative bg-gradient-to-b from-gray-100 via-white to-gray-50 border-b border-gray-200 pt-24 pb-10 sm:pt-28 sm:pb-14 overflow-hidden">
            <MangaSceneBackground />

            <div class="absolute -top-10 right-1/3 w-80 h-80 bg-primary/8 blur-3xl rounded-full pointer-events-none z-0"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="max-w-3xl space-y-3">
                    <div class="inline-flex items-center px-3.5 py-1.5 rounded-full bg-primary/10 border border-primary/20 text-primary text-xs font-extrabold uppercase tracking-wider">
                        Centre d'aide & Assistance
                    </div>
                    <h1 class="font-gang text-3xl sm:text-4xl lg:text-5xl text-gray-900 font-extrabold tracking-wide">
                        FAQ &amp; <span class="text-primary">Support</span>
                    </h1>
                    <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                        Une question sur vos tomes, l'emballage anti-choc ou le suivi de votre colis ? Retrouvez nos réponses instantanées ou écrivez directement à notre équipe.
                    </p>
                </div>

                <!-- Barre de recherche rapide + Filtres de catégories -->
                <div class="mt-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="relative w-full md:max-w-md">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Rechercher une question (ex. livraison, emballage, retour...)"
                            class="w-full bg-white text-sm text-gray-900 placeholder-gray-400 rounded-2xl py-3 pl-11 pr-4 border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/15 focus:outline-none shadow-sm transition-all"
                        />
                        <Search class="w-4 h-4 text-gray-400 absolute left-4 top-3.5" />
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <button
                            v-for="cat in categories"
                            :key="cat.id"
                            type="button"
                            @click="activeCategory = cat.id"
                            class="px-3.5 py-2 rounded-full text-xs font-bold transition-all duration-300 cursor-pointer flex items-center gap-1.5"
                            :class="
                                activeCategory === cat.id
                                    ? 'bg-primary text-white shadow-md shadow-primary/25'
                                    : 'bg-white text-gray-700 border border-gray-200 hover:border-primary/40 hover:text-primary'
                            "
                        >
                            <component :is="cat.icon" v-if="cat.icon" class="w-3.5 h-3.5" />
                            <span>{{ cat.label }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ─── Contenu Principal : Accordéon FAQ + Formulaire Support ─── -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

                <!-- Colonne Gauche : Questions Fréquentes (7 cols) -->
                <div class="lg:col-span-7 space-y-4">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="font-gang text-xl sm:text-2xl text-gray-900">
                            Questions Fréquentes
                        </h2>
                        <span class="text-xs font-semibold text-gray-400">
                            {{ filteredFaqs.length }} réponse{{ filteredFaqs.length > 1 ? 's' : '' }}
                        </span>
                    </div>

                    <div v-if="filteredFaqs.length === 0" class="bg-gray-50 border border-gray-200 rounded-2xl p-8 text-center space-y-3">
                        <p class="text-sm font-semibold text-gray-700">
                            Aucune question ne correspond à « {{ searchQuery }} ».
                        </p>
                        <button
                            type="button"
                            @click="searchQuery = ''; activeCategory = 'all'"
                            class="text-xs font-bold text-primary hover:underline"
                        >
                            Réinitialiser la recherche
                        </button>
                    </div>

                    <!-- Liste d'accordéons à cadence soyeuse (Règle n°5) -->
                    <div
                        v-for="item in filteredFaqs"
                        :key="item.id"
                        class="bg-white rounded-2xl border transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] overflow-hidden"
                        :class="
                            openId === item.id
                                ? 'border-primary/40 shadow-lg shadow-primary/5'
                                : 'border-gray-200/90 hover:border-gray-300 shadow-sm'
                        "
                    >
                        <button
                            type="button"
                            @click="toggleFaq(item.id)"
                            class="w-full text-left px-6 py-5 flex items-center justify-between gap-4 cursor-pointer focus:outline-none"
                        >
                            <span
                                class="text-sm sm:text-base font-bold transition-colors duration-300"
                                :class="openId === item.id ? 'text-primary' : 'text-gray-900'"
                            >
                                {{ item.question }}
                            </span>
                            <span
                                class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]"
                                :class="
                                    openId === item.id
                                        ? 'bg-primary text-white rotate-180'
                                        : 'bg-gray-100 text-gray-600'
                                "
                            >
                                <ChevronDown class="w-4 h-4" />
                            </span>
                        </button>

                        <!-- Déploiement fluide par grille CSS (0fr -> 1fr) -->
                        <div
                            class="faq-collapse-grid"
                            :class="openId === item.id ? 'is-open' : ''"
                        >
                            <div class="overflow-hidden">
                                <p class="px-6 pb-5 pt-1 text-xs sm:text-sm text-gray-600 leading-relaxed border-t border-gray-100">
                                    {{ item.answer }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Raccourcis utiles -->
                    <div class="pt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <Link
                            href="/orders"
                            class="group p-4 rounded-2xl bg-gray-50 hover:bg-white border border-gray-200 hover:border-primary/40 transition-all duration-300 flex items-center gap-3.5 shadow-sm"
                        >
                            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                                <Package class="w-5 h-5" />
                            </div>
                            <div>
                                <div class="text-xs font-bold text-gray-900 group-hover:text-primary transition-colors">
                                    Suivre mes commandes
                                </div>
                                <div class="text-[11px] text-gray-500">
                                    Historique et état d'expédition
                                </div>
                            </div>
                        </Link>

                        <Link
                            href="/catalogue"
                            class="group p-4 rounded-2xl bg-gray-50 hover:bg-white border border-gray-200 hover:border-primary/40 transition-all duration-300 flex items-center gap-3.5 shadow-sm"
                        >
                            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                                <BookOpen class="w-5 h-5" />
                            </div>
                            <div>
                                <div class="text-xs font-bold text-gray-900 group-hover:text-primary transition-colors">
                                    Explorer le catalogue
                                </div>
                                <div class="text-[11px] text-gray-500">
                                    Disponibilité des tomes en direct
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Colonne Droite : Contacter le Support (5 cols) -->
                <div class="lg:col-span-5">
                    <div class="bg-white rounded-3xl border border-gray-200/90 p-6 sm:p-8 shadow-xl shadow-gray-200/40 space-y-6">
                        <div class="space-y-1.5">
                            <div class="inline-flex items-center gap-1.5 text-xs font-extrabold uppercase tracking-wider text-primary">
                                <MessageSquare class="w-3.5 h-3.5" />
                                Support Direct
                            </div>
                            <h3 class="font-gang text-2xl text-gray-900">
                                Écrire à l'équipe MISEGA
                            </h3>
                            <p class="text-xs text-gray-500 leading-relaxed">
                                Notre équipe vous répond sous 24h ouvrées pour toute demande concernant une commande ou une série.
                            </p>
                        </div>

                        <!-- Message de confirmation -->
                        <div
                            v-if="submitted"
                            class="p-5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 space-y-2"
                        >
                            <div class="flex items-center gap-2 font-bold text-sm text-emerald-700">
                                <CheckCircle2 class="w-5 h-5 shrink-0" />
                                <span>Votre message a bien été transmis !</span>
                            </div>
                            <p class="text-xs text-emerald-700/90 leading-relaxed">
                                Merci de nous avoir contactés. Un conseiller MISEGA vous répondra à l'adresse <strong>{{ contactForm.email }}</strong> dans les plus brefs délais.
                            </p>
                            <button
                                type="button"
                                @click="submitted = false"
                                class="text-xs font-bold text-emerald-800 underline pt-1 cursor-pointer"
                            >
                                Envoyer une autre demande
                            </button>
                        </div>

                        <!-- Formulaire -->
                        <form v-else @submit.prevent="submitSupport" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                        Votre nom
                                    </label>
                                    <input
                                        v-model="contactForm.name"
                                        type="text"
                                        required
                                        placeholder="Ex. Dodji A."
                                        class="w-full rounded-xl border border-gray-200 bg-gray-50/70 focus:bg-white py-2.5 px-3.5 text-xs text-gray-900 focus:border-primary focus:ring-2 focus:ring-primary/15 focus:outline-none transition"
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                        Adresse e-mail
                                    </label>
                                    <input
                                        v-model="contactForm.email"
                                        type="email"
                                        required
                                        placeholder="vous@exemple.com"
                                        class="w-full rounded-xl border border-gray-200 bg-gray-50/70 focus:bg-white py-2.5 px-3.5 text-xs text-gray-900 focus:border-primary focus:ring-2 focus:ring-primary/15 focus:outline-none transition"
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                        Sujet de la demande
                                    </label>
                                    <select
                                        v-model="contactForm.subject"
                                        class="w-full rounded-xl border border-gray-200 bg-gray-50/70 focus:bg-white py-2.5 px-3.5 text-xs text-gray-900 focus:border-primary focus:ring-2 focus:ring-primary/15 focus:outline-none transition"
                                    >
                                        <option value="Suivi de commande">Suivi de commande</option>
                                        <option value="Disponibilité d'un tome">Disponibilité d'un tome</option>
                                        <option value="Retour ou échange">Retour ou échange</option>
                                        <option value="Autre question">Autre question</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                        N° de commande <span class="text-gray-400 font-normal">(optionnel)</span>
                                    </label>
                                    <input
                                        v-model="contactForm.orderNumber"
                                        type="text"
                                        placeholder="Ex. #1042"
                                        class="w-full rounded-xl border border-gray-200 bg-gray-50/70 focus:bg-white py-2.5 px-3.5 text-xs text-gray-900 focus:border-primary focus:ring-2 focus:ring-primary/15 focus:outline-none transition"
                                    />
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                    Votre message
                                </label>
                                <textarea
                                    v-model="contactForm.message"
                                    rows="4"
                                    required
                                    placeholder="Décrivez votre demande avec le plus de précisions possible..."
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50/70 focus:bg-white py-2.5 px-3.5 text-xs text-gray-900 focus:border-primary focus:ring-2 focus:ring-primary/15 focus:outline-none transition resize-none"
                                ></textarea>
                            </div>

                            <button
                                type="submit"
                                :disabled="isSubmitting"
                                class="w-full bg-primary hover:bg-primary-hover disabled:opacity-60 text-white text-xs font-bold py-3.5 px-6 rounded-xl shadow-lg shadow-primary/25 transition-all duration-300 hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer"
                            >
                                <Send class="w-4 h-4" />
                                <span>{{ isSubmitting ? 'Envoi en cours...' : 'Envoyer au Support MISEGA' }}</span>
                            </button>
                        </form>

                        <!-- Engagements d'assistance -->
                        <div class="pt-4 border-t border-gray-100 grid grid-cols-2 gap-3 text-[11px] text-gray-500">
                            <div class="flex items-center gap-2">
                                <Clock class="w-4 h-4 text-primary shrink-0" />
                                <span>Réponse sous 24h ouvrées</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <ShieldCheck class="w-4 h-4 text-primary shrink-0" />
                                <span>Garantie tome intact</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </MisegaLayout>
</template>

<style scoped>
/* Accordéon fluide et mesuré (Règle n°5) */
.faq-collapse-grid {
    display: grid;
    grid-template-rows: 0fr;
    transition: grid-template-rows 0.55s cubic-bezier(0.16, 1, 0.3, 1);
}

.faq-collapse-grid.is-open {
    grid-template-rows: 1fr;
}
</style>

