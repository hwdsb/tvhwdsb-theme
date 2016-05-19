<?php
/**
 * The template for our custom 'vp_video' post type.
 *
 * @package tvhwdsb
 */

get_header(); ?>

	<div class="site-content-inner">
		<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main">

				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content', 'vp_video' ); ?>
					<?php if(function_exists('social_warfare')):
    						social_warfare();
						endif;
						?>
					<?php
						// If comments are open or we have at least one comment, load up the comment template
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;
					?>

					<?php
						// Previous/next post navigation.
						the_post_navigation( array(
							'next_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Next', 'tvhwdsb' ) . '</span> ' . '<span class="screen-reader-text">' . __( 'Next post:', 'tvhwdsb' ) . '</span> ' . '<span class="post-title">%title</span>',
							'prev_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Previous', 'tvhwdsb' ) . '</span> ' . '<span class="screen-reader-text">' . __( 'Previous post:', 'tvhwdsb' ) . '</span> ' . '<span class="post-title">%title</span>',
				) );
					?>

				<?php endwhile; // end of the loop. ?>

			</main><!-- #main -->
		</div><!-- #primary -->

		<?php get_sidebar(); ?>
	</div><!-- .site-content-inner -->

<?php get_footer(); ?>
