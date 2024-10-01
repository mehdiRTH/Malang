<script lang="ts" setup>
import Modal from '@/Components/Modal.vue';
import { useForm } from '@inertiajs/vue3';
import MainInput from '@/Components/MainInput.vue';
import MainButton from '@/Components/MainButton.vue';
import { ThemeData } from '@/types/Themes/ThemeData';
import { ref } from 'vue';

const props=defineProps<{
    show:boolean,
    theme:ThemeData | null
}>()

const emit=defineEmits(['close'])
const isEdit=ref(props.theme?.id ? true : false);

const form=useForm({
    name:props.theme?.name ?? ''
})

const submit=(()=>{
    if(isEdit.value)
    {
        form.put(route('themes.update',{theme:props.theme}),{
            onSuccess:(()=>{
                emit('close')
            })
        })
    }else {
        form.post(route('themes.store'),{
            onSuccess:(()=>{
                emit('close')
            })
        })
    }

})
</script>
<template>
    <Modal :show="show" @close="$emit('close')">
        <form @submit.prevent="submit()" class="bg-gray-800 p-4">
            <h1 class="text-white mb-4 mt-2 font-semibold text-2xl">Create Theme</h1>
            <div>
                <MainInput  v-model="form.name"  label="Name" :error="form.errors.name" class="my-4" />

                <div class="flex items-end justify-end">
                    <MainButton color="white" textColor="gray-800" type="submit">Create</MainButton>
                </div>
            </div>
        </form>
    </Modal>
</template>
