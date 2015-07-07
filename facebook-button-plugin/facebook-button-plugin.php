<?php
/* Initialization */
if ( ! function_exists( 'fcbkbttn_init' ) ) {
	function fcbkbttn_init() {
		global $fcbkbttn_plugin_info;
		/* Internationalization, first(!) */
		load_plugin_textdomain( 'facebook', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		if ( empty( $fcbkbttn_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$fcbkbttn_plugin_info = get_plugin_data( __FILE__ );
		}

				/* Get options from the database */
		if ( ! is_admin() || ( isset( $_GET['page'] ) && ( "facebook-button-plugin.php" == $_GET['page'] || "social-buttons.php" == $_GET['page'] ) ) ) {
			/* Get/Register and check settings for plugin */
			fcbkbttn_settings();
		}
	}
}
/* End function init */

if ( ! function_exists( 'fcbkbttn_settings' ) ) {
	function fcbkbttn_settings() {
		global $fcbkbttn_options, $fcbkbttn_plugin_info, $fcbkbttn_options_default;

		$fcbkbttn_options_default = array(
			'plugin_option_version' => $fcbkbttn_plugin_info["Version"],
			'link'					=>	'',
			'my_page'				=>	1,
			'like'					=>	1,
			'share'					=>	0,
			'where'					=>	'',
			'display_option'		=>	'standard',
			'count_icon'			=>	1,
			'extention'				=>	'png',
			'fb_img_link'			=>	plugins_url( "images/standard-facebook-ico.png", __FILE__ ),
			'locale' 				=>	'en_US',
			'html5'					=>	0
		);
		/* Install the option defaults */
		if ( ! get_option( 'fcbk_bttn_plgn_options' ) ) {
			if ( false !== get_option( 'fcbk_bttn_plgn_options_array' ) ) {
				$old_options = get_option( 'fcbk_bttn_plgn_options_array' );
				foreach ( $fcbkbttn_options_default as $key => $value ) {
					if ( isset( $old_options['fcbk_bttn_plgn_' . $key] ) )
					$fcbkbttn_options_default[$key] = $old_options['fcbk_bttn_plgn_' . $key];
				}
				update_option( 'fcbk_bttn_plgn_options', $fcbkbttn_options_default );
				delete_option( 'fcbk_bttn_plgn_options_array' );
			}
			add_option( 'fcbk_bttn_plgn_options', $fcbkbttn_options_default );
		}
		/* Get options from the database */
		$fcbkbttn_options = get_option( 'fcbk_bttn_plgn_options' );

		if ( ! isset( $fcbkbttn_options['plugin_option_version'] ) || $fcbkbttn_options['plugin_option_version'] != $fcbkbttn_plugin_info["Version"] ) {
			if ( stristr( $fcbkbttn_options['fb_img_link'], 'standart-facebook-ico.jpg' ) || stristr( $fcbkbttn_options['fb_img_link'], 'standart-facebook-ico.png' ) )
				$fcbkbttn_options['fb_img_link'] = plugins_url( "images/standard-facebook-ico.png", __FILE__ );	

			if ( 'standart' == $fcbkbttn_options['display_option'] )
				$fcbkbttn_options['display_option'] = 'standard';

			if ( stristr( $fcbkbttn_options['fb_img_link'], 'img/' ) )
				$fcbkbttn_options['fb_img_link'] = plugins_url( str_replace( 'img/', 'images/', $fcbkbttn_options['fb_img_link'] ), __FILE__ );	

			$fcbkbttn_options = array_merge( $fcbkbttn_options_default, $fcbkbttn_options );
			$fcbkbttn_options['plugin_option_version'] = $fcbkbttn_plugin_info["Version"];
			update_option( 'fcbk_bttn_plgn_options', $fcbkbttn_options );
		}
	}
}

/* Function formed content of the plugin's admin page. */
if ( ! function_exists( 'fcbkbttn_settings_page' ) ) {
	function fcbkbttn_settings_page() {
		global $fcbkbttn_options, $wp_version, $fcbkbttn_plugin_info, $fcbkbttn_options_default;
		$message = $error = "";
		$upload_dir = wp_upload_dir();
		$plugin_basename = plugin_basename( __FILE__ );

		if ( isset( $_REQUEST['fcbkbttn_form_submit'] ) && check_admin_referer( $plugin_basename, 'fcbkbttn_nonce_name' ) ) {
			/* Takes all the changed settings on the plugin's admin page and saves them in array 'fcbk_bttn_plgn_options'. */
			if ( isset( $_REQUEST['fcbkbttn_where'] ) && isset( $_REQUEST['fcbkbttn_link'] ) && isset( $_REQUEST['fcbkbttn_display_option'] ) ) {
				$fcbkbttn_options['link']			=	stripslashes( esc_html( $_REQUEST['fcbkbttn_link'] ) );
				$fcbkbttn_options['link']			= 	str_replace( 'https://www.facebook.com/profile.php?id=', '', $fcbkbttn_options['link'] );
				$fcbkbttn_options['link']			= 	str_replace( 'https://www.facebook.com/', '', $fcbkbttn_options['link'] );

				$fcbkbttn_options['where']			=	$_REQUEST['fcbkbttn_where'];
				$fcbkbttn_options['display_option']	=	$_REQUEST['fcbkbttn_display_option'];
				if ( 'standard' == $fcbkbttn_options['display_option'] ) {
					$fcbkbttn_options['fb_img_link'] = plugins_url( 'images/standard-facebook-ico.png', __FILE__ );
				}				
				$fcbkbttn_options['my_page']		=	isset( $_REQUEST['fcbkbttn_my_page'] ) ? 1 : 0 ;
				$fcbkbttn_options['like']			=	isset( $_REQUEST['fcbkbttn_like'] ) ? 1 : 0 ;
				$fcbkbttn_options['share']			=	isset( $_REQUEST['fcbkbttn_share'] ) ? 1 : 0 ;
				$fcbkbttn_options['locale']			=	$_REQUEST['fcbkbttn_locale'];
				$fcbkbttn_options['html5']			= 	$_REQUEST['fcbkbttn_html5'];
				if ( isset( $_FILES['uploadfile']['tmp_name'] ) &&  $_FILES['uploadfile']['tmp_name'] != "" ) {
					$fcbkbttn_options['count_icon']	=	$fcbkbttn_options['count_icon'] + 1;
					$file_ext = wp_check_filetype( $_FILES['uploadfile']['name'] );
					$fcbkbttn_options['extention'] = $file_ext['ext'];
				}

				if ( 2 < $fcbkbttn_options['count_icon'] )
					$fcbkbttn_options['count_icon']	=	1;
				update_option( 'fcbk_bttn_plgn_options', $fcbkbttn_options );
				$message = __( "Settings saved", 'facebook' );
			}
			/* Form options */
			if ( isset( $_FILES['uploadfile']['tmp_name'] ) &&  "" != $_FILES['uploadfile']['tmp_name'] ) {
				if ( ! $upload_dir["error"] ) {
					$fcbkbttn_cstm_mg_folder = $upload_dir['basedir'] . '/facebook-image';
					if ( ! is_dir( $fcbkbttn_cstm_mg_folder ) ) {
						wp_mkdir_p( $fcbkbttn_cstm_mg_folder, 0755 );
					}
				}				
				$max_image_width	=	100;
				$max_image_height	=	40;
				$max_image_size		=	32 * 1024;
				$valid_types 		=	array( 'jpg', 'jpeg', 'png' );
				/* Construction to rename downloading file */
				$new_name			=	'facebook-ico' . $fcbkbttn_options['count_icon'];
				$new_ext			=	wp_check_filetype( $_FILES['uploadfile']['name'] );
				$namefile			=	$new_name . '.' . $new_ext['ext'];
				$uploadfile			=	$fcbkbttn_cstm_mg_folder . '/' . $namefile;

				/* Checks is file download initiated by user */
				if ( isset( $_FILES['uploadfile'] ) && 'custom' == $_REQUEST['fcbkbttn_display_option'] ) {
					/* Checking is allowed download file given parameters */
					if ( is_uploaded_file( $_FILES['uploadfile']['tmp_name'] ) ) {
						$filename	=	$_FILES['uploadfile']['tmp_name'];
						$ext		=	substr( $_FILES['uploadfile']['name'], 1 + strrpos( $_FILES['uploadfile']['name'], '.' ) );
						if ( filesize( $filename ) > $max_image_size ) {
							$error	=	__( "Error: File size > 32K", 'facebook' );
						}
						elseif ( ! in_array( $ext, $valid_types ) ) {
							$error	=	__( "Error: Invalid file type", 'facebook' );
						} else {
							$size	=	GetImageSize( $filename );
							if ( ( $size ) && ( $size[0] <= $max_image_width ) && ( $size[1] <= $max_image_height ) ) {
								/* If file satisfies requirements, we will move them from temp to your plugin folder and rename to 'facebook_ico.jpg' */
								if ( move_uploaded_file( $_FILES['uploadfile']['tmp_name'], $uploadfile ) ) {
									$message .= '. ' . __( "Upload successful.", 'facebook' );
									
									if ( 'standard' == $fcbkbttn_options['display_option'] ) {
										$fb_img_link = plugins_url( 'images/standard-facebook-ico.png', __FILE__ );
									} else if ( 'custom' == $fcbkbttn_options['display_option'] ) {
										$fb_img_link = $upload_dir['baseurl'] . '/facebook-image/facebook-ico' . $fcbkbttn_options['count_icon'] . '.' . $fcbkbttn_options['extention'];
									}
									$fcbkbttn_options['fb_img_link'] = $fb_img_link ;
									update_option( 'fcbk_bttn_plgn_options', $fcbkbttn_options );
								} else {
									$error = __( "Error: moving file failed", 'facebook' );
								}
							} else {
								$error = __( "Error: check image width or height", 'facebook' );
							}
						}
					} else {
						$error = __( "Uploading Error: check image properties", 'facebook' );
					}
				}
			}
		}

		$lang_codes = array(
			"af_ZA" => 'Afrikaans', "ar_AR" => 'العربية', "ay_BO" => 'Aymar aru', "az_AZ" => 'Azərbaycan dili', "be_BY" => 'Беларуская', "bg_BG" => 'Български', "bn_IN" => 'বাংলা', "bs_BA" => 'Bosanski', "ca_ES" => 'Català', "ck_US" => 'Cherokee', "cs_CZ" => 'Čeština', "cy_GB" => 'Cymraeg', "da_DK" => 'Dansk', "de_DE" => 'Deutsch', "el_GR" => 'Ελληνικά', "en_US" => 'English', "en_PI" => 'English (Pirate)', "eo_EO" => 'Esperanto', "es_CL" => 'Español (Chile)', "es_CO" => 'Español (Colombia)', "es_ES" => 'Español (España)', "es_LA" => 'Español', "es_MX" => 'Español (México)', "es_VE" => 'Español (Venezuela)', "et_EE" => 'Eesti', "eu_ES" => 'Euskara', "fa_IR" => 'فارسی', "fb_LT" => 'Leet Speak', "fi_FI" => 'Suomi', "fo_FO" => 'Føroyskt', "fr_CA" => 'Français (Canada)', "fr_FR" => 'Français (France)', "fy_NL" => 'Frysk', "ga_IE" => 'Gaeilge', "gl_ES" => 'Galego', "gn_PY" => "Avañe'ẽ", "gu_IN" => 'ગુજરાતી', "gx_GR" => 'Ἑλληνική ἀρχαία', "he_IL" => 'עברית', "hi_IN" => 'हिन्दी', "hr_HR" => 'Hrvatski', "hu_HU" => 'Magyar', "hy_AM" => 'Հայերեն', "id_ID" => 'Bahasa Indonesia', "is_IS" => 'Íslenska', "it_IT" => 'Italiano', "ja_JP" => '日本語', "jv_ID" => 'Basa Jawa', "ka_GE" => 'ქართული', "kk_KZ" => 'Қазақша', "km_KH" => 'ភាសាខ្មែរ', "kn_IN" => 'ಕನ್ನಡ', "ko_KR" => '한국어', "ku_TR" => 'Kurdî', "la_VA" => 'lingua latina', "li_NL" => 'Limburgs', "lt_LT" => 'Lietuvių', "lv_LV" => 'Latviešu', "mg_MG" => 'Malagasy', "mk_MK" => 'Македонски', "ml_IN" => 'മലയാളം', "mn_MN" => 'Монгол', "mr_IN" => 'मराठी', "ms_MY" => 'Bahasa Melayu', "mt_MT" => 'Malti', "nb_NO" => 'Norsk (bokmål)', "ne_NP" => 'नेपाली', "nl_BE" => 'Nederlands (België)', "nl_NL" => 'Nederlands', "nn_NO" => 'Norsk (nynorsk)', "pa_IN" => 'ਪੰਜਾਬੀ', "pl_PL" => 'Polski', "ps_AF" => 'پښتو', "pt_BR" => 'Português (Brasil)', "pt_PT" => 'Português (Portugal)', "qu_PE" => 'Qhichwa', "rm_CH" => 'Rumantsch', "ro_RO" => 'Română', "ru_RU" => 'Русский', "sa_IN" => 'संस्कृतम्', "se_NO" => 'Davvisámegiella', "sk_SK" => 'Slovenčina', "sl_SI" => 'Slovenščina', "so_SO" => 'Soomaaliga', "sq_AL" => 'Shqip', "sr_RS" => 'Српски', "sv_SE" => 'Svenska', "sw_KE" => 'Kiswahili', "sy_SY" => 'ܐܪܡܝܐ', "ta_IN" => 'தமிழ்', "te_IN" => 'తెలుగు', "tg_TJ" => 'тоҷикӣ', "th_TH" => 'ภาษาไทย', "tl_PH" => 'Filipino', "tl_ST" => 'tlhIngan-Hol', "tr_TR" => 'Türkçe', "tt_RU" => 'Татарча', "uk_UA" => 'Українська', "ur_PK" => 'اردو', "uz_UZ" => "O'zbek", "vi_VN" => 'Tiếng Việt', "yi_DE" => 'ייִדיש', "zh_CN" => '中文(简体)', "zh_HK" => '中文(香港)', "zh_TW" => '中文(台灣)', "zu_ZA" => 'isiZulu' 											
		);
				?>
					<div class="updated fade" <?php if ( empty( $message ) || "" != $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<div id="fcbkbttn_settings_notice" class="updated fade bws_settings_form_notice" style="display:none"><p><strong><?php _e( "Notice:", 'facebook' ); ?></strong> <?php _e( "The plugin's settings have been changed. In order to save them please don't forget to click the 'Save Changes' button.", 'facebook' ); ?></p></div>
			<div class="error" <?php if ( "" == $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $error; ?></strong></p></div>
			<?php ?>
					<form method="post" action="" enctype="multipart/form-data" id="fcbkbttn_settings_form" class="bws_settings_form">
						<table class="form-table">
							<tr valign="top">
								<th scope="row"><?php _e( 'Your Facebook ID or username', 'facebook' ); ?></th>
								<td>
									<input name='fcbkbttn_link' type='text' maxlength='250' value='<?php echo $fcbkbttn_options['link']; ?>' />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php _e( 'Display button', 'facebook' ); ?></th>
								<td>
									<label><input name='fcbkbttn_my_page' type='checkbox' value='1' <?php if ( 1 == $fcbkbttn_options['my_page'] ) echo 'checked="checked "'; ?>/> <?php _e( "My Page", 'facebook' ); ?></label><br />
									<label><input name='fcbkbttn_like' type='checkbox' value='1' <?php if ( 1 == $fcbkbttn_options['like'] ) echo 'checked="checked "'; ?>/> <?php _e( "Like", 'facebook' ); ?></label><br />
									<label><input name='fcbkbttn_share' type='checkbox' value='1' <?php if ( 1 == $fcbkbttn_options['share'] ) echo 'checked="checked "'; ?>/> <?php _e( "Share", 'facebook' ); ?></label>
								</td>
							</tr>
							<tr class="fcbkbttn_my_page" <?php if ( 1 != $fcbkbttn_options['my_page'] ) echo 'style="display:none"'; ?>>
								<th>
									<?php _e( '"My page" button image', 'facebook' ); ?>
								</th>
								<td>
									<?php if ( scandir( $upload_dir['basedir'] ) && is_writable( $upload_dir['basedir'] ) ) { ?>
										<select name="fcbkbttn_display_option">
											<option <?php if ( 'standard' == $fcbkbttn_options['display_option'] ) echo 'selected="selected"'; ?> value="standard"><?php _e( 'Standard Facebook image', 'facebook' ); ?></option>
											<option <?php if ( 'custom' == $fcbkbttn_options['display_option'] ) echo 'selected="selected"'; ?> value="custom"><?php _e( 'Custom Facebook image', 'facebook' ); ?></option>
										</select>
									<?php } else {
										echo __( 'To use custom image you need to setup permissions to upload directory of your site', 'facebook' ) . " - " . $upload_dir['basedir'];
									} ?>
								</td>
							</tr>
							<tr class="fcbkbttn_my_page" <?php if ( 1 != $fcbkbttn_options['my_page'] ) echo 'style="display:none"'; ?>>
								<th></th>
								<td>
									<?php _e( 'Current image', 'facebook' ); ?>: 
									<img src="<?php echo $fcbkbttn_options['fb_img_link']; ?>" style="vertical-align: middle;" />
								</td>
							</tr>	
							<tr class="fcbkbttn_my_page" id="fcbkbttn_display_option_custom" <?php if ( ! ( 1 == $fcbkbttn_options['my_page'] && 'custom' == $fcbkbttn_options['display_option'] ) ) echo 'style="display:none"'; ?>>
								<th></th>
								<td>
									<input name="uploadfile" type="file" /><br />
									<span class="bws_info"><?php _e( 'Image properties: max image width:100px; max image height:40px; max image size:32Kb; image types:"jpg", "jpeg", "png".', 'facebook' ); ?></span>
								</td>
							</tr>
							<tr>
								<th>
									<?php _e( 'Facebook buttons position', 'facebook' ); ?>
								</th>
								<td>
									<select name="fcbkbttn_where">
										<option <?php if ( 'before' == $fcbkbttn_options['where']  ) echo 'selected="selected"'; ?> value="before"><?php _e( "Before", 'facebook' ); ?></option>
										<option <?php if ( 'after' == $fcbkbttn_options['where']  ) echo 'selected="selected"'; ?> value="after"><?php _e( "After", 'facebook' ); ?></option>
										<option <?php if ( 'beforeandafter' == $fcbkbttn_options['where']  ) echo 'selected="selected"'; ?> value="beforeandafter"><?php _e( "Before and After", 'facebook' ); ?></option>
										<option <?php if ( 'shortcode' == $fcbkbttn_options['where'] ) echo 'selected="selected"'; ?> value="shortcode"><?php _e( "Shortcode", 'facebook' ); ?></option>
									</select>
									<span id="fcbkbttn_shortcode" class="bws_info" <?php if ( $fcbkbttn_options['where'] != 'shortcode' ) echo 'style="display:none"'; ?> ><?php _e( "If you would like to add a Facebook button to your website, just copy and paste this shortcode into your post or page:", 'facebook' ); ?> [fb_button].</span>
								</td>
							</tr>
							<tr>
								<th>
									<?php _e( "Facebook buttons language", 'facebook' ); ?>
								</th>
								<td>
									<select name="fcbkbttn_locale">
									<?php foreach ( $lang_codes as $key => $val ) {
										echo '<option value="' . $key . '"';
										if ( $key == $fcbkbttn_options['locale'] )
											echo ' selected="selected"';
										echo '>' . esc_html ( $val ) . '</option>';
									} ?>
									</select>
									<span class="bws_info"><?php _e( 'Change the language of Facebook Like Button', 'facebook' ); ?></span>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php _e( 'Html tag for "Like" button', 'facebook' ); ?></th>
								<td>
									<label><input name='fcbkbttn_html5' type='radio' value='0' <?php if ( 0 == $fcbkbttn_options['html5'] ) echo 'checked="checked "'; ?> /><?php echo "<code>&lt;fb:like&gt;</code>"; ?></label><br />
									<label><input name='fcbkbttn_html5' type='radio' value='1' <?php if ( 1 == $fcbkbttn_options['html5'] ) echo 'checked="checked "'; ?> /><?php echo "<code>&lt;div&gt;</code>"; ?></label>
									<span class="bws_info">(<?php _e( "Use this tag to improve validation of your site", 'facebook' ); ?>)</span>
								</td>
							</tr>
						</table>
												<input type="hidden" name="fcbkbttn_form_submit" value="submit" />
						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'facebook' ); ?>" />
						</p>
						<?php wp_nonce_field( $plugin_basename, 'fcbkbttn_nonce_name' ); ?>
					</form>				
						<?php }
}

/* Function taking from array 'fcbk_bttn_plgn_options' necessary information to create Facebook Button and reacting to your choise in plugin menu - points where it appears. */
if ( ! function_exists( 'fcbkbttn_display_button' ) ) {
	function fcbkbttn_display_button( $content ) {
		global $post, $fcbkbttn_options;
		/* Query the database to receive array 'fcbk_bttn_plgn_options' and receiving necessary information to create button */
		$fcbkbttn_where	=	$fcbkbttn_options['where'];
		$permalink_post	=	get_permalink( $post->ID );
		/* Button */
		$button			=	'<div class="fcbk_share">';
		$img			=	$fcbkbttn_options['fb_img_link'];
		$url			=	$fcbkbttn_options['link'];
		if ( 1 == $fcbkbttn_options['my_page'] ) {
			$button .=	'<div class="fcbk_button">
							<a href="http://www.facebook.com/' . $url . '"	target="_blank">
								<img src="' . $img . '" alt="Fb-Button" />
							</a>
						</div>';
		}
		if ( 1 == $fcbkbttn_options['like'] ) {
			$button .= '<div class="fcbk_like">';
			if ( 1 == $fcbkbttn_options['html5'] ) {
				$button .=	'<div class="fb-like" data-href="' . $permalink_post . '" data-layout="button_count" data-action="like" data-show-faces="false"';
					if ( 1 == $fcbkbttn_options['share'] )
						$button .= ' data-share="true"></div></div>';
					else
						$button .= ' data-share="false"></div></div>';
			} else {
				$button .= '<fb:like href="' . $permalink_post . '" layout="button_count" width="450" show_faces="false"';
				if ( 1 == $fcbkbttn_options['share'] )
					$button .= ' share="true"></fb:like></div>';
				else
					$button .= ' share="false"></fb:like></div>';
			}
		} else if ( 1 != $fcbkbttn_options['like'] && 1 == $fcbkbttn_options['share'] ) {
			$button .=	'<div class="fb-share-button" data-href="' . $permalink_post . '" data-type="button_count"></div>';
		}		
		$button .= '</div>';
		/* Indication where show Facebook Button depending on selected item in admin page. */
		if ( 'before' == $fcbkbttn_where ) {
			return $button . $content;
		} else if ( 'after' == $fcbkbttn_where ) {
			return $content . $button;
		} else if ( 'beforeandafter' == $fcbkbttn_where ) {
			return $button . $content . $button;
		} else if ( 'shortcode' == $fcbkbttn_where ) {
			return $content;
		} else {
			return $content;
		}
	}
}

/* Function 'fcbk_bttn_plgn_shortcode' are using to create shortcode by Facebook Button. */
if ( ! function_exists( 'fcbkbttn_shortcode' ) ) {
	function fcbkbttn_shortcode( $content ) {
		global $post, $fcbkbttn_options;
		$fcbkbttn_where	=	$fcbkbttn_options['where'];
		$permalink_post	=	get_permalink( $post->ID );
		$button			=	'<div class="fcbk_share">';
		$img			=	$fcbkbttn_options['fb_img_link'];
		$url			=	$fcbkbttn_options['link'];
		if ( 1 == $fcbkbttn_options['my_page'] ) {
			$button .=	'<div class="fcbk_button">
							<a href="http://www.facebook.com/' . $url . '"	target="_blank">
								<img src="' . $img . '" alt="Fb-Button" />
							</a>
						</div>';
		}
		if ( 1 == $fcbkbttn_options['like'] ) {
			$button .=	'<div class="fcbk_like">
							<div id="fb-root"></div>
							<script>(function(d, s, id) {
								var js, fjs = d.getElementsByTagName(s)[0];
								if (d.getElementById(id)) return;
								js = d.createElement(s); js.id = id;
								js.src = "//connect.facebook.net/' . $fcbkbttn_options['locale'] . '/sdk.js#xfbml=1&appId=1443946719181573&version=v2.0";
								fjs.parentNode.insertBefore(js, fjs);
							}(document, "script", "facebook-jssdk"));</script>';
			if ( 1 == $fcbkbttn_options['html5'] ) {
				$button .=	'<div class="fb-like" data-href="' . $permalink_post . '" data-layout="button_count" data-action="like" data-show-faces="false"';
					if ( 1 == $fcbkbttn_options['share'] )
						$button .= ' data-share="true"></div></div>';
					else
						$button .= ' data-share="false"></div></div>';
			} else {
				$button .= '<fb:like href="' . $permalink_post . '" layout="button_count" width="450" show_faces="false"';
				if ( 1 == $fcbkbttn_options['share'] )
					$button .= ' share="true"></fb:like></div>';
				else
					$button .= ' share="false"></fb:like></div>';
			}
		} else if ( 1 != $fcbkbttn_options['like'] && 1 == $fcbkbttn_options['share'] ) {
			$button .=	'<div class="fb-share-button" data-href="' . $permalink_post . '" data-type="button_count"></div>';
		}
		$button .= '</div>';
		return $button;
	}
}

/* Functions adds some right meta for Facebook */
if ( ! function_exists( 'fcbkbttn_meta' ) ) {
	function fcbkbttn_meta() {
		global $fcbkbttn_options;
		if ( 1 == $fcbkbttn_options['like'] ) {
			if ( is_singular() ) {
				$image = '';
				if ( has_post_thumbnail( get_the_ID() ) ) {
					$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail');
					$image = $image[0];
				}
				print "\n" . '<meta property="og:title" content="' . esc_attr( get_the_title() ) . '"/>';
				print "\n" . '<meta property="og:site_name" content="' . esc_attr( get_bloginfo() ) . '"/>';
				if ( ! empty( $image ) ) {
					print "\n" . '<meta property="og:image" content="' . esc_url( $image ) . '"/>';
				}
			}
		}
	}
}

if ( ! function_exists( 'fcbkbttn_footer_script' ) ) {
	function fcbkbttn_footer_script () {
		global $fcbkbttn_options;
		if ( ( 1 == $fcbkbttn_options['like'] || 1 == $fcbkbttn_options['share'] ) && 'shortcode' != $fcbkbttn_options['where'] ) {
			echo '<div id="fb-root"></div>
			<script>(function(d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) return;
				js = d.createElement(s); js.id = id;
				js.src = "//connect.facebook.net/' . $fcbkbttn_options['locale'] . '/sdk.js#xfbml=1&appId=1443946719181573&version=v2.0";
				fjs.parentNode.insertBefore(js, fjs);
			}(document, "script", "facebook-jssdk"));</script>';
		}
	}
}

if ( ! function_exists( 'fcbkbttn_admin_head' ) ) {
	function fcbkbttn_admin_head() {
		global $fcbkbttn_options;
		if ( isset( $_GET['page'] ) && "facebook-button-plugin.php" == $_GET['page'] ) {
			wp_enqueue_script( 'fcbk_script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery' ) );
			wp_enqueue_style( 'fcbk_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );
		} elseif ( ! is_admin() ) {
			wp_enqueue_style( 'fcbk_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );			
			if ( ( 1 == $fcbkbttn_options['like'] || 1 == $fcbkbttn_options['share'] ) && 'en_US' != $fcbkbttn_options['locale'] )
				wp_enqueue_script( 'fcbk_connect', '//connect.facebook.net/' . $fcbkbttn_options['locale'] . '/all.js#xfbml=1&appId=1443946719181573' );
		}
	}
}

add_action( 'init', 'fcbkbttn_init' );
add_action( 'wp_enqueue_scripts', 'fcbkbttn_admin_head' );
add_action( 'admin_enqueue_scripts', 'fcbkbttn_admin_head' );
/* Adding front-end stylesheets */
add_action( 'wp_head', 'fcbkbttn_meta' );
add_action( 'wp_footer', 'fcbkbttn_footer_script' );
/* Add shortcode and plugin buttons */
add_shortcode( 'fb_button', 'fcbkbttn_shortcode' );
add_filter( 'the_content', 'fcbkbttn_display_button' );
