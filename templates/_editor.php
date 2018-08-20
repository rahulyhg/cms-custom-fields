<form action="" method="post">
	<?php $site->cms->getCsrfToken('admin.customfields', 'input', true); ?>
	<div class="panel-wrapper fixed-right">
		<div class="panel-fixed">
			<div class="metabox">
				<div class="metabox-header">Attributes</div>
				<div class="metabox-body">
					<div class="form-fields">
						<!-- <div class="form-group">
							<label for="status" class="control-label">Status</label>
							<select name="status" id="status" class="form-control input-block" data-value="<?php echo($entity ? $entity->status : 'Published'); ?>">
								<option value="Published">Published</option>
								<option value="Private">Private</option>
								<option value="Draft">Draft</option>
							</select>
						</div> -->
					</div>
					<div class="form-actions text-right">
						<button type="submit" class="button button-primary" data-loading="Saving...">Save Field Group</button>
					</div>
				</div>
			</div>
		</div>
		<div class="panel-fluid">
			<div class="form-group">
				<label for="title" class="control-label hide">Title</label>
				<input type="text" name="title" id="title" class="form-control input-block form-control-large" placeholder="Field Group title" value="<?php echo($entity ? $entity->title : ''); ?>">
			</div>
			<div class="form-group">
				<label for="content" class="control-label hide">Content</label>
				<div class="codemirror content-box" data-mode="application/json" data-countwords="false" data-preview="false">
					<div class="content-header">
						<!--  -->
					</div>
					<div class="content-body">
						<textarea name="content" id="content" class="hide"><?php echo($entity ? $entity->content : ''); ?></textarea>
					</div>
					<div class="content-footer">
						<div class="row row-sm row-collapse">
							<div class="col col-sm-6">
								<div class="js-validation-output"></div>
							</div>
							<div class="col col-sm-6">
								<div class="text-right">
									<a href="#" class="link-simple js-validate-json" data-target=".content-box"><i class="fa fa-fw fa-bug"></i> Validate JSON</a>
									<a href="#" class="link-simple js-toggle-fixed" data-target=".content-box"><i class="fa fa-fw fa-window-maximize"></i> <span class="hide-mobile-inline">Fullscreen</span></a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>