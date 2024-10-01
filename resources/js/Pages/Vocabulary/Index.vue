<script lang="ts" setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusComponent from '@/Components/StatusComponent.vue';
import IconButton from '@/Components/IconButton.vue';
import MainTable from '@/Components/Table/MainTable.vue';
import TdTable from '@/Components/Table/TdTable.vue';
import ThTable from '@/Components/Table/ThTable.vue';
import { VocabulariesInterface } from '@/types/Vocabularies/VocabulariesInterface';
import { LanguageEnum } from '@/types/LanguageEnum';
import DeleteModal from '@/Components/DeleteModal.vue';
import { ref, Ref } from 'vue';
import { VocabularyData } from '@/types/Vocabularies/VocabularyData';

const props=defineProps<{
    vocabularies:VocabulariesInterface
}>()

const show : Ref<boolean> = ref(false)
const selectedVocabulary : Ref<VocabularyData | null> = ref(null)

function getLanguageDisplayName(language: LanguageEnum): string {
    switch (language) {
      case LanguageEnum.EN:
        return 'English';
      case LanguageEnum.NL:
        return 'Dutch';
      case LanguageEnum.AR:
        return 'Arabic';
      default:
        return 'Dutch';
    }
}

const toggleDeleteModal=((vocabulary : VocabularyData | null)=>{
    show.value=!show.value
    selectedVocabulary.value=vocabulary
})
</script>
<template>
   <AuthenticatedLayout label="Your Vocabularies">
        <MainTable label="" :meta="vocabularies.meta" :create_route="route('vocabularies.create')">
            <template #ThTable>
                <ThTable>Vocabularies</ThTable>
                <ThTable>languages available</ThTable>
                <ThTable>Date</ThTable>
                <ThTable>Actions</ThTable>
            </template>
            <template #TdTable>
                <tr v-for="vocabulary in vocabularies.data">
                    <TdTable>{{ vocabulary.name }}</TdTable>
                    <TdTable>
                        <StatusComponent v-for="lang in vocabulary.languages_available" :type="lang" >{{ getLanguageDisplayName(lang) }}</StatusComponent>
                    </TdTable>
                    <TdTable>{{ vocabulary.date }}</TdTable>
                    <TdTable>
                        <IconButton type="edit" :url="route('vocabularies.edit',{vocabulary:vocabulary})" />
                        <IconButton type="delete" @click="toggleDeleteModal(vocabulary)" />
                    </TdTable>
                </tr>
            </template>
        </MainTable>
        <DeleteModal v-if="selectedVocabulary" :show="show" :route="route('vocabularies.destroy',{vocabulary:selectedVocabulary})" @close="toggleDeleteModal(null)" />
   </AuthenticatedLayout>
</template>
