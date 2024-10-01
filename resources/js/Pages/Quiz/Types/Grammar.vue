<script lang="ts" setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MainInput from '@/Components/MainInput.vue';
import MainButton from '@/Components/MainButton.vue';
import { Ref, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { faCheck } from '@fortawesome/free-solid-svg-icons';
import WrongAnswerModal from '../WrongAnswerModal.vue';

const props=defineProps<{
    quiz_vocabularies:quiz_interface
}>()

interface quiz_interface{
    data : Array<{
        name:string,
        nl:string;
        en:string;
        answer:{
            presens:string | null,
            imperfectum:string | null,
            perfectum:string | null,
            name:string};
    }>
}

const addedAnswers  = ref(props.quiz_vocabularies.data)
const answers : Ref<string> = ref('')
const index : Ref<number> = ref(0)
const buttonLabel :Ref<string> = ref('Next')
const isFinished : Ref<boolean>=ref(false)
const url  = new URL(window.location)
const openWrongAnswer : Ref<boolean> = ref(false)
const stepGrammar : Ref<string> = ref('presens')

const form=useForm({
    grammar_answer:[],
    answers_lang:url.searchParams.get('answer_language'),
    vocabulary_number:url.searchParams.get('vocabulary_number'),
    quiz_date:url.searchParams.get('quiz_date'),
    isDone:false,
    type:'Grammar'
})

const submit=((isDoneParam : boolean)=>{

    form.isDone= isDoneParam ? true : false

    form.post(route('quiz.check_answers'),{
            preserveState:true,
            onSuccess: () => {
            isFinished.value = true;
         }
        })
})

const setGrammarSteps =(()=>{
    let quiz_length=props.quiz_vocabularies.data.length-1;

    if(stepGrammar.value=='presens')
    {
        addedAnswers.value[index.value].answer = {presens:null,imperfectum:null,perfectum:null,name:props.quiz_vocabularies.data[index.value].name};

        addedAnswers.value[index.value].answer.presens=answers.value
        stepGrammar.value='imperfectum'
    }else if(stepGrammar.value=='imperfectum'){
        addedAnswers.value[index.value].answer.imperfectum=answers.value
        stepGrammar.value='perfectum'
    }else{
        if(quiz_length-1==index.value) buttonLabel.value='Done'
        if(quiz_length==index.value)
        {
            addedAnswers.value[index.value].answer.perfectum=answers.value

            form.grammar_answer=addedAnswers.value;
            submit(false)

        }else{
            addedAnswers.value[index.value].answer.perfectum=answers.value
            ++index.value;
            stepGrammar.value='presens'
        }

    }

    answers.value=''
})

const retry=(()=>{
    addedAnswers.value=props.quiz_vocabularies.data
    index.value=0
    isFinished.value=false
    buttonLabel.value='Next'
})
</script>
<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Quiz</h2>
        </template>

        <div class="py-12">
            <div v-if="!isFinished" class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-gray-800 overflow-hidden sm:rounded-lg text-white py-7">
                    <h1 class="text-center font-semibold text-2xl">Practice Your Inserted vocabularies</h1>
                    <div class="mt-7">

                        <h2 class="text-center">Vocabulary</h2>
                        <h1 class="text-center text-4xl font-semibold">{{ quiz_vocabularies.data[index].name }}</h1>
                        <div class="my-6">
                            <h2 class="text-center font-semibold text-md ">{{ stepGrammar.toUpperCase() }}</h2>
                            <div class="flex items-center justify-center">
                                <MainInput v-model="answers" label="" error="" />
                            </div>
                        </div>
                        <div class="flex items-center justify-center ">
                            <MainButton @click="setGrammarSteps" color="red-600">{{ buttonLabel }}</MainButton>
                        </div>
                    </div>

                </div>
            </div>
            <div v-else class="flex items-center justify-center bg-gray-100">
                <div class="rounded-lg bg-gray-50 px-16 py-14">
                    <div class="flex justify-center">
                        <div :class="{
                            'bg-green-200':$page.props.auth.quiz_percentage>=80,
                            'bg-orange-200':$page.props.auth.quiz_percentage<=80 && $page.props.auth.quiz_percentage>=40,
                            'bg-red-200':$page.props.auth.quiz_percentage<40,
                            }" class="rounded-full  p-6">
                            <div :class="{
                            'bg-green-500':$page.props.auth.quiz_percentage>=80,
                            'bg-orange-500':$page.props.auth.quiz_percentage<=80 && $page.props.auth.quiz_percentage>=40,
                            'bg-red-500':$page.props.auth.quiz_percentage<40,
                            }" class="flex h-16 w-16 items-center justify-center rounded-full p-4">
                                <faIcon class="h-8 w-8 text-white" :icon="faCheck" />
                            </div>
                        </div>
                    </div>
                    <h3 class="my-4 text-center text-3xl font-semibold text-gray-700">Congratuation!!!</h3>
                    <p class="text-center font-normal text-gray-600">Your Score is {{ $page.props.auth.quiz_percentage }}%</p>
                    <div class="inline-flex">
                        <button @click="retry" type="button" class="mx-1 mt-10 block rounded-xl border-4 border-transparent bg-info px-6 py-3 text-center text-base font-medium text-orange-100 outline-8 hover:outline hover:duration-300">
                            Practice Again
                        </button>
                        <button type="button" @click="openWrongAnswer=!openWrongAnswer" class="mx-1 mt-10 block rounded-xl border-4 border-transparent bg-blue-600 px-6 py-3 text-center text-base font-medium text-white outline-8 hover:outline hover:duration-300">
                            OpenYour Wrong Answer
                        </button>
                        <button type="submit" @click="submit(true)" class="mx-1 mt-10 block rounded-xl border-4 border-transparent bg-success px-6 py-3 text-center text-base font-medium text-white outline-8 hover:outline hover:duration-300">
                            Save My Score
                        </button>
                    </div>
                </div>
                <WrongAnswerModal :show="openWrongAnswer" :key="openWrongAnswer.toString()" @close="openWrongAnswer=false" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
