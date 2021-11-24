<?php
class PtTest extends TestInfo{
	public $pid, $fid;	
	public function __construct($pid, $fid=0){
		$this->pid = $pid;
		$this->fid = $fid;
		parent::__construct();
	}
	
	function getTests_Other($dos){
		$arr_test_nm = array();	
		$arrExamsDone = array();
		
		$sql = "select c1.test_other_id, c1.test_other_eye, c2.temp_name as test_name   from test_other c1
				left join tests_name c2 ON c1.test_template_id=c2.id
				where c1.patientId = '".$this->pid."' AND (c1.formId='".$this->fid."' OR (c1.formId='0' AND c1.examDate='".$dos."')) 
				AND c1.purged='0' AND c1.del_status='0'
				and c1.test_template_id>0 
				ORDER by c1.test_other_id ";
		$rez=sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++){
			$test_nm = trim($row["test_name"]);
			if(!empty($row["test_other_id"]) && !in_array($test_nm, $arr_test_nm)){			
				$arr_test_nm[]=$test_nm;
				$test_eye = trim($row["test_other_eye"]);
				$arrExamsDone[] = array($test_nm, $test_eye);	
			}
		}	
		
		//Add test_custom_patient
		$sql = "select c1.test_id, c1.test_other_eye, c2.temp_name as test_name from test_custom_patient c1
				left join tests_name c2 ON c1.test_template_id=c2.id
				where c1.patientId = '".$this->pid."' AND (c1.formId='".$this->fid."' OR (c1.formId='0' AND c1.examDate='".$dos."')) 
				AND c1.purged='0' AND c1.del_status='0'
				and c1.test_template_id>0 
				ORDER by c1.test_id ";
		$rez=sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++){
			$test_nm = trim($row["test_name"]);
			if(!empty($row["test_id"]) && !in_array($test_nm, $arr_test_nm)){			
				$arr_test_nm[]=$test_nm;
				$test_eye = trim($row["test_other_eye"]);
				$arrExamsDone[] = array($test_nm, $test_eye);	
			}
		}		
		
		return $arrExamsDone;
		//*/
	}
	
	function getAllTestofDos($dos){
		
		$sql = "";
		$arr = array();
		$ptId = $this->pid;
		$formid = $this->fid;
		
		$arrName = $this->arrName; //
		
		foreach($arrName as $key => $val){
			$tf = $this->getTestFormFields($val,1);
			
			if($val=="Other"){
				$nm = " test_other AS nm, ";
			}else{
				$nm = "";
			}

			//Test
			$sql="SELECT ".$nm." ".$tf["keyId"]." AS id FROM ".$tf["tbl"]." ".
				 "WHERE ".$tf["ptId"]." = '".$ptId."' AND purged='0' AND del_status='0' ".
				 "AND ((".$tf["formId"]." = '".$formid."') OR (".$tf["formId"]." = '0' AND ".$tf["eDt"]." = '".$dos."')) ";
			$rez = sqlStatement($sql);
			for($i=0;$row=sqlFetchArray($rez);$i++){
				
				//TEst
				if(!empty($row["id"]) && (!isset($arr[$val]) || !in_array($row["id"], $arr[$val]))){
					if($nm == ""){
						$arr[$val][] = $row["id"];
					}else{
						$nm = $row["nm"];
						$arr[$val][$nm][] = $row["id"];
					}
				}
			}
		}
		
		return $arr;
		
	}
	
	function getAllTestofPt($flgRemSync=0, $flg_find_uninterpreted=0){

		$sql = "";
		$arr = array();
		
		$pId = $this->pid;
		if(empty($pId)){
			return "";
		}
		
		$oTestinfo = new TestInfo();
		$active = $oTestinfo->get_active_tests();
		$str_FUIN = "";
		if(!empty($flg_find_uninterpreted)){ 
			$str_FUIN = " AND phyName='0' AND performedBy!='0' "; 
			$str_FUIN2 = " AND phyName='0' AND performBy!='0' ";
			$str_FUIN3 = " AND phy='0' AND performed_by!='0' ";
			$str_FUIN4 = " AND signedById='0' AND performedByOD!='0' ";
		} 
		
		
		//TEST
		//	case "VF":
			if(in_array("VF", $active)){
				$sql = "SELECT vf_id AS tId,
						DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
						DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
						performedBy AS prfBy, phyName as phy,purged,formId
						FROM vf WHERE patientId='".$pId."' AND del_status='0' ".$str_FUIN."
						ORDER BY examDate DESC, examTime DESC, vf_id DESC " ;
				$rez = sqlStatement($sql);
				for($a=0;$row=sqlFetchArray($rez);$a++){
					if(!empty($row["tId"])){
					
						$arr[$row["eDt"]]["VF"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
															"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
															"purged"=>$row["purged"]);
					}
				}
			}	
			//case "VF-GL":
			if(in_array("VF-GL", $active)){
				$sql = "SELECT vf_gl_id AS tId,
						DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
						DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
						performedBy AS prfBy, phyName as phy,purged,formId
						FROM vf_gl WHERE patientId='".$pId."' AND del_status='0' ".$str_FUIN."
						ORDER BY examDate DESC, examTime DESC, vf_gl_id DESC " ;
				$rez = sqlStatement($sql);
				for($a=0;$row=sqlFetchArray($rez);$a++){
					if(!empty($row["tId"])){
					
						$arr[$row["eDt"]]["VF-GL"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
															"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
															"purged"=>$row["purged"]);
					}
				}
			}	

		//	case "HRT":
			if(in_array("HRT", $active)){
				$sql = "SELECT nfa_id AS tId,
						DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
						DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
						performBy AS prfBy, phyName as phy,purged,form_id
						FROM nfa WHERE patient_id='".$pId."' AND del_status='0' ".$str_FUIN2." 
						ORDER BY examDate DESC, examTime DESC, nfa_id DESC " ;
				$rez = sqlStatement($sql);
				for($a=0;$row=sqlFetchArray($rez);$a++){
					if(!empty($row["tId"])){
					
						$arr[$row["eDt"]]["HRT"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
															"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
															"purged"=>$row["purged"]);
					}
				}
			}	
		
		//	case "OCT":
			if(in_array("OCT", $active)){
				$sql = "SELECT oct_id AS tId,
						DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
						DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
						performBy AS prfBy, phyName as phy,purged,scanLaserOct,form_id
						FROM oct WHERE patient_id='".$pId."' AND del_status='0' ".$str_FUIN2." 
						ORDER BY examDate DESC, examTime DESC, oct_id DESC " ;
				$rez = sqlStatement($sql);
				for($a=0;$row=sqlFetchArray($rez);$a++){
					if(!empty($row["tId"])){						
						
						if(!empty($row["scanLaserOct"])){
							if($row["scanLaserOct"]=="3"){
								$row["scanLaserOct"]="AS";
							}else if($row["scanLaserOct"]=="1"){
								$row["scanLaserOct"]="ON";	
							}else if($row["scanLaserOct"]=="2"){
								$row["scanLaserOct"]="R";
							}					
						}
						
						$arr[$row["eDt"]]["OCT"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
															"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
															"purged"=>$row["purged"],
															"test_type"=>$row["scanLaserOct"]);
					}
				}
			}	
		
		//	case "OCT-RNFL":
			if(in_array("OCT-RNFL", $active)){
				$sql = "SELECT oct_rnfl_id AS tId,
						DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
						DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
						performBy AS prfBy, phyName as phy,purged,scanLaserOct_rnfl,form_id
						FROM oct_rnfl WHERE patient_id='".$pId."' AND del_status='0' ".$str_FUIN2." 
						ORDER BY examDate DESC, examTime DESC, oct_rnfl_id DESC " ;
				$rez = sqlStatement($sql);
				for($a=0;$row=sqlFetchArray($rez);$a++){
					if(!empty($row["tId"])){
						
						$arr[$row["eDt"]]["OCT-RNFL"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
															"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
															"purged"=>$row["purged"]
															);
					}
				}
			}
		
		//	case "GDX":
			if(in_array("GDX", $active)){
				$sql = "SELECT gdx_id AS tId,
						DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
						DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
						performBy AS prfBy, phyName as phy,purged, form_id
						FROM test_gdx WHERE patient_id='".$pId."' AND del_status='0' ".$str_FUIN2." 
						ORDER BY examDate DESC, examTime DESC, gdx_id DESC " ;
				$rez = sqlStatement($sql);
				for($a=0;$row=sqlFetchArray($rez);$a++){
					if(!empty($row["tId"])){

						$arr[$row["eDt"]]["GDX"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
															"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
															"purged"=>$row["purged"]);
					}
				}
			}	

		//	case "Pachy":
			if(in_array("Pachy", $active)){
				$sql = "SELECT pachy_id AS tId,
						DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
						DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
						performedBy AS prfBy, phyName as phy,purged,formId
						FROM pachy WHERE patientId='".$pId."' AND del_status='0' ".$str_FUIN." 
						ORDER BY examDate DESC, examTime DESC, pachy_id DESC " ;
				$rez = sqlStatement($sql);
				for($a=0;$row=sqlFetchArray($rez);$a++){
					if(!empty($row["tId"])){					
					
						$arr[$row["eDt"]]["Pachy"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
															"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
															"purged"=>$row["purged"]);
					}
				}
			}	

		//	case "IVFA":
			if(in_array("IVFA", $active)){
				$sql = "SELECT vf_id AS tId,
						DATE_FORMAT(exam_date, '%Y-%m-%d') AS eDt,
						DATE_FORMAT(exam_date, '".get_sql_date_format('','y')."') AS dt,
						performed_by AS prfBy, phy as phy,purged, form_id
						FROM ivfa WHERE patient_id='".$pId."' AND del_status='0' ".$str_FUIN3." 
						ORDER BY exam_date DESC, examTime DESC, vf_id DESC " ;
				$rez = sqlStatement($sql);
				for($a=0;$row=sqlFetchArray($rez);$a++){
					if(!empty($row["tId"])){
					
						$arr[$row["eDt"]]["IVFA"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
															"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
															"purged"=>$row["purged"]);
					}
				}
			}	

		//	case "ICG":
			if(in_array("ICG", $active)){
				$sql = "SELECT icg_id AS tId,
						DATE_FORMAT(exam_date, '%Y-%m-%d') AS eDt,
						DATE_FORMAT(exam_date, '".get_sql_date_format('','y')."') AS dt,
						performed_by AS prfBy, phy as phy,purged, form_id
						FROM icg WHERE patient_id='".$pId."' AND del_status='0' ".$str_FUIN3." 
						ORDER BY exam_date DESC, examTime DESC, icg_id DESC " ;
				$rez = sqlStatement($sql);
				for($a=0;$row=sqlFetchArray($rez);$a++){
					if(!empty($row["tId"])){
					
						$arr[$row["eDt"]]["ICG"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
															"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
															"purged"=>$row["purged"]);
					}
				}
			}	
				
		//	case "Fundus":
			if(in_array("Fundus", $active)){
				$sql = "SELECT disc_id AS tId,
						DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
						DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
						performedBy AS prfBy, phyName as phy,purged,fundusDiscPhoto, formId
						FROM disc WHERE patientId='".$pId."' AND del_status='0' ".$str_FUIN." 
						ORDER BY examDate DESC, examTime DESC, disc_id DESC " ;
				$rez = sqlStatement($sql);
				for($a=0;$row=sqlFetchArray($rez);$a++){
					if(!empty($row["tId"])){
						
						if(!empty($row["fundusDiscPhoto"])){
							if($row["fundusDiscPhoto"]=="1"){
								$row["fundusDiscPhoto"]="DP";
							}else if($row["fundusDiscPhoto"]=="2"){
								$row["fundusDiscPhoto"]="MP";
							}else if($row["fundusDiscPhoto"]=="3"){
								$row["fundusDiscPhoto"]="RP";
							}
						}					
						
						$arr[$row["eDt"]]["Fundus"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
															"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
															"purged"=>$row["purged"],
															"test_type"=>$row["fundusDiscPhoto"]);
					}
				}
			}	

		//	case "External/Anterior":
			if(in_array("External/Anterior", $active)){
				$sql = "SELECT disc_id AS tId,
						DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
						DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
						performedBy AS prfBy, phyName as phy,purged,fundusDiscPhoto, formId
						FROM disc_external WHERE patientId='".$pId."' AND del_status='0' ".$str_FUIN." 
						ORDER BY examDate DESC, examTime DESC, disc_id DESC " ;
				$rez = sqlStatement($sql);
				for($a=0;$row=sqlFetchArray($rez);$a++){
					if(!empty($row["tId"])){
						
						if(!empty($row["fundusDiscPhoto"])){
							if($row["fundusDiscPhoto"]=="1"){
								$row["fundusDiscPhoto"]="ES";
							}else if($row["fundusDiscPhoto"]=="2"){
								$row["fundusDiscPhoto"]="ASP";
							}
						}		
						
						$arr[$row["eDt"]]["External/Anterior"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
															"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
															"purged"=>$row["purged"],
															"test_type"=>$row["fundusDiscPhoto"]);
					}
				}
			}	

		//	case "Topography":
			if(in_array("Topography", $active)){
				$sql = "SELECT topo_id AS tId,
						DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
						DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
						performedBy AS prfBy, phyName as phy,purged, formId
						FROM topography WHERE patientId='".$pId."' AND del_status='0' ".$str_FUIN." 
						ORDER BY examDate DESC, examTime DESC, topo_id DESC " ;
				$rez = sqlStatement($sql);
				for($a=0;$row=sqlFetchArray($rez);$a++){
					if(!empty($row["tId"])){
					
						$arr[$row["eDt"]]["Topography"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
															"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
															"purged"=>$row["purged"]);
					}
				}
			}	

		/*///	case "Ophthalmoscopy":
				$sql = "SELECT ophtha_id AS tId, DATE_FORMAT(exam_date, '%m-%d-%Y') AS dt,
						performedBy AS prfBy, phyName as phy
						FROM ophtha WHERE patient_id='".$pId."'
						ORDER BY exam_date DESC, examTime DESC, ophtha_id DESC " ;
				$rez = sqlStatement($sql);
				for($i=0;$row=sqlFetchArray($rez);$i++){
					if(!empty($row["tId"]) && (!isset($arr["VdF"]) || !in_array($row["tId"], $arr["VdF"]))){
						$arr["VdF"][] = array("id"=>$row["tId"],"dt"=>$row["dt"]);
					}
				}
		*/

		//	case "Other":
			if(in_array("Other", $active)){
				$sql = "SELECT test_other_id AS tId,
						DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
						DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
						performedBy AS prfBy, phyName as phy, test_other AS subcat,purged, formId
						FROM test_other WHERE patientId='".$pId."' AND del_status='0' AND test_template_id=0 ".$str_FUIN." 
						ORDER BY test_other ASC, examDate DESC, examTime DESC, test_other_id DESC " ;
				$rez = sqlStatement($sql);
				for($a=0;$row=sqlFetchArray($rez);$a++){

					$test_other = ucfirst(strtolower($row["subcat"]));

					if(!empty($row["tId"])){
						if(!isset($arr[$row["eDt"]]["Other"][$test_other])){
							$arr[$row["eDt"]]["Other"][$test_other] = array();
						}

						$arr[$row["eDt"]]["Other"][$test_other][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																			"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																			"purged"=>$row["purged"]);
					}
				}
			}	
				
		//	case "TemplateTests":
			
				$sql = "SELECT test_other_id AS tId,
						DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
						DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
						performedBy AS prfBy, phyName as phy, tn.temp_name AS subcat, tn.test_name AS subcat_tn,  purged, formId
						FROM test_other JOIN tests_name tn ON (tn.id=test_other.test_template_id) 
						WHERE patientId='".$pId."' AND test_other.del_status='0' AND test_template_id>0 ".$str_FUIN." 
						ORDER BY test_other ASC, examDate DESC, examTime DESC, test_other_id DESC";
				$rez = sqlStatement($sql);
				for($a=0;$row=sqlFetchArray($rez);$a++){

					$test_template = ucfirst(strtolower($row["subcat"]));
					if(in_array($row["subcat_tn"], $active)){
					if(!empty($row["tId"])){
						if(!isset($arr[$row["eDt"]]["TemplateTests"][$test_template])){
							$arr[$row["eDt"]]["TemplateTests"][$test_template] = array();
						}

						$arr[$row["eDt"]]["TemplateTests"][$test_template][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																			"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																			"purged"=>$row["purged"]);
					}
					}
				}
			
			
		// 	case "test_custom_patient"
			
				$sql = "SELECT test_id AS tId,
						DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
						DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
						performedBy AS prfBy, phyName as phy, tn.temp_name AS subcat, tn.test_name AS subcat_tn, purged, formId
						FROM test_custom_patient JOIN tests_name tn ON (tn.id=test_custom_patient.test_template_id) 
						WHERE patientId='".$pId."' AND test_custom_patient.del_status='0' AND test_template_id>0 ".$str_FUIN." 
						ORDER BY test_other ASC, examDate DESC, examTime DESC, test_id DESC";
				$rez = sqlStatement($sql);
				for($a=0;$row=sqlFetchArray($rez);$a++){

					$test_template = ucfirst(strtolower($row["subcat"]));
					if(in_array($row["subcat_tn"], $active)){
					if(!empty($row["tId"])){
						if(!isset($arr[$row["eDt"]]["CustomTests"][$test_template])){
							$arr[$row["eDt"]]["CustomTests"][$test_template] = array();
						}

						$arr[$row["eDt"]]["CustomTests"][$test_template][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																			"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																			"purged"=>$row["purged"]);
					}
					}
				}
				
				
		//	case "Laboratories":
			if(in_array("Laboratories", $active)){
				$sql = "SELECT test_labs_id AS tId,
						DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
						DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
						performedBy AS prfBy, phyName as phy, test_labs AS subcat,purged, formId
						FROM test_labs WHERE patientId='".$pId."' AND del_status='0' ".$str_FUIN." 
						ORDER BY test_labs ASC, examDate DESC, examTime DESC, test_labs_id DESC " ;
				$rez = sqlStatement($sql);
				for($a=0;$row=sqlFetchArray($rez);$a++){

					if(!empty($row["tId"])){
					
						$arr[$row["eDt"]]["Labs"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
															"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
															"purged"=>$row["purged"]);
					}
				}
			}	
		
		//	case "A/Scan":
			if(in_array("A/Scan", $active)){
				$sql = "SELECT surgical_id AS tId,
						DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
						DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
						performedByOD AS prfBy, signedById as phy,purged, form_id
						FROM surgical_tbl WHERE patient_id ='".$pId."' AND del_status='0' ".$str_FUIN4."
						ORDER BY examDate DESC, examTime DESC, surgical_id DESC " ;
				$rez = sqlStatement($sql);
				for($a=0;$row=sqlFetchArray($rez);$a++){

					if(!empty($row["tId"])){
					
						$arr[$row["eDt"]]["A/Scan"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
															"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
															"purged"=>$row["purged"]);
					}
				}
			}	
		
		//	case "IOL Master":
			if(in_array("IOL Master", $active)){
				$sql = "SELECT iol_master_id AS tId,
						DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
						DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
						performedByOD AS prfBy, signedById as phy,purged, form_id
						FROM iol_master_tbl WHERE patient_id ='".$pId."' AND del_status='0' ".$str_FUIN4." 
						ORDER BY examDate DESC, examTime DESC, iol_master_id DESC " ;
				$rez = sqlStatement($sql);
				for($a=0;$row=sqlFetchArray($rez);$a++){

					if(!empty($row["tId"])){
					
						$arr[$row["eDt"]]["IOL Master"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
															"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
															"purged"=>$row["purged"]);
					}
				}
			}	

		//	case "BScan":
			if(in_array("B-Scan", $active)){
				$sql = "SELECT test_bscan_id AS tId,
						DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
						DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
						performedBy AS prfBy, phyName as phy,purged, formId
						FROM test_bscan WHERE patientId='".$pId."' AND del_status='0' ".$str_FUIN." 
						ORDER BY examDate DESC, examTime DESC, test_bscan_id DESC " ;
				$rez = sqlStatement($sql);
				for($a=0;$row=sqlFetchArray($rez);$a++){
					if(!empty($row["tId"])){
					
						$arr[$row["eDt"]]["B-Scan"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
															 "prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
															 "purged"=>$row["purged"]);
					}
				}
			}	

		//	case "CellCount":
			if(in_array("Cell Count", $active)){
				$sql = "SELECT test_cellcnt_id AS tId,
						DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
						DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
						performedBy AS prfBy, phyName as phy,purged, formId
						FROM test_cellcnt WHERE patientId='".$pId."' AND del_status='0' ".$str_FUIN." 
						ORDER BY examDate DESC, examTime DESC, test_cellcnt_id DESC " ;
				$rez = sqlStatement($sql);
				for($a=0;$row=sqlFetchArray($rez);$a++){
					if(!empty($row["tId"])){
					
						$arr[$row["eDt"]]["Cell Count"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
															 "prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
															 "purged"=>$row["purged"]);
					}
				}
			}	
		
		krsort($arr); //Sort Desc
		return $arr;
	}
	
	function getSignature($wh){
		$folder_name = $wh;
		$arr = array();
		$num = 0;
		$patient_id=$this->pid;	
		$phyId = $_SESSION["authId"];
		
		$oSaveFile = new SaveFile($patient_id);
		$tmpDirPth_up = $oSaveFile->getUploadDirPath();
		$tmpDirPth_sign = $oSaveFile->ptDir("test_sign/".$folder_name);
		$tmpDirPth_pt = "/PatientId_".$patient_id;
		$form_sign_path = $tmpDirPth_pt.$tmpDirPth_sign;
		$tmp_sign_path=realpath($tmpDirPth_up.$form_sign_path);
		
		if(!empty($phyId)){
			$sql = "SELECT sign, sign_path FROM users  WHERE id = '".$phyId."' ";
			$row = sqlQuery($sql);
			if($row != false){
				$strpixls = trim($row["sign"]);
				$str_sign_path = trim($row["sign_path"]);
				
				$chk1=$chk2=0;
				if((!empty($strpixls) && $strpixls!="0-0-0:;")){  $chk1=1; }
				if((!empty($str_sign_path) && strpos($str_sign_path,"UserId") !== false && file_exists($tmpDirPth_up.$str_sign_path) )){  $chk2=1; }
				
				if($chk1==1||$chk2==1){
					$arr["str"] = "";
					//-------------						
					//Make Image 			
					$img_nm = "/".$folder_name."_sig".$num."_".time().".jpg";			
					$tmp_sign_path1=$tmp_sign_path.$img_nm;						
					//global $gdFilename;
					if($chk2==1){
						if(copy($tmpDirPth_up.$str_sign_path, $tmp_sign_path1)){ }else{ $form_sign_path=$img_nm=""; }
					}else{						
						//drawOnImage_new($strpixls,"",$tmp_sign_path1);
					}
					//-------------
					
					$tmpSignPth_w = "";
					$tmpSignPth_w = $oSaveFile->getFilePath($tmpDirPth_up.$form_sign_path.$img_nm, "w");
					
					$arr["str"] .= "<img src=\"".$tmpSignPth_w."\" alt=\"sign\" width=\"225\" height=\"45\" >";					
					$arr["strpixls"]=$strpixls;
					$arr["strsignpath"]=$form_sign_path.$img_nm;	
					
					if($hidd_val!=""){
						$signatureSaveDateTime = date("Y-m-d H:i:s");
					}
				}
			}
		}
		return $arr;
	}
	
	function showPrvSynthesis_handler(){
		$tst=$_GET["tst"];
		$tid=$_GET["tid"];
		$op=$_GET["op"];
		$patient_id=$this->pid;	
		if(empty($patient_id)){ exit(""); }
		
		if($op=="Interpret"){		
			
			$phyId = $_SESSION["authId"];
			$hidd_val = "";			
			$flg_ret="";
			
			if($tst=="VF-GL"){
				$zflg_file_included=1;//
				$folder_name = "test_vf_gl";
				$arr = $this->getSignature($folder_name);
				//include_once(dirname(__FILE__)."/test_user_signature_ajax.php");
				$sigpath = $arr["strsignpath"];
				
				if(!empty($sigpath)){
					$sql="UPDATE vf_gl SET phyName='".$phyId."', sign_path='".$sigpath."', sign_path_date_time='".wv_dt('now')."' WHERE vf_gl_id='".$tid."' ";
					$row=sqlQuery($sql);
					$flg_ret="1";
				}
				
			}else if($tst=="OCT-RNFL"){
				$zflg_file_included=1;//
				$folder_name = "test_oct_rnfl";
				$arr = $this->getSignature($folder_name);
				//include_once(dirname(__FILE__)."/test_user_signature_ajax.php");
				$sigpath = $arr["strsignpath"];
				
				if(!empty($sigpath)){
					$sql="UPDATE oct_rnfl SET phyName='".$phyId."', sign_path='".$sigpath."', sign_path_date_time='".wv_dt('now')."' WHERE oct_rnfl_id='".$tid."' ";
					$row=sqlQuery($sql);
					$flg_ret="1";
				}
			}
			
			echo $flg_ret;				
		
		}else{			
			
			if($tst=="VF-GL"){
				$dvid="dv_syn_vf_gl";
				$sql = "SELECT descOd, descOs,synthesis_od,synthesis_os, examDate, vf_gl_id FROM vf_gl where patientId='".$patient_id."' AND vf_gl_id!='".$tid."' ORDER BY examDate DESC, vf_gl_id DESC ";
			}else if($tst=="OCT-RNFL"){
				$dvid="dv_syn_oct_rnfl";
				$sql = "SELECT descOd, descOs,synthesis_od,synthesis_os,examDate,oct_rnfl_id FROM oct_rnfl WHERE patient_id = '".$patient_id."' AND oct_rnfl_id!='".$tid."' ORDER BY examDate DESC, oct_rnfl_id DESC  ";
			}
			$html="";
			$rez = sqlStatement($sql);			
			for($i=0;$row=sqlFetchArray($rez);$i++){
				$descOd = trim($row["synthesis_od"]); 
				$descOs = trim($row["synthesis_os"]);
				$dateofExam=wv_formatDate($row["examDate"]);				
				if(!empty($descOd) || !empty($descOs)){	

					//make bold labels;
					$arC=array("MD:","VFI:","Stage:","Reliab:","Interpret:","Comments:","Artifact:","Hemianopsis:", "Quadranopsia:","Congruity:", "SS:", "Qual:", "Disc area:", "Avg RNFL:", "Disc edema:", "Overall:", "Superior:", "Inferior:", "Temporal:", "Symmetric:" );
					$arR=array("<b>MD:</b>","<b>VFI:</b>","<b>Stage:</b>","<b>Reliab:</b>","<b>Interpret:</b>","<b>Comments:</b>","<b>Artifact:</b>","<b>Hemianopsis:</b>", "<b>Quadranopsia:</b>","<b>Congruity:</b>", "<b>SS:</b>", "<b>Qual:</b>", "<b>Disc area:</b>", "<b>Avg RNFL:</b>", "<b>Disc edema:</b>", "<b>Overall:</b>", "<b>Superior:</b>", "<b>Inferior:</b>", "<b>Temporal:</b>", "<b>Symmetric:</b>" );
					$descOd=str_replace($arC,$arR,$descOd);
					$descOs=str_replace($arC,$arR,$descOs);

					$html.="
					<tr>
						<td>".$dateofExam."</td>
						<td>".nl2br($descOd)."</td>
						<td>".nl2br($descOs)."</td>
					</tr>
					";				
				}
			}
			
			if(!empty($html)){
				//<label>".$tst."</label><label class=\"od\">OD</label><label class=\"os\">OS</label><label class=\"close ui-icon ui-icon-closethick\" title=\"close\" onclick=\"showPrvSynthesis('x')\"></label>
				$html="<div id=\"modal_prev_synth\" class=\"modal fade\" role=\"dialog\">".
				"<div class=\"modal-dialog\">".
				"<!-- Modal content-->".
				    "<div class=\"modal-content\">".
				      "<div class=\"modal-header\">".
					"<button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>".
					"<h4 class=\"modal-title\">".$tst."</h4>".
				      "</div>".
				      "<div class=\"modal-body\">".
					"<div id=\"".$dvid."\" class=\"dvprvsyn\"><div><table class=\"table table-bordered table-striped\" ><tr><td></td><td class=\"od\">OD</td><td class=\"os\">OS</td></tr>".$html."</table></div></div>".
				      "</div>".
				      /*"<div class="modal-footer\">".
					"<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>".
				      "</div>".*/
				    "</div>".
				
				"</div>".
				"</div>";
			}
			echo $html;
		
		}
	}
	
	//function to attach test with chart note
	function attachFormId2Test($test, $dos, $fId, $pid,$str=""){
		$arrFF = $this->getTestFormFields($test,1);
		$arrRet=array();
		if(!empty($str)){
			$str = ",".$str;
		}

		//Check if Test of Same Date Exists
		$sql = "SELECT ".$arrFF["keyId"]." AS tId ".$str." FROM ".$arrFF["tbl"]." WHERE ".$arrFF["formId"]."='0' ".
			   "AND ".$arrFF["ptId"]."='".$pid."' AND ".$arrFF["eDt"]."='".$dos."' ".
			   "ORDER BY ".$arrFF["eDt"]." DESC, ".$arrFF["keyId"]." DESC ".
			   "LIMIT 0,1 ";
		
		$row = sqlQuery($sql);
		if($row != false){
			$tId = $row["tId"];
			if($test=="A/Scan" || $test=="IOL Master"){
				$performedBy =  0 ;
				if(!empty($row["performedByOD"])){
					$performedBy =  $row["performedByOD"] ;
				}elseif(!empty($row["performedByOS"])){
					$performedBy =  $row["performedByOS"] ;
				}elseif(!empty($row["performedByPhyOD"])){
					$performedBy =  $row["performedByPhyOD"] ;
				}elseif(!empty($row["performedIolOS"])){
					$performedBy =  $row["performedIolOS"] ;
				}
				$phyName = 0;
				if(!empty($row["signedById"])){
					$phyName = $row["signedById"];
				}elseif(!empty($row["signedByOSId"])){
					$phyName = $row["signedByOSId"];
				}			
				
				$ptInformed = 0;
				$ptInformedNv = 0;
				
			}else{
				$performedBy = !empty($row["performedBy"]) ? $row["performedBy"] : 0 ;
				$phyName = !empty($row["phyName"]) ? $row["phyName"] : 0 ;
				$ptInformed = !empty($row["ptInformed"]) ? $row["ptInformed"] : 0 ;
				$ptInformedNv = !empty($row["ptInformedNv"]) ? $row["ptInformedNv"] : 0 ;
			}

			//Set form Id to New Chart
			$sql = "UPDATE ".$arrFF["tbl"]." SET ".$arrFF["formId"]."='".$fId."' ".
					"WHERE ".$arrFF["keyId"]." = ".$tId." AND ".$arrFF["ptId"]."='".$pid."' ";
			$row = sqlQuery($sql);
			
			//remove from remote_sync
			//remove_remote_sync($pid, $tId, $test);			

			//Arr Ret
			$arrRet = array("performedBy"=>$performedBy,"phyName"=>$phyName,"ptInformed"=>$ptInformed,"ptInformedNv"=>$ptInformedNv);
		}
		return $arrRet;
	}
	
	function call_attachFormId2Test($dos_ymd){
		$form_id = $this->fid;
		$patient_id = $this->pid;
		if(!empty($form_id) && !empty($patient_id)){
		//start ----------		

		$arrSub=array("Testing"=>array( "sl_vf"=>array("VF"),
										"sl_vf_gl"=>array("VF-GL"),
											"sl_hrt"=>array("HRT"),
											"sl_oct"=>array("OCT"),
											"sl_oct_rnfl"=>array("OCT-RNFL"),
											"sl_gdx"=>array("GDX"),
											"sl_pachy"=>array("Pachy"),
											"sl_ivfa"=>array("IVFA"),
											"sl_icg"=>array("ICG"),
											"sl_fundus"=>array("Fundus"),
											"sl_ex_ant"=>array("External/Anterior"),
											"sl_topo"=>array("Topography"),
											"sl_cellc"=>array("Cell Count"), 
											"sl_opth"=>array("Opthalmoscopy"),
											"sl_ascan"=>array("A/Scan"),
											"sl_iol_master"=>array("IOL Master"),
											"sl_bscan"=>array("B-Scan"))
					);
		
		if(!empty($form_id)){	
		
		$test_info=array();
		$sql = "SELECT ".
				
				 //c15-vf-------
				 "c15.performedBy AS performedBy_vf , c15.phyName AS phyName_vf, ".
				 "c15.ptInformed AS ptInformed_vf, c15.ptInformedNv AS ptInformedNv_vf, ".
				 //c15-vf-------
				 
				 //c29-vf-------
				 "c29.performedBy AS performedBy_vf_gl , c29.phyName AS phyName_vf_gl, ".
				 "c29.ptInformed AS ptInformed_vf_gl, c29.ptInformedNv AS ptInformedNv_vf_gl, ".
				 //c29-vf-------				 

				 //c16-nfa-------
				 "c16.performBy AS performedBy_nfa , c16.phyName AS phyName_nfa, ".
				 "c16.ptInformed AS ptInformed_nfa, c16.ptInformedNv AS ptInformedNv_nfa, ".
				 //c16-nfa-------

				 //c17-pachy-------
				 "c17.performedBy AS performedBy_pachy , c17.phyName AS phyName_pachy, ".
				 "c17.ptInformed AS ptInformed_pachy, c17.ptInformedNv AS ptInformedNv_pachy, ".
				 //c17-pachy-------

				 //c18-ivfa-------
				 "c18.performed_by AS performedBy_ivfa , c18.phy AS phyName_ivfa, ".
				 "c18.PatientInformed AS ptInformed_ivfa, c18.ptInformedNv AS ptInformedNv_ivfa, ".
				 //c18-ivfa-------

				 //c19-disc-------
				 "c19.performedBy AS performedBy_disc , c19.phyName AS phyName_disc, ".
				 "c19.ptInformed AS ptInformed_disc, c19.ptInformedNv AS ptInformedNv_disc, ".
				 //c19-disc-------

				 //c20-topography-------
				 "c20.performedBy AS performedBy_topo , c20.phyName AS phyName_topo,".
				 "c20.ptInformed AS ptInformed_topo, c20.ptInformedNv AS ptInformedNv_topo, ".
				 //c20-topography-------

				 //c21-disc_external-------
				 "c21.performedBy AS performedBy_external , c21.phyName AS phyName_external, ".
				 "c21.ptInformed AS ptInformed_external, c21.ptInformedNv AS ptInformedNv_external, ".
				 //c21-disc_external-------

				 //c22-oct-------
				 "c22.performBy AS performedBy_oct , c22.phyName AS phyName_oct, ".
				 "c22.ptInformed AS ptInformed_oct, c22.ptInformedNv AS ptInformedNv_oct, ".
				 //c22-oct-------
				 
				 //c30-oct-rnfl-------
				 "c30.performBy AS performedBy_oct_rnfl , c30.phyName AS phyName_oct_rnfl, ".
				 "c30.ptInformed AS ptInformed_oct_rnfl, c30.ptInformedNv AS ptInformedNv_oct_rnfl, ".
				 //c30-oct-rnfl-------

				 //c23-bscan-------
				 "c23.performedBy AS performedBy_bscan, c23.phyName AS phyName_bscan, ".
				 "c23.ptInformed AS ptInformed_bscan, c23.ptInformedNv AS ptInformedNv_bscan, ".
				 //c23-bscan-------

				 //c24-cellcnt-------
				 "c24.performedBy AS performedBy_cellcnt, c24.phyName AS phyName_cellcnt, ".
				 "c24.ptInformed AS ptInformed_cellcnt, c24.ptInformedNv AS ptInformedNv_cellcnt, ".
				 //c24-cellcnt-------

				 //c18-ivfa-------
				 "c25.performed_by AS performedBy_icg , c25.phy AS phyName_icg, ".
				 "c25.PatientInformed AS ptInformed_icg, c25.ptInformedNv AS ptInformedNv_icg, ".
				 //c18-ivfa-------

				 //c26-A/Scan-------
				 "c26.performedByOD AS performedByOD_ascan, c26.performedByPhyOD AS performedByPhyOD_ascan , ".
				 "c26.performedByOS AS performedByOS_ascan, c26.performedIolOS AS performedIolOS_ascan , ".
				 "c26.signedById AS signedById_ascan , c26.signedByOSId AS signedByOSId_ascan , ".
				 //c26-A/Scan-------
				 
				 //c28-IOLMaster-------
				 "c28.performedByOD AS performedByOD_iol_master, c28.performedByPhyOD AS performedByPhyOD_iol_master , ".
				 "c28.performedByOS AS performedByOS_iol_master, c28.performedIolOS AS performedIolOS_iol_master ,  ".
				 "c28.signedById AS signedById_iol_master, c28.signedByOSId AS signedByOSId_iol_master, ".
				 //c28-IOLMaster-------
				 
				 //c27-gdx-------
				 "c27.performBy AS performedBy_gdx , c27.phyName AS phyName_gdx, ".
				 "c27.ptInformed AS ptInformed_gdx, c27.ptInformedNv AS ptInformedNv_gdx ".
				 //c27-gdx-------

				"FROM chart_master_table c1 ".
				"LEFT JOIN vf c15 ON c15.formId = c1.id AND c15.purged='0' AND c15.del_status='0'  ".
				"LEFT JOIN vf_gl c29 ON c29.formId = c1.id AND c29.purged='0' AND c29.del_status='0'  ".
				"LEFT JOIN nfa c16 ON c16.form_id = c1.id AND c16.purged='0' AND c16.del_status='0' ".
				"LEFT JOIN pachy c17 ON c17.formId = c1.id AND c17.purged='0' AND c17.del_status='0' ".
				"LEFT JOIN ivfa c18 ON c18.form_id = c1.id AND c18.purged='0' AND c18.del_status='0' ".
				"LEFT JOIN disc c19 ON c19.formId = c1.id AND c19.purged='0' AND c19.del_status='0' ".
				"LEFT JOIN topography c20 ON c20.formId = c1.id AND c20.purged='0' AND c20.del_status='0' ".
				"LEFT JOIN disc_external c21 ON c21.formId = c1.id AND c21.purged='0' AND c21.del_status='0' ".
				"LEFT JOIN oct c22 ON c22.form_id = c1.id AND c22.purged='0' AND c22.del_status='0' ".
				"LEFT JOIN oct_rnfl c30 ON c30.form_id = c1.id AND c30.purged='0' AND c30.del_status='0' ".
				"LEFT JOIN test_bscan c23 ON c23.formId = c1.id AND c23.purged='0' AND c23.del_status='0' ".
				"LEFT JOIN test_cellcnt c24 ON c24.formId = c1.id AND c24.purged='0' AND c24.del_status='0' ".
				"LEFT JOIN icg c25 ON c25.form_id = c1.id AND c25.purged='0' AND c25.del_status='0' ".
				"LEFT JOIN surgical_tbl c26 ON c26.form_id = c1.id AND c26.purged='0' AND c26.del_status='0' ".
				"LEFT JOIN iol_master_tbl c28 ON c28.form_id = c1.id AND c28.purged='0' AND c28.del_status='0' ".
				"LEFT JOIN test_gdx c27 ON c27.form_id = c1.id AND c27.purged='0' AND c27.del_status='0' ".				
				"WHERE c1.id='".$form_id."' AND c1.patient_id='".$patient_id."' ";
			
		$row=sqlQuery($sql);
		if($row != false){

			$arr= array("sl_vf"=>"vf", "sl_vf_gl"=>"vf_gl", "sl_hrt"=>"nfa","sl_pachy"=>"pachy","sl_ivfa"=>"ivfa","sl_icg"=>"icg",
								"sl_fundus"=>"disc","sl_topo"=>"topo","sl_ex_ant"=>"external",
								"sl_oct"=>"oct", "sl_oct_rnfl"=>"oct_rnfl", "sl_gdx"=>"gdx","sl_bscan"=>"bscan",
								"sl_cellc"=>"cellcnt","sl_ascan"=>"ascan","sl_iol_master"=>"iol_master");
					
			foreach($arr as $key => $val){
				$tnm = $val;
				$tid = $key;
				
				$flg_aso=1;
				if($tnm=="iol_master" || $tnm=="ascan"){				
					
					//Flag in Slider
					if(!empty($row["performedByOD_".$tnm]) || !empty($row["performedByOS_".$tnm]) || !empty($row["performedByPhyOD_".$tnm]) || !empty($row["performedIolOS_".$tnm])){
						$flg_aso=0;
					}			

				}			
				else{
					//Flag in Slider
					if(!empty($row["performedBy_".$tnm])){					
						$flg_aso=0;
					}
				}	
				
				//echo "<br/>".$tnm." - ".$flg_aso;
				
				if(!empty($flg_aso)){				
				
					//TEST --
					$strTestInfo = "performedBy AS performedBy , phyName AS phyName, ".
								   "ptInformed AS ptInformed, ptInformedNv AS ptInformedNv ";
					
					if($tnm=="ivfa"||$tnm=="icg"){
					$strTestInfo = "performed_by AS performedBy, phy AS phyName, ".
									"PatientInformed AS ptInformed, ptInformedNv AS ptInformedNv ";
					}

					if($tnm=="nfa"||$tnm=="oct"||$tnm=="oct_rnfl"||$tnm=="gdx"){
					$strTestInfo = "performBy AS performedBy , phyName AS phyName, ".
							       "ptInformed AS ptInformed, ptInformedNv AS ptInformedNv ";	
					}
					
					if($tnm=="iol_master" || $tnm=="ascan"){
						$strTestInfo = "performedByOD, performedByOS, performedByPhyOD, performedIolOS, signedById, signedByOSId  ";
					}
					
					$arrTestInfo = $this->attachFormId2Test($arrSub["Testing"][$tid][0],$dos_ymd,$form_id,$patient_id,$strTestInfo);
					
					//TEST --
				}
				
			}
		}
		}//
		//end ----------	
			
		}
	}
	
	function get_sb_codes(){
	
		if(!isset($GLOBALS["STOP_AP_DX_TEST_SB"]) || empty($GLOBALS["STOP_AP_DX_TEST_SB"])){
		$oChartAP =  new ChartAP($this->pid);
		$ar_dx_codes = $oChartAP->getLastVisitDxCodes(1);
		}else{ $ar_dx_codes = array(); }
		
		// Check if Pt has Insurance 'Medicare' --
		$opt = new Patient($this->pid);
		$flgPtInsMedicare = $opt->isPtInsMedicare();
		
		$ocpt = new CPT();
		$ocpt->set_flg_pt_ins($flgPtInsMedicare);
		$ar_cpt_codes = $ocpt->getTestCptCodes();
		
		$ar = array("dx"=>$ar_dx_codes, "cpt"=>$ar_cpt_codes);
		
		echo json_encode($ar);
	}
	
	function pt_test_uninterpreted(){
		$ret=0;
		$ar = $this->getAllTestofPt(0, 1);
		if(count($ar)>0){$ret=1;}
		return $ret;
	}
}
?>