<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="<?php bloginfo('description'); ?>">
	<title><?php echo trim(wp_title('', false)); ?><?php if (wp_title('', false)) { echo ' : '; } ?><?php bloginfo('name'); ?></title>
	<link href="<?php getfavicon(); ?>" rel="shortcut icon">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
<?php wp_head(); ?>
	<style>
		<?php getcolours(); ?><?php getvalue('theme_css_minified', true); ?>
	</style>
</head>
<?php $container = getvalue('container_class', false, false); ?>
<body id="override" class="<?php echo get_post_field('post_name'); ?>">
<?php if (getvalue('show_topbar', false, false) == 'yes'): ?>
	<div id="top-area">
		<div class="<?php echo $container; ?>">
			<div class="row">
				<?php if (is_active_sidebar('top-area')) dynamic_sidebar('top-area'); ?>

			</div>
		</div>
	</div>
<?php endif; ?>
	<div id="nav-area">
<?php get_template_part('nav'); ?>

	</div>
<?php if (is_front_page()): ?>
	<div id="header-area">
		<div class="<?php echo $container; ?>">
			<header class="row">
				<?php if (is_active_sidebar('header-area')) dynamic_sidebar('header-area'); ?>

			</header>
		</div>
	</div>
<?php endif; ?>
	<div id="content-area">
		<div class="<?php echo $container; ?>">