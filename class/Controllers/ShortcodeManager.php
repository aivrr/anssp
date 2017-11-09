<?php

/**
 * Shortcode Manager
 *
 */

namespace Baerr\Anssp\Controllers;

use Baerr\Anssp\Models\SocialShareProvider;


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class ShortcodeManager
{
    /**
     * provides html code
     * @var SocialShareProvider
     */
    protected $provider;

    public function __construct(SocialShareProvider $provider)
    {
        $this->provider = $provider;
        $this->register();
    }

	/**
     * shortcode to display share bar
     * @param  array $atts     shortcode attributes
     * @param  string $content enclosed content
     * @return string          html code
     */
    public function share_bar($atts, $content = '')
    {
        return $this->provider->full_width_content();
    }

    /**
     * registers shortcode hook
     */
    protected function register()
    {
        add_shortcode('anssp_bar', [$this, 'share_bar'], 10 , 2);
    }
}