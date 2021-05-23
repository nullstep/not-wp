		</div>
	</div>
<?php getsvg('#333'); ?>
	<div id="footer-area" class="content">
		<div class="<?php getcontainer(); ?>">
			<footer class="row">
				<?php if (is_active_sidebar('footer-top')) dynamic_sidebar('footer-top'); ?>

				<section class="col-sm-12"><?php wp_footer(); ?></section>
				<?php if (is_active_sidebar('footer-bottom')) dynamic_sidebar('footer-bottom'); ?>

			</footer>
		</div>
	</div>
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		<?php getjs(); ?>
	</script>
</body>
</html>