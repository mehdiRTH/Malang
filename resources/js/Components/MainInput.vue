<script lang="ts" setup>
import MainInputError from '@/Components/MainInputError.vue'

defineProps<{
    label:string,
    modelValue:string,
    placeholderText?:string,
    inputType?:string,
    error:string | undefined
}>()
</script>
<template>
    <div>
        <div v-if="inputType=='textarea'">
            <label class="text-white dark:text-gray-200" for="passwordConfirmation">{{label}}</label>
            <textarea :value="modelValue" @input="$emit('update:modelValue',($event.target as HTMLInputElement).value)" rows="4"
                :placeholder="placeholderText"
                class="block w-full px-4 py-2 mt-2 text-gray-700 bg-white border border-gray-300 rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-500 dark:focus:border-blue-500 focus:outline-none focus:ring"></textarea>
        </div>
        <div v-else>
            <label class="text-white dark:text-gray-200" for="password">{{label}}</label>
            <input :type="inputType" :value="modelValue"
                @input="$emit('update:modelValue',($event.target as HTMLInputElement).value)"
                class="block w-full px-4 py-2 mt-2 text-gray-700 bg-white border border-gray-300 rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-500 dark:focus:border-blue-500 focus:outline-none focus:ring">
        </div>
        <!-- <div v-else>
            <label class="text-white dark:text-gray-200">{{label}}</label>
            <input type="text" :value="modelValue"
                @input="$emit('update:modelValue',($event.target as HTMLInputElement).value)"
                :placeholder="placeholderText"
                class="block w-full px-4 py-2 mt-2 text-gray-700 bg-white border border-gray-300 rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-500 dark:focus:border-blue-500 focus:outline-none focus:ring">
        </div> -->
        <MainInputError :error="error" />
    </div>
</template>
