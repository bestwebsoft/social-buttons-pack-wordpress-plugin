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

		
		$bws_shortcode_list['gglplsn'] = array( 'name' => 'Google +1' );
	}
}

if ( ! function_exists ( 'gglplsn_settings' ) ) {
	function gglplsn_settings() {
		global $gglplsn_options, $gglplsn_plugin_info, $gglplsn_option_defaults;

		/* Default options */
		$gglplsn_option_defaults		=	array(
			'plugin_option_version'		=>	$gglplsn_plugin_info["Version"],
			'js'						=>	'1',
			'annotation'				=>	'none',
			'size'						=>	'standard',
			'position'					=>	'before_post',
			'lang'						=>	'en',
			'posts'						=>	'1',
			'pages'						=>	'1',
			'homepage'					=>	'1',
			'use_multilanguage_locale'	=>	0,
			'display_settings_notice'	=>	1,
			'first_install'				=>	strtotime( "now" )
		);

		if ( ! get_option( 'gglplsn_options' ) )
			add_option( 'gglplsn_options', $gglplsn_option_defaults );

		$gglplsn_options = get_option( 'gglplsn_options' );

		if ( ! isset( $gglplsn_options['plugin_option_version'] ) || $gglplsn_options['plugin_option_version'] != $gglplsn_plugin_info["Version"] ) {
			if ( '1' == $gglplsn_options['annotation'] )
				$gglplsn_options['annotation'] = 'bubble';
			elseif ( 0 == $gglplsn_options['annotation'] )
				$gglplsn_options['annotation'] = 'none';

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
			$gglplsn_options['js']							=	isset( $_REQUEST['gglplsn_js'] ) ? 1 : 0 ;
			$gglplsn_options['annotation']					=	$_REQUEST['gglplsn_annotation'];
			$gglplsn_options['size']						=	$_REQUEST['gglplsn_size'];
			$gglplsn_options['position']					=	$_REQUEST['gglplsn_position'];
			$gglplsn_options['lang']						=	$_REQUEST['gglplsn_lang'];
			$gglplsn_options['posts']						=	isset( $_REQUEST['gglplsn_posts'] ) ? 1 : 0 ;
			$gglplsn_options['pages']						=	isset( $_REQUEST['gglplsn_pages'] ) ? 1 : 0 ;
			$gglplsn_options['homepage']					=	isset( $_REQUEST['gglplsn_homepage'] ) ? 1 : 0 ;
			$gglplsn_options['use_multilanguage_locale']	=	isset( $_REQUEST['gglplsn_use_multilanguage_locale'] ) ? 1 : 0;
			$message = __( 'Settings saved', 'google-one' );
			update_option( 'gglplsn_options', $gglplsn_options );
		}		

		?>
		
			<div class="updated fade" <?php if ( '' == $message || "" != $error ) echo 'style="display:none"'; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<?php bws_show_settings_notice(); ?>
			<div class="error" <?php if ( "" == $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $error; ?></strong></p></div>
			<?php ?>
					<p><?php _e( 'For the correct work of the button do not use it locally or on a free hosting', 'google-one' ); ?><br /></p>
					<div><?php $icon_shortcode = ( "google-plus-one.php" == $_GET['page'] ) ? plugins_url( 'bws_menu/images/shortcode-icon.png', __FILE__ ) : plugins_url( 'social-buttons-pack/bws_menu/images/shortcode-icon.png' );
					printf( 
						__( "If you would like to add Google +1 button to your page or post, please use %s button", 'google-one' ), 
						'<span class="bws_code"><img style="vertical-align: sub;" src="' . $icon_shortcode . '" alt=""/></span>' ); ?> 
						<div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help">
							<div class="bws_hidden_help_text" style="min-width: 180px;">
								<?php printf( 
									__( "You can add Google +1 button to your page or post by clicking on %s button in the content edit block using the Visual mode. If the button isn't displayed, please use the shortcode %s", 'google-one' ), 
									'<code><img style="vertical-align: sub;" src="' . $icon_shortcode . '" alt="" /></code>',
									'<code>[bws_googleplusone]</code>'
								); ?>
							</div>
						</div>
					</div>
					<form method="post" action="" class="bws_form">
						<table class="form-table gglplsn_form-table">
							<tbody>
								<tr valign="top">
									<th><?php _e( 'Enable Google +1 Button', 'google-one' ); ?></th>
									<td>
										<label>
											<input type="checkbox" name="gglplsn_js"<?php if ( '1' == $gglplsn_options['js'] ) echo 'checked="checked"'; ?> value="1" />
											<span class="bws_info">(<?php _e( 'Enable or Disable Google+1 JavaScript', 'google-one' ); ?>)</span>
										</label>
									</td>
								</tr>
								<tr>
									<th scope="row"><?php _e( 'Size', 'google-one' ); ?></th>
									<td>
										<select name="gglplsn_size">
											<option value="standard" <?php if ( 'standard' == $gglplsn_options['size'] ) echo 'selected="selected"';?>> <?php _e( 'Standard', 'google-one' ); ?></option>
											<option value="small" <?php if ( 'small' == $gglplsn_options['size'] ) echo 'selected="selected"';?>> <?php _e( 'Small', 'google-one' ); ?></option>
											<option value="medium" <?php if ( 'medium' == $gglplsn_options['size'] ) echo 'selected="selected"';?>><?php _e( 'Medium', 'google-one' ); ?></option>
											<option value="tall" <?php if ( 'tall' == $gglplsn_options['size'] ) echo 'selected="selected"';?>><?php _e( 'Tall', 'google-one' ); ?></option>
										</select>
										<span class="bws_info">(<?php _e( 'Please choose one of four different sizes of buttons', 'google-one' ); ?>)</span>
									</td>
								</tr>
								<tr valign="top">
									<th><?php _e( 'Annotation', 'google-one' ); ?></th>
									<td>
										<select name="gglplsn_annotation">
											<option value="inline" <?php if ( 'inline' == $gglplsn_options['annotation'] ) echo 'selected="selected"';?>><?php _e( 'Inline', 'google-one' ); ?></option>
											<option value="bubble" <?php if ( 'bubble' == $gglplsn_options['annotation'] ) echo 'selected="selected"';?>><?php _e( 'Bubble', 'google-one' ); ?></option>
											<option value="none" <?php if ( 'none' == $gglplsn_options['annotation'] ) echo 'selected="selected"';?>><?php _e( 'None', 'google-one' ); ?></option>
										</select>
										<br /><span class="bws_info">(<?php _e( 'Display counters showing how many times your article has been liked', 'google-one' ); ?>)</span>
									</td>
								</tr>																
								<tr>
									<th scope="row"><?php _e( 'Language', 'google-one' ); ?></th>
									<td>
										<fieldset>
											<select name="gglplsn_lang">
												<?php foreach ( $gglplsn_lang_codes as $key => $val ) {
													echo '<option value="' . $key . '"';
													if ( $key == $gglplsn_options['lang'] )
														echo ' selected="selected"';
													echo '>' . esc_html ( $val ) . '</option>';
												} ?>
											</select>
											<span class="bws_info">(<?php _e( 'Select the language to display information on the button', 'google-one' ); ?>)</span><br />
											<label>
												<?php if ( array_key_exists( 'multilanguage/multilanguage.php', $all_plugins ) || array_key_exists( 'multilanguage-pro/multilanguage-pro.php', $all_plugins ) ) {
													if ( is_plugin_active( 'multilanguage/multilanguage.php' ) || is_plugin_active( 'multilanguage-pro/multilanguage-pro.php' ) ) { ?>
														<input type="checkbox" name="gglplsn_use_multilanguage_locale" value="1" <?php if ( 1 == $gglplsn_options["use_multilanguage_locale"] ) echo 'checked="checked"'; ?> /> 
														<?php _e( 'Use the current site language', 'google-one' ); ?> <span class="bws_info">(<?php _e( 'Using', 'google-one' ); ?> Multilanguage by BestWebSoft)</span>
													<?php } else { ?>
														<input disabled="disabled" type="checkbox" name="gglplsn_use_multilanguage_locale" value="1" /> 
														<?php _e( 'Use the current site language', 'google-one' ); ?> 
														<span class="bws_info">(<?php _e( 'Using', 'google-one' ); ?> Multilanguage by BestWebSoft) <a href="<?php echo bloginfo("url"); ?>/wp-admin/plugins.php"><?php _e( 'Activate', 'google-one' ); ?> Multilanguage</a></span>
													<?php }
												} else { ?>
													<input disabled="disabled" type="checkbox" name="gglplsn_use_multilanguage_locale" value="1" /> 
													<?php _e( 'Use the current site language', 'google-one' ); ?> 
													<span class="bws_info">(<?php _e( 'Using', 'google-one' ); ?> Multilanguage by BestWebSoft) <a href="http://bestwebsoft.com/products/multilanguage/?k=196fb3bb74b6e8b1e08f92cddfd54313&pn=78&v=<?php echo $gglplsn_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>"><?php _e( 'Download', 'google-one' ); ?> Multilanguage</a></span>
												<?php } ?>
										</label>
										</fieldset>
									</td>
								</tr>
								<tr>
									<th scope="row"><?php _e( 'Button Position', 'google-one' ); ?></th>
									<td>
										<select name="gglplsn_position">
											<option value="before_post" <?php if ( 'before_post' == $gglplsn_options['position'] ) echo 'selected="selected"';?>><?php _e( 'Before', 'google-one' ); ?></option>
											<option value="after_post" <?php if ( 'after_post' == $gglplsn_options['position'] ) echo 'selected="selected"';?>><?php _e( 'After', 'google-one' ); ?></option>
											<option value="afterandbefore" <?php if ( 'afterandbefore' == $gglplsn_options['position'] ) echo 'selected="selected"';?>><?php _e( 'Before And After', 'google-one' ); ?></option>
										</select>
										<span class="bws_info">(<?php _e( 'Please select location for the button on the page', 'google-one' ); ?>)</span>
									</td>
								</tr>
								<tr>
									<th scope="row"><?php _e( 'Show button', 'google-one' ); ?></th>
									<td>
										<p>
											<label>
												<input type="checkbox" name="gglplsn_posts" <?php if ( '1' == $gglplsn_options['posts'] ) echo 'checked="checked"'; ?> value="1" />
												<?php _e( 'Show in posts', 'google-one' ); ?>
											</label>
										</p>
										<p>
											<label>
												<input type="checkbox" name="gglplsn_pages" <?php if ( '1' == $gglplsn_options['pages'] ) echo 'checked="checked"'; ?>  value="1" />
												<?php _e( 'Show in pages', 'google-one' ); ?>
											</label>
										</p>
										<p>
											<label>
												<input type="checkbox" name="gglplsn_homepage" <?php if ( '1' == $gglplsn_options['homepage'] ) echo 'checked="checked"'; ?>  value="1" />
												<?php _e( 'Show on the homepage', 'google-one' ); ?>
											</label>
										</p>
										<p>
											<span class="bws_info">(<?php _e( 'Please select the page on which you want to see the button', 'google-one' ); ?>)</span>
										</p>
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
								
	<?php }
}

if ( ! function_exists( 'gglplsn_admin_head' ) ) {
	function gglplsn_admin_head() {
		if ( isset( $_GET['page'] ) && ( "google-plus-one.php" == $_GET['page'] || "social-buttons.php" == $_GET['page'] ) ) {
			wp_enqueue_style( 'gglplsn_style', plugins_url( 'css/style.css', __FILE__ ) );
		}
	}
}

if ( ! function_exists( 'gglplsn_js' ) ) {
	function gglplsn_js() {
		global $gglplsn_options, $gglplsn_lang_codes;		
		if ( '1' == $gglplsn_options['js'] ) {			
			if ( 1 == $gglplsn_options['use_multilanguage_locale'] && isset( $_SESSION['language'] ) ) {
				if ( array_key_exists( $_SESSION['language'], $gglplsn_lang_codes ) ) {
					$gglplsn_locale = $_SESSION['language'];
				} else {
					global $mltlngg_languages, $mltlnggpr_languages;
					if ( ! empty( $mltlngg_languages ) || ! empty( $mltlnggpr_languages ) ) {
						$languages_list = ! empty( $mltlngg_languages ) ? $mltlngg_languages : $mltlnggpr_languages;
						foreach ( $languages_list as $key => $one_lang ) {
							$mltlngg_lang_key = array_search( $_SESSION['language'], $one_lang );
							if ( false !== $mltlngg_lang_key ) {
								$gglplsn_lang_key = array_search( $one_lang[2], $gglplsn_lang_codes );
								if ( false != $gglplsn_lang_key )
									$gglplsn_locale = $gglplsn_lang_key;
								break;
							}
						}
					}
				}
			}
			if ( empty( $gglplsn_locale ) )
				$gglplsn_locale = $gglplsn_options['lang']; ?>
			<script type="text/javascript" src="https://apis.google.com/js/plusone.js">
				<?php if ( 'en-US' != $gglplsn_locale ) { ?>
					{'lang': '<?php echo $gglplsn_locale; ?>'}
				<?php } ?>
			</script>
		<?php }
	}
}


/* Google +1 on page  */
if ( ! function_exists( 'gglplsn_pos' ) ) {
	function gglplsn_pos( $content ) {
		global $gglplsn_options;		

		if ( "1" == $gglplsn_options['posts'] || '1' == $gglplsn_options['pages'] || '1' == $gglplsn_options['homepage'] ) {
			if ( ( is_single() && '1' == $gglplsn_options['posts'] ) || ( is_page() && '1' == $gglplsn_options['pages'] ) || ( ( is_home() || is_front_page() ) && '1' == $gglplsn_options['homepage'] ) ) {
				$button = '<div class="gglplsn_share"><div class="g-plusone"';
				if ( 'standard' != $gglplsn_options['size'] ) {
					$button .= ' data-size="' . $gglplsn_options['size'] . '"';
				}
				if ( 'none' == $gglplsn_options['annotation'] ) {
					$button .= ' data-annotation="none"';
				} elseif ( 'inline' == $gglplsn_options['annotation'] ) {
					$button .= ' data-annotation="inline"';
				}
				$button .= ' data-href="' . get_permalink() . '" data-callback="on"></div></div>';

				if ( 'before_post' == $gglplsn_options['position'] ) {
					return $button . $content;
				} else if ( 'after_post' == $gglplsn_options['position'] ) {
					return  $content . $button;
				} else if ( 'afterandbefore' == $gglplsn_options['position'] ) {
					return $button . $content . $button;
				}
			}			
		}
		return $content;
	}
}

/* Google +1 shortcode */
/* [bws_googleplusone] */
if ( ! function_exists( 'gglplsn_shortcode' ) ) {
	function gglplsn_shortcode( $atts ) {
		global $gglplsn_options;
		extract( shortcode_atts(
			array(
				"annotation"	=>	$gglplsn_options['annotation'],
				"url"			=>	get_permalink(),
				"size"			=>	$gglplsn_options['size']
			),
			$atts )
		);
		$shortbutton = '<br/><div class="gglplsn_share"><div class="g-plusone"';
		if ( 'standard' != $size ) {
			$shortbutton .= ' data-size="' . $size . '"';
		}
		if ( 'none' == $gglplsn_options['annotation'] ) {
			$shortbutton .= ' data-annotation="none"';
		} elseif ( 'inline' == $gglplsn_options['annotation'] ) {
			$shortbutton .= ' data-annotation="inline"';
		}
		$shortbutton .= ' data-href="' . $url . '" data-callback="on"></div></div>';
		return $shortbutton;
	}
}

/* add shortcode content  */
if ( ! function_exists( 'gglplsn_shortcode_button_content' ) ) {
	function gglplsn_shortcode_button_content( $content ) { ?>
		<div id="gglplsn" style="display:none;">
			<fieldset>				
				<?php _e( 'Add Google +1 button to your page or post', 'google-one' ); ?>
			</fieldset>
			<input class="bws_default_shortcode" type="hidden" name="default" value="[bws_googleplusone]" />
			<div class="clear"></div>
		</div>
	<?php }
}


add_action( 'init', 'gglplsn_init' );
add_action( 'plugins_loaded', 'gglplsn_plugins_loaded' );
add_action( 'admin_init', 'gglplsn_admin_init' );
/* Adding stylesheets */
add_action( 'wp_head', 'gglplsn_js' );
add_action( 'admin_enqueue_scripts', 'gglplsn_admin_head' );
/* Adding plugin buttons */
add_shortcode( 'bws_googleplusone', 'gglplsn_shortcode' );
add_filter( 'widget_text', 'do_shortcode' );
add_filter( 'the_content', 'gglplsn_pos' );
/* custom filter for bws button in tinyMCE */
add_filter( 'bws_shortcode_button_content', 'gglplsn_shortcode_button_content' );
