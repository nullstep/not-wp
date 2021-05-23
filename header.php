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
		<?php getfonts(); ?><?php getcss(); ?>
	</style>
</head>
<body id="override" class="<?php echo get_post_field('post_name'); ?>">
<?php $order = getorder(); ?>
<?php get_template_part($order[0]); ?>
<?php get_template_part($order[1]); ?>
<?php get_template_part($order[2]); ?>
	<div id="content-area" class="content">
		<div class="<?php getcontainer(); ?>">