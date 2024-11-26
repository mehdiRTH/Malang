<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import DisplayCards from '@/Components/DisplayCards.vue';
import { CardsType } from '@/types/CardsType';
import { faPercentage, faCheck, faFileContract, faSpellCheck, faClipboard} from '@fortawesome/free-solid-svg-icons';
import { ref, Ref, watch } from 'vue';
import { DataDate } from '@/types/Enums/DataDate';
import MainTable from '@/Components/Table/MainTable.vue';
import ThTable from '@/Components/Table/ThTable.vue';
import TdTable from '@/Components/Table/TdTable.vue';
import { DashboardDataInterface } from '@/types/DashboardInterface';
import UserNotice from './Dashboard/UserNotice.vue';
const props=defineProps<{
    dashboardCards : {
        countAddedGrammar : number,
        quizScoreRate : number,
        examScoreRate : number
    },
    vocabulariesData : DashboardDataInterface,
    userNotice : {
        lastTimeVocabulary : number,
        lastTimeQuiz : number,
        lastTimeExam : number
    },
    searchDate : DataDate
}>()

const isConsistent : Ref<boolean> = ref(props.userNotice.lastTimeVocabulary==0 && props.userNotice.lastTimeQuiz==0)

function  addedVocabularies() {
    return props.vocabulariesData.data.reduce((acc, ele) => {
        return acc + ele.vocabularies_number;
  }, 0)
}

const cardsItems : CardsType[]=[
    {Label : 'Added Vocabularies', subLabel : addedVocabularies(), color : 'blue', icon : faFileContract},
    {Label : 'Added Grammar', subLabel : props.dashboardCards.countAddedGrammar.toString(), color : 'red', icon : faSpellCheck},
    {Label : 'Quiz Rate', subLabel : props.dashboardCards.quizScoreRate+'%', color : 'purple', icon : faClipboard},
    {Label : 'Examen Rate', subLabel : props.dashboardCards.examScoreRate+'%', color : 'orange', icon : faPercentage}
]

const dataDateType : Ref<DataDate> = ref(props.searchDate=='This Month' ? DataDate.ThisMonth : DataDate.LastMonth)

watch(dataDateType,((val)=>{
    console.log(val)
    router.get(
        route('dashboard',{searchDate : val})
    )
}))
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
                            'text-blue-900 bg-blue-200':dataDateType==DataDate.LastMonth,
                            'text-blue-200 bg-blue-900':dataDateType==DataDate.ThisMonth}"
                            class="p-2 rounded mx-1">This Month</button>
                        <button @click="dataDateType=DataDate.LastMonth"
                        :class="{
                            'text-blue-200 bg-blue-900':dataDateType==DataDate.LastMonth,
                            'text-blue-900 bg-blue-200':dataDateType==DataDate.ThisMonth}"
                            class="p-2 rounded mx-1">Last Month</button>
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

                <!--Component to notice the user that he is consistent or not -->
                <UserNotice :userNotice="userNotice" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
