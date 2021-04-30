		</div>
	</div>
	<div id="footer-area">
		<div class="<?php getvalue('container_class'); ?>">
			<footer class="row">
				<section class="col-sm-12">
					<p class="py-5">&copy; <?php echo date('Y'); ?></p>
<?php wp_footer(); ?>
				</section>
			</footer>
		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		<?php getvalue('theme_js_minified', true); ?>
	</script>
</body>
</html>