<?php
/*
Plugin Name: Admin Notes
Plugin URI: http://www.polaraul.com/
Description: Creates and manages a very simple notes system for maintaining a to-do list etc.
Version: 1
Author: Paul Morley
Author URI: http://www.polaraul.com/
*/
/*  

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA
*/

class devat {
	
	// --------------------------------------------------------------------
	// Responsible for installing the plugin
	// --------------------------------------------------------------------
	function todo_install() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'devat_Adnotes';
		
		if($wpdb->get_var("SHOW TABLES LIKE '" . $table_name ."'") != $table_name) {
			$sql = "CREATE TABLE $table_name (
				task_id 	bigint(20) 	not null auto_increment,
				notes		text		not null default '',
				unique key id(task_id)
				);";
				
			$results = $wpdb->query($sql);
			
			// Add initial data
			$sql = "INSERT INTO `$table_name` (notes) VALUES ('An easy day, nothing to do')";
			$results = $wpdb->query($sql);
		}
	}
	
	// --------------------------------------------------------------------
	// Adds the ToDo page under Manage
	// --------------------------------------------------------------------
	function todo_addpages() {
		add_management_page('Manage your admin notes ', 'Admin Notes', 8, 'adnotes', array('devat', 'todo_addoption'));
	}
	
	

	
	
	// --------------------------------------------------------------------
	// Responsible for rendering the Admin Notes page under Manage
	// --------------------------------------------------------------------
	function todo_addoption()
	{		
	    global $otd_message;
		global $wpdb;
		if (!empty($otd_message)){
			$donegood =  '<div id="message" class="updated fade"><p>'.$otd_message.'</p></div>';
		}
		$table_name = $wpdb->prefix . 'devat_Adnotes';
	    $adminNotes = $wpdb->get_var("SELECT notes FROM $table_name WHERE task_id=1");
		$output_html = $donegood . '<div class="wrap">
		<h2>Admin Notes</h2>
		<form name="addtodo" id="addtodo" action="edit.php?page=adnotes" method="post">
		<input type="hidden" name="operation" value="update" />
		<table cellspacing="2" cellpadding="5" align="center">
		<tbody>
		    <tr>
				<td>
					<textarea name="notes" rows="4" cols="120">'.$adminNotes.'</textarea>
				</td>
			</tr>
			<tr>
				<td align="right">
					<input type="submit" name="submit" value="Update Notes" />
				</td>
			</tr>
		</tbody>
		</table>
		</form>
		</div>';	
		echo $output_html;
	}
}



// --------------------------------------------------------------------
// Called when user clicks activate in the plugin menu
// --------------------------------------------------------------------
if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
	if (defined('WPINC') && strpos($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME']) > 0) {
		add_action('init', array('devat', 'todo_install'));
	}
}

// --------------------------------------------------------------------
// Insert the mt_add_pages() sink into the plugin hook list for 'admin_menu'
// --------------------------------------------------------------------
add_action('admin_menu', array('devat', 'todo_addpages'));


// --------------------------------------------------------------------
// Add Admin Notes To Dashboard
// --------------------------------------------------------------------
add_action('activity_box_end', 'devat_get_todo');



// --------------------------------------------------------------------
// Handle post requests
// --------------------------------------------------------------------
$name = $_POST["operation"];
if('update' == $name) {
    global $otd_message;
	global $wpdb;
	$table_name = $wpdb->prefix . 'devat_Adnotes';
    $wpdb->query("UPDATE $table_name SET notes='" . $wpdb->escape($_POST['notes']) . "'");
	$otd_message = "Admin Notes Updated";
}

// --------------------------------------------------------------------
// Echo To DashBoard
// --------------------------------------------------------------------
function devat_get_todo()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'devat_Adnotes';
	$adminNotes = $wpdb->get_var("SELECT notes FROM $table_name WHERE task_id=1");
	echo '<h3>Admin Notes</h3>' . $adminNotes;
}
?>