<?php

/**
 * Admin Options Manager
 * Creates admin settings page useing WP Settings and Options APIs
 * All settings are stored as an array in wp option
 *
 */

namespace Baerr\Anssp\Controllers;

use Baerr\Anssp\App;
use Baerr\Anssp\Models\ShareBarOptions;
use Baerr\Anssp\Models\SocialShareProvider;


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class SettingsManager
{
    /**
     * admin menu slug
     * @var string
     */
    protected $menu_registered_slug;

    /**
     * option name which stores plugin settings
     * @var string
     */
    protected $option_name;

    /**
     * all options as an array
     * @var array
     */
    protected $options;

    /**
     * Provider outputs share buttons html
     * @var SocialShareProvider
     */
    protected $provider;

    public function __construct(ShareBarOptions $opts, SocialShareProvider $provider)
    {
        $this->option_name = App::config('settings.option_name');
        $this->options = $opts->all();
        $this->provider = $provider;
        // registers wordpress hooks
        $this->register();
    }

    /**
     * returns admin page slug
     * @return string
     */
    public function slug()
    {
        return $this->menu_registered_slug;
    }

    /**
     * Registers menu in admin area
     */
    public function register_menu() 
    {
        // add menu page to admin area
        $this->menu_registered_slug = add_menu_page(
            'Another Social Share Plugin', 
            'Social Share',
            App::config('settings.capability'), 
            App::config('settings.slug'), 
            [$this, 'settings_page'],
            'dashicons-share',
            78
        );
    }

    /**
     * inits and outputs settings on admin page
     */
    public function settings_init() 
    { 
        // all settings are stored under this name
        register_setting(App::config('settings.page'), $this->option_name, function($options) {
            // sanitize input
            // walks array recursively and applies sanitize function
            array_walk_recursive($options, function(&$item, $key) {
                $item = sanitize_text_field($item);
            });

            return $options;
        });

        // two settings sections
        $section_display = App::config('settings.page') . '_section_display';
        $section_filter = App::config('settings.page') . '_section_filter';
        // shorter variable names 
        $opt_name = $this->option_name;
        $options = $this->options;

        // two settings sections
        add_settings_section($section_filter, 'Basic Settings', function() {
        }, App::config('settings.page'));
        add_settings_section($section_display, 'Appearance Settings', function() {
        }, App::config('settings.page'));


        // Selectable Post types
        add_settings_field( 
            $opt_name . '-' . App::config('settings.field.post_types'), 
            'Select Post Types', 
            function() use ($options, $opt_name) {
                global $wp_post_types;
                // name attribute
                $name = esc_attr(sprintf('%s[%s][]', $opt_name, App::config('settings.field.post_types')));
                // all possible post types
                $types = ShareBarOptions::all_post_types();
                // post types
                $allowed = $options[App::config('settings.field.post_types')];

                ?>
                    <select name="<?= $name ?>" multiple>
                    <?php
                    foreach ($types as $type) :
                        $post_name = $wp_post_types[$type]->labels->singular_name; 
                        ?>
                            <option value="<?php echo esc_attr($type) ?>" 
                                <?php 
                                    echo (in_array($type, $allowed, true)) ? ' selected ' : ''; 
                                ?>
                            ><?php echo esc_html($post_name) ?></option>
                        <?php
                    endforeach;
                    ?>
                    </select>
                <?php
            }, 
            App::config('settings.page'), 
            $section_filter
        );


        // Active and inactive items
        add_settings_field( 
            $opt_name . '-' . App::config('settings.field.active'), 
            'Select Social Networks And Their Order', 
            function() use ($options, $opt_name) {
                // name attribute
                $name = esc_attr(sprintf('%s[%s]', $opt_name, App::config('settings.field.active')));
                // active social shares
                $active_items = esc_attr(implode(',', $options[App::config('settings.field.active')]));

                ?>
                    <div class="anssp-drag-containers-wrap">
                    <?php
                        echo $this->provider->settings_active();
                        echo $this->provider->settings_inactive();
                    ?>
                    </div>
                <?php

                ?>
                    <input type="hidden" value="<?= $active_items ?>" name="<?= $name ?>">
                <?php
            }, 
            App::config('settings.page'), 
            $section_filter
        );


        // Share bar position
        add_settings_field( 
            $opt_name . '-' . App::config('settings.field.position'), 
            'Select Share Bar Position', 
            function() use ($options, $opt_name) {
                // name attribute
                $name = esc_attr(sprintf('%s[%s][]', $opt_name, App::config('settings.field.position')));
                // all possible positions
                $types = App::config('shares.position');
                // saved positions
                $saved_pos = $options[App::config('settings.field.position')];
                // labels for user
                $labels = App::config('shares.position_name');

                foreach ($types as $pos_id => $pos) :
                    $id = esc_attr(sprintf('%s_%s', App::config('settings.field.position'), $pos_id));
                    ?>
                        <input type="checkbox" name="<?= $name ?>" id="<?= $id ?>"
                        <?php 
                            echo (in_array($pos, $saved_pos, true)) ? ' checked ' : ''; 
                        ?>      
                        value="<?php echo esc_attr($pos) ?>">
                        <label for="<?= $id ?>">
                            <?php echo esc_html(isset($labels[$pos_id]) ? $labels[$pos_id] : '') ?>
                        </label>
                        <br>
                    <?php
                endforeach;
            }, 
            App::config('settings.page'), 
            $section_display
        );


        // Fill color
        add_settings_field( 
            $opt_name . '-' . App::config('settings.field.fill_color'), 
            'Select Icon Color', 
            function() use ($options, $opt_name) {
                // name attribute
                $name = esc_attr(sprintf('%s[%s]', $opt_name, App::config('settings.field.fill_color')));
                // saved color value
                $fill_color = esc_attr($options[App::config('settings.field.fill_color')]);

                ?>
                    <input type="text" name="<?= $name ?>" value="<?= $fill_color ?>" class="wp-colorpicker" />
                <?php

            }, 
            App::config('settings.page'), 
            $section_display
        );


        // Button size
        add_settings_field( 
            $opt_name . '-' . App::config('settings.field.button_size'), 
            'Select Button Size', 
            function() use ($options, $opt_name) {
                // name attribute
                $name = esc_attr(sprintf('%s[%s]', $opt_name, App::config('settings.field.button_size')));
                // all possible button sizes
                $types = App::config('shares.size');
                // saved value
                $saved_size = $options[App::config('settings.field.button_size')];
                // labels for user
                $labels = App::config('shares.size_name');

                ?>
                    <select name="<?= $name ?>">
                    <?php
                    foreach ($types as $size_id => $size) :
                        ?>
                            <option value="<?php echo esc_attr($size) ?>" 
                                <?php 
                                    echo ($size === $saved_size) ? ' selected ' : ''; 
                                ?>
                            ><?php echo esc_html(isset($labels[$size_id]) ? $labels[$size_id] : '') ?></option>
                        <?php
                    endforeach;
                    ?>
                    </select>
                <?php
            }, 
            App::config('settings.page'), 
            $section_display
        );


    }

    /**
     * Displays admin page
     */
    public function settings_page() {
        // double check if user can access this page
        if (!current_user_can(App::config('settings.capability'))) {
            wp_die('You do not have permission to access this page', 'Error', ['response' => 403]);
        }

        ?>
        <form action='options.php' method='post' id="anssp-settings-form">

            <h1>Another Social Share Plugin Settings</h1>
            <p>please use [anssp_bar] shortcode to place a share bar anywhere within your post content</p>
            <br>

            <?php
            settings_fields(App::config('settings.page'));
            do_settings_sections(App::config('settings.page'));
            submit_button();
            ?>

        </form>
        <?php
    }

    /**
     * registers hooks
     */
    protected function register()
    {
        // register admin menu
        add_action( 'admin_menu', [$this, 'register_menu'], 99 );
        // register settings
        add_action( 'admin_init', [$this, 'settings_init'] );
    }

}