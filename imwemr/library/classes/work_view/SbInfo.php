<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: Admn.php
Coded in PHP7
Purpose: This class file provides functions to get Admin side values
Access Type : Include file
*/
?>
<?php
//SbInfo
class SbInfo{
	public $pid, $fid;
	public function __construct($pid, $fid){
		$this->pid = $pid;
		$this->fid = $fid;
	}

	function isPqriCodeBilled($code){

		if(isset($GLOBALS["STOP_PQRI1YRFILTER"])&&$GLOBALS["STOP_PQRI1YRFILTER"]=="1"){return false;}

		$ret = false;
		$sql = "SELECT * ".
			 "FROM patient_charge_list ".
			 "INNER JOIN patient_charge_list_details ON patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id ".
			 "INNER JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode ".
			 "WHERE patient_charge_list_details.del_status='0' and patient_charge_list.patient_id='".$this->pid."' ".
			 "AND patient_charge_list.submitted = 'true' ".
			 "AND YEAR(postedDate) = '".date("Y")."' ".
			 "AND cpt_fee_tbl.cpt_prac_code='".$code."' ";
		$row = sqlQuery($sql);
		if($row != false){
			$ret=true;
		}
		return $ret;
	}

	function isPqriCodeBilled_v2($code){

		if(isset($GLOBALS["STOP_PQRI1YRFILTER"])&&$GLOBALS["STOP_PQRI1YRFILTER"]=="1"){return array("isBilled"=>false);}

		$ret = array();
		$ret["isBilled"] = false;
		$sql = "SELECT patient_charge_list.postedDate ".
			 "FROM patient_charge_list ".
			 "INNER JOIN patient_charge_list_details ON patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id ".
			 "INNER JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode ".
			 "WHERE patient_charge_list_details.del_status='0' and patient_charge_list.patient_id='".$this->pid."' ".
			 "AND patient_charge_list.submitted = 'true' ".
			 "AND cpt_fee_tbl.cpt_prac_code='".$code."' ".
			 "AND YEAR(patient_charge_list.postedDate) = '".date("Y")."' ";
		$row = sqlQuery($sql);
		if($row != false){

			$ret["billDate"] = FormatDate_show($row["postedDate"]);
			$ret["isBilled"] = true;

		}
		return $ret;
	}

	function find_dx(){
		//XML HEADER
		header('Content-Type: text/xml');
		$str .= "<?xml version='1.0' encoding='ISO-8859-1'?>";
		$str .= "<dxcodeinfo>";

		$pid = $this->pid; //$_SESSION["patient"];
		$chkDesc = urldecode($_POST["elem_desc"]);
		$chkDesc = sqlEscStr(trim($chkDesc));
		$ICD10 = $_POST["ICD10"];
		$dxid = $_POST["dxid"];
		$dxCode = "";
		$otherdxCodes_multi = "";

		if(!empty($ICD10)){
			$vARR2 = explode(' [ICD-10: ', $chkDesc);
			if(count($vARR2)>1){
				$chkDesc = $vARR2[0];
				//if(!empty($vARR2[1])){

				//}
			}

			//if multiple
			if(strpos($chkDesc,",")!==false){
				$tmp_arr = explode(",",$chkDesc);
				$len_tmp_arr = count($tmp_arr);
				$chkDesc=trim($tmp_arr[$len_tmp_arr-1]);
				foreach($tmp_arr as $key1 => $var1){
					if(trim($var1)!=trim($chkDesc)){
						if(!empty($otherdxCodes_multi)) { $otherdxCodes_multi.=", "; }
						$otherdxCodes_multi.=trim($var1);
					}
				}
			}

			//icd10 code for litrality
			$chkDesc_2 = substr($chkDesc, 0, -1);
			$chkDesc_2.="-";
			$litrality_chk=substr($chkDesc, -1);

			$chkDesc_3 = substr($chkDesc, 0, -2);
			$chkDesc_3.="--";

			$chkDesc_4 = substr($chkDesc, 0, -3);
			$chkDesc_4.="-x-";

			$dxphrse="";
			if(!empty($dxid)){
				$dxphrse=" AND id='".$dxid."' ";
			}

			$sql = "SELECT id as mainId,icd10_desc AS diag_description, icd10 AS dx_code,icd10, laterality, staging, severity, (select count(id) from icd10_data where parent_id = mainId and deleted=0) as parents FROM icd10_data WHERE ((icd9 LIKE '".$chkDesc."%') OR (icd10 LIKE '".$chkDesc."%') OR (icd10 LIKE '".$chkDesc_2."%') OR (icd10 LIKE '".$chkDesc_3."%') OR (LOWER(icd10) LIKE '".strtolower($chkDesc_4)."%') OR (icd9_desc LIKE '".$chkDesc."%') OR (icd10_desc LIKE '".$chkDesc."%')) ".$dxphrse." AND deleted=0 ORDER BY parents DESC"; //AND parent_id=0

			$row =false;
			if(!empty($dxphrse)){ //check with dxid first
				$row = sqlQuery($sql);
			}

			if($row == false){
				if(!empty($dxphrse)){$sql = str_replace($dxphrse, "", $sql);} //remove dxid and check again
				$row = sqlQuery($sql);
			}

		}else{

			$sql = "SELECT dx_code,pqriCode,diag_description FROM diagnosis_code_tbl ".
				 "WHERE dx_code='".$chkDesc."' || d_prac_code='".$chkDesc."' || ".
				 "REPLACE(diag_description,'\r\n','')='".$chkDesc."' ";
			$row = sqlQuery($sql);
		}

		if($row != false){
			$dxCode = xss_rem($row["dx_code"]); $dxCode_db = trim($dxCode);
			$pqriCode = trim(xss_rem($row["pqriCode"]));
			$diagDesc = trim(xss_rem($row["diag_description"]));
			$incomplete = (strpos($dxCode, "-")!==false) ? "1" : "0" ;

			if(!empty($ICD10)){
				//add litrality
				if(strtolower($dxCode) == strtolower($chkDesc_2) || strtolower($dxCode) == strtolower($chkDesc_3) || strtolower($dxCode) == strtolower($chkDesc_4)){ $dxCode = $chkDesc; }

				//--
				$odx = new Dx();
				$str_dx_lss = $odx->get_lss_from_2dx($dxCode, $dxCode_db, array($row["laterality"], $row["staging"], $row["severity"]));
				if(!empty($str_dx_lss) && !empty($diagDesc)){
					$diagDesc = $diagDesc."; ".$str_dx_lss;
				}
				//--
			}

			$str .= "<dxcode>".strtoupper($dxCode)."</dxcode>";
			$str .= "<dxcodedesc>".$diagDesc."</dxcodedesc>";
			$str .= "<incomplete>".$incomplete."</incomplete>";

			$isMediCared = "false";
			$arrPqriCode = array();
			//Check Medicare Insurance
			$oPt = new Patient($pid);
			if($oPt->isPtInsMedicare(1)){
				//Check Associated PQRI codes
				if(!empty($pqriCode)){
					$arrPqriCodeTmp = explode(",",$pqriCode);
					$len = count($arrPqriCodeTmp);
					//Check Pqri Code
					$oAdmn = new Admn();
					for($i=0;$i<$len;$i++){
						$tmpCode = trim($arrPqriCodeTmp[$i]);
						$tmpCode = $oAdmn->checkPqriCode($tmpCode);
						if(!empty($tmpCode)){
							$arrPqriCode[]=$tmpCode;
						}
					}
				}
				$isMediCared = "true";
			}

			$str .="<medicare>".$isMediCared."</medicare>";
			$str .="<checkAsscPqri>".implode(",",$arrPqriCode)."</checkAsscPqri>";

			$arrValidPqriCode = array();
			if(count($arrPqriCode) > 0){
				//Check if PQRI Code has been billed in this calendar year
				foreach($arrPqriCode as $key => $val){
					$temp = $this->isPqriCodeBilled($val);
					if(!$temp){
						$arrValidPqriCode[]=$val;
					}
				}
			}

			$str .="<checkNotBilledPqri>".implode(",",$arrValidPqriCode)."</checkNotBilledPqri>";

			$str .= "<pqri>";
			if(count($arrValidPqriCode) > 0){
				foreach($arrValidPqriCode as $key => $val){
					$str .= "<cpt>".$val."</cpt>";
				}
			}
			$str .= "</pqri>";
		}

		//if multiple dx codes
		if(!empty($otherdxCodes_multi)){$str .= "<otherdxCodes_multi>".$otherdxCodes_multi."</otherdxCodes_multi>";}
		$str .= "</dxcodeinfo>";
		echo $str;

	}

	function checkCL_FitRefitEva_v2($dt){
		$pid = $this->pid;
		//$fid =  $this->fid;

		$t_ar=array();
		$t_prc = 0;
		$sql = "SELECT  c1.charges_id FROM  contactlensmaster c1
				WHERE c1.patient_id='".$pid."' AND c1.dos = '".$dt."'
				AND c1.charges_id!=''
				AND c1.del_status = '0'
				ORDER BY c1.clws_id DESC LIMIT 0, 1
				";
		//echo $sql;
		$rez = sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++){
			$str_charge_id = $row["charges_id"];
			if(!empty($str_charge_id)){
				$ar_charge_id = explode(",", $str_charge_id);
				if(count($ar_charge_id) > 0){

					foreach($ar_charge_id as $k_ci => $v_ci){

						$v_ci = trim($v_ci);

						$sql = "SELECT c2.price, c3.cpt_prac_code FROM cl_charges c2
								LEFT JOIN cpt_fee_tbl c3 ON c3.cpt_fee_id = c2.cpt_fee_id
								WHERE c2.cl_charge_id='".$v_ci."' AND c2.del_status = '0' AND c3.delete_status = '0'  ";
						$rez2 = sqlStatement($sql);
						for($j=0;$row2=sqlFetchArray($rez2);$j++){

							$prc = $row2["price"];
							$cpt = $row2["cpt_prac_code"];
							$t_prc = (float)$t_prc + (float)$row2["price"];
							$t_ar[] = $cpt;

						}

					}
				}
			}

		}
		//
		$str_t_ar="";
		if(count($t_ar)>0){	$str_t_ar = implode(", ", $t_ar);	  }

		$ret = array($t_prc, $t_ar, $str_t_ar);

		//print_r($ret);

		return $ret;
	}

	function calc_today_charges(){
		//$formId = $_POST["elem_formId"];
		$insCaseId = xss_rem($_POST["elem_insCaseId"]);
		$strCptCode = sqlEscStr(xss_rem($_POST["elem_strCptCodes"]));
		$strDos = xss_rem($_POST["elem_dos"]);
		$strCptUnit = xss_rem($_POST["elem_strCptUnits"]);
		$vVIP = xss_rem($_POST["elem_vVIP"]);
		$mod50 = xss_rem($_POST["elem_mod50"]);

		$strCptCode = strtoupper($strCptCode);
		$arrCpt = explode(",", $strCptCode);
		$arrUnits = explode(",", $strCptUnit);
		$arrmod50 = explode(",", $mod50);

		$strCptCode = (!empty($strCptCode)) ? " '".str_replace(",","', '",$strCptCode)."' " : "" ;

		//contact lens
		$pid = $this->pid; // $_SESSION["patient"];
		if(!empty($pid)){
			//list($clCpt, $clCptCost) = getCL_CPT_Charges($pid, $strDos);
			$strDos_ymd=wv_formatDate($strDos, 0,0, "insert");
			list($clCptCost, $cl_ar_cpt, $clCpt) = $this->checkCL_FitRefitEva_v2($strDos_ymd);
		}

		//Insurance case
		if(!isset($insCaseId) || empty($insCaseId))
		{
			$oPt = new Patient($patientId);
			$insCaseId = $oPt->getInuranceCaseId();
		}

		// Fee Column
		if(isset($insCaseId) && !empty($insCaseId)){
			$oIns = new Insurance();
			$insFeeColumn = $oIns->getInsFeeColumn($insCaseId, $strDos);
		}

		$insFeeColumn = ($insFeeColumn == 0) ? 1 : $insFeeColumn;

		// Get Fee Rates
		$arrCptTmp = array();
		$cptCharges = 0;
		$arrCptFee = array();
		if(!empty($strCptCode)){
			$sql = "SELECT
				cpt_fee_tbl.cpt_prac_code,
				cpt_fee_tbl.cpt_desc,
				cpt_fee_tbl.mod1,
				cpt_fee_tbl.mod2,
				cpt_fee_tbl.commonlyUsed,
				cpt_fee_table.cpt_fee
				FROM cpt_category_tbl
				INNER JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_cat_id = cpt_category_tbl.cpt_cat_id
				INNER JOIN cpt_fee_table ON cpt_fee_table.cpt_fee_id = cpt_fee_tbl.cpt_fee_id
				WHERE cpt_fee_tbl.status='Active' AND cpt_fee_tbl.delete_status='0'
				AND cpt_fee_table.fee_table_column_id='".$insFeeColumn."'
				AND UCASE(cpt_fee_tbl.cpt_prac_code) IN (".$strCptCode.")
				ORDER BY cpt_category_tbl.cpt_category, cpt_fee_tbl.cpt_desc";
			//echo $sql;exit();
			$rez = sqlStatement($sql);
			for($i=1;$row=sqlFetchArray($rez);$i++)
			{
				if(!empty($row["cpt_prac_code"]) && (!empty($row["cpt_fee"])) && (!in_array($row["cpt_prac_code"], $arrCptTmp))){
					$tmp = strtoupper($row["cpt_prac_code"]);
					$arrCptFee [$tmp]=$row["cpt_fee"];
					$arrCptTmp[] = $row["cpt_prac_code"];
				}
			}
		}
		//
		$oCpt = new CPT();

		// Calculate
		$len = count($arrCpt);
		for($i=0;$i<$len;$i++){

			//CL cost: take from contact lens and not from admin->feetable
			if(!empty($clCpt) && strpos($clCpt, $arrCpt[$i])!==false){
				if(!empty($clCptCost)){
					if(in_array($clCpt, $arrmod50)){ $clCptCost = $clCptCost * 2; }
					$cptCharges = $cptCharges + $clCptCost; $clCptCost=0;/*so that it is added once for multiple cl cpt codes*/
				}
				continue;
			}

			if($vVIP==1&&$oCpt->mkCPTCode($arrCpt[$i])=="92015"){continue;}

			//$key = array_search($row["cpt4_code"], $arrCpt);
			$units = ( !empty($arrUnits[$i]) ) ? $arrUnits[$i] : 1;
			if(in_array($arrCpt[$i], $arrmod50)){ $units = $units * 2; }//50 mod double cpt cost
			//echo 	$arrCpt[$i]." : ".$units." : ".$arrCptFee [$arrCpt[$i]];
			$cptCharges = $cptCharges + ($arrCptFee [$arrCpt[$i]] * $units);
		}

		echo number_format($cptCharges,2);
	}

	function getLevelofService( $levelHistory, $levelExam,$practiceBillCode,$levelComp=0,$levelROS=0, $levelpfsh=0, $flgIsPtNew="" ){
		$lvlTemp="0";
		if(($practiceBillCode != "920")){ //E/M

			if( !empty($levelHistory) &&  !empty($levelExam) && !empty($levelComp) ){//&& !empty($levelROS)

				if($flgIsPtNew=="Yes_confirmed"){
					if( ($levelHistory == 3) && ($levelExam == 5 ) && ($levelComp == 4) && $levelROS>=10 && !empty($levelpfsh)  ){
						$lvlTemp = 5;
					}else if( ($levelHistory == 3) && ($levelExam >= 4 ) && ($levelComp >= 3) && $levelROS>=10 && !empty($levelpfsh) ){
						$lvlTemp = 4;
					}else if( ($levelHistory == 3) && ($levelExam >= 3 ) && ($levelComp >= 2) && ($levelROS>=2) && !empty($levelpfsh) ){
						$lvlTemp = 3;
					}else if( ($levelHistory >= 2) && ($levelExam >= 2 ) && ($levelComp >= 1) && $levelROS>=1 ){
						$lvlTemp = 2;
					}else if( ($levelHistory >= 1) && ($levelExam >= 1 ) && ($levelComp >= 1) ){
						$lvlTemp = 1;
					}else{
						$lvlTemp = 0;
					}
				}else{

					if( ($levelHistory == 3) && ($levelExam == 5 ) && ($levelComp == 4) && $levelROS>=10 && !empty($levelpfsh)  ){
						$lvlTemp = 5;
					}else if( ($levelHistory == 3) && ($levelExam >= 4 ) && ($levelComp >= 3) && $levelROS>=2 && !empty($levelpfsh) ){
						$lvlTemp = 4;
					}else if( ($levelHistory >= 2) && ($levelExam >= 3 ) && ($levelComp >= 2) && ($levelROS>=1)  ){
						$lvlTemp = 3;
					}else if( ($levelHistory >= 2) && ($levelExam >= 2 ) && ($levelComp >= 1) && $levelROS>=0 ){
						$lvlTemp = 2;
					}else if( ($levelHistory >= 1) && ($levelExam >= 1 ) && ($levelComp >= 1) ){
						$lvlTemp = 1;
					}else{
						$lvlTemp = 0;
					}

				}
			}
		}else{ //Eye
			if(!empty($levelHistory)){
				$lvlTemp = !empty($levelExam) ? $levelExam : 0;
			}
		}

		return $lvlTemp;
	}

	function autocode(){
		//XML HEADER
		header('Content-Type: text/xml');
		$str .= "<?xml version='1.0' encoding='ISO-8859-1'?>";
		$str .= "<dxcodeinfo>";

		$pid = $this->pid; //$_SESSION["patient"];
		$form_id = xss_rem($_POST["elem_formId"]);
		$this->fid = $form_id;
		$dos = xss_rem($_POST["elem_dos"]);
		$insCaseId = xss_rem($_POST["elem_insCaseId"]);
		$elem_doctorId = xss_rem($_POST["elem_doctorId"]);
		$elem_v_icd10= xss_rem($_POST["elem_v_icd10"]);

		//obj
		$oPt = new Patient($pid);
		$oAdmn = new Admn();
		$oCN = new ChartNote($pid,$form_id);
		$oClsDrw = new CLSDrawingData();
		$oSB = new SuperBill($pid);

		//getEncounterId
		$enc_id = $oCN->getChartEncounterId();

		// Check if Pt has Insurance 'Medicare' --
		$flgPtInsMedicare = $oPt->isPtInsMedicare();

		///-------------
		//ApId
		$apid = $_POST["elem_apid"];
		//DxCodes
		$arr_dx = array();
		for($i=1;$i<=12;$i++){
			$temp_elem_dxCode = xss_rem($_POST["elem_dxCode".$i]);
			if(!empty($temp_elem_dxCode)){
				$arr_dx[]=$temp_elem_dxCode;
			}
		}

		//get Cpt from ap policy Through Dx
		if(!empty($apid) && count($arr_dx) > 0){
			$oUsrAP = new UserAp();
			$arrApCpt = $oUsrAP->getApCptFrmDx($apid,$arr_dx,$elem_v_icd10);
		}
		$lenApCpt = count($arrApCpt);
		//--------------


		//if DOS empty
		if(!isset($dos) || empty($dos))
		{
			$dos = $oCN->getDos();
		}

		//Dos YMd
		$dos_ymd = wv_formatDate($dos,0,0,'insert');

		//Insurance case: NOT Mandatory in super bill: Attached if Only One case exists.
		$oIns = new Insurance();
		if(!isset($insCaseId) || empty($insCaseId))
		{
			$insCaseId = $oPt->getInuranceCaseId();
		}

		//get ins Fee column
		$insProName = "";
		$insFeeColumn = 0;
		if(isset($insCaseId) && !empty($insCaseId)){
			$insFeeColumn = $oIns->getInsFeeColumn($insCaseId, $dos);
			//Get insurance provider name
			$insProName = $oIns->getInsProviderInfo($insCaseId);
		}

		//POE Visit Code
		$oPoe = new Poe($pid, $enc_id);
		if($oPoe->isPtInPoe()){ $post_op_visit="1"; }
		//

		$elem_flgIsPtNew = xss_rem($_POST["elem_flgIsPtNew"]);
		$elem_refraction = xss_rem($_POST["elem_refraction"]);
		$elem_refractionDx = xss_rem($_POST["elem_refractionDx"]);
		$elem_visionEye= xss_rem($_POST["elem_visionEye"]);
		$postVision = xss_rem($_POST["elem_vision"]);
		$levelComplexity = xss_rem($_POST["elem_levelComplexity"]);
		$proc_only_visit = xss_rem($_POST["elem_proc_only_visit"]);
		if(empty($post_op_visit)){$post_op_visit = xss_rem($_POST["elem_post_op_visit"]);}
		$practiceBillCode = xss_rem($_POST["elem_practiceBillCode"]);

		$postRvs = xss_rem($_POST["elem_rvs"]);
		$postCcHx = $_POST["elem_ccHx"]; //removed from this field because it sometime return empty string
		$postAssess = xss_rem($_POST["elem_assessment"]);
		$postNpsych = xss_rem($_POST["elem_neuroPsych"]);
		$el_manual_insert_visitcode = xss_rem($_POST["el_manual_insert_visitcode"]);
		if(!empty($el_manual_insert_visitcode)){ $post_op_visit="0"; }

		// Get Common Cpt Desc, Codes, Proc Fee, Modifiers
		$ocpt = new CPT();
		$ocpt->set_flg_pt_ins($flgPtInsMedicare); //set pt insurance in cpt
		//Dx
		$odx = new Dx();

		list($arrDesc,$arrCptCodes,$arrProcedureFee,$arrCptModifiers) = $ocpt->getCommonlyUsedCPTCodes($insFeeColumn); //
		for($i=1;$i<=3;$i++)
		{
			$empty = "User Defined".$i;
			$arrDesc[] = $arrUserDefined[] = $empty;
			$arrCptCodes[$empty]=array("");
		}

		//Array Cpt Codes And Desc
		$arrCptCats4Menu = array();
		$arrCptCodeAndDesc = array();
		$arrActiveCptCodes=array();
		$arrPracticCptCodes=array();
		$sql = "SELECT * FROM cpt_category_tbl ORDER BY cpt_category ";
		$rezCat = sqlStatement($sql);
		for($i=1;$rowCat=sqlFetchArray($rezCat);$i++)
		{
			$sql = "SELECT cpt_fee_tbl.cpt_desc, cpt_fee_tbl.cpt4_code, cpt_fee_tbl.cpt_prac_code ".
				 "FROM cpt_fee_tbl ".
				 "WHERE cpt_cat_id = '".$rowCat["cpt_cat_id"]."' AND cpt_fee_tbl.status = 'Active' AND delete_status = '0'".
				 "ORDER BY cpt_fee_tbl.cpt_desc ";
			$rez = sqlStatement($sql);
			$arrCodeTmp = array();
			for($i=1;$row=sqlFetchArray($rez);$i++)
			{
				if(!empty($row["cpt_desc"]) && array_search($row["cpt_desc"],$arrCptCodeAndDesc) === false)
				{
					$arrCptCodeAndDesc[] = addslashes($row["cpt_desc"]);
					$arrActiveCptCodes[$row["cpt_desc"]]=$ocpt->getCptCodesFromDesc_v2($row["cpt_desc"]);
				}
				$arrCptCodeAndDesc[] = $row["cpt_prac_code"];
				$arrPracticCptCodes[] = $row["cpt_prac_code"];
				$arrCodeTmp[] = array($row["cpt_prac_code"]."-".$row["cpt_desc"],$arrEmpty,$row["cpt_prac_code"]."-".$row["cpt_desc"]);

			}
			if(count($arrCodeTmp) > 0)
			{
				$arrCptCats4Menu[] = array($rowCat["cpt_category"],$arrCodeTmp);
			}
		}



		//GET information w.r.t Tests CPTS--

		//$arr_SuperbillTestCpt=$oAdmn->getSuperTestCpt();

		//GET information w.r.t Tests CPTS--


		/*//
		//Insert GCODE G8447
		$arrGCodeExam=array();
		if($flgPtInsMedicare){
			$tmp = checkTest4CptDesc("EHR-GCODE");
			$arrGCodeExam[] = $tmp;
		}
		*/

		// Check if Pt has Insurance 'Medicare' --

		// Auto Code Exams
		if(isset($form_id) && !empty($form_id)){
			//Fun Here
			$row = $oCN->getChartNotesExamsDone();
			if($row != false)
			{
				$arrExamsDone = array(); //$arrGCodeExam;
				$arrExamsDoneUnits = array();
				$arrUserDefinedExamsDone = array(); //User Defined2
				$arrModifiersExamsDone = array();
				$arrDxExamsDone = array();
				if(empty($doctorId)){
					$doctorId = (!empty($row["doctorId"])) ? $row["doctorId"] : $_SESSION["authId"];
				}

				//HRT
				if(!empty($row["nfa_id"]) ) //&& !empty($row["phyNameNfa"])
				{
					$tmp = $ocpt->checkTest4CptDesc("HRT",$row["scanLaserEye"]);
					$arrExamsDone = array_merge($arrExamsDone,$tmp);

					/*
					$tmp = checkTest4CptDesc("HRT/OCT/GDX");
					$arrExamsDone[] = $tmp;
					*/

					/*
					$tmp = checkTest4CptDesc("HRT/OCT/GDX");
					//array_search("92135",$arrCptCodes);
					$arrExamsDone[] = $tmp; //"92135";//"HRT/OCT/GDX";
					*/
					/* 
					$arrModifiersExamsDone[$tmp] = $row["scanLaserEye"];
					if($row["scanLaserEye"] == "OU")
					{
						$arrUserDefinedExamsDone[]=$tmp;//"92135";//"HRT/OCT/GDX";
					}
					*/
				}

				//OCT
				if(!empty($row["oct_id"]) ) //&& !empty($row["phyNameOct"])
				{
					if($row["scanLaserOct"]=="3"){
						$tmp = $ocpt->checkTest4CptDesc("OCT-Anterior Segment", $row["scanLaserEyeOct"]);
					}elseif($row["scanLaserOct"]=="2"){
						$tmp = $ocpt->checkTest4CptDesc("OCT-Retina", $row["scanLaserEyeOct"]);
					}else{
						$tmp = $ocpt->checkTest4CptDesc("OCT", $row["scanLaserEyeOct"]);
					}
					$arrExamsDone = array_merge($arrExamsDone,$tmp);

					/*
					if($row["scanLaserOct"]=="3"){
						$tmp = checkTest4CptDesc("HRT/OCT/GDX-Anterior Segment");
					}elseif($row["scanLaserOct"]=="2"){
						$tmp = checkTest4CptDesc("HRT/OCT/GDX-Retina");
					}else{
						$tmp = checkTest4CptDesc("HRT/OCT/GDX");
					}
					//$tmp = ($row["scanLaserOct"]=="2") ? checkTest4CptDesc("HRT/OCT/GDX-Retina") : checkTest4CptDesc("HRT/OCT/GDX");

					$arrExamsDone[] = $tmp;
					*/

					/* 3/29/2011 Stopped
					$arrModifiersExamsDone[$tmp] = $row["scanLaserEyeOct"];
					if($row["scanLaserEyeOct"] == "OU")
					{
						$arrUserDefinedExamsDone[]=$tmp;
					}
					*/
				}

				//OCT-RNFL
				if(!empty($row["oct_rnfl_id"]) ){ //&& !empty($row["phyNameOct_RNFL"])

					$tmp = $ocpt->checkTest4CptDesc("OCT-RNFL",$row["scanLaserEyeOct_RNFL"]);
					$arrExamsDone = array_merge($arrExamsDone,$tmp);

				}

				//GDX
				if(!empty($row["gdx_id"]) ) //&& !empty($row["phyNameGdx"])
				{
					$tmp = $ocpt->checkTest4CptDesc("GDX", $row["scanLaserEyeGdx"]);
					$arrExamsDone = array_merge($arrExamsDone,$tmp);
					/*
					$tmp = checkTest4CptDesc("HRT/OCT/GDX");
					$arrExamsDone[] = $tmp;
					*/
				}

				if(!empty($row["vfNewId"]) ) //&& !empty($row["phyNameVF"])
				{
					//
					//Do not show OU as vf is biletral test
					//
					/*
					$vfEye = "";
					if(!empty($row["vfSumOd"])){
						$vfEye = "OD";
						if(!empty($row["vfSumOs"])){
							$vfEye = "OU";
						}
					}else if(!empty($row["vfSumOs"])){
						$vfEye = "OS";
					}
					*/

					$tmp = $ocpt->checkTest4CptDesc("VF",$row["vf_eye"]);
					$arrExamsDone = array_merge($arrExamsDone,$tmp);

					/*
					$tmp = checkTest4CptDesc("Visual Field");
					$arrExamsDone[] = $tmp; //"Visual Field";
					*/
					//$arrModifiersExamsDone[$tmp] = (($vfEye == "OD") || ($vfEye == "OS")) ? $vfEye : "";

					/*
					if($vfEye == "OU")
					{
						$arrUserDefinedExamsDone[]=$tmp; //"Visual Field";
					}*/
				}

				//VF-GL
				if(!empty($row["vfGLId"]) ){ //&& !empty($row["phyNameVFGL"])

					$tmp = $ocpt->checkTest4CptDesc("VF-GL",$row["vf_gl_eye"]);
					$arrExamsDone = array_merge($arrExamsDone,$tmp);

				}


				if(!empty($row["pachy_id"]) ) //&& !empty($row["phyNamePachy"])
				{
					//
					//Do not show OU as pachy is biletral test
					//
					/*
					$tmp = checkTest4CptDesc("Pachymetry");
					$arrExamsDone[] = $tmp; //"Pachymetry";//"Pachy Metering";
					*/

					$tmp = $ocpt->checkTest4CptDesc("Pachy",$row["pachyMeterEye"]);
					$arrExamsDone = array_merge($arrExamsDone,$tmp);

					/*
					$arrModifiersExamsDone[$tmp] = (($row["pachyMeterEye"] == "OD") || ($row["pachyMeterEye"] == "OS")) ? $row["pachyMeterEye"] : "" ;

					if($row["pachyMeterEye"] == "OU")
					{
						$arrUserDefinedExamsDone[]= $tmp; //"Pachymetry";
					}*/
				}
				if(!empty($row["vfId"])  ) //&& !empty($row["phyNameIvfa"])
				{
					$ivfaEye = "";//
					if($row["ivfa_od"] == "1"){
						$ivfaEye = "OU";
					}else if($row["ivfa_od"] == "2"){
						$ivfaEye = "OD";
					}else if($row["ivfa_od"] == "3"){
						$ivfaEye = "OS";
					}

					$tmp = $ocpt->checkTest4CptDesc("IVFA",$ivfaEye);
					$arrExamsDone = array_merge($arrExamsDone,$tmp);

					/*
					$tmp = checkTest4CptDesc("IVFA");
					$arrExamsDone[] = $tmp; //"IVFA";
					*/

					/* //Date : 22-03-2013 : Discuss with Arun About IVFA Two Codes in case of OU and related modifiers --
					if($row["ivfa_od"] == "1")
					{
						$arrModifiersExamsDone[$tmp] = "OU";
						$arrUserDefinedExamsDone[]=$tmp; //"IVFA";
					}else if($row["ivfa_od"] == "2"){
						$arrModifiersExamsDone[$tmp] = "OD";
					}else if($row["ivfa_od"] == "3"){
						$arrModifiersExamsDone[$tmp] = "OS";
					}
					*/
				}

				if(!empty($row["disc_id"])  ) //&& !empty($row["phyNameDisc"])
				{
					$tmp = $ocpt->checkTest4CptDesc("Fundus",$row["photoEye"]);
					$arrExamsDone = array_merge($arrExamsDone,$tmp);

					/*
					$tmp = checkTest4CptDesc("Fundus Photos");
					$arrExamsDone[] = $tmp; //"Fundus Photos";

					if(!empty($insProName) &&
							(($insProName["insPro"] == "Medicare") || ($insProName["insPro"] == "Aetna") )
						) {
						//Nothing
					}else{

						/*
						//8/19/2011: Fundus � is Bilateral you do not need 2 code with RT and LT modifiers.

						if($row["photoEye"] == "OU")
						{
							$arrUserDefinedExamsDone[]=$tmp; //"Fundus Photos";
							$arrModifiersExamsDone[$tmp] = "OU";
						}
						else if($row["photoEye"] == "OS")
						{
							$arrModifiersExamsDone[$tmp] = "OS";
						}
						else if($row["photoEye"] == "OD")
						{
							$arrModifiersExamsDone[$tmp] = "OD";
						}
						* /
					}
					*/
				}

				//Exterior / Anterior Segment Photos
				if((!empty($row["discExterPhoto"])) ){ //&& (!empty($row["phyNameDiscExter"]))

					$tmp = ($row["discExterPhoto"] == "2") ? $ocpt->checkTest4CptDesc("Anterior Segment Photos", $row["discExterEye"]) : $ocpt->checkTest4CptDesc("External Photos", $row["discExterEye"]);
					$arrExamsDone = array_merge($arrExamsDone,$tmp);


					/*
					/* //Date : 22-03-2013 : Discuss with Arun About 'Exterior / Anterior Segment Photos' Two Codes in case of OU and related modifiers --
					$tmp = ($row["discExterPhoto"] == "2") ? checkTest4CptDesc("Anterior Segment Photos") : checkTest4CptDesc("External Photos");

					$arrExamsDone[] = $tmp;

					if($row["discExterPhoto"] == 2){ // ASP
						if($row["discExterEye"] == "OU")
						{
							$arrUserDefinedExamsDone[]=$tmp;
							$arrModifiersExamsDone[$tmp] = "OU";
						}
						else if($row["discExterEye"] == "OS")
						{
							$arrModifiersExamsDone[$tmp] = "OS";
						}
						else if($row["discExterEye"] == "OD")
						{
							$arrModifiersExamsDone[$tmp] = "OD";
						}
					}
					*/
				}

				//Topography
				if(!empty($row["topo_id"]) )	//&& !empty($row["phyNameTopo"])
				{
					$tmp = $ocpt->checkTest4CptDesc("Topography", $row["topoMeterEye"]);
					$arrExamsDone = array_merge($arrExamsDone,$tmp);

					/*
					$tmp = checkTest4CptDesc("Topography");
					$arrExamsDone[] = $tmp;
					*/

					/*
					$arrModifiersExamsDone[$tmp] = (($row["topoMeterEye"] == "OD") || ($row["topoMeterEye"] == "OS")) ? $row["topoMeterEye"] : "" ;

					if($row["topoMeterEye"] == "OU")
					{
						$arrUserDefinedExamsDone[]=$tmp;
					}
					*/
				}

				//Refraction
				//if(($row["vis_mr_od_s"] != "") || ($row["vis_mr_os_s"] != ""))
				//{
					//
					$oVis = new Vision($pid, $form_id);
					//if Physician is set to accept Refrection
					$oUsr = new User($elem_doctorId);
					list($flgPhyRefSet,$flgRefGivenOnly) = $oUsr->getPhyRefSetting();
					if($flgPhyRefSet){
						if($elem_refraction == "db"){
							list($chkRef, $dxRef, $eyeMR) = $oVis->isRefraction(1, $flgRefGivenOnly);
						}else{
							$chkRef = ($elem_refraction=="1") ? true : false;
							$dxRef = $elem_refractionDx;
							$eyeMR = $elem_visionEye;
						}
					}else{
						$chkRef = false;
					}

					if(isset($GLOBALS['STOP_REF_DX_LINK']) && !empty($GLOBALS['STOP_REF_DX_LINK'])){ //stop linking dx code with refraction code : 28480-Clarity Eye Center-Refractions automatically populate Presbyopia/Myopia
						$dxRef = "";
					}


					//a.       Miramar - PO visit � when the Pt has hyperopia the MR PCT codes always drops down even if they did or did not do the MR.  In PO visit MR code should never drop down if they want ot they can type it in
					if(isset($GLOBALS['STOP_REF_UNDER_SURGERY']) && !empty($GLOBALS['STOP_REF_UNDER_SURGERY'])){
						if(!empty($post_op_visit)){  $chkRef = false; }
					}



					if($chkRef){


						$tmp = $ocpt->checkTest4CptDesc("Refraction", $eyeMR);
						$arrExamsDone = array_merge($arrExamsDone,$tmp);
						if(!empty($dxRef) && count($tmp)>0){

							if($elem_v_icd10=="1"){ //convert to icd10
								$dxRef=$odx->convertICDDxCode($dxRef, 9, 1);
								if(empty($eyeMR)){ $eyeMR="OU"; }
								$dxRef=$odx->modifyICDDxCodeWEye($dxRef, $eyeMR);
							}

							foreach($tmp as $key => $val){		//Add Dx code All related Cpts of Refraction
								$arrDxExamsDone[$val] = $dxRef;
							}
						}

						/*
						$tmp = checkTest4CptDesc("Refraction"); //"Refraction";
						$arrExamsDone[] = $tmp;
						if(!empty($dxRef)){
							$arrDxExamsDone[$tmp] = $dxRef;
						}
						*/
					}


				//}

				if($row["amslerGridId"] != NULL){
					/*
					$arrExamsDone[] = checkTest4CptDesc("Amsler Grid");
					*/

					$apOd = $oClsDrw->isAppletModified($row["amsler_od"]);
					$apOs = $oClsDrw->isAppletModified($row["amsler_os"]);
					$tmpEye="";
					if( ($apOd == true) && ($apOs == true) ){
						$tmpEye = "OU";
					}else if($apOd == true){
						$tmpEye = "OD";
					}else if($apOs == true){
						$tmpEye = "OS";
					}

					$tmp = $ocpt->checkTest4CptDesc("Amsler Grid", $tmpEye);
					$arrExamsDone = array_merge($arrExamsDone,$tmp);

				}

				/**
				//02-04-2013: No Need for exams to Test in superbill

				if($row["eomId"] != NULL && !empty($row["seEom"]) && strpos($row["seEom"],"=1") !== false && $row["isPos_EOM"]=="1"){ //Agt 09, 2012 : The EOM CPT code should only come up if actual testing is done and NOT just select WNL from Summary Sheet or in the EOM chart
					/*
					$arrExamsDone[] = checkTest4CptDesc("EOM");
					* /
					$tmp = checkTest4CptDesc("EOM");
					$arrExamsDone = array_merge($arrExamsDone,$tmp);

				}
				if($row["eeId"] != NULL && !empty($row["seEe"]) && strpos($row["seEe"],"=1") !== false){
					/*
					$arrExamsDone[] = checkTest4CptDesc("External Exam");
					* /
					$tmpEye="";
					if(strpos($row["seEe"],"Od=1") !== false){ $tmpEye="OD"; }
					if(strpos($row["seEe"],"Os=1") !== false){ $tmpEye=($tmpEye=="OD") ? "OU" : "OS" ; }
					$tmp = checkTest4CptDesc("External", $tmpEye);
					$arrExamsDone = array_merge($arrExamsDone,$tmp);
				}

				**/



				if($row["gonioId"] != NULL &&
					!empty($row["seGonio"]) && strpos($row["seGonio"],"=1") !== false &&
					(!empty($row["isPos_gonio"]) ||
					 !empty($row["wnlFrmGonioOd"]) || !empty($row["wnlFrmGonioOd"]) ||
					 !empty($row["wnlDrawOdGonio"]) || !empty($row["wnlDrawOsGonio"]))
				){
					/*
					$arrExamsDone[] = checkTest4CptDesc("Gonioscopy");
					*/
					$tmpEye="";
					if(strpos($row["seGonio"],"Od=1") !== false){ $tmpEye="OD"; }
					if(strpos($row["seGonio"],"Os=1") !== false){ $tmpEye=($tmpEye=="OD") ? "OU" : "OS" ; }
					$tmp = $ocpt->checkTest4CptDesc("Gonioscopy", $tmpEye);
					$arrExamsDone = array_merge($arrExamsDone,$tmp);
				}

				/**
				//02-04-2013: No Need for exams to Test in superbill

				if($row["laId"] != NULL && !empty($row["seLa"]) && strpos($row["seLa"],"=1") !== false){
					/*
					$arrExamsDone[] = checkTest4CptDesc("LA");
					* /
					$tmpEye="";
					if(strpos($row["seLa"],"Od=1") !== false){ $tmpEye="OD"; }
					if(strpos($row["seLa"],"Os=1") !== false){ $tmpEye=($tmpEye=="OD") ? "OU" : "OS" ; }
					$tmp = checkTest4CptDesc("LA", $tmpEye);
					$arrExamsDone = array_merge($arrExamsDone,$tmp);
				}
				if($row["opticId"] != NULL && !empty($row["seOptic"]) && strpos($row["seOptic"],"=1") !== false){
					/*
					$arrExamsDone[] = checkTest4CptDesc("Optic");
					* /
					$tmpEye="";
					if(strpos($row["seOptic"],"Od=1") !== false){ $tmpEye="OD"; }
					if(strpos($row["seOptic"],"Os=1") !== false){ $tmpEye=($tmpEye=="OD") ? "OU" : "OS" ; }
					$tmp = checkTest4CptDesc("Optic Nerve", $tmpEye);
					$arrExamsDone = array_merge($arrExamsDone,$tmp);
				}
				if($row["pupilId"] != NULL && !empty($row["sePupil"]) && strpos($row["sePupil"],"=1") !== false){
					/*
					$arrExamsDone[] = checkTest4CptDesc("Pupil");
					* /
					$tmpEye="";
					if(strpos($row["sePupil"],"Od=1") !== false){ $tmpEye="OD"; }
					if(strpos($row["sePupil"],"Os=1") !== false){ $tmpEye=($tmpEye=="OD") ? "OU" : "OS" ; }
					$tmp = checkTest4CptDesc("Pupil", $tmpEye);
					$arrExamsDone = array_merge($arrExamsDone,$tmp);
				}
				if($row["rvId"] != NULL && !empty($row["seRv"]) && strpos($row["seRv"],"=1") !== false){
					/*
					$arrExamsDone[] = checkTest4CptDesc("RV");
					* /
					$tmpEye="";
					if(strpos($row["seRv"],"Od=1") !== false){ $tmpEye="OD"; }
					if(strpos($row["seRv"],"Os=1") !== false){ $tmpEye=($tmpEye=="OD") ? "OU" : "OS" ; }
					$tmp = checkTest4CptDesc("Retinal Exam", $tmpEye);
					$arrExamsDone = array_merge($arrExamsDone,$tmp);
				}
				if($row["sleId"] != NULL && !empty($row["seSle"]) && strpos($row["seSle"],"=1") !== false){
					/*
					$arrExamsDone[] = checkTest4CptDesc("SLE");
					* /
					$tmpEye="";
					if(strpos($row["seSle"],"Od=1") !== false){ $tmpEye="OD"; }
					if(strpos($row["seSle"],"Os=1") !== false){ $tmpEye=($tmpEye=="OD") ? "OU" : "OS" ; }
					$tmp = checkTest4CptDesc("SLE", $tmpEye);
					$arrExamsDone = array_merge($arrExamsDone,$tmp);
				}
				if($postVision == "db"){
					/*
					$arrExamsDone[] = ($row["visId"] != NULL) ? checkTest4CptDesc("Vision") : "";
					* /
					$tmp = checkTest4CptDesc("Vision");
					$arrExamsDone = array_merge($arrExamsDone,$tmp);

				}else if($postVision == "1"){
					/*
					$arrExamsDone[] = checkTest4CptDesc("Vision");
					* /
					$tmp = checkTest4CptDesc("Vision");
					$arrExamsDone = array_merge($arrExamsDone,$tmp);
				}

				**/

				/*
				if(!empty($row["vfId"]) || !empty($row["disc_id"])){
					$arrExamsDone[] = checkTest4CptDesc("IVFA/Disc");
				}
				if(!empty($row["vfNewId"]) || !empty($row["nfa_id"]) || !empty($row["pachy_id"])){
					$arrExamsDone[] = checkTest4CptDesc("VF/NFA/PACHY");
				}
				*/

				//dilatation
				$dilation=0;
				//if($row["dia_id"] != NULL&&empty($row["noDilation"])&&empty($row["unableDilation"])&&!empty($row["dilation"])){
				if($row["dia_id"] != NULL&&empty($row["noDilation"])){ //Ophthalmoscopy � Refuse Dilation for Ophtalmoscopy is same as if Pt was dilated i.e.  should not check if Pt was dilated for Ophthalmoscopy.
					if(!empty($row["dilation"])&&empty($row["unableDilation"])){
						$tmp= unserialize($row["dilation"]);
						if(count($tmp) > 0){
							foreach($tmp as $tmp_k => $tmp_v){
								if((!empty($tmp_v["dilate"]) || !empty($tmp_v["other_desc"])) && !empty($tmp_v["time"])){$dilation=1;break;}
							}
						}
					}else if(!empty($row["unableDilation"])){
						$dilation=1;
					}
				}

				// Optha
				/*
				Ophthalmoscopy Code
				a.       92225 and 92226 � Should use be listed on each line with Modifier RT and LT for patient that have commercial insurance
				b.      92225 and 92226 � As per current implementation for patients that have Medicare
				c.      patient is dilated
				*/

				// OR RV Drawing Done
				if(!empty($dilation)){
				$dilateEye=$row["eyeSide"]; //Dr silverman: it is billing for both eyes (50) when she only dilated the right
				$apOd = $oClsDrw->isAppletModified($row["ophtha_od"]);
				$apOs = $oClsDrw->isAppletModified($row["ophtha_os"]);
				$flgOpthaFirstTime = $oPt->isOpthaFirstTime();

				if(($row["ophthaId"] != NULL) && (($apOd == true) || ($apOs == true)) ){
					//array_search("92225",$arrCptCodes);
					/*
					$tmp = ($flgOpthaFirstTime == 0) ? checkTest4CptDesc("Ophth Ext. Sub.") : checkTest4CptDesc("Ophthalmoscopy");
					$arrExamsDone[] = $tmp;

					if( ($apOd == true) && ($apOs == true) ){
						if(!$flgPtInsMedicare){$arrUserDefinedExamsDone[]=$tmp;}
						$arrModifiersExamsDone[$tmp] = (!$flgPtInsMedicare) ? "OU" : "50";
					}else if($apOd == true){
						$arrModifiersExamsDone[$tmp] = "OD";
					}else if( $apOs == true ){
						$arrModifiersExamsDone[$tmp] = "OS";
					}

					*/

					/*
					$tmpEye="";
					if( ($apOd == true) && ($apOs == true) ){
						$tmpEye = "OU";
					}else if($apOd == true){
						$tmpEye = "OD";
					}else if($apOs == true){
						$tmpEye = "OS";
					}

					$tmp = ($flgOpthaFirstTime == 0) ? $ocpt->checkTest4CptDesc("Ophth Ext. Sub.",$tmpEye) : $ocpt->checkTest4CptDesc("Ophthalmoscopy",$tmpEye);
					$arrExamsDone = array_merge($arrExamsDone,$tmp);
					*/

					/****
					if(count($tmp) > 0){

						foreach($tmp as $key => $val ){
							if( ($apOd == true) && ($apOs == true) ){
								if(!$flgPtInsMedicare){$arrUserDefinedExamsDone[]=$val;}
								$arrModifiersExamsDone[$val] = (!$flgPtInsMedicare) ? "OU" : "50";
							}else if($apOd == true){
								$arrModifiersExamsDone[$val] = "OD";
							}else if( $apOs == true ){
								$arrModifiersExamsDone[$val] = "OS";
							}
						}
					}
					****/

				}else if($row["idRvDrw"] != NULL){ //RV

					$tmpSe = (!empty($row["seRvDrw"]) && (strpos($row["seRvDrw"],"5_Od=1") !== false||strpos($row["seRvDrw"],"5_Os=1") !== false)) ? true : false;

					if($tmpSe == true){
						$oCLSDrawingData = new CLSDrawingData();

						if($oCLSDrawingData->isExamDrawingExits($pid,$form_id,$row["idRvDrw"],"Fundus_Exam")){

							//$tmp = ($flgOpthaFirstTime == 0) ? $ocpt->checkTest4CptDesc("Ophth Ext. Sub.", $dilateEye) : $ocpt->checkTest4CptDesc("Ophthalmoscopy", $dilateEye);
							$tmp = ($row["drawTypeRV"] == 8 || $row["drawTypeRV"] == 9) ? $ocpt->checkTest4CptDesc("Ophthalmoscopy Optic Nerve & Macula","OU") : $ocpt->checkTest4CptDesc("Ophthalmoscopy Retina drawing and scleral depression","OU");
							$arrExamsDone = array_merge($arrExamsDone,$tmp);

							/****
							if(count($tmp) > 0){

								foreach($tmp as $key => $val ){
									if($dilateEye=="OU"){
										if(!$flgPtInsMedicare){$arrUserDefinedExamsDone[]=$val;}
										$arrModifiersExamsDone[$val] = (!$flgPtInsMedicare) ? "OU" : "50";
									}else if($dilateEye=="OD"){
										$arrModifiersExamsDone[$val] = "OD";
									}else if($dilateEye=="OS"){
										$arrModifiersExamsDone[$val] = "OS";
									}
								}
							}
							****/

							/*
							$tmp = ($flgOpthaFirstTime == 0) ? checkTest4CptDesc("Ophth Ext. Sub.") : checkTest4CptDesc("Ophthalmoscopy");
							$arrExamsDone[] = $tmp;
							if($dilateEye=="OU"){
								if(!$flgPtInsMedicare){$arrUserDefinedExamsDone[]=$tmp;}
								$arrModifiersExamsDone[$tmp] = (!$flgPtInsMedicare) ? "OU" : "50";
							}else if($dilateEye=="OD"){
								$arrModifiersExamsDone[$tmp] = "OD";
							}else if($dilateEye=="OS"){
								$arrModifiersExamsDone[$tmp] = "OS";
							}
							*/

						}else{

							$apOd = $oClsDrw->isAppletModified($row["od_drawing"]);
							$apOs = $oClsDrw->isAppletModified($row["os_drawing"]);

							if(($apOd == true || $apOs == true) && $tmpSe==true){
								/*
								//|| !empty($row["wnlDrawOd_RV"]) || !empty($row["wnlDrawOs_RV"])
								$tmp = ($flgOpthaFirstTime == 0) ? checkTest4CptDesc("Ophth Ext. Sub.") : checkTest4CptDesc("Ophthalmoscopy");
								$arrExamsDone[] = $tmp;
								//if( ($apOd == true || !empty($row["wnlDrawOd_RV"])) && ($apOs == true || !empty($row["wnlDrawOs_RV"])) ){
								if( ($apOd == true) && ($apOs == true) ){
									//$arrUserDefinedExamsDone[]=$tmp; //Do not show 2 lines only show 50 modifier
									if(!$flgPtInsMedicare){$arrUserDefinedExamsDone[]=$tmp;}
									$arrModifiersExamsDone[$tmp] = (!$flgPtInsMedicare) ? "OU" : "50";
								}else if($apOd == true){ //|| !empty($row["wnlDrawOd_RV"])
									$arrModifiersExamsDone[$tmp] = "OD";
								}else if( $apOs == true){ // || !empty($row["wnlDrawOs_RV"])
									$arrModifiersExamsDone[$tmp] = "OS";
								}
								*/

								$tmpEye="";
								if( ($apOd == true) && ($apOs == true) ){
									$tmpEye = "OU";
								}else if($apOd == true){
									$tmpEye = "OD";
								}else if($apOs == true){
									$tmpEye = "OS";
								}

								$tmp = ($flgOpthaFirstTime == 0) ? $ocpt->checkTest4CptDesc("Ophth Ext. Sub.", $tmpEye) : $ocpt->checkTest4CptDesc("Ophthalmoscopy", $tmpEye);
								$arrExamsDone = array_merge($arrExamsDone,$tmp);

								/****
								if(count($tmp) > 0){

									foreach($tmp as $key => $val ){
										if( ($apOd == true) && ($apOs == true) ){
											if(!$flgPtInsMedicare){$arrUserDefinedExamsDone[]=$val;}
											$arrModifiersExamsDone[$val] = (!$flgPtInsMedicare) ? "OU" : "50";
										}else if($apOd == true){
											$arrModifiersExamsDone[$val] = "OD";
										}else if( $apOs == true){
											$arrModifiersExamsDone[$val] = "OS";
										}
									}
								}
								****/
							}
						}
					}
				}
				}

				//Ascan
				if($row["surgical_id"] != NULL  ){//&& (!empty($row["signedById"]) || !empty($row["signedByOSId"]))

					//A/Scan
					/*
					$tmp = checkTest4CptDesc("A/Scan");
					$arrExamsDone[] = $tmp;
					*/

					$tmpEye ="";
					if(!empty($row["performedByOD"]) && !empty($row["performedByOS"])){
						$tmpEye ="OU";
					}else if(!empty($row["performedByOD"])){
						$tmpEye ="OD";
					}else if(!empty($row["performedByOS"])){
						$tmpEye ="OS";
					}

					$tmp_Ascan = $ocpt->checkTest4CptDesc("A/Scan", $tmpEye);
					$arrExamsDone = array_merge($arrExamsDone,$tmp_Ascan);

					//units
					/*
					$arrExamsDoneUnits[$tmp] = 2;
					$arrExamsDoneUnits[$tmp_2] = 2;
					*/

					/****
					if(count($tmp_Ascan) > 0){

						foreach($tmp_Ascan as $key => $val){

							//modifier
							if(!empty($insProName) && ($insProName["insPro"] == "Medicare")){

								if(!empty($row["performedByOD"])){
									$arrModifiersExamsDone[$val][] = "RT";
								}

								if(!empty($row["performedByOS"])){
									$arrModifiersExamsDone[$val][] = "LT";
								}

								//Sec
								$arrUserDefinedExamsDone[]=$val;

							}else{

								$arrModifiersExamsDone[$val][] = "26";
								$arrModifiersExamsDone[$val][] = "50";

								//Sec
								$arrUserDefinedExamsDone[]=$val;

							}
						}
					}
					****/

					/*
					//modifier
					if(!empty($insProName) && ($insProName["insPro"] == "Medicare")){

						if(!empty($row["performedByOD"])){
							$arrModifiersExamsDone[$tmp][] = "RT";
						}

						if(!empty($row["performedByOS"])){
							$arrModifiersExamsDone[$tmp][] = "LT";
						}

						//Sec
						$arrUserDefinedExamsDone[]=$tmp;

					}else{

						$arrModifiersExamsDone[$tmp][] = "26";
						$arrModifiersExamsDone[$tmp][] = "50";

						//Sec
						$arrUserDefinedExamsDone[]=$tmp;

					}
					*/

				}

				// iOLMaster
				if($row["iol_master_id"] != NULL ){//&& (!empty($row["signedById_OD_IOLM"]) || !empty($row["signedById_OS_IOLM"]))

					//iOLmaster
					/*
					$tmp_2 = checkTest4CptDesc("iOLMaster");
					$arrExamsDone[] = $tmp_2;
					*/

					$tmpEye ="";
					if(!empty($row["performedByOD_IOLM"]) && !empty($row["performedByOS_IOLM"])){
						$tmpEye ="OU";
					}else if(!empty($row["performedByOD_IOLM"])){
						$tmpEye ="OD";
					}else if(!empty($row["performedByOS_IOLM"])){
						$tmpEye ="OS";
					}

					$tmp_IOLM = $ocpt->checkTest4CptDesc("IOL Master", $tmpEye);
					$arrExamsDone = array_merge($arrExamsDone,$tmp_IOLM);

					//units
					//May 9
					/*
					$arrExamsDoneUnits[$tmp] = 2;
					$arrExamsDoneUnits[$tmp_2] = 2;
					*/

					/****
					if(count($tmp_IOLM) > 0){

						foreach($tmp_IOLM as $key => $val){

							//modifier
							if(!empty($insProName) && ($insProName["insPro"] == "Medicare")){

								if(!empty($row["performedByOD_IOLM"])){
									$arrModifiersExamsDone[$val][] = "RT";
								}

								if(!empty($row["performedByOS_IOLM"])){
									$arrModifiersExamsDone[$val][] = "LT";
								}

								//Sec
								$arrUserDefinedExamsDone[]=$val;

							}else{

								$arrModifiersExamsDone[$val][] = "26";
								$arrModifiersExamsDone[$val][] = "50";

								//Sec
								$arrUserDefinedExamsDone[]=$val;

							}

						}
					}
					****/

					/*
					//modifier
					if(!empty($insProName) && ($insProName["insPro"] == "Medicare")){

						if(!empty($row["performedByOD_IOLM"])){
							$arrModifiersExamsDone[$tmp_2][] = "RT";
						}

						if(!empty($row["performedByOS_IOLM"])){
							$arrModifiersExamsDone[$tmp_2][] = "LT";
						}

						//Sec
						$arrUserDefinedExamsDone[]=$tmp_2;

					}else{

						$arrModifiersExamsDone[$tmp_2][] = "26";
						$arrModifiersExamsDone[$tmp_2][] = "50";

						//Sec
						$arrUserDefinedExamsDone[]=$tmp_2;

					}
					*/
				}

				//Cell Count
				//CellCount : //Check Template
				// if Test Cell Count is Done or Current Chart note is of Template named Cell Count
				if((!empty($row["test_cellcnt_id"]) ) || $oCN->isChartCellCount()){	//&& !empty($row["phyNameCellCount"])
					/*
					$tmp = checkTest4CptDesc("Cell Count");
					$arrExamsDone[] = $tmp;
					*/

					$tmp = $ocpt->checkTest4CptDesc("Cell Count",$row["test_cellcnt_eye"]);
					$arrExamsDone = array_merge($arrExamsDone,$tmp);

				}


				//Bscan
				if((!empty($row["test_bscan_id"]) )){
					$tmp = $ocpt->checkTest4CptDesc("B-Scan",$row["test_bscan_eye"]);
					$arrExamsDone = array_merge($arrExamsDone,$tmp);
				}

				//icg
				if((!empty($row["icgId"]) )){
					$test_eye_icg = trim($row["icg_od"]);
					if($test_eye_icg==1){ $test_eye_icg="OU"; }elseif($test_eye_icg==2){ $test_eye_icg="OD";}elseif($test_eye_icg==3){ $test_eye_icgs="OS";}
					$tmp = $ocpt->checkTest4CptDesc("ICG", $test_eye_icg);
					$arrExamsDone = array_merge($arrExamsDone,$tmp);
				}

				//check Testsfrom Template
				$oPtTest = new PtTest($pid,$form_id);
				$ar_pt_tests = $oPtTest->getTests_Other($dos_ymd);
				if(count($ar_pt_tests) > 0){
					foreach($ar_pt_tests as $key => $ar_pt_test_info){
						if(!empty($ar_pt_test_info[0]) && !empty($ar_pt_test_info[1])){
							$tmp = $ocpt->checkTest4CptDesc($ar_pt_test_info[0], $ar_pt_test_info[1]);
							$arrExamsDone = array_merge($arrExamsDone,$tmp);
						}
					}
				}

				//$arrExamsDone_TT =getCPTsFromTestTemplate($pid,$form_id,$dos_ymd);

				//Check Contact Lens Fit/Refit/Eva.
				/*
				$arCL = checkCL_FitRefitEva($pid,$dos_ymd);
				if($arCL!=false && $arCL[0]==true){
					/*
					$tmp = checkTest4CptDesc("Contact Lens Fitting");//92310
					$arrExamsDone[] = $tmp;
					*-/

					$tmp = checkTest4CptDesc("Contact Lens Fitting", $arCL[1]);
					$arrExamsDone = array_merge($arrExamsDone,$tmp);

				}
				*/

				list($cl_ttl_prc, $cl_ar_cpt, $cl_str_cpt) = $this->checkCL_FitRefitEva_v2($dos_ymd);
				if(count($cl_ar_cpt)>0){	$arrExamsDone = array_merge($arrExamsDone, $cl_ar_cpt); }

				//Check Contact Lens Fit/Refit/Eva.

				//Get APPolicy CPTS
				$lenApCpt = count($arrApCpt);
				if($lenApCpt>0){

					foreach($arrApCpt as $keyCpt=>$valDx){

						if(!empty($keyCpt)){
						//Check So that Dupicate CPT can't enter
						if(!in_array($keyCpt,$arrExamsDone)){
							$arrExamsDone[] = $keyCpt;
						}

						}

					}
				}
				//Get APPolicy CPTS

				//$str .="<error>".$msg112."</error>";

				$str .="<allcptcode>";

				$arrSelectedProcs = array();
				foreach($arrExamsDone as $var => $val)
				{
					$practice_code = $val;
					//$tmp = array_search($val,$arrCptCodes);
					$tmp = arr_search_2dim($val, $arrCptCodes);
					if($tmp !== false)
					{
						$val = $tmp;
					}

					if(array_search($val,$arrDesc) !== false)
					{
						/*//check duplicate
						if(in_array($val, $arrSelectedProcs)){
							continue;
						}*/

						$str .="<cptcodeinfo>";
						$desc = $arrSelectedProcs[] = $val;
						$val2var = str_replace(" ","_",$desc);
						$tempCpt = (in_array($practice_code, $arrPracticCptCodes)) ? $practice_code : $ocpt->getProcedureCode($desc);

						if(!empty($tempCpt)){
							//DefMod+unit
							$arrCptDefVal = $ocpt->getCptDefVals($tempCpt);

							//Test units
							$units = "1";
							if(!empty($arrCptDefVal["units"])) $units = $arrCptDefVal["units"];//Default set in admin

							$tmp = array_key_exists($tempCpt,$arrExamsDoneUnits);
							if($tmp != false)
							{
								$units = $arrExamsDoneUnits[$tempCpt];
							}else{
								$tmp = array_key_exists($desc,$arrExamsDoneUnits);
								if($tmp != false){
									$units = $arrExamsDoneUnits[$desc];
								}
							}

							//if Refraction Code
							$isRef="";
							if($ocpt->mkCPTCode($tempCpt) == 92015){
								$isRef=" isRef=\"1\" ";
							}

							//valid_dx
							$w_valid_dx_code="";
							if(!empty($arrCptDefVal["dx_codes"])){ $w_valid_dx_code=" valid_dxcodes=\"".$arrCptDefVal["dx_codes"]."\" "; }


							//if ascan / iOLMaster
							//May 9
							/*
							if( $tempCpt == checkTest4CptDesc("A/Scan") || $desc == checkTest4CptDesc("A/Scan") ||
								$tempCpt == checkTest4CptDesc("iOLMaster") || $desc == checkTest4CptDesc("iOLMaster") ){
								$units = 2;
							}
							*/
							//Test units

							$arrSelectedCpt[] = $tempCpt;
							$tmp_desc_attr = (!empty($desc)) ? htmlentities($desc, ENT_QUOTES) : "";
							$str .="<cptcode desc=\"".$tmp_desc_attr."\" ".$isRef." ".$w_valid_dx_code." >".htmlentities($tempCpt, ENT_QUOTES)."</cptcode>";
							$str .="<units>".$units."</units>";

							//Modifiers
							$tmp_arr_mod=array();
							if(array_key_exists($tempCpt,$arrModifiersExamsDone)||array_key_exists($desc,$arrModifiersExamsDone))
							{
								$eyeCode="";
								$eyeCode=$arrModifiersExamsDone[$tempCpt];
								if(empty($eyeCode))$eyeCode=$arrModifiersExamsDone[$desc];

								if(is_array($eyeCode)){
									$tmp_arr_mod=array_merge($tmp_arr_mod,$eyeCode);
									/*
									$lnEyeCd = count($eyeCode);
									if($lnEyeCd > 0){
										for($iter=0;$iter<$lnEyeCd;$iter++){
											$str .="<modifier".($iter+1).">".$eyeCode[$iter]."</modifier".($iter+1).">";
										}
									}*/

								}else if($eyeCode == "OU"){
									//$str .="<modifier1>LT</modifier1>";
									$tmp_arr_mod[]="LT";
								}else if(($eyeCode == "OD")) //OD
								{
									//$str .="<modifier1>LT</modifier1>";
									$tmp_arr_mod[]="RT";
								}
								elseif($eyeCode == "OS") //OS
								{
									//$str .="<modifier1>RT</modifier1>";
									$tmp_arr_mod[]="LT";
								}
								else if(!empty($eyeCode)){
									//$str .="<modifier1>".$eyeCode."</modifier1>";
									$tmp_arr_mod[]="".$eyeCode;
								}
							}

							//Add default mods
							if(is_array($arrCptDefVal["mod"]) && count($arrCptDefVal["mod"])>0){
								$tmp_arr_mod = array_merge($tmp_arr_mod,$arrCptDefVal["mod"]);
							}

							$lnTam = count($tmp_arr_mod);
							if($lnTam > 0){
								for($iter=0;$iter<$lnTam&&$iter<4;$iter++){
									$str .="<modifier".($iter+1).">".$tmp_arr_mod[$iter]."</modifier".($iter+1).">";
								}
							}
							//Modifiers--
							//Dx
							$iDx = 1;
							if(array_key_exists($tempCpt,$arrDxExamsDone)||array_key_exists($desc,$arrDxExamsDone)){
								$tDxCode = $arrDxExamsDone[$tempCpt];
								if(empty($tDxCode)) $tDxCode = $arrDxExamsDone[$desc];
								if(!empty($tDxCode)){
									$tDxDesc = $odx->getDxTableInfo($tDxCode, $elem_v_icd10);
									$tDxDesc = ($tDxDesc !== false) ? htmlentities($tDxDesc, ENT_QUOTES) : "";
									$str .="<dx".$iDx." desc=\"".$tDxDesc."\">".$tDxCode."</dx".$iDx.">";
									$iDx++;
								}
							}

							//Dx 2 Pass
							if(isset($arrApCpt[$tempCpt])){
								$lenApCpt = count($arrApCpt[$tempCpt]);
								if($lenApCpt > 0){
									$iDx = 1;
									foreach($arrApCpt[$tempCpt] as $ki => $vi){
										$tDxCode = $vi;
										$tDxDesc = $odx->getDxTableInfo($tDxCode, $elem_v_icd10);
										$tDxDesc = ($tDxDesc !== false) ? htmlentities($tDxDesc, ENT_QUOTES) : "";
										$str .="<adx".$iDx." desc=\"".$tDxDesc."\">".$tDxCode."</adx".$iDx.">";
										$iDx++;
									}
								}
							}

							/*
							if($lenApCpt > 0){
								foreach($arrApCpt as $kpc => $vpc){
									if(in_array($val,$vpc)){
										$tDxCode = $kpc;
										$tDxDesc = getDxTableInfo($tDxCode);
										$tDxDesc = ($tDxDesc !== false) ? htmlentities($tDxDesc, ENT_QUOTES) : "";
										$str .="<dx".$iDx." desc=\"".$tDxDesc."\">".$tDxCode."</dx".$iDx.">";
										$iDx++;
									}
								}
							}
							*/

						}
						$str .="</cptcodeinfo>";
					}
				}
				//Test

				//*//echo "HELLO\n";
				//print_r($arrModifiersExamsDone);
				//print_r($arrExamsDone);
				//print_r($arrUserDefinedExamsDone);
				//print_r($str);
				//print_r($arrDxExamsDone);
				//echo "\nHELLO";
				//exit; //*/

				/****
				//User Defined
				$lenUserDefinedExamsDone = count($arrUserDefinedExamsDone);
				if($lenUserDefinedExamsDone > 0)
				{
					for($i=0,$j=0;$i<$lenUserDefinedExamsDone;$i++)
					{
						$procedureName = $arrUserDefinedExamsDone[$i];

						//Test
						$tmp = array_search($procedureName,$arrCptCodes);
						if($tmp !== false)
						{
							$procedureName = $tmp;
						}
						//Test

						if(array_search($procedureName,$arrDesc) !== false)
						{
							/* //check duplicate
							if(in_array($procedureName, $arrSelectedProcs)){
								continue;
							}* /

							$str .="<cptcodeinfo>";
							$desc = $arrSelectedProcs[] = "User Defined".(++$j);
							$cptCode = $arrSelectedCpt[] = getProcedureCode($procedureName);
							$val2var = str_replace(" ","_",$desc);
							//$elem_Desc = "elem_Desc_".$val2var;
							//$elem_units = "elem_units_".$val2var;
							//$elem_ProceName = "elem_Text_".$val2var;
							//$elem_CptCode = "elem_CptCode_".$val2var;
							//$arrDescEdit[$desc][$elem_Desc] = $desc;
							//$arrDescEdit[$desc][$elem_ProceName] = $procedureName;
							//$arrDescEdit[$desc][$elem_CptCode] = $cptCode;

							//DefMod+unit
							$arrCptDefVal = getCptDefVals($cptCode);

							//Test units
							$units = "1";
							if(!empty($arrCptDefVal["units"])) $units = $arrCptDefVal["units"];//Default set in admin

							$tmp = array_key_exists($cptCode,$arrExamsDoneUnits);
							if($tmp != false)
							{
								$units = $arrExamsDoneUnits[$cptCode];
							}else{
								$tmp = array_key_exists($desc,$arrExamsDoneUnits);
								if($tmp != false){
									$units = $arrExamsDoneUnits[$desc];
								}
							}

							//if Refraction Code
							$isRef="";
							if(mkCPTCode($cptCode) == 92015){
								$isRef=" isRef=\"1\" ";
							}

							//if ascan / iOLMaster
							/*
							if( $cptCode == checkTest4CptDesc("A/Scan") || $procedureName == checkTest4CptDesc("A/Scan") ||
								$cptCode == checkTest4CptDesc("iOLMaster") || $procedureName == checkTest4CptDesc("iOLMaster") ){
								$units = 1;
							}
							* /
							//if ascan / iOLMaster

							///$arrDescEdit[$desc][$elem_units] = $units;
							//Test units

							$str .="<cptcode desc=\"".$procedureName."\" ".$isRef." >".$cptCode."</cptcode>";
							$str .="<units>".$units."</units>";

							//Modifiers
							$tmp_arr_mod=array();
							if(array_key_exists($cptCode,$arrModifiersExamsDone) || array_key_exists($procedureName,$arrModifiersExamsDone))
							{
								$eyeCode=$arrModifiersExamsDone[$cptCode];
								if(empty($eyeCode)) $eyeCode=$arrModifiersExamsDone[$procedureName];
								/*
								if($cptCode == checkTest4CptDesc("A/Scan") || $procedureName == checkTest4CptDesc("A/Scan") ||
									$cptCode == checkTest4CptDesc("IOL Master") || $procedureName == checkTest4CptDesc("IOL Master")){
								* /

								if( in_array($cptCode, $tmp_Ascan) || in_array($procedureName, $tmp_Ascan) ||
									in_array($cptCode, $tmp_IOLM) || in_array($procedureName, $tmp_IOLM)  ){
									//$str .="<modifier1>TC</modifier1>";
									$tmp_arr_mod[]="TC";

								}else if(is_array($eyeCode)){
									$tmp_arr_mod=array_merge($tmp_arr_mod,$eyeCode);
									/*
									$lnEyeCd = count($eyeCode);
									if($lnEyeCd > 0){
										for($iter=0;$iter<$lnEyeCd;$iter++){
											$str .="<modifier".($iter+1).">".$eyeCode[$iter]."</modifier".($iter+1).">";
										}
									}* /

								}else if($eyeCode == "OU") //OU
								{
									//$str .="<modifier1>RT</modifier1>";
									$tmp_arr_mod[]="RT";
								}
							}

							//Add default mods
							if(count($arrCptDefVal["mod"])>0){
								$tmp_arr_mod = array_merge($tmp_arr_mod,$arrCptDefVal["mod"]);
							}

							$lnTam = count($tmp_arr_mod);
							if($lnTam > 0){
								for($iter=0;$iter<$lnTam&&$iter<4;$iter++){
									$str .="<modifier".($iter+1).">".$tmp_arr_mod[$iter]."</modifier".($iter+1).">";
								}
							}

							//Modifiers

							//Dx 2 Pass

							if(isset($arrApCpt[$cptCode])){
								$lenApCpt = count($arrApCpt[$cptCode]);
								if($lenApCpt > 0){
									$iDx = 1;
									foreach($arrApCpt[$cptCode] as $ki => $vi){
										$tDxCode = $vi;
										$tDxDesc = getDxTableInfo($tDxCode);
										$tDxDesc = ($tDxDesc !== false) ? htmlentities($tDxDesc, ENT_QUOTES) : "";
										$str .="<adx".$iDx." desc=\"".$tDxDesc."\">".$tDxCode."</adx".$iDx.">";
										$iDx++;
									}
								}
							}

							$str .="</cptcodeinfo>";
						}
					}
				}
				****/

				$str .="</allcptcode>";


				//POE Visit Code
				//Visit Code
				$poe_visit_code = "";
				//$oPoe = new Poe($pid);
				if($post_op_visit=="1"){
					$poe_visit_code = (!empty($GLOBALS["cpt_code_poe"])) ? $GLOBALS["cpt_code_poe"] : "999";
					$poe_visit_code = $ocpt->mkCPTCode($poe_visit_code,"Prac");
				}
				//POE Visit Code


				//Practice Code
				if(isset($practiceBillCode) && !empty($practiceBillCode)){

					if(empty($poe_visit_code)&&$proc_only_visit!="1"){ //check if POE is not set and  not procedure only visit
						include(dirname(__FILE__)."/process_visit_code.php");
					}

					//is Not Consult Patient
					if($patientCategory != "Consult")
					{

					    if($elem_flgIsPtNew=="No_confirmed"){
						$patientCategory = "Establish";
					    }else if($elem_flgIsPtNew=="Yes_confirmed"){
						$patientCategory = "New";
					    }else if($oSB->isPatientEstablish($form_id)){
						$patientCategory = "Establish";
					    }
					    else
					    {
					       $patientCategory = "New";
					    }
					}else{

					    if($elem_flgIsPtNew=="No_confirmed"){
						$patientCategory = "Establish";
					    }else if($elem_flgIsPtNew=="Yes_confirmed"){
						$patientCategory = "New";
					    }else if($oSB->isPatientEstablish($form_id)){ //||$elem_flgIsPtNew=="No_confirmed"
						//if consults: get pt cat for eye code
						$patientCategory_4consult = "Establish";
					    }
					    else
					    {
					       $patientCategory_4consult = "New";
					    }

					}

					$str .="<autocode>";
						$str .="<ptcategory>".$patientCategory."</ptcategory>";
						$str .="<ptservicelevel>".$patientLevelofService."</ptservicelevel>";
						$str .="<practicebillcode>".$practiceBillCode."</practicebillcode>";
						$str .= "<ptlevelhistory>".$levelHistory."</ptlevelhistory>";
						$str .= "<ptlevelexam>".$levelExam."</ptlevelexam>";
						$str .= "<ptlevelcomp>".$levelComplexity."</ptlevelcomp>";
						$str .= "<ptcategory_consult>".$patientCategory_4consult."</ptcategory_consult>";
						//Visit Code
						$str .="<pt_levelofservice_eye>".$patientLevelofServiceEye."</pt_levelofservice_eye>";
						$str .="<pt_levelofservice_em>".$patientLevelofServiceEM."</pt_levelofservice_em>";
						$str .="<pt_strexamdone_eye>".$strExamDone_eye."</pt_strexamdone_eye>";
						$str .="<pt_strexamnotdone_eye>".$strExamNotDone_eye."</pt_strexamnotdone_eye>";
						$str .="<pt_strexamdone_em>".$strExamDone_em."</pt_strexamdone_em>";
						$str .="<pt_strexamnotdone_em>".$strExamNotDone_em."</pt_strexamnotdone_em>";
						$str .="<poe_visit_code>".$poe_visit_code."</poe_visit_code>";
						$str .= "<pls_sel_nq_em>".$nxtLevelOfServiceEM_find."</pls_sel_nq_em>"; ///Pat level of service selected but not qualified
						$str .= "<pls_sel_nq_eye>".$nxtLevelOfServiceEye_find."</pls_sel_nq_eye>"; ///Pat level of service selected but not qualified
						//Visit Code
					$str .="</autocode>";
				}
				//

			}
		}

		//PQRI
		$str .="<eleminfo>";
		for($i=1,$j=1;$i<=12;$i++,$j++){

			$chkDxCd = xss_rem($_POST["elem_dxCode".$i]);

			if(isset($chkDxCd) && !empty($chkDxCd)){

				$str .= "<elem>";

				$dxCode = "";
				$sql = "SELECT dx_code,pqriCode,diag_description FROM diagnosis_code_tbl ".
					 "WHERE dx_code='".$chkDxCd."' || d_prac_code='".$chkDxCd."' || ".
					 "REPLACE(diag_description,'\r\n','')='".$chkDxCd."' ";
				$row = sqlQuery($sql);

				if($row != false){
					$dxCode = $row["dx_code"];
					$pqriCode = trim($row["pqriCode"]);
					$dxCodeDesc = trim($row["diag_description"]);

					$tmp_desc_attr = (!empty($dxCodeDesc)) ? htmlentities($dxCodeDesc, ENT_QUOTES) : "";
					$str .= "<dxcode desc=\"".$tmp_desc_attr."\">".$dxCode."</dxcode>";

					$isMediCared = "false";
					$arrPqriCode = array();
					//Check Medicare Insurance
					if($flgPtInsMedicare || !empty($GLOBALS["PQRIforAllClaims"])){
						//Check Associated PQRI codes
						if(!empty($pqriCode)){
							$arrPqriCodeTmp = explode(",",$pqriCode);
							$len = count($arrPqriCodeTmp);

							///echo "PQRI: ".$pqriCode.", Length: ".$len;

							//Check Pqri Code
							for($ka=0;$ka<$len;$ka++){
								$tmpCode = trim($arrPqriCodeTmp[$ka]);
								$tmpCode = $ocpt->checkPqriCode($tmpCode);
								if(!empty($tmpCode)){
									$arrPqriCode[]=$tmpCode;
								}
							}
						}
						$isMediCared = "true";

					}

					$str .="<medicare>".$isMediCared."</medicare>";
					$str .="<checkAsscPqri>".implode(",",$arrPqriCode)."</checkAsscPqri>";

					$arrValidPqriCode = array();
					if(count($arrPqriCode) > 0){
						//Check if PQRI Code has been billed in this calendar year
						foreach($arrPqriCode as $key => $val){
							$temp = $this->isPqriCodeBilled($val);
							if(!$temp){
								$arrValidPqriCode[]=$val;
							}
						}
					}

					$str .="<checkNotBilledPqri>".implode(",",$arrValidPqriCode)."</checkNotBilledPqri>";

					$str .= "<pqri>";
					if(count($arrValidPqriCode) > 0){
						foreach($arrValidPqriCode as $key => $val){
							$str .= "<cptcd>".$val."</cptcd>";
						}
					}
					$str .= "</pqri>";

				}

				$str .= "</elem>";

			}
		}
		$str .="</eleminfo>";
		//PQRI End


		// Get CL Order Supply Total Value
		//$tmp = check4CLOrder($pid,$dos_ymd);
		$tmp =0;
		if(count($cl_ar_cpt)>0){  $tmp =$cl_ttl_prc;  }
		if(!empty($tmp)){
			$str .="<cl_supply_total>".$tmp."</cl_supply_total>";
		}
		// Get CL Order Supply Total Value

		//Get Testing Cpts For Merging
		if(!empty($dos_ymd) && !empty($_POST["flgMergeSB"])){
			$str .=$this->getTestSbXml($dos_ymd, $elem_v_icd10);
		}
		//Get Testing Cpts For Merging

		//Get CPTs From Procedure Notes --
			$str .= $this->getProcNoteSbXml($elem_v_icd10);
		//Get CPTs From Procedure Notes --

		$str .= "</dxcodeinfo>";
		$str = xml_safe($str);
		echo $str;

	}

	//Get DOS in YMD
	// make xml for inserting to main super bill
	function getTestSbXml($dos,$icd10){

		$pid = $this->pid;
		$fid = $this->fid;
		$ocpt = new CPT();
		$odx = new Dx();

		$str = "<testing_sb>";

		$str_fid = "";
		if(!empty($fid)){
			$str_fid = "OR formId='".$fid."' ";
		}

		//Check Super bill
		$sql = "SELECT idSuperBill, test_id, test_name, arr_dx_codes FROM superbill
				WHERE patientId='".$pid."' AND (dateOfService='".$dos."' ".$str_fid." )
				AND test_id != '' AND test_name != '' AND postedStatus='0' and del_status='0'  ORDER BY formId DESC, dateOfService DESC ";

		$rez = sqlStatement($sql);
		for($i=1;$row=sqlFetchArray($rez);$i++){
			$idSuperBill = $row["idSuperBill"];


			$test_id = $row["test_id"];
			$test_name = $row["test_name"];
			$test_ar_dx_codes = !empty($row["arr_dx_codes"]) ? unserialize($row["arr_dx_codes"]) : array();

			//Check for Procedures and do not merge with wv
			if($test_name=="Procedures"){continue;}
			//Check for test interpretation --
			/*
			if(!isTestInterpreted($test_id, $test_name)){
				continue;
			}
			*/

			//Check for test interpretation--

			$cpt_info="";
			$sql = "SELECT * FROM procedureinfo
					WHERE idSuperBill='".$idSuperBill."'
					and delete_status = '0'
					";
			$rez2 = sqlStatement($sql);
			for($j=1;$row2=sqlFetchArray($rez2);$j++){

				$desc = htmlentities($row2["procedureName"],ENT_QUOTES);

				//if Refraction Code
				$isRef="";
				if($ocpt->mkCPTCode($row2["cptCode"]) == 92015){
					$isRef=" isRef=\"1\" ";
				}
				//valid_dx
				$arrCptDefVal = $ocpt->getCptDefVals($row2["cptCode"]);
				$w_valid_dx_code="";
				if(!empty($arrCptDefVal["dx_codes"])){ $w_valid_dx_code=" valid_dxcodes=\"".$arrCptDefVal["dx_codes"]."\" "; }

				$cptCode_show = htmlentities($row2["cptCode"],ENT_QUOTES);
				$cpt_info .= "<cpt>";
				$cpt_info .= "<code desc=\"".$desc."\" units=\"".$row2["units"]."\" ".$isRef." ".$w_valid_dx_code." >".$cptCode_show."</code>";

				for($k=1;$k<=12;$k++){
					$tDxCode = $row2["dx".$k];
					if(!empty($tDxCode)){

						$dxphrse = $tdxId = "";
						if(isset($test_ar_dx_codes[$k]) && !empty($test_ar_dx_codes[$k]) && strpos($test_ar_dx_codes[$k],"@*@")!==false){
							list($tdx, $tdxId) = explode("@*@", $test_ar_dx_codes[$k]);
							if(!empty($tdx)){
								$tDxCode = $tdx;
								if(!empty($tdxId)){
									$dxphrse = " dxid=\"".$tdxId."\" ";
								}
							}
						}

						$tDxDesc = $odx->getDxTableInfo($tDxCode, $icd10, $tdxId, 1);
						$tDxDesc = htmlentities($tDxDesc,ENT_QUOTES);
						$cpt_info .="<dx ".$dxphrse." num=\"".$k."\" desc=\"".$tDxDesc."\">".$tDxCode."</dx>";
					}

					$tModCode = $row2["modifier".$k];
					if(!empty($tModCode)){
						$tModCode = htmlentities($tModCode,ENT_QUOTES);
						$cpt_info .="<modifier num=\"".$k."\" >".$tModCode."</modifier>";
					}
				}

				$cpt_info .= "</cpt>";
			}

			$str .= "<sb id=\"".$idSuperBill."\">".
					$cpt_info.
					"</sb>";
		}

		$str .= "</testing_sb>";

		return $str;
	}

	//procedure notes ---

	function getProcNoteSbXml($vIcd10){
		$patient_id = $this->pid;
		$form_id = $this->fid;

		$ocpt = new CPT();
		$odx = new Dx();

		$str="";
		$dx_info=$cpt_info_all="";

		$sql = "SELECT c1.id, c1.cpt_code, c1.dx_code, c1.cpt_mod, c1.site, c1.corr_site, c1.lids_opts, ".
				" c2.procedure_name, c3.btx_total, c3.btx_usd, c3.btx_wstd, c3.type_btx  FROM chart_procedures c1 ".
				"LEFT JOIN operative_procedures c2 ON c2.procedure_id = c1.proc_id ".
				"LEFT JOIN chart_procedures_botox c3 ON c3.chart_proc_id = c1.id ".
				"WHERE patient_id = '".$patient_id."' AND form_id = '".$form_id."' AND deleted_by='0' AND encounter_id = '0' ";
		$rez = sqlStatement($sql);
		for($j=0;$row=sqlFetchArray($rez);$j++){

			$row["cpt_code"]=trim($row["cpt_code"]);
			if(empty($row["cpt_code"])){  continue; }

			if(strpos($row["cpt_code"],"|~|")!==false){
				$arr_cpt_code = explode("|~|", $row["cpt_code"]);
				$arr_cpt_code = array_map('trim', $arr_cpt_code);
				$arr_cpt_code = array_filter($arr_cpt_code);
				$arr_cpt_code = array_values($arr_cpt_code);

			}else if(strpos($row["cpt_code"],";")!==false){
				$arr_cpt_code =  preg_split( "/(;|,)/", $row["cpt_code"] ); //explode(";", $row["cpt_code"]);
				$arr_cpt_code = array_map('trim', $arr_cpt_code);
				$arr_cpt_code = array_filter($arr_cpt_code);
				$arr_cpt_code = array_values($arr_cpt_code);
			}else{
				$arr_cpt_code = explode(",", $row["cpt_code"]);
			}

			$dx_code = $row["dx_code"];
			$cpt_mod = $row["cpt_mod"];

			$lit_site = trim($row["site"]);
			if(empty($lit_site)){$lit_site = trim($row["corr_site"]);}

			$lids_opts = trim($row["lids_opts"]);

			$btx_total="";$btx_usd="";$btx_wstd="";$type_btx="";
			$proc_nm = strtolower($row["procedure_name"]);
			if(strpos($proc_nm,"botox")!==false && !empty($row["type_btx"])){
				$type_btx=$row["type_btx"];
				if($row["type_btx"]=="Medical" && !empty($row["btx_total"])){
					$btx_total=$row["btx_total"];
					$btx_usd=round($row["btx_usd"]);
					$btx_wstd=$btx_total-$btx_usd;
					$btx_wstd=round($btx_wstd);
				}
				if($row["type_btx"]=="Cosmetic" && !empty($row["btx_usd"])){  $btx_usd=$row["btx_usd"];  }
			}

			$len_cc = count($arr_cpt_code);

			for($i=0; $i<$len_cc; $i++){

			$cpt_code = trim($arr_cpt_code[$i]);

			$flgDx=0;
			$dxindx = 0;

			if(!empty($cpt_code)){
				//$arrCpt[] = $cpt_code;
				$sql = "SELECT mod1,mod2,mod3,units,cpt_desc,cpt_prac_code,cpt4_code,dx_codes FROM cpt_fee_tbl
						WHERE (cpt_prac_code='".$cpt_code."' OR cpt4_code='".$cpt_code."')  AND delete_status != '1' ";

				$row2=sqlQuery($sql);
				if($row2!=false){

					$desc = filter_var($desc, FILTER_SANITIZE_STRING); //removes
					//$desc = htmlentities($desc,ENT_QUOTES);

					//if Refraction Code
					$isRef="";
					if($ocpt->mkCPTCode($row2["cpt4_code"]) == 92015){
						$isRef=" isRef=\"1\" ";
					}

					//unit
					$unit = (!empty($row2["units"])) ? $row2["units"] : 1;
					//check cpt with J , set units for botox --
					if(preg_match('/^j/i' , $row2["cpt_prac_code"]) && !empty($type_btx)){
						if($type_btx == "Medical" && !empty($btx_usd)){ $unit =	$btx_usd;}
						elseif($type_btx == "Cosmetic" && !empty($btx_usd)){ $unit =	$btx_usd; }
					}
					//--

					//valid_dx
					$w_valid_dx_code="";
					if(!empty($row2["dx_codes"])){ $w_valid_dx_code=" valid_dxcodes=\"".$row2["dx_codes"]."\" "; }

					$cpt_info = "";

					$cpt_info .= "<cpt>";
					$cpt_info .= "<code desc=\"".$desc."\" units=\"".$unit."\" ".$isRef." ".$w_valid_dx_code." >".$row2["cpt_prac_code"]."</code>";

					// Loop DX Codes --

					if(!empty($dx_code)){

						//--
						if(strpos($dx_code,";")!==false){
							$dx_code = str_replace(",","!COMMA!",$dx_code);
							$arr_dx_code = preg_split( "/;/", $dx_code ); //explode(";", $dx_code);
							$arr_dx_code = array_map(function($v){ $v = str_replace("!COMMA!", ",", $v); return trim($v); }, $arr_dx_code);
							$arr_dx_code = array_filter($arr_dx_code);
							$arr_dx_code = array_values($arr_dx_code);
						}else{
							$arr_dx_code = explode(",", $dx_code);
						}

						if(count($arr_dx_code) > 0){
							foreach($arr_dx_code as $key_dx => $val_dx){
								$val_dx=trim($val_dx);
								if(!empty($val_dx)){
									/*
									//check $val_dx conatain , or not (Lids)
									$ar_val_dx = explode(",", $val_dx);
									if(count($ar_val_dx) > 1){ //if multiple dx codes

										foreach($ar_val_dx as $k_vd => $v_vd){

											if(!empty($v_vd)){
												$dxindx = $dxindx + 1;

												$ar_tmp = explode(" - ",$v_vd);
												$v_vd=$ar_tmp[0];
												$tDxDesc=trim($ar_tmp[1]);
												//
												if(empty($tDxDesc)){
													$tDxDesc = $odx->getDxTableInfo($v_vd,$vIcd10);
													$tDxDesc = htmlentities($tDxDesc,ENT_QUOTES);
												}

												if(!empty($tDxDesc) && !empty($v_vd)){

													if($vIcd10=="1"){ //convert to icd10
														$val_dx_tmp=$odx->convertICDDxCode($v_vd, 9, 1);
														if(!empty($val_dx_tmp)){ $v_vd=$val_dx_tmp;  }
														if(!empty($lit_site)||!empty($lids_opts)){ $v_vd=$odx->modifyICDDxCodeWEye($v_vd, $lit_site, $lids_opts); }
													}

													$cpt_info .="<dx num=\"".$dxindx."\" desc=\"".$tDxDesc."\">".$v_vd."</dx>";
												}

											}

										}

									}else{ // if signle dx code
									*/
										$dxindx = $dxindx + 1;

										$ar_tmp = explode(" - ",$val_dx);
										$val_dx=$ar_tmp[0];
										$tDxDesc=trim($ar_tmp[1]);
										$id_dx=$dx_phrs="";
										list($tmp,$tmpId) = explode("@~@", $tDxDesc);
										if(!empty($tmp)){
											$tDxDesc = "";// trim($tmp); //Get Description from dx code
											if(!empty($tmpId)){
											$id_dx = $tmpId;
											$dx_phrs = " dxid=\"".$id_dx."\" ";
											}
										}

										if(!empty($val_dx)){

											if($vIcd10=="1"){ //convert to icd10
												$val_dx_tmp=$odx->convertICDDxCode($val_dx, 9, 1);
												if(!empty($val_dx_tmp)){ $val_dx=$val_dx_tmp;  }
												if(!empty($lit_site)||!empty($lids_opts)){  $val_dx=$odx->modifyICDDxCodeWEye($val_dx, $lit_site, $lids_opts); }
											}

											//
											if(empty($tDxDesc)){
												$tDxDesc = $odx->getDxTableInfo($val_dx,$vIcd10,$id_dx,1);
												$tDxDesc = htmlentities($tDxDesc,ENT_QUOTES);
											}

											if(!empty($tDxDesc) && !empty($val_dx)){
												$cpt_info .="<dx ".$dx_phrs." num=\"".$dxindx."\" desc=\"".$tDxDesc."\">".$val_dx."</dx>";
											}
										}

									//}

									$flgDx=1;
								}
							}
						}
						//--
					}

					//--


					if(!empty($cpt_mod)){

						$cpt_mod_cur="";
						$flg_md_assoc=0;
						if(strpos($cpt_mod,"|~|")!==false){
							$arr_mod_code_tmp = explode("|~|", $cpt_mod);
							$arr_mod_code_tmp = array_map('trim', $arr_mod_code_tmp);
							//$arr_mod_code = array_filter($arr_mod_code);
							//$arr_mod_code = array_values($arr_mod_code);
							//$flg_md_assoc=1;

							$cpt_mod_cur = $arr_mod_code_tmp[$i];

						}else{
							$cpt_mod_cur = $cpt_mod;
						}

						if(strpos($cpt_mod_cur,";")!==false){
							$arr_mod_code_tmp = preg_split( "/(;|,)/", $cpt_mod_cur ); //explode(";", $cpt_mod);
							$arr_mod_code_tmp = array_map('trim', $arr_mod_code_tmp);
							$arr_mod_code = array_filter($arr_mod_code_tmp);
							$arr_mod_code = array_values($arr_mod_code);
						}else{
							$arr_mod_code = explode(",", $cpt_mod_cur);
						}

						$md_b=0;
						for($b=0;$b<4;$b++){
							$tModCode = trim($arr_mod_code[$b]);
							if(!empty($tModCode)){
								/*
								if($flg_md_assoc==1){
									if($b!=$i){ continue; }
									$ar_tModCode=explode(";", $tModCode);
									if(count($ar_tModCode)>0){
										foreach($ar_tModCode as $k_mdcd => $v_mdcd){
											$tModCode = trim($v_mdcd);
											if(!empty($tModCode)){
												$tModCode = htmlentities($tModCode,ENT_QUOTES);
												$cpt_info .="<modifier num=\"".($md_b+1)."\" >".$tModCode."</modifier>";
												$md_b++;
											}
										}
									}
								}else{
								*/
									$md_b=$b;
									$tModCode = htmlentities($tModCode,ENT_QUOTES);
									$cpt_info .="<modifier num=\"".($md_b+1)."\" >".$tModCode."</modifier>";
								//}
							}

						}
						//
						$md_b=$md_b+1;

					}else{

						$md_b=1;
						for($b=1;$b<=4;$b++){
							$tModCode = trim($row2["mod".$b]);
							if(!empty($tModCode)){
								$tModCode = htmlentities($tModCode,ENT_QUOTES);
								$cpt_info .="<modifier num=\"".$b."\" >".$tModCode."</modifier>";
								$md_b=$b;
							}
						}

					}

					$cpt_info .= "</cpt>";

					//check cpt with J , set units for botox --
					if(preg_match('/^j/i' , $row2["cpt_prac_code"]) && !empty($type_btx)){
							if($type_btx == "Medical" && !empty($btx_wstd)){
									$ptrn = !empty($btx_used) ? "units=\"".$btx_used."\"" : "units=\"".$unit."\""  ;
									$replce = "units=\"".$btx_wstd."\"";
									$cpt_info_wasted = $cpt_info;
									$cpt_info_wasted = str_replace($ptrn, $replce,  $cpt_info_wasted );

									$ptrn = "/<modifier\s+num=\"\d\"\s+>.+<\/modifier>/";
									$tmp_mod = "<modifier num=\"1\" >JW</modifier>";
									if(preg_match($ptrn, $cpt_info_wasted)){
										$cpt_info_wasted = preg_replace($ptrn, $tmp_mod,  $cpt_info_wasted );
									}else{
										$cpt_info_wasted = str_replace("</cpt>",$tmp_mod."</cpt>", $cpt_info_wasted );
									}

									$cpt_info .= $cpt_info_wasted;
							}
					}

					// Add in all
					$cpt_info_all .= $cpt_info;

				}
			}

			}//

			/*
			if(!empty($dx_code) && $flgDx==0){
				//$tDxDesc = getDxTableInfo($dx_code);
				//$tDxDesc = htmlentities($tDxDesc,ENT_QUOTES);
				//$dx_info .="<dx num=\"1\" desc=\"".$tDxDesc."\">".$dx_code."</dx>";

				// Loop DX Codes --

					if(!empty($dx_code)){

						//--
						$arr_dx_code = explode(",", $dx_code);
						if(count($arr_dx_code) > 0){
							foreach($arr_dx_code as $key_dx => $val_dx){
								$val_dx=trim($val_dx);
								if(!empty($val_dx)){
									$tDxDesc = getDxTableInfo($val_dx);

									$tDxDesc = htmlentities($tDxDesc,ENT_QUOTES);

									$dxindx = $dxindx + 1;

									$dx_info .="<dx num=\"".$dxindx."\" desc=\"".$tDxDesc."\">".$val_dx."</dx>";

								}
							}
						}
						//--
					}

				//--
			}
			*/
		}

		$str = "<proc_sb>";
		if(!empty($cpt_info_all)){  $str .= "<cpt_info>".$cpt_info_all."</cpt_info>";  }
		if(!empty($dx_info)){  $str .= "<dx_info>".$dx_info."</dx_info>";   }
		$str .= "</proc_sb>";

		return $str;
	}

	//procedure notes ---

	function checkMultiVisitCode(){
		$echo="0";
		$elem_strCptCodes =  "";
		$elem_strCptCodes = $_GET["elem_strCptCodes"];
		$elem_superbillDOS = wv_formatDate($_GET["elem_superbillDOS"], 0,0, "insert"); //FormatDate_insert($_GET["elem_superbillDOS"]);
		$elem_encounterId = xss_rem($_GET["elem_encounterId"]);
		$patient_id = $this->pid;

		$sql = "SELECT count(c2.idSuperBill) as num FROM superbill c1
				INNER JOIN procedureinfo c2 ON c2.idSuperBill = c1.idSuperBill
				WHERE c1.patientId = '".$patient_id."' AND c1.dateOfService='".$elem_superbillDOS."' AND c2.cptCode IN (".$elem_strCptCodes.") AND c1.encounterId!='".$elem_encounterId."'
				";
		$row=sqlQuery($sql);
		if($row != false){
			if($row["num"] > 0){
				$echo="1";
			}
		}

		//
		echo $echo;
	}

}
?>
