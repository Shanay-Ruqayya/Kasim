<?php
function my_theme_enqueue_styles() 
{
    // Main Css StyleSheet
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style('responsive-style', get_stylesheet_directory_uri() . '/css/responsive.css');
    wp_enqueue_style('header-footer-style', get_stylesheet_directory_uri() . '/css/header-footer.css');
    
    if ( is_front_page() ) {
        // Home Css StyleSheet
        wp_enqueue_style('home-style', get_stylesheet_directory_uri() . '/css/home.css');     
    }else{
        // Inner Css StyleSheet
        wp_enqueue_style('inner-style', get_stylesheet_directory_uri() . '/css/inner.css');
    }
    // Custome JavaScript
    wp_enqueue_script('custom-js', get_stylesheet_directory_uri() . '/js/custom.js', array(
        'jquery'
    ));
    // Slick Slider Css / Js
    // wp_enqueue_style('slick', get_stylesheet_directory_uri() . '/slick/slick.css');
    // wp_enqueue_style('slick-theme', get_stylesheet_directory_uri() . '/slick/slick-theme.css');
    // wp_enqueue_script('slick-min', get_stylesheet_directory_uri() . '/slick/slick.min.js' , array( 'jquery' ));
    
    
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );