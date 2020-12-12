<?php get_header(); ?>

		<main class="row">
			<section class="col-sm-9">
<?php if (have_posts()): the_post(); ?>
				<h1>Author Archives for <?php echo get_the_author(); ?></h1>
<?php if (get_the_author_meta('description')) : ?>
<?php echo get_avatar(get_the_author_meta('user_email')); ?>
				<h2>About <?php echo get_the_author(); ?></h2>
				<?php echo wpautop(get_the_author_meta('description')); ?>
<?php endif; ?>
<?php rewind_posts(); while (have_posts()) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php if ( has_post_thumbnail()) : ?>
					<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
						<?php the_post_thumbnail([120, 120]); ?>
					</a>
<?php endif; ?>
					<h2>
						<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
					</h2>
					<span class="date"><?php the_time('F j, Y'); ?> <?php the_time('g:i a'); ?></span>
					<span class="author">Written by <?php the_author_posts_link(); ?></span>
					<span class="comments"><?php comments_popup_link('Leave your thoughts', '1 Comment', '% Comments'); ?></span>
					<?php excerpts('excerpt'); ?>

					<br class="clear">
				</article>
<?php endwhile; ?>
<?php else: ?>
				<article>
					<h2>No Posts</h2>
				</article>
<?php endif; ?>
<?php get_template_part('pagination'); ?>

			</section>
			<section class="col-sm-3">
<?php get_sidebar(); ?>

			</section>
		</main>
<?php get_footer(); ?>