<?php
if ( ! function_exists( 'gglplsn_plugins_loaded' ) ) {
	function gglplsn_plugins_loaded() {
		/* Internationalization, first(!) */
		load_plugin_textdomain( 'google-one', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

if ( ! function_exists ( 'gglplsn_init' ) ) {
	function gglplsn_init() {
		global $gglplsn_plugin_info, $gglplsn_lang_codes;

		if ( empty( $gglplsn_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$gglplsn_plugin_info = get_plugin_data( __FILE__ );
		}

				/* Get/Register and check settings for plugin */
		if ( ! is_admin() || ( isset( $_GET['page'] ) && ( "google-plus-one.php" == $_GET['page'] || "social-buttons.php" == $_GET['page'] ) ) ) {
			gglplsn_settings();
			$gglplsn_lang_codes = array(
				'af' => "Afrikaans", 'am' => "Amharic", 'ar' => "Arabic", 'eu' => "Basque", 'bn' => "Bengali", 'bg' => "Bulgarian", 'ca' => "Catalan", 'zh-HK' => "Chinese (Hong Kong)", 'zh-CN' => "Chinese (Simplified)", 'zh-TW' => "Chinese (Traditional)", 'hr' => "Croatian", 'cs' => "Czech", 'da' => "Danish", 'nl' => "Dutch", 'en-GB' => "English (UK)", 'en' => "English (US)", 'et' => "Estonian", 'fil' => "Filipino", 'fi' => "Finnish", 'fr' => "French", 'fr-CA' => "French (Canadian)", 'gl' => "Galician", 'de' => "German", 'el' => "Greek", 'gu' => "Gujarati", 'iw' => "Hebrew", 'hi' => "Hindi", 'hu' => "Hungarian", 'is' => "Icelandic", 'id' => "Indonesian", 'it' => "Italian", 'ja' => "Japanese", 'kn' => "Kannada", 'ko' => "Korean", 'lv' => "Latvian", 'lt' => "Lithuanian", 'ms' => "Malay", 'ml' => "Malayalam", 'mr' => "Marathi", 'no' => "Norwegian", 'fa' => "Persian", 'pl' => "Polish", 'pt-BR' => "Portuguese (Brazil)", 'pt-PT' => "Portuguese (Portugal)", 'ro' => "Romanian", 'ru' => "Russian", 'sr' => "Serbian", 'sk' => "Slovak", 'sl' => "Slovenian", 'es' => "Spanish", 'es-419' => "Spanish (Latin America)", 'sw' => "Swahili", 'sv' => "Swedish", 'ta' => "Tamil", 'te' => "Telugu", 'th' => "Thai", 'tr' => "Turkish", 'uk' => "Ukrainian", 'ur' => "Urdu", 'vi' => "Vietnamese", 'zu' => "Zulu"
			);
		}
	}
}

/* Function for admin_init */
if ( ! function_exists( 'gglplsn_admin_init' ) ) {
	function gglplsn_admin_init() {
		global $bws_plugin_info, $gglplsn_plugin_info, $bws_shortcode_list;

				$bws_shortcode_list['gglplsn'] = array( 'name' => 'Google +1', 'js_function' => 'gglplsn_shortcode_init'  );
	}
}

if ( ! function_exists ( 'gglplsn_settings' ) ) {
	function gglplsn_settings() {
		global $gglplsn_options, $gglplsn_plugin_info, $gglplsn_option_defaults;

		/* Default options */
		$gglplsn_option_defaults		=	array(
			'plugin_option_version'		=>	$gglplsn_plugin_info["Version"],
			'plus_one_js'				=>	1,
			'plus_one_annotation'		=>	'none',
			'plus_one_size'				=>	'standard',
			'plus_one_annotation_type'	=>	'standard',
			'share_js'					=>	0,
			'share_size'				=>	20,
			'share_annotation'			=>	'none',
			'share_annotation_type'		=>	'standard',
			'follow_js'					=>	0,
			'follow_size'				=>	20,
			'follow_annotation'			=>	'none',
			'follow_relationship'		=>	'author',
			'follow_id'					=>	'',
			'hangout_js'				=>	0,
			'hangout_topic'				=>	'',
			'hangout_topic_title'		=>	1,
			'hangout_size'				=>	'standard',
			'hangout_type'				=>	'normal',
			'hangout_invite_type'		=>	array(),
			'hangout_invite_id'			=>	array(),
			'badge_js'					=>	0,
			'badge_type'				=>	'person',
			'badge_id'					=>	'',
			'badge_layout'				=>	'portrait',
			'badge_show_cover'			=>	false,
			'badge_show_tagline'		=>	false,
			'badge_show_owners'			=>	false,
			'badge_theme'				=>	'light',
			'badge_width'				=>	300,
			'position'					=>	'before_post',
			'posts'						=>	1,
			'pages'						=>	1,
			'homepage'					=>	1,
			'lang'						=>	'en',
			'use_multilanguage_locale'	=>	0,
			'display_settings_notice'	=>	1,
			'first_install'				=>	strtotime( "now" ),
			'suggest_feature_banner'	=>	1
		);

		if ( ! get_option( 'gglplsn_options' ) )
			add_option( 'gglplsn_options', $gglplsn_option_defaults );

		$gglplsn_options = get_option( 'gglplsn_options' );

		if ( ! isset( $gglplsn_options['plugin_option_version'] ) || $gglplsn_options['plugin_option_version'] != $gglplsn_plugin_info["Version"] ) {

			/**
			 * @deprecated since 1.2.8
			 * @todo remove
			 */
			if ( isset( $gglplsn_options['annotation'] ) ) {
				if ( is_numeric( $gglplsn_options['annotation'] ) ) {
					$gglplsn_options['plus_one_annotation'] = 1 == $gglplsn_options['annotation'] ? 'bubble' : 'none' ;
				} else {
					$gglplsn_options['plus_one_annotation'] = $gglplsn_options['annotation'];
				}

				unset( $gglplsn_options['annotation'] );
			}

			/**
			 * @deprecated since 1.2.8
			 * @todo remove
			 */
			if ( isset( $gglplsn_options['js'] ) ) {
				$gglplsn_options['plus_one_js'] = $gglplsn_options['js'];
				$gglplsn_options['plus_one_size'] = $gglplsn_options['size'];
				unset( $gglplsn_options['js'] );
				unset( $gglplsn_options['size'] );
			}

			$gglplsn_option_defaults['display_settings_notice'] = 0;
			$gglplsn_options = array_merge( $gglplsn_option_defaults, $gglplsn_options );
			$gglplsn_options['plugin_option_version'] = $gglplsn_plugin_info["Version"];
			/* show pro features */
			$gglplsn_options['hide_premium_options'] = array();

			update_option( 'gglplsn_options', $gglplsn_options );
		}
	}
}

/* Add settings page in admin area */
if ( ! function_exists( 'gglplsn_options' ) ) {
	function gglplsn_options() {
		global $gglplsn_options, $wp_version, $gglplsn_plugin_info, $gglplsn_option_defaults, $gglplsn_lang_codes;

		$message = $error = "";
		$plugin_basename = plugin_basename( __FILE__ );

		if ( ! function_exists( 'get_plugins' ) || ! function_exists( 'is_plugin_active' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		$all_plugins = get_plugins();

		/* Save data for settings page */
		if ( isset( $_REQUEST['gglplsn_form_submit'] ) && check_admin_referer( $plugin_basename, 'gglplsn_nonce_name' ) ) {
			if ( isset( $_POST['bws_hide_premium_options'] ) ) {
				$hide_result = bws_hide_premium_options( $gglplsn_options );
				$gglplsn_options = $hide_result['options'];
			}

			$gglplsn_options['plus_one_js']					=	isset( $_REQUEST['gglplsn_plus_one_js'] ) ? 1 : 0;
			$gglplsn_options['plus_one_annotation']			=	esc_html( $_REQUEST['gglplsn_plus_one_annotation'] );
			$gglplsn_options['plus_one_size']				=	esc_html( $_REQUEST['gglplsn_plus_one_size'] );
			$gglplsn_options['plus_one_annotation_type']	=	esc_html( $_REQUEST['gglplsn_plus_one_annotation_type'] );
			$gglplsn_options['share_js']					=	isset( $_REQUEST['gglplsn_share_js'] ) ? 1 : 0;
			$gglplsn_options['share_size']					=	intval( $_REQUEST['gglplsn_share_size'] );
			$gglplsn_options['share_annotation_type']		=	esc_html( $_REQUEST['gglplsn_share_annotation_type'] );
			$gglplsn_options['share_annotation']			=	esc_html( $_REQUEST['gglplsn_share_annotation'] );
			$gglplsn_options['follow_js']					=	isset( $_REQUEST['gglplsn_follow_js'] ) ? 1 : 0;
			$gglplsn_options['follow_annotation']			=	esc_html( $_REQUEST['gglplsn_follow_annotation'] );
			$gglplsn_options['follow_size']					=	intval( $_REQUEST['gglplsn_follow_size'] );
			$gglplsn_options['follow_id']					=	esc_html( $_REQUEST['gglplsn_follow_id'] );
			$gglplsn_options['follow_relationship']			=	esc_html( $_REQUEST['gglplsn_follow_relationship'] );
			$gglplsn_options['hangout_js']					=	isset( $_REQUEST['gglplsn_hangout_js'] ) ? 1 : 0;
			$gglplsn_options['hangout_topic']				=	esc_html( $_REQUEST['gglplsn_hangout_topic'] );
			$gglplsn_options['hangout_topic_title'] 		=	esc_html( $_REQUEST['gglplsn_hangout_topic_title'] );
			$gglplsn_options['hangout_size']				=	esc_html( $_REQUEST['gglplsn_hangout_size'] );
			$gglplsn_options['hangout_type']				=	esc_html( $_REQUEST['gglplsn_hangout_type'] );
			$gglplsn_options['hangout_invite_type']			=	array();
			$gglplsn_options['hangout_invite_id']			=	array();
			$gglplsn_options['badge_js']					=	isset( $_REQUEST['gglplsn_badge_js'] ) ? 1 : 0;
			$gglplsn_options['badge_type']					=	esc_html( $_REQUEST['gglplsn_badge_type'] );
			$gglplsn_options['badge_id']					=	esc_html( $_REQUEST['gglplsn_badge_id'] );
			$gglplsn_options['badge_layout']				=	( 'portrait' == $_REQUEST['gglplsn_badge_layout'] ) ? 'portrait' : 'landscape';
			$gglplsn_options['badge_show_cover']			=	isset( $_REQUEST['gglplsn_badge_show_cover'] ) ? true : false;
			$gglplsn_options['badge_show_tagline']			=	isset( $_REQUEST['gglplsn_badge_show_tagline'] ) ? true : false;
			$gglplsn_options['badge_show_owners']			=	isset( $_REQUEST['gglplsn_badge_show_owners'] ) ? true : false;
			$gglplsn_options['badge_theme']					=	esc_html( $_REQUEST['gglplsn_badge_theme'] );
			$gglplsn_options['badge_width']					=	intval( $_REQUEST['gglplsn_badge_width'] );
			$gglplsn_options['position']					=	esc_html( $_REQUEST['gglplsn_position'] );
			$gglplsn_options['lang']						=	esc_html( $_REQUEST['gglplsn_lang'] );
			$gglplsn_options['posts']						=	isset( $_REQUEST['gglplsn_posts'] ) ? 1 : 0 ;
			$gglplsn_options['pages']						=	isset( $_REQUEST['gglplsn_pages'] ) ? 1 : 0 ;
			$gglplsn_options['homepage']					=	isset( $_REQUEST['gglplsn_homepage'] ) ? 1 : 0 ;
			$gglplsn_options['use_multilanguage_locale']	=	isset( $_REQUEST['gglplsn_use_multilanguage_locale'] ) ? 1 : 0;

			if ( $gglplsn_options['badge_width'] < 180 && 'portrait' == $gglplsn_options['badge_layout'] ) {
				$gglplsn_options['badge_width'] = 180;
			} elseif ( $gglplsn_options['badge_width'] < 273 && 'landscape' == $gglplsn_options['badge_layout'] ) {
				$gglplsn_options['badge_width'] = 273;
			} elseif ( $gglplsn_options['badge_width'] > 450 ) {
				$gglplsn_options['badge_width'] = 450;
			}

			$count = 0;
			/* Save invites if Java Script is enabled */
			if ( ! isset( $_REQUEST['gglplsn_hangout_invite_type_hidden_noscript'] ) && ! isset( $_REQUEST['gglplsn_hangout_invite_del_noscript'] ) && ! isset( $_REQUEST['gglplsn_hangout_invite_add_noscript'] ) && isset( $_REQUEST['gglplsn_hangout_invite_type_hidden'] ) ) {
				foreach( $_REQUEST['gglplsn_hangout_invite_type_hidden'] as $value ) {
					$gglplsn_options['hangout_invite_type'][ $count ] = $value;
					$gglplsn_options['hangout_invite_id'][ $count ] = sanitize_text_field( $_REQUEST['gglplsn_hangout_invite_id_hidden'][ $count ] );
					$count ++;
				}
			/* Delete selected invites if Java Script is disabled */
			} elseif ( isset( $_REQUEST['gglplsn_hangout_invite_del_noscript'] ) && isset( $_REQUEST['gglplsn_hangout_invite_type_hidden_noscript'] ) ) {
				$save_count = 0;
				foreach( $_REQUEST['gglplsn_hangout_invite_type_hidden_noscript'] as $value ) {
					if ( ! isset( $_REQUEST['gglplsn_hangout_invite_checkbox'][ $count ] ) ) {
						$gglplsn_options['hangout_invite_type'][ $save_count ] = $value;
						$gglplsn_options['hangout_invite_id'][ $save_count ] = $_REQUEST['gglplsn_hangout_invite_id_hidden_noscript'][ $count ];
						$save_count ++;
					}
					$count ++;
				}
			/* Save added invites if Java Script is disabled */
			} elseif ( isset( $_REQUEST['gglplsn_hangout_invite_type_hidden_noscript'] ) ) {
				foreach( $_REQUEST['gglplsn_hangout_invite_type_hidden_noscript'] as $value ) {
					$gglplsn_options['hangout_invite_type'][ $count ] = $value;
					$gglplsn_options['hangout_invite_id'][ $count ] = $_REQUEST['gglplsn_hangout_invite_id_hidden_noscript'][ $count ];
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
					$error = __( 'Please, select the invitation type', 'google-one' );
				} elseif ( empty( $_REQUEST['gglplsn_hangout_invite_id_noscript'] ) ) {
					$error = __( "Invitation field can't be empty", 'google-one' );
				}

				if ( ! empty( $gglplsn_options['hangout_invite_type'] ) && '' == $error ) {
					$phone_added = array_search( 'PHONE', $gglplsn_options['hangout_invite_type'] );
					if ( false !== $phone_added && 'PHONE' == $_REQUEST['gglplsn_hangout_invite_type_select'] ) {
						$error = __( 'Only one phone number can be added', 'google-one' );
					} elseif ( false !== $phone_added && 'PHONE' != $_REQUEST['gglplsn_hangout_invite_type_select'] ) {
						$error = __( "You can't add the invitation because the phone number is already added", 'google-one' );
					} elseif ( false === $phone_added && 'PHONE' == $_REQUEST['gglplsn_hangout_invite_type_select'] ) {
						$error = __( "You can't add the phone number because another invitation type is already added", 'google-one' );
					}

					foreach( $gglplsn_options['hangout_invite_id'] as $value ) {
						if ( $_REQUEST['gglplsn_hangout_invite_id_noscript'] == $value ) {
							$error = __( 'Is already added', 'google-one' );
							break;
						}
					}
				}

				/* Add invite if Java Script is disabled */
				if ( '' == $error ) {
					$gglplsn_options['hangout_invite_type'][ $count ] = $_REQUEST['gglplsn_hangout_invite_type_select'];
					$gglplsn_options['hangout_invite_id'][ $count ] = sanitize_text_field( $_REQUEST['gglplsn_hangout_invite_id_noscript'] );
				}
			}

			$message = __( 'Settings saved', 'google-one' );
			update_option( 'gglplsn_options', $gglplsn_options );
		}

		?>
					<div class="updated fade below-h2" <?php if ( '' == $message || "" != $error ) echo 'style="display:none"'; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<?php bws_show_settings_notice(); ?>
			<div class="error below-h2" <?php if ( "" == $error ) echo 'style="display:none"'; ?>><p><strong><?php echo $error; ?></strong></p></div>
			<?php ?>
					<div id="gglplsn_settings_form_block">
						<p><?php _e( 'For the correct work of the button do not use it locally or on a free hosting', 'google-one' ); ?><br /></p>
						<div><?php $icon_shortcode = ( "google-plus-one.php" == $_GET['page'] ) ? plugins_url( 'bws_menu/images/shortcode-icon.png', __FILE__ ) : plugins_url( 'social-buttons-pack/bws_menu/images/shortcode-icon.png' );
						printf(
							__( "If you'd like to add Google Buttons to your page or post, please use %s button", 'google-one' ),
							'<span class="bws_code"><img style="vertical-align: sub;" src="' . $icon_shortcode . '" alt=""/></span>' ); ?>
							<div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help">
								<div class="bws_hidden_help_text" style="min-width:180px;">
									<?php printf(
										__( "You can add Google Buttons to your page or post by clicking on %s button in the content edit block using the Visual mode. If the button isn't displayed, please use the shortcode %s to show the Google +1 Button, or use parameter 'display', e.g. %s to display these buttons", 'google-one' ),
										'<code><img style="vertical-align: sub;" src="' . $icon_shortcode . '" alt="" /></code>',
										'<code>[bws_googleplusone]</code>',
										'<br><code>[bws_googleplusone display="plusone,share,follow,hangout,badge"]</code>'
									); ?>
								</div>
							</div>
						</div>
						<div class="gglplsn-form">
							<form method="post" action="" class="bws_form">
								<table class="form-table gglplsn_form-table">
									<tbody>
										<tr valign="top">
											<th><?php _e( 'Display Google Buttons', 'google-one' ); ?></th>
											<td>
												<fieldset>
													<label>
														<input type="checkbox" name="gglplsn_plus_one_js"<?php if ( 1 == $gglplsn_options['plus_one_js'] ) echo 'checked="checked"'; ?> value="1" />
														<?php _e( 'Google +1', 'google-one' ); ?>
													</label>
													<br />
													<label>
														<input type="checkbox" name="gglplsn_share_js"<?php if ( 1 == $gglplsn_options['share_js'] ) echo 'checked="checked"'; ?> value="1" />
														<?php _e( 'Share', 'google-one' ); ?>
													</label>
													<br />
													<label>
														<input type="checkbox" name="gglplsn_follow_js"<?php if ( 1 == $gglplsn_options['follow_js'] ) echo 'checked="checked"'; ?> value="1" />
														<?php _e( 'Follow', 'google-one' ); ?>
													</label>
													<span class="bws_info gglplsn_notice gglplsn-follow-notice gglplsn-unvisible-notice">
														<?php if ( empty( $gglplsn_options['follow_id'] ) ) { ?>
															( <?php _e( 'To see this button, please', 'google-one' ); ?>
															<a class="gglplsn-follow-focus"><?php _e( 'enter', 'google-one' ) ?></a>
															<?php _e( 'the Google+ ID', 'google-one' ); ?> )
														<?php } ?>
													</span>
													<br />
													<label>
														<input type="checkbox" name="gglplsn_hangout_js"<?php if ( 1 == $gglplsn_options['hangout_js'] ) echo 'checked="checked"'; ?> value="1" />
														<?php _e( 'Hangout', 'google-one' ); ?>
													</label>
													<br />
													<label>
														<input type="checkbox" name="gglplsn_badge_js"<?php if ( 1 == $gglplsn_options['badge_js'] ) echo 'checked="checked"'; ?> value="1" />
														<?php _e( 'Badge', 'google-one' ); ?>
													</label>
													<span class="bws_info gglplsn_notice gglplsn-badge-notice gglplsn-unvisible-notice">
														<?php if ( empty( $gglplsn_options['badge_id'] ) ) { ?>
															( <?php _e( 'To see this button, please', 'google-one' ); ?>
															<a class="gglplsn-badge-focus"><?php _e( 'enter', 'google-one' ) ?></a>
															<?php _e( 'the Google+ ID', 'google-one' ); ?> )
														<?php } ?>
													</span>
												</fieldset>
											</td>
										</tr>
										<tr>
											<th scope="row"><?php _e( 'Language', 'google-one' ); ?></th>
											<td>
												<fieldset>
													<select name="gglplsn_lang">
														<?php foreach ( $gglplsn_lang_codes as $key => $val ) {
															echo '<option value="' . $key . '"';
															if ( $key == $gglplsn_options['lang'] ) {
																echo ' selected="selected"';
															}
															echo '>' . esc_html ( $val ) . '</option>';
														} ?>
													</select>
													<span class="bws_info">(<?php _e( 'Select the language to display information on the button', 'google-one' ); ?>)</span>
													<br />
													<label>
														<?php if ( array_key_exists( 'multilanguage/multilanguage.php', $all_plugins ) || array_key_exists( 'multilanguage-pro/multilanguage-pro.php', $all_plugins ) ) {
															if ( is_plugin_active( 'multilanguage/multilanguage.php' ) || is_plugin_active( 'multilanguage-pro/multilanguage-pro.php' ) ) { ?>
																<input type="checkbox" name="gglplsn_use_multilanguage_locale" value="1" <?php if ( 1 == $gglplsn_options["use_multilanguage_locale"] ) echo 'checked="checked"'; ?> />
																<?php _e( 'Use the current site language', 'google-one' ); ?><span class="bws_info">(<?php _e( 'Using', 'google-one' ); ?> Multilanguage by BestWebSoft)</span>
															<?php } else { ?>
																<input disabled="disabled" type="checkbox" name="gglplsn_use_multilanguage_locale" value="1" />
																<?php _e( 'Use the current site language', 'google-one' ); ?>
																<span class="bws_info">(<?php _e( 'Using', 'google-one' ); ?> Multilanguage by BestWebSoft)
																	<a href="<?php echo bloginfo( "url" ); ?>/wp-admin/plugins.php"><?php _e( 'Activate', 'google-one' ); ?> Multilanguage</a>
																</span>
															<?php }
														} else { ?>
															<input disabled="disabled" type="checkbox" name="gglplsn_use_multilanguage_locale" value="1" />
															<?php _e( 'Use the current site language', 'google-one' ); ?>
															<span class="bws_info">(<?php _e( 'Using', 'google-one' ); ?> Multilanguage by BestWebSoft)
																<a href="http://bestwebsoft.com/products/multilanguage/?k=4f26802e271bc07146a382140164abc1&pn=102&v=<?php echo $gglplsn_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>"><?php _e( 'Download', 'google-one' ); ?> Multilanguage</a>
															</span>
														<?php } ?>
													</label>
												</fieldset>
											</td>
										</tr>
										<tr>
											<th scope="row"><?php _e( 'Buttons Position', 'google-one' ); ?></th>
											<td>
												<select name="gglplsn_position">
													<option value="before_post" <?php if ( 'before_post' == $gglplsn_options['position'] ) echo 'selected="selected"'; ?>><?php _e( 'Before', 'google-one' ); ?></option>
													<option value="after_post" <?php if ( 'after_post' == $gglplsn_options['position'] ) echo 'selected="selected"'; ?>><?php _e( 'After', 'google-one' ); ?></option>
													<option value="afterandbefore" <?php if ( 'afterandbefore' == $gglplsn_options['position'] ) echo 'selected="selected"'; ?>><?php _e( 'Before And After', 'google-one' ); ?></option>
													<option value="only_shortcode" <?php if ( 'only_shortcode' == $gglplsn_options['position'] ) echo 'selected="selected"'; ?>><?php _e( 'Only Shortcode', 'google-one' ); ?></option>
												</select>
												<span class="bws_info">(<?php _e( 'Please select location for the buttons on the page', 'google-one' ); ?>)</span>
											</td>
										</tr>
										<tr>
											<th scope="row"><?php _e( 'Show buttons', 'google-one' ); ?></th>
											<td>
												<p>
													<label>
														<input type="checkbox" name="gglplsn_posts" <?php if ( ! empty( $gglplsn_options['posts'] ) ) echo 'checked="checked"'; ?> value="1" />
														<?php _e( 'Show in posts', 'google-one' ); ?>
													</label>
												</p>
												<p>
													<label>
														<input type="checkbox" name="gglplsn_pages" <?php if ( ! empty( $gglplsn_options['pages'] ) ) echo 'checked="checked"'; ?>  value="1" />
														<?php _e( 'Show in pages', 'google-one' ); ?>
													</label>
												</p>
												<p>
													<label>
														<input type="checkbox" name="gglplsn_homepage" <?php if ( ! empty( $gglplsn_options['homepage'] ) ) echo 'checked="checked"'; ?>  value="1" />
														<?php _e( 'Show on the homepage', 'google-one' ); ?>
													</label>
												</p>
												<p>
													<span class="bws_info">(<?php _e( 'Please select the page on which you want to see the buttons', 'google-one' ); ?>)</span>
												</p>
											</td>
										</tr>
										<tr class="gglplsn-plus-one-options gglplsn-first <?php if ( 0 == $gglplsn_options['plus_one_js'] ) echo 'gglplsn-hide-option'; ?>">
											<th colspan="2"><?php _e( 'Settings for Google +1 Button', 'google-one' ); ?></th>
										</tr>
									</tbody>
								</table>
																<table class="form-table gglplsn_form-table">
									<tbody>
										<tr class="gglplsn-plus-one-options <?php if ( 0 == $gglplsn_options['plus_one_js'] ) echo 'gglplsn-hide-option'; ?>">
											<th scope="row"><?php _e( 'Size', 'google-one' ); ?></th>
											<td>
												<select name="gglplsn_plus_one_size">
													<option value="standard" <?php if ( 'standard' == $gglplsn_options['plus_one_size'] ) echo 'selected="selected"'; ?>><?php _ex( 'Standard', 'for:size', 'google-one' ); ?></option>
													<option value="small" <?php if ( 'small' == $gglplsn_options['plus_one_size'] ) echo 'selected="selected"'; ?>><?php _e( 'Small', 'google-one' ); ?></option>
													<option value="medium" <?php if ( 'medium' == $gglplsn_options['plus_one_size'] ) echo 'selected="selected"'; ?>><?php _e( 'Medium', 'google-one' ); ?></option>
													<option value="tall" <?php if ( 'tall' == $gglplsn_options['plus_one_size'] ) echo 'selected="selected"'; ?>><?php _e( 'Tall', 'google-one' ); ?></option>
												</select>
											</td>
										</tr>
										<tr class="gglplsn-plus-one-options <?php if ( 0 == $gglplsn_options['plus_one_js'] ) echo 'gglplsn-hide-option'; ?>">
											<th><?php _e( 'Annotation', 'google-one' ); ?></th>
											<td>
												<select name="gglplsn_plus_one_annotation">
													<option value="inline" <?php if ( 'inline' == $gglplsn_options['plus_one_annotation'] ) echo 'selected="selected"'; ?>><?php _e( 'Inline', 'google-one' ); ?></option>
													<option value="bubble" <?php if ( 'bubble' == $gglplsn_options['plus_one_annotation'] ) echo 'selected="selected"'; ?>><?php _e( 'Bubble', 'google-one' ); ?></option>
													<option value="none" <?php if ( 'none' == $gglplsn_options['plus_one_annotation'] ) echo 'selected="selected"'; ?>><?php _e( 'None', 'google-one' ); ?></option>
												</select>
												<br />
												<span class="bws_info">(<?php _e( 'Display counters showing how many times your article has been liked', 'google-one' ); ?>)</span>
											</td>
										</tr>
										<tr class="gglplsn-plus-one-annotation-type <?php if ( 0 == $gglplsn_options['plus_one_js'] || 'inline' != $gglplsn_options['plus_one_annotation'] ) echo 'gglplsn-hide-option'; ?>">
												<th scope="row"><?php _e( 'Annotation Type', 'google-one' ); ?></th>
												<td>
													<select name="gglplsn_plus_one_annotation_type">
														<option value="standard" <?php if ( 'standard' == $gglplsn_options['plus_one_annotation_type'] ) echo 'selected="selected"';?>><?php _ex( 'Standard', 'for:annotation type', 'google-one' ) ?></option>
														<option value="short" <?php if ( 'short' == $gglplsn_options['plus_one_annotation_type'] ) echo 'selected="selected"';?>><?php _e( 'Short', 'google-one' ) ?></option>
													</select>
												</td>
											</tr>
										<tr class="gglplsn-share-options gglplsn-first <?php if ( 0 == $gglplsn_options['share_js'] ) echo 'gglplsn-hide-option'; ?>">
											<th colspan="2"><?php _e( 'Settings for Share Button', 'google-one' ); ?></th>
										</tr>
										<tr class="gglplsn-share-options <?php if ( 0 == $gglplsn_options['share_js'] ) echo 'gglplsn-hide-option'; ?>">
											<th><?php _e( 'Annotation', 'google-one' ); ?></th>
											<td>
												<select name="gglplsn_share_annotation">
													<option value="inline" <?php if ( 'inline' == $gglplsn_options['share_annotation'] ) echo 'selected="selected"'; ?>><?php _e( 'Inline', 'google-one' ); ?></option>
													<option value="bubble" <?php if ( 'bubble' == $gglplsn_options['share_annotation'] ) echo 'selected="selected"'; ?>><?php _e( 'Bubble', 'google-one' ); ?></option>
													<option value="vertical-bubble" <?php if ( 'vertical-bubble' == $gglplsn_options['share_annotation'] ) echo 'selected="selected"'; ?>><?php _e( 'Vertical-bubble', 'google-one' ); ?></option>
													<option value="none" <?php if ( 'none' == $gglplsn_options['share_annotation'] ) echo 'selected="selected"'; ?>><?php _e( 'None', 'google-one' ); ?></option>
												</select>
												<p>
													<span class="bws_info">(<?php _e( 'Display the number of users who have shared the page', 'google-one' ); ?>)</span>
												</p>
											</td>
										</tr>
										<tr class="gglplsn-share-size <?php if ( 0 == $gglplsn_options['share_js'] || 'vertical-bubble' == $gglplsn_options['share_annotation'] ) echo 'gglplsn-hide-option'; ?>">
											<th scope="row"><?php _e( 'Size', 'google-one' ); ?></th>
											<td>
												<select name="gglplsn_share_size">
													<option value="15" <?php if ( 15 == $gglplsn_options['share_size'] ) echo 'selected="selected"'; ?>><?php _e( 'Small', 'google-one' ); ?></option>
													<option value="20" <?php if ( 20 == $gglplsn_options['share_size'] ) echo 'selected="selected"'; ?>><?php _e( 'Medium', 'google-one' ); ?></option>
													<option value="24" <?php if ( 24 == $gglplsn_options['share_size'] ) echo 'selected="selected"'; ?>><?php _e( 'Large', 'google-one' ); ?></option>
												</select>
											</td>
										</tr>
										<tr class="gglplsn-share-annotation-type <?php if ( 0 == $gglplsn_options['share_js'] || 'inline' != $gglplsn_options['share_annotation'] ) echo 'gglplsn-hide-option'; ?>">
											<th scope="row"><?php _e( 'Annotation Type', 'google-one' ); ?></th>
											<td>
												<select name="gglplsn_share_annotation_type">
													<option value="standard" <?php if ( 'standard' == $gglplsn_options['share_annotation_type'] ) echo 'selected="selected"';?>><?php echo _ex( 'Standard', 'for:annotation type', 'google-one' ) ?></option>
													<option value="short" <?php if ( 'short' == $gglplsn_options['share_annotation_type'] ) echo 'selected="selected"';?>><?php _e( 'Short', 'google-one' ) ?></option>
												</select>
											</td>
										</tr>
										<tr class="gglplsn-follow-options gglplsn-first <?php if ( 0 == $gglplsn_options['follow_js'] ) echo 'gglplsn-hide-option'; ?>">
											<th colspan="2"><?php _e( 'Settings for Follow Button', 'google-one' ); ?></th>
										</tr>
										<tr class="gglplsn-follow-options <?php if ( 0 == $gglplsn_options['follow_js'] ) echo 'gglplsn-hide-option'; ?>">
											<th><?php _e( 'Google+ ID', 'google-one' ); ?></th>
											<td>
												<input type="text" <?php if ( 1 == $gglplsn_options['follow_js'] ) { echo 'required="required"'; } ?> name="gglplsn_follow_id" value="<?php echo $gglplsn_options['follow_id']; ?>" />
												<p>
													<span class="bws_info">(<?php echo __( 'Enter the Google+ ID, e.g.', 'google-one' ) . '&nbsp;"12345678912345678912"&nbsp;' . __( 'or', 'google-one') . '&nbsp;"+YouName"'; ?>)</span>
												</p>
											</td>
										</tr>
										<tr class="gglplsn-follow-options <?php if ( 0 == $gglplsn_options['follow_js'] ) echo 'gglplsn-hide-option'; ?>">
											<th><?php _e( 'Relationship', 'google-one' ); ?></th>
											<td>
												<select name="gglplsn_follow_relationship">
													<option value="author" <?php if ( 'author' == $gglplsn_options['follow_relationship'] ) echo 'selected="selected"'; ?>><?php _e( 'Author', 'google-one' ); ?></option>
													<option value="publisher" <?php if ( 'publisher' == $gglplsn_options['follow_relationship'] ) echo 'selected="selected"'; ?>><?php _e( 'Publisher', 'google-one' ); ?></option>
												</select>
												<p>
													<span class="bws_info">(<?php _e( 'Describes your relationship to content of the page, where the button is embedded', 'google-one' ); ?>)</span>
												</p>
											</td>
										</tr>
										<tr class="gglplsn-follow-options <?php if ( 0 == $gglplsn_options['follow_js'] ) echo 'gglplsn-hide-option'; ?>">
											<th scope="row"><?php _e( 'Size', 'google-one' ); ?></th>
											<td>
												<select name="gglplsn_follow_size">
													<option value="15" <?php if ( 15 == $gglplsn_options['follow_size'] ) echo 'selected="selected"'; ?>><?php _e( 'Small', 'google-one' ); ?></option>
													<option value="20" <?php if ( 20 == $gglplsn_options['follow_size'] ) echo 'selected="selected"'; ?>><?php _e( 'Medium', 'google-one' ); ?></option>
													<option value="24" <?php if ( 24 == $gglplsn_options['follow_size'] ) echo 'selected="selected"'; ?>><?php _e( 'Large', 'google-one' ); ?></option>
												</select>
											</td>
										</tr>
										<tr class="gglplsn-follow-options <?php if ( 0 == $gglplsn_options['follow_js'] ) echo 'gglplsn-hide-option'; ?>">
											<th><?php _e( 'Annotation', 'google-one' ); ?></th>
											<td>
												<select name="gglplsn_follow_annotation">
													<option value="bubble" <?php if ( 'bubble' == $gglplsn_options['follow_annotation'] ) echo 'selected="selected"'; ?>><?php _e( 'Bubble', 'google-one' ); ?></option>
													<option value="vertical-bubble" <?php if ( 'vertical-bubble' == $gglplsn_options['follow_annotation'] ) echo 'selected="selected"'; ?>><?php _e( 'Vertical-bubble', 'google-one' ); ?></option>
													<option value="none" <?php if ( 'none' == $gglplsn_options['follow_annotation'] ) echo 'selected="selected"'; ?>><?php _e( 'None', 'google-one' ); ?></option>
												</select>
												<p>
													<span class="bws_info">(<?php _e( 'Display the number of users who are following this page or person', 'google-one' ); ?>)</span>
												</p>
											</td>
										</tr>
										<tr class="gglplsn-hangout-options gglplsn-first <?php if ( 0 == $gglplsn_options['hangout_js'] ) echo 'gglplsn-hide-option'; ?>">
											<th colspan="2"><?php _e( 'Settings for Hangout Button', 'google-one' ); ?></th>
										</tr>
										<tr class="gglplsn-hangout-options <?php if ( 0 == $gglplsn_options['hangout_js'] ) echo 'gglplsn-hide-option'; ?>">
											<th><?php _e( 'Topic', 'google-one' ); ?></th>
											<td>
												<label class="gglplsn-hangout-topic">
													<input type="radio" name="gglplsn_hangout_topic_title" value="1" <?php if ( 1 == $gglplsn_options['hangout_topic_title'] ) echo 'checked="checked"'; ?> />
													<?php _e( 'Use the title of the page', 'google-one' ); ?>
												</label>
												<br />
												<label class="gglplsn-hangout-topic">
													<input type="radio" name="gglplsn_hangout_topic_title" value="0" class="gglplsn-hangout-topic-radio" <?php if ( 1 != $gglplsn_options['hangout_topic_title'] ) echo 'checked="checked"'; ?> />
													<input type="text" name="gglplsn_hangout_topic" class="gglplsn-hangout-topic-text" value="<?php echo $gglplsn_options['hangout_topic']; ?>" />
												</label>
											</td>
										</tr>
										<tr class="gglplsn-hangout-options <?php if ( 0 == $gglplsn_options['hangout_js'] ) echo 'gglplsn-hide-option'; ?>">
											<th><?php _e( 'Size', 'google-one' ); ?></th>
											<td>
												<select name="gglplsn_hangout_size">
													<option value="standard" <?php if ( 'standard' == $gglplsn_options['hangout_size'] ) echo 'selected="selected"'; ?>><?php _ex( 'Standard', 'for:size', 'google-one' ); ?></option>
													<option value="narrow" <?php if ( 'narrow' == $gglplsn_options['hangout_size'] ) echo 'selected="selected"'; ?>><?php _e( 'Narrow', 'google-one' ); ?></option>
													<option value="wide" <?php if ( 'wide' == $gglplsn_options['hangout_size'] ) echo 'selected="selected"'; ?>><?php _e( 'Wide', 'google-one' ); ?></option>
												</select>
											</td>
										</tr>
										<tr class="gglplsn-hangout-options <?php if ( 0 == $gglplsn_options['hangout_js'] ) echo 'gglplsn-hide-option'; ?>">
											<th><?php _e( 'Type', 'google-one' ); ?></th>
											<td>
												<select name="gglplsn_hangout_type">
													<option value="normal" <?php if ( 'normal' == $gglplsn_options['hangout_type'] ) echo 'selected="selected"'; ?>><?php _e( 'Normal', 'google-one' ); ?></option>
													<option value="onair" <?php if ( 'onair' == $gglplsn_options['hangout_type'] ) echo 'selected="selected"'; ?>><?php _e( 'Onair', 'google-one' ); ?></option>
													<option value="party" <?php if ( 'party' == $gglplsn_options['hangout_type'] ) echo 'selected="selected"'; ?>><?php _e( 'Party', 'google-one' ); ?></option>
													<option value="moderated" <?php if ( 'moderated' == $gglplsn_options['hangout_type'] ) echo 'selected="selected"'; ?>><?php _e( 'Moderated', 'google-one' ); ?></option>
												</select>
											</td>
										</tr>
										<tr class="gglplsn-hangout-options gglplsn-hangout-invite-type <?php if ( 0 == $gglplsn_options['hangout_js'] ) echo 'gglplsn-hide-option'; ?>">
											<th><?php _e( 'Invitation Type', 'google-one' ); ?></th>
											<td>
												<select id="gglplsn_hangout_invite_type" name="gglplsn_hangout_invite_type_select">
													<option value="" disabled="disabled" selected="selected"><?php _e( 'Select The Type', 'google-one' ); ?></option>
													<option value="PROFILE"><?php _e( 'Google+ Profile ID', 'google-one' ); ?></option>
													<option value="CIRCLE"><?php _e( 'Google+ Circle ID', 'google-one' ); ?></option>
													<option value="EMAIL"><?php _e( 'Email', 'google-one' ); ?></option>
													<option value="PHONE"><?php _e( 'Phone Number', 'google-one' ); ?></option>
												</select>
												<div class="tagchecklist gglplsn-view-invited" style="display:none;">
													<?php if ( ! empty( $gglplsn_options['hangout_invite_type'] ) ) {
														for ( $i = 0; $i < count( $gglplsn_options['hangout_invite_type'] ); $i++ ) { ?>
															<div>
																<input name="gglplsn_hangout_invite_type_hidden[]" value="<?php echo $gglplsn_options['hangout_invite_type'][ $i ] ?>" type="hidden" />
																<input name="gglplsn_hangout_invite_id_hidden[]" value="<?php echo $gglplsn_options['hangout_invite_id'][ $i ] ?>" type="hidden" />
																<span>
																	<a class="delbutton"></a>
																	<?php echo '&nbsp;' . $gglplsn_options['hangout_invite_id'][ $i ] ?>
																</span>
															</div>
														<?php }
													} ?>
												</div>
												<noscript>
													<div class="tagchecklist gglplsn-view-invited-noscript">
														<?php if ( ! empty( $gglplsn_options['hangout_invite_type'] ) ) {
															for ( $i = 0; $i < count( $gglplsn_options['hangout_invite_type'] ); $i++ ) { ?>
																<p>
																	<input name="gglplsn_hangout_invite_type_hidden_noscript[<?php echo $i; ?>]" value="<?php echo $gglplsn_options['hangout_invite_type'][ $i ]; ?>" type="hidden" />
																	<input name="gglplsn_hangout_invite_id_hidden_noscript[<?php echo $i; ?>]" value="<?php echo $gglplsn_options['hangout_invite_id'][ $i ]; ?>" type="hidden" />
																	<input type="checkbox" name="gglplsn_hangout_invite_checkbox[<?php echo $i; ?>]" id="gglplsn_hangout_invite_checkbox[<?php echo $i; ?>]" value="1" />
																	<label for="gglplsn_hangout_invite_checkbox[<?php echo $i; ?>]">
																		<?php echo $gglplsn_options['hangout_invite_id'][ $i ]; ?>
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
													<input type="text" id="gglplsn_hangout_invite_id_noscript" name="gglplsn_hangout_invite_id_noscript" />
													<input type="submit" name="gglplsn_hangout_invite_add_noscript" class="button tagadd" value="<?php _e( 'Add', 'google-one' ); ?>" id="gglplsn_hangout_invite_add_noscript" />
													<?php if ( ! empty( $gglplsn_options['hangout_invite_type'] ) ) { ?>
														<input type="submit" name="gglplsn_hangout_invite_del_noscript" class="button tagadd" value="<?php _e( 'Delete Selected', 'google-one' ); ?>" id="gglplsn_hangout_invite_del" />
													<?php } ?>
													<p class="gglplsn-id-prompt">
														<span class="bws_info"><?php echo __( "If Invitation Type is 'Google+ Profile ID', it should look like", 'google-one' ) . '&nbsp;"12345678912345678912&nbsp"&nbsp;' . __( 'or', 'google-one' ) . '&nbsp;"+YouName"' ?></span>
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
														<span class="bws_info"><?php echo __( "If Invitation Type is 'Phone Number', it should look like", 'google-one' ) . '&nbsp;"+38001234567"'; ?></span>
													</p>
												</noscript>
											</td>
										</tr>
										<tr class="gglplsn-hangout-options gglplsn-hangout-invite-id <?php if ( 0 == $gglplsn_options['hangout_js'] ) echo 'gglplsn-hide-option'; ?>">
											<th style="display:none;"></th>
											<td style="display:none;">
												<input type="text" id="gglplsn_hangout_invite_id" />
												<input type="submit" class="button tagadd" value="<?php _e( 'Add', 'google-one' ); ?>" id="gglplsn_hangout_invite_add" />
												<p class="gglplsn-id-prompt">
													<span class="bws_info"></span>
												</p>
												<p id='gglplsn_invite_id_error' style="display:none;"></p>
											</td>
										</tr>
										<tr class="gglplsn-badge-options gglplsn-first <?php if ( 0 == $gglplsn_options['badge_js'] ) echo 'gglplsn-hide-option'; ?>">
											<th colspan="2"><?php _e( 'Settings for Badge', 'google-one' ); ?></th>
										</tr>
										<tr class="gglplsn-badge-options <?php if ( 0 == $gglplsn_options['badge_js'] ) echo 'gglplsn-hide-option'; ?>">
											<th><?php _e( 'Type', 'google-one' ); ?></th>
											<td>
												<select name="gglplsn_badge_type">
													<option value="person" <?php if ( 'person' == $gglplsn_options['badge_type'] ) echo 'selected="selected"'; ?>><?php _e( 'Person', 'google-one' ); ?></option>
													<option value="page" <?php if ( 'page' == $gglplsn_options['badge_type'] ) echo 'selected="selected"'; ?>><?php _e( 'Page', 'google-one' ); ?></option>
													<option value="community" <?php if ( 'community' == $gglplsn_options['badge_type'] ) echo 'selected="selected"'; ?>><?php _e( 'Community', 'google-one' ); ?></option>
												</select>
											</td>
										</tr>
										<tr class="gglplsn-badge-options <?php if ( 0 == $gglplsn_options['badge_js'] ) echo 'gglplsn-hide-option'; ?>">
											<th class="gglplsn-badge-id-th">
												<?php switch( $gglplsn_options['badge_type'] ) {
													case 'person' :
														$gglplsn_badge_id_th 		= __( 'Google+ ID', 'google-one' );
														$gglplsn_badge_id_info 		= __( 'Enter the Google+ ID, e.g.', 'google-one' ) . '&nbsp;"12345678912345678912"&nbsp;' . __( 'or', 'google-one' ) . '&nbsp;"+YouName"';
														$gglplsn_badge_tagline_info	= __( "Display the user's tag line", 'google-one' );
														break;
													case 'page' :
														$gglplsn_badge_id_th 		= __( 'Google+ Page ID', 'google-one' );
														$gglplsn_badge_id_info 		= __( 'Enter the Google+ Page ID, e.g.', 'google-one' ) . '&nbsp;"12345678912345678912"&nbsp;' . __( 'or', 'google-one' ) . '&nbsp;"+CompanyName"';
														$gglplsn_badge_tagline_info	= __( 'Display the company tag line', 'google-one' );
														break;
													case 'community' :
														$gglplsn_badge_id_th		= __( 'Google+ Community ID', 'google-one' );
														$gglplsn_badge_id_info		= __( 'Enter the Google+ Community ID, e.g.', 'google-one' ) . '&nbsp;"12345678912345678912"&nbsp;' . __( 'or', 'google-one' ) . '&nbsp;"+CommunityName"';
														$gglplsn_badge_tagline_info	= __( 'Display the community tag line', 'google-one' );
														break;
												}
												echo $gglplsn_badge_id_th; ?>
											</th>
											<td>
												<input type="text" name="gglplsn_badge_id" <?php if ( 1 == $gglplsn_options['badge_js'] ) { echo 'required="required"'; } ?> value="<?php echo $gglplsn_options['badge_id']; ?>" />
												<p>
													<span class="bws_info gglplsn-badge-id-info">(<?php echo $gglplsn_badge_id_info; ?>)</span>
												</p>
											</td>
										</tr>
										<tr class="gglplsn-badge-options <?php if ( 0 == $gglplsn_options['badge_js'] ) echo 'gglplsn-hide-option'; ?>">
											<th><?php _e( 'Layout', 'google-one' ); ?></th>
											<td>
												<select name="gglplsn_badge_layout">
													<option value="portrait" <?php if ( 'portrait' == $gglplsn_options['badge_layout'] ) echo 'selected="selected"'; ?>><?php _e( 'Portrait', 'google-one' ); ?></option>
													<option value="landscape" <?php if ( 'landscape' == $gglplsn_options['badge_layout'] ) echo 'selected="selected"'; ?>><?php _e( 'Landscape', 'google-one' ); ?></option>
												</select>
												<p>
													<span class="bws_info">(<?php _e( 'Sets the orientation of the badge', 'google-one' ); ?>)</span>
												</p>
											</td>
										</tr>
										<tr class="gglplsn-badge-options gglplsn-show-cover <?php if ( 0 == $gglplsn_options['badge_js'] ) echo 'gglplsn-hide-option'; ?>">
											<th><?php _e( 'Show Cover Photo', 'google-one' ); ?></th>
											<td>
												<label>
													<input type="checkbox" name="gglplsn_badge_show_cover"<?php if ( true == $gglplsn_options['badge_show_cover'] ) echo 'checked="checked"'; ?> value="1" />
												</label>
											</td>
										</tr>
										<tr class="gglplsn-badge-options gglplsn-show-tagline <?php if ( 0 == $gglplsn_options['badge_js'] ) echo 'gglplsn-hide-option'; ?>">
											<th><?php _e( 'Show Tag Line', 'google-one' ); ?></th>
											<td>
												<label>
													<input type="checkbox" name="gglplsn_badge_show_tagline" <?php if ( true == $gglplsn_options['badge_show_tagline'] ) echo 'checked="checked"'; ?> value="1" />
													<span class="bws_info gglplsn-badge-tagline-info">(<?php echo $gglplsn_badge_tagline_info; ?>)</span>
												</label>
											</td>
										</tr>
										<tr class="gglplsn-show-owners <?php if ( 0 == $gglplsn_options['badge_js'] || 'community' != $gglplsn_options['badge_type'] ) echo 'gglplsn-hide-option'; ?>">
											<th><?php _e( 'Show Owners', 'google-one' ); ?></th>
											<td>
												<label>
													<input type="checkbox" name="gglplsn_badge_show_owners"<?php if ( true == $gglplsn_options['badge_show_owners'] ) echo 'checked="checked"'; ?> value="1" />
													<span class="bws_info">(<?php _e( 'Display a list of community owners', 'google-one' ); ?>)</span>
												</label>
											</td>
										</tr>
										<tr class="gglplsn-badge-options <?php if ( 0 == $gglplsn_options['badge_js'] ) echo 'gglplsn-hide-option'; ?>">
											<th><?php _e( 'Theme', 'google-one' ); ?></th>
											<td>
												<select name="gglplsn_badge_theme">
													<option value="light" <?php if ( 'light' == $gglplsn_options['badge_theme'] ) echo 'selected="selected"'; ?>><?php _e( 'Light', 'google-one' ); ?></option>
													<option value="dark" <?php if ( 'dark' == $gglplsn_options['badge_theme'] ) echo 'selected="selected"'; ?>><?php _e( 'Dark', 'google-one' ); ?></option>
												</select>
											</td>
										</tr>
										<tr class="gglplsn-badge-options <?php if ( 0 == $gglplsn_options['badge_js'] ) echo 'gglplsn-hide-option'; ?>">
											<th><?php _e( 'Width', 'google-one' ); ?></th>
											<td>
												<input type="number" name="gglplsn_badge_width" max="450" <?php echo ( 'portrait' == $gglplsn_options['badge_layout'] ) ? 'min="180"' : 'min="273"'; ?> value="<?php echo $gglplsn_options['badge_width']; ?>" />
												 <?php _e( 'px', 'google-one' ); ?>
											</td>
										</tr>
									</tbody>
								</table>
																<p class="submit">
									<input id="bws-submit-button" type="submit" value="<?php _e( 'Save Changes', 'google-one' ); ?>" class="button-primary" />
									<input type="hidden" name="gglplsn_form_submit" value="1" />
									<?php wp_nonce_field( $plugin_basename, 'gglplsn_nonce_name' ); ?>
								</p>
							</form>
						</div>
					</div>
						<?php }
}

if ( ! function_exists( 'gglplsn_admin_head' ) ) {
	function gglplsn_admin_head() {
		global $hook_suffix, $gglplsn_is_button_shown;;
		if ( isset( $_GET['page'] ) && ( "google-plus-one.php" == $_GET['page'] || "social-buttons.php" == $_GET['page'] ) ) {
			if( isset( $_GET['action'] ) && 'custom_code' == $_GET['action'] ) {
				bws_plugins_include_codemirror();
			}
			wp_enqueue_style( 'gglplsn_style', plugins_url( 'css/style.css', __FILE__ ) );
			/* Loclize script */
			wp_enqueue_script( 'gglplsn-script', plugins_url( 'js/script.js' , __FILE__ ) );
			$js_strings = array(
				'already_added'				=> __( 'Is already added', 'google-one' ),
				'one_number'				=> __( 'Only one phone number can be added', 'google-one' ),
				'number_added'				=> __( "You can't add the invitation because the phone number is already added", 'google-one' ),
				'any_added'					=> __( "You can't add the phone number because another invitation type is already added", 'google-one' ),
				'empty_id'					=> __( "This field can't be empty", 'google-one' ),
				'invalid_email'     		=> __( 'Please, enter the valid Email', 'google-one' ),
				'email_th'					=> __( 'Email of Invited Person', 'google-one' ),
				'email_info'				=> __( 'Please, enter the Email of invited person, e.g.', 'google-one' ) . '&nbsp;"example@gmail.com"',
				'phone_th'					=> __( 'Phone Number of Invited Person', 'google-one' ),
				'phone_info'				=> __( 'Please, enter the phone number of invited person, e.g.', 'google-one' ) . '&nbsp;"+38001234567"',
				'profile_th'				=> __( 'Google+ Profile ID of Invited Person', 'google-one' ),
				'profile_info'				=> __( 'Please, enter the Google+ Profile ID of invited person, e.g.', 'google-one' ) . '&nbsp;"12345678912345678912"&nbsp;' . __( 'or', 'google-one' ) . '&nbsp;"+YouName"',
				'circle_th'					=> __( 'Google+ Circle ID for Invitation', 'google-one' ),
				'circle_info'				=> __( 'Please, enter the Google+ Circle ID for invitation, e.g.', 'google-one' ) . '&nbsp;"123ab345cd576ef7"',
				'person_id_th'				=> __( 'Google+ ID', 'google-one' ),
				'person_id_info'			=> __( 'Enter the Google+ ID, e.g.', 'google-one' ) . '&nbsp;"12345678912345678912"&nbsp;' . __( 'or', 'google-one' ) . '&nbsp;"+YouName"',
				'page_id_th'				=> __( 'Google+ Page ID', 'google-one' ),
				'page_id_info'				=> __( 'Enter the Google+ Page ID, e.g.', 'google-one' ) . '&nbsp;"12345678912345678912"&nbsp;' . __( 'or', 'google-one' ) . '&nbsp;"+CompanyName"',
				'community_id_th'			=> __( 'Google+ Community ID', 'google-one' ),
				'community_id_info'			=> __( 'Enter the Google+ Community ID, e.g.', 'google-one' ) . '&nbsp;"12345678912345678912"&nbsp;' . __( 'or', 'google-one' ) . '&nbsp;"+CommunityName"',
				'person_tagline_info'		=> __( "Display the user's tag line", 'google-one' ),
				'page_tagline_info'			=> __( 'Display the company tag line', 'google-one' ),
				'community_tagline_info'	=> __( 'Display the community tag line', 'google-one' ),
				'gglplsn_ajax_nonce'		=> wp_create_nonce( 'gglplsn_ajax_nonce' )
			);
			wp_localize_script( 'gglplsn-script', 'js_string', $js_strings );
		} elseif ( 'widgets.php' == $hook_suffix ) {
			wp_enqueue_script( 'gglplsn-widgets-script', plugins_url( 'js/widgets-script.js' , __FILE__ ) );
		} elseif ( ! is_admin() && ! empty( $gglplsn_is_button_shown ) ) {
			wp_enqueue_style( 'gglplsn_style', plugins_url( 'css/style.css', __FILE__ ) );
		}
	}
}

if ( ! function_exists( 'gglplsn_footer_actions' ) ) {
	function gglplsn_footer_actions() {
		gglplsn_js();
		gglplsn_admin_head();
	}
}

if ( ! function_exists( 'gglplsn_js' ) ) {
	function gglplsn_js() {
		global $gglplsn_is_button_shown;
		if ( ! empty( $gglplsn_is_button_shown ) ) {
			global $gglplsn_options, $gglplsn_lang_codes;
			if ( 1 == $gglplsn_options['plus_one_js'] || 1 == $gglplsn_options['share_js'] || 1 == $gglplsn_options['follow_js'] || 1 == $gglplsn_options['hangout_js'] || 1 == $gglplsn_options['badge_js'] ) {
				if ( 1 == $gglplsn_options['use_multilanguage_locale'] && isset( $_SESSION['language'] ) ) {
					if ( array_key_exists( $_SESSION['language'], $gglplsn_lang_codes ) ) {
						$gglplsn_locale = $_SESSION['language'];
					} else {
						$gglplsn_locale_from_multilanguage = str_replace( '_', '-', $_SESSION['language'] );
						if( array_key_exists( $gglplsn_locale_from_multilanguage, $gglplsn_lang_codes ) ) {
							$gglplsn_locale = $gglplsn_locale_from_multilanguage;
						} else {
							$gglplsn_locale_from_multilanguage = explode( '_', $_SESSION['language']  );
							if( is_array( $gglplsn_locale_from_multilanguage ) && array_key_exists( $gglplsn_locale_from_multilanguage[0], $gglplsn_lang_codes ) )
								$gglplsn_locale = $gglplsn_locale_from_multilanguage[0];
						}
					}
				}
			}
			if ( empty( $gglplsn_locale ) )
				$gglplsn_locale = $gglplsn_options['lang']; ?>
			<script type="text/javascript">
				window.___gcfg = {
					lang: '<?php echo $gglplsn_locale; ?>',
				};
			</script>
			<script type="text/javascript" src="https://apis.google.com/js/plusone.js" async defer></script>
		<?php } ?>
	<?php }
}

/* Google Buttons on page */
if ( ! function_exists( 'gglplsn_button_content' ) ) {
	function gglplsn_button_content() {
		global $gglplsn_options;

		$plus_one = ( 1 == $gglplsn_options['plus_one_js'] ) ? gglplsn_return_button( 'plusone', $gglplsn_options ) : '';
		$share = ( 1 == $gglplsn_options['share_js'] ) ? gglplsn_return_button( 'share', $gglplsn_options ) : '';
		$follow = ( 1 == $gglplsn_options['follow_js'] ) ? gglplsn_return_button( 'follow', $gglplsn_options ) : '';
		$hangout = ( 1 == $gglplsn_options['hangout_js'] ) ? gglplsn_return_button( 'hangout', $gglplsn_options ) : '';
		$badge = ( 1 == $gglplsn_options['badge_js'] ) ? gglplsn_return_button( 'badge', $gglplsn_options ) : '';
		return $badge . $hangout . $plus_one . $follow . $share;
	}
}

if ( ! function_exists( 'gglplsn_pos' ) ) {
	function gglplsn_pos( $content ) {
		global $gglplsn_options, $gglplsn_is_button_shown;

		if ( is_feed() )
			return $content;
		$button_content = gglplsn_button_content();
		if (
			! empty( $button_content ) &&
			( ! empty( $gglplsn_options['posts'] ) || ! empty( $gglplsn_options['pages'] ) || ! empty( $gglplsn_options['homepage'] ) )
		) {
			if ( ! is_home() && ! is_front_page() ) {
				if ( ( is_single() && ! empty( $gglplsn_options['posts'] ) ) || ( is_page() && ! empty( $gglplsn_options['pages'] ) ) ) {
					$button = '<div class="gglplsn_buttons">' . $button_content . '</div>';
				}
			} elseif ( ! empty( $gglplsn_options['homepage'] ) ) {
				$button = '<div class="gglplsn_buttons">' . $button_content . '</div>';
			}

			if ( ! empty( $button ) ) {
				$gglplsn_is_button_shown = true;
				if ( 'before_post' == $gglplsn_options['position'] ) {
					return $button . $content;
				} elseif ( 'after_post' == $gglplsn_options['position'] ) {
					return  $content . $button;
				} elseif ( 'afterandbefore' == $gglplsn_options['position'] ) {
					return $button . $content . $button;
				} elseif ( 'only_shortcode' == $gglplsn_options['position'] ) {
					return $content;
				}
			}
		}
		return $content;
	}
}

/* Badge Widget */
if ( ! class_exists( 'Gglplsn_Badge_Widget' ) ) {
	class Gglplsn_Badge_Widget extends WP_Widget {
		function __construct() {
			parent::__construct( 'gglplsn_badge', __( 'Google+ Badge Widget', 'google-one' ), array( 'description' => __( 'Show Google Badge on your site', 'google-one' ) ) );
		}

		function widget( $args, $instance ) {
			global $gglplsn_options, $gglplsn_is_button_shown;

			if ( ! isset( $instance['badge_layout'] ) ) {
				$instance['badge_layout'] = $gglplsn_options['badge_layout'];
			}

			if ( ! isset( $instance['badge_show_cover'] ) ) {
				$instance['badge_show_cover'] = $gglplsn_options['badge_show_cover'];
			}
			$instance['badge_id']				= ! empty( $instance['badge_id'] ) ? esc_html( $instance['badge_id'] ) : esc_html( $gglplsn_options['badge_id'] );
			$instance['badge_type']				= ! empty( $instance['badge_type'] ) ? esc_html( $instance['badge_type'] ) : esc_html( $gglplsn_options['badge_type'] );
			$instance['badge_show_tagline'] 	= ! empty( $instance['badge_show_tagline'] ) ? true : false;
			$instance['badge_show_owners']		= ! empty( $instance['badge_show_owners'] ) ? true : false;
			$instance['badge_theme']			= ! empty( $instance['badge_theme'] ) ? esc_html( $instance['badge_theme'] ) : esc_html( $gglplsn_options['badge_theme'] );
			$instance['badge_width']			= ! empty( $instance['badge_width'] ) ? intval( $instance['badge_width'] ) : intval( $gglplsn_options['badge_width'] );
			$title = ( ! empty( $instance['title'] ) ) ? apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) : '';
			echo $args['before_widget'];
			if ( ! empty( $title ) ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}
			$badge = gglplsn_return_button( 'badge', $instance );
			echo $badge;
			echo $args['after_widget'];
			$gglplsn_is_button_shown = true;
		}

		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title']					= strip_tags( $new_instance['title'] );
			$instance['badge_id']				= esc_html( $new_instance['badge_id'] );
			$instance['badge_type']				= $new_instance['badge_type'];
			$instance['badge_layout']			= $new_instance['badge_layout'];
			$instance['badge_show_cover']		= isset( $new_instance['badge_show_cover'] ) ? true : false;
			$instance['badge_show_tagline']		= isset( $new_instance['badge_show_tagline'] ) ? true : false;
			$instance['badge_show_owners']		= isset( $new_instance['badge_show_owners'] ) ? true : false;
			$instance['badge_width']			= isset( $new_instance['badge_width'] ) ? intval( $new_instance['badge_width'] ) : 270;
			return $instance;
		}

		function form( $instance ) {
			global $gglplsn_options;
			if ( empty( $gglplsn_options ) ) {
				$gglplsn_options = get_option( 'gglplsn_options' );
			}

			if ( ! empty( $instance ) ) {
				$title					= ! empty( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
				$badge_id				= ! empty( $instance['badge_id'] ) ? esc_html( $instance['badge_id'] ) : '';
				$badge_type				= ! empty( $instance['badge_type'] ) ? esc_html( $instance['badge_type'] ) : '';
				$badge_layout			= ! empty( $instance['badge_layout'] ) ? $instance['badge_layout'] : 'portrait';
				$badge_show_cover		= ! empty( $instance['badge_show_cover'] ) ? true : false;
				$badge_show_tagline		= ! empty( $instance['badge_show_tagline'] ) ? true : false;
				$badge_show_owners		= ( ! empty( $instance['badge_show_owners'] ) && 'community' == $badge_type ) ? true : false;
				$badge_width			= ! empty( $instance['badge_width'] ) ? intval( $instance['badge_width'] ) : 180;
			} else {
				$title					= '';
				$badge_id				= ! empty( $gglplsn_options['badge_id'] ) ? esc_html( $gglplsn_options['badge_id'] ) : '';
				$badge_type				= ! empty( $gglplsn_options['badge_type'] ) ? esc_html( $gglplsn_options['badge_type'] ) : '';
				$badge_layout			= ! empty( $gglplsn_options['badge_layout'] ) ? $gglplsn_options['badge_layout'] : 'portrait';
				$badge_show_cover		= ! empty( $gglplsn_options['badge_show_cover'] ) ? true : false;
				$badge_show_tagline		= ! empty( $gglplsn_options['badge_show_tagline'] ) ? true : false;
				$badge_show_owners		= ( ! empty( $gglplsn_options['badge_show_owners'] ) && 'community' == $badge_type ) ? true : false;
				$badge_width			= ! empty( $gglplsn_options['badge_width'] ) ? intval( $gglplsn_options['badge_width'] ) : 180;
			} ?>

			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'google-one' ); ?>:</label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'badge_id' ); ?>"><?php _e( 'Google+ ID', 'google-one' ); ?>:</label>
				<input type="text" class="widefat" name="<?php echo $this->get_field_name( 'badge_id' ); ?>" <?php echo 'required="required"'; ?> value="<?php echo $badge_id; ?>" />
				<span class="bws_info gglplsn-badge-id-info">(<?php echo __( 'Enter the Google+ ID, e.g.', 'google-one' ) . '&nbsp;"12345678912345678912"&nbsp;'; ?>)</span>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'badge_type' ); ?>"><?php _e( 'Type', 'google-one' ); ?></label>
				<select id="<?php echo $this->get_field_id( 'badge_type' ); ?>" class="gglplsn-badge-type" name="<?php echo $this->get_field_name( 'badge_type' ); ?>">
					<option value="person" <?php if ( 'person' == $badge_type ) echo 'selected="selected"'; ?>><?php _e( 'Person', 'google-one' ); ?></option>
					<option value="page" <?php if ('page' == $badge_type ) echo 'selected="selected"'; ?>><?php _e( 'Page', 'google-one' ); ?></option>
					<option value="community" <?php if ( 'community' == $badge_type ) echo 'selected="selected"'; ?>><?php _e( 'Community', 'google-one' ); ?></option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'badge_layout' ); ?>"><?php _e( 'Layout', 'google-one' ); ?></label>
				<select id="<?php echo $this->get_field_id( 'badge_layout' ); ?>" name="<?php echo $this->get_field_name( 'badge_layout' ); ?>">
					<option value="portrait" <?php if ( 'portrait' == $badge_layout ) echo 'selected="selected"';?>><?php _e( 'Portrait', 'google-one' ); ?></option>
					<option value="landscape" <?php if ( 'landscape' == $badge_layout ) echo 'selected="selected"';?>><?php _e( 'Landscape', 'google-one' ); ?></option>
				</select>
			</p>
			<p>
				<input class="widefat" id="<?php echo $this->get_field_id( 'badge_show_cover' ); ?>" name="<?php echo $this->get_field_name( 'badge_show_cover' ); ?>" type="checkbox" <?php if ( true == $badge_show_cover ) echo 'checked="checked"'; ?> value="1" />
				<label for="<?php echo $this->get_field_id( 'badge_show_cover' ); ?>"><?php _e( 'Show Cover Photo', 'google-one' ); ?></label>
			</p>
			<p>
				<input class="widefat" id="<?php echo $this->get_field_id( 'badge_show_tagline' ); ?>" name="<?php echo $this->get_field_name( 'badge_show_tagline' ); ?>" type="checkbox" <?php if ( true == $badge_show_tagline ) echo 'checked="checked"'; ?> value="1" />
				<label for="<?php echo $this->get_field_id( 'badge_show_tagline' ); ?>"><?php _e( 'Show Tag Line', 'google-one' ); ?></label>
			</p>

			<p <?php echo ( 'community' != $badge_type ) ? 'class="gglplsn-show-owners hidden"' : 'class="gglplsn-show-owners"'; ?>>
				<input class="widefat" id="<?php echo $this->get_field_id( 'badge_show_owners' ); ?>" name="<?php echo $this->get_field_name( 'badge_show_owners' ); ?>" type="checkbox"<?php if ( true == $badge_show_owners ) echo ' checked="checked"'; echo ( 'community' != $badge_type ) ? ' disabled="disabled"': ''; ?> value="1" />
				<label for="<?php echo $this->get_field_id( 'badge_show_owners' ); ?>"><?php _e( 'Show Owners', 'google-one' ); ?></label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'badge_width' ); ?>"><?php _e( 'Width', 'google-one' ); ?></label>
				<input type="number" id="<?php echo $this->get_field_id( 'badge_width' ); ?>" name="<?php echo $this->get_field_name( 'badge_width' ); ?>" max="450" <?php echo ( 'portrait' == $badge_layout ) ? 'min="180"' : 'min="273"'; ?> value="<?php echo $badge_width; ?>" />
				<?php _e( 'px', 'google-one' ); ?>
			</p>
		<?php }
	}
}

if ( ! function_exists( 'gglplsn_register_badge_widget' ) ) {
	function gglplsn_register_badge_widget() {
		register_widget( 'Gglplsn_Badge_Widget' );
	}
}

/* Function for forming buttons tags */
if ( ! function_exists( 'gglplsn_return_button' ) ) {
	function gglplsn_return_button( $request, $options ) {
		extract( $options );
		if ( 'plusone' == $request ) {
			switch( $plus_one_size ) {
				case 'standard' :
					$plus_one_width = ( 'standard' == $plus_one_annotation_type ) ? 189 : 139;
					break;
				case 'small' :
					$plus_one_width = ( 'standard' == $plus_one_annotation_type ) ? 185 : 129;
					break;
				case 'medium' :
					$plus_one_width = ( 'standard' == $plus_one_annotation_type ) ? 183 : 133;
					break;
				case 'tall' :
					$plus_one_width = ( 'standard' == $plus_one_annotation_type ) ? 201 : 151;
					break;
			}

			$plus_one = '<span class="gglplsn_plus_one"><g:plusone
				size="' . $plus_one_size . '"
				'. ( ( 'inline' == $plus_one_annotation ) ? 'width="' . $plus_one_width . '"' : "" ) .
				'annotation="' . $plus_one_annotation . '"
				callback="on"
				href="' . get_permalink() . '"></g:plusone></span>';
			return $plus_one;
		}

		if ( 'share' == $request ) {
			switch( $share_size ) {
				case 20 :
					$share_width = ( 'standard' == $share_annotation_type ) ? 200 : 150;
					break;
				case 15 :
					$share_width = ( 'standard' == $share_annotation_type ) ? 205 : 140;
					break;
				case 24 :
					$share_width = ( 'standard' == $share_annotation_type ) ? 206 : 150;
					break;
			}

			$share = '<span class="gglplsn_share"><g:plus action="share"
				href="'. get_permalink() . '"
				'. ( ( 'vertical-bubble' != $share_annotation ) ? 'height="' . $share_size . '"' : "" ) . '
				annotation="' . $share_annotation .'"
				'. ( ( 'inline' == $share_annotation ) ? 'width="' . $share_width . '"' : "" ) . '> </span></span>';
			return $share;
		}

		if ( 'follow' == $request ) {
			$follow_id = sanitize_text_field( $follow_id );
			$href = 'https://plus.google.com/' . $follow_id;
			$follow = '<span class="gglplsn_follow"><g:follow
				href="' . esc_url( $href ) . '"
				height="' . intval( $follow_size ) . '"
				annotation="' . $follow_annotation .'"
				rel="' . $follow_relationship . '"></g:follow></span>';
			return $follow;
		}

		if ( 'hangout' == $request ) {
			$hangout_topic_string = ( 1 == $hangout_topic_title ) ? get_the_title() : sanitize_text_field( $hangout_topic );
			$hangout_invite = "";
			if ( ! empty( $hangout_invite_type ) ) {
				foreach( $hangout_invite_type as $key => $value ) {
					$hangout_invite .= "{ id : '" . $hangout_invite_id[ $key ] . "', invite_type : '" . sanitize_text_field( $value ) . "' }, ";
				}
			}
			if ( 'standard' != $hangout_size ) {
				$hangout_width = ( 'narrow' == $hangout_size ) ? 72 : 175;
			}

			$hangout = '<span class="gglplsn_hangout"><g:hangout
				render="createhangout"
				topic="' . $hangout_topic_string . '"
				hangout_type="' . $hangout_type . '"
				'. ( ( 'standard' != $hangout_size ) ? 'widget_size="' . $hangout_width . '"' : "" ) . '
				invites="[' . $hangout_invite . ']"></g:hangout></span>';
			return $hangout;
		}

		if ( 'badge' == $request ) {
			$badge_id = esc_html( $badge_id );
			$href = 'https://plus.google.com/' . ( 'community' == $badge_type ? 'communities/': '' ) . $badge_id;
			$photo = ( ( 'community' != $badge_type ) ? 'showcoverphoto="' : 'showphoto="' ) . $badge_show_cover . '"';
			$badge_width = intval( $badge_width );
			if ( $badge_width < 180 && 'portrait' == $badge_layout ) {
				$badge_width = 180;
			} elseif ( $badge_width < 273 && 'landscape' == $badge_layout ) {
				$badge_width = 273;
			} elseif ( $badge_width > 450 ) {
				$badge_width = 450;
			}

			$badge = '<p class="gglplsn_badge"><g:' . $badge_type . '
				href="' . esc_url( $href ) . '"
				layout="' . $badge_layout . '"
				width="' . $badge_width . '"
				theme="' . $badge_theme . '"
				' . $photo . '
				showowners="' . $badge_show_owners . '"
				showtagline="' . $badge_show_tagline . '"></g:' . $badge_type . '></p>';
			return $badge;
		}
	}
}

/* Google +1 shortcode */
/* [bws_googleplusone] */
if ( ! function_exists( 'gglplsn_shortcode' ) ) {
	function gglplsn_shortcode( $atts ) {
		global $gglplsn_options, $gglplsn_is_button_shown;

		$buttons = '';
		$shortcode_atts = shortcode_atts( array( 'display' => 'plusone' ), $atts );
		$shortcode_atts = ( str_word_count( $shortcode_atts['display'], 1 ) );
		foreach ( $shortcode_atts as $value ) {
			if ( 'plusone' === $value ) {
				$buttons .= gglplsn_return_button( 'plusone', $gglplsn_options );
			}

			if ( 'share' === $value ) {
				$buttons .= gglplsn_return_button( 'share', $gglplsn_options );
			}

			if ( 'follow' === $value ) {
				$buttons .= gglplsn_return_button( 'follow', $gglplsn_options );
			}

			if ( 'hangout' === $value ) {
				$buttons .= gglplsn_return_button( 'hangout', $gglplsn_options );
			}

			if ( 'badge' === $value ) {
				$buttons .= gglplsn_return_button( 'badge', $gglplsn_options );
			}
		}
		if ( ! empty( $buttons ) ) {
			$gglplsn_is_button_shown = true;
		}
		return $buttons;
	}
}

/* add shortcode content  */
if ( ! function_exists( 'gglplsn_shortcode_button_content' ) ) {
	function gglplsn_shortcode_button_content( $content ) {
		global $wp_version; ?>
		<div id="gglplsn" style="display:none;">
			<fieldset>
				<?php _e( 'Add Google Buttons to your page or post', 'google-one' ); ?>
					<br />
					<label>
						<input type="checkbox" name="gglplsn_selected_plusone" value="plusone" checked="checked" />
						<?php _e( 'Google +1', 'google-one' ) ?>
					</label>
					<br />
					<label>
						<input type="checkbox" name="gglplsn_selected_share" value="share" />
						<?php _e( 'Share', 'google-one' ) ?>
					</label>
					<br />
					<label>
						<input type="checkbox" name="gglplsn_selected_follow" value="follow" />
						<?php _e( 'Follow', 'google-one' ) ?>
					</label>
					<br />
					<label>
						<input type="checkbox" name="gglplsn_selected_hangout" value="hangout" />
						<?php _e( 'Hangout', 'google-one' ) ?>
					</label>
					<br />
					<label>
						<input type="checkbox" name="gglplsn_selected_badge" value="badge" />
						<?php _e( 'Badge', 'google-one' ) ?>
					</label>
				<input class="bws_default_shortcode" type="hidden" name="default" value="[bws_googleplusone]" />
				<div class="clear"></div>
			</fieldset>
		</div>
		<script type="text/javascript">
			function gglplsn_shortcode_init() {
				( function( $ ) {
					var current_object = '<?php echo ( $wp_version < 3.9 ) ? "#TB_ajaxContent" : ".mce-reset"; ?>';
					$( current_object + ' input[name^="gglplsn_selected"]' ).change( function() {
						var result = '';
						$( current_object + ' input[name^="gglplsn_selected"]' ).each( function() {
							if ( $( this ).is( ':checked' ) ) {
								result += $( this ).val() + ',';
							}
						} );
						if ( '' == result ) {
							$( current_object + ' #bws_shortcode_display' ).text( '' );
						} else {
							result = result.slice( 0, - 1 );
							$( current_object + ' #bws_shortcode_display' ).text( '[bws_googleplusone display="' + result + '"]' );
						}
					} );
				} ) ( jQuery );
			}
		</script>
	<?php }
}

/* Validate email for Hangout invites */
if ( ! function_exists( 'gglplsn_validate_email' ) ) {
	function gglplsn_validate_email() {
		check_ajax_referer( 'gglplsn_ajax_nonce', 'gglplsn_nonce' );
		if ( isset( $_POST['gglplsn_email_for_validate'] ) ) {
			echo json_encode( array( 'gglplsn_email_validate' => is_email( $_POST['gglplsn_email_for_validate'] ) ) );
		}
		wp_die();
	}
}

add_action( 'init', 'gglplsn_init' );
add_action( 'plugins_loaded', 'gglplsn_plugins_loaded' );
add_action( 'admin_init', 'gglplsn_admin_init' );
/* Adding stylesheets */
add_action( 'wp_footer', 'gglplsn_footer_actions' );
add_action( 'admin_enqueue_scripts', 'gglplsn_admin_head' );
/* Adding plugin buttons */
add_shortcode( 'bws_googleplusone', 'gglplsn_shortcode' );
add_filter( 'widget_text', 'do_shortcode' );
add_filter( 'the_content', 'gglplsn_pos' );
/* Register widget */
add_action( 'widgets_init', 'gglplsn_register_badge_widget' );
/* custom filter for bws button in tinyMCE */
add_filter( 'bws_shortcode_button_content', 'gglplsn_shortcode_button_content' );
/* action for AJAX */
add_action( 'wp_ajax_gglplsn_validate_email', 'gglplsn_validate_email' );
