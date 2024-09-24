<template>
  <Combobox
    as="div"
    :modelValue="value"
    @update:modelValue="(val) => $emit('update:modelValue', val)"
    :by="keyProp"
    class="sm:col-span-6"
  >
    <ComboboxLabel class="block text-sm font-medium leading-6 text-gray-900">
      {{ props.label }}
    </ComboboxLabel>
    <p class="mt-2 text-grey-600">{{ props.helper }}</p>
    <div class="relative mt-2">
      <ComboboxInput
        class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
        @change="query = $event.target.value"
        @blur="query = ''"
        :display-value="(option) => option?.[labelProp]"
      />
      <ComboboxButton
        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none"
      >
        <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
      </ComboboxButton>

      <ComboboxOptions
        v-if="filteredOptions.length > 0"
        class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
      >
        <ComboboxOption
          v-for="option in filteredOptions"
          :key="option[keyProp]"
          :value="option"
          as="template"
          v-slot="{ active, selected }"
        >
          <li
            :class="[
              'relative cursor-default select-none py-2 pl-3 pr-9',
              active ? 'bg-indigo-600 text-white' : 'text-gray-900'
            ]"
          >
            <span :class="['block truncate', selected && 'font-semibold']">
              {{ option[labelProp] }}
            </span>

            <span
              v-if="selected"
              :class="[
                'absolute inset-y-0 right-0 flex items-center pr-4',
                active ? 'text-white' : 'text-indigo-600'
              ]"
            >
              <CheckIcon class="h-5 w-5" aria-hidden="true" />
            </span>
          </li>
        </ComboboxOption>
      </ComboboxOptions>
    </div>
  </Combobox>
</template>
<script setup>
import {
  Combobox,
  ComboboxButton,
  ComboboxInput,
  ComboboxLabel,
  ComboboxOption,
  ComboboxOptions
} from '@headlessui/vue'
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid'
import { ref, computed } from 'vue'

const props = defineProps({
  type: {
    type: String,
    default: 'text'
  },
  value: {
    type: Object,
    default: undefined
  },
  name: {
    type: String,
    required: true
  },
  label: {
    type: String,
    required: true
  },
  helper: {
    type: String,
    default: undefined
  },
  successMessage: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: ''
  },
  options: {
    type: Array,
    required: true
  },
  keyProp: {
    type: String,
    required: true
  },
  labelProp: {
    type: String,
    required: true
  }
})

const query = ref('')
const filteredOptions = computed(() =>
  query.value === ''
    ? props.options
    : props.options.filter((option) => {
        return option[props.labelProp].toLowerCase().includes(query.value.toLowerCase())
      })
)
</script>
