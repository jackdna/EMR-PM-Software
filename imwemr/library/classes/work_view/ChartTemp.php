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
	

	function __construct(){
		
		$this->tbl = "chart_template";		
	}

	function update($arrVals){
		$sql = "UPDATE ".$this->tbl." SET 
				temp_name='".$arrVals["temp_name"]."', 
				temp_fields='".addslashes($arrVals["temp_fields"])."', 
				ccda_cpt_code='".addslashes($arrVals["ccda_cpt_code"])."',
				temp_fields_tech='".addslashes($arrVals["temp_fields_tech"])."' 
				WHERE id = '".$arrVals["id"]."' ";
		$res = sqlQuery($sql);
		return $arrVals["id"];
	}

	function insert($arrVals){
		$sql = "INSERT INTO ".$this->tbl." (id, temp_name, temp_fields, temp_fields_tech, ccda_cpt_code ) 
				VALUES (NULL, '".$arrVals["temp_name"]."', '".$arrVals["temp_fields"]."', '".$arrVals["temp_fields_tech"]."', '".addslashes($arrVals["ccda_cpt_code"])."')";
		return sqlInsert($sql);
	}
	
	function getIdFromName($nm){
		$id = false;
		$sql = "SELECT id FROM ".$this->tbl." WHERE LCASE(temp_name) = '".strtolower($nm)."' ";
		$row = sqlQuery($sql);
		if($row != false){
			$id = $row["id"];
		}
		return $id;
	}
	
	function getAll(){
		$arr = array();
		$sql = "SELECT * FROM ".$this->tbl." ORDER BY temp_name ";
		$rez = sqlStatement($sql);					
		for($i=1;$row=sqlFetchArray($rez);$i++){				
			$tId = $row["id"];
			$tNm = $row["temp_name"];	
			$tccda_cpt_code = $row["ccda_cpt_code"];		
			$arr[] = array("id"=>$tId,"name"=>$tNm,"ccda_cpt_code"=>$tccda_cpt_code);			
		}
		
		return $arr;
	}
	
	function getChartTemplateOptions($hasActChart=""){
		
		//Chart Template Options
		$arrPtTemplate = array();
		
		$usrtmpid="GETFORMSETTINGS";
		//if(empty($hasActChart)){ 
			$arrPtTemplate["Default"] = array("Default",$arrEmpty,"Default"."-[_]-".$usrtmpid);  
			$arrPtTemplate["Memo"] = array("Memo",$arrEmpty,"Memo"."-[_]-"."0");  
			$arrPtTemplate["Amendment"] = array("Amendment",$arrEmpty,"Amendment"."-[_]-"."0");  
		//}	
		
		$arrPtTemplate["Comprehensive"] = array("Comprehensive",$arrEmpty,"Comprehensive"."-[_]-"."0");

		
		$tmp = $this->getAll();
		$tmpLn = count($tmp);
		if($tmpLn > 0){
			for($i=0;$i<$tmpLn;$i++){
				if(!empty($tmp[$i]["id"]) && !empty($tmp[$i]["name"]) && (!isset($arrPtTemplate[$tmp[$i]["name"]]) || $tmp[$i]["name"] == "Comprehensive")){
					$arrPtTemplate[$tmp[$i]["name"]] = array($tmp[$i]["name"],$arrEmpty,$tmp[$i]["name"]."-[_]-".$tmp[$i]["id"]);
				}		
			}
		}
		
		$str="";
		$arr = $arrPtTemplate;
		if(count($arr)>0){
			foreach($arr as $k => $v){
				if(!empty($k)){
					$strAtr="";
					$strAtr=" data-id=\"".$v[2]."\"  "; 
					
					//Test
					if($k=="Default" || $k=="Memo" || $k=="Amendment"){
						$cls_def_opt = " def_opts ";
						if(empty($hasActChart)){  $cls_def_opt .= "" ; }else{ $cls_def_opt .= " hidden " ; }
					}else{ $cls_def_opt = ""; }
					
					$str.="<li class=\"".$cls_def_opt."\"><a href=\"#\"  ".$strAtr." >".$k."</a></li>";
				}
			}
		}		
		if(!empty($str)){ $str="<div  id=\"divMenuTemplate\"  class=\"hidden\" ><ul class=\"dropdown-menu\">".$str."</ul></div>"; }
		return $str;
	}
	
	function getTempInfo($id){
		$arr = array();
		if(!empty($id)){
			$sql ="SELECT * FROM ".$this->tbl." WHERE id = '".$id."' ";
			$row = sqlQuery($sql);
			if($row != false){
				$tId = $row["id"];
				$tNm = $row["temp_name"];
				$tFlds = $row["temp_fields"];	
				$tcpt_code = $row["ccda_cpt_code"];				
				$tFlds_tech = $row["temp_fields_tech"];	
				$arr = array($tId,$tNm,$tFlds,$tcpt_code,$tFlds_tech);
			}
		}
		return $arr;
	}
	
	function deleteTemp($id){
		if(!empty($id)){
			$sql = "DELETE FROM ".$this->tbl." WHERE id = '".$id."' ";
			$row = sqlQuery($sql);
		}
		return 0;	
	}
	
	function getChartTempId($pid,$fid){
		$tId = 0;
		$sql="SELECT templateId FROM chart_master_table WHERE id='".$fid."' AND patient_id='".$pid."' ";
		$row = sqlQuery($sql);
		if($row != false){				
			$tId = $row["templateId"];
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

	function getChartTemplateSettings($elem_chartTemplateId){
		global $user_type, $finalize_flag, $isReviewable,
			$elem_phth_pros, $elem_eyePrPh, $elem_curset_phth_pros;
			
		//if empty show users default template	
		if(empty($elem_chartTemplateId)){$elem_chartTemplateId = $this->getUserDefTempId();}
		//if empty show comprehensive	
		if(empty($elem_chartTemplateId)){$elem_chartTemplateId = $this->getIdFromName("Comprehensive");}	
		
		//chart note work view settings
		$arr_chart_wv_exams = array("Pupil",
					"EOM","External","L&A",
					"IOP/Gonio","SLE","Fundus Exam",
					"Refractive Surgery",
					"Conjunctiva","Cornea","Ant. Chamber",
					"Iris & Pupil","Lens", "DrawSLE",
					"Opt. Nev","Macula","Vitreous",
					"Periphery","Blood Vessels","DrawFundus",
					"Retinal Exam", "VF/OCT - GL"
					); //"CVF", "Amsler Grid"
		
		$arrTempProc_js=array("All");	//for js
		$tmp = $this->getTempInfo($elem_chartTemplateId);
		//for missing template
		if(count($tmp) == 0){ 
			$elem_chartTemplateId = $this->getIdFromName("Comprehensive"); 
			$tmp = $this->getTempInfo($elem_chartTemplateId);	
		}			
		if(!empty($tmp[1])){
			$elem_chartTempName = $tmp[1];
			
			//check for logged in user : physician or technician and chart finalized		
			//Please remember Scribe are same as Physician i.e. their view should be same as Physician View: userid = 13		
			//Can see physician view		
			if( in_array($user_type, $GLOBALS['arrValidCNPhy']) || ($finalize_flag == 1 && $isReviewable==false) || $user_type == 13 || !empty($_SESSION["flg_phy_view"])){			
				$arrTempProc = (!empty($tmp[2])) ? explode(",", stripslashes($tmp[2])) : array(); //Phy			
			}else{
				$arrTempProc = (!empty($tmp[4])) ? explode(",", stripslashes($tmp[4])) : array(); //Tech			
			}		
			
			$arrTempProc_js=json_encode($arrTempProc); //js
		}		

		//chart_admin_settings --
		if(empty($elem_chartTemplateId) || $elem_chartTempName=="Comprehensive"){
			$elem_settingCompVFOCT = $this->getCompPlasticSetting(1); //Comprehensive VFOCT
		}
		
		//loop wv exams 
		$strLoadExams="";
		foreach($arr_chart_wv_exams as $key => $val){
			if(in_array($val,$arrTempProc)){
				$strLoadExams.=$val.",";
			}
		}
		
		//One Eye Str
		if(!empty($strLoadExams)){	
			if(!empty($elem_phth_pros)&&!empty($elem_eyePrPh)){			
				$oe_prms = $elem_phth_pros."~!~".$elem_eyePrPh."~!~".$elem_curset_phth_pros;
			}else{
				$oe_prms = "";
			}
		}
		
		return array("arrTempProc_js"=>$arrTempProc_js,"arrTempProc"=>$arrTempProc,"elem_chartTempName"=>$elem_chartTempName,
					"elem_settingCompVFOCT"=>$elem_settingCompVFOCT,"strLoadExams"=>$strLoadExams,"oe_prms"=>$oe_prms);
		
	}
	
	function getUserDefTempId(){
		$tempId="";
		$sql = "SELECT chart_template_id FROM users WHERE id = '".$_SESSION["authId"]."' ";
		$row = sqlQuery($sql);
		if($row != false){
			$tempId = $row["chart_template_id"];				
		}
		return $tempId;
	}
	
}
?>