<?php
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package imagegridly
 */

get_header(); ?>

	<div id="primary" class="featured-content content-area fullwidth-area-blog">
		<main id="main" class="site-main all-blog-articles">

		<?php
		if ( have_posts() ) : 
			$author_id = get_the_author_meta( 'ID' );
			?>

			<header class="fbox page-header author-archive">
				<h1 class="author-archive__page-title">Reviewer archive<br><span class="author-archive__page-title--author-name"><?php the_author(); ?></span></h1>
				<p class="author-archive__avatar-image"><?php echo get_avatar($author_id, 128); ?></p>
				<?php the_archive_description( '<div class="author-archive__description">', '</div>' );
				?>
				<hr class="author-archive__hr">
			</header>
			<!-- .page-header -->

			<?php
			/* Start the Loop */
			while ( have_posts() ) : the_post();

				/*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
				get_template_part( 'template-parts/content', get_post_format() );

			endwhile;

		echo '<div class="text-center pag-wrapper">';
				imagegridly_numeric_posts_nav();
				echo '</div>';
		else :

			get_template_part( 'template-parts/content', 'none' );

		endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
