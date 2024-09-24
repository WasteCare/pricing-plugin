<template>
  <div class="relative flex items-start">
    <div class="flex h-6 items-center">
      <input
        :name="name"
        :id="name"
        type="checkbox"
        :checked="checked"
        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
        @change="handleChange"
      />
    </div>
    <div class="ml-3 text-sm leading-6">
      <label :for="name" class="font-medium text-gray-900">{{ label }}</label>
      <p id="comments-description" class="text-gray-500">{{ message }}</p>
      <p v-if="meta.errors.length > 0" class="mt-2 text-sm text-red-600">{{ errorMessage }}</p>
    </div>
  </div>
</template>
<script setup>
import { useField } from 'vee-validate'
import { defineProps, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: null
  },
  name: {
    type: String,
    required: true
  },
  checkedValue: {
    type: Boolean
  },
  label: {
    type: String
  },
  message: {
    type: String
  },
  showError: {
    type: Boolean
  }
})

const { value, handleChange, checked, errorMessage, meta } = useField(() => props.name, undefined, {
  checkedValue: true,
  uncheckedValue: false,
  type: 'checkbox'
})

const emit = defineEmits(['update:modelValue'])

// Sync v-model binding with vee-validate model changes
watch(value, (newValue) => {
  if (newValue !== props.modelValue) {
    emit('update:modelValue', newValue)
  }
})

// Sync vee-validate's model with external model changes.
watch(
  () => props.modelValue,
  (newModel) => {
    if (newModel !== value.value) {
      value.value = newModel
    }
  }
)
</script>
