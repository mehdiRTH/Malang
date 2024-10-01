<script setup lang="ts">
import { MetaInterface } from '@/types/MetaInterface';
import { Link } from '@inertiajs/vue3';
import { faArrowRightLong, faArrowLeftLong } from '@fortawesome/free-solid-svg-icons';
import { computed, onMounted, ref, watch } from 'vue';

const props=defineProps<{
    data:array
}>()

const emit=defineEmits(['returnedData'])

const index=ref(0)

const chunk = (arr, size) =>
  Array.from({ length: Math.ceil(arr.length / size) }, (v, i) =>
    arr.slice(i * size, i * size + size)
  );

const currentData=ref(chunk(props.data,5))
onMounted(()=>{
    emit('returnedData',currentData.value[index.value])
})

watch(index,(newVal,oldVal)=>{
    emit('returnedData',currentData.value[index.value])
})
</script>
<template>
    <div class="flex items-center justify-between mt-6 pb-12">
        <button @click="index==0 ? index : index-=1"
            class="flex items-center px-5 py-2 text-sm text-gray-700 capitalize transition-colors duration-200 bg-white border rounded-md gap-x-2 hover:bg-gray-100 hover:bg-transparent dark:bg-gray-900 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-800">
            <faIcon :icon="faArrowLeftLong" class="w-3 h-3 rtl:-scale-x-100" />
            <span>
                Previous
            </span>
        </button>

        <div class="items-center hidden md:flex gap-x-3">
            <div v-for="item in Math.ceil(data.length/5)">
                <div >
                    <button @click="index=item-1"
                        :class="{'text-blue-500 bg-blue-100/60':item==(index+1),'text-gray-500 hover:bg-gray-100': item!=(index+1)}"
                        class="px-2 py-1 text-sm  rounded-md">{{ item }}</button>
                </div>
            </div>
        </div>

        <button @click="index==currentData.length ? index=index : index+=1"
            class="flex items-center px-5 py-2 text-sm text-gray-700 capitalize transition-colors duration-200 bg-white border rounded-md gap-x-2 hover:bg-transparent hover:bg-gray-100 dark:bg-gray-900 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-800">
            <span>
                Next
            </span>
            <faIcon :icon="faArrowRightLong" class="w-3 h-3 rtl:-scale-x-100" />
        </button>
    </div>
</template>
