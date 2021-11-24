<?php

class SuperBillPrint {

	private $pid, $enc, $showCurrencySymbol;
	public function __construct($pid){
		$this->pid=$pid;
		$this->showCurrencySymbol = $GLOBALS['currency'];
		if(empty($this->showCurrencySymbol)){ $this->showCurrencySymbol = "$"; }
	}

	function getPtSuberBill(){
		$ar=array();
		$oins = new Insurance();
		$patient_id = $this->pid;
		$getchargeListDetailsStr="SELECT formId, vipSuperBill, encounterId, refferingPhysician, insuranceCaseId, dateOfService
							FROM superbill
							WHERE patientId='$patient_id' and del_status='0'";
		$rez=sqlStatement($getchargeListDetailsStr);
		for($i=1; $row=sqlFetchArray($rez); $i++){
			$caseid = $row["insuranceCaseId"];
			$caseid_name = $oins->get_insurance_case_name($caseid);
			$row["ins_case"] = $caseid_name;
			$row["dateOfService"] = wv_formatDate($row["dateOfService"]);
			$ar[] = $row;
		}
		return $ar;
	}

	function getSuberBillInfo(){
		$ret=array();
		$encounter_id=$this->enc;
		$patient_id=$this->pid;

		$getSuperbillDetailsStr="SELECT * FROM superbill
											WHERE patientId='$patient_id'
											AND encounterId='$encounter_id' AND del_status='0'";
		$row = sqlQuery($getSuperbillDetailsStr);
		if($row!=false){
			$ret=$row;
			$type_of_service = ($type_of_service != "") ? $type_of_service : $row['tos'];
			$place_of_service = ($place_of_service != "") ? $place_of_service : $row['pos'];
			$ret["type_of_service"]=$type_of_service;
			$ret["place_of_service"]=$place_of_service;
			$ret["dos"] = wv_formatDate($row["dateOfService"]);
			$ret["time"]=$row['timeSuperBill'];
			$ret["ccExpDate"]=wv_formatDate($row['ccExpDate']);

			if(!empty($row["physicianId"])){
				$oUser = new User($row["physicianId"]);
				$ret["physicianName"]=$oUser->getName();
			}
		}
		return $ret;
	}

	function getProcedureInfo($insFeeColumn, $icd10){
		$encounter_id=$this->enc;
		$patient_id=$this->pid;
		$showCurrencySymbol = $this->showCurrencySymbol;


		$arrFirstColumn = array("New Patient","Establish","Consult");

		$newPatient=array();
		$establish=array();
		$consult=array();
		$others=array();

		$getProceduresPerformedStr="SELECT
								*
								FROM
								superbill
								LEFT JOIN procedureinfo ON procedureinfo.idSuperBill = superbill.idSuperBill ".
								//"LEFT JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt4_code = procedureinfo.cptCode".
								"WHERE superbill.patientId='$patient_id'
								AND superbill.encounterId='$encounter_id'
								AND procedureinfo.delete_status = '0'
								".
								//"ORDER BY cpt_fee_tbl.cpt_desc";
								"ORDER BY procedureinfo.porder ";
		//echo $getProceduresPerformedStr . " - " . $insFeeColumn ;
		$rez=sqlStatement($getProceduresPerformedStr);
		$counter=1;
		while($row=sqlFetchArray($rez)){
			$procedurePracCode=$row['cptCode']; //cptCode
			$cptUnits = $row['units']; //units



			$ocpt = new CPT();
			list($cptFee, $cptDesc) = $ocpt->getCptFee($procedurePracCode, $insFeeColumn);
			$cptTotalCharges = $cptUnits*$cptFee; // Total Charges

			// Check Description
			if(array_search($cptDesc,$arrFirstColumn) !== false)
			{
				if($cptDesc == $arrFirstColumn[0])
				{
					$varStore = "newPatient";
				}
				else if($cptDesc == $arrFirstColumn[1])
				{
					$varStore = "establish";
				}
				else if($cptDesc == $arrFirstColumn[2])
				{
					$varStore = "consult";
				}
			}
			else
			{
				$varStore = "others";
			}

			$oDx = new Dx();
			$arr_dx_codes = unserialize($row['arr_dx_codes']);



			$diagCode1=trim($row['dx1']); //dx1
			$diagCode2=trim($row['dx2']); //dx2
			$diagCode3=trim($row['dx3']); //dx3
			$diagCode4=trim($row['dx4']); //dx4
			$diagCode5=trim($row['dx5']);
			$diagCode6=trim($row['dx6']);
			$diagCode7=trim($row['dx7']);
			$diagCode8=trim($row['dx8']);
			$diagCode9=trim($row['dx9']);
			$diagCode10=trim($row['dx10']);
			$diagCode11=trim($row['dx11']);
			$diagCode12=trim($row['dx12']);

				if(!empty($diagCode1)){
					$tmp = $arr_dx_codes[1];
					$ar_tmp = explode("@*@", $tmp);
					$tmp_id = ($ar_tmp[0]==$diagCode1) ? $ar_tmp[1] : "" ;
					$diagDesc1=$oDx->getDxTableInfo($diagCode1,$icd10, $tmp_id);
				}
				if(!empty($diagCode2)){
					$tmp = $arr_dx_codes[2];
					$ar_tmp = explode("@*@", $tmp);
					$tmp_id = ($ar_tmp[0]==$diagCode2) ? $ar_tmp[1] : "" ;
					$diagDesc2=$oDx->getDxTableInfo($diagCode2,$icd10,$tmp_id);
				}
				if(!empty($diagCode3)){
					$tmp = $arr_dx_codes[3];
					$ar_tmp = explode("@*@", $tmp);
					$tmp_id = ($ar_tmp[0]==$diagCode3) ? $ar_tmp[1] : "" ;
					$diagDesc3=$oDx->getDxTableInfo($diagCode3,$icd10,$tmp_id);
				}
				if(!empty($diagCode4)){
					$tmp = $arr_dx_codes[4];
					$ar_tmp = explode("@*@", $tmp);
					$tmp_id = ($ar_tmp[0]==$diagCode4) ? $ar_tmp[1] : "" ;
					$diagDesc4=$oDx->getDxTableInfo($diagCode4,$icd10,$tmp_id);
				}
				if(!empty($diagCode5)){
					$tmp = $arr_dx_codes[5];
					$ar_tmp = explode("@*@", $tmp);
					$tmp_id = ($ar_tmp[0]==$diagCode5) ? $ar_tmp[1] : "" ;
					$diagDesc5=$oDx->getDxTableInfo($diagCode5,$icd10,$tmp_id);
				}
				if(!empty($diagCode6)){
					$tmp = $arr_dx_codes[6];
					$ar_tmp = explode("@*@", $tmp);
					$tmp_id = ($ar_tmp[0]==$diagCode6) ? $ar_tmp[1] : "" ;
					$diagDesc6=$oDx->getDxTableInfo($diagCode6,$icd10,$tmp_id);
				}
				if(!empty($diagCode7)){
					$tmp = $arr_dx_codes[7];
					$ar_tmp = explode("@*@", $tmp);
					$tmp_id = ($ar_tmp[0]==$diagCode7) ? $ar_tmp[1] : "" ;
					$diagDesc7=$oDx->getDxTableInfo($diagCode7,$icd10,$tmp_id);
				}
				if(!empty($diagCode8)){
					$tmp = $arr_dx_codes[8];
					$ar_tmp = explode("@*@", $tmp);
					$tmp_id = ($ar_tmp[0]==$diagCode8) ? $ar_tmp[1] : "" ;
					$diagDesc8=$oDx->getDxTableInfo($diagCode8,$icd10,$tmp_id);
				}
				if(!empty($diagCode9)){
					$tmp = $arr_dx_codes[9];
					$ar_tmp = explode("@*@", $tmp);
					$tmp_id = ($ar_tmp[0]==$diagCode9) ? $ar_tmp[1] : "" ;
					$diagDesc9=$oDx->getDxTableInfo($diagCode9,$icd10,$tmp_id);
				}
				if(!empty($diagCode10)){
					$tmp = $arr_dx_codes[10];
					$ar_tmp = explode("@*@", $tmp);
					$tmp_id = ($ar_tmp[0]==$diagCode10) ? $ar_tmp[1] : "" ;
					$diagDesc10=$oDx->getDxTableInfo($diagCode10,$icd10,$tmp_id);
				}
				if(!empty($diagCode11)){
					$tmp = $arr_dx_codes[11];
					$ar_tmp = explode("@*@", $tmp);
					$tmp_id = ($ar_tmp[0]==$diagCode11) ? $ar_tmp[1] : "" ;
					$diagDesc11=$oDx->getDxTableInfo($diagCode11,$icd10,$tmp_id);
				}
				if(!empty($diagCode12)){
					$tmp = $arr_dx_codes[12];
					$ar_tmp = explode("@*@", $tmp);
					$tmp_id = ($ar_tmp[0]==$diagCode12) ? $ar_tmp[1] : "" ;
					$diagDesc12=$oDx->getDxTableInfo($diagCode12,$icd10,$tmp_id);
				}

			/*
			if($getchartRow['enc_icd10']=="1"){
				$diagDesc1=$dx_code_title_arr[$diagCode1];
				$diagDesc2=$dx_code_title_arr[$diagCode2];
				$diagDesc3=$dx_code_title_arr[$diagCode3];
				$diagDesc4=$dx_code_title_arr[$diagCode4];
				$diagDesc5=$dx_code_title_arr[$diagCode5];
				$diagDesc6=$dx_code_title_arr[$diagCode6];
				$diagDesc7=$dx_code_title_arr[$diagCode7];
				$diagDesc8=$dx_code_title_arr[$diagCode8];
				$diagDesc9=$dx_code_title_arr[$diagCode9];
				$diagDesc10=$dx_code_title_arr[$diagCode10];
				$diagDesc11=$dx_code_title_arr[$diagCode11];
				$diagDesc12=$dx_code_title_arr[$diagCode12];
			}else{
				$diagDesc1=$arrDxCodeDesc[$diagCode1];
				$diagDesc2=$arrDxCodeDesc[$diagCode2];
				$diagDesc3=$arrDxCodeDesc[$diagCode3];
				$diagDesc4=$arrDxCodeDesc[$diagCode4];
				$diagDesc5=$arrDxCodeDesc[$diagCode5];
				$diagDesc6=$arrDxCodeDesc[$diagCode6];
				$diagDesc7=$arrDxCodeDesc[$diagCode7];
				$diagDesc8=$arrDxCodeDesc[$diagCode8];
				$diagDesc9=$arrDxCodeDesc[$diagCode9];
				$diagDesc10=$arrDxCodeDesc[$diagCode10];
				$diagDesc11=$arrDxCodeDesc[$diagCode11];
				$diagDesc12=$arrDxCodeDesc[$diagCode12];
			}
			*/

			$modifier1=$row['modifier1']; // mod1
			$omod = new Modifier();
			$artmp = $omod->getModifierInfo($modifier1);
			$modDesc1=$artmp["desc"];
			$modPracCode1=$artmp["prac_code"];


			$modifier2=$row['modifier2']; // mod2
			$artmp = $omod->getModifierInfo($modifier2);
			$modDesc2=$artmp["desc"];
			$modPracCode2=$artmp["prac_code"];

			$modifier3=$row['modifier3']; // mod3
			$artmp = $omod->getModifierInfo($modifier3);
			$modDesc3=$artmp["desc"];
			$modPracCode3=$artmp["prac_code"];

			$modifier4=$row['modifier4']; // mod4
			$artmp = $omod->getModifierInfo($modifier4);
			$modDesc4=$artmp["desc"];
			$modPracCode4=$artmp["prac_code"];

			++$seq;

			$ar_tmp = array();
			$ar_tmp["procedurePracCode"] = $procedurePracCode;
			$ar_tmp["cptDesc"] = $cptDesc;
				$ar_tmp_dx = array();
				if($diagCode1){ $ar_tmp_dx[] = array($diagCode1, $diagDesc1); }
				if($diagCode2){ $ar_tmp_dx[] = array($diagCode2, $diagDesc2); }
				if($diagCode3){ $ar_tmp_dx[] = array($diagCode3, $diagDesc3); }
				if($diagCode4){ $ar_tmp_dx[] = array($diagCode4, $diagDesc4); }
				if($diagCode5){ $ar_tmp_dx[] = array($diagCode5, $diagDesc5); }
				if($diagCode6){ $ar_tmp_dx[] = array($diagCode6, $diagDesc6); }
				if($diagCode7){ $ar_tmp_dx[] = array($diagCode7, $diagDesc7); }
				if($diagCode8){ $ar_tmp_dx[] = array($diagCode8, $diagDesc8); }
				if($diagCode9){ $ar_tmp_dx[] = array($diagCode9, $diagDesc9); }
				if($diagCode10){ $ar_tmp_dx[] = array($diagCode10, $diagDesc10); }
				if($diagCode11){ $ar_tmp_dx[] = array($diagCode11, $diagDesc11); }
				if($diagCode12){ $ar_tmp_dx[] = array($diagCode12, $diagDesc12); }
			$ar_tmp["ar_dx"] = $ar_tmp_dx;
				$ar_tmp_md = array();
				if($modifier1){ $ar_tmp_md[] = array($modPracCode1, $modDesc1); }
				if($modifier2){ $ar_tmp_md[] = array($modPracCode2, $modDesc2); }
				if($modifier3){ $ar_tmp_md[] = array($modPracCode3, $modDesc3); }
				if($modifier4){ $ar_tmp_md[] = array($modPracCode4, $modDesc4); }

			$ar_tmp["ar_md"] = $ar_tmp_md;
			$ar_tmp["cptUnits"] = $cptUnits;
			$ar_tmp["fee"] = $showCurrencySymbol.number_format($cptFee,2);
			$ar_tmp["tcharge"] = $showCurrencySymbol.number_format($cptTotalCharges,2);

			array_push($$varStore,$ar_tmp);

		}

		$arrStack = array();
		$arrStack = array_merge($newPatient,$establish,$consult,$others);

		$todays_charges = $showCurrencySymbol.number_format($todaysCharge,2);

		return $arrStack;


	}

	function getFacilityInfo($sch_id, $def_fc_id){
		$ar=array();
		//Logo
		$sql = "SELECT ".
			 "groups_new.group_Telephone, ".
			 "groups_new.group_Fax, ".
			 "facility.logo ".
			 "FROM facility ".
			 "LEFT JOIN groups_new ON(facility.default_group = groups_new.gro_id and groups_new.del_status='0')".
			 "WHERE facility.facility_type = '1'".
			 "LIMIT 0,1";
			 //OR facility.id = '1' - CONDITION REMOVED DUE TO IT IS DISPLAYING WRONG FACILITY LOGO IN UNPROCESSED SUPERBILLS
		$row = sqlQuery($sql);
		if($row != false){
			$ar["facilityPhone"]=$row['group_Telephone'];
			$ar["facilityFax"]=$row['group_Fax'];
			$ar["facilityLogo"]=$row['logo'];
			if(!empty($ar["facilityLogo"])){
				$oSvFile = new SaveFile();
				$pth = $oSvFile->getFilePath($ar["facilityLogo"],"w");
			}
		}

		//Def Facility
		if(!empty($def_fc_id)){
			$default_facility = $def_fc_id;
			$getFacilityDetailsStr="SELECT * FROM pos_facilityies_tbl WHERE pos_facility_id='$default_facility'";
			$getFacilityDetailsRow=sqlQuery($getFacilityDetailsStr);
			if($getFacilityDetailsRow!=false){
				$ar["facilityName"]=$getFacilityDetailsRow['facility_name'];
				$ar["facilityStreet"]=$getFacilityDetailsRow['pos_facility_address'];
				$ar["facilityState"]=$getFacilityDetailsRow['pos_facility_state'];
				$ar["facilityCity"]=$getFacilityDetailsRow['pos_facility_city'];
				$ar["facilityZip"]=$getFacilityDetailsRow['pos_facility_zip'];
			}
		}

		//facility details
		if(!empty($sch_id)){
			$sql ="SELECT sa_facility_id FROM schedule_appointments WHERE id = '".$sch_id."' ";
			$rowSch = sqlQuery($sql);
			if($rowSch!=false && !empty($rowSch["sa_facility_id"]) ){
				$getFacilityDetailsStr="SELECT * FROM facility WHERE id='".$rowSch["sa_facility_id"]."'";
				$getFacilityDetailsRow=sqlQuery($getFacilityDetailsStr);
				if($getFacilityDetailsRow!=false){
					$ar["facilityName"]=$getFacilityDetailsRow['name'];
					$ar["facilityStreet"]=$getFacilityDetailsRow['street'];
					$ar["facilityState"]=$getFacilityDetailsRow['state'];
					$ar["facilityCity"]=$getFacilityDetailsRow['city'];
					$ar["facilityZip"]=$getFacilityDetailsRow['zip_ext'];
				}
			}
		}
		//facility details
		return $ar;
	}

	function getPtInfo(){

		$oPt = new Patient($this->pid);
		$ar_pt = $oPt->getPtInfo();
		extract($ar_pt);
		$ar_pt["patientName"]=$fname." ".$mname." ".$lname;
		$ar_pt["primaryCare"]=$primary_care;
		$ar_pt["patientSex"] = $sex;
		if(empty($ar_pt["phoneHome"])){
			$ar_pt["phoneHome"] = $phone_biz;
		}
		if(empty($ar_pt["phoneHome"])){
			$ar_pt["phoneHome"] = $phone_contact;
		}
		if(empty($ar_pt["phoneHome"])){
			$ar_pt["phoneHome"] = $phone_cell;
		}

		$ar_pt["age"] = $oPt->getAge();

		return $ar_pt;
	}

	function get_resp_party(){
		$ar=array();
		$patient_id = $this->pid;
		$getRespPartyStr="SELECT * FROM resp_party WHERE patient_id='$patient_id'";
		$getRespPartyRow=sqlQuery($getRespPartyStr);
		if($getRespPartyRow!=false && ($getRespPartyRow["relation"] != "self") && !empty($getRespPartyRow["relation"])){
			$fname=$getRespPartyRow['fname'];
			$lname=$getRespPartyRow['lname'];
			$ar["ref_name"]=$fname.", ".$lname;
			$ar["ref_address"]=$getRespPartyRow['address'];
			$ar["ref_homePh"]=$getRespPartyRow['home_ph'];
			$ar["ref_workPh"]=$getRespPartyRow['work_ph'];
			$ar["ref_city"]=$getRespPartyRow['city'];
			$ar["ref_state"]=$getRespPartyRow['state'];
			$ar["ref_postal_code"] = $getRespPartyRow['zip'];
		}else{
			$oPt = new Patient($this->pid);
			$ar_pt = $oPt->getPtInfo();
			extract($ar_pt);
			$ar["ref_name"]=$fname.", ".$lname;
			$ar["ref_address"]=$street.", ".$street2;
			$ar["ref_postal_code"] = $postal_code;
			$ar["ref_homePh"]=$phone_home;
			$ar["ref_workPh"]=$phone_biz;
			$ar["ref_city"]=$city;
			$ar["ref_state"]=$state;
		}
		return $ar;
	}

	function getInsuranceInfo($ins_id, $dos){

		if(empty($ins_id)){
			$oPt = new Patient();
			$ins_id = $oPt->getInuranceCaseId();
		}
		$oins = new Insurance();
		$case_name = $oins->get_insurance_case_name($ins_id, 0);
		$ar_ins_info = $oins->getPtInsuranceInfo($this->pid, $ins_id);
		$insFeeColumn = $oins->getInsFeeColumn($ins_id, $dos);
		$insFeeColumn = (!isset($insFeeColumn) || empty($insFeeColumn)) ? "1" : $insFeeColumn; // default 1
		return array($case_name, $ar_ins_info, $insFeeColumn);
	}

	function get_copay($arr_Ins_info){

		$oadmn = new Admn();
		list($sec_copay_collect_amt, $sec_copay_for_ins) = $oadmn->get_sec_copay_policy();

		$pri_copay = $arr_Ins_info["primary"]["pri_copay"];
		$secCopay = ChkSecCopay_collect($arr_Ins_info["primary"]["providerId"]);
		if($secCopay=='Yes'){
			if($sec_copay_collect_amt>=$arr_Ins_info["secondary"]['copay'] || $sec_copay_for_ins==''){
				$sec_copay=$arr_Ins_info["secondary"]['copay'];
			}else{
				$sec_copay=0.00;
			}
		}else{
			$sec_copay="0.00";
		}
		$CoPay = $pri_copay+$sec_copay;
		return $CoPay;

	}

	function get_follow_up(){
		$encounter_id=$this->enc;
		$patient_id=$this->pid;

		$getPatientFollowupDetailsStr="SELECT a.*, b.* FROM
								chart_assessment_plans a,
								chart_master_table b
								WHERE a.patient_id='$patient_id'
								AND  b.patient_id='$patient_id'
								AND a.form_id=b.id
								AND b.encounterId='$encounter_id'";

		$getPatientFollowupDetailsRow=sqlQuery($getPatientFollowupDetailsStr);
		//Follow Up
		if(!empty($getPatientFollowupDetailsRow['follow_up_numeric_value']) || !empty($getPatientFollowupDetailsRow['follow_up'])){
			$follow_up=$getPatientFollowupDetailsRow['follow_up_numeric_value']." ".$getPatientFollowupDetailsRow['follow_up'];
			$follow_up .= "<br>";
		}

		$retina = $getPatientFollowupDetailsRow['retina'];
		$neuro_ophth = $getPatientFollowupDetailsRow['neuro_ophth'];
		$doctor_name = $getPatientFollowupDetailsRow['doctor_name'];
		if(!empty($retina) || !empty($neuro_ophth) || !empty($doctor_name)){
			$follow_up .="<b>Refer for Consult</b><br> ";
			$follow_up .= ($retina == "1") ? "Retina, " : "" ;
			$follow_up .= ($neuro_ophth == "1") ? "Neuro ophth, " : "" ;
			$follow_up .= $doctor_name;
			$follow_up .= "<br>";
		}

		$continue_meds = $getPatientFollowupDetailsRow['continue_meds'];
		$monitor_ag = $getPatientFollowupDetailsRow['monitor_ag'];
		$id_precation = $getPatientFollowupDetailsRow['id_precation'];
		$rd_precation = $getPatientFollowupDetailsRow['rd_precation'];
		$lid_scrubs_oint = $getPatientFollowupDetailsRow['lid_scrubs_oint'];

		if(!empty($continue_meds) || !empty($monitor_ag) || !empty($id_precation) || !empty($rd_precation) || !empty($lid_scrubs_oint)){
			$follow_up .= ($continue_meds == "1") ? "Continue Meds., " : "" ;
			$follow_up .= ($monitor_ag == "1") ? "Monitor AG, " : "" ;
			$follow_up .= ($id_precation == "1") ? "ID Precautions, " : "" ;
			$follow_up .= ($rd_precation == "1") ? "RD Precautions, " : "" ;
			$follow_up .= ($lid_scrubs_oint == "1") ? "Lid Scrubs & oint " : "" ;
		}

		$encounterId=$getPatientFollowupDetailsRow['encounterId'];
		$plan_notes=$getPatientFollowupDetailsRow['plan_notes'];

		/* Testing */
		$testing = "";
		$hrt = $getPatientFollowupDetailsRow["hrt"];
		$hrtEye = $getPatientFollowupDetailsRow["hrtEye"];
		$oct = $getPatientFollowupDetailsRow["oct"];
		$octEye = $getPatientFollowupDetailsRow["octEye"];
		$avf = $getPatientFollowupDetailsRow["avf"];
		$avfEye = $getPatientFollowupDetailsRow["avfEye"];
		$ivfa = $getPatientFollowupDetailsRow["ivfa"];
		$ivfaEye = $getPatientFollowupDetailsRow["ivfaEye"];
		$dfe = $getPatientFollowupDetailsRow["dfe"];
		$dfeEye = $getPatientFollowupDetailsRow["dfeEye"];
		$photos = $getPatientFollowupDetailsRow["photos"];
		$photosEye = $getPatientFollowupDetailsRow["photosEye"];
		$pachy = $getPatientFollowupDetailsRow["pachy"];
		$pachyEye = $getPatientFollowupDetailsRow["pachyEye"];

		if(!empty($hrt)){
			$testing .= ($hrt == "1") ? "HRT " : "";
			$testing .= (!empty($hrtEye)) ? "($hrtEye)" : "";
			$testing .= ", ";
		}

		if(!empty($oct)){
			$testing .= "OCT: ";
			$testing .= $oct;
			$testing .= (!empty($octEye)) ? "($octEye)" : "";
			$testing .= ", ";
		}

		if(!empty($avf)){
			$testing .= "AVF: ";
			$testing .= $avf;
			$testing .= (!empty($avfEye)) ? "($avfEye)" : "";
			$testing .= ", ";
		}

		if(!empty($ivfa)){
			$testing .= ($ivfa == "1") ? "IVFA " : "";
			$testing .= (!empty($ivfaEye)) ? "($ivfaEye)" : "";
			$testing .= ", ";
		}

		if(!empty($dfe)){
			$testing .= ($dfe == "1") ? "DFE " : "";
			$testing .= (!empty($dfeEye)) ? "($dfeEye)" : "";
			$testing .= ", ";
		}

		if(!empty($photos)){
			$testing .= "Photos: ";
			$testing .= $photos;
			$testing .= (!empty($photosEye)) ? "($photosEye)" : "";
			$testing .= ", ";
		}

		if(!empty($pachy)){
			$testing .= ($pachy == "1") ? "PACHY " : "";
			$testing .= (!empty($pachyEye)) ? "($pachyEye)" : "";
			$testing .= ", ";
		}

		/* Schedule/SX */
		$scheduleSx = "";
		$cat_iol = $getPatientFollowupDetailsRow["cat_iol"];
		$catIolEye = $getPatientFollowupDetailsRow["catIolEye"];
		$yag_cap = $getPatientFollowupDetailsRow["yag_cap"];
		$yagCapEye = $getPatientFollowupDetailsRow["yagCapEye"];
		$slt = $getPatientFollowupDetailsRow["slt"];
		$sltEye = $getPatientFollowupDetailsRow["sltEye"];
		$pi = $getPatientFollowupDetailsRow["pi"];
		$piEye = $getPatientFollowupDetailsRow["piEye"];
		$retinal_laser = $getPatientFollowupDetailsRow["retinal_laser"];
		$retinalLaserEye = $getPatientFollowupDetailsRow["retinalLaserEye"];
		$plan_notes = $getPatientFollowupDetailsRow["plan_notes"];

		if(!empty($cat_iol)){
			$scheduleSx .= ($cat_iol == "1") ? "Cat.IOL " : "";
			$scheduleSx .= (!empty($catIolEye)) ? "($catIolEye)" : "";
			$scheduleSx .= ", ";
		}

		if(!empty($yag_cap)){
			$scheduleSx .= ($yag_cap == "1") ? "Yag.cap " : "";
			$scheduleSx .= (!empty($yagCapEye)) ? "($yagCapEye)" : "";
			$scheduleSx .= ", ";
		}

		if(!empty($slt)){
			$scheduleSx .= ($slt == "1") ? "SLT " : "";
			$scheduleSx .= (!empty($sltEye)) ? "($sltEye)" : "";
			$scheduleSx .= ", ";
		}

		if(!empty($pi)){
			$scheduleSx .= ($pi == "1") ? "PI " : "";
			$scheduleSx .= (!empty($piEye)) ? "($piEye)" : "";
			$scheduleSx .= ", ";
		}

		if(!empty($retinal_laser)){
			$scheduleSx .= ($retinal_laser == "1") ? "Retinal Laser " : "";
			$scheduleSx .= (!empty($retinalLaserEye)) ? "($retinalLaserEye)" : "";
			$scheduleSx .= ", ";
		}

		//
		return array($follow_up, $testing, $scheduleSx, $plan_notes);

	}

	function main(){
		$patient_id = $this->pid;
		$encounter_id=$_GET['e_id'];
		$showCurrencySymbol = $this->showCurrencySymbol;

		$this->enc = $encounter_id;
		$ar_pt = $this->getPtInfo();
		extract($ar_pt);

		$ar_resp_party_info = $this->get_resp_party();
		extract($ar_resp_party_info);

		$arr_pt_sb = $this->getPtSuberBill();
		$arr_enc_sb =  $this->getSuberBillInfo();
		extract($arr_enc_sb);

		$arr_facility = $this->getFacilityInfo($sch_app_id, $default_facility);
		extract($arr_facility);

		list($caseName, $arr_Ins_info, $insFeeColumn) = $this->getInsuranceInfo($insuranceCaseId, $dateOfService);

		$oPtCharges = new PtCharges($patient_id);
		list($amountDue,$insuranceDue,$creditBalance,$tBalance,$lBalance,$lastPaymentDate) = $oPtCharges->getPatientDues();

		//----
		$amountDue = (!empty($amountDue)) ? number_format($amountDue,2) : number_format(0,2);
		$insuranceDue = (!empty($insuranceDue)) ? number_format($insuranceDue,2) : number_format(0,2);
		$creditBalance = (!empty($creditBalance)) ? number_format($creditBalance,2) : number_format(0,2);
		$tBalance = (!empty($tBalance)) ? number_format($tBalance,2) : number_format(0,2);
		$lBalance = (!empty($lBalance)) ? number_format($lBalance,2) : number_format(0,2);
		$lastDateOfPayment = wv_formatDate($lastPaymentDate);
		//---

		//Copay
		$CoPay = $this->get_copay($arr_Ins_info);

		//
		list($paymentAmountLast, $PaidLastDate) = $oPtCharges->getLastPaidInfo();
		list($today_total_pay, $today_pay_method, $today_pay_method_check, $today_pay_method_cc, $today_pay_method_cc_type) = $oPtCharges->getTodayPaidInfo($dateOfService);

		//Procedure info
		$arr_proc_info = $this->getProcedureInfo($insFeeColumn, $sup_icd10);

		//print_r($arr_proc_info);
		$oPt = new PtSchedule($patient_id);
		$ar_future_app = $oPt->getFutureAppointments("2", $dateOfService);


		$todaysCharges_f = $showCurrencySymbol.number_format($todaysCharges,2);

		list($follow_up, $testing, $scheduleSx, $plan_notes) = $this->get_follow_up();

		$tmp = str_replace("\x", "\\x", $GLOBALS['incdir']);
		include($tmp."/chart_notes/superbill_printing.inc.php");
	}

}

?>
