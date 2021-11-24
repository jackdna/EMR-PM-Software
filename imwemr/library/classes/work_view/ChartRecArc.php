<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: ChartRecArc.php
Coded in PHP7
Purpose: This Class provides Edit Functionality in Chart notes.
Access Type : Include file
*/
?>
<?php
///ChartRecArc.php

class ChartRecArc{

	private $db;
	private $pId;
	private $fId;
	private $uId;
	private $arrDbtbl;

	//Check Param
	private $chkTbl;

	public function __construct($pid,$fid,$uid){
		
		$this->pId = $pid;
		$this->fId = $fid;
		$this->uId = $uid;
		$this->arrDbtbl = array("chart_assessment_plans"=>array("patient_id","form_id","exam_date","doctorId"),
								"chart_correction_values"=>array("patient_id","form_id"),
								"chart_cvf"=>array("patientId","formId"),
								"chart_dialation"=>array("patient_id","form_id"),
								"chart_diplopia"=>array("patientId","formId"),
								"chart_eom"=>array("patient_id","form_id","exam_date","uid"),
								"chart_external_exam"=>array("patient_id","form_id","exam_date","uid"),
								"chart_gonio"=>array("patient_id","form_id","examDateGonio","uid"),
								"chart_iop"=>array("patient_id","form_id","exam_date","uid"),
								//"chart_la"=>array("patient_id","form_id","exam_date","uid"),
								
								"chart_drawings"=>array("patient_id","form_id","exam_date","uid"),
								"chart_lac_sys"=>array("patient_id","form_id","exam_date","uid"),
								"chart_lesion"=>array("patient_id","form_id","exam_date","uid"),
								"chart_lids"=>array("patient_id","form_id","exam_date","uid"),
								"chart_lid_pos"=>array("patient_id","form_id","exam_date","uid"),
								
								"chart_left_cc_history"=>array("patient_id","form_id","modi_date","pro_id"),
								"chart_left_provider_issue"=>array("patient_id","form_id"),
								"chart_master_table"=>array("patient_id","id","update_date","providerId"),
								"chart_objective_notes"=>array("", "obj_form_id"),
								"chart_optic"=>array("patient_id","form_id","exam_date","uid"),
								"chart_pupil"=>array("patientId","formId","examDate","uid"),
								//"chart_rv"=>array("patient_id","form_id","exam_date","uid"),
								
								"chart_vitreous"=>array("patient_id","form_id","exam_date","uid"),
								"chart_retinal_exam"=>array("patient_id","form_id","exam_date","uid"),
								"chart_macula"=>array("patient_id","form_id","exam_date","uid"),
								"chart_blood_vessels"=>array("patient_id","form_id","exam_date","uid"),
								"chart_periphery"=>array("patient_id","form_id","exam_date","uid"),
								
								//"chart_slit_lamp_exam"=>array("patient_id","form_id","exam_date","uid"),
								"chart_conjunctiva"=>array("patient_id","form_id","exam_date","uid"),
								"chart_cornea"=>array("patient_id","form_id","exam_date","uid"),
								"chart_ant_chamber"=>array("patient_id","form_id","exam_date","uid"),
								"chart_iris"=>array("patient_id","form_id","exam_date","uid"),
								"chart_lens"=>array("patient_id","form_id","exam_date","uid"),
								
								"chart_vis_master"=>array("patient_id","form_id"),								
								"amsler_grid"=>array("patient_id","form_id"),
								"ophtha"=>array("patient_id","form_id"),
								"memo_tbl"=>array("patient_id","form_id"),
								"pnotes"=>array("patient_id","form_id"),
								"surgical_tbl"=>array("patient_id","form_id")

								/*
								"disc"=>array("patientId","formId"),
								"disc_external"=>array("patientId","formId"),
								"ivfa"=>array("patient_id","form_id"),
								"nfa"=>array("patient_id","form_id"),
								"oct"=>array("patient_id","form_id"),
								"pachy"=>array("patientId","formId"),
								"test_labs"=>array("patientId","formId"),
								"test_other"=>array("patientId","formId"),
								"topography"=>array("patientId","formId"),
								"vf"=>array("patientId","formId"),
								*/
								);

	}
	//End construct

	public function getRes($tbl,$fields=" * ",$all_fields=0){
		$tmp = $this->arrDbtbl[$tbl];
		$f_patId = $tmp[0];
		$f_formId = $tmp[1];

		$phrasePid = "";
		if(!empty($f_patId)){
			$phrasePid = " AND ".$f_patId."='".$this->pId."'";
		}
		
		//---purge phrase-----
		if($tbl=="chart_eom" || $tbl=="chart_external_exam" || 
			$tbl=="chart_la" || $tbl=="chart_lid_pos" || $tbl=="chart_lids" || $tbl=="chart_lac_sys" || $tbl=="chart_lesion" || $tbl=="chart_drawings" || 
			$tbl=="chart_optic" || $tbl=="chart_pupil" || 
			$tbl=="chart_rv" || $tbl=="chart_vitreous" || $tbl=="chart_retinal_exam" || $tbl=="chart_macula" || $tbl=="chart_blood_vessels" || $tbl=="chart_periphery" || 
			$tbl=="chart_slit_lamp_exam" || $tbl=="chart_conjunctiva" || $tbl=="chart_cornea" || $tbl=="chart_ant_chamber" || $tbl=="chart_iris" ||  $tbl=="chart_lens" ||            
			$tbl=="chart_gonio" || $tbl=="chart_iop" || $tbl=="chart_dialation" ){
			$purgePhrse = " AND purged='0' ";
		}else{
			$purgePhrse = "";
		}		
		//--------
		
		//
		$sql = "SELECT ".$fields." FROM ".$tbl." WHERE  ".$f_formId."='".$this->fId."' ".$phrasePid." ".$purgePhrse ;
		//$res = $this->db->Execute($sql) or die("<br><br>Error in Query: ".$sql." : ".$this->db->errorMsg());
		$res = imw_query($sql);
		if($all_fields==1){
			return $res;
		}else if($res != false){
			$num = imw_num_rows($res);
			return ($num > 0) ? $res : false ;
		}
		return false;
	}

	public function isFinalized(){
		$sql = "SELECT id FROM chart_master_table ".
				"WHERE patient_id = '".$this->pId."' ".
				"AND finalize = '0' ".
				"AND delete_status='0' ";
		$res = sqlStatement($sql);
		if($res != false){
			$num = imw_num_rows($res);
			return ($num > 0) ? false : true ;
		}
		return false;
	}

	public function archive(){

		//Check if All forms are finalized if yes
		//And Change status to unfinalized
		if($this->isFinalized()){
			$sql = "UPDATE chart_master_table ".
					"SET update_date = '".wv_dt('now')."', finalize='0' , autoFinalize='0', finalizeDate='0000-00-00 00:00:00' WHERE id='".$this->fId."' AND patient_id = '".$this->pId."' ";
			$res = sqlQuery($sql);

		}else{
			return 1;
		}

		//Save User Info
		$sql = "INSERT INTO chart_records_archive (id, patient_id, form_id, user_id, add_datetime) ".
			   "VALUES (NULL, '".$this->pId."', '".$this->fId."', '".$this->uId."', '".wv_dt('now')."') ";
		$ins_id = sqlInsert($sql);		

		//Save Records
		foreach($this->arrDbtbl as $kdbtbl => $vdbtbl){
			$tbl = $kdbtbl;
			$this->arc_tbl_bin($tbl, $ins_id);
		}

		return 0;
	}
	
	function arc_tbl_bin($tbl, $ins_id){
		$rez = $this->getRes($tbl," * ",1);
		//if($rez != false){

			//Get Table Fields
			//Make xml
			$dom = new DOMDocument('1.0','utf-8');
			$rt_tag = $dom->appendChild($dom->createElement($tbl));

			$numCols = imw_num_fields($rez); //$rez->FieldCount();				
			if($numCols>0){
				$res = imw_fetch_assoc($rez);
				foreach($res as $f_name => $f_value){
					//$oField = $rez->FetchField($i); //Get Field Object
					//$f_name = $oField->name;
					//$f_value = $rez->fields[$f_name];
					//if( $f_name!="followup"){ //$f_name!="assess_plan" &&
						$f_value = htmlentities($f_value,ENT_QUOTES);
					//}
					
					$rt_tag->appendChild($dom->createElement($f_name,$f_value));
				}
			}

			##Debugging
			/*
			if($tbl == "ivfa"){
				$dom->formatOutput=true;
				print $dom->saveXml();
				exit;
			}
			*/
			##Debugging

			//xml Variable
			$xmlDoc = $dom->saveXml();

			//Save in Binary
			$sql = "INSERT INTO chart_records_archive_binary (id,chart_rec_arc_id,rec_doc,dbtable ) ".
					"VALUES(NULL,'".$ins_id."','','".$tbl."') ";
			$ins_id_bin = sqlInsert($sql); 
			
			//Update Blob
			$sql = "UPDATE chart_records_archive_binary SET rec_doc='".sqlEscStr($xmlDoc)."' WHERE id=".$ins_id_bin;
			$row = sqlQuery($sql);
			
		//}
	
	}
	
	/*reset table record in case of loss*/
	
	function reset_binary_data($ins_id_bin, $tbl){
		
		$ar_db_tbl = array_keys($this->arrDbtbl);
		
		if(!empty($ins_id_bin) && !empty($tbl) && in_array($tbl, $ar_db_tbl)){	
		
			$rez = $this->getRes($tbl," * ",1);
			
			//Make xml
			$dom = new DOMDocument('1.0','utf-8');
			$rt_tag = $dom->appendChild($dom->createElement($tbl));

			$numCols = imw_num_fields($rez); //$rez->FieldCount();				
			if($numCols>0){
				$res = imw_fetch_assoc($rez);
				foreach($res as $f_name => $f_value){					
					$f_value = htmlentities($f_value,ENT_QUOTES);
					$rt_tag->appendChild($dom->createElement($f_name,$f_value));
				}
			}
			
			//xml Variable
			$xmlDoc = $dom->saveXml();
			
			//Update Blob
			$sql = "UPDATE chart_records_archive_binary SET rec_doc='".sqlEscStr($xmlDoc)."' WHERE id=".$ins_id_bin;
			$row = sqlQuery($sql);
		}
	}

	//
	public function getXml2Array($strXml){
		$arr = array();
		$dom = new DomDocument();
		//Get Values From XML
		$dom->loadXML($strXml);
		$root = $dom->documentElement;
		if(count($root->childNodes) > 0){
			foreach($root->childNodes as $field){
				if($field->nodeType == 1){
					$arr[$field->tagName] = $field->firstChild->nodeValue;
				}
			}
		}
		return $arr;
	}

	public function getArchivedRes($tbl,$sel=" * "){

		$arr = array();
		$sql = "SELECT * FROM chart_records_archive c1 ".
				"INNER JOIN chart_records_archive_binary c2 ON c2.chart_rec_arc_id = c1.id ".
				"WHERE c1.patient_id = '".$this->pId."' AND c1.form_id='".$this->fId."' ".
				"AND c2.dbtable = '".$tbl."' ".
				"ORDER BY c1.add_datetime DESC ";
		$res = sqlStatement($sql); //$this->db->Execute($sql) or die("<br><br>Error in Query: ".$sql." : ".$this->db->errorMsg());
		
		//--
		if(imw_num_rows($res) <= 0 ){
			$tblm="";
			if($tbl == "chart_lids" || $tbl == "chart_lesion" || $tbl == "chart_lid_pos" || $tbl == "chart_lac_sys"){
				$tblm = "chart_la";
			}else if($tbl == "chart_vitreous" || $tbl == "chart_retinal_exam" || $tbl == "chart_macula" || $tbl == "chart_blood_vessels" || $tbl == "chart_periphery"){
				$tblm = "chart_rv";
			}
			if(!empty($tblm)){
				$arr =  $this->getArchivedRes($tblm,$sel);
				return $arr; 
			}
		}
		//--
		
		if($res != false){
			for($i=1;$row=sqlFetchArray($res);$i++){
				$rec_doc = $row["rec_doc"];
				$mod_dt = $row["mod_dt"];
				$mod_uid = $row["mod_uid"];
				
				//clear special chars
				$rec_doc = str_replace("&shy;", "andshysc", $rec_doc);

				//Get Values From XML
				$arr[]=array("rec"=>$this->getXml2Array($rec_doc),"dt"=>$mod_dt,"uid"=>$mod_uid);

				
			}
		}
		return $arr;
	}
	
	function print_arc_record($id){
		$pr = array();
		$sql = " SELECT * FROM  `chart_records_archive_binary` WHERE  `id` ='".$id."' ";
		$row=sqlQuery($sql);
		if($row!=false){
		
			$rec_doc = $row["rec_doc"];
			$pr = $this->getXml2Array($rec_doc);
		
		}

		echo "<pre>";
		print_r($pr);
		
	}
	
	public function getCurUid_Exdt($tblName){

		$tmp = $this->arrDbtbl[$tblName];
		$f_exDt = (isset($tmp[2]) && !empty($tmp[2])) ? $tmp[2] : "";
		$f_uId = (isset($tmp[3]) && !empty($tmp[3])) ? $tmp[3] : "";

		$res = $this->getRes($tblName," $f_exDt, $f_uId ");

		if($res !== false){
			for($i=1;$row=sqlFetchArray($res);$i++){
				$v_exDt = $row[$f_exDt];
				$v_uId = $row[$f_uId];
				
			}
		}

		//
		return array("exDt"=>$v_exDt,"uId"=>$v_uId);
	}

	public function getArcRecId($latest="0"){
		$arr = array();
		$limitPh = ($latest == "1") ? " LIMIT 0,1 " : "";
		$sql = "SELECT id FROM chart_records_archive ".
			   "WHERE patient_id = '".$this->pId."' AND form_id='".$this->fId."' ".
			   "ORDER BY add_datetime DESC, id DESC ".
			   $limitPh;
		$res = sqlStatement($sql);
		if($res !== false){
			for($i=1;$row=sqlFetchArray($res);$i++){
				$arr[] = $row["id"];
			}
		}

		return ($latest == "1") ? $arr[0] : $arr;
	}
	
	function isArchived(){
		$id = $this->getArcRecId(1); 
		$r = (!empty($id)) ? true : false;  
		return $r;
	}
	
	public function setChangeDt($tblName){

		$arcId = $this->getArcRecId("1");
		if(!empty($arcId) && !empty($tblName)){
			$sql = "UPDATE chart_records_archive_binary ".
					"SET mod_dt='".wv_dt('now')."', mod_uid='".$this->uId."' ".
					"WHERE dbtable = '".$tblName."' AND chart_rec_arc_id = '".$arcId."' ";
			$res = sqlQuery($sql);
		}
	}

	public function setChkTbl($tbl){
		$this->chkTbl = $tbl;
	}

	public function getChangeColorCss($flgCss,$chkstr,$strarc){
		$showstr=$focsjs="";
		$flgCss = trim($flgCss);
		$chkstr = trim($chkstr);
		$strarc = trim($strarc);

		if(empty($flgCss)){
			if(!empty($chkstr)){
				if(!empty($strarc)){
					if($strarc != $chkstr){
						$flgCss = "arcvChanged"; 
					}
				}else {
					$flgCss = "arcvNew";
				}

			}else{
				if(!empty($strarc)){
					$flgCss = "arcvDeleted";
					$showstr = $strarc;
					$focsjs = " onfocus=\"remRedStyleArc(this)\"  ";
				}
			}
		}
		return array($flgCss,$showstr,$focsjs);
	}

	public function getChangeDiv($strarc, $chkstr, $curArcval, $flgclr,$exDt,$uidNm ){
		$strDiv="";
		$strarc = trim($strarc);
		$chkstr = trim($chkstr);
		$curArcval = trim($curArcval);

		if(!empty($strarc)){
			if((  $strarc != $chkstr &&
				  $curArcval != $strarc) ||
				$flgclr == "red_strike"){
			$strDiv .= "". //"<!-- Arc_"." -->".
						"<div>".$exDt." ".$uidNm."<br />".
						"".nl2br($strarc).
						"</div>";
						//"<!-- Arc_"." -->";
			$curArcval = $strarc;
			}
		}else{
			if(!empty($chkstr)){
			$strDiv .= "".//"<!-- Arc_"." -->".
						"<div>".$exDt." ".$uidNm."<br />".
						"". // No Value will come here
						"</div>";
						//"<!-- Arc_"." -->";
			$curArcval = $strarc;
			}
		}

		return array($strDiv,$curArcval);
	}

	public function getBigDiv($elmId, $divch, $focsjs, $divCls,$divMn=""){
		$strMoe = $focsjs;
		
		if(!empty($divMn)){
			$divMn="<hr/>".$divMn;
		}
		
		if(!empty($divch)){
			//$strMoe .= " onmouseover=\"showdivArc('".$elmId."',1);\" onmouseout=\"showdivArc('".$elmId."',0);\" ";
			$strMoe = "1";	//This pointer is used to check if edit notes exits
			/*
			$divch = "<div id=\"div_arc_".$elmId."\"
							onmouseover=\"showdivArc('".$elmId."',1);\"
							onmouseout=\"showdivArc('".$elmId."',0);\"
							class=\"arcDiv ".$divCls."\">".$divch.$divMn."</div>";
			*/				
			$divch = "<div id=\"div_arc_".$elmId."\"							
							class=\"arcDiv ".$divCls."\">".$divch.$divMn."</div>";				
		}
		return array($strMoe,$divch);
	}

	public function getArcRec($arIn){ //array(elem id => Field, Check Str)

		//Mouse Over Events
		$ar_moeArc = $ar_strFunOnFocus = array();
		//Color
		$ar_flgArcColor = array();
		//Divs
		$ar_divArc = array();
		//Str To Show
		$ar_str2Show=array();

		try{

		$arrArc = $this->getArchivedRes($this->chkTbl);

		if(count($arrArc) > 0){
		  $ar_currArcVal=array();
		  foreach($arrArc as $key => $val){
			$arrArcVal = $val["rec"];
			$k= $key;
			$mod_dt=$val["dt"];
			$mod_uid=$val["uid"];

			//User fun
			$oUsrfun = new User($mod_uid);
			$elem_ExDt_arc = ($mod_dt != "0000-00-00 00:00:00") ? wv_formatDate($mod_dt,0,2) : "";
			list($fullCcUid,$shrtCcUid) = $oUsrfun->getName(2);

			//Loop 2 For Fields --
			foreach($arIn as $key2 => $val2){
			$elmId = $key2;
			$chkFld = $val2[0];
			$chkStr = $val2[1];

			$elem_arc = $arrArcVal[$chkFld];

				//assessment And Plan xml
				if($chkFld == "assess_plan"){
					//Assess ------------------
					$arr_ap_ret = $val2[3]; //Array of return in AP
					$lenAssess_oa_arc = $chkStr; // Cur Length

					// Get Ass and plan xml
					$strXml_arc = $arrArcVal["assess_plan"];
					$patient_id_arc = $arrArcVal["patient_id"];
					$form_id_arc = $arrArcVal["form_id"];
					
					

					//Loop Ass --
					$oChartApXml_arc = new ChartAP($patient_id_arc,$form_id_arc);
					//$arrApVals_arc = $oChartApXml_arc->getVal_Str($strXml_arc);
					$arrApVals_arc = $oChartApXml_arc->getVal();
					$arrAp_arc = $arrApVals_arc["data"]["ap"];
					$lenAssess_arc = count($arrAp_arc);
					if($lenAssess_oa_arc < $lenAssess_arc){
						$lenAssess_oa_arc = $lenAssess_arc;
					}

					for($i=0;$i<$lenAssess_oa_arc;$i++){
						$j=$i+1;

						$elem_assessment_arc = htmlentities($arrAp_arc[$i]["assessment"]);
						$elem_plan_arc =htmlentities($arrAp_arc[$i]["plan"]);

						//check if color is set
						//Ass
						if(!isset($ar_flgArcColor["as"][$j]) || empty($ar_flgArcColor["as"][$j])){
							$elem_assessment_tmp = "elem_assessment_full".$j;
							///global $$elem_assessment_tmp;
							$chkStr_as = $arr_ap_ret[$elem_assessment_tmp];
							$arTmp = $this->getChangeColorCss($ar_flgArcColor["as"][$j],$chkStr_as,$elem_assessment_arc);
							$ar_flgArcColor["as"][$j]=$arTmp[0];
							$ar_str2Show["as"][$j]=$arTmp[1];
							$ar_strFunOnFocus["as"][$j]=$arTmp[2];
						}

						//Plan
						if(!isset($ar_flgArcColor["pl"][$j]) || empty($ar_flgArcColor["pl"][$j])){
							$elem_plan_tmp = "elem_plan".$j;
							//global $$elem_plan_tmp;
							$chkStr_pl = $arr_ap_ret[$elem_plan_tmp];
							$arTmp = $this->getChangeColorCss($ar_flgArcColor["pl"][$j],$chkStr_pl,$elem_plan_arc);
							$ar_flgArcColor["pl"][$j]=$arTmp[0];
							$ar_str2Show["pl"][$j]=$arTmp[1];
							$ar_strFunOnFocus["pl"][$j]=$arTmp[2];
						}
						//check if color is set

						//Divs--
						//Ass
		$arTmp = $this->getChangeDiv($elem_assessment_arc, $chkStr_as, $currArcVal["as"][$j], $ar_flgArcColor["as"][$j],
										$elem_ExDt_arc,$fullCcUid );
						$ar_divArc["as"][$j] .= $arTmp[0];
						$currArcValas["as"][$j] = $arTmp[1];
						//Plan
		$arTmp = $this->getChangeDiv($elem_plan_arc, $chkStr_pl, $currArcVal["pl"][$j], $ar_flgArcColor["pl"][$j],
										$elem_ExDt_arc,$fullCcUid );
						$ar_divArc["pl"][$j] .= $arTmp[0];
						$currArcValas["pl"][$j] = $arTmp[1];
						//Divs--
					}
					//Loop Ass --
					//Assess ------------------

				}else if($chkFld == "followup"){ // FU xml
					//FU ------------------
					$arrFuVals = $val2[3]; //Array of return in AP
					$lenFu_oa_arc = $chkStr; // Cur Length
					//global $arrFuVals;					

					//Get Follow up --
					$elem_follow_up_arc = $arrArcVal["followup"];
					$elem_followUpNumber_arc = $arrArcVal["follow_up_numeric_value"];
					$elem_followUp_arc = $arrArcVal["follow_up"];
					$elem_followUpVistType_arc = $arrArcVal["followUpVistType"];
					$patient_id_arc = $arrArcVal["patient_id"];
					$form_id_arc = $arrArcVal["form_id"];

					//OFu
					$oFu = new Fu($patient_id_arc,$form_id_arc);

					$arrFuVals_arc = array();
					$lenFu_arc = 0;
					if(!empty($elem_follow_up_arc)){
						list($lenFu_arc, $arrFuVals_arc) = $oFu->fu_getXmlValsArr($elem_follow_up_arc);
					}
					// No Option
					if(empty($lenFu_arc)){
						$arrFuVals_arc[] = array( "number" => $elem_followUpNumber_arc,
													"time" => $elem_followUp_arc,
													"visit_type" => $elem_followUpVistType_arc);
						$lenFu_arc = 1;
					}
					//Get Follow up --

					//Loop Follow up --
					if($lenFu_oa_arc < $lenFu_arc){
						$lenFu_oa_arc = $lenFu_arc;
					}

					for($i=0;$i<$lenFu_oa_arc;$i++){
						$j=$i+1;

						$elem_followUpNumber_arc = $elem_followUp_arc = $elem_followUpVistType_arc = "";
						$elem_followUpNumber_arc = trim($arrFuVals_arc[$i]["number"]);
						$elem_followUp_arc = trim($arrFuVals_arc[$i]["time"]);
						$elem_followUpVistType_arc = trim($arrFuVals_arc[$i]["visit_type"]);

						//check if color is set
						//number
						if(!isset($ar_flgArcColor["number"][$j]) || empty($ar_flgArcColor["number"][$j])){
							$chkStr_nm = $arrFuVals[$i]["number"];
							$arTmp = $this->getChangeColorCss($ar_flgArcColor["number"][$j],$chkStr_nm,$elem_followUpNumber_arc);
							$ar_flgArcColor["number"][$j]=$arTmp[0];
							$ar_str2Show["number"][$j]=$arTmp[1];
							$ar_strFunOnFocus["number"][$j]=$arTmp[2];
						}

						//time
						if(!isset($ar_flgArcColor["time"][$j]) || empty($ar_flgArcColor["time"][$j])){
							$chkStr_tm = $arrFuVals[$i]["time"];
							$arTmp = $this->getChangeColorCss($ar_flgArcColor["time"][$j],$chkStr_tm,$elem_followUp_arc);
							$ar_flgArcColor["time"][$j]=$arTmp[0];
							$ar_str2Show["time"][$j]=$arTmp[1];
							$ar_strFunOnFocus["time"][$j]=$arTmp[2];
						}

						//visit_type
						if(!isset($ar_flgArcColor["visit_type"][$j]) || empty($ar_flgArcColor["visit_type"][$j])){
							$chkStr_vt = $arrFuVals[$i]["visit_type"];
							$arTmp = $this->getChangeColorCss($ar_flgArcColor["visit_type"][$j],$chkStr_vt,$elem_followUpVistType_arc);
							$ar_flgArcColor["visit_type"][$j]=$arTmp[0];
							$ar_str2Show["visit_type"][$j]=$arTmp[1];
							$ar_strFunOnFocus["visit_type"][$j]=$arTmp[2];
						}
						//check if color is set

						//Divs --
						//number
						$arTmp = $this->getChangeDiv($elem_followUpNumber_arc, $chkStr_nm, $currArcVal["number"][$j],
													$ar_flgArcColor["number"][$j],
													$elem_ExDt_arc,$fullCcUid );
						$ar_divArc["number"][$j] .= $arTmp[0];
						$currArcValas["number"][$j] = $arTmp[1];
						//time
						$arTmp = $this->getChangeDiv($elem_followUp_arc, $chkStr_tm, $currArcVal["time"][$j],
													$ar_flgArcColor["time"][$j],
													$elem_ExDt_arc,$fullCcUid );
						$ar_divArc["time"][$j] .= $arTmp[0];
						$currArcValas["time"][$j] = $arTmp[1];
						//visit_type
						$arTmp = $this->getChangeDiv($elem_followUpVistType_arc, $chkStr_vt, $currArcVal["visit_type"][$j],
													$ar_flgArcColor["visit_type"][$j],
													$elem_ExDt_arc,$fullCcUid );
						$ar_divArc["visit_type"][$j] .= $arTmp[0];
						$currArcValas["visit_type"][$j] = $arTmp[1];
						//Divs --
					}
					//Loop Follow up --

					//FU ------------------
				}else{
					//Other ------------------
				//Check for WNL field in Summary --
				if(empty($elem_arc) && isset($val2[3]) && !empty($val2[3])){
					$wnl = $arrArcVal[$val2[3]];
					if($wnl) {
						$elem_arc = (isset($val2[4])) ? $val2[4] : "WNL";
					}
				}
				//Check for WNL field in Summary --

				//Cur Color set CHECK for Changes--
				if(!isset($ar_flgArcColor[$elmId])) $ar_flgArcColor[$elmId] = "";
				if(!isset($ar_str2Show[$elmId])) $ar_str2Show[$elmId] = "";
				if(!isset($ar_strFunOnFocus[$elmId])) $ar_strFunOnFocus[$elmId] = "";
				if(!isset($ar_currArcVal[$elmId])) $ar_currArcVal[$elmId]="";
				if(!isset($ar_divArc[$elmId])) $ar_divArc[$elmId] = "";

				if(!isset($ar_flgArcColor[$elmId]) || empty($ar_flgArcColor[$elmId])){
					$arTmp = $this->getChangeColorCss($ar_flgArcColor[$elmId],$chkStr,$elem_arc);
					$ar_flgArcColor[$elmId]=$arTmp[0];
					$ar_str2Show[$elmId]=$arTmp[1];
					$ar_strFunOnFocus[$elmId]=$arTmp[2];
				}

				//Cur Color set CHECK for Changes--

				//Divs  --
				$arTmp = $this->getChangeDiv($elem_arc, $chkStr, $ar_currArcVal[$elmId], $ar_flgArcColor[$elmId],$elem_ExDt_arc,$fullCcUid );
				$ar_divArc[$elmId] .= $arTmp[0];
				$ar_currArcVal[$elmId] = $arTmp[1];
				//Divs --
					//Other ------------------

				}

			}
			//Loop 2 For Fields --

		  } // Loop

		  //Add Outer Div and mouseOver event
		  //Loop 2 For Fields --


		  foreach($arIn as $key2 => $val2){
			$elmId = $key2;
			$divCls = $val2[2];
			$chkStr = $val2[1];
			$chkMNhtm = $val2[5]; //modify div

			//assessment And Plan xml
			if($elmId == "assess_plan"){
				$lenAssess_oa_arc = $chkStr; // Cur Length
				for($i=0;$i<$lenAssess_oa_arc;$i++){
					$j=$i+1;

					//Ass
					$elmId = "elem_assessment".$j;
					$arTmp = $this->getBigDiv($elmId, $ar_divArc["as"][$j], $ar_strFunOnFocus["as"][$j], $divCls);
					$ar_moeArc["as"][$j] = $arTmp[0];
					$ar_divArc["as"][$j] = $arTmp[1];
					//Plan
					$elmId = "elem_plan".$j;
					$arTmp = $this->getBigDiv($elmId, $ar_divArc["pl"][$j], $ar_strFunOnFocus["pl"][$j], $divCls);
					$ar_moeArc["pl"][$j] = $arTmp[0];
					$ar_divArc["pl"][$j] = $arTmp[1];
				}

			}else if($elmId == "followup"){
				$lenFu_oa_arc = $chkStr; // Cur Length
				for($i=0;$i<$lenFu_oa_arc;$i++){
					$j=$i+1;
					//number
					$elmId = "elem_followUpNumber_".$j;
					$arTmp = $this->getBigDiv($elmId, $ar_divArc["number"][$j], $ar_strFunOnFocus["number"][$j], $divCls);
					$ar_moeArc["number"][$j] = $arTmp[0];
					$ar_divArc["number"][$j] = $arTmp[1];

					//time
					$elmId = "elem_followUp_".$j;
					$arTmp = $this->getBigDiv($elmId, $ar_divArc["time"][$j], $ar_strFunOnFocus["time"][$j], $divCls);
					$ar_moeArc["time"][$j] = $arTmp[0];
					$ar_divArc["time"][$j] = $arTmp[1];

					//visit_type
					$elmId = "elem_followUpVistType_".$j;
					$arTmp = $this->getBigDiv($elmId, $ar_divArc["visit_type"][$j], $ar_strFunOnFocus["visit_type"][$j], $divCls);
					$ar_moeArc["visit_type"][$j] = $arTmp[0];
					$ar_divArc["visit_type"][$j] = $arTmp[1];
				}

			}else{

				$arTmp = $this->getBigDiv($elmId, $ar_divArc[$elmId], $ar_strFunOnFocus[$elmId], $divCls,$chkMNhtm);
				$ar_moeArc[$elmId] = $arTmp[0];
				$ar_divArc[$elmId] = $arTmp[1];
			}
		  }
		  //Loop 2 For Fields --

		}
		//End Count array values

		return array("js"=>$ar_moeArc,"css"=>$ar_flgArcColor,"div"=>$ar_divArc,"curText"=>$ar_str2Show);

		}catch(Exception $e){
			echo "<br/>Error in ChartRecArc::getArcRec(): ".$e->getMessage();
		}

	}

} //End Class
?>