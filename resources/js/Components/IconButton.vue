<script lang="ts" setup>
import { IconDefinition } from '@fortawesome/fontawesome-svg-core';
import { faClock, faPen, faTrash, faEye,faToggleOff, faPlus } from '@fortawesome/free-solid-svg-icons';
import { Link } from '@inertiajs/vue3';
import { Ref, computed, ref } from 'vue';

const props=defineProps<{
    url?:string,
    icon?:IconDefinition,
    color?:string,
    type:string,
    label?:string
}>()

defineEmits(['click'])

interface IconType{
    color:string;
    icon:IconDefinition;
    type:string;
}

const types : Ref<IconType[]>=ref([
    {color:'stroke', icon:faClock, type:'default'},
    {color:'orange-500', icon:faPen, type:'edit'},
    {color:'red-500', icon:faTrash, type:'delete'},
    {color:'tertiary', icon:faToggleOff, type:'change'},
    {color:'primary', icon:faEye, type:'show'},
    {color:'tertiary', icon:faPlus, type:'plus'}
])

const typeSelected=computed(()=>{
    // let selectedType=types.value.find((item)=>item.type==props.type);

    return types.value.find((item)=>item.type==props.type) || types.value[0]

})

</script>
<template>
    <Link v-if="url" :href="url" :class="'text-white middle none center w-3 h-3 p-2 mx-2 rounded-md text-xs bg-' +(color!=null ? color : typeSelected?.color)">
        <faIcon v-if="icon" :icon="icon"  />
        <faIcon v-else :icon="typeSelected?.icon"  />
        {{label}}
    </Link>
    <button type="button" v-else @click="$emit('click')" :class="'text-white middle none center p-2 mx-2 rounded-md text-xs bg-' +(color!=null ? color : typeSelected?.color)">
        <faIcon v-if="icon" :icon="icon" />
        <faIcon v-else :icon="typeSelected?.icon" />
        {{label}}
    </button>
</template>
