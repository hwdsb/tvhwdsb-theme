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
			if ( true === function_exists( 'mexp_vimeo_get_shortcode_tag' ) ) {
				$autoplay = ! empty( $_GET['playlist'] ) ? ' autoplay="1"' : '';
				$content = '[' . mexp_vimeo_get_shortcode_tag() . ' height="360" id="' . $meta['vp_video_id'][0] . '"' . $autoplay . ']';
				$media = do_shortcode( $content );

			// Old way.
			} else {
				$content = '[video src="https://vimeo.com/' . $meta['vp_video_id'][0] . '"]';

				$content = apply_filters( 'the_content', $content );
				$media = get_media_embedded_in_content( $content, array( 'video', 'object', 'embed', 'iframe' ) );
				if ( ! empty( $media ) ) {
					$media = $media[0];
				}
			}
			break;
	}

	if ( ! empty( $media ) ) {
		printf( '<div class="post-media jetpack-video-wrapper">%s</div>', $media );
?>

<script>
/*! fluidvids.js v2.4.1 | (c) 2014 @toddmotto | License: MIT | https://github.com/toddmotto/fluidvids */
!function(e,t){"function"==typeof define&&define.amd?define(t):"object"==typeof exports?module.exports=t:e.fluidvids=t()}(this,function(){"use strict";function e(e){return new RegExp("^(https?:)?//(?:"+d.players.join("|")+").*$","i").test(e)}function t(e,t){return parseInt(e,10)/parseInt(t,10)*100+"%"}function i(i){if((e(i.src)||e(i.data))&&!i.getAttribute("data-fluidvids")){var n=document.createElement("div");i.parentNode.insertBefore(n,i),i.className+=(i.className?" ":"")+"fluidvids-item",i.setAttribute("data-fluidvids","loaded"),n.className+="fluidvids",n.style.paddingTop=t(i.height,i.width),n.appendChild(i)}}function n(){var e=document.createElement("div");e.innerHTML="<p>x</p><style>"+o+"</style>",r.appendChild(e.childNodes[1])}var d={selector:["iframe","object"],players:["www.youtube.com","player.vimeo.com"]},o=[".fluidvids {","width: 100%; max-width: 100%; position: relative;","}",".fluidvids-item {","position: absolute; top: 0px; left: 0px; width: 100%; height: 100%;","}"].join(""),r=document.head||document.getElementsByTagName("head")[0];return d.render=function(){for(var e=document.querySelectorAll(d.selector.join()),t=e.length;t--;)i(e[t])},d.init=function(e){for(var t in e)d[t]=e[t];d.render(),n()},d});

// init
fluidvids.init({
	selector: ['iframe'],
	players: ['.'] // remove default youtube / vimeo restriction.
});
</script>

<?php
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
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	remove_action( 'wp_enqueue_scripts', 'gazette_post_nav_background' );

	// Jetpack Featured Content - Override Gazette to use our CPT.
	add_theme_support( 'featured-content', array(
		'filter'      => 'gazette_get_featured_posts',
		'description' => __( 'The featured content section displays on the front page above the header.', 'gazette' ),
		'post_types'  => array( 'vp_video' ),
		'max_posts'   => 6,
	) );
}
add_action( 'after_setup_theme', 'hwdsb_tv_after_setup_theme', 20 );

/**
 * Remove header image on all pages except the homepage.
 *
 * @param  bool $retval Current setting.
 * @return bool
 */
function hwdsb_remove_header_image_on_video_page( $retval ) {
	if ( ! is_home() ) {
		return false;
	}

	return $retval;
}
add_filter( 'theme_mod_header_image', 'hwdsb_remove_header_image_on_video_page' );

/**
 * Override content width from parent theme, Gazette.
 */
function gazette_content_width() {
	global $content_width;

	// 644 is content width with sidebar.
	if ( is_singular() && ! is_page() ) {
		$content_width = 644;

	// @todo Why is this set to 869?
	} elseif ( is_page() ) {
		$content_width = 869;
	}
}

/**
 * Enqueue scripts and styles.
 */
function hwdsb_tv_enqueue_scripts() {
	// Enqueue parent style.
	wp_enqueue_style( 'gazette-parent-style', get_template_directory_uri() . '/style.css' );

	// Enqueue our stylesheet so we can bust CSS cache.
	wp_enqueue_style( 'tvhwdsb', get_stylesheet_uri(), array(), '20170510' );
}
add_action( 'wp_enqueue_scripts', 'hwdsb_tv_enqueue_scripts' );

/**
 * Dequeue scripts and styles.
 */
function hwdsb_tv_dequeue_scripts() {
	// Dequeue Gazette's style.css since we are manually doing it ourselves.
	wp_dequeue_style( 'gazette-style' );
}
add_action( 'wp_enqueue_scripts', 'hwdsb_tv_dequeue_scripts', 20 );

/**
 * Adds some logged-in user links to the top nav menu.
 */
function hwsdsb_nav_menu_author_links( $menu ) {
	if( ! is_user_logged_in() ){
		return $menu;
	}

	$videos_link    = get_author_posts_url( get_current_user_id() );
	$playlists_link = $videos_link . 'playlists/';

	$markup = <<<EOD

	<li class="menu-item"><a href="{$videos_link}" >My Videos</a></li>
	<li class="menu-item"><a href="{$playlists_link}" >My Playlists</a></li>

EOD;
	return $markup . $menu;
}
add_filter( 'wp_nav_menu_items', 'hwsdsb_nav_menu_author_links' );

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
	if ( 'gazette-featured-content-thumbnail' === $size ) {
		$size = '960x541';
		$attr = ' class="attachment-gazette-featured-content-thumbnail size-gazette-featured-content-thumbnail wp-post-image"';
	} else {
		$size = '295x166';
		$attr = '';
	}

	//print_r( $GLOBALS['_wp_additional_image_sizes'][$size] );

	return "<img src=\"https://i.vimeocdn.com/video/{$meta['vp_video_vimeo_picture_id'][0]}_{$size}.jpg?r=pad\"{$attr}/><span class=\"duration\">{$duration}</span>";
}
add_filter( 'post_thumbnail_html', 'hwdsb_vp_filter_thumbnail_html', 10, 5 );

/**
 * Jetpack Featured Content JS needs this to set the background-image.
 */
function hwdsb_vp_featured_content_post_class( $classes ) {
	if ( did_action( 'tvhwdsb_after_featured_content' ) || ! is_home() ) {
		return $classes;
	}

	if ( false === gazette_get_featured_posts() ) {
		return $classes;
	}

	$classes[] = 'format-image';
	return $classes;
}
add_filter( 'post_class', 'hwdsb_vp_featured_content_post_class' );

/**
 * Add BuddyPress' 'template_notices' hook to the post author's page.
 */
function hwdsb_vp_add_template_notices_hook_to_author_page() {
	if ( ! is_author() ) {
		return;
	}

	// Add BP's template notices hook.
	do_action( 'template_notices' );

	// Don't do this again.
	remove_action( 'get_template_part_content', 'hwdsb_vp_add_template_notices_hook_to_author_page' );
}
add_action( 'get_template_part_content', 'hwdsb_vp_add_template_notices_hook_to_author_page' );

/**
 * Add custom image for Jetpack.
 *
 * @link https://jetpack.com/2013/10/15/add-a-default-fallback-image-if-no-image/
 */
function hwdsb_vp_jetpack_custom_image( $media, $post_id, $args ) {
	if ( $media ) {
		return $media;
	} else {
		// Fallback image.
		$url = apply_filters( 'jetpack_photon_url', get_stylesheet_directory_uri() . '/images/hwdsbtv-preview-replace.png' );

		$meta = get_post_meta( $post_id );

		// Use our custom thumbnail if available.
		if ( ! empty( $meta['vp_video_vimeo_picture_id'][0] ) && ( 'vimeo' === $meta['vp_video_source'][0] || 'local' === $meta['vp_video_source'][0] ) ) {
			$url = "https://i.vimeocdn.com/video/{$meta['vp_video_vimeo_picture_id'][0]}_295x166.jpg?r=pad";
		}

		return array( array(
			'type'  => 'image',
			'from'  => 'custom_fallback',
			'src'   => esc_url( $url ),
			'href'  => get_permalink( $post_id ),
		) );
	}
}
add_filter( 'jetpack_images_get_images', 'hwdsb_vp_jetpack_custom_image', 10, 3 );

/**
 * Tell Jetpack about our custom post type.
 */
function hwdsb_vp_jetpack_register_cpt( $cpt ) {
	$cpt[] = 'vp_video';
	return $cpt;
}
add_filter( 'rest_api_allowed_post_types', 'hwdsb_vp_jetpack_register_cpt' );

/**
 * Remove Jetpack's related posts from 'the_content' filter.
 */
function hwdsb_jetpack_remove_related_posts_from_post_content() {
	if ( false === class_exists( 'Jetpack_RelatedPosts', false ) ) {
		return;
	}

	$jprp = Jetpack_RelatedPosts::init();
	$callback = array( $jprp, 'filter_add_target_to_dom' );
	remove_filter( 'the_content', $callback, 40 );
}
add_filter( 'wp', 'hwdsb_jetpack_remove_related_posts_from_post_content', 20 );

/**
 * Add Jetpack's related posts block to the top of the sidebar.
 */
function hwdsb_jetpack_add_related_posts_to_sidebar() {
	if ( false === is_singular( 'vp_video' ) ) {
		return;
	}

	if ( class_exists( 'Jetpack_RelatedPosts' ) ) {
		echo '<aside id="recent-videos" class="widget recent_videos">';
		echo do_shortcode( '[jetpack-related-posts]' );
		echo '</aside>';
	}
}
add_action( 'dynamic_sidebar_before', 'hwdsb_jetpack_add_related_posts_to_sidebar' );

/**
 * Alter Jetpack's related posts heading to 'Related Videos'
 */
function hwdsb_jetpack_related_posts_alter_heading( $headline ) {
	return sprintf( '<h2 class="widget-title">%s</h2>', esc_html( 'Related Videos' ) );
}
add_filter( 'jetpack_relatedposts_filter_headline', 'hwdsb_jetpack_related_posts_alter_heading' );

/**
 * Alter Jetpack's related posts byline.
 */
function hwdsb_jetpack_related_posts_filter_post_context( $retval ) {
	if ( 'Similar post' === $retval ) {
		$retval = 'Similar video';
	}

	return $retval;
}
add_filter( 'jetpack_relatedposts_filter_post_context', 'hwdsb_jetpack_related_posts_filter_post_context' );

/**
 * Add playlist video loop on a video page with the 'playlist' URL query var.
 */
function hwdsb_vp_playlist_loop_add_to_sidebar() {
	if ( false === is_singular( 'vp_video' ) || false === function_exists( 'vp_have_playlist_videos' ) ) {
		return;
	}

	if ( false === vp_have_playlist_videos() ) {
		return;
	}

	echo '<aside id="widget-playlist" class="widget widget_archive">';
	vp_get_template_part( 'widget-playlist_video' );
	echo '</aside>';
}
add_action( 'dynamic_sidebar_before', 'hwdsb_vp_playlist_loop_add_to_sidebar', 0 );

/**
 * Force Social Warface buttons to render on unlisted posts.
 *
 * Hooks into SW's post meta call for 'nc_floatLocation' to force the post
 * status to publish.  See {@link social_warfare_buttons()}.
 *
 * @param null|array|string  $value     The value get_metadata() should return - a single metadata value,
 *                                      or an array of values.
 * @param  int               $object_id Object ID.
 * @param  string            $meta_key  Meta key.
 * @param  bool              $single    Whether to return only the first value of the specified $meta_key.
 * @return mixed
 */
function hwdsb_vp_force_social_warface_on_unlisted_posts( $retval, $post_id, $meta_key, $single ) {
	if ( 'nc_floatLocation' !== $meta_key || false === class_exists( 'Ray_Unlisted_Posts', false ) ) {
		return $retval;
	}

	$post = get_post( $post_id );
	if ( 'vp_video' !== $post->post_type ) {
		return $retval;
	}

	if ( false === Ray_Unlisted_Posts::is_unlisted( $post_id ) ) {
		return $retval;
	}

	// Force post status to publish.
	add_filter( 'get_post_status', 'hwdsb_vp_force_post_status_to_publish', 10, 2 );

	return $retval;
}
add_filter( 'get_post_metadata', 'hwdsb_vp_force_social_warface_on_unlisted_posts', 10, 4 );

/**
 * Force post status to publish.
 *
 * @param  string  $retval Current post status.
 * @param  WP_Post $post   Current post.
 * @return string
 */
function hwdsb_vp_force_post_status_to_publish( $retval, $post ) {
	remove_filter( 'get_post_status', 'hwdsb_vp_force_post_status_to_publish', 10 );
	return 'publish';
}
// @todo Perhaps change the /author/ slug to something else?
// @link http://wordpress.stackexchange.com/a/82219
