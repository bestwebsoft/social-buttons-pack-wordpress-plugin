<?php
/**
 * Displays the content on the plugin settings page
 */

if ( ! class_exists( 'Nstgrm_Settings_Tabs' ) ) {
	class Nstgrm_Settings_Tabs extends Bws_Settings_Tabs {
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
			global $sclbttns_options, $sclbttns_plugin_info;

			if ( is_network_admin() ) {
				$tabs = array(
					'settings'		=> array( 'label' => __( 'Settings', 'social-buttons-pack' ) ),
				);
			} else {
				$tabs = array(
					'settings'		=> array( 'label' => __( 'Settings', 'social-buttons-pack' ) ),
				);
			}

			parent::__construct( array(
				'plugin_basename'	 => $plugin_basename,
				'plugins_info'		 => $sclbttns_plugin_info,
				'prefix'			 => 'nstgrm',
				'default_options'	 => sclbttns_get_option_defaults(),
				'options'			 => $sclbttns_options,
				'is_network_options' => is_network_admin(),
				'tabs'				 => $tabs,
				'wp_slug'			 => 'social-buttons-pack',
			) );

			add_action( get_parent_class( $this ) . '_display_metabox', array( $this, 'display_metabox' ) );
		}

		/**
		 * Save plugin options to the database
		 * @access public
		 * @param  void
		 * @return array    The action results
		 */
		public function save_options() {
			global $wpdb, $sclbttns_options;
			$message = $notice = $error = '';

			if ( ! $this->forbid_view ) {
				/* Takes all the changed settings on the plugin's admin page and saves them in array 'instagram_options'. */

				$this->options['instagram_options']['profile']				= isset( $_REQUEST['nstgrm_profile'] ) ? 1 : 0;
				$this->options['instagram_options']['size']                  = ( isset( $_REQUEST['nstgrm_size'] ) && in_array( $_REQUEST['nstgrm_size'], array( 'small', 'large' ) ) ) ? $_REQUEST['nstgrm_size'] : $this->options['instagram_options']['size'];

				$this->options['instagram_options']['where']                 = array();
				if ( ! empty( $_REQUEST['nstgrm_where'] ) && is_array( $_REQUEST['nstgrm_where'] ) ) {
					foreach ( $_REQUEST['nstgrm_where'] as $where ) {
						if ( in_array( $where, array( 'before', 'after' ) ) ) {
							$this->options['instagram_options']['where'][] 	= $where;
						}
					}
				}
				$this->options['instagram_options']['location']          = ( isset( $_REQUEST['nstgrm_location'] ) && in_array( $_REQUEST['nstgrm_location'], array( 'right', 'middle', 'left' ) ) ) ? $_REQUEST['nstgrm_location'] : $this->options['instagram_options']['location'];
				$this->options['instagram_options']['display_option']    = ( isset( $_REQUEST['nstgrm_display_option'] ) && 'custom' == $_REQUEST['nstgrm_display_option'] && ! empty( $_REQUEST['nstgrm_button_image_custom'] ) ) ? 'custom' : 'standard';

				if ( isset( $_REQUEST['nstgrm_link_account_name'] ) ) {
					$this->options['instagram_options']['account_name']	= sanitize_text_field( $_REQUEST['nstgrm_link_account_name'] );
					$this->options['instagram_options']['account_name']	= str_replace( 'https://www.instagram.com/', '', $this->options['instagram_options']['account_name'] );
				}

				$message .= __( 'Settings saved', 'social-buttons-pack' );

				if ( isset( $_REQUEST['nstgrm_button_image_custom'] ) && $this->options['instagram_options']['img_link'] != $_REQUEST['nstgrm_button_image_custom'] ) {
					if ( ! empty( $_REQUEST['nstgrm_button_image_custom'] ) ) {
						$max_image_width	= 50;
						$max_image_height	= 50;
						$valid_types 		= array( 'jpg', 'jpeg', 'png' );
						$attachment_id = intval( $_REQUEST['nstgrm_button_image_custom'] );
						$metadata = wp_get_attachment_metadata( $attachment_id );
						$filename = pathinfo( $metadata['file'] );

						if ( in_array( $filename['extension'], $valid_types ) ) {
							if ( ( $metadata['width'] <= $max_image_width ) && ( $metadata['height'] <= $max_image_height ) ) {
								$this->options['instagram_options']['img_link'] = $attachment_id;
							} else {
								$this->options['instagram_options']['display_option'] = 'standard';
								$error = __( "Error: Check image width or height.", 'social-buttons-pack' );
							}
						} else {
							$this->options['instagram_options']['display_option'] = 'standard';
							$error	= __( "Error: Invalid file type", 'social-buttons-pack' );
						}
					} else {
						$this->options['instagram_options']['img_link'] = '';
					}
				}
			}

			return compact( 'message', 'notice', 'error' );
		}

		public function tab_settings() {
			if ( ! $this->upload_dir ) {
				$this->upload_dir = wp_upload_dir();
			}

			if ( ! $this->all_plugins ) {
				if ( ! function_exists( 'get_plugins' ) ) {
					require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				}
				$this->all_plugins = get_plugins();
			} ?>
			<h3 class="bws_tab_label"><?php _e( 'Instagram Settings', 'social-buttons-pack' ); ?></h3>
			<?php $this->help_phrase();
			$img_name = 'large' == $this->options['instagram_options']['size'] ? 'large-instagram-ico' : 'standard-instagram-ico';
			$nstgrm_img = plugins_url( 'images/' . $img_name . '.jpg', dirname( __FILE__ ) ); ?>
			<hr>
			<div class="bws_tab_sub_label sclbttns_general_enabled"><?php _e( 'General', 'social-buttons-pack' ); ?></div>
			<table class="form-table nstgrm_settings_form">
				<tr>
					<th scope="row"><?php _e( 'Buttons', 'social-buttons-pack' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input class="bws_option_affect" data-affect-show=".nstgrm_profile_enabled" name='nstgrm_profile' type='checkbox' value='1' <?php checked( $this->options['instagram_options']['profile'] ); ?> /> <?php _e( 'Profile URL', 'social-buttons-pack' ); ?>
							</label><br />
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( "Buttons Size", 'social-buttons-pack' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input name="nstgrm_size" type="radio" value="small" <?php checked( 'small', $this->options['instagram_options']['size'] ); ?> /> <?php _e( 'Small', 'social-buttons-pack' ); ?>
							</label><br />
							<label>
								<input name="nstgrm_size" type="radio" value="large" <?php checked( 'large', $this->options['instagram_options']['size'] ); ?> /> <?php _e( 'Large', 'social-buttons-pack' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Buttons Position', 'social-buttons-pack' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="checkbox" name="nstgrm_where[]" value="before" <?php echo in_array( 'before', $this->options['instagram_options']['where'] ) ? 'checked="checked"' : ''; ?> />
								<?php _e( 'Before content', 'social-buttons-pack' ); ?>
							</label><br />
							<label>
								<input type="checkbox" name="nstgrm_where[]" value="after" <?php echo in_array( 'after', $this->options['instagram_options']['where'] ) ? 'checked="checked"' : ''; ?> />
								<?php _e( 'After content', 'social-buttons-pack' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Buttons Align', 'social-buttons-pack' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input name="nstgrm_location" type="radio" value="right" <?php checked( 'right', $this->options['instagram_options']['location'] ); ?> /> <?php _e( 'Right', 'social-buttons-pack' ); ?>
							</label><br />
							<label>
								<input name="nstgrm_location" type="radio" value="middle" <?php checked( 'middle', $this->options['instagram_options']['location'] ); ?> /> <?php _e( 'Center', 'social-buttons-pack' ); ?>
							</label><br />
							<label>
								<input name="nstgrm_location" type="radio" value="left" <?php checked( 'left', $this->options['instagram_options']['location'] ); ?> /> <?php _e( 'Left', 'social-buttons-pack' ); ?>
							</label><br />
						</fieldset>
					</td>
				</tr>
			</table>
			<div class="bws_tab_sub_label nstgrm_profile_enabled"><?php _e( 'Profile URL Button', 'social-buttons-pack' ); ?></div>
			<table class="form-table nstgrm_settings_form nstgrm_profile_enabled">
                <tr>
                    <th scope="row"><?php _e( 'Account Username', 'social-buttons-pack' ); ?></th>
                    <td>
                        <input name='nstgrm_link_account_name' type='text' maxlength='250' value='<?php echo $this->options['instagram_options']['account_name']; ?>' />
                    </td>
                </tr>
                <tr>
                    <th>
						<?php _e( 'Profile Button Image', 'social-buttons-pack' ); ?>
                    </th>
                    <td>
                        <fieldset>
	                        <label>
		                        <input class="bws_option_affect" type="radio" data-affect-show=".nstgrm_display_option_default" data-affect-hide=".nstgrm_display_option_custom" name="nstgrm_display_option" value="standard" <?php checked( 'standard', $this->options['instagram_options']['display_option'] ); ?> />
		                        <?php _e( 'Default', 'social-buttons-pack' ); ?>
	                        </label><br />
	                        <div class="bws_info nstgrm_display_option_default">
		                        <img src="<?php echo $nstgrm_img; ?>" style="vertical-align: middle;" />
		                        <br /><br />
	                        </div>
	                        <label>
		                        <input class="bws_option_affect" type="radio" data-affect-show=".nstgrm_display_option_custom" data-affect-hide=".nstgrm_display_option_default" name="nstgrm_display_option" value="custom" <?php checked( 'custom', $this->options['instagram_options']['display_option'] ); ?> />
		                        <?php _e( 'Custom image', 'social-buttons-pack' ); ?>
	                        </label><br />
                        </fieldset>
                        <div class="nstgrm_display_option_custom" id="nstgrm_image_custom">
	                        <div class="wp-media-buttons">
		                        <a href="#" class="button insert-media add_media hide-if-no-js"><span class="wp-media-buttons-icon"></span> <?php _e( 'Add Media', 'social-buttons-pack' ); ?></a>
		                        <br />
		                        <span class="bws_info"><?php _e( 'Image requirements: max image width: 50px; max image height: 50px; image types: "jpg", "jpeg", "png".', 'social-buttons-pack' ); ?></span>
	                        </div>
	                        <br />
	                        <div class="nstgrm-image">
		                        <?php if ( ! empty( $this->options['instagram_options']['img_link'] ) ) {
			                        /**
			                         * Update
			                         * @deprecated 2.65
			                         * @todo Update after 03.06.2020
			                         */
			                        $url = is_int( $this->options['instagram_options']['img_link'] ) ? wp_get_attachment_url( $this->options['instagram_options']['img_link'] ) : $this->options['instagram_options']['img_link'] ;
			                        /* end todo */
			                        echo '<img src="' . $url . '" /><span class="nstgrm-delete-image"><span class="dashicons dashicons-no-alt"></span></span>';
		                        } ?>
	                        </div>
	                        <input class="nstgrm-image-id hide-if-js" type="text" name="nstgrm_button_image_custom" value="<?php if ( ! empty( $this->options['instagram_options']['img_link'] ) ) echo $this->options['instagram_options']['img_link']; ?>" />
                        </div>
                    </td>
                </tr>
			</table>
		<?php }

		/**
		* Display custom metabox
		* @access public
		* @param  void
		* @return array    The action results
		*/
		public function display_metabox() { ?>
			<div class="postbox">
				<h3 class="hndle">
					<?php _e( 'Instagram Shortcode', 'social-buttons-pack' ); ?>
				</h3>
				<div class="inside">
					<?php _e( "Add Instagram button to your posts, pages, custom post types or widgets by using the following shortcode:", 'social-buttons-pack' ); ?>
					<?php bws_shortcode_output( '[instagram_button]' ); ?>
				</div>
			</div>
		<?php }
	}
}