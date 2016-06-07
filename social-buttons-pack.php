<?php
/*
Plugin Name: Social Buttons Pack by BestWebSoft
Plugin URI: http://bestwebsoft.com/products/
Description: Add social buttons and widgets in to your site.
Author: BestWebSoft
Version: 1.0.7
Author URI: http://bestwebsoft.com/
License: GPLv3 or later
*/

/*  Copyright 2016  BestWebSoft  ( http://support.bestwebsoft.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* add buttons file */
require_once( dirname( __FILE__ ) . '/facebook-button-plugin/facebook-button-plugin.php' );
require_once( dirname( __FILE__ ) . '/twitter-plugin/twitter.php' );
require_once( dirname( __FILE__ ) . '/google-one/google-plus-one.php' );
require_once( dirname( __FILE__ ) . '/bws-linkedin/bws-linkedin.php' );
require_once( dirname( __FILE__ ) . '/bws-pinterest/bws-pinterest.php' );


if ( ! function_exists( 'sclbttns_add_pages' ) ) {
	function sclbttns_add_pages() {
		bws_general_menu();
		$settings = add_submenu_page( 'bws_plugins', __( 'Social Buttons Settings' ), 'Social Buttons', 'manage_options', "social-buttons.php", 'sclbttns_settings_page' );
		add_action( 'load-' . $settings, 'sclbttns_add_tabs' );
	}
}

if ( ! function_exists( 'sclbttns_init' ) ) {
    function sclbttns_init() {
        global $sclbttns_plugin_info;

        if ( empty( $sclbttns_plugin_info ) ) {
            if ( ! function_exists( 'get_plugin_data' ) )
                require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            $sclbttns_plugin_info = get_plugin_data( __FILE__ );
        }

        require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
        bws_include_init( plugin_basename( __FILE__ ) );

        bws_wp_min_version_check( plugin_basename( __FILE__ ), $sclbttns_plugin_info, '3.8' );
    }
}

if ( ! function_exists( 'sclbttns_admin_init' ) ) {
    function sclbttns_admin_init() {
        global $bws_plugin_info, $sclbttns_plugin_info;

        $deactivate_plugins = array( 
            'google-one/google-plus-one.php', 
            'twitter-plugin/twitter.php', 
            'facebook-button-plugin/facebook-button-plugin.php',
            'bws-pinterest/bws-pinterest.php',
            'bws-linkedin/bws-linkedin.php'
        );

        if ( function_exists( 'is_multisite' ) && is_multisite() ) {
            if ( ! is_plugin_active_for_network( plugin_basename( __FILE__ ) ) )
                $deactivate_not_for_all_network = true;
        }

        if ( isset( $deactivate_not_for_all_network ) && is_plugin_active_for_network( $free ) ) {
            global $wpdb;
            deactivate_plugins( $deactivate_plugins );

            $old_blog = $wpdb->blogid;
            /* Get all blog ids */
            $blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
            foreach ( $blogids as $blog_id ) {
                switch_to_blog( $blog_id );
                activate_plugin( $free );
            }
            switch_to_blog( $old_blog );
        } else {
            deactivate_plugins( $deactivate_plugins );
        }

        if ( empty( $bws_plugin_info ) )
            $bws_plugin_info = array( 'id' => '209', 'version' => $sclbttns_plugin_info["Version"] );
    }
}

if ( ! function_exists( 'sclbttns_settings_page' ) ) {
    function sclbttns_settings_page() { 
        global $sclbttns_plugin_info; 
        $plugin_basename = plugin_basename( __FILE__ ); 

        /* Add restore function */
        if ( isset( $_REQUEST['bws_restore_confirm'] ) && check_admin_referer( $plugin_basename, 'bws_settings_nonce_name' ) ) {
            global $gglplsn_option_defaults, $fcbkbttn_options_default, $twttr_options_default, $lnkdn_options_defaults, $pntrst_options_defaults,
                $gglplsn_options, $fcbkbttn_options, $twttr_options, $pntrst_options, $lnkdn_options;
            
            $gglplsn_options = $gglplsn_option_defaults;
            update_option( 'gglplsn_options', $gglplsn_options );
            $fcbkbttn_options = $fcbkbttn_options_default;
            update_option( 'fcbk_bttn_plgn_options', $fcbkbttn_options );
            $twttr_options = $twttr_options_default;
            update_option( 'twttr_options', $twttr_options );
            $pntrst_options = $pntrst_options_defaults;
            update_option( 'pntrst_options', $pntrst_options );
            $lnkdn_options = $lnkdn_options_defaults;
            update_option( 'lnkdn_options', $lnkdn_options );

            $message = __( 'All plugin settings were restored.' );
        } ?>
        <div class="wrap">
            <h1><?php _e( "Social Buttons Settings" ); ?></h1>
            <h2 class="nav-tab-wrapper">
                <a class="nav-tab<?php if ( ! isset( $_GET['action'] ) || ( isset( $_GET['action'] ) && 'facebook' == $_GET['action'] ) ) echo ' nav-tab-active'; ?>" href="admin.php?page=social-buttons.php&amp;action=facebook">Facebook</a>
                <a class="nav-tab<?php if ( isset( $_GET['action'] ) && 'twitter' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=social-buttons.php&amp;action=twitter">Twitter</a>
                <a class="nav-tab<?php if ( isset( $_GET['action'] ) && 'google-one' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=social-buttons.php&amp;action=google-one">Google+1</a>
                <a class="nav-tab<?php if ( isset( $_GET['action'] ) && 'pinterest' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=social-buttons.php&amp;action=pinterest">Pinterest</a>
                <a class="nav-tab<?php if ( isset( $_GET['action'] ) && 'linkedin' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=social-buttons.php&amp;action=linkedin">LinkedIn</a>
                <a class="nav-tab <?php if ( isset( $_GET['action'] ) && 'custom_code' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=social-buttons.php&amp;action=custom_code"><?php _e( 'Custom code' ); ?></a> 
            </h2>
            <?php if ( ! empty( $message ) ) { ?>
                <div class="updated fade below-h2"><p><strong><?php echo $message; ?></strong></p></div>
            <?php }
            if ( isset( $_REQUEST['bws_restore_default'] ) && check_admin_referer( $plugin_basename, 'bws_settings_nonce_name' ) ) {
                bws_form_restore_default_confirm( $plugin_basename );
            } else { 
                if ( ! isset( $_GET['action'] ) || ( isset( $_GET['action'] ) && 'facebook' == $_GET['action'] ) ) {
                    fcbkbttn_settings_page();
                } elseif ( 'twitter' == $_GET['action'] ) {
                    twttr_settings_page();
                } elseif ( 'google-one' == $_GET['action'] ) {
                    gglplsn_options();
                } elseif ( 'pinterest' == $_GET['action'] ) {
                    pntrst_settings_page();
                } elseif ( 'linkedin' == $_GET['action'] ) {
                    lnkdn_settings_page();
                } elseif ( 'custom_code' == $_GET['action'] ) {
                   bws_custom_code_tab();
                }     

                if ( ! isset( $_GET['action'] ) || ( isset( $_GET['action'] ) && 'custom_code' != $_GET['action'] ) )    
                    bws_form_restore_default_settings( $plugin_basename );
            }
            bws_plugin_reviews_block( $sclbttns_plugin_info['Name'], 'social-buttons-pack' ); ?>
        </div>
    <?php }
}

if ( ! function_exists ( 'sclbttns_admin_enqueue_scripts' ) ) {
    function sclbttns_admin_enqueue_scripts() {
        if ( isset( $_REQUEST['page'] ) && 'social-buttons.php' == $_REQUEST['page'] ) {
            if ( isset( $_GET['action'] ) && 'custom_code' == $_GET['action'] )
                bws_plugins_include_codemirror();
        }
    }
}

/* Registering and apllying styles and scripts */
if ( ! function_exists( 'sclbttns_wp_enqueue_scripts' ) ) {
    function sclbttns_wp_enqueue_scripts() {
        wp_enqueue_style( 'sclbttns_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );
    }
}

if ( ! function_exists( 'sclbttns_action_links' ) ) {
    function sclbttns_action_links( $links, $file ) {
        if ( ! is_network_admin() ) {
            /* Static so we don't call plugin_basename on every plugin row */
            static $this_plugin;
            if ( ! $this_plugin ) $this_plugin = plugin_basename( __FILE__ );
            if ( $file == $this_plugin ) {
                $settings_link = '<a href="admin.php?page=social-buttons.php">' . __( 'Settings' ) . '</a>';
                array_unshift( $links, $settings_link );
            }           
        }
        return $links;
    }
}

if ( ! function_exists( 'sclbttns_links' ) ) {
    function sclbttns_links( $links, $file ) {
        $base = plugin_basename( __FILE__ );
        if ( $file == $base ) {
            if ( ! is_network_admin() )
                $links[]    =   '<a href="admin.php?page=social-buttons.php">' . __( 'Settings' ) . '</a>';
            $links[]    =   '<a href="http://wordpress.org/plugins/social-buttons-pack/faq/" target="_blank">' . __( 'FAQ' ) . '</a>';
            $links[]    =   '<a href="http://support.bestwebsoft.com">' . __( 'Support' ) . '</a>';
        }
        return $links;
    }
}

if ( ! function_exists ( 'sclbttns_plugin_banner' ) ) {
    function sclbttns_plugin_banner() {
        global $hook_suffix;
        if ( 'plugins.php' == $hook_suffix && ! is_network_admin() ) {
            global $sclbttns_plugin_info, $sclbttns_options;

            if ( empty( $sclbttns_options ) )
                $sclbttns_options = get_option( 'sclbttns_options' );
        
            bws_plugin_banner_to_settings( $sclbttns_plugin_info, 'sclbttns_options', 'social-buttons-pack', 'admin.php?page=social-buttons.php' );
        }
    }
}

/* add help tab  */
if ( ! function_exists( 'sclbttns_add_tabs' ) ) {
    function sclbttns_add_tabs() {
        $screen = get_current_screen();
        $args = array(
            'id'            => 'sclbttns',
            'section'       => '200971639'
        );
        bws_help_tab( $screen, $args );
    }
}

/* Function for delete options */
if ( ! function_exists( 'sclbttns_uninstall' ) ) {
    function sclbttns_uninstall() {
        global $wpdb;
        if ( ! function_exists( 'get_plugins' ) )
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        $all_plugins = get_plugins();

        /* TWITTER */
        if ( ! array_key_exists( 'twitter-plugin/twitter-plugin.php', $all_plugins ) ) {
            if ( ! array_key_exists( 'twitter-pro/twitter-pro.php', $all_plugins ) ) {
                /* delete custom images if no PRO version */
                $upload_dir = wp_upload_dir();
                $twttr_cstm_mg_folder = $upload_dir['basedir'] . '/twitter-logo/';
                if ( is_dir( $twttr_cstm_mg_folder ) ) {
                    $twttr_cstm_mg_files = scandir( $twttr_cstm_mg_folder );
                    foreach ( $twttr_cstm_mg_files as $value ) {
                        @unlink ( $twttr_cstm_mg_folder . $value );
                    }
                    @rmdir( $twttr_cstm_mg_folder );
                }
            } 
            $delete_twitter = true;                         
        }

        /* google-plus-one */
        if ( ! array_key_exists( 'google-one/google-plus-one.php', $all_plugins ) )
            $delete_google_one = true;

        /* FB */
        if ( ! array_key_exists( 'facebook-button-plugin/facebook-button-plugin.php', $all_plugins ) ) {
            if ( ! array_key_exists( 'facebook-button-pro/facebook-button-pro.php', $all_plugins ) ) {
                /* delete custom images if no PRO version */
                $upload_dir = wp_upload_dir();
                $fcbkbttn_cstm_mg_folder = $upload_dir['basedir'] . '/facebook-image/';
                if ( is_dir( $fcbkbttn_cstm_mg_folder ) ) {
                    $fcbkbttn_cstm_mg_files = scandir( $fcbkbttn_cstm_mg_folder );
                    foreach ( $fcbkbttn_cstm_mg_files as $value ) {
                        @unlink ( $fcbkbttn_cstm_mg_folder . $value );
                    }
                    @rmdir( $fcbkbttn_cstm_mg_folder );
                }
            }
            $delete_facebook = true;
        }

        /* pinterest */
        if ( ! array_key_exists( 'bws-pinterest-plugin/bws-pinterest-plugin.php', $all_plugins ) ) {
            if ( ! array_key_exists( 'bws-pinterest-pro/bws-pinterest-pro.php', $all_plugins ) ) {
                /* delete custom images if no PRO version */
                $upload_dir = wp_upload_dir();
                $custom_img_folder = $upload_dir['basedir'] . '/pinterest-image/';
                if ( is_dir( $custom_img_folder ) ) {
                    $pntrstpr_custom_img_files = scandir( $custom_img_folder );
                    foreach ( $pntrstpr_custom_img_files as $value ) {
                        @unlink( $custom_img_folder . $value );
                    }
                    @rmdir( $custom_img_folder );
                }
            }
            $delete_pinterest = true;
        }

        /* linkedin */
        if ( ! array_key_exists( 'bws-linkedin/bws-linkedin.php', $all_plugins ) )
            $delete_linkedin = true;

        if ( isset( $delete_twitter ) || isset( $delete_google_one ) || isset( $delete_facebook ) || isset( $delete_linkedin ) || isset( $delete_pinterest ) ) {
            if ( function_exists( 'is_multisite' ) && is_multisite() ) {               
                $old_blog = $wpdb->blogid;
                /* Get all blog ids */
                $blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
                foreach ( $blogids as $blog_id ) {
                    switch_to_blog( $blog_id );
                    if ( isset( $delete_twitter ) )
                        delete_option( 'twttr_options' );
                    if ( isset( $delete_google_one ) )
                        delete_option( 'gglplsn_options' );
                    if ( isset( $delete_facebook ) )
                        delete_option( 'fcbk_bttn_plgn_options' );
                    if ( isset( $delete_linkedin ) )
                        delete_option( 'lnkdn_options' );
                    if ( isset( $delete_pinterest ) )
                        delete_option( 'pntrst_options' );
                }
                switch_to_blog( $old_blog );
            } else {
                if ( isset( $delete_twitter ) )
                    delete_option( 'twttr_options' );
                if ( isset( $delete_google_one ) )
                    delete_option( 'gglplsn_options' );
                if ( isset( $delete_facebook ) )
                    delete_option( 'fcbk_bttn_plgn_options' );
                if ( isset( $delete_linkedin ) )
                    delete_option( 'lnkdn_options' );
                if ( isset( $delete_pinterest ) )
                    delete_option( 'pntrst_options' );
            }
        } 

        require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
        bws_include_init( plugin_basename( __FILE__ ) );
        bws_delete_plugin( plugin_basename( __FILE__ ) );
    }
}

/* Adding 'BWS Plugins' admin menu */
add_action( 'admin_menu', 'sclbttns_add_pages' );

add_action( 'init', 'sclbttns_init' );
add_action( 'admin_init', 'sclbttns_admin_init' );

add_action( 'admin_enqueue_scripts', 'sclbttns_admin_enqueue_scripts' );
add_action( 'wp_enqueue_scripts', 'sclbttns_wp_enqueue_scripts' );

add_filter( 'plugin_action_links', 'sclbttns_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'sclbttns_links', 10, 2 );
/* Adding banner */
add_action( 'admin_notices', 'sclbttns_plugin_banner' );

register_uninstall_hook( __FILE__, 'sclbttns_uninstall' );
