<?php
class ZestAdmin
{
    /**
     * @uses https://developer.wordpress.org/reference/functions/sanitize_option/
     */
    public function start()
    {

        if(isset($_GET['magicLink']))
        {
            $magic_link = sanitize_key($_GET['magicLink']);

            $response = ZestAPI::Create()->resolveMagicLink($magic_link);
            if(!is_null($response)) add_option(ZEST__MARKT__OPTION_KEY, $response);
        }

        if(isset($_POST['logout']))
        {
            $should_logout = sanitize_option($_POST['logout'], "yes");
            if($should_logout === "yes") delete_option(ZEST__MARKT__OPTION_KEY);
        }

        $admin_css = plugin_dir_url(__FILE__) . '_resources/zest.css';
        wp_enqueue_style('zest-style', $admin_css);

        require_once(ZEST__MARKT__VIEWS . 'admin.php');
    }

    public function setupMetaBox($post)
    {
        $suggested = get_option(ZEST__MARKT__OPTION_SUGGESTED);

        if(isset($suggested[$post->ID]))
        {
            ?><p>✔️ The post was already submitted to Zest.</p><?php
            return;
        }

        ?>
        <input type="checkbox" id="wporg_field" name="zestprf-suggest" checked />
        <label for="wporg_field">Contribute to the Zest community once post become public</label>
        <?php
    }
}