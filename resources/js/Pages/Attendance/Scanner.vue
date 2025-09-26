<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { QrcodeStream } from 'vue-qrcode-reader';
import axios from 'axios';

// State untuk menyimpan hasil dan status proses (tetap sama)
const scanResult = ref(null);
const scanError = ref(null);
const isLoading = ref(false);

// Fungsi ini akan dipanggil oleh library saat QR code terdeteksi
const onDetect = async (detectedCodes) => {
    if (isLoading.value) return;

    isLoading.value = true;
    scanResult.value = null;
    scanError.value = null;

    const firstCode = detectedCodes[0];
    const nis = firstCode.rawValue;

    try {
        // Kirim NIS hasil scan ke backend
        const response = await axios.post('/api/attendance', { nis: nis });
        
        // Jika sukses, tampilkan pesan berhasil
        scanResult.value = response.data.message;
        
        // Ucapkan nama siswa yang berhasil diabsen
        const studentName = response.data.data.name;
        speak(`${studentName} berhasil di absen`);

    } catch (error) {
        // Jika gagal, tampilkan pesan error
        const errorMessage = error.response.data.message;
        scanError.value = errorMessage;
        
        // Ucapkan pesan errornya
        speak(errorMessage);

    } finally {
        // Setelah 3 detik, reset state agar bisa scan lagi
        setTimeout(() => {
            isLoading.value = false;
            scanResult.value = null;
            scanError.value = null;
        }, 3000);
    }
};

// --- FUNGSI BARU UNTUK TEXT-TO-SPEECH ---
const speak = (text) => {
    // Cek apakah browser mendukung Web Speech API
    if ('speechSynthesis' in window) {
        // Hentikan suara yang sedang berjalan (jika ada)
        window.speechSynthesis.cancel();

        // Buat objek ucapan baru
        const utterance = new SpeechSynthesisUtterance(text);
        
        // Atur bahasa ke Bahasa Indonesia agar aksennya pas
        utterance.lang = 'id-ID';
        
        // Atur kecepatan bicara (opsional, 1.0 adalah normal)
        utterance.rate = 1.0;

        // Mulai berbicara
        window.speechSynthesis.speak(utterance);
    } else {
        console.error("Browser Anda tidak mendukung Text-to-Speech.");
    }
};
</script>

<template>
    <Head title="Scan Absensi" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Scan QR Code Absensi</h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        
                        <div v-if="isLoading" class="p-4 mb-4 text-center bg-blue-100 text-blue-800 rounded-lg">
                            Memproses...
                        </div>
                        <div v-if="scanResult" class="p-4 mb-4 text-center bg-green-100 text-green-800 rounded-lg font-bold">
                            {{ scanResult }}
                        </div>
                        <div v-if="scanError" class="p-4 mb-4 text-center bg-red-100 text-red-800 rounded-lg font-bold">
                            {{ scanError }}
                        </div>

                        <div class="border-4 border-dashed rounded-lg p-2">
                           <qrcode-stream @detect="onDetect"></qrcode-stream>
                        </div>
                        <p class="text-center text-sm text-gray-500 mt-4">Arahkan QR Code pada ID Card ke dalam area kamera.</p>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>