<div class="form-group">
	<label for="<?php $site->cms->sanitizeText("fields_{$config->slug}", true); ?>" class="control-label"><?php $site->cms->sanitizeText($config->label, true); ?><?php echo(get_item($config, 'required', false) ? '<span class="required">*</span>' : ''); ?></label>
	<select name="fields[<?php $site->cms->sanitizeText($config->field, true); ?>]" id="<?php $site->cms->sanitizeText("fields_{$config->slug}", true); ?>" class="form-control input-block" data-value="<?php $site->cms->sanitizeText($value, true); ?>" placeholder="<?php $site->cms->sanitizeText(get_item($config, 'placeholder'), true); ?>" data-validate="<?php echo(get_item($config, 'required', false) ? 'required' : ''); ?>">
		<?php
			$type = get_item($config, 'entity');
			$args = [];
			$args['conditions'] = "type = '{$type}'";
			$entities = Entities::all($args);
			if ($entities):
				foreach ($entities as $entity_obj):
		?>
			<option value="<?php echo $entity_obj->id; ?>"><?php $site->cms->sanitizeText($entity_obj->title, true); ?></option>
		<?php
				endforeach;
			endif;
		?>
	</select>
	<?php if ( $instructions = get_item($config, 'instructions') ): ?>
		<span class="help-block"><?php $site->cms->sanitizeText($instructions, true); ?></span>
	<?php endif; ?>
</div>