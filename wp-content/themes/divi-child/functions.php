<?php
function my_theme_enqueue_styles() 
{
    // Main Css StyleSheet
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    
    if ( is_front_page() ) {
        // Home Css StyleSheet
        wp_enqueue_style('home-style', get_stylesheet_directory_uri() . '/css/home.css');     
    }else{
        // Inner Css StyleSheet
        wp_enqueue_style('inner-style', get_stylesheet_directory_uri() . '/css/inner.css');
    }
    // Font
    wp_enqueue_style('font-style', get_stylesheet_directory_uri() . '/css/font/font.css');
    // Responsive Css
    wp_enqueue_style('responsive-style', get_stylesheet_directory_uri() . '/css/responsive.css');
    // Header and Footer Css
    wp_enqueue_style('header-footer-style', get_stylesheet_directory_uri() . '/css/header-footer.css');
    
    // Custome JavaScript
    wp_enqueue_script('custom-js', get_stylesheet_directory_uri() . '/js/custom.js', array(
        'jquery'
    ));

    
    // Slick Slider Css / Js
    wp_enqueue_style('slick', get_stylesheet_directory_uri() . '/js/slick/slick/slick.css');
    wp_enqueue_style('slick-theme', get_stylesheet_directory_uri() . '/js/slick/slick/slick-theme.css');
    wp_enqueue_script('slick-min', get_stylesheet_directory_uri() . '/js/slick/slick/slick.min.js' , array( 'jquery' ));
    
    
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );


// Events blog
function show_blog_sc_hp_function($attr){

    $args = array(

       'post_type' => 'post',

       'posts_per_page' => $attr['number'],

       'posts_status' => "publish",

       'order_by' => "id",

       'order' => 'desc'

   );

   

   $featured = new WP_Query($args);

   // var_dump($featured);die;
   $result = "";
   if ($featured->have_posts()):
        $result .= '<div class="events-section">';
   while($featured->have_posts()): $featured->the_post();

   // $thumb = get_field('thumb_for_video');

    

        $result .= '<a href="'.get_permalink().'" class="events-content">';
        
            $result .= '<div class="content">';
                    
                $result .= '<div>';
                    
                    $result .= '<img loading="lazy" width="271" height="302" src="'.get_the_post_thumbnail_url().'" class="attachment-post-thumbnail size-post-thumbnail wp-post-image" alt="'.get_the_title().'">';
                    
                $result .= '</div>';

                $result .= '<div>';
                    
                    $result .= '<h4>'.get_the_title().'</h4>';

                    $result .= '<p>'.get_the_date().'</p>';
                    
                $result .= '</div>';

            $result .= '</div>';

        $result .= '</a>';

    
   endwhile;

   $result .= "</div>";

   endif;	

   

   wp_reset_query();

   return $result;

}



add_shortcode('show_blog_sc_hp', 'show_blog_sc_hp_function');
