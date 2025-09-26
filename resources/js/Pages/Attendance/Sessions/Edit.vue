<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SessionForm from './SessionForm.vue'; // <-- Import komponen form
import { Head, useForm } from '@inertiajs/vue3';

// Terima data sesi yang ada dari controller
const props = defineProps({
    session: Object,
});

// Isi form dengan data yang sudah ada
const form = useForm({
    session_date: props.session.session_date,
    start_time: props.session.start_time,
    end_time: props.session.end_time,
    description: props.session.description,
});

// Fungsi untuk mengirim data ke controller update
const updateSession = () => {
    form.put(route('sessions.update', props.session.id));
};
</script>

<template>
    <Head title="Edit Sesi" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Sesi Absensi</h2>
        </template>
        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <SessionForm :form="form" @submit="updateSession" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>