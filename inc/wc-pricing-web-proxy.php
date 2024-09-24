<?php

class WcAPIService {

    private $options;

    function __construct() {
        $this->options = get_option('wc_pricing_plugin_settings');
    }

    private function api_key() {
        return $this->options['wastecare_api_key'];
    }

    private function api_url() {
        return $this->options['wastecare_api_url'];
    }

    public function get_initial_data()
    {
        return $this->cached_api_call('quoteengine/GetInitialData', 'wc_pricer_initial_data');
    }

    public function get_materials() 
    {
        return $this->cached_api_call('quoteengine/GetMaterials', 'wc_pricer_all_materials');
    }

    /* Ajax call, caching tbc */
    public function get_materials_by_industry($args) {

        check_ajax_referer( 'wc_pricer' );

        $industry = $_GET['industry'];

        $response = wp_remote_get($this->api_url() . 'quoteengine/getmaterialsbyindustrykey' . $industry, 
            ['headers' => $this->api_headers()]
        );
        
        $status = wp_remote_retrieve_response_code( $response );

        if( is_wp_error( $response ) || $status != "200" ) {
            status_header($status);
            exit();
            wp_die();
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body );

        exit ( json_encode( $data ) );
        wp_die(); // all ajax handlers should die when finished
    }

/* helpers */

    private function cached_api_call($url, $cache_key) 
    {
        global $wpdb;

        $data = get_transient($cache_key);

        if($data === false) {
            $response = wp_remote_get($this->api_url() . $url, ['headers' => $this->api_headers()]);
            $status = wp_remote_retrieve_response_code( $response );
     
            if( is_wp_error( $response ) || $status != "200" ) {
                return "Error";
            }
    
            $body = wp_remote_retrieve_body( $response );
            $data = json_decode( $body );
            set_transient($cache_key, $data, 3600 * 24);          
        }

        return $data;
    }

    private function api_headers() {
        return [
            'Content-Type' => 'application/json',
            'x-ApiKey' => $this->api_key()
        ];
    }
}