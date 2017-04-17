<?php


/**
 * Internationalization
 */
if ( ! function_exists( 'pntrst_loaded' ) ) {
	function pntrst_loaded() {
		load_plugin_textdomain( 'bws-pinterest', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

/* Plugin init function */
if ( ! function_exists ( 'pntrst_init' ) ) {
	function pntrst_init() {
		global $bws_plugin_info, $pntrst_plugin_info;		

		if ( empty( $pntrst_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$pntrst_plugin_info = get_plugin_data( __FILE__ );
		}

		

		/* Call register settings function pntrst_register_settings() */
		if ( ! is_admin() || ( isset( $_GET['page'] ) && ( "pinterest.php" == $_GET['page'] || "social-buttons.php" == $_GET['page'] ) ) ) {
			pntrst_register_settings();
		}
	}
}

/* Function for admin_init */
if ( ! function_exists( 'pntrst_admin_init' ) ) {
	function pntrst_admin_init() {
		/* Add variable for bws_menu */
		global $bws_plugin_info, $pntrst_plugin_info, $bws_shortcode_list;

		
		$bws_shortcode_list['pntrst'] = array( 'name' => 'Pinterest', 'js_function' => 'pntrst_shortcode_init' );
	}
}

/* Enqueue plugin scripts and styles for admin */
if ( ! function_exists ( 'pntrst_enqueue' ) ) {
	function pntrst_enqueue( $hook ) {
		if ( isset( $_GET['page'] ) && ( 'pinterest.php' == $_GET['page'] || 'social-buttons.php' == $_GET['page'] ) ) {
			wp_enqueue_style( 'pntrst_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );
			wp_enqueue_script( 'pntrst_script', plugins_url( 'js/script.js', __FILE__ ) );

			if ( isset( $_GET['action'] ) && 'custom_code' == $_GET['action'] )
				bws_plugins_include_codemirror();
		}

		if ( $hook == 'widgets.php' || $hook == 'customize.php' ) {
			wp_enqueue_script( 'pntrst_script', plugins_url( 'js/script.js', __FILE__ ) );
			wp_enqueue_style( 'pntrst_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );
		}
	}
}

/* Enqueue plugin scripts and styles */
if ( ! function_exists( 'pntrst_script_enqueue' ) ) {
	function pntrst_script_enqueue() {
		wp_enqueue_style( 'pntrst_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );
	}
}

/* Register settings */
if ( ! function_exists( 'pntrst_register_settings' ) ) {
	function pntrst_register_settings() {
		global $pntrst_options, $pntrst_plugin_info, $pntrst_options_defaults;

		$pntrst_options_defaults = array(
			'plugin_option_version'		=> $pntrst_plugin_info["Version"],
			'pinit_before'				=> 1,
			'pinit_after'				=> 0,
			'pinit_hover'				=> 0,
			'pinit_image'				=> 1,
			'pinit_custom_image_link'	=> '',
			'pinit_image_shape'			=> 1,
			'pinit_image_size'			=> 1,
			'pinit_image_color'			=> 'red',
			'pinit_counts'				=> 'none',
			'follow_before'				=> 0,
			'follow_after'				=> 0,
			'follow_button_label'		=> __( 'Follow me', 'bws-pinterest' ),
			'profile_url'				=> '',
			'display_settings_notice'	=> 0,
			'suggest_feature_banner'	=> 1
		);

		/* Install the option defaults */
		if ( ! get_option( 'pntrst_options' ) )
			add_option( 'pntrst_options', $pntrst_options_defaults );

		/* Get options from the database */
		$pntrst_options = get_option( 'pntrst_options' );

		/* Array merge incase this version has added new options */
		if ( ! isset( $pntrst_options['plugin_option_version'] ) || $pntrst_options['plugin_option_version'] != $pntrst_plugin_info["Version"] ) {
			$pntrst_options = array_merge( $pntrst_options_defaults, $pntrst_options );
			$pntrst_options['plugin_option_version'] = $pntrst_plugin_info["Version"];
			update_option( 'pntrst_options', $pntrst_options );
		}
	}
}

/* Plugin's settings page in the admin */
if ( ! function_exists( 'pntrst_settings_page' ) ) {
	function pntrst_settings_page() {
		global $wp_version, $pntrst_options, $pntrst_plugin_info, $pntrst_options_defaults;

		$message = $error = "";
		$plugin_basename = plugin_basename( __FILE__ );

		/* save options */
		if ( isset( $_POST["pntrst_submit"] ) && check_admin_referer( $plugin_basename, 'pntrst_nonce_name' ) ) {
			$pntrst_options['pinit_before'] 		= isset( $_REQUEST['pntrst_before'] ) ? 1 : 0;
			$pntrst_options['pinit_after'] 			= isset( $_REQUEST['pntrst_after'] ) ? 1 : 0;
			$pntrst_options['pinit_hover'] 			= isset( $_REQUEST['pntrst_hover'] ) ? 1 : 0;
			if ( '0' == $_REQUEST['pntrst_image'] || '1' == $_REQUEST['pntrst_image'] )
				$pntrst_options['pinit_image'] = $_REQUEST['pntrst_image'];
			if ( '0' == $_REQUEST['pntrst_image_shape'] || '1' == $_REQUEST['pntrst_image_shape'] )
				$pntrst_options['pinit_image_shape'] = $_REQUEST['pntrst_image_shape'];
			if ( '0' == $_REQUEST['pntrst_image_size'] || '1' == $_REQUEST['pntrst_image_size'] )
				$pntrst_options['pinit_image_size'] = $_REQUEST['pntrst_image_size'];
			if ( 'red' == $_REQUEST['pntrst_image_color'] || 'gray' == $_REQUEST['pntrst_image_color'] || 'white' == $_REQUEST['pntrst_image_color'] )
				$pntrst_options['pinit_image_color'] = $_REQUEST['pntrst_image_color'];
			if ( 'none' == $_REQUEST['pntrst_pin_counts'] || 'above' == $_REQUEST['pntrst_pin_counts'] || 'beside' == $_REQUEST['pntrst_pin_counts'] )
				$pntrst_options['pinit_counts'] = $_REQUEST['pntrst_pin_counts'];
			$pntrst_options['follow_before'] 		= isset( $_REQUEST['pntrst_follow_before'] ) ? 1 : 0;
			$pntrst_options['follow_after'] 		= isset( $_REQUEST['pntrst_follow_after'] ) ? 1 : 0;
			$pntrst_options['follow_button_label'] 	= sanitize_text_field( $_REQUEST['pntrst_follow_button_label'] );
			$pntrst_options['profile_url'] 			= sanitize_text_field( str_replace( '/', '', $_REQUEST['pntrst_profile_url'] ) );

			if ( ( 1 == $pntrst_options['follow_before'] || 1 == $pntrst_options['follow_after'] ) && empty( $pntrst_options['profile_url'] ) )
				$error = __( 'Please, enter "Pinterest user URL" to add Follow Button. Settings are not saved.', 'bws-pinterest' );

			/* file upload script */
			if ( isset( $_FILES['pntrst-custom-image']['tmp_name'] ) &&  "" != $_FILES['pntrst-custom-image']['tmp_name'] ) {
				$upload_dir = wp_upload_dir();
				if ( ! $upload_dir["error"] ) {
					/* create image directory in WP /uploads */
					$pntrst_custom_img_folder = $upload_dir['basedir'] . '/pinterest-image';
					if ( ! is_dir( $pntrst_custom_img_folder ) ) {
						wp_mkdir_p( $pntrst_custom_img_folder, 0755 );
					}
				}

				/* Checks is file download initiated by user */
				if ( isset( $_FILES['pntrst-custom-image'] ) && '0' == $_REQUEST['pntrst_image'] ) {
					if ( is_uploaded_file( $_FILES['pntrst-custom-image']['tmp_name'] ) ) {
						$filename = $_FILES['pntrst-custom-image']['tmp_name'];
						$ext = substr( $_FILES['pntrst-custom-image']['name'], 1 + strrpos( $_FILES['pntrst-custom-image']['name'], '.' ) );
						$max_image_size = 512 * 1024;
						$valid_types = array( 'jpg', 'jpeg', 'png' );
						/*check if valid file size */
						if ( filesize( $filename ) > $max_image_size ) {
							$error = sprintf( __( 'Error: File size %1s', 'bws-pinterest' ), '> 512Kb' );
						/*check if valid file type */
						} elseif ( ! in_array( strtolower( $ext ), $valid_types ) ) {
							$error = __( 'Error: Invalid file type', 'bws-pinterest' );
						} else {
							/* Construction to rename downloading file */
							$file_ext = wp_check_filetype( $_FILES['pntrst-custom-image']['name'] );
							$new_name = 'pinterest-button';
							$namefile = $new_name . '.' . $file_ext['ext'];
							$uploadfile = $pntrst_custom_img_folder . '/' . $namefile;
							/* Move uploaded file to image directory /uploads/pinterest-image */
							if ( move_uploaded_file( $_FILES['pntrst-custom-image']['tmp_name'], $uploadfile ) ) {
								/* link to uploaded file */
								$pntrst_options['pinit_custom_image_link'] = $upload_dir['baseurl'] . '/pinterest-image/pinterest-button' . '.' . $file_ext['ext'];
							} else {
								$error = __( 'Error: moving file failed', 'bws-pinterest' );
							}
						}
					}
				}
			} elseif ( isset( $_FILES['pntrst-custom-image']['tmp_name'] ) && "" == $_FILES['pntrst-custom-image']['tmp_name'] && "" == $pntrst_options['pinit_custom_image_link'] ) {
				$error = __( 'Error: select the upload file', 'bws-pinterest' );
			}
			if ( empty( $error ) ) {
				$pntrst_options = apply_filters( 'pntrst_before_save_options', $pntrst_options );
				update_option( 'pntrst_options', $pntrst_options );
				$message = __( 'Settings saved', 'bws-pinterest' );
			}
		}
		
		?>
		
			<?php if ( ! empty( $message ) ) { ?>
				<div  class="updated fade below-h2"><p><strong><?php echo $message; ?></strong></p></div>
			<?php }
			if ( ! empty( $error ) ) { ?>
				<div class="error below-h2"><p><strong><?php echo $error; ?></strong></p></div>
			<?php }
			bws_show_settings_notice();
			?>
					<br />
					<div>
						<?php printf(
							__( 'If you would like to add Pinterest buttons or widgets to your page or post, please use %s button', 'bws-pinterest' ),
							'<span class="bws_code"><span class="bwsicons bwsicons-shortcode"></span></span>' ); ?>
						<div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help">
							<div class="bws_hidden_help_text" style="min-width: 200px;">
								<?php printf(
									__( "You can add buttons or widgets to your content by clicking on %1s button in the content edit block using the Visual mode. If the button isn't displayed, please use the shortcode %2s for Pin It button, %3s for Follow button or %4s for Pinteresr widget", 'bws-pinterest' ),
										'<span class="bws_code"><span class="bwsicons bwsicons-shortcode"></span></span>',
										'<span class="bws_code">[bws_pinterest_pin_it]</span>',
										'<span class="bws_code">[bws_pinterest_follow]</span>',
										'<span class="bws_code">[bws_pinterest_widget]</span>'
								); ?>
							</div>
						</div>
					</div>
					<form  class="bws_form" name="pntrst_form" enctype="multipart/form-data" method="post" action="">
						<table class="form-table">
							<tr class="pntrst-profile-url">
								<th scope="row"><?php _e( 'Pinterest user URL', 'bws-pinterest' ); ?></th>
								<td>
									<label>https://www.pinterest.com/<input name="pntrst_profile_url" type="text" maxlength="250" value="<?php echo $pntrst_options['profile_url']; ?>"></label>
									<div class="bws_help_box dashicons dashicons-editor-help">
										<div class="bws_hidden_help_text" style="min-width: 200px;">
											<?php _e( 'Example', 'bws-pinterest' ); ?> : https://www.pinterest.com/pinterest/
										</div>
									</div>
								</td>
							</tr>
							<?php do_action( 'pntrst_settings_page_action', $pntrst_options ); ?>
						</table>
						<hr />
						<h3><?php _e( 'Settings for Pin It Button', 'bws-pinterest' ); ?></h3>
						<!--I. Pin it button -->
						<table class="form-table">
							<!--1. Place (Checkbox field) -->
							<tr>
								<th scope="row"><?php _e( 'Pin It Button place', 'bws-pinterest' ); ?></th>
								<td>
									<fieldset>
										<label><input name="pntrst_before" type="checkbox" value="1" <?php if ( 1 == $pntrst_options['pinit_before'] ) echo 'checked="checked"'; ?> /><?php _e( 'Before the content', 'bws-pinterest' ); ?></label><br />
										<label><input name="pntrst_after" type="checkbox" value="1" <?php if ( 1 == $pntrst_options['pinit_after'] ) echo 'checked="checked"'; ?> /><?php _e( 'After the content', 'bws-pinterest' ); ?></label><br />
										<label><input name="pntrst_hover" type="checkbox" value="1" <?php if ( 1 == $pntrst_options['pinit_hover'] ) echo 'checked="checked"'; ?> /><?php _e( 'On image hover', 'bws-pinterest' ); ?></label><br />
									</fieldset>
								</td>
							</tr>
							<!-- 2. Image type (Radio) -->
							<tr class="pntrst-image">
								<th scope="row"><?php _e( 'Button image type', 'bws-pinterest' ); ?></th>
								<td>
									<fieldset>
										<label><input id="pntrst_image_default"name="pntrst_image" type="radio" value="1" <?php if ( 1 == $pntrst_options['pinit_image'] ) echo 'checked="checked"'; ?> /><?php _e( 'Default', 'bws-pinterest' ); ?></label><br />
										<label><input id="pntrst_image_custom" name="pntrst_image" type="radio" value="0" <?php if ( 0 == $pntrst_options['pinit_image'] ) echo 'checked="checked"'; ?> /><?php _e( 'Custom', 'bws-pinterest' ); ?></label><br/>
										<div class="pntrst-custom-button">
											<input id="pntrst-custom-image" name="pntrst-custom-image" type="file">
											<div class="bws_help_box dashicons dashicons-editor-help">
												<div class="bws_hidden_help_text" style="min-width: 210px;">
													<?php printf( 
														__( 'Max image size: %1s. Allowed file extensions: %2s' , 'bws-pinterest' ),
															'512Kb',
															'jpg, jpeg, png'
													); ?>
												</div>
											</div><br />
											<?php if ( ! empty ( $pntrst_options['pinit_custom_image_link'] ) ) { ?>
											<p><?php _e( 'Current custom image', 'bws-pinterest' ); ?>:</p><img width="50" src="<?php echo esc_url( $pntrst_options['pinit_custom_image_link'] ); ?>" />
											<?php } ?>
										</div>
									</fieldset>
								</td>
							</tr>
							<!--3. Image form (Radio) -->
							<tr class="pntrst-image-shape">
								<th scope="row"><?php _e( 'Button Shape', 'bws-pinterest' ); ?></th>
								<td>
									<fieldset>
										<label><input name="pntrst_image_shape" type="radio" value="1" <?php if ( 1 == $pntrst_options['pinit_image_shape'] ) echo 'checked="checked"'; ?> /><?php _e( 'Square', 'bws-pinterest' ); ?></label><br />
										<label><input name="pntrst_image_shape" type="radio" value="0" <?php if ( 0 == $pntrst_options['pinit_image_shape'] ) echo 'checked="checked"'; ?> /><?php _e( 'Round', 'bws-pinterest' ); ?></label>
									</fieldset>
								</td>
							</tr>
							<!--4. Image size (Radio)-->
							<tr class="pntrst-image-size">
								<th scope="row"><?php _e( 'Button Size', 'bws-pinterest' ); ?></th>
								<td>
									<fieldset>
										<label><input name="pntrst_image_size" type="radio" value="1" <?php if ( 1 == $pntrst_options['pinit_image_size'] ) echo 'checked="checked"'; ?> /><?php _e( 'Small', 'bws-pinterest' ); ?></label><br />
										<label><input name="pntrst_image_size" type="radio" value="0" <?php if ( 0 == $pntrst_options['pinit_image_size'] ) echo 'checked="checked"'; ?> /><?php _e( 'Large', 'bws-pinterest' ); ?></label>
									</fieldset>
								</td>
							</tr>
							<!--5. Image color (Selectbox)-->
							<tr class="pntrst-image-color">
								<th scope="row"><?php _e( 'Button Color', 'bws-pinterest' ); ?></th>
								<td>
									<select name="pntrst_image_color">
										<option value="red" <?php if ( 'red' == $pntrst_options['pinit_image_color']  ) echo 'selected="selected"'; ?> ><?php _e( 'Red', 'bws-pinterest' ); ?></option>
										<option value="gray" <?php if ( 'gray' == $pntrst_options['pinit_image_color']  ) echo 'selected="selected"'; ?> ><?php _e( 'Gray', 'bws-pinterest' ); ?></option>
										<option value="white" <?php if ( 'white' == $pntrst_options['pinit_image_color']  ) echo 'selected="selected"'; ?> ><?php _e( 'White', 'bws-pinterest' ); ?></option>
									</select>
									<p class="description hide-if-js"><?php _e( 'Works only if the square image is chosen', 'bws-pinterest' ); ?>.</p>
								</td>
							</tr>
							<!--6. Pin Counts (Selectbox)-->
							<tr class="pntrst-pin-counts">
								<th scope="row"><?php _e( 'Pin Counts', 'bws-pinterest' ); ?></th>
								<td>
									<select name="pntrst_pin_counts">
										<option value="none" <?php if ( 'none' == $pntrst_options['pinit_counts']  ) echo 'selected="selected"'; ?> ><?php _e( 'None', 'bws-pinterest' ); ?></option>
										<option value="above" <?php if ( 'above' == $pntrst_options['pinit_counts']  ) echo 'selected="selected"'; ?> ><?php _e( 'Above the button', 'bws-pinterest' ); ?></option>
										<option value="beside" <?php if ( 'beside' == $pntrst_options['pinit_counts']  ) echo 'selected="selected"'; ?> ><?php _e( 'Beside the button', 'bws-pinterest' ); ?></option>
									</select>
									<p class="description hide-if-js"><?php _e( 'Works only if the square image is chosen', 'bws-pinterest' ); ?>.</p>
								</td>
							</tr>
						</table>
						<hr />
						<!-- II. Follow button -->
						<h3><?php _e( 'Settings for Follow Button', 'bws-pinterest' ); ?></h3>
						<table class="form-table">
							<!-- 1. Place (Checkbox field)-->
							<tr>
								<th scope="row"><?php _e( 'Follow Button place', 'bws-pinterest' ); ?></th>
								<td>
									<fieldset>
										<label><input name="pntrst_follow_before" type="checkbox" value="0" <?php if ( 1 == $pntrst_options['follow_before'] ) echo 'checked="checked"'; ?> /><?php _e( 'Before the content', 'bws-pinterest' ); ?></label><br />
										<label><input name="pntrst_follow_after" type="checkbox" value="1" <?php if ( 1 == $pntrst_options['follow_after'] ) echo 'checked="checked"'; ?> /><?php _e( 'After the content', 'bws-pinterest' ); ?></label>
									</fieldset>
								</td>
							</tr>
							<!--2. Button text (Text field)-->
							<tr class="pntrst-follow-button-label">
								<th scope="row"><?php _e( 'Follow Button label', 'bws-pinterest' ); ?></th>
								<td>
									<input name="pntrst_follow_button_label" type="text" size="30" maxlength="250" value="<?php echo $pntrst_options['follow_button_label']; ?>">
								</td>
							</tr>
						</table>
						<p class="submit">
							<input type="hidden" name="pntrst_submit" value="submit">
							<input id="bws-submit-button" type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'bws-pinterest' ); ?>" />
							<?php wp_nonce_field( $plugin_basename, 'pntrst_nonce_name' ); ?>
						</p>
					</form>
					
	<?php }
}

/* Add pinterest.js */
if ( ! function_exists( 'pntrst_pinit_js_config' ) ) {
	function pntrst_pinit_js_config() {
		global $pntrst_options;
		$return_string = "async";

		/* check if custom image is chosen and load pntrst_custom_hover_img_script */
		if ( "0" !== $pntrst_options['pinit_image'] && "1" !== $pntrst_options['pinit_hover'] ) {
			/* if image hover is enabled, append the data-pin-hover attribute */
			if ( 1 == $pntrst_options['pinit_hover'] )
				$return_string .= ' data-pin-hover="true"';
			/* button shape */
			if ( 0 == $pntrst_options['pinit_image_shape'] )
				$return_string .= ' data-pin-round="true"';
			/* button size */
			if ( 0 == $pntrst_options['pinit_image_size'] )
				$return_string .= ' data-pin-tall="true"';
			/* if image shape square */
			if ( 1 == $pntrst_options['pinit_image_shape'] ) {
				if ( isset( $pntrst_options['pinit_image_color'] ) )
					$return_string .= ' data-pin-color="' . $pntrst_options['pinit_image_color'] . '"';

				if ( isset( $pntrst_options['pinit_counts'] ) )
					$return_string .= ' data-pin-count="' . $pntrst_options['pinit_counts'] . '"';
			}
		} ?>
		<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js" <?php echo $return_string; ?>></script>
	<?php }
}

if ( ! function_exists( 'pntrst_custom_hover_img_script' ) ) {
	function pntrst_custom_hover_img_script () {
		global $pntrst_options; 
		if ( "0" == $pntrst_options['pinit_image'] && "1" == $pntrst_options['pinit_hover'] ) { ?>
			<script id='bws-custom-hover-js' src="<?php echo plugins_url( 'js/custom_hover.js', __FILE__ ) ?>" data-custom-button-image="<?php echo $pntrst_options['pinit_custom_image_link'] ?>" async type='text/javascript'></script>
		<?php } elseif ( "1" == $pntrst_options['pinit_hover'] ) { ?>
			<script type="text/javascript">
				(function($){
					$(document).ready( function() {
						$( '#fancybox-outer' ).hover( function(){ 
							$( 'body' ).find( '#fancybox-img' ).attr( 'data-pin-no-hover', '1' );
						});				
					});
				})(jQuery);
			</script>
		<?php }
	}
}

/* Function for display plugin frontend */
if ( ! function_exists( 'pntrst_frontend' ) ) {
	function pntrst_frontend( $content ) {
		global $pntrst_options;
		/* Check if custom image. */
		$before = $after = '';
		if ( ! is_feed() ) {
			if ( 0 == $pntrst_options['pinit_image'] ) {
				$custom = 'true';
				$img_link = $pntrst_options['pinit_custom_image_link'];
			} else {
				$custom = $img_link = '';
			}
			$pinit_code = '<div class="pntrst-button-wrap">
							<a data-pin-do="buttonBookmark" data-pin-custom="' . $custom . '" href="https://www.pinterest.com/pin/create/button/"><img data-pin-nopin="1" class="pntrst-custom-pin" src="' . esc_url( $img_link ). '" width="60"></a>
						</div>';
			$follow_code = '<div class="pntrst-button-wrap">
							<a data-pin-do="buttonFollow" href="https://www.pinterest.com/' . esc_attr( $pntrst_options['profile_url'] ) . '/">' . esc_html( $pntrst_options['follow_button_label'] ) . '</a>
						</div>';
			/* Check which buttons display before content */
			if ( 1 == $pntrst_options['pinit_before'] )
				$before .= $pinit_code;
			if ( 1 == $pntrst_options['follow_before'] )
				$before .= $follow_code;

			/* Check which buttons display after content */
			if ( 1 == $pntrst_options['pinit_after'] )
				$after .= $pinit_code;
			if ( 1 == $pntrst_options['follow_after'] )
				$after .= $follow_code;

			$before = apply_filters( 'pntrst_button_in_the_content', $before );
			$after = apply_filters( 'pntrst_button_in_the_content', $after );
		}
		return $before . $content . $after;
	}
}

if ( ! function_exists( 'pntrst_pagination_callback' ) ) {
	function pntrst_pagination_callback( $content ) {
		$content .= "if ( typeof( PinUtils ) != 'undefined' ) { PinUtils.build(); }";
		return $content;
	}
}

/**
 * Shortcodes
 *
 * Create shortcodes for displaying Pinterest buttons.
 */
 /* Function which create shortcode for Pin It button */
 if ( ! function_exists( 'pntrst_pin_it_shortcode' ) ) {
	function pntrst_pin_it_shortcode( $atts ) {
		global $pntrst_options;
		$pin_it_atts = shortcode_atts( array(
			'type' 		=> 'any',
			'image_url' => '',
			'custom' 	=> '',
			'url' 		=> $pntrst_options['pinit_custom_image_link'],
		), $atts );

		if ( 'any' == $pin_it_atts['type'] ) {
			return '<div class="pntrst-button-wrap">
								<a data-pin-do="buttonBookmark" data-pin-custom="' . esc_html( $pin_it_atts['custom'] ) . '"  href="https://www.pinterest.com/pin/create/button/"><img data-pin-nopin="1"  class="pntrst-custom-pin" src="' . esc_url( $pin_it_atts['url'] ) . '" width="60"></a>
							</div>';
		} elseif ( 'one' == $pin_it_atts['type'] ) {
			return '<div class="pntrst-button-wrap">
								<a data-pin-do="buttonPin" data-pin-media="' . esc_url( $pin_it_atts['image_url'] ) . '" data-pin-custom="' . esc_html( $pin_it_atts['custom'] ) . '"  href="https://www.pinterest.com/pin/create/button/"><img data-pin-nopin="1"  class="pntrst-custom-pin" src="' . esc_url( $pin_it_atts['url'] ) . '" width="60"></a>
							</div>';
		}
	}
}

/* Function which create shortcode for Pinterest Follow button */
 if ( ! function_exists( 'pntrst_pin_follow_shortcode' ) ) {
	function pntrst_pin_follow_shortcode( $atts ) {
		global $pntrst_options;
		$pin_follow_atts = shortcode_atts( array(
			'label' => $pntrst_options['follow_button_label'],
		), $atts );

		return '<div class="pntrst-button-wrap">
							<a data-pin-do="buttonFollow" href="https://www.pinterest.com/' . esc_attr( $pntrst_options['profile_url'] ) . '/">' . esc_html( $pin_follow_atts['label'] ) . '</a>
						</div>';
	}
}

/**
 * Pinterest Widget
 *
 * Widget that allows users to add Pinterest Pin, Board and Profile widgets on their site.
 */
if ( ! class_exists( 'Pinterest_Widget' ) ) {
	class Pinterest_Widget extends WP_Widget {
		/**
		 * Sets up the widget name etc
		 */
		function __construct() {
			/* widget actual processes */
			parent::__construct(
				/*id*/
				'pntrst-widget',
				/*name*/
				__( 'Pinterest Widget', 'bws-pinterest' ),
				/* Widget description */
				array(
					'description' => __( 'Widget for adding Pinterest Pin, Board and Profile widgets', 'bws-pinterest' ),  /*description displayed in admin */
				)
			);
		}
		/**
		 * Outputs the content of the widget
		 */
		function widget( $args, $instance ) {			
			global $pntrst_options;		
			if ( empty( $pntrst_options ) )
				$pntrst_options = get_option( 'pntrst_options' );	

			$title 					= empty( $instance['pntrst_title'] ) ? '' : apply_filters( 'widget_title', $instance['pntrst_title'], $instance, $this->id_base );
			$widget_type 			= empty( $instance['pntrst_widget_type'] ) ? '' : $instance['pntrst_widget_type'];
			$widget_url 			= empty( $instance['pntrst_widget_url'] ) ? '' : $instance['pntrst_widget_url'];
			$pin_widget_size 		= empty( $instance['pntrst_pin_widget_size'] ) ? '' : $instance['pntrst_pin_widget_size'];
			$widget_width 			= empty( $instance['pntrst_widget_width'] ) ? '' : $instance['pntrst_widget_width'];
			$widget_height 			= empty( $instance['pntrst_widget_height'] ) ? '' : $instance['pntrst_widget_height'];
			$widget_pin_scale 		= empty( $instance['pntrst_widget_pin_scale'] ) ? '' : $instance['pntrst_widget_pin_scale'];
			/* before and after widget arguments are defined by themes */
			echo $args['before_widget'];
			if ( ! empty( $title ) ) {
				echo $args['before_title'] . $title . $args['after_title'];
			} ?>
			<div class="pntrst-widget">
				<?php if ( 'embedPin' == $widget_type ) { ?>
					<a data-pin-do="<?php echo $widget_type ?>" data-pin-width="<?php echo $pin_widget_size; ?>" href="<?php echo esc_url( $widget_url ); ?>"></a>
				<?php } elseif ( 'embedBoard' == $widget_type ) { ?>
					<a data-pin-do="<?php echo $widget_type ?>" data-pin-board-width="<?php echo esc_attr( $widget_width ); ?>" data-pin-scale-height="<?php echo esc_attr( $widget_height ); ?>" data-pin-scale-width="<?php echo esc_attr( $widget_pin_scale ); ?>" href="<?php echo esc_url( $widget_url ); ?>"></a>
				<?php } elseif ( 'embedUser' == $widget_type ) { ?>
					<a data-pin-do="<?php echo $widget_type ?>" data-pin-board-width="<?php echo esc_attr( $widget_width ); ?>" data-pin-scale-height="<?php echo esc_attr( $widget_height ); ?>" data-pin-scale-width="<?php echo esc_attr( $widget_pin_scale ); ?>" href="https://www.pinterest.com/<?php echo esc_attr( $pntrst_options['profile_url'] ); ?>"></a>
				<?php } ?>
			</div>
			<?php echo $args['after_widget'];
		}
		/**
		 * Outputs the options form on admin
		 *
		 * The widget options.
		 */
		function form( $instance ) {
			global $pntrst_options;
			if ( empty( $pntrst_options ) )
				$pntrst_options = get_option( 'pntrst_options' );
			/* outputs the options form on admin */
			$instance = wp_parse_args( (array) $instance,
				array(
					'pntrst_title'				=> '',
					'pntrst_widget_type'		=> 'embedPin',
					'pntrst_widget_url'			=> '',
					'pntrst_pin_widget_size'	=> '',
					'pntrst_widget_width'		=> '',
					'pntrst_widget_height'		=> '',
					'pntrst_widget_pin_scale'	=> '',
				)
			);
			$pntrst_title = esc_attr( $instance['pntrst_title'] );
			$widget_type = esc_attr( $instance['pntrst_widget_type'] );
			$widget_url = esc_attr( $instance['pntrst_widget_url'] );
			$pin_widget_size = esc_attr( $instance['pntrst_pin_widget_size'] );
			$widget_width = esc_attr( $instance['pntrst_widget_width'] );
			$widget_height = esc_attr( $instance['pntrst_widget_height'] );
			$widget_pin_scale = esc_attr( $instance['pntrst_widget_pin_scale'] ); ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'pntrst_title' ); ?>"><?php _e( 'Title', 'bws-pinterest' ); ?>:</label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'pntrst_title' ); ?>" name="<?php echo $this->get_field_name( 'pntrst_title' ); ?>" type="text" value="<?php echo esc_attr( $pntrst_title ); ?>" />
			</p>
			<div>
				<label for="<?php echo $this->get_field_id( 'pntrst_widget_type' ); ?>">
					<?php _e( 'Type', 'bws-pinterest' ); ?>:
					<select id="<?php echo $this->get_field_id( 'pntrst_widget_type' ); ?>" class="pntrst-widget-type" name="<?php echo $this->get_field_name( 'pntrst_widget_type' ); ?>">
						<option value="embedPin"<?php if ( 'embedPin' == $widget_type ) echo 'selected="selected"'; ?>><?php _e( 'Pin Widget', 'bws-pinterest' ) ?></option>
						<option value="embedBoard"<?php if ( 'embedBoard' == $widget_type ) echo 'selected="selected"'; ?>><?php _e( 'Board Widget', 'bws-pinterest' ) ?></option>
						<option value="embedUser"<?php if ( 'embedUser' == $widget_type ) echo 'selected="selected"'; ?>><?php _e( 'Profile Widget', 'bws-pinterest' ) ?></option>
					</select>
				</label>
				<div class="pntrst-widget-admin pntrst-widget-url <?php if ( 'embedUser' == $widget_type ) echo 'hidden'; ?>">
					<label for="<?php echo $this->get_field_id( 'pntrst_widget_url' ); ?>"><?php _e( 'URL', 'bws-pinterest' ); ?>*:</label>
					<div class="bws_help_box dashicons dashicons-editor-help">
						<div class="bws_hidden_help_text" style="min-width: 200px;">
							<?php  _e( 'Examples', 'bws-pinterest' ); ?>:<br />
							<?php  _e( 'Pin Widget', 'bws-pinterest' ); ?>: https://www.pinterest.com/pin/99360735500167749/<br />
							<?php  _e( 'Board Widget', 'bws-pinterest' ); ?>: https://www.pinterest.com/pinterest/official-news/
						</div>
					</div>
					<input class="widefat" id="<?php echo $this->get_field_id( 'pntrst_widget_url' ); ?>" name="<?php echo $this->get_field_name( 'pntrst_widget_url' ); ?>" type="url" value="<?php echo esc_url( $widget_url ); ?>" />
				</div>
				<div class="pntrst-widget-admin pntrst-pin-widget-size <?php if ( 'embedBoard' == $widget_type || 'embedUser' == $widget_type ) echo 'hidden'; ?>">
					<label for="<?php echo $this->get_field_id( 'pntrst_pin_widget_size' ); ?>"><?php _e( 'Size', 'bws-pinterest' ); ?>:</label>
					<select id="<?php echo $this->get_field_id( 'pntrst_pin_widget_size' ); ?>" name="<?php echo $this->get_field_name( 'pntrst_pin_widget_size' ); ?>">
						<option value="small"<?php selected( $pin_widget_size, 'small' ); ?>><?php _e( 'Small', 'bws-pinterest' ) ?></option>
						<option value="medium"<?php selected( $pin_widget_size, 'medium' ); ?>><?php _e( 'Medium', 'bws-pinterest' ) ?></option>
						<option value="large"<?php selected( $pin_widget_size, 'large' ); ?>><?php _e( 'Large', 'bws-pinterest' ) ?></option>
					</select>
				</div>
				<div class="pntrst-widget-admin pntrst-widget-size <?php if ( 'embedPin' == $widget_type ) echo 'hidden'; ?>">
					<label for="<?php echo $this->get_field_id( 'pntrst_widget_width' ); ?>"><?php _e( 'Width', 'bws-pinterest' ); ?>:</label>
					<div class="bws_help_box dashicons dashicons-editor-help">
						<div class="bws_hidden_help_text" style="min-width: 120px;">
							<?php printf( __( 'Min width%1s. If emtpy width: auto', 'bws-pinterest' ), ': 130px' ); ?>
						</div>
					</div>
					<input class="widefat" id="<?php echo $this->get_field_id( 'pntrst_widget_width' ); ?>" name="<?php echo $this->get_field_name( 'pntrst_widget_width' ); ?>" type="number" min="130" max="10000" value="<?php echo $widget_width; ?>" />
					<em class="description hide-if-js">
						<?php printf( __( 'Min width%1s. If emtpy width: auto', 'bws-pinterest' ), ': 130px' ); ?>
					</em>
					<label for="<?php echo $this->get_field_id( 'pntrst_widget_height' ); ?>"><?php _e( 'Height', 'bws-pinterest' ); ?>:</label>
					<div class="bws_help_box dashicons dashicons-editor-help" >
						<div class="bws_hidden_help_text" style="min-width: 120px;">
							<?php _e( 'Min height', 'bws-pinterest'); ?>: 60px
						</div>
					</div>
					<input class="widefat" id="<?php echo $this->get_field_id( 'pntrst_widget_height' ); ?>" name="<?php echo $this->get_field_name( 'pntrst_widget_height' ); ?>" type="number" min="60" max="10000" value="<?php echo $widget_height; ?>" />
					<em class="description hide-if-js"><?php _e( 'Min height', 'bws-pinterest' ) ?>: 60px.</em>
					<label for="<?php echo $this->get_field_id( 'pntrst_widget_pin_scale' ); ?>"><?php _e( 'Thumbnails width', 'bws-pinterest' ); ?>:</label>
					<div class="bws_help_box dashicons dashicons-editor-help">
						<div class="bws_hidden_help_text" style="min-width: 120px;">
							<?php printf(
								__( 'Min width%1s. If the widget width field is empty, the thumbnail width will be auto.', 'bws-pinterest' ),
								': 60px'
							); ?>
						</div>
					</div>
					<input class="widefat" id="<?php echo $this->get_field_id( 'pntrst_widget_pin_scale' ); ?>" name="<?php echo $this->get_field_name( 'pntrst_widget_pin_scale' ); ?>" type="number" min="60" max="10000" value="<?php echo $widget_pin_scale; ?>" />
					<em class="description hide-if-js">
						<?php printf(
							__( 'Min width%1s. If the widget width field is empty, the thumbnail width will be auto.', 'bws-pinterest' ),
							': 60px'
						); ?>
					</em>
				</div>
			</div>
		<?php }
		/**
		 * Processing widget options on save
		 *
		 * @param array $new_instance The new options.
		 * @param array $old_instance The previous options.
		 */
		function update( $new_instance, $old_instance ) {
			/* processes widget options to be saved */
			$instance = $old_instance;
			/* Fields */
			$instance['pntrst_title'] = sanitize_text_field( $new_instance['pntrst_title'] );
			if ( 'embedPin' == $new_instance['pntrst_widget_type'] || 'embedBoard' == $new_instance['pntrst_widget_type'] || 'embedUser' == $new_instance['pntrst_widget_type'] ) { 
				$instance['pntrst_widget_type'] = $new_instance['pntrst_widget_type'];
			}
			/* Check if user save correct url (starts with https://www.pinterest.com/). Else clean url. */
			if ( false !== strpos( $new_instance['pntrst_widget_url'], 'https://www.pinterest.com/' ) ) {
				$instance['pntrst_widget_url'] = esc_url( $new_instance['pntrst_widget_url'] );
			} else {
				$instance['pntrst_widget_url'] = '';
			}
			/* Check if board or profile widget selected and save widget size options/clean pin widget options. Else clean widget size options and save pin widget options*/
			if ( 'embedPin' !== $new_instance['pntrst_widget_type'] ) {
				/* clean pin widget size option */
				$instance['pntrst_pin_widget_size'] = '';
				/* save board and profile widget size options */
				if ( empty( $new_instance['pntrst_widget_width'] ) ) {
					$instance['pntrst_widget_width'] = '';
				} else {
					$instance['pntrst_widget_width'] = intval( $new_instance['pntrst_widget_width'] ) < 130 ? 130 : intval( $new_instance['pntrst_widget_width'] );
				}
				$instance['pntrst_widget_height'] = intval( $new_instance['pntrst_widget_height'] ) < 60 ? 60 : intval( $new_instance['pntrst_widget_height'] );
				$instance['pntrst_widget_pin_scale'] = intval( $new_instance['pntrst_widget_pin_scale'] ) < 60 ? 60 : intval( $new_instance['pntrst_widget_pin_scale'] );
			} else {
				/* save pin widget size option */
				if ( 'small' == $new_instance['pntrst_pin_widget_size'] || 'medium' == $new_instance['pntrst_pin_widget_size'] || 'large' == $new_instance['pntrst_pin_widget_size'] ) {
					$instance['pntrst_pin_widget_size'] = $new_instance['pntrst_pin_widget_size'];
				}
				/* clean board and profile widget size options */
				$instance['pntrst_widget_width'] = $instance['pntrst_widget_height'] = $instance['pntrst_widget_pin_scale'] = '';
			}
			return $instance;
		}
	}
}

/**
 * Register Widget
 */
 if ( ! function_exists( 'pntrst_widget_register' ) ) {
	function pntrst_widget_register() {
		register_widget( 'Pinterest_Widget' );
	}
}

/* Function which create shortcode for Pinterest widgets */
 if ( ! function_exists( 'pntrst_widget_shortcode' ) ) {
	function pntrst_widget_shortcode( $atts ) {
		global $pntrst_options;
		$pin_widget_atts = shortcode_atts( array(
			'type' =>		'',
			'url' =>		'',
			'size' =>		'small',
			'width' =>		'',
			'height' =>		'175',
			'thumbnail' =>	'92',
		), $atts );
		if ( 'pin' == $pin_widget_atts['type'] ) {
			return '<div class="pntrst-widget-wrap"><a data-pin-do="embedPin" data-pin-width="' . esc_attr( $pin_widget_atts['size'] ) . '" href="' . esc_url( $pin_widget_atts['url'] ) . '"></a></div>';
		} elseif ( 'board' == $pin_widget_atts['type'] ) {
			return '<div class="pntrst-widget-wrap"><a data-pin-do="embedBoard" data-pin-board-width="' . esc_attr( $pin_widget_atts['width'] ) . '" data-pin-scale-height="' . esc_attr( $pin_widget_atts['height'] ) . '" data-pin-scale-width="' . esc_attr( $pin_widget_atts['thumbnail'] ) . '" href="' . esc_url( $pin_widget_atts['url'] ) . '"></a></div>';
		} elseif ( 'profile' == $pin_widget_atts['type'] ) {
			return '<div class="pntrst-widget-wrap"><a data-pin-do="embedUser" data-pin-board-width="' . esc_attr( $pin_widget_atts['width'] ) . '" data-pin-scale-height="' . esc_attr( $pin_widget_atts['height'] ) . '" data-pin-scale-width="' . esc_attr( $pin_widget_atts['thumbnail'] ) . '" href="https://www.pinterest.com/' . esc_attr( $pntrst_options['profile_url'] ) . '/"></a></div>';
		}
	}
}

/* Function which add Pinterest shortcodes in post visual editor */
if ( ! function_exists( 'pntrst_shortcode_content' ) ) {
	function pntrst_shortcode_content( $content ) {
		global $pntrst_options, $wp_version; 
		if ( empty( $pntrst_options ) )
			$pntrst_options = get_option( 'pntrst_options' ); ?>
		<div id="pntrst" style="display:none;">
			<div>
				<?php _e( 'Shortcode Type', 'bws-pinterest' ); ?>:
				<select name="pntrst_shortcode_type">
					<option selected="selected" value="follow"><?php _e( 'Follow Button', 'bws-pinterest' ); ?></option>
					<option value="pin_it"><?php _e( 'Pin It button', 'bws-pinterest' ); ?></option>
					<option value="pin_widget"><?php _e( 'Pin Widget', 'bws-pinterest' ); ?></option>
					<option value="board_widget"><?php _e( 'Board Widget', 'bws-pinterest' ); ?></option>
					<option value="profile_widget"><?php _e( 'Profile Widget', 'bws-pinterest' ); ?></option>
				</select>
			</div>
			<div class="pntrst-follow-shortcode">
				<p><?php _e( 'Button label', 'bws-pinterest' ); ?>:</p>
				<input name="pntrst_follow_label" type="text" maxlength="250" value="<?php echo $pntrst_options['follow_button_label'] ?>" style="padding: 2px; margin: 2px 0; width: 100%;" />
			</div>
			<fieldset class="pntrst-pin-it-shortcode" style="display:none;">
				<label><input checked="checked" name="pntrst_pit_it_type" type="radio" value="any"><?php _e( 'Save any image', 'bws-pinterest' ); ?></label><br/>
				<label><input name="pntrst_pit_it_type" type="radio" value="one"><?php _e( 'Save defined image', 'bws-pinterest' ); ?></label><br/>
				<input name="pntrst_pin_image_url" type="url" value="" placeholder="<?php _e( 'Pin image URL', 'bws-pinterest' ); ?>" style="display:none; padding: 2px; margin: 2px 0; width: 100%;" />
				<label><input name="pntrst_custom_button" type="checkbox" value="0"><?php _e( 'Custom button image', 'bws-pinterest' ); ?></label><br/>
				<input name="pntrst_custom_button_image" type="url" value="" placeholder="<?php _e( 'Custom image URL', 'bws-pinterest' ); ?>" style="display:none; padding: 2px; margin: 2px 0; width: 100%;" />
			</fieldset>
			<fieldset class="pntrst-widget-shortcode" style="display:none;">
				<div id="pntrst-widget-url">
					<label><?php _e( 'URL', 'bws-pinterest' ); ?>* : </label>
					<input name="pntrst_widget_url" type="url" value="" style="padding: 2px; margin: 2px 0; width: 100%;" />
				</div>
				<div id="pntrst_pin_widget_size">
					<label>
						<?php _e( 'Size', 'bws-pinterest' ); ?>: 
						<select name="pntrst_pin_widget_size">
							<option selected="selected" value="small"><?php _e( 'Small', 'bws-pinterest' ); ?></option>
							<option value="medium"><?php _e( 'Medium', 'bws-pinterest' ); ?></option>
							<option value="large"><?php _e( 'Large', 'bws-pinterest' ); ?></option>
						</select>					
					</label>
				</div>
				<div id="pntrst_widget_size" style="display:none;">
					<label><?php _e( 'Width', 'bws-pinterest' ); ?>: <input style="height: auto;" name="pntrst_widget_width" type="number" min="130" max="10000" value="" class="small-text" /> (px)</label><br/>
					<label><?php _e( 'Height', 'bws-pinterest' ); ?>: <input style="height: auto;" name="pntrst_widget_height" type="number" min="60" max="10000" value="60" class="small-text" /> (px)</label><br/>
					<label><?php _e( 'Thumbnails width', 'bws-pinterest' ); ?>: <input style="height: auto;" name="pntrst_widget_thumbnail" type="number" min="60" max="10000" value="60" class="small-text" /> (px)</label>
				</div>				
			</fieldset>
			<input class="bws_default_shortcode" type="hidden" name="default" value="[bws_pinterest_follow label=&quot;<?php echo $pntrst_options['follow_button_label']; ?>&quot;]" />
			<script type="text/javascript">
				function pntrst_shortcode_init() {
					(function($) {
						var current_object = '<?php echo ( $wp_version < 3.9 ) ? "#TB_ajaxContent" : ".mce-reset"; ?>';

						$( current_object + ' #pntrst input, ' + current_object + ' #pntrst select' ).change( function() {
							
							var shortcodeType = $( current_object + ' select[name="pntrst_shortcode_type"] option:selected' ).val();
							
							if ( 'follow' == shortcodeType ) {
								$( current_object + ' .pntrst-follow-shortcode' ).show();
								$( current_object + ' .pntrst-pin-it-shortcode, ' + current_object + ' .pntrst-widget-shortcode' ).hide();
								/* Display follow button shortcode */
								var shortcode = '[bws_pinterest_follow label="' + $( current_object + ' input[name="pntrst_follow_label"]' ).val() + '"]';			
							} else if ( 'pin_it' == shortcodeType ) {
								$( current_object + ' .pntrst-pin-it-shortcode' ).show();
								$( current_object + ' .pntrst-follow-shortcode, ' + current_object + ' .pntrst-widget-shortcode' ).hide();
								/* Display pin it button shortcode */
								var buttonType = $( current_object + ' input[name="pntrst_pit_it_type"]:checked' ).val();		

								if ( $( current_object + ' input[name="pntrst_custom_button"]' ).is( ':checked' ) ) {
									$( current_object + ' input[name="pntrst_custom_button_image"]' ).show();		
									var customButtonImage = $( current_object + ' input[name="pntrst_custom_button_image"]' ).val();
									if ( false === /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test( customButtonImage ) ) {
										customButtonImage = '';
									}
								} else {
									$( current_object + ' input[name="pntrst_custom_button_image"]' ).hide();
								}

								if ( buttonType == 'any' ) {	
									$( current_object + ' input[name="pntrst_pin_image_url"]' ).hide();								
									if ( $( current_object + ' input[name="pntrst_custom_button"]' ).is( ':checked' ) ) {
										if ( customButtonImage.length > 0 ) {
											var shortcode = '[bws_pinterest_pin_it type="' + buttonType + '" custom="true" url="' + customButtonImage + '"]';
										} else {
											var shortcode = '[bws_pinterest_pin_it type="' + buttonType + '" custom="true"]';
										}
									} else {
										var shortcode = '[bws_pinterest_pin_it type="' + buttonType + '"]';
									}
								} else if ( buttonType == 'one' ) {
									$( current_object + ' input[name="pntrst_pin_image_url"]' ).show();
									var pinImageUrl = $( current_object + ' input[name="pntrst_pin_image_url"]' ).val();	
									if ( false === /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test( pinImageUrl ) ) {
										pinImageUrl = '';
									}							
									if ( $( current_object + ' input[name="pntrst_custom_button"]' ).is( ':checked' ) ) {
										if ( customButtonImage.length > 0 ) {
											var shortcode = '[bws_pinterest_pin_it type="' + buttonType + '" image_url="' + pinImageUrl + '" custom="true" url="' + customButtonImage + '"]';
										} else {
											var shortcode = '[bws_pinterest_pin_it type="' + buttonType + '" image_url="' + pinImageUrl + '" custom="true"]';
										}
									} else {
										var shortcode = '[bws_pinterest_pin_it type="' + buttonType + '" image_url="' + pinImageUrl + '"]';
									}
								}								
							} else if ( 'pin_widget' == shortcodeType || 'board_widget' == shortcodeType || 'profile_widget' == shortcodeType ) {
								$( current_object + ' .pntrst-widget-shortcode' ).show();
								$( current_object + ' .pntrst-follow-shortcode, ' + current_object + ' .pntrst-pin-it-shortcode' ).hide();
								
								var widgetWidth = $( current_object + ' input[name="pntrst_widget_width"]' ).val();
								var widgetHeight = $( current_object + ' input[name="pntrst_widget_height"]' ).val();
								var widgetThumbnail = $( current_object + ' input[name="pntrst_widget_thumbnail"]' ).val();

								if ( '' != widgetWidth ) {
									widgetWidth = parseInt( widgetWidth ) < 130 ? 130 : parseInt( widgetWidth );
								}
								widgetHeight = parseInt( widgetHeight ) < 60 ? 60 : parseInt( widgetHeight );
								widgetThumbnail = parseInt( widgetThumbnail ) < 60 ? 60 : parseInt( widgetThumbnail );

								var widgetUrl = $( current_object + ' input[name="pntrst_widget_url"]' ).val();

								if ( false === /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test( widgetUrl ) ) {
									widgetUrl = '';
								}

								if ( 'pin_widget' == shortcodeType ) {
									$( current_object + ' #pntrst_pin_widget_size, ' + current_object + ' #pntrst-widget-url' ).show();
									$( current_object + ' #pntrst_widget_size' ).hide();

									var pinWidgetSize = $( current_object + ' select[name="pntrst_pin_widget_size"] option:selected' ).val();
									var shortcode = '[bws_pinterest_widget type="pin" size="' + pinWidgetSize + '" url="' + widgetUrl + '"]';
								} else if ( 'board_widget' == shortcodeType ) {
									$( current_object + ' #pntrst_pin_widget_size' ).hide();
									$( current_object + ' #pntrst_widget_size, ' + current_object + ' #pntrst-widget-url' ).show();

									if ( '' != widgetWidth || widgetHeight != 60 || widgetThumbnail != 60 ) {
										var shortcode = '[bws_pinterest_widget type="board" width="' + widgetWidth + '" height="' + widgetHeight + '" thumbnail="' + widgetThumbnail + '" url="' + widgetUrl + '"]';
									} else {
										var shortcode = '[bws_pinterest_widget type="board" url="' + widgetUrl + '"]';
									}
								} else {
									$( current_object + ' #pntrst_pin_widget_size, ' + current_object + ' #pntrst-widget-url' ).hide();
									$( current_object + ' #pntrst_widget_size' ).show();

									if ( '' != widgetWidth || widgetHeight != 60 || widgetThumbnail != 60 ) {
										var shortcode = '[bws_pinterest_widget type="profile" width="' + widgetWidth + '" height="' + widgetHeight + '" thumbnail="' + widgetThumbnail + '"]';
									} else {
										var shortcode = '[bws_pinterest_widget type="profile"]';
									}
								}						
							}
							/* Shortcode output */
							$( current_object + ' #bws_shortcode_display' ).text( shortcode );
						});
					})(jQuery);
				}
			</script>
			<div class="clear"></div>
		</div>
	<?php }
}


add_action( 'plugins_loaded', 'pntrst_loaded' );
/* plugin init */
add_action( 'init', 'pntrst_init' );
add_action( 'admin_init', 'pntrst_admin_init' );
/* add Pinterest widget */
add_action( 'widgets_init', 'pntrst_widget_register' );
/* Enqueue plugin scripts and styles for admin */
add_action( 'admin_enqueue_scripts', 'pntrst_enqueue' );
/* Enqueue plugin scripts and styles */
add_action( 'wp_enqueue_scripts', 'pntrst_script_enqueue' );
/* Add pinit.js script with option params */
add_action( 'wp_head', 'pntrst_pinit_js_config' );
/* Load script for custom image on hover */
add_action( 'wp_footer', 'pntrst_custom_hover_img_script' );
/* show buttons on frontend */
add_filter( 'the_content', 'pntrst_frontend' );
add_filter( 'pgntn_callback', 'pntrst_pagination_callback' );

/* add shortcode for Pin It button */
add_shortcode( 'bws_pinterest_pin_it', 'pntrst_pin_it_shortcode' );
/* add shortcode for Pin Follow button */
add_shortcode( 'bws_pinterest_follow', 'pntrst_pin_follow_shortcode' );
/* add shortcode for Pinterest widgets button */
add_shortcode( 'bws_pinterest_widget', 'pntrst_widget_shortcode' );
/* custom filter for bws button in tinyMCE */
add_filter( 'bws_shortcode_button_content', 'pntrst_shortcode_content' );
