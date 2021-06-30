<?php
if ( ! function_exists( 'lnkdn_plugins_loaded' ) ) {
	function lnkdn_plugins_loaded() {
		/* Internationalization, first(!) */
		load_plugin_textdomain( 'bws-linkedin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

/* Initialization */
if ( ! function_exists( 'lnkdn_init' ) ) {
	function lnkdn_init() {
		global $lnkdn_plugin_info, $lnkdn_lang_codes;

		if ( empty( $lnkdn_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			$lnkdn_plugin_info = get_plugin_data( __FILE__ );
		}

				/* Get options from the database */
		if ( ! is_admin() || ( isset( $_GET['page'] ) && ( "linkedin.php" == $_GET['page'] || "social-buttons.php" == $_GET['page'] ) ) ) {
			/* Get/Register and check settings for plugin */
			lnkdn_settings();
			$lnkdn_lang_codes = array(
				"en_US" => 'English', "ar_AE" => 'Arabic', "zh_CN" => 'Chinese - Simplified', "zh_TW" => 'Chinese - Traditional', "cs_CZ" => 'Czech', "da_DK" => 'Danish', "nl_NL" => 'Dutch', "fr_FR" => 'French', "de_DE" => 'German', "in_ID" => 'Indonesian', "it_IT" => 'Italian', "ja_JP" => 'Japanese', "ko_KR" => 'Korean', "ms_MY" => 'Malay', "no_NO" => 'Norwegian', "pl_PL" => 'Polish', "pt_BR" => 'Portuguese', "ro_RO" => 'Romanian', "ru_RU" => 'Russian', "es_ES" => 'Spanish', "sv_SE" => 'Swedish', "tl_PH" => 'Tagalog', "th_TH" => 'Thai', "tr_TR" => 'Turkish'
			);
		}
	}
}

/* Function for admin_init */
if ( ! function_exists( 'lnkdn_admin_init' ) ) {
	function lnkdn_admin_init() {
		global $bws_plugin_info, $lnkdn_plugin_info, $bws_shortcode_list, $pagenow, $lnkdn_options;

		/* pls*/

		/* Add LinkedIn to global $bws_shortcode_list */
		$bws_shortcode_list['lnkdn'] = array( 'name' => 'LinkedIn Button', 'js_function' => 'lnkdn_shortcode_init' );
	}
}

if ( ! function_exists ( 'lnkdn_settings' ) ) {
	function lnkdn_settings() {
		global $lnkdn_options, $lnkdn_plugin_info;

		/* install the option defaults */
		if ( ! get_option( 'lnkdn_options' ) ) {
			$options_defaults = lnkdn_get_options_default();
			add_option( 'lnkdn_options', $options_defaults );
		}

		$lnkdn_options = get_option( 'lnkdn_options' );

		if ( ! isset( $lnkdn_options['plugin_option_version'] ) || $lnkdn_options['plugin_option_version'] != $lnkdn_plugin_info['Version'] ) {

						$options_defaults = lnkdn_get_options_default();
			$lnkdn_options = array_merge( $options_defaults, $lnkdn_options );
			$lnkdn_options['plugin_option_version'] = $options_defaults['plugin_option_version'];

			update_option( 'lnkdn_options', $lnkdn_options );
		}
	}
}

if ( ! function_exists( 'lnkdn_get_options_default' ) ) {
	function lnkdn_get_options_default() {
		global $lnkdn_plugin_info;

		$options_default = array(
			'plugin_option_version'		=> $lnkdn_plugin_info['Version'],
			'display_settings_notice'	=> 1,
			'suggest_feature_banner'	=> 1,
			'follow'					=> 0,
			'follow_count_mode'			=> 'top',
			'follow_page_name'			=> '',
			'homepage'					=> 1,
			'pages'						=> 1,
			'posts'						=> 1,
			'lang'						=> 'en_US',
			'position'					=> array( 'before_post' ),
			'use_multilanguage_locale'	=> 0,
			'share'                     => 0,
            'share_url'					=> ''
		);

		return $options_default;
	}
}

if ( ! function_exists( 'lnkdn_return_button' ) ) {
	function lnkdn_return_button( $request ) {
		global $lnkdn_options;

		if ( empty( $lnkdn_options['share_url'] ) ) {
			$share_url = get_permalink();
		} else {
			$share_url = $lnkdn_options['share_url'];
		}

		if ( 'share' == $request ) {
			$share = '<div class="lnkdn-share-button">
						<script type="IN/Share" data-url="' . $share_url . '" data-counter="' . '"></script>
					</div>';
			return $share;
		}

		if ( 'follow' == $request && '' != $lnkdn_options['follow_page_name'] ) {
			$follow = '<div class="lnkdn-follow-button">
						<script type="IN/FollowCompany" data-id="' . $lnkdn_options['follow_page_name'] . '" data-counter="' . $lnkdn_options['follow_count_mode'] . '"></script>
					</div>';
			return $follow;
		}
	}
}

/* LinkedIn buttons on page */
if ( ! function_exists( 'lnkdn_position' ) ) {
	function lnkdn_position( $content ) {
		global $lnkdn_options;

		if ( is_feed() )
			return $content;

		if ( ! empty( $lnkdn_options['position'] ) ) {
			$display_button = false;

			if ( ( ! is_home() && ! is_front_page() ) || 1 == $lnkdn_options['homepage'] ) {
				if ( ( is_single() && 1 == $lnkdn_options['posts'] ) || ( is_page() && 1 == $lnkdn_options['pages'] ) || ( is_home() && 1 == $lnkdn_options['homepage'] ) ) {
					$display_button = true;
				}
			}

			$display_button = apply_filters( 'lnkdn_button_in_the_content', $display_button );

			if ( $display_button ) {
				$share = ( 1 == $lnkdn_options['share'] ) ? lnkdn_return_button( 'share' ) : '';
				$follow = ( 1 == $lnkdn_options['follow'] ) ? lnkdn_return_button( 'follow' ) : '';
				$button = '<div class="lnkdn_buttons">' . $share . $follow . '</div>';

				if ( in_array( 'before_post', $lnkdn_options['position'] ) )
					$content = $button . $content;
				if ( in_array( 'after_post', $lnkdn_options['position'] ) )
					$content .= $button;
			}
		}
		return $content;
	}
}

if ( ! function_exists( 'lnkdn_admin_head' ) ) {
	function lnkdn_admin_head() {
		wp_enqueue_style( 'lnkdn_icon', plugins_url( 'css/icon.css', __FILE__ ) );

		if ( ! is_admin() ) {
			wp_enqueue_style( 'lnkdn_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );
			lnkdn_js();
		} elseif ( isset( $_GET['page'] ) && ( 'linkedin.php' == $_GET['page'] || "social-buttons.php" == $_GET['page'] ) ) {
			wp_enqueue_style( 'lnkdn_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );
			bws_enqueue_settings_scripts();
			bws_plugins_include_codemirror();
		}
	}
}

/* lnkdn script add */
if ( ! function_exists( 'lnkdn_js' ) ) {
	function lnkdn_js() {
		global $lnkdn_options, $lnkdn_shortcode_add_script, $lnkdn_js_added;
		
		if ( isset( $lnkdn_js_added ) )
			return;

		if ( 1 == $lnkdn_options['share'] || 1 == $lnkdn_options['follow'] || isset( $lnkdn_shortcode_add_script ) || defined( 'BWS_ENQUEUE_ALL_SCRIPTS' ) ) {
			wp_enqueue_script( 'in.js', '//platform.linkedin.com/in.js', array(), null, true );

			$lnkdn_js_added = true;	
		}
	}
}

if ( ! function_exists( 'lnkdn_add_lang_to_script' ) ) {
	function lnkdn_add_lang_to_script( $tag, $handle ) {
		global $lnkdn_options, $lnkdn_lang_codes, $mltlngg_current_language;

		if ( 'in.js' === $handle ) {	
			
			if ( 1 == $lnkdn_options['use_multilanguage_locale'] && isset( $mltlngg_current_language ) ) {
				if ( array_key_exists( $mltlngg_current_language, $lnkdn_lang_codes ) ) {
					$lnkdn_locale = $mltlngg_current_language;
				} else {
					$lnkdn_locale_from_multilanguage = str_replace( '_', '-', $mltlngg_current_language );
					if ( array_key_exists( $lnkdn_locale_from_multilanguage, $lnkdn_lang_codes ) ) {
						$lnkdn_locale = $lnkdn_locale_from_multilanguage;
					} else {
						$lnkdn_locale_from_multilanguage = explode( '_', $mltlngg_current_language );
						if ( is_array( $lnkdn_locale_from_multilanguage ) && array_key_exists( $lnkdn_locale_from_multilanguage[0], $lnkdn_lang_codes ) ) {
							$lnkdn_locale = $lnkdn_locale_from_multilanguage[0];
						}
					}
				}
			}

			if ( empty( $lnkdn_locale ) ) {
				$lnkdn_locale = $lnkdn_options['lang'];
			}
			$return_string = 'lang: ' . $lnkdn_locale;
			$tag = preg_replace( ':(?=</script>):', " $return_string", $tag, 1 );
		}
		return $tag;
		
	}
}

if ( ! function_exists( 'lnkdn_pagination_callback' ) ) {
	function lnkdn_pagination_callback( $content ) {
		$content .= "if ( typeof( IN ) != 'undefined' ) { IN.parse(); }";
		return $content;
	}
}

/* LinkedIn Buttons shortcode */
/* [bws_linkedin display="share,follow"] */
if ( ! function_exists( 'lnkdn_shortcode' ) ) {
	function lnkdn_shortcode( $atts ) {
		global $lnkdn_options, $lnkdn_shortcode_add_script;

		$buttons = '';
		$shortcode_atts = shortcode_atts( array( 'display' => 'share,follow' ), $atts );
		$shortcode_atts = ( str_word_count( $shortcode_atts['display'], 1 ) );

		foreach ( $shortcode_atts as $value ) {
			if ( 'share' === $value ) {
				$buttons .= lnkdn_return_button( 'share' );
			}
			if ( 'follow' === $value ) {
				$buttons .= lnkdn_return_button( 'follow' );
			}
		}
		$lnkdn_shortcode_add_script = true;
		lnkdn_js();

		return '<div class="lnkdn_buttons">' . $buttons . '</div>';
	}
}

/* add shortcode content */
if ( ! function_exists( 'lnkdn_shortcode_button_content' ) ) {
	function lnkdn_shortcode_button_content( $content ) {
		global $wp_version; ?>
		<div id="lnkdn" style="display:none;">
			<fieldset>
				<label>
					<input type="checkbox" name="lnkdn_selected_share" value="share" checked="checked" />
					<?php _e( 'LinkedIn Share Button', 'bws-linkedin' ) ?>
				</label>
				<br />
				<label>
					<input type="checkbox" name="lnkdn_selected_follow" value="follow" checked="checked" />
					<?php _e( 'LinkedIn Follow Button', 'bws-linkedin' ) ?>
				</label>
				<input class="bws_default_shortcode" type="hidden" name="default" value='[bws_linkedin display="share,follow"]' />
				<div class="clear"></div>
			</fieldset>
		</div>
		<?php $script = "function lnkdn_shortcode_init() {
				( function( $ ) {
					$( '.mce-reset input[name^=\"lnkdn_selected\"]' ).change( function() {
						var result = '';
						$( '.mce-reset input[name^=\"lnkdn_selected\"]' ).each( function() {
							if ( $( this ).is( ':checked' ) ) {
								result += $( this ).val() + ',';
							}
						} );
						if ( '' == result ) {
							$( '.mce-reset #bws_shortcode_display' ).text( '' );
						} else {
							result = result.slice( 0, - 1 );
							$( '.mce-reset #bws_shortcode_display' ).text( '[bws_linkedin display=\"' + result + '\"]' );
						}
					} );
				} ) ( jQuery );
			}";
		wp_register_script( 'lnkdn_bws_shortcode_button', '' );
		wp_enqueue_script( 'lnkdn_bws_shortcode_button' );
		wp_add_inline_script( 'lnkdn_bws_shortcode_button', sprintf( $script ) );
	}
}

/* Adding class in 'body' Twenty Fifteen/Sixteen Theme for LinkedIn Buttons */
if ( ! function_exists( 'lnkdn_add_body_class' ) ) {
	function lnkdn_add_body_class( $classes ) {
		$current_theme = wp_get_theme();
		if ( 'Twenty Fifteen' == $current_theme->get( 'Name' ) || 'Twenty Sixteen' == $current_theme->get( 'Name' ) ) {
			$classes[] = 'lnkdn-button-certain-theme';
		}
		if ( 'Twenty Twelve' == $current_theme->get( 'Name' ) ) {
			$classes[] = 'lnkdn-button-twenty-twelve-theme';
		}
		return $classes;
	}
}

add_action( 'init', 'lnkdn_init' );
add_action( 'admin_init', 'lnkdn_admin_init' );
add_action( 'plugins_loaded', 'lnkdn_plugins_loaded' );
/* Adding stylesheets */
add_action( 'admin_enqueue_scripts', 'lnkdn_admin_head' );
add_action( 'wp_enqueue_scripts', 'lnkdn_admin_head' );
add_filter( 'script_loader_tag', 'lnkdn_add_lang_to_script', 10, 2 );
add_filter( 'pgntn_callback', 'lnkdn_pagination_callback' );
/* Adding plugin buttons */
add_shortcode( 'bws_linkedin', 'lnkdn_shortcode' );
add_filter( 'the_content', 'lnkdn_position' );
/* custom filter for bws button in tinyMCE */
add_filter( 'bws_shortcode_button_content', 'lnkdn_shortcode_button_content' );
/* Adding class in 'body' Twenty Fifteen/Sixteen Theme for LinkedIn Buttons */
add_filter( 'body_class', 'lnkdn_add_body_class' );
