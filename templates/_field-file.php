<div class="form-group">
	<label for="<?php $site->cms->sanitizeText("fields_{$config->slug}", true); ?>" class="control-label"><?php $site->cms->sanitizeText($config->label, true); ?><?php echo(get_item($config, 'required', false) ? '<span class="required">*</span>' : ''); ?></label>

	<div class="file-widget widget-custom-fields">
		<div class="file-wrapper">
			<div class="file-details <?php echo($value ? '' : 'hide'); ?>">
				<?php
					if ($value):
						$attachment = Entities::getById($value);
				?>
					<i class="fa fa-file fa-4x pull-left text-muted"></i>
					<h4><?php $site->cms->sanitizeText($attachment ? $attachment->title : '', true); ?></h4>
					<p class="text-muted"><?php $site->cms->sanitizeText($attachment ? $attachment->mime_type : '', true); ?></p>
				<?php
					endif;
				?>
			</div>
			<input type="hidden" name="fields[<?php $site->cms->sanitizeText($config->field, true); ?>]" id="<?php $site->cms->sanitizeText("fields_{$config->slug}", true); ?>" value="<?php $site->cms->sanitizeText($value, true); ?>">
			<a href="#" class="button button-primary js-file-select" data-attachment="single" data-input="<?php $site->cms->sanitizeText("#fields_{$config->slug}", true); ?>">Select file</a>
		</div>
	</div>

	<?php if ( $instructions = get_item($config, 'instructions') ): ?>
		<span class="help-block"><?php $site->cms->sanitizeText($instructions, true); ?></span>
	<?php endif; ?>
</div>