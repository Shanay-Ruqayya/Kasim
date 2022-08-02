<?php
/**
 * Used to process publish and edit episode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'CFMH_Hosting_Publish_Episode' ) ) :

	if ( function_exists( 'set_time_limit' ) ) {
		set_time_limit( 0 );
	}

	/**
	 * Hosting Publish Episode class
	 *
	 * @since 1.0
	 */
	class CFMH_Hosting_Publish_Episode {

		/**
		 * Save episode
		 *
		 * @since 1.0
		 * @return void
		 */
		public static function publish_episode_save() {

			$post_id = isset( $_POST['post_id'] ) ? sanitize_text_field( wp_unslash( $_POST['post_id'] ) ) : 0;
			$post_id = (int) $post_id;

			if ( ! isset( $_POST['_sec'] ) || ! wp_verify_nonce( $_POST['_sec'], '_sec_action_' . $post_id ) ) {

				wp_die( __( "Cheatin' uh?" ) );
				exit;
			} else {

				$episode_info = array();
				$response    = '0';
				$errors      = 0;

				if ( isset( $_POST['show_id'] ) ) {

					// required fields.
					if ( '' == $_POST['media_id'] && ! isset( $_POST['episode_draft'] ) ) {
						++$errors; }
					if ( '' == $_POST['post_title'] ) {
						++$errors; }
					if ( ('' == $_POST['post_content'] || '<p><br></p>' == $_POST['post_content']) && 'off' == $_POST['enable_wordpress_editor']) {
						++$errors; }
					if ( ('' == $_POST['post_content_wp']) && isset($_POST['enable_wordpress_editor']) && 'on' == $_POST['enable_wordpress_editor']) {
						++$errors; }

					$itunes_title = ( isset( $_POST['post_title_check'] ) && isset( $_POST['itunesTitle'] ) ) ? sanitize_text_field( wp_unslash( $_POST['itunesTitle'] ) ) : '';
					$post_title   = isset( $_POST['post_title'] ) ? sanitize_text_field( wp_unslash( $_POST['post_title'] ) ) : '';
					$enable_wordpress_editor = isset( $_POST['enable_wordpress_editor'] ) ? sanitize_text_field( wp_unslash( $_POST['enable_wordpress_editor'] ) ) : 'off';
					$shownotes = $enable_wordpress_editor == 'on' ? wp_filter_post_kses( $_POST['post_content_wp'] ) : wp_filter_post_kses( $_POST['post_content'] );

					// Post data.
					$post_author = isset( $_POST['post_author'] ) ? sanitize_text_field( wp_unslash( $_POST['post_author'] ) ) : get_current_user_id();

					$post_data = array(
						'post_title'   => sanitize_text_field( wp_unslash( $post_title ) ),
						'post_content' => wp_unslash( $shownotes ),
						'post_author'  => (int) $post_author,
						'post_excerpt'  => wp_unslash( wp_filter_kses( $_POST['post_excerpt'] ) ),
						'post_type'    => 'captivate_podcast',
					);

					// Post date and status.
					$post_datetime = sanitize_text_field( wp_unslash( $_POST['publish_date'] ) ) . ' ' . sanitize_text_field( wp_unslash( $_POST['publish_time'] ) );
					$post_datetime = date( 'Y-m-d H:i:s', strtotime( $post_datetime ) );

					$current_date      = new DateTime();
					$post_date_publish = new DateTime( $post_datetime );
					if ( $post_date_publish > $current_date ) {
						$post_data['post_status'] = 'future';
						$episode_info['status']  = 'Future';
					} else {
						$post_data['post_status'] = 'publish';
						$episode_info['status']  = 'Published';
					}
					if ( isset( $_POST['episode_draft'] ) ) {
						$post_data['post_status'] = 'draft';
						$episode_info['status']  = 'Draft';
					}

					if ( isset( $_POST['new_post_name'] ) ) {
						$post_data['post_name'] = sanitize_title( wp_unslash( $_POST['new_post_name'] ) );
						$episode_info['slug'] = sanitize_title( wp_unslash( $_POST['new_post_name'] ) );
					}

					$post_data['comment_status'] = isset( $_POST['website_comment'] ) ? 'open' : 'closed';
					$post_data['ping_status'] = isset( $_POST['website_ping'] ) ? 'open' : 'closed';

					$post_data['post_date']     = $post_datetime;
					$post_data['post_date_gmt'] = get_gmt_from_date( $post_datetime, 'Y-m-d H:i:s' );

					// Insert the post into the database if no error.
					if ( $errors > 0 ) {
						if ( ( isset( $_POST['episode_update'] ) || isset( $_POST['episode_draft'] ) ) && 0 != $post_id ) {
							wp_redirect( admin_url( 'admin.php?page=cfm-hosting-edit-episode&show_id=' . sanitize_text_field( wp_unslash( $_POST['show_id'] ) ) . "&eid={$post_id}&response=1" ) );
						} else {
							wp_redirect( admin_url( 'admin.php?page=cfm-hosting-publish-episode&response=1' ) );
						}
					} else {

						$cfm_episode_id = get_post_meta( $post_id, 'cfm_episode_id', true );

						if ( 0 != $post_id ) {
							$post_data['ID'] = $post_id;
							$post_data['edit_date'] = true;
							wp_update_post( $post_data );
							$episode_info['episodes_id'] = $cfm_episode_id;
						} else {
							$post_id = wp_insert_post( $post_data );
						}

						// episode categories.
						if ( isset( $_POST['tax_input']['captivate_category'] ) ) {
							$captivate_categories 	= wp_unslash( $_POST['tax_input']['captivate_category'] );
							$selected_categories	= array();

							if ( is_array( $captivate_categories ) && ! empty( $captivate_categories ) ) {
								foreach ( $captivate_categories as $id ) {
									$selected_categories[] = sanitize_text_field( $id );
								}
							}

							if ( ! empty( $selected_categories ) ) {
								wp_set_post_terms( $post_id, $selected_categories, 'captivate_category', false );
							}
						}

						// episode tags.
						if ( isset( $_POST['tax_input']['captivate_tag'] ) ) {
							$captivate_tags = wp_unslash( $_POST['tax_input']['captivate_tag'] );
							if ( ! empty( $captivate_tags ) ) {
								$tags = array();
								foreach ( $captivate_tags as $tag ) {
									$tags[] = (int) sanitize_text_field( $tag );
								}
								wp_set_post_terms( $post_id, $tags, 'captivate_tag', false );
							}
						}

						// show id.
						$episode_info['shows_id'] = sanitize_text_field( wp_unslash( $_POST['show_id'] ) );
						update_post_meta( $post_id, 'cfm_show_id', sanitize_text_field( wp_unslash( $_POST['show_id'] ) ) );

						// use wordpress editor.
						update_post_meta( $post_id, 'cfm_enable_wordpress_editor', sanitize_text_field( wp_unslash( $enable_wordpress_editor ) ) );

						// iTunes title.
						$episode_info['itunes_title'] = $itunes_title;
						update_post_meta( $post_id, 'cfm_episode_itunes_title', $itunes_title );

						// Artwork, select new, do nothing if no artwork selected and if it's just the same.
						$uploaded_artwork = '';
						$show_artwork = cfm_get_show_info( $_POST['show_id'], 'artwork' );
						if ( $show_artwork != $_POST['episode_artwork'] ) {
							$uploaded_artwork = sanitize_text_field( wp_unslash( $_POST['episode_artwork'] ) );
						}
						$artwork_id = sanitize_text_field( wp_unslash( $_POST['episode_artwork_id'] ) );

						if ( '' != $artwork_id && get_post_meta( $post_id, 'cfm_episode_artwork_id', true ) != $artwork_id ) {
							$artwork_url = sanitize_text_field( wp_unslash( $_POST['episode_artwork'] ) );
							$artwork_width = sanitize_text_field( wp_unslash( $_POST['episode_artwork_width'] ) );
							$artwork_height = sanitize_text_field( wp_unslash( $_POST['episode_artwork_height'] ) );
							$artwork_type = sanitize_text_field( wp_unslash( $_POST['episode_artwork_type'] ) );
							$artwork_filesize = sanitize_text_field( wp_unslash( $_POST['episode_artwork_filesize'] ) );

							// Upload selected artwork to Captivate.
							$uploaded_artwork = cfm_upload_file( $artwork_url, sanitize_text_field( wp_unslash( $_POST['show_id'] ) ) );

							update_post_meta( $post_id, 'cfm_episode_artwork_id', $artwork_id );
							update_post_meta( $post_id, 'cfm_episode_artwork_width', $artwork_width );
							update_post_meta( $post_id, 'cfm_episode_artwork_height', $artwork_height );
							update_post_meta( $post_id, 'cfm_episode_artwork_type', $artwork_type );
							update_post_meta( $post_id, 'cfm_episode_artwork_filesize', $artwork_filesize );
							update_post_meta( $post_id, 'cfm_episode_artwork', $uploaded_artwork );
						}

						$episode_info['episode_art'] = $uploaded_artwork;

						// Featured image.
						if ( isset( $_POST['featured_image'] ) && '' != $_POST['featured_image'] ) {
							$image_id = sanitize_text_field( wp_unslash( $_POST['featured_image'] ) );

							// set as featured image.
							update_post_meta( $post_id, '_thumbnail_id', $image_id );
						}

						// remove featured image.
						if ( '0' == $_POST['featured_image'] ) {
							delete_post_meta( $post_id, '_thumbnail_id' );
						}

						// Episode subtitle.
						$episode_info['itunes_subtitle'] = sanitize_text_field( wp_unslash( $_POST['itunesSubtitle'] ) );
						update_post_meta( $post_id, 'cfm_episode_itunes_subtitle', sanitize_text_field( wp_unslash( $_POST['itunesSubtitle'] ) ) );

						// Episode season.
						$episode_info['episode_season'] = sanitize_text_field( wp_unslash( $_POST['itunesEpisodeSeason'] ) );
						update_post_meta( $post_id, 'cfm_episode_itunes_season', sanitize_text_field( wp_unslash( $_POST['itunesEpisodeSeason'] ) ) );

						// Episode number.
						$episode_info['episode_number'] = sanitize_text_field( wp_unslash( $_POST['itunesEpisodeNumber'] ) );
						update_post_meta( $post_id, 'cfm_episode_itunes_number', sanitize_text_field( wp_unslash( $_POST['itunesEpisodeNumber'] ) ) );

						// Episode type.
						$episode_info['episode_type'] = sanitize_text_field( wp_unslash( $_POST['itunesEpisodeType'] ) );
						update_post_meta( $post_id, 'cfm_episode_itunes_type', sanitize_text_field( wp_unslash( $_POST['itunesEpisodeType'] ) ) );

						// Episode explicit.
						$episode_info['explicit'] = sanitize_text_field( wp_unslash( $_POST['itunesExplicit'] ) );
						update_post_meta( $post_id, 'cfm_episode_itunes_explicit', sanitize_text_field( wp_unslash( $_POST['itunesExplicit'] ) ) );

						// Donation link.
						$donation_link = isset( $_POST['donationLink'] ) ? sanitize_text_field( wp_unslash( $_POST['donationLink'] ) ) : '';
						if (filter_var($donation_link, FILTER_VALIDATE_URL) !== FALSE) {
    						$episode_info['donation_link'] = $donation_link;
							update_post_meta( $post_id, 'cfm_episode_donation_link', $donation_link );
						}

						// Donation label.
						$episode_info['donation_text'] = sanitize_text_field( wp_unslash( $_POST['donationLabel'] ) );
						update_post_meta( $post_id, 'cfm_episode_donation_label', sanitize_text_field( wp_unslash( $_POST['donationLabel'] ) ) );

						// SEO title.
						$episode_info['seo_title'] = sanitize_text_field( wp_unslash( $_POST['seoTitle'] ) );
						update_post_meta( $post_id, 'cfm_episode_seo_title', sanitize_text_field( wp_unslash( $_POST['seoTitle'] ) ) );

						// SEO description.
						$episode_info['seo_description'] = sanitize_text_field( wp_unslash( $_POST['seoDescription'] ) );
						update_post_meta( $post_id, 'cfm_episode_seo_description', sanitize_text_field( wp_unslash( $_POST['seoDescription'] ) ) );

						// Episode summary.
						$itunesSummary = ( isset( $_POST['itunes_summary_check'] ) && isset( $_POST['itunesSummary'] ) ) ? wp_unslash( wp_filter_kses( $_POST['itunesSummary'] ) ) : '';
						$episode_info['summary'] = $itunesSummary;
						update_post_meta( $post_id, 'cfm_episode_itunes_summary', $itunesSummary );

						// Audio file.
						if ( isset( $_POST['media_id'] ) ) {
							$episode_info['media_id'] = sanitize_text_field( wp_unslash( $_POST['media_id'] ) );
							update_post_meta( $post_id, 'cfm_episode_media_id', sanitize_text_field( wp_unslash( $_POST['media_id'] ) ) );
						}

						if ( isset( $_POST['media_url'] ) ) {

							$media_info = array( 'duration' => sanitize_text_field( wp_unslash( $_POST['media_duration'] ) ) );

							$enclosure = sanitize_text_field( $_POST['media_url'] ) . "\n" . sanitize_text_field( wp_unslash( $_POST['media_size'] ) ) . "\n" . sanitize_text_field( wp_unslash( $_POST['media_type'] ) ) . "\n" . serialize( $media_info );
							update_post_meta( $post_id, 'enclosure', $enclosure );
							update_post_meta( $post_id, 'cfm_episode_media_url', sanitize_text_field( wp_unslash( $_POST['media_url'] ) ) );
						} else {

							update_post_meta( $post_id, 'cfm_episode_media_url', '' );
						}

						if ( isset( $_POST['media_type'] ) ) {
							update_post_meta( $post_id, 'cfm_episode_media_type', sanitize_text_field( wp_unslash( $_POST['media_type'] ) ) );
						}

						if ( isset( $_POST['media_size'] ) ) {
							update_post_meta( $post_id, 'cfm_episode_media_size', sanitize_text_field( wp_unslash( $_POST['media_size'] ) ) );
						}

						if ( isset( $_POST['media_duration'] ) ) {
							update_post_meta( $post_id, 'cfm_episode_media_duration', sanitize_text_field( wp_unslash( $_POST['media_duration'] ) ) );
						}

						// Transcript.
						if ( isset( $_FILES['transcriptFile'] ) && $_FILES['transcriptFile']['size'] != 0 ) {

							$transcript_allowed = array( 'srt' );
							$transcript_filename = $_FILES['transcriptFile']['name'];
							$transcript_ext = pathinfo( $transcript_filename, PATHINFO_EXTENSION );

							if ( ! in_array( $transcript_ext, $transcript_allowed ) ) {
							    $transcript = array();
							}
							else {
								$transcript = $_FILES['transcriptFile'];
							}
						}
						else {
							$transcript = wp_unslash( wp_filter_kses( $_POST['transcriptText'] ) );
						}

						// Custom field.
						if ( isset( $_POST['custom_field'] ) ) {
							update_post_meta( $post_id, 'cfm_episode_custom_field', wp_unslash( $_POST['custom_field'] ) );
						}

						$episode_info['title']     = $post_title;
						$episode_info['shownotes'] = wp_unslash( $shownotes );
						$episode_info['date']      = $post_datetime;
						$episode_info['via_sync']  = true;

						if ( $cfm_episode_id && ( isset( $_POST['episode_update'] ) || isset( $_POST['episode_draft'] ) ) ) {

							$response = wp_remote_request(
								CFMH_API_URL . '/episodes/' . $cfm_episode_id,
								array(
									'timeout' => 500,
									'body'    => $episode_info,
									'method'  => 'PUT',
									'headers' => array(
										'Authorization' => 'Bearer ' . get_transient( 'cfm_authentication_token' ),
									),
								)
							);

							// Debugging.
							if ( cfm_is_debugging_on() ) {
								$log_date = date( 'Y-m-d H:i:s', time() );
								$txt = '**EDIT EPISODE (ID ' . $post_id . ') - ' . $log_date . '**' . PHP_EOL . print_r( $response, true ) . '**END EDIT EPISODE**';
								$myfile = file_put_contents( CFMH . '/logs.txt', PHP_EOL . $txt . PHP_EOL , FILE_APPEND | LOCK_EX );
							}

							if ( ! is_wp_error( $response ) && 'Unauthorized' !== $response['body'] && is_array( $response ) ) {

								$body = json_decode( $response['body'] );

								if ( 403 == $response['response']['code'] ) {
									wp_redirect( admin_url( 'admin.php?page=cfm-hosting-edit-episode&show_id=' . sanitize_text_field( wp_unslash( $_POST['show_id'] ) ) . "&eid={$post_id}&response=4" ) );
								}

								if ( isset( $body->success ) ) {

									// transcriptions.
									if ( isset( $_POST['transcript_updated'] ) && '1' == $_POST['transcript_updated'] ) {
										$update_transcript = cfm_update_transcript( $transcript, $cfm_episode_id );
										update_post_meta( $post_id, 'cfm_episode_transcript', $update_transcript );
									}

									wp_redirect( admin_url( 'admin.php?page=cfm-hosting-edit-episode&show_id=' . sanitize_text_field( wp_unslash( $_POST['show_id'] ) ) . "&eid={$post_id}&response=3" ) );
								}

							} else {
								// api error
								wp_redirect( admin_url( 'admin.php?page=cfm-hosting-edit-episode&show_id=' . sanitize_text_field( wp_unslash( $_POST['show_id'] ) ) . "&eid={$post_id}&response=6" ) );
							}

						} else {

							$response = wp_remote_post(
								CFMH_API_URL . '/episodes',
								array(
									'timeout' => 500,
									'body'    => $episode_info,
									'headers' => array(
										'Authorization' => 'Bearer ' . get_transient( 'cfm_authentication_token' ),
									),
								)
							);

							// Debugging.
							if ( cfm_is_debugging_on() ) {
								$log_date = date( 'Y-m-d H:i:s', time() );
								$txt = '**PUBLISH EPISODE (ID ' . $post_id . ') - ' . $log_date . '**' . PHP_EOL . print_r( $response, true ) . '**END PUBLISH EPISODE**';
								$myfile = file_put_contents( CFMH . '/logs.txt', PHP_EOL . $txt . PHP_EOL , FILE_APPEND | LOCK_EX );
							}

							if ( ! is_wp_error( $response ) && 'Unauthorized' !== $response['body'] && is_array( $response ) ) {
								if ( 403 == $response['response']['code'] ) {
									wp_redirect( admin_url( 'admin.php?page=cfm-hosting-publish-episode&response=4' ) );
								}

								$body = json_decode( $response['body'] );

								if ( isset( $body->success ) && $body->episode ) {

									$captivate_episode_id = $body->episode->id ? $body->episode->id : $body->episode->episodes_id;

									update_post_meta( $post_id, 'cfm_episode_id', $captivate_episode_id );
									update_post_meta( $post_id, 'cfm_show_id', sanitize_text_field( wp_unslash( $_POST['show_id'] ) ) );

									// transcriptions.
									if ( isset( $_POST['transcript_updated'] ) && '1' == $_POST['transcript_updated'] ) {

										// add only if transcript exists.
										if ( isset( $_POST['transcript_current'] ) ) {
											$update_transcript = cfm_update_transcript( $transcript, $captivate_episode_id );
											update_post_meta( $post_id, 'cfm_episode_transcript', $update_transcript );
										}
									}

									wp_redirect( admin_url( 'admin.php?page=cfm-hosting-edit-episode&show_id=' . sanitize_text_field( wp_unslash( $_POST['show_id'] ) ) . "&eid={$post_id}&response=2&action=published" ) );
								} else {
									wp_redirect( admin_url( 'admin.php?page=cfm-hosting-edit-episode&show_id=' . sanitize_text_field( wp_unslash( $_POST['show_id'] ) ) . "&eid={$post_id}&response=6" ) );
								}
							} else {
								wp_redirect( admin_url( 'admin.php?page=cfm-hosting-edit-episode&show_id=' . sanitize_text_field( wp_unslash( $_POST['show_id'] ) ) . "&eid={$post_id}&response=6" ) );
							}

						}
					}
				} else {
					wp_redirect( admin_url( 'admin.php?page=cfm-hosting-publish-episode&response=5' ) );
				}
			}

		}

		/**
		 * Add category
		 *
		 * @since 1.0
		 * @return json
		 */
		public static function add_webcategory() {

			$output = '';

			if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( $_POST['_nonce'], '_cfm_nonce' ) ) {
				$output = 'error';
			} else {

				$parent   = isset( $_POST['category_parent'] ) ? sanitize_text_field( wp_unslash( $_POST['category_parent'] ) ) : '';
				$category = isset( $_POST['category'] ) ? sanitize_text_field( wp_unslash( $_POST['category'] ) ) : '';

				if ( ! empty( $category ) ) :

					$json = array();

					$inserted_cat = wp_insert_term( $category, 'captivate_category', array( 'parent' => $parent ) );

					$term = get_term_by( 'id', $inserted_cat['term_id'], 'captivate_category' );

					$json['cat_checklist'] = '<li id="captivate_category-' . esc_attr( $inserted_cat['term_id'] ) . '"><label class="selectit"><input value="' . esc_attr( $inserted_cat['term_id'] ) . '" type="checkbox" name="tax_input[captivate_category][]" id="in-captivate_category-' . esc_attr( $inserted_cat['term_id'] ) . '" checked="checked">' . esc_html( $term->name ) . '</label></li>';

					$args = array(
						'show_option_all'   => '',
						'show_option_none'  => '— Parent Category —',
						'option_none_value' => '-1',
						'orderby'           => 'name',
						'order'             => 'ASC',
						'show_count'        => 0,
						'hide_empty'        => 0,
						'child_of'          => 0,
						'exclude'           => '',
						'include'           => '',
						'echo'              => 0,
						'selected'          => 0,
						'hierarchical'      => 1,
						'name'              => 'category_parent',
						'id'                => '',
						'class'             => 'form-control',
						'depth'             => 0,
						'tab_index'         => 0,
						'taxonomy'          => 'captivate_category',
						'hide_if_empty'     => false,
						'value_field'       => 'term_id',
					);

					$json['cat_parent'] = wp_dropdown_categories( $args );

					if ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) {
						$output = json_encode( $json );
					} else {
						$output = 'error';
					}

				endif;

			}

			echo $output;

			wp_die();
		}

		/**
		 * Add tags
		 *
		 * @since 1.0
		 * @return string
		 */
		public static function add_tags() {

			if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( $_POST['_nonce'], '_cfm_nonce' ) ) {
				echo 'error';
			} else {

				$tags = isset( $_POST['tags'] ) ? sanitize_text_field( wp_unslash( $_POST['tags'] ) ) : array();

				if ( ! empty( $tags ) ) :

					$separated_tags = explode( ',', $tags );

					foreach ( $separated_tags as $tag ) {
						$inserted_tag = wp_insert_term( $tag, 'captivate_tag' ); // optional insert without saving the post.

						$term = get_term_by( 'id', $inserted_tag['term_id'], 'captivate_tag' );

						echo '<li id="captivate_tag-' . esc_attr( $inserted_tag['term_id'] ) . '"><label class="selectit"><input value="' . esc_attr( $inserted_tag['term_id'] ) . '" type="checkbox" name="tax_input[captivate_tag][]" id="in-captivate_tag-' . esc_attr( $inserted_tag['term_id'] ) . '" checked="checked">' . esc_html( $term->name ) . '</label></li>';
					}

				endif;

			}

			wp_die();
		}

		/**
		 * Manage snippets
		 *
		 * @since 2.0.0
		 * @return string
		 */
		public static function manage_snippets() {

			$output .= '';

			if ( isset( $_POST['_nonce'] ) && wp_verify_nonce( $_POST['_nonce'], '_cfm_nonce' ) ) {

				$show_id = ( '' != $_POST['show_id'] ) ? sanitize_text_field( wp_unslash( $_POST['show_id'] ) ) : '';

				$snippets = cfm_get_snippets( $show_id, 'snippets' );
				$snippets_count = cfm_get_snippets( $show_id, 'count' );
				$html_create_btn = ( $snippets_count < 5 ) ? '<button class="btn btn-light cfm-snippet-edit" data-id=""><i class="fal fa-plus"></i><span> Create</span></button>' : '';

				$output .= '<div class="row align-items-center mb-4"><div class="col-sm-9 cfm-snippets-count"><span>You are currently using ' . esc_html( $snippets_count ) . ' / 5 of your snippets allowance.</span></div><div class="col-sm-3 text-right">' . $html_create_btn . '</div></div>';

				if ( $snippets_count > 0 ) {
					foreach ( $snippets as $s ) {
						$delete_nonce = wp_create_nonce( 'delete_snippet_' . $s->id );
						$output .= '<div id="snippet-' . esc_attr( $s->id ). '" class="row align-items-center pt-2 pb-2"><div class="col-sm-8"><p>' . esc_html( $s->title ) . '</p></div><div class="col-sm-4 text-right"><button class="btn btn-light cfm-snippet-edit" data-id="' . esc_attr( $s->id ) . '" data-title="' . esc_attr( $s->title ) . '" data-content="' . esc_attr( $s->snippet ) . '"><i class="fal fa-edit"></i><span> Edit</span></button><button class="btn btn-outline-danger cfm-snippet-delete ml-2" data-nonce="' . esc_attr( $delete_nonce ) . '" data-id="' . esc_attr( $s->id ) . '"><i class="fal fa-trash-alt"></i></button></div></div>';
					}
				}
				else {
					$output .= '<div class="cfm-snippets-empty text-center pt-5 pb-5 mt-5 mb-5"><p>You currently have no snippets</p>';
					$output .= '<button class="btn btn-light cfm-snippet-edit" data-id=""><span>Create Your First</span></button></div>';
				}
			}

			echo $output;

			wp_die();
		}

		/**
		 * Create/edit snippets
		 *
		 * @since 2.0.0
		 * @return string
		 */
		public static function edit_snippet() {

			$output = 'error';
			$snippet_update_api = 'error';

			if ( isset( $_POST['_nonce'] ) && wp_verify_nonce( $_POST['_nonce'], '_cfm_nonce' ) ) {

				$show_id = ( '' != $_POST['show_id'] ) ? sanitize_text_field( wp_unslash( $_POST['show_id'] ) ) : '';
				$snippet_id = ( '' != $_POST['snippet_id'] ) ? sanitize_text_field( wp_unslash( $_POST['snippet_id'] ) ) : '';
				$snippet_title = sanitize_text_field( wp_unslash( $_POST['snippet_title'] ) );
				$snippet_content = wp_unslash( wp_filter_post_kses( $_POST['snippet_content'] ) );
				$snippet_textarea = wp_unslash( wp_filter_post_kses( $_POST['snippet_textarea'] ) );

				$snippets = cfm_get_snippets( $show_id, 'snippets' );
				$snippets_count = cfm_get_snippets( $show_id, 'count' );

				$snippet_info = array();
				$snippet_info['title'] = $snippet_title;
				$snippet_info['snippet'] = $snippet_content;

				if ( '' === $show_id || '' == $snippet_title || '' == $snippet_textarea || '<p><br></p>' == $snippet_textarea ) {
					// terminate if no show id or empty required fields.
					$output = 'req_fields';
				}
				else {
					// terminate if max snippets reached on insert.
					if ( '' === $snippet_id && $snippets_count >= 5 ) {
						$output = 'max_snippets_reached';
					}
					else {

						if ( '' === $snippet_id ) {

							// insert snippet.
							$insert_snippet = wp_remote_post(
								CFMH_API_URL . '/shows/' . $show_id . '/snippets/',
								array(
									'timeout' => 500,
									'body'    => $snippet_info,
									'headers' => array(
										'Authorization' => 'Bearer ' . get_transient( 'cfm_authentication_token' ),
									),
								)
							);

							// Debugging.
							if ( cfm_is_debugging_on() ) {
								$log_date = date( 'Y-m-d H:i:s', time() );
								$txt = '**CREATE SNIPPET - ' . $log_date . '**' . PHP_EOL . print_r( $insert_snippet, true ) . '**END CREATE SNIPPET**';
								$myfile = file_put_contents( CFMH . '/logs.txt', PHP_EOL . $txt . PHP_EOL , FILE_APPEND | LOCK_EX );
							}

							if ( ! is_wp_error( $insert_snippet ) && 'Unauthorized' !== $insert_snippet['body'] && is_array( $insert_snippet ) ) {

								$snippet_update_api = 'success';
							}

						}
						else {
							// edit snippet.
							$snippet_info['id'] = $snippet_id;
							$update_snippet = wp_remote_request(
								CFMH_API_URL . '/shows/' . $show_id . '/snippets/' . $snippet_id,
								array(
									'timeout' => 500,
									'body'    => $snippet_info,
									'method'  => 'PUT',
									'headers' => array(
										'Authorization' => 'Bearer ' . get_transient( 'cfm_authentication_token' ),
									),
								)
							);

							// Debugging.
							if ( cfm_is_debugging_on() ) {
								$log_date = date( 'Y-m-d H:i:s', time() );
								$txt = '**EDIT SNIPPET - ' . $log_date . '**' . PHP_EOL . print_r( $update_snippet, true ) . '**END EDIT SNIPPET**';
								$myfile = file_put_contents( CFMH . '/logs.txt', PHP_EOL . $txt . PHP_EOL , FILE_APPEND | LOCK_EX );
							}

							if ( ! is_wp_error( $update_snippet ) && 'Unauthorized' !== $update_snippet['body'] && is_array( $update_snippet ) ) {

								$snippet_update_api = 'success';
							}
						}

						if ( 'success' == $snippet_update_api ) {
							$output = 'success';
						}
					}
				}
			}

			echo $output;

			wp_die();
		}

		public static function delete_snippet() {

			$output = 'error';

			$show_id = ( '' != $_POST['show_id'] ) ? sanitize_text_field( wp_unslash( $_POST['show_id'] ) ) : '';
			$snippet_id = ( '' != $_POST['snippet_id'] ) ? sanitize_text_field( wp_unslash( $_POST['snippet_id'] ) ) : '';

			if ( isset( $_POST['_nonce'] ) && wp_verify_nonce( $_POST['_nonce'], 'delete_snippet_' . $snippet_id ) ) {

				$delete_snippet = wp_remote_request(
					CFMH_API_URL . '/shows/' . $show_id . '/snippets/' . $snippet_id,
					array(
						'timeout' => 500,
						'method'  => 'DELETE',
						'headers' => array(
							'Authorization' => 'Bearer ' . get_transient( 'cfm_authentication_token' ),
						),
					)
				);

				// Debugging.
				if ( cfm_is_debugging_on() ) {
					$log_date = date( 'Y-m-d H:i:s', time() );
					$txt = '**DELETE SNIPPET - ' . $log_date . '** ' . PHP_EOL . print_r( $delete_snippet, true ) . '**END DELETE SNIPPET**';
					$myfile = file_put_contents( CFMH . '/logs.txt', PHP_EOL . $txt . PHP_EOL , FILE_APPEND | LOCK_EX );
				}

				if ( ! is_wp_error( $delete_snippet ) && 'Unauthorized' != $delete_snippet['body'] && is_array( $delete_snippet ) ) {

					$output = 'success';

				}
			}

			echo $output;

			wp_die();
		}

		public static function ql_load_snippets() {

			$json['output'] = 'error';

			if ( isset( $_POST['_nonce'] ) && wp_verify_nonce( $_POST['_nonce'], '_cfm_nonce' ) ) {

				$show_id = ( '' != $_POST['show_id'] ) ? sanitize_text_field( wp_unslash( $_POST['show_id'] ) ) : '';

				$ql_toolbar_snippets = cfm_get_snippets( $show_id, 'snippets' );
				$ql_toolbar_snippets_count = cfm_get_snippets( $show_id, 'count' );

				if ( $ql_toolbar_snippets_count > 0 ) {
					foreach ( $ql_toolbar_snippets as $s ) {
						$ql_snippets .= '<option value="' . esc_attr( $s->snippet ) . '">' . esc_attr( $s->title ) . '</option>';
						$ql_snippets2 .= '<span tabindex="0" role="button" class="ql-picker-item" data-value="' . esc_attr( $s->snippet ) . '" data-label="' . esc_attr( $s->title ) . '"></span>';
					}
				}

				$json['output'] = 'success';
				$json['ql_snippets'] = $ql_snippets;
				$json['ql_snippets2'] = $ql_snippets2;
			}

			if ( ! empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) {
				$output = json_encode( $json );
			}

			echo $output;

			wp_die();
		}

	}

endif;
