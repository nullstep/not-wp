<?php get_header(); ?>

		<main class="row">
			<section class="col-sm-9">
				<h1><?php
					$n = $wp_query->found_posts;
					$s = ($n == 1) ? '' : 's';
					echo $n . ' search result' . $s . ' for "' . get_search_query() . '"';
				?></h1>
<?php get_template_part('loop'); ?>
<?php get_template_part('pagination'); ?>

			</section>
			<section class="col-sm-3">
<?php get_sidebar(); ?>

			</section>
		</main>
<?php get_footer(); ?>