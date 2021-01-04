<?php get_header(); ?>

		<main class="row">
			<section class="col-sm-9">
				<h2>Archives</h2>
<?php get_template_part('loop'); ?>
<?php get_template_part('pagination'); ?>

			</section>
			<section class="col-sm-3">
<?php get_sidebar(); ?>

			</section>
		</main>
<?php get_footer(); ?>