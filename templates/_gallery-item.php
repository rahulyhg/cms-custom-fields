<script type="text/template" id="template_gallery_item">
	<div class="image-wrapper image-preview image-grid" data-id="<%= item.id %>">
		<input type="hidden" name="fields[<%= field %>][]" value="<%= item.id %>">
		<img src="<%= item.image %>" alt="" class="img-responsive">
		<div class="image-actions">
			<a href="#" class="image-action action-danger js-gallery-delete"><i class="fa fa-fw fa-times"></i></a>
		</div>
	</div>
</script>