<?php
/**
 * Displays the content on the plugin settings page
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( ! class_exists( 'Mssngrs_Settings_Tabs' ) ) {
	/**
	 * Class Mssngrs_Settings_Tabs for MessengersSetting tab
	 */
	class Mssngrs_Settings_Tabs extends Bws_Settings_Tabs {
		/**
		 * Constructor.
		 *
		 * @access public
		 *
		 * @see Bws_Settings_Tabs::__construct() for more information on default arguments.
		 *
		 * @param string $plugin_basename Plugin basename.
		 */
		public function __construct( $plugin_basename ) {
			global $sclbttns_options, $sclbttns_plugin_info;

			if ( is_network_admin() ) {
				$tabs = array(
					'settings' => array( 'label' => __( 'Settings', 'social-buttons-pack' ) ),
				);
			} else {
				$tabs = array(
					'settings' => array( 'label' => __( 'Settings', 'social-buttons-pack' ) ),
				);
			}

			parent::__construct(
				array(
					'plugin_basename'    => $plugin_basename,
					'plugins_info'       => $sclbttns_plugin_info,
					'prefix'             => 'tlgrm',
					'default_options'    => sclbttns_get_option_defaults(),
					'options'            => $sclbttns_options,
					'is_network_options' => is_network_admin(),
					'tabs'               => $tabs,
					'wp_slug'            => 'social-buttons-pack',
				)
			);

			if ( $this->is_multisite && ! $this->is_network_options ) {
				$network_options = get_site_option( 'sclbttns_options' );
				if ( $network_options ) {
					if ( 'all' === $network_options['network_apply'] && 0 === $network_options['network_change'] ) {
						$this->change_permission_attr = ' readonly="readonly" disabled="disabled"';
					}
					if ( 'all' === $network_options['network_apply'] && 0 === $network_options['network_view'] ) {
						$this->forbid_view = true;
					}
				}
			}
		}

		/**
		 * Save plugin options to the database
		 *
		 * @access public
		 */
		public function save_options() {
		}

		/**
		 * Displays 'settings' menu-tab
		 *
		 * @access public
		 */
		public function tab_settings() {
			?>
			<h3 class="bws_tab_label"><?php esc_html_e( 'Messengers Settings', 'social-buttons-pack' ); ?></h3>
			<?php
			$this->help_phrase();
			?>
			<hr>
				<div class="bws_pro_version_bloc">
				<div class="bws_pro_version_table_bloc">
					<div class="bws_table_bg"></div>
					<div class="bws_tab_sub_label"><?php esc_html_e( 'General', 'social-buttons-pack' ); ?></div>
					<table class="form-table bws_pro_version">
						<tr>
							<th><?php esc_html_e( 'Buttons Size', 'social-buttons-pack' ); ?></th>
							<td>
								<fieldset>
									<label>
										<input type="radio" /> <?php esc_html_e( 'Small', 'social-buttons-pack' ); ?>
									</label><br />
									<label>
										<input type="radio" /> <?php esc_html_e( 'Large', 'social-buttons-pack' ); ?>
									</label>
								</fieldset>
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Buttons Position', 'social-buttons-pack' ); ?></th>
							<td>
								<fieldset>
									<label>
										<input type="checkbox" /> <?php esc_html_e( 'Before content', 'social-buttons-pack' ); ?>
									</label><br />
									<label>
										<input type="checkbox" /> <?php esc_html_e( 'After content', 'social-buttons-pack' ); ?>
									</label><br />
								</fieldset>
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Buttons Align', 'social-buttons-pack' ); ?></th>
							<td>
								<fieldset>
									<label>
										<input type="radio" /> <?php esc_html_e( 'Right', 'social-buttons-pack' ); ?>
									</label><br />
									<label>
										<input type="radio" /> <?php esc_html_e( 'Center', 'social-buttons-pack' ); ?>
									</label><br />
									<label>
										<input type="radio" /> <?php esc_html_e( 'Left', 'social-buttons-pack' ); ?>
									</label><br />
									<label>
								</fieldset>
							</td>
						</tr>
					</table>
					<div class="bws_tab_sub_label"><?php esc_html_e( 'Telegram Button', 'social-buttons-pack' ); ?></div>
					<table class="form-table bws_pro_version">
						<tr>
							<th scope="row"><?php esc_html_e( 'Telegram Account ', 'social-buttons-pack' ); ?></th>
							<td>
								<input type="text" />
								<div class="bws_info"><?php esc_html_e( 'Enter the ID in the format "@bestwebsoft" or the link in the format "https://t.me/bestwebsoft".', 'bws-pinterest-pro' ); ?></div>
							</td>
						</tr>
					</table>
					<div class="bws_tab_sub_label"><?php esc_html_e( 'Whatsapp Button', 'social-buttons-pack' ); ?></div>
					<table class="form-table bws_pro_version">
						<tr>
							<th scope="row"><?php esc_html_e( 'WhatsApp Account ', 'social-buttons-pack' ); ?></th>
							<td>
								<input type="text" pattern="^\+?[0-9]{1,15}$" />
								<div class="bws_info"><?php esc_html_e( 'Enter the phone number in the format: +1XXXXXXXXXXX .', 'bws-pinterest-pro' ); ?></div>

							</td>
						</tr>
					</table>
					<div class="bws_tab_sub_label"><?php esc_html_e( 'Youtube Button', 'social-buttons-pack' ); ?></div>
					<table class="form-table bws_pro_version">
						<tr>
							<th scope="row"><?php esc_html_e( 'Youtube Account ', 'social-buttons-pack' ); ?></th>
							<td>
								<input type="text" maxlength="250" />
								<div class="bws_info"><?php esc_html_e( 'Enter the ID in the format "@bestwebsoft", "bestwebsoft" or the link in the format "https://www.youtube.com/bestwebsoft".', 'bws-pinterest-pro' ); ?></div>
							</td>
						</tr>
					</table>
					</table>
				</div>
				<?php $this->bws_pro_block_links(); ?>
			</div>
			<?php
		}
	}
}
