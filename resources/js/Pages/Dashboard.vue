<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import DisplayCards from '@/Components/DisplayCards.vue';
import { CardsType } from '@/types/CardsType';
import { faPercentage, faCheck, faFileContract, faSpellCheck, faClipboard} from '@fortawesome/free-solid-svg-icons';
import { ref, Ref } from 'vue';
import { DataDate } from '@/types/Enums/DataDate';
import MainTable from '@/Components/Table/MainTable.vue';
import ThTable from '@/Components/Table/ThTable.vue';
import TdTable from '@/Components/Table/TdTable.vue';
import { DashboardDataInterface } from '@/types/DashboardInterface';

const props=defineProps<{
    dashboardCards : {
        countAddedGrammar : number,
        quizScoreRate : number,
        examScoreRate : number
    },
    vocabulariesData : DashboardDataInterface,
    userNotice : {
        lastTimeVocabulary : number,
        lastTimeQuiz : number
    }
}>()

const isConsistent : Ref<boolean> = ref(props.userNotice.lastTimeVocabulary==0 && props.userNotice.lastTimeQuiz==0)

function  addedVocabularies() {
    return props.vocabulariesData.data.reduce((acc, ele) => {
        return acc + ele.vocabularies_number;
  }, 0)
}

const cardsItems : CardsType[]=[
    {Label : 'Added Vocabularies', subLabel : addedVocabularies(), color : 'blue', icon : faFileContract},
    {Label : 'Added Grammar', subLabel : props.dashboardCards.countAddedGrammar, color : 'red', icon : faSpellCheck},
    {Label : 'Quiz Rate', subLabel : props.dashboardCards.quizScoreRate+'%', color : 'purple', icon : faClipboard},
    {Label : 'Examen Rate', subLabel : props.dashboardCards.examScoreRate+'%', color : 'orange', icon : faPercentage}
]

const dataDateType : Ref<DataDate> = ref(DataDate.ThisMonth)
</script>
<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>
        <div class="mb-12 max-w-7xl mx-auto">
            <div>
                <div class="flex justify-between items-center">
                    <h1 class="font-semibold text-xl">General data</h1>
                    <div >
                        <button @click="dataDateType=DataDate.ThisMonth"
                        :class="{
                            'text-teal-900 bg-teal-200':dataDateType==DataDate.LastMonth,
                            'text-teal-200 bg-teal-900':dataDateType==DataDate.ThisMonth}"
                            class="p-2 rounded mx-1">This Month</button>
                        <button @click="dataDateType=DataDate.LastMonth"
                        :class="{
                            'text-teal-200 bg-teal-900':dataDateType==DataDate.LastMonth,
                            'text-teal-900 bg-teal-200':dataDateType==DataDate.ThisMonth}" class="p-2 text-teal-900 bg-teal-200 rounded mx-1">Last Month</button>
                    </div>
                </div>
                <DisplayCards :key="cardsItems+''" :items="cardsItems" />
            </div>

            <!-- <h1 class="font-semibold mt-5 text-xl">Analytic For Vocabularies ({{ dataDateType }})</h1> -->
            <div class="grid grid-cols-4 py-2">
                <div class="mr-4 rounded-l col-span-3">
                    <MainTable :label="'Vocabularies Analytics ('+dataDateType +')'" :meta="vocabulariesData.meta">
                        <template #ThTable>
                            <ThTable>Date</ThTable>
                            <ThTable>Vocabularies Number</ThTable>
                            <ThTable>Quiz Score</ThTable>
                        </template>
                        <template #TdTable>
                            <tr v-for="item in vocabulariesData.data">
                                <TdTable>{{ item.date }}</TdTable>
                                <TdTable>{{ item.vocabularies_number }} Vocabularies</TdTable>
                                <TdTable>
                                    <div :class="{
                                            'bg-red-500' : item.score < 35,
                                            'bg-orange-500' : item.score > 35 && item.score < 80,
                                            'bg-green-500' : item.score > 80
                                        }" class="p-1 text-white rounded-lg w-1/4 font-bold text-center">
                                        {{ item.score }}%
                                    </div>
                                </TdTable>
                                <TdTable></TdTable>
                            </tr>
                        </template>
                    </MainTable>
                </div>
                <div>
                    <div class="rounded-lg bg-white py-14 mt-8">
                        <div class="flex justify-center">
                            <div :class="{'bg-green-200':isConsistent, 'bg-orange-200':!isConsistent}"
                                 class="rounded-full p-6">
                                <div :class="{'bg-green-500':isConsistent, 'bg-orange-500':!isConsistent}" class="flex items-center justify-center rounded-full p-4">
                                <faIcon :icon="faCheck" class="h-8 w-8 text-white"/>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h3 class="my-4 text-center text-3xl font-semibold text-gray-700">
                                {{ isConsistent ? 'Consistent' : 'Not Consistent' }}
                            </h3>
                            <ul class="my-3 mx-2 font-medium text-center">
                                <li :class="{
                                        'bg-orange-200' : userNotice.lastTimeVocabulary > 0,
                                        'bg-green-200' : userNotice.lastTimeVocabulary == 0}"
                                    class="p-2 rounded-xl mb-3">
                                    Last Time You uploaded a Vocabulary was
                                    <span :class="
                                    {
                                        'text-orange-600':userNotice.lastTimeVocabulary > 0,
                                        'bg-green-600':userNotice.lastTimeVocabulary == 0
                                    }" class="font-semibold"> {{ userNotice.lastTimeVocabulary }} day(s). </span>
                                </li>
                                <li :class="{
                                            'bg-orange-200':userNotice.lastTimeQuiz > 0,
                                            'bg-green-200':userNotice.lastTimeQuiz == 0}"
                                    class="bg-orange-200 p-2 rounded-xl">
                                    Last Time You Practiced Vocabularies <span :class="
                                    {
                                        'text-orange-600': userNotice.lastTimeQuiz > 0,
                                        'bg-green-600': userNotice.lastTimeQuiz == 0
                                    }" class="text-orange-500 font-semibold">{{ userNotice.lastTimeQuiz }} day(s). </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
