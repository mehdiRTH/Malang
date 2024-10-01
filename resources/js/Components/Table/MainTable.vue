<script lang="ts" setup>
import { MetaInterface } from "@/types/MetaInterface";
import Paginate from "@/Components/Table/Paginate.vue"
import { faAdd } from '@fortawesome/free-solid-svg-icons';
import { Link } from '@inertiajs/vue3';

defineProps<{
    label:string,
    create_route?:string | null,
    meta?:MetaInterface,
    isEmpty?:boolean
}>();

const emits=defineEmits(['click_create'])

</script>
<template>
    <div class="mb-4 mt-8 block items-center justify-center w-full">
            <div class="relative flex flex-col bg-clip-border rounded-xl bg-white text-gray-700 shadow-md overflow-hidden xl:col-span-2">
            <div class="relative bg-clip-border rounded-xl overflow-hidden bg-transparent text-gray-700 shadow-none m-0 flex items-center justify-between p-6">
                <div>
                <h6 class="block antialiased tracking-normal font-sans text-base font-semibold leading-relaxed text-blue-gray-900 mb-1">{{ label }}</h6>
                <!-- <p class="antialiased font-sans text-sm leading-normal flex items-center gap-1 font-normal text-blue-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" aria-hidden="true" class="h-4 w-4 text-blue-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path>
                    </svg>
                    <strong>30 done</strong> this month
                </p> -->
                </div>
                <Link v-if="create_route" :href="create_route"  aria-expanded="false" aria-haspopup="menu" id=":r5:" class="relative middle none font-sans font-medium text-center transition-all disabled:opacity-50 disabled:shadow-none disabled:pointer-events-none rounded-lg text-xs text-blue-gray-500 hover:bg-blue-gray-500/10 active:bg-blue-gray-500/30" type="button">
                    <span class="inline-flex items-center justify-center bg-gray-800 py-2 px-2.5 rounded-full text-white">
                            <faIcon :icon="faAdd" /> &nbsp; Create
                    </span>
                </Link>
                <button v-else @click="emits('click_create')" aria-expanded="false" aria-haspopup="menu" id=":r5:" class="relative middle none font-sans font-medium text-center transition-all disabled:opacity-50 disabled:shadow-none disabled:pointer-events-none rounded-lg text-xs text-blue-gray-500 hover:bg-blue-gray-500/10 active:bg-blue-gray-500/30" type="button">
                <span class="inline-flex items-center justify-center bg-gray-800 py-2 px-2.5 rounded-full text-white">
                        <faIcon :icon="faAdd" /> &nbsp; Create
                </span>
                </button>
            </div>
            <div class="p-6 overflow-x-scroll px-0 pt-0 pb-2">
                <table class="w-full min-w-[640px] table-auto">
                <thead>
                    <tr>
                        <slot name="ThTable" />
                    </tr>
                </thead>
                <tbody>
                    <slot name="TdTable" />
                </tbody>
                </table>

            </div>

            </div>
            <Paginate v-if="meta" :metaData="meta" />
      </div>
</template>
