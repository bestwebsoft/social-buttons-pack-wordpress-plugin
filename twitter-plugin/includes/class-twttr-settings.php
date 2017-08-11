<?php
/**
 * Displays the content on the plugin settings page
 * @since 2.54
 */

if ( ! class_exists( 'Bws_Settings_Tabs' ) )
	require_once( dirname( dirname( __FILE__ ) ) . '/bws_menu/class-bws-settings.php' );

if ( ! class_exists( 'Twttr_Settings_Tabs' ) ) {
	class Twttr_Settings_Tabs extends Bws_Settings_Tabs {
		/**
		 * Constructor.
		 *
		 * @access public
		 *
		 * @see Bws_Settings_Tabs::__construct() for more information on default arguments.
		 *
		 * @param string $plugin_basename
		 */
		public function __construct( $plugin_basename ) {
			global $twttr_options, $twttr_plugin_info;

			$tabs = array(
				'settings' 		=> array( 'label' => __( 'Settings', 'twitter-plugin' ) ),
				/*pls */
				'display' 		=> array( 'label' => __( 'Display', 'twitter-plugin' ), 'is_pro' => 1 ),
				/* pls*/
				'misc' 			=> array( 'label' => __( 'Misc', 'twitter-plugin' ) ),
				'custom_code' 	=> array( 'label' => __( 'Custom Code', 'twitter-plugin' ) ),
				/*pls */
				'license'		=> array( 'label' => __( 'License Key', 'twitter-plugin' ) ),
				/* pls*/
			);

			parent::__construct( array(
				'plugin_basename' 	 => $plugin_basename,
				'plugins_info'		 => $twttr_plugin_info,
				'prefix' 			 => 'twttr',
				'default_options' 	 => twttr_get_options_default(),
				'options' 			 => $twttr_options,
				'is_network_options' => is_network_admin(),
				'tabs' 				 => $tabs,
				'doc_link'			=> 'https://docs.google.com/document/d/1Zy0GILpchmrvwxeMXxIpti12hTKR_wuobiIku0xKYAQ/',
				/*pls */
				'wp_slug'			 => 'twitter-plugin',
				'pro_page' 			 => 'admin.php?page=twitter-pro.php',
				'bws_license_plugin' => 'twitter-pro/twitter-pro.php',
				'link_key' 			 => 'a8417eabe3c9fb0c2c5bed79e76de43c',
				'link_pn' 			 => '76'
				/* pls*/
			) );

			add_action( get_parent_class( $this ) . '_additional_misc_options', array( $this, 'additional_misc_options' ) );
			add_action( get_parent_class( $this ) . '_display_metabox', array( $this, 'display_metabox' ) );
			/*pls */		}

		/**
		 * Save plugin options to the database
		 * @access public
		 * @param  void
		 * @return array    The action results
		 */
		public function save_options() {
			$this->options['url_twitter']				= stripslashes( esc_html( $_REQUEST['twttr_url_twitter'] ) );

			$this->options['position']					= array();

			if ( isset( $_REQUEST['twttr_position'] ) && is_array( $_REQUEST['twttr_position'] ) ) {
				foreach ( $_REQUEST['twttr_position'] as $value) {
					if ( in_array( $value, array( 'before', 'after' ) ) )
						$this->options['position'][] = $value;
				}
			}

			$this->options['tweet_display']				= isset( $_REQUEST['twttr_tweet_display'] ) ? 1 : 0;
			$this->options['size']						= stripslashes( esc_html( $_REQUEST['twttr_size'] ) );
			$this->options['lang_default']				= isset( $_REQUEST['twttr_lang_default'] ) ? 1 : 0;
			$this->options['lang'] 						= stripslashes( esc_html( $_REQUEST['twttr_lang'] ) );
			$this->options['tailoring']					= isset( $_REQUEST['twttr_tailoring'] ) ? 1 : 0;
			$this->options['url_of_twitter']			= stripslashes( esc_html( $_REQUEST['twttr_url_of_twitter'] ) );
			$this->options['text_option_twitter']		= stripslashes( esc_html( $_REQUEST['twttr_text_option_twitter'] ) );
			$this->options['text_twitter']				= stripslashes( esc_html( $_REQUEST['twttr_text_twitter'] ) );

			$this->options['via_twitter']				= stripslashes( esc_html( $_REQUEST['twttr_via_twitter'] ) );

			$this->options['followme_display']			= isset( $_REQUEST['twttr_followme_display'] ) ? 1 : 0;
			$this->options['username_display']			= isset( $_REQUEST['twttr_username_display'] ) ? 1 : 0;
			$this->options['followers_count_followme']	= isset( $_REQUEST['twttr_followers_count_followme'] ) ? 1 : 0;
			if ( isset( $_REQUEST['twttr_display_option'] ) )
				$this->options['display_option' ] 			= stripslashes( esc_html( $_REQUEST['twttr_display_option'] ) );
			$this->options['hashtag_display']			= isset( $_REQUEST['twttr_hashtag_display'] ) ? 1 : 0;
			$this->options['text_option_hashtag']		= stripslashes( esc_html( $_REQUEST['twttr_text_option_hashtag'] ) );
			$this->options['text_hashtag']				= stripslashes( esc_html( $_REQUEST['twttr_text_hashtag'] ) );
			$this->options['url_option_hashtag']		= stripslashes( esc_html( $_REQUEST['twttr_url_option_hashtag'] ) );
			$this->options['mention_display']			= isset( $_REQUEST['twttr_mention_display'] ) ? 1 : 0;
			
			$this->options['tweet_to_mention']			= stripslashes( esc_html( $_REQUEST['twttr_tweet_to_mention'] ) );
			/* '\w' can not be used due to php 5.2.4 have bugs with cirillic symbols in preg_ functions */
			$this->options['tweet_to_mention']			= preg_replace( '~[^\d_a-z]~', '', $this->options['tweet_to_mention'] );

			$this->options['text_option_mention']		= stripslashes( esc_html( $_REQUEST['twttr_text_option_mention'] ) );
			$this->options['text_mention']				= stripslashes( esc_html( $_REQUEST['twttr_text_mention'] ) );

			if ( 'custom' !=  $this->options['display_option'] ) {
				$img_name =
					'large' == $this->options['size'] ?
					'twitter-follow' :
					'twitter-follow-small';

				$this->options['img_link'] = plugins_url( 'images/' . $img_name . '.png', dirname( __FILE__ ) );
			}

			/* "reccomended users" fields */
			$related = array(
				'related_twitter',
				'related_hashtag',
				'related_mention'
			);

			/* fields with values which can be listed with "," separator */
			$multipleValues = array_merge(
				array( 'hashtag_twitter', 'hashtag' ),
				$related
			);

			foreach ( $multipleValues as $field ) {
				$value = stripslashes( esc_html( $_REQUEST[ 'twttr_' . $field ] ) );
				$value = preg_replace( '~\s+~', '', $value ); /* delete from fields all space symbols */

				$exploded_values = explode( ',', $value ); /* create an array of values */

				/* delete all punctuation symbols form values */
				foreach ( $exploded_values as $key => $value )
					$exploded_values[ $key ] = preg_replace( '~[[:punct:]]~u', '', $value );

				$exploded_values = array_filter( $exploded_values ); /* delete all empty elements */

				if ( ! empty( $exploded_values ) ) {
					if ( in_array( $field, $related ) ) /* 'related' fields could have 2 values max */
						$exploded_values = array_slice( $exploded_values, 0, 2); /* return only 2 first elements */

					$this->options[ $field ] = implode( ', ', $exploded_values );
				}
			}

			if ( isset( $_FILES['twttr_upload_file']['tmp_name'] ) && $_FILES['twttr_upload_file']['tmp_name'] != "" )
				$this->options['count_icon'] = $this->options['count_icon'] + 1;

			if ( 2 < $this->options['count_icon'] )
				$this->options['count_icon'] = 1;

			/* Form options */
			if ( isset( $_FILES['twttr_upload_file']['tmp_name'] ) && "" != $_FILES['twttr_upload_file']['tmp_name'] ) {
				if ( ! $this->upload_dir )
					$this->upload_dir = wp_upload_dir();

				if ( ! $this->upload_dir["error"] ) {
					$twttr_cstm_mg_folder = $this->upload_dir['basedir'] . '/twitter-logo';
					if ( ! is_dir( $twttr_cstm_mg_folder ) ) {
						wp_mkdir_p( $twttr_cstm_mg_folder, 0755 );
					}
				}
				$max_image_width 	= $max_image_height = 100;
				$max_image_size		= 32 * 1024;
				$valid_types 		= array( 'jpg', 'jpeg', 'png' );

				/* Checks is file download initiated by user */
				if ( isset( $_FILES['twttr_upload_file'] ) && 'custom' == $_REQUEST['twttr_display_option'] ) {
					/* Checking is allowed download file given parameters */
					if ( is_uploaded_file( $_FILES['twttr_upload_file']['tmp_name'] ) ) {
						$filename	= $_FILES['twttr_upload_file']['tmp_name'];
						$ext		= substr( $_FILES['twttr_upload_file']['name'], 1 + strrpos( $_FILES['twttr_upload_file']['name'], '.' ) );

						if ( filesize ( $filename ) > $max_image_size ) {
							$error = __( "Error: File size > 32K", 'twitter-plugin' );
						} elseif ( ! in_array( strtolower( $ext ), $valid_types ) ) {
							$error = __( "Error: Invalid file type", 'twitter-plugin' );
						} else {

							$size = GetImageSize( $filename );
							if ( ( $size ) && ( $size[0] <= $max_image_width ) && ( $size[1] <= $max_image_height ) ) {
								/* If file satisfies requirements, we will move them from temp to your plugin folder and rename to 'twitter_ico' */
								/* Construction to rename downloading file */
								$namefile		= 'twitter-follow' . $this->options['count_icon'] . '.' . $ext;
								$uploadfile		= $twttr_cstm_mg_folder . '/' . $namefile;

								if ( move_uploaded_file( $_FILES['twttr_upload_file']['tmp_name'], $uploadfile ) ) {
									if ( 'custom' == $this->options['display_option'] )
										$this->options['img_link']	= $this->upload_dir['baseurl'] . '/twitter-logo/twitter-follow' . $this->options['count_icon'] . '.' . $ext;
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

			$this->options = apply_filters( 'twttr_before_save_options', $this->options );
			update_option( 'twttr_options', $this->options );

			$message = __( 'Settings saved', 'twitter-plugin' );

			return compact( 'message', 'notice', 'error' );
		}

		/**
		 *
		 */
		public function tab_settings() {
			global $twttr_lang_codes, $wp_version;
			if ( ! $this->upload_dir )
				$this->upload_dir = wp_upload_dir(); ?>
			<h3 class="bws_tab_label"><?php _e( 'Twitter Button Settings', 'twitter-plugin' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<div class="bws_tab_sub_label twttr_general"><?php _e( 'General', 'twitter-plugin' ); ?></div>
			<table class="form-table twttr_settings_form">
				<tr>
					<th><?php _e( 'Buttons', 'twitter-plugin' ); ?></th>
					<td>
						<fieldset>
							<label><input name="twttr_tweet_display" type="checkbox" value="1" <?php checked( 1, $this->options['tweet_display'] ); ?> /> <?php _e( 'Tweet', 'twitter-plugin' ); ?></label>
							<br />
							<label><input name="twttr_followme_display" type="checkbox" value="1" <?php checked( 1, $this->options['followme_display'] ); ?> /> <?php _e( 'Follow', 'twitter-plugin' ); ?></label>
							<br />
							<label><input name="twttr_hashtag_display" type="checkbox" value="1" <?php checked( 1, $this->options['hashtag_display'] ); ?> /> <?php _e( 'Hashtag', 'twitter-plugin' ); ?></label>
							<br />
							<label><input name="twttr_mention_display" type="checkbox" value="1" <?php checked( 1, $this->options['mention_display'] ); ?> /> <?php _e( 'Mention', 'twitter-plugin' ); ?></label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( "Buttons Size", 'twitter-plugin' ); ?></th>
					<td>
						<fieldset>
							<label><input name="twttr_size" type="radio" value="default" <?php checked( 'default', $this->options['size'] ); ?> /> <?php _e( 'Small', 'twitter-plugin' ); ?></label><br />
							<label><input name="twttr_size" type="radio" value="large" <?php checked( 'large', $this->options['size'] ); ?> /> <?php _e( 'Large', 'twitter-plugin' ); ?></label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Buttons Position', 'twitter-plugin' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="checkbox" name="twttr_position[]" value="before" <?php if ( in_array( 'before', $this->options['position'] ) ) echo 'checked="checked"'; ?> />
								<?php _e( 'Before content', 'twitter-plugin' ); ?></option>
							</label>
							<br>
							<label>
								<input type="checkbox" name="twttr_position[]" value="after" <?php if ( in_array( 'after', $this->options['position'] ) ) echo 'checked="checked"'; ?> />
								<?php _e( 'After content', 'twitter-plugin' ); ?></option>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( "Language", 'twitter-plugin' ); ?></th>
					<td>
						<label><input id="twttr_lang_default" name="twttr_lang_default" type="checkbox" value="1" <?php checked( 1, $this->options['lang_default'] ); ?> /> <?php _e( 'Automatic', 'twitter-plugin' ); ?></label>
						<br />
						<select name="twttr_lang" id="twttr_lang_choose" <?php if ( 1 == $this->options['lang_default'] ) echo 'style="display:none"'; ?> >
							<option <?php selected( 'en', $this->options['lang'] ); ?> value="en">English</option>
							<option <?php selected( 'en-gb', $this->options['lang'] ); ?> value="en-gb">English UK</option>
							<option <?php selected( 'fr', $this->options['lang'] ); ?> value="fr">French - français</option>
							<option <?php selected( 'es', $this->options['lang'] ); ?> value="es">Spanish - Español</option>
							<option <?php selected( 'de', $this->options['lang'] ); ?> value="de">German - Deutsch</option>
							<option <?php selected( 'it', $this->options['lang'] ); ?> value="it">Italian - Italiano</option>
							<option <?php selected( 'ru', $this->options['lang'] ); ?> value="ru">Russian - Русский</option>
							<option <?php selected( 'ar', $this->options['lang'] ); ?> value="ar">Arabic - العربية</option>
							<option <?php selected( 'ja', $this->options['lang'] ); ?> value="ja">Japanese - 日本語</option>
							<option <?php selected( 'id', $this->options['lang'] ); ?> value="id">Indonesian - Bahasa Indonesia</option>
							<option <?php selected( 'pt', $this->options['lang'] ); ?> value="pt">Portuguese - Português</option>
							<option <?php selected( 'ko', $this->options['lang'] ); ?> value="ko">Korean - 한국어</option>
							<option <?php selected( 'tr', $this->options['lang'] ); ?> value="tr">Turkish - Türkçe</option>
							<option <?php selected( 'nl', $this->options['lang'] ); ?> value="nl">Dutch - Nederlands</option>
							<option <?php selected( 'fil', $this->options['lang'] ); ?> value="fil">Filipino - Filipino</option>
							<option <?php selected( 'msa', $this->options['lang'] ); ?> value="msa">Malay - Bahasa Melayu</option>
							<option <?php selected( 'zh-tw', $this->options['lang'] ); ?> value="zh-tw">Traditional Chinese - 繁體中文</option>
							<option <?php selected( 'zh-cn', $this->options['lang'] ); ?> value="zh-cn">"Simplified Chinese - 简体中文</option>
							<option <?php selected( 'hi', $this->options['lang'] ); ?> value="hi">Hindi - हिन्दी</option>
							<option <?php selected( 'no', $this->options['lang'] ); ?> value="no">Norwegian - Norsk</option>
							<option <?php selected( 'sv', $this->options['lang'] ); ?> value="sv">Swedish - Svenska</option>
							<option <?php selected( 'fi', $this->options['lang'] ); ?> value="fi">Finnish - Suomi</option>
							<option <?php selected( 'da', $this->options['lang'] ); ?> value="da">Danish - Dansk</option>
							<option <?php selected( 'pl', $this->options['lang'] ); ?> value="pl">Polish - Polski</option>
							<option <?php selected( 'hu', $this->options['lang'] ); ?> value="hu">Hungarian - Magyar</option>
							<option <?php selected( 'fa', $this->options['lang'] ); ?> value="fa">Farsi - فارسی</option>
							<option <?php selected( 'he', $this->options['lang'] ); ?> value="he">Hebrew - עִבְרִית</option>
							<option <?php selected( 'ur', $this->options['lang'] ); ?> value="ur">Urdu - اردو</option>
							<option <?php selected( 'th', $this->options['lang'] ); ?> value="th">Thai - ภาษาไทย</option>
						</select>
						<div class="bws_info"><?php _e( 'Select the default language for Twitter button(-s).', 'twitter-plugin' ); ?></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Twitter Tailoring', 'twitter-plugin' ); ?></th>
					<td>
						<input name="twttr_tailoring" type="checkbox" value="1" <?php checked( 1, $this->options['tailoring'] ); ?> />
						<span class="bws_info"><?php _e( 'Enable tailored suggestions from Twitter.', 'twitter-plugin' ); ?> <a href="https://support.twitter.com/articles/20169421#" target="_blank"><?php _e( 'Learn More', 'twitter-plugin' ); ?></a></span>
					</td>
				</tr>
				<?php do_action( 'twttr_settings_page_action', $this->options ); ?>
			</table>
			<div class="bws_tab_sub_label twttr_tweet_enabled"><?php _e( 'Tweet Button', 'twitter-plugin' ); ?></div>
			<table class="form-table twttr_settings_form twttr_tweet_enabled">
				<tr>
					<th><?php _e( 'Share URL', 'twitter-plugin' ); ?></th>
					<td>
						<fieldset>
							<label><input name="twttr_url_of_twitter" type="radio" value="page_url" <?php checked( 'page_url', $this->options['url_of_twitter'] ); ?> /> <?php _e( 'Current page', 'twitter-plugin' ); ?></label>
							<br />
							<label><input name="twttr_url_of_twitter" type="radio" value="home_url" <?php checked( 'home_url', $this->options['url_of_twitter'] ); ?> /> <?php _e( 'Home page', 'twitter-plugin' ); ?></label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th>
						<?php _e( 'Tweet Text', 'twitter-plugin' ); ?>
					</th>
					<td>
						<fieldset>
							<label><input name="twttr_text_option_twitter" type="radio" value="page_title" <?php checked( 'page_title', $this->options['text_option_twitter'] ); ?> /> <?php _e( 'Current page title', 'twitter-plugin' ); ?></label>
							<br />
							<label><input name="twttr_text_option_twitter" type="radio" value="custom" <?php checked( 'custom', $this->options['text_option_twitter'] ); ?> /> <?php _e( 'Custom', 'twitter-plugin' ); ?></label>
							<br />
							<textarea class="twttr_custom_input" name="twttr_text_twitter"><?php echo $this->options['text_twitter']; ?></textarea>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( "Via", 'twitter-plugin' ); ?></th>
					<td>
						<input name="twttr_via_twitter" type="text" value="<?php echo $this->options['via_twitter'] ?>" maxlength="250" />
						<div class="bws_info"><?php _e( 'Enter username for Tweet via parameter.', 'twitter-plugin' ); ?></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Recommend', 'twitter-plugin' ); ?></th>
					<td>
						<input name="twttr_related_twitter" type="text" value="<?php echo $this->options['related_twitter'] ?>" maxlength="250" />
						<div class="bws_info"> <?php _e( 'Enter usernames of someone you recommend (maximum 2 allowed). For example: bestwebsoft, wordpress.', 'twitter-plugin' ); ?></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Hashtag', 'twitter-plugin' ); ?></th>
					<td>
						<input name="twttr_hashtag_twitter" type="text" value="<?php echo $this->options['hashtag_twitter'] ?>" maxlength="512" />
						<div class="bws_info"> <?php _e( 'Enter one or multiple hashtags for your tweet. For example: bestwebsoft, wordpress, etc.', 'twitter-plugin' ); ?></div>
					</td>
				</tr>
			</table>
			<div class="bws_tab_sub_label twttr_follow_enabled"><?php _e( 'Follow Button', 'twitter-plugin' ); ?></div>
			<table class="form-table twttr_settings_form twttr_follow_enabled">
				<tr>
					<th><?php _e( "Twitter username", 'twitter-plugin' ); ?></th>
					<td>
						<input name="twttr_url_twitter" type="text" value="<?php echo $this->options['url_twitter'] ?>" maxlength="19" />
						<div class="bws_info"><?php printf( __( 'Enter your Twitter account username or %s create a new one %s.', 'twitter-plugin' ), '<a target="_blank" href="https://twitter.com/signup">', '</a>' ); ?></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Follow Button Image', 'twitter-plugin' ); ?></th>
					<td>
						<?php if ( scandir( $this->upload_dir['basedir'] ) && is_writable( $this->upload_dir['basedir'] ) ) { ?>
							<fieldset>
								<label><input name="twttr_display_option" type="radio" value="standart" <?php checked( 'standart', $this->options['display_option'] ); ?> /> <?php _e( 'Default', 'twitter-plugin' ); ?></label>
								<br />
								<label><input name="twttr_display_option" type="radio" value="custom" <?php checked( 'custom', $this->options['display_option'] ); ?> /> <?php _e( 'Custom', 'twitter-plugin' ); ?></label>
							</fieldset>
						<?php } else {
							$no_upload_permission = true;
							printf( __( "To use custom image, You need to setup permissions to upload directory of your site - %s", 'twitter-plugin' ), $this->upload_dir['basedir'] );
						} ?>
					</td>
				</tr>
					<?php if ( ! isset( $no_upload_permission ) ) { ?>
						<tr class="twttr_display_option_custom">
							<th></th>
							<td>
								<?php _e( "Current image", 'twitter-plugin' ); ?>: <img src="<?php echo $this->options['img_link']; ?>" />
								<input type="hidden" name="twttr_img_link" value="<?php echo $this->options['img_link']; ?>" />
							</td>
						</tr>
						<tr class="twttr_display_option_custom">
							<th></th>
							<td>
								<input type="file" name="twttr_upload_file" />
								<div class="bws_info"><?php printf( __( 'Upload image with the %s dimensions, JPG, JPEG or PNG formats (maximum file size - %s).', 'twitter-plugin' ), '100×100px', '32kb' ); ?></div>
							</td>
						</tr>
					<?php } ?>
					<tr class="twttr_display_option_standart">
						<th><?php _e( 'Show Follow Button', 'twitter-plugin' ); ?></th>
						<td>
							<fieldset>
								<label><input name="twttr_username_display" type="checkbox" value="1" <?php checked( 1, $this->options['username_display'] ); ?> /> <?php _e( 'Username', 'twitter-plugin' ); ?></label>
								<br/>
								<label><input name="twttr_followers_count_followme" type="checkbox" value="1" <?php checked( 1, $this->options['followers_count_followme'] ); ?> /> <?php _e( 'Count', 'twitter-plugin' ); ?></label>
							</fieldset>
						</td>
					</tr>
				</table>
				<div class="bws_tab_sub_label twttr_hashtag_enabled"><?php _e( 'Hashtag Button', 'twitter-plugin' ); ?></div>
				<table class="form-table twttr_settings_form twttr_hashtag_enabled">
					<tr>
						<th><?php _e( 'Hashtag', 'twitter-plugin' ); ?></th>
						<td>
							<input name="twttr_hashtag" type="text" value="<?php echo $this->options['hashtag'] ?>" maxlength="512" />
							<div class="bws_info"><?php _e( 'Enter one or multiple hashtags for your tweet. For example: bestwebsoft, wordpress, etc.', 'twitter-plugin' ); ?></div>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Share URL', 'twitter-plugin' ); ?></th>
						<td>
							<fieldset>
								<label><input name="twttr_url_option_hashtag" type="radio" value="no_url" <?php checked( 'no_url', $this->options['url_option_hashtag'] ); ?> /> <?php _e( 'None', 'twitter-plugin' ); ?></label>
								<br />
								<label><input name="twttr_url_option_hashtag" type="radio" value="page_url" <?php checked( 'page_url', $this->options['url_option_hashtag'] ); ?> /> <?php _e( 'Current page', 'twitter-plugin' ); ?></label>
								<br />
								<label><input name="twttr_url_option_hashtag" type="radio" value="home_url" <?php checked( 'home_url', $this->options['url_option_hashtag'] ); ?> /> <?php _e( 'Home page', 'twitter-plugin' ); ?></label>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Tweet Text', 'twitter-plugin' ); ?></th>
						<td>
						<fieldset>
								<label><input name="twttr_text_option_hashtag" type="radio" value="page_title" <?php checked( 'page_title', $this->options['text_option_hashtag'] ); ?> /> <?php _e( 'Current page title', 'twitter-plugin' ); ?></label>
								<br />
								<label> <input name="twttr_text_option_hashtag" type="radio" value="custom" <?php checked( 'custom', $this->options['text_option_hashtag'] ); ?> /> <?php _e( 'Custom', 'twitter-plugin' ); ?></label>
								<br />
								<textarea class="twttr_custom_input" name="twttr_text_hashtag"><?php echo $this->options['text_hashtag']; ?></textarea>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Recommend', 'twitter-plugin' ); ?></th>
						<td>
							<input name="twttr_related_hashtag" type="text" value="<?php echo $this->options['related_hashtag'] ?>" maxlength="512" />
							<div class="bws_info"> <?php _e( 'Enter usernames of someone you recommend (maximum 2 allowed). For example: bestwebsoft, wordpress.', 'twitter-plugin' ); ?></div>
						</td>
					</tr>
				</table>
				<div class="bws_tab_sub_label twttr_mention_enabled"><?php _e( 'Mention Button', 'twitter-plugin' ); ?></div>
				<table class="form-table twttr_settings_form twttr_mention_enabled">
					<tr>
						<th>
							<?php _e( 'Tweet to', 'twitter-plugin' ); ?>
						</th>
						<td>
							<input name="twttr_tweet_to_mention" type="text" value="<?php echo $this->options['tweet_to_mention'] ?>" maxlength="250" />
							<div class="bws_info"> <?php _e( 'Enter username of someone you want to mention.', 'twitter-plugin' ); ?> <?php printf( __( 'For example, %s.', 'twitter-plugin' ), 'bestwebsoft' ); ?></div>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Tweet Text', 'twitter-plugin' ); ?></th>
						<td>
							<fieldset>
								<label><input name="twttr_text_option_mention" type="radio" value="page_title" <?php checked( 'page_title', $this->options['text_option_mention'] ); ?> /> <?php _e( 'Current page', 'twitter-plugin' ); ?></label>
								<br />
								<label><input name="twttr_text_option_mention" type="radio" value="custom" <?php checked( 'custom', $this->options['text_option_mention'] ); ?> /> <?php _e( 'Custom', 'twitter-plugin' ); ?></label>
								<br />
								<textarea class="twttr_custom_input" name="twttr_text_mention"><?php echo $this->options['text_mention']; ?></textarea>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Recommend', 'twitter-plugin' ); ?></th>
						<td>
							<input name="twttr_related_mention" type="text" value="<?php echo $this->options['related_mention'] ?>" maxlength="250" />
							<div class="bws_info"><?php _e( 'Enter usernames of someone you recommend (maximum 2 allowed). For example: bestwebsoft, wordpress.', 'twitter-plugin' ); ?></div>
						</td>
					</tr>
				</tr>
			</table>
		<?php }

		/**
		 * Display custom options on the 'misc' tab
		 * @access public
		 */
		public function additional_misc_options() {
			do_action( 'twttr_settings_page_misc_action', $this->options );
		}

		/**
		 * Display custom metabox
		 * @access public
		 * @param  void
		 * @return array    The action results
		 */
		public function display_metabox() { ?>
			<div class="postbox">
				<h3 class="hndle">
					<?php _e( 'Twitter Buttons Shortchode', 'twitter-plugin' ); ?>
				</h3>
				<div class="inside">
					<?php _e( "Add Twitter button(-s) to your posts, pages, custom post types or widgets by using the following shortcode:", 'twitter-plugin' ); ?>
					<?php bws_shortcode_output( '[twitter_buttons display=tweet,follow,hashtag,mention]' ); ?>
				</div>
			</div>
		<?php /*pls */ }

		public function display_second_postbox() {
			if ( ! $this->hide_pro_tabs ) { ?>
				<div class="postbox bws_pro_version_bloc">
					<div class="bws_table_bg"></div>
					<h3 class="hndle">
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'twitter-plugin' ); ?>"></button>
						<?php _e( 'Twitter Buttons Preview', 'twitter-plugin' ); ?>
					</h3>
					<div class="inside">
						<img src='<?php echo plugins_url( 'images/preview.png', dirname( __FILE__ ) ); ?>' />
					</div>
					<?php $this->bws_pro_block_links(); ?>
				</div>
			<?php }
		}

		/**
		 *
		 */
		public function tab_display() { ?>
			<h3 class="bws_tab_label"><?php _e( 'Display Settings', 'twitter-plugin' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<div class="bws_pro_version_bloc">
				<div class="bws_pro_version_table_bloc">
					<div class="bws_table_bg"></div>
					<table class="form-table bws_pro_version">
						<tr>
							<td colspan="2">
								<?php _e( 'Please choose the necessary post types (or single pages) where Twitter buttons will be displayed:', 'twitter-plugin' ); ?>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<label>
									<input disabled="disabled" checked="checked" type="checkbox" name="jstree_url" value="1" />
									<?php _e( "Show URL for pages", 'twitter-plugin' );?>
								</label>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<img src="<?php echo plugins_url( 'images/pro_screen_1.png', dirname( __FILE__ ) ); ?>" alt="<?php _e( "Example of the site's pages tree", 'twitter-plugin' ); ?>" title="<?php _e( "Example of site pages' tree", 'twitter-plugin' ); ?>" />
							</td>
						</tr>
					</table>
				</div>
				<?php $this->bws_pro_block_links(); ?>
			</div>
		<?php /* pls*/ }
	}
}