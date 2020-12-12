<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="<?php bloginfo('description'); ?>">
	<title><?php echo trim(wp_title('', false)); ?><?php if (wp_title('', false)) { echo ' : '; } ?><?php bloginfo('name'); ?></title>
	<link href="<?php getvalue('site_favicon'); ?>" rel="shortcut icon">
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Oxygen:wght@300&family=Teko:wght@500&family=Fira+Code:wght@300&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<?php wp_head(); ?>
	<style>
		:root {
			--main-doc-colour: #fff;
			--top-area-colour: #eee;
			--top-area-font-colour: #000;
			--top-area-height: auto;
			--navbar-colour: #000;
			--navitem-hover-colour: #eee;
			--navlink-colour: #fff;
			--navlink-hover-colour: #000b;
			--navtext-transform: uppercase;
			--header-area-colour: auto;
			--header-area-font-colour: #000;
			--header-area-height: auto;
			--content-area-colour: #fff;
			--content-area-font-colour: #000;
			--footer-area-colour: #000;
			--footer-area-font-colour: #fff;
			--footer-area-height: auto;
			--link-colour: #415a6b;
			--link-hover-colour: #222;
			--heading-font: 'Teko';
			--heading-colour: #000;
			--heading-transform: uppercase;
			--body-font: 'Oxygen';
			--mono-font: 'Fira Code';
			--border-colour: #f00;
			--h1-size: 48px;
			--h2-size: 40px;
			--h3-size: 32px;
			--h4-size: 24px;
			--h5-size: 20px;
			--h6-size: 16px;
			--p-size: 16px;
			--li-size: 16px;
			--button-colour: #000;
			--button-text-colour: #fff;
			--button-fx-colour: #333;
		}
		html{background-color:var(--main-doc-colour)}body{background-color:var(--main-doc-colour);font-family:var(--body-font)}h1,h2,h3,h4,h5,h6{font-family:var(--heading-font), sans-serif;color:var(--heading-colour);text-transform:var(--heading-transform)}h1{font-size:var(--h1-size)}h2{font-size:var(--h2-size)}h3{font-size:var(--h3-size)}h4{font-size:var(--h4-size)}h5{font-size:var(--h5-size)}h6{font-size:var(--h6-size)}a{color:var(--link-colour);text-decoration:none !important}a:hover{color:var(--link-hover-colour);text-decoration:none !important}h1 a,h2 a,h3 a,h4 a,h5 a,h6 a{color:var(--heading-colour)}p{font-size:var(--p-size)}li{font-size:var(--li-size)}#top-area{height:var(--top-area-height);background-color:var(--top-area-colour);color:var(--top-area-font-colour)}#header-area{height:var(--header-area-height);color:var(--header-area-font-colour)}#content-area{background-color:var(--content-area-colour);color:var(--content-area-font-colour);padding-top:1rem;min-height:450px}#footer-area{height:var(--footer-area-height);background-color:var(--footer-area-colour);color:var(--footer-area-font-colour);padding-top:1rem}#override .btn-primary{background-color:var(--button-colour);border:none;border-radius:0;color:var(--button-text-colour)}#override .bg-dark{background-color:var(--navbar-colour) !important}#override .nav-item{text-transform:var(--navtext-transform)}#override .nav-item:hover{background-color:var(--navitem-hover-colour)}#override .nav-link{color:var(--navlink-colour)}#override .nav-link:hover{color:var(--navlink-hover-colour)}#search-box{border:1px solid #333 !important;padding:2px 5px;outline:none}.shrink{transition:all 0.2s;transform:scale(1)}.shrink:hover{transform:scale(0.90)}.btn-fx{box-shadow:inset 0 0 0 0 #fff}.btn-fx:hover{box-shadow:inset 0 0 0 50px var(--button-fx-colour)}#logo{padding:15px 0;height:150px}.mono{font-family:var(--mono-font) !important}@media(min-width:768px){#title-area{text-align:right}}@media(max-width:767px){#logo-area{text-align:center}#title-area{text-align:center}}
	</style>
</head>
<body id="override">
	<div id="top-area">
		<div class="container">
			<div class="row">
				<div id="logo-area" class="col-sm-6">
					<a href="/"><img id="logo" src="<?php getvalue('site_logo'); ?>" class="shrink"></a>
				</div>
				<div id="title-area" class="col-sm-6">
					<h3 class="my-3"><?php bloginfo('description'); ?></h3>
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