<?php
/**
 * @package Mojo Adaptive Images
 * @version 1.0
 */
/*
Plugin Name: Mojo Adaptive Images
Plugin URI: http://www.ryandev.rocks
Description: Many users uses different kinds devices to view the web. It is important for a design to make their identity to be visible to all kinds of media. This plugin will handle the images on different devices.
Author: Ryan Gonzales
Version: 1.0
Author URI: http://www.ryandev.rocks
License: GNU GPL2
*/
/*  
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
global $wpdb;
global $mojo_table_name;
global $mojo_err;
$mojo_err = "";
$mojo_table_name = $wpdb->prefix . 'mojo_adaptive';

// WP API Scripts
add_action( 'wp_enqueue_scripts', 'mojo_adaptive_logo_scripts' );
add_action('admin_menu', 'mojo_adaptive_logo_menu');
add_action ( 'admin_enqueue_scripts', function () {
    if (is_admin ())
        wp_enqueue_media ();
} );

add_shortcode( 'mojo_adaptive_img', 'mojo_adaptive_img_shortcode' );
add_action( 'admin_head', 'mojo_admin_scripts' );

// INSTALL AND UNINSTALL MODULE
function mojo_adaptive_install() {
	global $wpdb;
	global $mojo_table_name;

	$table_name = $mojo_table_name;
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id int(20) NOT NULL AUTO_INCREMENT,
		gid int(20) NOT NULL,
		img_json text NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	$wpdb->query($sql);
}

function mojo_adaptive_uninstall() {
	$mojo_al_uninstall = get_option('mojo_al_uninstall');

	if($mojo_al_uninstall == 1){
	    global $wpdb;
		global $mojo_table_name;
	    $table_name = $mojo_table_name;

		$wpdb->query("DROP TABLE IF EXISTS $table_name");
	}
}

register_activation_hook( __FILE__, 'mojo_adaptive_install' );
register_deactivation_hook( __FILE__, 'mojo_adaptive_uninstall' );

function mojo_admin_scripts(){
	if(is_admin()){
		echo '<link rel="stylesheet" type="text/css" href="'.plugins_url( 'css/admin.css', __FILE__ ).'">';
		echo '<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">';
		echo '<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js" />';
	}
}

// END - INSTALL AND UNINSTALL MODULE

// [mojo_adaptive_img id=""]
function mojo_adaptive_img_shortcode($atts){
	$a = shortcode_atts( array(
        'gid' => '0',
    ), $atts );

	global $wpdb;
	global $mojo_table_name;
	$table_name = $mojo_table_name;

	$sql = 'SELECT * FROM ' . $table_name . ' WHERE gid = '.$a['gid'];
	$mojo_adaptive_image = $wpdb->get_results($sql);

	foreach($mojo_adaptive_image as $mai):
		$al_image_break = json_decode($mai->img_json);
		$al_gid = $mai->gid;
	endforeach;
	
	ob_start();
	require("view/shortcode.php");
	return ob_get_clean();
}

function mojo_adaptive_logo_scripts()
{
    wp_register_script( 'custom-script', plugins_url( '/js/mbi.script.js', __FILE__ ), array( 'jquery' ) );
    wp_enqueue_script( 'custom-script' );
}

// PAGE INSTANCES
function mojo_adaptive_logo_menu() {
	add_menu_page('Mojo Adaptive Images Manager', 'Adaptive Images', 'manage_options', 'mojo-adaptive-images', 'mojo_adaptive_images_manage', 'dashicons-images-alt', 11);
	add_submenu_page('mojo-adaptive-images', 'Mojo Adaptive Images Editor', 'Add Adaptive Images', 'manage_options', 'mojo-adaptive-editor', 'mojo_adaptive_images_editor');
}

function mojo_adaptive_images_manage(){
	global $mojo_err;
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	global $wpdb;
	global $mojo_table_name;
	$table_name = $mojo_table_name;

	if($_GET['al-command'] == 'del' && $_GET['id'] > 0 ){
		$mojo_al_id = $_GET['id'];
		$sql_query = 'DELETE FROM `'. $table_name.'` WHERE `id` = '.$_GET['id'];
		if($wpdb->query($sql_query)){
			$mojo_err = "Successfully Deleted.";
		}else{
			$mojo_err = "We are unable to delete entry. It seems that we can't find the entry that you are looking for.";
		}
	}

	$mojo_al_uninstall = get_option('mojo_al_uninstall');

	$sql = 'SELECT `id`,`gid` FROM ' . $table_name;
	$mojo_adaptive_images = $wpdb->get_results($sql);

	require("view/manage.php");
}

function mojo_adaptive_images_editor() {
	global $mojo_err;

	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

	if(!empty($_GET['id'])){
		global $wpdb;
		global $mojo_table_name;
		$table_name = $mojo_table_name;

		$sql = 'SELECT * FROM ' . $table_name . ' WHERE id = '.$_GET['id'];
		$mojo_adaptive_image = $wpdb->get_results($sql);

		foreach($mojo_adaptive_image as $mai):
			$al_image_break = json_decode($mai->img_json);
			$gid = $mai->gid;
		endforeach;
	}

	require("view/editor.php");
}

//EVENTS
if(!empty($_POST) && $_POST['al-post-pass'] == 1){
	$count = 0;

	foreach($_POST['image-break']['image'] as $img){
		$breakpoint = $_POST['image-break']['breakpoint'][$count];

		if($img == "" && $breakpoint == "") continue;

		$image_break[$count]['image'] = sanitize_text_field($img);
		$image_break[$count]['breakpoint'] = sanitize_text_field($breakpoint);
		$count++;
	}

	$img_json = json_encode($image_break);
	$gid = $_POST['gid'];

	if(empty($_POST['id']) || $_POST['id'] == ""){
		$sql_query = "INSERT INTO `".$mojo_table_name."` (`id`, `gid`, `img_json`) VALUES (NULL, '".$gid."', '".$img_json."');";
	}else{
		$sql_query = "UPDATE `".$mojo_table_name."` SET `gid` = '".$gid."', `img_json` = '".$img_json."' WHERE `wp_mojo_adaptive`.`id` = 2;";
	}

	if($wpdb->query($sql_query)){
		$mojo_err = "Successfully Saved.";
	}else{
		$mojo_err = "We are unable to save your images. It seems that there's an error while saving your entries or you didn't change entry.";
	}
}

if(!empty($_POST) && $_POST['al-post-pass'] == 2){
	$mojo_al_uninstall = $_POST['mojo_al_uninstall'];

	if ( get_option( 'mojo_al_uninstall' ) !== false ) {
	    update_option('mojo_al_uninstall', $mojo_al_uninstall);
	} else {
	    add_option( 'mojo_al_uninstall', $mojo_al_uninstall, null, 'no' );
	}

	$mojo_err = "Successfully Saved.";
}

?>
