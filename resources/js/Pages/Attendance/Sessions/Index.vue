<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3'; // <-- 1. IMPORT usePage
import { computed } from 'vue'; // <-- (Opsional) Import computed untuk kemudahan

// 2. PANGGIL usePage() untuk mendapatkan semua properti halaman
const page = usePage();

// (Opsional) Buat computed property agar lebih singkat saat memanggilnya di template
const flashMessage = computed(() => page.props.flash && page.props.flash.message);

defineProps({
    sessions: Array,
});

const deleteSession = (id) => {
    if (confirm('Anda yakin ingin menghapus sesi ini?')) {
        router.delete(route('sessions.destroy', id));
    }
};
</script>

<template>
    <Head title="Manajemen Sesi" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Sesi Absensi</h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div v-if="flashMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ flashMessage }}</span>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <Link :href="route('sessions.create')" class="mb-4 inline-block bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700">
                            + Buat Sesi Baru
                        </Link>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">Tanggal</th>
                                    <th class="px-6 py-3 text-left">Jam Mulai</th>
                                    <th class="px-6 py-3 text-left">Batas Terlambat</th>
                                    <th class="px-6 py-3 text-left">Deskripsi</th>
                                    <th class="px-6 py-3 text-left">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="session in sessions" :key="session.id">
                                    <td class="px-6 py-4">{{ session.session_date }}</td>
                                    <td class="px-6 py-4">{{ session.start_time }}</td>
                                    <td class="px-6 py-4">{{ session.end_time }}</td>
                                    <td class="px-6 py-4">{{ session.description }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <Link :href="route('sessions.edit', session.id)" class="text-indigo-600 hover:text-indigo-900">Edit</Link>
                                        <button @click="deleteSession(session.id)" class="ml-4 text-red-600 hover:text-red-900">Hapus</button>
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