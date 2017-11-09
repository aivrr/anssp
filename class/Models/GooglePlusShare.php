<?php

/**
 * Class implements logos (buttons) for specific social share sites
 */

namespace Baerr\Anssp\Models;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class GooglePlusShare extends SocialShareBase
{
	/**
	 * Inits variables with values specific to social share site
	 */
	public function __construct()
	{
		$this->share_url = 'https://plus.google.com/share?url=%s';
		$this->fill_color = '#DC4E41';
		$this->name = 'Google+';
		$this->svg_code = '
			<svg %s viewBox="0 2 16 12" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="1.414"><path d="M5.09 7.273v1.745h2.89c-.116.75-.873 2.197-2.887 2.197-1.737 0-3.155-1.44-3.155-3.215S3.353 4.785 5.09 4.785c.99 0 1.652.422 2.03.786l1.382-1.33c-.887-.83-2.037-1.33-3.41-1.33C2.275 2.91 0 5.19 0 8s2.276 5.09 5.09 5.09c2.94 0 4.888-2.065 4.888-4.974 0-.334-.036-.59-.08-.843H5.09zm10.91 0h-1.455V5.818H13.09v1.455h-1.454v1.454h1.455v1.455h1.46V8.727H16"/></svg>';
	}
	

}