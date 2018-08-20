<div class="form-group">
	<label for="<?php $site->cms->sanitizeText("fields_{$config->slug}", true); ?>" class="control-label"><?php $site->cms->sanitizeText($config->label, true); ?><?php echo(get_item($config, 'required', false) ? '<span class="required">*</span>' : ''); ?></label>
	<div class="codemirror content-box box-simple" data-mode="<?php echo(get_item($config, 'mime_type', 'text/markdown')); ?>">
		<div class="content-header">
			<div class="toggle"></div>
		</div>
		<div class="content-body">
			<textarea name="fields[<?php $site->cms->sanitizeText($config->field, true); ?>]" id="<?php $site->cms->sanitizeText("fields_{$config->slug}", true); ?>" class="form-control input-block hide" placeholder="<?php $site->cms->sanitizeText(get_item($config, 'placeholder'), true); ?>" data-validate="<?php echo(get_item($config, 'required', false) ? 'required' : ''); ?>"><?php $site->cms->sanitizeText($value, true); ?></textarea>
			<div class="content-preview hide"></div>
		</div>
		<div class="content-footer">
			<div class="row row-sm row-collapse">
				<div class="col col-sm-9">
					<div>Number of words: <span class="js-num-words">0</span></div>
				</div>
				<div class="col col-sm-3">
					<div class="text-right">
						<a href="#" class="link-simple js-toggle-fixed" data-target=".content-box"><i class="fa fa-fw fa-window-maximize"></i> <span class="hide-mobile-inline">Fullscreen</span></a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php if ( $instructions = get_item($config, 'instructions') ): ?>
		<span class="help-block"><?php $site->cms->sanitizeText($instructions, true); ?></span>
	<?php endif; ?>
</div>