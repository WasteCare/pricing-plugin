import './assets/main.css'
import 'vue3-form-wizard/dist/style.css'

import { createApp } from 'vue'
import { FormWizard, TabContent } from 'vue3-form-wizard'

import App from './App.vue'

document.addEventListener(
  'DOMContentLoaded',
  (function (globals, window, undefined) {
    createApp(App)
      .component('FormWizard', FormWizard)
      .component('TabContent', TabContent)
      .provide('ajaxUrl', globals.ajaxUrl)
      .provide('formUrl', `/wp-json/gf/v2/forms/${globals.form.contactName.form}/submissions`)
      .provide('nonce', globals.nonce)
      .provide('ajaxAction', globals.action)
      .provide('formFields', globals.form)
      .provide('pricePlans', globals.initial_data.pricePlans)
      .provide('eaFee', globals.initial_data.eaFee )
      .provide('industryTypes', globals.initial_data.industries)
      .provide('materials', globals.materials)
      .mount('#wc-app')
  })(wc_ajax_obj, window)
)
