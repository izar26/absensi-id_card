<script setup>
defineProps({
    form: Object, // Menerima objek form dari halaman Create/Edit
});

defineEmits(['submit']); // Mendefinisikan event 'submit'
</script>

<template>
    <form @submit.prevent="$emit('submit')">
        <div class="mb-4">
            <label for="session_date" class="block text-sm font-medium text-gray-700">Tanggal Sesi</label>
            <input v-model="form.session_date" type="date" id="session_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            <p v-if="form.errors.session_date" class="text-sm text-red-600 mt-1">{{ form.errors.session_date }}</p>
        </div>

        <div class="mb-4">
            <label for="start_time" class="block text-sm font-medium text-gray-700">Jam Mulai</label>
            <input v-model="form.start_time" type="time" id="start_time" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            <p v-if="form.errors.start_time" class="text-sm text-red-600 mt-1">{{ form.errors.start_time }}</p>
        </div>

        <div class="mb-4">
            <label for="end_time" class="block text-sm font-medium text-gray-700">Batas Terlambat</label>
            <input v-model="form.end_time" type="time" id="end_time" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            <p v-if="form.errors.end_time" class="text-sm text-red-600 mt-1">{{ form.errors.end_time }}</p>
        </div>

        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
            <input v-model="form.description" type="text" id="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <p v-if="form.errors.description" class="text-sm text-red-600 mt-1">{{ form.errors.description }}</p>
        </div>

        <div class="flex justify-end mt-6">
            <button type="submit" :disabled="form.processing" class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 disabled:opacity-50">
                Simpan
            </button>
        </div>
    </form>
</template>