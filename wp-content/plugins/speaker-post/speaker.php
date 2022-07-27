<?php



/*

Plugin Name: Speaker Post Plugin

Plugin URI: http://genetechsolutions.com/

Description: Post and Speaker Content.

Author: Muzzamil Qureshi

Version: 1.0.0

*/



add_action( 'init', 'codex_speaker_init' );




function codex_speaker_init() {

	$labels = array(

		'name'               => _x( 'Speaker Media', 'post type general name', 'your-plugin-textdomain' ),

		'singular_name'      => _x( 'Speaker Media', 'post type singular name', 'your-plugin-textdomain' ),

		'menu_name'          => _x( 'Speaker Media', 'admin menu', 'your-plugin-textdomain' ),

		'name_admin_bar'     => _x( 'Speaker', 'add new on admin bar', 'your-plugin-textdomain' ),

		'add_new'            => _x( 'Add Post', 'News', 'your-plugin-textdomain' ),

		'add_new_item'       => __( 'Add New Post', 'your-plugin-textdomain' ),

		'new_item'           => __( 'New Post', 'your-plugin-textdomain' ),

		'edit_item'          => __( 'Edit Post', 'your-plugin-textdomain' ),

		'view_item'          => __( 'View Posts', 'your-plugin-textdomain' ),

		'all_items'          => __( 'All posts', 'your-plugin-textdomain' ),

		'search_items'       => __( 'Search Post', 'your-plugin-textdomain' ),

		'parent_item_colon'  => __( 'Parent Post:', 'your-plugin-textdomain' ),

		'not_found'          => __( 'No Post found.', 'your-plugin-textdomain' ),

		'not_found_in_trash' => __( 'No Post in Trash.', 'your-plugin-textdomain' )

	);



	$args = array(

		'labels'             => $labels,

        'description'        => __( 'speaker media', 'your-plugin-textdomain' ),

		'public'             => true,

		'publicly_queryable' => true,

		'menu_icon'          => 'dashicons-format-status',

		'show_ui'            => true,

		'show_in_menu'       => true,

		'query_var'          => true,

		'rewrite'            => array( 'slug' => 'speaker' ),

		'capability_type'    => 'post',

		'has_archive'        => false,

		'hierarchical'       => true,

		'menu_position'      => null,

		'supports'           => array( 'title',  'button' )

	);

	register_post_type('speaker', $args );	

}



function show_speaker_sc_hp_function($attr){

	 $args = array(

		'post_type' => 'speaker',

		'posts_per_page' => $attr['number'],

		'posts_status' => "publish",

		'order_by' => "id",

		'order' => 'desc'

	);

	

	$featured = new WP_Query($args);

    // var_dump($featured);die;
	$result = "";
	if ($featured->have_posts()):
        $result  .= "<div class='mq-speaker-post-section'>";
    while($featured->have_posts()): $featured->the_post();

	// $thumb = get_field('thumb_for_video');

    $result .= "<div class='mq-speaker-post-content'>";

    	$result .= '<div class="speaker-content">';
    	    $result .= '<video controls>';
    	        $result .= '<source type="video/mp4" src="'.get_field('speaker_post_video').'">';
    	    $result .= '</video>';
    	    $result .= '<div class="overlay-image" style="background: url('.get_field('overlay_image')['url'].')no-repeat center / cover;">';
    	    $result .= '</div>';
        $result .= '</div>';

    $result .= '</div>';

	endwhile;

	$result .= "</div>";

	endif;	

    wp_reset_query();

	return $result;

}



add_shortcode('show_speaker_sc_hp', 'show_speaker_sc_hp_function');

