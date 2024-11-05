<script lang="ts" setup>
import QuizWrongAnswerModal from '@/Pages/Quiz/WrongAnswerModal.vue';
import ExamWrongAnswersModal from '@/Pages/Examens/WrongAnswersModal.vue';
import { usePage } from '@inertiajs/vue3';
import { faCheck } from '@fortawesome/free-solid-svg-icons';
import { ref, Ref } from 'vue';


const props=defineProps({
    componentType : String
})

const openWrongAnswer : Ref<boolean> = ref(false)


const calculateExamScore=(()=>{
    let totalScore=0;
    usePage().props.auth.examen_results.map((item : number)=>totalScore+=parseInt(item.score))

    return Math.ceil(totalScore/4);
})


const typeScoreRate =(()=>{
    return props.componentType=='Vocabulary' ? usePage().props.auth.quiz_percentage : calculateExamScore()
})

// const retry=(()=>{
//     addedAnswers.value=props.examData
//     index.value=0
//     subIndex.value=0
//     isFinished.value=false
//     buttonLabel.value='Next'
// })
</script>
<template>
    <div class="flex items-center justify-center bg-gray-100">
        <!-- <div class="rounded-lg bg-gray-50 px-16 py-14">
            <div class="flex justify-center">
                <div :class="{
                    'bg-green-200':typeScoreRate()>=80,
                    'bg-orange-200':typeScoreRate()<=80 && typeScoreRate()>=40,
                    'bg-red-200':typeScoreRate()<40,
                    }" class="rounded-full  p-6">
                <div :class="{
                    'bg-green-500':typeScoreRate()>=80,
                    'bg-orange-500':typeScoreRate()<=80 && typeScoreRate()>=40,
                    'bg-red-500':typeScoreRate()<40,
                    }" class="flex h-16 w-16 items-center justify-center rounded-full p-4">
                        <faIcon class="h-8 w-8 text-white" :icon="faCheck" />
                    </div>
                </div>
            </div>
            <h3 class="my-4 text-center text-3xl font-semibold text-gray-700">Congratuation!!!</h3>
            <p class="text-center font-normal text-gray-600">Your Score is {{ typeScoreRate() }}%</p>
            <div class="inline-flex">
                <button @click="retry" type="button" class="mx-1 mt-10 block rounded-xl border-4 border-transparent bg-info px-6 py-3 text-center text-base font-medium text-orange-100 outline-8 hover:outline hover:duration-300">
                    Practice Again
                </button>
                <button type="button" @click="openWrongAnswer=!openWrongAnswer" class="mx-1 mt-10 block rounded-xl border-4 border-transparent bg-blue-600 px-6 py-3 text-center text-base font-medium text-white outline-8 hover:outline hover:duration-300">
                    OpenYour Wrong Answer
                </button>
                <button type="submit" @click="componentType=='Vocabulary' ? VocabularySubmit(true) : submitExam(true)" class="mx-1 mt-10 block rounded-xl border-4 border-transparent bg-success px-6 py-3 text-center text-base font-medium text-white outline-8 hover:outline hover:duration-300">
                    Save My Score
                </button>
            </div>
        </div>
        <QuizWrongAnswerModal v-if="componentType=='Vocabulary'" :show="openWrongAnswer" :key="openWrongAnswer.toString()" @close="openWrongAnswer=false" />
        <ExamWrongAnswersModal v-else :show="openWrongAnswer" :key="openWrongAnswer.toString()+''" @close="openWrongAnswer=false" /> -->
    </div>
</template>
