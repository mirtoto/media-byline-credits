<?php

/**
* Define the internationalization functionality
*
* Loads and defines the internationalization files for this plugin
* so that it is ready for translation.
*
* @link       https://www.devbackroom.com/
* @since      1.0.0
*
* @package    Image_Byline
* @subpackage Image_Byline/includes
*/

/**
* Define the internationalization functionality.
*
* Loads and defines the internationalization files for this plugin
* so that it is ready for translation.
*
* @since      1.0.0
* @package    Image_Byline
* @subpackage Image_Byline/includes
* @author     Michelle Earl <michelle@devbackroom.com>
*/
class Image_Byline_i18n {

	/**
	* Load the plugin text domain for translation.
	*
	* @since    1.0.0
	*/
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'image-byline',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

}
