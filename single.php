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
				<article>
<?php if (getvalue('show_page_titles', false, false) != 'none'): ?>
					<<?php getvalue('show_page_titles'); ?> class="page-title"><?php the_title(); ?></<?php getvalue('show_page_titles'); ?>>
<?php endif; ?>
<?php if (getvalue('show_post_date', false, false) == '1'): ?>
					<p class="post-datetime"><span class="post-date"><?php the_time(get_option('date_format')); ?></span> - <span class="post-time"><?php the_time(); ?></span></p>
<?php endif; ?>
<?php the_content(); ?>

				</article>
<?php endwhile; ?>
<?php else: ?>
				<article>
					<h2 class="post-title">No <?php getvalue('plural_post_name'); ?></h2>
				</article>
<?php endif; ?>
			</section>
<?php if (getvalue('sidebar_on_posts', false, false) == 'right'): ?>
			<section class="col-sm-3">
<?php if (is_active_sidebar('side-bar')) dynamic_sidebar('side-bar'); ?>

			</section>
<?php endif; ?>
		</main>
<?php get_footer(); ?>