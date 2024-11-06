<script lang="ts" setup>
import { faCheck } from '@fortawesome/free-solid-svg-icons';

const props=defineProps({
    score : Number
})

</script>
<template>
    <div class="flex items-center justify-center bg-gray-100">
        <div class="rounded-lg bg-gray-50 px-16 py-14">
            <div class="flex justify-center">
                <div :class="{
                    'bg-green-200':score >= 80,
                    'bg-orange-200':score <=80 && score>=40,
                    'bg-red-200':score<40,
                    }" class="rounded-full  p-6">
                <div :class="{
                    'bg-green-500':score>=80,
                    'bg-orange-500':score<=80 && score>=40,
                    'bg-red-500':score<40,
                    }" class="flex h-16 w-16 items-center justify-center rounded-full p-4">
                        <faIcon class="h-8 w-8 text-white" :icon="faCheck" />
                    </div>
                </div>
            </div>
            <h3 class="my-4 text-center text-3xl font-semibold text-gray-700">Congratuation!!!</h3>
            <p class="text-center font-normal text-gray-600">Your Score is {{ score }}%</p>
            <div class="inline-flex">
                <button @click="$emit('retry')" type="button" class="mx-1 mt-10 block rounded-xl border-4 border-transparent bg-info px-6 py-3 text-center text-base font-medium text-orange-100 outline-8 hover:outline hover:duration-300">
                    Practice Again
                </button>
                <button type="button" @click="$emit('wrongAnswer')" class="mx-1 mt-10 block rounded-xl border-4 border-transparent bg-blue-600 px-6 py-3 text-center text-base font-medium text-white outline-8 hover:outline hover:duration-300">
                    OpenYour Wrong Answer
                </button>
                <button type="submit" @click="$emit('saveScore')" class="mx-1 mt-10 block rounded-xl border-4 border-transparent bg-success px-6 py-3 text-center text-base font-medium text-white outline-8 hover:outline hover:duration-300">
                    Save My Score
                </button>
            </div>
        </div>
        <slot />
    </div>
</template>
