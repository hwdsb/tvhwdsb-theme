<?php
/**
 * @package tvhwdsb
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<div class="entry-meta">
			<?php gazette_entry_meta(); ?>
		</div><!-- .entry-meta -->

		<?php hwdsb_vp_the_video(); ?>

		<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>

		<div class="entry-meta">
			<?php gazette_posted_on(); ?>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<div class="entry-summary">
		<?php the_content(); ?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php
			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', __( ', ', 'tvhwdsb' ) );
			if ( $tags_list ) {
				printf( '<span class="tags-links">%1$s</span>', $tags_list );
			}
		?>
	</footer><!-- .entry-footer -->

</article><!-- #post-## -->
