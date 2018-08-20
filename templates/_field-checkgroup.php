<div class="form-group">
	<label for="<?php $site->cms->sanitizeText("fields_{$config->slug}", true); ?>" class="control-label"><?php $site->cms->sanitizeText($config->label, true); ?><?php echo(get_item($config, 'required', false) ? '<span class="required">*</span>' : ''); ?></label>
	<div class="check-list list-simple">
		<?php
			$selection = $value ?: [];
			if ($config->options):
				foreach ($config->options as $value => $label):
					$id = "{$config->slug}_{$value}";
		?>
			<label class="item-label" for="<?php $site->cms->sanitizeText($id, true); ?>">
				<input type="checkbox" name="fields[<?php $site->cms->sanitizeText($config->field, true); ?>][]" value="<?php $site->cms->sanitizeText($value, true); ?>" <?php echo(in_array($value, $selection) ? 'checked="checked"' : ''); ?> id="<?php $site->cms->sanitizeText($id, true); ?>">
				<span><?php $site->cms->sanitizeText($label, true); ?></span>
			</label>
		<?php
				endforeach;
			endif;
		?>
	</div>
	<?php if ( $instructions = get_item($config, 'instructions') ): ?>
		<span class="help-block"><?php $site->cms->sanitizeText($instructions, true); ?></span>
	<?php endif; ?>
</div>