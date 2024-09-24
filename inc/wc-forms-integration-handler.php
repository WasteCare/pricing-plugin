<?php

class WcFormsIntegrationHandler {

    public function get_form_settings() {
        
        $options = get_option('wc_pricing_plugin_settings');
        $output = [];
        $form = GFAPI::get_form( $options['gravity_form'] );
        $fields = $form['fields'];
        
        $plugin_fields = [
            $options['gf_name_field'] => 'contactName',
            $options['gf_business_name_field'] => 'businessName',
            $options['gf_business_address_field'] => 'address',
            $options['gf_email_address_field'] => 'email',
            $options['gf_phone_field'] => 'phone',
            $options['gf_account_number_field'] => 'accountNumber',
            $options['gf_multiple_sites_field'] => 'multipleSites',
            $options['gf_marketing_opt_in_field'] => 'marketingOi',
            $options['gf_privacy_confirmation_field'] => 'privacyOi',
            $options['gf_transport_rate_field'] => 'transportRate',
            $options['gf_ea_fee_field'] => 'eaFee',
            $options['gf_rebate_field'] => 'rebate',
            $options['gf_total_charge_field'] => 'totalCharge',
            $options['gf_industry_type_field'] => 'industryType',
            $options['gf_existing_customer_field'] => 'existingCustomer',
            $options['gf_materials_field'] => 'materials'

        ];

        foreach($fields as $i => $field)
        {
            if(array_key_exists( $field['id'], $plugin_fields )) {
                $output[$plugin_fields[$field['id']]] = [
                    'id' => $field['id'],
                    'form' => $field['formId'],
                    'label' => $field['label'],
                    'description' => $field['description'],
                    'required' => $field['isRequired'],
                    'type' => $field['type'],
                    'inputType' => $field['inputType'],
                    'placeholder' => $field['placeholder'],
                    'autocompleteAttribute' => $field['autocoompleteAttribure'],
                    'choices' => $field['choices'],
                    'subFields' => $field['fields'],
                    'checkboxLabel' => $field['checkboxLabel']
                ];
               // $field['client_map'] = $plugin_fields[$field['id']];
            };
        }

        return $output; // 
        //return $form['fields'];

    }
}