// theme admin script

jQuery(function($) {
	const fields = [
		'container_class',
		'favicon_image',
		'logo_image',
		'header_image',
		'navbar_colour',
		'footer_colour'
	];
	var _theme = not_wp;
	$.ajax({
		method: 'GET',
		url: _theme.api.url,
		beforeSend: function(xhr) {
			xhr.setRequestHeader('X-WP-Nonce', _theme.api.nonce);
		}
	})
	.then(function(r) {
		fields.forEach(function(item, index) {
			if (r.hasOwnProperty(item)) {
				$('#' + item).val(r[item]);
				if (r[item][0] == '#') {
					$('#' + item).parent().find('[data-id="' + item + '"]').val(r[item]);
				}
			}
		});
	});
	$('#_theme-form').on('submit', function(e) {
		e.preventDefault();
		$('#submit').text('...').attr('disabled', 'disabled');
		var data = {};
		fields.forEach(function(item, index) {
			data[item] = $('#' + item).val();
		});
		$.ajax({
			method: 'POST',
			url: _theme.api.url,
			beforeSend: function (xhr) {
				xhr.setRequestHeader('X-WP-Nonce', _theme.api.nonce);
			},
			data: data
		})
		.then(function(r) {
			$('#feedback').html('<p>' + _theme.strings.saved + '</p>');
			$('#submit').removeAttr('disabled');
			fields.forEach(function(item, index) {
				if (r.hasOwnProperty(item)) {
					$('#' + item).val(r[item]);
				}
			});
		})
		.fail(function(r) {
			var message = _theme.strings.error;
			if (r.hasOwnProperty('message')) {
				message = r.message;
			}
			$('#submit').removeAttr('disabled');
			$('#feedback').html('<p>' + message + '</p>');
		});
	});
	var mediaUploader, id;
	$('.choose-file-button').on('click', function(e) {
		id = '#' + $(this).data('id');
		e.preventDefault();
		if (mediaUploader) {
			mediaUploader.open();
			return;
		}
		mediaUploader = wp.media.frames.file_frame = wp.media({
			title: 'Choose File',
			button: {
				text: 'Choose File'
			}, multiple: false
		});
		wp.media.frame.on('open', function() {
			if (wp.media.frame.content.get() !== null) {          
				wp.media.frame.content.get().collection._requery(true);
				wp.media.frame.content.get().options.selection.reset();
			}
		}, this);
		mediaUploader.on('select', function() {
			var attachment = mediaUploader.state().get('selection').first().toJSON();
			$(id).val(attachment.url.split('/').pop());
		});
		mediaUploader.open();
	});
	$('.choose-colour-button').on('change', function() {
		id = '#' + $(this).data('id');
		$(id).val($(this).val());
	});
});

// EOF