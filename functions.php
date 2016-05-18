<?php

/** TEMPLATE TAGS ********************************************************/

/**
 * Output the video for the current post.
 */
function hwdsb_vp_the_video() {
	$meta = get_post_meta( get_the_ID() );

	if ( empty( $meta['vp_video_source'] ) ) {
		return;
	}

	$source = $meta['vp_video_source'][0];
	$content = '';

	switch( $source ) {
		case 'local' :
		case 'vimeo' :
			$content = '[video src="https://vimeo.com/' . $meta['vp_video_id'][0] . '"]';
			break;
	}

	$content = apply_filters( 'the_content', $content );
	$media = get_media_embedded_in_content( $content, array( 'video', 'object', 'embed', 'iframe' ) );
	if ( ! empty( $media ) ) {
		printf( '<div class="post-media jetpack-video-wrapper">%s</div>', $media[0] );
	} else {
		return;
	}
}

/**
 * Output the video metadata such as post author and relative time.
 *
 * Used on archive pages.
 */
function hwdsb_vp_the_video_metadata() {
	the_author_posts_link();
	echo ' &middot; ' . human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' ago';
}

/** HOOKS ****************************************************************/

/**
 * Do some stuff after the theme is set up.
 */
function hwdsb_tv_after_setup_theme() {
	remove_filter( 'the_excerpt', 'gazette_continue_reading', 9 );
}
add_action( 'after_setup_theme', 'hwdsb_tv_after_setup_theme' );

/**
 * Enqueue scripts and styles.
 */
function hwdsb_tv_enqueue_scripts() {
	// Enqueue parent style.
	wp_enqueue_style( 'gazette-parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'hwdsb_tv_enqueue_scripts' );

/**
 * Force has_post_thumbnail() to true for Video Portal videos.
 */
function hwdsb_vp_filter_thumbnail_id( $retval, $post_id, $meta_key, $single ) {
	if ( is_single() ) {
		return $retval;
	}

	if ( '_thumbnail_id' !== $meta_key ) {
		return $retval;
	}

	$post = get_post( $post_id );

	if ( 'vp_video' !== $post->post_type ) {
		return $retval;
	}

	return true;
}
add_filter( 'get_post_metadata', 'hwdsb_vp_filter_thumbnail_id', 10, 4 );

/**
 * Filter the thumbnail to use the direct Vimeo video thumbnail.
 */
function hwdsb_vp_filter_thumbnail_html( $retval, $post_id, $post_thumbnail_id, $size, $attr ) {
	$meta = get_post_meta( $post_id );

	if ( empty( $meta['vp_video_source'] ) ) {
		return $retval;
	}

	// Should fallback to a generic thumbnail here.
	if ( empty( $meta['vp_video_vimeo_picture_id'][0] ) ) {
		return $retval;
	}

	// Duration
	$duration = '';
	if ( true === function_exists( 'hwdsb_get_the_duration' ) ) {
		$duration = hwdsb_get_the_duration( $post_id );
	}

	// Down in the ghetto...
	//print_r( $GLOBALS['_wp_additional_image_sizes'][$size] );

	return "<img src=\"https://i.vimeocdn.com/video/{$meta['vp_video_vimeo_picture_id'][0]}_295x166.jpg?r=pad\" /><span class=\"duration\">{$duration}</span>";
}
add_filter( 'post_thumbnail_html', 'hwdsb_vp_filter_thumbnail_html', 10, 5 );

// @todo Perhaps change the /author/ slug to something else?
// @link http://wordpress.stackexchange.com/a/82219
