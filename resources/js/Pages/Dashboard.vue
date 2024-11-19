<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import DisplayCards from '@/Components/DisplayCards.vue';
import { CardsType } from '@/types/CardsType';
import { faPercentage, faNetworkWired } from '@fortawesome/free-solid-svg-icons';
import { ref, Ref } from 'vue';
import { DataDate } from '@/types/Enums/DataDate';
import { Chart, Grid, Line } from 'vue3-charts'
import MainTable from '@/Components/Table/MainTable.vue';
import ThTable from '@/Components/Table/ThTable.vue';
import TdTable from '@/Components/Table/TdTable.vue';
import { MetaInterface } from '@/types/MetaInterface';
import { faCheck, faWarning, faCross } from '@fortawesome/free-solid-svg-icons';


interface DashboardDataInterface{
    data:{
        vocabularies_number:number,
        date:Date,
        score:number
    },
    meta:MetaInterface
}

const props=defineProps({
    vocabulariesData : Array<DashboardDataInterface>,
    lastTimeVocabulary : Number,
    lastTimeQuiz : Number
})

const isConsistent : Ref<boolean> = ref(props.lastTimeVocabulary==0 && props.lastTimeQuiz==0)


const cardsItems : CardsType[]=[
    {Label:'Quiz rate',subLabel:'0%','color':'blue',icon:faPercentage},
    {Label:' Vocabularies',subLabel:'0','color':'red',icon:faNetworkWired},
    {Label:'Quiz rate',subLabel:'0%','color':'red',icon:faPercentage},
    {Label:' Vocabularies',subLabel:'0','color':'orange',icon:faNetworkWired}
]

const dataDateType : Ref<DataDate> = ref(DataDate.ThisMonth)

    const direction = ref('horizontal')
    const margin = ref({
      left: 0,
      top: 20,
      right: 20,
      bottom: 0
    })
    const plByMonth = [
  { name: 'Jan', pl: 1000, avg: 500, inc: 300 },
  { name: 'Feb', pl: 2000, avg: 900, inc: 400 },
  { name: 'Apr', pl: 400, avg: 400, inc: 500 },
  { name: 'Mar', pl: 3100, avg: 1300, inc: 700 },
  { name: 'May', pl: 200, avg: 100, inc: 200 },
  { name: 'Jun', pl: 600, avg: 400, inc: 300 },
  { name: 'Jul', pl: 500, avg: 90, inc: 100 }
]
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
                <div class="px-2 bg-white mr-4 rounded-l col-span-3">
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
                                <TdTable>{{ item.score }}%</TdTable>
                                <TdTable></TdTable>
                            </tr>
                        </template>
                    </MainTable>
                </div>
                <div>
                    <!-- <div class="p-6 bg-white rounded-l ">
                        <div class="bg-red-600 bg-opacity-30 p-20 flex items-center justify-center">
                            <faIcon :icon="faCheck" class="bg-white p-6 rounded-full text-2xl" />
                            <ul class="ml-8 my-3 list-disc">
                                <li>Last Time You uploaded a Vocabulary was {{ lastTimeVocabulary }} days</li>
                                <li>Last Time You Practiced Vocabularies {{ lastTimeQuiz }} days</li>
                                <li>You should keep consistent</li>
                                <li>Practice the old words </li>
                            </ul>
                        </div>
                    </div> -->

                    <div class="rounded-lg bg-white py-14">
                        <div class="flex justify-center">
                            <div :class="{'bg-green-200':isConsistent, 'bg-orange-200':!isConsistent}"
                                 class="rounded-full p-6">
                                <div :class="{'bg-green-500':isConsistent, 'bg-orange-500':!isConsistent}" class="flex items-center justify-center rounded-full p-4">
                                <faIcon :icon="faCheck" class="h-8 w-8 text-white"/>
                                </div>
                            </div>
                        </div>
                        <div >
                            <h3 class="my-4 text-center text-3xl font-semibold text-gray-700">
                                {{ isConsistent ? 'Consistent' : 'Not Consistent' }}
                            </h3>
                            <ul class="ml-8 my-3 list-disc mx-4 font-medium">
                                <li v-if="lastTimeVocabulary>0">
                                    Last Time You uploaded a Vocabulary was <span class="text-orange-500 font-semibold"> {{ lastTimeVocabulary }} day(s). </span>
                                </li>
                                <li v-if="lastTimeQuiz>0">
                                    Last Time You Practiced Vocabularies <span class="text-orange-500 font-semibold">{{ lastTimeQuiz }} day(s). </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
