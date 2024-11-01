<?php
class ZestAPI
{
    /**
     * Call this method to get singleton
     * @return ZestAPI
     */
    public static function Create()
    {
        static $inst = null;
        if($inst === null) $inst = new ZestAPI();

        return $inst;
    }

    public function resolveMagicLink($token)
    {
        $url = ZEST__MARKT__ENDPOINT . '/1.0/connect/magic-link?token=' . $token;

        $response = wp_remote_get($url, array(
            "timeout" => 30,
            "redirection" => 1,
            "httpversion" => "1.0",
            "blocking" => true,
            "headers" => array(
                "Content-Type" => "application/json"
            )
        ));

        if(gettype($response) === "WP_Error")
        {
            echo "Failed to sign in user. Please try again or contact support at support@zest.is";
            return null;
        }

        if(isset($response['body']))
        {
            $decoded = json_decode($response['body'], true);

            /* API level failure */
            if(!isset($decoded['code']) || $decoded['code'] > 0) return null;

            if(!isset($decoded['message']['data']) || !$decoded['message']['data']['success']) return null;

            return $decoded['message']['data']['data'];
        }

        echo "Failed to sign in user. Please try again or contact support at support@zest.is";
        return null;
    }

    /**
     * @param $link
     * @param $post_id
     * @return bool
     */
    public function contributePost($link, $post_id)
    {
        $user = get_option(ZEST__MARKT__OPTION_KEY);

        $url = ZEST__MARKT__ENDPOINT . '/1.0/suggest/' . $user['token'];
        $response = wp_remote_post($url, array(
            "method" => "POST",
            "timeout" => 30,
            "redirection" => 1,
            "httpversion" => "1.0",
            "blocking" => true,
            "headers" => array(
                "Content-Type" => "application/json"
            ),
            "body" => json_encode(array(
                "link" => $link,
                "source" => "WPPlugin",
                "new" => true,
                "subscribe" => false
            ))
        ));

        if(gettype($response) === "WP_Error")
        {
            error_log("Failed to contribute content. " . $response->get_error_message());
            return null;
        }

        if(isset($response['body']))
        {
            $decoded = json_decode($response['body'], true);

            if(isset($decoded['code']) && $decoded['code'] === 0)
            {
                $suggested = get_option(ZEST__MARKT__OPTION_SUGGESTED);

                if(is_null($suggested))
                {
                    $suggested = array();
                    $suggested[$post_id] = true;

                    add_option(ZEST__MARKT__OPTION_SUGGESTED, $suggested);
                    return true;
                }

                $suggested[$post_id] = true;
                update_option(ZEST__MARKT__OPTION_SUGGESTED, $suggested);

                return true;
            }

            return false;
        }

        echo "<pre>".print_r($response, true)."</pre>";die;
    }
}