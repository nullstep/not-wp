<?php get_header(); ?>

		<main class="row">
			<section class="col-sm-9">
				<h1><?php single_cat_title(); ?></h1>
				<?php the_archive_description('<div class="description">', '</div>'); ?>
<?php get_template_part('loop'); ?>
<?php get_template_part('pagination'); ?>

			</section>
			<section class="col-sm-3">
<?php get_sidebar(); ?>

			</section>
		</main>
<?php get_footer(); ?>