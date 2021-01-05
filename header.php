<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="<?php bloginfo('description'); ?>">
	<title><?php echo trim(wp_title('', false)); ?><?php if (wp_title('', false)) { echo ' : '; } ?><?php bloginfo('name'); ?></title>
	<link href="/uploads/<?php getvalue('favicon_image'); ?>" rel="shortcut icon">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" rel="stylesheet">
<?php wp_head(); ?>
	<style>
		:root {
			--main-doc-colour: #fff;
			--top-area-colour: #eee;
			--top-area-font-colour: #000;
			--top-area-height: auto;
			--navbar-colour: <?php getvalue('navbar_colour'); ?>;
			--navitem-hover-colour: #eee;
			--navlink-colour: #fff;
			--navlink-hover-colour: #333;
			--navtext-transform: uppercase;
			--header-area-colour: #eee;
			--header-area-font-colour: #333;
			--header-area-height: auto;
			--header-area-image: url('/uploads/<?php getvalue('header_image'); ?>');
			--content-area-colour: #fff;
			--content-area-font-colour: #333;
			--footer-area-colour: #333;
			--footer-area-font-colour: #fff;
			--footer-area-height: auto;
			--link-colour: #415a6b;
			--link-hover-colour: #222;
			--heading-font: 'Arial Black';
			--heading-colour: #333;
			--heading-transform: uppercase;
			--nav-font: 'Arial';
			--body-font: 'Times New Roman';
			--mono-font: 'Courier New';
			--border-colour: #f00;
			--h1-size: 2rem;
			--h2-size: 1.8rem;
			--h3-size: 1.6rem;
			--h4-size: 1.4rem;
			--h5-size: 1.2rem;
			--h6-size: 1rem;
			--p-size: 1.25rem;
			--li-size: 1.25rem;
			--button-colour: #333;
			--button-text-colour: #fff;
			--button-hover-colour: #555;
			--button-hover-text-colour: #fff;
		}
		html{background-color:var(--main-doc-colour)}body{background-color:var(--main-doc-colour);font-family:var(--body-font)}h1,h2,h3,h4,h5,h6{font-family:var(--heading-font);color:var(--heading-colour);text-transform:var(--heading-transform)}h1{font-size:var(--h1-size);margin-bottom:1.5rem}h2{font-size:var(--h2-size)}h3{font-size:var(--h3-size)}h4{font-size:var(--h4-size)}h5{font-size:var(--h5-size)}h6{font-size:var(--h6-size)}a{color:var(--link-colour);text-decoration:none !important}a:hover{color:var(--link-hover-colour);text-decoration:none !important}h1 a,h2 a,h3 a,h4 a,h5 a,h6 a{color:var(--heading-colour)}p{font-size:var(--p-size)}li{font-size:var(--li-size)}#top-area{height:var(--top-area-height);background-color:var(--top-area-colour);color:var(--top-area-font-colour)}#header-area{background-image:var(--header-area-image);background-size:cover;height:var(--header-area-height);color:var(--header-area-font-colour)}#content-area{background-color:var(--content-area-colour);color:var(--content-area-font-colour);padding-top:1rem;min-height:450px}#footer-area{height:var(--footer-area-height);background-color:var(--footer-area-colour);color:var(--footer-area-font-colour);padding-top:1rem}#override .btn-primary{font-family:var(--nav-font);text-transform:var(--heading-transform);background-color:var(--button-colour);border:none;border-radius:0;color:var(--button-text-colour)}#override .btn-primary:hover{background-color:var(--button-hover-colour);color:var(--button-hover-text-colour)}#override .bg-dark{background-color:var(--navbar-colour) !important}#override .nav-item{font-family:var(--nav-font);text-transform:var(--navtext-transform)}#override .nav-item:hover{background-color:var(--navitem-hover-colour)}#override .nav-link{color:var(--navlink-colour)}#override .nav-link:hover{color:var(--navlink-hover-colour)}#search-box{border:1px solid #333 !important;padding:2px 5px;outline:none}.shrink{transition:all 0.2s;transform:scale(1)}.shrink:hover{transform:scale(0.90)}#logo{padding:15px 0;height:100px}pre{font-family:var(--mono-font) !important}.pagination{font-family:var(--nav-font);text-transform:var(--heading-transform);margin:2rem 0}.page-numbers{margin:0 1rem 0 0;background-color:var(--button-colour);border:none;border-radius:0;color:var(--button-text-colour);padding:2px 0.5rem 0}.current{background-color:var(--button-hover-colour)}.page-numbers:hover{background-color:var(--button-hover-colour);color:var(--button-hover-text-colour)}.post-date{font-size:14px;font-style:italic}.latest-posts{list-style-type:none;padding-left:0}.latest-posts span{font-size:12px;font-style:italic}.video{position:relative;padding-bottom:56.25%;padding-top:30px;height:0;overflow:hidden;}.video iframe,.video object,.video embed{position:absolute;top:0;left:0;width:100%;height:100%;}.description{font-style:italic;margin-bottom:2rem}@media(min-width:768px){#title-area{text-align:right}}@media(max-width:767px){#logo-area{text-align:center}#title-area{text-align:center}}
	</style>
</head>
<body id="override">
	<div id="top-area">
		<div class="container">
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
		<div class="container">
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
		<div class="container">