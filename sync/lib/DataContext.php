<?php
/**************************************************************************************************
	Database context class
	Handles all tabase related transaction 
	
****************************************************************************************************/
include_once "Database.php";
include_once "Member.php"; 


class DataContext {
	
	var $connection;
	var $Objects;		// object array to be updated or inserted
	var $BadObjects;
	
	// context objects
	var $Members;


	function DataContext (){
		// initialize and open database connection
		$this->connection = new Database();
		$this->connection->connect();
		
		// initialize
		$this->Objects = array();
		$this->BadObjects = array();
		
		$this->Members = new ContextModel($this->connection, 'members','Member');

	}
	
	function Save($obj){
		
		if ($this->validate($obj)){
			$this->Objects[] = $obj;
			return true;
		}
		else {
			$this->BadObjects[] = $obj;
			return false;
		}		
	}
	
	function Update($obj){
		
		if ($this->validate($obj)){
			$obj->Action = "update";
			$this->Objects[] = $obj;
			return true;
		}
		else {
			$this->BadObjects[] = $obj;
			return false;
		}		
	}
	
	function Del($obj){
		$this->Delete($obj);
	}
	
	function Delete ($obj){
		
		if (!empty($obj)){
			$obj->Action = "delete";
			$this->Objects[] = $obj;
		}
	}
	
	function validate ($obj){
		
		// validate  data fields based on data types
		foreach ($obj->Fields as $field => $rules ){
			
			if (!$rules["Nullable"]){
				// Handle exceptional conditions
				if ($obj->Action == 'insert' && $rules["Auto"]){
					// new record, auto incriment field can be null
					continue;
				}
				
				//check for null value	
				if (empty($obj->{$field})){
					$obj->addError($field, "Cannot have null value");
					continue;
				}
			}
			
			// now, ignore nul fields
			if (empty($obj->{$field})){
				continue;
			}
			
			// data type validation
			if (($rules["Type"] == 'int' || $rules["Type"] == 'float' || $rules["Type"] == 'double') && !is_numeric($obj->{$field})){
				
				$obj->addError($field, "Numeric field contains invalid value : " .$obj->{$field});
				continue;
			}
			
			// check char length
			if ($rules["Type"] == 'char'){
				
				if (strlen($obj->{$field}) > $rules["Length"]){
					$obj->addError($field, "Data too long to field. Expected : ".$rules["Length"] . ", Contains : ". strlen($obj->{$field})  );
					continue;
				}
			}
			
			// fix data/time value issues
			if ($rules["Type"] == 'datetime' || $rules["Type"] == 'date'){
				$dt = strtotime($obj->{$field});
				if (!empty($dt)){
					$obj->{$field} = date("Y-m-d H:i:s",strtotime($obj->{$field}));				 
				}
				else {
					$obj->{$field} = date("Y-m-d H:i:s");
				}
			}
			
			if ($rules["Type"] == 'bool' ){
				if ($obj->{$field}){
					$obj->{$field} = 1;
				}
				else {
					$obj->{$field} = 0;
				}
			}		
			
		}
		
		if (empty($obj->Errors)){
			
			return $obj->validate();	
		}
		else {
			
			return false;	
		}
		
	}
	
	function Query ($q){
		
		// run a query on database (select)
		return $this->connection->fetch_all_array($q);	
		
	}
	
	function NonQuery ($q){
		// Run a non-query on database (insert, update, delete)
		$qid = $this->connection->query($q);
		if($qid != -1){
			return $this->connecttion->affected_rows;
		}
		else {
			return false;	
		}
	}
	
	
	function Submit ($CloseConnection = false) {
		// submit all data to database
		
		// execute statesmnts
		foreach ($this->Objects as $index =>  $obj){
			
			if (empty($obj)){
				continue;
			}
			
			// prepare data array
			$data = array();
			foreach ($obj->Fields as $field => $val){
								
				if (!empty($obj->{$field}) || $obj->{$field} === 0 ){
					$data[$field] = $obj->{$field};
				}				
			}
			
			if ($obj->Action == 'insert'){
				//echo "<h2>Inserting</h2>";
				$res = $this->connection->query_insert($obj->Table,$data );
				if ($res !== false){
					$obj->id = $res;
				}
			}
			
			if ($obj->Action == 'update'){
				//echo "<h2>Updating</h2>";
				unset($data[$obj->PrimaryKey]);
				$this->connection->query_update($obj->Table,$data ," {$obj->PrimaryKey} = {$obj->{$obj->PrimaryKey}} LIMIT 1");
			}
			
			if ($obj->Action == 'delete'){
				//echo "<h2>Updating</h2>";
				// make delete query
				$q = "DELETE FROM {$obj->Table} WHERE {$obj->PrimaryKey} = {$obj->{$obj->PrimaryKey}} LIMIT 1";
				$this->connection->query($q);
			}
			
			$this->Objects[$index] = NULL;			
		}
		
		
		// close database connection
		if($CloseConnection){
			$this->connection->close();
		}
		
	}
	
	// make sql query safe to run in the server
	function escape ($string){
		
		if(get_magic_quotes_runtime()) 
			$string = stripslashes($string);
			
		return @mysql_real_escape_string($string,$this->connection->link_id);
		
	}

}


class ContextModel {
	
	var $Table;
	var $Model;
	var $connection;
	
	function ContextModel (&$con, $type, $model){
		$this->Table = $type;	
		$this->Model = $model;
		$this->connection = $con;
	}
	
	function Find ($conditions = "", $orderBy = "", $limit = "", $page = ""){
		
		$query = $this->BuildQuery($conditions, $orderBy , 1);
		$data = $this->connection->query_first($query);
		
		if (!empty($data)){				
			
			return $this->Parse($data);
		}
		else {
			// data not found
			return false;
		}
	}
	
	function FindAll ($conditions = "", $orderBy = "", $limit = "", $page = ""){
		
		$query = $this->BuildQuery($conditions, $orderBy , $limit,$page );
		$rows = $this->connection->fetch_all_array($query);
		
		$output = array();
		
		if (!empty($rows)){			
			
			foreach ($rows as $data){
				if (!empty($rows)){
					$output[] =  $this->Parse($data);
				}
			}
		}
		
		return $output;
	}
	
	
	
	function Parse ($data){
		
		if (!empty($data)){
			$obj = new $this->Model();
			
			foreach ($data as $field => $val){
				
				if ($obj->Fields[$field]['Type'] == 'bool'){
					if($val == 1)
						$obj->{$field} = true;
					else
						$obj->{$field} = false;
				}
				else {
					$obj->{$field} = $val;
				}
				
			}
			
			$obj->Action = "update";
			
			return $obj;
		}
		else {
			// data not found
			return NULL;
		}
	}
	
	
	function BuildQuery ($conditions = "", $orderBy = "", $limit = "", $page = ""){
		
		$query = "SELECT * FROM {$this->Table}";
		
		if (!empty($conditions)){
			 $query .= " WHERE ".$conditions;
		}
		
		if (!empty($orderBy)){
			$query .= " ORDER BY ".$orderBy;	   
		}
		
		if (!empty($limit) && !empty($page)){
			$lower = $limit * ($page-1);
			$query .= " LIMIT ".$lower.",".$limit;	   
		}
		elseif (!empty($limit)){
			$query .= " LIMIT ".$limit;	   
		}
		
		return $query;
	}
	

}


?>