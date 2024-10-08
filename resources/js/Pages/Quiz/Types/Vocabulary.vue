<script lang="ts" setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import QuizComponent from '@/Components/QuizComponent.vue';
import { QuizComponentEnum } from '@/types/QuizComponentEnum';

const props=defineProps<{
    quiz_vocabularies:quiz_inteface
}>()

interface quiz_inteface{
    data : Array<{
        nl:string;
        en:string;
        answer:string;
    }>
}

</script>
<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Quiz</h2>
        </template>

        <div class="py-12">
            <QuizComponent :examData="quiz_vocabularies.data" quizType="vocabulary" :componentType="QuizComponentEnum.Vocabulary" />
        </div>
    </AuthenticatedLayout>
</template>
