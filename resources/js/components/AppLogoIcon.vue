<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 40 40"
        :class="className"
        v-bind="$attrs"
        fill="none"
        aria-hidden="true"
    >
        <path
            d="M10 8.5h11l7 7V31.5H10z"
            stroke="currentColor"
            stroke-width="2.3"
            stroke-linejoin="round"
        />
        <path
            d="M21 8.5v7h7M14.5 21h9M14.5 25.5h6"
            stroke="currentColor"
            stroke-width="2.3"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
        <path
            d="m23.5 28 2.1 2.1 4.4-5"
            stroke="#86efac"
            stroke-width="2.4"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
    </svg>
</template>
