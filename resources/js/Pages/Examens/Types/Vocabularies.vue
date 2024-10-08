<script lang="ts" setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import QuizComponent from '@/Components/QuizComponent.vue';
import { QuizComponentEnum } from '@/types/QuizComponentEnum';

const props=defineProps<{
    dividedVocabularies:Array,
    exam:Array
}>()

</script>
<template>
    <AuthenticatedLayout label="Exam">
        <div class="py-12">
            <!-- Examen component -->
            <QuizComponent :examData="dividedVocabularies.data" quizType="vocabulary" :componentType="QuizComponentEnum.Exam" :exam="props.exam" />
        </div>
    </AuthenticatedLayout>

</template>
<style>

</style>
