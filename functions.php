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
				$content = '[' . mexp_vimeo_get_shortcode_tag() . ' id="' . $meta['vp_video_id'][0] . '"]';
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
}
add_action( 'after_setup_theme', 'hwdsb_tv_after_setup_theme' );

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

// Add Fallback Featured Image if one doesn't exist already

function jeherve_custom_image( $media, $post_id, $args ) {
    if ( $media ) {
        return $media;
    } else {
        $permalink = get_permalink( $post_id );
	$url = apply_filters( 'jetpack_photon_url', get_stylesheet_directory_uri() . '/images/hwdsbtv-preview-replace.png' );
     
        return array( array(
            'type'  => 'image',
            'from'  => 'custom_fallback',
            'src'   => esc_url( $url ),
            'href'  => $permalink,
        ) );
    }
}
add_filter( 'jetpack_images_get_images', 'jeherve_custom_image', 10, 3 );

// Add Jetpack Related Post Functionality to the VP_Video CPT

function allow_my_post_types($allowed_post_types) {
    $allowed_post_types[] = 'vp_video';
    return $allowed_post_types;
}
add_filter( 'rest_api_allowed_post_types', 'allow_my_post_types' );

// @todo Perhaps change the /author/ slug to something else?
// @link http://wordpress.stackexchange.com/a/82219
