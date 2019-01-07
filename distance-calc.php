<?php
/**
 * Plugin Name:     Distance Calculator
  * Description:    Calculates the distance between two locations
 * Author:          Clive Dev
 * Author URI:      YOUR SITE HERE
 * Text Domain:     distance-calc
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Distance_Matrix
 */


require 'blocks/distance.php';
require 'cost-per-mile-admin-page.php';

// adding settings page
$id="cost-per-mile";


add_action('admin_menu', function(){
	$settings = new dc_cost_per_mile_admin_page();
	add_submenu_page( 'options-general.php', 'Distance Calculator Settings', 'Distance Calculator', 'manage_options', 'dc-settings', [$settings,'display'] );
});