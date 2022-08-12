<?php



/*

Plugin Name: Book Reviews Plugin

Plugin URI: http://genetechsolutions.com/

Description: Add Book Reviews Easy and Simple.

Author: Shan E Ruqayya

Version: 1.0.0

*/



add_action( 'init', 'codex_reviews_init' );




function codex_reviews_init() {

	$labels = array(

		'name'               => _x( 'Reviews', 'post type general name', 'your-plugin-textdomain' ),

		'singular_name'      => _x( 'Review', 'post type singular name', 'your-plugin-textdomain' ),

		'menu_name'          => _x( 'Reviews', 'admin menu', 'your-plugin-textdomain' ),

		'name_admin_bar'     => _x( 'Reviews', 'add new on admin bar', 'your-plugin-textdomain' ),

		'add_new'            => _x( 'Add Review', 'News', 'your-plugin-textdomain' ),

		'add_new_item'       => __( 'Add New Review', 'your-plugin-textdomain' ),

		'new_item'           => __( 'New Review', 'your-plugin-textdomain' ),

		'edit_item'          => __( 'Edit Review', 'your-plugin-textdomain' ),

		'view_item'          => __( 'View Reviews', 'your-plugin-textdomain' ),

		'all_items'          => __( 'All Reviews', 'your-plugin-textdomain' ),

		'search_items'       => __( 'Search Review', 'your-plugin-textdomain' ),

		'parent_item_colon'  => __( 'Parent Review:', 'your-plugin-textdomain' ),

		'not_found'          => __( 'No Reviews found.', 'your-plugin-textdomain' ),

		'not_found_in_trash' => __( 'No Review in Trash.', 'your-plugin-textdomain' )

	);



	$args = array(

		'labels'             => $labels,

        'description'        => __( 'Reviews', 'your-plugin-textdomain' ),

		'public'             => true,

		'publicly_queryable' => true,

		'menu_icon'          => 'dashicons-format-status',

		'show_ui'            => true,

		'show_in_menu'       => true,

		'query_var'          => true,

		'rewrite'            => array( 'slug' => 'review' ),

		'capability_type'    => 'post',

		'has_archive'        => false,

		'hierarchical'       => true,

		'menu_position'      => null,

		'supports'           => array( 'title',  'button', 'editor' )

	);

	register_post_type('reviews', $args );	

}



function show_reviews_sc_hp_function($attr){

	 $args = array(

		'post_type' => 'reviews',

		'posts_per_page' => $attr['number'],

		'posts_status' => "publish",

		'order_by' => "id",

		'order' => 'desc'

	);

	

	$featured = new WP_Query($args);

    // var_dump($featured);die;
	$result = "";
	if ($featured->have_posts()):
        $result  .= "<div class='reviews-section-posttype'>";
    while($featured->have_posts()): $featured->the_post();

	// $thumb = get_field('thumb_for_video');

    $result .= "<div class='review-content'>";

    	$result .= '<div class="box">';
            
    	    $title = get_the_title();
			
			

			$result .= '<div>';

				$result .= "<div class='five-stars'> </div>";

                $result .= '<h5> '.$title .'</h5>';

				$result .= '<p>' . get_the_content() . '</p>';

                $result .= '<p><strong>-' . get_field('author') . '</strong></p>';

            $result .= '</div>';
            
        $result .= '</div>';

    $result .= '</div>';

	endwhile;

	$result .= "</div>";

	endif;	

    wp_reset_query();

	return $result;

}



add_shortcode('show_reviews_sc_hp', 'show_reviews_sc_hp_function');

