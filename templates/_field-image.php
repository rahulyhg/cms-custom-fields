<div class="form-group">
	<label for="<?php $site->cms->sanitizeText("fields_{$config->slug}", true); ?>" class="control-label"><?php $site->cms->sanitizeText($config->label, true); ?><?php echo(get_item($config, 'required', false) ? '<span class="required">*</span>' : ''); ?></label>

	<div class="image-widget widget-custom-fields">
		<div class="image-wrapper image-preview <?php echo(!$value ? 'hide' : ''); ?>">
			<div class="padding">
				<div class="image-grid">
					<img src="<?php echo($value ? $site->cms->getImage($value) : ''); ?>" alt="" class="img-responsive">
					<div class="image-actions">
						<a href="#" class="image-action js-image-select"><i class="fa fa-fw fa-pencil"></i></a>
						<a href="#" class="image-action action-danger js-action-delete"><i class="fa fa-fw fa-times"></i></a>
					</div>
				</div>
			</div>
		</div>
		<div class="boxfix image-selector <?php echo($value ? 'hide' : ''); ?>">
			<input type="hidden" name="fields[<?php $site->cms->sanitizeText($config->field, true); ?>]" id="<?php $site->cms->sanitizeText("fields_{$config->slug}", true); ?>" value="<?php $site->cms->sanitizeText($value, true); ?>">
			<a href="#" class="button button-primary js-image-select" data-attachment="single" data-input="<?php $site->cms->sanitizeText("#fields_{$config->slug}", true); ?>">Select image</a>
		</div>
	</div>

	<?php if ( $instructions = get_item($config, 'instructions') ): ?>
		<span class="help-block"><?php $site->cms->sanitizeText($instructions, true); ?></span>
	<?php endif; ?>
</div>