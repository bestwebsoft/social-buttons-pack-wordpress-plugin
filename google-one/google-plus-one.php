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
		global $gglplsn_options, $gglplsn_plugin_info, $sclbttns_plugin_info;


		if ( ! get_option( 'gglplsn_options' ) ) {
			$options_default = gglplsn_get_options_default();
			add_option( 'gglplsn_options', $options_default );
		}

		$gglplsn_options = get_option( 'gglplsn_options' );

		if ( ! isset( $gglplsn_options['plugin_option_version'] ) || $gglplsn_options['plugin_option_version'] != $gglplsn_plugin_info["Version"] || $gglplsn_options['plugin_option_version'] != $sclbttns_plugin_info["Version"] ) {

			/**
			* @since 1.3.5
			* @todo remove after 12.10.2017
			*/
			if ( ! is_array( $gglplsn_options['position'] ) ) {
				switch ( $gglplsn_options['position'] ) {
					case 'only_shortcode':
						$gglplsn_options['position'] = array();
						break;
					case 'afterandbefore':
						$gglplsn_options['position'] = array( 'before', 'after' );
						break;
					case 'before_post':
						$gglplsn_options['position'] = array( 'before' );
						break;
					case 'after_post':
						$gglplsn_options['position'] = array( 'after' );
						break;
				}
			}
			/* end @todo */

			$options_default = gglplsn_get_options_default();
			$gglplsn_options = array_merge( $options_default, $gglplsn_options );
			$gglplsn_options['plugin_option_version'] = $gglplsn_plugin_info["Version"];
			/* show pro features */
			$gglplsn_options['hide_premium_options'] = array();

			update_option( 'gglplsn_options', $gglplsn_options );
		}
	}
}

if ( ! function_exists( 'gglplsn_get_options_default' ) ) {
	function gglplsn_get_options_default() {
		global $gglplsn_plugin_info;

		$options_default = array(
			'plugin_option_version'		=>	$gglplsn_plugin_info['Version'],
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
			'position'					=>	array( 'before' ),
			'homepage'					=>	1,
			'posts'						=>	1,
			'pages'						=>	1,
			'lang'						=>	'en',
			'homepage'					=>	1,
			'use_multilanguage_locale'	=>	0,
			'display_settings_notice'	=>	1,
			'first_install'				=>	strtotime( "now" ),
			'suggest_feature_banner'	=>	1
		);

		return $options_default;
	}
}

if ( ! function_exists( 'gglplsn_admin_head' ) ) {
	function gglplsn_admin_head() {
		global $hook_suffix, $gglplsn_is_button_shown, $gglplsn_plugin_info;
		if ( isset( $_GET['page'] ) && ( "google-plus-one.php" == $_GET['page'] || "social-buttons.php" == $_GET['page'] ) ) {
			wp_enqueue_style( 'gglplsn_style', plugins_url( 'css/style.css', __FILE__ ), array(), $gglplsn_plugin_info['Version'] );
			/* Loclize script */
			wp_enqueue_script( 'gglplsn-script', plugins_url( 'js/script.js' , __FILE__ ), array( 'jquery' ), $gglplsn_plugin_info['Version'] );
			
			$js_strings = array(
				'already_added'				=>	__( 'Is already added.', 'google-one' ),
				'one_number'				=>	__( 'Only one phone number can be added.', 'google-one' ),
				'number_added'				=>	__( "You can't add the invitation because the phone number is already added.", 'google-one' ),
				'any_added'					=>	__( "You can't add the phone number because another invitation type is already added.", 'google-one' ),
				'empty_id'					=>  __( "This field can't be empty.", 'google-one' ),
				'invalid_email'     		=>  __( 'Enter the valid Email.', 'google-one' ),
				'email_th'					=>  __( 'Email of Invited Person', 'google-one' ),
				'email_info'				=>  __( 'Enter the Email of invited person.', 'google-one' ) . '&nbsp;' . __( 'For example', 'google-one' ) . ',&nbsp;"example@gmail.com".',
				'phone_th'					=>  __( 'Phone Number of Invited Person', 'google-one' ),
				'phone_info'				=>  __( 'Enter the phone number of invited person.', 'google-one' ) . '&nbsp;' . __( 'For example', 'google-one' ) . ',&nbsp;"+38001234567".',
				'profile_th'				=>  __( 'Google+ Profile ID of Invited Person', 'google-one' ),
				'profile_info'				=>  __( 'Enter the Google+ Profile ID of invited person.', 'google-one' ) . '&nbsp;' . __( 'For example', 'google-one' ) . ',&nbsp;"12345678912345678912"&nbsp;' . __( 'or', 'google-one' ) . '&nbsp;"+YouName".',
				'circle_th'					=>  __( 'Google+ Circle ID for the Invitation', 'google-one' ),
				'circle_info'				=>  __( 'Enter the Google+ Circle ID for the Invitation.', 'google-one' ) . '&nbsp;' . __( 'For example', 'google-one' ) . ',&nbsp;"123ab345cd576ef7".',
				'person_id_th'				=>	__( 'Google+ ID', 'google-one' ),
				'person_id_info'			=>	__( 'Enter the Google+ ID.', 'google-one' ) . '&nbsp;' . __( 'For example', 'google-one' ) . ',&nbsp;"12345678912345678912"&nbsp;' . __( 'or', 'google-one' ) . '&nbsp;"+YouName".',
				'page_id_th'				=>	__( 'Google+ Page ID', 'google-one' ),
				'page_id_info'				=>	__( 'Enter the Google+ Page ID.', 'google-one' ) . '&nbsp;' . __( 'For example', 'google-one' ) . ',&nbsp;"12345678912345678912"&nbsp;' . __( 'or', 'google-one' ) . '&nbsp;"+CompanyName".',
				'community_id_th'			=>	__( 'Google+ Community ID', 'google-one' ),
				'community_id_info'			=>	__( 'Enter the Google+ Community ID.', 'google-one' ) . '&nbsp;' . __( 'For example', 'google-one' ) . ',&nbsp;"12345678912345678912"&nbsp;' . __( 'or', 'google-one' ) . '&nbsp;"+CommunityName".',
				'person_tagline_info'		=>	__( "Display the user's tag line.", 'google-one' ),
				'page_tagline_info'			=>	__( 'Display the company tag line.', 'google-one' ),
				'community_tagline_info'	=>	__( 'Display the community tag line.', 'google-one' ),
				'gglplsn_ajax_nonce'		=>	wp_create_nonce( 'gglplsn_ajax_nonce' )
			);
			wp_localize_script( 'gglplsn-script', 'js_string', $js_strings );

			bws_enqueue_settings_scripts();
			bws_plugins_include_codemirror();

		} elseif ( 'widgets.php' == $hook_suffix ) {
			wp_enqueue_script( 'gglplsn-widgets-script', plugins_url( 'js/widgets-script.js' , __FILE__ ), array( 'jquery' ), $gglplsn_plugin_info['Version'] );
		} elseif ( ! is_admin() && ( ! empty( $gglplsn_is_button_shown ) || defined( 'BWS_ENQUEUE_ALL_SCRIPTS' ) ) ) {
			wp_enqueue_style( 'gglplsn_style', plugins_url( 'css/style.css', __FILE__ ), array(), $gglplsn_plugin_info['Version'] );
		}
	}
}

if ( ! function_exists( 'gglplsn_footer_actions' ) ) {
	function gglplsn_footer_actions() {
		gglplsn_js();
		gglplsn_admin_head();
	}
}

if ( ! function_exists( 'gglplsn_pagination_callback' ) ) {
	function gglplsn_pagination_callback( $content ) {
		$content .= "if ( typeof gapi !== 'undefined' ) {
			gapi.plusone.go();
			gapi.plus.go();
			gapi.follow.go();
			gapi.hangout.go();
			gapi.person.go();
			gapi.page.go();
			gapi.community.go();
		}";
		return $content;
	}
}

if ( ! function_exists( 'gglplsn_js' ) ) {
	function gglplsn_js() {
		global $gglplsn_is_button_shown;
		if ( ! empty( $gglplsn_is_button_shown ) || defined( 'BWS_ENQUEUE_ALL_SCRIPTS' ) ) {
			global $gglplsn_options, $gglplsn_lang_codes;
			
			if (
				1 == $gglplsn_options['plus_one_js'] ||
				1 == $gglplsn_options['share_js'] ||
				1 == $gglplsn_options['follow_js'] ||
				1 == $gglplsn_options['hangout_js'] ||
				1 == $gglplsn_options['badge_js']
			) {
				if ( 1 == $gglplsn_options['use_multilanguage_locale'] && isset( $_SESSION['language'] ) ) {
					if ( array_key_exists( $_SESSION['language'], $gglplsn_lang_codes ) ) {
						$gglplsn_locale = $_SESSION['language'];
					} else {
						$gglplsn_locale_from_multilanguage = str_replace( '_', '-', $_SESSION['language'] );
						if ( array_key_exists( $gglplsn_locale_from_multilanguage, $gglplsn_lang_codes ) ) {
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
				if ( in_array( 'before', $gglplsn_options['position'] ) )
					$content = $button . $content;
				if ( in_array( 'after', $gglplsn_options['position'] ) )
					$content .= $button;
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
				<span class="bws_info gglplsn-badge-id-info"><?php echo __( 'Enter your Google+ ID.', 'google-one' ) . '&nbsp;' . __( 'For example', 'google-one' ) . ',&nbsp;"12345678912345678912"&nbsp;'; ?>)</span>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'badge_type' ); ?>"><?php _e( 'Type', 'google-one' ); ?></label>
				<select id="<?php echo $this->get_field_id( 'badge_type' ); ?>" class="gglplsn-badge-type" name="<?php echo $this->get_field_name( 'badge_type' ); ?>">
					<option value="person" <?php selected( 'person', $badge_type ); ?>><?php _e( 'Person', 'google-one' ); ?></option>
					<option value="page" <?php selected('page', $badge_type ); ?>><?php _e( 'Page', 'google-one' ); ?></option>
					<option value="community" <?php selected( 'community', $badge_type ); ?>><?php _e( 'Community', 'google-one' ); ?></option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'badge_layout' ); ?>"><?php _e( 'Layout', 'google-one' ); ?></label>
				<select id="<?php echo $this->get_field_id( 'badge_layout' ); ?>" name="<?php echo $this->get_field_name( 'badge_layout' ); ?>">
					<option value="portrait" <?php selected( 'portrait', $badge_layout ); ?>><?php _e( 'Portrait', 'google-one' ); ?></option>
					<option value="landscape" <?php selected( 'landscape', $badge_layout ); ?>><?php _e( 'Landscape', 'google-one' ); ?></option>
				</select>
			</p>
			<p>
				<input class="widefat" id="<?php echo $this->get_field_id( 'badge_show_cover' ); ?>" name="<?php echo $this->get_field_name( 'badge_show_cover' ); ?>" type="checkbox" <?php checked( true, $badge_show_cover ); ?> value="1" />
				<label for="<?php echo $this->get_field_id( 'badge_show_cover' ); ?>"><?php _e( 'Show Cover Photo', 'google-one' ); ?></label>
			</p>
			<p>
				<input class="widefat" id="<?php echo $this->get_field_id( 'badge_show_tagline' ); ?>" name="<?php echo $this->get_field_name( 'badge_show_tagline' ); ?>" type="checkbox" <?php checked( true, $badge_show_tagline ); ?> value="1" />
				<label for="<?php echo $this->get_field_id( 'badge_show_tagline' ); ?>"><?php _e( 'Show Tag Line', 'google-one' ); ?></label>
			</p>

			<p <?php echo ( 'community' != $badge_type ) ? 'class="gglplsn-show-owners hidden"' : 'class="gglplsn-show-owners"'; ?>>
				<input class="widefat" id="<?php echo $this->get_field_id( 'badge_show_owners' ); ?>" name="<?php echo $this->get_field_name( 'badge_show_owners' ); ?>" type="checkbox" <?php checked( true, $badge_show_owners ); echo ( 'community' != $badge_type ) ? ' disabled="disabled"': ''; ?> value="1" />
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
			if ( ! empty( $follow_id ) ) {
				$href = 'https://plus.google.com/' . $follow_id;
				$follow = '<span class="gglplsn_follow"><g:follow
					href="' . esc_url( $href ) . '"
					height="' . intval( $follow_size ) . '"
					annotation="' . $follow_annotation .'"
					rel="' . $follow_relationship . '"></g:follow></span>';
				return $follow;
			} else {
				return '';
			}
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
			if ( ! empty( $badge_id ) ) {
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
			} else {
				return '';
			}
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
		global $wp_version, $gglplsn_options, $sclbttns_plugin_info;
		if ( empty( $gglplsn_options ) )
			$gglplsn_options = get_option( 'gglplsn_options' ); 

		$settings_page = $sclbttns_plugin_info ? 'admin.php?page=social-buttons.php' : 'admin.php?page=google-plus-one.php'; ?>
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
						<?php _e( 'Follow', 'google-one' ); ?>
						<?php if ( empty( $gglplsn_options['follow_id'] ) ) { ?>
							<span class="bws_info">
								(<?php _e( 'To see this button, please', 'google-one' ); ?>
								<a style="color: #0073aa;" href="<?php echo $settings_page; ?>"><?php _e( 'enter', 'google-one' ) ?></a>
								<?php _e( 'the Google+ ID', 'google-one' ); ?>)
							</span>
						<?php } ?>
					</label>
					<br />
					<label>
						<input type="checkbox" name="gglplsn_selected_hangout" value="hangout" />
						<?php _e( 'Hangout', 'google-one' ) ?>
					</label>
					<br />
					<label>
						<input type="checkbox" name="gglplsn_selected_badge" value="badge" />
						<?php _e( 'Badge', 'google-one' ); ?>
						<?php if ( empty( $gglplsn_options['badge_id'] ) ) { ?>
						<span class="bws_info">
							(<?php _e( 'To see this button, please', 'google-one' ); ?>
							<a style="color: #0073aa;" href="<?php echo $settings_page; ?>"><?php _e( 'enter', 'google-one' ) ?></a>
							<?php _e( 'the Google+ ID', 'google-one' ); ?>)
						</span>
					<?php } ?>
					</label>
					<input class="bws_default_shortcode" type="hidden" name="default" value="[bws_googleplusone]" />
				<div class="clear"></div>
			</fieldset>
		</div>
		<script type="text/javascript">
			function gglplsn_shortcode_init() {
				( function( $ ) {
					$( '.mce-reset input[name^="gglplsn_selected"]' ).change( function() {
						var result = '';
						$( '.mce-reset input[name^="gglplsn_selected"]' ).each( function() {
							if ( $( this ).is( ':checked' ) ) {
								result += $( this ).val() + ',';
							}
						} );
						if ( '' == result ) {
							$( '.mce-reset #bws_shortcode_display' ).text( '' );
						} else {
							result = result.slice( 0, - 1 );
							$( '.mce-reset #bws_shortcode_display' ).text( '[bws_googleplusone display="' + result + '"]' );
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

add_filter( 'pgntn_callback', 'gglplsn_pagination_callback' );

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