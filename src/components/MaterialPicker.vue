<template>
  <div class="bg-white">
    <div>
      <!-- Mobile filter dialog -->
      <TransitionRoot as="template" :show="mobileFiltersOpen">
        <Dialog class="relative z-40 lg:hidden" @close="mobileFiltersOpen = false">
          <TransitionChild
            as="template"
            enter="transition-opacity ease-linear duration-300"
            enter-from="opacity-0"
            enter-to="opacity-100"
            leave="transition-opacity ease-linear duration-300"
            leave-from="opacity-100"
            leave-to="opacity-0"
          >
            <div class="fixed inset-0 bg-black bg-opacity-25" />
          </TransitionChild>

          <div class="fixed inset-0 z-40 flex">
            <TransitionChild
              as="template"
              enter="transition ease-in-out duration-300 transform"
              enter-from="translate-x-full"
              enter-to="translate-x-0"
              leave="transition ease-in-out duration-300 transform"
              leave-from="translate-x-0"
              leave-to="translate-x-full"
            >
              <DialogPanel
                class="relative ml-auto flex h-full w-full max-w-xs flex-col overflow-y-auto bg-white py-4 pb-6 shadow-xl"
              >
                <div class="flex items-center justify-between px-4">
                  <h2 class="text-lg font-medium text-gray-900">Filters</h2>
                  <button
                    type="button"
                    class="-mr-2 flex h-10 w-10 items-center justify-center p-2 text-gray-400 hover:text-gray-500"
                    @click="mobileFiltersOpen = false"
                  >
                    <span class="sr-only">Close menu</span>
                    <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                  </button>
                </div>

                <!-- Filters -->
                <form class="mt-4">
                  <Disclosure
                    as="div"
                    v-for="section in filters"
                    :key="section.header"
                    class="border-t border-gray-200 pb-4 pt-4"
                    v-slot="{ open }"
                  >
                    <fieldset>
                      <legend class="w-full px-2">
                        <DisclosureButton
                          class="flex w-full items-center justify-between p-2 text-gray-400 hover:text-gray-500"
                        >
                          <span class="text-sm font-medium text-gray-900">{{
                            section.header
                          }}</span>
                          <span class="ml-6 flex h-7 items-center">
                            <ChevronDownIcon
                              :class="[open ? '-rotate-180' : 'rotate-0', 'h-5 w-5 transform']"
                              aria-hidden="true"
                            />
                          </span>
                        </DisclosureButton>
                      </legend>
                      <DisclosurePanel class="px-4 pb-2 pt-4">
                        <div class="space-y-6">
                          <div
                            v-for="(option, optionIdx) in section.options"
                            :key="option.id"
                            class="flex items-center"
                          >
                            <input
                              :id="`${section.id}-${optionIdx}-mobile`"
                              :name="`${section.header}[]`"
                              :value="option.name"
                              type="checkbox"
                              class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />
                            <label
                              :for="`${section.id}-${optionIdx}-mobile`"
                              class="ml-3 text-sm text-gray-500"
                              >{{ option.name }}</label
                            >
                          </div>
                        </div>
                      </DisclosurePanel>
                    </fieldset>
                  </Disclosure>
                </form>
              </DialogPanel>
            </TransitionChild>
          </div>
        </Dialog>
      </TransitionRoot>

      <main class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
        <div class="border-b border-gray-200 pb-10">
          <h1 class="text-4xl font-bold tracking-tight text-gray-900">Select your waste</h1>
          <p class="mt-4 text-base text-gray-500">If you need any help, please call us!</p>
        </div>
        <Popover as="template" v-slot="{ open }">
          <header
            :class="[
              open ? 'fixed inset-0 z-40 overflow-y-auto' : '',
              'bg-white shadow-sm lg:sticky lg:top-0 lg:overflow-y-visible'
            ]"
          >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
              <div class="relative flex justify-end lg:gap-8">
                <div class="min-w-0 flex-1 md:px-8 lg:px-0">
                  <Button @click="prev" type="button">Previous</Button>
                  <div
                    class="flex items-center px-6 py-4 md:mx-auto md:max-w-3xl lg:mx-0 lg:max-w-none xl:px-0"
                  >
                    <div class="w-full">
                      <label for="search" class="sr-only">Search</label>
                      <div class="relative">
                        <div
                          class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                        >
                          <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                        </div>
                        <input
                          id="search"
                          name="search"
                          class="block w-full rounded-md border-0 bg-white py-1.5 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6"
                          placeholder="Search"
                          type="search"
                          v-model="query"
                        />
                      </div>
                    </div>
                  </div>
                </div>
                <div class="flex items-center md:absolute md:inset-y-0 md:right-0 lg:hidden">
                  <!-- Mobile menu button -->
                  <PopoverButton
                    class="relative -mx-2 inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                  >
                    <span class="absolute -inset-0.5" />
                    <span class="sr-only">Open menu</span>
                    <Bars3Icon v-if="!open" class="block h-6 w-6" aria-hidden="true" />
                    <XMarkIcon v-else class="block h-6 w-6" aria-hidden="true" />
                  </PopoverButton>
                </div>
                <div class="hidden lg:flex lg:items-center lg:justify-end xl:col-span-4">
                  <Popover>
                    <PopoverButton
                      class="relative ml-5 flex-shrink-0 rounded-full bg-white p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                      <span class="absolute -inset-1.5" />
                      <span class="sr-only">View notifications</span>
                      <ShoppingCartIcon class="h-6 w-6" aria-hidden="true" />
                      <div
                        class="absolute inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full -top-2 -end-2 dark:border-gray-900"
                      >
                        {{ selectedMaterials.length }}
                      </div>
                    </PopoverButton>
                    <transition
                      enter-active-class="transition ease-out duration-200"
                      enter-from-class="opacity-0 translate-y-1"
                      enter-to-class="opacity-100 translate-y-0"
                      leave-active-class="transition ease-in duration-150"
                      leave-from-class="opacity-100 translate-y-0"
                      leave-to-class="opacity-0 translate-y-1"
                    >
                      <PopoverPanel
                        class="absolute left-1/2 z-10 mt-5 flex w-screen max-w-max -translate-x-1/2 px-4"
                      >
                        <div
                          class="w-screen max-w-md flex-auto overflow-hidden rounded-3xl bg-white text-sm leading-6 shadow-lg ring-1 ring-gray-900/5"
                        >
                          <div class="p-4">
                            <MaterialSummaryItem
                              v-for="material in selectedMaterials"
                              :key="material.matKey"
                              :id="material.matKey"
                              :name="material.materialCustomerName"
                              :description="material.materialCustomerDescription"
                              :imageLink="material.imageLink"
                              :qty="material?.qty"
                              :price="material.price"
                              @remove="removeMaterial"
                              @change-qty="updateMaterialQty"
                            />
                          </div>
                          <div class="grid grid-cols-2 divide-x divide-gray-900/5 bg-gray-50"></div>
                        </div>
                      </PopoverPanel>
                    </transition>
                    <!--    <button type="button" class="relative inline-flex items-center p-3 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"> -->
                  </Popover>
                  <Button @click="next" type="button">Next</Button>
                </div>
              </div>
            </div>
          </header>
        </Popover>

        <div class="pt-12 lg:grid lg:grid-cols-3 lg:gap-x-8 xl:grid-cols-4">
          <aside>
            <h2 class="sr-only">Filters</h2>

            <button
              type="button"
              class="inline-flex items-center lg:hidden"
              @click="mobileFiltersOpen = true"
            >
              <span class="text-sm font-medium text-gray-700">Filters</span>
              <PlusIcon class="ml-1 h-5 w-5 flex-shrink-0 text-gray-400" aria-hidden="true" />
            </button>

            <div class="hidden lg:block">
              <form class="space-y-10 divide-y divide-gray-200">
                <div
                  v-for="(section, sectionIdx) in filters"
                  :key="section.header"
                  :class="sectionIdx === 0 ? null : 'pt-10'"
                >
                  <fieldset>
                    <legend class="block text-sm font-medium text-gray-900">
                      {{ section.header }}
                    </legend>
                    <div class="space-y-3 pt-6">
                      <div
                        v-for="(option, optionIdx) in section.options"
                        :key="option.id"
                        class="flex items-center"
                      >
                        <input
                          :id="`${section.header}-${optionIdx}`"
                          :name="`${section.id}[]`"
                          :value="option.id"
                          v-model="selectedFilters"
                          type="checkbox"
                          class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        />
                        <label
                          :for="`${section.header}-${optionIdx}`"
                          class="ml-3 text-sm text-gray-600"
                          >{{ option.name }}</label
                        >
                      </div>
                    </div>
                  </fieldset>
                </div>
              </form>
            </div>
          </aside>

          <!-- Product grid -->
          <div
            class="grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:col-span-3 lg:grid-cols-4 lg:gap-x-8"
          >
            <div
              class="overflow-hidden rounded-lg bg-white shadow flex flex-col justify-between"
              v-for="material in searchResults"
              :key="material.matKey"
              href="#"
            >
              <div class="px-4 py-5 sm:p-6">
                <!-- Content goes here -->
                <div
                  class="aspect-h-1 aspect-w-1 w-full overflow-hidden rounded-lg bg-gray-100 group-hover:opacity-75"
                >
                  <img :src="material.imageLink" class="h-full w-full object-cover object-center" />
                </div>
                <h3 class="mt-4 font-medium text-gray-900">
                  {{ material.matKey }} | {{ material.materialCustomerName }}
                </h3>
                <p class="italic text-gray-500">{{ material.materialCustomerDescription }}</p>
                <p class="mt-2 font-medium text-gray-900">{{ material.price }}</p>
              </div>
              <div class="bg-gray-50 px-4 py-4 sm:px-6">
                <!-- Content goes here -->
                <!-- We use less vertical padding on card footers at all sizes than on headers or body sections -->
                <div class="w-full sm:max-w-xs">
                  <label for="quantity" class="sr-only">Email</label>
                  <input
                    type="number"
                    v-model="material.qty"
                    name="quantity"
                    id="quantity"
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    placeholder="1"
                  />
                </div>
                <button
                  type="button"
                  @click="addToQuote(material, this)"
                  class="mt-3 inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:ml-3 sm:mt-0 sm:w-auto"
                >
                  Add to Quote
                </button>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { inject, ref, computed, defineModel } from 'vue'
import {
  Dialog,
  DialogPanel,
  Disclosure,
  DisclosureButton,
  DisclosurePanel,
  TransitionChild,
  TransitionRoot,
  Popover,
  PopoverButton,
  PopoverPanel
} from '@headlessui/vue'
import Button from './base-elements/Button.vue'
import MaterialSummaryItem from './MaterialSummaryItem.vue'
import { XMarkIcon, Bars3Icon, ShoppingCartIcon } from '@heroicons/vue/24/outline'
import { ChevronDownIcon, PlusIcon, MagnifyingGlassIcon } from '@heroicons/vue/20/solid'

const mobileFiltersOpen = ref(false)
const materials = inject('materials')
const query = ref('')
const model = defineModel()

const filters = Object.entries(
  Object.groupBy(
    materials
      .flatMap((x) => {
        return x.materialCategories
      })
      .map((t) => {
        return {
          header: t.categoryHeader,
          id: t.categories[0].categoryId,
          name: t.categories[0].categoryName
        }
      })
      .filter(
        (value, index, self) =>
          index === self.findIndex((t) => t.id === value.id && t.header === value.header)
      ),
    ({ header }) => header
  )
).map(([key, value]) => {
  return { header: key, options: value }
})

const selectedFilters = ref([])
const selectedMaterials = model

const searchResults = computed(() =>
  query.value === ''
    ? filteredMaterials.value
    : filteredMaterials.value.filter((material) => {
        return `${material.materialCustomerName}${material.materialCustomerDescription}`
          .toLowerCase()
          .includes(query.value.toLowerCase())
      })
)

const filteredMaterials = computed(() => {
  if (selectedFilters.value.length === 0) return materials

  let mats = materials.filter((material) => {
    return material.materialCategories.some((matcat) => {
      return matcat.categories.flat().some((category) => {
        return selectedFilters.value.includes(category.categoryId)
      })
    })
  })

  return mats
})

function addToQuote(mat) {
  if (!selectedMaterials.value.some((material) => material.matKey === mat.matKey)) {
    selectedMaterials.value.push(mat)
  } else {
    //update the quantities
  }
}

function next() {
  emit('next')
}

function prev() {
  emit('prev')
}

const emit = defineEmits(['next', 'prev'])
</script>
