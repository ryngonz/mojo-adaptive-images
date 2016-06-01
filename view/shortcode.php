<?php
	$breakpoints = array();
	foreach($al_image_break as $image_break):
?>
<img src="<?=$image_break->image?>" id="al_image_<?=$al_gid?>_<?=$image_break->breakpoint?>" class="al_image">
<?php
	$breakpoints[] = $image_break->breakpoint;
	endforeach; 
?>

<style>
	.al_image{
		display:none;
	}
<?php
	$count = 1;
	foreach($breakpoints as $breakpoint):
		
		if(isset($breakpoints[$count])){
			$media = "(max-width:".$breakpoint."px) and (min-width:".($breakpoints[$count] + 1)."px)";
		}else{
			$media = "(max-width:".$breakpoint."px)";
		}
?>
@media all and <?=$media?>{
	#al_image_<?=$al_gid?>_<?=$breakpoint?>{
		display:block;
	}
}
<?php
	$count++;
	endforeach;
?>
</style>