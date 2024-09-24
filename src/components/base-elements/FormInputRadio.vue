<template>
  <div class="sm:col-span-6 pt-10">
    <RadioGroup v-model="value" :disabled="disabled" by="value">
      <RadioGroupLabel class="block text-sm font-medium leading-6 text-gray-900">
        {{ label }} <span v-if="required">*</span>
      </RadioGroupLabel>

      <fieldset :aria-label="label" class="mt-4">
        <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
          <RadioGroupOption
            as="template"
            v-for="option in options"
            :key="option.value"
            :value="option"
            v-slot="{ active, checked }"
          >
            <div
              :class="[
                checked ? 'border-transparent checked' : 'border-gray-300',
                active ? 'ring-2 ring-indigo-500' : '',
                'relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none'
              ]"
            >
              <span class="flex flex-1">
                <span class="flex flex-col">
                  <span class="block text-sm font-medium text-gray-900">{{ option.text }}</span>
                </span>
              </span>
              <CheckCircleIcon v-if="checked" class="h-5 w-5 text-indigo-600" aria-hidden="true" />
              <span
                :class="[
                  active ? 'border' : 'border-2',
                  checked ? 'border-indigo-500 checked' : 'border-transparent',
                  'pointer-events-none absolute -inset-px rounded-lg'
                ]"
                aria-hidden="true"
              />
            </div>
          </RadioGroupOption>
        </div>
      </fieldset>
    </RadioGroup>
    <span v-if="showError && !meta.valid">{{ errorMessage }}</span>
  </div>
</template>

<script setup>
import { useField } from 'vee-validate'
import { watch } from 'vue'
import { RadioGroup, RadioGroupOption, RadioGroupLabel } from '@headlessui/vue'
import { CheckCircleIcon } from '@heroicons/vue/20/solid'

const props = defineProps({
  modelValue: {
    type: Object
  },
  name: {
    type: String,
    required: true
  },
  label: {
    type: String
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
  },
  options: {
    type: Array
  }
})

const emit = defineEmits(['update:modelValue'])

const { value, errorMessage, meta } = useField(props.name, props.rules, {
  initialValue: props.modelValue
})

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
