<?php

class WcSettingsPersistence
{
    public function add_settings()
    {
        register_setting(
            "wc_pricing_plugin_settings",
            "wc_pricing_plugin_settings",
            [$this, "wc_pricer_validate_settings"]
        );

        $this->gform_settings();

        add_settings_section(
          "wastecare_settings",
          "WasteCare Settings",
          [$this, "wastecare_settings_text"],
          "wc_pricing_plugin_settings"
      );


        add_settings_field(
            "wastecare_api_url",
            "WasteCare API Url",
            [$this, "wc_pricing_render_wastecare_api_url_field"],
            "wc_pricing_plugin_settings",
            "wastecare_settings"
        );

        add_settings_field(
            "wastecare_api_key",
            "WasteCare API Key",
            [$this, "wc_pricing_render_wastecare_api_key_field"],
            "wc_pricing_plugin_settings",
            "wastecare_settings"
        );
    }

    public function gform_settings()
    {
      add_settings_section(
        "wastecare_gform_settings",
        "Gravity Forms Settings",
        [$this, "gform_settings_text"],
        "wc_pricing_plugin_settings"
      );


      $gform_settings_name = "wastecare_gform_settings";

      add_settings_field(
        "gravity_form",
        "Gravity form for Pricer",
        [$this, "wc_pricer_render_gf_field"],
        "wc_pricing_plugin_settings",
        "wastecare_gform_settings"
      );

      $this->add_gf_list_field("gf_name_field", "Name", $gform_settings_name);
      $this->add_gf_list_field("gf_business_address_field", "Business Address", $gform_settings_name); 
      $this->add_gf_list_field("gf_business_name_field", "Business Name", $gform_settings_name);
      $this->add_gf_list_field("gf_industry_type_field", "Industry Type", $gform_settings_name);
      //$this->add_gf_list_field("gf_material_lines_field", "Materials", $gform_settings_name);
      $this->add_gf_list_field("gf_email_address_field", "Email Address", $gform_settings_name);
      $this->add_gf_list_field("gf_phone_field", "Phone", $gform_settings_name); 
      $this->add_gf_list_field("gf_account_number_field", "Account Number", $gform_settings_name);
      $this->add_gf_list_field("gf_multiple_sites_field", "Collections from multiple Sites", $gform_settings_name);
      $this->add_gf_list_field("gf_marketing_opt_in_field", "Marketing opt-in", $gform_settings_name);
      $this->add_gf_list_field("gf_privacy_confirmation_field", "Privacy Confirmation", $gform_settings_name);
      $this->add_gf_list_field("gf_transport_rate_field", "Transport Charge", $gform_settings_name);
      $this->add_gf_list_field("gf_ea_fee_field", "EA Fee", $gform_settings_name);
      $this->add_gf_list_field("gf_rebate_field", "Rebate Amount", $gform_settings_name);
      $this->add_gf_list_field("gf_total_charge_field", "Total Charge", $gform_settings_name);
      $this->add_gf_list_field("gf_existing_customer_field", "Existing Customer", $gform_settings_name);
      $this->add_gf_list_field("gf_materials_field", "Materials", $gform_settings_name);

    }

    function gform_settings_text()
    {
        echo "<p>Settings relating to the gravity form. This works by matching fields to an existing gravity form - you should map the fields here</p>";
    }
    
    function wastecare_settings_text()
    {
        echo "<p>Settings relating to WasteCare</p>";
    }


    function google_map_settings_text()
    {
        echo "<p>Settings relating to Google</p>";
    }

    function wc_pricer_render_gf_field()
    {
        $options = get_option("wc_pricing_plugin_settings");
        $form_options = GFAPI::get_forms();
        ?>
    <select name='wc_pricing_plugin_settings[gravity_form]'>
        <?php foreach ($form_options as $i => $form):
            echo '<option value="' .
                $form["id"] .
                '"' .
                selected($options["gravity_form"], $form["id"]) .
                ">" .
                $form["title"] .
                "</option>"; //close your tags!!
        endforeach; ?>
    </select>
    <?php
    }

    function wc_pricing_render_wastecare_api_url_field($args)
    {
        $options = get_option("wc_pricing_plugin_settings");
        printf(
            '<input type="text" name="%s" value="%s" />',
            esc_attr("wc_pricing_plugin_settings[wastecare_api_url]"),
            esc_attr($options["wastecare_api_url"])
        );
    }

    function wc_pricing_render_wastecare_api_key_field()
    {
        $options = get_option("wc_pricing_plugin_settings");
        printf(
            '<input type="text" name="%s" value="%s" />',
            esc_attr("wc_pricing_plugin_settings[wastecare_api_key]"),
            esc_attr($options["wastecare_api_key"])
        );
    }


    function add_gf_list_field($name, $title, $section) 
    {
        add_settings_field(
            $name,
            $title,
            [$this, "generate_options_for_gf_field"],
            "wc_pricing_plugin_settings",
            $section,
            [$name]
        );
    }

    function generate_options_for_gf_field($field_name)
    {
        $options = get_option("wc_pricing_plugin_settings");
        $form_id = $options["gravity_form"];
        $form = GFAPI::get_form($form_id);
        $this->generate_select_for_form(
            $form,
            $field_name[0],
            rgar($options, $field_name[0])
        );
    }

    public function generate_select_for_form($form, $key, $selected_item)
    {
        echo '<select name="wc_pricing_plugin_settings[' . $key . "]>";
        $fields = $form["fields"];
        foreach ($fields as $i => $field) {
            echo '<option value="' .
                $field["id"] .
                '"' .
                selected($selected_item, $field["id"]) .
                ">" .
                $field["label"] .
                "</option>";
        }
        echo "</select>";
    }

    function wc_pricer_validate_settings($input)
    {
        
        $output["wastecare_api_url"] = sanitize_text_field($input["wastecare_api_url"]);
        $output["wastecare_api_key"] = sanitize_text_field($input["wastecare_api_key"]);
        $output["gravity_form"] = sanitize_text_field($input["gravity_form"]);

        /* gravity forms mapping */ 

        $output["gf_name_field"] = sanitize_text_field($input["gf_name_field"]);
        $output["gf_business_name_field"] = sanitize_text_field($input["gf_business_name_field"]);
        $output["gf_business_address_field"] = sanitize_text_field($input["gf_business_address_field"]);
        $output["gf_material_lines_field"] = sanitize_text_field($input["gf_material_lines_field"]);
        $output['gf_material_lines_field'] = sanitize_text_field($input['gf_materials_field']);
        $output['gf_email_address_field'] = sanitize_text_field($input['gf_email_address_field']);
        $output['gf_phone_field'] = sanitize_text_field($input['gf_phone_field']);
        $output['gf_account_number_field'] = sanitize_text_field($input['gf_account_number_field']);
        $output['gf_multiple_sites_field'] = sanitize_text_field($input['gf_multiple_site_field']);
        $output['gf_marketing_opt_in_field'] = sanitize_text_field($input['gf_marketing_opt_in_field']);
        $output['gf_privacy_confirmation_field'] = sanitize_text_field($input['gf_privacy_confirmation_field']);
        $output['gf_transport_rate_field'] = sanitize_text_field($input['gf_transport_rate_field']);
        $output['gf_ea_fee_field'] = sanitize_text_field($input['gf_ea_fee_field']);
        $output['gf_rebate_field'] = sanitize_text_field($input['gf_rebate_field']);
        $output['gf_total_charge_field'] = sanitize_text_field($input['gf_total_charge_field']);
        $output['gf_industry_type_field'] = sanitize_text_field($input['gf_industry_type_field']);

        return $input;
    }
}
