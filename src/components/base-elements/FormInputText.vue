<template>
  <div>
    <label
      v-if="showLabel || type === 'hidden'"
      :for="name"
      class="block text-sm font-medium text-gray-700"
      >{{ label }} <span v-if="required">*</span></label
    >
    <div class="relative mt-2 rounded-md shadow-sm">
      <input
        :disabled="disabled"
        :type="type"
        :name="name"
        :id="name"
        v-model="model"
        :placeholder="placeholder"
        v-on="validationListeners"
        @blur="handleBlur"
        :class="[
          isError
            ? 'block w-full rounded-md border-0 py-1.5 pr-10 text-red-900 ring-1 ring-inset ring-red-300 placeholder:text-red-300 focus:ring-2 focus:ring-inset focus:ring-red-500 sm:text-sm sm:leading-6'
            : 'block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6'
        ]"
      />
      <div
        v-if="isError"
        class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3"
      >
        <ExclamationCircleIcon class="h-5 w-5 text-red-500" aria-hidden="true" />
      </div>
    </div>

    <p v-if="isError" class="mt-2 text-sm text-red-600">{{ errorMessage }}</p>
  </div>
</template>

<script setup>
import { useField } from 'vee-validate'
import { computed, defineModel } from 'vue'
import { ExclamationCircleIcon } from '@heroicons/vue/20/solid'

const props = defineProps({
  name: {
    type: String,
    required: true
  },
  type: {
    type: String,
    default: 'text'
  },
  label: {
    type: String
  },
  placeholder: {
    type: String,
    default: ''
  },
  showLabel: {
    type: Boolean,
    default: true
  },
  showError: {
    type: Boolean,
    default: true
  },
  disabled: {
    type: Boolean,
    default: false
  },
  required: {
    type: Boolean,
    default: false
  }
})

const model = defineModel()
const { handleChange, handleBlur, errorMessage, meta } = useField(() => props.name, undefined, {
  initialValue: model,
  validateOnValueUpdate: false
})

const isError = computed(() => {
  return props.showError && meta.errors.length > 0
})

const validationListeners = computed(() => {
  if (!errorMessage.value) {
    return {
      blur: handleChange,
      change: handleChange,
      input: (e) => handleChange(e, false)
    }
  }
  return {
    blur: handleChange,
    change: handleChange,
    input: handleChange
  }
})
</script>
