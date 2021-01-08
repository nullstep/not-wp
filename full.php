<?php /* Template Name: Full Width template */ ?>
<?php setviews(get_the_ID()); ?>
<?php get_header(); ?>

		<main class="row">
			<section class="col-sm-12">
				<h2><?php the_title(); ?></h2>
<?php if (have_posts()): while (have_posts()) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>">
<?php the_content(); ?>

				</article>
<?php endwhile; ?>
<?php else: ?>
				<article>
					<h2>No Content</h2>
				</article>
<?php endif; ?>
			</section>
		</main>
<?php get_footer(); ?>