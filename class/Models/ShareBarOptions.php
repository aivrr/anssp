<?php

/**
 * Share Bar Options
 * gets options from database and provides them to other classes
 */

namespace Baerr\Anssp\Models;

use Baerr\Anssp\App;


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class ShareBarOptions
{
	/**
	 * types of posts which display share bar
	 * @var array
	 */
	protected $post_types;

	/**
	 * active social shares
	 * @var array
	 */
	protected $active_items;

	/**
	 * where share bar is displayed
	 * @var array
	 */
	protected $position;

	/**
	 * share button color 
	 * null means original color
	 * @var string/null
	 */
	protected $fill_color;

	/**
	 * button size
	 * @var string
	 */
	protected $button_size;

	/**
	 * all options in one array
	 * @var array
	 */
	protected $options;

	public function __construct()
	{
		// options defaults 
		$defaults = [
			App::config('settings.field.post_types') => [],
			App::config('settings.field.active') => App::config('shares.all'),
			App::config('settings.field.button_size') => App::config('shares.size.medium'),
			App::config('settings.field.fill_color') => null,
			App::config('settings.field.position') => [App::config('shares.position.after_content')],
		];

		// get options from db, use defaults as needed
		$options = wp_parse_args(get_option(App::config('settings.option_name')), $defaults);
		
		$this->post_types = $options[App::config('settings.field.post_types')];

		// active items are stored as CSV
		if (!is_array($options[App::config('settings.field.active')])) {
			$options[App::config('settings.field.active')] = explode(',', $options[App::config('settings.field.active')]);
		}
		// check against allowed list of shares/classes
		$options[App::config('settings.field.active')] = array_intersect($options[App::config('settings.field.active')], App::config('shares.all'));
		$this->active_items = $options[App::config('settings.field.active')];

		// convert any empty color value to null for consistency
		if (empty($options[App::config('settings.field.fill_color')])) {
			$options[App::config('settings.field.fill_color')] = null;
		}
		$this->fill_color = $options[App::config('settings.field.fill_color')];
		$this->button_size = $options[App::config('settings.field.button_size')];
		$this->position = $options[App::config('settings.field.position')];

		$this->options = $options;
	}
	
	public function active_items()
	{
		return $this->active_items;
	}

	public function inactive_items()
	{
		return array_diff(App::config('shares.all'), $this->active_items);
	}

	public function position()
	{
		return $this->position;
	}

	public function color()
	{
		return $this->fill_color;
	}

	public function size()
	{
		return $this->button_size;
	}

	/**
	 * all public post types
	 * @return array   of all post types
	 */
	public static function all_post_types()
	{
		return array_merge(App::config('builtin_post_types'), App::get_public_cpt());
	}

	public function post_types()
	{
		return $this->post_types;
	}

	/**
	 * returns all options in one array
	 * @return array  associative array with options
	 */
	public function all()
	{
		return $this->options;
	}
}