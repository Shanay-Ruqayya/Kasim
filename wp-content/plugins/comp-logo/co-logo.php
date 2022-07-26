<?php



/*

Plugin Name: Co-logo Plugin

Plugin URI: http://genetechsolutions.com/

Description: Add Company Logo and there Links Easy and Simple.

Author: Muzzamil Qureshi

Version: 1.0.0

*/



add_action( 'init', 'codex_logo_init' );




function codex_logo_init() {

	$labels = array(

		'name'               => _x( 'Company Logo', 'post type general name', 'your-plugin-textdomain' ),

		'singular_name'      => _x( 'Company Logo', 'post type singular name', 'your-plugin-textdomain' ),

		'menu_name'          => _x( 'Company Logo', 'admin menu', 'your-plugin-textdomain' ),

		'name_admin_bar'     => _x( 'Logo', 'add new on admin bar', 'your-plugin-textdomain' ),

		'add_new'            => _x( 'Add Logo', 'News', 'your-plugin-textdomain' ),

		'add_new_item'       => __( 'Add New Logo', 'your-plugin-textdomain' ),

		'new_item'           => __( 'New Logo', 'your-plugin-textdomain' ),

		'edit_item'          => __( 'Edit Logo', 'your-plugin-textdomain' ),

		'view_item'          => __( 'View Logos', 'your-plugin-textdomain' ),

		'all_items'          => __( 'All Logos', 'your-plugin-textdomain' ),

		'search_items'       => __( 'Search Logo', 'your-plugin-textdomain' ),

		'parent_item_colon'  => __( 'Parent Logo:', 'your-plugin-textdomain' ),

		'not_found'          => __( 'No Logos found.', 'your-plugin-textdomain' ),

		'not_found_in_trash' => __( 'No Logo in Trash.', 'your-plugin-textdomain' )

	);



	$args = array(

		'labels'             => $labels,

        'description'        => __( 'Company Logo', 'your-plugin-textdomain' ),

		'public'             => true,

		'publicly_queryable' => true,

		'menu_icon'          => 'dashicons-format-status',

		'show_ui'            => true,

		'show_in_menu'       => true,

		'query_var'          => true,

		'rewrite'            => array( 'slug' => 'logo' ),

		'capability_type'    => 'post',

		'has_archive'        => false,

		'hierarchical'       => true,

		'menu_position'      => null,

		'supports'           => array( 'title',  'button' )

	);

	register_post_type('logo', $args );	

}



function show_logo_sc_hp_function($attr){

	 $args = array(

		'post_type' => 'logo',

		'posts_per_page' => $attr['number'],

		'posts_status' => "publish",

		'order_by' => "id",

		'order' => 'desc'

	);

	

	$featured = new WP_Query($args);

    // var_dump($featured);die;
	$result = "";
	if ($featured->have_posts()):
        $result  .= "<div class='mq-co-logo-section'>";
    while($featured->have_posts()): $featured->the_post();

	// $thumb = get_field('thumb_for_video');

    $result .= "<div class='mq-co-logo-content'>";

    	$result .= '<div class="content">';
            
    	    $result .= '<a href="'.get_field('company_link').'" target="_blank" class="mq-c-t-img">';
				$result .= '<img src="'.get_field('company_logo')['url'].'" alt="'.get_field('company_name').'">';
    	    $result .= '</a>';
			
        $result .= '</div>';

    $result .= '</div>';

	endwhile;

	$result .= "</div>";

	endif;	

    wp_reset_query();

	return $result;

}



add_shortcode('show_logo_sc_hp', 'show_logo_sc_hp_function');

