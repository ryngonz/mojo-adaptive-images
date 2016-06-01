<div class="wrap">
	<h1>Adaptive Images Manager <a href="admin.php?page=mojo-adaptive-editor" class="page-title-action">Add New</a></h1>
	<div class="clear-block"></div>
	<?php if($mojo_err != ""): ?>
		<div class="notice notice-warning is-dismissible">
	        <p><?php _e( $mojo_err, 'mojo-adaptive-images-notice' ); ?></p>
	    </div>
	<?php endif; ?>
	<table id="mojo-adaptive-table" class="wp-list-table widefat fixed striped posts">
	<thead>
		<tr>
			<th>ID</th>
			<th>Group ID</th>
			<th>Shortcode</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($mojo_adaptive_images as $mai): ?>
		<tr>
			<td><?=$mai->id?></td>
			<td><?=$mai->gid?></td>
			<td>[mojo_adaptive_logo gid="<?=$mai->gid?>"]</td>
			<td><a class="button button-primary" href="admin.php?page=mojo-adaptive-editor&id=<?=$mai->id?>"">Edit</a> <a class="button button-secondary" href="admin.php?page=mojo-adaptive-images&id=<?=$mai->id?>&al-command=del" onclick="return confirm('Are you sure that you want to delete this entry?')">Delete</a></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
	</table>
	<form id="mbi-form" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="POST">
		<p>Do you want to drop the database after deactivating the plugin?</p>
		<select name="mojo_al_uninstall">
			<option value="0">No</option>
			<option value="1" <?php if($mojo_al_uninstall == 1) echo "selected";?>>Yes</option>
		</select>&nbsp;
		<input type="hidden" name="al-post-pass" value="2">
		<input class="button button-primary" type="submit" value="Save">
	</form>
</div>

<script>
	jQuery(document).ready(function($){
	    $('#mojo-adaptive-table').DataTable();
	});
</script>