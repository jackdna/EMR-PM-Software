<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: Patient.php
Coded in PHP7
Purpose: This class provides basic functions to done on/by a patient.
Access Type : Include file
*/
?>
<?php
//Patient
class Patient{
	public $pid;
	public $resPt=false;
	public function __construct($id){
		$this->pid = $id ;
	}

	function getName($frmt=""){
		$nm="";
		$sql =	"SELECT c1.id,c1.fname, c1.lname, c1.mname,c1.default_facility,c2.facilityPracCode, ".
				"c1.DOB, c1.suffix, c1.sex ".
				"FROM patient_data c1 ".
				"LEFT JOIN pos_facilityies_tbl c2 ON c2.pos_facility_id = c1.default_facility ".
				"WHERE id = '".$this->pid."' ";
		//$res = $this->db->Execute($sql);
		$res = imw_exec($sql);
		if($res != false){			
			$fn = trim($res["fname"]);
			$mn = trim($res["mname"]);
			$lm = trim($res["lname"]);
			$id = $res["id"];
			$sf = trim($res['suffix']);
			$fc = $res['facilityPracCode'];
			$dob=$res['DOB'];
			$sex=$res['sex'];
			if($frmt=="7"){
				$nm = $lm.", ".$fn." ".strtoupper(substr($mn,0,1))." - ".$id." ";
			}else	if($frmt=="6"){	
				$dob = wv_formatDate($dob,0,0,'show');
				$nm = $lm.", ".$fn." ".strtoupper(substr($mn,0,1))." -- ".$dob;
				
			}else if($frmt=="3" || $frmt=="31"){		//Last Name, Frist Name Middle initial – Pt ID	(DOB – mm/dd/yy, Age (xxx))
				$nm = $lm.", ".$fn." ".strtoupper(substr($mn,0,1))." - ".$id." ";
				
				if(!empty($dob)&&($dob!="0000-00-00")){
					$ag = $this->getAge($dob);
					//$dob = $this->formatDate($dob,1,0,'show');
					$dob = wv_formatDate($dob);
					$nm .= "&nbsp;&nbsp;&nbsp;&nbsp; (DOB - ".$dob.", Age - ".$ag.") ";
				}
				
				if($frmt=="31"){
					if(!empty($sex)){
						$nm .= "&nbsp;".$sex." ";
					}
				}	
				
				if(!empty($fc)) $nm .= "(".$fc.")";
				$nm = trim($nm);
				
			}else if($frmt=="4"){
				
				$mn = (!empty($mn)) ? " ".strtoupper(substr($mn,0,1))." " : " ";
				$nm_1 = trim($fn.$mn.$lm);
				$flgStp=0;
				if(strlen($nm_1)>20){ $nm_1 = substr($nm_1, 0,18).".."; $flgStp=1; }				
				
				$nm = $nm_1." - ".$id;
				
				if($flgStp==0){
					if(!empty($sf)) $nm .= " ".$sf."";
					
					if(!empty($fc)) $nm .= "(".$fc.")";
				}
				
			}else{			
				$nm = $fn." ".$mn." ".$lm." - ".$id." ".$sf;
							
				if($frmt=="2"){ //Patient name – ID DOB (age) (Facility)
					if(!empty($dob)&&($dob!="0000-00-00")){
						$ag = $this->getAge($dob);
						$dob = wv_formatDate($dob,0,0,'show');
						$nm .= " (".$dob.")(".$ag.")";
					}
				}	
				if(!empty($fc)) $nm .= "(".$fc.")";
			}
		}
		return $nm;
	}
	
	function getPtInfo(){
		if($this->resPt==false){
			$sql="SELECT * FROM patient_data WHERE id='".$this->pid."' ";
			//$this->resPt = $this->db->Execute($sql);
			$this->resPt = imw_exec($sql);
		}
		return $this->resPt;
	}
	
	function getAddress(){
		$arrPtInfo = $this->getPtInfo();
		$patientAddressFull = $arrPtInfo["street"];
		if(!empty($arrPtInfo["street2"])){$patientAddressFull .= $arrPtInfo["street2"].", ";}
		if(!empty($arrPtInfo["city"])){$patientAddressFull .= $arrPtInfo["city"].", "; }
		if(!empty($arrPtInfo["state"])){$patientAddressFull .= $arrPtInfo["state"].", ";}
		if(!empty($arrPtInfo["postal_code"])){$patientAddressFull .= $arrPtInfo["postal_code"].", ";}		
		return $patientAddressFull;
	}
	
	
	function getAge($dob="", $v2=""){
		$yr=0;
		if(empty($dob)){
			$sql =	"SELECT DOB ".
					"FROM patient_data ".
					"WHERE id = '".$this->pid."' ";
			//$res = $this->db->Execute($sql);
			$res = imw_exec($sql);
			if($res != false){
				$dob=$res['DOB'];
			}
		}
		if(!empty($dob)&&($dob!="0000-00-00")){
			if($v2==1){
				$yr = get_age($dob);
			}else{
				$dob_time = strtotime($dob);
				$cur_time = strtotime(date("Y-m-d H:i:s"));
				$yr = floor(($cur_time-$dob_time)/(60*60*24*365));
			}
		}
		return $yr;		
	}
	
	public function getSchDoc(){
		$sql = "select sa_doctor_id from schedule_appointments
				where sa_patient_app_status_id not in (201, 18, 203, 19, 20)
				and sa_patient_id = '".$this->pid."' and sa_app_start_date <= '".wv_dt('now')."'
				order by sa_app_start_date desc, sa_app_starttime desc limit 0, 1"; 
		$row = sqlQuery($sql);
		return $row["sa_doctor_id"];
	}

	function getPro4CnIn($uid="",$utp="",$crdt=""){
		$id=0;
		if(empty($uid))$uid = $_SESSION["authId"];
		if(empty($utp)&& $uid!="-1"){
			//Get Type
			$oUsrfun = new User($uid);
			$utp = $oUsrfun->getUType(1);
		}

		//if Provider is creator.
		if($utp == "1"){
			//if a Resident is following a physician then use the follow physician
			if(isset($_SESSION['res_fellow_sess']) && !empty($_SESSION['res_fellow_sess'])){
				return $_SESSION['res_fellow_sess'];
			}else{
				return $uid;
			}
		}

		//create _ Date --
		if(empty($crdt)){
			$crdt = " CURDATE() ";
		}else{
			$crdt = wv_formatDate($crdt,0,0,"insert");
			$crdt = " '".$crdt."' ";
		}

		//get Patient Doctor Appointment for Today
		$sql = "SELECT sa_doctor_id FROM schedule_appointments c1
				INNER JOIN users c2 ON c1.sa_doctor_id=c2.id
				WHERE sa_patient_id='".$this->pid."' AND sa_app_start_date = ".$crdt." AND c1.sa_patient_app_status_id NOT IN('18','203') ";
		
		//$res = $this->db->Execute($sql);
		$res = imw_exec($sql);
		if($res != false){
			if(!empty($res["sa_doctor_id"])){
				$id = $res["sa_doctor_id"];
				
				//
				if(!empty($id)){
					//Get Type
					$oUsrfun = new User($id);
					$utp = $oUsrfun->getUType(1);
					if($utp == "1"){
						return $id;
					}
				}
			}
		}
		
		//if a Tech or Scribe is following a physician then use the follow physician
		if(isset($_SESSION['res_fellow_sess']) && !empty($_SESSION['res_fellow_sess'])){
			return $_SESSION['res_fellow_sess'];
		}

		//Demo Phy
		$id = $this->getPtPrimaryPhy();
		
		// At Last if there is no Provider id
		return $id;
	}

	//Check if Patient has any chart
	function getPtLastChart($stfinal=""){
		$strAct="";
		if($stfinal=="active") $strAct=" AND c1.finalize = '0' ";
		
		$id=0;
		$sql = "SELECT c1.id FROM chart_master_table c1 ".
				//"LEFT JOIN chart_left_cc_history c2 ON c2.form_id = c1.id ".
				"WHERE c1.patient_id = '".$this->pid."' 
				AND c1.delete_status='0' ".
				$strAct.
				"ORDER BY c1.date_of_service DESC, c1.create_dt DESC, c1.id DESC 
				LIMIT 0,1 ";		
		//$res = $this->db->Execute($sql);
		$res = imw_exec($sql);
		if($res != false){
			if(!empty($res["id"])){
				$id = $res["id"];
			}
		}
		return $id;
	}

	//Get Patient Chart id - Latest

	function getPtPrimaryPhy(){
		$id=0;
		$sql = "SELECT providerID
				FROM patient_data c1 
				INNER JOIN users c2 ON c2.id=c1.providerID 
				WHERE c1.id = '".$this->pid."'";
		//$res = $this->db->Execute($sql);
		$res = imw_exec($sql);
		if($res != false){
			
			if(!empty($res["providerID"])){
				$id = $res["providerID"];
			}
			/*else if(!empty($res->fields["created_by"])){
				$id = $res->fields["created_by"];
			}*/ 
		}
		return $id;
	}
	
	function eRxEligiblity(){
		$sql = "select Allow_erx_medicare from copay_policies where policies_id  = 1";
		//$res = $this->db->Execute($sql);
		$res = imw_exec($sql);
		if($res != false && strtolower($res['Allow_erx_medicare']) == 'yes'){
			$sql = "select erx_entry,erx_patient_id from patient_data where id = '".$this->pid."'";
			//$res = $this->db->Execute($sql);
			$res = imw_exec($sql);
			/*$res->fields['erx_entry'] == 1 &&*/ /*Commented on 11-10-12*/
			if($res['erx_patient_id'] != 'null' && $res['erx_patient_id'] != ''){
				return 1;
			}			
		}
		return 0;	
	}
	
	public function getChartFacilityFromSchApp($dos='', $proId=0){
	
		$phse = "";
		if(empty($dos)){
			$dos = " CURDATE() ";
		}else{
			$dos = "'".$dos."'";
		}
		
		$ret=0;
		if(!empty($this->pid)){		
		$sql="select sa_facility_id, sa_doctor_id from schedule_appointments where sa_app_start_date=".$dos." and sa_patient_id='".$this->pid."' and sa_patient_app_status_id NOT IN (203,201,18,19,20) order by sa_app_start_date,sa_app_starttime asc";
		$rez = sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++){
			if(!empty($row["sa_facility_id"]) && (empty($ret) || (!empty($proId) && $row["sa_doctor_id"]==$proId))){				
				$ret = $row["sa_facility_id"];
			}
		}		
		}
		return $ret;
	}
	
	function isAllCNFinalized(){
		$sql = "SELECT id FROM chart_master_table ".
				"WHERE patient_id = '".$this->pid."' ".
				"AND finalize = '0' AND delete_status='0' ";
		$rez = sqlStatement($sql);
		if(imw_num_rows($rez) > 0){
			return false ; // Active
		}
		return true; // Finalized
	}
	
	function getPtActiveFormId(){
		$ret = 0;
		if(empty($this->pid)){ return $ret;}
		$sql = "SELECT id FROM chart_master_table 
				WHERE patient_id ='".$this->pid."' AND finalize = '0' AND delete_status = '0' ";
		//$res=$this->db->Execute($sql);		
		$res = sqlQuery($sql);
		if($res!=false && !empty($res["id"])){
			$ret = $res->fields["id"];
		}		
		return $ret;
	}
	
	public function getChartNoteInfo(){
		$arr = array();
		
		if(!isset($_SESSION["finalize_id"]) || empty($_SESSION["finalize_id"])){

			//Check for Active Records latest
			$sql = "SELECT chart_master_table.* FROM chart_master_table ".
				   //"LEFT JOIN chart_left_cc_history ON chart_left_cc_history.form_id = chart_master_table.id ".
				   "WHERE chart_master_table.patient_id = '".$this->pid."' ".
				   "AND chart_master_table.finalize = '0' ".
				   "AND chart_master_table.delete_status = '0' ".
				   //"ORDER BY IFNULL(chart_left_cc_history.date_of_service,chart_master_table.create_dt) DESC, chart_master_table.id DESC ".
				   "ORDER BY chart_master_table.date_of_service DESC, chart_master_table.id DESC ".
				   "LIMIT 0,1 ";	
			$row = sqlQuery($sql);
			if($row != false){ // Active Chart Notes
				$elem_masterFinalize = $row["finalize"];
				$elem_masterRecordValidity = $row["record_validity"];
				$elem_masterIsSuperBilled = $row["isSuperBilled"];
				$elem_masterPtMedHxReviewed = $row["ptMedHxReviewed"];
				$elem_masterPtMedHxShowed = $row["ptMedHxShowed"];
				$elem_masterPtVisit = $row["ptVisit"];
				$elem_masterTesting = $row["testing"];
				$elem_masterFinalizerId = $row["finalizerId"];
				$elem_masterpurge_status = $row["purge_status"];
				$elem_chartTemplateId = $row["templateId"];
				$form_id = $row["id"];
				$caseId = $row["caseId"];
				$_SESSION["encounter_id"]=$encounterId = $row["encounterId"];
				if(!empty($row["providerId"])) $elem_masterProviderId = $row["providerId"];
				$_SESSION["form_id"] = $form_id;
				$relNum = $row["releaseNumber"];
				$elem_masterFinalDate = "";
				//if(strpos($row["create_dt"],"0000")===false) $elem_dos = $oChrtFun->formatDate($row["create_dt"]);
				if(strpos($row["date_of_service"],"0000")===false) $elem_dos = wv_formatDate($row["date_of_service"]);
				$finalize_flag = 0;
				$isMemo = $row["memo"];
				$isAutoFinalized = $row["autoFinalize"];
				$enc_icd10 = $row['enc_icd10'];
				$cryfwd_form_id = $row['cryfwd_form_id'];
				$service_eligibility = $row['service_eligibility'];
				$time_of_service = $row['time_of_service'];

			}else{

				//Finalized	Records
				//Check All records and decide finailize or not or add new record
				$sql = "SELECT chart_master_table.* FROM chart_master_table ".
					 //"LEFT JOIN chart_left_cc_history ON chart_left_cc_history.form_id = chart_master_table.id ".
					 "WHERE chart_master_table.patient_id = '".$this->pid."' ".
					 "AND chart_master_table.delete_status = '0' ".
					 "ORDER BY chart_master_table.date_of_service DESC, chart_master_table.id DESC ".
					 //"ORDER BY IFNULL(chart_left_cc_history.date_of_service,chart_master_table.create_dt) DESC, chart_master_table.id DESC ".
					 //"ORDER BY chart_master_table.update_date DESC, chart_master_table.id DESC ".
					 "LIMIT 0,1 ";
				$row = sqlQuery($sql);
				if($row == false){
					//Check For Patient First Chart --
					$lastFormId = $this->getPtLastChart();
					if(empty($lastFormId)){
						//include(dirname(__FILE__)."/../make_first_chart_prompt.php");
						global $elem_curPatientName;
						//--
						$oChartPtLock = new ChartPtLock();
						$scva_flgWorkView = 1;
						list($elem_per_vo,$lockUsrId, $chart_showLock, $scva_lock_exam)=$oChartPtLock->get_view_access($scva_flgWorkView);
						$htm_pt_chart_lock=($chart_showLock) ? $oChartPtLock->getPtChartLockHtml($lockUsrId, $scva_lock_exam) : "";
						//--
						include($GLOBALS['incdir']."/chart_notes/view/make_first_chart_prompt.php");
						exit();
					}
					
					//New Pat
					list($form_id,$elem_dos, $encounterId) = $this->createNewChart();
					$elem_dos = wv_formatDate($elem_dos);
					//$form_id = $oChrtFun->getFormId();
					//$elem_dos = $oChrtFun->getDos();
					$_SESSION["encounter_id"] = $encounterId ; 
					//= $oChrtFun->getChartEncounterId();
			
					$_SESSION["form_id"] = $form_id;
					$isExistingPatient=false;
					$finalize_flag = 0;

				}else{
					$elem_masterFinalize = $row["finalize"];
					$elem_masterRecordValidity = $row["record_validity"];
					$elem_masterIsSuperBilled = $row["isSuperBilled"];
					$elem_masterPtMedHxReviewed = $row["ptMedHxReviewed"];
					$elem_masterPtMedHxShowed = $row["ptMedHxShowed"];
					$elem_masterPtVisit = $row["ptVisit"];
					$elem_masterTesting = $row["testing"];
					$elem_masterFinalizerId = $row["finalizerId"];
					$elem_masterpurge_status = $row["purge_status"];
					$elem_chartTemplateId = $row["templateId"];
					//if(strpos($row["create_dt"],"0000")===false) $elem_dos = $oChrtFun->formatDate($row["create_dt"]);
					if(strpos($row["date_of_service"],"0000")===false) $elem_dos = wv_formatDate($row["date_of_service"]);
					$isMemo = $row["memo"];
					$isAutoFinalized = $row["autoFinalize"];
					$enc_icd10 = $row['enc_icd10'];
					$cryfwd_form_id = $row['cryfwd_form_id'];
                    $service_eligibility = $row['service_eligibility'];
                    $time_of_service = $row['time_of_service'];

					if($row["finalize"] == 0){
						//never comes here
						$form_id = $row["id"];
						$caseId = $row["caseId"];
						$_SESSION["encounter_id"] = $encounterId = $row["encounterId"];
						if(!empty($row["providerId"])) $elem_masterProviderId = $row["providerId"];
						$_SESSION["form_id"] = $form_id;
						$relNum = $row["releaseNumber"];
						$finalize_flag = 0;
					}else if($row["finalize"] == 1){
						//always heree
						
						$relNum = $row["releaseNumber"];
						$elem_masterFinalDate = ($row["finalizeDate"] != "0000-00-00 00:00:00") ? $row["finalizeDate"] : $row["update_date"];
						$oCN = new ChartNote($this->pid, $row["id"]);
						list($isReviewable,$isEditable,$iscur_user_vphy) = $oCN->isChartReviewable($_SESSION["authUserID"],1);
						//if($isReviewable == true){
							$form_id = $row["id"];
							$caseId = $row["caseId"];
							$_SESSION["encounter_id"] = $encounterId = $row["encounterId"];
							if(!empty($row["providerId"])) $elem_masterProviderId = $row["providerId"];
							$_SESSION["finalize_id"] = $row["id"];
							$finalize_flag = 1;
							//$relNum = $row["releaseNumber"];
						//}else{
						//	$_SESSION["finalize_id"] = $row["id"];
						//}
					}
				}
			}

		}else{
			//Finalized Records
			$form_id = $_SESSION["finalize_id"];
			$finalize_flag = 1;
			$elem_activeFormId = $this->getPtActiveFormId();
			$oCN = new ChartNote($this->pid, $_SESSION["finalize_id"]);			
			list($isReviewable,$isEditable,$iscur_user_vphy) = $oCN->isChartReviewable($_SESSION["authId"],1);
			$sql = "SELECT * FROM chart_master_table WHERE id= '".$_SESSION["finalize_id"]."' AND delete_status = '0' ";
			$row = sqlQuery($sql);
			if($row != false){

				//if($isReviewable == true){
					$form_id = $row["id"];
					$caseId = $row["caseId"];
					$_SESSION["encounter_id"] = $encounterId = $row["encounterId"];
					if(!empty($row["providerId"])) $elem_masterProviderId = $row["providerId"];
				//}

				$relNum = $row["releaseNumber"];
				$elem_masterFinalize = $row["finalize"];
				$elem_masterRecordValidity = $row["record_validity"];
				$elem_masterIsSuperBilled = $row["isSuperBilled"];
				$elem_masterPtMedHxReviewed = $row["ptMedHxReviewed"];
				$elem_masterPtMedHxShowed = $row["ptMedHxShowed"];
				$elem_masterPtVisit = $row["ptVisit"];
				$elem_masterTesting = $row["testing"];
				$elem_masterFinalizerId = $row["finalizerId"];
				$elem_chartTemplateId = $row["templateId"];
				$elem_masterpurge_status = $row["purge_status"];
				$elem_masterFinalDate = ($row["finalizeDate"] != "0000-00-00 00:00:00") ? $row["finalizeDate"] : $row["update_date"] ;
				//if(strpos($row["create_dt"],"0000")===false) $elem_dos = $oChrtFun->formatDate($row["create_dt"]);
				if(strpos($row["date_of_service"],"0000")===false) $elem_dos = wv_formatDate($row["date_of_service"]);
				$isMemo = $row["memo"];
				$isAutoFinalized = $row["autoFinalize"];
				$enc_icd10 = $row['enc_icd10'];
				$cryfwd_form_id = $row['cryfwd_form_id'];
                $service_eligibility = $row['service_eligibility'];
                $time_of_service = $row['time_of_service'];
			}
		}
		
		//set Visit as procedue+site as per appointment
		if(empty($elem_masterPtVisit) && ($finalize_flag == 0)){
			$optsch = new PtSchedule($this->pid);
			$elem_masterPtVisit = $optsch->getVisitType($row["date_of_service"]); //,$patient_id
		}
		//set Visit as procedue+site as per appointment
		
		//Insurance case id
		if(empty($caseId)){	$caseId=$this->getInuranceCaseId();		}
		
		//return array
		if(isset($elem_masterFinalize)){ $arr["elem_masterFinalize"] = $elem_masterFinalize ;}
		if(isset($elem_masterRecordValidity)){ $arr["elem_masterRecordValidity"] = $elem_masterRecordValidity;}
		if(isset($elem_masterIsSuperBilled)){ $arr["elem_masterIsSuperBilled"] = $elem_masterIsSuperBilled ;}
		if(isset($elem_masterPtMedHxReviewed)){ $arr["elem_masterPtMedHxReviewed"] = $elem_masterPtMedHxReviewed ;}
		if(isset($elem_masterPtMedHxShowed)){ $arr["elem_masterPtMedHxShowed"] = $elem_masterPtMedHxShowed ;}
		if(isset($elem_masterPtVisit)){ $arr["elem_masterPtVisit"] = $elem_masterPtVisit ;}
		if(isset($elem_masterTesting)){ $arr["elem_masterTesting"] = $elem_masterTesting ;}
		if(isset($elem_masterFinalizerId)){ $arr["elem_masterFinalizerId"] = $elem_masterFinalizerId ;}
		if(isset($elem_masterpurge_status)){ $arr["elem_masterpurge_status"] = $elem_masterpurge_status ;}
		if(isset($elem_chartTemplateId)){ $arr["elem_chartTemplateId"] = $elem_chartTemplateId ;}
		if(isset($form_id)){ $arr["form_id"] = $form_id ;}
		if(isset($caseId)){ $arr["caseId"] = $caseId ;}
		if(isset($encounterId)){ $arr["encounterId"] = $encounterId ;}
		if(isset($elem_masterProviderId)){ $arr["elem_masterProviderId"] = $elem_masterProviderId ;}		
		if(isset($relNum)){ $arr["relNum"] = $relNum ;}
		if(isset($elem_masterFinalDate)){ $arr["elem_masterFinalDate"] = $elem_masterFinalDate;}		
		if(isset($elem_dos)){ $arr["elem_dos"] = $elem_dos ;}
		if(isset($finalize_flag)){ $arr["finalize_flag"] = $finalize_flag ;}
		if(isset($isMemo)){ $arr["isMemo"] = $isMemo ;}
		if(isset($isAutoFinalized)){ $arr["isAutoFinalized"] = $isAutoFinalized ;}
		if(isset($enc_icd10)){ $arr["enc_icd10"] = $enc_icd10 ;}
		if(isset($isExistingPatient)){ $arr["isExistingPatient"] = $isExistingPatient;}
		if(isset($isReviewable)){ $arr["isReviewable"] = $isReviewable;}
		if(isset($isEditable)){ $arr["isEditable"] = $isEditable;}
		if(isset($iscur_user_vphy)){ $arr["iscur_user_vphy"] = $iscur_user_vphy;}
		if(isset($elem_activeFormId)){ $arr["elem_activeFormId"] = $elem_activeFormId;}	
		if(isset($cryfwd_form_id)){ $arr["cryfwd_form_id"] = $cryfwd_form_id ;}
		if(isset($service_eligibility)){ $arr["service_eligibility"] = $service_eligibility ;}
		if(isset($time_of_service)){ $arr["time_of_service"] = $time_of_service ;}
		return $arr;
	}
	
	public function createNewChart($tempId=0,$memo=0,$cryfwd_form_id=0){
		$oFacilityfun = new Facility();
		$encounterId = $oFacilityfun->getEncounterId();
		//$this->enctrId = $encounterId;
		
		$cn_providerId = $this->getPro4CnIn();
		$serverId=""; //$this->getServerId();
		$facilityid = $this->getChartFacilityFromSchApp('', $cn_providerId);
		if(empty($facilityid)){$facilityid = $_SESSION['login_facility'];}
		
		$now = wv_dt("now");
		$dos_dt= wv_dt();
		$dos_time= wv_dt("time");
		
		//Final check for active chart
		$sql = "SELECT id, date_of_service, encounterId FROM chart_master_table WHERE patient_id='".$this->pid."' and delete_status='0' AND finalize='0'  ";
		$row=sqlQuery($sql);
		if($row!=false){
			$form_id = $row["id"];
			$elem_dos = $row["date_of_service"];
			$encounterId = $row["encounterId"];
		}else{
		
			$sql = "INSERT INTO chart_master_table (id, patient_id, update_date, finalize, ".
					"encounterId, isSuperBilled,providerId,releaseNumber,create_dt, create_by, ".
					"templateId, memo, date_of_service, time_of_service, serverId, facilityid, cryfwd_form_id ) ".
					"VALUES (NULL, '".$this->pid."', '".$now."', '0', '".$encounterId."', '0', '".$cn_providerId."', '1',".
					"'".$now."','".$_SESSION["authId"]."','".$tempId."','".$memo."', '".$dos_dt."', '".$dos_time."', '".$serverId."', '".$facilityid."', '".$cryfwd_form_id."' ".
					") ";
			$form_id = sqlInsert($sql);	
			$elem_dos	= $dos_dt;
			/*		
			$res = $this->db->Execute($sql);
			if($res !== false){
				$this->fid = $this->db->Insert_ID();
			}
			*/
		}
		
		//check if roll change
		if(isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"])){			
			if($_SESSION["logged_user_type"] == "3" || $_SESSION["logged_user_type"] == "13"){				
				$oRoleAs = new RoleAs();
				$oRoleAs->save_role($form_id);
			}
		}
		
		//--
		
		return array($form_id,$elem_dos, $encounterId);
	}
	
	public function getInuranceCaseId()
	{
		global $isMultiCase;
		$sql = "select ins_caseid from insurance_case where patient_id='".$this->pid."' and  case_status='Open' order by ins_caseid DESC";
		$rez = sqlStatement($sql);
		if($rez!==false)
		{
			$arrAllCaseId = array();
			for($i=0;$row=sqlFetchArray($rez);$i++)
			{
				$arrAllCaseId[] = $row["ins_caseid"];
			}

			if(count($arrAllCaseId) > 1)
			{
				$isMultiCase = true;
				$sql = "SELECT case_type_id FROM schedule_appointments
						WHERE sa_patient_id ='".$this->pid."' AND sa_app_start_date = '".date("Y-m-d")."' 
						AND sa_patient_app_status_id not in(201,203) order by sa_app_start_date DESC LIMIT 0,1 ";
				$row = sqlQuery($sql);
				if($row != false)
				{
					if(array_search($row["case_type_id"],$arrAllCaseId) !== false)
					{
						return $row["case_type_id"];
					}
				}
				else
				{
					/*
					for($i=0;$row=sqlFetchArray($rez);$i++)
					{
						return $row["ins_caseid"];
						break;
					}
					*/
					return "";
				}
			}
			else
			{
				$isMultiCase = false;
				return $arrAllCaseId[0];
			}
		}
		else
		{
			return "";
		}
	}
	
	function getSchRecalls(){
		
		$sttr="";
		$sql = "SELECT par.*, sp.active_status, sp.proc, sp.acronym FROM patient_app_recall par 
				LEFT JOIN slot_procedures sp ON par.procedure_id = sp.id 				
				where par.patient_id='".$this->pid."' AND par.descriptions != 'MUR_PATCH'  
				order by par.recalldate desc ";
		$rez = sqlStatement($sql);
		for($i=0; $row=sqlFetchArray($rez); $i++){
			$recall_Date=wv_formatDate($row['recalldate']);
			$proc_id=$row['procedure_id'];
			$desc=$row['descriptions'];
			$recall_months=$row['recall_months'];
			if($recall_months<10){ $recall_months = "(0".$recall_months." M)"; }else{ $recall_months = "(".$recall_months." M)";}
			$operator=$row['operator'];
			//$operator = getUserFirstName($operator,2);
			//$operator = $operator[1];
			$ousr = new User($operator);
			$ar_usr_nm=$ousr->getName(2);
			$operator=$ar_usr_nm[2];
			
			
			$proc_name = $row['proc'];
			$proc_id = ($proc_id == "import from csv") ? "<i>Import Data</i>" : $proc_name;
			$desc = str_ireplace(array("<br>", "<br >", "</span>", "&lt;br /&gt;", "&lt;br &gt;"),"", $desc);
			$desc2 = (strlen($desc)>14) ? substr($desc, 0, 14).".." : $desc ;
			$proc_id2 = (strlen($proc_id)>8) ? substr($proc_id, 0, 5).".." : $proc_id ;
			
			
			$str2= "".$recall_Date."  ".$proc_id."  ".$desc."  ".$recall_months."  ".$operator;
			$sttr .= "<span title=\"".$str2."\"><b>".$recall_Date."</b>  ".$proc_id2."  ".$desc2."  ".$recall_months."  ".$operator."</span>,<br/>";
			//$sttr .= "<xmp><b>".$recall_Date."</b>  ".$proc_id."  ".$desc."  ".$recall_months."  ".$operator.",<br/></xmp>";
		
		}
		
		if(empty($sttr)){  $sttr="No Recalls";  }
		
		return $sttr;
	}
	
	function getLastDoneDilation(){
		$lastPtDilation = "";
		$formIdDilation = "";
		$cyr = "-".date("y");
		$cdt = date("Y-m-d");
		
		
		$sql = "SELECT ".
			// "DATE_FORMAT(chart_left_cc_history.date_of_service, '%m-%d-%y') as dosDilation, ".
			 //"DATE_FORMAT(chart_master_table.date_of_service, '".getSqlDateFormat('','y')."') as dosDilation, ".
			 "chart_master_table.date_of_service as dosDilation, ".
			 "chart_dialation.exam_date as dateDilation, ".
			 //"DATE_FORMAT(chart_dialation.exam_date, '".getSqlDateFormat('','y')."') as dateDilation1, ".
			 "chart_dialation.pheny10, chart_dialation.pheny25, chart_dialation.mydiacyl1, chart_dialation.mydiacyl5, ".
			 "chart_dialation.tropicanide, chart_dialation.cyclogel, chart_dialation.dilated_other,chart_dialation.dilation, ".
			 "chart_dialation.noDilation, chart_dialation.unableDilation, ".
			 "chart_master_table.id AS form_id, ".
			 "chart_master_table.finalize, ".
			 "chart_master_table.releaseNumber ".
			 "FROM chart_master_table ".
			// "LEFT JOIN chart_left_cc_history ON chart_left_cc_history.form_id = chart_master_table.id ".
			 "INNER JOIN chart_dialation ON chart_dialation.form_id = chart_master_table.id AND chart_dialation.purged='0'  ".
			 "WHERE chart_master_table.patient_id='".$this->pid."'  ".
			 "AND chart_master_table.delete_status='0' ".
			 "AND chart_master_table.purge_status='0' ".
			 //"ORDER BY chart_left_cc_history.date_of_service DESC, chart_master_table.id DESC ".
			 //"ORDER BY IFNULL(chart_left_cc_history.date_of_service,DATE_FORMAT(chart_master_table.create_dt,'%Y-%m-%d')) DESC, chart_master_table.id DESC ".
			 "ORDER BY chart_master_table.date_of_service DESC, chart_master_table.id DESC ".
			 "";
		$rez = sqlStatement($sql);
		for($i=0;$row = sqlFetchArray($rez);$i++){
			
			$flg_dialation_done=0;
			if(!empty($row["dilation"])){			
				$arrD = unserialize($row["dilation"]);
				if(count($arrD)>=1&&(!empty($arrD[0]["dilate"])||!empty($arrD[0]["other_desc"]))){
					$flg_dialation_done=1;
				}
			}		
			
			if( !empty($flg_dialation_done) || (!empty($row["pheny10"]) || !empty($row["pheny25"]) ||
				!empty($row["mydiacyl1"]) || !empty($row["mydiacyl5"]) ||
				!empty($row["tropicanide"]) || !empty($row["cyclogel"]) ||
				!empty($row["dilated_other"]) ||
				!empty($row["noDilation"]) || !empty($row["unableDilation"]) ) 	
				&& (!empty($row["dateDilation"]))){
				$lblNo= (!empty($row["noDilation"]) || !empty($row["unableDilation"])) ? "No " : "";
				
				if(!empty($row["dateDilation"]) && ( $row["dateDilation"] != "0000-00-00" )){
					$lastPtDilation = $lblNo."Dilation (".wv_formatDate($row["dateDilation"],1).")";
					$formIdDilation = $row["form_id"];
					$chartStDilation = ($row["finalize"] == "1") ? "Final" : "Active";
					$relNumDilation = $row["releaseNumber"];
					$curyrDilation = isDt12mOld($cdt, $row["dateDilation"])?"0":"1";
					//(strpos($row["dateDilation1"],$cyr) !== false) ? "1" : "0";
					break;
				}
			}
		}
		return array($lastPtDilation,$formIdDilation,$chartStDilation,$relNumDilation,$curyrDilation);
	}
	
	function isPtInsMedicare($flgPQRI=0){
	
		// PQRI code comes up for all Insurance carriers. ----
		if($flgPQRI==1){
			if(isset($GLOBALS["PQRIforAllClaims"]) && $GLOBALS["PQRIforAllClaims"]==1){
				return true;
			}
		}
		// PQRI code comes up for all Insurance carriers. ----
		
		$ret = false;
		$sql = "SELECT * FROM insurance_data ".
			 "INNER JOIN insurance_companies ON insurance_companies.id = insurance_data.provider ".
			 "WHERE insurance_data.pid='".$this->pid."' ".
			 "AND insurance_data.actInsComp='1' ".
			 "AND insurance_data.type='primary' ". // Ticket 4615
			 "AND (insurance_companies.name = 'MEDICARE' || insurance_companies.in_house_code = 'MEDICARE') ";
		$row = sqlQuery($sql);
		if($row != false){
			$ret = true;
		}
		return $ret;
	}
	
	/*
	function getOcularMedication($medication = '',$type=''){
		$ocular_medi=$ocular_dos=$ocular_sig=$ocular_type=$ocular_titleSite=array();
		//Ocular Medication
		$check_data="select title, destination, type, sig, DATE_FORMAT(date,'%Y-%m-%d') as 'eDate', med_comments, sites, compliant from lists where pid='".$this->pid."'";
		
		//if(!empty($medication) && $medication == "ocular")	
		if(!empty($type)){ 
			$type_more = " OR type = '".$type."' ";
		}
		
		$check_data .= "and (type='4' ".$type_more.")";
		
		/*$check_data .= "AND allergy_status != 'Deleted' AND allergy_status != 'Discontinue' AND allergy_status != 'Stop' 
						ORDER BY begdate DESC
						";*-/
		$check_data .= "AND allergy_status = 'Active' 
						ORDER BY begdate DESC
						";				
		$checkSql = sqlStatement($check_data);
		while($checkl = sqlFetchArray($checkSql)){
			
			//$tmp = "";
			//$tmp = $checkl['title'];
			//$tmp .= (!empty($checkl['destination'])) ? $checkl['destination'] : "";
			$ocular_medi[] = stripslashes($checkl['title']);
			$ocular_sig[] = stripslashes($checkl['sig']);
			$ocular_type[] = stripslashes($checkl['type']);
			$ocular_date[] = stripslashes($checkl['eDate']);
			$ocular_comments[] = stripslashes($checkl['med_comments']);
			$ocular_sites[] = stripslashes($checkl['sites']);
			$ocular_compliant[] = stripslashes($checkl['compliant']);
			$ocular_dosage[] = stripslashes($checkl['destination']);
			$ocular_titleSite[] = trim(stripslashes($checkl['title'])." ".stripslashes($checkl['sites']));

		}
		//$ocular_medi=substr($ocular_medi1,0,strlen($ocular_medi1)-2);

		return array($ocular_medi,$ocular_sig,$ocular_type,$ocular_date,$ocular_comments,$ocular_sites, $ocular_compliant, $ocular_dosage,$ocular_titleSite);
	}
	*/

//--
function getMultiPhy(){
	$strRefPhy = $strPCPPhy = $strCMPhy = $strRet = "";
	$strRefPhyFull = $strPCPPhyFull = $strCMPhyFull = "";
	$intRefPhy = $intPCPPhy = $intCMPhy = 0;
	$arrTemp = array();
	$qrySelpatRefPhy = "select refPhy.FirstName AS rp_fname, refPhy.LastName AS rp_lname, refPhy.Title AS rp_title, credential, refPhy.MiddleName AS rp_mname, pmrf.phy_type,
						refPhy.Address1, refPhy.Address2, refPhy.ZipCode, refPhy.City, refPhy.State, refPhy.comments, refPhy.physician_phone
						from patient_multi_ref_phy pmrf INNER JOIN refferphysician refPhy ON pmrf.ref_phy_id = refPhy.physician_Reffer_id 
						where pmrf.patient_id = '".$this->pid."' and pmrf.phy_type IN (1,2,3,4) and pmrf.status = '0' and refPhy.delete_status = 0
						ORDER BY pmrf.id ";						
	$rsSelpatRefPhy = imw_query($qrySelpatRefPhy);
	if(imw_num_rows($rsSelpatRefPhy) > 0){
		while($rowSelpatRefPhy = imw_fetch_array($rsSelpatRefPhy)){
			$intDBPhyTyp = 0;
			$intDBPhyTyp = $rowSelpatRefPhy["phy_type"];
			if($intDBPhyTyp == 1){
				if(empty($strRefPhy) == true){
					//Referring Physician
					//$strRefPhy = "&nbsp;-&nbsp;".stripslashes(formatName($rowSelpatRefPhy["rp_fname"],$rowSelpatRefPhy["rp_lname"],"","","FLname"));
					$strRefPhy = '';
					$strRefPhy .= $rowSelpatRefPhy['rp_fname'] != '' ? strtoupper(substr($rowSelpatRefPhy['rp_fname'],0,1)).' ':'';
					$strRefPhy .= $rowSelpatRefPhy['rp_lname'] != '' ? trim($rowSelpatRefPhy['rp_lname']).' ':'';					
					$strRefPhy .= $rowSelpatRefPhy['credential'] != '' ? trim($rowSelpatRefPhy['credential']).' ':'';
					
					$strRefPhyFull = '';
					if(!empty($rowSelpatRefPhy['rp_fname'])){ $strRefPhyFull .= $rowSelpatRefPhy['rp_fname']." "; }
					if(!empty($rowSelpatRefPhy['rp_lname'])){ $strRefPhyFull .= $rowSelpatRefPhy['rp_lname']; }
					$strRefPhyFull .= '<br/>';
					if(!empty($rowSelpatRefPhy['Address1'])){ $strRefPhyFull .= $rowSelpatRefPhy['Address1']."<br/>"; }
					if(!empty($rowSelpatRefPhy['Address2'])){ $strRefPhyFull .= $rowSelpatRefPhy['Address2']."<br/>"; }
					
					if(!empty($rowSelpatRefPhy['City'])){ $strRefPhyFull .= $rowSelpatRefPhy['City'].","; }
					if(!empty($rowSelpatRefPhy['State'])){ $strRefPhyFull .= $rowSelpatRefPhy['State'].","; }
					if(!empty($rowSelpatRefPhy['ZipCode'])){ $strRefPhyFull .= $rowSelpatRefPhy['ZipCode'].","; }
					$strRefPhyFull = trim($strRefPhyFull); $strRefPhyFull = trim($strRefPhyFull,",");
					$strRefPhyFull .= '<br/>';
					if(!empty($rowSelpatRefPhy['physician_phone'])){ $strRefPhyFull .= "PH: ".$rowSelpatRefPhy['physician_phone']."<br/>"; }
					if(!empty($rowSelpatRefPhy['comments'])){ $strRefPhyFull .= "Comments: ".$rowSelpatRefPhy['comments']."<br/>"; }
					
					
					
					/*
					if(!isset($GLOBALS['REF_PHY_FORMAT']) || strtolower($GLOBALS['REF_PHY_FORMAT']) != 'boston'){
					$strRefPhy .= $rowSelpatRefPhy['rp_title'] != '' ? trim($rowSelpatRefPhy['rp_title']).' ':'';
					$strRefPhy .= $rowSelpatRefPhy['rp_lname'] != '' ? trim($rowSelpatRefPhy['rp_lname']).', ':'';
					$strRefPhy .= $rowSelpatRefPhy['rp_fname'] != '' ? trim($rowSelpatRefPhy['rp_fname']).' ':'';
					$strRefPhy .= $rowSelpatRefPhy['rp_mname'] != '' ? trim($rowSelpatRefPhy['rp_mname']):'';
					}
					else if(isset($GLOBALS['REF_PHY_FORMAT']) && strtolower($GLOBALS['REF_PHY_FORMAT']) == 'boston'){
					$strRefPhy .= $rowSelpatRefPhy['rp_lname'] != '' ? trim($rowSelpatRefPhy['rp_lname']).', ':'';
					$strRefPhy .= $rowSelpatRefPhy['rp_fname'] != '' ? trim($rowSelpatRefPhy['rp_fname']).' ':'';
					$strRefPhy .= $rowSelpatRefPhy['rp_mname'] != '' ? trim($rowSelpatRefPhy['rp_mname']).' ':'';
					$strRefPhy .= $rowSelpatRefPhy['rp_title'] != '' ? trim($rowSelpatRefPhy['rp_title']):'';
					}
					*/
					$intRefPhy++;
				}
				else{
					$intRefPhy++;
				}
			}
			if((($intDBPhyTyp == 3) || ($intDBPhyTyp == 4))){
				if(empty($strPCPPhy) == true){
					//Primary Care Phy
					//$strPCPPhy = "&nbsp;-&nbsp;".stripslashes(formatName($rowSelpatRefPhy["rp_fname"],$rowSelpatRefPhy["rp_lname"],"","",""));
					$strPCPPhy = '';
					$strPCPPhy .= $rowSelpatRefPhy['rp_fname'] != '' ? strtoupper(substr($rowSelpatRefPhy['rp_fname'],0,1)).' ':'';
					$strPCPPhy .= $rowSelpatRefPhy['rp_lname'] != '' ? trim($rowSelpatRefPhy['rp_lname']).' ':'';					
					$strPCPPhy .= $rowSelpatRefPhy['credential'] != '' ? trim($rowSelpatRefPhy['credential']).' ':'';
					
					$strPCPPhyFull = '';
					if(!empty($rowSelpatRefPhy['rp_fname'])){ $strPCPPhyFull .= $rowSelpatRefPhy['rp_fname']." "; }
					if(!empty($rowSelpatRefPhy['rp_lname'])){ $strPCPPhyFull .= $rowSelpatRefPhy['rp_lname']; }
					$strPCPPhyFull .= '<br/>';
					if(!empty($rowSelpatRefPhy['Address1'])){ $strPCPPhyFull .= $rowSelpatRefPhy['Address1']."<br/>"; }
					if(!empty($rowSelpatRefPhy['Address2'])){ $strPCPPhyFull .= $rowSelpatRefPhy['Address2']."<br/>"; }
					
					if(!empty($rowSelpatRefPhy['City'])){ $strPCPPhyFull .= $rowSelpatRefPhy['City'].","; }
					if(!empty($rowSelpatRefPhy['State'])){ $strPCPPhyFull .= $rowSelpatRefPhy['State'].","; }
					if(!empty($rowSelpatRefPhy['ZipCode'])){ $strPCPPhyFull .= $rowSelpatRefPhy['ZipCode'].","; }
					$strPCPPhyFull = trim($strPCPPhyFull); $strPCPPhyFull = trim($strPCPPhyFull,",");
					$strPCPPhyFull .= '<br/>';
					if(!empty($rowSelpatRefPhy['physician_phone'])){ $strPCPPhyFull .= "PH: ".$rowSelpatRefPhy['physician_phone']."<br/>"; }
					if(!empty($rowSelpatRefPhy['comments'])){ $strPCPPhyFull .= "Comments: ".$rowSelpatRefPhy['comments']."<br/>"; }
					
					/*
					if(!isset($GLOBALS['REF_PHY_FORMAT']) || strtolower($GLOBALS['REF_PHY_FORMAT']) != 'boston'){
					$strPCPPhy .= $rowSelpatRefPhy['rp_title'] != '' ? trim($rowSelpatRefPhy['rp_title']).' ':'';
					$strPCPPhy .= $rowSelpatRefPhy['rp_lname'] != '' ? trim($rowSelpatRefPhy['rp_lname']).', ':'';
					$strPCPPhy .= $rowSelpatRefPhy['rp_fname'] != '' ? trim($rowSelpatRefPhy['rp_fname']).' ':'';
					$strPCPPhy .= $rowSelpatRefPhy['rp_mname'] != '' ? trim($rowSelpatRefPhy['rp_mname']):'';
					}
					else if(isset($GLOBALS['REF_PHY_FORMAT']) && strtolower($GLOBALS['REF_PHY_FORMAT']) == 'boston'){
					$strPCPPhy .= $rowSelpatRefPhy['rp_lname'] != '' ? trim($rowSelpatRefPhy['rp_lname']).', ':'';
					$strPCPPhy .= $rowSelpatRefPhy['rp_fname'] != '' ? trim($rowSelpatRefPhy['rp_fname']).' ':'';
					$strPCPPhy .= $rowSelpatRefPhy['rp_mname'] != '' ? trim($rowSelpatRefPhy['rp_mname']).' ':'';
					$strPCPPhy .= $rowSelpatRefPhy['rp_title'] != '' ? trim($rowSelpatRefPhy['rp_title']):'';
					}
					*/
					$intPCPPhy++;
				}
				else{
					$intPCPPhy++;
				}	
			}
			if($intDBPhyTyp == 2){
				if(empty($strCMPhy) == true){
					//Co-Managed
					//$strCMPhy = "&nbsp;-&nbsp;".stripslashes(formatName($rowSelpatRefPhy["rp_fname"],$rowSelpatRefPhy["rp_lname"],"","","FLname"));
					$strCMPhy = '';
					$strCMPhy .= $rowSelpatRefPhy['rp_fname'] != '' ? strtoupper(substr($rowSelpatRefPhy['rp_fname'],0,1)).' ':'';
					$strCMPhy .= $rowSelpatRefPhy['rp_lname'] != '' ? trim($rowSelpatRefPhy['rp_lname']).' ':'';					
					$strCMPhy .= $rowSelpatRefPhy['credential'] != '' ? trim($rowSelpatRefPhy['credential']).' ':'';
					
					$strCMPhyFull = '';
					if(!empty($rowSelpatRefPhy['rp_fname'])){ $strCMPhyFull .= $rowSelpatRefPhy['rp_fname']." "; }
					if(!empty($rowSelpatRefPhy['rp_lname'])){ $strCMPhyFull .= $rowSelpatRefPhy['rp_lname']; }
					$strCMPhyFull .= '<br/>';
					if(!empty($rowSelpatRefPhy['Address1'])){ $strCMPhyFull .= $rowSelpatRefPhy['Address1']."<br/>"; }
					if(!empty($rowSelpatRefPhy['Address2'])){ $strCMPhyFull .= $rowSelpatRefPhy['Address2']."<br/>"; }
					
					if(!empty($rowSelpatRefPhy['City'])){ $strCMPhyFull .= $rowSelpatRefPhy['City'].","; }
					if(!empty($rowSelpatRefPhy['State'])){ $strCMPhyFull .= $rowSelpatRefPhy['State'].","; }
					if(!empty($rowSelpatRefPhy['ZipCode'])){ $strCMPhyFull .= $rowSelpatRefPhy['ZipCode'].","; }
					$strCMPhyFull = trim($strCMPhyFull); $strCMPhyFull = trim($strCMPhyFull,",");
					$strCMPhyFull .= '<br/>';
					if(!empty($rowSelpatRefPhy['physician_phone'])){ $strCMPhyFull .= "PH: ".$rowSelpatRefPhy['physician_phone']."<br/>"; }
					if(!empty($rowSelpatRefPhy['comments'])){ $strCMPhyFull .= "Comments: ".$rowSelpatRefPhy['comments']."<br/>"; }
					
					/*
					if(!isset($GLOBALS['REF_PHY_FORMAT']) || strtolower($GLOBALS['REF_PHY_FORMAT']) != 'boston'){
					$strCMPhy .= $rowSelpatRefPhy['rp_title'] != '' ? trim($rowSelpatRefPhy['rp_title']).' ':'';
					$strCMPhy .= $rowSelpatRefPhy['rp_lname'] != '' ? trim($rowSelpatRefPhy['rp_lname']).', ':'';
					$strCMPhy .= $rowSelpatRefPhy['rp_fname'] != '' ? trim($rowSelpatRefPhy['rp_fname']).' ':'';
					$strCMPhy .= $rowSelpatRefPhy['rp_mname'] != '' ? trim($rowSelpatRefPhy['rp_mname']):'';
					}
					else if(isset($GLOBALS['REF_PHY_FORMAT']) && strtolower($GLOBALS['REF_PHY_FORMAT']) == 'boston'){
					$strCMPhy .= $rowSelpatRefPhy['rp_lname'] != '' ? trim($rowSelpatRefPhy['rp_lname']).', ':'';
					$strCMPhy .= $rowSelpatRefPhy['rp_fname'] != '' ? trim($rowSelpatRefPhy['rp_fname']).' ':'';
					$strCMPhy .= $rowSelpatRefPhy['rp_mname'] != '' ? trim($rowSelpatRefPhy['rp_mname']).' ':'';
					$strCMPhy .= $rowSelpatRefPhy['rp_title'] != '' ? trim($rowSelpatRefPhy['rp_title']):'';
					}
					*/
					$intCMPhy++;
				}
				else{
					$intCMPhy++;
				}
			}
		}
	}
	$strMoreRefPhy = $strMorePCPPhy = $strMoreCMPhy = "";
	if($intRefPhy > 1){
		$strMoreRefPhy = "&#x25BC;";
	}
	if($intPCPPhy > 1){
		$strMorePCPPhy = "&#x25BC;";
	}
	if($intCMPhy > 1){
		$strMoreCMPhy = "&#x25BC;";
	}
	$strRet = $strRefPhy."!@!".$strPCPPhy."!@!".$strCMPhy."!@!".$strMoreRefPhy."!@!".$strMorePCPPhy."!@!".$strMoreCMPhy."!@!".$strRefPhyFull."!@!".$strPCPPhyFull."!@!".$strCMPhyFull;
	return $strRet;
}

//Optha First Time
function isOpthaFirstTime(){
	$c=0;
	//Optha
	$sql = "SELECT count(ophtha_id) as num FROM ophtha ".
		   "WHERE patient_id = '".$this->pid."' AND ".
		   "(((ophtha_os != '') AND (ophtha_os != '0-0-0:;')) ".
		   "OR ((ophtha_od != '') AND (ophtha_od != '0-0-0:;'))) ";
	$row = sqlQuery($sql);
	if($row != false && $row["num"] >= 1){
		//$ret = ($row["num"] >= 2) ? 0 : 1;
		$c++;
	}
	
	//if($ret == 1){
		//RV drawings
		$sql = "SELECT idoc_drawing_id,id as rv_id,form_id,exm_drawing FROM chart_drawings  ".
			   "WHERE patient_id = '".$this->pid."' AND purged='0' AND ".
			   "((exm_drawing != '' AND exm_drawing != '0-0-0:;') ".
			   " OR (idoc_drawing_id != '0')) ";
		$rez = sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++){
			if(!empty($row["idoc_drawing_id"])){				
				$form_id=$row["form_id"];
				$oCLSDrawingData = new CLSDrawingData();				
				if($oCLSDrawingData->isExamDrawingExits($this->pid,$form_id,$row["rv_id"],"Fundus_Exam")){
					$c++;
				}
			}else if(!empty($row["od_drawing"])||!empty($row["os_drawing"])){
				$c++;
			}
		}		
	//}	

	$ret = ($c>=2) ? 0 : 1;
	return $ret;
}

function getPrimaryCarePhysician()
{
	$sql = "SELECT primary_care_id FROM patient_data WHERE id = '".$this->pid."'";
	$row =  sqlQuery($sql);
	if(($row != false) && ($row["primary_care_id"] != ""))
	{
		return $row["primary_care_id"];
	}
	return "";
}

function getPtChartDos(){
	$sql = "SELECT id, date_of_service, time_of_service FROM chart_master_table where patient_id = '".$this->pid."' AND delete_status = '0' ORDER BY date_of_service  DESC, id DESC ";
	$res = imw_query($sql);
	return $res;	
}

function getPtAlert($for="Charts"){
	$message="";
	$sql_notes_column = "chk_notes_chart_notes AS chk";
	$sql = "select patient_notes AS notes, ".$sql_notes_column." FROM patient_data Where id='".$this->pid."'";
	$row =  sqlQuery($sql);
	if($row!="" && $row["chk"] == "1"){
		$notes = trim($row["notes"]);
		if(!empty($notes)){
			$message = preg_replace("/[\n\r]/","<br/>",$notes);		
		}
	}
	if(!empty($message)){
		$message = nl2br($message);		
		$message=addslashes($message);
		$message = " show_pop_up_pt_alert('".$message."'); ";
	}
	
	return $message;
}
	
function prompt_pt_dos(){
	
	$cur_form_id = $_GET["cur_form_id"];
	$cur_cf_form_id = $_GET["cur_cf_form_id"];
	$el_filter_pro = $_GET["el_filter_pro"];
	$el_filter_facility = $_GET["el_filter_facility"];

	//dos, provide name, facility, type template
	$htm_dos_list = ""; $opt_select_pro=""; $usrs_id=array();
	$opt_select_facility = ""; $facilities_id=array();
	$sql = "SELECT c1.id, c1.date_of_service, c1.time_of_service, c1.providerId, c1.templateId, c1.facilityid, c1.facilityid, cryfwd_form_id,
				c2.temp_name,  c4.fname, c4.mname, c4.lname  
			FROM chart_master_table c1 
			LEFT JOIN chart_template c2 ON c2.id = c1.templateId
			LEFT JOIN users c4 ON c4.id = c1.providerId
			where c1.patient_id = '".$this->pid."' AND c1.id < '".$cur_form_id."'  AND c1.delete_status = '0' AND c1.purge_status='0' 
			ORDER BY c1.date_of_service  DESC, id DESC ";
	$rez = imw_query($sql);	
	for($i=1, $indx=1;$row = sqlFetchArray($rez); $i++){
		if(!empty($row["id"])){
			$frm_id = $row["id"];
			$cryfwd_form_id = $row["cryfwd_form_id"];
			$dbDos = $row["date_of_service"];
			$dos = wv_formatDate($dbDos);
			$temp_name = $row["temp_name"];
			if(empty($temp_name)){ $temp_name = "Comprehensive"; }
			$chartfacilityid = $row["facilityid"];
			
			$providerId = $row["providerId"];
			$pro_nm = "";
			if(!empty($row["fname"])){ $pro_nm .= $row["fname"]." "; }
			if(!empty($row["fname"])){ $pro_nm .= $row["mname"]." "; }
			if(!empty($row["fname"])){ $pro_nm .= $row["lname"]." "; }
			
			//if server id and facility id is empty, get from scheduler appointment if exists
			$fac_nm="";
			$chartfacilityid_sc =  $this->getChartFacilityFromSchApp($dbDos, $providerId);
			if(!empty($chartfacilityid_sc)){$chartfacilityid=$chartfacilityid_sc;}
			if(!empty($chartfacilityid)){
				$ofac=new Facility($chartfacilityid);
				$fac_nm = $ofac->getFacilityName();
			}else{ $fac_nm = "";  }			
			
			$sel = "";$tr_sel="";
			if($cur_cf_form_id == $frm_id){
				$sel =  "checked";
				$tr_sel = " class=\"success\" ";
			}
			
			//
			if(!empty($providerId) || !empty($pro_nm)){
				if(!in_array($providerId, $usrs_id)){
					$chsn = "";
					if(!empty($el_filter_pro) && $providerId == $el_filter_pro){ $chsn = "selected"; }
					$tmp = "<option value=\"".$providerId."\" ".$chsn." >".$pro_nm."</option>";
					$opt_select_pro.=$tmp;
					$usrs_id[] = $providerId;
				}
			}
			
			//
			if(!empty($chartfacilityid) || !empty($fac_nm)){
				if(!in_array($chartfacilityid, $facilities_id)){
					$chsn = "";
					if(!empty($el_filter_facility) && $chartfacilityid == $el_filter_facility){ $chsn = "selected"; }
					$tmp = "<option value=\"".$chartfacilityid."\" ".$chsn." >".$fac_nm."</option>";
					$opt_select_facility.=$tmp;
					$facilities_id[] = $chartfacilityid;
				}
			}

			//
			if(!empty($el_filter_facility) && $chartfacilityid != $el_filter_facility){
				continue;
			}
			if(!empty($el_filter_pro) && $providerId != $el_filter_pro){  
				continue;
			}
			
			
			$htm_dos_list .= "<tr ".$tr_sel.">
						<td>".$indx.".</td>
						<td class=\"hidden\"><div class=\"radio\"><input type=\"radio\" name=\"el_nfc_ds\" id=\"el_nfc_ds".$i."\" value=\"".$frm_id."\" ".$sel." onclick=\"set_new_carry(1)\"><label for=\"el_nfc_ds".$i."\"></label></div></td>
						<td><label class=\"ltyro cur_hnd\" for=\"el_nfc_ds".$i."\">".$dos."</label></td>
						<td><label class=\"ltyro cur_hnd\" for=\"el_nfc_ds".$i."\">".$pro_nm."</label></td>
						<td><label class=\"ltyro cur_hnd\" for=\"el_nfc_ds".$i."\">".$fac_nm."</label></td>
						<td><label class=\"ltyro cur_hnd\" for=\"el_nfc_ds".$i."\">".$temp_name."</label></td>
						</tr>";
			$indx++;
			
		}
	}
	
	if(empty($htm_dos_list)){ $htm_dos_list = "<tr><td colspan=\"6\"> No Previous DOS Found. </td></tr>"; }
	$htm_dos_list = "<table class=\"table table-bordered table-striped\"><tr><th>Sr.</th><th>DOS</th>
				<th ><select id=\"el_filter_pro\" class=\"form-control minimal\" onchange=\"set_new_carry(0)\"><option value=\"\">Provider</option>".$opt_select_pro."</select></th>
				<th ><select id=\"el_filter_facility\" class=\"form-control minimal\" onchange=\"set_new_carry(0)\"><option value=\"\">Facility</option>".$opt_select_facility."</select></th>
				<th>Template</th></tr>".$htm_dos_list."</table>";
		
	
	include($GLOBALS['fileroot']."/interface/chart_notes/view/prompt_pt_dos.php");
}	
	
}
?>