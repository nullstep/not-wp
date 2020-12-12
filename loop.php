<?php if (have_posts()): while (have_posts()) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>">
					<h2>
						<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
					</h2>
				</article>
<?php endwhile; ?>
<?php else: ?>
				<article>
					<h2>No Posts</h2>
				</article>
<?php endif; ?>