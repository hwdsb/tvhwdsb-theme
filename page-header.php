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

				<?php if ( is_tax( 'vp_playlist' ) ) : ?>
					<?php if ( function_exists( 'vp_playlist_is_user_collaborator' ) && vp_playlist_is_user_collaborator( get_current_user_id(), get_queried_object()->term_id ) && vp_playlist_is_collaborative( get_queried_object()->term_id ) ) : ?>
						<div class="alert alert-success" role="alert">
							You are a collaborator to this playlist. To add videos to this playlist, watch any video and click on the "Playlist" tab. Once there, you can add the current video to this playlist.
						</div>

					<?php endif; ?>

					Created by <?php vp_the_playlist_data( 'author_link', array( 'term' => get_queried_object() ) ); ?>.
				<?php endif; ?>

				<?php if ( is_tax( 'vp_playlist' ) && 'private' !== get_term_meta( get_queried_object()->term_id, 'privacy', true ) ) : ?>

					<div class="comment-navigation vp-playlist-embed"><a href="#" class="vp-playlist-embed" title="Embed" data-playlist-id="<?php echo (int) get_queried_object()->term_id; ?>"><span class="dashicons dashicons-share-alt2"></span></a></div>

				<?php endif; ?>

				<?php if ( is_tax( 'vp_playlist' ) && is_user_logged_in() && get_current_user_id() === (int) get_term_meta( get_queried_object()->term_id, 'author', true ) ) : ?>
					<div class="comment-navigation vp-playlist-edit"><a href="#" class="vp-playlist-edit" title="Edit" data-playlist-id="<?php echo (int) get_queried_object()->term_id; ?>"><span class="dashicons dashicons-admin-generic"></span></a></div>
				<?php endif; ?>

			</header><!-- .page-header -->

			<?php if ( get_query_var( 'author_playlists' ) ) : ?>

				<ul class="subtabs">
					<li class="tab-link <?php echo ! get_query_var( 'author_collaborate' ) ? 'current' : ''; ?>"><a href="<?php echo get_author_posts_url( get_query_var( 'vp_author_id' ) ? get_query_var( 'vp_author_id' ) : get_queried_object()->ID ) . 'playlists/'; ?>">Created By</a></li>
					<li class="tab-link <?php echo get_query_var( 'author_collaborate' ) ? 'current' : ''; ?>"><a href="<?php echo get_author_posts_url( get_query_var( 'vp_author_id' ) ? get_query_var( 'vp_author_id' ) : get_queried_object()->ID ) . 'playlists/collaborate/'; ?>">Collaborative</a></li>
				</ul>

			<?php endif; ?>