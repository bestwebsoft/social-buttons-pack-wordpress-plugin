<?php
if ( ! function_exists( 'fcbkbttn_plugins_loaded' ) ) {
	function fcbkbttn_plugins_loaded() {
		/* Internationalization, first(!) */
		load_plugin_textdomain( 'facebook-button-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

/* Initialization */
if ( ! function_exists( 'fcbkbttn_init' ) ) {
	function fcbkbttn_init() {
		global $fcbkbttn_plugin_info, $fcbkbttn_lang_codes, $fcbkbttn_options;

		if ( empty( $fcbkbttn_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$fcbkbttn_plugin_info = get_plugin_data( __FILE__ );
		}

				/* Get options from the database */
		if ( ! is_admin() || ( isset( $_GET['page'] ) && ( "facebook-button-plugin.php" == $_GET['page'] || "social-buttons.php" == $_GET['page'] ) ) ) {
			/* Get/Register and check settings for plugin */
			fcbkbttn_settings();

			$fcbkbttn_lang_codes = array(
				"af_ZA" => 'Afrikaans', "ar_AR" => 'العربية', "az_AZ" => 'Azərbaycan dili', "be_BY" => 'Беларуская', "bg_BG" => 'Български', "bn_IN" => 'বাংলা', "bs_BA" => 'Bosanski', "ca_ES" => 'Català', "cs_CZ" => 'Čeština', "cy_GB" => 'Cymraeg', "da_DK" => 'Dansk', "de_DE" => 'Deutsch', "el_GR" => 'Ελληνικά', "en_US" => 'English', "en_PI" => 'English (Pirate)', "eo_EO" => 'Esperanto', "es_CO" => 'Español (Colombia)', "es_ES" => 'Español (España)', "es_LA" => 'Español', "et_EE" => 'Eesti', "eu_ES" => 'Euskara', "fa_IR" => 'فارسی', "fb_LT" => 'Leet Speak', "fi_FI" => 'Suomi', "fo_FO" => 'Føroyskt', "fr_CA" => 'Français (Canada)', "fr_FR" => 'Français (France)', "fy_NL" => 'Frysk', "ga_IE" => 'Gaeilge', "gl_ES" => 'Galego', "gn_PY" => "Avañe'ẽ", "gu_IN" => 'ગુજરાતી', "he_IL" => 'עברית', "hi_IN" => 'हिन्दी', "hr_HR" => 'Hrvatski', "hu_HU" => 'Magyar', "hy_AM" => 'Հայերեն', "id_ID" => 'Bahasa Indonesia', "is_IS" => 'Íslenska', "it_IT" => 'Italiano', "ja_JP" => '日本語', "jv_ID" => 'Basa Jawa', "ka_GE" => 'ქართული', "kk_KZ" => 'Қазақша', "km_KH" => 'ភាសាខ្មែរ', "kn_IN" => 'ಕನ್ನಡ', "ko_KR" => '한국어', "ku_TR" => 'Kurdî', "la_VA" => 'lingua latina', "lt_LT" => 'Lietuvių', "lv_LV" => 'Latviešu', "mk_MK" => 'Македонски', "ml_IN" => 'മലയാളം', "mn_MN" => 'Монгол', "mr_IN" => 'मराठी', "ms_MY" => 'Bahasa Melayu', "nb_NO" => 'Norsk (bokmål)', "ne_NP" => 'नेपाली', "nl_BE" => 'Nederlands (België)', "nl_NL" => 'Nederlands', "nn_NO" => 'Norsk (nynorsk)', "pa_IN" => 'ਪੰਜਾਬੀ', "pl_PL" => 'Polski', "ps_AF" => 'پښتو', "pt_BR" => 'Português (Brasil)', "pt_PT" => 'Português (Portugal)', "ro_RO" => 'Română', "ru_RU" => 'Русский', "sk_SK" => 'Slovenčina', "sl_SI" => 'Slovenščina', "sq_AL" => 'Shqip', "sr_RS" => 'Српски', "sv_SE" => 'Svenska', "sw_KE" => 'Kiswahili', "ta_IN" => 'தமிழ்', "te_IN" => 'తెలుగు', "tg_TJ" => 'тоҷикӣ', "th_TH" => 'ภาษาไทย', "tl_PH" => 'Filipino', "tr_TR" => 'Türkçe', "uk_UA" => 'Українська', "ur_PK" => 'اردو', "uz_UZ" => "O'zbek", "vi_VN" => 'Tiếng Việt', "zh_CN" => '中文(简体)', "zh_HK" => '中文(香港)', "zh_TW" => '中文(台灣)'
			);

			if ( ! is_admin() ) {
				add_filter( 'the_content', 'fcbkbttn_display_button' );
				if ( isset( $fcbkbttn_options['display_for_excerpt'] ) && ! empty( $fcbkbttn_options['display_for_excerpt'] ) ) {
					add_filter( 'the_excerpt', 'fcbkbttn_display_button' );
				}
			}
		}
	}
}
/* End function init */

/* Function for admin_init */
if ( ! function_exists( 'fcbkbttn_admin_init' ) ) {
	function fcbkbttn_admin_init() {
		/* Add variable for bws_menu */
		global $bws_plugin_info, $fcbkbttn_plugin_info, $bws_shortcode_list;

				$bws_shortcode_list['fcbkbttn'] = array( 'name' => 'Facebook Button' );

	}
}
/* end fcbkbttn_admin_init */

if ( ! function_exists( 'fcbkbttn_settings' ) ) {
	function fcbkbttn_settings() {
		global $fcbkbttn_options, $fcbkbttn_plugin_info, $sclbttns_plugin_info;

		/* Install the option defaults */
		if ( ! get_option( 'fcbkbttn_options' ) ) {
			$options_default = fcbkbttn_get_options_default();
			add_option( 'fcbkbttn_options', $options_default );
		}

		/* Get options from the database */
		$fcbkbttn_options = get_option( 'fcbkbttn_options' );

		if ( ! isset( $fcbkbttn_options['plugin_option_version'] ) || $fcbkbttn_options['plugin_option_version'] != $fcbkbttn_plugin_info["Version"] || $fcbkbttn_options['plugin_option_version'] != $sclbttns_plugin_info["Version"] ) {
			$fcbkbttn_options['hide_premium_options'] = array();

			$options_default = fcbkbttn_get_options_default();
			$fcbkbttn_options = array_merge( $options_default, $fcbkbttn_options );
			$fcbkbttn_options['plugin_option_version'] = $fcbkbttn_plugin_info["Version"];

			update_option( 'fcbkbttn_options', $fcbkbttn_options );
		}
	}
}

if ( ! function_exists( 'fcbkbttn_get_options_default' ) ) {
	function fcbkbttn_get_options_default() {
		global $fcbkbttn_plugin_info;

		$options_default = array(
			'plugin_option_version'		=>	$fcbkbttn_plugin_info["Version"],
			'display_settings_notice'	=>	1,
			'first_install'				=>	strtotime( "now" ),
			'suggest_feature_banner'	=>	1,
			'link'						=>	'',
			'my_page'					=>	1,
			'like'						=>	1,
			'layout_like_option'		=>	'standard',
			'layout_share_option'		=>	'button_count',
			'like_action'				=>	'like',
			'color_scheme'				=>	'light',
			'share'						=>	0,
			'faces'						=>	0,
			'width'						=>	225,
			'size'						=>	'small',
			'where'						=>	array( 'before' ),
			'display_option'			=>	'standard',
			'count_icon'				=>	1,
			'extention'					=>	'png',
			'fb_img_link'				=>	plugins_url( "images/standard-facebook-ico.png", __FILE__ ),
			'locale'					=>	'en_US',
			'html5'						=>	0,
			'use_multilanguage_locale'	=>	0,
			'display_for_excerpt'		=>	0,
			'location'					=>	'left',
			'id'						=>	1443946719181573
		);
		return $options_default;
	}
}

if ( ! function_exists( 'fcbkbttn_button' ) ) {
	function fcbkbttn_button() {
		global $post, $fcbkbttn_options;

		if ( isset( $post->ID ) ) {
			$permalink_post = get_permalink( $post->ID );
		}

		$if_large = '';
		if ( $fcbkbttn_options['size'] == 'large' ) {
			$if_large = 'fcbkbttn_large_button';
		}

		if ( 'right' == $fcbkbttn_options['location'] ) {
			$button = '<div class="fcbkbttn_buttons_block" id="fcbkbttn_right">'; 
		} elseif ( 'middle' == $fcbkbttn_options['location'] ) {
			$button = '<div class="fcbkbttn_buttons_block" id="fcbkbttn_middle">'; 
		} elseif ( 'left' == $fcbkbttn_options['location'] ) {
			$button = '<div class="fcbkbttn_buttons_block" id="fcbkbttn_left">'; 
		}
		if ( ! empty( $fcbkbttn_options['my_page'] ) ) {
			$button .= '<div class="fcbkbttn_button">
							<a href="https://www.facebook.com/' . $fcbkbttn_options['link'] . '" target="_blank">
								<img src="' . $fcbkbttn_options['fb_img_link'] . '" alt="Fb-Button" />
							</a>
						</div>';
		}

		$location_share = ( 'right' == $fcbkbttn_options['location'] && "standard" == $fcbkbttn_options['layout_like_option'] ) ? 1 : 0;

		if ( ! empty( $fcbkbttn_options['share'] ) && ! empty( $location_share ) ) {
			$button .= '<div class="fb-share-button ' . $if_large . ' " data-href="' . $permalink_post . '" data-type="' . $fcbkbttn_options['layout_share_option'] . '" data-size="' . $fcbkbttn_options['size'] . '"></div>';
		}

		if ( ! empty( $fcbkbttn_options['like'] ) ) {
			$button .= '<div class="fcbkbttn_like ' . $if_large . '">';

			if ( ! empty( $fcbkbttn_options['html5'] ) ) {
				$button .= '<div class="fb-like fb-like-'. $fcbkbttn_options['layout_like_option'] .'" data-href="' . $permalink_post . '" data-colorscheme="' . $fcbkbttn_options['color_scheme'] . '" data-layout="' . $fcbkbttn_options['layout_like_option'] . '" data-action="' . $fcbkbttn_options['like_action'] . '" ';
				if ( 'standard' == $fcbkbttn_options['layout_like_option'] ) {
					$button .= ' data-width="' . $fcbkbttn_options['width'] . 'px"';
					$button .= ( ! empty( $fcbkbttn_options['faces'] ) ) ? " data-show-faces='true'" : " data-show-faces='false'";
				}
				$button .= ' data-size="' . $fcbkbttn_options['size'] . '"';
				$button .= '></div></div>';
			} else {
				$button .= '<fb:like href="' . $permalink_post . '" action="' . $fcbkbttn_options['like_action'] . '" colorscheme="' . $fcbkbttn_options['color_scheme'] . '" layout="' . $fcbkbttn_options['layout_like_option'] . '" ';
				if ( 'standard' == $fcbkbttn_options['layout_like_option'] ) {
					$button .= ( ! empty( $fcbkbttn_options['faces'] ) ) ? "show-faces='true'" : "show-faces='false'";
					$button .= ' width="' . $fcbkbttn_options['width'] . 'px"';
				}

				$button .= ' size="' . $fcbkbttn_options['size'] . '"';
				$button .= '></fb:like></div>';
			}
		}
		if ( ! empty( $fcbkbttn_options['share'] ) && empty( $location_share ) ) {
			$button .= '<div class="fb-share-button ' . $if_large . ' " data-href="' . $permalink_post . '" data-type="' . $fcbkbttn_options['layout_share_option'] . '" data-size="' . $fcbkbttn_options['size'] . '"></div>';
		}

		$button .= '</div>';

		return $button;
	}
}

/* Function taking from array 'fcbkbttn_options' necessary information to create Facebook Button and reacting to your choise in plugin menu - points where it appears. */
if ( ! function_exists( 'fcbkbttn_display_button' ) ) {
	function fcbkbttn_display_button( $content ) {

		if ( is_feed() ) {
			return $content;
		}

		global $fcbkbttn_options;

		$button = apply_filters( 'fcbkbttn_button_in_the_content', fcbkbttn_button() );
		/* Indication where show Facebook Button depending on selected item in admin page. */
		if ( ! empty( $fcbkbttn_options['where'] ) && in_array( 'before', $fcbkbttn_options['where'] ) ) {
			$content = $button . $content;
		}
		if ( ! empty( $fcbkbttn_options['where'] ) && in_array( 'after', $fcbkbttn_options['where'] ) ) {
			$content .= $button;
		}

		return $content;
	}
}

/* Function 'fcbkbttn_shortcode' is used to create content for Facebook Button shortcode. */
if ( ! function_exists( 'fcbkbttn_shortcode' ) ) {
	function fcbkbttn_shortcode( $content ) {
		global $post, $fcbkbttn_options, $fcbkbttn_shortcode_add_script;

		if ( isset( $post->ID ) )
			$permalink_post	= get_permalink( $post->ID );

		$button = fcbkbttn_button();

		if ( ( ! empty( $fcbkbttn_options['like'] ) || ! empty( $fcbkbttn_options['share'] ) ) && isset( $permalink_post ) ) {
			$fcbkbttn_shortcode_add_script = true;
		}
		return $button;
	}
}

/* add shortcode content  */
if ( ! function_exists( 'fcbkbttn_shortcode_button_content' ) ) {
	function fcbkbttn_shortcode_button_content( $content ) { ?>
		<div id="fcbkbttn" style="display:none;">
			<fieldset>
				<?php _e( 'Add Facebook buttons to your page or post', 'facebook-button-plugin' ); ?>
			</fieldset>
			<input class="bws_default_shortcode" type="hidden" name="default" value="[fb_button]" />
			<div class="clear"></div>
		</div>
	<?php }
}

/* Functions adds some right meta for Facebook */
if ( ! function_exists( 'fcbkbttn_meta' ) ) {
	function fcbkbttn_meta() {
		global $fcbkbttn_options, $post;
		if ( ! empty( $fcbkbttn_options['like'] ) || ! empty( $fcbkbttn_options['share'] ) ) {
			if ( is_singular() ) {
				$image = '';
				if ( has_post_thumbnail( get_the_ID() ) ) {
					$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium' );
					$image = $image[0];
				}

				$description = ( has_excerpt() ) ? get_the_excerpt() : strip_tags( substr( $post->post_content, 0, 200 ) );

				$url 			= apply_filters( 'fcbkbttn_meta_url', esc_attr( get_permalink() ) );
				$description	= apply_filters( 'fcbkbttn_meta_description',  esc_attr( $description ) );
				$title 			= apply_filters( 'fcbkbttn_meta_title', esc_attr( get_the_title() ) );
				$site_name 		= apply_filters( 'fcbkbttn_meta_site_name', esc_attr( get_bloginfo() ) );
				$meta_image 	= apply_filters( 'fcbkbttn_meta_image', esc_url( $image ) );
				print "\n" . '<!-- fcbkbttn meta start -->';
				print "\n" . '<meta property="og:url" content="' . $url . '"/>';
				print "\n" . '<meta property="og:title" content="' . $title . '"/>';
				print "\n" . '<meta property="og:site_name" content="' . $site_name . '"/>';
				if ( ! empty( $image ) ) {
					print "\n" . '<meta property="og:image" content="' . $meta_image . '"/>';
				}
				if ( ! empty( $description ) ) {
					print "\n" . '<meta property="og:description" content="' . $description . '"/>';
				}
				print "\n" . '<!-- fcbkbttn meta end -->';
			}
		}
	}
}

if ( ! function_exists( 'fcbkbttn_get_locale' ) ) {
	function fcbkbttn_get_locale() {
		global $fcbkbttn_options, $fcbkbttn_lang_codes, $mltlngg_current_language;
		if ( ! empty( $fcbkbttn_options['use_multilanguage_locale'] ) && isset( $mltlngg_current_language ) ) {
			if ( array_key_exists( $mltlngg_current_language, $fcbkbttn_lang_codes ) ) {
				$fcbkbttn_locale = $mltlngg_current_language;
			} else {
				$locale_from_multilanguage = explode( '_', $mltlngg_current_language );
				if ( is_array( $locale_from_multilanguage ) && array_key_exists( $locale_from_multilanguage[0], $fcbkbttn_lang_codes ) ) {
					$fcbkbttn_locale = $locale_from_multilanguage[0];
				} else {
					foreach ( $fcbkbttn_lang_codes as $language_key => $language ) {
						$locale = explode( '_', $language_key );
						if ( $locale_from_multilanguage[0] == $locale[0] ) {
							$fcbkbttn_locale = $language_key;
							break;
						}
					}
				}
			}
		}
		if ( empty( $fcbkbttn_locale ) ) {
			$fcbkbttn_locale = $fcbkbttn_options['locale'];
		}

		return $fcbkbttn_locale;
	}
}

if ( ! function_exists( 'fcbkbttn_footer_script' ) ) {
	function fcbkbttn_footer_script() {
		global $fcbkbttn_options, $fcbkbttn_shortcode_add_script;

		if ( isset( $fcbkbttn_shortcode_add_script ) ||
			( ( ! empty( $fcbkbttn_options['like'] ) || ! empty( $fcbkbttn_options['share'] ) ) && ! empty( $fcbkbttn_options['where'] ) )
			|| defined( 'BWS_ENQUEUE_ALL_SCRIPTS' ) ) { ?>
			<div id="fb-root"></div>
			<script>(function(d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0]; 

				if (d.getElementById(id)) return;
				js = d.createElement(s); js.id = id;

				js.src = "//connect.facebook.net/<?php echo fcbkbttn_get_locale(); ?>/sdk.js#xfbml=1&appId=<?php echo $fcbkbttn_options['id']; ?>&version=v2.12";
				fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));
			</script>
		<?php }
	}
}

if ( ! function_exists( 'fcbkbttn_pagination_callback' ) ) {
	function fcbkbttn_pagination_callback( $content ) {
		$content .= "if (typeof( FB ) != 'undefined' && FB != null ) { FB.XFBML.parse(); }";
		return $content;
	}
}

if ( ! function_exists( 'fcbkbttn_enqueue_scripts' ) ) {
	function fcbkbttn_enqueue_scripts() {
		if ( isset( $_GET['page'] ) && ( "facebook-button-plugin.php" == $_GET['page'] || "social-buttons.php" == $_GET['page'] ) ) {
			bws_enqueue_settings_scripts();
			bws_plugins_include_codemirror();
			wp_enqueue_script( 'fcbkbttn_script', plugins_url( 'js/admin-script.js', __FILE__ ), array( 'jquery' ) );
			wp_enqueue_style( 'fcbkbttn_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );
		} elseif ( ! is_admin() ) {
			wp_enqueue_style( 'fcbkbttn_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );
			wp_enqueue_script( 'fcbkbttn_script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery' ) );
		}
	}
}

add_action( 'plugins_loaded', 'fcbkbttn_plugins_loaded' );
add_action( 'init', 'fcbkbttn_init' );
add_action( 'admin_init', 'fcbkbttn_admin_init' );

/* Adding stylesheets */
add_action( 'wp_enqueue_scripts', 'fcbkbttn_enqueue_scripts' );
add_action( 'admin_enqueue_scripts', 'fcbkbttn_enqueue_scripts' );
/* Adding front-end stylesheets */
add_action( 'wp_head', 'fcbkbttn_meta' );
add_action( 'wp_footer', 'fcbkbttn_footer_script' );
add_filter( 'pgntn_callback', 'fcbkbttn_pagination_callback' );
/* Add shortcode and plugin buttons */
add_shortcode( 'fb_button', 'fcbkbttn_shortcode' );
/* custom filter for bws button in tinyMCE */
add_filter( 'bws_shortcode_button_content', 'fcbkbttn_shortcode_button_content' );
