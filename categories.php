<?php

include("globals.php");

if ($_REQUEST["action"] == "do_add"){
	$query = "INSERT INTO `anyInventory_categories` (`name`,`parent`) VALUES ('".$_REQUEST["name"]."','".$_REQUEST["parent"]."')";
	$result = query($query);
	
	$this_id = insert_id();
	
	if (is_array($_REQUEST["fields"])){
		foreach($_REQUEST["fields"] as $key => $value){
			$temp_field = new field($key);
			$temp_field->add_category($this_id);
		}
	}
	
	header("Location: ".$_SERVER["PHP_SELF"]);
}
elseif($_REQUEST["action"] == "do_delete"){
	if ($_REQUEST["delete"] == "Delete"){
		$category = new category($_REQUEST["id"]);
		
		$query = "DELETE FROM `anyInventory_categories` WHERE `id`='".$_REQUEST["id"]."'"; 
		$result = query($query);
		
		if ($_REQUEST["item_action"] == "delete"){
			$query = "DELETE FROM `anyInventory_items` WEHRE `category`='".$category->id."'";
			$result = query($query);
		}
		elseif($_REQUEST["item_action"] == "move"){
			$query = "UPDATE `anyInventory_items` SET `category`='".$_REQUEST["move_items_to"]."'";
			$result = query($query);
		}
		
		if ($_REQUEST["subcat_action"] == "delete"){
			delete_subcategories($category);
		}
		elseif($_REQUEST["subcat_action"] == "move"){
			$query = "UPDATE `anyInventory_categories` SET `parent`='".$_REQUEST["move_subcats_to"]."' WHERE `parent`='".$category->id."'";
			$result = query($query);
		}
		
		remove_from_fields($category->id);
	}
	
	header("Location: ".$_SERVER["PHP_SELF"]);
}

$title = 'anyInventory Categories';
$page_key = "categories";
$links = array(array("url"=>$_SERVER["PHP_SELF"]."?action=add","name"=>"Add a Category"));

if ($_REQUEST["action"] == "add"){
	$output = '
		<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data">
			<input type="hidden" name="action" value="do_add" />
			<table>
				<tr>
					<td class="form_label"><label for="name">Name:</label></td>
					<td class="form_input"><input type="text" name="name" id="name" value="" /></td>
				</tr>
				<tr>
					<td class="form_label"><label for="parent">Parent Category:</label></td>
					<td class="form_input">
						<select name="parent" id="parent">
							'.get_category_dropdown().'
						</select>
					</td>
				</tr>
				<tr>
					<td class="form_label">Fields:</td>
					<td class="form_input">
						'.get_fields_checkbox_area().'
					</td>
				</tr>
				<tr>
					<td class="form_label">&nbsp;</td>
					<td class="form_input"><input type="submit" name="submit" id="submit" value="Submit" /></td>
				</tr>
			</table>
		</form>';
}
elseif($_REQUEST["action"] == "delete"){
	$category = new category($_REQUEST["id"]);
	
	$output .= '
		<form method="post" action="'.$_SERVER["PHP_SELF"].'">
			<input type="hidden" name="id" value="'.$_REQUEST["id"].'" />
			<input type="hidden" name="action" value="do_delete" />
			<p>Are you sure you want to delete this category?</p>';
	
	$output .= '
		<div class="category_info">
			<p class="category_name"><b>Name:</b> '.$category->breadcrumb_names.'</p>
			<p class="category_fields"><b>Fields:</b> ';
	
	if(is_array($category->fields)){
		foreach($category->fields as $field){
			$output .= $field["name"].', ';
		}
		$output = substr($output, 0, strlen($output) - 2);
	}
	else{
		$output .= 'None';
	}
	
	$output .= '</p><p class="category_num_items"><b>Number of items inventoried in this category:</b> '.$category->num_items().'</p>';
	
	if ($category->num_items() > 0){
		$output .= '
			<p>
				<input type="radio" name="item_action" value="delete" /> Delete all items in this category<br />
			   	<input type="radio" name="item_action" value="move" /> Move all items in this category to <select name="move_items_to" id="move_items_to">'.get_category_dropdown($category->parent_id).'</select>.
			</p>';
	}
	
	$output .= '<p><b>Number of subcategories:</b> '.count($category->children).'</p>';
	
	if (count($category->children) > 0){
		$output .= '
			<p>
				<input type="radio" name="subcat_action" value="delete" /> Delete all sub-categories<br />
			   	<input type="radio" name="subcat_action" value="move" /> Move all sub-categories to <select name="move_subcats_to" id="move_subcats_to">'.get_category_dropdown($category->parent_id).'</select>.
			</p>';
	}
	
	$output .= '
			<p class="category_num_items"><b>Number of items inventoried in this category\'s subcategories:</b> '.$category->num_items_r().'</p>
			<p style="text-align: center;"><input type="submit" name="delete" value="Delete" /> <input type="submit" name="cancel" value="Cancel" /></p>
		</form>';
}
else{
	$output .= '<p><a href="'.$_SERVER["PHP_SELF"].'?action=add">Add a Category.</a></p>';
	
	//$query = "SELECT *,'' as `nosortcol_`,`name` as `sortcol_Name`,'' as `nosortcol_Fields`, '' as `nosortcol_Items` FROM `anyInventory_categories`";
	//$data_obj = new dataset_library("Categories", $query, $_REQUEST, "mysql");
	//$result = $data_obj->get_result_resource();
	//$rows = $data_obj->get_result_set();
	
	$rows = get_category_array();
	
	if (count($rows) > 0){
		$i = 0;
		
		foreach($rows as $row){
			$temp = new category($row["id"]);
			
			$color_code = (($i % 2) == 1) ? 'row_on' : 'row_off';
			$table_set .= '<tr class="'.$color_code.'">';
			$table_set .= '<td align="center" style="width: 10%; white-space: nowrap;"><a href="'.$_SERVER["PHP_SELF"].'?action=edit&amp;id='.$row["id"].'">[edit]</a> <a href="'.$_SERVER["PHP_SELF"].'?action=delete&amp;id='.$row["id"].'">[delete]</a></td>';
			$table_set .= '<td style="white-space: nowrap;">'.$row["name"].'</td>';
			$table_set .= '<td>';
			
			if (count($temp->fields) > 0){
				foreach($temp->fields as $field){
					$table_set .= $field["name"] . ', ';
				}
				
				$table_set = substr($table_set, 0, strlen($table_set) - 2);
			}
			
			$table_set .= '&nbsp;</td>';
			$table_set .= '<td>'.$temp->num_items().'</td>';
			$table_set .= '</tr>';
			$i++;
		}
	}
	else{
		$table_set .= '<tr class="row_off"><td>There are no categories to display.</td></tr>';
	}
	
	//$table_set = $data_obj->get_sort_interface() . $table_set . $data_obj->get_paging_interface();
	
	$output .= '<table style="width: 100%; background-color: #000000;" cellspacing="1" cellpadding="2">'.$table_set.'</table>';
}

include("header.php");
echo $output;
include("footer.php");

exit;

function get_category_dropdown($selected = 0){
	$output = '<option value="0">Top Level</option>';
	$output .= get_dropdown_children(0, '', $selected);
	
	return $output;
}

function get_dropdown_children($id, $pre = "", $selected = 0){
	$query = "SELECT * FROM `anyInventory_categories` WHERE `parent`='".$id."' ORDER BY `name` ASC";
	$result = query($query);
	
	if ($id != 0){
		$newquery = "SELECT `name` FROM `anyInventory_categories` WHERE `id`='".$id."'";
		$newresult = query($newquery);
		$category_name = result($newresult, 0, 'name');
		$pre .= $category_name . ' > ';
	}
	
	$list = '';
	
	if (num_rows($result) > 0){
		while ($row = fetch_array($result)){
			$category = $row["name"];
			
			$list .= '<option value="'.$row["id"].'"';
			if ($row["id"] == $selected) $list .= ' selected="selected"';
			$list .= '>'.$pre . $category.'</option>';
			
			$list .= get_dropdown_children($row["id"], $pre, $selected);
		}
	}
	
	return $list;
}

function get_fields_checkbox_area($checked = null){
	$query = "SELECT * FROM `anyInventory_fields` WHERE 1 ORDER BY `name` ASC";
	$result = query($query);
	
	$num_fields = num_rows($result);
	
	$output .= '<div id="field_checkboxes">
		<div style="float: left;">';
	
	for ($i = 0; $i < ceil($num_fields / 2); $i++){
		$output .= '<div class="checkbox"><input type="checkbox" name="fields['.result($result, $i, "id").']" value="yes" /> '.result($result, $i, "name").'</div>';
	}
	
	$output .= '</div>
		<div>';
	
	for (; $i < $num_fields; $i++){
		$output .= '<div class="checkbox"><input type="checkbox" name="fields['.result($result, $i, "id").']" value="yes" /> '.result($result, $i, "name").'</div>';	
	}
	
	$output .= '</div>';
	
	return $output;
}

function get_category_array(){
	$array = array();
	
	get_array_children(0, $array);
	
	return $array;
}

function get_array_children($id, &$array, $pre = ""){
	$query = "SELECT `name`,`id` FROM `anyInventory_categories` WHERE `parent`='".$id."' ORDER BY `name` ASC";
	$result = query($query);
	
	if ($id != 0){
		$newquery = "SELECT `name` FROM `anyInventory_categories` WHERE `id`='".$id."'";
		$newresult = query($newquery);
		$pre .= result($newresult, 0, 'name') . ' > ';
	}
	
	if (num_rows($result) > 0){
		while ($row = fetch_array($result)){
			$array[] = array("name"=>$pre.$row["name"],"id"=>$row["id"]);
						
			get_array_children($row["id"], $array, $pre);
		}
	}
}

function delete_subcategories($category){
	if (is_array($category->children)){
		foreach($category->children as $child){
			delete_subcategory($child);
		}
	}
	
	return;
}

function delete_subcategory($category){
	if (is_array($category->children)){
		foreach($category->children as $child){
			delete_subcategories($child);
		}
	}
	
	$query = "DELETE FROM `anyInventory_items` WHERE `category`='".$category->id."'";
	$result = query($query);
	
	$query = "UPDATE `anyInventory_categories` SET `parent`='".$category->parent_id."' WHERE `parent`='".$category->id."'";
	$result = query($query);
	
	$query = "DELETE FROM `anyInventory_categories` WHERE `id`='".$category->id."'";
	$result = query($query);
	
	remove_from_fields($category->id);
	
	return;
}

function remove_from_fields($cat_id){
	$query = "SELECT `id` FROM `anyInventory_fields` WHERE `categories` LIKE '%".$cat_id.",%'";
	$result = query($query);
	
	while($row = fetch_array($result)){
		$field = new field($row["id"]);
		$field->remove_category($cat_id);
	}
	
	return;
}

?>