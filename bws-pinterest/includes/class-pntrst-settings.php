<?php
/**
 * Displays the content on the plugin settings page
 */

if ( ! class_exists( 'Pntrst_Settings_Tabs' ) ) {
	class Pntrst_Settings_Tabs extends Bws_Settings_Tabs {
		/**
		 * Constructor.
		 *
		 * @access public
		 *
		 * @see Bws_Settings_Tabs::__construct() for more information on default arguments.
		 *
		 * @param string $plugin_basename
		 */
		public function __construct( $plugin_basename ) {
			global $pntrst_options, $pntrst_plugin_info;

			$tabs = array(
				'settings' 		=> array( 'label' => __( 'Settings', 'bws-pinterest' ) ),
				/*pls */
				'display' 		=> array( 'label' => __( 'Display', 'bws-pinterest' ), 'is_pro' => 1 ),
				/* pls*/
				'misc' 			=> array( 'label' => __( 'Misc', 'bws-pinterest' ) ),
				'custom_code' 	=> array( 'label' => __( 'Custom Code', 'bws-pinterest' ) ),
				/*pls */
				'license'		=> array( 'label' => __( 'License Key', 'bws-pinterest' ) )
				/* pls*/
			);

			parent::__construct( array(
				'plugin_basename' 	 => $plugin_basename,
				'plugins_info'		 => $pntrst_plugin_info,
				'prefix' 			 => 'pntrst',
				'default_options' 	 => pntrst_get_options_default(),
				'options' 			 => $pntrst_options,
				'is_network_options' => is_network_admin(),
				'tabs' 				 => $tabs,
                'doc_link'			 => 'https://bestwebsoft.com/documentation/bestwebsofts-pinterest/bestwebsofts-pinterest-user-guide/',
                /*pls */
				'wp_slug'			 => 'bws-pinterest',
				'link_key' 			 => 'f8f97fcf6a752a73595ec494940c4bb8',
				'link_pn' 			 => '547'
				/* pls*/
			) );

			add_action( get_parent_class( $this ) . '_display_custom_messages', array( $this, 'display_custom_messages' ) );
			add_action( get_parent_class( $this ) . '_additional_misc_options', array( $this, 'additional_misc_options' ) );
			add_action( get_parent_class( $this ) . '_display_metabox', array( $this, 'display_metabox' ) );
		}
		/**
		 * Display custom error\message\notice
		 * @access public
		 * @param  $save_results - array with error\message\notice
		 * @return void
		 */
		public function display_custom_messages( $save_results ) {

			$message = '';

			if ( empty( $this->options['pinit_before'] ) && empty( $this->options['pinit_after'] ) && empty( $this->options['pinit_hover'] ) ) {
				$message .= __( '"Save" button location is not selected. The button will be displayed only via shortcode.', 'bws-pinterest' ) . '<br />';
			}

			if ( empty( $this->options['follow_before'] ) && empty( $this->options['follow_after'] ) ) {
				$message .= __( '"Follow" button location is not selected. The button will be displayed only via shortcode.', 'bws-pinterest' );
			} ?>
			<div class="updated bws-notice below-h2" <?php if ( empty( $message ) ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
		<?php }

		/**
		 * Save plugin options to the database
		 * @access public
		 * @param  void
		 * @return array    The action results
		 */
		public function save_options() {
			global $pntrst_lang_codes;

			$message = $notice = $error = '';

			$this->options['pinit_save']				= isset( $_REQUEST['pntrst_save'] ) ? 1 : 0;
			$this->options['pinit_follow']				= isset( $_REQUEST['pntrst_follow'] ) ? 1 : 0;

			if ( ! empty( $_REQUEST['pntrst_save'] ) ) {
				$this->options['pinit_before']			= isset( $_REQUEST['pntrst_before'] ) ? 1 : 0;
				$this->options['pinit_after']			= isset( $_REQUEST['pntrst_after'] ) ? 1 : 0;
				$this->options['pinit_hover']			= isset( $_REQUEST['pntrst_hover'] ) ? 1 : 0;
			} else {
				$this->options['pinit_before']			= 0;
				$this->options['pinit_after']			= 0;
				$this->options['pinit_hover']			= 0;
			}

			$this->options['pinit_image'] = ! empty( $_REQUEST['pntrst_image'] ) ? 1 : 0;
			$this->options['pinit_image_shape'] = ! empty( $_REQUEST['pntrst_image_shape'] ) ? 1 : 0;
			$this->options['pinit_image_size'] = ! empty( $_REQUEST['pntrst_image_size'] ) ? 1 : 0;
			
			if ( isset( $_REQUEST['pntrst_pin_counts'] ) && in_array( $_REQUEST['pntrst_pin_counts'], array( 'none', 'above', 'beside' ) ) ) {
				$this->options['pinit_counts'] = $_REQUEST['pntrst_pin_counts'];
			}

			if ( ! empty( $_REQUEST['pntrst_follow'] ) ) {
				$this->options['follow_before']			= isset( $_REQUEST['pntrst_follow_before'] ) ? 1 : 0;
				$this->options['follow_after']			= isset( $_REQUEST['pntrst_follow_after'] ) ? 1 : 0;
			} else {
				$this->options['follow_before']			= 0;
				$this->options['follow_after']			= 0;
			}

			$this->options['follow_button_label']		= sanitize_text_field( wp_unslash( $_REQUEST['pntrst_follow_button_label'] ) );
			$this->options['profile_url']				= sanitize_text_field( str_replace( '/', '', $_REQUEST['pntrst_profile_url'] ) );
			$this->options['use_multilanguage_locale']	= isset( $_REQUEST['pntrst_use_multilanguage_locale'] ) ? 1 : 0;

			if ( ( ! empty( $this->options['follow_before'] ) || ! empty( $this->options['follow_after'] ) ) && empty( $this->options['profile_url'] ) ) {
				$error = __( 'Please, enter "Pinterest User ID" to add Follow Button. Settings are not saved.', 'bws-pinterest' );
			}

			if ( ! empty( $_REQUEST['pntrst_lang'] ) && array_key_exists( $_REQUEST['pntrst_lang'], $pntrst_lang_codes ) ) {
				$this->options['lang'] = $_REQUEST['pntrst_lang'];
			}

			if ( ! empty( $_FILES['pntrst-custom-image']['tmp_name'] ) ) {
				$upload_dir = wp_upload_dir();

				if ( false == $upload_dir["error"] ) {
					/* create image directory in WP /uploads */
					$pntrst_custom_img_folder = $upload_dir['basedir'] . '/pinterest-image';
					if ( ( is_dir( $pntrst_custom_img_folder ) || wp_mkdir_p( $pntrst_custom_img_folder, 0755 ) ) && isset( $_FILES['pntrst-custom-image'] ) && empty( $_REQUEST['pntrst_image'] ) && is_uploaded_file( $_FILES['pntrst-custom-image']['tmp_name'] ) ) {

							$ext = substr( $_FILES['pntrst-custom-image']['name'], 1 + strrpos( $_FILES['pntrst-custom-image']['name'], '.' ) );
							$max_image_size = 512 * 1024;
							$valid_types = array( 'jpg', 'jpeg', 'png' );
							/*check if valid file size */
							if ( filesize( $_FILES['pntrst-custom-image']['tmp_name'] ) > $max_image_size ) {
								$error = sprintf( __( 'Error: File size %1s', 'bws-pinterest-pro' ), '> 512Kb' );
							/*check if valid file type */
							} elseif ( ! in_array( strtolower( $ext ), $valid_types ) ) {
								$error = __( 'Error: Invalid file type', 'bws-pinterest-pro' );
							} else {
								/* Construction to rename downloading file */
								$file_ext = wp_check_filetype( $_FILES['pntrst-custom-image']['name'] );
								$new_name = 'pinterest-button';
								$namefile = $new_name . '.' . $file_ext['ext'];
								$uploadfile = $pntrst_custom_img_folder . '/' . $namefile;
								/* Move uploaded file to image directory /uploads/pinterest-image */
								if ( move_uploaded_file( $_FILES['pntrst-custom-image']['tmp_name'], $uploadfile ) ) {
									/* link to uploaded file */
									$this->options['pinit_custom_image_link'] = $upload_dir['baseurl'] . '/pinterest-image/pinterest-button' . '.' . $file_ext['ext'];
								} else {
									$error = __( 'Error: moving file failed', 'bws-pinterest-pro' );
								}
							}
					}
				} else {
					$error = __( "Can't find upload directory path. Settings are not saved.", 'bws-pinterest-pro' );
				}
			} elseif ( isset( $_FILES['pntrst-custom-image']['tmp_name'] ) && empty( $_FILES['pntrst-custom-image']['tmp_name'] ) && empty( $this->options['pinit_custom_image_link'] ) ) {
				$error = __( 'Error: select the upload file', 'bws-pinterest-pro' );
			}

			$this->options = apply_filters( 'pntrst_before_save_options', $this->options );
			if ( empty( $error ) ) {
				/* Update options in the database */
				update_option( 'pntrst_options', $this->options );
				$message = __( 'Settings saved', 'bws-pinterest' );
			}

			return compact( 'message', 'notice', 'error' );
		}

		/**
		 *
		 */
		public function tab_settings() {
			global $pntrst_lang_codes, $wp_version;

			if ( ! function_exists( 'get_plugins' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			$all_plugins = get_plugins(); ?>
			<h3 class="bws_tab_label"><?php _e( 'Pinterest Settings', 'bws-pinterest' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr />
			<div class="bws_tab_sub_label"><?php _e( 'General', 'bws-pinterest' ); ?></div>
			<table class="form-table pntrst_settings_form">
				<tr class="pntrst-profile-url">
					<th scope="row"><?php _e( 'Pinterest User ID', 'bws-pinterest' ); ?></th>
					<td>
						<input name="pntrst_profile_url" type="text" maxlength="250" value="<?php echo $this->options['profile_url']; ?>">
						<div class="bws_info"><?php _e( 'Enter your Pinterest user ID. For example, "bestwebsoft".', 'bws-pinterest' ); ?></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Buttons', 'bws-pinterest' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="checkbox" name="pntrst_save" value="1" <?php checked( 1, $this->options['pinit_save'] ); ?> />
								<?php _e( 'Save', 'bws-pinterest' ); ?>
							</label>
							<br />
							<label>
								<input type="checkbox" name="pntrst_follow" value="1" <?php checked( 1, $this->options['pinit_follow'] ); ?> />
								<?php _e( 'Follow', 'bws-pinterest' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Language', 'bws-pinterest' ); ?></th>
					<td>
						<select name="pntrst_lang">
							<?php foreach ( $pntrst_lang_codes as $key => $value ) {
								echo '<option value="' . $key . '"';
								if ( $key == $this->options['lang'] ) {
									echo 'selected="selected"';
								}
								echo '>' . esc_html( $value ) . '</option>';
							} ?>
						</select>
						<div class="bws_info"><?php _e( 'Select the default language for Pinterest button(-s).', 'bws-pinterest' ); ?></div>
					</td>
				</tr>
				<tr>
					<th>Multilanguage</th>
					<td>
						<?php if ( array_key_exists( 'multilanguage/multilanguage.php', $all_plugins ) || array_key_exists( 'multilanguage-pro/multilanguage-pro.php', $all_plugins ) ) {
							if ( is_plugin_active( 'multilanguage/multilanguage.php' ) || is_plugin_active( 'multilanguage-pro/multilanguage-pro.php' ) ) { ?>
								<label>
									<input type="checkbox" name="pntrst_use_multilanguage_locale" value="1" <?php checked( 1, $this->options["use_multilanguage_locale"] ); ?> />
									<span class="bws_info"><?php _e( 'Enable to switch language automatically on multilingual website using Multilanguage plugin.', 'bws-pinterest' ); ?></span>
								</label>
							<?php } else { ?>
								<input disabled="disabled" type="checkbox" name="pntrst_use_multilanguage_locale" value="1" />
								<span class="bws_info"><?php _e( 'Enable to switch language automatically on multilingual website using Multilanguage plugin.', 'bws-pinterest' ); ?> <a href="<?php echo bloginfo( "url" ); ?>/wp-admin/plugins.php"><?php printf( __( 'Activate %s', 'bws-pinterest' ), 'Multilanguage' ); ?></a></span>
							<?php }
						} else { ?>
							<input disabled="disabled" type="checkbox" name="pntrst_use_multilanguage_locale" value="1" />
							<span class="bws_info"><?php _e( 'Enable to switch language automatically on multilingual website using Multilanguage plugin.', 'bws-pinterest' ); ?> <a href="https://bestwebsoft.com/products/wordpress/plugins/multilanguage/?k=19400c9ffca0daf047c13bb267ed989b&pn=547&v=<?php echo $this->plugins_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>"><?php _e( 'Learn More', 'bws-pinterest' ); ?></a></span>
						<?php } ?>
					</td>
				</tr>
				<?php do_action( 'pntrst_settings_page_action', $this->options ); ?>
			</table>

			<div class="bws_tab_sub_label pntrst_save_enabled"><?php _e( 'Save Button', 'bws-pinterest' ); ?></div>
			<table class="form-table pntrst_settings_form pntrst_save_enabled">
				<tr>
					<th><?php _e( 'Button Position', 'bws-pinterest' ); ?></th>
					<td>
						<fieldset>
							<label><input name="pntrst_before" type="checkbox" value="1" <?php checked( 1, $this->options['pinit_before'] ); ?> /><?php _e( 'Before content', 'bws-pinterest' ); ?></label><br />
							<label><input name="pntrst_after" type="checkbox" value="1" <?php checked( 1, $this->options['pinit_after'] ); ?> /><?php _e( 'After content', 'bws-pinterest' ); ?></label><br />
							<label><input name="pntrst_hover" type="checkbox" value="1" <?php checked( 1, $this->options['pinit_hover'] ); ?> /><?php _e( 'On image hover', 'bws-pinterest' ); ?></label>
						</fieldset>
					</td>
				</tr>
				<tr class="pntrst-image">
					<th scope="row"><?php _e( 'Button Image', 'bws-pinterest' ); ?></th>
					<td>
						<fieldset>
							<label><input id="pntrst_image_default"name="pntrst_image" type="radio" value="1" <?php checked( 1, $this->options['pinit_image'] ); ?> /><?php _e( 'Default', 'bws-pinterest' ); ?></label><br />
							<label><input id="pntrst_image_custom" name="pntrst_image" type="radio" value="0" <?php checked( 0, $this->options['pinit_image'] ); ?> /><?php _e( 'Custom', 'bws-pinterest' ); ?></label><br/>
							<div class="pntrst-custom-button">
								<input id="pntrst-custom-image" name="pntrst-custom-image" type="file">
								<div class="bws_info">
									<?php printf(
										__( 'Max image size: %1s. Allowed file extensions: %2s' , 'bws-pinterest' ),
											'512Kb',
											'jpg, jpeg, png'
									); ?>
								</div>
								<?php
								if ( ! empty ( $this->options['pinit_custom_image_link'] ) ) { ?>
								<p><?php _e( 'Current custom image', 'bws-pinterest' ); ?>:</p><img width="50" src="<?php echo esc_url( $this->options['pinit_custom_image_link'] ); ?>" />
								<?php } ?>
							</div>
						</fieldset>
					</td>
				</tr>
				<tr class="pntrst-image-shape">
					<th scope="row"><?php _e( 'Button Shape', 'bws-pinterest' ); ?></th>
					<td>
						<fieldset>
							<label><input name="pntrst_image_shape" type="radio" value="1" <?php checked( 1, $this->options['pinit_image_shape'] ); ?> /><?php _e( 'Square', 'bws-pinterest' ); ?></label><br />
							<label><input name="pntrst_image_shape" type="radio" value="0" <?php checked( 0, $this->options['pinit_image_shape'] ); ?> /><?php _e( 'Round', 'bws-pinterest' ); ?></label>
						</fieldset>
					</td>
				</tr>
				<tr class="pntrst-image-size">
					<th scope="row"><?php _e( 'Button Size', 'bws-pinterest' ); ?></th>
					<td>
						<fieldset>
							<label><input name="pntrst_image_size" type="radio" value="1" <?php checked( 1, $this->options['pinit_image_size'] ); ?> /><?php _e( 'Small', 'bws-pinterest' ); ?></label><br />
							<label><input name="pntrst_image_size" type="radio" value="0" <?php checked( 0, $this->options['pinit_image_size'] ); ?> /><?php _e( 'Large', 'bws-pinterest' ); ?></label>
						</fieldset>
					</td>
				</tr>
				<tr class="pntrst-pin-counts">
					<th scope="row"><?php _e( 'Show Pin Count', 'bws-pinterest' ); ?></th>
					<td>
						<fieldset>
							<label><input name="pntrst_pin_counts" type="radio" value="none" <?php checked( 'none', $this->options['pinit_counts'] ); ?> /><?php _e( 'Not shown', 'bws-pinterest' ); ?></label>
							<br />
							<label><input name="pntrst_pin_counts" type="radio" value="above" <?php checked( 'above', $this->options['pinit_counts'] ); ?> /><?php _e( 'Above the button', 'bws-pinterest' ); ?></label>
							<br />
							<label><input name="pntrst_pin_counts" type="radio" value="beside" <?php checked( 'beside', $this->options['pinit_counts'] ); ?> /><?php _e( 'Beside the button', 'bws-pinterest' ); ?></label>
						</fieldset>
					</td>
				</tr>
			</table>

			<div class="bws_tab_sub_label pntrst_follow_enabled"><?php _e( 'Follow Button', 'bws-pinterest' ); ?></div>
			<table class="form-table pntrst_settings_form pntrst_follow_enabled">
				<tr class="pntrst-follow-button-label">
					<th scope="row"><?php _e( 'Full Name', 'bws-pinterest' ); ?></th>
					<td>
						<input name="pntrst_follow_button_label" type="text" size="30" maxlength="50" value="<?php echo $this->options['follow_button_label']; ?>">
						<div class="bws_info"><?php _e( 'Enter your Pinterest profile name. For example, "bestwebsoft".', 'bws-pinterest' ); ?></div>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Button Position', 'bws-pinterest' ); ?></th>
					<td>
						<fieldset>
							<label><input name="pntrst_follow_before" type="checkbox" value="0" <?php checked( 1, $this->options['follow_before'] ); ?> /><?php _e( 'Before content', 'bws-pinterest' ); ?></label><br />
							<label><input name="pntrst_follow_after" type="checkbox" value="1" <?php checked( 1, $this->options['follow_after'] ); ?> /><?php _e( 'After content', 'bws-pinterest' ); ?></label>
						</fieldset>
					</td>
				</tr>
			</table>
		<?php }

		/**
		 * Display custom options on the 'misc' tab
		 * @access public
		 */
		public function additional_misc_options() {
			do_action( 'pntrst_settings_page_misc_action', $this->options );
		}

		/**
		 * Display custom metabox
		 * @access public
		 * @param  void
		 * @return array    The action results
		 */
		public function display_metabox() { ?>
			<div class="postbox">
				<h3 class="hndle">
					<?php _e( 'Pinterest Buttons Shortcode', 'bws-pinterest' ); ?>
				</h3>
				<div class="inside">
					<p><?php _e( 'Add Pinterest to a widget.', 'bws-pinterest' ); ?> <a href="widgets.php"><?php _e( 'Navigate to Widgets', 'bws-pinterest' ); ?></a></p>
					<?php _e( "Add Pinterest button(-s) to your posts, pages or custom post types using the following shortcode:", 'bws-pinterest' ); ?>
					<div class="bws_margined_box">
						<?php _e( 'Save', 'bws-pinterest' );
						bws_shortcode_output( '[bws_pinterest_pin_it]' ); ?>
					</div>
					<div class="bws_margined_box">
						<?php _e( 'Follow', 'bws-pinterest' );
						bws_shortcode_output( '[bws_pinterest_follow]' ); ?>
					</div>
				</div>
			</div>
		<?php }

        /*pls */
		public function tab_display() { ?>
			<h3 class="bws_tab_label"><?php _e( 'Display Settings', 'bws-pinterest' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr />
				<div class="bws_pro_version_bloc">
					<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'bws-pinterest' ); ?>"></button>
					<div class="bws_pro_version_table_bloc">
						<div class="bws_table_bg"></div>
						<table class="form-table bws_pro_version">
							<tr>
								<td colspan="2">
									<?php _e( 'Please choose the necessary post types (or single pages) where Pinterest buttons will be displayed:', 'bws-pinterest' ); ?>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<label>
										<input disabled="disabled" checked="checked" type="checkbox" name="jstree_url" value="1" />
										<?php _e( "Show URL for pages", 'bws-pinterest' );?>
									</label>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<img class="pntrst_pro_version_show" src="<?php echo plugins_url( '../images/pro_screen_1.png', __FILE__ ); ?>" alt="<?php _e( "Example of the site's pages tree", 'bws-pinterest' ); ?>" title="<?php _e( "Example of the site's pages tree", 'bws-pinterest' ); ?>" />
								</td>
							</tr>
						</table>
					</div>
					<?php $this->bws_pro_block_links(); ?>
				</div>
		<?php }
		/* pls*/
	}
}