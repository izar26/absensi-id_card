<template>
  <AuthenticatedLayout>
    <Head title="Data Siswa" />

    <div class="p-6">
      <h1 class="text-2xl font-bold mb-6">Data Siswa</h1>

      <!-- 🔍 Search & Tambah -->
      <div class="flex justify-between items-center mb-6">
        <input
          v-model="search"
          type="text"
          placeholder="Cari siswa..."
          class="px-3 py-2 border rounded w-1/3"
        />
        <button
          @click="openAddModal"
          class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
          + Tambah Siswa
        </button>
      </div>

      <!-- 📄 Tabel Data -->
      <div class="overflow-x-auto">
        <table class="w-full border-collapse border">
          <thead>
            <tr class="bg-gray-100">
              <th class="p-2 border">Foto</th>
              <th class="p-2 border">Nama</th>
              <th class="p-2 border">NIS</th>
              <th class="p-2 border">Kelas</th>
              <th class="p-2 border">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="student in props.students.data" :key="student.id">
              <td class="p-2 border text-center">
                <img
                  v-if="student.photo"
                  :src="`/storage/${student.photo}`"
                  class="w-12 h-12 object-cover rounded-full mx-auto"
                />
                <span v-else class="text-gray-400">-</span>
              </td>
              <td class="p-2 border">{{ student.name }}</td>
              <td class="p-2 border">{{ student.nis }}</td>
              <td class="p-2 border">{{ student.class }}</td>
              <td class="p-2 border text-center">
                <!-- ✏️ Edit -->
                <button
                  @click="openEditModal(student)"
                  class="px-2 py-1 bg-yellow-400 rounded mr-2"
                >
                  ✏️
                </button>

                <!-- 🗑 Hapus -->
                <button
                  @click="deleteStudent(student.id)"
                  class="px-2 py-1 bg-red-500 text-white rounded"
                >
                  🗑
                </button>

                <!-- 🖨 Cetak Perorangan -->
                <a
                  :href="route('students.idcard', student.id)"
                  target="_blank"
                  class="ml-2 px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700"
                >
                  🖨 Cetak
                </a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- 🔗 Pagination -->
      <Pagination :links="props.students.links" class="mt-4" />

      <!-- 🖨️ Cetak Semua ID Card -->
      <div class="mt-10 border-t pt-6">
        <h2 class="text-xl font-semibold mb-4">Cetak Semua ID Card</h2>
        <div class="flex gap-4 mb-4">
          <button
            @click="startGeneration"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
            :disabled="isProcessing"
          >
            {{ isProcessing ? 'Sedang Memproses...' : 'Generate Semua ID Card' }}
          </button>

          <button
            v-if="progress?.status === 'processing'"
            @click="cancelGeneration"
            class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
          >
            Batalkan
          </button>
        </div>

        <!-- Progress Bar -->
        <div v-if="progress">
          <div class="w-full bg-gray-200 rounded-full h-4 mb-2">
            <div
              class="bg-green-500 h-4 rounded-full transition-all duration-500"
              :style="{ width: progress.percentage_complete + '%' }"
            ></div>
          </div>
          <p class="text-sm text-gray-600 mb-4">
            {{ progress.percentage_complete }}% selesai
            ({{ progress.completed_batches }}/{{ progress.total_batches }} batch)
          </p>

          <!-- Download link -->
          <div v-if="progress.status === 'completed' && progress.file_path">
            <a
              :href="`/${progress.file_path}`"
              target="_blank"
              class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
            >
              📥 Download PDF Terbaru
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- 📌 Modal Tambah/Edit -->
    <div
      v-if="isModalOpen"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">
          {{ editMode ? 'Edit Siswa' : 'Tambah Siswa' }}
        </h2>

        <form @submit.prevent="submit">
          <div class="mb-3">
            <label class="block mb-1">Nama</label>
            <input
              v-model="form.name"
              type="text"
              class="w-full px-3 py-2 border rounded"
              required
            />
          </div>

          <div class="mb-3">
            <label class="block mb-1">NIS</label>
            <input
              v-model="form.nis"
              type="text"
              class="w-full px-3 py-2 border rounded"
              required
            />
          </div>

          <div class="mb-3">
            <label class="block mb-1">Kelas</label>
            <input
              v-model="form.class"
              type="text"
              class="w-full px-3 py-2 border rounded"
              required
            />
          </div>

          <div class="mb-3">
            <label class="block mb-1">Foto</label>
            <input type="file" @change="handleFileChange" class="block w-full" />
            <div v-if="photoPreview" class="mt-2">
              <img
                :src="photoPreview"
                class="w-20 h-20 object-cover rounded border"
              />
            </div>
          </div>

          <div class="flex justify-end gap-3 mt-6">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 border rounded"
            >
              Batal
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
            >
              Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, watch, onMounted, onUnmounted } from 'vue'
import { debounce } from 'lodash'
import axios from 'axios'

// Props dari controller
const props = defineProps({
  students: Object, // paginator
  filters: Object,  // search filter
})

// 🔍 Search
const search = ref(props.filters.search || '')
watch(
  search,
  debounce((value) => {
    router.get(
      route('students.index'),
      { search: value },
      { preserveState: true, replace: true }
    )
  }, 300)
)

// 📝 Form & Modal
const editMode = ref(false)
const isModalOpen = ref(false)
const photoPreview = ref(null)

const form = useForm({
  id: null,
  name: '',
  nis: '',
  class: '',
  photo: null,
})

const openAddModal = () => {
  form.reset()
  editMode.value = false
  isModalOpen.value = true
}

const openEditModal = (student) => {
  form.id = student.id
  form.name = student.name
  form.nis = student.nis
  form.class = student.class
  photoPreview.value = student.photo ? `/storage/${student.photo}` : null
  editMode.value = true
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
  form.reset()
  photoPreview.value = null
}

const submit = () => {
  if (editMode.value) {
    form.put(route('students.update', form.id), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
    })
  } else {
    form.post(route('students.store'), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
    })
  }
}

const deleteStudent = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus siswa ini?')) {
    router.delete(route('students.destroy', id), {
      preserveScroll: true,
    })
  }
}

const handleFileChange = (event) => {
  const file = event.target.files[0]
  if (!file) return
  form.photo = file
  photoPreview.value = URL.createObjectURL(file)
}

// 📊 Progress Bar Generate ID Card
const progress = ref(null)
const isProcessing = ref(false)
let intervalId = null

const startGeneration = async () => {
  try {
    await axios.post(route('students.idcards.start'))
    isProcessing.value = true
    pollProgress()
  } catch (err) {
    console.error(err)
  }
}

const cancelGeneration = async () => {
  try {
    await axios.post(route('students.idcards.cancel'))
    isProcessing.value = false
    progress.value = null
    clearInterval(intervalId)
  } catch (err) {
    console.error(err)
  }
}

const fetchProgress = async () => {
  try {
    const res = await axios.get(route('students.idcards.progress'))
    progress.value = res.data

    if (progress.value?.status === 'completed') {
      isProcessing.value = false
      clearInterval(intervalId)
    }
  } catch (err) {
    console.error(err)
  }
}

const pollProgress = () => {
  fetchProgress()
  intervalId = setInterval(fetchProgress, 5000)
}

onMounted(fetchProgress)
onUnmounted(() => clearInterval(intervalId))
</script>
