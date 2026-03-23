<?php
// ==========================
// 1. ACF Options Page
// ==========================
if( function_exists('acf_add_options_page') ) {
    acf_add_options_page([
        'page_title' => 'Dynamic Filters',
        'menu_title' => 'Dynamic Filters',
        'menu_slug'  => 'dynamic-filters',
        'capability' => 'edit_posts',
        'redirect'   => false
    ]);
}

// ==========================
// 2. Shortcode
// ==========================
add_shortcode('dynamic_archive', function($atts){
    ob_start();
    get_template_part('archive-dynamic'); 
    return ob_get_clean();
});

// ==========================
// 3. AJAX Filter Handler
// ==========================
add_action('wp_ajax_filter_products', 'dynamic_filter_products');
add_action('wp_ajax_nopriv_filter_products', 'dynamic_filter_products');
function dynamic_filter_products(){
    $cpt = $_POST['cpt'] ?? 'product';
    $paged = $_POST['page'] ?? 1;
    $meta_query = ['relation'=>'AND'];
    $tax_query = [];

    foreach($_POST as $key=>$val){
        if(in_array($key,['action','cpt','page'])) continue;

        // Range
        if(is_array($val) && isset($val['min'],$val['max'])){
            $meta_query[] = [
                'key'=>$key,
                'value'=>[$val['min'],$val['max']],
                'type'=>'NUMERIC',
                'compare'=>'BETWEEN'
            ];
        }
        // Checkbox / multi-select
        else if(is_array($val)){
            if(taxonomy_exists($key)){
                $tax_query[] = ['taxonomy'=>$key,'field'=>'slug','terms'=>$val,'operator'=>'IN'];
            } else {
                $meta_query[] = ['key'=>$key,'value'=>$val,'compare'=>'IN'];
            }
        }
        else{
            $meta_query[] = ['key'=>$key,'value'=>$val];
        }
    }

    $args = [
        'post_type'=>$cpt,
        'posts_per_page'=>6,
        'paged'=>$paged,
        'meta_query'=>$meta_query,
        'tax_query'=>$tax_query,
    ];

    $query = new WP_Query($args);
    if($query->have_posts()):
        while($query->have_posts()): $query->the_post(); ?>
            <div class="product">
                <h3><?php the_title() ?></h3>
                <p>£<?php the_field('price') ?></p>
            </div>
        <?php endwhile;
    else:
        echo '<p>No products found</p>';
    endif;
    wp_die();
}

// ==========================
// 4. Enqueue Assets
// ==========================
add_action('wp_enqueue_scripts', function(){
    wp_enqueue_style('dynamic-filters', get_template_directory_uri().'/assets/css/dynamic-filters.css');
    wp_enqueue_script('dynamic-filters', get_template_directory_uri().'/assets/js/dynamic-filters.js',['jquery'],'1.0',true);

    // noUiSlider for dual range
    wp_enqueue_style('nouislider','https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.css');
    wp_enqueue_script('nouislider','https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.js',['jquery'],'15.7.0',true);

    wp_localize_script('dynamic-filters','ajaxfilters',['ajaxurl'=>admin_url('admin-ajax.php')]);
});