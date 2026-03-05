<?php
$mods = asap_get_theme_mods();
$new_post_info = asap_get_new_post_info();
$post_ids_shown = [];
$excluded_posts = asap_get_excluded_post_ids();
?>

<main class="content-loop-design">

<?php asap_show_home_text_before(); ?>

<?php 
asap_render_featured_posts(
    $post_ids_shown,
    $new_post_info,
    $mods['show_category'],
    $mods['show_date_loop'],
    $mods['show_stars'],
    $mods['enable_cache'],
    $mods['cache_period'],      
    $mods,
    $excluded_posts,
    null,
    null,
    null
);
?>

<div class="asap-padding-newspapper">
    <div class="home-categories">
    <?php
        if ($mods['home_show_latest_posts']) {
            asap_render_latest_posts(
                $mods['home_last_post_count'],
                $mods['columns'],
                $mods['show_category'],
                $mods['show_date_loop'],
                $mods['show_extract'],
                $mods['show_stars'],
                $mods['show_featured_first'],
                $mods['enable_cache'],
                $mods['cache_period'],   
                $mods['content_type'],             
                $new_post_info,
                $post_ids_shown,
                $excluded_posts
            );
        }
        if (!empty($mods['home_categories'])) {
            asap_render_home_categories(
                $mods['home_categories'],
                $mods['sort_order'],
                $mods['show_category'],
                $mods['show_extract'],
                $mods['show_date_loop'],
                $mods['show_stars'],
                $mods['show_featured_first'],
                $mods['enable_cache'],
                $mods['cache_period'], 
                $mods['content_type'],           
                $new_post_info,
                $post_ids_shown,
                $excluded_posts
            );
        }
        if (!empty($mods['home_tags'])) {        
            asap_render_home_tags(
                $mods['home_tags'],
                $mods['tag_sort_order'],
                $mods['show_category'],
                $mods['show_extract'],
                $mods['show_date_loop'],
                $mods['show_stars'],
                $mods['show_featured_first'],
                $mods['enable_cache'],
                $mods['cache_period'], 
                $mods['content_type'],           
                $new_post_info,
                $post_ids_shown,
                $excluded_posts
            );    
        }    
        ?>
    </div>
</div>

<?php asap_show_home_text_after(); ?>

</main>
