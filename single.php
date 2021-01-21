<?php setviews(get_the_ID()); ?>
<?php get_header(); ?>

		<main class="row">
			<section class="col-sm-9">
<?php if (have_posts()): while (have_posts()) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>">
					<h2>
						<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
					</h2>
<?php the_content(); ?>

				</article>
<?php endwhile; ?>
<?php else: ?>
				<article>
					<h2>No Posts</h2>
				</article>
<?php endif; ?>
			</section>
		</main>
<?php get_footer(); ?>