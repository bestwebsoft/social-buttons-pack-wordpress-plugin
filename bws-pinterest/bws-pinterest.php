<?php



if ( ! function_exists( 'pntrst_loaded' ) ) {
	/**
	 * Internationalization
	 */	
	function pntrst_loaded() {
		load_plugin_textdomain( 'bws-pinterest', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

if ( ! function_exists ( 'pntrst_init' ) ) {
	/**
	 * Function initialisation plugin for init
	 */
	function pntrst_init() {
		global $bws_plugin_info, $pntrst_plugin_info, $pntrst_lang_codes;

		if ( empty( $pntrst_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$pntrst_plugin_info = get_plugin_data( __FILE__ );
		}

		

		/* Call register settings function pntrst_register_settings() */
		if ( ! is_admin() || ( isset( $_GET['page'] ) && ( 'pinterest.php' === $_GET['page'] || 'social-buttons.php' === $_GET['page'] ) ) ) {
			pntrst_register_settings();
			$pntrst_lang_codes = array(
				'en'    => 'English',
				'cs'    => 'Czech',
				'da'    => 'Danish',
				'de'    => 'German',
				'el'    => 'Greek',
				'fr'    => 'French',
				'hi'    => 'Hindu',
				'hu'    => 'Hungarian',
				'id'    => 'Indonesian',
				'it'    => 'Italian',
				'ja'    => 'Japanese',
				'ko'    => 'Korean',
				'ms'    => 'Malaysian',
				'nb'    => 'Norwegian',
				'nl'    => 'Dutch',
				'pl'    => 'Polish',
				'pt'    => 'Portuguese',
				'pt-br' => 'Portuguese (Brazil)',
				'ro'    => 'Romanian',
				'ru'    => 'Russian',
				'sk'    => 'Slovak',
				'sv'    => 'Swedish',
				'tl'    => 'Tagalog',
				'th'    => 'Thai',
				'tr'    => 'Turkish',
				'uk'    => 'Ukrainian',
				'vi'    => 'Vietnamese',
			);
		}
	}
}

if ( ! function_exists( 'pntrst_admin_init' ) ) {
	/**
	 * Function initialisation plugin for admin_init
	 */
	function pntrst_admin_init() {
		/* Add variable for bws_menu */
		global $pagenow, $bws_plugin_info, $pntrst_plugin_info, $bws_shortcode_list, $pntrst_options;

		/* pls*/

		/* add Pinterest to global $bws_shortcode_list */
		$bws_shortcode_list['pntrst'] = array(
			'name'        => 'Pinterest',
			'js_function' => 'pntrst_shortcode_init',
		);
	}
}

if ( ! function_exists( 'pntrst_register_settings' ) ) {
	/**
	 * Register settings function
	 */
	function pntrst_register_settings() {
		global $pntrst_options, $pntrst_plugin_info;

		/* Install the option defaults */
		if ( ! get_option( 'pntrst_options' ) ) {
			
			$options_defaults = pntrst_get_options_default();
			add_option( 'pntrst_options', $options_defaults );
		}

		/* Get options from the database */
		$pntrst_options = get_option( 'pntrst_options' );

		/* Array merge incase this version has added new options */
		if ( ! isset( $pntrst_options['plugin_option_version'] ) || $pntrst_options['plugin_option_version'] !== $pntrst_plugin_info['Version'] ) {
			/* show pro features */
			$pntrst_options['hide_premium_options'] = array();

			$options_defaults = pntrst_get_options_default();
			/**
			 * @deprecated since 1.0.9
			 * @todo remove after 31.12.2018
			 */
			if ( empty( $pntrst_options['pinit_before'] ) && empty( $pntrst_options['pinit_after'] ) && empty( $pntrst_options['pinit_hover'] ) ) {
				$pntrst_options['pinit_save'] = 0;
			}
			if ( empty( $pntrst_options['follow_before'] ) && empty( $pntrst_options['follow_after'] ) ) {
				$pntrst_options['pinit_follow'] = 0;
			}
			/* deprecated (end) */
			$pntrst_options = array_merge( $options_defaults, $pntrst_options );
			$pntrst_options['plugin_option_version'] = $pntrst_plugin_info['Version'];
			update_option( 'pntrst_options', $pntrst_options );
		}
	}
}



if ( ! function_exists( 'pntrst_get_options_default' ) ) {
	/**
	 * Fetch plugin default options
	 *
	 * @return array
	 */
	function pntrst_get_options_default() {
		global $pntrst_plugin_info;

		$options_defaults = array(
			'plugin_option_version'    => $pntrst_plugin_info['Version'],
			'pinit_save'				=> 1,
			'pinit_follow'				=> 1,
			'pinit_before'				=> 1,
			'pinit_after'				=> 0,
			'pinit_hover'				=> 0,
			'pinit_image'				=> 1,
			'pinit_custom_image_link'	=> '',
			'pinit_image_shape'			=> 1,
			'pinit_image_size'			=> 1,
			'pinit_counts'				=> 'none',
			'follow_before'				=> 1,
			'follow_after'				=> 0,
			'follow_button_label'		=> __( 'Follow me', 'bws-pinterest' ),
			'profile_url'				=> '',
			'lang' 						=> 'en',
			'display_settings_notice'	=> 0,
			'suggest_feature_banner'	=> 1,
			'use_multilanguage_locale'	=> 0,
		);

		return $options_defaults;
	}
}


if ( ! function_exists ( 'pntrst_enqueue' ) ) {
	/**
	 * Function for enqueue plugin scripts and styles for admin
	 *
	 * @param string $hook File name.
	 */
	function pntrst_enqueue( $hook ) {
		global $pntrst_plugin_info;
		wp_enqueue_style( 'pntrst_icon', plugins_url( 'css/icon.css', __FILE__ ), false, $pntrst_plugin_info['Version'] );
		if ( isset( $_GET['page'] ) && ( 'pinterest.php' === $_GET['page'] || 'social-buttons.php' === $_GET['page'] ) ) {
			wp_enqueue_style( 'pntrst_stylesheet', plugins_url( 'css/style.css', __FILE__ ), false, $pntrst_plugin_info['Version'] );
			wp_enqueue_script( 'pntrst_script', plugins_url( 'js/script.js', __FILE__ ), array(), $pntrst_plugin_info['Version'], true );

			bws_plugins_include_codemirror();
			bws_enqueue_settings_scripts();
		}

		if ( 'widgets.php' === $hook || 'customize.php' === $hook ) {
			wp_enqueue_script( 'pntrst_script', plugins_url( 'js/script.js', __FILE__ ), array(), $pntrst_plugin_info['Version'], true );
			wp_enqueue_style( 'pntrst_stylesheet', plugins_url( 'css/style.css', __FILE__ ), false, $pntrst_plugin_info['Version'] );
		}
	}
}

if ( ! function_exists( 'pntrst_script_enqueue' ) ) {
	/**
	 * Function for enqueue plugin scripts and styles
	 */
	function pntrst_script_enqueue() {
		global $pntrst_options, $pntrst_plugin_info;

		wp_enqueue_style( 'pntrst_stylesheet', plugins_url( 'css/style.css', __FILE__ ), array(), $pntrst_plugin_info['Version'] );
		wp_enqueue_script( 'pinit.js', '//assets.pinterest.com/js/pinit.js', array(), null, true );

		if ( empty( $pntrst_options['pinit_image'] ) && ! empty( $pntrst_options['pinit_hover'] ) ) {
			wp_enqueue_script( 'bws-custom-hover-js', plugins_url( 'js/custom_hover.js', __FILE__ ), array( 'jquery' ), $pntrst_plugin_info['Version'], true );
		} elseif ( ! empty( $pntrst_options['pinit_hover'] ) ) {
			wp_enqueue_script( 'pntrst_pinit_hover', plugins_url( 'js/pinit_hover.js', __FILE__ ), array( 'jquery' ), $pntrst_plugin_info['Version'], true );
		}
	}
}

if ( ! function_exists( 'pntrst_add_data_to_script' ) ) {
	/**
	 * Adds async/defer and data attributes to enqueued / registered scripts.
	 *
	 * @param string $tag    The script tag.
	 * @param string $handle The script handle.
	 * @return string Script HTML string.
	 */
	function pntrst_add_data_to_script( $tag, $handle ) {
		global $pntrst_options, $pntrst_lang_codes, $mltlngg_current_language;
		
		if ( 'pinit.js' === $handle ) {
			$return_string = 'async';

			/* check if custom image is chosen and load pntrst_custom_hover_img_script */
			if ( ! empty( $pntrst_options['pinit_image'] ) ) {				

				/* if image hover is enabled, append the data-pin-hover attribute */
				if ( ! empty( $pntrst_options['pinit_hover'] ) ) {
					$return_string .= ' data-pin-hover="true"';
				}
				/* button shape */
				if ( empty( $pntrst_options['pinit_image_shape'] ) ) {
					$return_string .= ' data-pin-round="true" data-pin-save="false"';
				}
				/* button size */
				if ( empty( $pntrst_options['pinit_image_size'] ) ) {
					$return_string .= ' data-pin-tall="true"';
				}
				/* if image shape square */
				if ( ! empty( $pntrst_options['pinit_image_shape'] ) ) {

					/* Add multilanguage options */
					if ( ! empty( $pntrst_options['use_multilanguage_locale'] ) && isset( $mltlngg_current_language ) ) {
						if ( array_key_exists( $mltlngg_current_language, $pntrst_lang_codes ) ) {
							$pntrst_locale = $mltlngg_current_language;
						} else {
							$pntrst_locale_from_multilanguage = str_replace( '_', '-', $mltlngg_current_language );
							if ( array_key_exists( $pntrst_locale_from_multilanguage, $pntrst_lang_codes ) ) {
								$pntrst_locale = $pntrst_locale_from_multilanguage;
							} else {
								$pntrst_locale_from_multilanguage = explode( '_', $mltlngg_current_language );
								if ( is_array( $pntrst_locale_from_multilanguage ) && array_key_exists( $pntrst_locale_from_multilanguage[0], $pntrst_lang_codes ) ) {
									$pntrst_locale = $pntrst_locale_from_multilanguage[0];
								}
							}
						}
					}

					if ( empty( $pntrst_locale ) ) {
						$pntrst_locale = $pntrst_options['lang'];
					}

					$return_string .= ' data-pin-save="true" data-pin-lang="' . $pntrst_locale . '"';

					if ( isset( $pntrst_options['pinit_counts'] ) ) {
						$return_string .= ' data-pin-count="' . $pntrst_options['pinit_counts'] . '"';
					}
				}				
			}
			$tag = preg_replace( ':(?=></script>):', " $return_string", $tag, 1 );
		} else if ( 'bws-custom-hover-js' === $handle ) {
			$return_string = 'id="bws-custom-hover-js" data-custom-button-image="' . $pntrst_options['pinit_custom_image_link'] . '" async';
			$tag = preg_replace( ':(?=></script>):', " $return_string", $tag, 1 );
		}
		return $tag;
	}
}

if ( ! function_exists( 'pntrst_frontend' ) ) {
	/**
	 * Function for display plugin frontend
	 *
	 * @param string $content Content.
	 */
	function pntrst_frontend( $content ) {
		global $pntrst_options;
		/* Check if custom image. */
		$before = '';
		$after  = '';
		if ( ! is_feed() ) {
			if ( empty( $pntrst_options['pinit_image'] ) ) {
				$custom = 'true';
				$img_link = $pntrst_options['pinit_custom_image_link'];
			} else {
				$custom = '';
				$img_link = plugins_url( 'images/pin.png', __FILE__ );
			}
			if ( empty( $pntrst_options['pinit_image_size'] ) ) {
				$pinterest_folow_large = ' data-pin-tall="true" ';
				$style_large_text = 'pntrst_style_add_large_folow_button';
			} else {
				$pinterest_folow_large = '';
				$style_large_text      = '';
			}
			$pinit_code = '<div class="pntrst-button-wrap">
							<a data-pin-do="buttonBookmark" data-pin-custom="' . $custom . '" href="https://www.pinterest.com/pin/create/button/"><img data-pin-nopin="1" class="pntrst-custom-pin" src="' . esc_url( $img_link ) . '" width="60"></a>
						</div>';
			$follow_code = '<div class="pntrst-button-wrap ' . $style_large_text . '" >
							<a ' . $pinterest_folow_large . ' data-pin-do="buttonFollow" href="https://www.pinterest.com/' . esc_attr( $pntrst_options['profile_url'] ) . '/">' . esc_html( $pntrst_options['follow_button_label'] ) . '</a>
						</div>';
			/* Check which buttons display before content */
			if ( ! empty( $pntrst_options['pinit_before'] ) ) {
				$before .= $pinit_code;
			}
			if ( ! empty( $pntrst_options['follow_before'] ) ) {
				$before .= $follow_code;
			}

			/* Check which buttons display after content */
			if ( ! empty( $pntrst_options['pinit_after'] ) ) {
				$after .= $pinit_code;
			}
			if ( ! empty( $pntrst_options['follow_after'] ) ) {
				$after .= $follow_code;
			}

			$before = apply_filters( 'pntrst_button_in_the_content', '<div class="pntrst_main_before_after">' . $before . '</div>' );
			$after = apply_filters( 'pntrst_button_in_the_content', '<div class="pntrst_main_before_after">' . $after . '</div>' );
		}
		return $before . $content . $after;
	}
}

if ( ! function_exists( 'pntrst_pagination_callback' ) ) {
	/**
	 * Function pagination callback
	 *
	 * @param string $content Callback content.
	 * @return string Content for callback.
	 */
	function pntrst_pagination_callback( $content ) {
		$content .= "if ( typeof( PinUtils ) != 'undefined' ) { PinUtils.build(); }";
		return $content;
	}
}

if ( ! function_exists( 'pntrst_pin_it_shortcode' ) ) {
/**
	 * Function which create shortcode for Pin It button
 *
	 * @param array $atts Array with attr.
	 * @return string Content for shortcode.
 */
	function pntrst_pin_it_shortcode( $atts ) {
		global $pntrst_options;
		$pin_it_atts = shortcode_atts(
			array(
			'type' 		=> 'any',
			'image_url' => '',
			'custom' 	=> '',
			'url' 		=> $pntrst_options['pinit_custom_image_link'],
			),
			$atts
		);

		if ( 'any' === $pin_it_atts['type'] ) {
			return '<div class="pntrst-button-wrap">
						<a data-pin-do="buttonBookmark" data-pin-custom="' . esc_html( $pin_it_atts['custom'] ) . '" href="https://www.pinterest.com/pin/create/button/"><img data-pin-nopin="1" class="pntrst-custom-pin" src="' . esc_url( $pin_it_atts['url'] ) . '" width="60"></a>
					</div>';
		} elseif ( 'one' === $pin_it_atts['type'] ) {
			return '<div class="pntrst-button-wrap">
						<a  data-pin-do="buttonPin" data-pin-media="' . esc_url( $pin_it_atts['image_url'] ) . '" data-pin-custom="' . esc_html( $pin_it_atts['custom'] ) . '" href="https://www.pinterest.com/pin/create/button/"><img data-pin-nopin="1" class="pntrst-custom-pin" src="' . esc_url( $pin_it_atts['url'] ) . '" width="60"></a>
					</div>';
		}
	}
}

 if ( ! function_exists( 'pntrst_pin_follow_shortcode' ) ) {
	/**
	 * Function which create shortcode for Pinterest Follow button
	 *
	 * @param array $atts Array with attr.
	 * @return string Content for shortcode.
	 */
	function pntrst_pin_follow_shortcode( $atts ) {
		global $pntrst_options;
		$pin_follow_atts = shortcode_atts(
			array(
			'label' => $pntrst_options['follow_button_label'],
			),
			$atts
		);

		return '<div class="pntrst-button-wrap">
					<a data-pin-do="buttonFollow" href="https://www.pinterest.com/' . esc_attr( $pntrst_options['profile_url'] ) . '/">' . esc_html( $pin_follow_atts['label'] ) . '</a>
				</div>';
	}
}

if ( ! function_exists( 'pntrst_widget_register' ) ) {
	/**
	 * Register Widget
	 */
	function pntrst_widget_register() {
		if ( ! class_exists( 'Pinterest_Widget' ) ) {
			require_once dirname( __FILE__ ) . '/includes/class-pinterest-widget.php';
		}
		register_widget( 'Pinterest_Widget' );
	}
}

 if ( ! function_exists( 'pntrst_widget_shortcode' ) ) {
	/**
	 * Function which create shortcode for Pinterest widgets
	 *
	 * @param array $atts Array with attr.
	 * @return string Content for shortcode.
	 */
	function pntrst_widget_shortcode( $atts ) {
		global $pntrst_options;
		$pin_widget_atts = shortcode_atts(
			array(
			'type' =>		'',
			'url' =>		'',
			'size' =>		'small',
			'width' =>		'',
			'height' =>		'175',
			'thumbnail' =>	'92',
			),
			$atts
		);
		if ( 'pin' === $pin_widget_atts['type'] ) {
			return '<div class="pntrst-widget-wrap"><a data-pin-do="embedPin" data-pin-width="' . esc_attr( $pin_widget_atts['size'] ) . '" href="' . esc_url( $pin_widget_atts['url'] ) . '"></a></div>';
		} elseif ( 'board' === $pin_widget_atts['type'] ) {
			return '<div class="pntrst-widget-wrap"><a data-pin-do="embedBoard" data-pin-board-width="' . esc_attr( $pin_widget_atts['width'] ) . '" data-pin-scale-height="' . esc_attr( $pin_widget_atts['height'] ) . '" data-pin-scale-width="' . esc_attr( $pin_widget_atts['thumbnail'] ) . '" href="' . esc_url( $pin_widget_atts['url'] ) . '"></a></div>';
		} elseif ( 'profile' === $pin_widget_atts['type'] ) {
			return '<div class="pntrst-widget-wrap"><a data-pin-do="embedUser" data-pin-board-width="' . esc_attr( $pin_widget_atts['width'] ) . '" data-pin-scale-height="' . esc_attr( $pin_widget_atts['height'] ) . '" data-pin-scale-width="' . esc_attr( $pin_widget_atts['thumbnail'] ) . '" href="https://www.pinterest.com/' . esc_attr( $pntrst_options['profile_url'] ) . '/"></a></div>';
		}
	}
}

if ( ! function_exists( 'pntrst_shortcode_content' ) ) {
	/**
	 * Function which add Pinterest shortcodes in post visual editor
	 *
	 * @param string $content Sortcode content.
	 */
	function pntrst_shortcode_content( $content ) {
		global $pntrst_options, $wp_version;
		if ( empty( $pntrst_options ) ) {
			pntrst_register_settings();
		}
		?>
		<div id="pntrst" style="display:none;">
			<div>
				<?php esc_html_e( 'Shortcode Type', 'bws-pinterest' ); ?>:
				<select name="pntrst_shortcode_type">
					<option selected="selected" value="follow"><?php esc_html_e( 'Follow Button', 'bws-pinterest' ); ?></option>
					<option value="pin_it"><?php esc_html_e( 'Save Button', 'bws-pinterest' ); ?></option>
					<option value="pin_widget"><?php esc_html_e( 'Pin Widget', 'bws-pinterest' ); ?></option>
					<option value="board_widget"><?php esc_html_e( 'Board Widget', 'bws-pinterest' ); ?></option>
					<option value="profile_widget"><?php esc_html_e( 'Profile Widget', 'bws-pinterest' ); ?></option>
				</select>
			</div>
			<div class="pntrst-follow-shortcode">
				<p><?php esc_html_e( 'Button label', 'bws-pinterest' ); ?>:</p>
				<input name="pntrst_follow_label" type="text" maxlength="50" value="<?php echo esc_html( $pntrst_options['follow_button_label'] ); ?>" style="padding: 2px; margin: 2px 0; width: 100%;" />
			</div>
			<fieldset class="pntrst-pin-it-shortcode" style="display:none;">
				<label><input checked="checked" name="pntrst_pit_it_type" type="radio" value="any"><?php esc_html_e( 'Save any image', 'bws-pinterest' ); ?></label><br/>
				<label><input name="pntrst_pit_it_type" type="radio" value="one"><?php esc_html_e( 'Save defined image', 'bws-pinterest' ); ?></label><br/>
				<input name="pntrst_pin_image_url" type="url" value="" placeholder="<?php esc_html_e( 'Pin image URL', 'bws-pinterest' ); ?>" style="display:none; padding: 2px; margin: 2px 0; width: 100%;" />
				<label><input name="pntrst_custom_button" type="checkbox" value="0"><?php esc_html_e( 'Custom button image', 'bws-pinterest' ); ?></label><br/>
				<input name="pntrst_custom_button_image" type="url" value="" placeholder="<?php esc_html_e( 'Custom image URL', 'bws-pinterest' ); ?>" style="display:none; padding: 2px; margin: 2px 0; width: 100%;" />
			</fieldset>
			<fieldset class="pntrst-widget-shortcode" style="display:none;">
				<div id="pntrst-widget-url">
					<label><?php esc_html_e( 'URL', 'bws-pinterest' ); ?>* : </label>
					<input name="pntrst_widget_url" type="url" value="" style="padding: 2px; margin: 2px 0; width: 100%;" />
				</div>
				<div id="pntrst_pin_widget_size">
					<label>
						<?php esc_html_e( 'Size', 'bws-pinterest' ); ?>:
						<select name="pntrst_pin_widget_size">
							<option selected="selected" value="small"><?php esc_html_e( 'Small', 'bws-pinterest' ); ?></option>
							<option value="medium"><?php esc_html_e( 'Medium', 'bws-pinterest' ); ?></option>
							<option value="large"><?php esc_html_e( 'Large', 'bws-pinterest' ); ?></option>
						</select>
					</label>
				</div>
				<div id="pntrst_widget_size" style="display:none;">
					<label><?php esc_html_e( 'Width', 'bws-pinterest' ); ?>: <input style="height: auto;" name="pntrst_widget_width" type="number" min="130" max="2000" value="" class="small-text" /> (px)</label><br/>
					<label><?php esc_html_e( 'Height', 'bws-pinterest' ); ?>: <input style="height: auto;" name="pntrst_widget_height" type="number" min="60" max="1500" value="" class="small-text" /> (px)</label><br/>
					<label><?php esc_html_e( 'Thumbnails width', 'bws-pinterest' ); ?>: <input style="height: auto;" name="pntrst_widget_thumbnail" type="number" min="60" max="2000" value="" class="small-text" /> (px)</label>
				</div>
			</fieldset>
			<input class="bws_default_shortcode" type="hidden" name="default" value="[bws_pinterest_follow label=&quot;<?php echo esc_html( $pntrst_options['follow_button_label'] ); ?>&quot;]" />			
			<div class="clear"></div>
		</div>
		<?php
		$script = "function pntrst_shortcode_init() {
				(function( $ ) {
					$( '.mce-reset #pntrst input, .mce-reset #pntrst select' ).change( function() {

						var shortcodeType = $( '.mce-reset select[name=\"pntrst_shortcode_type\"] option:selected' ).val();

						if ( 'follow' == shortcodeType ) {
							$( '.mce-reset .pntrst-follow-shortcode' ).show();
							$( '.mce-reset .pntrst-pin-it-shortcode, .mce-reset .pntrst-widget-shortcode' ).hide();
							/* Display follow button shortcode */
							var shortcode = '[bws_pinterest_follow label=\"' + $( '.mce-reset input[name=\"pntrst_follow_label\"]' ).val() + '\"]';
						} else if ( 'pin_it' == shortcodeType ) {
							$( '.mce-reset .pntrst-pin-it-shortcode' ).show();
							$( '.mce-reset .pntrst-follow-shortcode, .mce-reset .pntrst-widget-shortcode' ).hide();
							/* Display pin it button shortcode */
							var buttonType = $( '.mce-reset input[name=\"pntrst_pit_it_type\"]:checked' ).val();

							if ( $( '.mce-reset input[name=\"pntrst_custom_button\"]' ).is( ':checked' ) ) {
								$( '.mce-reset input[name=\"pntrst_custom_button_image\"]' ).show();
								var customButtonImage = $( '.mce-reset input[name=\"pntrst_custom_button_image\"]' ).val();
								if ( false === /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test( customButtonImage ) ) {
									customButtonImage = '';
								}
							} else {
								$( '.mce-reset input[name=\"pntrst_custom_button_image\"]' ).hide();
							}

							if ( 'any' == buttonType ) {
								$( '.mce-reset input[name=\"pntrst_pin_image_url\"]' ).hide();
								if ( $( '.mce-reset input[name=\"pntrst_custom_button\"]' ).is( ':checked' ) ) {
									if ( customButtonImage.length > 0 ) {
										var shortcode = '[bws_pinterest_pin_it type=\"' + buttonType + '\" custom=\"true\" url=\"' + customButtonImage + '\"]';
									} else {
										var shortcode = '[bws_pinterest_pin_it type=\"' + buttonType + '\" custom=\"true\"]';
									}
								} else {
									var shortcode = '[bws_pinterest_pin_it type=\"' + buttonType + '\"]';
								}
							} else if ( 'one' == buttonType ) {
								$( '.mce-reset input[name=\"pntrst_pin_image_url\"]' ).show();
								var pinImageUrl = $( '.mce-reset input[name=\"pntrst_pin_image_url\"]' ).val();
								if ( false === /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test( pinImageUrl ) ) {
									pinImageUrl = '';
								}
								if ( $( '.mce-reset input[name=\"pntrst_custom_button\"]' ).is( ':checked' ) ) {
									if ( customButtonImage.length > 0 ) {
										var shortcode = '[bws_pinterest_pin_it type=\"' + buttonType + '\" image_url=\"' + pinImageUrl + '\" custom=\"true\" url=\"' + customButtonImage + '\"]';
									} else {
										var shortcode = '[bws_pinterest_pin_it type=\"' + buttonType + '\" image_url=\"' + pinImageUrl + '\" custom=\"true\"]';
									}
								} else {
									var shortcode = '[bws_pinterest_pin_it type=\"' + buttonType + '\" image_url=\"' + pinImageUrl + '\"]';
								}
							}
						} else if ( 'pin_widget' == shortcodeType || 'board_widget' == shortcodeType || 'profile_widget' == shortcodeType ) {
							$( '.mce-reset .pntrst-widget-shortcode' ).show();
							$( '.mce-reset .pntrst-follow-shortcode, .mce-reset .pntrst-pin-it-shortcode' ).hide();

							var widgetWidth = $( '.mce-reset input[name=\"pntrst_widget_width\"]' ).val();
							var widgetHeight = $( '.mce-reset input[name=\"pntrst_widget_height\"]' ).val();
							var widgetThumbnail = $( '.mce-reset input[name=\"pntrst_widget_thumbnail\"]' ).val();

							if ( false == widgetWidth || parseInt( widgetWidth ) < 130 ) {
								widgetWidth = 130;
							} else if ( parseInt( widgetWidth ) > 2000 ) {
								widgetWidth = 2000;
							} else {
								widgetWidth = parseInt( widgetWidth );
							}
							if ( false == widgetHeight || parseInt( widgetHeight ) < 60 ) {
								widgetHeight = 60;
							} else if ( parseInt( widgetHeight ) > 1500 ) {
								widgetHeight = 1500;
							} else {
								widgetHeight = parseInt( widgetHeight );
							}
							if ( false == widgetThumbnail || parseInt( widgetThumbnail ) < 60 ) {
								widgetThumbnail = 60;
							} else if ( parseInt( widgetThumbnail ) > 2000 ) {
								widgetThumbnail = 2000;
							} else {
								widgetThumbnail = parseInt( widgetThumbnail );
							}

							var widgetUrl = $( '.mce-reset input[name=\"pntrst_widget_url\"]' ).val();

							if ( false === /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test( widgetUrl ) ) {
								widgetUrl = '';
							}

							if ( 'pin_widget' == shortcodeType ) {
								$( '.mce-reset #pntrst_pin_widget_size, .mce-reset #pntrst-widget-url' ).show();
								$( '.mce-reset #pntrst_widget_size' ).hide();

								var pinWidgetSize = $( '.mce-reset select[name=\"pntrst_pin_widget_size\"] option:selected' ).val();
								var shortcode = '[bws_pinterest_widget type=\"pin\" size=\"' + pinWidgetSize + '\" url=\"' + widgetUrl + '\"]';
							} else if ( 'board_widget' == shortcodeType ) {
								$( '.mce-reset #pntrst_pin_widget_size' ).hide();
								$( '.mce-reset #pntrst_widget_size, .mce-reset #pntrst-widget-url' ).show();

								if ( '' != widgetWidth || 60 != widgetHeight || 60 != widgetThumbnail ) {
									var shortcode = '[bws_pinterest_widget type=\"board\" width=\"' + widgetWidth + '\" height=\"' + widgetHeight + '\" thumbnail=\"' + widgetThumbnail + '\" url=\"' + widgetUrl + '\"]';
								} else {
									var shortcode = '[bws_pinterest_widget type=\"board\" url=\"' + widgetUrl + '\"]';
								}
							} else {
								$( '.mce-reset #pntrst_pin_widget_size, .mce-reset #pntrst-widget-url' ).hide();
								$( '.mce-reset #pntrst_widget_size' ).show();

								if ( '' != widgetWidth || 60 != widgetHeight || 60 != widgetThumbnail ) {
									var shortcode = '[bws_pinterest_widget type=\"profile\" width=\"' + widgetWidth + '\" height=\"' + widgetHeight + '\" thumbnail=\"' + widgetThumbnail + '\"]';
								} else {
									var shortcode = '[bws_pinterest_widget type=\"profile\"]';
								}
							}
						}
						/* Shortcode output */
						$( '.mce-reset #bws_shortcode_display' ).text( shortcode );
					});
				})( jQuery );
			}";
		wp_register_script( 'pntrst_bws_shortcode_button', '' );
		wp_enqueue_script( 'pntrst_bws_shortcode_button' );
		wp_add_inline_script( 'pntrst_bws_shortcode_button', sprintf( $script ) );
	}
}


add_action( 'plugins_loaded', 'pntrst_loaded' );
/* plugin init */
add_action( 'init', 'pntrst_init' );
add_action( 'admin_init', 'pntrst_admin_init' );
/* add Pinterest widget */
add_action( 'widgets_init', 'pntrst_widget_register' );
/* Enqueue plugin scripts and styles for admin */
add_action( 'admin_enqueue_scripts', 'pntrst_enqueue' );
/* Enqueue plugin scripts and styles */
add_action( 'wp_enqueue_scripts', 'pntrst_script_enqueue' );
add_filter( 'script_loader_tag', 'pntrst_add_data_to_script', 10, 2 );
/* show buttons on frontend */
add_filter( 'the_content', 'pntrst_frontend' );
add_filter( 'pgntn_callback', 'pntrst_pagination_callback' );

/* add shortcode for Pin It button */
add_shortcode( 'bws_pinterest_pin_it', 'pntrst_pin_it_shortcode' );
/* add shortcode for Pin Follow button */
add_shortcode( 'bws_pinterest_follow', 'pntrst_pin_follow_shortcode' );
/* add shortcode for Pinterest widgets button */
add_shortcode( 'bws_pinterest_widget', 'pntrst_widget_shortcode' );
/* custom filter for bws button in tinyMCE */
add_filter( 'bws_shortcode_button_content', 'pntrst_shortcode_content' );
