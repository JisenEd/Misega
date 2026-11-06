<script setup>
import MisegaLoader from '@/Components/MisegaLoader.vue';
import LiquidGlassNavbar from '@/Components/LiquidGlassNavbar.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-white text-gray-900 flex flex-col font-sans selection:bg-primary selection:text-white relative overflow-x-hidden">
        <!-- Fullscreen 3D Manga Loader -->
        <MisegaLoader />

        <!-- 3D Liquid Glass Morphing Floating Navbar -->
        <LiquidGlassNavbar />

        <!-- Main Content -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 border-t border-gray-700 text-gray-400 text-sm mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <!-- Brand info -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-2">
                            <img src="/img/misega-icon-book-only-transparent.png" alt="Misega" class="h-8 w-8 object-contain" />
                            <span class="font-gang text-2xl font-extrabold text-white">MI<span class="text-primary">SEGA</span></span>
                        </div>
                        <p class="text-xs text-gray-400 leading-relaxed">
                            Votre destination numéro 1 pour la commande de mangas en ligne. Qualité, livraison rapide et passion du manga au quotidien.
                        </p>
                    </div>

                    <!-- Navigation -->
                    <div>
                        <h4 class="font-gang text-white text-base mb-3 tracking-wide">Navigation</h4>
                        <ul class="space-y-2 text-xs">
                            <li><Link href="/catalogue" class="hover:text-primary transition">Catalogue Complet</Link></li>
                            <li><Link href="/catalogue?genre=Shonen" class="hover:text-primary transition font-sans">Shōnen Populaires</Link></li>
                            <li><Link href="/catalogue?genre=Seinen" class="hover:text-primary transition">Nouveautés Seinen</Link></li>
                        </ul>
                    </div>

                    <!-- Services -->
                    <div>
                        <h4 class="font-gang text-white text-base mb-3 tracking-wide">Client</h4>
                        <ul class="space-y-2 text-xs">
                            <li><Link href="/cart" class="hover:text-primary transition">Mon Panier</Link></li>
                            <li><Link href="/orders" class="hover:text-primary transition">Suivi de Commandes</Link></li>
                            <li><a href="#" class="hover:text-primary transition">FAQ & Support</a></li>
                        </ul>
                    </div>

                    <!-- Payment -->
                    <div class="space-y-3">
                        <h4 class="font-gang text-white text-base mb-3 tracking-wide">Paiement Sécurisé</h4>
                        <p class="text-xs text-gray-500">Expédition sous 24h ouvrées. Emballage soigné spécial manga.</p>
                        <div class="flex items-center gap-2 pt-2">
                            <span class="px-2.5 py-1 text-[10px] font-bold bg-gray-800 border border-gray-700 rounded text-gray-300">VISA</span>
                            <span class="px-2.5 py-1 text-[10px] font-bold bg-gray-800 border border-gray-700 rounded text-gray-300">MASTERCARD</span>
                            <span class="px-2.5 py-1 text-[10px] font-bold bg-gray-800 border border-gray-700 rounded text-gray-300">PAYPAL</span>
                        </div>
                    </div>
                </div>

                <div class="mt-12 pt-6 border-t border-gray-800 text-center text-xs text-gray-600 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p>© {{ new Date().getFullYear() }} MISEGA - Tous droits réservés.</p>
                    <p class="font-gang text-gray-500 tracking-wider">L'Art du Manga</p>
                </div>
            </div>
        </footer>
    </div>
</template>
