<?php
/**
 * @package VidAnalytic
 * @version 0.8.2
 */
/*T
Plugin Name: VidAnalytic
Plugin URI: http://wordpress.org/extend/plugins/video-analytics-by-vidanalyticcom/
Description: VidAnalytic is a free companion solution to Google Analytics for tracking embedded video usage on site.
Author: vidanalytic
Version: 0.8.2
Author URI: http://www.vidanalytic.com/
*/

function cxtn_rewrite_youtube($content) {
  $mChannelID = get_option('cxtn_channel_id');
  if(empty($mChannelID))
  {
    # if no channel ID found, do not rewrite the URL 
    return $content;
  }

  /* TODO: Shortcodes? */

  $yt_http_embedprefix         = "http://www.youtube.com/embed/";
  $yt_http_embedprefix_length  = strlen($yt_http_embedprefix);
  $yt_https_embedprefix        = "https://www.youtube.com/embed/";
  $yt_https_embedprefix_length = strlen($yt_https_embedprefix);
  

  $dom=new domDocument();
  @$dom->loadHTML('<?xml encoding="' . DB_CHARSET . '"?>' . $content);
  
  /* Find and replace all IFRAME embeds with Content Extension embeds */
  $xpath = new DOMXpath($dom);
  $result = $xpath->query("//iframe");
  foreach ($result as $iframeNode) {
    $iframeAutoplay = FALSE;
    $iframeURL = $iframeNode->getAttribute("src");
    $iframeQuery = parse_url($iframeURL, PHP_URL_QUERY);
    if (is_string($iframeQuery)) {
        $queryParts = array();
        parse_str($iframeQuery, $queryParts);
        if (isset($queryParts['autoplay'])) {
            if ($queryParts['autoplay'] == TRUE) {
                $iframeAutoplay = TRUE;
            }
        }
    }

    if (strncmp($iframeURL, $yt_http_embedprefix, $yt_http_embedprefix_length) === 0)
    {
      $yt_video_id = substr($iframeURL, $yt_http_embedprefix_length, 11);
      $srcURL = sprintf("http://player.cxtn.net/player/embed/youtube/%s?channelid=".$mChannelID, $yt_video_id );
      if ($iframeAutoplay == TRUE) {
          $srcURL .= "&autoplay=1";
      }
      $iframeNode->setAttribute("src", $srcURL);
    }

    if (strncmp($iframeURL, $yt_https_embedprefix, $yt_https_embedprefix_length) === 0)
    {
      $yt_video_id = substr($iframeURL, $yt_https_embedprefix_length, 11);
      $srcURL = sprintf("http://player.cxtn.net/player/embed/youtube/%s?channelid=".$mChannelID, $yt_video_id );
      if ($iframeAutoplay == TRUE) {
          $srcURL .= "&autoplay=1";
      }
      $iframeNode->setAttribute("src", $srcURL);
    }

  }

  $newcontent = $dom->saveHTML();
  return $newcontent;
}

add_filter( 'the_content', 'cxtn_rewrite_youtube' );

if(!get_option('cxtn_channel_id') && $_GET['page'] != 'cxtn_dashboard_menu')
{
  add_action('admin_notices', 'swp_analytic_admin_notice');
  function swp_analytic_admin_notice() {
    echo '<div class="updated"><p><strong>VidAnalytic is almost ready.</strong> You must enter your <a href="'.admin_url("admin.php?page=cxtn_dashboard_menu").'">Channel ID</a> for it to work.</p></div>';
  }
}

function cxtn_add_loader() {
  $mChannelID = get_option('cxtn_channel_id');
  if(!empty($mChannelID))
  {
    $cxtn_loader_path = "http://cdn.cxtn.net/loader/loader.js#channelid=".$mChannelID;
    wp_enqueue_script(
         "cxtn_loader",
       $cxtn_loader_path
    );
  }  
}
add_action('wp_enqueue_scripts', 'cxtn_add_loader');

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
}
?>
