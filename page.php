<?php setviews(get_the_ID()); ?>
<?php get_header(); ?>

			<main class="row">
				<?php if (is_active_sidebar('page-top')) dynamic_sidebar('page-top'); ?>

<?php if (getvalue('sidebar_on_pages') == 'left'): ?>
				<section class="col-sm-3">
<?php if (is_active_sidebar('side-bar')) dynamic_sidebar('side-bar'); ?>

				</section>
<?php endif; ?>
<?php if (getvalue('sidebar_on_pages') == 'none'): ?>
				<section class="col-sm-12">
<?php else: ?>
				<section class="col-sm-9">
<?php endif; ?>
<?php if (have_posts()): while (have_posts()) : the_post(); ?>
					<article>
<?php if (getvalue('show_page_titles') != 'none'): ?>
						<<?php echo getvalue('show_page_titles'); ?> class="page-title"><?php the_title(); ?></<?php echo getvalue('show_page_titles'); ?>>
<?php endif; ?>
<?php the_content(); ?>

					</article>
<?php endwhile; ?>
<?php else: ?>
					<article>
						<h2>No Content</h2>
					</article>
<?php endif; ?>
				</section>
<?php if (getvalue('sidebar_on_pages') == 'right'): ?>
				<section class="col-sm-3">
<?php if (is_active_sidebar('side-bar')) dynamic_sidebar('side-bar'); ?>

				</section>
<?php endif; ?>
				<?php if (is_active_sidebar('page-bottom')) dynamic_sidebar('page-bottom'); ?>

			</main>
<?php get_footer(); ?>