<?php
include_once('nusoap.php');

class kennect {
	
	var $client = NULL;
	var $session = NULL;
	
	function kennect ($account){
		// login to api
		$wsdl = "lib/KinteraConnect.wsdl";
		$this->client = new nusoapclient($wsdl,true);
		$this->client->soap_defencoding = 'UTF-8';
		$this->client->decode_utf8  = false;
		/*
		 $loginRequest = array(
            'LoginName' => 'your login name',
            'Password' => 'your password',
            
            //Set optional parameters if needs to
            'SessionTimeout'=>30,
            'UserID'=>0,
            'AccountID'=>0,
            'VirtualAccountID'=>0
            );
	*/
		
		$loginRequest = $account;
		
		$loginResult = $this->client->call('Login', array('parameters' => array('request' => $loginRequest)));
		
				
		if (isset($loginResult['LoginResult']['SessionID']))
			$this->session=$loginResult['LoginResult']['SessionID'];
			$sessionHeader =
            "<SessionHeader xmlns=\"http://schema.kintera.com/API/\"><SessionID>"
            .$this->session.
            "</SessionID></SessionHeader>" ;
            $this->client->setHeaders($sessionHeader);
	}
	
	function query ($query, $page = 1){
		 
			
			$queryCondition = array(
		    'QueryText' => $query,
            'PageSize' => 250,
            'PageNumber' => $page
            );
			
			//Make server Query call
            $queryRequest = new soapval('request', 'QueryRequest', $queryCondition, false, 'tns');
            $param = array('request' => $queryRequest);
            $queryResponse = $this->client->call('Query', array('parameters' => $param));
			//$queryResult = $queryResponse['QueryResult'];
			//$print_r($this->client->request);
			if (isset($queryResponse['QueryResult'])){
				return $queryResponse['QueryResult'];
			}
			else{
				
				return array();
			}
	}
	
	function retrieve ($identifier, $entity){
		
		 	$property = new soapval('propertySet', 'AllProperties', array(), false, 'tns');
            $id = new soapval('id', $entity.'Identifier', $identifier, false, 'tns');
            $param = array('id' => $id, 'propertySet' => $property );
            $retrieveResult = $this->client->call('Retrieve', array('parameters' => $param));
			
			if (isset($retrieveResult['RetrieveResult']))
            	return $retrieveResult['RetrieveResult'];
			else
				return array();

	}
	
	function update ($data, $name){
			
			//cleanup data
			$newdata = $data;
			foreach ($newdata as $key => $val){
				if (empty($val))
					unset($data[$key]);
			}
			
			//send to process
			$entity = new soapval('entity', $name, $data, false, 'tns');
            $param = array('entity' => $entity);
            $result = $this->client->call('Update', array('parameters' => $param));
			
			if (empty($result)){
				return true;	
			}
			else {
				return false;	
			}
	}
	
	function create ($data, $name){
			
			//cleanup data
			$newdata = $data;
			foreach ($newdata as $key => $val){
				if (empty($val))
					unset($data[$key]);
			}
			
			//send to process

			$param = array('entity' => new soapval('entity', $name, $data, false, 'tns'));
            $createResult = $this->client->call('Create', array('parameters' => $param));
				
			if (isset($createResult['CreateResult'])){
				return $createResult['CreateResult'];
			}
			else {
				echo "<div style='color:#ff0000'>".$createResult['faultstring']."</div>";
				return false;	
			}
	}
	
	function delete ($identifier, $entity){
		
			//$id = new soapval('id', 'ContactProfileIdentifier', array('ContactID' => $contactID), false, 'tns');
			$id = new soapval('id', $entity.'Identifier', $identifier, false, 'tns');
            $param = array('id' => $id);
            $this->client->call('Delete', array('parameters' => $param));
			
			if (empty($result)){
				return true;	
			}
			else {
				return false;	
			}
	}
	
	function multiple ($identifier, $entity){
		
		if (substr($entity,strlen($entity)-1,1) == 'y')
			$entity = substr($entity,0,strlen($entity)-1).'ies';
		else
			$entity .= 's';
		
		
		 $retrieveMultipleRequest = new soapval('request','RetrieveMultiple'.$entity.'Request',	array('ContactID' => $identifier['ContactID']), false, 'tns');
											   
		// Make service call
		$param = array('request' => $retrieveMultipleRequest);
		$retrieveMultipleResponse = $this->client->call('RetrieveMultiple', array('parameters' => $param));
		//$retrieveRecords = $retrieveMultipleResponse['RetrieveMultipleResult']['Records']['Record'];
		//print_r($this->client->request);
		//print_r($retrieveMultipleResponse);
			//$queryResult = $queryResponse['QueryResult'];
		if (!empty($retrieveMultipleResponse['RetrieveMultipleResult'])){
				if (isset($retrieveMultipleResponse['RetrieveMultipleResult']['Records']['Record'][0]))
					$retrieveMultipleResponse['RetrieveMultipleResult']['Total'] = count($retrieveMultipleResponse['RetrieveMultipleResult']['Records']['Record']);
				elseif (!empty($retrieveMultipleResponse['RetrieveMultipleResult']['Records']))
					$retrieveMultipleResponse['RetrieveMultipleResult']['Total'] = 1;
				else
					$retrieveMultipleResponse['RetrieveMultipleResult']['Total'] = 0;
		
		
            	return $retrieveMultipleResponse['RetrieveMultipleResult'];
		}	else {	
				return array();
				
			}
	
			
	}
	
}
?>