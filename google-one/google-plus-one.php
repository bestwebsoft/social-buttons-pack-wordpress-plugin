<?php


if ( ! function_exists ( 'gglplsn_init' ) ) {
	function gglplsn_init() {
		global $gglplsn_plugin_info;
		/* Internationalization */
		load_plugin_textdomain( 'google_plus_one', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		if ( empty( $gglplsn_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$gglplsn_plugin_info = get_plugin_data( __FILE__ );
		}

		

		/* Get/Register and check settings for plugin */
		if ( ! is_admin() || ( isset( $_GET['page'] ) && ( "google-plus-one.php" == $_GET['page'] || "social-buttons.php" == $_GET['page'] ) ) )
			gglplsn_settings();
	}
}



if ( ! function_exists ( 'gglplsn_settings' ) ) {
	function gglplsn_settings() {
		global $gglplsn_options, $gglplsn_plugin_info, $gglplsn_option_defaults;

		/* Default options */
		$gglplsn_option_defaults	=	array(
			'plugin_option_version' => $gglplsn_plugin_info["Version"],
			'js'					=>	'1',
			'annotation'			=>	'0',
			'size'					=>	'standart',
			'position'				=>	'before_post',
			'lang'					=>	'en-GB',
			'posts'					=>	'1',
			'pages'					=>	'1',
			'homepage'				=>	'1'
		);

		if ( ! get_option( 'gglplsn_options' ) )
			add_option( 'gglplsn_options', $gglplsn_option_defaults );

		$gglplsn_options = get_option( 'gglplsn_options' );

		if ( ! isset( $gglplsn_options['plugin_option_version'] ) || $gglplsn_options['plugin_option_version'] != $gglplsn_plugin_info["Version"] ) {
			$gglplsn_options = array_merge( $gglplsn_option_defaults, $gglplsn_options );
			$gglplsn_options['plugin_option_version'] = $gglplsn_plugin_info["Version"];
			update_option( 'gglplsn_options', $gglplsn_options );
		}
	}
}

/* Add settings page in admin area */
if ( ! function_exists( 'gglplsn_options' ) ) {
	function gglplsn_options() {
		global $gglplsn_options, $wp_version, $gglplsn_plugin_info, $gglplsn_option_defaults;
		$message = $error = "";
		$plugin_basename = plugin_basename( __FILE__ );

		/* Save data for settings page */
		if ( isset( $_REQUEST['gglplsn_form_submit'] ) && check_admin_referer( $plugin_basename, 'gglplsn_nonce_name' ) ) {
			$gglplsn_options['js']			=	isset( $_REQUEST['gglplsn_js'] ) ? 1 : 0 ;
			$gglplsn_options['annotation']	=	isset( $_REQUEST['gglplsn_annotation'] ) ? 1 : 0 ;
			$gglplsn_options['size']		=	$_REQUEST['gglplsn_size'];
			$gglplsn_options['position']	=	$_REQUEST['gglplsn_position'];
			$gglplsn_options['lang']		=	$_REQUEST['gglplsn_lang'];
			$gglplsn_options['posts']		=	isset( $_REQUEST['gglplsn_posts'] ) ? 1 : 0 ;
			$gglplsn_options['pages']		=	isset( $_REQUEST['gglplsn_pages'] ) ? 1 : 0 ;
			$gglplsn_options['homepage']	=	isset( $_REQUEST['gglplsn_homepage'] ) ? 1 : 0 ;
			$message = __( 'Settings saved', 'google_plus_one' );
			update_option( 'gglplsn_options', $gglplsn_options );
		}
		$lang_codes = array(
			'af' => "Afrikaans", 'am' => "Amharic", 'ar' => "Arabic", 'eu' => "Basque", 'bn' => "Bengali", 'bg' => "Bulgarian", 'ca' => "Catalan", 'zh-HK' => "Chinese (Hong Kong)", 'zn-CH' => "Chinese (Simplified)", 'zh-TW' => "Chinese (Traditional)", 'hr' => "Croatian", 'cs' => "Czech", 'da' => "Danish", 'nl' => "Dutch", 'en-GB' => "English (UK)", 'en-US' => "English (US)", 'et' => "Estonian", 'fil' => "Filipino", 'fi' => "Finnish", 'fr' => "French", 'fr-CA' => "French (Canadian)", 'gl' => "Galician", 'de' => "German", 'el' => "Greek", 'gu' => "Gujarati", 'iw' => "Hebrew", 'hi' => "Hindi", 'hu' => "Hungarian", 'is' => "Icelandic", 'id' => "Indonesian", 'it' => "Italian", 'ja' => "Japanese", 'kn' => "Kannada", 'ko' => "Korean", 'lv' => "Latvian", 'lt' => "Lithuanian", 'ms' => "Malay", 'ml' => "Malayalam", 'mr' => "Marathi", 'no' => "Norwegian", 'fa' => "Persian", 'pl' => "Polish", 'pt-BR' => "Portuguese (Brazil)", 'pt-PT' => "Portuguese (Portugal)", 'ro' => "Romanian", 'ru' => "Russian", 'sr' => "Serbian", 'sk' => "Slovak", 'sl' => "Slovenian", 'es' => "Spanish", 'es-419' => "Spanish (Latin America)", 'sw' => "Swahili", 'sv' => "Swedish", 'ta' => "Tamil", 'te' => "Telugu", 'th' => "Thai", 'tr' => "Turkish", 'uk' => "Ukrainian", 'ur' => "Urdu", 'vi' => "Vietnamese", 'zu' => "Zulu"
		);

		

		?>
		
			<div class="updated fade" <?php if ( '' == $message || "" != $error ) echo 'style="display:none"'; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<div id="gglplsn_settings_notice" class="updated fade bws_settings_form_notice" style="display:none"><p><strong><?php _e( "Notice:", 'google_plus_one' ); ?></strong> <?php _e( "The plugin's settings have been changed. In order to save them please don't forget to click the 'Save Changes' button.", 'google_plus_one' ); ?></p></div>
			<div class="error" <?php if ( "" == $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $error; ?></strong></p></div>
			<?php ?>
					<p><?php _e( 'For the correct work of the button do not use it locally or on a free hosting', 'google_plus_one' ); ?><br /></p>
					<p><?php _e( 'If you want to insert the button in any place on the site, please use the following code:', 'google_plus_one' ); ?> [bws_googleplusone]</p>
					<form method="post" action="" id="gglplsn_settings_form" class="bws_settings_form">
						<table class="form-table gglplsn_form-table">
							<tbody>
								<tr valign="top">
									<th><?php _e( 'Enable Google +1 Button', 'google_plus_one' ); ?></th>
									<td>
										<label>
											<input type="checkbox" name="gglplsn_js"<?php if ( '1' == $gglplsn_options['js'] ) echo 'checked="checked"'; ?> value="1" />
											<span class="gglplsn_info">(<?php _e( 'Enable or Disable Google+1 JavaScript', 'google_plus_one' ); ?>)</span>
										</label>
									</td>
								</tr>
								<tr valign="top">
									<th><?php _e( 'Show +1 count in the button', 'google_plus_one' ); ?></th>
									<td>
										<label>
											<input type="checkbox" name="gglplsn_annotation" <?php if ( '1' == $gglplsn_options['annotation'] ) echo 'checked="checked"'; ?> value="1" />
											<span class="gglplsn_info">(<?php _e( 'Display counters showing how many times your article has been liked', 'google_plus_one' ); ?>)</span>
										</label>
									</td>
								</tr>
								<tr>
									<th scope="row"><?php _e( 'Button Size', 'google_plus_one' ); ?></th>
									<td class="gglplsn_no_padding">
										<select name="gglplsn_size">
											<option value="standart" <?php if ( 'standart' == $gglplsn_options['size'] ) echo 'selected="selected"';?>> <?php _e( 'Standart', 'google_plus_one' ); ?></option>
											<option value="small" <?php if ( 'small' == $gglplsn_options['size'] ) echo 'selected="selected"';?>> <?php _e( 'Small', 'google_plus_one' ); ?></option>
											<option value="medium" <?php if ( 'medium' == $gglplsn_options['size'] ) echo 'selected="selected"';?>><?php _e( 'Medium', 'google_plus_one' ); ?></option>
											<option value="tall" <?php if ( 'tall' == $gglplsn_options['size'] ) echo 'selected="selected"';?>><?php _e( 'Tall', 'google_plus_one' ); ?></option>
										</select>
										<span class="gglplsn_info">(<?php _e( 'Please choose one of four different sizes of buttons', 'google_plus_one' ); ?>)</span>
									</td>
								</tr>
								<tr>
									<th scope="row"><?php _e( 'Button Position', 'google_plus_one' ); ?></th>
									<td class="gglplsn_no_padding">
										<select name="gglplsn_position">
											<option value="before_post" <?php if ( 'before_post' == $gglplsn_options['position'] ) echo 'selected="selected"';?>><?php _e( 'Before', 'google_plus_one' ); ?></option>
											<option value="after_post" <?php if ( 'after_post' == $gglplsn_options['position'] ) echo 'selected="selected"';?>><?php _e( 'After', 'google_plus_one' ); ?></option>
											<option value="afterandbefore" <?php if ( 'afterandbefore' == $gglplsn_options['position'] ) echo 'selected="selected"';?>><?php _e( 'Before And After', 'google_plus_one' ); ?></option>
										</select>
										<span class="gglplsn_info">(<?php _e( 'Please select location for the button on the page', 'google_plus_one' ); ?>)</span>
									</td>
								</tr>
								<tr>
									<th scope="row"><?php _e( 'Language', 'google_plus_one' ); ?></th>
									<td class="gglplsn_no_padding">
										<select name="gglplsn_lang">
											<?php foreach ( $lang_codes as $key => $val ) {
												echo '<option value="' . $key . '"';
												if ( $key == $gglplsn_options['lang'] )
													echo ' selected="selected"';
												echo '>' . esc_html ( $val ) . '</option>';
											} ?>
										</select>
										<span class="gglplsn_info">(<?php _e( 'Select the language to display information on the button', 'google_plus_one' ); ?>)</span>
									</td>
								</tr>
								<tr>
									<th scope="row"><?php _e( 'Show button', 'google_plus_one' ); ?></th>
									<td>
										<p>
											<label>
												<input type="checkbox" name="gglplsn_posts" <?php if ( '1' == $gglplsn_options['posts'] ) echo 'checked="checked"'; ?> value="1" />
												<?php _e( 'Show in posts', 'google_plus_one' ); ?>
											</label>
										</p>
										<p>
											<label>
												<input type="checkbox" name="gglplsn_pages" <?php if ( '1' == $gglplsn_options['pages'] ) echo 'checked="checked"'; ?>  value="1" />
												<?php _e( 'Show in pages', 'google_plus_one' ); ?>
											</label>
										</p>
										<p>
											<label>
												<input type="checkbox" name="gglplsn_homepage" <?php if ( '1' == $gglplsn_options['homepage'] ) echo 'checked="checked"'; ?>  value="1" />
												<?php _e( 'Show on the homepage', 'google_plus_one' ); ?>
											</label>
										</p>
										<p>
											<span class="gglplsn_info">(<?php _e( 'Please select the page on which you want to see the button', 'google_plus_one' ); ?>)</span>
										</p>
									</td>
								</tr>
							</tbody>
						</table>
						<input type="hidden" name="gglplsn_form_submit" value="1" />
						<p class="submit">
							<input type="submit" value="<?php _e( 'Save Changes', 'google_plus_one' ); ?>" class="button-primary" />
						</p>
						<?php wp_nonce_field( $plugin_basename, 'gglplsn_nonce_name' ); ?>
					</form>
								
	<?php }
}

if ( ! function_exists( 'gglplsn_admin_head' ) ) {
	function gglplsn_admin_head() {
		if ( isset( $_GET['page'] ) && ( "google-plus-one.php" == $_GET['page'] || "social-buttons.php" == $_GET['page'] ) ) {
			wp_enqueue_style( 'gglplsn_style', plugins_url( 'css/style.css', __FILE__ ) );
			if ( isset( $_GET['page'] ) && "google-plus-one.php" == $_GET['page'] )
				wp_enqueue_script( 'gglplsn_script', plugins_url( 'js/script.js', __FILE__ ) );
		}
	}
}

if ( ! function_exists( 'gglplsn_js' ) ) {
	function gglplsn_js() {
		global $gglplsn_options;
		if ( '1' == $gglplsn_options['js'] ) { ?>
			<script type="text/javascript" src="https://apis.google.com/js/plusone.js">
				<?php if ( 'en-US' != $gglplsn_options['lang'] ) { ?>
					{'lang': '<?php echo $gglplsn_options['lang']; ?>'}
				<?php } ?>
			</script>
		<?php }
	}
}

/* Google +1 button */
if ( ! function_exists( 'gglplsn_button' ) ) {
	function gglplsn_button( $content ) {
		global $gglplsn_options;
		if ( ( is_single() && '1' == $gglplsn_options['posts'] ) || ( is_page() && '1' == $gglplsn_options['pages'] ) || ( ( is_home() || is_front_page() ) && '1' == $gglplsn_options['homepage'] ) ) {
			$content .= '<div class="gglplsn_share"><div class="g-plusone"';
			if ( 'standard' != $gglplsn_options['size'] ) {
				$content .= ' data-size="' . $gglplsn_options['size'] . '"';
			}
			if ( '1' != $gglplsn_options['annotation'] ) {
				$content .= ' data-annotation="none"';
			}
			$content .= ' data-href="' . get_permalink() . '" data-callback="on"></div></div>';
		}
		return $content;
	}
}

/* Google +1 position on page  */
if ( ! function_exists( 'gglplsn_pos' ) ) {
	function gglplsn_pos( $content ) {
		global $gglplsn_options;
		$button = gglplsn_button( '' );
		if ( "1" == $gglplsn_options['posts'] || '1' == $gglplsn_options['pages'] || '1' == $gglplsn_options['homepage'] ) {
			if ( 'before_post' == $gglplsn_options['position'] ) {
				return $button . $content;
			} else if ( 'after_post' == $gglplsn_options['position'] ) {
				return  $content . $button;
			} else if ( 'afterandbefore' == $gglplsn_options['position'] ){
				return $button . $content . $button;
			}
		} else {
			return $content;
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
		if ( '1' != $annotation ) {
			$shortbutton .= ' data-annotation="none"';
		}
		$shortbutton .= ' data-href="' . $url . '" data-callback="on"></div></div>';
		return $shortbutton;
	}
}


add_action( 'init', 'gglplsn_init' );

add_action( 'wp_head', 'gglplsn_js' );
add_action( 'admin_enqueue_scripts', 'gglplsn_admin_head' );
/* Adding plugin buttons */
add_shortcode( 'bws_googleplusone', 'gglplsn_shortcode' );
add_filter( 'widget_text', 'do_shortcode' );
add_filter( 'the_content', 'gglplsn_pos' );
