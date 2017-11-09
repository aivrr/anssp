<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * and defines a function that starts the plugin.
 *
 * Plugin Name:       Another Social Share Plugin
 * Description:       Easily add social share bar to your posts and pages.
 * Version:           1.0.0
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}


            // ************ AUTOLOADER ************

require 'vendor/autoload.php';


use Baerr\Anssp\App;
use Baerr\Anssp\PluginCore;

            // ************ ERROR HANDLER FOR DEBUGGING ************

// PHP error handler
// Catches any PHP error and logs it to file
set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
    // exit if not local environment 
    if (!App::is_local_env()) {
        return false;
    }

    error_log('*** ERROR HANDLER ***');
    error_log($errno);
    error_log($errstr);
    error_log($errfile);
    error_log($errline);

    error_log((new Exception)->getTraceAsString());

    // allow PHP to run its own handler
    return false;
});

            // ************ BASE CLASS ************

if ( !class_exists('Another_Social_Share_Plugin') ) {

    class Another_Social_Share_Plugin 
    {
        
        // Constructor
        public function __construct() {
            // nothing here
        }

        // Get the URL directory path (with trailing slash) 
        public static function base_url() {
            return plugin_dir_url( __FILE__ );
        }

        // Get the filesystem directory path (with trailing slash) 
        public static function base_file_path() {
            return plugin_dir_path( __FILE__ );
        }

        // Get the plugin basename  
        public static function base_plugin_name() {
            return plugin_basename( __FILE__ );
        }

    } 

} 



/**
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 */
function anssplugin() {
    return PluginCore::instance();
}


// Get Plugin Running.
add_action( 'plugins_loaded', 'anssplugin', 999 );
