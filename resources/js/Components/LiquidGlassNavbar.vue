<script setup>
/**
 * LiquidGlassNavbar — Barre de navigation 3D en Verre Liquide (60 FPS Pur GPU)
 *
 * Correctifs clés :
 *  1. Circonférence uniforme : bordure continue sur 360° (plus aucun trait rouge coupé sur les arrondis).
 *  2. Alignement symétrique : largeur calculée automatiquement sur le contenu réel (`scrollWidth`)
 *     avec marge équilibrée à gauche et à droite pour que « Rejoindre » épouse l'arrondi droit sans déborder.
 *  3. 60 FPS constant :
 *     - suppression des `filter: blur()` animés qui saturaient le GPU avec `backdrop-filter`
 *     - suppression des re-rendus Vue dans `requestAnimationFrame` (variables CSS directes)
 *     - courbe d'ouverture continue et soyeuse (Règle n°5)
 */
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { useCartStore } from '@/stores/useCartStore';
import { ShoppingBag, Search, X, LogOut, Package, ChevronRight } from '@lucide/vue';
import { gsap } from 'gsap';

const cartStore = useCartStore();
const page = usePage();
const user = computed(() => page.props.auth?.user);

const isHovered = ref(false);
const isMobileOpen = ref(false);
const isVisible = ref(true);
const searchQuery = ref('');
const isSearchFocused = ref(false);

const navbarRef = ref(null);
const capsuleRef = ref(null);
const contentRowRef = ref(null);
const brandTextRef = ref(null);
const navItemsRef = ref(null);
const searchBoxRef = ref(null);
const actionsRef = ref(null);
const closedBadgeRef = ref(null);

let morphTl = null;
let closeTimeout = null;
let lastScrollY = 0;
const scrollThreshold = 8;
const ORB_SIZE = 60;

// Suivi spéculaire 60fps sans déclencher le Virtual DOM de Vue
let glareRaf = null;
let targetGlareX = 50;
let targetGlareY = 16;
let currentGlareX = 50;
let currentGlareY = 16;

const tickGlare = () => {
    const dx = targetGlareX - currentGlareX;
    const dy = targetGlareY - currentGlareY;

    if (Math.abs(dx) > 0.08 || Math.abs(dy) > 0.08) {
        currentGlareX += dx * 0.12;
        currentGlareY += dy * 0.12;
        if (capsuleRef.value) {
            capsuleRef.value.style.setProperty('--gx', `${currentGlareX.toFixed(1)}%`);
            capsuleRef.value.style.setProperty('--gy', `${currentGlareY.toFixed(1)}%`);
        }
        glareRaf = requestAnimationFrame(tickGlare);
    } else {
        glareRaf = null;
    }
};

const handleCapsuleMouseMove = (e) => {
    if (!capsuleRef.value) return;
    const rect = capsuleRef.value.getBoundingClientRect();
    targetGlareX = Math.max(10, Math.min(90, ((e.clientX - rect.left) / rect.width) * 100));
    targetGlareY = Math.max(8, Math.min(85, ((e.clientY - rect.top) / rect.height) * 100));
    if (!glareRaf) {
        glareRaf = requestAnimationFrame(tickGlare);
    }
};

// ─── Timeline GSAP 60 FPS (uniquement transform3d, opacity et width unique) ──
const buildMorphTimeline = () => {
    if (!capsuleRef.value || !contentRowRef.value) return;
    if (morphTl) morphTl.kill();

    // Mesure exacte de la largeur nécessaire au contenu (+ marge droite confortable)
    const naturalWidth = contentRowRef.value.scrollWidth;
    const maxViewportWidth = typeof window !== 'undefined' ? window.innerWidth - 32 : 900;
    const targetWidth = Math.min(Math.max(naturalWidth, 780), maxViewportWidth);

    morphTl = gsap.timeline({
        paused: true,
        defaults: { overwrite: 'auto' },
    });

    // 1. Étirement fluide et continu du cercle en pilule
    morphTl.fromTo(
        capsuleRef.value,
        { width: ORB_SIZE },
        {
            width: targetWidth,
            duration: 0.72,
            ease: 'power3.inOut',
        },
        0
    );

    // 2. Pastille panier de l'orbe fermé
    if (closedBadgeRef.value) {
        morphTl.to(
            closedBadgeRef.value,
            { autoAlpha: 0, scale: 0.5, duration: 0.2, ease: 'power2.out' },
            0
        );
    }

    // 3. Texte MISEGA qui émerge à droite de l'icône du livre
    if (brandTextRef.value) {
        morphTl.fromTo(
            brandTextRef.value,
            { autoAlpha: 0, x: -10 },
            { autoAlpha: 1, x: 0, duration: 0.45, ease: 'power2.out' },
            0.16
        );
    }

    // 4. Liens de navigation centraux
    if (navItemsRef.value) {
        morphTl.fromTo(
            navItemsRef.value.children,
            { autoAlpha: 0, y: 6 },
            {
                autoAlpha: 1,
                y: 0,
                duration: 0.42,
                stagger: 0.045,
                ease: 'power2.out',
            },
            0.22
        );
    }

    // 5. Champ de recherche
    if (searchBoxRef.value) {
        morphTl.fromTo(
            searchBoxRef.value,
            { autoAlpha: 0, x: -8, scale: 0.96 },
            { autoAlpha: 1, x: 0, scale: 1, duration: 0.45, ease: 'power2.out' },
            0.28
        );
    }

    // 6. Actions à droite (Panier, Connexion, Rejoindre)
    if (actionsRef.value) {
        morphTl.fromTo(
            actionsRef.value,
            { autoAlpha: 0, x: -8 },
            { autoAlpha: 1, x: 0, duration: 0.45, ease: 'power2.out' },
            0.34
        );
    }
};

const openCapsule = () => {
    if (closeTimeout) clearTimeout(closeTimeout);
    isHovered.value = true;
    if (morphTl) morphTl.timeScale(1).play();
};

const closeCapsule = () => {
    closeTimeout = setTimeout(() => {
        if (!isSearchFocused.value) {
            isHovered.value = false;
            targetGlareX = 50;
            targetGlareY = 16;
            if (!glareRaf) glareRaf = requestAnimationFrame(tickGlare);
            if (morphTl) morphTl.timeScale(1.1).reverse();
        }
    }, 220);
};

// ─── Retrait au scroll vers le bas ───────────────────────────────────────────
const handleScroll = () => {
    if (typeof window === 'undefined') return;
    const currentScrollY = window.scrollY || window.pageYOffset;

    if (currentScrollY <= 30) {
        isVisible.value = true;
        lastScrollY = currentScrollY;
        return;
    }

    if (currentScrollY > lastScrollY + scrollThreshold) {
        if (!isSearchFocused.value) {
            isVisible.value = false;
            isHovered.value = false;
            isMobileOpen.value = false;
            if (morphTl) morphTl.timeScale(1.25).reverse();
        }
    } else if (currentScrollY < lastScrollY - scrollThreshold) {
        isVisible.value = true;
    }

    lastScrollY = currentScrollY;
};

const toggleMobile = () => {
    isMobileOpen.value = !isMobileOpen.value;
};

const closeMobile = () => {
    isMobileOpen.value = false;
};

const handleSearch = () => {
    if (searchQuery.value.trim()) {
        router.get('/catalogue', { search: searchQuery.value.trim() });
        isHovered.value = false;
        isMobileOpen.value = false;
        if (morphTl) morphTl.reverse();
    }
};

const handleSearchBlur = () => {
    isSearchFocused.value = false;
    if (!isHovered.value && morphTl) {
        closeCapsule();
    }
};

const handleClickOutside = (e) => {
    if (navbarRef.value && !navbarRef.value.contains(e.target)) {
        isHovered.value = false;
        isMobileOpen.value = false;
        if (morphTl) morphTl.reverse();
    }
};

const handleResize = () => {
    const wasOpen = isHovered.value;
    buildMorphTimeline();
    if (wasOpen && morphTl) {
        morphTl.progress(1);
    }
};

onMounted(async () => {
    await nextTick();
    buildMorphTimeline();
    if (document.fonts?.ready) {
        document.fonts.ready.then(() => handleResize());
    }
    document.addEventListener('click', handleClickOutside);
    window.addEventListener('scroll', handleScroll, { passive: true });
    window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
    if (glareRaf) cancelAnimationFrame(glareRaf);
    if (morphTl) morphTl.kill();
    document.removeEventListener('click', handleClickOutside);
    window.removeEventListener('scroll', handleScroll);
    window.removeEventListener('resize', handleResize);
    if (closeTimeout) clearTimeout(closeTimeout);
});
</script>

<template>
    <header
        ref="navbarRef"
        class="fixed top-4 sm:top-6 left-1/2 -translate-x-1/2 z-50 pointer-events-auto transition-all duration-700 ease-[cubic-bezier(0.16,1,0.3,1)]"
        :class="[
            isVisible
                ? 'translate-y-0 opacity-100 pointer-events-auto'
                : '-translate-y-24 opacity-0 pointer-events-none'
        ]"
        @mouseenter="openCapsule"
        @mouseleave="closeCapsule"
    >
        <!-- ═══════════════════════════════════════════════════════════════════
             DESKTOP : SPHÈRE 3D LIQUID GLASS → PILULE (60 FPS)
             ═══════════════════════════════════════════════════════════════════ -->
        <nav
            ref="capsuleRef"
            @mousemove="handleCapsuleMouseMove"
            class="hidden md:flex items-center h-[60px] w-[60px] rounded-full overflow-hidden liquid-glass-orb relative select-none"
            style="--gx: 50%; --gy: 16%; will-change: width;"
        >
            <!-- Anneau interne de réfraction 360° uniforme -->
            <div class="glass-inner-ring"></div>

            <!-- Reflet spéculaire 3D piloté par variables CSS (--gx, --gy) -->
            <div class="glass-interactive-glare"></div>

            <!-- Dôme lumineux supérieur (courbure convexe de la goutte) -->
            <div class="glass-top-meniscus"></div>

            <!-- Rangée intérieure unifiée : largeur mesurée dynamiquement -->
            <div
                ref="contentRowRef"
                class="relative z-10 flex items-center h-full w-max pr-3.5"
            >
                <!-- Pôle Gauche : 57px × 57px correspond exactement au diamètre interne (60px - 2×1.5px de bordure) -->
                <Link
                    href="/"
                    class="flex items-center shrink-0 h-full group focus:outline-none"
                    title="Accueil MISEGA"
                >
                    <div class="w-[57px] h-[57px] flex items-center justify-center shrink-0 relative">
                        <img
                            src="/img/misega-icon-book-black-red.png"
                            alt="MISEGA"
                            class="h-7 w-7 object-contain block transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:scale-110 group-hover:rotate-[-5deg] drop-shadow-[0_2px_8px_rgba(188,0,45,0.35)]"
                        />
                        <span
                            ref="closedBadgeRef"
                            v-show="cartStore.totalItems > 0"
                            class="absolute top-2.5 right-2.5 w-3 h-3 bg-primary rounded-full border-2 border-white shadow-sm"
                        ></span>
                    </div>

                    <span
                        ref="brandTextRef"
                        class="font-gang text-lg font-black tracking-wider text-gray-950 pr-4 opacity-0 invisible whitespace-nowrap"
                    >
                        MI<span class="text-primary">SEGA</span>
                    </span>
                </Link>

                <!-- Liens de navigation -->
                <div
                    ref="navItemsRef"
                    class="flex items-center space-x-1 text-xs font-bold tracking-wide shrink-0"
                >
                    <Link
                        href="/catalogue"
                        class="px-3.5 py-1.5 rounded-full transition-colors duration-300 opacity-0 invisible whitespace-nowrap"
                        :class="
                            page.url.startsWith('/catalogue') || page.url.startsWith('/catalog')
                                ? 'bg-primary text-white shadow-sm shadow-primary/30'
                                : 'text-gray-900 hover:text-primary hover:bg-white/60'
                        "
                    >
                        Catalogue
                    </Link>
                    <Link
                        href="/catalogue?genre=Shonen"
                        class="px-3.5 py-1.5 rounded-full text-gray-900 hover:text-primary hover:bg-white/60 transition-colors duration-300 font-sans opacity-0 invisible whitespace-nowrap"
                    >
                        Shōnen
                    </Link>
                    <Link
                        href="/catalogue?genre=Seinen"
                        class="px-3.5 py-1.5 rounded-full text-gray-900 hover:text-primary hover:bg-white/60 transition-colors duration-300 opacity-0 invisible whitespace-nowrap"
                    >
                        Seinen
                    </Link>
                </div>

                <!-- Barre de recherche -->
                <div
                    ref="searchBoxRef"
                    class="relative w-[180px] lg:w-[205px] shrink-0 mx-3.5 opacity-0 invisible"
                >
                    <input
                        v-model="searchQuery"
                        @keyup.enter="handleSearch"
                        @focus="isSearchFocused = true"
                        @blur="handleSearchBlur"
                        type="text"
                        placeholder="Rechercher..."
                        class="w-full glass-inner-input text-xs text-gray-900 placeholder-gray-500 rounded-full py-1.5 pl-8 pr-3 focus:outline-none transition-all duration-300"
                    />
                    <Search
                        @click="handleSearch"
                        class="w-3.5 h-3.5 text-gray-500 absolute left-2.5 top-2 cursor-pointer hover:text-primary transition"
                    />
                </div>

                <!-- Actions à droite (avec padding droit de sécurité sur le parent pour ne jamais déborder) -->
                <div
                    ref="actionsRef"
                    class="flex items-center gap-2 shrink-0 opacity-0 invisible"
                >
                    <Link
                        href="/cart"
                        class="relative p-2 rounded-full glass-inner-pill hover:bg-primary hover:text-white text-gray-900 transition-all duration-300 flex items-center justify-center group"
                        title="Panier"
                    >
                        <ShoppingBag class="w-4 h-4 transition-transform duration-300 group-hover:scale-110" />
                        <span
                            v-if="cartStore.totalItems > 0"
                            class="absolute -top-1 -right-1 bg-primary text-white text-[9px] font-black w-4 h-4 rounded-full flex items-center justify-center border-2 border-white shadow-sm"
                        >
                            {{ cartStore.totalItems }}
                        </span>
                    </Link>

                    <div v-if="user" class="flex items-center gap-1.5">
                        <Link
                            href="/orders"
                            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full glass-inner-pill text-gray-900 hover:text-primary transition whitespace-nowrap"
                            title="Mes Commandes"
                        >
                            <Package class="w-3.5 h-3.5 text-primary" />
                            <span class="hidden lg:inline">Commandes</span>
                        </Link>
                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            class="p-2 rounded-full glass-inner-pill text-gray-600 hover:text-red-600 transition"
                            title="Déconnexion"
                        >
                            <LogOut class="w-3.5 h-3.5" />
                        </Link>
                    </div>

                    <div v-else class="flex items-center gap-1.5">
                        <Link
                            href="/login"
                            class="text-xs font-bold text-gray-900 hover:text-primary px-2.5 py-1.5 rounded-full hover:bg-white/55 transition whitespace-nowrap"
                        >
                            Connexion
                        </Link>
                        <Link
                            href="/register"
                            class="text-xs font-bold text-white bg-primary hover:bg-primary-hover px-4 py-1.5 rounded-full shadow-sm shadow-primary/30 transition-transform duration-300 hover:scale-105 active:scale-95 whitespace-nowrap"
                        >
                            Rejoindre
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- ═══════════════════════════════════════════════════════════════════
             MOBILE : SPHÈRE 3D LIQUID GLASS & BULLE DÉPLOYÉE
             ═══════════════════════════════════════════════════════════════════ -->
        <div class="md:hidden">
            <button
                v-if="!isMobileOpen"
                @click="toggleMobile"
                class="w-[56px] h-[56px] rounded-full liquid-glass-orb flex items-center justify-center relative cursor-pointer active:scale-95 transition-transform duration-300"
                aria-label="Ouvrir le menu de navigation"
            >
                <div class="glass-inner-ring"></div>
                <div class="glass-top-meniscus"></div>
                <img
                    src="/img/misega-icon-book-black-red.png"
                    alt="MISEGA"
                    class="h-7 w-7 object-contain relative z-10 drop-shadow-[0_2px_8px_rgba(188,0,45,0.4)]"
                />
                <span
                    v-if="cartStore.totalItems > 0"
                    class="absolute top-2 right-2 w-3 h-3 bg-primary rounded-full border-2 border-white z-20"
                ></span>
            </button>

            <div
                v-else
                class="w-[92vw] max-w-sm liquid-glass-orb rounded-3xl p-4 animate-mobile-liquid space-y-4 relative overflow-hidden"
            >
                <div class="glass-inner-ring"></div>
                <div class="glass-top-meniscus"></div>

                <div class="flex items-center justify-between pb-2 border-b border-black/[0.08] relative z-10">
                    <Link href="/" @click="closeMobile" class="flex items-center gap-2">
                        <img
                            src="/img/misega-icon-book-black-red.png"
                            alt="MISEGA"
                            class="h-7 w-7 object-contain"
                        />
                        <span class="font-gang text-lg font-black text-black">
                            MI<span class="text-primary">SEGA</span>
                        </span>
                    </Link>

                    <div class="flex items-center gap-2">
                        <Link
                            href="/cart"
                            @click="closeMobile"
                            class="relative p-2 rounded-full glass-inner-pill text-gray-800"
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
                            class="p-2 rounded-full glass-inner-pill text-gray-700"
                            aria-label="Fermer le menu"
                        >
                            <X class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <div class="relative z-10">
                    <input
                        v-model="searchQuery"
                        @keyup.enter="handleSearch"
                        type="text"
                        placeholder="Rechercher un manga..."
                        class="w-full glass-inner-input text-xs text-gray-900 placeholder-gray-600 rounded-full py-2 pl-9 pr-3 focus:outline-none"
                    />
                    <Search @click="handleSearch" class="w-4 h-4 text-gray-500 absolute left-3 top-2.5" />
                </div>

                <div class="space-y-1 text-sm font-semibold text-gray-900 relative z-10">
                    <Link
                        href="/catalogue"
                        @click="closeMobile"
                        class="flex items-center justify-between p-2.5 rounded-xl hover:bg-white/50 transition"
                    >
                        <span>Catalogue Complet</span>
                        <ChevronRight class="w-4 h-4 text-gray-500" />
                    </Link>
                    <Link
                        href="/catalogue?genre=Shonen"
                        @click="closeMobile"
                        class="flex items-center justify-between p-2.5 rounded-xl hover:bg-white/50 transition font-sans"
                    >
                        <span>Shōnen</span>
                        <ChevronRight class="w-4 h-4 text-gray-500" />
                    </Link>
                    <Link
                        href="/catalogue?genre=Seinen"
                        @click="closeMobile"
                        class="flex items-center justify-between p-2.5 rounded-xl hover:bg-white/50 transition"
                    >
                        <span>Seinen</span>
                        <ChevronRight class="w-4 h-4 text-gray-500" />
                    </Link>
                    <div v-if="user">
                        <Link
                            href="/orders"
                            @click="closeMobile"
                            class="flex items-center justify-between p-2.5 rounded-xl hover:bg-white/50 transition text-primary font-bold"
                        >
                            <span>Mes Commandes</span>
                            <Package class="w-4 h-4 text-primary" />
                        </Link>
                    </div>
                </div>

                <div class="pt-2 border-t border-black/[0.08] flex items-center gap-2 relative z-10">
                    <template v-if="user">
                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            class="w-full text-center py-2 text-xs font-bold text-red-600 bg-red-50/80 rounded-xl transition"
                        >
                            Déconnexion
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            href="/login"
                            @click="closeMobile"
                            class="flex-1 text-center py-2 text-xs font-bold text-gray-800 glass-inner-pill rounded-xl transition"
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
/* ═══════════════════════════════════════════════════════════════════════════════
   MATIÈRE LIQUID GLASS 3D — CIRCONFÉRENCE 360° PURE & SANS COUPURE
   ═══════════════════════════════════════════════════════════════════════════ */
.liquid-glass-orb {
    /* Eau cristalline très transparente avec une subtile lueur interne */
    background: linear-gradient(
        180deg,
        rgba(255, 255, 255, 0.56) 0%,
        rgba(255, 250, 251, 0.24) 52%,
        rgba(255, 240, 244, 0.36) 100%
    );

    backdrop-filter: blur(16px) saturate(210%) brightness(102%);
    -webkit-backdrop-filter: blur(16px) saturate(210%) brightness(102%);

    /* Bordure 100% uniforme sur toute la circonférence : zéro trait rouge coupé ! */
    border: 1.5px solid rgba(255, 255, 255, 0.92);

    /* Volume 3D bombé uniquement par ombres internes et externes continues */
    box-shadow:
        inset 0 3px 6px rgba(255, 255, 255, 0.95),
        inset 0 -4px 10px rgba(188, 0, 45, 0.10),
        0 14px 34px -8px rgba(188, 0, 45, 0.16),
        0 6px 16px -4px rgba(15, 23, 42, 0.08),
        0 0 0 1px rgba(188, 0, 45, 0.08);
}

/* Épaisseur interne de la paroi de verre (360° continu) */
.glass-inner-ring {
    position: absolute;
    inset: 2px;
    border-radius: inherit;
    pointer-events: none;
    border: 1px solid rgba(255, 255, 255, 0.65);
    z-index: 1;
}

/* Reflet spéculaire 3D piloté par variables CSS --gx / --gy */
.glass-interactive-glare {
    position: absolute;
    inset: 0;
    border-radius: inherit;
    pointer-events: none;
    background: radial-gradient(
        ellipse 50% 70% at var(--gx, 50%) var(--gy, 16%),
        rgba(255, 255, 255, 0.80) 0%,
        rgba(255, 255, 255, 0.20) 45%,
        transparent 75%
    );
    z-index: 2;
}

/* Dôme lumineux supérieur simulant la courbure convexe */
.glass-top-meniscus {
    position: absolute;
    top: 2px;
    left: 22px;
    right: 22px;
    height: 40%;
    border-radius: 9999px;
    pointer-events: none;
    background: linear-gradient(
        180deg,
        rgba(255, 255, 255, 0.78) 0%,
        rgba(255, 255, 255, 0.05) 100%
    );
    z-index: 2;
}

/* Champ de recherche interne */
.glass-inner-input {
    background: rgba(255, 255, 255, 0.58);
    border: 1px solid rgba(188, 0, 45, 0.18);
    box-shadow:
        inset 0 1px 3px rgba(15, 23, 42, 0.05),
        0 1px 0 rgba(255, 255, 255, 0.9);
}

.glass-inner-input:focus {
    background: rgba(255, 255, 255, 0.90);
    border-color: #BC002D;
    box-shadow: 0 0 0 3px rgba(188, 0, 45, 0.12);
}

/* Petites capsules internes (Panier, Commandes) */
.glass-inner-pill {
    background: rgba(255, 255, 255, 0.55);
    border: 1px solid rgba(255, 255, 255, 0.85);
    box-shadow:
        inset 0 1px 2px rgba(255, 255, 255, 0.9),
        0 2px 5px rgba(15, 23, 42, 0.05);
}

@keyframes mobileLiquidExpand {
    0% {
        opacity: 0;
        transform: scale(0.4) translateY(-14px);
    }
    100% {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.animate-mobile-liquid {
    animation: mobileLiquidExpand 0.55s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
