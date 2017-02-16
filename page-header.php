<?php
/**
 * @package tvhwdsb
 */
?>

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

				<?php if ( is_tax( 'vp_playlist' ) && is_user_logged_in() && get_current_user_id() === (int) get_term_meta( get_queried_object()->term_id, 'author', true ) ) : ?>
					<div class="comment-navigation vp-playlist-edit"><a href="#" class="vp-playlist-edit" title="Edit" data-playlist-id="<?php echo (int) get_queried_object()->term_id; ?>"><span class="dashicons dashicons-admin-generic"></span></a></div>
				<?php endif; ?>

			</header><!-- .page-header -->
