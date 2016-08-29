<?php
if ( ! function_exists( 'twttr_plugins_loaded' ) ) {
	function twttr_plugins_loaded() {
		/* Internationalization, first(!) */
		load_plugin_textdomain( 'twitter-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

/* Function for init */
if ( ! function_exists( 'twttr_init' ) ) {
	function twttr_init() {
		global $twttr_plugin_info;

		if ( empty( $twttr_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$twttr_plugin_info = get_plugin_data( __FILE__ );
		}

				/* Get/Register and check settings for plugin */
		if ( ! is_admin() || ( isset( $_GET['page'] ) && ( "twitter.php" == $_GET['page'] || "social-buttons.php" == $_GET['page'] ) ) )
			twttr_settings();
	}
}

/* Function for admin_init */
if ( ! function_exists( 'twttr_admin_init' ) ) {
	function twttr_admin_init() {
		/* Add variable for bws_menu */
		global $bws_plugin_info, $twttr_plugin_info, $bws_shortcode_list;

				$bws_shortcode_list['twttr'] = array( 'name' => 'Twitter Button', 'js_function' => 'twttr_shortcode_init' );
	}
}
/* end twttr_admin_init */

/* Register settings for plugin */
if ( ! function_exists( 'twttr_settings' ) ) {
	function twttr_settings() {
		global $twttr_options, $twttr_plugin_info, $twttr_options_default;

		$twttr_options_default = array(
			'plugin_option_version'		=> $twttr_plugin_info["Version"],
			'display_settings_notice'	=> 1,
			'suggest_feature_banner'	=> 1,
			'first_install'				=> strtotime( "now" ),
			'url_twitter' 				=> 'admin',
			'display_option'			=> 'standart',
			'count_icon' 				=> 1,
			'img_link' 					=> plugins_url( "images/twitter-follow.jpg", __FILE__ ),
			'position' 					=> 'before',						
			'tweet_display'				=> 1,
			'size'						=> 'default',
			'lang_default'				=> 1,
			'lang'						=> 'en',
			'tailoring'					=> false,
			'url_of_twitter'			=> 'page_url',
			'text_option_twitter'		=> 'page_title',
			'text_twitter'				=> '',
			'via_twitter'				=> '',
			'related_twitter'			=> '',
			'hashtag_twitter'			=> '',
			'followme_display'			=> 0,
			'username_display'			=> false,
			'followers_count_followme'	=> 'false',
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
			'related_mention'			=> ''			
		);
		/* Install the option defaults */
		/* Get options from the database */
		if ( ! get_option( 'twttr_options' ) )
			add_option( 'twttr_options', $twttr_options_default );

		$twttr_options = get_option( 'twttr_options' );

		if ( ! isset( $twttr_options['plugin_option_version'] ) || $twttr_options['plugin_option_version'] != $twttr_plugin_info["Version"] ) {
			/*pls
			* @since 2.48
			* @todo remove after 05.07.2016
			*/
			if ( isset( $twttr_options['disable'] ) ) {
				if ( 0 == $twttr_options['disable'] )
					$twttr_options['tweet_display'] = 1;
				elseif ( 1 == $twttr_options['disable'] )
					$twttr_options['tweet_display'] = 0;
				unset( $twttr_options['disable'] );
			}
			$twttr_options_default['display_settings_notice'] = 0;
			/* end @todo pls*/
		
			$twttr_options = array_merge( $twttr_options_default, $twttr_options );
			$twttr_options['plugin_option_version'] = $twttr_plugin_info["Version"];
			update_option( 'twttr_options', $twttr_options );
		}
	}
}
/* end twttr_settings */

/* Add Setting page */
if ( ! function_exists( 'twttr_settings_page' ) ) {
	function twttr_settings_page() {
		global $twttr_options, $wp_version, $twttr_plugin_info, $title, $twttr_options_default;
		$message = $error = "";
		$upload_dir = wp_upload_dir();
		$plugin_basename = plugin_basename( __FILE__ );

		if ( isset( $_REQUEST['twttr_form_submit'] ) && check_admin_referer( $plugin_basename, 'twttr_nonce_name' ) ) {
			$twttr_options['url_twitter']				= stripslashes( esc_html( $_REQUEST['twttr_user'] ) );
			$twttr_options['display_option' ]			= isset( $_REQUEST['twttr_display_option'] ) ? $_REQUEST['twttr_display_option'] : 'standart' ;
			$twttr_options['position']					= $_REQUEST['twttr_position'];
			$twttr_options['tweet_display']				= isset( $_REQUEST["twttr_twitter_display"] ) ? 1 : 0;
			if ( isset( $_FILES['upload_file']['tmp_name'] ) && $_FILES['upload_file']['tmp_name'] != "" )
				$twttr_options['count_icon']	= $twttr_options['count_icon'] + 1;
			if ( 2 < $twttr_options['count_icon'] )
				$twttr_options['count_icon']	= 1;
			$twttr_options['size']						= $_REQUEST['twttr_size'];
			$twttr_options['lang_default']				= isset( $_REQUEST['twttr_lang_default'] ) ? 1 : 0;
			$twttr_options['lang'] 						= $_REQUEST['twttr_lang'];
			$twttr_options['tailoring']					= isset( $_REQUEST['twttr_tailoring'] ) ? true : false;
			$twttr_options['url_of_twitter']			= esc_html( $_REQUEST['twttr_url_of_twitter'] );
			$twttr_options['text_option_twitter']		= $_REQUEST['twttr_text_option_twitter'];
			$twttr_options['text_twitter']				= esc_html( $_REQUEST['twttr_text_twitter'] );
			$twttr_options['via_twitter']				= esc_html( $_REQUEST['twttr_via_twitter'] );
			$twttr_options['related_twitter']			= esc_html( $_REQUEST['twttr_related_twitter'] );
			$twttr_options['hashtag_twitter']			= esc_html( $_REQUEST['twttr_hashtag_twitter'] );
			$twttr_options['followme_display']			= isset( $_REQUEST['twttr_followme_display'] ) ? 1 : 0;
			$twttr_options['username_display']			= isset( $_REQUEST['twttr_username_display'] ) ? true : false;
			$twttr_options['followers_count_followme']	= isset( $_REQUEST['twttr_followers_count_followme'] ) ? true : false;
			$twttr_options['hashtag_display']			= isset( $_REQUEST['twttr_hashtag_display'] ) ? 1 : 0;
			$twttr_options['hashtag']					= esc_html( $_REQUEST['twttr_hashtag'] );
			$twttr_options['text_option_hashtag']		= $_REQUEST['twttr_text_option_hashtag'];
			$twttr_options['text_hashtag']				= esc_html( $_REQUEST['twttr_text_hashtag'] );
			$twttr_options['url_option_hashtag']		= $_REQUEST['twttr_url_option_hashtag'];
			$twttr_options['related_hashtag']			= esc_html( $_REQUEST['twttr_related_hashtag'] );
			$twttr_options['mention_display']			= isset( $_REQUEST['twttr_mention_display'] ) ? 1 : 0;
			$twttr_options['tweet_to_mention']			= esc_html( $_REQUEST['twttr_tweet_to_mention'] );
			$twttr_options['text_option_mention']		= $_REQUEST['twttr_text_option_mention'];
			$twttr_options['text_mention']				= esc_html( $_REQUEST['twttr_text_mention'] );
			$twttr_options['related_mention']			= esc_html( $_REQUEST['twttr_related_mention'] );

			$twttr_options = apply_filters( 'twttr_before_save_options', $twttr_options );

			update_option( 'twttr_options', $twttr_options );
			$message = __( "Settings saved", 'twitter-plugin' );

			/* Form options */
			if ( isset( $_FILES['upload_file']['tmp_name'] ) && "" != $_FILES['upload_file']['tmp_name'] ) {
				if ( ! $upload_dir["error"] ) {
					$twttr_cstm_mg_folder = $upload_dir['basedir'] . '/twitter-logo';
					if ( ! is_dir( $twttr_cstm_mg_folder ) ) {
						wp_mkdir_p( $twttr_cstm_mg_folder, 0755 );
					}
				}
				$max_image_width	= 100;
				$max_image_height	= 100;
				$max_image_size		= 32 * 1024;
				$valid_types 		= array( 'jpg', 'jpeg', 'png' );

				/* Checks is file download initiated by user */
				if ( isset( $_FILES['upload_file'] ) && 'custom' == $_REQUEST['twttr_display_option'] ) {
					/* Checking is allowed download file given parameters */
					if ( is_uploaded_file( $_FILES['upload_file']['tmp_name'] ) ) {
						$filename	= $_FILES['upload_file']['tmp_name'];
						$ext		= substr( $_FILES['upload_file']['name'], 1 + strrpos( $_FILES['upload_file']['name'], '.' ) );
						if ( filesize( $filename ) > $max_image_size ) {
							$error = __( "Error: File size > 32K", 'twitter-plugin' );
						} elseif ( ! in_array( strtolower( $ext ), $valid_types ) ) {
							$error = __( "Error: Invalid file type", 'twitter-plugin' );
						} else {
							$size = GetImageSize( $filename );
							if ( $size && $size[0] <= $max_image_width && $size[1] <= $max_image_height ) {
								/* If file satisfies requirements, we will move them from temp to your plugin folder and rename to 'twitter_ico.jpg' */
								/* Construction to rename downloading file */
								$namefile	= 'twitter-follow' . $twttr_options['count_icon'] . '.' . $ext;
								$uploadfile	= $twttr_cstm_mg_folder . '/' . $namefile;

								if ( move_uploaded_file( $_FILES['upload_file']['tmp_name'], $uploadfile ) ) {
									if ( 'standart' == $twttr_options[ 'display_option' ] ) {
										$twttr_img_link	= plugins_url( 'images/twitter-follow.jpg', __FILE__ );
									} else if ( 'custom' == $twttr_options['display_option'] ) {
										$twttr_img_link = $upload_dir['baseurl'] . '/twitter-logo/twitter-follow' . $twttr_options['count_icon'] . '.' . $ext;
									}
									$twttr_options['img_link'] = $twttr_img_link;
									update_option( "twttr_options", $twttr_options );
									$message .= '. ' . __( "The upload was successful", 'twitter-plugin' );
								} else {
									$error = __( "Error: Failed to move file", 'twitter-plugin' );
								}
							} else {
								$error = __( "Error: check image width or height", 'twitter-plugin' );
							}
						}
					} else {
						$error = __( "Uploading Error: check image properties", 'twitter-plugin' );
					}
				}
			}
		}

				?>
					<div class="updated fade below-h2" <?php if ( empty( $message ) || "" != $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<?php bws_show_settings_notice(); ?>
			<div class="error below-h2" <?php if ( "" == $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $error; ?></strong></p></div>
			<?php ?>
					<br>
					<div><?php $icon_shortcode = ( 'social-buttons.php' == $_GET['page'] ) ? plugins_url( 'social-buttons-pack/bws_menu/images/shortcode-icon.png' ) : plugins_url( 'bws_menu/images/shortcode-icon.png', __FILE__ );
					printf( 
						__( "If you want to add twitter buttons to your page or post, please use %s button", 'twitter-plugin' ),
						'<span class="bws_code"><img style="vertical-align: sub;" src="' . $icon_shortcode . '" alt=""/></span>' ); ?>
						<div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help">
							<div class="bws_hidden_help_text" style="min-width: 180px;">
								<?php printf(
									__( "You can add Twitter buttons to your page or post by clicking on %s button in the content edit block using the Visual mode. If the button isn't displayed, please use the shortcode %s. Instead of asterisk, please add the necessary buttons separated by commas (Ex:", 'twitter-plugin' ),
									'<code><img style="vertical-align: sub;" src="' . $icon_shortcode . '" alt="" /></code>',
									'<code>[twitter_buttons display=*]</code>'
								); ?>
								<code>[twitter_buttons display=tweet,follow,hashtag,mention]</code>)
							</div>
						</div>
					</div>
					<form method="post" action="" enctype="multipart/form-data" class="bws_form" id="twttr_settings_form">
						<div id="twttr_form_table">
							<table class="form-table">
								<tr valign="top" id="twttr_position">
									<th scope="row">
										<?php _e( 'Twitter buttons position', 'twitter-plugin' ); ?>
									</th>
									<td>
										<select name="twttr_position">
											<option value="before" <?php if ( 'before' == $twttr_options['position'] ) echo 'selected="selected"';?>><?php _e( 'Before', 'twitter-plugin' ); ?></option>
											<option value="after" <?php if ( 'after' == $twttr_options['position'] ) echo 'selected="selected"';?>><?php _e( 'After', 'twitter-plugin' ); ?></option>
											<option value="after_and_before" <?php if ( 'after_and_before' == $twttr_options['position'] ) echo 'selected="selected"';?>><?php _e( 'Before And After', 'twitter-plugin' ); ?></option>
											<option value="shortcode" <?php if ( 'shortcode' == $twttr_options['position'] ) echo 'selected="selected"';?>><?php _e( 'Only shortcode', 'twitter-plugin' ); ?></option>
										</select>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( 'Show button', 'twitter-plugin' ); ?></th>
									<td><fieldset>
										<label><input id="twttr_twitter_display" name="twttr_twitter_display" type="checkbox" value="1" <?php if ( 1 == $twttr_options['tweet_display'] ) echo 'checked="checked"'; ?> />
										<?php _e( 'Tweet (Share a link)', 'twitter-plugin' ); ?></label><br />
										<label><input id="twttr_followme_display" name="twttr_followme_display" type="checkbox" value="1" <?php if ( 1 == $twttr_options['followme_display'] ) echo 'checked="checked"'; ?> />
										<?php _e( 'Follow me', 'twitter-plugin' ); ?></label><br />
										<label><input id="twttr_hashtag_display" name="twttr_hashtag_display" type="checkbox" value="1" <?php if ( 1 == $twttr_options['hashtag_display'] ) echo 'checked="checked"'; ?> />
										<?php _e( 'Hashtag', 'twitter-plugin' ); ?></label><br />
										<label><input id="twttr_mention_display" name="twttr_mention_display" type="checkbox" value="1" <?php if ( 1 == $twttr_options['mention_display'] ) echo 'checked="checked"'; ?> />
										<?php _e( 'Mention', 'twitter-plugin' ); ?></label>
									</fieldset></td>
								</tr>
								<tr valign="top">
									<th scope="row">
										<?php _e( 'Language', 'twitter-plugin' ); ?>
									</th>
									<td>
										<label><input id="twttr_lang_default" name="twttr_lang_default" type="checkbox" value="1" <?php if ( 1 == $twttr_options['lang_default'] ) echo 'checked="checked"'; ?> />
										<?php _e( 'Automatic', 'twitter-plugin' ); ?></label><br /><br />
										<select name="twttr_lang" id="twttr_lang_choose" <?php if ( 1 == $twttr_options['lang_default'] ) echo 'style="display:none"'; ?> >
											<option <?php if ( 'en' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="en">English</option>
											<option <?php if ( 'fr' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="fr">French - français</option>
											<option <?php if ( 'es' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="es">Spanish - Español</option>
											<option <?php if ( 'de' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="de">German - Deutsch</option>
											<option <?php if ( 'it' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="it">Italian - Italiano</option>
											<option <?php if ( 'ru' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="ru">Russian - Русский</option>
											<option <?php if ( 'ar' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="ar">Arabic - العربية</option>
											<option <?php if ( 'ja' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="ja">Japanese - 日本語</option>
											<option <?php if ( 'id' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="id">Indonesian - Bahasa Indonesia</option>
											<option <?php if ( 'pt' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="pt">Portuguese - Português</option>
											<option <?php if ( 'ko' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="ko">Korean - 한국어</option>
											<option <?php if ( 'tr' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="tr">Turkish - Türkçe</option>
											<option <?php if ( 'nl' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="nl">Dutch - Nederlands</option>
											<option <?php if ( 'fil' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="fil">Filipino - Filipino</option>
											<option <?php if ( 'msa' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="msa">Malay - Bahasa Melayu</option>
											<option <?php if ( 'zh-tw' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="zh-tw">Traditional Chinese - 繁體中文</option>
											<option <?php if ( 'zh-cn' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="zh-cn">"Simplified Chinese - 简体中文</option>
											<option <?php if ( 'hi' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="hi">Hindi - हिन्दी</option>
											<option <?php if ( 'no' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="no">Norwegian - Norsk</option>
											<option <?php if ( 'sv' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="sv">Swedish - Svenska</option>
											<option <?php if ( 'fi' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="fi">Finnish - Suomi</option>
											<option <?php if ( 'da' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="da">Danish - Dansk</option>
											<option <?php if ( 'pl' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="pl">Polish - Polski</option>
											<option <?php if ( 'hu' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="hu">Hungarian - Magyar</option>
											<option <?php if ( 'fa' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="fa">Farsi - فارسی</option>
											<option <?php if ( 'he' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="he">Hebrew - עִבְרִית</option>
											<option <?php if ( 'ur' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="ur">Urdu - اردو</option>
											<option <?php if ( 'th' == $twttr_options['lang'] ) echo 'selected="selected"'; ?> value="th">Thai - ภาษาไทย</option>
										</select>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">
										<?php _e( 'Size', 'twitter-plugin' ); ?>
									</th>
									<td>
										<select name="twttr_size">
											<option <?php if ( 'default' == $twttr_options['size'] ) echo 'selected="selected"'; ?> value="default"><?php _e( 'Small', 'twitter-plugin' ); ?></option>
											<option <?php if ( 'large' == $twttr_options['size'] ) echo 'selected="selected"'; ?> value="large"><?php _e( 'Large' , 'twitter-plugin' ); ?></option>
										</select>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">
										<?php _e( 'Opt-out of tailoring Twitter', 'twitter-plugin' ); ?>
									</th>
									<td>
										<input name="twttr_tailoring" type="checkbox" value="1" <?php if ( true == $twttr_options['tailoring'] ) echo 'checked="checked"'; ?> />
										<span class="bws_info"><?php echo __( 'For getting more information about this option, please', 'twitter-plugin' ) . '&#032'; ?><a href="https://support.twitter.com/articles/20169421#" target="_blank"><?php _e( 'click here', 'twitter-plugin' ); ?></a></span>
									</td>
								</tr>
								<?php do_action( 'twttr_settings_page_action', $twttr_options ); ?>
								<tr valign="top" class="twttr_twitter_option" <?php if ( 0 == $twttr_options['tweet_display'] ) echo 'style="display:none"'; ?>>
									<th scope="row" colspan="2" class="twttr-table">
										<?php _e( 'Settings for the "Tweet" button', 'twitter-plugin' ); ?>:
									</th>
								</tr>
								<tr valign="top" class="twttr_twitter_option" <?php if ( 0 == $twttr_options['tweet_display'] ) echo 'style="display:none"'; ?>>
									<th scope="row">
										<?php _e( 'Share URL', 'twitter-plugin' ); ?>
									</th>
									<td><fieldset>
										<label><input name="twttr_url_of_twitter" type="radio" value="page_url" <?php if ( 'page_url' == $twttr_options['url_of_twitter'] ) echo 'checked="checked"'; ?> /><?php _e( 'the page URL', 'twitter-plugin' ); ?></label><br />
										<label><input name="twttr_url_of_twitter" type="radio" value="home_url" <?php if ( 'home_url' == $twttr_options['url_of_twitter'] ) echo 'checked="checked"'; ?> /><?php _e( 'home URL', 'twitter-plugin' ); ?></label>
									</fieldset></td>
								</tr>
								<tr valign="top" class="twttr_twitter_option" <?php if ( 0 == $twttr_options['tweet_display'] ) echo 'style="display:none"'; ?>>
									<th scope="row">
										<?php _e( 'Tweet text', 'twitter-plugin' ); ?>
									</th>
									<td><fieldset>
										<label><input name="twttr_text_option_twitter" type="radio" value="page_title" <?php if ( 'page_title' == $twttr_options['text_option_twitter'] ) echo 'checked="checked"'; ?> /><?php _e( 'the title of the page', 'twitter-plugin' ); ?></label><br />
										<input id="twttr_text_option_twitter" name="twttr_text_option_twitter" type="radio" value="custom" <?php if ( 'custom' == $twttr_options['text_option_twitter'] ) echo 'checked="checked"'; ?> />
										<input name="twttr_text_twitter" id="twttr_text_twitter" type="text" value="<?php echo $twttr_options['text_twitter']; ?>" maxlength="250" />
									</fieldset></td>
								</tr>
								<tr valign="top" class="twttr_twitter_option" <?php if ( 0 == $twttr_options['tweet_display'] ) echo 'style="display:none"'; ?>>
									<th scope="row">
										<?php _e( 'Via', 'twitter-plugin' ); ?>
									</th>
									<td>
										<input name="twttr_via_twitter" type="text" value="<?php echo $twttr_options['via_twitter']; ?>" maxlength="250" /><br />
										<span class="bws_info"><?php _e( 'Tweet been received from the Twitter username', 'twitter-plugin' ); ?></span>
									</td>
								</tr>
								<tr valign="top" class="twttr_twitter_option" <?php if ( 0 == $twttr_options['tweet_display'] ) echo 'style="display:none"'; ?>>
									<th scope="row">
										<?php _e( 'Recommend', 'twitter-plugin' ); ?>
									</th>
									<td>
										<input name="twttr_related_twitter" type="text" value="<?php echo $twttr_options['related_twitter']; ?>" maxlength="250" /><br /><span class="bws_info"> <?php _e( 'Enter username of someone you recomend', 'twitter-plugin' ); ?></span>
									</td>
								</tr>
								<tr valign="top" class="twttr_twitter_option" <?php if ( 0 == $twttr_options['tweet_display'] ) echo 'style="display:none"'; ?>>
									<th scope="row">
										<?php _e( 'Hashtag', 'twitter-plugin' ); ?>
									</th>
									<td>
										<input name="twttr_hashtag_twitter" type="text" value="<?php echo $twttr_options['hashtag_twitter']; ?>" maxlength="250" />
									</td>
								</tr>
								<tr valign="top" class="twttr_followme_option" <?php if ( 0 == $twttr_options['followme_display'] ) echo 'style="display:none"'; ?>>
									<th scope="row" colspan="2" class="twttr-table">
										<?php _e( 'Settings for the "Follow me" button', 'twitter-plugin' ); ?>:
									</th>
								</tr>
								<tr valign="top" class="twttr_followme_option" <?php if ( 0 == $twttr_options['followme_display'] ) echo 'style="display:none"'; ?>>
									<th scope="row">
										<?php _e( 'Your username', 'twitter-plugin' ); ?>
									</th>
									<td>
										<input name="twttr_user" type="text" value="<?php echo $twttr_options['url_twitter']; ?>" maxlength="19" /><br />
										<span class="bws_info"><?php _e( 'If you do not have Twitter account yet, you should create it using this link', 'twitter-plugin' ); ?> <a target="_blank" href="https://twitter.com/signup">https://twitter.com/signup</a> .</span>
									</td>
								</tr>
								<tr valign="top" class="twttr_followme_option" <?php if ( 0 == $twttr_options['followme_display'] ) echo 'style="display:none"'; ?>>
									<th scope="row">
										<?php _e( '"Follow me" button image', 'twitter-plugin' ); ?>
									</th>
									<td>
										<?php if ( scandir( $upload_dir['basedir'] ) && is_writable( $upload_dir['basedir'] ) ) { ?>
											<select name="twttr_display_option" id="twttr_display_option">
												<option <?php if ( 'standart' == $twttr_options['display_option'] ) echo 'selected="selected"'; ?> value="standart"><?php _e( "Standard image", 'twitter-plugin' ); ?></option>
												<option <?php if ( 'custom' == $twttr_options['display_option'] ) echo 'selected="selected"'; ?> value="custom"><?php _e( "Custom image", 'twitter-plugin' ); ?></option>
											</select>
										<?php } else {
											echo __( 'To use custom image You need to setup permissions to upload directory of your site', 'twitter-plugin' ) . " - " . $upload_dir['basedir'];
										} ?>
									</td>
								</tr>
								<tr valign="top" class="twttr_followme_option" <?php if ( 0 == $twttr_options['followme_display'] ) echo 'style="display:none"'; ?>>
									<th class="twttr_display_option_custom" <?php if ( 'standart' == $twttr_options['display_option'] ) echo 'style="display:none"'; ?>></th>
									<td class="twttr_display_option_custom" <?php if ( 'standart' == $twttr_options['display_option'] ) echo 'style="display:none"'; ?>>
										<?php _e( 'Current image', 'twitter-plugin' ); ?>: <img src="<?php echo $twttr_options['img_link']; ?>" />
									</td>
								</tr>
								<tr class="twttr_followme_option" <?php if ( 0 == $twttr_options['followme_display'] ) echo 'style="display:none"'; ?>>
									<th class="twttr_display_option_custom" <?php if ( 'standart' == $twttr_options['display_option'] ) echo 'style="display:none"'; ?>></th>
									<td class="twttr_display_option_custom" <?php if ( 'standart' == $twttr_options['display_option'] ) echo 'style="display:none"'; ?>>
										<input type="file" name="upload_file" /><br />
										<span class="bws_info"><?php _e( 'Image properties: max image width:100px; max image height:100px; max image size:32Kb; image types:"jpg", "jpeg".', 'twitter-plugin' ); ?></span>
									</td>
								</tr>
								<tr valign="top" class="twttr_followme_option" <?php if ( 0 == $twttr_options['followme_display'] ) echo 'style="display:none"'; ?>>
									<th class="twttr_display_option_standart" <?php if ( 'standart' != $twttr_options['display_option'] ) echo 'style="display:none"'; ?>>
										<?php _e( 'Show username', 'twitter-plugin' ); ?>
									</th>
									<td class="twttr_display_option_standart" <?php if ( 'standart' != $twttr_options['display_option'] ) echo 'style="display:none"'; ?>>
										<input name="twttr_username_display" type="checkbox" value="true" <?php if ( true == $twttr_options['username_display'] ) echo 'checked="checked"'; ?> />
									</td>
								</tr>
								<tr valign="top" class="twttr_followme_option" <?php if ( 0 == $twttr_options['followme_display'] ) echo 'style="display:none"'; ?>>
									<th class="twttr_display_option_standart" <?php if ( 'standart' != $twttr_options['display_option'] ) echo 'style="display:none"'; ?>>
										<?php _e( 'Show count', 'twitter-plugin' ); ?>
									</th>
									<td class="twttr_display_option_standart" <?php if ( 'standart' != $twttr_options['display_option'] ) echo 'style="display:none"'; ?>>
										<label><input name="twttr_followers_count_followme" type="checkbox" value="true" <?php if ( true == $twttr_options['followers_count_followme'] ) echo 'checked="checked"'; ?> />
										<span class="bws_info"> <?php _e( 'Show the number of Twitter accounts following the specified account', 'twitter-plugin' ); ?></span></label>
									</td>
								</tr>
								<tr valign="top" class="twttr_hashtag_option" <?php if ( 0 == $twttr_options['hashtag_display'] ) echo 'style="display:none"'; ?>>
									<th scope="row" colspan="2" class="twttr-table">
										<?php _e( 'Settings for the "Hashtag" button', 'twitter-plugin' ); ?>:
									</th>
								</tr>
								<tr valign="top" class="twttr_hashtag_option" <?php if ( 0 == $twttr_options['hashtag_display'] ) echo 'style="display:none"'; ?>>
									<th scope="row">
										<?php _e( 'Hashtag', 'twitter-plugin' ); ?>
									</th>
									<td>
										<input name="twttr_hashtag" type="text" value="<?php echo $twttr_options['hashtag']; ?>" maxlength="250" />
									</td>
								</tr>
								<tr valign="top" class="twttr_hashtag_option" <?php if ( 0 == $twttr_options['hashtag_display'] ) echo 'style="display:none"'; ?>>
									<th scope="row">
										<?php _e( 'Tweet text', 'twitter-plugin' ); ?>
									</th>
									<td><fieldset>
										<label><input name="twttr_text_option_hashtag" type="radio" value="page_title" <?php if ( 'page_title' == $twttr_options['text_option_hashtag'] ) echo 'checked="checked"'; ?> /><?php _e( 'the title of the page', 'twitter-plugin' ); ?></label><br />
										<input id="twttr_text_option_hashtag" name="twttr_text_option_hashtag" type="radio" value="custom" <?php if ( 'custom' == $twttr_options['text_option_hashtag'] ) echo 'checked="checked"'; ?> />
										<input name="twttr_text_hashtag" id="twttr_text_hashtag" type="text" value="<?php echo $twttr_options['text_hashtag']; ?>" maxlength="250" />
									</fieldset></td>
								</tr>
								<tr valign="top" class="twttr_hashtag_option" <?php if ( 0 == $twttr_options['hashtag_display'] ) echo 'style="display:none"'; ?>>
									<th scope="row">
										<?php _e( 'Share URL', 'twitter-plugin' ); ?>
									</th>
									<td><fieldset>
										<label><input name="twttr_url_option_hashtag" type="radio" value="no_url" <?php if ( 'no_url' == $twttr_options['url_option_hashtag'] ) echo 'checked="checked"'; ?> /><?php _e( 'no URL', 'twitter-plugin' ); ?></label><br />
										<label><input name="twttr_url_option_hashtag" type="radio" value="home_url" <?php if ( 'home_url' == $twttr_options['url_option_hashtag'] ) echo 'checked="checked"'; ?> /><?php _e( 'home URL', 'twitter-plugin' ); ?></label><br />
										<label><input name="twttr_url_option_hashtag" type="radio" value="page_url" <?php if ( 'page_url' == $twttr_options['url_option_hashtag'] ) echo 'checked="checked"'; ?> /><?php _e( 'the page URL', 'twitter-plugin' ); ?></label>
									</fieldset></td>
								</tr>
								<tr valign="top" class="twttr_hashtag_option" <?php if ( 0 == $twttr_options['hashtag_display'] ) echo 'style="display:none"'; ?>>
									<th scope="row">
										<?php _e( 'Recommend', 'twitter-plugin' ); ?>
									</th>
									<td>
										<input name="twttr_related_hashtag" type="text" value="<?php echo $twttr_options['related_hashtag']; ?>" maxlength="250" /><br /><span class="bws_info"> <?php _e( 'Enter username of someone you recomend', 'twitter-plugin' ); ?></span>
									</td>
								</tr>
								<tr valign="top" class="twttr_mention_option" <?php if ( 0 == $twttr_options['mention_display'] ) echo 'style="display:none"'; ?>>
									<th scope="row" colspan="2" class="twttr-table">
										<?php _e( 'Settings for the "Mention" button', 'twitter-plugin' ); ?>:
									</th>
								</tr>
								<tr valign="top" class="twttr_mention_option" <?php if ( 0 == $twttr_options['mention_display'] ) echo 'style="display:none"'; ?>>
									<th scope="row">
										<?php _e( 'Tweet to', 'twitter-plugin' ); ?>
									</th>
									<td>
										<input name="twttr_tweet_to_mention" type="text" value="<?php echo $twttr_options['tweet_to_mention']; ?>" maxlength="250" placeholder="support" />
									</td>
								</tr>
								<tr valign="top" class="twttr_mention_option" <?php if ( 0 == $twttr_options['mention_display'] ) echo 'style="display:none"'; ?>>
									<th scope="row">
										<?php _e( 'Tweet text', 'twitter-plugin' ); ?>
									</th>
									<td><fieldset>
										<label><input name="twttr_text_option_mention" type="radio" value="page_title" <?php if ( 'page_title' == $twttr_options['text_option_mention'] ) echo 'checked="checked"'; ?> /><?php _e( 'the title of the page', 'twitter-plugin' ); ?></label><br />
										<input id="twttr_text_option_mention" name="twttr_text_option_mention" type="radio" value="custom" <?php if ( 'custom' == $twttr_options['text_option_mention'] ) echo 'checked="checked"'; ?> />
										<input name="twttr_text_mention" id="twttr_text_mention" type="text" value="<?php echo $twttr_options['text_mention']; ?>" maxlength="250" />
									</fieldset></td>
								</tr>
								<tr valign="top" class="twttr_mention_option" <?php if ( 0 == $twttr_options['mention_display'] ) echo 'style="display:none"'; ?>>
									<th scope="row">
										<?php _e( 'Recommend', 'twitter-plugin' ); ?>
									</th>
									<td>
										<input name="twttr_related_mention" type="text" value="<?php echo $twttr_options['related_mention']; ?>" maxlength="250" /><br /><span class="bws_info"> <?php _e( 'Enter username of someone you recomend', 'twitter-plugin' ); ?></span>
									</td>
								</tr>
							</table>
							<p class="submit">
								<input type="hidden" name="twttr_form_submit" value="submit" />
								<input id="bws-submit-button" type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'twitter-plugin' ) ?>" />
								<?php wp_nonce_field( $plugin_basename, 'twttr_nonce_name' ); ?>
							</p>
						</div>
						<!-- end pls -->
					</form>
						<?php }
}

/* Function to creates shortcode [twitter_buttons] */
if ( ! function_exists( 'twttr_twitter_buttons' ) ) {
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

/* Positioning in the page */
if ( ! function_exists( 'twttr_twit' ) ) {
	function twttr_twit( $content ) {
		global $post, $twttr_options, $wp_current_filter;

		if ( ! empty( $wp_current_filter ) && in_array( 'get_the_excerpt', $wp_current_filter ) )
			return $content;

		if ( ( $twttr_options['position'] != 'shortcode' ) && ( 1 == $twttr_options['tweet_display'] || 1 == $twttr_options['followme_display'] || 1 == $twttr_options['hashtag_display'] || 1 == $twttr_options['mention_display'] ) ) {
			$buttons = twttr_show_button( $twttr_options['tweet_display'], $twttr_options['followme_display'], $twttr_options['hashtag_display'], $twttr_options['mention_display'] );
			$buttons = apply_filters( 'twttr_button_in_the_content', $buttons );
			if ( 'before' == $twttr_options['position'] ) {
				return $buttons . $content;
			} elseif ( 'after' == $twttr_options['position'] ) {
				return $content . $buttons;
			} else {
				return $buttons . $content . $buttons;
			}
		}

		return $content;
	}
}

/* Function for showing buttons */
if ( ! function_exists( 'twttr_show_button' ) ) {
	function twttr_show_button( $tweet, $follow, $hashtag, $mention ) {
		global $post, $twttr_options, $twttr_add_api_script;

		if ( is_feed() )
			return;		

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
				$tweet = "";
			}
			if ( 1 == $follow ) {
				/*option for follow me button*/
				if ( $twttr_options['url_twitter'] == "" )
					$twttr_options['url_twitter'] = "twitter";				

				/*show follow me button*/
				$upload_dir = wp_upload_dir();
				if ( 'standart' == $twttr_options[ 'display_option' ] || ! is_writable( $upload_dir['basedir'] ) ) {
					$show_count = ( $twttr_options['followers_count_followme'] ) ? 'data-show-count="true"' : 'data-show-count="false"';
					$show_name = ( $twttr_options['username_display'] ) ? 'data-show-screen-name="true"' : 'data-show-screen-name="false"';

					$follow = '<div class="twttr_followme">
						<a href="https://twitter.com/' . $twttr_options['url_twitter'] . '" class="twitter-follow-button" ' . $show_count . ' data-size="' . $twttr_options['size'] . '" ' . $lang . ' ' . $show_name . ' ' . $tailoring . ' target="_blank">' . __( 'Follow me', 'twitter-plugin' ) . '</a>
					</div>';
				} else {
					$follow = '<div class="twttr_followme">
						<a href="http://twitter.com/' . $twttr_options['url_twitter'] . '" target="_blank" title="Follow me"><img src="' . $twttr_options['img_link'] . '" alt="' . __( 'Follow me', 'twitter-plugin' ) . '" /></a>
					</div>';
				}
			} else {
				$follow = "";
			}
			if ( 1 == $hashtag ) {
				/*option for hashtag button*/
				$hashtag_tag = ( $twttr_options['hashtag'] == "" ) ? $twttr_options['hashtag'] = __( 'TwitterStories', 'twitter-pro' ) : urlencode( $twttr_options['hashtag'] );
				if ( $twttr_options['hashtag'] == "" )
					$twttr_options['hashtag'] = __( 'TwitterStories', 'twitter-plugin' );

				$text_hashtag = ( 'page_title' == $twttr_options['text_option_hashtag'] ) ? htmlspecialchars( urlencode( $post->post_title ) ) : urlencode( $twttr_options['text_hashtag'] );

				if ( 'no_url' == $twttr_options['url_option_hashtag'] ) {
					$url_hashtag = '';
				} elseif ( 'home_url' == $twttr_options['url_option_hashtag'] ) {
					$url_hashtag = get_home_url();
				} else {
					$url_hashtag = get_permalink( $post->ID );
				}
				/*show hashtag button*/
				if ( $text_hashtag == "" ) {
					$hashtag = '<div class="twttr_hashtag">
						<a href="https://twitter.com/intent/tweet?button_hashtag=' . $hashtag_tag . '" class="twitter-hashtag-button" data-size="' . $twttr_options['size'] . '" ' . $lang . ' data-related="' . $twttr_options['related_hashtag'] . '" data-url="' . $url_hashtag . '" ' . $tailoring . ' target="_blank">' . __( 'Tweet', 'twitter-plugin' ) . ' #' . $twttr_options['hashtag'] . '</a>
					</div>';
				} else {
					$hashtag = '<div class="twttr_hashtag">
						<a href="https://twitter.com/intent/tweet?button_hashtag=' . $hashtag_tag . '&text=' . $text_hashtag . '" class="twitter-hashtag-button" data-size="' . $twttr_options['size'] . '" ' . $lang . ' data-related="' . $twttr_options['related_hashtag'] . '" data-url="' . $url_hashtag . '" ' . $tailoring . ' target="_blank">' . __( 'Tweet', 'twitter-plugin' ) . ' #' . $twttr_options['hashtag'] . '</a>
					</div>';
				}
			} else {
				$hashtag = "";
			}
			if ( 1 == $mention ) {
				/*option for mention button*/
				if ( empty( $twttr_options['tweet_to_mention'] ) ) {
					$twttr_options['tweet_to_mention'] = "support";
				}
				if ( 'page_title' == $twttr_options['text_option_mention'] )
					$text_mention = '';

				$text_mention = ( 'page_title' == $twttr_options['text_option_mention'] ) ? htmlspecialchars( urlencode( $post->post_title ) ) : urlencode( $twttr_options['text_mention'] );
				/*show mention button*/
				$mention = '<div class="twttr_mention">
					<a href="https://twitter.com/intent/tweet?screen_name=' . $twttr_options['tweet_to_mention'] . '&text=' . $text_mention . '" class="twitter-mention-button" data-size="' . $twttr_options['size'] . '" ' . $lang . ' data-related="' . $twttr_options['related_mention'] . '" ' . $tailoring . ' target="_blank">' . __( 'Tweet to', 'twitter-plugin' ) . ' @'. $twttr_options['tweet_to_mention'] .'</a>
				</div>';
			} else {
				$mention = "";
			}
			return '<div class="twttr_buttons">' . $tweet . $follow . $hashtag . $mention . '</div>';
		}
	}
}

/* Registering and apllying styles and scripts */
if ( ! function_exists( 'twttr_wp_head' ) ) {
	function twttr_wp_head() {
		wp_enqueue_style( 'twttr_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );
	}
}

if ( ! function_exists( 'twttr_admin_enqueue_scripts' ) ) {
	function twttr_admin_enqueue_scripts() {
		if ( isset( $_GET['page'] ) && ( "twitter.php" == $_GET['page'] || "social-buttons.php" == $_GET['page'] ) ) {
			wp_enqueue_style( 'twttr_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'twttr_script', plugins_url( 'js/script.js' , __FILE__ ), array( 'jquery' ) );

			if ( "twitter.php" == $_GET['page'] && isset( $_GET['action'] ) && 'custom_code' == $_GET['action'] )
				bws_plugins_include_codemirror();
		}
	}
}

if ( ! function_exists( 'twttr_api_scripts' ) ) {
	function twttr_api_scripts() {
		global $twttr_add_api_script;
		if ( true == $twttr_add_api_script ) { ?>
			<script type="text/javascript">
				!function(d,s,id) {var js,fjs=d.getElementsByTagName(s)[0];if (!d.getElementById(id)) {js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
			</script> 
			<?php $twttr_add_api_script = false;
		}
	}
}

/* add shortcode content */
if ( ! function_exists( 'twttr_shortcode_button_content' ) ) {
	function twttr_shortcode_button_content( $content ) {
		global $wp_version, $post; ?>
		<div id="twttr" style="display:none;">
			<fieldset>
				<?php _e( 'Please select twitter buttons which will be displayed', 'twitter-plugin' ); ?><br />
				<label><input type="checkbox" value="1" id="twttr_tweet" name="twttr_tweet"><?php _e( 'Tweet', 'twitter-plugin' ); ?></label><br />
				<label><input type="checkbox" value="1" id="twttr_followme" name="twttr_followme" checked="checked"><?php _e( 'Follow me', 'twitter-plugin' ); ?></label><br />
				<label><input type="checkbox" value="1" id="twttr_hashtag" name="twttr_hashtag"><?php _e( 'Hashtag', 'twitter-plugin' ); ?></label><br />
				<label><input type="checkbox" value="1" id="twttr_mention" name="twttr_mention"><?php _e( 'Mention', 'twitter-plugin' ); ?></label><br />
			</fieldset>
			<input class="bws_default_shortcode" type="hidden" name="default" value="[twitter_buttons]" />
			<script type="text/javascript">
				function twttr_shortcode_init() {
					(function($) {
						<?php if ( $wp_version < '3.9' ) { ?>
							var current_object = '#TB_ajaxContent';
						<?php } else { ?>
							var current_object = '.mce-reset';
						<?php } ?>
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
				}
			</script>
			<div class="clear"></div>
		</div>
	<?php }
}

add_action( 'plugins_loaded', 'twttr_plugins_loaded' );
add_action( 'init', 'twttr_init' );
/*admin_init */
add_action( 'admin_init', 'twttr_admin_init' );
/* Adding stylesheets */
add_action( 'wp_enqueue_scripts', 'twttr_wp_head' );
add_action( 'wp_footer', 'twttr_api_scripts' );
add_action( 'admin_enqueue_scripts', 'twttr_admin_enqueue_scripts' );
/* Adding plugin buttons */
add_shortcode( 'follow_me', 'twttr_twitter_buttons' );
add_shortcode( 'twitter_buttons', 'twttr_twitter_buttons' );
add_filter( 'widget_text', 'do_shortcode' );
add_filter( 'the_content', "twttr_twit" );
/* custom filter for bws button in tinyMCE */
add_filter( 'bws_shortcode_button_content', 'twttr_shortcode_button_content' );
