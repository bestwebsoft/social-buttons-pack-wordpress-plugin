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

				$bws_shortcode_list['twttr'] = array( 'name' => 'Twitter Button' );
	}
}
/* end twttr_admin_init */

/* Register settings for plugin */
if ( ! function_exists( 'twttr_settings' ) ) {
	function twttr_settings() {
		global $twttr_options, $twttr_plugin_info, $twttr_options_default;

		$twttr_options_default = array(
			'plugin_option_version'		=> $twttr_plugin_info["Version"],
			'url_twitter' 				=>	'admin',
			'display_option'			=>	'custom',
			'count_icon' 				=>	1,
			'img_link' 					=>	plugins_url( "images/twitter-follow.jpg", __FILE__ ),
			'position' 					=>	'before',
			'disable' 					=>	'0',
			'first_install'				=>	strtotime( "now" ),
			'display_settings_notice'	=> 1
		);
		/* Install the option defaults */
		/* Get options from the database */
		if ( ! get_option( 'twttr_options' ) ) {
			if ( false !== get_option( 'twttr_options_array' ) ) {
				$old_options = get_option( 'twttr_options_array' );
				foreach ( $twttr_options_default as $key => $value ) {
					if ( isset( $old_options['twttr_' . $key] ) )
						$twttr_options_default[$key] = $old_options['twttr_' . $key];
				}
				delete_option( 'twttr_options_array' );
			}
			add_option( 'twttr_options', $twttr_options_default );
		}
		$twttr_options = get_option( 'twttr_options' );
		
		if ( ! isset( $twttr_options['plugin_option_version'] ) || $twttr_options['plugin_option_version'] != $twttr_plugin_info["Version"] ) {
			if ( '0' == $twttr_options['position'] )
				$twttr_options['position'] = 'after';
			elseif ( '1' == $twttr_options['position'] )
				$twttr_options['position'] = 'before';

			$twttr_options_default['display_settings_notice'] = 0;
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
			$twttr_options['url_twitter']		=	stripslashes( esc_html( $_REQUEST['twttr_url_twitter'] ) );
			$twttr_options['display_option' ]	=	$_REQUEST['twttr_display_option'];
			$twttr_options['position']			=	$_REQUEST['twttr_position'];
			$twttr_options['disable']			=	isset( $_REQUEST["twttr_disable"] ) ? 1 : 0;
			if ( isset( $_FILES['upload_file']['tmp_name'] ) &&  $_FILES['upload_file']['tmp_name'] != "" )
				$twttr_options['count_icon']	=	$twttr_options['count_icon'] + 1;
			if ( 2 < $twttr_options['count_icon'] )
				$twttr_options['count_icon']	=	1;

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
				$max_image_width	=	100;
				$max_image_height	=	100;
				$max_image_size		=	32 * 1024;
				$valid_types 		=	array( 'jpg', 'jpeg', 'png' );				

				/* Checks is file download initiated by user */
				if ( isset( $_FILES['upload_file'] ) && 'custom' == $_REQUEST['twttr_display_option'] )	{
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
								$namefile	=	'twitter-follow' . $twttr_options['count_icon'] . '.' . $ext;
								$uploadfile	=	$twttr_cstm_mg_folder . '/' . $namefile;
								
								if ( move_uploaded_file( $_FILES['upload_file']['tmp_name'], $uploadfile ) ) {									
									if ( 'standart' == $twttr_options[ 'display_option' ] ) {
										$twttr_img_link	=	plugins_url( 'images/twitter-follow.jpg', __FILE__ );
									} else if ( 'custom' == $twttr_options['display_option'] ) {
										$twttr_img_link = $upload_dir['baseurl'] . '/twitter-logo/twitter-follow' . $twttr_options['count_icon'] . '.' . $ext;
									}
									$twttr_options['img_link'] = $twttr_img_link;
									update_option( "twttr_options", $twttr_options );
									$message .= '. ' . __( "Upload successful.", 'twitter-plugin' );
								} else {
									$error = __( "Error: moving file failed", 'twitter-plugin' );
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
					<div class="updated fade" <?php if ( empty( $message ) || "" != $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<?php bws_show_settings_notice(); ?>
			<div class="error" <?php if ( "" == $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $error; ?></strong></p></div>
			<?php ?>
					<br>
					<div><?php $icon_shortcode = ( "twitter.php" == $_GET['page'] ) ? plugins_url( 'bws_menu/images/shortcode-icon.png', __FILE__ ) : plugins_url( 'social-buttons-pack/bws_menu/images/shortcode-icon.png' );
					printf( 
						__( "If you would like to add 'Follow Me' button to your page or post, please use %s button", 'twitter-plugin' ), 
						'<span class="bws_code"><img style="vertical-align: sub;" src="' . $icon_shortcode . '" alt=""/></span>' ); ?> 
						<div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help">
							<div class="bws_hidden_help_text" style="min-width: 180px;">
								<?php printf( 
									__( "You can add 'Follow Me' button to your page or post by clicking on %s button in the content edit block using the Visual mode. If the button isn't displayed, please use the shortcode %s", 'twitter-plugin' ), 
									'<code><img style="vertical-align: sub;" src="' . $icon_shortcode . '" alt="" /></code>',
									'<code>[follow_me]</code>'
								); ?>
							</div>
						</div>
					</div>
					<?php _e( 'If you would like to use this button in some other place, please paste this line into the template source code', 'twitter-plugin' ); ?>	<span class="bws_code">&#60;?php if ( function_exists( 'twttr_follow_me' ) ) echo twttr_follow_me(); ?&#62;</span></p>
					<form method='post' action="" enctype="multipart/form-data" class="bws_form">
						<table class="form-table">
							<tr valign="top">
								<th scope="row" colspan="2"><?php _e( 'Settings for the button "Follow Me"', 'twitter-plugin' ); ?>:</th>
							</tr>
							<tr valign="top">
								<th scope="row">
									<?php _e( "Enter your username", 'twitter-plugin' ); ?>
								</th>
								<td>
									<input name='twttr_url_twitter' type='text' value='<?php echo $twttr_options['url_twitter'] ?>' maxlength='250' /><br />
									<span class="bws_info"><?php _e( 'If you do not have Twitter account yet, you should create it using this link', 'twitter-plugin' ); ?> <a target="_blank" href="https://twitter.com/signup">https://twitter.com/signup</a> .</span><br />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<?php _e( "Choose display settings", 'twitter-plugin' ); ?>
								</th>
								<td>
									<?php if ( scandir( $upload_dir['basedir'] ) && is_writable( $upload_dir['basedir'] ) ) { ?>
										<select name="twttr_display_option" onchange="if ( this . value == 'custom' ) { getElementById ( 'twttr_display_option_custom' ) . style.display = 'block'; } else { getElementById ( 'twttr_display_option_custom' ) . style.display = 'none'; }">
											<option <?php if ( 'standart' == $twttr_options['display_option'] ) echo 'selected="selected"'; ?> value="standart"><?php _e( "Standard button", 'twitter-plugin' ); ?></option>
											<option <?php if ( 'custom' == $twttr_options['display_option'] ) echo 'selected="selected"'; ?> value="custom"><?php _e( "Custom button", 'twitter-plugin' ); ?></option>
										</select>
									<?php } else {
										echo __( "To use custom image You need to setup permissions to upload directory of your site", 'twitter-plugin' ) . " - " . $upload_dir['basedir'];
									} ?>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<div id="twttr_display_option_custom" <?php if ( 'custom' == $twttr_options['display_option'] ) { echo ( 'style="display:block"' ); } else { echo ( 'style="display:none"' ); } ?>>
										<table>
											<th style="padding-left:0px;font-size:13px;">
												<?php _e( "Current image", 'twitter-plugin' ); ?>
											</th>
											<td>
												<img src="<?php echo $twttr_options['img_link']; ?>" />
											</td>
										</table>
										<table>
											<th style="padding-left:0px;font-size:13px;">
												<?php _e( '"Follow Me" image', 'twitter-plugin' ); ?>
											</th>
											<td>
												<input type="file" name="upload_file" /><br />
												<span class="bws_info"><?php _e( 'Image properties: max image width:100px; max image height:100px; max image size:32Kb; image types:"jpg", "jpeg".', 'twitter-plugin' ); ?></span>
											</td>
										</table>
									</div>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row" colspan="2"><?php _e( 'Settings for the "Twitter" button', 'twitter-plugin' ); ?>:</th>
							</tr>
							<tr>
								<th><?php _e( 'Disable the "Twitter" button', 'twitter-plugin' ); ?></th>
								<td>
									<input type="checkbox" name="twttr_disable" value="1" <?php if ( 1 == $twttr_options["disable"] ) echo "checked=\"checked\""; ?> />
									<span class="bws_info"> <?php _e( 'The button "T" will not be displayed. Just the shortcode &lsqb;follow_me&rsqb; will work.', 'twitter-plugin' ); ?></span><br />
								</td>
							</tr>
							<tr>
								<th>
									<?php _e( 'The "Twitter" icon position', 'twitter-plugin' ); ?>
								</th>
								<td>
									<select name="twttr_position">
										<option value="before" <?php if ( 'before' == $twttr_options['position'] ) echo 'selected="selected"';?>><?php _e( 'Before', 'twitter-plugin' ); ?></option>
										<option value="after" <?php if ( 'after' == $twttr_options['position'] ) echo 'selected="selected"';?>><?php _e( 'After', 'twitter-plugin' ); ?></option>
										<option value="after_and_before" <?php if ( 'after_and_before' == $twttr_options['position'] ) echo 'selected="selected"';?>><?php _e( 'Before And After', 'twitter-plugin' ); ?></option>
									</select>
									<span class="bws_info"><?php _e( 'By clicking this icon a user can add the article he/she likes to his/her Twitter page.', 'twitter-plugin' ); ?></span><br />
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<input type="hidden" name="twttr_form_submit" value="submit" />
									<input id="bws-submit-button" type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'twitter-plugin' ) ?>" />
								</td>
							</tr>
						</table>
						<?php wp_nonce_field( $plugin_basename, 'twttr_nonce_name' ); ?>
					</form>
						<?php }
}

/* Function to creates shortcode [follow_me] */
if ( ! function_exists( 'twttr_follow_me' ) ) {
	function twttr_follow_me() {
		global $twttr_options;
		if ( 'standart' == $twttr_options[ 'display_option' ] ) {
			return '<div class="twttr_follow">
						<a href="https://twitter.com/' . $twttr_options["url_twitter"] . '" class="twitter-follow-button" data-show-count="true">Follow me</a>
						<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
					</div>';
		} else {
			return '<div class="twttr_follow"><a href="http://twitter.com/' . $twttr_options["url_twitter"] . '" target="_blank" title="Follow me">
						<img src="' . $twttr_options['img_link'] . '" alt="Follow me" />
					</a></div>';
		}
	}
}

/* Positioning in the page	*/
if ( ! function_exists( 'twttr_twit' ) ) {
	function twttr_twit( $content ) {
		global $post, $twttr_options;
		$permalink_post	=	get_permalink( $post->ID );
		$title_post		=	htmlspecialchars( urlencode( $post->post_title ) );
		if ( $title_post == 'your-post-page-title' )
			return $content;
		if ( 0 == $twttr_options['disable'] ) {
			$str = '<div class="twttr_button">
						<a href="http://twitter.com/share?url=' . $permalink_post . '&text=' . $title_post . '" target="_blank" title="' . __( 'Click here if you like this article.', 'twitter-plugin' ) . '">
							<img src="' . plugins_url( 'images/twitt.gif', __FILE__ ) . '" alt="Twitt" />
						</a>
					</div>';
			if ( 'before' == $twttr_options['position'] ) {
				return $str . $content;
			} elseif ( 'after' == $twttr_options['position'] ) {
				return $content . $str;
			} else {
				return $str . $content . $str;
			}
		} else {
			return $content;
		}
	}
}

/* Registering and apllying styles and scripts */
if ( ! function_exists( 'twttr_wp_head' ) ) {
	function twttr_wp_head() {
		wp_enqueue_style( 'twttr_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );
	}
}

/* add shortcode content  */
if ( ! function_exists( 'twttr_shortcode_button_content' ) ) {
	function twttr_shortcode_button_content( $content ) {
		global $wp_version, $post; ?>
		<div id="twttr" style="display:none;">
			<fieldset>
				<?php _e( 'Insert the shortcode to use the "Follow Me" button.', 'twitter-plugin' ); ?>
			</fieldset>
			<input class="bws_default_shortcode" type="hidden" name="default" value="[follow_me]" />
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
/* Adding plugin buttons */
add_shortcode( 'follow_me', 'twttr_follow_me' );
add_filter( 'widget_text', 'do_shortcode' );
add_filter( 'the_content', "twttr_twit" );
/* custom filter for bws button in tinyMCE */
add_filter( 'bws_shortcode_button_content', 'twttr_shortcode_button_content' );