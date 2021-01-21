<?php /* Template Name: Sidebar template */ ?>
<?php setviews(get_the_ID()); ?>
<?php get_header(); ?>

			<main class="row">
				<section class="col-sm-9">
					<h2><?php the_title(); ?></h2>
<?php if (have_posts()): while (have_posts()) : the_post(); ?>
					<article>
<?php the_content(); ?>

					</article>
<?php endwhile; ?>
<?php else: ?>
					<article>
						<h2>No Content</h2>
					</article>
<?php endif; ?>
				</section>
				<section class="col-sm-3">
<?php get_sidebar(); ?>

				</section>
			</main>
<?php get_footer(); ?>