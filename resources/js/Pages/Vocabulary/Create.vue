<script lang="ts" setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';
import Form from '@/Components/Form.vue';
import MainInput from '@/Components/MainInput.vue';
import { onMounted, ref, Ref } from 'vue';
import IconButton from '@/Components/IconButton.vue';
import { faClose } from '@fortawesome/free-solid-svg-icons';
import MainButton from '@/Components/MainButton.vue';
import InputError from '@/Components/InputError.vue';

const props=defineProps<{
    vocabulary:array
}>()

enum languageEnum{
    'nl'='Dutch',
    'ar'='Arabic',
    'en'='English'
}

interface translationsInterface{
    vocabulary:string;
    language:string;
}

const translations : Ref<Array<translationsInterface>> =ref([])


const form=useForm({
    sheet:'',
    name:props.vocabulary.name,
    translations:props.vocabulary?.translations ?? Array<translationsInterface>,
    createByUpload:true
})

const changeFile=((event : any)=>{
    form.sheet=event.target.files[0]

})

const addVocabulary=(()=>{
        translations.value.push({vocabulary:'',language:''})
})

const removeVocabulary=((index : number)=>{
    translations.value.splice(index,1)
})

const submit=(()=>{
    form.translations=translations.value
    if(props.vocabulary.data?.id)
    {
        form.put(route('vocabularies.update',props.vocabulary))
    }else{
        form.post(route('vocabularies.store'));
    }

})

const toggleSections=(()=>{
    form.createByUpload=!form.createByUpload;
    form.reset('sheet', 'name', 'translations');
})


onMounted(()=>{
    if(props.vocabulary?.id)
    {
        let objectLength=Object.keys(props.vocabulary.translations);

        for(let i=0;i<objectLength.length;i++)
        {
            translations.value.push({vocabulary:props.vocabulary.translations[objectLength[i]],language:objectLength[i]=='nl' ? 'Dutch' : (objectLength[i]=='en' ? 'English' : 'Arabic')})
        }
    }
})
</script>
<template>
<AuthenticatedLayout>
    <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Vocabularies</h2>
    </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-gray-800 overflow-hidden sm:rounded-lg">
                    <Form :btnLabel="'save'" @submit="submit" :errors="form.errors" label="Vocabulary">

                        <div class="block items-center justify-center col-span-2">
                            <div class='w-full max-w-lg px-10 py-8 mx-auto rounded-lg items-center justify-center flex'>
                            <span class="p-1 inline-flex border bg-gray-200 rounded-md">
                                <button type="button" :class="{'bg-white':form.createByUpload}" class="px-2 py-1 rounded inline-flex" @click="toggleSections">
                                    <span class="font-semibold text-gray-600">Upload Vocabularies By Sheet</span>
                                </button>
                                <button type="button" :class="{'bg-white':!form.createByUpload}" @click="toggleSections" class="px-2 py-1 rounded inline-flex">
                                    <span>Create a vocabulary manually</span>
                                </button>
                            </span>
                        </div>

                        <div v-if="form.createByUpload">
                            <label class="block text-sm font-medium text-white">
                               Vocabulary Sheet
                            </label>
                            <main class="container mx-auto max-w-screen-lg h-full">
                                <article aria-label="File Upload Modal" class="relative h-full flex flex-col rounded-md" >
                                    <section class="h-full overflow-auto p-8 w-full flex flex-col">
                                        <header class="border-dashed border-2 border-gray-400 py-12 flex flex-col justify-center items-center">
                                        <p class="mb-3 font-semibold text-white flex flex-wrap justify-center">
                                            <span>Drag and drop your</span>&nbsp;<span>files anywhere or</span>
                                        </p>
                                        <input id="hidden-input" type="file" multiple class="hidden" />
                                        <input @change="changeFile" type="file" class="mt-2 rounded-sm px-3 py-1 bg-gray-200 hover:bg-gray-300 focus:shadow-outline focus:outline-none">
                                        </input>
                                        </header>
                                    </section>
                                </article>
                            </main>
                                <InputError :message="form.errors.sheet"/>
                        </div>
                            <div v-else class="col-span-2">
                                <MainInput label="name" v-model="form.name" :error="form.errors.name"  />
                                <div class="border-gray-300 border p-3 mt-4 rounded-md">
                                    <div class="flex justify-between">
                                        <span class="text-white font-semibold">Add Vocabulary Translations</span>
                                        <IconButton type="plus" label="Add Vocabulary" @click="addVocabulary" />
                                    </div>
                                    <div v-for=" (trans,idx) in translations" :key="trans.language" class="mt-5">
                                        <div class="grid grid-cols-2 gap-4">
                                            <MainInput  label="Vocabulary" v-model="trans.vocabulary" error=""  />
                                            <div>
                                                <label class="text-white dark:text-gray-200" for="passwordConfirmation">Language</label>
                                                <select v-model="trans.language" class="block w-full px-4 py-2 mt-2 text-gray-700 bg-white border border-gray-300 rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-500 dark:focus:border-blue-500 focus:outline-none focus:ring">
                                                    <option v-for="item in languageEnum" >{{ item }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-center mt-4">
                                            <MainButton color="tertiary" :icon="faClose" @click="removeVocabulary(idx)">
                                                <span class="text-xs"> Delete The Vocabulary </span>
                                            </MainButton>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Form>
                </div>
            </div>
        </div>
</AuthenticatedLayout>
</template>
