<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

// Terima props 'stats' dari DashboardController
defineProps({
    stats: Object,
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-sm font-medium text-gray-500">Total Siswa</h3>
                        <p class="mt-1 text-3xl font-semibold text-gray-900">{{ stats.totalStudents }}</p>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-sm font-medium text-gray-500">Hadir Hari Ini</h3>
                        <p class="mt-1 text-3xl font-semibold text-green-600">{{ stats.presentToday }}</p>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-sm font-medium text-gray-500">Alfa Hari Ini</h3>
                        <p class="mt-1 text-3xl font-semibold text-red-600">{{ stats.absentToday }}</p>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-sm font-medium text-gray-500">Persentase Kehadiran</h3>
                        <p class="mt-1 text-3xl font-semibold text-blue-600">{{ stats.attendancePercentage }}%</p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Aktivitas Absensi Terkini</h3>
                        <ul role="list" class="divide-y divide-gray-200">
                            <li v-for="scan in stats.recentScans" :key="scan.id" class="py-4 flex">
                                <img class="h-10 w-10 rounded-full" :src="scan.student.photo ? `/storage/${scan.student.photo}` : 'https://via.placeholder.com/150'" alt="">
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ scan.student.name }}</p>
                                    <p class="text-sm text-gray-500">
                                        Status: <span class="font-semibold" :class="scan.status === 'Hadir' ? 'text-green-600' : 'text-yellow-600'">{{ scan.status }}</span>
                                        - {{ new Date(scan.created_at).toLocaleTimeString('id-ID') }}
                                    </p>
                                </div>
                            </li>
                            <li v-if="stats.recentScans.length === 0" class="py-4 text-center text-gray-500">
                                Belum ada aktivitas absensi hari ini.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>