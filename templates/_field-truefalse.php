<div class="form-group">
	<label class="control-label"><?php $site->cms->sanitizeText($config->label, true); ?></label>
	<label for="<?php $site->cms->sanitizeText("fields_{$config->slug}", true); ?>" class="check-label">
		<input type="checkbox" name="fields[<?php $site->cms->sanitizeText($config->field, true); ?>]" id="<?php $site->cms->sanitizeText("fields_{$config->slug}", true); ?>" value="1" <?php echo($value ? 'checked="checked"' : ''); ?> data-validate="<?php echo(get_item($config, 'required', false) ? 'required' : ''); ?>">
		<span> <?php $site->cms->sanitizeText(get_item($config, 'message', $config->label), true); ?><?php echo(get_item($config, 'required', false) ? '<span class="required">*</span>' : ''); ?></span>
	</label>
	<?php if ( $instructions = get_item($config, 'instructions') ): ?>
		<span class="help-block"><?php $site->cms->sanitizeText($instructions, true); ?></span>
	<?php endif; ?>
</div>