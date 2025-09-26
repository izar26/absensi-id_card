<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

// Terima props dari controller
const props = defineProps({
    reportData: Array,
    filterDate: String,
});

// Buat state reaktif untuk input tanggal, diisi dengan tanggal dari controller
const date = ref(props.filterDate);

// "Mata-mata" yang akan memuat ulang data saat tanggal di filter berubah
watch(date, (newDate) => {
    router.get(route('reports.attendance.index'), {
        date: newDate // Kirim tanggal baru sebagai query parameter
    }, {
        preserveState: true, // Agar halaman tidak terasa 'flash' saat reload
        replace: true,       // Ganti history state, bukan menambah baru
    });
});

// Fungsi untuk menentukan warna badge status
const statusClass = (status) => {
    if (status === 'Hadir') return 'bg-green-100 text-green-800';
    if (status === 'Terlambat') return 'bg-yellow-100 text-yellow-800';
    if (status === 'Alfa') return 'bg-red-100 text-red-800';
    return 'bg-gray-100 text-gray-800';
};

const updateStatus = (studentId, newStatus) => {
    router.post(route('reports.attendance.updateStatus'), {
        student_id: studentId,
        status: newStatus,
        date: props.filterDate,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Laporan Absensi" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Laporan Kehadiran Siswa</h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        
                        <div class="mb-4">
                            <label for="filter_date" class="block text-sm font-medium text-gray-700">Pilih Tanggal Laporan:</label>
                            <input v-model="date" type="date" id="filter_date" class="mt-1 block w-full md:w-1/4 border-gray-300 rounded-md shadow-sm">
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left">No</th>
                                        <th class="px-6 py-3 text-left">Nama Siswa</th>
                                        <th class="px-6 py-3 text-left">Kelas</th>
                                        <th class="px-6 py-3 text-center">Status</th>
                                        <th class="px-6 py-3 text-left">Jam Absen</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="(item, index) in reportData" :key="item.id">
                                        <td class="px-6 py-4">{{ index + 1 }}</td>
                                        <td class="px-6 py-4">{{ item.name }}</td>
                                        <td class="px-6 py-4">{{ item.class }}</td>
                                        <td class="px-6 py-4 text-center">
    <select 
        :value="item.status" 
        @change="updateStatus(item.id, $event.target.value)"
        class="border-gray-300 rounded-md shadow-sm text-xs"
        :class="statusClass(item.status)"
    >
        <option value="Hadir">Hadir</option>
        <option value="Terlambat">Terlambat</option>
        <option value="Izin">Izin</option>
        <option value="Sakit">Sakit</option>
        <option value="Alfa">Alfa</option>
    </select>
</td>
                                        <td class="px-6 py-4">{{ item.scan_time }}</td>
                                    </tr>
                                    <tr v-if="reportData.length === 0">
                                        <td colspan="5" class="text-center py-4">Tidak ada data absensi untuk tanggal yang dipilih.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>