<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { useCartStore } from '@/stores/useCartStore';
import { ShoppingBag, Search, X, LogOut, Package, User, ChevronRight } from '@lucide/vue';

const cartStore = useCartStore();
const page = usePage();
const user = computed(() => page.props.auth?.user);

const isHovered = ref(false);
const isMobileOpen = ref(false);
const searchQuery = ref('');
const isSearchFocused = ref(false);
const navbarRef = ref(null);

let closeTimeout = null;

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
    }, 220); // 220ms grace buffer for fluid handling
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
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    if (closeTimeout) clearTimeout(closeTimeout);
});

// Computed active state
const isOpen = computed(() => isHovered.value || isMobileOpen.value);
</script>

<template>
    <!-- Floating Top Container -->
    <header 
        ref="navbarRef"
        class="fixed top-4 sm:top-6 left-1/2 -translate-x-1/2 z-50 pointer-events-auto"
        @mouseenter="handleMouseEnter"
        @mouseleave="handleMouseLeave"
    >
        <!-- ─── DESKTOP LIQUID GLASS CAPSULE ─── -->
        <nav 
            class="hidden md:flex items-center justify-between transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] liquid-glass-surface"
            :class="[
                isOpen 
                    ? 'w-[740px] lg:w-[820px] h-[60px] px-4 rounded-full shadow-2xl scale-100' 
                    : 'w-[58px] h-[58px] p-0 rounded-full shadow-lg hover:scale-105'
            ]"
        >
            <!-- ─── Static / Closed State: Book Icon in Liquid Glass Orb ─── -->
            <div 
                v-if="!isOpen" 
                class="w-full h-full flex items-center justify-center cursor-pointer select-none relative group"
                title="Ouvrir le menu MISEGA"
            >
                <!-- Central Misega Book Icon -->
                <img 
                    src="/img/misega-icon-book-only-transparent.png" 
                    alt="MISEGA" 
                    class="h-8 w-8 object-contain transition-transform duration-300 group-hover:scale-115 drop-shadow-[0_2px_8px_rgba(188,0,45,0.35)]"
                />

                <!-- Prismatic Specular Sheen Reflex -->
                <div class="absolute inset-0 rounded-full bg-gradient-to-tr from-white/0 via-white/50 to-white/0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>

                <!-- Subtle Pulse Ring if items in cart -->
                <span 
                    v-if="cartStore.totalItems > 0"
                    class="absolute -top-0.5 -right-0.5 w-3.5 h-3.5 bg-primary rounded-full border-2 border-white animate-pulse"
                ></span>
            </div>

            <!-- ─── Expanded State: Full Fluid Navigation Bar ─── -->
            <div 
                v-else 
                class="w-full flex items-center justify-between gap-3 animate-fade-in"
            >
                <!-- Left: Home Logo Anchor -->
                <Link 
                    href="/" 
                    class="flex items-center gap-2.5 shrink-0 group px-2 py-1 rounded-full hover:bg-black/[0.04] transition"
                    title="Accueil MISEGA"
                >
                    <img 
                        src="/img/misega-icon-book-only-transparent.png" 
                        alt="MISEGA" 
                        class="h-7 w-7 object-contain transition-transform duration-300 group-hover:rotate-[-6deg] drop-shadow-[0_2px_6px_rgba(188,0,45,0.4)]"
                    />
                    <span class="font-gang text-lg font-black tracking-wider text-gray-900">
                        MI<span class="text-primary">SEGA</span>
                    </span>
                </Link>

                <!-- Center: Navigation Links -->
                <div class="flex items-center space-x-1 text-xs font-bold tracking-wide">
                    <Link
                        href="/catalogue"
                        class="px-3 py-1.5 rounded-full transition-all"
                        :class="page.url.startsWith('/catalogue') || page.url.startsWith('/catalog') ? 'bg-primary text-white shadow-sm shadow-primary/30' : 'text-gray-700 hover:text-gray-900 hover:bg-black/[0.04]'"
                    >
                        Catalogue
                    </Link>
                    <Link
                        href="/catalogue?genre=Shonen"
                        class="px-3 py-1.5 rounded-full text-gray-700 hover:text-gray-900 hover:bg-black/[0.04] transition-all font-sans"
                    >
                        Shōnen
                    </Link>
                    <Link
                        href="/catalogue?genre=Seinen"
                        class="px-3 py-1.5 rounded-full text-gray-700 hover:text-gray-900 hover:bg-black/[0.04] transition-all"
                    >
                        Seinen
                    </Link>
                </div>

                <!-- Search Input Pill -->
                <div class="relative flex-1 max-w-[200px] lg:max-w-[230px]">
                    <input
                        v-model="searchQuery"
                        @keyup.enter="handleSearch"
                        @focus="isSearchFocused = true"
                        @blur="isSearchFocused = false"
                        type="text"
                        placeholder="Rechercher..."
                        class="w-full bg-black/[0.05] hover:bg-black/[0.08] focus:bg-white text-xs text-gray-900 placeholder-gray-500 rounded-full py-1.5 pl-8 pr-3 border border-black/[0.06] focus:border-primary/50 focus:outline-none transition-all shadow-inner"
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
                        class="relative p-2 rounded-full bg-black/[0.04] hover:bg-primary hover:text-white text-gray-700 transition flex items-center justify-center group"
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
                            class="text-xs font-bold text-gray-700 hover:text-gray-900 px-2.5 py-1.5 rounded-full hover:bg-black/[0.04] transition"
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
        </nav>

        <!-- ─── MOBILE LIQUID GLASS MORPHING NAVBAR ─── -->
        <div class="md:hidden">
            <!-- Closed Static Orb -->
            <button 
                v-if="!isMobileOpen"
                @click="toggleMobile"
                class="w-[52px] h-[52px] rounded-full liquid-glass-surface flex items-center justify-center relative cursor-pointer shadow-xl active:scale-95 transition-transform"
                aria-label="Ouvrir le menu de navigation"
            >
                <img 
                    src="/img/misega-icon-book-only-transparent.png" 
                    alt="MISEGA" 
                    class="h-7 w-7 object-contain drop-shadow-[0_2px_6px_rgba(188,0,45,0.4)]"
                />
                <span 
                    v-if="cartStore.totalItems > 0"
                    class="absolute -top-0.5 -right-0.5 w-3.5 h-3.5 bg-primary rounded-full border-2 border-white animate-bounce"
                ></span>
            </button>

            <!-- Expanded Mobile Bubble Dropdown -->
            <div 
                v-else
                class="w-[92vw] max-w-sm liquid-glass-surface rounded-3xl p-4 shadow-2xl animate-scale-up space-y-4"
            >
                <!-- Header of Bubble -->
                <div class="flex items-center justify-between pb-2 border-b border-black/[0.06]">
                    <Link href="/" @click="closeMobile" class="flex items-center gap-2">
                        <img 
                            src="/img/misega-icon-book-only-transparent.png" 
                            alt="MISEGA" 
                            class="h-7 w-7 object-contain"
                        />
                        <span class="font-gang text-lg font-black text-gray-900">MI<span class="text-primary">SEGA</span></span>
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
                <div class="relative">
                    <input
                        v-model="searchQuery"
                        @keyup.enter="handleSearch"
                        type="text"
                        placeholder="Rechercher un manga..."
                        class="w-full bg-black/[0.05] text-xs text-gray-900 placeholder-gray-500 rounded-full py-2 pl-9 pr-3 border border-black/[0.06] focus:border-primary focus:outline-none"
                    />
                    <Search @click="handleSearch" class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" />
                </div>

                <!-- Navigation Links -->
                <div class="space-y-1 text-sm font-semibold text-gray-800">
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
                <div class="pt-2 border-t border-black/[0.06] flex items-center gap-2">
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
/* ─── 3D Liquid Glass Materiality ─── */
.liquid-glass-surface {
    /* Multi-layered translucent crystal foundation */
    background: linear-gradient(
        135deg,
        rgba(255, 255, 255, 0.72) 0%,
        rgba(255, 255, 255, 0.45) 50%,
        rgba(255, 255, 255, 0.60) 100%
    );
    backdrop-filter: blur(28px) saturate(180%);
    -webkit-backdrop-filter: blur(28px) saturate(180%);

    /* 3D Convex bevel and refractive rim lighting */
    border: 1px solid rgba(255, 255, 255, 0.85);
    border-top: 1.5px solid rgba(255, 255, 255, 0.98);
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);

    /* Liquid volume depth shadows & caustics */
    box-shadow: 
        inset 0 2px 4px rgba(255, 255, 255, 0.95),
        inset 0 -2px 4px rgba(0, 0, 0, 0.04),
        inset 0 0 16px rgba(255, 255, 255, 0.5),
        0 16px 36px -10px rgba(0, 0, 0, 0.12),
        0 4px 12px rgba(0, 0, 0, 0.04);
}

/* Fluid entrance animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.96);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-fade-in {
    animation: fadeIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes scaleUp {
    from {
        opacity: 0;
        transform: scale(0.9) translateY(-10px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.animate-scale-up {
    animation: scaleUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>

