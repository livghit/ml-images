<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Pagination from '@/Components/Pagination.vue';
import { toast } from 'vue3-toastify';

interface Photo {
  id: number;
  name: string;
  path: string;
  mime: string;
  created_at: string;
}

interface PaginatedPhotos {
  data: Photo[];
  links: any[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number;
  to: number;
}

defineProps<{
  photos: PaginatedPhotos;
}>();

const selectedPhotos = ref<Set<number>>(new Set());
const isDownloading = ref(false);

const selectedCount = computed(() => selectedPhotos.value.size);

function togglePhoto(photoId: number) {
  if (selectedPhotos.value.has(photoId)) {
    selectedPhotos.value.delete(photoId);
  } else {
    selectedPhotos.value.add(photoId);
  }
}

function selectAll(photos: Photo[]) {
  photos.forEach(photo => selectedPhotos.value.add(photo.id));
}

function clearSelection() {
  selectedPhotos.value.clear();
}

function downloadSelected() {
  if (selectedPhotos.value.size === 0) {
    toast.warning('Please select at least one photo to download');
    return;
  }

  isDownloading.value = true;
  
  const photoIds = Array.from(selectedPhotos.value);
  
  // Create form data for POST request with proper CSRF
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = route('gallery.download');
  form.style.display = 'none';

  // Add CSRF token from meta tag
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (csrfToken) {
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);
  }

  // Add photo IDs
  photoIds.forEach(id => {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'photo_ids[]';
    input.value = id.toString();
    form.appendChild(input);
  });

  document.body.appendChild(form);
  form.submit();
  document.body.removeChild(form);

  setTimeout(() => {
    isDownloading.value = false;
    toast.success(`🎉 Started downloading ${selectedCount.value} photos!`);
  }, 1000);
}

function logout() {
  router.post(route('gallery.logout'));
}
</script>

<template>
  <Head title="Wedding Photo Gallery" />

  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex justify-between items-center">
          <h1 class="text-2xl font-bold text-gray-900">💒 Wedding Photo Gallery</h1>
          <SecondaryButton @click="logout" class="text-sm">
            Exit Gallery
          </SecondaryButton>
        </div>
      </div>
    </div>

    <!-- Controls -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
          <div class="text-sm text-gray-600">
            <span class="font-medium">{{ photos.total }}</span> photos total
            <span v-if="selectedCount > 0" class="ml-4">
              <span class="font-medium text-purple-600">{{ selectedCount }}</span> selected
            </span>
          </div>
          
          <div class="flex gap-3">
            <SecondaryButton 
              @click="selectAll(photos.data)"
              :disabled="photos.data.length === 0"
              class="text-sm"
            >
              Select All on Page
            </SecondaryButton>
            
            <SecondaryButton 
              @click="clearSelection"
              :disabled="selectedCount === 0"
              class="text-sm"
            >
              Clear Selection
            </SecondaryButton>
            
            <PrimaryButton 
              @click="downloadSelected"
              :disabled="selectedCount === 0 || isDownloading"
              class="text-sm font-semibold bg-gradient-to-r from-pink-500 to-purple-600 hover:from-pink-600 hover:to-purple-700"
            >
              <span v-if="isDownloading">Preparing Download...</span>
              <span v-else>📥 Download Selected ({{ selectedCount }})</span>
            </PrimaryButton>
          </div>
        </div>
      </div>

      <!-- Photo Grid -->
      <div v-if="photos.data.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 mb-8">
        <div
          v-for="photo in photos.data"
          :key="photo.id"
          class="relative group cursor-pointer aspect-square"
          @click="togglePhoto(photo.id)"
        >
          <img
            :src="`/storage/${photo.path}`"
            :alt="photo.name"
            class="w-full h-full object-cover rounded-lg transition-all duration-200"
            :class="{
              'ring-4 ring-purple-500 ring-offset-2': selectedPhotos.has(photo.id),
              'group-hover:scale-105': !selectedPhotos.has(photo.id)
            }"
          />
          
          <!-- Selection overlay -->
          <div 
            v-if="selectedPhotos.has(photo.id)"
            class="absolute inset-0 bg-purple-500 bg-opacity-20 rounded-lg flex items-center justify-center"
          >
            <div class="bg-purple-500 text-white rounded-full p-2">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
          </div>

          <!-- Hover overlay -->
          <div 
            v-else
            class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded-lg flex items-center justify-center"
          >
            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 bg-white rounded-full p-2">
              <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- No photos message -->
      <div v-else class="text-center py-12">
        <div class="text-gray-400 text-6xl mb-4">📷</div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No photos yet</h3>
        <p class="text-gray-500">Photos will appear here once they're uploaded!</p>
      </div>

      <!-- Pagination -->
      <div v-if="photos.last_page > 1" class="flex justify-center">
        <Pagination :links="photos.links" />
      </div>
    </div>
  </div>
</template>

<style scoped>
.bg-gradient-to-r {
  background: linear-gradient(to right, #ec4899, #9333ea);
}

.hover\:from-pink-600:hover {
  background: linear-gradient(to right, #db2777, #7c3aed);
}
</style>