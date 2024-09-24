<script setup>
import FormInputText from './base-elements/FormInputText.vue'
import { defineEmits, defineProps, ref } from 'vue'
import { TrashIcon } from '@heroicons/vue/20/solid'

const props = defineProps({
  id: {
    type: String
  },
  name: {
    type: String
  },
  description: {
    type: String
  },
  imageLink: {
    type: String
  },
  qty: {
    type: Number
  },
  price: {
    type: Number
  }
})

const iqty = ref(props.qty)

defineEmits(['remove', 'changeQty'])
</script>

<template>
  <li class="flex px-4 py-6 sm:px-6">
    <div class="flex-shrink-0">
      <img :src="imageLink" alt="" class="w-20 rounded-md" />
    </div>
    <div class="ml-6 flex flex-1 flex-col">
      <div class="flex">
        <div class="min-w-0 flex-1">
          <h4 class="text-sm">{{ id }} | {{ name }}</h4>
          <p class="mt-1 text-sm text-gray-500">
            {{ description }}
          </p>
        </div>
        <div class="ml-4 flow-root flex-shrink-0">
          <button
            type="button"
            @click="$emit('remove', id)"
            class="-m-2.5 flex items-center justify-center bg-white p-2.5 text-gray-400 hover:text-gray-500"
          >
            <span class="sr-only">Remove</span>
            <TrashIcon class="h-5 w-5" aria-hidden="true" />
          </button>
        </div>
      </div>
      <div class="flex flex-1 items-end justify-between pt-2">
        <p class="mt-1 text-sm font-medium text-gray-900">{{ price }}</p>
        <div class="ml-4">
          <label for="quantity" class="sr-only">Quantity</label>
          <FormInputText
            id="quantity"
            name="quantity"
            v-model="iqty"
            @input="$emit('changeQty', id, iqty)"
          >
          </FormInputText>
        </div>
      </div>
    </div>
  </li>
</template>
