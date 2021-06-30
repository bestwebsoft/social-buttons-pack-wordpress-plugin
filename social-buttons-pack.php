<?php
/*
Plugin Name: Social Buttons Pack by BestWebSoft
Plugin URI: https://bestwebsoft.com/products/wordpress/plugins/social-buttons-pack/
Description: Add social media buttons and widgets to WordPress posts, pages and widgets. FB, Twitter, Pinterest, LinkedIn.
Author: BestWebSoft
Text Domain: social-buttons-pack
Domain Path: /languages
Version: 1.1.8
Author URI: https://bestwebsoft.com/
License: GPLv3 or later
*/

/*  Copyright 2021  BestWebSoft  ( https://support.bestwebsoft.com )

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

if ( ! function_exists( 'sclbttns_add_pages' ) ) {
	function sclbttns_add_pages() {
        global $submenu, $sclbttns_plugin_info, $wp_version;

        $settings = add_menu_page( __( 'Social Buttons Settings', 'social-buttons-pack' ), 'Social Buttons', 'manage_options', 'social-buttons.php', 'sclbttns_settings_page', 'none' );
        add_submenu_page( 'social-buttons.php', __( 'Social Buttons Settings', 'social-buttons-pack' ), __( 'Settings', 'facebook-button-plugin' ), 'manage_options', 'social-buttons.php', 'sclbttns_settings_page' );

        add_submenu_page( 'social-buttons.php', 'BWS Panel', 'BWS Panel', 'manage_options', 'sclbttns-bws-panel', 'bws_add_menu_render' );
        if ( isset( $submenu['social-buttons.php'] ) )
            $submenu['social-buttons.php'][] = array( 
                '<span style="color:#d86463"> ' . __( 'Upgrade to Pro', 'social-buttons-pack' ) . '</span>',
                'manage_options',
                'https://bestwebsoft.com/products/wordpress/plugins/social-buttons-pack/?k=c0d1b84b603c503e8a16cfa6252b2f70&pn=209&v=' . $sclbttns_plugin_info["Version"] . '&wp_v=' . $wp_version );

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

        bws_wp_min_version_check( plugin_basename( __FILE__ ), $sclbttns_plugin_info, '4.5' );
    }
}

if ( ! function_exists( 'sclbttns_admin_init' ) ) {
    function sclbttns_admin_init() {
        global $bws_plugin_info, $sclbttns_plugin_info, $sclbttns_options, $pagenow;

        $deactivate_plugins = array( 
            'twitter-plugin/twitter.php', 
            'facebook-button-plugin/facebook-button-plugin.php',
            'bws-pinterest/bws-pinterest.php',
            'bws-linkedin/bws-linkedin.php'
        );

        if ( function_exists( 'is_multisite' ) && is_multisite() ) {
            if ( ! is_plugin_active_for_network( plugin_basename( __FILE__ ) ) )
                $deactivate_not_for_all_network = true;
        }

        foreach ( $deactivate_plugins as $free ) {
            if ( isset( $deactivate_not_for_all_network ) && is_plugin_active_for_network( $free ) ) {
                global $wpdb;
                deactivate_plugins( $free );

                $old_blog = $wpdb->blogid;
                /* Get all blog ids */
                $blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
                foreach ( $blogids as $blog_id ) {
                    switch_to_blog( $blog_id );
                    activate_plugin( $free );
                }
                switch_to_blog( $old_blog );
            } else {
                deactivate_plugins( $free );
            }
        }

        if ( empty( $bws_plugin_info ) )
            $bws_plugin_info = array( 'id' => '209', 'version' => $sclbttns_plugin_info["Version"] );

        if ( isset( $_GET['page'] ) && 'social-buttons.php' == $_GET['page'] )
            sclbttns_settings();

        if ( 'plugins.php' == $pagenow ) {
            /* Install the option defaults */
            if ( function_exists( 'bws_plugin_banner_go_pro' ) ) {
	            sclbttns_settings();
                bws_plugin_banner_go_pro( $sclbttns_options, $sclbttns_plugin_info, 'sclbttns', 'social-buttons-pack', '8e369cddbfb4fe0b44a75ee687df1716', '209', 'social-buttons-pack' );
            }
        }
    }
}

if ( ! function_exists( 'sclbttns_plugins_loaded' ) ) {
    function sclbttns_plugins_loaded() {
        /* Internationalization, first(!) */
        load_plugin_textdomain( 'social-buttons-pack', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }
}

/**
 * Write plugin settings in to database
 * @return void
 */
if ( ! function_exists( 'sclbttns_settings' ) ) {
    function sclbttns_settings() {
        global $sclbttns_options, $sclbttns_plugin_info;
    
        if ( ! get_option( 'sclbttns_options' ) ) {
            $option_defaults = sclbttns_get_option_defaults();
            add_option( 'sclbttns_options', $option_defaults );
        }

        $sclbttns_options = get_option( 'sclbttns_options' );
        
        if ( ! isset( $sclbttns_options['plugin_option_version'] ) || $sclbttns_options['plugin_option_version'] != $sclbttns_plugin_info["Version"] ) {
            $option_defaults = sclbttns_get_option_defaults();
            $sclbttns_options = array_merge( $option_defaults, $sclbttns_options );
            $sclbttns_options['plugin_option_version'] = $sclbttns_plugin_info["Version"];
            /* show pro features */
            $sclbttns_options['hide_premium_options'] = array();
            update_option( 'sclbttns_options', $sclbttns_options );
        }
    }
}

if ( ! function_exists( 'sclbttns_get_option_defaults' ) ) {
    function sclbttns_get_option_defaults() {
        global $sclbttns_plugin_info;

        $option_defaults = array(
            'plugin_option_version'         => $sclbttns_plugin_info["Version"],
            'display_settings_notice'       => 1,
            'suggest_feature_banner'        => 1 
        );

        return $option_defaults;
    }
}

if ( ! function_exists( 'sclbttns_settings_page' ) ) {
    function sclbttns_settings_page() { 
        if ( ! class_exists( 'Bws_Settings_Tabs' ) )
            require_once( dirname( __FILE__ ) . '/bws_menu/class-bws-settings.php' );
        require_once( dirname( __FILE__ ) . '/includes/class-sclbttns-settings.php' );
        $page = new Sclbttns_Settings_Tabs( plugin_basename( __FILE__ ) ); 
        if ( method_exists( $page, 'add_request_feature' ) ) {
            $page->add_request_feature();      
        } ?>
        <div class="wrap">
            <h1><?php _e( 'Social Buttons Settings', 'social-buttons-pack' ); ?></h1>
            <noscript><div class="error below-h2"><p><strong><?php _e( "Please, enable JavaScript in Your browser.", 'social-buttons-pack' ); ?></strong></p></div></noscript>
            <?php $page->display_content(); ?>
        </div>
    <?php }
}

if ( ! function_exists ( 'sclbttns_admin_enqueue_scripts' ) ) {
    function sclbttns_admin_enqueue_scripts() {
        wp_enqueue_style( 'sclbttns_stylesheet', plugins_url( 'css/admin_style.css', __FILE__ ) );

        if ( isset( $_REQUEST['page'] ) && 'social-buttons.php' == $_REQUEST['page'] ) {
            bws_enqueue_settings_scripts();
            bws_plugins_include_codemirror();
            wp_enqueue_script( 'sclbttns_script', plugins_url( 'js/admin-script.js', __FILE__ ), array( 'jquery' ) );
            wp_enqueue_media();
            wp_localize_script( 'sclbttns_script', 'sclbttns_var',
                array(
                    'wp_media_title'    => __( 'Insert Media', 'social-buttons-pack' ),
                    'wp_media_button'   => __( 'Insert', 'social-buttons-pack' )
                )
            );
        }
    }
}

/* Registering and apllying styles and scripts */
if ( ! function_exists( 'sclbttns_wp_enqueue_scripts' ) ) {
    function sclbttns_wp_enqueue_scripts() {
        wp_enqueue_style( 'sclbttns_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );
    }
}


if ( ! function_exists ( 'sclbttns_theme_body_classes' ) ) {
    function sclbttns_theme_body_classes( $classes ) {
        if ( function_exists( 'wp_get_theme' ) ) {
            $current_theme = wp_get_theme();
            $classes[] = basename( $current_theme->get( 'ThemeURI' ) );
        }
        return $classes;
    }
}

if ( ! function_exists( 'sclbttns_action_links' ) ) {
    function sclbttns_action_links( $links, $file ) {
        if ( ! is_network_admin() ) {
            /* Static so we don't call plugin_basename on every plugin row */
            static $this_plugin;
            if ( ! $this_plugin ) $this_plugin = plugin_basename( __FILE__ );
            if ( $file == $this_plugin ) {
                $settings_link = '<a href="admin.php?page=social-buttons.php">' . __( 'Settings', 'social-buttons-pack' ) . '</a>';
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
                $links[]    =   '<a href="admin.php?page=social-buttons.php">' . __( 'Settings', 'social-buttons-pack' ) . '</a>';
            $links[]    =   '<a href="http://wordpress.org/plugins/social-buttons-pack/faq/" target="_blank">' . __( 'FAQ', 'social-buttons-pack' ) . '</a>';
            $links[]    =   '<a href="https://support.bestwebsoft.com">' . __( 'Support', 'social-buttons-pack' ) . '</a>';
        }
        return $links;
    }
}

if ( ! function_exists ( 'sclbttns_plugin_banner' ) ) {
    function sclbttns_plugin_banner() {
        global $hook_suffix, $sclbttns_plugin_info;
        if ( 'plugins.php' == $hook_suffix && ! is_network_admin() ) {
            bws_plugin_banner_to_settings( $sclbttns_plugin_info, 'sclbttns_options', 'social-buttons-pack', 'admin.php?page=social-buttons.php' );
        }

        if ( isset( $_GET['page'] ) && 'social-buttons.php' == $_GET['page'] )
            bws_plugin_suggest_feature_banner( $sclbttns_plugin_info, 'sclbttns_options', 'social-buttons-pack' );
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

        if ( ! array_key_exists( 'social-buttons-pack-pro/social-buttons-pack-pro', $all_plugins ) ) {
            /* TWITTER */
            if ( ! array_key_exists( 'twitter-plugin/twitter-plugin.php', $all_plugins ) && ! array_key_exists( 'twitter-pro/twitter-pro.php', $all_plugins ) ) {
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

                $delete_twitter = true;                                    
            }

	        /* FB */
	        if ( ! array_key_exists( 'facebook-button-plugin/facebook-button-plugin.php', $all_plugins ) && ! array_key_exists( 'facebook-button-pro/facebook-button-pro.php', $all_plugins ) ) {
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
		        $delete_facebook = true;
	        }

            /* pinterest */
            if ( ! array_key_exists( 'bws-pinterest-plugin/bws-pinterest-plugin.php', $all_plugins ) && ! array_key_exists( 'bws-pinterest-pro/bws-pinterest-pro.php', $all_plugins ) ) {
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
                $delete_pinterest = true;          
            }

            /* linkedin */
            if ( ! array_key_exists( 'bws-linkedin/bws-linkedin.php', $all_plugins ) && ! array_key_exists( 'bws-linkedin-pro/bws-linkedin-pro.php', $all_plugins ) )
                $delete_linkedin = true;

            if ( isset( $delete_twitter ) || isset( $delete_facebook ) || isset( $delete_linkedin ) || isset( $delete_pinterest ) ) {
                if ( function_exists( 'is_multisite' ) && is_multisite() ) {               
                    $old_blog = $wpdb->blogid;
                    /* Get all blog ids */
                    $blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
                    foreach ( $blogids as $blog_id ) {
                        switch_to_blog( $blog_id );
                        if ( isset( $delete_twitter ) )
                            delete_option( 'twttr_options' );
                        if ( isset( $delete_facebook ) )
                            delete_option( 'fcbkbttn_options' );
                        if ( isset( $delete_linkedin ) )
                            delete_option( 'lnkdn_options' );
                        if ( isset( $delete_pinterest ) )
                            delete_option( 'pntrst_options' );
                    }
                    switch_to_blog( $old_blog );
                } else {
                    if ( isset( $delete_twitter ) )
                        delete_option( 'twttr_options' );
                    if ( isset( $delete_facebook ) )
                        delete_option( 'fcbkbttn_options' );
                    if ( isset( $delete_linkedin ) )
                        delete_option( 'lnkdn_options' );
                    if ( isset( $delete_pinterest ) )
                        delete_option( 'pntrst_options' );
                }
            }

            if ( function_exists( 'is_multisite' ) && is_multisite() ) {               
                $old_blog = $wpdb->blogid;
                /* Get all blog ids */
                $blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
                foreach ( $blogids as $blog_id ) {
                    switch_to_blog( $blog_id );
                    delete_option( 'sclbttns_options' );
                }
                switch_to_blog( $old_blog );
            } else {
                delete_option( 'sclbttns_options' );
            }
        }

        require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
        bws_include_init( plugin_basename( __FILE__ ) );
        bws_delete_plugin( plugin_basename( __FILE__ ) );
    }
}

/* Adding 'BWS Plugins' admin menu */
add_action( 'admin_menu', 'sclbttns_add_pages' );

add_action( 'plugins_loaded', 'sclbttns_plugins_loaded' );
add_action( 'init', 'sclbttns_init' );
add_action( 'admin_init', 'sclbttns_admin_init' );

add_action( 'admin_enqueue_scripts', 'sclbttns_admin_enqueue_scripts' );
add_action( 'wp_enqueue_scripts', 'sclbttns_wp_enqueue_scripts' );

add_filter( 'plugin_action_links', 'sclbttns_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'sclbttns_links', 10, 2 );
/* Adding banner */
add_action( 'admin_notices', 'sclbttns_plugin_banner' );
/* add theme name as class to body tag */
add_filter( 'body_class', 'sclbttns_theme_body_classes' );

register_uninstall_hook( __FILE__, 'sclbttns_uninstall' );

/* add buttons file - we added it after all actions&filter because we need to add theirs filter after */
require_once( dirname( __FILE__ ) . '/facebook-button-plugin/facebook-button-plugin.php' );
require_once( dirname( __FILE__ ) . '/twitter-plugin/twitter.php' );
require_once( dirname( __FILE__ ) . '/bws-linkedin/bws-linkedin.php' );
require_once( dirname( __FILE__ ) . '/bws-pinterest/bws-pinterest.php' );
require_once( dirname( __FILE__ ) . '/includes/sclbttns-nstgrm.php' );