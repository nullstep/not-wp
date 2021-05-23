<?php get_header(); ?>

		<main class="row">
			<section class="col-sm-9">
				<h2><?php
					$n = $wp_query->found_posts;
					$s = ($n == 1) ? '' : 's';
					echo $n . ' search result' . $s . ' for "' . get_search_query() . '"';
				?></h2>
<?php get_template_part('loop'); ?>
				<div class="pagination">
<?php pagination(); ?>

				</div>
			</section>
			<section class="col-sm-3">
<?php get_sidebar(); ?>

			</section>
		</main>
<?php get_footer(); ?>