<?php

class Member {
	
	/* Parameters required to validate data */
	var $Table = "members";
	var $Action = "insert"; 	// insert | update | delete
	var $PrimaryKey = "id";
	var $Errors;
	// validation rules
	var $Fields = array (
		"id" => array ("Type" => 'int', "Nullable" => false, "Auto" => false, "Length" => 20),
		"fname" => array ("Type" => 'char', "Nullable" => true, "Auto" => false, "Length" => 100),
		"lname" => array ("Type" => 'char', "Nullable" => true, "Auto" => false, "Length" => 100),
		"event_name" => array ("Type" => 'char', "Nullable" => true, "Auto" => false, "Length" => 255),
		"event_id" => array ("Type" => 'int', "Nullable" => true, "Auto" => false, "Length" => 11),
		"donation" => array ("Type" => 'double', "Nullable" => true, "Auto" => false, "Length" => 0)
	);
		
	// Table property list 
	var $id;
	var $fname;
	var $lname;
	var $event_name;
	var $event_id;
	var $donation;
	
	
	function Member () {
		// init object
		
		// clear previous validation errors
		$this->Errors = array();
	}
	
	function validate (){
		// custom validation rue goes here	
		return true;
	}
	
	function addError($field, $message){
		// add validation error
		$this->Errors[] = array("Field"=> $field, "Message" => $message);
	}
	
}

?>