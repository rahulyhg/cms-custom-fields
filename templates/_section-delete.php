<div class="admin-title">
	<a href="<?php $site->cms->admin->adminUrl("/custom-fields/new", true); ?>" class="button button-primary pull-right">
		<i class="fa fa-fw fa-plus"></i>
		<span class="hide-mobile-inline"> Add Field Group</span>
	</a>
	<h2 class="section-title has-button">
		<a href="<?php $site->cms->admin->adminUrl("/custom-fields/", true); ?>" class="button-title"><i class="fa fa-angle-left"></i></a>
		<span>Delete Field Group</span>
	</h2>
</div>
<form action="" method="post">
	<?php $site->cms->getCsrfToken('admin.entity', 'input', true); ?>
	<p>Are you sure you want to delete <strong>&quot;<?php $site->cms->sanitizeText($entity->title, true); ?>&quot;</strong>?</p>
	<p class="text-muted">Tip: Deleted items can be recovered from the trash.</p>
	<a href="<?php $site->cms->admin->adminUrl("/custom-fields", true); ?>" class="button button button-link">No, go back to list</a>
	<button type="submit" class="button button-primary">Yes, delete</button>
</form>