<?php

/**
 * Produces HTML code for displaying social share bar
 */

namespace Baerr\Anssp\Models;

use Baerr\Anssp\App;
use Baerr\Anssp\Models\ShareBarOptions;
use Baerr\Anssp\Models\SocialShareInterface;
use Detection\MobileDetect;


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class SocialShareProvider
{
	/**
	 * Options for displaying social share bar
	 * @var ShareBarOptions
	 */
	protected $options;

	/**
	 * Contains all social shares to display
	 * @var array
	 */
	protected $items;

	/**
	 * Contains inactive social shares
	 * @var array
	 */
	protected $inactive;

	/**
	 * Contains active social shares
	 * @var array
	 */
	protected $active;

	/**
	 * is mobile screen (tablet included)
	 * @var boolean
	 */
	protected $is_mobile;


	public function __construct(ShareBarOptions $opts)
	{
		// mobile device detection
		$mobileDetect = new MobileDetect();
		$this->is_mobile = $mobileDetect->isMobile();

		$this->options = $opts;
		// used for frontend share bar
		$this->items = $this->load_items($opts->active_items());
		// used for admin settings screen
		$this->inactive = $this->load_items($opts->inactive_items(), true);
		$this->active = $this->load_items($opts->active_items(), true);
	}

	/**
	 * full width container with social share bar
	 * @return string   html code
	 */
	public function full_width_content()
	{
		return App::get_template('full-width', ['content' => $this->items_html($this->items)], false);
	}

	/**
	 * floating bar with social share bar
	 * @return string   html code
	 */
	public function floating_bar()
	{
		return App::get_template('floating', ['content' => $this->items_html($this->items)], false);
	}

	/**
	 * admin settings container with active items
	 * @return string   html code
	 */
	public function settings_active()
	{
		return App::get_template('container', [
			'content' => $this->items_html($this->active, true), 
			'id' => 'anssp-active-items', 
			'label' => 'Active Items'
		], false);
	}

	/**
	 * admin settings container with inactive items
	 * @return string   html code
	 */
	public function settings_inactive()
	{
		return App::get_template('container', [
			'content' => $this->items_html($this->inactive, true), 
			'id' => 'anssp-inactive-items', 
			'label' => 'Inactive Items'
		], false);
	}

	/**
	 * admin settings container with active items
	 * @param  array  $items     array of SocialShareInterface objects 
	 * @param  boolean $no_links do not generate <a></a> around button if true 
	 * @return string   html code
	 */
	protected function items_html($items, $no_links = false)
	{
		$html = array_map(function($item) use ($no_links) {

			$size = ($this->options->size() !== App::config('shares.size.medium')) ? $this->options->size() : '';
			// class name without namespace
			$name = substr(strrchr(get_class($item), '\\'), 1);
			$link = null;
			if (!$no_links && get_permalink() !== false) {
				if ($item->need_media() && empty(get_the_post_thumbnail_url())) {
					return '';
				} 

				$link = $item->need_media()
					? $item->share_url(get_permalink(), get_the_post_thumbnail_url())
					: $item->share_url(get_permalink());
			}

			return App::get_template('button', [
				'svg' => $item->html($this->options->color()), 
				'size' => esc_attr($size), 
				'share_name' => esc_attr($name), 
				'color' => esc_attr($this->options->color()),
				'link' => esc_url($link),
			], false);

		}, $items);

		return implode($html);
	}

	/**
	 * creates SocialShareInterface instances
	 * @param  array  $items      social shares types / class names
	 * @param  boolean $no_filter if false uses mobile device detection and mobile only items
	 * @return array              SocialShareInterface instances
	 */
	protected function load_items($items, $no_filter = false)
	{
		$shares = [];
		foreach ($items as $item) {
			// get namespaced class name
			$class_name = __NAMESPACE__ . '\\' . $item;
			if (!class_exists($class_name)) {
				continue;
			}

			$loaded = new $class_name;
			// class must implement SocialShareInterface
			if (!$loaded instanceof SocialShareInterface) {
				continue;
			}

			// check for mobile only items
			if (!$no_filter && $loaded->only_mobile() && !$this->is_mobile) {
				continue;
			}

			$shares[] = $loaded;
		}

		return $shares;
	}


}