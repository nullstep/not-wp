<?php get_header(); ?>

		<main class="row">
<?php if (getvalue('sidebar_on_feeds', false, false) == 'left'): ?>
			<section class="col-sm-3">
<?php if (is_active_sidebar('side-bar')) dynamic_sidebar('side-bar'); ?>

			</section>
<?php endif; ?>
<?php if (getvalue('sidebar_on_feeds', false, false) == 'none'): ?>
			<section class="col-sm-12">
<?php else: ?>
			<section class="col-sm-9">
<?php endif; ?>
				<h2 class="feed-title"><?php single_cat_title(); ?></h2>
				<?php the_archive_description('<div class="description">', '</div>'); ?>
<?php get_template_part('loop'); ?>
<?php get_template_part('pagination'); ?>

			</section>
<?php if (getvalue('sidebar_on_feeds', false, false) == 'right'): ?>
			<section class="col-sm-3">
<?php if (is_active_sidebar('side-bar')) dynamic_sidebar('side-bar'); ?>

			</section>
<?php endif; ?>
		</main>
<?php get_footer(); ?>