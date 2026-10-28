# 📜 BRIEF DE PASSATION TECHNIQUE & ARTISTIQUE : MISEGA 3D EXPERIENCE

---

## 1. Concept et Intention

### 1.1 Idée générale
**MISEGA** est une plateforme e-commerce haut de gamme dédiée à l'art du manga. L'expérience visuelle ne doit pas ressembler à une boutique standard, mais à une immersion cinématographique dans l'univers de l'édition et de l'énergie manga. L'application combine une esthétique **Dark Cyber-Manga** et une métaphore visuelle forte : le grimoire / manga magique qui prend vie, les fragments de pages en lévitation spatiale, et des portails dimensionnels permettant de voyager d'un univers narratif à un autre.

### 1.2 Émotion recherchée
- **Le Frisson du Mystère (Occulte & Puissance) :** Rappeler l'intensité des artefacts maudits ou des grimoires ancestraux dès l'arrivée sur la plateforme.
- **La Fluidité Cinématique :** Aucune saccade, des accélérations organiques (courbes cubiques personnalisées), une caméra qui réagit subtilement aux mouvements de l'utilisateur (parallax tilt).
- **Le Prestige & la Rareté :** Contrastes profonds, halos néon cramoisi mesurés, verre dépoli (glassmorphism) et typographie percutante.

### 1.3 Références culturelles et esthétiques
- **Jujutsu Kaisen :** L'énergie occulte, les sceaux ancestraux, la profondeur de champ sombre ponctuée d'éclats cramoisis.
- **The Eminence in Shadow :** L'élégance ténébreuse, la maîtrise des ombres, le minimalisme tranchant.
- **Esthétique Japonaise Moderne :** Harmonie entre le kanji calligraphique et le minimalisme spatial contemporain.

---

## 2. Chronologie & Cinématique de l'Animation

### 2.1 Séquence du Loader d'Introduction (`MisegaLoader.vue`)

| Séquence | Début (t) | Durée | Fin (t) | Courbe d'accélération (Easing) | Description exacte du mouvement / transformation |
|---|---|---|---|---|---|
| **Init & Scène 3D** | `0 ms` | Permanent | `∞` | `ease-in-out` | 14 kanjis répartis sur 4 plans de profondeur flottent avec inversion alternée (`alternate`) : translation Y de 0 à $\pm 28\text{px}$, rotation Z de 0 à $\pm 8^\circ$, rotation Y de 0 à $\pm 12^\circ$. Durées individuelles de 8s à 14s. |
| **Amorce Tracé SVG** | `200 ms` | - | - | Déclenchement d'état | Bascule de `bookPhase` de 0 à 1. Prépare les propriétés `stroke-dashoffset`. |
| **Tracé de la Reliure (Spine)** | `300 ms` | `600 ms` | `900 ms` | `ease-out` (délai 100ms) | Ligne médiane ($x=50, y=22 \rightarrow 78$). `stroke-dashoffset` : 1 $\rightarrow$ 0. Épaisseur de trait : `3.5px`, couleur `#BC002D`. |
| **Tracé Page Gauche** | `400 ms` | `1200 ms` | `1600 ms` | `cubic-bezier(0.25, 1, 0.5, 1)` | Courbe de Bézier de la page gauche. `stroke-dashoffset` : 1 $\rightarrow$ 0 (délai 200ms). Trait `#BC002D` (`3px`), fond `rgba(188,0,45,0.06)`. |
| **Tracé Page Droite** | `700 ms` | `1200 ms` | `1900 ms` | `cubic-bezier(0.25, 1, 0.5, 1)` | Courbe de la page droite symétrique. `stroke-dashoffset` : 1 $\rightarrow$ 0 (délai 500ms). Trait `#FFFFFF` (`3px`), fond `rgba(255,255,255,0.04)`. |
| **Lignes Intérieures (Texte)** | `1100 ms` à `1400 ms` | `800 ms` ch. | `2200 ms` | `ease-out` (délais 900ms, 1000ms, 1100ms, 1200ms) | Tracé séquentiel des 4 lignes simulant le texte manga (2 gauches en cramoisi opacité 0.4/0.3, 2 droites en blanc opacité 0.25/0.18). |
| **Apparition Logotype MISEGA** | `1200 ms` | `800 ms` | `2000 ms` | `ease` (délai 1000ms) | `opacity` : 0 $\rightarrow$ 1, `transform` : `translateY(12px)` $\rightarrow$ `translateY(0px)`. |
| **Barre de Shimmer (Pulsation)** | `1600 ms` | `500 ms` (in) + `1800 ms` boucle | `∞` | `ease` (apparition) puis `ease-in-out` infini | Apparition de la barre fine (`1px`). Gradient shimmer `via-[#BC002D]` se déplaçant horizontalement de `background-position: 200%` à `-200%`. |
| **Temps Minimum Garanti** | `0 ms` | `2200 ms` | `2200 ms` | Strict minimum | Le loader reste affiché au minimum 2.2s pour garantir la contemplation de l'animation vectorielle complète. |
| **Transition de Sortie (Fade-out)** | Déclenchement (dès fin de chargement ou fallback 5000ms) | `900 ms` | `t + 900 ms` | `cubic-bezier(0.4, 0, 0.2, 1)` | `opacity` : 1 $\rightarrow$ 0 sur l'écran complet, `pointer-events: none`. À `t + 900ms`, destruction du composant du DOM. |

---

### 2.2 Séquence du Carrousel 3D Hero (`Hero3DCarousel.vue`)
- **Scène de perspective :** `perspective: 1200px` avec `transform-style: preserve-3d`.
- **Comportement Carte Active (Centre, diff = 0) :**
  - Position spatiale : `translateZ(90px) scale(1.06)`.
  - Inclinaison interactive (curseur) : `rotateX(${-Y \times 14^\circ})` et `rotateY(${X \times 14^\circ})`.
  - Ombre projetée : `box-shadow: 0 30px 60px -15px rgba(188, 0, 45, 0.35), 0 0 25px rgba(188, 0, 45, 0.2)`.
  - Éléments internes projetés en avant : Badges et textes avec `transform: translateZ(25px)`.
  - Transition de retour au repos : `500 ms` avec `cubic-bezier(0.25, 1, 0.5, 1)`.
- **Comportement Cartes Latérales (diff = -1 et +1) :**
  - Gauche : `translateX(-110px) translateZ(-80px) rotateY(35deg) scale(0.85) blur(2px)`, opacité `0.7`.
  - Droite : `translateX(110px) translateZ(-80px) rotateY(-35deg) scale(0.85) blur(2px)`, opacité `0.7`.
  - Transition de défilement : `all 0.5s cubic-bezier(0.25, 1, 0.5, 1)`.

---

### 2.3 Séquence du Cylindre 3D des Portails d'Univers (`Genre3DPortal.vue`)
- **Structure géométrique :** Prisme régulier à 5 faces (pentagone 3D).
- **Pas angulaire entre les faces :** $\Delta\theta = \frac{360^\circ}{5} = 72^\circ$.
- **Rayon du cylindre :** $R = 430\text{px}$ (optimisé pour des cartes de $300\text{px}$ de largeur).
- **Position de chaque face $i$ :**
  $$\theta_i = \text{currentAngle} + i \times 72^\circ$$
  $$\text{transform} = \text{rotateY}(\theta_i)\;\text{translateZ}(430\text{px})$$
- **Physique de rotation par glissement (Drag & Momentum) :**
  - **Sensibilité :** $\Delta\theta = \Delta X \times 0.22^\circ/\text{px}$.
  - **Amortissement / Friction cinétique :** Vélocité multipliée par `0.95` à chaque frame de 60fps ($v_{t+1} = v_t \times 0.95$).
  - **Magnétisme d'arrêt (Snap) :** Lorsque $|v| < 0.08^\circ/\text{frame}$, calcul de la face la plus proche ($\text{round}(\theta / 72) \times 72$) et interpolation douce `lerp` avec un coefficient de `0.1` jusqu'à stabilisation complète.
- **Rendu visuel dynamique par face selon l'angle relatif $\Delta\theta_{\text{rel}}$ :**
  - **Opacité :** $1.0$ (au centre face caméra) $\rightarrow$ $0.35$ (en position arrière).
  - **Profondeur de flou :** `0px` au centre $\rightarrow$ `6px` sur les faces reculées.
  - **Échelle :** $1.0$ au centre $\rightarrow$ $0.82$ à l'arrière.

---

## 3. Direction Artistique

### 3.1 Palette Chromatique

| Rôle | Code Hexadécimal | Utilisation / Justification |
|---|---|---|
| **Rouge Marque Signature** | `#BC002D` | Couleur primaire absolue de MISEGA. Liens actifs, bordures d'énergie, boutons phares, reliure du logo. |
| **Crimson Shōnen** | `#E5003A` | Univers Shōnen : éclatant, héroïque, dynamique. |
| **Maroon Seinen** | `#8B0020` | Univers Seinen : pourpre profond, sérieux, sang séché. |
| **Rose Cramoisi Shōjo** | `#C40030` | Univers Shōjo : vibrant, émotionnel, passionné. |
| **Dark Wine Fantasy** | `#6B0018` | Univers Dark Fantasy : crépusculaire, gothique, ténébreux. |
| **Ruby Action** | `#D4002B` | Univers Action : percutant, vitesse, adrénaline pure. |
| **Fond Spatial Sombre** | `#09090D` | Arrière-plan du loader d'introduction (noir teinté d'indigo très profond). |
| **Fond Page Sombre** | `#0A0A0C` | Arrière-plan des blocs de scène hero. |
| **Surfaces de Cartes** | `#121216` / `#18181F` | Cartes 3D et blocs interactifs en mode sombre. |
| **Blanc Titre** | `#FFFFFF` | Typographies principales sur fond sombre, page droite du livre. |
| **Gris Neutre** | `#9CA3AF` / `#6B7280` | Textes secondaires, sous-titres, descriptions. |

---

### 3.2 Typographie

1. **Titres d'impact & Logotype :**
   - **Famille :** `GANG OF THREE` (`font-gang`, source locale `@font-face` via `/fonts/go3v2.ttf`).
   - **Graisses & Casse :** Extrabold / Black, Majuscules, interlettrage `tracking-widest` ou `tracking-wider`.
   - **Contrainte critique découverte :** Cette police ne comporte pas le caractère diacritique `ō` (macron). Pour tout texte contenant des voyelles longues japonaises (comme *Shōnen* ou *Shōjo*), basculer impérativement sur la police sans-serif avec graisse lourde (`font-extrabold`) pour éviter que la lettre ne devienne invisible à l'écran.

2. **Corps de texte & Interface :**
   - **Famille :** `Plus Jakarta Sans` (`font-sans`, intégrée via Google Fonts).
   - **Tailles & Graisses :**
     - Slogans / Badges : `10px` à `12px`, Bold (`700`), interlettrage `tracking-[0.3em]`, Majuscules.
     - Descriptions : `13px` à `14px`, Medium (`500`), interligne `leading-relaxed`.
     - Titres de cartes : `20px` à `24px` (`text-xl` à `text-2xl`), Extrabold (`800`).

---

### 3.3 Effets Visuels & Formats
- **Perspective et Caméra :** Perspective configurée à `900px` (Loader) et `1200px` (Carrousels).
- **Profondeur de champ optique (Bokeh) :** 4 plans optiques sur les kanjis :
  - *Arrière-plan lointain :* `scale: 0.28`, `blur: 10px`, `opacity: 0.08`.
  - *Moyen-plan arrière :* `scale: 0.62`, `blur: 4px`, `opacity: 0.16`.
  - *Plan focal net :* `scale: 1.0`, `blur: 0px`, `opacity: 0.28` à `0.30`.
  - *Premier plan avant :* `scale: 2.6` à `3.2`, `blur: 8px` à `12px`, `opacity: 0.06` à `0.09`.
- **Texture Scanlines :** Dégradé tramé répété (`2px` transparent, `1px` blanc `rgba(255,255,255,0.5)`) à `2.5%` d'opacité.
- **Ratio d'enregistrement / Scène visuelle :** Strictement configuré au format **16:9** (recommandé $1920 \times 1080$ ou $1280 \times 720$).

---

## 4. Textes Exacts Affichés à l'Écran

### 4.1 Écran de Chargement (`MisegaLoader.vue`)
- **Logo :** `MI` (couleur `#FFFFFF`) + `SEGA` (couleur `#BC002D`).
- **Sous-titre :** `L'ART DU MANGA` (majuscules, espacement de lettres `0.3em`).
- **Glyphes Kanjis flottants :**
  - `術` (*Jutsu* : Technique / Art occulte)
  - `書` (*Sho* : Livre / Écrit)
  - `読` (*Doku* : Lecture)
  - `芸` (*Gei* : Art / Artisanat)
  - `漫` (*Man* : Manga / Divertissement)
  - `画` (*Ga* : Dessin / Image)

### 4.2 Carrousel Hero 3D (`Hero3DCarousel.vue`)
- **Badge Flottant 3D :** `[GENRE]` (ex. `SHŌNEN`, `SEINEN`)
- **Auteur :** `Par [Nom de l'auteur]`
- **Compteur de tomes :** `[N] Tomes`
- **Bouton d'action de la carte active :** `Découvrir en 3D` (avec icône œil Lucide)

### 4.3 Portails des Univers Manga (`Genre3DPortal.vue`)
- **En-tête de section :**
  - Surtitre : `PORTAILS DIMENSIONNELS`
  - Titre principal : `EXPLOREZ PAR UNIVERS MANGA`
- **Contenu des 5 Cartes de Portail :**
  1. *Shōnen :* Titre: `SHŌNEN` | Kanji: `少年` | Tagline: `QUÊTE & DÉPASSEMENT` | Description: `Courage, dépassement de soi et liens indéfectibles. Plongez dans des aventures légendaires.` | Bouton: `Explorer l'univers`
  2. *Seinen :* Titre: `SEINEN` | Kanji: `青年` | Tagline: `MATURITÉ & PSYCHOLOGIE` | Description: `Intrigues complexes, dilemmes moraux et récits sans concession pour lecteurs avertis.` | Bouton: `Explorer l'univers`
  3. *Shōjo :* Titre: `SHŌJO` | Kanji: `少女` | Tagline: `ÉMOTION & DESTINÉE` | Description: `Sentiments profonds, quêtes d'identité et relations humaines explorées avec grâce.` | Bouton: `Explorer l'univers`
  4. *Dark Fantasy :* Titre: `DARK FANTASY` | Kanji: `闇` | Tagline: `TÉNÈBRES & DESTIN` | Description: `Mondes crépusculaires, sacrifices déchirants et combats désespérés contre le destin.` | Bouton: `Explorer l'univers`
  5. *Action :* Titre: `ACTION` | Kanji: `闘` | Tagline: `ADRÉNALINE & IMPACT` | Description: `Chorégraphies martiales millimétrées, pouvoirs titanesques et affrontements épiques.` | Bouton: `Explorer l'univers`
- **Navigation basse :**
  - Boutons fléchés : `ChevronLeft` et `ChevronRight`
  - Pills de sélection : `Shōnen`, `Seinen`, `Shōjo`, `Dark Fantasy`, `Action`
  - Bouton CTA principal : `VOIR TOUT EN [NOM DU GENRE]` (avec flèche orientée haut-droite)

---

## 5. Interactions, Événements & Mode d'Enregistrement

### 5.1 Règle Critique de Gestion des Événements Pointeurs
> **Piège évité :** Lorsque le conteneur 3D écoute l'événement `pointerdown` pour démarrer le drag du cylindre, **il ne faut jamais appeler `setPointerCapture` si la cible de l'événement est un élément interactif** (`button`, `a`, ou leurs enfants).
>
> **Code de garde obligatoire :**
```javascript
const onPointerDown = (e) => {
    if (e.target.closest('button, a')) {
        return;
    }
    isDragging.value = true;
    startX = e.clientX;
    lastX = e.clientX;
    velocity = 0;
    stageRef.value?.setPointerCapture?.(e.pointerId);
};
```

### 5.2 Clavier & Navigation
- Touches Flèche Gauche (`ArrowLeft`) / Flèche Droite (`ArrowRight`) : rotation d'un incrément de $\pm 72^\circ$ vers la face suivante ou précédente.
- Support tactile complet : glissement au doigt (`touchstart` / `touchmove` / `touchend`) avec préservation de l'inertie.

### 5.3 Précautions pour l'Enregistrement Vidéo & Médias
- **Format scène :** Cadre 16:9 sans débordement (`overflow: hidden`).
- **Masquage d'accessibilité :** Les kanjis de fond et les particules possèdent `aria-hidden="true"` et `pointer-events: none` afin de ne jamais gêner la navigation ou les lecteurs d'écran.

---

## 6. État Actuel du Projet

- **Ce qui est 100% opérationnel :**
  - Le **Loader d'introduction** (`MisegaLoader.vue`) avec son tracé de livre SVG synchronisé et la scène 3D de kanjis.
  - Le **Carrousel 3D Hero** (`Hero3DCarousel.vue`) avec orientation dynamique à la souris et transitions de profondeur.
  - La **Scène de fond manga** (`MangaSceneBackground.vue`) avec lévitation de pages manga et interpolation lerp au défilement.
  - Tout le système e-commerce de la plateforme (panier Pinia persistant, catalogue avec filtres dynamiques, calcul des frais de port en FCFA, tunnel de commande, vérification par email).
- **Statut de la Proposition n°3 (`Genre3DPortal.vue`) :**
  - Le composant a été entièrement codé, stylisé, ajusté (espacement `mt-28` pour détacher la barre de navigation du cylindre, typographie corrigée pour les macrons, écoute des clics rétablie).
  - À la demande de M. Ed, le composant a été retiré temporairement de la page `Home.vue` pour revenir à la grille de cartes sobre, afin que le concept 3D puisse être repris et exporté dans un outil dédié en toute autonomie.
- **Idées envisagées pour la suite :**
  - Effet de téléportation cinématique (*Warp Speed*) : lors du clic sur « Explorer l'univers », la caméra avance à toute vitesse dans le portail (`translateZ(1200px)`) avant de charger la page catalogue filtrée.

---

## 7. Décisions Prises et Pistes Rejetées

1. **CSS 3D matériel vs Three.js / WebGL :**
   - *Piste rejetée :* Utiliser une scène Three.js complète avec Canvas WebGL.
   - *Raison du choix :* Three.js ajoutait un surpoids de bundle de plus de 600 Ko et compliquait le rendu du texte vectoriel net ainsi que l'intégration des liens Inertia.js. Le CSS 3D matériel (`transform-style: preserve-3d`) a permis une fluidité à 60fps constante, un texte parfaitement net quel que soit le zoom de l'écran, et une intégration DOM native.
2. **Harmonisation de la palette vers le Rouge Marque :**
   - *Piste rejetée :* Attribuer une couleur très différente à chaque genre (ex. vert pour Shōnen, violet pour Dark Fantasy, bleu pour Action).
   - *Raison du choix :* L'effet visuel cassait la cohérence de l'identité MISEGA. Nous avons unifié toute la gamme dans des déclinaisons chaudes et profondes du rouge de marque `#BC002D` (ruby, maroon, wine, crimson).
3. **Typographie des genres avec macron (`ō`) :**
   - *Piste rejetée :* Forcer la police `font-gang` sur le titre principal des cartes.
   - *Raison du choix :* Le fichier de police `go3v2.ttf` ne possède pas le caractère Unicode `ō`, ce qui faisait disparaître la lettre dans « Shōnen » et « Shōjo ». Nous avons basculé ces titres en `font-sans` (Plus Jakarta Sans) ultra-bold, tout en réservant `font-gang` aux logos et kanjis.

---

## 8. Code Source Intégral des Fichiers Clés

### 8.1 `MisegaLoader.vue` (Loader d'Introduction 3D)
```vue
<script>
let hasAlreadyLoaded = false;
if (typeof window !== 'undefined' && sessionStorage.getItem('misega_loader_shown')) {
    hasAlreadyLoaded = true;
}
</script>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';

// ─── State ─────────────────────────────────────────────────────────────────
const isVisible   = ref(!hasAlreadyLoaded);
const isFadingOut = ref(false);
const bookPhase   = ref(hasAlreadyLoaded ? 2 : 0); // 0=idle, 1=drawing, 2=done

// ─── Kanji scene: depth-of-field via scale + blur ──────────────────────────
const kanjis = [
    // --- Far plane (small, back-blurred) ---
    { id: 0,  char: '術', left: 4,  top: 5,   scale: 0.28, blur: 10, opacity: 0.08, dur: 12, delay: 0,   color: '#BC002D', rotDir: 1  },
    { id: 1,  char: '書', left: 88, top: 10,  scale: 0.32, blur: 9,  opacity: 0.09, dur: 10, delay: 2.5, color: '#ffffff', rotDir: -1 },
    { id: 2,  char: '読', left: 50, top: 85,  scale: 0.40, blur: 8,  opacity: 0.11, dur: 11, delay: 4,   color: '#BC002D', rotDir: 1  },
    { id: 3,  char: '芸', left: 78, top: 78,  scale: 0.45, blur: 7,  opacity: 0.12, dur: 9,  delay: 6,   color: '#ffffff', rotDir: -1 },
    // --- Near-far (slightly blurred) ---
    { id: 4,  char: '漫', left: 20, top: 72,  scale: 0.62, blur: 4,  opacity: 0.16, dur: 14, delay: 1,   color: '#ffffff', rotDir: 1  },
    { id: 5,  char: '画', left: 68, top: 55,  scale: 0.70, blur: 3,  opacity: 0.18, dur: 11, delay: 3,   color: '#BC002D', rotDir: -1 },
    // --- Focal plane (sharp, medium size) ---
    { id: 6,  char: '芸', left: 8,  top: 35,  scale: 1.0,  blur: 0,  opacity: 0.28, dur: 13, delay: 0.5, color: '#BC002D', rotDir: 1  },
    { id: 7,  char: '読', left: 84, top: 32,  scale: 0.95, blur: 0,  opacity: 0.25, dur: 10, delay: 5,   color: '#ffffff', rotDir: -1 },
    { id: 8,  char: '書', left: 38, top: 14,  scale: 1.05, blur: 0,  opacity: 0.30, dur: 12, delay: 2,   color: '#ffffff', rotDir: 1  },
    // --- Near-front (slightly blurred) ---
    { id: 9,  char: '術', left: 72, top: 20,  scale: 1.5,  blur: 3,  opacity: 0.18, dur: 9,  delay: 1.5, color: '#BC002D', rotDir: -1 },
    { id: 10, char: '漫', left: 22, top: 52,  scale: 1.65, blur: 4,  opacity: 0.15, dur: 10, delay: 3.5, color: '#ffffff', rotDir: 1  },
    // --- Front plane (large, heavy blur) ---
    { id: 11, char: '画', left: 60, top: 5,   scale: 2.6,  blur: 8,  opacity: 0.09, dur: 8,  delay: 0,   color: '#ffffff', rotDir: -1 },
    { id: 12, char: '芸', left: 5,  top: 68,  scale: 2.9,  blur: 10, opacity: 0.07, dur: 10, delay: 2,   color: '#BC002D', rotDir: 1  },
    { id: 13, char: '漫', left: 80, top: 60,  scale: 3.2,  blur: 12, opacity: 0.06, dur: 9,  delay: 4,   color: '#ffffff', rotDir: -1 },
];

// ─── Loader lifecycle ───────────────────────────────────────────────────────
const startFadeOut = () => {
    if (isFadingOut.value) return;
    isFadingOut.value = true;
    setTimeout(() => { isVisible.value = false; }, 900);
};

let unsubFinish = null;
let unsubStart  = null;
let fallbackTimer = null;

onMounted(() => {
    if (hasAlreadyLoaded) return;
    hasAlreadyLoaded = true;
    if (typeof window !== 'undefined') {
        sessionStorage.setItem('misega_loader_shown', 'true');
    }

    setTimeout(() => { bookPhase.value = 1; }, 200);
    setTimeout(() => { bookPhase.value = 2; }, 1800);

    let inertiaReady  = false;
    let minTimePassed = false;

    const tryFadeOut = () => {
        if (inertiaReady && minTimePassed) startFadeOut();
    };

    setTimeout(() => { minTimePassed = true; tryFadeOut(); }, 2200);

    unsubFinish = router.on('finish', () => { inertiaReady = true; tryFadeOut(); });
    fallbackTimer = setTimeout(() => { if (isVisible.value) startFadeOut(); }, 5000);
});

onUnmounted(() => {
    if (unsubFinish) unsubFinish();
    if (unsubStart)  unsubStart();
    if (fallbackTimer) clearTimeout(fallbackTimer);
});
</script>

<template>
    <Transition name="loader-fade" appear>
        <div
            v-if="isVisible"
            class="fixed inset-0 z-[9999] flex items-center justify-center overflow-hidden select-none"
            :class="isFadingOut ? 'pointer-events-none' : ''"
            :style="{ opacity: isFadingOut ? 0 : 1, transition: 'opacity 0.9s cubic-bezier(0.4, 0, 0.2, 1)' }"
            aria-hidden="true"
        >
            <div class="absolute inset-0 bg-[#09090D]">
                <div class="absolute inset-0" style="background: radial-gradient(ellipse 80% 70% at 50% 50%, rgba(188,0,45,0.05) 0%, transparent 70%);"></div>
                <div class="absolute inset-0 opacity-[0.025]" style="background-image: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(255,255,255,0.5) 2px, rgba(255,255,255,0.5) 3px);"></div>
            </div>

            <div class="absolute inset-0 overflow-hidden" style="perspective: 900px;">
                <div
                    v-for="k in kanjis"
                    :key="k.id"
                    class="absolute font-gang leading-none"
                    :style="{
                        left: `${k.left}%`,
                        top: `${k.top}%`,
                        fontSize: `${k.scale * 6}rem`,
                        color: k.color,
                        opacity: k.opacity,
                        filter: k.blur > 0 ? `blur(${k.blur}px)` : 'none',
                        animationName: 'kanjiFloat',
                        animationDuration: `${k.dur}s`,
                        animationDelay: `${k.delay}s`,
                        animationTimingFunction: 'ease-in-out',
                        animationIterationCount: 'infinite',
                        animationDirection: 'alternate',
                        '--rot-dir': k.rotDir,
                        transformOrigin: 'center center',
                        willChange: 'transform',
                    }"
                >{{ k.char }}</div>
            </div>

            <div class="relative z-10 flex flex-col items-center gap-6">
                <div class="relative w-24 h-24">
                    <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full overflow-visible">
                        <!-- Left Page -->
                        <path
                            pathLength="1"
                            d="M 50,22 C 36,14 18,18 10,22 L 10,76 C 18,72 36,68 50,78 Z"
                            stroke="#BC002D"
                            stroke-width="3"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            fill="rgba(188,0,45,0.06)"
                            :style="bookPhase >= 1 ? 'stroke-dasharray:1; stroke-dashoffset:0; transition: stroke-dashoffset 1.2s cubic-bezier(0.25,1,0.5,1) 0.2s' : 'stroke-dasharray:1; stroke-dashoffset:1'"
                        />
                        <!-- Right Page -->
                        <path
                            pathLength="1"
                            d="M 50,22 C 64,14 82,18 90,22 L 90,76 C 82,72 64,68 50,78 Z"
                            stroke="#ffffff"
                            stroke-width="3"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            fill="rgba(255,255,255,0.04)"
                            :style="bookPhase >= 1 ? 'stroke-dasharray:1; stroke-dashoffset:0; transition: stroke-dashoffset 1.2s cubic-bezier(0.25,1,0.5,1) 0.5s' : 'stroke-dasharray:1; stroke-dashoffset:1'"
                        />
                        <!-- Spine -->
                        <line
                            x1="50" y1="22" x2="50" y2="78"
                            stroke="#BC002D"
                            stroke-width="3.5"
                            stroke-linecap="round"
                            pathLength="1"
                            :style="bookPhase >= 1 ? 'stroke-dasharray:1; stroke-dashoffset:0; transition: stroke-dashoffset 0.6s ease-out 0.1s' : 'stroke-dasharray:1; stroke-dashoffset:1'"
                        />
                        <!-- Page lines -->
                        <line x1="22" y1="36" x2="44" y2="33" stroke="rgba(188,0,45,0.4)" stroke-width="1.5" stroke-linecap="round" pathLength="1"
                            :style="bookPhase >= 1 ? 'stroke-dasharray:1; stroke-dashoffset:0; transition: stroke-dashoffset 0.8s ease-out 0.9s' : 'stroke-dasharray:1; stroke-dashoffset:1'"
                        />
                        <line x1="22" y1="44" x2="44" y2="41" stroke="rgba(188,0,45,0.3)" stroke-width="1.5" stroke-linecap="round" pathLength="1"
                            :style="bookPhase >= 1 ? 'stroke-dasharray:1; stroke-dashoffset:0; transition: stroke-dashoffset 0.8s ease-out 1.0s' : 'stroke-dasharray:1; stroke-dashoffset:1'"
                        />
                        <line x1="56" y1="33" x2="78" y2="36" stroke="rgba(255,255,255,0.25)" stroke-width="1.5" stroke-linecap="round" pathLength="1"
                            :style="bookPhase >= 1 ? 'stroke-dasharray:1; stroke-dashoffset:0; transition: stroke-dashoffset 0.8s ease-out 1.1s' : 'stroke-dasharray:1; stroke-dashoffset:1'"
                        />
                        <line x1="56" y1="41" x2="78" y2="44" stroke="rgba(255,255,255,0.18)" stroke-width="1.5" stroke-linecap="round" pathLength="1"
                            :style="bookPhase >= 1 ? 'stroke-dasharray:1; stroke-dashoffset:0; transition: stroke-dashoffset 0.8s ease-out 1.2s' : 'stroke-dasharray:1; stroke-dashoffset:1'"
                        />
                    </svg>
                </div>

                <div
                    class="text-center"
                    :style="{ opacity: bookPhase >= 1 ? 1 : 0, transform: bookPhase >= 1 ? 'translateY(0)' : 'translateY(12px)', transition: 'opacity 0.8s ease 1.0s, transform 0.8s ease 1.0s' }"
                >
                    <h1 class="font-gang text-5xl sm:text-6xl font-extrabold tracking-widest text-white">
                        MI<span class="text-[#BC002D]">SEGA</span>
                    </h1>
                    <p class="text-gray-500 text-xs font-semibold tracking-[0.3em] uppercase mt-2">
                        L'Art du Manga
                    </p>
                </div>

                <div
                    class="w-40 h-px bg-white/10 rounded-full overflow-hidden mt-2"
                    :style="{ opacity: bookPhase >= 1 ? 1 : 0, transition: 'opacity 0.5s ease 1.4s' }"
                >
                    <div
                        class="h-full bg-gradient-to-r from-transparent via-[#BC002D] to-transparent rounded-full"
                        :style="{ animation: 'loadingShimmer 1.8s ease-in-out infinite', backgroundSize: '200% 100%' }"
                    ></div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
@keyframes kanjiFloat {
    0% {
        transform: translateY(0px) rotateZ(0deg) rotateY(0deg);
    }
    100% {
        transform: translateY(calc(var(--rot-dir, 1) * -28px)) rotateZ(calc(var(--rot-dir, 1) * 8deg)) rotateY(calc(var(--rot-dir, 1) * 12deg));
    }
}

@keyframes loadingShimmer {
    0%   { background-position: 200% center; }
    100% { background-position: -200% center; }
}
</style>
```

---

### 8.2 `Hero3DCarousel.vue` (Carrousel Perspective Hero)
```vue
<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, Eye } from '@lucide/vue';

const props = defineProps({
    mangas: {
        type: Array,
        default: () => []
    }
});

const imageErrors = ref({});
const getMangaCover = (manga) => manga?.cover_url || manga?.cover_image || null;
const handleImgError = (id) => { imageErrors.value[id] = true; };

const activeIndex = ref(1);
const cardTilts = ref({});

const carouselMangas = computed(() => {
    if (props.mangas.length >= 3) {
        return props.mangas.slice(0, 3);
    }
    return props.mangas;
});

const nextSlide = () => {
    if (carouselMangas.value.length === 0) return;
    activeIndex.value = (activeIndex.value + 1) % carouselMangas.value.length;
};

const prevSlide = () => {
    if (carouselMangas.value.length === 0) return;
    activeIndex.value = (activeIndex.value - 1 + carouselMangas.value.length) % carouselMangas.value.length;
};

const selectCard = (index) => {
    activeIndex.value = index;
};

const getCardStyle = (index) => {
    const total = carouselMangas.value.length;
    if (total === 0) return {};

    let diff = index - activeIndex.value;
    if (diff < -1) diff += total;
    if (diff > 1) diff -= total;

    const tilt = cardTilts.value[index] || { x: 0, y: 0 };

    if (diff === 0) {
        return {
            transform: `perspective(1200px) rotateY(${tilt.y}deg) rotateX(${tilt.x}deg) translateZ(90px) scale(1.06)`,
            opacity: 1,
            filter: 'blur(0px)',
            zIndex: 30,
            boxShadow: '0 30px 60px -15px rgba(188, 0, 45, 0.35), 0 0 25px rgba(188, 0, 45, 0.2)',
            transition: tilt.x === 0 && tilt.y === 0 ? 'all 0.5s cubic-bezier(0.25, 1, 0.5, 1)' : 'transform 0.1s ease-out',
        };
    } else if (diff === -1 || (diff === 2 && total === 3)) {
        return {
            transform: 'perspective(1200px) rotateY(35deg) translateX(-110px) translateZ(-80px) scale(0.85)',
            opacity: 0.7,
            filter: 'blur(2px)',
            zIndex: 10,
            boxShadow: '0 15px 30px rgba(0,0,0,0.15)',
            transition: 'all 0.5s cubic-bezier(0.25, 1, 0.5, 1)',
        };
    } else {
        return {
            transform: 'perspective(1200px) rotateY(-35deg) translateX(110px) translateZ(-80px) scale(0.85)',
            opacity: 0.7,
            filter: 'blur(2px)',
            zIndex: 10,
            boxShadow: '0 15px 30px rgba(0,0,0,0.15)',
            transition: 'all 0.5s cubic-bezier(0.25, 1, 0.5, 1)',
        };
    }
};

const handleMouseMove = (e, index) => {
    if (index !== activeIndex.value) return;
    const card = e.currentTarget;
    const rect = card.getBoundingClientRect();
    const x = e.clientX - rect.left - rect.width / 2;
    const y = e.clientY - rect.top - rect.height / 2;

    const rotateX = (-y / (rect.height / 2)) * 14;
    const rotateY = (x / (rect.width / 2)) * 14;

    cardTilts.value[index] = { x: rotateX, y: rotateY };
};

const handleMouseLeave = (index) => {
    cardTilts.value[index] = { x: 0, y: 0 };
};
</script>

<template>
    <div class="relative w-full max-w-lg mx-auto py-8 px-4 flex flex-col items-center">
        <div class="relative w-full h-[360px] sm:h-[400px] flex items-center justify-center perspective-stage">
            <div 
                v-for="(manga, index) in carouselMangas" 
                :key="manga.id"
                @click="selectCard(index)"
                @mousemove="e => handleMouseMove(e, index)"
                @mouseleave="handleMouseLeave(index)"
                :style="getCardStyle(index)"
                class="absolute w-[210px] sm:w-[240px] aspect-[3/4] rounded-2xl overflow-hidden cursor-pointer select-none bg-white border-2 border-white/80 transform-gpu group"
            >
                <div class="relative w-full h-full transform-style-3d">
                    <div v-if="imageErrors[manga.id] || !getMangaCover(manga)" class="w-full h-full bg-gradient-to-br from-gray-800 via-gray-900 to-gray-950 flex flex-col items-center justify-center p-4 text-center select-none rounded-2xl">
                        <div class="font-gang text-white text-base sm:text-lg font-bold line-clamp-2 leading-tight mb-2">
                            {{ manga.title }}
                        </div>
                        <span class="text-[10px] font-semibold text-primary uppercase tracking-widest bg-primary/10 px-2.5 py-0.5 rounded border border-primary/20">
                            {{ manga.genre }}
                        </span>
                    </div>
                    <img 
                        v-else
                        :src="getMangaCover(manga)" 
                        :alt="manga.title" 
                        @error="handleImgError(manga.id)"
                        class="w-full h-full object-cover rounded-2xl group-hover:scale-105 transition-transform duration-500"
                    />

                    <div class="absolute inset-0 bg-gradient-to-t from-gray-950/90 via-gray-950/20 to-transparent"></div>

                    <div class="absolute top-3 left-3 bg-primary text-white text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-md shadow-md transform-3d-front">
                        {{ manga.genre }}
                    </div>

                    <div class="absolute inset-x-0 bottom-0 p-4 flex flex-col justify-end text-white transform-3d-front space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-gray-300">Par {{ manga.author }}</span>
                            <span class="text-[10px] bg-white/20 backdrop-blur-md px-2 py-0.5 rounded font-bold">
                                {{ manga.volumes ? manga.volumes.length : 0 }} Tomes
                            </span>
                        </div>

                        <h3 class="font-gang text-xl font-extrabold tracking-wide text-white drop-shadow-md line-clamp-1">
                            {{ manga.title }}
                        </h3>

                        <div class="pt-1 flex items-center gap-2">
                            <Link 
                                :href="`/manga/${manga.id}`"
                                class="w-full bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2 px-3 rounded-xl shadow-lg shadow-primary/40 flex items-center justify-center gap-1.5 transition"
                            >
                                <Eye class="w-3.5 h-3.5" />
                                Découvrir en 3D
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4 mt-6 z-40">
            <button 
                @click="prevSlide"
                class="p-2.5 rounded-full bg-white hover:bg-gray-100 border border-gray-200 shadow-md text-gray-700 hover:text-primary transition hover:scale-110 active:scale-95"
                title="Manga Précédent"
            >
                <ChevronLeft class="w-5 h-5" />
            </button>

            <div class="flex items-center gap-2">
                <button 
                    v-for="(manga, idx) in carouselMangas" 
                    :key="manga.id"
                    @click="selectCard(idx)"
                    class="h-2.5 rounded-full transition-all duration-300"
                    :class="idx === activeIndex ? 'w-8 bg-primary shadow-sm' : 'w-2.5 bg-gray-300 hover:bg-gray-400'"
                ></button>
            </div>

            <button 
                @click="nextSlide"
                class="p-2.5 rounded-full bg-white hover:bg-gray-100 border border-gray-200 shadow-md text-gray-700 hover:text-primary transition hover:scale-110 active:scale-95"
                title="Manga Suivant"
            >
                <ChevronRight class="w-5 h-5" />
            </button>
        </div>
    </div>
</template>

<style scoped>
.perspective-stage {
    perspective: 1200px;
    transform-style: preserve-3d;
}

.transform-style-3d {
    transform-style: preserve-3d;
}

.transform-3d-front {
    transform: translateZ(25px);
}
</style>
```

---

### 8.3 `Genre3DPortal.vue` (Proposition n°3 : Les Portails Dimensionnels 3D)
```vue
<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, Sparkles, ArrowUpRight } from '@lucide/vue';

// ─── Univers / Genres (Harmonisés autour de #BC002D) ─────────────────────────
const universes = [
    {
        id: 'Shonen',
        name: 'Shōnen',
        kanji: '少年',
        accentColor: '#E5003A',
        tagline: 'QUÊTE & DÉPASSEMENT',
        description: 'Courage, dépassement de soi et liens indéfectibles. Plongez dans des aventures légendaires.',
        icon: '⚔️',
        seriesCount: 42,
    },
    {
        id: 'Seinen',
        name: 'Seinen',
        kanji: '青年',
        accentColor: '#8B0020',
        tagline: 'MATURITÉ & PSYCHOLOGIE',
        description: 'Intrigues complexes, dilemmes moraux et récits sans concession pour lecteurs avertis.',
        icon: '🌑',
        seriesCount: 28,
    },
    {
        id: 'Shojo',
        name: 'Shōjo',
        kanji: '少女',
        accentColor: '#C40030',
        tagline: 'ÉMOTION & DESTINÉE',
        description: 'Sentiments profonds, quêtes d\'identité et relations humaines explorées avec grâce.',
        icon: '🌸',
        seriesCount: 19,
    },
    {
        id: 'Dark Fantasy',
        name: 'Dark Fantasy',
        kanji: '闇',
        accentColor: '#6B0018',
        tagline: 'TÉNÈBRES & DESTIN',
        description: 'Mondes crépusculaires, sacrifices déchirants et combats désespérés contre le destin.',
        icon: '🗡️',
        seriesCount: 31,
    },
    {
        id: 'Action',
        name: 'Action',
        kanji: '闘',
        accentColor: '#D4002B',
        tagline: 'ADRÉNALINE & IMPACT',
        description: 'Chorégraphies martiales millimétrées, pouvoirs titanesques et affrontements épiques.',
        icon: '⚡',
        seriesCount: 37,
    },
];

const totalUniverses = universes.length;
const angleStep = 360 / totalUniverses; // 72 deg
const cylinderRadius = 430; // Rayon 3D en pixels

const currentAngle = ref(0);
const activeIndex = ref(0);
const isDragging = ref(false);
const stageRef = ref(null);

let startX = 0;
let lastX = 0;
let velocity = 0;
let rafId = null;

const currentUniverse = computed(() => universes[activeIndex.value]);

const normalizeAngle = (angle) => {
    let a = angle % 360;
    if (a < 0) a += 360;
    return a;
};

const updateActiveFromAngle = () => {
    const raw = -currentAngle.value / angleStep;
    let idx = Math.round(raw) % totalUniverses;
    if (idx < 0) idx += totalUniverses;
    activeIndex.value = idx;
};

const getFaceStyle = (index) => {
    const baseAngle = index * angleStep;
    const faceAngle = baseAngle + currentAngle.value;
    const norm = normalizeAngle(faceAngle);
    const rad = (norm * Math.PI) / 180;
    const cosVal = Math.cos(rad);

    const opacity = 0.35 + 0.65 * Math.max(0, cosVal);
    const blurPx = Math.max(0, (1 - cosVal) * 4);
    const brightness = 0.5 + 0.5 * Math.max(0, cosVal);
    const zIndex = Math.round((cosVal + 1) * 100);
    const scale = 0.85 + 0.15 * Math.max(0, cosVal);

    return {
        transform: `rotateY(${baseAngle}deg) translateZ(${cylinderRadius}px) scale(${scale})`,
        opacity,
        filter: `blur(${blurPx.toFixed(1)}px) brightness(${brightness.toFixed(2)})`,
        zIndex,
        transition: isDragging.value ? 'none' : 'filter 0.4s ease, opacity 0.4s ease',
    };
};

const rotateToUniverse = (index) => {
    const target = -index * angleStep;
    const diff = ((target - currentAngle.value) % 360 + 540) % 360 - 180;
    animateToAngle(currentAngle.value + diff);
};

const nextUniverse = () => rotateToUniverse((activeIndex.value + 1) % totalUniverses);
const prevUniverse = () => rotateToUniverse((activeIndex.value - 1 + totalUniverses) % totalUniverses);

const animateToAngle = (target) => {
    if (rafId) cancelAnimationFrame(rafId);
    const start = currentAngle.value;
    const delta = target - start;
    const duration = 500;
    const startTime = performance.now();

    const step = (now) => {
        const elapsed = now - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const ease = 1 - Math.pow(1 - progress, 3);
        currentAngle.value = start + delta * ease;
        updateActiveFromAngle();

        if (progress < 1) {
            rafId = requestAnimationFrame(step);
        } else {
            currentAngle.value = target;
            updateActiveFromAngle();
        }
    };
    rafId = requestAnimationFrame(step);
};

const enterUniverse = (genreId) => {
    router.visit(`/catalogue?genre=${encodeURIComponent(genreId)}`);
};

// ─── Drag Gestures avec garde de clic ────────────────────────────────────────
const onPointerDown = (e) => {
    if (e.target.closest('button, a')) return;
    isDragging.value = true;
    startX = e.clientX;
    lastX = e.clientX;
    velocity = 0;
    if (rafId) cancelAnimationFrame(rafId);
    stageRef.value?.setPointerCapture?.(e.pointerId);
};

const onPointerMove = (e) => {
    if (!isDragging.value) return;
    const dx = e.clientX - lastX;
    velocity = dx * 0.22;
    currentAngle.value += velocity;
    lastX = e.clientX;
    updateActiveFromAngle();
};

const onPointerUp = (e) => {
    if (!isDragging.value) return;
    isDragging.value = false;
    stageRef.value?.releasePointerCapture?.(e.pointerId);

    const applyMomentum = () => {
        if (Math.abs(velocity) > 0.08) {
            currentAngle.value += velocity;
            velocity *= 0.95;
            updateActiveFromAngle();
            rafId = requestAnimationFrame(applyMomentum);
        } else {
            const nearest = Math.round(currentAngle.value / angleStep) * angleStep;
            animateToAngle(nearest);
        }
    };
    applyMomentum();
};

const onKeyDown = (e) => {
    if (e.key === 'ArrowLeft') prevUniverse();
    else if (e.key === 'ArrowRight') nextUniverse();
};

onMounted(() => window.addEventListener('keydown', onKeyDown));
onUnmounted(() => {
    window.removeEventListener('keydown', onKeyDown);
    if (rafId) cancelAnimationFrame(rafId);
});
</script>

<template>
    <section class="relative w-full pt-28 pb-24 px-4 sm:px-6 lg:px-8 bg-gray-950 text-white overflow-hidden select-none border-y border-gray-800/60">
        <!-- Ambiance background -->
        <div class="absolute inset-0 pointer-events-none">
            <div
                class="absolute inset-0 transition-all duration-700 ease-out"
                :style="{
                    background: `radial-gradient(ellipse 65% 55% at 50% 45%, ${currentUniverse.accentColor}18 0%, rgba(10,10,16,0.95) 75%)`
                }"
            ></div>
            <div class="absolute inset-0 opacity-[0.025]" style="background-image: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(255,255,255,0.4) 2px, rgba(255,255,255,0.4) 3px);"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto flex flex-col items-center">
            <!-- Header -->
            <div class="text-center max-w-2xl mx-auto mb-16 sm:mb-20">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold tracking-widest uppercase mb-3 border border-red-500/20 bg-red-950/30 text-[#BC002D]">
                    <Sparkles class="w-3.5 h-3.5" />
                    <span>PORTAILS DIMENSIONNELS</span>
                </div>
                <h2 class="font-gang text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-wide text-white">
                    EXPLOREZ PAR UNIVERS MANGA
                </h2>
            </div>

            <!-- Cylindre 3D Stage -->
            <div
                ref="stageRef"
                class="stage-container relative w-full h-[460px] sm:h-[500px] flex items-center justify-center cursor-grab active:cursor-grabbing"
                @pointerdown="onPointerDown"
                @pointermove="onPointerMove"
                @pointerup="onPointerUp"
                @pointercancel="onPointerUp"
            >
                <div
                    class="cylinder-ring"
                    :style="{
                        transform: `rotateY(${currentAngle}deg)`,
                        transition: isDragging ? 'none' : 'transform 0.05s linear',
                    }"
                >
                    <div
                        v-for="(u, idx) in universes"
                        :key="u.id"
                        class="portal-face absolute top-1/2 left-1/2 rounded-2xl overflow-hidden"
                        :style="getFaceStyle(idx)"
                    >
                        <div class="relative w-full h-full p-6 flex flex-col justify-between bg-gradient-to-b from-gray-900/90 to-gray-950/95 border border-white/10 rounded-2xl backdrop-blur-md">
                            <!-- Header face -->
                            <div class="flex items-center justify-between">
                                <span class="text-2xl">{{ u.icon }}</span>
                                <span class="font-gang text-4xl text-white/20 select-none">{{ u.kanji }}</span>
                            </div>

                            <!-- Body face -->
                            <div class="flex-1 flex flex-col justify-center my-4">
                                <span class="text-[11px] font-bold tracking-widest text-[#BC002D] uppercase mb-1">{{ u.tagline }}</span>
                                <h3 class="font-sans text-2xl font-extrabold text-white mb-2">{{ u.name }}</h3>
                                <p class="text-gray-400 text-xs leading-relaxed line-clamp-3">{{ u.description }}</p>
                            </div>

                            <!-- CTA face -->
                            <button
                                @click.stop="enterUniverse(u.id)"
                                class="w-full py-2.5 px-4 rounded-xl text-xs font-bold flex items-center justify-center gap-2 bg-[#BC002D] hover:bg-red-700 text-white transition-colors duration-200 shadow-lg shadow-red-950/50"
                            >
                                <span>Explorer l'univers</span>
                                <ArrowUpRight class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barre de navigation détachée (mt-28) -->
            <div class="mt-28 flex flex-col lg:flex-row items-center gap-6 w-full max-w-4xl justify-between px-4">
                <div class="flex items-center gap-3">
                    <button @click="prevUniverse" class="p-3.5 rounded-full bg-gray-900 border border-gray-800 text-gray-300 hover:text-white hover:border-[#BC002D] transition">
                        <ChevronLeft class="w-6 h-6" />
                    </button>
                    <span class="text-sm font-bold text-gray-400">
                        {{ activeIndex + 1 }} / {{ totalUniverses }}
                    </span>
                    <button @click="nextUniverse" class="p-3.5 rounded-full bg-gray-900 border border-gray-800 text-gray-300 hover:text-white hover:border-[#BC002D] transition">
                        <ChevronRight class="w-6 h-6" />
                    </button>
                </div>

                <!-- Pills de sélection directe -->
                <div class="flex flex-wrap items-center justify-center gap-2">
                    <button
                        v-for="(u, idx) in universes"
                        :key="u.id"
                        @click="rotateToUniverse(idx)"
                        class="px-4 py-2 rounded-xl text-sm font-bold transition-all duration-300"
                        :class="idx === activeIndex ? 'bg-[#BC002D] text-white shadow-md shadow-red-900/40' : 'bg-gray-900/80 text-gray-400 hover:text-white border border-gray-800'"
                    >
                        {{ u.name }}
                    </button>
                </div>

                <!-- CTA général -->
                <Link
                    :href="`/catalogue?genre=${encodeURIComponent(currentUniverse.id)}`"
                    class="px-6 py-3 rounded-xl text-sm font-extrabold uppercase tracking-wider bg-white text-gray-950 hover:bg-gray-200 transition flex items-center gap-2 shrink-0"
                >
                    <span>VOIR TOUT EN {{ currentUniverse.name }}</span>
                    <ArrowUpRight class="w-4 h-4" />
                </Link>
            </div>
        </div>
    </section>
</template>

<style scoped>
.stage-container {
    perspective: 1200px;
    perspective-origin: 50% 50%;
}

.cylinder-ring {
    position: relative;
    width: 0;
    height: 0;
    transform-style: preserve-3d;
    will-change: transform;
}

.portal-face {
    width: 300px;
    height: 420px;
    margin-left: -150px;
    margin-top: -210px;
    transform-style: preserve-3d;
    backface-visibility: hidden;
    will-change: transform, opacity, filter;
}
</style>
```
