<script lang="ts" setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusComponent from '@/Components/StatusComponent.vue';
import { Ref, ref } from 'vue';
import CreateModel from './CreateModel.vue';
import MainTable from '@/Components/Table/MainTable.vue';
import TdTable from '@/Components/Table/TdTable.vue';
import ThTable from '@/Components/Table/ThTable.vue';
import { ThemesInterface } from '@/types/Themes/ThemesInterface';
import IconButton from '@/Components/IconButton.vue';
import { ThemeData } from '@/types/Themes/ThemeData';
import DeleteModal from '@/Components/DeleteModal.vue';

defineProps<{
    themes:ThemesInterface
}>()

const toggleModal : Ref<boolean>=ref(false)
const toggleDeleteModal : Ref<boolean>=ref(false)

const selectedTheme : Ref<ThemeData | null> = ref(null);

const editToggleModal=((theme : ThemeData)=>{
    toggleModal.value=!toggleModal.value
    selectedTheme.value=theme
})

const deleteToggleModal=((theme : ThemeData | null)=>{
    toggleDeleteModal.value=!toggleDeleteModal.value
    selectedTheme.value=theme
})
</script>
<template>
    <AuthenticatedLayout label="Themes">

            <MainTable label="Themes" @click_create="toggleModal=true">
                <template #ThTable>
                    <ThTable>Theme Name</ThTable>
                    <ThTable>Vocabularies number</ThTable>
                    <ThTable>Date</ThTable>
                    <ThTable></ThTable>
                </template>
                <template #TdTable>
                    <tr v-for="theme in themes.data">
                        <TdTable>{{ theme.name }}</TdTable>
                        <TdTable>{{ theme.date }}</TdTable>
                        <TdTable>
                            <StatusComponent :type="''" >{{ theme.vocabularies_num }} Vocabularies</StatusComponent>
                        </TdTable>
                        <TdTable>
                            <IconButton type="edit" @click="editToggleModal(theme)" />
                            <IconButton type="delete" @click="deleteToggleModal(theme)"/>
                        </TdTable>

                    </tr>
                </template>
            </MainTable>
        <CreateModel :show="toggleModal" :key="toggleModal.toString()" :theme="selectedTheme" @close="toggleModal=false;selectedTheme=null"/>
        <!-- <DeleteModal :show="toggleDeleteModal" :theme="selectedTheme" @close="toggleDeleteModal=false" /> -->
         <DeleteModal v-if="selectedTheme"
         :show="toggleDeleteModal"
         :route="route('themes.destroy',{theme:selectedTheme})"
         @close="deleteToggleModal(null)"
         subTitle="In case of there's vocabularies affected to this theme they will be generalized."/>
    </AuthenticatedLayout>
</template>
