<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: pnTemplate.php
Coded in PHP7
Purpose: This class provides management of Operative note templates.
Access Type : Include file
*/
?>
<?php

class PnTemplate{	
	private $tbl;
	private $db;	
	function __construct(){
		//$this->db = $GLOBALS['adodb']['db'];
		$this->tbl = "pn_template";
		$this->templt_name = "temp_name";
		
	}
	
	//Give Temp Name and id Array
	public function getProgNtsInfo($str=" * "){
		$arrTemplates = array();
		$sql = "SELECT ".$str." FROM ".$this->tbl." ORDER BY ".$this->templt_name;
		$res = sqlStatement($sql); //$this->db->Execute($sql);
		if($res !== false){
			while ($row=sqlFetchArray($res)) {
				$id = $row["temp_id"];
				$name = $row["temp_name"];
				$arrTemplates[] = array("id"=>$id,"name"=>$name);
			}
		}else{
			print imw_error();
		}
		return $arrTemplates;
	}
	
	//Delete template
	public function deleteTemp($id){
		if(!empty($id)){
			$sql = "DELETE FROM ".$this->tbl." WHERE temp_id = '".$id."' ";
			$res = sqlQuery($sql); 
			return ($res !== false) ? 0 : 1;
		}
		return 0;
	}
	
	//Check template name exits
	public function isRecordExists($tempNm){		
		if(!empty($tempNm)){			
			$sql = "SELECT temp_id,temp_name FROM ".$this->tbl." WHERE LCASE(temp_name) = ".sqlEscStr(strtolower($tempNm))." ";
			$res = sqlQuery($sql); 
			if($res !== false){				
				$max = imw_num_rows($res);
				if($max > 0){					
					return 1;
				}
			}
		}
		return 0;
	}
	
	//Insert Records
	public function insertRecord($tName,$tData){
		$sql = "INSERT INTO ".$this->tbl." (temp_id, temp_name, temp_data) ".
			 " VALUES ".
			 " (NULL, ".sqlEscStr($tName).", ".sqlEscStr($tData).") ";
		$res = sqlInsert($sql); 
		if($res !== false){
			return $res;
		}
		return 0;
	}
	
	//Update Records
	public function updateRecord($tid, $tName,$tData){
		$sql = "UPDATE ".$this->tbl." SET ".
			 "temp_name = ".sqlEscStr($tName).", ".
			 "temp_data = ".sqlEscStr($tData)." ".
			 "WHERE temp_id = '".$tid."' ";
		$res = sqlQuery($sql); 
		return ($res !== false) ? 0 : 1;
	}
	
	//Get a Record
	public function getRecordInfo($tid, $str=" * "){
		$arr = array();
		if(!empty($tid)){			
			$sql = "SELECT ".$str." FROM ".$this->tbl." WHERE temp_id='".$tid."'";
			$row = sqlQuery($sql); 
			if(row !== false){				
				$arr[] = $row["temp_id"];				
				$arr[] = $row["temp_name"];				
				$arr[] = $row["temp_data"];
			}
		}
		return $arr;
	}
	
	//Get a name
	public function getTempName($tId){
		$str = "";
		$sql = "SELECT temp_name FROM ".$this->tbl." WHERE temp_id='".$tId."'";
		$row = sqlQuery($sql); 
		if($res !== false){
			$str = $row["temp_name"];
		}
		return $str;
	}
}
?>