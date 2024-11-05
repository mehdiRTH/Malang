<script lang="ts" setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { onMounted, ref, Ref } from 'vue';
import { faCheck } from '@fortawesome/free-solid-svg-icons';
import MainInput from './MainInput.vue';
import MainButton from './MainButton.vue';
import QuizWrongAnswerModal from '@/Pages/Quiz/WrongAnswerModal.vue';
import ExamWrongAnswersModal from '@/Pages/Examens/WrongAnswersModal.vue';
import { QuizComponentEnum } from '@/types/QuizComponentEnum';

interface ExamType{
    id:string
}

interface quizVocabularyType{
    nl:string;
    en:string;
    answer:Object
}

const props=defineProps<{
    componentType:QuizComponentEnum,
    quizType:string,
    examData:Array<quizVocabularyType>,
    exam?:ExamType
}>()


const addedAnswers = ref(props.examData)
const answer : Ref<string>=ref('')
const index : Ref<number>=ref(0)
const subIndex : Ref<number>=ref(0)
const buttonLabel : Ref<string>=ref('Next')
const isFinished : Ref<boolean>=ref(false)
const url  = new URL(window.location)
const openWrongAnswer : Ref<boolean> = ref(false)


const form=useForm({
    quiz_answers:[],
    answers_lang:url?.searchParams.get('answer_language'),
    vocabulary_number:url?.searchParams.get('vocabulary_number'),
    quiz_date:url?.searchParams.get('quiz_date'),
    isDone:false,
    type:props.componentType
})

const examForm=useForm({
    quiz_answer:[] as Array<quizVocabularyType>,
    exam:props.exam?.id,
    isDone:false,
    globalScore:0
})


const VocabularySubmit=((isDoneParam : boolean)=>{

    form.isDone= isDoneParam ? true : false

    form.post(route('quiz.check_answers'),{
            preserveState:true,
            onSuccess: () => {
            isFinished.value = true;
        }
        })
})

const indexingVocabularies =(()=>{
    let lastQuizLength=props.examData.length-1;

    if(lastQuizLength-1==index.value) buttonLabel.value='Done'

    // Update the answer for the current index
    addedAnswers.value[index.value].answer=answer.value

    if(lastQuizLength==index.value)
    {
         form.quiz_answers=addedAnswers.value;
        VocabularySubmit(false)
    }else{
        ++index.value;
    }
    answer.value=''
})

const retry=(()=>{
    addedAnswers.value=props.examData
    index.value=0
    subIndex.value=0
    isFinished.value=false
    buttonLabel.value='Next'
})

const indexingExam=(()=>{
    // Update the answer for the current index and subIndex
    props.examData[index.value][subIndex.value]['answer']=answer.value

    if(subIndex.value==props.examData[index.value]?.length-1){
        if(index.value < 3)
        {
            index.value++;
            subIndex.value=0;
        }else{
            examForm.quiz_answer=props.examData
            submitExam(false)
        }
    }
    else{
        subIndex.value++;
    }
    answer.value=''
})

const submitExam=((isDoneParam : boolean)=>{

    examForm.isDone= isDoneParam ? true : false
    examForm.globalScore=calculateExamScore()

    examForm.post(route('exams.check_answers'),{
    preserveState:true,
    onSuccess:(()=>{
        isFinished.value=true
        })
    })
})

const calculateExamScore=(()=>{
    let totalScore=0;
    usePage().props.auth.examen_results.map((item : number)=>totalScore+=parseInt(item.score))

    return Math.ceil(totalScore/4);
})

const switchTypes=(()=>{
    props.componentType=='Vocabulary' ? indexingVocabularies()  : indexingExam()
})

const typeScoreRate =(()=>{
    return props.componentType=='Vocabulary' ? usePage().props.auth.quiz_percentage : calculateExamScore()
})


const handleBeforeUnload=((event : any)=>{
    event.preventDefault();
    event.returnValue="Are you sure to leave site?"
})

onMounted(()=>{
    window.onbeforeunload=handleBeforeUnload
})
</script>
<template>
    <div class="py-12">
            <div v-if="!isFinished" class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div v-if="componentType=='Exam'" class="flex items-center justify-between text-base text-gray-600 dark:text-gray-400 mb-4">
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
                        <div v-if="componentType=='Vocabulary'" class="bg-white p-4 rounded-full text-black font-bold">{{ (index+1)+'/'+examData?.length }}</div>
                        <div v-else class="bg-white p-4 rounded-full text-black font-bold">{{ (subIndex+1)+'/'+examData[index]?.length }}</div>
                    </div>
                    <div class="mt-7">

                        <h2 class="text-center">Vocabulary in {{ form.answers_lang=='nl' ? 'English' : 'Dutch' }}</h2>
                        <h1 v-if="componentType=='Vocabulary'" class="text-center text-4xl font-semibold">{{ form.answers_lang=='nl' ? examData[index]?.en : examData[index]?.nl }}</h1>
                        <h1 v-else class="text-center text-4xl font-semibold">{{ examData[index][subIndex]?.en }}</h1>

                        <div class="my-6">
                            <h2 class="text-center">Answer with {{ form.answers_lang=='nl' ? 'Dutch' : 'English' }}</h2>
                            <div class="flex items-center justify-center">
                                <MainInput v-model="answer" label="" error="" @keyup.enter="switchTypes" />
                            </div>
                        </div>
                        <div class="flex items-center justify-center ">
                            <MainButton @click="switchTypes" color="blue-600">{{ buttonLabel }}</MainButton>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="flex items-center justify-center bg-gray-100">
                <div class="rounded-lg bg-gray-50 px-16 py-14">
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
                <ExamWrongAnswersModal v-else :show="openWrongAnswer" :key="openWrongAnswer.toString()+''" @close="openWrongAnswer=false" />
            </div>
        </div>
</template>
