<?php get_header(); ?>

		<main class="row">
<?php if (getvalue('sidebar_on_feeds') == 'left'): ?>
			<section class="col-sm-3">
<?php if (is_active_sidebar('side-bar')) dynamic_sidebar('side-bar'); ?>

			</section>
<?php endif; ?>
<?php if (getvalue('sidebar_on_feeds') == 'none'): ?>
			<section class="col-sm-12">
<?php else: ?>
			<section class="col-sm-9">
<?php endif; ?>
				<h2 class="feed-title"><?php single_cat_title(); ?></h2>
<?php if (getvalue('show_cat_desc') == 'yes'): ?>
				<?php the_archive_description('<div class="description">', '</div>'); ?>
<?php endif; ?>
<?php get_template_part('loop'); ?>
				<div class="pagination">
<?php pagination(); ?>

				</div>
			</section>
<?php if (getvalue('sidebar_on_feeds') == 'right'): ?>
			<section class="col-sm-3">
<?php if (is_active_sidebar('side-bar')) dynamic_sidebar('side-bar'); ?>

			</section>
<?php endif; ?>
		</main>
<?php get_footer(); ?>