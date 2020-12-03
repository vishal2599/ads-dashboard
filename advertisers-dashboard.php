<?php

/**
 * @package AdvertisersDashboard
 *
 */
/*
Plugin Name: Advertisers Dashboard
Description: This is an Advertising Management Plugin
Version: 1.0.0
Author: ShotgunFlat
License: GPLv2 or later
Text Domain: advertisers-dashboard
 */

// If this file is called directly, Abort!!
defined('ABSPATH') or die('Hey, what are you doing here? You silly human!');

// Require Once the Composer Autoload
if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

// Method that runs on Plugin Activation
function activate_advertisers_dashboard()
{
    AdvDashboard\Base\Activate::activate();
}
register_activation_hook( __FILE__ , 'activate_advertisers_dashboard' );


// Method that runs on Plugin Deactivation
function deactivate_advertisers_dashboard()
{
    AdvDashboard\Base\Deactivate::deactivate();
}
register_deactivation_hook( __FILE__ , 'deactivate_advertisers_dashboard' );


if( class_exists( "AdvDashboard\\Init" ) ){
    AdvDashboard\Init::register_services();
}