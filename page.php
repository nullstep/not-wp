<?php setviews(get_the_ID()); ?>
<?php get_header(); ?>

			<main class="row">
				<?php if (is_active_sidebar('page-top')) dynamic_sidebar('page-top'); ?>

				<section class="col-sm-12">
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
				<?php if (is_active_sidebar('page-bottom')) dynamic_sidebar('page-bottom'); ?>

			</main>
<?php get_footer(); ?>