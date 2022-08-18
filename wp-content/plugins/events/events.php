<?php



/*

Plugin Name: Events Plugin

Plugin URI: http://genetechsolutions.com/

Description: Add Events and their Details Easy and Simple.

Author: Shan E Ruqayya

Version: 1.0.0

*/



add_action( 'init', 'codex_events_init' );




function codex_events_init() {

	$labels = array(

		'name'               => _x( 'Events', 'post type general name', 'your-plugin-textdomain' ),

		'singular_name'      => _x( 'Event', 'post type singular name', 'your-plugin-textdomain' ),

		'menu_name'          => _x( 'Events', 'admin menu', 'your-plugin-textdomain' ),

		'name_admin_bar'     => _x( 'Events', 'add new on admin bar', 'your-plugin-textdomain' ),

		'add_new'            => _x( 'Add Event', 'News', 'your-plugin-textdomain' ),

		'add_new_item'       => __( 'Add New Event', 'your-plugin-textdomain' ),

		'new_item'           => __( 'New Event', 'your-plugin-textdomain' ),

		'edit_item'          => __( 'Edit Event', 'your-plugin-textdomain' ),

		'view_item'          => __( 'View Events', 'your-plugin-textdomain' ),

		'all_items'          => __( 'All Events', 'your-plugin-textdomain' ),

		'search_items'       => __( 'Search Event', 'your-plugin-textdomain' ),

		'parent_item_colon'  => __( 'Parent Event:', 'your-plugin-textdomain' ),

		'not_found'          => __( 'No Events found.', 'your-plugin-textdomain' ),

		'not_found_in_trash' => __( 'No Event in Trash.', 'your-plugin-textdomain' )

	);



	$args = array(

		'labels'             => $labels,

        'description'        => __( 'Events', 'your-plugin-textdomain' ),

		'public'             => true,

		'publicly_queryable' => true,

		'menu_icon'          => 'dashicons-schedule',

		'show_ui'            => true,

		'show_in_menu'       => true,

		'query_var'          => true,

		'rewrite'            => array( 'slug' => 'event' ),

		'capability_type'    => 'post',

		'has_archive'        => false,

		'hierarchical'       => true,

		'menu_position'      => null,

		'supports'           => array( 'title',  'button', 'thumbnail'  )

	);

	register_post_type('events', $args );	

}



function show_events_sc_hp_function($attr){

	 $args = array(

		'post_type' => 'events',

		'posts_per_page' => $attr['number'],

		'posts_status' => "publish",

		'order_by' => "id",

		'order' => 'desc'

	);

	

	$featured = new WP_Query($args);

    // var_dump($featured);die;
	$result = "";
	if ($featured->have_posts()):
        $result  .= "<div class='events-section'>";
    while($featured->have_posts()): $featured->the_post();

	// $thumb = get_field('thumb_for_video');

    $result .= "<div class='events-content'>";

    	$result .= '<div class="content">';
            
    	    $title = get_the_title();
			
			$result .= '<div>';

				$result .= get_the_post_thumbnail();

			$result .= '</div>';	

			$result .= '<div>';

                $result .= '<h4> '.$title .'</h4>';

                $result .= '<p>' . get_the_date() . '</p>';

            $result .= '</div>';
            
        $result .= '</div>';

    $result .= '</div>';

	endwhile;

	$result .= "</div>";

	endif;	

    wp_reset_query();

	return $result;

}



add_shortcode('show_events_sc_hp', 'show_events_sc_hp_function');

