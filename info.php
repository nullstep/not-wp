<?php if (getvalue('show_infobar') == 'yes'): ?>
	<div id="info-area" class="content">
		<div class="<?php getcontainer(); ?>">
			<div class="row">
				<?php if (is_active_sidebar('info-area')) dynamic_sidebar('info-area'); ?>

			</div>
		</div>
	</div>
<?php endif; ?>