<?php
/**
 * Displays the content on the plugin settings page
 */

if ( ! class_exists( 'Sclbttns_Settings_Tabs' ) ) {
	class Sclbttns_Settings_Tabs extends Bws_Settings_Tabs {
		public $fcbkbttn_page, $twttr_page, $pntrst_page, $lnkdn_page, $nstgrm_page;
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
			global $sclbttns_options, $sclbttns_plugin_info;

			$tabs = array(
				'facebook' 		=> array( 'label' => 'Facebook' ),
				'twitter' 		=> array( 'label' => 'Twitter' ),
				'pinterest' 	=> array( 'label' => 'Pinterest' ),
				'linkedin' 		=> array( 'label' => 'LinkedIn' ),
				'instagram' 	=> array( 'label' => 'Instagram' ),
				'display' 		=> array( 'label' => __( 'Display', 'social-buttons-pack' ), 'is_pro' => 1 ),
				'misc' 			=> array( 'label' => __( 'Misc', 'social-buttons-pack' ) ),
				'custom_code' 	=> array( 'label' => __( 'Custom Code', 'social-buttons-pack' ) ),
				'license'		=> array( 'label' => __( 'License Key', 'social-buttons-pack' ) ),
			);

			parent::__construct( array(
				'plugin_basename' 	 => $plugin_basename,
				'plugins_info'		 => $sclbttns_plugin_info,
				'prefix' 			 => 'sclbttns',
				'default_options' 	 => sclbttns_get_option_defaults(),
				'options' 			 => $sclbttns_options,
				'is_network_options' => is_network_admin(),
				'tabs' 				 => $tabs,				
				'wp_slug'			 => 'social-buttons-pack',
				'link_key' 			 => 'c0d1b84b603c503e8a16cfa6252b2f70',
				'link_pn' 			 => '209'
			) );

			require_once( dirname( dirname( __FILE__ ) ) . '/facebook-button-plugin/includes/class-fcbkbttn-settings.php' );
			$this->fcbkbttn_page = new Fcbkbttn_Settings_Tabs( plugin_basename( __FILE__ ) );

			require_once( dirname( dirname( __FILE__ ) ) . '/twitter-plugin/includes/class-twttr-settings.php' );
			$this->twttr_page = new Twttr_Settings_Tabs( plugin_basename( __FILE__ ) );

			require_once( dirname( dirname( __FILE__ ) ) . '/bws-pinterest/includes/class-pntrst-settings.php' );
			$this->pntrst_page = new Pntrst_Settings_Tabs( plugin_basename( __FILE__ ) );

			require_once( dirname( dirname( __FILE__ ) ) . '/bws-linkedin/includes/class-lnkdn-settings.php' );
			$this->lnkdn_page = new Lnkdn_Settings_Tabs( plugin_basename( __FILE__ ) );

			require_once( dirname( dirname( __FILE__ ) ) . '/includes/sclbttns-nstgrm-settings.php' );
			$this->nstgrm_page = new Nstgrm_Settings_Tabs( plugin_basename( __FILE__ ) );
		}

		/**
		 * Save plugin options to the database
		 * @access public
		 * @param  void
		 * @return array    The action results
		 */
		public function save_options() {
			$message = $notice = $error = '';

			$socials = array(
				$this->fcbkbttn_page,	
				$this->twttr_page,	
				$this->pntrst_page,			
				$this->lnkdn_page,
				$this->nstgrm_page
			);

			foreach ( $socials as $current_class ) {
				$result = $current_class->save_options();
				if ( ! empty( $result['error'] ) )
					$error .= $result['error'] . '<br/>';
			}

			$this->options['instagram_options'] = $this->nstgrm_page->options['instagram_options'];
			update_option( 'sclbttns_options', $this->options );

			$message .= __( 'Settings saved.', 'social-buttons-pack' );

			return compact( 'message', 'notice', 'error' );
		}

		/**
		 *
		 */
		public function tab_facebook() {			
			$this->fcbkbttn_page->tab_settings();
		}

		public function tab_twitter() {			
			$this->twttr_page->tab_settings();
		}

		public function tab_pinterest() {			
			$this->pntrst_page->tab_settings();
		}

		public function tab_linkedin() {			
			$this->lnkdn_page->tab_settings();
		}

		public function tab_instagram() {			
			$this->nstgrm_page->tab_settings();
		}

		/**
		 *
		 */
		public function tab_display() { ?>
			<h3 class="bws_tab_label"><?php _e( 'Display Settings', 'social-buttons-pack' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<div class="bws_pro_version_bloc">
				<div class="bws_pro_version_table_bloc">
					<div class="bws_table_bg"></div>
					<table class="form-table bws_pro_version">
						<tr>
							<td colspan="2">
								<?php _e( 'Please choose the necessary post types (or single pages) where Social Suttons will be displayed:', 'social-buttons-pack' ); ?>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<label>
									<input disabled="disabled" checked="checked" type="checkbox" name="jstree_url" value="1" />
									<?php _e( "Show URL for pages", 'social-buttons-pack' );?>
								</label>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<img src="<?php echo plugins_url( 'images/pro_screen_1.png', dirname( __FILE__ ) ); ?>" alt="<?php _e( "Example of site pages tree", 'social-buttons-pack' ); ?>" title="<?php _e( "Example of site pages tree", 'social-buttons-pack' ); ?>" />
							</td>
						</tr>
					</table>
				</div>
				<?php $this->bws_pro_block_links(); ?>
			</div>
		<?php }

		/**
		 * Restore plugin options to defaults - Redefine default function 
		 */
		public function restore_options() {
			$socials = array(
				$this->fcbkbttn_page,	
				$this->twttr_page,		
				$this->pntrst_page,			
				$this->lnkdn_page
			);

			foreach ( $socials as $current_class ) {

				unset(
					$current_class->default_options['first_install'],
					$current_class->default_options['suggest_feature_banner'],
					$current_class->default_options['display_settings_notice']
				);
				$current_class->options = $current_class->default_options;

				update_option( $current_class->prefix . '_options', $current_class->options );
			}	

			/* Restore Instagram Options */
			$this->nstgrm_page->options = $this->nstgrm_page->default_options;
		}
	}
}