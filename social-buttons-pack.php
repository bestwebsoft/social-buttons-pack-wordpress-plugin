<?php
/*
Plugin Name: Social Buttons Pack by BestWebSoft
Plugin URI: http://bestwebsoft.com/products/
Description: Add Social Buttons in to your site.
Author: BestWebSoft
Version: 1.0.0
Author URI: http://bestwebsoft.com/
License: GPLv3 or later
*/

/*  Copyright 2015  BestWebSoft  ( http://support.bestwebsoft.com )

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

if ( ! function_exists( 'sclbttns_add_pages' ) ) {
	function sclbttns_add_pages() {
		bws_add_general_menu( plugin_basename( __FILE__ ) );
		add_submenu_page( 'bws_plugins', __( 'Social Buttons Settings' ), 'Social Buttons', 'manage_options', "social-buttons.php", 'sclbttns_settings_page' );
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

        require_once( dirname( __FILE__ ) . '/bws_menu/bws_functions.php' );        
        bws_wp_version_check( plugin_basename( __FILE__ ), $sclbttns_plugin_info, "3.1" );

    }
}

if ( ! function_exists( 'sclbttns_admin_init' ) ) {
    function sclbttns_admin_init() {
        global $bws_plugin_info, $sclbttns_plugin_info;

        deactivate_plugins( 'google-one/google-plus-one.php' );
        deactivate_plugins( 'twitter-plugin/twitter.php' );
        deactivate_plugins( 'facebook-button-plugin/facebook-button-plugin.php' );

        if ( ! isset( $bws_plugin_info ) || empty( $bws_plugin_info ) )
            $bws_plugin_info = array( 'id' => '209', 'version' => $sclbttns_plugin_info["Version"] );
    }
}

if ( ! function_exists( 'sclbttns_settings_page' ) ) {
    function sclbttns_settings_page() { 
        global $sclbttns_plugin_info; ?>
        <div class="wrap">
            <div class="icon32 icon32-bws" id="icon-options-general"></div>
            <h2><?php _e( "Social Buttons Settings", 'facebook' ); ?></h2>
            <h2 class="nav-tab-wrapper">
                <a class="nav-tab<?php if ( ! isset( $_GET['action'] ) || ( isset( $_GET['action'] ) && 'facebook' == $_GET['action'] ) ) echo ' nav-tab-active'; ?>" href="admin.php?page=social-buttons.php&amp;action=facebook"><?php _e( 'Facebook' ); ?></a>
                <a class="nav-tab<?php if ( isset( $_GET['action'] ) && 'twitter' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=social-buttons.php&amp;action=twitter"><?php _e( 'Twitter' ); ?></a>
                <a class="nav-tab<?php if ( isset( $_GET['action'] ) && 'google-one' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=social-buttons.php&amp;action=google-one"><?php _e( 'Google+1' ); ?></a>
                <a class="nav-tab" href="http://bestwebsoft.com/products/social-buttons-pack/faq/" target="_blank"><?php _e( 'FAQ' ); ?></a>
            </h2>
            <?php if ( ! isset( $_GET['action'] ) || ( isset( $_GET['action'] ) && 'facebook' == $_GET['action'] ) ) {
                fcbkbttn_settings_page();
            } elseif ( 'twitter' == $_GET['action'] ) {
                twttr_settings_page();
            } elseif ( 'google-one' == $_GET['action'] ) {
                gglplsn_options();
            }
            bws_plugin_reviews_block( $sclbttns_plugin_info['Name'], 'social-buttons-pack' ); ?>
        </div>
    <?php }
}

/* Registering and apllying styles and scripts */
if ( ! function_exists( 'sclbttns_admin_head' ) ) {
    function sclbttns_admin_head() {
        wp_enqueue_script( 'sclbttns_script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery' ) );
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

/* Function for delete options */
if ( ! function_exists( 'sclbttns_uninstall' ) ) {
    function sclbttns_uninstall() {
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
            delete_option( 'twttr_options' );
        }

        /* google-plus-one */
        if ( ! array_key_exists( 'google-one/google-plus-one.php', $all_plugins ) )
            delete_option( 'gglplsn_options' );

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
            delete_option( 'fcbk_bttn_plgn_options' );
        }
    }
}

/* Adding 'BWS Plugins' admin menu */
add_action( 'admin_menu', 'sclbttns_add_pages' );

add_action( 'init', 'sclbttns_init' );
add_action( 'admin_init', 'sclbttns_admin_init' );

add_action( 'admin_enqueue_scripts', 'sclbttns_admin_head' );
add_action( 'wp_enqueue_scripts', 'sclbttns_wp_enqueue_scripts' );

add_filter( 'plugin_action_links', 'sclbttns_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'sclbttns_links', 10, 2 );

register_uninstall_hook( __FILE__, 'sclbttns_uninstall' );