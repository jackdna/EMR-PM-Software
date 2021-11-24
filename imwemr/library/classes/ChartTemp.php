<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: ChartTemp.php
Coded in PHP7
Purpose: This class provides functionality to manage chart note templates in admin->chart note->templates.
Access Type : Include file
*/
?>
<?php
//ChartTemp.php

class ChartTemp{

	private $tbl;
	private $db;

	function __construct(){
		$this->db = $GLOBALS['adodb']['db'];
		$this->tbl = "chart_template";		
	}

	function update($arrVals){
		$sql = "UPDATE ".$this->tbl." SET 
				temp_name='".$arrVals["temp_name"]."', 
				temp_fields='".addslashes($arrVals["temp_fields"])."', 
				ccda_cpt_code='".addslashes($arrVals["ccda_cpt_code"])."',
				temp_fields_tech='".addslashes($arrVals["temp_fields_tech"])."' 
				WHERE id = '".$arrVals["id"]."' ";
		$res = $this->db->Execute($sql) or die("Error in query: ".$sql." ".$this->db->errorMsg());
		return $arrVals["id"];
	}

	function insert($arrVals){
		$sql = "INSERT INTO ".$this->tbl." (id, temp_name, temp_fields, temp_fields_tech, ccda_cpt_code ) 
				VALUES (NULL, '".$arrVals["temp_name"]."', '".$arrVals["temp_fields"]."', '".$arrVals["temp_fields_tech"]."', '".addslashes($arrVals["ccda_cpt_code"])."')";
		$res = $this->db->Execute($sql) or die("Error in query: ".$sql." ".$this->db->errorMsg());
		return $this->db->Insert_ID();
	}
	
	function getIdFromName($nm){
		$id = false;
		$sql = "SELECT id FROM ".$this->tbl." WHERE LCASE(temp_name) = '".strtolower($nm)."' ";
		$row = $this->db->Execute($sql) or die("Error in query: ".$sql." ".$this->db->errorMsg());
		if($row != false){
			$id = $row->fields["id"];
		}
		return $id;
	}
	
	function getAll(){
		$arr = array();
		$sql = "SELECT * FROM ".$this->tbl." ORDER BY temp_name ";
		$res = $this->db->Execute($sql) or die("Error in query: ".$sql." ".$this->db->errorMsg());
		if($res != false){			
			while(!$res->EOF){				
				$tId = $res->fields["id"];
				$tNm = $res->fields["temp_name"];	
				$tccda_cpt_code = $res->fields["ccda_cpt_code"];		
				$arr[] = array("id"=>$tId,"name"=>$tNm,"ccda_cpt_code"=>$tccda_cpt_code);
				$res->MoveNext();
			}
		}
		return $arr;
	}
	
	function getTempInfo($id){
		$arr = array();
		if(!empty($id)){
			$sql ="SELECT * FROM ".$this->tbl." WHERE id = '".$id."' ";
			$res = $this->db->Execute($sql) or die("Error in query: ".$sql." ".$this->db->errorMsg());
			if($res != false){
				$tId = $res->fields["id"];
				$tNm = $res->fields["temp_name"];
				$tFlds = $res->fields["temp_fields"];	
				$tcpt_code = $res->fields["ccda_cpt_code"];				
				$tFlds_tech = $res->fields["temp_fields_tech"];	
				$arr = array($tId,$tNm,$tFlds,$tcpt_code,$tFlds_tech);
			}
		}
		return $arr;
	}
	
	function deleteTemp($id){
		if(!empty($id)){
			$sql = "DELETE FROM ".$this->tbl." WHERE id = '".$id."' ";
			$res = $this->db->Execute($sql) or die("Error in query: ".$sql." ".$this->db->errorMsg());
		}
		return 0;	
	}
	
	function getChartTempId($pid,$fid){
		$tId = 0;
		$sql="SELECT templateId FROM chart_master_table WHERE id='".$fid."' AND patient_id='".$pid."' ";
		$res = $this->db->Execute($sql) or die("Error in query: ".$sql." ".$this->db->errorMsg());
		if($res != false){				
			$tId = $res->fields["templateId"];
		}
		return $tId ;	
	}
	
	//chart_admin_settings --
	function getCompPlasticSetting($flg=0){
		$elem_settingPlastic=1;
		$elem_setting_vf_gl=1;
		$sql = "SELECT plastic, vf_gl FROM chart_admin_settings WHERE id = 1";
		$row=sqlQuery($sql);
		if($row!=false){
			$elem_settingPlastic=$row["plastic"];
			$elem_setting_vf_gl=$row["vf_gl"];
		}
		
		if($flg==1){ //VF_LG
			return $elem_setting_vf_gl;
		}else if($flg==2){
			return array($elem_settingPlastic, $elem_setting_vf_gl);
		}else {
			return $elem_settingPlastic;
		}
		
	}	
}
?>