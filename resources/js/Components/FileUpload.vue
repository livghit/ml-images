<script setup lang="ts">
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import {FileWithPreview} from "@/types";

const form = useForm({
  photos: [] as FileWithPreview[]
});

const fileInput = ref<HTMLInputElement | null>(null);
const dropZone = ref<HTMLDivElement | null>(null);
const isDragging = ref(false);
const uploadMessage = ref<string | null>(null);

function generatePreviews(files: FileWithPreview[]) {
  return files.map(file => {
    if (!file.preview) {
      file.preview = URL.createObjectURL(file);
    }
    return file;
  });
}

function handleFileChange(e: Event) {
  const target = e.target as HTMLInputElement;
  if (target.files) {
    form.photos = generatePreviews([...target.files]);
  }
}

function handleDrop(e: DragEvent) {
  e.preventDefault();
  isDragging.value = false;

  if (e.dataTransfer?.files) {
    form.photos = generatePreviews([...e.dataTransfer.files]);
  }
}

function removeFile(index: number) {
  if (form.photos[index]?.preview) {
    URL.revokeObjectURL(form.photos[index].preview!);
  }
  form.photos.splice(index, 1);
}

function upload() {
  form.post(route('upload.photos'), {
    preserveScroll: true,
    onSuccess: () => {
      form.photos.forEach(file => {
        if (file.preview) URL.revokeObjectURL(file.preview);
      });
      form.reset('photos');
      uploadMessage.value = "Upload successful!";
      if (fileInput.value) {
        fileInput.value.value = '';
      }
    },
  });
}

// Cleanup previews when component unmounts
watch(() => form.photos, (files) => {
  if (files.length === 0) {
    form.photos.forEach(file => {
      if (file.preview) URL.revokeObjectURL(file.preview);
    });
  }
});
</script>

<template>
  <Head title="Upload Picture" />

  <div class="w-full">
    <!-- Drop Zone -->
    <div
      ref="dropZone"
      class="relative border-2 border-dashed border-gray-300 rounded-lg p-8 text-center transition-all"
      :class="{ 'border-primary-600 bg-primary-50': isDragging }"
      @dragenter.prevent="isDragging = true"
      @dragleave.prevent="isDragging = false"
      @dragover.prevent
      @drop="handleDrop"
    >
      <input
        ref="fileInput"
        type="file"
        multiple
        accept="image/*"
        @change="handleFileChange"
        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
      />

      <div class="space-y-2">
        <div class="flex items-center justify-center">
          <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </div>
        <div class="text-gray-600">
          <span class="font-medium">Drop your images here</span> or click to select
        </div>
        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 50MB and max of 50 Images</p>
      </div>
    </div>

    <!-- Preview Grid -->
    <div v-if="form.photos.length" class="mt-8 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
      <div
        v-for="(photo, index) in form.photos"
        :key="index"
        class="relative group aspect-square rounded-lg overflow-hidden bg-gray-100"
      >
        <img
          :src="photo.preview"
          :alt="photo.name"
          class="w-full h-full object-cover"
        />
        <button
          @click.prevent="removeFile(index)"
          class="absolute top-2 right-2 p-1 rounded-full bg-red-500 text-white opacity-0 group-hover:opacity-100 transition-opacity"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Error Message -->
    <div v-if="form.errors.photos" class="mt-2 text-sm text-red-600">
      {{ form.errors.photos }}
    </div>

    <!-- Upload Button -->
    <div class="mt-4 flex justify-end gap-4 items-center">
      <p v-if="uploadMessage" class="text-sm text-green-600">{{ uploadMessage }}</p>
      <PrimaryButton
        type="submit"
        :disabled="form.processing || form.photos.length === 0"
        @click="upload"
        class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors disabled:opacity-50"
      >
        {{ form.processing ? 'Uploading...' : 'Upload' }}
      </PrimaryButton>
    </div>
  </div>
</template>

<style scoped>
.border-primary-600 {
  border-color: rgb(79 70 229);
}
.bg-primary-50 {
  background-color: rgb(238 242 255);
}
.bg-primary-600 {
  background-color: rgb(79 70 229);
}
.hover\:bg-primary-700:hover {
  background-color: rgb(67 56 202);
}
</style>
