<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
?>
<?php
/*
File: smart_chart_functions.inc.php
Purpose: This file provides functions for smart chart functionality.
Access Type : Include file
*/
?>
<?php
// smart_chart_functions.inc.php

function ssc_checkSympExam($symp){

	global $arrRvs,$arrMedHx;
	global $arrMenuOptions_Pupil, $arrMenuOptions_Ee;

	global $arrMenuOptions_LA_lids, $arrMenuOptions_LA_lesion,
			 $arrMenuOptions_LA_lidpos, $arrMenuOptions_LA_lacsys;

	global $arrMenuOptions_Gonio;

	global  $arrMenuOptions_SLE_conj,$arrMenuOptions_SLE_cor,
			$arrMenuOptions_SLE_ac,$arrMenuOptions_SLE_iris,
			$arrMenuOptions_SLE_lens;

	global  $arrMenuOptions_RV_opt,$arrMenuOptions_RV_mac,
			$arrMenuOptions_RV_vit,$arrMenuOptions_RV_peri,
			$arrMenuOptions_RV_bv,$arrMenuOptions_RV_retinal; 

	global $arrMenuOptions_Cvf, $arrMenuOptions_Dip;

	$arr = array(	//"RVS"=>$arrRvs, //Stopped as we are not implementing it
				 "MedHx"=>$arrMedHx,
				 "Pupil"=>$arrMenuOptions_Pupil, "Ee"=>$arrMenuOptions_Ee,
				 "Lids"=>$arrMenuOptions_LA_lids, "Lesion"=>$arrMenuOptions_LA_lesion,
				 "LidPos"=>$arrMenuOptions_LA_lidpos, "LacSys"=>$arrMenuOptions_LA_lacsys,
				 "Gonio"=>$arrMenuOptions_Gonio,
				 "Conj"=>$arrMenuOptions_SLE_conj, "Cornea"=>$arrMenuOptions_SLE_cor,
				 "AntChamber"=>$arrMenuOptions_SLE_ac, "Iris"=>$arrMenuOptions_SLE_iris,
				 "Lens"=>$arrMenuOptions_SLE_lens,
				 "Optic"=>$arrMenuOptions_RV_opt, 
				 "Vit"=>$arrMenuOptions_RV_vit, "Retinal"=>$arrMenuOptions_RV_retinal, 				 
				 "CVF"=>$arrMenuOptions_Cvf, "Dip"=>$arrMenuOptions_Dip
				);
				
				/*
				"Macula"=>$arrMenuOptions_RV_mac,
				 "Peri"=>$arrMenuOptions_RV_peri,
				 "BV"=>$arrMenuOptions_RV_bv,
				*/
				
	// ---------
	$ret = array();
	foreach($arr as $key => $val){
		if(in_array_nocase($symp, $val)){
			//echo "<br/>".$key;
			$ret[] = $key;
		}
	}

	//echo $symp."<br/>".strlen($symp)."-".strlen("Peri-papillary atrophy");
	/*
	if(trim($symp)=="Peri-papillary atrophy"){
		echo "Symp. ".$symp."<br/>"; //Peri-papillary atrophy
		print_r($arrMenuOptions_RV_opt);
		echo "<br/>";
		print_r($ret);
	}
	*/

	// ---------
	return $ret;
}

function sc_checkAp4MPlans($str){
	if(!empty($str)){
		$arrPl = explode("\n",$str);
		$ln=count($arrPl);
		for($i=0,$j=0;$i<$ln;$i++){
			if(!empty($arrPl[$i])){
				$j++;
			}
		}
		if($j>=2){
			return 1;
		}
	}
	return 0;
}

function sc_getSite($site){
	$tSite = "";
	if(strpos($site,"OU")!== false || (strpos($site,"OD")!== false && strpos($site,"OS")!== false)){
		$tSite ="OU";
	}else if(strpos($site,"OD")!== false){
		$tSite ="OD";
	}else if(strpos($site,"OS")!== false){
		$tSite ="OS";
	}else if(strpos($site,"RUL")!== false){
		$tSite ="RUL";
	}else if(strpos($site,"RLL")!== false){
		$tSite ="RLL";
	}else if(strpos($site,"LLL")!== false){
		$tSite ="LLL";
	}else if(strpos($site,"LUL")!== false){
		$tSite ="LUL";
	}
	
	return trim($tSite);
}

function sc_getAssesProcessed($assessment,$site,$dxcode){
	$ta = trim($assessment);
	if(!empty($ta)){
		//$ta .= (!empty($site)) ? " - ".$site : ""; 
		$ta .= (!empty($dxcode)) ? " (".$dxcode.")" : "";
	}
	return trim($ta);
}

function sc_getFUArr($xmlFU){
	if(!empty($xmlFU)){
		
		$oFu = new Fu();
		list($lenFu, $arrFuVals) = $oFu->fu_getXmlValsArr($xmlFU);
	}
	return $arrFuVals;
}

//
function sc_getConsoleVal($todoid, $site, $icd="",$lids_opts=""){
	if(!empty($lids_opts)){  $site = $lids_opts; }
	$assessment=$plan="";
	$arrFu=array();
	$sql = "SELECT * FROM console_to_do WHERE ".
			"to_do_id='".$todoid."' ";
	$row = sqlQuery($sql);
	if($row != false){
		$assessment = trim($row["assessment"]);
		$plan = trim($row["plan"]);
		$dxcode = ($icd==1) ? trim($row["dxcode_10"]) : trim($row["dxcode"]);
		$xmlFU = $row["xmlFU"];
		
		$dxcode=modifyICDDxCodeWEye($dxcode, $site);		
		
		//$assessment = sc_getAssesProcessed($assessment,$site,$dxcode);
		$arrFu = sc_getFUArr($xmlFU);
	}
	return array("A"=>$assessment,"P"=>$plan,"FU"=>$arrFu,"DX"=>$dxcode);
}

function sc_insertIntoProbList($tmpAsses, $date_of_service, $patient_id, $doctorId, $cosigner_id, $form_id){
	
	$oPtProblemList = new PtProblemList($patient_id);
	
	$tmpAsses_problistid = 0 ;
	$tmp = $oPtProblemList->isProblemExists($tmpAsses,1); //Check if Problem Exists in List
	//Insert into Pt Problem List
	$arrUp["problem_name"] = addslashes($tmpAsses);
	$arrUp["onset_date"] = $date_of_service; // DOS in CC & Hx
	$arrUp["comments"] = "";
	$arrUp["status"] = "Active";
	$arrUp["user_id"] = $_SESSION["authId"];
	$arrUp["pt_id"] = $patient_id;
	$arrUp["signerId"] = $doctorId;
	$arrUp["coSignerId"] = $cosigner_id;
	$arrUp["form_id"] = $form_id;
	
	if($tmpAsses!=""){
		$pl_exp_whr="";
		$pl_exp = explode('(',trim($tmpAsses));
		$pl_exp = explode('-',trim($pl_exp[0]));
		$pl_exp_whr=trim($pl_exp[0]);
		
		$ccda_code_qry=mysql_query("select snowmed_ct from diagnosis_code_tbl where diag_description='".addslashes($pl_exp_whr)."' order by delete_status asc");
		$ccda_code_row=mysql_fetch_array($ccda_code_qry);
		$arrUp["ccda_code"] = $ccda_code_row['snowmed_ct'];	
	}
	
	
	if(empty($tmp)&&empty($tmpAsses_problistid)){ // if Not
		$tmpAsses_problistid = $oPtProblemList->insertRec($arrUp);							
	}elseif(!empty($tmpAsses_problistid)||!empty($tmp)){
		if(empty($tmpAsses_problistid)&&!empty($tmp)){ //pastdata								
			$tmpAsses_problistid = $tmp;	
		}
		$arrUp["id"] = $tmpAsses_problistid;
		$oPtProblemList->updateRec($arrUp);
	}
	return $tmpAsses_problistid;
}

function sc_insertAssesPlan($arrAP,$arrFU){	
	global $elem_formId,$patientid;

	$oChartAP = new ChartAP($patientid,$elem_formId);
	if($oChartAP->isRecordExists()){
		//Up
		$arrAP_db = $oChartAP->getArrAP();
		$arrFU_db = $oChartAP->getArrFU();
	}else{
		//Assess Previous
		$arrAP_db = $oChartAP->getLFAssess();
		//Insert
		$oChartAP->insertNewRec();
		$arrFU_db=array();
	}

	// merge Assess array --
	$arrAP_db = $oChartAP->mergeApArr($arrAP_db,$arrAP);
	// merge Assess array --

	// merge FU array --
	$arrFU_db = $oChartAP->mergeFuArr($arrFU_db,$arrFU);
	// merge FU array --

	//Update
	$oChartAP->updateAp_Fu($arrAP_db,$arrFU_db);
}

function sc_insertExamsValues($arrExm){
	if(count($arrExm)<=0)return;

	global $elem_formId,$patientid;

	foreach($arrExm as $key=>$val){
		$exmNm = $key;
		$arrVal = $val;

		switch($exmNm){
			case "Pupil":
				
				$oPupil = new Pupil($patientid,$elem_formId);
				$oPupil->smartChart($arrVal);
			break;

			case "Ee":
				
				$oEe = new ExternalExam($patientid,$elem_formId);
				$oEe->smartChart($arrVal);
			break;

			case "Lesion":
			case "Lids":
			case "LidPos":
			case "LacSys":
				
				$oLa = new LA($patientid,$elem_formId);
				$oLa->smartChart($arrVal,$exmNm);
			break;

			case "Gonio":
				
				$oGon = new Gonio($patientid,$elem_formId);
				$oGon->smartChart($arrVal);
			break;

			case "Conj":
			case "Cornea":
			case "AntChamber":
			case "Iris":
			case "Lens":
				
				$oSle = new SLE($patientid,$elem_formId);
				$oSle->smartChart($arrVal,$exmNm);
			break;

			case "Optic":
				
				$oOptic = new OpticNerve($patientid,$elem_formId);
				$oOptic->smartChart($arrVal,$exmNm);
			break;

			case "Macula":
			case "Vit":
			case "Peri":
			case "BV":
			case "Retinal":
				
				$oRv = new FundusExam($patientid,$elem_formId);
				$oRv->smartChart($arrVal,$exmNm);
			break;

			case "CVF":
			break;

			case "Dip":
			break;

		}
	}
}


function sc_getSitefromPostData($post){
	$reset_flg=0;
	$od=$os=0;$arlid=array();
	foreach($post as $key => $val){
		$val=trim($val);
		if(!empty($val) && strpos($key, "Od_")!==false){
			$od=1;
		}elseif(!empty($val) && strpos($key, "Od")!==false){
			$od=1;
		}
		if(!empty($val) && strpos($key, "Os_")!==false){
			$os=1;
		}elseif(!empty($val) && strpos($key, "Os")!==false){
			$os=1;
		}
		
		//lids
		if(!empty($val) && ($val=="RLL" || $val=="RUL" || $val=="LLL" || $val=="LUL")){
			$arlid[] = $val;	
		}		
		//--
		
		//incase of reset exam findings pop up: site will be OU
		if($key == "elem_exm_find_pop_reset" && $val=="1"){
			$reset_flg=1;
		}
		//--
	}
	
	$ret="";
	//Ou
	if($od==1 && $os==1){
		$ret = "OU";
	}else{
		if($od==1){
			$ret = "OD";
		}
		
		if($os==1){
			$ret = "OS";
		}
	}

	//set reset eye
	if(empty($ret) && $reset_flg==1){$ret = "OU";}
	
	//lids str
	$arlid=array_unique($arlid);
	$strlid=implode(",", $arlid);
	
	return array($ret, $strlid);
}



?>