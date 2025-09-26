<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const flashMessage = computed(() => page.props.flash && page.props.flash.message);

defineProps({ exceptions: Array });

const deleteException = (id) => {
    if (confirm('Anda yakin ingin menghapus pengecualian ini?')) {
        router.delete(route('exceptions.destroy', id));
    }
};
</script>
<template>
    <Head title="Pengecualian Absensi" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Pengecualian Absensi</h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="flashMessage" class="bg-green-100 border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ flashMessage }}
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <Link :href="route('exceptions.create')" class="mb-4 inline-block bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700">
                            + Tambah Pengecualian
                        </Link>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left">Tanggal</th>
                                    <th class="px-6 py-3 text-left">Nama Siswa</th>
                                    <th class="px-6 py-3 text-left">Alasan</th>
                                    <th class="px-6 py-3 text-left">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="exc in exceptions" :key="exc.id">
                                    <td class="px-6 py-4">{{ exc.exception_date }}</td>
                                    <td class="px-6 py-4">{{ exc.student.name }}</td>
                                    <td class="px-6 py-4">{{ exc.reason }}</td>
                                    <td class="px-6 py-4">
                                        <button @click="deleteException(exc.id)" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>