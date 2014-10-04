<?php
/**
 * @package VidAnalytic
 * @version 1.0.0
 */
/*T
Plugin Name: VidAnalytic
Plugin URI: http://wordpress.org/extend/plugins/video-analytics-by-vidanalyticcom/
Description: VidAnalytic is a free companion solution to Google Analytics for tracking embedded video usage on site.
Author: vidanalytic
Version: 1.0.0
Author URI: http://www.vidanalytic.com/
*/

if(!get_option('cxtn_channel_id') && $_GET['page'] != 'cxtn_dashboard_menu')
{
  add_action('admin_notices', 'swp_analytic_admin_notice');
  function swp_analytic_admin_notice() {
    echo '<div class="updated"><p><strong>VidAnalytic is almost ready.</strong> You must enter your <a href="'.admin_url("admin.php?page=cxtn_dashboard_menu").'">Channel ID</a> for it to work.</p></div>';
  }
}

function cxtn_add_loader_v2() {
    $plugin_version = '1.0.0';
    $mChannelID = get_option('cxtn_channel_id');

    $cxtnmOptions= get_option('cxtn_track_permission');
    $advpermission= $cxtnmOptions['cxtn_adv_track_opt'];
    $permission=false;
    $current_user = wp_get_current_user();

    if ( is_user_logged_in() ) {
        $roles =$current_user->roles;
        $role = array_shift($roles);
        if( $cxtnmOptions['cxtn_adv_track_role_'.$role]==1) $permission = true;

        if($advpermission==1 && $permission==false )
            $mChannelID= 'default';
    }

    if(empty($mChannelID))
    {
        $mChannelID= 'default';
    }


    echo '<script>
        (function() {
        var channelid = \''.$mChannelID.'\';
        var cx = document.createElement(\'script\'); cx.type = \'text/javascript\'; cx.async = true;
        cx.src = "http://cdn.cxtn.net/loader/loader.js?piver='.$plugin_version.'&ver=wp'.get_bloginfo('version').'#channelid=" + channelid;
        var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(cx, s);
      })();
    </script>';

}
add_action( 'wp_head', 'cxtn_add_loader_v2' );

function cxtn_plugin_menu() {
        add_options_page( 'VidAnalytic Plugin Options', 'VidAnalytic', 'manage_options', 'vidanalytic-plugin-options', 'cxtn_plugin_options' );

}
add_action( 'admin_menu', 'cxtn_plugin_menu' );

/* What to do when the plugin is activated? */
//register_activation_hook(__FILE__,'cxtn_plugin_install');

/* What to do when the plugin is deactivated? */
register_deactivation_hook( __FILE__, 'cxtn_plugin_remove' );

function cxtn_plugin_install() {
/* Create a new database field */
        /*  add_option("cxtn_channel_id", '', 'deprecated', 'yes'); */
}

function cxtn_plugin_remove() {
/* Delete the database field */
  delete_option('cxtn_channel_id');
}

function cxtn_plugin_options() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    include_once('option-page.php');
}

add_action('admin_menu', 'register_custom_menu_page');

function register_custom_menu_page() {
    add_menu_page( "VidAnalytic", "VidAnalytic", "manage_options", "cxtn_dashboard_menu", 'cxtn_plugin_options' );

}

# Need for applying Wordpress Default validation
add_action('admin_head', 'cxtn_admin_head');
function cxtn_admin_head() {
    wp_enqueue_script('post');
    wp_enqueue_script('jquery');

}
?>
