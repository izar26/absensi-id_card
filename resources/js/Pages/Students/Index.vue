<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

// Menerima 'students' dari controller sebagai props
const props = defineProps({
    students: Array
});

// --- STATE MANAGEMENT ---

// State untuk membedakan mode: false = Tambah, true = Edit
const editMode = ref(false);

// State untuk membuka/menutup modal
const isModalOpen = ref(false);

const photoPreview = ref(null);

// State untuk menampung data form, dikelola oleh Inertia
// 'id' dibutuhkan untuk proses update
const form = useForm({
    id: null,
    name: '',
    nis: '',
    class: '',
    photo: null,
});


// --- FUNCTIONS ---

// Fungsi untuk membuka modal dalam mode TAMBAH
const openAddModal = () => {
    form.reset(); // Kosongkan form dari data sebelumnya
    editMode.value = false;
    isModalOpen.value = true;
};

// Fungsi untuk membuka modal dalam mode EDIT
const openEditModal = (student) => {
    // Isi form dengan data siswa yang akan diedit
    form.id = student.id;
    form.name = student.name;
    form.nis = student.nis;
    form.class = student.class;
    
    photoPreview.value = student.photo ? `/storage/${student.photo}` : null; // <-- Set preview untuk edit
    editMode.value = true;
    isModalOpen.value = true;
};

// Fungsi untuk menutup modal
const closeModal = () => {
    isModalOpen.value = false;
    form.reset(); // Selalu reset form saat modal ditutup
    photoPreview.value = null;
};

// Fungsi untuk submit form (bisa untuk Tambah atau Edit)
const submit = () => {
    if (editMode.value) {
        // Ganti dari form.post menjadi form.put
        form.put(route('students.update', form.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        // Jika mode tambah, kirim request CREATE
        form.post(route('students.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

// Fungsi untuk menghapus siswa
const deleteStudent = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus siswa ini?')) {
        router.delete(route('students.destroy', id), {
            preserveScroll: true,
        });
    }
};

// Fungsi baru untuk menangani input file
const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (!file) return;
    form.photo = file;
    photoPreview.value = URL.createObjectURL(file); // <-- Buat URL sementara untuk preview
};
</script>

<template>
    <Head title="Daftar Siswa" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Siswa</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div v-if="$page.props.flash && $page.props.flash.message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ $page.props.flash.message }}</span>
                        </div>
                        <button @click="openAddModal" class="mb-4 bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700">
                            + Tambah Siswa Baru
                        </button>

                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="student in students" :key="student.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <img v-if="student.photo" :src="`/storage/${student.photo}`" alt="Foto Siswa" class="h-10 w-10 rounded-full object-cover">
                                        <span v-else class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-xs">No Pic</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ student.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ student.nis }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ student.class }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                        <button @click="openEditModal(student)" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                        <a :href="route('students.idcard', student.id)" target="_blank" class="ml-4 text-green-600 hover:text-green-900">Cetak</a>
                                        <button @click="deleteStudent(student.id)" class="ml-4 text-red-600 hover:text-red-900">Hapus</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="isModalOpen" class="fixed inset-0 flex items-center justify-center z-50">
            <div @click="closeModal" class="absolute inset-0 bg-gray-900 opacity-50"></div>
            
            <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full p-6 z-10" @click.stop>
                <div class="flex justify-between items-center border-b pb-3">
                    
                    <h3 class="text-2xl font-semibold">{{ editMode ? 'Edit Siswa' : 'Tambah Siswa Baru' }}</h3>
                    <button @click="closeModal" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
                </div>

                <form @submit.prevent="submit" class="mt-4">
                    <div class="mb-4 text-center">
                        <img v-if="photoPreview" :src="photoPreview" alt="Preview Foto" class="w-32 h-32 rounded-full object-cover mx-auto">
                        <div v-else class="w-32 h-32 rounded-full bg-gray-200 mx-auto flex items-center justify-center text-gray-500">
                            Preview
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="photo" class="block text-sm font-medium text-gray-700">Foto</label>
                        <input @input="form.photo = $event.target.files[0]" type="file" id="photo" class="mt-1 block w-full">
                        <progress v-if="form.progress" :value="form.progress.percentage" max="100" class="w-full mt-2">
                            {{ form.progress.percentage }}%
                        </progress>
                        <p v-if="form.errors.photo" class="text-sm text-red-600 mt-1">{{ form.errors.photo }}</p>
                    </div>
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input v-model="form.name" type="text" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <p v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</p>
                    </div>

                    <div class="mb-4">
                        <label for="nis" class="block text-sm font-medium text-gray-700">NIS</label>
                        <input v-model="form.nis" type="text" id="nis" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <p v-if="form.errors.nis" class="text-sm text-red-600 mt-1">{{ form.errors.nis }}</p>
                    </div>

                    <div class="mb-4">
                        <label for="class" class="block text-sm font-medium text-gray-700">Kelas</label>
                        <input v-model="form.class" type="text" id="class" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <p v-if="form.errors.class" class="text-sm text-red-600 mt-1">{{ form.errors.class }}</p>
                    </div>
                    
                    <div class="flex justify-end mt-6">
                        <button type="button" @click="closeModal" class="mr-3 bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded hover:bg-gray-400">
                            Batal
                        </button>
                        <button type="submit" :disabled="form.processing" class="bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-700 disabled:opacity-50">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </AuthenticatedLayout>
</template>