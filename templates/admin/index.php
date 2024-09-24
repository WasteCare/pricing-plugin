<h2>WasteCare Pric Settings</h2>
  <form action="options.php" method="post">
    <?php 
    settings_fields( 'wc_pricing_plugin_settings' );
    do_settings_sections( 'wc_pricing_plugin_settings' );
    ?>
    <input
      type="submit"
      name="submit"
      class="button button-primary"
      value="<?php esc_attr_e( 'Save' ); ?>"
    />
  </form>