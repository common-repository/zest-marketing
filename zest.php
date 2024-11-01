<?php
/*
Plugin Name: Zest Marketing
Plugin URI: https://zest.is/marketing
Description: Make marketing content distribution easier by auto submitting content to Zest (<a href="https://zest.is/marketing/contribute-content" target="_blank">https://zest.is/marketing/contribute-content</a>)
Version: 1.0.2
Author: Snow White Labs ltd
Author URI: https://zest.is
License: GPLv2 or later
Text Domain: zest
*/

// @see https://developer.wordpress.org/plugins/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define( 'ZEST__MARKT__VERSION', '0.0.2');
define( 'ZEST__MARKT__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ZEST__MARKT__RESOURCES', ZEST__MARKT__PLUGIN_DIR . '_resources/');
define( 'ZEST__MARKT__VIEWS', ZEST__MARKT__PLUGIN_DIR . 'views/');
define( 'ZEST__MARKT__ENDPOINT', 'https://api.zest.is');
define( 'ZEST__MARKT__OPTION_KEY', 'zest_user');
define( 'ZEST__MARKT__OPTION_SUGGESTED', 'zest_suggested');

require_once( ZEST__MARKT__PLUGIN_DIR . 'class.zest-admin.php' );
require_once( ZEST__MARKT__PLUGIN_DIR . 'class.zest-api.php' );

class Zest
{
    public function init()
    {
        add_action("admin_menu", array($this, 'addAdminPage'));
        add_action("save_post", array($this, 'onPostSave'));
        add_action("add_meta_boxes", array($this, 'addMetaBox'), 2);
    }

    public function onPostSave($post_id)
    {
        error_log($_POST['zestprf-suggest'] .  ">> " . var_export($_POST['zestprf-suggest'], true));

        if(!isset($_POST['zestprf-suggest']) || $_POST['zestprf-suggest'] !== "on")
        {
            return;
        }

        $post = get_post($post_id);

        if($post->post_type !== "post") return;

        if($post->post_status === "publish")
        {
            $suggested = get_option(ZEST__MARKT__OPTION_SUGGESTED);
            if(is_null($suggested) || !$suggested[$post_id])
            {
                error_log("SUGGEST POST");
                ZestAPI::Create()->contributePost(get_permalink($post), $post_id);
            }
        }
    }

    public function addMetaBox()
    {
        $screens = [ 'post', 'wporg_cpt' ];
        foreach ($screens as $screen) {
            add_meta_box(
                'zest-meta-box',
                '🍋 Zest',
                array(new ZestAdmin(), 'setupMetaBox'),
                $screen,
                'side',
                'high'
            );
        }
    }

    public function addAdminPage()
    {
        add_menu_page( 'Zest', 'Zest', 'manage_options', 'zest-marketing', array(new ZestAdmin(), 'start') );
    }
}

$zest = new Zest();

add_action( 'init', array($zest, 'init'));