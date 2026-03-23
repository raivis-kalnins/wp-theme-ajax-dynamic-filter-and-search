<?php
add_action('wp_ajax_global_search', 'ajax_global_search');
add_action('wp_ajax_nopriv_global_search', 'ajax_global_search');

function ajax_global_search() {
    $term = sanitize_text_field($_POST['term'] ?? '');
    if (!$term) {
        wp_send_json([]);
    }

    $args = [
        'post_type'      => ['post','page','product'], 
        'posts_per_page' => 10,
        's'              => $term,
    ];

    if (is_numeric($term)) {
        $args['p'] = intval($term);
    }

    add_filter('posts_join', 'search_woocommerce_sku_join');
    add_filter('posts_where', 'search_woocommerce_sku_where', 10, 2);

    $query = new WP_Query($args);

    remove_filter('posts_join', 'search_woocommerce_sku_join');
    remove_filter('posts_where', 'search_woocommerce_sku_where', 10);

    $results = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $image = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail') 
                     ?: wc_placeholder_img_src();

            $results[] = [
                'title'     => get_the_title(),
                'permalink' => get_permalink(),
                'image'     => $image,
            ];
        }
    }

    if ($query->found_posts > 10) {
        $results[] = [
            'title'     => 'See all results',
            'permalink' => esc_url(home_url('/?s=' . urlencode($term))),
            'image'     => '',
        ];
    }

    wp_reset_postdata();

    wp_send_json($results);
}

// **Helper filter functions for SKU search**
function search_woocommerce_sku_join( $join ) {
    global $wpdb;
    $join .= " LEFT JOIN {$wpdb->postmeta} AS sku_pm ON {$wpdb->posts}.ID = sku_pm.post_id ";
    return $join;
}

function search_woocommerce_sku_where( $where, $wp_query ) {
    global $wpdb;
    $term = esc_sql($wp_query->get('s'));
    $where .= " OR ( sku_pm.meta_key = '_sku' AND sku_pm.meta_value LIKE '%{$term}%' ) ";
    return $where;
}