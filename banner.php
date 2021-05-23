<?php if (getvalue('show_banner') == 'yes'): ?>
<?php if (is_front_page()): ?>
	<div id="banner-area" class="content">
		<div class="<?php getcontainer(); ?>">
			<header class="row">
				<?php if (is_active_sidebar('banner-area')) dynamic_sidebar('banner-area'); ?>

			</header>
		</div>
	</div>
<?php endif; ?>
<?php endif; ?>