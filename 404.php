<?php get_header(); ?>

		<main class="row">
			<section class="col-sm-9">
				<article id="post-404">
					<h1>404</h1>
					<h2>
						<a href="<?php echo home_url(); ?>">Sorry that page was not found. Click here to return to the home-page.</a>
					</h2>
				</article>
			</section>
			<section class="col-sm-3">
<?php get_sidebar(); ?>

			</section>
		</main>
<?php get_footer(); ?>