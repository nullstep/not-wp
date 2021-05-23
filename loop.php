<?php if (have_posts()): while (have_posts()) : the_post(); ?>
				<article>
					<h3 class="page-title"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h3>
<?php if (getvalue('show_post_date') == 'yes'): ?>
					<p class="post-datetime"><span class="post-date"><?php the_time(get_option('date_format')); ?></span> - <span class="post-time"><?php the_time(); ?></span></p>
<?php endif; ?>
					<p class="post-excerpt"><?php the_excerpt(); ?></p>
				</article>
<?php endwhile; ?>
<?php else: ?>
				<article>
					<h3>No <?php echo getvalue('plural_post_name'); ?></h3>
				</article>
<?php endif; ?>