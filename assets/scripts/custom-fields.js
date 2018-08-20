jQuery(document).ready(function($) {

	$('.js-image-select').on('click', function(e) {
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

	$('.js-file-select').on('click', function(e) {
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

	$('.js-gallery-add').on('click', function(e) {
		var el = $(this),
			gallery = el.closest('.gallery-widget'),
			images = gallery.find('.gallery-images'),
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
						images.append( template({ item: item }) );
					}
				}
			}
		});
	});

	$('.gallery-images').each(function(index, el) {
		dragula([el]);
	}).on('click', '.js-action-delete', function(e) {
		var el = $(this),
			image = el.closest('.image-wrapper');
		e.preventDefault();
		image.fadeOut(function() {
			image.remove();
		});
	});

	var fixRepeaterNumbers = function(widget) {
		var items = widget.find('.repeater-items .repeater-item');
		items.each(function(index) {
			var item = $(this);
			item.find('.item-grip .number').text(index + 1);
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
		switch (position) {
			case 'before':
				item.before(newItem);
			break;
			case 'append':
				container.append(newItem);
			break;
		}
		fixRepeaterNumbers(widget);
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

});