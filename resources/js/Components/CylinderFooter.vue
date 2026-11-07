<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import { Link } from '@inertiajs/vue3';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

const trackRef = ref(null);
const stickyRef = ref(null);
const cylinderGraphicRef = ref(null);

let gsapCtx = null;
let refreshTimers = [];

// Support notice modal/alert state
const showSupportNotice = ref(false);
const handleSupportClick = (e) => {
    e.preventDefault();
    showSupportNotice.value = true;
    setTimeout(() => {
        showSupportNotice.value = false;
    }, 3500);
};

onMounted(async () => {
    await nextTick();

    gsapCtx = gsap.context(() => {
        const step1 = document.querySelector('.cylinder-step-1');
        const step2 = document.querySelector('.cylinder-step-2');
        const step3 = document.querySelector('.cylinder-step-3');
        const step4 = document.querySelector('.cylinder-step-4');
        const cylinder3D = document.querySelector('.cylinder-mesh-rings');

        // Master timeline linked directly to the sticky track scroll
        const tl = gsap.timeline({
            scrollTrigger: {
                trigger: trackRef.value,
                start: 'top top',
                end: 'bottom bottom',
                scrub: 1.2,
                invalidateOnRefresh: true,
            },
        });

        // ── 0. Continuous rotation of the 3D cylinder spiral rings ──
        if (cylinder3D) {
            tl.to(
                cylinder3D,
                {
                    rotationZ: -120,
                    rotationX: 25,
                    rotationY: 45,
                    ease: 'none',
                    duration: 10,
                },
                0
            );
        }

        // Initial setup for all steps: placed on the spiral cylinder
        gsap.set([step1, step2, step3, step4], {
            transformPerspective: 1200,
            transformStyle: 'preserve-3d',
            backfaceVisibility: 'hidden',
        });

        // ══════════════════════════════════════════════════════════════
        // ÉTAPE 1 : IDENTITÉ MISEGA (0% -> 25%)
        // ══════════════════════════════════════════════════════════════
        // Entre depuis le fond du cylindre en vis, tourne vers l'avant et s'ancre à gauche
        tl.fromTo(
            step1,
            {
                opacity: 0,
                rotateY: 110,
                rotateX: -12,
                rotateZ: -8,
                z: -500,
                x: 180,
                scale: 0.65,
                filter: 'blur(8px)',
            },
            {
                opacity: 1,
                rotateY: 0,
                rotateX: 0,
                rotateZ: 0,
                z: 0,
                x: 0,
                scale: 1,
                filter: 'blur(0px)',
                duration: 1.6,
                ease: 'power2.out',
            },
            0.2
        );
        // Pause de lecture
        tl.to(step1, { duration: 1.0 }, 1.8);
        // Quitte le cylindre vers l'avant-gauche
        tl.to(
            step1,
            {
                opacity: 0,
                rotateY: -35,
                z: 180,
                x: -120,
                filter: 'blur(6px)',
                duration: 1.0,
                ease: 'power2.in',
            },
            2.8
        );

        // ══════════════════════════════════════════════════════════════
        // ÉTAPE 2 : NAVIGATION (28% -> 52%)
        // ══════════════════════════════════════════════════════════════
        tl.fromTo(
            step2,
            {
                opacity: 0,
                rotateY: 110,
                rotateX: -12,
                rotateZ: -8,
                z: -500,
                x: 180,
                scale: 0.65,
                filter: 'blur(8px)',
            },
            {
                opacity: 1,
                rotateY: 0,
                rotateX: 0,
                rotateZ: 0,
                z: 0,
                x: 0,
                scale: 1,
                filter: 'blur(0px)',
                duration: 1.6,
                ease: 'power2.out',
            },
            3.2
        );
        tl.to(step2, { duration: 1.0 }, 4.8);
        tl.to(
            step2,
            {
                opacity: 0,
                rotateY: -35,
                z: 180,
                x: -120,
                filter: 'blur(6px)',
                duration: 1.0,
                ease: 'power2.in',
            },
            5.8
        );

        // ══════════════════════════════════════════════════════════════
        // ÉTAPE 3 : CLIENT (55% -> 78%)
        // ══════════════════════════════════════════════════════════════
        tl.fromTo(
            step3,
            {
                opacity: 0,
                rotateY: 110,
                rotateX: -12,
                rotateZ: -8,
                z: -500,
                x: 180,
                scale: 0.65,
                filter: 'blur(8px)',
            },
            {
                opacity: 1,
                rotateY: 0,
                rotateX: 0,
                rotateZ: 0,
                z: 0,
                x: 0,
                scale: 1,
                filter: 'blur(0px)',
                duration: 1.6,
                ease: 'power2.out',
            },
            6.2
        );
        tl.to(step3, { duration: 1.0 }, 7.8);
        tl.to(
            step3,
            {
                opacity: 0,
                rotateY: -35,
                z: 180,
                x: -120,
                filter: 'blur(6px)',
                duration: 1.0,
                ease: 'power2.in',
            },
            8.8
        );

        // ══════════════════════════════════════════════════════════════
        // ÉTAPE 4 : COPYRIGHT & L'ART DU MANGA (80% -> 100%)
        // ══════════════════════════════════════════════════════════════
        tl.fromTo(
            step4,
            {
                opacity: 0,
                rotateY: 110,
                rotateX: -12,
                rotateZ: -8,
                z: -500,
                x: 180,
                scale: 0.65,
                filter: 'blur(8px)',
            },
            {
                opacity: 1,
                rotateY: 0,
                rotateX: 0,
                rotateZ: 0,
                z: 0,
                x: 0,
                scale: 1,
                filter: 'blur(0px)',
                duration: 1.6,
                ease: 'power2.out',
            },
            9.2
        );
        // Reste posé jusqu'à la fin
        tl.to(step4, { duration: 1.2 }, 10.8);
    }, trackRef.value);

    // Refresh ScrollTrigger cleanly when images/layout finish settling
    const refresh = () => ScrollTrigger.refresh();
    window.addEventListener('resize', refresh);
    refreshTimers = [
        setTimeout(refresh, 200),
        setTimeout(refresh, 600),
        setTimeout(refresh, 1500),
    ];
});

onUnmounted(() => {
    refreshTimers.forEach(clearTimeout);
    window.removeEventListener('resize', () => ScrollTrigger.refresh());
    if (gsapCtx) gsapCtx.revert();
});
</script>

<template>
    <!-- ═══════════════════════════════════════════════════════════════════════════
         3D CYLINDER TUNNEL FOOTER
         Scroll track : h-[380vh] garantit une molette soyeuse et stable
         ═══════════════════════════════════════════════════════════════════════════ -->
    <footer ref="trackRef" class="relative w-full h-[380vh] footer-track-container">
        
        <!-- Sticky 100vh Viewport : se fige parfaitement au sommet de la vue -->
        <div ref="stickyRef" class="sticky top-0 h-screen w-full overflow-hidden flex items-center footer-bg">
            
            <!-- ── Lueurs d'ambiance Noir Obsidienne & Rouge Carmin ────────────── -->
            <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[750px] h-[340px] bg-primary/25 blur-[140px] rounded-full pointer-events-none z-0"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-[#80001f]/20 blur-[140px] rounded-full pointer-events-none z-0"></div>
            <div class="absolute -bottom-20 right-10 w-96 h-96 bg-primary/15 blur-[150px] rounded-full pointer-events-none z-0"></div>

            <!-- Arête supérieure biseautée optique (transition avec le haut) -->
            <div class="footer-top-edge"></div>

            <!-- ── 3D CYLINDRE VORTEX SPIRAL GUIDES (Arrière-plan spatial) ──────── -->
            <!-- Anneaux et vis géométriques en 3D suggérant le cylindre invisible -->
            <div class="absolute right-[5%] sm:right-[15%] top-1/2 -translate-y-1/2 w-[420px] sm:w-[560px] h-[420px] sm:h-[560px] pointer-events-none z-[1] opacity-35 perspective-stage">
                <div class="w-full h-full cylinder-mesh-rings relative transform-style-3d">
                    <!-- Anneau 1 (externe) -->
                    <div class="absolute inset-0 rounded-full border border-primary/40 transform-3d-ring-1"></div>
                    <!-- Anneau 2 -->
                    <div class="absolute inset-8 rounded-full border border-white/20 transform-3d-ring-2"></div>
                    <!-- Anneau 3 -->
                    <div class="absolute inset-16 rounded-full border border-dashed border-primary/30 transform-3d-ring-3"></div>
                    <!-- Anneau 4 (interne) -->
                    <div class="absolute inset-24 rounded-full border border-white/15 transform-3d-ring-4"></div>
                    <!-- Axe de spirale / filigrane kanji -->
                    <div class="absolute inset-0 flex items-center justify-center font-gang text-7xl sm:text-8xl text-white/[0.04] select-none rotate-12">
                        巻
                    </div>
                </div>
            </div>

            <!-- ── SCÈNE 3D DES ÉTAPES DU FOOTER (DÉROULEMENT SUR LA GAUCHE) ────── -->
            <div class="relative z-10 w-full max-w-7xl mx-auto px-6 sm:px-12 lg:px-16 cylinder-perspective-stage">
                
                <!-- ══════════════════════════════════════════════════════════════
                     ÉTAPE 1 : IDENTITÉ MISEGA
                     ══════════════════════════════════════════════════════════════ -->
                <div class="cylinder-step cylinder-step-1 pointer-events-auto">
                    <div class="step-card">
                        <div class="flex items-center gap-3.5 mb-4">
                            <img
                                src="/img/misega-icon-book-only-transparent.png"
                                alt="MISEGA"
                                class="h-10 sm:h-12 w-10 sm:w-12 object-contain drop-shadow-[0_4px_14px_rgba(188,0,45,0.7)]"
                            />
                            <span class="font-gang text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-wider">
                                MI<span class="text-primary">SEGA</span>
                            </span>
                        </div>

                        <p class="text-gray-300 text-sm sm:text-base leading-relaxed max-w-lg">
                            Votre destination numéro 1 pour la commande de mangas en ligne. Qualité, livraison rapide et passion du manga au quotidien.
                        </p>

                        <!-- Indication scroll discrète -->
                        <div class="mt-6 flex items-center gap-2 text-xs text-gray-500 font-semibold">
                            <span class="inline-block animate-bounce">↓</span>
                            <span>Continuez de faire défiler pour explorer</span>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════════════════════════
                     ÉTAPE 2 : NAVIGATION
                     ══════════════════════════════════════════════════════════════ -->
                <div class="cylinder-step cylinder-step-2 pointer-events-auto">
                    <div class="step-card">
                        <h3 class="font-gang text-2xl sm:text-4xl text-white tracking-wide mb-6">
                            Catalogue & Collections
                        </h3>

                        <ul class="space-y-3.5 text-base sm:text-lg">
                            <li>
                                <Link href="/catalogue" class="cylinder-link flex items-center gap-2.5">
                                    <span class="w-2 h-2 rounded-full bg-primary/60"></span>
                                    <span>Catalogue Complet</span>
                                    <span class="text-xs text-primary font-bold ml-1 opacity-0 cylinder-link-arrow">→</span>
                                </Link>
                            </li>
                            <li>
                                <Link href="/catalogue?genre=Shonen" class="cylinder-link flex items-center gap-2.5 font-sans">
                                    <span class="w-2 h-2 rounded-full bg-primary/60"></span>
                                    <span>Shōnen Populaires</span>
                                    <span class="text-xs text-primary font-bold ml-1 opacity-0 cylinder-link-arrow">→</span>
                                </Link>
                            </li>
                            <li>
                                <Link href="/catalogue?genre=Seinen" class="cylinder-link flex items-center gap-2.5">
                                    <span class="w-2 h-2 rounded-full bg-primary/60"></span>
                                    <span>Nouveautés Seinen</span>
                                    <span class="text-xs text-primary font-bold ml-1 opacity-0 cylinder-link-arrow">→</span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════════════════════════
                     ÉTAPE 3 : CLIENT
                     ══════════════════════════════════════════════════════════════ -->
                <div class="cylinder-step cylinder-step-3 pointer-events-auto">
                    <div class="step-card">
                        <h3 class="font-gang text-2xl sm:text-4xl text-white tracking-wide mb-6">
                            Commandes & Assistance
                        </h3>

                        <ul class="space-y-3.5 text-base sm:text-lg">
                            <li>
                                <Link href="/cart" class="cylinder-link flex items-center gap-2.5">
                                    <span class="w-2 h-2 rounded-full bg-primary/60"></span>
                                    <span>Mon Panier</span>
                                    <span class="text-xs text-primary font-bold ml-1 opacity-0 cylinder-link-arrow">→</span>
                                </Link>
                            </li>
                            <li>
                                <Link href="/orders" class="cylinder-link flex items-center gap-2.5">
                                    <span class="w-2 h-2 rounded-full bg-primary/60"></span>
                                    <span>Suivi de Commandes</span>
                                    <span class="text-xs text-primary font-bold ml-1 opacity-0 cylinder-link-arrow">→</span>
                                </Link>
                            </li>
                            <li>
                                <button
                                    @click="handleSupportClick"
                                    class="cylinder-link flex items-center gap-2.5 text-left cursor-pointer"
                                    type="button"
                                >
                                    <span class="w-2 h-2 rounded-full bg-primary/60"></span>
                                    <span>FAQ & Support</span>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-white/10 text-gray-400 font-normal">Bientôt</span>
                                </button>
                            </li>
                        </ul>

                        <!-- Message d'information FAQ (ne recharge jamais la page) -->
                        <div v-if="showSupportNotice" class="mt-4 p-3 rounded-xl bg-white/10 border border-primary/40 text-xs text-primary font-medium animate-fade-in">
                            ℹ Notre portail FAQ & Support interactif est en cours de déploiement pour la version 2.0.
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════════════════════════
                     ÉTAPE 4 : COPYRIGHT & FIN DU VOYAGE
                     ══════════════════════════════════════════════════════════════ -->
                <div class="cylinder-step cylinder-step-4 pointer-events-auto">
                    <div class="step-card">
                        <div class="font-gang text-3xl sm:text-5xl text-white tracking-wider mb-3">
                            L'Art du Manga
                        </div>

                        <p class="text-gray-400 text-sm sm:text-base leading-relaxed mb-6 max-w-md">
                            © {{ new Date().getFullYear() }} MISEGA. Tous droits réservés.
                            Plateforme dédiée aux passionnés de manga.
                        </p>

                        <!-- Remonter le défilement -->
                        <div class="flex items-center gap-2 text-xs text-gray-500 font-semibold">
                            <span class="inline-block animate-bounce">↑</span>
                            <span>Faites défiler vers le haut pour rembobiner le cylindre</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </footer>
</template>

<style scoped>
/* ── FOND GLOBAL : NOIR OBSIDIENNE & ROUGE CARMIN ── */
.footer-bg {
    background:
        radial-gradient(
            ellipse 100% 70% at 50% -15%,
            rgba(188, 0, 45, 0.32) 0%,
            rgba(45, 4, 11, 0.88) 40%,
            rgba(16, 2, 5, 0.98) 75%,
            #0a0103 100%
        ),
        linear-gradient(180deg, #160205 0%, #080102 100%);
}

/* ── Lèvre supérieure biseautée optique ── */
.footer-top-edge {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1.5px;
    z-index: 10;
    background: linear-gradient(
        90deg,
        rgba(255, 255, 255, 0.05) 0%,
        rgba(255, 255, 255, 0.55) 25%,
        rgba(188, 0, 45, 0.85) 50%,
        rgba(255, 255, 255, 0.55) 75%,
        rgba(255, 255, 255, 0.05) 100%
    );
    box-shadow: 0 1px 12px rgba(188, 0, 45, 0.6);
}

/* ── SCÈNE PERSPECTIVE 3D ── */
.cylinder-perspective-stage {
    perspective: 1400px;
    transform-style: preserve-3d;
    position: relative;
    min-height: 380px;
}

/* ── CHACUNE DES ÉTAPES SUR LE CYLINDRE ── */
.cylinder-step {
    position: absolute;
    top: 50%;
    left: clamp(2.5rem, 8vw, 8rem);
    transform: translateY(-50%);
    width: calc(100% - clamp(5rem, 16vw, 16rem));
    max-width: 32rem;
    will-change: transform, opacity, filter;
    transform-origin: left center -150px;
    opacity: 0; /* Géré par GSAP */
}

/* ── CONTENU DU BLOC TEXTE ── */
.step-card {
    position: relative;
    padding: 1.5rem 0;
}

/* ── ANNEAUX 3D DU CYLINDRE VORTEX ── */
.perspective-stage {
    perspective: 1000px;
    transform-style: preserve-3d;
}

.transform-style-3d {
    transform-style: preserve-3d;
}

.transform-3d-ring-1 {
    transform: rotateX(65deg) rotateY(15deg) translateZ(0px);
}
.transform-3d-ring-2 {
    transform: rotateX(65deg) rotateY(15deg) translateZ(-60px);
}
.transform-3d-ring-3 {
    transform: rotateX(65deg) rotateY(15deg) translateZ(-130px);
}
.transform-3d-ring-4 {
    transform: rotateX(65deg) rotateY(15deg) translateZ(-200px);
}

/* ── LIENS INTERACTIFS — CADENCE SOYEUSE (Règle n°5) ── */
.cylinder-link {
    color: #d1d5db;
    transition: all 0.45s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    display: inline-flex;
    align-items: center;
}

.cylinder-link:hover {
    color: #ffffff;
    transform: translateX(8px);
    text-shadow: 0 0 14px rgba(188, 0, 45, 0.6);
}

.cylinder-link:hover .cylinder-link-arrow {
    opacity: 1;
    transform: translateX(4px);
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fadeIn 0.4s ease-out forwards;
}
</style>
