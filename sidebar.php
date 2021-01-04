				<aside role="complementary">
<?php get_template_part('searchform'); ?>

					<div class="sidebar-widget">
						<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('sidebar-widget-area')) ?>

					</div>
				</aside>
				<aside>
					<h2 class="mt-3 mb-0">Latest Posts</h2>
					<?php latest_posts(5); ?>
				</aside>