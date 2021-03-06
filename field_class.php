<?php

class field {
	var $id;					// The id of this field, matches up with id field in anyInventory_fields
	
	var $name;					// The name of this field.
	var $input_type;			// The input type of this field, can be one of: text, select, radio, checkbox, multiple
	var $values = array();		// The possible values for this field, doesn't apply to input type text
	var $default_value = '';	// The default value for this field, doesn't apply to input type text when size is greater than 255
	var $size = 0;				// The size (number of characters allowed) of this field, only entered by the user for input type text
	var $categories = array();	// The ids of the categories that use this field
	
	var $highlight = false;		// Whether or not the field is highlighted
	
	var $importance = 0;
	
	function field($field_id){
		global $db;
		
		// Set the id of this field.
		$this->id = $field_id;
		
		// Get the information about this field.
		$query = "SELECT * FROM " . $db->quoteIdentifier('anyInventory_fields') . " WHERE " . $db->quoteIdentifier('id') . "='".$this->id."'";
		$result = $db->query($query);
		if (DB::isError($result)) die($result->getMessage().'<br /><br />'.SUBMIT_REPORT . '<br /><br />'. $query);
		
		$row = $result->fetchRow();
		
		// Set the name and input type
		$this->name = $row["name"];
		$this->input_type = $row["input_type"];
		$this->importance = $row["importance"];
		
		if ($this->input_type != 'divider'){
			// Set the values; the values are stored separated by commas
			$this->values = unserialize($row["values"]);
			
			// Set the default value
			$this->default_value = $row["default_value"];
			
			// If there is a size set, set it here. Otherwise, just keep an empty string. 
			$this->size = ($row["size"] > 0) ? $row["size"] : '';
			
			// Set the categories, which are also stores separated by commas
			$this->categories = unserialize($row["categories"]);
			
			$this->highlight = $row["highlight"];
		}
	}
	
	// This function removes a field from a category.
	// ***It modifies the database, not just the object.***
	
	function remove_category($cat_id){
		if ($this->input_type != 'divider'){
			// Find the key of the category id in the array.
			$key = array_search($cat_id, $this->categories);
			
			// If the category id is in the array, remove it.
			if ($key !== false) unset($this->categories[$key]);
			
			// Synchronize with the DB.
			$this->refresh_categories($this->categories);
		}
	}
	
	// This function adds a field to a category.
	// ***It modifies the database, not just the object.***
	
	function add_category($cat_id){
		if ($this->input_type != 'divider'){
			// Add the category id to the array
			$this->categories[] = (string) ($cat_id / 1);
			
			// Remove any duplicate values.
			$this->categories = array_unique($this->categories);
			
			// Sort the values in order.
			sort($this->categories);
			
			// Synchronize with the DB.
			$this->refresh_categories($this->categories);
		}
	}
	
	// This function synchronizes the category list in the database with the category list in the object.
	
	function refresh_categories($cat_ids){
		global $db;
		
		if ($this->input_type != 'divider'){
			if (is_array($cat_ids)){
				$query = "UPDATE " . $db->quoteIdentifier('anyInventory_fields') . " SET " . $db->quoteIdentifier('categories') . "='".serialize($cat_ids)."' WHERE " . $db->quoteIdentifier('id') . "='".$this->id."'";
				$result = $db->query($query);
				if (DB::isError($result)) die($result->getMessage().'<br /><br />'.SUBMIT_REPORT . '<br /><br />'. $query);
			}
			
			return;
		}
	}
}

?>
