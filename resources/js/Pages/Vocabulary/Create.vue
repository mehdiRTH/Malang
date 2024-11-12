<script lang="ts" setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';
import Form from '@/Components/Form.vue';
import {ref, Ref } from 'vue';
import InputError from '@/Components/InputError.vue';
import VocabularyType from './Components/VocabularyType.vue';
import { VocabularyData } from '@/types/Vocabularies/VocabularyData';
import { TranslationsInterface } from '@/types/Vocabularies/TranslationssInterface';

const props=defineProps<{
    vocabulary:VocabularyData
}>()

const isEdit : Ref<boolean> = ref(props.vocabulary?.id ? true : false)

const form=useForm({
    sheet:'',
    name:props.vocabulary.name,
    translations:props.vocabulary?.translations ?? Array<TranslationsInterface>,
    grammar:[],
    createByUpload:isEdit.value ? false : true
})

const changeFile=((event : any)=>{
    form.sheet=event.target.files[0]

})

const submit=(()=>{
    if(isEdit.value)
    {
        form.put(route('vocabularies.update',{vocabulary:props.vocabulary}))
    }else{
        form.post(route('vocabularies.store'));
    }

})

const toggleSections=(()=>{
    form.createByUpload=!form.createByUpload;
    form.reset('sheet', 'name', 'translations');
})

const changeData=((data: any)=>{
    form.grammar=data[0]
    form.translations=data[1]
    form.name=data[2]
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
                            <div v-if="!isEdit" class='w-full max-w-lg px-10 py-8 mx-auto rounded-lg items-center justify-center flex'>
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
                            <VocabularyType v-else :vocabulary="vocabulary" :errors="form.errors" @dataChanged="changeData" />
                        </div>
                    </Form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
