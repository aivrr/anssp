<?php

/**
 * Plugin Core Module
 * Loads other modules and general purpose hooks
 *
 */

namespace Baerr\Anssp;

use Baerr\Anssp\Controllers\SettingsManager;
use Baerr\Anssp\Models\ShareBar;
use Baerr\Anssp\Controllers\ShortcodeManager;
use Baerr\Anssp\Models\ShareBarOptions;
use Baerr\Anssp\Test\TestCpt;


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}


class PluginCore 
{
    /**
     * the only singleton instance
     * @var self
     */
    private static $instance;

    /**
     * options for share bar
     * @var ShareBarOptions
     */
    private $share_options;

    /**
     * outputs share bar itself
     * @var ShareBar
     */
    private $share_bar;

    /**
     *  manages shortcodes
     * @var ShortcodeManager
     */
    private $shortcode_manager;

    /**
     * manages settings in admin backend
     * @var SettingsManager
     */
    private $settings_manager;


    // *** BASE ***

    // Singleton Constructor
    private function __construct() {
        $this->register_hooks();

        // load other modules
        $options = new ShareBarOptions;
        $this->share_options = $options;
        $share_bar = new ShareBar($options);
        $this->share_bar = $share_bar;
        $this->shortcode_manager = new ShortcodeManager($share_bar->provider());
        $settings_manager = new SettingsManager($options, $share_bar->provider());
        $this->settings_manager = $settings_manager;
    }

    /**
     * Displays notice if PHP version is wrong
     * @param  string $name       Plugin name
     * @param  string $php_needed version needed
     * @param  string $version    current version
     * @return Closure             function to output html
     */
    private static function notice_php_version($name, $php_needed, $version)
    {
        return function() use ($name, $php_needed, $version) {
            echo '<div class="error notice"><p>' 
            . "$name requires PHP version $php_needed to function properly while you're running $version. Please upgrade your PHP version. Meanwhile this plugin has been auto-deactivated."
            . '</p></div>';

            if ( isset($_GET['activate']) ) { 
                unset($_GET['activate']);
            }
        };
    }

    // Init plugin instance
    // Check PHP version 
    private static function init_instance() {
        if ( !function_exists('deactivate_plugins') ) {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }

        $name = App::config('name');
        $php_needed = App::config('dependency.php_version');
        $version = PHP_VERSION;

        // check for PHP version
        // if wrong deactivate and return
        if (version_compare($version, $php_needed, '<')) {
            add_action( 'admin_notices', self::notice_php_version($name, $php_needed, $version) );
            deactivate_plugins( App::base_plugin_name() );

            return;
        } 

        self::$instance = new self;
    }

    /**
     * Main Instance.
     *
     * Insures that only one instance of plugin exists at any time
     */
    public static function instance() {
        if ( !(self::$instance instanceof self) ) {
            self::init_instance();
        }

        return self::$instance;
    }


    // *** HOOKS ***
    // =======================================================================

    // Enqueues CSS and JS files
    public function enqueue_files() {
        $this->frontpage_scripts();
    }

    // Enqueue JS and CSS files only on specific admin page
    public function admin_enqueue_files($admin_uri) {
        $slug = $this->settings_manager->slug();
        // Loads only on own admin page
        if (empty($slug) || $slug !== $admin_uri) {
            return;
        }

        $this->admin_scripts();
    }

    // saves new version to wp options after other hooks fired
    // usually used to flush rewrite rules 
    public function update_plugin_version() {
        // Save new version after all hooks fired
        $plugin_ver = get_option(App::config('wp_option.version'));
        if ( empty($plugin_ver) || version_compare($plugin_ver, App::config('version')) !== 0 ) {
            update_option(App::config('wp_option.version'), App::config('version'));
        }
    }


    // *** PUBLIC METHODS ***
    // =======================================================================

    /**
     * Checks if current post type has share bar enabled in options
     * @return boolean   whether share bar should be displayed
     */
    public function has_share_bar() {
        $allowed = $this->share_options->post_types();
        
        // no post type allowed
        if (empty($allowed)) {
            return false;
        }

        // get current post type
        $post_type = get_post_type();

        // no post type
        if ($post_type === false) {
            return false;
        }

        return in_array($post_type, $allowed, true);
    }

    // *** PRIVATE METHODS ***
    // =======================================================================


    // All Wordpress hooks 
    private function register_hooks() {

        // save new version when it changes
        add_filter( 'wp_loaded', [$this, 'update_plugin_version'], 999 );

        // load css and js scripts
        add_action( 'wp_enqueue_scripts', [$this, 'enqueue_files'], 999 );

        // load css and js scripts for admin pages
        add_action( 'admin_enqueue_scripts', [$this, 'admin_enqueue_files'] );

    }

    /**
     * Scripts for a front page
     */
    private function frontpage_scripts() {
        // hash for cache busting
        $hash = App::enqueue_hash();

        // Custom CSS
        wp_enqueue_style( 
            'anssp-frontpage',
            App::base_url() . 'assets/css/frontpage.css',
            [], 
            $hash 
        );
    }

    /**
     * admin side scripts
     * loads only on plugin's settings page
     */
    private function admin_scripts() {
        // hash for cache busting
        $hash = App::enqueue_hash();

        // WordPress color picker
        wp_enqueue_style('wp-color-picker');

        // Dragula JS
        wp_enqueue_script( 
            'anssp-dragula',
            App::base_url() . 'assets/vendor/dragula.min.js', 
            [],
            $hash,
            true
        );

        // Custom JS
        wp_enqueue_script( 
            'anssp-adminpage',
            App::base_url() . 'assets/js/adminpage.js', 
            ['jquery', 'wp-color-picker', 'anssp-dragula'],
            $hash,
            true
        );

        // Dragula CSS
        wp_enqueue_style( 
            'anssp-dragula',
            App::base_url() . 'assets/vendor/dragula.min.css',
            [], 
            $hash 
        );

        // Custom CSS
        wp_enqueue_style( 
            'anssp-adminpage',
            App::base_url() . 'assets/css/frontpage.css',
            [], 
            $hash 
        );
    }

} 
