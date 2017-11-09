<?php

/**
 * Base Social Share Button Class
 */

namespace Baerr\Anssp\Models;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class SocialShareBase implements SocialShareInterface
{
	/**
	 * URL for sharing. Includes %s placeholder
	 * @var string
	 */
	protected $share_url;

	/**
	 * Original logo fill color. Must be valid CSS color value.
	 * @var string 
	 */
	protected $fill_color;

	/**
	 * SVG for displaying logo. Includes %s placeholder for fill color
	 * @var string
	 */
	protected $svg_code;

	/**
	 * Name for displaying label on button
	 * @var string
	 */
	protected $name;

	/**
	 * Share is only displayed on mobile screens
	 * @var boolean
	 */
	protected $mobile_only = false;

	/**
	 * This share requires media url
	 * @var boolean
	 */
	protected $need_media = false;

	/**
	 * Gets HTML code for social share button
	 * @param  string $color logo color
	 * @return string   button html
	 */
	public function html($color = null)
	{
		if (!isset($color)) {
			$color = $this->fill_color;
		}

		$color_attr = sprintf('fill="%s"', esc_attr($color));

		return sprintf($this->svg_code, $color_attr);
	}

	/**
	 * Gets share url
	 * @param  string $permalink  url to share
	 * @return string   share url
	 */
	public function share_url($permalink)
	{
		return sprintf($this->share_url, urlencode($permalink));
	}

	/**
	 * Gets original logo color
	 * @return string   color value
	 */
	public function color()
	{
		return $this->fill_color;
	}

	/**
	 * Gets name/label for button
	 * @return string   name
	 */
	public function name()
	{
		return $this->name;
	}

	/**
	 * Display only on mobile screens (tablets included)
	 * @return string   name
	 */
	public function only_mobile()
	{
		return $this->mobile_only;
	}

	/**
	 * Social share requires media url
	 * @return string   name
	 */
	public function need_media()
	{
		return $this->need_media;
	}
	

}