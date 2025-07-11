<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { toast } from 'vue3-toastify';

defineProps<{
  error?: string;
}>();

const form = useForm({
  code: ''
});

function submitCode() {
  if (!form.code.trim()) {
    toast.error('Please enter a download code');
    return;
  }

  form.post(route('gallery.verify-code'), {
    onError: () => {
      toast.error('Invalid download code. Please try again.');
    }
  });
}
</script>

<template>
  <Head title="Enter Download Code" />

  <div class="min-h-screen bg-gradient-to-br from-pink-50 to-purple-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md">
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">💒 Wedding Photos</h1>
        <p class="text-gray-600">Enter your download code to access the photos</p>
      </div>

      <form @submit.prevent="submitCode" class="space-y-6">
        <div>
          <InputLabel for="code" value="Download Code" />
          <TextInput
            id="code"
            v-model="form.code"
            type="text"
            class="mt-1 block w-full uppercase tracking-wider"
            placeholder="Enter your code here"
            required
            autocomplete="off"
            :class="{ 'border-red-500': form.errors.code }"
          />
          <InputError :message="form.errors.code" class="mt-2" />
        </div>

        <PrimaryButton 
          type="submit"
          :disabled="form.processing || !form.code.trim()"
          class="w-full justify-center py-3 text-lg font-semibold bg-gradient-to-r from-pink-500 to-purple-600 hover:from-pink-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-200"
        >
          <span v-if="form.processing">Verifying...</span>
          <span v-else>🔓 Access Photos</span>
        </PrimaryButton>
      </form>

      <div class="mt-6 text-center text-sm text-gray-500">
        <p>Don't have a code? Contact the wedding couple!</p>
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