jQuery(document).ready(function($) {

	var host = $('.has-custom-fields');

	host.on('click', '.js-image-select', function(e) {
		e.preventDefault();
		var el = $(this),
			container = el.closest('.image-widget'),
			preview = container.find('.image-preview'),
			selector = container.find('.image-selector'),
			input = container.find('input');
		admin.mediaPicker.selected = [ Number(input.val()) ];
		admin.mediaPicker.type = MediaPicker.types.IMAGES;
		admin.mediaPicker.mode = MediaPicker.modes.SINGLE;
		admin.mediaPicker.show(function(selection) {
			if (selection) {
				container.find('input').val(selection.id);
				container.find('.image-preview img').attr('src', selection.image);
				preview.removeClass('hide');
				selector.addClass('hide');
			}
		});
	});

	host.on('click', '.js-image-delete', function(e) {
		e.preventDefault();
		var el = $(this),
			preview = el.closest('.image-preview'),
			container = preview.closest('.metabox-body'),
			selector = container.find('.image-selector');
		preview.addClass('hide');
		selector.removeClass('hide').find('input').val(0);
	});

	host.on('click', '.js-file-select', function(e) {
		e.preventDefault();
		var el = $(this),
			container = el.closest('.file-widget'),
			input = container.find('input');
		admin.mediaPicker.selected = [ Number(input.val()) ];
		admin.mediaPicker.type = MediaPicker.types.ALL;
		admin.mediaPicker.mode = MediaPicker.modes.SINGLE;
		admin.mediaPicker.show(function(selection) {
			if (selection) {
				var details = container.find('.file-details');
				details.removeClass('hide').empty();
				details.append('<i class="fa fa-file fa-4x pull-left text-muted"></i>');
				details.append('<h4>'+ admin.sanitizeText(selection.name) +'</h4>');
				details.append('<p class="text-muted">'+ admin.sanitizeText(selection.mime) +'</p>');
				container.find('input').val(selection.id);
			}
		});
	});

	host.on('click', '.js-gallery-add', function(e) {
		var el = $(this),
			gallery = el.closest('.gallery-widget'),
			images = gallery.find('.gallery-images'),
			field = gallery.data('field'),
			template = _.template( $( gallery.dataToElement('template') ).html() || '<div>Template not found</div>' ),
			selected = [];
		e.preventDefault();
		images.find('[data-id]').each(function() {
			var image = $(this);
			selected.push( Number(image.data('id')) );
		});
		admin.mediaPicker.selected = selected;
		admin.mediaPicker.type = MediaPicker.types.IMAGES;
		admin.mediaPicker.mode = MediaPicker.modes.MULTIPLE;
		admin.mediaPicker.show(function(selection) {
			if (selection) {
				for (var i = 0; i < selection.length; i++) {
					var item = selection[i];
					if (! images.find('[data-id='+ item.id +']').length ) {
						images.append( template({ item: item, field: field }) );
					}
				}
			}
		});
	});

	host.on('click', '.js-gallery-delete', function(e) {
		var el = $(this),
			image = el.closest('.image-wrapper');
		e.preventDefault();
		image.fadeOut(function() {
			image.remove();
		});
	});

	var bindGalleryDrag = function(container) {
		container = container || host;
		container.find('.gallery-images').each(function(index, el) {
			dragula([el]);
		});
	};

	var bindContentAreas = function(container) {
		container = container || host;
		var body = $('body');
		container.find('.codemirror').each(function(index, el) {
			var el = $(this),
				mode = el.data('mode'),
				readOnly = el.data('readonly') || false,
				lineWrapping = el.data('lineWrapping') || false,
				textarea = el.find('textarea'),
				countTimer = null,
				countLabel = el.find('.js-num-words'),
				updateWordCount = function() {
					var regexp = /[\w\u00E0-\u00FC]+(-[\w\u00E0-\u00FC]+)*/g,
						matches = editor.getValue().match(regexp) || [];
					countLabel.text(matches.length);
				};
			var tabWidth = 'application/json' ? 2 : 4;
			var config = {
				keyMap: 'sublime',
				autoRefresh: true,
				lineWrapping: true,
				styleActiveLine: true,
				tabSize: tabWidth,
				indentUnit: tabWidth,
				indentWithTabs: true,
				readOnly: readOnly,
				lineNumbers: true,
				theme: body.hasClass('theme-dark') ? 'monokai' : 'github-light',
				mode: mode
			};
			// console.log(config);
			var editor = CodeMirror.fromTextArea(textarea[0], config);
			//
			editor.name = textarea.attr('name');
			editor.parent = el;
			//
			emmetCodeMirror(editor);
			//
			el.data('editor', editor);
			//
			el.find('.toggle').toggleSwitch({
				labels: ['Code', 'Preview'],
				onChange: function(state) {
					var preview = el.find('.content-preview');
					if (state) {
						var mime = $('[name=mime_type]').val(),
							code = editor.getValue();
						if (mime == 'text/markdown') {
							code = marked(code);
						}
						preview.html(code);
						preview.removeClass('hide');
					} else {
						preview.addClass('hide');
					}
				}
			});
			//
			$('[name=mime_type]').on('change', function() {
				var el = $(this),
					val = el.val();
				editor.setOption('mode', val);
			});
			//
			updateWordCount();
			//
			editor.on('change', function() {
				if (countLabel.length) {
					if (countTimer) {
						clearTimeout(countTimer);
						countTimer = null;
					}
					setTimeout(updateWordCount, 500);
				}
			});
		});
		container.find('.js-toggle-fixed').on('click', function(e) {
			var el = $(this),
				html = $('html'),
				target = el.dataToElement('target'),
				editor = target.data('editor') || null,
				fixed = false;
			e.preventDefault();
			target.toggleClass('fixed');
			fixed = target.hasClass('fixed');
			html.css('overflowY', fixed ? 'hidden' : 'scroll');
			el.find('span').text(fixed ? 'Restore' : 'Fullscreen');
			el.find('i.fa').attr('class', fixed ? 'fa fa-fw fa-window-restore' : 'fa fa-fw fa-window-maximize');
			if (editor) {
				editor.refresh();
			}
		});
	};

	var fixRepeaterNumbers = function(widget) {
		var items = widget.find('.repeater-items .repeater-item');
		items.each(function(index) {
			var item = $(this);
			item.find('.item-grip .number').text(index + 1);
			var subfields = item.find('[name]'),
				galleries = item.find('.gallery-widget');
			subfields.each(function() {
				var subfield = $(this)
					name = subfield.attr('name');
				name = name.replace(/(fields\[[^\s\]]+\]\[[^\s\]]+\]\[)([0-9]+)(\](?:\[\])?)/, function() {
					var ret = arguments[1] + index + arguments[3];
					return ret;
				});
				subfield.attr('name', name);
			});
			galleries.each(function() {
				var gallery = $(this),
					field = gallery.data('field');
				field = field.replace(/([^\s\]]+\]\[[^\s\]]+\]\[)([0-9]+)/, function() {
					var ret = arguments[1] + index;
					return ret;
				});
				gallery.attr('data-field', field);
			});
		});
		if (items.length) {
			widget.removeClass('empty');
		} else {
			widget.addClass('empty');
		}
	}

	$('.repeater-items').each(function(index, el) {
		dragula([el], {
			direction: 'vertical',
			moves: function (el, container, handle) {
				return handle.classList.contains('item-grip') || handle.classList.contains('number');
			}
		}).on('dragend', function() {
			var items = $(el),
				widget = items.closest('.repeater-widget');
			fixRepeaterNumbers(widget);
		});
	});

	$('.repeater-widget').on('click', '.js-repeater-add', function(e) {
		var el = $(this),
			widget = el.closest('.repeater-widget'),
			template = _.template( $( widget.dataToElement('template') ).html() || '<div>Template not found</div>' ),
			position = el.data('position'),
			item = el.closest('.repeater-item'),
			container = widget.find('.repeater-items'),
			newItem = template();
		e.preventDefault();
		newItem = $( newItem.replace(/\*\|NUM\|\*/g, container.children('.repeater-item').length) );
		switch (position) {
			case 'before':
				item.before(newItem);
			break;
			case 'append':
				container.append(newItem);
			break;
		}
		fixRepeaterNumbers(widget);
		bindGalleryDrag(newItem);
		bindContentAreas(newItem);
	}).on('click', '.js-repeater-delete', function(e) {
		var el = $(this),
			item = el.closest('.repeater-item'),
			widget = item.closest('.repeater-widget');
		e.preventDefault();
		item.fadeOut(function() {
			item.remove();
			fixRepeaterNumbers(widget);
		});
	});

	$('.tabs-custom-fields').each(function() {
		var container = $(this),
			metabox = container.closest('.metabox-body');
			ul = $('<ul class="tab-list tab-list-custom-fields"></ul>');
		metabox.addClass('body-simple');
		container.before(ul);
		container.find('.tab').each(function() {
			var tab = $(this),
				label = tab.data('label');
			ul.append('<li><a href="#">'+ label +'</a></li>');
		});
		ul.on('click', 'li a', function(e) {
			e.preventDefault();
			var el = $(this),
				li = el.closest('li'),
				index = li.index();
			li.addClass('selected').siblings('li').removeClass('selected');
			container.find('.tab').removeClass('active').eq(index).addClass('active');
		});
		ul.find('li a').first().trigger('click');
	});

	bindGalleryDrag();

});