<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SessionForm from './SessionForm.vue'; // <-- Import komponen form
import { Head, useForm } from '@inertiajs/vue3';

// Siapkan objek form kosong untuk sesi baru
const form = useForm({
    session_date: '',
    start_time: '',
    end_time: '',
    description: '',
});

// Fungsi untuk mengirim data ke controller store
const storeSession = () => {
    form.post(route('sessions.store'));
};
</script>

<template>
    <Head title="Buat Sesi Baru" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Buat Sesi Absensi Baru</h2>
        </template>
        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <SessionForm :form="form" @submit="storeSession" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>