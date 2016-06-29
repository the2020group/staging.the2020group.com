<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WooCom_AdobeConnect_Settings {

	public function __construct( $file ) {
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->errlog_dir = trailingslashit( $this->dir ) . 'error_logs';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );
		$this->settings_base = 'wpt_';

		// Initialise settings
		add_action( 'admin_init', array( $this, 'init' ) );

		// Register plugin settings
		add_action( 'admin_init' , array( $this, 'register_settings' ) );

		// Add settings page to menu
		add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );

		// Add settings link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( $this->file ) , array( $this, 'add_settings_link' ) );
	}

	/**
	 * Initialise settings
	 * @return void
	 */
	public function init() {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	public function add_menu_item() {
		$page = add_options_page( __( 'WooCom to Adobe Connect', 'wcac_plugin_text' ) , __( 'WooCom to Adobe Connect', 'wcac_plugin_text' ) , 'manage_options' , 'wcac_settings' ,  array( $this, 'settings_page' ) );
		$page = add_menu_page( 'WCAC', '<b>Adobe Connect</b>', 'manage_options', 'wcac_settings', array($this,'settings_page'), plugins_url( 'images/favicon.ico', __FILE__ )  );
		add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
			
	}

	/**
	 * Load settings JS & CSS
	 * @return void
	 */
	public function settings_assets() {

        // We're including the farbtastic script & styles here because they're needed for the colour picker
        // If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below
        wp_enqueue_style( 'farbtastic' );
        wp_enqueue_script( 'farbtastic' );

        // We're including the WP media scripts here because they're needed for the image upload field
        // If you're not including an image upload then you can leave this function call out
        wp_enqueue_media();

        wp_register_script( 'wpt-admin-js', $this->assets_url . 'js/admin.js', array( 'farbtastic', 'jquery' ), '1.0.0' );
        wp_enqueue_script( 'wpt-admin-js' );
	}

	/**
	 * Add settings link to plugin list table
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=wcac_settings">' . __( 'Settings', 'wcac_plugin_text' ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}

	/**
	 * Build settings fields
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields() {
		
		$settings['standard'] = array(
			'title'					=> __( 'Adobe Connect Settings', 'wcac_plugin_text' ),
			'description'			=> __( 'Settings for Adobe Connect Server.', 'wcac_plugin_text' ),
			'fields'				=> array(
			
				array(
					'id' 			=> 'domain_field',
					'label'			=> __( 'Domain' , 'wcac_plugin_text' ),
					'description'	=> __( 'Enter the full domain of your host. DO NOT include "/" at the end. (i.e. - http://example.adobeconnect.com', 'wcac_plugin_text' ),
					'type'			=> 'text',
					'default'		=> 'https://example.adobeconnect.com',
					'placeholder'	=> __( 'Enter your domain', 'wcac_plugin_text' )
				),				
				array(
					'id' 			=> 'login_field',
					'label'			=> __( 'Admin Login' , 'wcac_plugin_text' ),
					'description'	=> __( 'Login to the account that can create stuff.', 'wcac_plugin_text' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'Enter login', 'wcac_plugin_text' )
				),
				array(
					'id' 			=> 'password_field',
					'label'			=> __( 'Admin Password' , 'wcac_plugin_text' ),
					'description'	=> __( 'Password for the account above.', 'wcac_plugin_text' ),
					'type'			=> 'password',
					'default'		=> '',
					'placeholder'	=> __( 'Enter Password', 'wcac_plugin_text' )
				),

				//used only to create meetings - not currently used
				/*array(
					'id' 			=> 'folder_field',
					'label'			=> __( 'Folder ID' , 'wcac_plugin_text' ),
					'description'	=> __( 'This is an SCO of the folder where the stuff is stored.', 'wcac_plugin_text' ),
					'type'			=> 'number',
					'default'		=> '',
					'placeholder'	=> __( 'Folder SCO ID', 'wcac_plugin_text' )
				),*/
				
				array(  //A standard checkbox - if you save this option as checked then it will store the option as \'on\', otherwise it will be an empty string.
					'id' 			=> 'enable_email_notification_checkbox',
					'label'			=> __( 'E-mail notification', 'wcac_plugin_text' ),
					'description'	=> __( 'Select this option to enable a welcome email notification (sent and formatted by Adobe Connect server) to new users created by this. ', 'wcac_plugin_text' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				
				array(
					'id' 			=> 'prefix_field',
					'label'			=> __( 'User Prefix' , 'wcac_plugin_text' ),
					'description'	=> __( 'Set prefix for new users. Enter something like "C123-" to create a user like C123-FLastname. Plugin will use the first letter of the first name and full last name for usernames supplied by the billing info in WooC', 'wcac_plugin_text' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'Enter user prefix if required', 'wcac_plugin_text' )
				),							
			)
		);

		$settings['extra'] = array(
			'title'					=> __( 'WooCommerce Settings', 'wcac_plugin_text' ),
			'description'			=> __( 'WooCommerce related stuff.', 'wcac_plugin_text' ),
			'fields'				=> array(
			
				array(  //A standard checkbox - if you save this option as checked then it will store the option as \'on\', otherwise it will be an empty string.
					'id' 			=> 'enable_WC_checkbox',
					'label'			=> __( 'Enable WooCommerce integration', 'wcac_plugin_text' ),
					'description'	=> __( 'Select this option to enable additional tab in the Products Data section. This basically enables/disables integration.', 'wcac_plugin_text' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				

				array(
					'id' 			=> 'WC_tab_name_field',
					'label'			=> __( 'WC Tab Name' , 'wcac_plugin_text' ),
					'description'	=> __( 'Enter the desired Tab name that will show in the Product Info box tabs in the admin of WooCommerce', 'wcac_plugin_text' ),
					'type'			=> 'text',
					'default'		=> 'AC Details',
					'placeholder'	=> __( 'Enter desired tab name', 'wcac_plugin_text' )
				),	
			)
		);
		
	$settings['errors'] = array(
			'title'					=> __( 'Error Log Settings', 'wcac_plugin_text' ),
			'description'			=> __( 'These are some extra settings.', 'wcac_plugin_text' ),
			'fields'				=> array(
			
				array(  //A standard checkbox - if you save this option as checked then it will store the option as \'on\', otherwise it will be an empty string.
					'id' 			=> 'enable_errorlog_checkbox',
					'label'			=> __( 'Enable Error Log', 'wcac_plugin_text' ),
					'description'	=> __( 'Select this option to enable Error Log', 'wcac_plugin_text' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				

				array(
					'id' 			=> 'errorlog_name_field',
					'label'			=> __( 'Error Log File' , 'wcac_plugin_text' ),
					'description'	=> __( 'Enter the desired name for the error log. It will be placed into the plugin Error Logs directory', 'wcac_plugin_text' ),
					'type'			=> 'text',
					'default'		=> 'error_log.txt',
					'placeholder'	=> __( 'Enter error log file name', 'wcac_plugin_text' )
				),	

			)
		);		

		$settings = apply_filters( 'plugin_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register plugin settings
	 * @return void
	 */
	public function register_settings() {
		if( is_array( $this->settings ) ) {
			foreach( $this->settings as $section => $data ) {

				// Add section to page
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), 'wcac_settings' );

				foreach( $data['fields'] as $field ) {

					// Validation callback for field
					$validation = '';
					if( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field
					$option_name = $this->settings_base . $field['id'];
					register_setting( 'wcac_settings', $option_name, $validation );

					// Add field to page
					add_settings_field( $field['id'], $field['label'], array( $this, 'display_field' ), 'wcac_settings', $section, array( 'field' => $field ) );
				}
			}
		}
	}

	public function settings_section( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	/**
	 * Generate HTML for displaying fields
	 * @param  array $args Field data
	 * @return void
	 */
	public function display_field( $args ) {

		$field = $args['field'];

		$html = '';

		$option_name = $this->settings_base . $field['id'];
		$option = get_option( $option_name );

		$data = '';
		if( isset( $field['default'] ) ) {
			$data = $field['default'];
			if( $option ) {
				$data = $option;
			}
		}

		switch( $field['type'] ) {

			case 'text':
				$html .= '<input size="35" id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . $data . '"/>' . "\n";
			break;
			
			case 'password':
			case 'number':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . $data . '"/>' . "\n";
			break;

			case 'text_secret':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value=""/>' . "\n";
			break;

			case 'textarea':
				$html .= '<textarea id="' . esc_attr( $field['id'] ) . '" rows="5" cols="50" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '">' . $data . '</textarea><br/>'. "\n";
			break;

			case 'checkbox':
				$checked = '';
				if( $option && 'on' == $option ){
					$checked = 'checked="checked"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>' . "\n";
			break;

			case 'checkbox_multi':
				foreach( $field['options'] as $k => $v ) {
					$checked = false;
					if( in_array( $k, $data ) ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="checkbox" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '[]" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
			break;

			case 'radio':
				foreach( $field['options'] as $k => $v ) {
					$checked = false;
					if( $k == $data ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="radio" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
			break;

			case 'select':
				$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '">';
				foreach( $field['options'] as $k => $v ) {
					$selected = false;
					if( $k == $data ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
			break;

			case 'select_multi':
				$html .= '<select name="' . esc_attr( $option_name ) . '[]" id="' . esc_attr( $field['id'] ) . '" multiple="multiple">';
				foreach( $field['options'] as $k => $v ) {
					$selected = false;
					if( in_array( $k, $data ) ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '" />' . $v . '</label> ';
				}
				$html .= '</select> ';
			break;

			case 'image':
				$image_thumb = '';
				if( $data ) {
					$image_thumb = wp_get_attachment_thumb_url( $data );
				}
				$html .= '<img id="' . $option_name . '_preview" class="image_preview" src="' . $image_thumb . '" /><br/>' . "\n";
				$html .= '<input id="' . $option_name . '_button" type="button" data-uploader_title="' . __( 'Upload an image' , 'wcac_plugin_text' ) . '" data-uploader_button_text="' . __( 'Use image' , 'wcac_plugin_text' ) . '" class="image_upload_button button" value="'. __( 'Upload new image' , 'wcac_plugin_text' ) . '" />' . "\n";
				$html .= '<input id="' . $option_name . '_delete" type="button" class="image_delete_button button" value="'. __( 'Remove image' , 'wcac_plugin_text' ) . '" />' . "\n";
				$html .= '<input id="' . $option_name . '" class="image_data_field" type="hidden" name="' . $option_name . '" value="' . $data . '"/><br/>' . "\n";
			break;

			case 'color':
				?><div class="color-picker" style="position:relative;">
			        <input type="text" name="<?php esc_attr_e( $option_name ); ?>" class="color" value="<?php esc_attr_e( $data ); ?>" />
			        <div style="position:absolute;background:#FFF;z-index:99;border-radius:100%;" class="colorpicker"></div>
			    </div>
			    <?php
			break;

		}

		switch( $field['type'] ) {

			case 'checkbox_multi':
			case 'radio':
			case 'select_multi':
				$html .= '<br/><span class="description">' . $field['description'] . '</span>';
			break;

			default:
				$html .= '<label for="' . esc_attr( $field['id'] ) . '"><span class="description">' . $field['description'] . '</span></label>' . "\n";
			break;
		}

		echo $html;
	}

	/**
	 * Validate individual settings field
	 * @param  string $data Inputted value
	 * @return string       Validated value
	 */
	public function validate_field( $data ) {
		if( $data && strlen( $data ) > 0 && $data != '' ) {
			$data = urlencode( strtolower( str_replace( ' ' , '-' , $data ) ) );
		}
		return $data;
	}

	/**
	 * Load settings page content
	 * @return void
	 */
	public function settings_page() {

		// Build page HTML
		$html = '<div class="wrap" id="wcac_settings">' . "\n";
			$html .= '<h2>' . __( 'Plugin Settings' , 'wcac_plugin_text' ) . '</h2>' . "\n";
			$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

				// Setup navigation
				$html .= '<ul id="settings-sections" class="subsubsub hide-if-no-js">' . "\n";
					$html .= '<li><a class="tab all current" href="#all">' . __( 'All' , 'wcac_plugin_text' ) . '</a></li>' . "\n";

					foreach( $this->settings as $section => $data ) {
						$html .= '<li>| <a class="tab" href="#' . $section . '">' . $data['title'] . '</a></li>' . "\n";
					}

				$html .= '</ul>' . "\n";

				$html .= '<div class="clear"></div>' . "\n";

				// Get settings fields
				ob_start();
				settings_fields( 'wcac_settings' );
				do_settings_sections( 'wcac_settings' );
				$html .= ob_get_clean();
				
				//add test button
				$html .= '
				<script>
				function myFunction() {
					window.open("'.get_option($this->settings_base.'domain_field').'/api/xml?action=login&login='.get_option($this->settings_base.'login_field').'&password='.get_option($this->settings_base.'password_field').'", "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=300, left=300, width=400, height=200");
				}
				</script>
				<hr>
				<p>Once you click the button below a new window will open. If you see <b>&lt;status code="ok"\&gt;</b> then you are all set and login to your AC server was succesful.</p>
				<p>If you see <b>&lt;status code="no-data"/&gt;</b> then your login credentials or the domain are wrong.</p>
				<button type="button" onclick="myFunction();">Test Settings (use after clicking "Save Settings")</button>
				<hr>
				';

				$html .= '<p class="submit">' . "\n";
				$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , 'wcac_plugin_text' ) ) . '" />' . "\n";
				$html .= '</p>' . "\n";
			$html .= '</form>' . "\n";
		$html .= '</div>' . "\n";

		echo $html;
	}
} //end class
?>