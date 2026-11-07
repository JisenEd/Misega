---
trigger: always_on
---

# Règle d'Or du Motion Design & de la 3D (MISEGA)

1. **Lenteur Contemplative & Fluidité 60 FPS** :
   - Toutes les animations (rotations 3D, défilement, morphing, transitions, lévitation) doivent être **toujours fluides (60 FPS)** mais avec un mouvement **suffisamment lent et smooth** pour que l'utilisateur puisse admirer la trajectoire, la physique 3D et les détails visuels.
   - Ne jamais faire tourner un objet 3D de plusieurs tours rapides : privilégier des arcs de rotation amples, lents et majestueux (ex. 40° à 70° sur une longue durée, et des oscillations de lévitation très lentes sur des cycles de 12 à 16 secondes).

2. **Intégrité du Rendu 3D CSS (`transform-style: preserve-3d`)** :
   - Ne **JAMAIS** appliquer `opacity < 1`, `overflow: hidden` ou `filter: blur()` sur le même élément DOM qui porte `transform-style: preserve-3d` et les rotations 3D (`rotateX`, `rotateY`, `rotateZ`), car la spécification CSS écrase alors l'arbre 3D en un plan 2D plat (`transform-style: flat`).
   - Toujours séparer :
     - un **conteneur externe de positionnement** (`translate3d`, `scale`, `opacity`, `perspective`),
     - et un **objet 3D interne pur** (`transform-style: preserve-3d`, `rotateX`, `rotateY`, `rotateZ`, avec `opacity: 1` constant) contenant les 6 faces physiques du volume.

