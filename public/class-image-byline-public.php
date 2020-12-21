<?php

/**
* The public-facing functionality of the plugin.
*
* @link       https://www.devbackroom.com/
* @since      1.0.0
*
* @package    Image_Byline
* @subpackage Image_Byline/public
*/

/**
* The public-facing functionality of the plugin.
*
* Defines the plugin name, version, and two examples hooks for how to
* enqueue the public-facing stylesheet and JavaScript.
*
* @package    Image_Byline
* @subpackage Image_Byline/public
* @author     Michelle Earl <michelle@devbackroom.com>
*/
class Image_Byline_Public {

	/**
	* The ID of this plugin.
	*
	* @since    1.0.0
	* @access   private
	* @var      string    $plugin_name    The ID of this plugin.
	*/
	private $plugin_name;

	/**
	* The version of this plugin.
	*
	* @since    1.0.0
	* @access   private
	* @var      string    $version    The current version of this plugin.
	*/
	private $version;

	/**
	* Initialize the class and set its properties.
	*
	* @since    1.0.0
	* @param      string    $plugin_name       The name of the plugin.
	* @param      string    $version    The version of this plugin.
	*/
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	* Register the stylesheets for the public-facing side of the site.
	*
	* @since    1.0.0
	*/
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/image-byline-public.css', array(), $this->version, 'all' );

	}

	/**
	* Register the JavaScript for the public-facing side of the site.
	*
	* @since    1.0.0
	*/
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/image-byline-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	* Register shortcodes.
	*
	* @since    1.0.0
	*/
	public function register_shortcodes() {
		add_shortcode( 'byline', array( $this, 'shortcode_byline') );
	}

	/**
	* Byline shortcode [byline]Photographer Name[/byline].
	*
	* @since    1.0.0
	*/
	public function shortcode_byline($atts, $content='') {
		return '<figcaption class="image-credit"><span class="image-byline">' . $content . '</span></figcaption>';
	}

	/**
	* Add the byline to the caption.
	*
	* @since    1.0.0
	*/
	public function add_byline_to_caption($caption, $post_id) {
		$img_id = $post_id;

		$byline = get_post_meta( $img_id, '_byline', true );
		if ( !empty($byline) ) {
			$link = get_post_meta( $img_id, '_byline_link', true );
			if ( !empty($link) ) {
				$byline = '<a href="' . $link . '" target="_blank" rel="no-follow">' . $byline . '</a>';
			}
		}

		$options = get_option( 'imageByline_options' );
		if ( !empty($options['before_byline']) ) {
			$before_byline = $options['before_byline'];
		} else {
			$before_byline = '';
		}

		$credits = '';
		if ( !empty( $byline) ) {
			if ( !empty( $caption ) ) {
				$credits .= ', ';
			}
			$credits .= '<span class="image-byline">';
			if ( !empty( $before_byline ) ) {
				$credits .= $before_byline . ' ';
			}
			$credits .= $byline . '</span>';
		}

		return '<figcaption class="image-credit"><span class="image-caption">' . $caption . '</span>'. $credits . '</figcaption>';
	}

	/**
	* Add the byline credit to the core image block.
	*
	* @since    1.0.0
	*/
	function byline_image_render( $attributes, $content ) {

		$attachment = get_post( $attributes['id'] );
		//$caption = wp_get_attachment_caption( $attributes['id'] );
		//$old_caption = '<figcaption>' . $attachment->post_excerpt . '</figcaption>';

		//return empty( $caption ) ? $content : str_replace( $old_caption, $caption, $content );

		libxml_use_internal_errors( true );
	
		$figureDocument = new DOMDocument();
		if (false === $figureDocument->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES' ) ) ) {
			return $content;
		}

		$captionValue = '';
		$figcaption = $figureDocument->getElementsByTagName( 'figcaption' );
		if ( 0 !== count( $figcaption ) ) {
			$captionValue = $figcaption->item(0)->nodeValue;
			// remove figcaption
			$figcaption->item(0)->parentNode->removeChild($figcaption->item(0));
		}
		// build new caption with credits
		$caption = $this->add_byline_to_caption($captionValue, $attributes['id']);

		// insert new figcaption
		$figcaptionDocument = new DOMDocument();
		if (false === $figcaptionDocument->loadHTML( mb_convert_encoding( $caption, 'HTML-ENTITIES' ) ) ) {
			return $content;
		}
		$figcaptionNode = $figureDocument->importNode( $figcaptionDocument->documentElement, true );
		$figureDocument->getElementsByTagName( 'figure' )->item(0)->appendChild( $figcaptionNode );

		return $figureDocument->saveHTML();
	}

	/**
	* Register the core image block with a render callback.
	*
	* @since    1.0.0
	*/
	function byline_register_image() {

		register_block_type( 'core/image', array(
			'render_callback' => array( $this, 'byline_image_render'),
		) );
	}

}
