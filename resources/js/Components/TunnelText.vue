<script setup>
/**
 * TunnelText — découpe un texte en caractères individuels (.tc)
 * afin que CylinderFooter puisse placer chaque lettre sur la paroi du tunnel.
 * Les mots restent insécables pour que la mise en page finale (à plat) reste propre.
 */
import { computed } from 'vue';

const props = defineProps({
    text: { type: String, required: true },
});

const words = computed(() => props.text.split(' '));
</script>

<template>
    <template v-for="(word, wi) in words" :key="wi"><span class="inline-block whitespace-nowrap"><span v-for="(char, ci) in word" :key="ci" class="tc inline-block">{{ char }}</span></span>{{ wi < words.length - 1 ? ' ' : '' }}</template>
</template>

