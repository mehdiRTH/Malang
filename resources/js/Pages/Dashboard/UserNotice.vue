<script lang="ts" setup>
import { Ref, ref } from 'vue';
import { faCheck } from '@fortawesome/free-solid-svg-icons';
import { DashboardDataNoticeInterface } from '@/types/Dashboard/DashboardDataNoticeInterface';

const props=defineProps<{
    userNotice:DashboardDataNoticeInterface
}>()
const isConsistent : Ref<boolean> = ref(props.userNotice.lastTimeVocabulary==0 && props.userNotice.lastTimeQuiz==0 && props.userNotice.lastTimeExam==0)
const noticeLabel : Ref<DashboardDataNoticeInterface> = ref({lastTimeVocabulary:'uploaded a Vocabulary',lastTimeQuiz:'Exercise a Quiz', lastTimeExam:'Passed an Exam'})

</script>
<template>
    <div>
        <div class="rounded-lg bg-white py-10 mt-8">
            <div class="flex justify-center">
                <div :class="{'bg-green-200':isConsistent, 'bg-orange-200':!isConsistent}"
                        class="rounded-full p-6">
                    <div :class="{'bg-green-500':isConsistent, 'bg-orange-500':!isConsistent}" class="flex items-center justify-center rounded-full p-4">
                    <faIcon :icon="faCheck" class="h-8 w-8 text-white"/>
                    </div>
                </div>
            </div>
            <div>
                <!-- Check If The user Consistent based on last time he practice a quiz and he has uploaded vocabularies and also passed exams -->
                <h3 class="my-4 text-center text-3xl font-semibold text-gray-700">
                    {{ isConsistent ? 'Consistent' : 'Not Consistent' }}
                </h3>

                <!-- Message To the user counting last time he practice quiz, upload a vocabularies, passed an exam -->
                <ul class="my-3 mx-2 font-medium text-center">
                    <li v-for="(item, key) in userNotice"
                    :class="{
                            'bg-orange-200' : item as number > 0,
                            'bg-green-200' : item == 0
                            }"
                        class="p-2 rounded-xl mb-3">
                        Last Time You {{ noticeLabel[key] }}  was
                        <span :class="
                        {
                            'text-orange-600':item as number > 0,
                            'bg-green-600':item == 0
                        }" class="font-semibold"> {{ item }} day(s). </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
