<script lang="ts" setup>
import { faClose } from '@fortawesome/free-solid-svg-icons'
import MainInputError from '@/Components/MainInputError.vue'

defineProps<{
    label:string,
    error?:string,
    text_color?:string
}>()
</script>
<template>
    <div>
        <label :class="'dark:text-gray-200 mb-4 text-'+(text_color ?? 'white')">{{label}}</label>
        <div class="block w-full mt-2">
            <slot />
        </div>
        <MainInputError :error="error" />
    </div>
</template>
