<?php

use WCBT\Helpers\WishList;

if (!isset($data['wishlist_page_url'])) {
    return;
}

do_action('wcbt/layout/wishlist/wishlist-link/before', $data);
?>
<div class="<?php echo esc_attr(
                apply_filters('wcbt/filter/wishlist/wishlist-link/wrapper-class', 'wcbt-show-wishlist')
            ); ?>">
    <span class="count"><?php echo esc_html(WishList::get_count()); ?></span>
    <a href="<?php echo esc_url($data['wishlist_page_url']); ?>">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g id="Icon">
                <path id="Vector" d="M20.8401 4.61C20.3294 4.09901 19.7229 3.69365 19.0555 3.41709C18.388 3.14052 17.6726 2.99818 16.9501 2.99818C16.2276 2.99818 15.5122 3.14052 14.8448 3.41709C14.1773 3.69365 13.5709 4.09901 13.0601 4.61L12.0001 5.67L10.9401 4.61C9.90843 3.57831 8.50915 2.99871 7.05012 2.99871C5.59109 2.99871 4.19181 3.57831 3.16012 4.61C2.12843 5.64169 1.54883 7.04097 1.54883 8.5C1.54883 9.95903 2.12843 11.3583 3.16012 12.39L4.22012 13.45L12.0001 21.23L19.7801 13.45L20.8401 12.39C21.3511 11.8792 21.7565 11.2728 22.033 10.6054C22.3096 9.9379 22.4519 9.22249 22.4519 8.5C22.4519 7.77751 22.3096 7.06211 22.033 6.39465C21.7565 5.72719 21.3511 5.12076 20.8401 4.61Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </g>
        </svg>
    </a>
</div>

<?php
do_action('wcbt/layout/wishlist/wishlist-link/after', $data);
