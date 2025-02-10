<?php
/**
* Displays the content on the plugin settings page
*/

if ( ! defined( 'ABSPATH' ) ) {
	die();
}


if ( ! class_exists( 'Fcbkbttn_Settings_Tabs' ) ) {
	/**
	 * Class for display Settings_Tabs
	 */
	class Fcbkbttn_Settings_Tabs extends Bws_Settings_Tabs {
		/**
		* Constructor.
		*
		* @access public
		*
		* @see Bws_Settings_Tabs::__construct() for more information on default arguments.
		*
		 * @param string $plugin_basename Plugin basename.
		*/
		public function __construct( $plugin_basename ) {
			global $fcbkbttn_options, $fcbkbttn_plugin_info;

			$tabs = array(
				'settings'    => array( 'label' => esc_html__( 'Settings', 'facebook-button-plugin' ) ),
				'display'     => array(
					'label' => esc_html__( 'Display', 'facebook-button-plugin' ),
					'is_pro' => 1,
				),
				'misc'        => array( 'label' => esc_html__( 'Misc', 'facebook-button-plugin' ) ),
				'custom_code'   => array( 'label' => esc_html__( 'Custom Code', 'facebook-button-plugin' ) ),
				'license'     => array( 'label' => esc_html__( 'License Key', 'facebook-button-plugin' ) ),
			);

			parent::__construct(
				array(
				'plugin_basename'	=> $plugin_basename,
				'plugins_info'		=> $fcbkbttn_plugin_info,
				'prefix'			=> 'fcbkbttn',
				'default_options'	=> fcbkbttn_get_options_default(),
				'options'			=> $fcbkbttn_options,
				'is_network_options'=> is_network_admin(),
				'tabs'				=> $tabs,
				'doc_link'			=> 'https://bestwebsoft.com/documentation/bestwebsofts-like-share/bestwebsofts-like-share-user-guide/',
					'doc_video_link'     => 'https://www.youtube.com/watch?v=rFPsPmo02gs',
				'wp_slug'			=> 'facebook-button-plugin',
				'link_key'			=> '427287ceae749cbd015b4bba6041c4b8',
					'link_pn'            => '78',
				)
			);

			add_action( get_parent_class( $this ) . '_additional_misc_options', array( $this, 'additional_misc_options' ) );
			add_action( get_parent_class( $this ) . '_display_metabox', array( $this, 'display_metabox' ) );
			add_action( get_parent_class( $this ) . '_display_second_postbox', array( $this, 'display_second_postbox' ) );
		}

		/**
		* Save plugin options to the database
		 *
		* @access public
		* @return array    The action results
		*/
		public function save_options() {

		    global $fcbkbttn_lang_codes;

			$message = '';
			$notice  = '';
			$error   = '';

			/* Takes all the changed settings on the plugin's admin page and saves them in array 'fcbkbttn_options'. */

			$this->options['id']                    = ! empty( $_REQUEST['fcbkbttn_id'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['fcbkbttn_id'] ) ) : 1443946719181573;
			$this->options['app_secret']            = ! empty( $_REQUEST['fcbkbttn_app_secret'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['fcbkbttn_app_secret'] ) ) : 'd0f86a5b6447a9eade31c0acb7ad4581';
			$this->options['my_page']				= isset( $_REQUEST['fcbkbttn_my_page'] ) ? 1 : 0;
			$this->options['like']					= isset( $_REQUEST['fcbkbttn_like'] ) ? 1 : 0;
			$this->options['share']					= isset( $_REQUEST['fcbkbttn_share'] ) ? 1 : 0;
			$this->options['size']                  = ( isset( $_REQUEST['fcbkbttn_size'] ) && in_array( sanitize_text_field( wp_unslash( $_REQUEST['fcbkbttn_size'] ) ), array( 'small', 'large' ) ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['fcbkbttn_size'] ) ) : $this->options['size'];

			$this->options['where']                 = array();
			if ( ! empty( $_REQUEST['fcbkbttn_where'] ) && is_array( $_REQUEST['fcbkbttn_where'] ) ) {
				foreach ( $_REQUEST['fcbkbttn_where'] as $where ) {
					if ( in_array( sanitize_text_field( wp_unslash( $where ) ), array( 'before', 'after' ) ) ) {
						$this->options['where'][] = sanitize_text_field( wp_unslash( $where ) );
					}
				}
			}
			$this->options['location']              = ( isset( $_REQUEST['fcbkbttn_location'] ) && in_array( sanitize_text_field( wp_unslash( $_REQUEST['fcbkbttn_location'] ) ), array( 'right', 'middle', 'left' ) ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['fcbkbttn_location'] ) ) : $this->options['location'];
			$this->options['locale']                = ( isset( $_REQUEST['fcbkbttn_locale'] ) && array_key_exists( sanitize_text_field( wp_unslash( $_REQUEST['fcbkbttn_locale'] ) ), $fcbkbttn_lang_codes ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['fcbkbttn_locale'] ) ) : $this->options['locale'];
			$this->options['display_option']        = ( isset( $_REQUEST['fcbkbttn_display_option'] ) && 'custom' == $_REQUEST['fcbkbttn_display_option'] && ! empty( $_REQUEST['fcbkbttn_button_image_custom'] ) ) ? 'custom' : 'standard';
			$this->options['layout_like_option']    = ( isset( $_REQUEST['fcbkbttn_like_layout'] ) && in_array( sanitize_text_field( wp_unslash( $_REQUEST['fcbkbttn_like_layout'] ) ), array( 'standard', 'box_count', 'button_count', 'button' ) ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['fcbkbttn_like_layout'] ) ) : $this->options['layout_like_option'];
			$this->options['layout_share_option']   = ( isset( $_REQUEST['fcbkbttn_share_layout'] ) && in_array( sanitize_text_field( wp_unslash( $_REQUEST['fcbkbttn_share_layout'] ) ), array( 'box_count', 'button_count', 'button', 'icon_link', 'icon', 'link' ) ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['fcbkbttn_share_layout'] ) ) : $this->options['layout_share_option'];
			$this->options['like_action']           = ( isset( $_REQUEST['fcbkbttn_like_action'] ) && in_array( sanitize_text_field( wp_unslash( $_REQUEST['fcbkbttn_like_action'] ) ), array( 'like', 'recommend' ) ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['fcbkbttn_like_action'] ) ) : $this->options['like_action'];
			$this->options['color_scheme']          = ( isset( $_REQUEST['fcbkbttn_color_scheme'] ) && in_array( sanitize_text_field( wp_unslash( $_REQUEST['fcbkbttn_color_scheme'] ) ), array( 'light', 'dark' ) ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['fcbkbttn_color_scheme'] ) ) : $this->options['color_scheme'];
			$this->options['width']                 = isset( $_REQUEST['fcbkbttn_width'] ) ? absint( $_REQUEST['fcbkbttn_width'] ) : '';
			$this->options['html5']                 = ( isset( $_REQUEST['fcbkbttn_html5'] ) && in_array( sanitize_text_field( wp_unslash( $_REQUEST['fcbkbttn_html5'] ) ), array( '1', '0' ) ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['fcbkbttn_html5'] ) ) : $this->options['html5'];

			if ( isset( $_REQUEST['fcbkbttn_link'] ) ) {
				$this->options['link']              = sanitize_text_field( wp_unslash( $_REQUEST['fcbkbttn_link'] ) );
				$this->options['link']				= str_replace( 'https://www.facebook.com/profile.php?id=', '', $this->options['link'] );
				$this->options['link']				= str_replace( 'https://www.facebook.com/', '', $this->options['link'] );
			}

			$this->options['use_multilanguage_locale'] = isset( $_REQUEST['fcbkbttn_use_multilanguage_locale'] ) ? 1 : 0;
			$this->options['display_for_excerpt'] = isset( $_REQUEST['fcbkbttn_display_for_excerpt'] ) ? 1 : 0;
			$this->options['display_for_open_graph'] = isset( $_REQUEST['fcbkbttn_display_for_open_graph'] ) ? 1 : 0;

			if ( ( ! empty( $_REQUEST['fcbkbttn_app_secret'] ) && empty( $_REQUEST['fcbkbttn_id'] ) ) || ( empty( $_REQUEST['fcbkbttn_app_secret'] ) && ! empty( $_REQUEST['fcbkbttn_id'] ) ) ) {
				$error  = esc_html__( 'Error: Both "App ID" and "App Secret" fields must be filled or empty. ', 'facebook-button-plugin' );
			}

			/**
			 * Update
			 *
			 * @deprecated 2.65
			 * @todo Update after 03.06.2020
			 */
			if ( isset( $_REQUEST['fcbkbttn_button_image_custom'] ) && sanitize_text_field( wp_unslash( $_REQUEST['fcbkbttn_button_image_custom'] ) ) !== $this->options['fb_img_link'] ) {
				if ( ! empty( $_REQUEST['fcbkbttn_button_image_custom'] ) ) {
					$max_image_width	= 100;
					$max_image_height	= 40;
					$valid_types 		= array( 'jpg', 'jpeg', 'png' );
					$attachment_id = absint( $_REQUEST['fcbkbttn_button_image_custom'] );
					$metadata = wp_get_attachment_metadata( $attachment_id );
					$filename = pathinfo( $metadata['file'] );

					if ( in_array( $filename['extension'], $valid_types ) ) {
						if ( ( $metadata['width'] <= $max_image_width ) && ( $metadata['height'] <= $max_image_height ) ) {
							$this->options['fb_img_link'] = $attachment_id;
						} else {
							$this->options['display_option'] = 'standard';
							$error = esc_html__( 'Error: Check image width or height.', 'facebook-button-plugin' );
						}
					} else {
						$this->options['display_option'] = 'standard';
						$error  = esc_html__( 'Error: Invalid file type', 'facebook-button-plugin' );
					}
				} else {
					$this->options['fb_img_link'] = '';
				}
			}
			/* end todo */

			$this->options = apply_filters( 'fcbkbttn_before_save_options', $this->options );
			update_option( 'fcbkbttn_options', $this->options );
			$message .= esc_html__( 'Settings saved', 'facebook-button-plugin' );

			return compact( 'message', 'notice', 'error' );
		}

		/**
		 * Displays 'settings' menu-tab
		*
		 * @access public
		*/
		public function tab_settings() {
			global $fcbkbttn_lang_codes, $wp_version;
			if ( ! $this->upload_dir ) {
				$this->upload_dir = wp_upload_dir();
			}

			if ( ! $this->all_plugins ) {
				if ( ! function_exists( 'get_plugins' ) ) {
					require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				}
				$this->all_plugins = get_plugins();
			} ?>
			<h3 class="bws_tab_label"><?php esc_html_e( 'Like & Share Settings', 'facebook-button-plugin' ); ?></h3>
			<?php
			$this->help_phrase();
			$output_key = ( 1443946719181573 != $this->options['id'] ) ? $this->options['id'] : '';
			$output_secret = ( 'd0f86a5b6447a9eade31c0acb7ad4581' != $this->options['app_secret'] ) ? $this->options['app_secret'] : '';
			$img_name = 'large' == $this->options['size'] ? 'large-facebook-ico' : 'standard-facebook-ico';
			$fcbkbttn_img = plugins_url( 'images/' . $img_name . '.png', dirname( __FILE__ ) );
			?>
			<hr>
			<div class="bws_tab_sub_label"><?php esc_html_e( 'General', 'facebook-button-plugin' ); ?>
			</div>
			<div class="bws_info">
				<?php
				printf(
					'%s <span>%s</span>',
					esc_html__( 'Why are there no like and share buttons on the site or they are not working?', 'facebook-button-plugin' ),
					wp_kses_post( bws_add_help_box( '<div style="max-width: 450px;">' .
esc_html__( 'Facebook will no longer support the \'Like\', \'Share\' and \'Feed\' Social Plugins for European Region users, unless they are both 1) Logged into their Facebook account, and 2) have provided consent to the "App and Website Cookies" control. If both of these requirements are met, the user will be able to see and interact with plugins such as the \'Like\' and \'Share\' buttons and Facebook Feed. If either of the requirements above are not met, the user will not be able to see the plugins.', 'facebook-button-plugin' ) .
'</div>', 'bws-hide-for-mobile bws-auto-width' ) )
				);
				?>
			</div>
			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( 'App ID', 'facebook-button-plugin' ); ?></th>
					<td>
						<input name='fcbkbttn_id' type='text' maxlength='250' value='<?php echo esc_html( $output_key ); ?>' />
						<br />
						<span class="bws_info"><?php esc_html_e( 'Leave blank to use a default App ID or', 'facebook-button-plugin' ); ?> <a href="https://developers.facebook.com/quickstarts/?platform=web" target="_blank"><?php esc_html_e( 'create a new one.', 'facebook-button-plugin' ); ?></a></span>
                    </td>
				</tr>
				<tr>
						<th scope="row"><?php esc_html_e( 'App Secret', 'facebook-button-plugin' ); ?></th>
						<td>
							<input name='fcbkbttn_app_secret' type='text' maxlength='250' value='<?php echo esc_html( $output_secret ); ?>' /><br />
							<span class="bws_info"><?php esc_html_e( 'Leave blank to use a default App Secret or', 'facebook-button-plugin' ); ?> <a href="https://developers.facebook.com/quickstarts/?platform=web" target="_blank"><?php esc_html_e( 'create a new one.', 'facebook-button-plugin' ); ?></a></span>
						</td>
					</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Buttons', 'facebook-button-plugin' ); ?></th>
					<td>
						<fieldset>
							<label><input name='fcbkbttn_my_page' type='checkbox' value='1' <?php checked( $this->options['my_page'] ); ?> /> <?php esc_html_e( 'Profile URL', 'facebook-button-plugin' ); ?></label><br />
							<label><input name='fcbkbttn_like' type='checkbox' value='1' <?php checked( $this->options['like'] ); ?> /> <?php esc_html_e( 'Like', 'facebook-button-plugin' ); ?></label><br />
							<label><input name='fcbkbttn_share' type='checkbox' value='1' <?php checked( $this->options['share'] ); ?> /> <?php esc_html_e( 'Share', 'facebook-button-plugin' ); ?></label><br />
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Buttons Size', 'facebook-button-plugin' ); ?></th>
					<td>
						<fieldset>
							<label><input name="fcbkbttn_size" type="radio" value="small" <?php checked( 'small', $this->options['size'] ); ?> /> <?php esc_html_e( 'Small', 'facebook-button-plugin' ); ?></label><br />
							<label><input name="fcbkbttn_size" type="radio" value="large" <?php checked( 'large', $this->options['size'] ); ?> /> <?php esc_html_e( 'Large', 'facebook-button-plugin' ); ?></label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Buttons Position', 'facebook-button-plugin' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="checkbox" name="fcbkbttn_where[]" value="before" <?php checked( in_array( 'before', $this->options['where'] ) ); ?> />
								<?php esc_html_e( 'Before content', 'facebook-button-plugin' ); ?>
							</label>
							<br />
							<label>
								<input type="checkbox" name="fcbkbttn_where[]" value="after" <?php checked( in_array( 'after', $this->options['where'] ) ); ?> />
								<?php esc_html_e( 'After content', 'facebook-button-plugin' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Buttons Align', 'facebook-button-plugin' ); ?></th>
					<td>
						<fieldset>
							<label><input name="fcbkbttn_location" type="radio" value="right" <?php checked( 'right', $this->options['location'] ); ?> /> <?php esc_html_e( 'Right', 'facebook-button-plugin' ); ?></label><br />
							<label><input name="fcbkbttn_location" type="radio" value="middle" <?php checked( 'middle', $this->options['location'] ); ?> /> <?php esc_html_e( 'Center', 'facebook-button-plugin' ); ?></label><br />
							<label><input name="fcbkbttn_location" type="radio" value="left" <?php checked( 'left', $this->options['location'] ); ?> /> <?php esc_html_e( 'Left', 'facebook-button-plugin' ); ?></label><br />
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Language', 'facebook-button-plugin' ); ?></th>
					<td>
						<select name="fcbkbttn_locale">
							<?php
							foreach ( $fcbkbttn_lang_codes as $key => $val ) {
								echo '<option value="' . esc_attr( $key ) . '"';
								if ( $key == $this->options['locale'] ) {
									echo ' selected="selected"';
								}
								echo '>' . esc_html ( $val ) . '</option>';
							}
							?>
						</select>
						<br />
						<span class="bws_info"><?php esc_html_e( 'Select the default language for Like & Share buttons.', 'facebook-button-plugin' ); ?></span>
					</td>
				</tr>
				<tr>
					<th>Multilanguage</th>
					<td>
						<?php
						if ( array_key_exists( 'multilanguage/multilanguage.php', $this->all_plugins ) || array_key_exists( 'multilanguage-pro/multilanguage-pro.php', $this->all_plugins ) ) {
							if ( is_plugin_active( 'multilanguage/multilanguage.php' ) || is_plugin_active( 'multilanguage-pro/multilanguage-pro.php' ) ) {
								?>
								<input type="checkbox" name="fcbkbttn_use_multilanguage_locale" value="1" <?php checked( $this->options['use_multilanguage_locale'] ); ?> />
								<span class="bws_info"><?php esc_html_e( 'Enable to switch language automatically on multilingual website using Multilanguage plugin.', 'facebook-button-plugin' ); ?></span>
							<?php } else { ?>
								<input disabled="disabled" type="checkbox" name="fcbkbttn_use_multilanguage_locale" value="1" />
								<span class="bws_info"><?php esc_html_e( 'Enable to switch language automatically on multilingual website using Multilanguage plugin.', 'facebook-button-plugin' ); ?> <a href="<?php bloginfo( 'url' ); ?>/wp-admin/plugins.php" target="_blank"><?php esc_html_e( 'Activate', 'facebook-button-plugin' ); ?></a></span>
								<?php
							}
						} else {
							?>
							<input disabled="disabled" type="checkbox" name="fcbkbttn_use_multilanguage_locale" value="1" />
							<span class="bws_info"><?php esc_html_e( 'Enable to switch language automatically on multilingual website using Multilanguage plugin.', 'facebook-button-plugin' ); ?> <a href="https://bestwebsoft.com/products/wordpress/plugins/multilanguage/?k=196fb3bb74b6e8b1e08f92cddfd54313&pn=78&v=<?php echo esc_attr( $this->plugins_info['Version'] ); ?>&wp_v=<?php echo esc_attr( $wp_version ); ?>" target="_blank"><?php esc_html_e( 'Install Now', 'facebook-button-plugin' ); ?></a></span>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Excerpt', 'facebook-button-plugin' ); ?></th>
					<td>
						<input name='fcbkbttn_display_for_excerpt' type='checkbox' value='1' <?php checked( $this->options['display_for_excerpt'] ); ?> /> <span class="bws_info"><?php esc_html_e( 'Enable to display buttons in excerpt.', 'facebook-button-plugin' ); ?></span>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Meta Tags', 'facebook-button-plugin' ); ?></th>
					<td>
						<input name='fcbkbttn_display_for_open_graph' type='checkbox' value='1' <?php checked( $this->options['display_for_open_graph'] ); ?> /> <span class="bws_info"><?php esc_html_e( 'Enable to use meta tags.', 'facebook-button-plugin' ); ?></span>
					</td>
				</tr>
				<?php do_action( 'fcbkbttn_settings_page_action', $this->options ); ?>
			</table>
			<!-- end general -->
			<div class="bws_tab_sub_label fcbkbttn_my_page_enabled"><?php esc_html_e( 'Profile URL Button', 'facebook-button-plugin' ); ?></div>
            <table class="fcbkbttn_settings_form form-table fcbkbttn_my_page_enabled">
                <tr>
					<th scope="row"><?php esc_html_e( 'Facebook ID or Username', 'facebook-button-plugin' ); ?></th>
                    <td>
						<input name='fcbkbttn_link' type='text' maxlength='250' value='<?php echo esc_html( $this->options['link'] ); ?>' />
                    </td>
                </tr>
                <tr>
                    <th>
						<?php esc_html_e( 'Profile Button Image', 'facebook-button-plugin' ); ?>
                    </th>
                    <td>
                        <fieldset>
                            <label>
                                <input class="bws_option_affect" type="radio" data-affect-show=".fcbkbttn_display_option_default" data-affect-hide=".fcbkbttn_display_option_custom" name="fcbkbttn_display_option" value="standard" <?php checked( 'standard', $this->options['display_option'] ); ?> />
								<?php esc_html_e( 'Default', 'facebook-button-plugin' ); ?>
                            </label><br />
                            <div class="bws_info fcbkbttn_display_option_default">
								<img src="<?php echo esc_url( $fcbkbttn_img ); ?>" style="vertical-align: middle;" />
                                <br /><br />
                            </div>
                            <label>
                                <input class="bws_option_affect" type="radio" data-affect-show=".fcbkbttn_display_option_custom" data-affect-hide=".fcbkbttn_display_option_default" name="fcbkbttn_display_option" value="custom" <?php checked( 'custom', $this->options['display_option'] ); ?> />
								<?php esc_html_e( 'Custom image', 'facebook-button-plugin' ); ?>
                            </label><br />
                        </fieldset>
                        <div class="fcbkbttn_display_option_custom" id="fcbkbttn_image_custom">
                            <div class="wp-media-buttons">
								<a href="#" class="button insert-media add_media hide-if-no-js"><span class="wp-media-buttons-icon"></span> <?php esc_html_e( 'Add Media', 'facebook-button-plugin' ); ?></a>
                                <br />
								<span class="bws_info"><?php esc_html_e( 'Image requirements: max image width: 100px; max image height: 40px; image types: "jpg", "jpeg", "png".', 'facebook-button-plugin' ); ?></span>
                            </div>
                            <br />
                            <div class="fcbkbttn-image">
								<?php
								if ( ! empty( $this->options['fb_img_link'] ) ) {
						            /**
						             * Update
									 *
						             * @deprecated 2.65
						             * @todo Update after 03.06.2020
						             */
						            $url = is_int( $this->options['fb_img_link'] ) ? wp_get_attachment_url( $this->options['fb_img_link'] ) : $this->options['fb_img_link'] ;
						            /* end todo */
									echo '<img src="' . esc_url( $url ) . '" /><span class="fcbkbttn-delete-image"><span class="dashicons dashicons-no-alt"></span></span>';
								}
								?>
                            </div>
							<input class="fcbkbttn-image-id hide-if-js" type="text" name="fcbkbttn_button_image_custom" value="<?php echo ! empty( $this->options['fb_img_link'] ) ? esc_url( $this->options['fb_img_link'] ) : ''; ?>" />
                        </div>
                    </td>
                </tr>
            </table>
			<div class="bws_tab_sub_label fcbkbttn_share_like_block"><?php esc_html_e( 'Like&Share Buttons', 'facebook-button-plugin' ); ?></div>
			<table class="form-table">
				<tr class="fcbkbttn_like_enabled">
					<th><?php esc_html_e( 'Like Button Layout', 'facebook-button-plugin' ); ?></th>
					<td>
						<fieldset class="fcbkbttn_layout_option">
							<label class="fcbkbttn_like_layout">
								<input id="fcbkbttn_standard_layout" type="radio" name="fcbkbttn_like_layout" value="standard" <?php checked( 'standard', $this->options['layout_like_option'] ); ?> />
								Standard
							</label>
							<label>
								<input id="fcbkbttn_box_count_layout" type="radio" name="fcbkbttn_like_layout" value="box_count" <?php checked( 'box_count', $this->options['layout_like_option'] ); ?> />
								Box count
							</label>
							<label>
								<input type="radio" name="fcbkbttn_like_layout" value="button_count" <?php checked( 'button_count', $this->options['layout_like_option'] ); ?> />
								Button count
							</label>
							<label>
								<input type="radio" name="fcbkbttn_like_layout" value="button" <?php checked( 'button', $this->options['layout_like_option'] ); ?> />
								Button
							</label>
						</fieldset>
					</td>
				</tr>
				<tr class="fcbkbttn_share_enabled">
					<th><?php esc_html_e( 'Share Button Layout', 'facebook-button-plugin' ); ?></th>
					<td>
						<fieldset class="fcbkbttn_layout_option">
							<label>
								<input id="fcbkbttn_box_count_layout" type="radio" name="fcbkbttn_share_layout" <?php checked( 'box_count', $this->options['layout_share_option'] ); ?> value="box_count" />
								Box count
							</label>
							<label>
								<input type="radio" name="fcbkbttn_share_layout" value="button_count" <?php checked( 'button_count', $this->options['layout_share_option'] ); ?> />
								Button count
							</label>
							<label>
								<input type="radio" name="fcbkbttn_share_layout" value="button" <?php checked( 'button', $this->options['layout_share_option'] ); ?> />
								Button
							</label>
							<label class="fcbkbttn_share_layout">
								<input type="radio" name="fcbkbttn_share_layout" value="icon_link" <?php checked( 'icon_link', $this->options['layout_share_option'] ); ?> />
								Icon link
							</label>
							<label class="fcbkbttn_share_layout">
								<input type="radio" name="fcbkbttn_share_layout" value="icon" <?php checked( 'icon', $this->options['layout_share_option'] ); ?> />
								Icon
							</label>
							<label class="fcbkbttn_share_layout">
								<input type="radio" name="fcbkbttn_share_layout" value="link" <?php checked( 'link', $this->options['layout_share_option'] ); ?> />
								Link
							</label>
						</fieldset>
					</td>
				</tr>
				<tr class="fcbkbttn_like_enabled">
					<th><?php esc_html_e( 'Like Button Action', 'facebook-button-plugin' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="fcbkbttn_like_action" value="like" <?php checked( 'like', $this->options['like_action'] ); ?> />
								<?php esc_html_e( 'Like', 'facebook-button-plugin' ); ?>
							</label>
							<br />
							<label>
								<input type="radio" name="fcbkbttn_like_action" value="recommend" <?php checked( 'recommend', $this->options['like_action'] ); ?> />
								<?php esc_html_e( 'Recommend', 'facebook-button-plugin' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr class="fcbkbttn_like_standard_layout">
					<th><?php esc_html_e( 'Layout Width', 'facebook-button-plugin' ); ?></th>
					<td>
						<label>
							<input required name="fcbkbttn_width" type="number" step="1" min="225" max="450" value="<?php echo esc_attr( $this->options['width'] ); ?>" />
							<?php esc_html_e( 'px', 'facebook-button-plugin' ); ?>
						</label>
					</td>
				</tr>
				<tr class="fcbkbttn_like_standard_layout">
					<th><?php esc_html_e( 'Theme', 'facebook-button-plugin' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="fcbkbttn_color_scheme" value="light" <?php checked( 'light', $this->options['color_scheme'] ) ; ?> />
								<?php esc_html_e( 'Light', 'facebook-button-plugin' ); ?>
							</label>
							<br />
							<label>
								<input type="radio" name="fcbkbttn_color_scheme" value="dark" <?php checked( 'dark', $this->options['color_scheme'] ); ?> />
								<?php esc_html_e( 'Dark', 'facebook-button-plugin' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr class="fcbkbttn_like_enabled">
					<th scope="row"><?php esc_html_e( 'Like Button HTML Tag', 'facebook-button-plugin' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input name='fcbkbttn_html5' type='radio' value='0' <?php checked( '0', $this->options['html5'] ); ?> /><?php echo '&lt;fb:like&gt;'; ?>
							</label><br />
							<label>
								<input name='fcbkbttn_html5' type='radio' value='1' <?php checked( '1', $this->options['html5'] ); ?> /><?php echo '&lt;div&gt;'; ?>
							</label><br />
							<span class="bws_info"><?php printf( esc_html__( 'Tag %s can be used to improve website code validation.', 'facebook-button-plus' ), '&lt;div&gt;' ); ?></span>
						</fieldset>
					</td>
				</tr>
			</table>
			<!-- general --><!-- pls -->
		<?php }

		/**
		* Display custom options on the 'misc' tab
		 *
		* @access public
		*/
		public function additional_misc_options() {
			do_action( 'fcbkbttn_settings_page_misc_action', $this->options );
		}

		/**
		* Display custom metabox
		 *
		* @access public
		*/
		public function display_metabox() {
			?>
			<div class="postbox">
				<h3 class="hndle">
					<?php esc_html_e( 'Like & Share Shortcode', 'facebook-button-plugin' ); ?>
				</h3>
				<div class="inside">
					<?php esc_html_e( 'Add Like & Share buttons to your posts, pages, custom post types or widgets by using the following shortcode:', 'facebook-button-plugin' ); ?>
					<?php bws_shortcode_output( '[fb_button]' ); ?>
				</div>
			</div>
			<?php
		 }

        /*pls */
		/**
		* Display custom metabox
		 *
		* @access public
		*/
		public function display_second_postbox() {

			if ( ! $this->hide_pro_tabs ) {
				?>
				<div class="postbox bws_pro_version_bloc">
					<div class="bws_table_bg"></div>
					<h3 class="hndle">
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php esc_html_e( 'Close', 'facebook-button-plugin' ); ?>"></button>
						<?php esc_html_e( 'Like & Share buttons Preview', 'facebook-button-plugin' ); ?>
					</h3>
					<div class="inside">
						<img src='<?php echo esc_url( plugins_url( 'images/preview.png', dirname( __FILE__ ) ) ); ?>' />
					</div>
					<?php $this->bws_pro_block_links(); ?>
				</div>
				<?php
			}
		}

		/**
		 * Displays 'Display' menu-tab
		*
		 * @access public
		*/
		public function tab_display() {
			?>
			<h3 class="bws_tab_label"><?php esc_html_e( 'Display Settings', 'facebook-button-plugin' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<div class="bws_pro_version_bloc">
				<div class="bws_pro_version_table_bloc">
					<div class="bws_table_bg"></div>
					<table class="form-table bws_pro_version">
						<tr>
							<td colspan="2">
								<?php esc_html_e( 'Choose the necessary post types (or single pages) where Like & Share buttons will be displayed:', 'facebook-button-plugin' ); ?>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<label>
									<input disabled="disabled" checked="checked" type="checkbox" name="jstree_url" value="1" />
									<?php esc_html_e( 'Show URL for pages', 'facebook-button-plugin' ); ?>
								</label>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<img class="fcbkbttn_pro_block" src="<?php echo esc_url( plugins_url( 'images/pro_screen_1.png', dirname( __FILE__ ) ) ); ?>" alt="<?php esc_html_e( 'Example of site pages tree', 'facebook-button-plugin' ); ?>" title="<?php esc_html_e( 'Example of site pages tree', 'facebook-button-plugin' ); ?>" />
							</td>
						</tr>
					</table>
				</div>
				<?php $this->bws_pro_block_links(); ?>
			</div>
			<?php
		}
	}
}
