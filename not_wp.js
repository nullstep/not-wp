// theme admin script

jQuery(function($) {
	// config
	const id = 'not_wp';
	var theme = not_wp;
	const fields = [
		'seed',
		'use_seed',
		'container_class',
		'show_infobar',
		'show_nav',
		'show_banner',
		'header_order',
		'sticky_nav',
		'favicon_image',
		'logo_image_normal',
		'logo_image_contrast',
		'nav_logo',
		'primary_colour',
		'secondary_colour',
		'tertiary_colour',
		'quaternary_colour',
		'heading_font',
		'nav_font',
		'body_font',
		'mono_font',
		'show_page_titles',
		'show_child_titles',
		'sidebar_on_pages',
		'sidebar_on_posts',
		'sidebar_on_feeds',
		'single_post_name',
		'plural_post_name',
		'show_cat_desc',
		'show_post_date',
		'excerpt_length',
		'theme_css',
		'theme_js'
	];
	const editors = [
		'theme_css',
		'theme_js',
		id + '-code_code'
	];
	const texts = [
		'jackdaws love my big sphinx of quartz',
		'pack my box with five-dozen liquor jugs',
		'quirky novel with pages of zany jumbled lexicon',
		'a quick brown fox jumps over the lazy dog'
	];
	const preview = function(n, v) {
		n += '_preview';
		var s = '<style>@import url(\'https://fonts.googleapis.com/css2?family=' + v.replace(' ', '+') + '&display=swap\');#' + n + ' p{font-family:' + v + '}</style>';
		if (v != '') {
			$('#' + n).html(s + '<p>' + texts[Math.floor(Math.random() * 4)] + '</p>');
		}
		else {
			$('#' + n).empty();
		}
	};
	// let's go
	var cm = [];
	$('#' + id + '-form .tab-content .' + id + '-tab').eq(0).show();
	$('#' + id + '-form .nav-tab').eq(0).addClass('nav-tab-active');
	$('#' + id + '-nav a').on('click', function(e) {
		e.preventDefault();
		$('#' + id + '-nav a').removeClass('nav-tab-active');
		var tab = $(this).attr('href');
		$('.' + id + '-tab').hide();
		$(this).addClass('nav-tab-active');
		$(tab).show();
		if ($(tab).find('.code').length) {
			cm.forEach(function(item, index, arr) {
				item.codemirror.refresh();
			});
		}
	});
	$.ajax({
		method: 'GET',
		url: theme.api.url,
		beforeSend: function(xhr) {
			xhr.setRequestHeader('X-WP-Nonce', theme.api.nonce);
		}
	})
	.then(function(r) {
		fields.forEach(function(item, index) {
			if (r.hasOwnProperty(item)) {
				var i = $('#' + item);
				i.val(r[item]);
				if (i.is(':checkbox')) {
					if (r[item] == 'yes') {
						i.prop('checked', true);
					}
				}
				if (r[item][0] == '#') {
					i.parent().find('[data-id="' + item + '"]').val(r[item]);
				}
				if (item.indexOf('font') != -1) {
					preview(item, r[item]);
				}
			}
			cm.forEach(function(item, index, arr) {
				item.codemirror.setValue($('#' + editors[index]).val());
			});
		});
	});
	editors.forEach(function(item, index, arr) {
		var eid = $('#' + item);
		if (eid.length) {
			var es = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
			es.codemirror = _.extend(
				{},
				es.codemirror, {
					indentUnit: 2,
					tabSize: 2,
					mode: 'css'
				}
			);
			var editor = wp.codeEditor.initialize(item, es);
			cm.push(editor);
			editor.codemirror.on('change', function(cMirror) {
				editor.codemirror.save();
				eid.change();
			});
		}
	});
	$('#' + id + '-form .colour').on('change', function(e) {
		var i = $(this).attr('id');
		$(this).parent().find(`[data-id='${i}']`).val($(this).val());
	});
	$('#' + id + '-form .font').on('change', function(e) {
		preview($(this).attr('id'), $(this).val());
	});
	$('.font-preview').on('click', function(e) {
		var e = $(this).find('p'), t = e.text();
		if (t[0] == t[0].toUpperCase()) {
			e.text(t.toLowerCase());
		}
		else {
			e.text(t.toUpperCase());			
		}
	});
	var enabled = true;
	$('#' + id + '-form .text').keydown(function(e) {
		if (e.keyCode == 27) {
			enabled = !enabled;
			return false;
		}
		if (e.keyCode === 13 && enabled) {
			if (this.selectionStart == this.selectionEnd) {
				var sel = this.selectionStart;
				var text = $(this).val();
				while (sel > 0 && text[sel-1] != '\n')
				sel--;
				var lineStart = sel;
				while (text[sel] == ' ' || text[sel] == '\t')
				sel++;
				if (sel > lineStart) {
					document.execCommand('insertText', false, "\n" + text.substr(lineStart, sel-lineStart));
					this.blur();
					this.focus();
					return false;
				}
			}
		}
		if (e.keyCode === 9 && enabled) {
			if (this.selectionStart == this.selectionEnd) {
				if (!e.shiftKey) {
					document.execCommand('insertText', false, "\t");
				}
				else {
					var text = this.value;
					if (this.selectionStart > 0 && text[this.selectionStart-1]=='\t') {
						document.execCommand('delete');
					}
				}
			}
			else {
				var selStart = this.selectionStart;
				var selEnd = this.selectionEnd;
				var text = $(this).val();
				while (selStart > 0 && text[selStart-1] != '\n')
					selStart--;
				while (selEnd > 0 && text[selEnd-1]!='\n' && selEnd < text.length)
					selEnd++;
				var lines = text.substr(selStart, selEnd - selStart).split('\n');
				for (var i=0; i<lines.length; i++) {
					if (i==lines.length-1 && lines[i].length==0)
						continue;
					if (e.shiftKey) {
						if (lines[i].startsWith('\t'))
							lines[i] = lines[i].substr(1);
						else if (lines[i].startsWith("    "))
							lines[i] = lines[i].substr(4);
					}
					else
						lines[i] = "\t" + lines[i];
				}
				lines = lines.join('\n');
				this.value = text.substr(0, selStart) + lines + text.substr(selEnd);
				this.selectionStart = selStart;
				this.selectionEnd = selStart + lines.length; 
			}
			return false;
		}
		enabled = true;
		return true;
	});
	$('#' + id + '-form').on('submit', function(e) {
		e.preventDefault();
		cm.forEach(function(item, index, arr) {
			item.codemirror.save();
		});
		$('#submit').text('...').attr('disabled', 'disabled');
		var data = {};
		fields.forEach(function(item, index) {
			var i = $('#' + item);
			if (i.is(':checkbox')) {
				data[item] = (i.prop('checked')) ? 'yes' : '';
			}
			else {
				data[item] = i.val();
			}
		});
		$.ajax({
			method: 'POST',
			url: theme.api.url,
			beforeSend: function (xhr) {
				xhr.setRequestHeader('X-WP-Nonce', theme.api.nonce);
			},
			data: data
		})
		.then(function(r) {
			$('#' + id + '-feedback').html('<p>' + theme.strings.saved + '</p>').show().delay(3000).fadeOut();
			$('#submit').removeAttr('disabled');
			fields.forEach(function(item, index) {
				if (r.hasOwnProperty(item)) {
					$('#' + item).val(r[item]);
				}
			});
		})
		.fail(function(r) {
			var message = theme.strings.error;
			if (r.hasOwnProperty('message')) {
				message = r.message;
			}
			$('#submit').removeAttr('disabled');
			$('#' + id + '-feedback').html('<p>' + message + '</p>').show().delay(3000).fadeOut();
		});
	});
	var mediaUploader, bid;
	$('.choose-file-button').on('click', function(e) {
		bid = '#' + $(this).data('id');
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
			$(bid).val(attachment.url.split('/').pop());
		});
		mediaUploader.open();
	});
	$('.choose-colour-button').on('change', function() {
		bid = '#' + $(this).data('id');
		$(bid).val($(this).val());
	});
});

// EOF