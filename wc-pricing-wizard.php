<?php
/**
 * @wordpress-plugin
 * Plugin Name:       WasteCare Pricing Calculator
 * Plugin URI:        https://www.waste.care
 * Description:       Pricing Wizard for Wastecare Website
 * Version:           1.0.1
 * Author:            WasteCare
 * Author URI:        https://www.waste.care
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       vwp-plugin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}



require_once plugin_dir_path(__FILE__) . 'inc/wc-settings-persistence.php';
require_once plugin_dir_path(__FILE__) . 'inc/wc-pricing-web-proxy.php';
require_once plugin_dir_path(__FILE__) . 'inc/wc-forms-integration-handler.php';

class WcPricingWizard
{
  public $plugin;
  public $settings;
  public $forms_handler;

  function __construct() {
    $this->plugin = plugin_basename(__FILE__);
    $this->settings = new WcSettingsPersistence();
    $this->forms_handler = new WcFormsIntegrationHandler();
    $this->webservice = new WcAPIService();
  }

  function register() {

    add_action('admin_menu', array($this, 'add_admin_page'));
    add_action('admin_init', array($this->settings, 'add_settings'));
    add_shortcode('wc_pricing_plugin', array($this, 'render_pricing_wizard'));
    add_filter("plugin_action_links_$this->plugin", array($this, 'settings_link'));
   //add_action('wp_enqueue_scripts', array($this, 'wc_pricing_script_object'));
  }


  public function settings_link( $links ) {
    $settings_link = '<a href="admin.php?page=wc_pricing_plugin_settings">Settings</a>';
    array_push($links, $settings_link);
    return $links;
  }

  public function render_pricing_wizard() {
    wp_enqueue_style("wc-pricing-app", plugins_url('/dist/assets/index-2jA94izr.css', __FILE__), '1.0.2');
    wp_enqueue_script_module( "wc-pricing-app", plugins_url('/dist/assets/index-DL5ZbnIA.js',__FILE__), [], '1.0.2' );
    $options = get_option('wc_pricing_plugin_settings');

    $script = '<script>wc_ajax_obj =';
    $script .= json_encode(
      array( 
        'form' => $this->forms_handler->get_form_settings(),
        'initial_data' => $this->webservice->get_initial_data(),
        'materials' => $this->webservice->get_materials()
      )
      );
    $script .= '</script>';


    return $script.'<div id="wc-app"></div>';
  }

  public function add_admin_page() {
    add_options_page('Pricing Wizard', 'Pricing Wizard', 'manage_options', 'wc_pricing_plugin_settings', array($this, 'admin_index'), 10);
  }

  public function admin_index() {
    require_once plugin_dir_path(__FILE__) . 'templates/admin/index.php';
  }

}

if ( class_exists('WcPricingWizard') ) {
  $wcPlugin = new  WcPricingWizard();
  $wcPlugin->register();
}

// Activation
require_once plugin_dir_path(__FILE__)  . 'inc/wc-pricing-activate.php';
register_activation_hook( __FILE__, array('WcPricingActivate', 'activate' ) );

// Deactivation
require_once plugin_dir_path(__FILE__)  . 'inc/wc-pricing-deactivate.php';
register_deactivation_hook( __FILE__, array( 'WcPrcingDeactivate', 'deactivate' ) );
