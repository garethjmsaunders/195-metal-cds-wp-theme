<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package imagegridly
 */

?>
		<!--<?php // if ( has_post_thumbnail() ) : ?>
		<div class="featured-thumbnail">
			<?php // the_post_thumbnail('imagegridly-slider'); ?>
		</div>
	<?php // endif; ?>-->
<article id="post-<?php the_ID(); ?>" <?php post_class('posts-entry fbox'); ?>>
	<header class="entry-header">

		<?php
		if ( is_singular() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;

		if ( 'post' === get_post_type() ) : ?>

		<!-- .post-categories -->
		<div class="single entry-content">
			<p class="post-categories">
			<!-- List categories -->
			<?php
				$page_id = get_queried_object_id();
				echo get_the_category_list(' | ','',$page_id);
			?>
			</p>
			<!-- Score -->
			<p>
				<?php
					$review_score = get_post_meta( get_the_ID(), '195metalcds-score', true );

					if ($review_score) {
					    echo('<p class="top-review-score">' . $review_score . '%</p>');
					} else {
					    // NOTHING
					}
				?>
			</p>
		</div>


		<div class="entry-meta">
			<div class="blog-data-wrapper">
				<div class="post-data-divider"></div>
				<div class="post-data-positioning">
					<div class="post-data-text">
						<?php imagegridly_posted_on(); ?>
				</div>	
				</div>
			</div>
		</div><!-- .entry-meta -->



		<?php
		endif; ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>

	</div><!-- .entry-content -->
</article><!-- #post-<?php the_ID(); ?> -->
