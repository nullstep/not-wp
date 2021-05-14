<?php if (have_posts()): while (have_posts()) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>">
<?php if (getvalue('show_page_titles', false, false) != 'none'): ?>
					<<?php getvalue('show_page_titles'); ?> class="page-title"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></<?php getvalue('show_page_titles'); ?>>
<?php endif; ?>
				</article>
<?php endwhile; ?>
<?php else: ?>
				<article>
					<h2>No Posts</h2>
				</article>
<?php endif; ?>