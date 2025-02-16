<?php
if ( ! function_exists( 'twttr_plugins_loaded' ) ) {
	/**
	 * Internationalization
	 */
	function twttr_plugins_loaded() {
		load_plugin_textdomain( 'twitter-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

if ( ! function_exists( 'twttr_init' ) ) {
	/**
	 * Init
	 */
	function twttr_init() {
		global $twttr_plugin_info;

		if ( empty( $twttr_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$twttr_plugin_info = get_plugin_data( __FILE__ );
		}

				/* Get/Register and check settings for plugin */
		if ( ! is_admin() || ( isset( $_GET['page'] ) && ( 'twitter.php' == $_GET['page'] || 'social-buttons.php' == $_GET['page'] ) ) ) {
		    twttr_settings();
		}
	}
}

if ( ! function_exists( 'twttr_admin_init' ) ) {
	/**
	 * Admin init
	 */
	function twttr_admin_init() {
		/* Add variable for bws_menu */
		global $bws_plugin_info, $twttr_plugin_info, $bws_shortcode_list, $pagenow, $twttr_options;

		/* pls*/

		/* add Twitter to global $bws_shortcode_list */
		$bws_shortcode_list['twttr'] = array(
			'name'        => 'Twitter',
			'js_function' => 'twttr_shortcode_init',
		);
	}
}

if ( ! function_exists( 'twttr_settings' ) ) {
	/**
	 * This is settings functions
	 */
	function twttr_settings() {
		global $twttr_options, $twttr_plugin_info;
		/* Get options from the database */
		if ( ! get_option( 'twttr_options' ) ) {
			$options_default = twttr_get_options_default();
			add_option( 'twttr_options', $options_default );
		}

		$twttr_options = get_option( 'twttr_options' );

		if ( ! isset( $twttr_options['plugin_option_version'] ) || $twttr_options['plugin_option_version'] != $twttr_plugin_info['Version'] ) {
			$options_default = twttr_get_options_default();
			$twttr_options = array_merge( $options_default, $twttr_options );
			$twttr_options['plugin_option_version'] = $twttr_plugin_info["Version"];
			update_option( 'twttr_options', $twttr_options );
		}
	}
}

if ( ! function_exists( 'twttr_get_options_default' ) ) {
	/**
	 * Get default options for plugin
	 */
	function twttr_get_options_default() {
		global $twttr_plugin_info;

		$options_default = array(
			'plugin_option_version'     => $twttr_plugin_info['Version'],
			'display_settings_notice'	=> 1,
			'suggest_feature_banner'	=> 1,
			'url_twitter' 				=> 'twitter',
			'display_option'			=> 'standart',
			'count_icon' 				=> 1,
			'img_link' 					=> plugins_url( 'images/twitter-follow.png', __FILE__ ),
			'position' 					=> array( 'before' ),
			'tweet_display'             => 0,
			'size'						=> 'default',
			'lang_default'				=> 1,
			'lang'						=> 'en',
			'tailoring'					=> 0,
			'url_of_twitter'			=> 'page_url',
			'text_option_twitter'		=> 'page_title',
			'text_twitter'				=> '',
			'via_twitter'				=> '',
			'related_twitter'			=> '',
			'hashtag_twitter'			=> '',
			'followme_display'			=> 0,
			'username_display'			=> 0,
			'followers_count_followme'	=> 0,
			'hashtag_display'			=> 0,
			'hashtag'					=> '',
			'text_option_hashtag'		=> 'page_title',
			'text_hashtag'				=> '',
			'url_option_hashtag'		=> 'page_url',
			'url_hashtag'				=> '',
			'related_hashtag'			=> '',
			'mention_display'			=> 0,
			'tweet_to_mention'			=> '',
			'text_option_mention'		=> 'page_title',
			'text_mention'				=> '',
			'related_mention'           => '',
		);
		return $options_default;
	}
}

/* Function to creates shortcode [twitter_buttons] */
if ( ! function_exists( 'twttr_twitter_buttons' ) ) {
	/**
	 * Function to creates shortcode [twitter_buttons]
	 *
	 * @param array $atts Attributes.
	 */
	function twttr_twitter_buttons( $atts = array( 'display' => 'follow' ) ) {
		$atts = shortcode_atts( array( 'display' => 'follow' ), $atts, 'twitter_buttons' );
		$tweet = ( stripos( $atts['display'], 'tweet' ) === false ) ? 0 : 1;
		$follow = ( stripos( $atts['display'], 'follow' ) === false ) ? 0 : 1;
		$hashtag = ( stripos( $atts['display'], 'hashtag' ) === false ) ? 0 : 1;
		$mention = ( stripos( $atts['display'], 'mention' ) === false ) ? 0 : 1;
		if ( 1 == $tweet || 1 == $follow || 1 == $hashtag || 1 == $mention ) {
			return twttr_show_button( $tweet, $follow, $hashtag, $mention );
		}
	}
}

if ( ! function_exists( 'twttr_twit' ) ) {
	/**
	 * Positioning in the page
	 *
	 * @param string $content Content.
	 */
	function twttr_twit( $content ) {
		global $post, $twttr_options, $wp_current_filter;

		if ( ! empty( $wp_current_filter ) && in_array( 'get_the_excerpt', $wp_current_filter ) ) {
			return $content;
		}
		if ( ! empty( $twttr_options['position'] ) &&
			( 1 == $twttr_options['tweet_display'] || 1 == $twttr_options['followme_display'] || 1 == $twttr_options['hashtag_display'] || 1 == $twttr_options['mention_display'] ) ) {

			$buttons = twttr_show_button( $twttr_options['tweet_display'], $twttr_options['followme_display'], $twttr_options['hashtag_display'], $twttr_options['mention_display'] );
			$buttons = apply_filters( 'twttr_button_in_the_content', $buttons );

			if ( in_array( 'before', $twttr_options['position'] ) ) {
				$content = $buttons . $content;
			}
			if ( in_array( 'after', $twttr_options['position'] ) ) {
				$content = $content . $buttons;
			}
		}
		return $content;
	}
}


if ( ! function_exists( 'twttr_show_button' ) ) {
	/**
	 * Function for showing buttons
	 *
	 * @param int $tweet   Flag for display.
	 * @param int $follow  Flag for display.
	 * @param int $hashtag Flag for display.
	 * @param int $mention Flag for display.
	 */
	function twttr_show_button( $tweet, $follow, $hashtag, $mention ) {
		global $post, $twttr_options, $twttr_add_api_script;

		if ( is_feed() ) {

			return;
		}
		if ( 1 == $tweet || 1 == $follow || 1 == $hashtag || 1 == $mention ) {

			$twttr_add_api_script = true;

			$lang = ( 1 == $twttr_options['lang_default'] ) ? '' : 'data-lang="'. $twttr_options['lang'] . '"';
			$tailoring = ! empty( $twttr_options['tailoring'] ) ? 'data-dnt="true"' : '';

			if ( 1 == $tweet ) {
				/*option for tweet button*/
				$permalink_post	= ( 'page_url' == $twttr_options['url_of_twitter'] ) ? get_permalink( $post->ID ) : get_home_url();
				$title_post = ( 'page_title' == $twttr_options['text_option_twitter'] ) ? htmlspecialchars( urlencode( $post->post_title ) ) : $twttr_options['text_twitter'];
				/*show tweet button*/
				$tweet = '<div class="twttr_twitter">
					<a href="http://twitter.com/share?text=' . $title_post . '" class="twitter-share-button" data-via="'. $twttr_options['via_twitter'] . '" data-hashtags="' . $twttr_options['hashtag_twitter'] . '" ' . $lang . ' data-size="' . $twttr_options['size'] . '" data-url="' . $permalink_post . '" ' . $tailoring . ' data-related="' . $twttr_options['related_twitter'] . '" target="_blank">' . __( 'Tweet', 'twitter-plugin' ) . '</a>
				</div>';
			} else {
				$tweet = '';
			}
			if ( 1 == $follow ) {
				/*option for follow me button*/
				if ( '' === $twttr_options['url_twitter'] ) {
					$twttr_options['url_twitter'] = 'twitter';
				}

				/*show follow me button*/
				$upload_dir = wp_upload_dir();
				if ( 'standart' == $twttr_options[ 'display_option' ] || ! is_writable( $upload_dir['basedir'] ) ) {
					$show_name = ( $twttr_options['username_display'] ) ? 'data-show-screen-name="true"' : 'data-show-screen-name="false"';

					$follow = '<div class="twttr_followme">
						<a href="https://twitter.com/' . $twttr_options['url_twitter'] . '" class="twitter-follow-button" data-size="' . $twttr_options['size'] . '" ' . $lang . ' ' . $show_name . ' ' . $tailoring . ' target="_blank">' . __( 'Follow me', 'twitter-plugin' ) . '</a>
					</div>';
				} else {
					$follow = '<div class="twttr_followme">
						<a href="http://twitter.com/' . $twttr_options['url_twitter'] . '" target="_blank" title="Follow me"><img src="' . $twttr_options['img_link'] . '" alt="' . __( 'Follow me', 'twitter-plugin' ) . '" /></a>
					</div>';
				}
			} else {
				$follow = '';
			}
			if ( 1 == $hashtag ) {
				/*option for hashtag button*/
				if ( '' == $twttr_options['hashtag'] ) {
					$main_hashtag             = __( 'TwitterStories', 'twitter-plugin' );
					$twttr_options['hashtag'] = $main_hashtag;
				} else {
					$hashtags = explode( '%2C', urlencode( $twttr_options['hashtag'] ), 2 );
					$main_hashtag = $hashtags[0];
				}

				$secondary_hastags = ( ! empty( $hashtags[1] ) ) ? '&hashtags=' . $hashtags[1] : '';

				$text_hashtag = ( 'page_title' == $twttr_options['text_option_hashtag'] ) ? htmlspecialchars( urlencode( $post->post_title ) ) : urlencode( $twttr_options['text_hashtag'] );

				if ( 'no_url' == $twttr_options['url_option_hashtag'] ) {
					$url_hashtag = '';
				} elseif ( 'home_url' == $twttr_options['url_option_hashtag'] ) {
					$url_hashtag = get_home_url();
				} else {
					$url_hashtag = get_permalink( $post->ID );
				}
				/*show hashtag button*/
				if ( '' === $text_hashtag ) {
					$hashtag = '<div class="twttr_hashtag">
						<a href="https://twitter.com/intent/tweet?button_hashtag=' . $main_hashtag . $secondary_hastags . '" class="twitter-hashtag-button" data-size="' . $twttr_options['size'] . '" ' . $lang . ' data-related="' . $twttr_options['related_hashtag'] . '" data-url="' . $url_hashtag . '" ' . $tailoring . ' target="_blank">' . __( 'Tweet', 'twitter-plugin' ) . ' #' . $twttr_options['hashtag'] . '</a>
					</div>';
				} else {
					$hashtag = '<div class="twttr_hashtag">
						<a href="https://twitter.com/intent/tweet?button_hashtag=' . $main_hashtag . $secondary_hastags . '&text=' . $text_hashtag . '" class="twitter-hashtag-button" data-size="' . $twttr_options['size'] . '" ' . $lang . ' data-related="' . $twttr_options['related_hashtag'] . '" data-url="' . $url_hashtag . '" ' . $tailoring . ' target="_blank">' . __( 'Tweet', 'twitter-plugin' ) . ' #' . $twttr_options['hashtag'] . '</a>
					</div>';
				}
			} else {
				$hashtag = '';
			}
			if ( 1 == $mention ) {
				/*option for mention button*/
				if ( empty( $twttr_options['tweet_to_mention'] ) ) {
					$twttr_options['tweet_to_mention'] = 'support';
				}
				if ( 'page_title' == $twttr_options['text_option_mention'] ) {
					$text_mention = '';
				}
				$text_mention = ( 'page_title' == $twttr_options['text_option_mention'] ) ? htmlspecialchars( urlencode( $post->post_title ) ) : urlencode( $twttr_options['text_mention'] );
				/*show mention button*/
				$mention = '<div class="twttr_mention">
					<a href="https://twitter.com/intent/tweet?screen_name=' . $twttr_options['tweet_to_mention'] . '&text=' . $text_mention . '" class="twitter-mention-button" data-size="' . $twttr_options['size'] . '" ' . $lang . ' data-related="' . $twttr_options['related_mention'] . '" ' . $tailoring . ' target="_blank">' . __( 'Tweet to', 'twitter-plugin' ) . ' @'. $twttr_options['tweet_to_mention'] .'</a>
				</div>';
			} else {
				$mention = '';
			}
			return '<div class="twttr_buttons">' . $tweet . $follow . $hashtag . $mention . '</div>';
		}
	}
}

if ( ! function_exists( 'twttr_wp_head' ) ) {
	/**
	 * Connecting CSS styles
	 */
	function twttr_wp_head() {
		wp_enqueue_style( 'twttr_stylesheet', plugins_url( 'css/style.css', __FILE__ ), array(), '2.66' );
	}
}

if ( ! function_exists( 'twttr_admin_enqueue_scripts' ) ) {
	/**
	 * Connecting JS and CSS styles to dashboard
	 */
	function twttr_admin_enqueue_scripts() {
		global $twttr_plugin_info;
		wp_enqueue_style( 'twttr_icon', plugins_url( 'css/icon.css', __FILE__ ), array(), $twttr_plugin_info['Version'] );
		if ( isset( $_GET['page'] ) && ( 'twitter.php' == $_GET['page'] || 'social-buttons.php' == $_GET['page'] ) ) {
			wp_enqueue_style( 'twttr_stylesheet', plugins_url( 'css/style.css', __FILE__ ), array(), $twttr_plugin_info['Version'] );
			wp_enqueue_script( 'twttr_script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery' ), $twttr_plugin_info['Version'], true );

			bws_enqueue_settings_scripts();
			bws_plugins_include_codemirror();
		}
	}
}

if ( ! function_exists( 'twttr_api_scripts' ) ) {
	/**
	 * Connecting JS
	 */
	function twttr_api_scripts() {
		global $twttr_add_api_script, $twttr_plugin_info;
		if ( true == $twttr_add_api_script || defined( 'BWS_ENQUEUE_ALL_SCRIPTS' ) ) { 
			$script_api = '!function(d,s,id) {
					var js,fjs=d.getElementsByTagName(s)[0];
					if (!d.getElementById(id)) {
						js=d.createElement(s);
						js.id=id;
						js.src="https://platform.twitter.com/widgets.js";
						fjs.parentNode.insertBefore(js,fjs);
					}
				}(document,"script","twitter-wjs")';
			wp_register_script( 'twttr_api_scripts_init', '', array(), $twttr_plugin_info['Version'], true );
			wp_enqueue_script( 'twttr_api_scripts_init' );
			wp_add_inline_script( 'twttr_api_scripts_init', sprintf( $script_api ) );
			$twttr_add_api_script = false;
		}
	}
}

if ( ! function_exists( 'twttr_pagination_callback' ) ) {
	/**
	 * Pagination_callback function
	 *
	 * @param string $content Content.
	 */
	function twttr_pagination_callback( $content ) {
		$content .= "if (typeof twttr !== 'undefined') { twttr.widgets.load(); }";
		return $content;
	}
}

if ( ! function_exists( 'twttr_shortcode_button_content' ) ) {
	/**
	 * Shortcode content
	 *
	 * @param string $content Content.
	 */
	function twttr_shortcode_button_content( $content ) {
		global $post, $twttr_plugin_info;
		?>
		<div id="twttr" style="display:none;">
			<fieldset>
				<?php esc_html_e( 'Please select twitter buttons which will be displayed', 'twitter-plugin' ); ?><br />
				<label><input type="checkbox" value="1" id="twttr_tweet" name="twttr_tweet"><?php esc_html_e( 'Tweet', 'twitter-plugin' ); ?></label><br />
				<label><input type="checkbox" value="1" id="twttr_followme" name="twttr_followme" checked="checked"><?php esc_html_e( 'Follow', 'twitter-plugin' ); ?></label><br />
				<label><input type="checkbox" value="1" id="twttr_hashtag" name="twttr_hashtag"><?php esc_html_e( 'Hashtag', 'twitter-plugin' ); ?></label><br />
				<label><input type="checkbox" value="1" id="twttr_mention" name="twttr_mention"><?php esc_html_e( 'Mention', 'twitter-plugin' ); ?></label><br />
			</fieldset>
			<input class="bws_default_shortcode" type="hidden" name="default" value="[twitter_buttons]" />
			<?php
			$script = "function twttr_shortcode_init() {
					( function( $ ) {
						var current_object = '.mce-reset';
						$( current_object + ' #twttr_tweet,' + current_object + ' #twttr_followme,' + current_object + ' #twttr_hashtag,' + current_object + ' #twttr_mention' ).on( 'change', function() {
							var tweet = ( $( current_object + ' #twttr_tweet' ).is( ':checked' ) ) ? 'tweet,' : '';
							var follow_me = ( $( current_object + ' #twttr_followme' ).is( ':checked' ) ) ? 'follow,' : '';
							var hashtag = ( $( current_object + ' #twttr_hashtag' ).is( ':checked' ) ) ? 'hashtag,' : '';
							var mention = ( $( current_object + ' #twttr_mention' ).is( ':checked' ) ) ? 'mention' : '';
							if ( tweet != '' || follow_me != '' || hashtag != '' || mention != '' ) {
								var shortcode = '[twitter_buttons display=' + tweet + follow_me + hashtag + mention + ']';
							} else {
								var shortcode = '';
							}
							$( current_object + ' #bws_shortcode_display' ).text( shortcode );
						} );
					} ) ( jQuery );
				}";
			wp_register_script( 'twttr_bws_shortcode_button', '', array(), $twttr_plugin_info['Version'], true );
			wp_enqueue_script( 'twttr_bws_shortcode_button' );
			wp_add_inline_script( 'twttr_bws_shortcode_button', sprintf( $script ) );
			?>
			<div class="clear"></div>
		</div>
		<?php
	}
}

add_action( 'plugins_loaded', 'twttr_plugins_loaded' );
add_action( 'init', 'twttr_init' );
/*admin_init */
add_action( 'admin_init', 'twttr_admin_init' );
/* Adding stylesheets */
add_action( 'wp_enqueue_scripts', 'twttr_wp_head' );
add_action( 'wp_footer', 'twttr_api_scripts' );
add_filter( 'pgntn_callback', 'twttr_pagination_callback' );
add_action( 'admin_enqueue_scripts', 'twttr_admin_enqueue_scripts' );
/* Adding plugin buttons */
add_shortcode( 'follow_me', 'twttr_twitter_buttons' );
add_shortcode( 'twitter_buttons', 'twttr_twitter_buttons' );
add_filter( 'widget_text', 'do_shortcode' );
add_filter( 'the_content', "twttr_twit" );
/* custom filter for bws button in tinyMCE */
add_filter( 'bws_shortcode_button_content', 'twttr_shortcode_button_content' );
