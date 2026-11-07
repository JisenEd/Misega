<script setup>
/**
 * CylinderFooter — Footer « Tunnel 3D » équilibré Gauche / Centre / Droite.
 *
 * Principes clés (Règle d'Or Motion & 3D) :
 *  1. Couloir Gauche : Textes déroulés à plat, bornés géométriquement pour ne jamais toucher le cylindre.
 *  2. Couloir Central : Tunnel 3D avec hélice à cadence lente et soyeuse.
 *  3. Couloir Droit : Véritables livres 3D (6 faces physiques : couverture avec relief Z, dos relié 46px,
 *     tranches de pages supérieure/inférieure/droite, 4e de couverture).
 *     Séparation stricte entre le conteneur d'opacité/position (.tome-positioner) et le volume 3D
 *     (.tome-book-3d en preserve-3d pur avec opacity: 1) pour que le navigateur ne l'aplatisse jamais.
 */
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import { Link } from '@inertiajs/vue3';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import TunnelText from '@/Components/TunnelText.vue';

gsap.registerPlugin(ScrollTrigger);

const trackRef = ref(null);
const stageRef = ref(null);
const colRef = ref(null);
const stepEls = [];
const tomeWrapEls = [];
const tomeRigEls = [];
const tomeGlareEls = [];
const ringEls = [];
const RING_COUNT = 10;
const year = new Date().getFullYear();

// ─── Tomes 3D emblématiques associés aux 4 séquences du tunnel ───────────────
const footerTomes = [
    {
        number: '01',
        badge: 'Édition Prestige',
        title: 'Jujutsu Kaisen',
        subtitle: 'Gege Akutami',
        tag: 'Shōnen Culte',
        cover: 'https://cdn.myanimelist.net/images/manga/3/210341l.jpg',
        href: '/catalogue?genre=Shonen',
    },
    {
        number: '02',
        badge: 'Catalogue Officiel',
        title: 'Berserk',
        subtitle: 'Kentarou Miura',
        tag: 'Seinen Dark Fantasy',
        cover: 'https://cdn.myanimelist.net/images/manga/1/157897l.jpg',
        href: '/catalogue?genre=Seinen',
    },
    {
        number: '03',
        badge: 'Assistance & Suivi',
        title: 'Guide & Support',
        subtitle: 'Service Client MISEGA',
        tag: 'FAQ & Livraison',
        cover: 'https://cdn.myanimelist.net/images/manga/3/219741l.jpg',
        href: '/faq',
    },
    {
        number: '04',
        badge: 'Collection Certifiée',
        title: 'Chainsaw Man',
        subtitle: 'Tatsuki Fujimoto',
        tag: "L'Art du Manga",
        cover: 'https://cdn.myanimelist.net/images/manga/3/216464l.jpg',
        href: '/catalogue',
    },
];

// ─── Paramètres du tunnel & Cadence lente et contemplative ───────────────────
const F = 900;            // distance focale (perspective)
const DEPTH = 3000;       // profondeur du tunnel
const SPACING = 1.28;     // espacement généreux entre deux séquences
const STEPS = 4;
const P_MAX = (STEPS - 1) * SPACING + 1; // 4.84
const TAU = Math.PI * 2;

// Vitesse plafonnée très douce pour admirer chaque détail du mouvement
const MAX_SPEED_PER_FRAME = 0.0072;
const MAX_LEAD_P = 0.22;

let steps = [];
let tomes = [];
let R = 240;
let dTheta = 0.08;
let pitch = 3;
let target = 0;
let current = 0;
let raf = null;
let st = null;

const clamp = (v, a = 0, b = 1) => Math.min(b, Math.max(a, v));
const lerp = (a, b, t) => a + (b - a) * t;
const easeOut = (t) => 1 - Math.pow(1 - t, 3);
const easeIn = (t) => t * t * t;
const easeInOut = (t) => (t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2);

// Mesure géométrique anti-chevauchement + positions finales des lettres et des tomes 3D
const measure = () => {
    const stage = stageRef.value;
    const col = colRef.value;
    if (!stage || !col) return;

    const sr = stage.getBoundingClientRect();
    const cx = sr.left + sr.width / 2;
    const cy = sr.top + sr.height / 2;

    const isDesktop = sr.width >= 960;
    R = isDesktop
        ? Math.min(sr.width * 0.185, sr.height * 0.31)
        : Math.min(sr.width, sr.height) * 0.32;

    dTheta = 24 / R;
    pitch = 260 / (TAU / dTheta);

    ringEls.forEach((ring) => {
        if (!ring) return;
        ring.style.width = `${2 * R}px`;
        ring.style.height = `${2 * R}px`;
    });

    // Contrainte stricte de la colonne gauche : s'arrête obligatoirement à 36px du cylindre
    if (isDesktop) {
        const colRect = col.getBoundingClientRect();
        const colLeftInStage = colRect.left - sr.left;
        const cylinderLeftEdge = sr.width / 2 - R;
        const safeWidth = Math.max(220, Math.min(340, cylinderLeftEdge - colLeftInStage - 36));
        col.style.width = `${safeWidth}px`;
    } else {
        col.style.width = `${Math.min(330, sr.width - 48)}px`;
    }

    steps = stepEls.map((el) => {
        const nodes = Array.from(el.querySelectorAll('.tc'));
        nodes.forEach((n) => (n.style.transform = ''));
        const chars = nodes.map((n) => {
            const r = n.getBoundingClientRect();
            const fx = r.left + r.width / 2 - cx;
            const fy = r.top + r.height / 2 - cy;
            return { el: n, fx, fy, thF0: Math.atan2(fy, fx), rF: Math.hypot(fx, fy) };
        });
        return { el, chars, visible: null, interactive: null };
    });

    // Mesure du socle de chaque tome 3D à droite par rapport au centre du tunnel
    tomes = tomeWrapEls.map((wrapEl, idx) => {
        if (!wrapEl) return null;
        wrapEl.style.transform = '';
        const r = wrapEl.getBoundingClientRect();
        const fx = r.left + r.width / 2 - cx;
        const fy = r.top + r.height / 2 - cy;
        return {
            wrapEl,
            rigEl: tomeRigEls[idx] || null,
            glareEl: tomeGlareEls[idx] || null,
            fx,
            fy,
            visible: null,
            interactive: null,
        };
    });

    render(current, performance.now());
};

const render = (P, now = 0) => {
    const n = steps.length;

    steps.forEach((step, i) => {
        const t = P - i * SPACING;
        const isLast = i === n - 1;

        const visible = t > 0 && (isLast || t < 1.44);
        if (visible !== step.visible) {
            step.el.style.visibility = visible ? 'visible' : 'hidden';
            step.visible = visible;
        }

        const interactive = t > 0.56 && (isLast || t < 1.24);
        if (interactive !== step.interactive) {
            step.el.style.pointerEvents = interactive ? 'auto' : 'none';
            step.interactive = interactive;
        }

        // ─── Animation 3D lente et sculpturale du Livre sur la droite ────────
        const tome = tomes[i];
        if (tome && tome.wrapEl && tome.rigEl) {
            if (visible !== tome.visible) {
                tome.wrapEl.style.visibility = visible ? 'visible' : 'hidden';
                tome.visible = visible;
            }
            if (interactive !== tome.interactive) {
                tome.wrapEl.style.pointerEvents = interactive ? 'auto' : 'none';
                tome.interactive = interactive;
            }

            if (visible) {
                // Approche très progressive depuis le cœur du tunnel (t : 0.02 -> 0.72)
                const uTome = easeInOut(clamp((t - 0.02) / 0.70));
                const exitTome = isLast ? 0 : easeIn(clamp((t - 1.16) / 0.28));
                const appearTome = clamp((t - 0.01) / 0.16);

                // Respiration verticale très lente (cycle de ~12 secondes)
                const floatY = Math.sin(now * 0.00052 + i * 1.3) * 7 * uTome;
                const xRel = lerp(-tome.fx, 0, uTome) + exitTome * 110;
                const yRel = lerp(-tome.fy, floatY, uTome);
                const zTome = lerp(-DEPTH * 0.55, 0, uTome) + exitTome * 380;
                const sTome = F / (F - Math.min(zTome, F - 90));

                const fogTome = clamp((zTome + DEPTH * 0.55) / 1000);
                const alphaTome = appearTome * fogTome * (1 - exitTome);

                // 1) Le conteneur externe gère UNIQUEMENT la trajectoire 2D, l'échelle et l'opacité
                //    (ce qui préserve à 100% le preserve-3d du livre interne !)
                tome.wrapEl.style.transform = `translate3d(${(xRel * sTome).toFixed(2)}px, ${(yRel * sTome).toFixed(2)}px, 0) scale(${sTome.toFixed(4)})`;
                tome.wrapEl.style.opacity = alphaTome.toFixed(3);

                // 2) Le livre 3D interne (opacity: 1 permanent) pivote avec une lenteur majestueuse :
                //    oscillation lente (cycle de ~15 secondes) entre +24° (dos relié visible)
                //    et -26° (tranche de pages blanches visible) + inclinaison X pour voir la tranche supérieure
                const slowWaveY = Math.sin(now * 0.00042 + i * 0.9);
                const slowWaveX = Math.cos(now * 0.00036 + i * 1.1);

                const targetRotY = slowWaveY * 25;         // alterne doucement entre +25° (dos) et -25° (pages)
                const targetRotX = 8 + slowWaveX * 5;      // légère vue plongeante révélant la tranche supérieure
                const targetRotZ = slowWaveY * -2.2;

                // Arc d'émergence doux (seulement 45° d'amplitude au lieu d'une toupie)
                const rotY = lerp(42, targetRotY, uTome) - exitTome * 28;
                const rotX = lerp(22, targetRotX, uTome) + exitTome * 8;
                const rotZ = lerp(-8, targetRotZ, uTome);

                tome.rigEl.style.transform = `rotateX(${rotX.toFixed(2)}deg) rotateY(${rotY.toFixed(2)}deg) rotateZ(${rotZ.toFixed(2)}deg)`;

                // Reflet spéculaire qui glisse lentement sur la couverture selon l'angle 3D
                if (tome.glareEl) {
                    const glareShift = 50 + rotY * 1.4;
                    tome.glareEl.style.background = `linear-gradient(${(115 + rotZ * 2).toFixed(1)}deg, transparent ${(glareShift - 35).toFixed(1)}%, rgba(255,255,255,0.24) ${glareShift.toFixed(1)}%, transparent ${(glareShift + 35).toFixed(1)}%)`;
                }
            }
        }

        if (!visible) return;

        // ─── Animation des lettres (.tc) vers le couloir gauche (rotation douce) ──
        const e = easeOut(clamp(t / 0.50));
        const zFront = lerp(-DEPTH, -120, e);
        // 2.6 demi-tours au lieu de 5 : rotation hélicoïdale posée et lisible
        const theta0 = -Math.PI / 2 - (1 - e) * 2.6 * Math.PI;

        const exitE = isLast ? 0 : easeIn(clamp((t - 1.16) / 0.28));
        const appear = clamp(t / 0.12);
        const count = step.chars.length || 1;

        step.chars.forEach((c, k) => {
            const thH = theta0 + k * dTheta;
            const zH = zFront - k * pitch;

            const u = easeInOut(clamp((t - 0.44 - (k / count) * 0.12) / 0.16));

            if (u >= 0.999 && exitE === 0) {
                c.el.style.transform = '';
                c.el.style.opacity = '1';
                return;
            }

            const thF = c.thF0 + Math.ceil((thH - c.thF0) / TAU) * TAU;

            const th = lerp(thH, thF, u);
            const r = lerp(R, c.rF, u);
            const z = Math.min(lerp(zH, 0, u) + exitE * 650, F - 60);
            const x = r * Math.cos(th) - exitE * 130;
            const y = r * Math.sin(th);
            const s = F / (F - z);

            const rotH = ((((thH * 180) / Math.PI + 90) % 360) + 540) % 360 - 180;
            const rot = lerp(rotH, 0, u);
            const fog = clamp((z + DEPTH) / 1800);

            c.el.style.transform = `translate3d(${(x * s - c.fx).toFixed(2)}px, ${(y * s - c.fy).toFixed(2)}px, 0) rotate(${rot.toFixed(2)}deg) scale(${s.toFixed(4)})`;
            c.el.style.opacity = (appear * fog * (1 - exitE)).toFixed(3);
        });
    });

    // Anneaux du tunnel qui avancent lentement vers l'utilisateur
    const ringsIn = clamp(P / 0.3);
    ringEls.forEach((ring, j) => {
        if (!ring) return;
        const z = -DEPTH + ((j * (DEPTH / RING_COUNT) + P * 720) % DEPTH);
        const s = F / (F - z);
        const fog = clamp((z + DEPTH) / 2000) * clamp(-z / 400);
        ring.style.transform = `translate(-50%, -50%) scale(${s.toFixed(4)})`;
        ring.style.opacity = (fog * 0.5 * ringsIn).toFixed(3);
    });
};

// ─── Verrouillage de la vitesse de défilement (Molette / Pavé tactile) ──────
const onWheel = (e) => {
    const track = trackRef.value;
    if (!track) return;

    const rect = track.getBoundingClientRect();
    const scrollable = track.offsetHeight - window.innerHeight;
    if (scrollable <= 0) return;

    if (rect.top > 2 && rect.top - e.deltaY < -20 && e.deltaY > 0) {
        e.preventDefault();
        window.scrollTo({ top: window.scrollY + rect.top, behavior: 'instant' });
        return;
    }

    if (rect.top <= 2 && rect.bottom >= window.innerHeight - 2) {
        if (e.deltaY < 0 && rect.top >= -4 && current <= 0.04) {
            return;
        }
        if (e.deltaY > 0 && current >= P_MAX - 0.01 && rect.bottom <= window.innerHeight + 2) {
            return;
        }

        e.preventDefault();

        const dir = Math.sign(e.deltaY);
        const cappedDeltaPx = dir * Math.min(Math.abs(e.deltaY) * 0.35, 24);
        const deltaP = (cappedDeltaPx / scrollable) * P_MAX;

        const minTarget = Math.max(0, current - MAX_LEAD_P);
        const maxTarget = Math.min(P_MAX, current + MAX_LEAD_P);
        target = clamp(target + deltaP, minTarget, maxTarget);

        const footerTop = window.scrollY + rect.top;
        const lockedScrollY = footerTop + (target / P_MAX) * scrollable;
        window.scrollTo({ top: lockedScrollY, behavior: 'instant' });
    }
};

// Boucle 60 FPS : vitesse plafonnée + lévitation 3D lente et continue
const tick = (now) => {
    const diff = target - current;
    const rawStep = diff * 0.055;
    const clampedStep = Math.sign(diff) * Math.min(Math.abs(rawStep), MAX_SPEED_PER_FRAME);
    current += clampedStep;

    render(current, now);
    raf = requestAnimationFrame(tick);
};

const onResize = () => {
    measure();
    ScrollTrigger.refresh();
};

onMounted(async () => {
    await nextTick();

    st = ScrollTrigger.create({
        trigger: trackRef.value,
        start: 'top top',
        end: 'bottom bottom',
        onUpdate: (self) => {
            target = self.progress * P_MAX;
        },
    });
    target = st.progress * P_MAX;
    current = target;

    measure();
    if (document.fonts?.ready) document.fonts.ready.then(measure);
    window.addEventListener('resize', onResize);
    window.addEventListener('wheel', onWheel, { passive: false });
    setTimeout(onResize, 800);

    raf = requestAnimationFrame(tick);
});

onUnmounted(() => {
    if (raf) cancelAnimationFrame(raf);
    if (st) st.kill();
    window.removeEventListener('resize', onResize);
    window.removeEventListener('wheel', onWheel);
});
</script>

<template>
    <footer ref="trackRef" class="relative w-full h-[600vh]">
        <div ref="stageRef" class="sticky top-0 h-screen w-full overflow-hidden footer-bg">

            <!-- Lueurs d'ambiance -->
            <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[750px] h-[340px] bg-primary/25 blur-[140px] rounded-full pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-[#80001f]/20 blur-[140px] rounded-full pointer-events-none"></div>
            <div class="absolute top-1/2 right-12 -translate-y-1/2 w-80 h-80 bg-primary/20 blur-[130px] rounded-full pointer-events-none hidden lg:block"></div>

            <!-- Point de fuite du tunnel -->
            <div class="tunnel-core"></div>

            <!-- Anneaux du tunnel -->
            <div
                v-for="j in RING_COUNT"
                :key="`ring-${j}`"
                :ref="(el) => { if (el) ringEls[j - 1] = el; }"
                class="tunnel-ring"
            ></div>

            <div class="footer-top-edge"></div>

            <!-- ═══════════════════════════════════════════════════════════════════
                 COULOIR GAUCHE : TEXTES DÉROULÉS (calibrés sans débordement)
                 ═══════════════════════════════════════════════════════════════════ -->
            <div ref="colRef" class="tunnel-column grid">

                <!-- Bloc 1 : Identité -->
                <div :ref="(el) => { if (el) stepEls[0] = el; }" class="tunnel-step">
                    <div class="mb-3">
                        <span class="font-gang text-3xl sm:text-4xl font-black text-white tracking-wider"><TunnelText text="MI" /><span class="text-primary"><TunnelText text="SEGA" /></span></span>
                    </div>
                    <p class="text-gray-300 text-sm leading-relaxed">
                        <TunnelText text="Votre librairie manga de référence. Éditions officielles, expédition soignée et passion du manga au quotidien." />
                    </p>
                    <p class="mt-5 text-xs text-gray-500 font-semibold">
                        <TunnelText text="↓ Défilez pour explorer" />
                    </p>
                </div>

                <!-- Bloc 2 : Navigation -->
                <div :ref="(el) => { if (el) stepEls[1] = el; }" class="tunnel-step">
                    <h3 class="font-gang text-2xl sm:text-3xl text-white tracking-wide mb-5">
                        <TunnelText text="Navigation" />
                    </h3>
                    <ul class="space-y-3 text-sm sm:text-base">
                        <li>
                            <Link href="/catalogue" class="tunnel-link">
                                <TunnelText text="Catalogue Complet →" />
                            </Link>
                        </li>
                        <li>
                            <Link href="/catalogue?genre=Shonen" class="tunnel-link font-sans">
                                <TunnelText text="Shōnen Populaires →" />
                            </Link>
                        </li>
                        <li>
                            <Link href="/catalogue?genre=Seinen" class="tunnel-link">
                                <TunnelText text="Nouveautés Seinen →" />
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Bloc 3 : Client & FAQ Support -->
                <div :ref="(el) => { if (el) stepEls[2] = el; }" class="tunnel-step">
                    <h3 class="font-gang text-2xl sm:text-3xl text-white tracking-wide mb-5">
                        <TunnelText text="Espace Client" />
                    </h3>
                    <ul class="space-y-3 text-sm sm:text-base">
                        <li>
                            <Link href="/cart" class="tunnel-link">
                                <TunnelText text="Mon Panier →" />
                            </Link>
                        </li>
                        <li>
                            <Link href="/orders" class="tunnel-link">
                                <TunnelText text="Suivi de Commandes →" />
                            </Link>
                        </li>
                        <li>
                            <Link href="/faq" class="tunnel-link tunnel-link-highlight">
                                <TunnelText text="FAQ & Support →" />
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Bloc 4 : Copyright & Accès direct permanent -->
                <div :ref="(el) => { if (el) stepEls[3] = el; }" class="tunnel-step">
                    <div class="font-gang text-2xl sm:text-3xl text-white tracking-wider mb-3">
                        <TunnelText text="L'Art du Manga" />
                    </div>
                    <p class="text-gray-400 text-xs sm:text-sm leading-relaxed mb-4">
                        <TunnelText :text="`© ${year} MISEGA. Tous droits réservés.`" />
                    </p>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mb-5 text-xs sm:text-sm">
                        <Link href="/catalogue" class="tunnel-link">
                            <TunnelText text="Catalogue" />
                        </Link>
                        <Link href="/orders" class="tunnel-link">
                            <TunnelText text="Commandes" />
                        </Link>
                        <Link href="/faq" class="tunnel-link tunnel-link-highlight">
                            <TunnelText text="FAQ & Support →" />
                        </Link>
                    </div>
                    <p class="text-xs text-gray-500 font-semibold">
                        <TunnelText text="↑ Remontez le tunnel" />
                    </p>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════════
                 COULOIR DROIT : LIVRES 3D VOLUMÉTRIQUES (6 FACES + RELIEF Z)
                 ═══════════════════════════════════════════════════════════════════ -->
            <div class="tome-right-column hidden lg:grid">
                <div
                    v-for="(item, idx) in footerTomes"
                    :key="item.number"
                    :ref="(el) => { if (el) tomeWrapEls[idx] = el; }"
                    class="tome-positioner"
                >
                    <Link
                        :href="item.href"
                        :ref="(el) => { if (el) tomeRigEls[idx] = el.$el || el; }"
                        class="tome-book-3d group"
                    >
                        <!-- 1. COUVERTURE AVANT (+23px) -->
                        <div class="tome-face tome-front">
                            <img
                                :src="item.cover"
                                :alt="item.title"
                                class="absolute inset-0 w-full h-full object-cover opacity-70 group-hover:opacity-85 transition-opacity duration-700 rounded-r-[10px]"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-[#070102] via-[#120205]/55 to-black/20 rounded-r-[10px]"></div>

                            <!-- Reflet spéculaire dynamique piloté par l'angle 3D -->
                            <div
                                :ref="(el) => { if (el) tomeGlareEls[idx] = el; }"
                                class="tome-glare rounded-r-[10px]"
                            ></div>

                            <!-- Rainure de reliure embossée à gauche -->
                            <div class="absolute inset-y-0 left-0 w-3.5 bg-gradient-to-r from-black/90 via-white/15 to-transparent"></div>

                            <!-- Couche supérieure en relief 3D (+14px devant la couverture) -->
                            <div class="tome-front-relief">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-white bg-primary/90 px-2.5 py-1 rounded-md shadow-lg border border-white/25">
                                        {{ item.badge }}
                                    </span>
                                    <span class="font-gang text-xs text-white/90 bg-black/60 px-2 py-0.5 rounded border border-white/15">
                                        T.{{ item.number }}
                                    </span>
                                </div>

                                <div class="mt-auto pt-4">
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-primary drop-shadow block mb-1">
                                        {{ item.tag }}
                                    </span>
                                    <h4 class="font-gang text-xl text-white tracking-wide leading-tight drop-shadow-[0_4px_8px_rgba(0,0,0,0.9)]">
                                        {{ item.title }}
                                    </h4>
                                    <p class="text-[11px] text-gray-200 font-medium mt-0.5 drop-shadow">
                                        {{ item.subtitle }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- 2. DOS RELIÉ 3D GAUCHE (épaisseur 46px) -->
                        <div class="tome-face tome-spine">
                            <span class="font-gang text-xs text-primary tracking-widest">{{ item.number }}</span>
                            <div class="w-4 h-[1px] bg-primary/50"></div>
                            <span class="tome-spine-title font-gang text-xs text-white tracking-[0.2em]">{{ item.title }}</span>
                            <div class="w-4 h-[1px] bg-primary/50"></div>
                            <span class="font-gang text-[9px] text-white/70 tracking-wider">MISEGA</span>
                        </div>

                        <!-- 3. TRANCHE DE PAGES PAPIER DROITE (épaisseur 44px en retrait) -->
                        <div class="tome-face tome-pages-right"></div>

                        <!-- 4. TRANCHE DE PAGES SUPÉRIEURE (épaisseur 44px) -->
                        <div class="tome-face tome-pages-top"></div>

                        <!-- 5. TRANCHE DE PAGES INFÉRIEURE (épaisseur 44px) -->
                        <div class="tome-face tome-pages-bottom"></div>

                        <!-- 6. QUATRIÈME DE COUVERTURE ARRIÈRE (-23px) -->
                        <div class="tome-face tome-back">
                            <div class="border border-primary/30 rounded-lg px-4 py-6 flex flex-col items-center">
                                <span class="font-gang text-xl text-white/40 tracking-widest">MI<span class="text-primary/60">SEGA</span></span>
                                <span class="text-[9px] uppercase tracking-[0.25em] text-white/25 mt-1">Édition Officielle</span>
                            </div>
                        </div>
                    </Link>

                    <!-- Ombre portée & halo carmin au sol -->
                    <div class="tome-pedestal"></div>
                </div>
            </div>
        </div>
    </footer>
</template>

<style scoped>
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

/* Lueur du point de fuite, au centre du tunnel */
.tunnel-core {
    position: absolute;
    left: 50%;
    top: 50%;
    width: 220px;
    height: 220px;
    transform: translate(-50%, -50%);
    border-radius: 9999px;
    background: radial-gradient(circle, rgba(188, 0, 45, 0.28) 0%, rgba(188, 0, 45, 0.06) 45%, transparent 70%);
    filter: blur(10px);
    pointer-events: none;
}

.tunnel-ring {
    position: absolute;
    left: 50%;
    top: 50%;
    border-radius: 9999px;
    border: 1px solid rgba(188, 0, 45, 0.55);
    box-shadow: 0 0 18px rgba(188, 0, 45, 0.15), inset 0 0 18px rgba(188, 0, 45, 0.1);
    opacity: 0;
    pointer-events: none;
    will-change: transform, opacity;
}

/* Couloir Gauche : largeur bornée dynamiquement par measure() */
.tunnel-column {
    position: absolute;
    top: 50%;
    left: clamp(2rem, 5vw, 5.5rem);
    width: 18.5rem;
    transform: translateY(-50%);
    z-index: 5;
}

.tunnel-step {
    grid-column: 1;
    grid-row: 1;
    align-self: center;
    visibility: hidden;
    pointer-events: none;
}

.tunnel-step :deep(.tc) {
    opacity: 0;
    transform-origin: center center;
}

.tunnel-link {
    color: #d1d5db;
    display: inline-block;
    padding: 0.15rem 0;
    cursor: pointer;
    transition: color 0.5s cubic-bezier(0.16, 1, 0.3, 1), text-shadow 0.5s cubic-bezier(0.16, 1, 0.3, 1), transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}

.tunnel-link:hover {
    color: #ffffff;
    text-shadow: 0 0 14px rgba(188, 0, 45, 0.7);
    transform: translateX(4px);
}

.tunnel-link-highlight {
    color: #ffffff;
    font-weight: 700;
    border-bottom: 1px solid rgba(188, 0, 45, 0.65);
}

.tunnel-link-highlight:hover {
    color: #ff3366;
    border-bottom-color: #ff3366;
}

/* ═══════════════════════════════════════════════════════════════════════════
   COULOIR DROIT : VÉRITABLE LIVRE 3D (6 FACES PHYSIQUES + RELIEF Z)
   ═══════════════════════════════════════════════════════════════════════════ */
.tome-right-column {
    position: absolute;
    top: 50%;
    right: clamp(2.5rem, 6vw, 6.5rem);
    width: 210px;
    height: 290px;
    transform: translateY(-50%);
    z-index: 5;
}

/* Conteneur externe : porte perspective + translate3d + scale + opacity
   Ne porte PAS les rotations 3D pour ne jamais aplatir l'enfant preserve-3d */
.tome-positioner {
    grid-column: 1;
    grid-row: 1;
    width: 200px;
    height: 284px;
    position: relative;
    perspective: 950px;
    visibility: hidden;
    pointer-events: none;
    opacity: 0;
    will-change: transform, opacity;
}

/* Rig 3D pur : opacity toujours à 1, transform-style: preserve-3d intact */
.tome-book-3d {
    display: block;
    width: 200px;
    height: 284px;
    position: relative;
    transform-style: preserve-3d;
    will-change: transform;
}

.tome-face {
    position: absolute;
    transform-style: preserve-3d;
}

/* 1. Couverture Avant (200 × 284) avancée à Z = +23px */
.tome-front {
    inset: 0;
    transform: translateZ(23px);
    border-radius: 3px 10px 10px 3px;
    background: linear-gradient(145deg, #22040a 0%, #0b0103 100%);
    border: 1.5px solid rgba(255, 255, 255, 0.28);
    box-shadow:
        inset 0 1px 3px rgba(255, 255, 255, 0.4),
        0 0 25px rgba(188, 0, 45, 0.25);
}

/* Relief 3D flottant à +14px devant la couverture (Z total = +37px) */
.tome-front-relief {
    position: absolute;
    inset: 0;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    transform: translateZ(14px);
    pointer-events: none;
}

.tome-glare {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

/* 2. Dos relié gauche (largeur 46px = de Z -23px à Z +23px) */
.tome-spine {
    top: 0;
    bottom: 0;
    left: -23px;
    width: 46px;
    transform: rotateY(-90deg);
    background: linear-gradient(
        90deg,
        #120205 0%,
        #2b050d 25%,
        #190207 50%,
        #2b050d 75%,
        #120205 100%
    );
    border-top: 1.5px solid rgba(255, 255, 255, 0.25);
    border-bottom: 1.5px solid rgba(188, 0, 45, 0.5);
    border-left: 1px solid rgba(255, 255, 255, 0.22);
    border-right: 1px solid rgba(255, 255, 255, 0.22);
    box-shadow: inset 0 0 12px rgba(0, 0, 0, 0.8);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    padding: 14px 0;
}

.tome-spine-title {
    writing-mode: vertical-rl;
    text-orientation: mixed;
    transform: rotate(180deg);
}

/* 3. Tranche de pages papier à droite (44px d'épaisseur, légèrement en retrait de 4px sous la couverture) */
.tome-pages-right {
    top: 4px;
    bottom: 4px;
    right: -18px;
    width: 44px;
    transform: rotateY(90deg);
    background:
        repeating-linear-gradient(
            90deg,
            #f7f3eb 0px,
            #f7f3eb 2px,
            #dfd7c8 2px,
            #dfd7c8 3.5px,
            #ece5d8 3.5px,
            #ece5d8 5px
        );
    border-top: 1px solid #cbbba0;
    border-bottom: 1px solid #cbbba0;
    box-shadow:
        inset 6px 0 10px rgba(0, 0, 0, 0.45),
        inset -6px 0 10px rgba(0, 0, 0, 0.45);
}

/* 4. Tranche de pages supérieure (44px de profondeur) */
.tome-pages-top {
    top: -18px;
    left: 2px;
    right: 4px;
    height: 44px;
    transform: rotateX(90deg);
    background:
        repeating-linear-gradient(
            0deg,
            #f4efe6 0px,
            #f4efe6 2px,
            #dad1c2 2px,
            #dad1c2 3.5px
        );
    box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.45);
}

/* 5. Tranche de pages inférieure (44px de profondeur) */
.tome-pages-bottom {
    bottom: -18px;
    left: 2px;
    right: 4px;
    height: 44px;
    transform: rotateX(-90deg);
    background:
        repeating-linear-gradient(
            0deg,
            #e8e1d5 0px,
            #e8e1d5 2px,
            #c9bfb0 2px,
            #c9bfb0 3.5px
        );
    box-shadow: inset 0 0 12px rgba(0, 0, 0, 0.6);
}

/* 6. Quatrième de couverture arrière (Z = -23px) */
.tome-back {
    inset: 0;
    transform: rotateY(180deg) translateZ(23px);
    border-radius: 10px 3px 3px 10px;
    background: linear-gradient(145deg, #1a0308 0%, #090103 100%);
    border: 1.5px solid rgba(188, 0, 45, 0.45);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.9);
}

/* Socle lumineux sous le tome en lévitation */
.tome-pedestal {
    position: absolute;
    bottom: -38px;
    left: 50%;
    width: 165px;
    height: 28px;
    transform: translateX(-50%) rotateX(75deg);
    border-radius: 9999px;
    background: radial-gradient(
        circle,
        rgba(188, 0, 45, 0.6) 0%,
        rgba(188, 0, 45, 0.18) 55%,
        transparent 75%
    );
    filter: blur(6px);
    pointer-events: none;
}
</style>
