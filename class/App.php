<?php

/**
 * App-wide helper functions and config variables
 *
 */

namespace Baerr\Anssp;

use Another_Social_Share_Plugin as PluginLoader;
use Baerr\Anssp\Exceptions\GeneralException;
use Baerr\Anssp\PluginCore;
use Exception;
use WP_Post;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class App 
{
    // config variables
    private static $config = [
        // public name
        'name' => 'Another Social Share Plugin',
        // plugin version
        'version' => '1.0.0',
        // plugin dependencies
        'dependency' => [
            //required PHP version
            'php_version' => '5.6',
        ],
        // nonce for form submission
        'nonce_key' => 'anssplugin_nonce',
        // WP option names
        'wp_option' => [
            'version' => 'anssplugin_version',
        ],
        // social shares
        'shares' => [
            // available types/class names
            'all' => [
                'FacebookShare',
                'PinterestShare',
                'GooglePlusShare',
                'LinkedInShare',
                'WhatsAppShare',
                'TwitterShare',
            ],
            // positions
            'position' => [
                'below_title' => 'below_post_title', 
                'float_left' => 'floating_left', 
                'after_content' => 'after_post_content', 
                'inside_hero' => 'inside_featured_image',
            ],
            // labels for settings screen
            'position_name' => [
                'below_title' => 'Below the post title', 
                'float_left' => 'Floating on the left area', 
                'after_content' => 'After the post content', 
                'inside_hero' => 'Inside the featured image',
            ],
            // sizes / css class for displaying buttons
            'size' => [
                'large' => 'anssp-large',
                'medium' => 'anssp-medium',
                'small' => 'anssp-small',
            ],
            // labels for settings screen
            'size_name' => [
                'large' => 'Large',
                'medium' => 'Medium',
                'small' => 'Small',
            ]
        ],
        // WordPress builtin post types which can display share bar
        'builtin_post_types' => [
            'post',
            'page'
        ],
        // plugin settings variables
        'settings' => [
            // WP admin screen slug
            'slug' => 'anssp_admin',
            // allows settings edit
            'capability' => 'manage_options',
            // WP settings API 
            'page' => 'anssp_settings_page',
            // option name to be saved to db
            'option_name' => 'anssp_settings',
            // options field names
            'field' => [
                'post_types' => 'post_types',
                'active' => 'active_items',
                'button_size' => 'button_size',
                'fill_color' => 'fill_color',
                'position' => 'position',
            ]
        ]
    ];


    /**
     * gets config variable by its name
     * can use "." to access array items instead of []
     * Throws exception in debug environment if wrong key is supllied
     * Otherwise quietly returns null
     * @param  string $path config item path, uses dots to access array items
     * @return mixed       config variable
     */
    public static function config($path)
    {
    	$keys = explode('.', $path);
    	$config = self::$config;
    	foreach ($keys as $key) {
    		if (!isset($config[$key])) {
                // throw exception if wrong key while debuggin
                if (self::debug_mode()) {
                    throw new Exception('Wrong config key');
                }
    			return null;
    		}
    	$config = $config[$key];
    	}

    	return $config;
    }

	// *** BASE ***
    // =======================================================================

    // Get the URL directory path (with trailing slash) 
    public static function base_url() {
        return PluginLoader::base_url();
    }

    // Get the filesystem directory path (with trailing slash) 
    public static function base_file_path() {
        return PluginLoader::base_file_path();
    }
    
    // Get the plugin basename 
    public static function base_plugin_name() {
        return PluginLoader::base_plugin_name();
    }

    // check if plugin's frontpage
    public static function is_frontpage() {
        return !is_admin();
    }

    // enqueue hash for cache busting
    public static function enqueue_hash() {
        return self::debug_mode()
            ? self::config('version')
            : substr(time(), -3);
    }

    // debug mode
    public static function debug_mode() {
        return (defined('WP_DEBUG') && WP_DEBUG === true);
    }

    // local env
    public static function is_local_env() {
        return (!isset($_SERVER['SERVER_NAME']) || stripos($_SERVER['SERVER_NAME'], 'localhost'));
    }

    /**
     * gets template
     * @param  string  $name    template name. may substitute "." for "/"
     *                          IMPORTANT: do not add ".php" extension
     * @param  array  $args variables to import into template namespace
     * @param  boolean $echo echo or return string
     * @return mixed        void or string depending on $echo
     */
    public static function get_template($name, $args = [], $echo = true)
    {
        extract($args, EXTR_SKIP);
        $tpl_name = self::template_path($name);

        if ($echo) {
            require $tpl_name;
            return;
        }

        ob_start();
        require $tpl_name;
        return ob_get_clean();
    }

    /**
     * returns full template path and name 
     * @param  string  $name     template name. may substitute "." for "/"
     *                           IMPORTANT: do not add ".php" extension
     * @return string       full template path
     */
    public static function template_path($name)
    {
        $name = str_replace('.', '/', $name) . '.php';
        $tpl_name = self::base_file_path() . 'templates/' . $name;

        if (!file_exists($tpl_name)) {
            throw new GeneralException("Asking for template $tpl_name which does not exist");
        }

        return $tpl_name;
    }

    /**
     * get public custom post types
     * @return array of custom post types
     */
    public static function get_public_cpt()
    {
        return array_values(get_post_types([
            'public' => true,
            '_builtin' => false
        ]));
    }


}
