<script lang="ts" setup>
import { CardsType } from '@/types/CardsType'

defineProps<{
    items:CardsType[]
}>()
</script>
<template>
    <div :class="'mt-2 grid grid-cols-1 gap-4 sm:grid-cols-'+items.length+' lg:grid-cols-'+items.length+' xl:grid-cols-'+items.length">
        <div v-for="item in items" class="flex items-start rounded-xl bg-white p-4 shadow-lg">
            <div :class="'flex h-12 w-12 items-center justify-center rounded-full border border-'+item.color+'-100 bg-'+item.color+'-200'">
                <faIcon :icon="item.icon" :class="'h-6 w-6 text-'+item.color+'-500'"/>
            </div>

            <div class="ml-4">
                <h2 class="font-semibold">{{ item.Label }} </h2>
                <p v-if="item.subLabel" class="mt-2 text-sm text-gray-500">{{ item.subLabel }}</p>
            </div>
        </div>
    </div>
</template>
