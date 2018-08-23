<div class="form-group">
	<label for="<?php $site->cms->sanitizeText("fields_{$config->slug}", true); ?>" class="control-label"><?php $site->cms->sanitizeText($config->label, true); ?><?php echo(get_item($config, 'required', false) ? '<span class="required">*</span>' : ''); ?></label>

	<div class="repeater-widget widget-custom-fields <?php echo ($value ? '' : 'empty'); ?>" data-template="<?php $site->cms->sanitizeText("#template_{$config->slug}", true); ?>">

		<div class="repeater-items">
			<input type="hidden" name="fields[<?php $site->cms->sanitizeText($config->field, true); ?>]">

			<?php
				$subfields = [];
				if ($config->subfields) {
					foreach ($config->subfields as $subfield) {
						$subfields[$subfield->name] = $subfield->type;
					}
				}
				printf('<input type="hidden" name="_fields[_subfields-%s]" value="%s">', $config->field, htmlspecialchars(serialize($subfields)) );
				if ($value):
					$values = [];
					foreach ($value as $name => $items) {
						for ($i = 0; $i < count($items); $i++) {
							if (! isset( $values["item_{$i}"] ) ) {
								$values["item_{$i}"] = [];
							}
							$values["item_{$i}"][$name] = $items[$i];
						}
					}
					for ($i = 0; $i < count($values); $i++):
			?>
				<div class="repeater-item cf">
					<div class="item-grip">
						<span class="number"><?php echo $i + 1; ?></span>
					</div>
					<div class="item-subfields layout-<?php echo get_item($config, 'layout', 'table'); ?>">
						<?php
							if ($config->subfields):
								foreach ($config->subfields as $subfield):
									$type = $plugin->getFieldType($subfield->type);
									$renderer = get_item($type, 'renderer');
									$value = get_item($values["item_{$i}"], $subfield->name);
									$subfield->slug = "{$config->name}_{$subfield->name}";
									$subfield->field = "{$config->name}][{$subfield->name}][";
									#
									if ($type && is_callable($renderer)) {
										call_user_func($renderer, $subfield, $value);
									} else {
										CustomFieldsPlugin::renderDebug($subfield, $value);
									}
								endforeach;
							endif;
						?>
					</div>
					<div class="item-actions">
						<a href="#" class="action action-insert js-repeater-add" data-position="before"><i class="fa fa-plus"></i></a>
						<a href="#" class="action action-delete js-repeater-delete"><i class="fa fa-minus"></i></a>
					</div>
				</div>
			<?php
					endfor;
				endif;
			?>

		</div>

		<div class="repeater-actions">
			<a href="#" class="button button-primary js-repeater-add" data-position="append">Add row</a>
		</div>

		<script type="text/template" id="<?php $site->cms->sanitizeText("template_{$config->slug}", true); ?>">
			<div class="repeater-item cf">
				<div class="item-grip">
					<span class="number">0</span>
				</div>
				<div class="item-subfields layout-<?php echo get_item($config, 'layout', 'table'); ?>">
					<?php
						if ($config->subfields):
							foreach ($config->subfields as $subfield):
								$type = $plugin->getFieldType($subfield->type);
								$renderer = get_item($type, 'renderer');
								$value = '';
								$subfield->slug = "{$config->name}_{$subfield->name}";
								$subfield->field = "{$config->name}][{$subfield->name}][";
								if ($type && is_callable($renderer)) {
									call_user_func($renderer, $subfield, $value);
								} else {
									CustomFieldsPlugin::renderDebug($subfield, $value);
								}
							endforeach;
						endif;
					?>
				</div>
				<div class="item-actions">
					<a href="#" class="action action-insert js-repeater-add" data-position="before"><i class="fa fa-plus"></i></a>
					<a href="#" class="action action-delete js-repeater-delete"><i class="fa fa-minus"></i></a>
				</div>
			</div>
		</script>

	</div>

	<?php if ( $instructions = get_item($config, 'instructions') ): ?>
		<span class="help-block"><?php $site->cms->sanitizeText($instructions, true); ?></span>
	<?php endif; ?>
</div>