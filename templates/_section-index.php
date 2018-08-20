<div class="admin-title">
	<a href="<?php $site->cms->admin->adminUrl("/custom-fields/new", true); ?>" class="button button-primary pull-right">
		<i class="fa fa-fw fa-plus"></i>
		<span class="hide-mobile-inline"> Add Field Group</span>
	</a>
	<h2 class="section-title has-button">
		<a href="<?php $site->cms->admin->adminUrl('/', true); ?>" class="button-title"><i class="fa fa-angle-left"></i></a>
		<span>Field Groups</span>
	</h2>
</div>
<div class="panel-wrapper fixed-right">
	<div class="panel-fixed">
		<div class="metabox">
			<div class="metabox-header">Filter</div>
			<div class="metabox-body">
				<form action="" method="get">
					<div class="form-fields">
						<!-- <div class="form-group">
							<label for="search" class="control-label">Search field_groups</label>
							<input type="text" name="search" id="search" class="form-control input-block">
						</div> -->
						<!-- <div class="form-group">
							<label for="status" class="control-label">Type</label>
							<select name="status" id="status" class="form-control input-block">
								<option value="">All</option>
								<option value="Administrator">Administrator</option>
								<option value="Editor">Editor</option>
								<option value="Collaborator">Collaborator</option>
								<option value="Subscriber">Subscriber</option>
							</select>
						</div> -->
					</div>
					<!-- <div class="form-actions text-right">
						<button type="submit" class="button button-primary" data-loading="Filtering...">Filter Field Groups</button>
					</div> -->
				</form>
			</div>
		</div>
	</div>
	<div class="panel-fluid">
		<div class="metabox">
			<div class="metabox-body body-simple">
				<div class="table-wrapper">
					<table class="table">
						<thead>
							<tr>
								<th class="checkbox"><input type="checkbox" name="select-all"></th>
								<th>Name</th>
								<th>Fields</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th class="checkbox"><input type="checkbox" name="select-all"></th>
								<th>Name</th>
								<th>Fields</th>
							</tr>
						</tfoot>
						<tbody>
							<?php
								if ($entities):
									$csrf = $site->cms->getCsrfToken('admin.customfields');
									foreach ($entities as $entity):
										$config = @json_decode($entity->content);
							?>
								<tr>
									<td class="checkbox"><input type="checkbox" name="select" value="<?php echo $entity->id; ?>"></td>
									<td>
										<div class="item-name">
											<a href="<?php $site->cms->admin->adminUrl("/custom-fields/edit/{$entity->id}", true); ?>"><?php $site->cms->sanitizeText($entity->title, true); ?></a>
										</div>
										<div class="item-actions">
											<a href="<?php $site->cms->admin->adminUrl("/custom-fields/edit/{$entity->id}", true); ?>">Edit</a>
											<span class="divider">|</span>
											<a href="<?php $site->cms->admin->adminUrl("/custom-fields/duplicate/{$entity->id}?csrf={$csrf}", true); ?>">Duplicate</a>
											<span class="divider">|</span>
											<a href="<?php $site->cms->admin->adminUrl("/custom-fields/delete/{$entity->id}", true); ?>" class="action-delete">Delete</a>
										</div>
									</td>
									<td><?php echo($config && $config->fields ? count($config->fields) : 0); ?></td>
								</tr>
							<?php
									endforeach;
								else:
							?>
								<tr>
									<td colspan="3"><em class="text-muted">No field groups yet, <a href="<?php $site->cms->admin->adminUrl("/custom-fields/new", true); ?>">click here to create one</a></em></td>
								</tr>
							<?php
								endif;
							?>

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>