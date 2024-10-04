<script lang="ts" setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';
import MainButton from '@/Components/MainButton.vue';
import MainInput from '@/Components/MainInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { faCheck } from '@fortawesome/free-solid-svg-icons';
import WrongAnswersModal from '../WrongAnswersModal.vue';

const props=defineProps<{
    dividedVocabularies:Array,
    exam:Array
}>()

const index =ref(0)
const subIndex =ref(0)
const currentVocabularies = ref(props.dividedVocabularies.data)
const answer =ref('')

const calculateScore=(()=>{
    let totalScore=0;
    usePage().props.auth.examen_results.map((item : number)=>totalScore+=parseInt(item.score))

    return Math.ceil(totalScore/4);
})

const form=useForm({
    quiz_answer:[],
    exam:props.exam.id,
    isDone:false,
    globalScore:calculateScore()
})

const isFinished =ref(false)
const openWrongAnswer=ref(false)

const indexing=(()=>{

    if(subIndex.value==currentVocabularies.value[index.value]?.length-1){
        currentVocabularies.value[index.value][subIndex.value]['answer']=answer.value
        if(index.value < 3)
        {
            index.value++;
            subIndex.value=0;
        }else{
            form.quiz_answer=currentVocabularies.value
            submit(false)
        }
    }
    else{
        currentVocabularies.value[index.value][subIndex.value]['answer']=answer.value
        subIndex.value++;
    }
    answer.value=''
})

const submit=((isDoneParam : boolean)=>{

form.isDone= isDoneParam ? true : false

form.post(route('examen.check_answers'),{
        preserveState:true,
        onSuccess:(()=>{
            isFinished.value=true
        })
    })
})


</script>
<template>
    <AuthenticatedLayout label="Exam">
        <div class="py-12">
            <!-- component -->
            <div v-if="!isFinished" class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="flex items-center justify-between text-base text-gray-600 dark:text-gray-400 mb-4">
                    <div class="flex items-center">
                        <div :class="{'bg-info':index==0,'bg-success':index>0}" class="w-12 h-12 rounded-full flex items-center justify-center text-white text-xl">
                        1
                        </div>
                    </div>
                    <div class="h-1 w-16 bg-indigo-200 dark:bg-indigo-600"></div>
                    <div class="flex items-center">
                        <div :class="{'bg-info':index==1,'bg-success':index>1,'bg-gray-800':index<1}" class="w-12 h-12 rounded-full flex items-center justify-center text-white text-xl">
                        2
                        </div>
                    </div>
                    <div class="h-1 w-16 bg-blue-200 dark:bg-blue-600"></div>
                    <div class="flex items-center">
                        <div :class="{'bg-info':index==2,'bg-success':index>2,'bg-gray-800':index<2}" class="w-12 h-12 rounded-full flex items-center justify-center text-white text-xl">
                        3
                        </div>
                    </div>
                    <div class="h-1 w-16 bg-blue-200 dark:bg-blue-600"></div>
                    <div class="flex items-center">
                        <div :class="{'bg-info':index==3,'bg-gray-800':index<3}" class="w-12 h-12 rounded-full flex items-center justify-center text-white text-xl">
                        4
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 overflow-hidden sm:rounded-lg text-white py-7">
                    <h1 class="text-center font-semibold text-2xl">Practice Your Inserted vocabularies</h1>
                    <div class="flex justify-center mt-2">
                        <div class="bg-white p-4 rounded-full text-black font-bold">{{ (subIndex+1)+'/'+currentVocabularies[index]?.length }}</div>
                    </div>
                    <div class="mt-7">

                        <h2 class="text-center">Vocabulary in English</h2>
                        <h1 class="text-center text-4xl font-semibold">{{ currentVocabularies[index][subIndex]?.en }}</h1>

                        <div class="my-6">
                            <h2 class="text-center">Answer with Dutch</h2>
                            <div class="flex items-center justify-center">
                                <MainInput v-model="answer" label="" error="" />
                            </div>
                        </div>
                        <div class="flex items-center justify-center ">
                            <MainButton @click="indexing" color="red-600">Next</MainButton>
                        </div>
                    </div>

                </div>
            </div>
            <div v-else class="flex items-center justify-center bg-gray-100">
                <div class="rounded-lg bg-gray-50 px-16 py-14">
                    <div class="flex justify-center">
                        <div :class="{
                            'bg-green-200':calculateScore()>=80,
                            'bg-orange-200':calculateScore()<=80 && calculateScore()>=40,
                            'bg-red-200':calculateScore()<40,
                            }" class="rounded-full  p-6">
                            <div :class="{
                            'bg-green-500':calculateScore()>=80,
                            'bg-orange-500':calculateScore()<=80 && calculateScore()>=40,
                            'bg-red-500':calculateScore()<40,
                            }" class="flex h-16 w-16 items-center justify-center rounded-full p-4">
                                <faIcon class="h-8 w-8 text-white" :icon="faCheck" />
                            </div>
                        </div>
                    </div>
                    <h3 class="my-4 text-center text-3xl font-semibold text-gray-700">Congratuation!!!</h3>
                    <p class="text-center font-normal text-gray-600">Your Score is {{ calculateScore() }}%</p>
                    <div class="inline-flex">
                        <!-- <button @click="retry" type="button" class="mx-1 mt-10 block rounded-xl border-4 border-transparent bg-info px-6 py-3 text-center text-base font-medium text-orange-100 outline-8 hover:outline hover:duration-300">
                            Practice Again
                        </button> -->
                        <button type="button" @click="openWrongAnswer=!openWrongAnswer" class="mx-1 mt-10 block rounded-xl border-4 border-transparent bg-blue-600 px-6 py-3 text-center text-base font-medium text-white outline-8 hover:outline hover:duration-300">
                            OpenYour Wrong Answer
                        </button>
                        <button type="submit" @click="submit(true)" class="mx-1 mt-10 block rounded-xl border-4 border-transparent bg-success px-6 py-3 text-center text-base font-medium text-white outline-8 hover:outline hover:duration-300">
                            Save My Score
                        </button>
                    </div>
                </div>

                <WrongAnswersModal :show="openWrongAnswer" :key="openWrongAnswer.toString()" @close="openWrongAnswer=false" />
            </div>
        </div>
    </AuthenticatedLayout>

</template>
<style>

</style>
