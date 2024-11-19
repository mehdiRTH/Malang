<script lang="ts" setup>
import { CardsType } from '@/types/CardsType'
import { Ref, ref } from 'vue';
import { Chart, Grid, Line } from 'vue3-charts'

const props=defineProps<{
    items:CardsType[]
}>()

const mainStyle : Ref<string> = ref('mt-2 grid grid-cols-'+props.items.length+' gap-4')
</script>
<template>
    <div :class="mainStyle">
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
