<div class="admin-title">
	<a href="<?php $site->cms->admin->adminUrl("/custom-fields/new", true); ?>" class="button button-primary pull-right hide">
		<i class="fa fa-fw fa-plus"></i>
		<span class="hide-mobile-inline"> Add Field Group</span>
	</a>
	<h2 class="section-title has-button">
		<a href="<?php $site->cms->admin->adminUrl("/custom-fields/", true); ?>" class="button-title"><i class="fa fa-angle-left"></i></a>
		<span>Add Field Group</span>
	</h2>
</div>
<?php
	$data = [];
	$data['entity'] = $entity;
	$site->partial('editor', $data, $templates_dir);
?>