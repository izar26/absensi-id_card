<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({ students: Array });

const form = useForm({
    student_id: '',
    exception_date: '',
    reason: '',
});

const submit = () => form.post(route('exceptions.store'));
</script>
<template>
    <Head title="Tambah Pengecualian" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Pengecualian Absensi</h2>
        </template>
        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit">
                            <div class="mb-4">
                                <label for="student_id">Siswa</label>
                                <select v-model="form.student_id" id="student_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="" disabled>Pilih Siswa</option>
                                    <option v-for="student in students" :key="student.id" :value="student.id">{{ student.name }}</option>
                                </select>
                                <p v-if="form.errors.student_id" class="text-sm text-red-600 mt-1">{{ form.errors.student_id }}</p>
                            </div>
                            <div class="mb-4">
                                <label for="exception_date">Tanggal Pengecualian</label>
                                <input v-model="form.exception_date" type="date" id="exception_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <p v-if="form.errors.exception_date" class="text-sm text-red-600 mt-1">{{ form.errors.exception_date }}</p>
                            </div>
                            <div class="mb-4">
                                <label for="reason">Alasan</label>
                                <textarea v-model="form.reason" id="reason" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required></textarea>
                                <p v-if="form.errors.reason" class="text-sm text-red-600 mt-1">{{ form.errors.reason }}</p>
                            </div>
                            <div class="flex justify-end mt-6">
                                <button type="submit" :disabled="form.processing" class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>