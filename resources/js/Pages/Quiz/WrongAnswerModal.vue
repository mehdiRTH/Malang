<script lang="ts" setup>
import Modal from '@/Components/Modal.vue';
import { ref } from 'vue';

defineProps<{
    show:boolean
}>()

function columnColor(answer: any, time : string) : string
{
    let color = '';
    if(answer.answer.grammar[time]?.toLowerCase() ==answer.right_answer.grammar[time]?.toLowerCase())
    {
        color='text-success'
    }else {
        color='text-danger'
    }

    return color
}
</script>
<template>
    <Modal :show="show" @close="$emit('close')" :maxWidth="$page.props.auth.type_quiz=='Grammar' ? '5xl' : '2xl'">
        <div class="bg-gray-800 p-4">
            <h1 class="text-white text-2xl font-semibold text-center my-4">Your Wrong answers</h1>
            <div class="flex items-center justify-center">
                <div class="p-6 overflow-none px-0 pt-0 pb-2">
                <table v-if="$page.props.auth.type_quiz=='Vocabulary'" class="w-full min-w-[640px] table-auto bg-white border rounded-l">
                <thead>
                    <tr>
                    <th class="border-b border-blue-gray-50 py-3 px-6 text-left">
                        <p class="block  text-center antialiased font-sans text-[11px]  font-semibold uppercase text-blue-gray-400">Your answer</p>
                    </th>
                    <th class="border-b border-blue-gray-50 py-3 px-6 text-left">
                        <p class="block antialiased text-center font-sans text-[11px] font-semibold uppercase text-blue-gray-400">The right answer</p>
                    </th>
                    <th class="border-b border-blue-gray-50 py-3 px-6 text-left">
                        <p class="block antialiased text-center font-sans text-[11px] font-semibold uppercase text-blue-gray-400">transaltion</p>
                    </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="answer in $page.props.auth.wrong_answers">
                        <td class="py-3 px-5 border-b border-blue-gray-50">
                            <div class="flex items-center justify-center gap-4">
                            <p class="block text-center antialiased font-sans text-sm leading-normal  text-danger font-bold">{{ answer.answer==null ? 'None' : answer.answer }}</p>
                            </div>
                        </td>
                        <td class="py-3 px-5 border-b border-blue-gray-50">
                            <div class="flex items-center justify-center gap-4">
                            <p class="block text-center antialiased font-sans text-sm leading-normal text-success font-bold">{{ answer.right_answer }}</p>
                            </div>
                        </td>
                        <td class="py-3 px-5 border-b border-blue-gray-50">
                            <div class="flex items-center justify-center gap-4">
                            <p class="block text-center antialiased font-sans text-sm leading-normal text-blue-600 font-bold">{{ answer.translation_answer }}</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
                </table>
                <table v-else class="w-full min-w-[640px] table-auto bg-white border rounded-l">
                    <thead>
                        <tr>
                            <th class="border-b border-blue-gray-50 py-3 px-6 text-left">
                            <p class="block  text-center antialiased font-sans text-[11px]  font-semibold uppercase text-blue-gray-400">Vocabularies</p>
                        </th>
                        <th class="border-b border-blue-gray-50 py-3 px-6 text-left">
                            <p class="block  text-center antialiased font-sans text-[11px]  font-semibold uppercase text-blue-gray-400">Your answer (Presens)</p>
                        </th>
                        <th class="border-b border-blue-gray-50 py-3 px-6 text-left">
                            <p class="block  text-center antialiased font-sans text-[11px]  font-semibold uppercase text-blue-gray-400">right answer (Presens)</p>
                        </th>
                        <th class="border-b border-blue-gray-50 py-3 px-6 text-left">
                            <p class="block  text-center antialiased font-sans text-[11px]  font-semibold uppercase text-blue-gray-400">Your answer (Imperfectum)</p>
                        </th>
                        <th class="border-b border-blue-gray-50 py-3 px-6 text-left">
                            <p class="block  text-center antialiased font-sans text-[11px]  font-semibold uppercase text-blue-gray-400">right answer (Imperfectum)</p>
                        </th>
                        <th class="border-b border-blue-gray-50 py-3 px-6 text-left">
                            <p class="block  text-center antialiased font-sans text-[11px]  font-semibold uppercase text-blue-gray-400">Your answer (Perfectum)</p>
                        </th>
                        <th class="border-b border-blue-gray-50 py-3 px-6 text-left">
                            <p class="block  text-center antialiased font-sans text-[11px]  font-semibold uppercase text-blue-gray-400">right answer (Perfectum)</p>
                        </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="answer in $page.props.auth.wrong_answers">
                            <td class="py-3 px-5 border-b border-blue-gray-50">
                                <div class="flex items-center justify-center gap-4">
                                <p class="block text-center antialiased font-sans text-sm leading-normal  text-blue-600 font-bold">{{ answer.answer.name }}</p>
                                </div>
                            </td>
                            <td class="py-3 px-5 border-b border-blue-gray-50">
                                <div class="flex items-center justify-center gap-4">
                                    <p
                                        :class="'block text-center antialiased font-sans text-sm leading-normal  font-bold '+columnColor(answer,'presens')">
                                        {{ answer.answer.grammar.presens==null ? 'None' : answer.answer.grammar.presens }}
                                    </p>
                                </div>
                            </td>
                            <td class="py-3 px-5 border-b border-blue-gray-50">
                                <div class="flex items-center justify-center gap-4">
                                    <p class="block text-center antialiased font-sans text-sm leading-normal text-success font-bold">
                                        {{ answer.right_answer.grammar.presens }}
                                    </p>
                                </div>
                            </td>
                            <td class="py-3 px-5 border-b border-blue-gray-50">
                                <div class="flex items-center justify-center gap-4">
                                    <p
                                        :class="'block text-center antialiased font-sans text-sm leading-normal  font-bold '+columnColor(answer,'imperfectum')">
                                        {{ answer.answer.grammar.imperfectum==null ? 'None' : answer.answer.grammar.imperfectum }}
                                    </p>
                                </div>
                            </td>
                            <td class="py-3 px-5 border-b border-blue-gray-50">
                                <div class="flex items-center justify-center gap-4">
                                <p class="block text-center antialiased font-sans text-sm leading-normal text-success font-bold">{{ answer.right_answer.grammar.imperfectum }}</p>
                                </div>
                            </td>
                            <td class="py-3 px-5 border-b border-blue-gray-50">
                                <div class="flex items-center justify-center gap-4">
                                    <p :class="'block text-center antialiased font-sans text-sm leading-normal  font-bold '+columnColor(answer,'perfectum')">
                                        {{ answer.answer.grammar.perfectum==null ? 'None' : answer.answer.grammar.perfectum }}
                                    </p>
                                </div>
                            </td>
                            <td class="py-3 px-5 border-b border-blue-gray-50">
                                <div class="flex items-center justify-center gap-4">
                                <p class="block text-center antialiased font-sans text-sm leading-normal text-success font-bold">{{ answer.right_answer.grammar.perfectum }}</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </Modal>
</template>
