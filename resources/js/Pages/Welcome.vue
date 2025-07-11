<script setup lang="ts">
import FileUpload from '@/Components/FileUpload.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
  <Head title="Wedding Photo Upload" />

  <div class="min-h-screen bg-gradient-to-br from-pink-50 to-purple-50 flex items-center justify-center p-4">
    <div class="w-full max-w-2xl">
      <!-- Header -->
      <div class="text-center mb-8">
        <div class="text-5xl mb-4">💒</div>
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-3">
          Wedding Photo Upload
        </h1>
        <p class="text-lg text-gray-600 max-w-lg mx-auto">
          Share your beautiful memories from this special day. Upload your favorite photos to create lasting memories together.
        </p>
      </div>

      <!-- Main Upload Card -->
      <div class="bg-white rounded-2xl shadow-xl p-8 sm:p-10 mb-8">
        <!-- Upload Section -->
        <div class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-2 text-center">Upload Your Photos</h2>
          <p class="text-gray-600 text-center mb-6">
            Drag & drop or click to select up to 50 photos at once
          </p>
          
          <FileUpload />
        </div>

        <!-- Features -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
          <div class="text-center p-4 bg-pink-50 rounded-xl">
            <div class="text-pink-500 text-2xl mb-2">📸</div>
            <div class="text-sm font-medium text-gray-800">Easy Upload</div>
          </div>
          
          <div class="text-center p-4 bg-purple-50 rounded-xl">
            <div class="text-purple-500 text-2xl mb-2">🔒</div>
            <div class="text-sm font-medium text-gray-800">Safe & Secure</div>
          </div>
          
          <div class="text-center p-4 bg-blue-50 rounded-xl">
            <div class="text-blue-500 text-2xl mb-2">💝</div>
            <div class="text-sm font-medium text-gray-800">Share & Download</div>
          </div>
        </div>

        <!-- Download Gallery Link -->
        <div class="text-center pt-6 border-t border-gray-100">
          <p class="text-gray-600 mb-3">Want to download photos?</p>
          <Link 
            href="/gallery" 
            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-medium rounded-lg hover:from-pink-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg"
          >
            🎁 Access Photo Gallery
          </Link>
          <p class="text-xs text-gray-500 mt-2">Enter your special download code</p>
        </div>
      </div>

      <!-- Footer -->
      <div class="text-center text-gray-500 text-sm">
        <p>Thank you for celebrating with us! 💕</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.bg-gradient-to-br {
  background: linear-gradient(to bottom right, #fdf2f8, #faf5ff);
}

.bg-gradient-to-r {
  background: linear-gradient(to right, #ec4899, #9333ea);
}

.hover\:from-pink-600:hover {
  background: linear-gradient(to right, #db2777, #7c3aed);
}
</style>
