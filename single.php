<?php setviews(get_the_ID()); ?>
<?php get_header(); ?>

		<main class="row">
<?php if (getvalue('sidebar_on_posts', false, false) == 'left'): ?>
			<section class="col-sm-3">
<?php if (is_active_sidebar('side-bar')) dynamic_sidebar('side-bar'); ?>

			</section>
<?php endif; ?>
<?php if (getvalue('sidebar_on_posts', false, false) == 'none'): ?>
			<section class="col-sm-12">
<?php else: ?>
			<section class="col-sm-9">
<?php endif; ?>
<?php if (have_posts()): while (have_posts()) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>">
					<h2 class="post-title"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
<?php the_content(); ?>

				</article>
<?php endwhile; ?>
<?php else: ?>
				<article>
					<h2 class="post-title">No Posts</h2>
				</article>
<?php endif; ?>
			</section>
<?php if (getvalue('sidebar_on_posts', false, false) == 'right'): ?>
			<section class="col-sm-3">
<?php if (is_active_sidebar('side-bar')) dynamic_sidebar('side-bar'); ?>

			</section>
<?php endif; ?>		</main>
<?php get_footer(); ?>