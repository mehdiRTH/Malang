<script lang="ts" setup>
import { faClose } from '@fortawesome/free-solid-svg-icons'

defineProps<{
    error:string | undefined
}>()
</script>
<template>
    <span v-if="error!=null && error!=''" class="text-tertiary bg-white p-0.5 my-8 ml-1 px-3 w-full rounded-full">
        <faIcon :icon="faClose" class="mr-2 mt-4" />
        {{ error }}
    </span>
</template>
