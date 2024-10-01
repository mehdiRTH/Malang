<script setup lang="ts">
import { MetaInterface } from '@/types/MetaInterface';
import { Link } from '@inertiajs/vue3';
import { faArrowRightLong, faArrowLeftLong } from '@fortawesome/free-solid-svg-icons';

defineProps<{
    metaData:MetaInterface
}>()
</script>
<template>

    <div v-if="metaData.per_page < metaData.total" class="flex items-center justify-between mt-6 pb-12">
        <Link v-if="metaData.links[0].url" :href="metaData.links[0].url"
            class="flex items-center px-5 py-2 text-sm text-gray-700 capitalize transition-colors duration-200 bg-white border rounded-md gap-x-2 hover:bg-gray-100 hover:bg-transparent dark:bg-gray-900 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-800">
            <!--Left Icon-->
            <faIcon :icon="faArrowLeftLong" class="w-3 h-3 rtl:-scale-x-100" />
            <span>
                Previous
            </span>
        </Link>
        <div v-else
            class="flex items-center px-5 py-2 text-sm text-gray-700 capitalize transition-colors duration-200 bg-white border rounded-md gap-x-2 hover:bg-gray-100 hover:bg-transparent dark:bg-gray-900 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-800">
            <!--Left Icon-->
            <faIcon :icon="faArrowLeftLong" class="w-3 h-3 text-black rtl:-scale-x-100" />
            <span>
                Previous
            </span>
        </div>

        <div class="items-center hidden md:flex gap-x-3">
            <div v-for="meta in metaData.links">
                <div v-if="!(meta.label.includes('Previous') || meta.label.includes('Next'))">
                    <Link href="#" v-if="meta.active"
                        class="px-2 py-1 text-sm text-blue-500 rounded-md dark:bg-gray-800 bg-blue-100/60">{{ meta.label
                    }}</Link>
                    <Link :href="meta.url ?? ''" v-else
                        class="px-2 py-1 text-sm text-gray-500 rounded-md dark:hover:bg-gray-800 dark:text-gray-300 hover:bg-gray-100">
                    {{ meta.label }}</Link>
                </div>
            </div>
        </div>

        <Link v-if="metaData.links[metaData.links.length-1]?.url"
            :href="metaData.links[metaData.links.length-1].url ?? ''"
            class="flex items-center px-5 py-2 text-sm text-gray-700 capitalize transition-colors duration-200 bg-white border rounded-md gap-x-2 hover:bg-transparent hover:bg-gray-100 dark:bg-gray-900 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-800">
            <span>
                Next
            </span>
            <!--Left Icon-->
            <faIcon :icon="faArrowRightLong" class="w-3 h-3 rtl:-scale-x-100" />
        </Link>
        <div v-else
            class="flex items-center px-5 py-2 text-sm text-gray-700 capitalize transition-colors duration-200 bg-white border rounded-md gap-x-2 hover:bg-transparent hover:bg-gray-100 dark:bg-gray-900 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-800">
            <span>
                Next
            </span>

            <!--Left Icon-->
            <faIcon :icon="faArrowRightLong" class="w-3 h-3 rtl:-scale-x-100" />
        </div>
    </div>
</template>
