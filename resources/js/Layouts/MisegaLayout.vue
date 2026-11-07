<script setup>
import MisegaLoader from '@/Components/MisegaLoader.vue';
import LiquidGlassNavbar from '@/Components/LiquidGlassNavbar.vue';
import CylinderFooter from '@/Components/CylinderFooter.vue';
</script>

<template>
    <div class="min-h-screen bg-white text-gray-900 flex flex-col font-sans selection:bg-primary selection:text-white relative overflow-x-clip">
        <!-- Fullscreen 3D Manga Loader -->
        <MisegaLoader />

        <!-- 3D Liquid Glass Morphing Floating Navbar -->
        <LiquidGlassNavbar />

        <!-- Main Content -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- 3D Cylinder Tunnel Footer -->
        <CylinderFooter />
    </div>
</template>
