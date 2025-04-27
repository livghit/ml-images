<script setup lang="ts">
import FileUpload from '@/Components/FileUpload.vue';
import { Head , usePage , Link } from '@inertiajs/vue3';
import {computed} from "vue";

interface Photo {
  id: number;
  name: string;
  path: string;
  mime: string;
}

interface PhotosPagination {
  data: Photo[];
  links: any;
}

const page = usePage();
const props = computed(() => page.props.photos as PhotosPagination);

</script>

<template>
  <Head title="Photo Gallery" />

  <div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-slate-800 mb-4">ML - Photo Gallery</h1>
        <p class="text-lg text-slate-600">Upload and share your favorite pictures</p>
      </div>

      <!-- Upload Section -->
      <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <FileUpload />
      </div>

      <!-- Gallery Section -->
      <div class="space-y-6">
        <h2 class="text-2xl font-semibold text-gray-800">Latest Uploads</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <div v-for="photo in props.data" :key="photo.id"
               class="group relative bg-white rounded-lg shadow-sm overflow-hidden">
            <img
              :src="`/storage/${photo.path}`"
              :alt="photo.name"
              class="w-full h-64 object-cover transform transition-transform duration-300 group-hover:scale-105"
            >
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-4">
              <p class="text-white text-sm truncate">{{ photo.name }}</p>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="!props.data.length"
             class="text-center py-12 bg-white rounded-lg">
          <p class="text-gray-500">No photos uploaded yet</p>
        </div>

        <!-- Pagination -->
        <div class="mt-6 flex justify-center">
            <Link
                v-for="link in props.links"
                preserve-scroll
                :key="link.label"
                :href="link.url"
                v-html = "link.label"
                class="inline-block px-4 py-2 mx-1 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring focus:ring-primary-500"
                :class="{ 'bg-gray-200': link.active }"
            />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.group:hover img {
  opacity: 0.9;
}
</style>
