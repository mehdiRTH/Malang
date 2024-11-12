<script lang="ts" setup>
import { ref, Ref, watch, onMounted } from 'vue';
import { faArrowRight, faArrowDown, faClose } from '@fortawesome/free-solid-svg-icons';
import { useForm } from '@inertiajs/vue3';
import IconButton from '@/Components/IconButton.vue';
import MainButton from '@/Components/MainButton.vue';
import MainInput from '@/Components/MainInput.vue';
import { GrammarInterface } from '@/types/Vocabularies/GrammarInterface';
import { VocabularyData } from '@/types/Vocabularies/VocabularyData';
import { TypeVocabulary } from '@/types/Enums/TypeVocabulary';
import { LanguageEnum } from '@/types/LanguageEnum';

const props=defineProps<{
    vocabulary : VocabularyData,
    errors? : Object
}>()

const emits=defineEmits(['dataChanged'])

interface translationsInterface{
    vocabulary : string;
    language : string;
}

const translations : Ref<Array<translationsInterface>> =ref([])
const typeEdit : Ref<TypeVocabulary | null> = ref(TypeVocabulary.Vocabulary)
const isEdit : Ref<boolean> = ref(props.vocabulary?.id ? true : false)
const grammar : Ref<GrammarInterface> = ref(props.vocabulary?.vocabulary_grammar)

const form=useForm({
    name:props.vocabulary.name,
    translations:props.vocabulary?.translations ?? Array<translationsInterface>,
    grammar:[],
    createByUpload:isEdit.value ? false : true
})

const addVocabulary=(()=>{
        translations.value.push({vocabulary:'',language:''})
})

const removeVocabulary=((index : number)=>{
    translations.value.splice(index,1)
})

onMounted(()=>{
    if(isEdit.value)
    {
        let translationInstance=Object.keys(props.vocabulary.translations);

        for(let i=0;i<translationInstance.length;i++)
        {
            translations.value.push({
                vocabulary:props.vocabulary.translations[translationInstance[i]],
                language:translationInstance[i]=='nl' ? 'Dutch' : (translationInstance[i]=='en' ? 'English' : 'Arabic')
            })
        }
    }
})

watch([grammar.value, translations.value, form], () => {
    emits('dataChanged', [grammar.value, translations.value, form.name]);
});

</script>
<template>
    <div class="col-span-2">
        <!-- Main Input for Vocabulary Name -->
        <MainInput
            label="name"
            v-model="form.name"
            :error="errors?.name"
        />

        <!-- Button to Toggle Vocabulary Section -->
        <button
            type="button"
            @click="typeEdit = typeEdit == null ? TypeVocabulary.Vocabulary : null"
            class="w-full p-2 mt-4 rounded-l bg-blue-400 text-white font-semibold"
        >
            Vocabulary
            <!-- Icon indicating the section state (open/close) -->
            <faIcon :icon="typeEdit == TypeVocabulary.Vocabulary ? faArrowRight : faArrowDown " />
        </button>

        <!-- Vocabulary Translations Section (Visible if Vocabulary is selected) -->
        <div v-if="typeEdit == 'Vocabulary'" class="border-gray-300 border p-3 mt-4 rounded-md">
            <div class="flex justify-between">
                <!-- Title indicating Add or Edit mode -->
                <span class="text-white font-semibold">{{ isEdit ? 'Edit' : 'Add' }} Vocabulary Translations</span>
                <!-- Button to Add New Vocabulary -->
                <IconButton type="plus" label="Add Vocabulary" @click="addVocabulary" />
            </div>

            <!-- Loop over each translation item -->
            <div v-for="(trans, idx) in translations" :key="trans.language" class="mt-5">
                <div class="grid grid-cols-2 gap-4">
                    <!-- Input for Vocabulary Text -->
                    <MainInput label="Vocabulary" v-model="trans.vocabulary" error="" />

                    <!-- Dropdown for Language Selection -->
                    <div>
                        <label class="text-white dark:text-gray-200" for="language">Language</label>
                        <select
                            v-model="trans.language"
                            class="block w-full px-4 py-2 mt-2 text-gray-700 bg-white border border-gray-300 rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-500 dark:focus:border-blue-500 focus:outline-none focus:ring"
                        >
                            <!-- Language Options from Enum -->
                            <option v-for="item in LanguageEnum">{{ item }}</option>
                        </select>
                    </div>
                </div>

                <!-- Button to Delete the Vocabulary Entry -->
                <div class="flex items-center justify-center mt-4">
                    <MainButton color="tertiary" :icon="faClose" @click="removeVocabulary(idx)">
                        <span class="text-xs">Delete The Vocabulary</span>
                    </MainButton>
                </div>
            </div>
        </div>

        <!-- Button to Toggle Grammar Section (Visible if vocabulary grammar is present) -->
        <button
            v-if="vocabulary.vocabulary_grammar != null"
            type="button"
            @click="typeEdit = typeEdit == null ? TypeVocabulary.Grammar : null"
            class="w-full p-2 mt-4 rounded-l bg-blue-400 text-white font-semibold"
        >
            Grammar
            <!-- Icon indicating the section state (open/close) -->
            <faIcon :icon="typeEdit == TypeVocabulary.Grammar ? faArrowRight : faArrowDown" />
        </button>

        <!-- Vocabulary Grammar Section (Visible if Grammar is selected) -->
        <div v-if="typeEdit == TypeVocabulary.Grammar" class="border-gray-300 border p-3 mt-4 rounded-md">
            <div class="flex justify-between">
                <!-- Title for Grammar Editing Section -->
                <span class="text-white font-semibold">Edit Vocabulary Grammar</span>
            </div>

            <!-- Grammar Inputs for Dutch -->
            <MainInput label="Presens" v-model="grammar['nl'].presens" error="" class="my-7" />
            <MainInput label="Imperfectum" v-model="grammar['nl'].imperfectum" error="" class="my-7" />
            <MainInput label="Perfectum" v-model="grammar['nl'].perfectum" error="" class="my-7" />
        </div>
    </div>
</template>
