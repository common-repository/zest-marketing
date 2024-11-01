<div class="wrap">
    <h1>Zest</h1>
    <p>The Marketing Plugin enable Wordpress owners to easily contribute content to the Zest's marketing community.</p>


    <?php
        $zest_user = get_option(ZEST__MARKT__OPTION_KEY);
//        echo "<pre>".print_r($zest_user, true)."</pre>";

        if(!$zest_user) :

            $signup_url = 'https://zest.is/signup?r=' . urlencode(menu_page_url('zest-marketing', false));
    ?>
        <p>You're not logged in, please login with your Zest account</p>
        <a href="<?= esc_url($signup_url);  ?>#signin" class="button button-primary">Sign in on Zest</a>

    <?php else : ?>

        <p>You're connected to Zest. New posts will be contributed to Zest on behalf of <strong><?=esc_html($zest_user['name'])?></strong>.</p>

        <div class="zest-user">
            <figure><img src="<?= esc_url($zest_user['image']); ?>" alt="<?= esc_attr($zest_user['name']); ?>"/></figure>
            <div>
                <strong><?= $zest_user['name']; ?></strong>
                <nav>
                    <a href="https://zest.is/marketing/newsletter" target="_blank">Newsletter</a>
                    <a href="https://chrome.google.com/webstore/detail/zest-distilled-marketing/lgbbbmmegpehafpempogpgacjnfcbekj?hl=en&utm_source=wordpress&utm_medium=plugin">Add New Tab to Chrome</a>

                    <a href="https://zest.is/marketing" target="_blank">The Marketing Community</a>
                    <a href="https://support.zest.is" target="_blank">Support</a>
                </nav>
            </div>
        </div>

        <form action="<?= menu_page_url('zest-marketing', false) ?>" method="post">
            <input type="hidden" name="logout" value="yes" />
            <button type="submit" class="button button-primary">Sign out</button>
        </form>

    <?php endif; ?>

</div>