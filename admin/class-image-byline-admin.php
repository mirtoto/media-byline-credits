<?php

/**
* The admin-specific functionality of the plugin.
*
* @link       https://www.devbackroom.com/
* @since      1.0.0
*
* @package    Image_Byline
* @subpackage Image_Byline/admin
*/

/**
* The admin-specific functionality of the plugin.
*
* Defines the plugin name, version, enqueues the admin-specific stylesheets
* and JavaScript.
*
* @package    Image_Byline
* @subpackage Image_Byline/admin
* @author     Michelle Earl <michelle@devbackroom.com>
*/
class Image_Byline_Admin {

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
	* @param      string    $plugin_name       The name of this plugin.
	* @param      string    $version    The version of this plugin.
	*/
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	* Register the stylesheets for the admin area.
	*
	* @since    1.0.0
	*/
	public function enqueue_styles() {

		wp_enqueue_style( 'auto_complete', plugin_dir_url( __FILE__ ) . 'css/jquery.auto-complete.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/image-byline-admin.css', array(), $this->version, 'all' );

	}

	/**
	* Register the JavaScript for the admin area.
	*
	* @since    1.0.0
	*/
	public function enqueue_scripts() {

		wp_enqueue_script( 'auto_complete_js', plugin_dir_url( __FILE__ ) . 'js/jquery.auto-complete.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/image-byline-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	* Output a json list of names for the autocomplete search.
	*
	* @since    1.0.0
	*/
	public function ajax_search_list() {

		$options = get_option( 'imageByline_options' );
		$search_list = array();

		if ( !empty($options['search_suggestions_role']) ) {
			$users = get_users( 'orderby=nicename&role='.$options['search_suggestions_role'] );

			foreach ( $users as $value ) {
				$search_list[] = addslashes($value->display_name);
			}
		}

		if ( !empty($options['search_suggestions']) ) {
			$names = explode("\n", str_replace(array("\n", "\r\n"), "\n", $options['search_suggestions']));
			foreach ($names as $value) {
				$value = trim($value);
				if (!in_array($value, $search_list)) {
					$search_list[] = $value;
				}
			}
		}

		echo json_encode($search_list);
		die(); //stop "0" from being output
	}

	/**
	* Add byline_attachment_field to the $form_fields array
	*
	* @param array $form_fields
	* @param object $post
	* @return array
	* @since    1.0.0
	*/
	function byline_attachment_fields( $form_fields, $post ) {

		$form_fields['byline'] = array(
			'label' => __('Byline Credit', 'image-byline'),
			'input' => 'text',
			'value' => get_post_meta( $post->ID, '_byline', true )
		);

		$form_fields['byline_link'] = array(
			'label' => __('Byline Credit Link', 'image-byline'),
			'input' => 'text',
			'value' => get_post_meta( $post->ID, '_byline_link', true )
		);

		return $form_fields;
	}

	/**
	* Add byline_attachment_field to the $form_fields array
	*
	* @param object $post
	* @param array $attachment
	* @return object
	* @since    1.0.0
	*/
	function byline_attachment_fields_save( $post, $attachment ) {

		if ( isset( $attachment['byline'] ) ) {
			update_post_meta( $post['ID'], '_byline', $attachment['byline'] );
		}

		if ( isset( $attachment['byline_link'] ) ) {
			update_post_meta( $post['ID'], '_byline_link', $attachment['byline_link'] );
		}

		return $post;
	}

	/**
	* Add the dashboard settings menu item.
	*
	* @since    1.0.0
	*/
	public function add_admin_menu(  ) {

		add_submenu_page(
			'tools.php',
			__( 'Image Byline', 'image-byline' ),
			__( 'Image Byline', 'image-byline' ),
			'manage_options',
			'image_byline',
			array( $this, 'options_page' )
		);

	}

	/**
	* Initialize the dashboard settings page.
	*
	* @since    1.0.0
	*/
	public function settings_init() {

		register_setting(
			'imageByline_group',
			'imageByline_options',
			array('type' => 'array')
		);

		add_settings_section(
			'imageByline_group_section',
			__( 'General Options', 'image-byline' ),
			array( $this, 'settings_section_callback' ),
			'imageByline_group'
		);

		add_settings_field(
			'before_byline',
			__( 'Prefix byline credit', 'image-byline' ),
			array( $this, 'before_byline_render' ),
			'imageByline_group',
			'imageByline_group_section'
		);

		add_settings_field(
			'search_suggestions_role',
			__( 'Search suggestions role', 'image-byline' ),
			array( $this, 'search_suggestions_role_render' ),
			'imageByline_group',
			'imageByline_group_section'
		);

		add_settings_field(
			'search_suggestions',
			__( 'Search suggestions', 'image-byline' ),
			array( $this, 'search_suggestions_render' ),
			'imageByline_group',
			'imageByline_group_section'
		);
	}

	/**
	* Render the before_byline text field on the settings/options page.
	*
	* @since    1.0.0
	*/
	function before_byline_render() {

		$options = get_option( 'imageByline_options' );
		if ( !empty($options['before_byline']) ) {
			$value = $options['before_byline'];
		} else {
			$value = '';
		}
		?>
		<input type="text" name="imageByline_options[before_byline]" value="<?php echo $value; ?>">
		<td class="align-top"><em><?php esc_html_e( 'A short label to show before the byline credit e.g. Source:.', 'image-byline' ); ?></em></td>
		<?php

	}

	/**
	* Render the search_suggestions_role select on the settings/options page.
	*
	* @since    1.0.0
	*/
	function search_suggestions_role_render() {

		$options = get_option( 'imageByline_options' );

		global $wp_roles;
		$select_options = '<option value="">Select user role</option>';
		foreach ($wp_roles->get_names() as $key => $value) {
			if ( !empty($options['search_suggestions_role'] ) && $options['search_suggestions_role'] === $key ) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}
			$select_options .= '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		}

		?>
		<select name="imageByline_options[search_suggestions_role]"><?php echo $select_options; ?></select>
		<td class="align-top"><em><?php esc_html_e( 'You can use a user role to automatically get a list of sources to show in the Media Library Byline Credit field suggestions. Leave this field blank if you just want to use the simple list.', 'image-byline' ); ?></em></td>
		<?php

	}

	/**
	* Render the search_suggestions field on the settings/options page.
	*
	* @since    1.0.0
	*/
	function search_suggestions_render() {
		$options = get_option( 'imageByline_options' );
		if ( !empty($options['search_suggestions']) ) {
			$value = $options['search_suggestions'];
		} else {
			$value = '';
		}

		?>
		<textarea cols='40' rows='30' name='imageByline_options[search_suggestions]'><?php echo $value; ?></textarea>
		<td class="align-top"><em><?php esc_html_e( 'A list of sources to show in the Media Library Byline Credit field suggestions. Put each value on a new line. Leave this field blank if you just want to use a user role for your suggestions.', 'image-byline' ); ?></em></td>
		<?php

	}

	/**
	* Callback for the settings/options page.
	*
	* @since    1.0.0
	*/
	function settings_section_callback() {

		esc_html_e( 'Here you can configure your options for the Image and Media Byline Credits plugin. All the settings are optional.', 'image-byline' );

	}

	/**
	* Render the settings/options page.
	*
	* @since    1.0.0
	*/
	function options_page() {

		include_once('partials/image-byline-admin-display.php');

	}

}
