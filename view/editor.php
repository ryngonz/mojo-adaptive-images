<?php
	if(empty($_GET['id'])){
		$date = new DateTime();
		$gid = ceil($date->getTimestamp() / 4);
	}
?>
<div class="wrap">
<h1>Adaptive Image Editor</h1>
<?php if($mojo_err != ""): ?>
	<div class="notice notice-warning is-dismissible">
        <p><?php _e( $mojo_err, 'mojo-adaptive-images-notice' ); ?></p>
    </div>
<?php endif; ?>
<p>Use the shortcode: <strong>[mojo_adaptive_img gid="<?=$gid?>"]</strong></p>
<form id="mbi-form" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="POST">
	<div id="form-container">
		<?php foreach($al_image_break as $image_break): ?>
			<div class="mbi-image-container">
				<p><label style="font-weight:bold;">Screen Size:</label> <input type="number" name="image-break[breakpoint][]" class="image-breakpoint" value="<?=$image_break->breakpoint?>"> px;</p>
				<input class="image-field" type="text" value="<?=$image_break->image?>" name="image-break[image][]" style="width:100%;" placeholder="Image URL">
				<button class="button button-secondary upload_image_button" style="cursor:pointer;">Add Media</button> <button class="button button-secondary remove-breakpoint">Remove</button>
			</div>
		<?php endforeach; ?>
		<div class="mbi-image-container">
			<p><label style="font-weight:bold;">Screen Size:</label> <input type="number" name="image-break[breakpoint][]" class="image-breakpoint"> px;</p>
			<input class="image-field" type="text" value="" name="image-break[image][]" style="width:100%;" placeholder="Image URL">
			<button class="button button-secondary upload_image_button" style="cursor:pointer;">Add Media</button> <button class="button button-secondary remove-breakpoint">Remove</button>
		</div>
	</div>
	<input type="hidden" name="al-post-pass" value="1">
	<?php if(!empty($_GET['id'])) ?> <input type="hidden" name="id" value="<?=$_GET['id']?>">
	<input type="hidden" name="gid" value="<?=$gid?>">
	<button class="button button-primary" id="add-breakpoint">Add Breakpoint</button> <input class="button button-primary" type="submit" value="Save">
</form>

</div>

<script>
	jQuery(document).ready(function($){
	    $('.upload_image_button').live("click",function(e) {
	    	var getCurButton = $(this);
	        e.preventDefault();
	        var image = wp.media({ 
	            title: 'Upload Image',
	            multiple: false
	        }).open()
	        .on('select', function(e){
	            var uploaded_image = image.state().get('selection').first();
	            var image_url = uploaded_image.toJSON().url;
	            var prevfield = getCurButton.prev("input.image-field");
	            prevfield.val(image_url);
	        });
	        return false;
	    });

	    $('#add-breakpoint').click(function(e) {
	    	$( ".mbi-image-container:first-child" ).clone().appendTo( "#form-container" );
	    	$( ".mbi-image-container:last-child" ).css("display","none");
	    	$( ".mbi-image-container:last-child input.image-field" ).val("");
	    	$( ".mbi-image-container:last-child input.image-breakpoint" ).val("");
	    	$( ".mbi-image-container:last-child" ).slideDown();
	    	return false;
	    });

	    $(".remove-breakpoint").live("click",function(e) {
	    	var count_children = $("#mbi-form #form-container").children().length;
	    	if(count_children > 1){
	    		$( this ).parent(".mbi-image-container").slideUp("fast", function(){
	    			$(this).remove();
	    		});
	    	}
	    	return false;
	    });
	});
</script>
