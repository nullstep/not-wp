<?php setviews(get_the_ID()); ?>
<?php get_header(); ?>

		<main class="row">
			<section class="col-sm-9">
				<h1><?php the_title(); ?></h1>
<?php if (have_posts()): while (have_posts()) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>">
<?php the_content(); ?>

				</article>
<?php endwhile; ?>
<?php else: ?>
				<article>
					<h1>No Content</h1>
				</article>
<?php endif; ?>
			</section>
			<section class="col-sm-3">
<?php get_sidebar(); ?>

			</section>
		</main>
<?php get_footer(); ?>