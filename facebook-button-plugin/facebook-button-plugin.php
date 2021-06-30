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
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
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
		global $bws_plugin_info, $fcbkbttn_options, $fcbkbttn_plugin_info, $bws_shortcode_list, $pagenow;

		/* pls*/

		/* add Facebook to global $bws_shortcode_list */
		$bws_shortcode_list['fcbkbttn'] = array( 'name' => 'Like & Share' );
	}
}
/* end fcbkbttn_admin_init */

if ( ! function_exists( 'fcbkbttn_settings' ) ) {
	function fcbkbttn_settings() {
		global $fcbkbttn_options, $fcbkbttn_plugin_info;

		/* Install the option defaults */
		if ( ! get_option( 'fcbkbttn_options' ) ) {
			$options_default = fcbkbttn_get_options_default();
			add_option( 'fcbkbttn_options', $options_default );
		}

		/* Get options from the database */
		$fcbkbttn_options = get_option( 'fcbkbttn_options' );

		if ( ! isset( $fcbkbttn_options['plugin_option_version'] ) || $fcbkbttn_options['plugin_option_version'] != $fcbkbttn_plugin_info["Version"] ) {
			$fcbkbttn_options['hide_premium_options'] = array();
						$options_default = fcbkbttn_get_options_default();
			$fcbkbttn_options = array_merge( $options_default, $fcbkbttn_options );
			$fcbkbttn_options['plugin_option_version'] = $fcbkbttn_plugin_info["Version"];

			update_option( 'fcbkbttn_options', $fcbkbttn_options );
		}
	}
}

/*pls */
if ( ! function_exists( 'fcbkbttn_statistics_page' ) ) {
	function fcbkbttn_statistics_page() {
		global $fcbkbttn_plugin_info, $wp_version, $fcbkbttn_options;

		if ( empty( $fcbkbttn_options ) )
			fcbkbttn_settings();

		$bws_hide_premium_options_check = bws_hide_premium_options_check( $fcbkbttn_options ); ?>
		<div class="wrap">
			<h1><?php _e( 'Like & Share Statistics', 'facebook-button-plugin' ); ?></h1>
			<?php if ( ! $bws_hide_premium_options_check ) : ?>
			<div class="bws_pro_version_bloc">
				<div class="bws_pro_version_table_bloc">
					<div class="bws_table_bg" style="top: 0;"></div>
					<div class="tablenav top">
						<p class="search-box alignright">
							<label class="screen-reader-text" for="title-search-input"><?php _e( 'search', 'facebook-button-plugin' ); ?>:</label>
								<input type="search" id="title-search-input" name="s" value=""placeholder="Search"/>
								<input type="submit" id="search-submit" class="button" value="<?php _e( 'Search', 'facebook-button-plugin' ); ?>"  />
						</p>
						<div class="alignleft actions bulkactions">
							<div class="fcbkbttn-container-statistics-filter">
								<select name="fcbkbttn_statistics_filter" class="fcbkbttn-statistics-filter">
									<option value=""><?php _e( 'All types', 'facebook-button-plugin' ); ?></option>
								</select>
								<input type="submit" name="filter_action" id="statistics-filter-submit" class="button" value="Filter"  />
							</div>
				 		</div><!--.bulkactions-->
				 		<br><br>
						<div class='tablenav-pages'>
							<span class="displaying-num">22 <?php _e( 'items', 'facebook-button-plugin' ); ?></span>
							<span class='pagination-links'>
								<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&laquo;</span>
								<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&lsaquo;</span>
								<span class="paging-input">
									<label for="current-page-selector" class="screen-reader-text"><?php _e( 'Current Page', 'facebook-button-plugin' ); ?></label>
									<input class='current-page' id='current-page-selector' type='text' name='paged' value='1' size='1' aria-describedby='table-paging' />
									<span class='tablenav-paging-text'> <?php _e( 'of', 'facebook-button-plugin' ); ?> <span class='total-pages'>2</span></span>
								</span>
								<a class='next-page button' href='#'>
									<span class='screen-reader-text'><?php _e( 'Next page', 'facebook-button-plugin' ); ?></span>
									<span aria-hidden='true'>&rsaquo;</span>
								</a>
								<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&raquo;</span>
							</span>
						</div><!--.tablenav-pages-->
					</div><!--.tablenav.top-->
					<table class="wp-list-table widefat fixed striped table-view-list like-share_page_statistics">
						<thead>
							<tr>
								<th scope="col" id='title' class='manage-column column-title sortable asc'>
									<a href="#"><span><?php _e( 'Title', 'facebook-button-plugin' ); ?></span></a>
								</th>
								<th scope="col" id='type' class='manage-column column-type sortable asc'>
									<a href="#"><span><?php _e( 'Type', 'facebook-button-plugin' ); ?></span></a>
								</th>
								<th scope="col" id='count_like' class='manage-column column-count_like sortable asc'>
									<a href="#"><span><?php _e( 'Like Count', 'facebook-button-plugin' ); ?></span></a>
								</th>
								<th scope="col" id='count_share' class='manage-column column-count_share sortable asc'>
									<a href="#"><span><?php _e( 'Share Count', 'facebook-button-plugin' ); ?></span></a>
								</th>
								<th scope="col" id='count_profile_url' class='manage-column column-count_profile_url sortable asc'>
									<a href="#"><span><?php _e( 'Profile URL Count', 'facebook-button-plugin' ); ?></span></a>
								</th>
							</tr>
						</thead>
						<tbody id="the-list">
							<tr>
								<td class="fcbkbttn_title">
									<a href="#"><?php _e( 'Hello world', 'facebook-button-plugin' ); ?>!</a>
								</td>
								<td class="fcbkbttn_type"><?php _e( 'post', 'facebook-button-plugin' ); ?></td>
								<td class="fcbkbttn_count_like">15</td>
								<td class="fcbkbttn_count_share">-</td>
								<td class="fcbkbttn_count_profile_url">48</td>
							</tr>
							<tr>
								<td class="fcbkbttn_title">
									<a href="#"><?php _e( 'Post Example', 'facebook-button-plugin' ); ?>1</a>
								</td>
								<td class="fcbkbttn_type"><?php _e( 'post', 'facebook-button-plugin' ); ?></td>
								<td class="fcbkbttn_count_like">345</td>
								<td class="fcbkbttn_count_share">58</td>
								<td class="fcbkbttn_count_profile_url">66</td>
							</tr>
							<tr>
								<td class="fcbkbttn_title">
									<a href="#"><?php _e( 'Post Example', 'facebook-button-plugin' ); ?>2</a>
								</td>
								<td class="fcbkbttn_type"><?php _e( 'post', 'facebook-button-plugin' ); ?></td>
								<td class="fcbkbttn_count_like">180</td>
								<td class="fcbkbttn_count_share">37</td>
								<td class="fcbkbttn_count_profile_url">18</td>
							</tr>
							<tr>
								<td class="fcbkbttn_title">
									<a href="#"><?php _e( 'Post of Custom Type Example', 'facebook-button-plugin' ); ?>1</a>
								</td>
								<td class="fcbkbttn_type">bws-gallery</td>
								<td class="fcbkbttn_count_like">175</td>
								<td class="fcbkbttn_count_share">165</td>
								<td class="fcbkbttn_count_profile_url">200</td>
							</tr>
							<tr>
								<td class="fcbkbttn_title">
									<a href="#"><?php _e( 'Sample Page', 'facebook-button-plugin' ); ?></a>
								</td>
								<td class="fcbkbttn_type"><?php _e( 'page', 'facebook-button-plugin' ); ?></td>
								<td class="fcbkbttn_count_like">18</td>
								<td class="fcbkbttn_count_share">2</td>
								<td class="fcbkbttn_count_profile_url">-</td>
							</tr>
							<tr>
								<td class="fcbkbttn_title">
									<a href="#"><?php _e( 'Page Example', 'facebook-button-plugin' ); ?>1</a>
								</td>
								<td class="fcbkbttn_type"><?php _e( 'page', 'facebook-button-plugin' ); ?></td>
								<td class="fcbkbttn_count_like">-</td>
								<td class="fcbkbttn_count_share">-</td>
								<td class="fcbkbttn_count_profile_url">-</td>
							</tr>
							<tr>
								<td class="fcbkbttn_title">
									<a href="#"><?php _e( 'Page Example', 'facebook-button-plugin' ); ?>2</a>
								</td>
								<td class="fcbkbttn_type"><?php _e( 'page', 'facebook-button-plugin' ); ?></td>
								<td class="fcbkbttn_count_like">-</td>
								<td class="fcbkbttn_count_share">-</td>
								<td class="fcbkbttn_count_profile_url">6</td>
							</tr>
							<tr>
								<td class="fcbkbttn_title">
									<a href="#"><?php _e( 'Post Example', 'facebook-button-plugin' ); ?>3</a>
								</td>
								<td class="fcbkbttn_type"><?php _e( 'post', 'facebook-button-plugin' ); ?></td>
								<td class="fcbkbttn_count_like">488</td>
								<td class="fcbkbttn_count_share">150</td>
								<td class="fcbkbttn_count_profile_url">373</td>
							</tr>
							<tr>
								<td class="fcbkbttn_title">
									<a href="#"><?php _e( 'Post of Custom Type Example', 'facebook-button-plugin' ); ?>2</a>
								</td>
								<td class="fcbkbttn_type">bws-gallery</td>
								<td class="fcbkbttn_count_like">15</td>
								<td class="fcbkbttn_count_share">2</td>
								<td class="fcbkbttn_count_profile_url">13</td>
							</tr>
							<tr>
								<td class="fcbkbttn_title">
									<a href="#"><?php _e( 'Post Example', 'facebook-button-plugin' ); ?>4</a>
								</td>
								<td class="fcbkbttn_type"><?php _e( 'post', 'facebook-button-plugin' ); ?></td>
								<td class="fcbkbttn_count_like">555</td>
								<td class="fcbkbttn_count_share">117</td>
								<td class="fcbkbttn_count_profile_url">200</td>
							</tr>
														<tr>
								<td class="fcbkbttn_title">
									<a href="#"><?php _e( 'Post Example', 'facebook-button-plugin' ); ?>5</a>
								</td>
								<td class="fcbkbttn_type"><?php _e( 'post', 'facebook-button-plugin' ); ?></td>
								<td class="fcbkbttn_count_like">180</td>
								<td class="fcbkbttn_count_share">37</td>
								<td class="fcbkbttn_count_profile_url">18</td>
							</tr>
							<tr>
								<td class="fcbkbttn_title">
									<a href="#"><?php _e( 'Post of Custom Type Example', 'facebook-button-plugin' ); ?>3</a>
								</td>
								<td class="fcbkbttn_type">bws-gallery</td>
								<td class="fcbkbttn_count_like">175</td>
								<td class="fcbkbttn_count_share">165</td>
								<td class="fcbkbttn_count_profile_url">200</td>
							</tr>
							<tr>
								<td class="fcbkbttn_title">
									<a href="#"><?php _e( 'Sample Page', 'facebook-button-plugin' ); ?></a>
								</td>
								<td class="fcbkbttn_type"><?php _e( 'page', 'facebook-button-plugin' ); ?></td>
								<td class="fcbkbttn_count_like">18</td>
								<td class="fcbkbttn_count_share">2</td>
								<td class="fcbkbttn_count_profile_url">-</td>
							</tr>
							<tr>
								<td class="fcbkbttn_title">
									<a href="#"><?php _e( 'Page Example', 'facebook-button-plugin' ); ?>3</a>
								</td>
								<td class="fcbkbttn_type"><?php _e( 'page', 'facebook-button-plugin' ); ?></td>
								<td class="fcbkbttn_count_like">-</td>
								<td class="fcbkbttn_count_share">-</td>
								<td class="fcbkbttn_count_profile_url">-</td>
							</tr>
							<tr>
								<td class="fcbkbttn_title">
									<a href="#"><?php _e( 'Page Example', 'facebook-button-plugin' ); ?>4</a>
								</td>
								<td class="fcbkbttn_type"><?php _e( 'page', 'facebook-button-plugin' ); ?></td>
								<td class="fcbkbttn_count_like">-</td>
								<td class="fcbkbttn_count_share">-</td>
								<td class="fcbkbttn_count_profile_url">6</td>
							</tr>
							<tr>
								<td class="fcbkbttn_title">
									<a href="#"><?php _e( 'Post Example', 'facebook-button-plugin' ); ?>6</a>
								</td>
								<td class="fcbkbttn_type"><?php _e( 'post', 'facebook-button-plugin' ); ?></td>
								<td class="fcbkbttn_count_like">488</td>
								<td class="fcbkbttn_count_share">150</td>
								<td class="fcbkbttn_count_profile_url">373</td>
							</tr>
							<tr>
								<td class="fcbkbttn_title">
									<a href="#"><?php _e( 'Post of Custom Type Example', 'facebook-button-plugin' ); ?>4</a>
								</td>
								<td class="fcbkbttn_type">bws-gallery</td>
								<td class="fcbkbttn_count_like">160</td>
								<td class="fcbkbttn_count_share">29</td>
								<td class="fcbkbttn_count_profile_url">33</td>
							</tr>
							<tr>
								<td class="fcbkbttn_title">
									<a href="#"><?php _e( 'Post Example', 'facebook-button-plugin' ); ?>7</a>
								</td>
								<td class="fcbkbttn_type"><?php _e( 'post', 'facebook-button-plugin' ); ?></td>
								<td class="fcbkbttn_count_like">545</td>
								<td class="fcbkbttn_count_share">227</td>
								<td class="fcbkbttn_count_profile_url">210</td>
							</tr>
														<tr>
								<td class="fcbkbttn_title">
									<a href="#"><?php _e( 'Post of Custom Type Example', 'facebook-button-plugin' ); ?>5</a>
								</td>
								<td class="fcbkbttn_type">bws-gallery</td>
								<td class="fcbkbttn_count_like">17</td>
								<td class="fcbkbttn_count_share">2</td>
								<td class="fcbkbttn_count_profile_url">15</td>
							</tr>
							<tr>
								<td class="fcbkbttn_title">
									<a href="#"><?php _e( 'Post Example', 'facebook-button-plugin' ); ?>8</a>
								</td>
								<td class="fcbkbttn_type"><?php _e( 'post', 'facebook-button-plugin' ); ?></td>
								<td class="fcbkbttn_count_like">535</td>
								<td class="fcbkbttn_count_share">127</td>
								<td class="fcbkbttn_count_profile_url">160</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<th scope="col"  class='manage-column column-title sortable asc'>
									<a href="#"><span><?php _e( 'Title', 'facebook-button-plugin' ); ?></span></a>
								</th>
								<th scope="col"  class='manage-column column-type sortable asc'>
									<a href="#"><span><?php _e( 'Type', 'facebook-button-plugin' ); ?></span></a>
								</th>
								<th scope="col"  class='manage-column column-count_like sortable asc'>
									<a href="#"><span><?php _e( 'Like Count', 'facebook-button-plugin' ); ?></span></a>
								</th>
								<th scope="col"  class='manage-column column-count_share sortable asc'>
									<a href="#"><span><?php _e( 'Share Count', 'facebook-button-plugin' ); ?></span></a>
								</th>
								<th scope="col"  class='manage-column column-count_profile_url sortable asc'>
									<a href="#"><span><?php _e( 'Profile URL Count', 'facebook-button-plugin' ); ?></span></a>
								</th>
							</tr>
						</tfoot>
					</table><!--.wp-list-table-->
					<div class="tablenav bottom">
						<div class="alignleft actions bulkactions"></div>
						<div class='tablenav-pages'>
							<span class="displaying-num">22 <?php _e( 'items', 'facebook-button-plugin' ); ?></span>
							<span class='pagination-links'>
								<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&laquo;</span>
								<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&lsaquo;</span>
								<span class="screen-reader-text"><?php _e( 'Current Page', 'facebook-button-plugin' ); ?></span>
								<span id="table-paging" class="paging-input">
									<span class="tablenav-paging-text">1 <?php _e( 'of', 'facebook-button-plugin' ); ?>
										<span class='total-pages'>2</span>
									</span>
								</span>
								<a class='next-page button' href='#'>
									<span class='screen-reader-text'><?php _e( 'Next page', 'facebook-button-plugin' ); ?></span>
									<span aria-hidden='true'>&rsaquo;</span>
								</a>
								<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&raquo;</span>
							</span>
						</div>
						<br class="clear" />
					</div><!--.tablenav-->
				</div>
				<div class="bws_pro_version_tooltip">
	                <a class="bws_button" href="https://bestwebsoft.com/products/wordpress/plugins/facebook-like-button/?k=427287ceae749cbd015b4bba6041c4b8&amp;pn=78&amp;v=<?php echo $fcbkbttn_plugin_info["Version"]; ?>&amp;wp_v=<?php echo $wp_version; ?>" target="_blank" title="Like & Share Pro"><?php _e( 'Upgrade to Pro', 'facebook-button-plugin' ); ?></a>
					<div class="clear"></div>
	            </div>
			</div>
			<?php else : ?>
			<p>
				<?php _e( 'This tab contains Pro options only.', 'facebook-button-plugin' );
				echo ' ' . sprintf(
					__( '%sChange the settings%s to view the Pro options.', 'facebook-button-plugin' ),
					'<a href="admin.php?page=facebook-button-plugin.php&bws_active_tab=misc">',
					'</a>'
					); ?>
			</p>
			<?php endif; ?>
		</div>
	<?php }
}
/* pls*/

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
			'fb_img_link'				=>	'',
			'locale'					=>	'en_US',
			'html5'						=>	0,
			'use_multilanguage_locale'	=>	0,
			'display_for_excerpt'		=>	0,
			'display_for_open_graph'	=>	1,
			'location'					=>	'left',
			'id'						=>	1443946719181573
		);
		return $options_default;
	}
}

/* Generate content for the buttons. */
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

		if ( 'left' == $fcbkbttn_options['location'] ) {
			$button = '<div class="fcbkbttn_buttons_block" id="fcbkbttn_left">';
		} elseif ( 'middle' == $fcbkbttn_options['location'] ) {
			$button = '<div class="fcbkbttn_buttons_block" id="fcbkbttn_middle">';
		} else {
			$button = '<div class="fcbkbttn_buttons_block" id="fcbkbttn_right">';
		}
        if ( ! empty( $fcbkbttn_options['my_page'] ) ) {
	        if ( 'standard' == $fcbkbttn_options['display_option'] || empty( $fcbkbttn_options['fb_img_link'] ) ) {
		        $img_name = 'large' == $fcbkbttn_options['size'] ? 'large-facebook-ico' : 'standard-facebook-ico';
		        $fcbkbttn_img = plugins_url( 'facebook-button-plugin/images/' . $img_name . '.png', dirname( __FILE__ ) );
	        } else {
		        $fcbkbttn_img = wp_get_attachment_url( $fcbkbttn_options['fb_img_link'] );
	        }

            $button .= '<div class="fcbkbttn_button">
                            <a href="https://www.facebook.com/' . $fcbkbttn_options['link'] . '" target="_blank">
                                <img src="' . $fcbkbttn_img . '" alt="Fb-Button" />
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

if ( ! function_exists( 'fcbkbttn_function_display_arhive' ) ) {
	function fcbkbttn_function_display_arhive()
	{
		global $post, $fcbkbttn_options,$wp;
		if ( is_archive( ) ) {
			$if_large = '';
			if ( $fcbkbttn_options['size'] == 'large' ) {
				$if_large = 'fcbkbttn_large_button';
			}
			if ( 'left' == $fcbkbttn_options['location'] ) {
				$button = '<div class="fcbkbttn_buttons_block fcbkbttn_arhiv" id="fcbkbttn_left">';
			} elseif ( 'middle' == $fcbkbttn_options['location'] ) {
				$button = '<div class="fcbkbttn_buttons_block fcbkbttn_arhiv" id="fcbkbttn_middle">';
			} else {
				$button = '<div class="fcbkbttn_buttons_block fcbkbttn_arhiv" id="fcbkbttn_right">';
			}
			if ( ! empty( $fcbkbttn_options['my_page'] ) ) {
				if ( 'standard' == $fcbkbttn_options['display_option'] || empty( $fcbkbttn_options['fb_img_link'] ) ) {
					$img_name = 'large' == $fcbkbttn_options['size'] ? 'large-facebook-ico' : 'standard-facebook-ico';
					$fcbkbttn_img = plugins_url( 'facebook-button-plugin/images/' . $img_name . '.png', dirname( __FILE__ ) );
				} else {
					$fcbkbttn_img = wp_get_attachment_url( $fcbkbttn_options['fb_img_link'] );
				}

				$button .= '<div class="fcbkbttn_button">
                            <a href="https://www.facebook.com/' . $fcbkbttn_options['link'] . '" target="_blank">
                                <img src="' . $fcbkbttn_img . '" alt="Fb-Button" />
                            </a>
                        </div>';
			}

			$permalink_page = home_url( $wp->request );
			$permalink = explode( "/page/", $permalink_page );
			$permalink_page = $permalink[0];


			$location_share = ( 'right' == $fcbkbttn_options['location'] && "standard" == $fcbkbttn_options['layout_like_option'] ) ? 1 : 0;
			if ( ! empty( $fcbkbttn_options['share'] ) && !empty( $location_share ) ) {
				$button .= '<div class="fb-share-button ' . $if_large . ' " data-href="' . $permalink_page . '" data-type="' . $fcbkbttn_options['layout_share_option'] . '" data-size="' . $fcbkbttn_options['size'] . '"></div>';
			}

			if ( ! empty( $fcbkbttn_options['like'] ) ) {
				$button .= '<div class="fcbkbttn_like ' . $if_large . '">';

				if ( ! empty( $fcbkbttn_options['html5'] ) ) {
					$button .= '<div class="fb-like fb-like-' . $fcbkbttn_options['layout_like_option'] . '" data-href="' . $permalink_page . '" data-colorscheme="' . $fcbkbttn_options['color_scheme'] . '" data-layout="' . $fcbkbttn_options['layout_like_option'] . '" data-action="' . $fcbkbttn_options['like_action'] . '" ';
					if ( 'standard' == $fcbkbttn_options['layout_like_option'] ) {
						$button .= ' data-width="' . $fcbkbttn_options['width'] . 'px"';
						$button .= ( ! empty( $fcbkbttn_options['faces'] ) ) ? " data-show-faces='true'" : " data-show-faces='false'";
					}
					$button .= ' data-size="' . $fcbkbttn_options['size'] . '"';
					$button .= '></div></div>';
				} else {
					$button .= '<fb:like href="' . $permalink_page . '" action="' . $fcbkbttn_options['like_action'] . '" colorscheme="' . $fcbkbttn_options['color_scheme'] . '" layout="' . $fcbkbttn_options['layout_like_option'] . '" ';
					if ( 'standard' == $fcbkbttn_options['layout_like_option'] ) {
						$button .= ( ! empty( $fcbkbttn_options['faces'] ) ) ? "show-faces='true'" : "show-faces='false'";
						$button .= ' width="' . $fcbkbttn_options['width'] . 'px"';
					}

					$button .= ' size="' . $fcbkbttn_options['size'] . '"';
					$button .= '></fb:like></div>';
				}
			}


			if ( ! empty( $fcbkbttn_options['share'] ) && empty( $location_share ) ) {
				$button .= '<div class="fb-share-button ' . $if_large . ' " data-href="' . $permalink_page . '" data-type="' . $fcbkbttn_options['layout_share_option'] . '" data-size="' . $fcbkbttn_options['size'] . '"></div>';
			}
			$button .= '</div>';
			echo $button;
		}
	}
}

/* Function taking from array 'fcbkbttn_options' necessary information to create BestWebSoft Like & Share and reacting to your choise in plugin menu - points where it appears. */
if ( ! function_exists( 'fcbkbttn_display_button' ) ) {
	function fcbkbttn_display_button( $content ) {
	    global $post;
		if ( isset( $post ) ) {
            if ( is_feed() ) {
                return $content;
            }

            global $fcbkbttn_options;

            $button = apply_filters( 'fcbkbttn_button_in_the_content', fcbkbttn_button() );
            /* Indication where show BestWebSoft Like & Share buttons depending on selected item in admin page. */
            if ( ! empty( $fcbkbttn_options['where'] ) && in_array( 'before', $fcbkbttn_options['where'] ) ) {
                $content = $button . $content;
            }
            if ( ! empty( $fcbkbttn_options['where'] ) && in_array( 'after', $fcbkbttn_options['where'] ) ) {
                $content .= $button;
            }
		}
		return $content;
	}
}

/* Function 'fcbkbttn_shortcode' is used to create content for BestWebSoft Like & Share shortcode. */
if ( ! function_exists( 'fcbkbttn_shortcode' ) ) {
	function fcbkbttn_shortcode( $content ) {
		global $post, $fcbkbttn_options, $fcbkbttn_shortcode_add_script;

		if ( isset( $post->ID ) ) {
			$permalink_post	= get_permalink( $post->ID );
		}

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
				<?php _e( 'Add Like & Share buttons to your page or post', 'facebook-button-plugin' ); ?>
			</fieldset>
			<input class="bws_default_shortcode" type="hidden" name="default" value="[fb_button]" />
			<div class="clear"></div>
		</div>
	<?php }
}

/* Functions adds some right meta for Facebook */
if ( ! function_exists( 'fcbkbttn_meta' ) ) {
	function fcbkbttn_meta() {
		global $fcbkbttn_options, $post, $wp;
		if ( ( ! empty( $fcbkbttn_options['like'] ) || ! empty( $fcbkbttn_options['share'] ) ) && 1 == $fcbkbttn_options['display_for_open_graph'] ) {
			if ( is_singular() ) {
				$image = '';
				if ( has_post_thumbnail( get_the_ID() ) ) {
					$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium' );
					$image = $image[0];
				}

				$description = ( has_excerpt() ) ? get_the_excerpt() : ( $post ? strip_tags( substr( $post->post_content, 0, 200 ) ) : '' );

				$url 			= apply_filters( 'fcbkbttn_meta_url', esc_attr( get_permalink() ) );
				$description	= apply_filters( 'fcbkbttn_meta_description', esc_attr( $description ) );
				$title 			= apply_filters( 'fcbkbttn_meta_title', esc_attr( get_the_title() ) );
				$site_name 		= apply_filters( 'fcbkbttn_meta_site_name', esc_attr( get_bloginfo() ) );
				$meta_image 	= apply_filters( 'fcbkbttn_meta_image', esc_url( $image ) );
				print "\n" . '<!-- fcbkbttn meta start -->';
				print "\n" . '<meta property="og:url" content="' . $url . '"/>';
				print "\n" . '<meta property="og:type" content="article"/>';
				print "\n" . '<meta property="og:title" content="' . $title . '"/>';
				print "\n" . '<meta property="og:site_name" content="' . $site_name . '"/>';
				if ( ! empty( $image ) ) {
					print "\n" . '<meta property="og:image" content="' . $meta_image . '"/>';
				} else {
					print "\n" . '<meta property="og:image" content=""/>';
				}
				if ( ! empty( $description ) ) {
					print "\n" . '<meta property="og:description" content="' . $description . '"/>';
				}
				print "\n" . '<!-- fcbkbttn meta end -->' . "\n";
			}
			if ( is_archive() ) {
				$permalink_page = home_url( $wp->request );
				$permalink = explode( "/page/", $permalink_page );
				$permalink_page = $permalink[0];
				$name = get_the_archive_title();
				$description = get_the_archive_description(); 
					
				$url 			= apply_filters( 'fcbkbttn_meta_url', esc_attr( $permalink_page ) );
				$description	= apply_filters( 'fcbkbttn_meta_description', esc_attr( $description ) );
				$title 			= apply_filters( 'fcbkbttn_meta_title', esc_attr( $name ) );
				$site_name 		= apply_filters( 'fcbkbttn_meta_site_name', esc_attr( get_bloginfo() ) );
				
				print "\n" . '<!-- fcbkbttn meta start -->';
				print "\n" . '<meta property="og:url" content="' . $url . '"/>';
				print "\n" . '<meta property="og:title" content="' . $title . '"/>';
				print "\n" . '<meta property="og:site_name" content="' . $site_name . '"/>';
				if ( ! empty( $description ) ) {
					print "\n" . '<meta property="og:description" content="' . $description . '"/>';
				}
				print "\n" . '<!-- fcbkbttn meta end -->' . "\n";
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
            <?php $locale = fcbkbttn_get_locale();
			$app_id = $fcbkbttn_options['id'];
			$fcbkbttn_sdk_script = "https://connect.facebook.net/{$locale}/sdk.js#xfbml=1&version=v6.0&appId={$app_id}&autoLogAppEvents=1";
			wp_register_script( 'fcbkbttn_sdk_script', htmlspecialchars_decode( $fcbkbttn_sdk_script ) );
			wp_enqueue_script( 'fcbkbttn_sdk_script' );
		}
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
        wp_enqueue_style( 'fcbkbttn_icon', plugins_url( 'css/icon.css', __FILE__ ) ) ;
        if ( isset( $_GET['page'] ) && ( "facebook-button-plugin.php" == $_GET['page'] || "social-buttons.php" == $_GET['page'] ) ) {
			bws_enqueue_settings_scripts();
			bws_plugins_include_codemirror();
			wp_enqueue_script( 'fcbkbttn_script', plugins_url( 'js/admin-script.js', __FILE__ ), array( 'jquery' ) );
            wp_enqueue_media();
	        wp_localize_script( 'fcbkbttn_script', 'fcbkbttn_var',
		        array(
			        'wp_media_title'    => __( 'Insert Media', 'facebook-button-plugin' ),
			        'wp_media_button'	=> __( 'Insert', 'facebook-button-plugin' )
		        )
	        );
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
add_action( 'loop_start' , 'fcbkbttn_function_display_arhive' );
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
