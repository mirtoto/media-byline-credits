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
		add_shortcode( 'byline', array( $this, 'shortcode_byline' ) );
	}

	/**
	* Byline shortcode [byline]Photographer Name[/byline].
	*
	* @since    1.0.0
	*/
	public function shortcode_byline($atts, $content = '') {
		return $this->figcaption( '<span class="image-byline">' . $content . '</span>' );
	}

	/**
	* Add the byline to the caption.
	*
	* @since    1.0.0
	*/
	public function add_byline_to_caption( $caption, $post_id ) {

		$img_id = $post_id;

		$byline = get_post_meta( $img_id, '_byline', true );
		if ( !empty($byline) ) {
			$link = get_post_meta( $img_id, '_byline_link', true );
			if ( !empty($link) ) {
				$byline = '<a class="image-byline-link" href="' . $link . '" target="_blank" rel="no-follow">' . $byline . '</a>';
			}
		}

		$before_byline = '';
		$options = get_option( 'imageByline_options' );
		if ( !empty($options[ 'before_byline' ]) ) {
			$before_byline = $options[ 'before_byline' ];
		}

		$credits = '';
		if ( !empty( $byline) ) {
			if ( !empty( $caption ) ) {
				$credits .= '<span class="image-credit-separator"></span>';
			}
			$credits .= '<span class="image-byline">';
			if ( !empty( $before_byline ) ) {
				$credits .= $before_byline;
			}
			$credits .= $byline . '</span>';
		}

		return '<span class="image-caption">' . $caption . '</span>'. $credits;
	}

	/**
	* Add the byline credit to the core image block.
	*
	* @since    1.0.0
	*/
	function byline_image_render( $attributes, $content ) {

		libxml_use_internal_errors( true );
		$libxmlOpt = LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD;
	
		// parse figure block
		$figureDocument = new DOMDocument();
		if (false === $figureDocument->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES' ), $libxmlOpt ) ) {
			return $content;
		}

		$captionValue = '';
		$figcaption = $figureDocument->getElementsByTagName( 'figcaption' );
		if ( 0 !== count( $figcaption ) ) {
			$captionValue = $figcaption->item(0)->nodeValue;
			// remove figcaption
			$figcaption->item(0)->parentNode->removeChild( $figcaption->item(0) );
		}
		// build new caption with credits
		$caption = $this->figcaption( $this->add_byline_to_caption( $captionValue, $attributes[ 'id' ] ) );

		// parse new figcaption block
		$figcaptionDocument = new DOMDocument();
		if (false === $figcaptionDocument->loadHTML( mb_convert_encoding( $caption, 'HTML-ENTITIES' ), $libxmlOpt ) ) {
			return $content;
		}
		// insert new figcaption block into figure block
		$figcaptionNode = $figureDocument->importNode( $figcaptionDocument->documentElement, true );
		$figure = $figureDocument->getElementsByTagName( 'figure' );
		if ( 0 !== count( $figure ) ) {
			$figure->item(0)->appendChild( $figcaptionNode );
		}

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

	/**
	 * Add the byline to the featured image.
	 * 
	 * @since    1.1.0
	 */
	function add_byline_to_featured_caption( $html, $post_id, $post_thumbnail_id, $size, $attr ) {

		$featured_image_byline = 0;
		$options = get_option( 'imageByline_options' );
		if ( !empty($options[ 'featured_image_byline' ]) ) {
			$featured_image_byline = $options[ 'featured_image_byline' ];
		}

		// If byline caption append to featured image is not enabled
		// or we're not in The Loop, return the HTML unchanged
        if ( ! $featured_image_byline || ! in_the_loop() ) {
            return $html;
		} 
		
		// If we're not on a single post
        if ( ! is_singular() ) {
            return $html;
		}
		
		$caption = get_post( $post_thumbnail_id )->post_excerpt;
		$caption = $this->add_byline_to_caption( $caption, $post_thumbnail_id );
		return $html . $this->figcaption( $caption, true );
	}

	/**
	 * Wrap passed caption by <figcaption></figcaption> container.
	 * 
	 * @since    1.1.0
	 */
	function figcaption( $caption, $featured = false ) {
		$featured = $featured ? ' featured-image-credit' : '';
		return '<figcaption class="wp-caption-text image-credit' . $featured . '">' . $caption . '</figcaption>';
	}

}
