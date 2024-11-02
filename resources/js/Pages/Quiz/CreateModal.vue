<script lang="ts" setup>
import Modal from '@/Components/Modal.vue';
import MainInput from '@/Components/MainInput.vue';
import { useForm } from '@inertiajs/vue3';
import MainButton from '@/Components/MainButton.vue';
import Label from '@/Components/Label.vue';
import { faWarning } from '@fortawesome/free-solid-svg-icons';
import { Ref, ref } from 'vue';
import { ThemesInterface } from '@/types/Themes/ThemesInterface';
import { ThemeData } from '@/types/Themes/ThemeData';


const props=defineProps<{
    show:Boolean,
    themes:ThemesInterface,
    type:string
}>()

const form=useForm({
    vocabulary_number:'10',
    quiz_date:'',
    theme:{} as ThemeData,
    answer_language:null,
    type_search:props.type,
    isThemeGrammar:props.type=='Grammar' ? true : false
})

const count_vocabularies_number_error : Ref<number | null>=ref(null)

const submit=(()=>{

    form.get(route('quiz.generate'),{
        preserveState: true,
        onError:(errors)=>{
            console.error(errors)
            form.errors=errors
            count_vocabularies_number_error.value = parseInt(errors.count_vocabulary_number);
        }
    })
})

const switchTypeSearch=((type : string)=>{
    form.reset()
    form.type_search=type
})

</script>
<template>
    <Modal :show="show" @close="$emit('close')" >
        <form @submit.prevent="submit()" class="bg-gray-800 p-4">
            <h1 class="text-white mb-4 mt-2 font-semibold text-2xl">Generate Quiz</h1>

            <div v-if="count_vocabularies_number_error==null">
                <MainInput inputType="number" v-model="form.vocabulary_number"  label="how many vocabularies (min 10 vocabularies)" :error="form.errors.vocabulary_number" min="10" class="my-4" />
                <Label v-if="!form.isThemeGrammar" label="Type of Quiz Vocabularies" error="" class="my-4">
                    <div class=" bg-white mx-8 shadow rounded-full h-10 my-4 flex p-1 relative items-center">
                        <div class="w-full flex justify-center">
                            <button @click="switchTypeSearch('Theme')" type="button">{{ form.type_search=='Theme' ? 'Date' : 'Theme' }}</button>
                        </div>
                        <div class="w-full flex justify-center">
                            <button @click="switchTypeSearch('Date')" type="button">{{ form.type_search=='Date' ? 'Theme' : 'Date' }}</button>
                        </div>
                        <span :class="{'right-1':form.type_search=='Date','left-1':form.type_search=='Theme'}"
                        class="elSwitch bg-gray-800 shadow text-white flex items-center justify-center w-1/2 rounded-full h-8 transition-all top-[4px] absolute">
                            {{ form.type_search }}
                        </span>
                    </div>
                </Label>
                <MainInput v-if="form.type_search=='Date' || form.isThemeGrammar" v-model="form.quiz_date" inputType="date" :label="(form.isThemeGrammar ? 'Grammar Vocabularies' : 'Vocabularies')+' Date'" :error="form.errors.quiz_date" class="my-4"/>
                <Label v-else label="Themes" :error="form.errors.theme" class="my-4">
                    <select v-model="form.theme" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="" selected disabled hidden>Choose a Theme</option>
                        <option v-for="theme in themes.data" :key="theme.id" :value="theme" >{{ theme.name }}</option>
                    </select>
                </Label>
                <Label v-if="!form.isThemeGrammar" label="Language to answer vocabularies" :error="form.errors.answer_language" class="my-4">
                    <select v-model="form.answer_language" id="countries" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="null">Choose a language</option>
                        <option value="nl">Dutch</option>
                        <option value="en">English</option>
                        <option value="ar">Arabic</option>
                    </select>
                </Label>

                <div class="flex items-end justify-end">
                    <MainButton color="white" textColor="gray-800" type="submit">Send</MainButton>
                </div>
            </div>
            <div v-else>
                <div v-if="count_vocabularies_number_error==0" class="bg-white text-center py-4 my-6 rounded-lg">
                    <h1 class="font-semibold text-xl"> <faIcon :icon="faWarning"/> There's No Vocabulary in This Date {{ form.quiz_date }}</h1>
                    <p class="">Try To Change the date or upload Vocabularies</p>

                    <MainButton color="red-600" textColor="white" class="mt-3" @click="count_vocabularies_number_error=null">
                        Change the date
                    </MainButton>
                </div>
                <div v-else class="bg-white text-center py-4 my-6 rounded-lg">
                    <h1 class="font-semibold text-xl"> <faIcon :icon="faWarning"/> There's {{ count_vocabularies_number_error }} Vocabularies in This Date {{ form.quiz_date }}</h1>
                    <p class="">Try To Change the date or continue with {{ count_vocabularies_number_error }} vocabularies</p>

                    <div class="inline-flex">
                        <MainButton color="red-600" textColor="white" class="mt-3" @click="count_vocabularies_number_error=null">
                            Change the date
                        </MainButton>
                        <MainButton color="gray-800" textColor="white" class="mt-3" @click="form.vocabulary_number=count_vocabularies_number_error.toString()" type="submit">
                            Continue with {{ count_vocabularies_number_error }} vocabularies
                        </MainButton>
                    </div>
                </div>
            </div>

        </form>
    </Modal>
</template>
