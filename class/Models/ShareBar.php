<?php

/**
 * Outputs and injects html code for displaying share bar
 * Html code is provided by SocialShareProvider
 *
 */

namespace Baerr\Anssp\Models;

use Baerr\Anssp\App;
use Baerr\Anssp\Models\ShareBarOptions;
use Baerr\Anssp\Models\SocialShareProvider;


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class ShareBar
{
    /**
     * share bar options
     * @var ShareBarOptions
     */
    protected $options;

    /**
     * outputs html code
     * @var SocialShareProvider
     */
    protected $provider;

    public function __construct(ShareBarOptions $opts)
    {
        $this->options = $opts;
        $this->provider = new SocialShareProvider($this->options);
        $this->register_hooks();
    }

    /**
     * registers hooks to inject share bar html code 
     */
    protected function register_hooks()
    {
        // register hooks according to position setting
        foreach($this->options->position() as $pos) {
            switch ($pos) {
                case App::config('shares.position.below_title'):
                    add_filter('the_content', [$this, 'html_after_post_title'], 99);
                    break;

                case App::config('shares.position.float_left'):
                    add_filter('the_content', [$this, 'html_floating_left'], 99);
                    break;

                case App::config('shares.position.after_content'):
                    add_filter('the_content', [$this, 'html_after_content'], 99);
                    break;

                case App::config('shares.position.inside_hero'):
                    add_filter('post_thumbnail_html', [$this, 'wrap_post_thumbnail'], 99, 5);
                    break;
            }
        }
    }

    /**
     * Filters the post thumbnail HTML
     * @param  string $html              The post thumbnail HTML
     * @param  int $post_id              The post ID
     * @param  string $post_thumbnail_id The post thumbnail ID
     * @param  string/array $size        The post thumbnail size
     * @param  string $attr              Query string of attributes
     * @return string                    post thumbnail HTML
     */
    public function wrap_post_thumbnail($html, $post_id, $post_thumbnail_id, $size, $attr) {
        // post type is not allowed
        if (!anssplugin()->has_share_bar()) {
            return $html;
        }

        // don't display on blog homepage (list of posts)
        // don't display if $html empty (no image to display)
        if (is_home() || empty($html)) {
            return $html;
        }

        return sprintf('<div class="anssp-hero-wrap">%s%s</div>', $html, $this->provider->full_width_content());
    }

    /**
     * Filters the content of the post
     * share bar after post title
     * @param  string $content Content of the post
     * @return string          Content of the post
     */
    public function html_after_post_title($content)
    {
        // post type is not allowed
        if (!anssplugin()->has_share_bar()) {
            return $content;
        }
        
        return $this->provider->full_width_content() . $content;
    }

    /**
     * Filters the content of the post
     * share bar floating
     * @param  string $content Content of the post
     * @return string          Content of the post
     */
    public function html_floating_left($content)
    {
        // post type is not allowed
        if (!anssplugin()->has_share_bar()) {
            return $content;
        }

        return $content . $this->provider->floating_bar();
    }

    /**
     * Filters the content of the post
     * share bar after post content
     * @param  string $content Content of the post
     * @return string          Content of the post
     */
    public function html_after_content($content)
    {
        // post type is not allowed
        if (!anssplugin()->has_share_bar()) {
            return $content;
        }
        
        return $content . $this->provider->full_width_content();
    }

    /**
     * SocialShareProvider instance
     * @return SocialShareProvider
     */
    public function provider()
    {
        return $this->provider;
    }
    
}