<?php

class SuperbillSaver extends Patient{
	private $z_refrectValue, $arG;

	public function __construct($pid){
		parent::__construct($pid);
		$this->z_refrectValue = 0;
		$this->arG=array();
	}

	function sb_isSbDxOk($ar){
		$ret_no_empty=0; $ret_no_complete=0;
		for($i=1;$i<=12;$i++){
			$varDxCodes = "elem_dxCode_".$i;
			if(!empty($ar[$varDxCodes])){
				$ret_no_empty=1;
				if(strpos($ar[$varDxCodes], "-")!==false){
					$ret_no_complete=1;
				}
			}
		}

		// if incomplete then not done
		if($ret_no_complete==1){ $ret_no_empty=0;  }

		return $ret_no_empty;
	}


	function setTodaysChargesV2($cptCode,$cptFee,$todaysCharges,$units,$todaysPayment,$insCase,$policyRefrec,$cptNC){

		$clCpt = $this->arG["clCpt"];
		$clCptCost = $this->arG["clCptCost"];

		//CL cost: take from contact lens and not from admin->feetable
		if(!empty($clCpt) && strpos($cptCode,$clCpt)!==false){
			if(!empty($clCptCost)){$todaysCharges = $todaysCharges + $clCptCost;$clCptCost=0; $this->arG["clCptCost"] = $clCptCost; /*so that it is added once for multiple cl cpt codes*/}
		}else if(!empty($cptFee) && ($cptFee != "0.00") ){
			$tmp=$todaysCharges+($cptFee*$units);
			$todaysCharges=$tmp;
		}
		$oCpt = new CPT();
		if($oCpt->mkCPTCode($cptCode) == 92015 && $cptNC>0){
			$this->z_refrectValue = ($cptFee*$units);
			if(($insCase != "") && ($insCase != "Unassigned") && ($policyRefrec == "Yes")){
				$todaysPayment = $todaysPayment + ($cptFee * $units);
			}
		}

		if(($insCase == "") || ($insCase == "Unassigned")){
			$todaysPayment = $todaysCharges;
			if($policyRefrec == "No"){
				$refrect = $this->z_refrectValue;
				$todaysPayment = $todaysPayment - $refrect;
			}
		}
		return array($todaysCharges, $todaysPayment);
	}

	function check_duplicate_encounterId_in_mastertable($encounterId, $formId){
		$arr_ret = array();
		$flg_stop_insert="0";
		$patId = $this->pid;
		//check superbill created with this encounter
		$sql = "SELECT count(*) as num, patientId, formId FROM superbill WHERE encounterId = '".$encounterId."'  ";
		$row = sqlQuery($sql);
		if($row!=false && $row["num"] > 0){
			if($row["patientId"] == $patId && $row["formId"]==$formId){
				$flg_stop_insert="1";
			}else{
				//assign a new encounter id
				$oCN = new ChartNote($this->pid, $formId);
				$encounterId = $oCN->resetEncounterId();
			}
		}
		$arr_ret["flg_stop_insert"] = $flg_stop_insert;
		$arr_ret["encounterId"] = $encounterId;
		return $arr_ret;
	}

	//superbill function
	function set_app_id_group_pos($sbid){

		$sql = "SELECT sch_app_id FROM superbill WHERE idSuperBill = '".$sbid."' AND postedStatus='0' AND sch_app_id!='' ";
		$row = sqlQuery($sql);
		if($row != false){
			$sch_app_id = $row["sch_app_id"];
			if(!empty($sch_app_id)){
				//get
				$sql = "SELECT sa_facility_id FROM schedule_appointments WHERE id = '".$sch_app_id."' ";
				$row = sqlQuery($sql);
				if($row!=false){
					$sa_facility_id = $row["sa_facility_id"];
					if(!empty($sa_facility_id)){
						$sql = "SELECT c2.gro_id, c2.pos_code, c4.pos_prac_code FROM facility c2
								LEFT JOIN pos_facilityies_tbl c3 ON c3.pos_facility_id = c2.fac_prac_code
								LEFT JOIN pos_tbl c4 ON c4.pos_id = c3.pos_id
								WHERE c2.id = '".$sa_facility_id."' ";
						$row=sqlQuery($sql);
						if($row != false){
							$groupId=$row["gro_id"];
							$pos = $row["pos_prac_code"];

							if(!empty($groupId) || !empty($pos)){

								$sql = "UPDATE superbill SET ";
								if(!empty($groupId)){ $sql .= " gro_id='".$groupId."' ";    }
								if(!empty($pos)){
									if(!empty($groupId)){  $sql.= ", "; }
									$sql.= " pos='".$pos."'  ";  }

								$sql .= "WHERE idSuperBill = '".$sbid."' AND postedStatus='0' ";
								$row = sqlQuery($sql);

							}

						}
					}
				}
			}
		}
	}

	function saveHandler(){
		$arG=array();
		$arG["flgThisisCN"]=false;
		$arG["elem_physicianId"]=$_POST["elem_physicianId"];
		$arG["doctorId"]=0;
		$arG["caseId"]= $_POST["elem_masterCaseId"] ;
		$arG["encounterId"]= isset($_SESSION['cn_enc']) && !empty($_SESSION['cn_enc']) ? $_SESSION['cn_enc'] : 0;
		$arG["date_of_service"]=$_POST["date_of_service"];
		$arG["sb_testId"]=0;
		$arG["sb_testName"]=0;
		$arG["sb_arrMerged"]=0;
		$arG["form_id"]=$_POST["elem_masterId"];
		$arG["printMode"]= (!empty($_POST["save_print"])) ? 1 : 0;
		$arG["super_update_chk"]=$_POST["super_update_chk"];

		$this->save($arG);
	}

	function getSbProcIds($idsb){
		$ar=array();
		$sql = "SELECT id,porder,cptCode FROM procedureinfo where idSuperBill ='".$idsb."' AND delete_status='0' order by id ";
		$rez = sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++){
			$ar[] = $row;
		}
		return $ar;
	}

	function save($arG=array()){

		$flgThisisCN = $arG["flgThisisCN"];
		$elem_physicianId = $arG["elem_physicianId"];
		$doctorId = $arG["doctorId"];
		$caseId = $arG["caseId"];
		$encounterId = $arG["encounterId"];
		$date_of_service = $arG["date_of_service"];
		$sb_testId = $arG["sb_testId"];
		$sb_testName = $arG["sb_testName"];
		$sb_arrMerged = $arG["sb_arrMerged"];
		$sb_formId=$arG["form_id"];			//?
		$printMode=$arG["printMode"];
		$elem_pos = $_POST["elem_pos"];
		$elem_tos = $_POST["elem_tos"];
		$elem_procOrder = $_POST["elem_procOrder"];
		$super_update_chk = $arG["super_update_chk"];

		$is_test_interpreted = $arG["test_interpreted"];

		$sb_arrMerged=array();
		if(isset($_POST["elem_sb_tsbIds"]) && !empty($_POST["elem_sb_tsbIds"])){
			$sb_arrMerged = $_POST["elem_sb_tsbIds"];
		}

		//save_superbill.php
		$isSuperBillCreated = false;
		$makeDFT_HL7		= 0;
		$NEWSuperBill_Created = false;
		$hl7_SuperBill_id	= 0;
		//

		//
		$patientId=$this->pid;

		//check if super bill exists for this encounter id
		$sql = "SELECT idSuperBill,physicianId,dateOfService FROM superbill WHERE encounterId = '".$encounterId."' AND patientId='".$patientId."' AND del_status='0'";
		$row = sqlQuery($sql);

		//if Test superbill, check with test id and test name
		$str_update_enc_id="";
		if($row == false && !empty($sb_testName) && !empty($sb_testId)){
			$sql = "SELECT idSuperBill,physicianId,dateOfService FROM superbill WHERE test_name = '".$sb_testName."' AND test_id='".$sb_testId."' AND patientId='".$patientId."' AND del_status='0'";
			$row = sqlQuery($sql);
			if($row != false){ $str_update_enc_id = " ,encounterId='".$encounterId."' ";  } //update with new encounter id
		}

		if($row != false){
			$superBillId=$row["idSuperBill"];
			$chkSBPhyId = $row["physicianId"];
			$isSuperBillCreated = true;
			$dateOfService_db = $row["dateOfService"];

			//set false as this insert
			$flgChkSBPhywidCNDoc=true;

			//get Procedure info of superbill
			//$arr_sbProcIds = $this->getSbProcIds($superBillId);

		}else{
			if(!empty($elem_procOrder)){
				$isSuperBillCreated = true;
			}
		}
		//

		//check superbill again for incomplete dx codes
		if($isSuperBillCreated == true){  if(!$this->sb_isSbDxOk($_POST)){ $isSuperBillCreated = false; }}

		if($isSuperBillCreated && isset($_POST["elem_cptCode_1"])){
		//echo "SAVING S. BILL";
		//exit;
		//Superbill values

		$oPt = new Patient($patientId);
		$oPtSch = new PtSchedule($patientId);
		if($elem_physicianId!=""){
			$physicianId=$elem_physicianId;	//?
		}else{
			$physicianId=$doctorId;	//?
		}
		$insuranceCaseId=!empty($_POST["elem_sb_insuranceCaseId"]) ? $_POST["elem_sb_insuranceCaseId"] : $caseId;	//?
		$encounterId=$encounterId;		//?

		$timeSuperBill = date("h:i:s A", time());
		$dateOfService=$date_of_service;		//?
		$patientStatus="Active";
		$refferingPhysician= $oPt->getPrimaryCarePhysician(); //Default Reffering Patient
		$financialStatus="Self";
		$todaysCharges="0.00";
		$coPay="0.00";
		$todaysPayment="0.00";
		$methodOfPayment="Cash";
		$chequeCcNumber="";
		$deposit="0";
		$ccHolderName="";
		$ccExpDate="";
		$ccAcctNumber="";
		$referTo="";
		$sendWithCopyTodaysNotes="";
		$sendWithCopyIvfa="";
		$sendWithCopyLatestVf="";
		$sendWithCopyRecRelease="";
		$test_id=$sb_testId;		//?
		$test_name=$sb_testName;	//?
		$strSbMerged = $sb_arrMerged;	//?

		//OChart note
		$ochart = new ChartNote($patientId, $sb_formId);

		// GET From chart_master_table
		$vipSuperBill= (!empty($sb_formId)) ? $ochart->isVip() : "" ;	//TableValues("chart_master_table", "id='".$sb_formId."'", "isVip")
		$notesSuperBill="";
		if($elem_vipSuperBill){
			$vipSuperBill=$elem_vipSuperBill;
		}
		if($elem_notesSuperBill!=""){
			$notesSuperBill=$elem_notesSuperBill;
		}
		if($elem_pos!=""){
			$pos=addslashes($elem_pos);
		}else{
			$tmp = $oPtSch->getPatientPOS($dateOfService);
			$def_pos = !empty($GLOBALS["SB_POS_Default"]) ? $GLOBALS["SB_POS_Default"] : "O";
			$pos= !empty($tmp) ? $tmp : $def_pos;
		}

		if($elem_tos!=""){
			$tos=$elem_tos;
		}else{
			$varCpt_chk = "elem_cptCode_1";
			$cptCode_chk = sqlEscStr($_POST[$varCpt_chk]);
			$proc_tos="";
			if($cptCode_chk!=""){
				$getCPTPriceQry = "SELECT a.tos_prac_cod FROM tos_tbl a,
												cpt_fee_tbl b
												WHERE
												a.tos_id = b.tos_id
												and (b.cpt4_code='$cptCode_chk' or b.cpt_desc='$cptCode_chk')
												AND b.delete_status = '0' order by b.status asc";
				$getCPTPriceRow = sqlQuery($getCPTPriceQry);
				$tos = $getCPTPriceRow['tos_prac_cod'];
			}
			if($tos==""){
				$tos="1";
			}
		}

		$elem_mxSBId = $_POST["elem_mxSBId"];
		$elem_procOrder = sqlEscStr($_POST["elem_procOrder"]);
		$pri_copay=$sec_copay=$ter_copay="0.00";
		if(isset($_POST["vipSuperBill"])&&!empty($_POST["vipSuperBill"])) {$vipSuperBill = $_POST["vipSuperBill"];}
		//exit;

		//Insurance case
		if(!isset($insuranceCaseId) || empty($insuranceCaseId))
		{
			$insuranceCaseId = $oPt->getInuranceCaseId();
		}
		// Fee Column
		if(isset($insuranceCaseId) && !empty($insuranceCaseId)){
			$oIns = new Insurance();
			$insFeeColumn = $oIns->getInsFeeColumn($insuranceCaseId, $dateOfService);
		}

		//
		$oCpt = new CPT();

		// Get Active Cpt Desc, Codes, Proc Fee, Modifiers
		list($arrDesc,$arrCptCodes,$arrProcedureFee,$arrCptModifiers,$arrCptCU,$arrCptNC) = $oCpt->getActiveCptCodesInfo($insFeeColumn);
		$arrOtherProcedures=$oCpt->getOtherProceduresFee($insFeeColumn);
		if(count($arrOtherProcedures) > 0){
			foreach($arrOtherProcedures as $feeKey => $feeVal)
			{
				if(!isset($arrProcedureFee[$feeKey])){
					$arrProcedureFee[$feeKey] = $feeVal;
				}
			}
		}

		//Physician Id
		if(!isset($physicianId) || empty($physicianId))
		{
			$physicianId = $oPt->getPtPrimaryPhy() ; //getDefaultPhisicianId($patientId); //$_SESSION["authId"];
		}

		//icd 10  to save in superbill
		$sup_icd10 = $_POST["hid_icd10"];

		// Copay
		/* Copay is Stopped in super bill as per arun sir instruction
		if(empty($coPay) || ($coPay == 0.00))
		{

			if(isset($insuranceCaseId) && !empty($insuranceCaseId))
			{
				$arrCoPay = getCopay($insuranceCaseId,1);
				$pri_copay=number_format($arrCoPay["pri_copay"],2);
				$sec_copay=number_format($arrCoPay["sec_copay"],2);
				$ter_copay=number_format($arrCoPay["ter_copay"],2);
				$coPay=number_format($arrCoPay["total_copay"],2);
			}
		}
		*/

		// TodaysPayment
		if(empty($todaysPayment) || ($todaysPayment == 0.00))
		{
			//$todaysPayment = $todaysPayment + $coPay;
			$todaysPayment = $todaysPayment; //coopay stopped
			$todaysPayment = number_format($todaysPayment,2);
		}

		$oAdmn = new Admn();
		// Refraction Policy
		$tmp = $oAdmn->cp_getRefSetting();
		$policyRefraction = ($tmp[0]==true) ? "Yes" : "No"; //  getRefractionPolicy();

		//Dx Codes
		$sb_dxids = trim($_POST["sb_dxids"]);
		$ar_sb_dxids = !empty($sb_dxids) ? explode(";", $sb_dxids) : array() ;
		$arrDxCodes=array();
		$str_dx_codes="";
		//for($i=1;$i<=4;$i++){
		for($i=1;$i<=12;$i++){
			$varDxCodes = "elem_dxCode_".$i;
			$tmp = $_POST[$varDxCodes];
			if(isset($ar_sb_dxids[$i-1]) && !empty($ar_sb_dxids[$i-1])){
				$tmp = $tmp ."@*@".$ar_sb_dxids[$i-1];
			}
			//$varDxCodes = "diagText_".$i;
			$arrDxCodes[$i]=$tmp;
		}
		$str_dx_codes=serialize($arrDxCodes);

		//ProcOrder

		##Debugging
		//echo "<br>".$elem_mxSBId;
		//print_r($arrCptCU);
		//echo "<br>Desc: ";
		//print_r($arrDesc);
		//echo "<br>Codes: <br>";
		//print_r($arrCptCodes);
		//echo "<br><br>";
		//echo "<BR>TodayCharges: ".$todaysCharges.", TodayPayments: ".$todaysPayment."InsCaseId: ".$insuranceCaseId.",PolicyRefrec: ".$policyRefraction;
		##Debugging
		//exit;

		//contact lens --
		$clCpt=""; $clCptCost="";
		if(!empty($patientId)){
			//list($clCpt, $clCptCost) = getCL_CPT_Charges($patientId, $dateOfService);
			$oSbInfo = new SbInfo($patientId, $sb_formId);
			list($clCptCost, $cl_ar_cpt, $clCpt) = $oSbInfo->checkCL_FitRefitEva_v2($dateOfService);
		}
		$this->arG["clCpt"] = $clCpt;
		$this->arG["clCptCost"] = $clCptCost;
		//contact lens --

		//Charges
		$arrCurProcedureInfo=array();
		$arrCurCpt = array();
		$cntrCU=0;
		$flagChkSB = true;
		//$arrProcOrderTmp = (!emtpy($elem_procOrder)) ? explode(",",$elem_procOrder) : array();

		for($i=1;$i<=$elem_mxSBId;$i++){
			$varCpt = "elem_cptCode_".$i;
			$varProcId = "elem_procedureId_".$i;
			$varProcUnits = "elem_procUnits_".$i;
			$varProcOrder = "elem_procedureOrder_".$i;

			if(isset($_POST[$varCpt]) && !empty($_POST[$varCpt])){

				$procId = $_POST[$varProcId];

				//This fix is for superbill getting saved without reloading. It create new record for procedureinfo. following fix stop it by assigning procedure ids.
				/*if(empty($procId) && !empty($superBillId)){
					if(count($arr_sbProcIds)>0){
						$unset_k=-1;
						foreach($arr_sbProcIds as $k => $sbProcIds){
							if(trim($sbProcIds["cptCode"]) == trim($_POST[$varCpt])){
								$procId = $sbProcIds["id"];
								$unset_k=$k;
								break;
							}
						}
						if($unset_k!=-1){ unset($arr_sbProcIds[$unset_k]); }
					}
				}*/
				//---


				$arrtmp = array();
				$cptCode = sqlEscStr($_POST[$varCpt]); //CPT

				$cptFee = $arrProcedureFee[$cptCode];//Fee
				$units=$_POST[$varProcUnits];
				$cptNC = $arrCptNC[$cptCode];//Fee
				$units = (empty($units)) ? "1" : $units;

				$tmpDesc = array_search($cptCode,$arrCptCodes);
				if($tmpDesc !== false){
					$description = $procedureName = $tmpDesc ;
				}else{
					$procedureName = $cptCode ;
				}
				//User Defined
				
				if(($arrCptCU[$cptCode] != "1") || (in_array($cptCode, $arrCurCpt)) ){

					$ocpt = new CPT();
					$ar_cpt = $ocpt->getCptDefVals($cptCode,"cpt_desc");  //"User Defined".(++$cntrCU);
					$description = !empty($ar_cpt["cpt_desc"]) ? $ar_cpt["cpt_desc"] : "" ;
					if(!empty($description)){$procedureName = $description;}
					//$description ="User Defined".(++$cntrCU);
				}

				////checkCPT
				$order = sqlEscStr($_POST[$varProcOrder]);

				$arrtmp["procId"] = $procId;
				$arrtmp["cptCode"] = $cptCode;
				$arrtmp["cptFee"] = $cptFee;
				$arrtmp["units"] = $units;
				$arrtmp["desc"] = $description;
				$arrtmp["procedureName"] = $procedureName;
				$arrtmp["order"] = $order;
				$arrtmp["indx"] = $i;
				//echo "<br>cptCode:".$cptCode.", cptFee:".$cptFee.", units:".$units;
				if($vipSuperBill=="1" && $oCpt->mkCPTCode($cptCode)=="92015"){
					//Do not add
				}else{
					list($todaysCharges, $todaysPayment) = $this->setTodaysChargesV2($cptCode,$cptFee,$todaysCharges,$units,$todaysPayment,$insuranceCaseId,$policyRefraction,$cptNC);
				}
				$arrDx = array();
				$arrMd = array();
				for($j=1;$j<=4;$j++){
					/*
					$elemDxAssoc = $_POST["elem_dxCodeAssoc_".$i."_".$j];
					if($elemDxAssoc == "1"){
						$arrDx[] = $arrDxCodes[$j];//DX
					}*/

					if($j<=4){
						$elemMdCodes = $_POST["elem_modCode_".$i."_".$j];
						if(isset($elemMdCodes) && !empty($elemMdCodes)){
							$arrMd[]=$elemMdCodes;//Md

						}
					}
				}
				//dx codes
				$elemDxAssoc = $_POST["elem_dxCodeAssoc_".$i];
				if(count($elemDxAssoc)>0){
					foreach($elemDxAssoc as $keyDx => $valDx){
						if(!empty($valDx)){
							$arrValDx = explode("**", $valDx);
							$arrDx[$arrValDx[1]] = $arrValDx[0];
						}
					}
				}
				/*
				if($elemDxAssoc == "1"){
					$arrDx[] = $arrDxCodes[$j];//DX
				}*/

				//Check Dx
				if( count($arrDx) == 0 ){
					$flagChkSB = false;
				}

				$arrtmp["Dx"] = $arrDx;
				$arrtmp["Md"] = $arrMd;
				$arrCurProcedureInfo[]	= $arrtmp;
				$arrCurCpt[] = $cptCode;
			}
		}

		//POE Object
		$oPoe = new Poe($patientId,$encounterId);
		//$flgPoe = false;

		//echo "<br>";
		//print_r($arrCurProcedureInfo);
		//exit;

		$sel_provider_qry="select sa_doctor_id,sa_facility_id from schedule_appointments,users
			where users.id=schedule_appointments.sa_doctor_id and
			users.user_type=1 and
			sa_doctor_id>0 and sa_app_start_date='".$dateOfService."' and sa_patient_id='".$patientId."'
			and sa_patient_app_status_id NOT IN (203,201,18,19,20) order by sa_app_starttime desc limit 0,1";
		$sel_provider_fet=sqlQuery($sel_provider_qry);
		$sa_doctor_id =$sel_provider_fet['sa_doctor_id'];
		$sa_facility_id =$sel_provider_fet['sa_facility_id'];

		if($sa_facility_id>0){
			$sel_grp_qry="select default_group from facility where id='".$sa_facility_id."'";
			$sel_grp_fet=sqlQuery($sel_grp_qry);
			$gro_id=$sel_grp_fet['default_group'];
		}
		if($gro_id>0){
		}else{
			$sel_proc="select default_group from users where default_group>0 and id='".$physicianId."'";
			$proc_row=sqlQuery($sel_proc);
			if($proc_row['default_group']>0){
				$gro_id=$proc_row['default_group'];
			}else{
				$sel_proc="select gro_id from groups_new where group_institution='0' and del_status='0' order by gro_id asc";
				$proc_row=sqlQuery($sel_proc);
				$gro_id=$proc_row['gro_id'];
			}
		}

		$hl7_SuperBill_id = $superBillId;
		if($flagChkSB == true){

			//Delete by click --
			if(!empty($_POST["elem_proc_del_id"])){
				$ar_del_proc_id=explode(",",$_POST["elem_proc_del_id"]);
				if(count($ar_del_proc_id)>0){
					foreach($ar_del_proc_id as $key_del => $v_del_proc_id){
						if(!empty($v_del_proc_id)){
							$c_delete_status ='1';
							$sql = "UPDATE procedureinfo SET delete_status ='".$c_delete_status."' WHERE id = '".$v_del_proc_id."' AND idSuperBill='".$superBillId."' ";
							$res = sqlQuery($sql);
						}
					}
				}
			}
			//Delete by click --


			//ProcIDs
			$arrProcedureIds = array();

			if(!empty($superBillId)){ // UPDATE SUPER BILL
				if($super_update_chk==1){
					$whr_up_sup=",physicianId='$physicianId',primary_provider_id_for_reports='$physicianId', notesSuperBill='$notesSuperBill',pos='$pos',tos='$tos' ";
				}
				//Update DOS
				if(!empty($dateOfService) && strpos($dateOfService,"0000")===false && $dateOfService!=$dateOfService_db){
					$whr_up_dos=",dateOfService = '$dateOfService' ";
				}

				$sql = "UPDATE superbill ".
					   "SET ".
					   "todaysCharges = '".$todaysCharges."', ".
					   //"coPay = '".$coPay."', ". //stopped
					   "procOrder = '".$elem_procOrder."', ".
					   "gro_id = '".$gro_id."', ".
					   "vipSuperBill = '".$vipSuperBill."', ".
					   "insuranceCaseId  = '".$insuranceCaseId."', ".
					   "todaysPayment = '".$todaysPayment."', ".
					   "arr_dx_codes = '".sqlEscStr($str_dx_codes)."' ".
					   //"pri_copay = '".$pri_copay."', ". //stopped
					   //"sec_copay = '".$sec_copay."', ". //stopped
					   //"ter_copay = '".$ter_copay."' ". //stopped
					   $whr_up_sup.$whr_up_dos.$str_update_enc_id.
					   "WHERE idSuperBill= '".$superBillId."'";
				$result = sqlQuery($sql);
				if(imw_affected_rows()>0){
					sqlQuery("update superbill set modified_date_time='".date('Y-m-d H:i:s')."',modified_by='".$_SESSION["authId"]."' where idSuperBill= '".$superBillId."'");
				}
				$insertIdSuperBill = $superBillId;

				//getProcIds
				// Select Old Procedure Info Ids of superbill
				$sql = "SELECT id,cptCode FROM procedureinfo
						WHERE idSuperBill = '".$insertIdSuperBill."'
						AND delete_status ='0'
						";
				$rez = sqlStatement($sql);
				for($i=0;$row=sqlFetchArray($rez);$i++)
				{
					$arrProcedureIds[] = $row["id"];
				}

			}else{ //INSERT
				if(!empty($encounterId)){ //Check empty encounter

				//
				$arTmp = $this->check_duplicate_encounterId_in_mastertable($encounterId, $sb_formId);
				$encounterId = $arTmp["encounterId"];

				if(!empty($arTmp["flg_stop_insert"])){
					//donot insert if record exists for same patient and chart: this is to stop slow myserver issue
				}else{
				$sql = "INSERT INTO superbill ".
					   "(idSuperBill, patientId, physicianId, insuranceCaseId, encounterId, formId, ".
					   " timeSuperBill, dateOfService, patientStatus, refferingPhysician, financialStatus, ".
					   " todaysCharges, coPay, todaysPayment, methodOfPayment, chequeCcNumber, ".
					   " deposit, ccHolderName, ccExpDate, ccAcctNumber, ".
					   " referTo, sendWithCopyTodaysNotes, ".
					   " sendWithCopyIvfa, sendWithCopyLatestVf, sendWithCopyRecRelease, vipSuperBill, notesSuperBill, ".
					   " pos,tos,procOrder,gro_id, ".
					   //" pri_copay,sec_copay,ter_copay ".
						" test_id, test_name, arr_dx_codes,primary_provider_id_for_reports, ".
						"sup_icd10 ".
					   " ) ".
					   " VALUES ".
					   " (NULL, '$patientId', '$physicianId', '$insuranceCaseId', '$encounterId', '$sb_formId', ".
					   " '$timeSuperBill', '$dateOfService', '$patientStatus', '$refferingPhysician', '$financialStatus', ".
					   " '$todaysCharges', '$coPay', '$todaysPayment', '$methodOfPayment', '$chequeCcNumber', ".
					   " '$deposit', '$ccHolderName', '$ccExpDate', '$ccAcctNumber', ".
					   " '$referTo', '$sendWithCopyTodaysNotes', ".
					   " '$sendWithCopyIvfa', '$sendWithCopyLatestVf', '$sendWithCopyRecRelease', '$vipSuperBill', '$notesSuperBill', ".
					   " '$pos', '$tos', '$elem_procOrder', '$gro_id', ".
					   //" '$pri_copay','$sec_copay','$ter_copay' ".
						" '$test_id', '$test_name', '".sqlEscStr($str_dx_codes)."','$physicianId', ".
						" '$sup_icd10' ".
					   " ) ";

					// Insert Into SuperBill
					$insertIdSuperBill = sqlInsert($sql);

					if(!empty($sb_formId)){
						//Update Chart_master_table
						$sql = "UPDATE chart_master_table ".
							 "SET ".
							 "update_date = '".wv_dt('now')."', ".
							 "isSuperBilled='1' ".
							 "WHERE id= '".$sb_formId."' ";
						$res = sqlQuery($sql);
					}
				}
				}
			}

			//Procedures
			$arrCurProcedureIds=array();
			$sb_arrCurProcIds=array();

			if(count($arrCurProcedureInfo) > 0){
				foreach($arrCurProcedureInfo as $var => $val){
					$procId = $val["procId"];
					$cptCode = $val["cptCode"];
					//echo "<br> Fee: ".$val["cptFee"];
					$units = $val["units"];
					$description = $val["desc"];
					$procedureName = $val["procedureName"];

					$dx1 = $val["Dx"][1];
					$dx2 = $val["Dx"][2];
					$dx3 = $val["Dx"][3];
					$dx4 = $val["Dx"][4];
					$dx5 = $val["Dx"][5];
					$dx6 = $val["Dx"][6];
					$dx7 = $val["Dx"][7];
					$dx8 = $val["Dx"][8];
					$dx9 = $val["Dx"][9];
					$dx10 = $val["Dx"][10];
					$dx11 = $val["Dx"][11];
					$dx12 = $val["Dx"][12];

					$modifier1 = $val["Md"][0];
					$modifier2 = $val["Md"][1];
					$modifier3 = $val["Md"][2];
					$modifier4 = $val["Md"][3];

					$pOrder = $val["order"];

					if(!empty($procId)){ // Update Procedure
						$procedureId=$procId;
						$description=sqlEscStr($description);
						$procedureName = sqlEscStr($procedureName);

						//UPDATE
						$sql = "UPDATE procedureinfo ".
							   "SET ".
							   "description = '".$description."', procedureName = '".$procedureName."',".
							   "cptCode = '".$cptCode."', dx1 = '".$dx1."', ".
							   "dx2 = '".$dx2."', dx3 = '".$dx3."',".
							   "dx4 = '".$dx4."', dx1MenuString = '', ".
							   "dx2MenuString = '', dx3MenuString = '', ".
							   "dx4MenuString = '', modifier1 = '".$modifier1."', ".
							   "modifier2 = '".$modifier2."', modifier3 = '".$modifier3."', modifier4 = '".$modifier4."', ".
							   "modifier1MenuString = '', modifier2MenuString = '', ".
							   "modifier3MenuString = '', ".
							   "units = '".$units."', porder='".$pOrder."', ".
							   "dx5 = '".$dx5."', dx6 = '".$dx6."', dx7 = '".$dx7."', dx8 = '".$dx8."',  ".
							   "dx9 = '".$dx9."', dx10 = '".$dx10."', dx11 = '".$dx11."', dx12 = '".$dx12."'  ".
							   "WHERE id = '".$procedureId."' AND idSuperBill='".$insertIdSuperBill."' ";

						$res = sqlQuery($sql);
						if($makeDFT_HL7==0) $makeDFT_HL7 = imw_affected_rows();
						$arrCurProcedureIds[]=$procedureId;
						$sb_arrCurProcIds[$val["indx"]]=$procedureId;

					}else{ // Insert a new Procedure

						if(!empty($insertIdSuperBill)){//do not enter if superbillId is empty

							$description=sqlEscStr($description);
							$procedureName = sqlEscStr($procedureName);

							$sql = "INSERT INTO procedureinfo ".
								   "(id, description, procedureName, cptCode, dx1, dx2, dx3, dx4, ".
								   "dx1MenuString, dx2MenuString, dx3MenuString, dx4MenuString, ".
								   "modifier1, modifier2, modifier3, modifier4, ".
								   "modifier1MenuString, modifier2MenuString, modifier3MenuString, ".
								   "idSuperBill, units, porder, ".
								   "dx5, dx6, dx7, dx8, ".
								   "dx9, dx10, dx11, dx12 ".
								   ") ".
								   "VALUES ".
								   "(NULL, '".$description."', '".$procedureName."', '".$cptCode."','".$dx1."','".$dx2."','".$dx3."','".$dx4."', ".
								   "'', '', '', '', ".
								   "'".$modifier1."','".$modifier2."','".$modifier3."', '".$modifier4."', ".
								   "'','','', ".
								   "'".$insertIdSuperBill."', '".$units."','".$pOrder."', ".
								   "'".$dx5."', '".$dx6."', '".$dx7."', '".$dx8."', ".
								   "'".$dx9."', '".$dx10."', '".$dx11."', '".$dx12."' ".
								   ") ";
							$temp = sqlInsert($sql);
							$arrCurProcedureIds[]=$temp;
							$sb_arrCurProcIds[$val["indx"]]=$temp;
							$makeDFT_HL7 = 1;
							$NEWSuperBill_Created = true;
						}

					}

					//Check Cpt For POE
					$oPoe->isPoeCode($cptCode);
					/*
					if($flgPoe == false && $oPoe->isPoeCode($cptCode)){
						$flgPoe = true;
					}
					*/
				}


				// DELETE
				$countProcedureIds = count($arrProcedureIds);
				if($countProcedureIds > 0){
					foreach($arrProcedureIds as $key => $val)
					{
						if(array_search($val,$arrCurProcedureIds) === false)
						{
							//$sql = "DELETE FROM procedureinfo WHERE id = '".$val."' ";
							//if($flgThisisCN==true){ $c_delete_status ='2'; }else{ $c_delete_status ='3'; }
							$c_delete_status ='1';
							$sql = "UPDATE procedureinfo SET delete_status ='".$c_delete_status."' WHERE id = '".$val."' AND idSuperBill='".$insertIdSuperBill."' ";
							$res = sqlQuery($sql);

						}
					}
				}

			}


			//Check super bill physician id with chart notes doctor id
			// if different set chart notes doctor id as physician id of super bill
			// Run From main save only
			if($flgThisisCN==true && $flgChkSBPhywidCNDoc==true && !empty($doctorId) && !empty($insertIdSuperBill)){

				$oUsr = new User($doctorId);
				$docTypeCn = $oUsr->getUType(1);

				//$docTypeCn = getUserType($doctorId);
				//if(in_array($docTypeCn,$GLOBALS['arrValidCNPhy'])){
				if($docTypeCn == 1){

					if($chkSBPhyId != $doctorId){
						$sql = "UPDATE superbill SET physicianId = '".$doctorId."',primary_provider_id_for_reports='".$doctorId."' WHERE idSuperBill = '".$insertIdSuperBill."' ";
						$r=sqlQuery($sql);
					}

				}
			}

			// Update with Phy id --
			if(!empty($insertIdSuperBill)){
				$sql = "SELECT count(*) as num from superbill where idSuperBill = '".$insertIdSuperBill."' AND physicianId='0' ";
				$row=sqlQuery($sql);
				if($row!=false){
					if($row["num"]>0){

						$set_phy_id=0;

						if(empty($set_phy_id)){
							//check from master table providerid
							if(!empty($sb_formId)){
								$sql = "SELECT providerId From chart_master_table where id = '".$sb_formId."' AND providerId!='0'  ";
								$row=sqlQuery($sql);
								if($row!=false){
									if(!empty($row["providerId"])){
										$oUsr = new User($row["providerId"]);
										$tmp = $oUsr->getUType(1);

										//$tmp = getUserType($row["providerId"]);
										//if(in_array($tmp,$GLOBALS['arrValidCNPhy'])){
										if($tmp == 1){
											$set_phy_id=$row["providerId"];
										}
									}
								}
							}
						}//

						if(empty($set_phy_id)){
							////check scheduler
							//$dateOfService
							//get Patient Doctor Appointment for Today
							$sql = "SELECT sa_doctor_id FROM schedule_appointments c1
									INNER JOIN users c2 ON c1.sa_doctor_id=c2.id
									WHERE c1.sa_patient_id='".$patientId."' AND c1.sa_app_start_date = '".$dateOfService."'  ";
							$row=sqlQuery($sql);
							if($row!=false){
								if(!empty($row["sa_doctor_id"])){
									$oUsr = new User($row["sa_doctor_id"]);
									$tmp = $oUsr->getUType(1);
									//$tmp = getUserType($row["sa_doctor_id"]);
									//if(in_array($tmp,$GLOBALS['arrValidCNPhy'])){
									if($tmp == 1){
										$set_phy_id=$row["sa_doctor_id"];
									}
								}
							}
						}

						if(empty($set_phy_id)){
							//getpt demo
							$demo_phy_id = $oPt->getPtPrimaryPhy(); //getDefaultPhisicianId($patientId);
							if(!empty($demo_phy_id)){
								$oUsr = new User($demo_phy_id);
								$tmp = $oUsr->getUType(1);
								//$tmp = getUserType($demo_phy_id);
								//if(in_array($tmp,$GLOBALS['arrValidCNPhy'])){
								if($tmp == 1){
									$set_phy_id=$demo_phy_id;
								}
							}
						}

						if(!empty($set_phy_id)){
							$sql = "UPDATE superbill SET physicianId='".$set_phy_id."' WHERE idSuperBill = '".$insertIdSuperBill."' AND physicianId='0' ";
							$row=sqlQuery($sql);
						}
					}
				}
			}
			//--

			//End check

			//Set POE DATE
			$oPoe->setPoeEnId();

			// Set SB to Merged --
			if(isset($strSbMerged) && !empty($strSbMerged) && $insertIdSuperBill!=$strSbMerged){
				$sql = "UPDATE superbill SET merged_with='".$insertIdSuperBill."' ".
						"WHERE idSuperBill IN (".$strSbMerged.")";
				$res = sqlQuery($sql);
			}
			// Set SB to Merged --

			/*if($insertIdSuperBill>0){
				$sql = "UPDATE superbill SET refferingPhysician = '".$refferingPhysician."' WHERE idSuperBill = '".$insertIdSuperBill."' and postedStatus='0'";
				$r=sqlQuery($sql);
			}*/

			//Schedule id is not saving in superbill table. Please use "set_app_id_chl($patient_id,$sup_date,'sup');" function while saving superbill.
			if(!function_exists("set_app_id_chl")){
				include_once($GLOBALS["incdir"]."/../library/classes/acc_functions.php");
			}
			set_app_id_chl($patientId,$dateOfService,'sup');

			// Update Group Id and POS if sch app id is set in superbill
			$this->set_app_id_group_pos($insertIdSuperBill);
			//-----
		}
		}

		/***HL7 DFT MESSAGE SAVING 11-DEC-2014***/
		if($insertIdSuperBill && !empty($insertIdSuperBill)) $hl7_SuperBill_id = $insertIdSuperBill;

		if($hl7_SuperBill_id && constant('SUP_DFT_GENERATION')==true){
			require_once( dirname(__FILE__).'/../../../hl7sys/old/CLS_makeHL7.php');
			$makeHL7		= new makeHL7;

			//LOG login facility if superbill created, in pre_hl7_sent.
			if($NEWSuperBill_Created && in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('boston'))){
				$make_dft_4_this_fac = $makeHL7->log_hl7_sent_pre($hl7_SuperBill_id,$_SESSION['login_facility']);
			}

			if($_POST['el_btfinalize_pressed'] == "1" && $hl7_SuperBill_id){
				$makeHL7->log_HL7_message($hl7_SuperBill_id,'Detailed Financial Transaction','SUP_DFT');
			}
		}
		/*******/

                /*** HL7 DFT Message Saving for Tests 06-June-2018 ***/
                if( $hl7_SuperBill_id && $is_test_interpreted === true && constant('TEST_SUP_DFT_GENERATION')==true )
                {
		    require_once( dirname(__FILE__).'/createTestHl7.php');
                }
                /*** End Test HL7 message generation ***/


		$page_reload="";
		if($_POST["save_form"] != "1"){
			if($super_update_chk==1){

				echo "<script type=\"text/javascript\">
					if(opener.top.fmain.document.getElementById('Superbill_Charges')){
					    opener.top.fmain.changeSelection(opener.top.fmain.document.getElementById('Superbill_Charges'));
					}else{
					    //window.opener.reloadPage();
					    window.opener.top.fmain.location.reload();
					}
				    </script>";



				if($printMode == "1")
				{	//echo "window.location.replace(\"superbill.php?id=".$insertIdSuperBill."&chartid=-1&printMode=1\");";
					echo "<script>window.location.replace('".$GLOBALS["rootdir"]."//chart_notes/requestHandler.php?elem_formAction=SuperBill_Print&e_id=".$encounterId."&neww=1');</script>";
					//echo "window.open('superbill_printing.php?e_id=".$encounterId."','','width=800,height=675,top=10,left=10,scrollbars=yes,resizable=yes,location=no');";
				}else{
					echo "<script>self.close();</script>";
				}
			}elseif(!empty($arG["ret_proc_id"])){
				return $sb_arrCurProcIds;
			}
		}else{
			if($_POST["hid_save_section"] == "accounts"){
			$page_reload ="<script>window.location.replace('".$GLOBALS["rootdir"]."/accounting/acc_superbill.php?id=".$insertIdSuperBill."&enc_id=".$encounterId."');</script>";
			}else{
			$page_reload ="<script>window.opener.top.fmain.location.reload();self.close();</script>";
			}
			echo $page_reload;
		}
	}
}

?>
