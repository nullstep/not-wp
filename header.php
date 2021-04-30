<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="<?php bloginfo('description'); ?>">

	<title><?php echo trim(wp_title('', false)); ?><?php if (wp_title('', false)) { echo ' : '; } ?><?php bloginfo('name'); ?></title>

	<link href="/uploads/<?php getvalue('favicon_image'); ?>" rel="shortcut icon">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
<?php wp_head(); ?>
	<style>
		<?php getcolours(); ?><?php getvalue('theme_css_minified', true); ?>
	</style>
</head>
<?php $container = getvalue('container_class', false, false); ?>
<body id="override" class="<?php echo get_post_field('post_name'); ?>">
	<div id="top-area">
		<div class="<?php echo $container; ?>">
			<div class="row">
				<div id="logo-area" class="col-sm-6">
					<a href="/"><img id="logo" src="/uploads/<?php getvalue('logo_image'); ?>" class="shrink"></a>
				</div>
				<div id="title-area" class="col-sm-6">
					<p class="my-3"><?php bloginfo('description'); ?></p>
				</div>
			</div>
		</div>
	</div>
	<div id="nav-area">
<?php get_template_part('nav'); ?>

	</div>
<?php if (is_front_page()): ?>
	<div id="header-area">
		<div class="<?php echo $container; ?>">
			<header class="row">
				<div class="col-sm-6">
					<!-- -->
				</div>
				<div class="col-sm-6">
					<!-- -->
				</div>
			</header>
		</div>
	</div>
<?php endif; ?>
	<div id="content-area">
		<div class="<?php echo $container; ?>">