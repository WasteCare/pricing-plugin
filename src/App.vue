<script setup>
import Button from './components/base-elements/Button.vue'
import MaterialPicker from './components/MaterialPicker.vue'
import ComboBoxList from './components/base-elements/ComboBoxList.vue'
import FormInputText from './components/base-elements/FormInputText.vue'
import FormInputRadio from './components/base-elements/FormInputRadio.vue'
import { Field } from 'vee-validate'
import FormInputAccept from './components/base-elements/FormInputAccept.vue'
import * as yup from 'yup'
import { ref, computed, inject, watch } from 'vue'
import { useForm } from 'vee-validate'
import MaterialSummaryItem from './components/MaterialSummaryItem.vue'
import { postForm } from './services/ApiService'

const currentStep = ref(0)
const stepLength = ref(3)
const formWizard = ref()

const fields = inject('formFields', 'no industries')
const industries = inject('industryTypes')
const url = inject('formUrl')
const pricePlans = inject('pricePlans')
const eaFee = inject('eaFee')
const selectedIndustry = ref(null)

// Refs for stuff we want to post back, vee validate is a PITA and it's easier to track separately

const address = ref('')
const businessName = ref('')
const contactName = ref()
const email = ref()
const existingCustomer = ref(fields.existingCustomer.choices[0])
const marketingOi = ref()
const materials = ref([])
/* multiple sites */
const multipleSites = ref(
  fields.multipleSites.choices.filter((choice) => {
    return choice.isSelected
  })[0]
)
const phone = ref('')
const privacyOi = ref(false)
const pricingCalcs = ref({
  eaFee: 0,
  transportCharge: 0,
  rebate: 0,
  totalCharge: 0
})

const schemas = {
  0: yup.object({
    contactName: yup.string().required(),
    businessName: yup.string().required(),
    address: yup.string().required()
  }),
  alt0: yup.object({
    selectedIndustry: yup.object().required(),
    privacy: yup.bool().oneOf([true], 'You must accept the privacy policy'),
    multipleSites: yup.object().required()
  }),
  1: yup.object({}),
  2: yup.object({
    //existing customer
    address: yup.string().required(),
    contactName: yup.string().required(),
    businessName: yup.string().required(),
    multipleSites: yup.object().required(),
    email: yup.string().email().required(),
    privacy: yup.bool().oneOf([true], 'You must accept the privacy policy'),
    phone: yup.string().required()
  }),
  alt2: yup.object({
    //new customer
    address: yup.string().required(),
    contactName: yup.string().required(),
    businessName: yup.string().required(),
    multipleSites: yup.object().required(),
    email: yup.string().email().required(),
    phone: yup.string().required()
  })
}

const currentValues = computed(() => {
  return {
    address: address.value,
    businessName: businessName.value,
    contactName: contactName.value,
    email: email.value,
    existingCustomer: existingCustomer.value,
    marketingOi: marketingOi.value,
    materials: materials.value,
    multipleSites: multipleSites.value,
    phone: phone.value,
    privacyOi: privacyOi.value
  }
})

const currentSchema = computed(() => {
  if (currentStep.value === 0 && existingCustomer.value.value.toLowerCase() == 'no') {
    return schemas.alt0 // We need different validation on the same step for new customers
  } else {
    if (currentStep.value === 2 && existingCustomer.value.value.toLowerCase() == 'no') {
      //resetForm(currentValues.value)
      return schemas.alt2 // We need different validation on the same step for new customers
    } else {
      return schemas[currentStep.value]
    }
  }
})

const removeMaterial = function (matKey) {
  materials.value.splice(
    materials.value.findIndex((mat) => mat.matKey === matKey),
    1
  )
}

const updateMaterialQty = function (matKey, qty) {
  materials.value.map((mat) => {
    if (mat.matKey === matKey) mat.qty = parseInt(qty, 10)
  })
}

const { validate } = useForm({
  initialValues: currentValues.value,
  validationSchema: currentSchema
})

async function nextStep(values) {
  const { valid } = await validate()
  if (valid) {
    if (currentStep.value === stepLength.value) {
      console.log('Done: ', JSON.stringify(values, null, 2))
      return
    }

    currentStep.value++
    formWizard.value?.nextTab()
  }
}

function prevStep() {
  if (currentStep.value <= 0) {
    return
  }

  currentStep.value--
  formWizard.value?.prevTab()
}

function submitForm() {
  calculateBasket()
  postForm(url, fields, { ...currentValues.value, ...pricingCalcs.value }).then((data) => {
    console.log(data)
  })
}

function calculateTransport(volume) {
  return Math.min(
    ...pricePlans.map((plan) => {
      return Math.max(plan.litreRate * volume, plan.minimumCharge)
    })
  )
}

function calculateBasket() {
  const calculations = {
    totalPrice: 0,
    totalTransportVolume: 0,
    totalRebate: 0,
    hazardous: 0,
    totalTransportCharge: 0
  }

  materials.value.forEach((material) => {
    if (material.quantity === 0) return

    calculations.totalPrice += material.qty * material.price
    calculations.totalTransportVolume += material.qty * material.transportVolume
    calculations.totalRebate += material.qty * material.rebate
    calculations.hazardous = calculations.hazardous | material.hazardous
  })

  calculations.totalTransportCharge = calculateTransport(calculations.totalTransportVolume)

  pricingCalcs.value.eaFee = calculations.hazardous * eaFee
  pricingCalcs.value.rebate = calculations.totalRebate
  pricingCalcs.value.transportCharge = calculations.totalTransportCharge

  let totalCharge =
    calculations.totalPrice -
    calculations.totalRebate +
    calculations.hazardous * eaFee +
    calculations.totalTransportCharge

  pricingCalcs.value.totalCharge = totalCharge
}

watch(materials, calculateBasket, { deep: true })
</script>

<template>
  <div class="mx-auto sm:px-6 lg:px-8">
    <form @submit="nextStep" class="custom-form">
      <FormWizard hide-buttons ref="formWizard" disable-back>
        <TabContent>
          <div class="bg-white relative">
            <main
              class="relative mx-auto grid max-w-7xl grid-cols-1 gap-x-16 lg:grid-cols-2 lg:px-8 xl:gap-x-48"
            >
              <section
                class="bg-gray-50 px-4 pb-10 pt-16 sm:px-6 lg:col-start-2 lg:row-start-1 lg:bg-transparent lg:px-0 lg:pb-16"
              >
                <div class="mx-auto max-w-lg lg:max-w-none"></div>
              </section>
              <div class="px-4 pb-36 pt-16 sm:px-6 lg:col-start-1 lg:row-start-1 lg:px-0 lg:pb-16">
                <div class="mx-auto max-w-lg lg:max-w-none">
                  <section aria-labelledby="contact-info-heading">
                    <h2 id="contact-info-heading" class="text-lg font-medium text-gray-900">
                      Form Information
                    </h2>

                    <div class="mt-6">
                      <FormInputRadio
                        v-model="existingCustomer"
                        name="existingCustomer"
                        :options="fields.existingCustomer.choices"
                        :label="fields.existingCustomer.label"
                        :required="true"
                      />
                      <template v-if="existingCustomer.value.toLowerCase() == 'yes'">
                        <FormInputText
                          v-model="contactName"
                          name="contactName"
                          :label="fields.contactName.label"
                          :required="true"
                        />
                        <FormInputText
                          v-model="businessName"
                          name="businessName"
                          :label="fields.businessName.label"
                          :required="true"
                        />
                        <FormInputText
                          v-model="address"
                          name="address"
                          :label="fields.address.label"
                          :required="true"
                        />
                      </template>
                      <template v-else>
                        <Field
                          name="selectedIndustry"
                          v-slot="{ field, errorMessage }"
                          v-model="selectedIndustry"
                        >
                          <ComboBoxList
                            :options="industries"
                            v-bind="field"
                            key-prop="industryKey"
                            label-prop="industryName"
                            :label="fields.industryType.label"
                            :helper="fields.industryType.description"
                          />
                          <span class="text-red-500">{{ errorMessage }}</span>
                        </Field>

                        <FormInputRadio
                          v-model="multipleSites"
                          name="multipleSites"
                          :options="fields.multipleSites.choices"
                          label="sites"
                          :required="true"
                        />
                        <FormInputAccept
                          name="privacy"
                          v-model="privacyOi"
                          :checked-value="privacyOi"
                          :label="fields.privacyOi.label"
                          :message="fields.privacyOi.description"
                        />
                        <div class="sm:col-span-6 relative flex items-start"></div>
                      </template>
                    </div>
                  </section>
                </div>
              </div>
            </main>
          </div>
        </TabContent>
        <TabContent>
          <MaterialPicker v-model="materials" @prev="prevStep" @next="nextStep"></MaterialPicker>
        </TabContent>
        <TabContent>
          <div class="bg-white relative">
            <!-- Background color split screen for large screens -->
            <div class="fixed left-0 top-0 hidden w-1/2 bg-white lg:block" aria-hidden="true" />
            <div class="fixed right-0 top-0 hidden w-1/2 bg-gray-50 lg:block" aria-hidden="true" />
            <main
              class="relative mx-auto grid max-w-7xl grid-cols-1 gap-x-16 lg:grid-cols-2 lg:px-8 xl:gap-x-48"
            >
              <section
                aria-labelledby="summary-heading"
                class="bg-gray-50 px-4 pb-10 pt-16 sm:px-6 lg:col-start-2 lg:row-start-1 lg:bg-transparent lg:px-0 lg:pb-16"
              >
                <div class="mx-auto max-w-lg lg:max-w-none">
                  <h2 id="summary-heading" class="text-lg font-medium text-gray-900">
                    Item Summary
                  </h2>

                  <ul
                    role="list"
                    class="divide-y divide-gray-200 text-sm font-medium text-gray-900"
                  >
                    <MaterialSummaryItem
                      v-for="material in materials"
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
                  </ul>
                </div>
              </section>
              <div class="px-4 pb-36 pt-16 sm:px-6 lg:col-start-1 lg:row-start-1 lg:px-0 lg:pb-16">
                <div class="mx-auto max-w-lg lg:max-w-none">
                  <section aria-labelledby="contact-info-heading">
                    <h2 id="contact-info-heading" class="text-lg font-medium text-gray-900">
                      Contact information
                    </h2>

                    <div class="mt-6">
                      <div v-if="currentStep === 2">
                        <template v-if="existingCustomer.value == 'Yes'">
                          <!-- Existing Customer -->
                          <FormInputText
                            v-model="contactName"
                            name="contactName"
                            :label="fields.contactName.label"
                            :required="true"
                          />
                          <FormInputText
                            v-model="businessName"
                            name="businessName"
                            :label="fields.businessName.label"
                            :required="true"
                          />
                          <FormInputText
                            v-model="email"
                            name="email"
                            :label="fields.email.label"
                            :placeholder="fields.email.placeholder"
                            :required="fields.email.required"
                            :type="fields.email.type"
                          />
                          <FormInputText
                            v-model="phone"
                            name="phone"
                            :label="fields.phone.label"
                            :placeholder="fields.phone.placeholder"
                            :required="fields.phone.required"
                          />
                          <FormInputText
                            v-model="address"
                            name="address"
                            :label="fields.address.label"
                            :required="true"
                          />
                          <FormInputRadio
                            v-model="multipleSites"
                            name="multipleSites"
                            :options="fields.multipleSites.choices"
                            label="sites"
                            :required="true"
                          />

                          <FormInputAccept
                            name="marketing"
                            v-model="marketingOi"
                            :checked-value="marketingOi"
                            :label="fields.marketingOi.label"
                            :message="fields.marketingOi.description"
                          />
                        </template>
                        <template v-else>
                          <!-- New Customer -->
                          <FormInputText
                            v-model="address"
                            name="address"
                            :label="fields.address.label"
                            :required="true"
                          />

                          <FormInputText
                            v-model="businessName"
                            name="businessName"
                            :label="fields.businessName.label"
                            :required="true"
                          />
                          <FormInputText
                            v-model="contactName"
                            name="contactName"
                            :label="fields.contactName.label"
                            :required="true"
                          />
                          <FormInputText
                            v-model="phone"
                            name="phone"
                            :label="fields.phone.label"
                            :placeholder="fields.phone.placeholder"
                            :required="fields.phone.required"
                          />
                          <FormInputText
                            v-model="email"
                            name="email"
                            :label="fields.email.label"
                            :placeholder="fields.email.placeholder"
                            :required="fields.email.required"
                            :type="fields.email.type"
                          />
                          <FormInputAccept
                            name="marketing"
                            v-model="marketingOi"
                            :checked-value="marketingOi"
                            :label="fields.marketingOi.label"
                            :message="fields.marketingOi.description"
                          />
                          <FormInputAccept
                            name="privacy"
                            v-model="privacyOi"
                            :checked-value="privacyOi"
                            :label="fields.privacyOi.label"
                            :message="fields.privacyOi.description"
                          />
                        </template>
                      </div>
                    </div>
                  </section>
                </div>
              </div>
            </main>
          </div>
        </TabContent>

        <TabContent>
          <div class="bg-white relative">
            <main
              class="relative mx-auto grid max-w-7xl grid-cols-1 gap-x-16 lg:grid-cols-2 lg:px-8 xl:gap-x-48"
            >
              <section
                class="bg-gray-50 px-4 pb-10 pt-16 sm:px-6 lg:col-start-2 lg:row-start-1 lg:bg-transparent lg:px-0 lg:pb-16"
              >
                <div class="mx-auto max-w-lg lg:max-w-none"></div>
              </section>
              <div class="px-4 pb-36 pt-16 sm:px-6 lg:col-start-1 lg:row-start-1 lg:px-0 lg:pb-16">
                <div class="mx-auto max-w-lg lg:max-w-none">
                  <section aria-labelledby="contact-info-heading">
                    <h2 id="contact-info-heading" class="text-lg font-medium text-gray-900">
                      Your Estimate
                    </h2>

                    <div class="mt-6">
                      <ul
                        role="list"
                        class="divide-y divide-gray-200 text-sm font-medium text-gray-900"
                      >
                        <MaterialSummaryItem
                          v-for="material in materials"
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
                      </ul>
                      <dl class="space-y-6 border-t border-gray-200 px-4 py-6 sm:px-6">
                        <div class="flex items-center justify-between">
                          <dt class="text-sm">Recycling Costs</dt>
                          <dd class="text-sm font-medium text-gray-900">
                            {{
                              (
                                pricingCalcs.totalCharge +
                                pricingCalcs.rebate -
                                pricingCalcs.eaFee -
                                pricingCalcs.transportCharge
                              ).toLocaleString('en-US', { style: 'currency', currency: 'GBP' })
                            }}
                          </dd>
                        </div>
                        <div class="flex items-center justify-between">
                          <dt class="text-sm">Rebate</dt>
                          <dd class="text-sm font-medium text-gray-900">
                            {{
                              pricingCalcs.rebate.toLocaleString('en-US', {
                                style: 'currency',
                                currency: 'GBP'
                              })
                            }}
                          </dd>
                        </div>
                        <div class="flex items-center justify-between">
                          <dt class="text-sm">Transport</dt>
                          <dd class="text-sm font-medium text-gray-900">
                            {{
                              pricingCalcs.transportCharge.toLocaleString('en-US', {
                                style: 'currency',
                                currency: 'GBP'
                              })
                            }}
                          </dd>
                        </div>
                        <div class="flex items-center justify-between">
                          <dt class="text-sm">EA Fee</dt>
                          <dd class="text-sm font-medium text-gray-900">
                            {{
                              pricingCalcs.eaFee.toLocaleString('en-US', {
                                style: 'currency',
                                currency: 'GBP'
                              })
                            }}
                          </dd>
                        </div>
                        <div
                          class="flex items-center justify-between border-t border-gray-200 pt-6"
                        >
                          <dt class="text-sm">Subtotal</dt>
                          <dd class="text-sm font-medium text-gray-900">
                            {{
                              pricingCalcs.totalCharge.toLocaleString('en-US', {
                                style: 'currency',
                                currency: 'GBP'
                              })
                            }}
                          </dd>
                        </div>
                        <div class="flex items-center justify-between">
                          <dt class="text-sm">VAT</dt>
                          <dd class="text-sm font-medium text-gray-900">
                            {{
                              (pricingCalcs.totalCharge * 0.2).toLocaleString('en-US', {
                                style: 'currency',
                                currency: 'GBP'
                              })
                            }}
                          </dd>
                        </div>

                        <div
                          class="flex items-center justify-between border-t border-gray-200 pt-6"
                        >
                          <dt class="text-base font-medium">Total</dt>
                          <dd class="text-base font-medium text-gray-900">
                            {{
                              (pricingCalcs.totalCharge * 1.2).toLocaleString('en-US', {
                                style: 'currency',
                                currency: 'GBP'
                              })
                            }}
                          </dd>
                        </div>
                      </dl>
                    </div>
                  </section>
                </div>
              </div>
            </main>
          </div>
        </TabContent>

        <button v-if="currentStep !== 0" type="button" @click="prevStep">Previous</button>
        <Button v-if="currentStep !== stepLength" type="button" @click="nextStep">Next</Button>

        <button v-if="currentStep === stepLength" type="submit">Finish</button>
        <Button @click="submitForm" type="button">SUBMIT</Button>
      </FormWizard>
    </form>
  </div>
</template>
<style scoped>
.entry-content
  > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(
    .woocommerce
  ) {
  width: 100%;
  max-width: 100%;
}
</style>
