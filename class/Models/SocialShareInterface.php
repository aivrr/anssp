<?php

/**
 * Interface for Social Share Button
 */

namespace Baerr\Anssp\Models;


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

interface SocialShareInterface
{

	/**
	 * Gets HTML code for social share button
	 * @param  string $color logo color
	 * @return string   button html
	 */
	public function html($color = null);

	/**
	 * Gets share url
	 * @param  string $permalink  url to share
	 * @return string   share url
	 */
	public function share_url($permalink);

	/**
	 * Gets original logo color
	 * @return string   color value
	 */
	public function color();

	/**
	 * Gets name/label for button
	 * @return string   name
	 */
	public function name();

	/**
	 * Display only on mobile screens (tablets included)
	 * @return string   name
	 */
	public function only_mobile();

	/**
	 * Social share requires media url
	 * @return string   name
	 */
	public function need_media();
	

}