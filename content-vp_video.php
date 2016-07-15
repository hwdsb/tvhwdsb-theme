<?php
/**
 * @package tvhwdsb
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php hwdsb_vp_the_video(); ?>

		<?php // Show message block about video processing. ?>
		<?php if ( '' === get_post_meta( get_queried_object_id(), 'vp_video_duration', true ) && 'local' === get_post_meta( get_queried_object_id(), 'vp_video_source', true ) ) : ?>
			<div class="alert alert-danger" role="alert">
				<strong>Your video is processing</strong> - You will receive an email once your video is ready to watch.
			</div>
		<?php endif; ?>

		<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>

		<div class="entry-meta">
			Posted by <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author_meta( 'display_name' ); ?></a>

			<?php printf( _x( '%s ago', '%s = human-readable time difference', 'tvhwdsb' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); ?>

		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<div class="entry-summary">

		<div class="tab-container">

			<ul class="tabs">
				<li class="tab-link current" data-tab="tab-1">About</li>

				<?php if ( 'publish' === get_post_status() || ( class_exists( 'Ray_Unlisted_Posts' ) && Ray_Unlisted_Posts::is_unlisted() ) ) : ?>

					<li class="tab-link" data-tab="tab-2">Embed</li>

					<?php if ( function_exists( 'social_warfare' ) ) : ?>
						<li class="tab-link" data-tab="tab-3">Share</li>
					<?php endif; ?>

				<?php endif; ?>
			</ul>

			<div id="tab-1" class="tab-content current">
				<?php the_content(); ?>

				<?php
					/* translators: used between list items, there is a space after the comma */
					$tags_list = get_the_tag_list( '', __( ', ', 'tvhwdsb' ) );
					if ( $tags_list ) {
						printf( '<span class="tags-links">%1$s</span>', $tags_list );
					}
				?>
			</div>

			<?php if ( 'publish' === get_post_status() || ( class_exists( 'Ray_Unlisted_Posts' ) && Ray_Unlisted_Posts::is_unlisted() ) ) : ?>

				<div id="tab-2" class="tab-content">
					<?php hwdsb_vp_the_embed_code(); ?>
				</div>

				<?php if ( function_exists( 'social_warfare' ) ) : ?>

					<div id="tab-3" class="tab-content">
						<?php social_warfare();	?>
					</div>

				<?php endif; ?>

			<?php endif; ?>

		</div><!-- container -->

	</div><!-- .entry-content -->

	<script>
	jQuery(function($){
		$('ul.tabs li').click(function(){
			var tab_id = $(this).attr('data-tab');

			$('ul.tabs li').removeClass('current');
			$('.tab-content').removeClass('current');

			$(this).addClass('current');
			$("#"+tab_id).addClass('current');
		})
	})
	</script>

</article><!-- #post-## -->
