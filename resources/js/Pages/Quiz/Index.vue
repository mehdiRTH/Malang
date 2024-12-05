<script lang="ts" setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CreateModal from './CreateModal.vue';
import { ref, Ref } from 'vue';
import MainTable from '@/Components/Table/MainTable.vue';
import TdTable from '@/Components/Table/TdTable.vue';
import ThTable from '@/Components/Table/ThTable.vue';
import DisplayCards from '@/Components/DisplayCards.vue';
import { CardsType } from '@/types/CardsType';
import { faPercentage, faNetworkWired } from '@fortawesome/free-solid-svg-icons';
import { QuizPerformanceInterface } from '@/types/QuizPerformance/QuizPerformanceInterface';
import { ThemesInterface } from '@/types/Themes/ThemesInterface';


const props=defineProps<{
    quizVocabularies:QuizPerformanceInterface,
    themes:ThemesInterface,
    thisMonthScoreRate:number,
    thisMonthUploadedVocabularies:number,
    lastMonthScoreRate:number,
    lastMonthUploadedVocabularies:number,
    type:string,
    availableDates:Array<Date>
}>()

const toggleModal : Ref<boolean> = ref(false)
const cardsItems : CardsType[]=[
    {Label:props.lastMonthScoreRate+'% Quiz rate',subLabel:'last month','color':'blue',icon:faPercentage},
    {Label:props.lastMonthUploadedVocabularies+' Vocabularies',subLabel:'last month','color':'red',icon:faNetworkWired},
    {Label:props.thisMonthScoreRate+'% Quiz rate',subLabel:'This month','color':'red',icon:faPercentage},
    {Label:props.thisMonthUploadedVocabularies+' Vocabularies',subLabel:'This month','color':'orange',icon:faNetworkWired}
]

</script>
<template>
  <AuthenticatedLayout label="Quiz Performance">
        <CreateModal :show="toggleModal" :type="type" :dates="availableDates" :themes="themes" @close="toggleModal=false" />
        <!-- Cards component -->
        <DisplayCards :key="cardsItems+''" :items="cardsItems" />

        <MainTable label="Quiz Table" :meta="quizVocabularies.meta" @click_create="toggleModal=!toggleModal">
            <template #ThTable>
                <ThTable>Answer language</ThTable>
                <ThTable>Vocabularies number</ThTable>
                <ThTable>Vocabularies Date</ThTable>
                <ThTable>Quiz Date</ThTable>
                <ThTable>Success Rate</ThTable>
            </template>
            <template #TdTable>
                <tr v-for="quiz in quizVocabularies.data">
                    <TdTable>{{ quiz.answer_language }}</TdTable>
                    <TdTable>{{ quiz.vocabulary_number }} Vocabularies</TdTable>
                    <TdTable>{{ quiz.quiz_date }}</TdTable>
                    <TdTable>{{ quiz.created_at }}</TdTable>
                    <TdTable>
                        <div class="w-10/12">
                            <p class="antialiased font-sans mb-1 block text-xs font-medium text-blue-gray-600">{{ quiz.success_rate }}%</p>
                            <div class="flex flex-start bg-blue-gray-50 overflow-hidden w-full rounded-sm font-sans text-xs font-medium h-1">
                                <div :class="{
                                    'from-green-500 to-green-200':quiz.success_rate>80,
                                    'from-orange-500 to-orange-200':quiz.success_rate>50 && quiz.success_rate<=75,
                                    'from-blue-500 to-blue-200':quiz.success_rate>25 && quiz.success_rate<=50,
                                    'from-red-500 to-red-200':quiz.success_rate<=25,
                                    }"
                                    class="flex justify-center items-center h-full bg-gradient-to-tr text-white"
                                    :style="'width:'+quiz.success_rate+'%;'"></div>
                            </div>
                            </div>
                    </TdTable>
                </tr>
            </template>
        </MainTable>
  </AuthenticatedLayout>
</template>
