<?php
/**
 * Init Instagram page
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( ! function_exists( 'sclbttns_nstgrm_admin_init' ) ) {
	/**
	 * Function to init for Instagram tab
	 */
	function sclbttns_nstgrm_admin_init() {
		global $bws_shortcode_list;

		/* Add Instagram to global $bws_shortcode_list */
		$bws_shortcode_list['nstgrm'] = array( 'name' => 'Instagram' );
	}
}

if ( ! function_exists( 'sclbttns_nstgrm_button' ) ) {
	/**
	 * Function to display for Instagram button
	 */
	function sclbttns_nstgrm_button() {
		global $post, $sclbttns_options;

		if ( isset( $post ) ) {
			if ( isset( $post->ID ) ) {
				$permalink_post = get_permalink( $post->ID );
			}

			$if_large = '';
			if ( 'large' === $sclbttns_options['instagram_options']['size'] ) {
				$if_large = 'nstgrm_large_button';
			}

			if ( 'left' === $sclbttns_options['instagram_options']['location'] ) {
				$button = '<div class="nstgrm_buttons_block" id="nstgrm_left">';
			} elseif ( 'middle' === $sclbttns_options['instagram_options']['location'] ) {
				$button = '<div class="nstgrm_buttons_block" id="nstgrm_middle">';
			} else {
				$button = '<div class="nstgrm_buttons_block" id="nstgrm_right">';
			}

			if ( ! empty( $sclbttns_options['instagram_options']['profile'] ) ) {
				if ( 'standard' === $sclbttns_options['instagram_options']['display_option'] || empty( $sclbttns_options['instagram_options']['img_link'] ) ) {
					$img_name   = 'large' === $sclbttns_options['instagram_options']['size'] ? 'large-instagram-ico' : 'standard-instagram-ico';
					$nstgrm_img = plugins_url( 'images/' . $img_name . '.jpg', dirname( __FILE__ ) );
				} else {
					$nstgrm_img = wp_get_attachment_url( $sclbttns_options['instagram_options']['img_link'] );
				}

				$button .= '<div class="nstgrm_button" data-id-post="' . $post->ID . '">
					<a href="https://www.instagram.com/' . esc_html( $sclbttns_options['instagram_options']['account_name'] ) . '" target="_blank">
						<img src="' . esc_url( $nstgrm_img ) . '" alt="instagram-Button" />
					</a>
				</div>';
			}

			$button .= '</div>';

			return $button;
		}
	}
}


if ( ! function_exists( 'sclbttns_nstgrm_display_button' ) ) {
	/**
	 * Function 'instagram_button' taking from array 'sclbttns_options'
	 * necessary information to create BestWebSoft Instagram and reacting to your choise in plugin menu - points where it appears
	 *
	 * @param string $content Content for button.
	 */
	function sclbttns_nstgrm_display_button( $content ) {
		global $post, $sclbttns_options;

		if ( ! isset( $sclbttns_options ) ) {
			sclbttns_settings();
		}

		if ( isset( $post ) ) {
			if ( is_feed() ) {
				return $content;
			}
			if ( is_array( $sclbttns_options['instagram_options']['where'] ) ) {
				$button = sclbttns_nstgrm_button();
				if ( ! empty( $sclbttns_options['instagram_options']['where'] ) && in_array( 'before', $sclbttns_options['instagram_options']['where'] ) ) {
					$content = $button . $content;
				}
				if ( ! empty( $sclbttns_options['instagram_options']['where'] ) && in_array( 'after', $sclbttns_options['instagram_options']['where'] ) ) {
					$content .= $button;
				}
			}
		}
		return $content;
	}
}

if ( ! function_exists( 'sclbttns_nstgrm_shortcode' ) ) {
	/**
	 * Function 'nstgrm_shortcode' are using to create shortcode by BestWebSoft Instagram
	 */
	function sclbttns_nstgrm_shortcode() {
		global $sclbttns_options;

		if ( ! isset( $sclbttns_options ) ) {
			sclbttns_settings();
		}

		$button = sclbttns_nstgrm_button();

		return $button;
	}
}

if ( ! function_exists( 'sclbttns_nstgrm_shortcode_button_content' ) ) {
	/**
	 * Function to add shortcode content
	 *
	 * @param array  $attr    Shortcode attr.
	 * @param string $content Shortcode content.
	 */
	function sclbttns_nstgrm_shortcode_button_content( $attr = array(), $content = '' ) { ?>
		<div id="nstgrm" style="display:none;">
			<fieldset>
				<?php esc_html_e( 'Add Instagram Profile button to your page or post', 'social-buttons-pack' ); ?>
			</fieldset>
			<input class="bws_default_shortcode" type="hidden" name="default" value="[instagram_button]" />
			<div class="clear"></div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'sclbttns_nstgrm_function_display_arhive' ) ) {
	/**
	 * Function to display Instagram button for arhive
	 */
	function sclbttns_nstgrm_function_display_arhive() {
		global $post, $sclbttns_options, $wp;

		if ( ! isset( $sclbttns_options ) ) {
			sclbttns_settings();
		}

		if ( is_archive() && ! empty( $post ) ) {
			$if_large = '';
			if ( 'large' === $sclbttns_options['instagram_options']['size'] ) {
				$if_large = 'nstgrm_large_button';
			}
			if ( 'left' === $sclbttns_options['instagram_options']['location'] ) {
				$button = '<div class="nstgrm_buttons_block nstgrm_arhiv" id="nstgrm_left">';
			} elseif ( 'middle' === $sclbttns_options['instagram_options']['location'] ) {
				$button = '<div class="nstgrm_buttons_block nstgrm_arhiv" id="nstgrm_middle">';
			} else {
				$button = '<div class="nstgrm_buttons_block nstgrm_arhiv" id="nstgrm_right">';
			}
			if ( ! empty( $sclbttns_options['instagram_options']['profile'] ) ) {
				if ( 'standard' === $sclbttns_options['instagram_options']['display_option'] || empty( $sclbttns_options['instagram_options']['img_link'] ) ) {
					$img_name   = 'large' === $sclbttns_options['instagram_options']['size'] ? 'large-instagram-ico' : 'standard-instagram-ico';
					$nstgrm_img = plugins_url( 'images/' . $img_name . '.jpg', dirname( __FILE__ ) );
				} else {
					$nstgrm_img = wp_get_attachment_url( $sclbttns_options['instagram_options']['img_link'] );
				}

				$button .= '<div class="nstgrm_button">
					<a href="https://www.instagram.com/' . esc_html( $sclbttns_options['instagram_options']['account_name'] ) . '" target="_blank">
						<img src="' . esc_url( $nstgrm_img ) . '" alt="Fb-Button" />
					</a>
				</div>';
			}

			$button .= '</div>';
			echo wp_kses_post( $button );
		}
	}
}

add_action( 'admin_init', 'sclbttns_nstgrm_admin_init' );
/* Add button */
add_filter( 'the_content', 'sclbttns_nstgrm_display_button' );
/* Add shortcode. */
add_shortcode( 'instagram_button', 'sclbttns_nstgrm_shortcode' );
/* Custom filter for bws button in tinyMCE */
add_filter( 'bws_shortcode_button_content', 'sclbttns_nstgrm_shortcode_button_content' );

add_action( 'loop_start', 'sclbttns_nstgrm_function_display_arhive' );
