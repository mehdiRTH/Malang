<script lang="ts" setup>
import MainButton from './MainButton.vue';

const props=defineProps<{
    errors:Object,
    label:string,
    btnLabel:string
}>()

const emits=defineEmits(['submit'])

function isErrorsEmpty():boolean
{

    return props.errors ? Object.keys(props.errors).length==0 : true
}
</script>
<template>
    <section class="max-w-6xl p-6 mx-auto bg-primary rounded-md dark:bg-gray-800 my-10">
            <h1 class="text-xl font-bold text-white capitalize dark:text-white">{{ label }}</h1>
            <form @submit.prevent="$emit('submit')">
                <div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-2">
                    <slot />
                </div>
                <div class="flex justify-end mt-6">
                    <MainButton :color="'white'" :text-color="isErrorsEmpty() ? 'primary' : 'tertiary'" type="submit">{{ btnLabel }}</MainButton>
                </div>
            </form>
    </section>
</template>
