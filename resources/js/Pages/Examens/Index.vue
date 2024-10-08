<script lang="ts" setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
defineProps<{
    examens:array
}>()

const generateExam=((startDate : Date,endDate:Date)=>{
    console.log([startDate,endDate])
})
</script>
<template>
<AuthenticatedLayout label="This Month Exams">
    <!-- component -->
    <section class="text-gray-600 body-font  rounded flex justify-center items-center">
    <div class="container px-5 py-14 mx-auto">
        <div class="flex flex-wrap -m-4 text-center gap-4">
            <div v-for="exam in examens.data" class="hover:scale-105 duration-500">
                <Link v-if="exam.vocabularies>0" :href="route('examen.custom_examen',{start_date:exam.startDate,end_date:exam.endDate})" class="p-4 sm:w-1/2 lg:w-1/3 w-full">
                    <div class=" flex items-center  justify-between p-4  rounded-lg bg-white shadow-indigo-50 shadow-md">
                    <div>
                        <h2 class="text-gray-900 text-lg font-bold">Exam of the {{ exam.weekNumber }} week</h2>
                        <h3 class="mt-2 text-xl font-bold text-yellow-500 text-left">+{{ exam.vocabularies }} Vocabularies</h3>
                        <p class="text-sm font-semibold text-gray-400 mt-2">{{ exam.startDate }} -> {{ exam.endDate }}</p>
                        <p class="text-sm font-semibold text-gray-400 mt-2">Attempt(s) : {{ exam.exam ? exam.exam.attempts+' Attempt' : 'Not yet started' }}</p>
                    </div>
                    <div :class="{
                            'from-gray-800 to-gray-700 shadow-gray-400' : !exam.exam,
                            'from-red-500 to-red-400 shadow-red-400' : exam.exam && exam.exam.score<=50,
                            'from-orange-500 to-orange-400 shadow-orange-400' : exam.exam && exam.exam.score>50 && exam.exam.score<=80,
                            'from-green-500 to-green-400 shadow-green-400' : exam.exam && exam.exam.score>80
                            } "
                        class="bg-gradient-to-tr  w-32 h-32  rounded-full shadow-2xl border-white  border-dashed border-2  flex justify-center items-center ">
                        <div>
                        <h1 class="text-white text-2xl">{{ exam.exam ? exam.exam.score+'%' : 'Pending'}}</h1>
                        </div>
                    </div>
                    </div>

                </Link>
            </div>
        </div>
        </div>
    </section>
</AuthenticatedLayout>
</template>
