<script lang="ts" setup>
import Modal from '@/Components/Modal.vue';
import MainButton from '@/Components/MainButton.vue';
import { ref } from 'vue';
import Paginate from '@/Components/Paginate.vue';
import { usePage } from '@inertiajs/vue3';

defineProps<{
    show:boolean
}>()

const index = ref(0)
const examenResults=ref(usePage().props.auth.examen_results)
const wrongAnswers =ref([])

const returnedAnswers=((answers)=>{
    wrongAnswers.value=answers
})
</script>
<template>
<Modal :show="show"  maxWidth="5xl" >
    <h1 class="font-semibold text-center mt-4 text-3xl">Examen Results</h1>
    <div class="max-w-3xl mx-auto mt-8 w-full block">
        <div class="flex items-center justify-center">
            <MainButton v-for="(item,idx) in examenResults" :color="index==idx ? 'blue-400' : 'gray-800'" @click="index=idx" >{{ (idx+1)+(idx+1==3 ?  'rd' : 'st') }} Exam</MainButton>
        </div>
        <!-- {{ examenResults[index] }} -->
        <div class="flex flex-wrap items-center justify-center gap-5 md:gap-10 m-3 mt-10">
            <div class="text-sm">
                <span
                class="bg-success align-middle w-4 h-4 inline-block mr-1 -mt-1"
                ></span>
                Right Answers
            </div>

            <div class="text-sm">
                <span
                class="bg-danger align-middle w-4 h-4 inline-block mr-1 -mt-1"
                ></span>
                Wrong Answers
            </div>
          </div>
        <div class="flex w-full min-h-[60px] my-5">
              <span
                :class="'rounded-l p-3 text-center leading-10 mr-1 bg-success text-white w-['+(examenResults[index].score>50 ? '100' : examenResults[index].score) +'%]'"
                >{{ examenResults[index].score }}%
              </span>
              <span
                :class="'text-center p-3 leading-10 mr-1 bg-danger text-white w-['+(100-examenResults[index].score)+'%]' "
                >{{ 100-examenResults[index].score }}%
              </span>
            </div>
        <table class="w-full min-w-[640px] table-auto bg-white border rounded-l mt-4">
            <thead>
                <tr>
                <th class="border-b border-blue-gray-50 py-3 px-6 text-left">
                    <p class="block  text-center antialiased font-sans text-[11px]  font-semibold uppercase text-gray-800">Your answer</p>
                </th>
                <th class="border-b border-blue-gray-50 py-3 px-6 text-left">
                    <p class="block antialiased text-center font-sans text-[11px] font-semibold uppercase text-gray-800">The right answer</p>
                </th>
                <th class="border-b border-blue-gray-50 py-3 px-6 text-left">
                    <p class="block antialiased text-center font-sans text-[11px] font-semibold uppercase text-gray-800">transaltion</p>
                </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="answer in wrongAnswers">
                    <td class="py-3 px-5 border-b border-blue-gray-50">
                        <div class="flex items-center justify-center gap-4">
                        <p class="block text-center antialiased font-sans text-sm leading-normal  text-danger font-bold">{{ answer.answer==null ? 'None' : answer.answer }}</p>
                        </div>
                    </td>
                    <td class="py-3 px-5 border-b border-blue-gray-50">
                        <div class="flex items-center justify-center gap-4">
                        <p class="block text-center antialiased font-sans text-sm leading-normal text-success font-bold">{{ answer.right_answer }}</p>
                        </div>
                    </td>
                    <td class="py-3 px-5 border-b border-blue-gray-50">
                        <div class="flex items-center justify-center gap-4">
                        <p class="block text-center antialiased font-sans text-sm leading-normal text-blue-600 font-bold">{{ answer.translation_answer }}</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <Paginate :data="examenResults[index].wrong_answers" :key="index.toString()" @returnedData="returnedAnswers" />
    </div>
</Modal>
</template>
