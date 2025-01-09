<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

</script>

<template>
    <Head title="Welcome" />
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <img id="background" class="absolute -left-20 top-0 max-w-[877px]" src="https://laravel.com/assets/img/welcome/background.svg" />
        <div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                    <p>
                        AdmissionHubs Api Service Version: <span class="font-semibold"> v1.0.0 </span>
                    </p>
                </footer>
            </div>
        </div>
    </div>
</template>
