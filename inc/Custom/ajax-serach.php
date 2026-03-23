<?php
add_action('wp_ajax_global_search', 'ajax_global_search');
add_action('wp_ajax_nopriv_global_search', 'ajax_global_search');

function ajax_global_search() {
    $term = sanitize_text_field($_POST['term'] ?? '');
    if(empty($term)){
        wp_send_json([]);
    }

    $args = [
        'post_type' => ['post','page','product'], // add your CPTs here
        'posts_per_page' => 10,
        's' => $term,
        'meta_query' => [ // search WooCommerce SKU if product
            [
                'key' => '_sku',
                'value' => $term,
                'compare' => 'LIKE'
            ]
        ]
    ];

    // Check if $term is numeric to allow ID search
    if(is_numeric($term)){
        $args['p'] = intval($term);
    }

    $query = new WP_Query($args);
    $results = [];

    if($query->have_posts()){
        while($query->have_posts()){
            $query->the_post();
            $image = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail') ?: wc_placeholder_img_src();
            $results[] = [
                'title' => get_the_title(),
                'permalink' => get_permalink(),
                'image' => $image,
            ];
        }
    }

    // If more than 10 results, add “See more” link
    if($query->found_posts > 10){
        $results[] = [
            'title' => 'See more results',
            'permalink' => home_url('/?s=' . urlencode($term)),
            'image' => ''
        ];
    }

    wp_reset_postdata();
    wp_send_json($results);
}