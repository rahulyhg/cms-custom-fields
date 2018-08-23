<div class="form-group">
	<label for="<?php $site->cms->sanitizeText("fields_{$config->slug}", true); ?>" class="control-label"><?php $site->cms->sanitizeText($config->label, true); ?><?php echo(get_item($config, 'required', false) ? '<span class="required">*</span>' : ''); ?></label>

	<div class="gallery-widget widget-custom-fields" data-template="#template_gallery_item" data-field="<?php $site->cms->sanitizeText($config->field, true); ?>">
		<input type="hidden" name="fields[<?php $site->cms->sanitizeText($config->field, true); ?>]">
		<div class="gallery-images cf">
			<?php
				if ($value):
					foreach ($value as $id):
						$image = $site->cms->getImage($id, 'thumbnail');
			?>
				<div class="image-wrapper image-preview image-grid" data-id="<?php echo $id; ?>">
					<input type="hidden" name="fields[<?php $site->cms->sanitizeText($config->field, true); ?>][]" value="<?php echo $id; ?>">
					<img src="<?php echo $image; ?>" alt="" class="img-responsive">
					<div class="image-actions">
						<a href="#" class="image-action action-danger js-gallery-delete"><i class="fa fa-fw fa-times"></i></a>
					</div>
				</div>
			<?php
					endforeach;
				endif;
			?>
		</div>
		<div class="gallery-actions">
			<a href="#" class="button button-primary js-gallery-add">Add image</a>
		</div>
	</div>

	<?php if ( $instructions = get_item($config, 'instructions') ): ?>
		<span class="help-block"><?php $site->cms->sanitizeText($instructions, true); ?></span>
	<?php endif; ?>
</div>