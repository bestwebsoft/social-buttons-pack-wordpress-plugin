<?php
/**
 * Init Messengers page
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( ! function_exists( 'sclbttns_messengers_admin_init' ) ) {
	/**
	 * Function to init for Messengers tab
	 */
	function sclbttns_messengers_admin_init() {
		global $bws_shortcode_list;

		/* add Telegram to global $bws_shortcode_list */
		$bws_shortcode_list['telegram'] = array( 'name' => 'Telegram' );
		$bws_shortcode_list['whatsapp'] = array( 'name' => 'WhatsApp' );
		$bws_shortcode_list['youtube']  = array( 'name' => 'YouTube' );
	}
}

if ( ! function_exists( 'sclbttns_messengers_display_button' ) ) {
	/**
	 * Function 'telegram_button' taking from array 'sclbttns_options'
	 * necessary information to create BestWebSoft Telegram and reacting to your choise in plugin menu - points where it appears
	 *
	 * @param string $content Content for button.
	 */
	function sclbttns_messengers_display_button( $content ) {
		global $post, $sclbttns_options;

		if ( ! isset( $sclbttns_options ) ) {
			sclbttns_settings();
		}

		if ( isset( $post ) ) {
			if ( is_feed() ) {
				return $content;
			}

			if ( ( sclbttns_messenger_is_post_not_excluded( $post, 'telegram' ) || sclbttns_messenger_is_post_not_excluded( $post, 'whatsapp' ) || sclbttns_messenger_is_post_not_excluded( $post, 'youtube' ) ) && is_array( $sclbttns_options['messengers_options']['where'] ) ) {
				$button = sclbttns_messengers_button();

				if ( ! empty( $sclbttns_options['messengers_options']['where'] ) && in_array( 'before', $sclbttns_options['messengers_options']['where'] ) ) {
					$content = $button . $content;
				}
				if ( ! empty( $sclbttns_options['messengers_options']['where'] ) && in_array( 'after', $sclbttns_options['messengers_options']['where'] ) ) {
					$content .= $button;
				}
			}
		}

		return $content;
	}
}

if ( ! function_exists( 'sclbttns_messengers_button' ) ) {
	/**
	 * Function to display for Messengers button
	 */
	function sclbttns_messengers_button() {
		global $wpdb, $post, $sclbttns_options;

		if ( isset( $post ) ) {
			if ( isset( $post->ID ) ) {
				$permalink_post = get_permalink( $post->ID );
			}

			$button = '<div class="messengers_buttons_block messengers_' . esc_attr( $sclbttns_options['messengers_options']['location'] ) . '">';

			if ( ! empty( $sclbttns_options['messengers_options']['telegram_account'] ) ) {
				/* Check if Telegram is excluded */
				$is_telegram_excluded = $wpdb->get_var(
					$wpdb->prepare(
						'SELECT `id` FROM `' . $wpdb->prefix . 'bws_fancytree`
						WHERE `post_id` IS NULL AND
							`category_id` IS NULL AND
							`post_type` = %s AND
							`exeption` = %s',
						$post->post_type,
						'telegram'
					)
				);

				if ( ! $is_telegram_excluded ) {
					$image_name     = 'large' === $sclbttns_options['messengers_options']['size'] ? 'large-telegram-ico' : 'standard-telegram-ico';
					$telegram_image = plugins_url( 'images/' . $image_name . '.jpg', dirname( __FILE__ ) );

					$button .= '<div class="telegram_button" data-id-post="' . esc_attr( $post->ID ) . '">
							<a href="https://t.me/' . esc_attr( $sclbttns_options['messengers_options']['telegram_account'] ) . '" target="_blank">
								<img src="' . esc_url( $telegram_image ) . '" alt="Telegram ' . esc_html__( 'Button', 'social-buttons-pack' ) . '" />
							</a>
						</div>';
				}
			}

			if ( ! empty( $sclbttns_options['messengers_options']['whatsapp_account'] ) ) {
				/* Check if WhatsApp is excluded */
				$is_whatsapp_excluded = $wpdb->get_var(
					$wpdb->prepare(
						'SELECT `id` FROM `' . $wpdb->prefix . 'bws_fancytree`
						WHERE `post_id` IS NULL AND
							`category_id` IS NULL AND
							`post_type` = %s AND
							`exeption` = %s',
						$post->post_type,
						'whatsapp'
					)
				);

				if ( ! $is_whatsapp_excluded ) {
					if ( 'standard' === $sclbttns_options['messengers_options']['display_option'] || empty( $sclbttns_options['messengers_options']['wtsp_image_link'] ) ) {
						$image_name     = 'large' === $sclbttns_options['messengers_options']['size'] ? 'large-whatsapp-ico' : 'standard-whatsapp-ico';
						$whatsapp_image = plugins_url( 'images/' . $image_name . '.jpg', dirname( __FILE__ ) );
					}

					$whatsapp_link = '';
					if ( false === strpos( $sclbttns_options['messengers_options']['whatsapp_account'], 'https://api.whatsapp.com/' ) ) {
						/*It's a phone number */
						$whatsapp_link = 'https://api.whatsapp.com/send/?phone=' . $sclbttns_options['messengers_options']['whatsapp_account'];
					} else {
						/* It's a chat link */
						$whatsapp_link = $sclbttns_options['messengers_options']['whatsapp_account'];
					}

					$button .= '<div class="whatsapp_button" data-id-post="' . esc_attr( $post->ID ) . '">
						<a href="' . esc_url( $whatsapp_link ) . '" target="_blank">
							<img src="' . esc_url( $whatsapp_image ) . '" alt="Whatsapp ' . esc_html__( 'Button', 'social-buttons-pack' ) . '" />
						</a>
					</div>';
				}
			}

			if ( ! empty( $sclbttns_options['messengers_options']['youtube_account'] ) ) {
				/* Check if YouTube is excluded */
				$is_youtube_excluded = $wpdb->get_var(
					$wpdb->prepare(
						'SELECT `id` FROM `' . $wpdb->prefix . 'bws_fancytree`
						WHERE `post_id` IS NULL AND
							`category_id` IS NULL AND
							`post_type` = %s AND
							`exeption` = %s',
						$post->post_type,
						'youtube'
					)
				);
				if ( ! $is_youtube_excluded ) {
					if ( 'standard' === $sclbttns_options['messengers_options']['display_option'] || empty( $sclbttns_options['messengers_options']['ytb_image_link'] ) ) {
						$image_name    = 'large' === $sclbttns_options['messengers_options']['size'] ? 'large-youtube-ico' : 'standard-youtube-ico';
						$youtube_image = plugins_url( 'images/' . $image_name . '.jpg', dirname( __FILE__ ) );
					}

					$button .= '<div class="youtube_button" data-id-post="' . esc_attr( $post->ID ) . '">
						<a href="https://www.youtube.com/' . esc_attr( $sclbttns_options['messengers_options']['youtube_account'] ) . '" target="_blank">
							<img src="' . esc_url( $youtube_image ) . '" alt="Youtube ' . esc_html__( 'Button', 'social-buttons-pack' ) . '" />
						</a>
					</div>';
				}
			}

			$button .= '</div>';

			return $button;
		}

		return '';
	}
}

if ( ! function_exists( 'sclbttns_telegram_button' ) ) {
	/**
	 * Function to display for Telegram button
	 */
	function sclbttns_telegram_button() {
		global $wpdb, $post, $sclbttns_options;

		if ( isset( $post ) ) {
			if ( isset( $post->ID ) ) {
				$permalink_post = get_permalink( $post->ID );
			}

			$if_large = '';
			if ( 'large' === $sclbttns_options['messengers_options']['size'] ) {
				$if_large = 'telegram_large_button';
			}

			if ( 'left' === $sclbttns_options['messengers_options']['location'] ) {
				$button = '<div class="telegram_buttons_block" id="telegram_left">';
			} elseif ( 'middle' === $sclbttns_options['messengers_options']['location'] ) {
				$button = '<div class="telegram_buttons_block" id="telegram_middle">';
			} else {
				$button = '<div class="telegram_buttons_block" id="telegram_right">';
			}

			$account_name = isset( $sclbttns_options['messengers_options']['account_name'] ) ? $sclbttns_options['messengers_options']['account_name'] : '';

			if ( ! empty( $account_name ) ) {
				/* Check if Telegram is excluded */
				$is_telegram_excluded = $wpdb->get_var(
					$wpdb->prepare(
						'SELECT `id` FROM `' . $wpdb->prefix . 'bws_fancytree`
						WHERE `post_id` IS NULL AND
							`category_id` IS NULL AND
							`post_type` = %s AND
							`exeption` = %s',
						$post->post_type,
						'telegram'
					)
				);

				if ( ! $is_telegram_excluded ) {
					if ( 'standard' === $sclbttns_options['messengers_options']['display_option'] || empty( $sclbttns_options['messengers_options']['image_link'] ) ) {
						$image_name     = 'large' === $sclbttns_options['messengers_options']['size'] ? 'large-telegram-ico' : 'standard-telegram-ico';
						$telegram_image = plugins_url( 'images/' . $image_name . '.jpg', dirname( __FILE__ ) );
					}

					$button .= '<div class="telegram_button" data-id-post="' . esc_attr( $post->ID ) . '">
							<a href="https://t.me/' . esc_attr( $account_name ) . '" target="_blank">
								<img src="' . esc_url( $telegram_image ) . '" alt="Telegram ' . esc_html__( 'Button', 'social-buttons-pack' ) . '" />
							</a>
						</div>';
				}
			}

			$button .= '</div>';

			return $button;
		}

		return '';
	}
}

if ( ! function_exists( 'sclbttns_whatsapp_button' ) ) {
	/**
	 * Function to display for Whatsapp button
	 */
	function sclbttns_whatsapp_button() {
		global $wpdb, $post, $sclbttns_options;

		if ( isset( $post ) ) {
			if ( isset( $post->ID ) ) {
				$permalink_post = get_permalink( $post->ID );
			}

			$if_large = '';
			if ( 'large' === $sclbttns_options['messengers_options']['size'] ) {
				$if_large = 'whatsapp_large_button';
			}

			if ( 'left' === $sclbttns_options['messengers_options']['location'] ) {
				$button = '<div class="whatsapp_buttons_block" id="whatsapp_left">';
			} elseif ( 'middle' === $sclbttns_options['messengers_options']['location'] ) {
				$button = '<div class="whatsapp_buttons_block" id="whatsapp_middle">';
			} else {
				$button = '<div class="whatsapp_buttons_block" id="whatsapp_right">';
			}

			$number_account = isset( $sclbttns_options['messengers_options']['number_account'] ) ? $sclbttns_options['messengers_options']['number_account'] : '';

			if ( ! empty( $number_account ) ) {
				/* Check if WhatsApp is excluded */
				$is_whatsapp_excluded = $wpdb->get_var(
					$wpdb->prepare(
						'SELECT `id` FROM `' . $wpdb->prefix . 'bws_fancytree`
						WHERE `post_id` IS NULL AND
							`category_id` IS NULL AND
							`post_type` = %s AND
							`exeption` = %s',
						$post->post_type,
						'whatsapp'
					)
				);

				if ( ! $is_whatsapp_excluded ) {
					if ( 'standard' === $sclbttns_options['messengers_options']['display_option'] || empty( $sclbttns_options['messengers_options']['wtsp_image_link'] ) ) {
						$image_name     = 'large' === $sclbttns_options['messengers_options']['size'] ? 'large-whatsapp-ico' : 'standard-whatsapp-ico';
						$whatsapp_image = plugins_url( 'images/' . $image_name . '.jpg', dirname( __FILE__ ) );
					}

					$whatsapp_link = '';
					if ( false !== strpos( $number_account, 'https://api.whatsapp.com/' ) ) {
						/*It's a phone number */
						$whatsapp_link = 'https://api.whatsapp.com/send/?phone=' . $number_account;
					} else {
						/* It's a chat link */
						$whatsapp_link = $number_account;
					}

					$button .= '<div class="whatsapp_button" data-id-post="' . esc_attr( $post->ID ) . '">
						<a href="' . esc_url( $whatsapp_link ) . '" target="_blank">
							<img src="' . esc_url( $whatsapp_image ) . '" alt="Whatsapp ' . esc_html__( 'Button', 'social-buttons-pack' ) . '" />
						</a>
					</div>';
				}
			}

			$button .= '</div>';

			return $button;
		}

		return '';
	}
}

if ( ! function_exists( 'sclbttns_youtube_button' ) ) {
	/**
	 * Function to display for Youtube button
	 */
	function sclbttns_youtube_button() {
		global $wpdb, $post, $sclbttns_options;

		if ( isset( $post ) ) {
			if ( isset( $post->ID ) ) {
				$permalink_post = get_permalink( $post->ID );
			}

			$if_large = '';
			if ( 'large' === $sclbttns_options['messengers_options']['size'] ) {
				$if_large = 'youtube_large_button';
			}

			if ( 'left' === $sclbttns_options['messengers_options']['location'] ) {
				$button = '<div class="youtube_buttons_block" id="youtube_left">';
			} elseif ( 'middle' === $sclbttns_options['messengers_options']['location'] ) {
				$button = '<div class="youtube_buttons_block" id="youtube_middle">';
			} else {
				$button = '<div class="youtube_buttons_block" id="youtube_right">';
			}

			$youtube_account = isset( $sclbttns_options['messengers_options']['youtube_account'] ) ? $sclbttns_options['messengers_options']['youtube_account'] : '';

			if ( ! empty( $youtube_account ) ) {
				/* Check if YouTube is excluded */
				$is_youtube_excluded = $wpdb->get_var(
					$wpdb->prepare(
						'SELECT `id` FROM `' . $wpdb->prefix . 'bws_fancytree`
						WHERE `post_id` IS NULL AND
							`category_id` IS NULL AND
							`post_type` = %s AND
							`exeption` = %s',
						$post->post_type,
						'youtube'
					)
				);
				if ( ! $is_youtube_excluded ) {
					if ( 'standard' === $sclbttns_options['messengers_options']['display_option'] || empty( $sclbttns_options['messengers_options']['ytb_image_link'] ) ) {
						$image_name    = 'large' === $sclbttns_options['messengers_options']['size'] ? 'large-youtube-ico' : 'standard-youtube-ico';
						$youtube_image = plugins_url( 'images/' . $image_name . '.jpg', dirname( __FILE__ ) );
					}

					$button .= '<div class="youtube_button" data-id-post="' . esc_attr( $post->ID ) . '">
						<a href="https://www.youtube.com/' . esc_attr( $youtube_account ) . '" target="_blank">
							<img src="' . esc_url( $youtube_image ) . '" alt="Youtube ' . esc_html__( 'Button', 'social-buttons-pack' ) . '" />
						</a>
					</div>';
				}
			}

			$button .= '</div>';

			return $button;
		}

		return '';
	}
}


if ( ! function_exists( 'sclbttns_messenger_is_post_not_excluded' ) ) {
	/**
	 * Function to Check if post or page is excluded
	 *
	 * @param object $post   Post object.
	 * @param string $button Button type.
	 */
	function sclbttns_messenger_is_post_not_excluded( $post, $button ) {
		global $wpdb;

		$result = true;

		/* exclude post_type */
		$post_type_in_exception = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT `id` FROM `' . $wpdb->prefix . 'bws_fancytree`
				WHERE `post_id` IS NULL AND
					`category_id` IS NULL AND
					`post_type` = %s AND
					`exeption` = %s',
				$post->post_type,
				$button
			)
		);

		if ( ! $post_type_in_exception ) {

			/* exclude categories */
			if ( 'post' === $post->post_type ) {
				$post_categories = get_the_category( $post->ID );
				if ( ! empty( $post_categories ) ) {
					$categories = '';
					foreach ( $post_categories as $key => $value ) {
						$categories .= $value->term_id . ',';
					}

					$categories = rtrim( $categories, ',' );

					if ( ! empty( $categories ) ) {
						$category_in_exception = $wpdb->get_var(
							$wpdb->prepare(
								'SELECT `id` FROM `' . $wpdb->prefix . 'bws_fancytree`
								WHERE `post_id` IS NULL AND
									`category_id` IN ( ' . $categories . ' ) AND
									`post_type` = "post" AND
									`exeption` = %s',
								$button
							)
						);
					}

					if ( ! empty( $category_in_exception ) ) {
						$result = false;
					}
				}
			}

			/* exclude for posts ID */
			$single_exception = $wpdb->get_var(
				$wpdb->prepare(
					'SELECT `id` FROM `' . $wpdb->prefix . 'bws_fancytree`
					WHERE `post_type` = %s AND
						`exeption` = %s AND
						`post_id` = %s',
					$post->post_type,
					$button,
					$post->ID
				)
			);

			if ( ! empty( $single_exception ) ) {
				$result = false;
			}
		} else {
			$result = false;
		}

		return $result;
	}
}

add_action( 'admin_init', 'sclbttns_messengers_admin_init' );
add_filter( 'the_content', 'sclbttns_messengers_display_button' );
add_shortcode( 'telegram_button', 'sclbttns_telegram_button' );
add_shortcode( 'whatsapp_button', 'sclbttns_whatsapp_button' );
add_shortcode( 'youtube_button', 'sclbttns_youtube_button' );

