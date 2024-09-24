<?php

class WcPricingDeactivate
{
  public static function deactivate() {
    flush_rewrite_rules();
  }
}
