<script lang="ts" setup></script>
<template>
    <div class=" bg-primary bg-opacity-10 rounded text-center w-full h-80 mt-8 flex items-center justify-center">
        <div class="container flex flex-col md:flex-row items-center justify-center px-5 text-gray-700">
            <div class="max-w-md">
                <div class="text-5xl font-dark font-bold">There's no Requests</div>
                <p
                class="text-2xl mb-8 md:text-3xl font-light leading-normal"
                >Create A Request </p>
            <!-- <p class="mb-8">But dont worry, you can find plenty of other things on our homepage.</p> -->

            <Link v-if="!$helpers.hasRoles(['HR'])" :href="route('off_requests.create')" class="px-4 inline py-2 text-sm font-medium leading-5 shadow text-white transition-colors duration-150 border border-transparent rounded-lg focus:outline-none focus:shadow-outline-blue bg-primary active:bg-blue-600 hover:bg-blue-700">
                Create Request
            </Link>
            </div>
        </div>
    </div>
</template>
