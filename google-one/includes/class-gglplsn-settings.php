<?php
/**
 * Displays the content on the plugin settings page
 */

if ( ! class_exists( 'Bws_Settings_Tabs' ) )
	require_once( dirname( dirname( __FILE__ ) ) . '/bws_menu/class-bws-settings.php' );

if ( ! class_exists( 'Gglplsn_Settings_Tabs' ) ) {
	class Gglplsn_Settings_Tabs extends Bws_Settings_Tabs {
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
			global $gglplsn_options, $gglplsn_plugin_info;

			$tabs = array(
				'settings' 		=> array( 'label' => __( 'Settings', 'google-one' ) ),
				'display' 		=> array( 'label' => __( 'Display', 'google-one' ), 'is_pro' => 1 ),
				'misc' 			=> array( 'label' => __( 'Misc', 'google-one' ) ),
				'custom_code' 	=> array( 'label' => __( 'Custom Code', 'google-one' ) ),
				'license'		=> array( 'label' => __( 'License Key', 'google-one' ) )
			);

			parent::__construct( array(
				'plugin_basename' 	 => $plugin_basename,
				'plugins_info'		 => $gglplsn_plugin_info,
				'prefix' 			 => 'gglplsn',
				'default_options' 	 => gglplsn_get_options_default(),
				'options' 			 => $gglplsn_options,
				'is_network_options' => is_network_admin(),
				'tabs' 				 => $tabs,
				'wp_slug'			 => 'google-one',
				'doc_link'			 => 'https://docs.google.com/document/d/1v7j8TysjjBzXVnqozmfxMgjg5f7PS6XtV1GmZxnlNDk',
				'pro_page' 			 => 'admin.php?page=google-plus-one-pro.php',
				'bws_license_plugin' => 'google-one-pro/google-plus-one-pro.php',
				'link_key' 			 => '0a5a8a70ed3c34b95587de0604ca9517',
				'link_pn' 			 => '102'
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
			global $wpdb;

			if ( ! $this->forbid_view ) {
				$this->options['plus_one_js']					= isset( $_REQUEST['gglplsn_plus_one_js'] ) ? 1 : 0;
				$this->options['plus_one_annotation']			= esc_html( $_REQUEST['gglplsn_plus_one_annotation'] );
				$this->options['plus_one_size']					= esc_html( $_REQUEST['gglplsn_plus_one_size'] );
				$this->options['plus_one_annotation_type']		= esc_html( $_REQUEST['gglplsn_plus_one_annotation_type'] );
				$this->options['share_js']						= isset( $_REQUEST['gglplsn_share_js'] ) ? 1 : 0;
				$this->options['share_size']					= intval( $_REQUEST['gglplsn_share_size'] );
				$this->options['share_annotation_type']			= esc_html( $_REQUEST['gglplsn_share_annotation_type'] );
				$this->options['share_annotation']				= esc_html( $_REQUEST['gglplsn_share_annotation'] );
				$this->options['follow_js']						= isset( $_REQUEST['gglplsn_follow_js'] ) ? 1 : 0;
				$this->options['follow_size']					= intval( $_REQUEST['gglplsn_follow_size'] );
				$this->options['follow_annotation']				= esc_html( $_REQUEST['gglplsn_follow_annotation'] );
				$this->options['follow_id']						= sanitize_text_field( $_REQUEST['gglplsn_follow_id'] );
				$this->options['follow_relationship']			= esc_html( $_REQUEST['gglplsn_follow_relationship'] );
				$this->options['hangout_js']					= isset( $_REQUEST['gglplsn_hangout_js'] ) ? 1 : 0;
				$this->options['hangout_topic']					= sanitize_text_field( $_REQUEST['gglplsn_hangout_topic'] );
				$this->options['hangout_topic_title'] 			= esc_html( $_REQUEST['gglplsn_hangout_topic_title'] );
				$this->options['hangout_size']		 			= esc_html( $_REQUEST['gglplsn_hangout_size'] );
				$this->options['hangout_type']					= esc_html( $_REQUEST['gglplsn_hangout_type'] );
				$this->options['hangout_invite_type']			= array();
				$this->options['hangout_invite_id']				= array();
				$this->options['badge_js']						= isset( $_REQUEST['gglplsn_badge_js'] ) ? 1 : 0;
				$this->options['badge_type']					= esc_html( $_REQUEST['gglplsn_badge_type'] );
				$this->options['badge_id']						= sanitize_text_field( $_REQUEST['gglplsn_badge_id'] );
				$this->options['badge_layout']					= ( 'portrait' == $_REQUEST['gglplsn_badge_layout'] ) ? 'portrait' : 'landscape';
				$this->options['badge_show_cover']				= isset( $_REQUEST['gglplsn_badge_show_cover'] ) ? true : false;
				$this->options['badge_show_tagline']			= isset( $_REQUEST['gglplsn_badge_show_tagline'] ) ? true : false;
				$this->options['badge_show_owners']				= isset( $_REQUEST['gglplsn_badge_show_owners'] ) ? true : false;
				$this->options['badge_theme']					= esc_html( $_REQUEST['gglplsn_badge_theme'] );
				$this->options['badge_width']					= intval( $_REQUEST['gglplsn_badge_width'] );
				$this->options['position']						= isset( $_REQUEST['gglplsn_position'] ) ? $_REQUEST['gglplsn_position'] : array();
				$this->options['lang']							= esc_html( $_REQUEST['gglplsn_lang'] );
				$this->options['homepage']						= isset( $_REQUEST['gglplsn_homepage'] ) ? 1 : 0 ;
				$this->options['posts']						= isset( $_REQUEST['gglplsn_posts'] ) ? 1 : 0 ;
				$this->options['pages']						= isset( $_REQUEST['gglplsn_pages'] ) ? 1 : 0 ;
				$this->options['use_multilanguage_locale']		= isset( $_REQUEST['gglplsn_use_multilanguage_locale'] ) ? 1 : 0;

				if ( $this->options['badge_width'] < 180 && 'portrait' == $this->options['badge_layout'] ) {
					$this->options['badge_width'] = 180;
				} elseif ( $this->options['badge_width'] < 273 && 'landscape' == $this->options['badge_layout'] ) {
					$this->options['badge_width'] = 273;
				} elseif ( $this->options['badge_width'] > 450 ) {
					$this->options['badge_width'] = 450;
				}

				$count = 0;
				/* Save invites if Java Script is enabled */
				if ( ! isset( $_REQUEST['gglplsn_hangout_invite_type_hidden_noscript'] ) && ! isset( $_REQUEST['gglplsn_hangout_invite_del_noscript'] ) && ! isset( $_REQUEST['gglplsn_hangout_invite_add_noscript'] ) && isset( $_REQUEST['gglplsn_hangout_invite_type_hidden'] ) ) {
					foreach( $_REQUEST['gglplsn_hangout_invite_type_hidden'] as $value ) {
						$this->options['hangout_invite_type'][ $count ] = $value;
						$this->options['hangout_invite_id'][ $count ] = sanitize_text_field( $_REQUEST['gglplsn_hangout_invite_id_hidden'][ $count ] );
						$count ++;
					}
				/* Delete selected invites if Java Script is disabled */
				} elseif ( isset( $_REQUEST['gglplsn_hangout_invite_del_noscript'] ) && isset( $_REQUEST['gglplsn_hangout_invite_type_hidden_noscript'] ) ) {
					$save_count = 0;
					foreach( $_REQUEST['gglplsn_hangout_invite_type_hidden_noscript'] as $value ) {
						if ( ! isset( $_REQUEST['gglplsn_hangout_invite_checkbox'][ $count ] ) ) {
							$this->options['hangout_invite_type'][ $save_count ] = $value;
							$this->options['hangout_invite_id'][ $save_count ] = $_REQUEST['gglplsn_hangout_invite_id_hidden_noscript'][ $count ];
							$save_count ++;
						}
						$count ++;
					}
				/* Save added invites if Java Script is disabled */
				} elseif ( isset( $_REQUEST['gglplsn_hangout_invite_type_hidden_noscript'] ) ) {
					foreach( $_REQUEST['gglplsn_hangout_invite_type_hidden_noscript'] as $value ) {
						$this->options['hangout_invite_type'][ $count ] = $value;
						$this->options['hangout_invite_id'][ $count ] = $_REQUEST['gglplsn_hangout_invite_id_hidden_noscript'][ $count ];
						$count ++;
					}
				}

				if ( isset( $_REQUEST['gglplsn_hangout_invite_add_noscript'] ) ) {
					/* Invite Email validating if Java Script is disabled */
					if ( ! empty( $_REQUEST['gglplsn_hangout_invite_id_noscript'] ) && isset( $_REQUEST['gglplsn_hangout_invite_type_select'] ) ) {
						$noscript_validate_email = true;
						if ( 'EMAIL' == $_REQUEST['gglplsn_hangout_invite_type_select'] ) {
							$noscript_validate_email = is_email( $_REQUEST['gglplsn_hangout_invite_id_noscript'] );
						}
					}

					/* Invite errors for disabled Java Script */
					if ( isset( $noscript_validate_email ) && false == $noscript_validate_email ) {
						$error = __( 'Email is invalid', 'google-one' );
					} elseif ( empty( $_REQUEST['gglplsn_hangout_invite_type_select'] ) ) {
						$error = __( 'Please select the invitation type', 'google-one' );
					} elseif ( empty( $_REQUEST['gglplsn_hangout_invite_id_noscript'] ) ) {
						$error = __( "Invitation field can't be empty", 'google-one' );
					}

					if ( ! empty( $this->options['hangout_invite_type'] ) && '' == $error ) {
						$phone_added = array_search( 'PHONE', $this->options['hangout_invite_type'] );
						if ( false !== $phone_added && 'PHONE' == $_REQUEST['gglplsn_hangout_invite_type_select'] ) {
							$error = __( 'Only one phone number can be added', 'google-one' );
						} elseif ( false !== $phone_added && 'PHONE' != $_REQUEST['gglplsn_hangout_invite_type_select'] ) {
							$error = __( "You can't add the invitation because the phone number is already added", 'google-one' );
						} elseif ( false === $phone_added && 'PHONE' == $_REQUEST['gglplsn_hangout_invite_type_select'] ) {
							$error = __( "You can't add the phone number because another invitation type is already added", 'google-one' );
						}

						foreach ( $this->options['hangout_invite_id'] as $value ) {
							if ( $_REQUEST['gglplsn_hangout_invite_id_noscript'] == $value ) {
								$error = __( 'Is already added', 'google-one' );
								break;
							}
						}
					}

					/* Add invite if Java Script is disabled */
					if ( '' == $error ) {
						$this->options['hangout_invite_type'][ $count ] = $_REQUEST['gglplsn_hangout_invite_type_select'];
						$this->options['hangout_invite_id'][ $count ] = sanitize_text_field( $_REQUEST['gglplsn_hangout_invite_id_noscript'] );
					}
				}

				/* Update options in the database */				
				update_option( 'gglplsn_options', $this->options );

				$message = __( 'Settings saved', 'google-one' );
			}

			return compact( 'message', 'notice', 'error' );
		}

		/**
		 *
		 */
		public function tab_settings() { 
			global $gglplsn_lang_codes, $wp_version;

			if ( ! $this->all_plugins ) {
				if ( ! function_exists( 'get_plugins' ) )
					require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				$this->all_plugins = get_plugins();
			} ?>
			<h3 class="bws_tab_label"><?php _e( 'Google +1 Settings', 'google-one' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>			
			<div class="bws_tab_sub_label"><?php _e( 'General', 'google-one' ); ?></div>
			<table class="form-table gglplsn_settings_form">
				<tr>
					<th><?php _e( 'Buttons', 'google-one' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="checkbox" value="1" name="gglplsn_plus_one_js"<?php checked( 1, $this->options['plus_one_js'] ); ?> /> <?php _e( 'Google +1', 'google-one' ); ?>
							</label>
							<br />
							<label>
								<input type="checkbox" value="1" name="gglplsn_share_js"<?php checked( 1, $this->options['share_js'] ); ?> /> <?php _e( 'Share', 'google-one' ); ?>
							</label>
							<br />
							<label>
								<input type="checkbox" value="1" name="gglplsn_follow_js"<?php checked( 1, $this->options['follow_js'] ); ?> />
								<?php _e( 'Follow', 'google-one' ); ?>								
								<span class="bws_info gglplsn_notice gglplsn-follow-notice gglplsn-unvisible-notice">
									<?php if ( empty( $this->options['follow_id'] ) ) { ?>
										<?php _e( 'To see this button, please', 'google-one' ); ?>
										<a href="#gglplsn_follow_id"><?php _e( 'enter', 'google-one' ) ?></a>
										<?php _e( 'the Google+ ID', 'google-one' ); ?>.
									<?php } ?>
								</span>
							</label>								
							<br />
							<label>
								<input type="checkbox" value="1" name="gglplsn_hangout_js"<?php checked( 1, $this->options['hangout_js'] ); ?> />
								<?php _e( 'Hangout', 'google-one' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Badge', 'google-one' ); ?></th>
					<td>
						<label>
							<input type="checkbox" value="1" name="gglplsn_badge_js" <?php checked( 1, $this->options['badge_js'] ); ?> /> 
							<span class="bws_info"><?php _e( 'Enable to display a Google+ badge in the front-end.', 'google-one'  ); ?></span> 
							<span class="bws_info gglplsn_notice gglplsn-badge-notice gglplsn-unvisible-notice">
								<?php if ( empty( $this->options['badge_id'] ) ) { ?>
									<?php _e( 'To see this button, please', 'google-one' ); ?>
									<a href="#gglplsn_badge_id"><?php _e( 'enter', 'google-one' ) ?></a>
									<?php _e( 'the Google+ ID', 'google-one' ); ?>.
								<?php } ?>
							</span>
						</label>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Display on', 'google-one' ); ?></th>
					<td>
						<fieldset>
							<label><input type="checkbox" value="1" name="gglplsn_homepage" class="gglplsn-no-ajax" <?php checked( 1, $this->options['homepage'] ); ?> /> <?php _e( 'Home page', 'google-one' ); ?></label>
							<br/>
							<label>
								<input type="checkbox" name="gglplsn_posts" <?php checked( 1, $this->options['posts'] ); ?> value="1" />
								<?php _e( 'Posts', 'google-one' ); ?>
							</label>
							<br/>
							<label>
								<input type="checkbox" name="gglplsn_pages" <?php checked( 1, $this->options['pages'] ); ?>  value="1" />
								<?php _e( 'Pages', 'google-one' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Buttons Position', 'google-one' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="checkbox" name="gglplsn_position[]" value="before" <?php if ( in_array( 'before', $this->options['position'] ) ) echo 'checked="checked"'; ?> /> 
								<?php _e( 'Before content', 'google-one' ); ?></option>
							</label>
							<br>
							<label>
								<input type="checkbox" name="gglplsn_position[]" value="after" <?php if ( in_array( 'after', $this->options['position'] ) ) echo 'checked="checked"'; ?> /> 
								<?php _e( 'After content', 'google-one' ); ?></option>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Language', 'google-one' ); ?></th>
					<td>
						<fieldset>
							<select name="gglplsn_lang">
								<?php foreach ( $gglplsn_lang_codes as $key => $val ) {
									echo '<option value="' . $key . '"';
									if ( $key == $this->options['lang'] )
										echo ' selected="selected"';
									echo '>' . esc_html( $val ) . '</option>';
								} ?>
							</select>							
						</fieldset>
						<div class="bws_info"><?php _e( 'Select the default language for Google+ button(-s).', 'google-one' ); ?></div>
					</td>
				</tr>
				<tr>
					<th>Multilanguage</th>
					<td>							
						<?php if ( array_key_exists( 'multilanguage/multilanguage.php', $this->all_plugins ) || array_key_exists( 'multilanguage-pro/multilanguage-pro.php', $this->all_plugins ) ) {
							if ( is_plugin_active( 'multilanguage/multilanguage.php' ) || is_plugin_active( 'multilanguage-pro/multilanguage-pro.php' ) ) { ?>
								
								<label><input type="checkbox" name="gglplsn_use_multilanguage_locale" value="1" <?php checked( 1, $this->options["use_multilanguage_locale"] ); ?> />
								<span class="bws_info"><?php _e( 'Enable to switch language automatically on multilingual website using Multilanguage plugin.', 'google-one' ); ?></span></label>
							<?php } else { ?>
								<input disabled="disabled" type="checkbox" name="gglplsn_use_multilanguage_locale" value="1" />
								<span class="bws_info"><?php _e( 'Enable to switch language automatically on multilingual website using Multilanguage plugin.', 'google-one' ); ?> <a href="<?php echo bloginfo( "url" ); ?>/wp-admin/plugins.php"><?php printf( __( 'Activate %s', 'google-one' ), 'Multilanguage' ); ?></a></span>
							<?php }
						} else { ?>
							<input disabled="disabled" type="checkbox" name="gglplsn_use_multilanguage_locale" value="1" />
							<span class="bws_info"><?php _e( 'Enable to switch language automatically on multilingual website using Multilanguage plugin.', 'google-one' ); ?> <a href="https://bestwebsoft.com/products/wordpress/plugins/multilanguage/?k=28a18815248c6b7f41cfb667574b9dc4&pn=118&v=<?php echo $this->plugins_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>"><?php _e( 'Learn More', 'google-one' ); ?></a></span>
						<?php } ?>							
					</td>
				</tr>
			</table>
			<div class="bws_tab_sub_label gglplsn_plus_one_enabled"><?php _e( 'Google+ Button', 'google-one' ); ?></div>
						<table class="form-table gglplsn_settings_form gglplsn_plus_one_enabled">
				<tr>
					<th><?php _e( 'Size', 'google-one' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="gglplsn_plus_one_size" value="small" <?php checked( 'small', $this->options['plus_one_size'] ); ?> /> <?php _ex( 'Small', 'for:size', 'google-one' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="gglplsn_plus_one_size" value="medium" <?php checked( 'medium', $this->options['plus_one_size'] ); ?> /> <?php _ex( 'Medium', 'for:size', 'google-one' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="gglplsn_plus_one_size" value="standard" <?php checked( 'standard', $this->options['plus_one_size'] ); ?> /> <?php _ex( 'Standard', 'for:size', 'google-one' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="gglplsn_plus_one_size" value="tall" <?php checked( 'tall', $this->options['plus_one_size'] ); ?> /> <?php _ex( 'Tall', 'for:size', 'google-one' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Annotation', 'google-one' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="gglplsn_plus_one_annotation" value="none" <?php checked( 'none', $this->options['plus_one_annotation'] ); ?> /> <?php _e( 'None', 'google-one' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="gglplsn_plus_one_annotation" value="bubble" <?php checked( 'bubble', $this->options['plus_one_annotation'] ); ?> /> <?php _e( 'Bubble', 'google-one' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="gglplsn_plus_one_annotation" value="inline" <?php checked( 'inline', $this->options['plus_one_annotation'] ); ?> /> <?php _e( 'Inline', 'google-one' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr class="gglplsn-plus-one-annotation-type">
					<th><?php _e( 'Annotation Type', 'google-one' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="gglplsn_plus_one_annotation_type" value="standard" <?php checked( 'standard', $this->options['plus_one_annotation_type'] ); ?> /> <?php _e( 'Standard', 'google-one' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="gglplsn_plus_one_annotation_type" value="short" <?php checked( 'short', $this->options['plus_one_annotation_type'] ); ?> /> <?php _e( 'Short', 'google-one' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
			</table>
			<div class="bws_tab_sub_label gglplsn_share_enabled"><?php _e( 'Share Button', 'google-one' ); ?></div>
			<table class="form-table gglplsn_settings_form gglplsn_share_enabled">
				<tr>
					<th><?php _e( 'Annotation', 'google-one' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="gglplsn_share_annotation" value="none" <?php checked( 'none', $this->options['share_annotation'] ); ?> /> <?php _e( 'None', 'google-one' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="gglplsn_share_annotation" value="inline" <?php checked( 'inline', $this->options['share_annotation'] ); ?> /> <?php _e( 'Inline', 'google-one' ); ?> (<?php _e( 'horizontal', 'google-one' ); ?>)
							</label>
							<br/>
							<label>
								<input type="radio" name="gglplsn_share_annotation" value="bubble" <?php checked( 'bubble', $this->options['share_annotation'] ); ?> /> <?php _e( 'Bubble', 'google-one' ); ?> (<?php _e( 'horizontal', 'google-one' ); ?>)
							</label>
							<br/>
							<label>
								<input type="radio" name="gglplsn_share_annotation" value="vertical-bubble" <?php checked( 'vertical-bubble', $this->options['share_annotation'] ); ?> /> <?php _e( 'Bubble', 'google-one' ); ?> (<?php _e( 'vertical', 'google-one' ); ?>)
							</label>
						</fieldset>
						<div class="bws_info"><?php _e( 'Display the number of users who have shared the page.', 'google-one' ); ?></div>
					</td>
				</tr>
				<tr class="gglplsn-share-size">
					<th><?php _e( 'Size', 'google-one' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="gglplsn_share_size" value="15" <?php checked( '15', $this->options['share_size'] ); ?> /> <?php _e( 'Small', 'google-one' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="gglplsn_share_size" value="20" <?php checked( '20', $this->options['share_size'] ); ?> /> <?php _e( 'Medium', 'google-one' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="gglplsn_share_size" value="24" <?php checked( '24', $this->options['share_size'] ); ?> /> <?php _e( 'Large', 'google-one' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr class="gglplsn-share-annotation-type">
					<th><?php _e( 'Annotation Type', 'google-one' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="gglplsn_share_annotation_type" value="standard" <?php checked( 'standard', $this->options['share_annotation_type'] ); ?> /> <?php _e( 'Standard', 'google-one' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="gglplsn_share_annotation_type" value="short" <?php checked( 'short', $this->options['share_annotation_type'] ); ?> /> <?php _e( 'Short', 'google-one' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
			</table>
			<div class="bws_tab_sub_label gglplsn_follow_enabled"><?php _e( 'Follow Button', 'google-one' ); ?></div>
			<table class="form-table gglplsn_settings_form gglplsn_follow_enabled">
				<tr id="gglplsn_follow_id">
					<th><?php _e( 'Google+ User ID', 'google-one' ); ?></th>
					<td>
						<input type="text" <?php if ( 1 == $this->options['follow_js'] ) echo 'required="required"'; ?> name="gglplsn_follow_id" value="<?php echo $this->options['follow_id']; ?>">
						<div class="bws_info"><?php echo __( 'Enter your Google+ user ID.', 'google-one' ) . '&nbsp;' . __( 'For example', 'google-one' ) . ',&nbsp;"12345678912345678912"&nbsp;' . __( 'or', 'google-one' ) . '&nbsp;"+YouName".'; ?></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Relationship', 'google-one' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="gglplsn_follow_relationship" value="author" <?php checked( 'author', $this->options['follow_relationship'] ); ?> /> <?php _e( 'Author', 'google-one' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="gglplsn_follow_relationship" value="publisher" <?php checked( 'publisher', $this->options['follow_relationship'] ); ?> /> <?php _e( 'Publisher', 'google-one' ); ?>
							</label>
						</fieldset>
						<div class="bws_info"><?php _e( 'Describes your relationship to the content of the page where the button is added.', 'google-one' ); ?></div>
						</p>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Size', 'google-one' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="gglplsn_follow_size" value="15" <?php checked( '15', $this->options['follow_size'] ); ?> /> <?php _e( 'Small', 'google-one' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="gglplsn_follow_size" value="20" <?php checked( '20', $this->options['follow_size'] ); ?> /> <?php _e( 'Medium', 'google-one' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="gglplsn_follow_size" value="24" <?php checked( '24', $this->options['follow_size'] ); ?> /> <?php _e( 'Large', 'google-one' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Annotation', 'google-one' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="gglplsn_follow_annotation" value="none" <?php checked( 'none', $this->options['follow_annotation'] ); ?> /> <?php _e( 'None', 'google-one' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="gglplsn_follow_annotation" value="bubble" <?php checked( 'bubble', $this->options['follow_annotation'] ); ?> /> <?php _e( 'Bubble', 'google-one' ); ?> (<?php _e( 'horizontal', 'google-one' ); ?>)
							</label>
							<br/>
							<label>
								<input type="radio" name="gglplsn_follow_annotation" value="vertical-bubble" <?php checked( 'vertical-bubble', $this->options['follow_annotation'] ); ?> /> <?php _e( 'Bubble', 'google-one' ); ?> (<?php _e( 'vertical', 'google-one' ); ?>)
							</label>
						</fieldset>
						<div class="bws_info"><?php _e( 'Display the number of users who are following this page or person.', 'google-one' ); ?></div>
					</td>
				</tr>
			</table>
			<div class="bws_tab_sub_label gglplsn_hangout_enabled"><?php _e( 'Hangout Button', 'google-one' ); ?></div>
			<table class="form-table gglplsn_settings_form gglplsn_hangout_enabled">
				<tr>
					<th><?php _e( 'Topic', 'google-one' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="gglplsn_hangout_topic_title" class="gglplsn-no-ajax" value="1" <?php checked( 1, $this->options['hangout_topic_title'] ); ?> />
								<?php _e( 'Current page title', 'google-one' ); ?>
							</label>
							<br />
							<label>
								<input type="radio" name="gglplsn_hangout_topic_title" class="gglplsn-no-ajax" value="0" <?php checked( 0, $this->options['hangout_topic_title'] ); ?> /> 
								<?php _e( 'Custom', 'google-one' ); ?>
							</label>
							<br />
							<input type="text" name="gglplsn_hangout_topic" class="gglplsn-no-ajax gglplsn_hangout_topic_custom" value="<?php echo $this->options['hangout_topic']; ?>" />
						</fieldset>							
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Size', 'google-one' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="gglplsn_hangout_size" value="standard" <?php checked( 'standard', $this->options['hangout_size'] ); ?> /> <?php _ex( 'Standard', 'for:hangout_size', 'google-one' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="gglplsn_hangout_size" value="narrow" <?php checked( 'narrow', $this->options['hangout_size'] ); ?> /> <?php _e( 'Narrow', 'google-one' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="gglplsn_hangout_size" value="wide" <?php checked( 'wide', $this->options['hangout_size'] ); ?> /> <?php _e( 'Wide', 'google-one' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Type', 'google-one' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="gglplsn_hangout_type" value="normal" <?php checked( 'normal', $this->options['hangout_type'] ); ?> /> <?php _e( 'Normal', 'google-one' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="gglplsn_hangout_type" value="onair" <?php checked( 'onair', $this->options['hangout_type'] ); ?> /> <?php _e( 'On air', 'google-one' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="gglplsn_hangout_type" value="moderated" <?php checked( 'moderated', $this->options['hangout_type'] ); ?> /> <?php _e( 'Party', 'google-one' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Invitation Type', 'google-one' ); ?></th>
					<td>
						<select id="gglplsn_hangout_invite_type" name="gglplsn_hangout_invite_type_select" class="gglplsn-no-ajax">
							<option value="" disabled="disabled" selected="selected"><?php _e( 'Select The Type', 'google-one' ); ?></option>
							<option value="PROFILE"><?php _e( 'Google+ Profile ID', 'google-one' ); ?></option>
							<option value="CIRCLE"><?php _e( 'Google+ Circle ID', 'google-one' ); ?></option>
							<option value="EMAIL"><?php _e( 'Email', 'google-one' ); ?></option>
							<option value="PHONE"><?php _e( 'Phone Number', 'google-one' ); ?></option>
						</select>
						<div class="gglplsn-view-invited" style="display:none;">
							<?php if ( ! empty( $this->options['hangout_invite_type'] ) ) {
								for ( $i = 0; $i < count( $this->options['hangout_invite_type'] ); $i++ ) { ?>
									<div>
										<input name="gglplsn_hangout_invite_type_hidden[]" value="<?php echo $this->options['hangout_invite_type'][ $i ]; ?>" type="hidden">
										<input name="gglplsn_hangout_invite_id_hidden[]" value="<?php echo $this->options['hangout_invite_id'][ $i ]; ?>" type="hidden">
										<span>
											<a class="gglplsn-delbutton"></a>
											<?php echo '&nbsp;' . $this->options['hangout_invite_id'][ $i ]; ?>
										</span>
									</div>
								<?php }
							} ?>
						</div>
						<noscript>
							<div class="gglplsn-view-invited-noscript">
								<?php if ( ! empty( $this->options['hangout_invite_type'] ) ) {
									for ( $i = 0; $i < count( $this->options['hangout_invite_type'] ); $i++ ) { ?>
										<p>
											<input name="gglplsn_hangout_invite_type_hidden_noscript[<?php echo $i; ?>]" value="<?php echo $this->options['hangout_invite_type'][ $i ]; ?>" type="hidden" />
											<input name="gglplsn_hangout_invite_id_hidden_noscript[<?php echo $i; ?>]" value="<?php echo $this->options['hangout_invite_id'][ $i ]; ?>" type="hidden" />
											<input type="checkbox" value="1" name="gglplsn_hangout_invite_checkbox[<?php echo $i; ?>]" id="gglplsn_hangout_invite_checkbox[<?php echo $i; ?>]" />
											<label for="gglplsn_hangout_invite_checkbox[<?php echo $i; ?>]">
												<?php echo $this->options['hangout_invite_id'][ $i ]; ?>
											</label>
										</p>
									<?php }
								} ?>
							</div>
						</noscript>
					</td>
				</tr>
				<tr class="gglplsn-invite-tr-noscript">
					<th>
						<noscript>
							<?php _e( 'Google+ ID, Phone Number or Email', 'google-one' ); ?>
						</noscript>
					</th>
					<td>
						<noscript>
							<input type="text" id="gglplsn_hangout_invite_id_noscript" name="gglplsn_hangout_invite_id_noscript" class="gglplsn-no-ajax">
							<input type="submit" name="gglplsn_hangout_invite_add_noscript" class="button tagadd" value="<?php _e( 'Add', 'google-one' ); ?>" id="gglplsn_hangout_invite_add_noscript">
							<?php if ( ! empty( $this->options['hangout_invite_type'] ) ) { ?>
								<input type="submit" name="gglplsn_hangout_invite_del_noscript" class="button tagadd" value="<?php _e( 'Delete Selected', 'google-one' ); ?>" id="gglplsn_hangout_invite_del">
							<?php } ?>
							<p class="gglplsn-id-prompt">
								<span class="bws_info"><?php echo __( "If Invitation Type is 'Google+ Profile ID', it should look like", 'google-one' ) . '&nbsp;"12345678912345678912"&nbsp;' . __( 'or', 'google-one' ) . '&nbsp;"+YouName".'; ?></span>
							</p>
							<hr class="gglplsn-noscript-hr" />
							<p class="gglplsn-id-prompt">
								<span class="bws_info"><?php echo __( "If Invitation Type is 'Google+ Circle ID', it should look like", 'google-one' ) . '&nbsp;"123ab345cd576ef7"'?></span>
							</p>
							<hr class="gglplsn-noscript-hr" />
							<p class="gglplsn-id-prompt">
								<span class="bws_info"><?php echo __( "If Invitation Type is 'Email', it should look like", 'google-one' ) . '&nbsp;"example@gmail.com"'; ?></span>
							</p>
							<hr class="gglplsn-noscript-hr" />
							<p class="gglplsn-id-prompt">
								<span class="bws_info"><?php echo __( "If Invitation Type is 'Phone Number', it should look like", 'google-one' ) . '&nbsp;"+38001234567"&nbsp;'; ?></span>
							</p>
						</noscript>
					</td>
				</tr>
				<tr class="gglplsn-hangout-invite-id">
					<th style="display:none;"></th>
					<td style="display:none;">
						<input type="text" id="gglplsn_hangout_invite_id" class="gglplsn-no-ajax">
						<input type="submit" class="button tagadd" value="<?php _e( 'Add', 'google-one' ); ?>" id="gglplsn_hangout_invite_add">
						<p class="gglplsn-id-prompt">
							<span class="bws_info"></span>
						</p>
						<p id="gglplsn_invite_id_error" style="display:none;"></p>
					</td>
				</tr>
			</table>
			<div class="bws_tab_sub_label gglplsn_badge_enabled"><?php _e( 'Google+ Badge', 'google-one' ); ?></div>
			<table class="form-table gglplsn_settings_form gglplsn_badge_enabled">
				<tr>
					<th><?php _e( 'Type', 'google-one' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="gglplsn_badge_type" value="person" <?php checked( 'person', $this->options['badge_type'] ); ?> /> <?php _e( 'Person', 'google-one' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="gglplsn_badge_type" value="page" <?php checked( 'page', $this->options['badge_type'] ); ?> /> <?php _e( 'Page', 'google-one' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="gglplsn_badge_type" value="community" <?php checked( 'community', $this->options['badge_type'] ); ?> /> <?php _e( 'Community', 'google-one' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr id="gglplsn_badge_id">
					<th class="gglplsn-badge-id-th">
						<?php switch ( $this->options['badge_type'] ) {
							case 'person' :
								_e( 'Google+ User ID', 'google-one' );
								$badge_id_info 		= __( 'Enter your Google+ user ID.', 'google-one' ) . '&nbsp;' . __( 'For example', 'google-one' ) . ',&nbsp;"12345678912345678912"&nbsp;' . __( 'or', 'google-one' ) . '&nbsp;"+YouName".';
								$badge_tagline_info	= __( "Enable to display the user's tag line.", 'google-one' );
								break;
							case 'page' :
								_e( 'Google+ Page ID', 'google-one' );
								$badge_id_info 		= __( 'Enter your Google+ page ID.', 'google-one' ) . '&nbsp;' . __( 'For example', 'google-one' ) . ',&nbsp;"12345678912345678912"&nbsp;' . __( 'or', 'google-one' ) . '&nbsp;"+CompanyName".';
								$badge_tagline_info	= __( 'Enable to display the company tag line.', 'google-one' );
								break;
							case 'community' :
								_e( 'Google+ Community ID', 'google-one' );
								$badge_id_info		= __( 'Enter your Google+ community ID.', 'google-one' ) . '&nbsp;' . __( 'For example', 'google-one' ) . ',&nbsp;"12345678912345678912"&nbsp;' . __( 'or', 'google-one' ) . '&nbsp;"+CommunityName".';
								$badge_tagline_info	= __( 'Enable to display the community tag line.', 'google-one' );
								break;
						} ?>
					</th>
					<td>
						<input type="text" name="gglplsn_badge_id" <?php if ( 1 == $this->options['badge_js'] ) echo 'required="required"'; ?> value="<?php echo $this->options['badge_id']; ?>">
						<div class="bws_info gglplsn-badge-id-info"><?php echo $badge_id_info; ?></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Layout', 'google-one' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="gglplsn_badge_layout" value="portrait" <?php checked( 'portrait', $this->options['badge_layout'] ); ?> /> <?php _e( 'Portrait', 'google-one' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="gglplsn_badge_layout" value="landscape" <?php checked( 'landscape', $this->options['badge_layout'] ); ?> /> <?php _e( 'Landscape', 'google-one' ); ?>
							</label>
						</fieldset>
						<span class="bws_info"><?php _e( 'Sets the orientation of the badge.', 'google-one' ); ?></span>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Width', 'google-one' ); ?></th>
					<td>
						<input type="number" name="gglplsn_badge_width" max="450" <?php echo ( 'portrait' == $this->options['badge_layout'] ) ? 'min="180"' : 'min="273"'; ?>
							value="<?php echo $this->options['badge_width']; ?>">
							<?php _e( 'px', 'google-one' ); ?>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Color Theme', 'google-one' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="gglplsn_badge_theme" value="light" <?php checked( 'light', $this->options['badge_theme'] ); ?> /> <?php _e( 'Light', 'google-one' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="gglplsn_badge_theme" value="dark" <?php checked( 'dark', $this->options['badge_theme'] ); ?> /> <?php _e( 'Dark', 'google-one' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr class="gglplsn-show-cover">
					<th><?php _e( 'Cover Photo', 'google-one' ); ?></th>
					<td>
						<label><input type="checkbox" value="1" name="gglplsn_badge_show_cover" <?php checked( true, $this->options['badge_show_cover'] ); ?> /> <span class="bws_info"><?php _e( 'Enable to display a cover photo.', 'google-one' ); ?></span></label>
					</td>
				</tr>
				<tr class="gglplsn-show-tagline">
					<th><?php _e( 'Tag Line', 'google-one' ); ?></th>
					<td>
						<label><input type="checkbox" value="1" name="gglplsn_badge_show_tagline"<?php checked( true, $this->options['badge_show_tagline'] ); ?> /> <span class="bws_info gglplsn-badge-tagline-info"><?php echo $badge_tagline_info; ?></span></label>
					</td>
				</tr>
				<tr class="gglplsn-show-owners">
					<th><?php _e( 'Owners', 'google-one' ); ?></th>
					<td>
						<label><input type="checkbox" value="1" name="gglplsn_badge_show_owners"<?php checked( true, $this->options['badge_show_owners'] ); ?> /> <span class="bws_info"><?php _e( 'Enable to display a list of community owners.', 'google-one' ); ?></span></label>
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
					<?php _e( 'Google +1 Buttons Shortchode', 'google-one' ); ?>
				</h3>
				<div class="inside">
					<?php _e( "Add Google +1 button(-s) to your posts, pages, custom post types or widgets by using the following shortcode:", 'google-one' ); ?>
					<?php bws_shortcode_output( '[bws_googleplusone]' ); ?>						
				</div>
			</div>
		<?php }

		public function display_second_postbox() {
			if ( ! $this->hide_pro_tabs ) { ?>
				<div class="postbox bws_pro_version_bloc">
					<div class="bws_table_bg"></div>
					<h3 class="hndle">
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'google-one' ); ?>"></button>
						<?php _e( 'Google +1 Buttons Preview', 'google-one' ); ?>
					</h3>
					<div class="inside">								
						<img src='<?php echo plugins_url( 'images/preview_screenshot.png', dirname( __FILE__ ) ); ?>' />
					</div>
					<?php $this->bws_pro_block_links(); ?>
				</div>
			<?php }
		}

		/**
		 *
		 */
		public function tab_display() { ?>
			<h3 class="bws_tab_label"><?php _e( 'Display Settings', 'google-one' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<div class="bws_pro_version_bloc">
				<div class="bws_pro_version_table_bloc">
					<div class="bws_table_bg"></div>
					<table class="form-table bws_pro_version">
						<tr>
							<td>
								<?php _e( 'Please choose the necessary post types (or single pages) where Google buttons will be displayed:', 'google-one' ); ?>
							</td>
						</tr>
						<tr>
							<td>
								<label>
									<input disabled="disabled" checked="checked" type="checkbox" name="jstree_url" value="1" />
									<?php _e( "Show URL for pages", 'google-one' );?>
								</label>
							</td>
						</tr>
						<tr>
							<td>
								<img src="<?php echo plugins_url( 'images/pro_screen_1.png', dirname( __FILE__ ) ); ?>" alt="<?php _e( "Example of the site's pages tree", 'google-one' ); ?>" title="<?php _e( "Example of site pages' tree", 'google-one' ); ?>" />
							</td>
						</tr>
					</table>
				</div>
				<?php $this->bws_pro_block_links(); ?>
			</div>
		<?php }
	}
}