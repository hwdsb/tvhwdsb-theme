<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Gazette
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<?php
					the_archive_title( '<h1 class="page-title">', '</h1>' );
					the_archive_description( '<div class="taxonomy-description">', '</div>' );
				?>

				<?php if ( is_author() ) : ?>
					<div class="tab-container">
						<ul class="tabs">
							<li class="tab-link <?php echo ! get_query_var( 'vp_author_id' ) ? 'current' : ''; ?>"><a href="<?php echo get_author_posts_url( get_query_var( 'vp_author_id' ) ? get_query_var( 'vp_author_id' ) : get_queried_object()->ID ); ?>">Videos</a></li>
							<li class="tab-link <?php echo get_query_var( 'vp_author_id' ) ? 'current' : ''; ?>"><a href="<?php echo get_author_posts_url( get_query_var( 'vp_author_id' ) ? get_query_var( 'vp_author_id' ) : get_queried_object()->ID ) . 'playlists/'; ?>">Playlists</a></li>
						</ul>
					</div>
					
				<?php endif; ?>

			</header><!-- .page-header -->

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<?php
					/* Include the Post-Format-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					get_template_part( 'content', get_post_format() );
				?>

			<?php endwhile; ?>

			<?php the_posts_navigation(); ?>

		<?php else : ?>

			<?php get_template_part( 'content', 'none' ); ?>

		<?php endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>