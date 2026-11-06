<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { useCartStore } from '@/stores/useCartStore';
import { ShoppingBag, Search, X, LogOut, Package, ChevronRight } from '@lucide/vue';

const cartStore = useCartStore();
const page = usePage();
const user = computed(() => page.props.auth?.user);

const isHovered = ref(false);
const isMobileOpen = ref(false);
const isVisible = ref(true);
const searchQuery = ref('');
const isSearchFocused = ref(false);
const navbarRef = ref(null);

let closeTimeout = null;
let lastScrollY = 0;
const scrollThreshold = 8;

// Scroll detection: retract when scrolling DOWN, reveal when scrolling UP
const handleScroll = () => {
    if (typeof window === 'undefined') return;
    const currentScrollY = window.scrollY || window.pageYOffset;

    // Always visible at the top of page
    if (currentScrollY <= 30) {
        isVisible.value = true;
        lastScrollY = currentScrollY;
        return;
    }

    // Scrolling down -> retract
    if (currentScrollY > lastScrollY + scrollThreshold) {
        if (!isSearchFocused.value) {
            isVisible.value = false;
            isHovered.value = false;
            isMobileOpen.value = false;
        }
    } 
    // Scrolling up -> reveal
    else if (currentScrollY < lastScrollY - scrollThreshold) {
        isVisible.value = true;
    }

    lastScrollY = currentScrollY;
};

// Desktop hover handlers with grace period
const handleMouseEnter = () => {
    if (closeTimeout) clearTimeout(closeTimeout);
    isHovered.value = true;
};

const handleMouseLeave = () => {
    closeTimeout = setTimeout(() => {
        if (!isSearchFocused.value) {
            isHovered.value = false;
        }
    }, 240); // 240ms grace buffer for fluid handling
};

// Mobile toggle
const toggleMobile = () => {
    isMobileOpen.value = !isMobileOpen.value;
};

const closeMobile = () => {
    isMobileOpen.value = false;
};

// Search action
const handleSearch = () => {
    if (searchQuery.value.trim()) {
        router.get('/catalogue', { search: searchQuery.value.trim() });
        isHovered.value = false;
        isMobileOpen.value = false;
    }
};

// Click outside handler
const handleClickOutside = (e) => {
    if (navbarRef.value && !navbarRef.value.contains(e.target)) {
        isHovered.value = false;
        isMobileOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    window.addEventListener('scroll', handleScroll, { passive: true });
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    window.removeEventListener('scroll', handleScroll);
    if (closeTimeout) clearTimeout(closeTimeout);
});

// Computed active state
const isOpen = computed(() => isHovered.value || isMobileOpen.value);
</script>

<template>
    <!-- Floating Top Container with Scroll Retract -->
    <header 
        ref="navbarRef"
        class="fixed top-4 sm:top-6 left-1/2 -translate-x-1/2 z-50 pointer-events-auto transition-all duration-700 ease-[cubic-bezier(0.16,1,0.3,1)]"
        :class="[
            isVisible 
                ? 'translate-y-0 opacity-100 pointer-events-auto' 
                : '-translate-y-24 opacity-0 pointer-events-none'
        ]"
        @mouseenter="handleMouseEnter"
        @mouseleave="handleMouseLeave"
    >
        <!-- ─── DESKTOP LIQUID GLASS CAPSULE (Continuous Fluid Morphing) ─── -->
        <nav 
            class="hidden md:flex items-center h-[60px] rounded-full overflow-hidden liquid-glass-surface relative cursor-pointer select-none transform-gpu"
            :class="[
                isOpen 
                    ? 'w-[750px] lg:w-[820px] px-4 shadow-2xl scale-100' 
                    : 'w-[60px] px-0 shadow-lg hover:scale-105'
            ]"
            style="transition: width 0.65s cubic-bezier(0.16, 1, 0.3, 1), transform 0.65s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s ease;"
        >
            <!-- 3D Glass Specular Reflection Highlight -->
            <div class="glass-specular-glare"></div>

            <!-- 1. Expanded Full Navigation Row (Persists in DOM for 100% fluid interpolation) -->
            <div 
                class="flex items-center justify-between w-full min-w-[720px] lg:min-w-[790px] transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]"
                :class="isOpen ? 'opacity-100 translate-x-0 pointer-events-auto' : 'opacity-0 -translate-x-6 pointer-events-none'"
            >
                <!-- Left: Home Logo Anchor -->
                <Link 
                    href="/" 
                    class="flex items-center gap-2.5 shrink-0 group px-2 py-1 rounded-full hover:bg-black/[0.04] transition"
                    title="Accueil MISEGA"
                >
                    <img 
                        src="/img/misega-icon-book-black-red.png" 
                        alt="MISEGA" 
                        class="h-7 w-7 object-contain transition-transform duration-300 group-hover:rotate-[-6deg] drop-shadow-[0_2px_6px_rgba(188,0,45,0.4)]"
                    />
                    <span class="font-gang text-lg font-black tracking-wider text-black">
                        MI<span class="text-primary">SEGA</span>
                    </span>
                </Link>

                <!-- Center: Navigation Links -->
                <div class="flex items-center space-x-1 text-xs font-bold tracking-wide">
                    <Link
                        href="/catalogue"
                        class="px-3 py-1.5 rounded-full transition-all"
                        :class="page.url.startsWith('/catalogue') || page.url.startsWith('/catalog') ? 'bg-primary text-white shadow-sm shadow-primary/30' : 'text-gray-800 hover:text-primary hover:bg-black/[0.04]'"
                    >
                        Catalogue
                    </Link>
                    <Link
                        href="/catalogue?genre=Shonen"
                        class="px-3 py-1.5 rounded-full text-gray-800 hover:text-primary hover:bg-black/[0.04] transition-all font-sans"
                    >
                        Shōnen
                    </Link>
                    <Link
                        href="/catalogue?genre=Seinen"
                        class="px-3 py-1.5 rounded-full text-gray-800 hover:text-primary hover:bg-black/[0.04] transition-all"
                    >
                        Seinen
                    </Link>
                </div>

                <!-- Search Input Pill -->
                <div class="relative flex-1 max-w-[190px] lg:max-w-[220px]">
                    <input
                        v-model="searchQuery"
                        @keyup.enter="handleSearch"
                        @focus="isSearchFocused = true"
                        @blur="isSearchFocused = false"
                        type="text"
                        placeholder="Rechercher..."
                        class="w-full bg-white/60 hover:bg-white/80 focus:bg-white text-xs text-gray-900 placeholder-gray-500 rounded-full py-1.5 pl-8 pr-3 border border-primary/25 focus:border-primary focus:outline-none transition-all shadow-inner"
                    />
                    <Search 
                        @click="handleSearch" 
                        class="w-3.5 h-3.5 text-gray-500 absolute left-2.5 top-2 cursor-pointer hover:text-primary transition" 
                    />
                </div>

                <!-- Right: Cart & User Actions -->
                <div class="flex items-center gap-2 shrink-0">
                    <!-- Cart Button -->
                    <Link
                        href="/cart"
                        class="relative p-2 rounded-full bg-black/[0.04] hover:bg-primary hover:text-white text-gray-800 transition flex items-center justify-center group"
                        title="Panier"
                    >
                        <ShoppingBag class="w-4 h-4 transition-transform group-hover:scale-110" />
                        <span
                            v-if="cartStore.totalItems > 0"
                            class="absolute -top-1 -right-1 bg-primary text-white text-[9px] font-black w-4 h-4 rounded-full flex items-center justify-center border-2 border-white shadow-sm"
                        >
                            {{ cartStore.totalItems }}
                        </span>
                    </Link>

                    <!-- Authenticated User -->
                    <div v-if="user" class="flex items-center gap-1.5">
                        <Link
                            href="/orders"
                            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full bg-black/[0.04] hover:bg-black/[0.08] text-gray-800 transition"
                            title="Mes Commandes"
                        >
                            <Package class="w-3.5 h-3.5 text-primary" />
                            <span class="hidden lg:inline">Commandes</span>
                        </Link>
                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            class="p-2 rounded-full bg-black/[0.04] hover:bg-red-50 text-gray-500 hover:text-red-600 transition"
                            title="Déconnexion"
                        >
                            <LogOut class="w-3.5 h-3.5" />
                        </Link>
                    </div>

                    <!-- Guest / Login -->
                    <div v-else class="flex items-center gap-1">
                        <Link
                            href="/login"
                            class="text-xs font-bold text-gray-800 hover:text-primary px-2.5 py-1.5 rounded-full hover:bg-black/[0.04] transition"
                        >
                            Connexion
                        </Link>
                        <Link
                            href="/register"
                            class="text-xs font-bold text-white bg-primary hover:bg-primary-hover px-3 py-1.5 rounded-full shadow-sm shadow-primary/30 transition hover:scale-105 active:scale-95"
                        >
                            Rejoindre
                        </Link>
                    </div>
                </div>
            </div>

            <!-- 2. Closed State Central Orb Icon (Smoothly fades & scales out without DOM unmount) -->
            <div 
                class="absolute inset-0 flex items-center justify-center pointer-events-none transition-all duration-400 ease-[cubic-bezier(0.16,1,0.3,1)]"
                :class="isOpen ? 'opacity-0 scale-75' : 'opacity-100 scale-100'"
            >
                <img 
                    src="/img/misega-icon-book-black-red.png" 
                    alt="MISEGA" 
                    class="h-8 w-8 object-contain drop-shadow-[0_2px_8px_rgba(188,0,45,0.45)]"
                />
                <span 
                    v-if="cartStore.totalItems > 0"
                    class="absolute top-2 right-2 w-3.5 h-3.5 bg-primary rounded-full border-2 border-white animate-pulse"
                ></span>
            </div>
        </nav>

        <!-- ─── MOBILE LIQUID GLASS MORPHING NAVBAR ─── -->
        <div class="md:hidden">
            <!-- Closed Static Orb -->
            <button 
                v-if="!isMobileOpen"
                @click="toggleMobile"
                class="w-[54px] h-[54px] rounded-full liquid-glass-surface flex items-center justify-center relative cursor-pointer shadow-xl active:scale-95 transition-transform"
                aria-label="Ouvrir le menu de navigation"
            >
                <div class="glass-specular-glare"></div>
                <img 
                    src="/img/misega-icon-book-black-red.png" 
                    alt="MISEGA" 
                    class="h-7 w-7 object-contain drop-shadow-[0_2px_6px_rgba(188,0,45,0.45)]"
                />
                <span 
                    v-if="cartStore.totalItems > 0"
                    class="absolute -top-0.5 -right-0.5 w-3.5 h-3.5 bg-primary rounded-full border-2 border-white animate-bounce"
                ></span>
            </button>

            <!-- Expanded Mobile Bubble Dropdown -->
            <div 
                v-else
                class="w-[92vw] max-w-sm liquid-glass-surface rounded-3xl p-4 shadow-2xl animate-scale-up space-y-4 relative overflow-hidden"
            >
                <div class="glass-specular-glare"></div>

                <!-- Header of Bubble -->
                <div class="flex items-center justify-between pb-2 border-b border-black/[0.06] relative z-10">
                    <Link href="/" @click="closeMobile" class="flex items-center gap-2">
                        <img 
                            src="/img/misega-icon-book-black-red.png" 
                            alt="MISEGA" 
                            class="h-7 w-7 object-contain"
                        />
                        <span class="font-gang text-lg font-black text-black">MI<span class="text-primary">SEGA</span></span>
                    </Link>

                    <div class="flex items-center gap-2">
                        <Link 
                            href="/cart" 
                            @click="closeMobile"
                            class="relative p-2 rounded-full bg-black/[0.04] text-gray-700"
                        >
                            <ShoppingBag class="w-4 h-4" />
                            <span 
                                v-if="cartStore.totalItems > 0" 
                                class="absolute -top-1 -right-1 bg-primary text-white text-[9px] font-black w-4 h-4 rounded-full flex items-center justify-center border border-white"
                            >
                                {{ cartStore.totalItems }}
                            </span>
                        </Link>
                        <button 
                            @click="closeMobile"
                            class="p-2 rounded-full bg-black/[0.05] hover:bg-black/[0.1] text-gray-600 transition"
                            aria-label="Fermer le menu"
                        >
                            <X class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <!-- Search Input Mobile -->
                <div class="relative z-10">
                    <input
                        v-model="searchQuery"
                        @keyup.enter="handleSearch"
                        type="text"
                        placeholder="Rechercher un manga..."
                        class="w-full bg-white/60 text-xs text-gray-900 placeholder-gray-500 rounded-full py-2 pl-9 pr-3 border border-primary/20 focus:border-primary focus:outline-none"
                    />
                    <Search @click="handleSearch" class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" />
                </div>

                <!-- Navigation Links -->
                <div class="space-y-1 text-sm font-semibold text-gray-800 relative z-10">
                    <Link 
                        href="/catalogue" 
                        @click="closeMobile" 
                        class="flex items-center justify-between p-2.5 rounded-xl hover:bg-black/[0.04] transition"
                    >
                        <span>Catalogue Complet</span>
                        <ChevronRight class="w-4 h-4 text-gray-400" />
                    </Link>
                    <Link 
                        href="/catalogue?genre=Shonen" 
                        @click="closeMobile" 
                        class="flex items-center justify-between p-2.5 rounded-xl hover:bg-black/[0.04] transition font-sans"
                    >
                        <span>Shōnen</span>
                        <ChevronRight class="w-4 h-4 text-gray-400" />
                    </Link>
                    <Link 
                        href="/catalogue?genre=Seinen" 
                        @click="closeMobile" 
                        class="flex items-center justify-between p-2.5 rounded-xl hover:bg-black/[0.04] transition"
                    >
                        <span>Seinen</span>
                        <ChevronRight class="w-4 h-4 text-gray-400" />
                    </Link>
                    <div v-if="user">
                        <Link 
                            href="/orders" 
                            @click="closeMobile" 
                            class="flex items-center justify-between p-2.5 rounded-xl hover:bg-black/[0.04] transition text-primary font-bold"
                        >
                            <span>Mes Commandes</span>
                            <Package class="w-4 h-4 text-primary" />
                        </Link>
                    </div>
                </div>

                <!-- Auth Buttons Mobile -->
                <div class="pt-2 border-t border-black/[0.06] flex items-center gap-2 relative z-10">
                    <template v-if="user">
                        <Link 
                            href="/logout" 
                            method="post" 
                            as="button" 
                            class="w-full text-center py-2 text-xs font-bold text-red-600 bg-red-50 rounded-xl transition"
                        >
                            Déconnexion
                        </Link>
                    </template>
                    <template v-else>
                        <Link 
                            href="/login" 
                            @click="closeMobile"
                            class="flex-1 text-center py-2 text-xs font-bold text-gray-700 bg-black/[0.04] rounded-xl transition"
                        >
                            Connexion
                        </Link>
                        <Link 
                            href="/register" 
                            @click="closeMobile"
                            class="flex-1 text-center py-2 text-xs font-bold text-white bg-primary rounded-xl shadow-sm transition"
                        >
                            Inscription
                        </Link>
                    </template>
                </div>
            </div>
        </div>
    </header>
</template>

<style scoped>
/* ─── 3D Liquid Glass Materiality (Haute Transparence & Définition Nette) ─── */
.liquid-glass-surface {
    position: relative;

    /* Base translucide hautement cristalline avec nuance rose-quartz délicate */
    background: linear-gradient(
        135deg,
        rgba(255, 255, 255, 0.40) 0%,
        rgba(255, 240, 244, 0.18) 45%,
        rgba(255, 228, 235, 0.14) 75%,
        rgba(255, 250, 252, 0.35) 100%
    );

    /* Réfraction optique pure : contraste, saturation & netteté de lentille */
    backdrop-filter: blur(18px) saturate(220%) contrast(106%) brightness(103%);
    -webkit-backdrop-filter: blur(18px) saturate(220%) contrast(106%) brightness(103%);

    /* Arêtes biseautées haute définition pour détacher nettement le verre du fond clair */
    border: 1px solid rgba(255, 255, 255, 0.85);
    border-top: 1.5px solid #ffffff;
    border-bottom: 2px solid rgba(188, 0, 45, 0.38);
    border-right: 1.5px solid rgba(188, 0, 45, 0.22);
    border-left: 1.5px solid rgba(255, 255, 255, 0.9);

    /* Caustiques liquides, reflets internes rosés et ombre de contact volumétrique */
    box-shadow: 
        inset 0 2px 4px rgba(255, 255, 255, 0.95),
        inset 0 -2px 6px rgba(188, 0, 45, 0.14),
        inset 0 0 16px rgba(255, 215, 225, 0.45),
        0 20px 42px -10px rgba(188, 0, 45, 0.18),
        0 6px 18px -4px rgba(0, 0, 0, 0.08),
        0 0 0 1px rgba(255, 255, 255, 0.6);
}

/* Éclat spéculaire permanent simulant la courbure convexe du verre 3D */
.glass-specular-glare {
    position: absolute;
    inset: 0;
    border-radius: inherit;
    pointer-events: none;
    background: radial-gradient(
        ellipse 80% 50% at 50% 12%,
        rgba(255, 255, 255, 0.70) 0%,
        rgba(255, 255, 255, 0.18) 40%,
        transparent 75%
    );
    opacity: 0.95;
    z-index: 1;
}

@keyframes scaleUp {
    from {
        opacity: 0;
        transform: scale(0.92) translateY(-10px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.animate-scale-up {
    animation: scaleUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
