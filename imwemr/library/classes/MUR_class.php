<?php 
	include_once $GLOBALS['srcdir'].'/classes/common_function.php';
	include_once $GLOBALS['srcdir'].'/classes/work_view/wv_functions.php';
	
	class MUR{
		public $patient_id = '';
		
		public function __construct($pid){
			$this->patient_id = $pid;
			$this->dtfrom = $this->dtupto = date('m-d-Y');
			$this->dos = date('Y-m-d');
			$this->provider = $this->get_EP($this->dos,$this->patient_id);
			if(isset($_SESSION['authId']) && trim($_SESSION['authId']) != ''){
				$this->auth_id = $_SESSION['authId'];
				$this->Sch_facility_IDs = $this->get_active_facilities();
			}
		}
		
		public function get_all_mur_data(){
			$return_arr = array();
			//DOS
			$return_arr['dos'] = $this->dos;	
			
			//Provider name
			$return_arr['provider']	= $this->provider;
			
			//Sch. facility id
			$return_arr['Sch_facility_IDs'] = $this->Sch_facility_IDs;
			
			//Patient name details
			$patient_name_arr = $this->core_get_patient_name($this->patient_id);
			$return_arr['patient_name_arr'] = $patient_name_arr;
			$return_arr['patient_name'] = $patient_name_arr[2].', '.$patient_name_arr[1].' '.substr($patient_name_arr[3],0,1).' - '.$this->patient_id;
			
			//Vocabulary
			$temp_res = imw_query("SELECT sex,providerID FROM patient_data WHERE id ='$this->patient_id' LIMIT 0,1");
			$temp_rs = imw_fetch_assoc($temp_res);
			if($this->provider == '' || $this->provider == 0 || !$this->provider){$this->provider = $temp_rs['providerID'];}
			$heshe = 'to ';
			if(strtolower($temp_rs['sex'])=='male')$heshe = 'he can ';
			else if(strtolower($temp_rs['sex'])=='female') $heshe = 'she can ';	
			$return_arr['heshe'] = $heshe;
			
			//Dates
			$currDt = date('Y-m-d');
			list($year,$month,$day) = explode('-',$currDt);
			$ageFrom = date('Y-m-d',mktime(0,0,0,$month,$day,$year-18));
			$EduDocFrom = date('Y-m-d',mktime(0,0,0,$month,$day-90,$year));
			
			$return_arr['currDt'] = $currDt;	
			$return_arr['ageFrom'] = $ageFrom;	
			$return_arr['EduDocFrom'] = $EduDocFrom;
			
			//Pt. MUR details
			$query 	= "SELECT DISTINCT(sh.patient_id) FROM social_history sh 
			JOIN patient_data pd ON (pd.id=sh.patient_id) 
			WHERE sh.patient_id IN($patient_id) AND sh.smoking_status !='' AND pd.DOB<='".$ageFrom."'";
			$ptSmoke = $this->getPtIdFun($query,'patient_id');
			
			$return_arr['ptSmoke'] = $ptSmoke;
			
			$ptMedRecon		= $this->getMedReconcil($this->patient_id);
			$ptEduRes		= $this->getEduResourceToPt($this->patient_id,$EduDocFrom);
			$ptAccess		= $this->getTimelyPtElectHlthInfo($this->patient_id);
			$ptSumCare1		= $this->getSummaryCareRec($this->patient_id,'m1');
			$ptSumCare2		= $this->getSummaryCareRec($this->patient_id,'m2');
			
			$return_arr['ptMedRecon']  = $ptMedRecon;
			$return_arr['ptEduRes']    = $ptEduRes;
			$return_arr['ptAccess']    = $ptAccess;
			$return_arr['ptSumCare1']  = $ptSumCare1;
			$return_arr['ptSumCare2']  = $ptSumCare2;
			

			/*-------CPOE----------*/
			$HandWrittenMeds = $this->getHandWritten_Orders($this->provider,$this->dos,$this->dos,'Meds',$this->patient_id);	
			$ptCPOE			 = $this->getCPOE('Meds',$this->patient_id);
			$ptCPOE2		 = $this->getCPOE('Imaging/Rad',$this->patient_id);
			$ptCPOE3		 = $this->getCPOE('Labs',$this->patient_id);
			
			$return_arr['HandWrittenMeds']  = $HandWrittenMeds;
			$return_arr['ptCPOE']           = $ptCPOE;
			$return_arr['ptCPOE2']          = $ptCPOE2;
			$return_arr['ptCPOE3']          = $ptCPOE3;
			
			/*----CQMs-------*/
			$NQF0018Pt		= $this->getPatientsByAge('18-85',$this->patient_id);
			$NQF0018		= $this->getNQF0018($NQF0018Pt);
			
			$return_arr['NQF0018Pt'] = $NQF0018Pt;
			$return_arr['NQF0018']   = $NQF0018;

			$NQF0022Pt		= $this->getPatientsByAge('66',$this->patient_id);
			$return_arr['NQF0022Pt']   = $NQF0022Pt;
			
			$NQF0421PtA		= $this->getPatientsByAge('65',$this->patient_id);
			$NQF0421A		= $this->NQF0421($NQF0421PtA,$this->dos);
			
			$return_arr['NQF0421PtA'] = $NQF0421PtA;
			$return_arr['NQF0421A']   = $NQF0421A;

			$NQF0421PtB		= $this->getPatientsByAge('18-65',$this->patient_id);
			$NQF0421B		= $this->NQF0421($NQF0421PtB,$this->dos);
			$CMS50V2		= $ptSumCare2; //referral loop.
			$NQF0086Pt		= $this->getPatientsByAge('18',$this->patient_id);
			
			$return_arr['NQF0421PtB']  = $NQF0421PtB;
			$return_arr['NQF0421B']    = $NQF0421B;
			$return_arr['CMS50V2']     = $CMS50V2;
			$return_arr['NQF0086Pt']   = $NQF0086Pt;
			
			$NQF0028a		= $this->getNQF0028a_local($NQF0086Pt);
			$return_arr['NQF0028a']    = $NQF0028a;
			
			$NQF0086		= $this->getNQF0086($NQF0086Pt);
			$NQF0088		= $this->getNQF0088($NQF0086Pt);
			$NQF0089		= $this->getNQF0089($NQF0086Pt);
			
			$return_arr['NQF0086']    = $NQF0086;
			$return_arr['NQF0088']    = $NQF0088;
			$return_arr['NQF0089']    = $NQF0089;
			
			$NQF0055Pt		= $this->getPatientsByAge('18-75',$this->patient_id);
			$NQF0055		= $this->getNQF0055($NQF0055Pt);
			
			$return_arr['NQF0055Pt']  = $NQF0055Pt;
			$return_arr['NQF0055']    = $NQF0055;
			
			$NQF0566Pt		= $this->getPatientsByAge('50',$this->patient_id);
			$NQF0566		= $this->getNQF0566_local($NQF0566Pt);
			$NQF0419		= $this->getNQF0419_local($NQF0086Pt);
			$NQF0087		= $this->getNQF0087_local($NQF0086Pt);
			$NQF0101		= $this->getNQF0101_local($NQF0421PtA);

			$return_arr['NQF0566Pt']  = $NQF0566Pt;			
			$return_arr['NQF0566']    = $NQF0566;			
			$return_arr['NQF0419']    = $NQF0419;			
			$return_arr['NQF0087']    = $NQF0087;			
			$return_arr['NQF0101']    = $NQF0101;

			return $return_arr;	
		}
		
		public function get_EP($dos,$patient_id){
			$ep_id = false;
			$q1 = "SELECT id as form_id, providerId as ep_id FROM chart_master_table 
					WHERE date_of_service = '".$dos."' AND patient_id='".$patient_id."' AND purge_status=0 
					ORDER BY update_date DESC LIMIT 0,1";
			$res1 = imw_query($q1);
			if($res1 && imw_num_rows($res1)==1){
				$rs1 = imw_fetch_array($res1);
				$ep_id 		= $rs1['ep_id'];
				$form_id	= $rs1['form_id'];
				if($ep_id <= 0){
					$q2 = "SELECT doctorId as ep_id FROM chart_assessment_plans WHERE form_id = '".$form_id."' LIMIT 0,1";
					$res2 = imw_query($q2);
					if($res2 && imw_num_rows($res2)==1){
						$rs2 = imw_fetch_array($res2);
						$ep_id 		= $rs2['ep_id'];
						if($ep_id <= 0){
							$q2 = "SELECT sa.sa_doctor_id FROM schedule_appointments sa 
							JOIN previous_status ps ON(ps.sch_id = sa.id) 
							WHERE ps.status = '13' AND sa.sa_app_start_date = '".$dos."' AND sa.sa_app_end_date = '".$dos."' 
							AND sa.sa_patient_app_status_id NOT IN(203,201,18,19,20,3) 
							AND sa.sa_patient_id = '".$patient_id."' 
							ORDER BY ps.status_time DESC LIMIT 0,1";
							$res2 = imw_query($q2);
							if($res2 && imw_num_rows($res2)==1){
								$rs2 = imw_fetch_array($res2);
								$ep_id 		= $rs2['sa_provider_id'];
							}		
						}				
					}			
				}
			}
			return $ep_id;
		}

		public function core_get_patient_name($id){
			$getPatientName = "SELECT id,fname, lname, mname FROM patient_data WHERE id = '$id' ";
			$rsGetPatientName = imw_query($getPatientName);
			if(imw_num_rows($rsGetPatientName)>0) {					
				$rowGetPatientName = imw_fetch_array($rsGetPatientName);
				$ptId = $rowGetPatientName['id'];
				$ptFName = $rowGetPatientName['fname'];
				$ptLName = $rowGetPatientName['lname'];
				$ptMName = $rowGetPatientName['mname'];		
			}	
			($rsGetPatientName) ? imw_free_result($rsGetPatientName) : "";		   
			return array($ptId,$ptFName,$ptLName,$ptMName);	   
		}

		public function getPtIdFun($query,$ptId) {
			$pidArr = array();
			$result = imw_query($query);
			if(imw_num_rows($result) > 0){
				while($row = imw_fetch_array($result)){
					$pidArr[] 	= $rs[$ptId];
				}
				$pidArr = array_unique($pidArr);
			}
			return $pidArr;
		}
		
		public function getMedReconcil($totalPtIDs){//old logic below, new logic pending.
			if($totalPtIDs=='0') return;
			$dtfrom1 = get_date_format($this->dtfrom);
			$dtupto1 = get_date_format($this->dtupto);
			$denoNumExcl = array();
			$ptIDs = $ptIDs2 = array();
			$q1 = "SELECT DISTINCT(patient_id) FROM patient_multi_ref_phy pmrp 
					JOIN patient_data pd ON (pmrp.patient_id=pd.id) 
					JOIN refferphysician rf ON (rf.physician_Reffer_id=pmrp.ref_phy_id) 
					LEFT JOIN users u ON (u.id=pd.providerID AND u.user_npi!=rf.NPI) 
					WHERE pmrp.patient_id IN ($totalPtIDs)"; 

			$ptIDs = $this->getPtIdFun($q1,'patient_id');
			$denoNumExcl['denominator'] = $ptIDs; //No. of cares where the EP was receiving party.
			
			if(count($denoNumExcl['denominator'])>0){
				$totalPtIDs2 = implode(',',$ptIDs);
				$q2 = "SELECT DISTINCT(patient_id) FROM patient_last_examined WHERE patient_id IN($totalPtIDs2) 
						AND LOWER(section_name) IN ('medications','complete') AND save_or_review = '2' 
						AND (DATE_FORMAT(created_date,'%Y-%m-%d') BETWEEN '$dtfrom1' AND '$dtupto1')";
				$ptIDs2 = $this->getPtIdFun($q2,'patient_id');
				$denoNumExcl['numerator'] = $ptIDs2; //No. of cares, for which Reconcillation was performed (med Reviewed).
			}
			$denoNumExcl['exclusion'] = array();
			return $denoNumExcl;
		}
		
		public function getEduResourceToPt($totalPtIDs,$EduDocFrom=''){
			if($totalPtIDs=='0') return;
			$dtfrom1 = get_date_format($this->dtfrom);
			$dtupto1 = get_date_format($this->dtupto);
			if($EduDocFrom!='') $dtfrom1 = $EduDocFrom;
			$denoNumExcl = array();
			$denoNumExcl['denominator'] = explode(',',$totalPtIDs);
			$query 	= "SELECT DISTINCT(p_id) FROM document_patient_rel WHERE p_id IN($totalPtIDs) 
					   AND (DATE_FORMAT(date_time,'%Y-%m-%d') BETWEEN '$dtfrom1' AND '$dtupto1')";    
			$denoNumExcl['numerator'] = $this->getPtIdFun($query,'p_id');
			$denoNumExcl['exclusion'] = array();
			return $denoNumExcl;
		}

		public function getTimelyPtElectHlthInfo($totalPtIDs){
			if($totalPtIDs=='0') return;
			$denoNumExcl = array();
			$denoNumExcl['denominator'] = explode(',',$totalPtIDs);
			$query 	= imw_query("SELECT id FROM patient_data 
						WHERE id IN($totalPtIDs) 
						AND ((username != '' AND password != '') 
							  OR (temp_key!='' AND temp_key_expire!='yes' AND temp_key_chk_val='1' AND ".constant('IPORTAL')."='1')) AND locked = '0'"); 
			$denoNumExcl['numerator'] = array();
			if(imw_num_rows($query) > 0){
				$ptIDs = array();
				while($row = imw_fetch_array($query)){
					$ptIDs[] = $rs['id'];
				}
				$denoNumExcl['numerator'] = $ptIDs;				
			}
			
			$denoNumExcl['exclusion'] = $this->get_excluded_patients($denoNumExcl['numerator'],array('timelyPtElectHlthInfo'));
			return $denoNumExcl;	
		}

		public function get_excluded_patients($numerator,$arr_sections){
			$arr_exc_ptIDs = array();
			foreach($arr_sections as $secVal){
				foreach($numerator as $val){
					if($this->specific_exclusion($val,$secVal,'get')==1){
						$arr_exc_ptIDs[] = $val;
					}
				}
			}
			if(count($arr_exc_ptIDs)>0){
				$arr_exc_ptIDs = array_unique($arr_exc_ptIDs);
			}
			return $arr_exc_ptIDs;
		}
		
		public function specific_exclusion($patient_id,$section,$action,$exclusion_val=0){
			if(empty($patient_id) || empty($section) || empty($action)  || !isset($this->auth_id) || empty($this->auth_id)){return;}
			$exclusion = '';
			$query = imw_query("SELECT exclusion FROM specific_exclusions WHERE patient_id='$patient_id' AND section_name='$section'");
			if(imw_num_rows($query) > 0){
				$rs = imw_fetch_array($query);
				$exclusion = $rs['exclusion'];
			}
			
			if($action=='get'){
				return $exclusion;
			}
			else if($action=='set'){
				$authId = $this->auth_id;
				$onDate = date("Y-m-d H:i:s");
				$query2 = "specific_exclusions SET 
					section_name='$section',
					patient_id='$patient_id',
					exclusion='$exclusion_val',
					operator_id='$authId',
					ondate='$onDate'";		
				if($exclusion == '' && $exclusion_val == 1){
					$query2 = "INSERT INTO ".$query2;
				}else if($exclusion >= 0){
					$query2 = "UPDATE ".$query2." WHERE patient_id='$patient_id' AND section_name='$section'";
				}
				$result2 = imw_query($query2);
				if($result2){return true;}
			}
		}
		
		public function getSummaryCareRec($totalPtIDs,$subSection){
			if($totalPtIDs=='0') return;
			$provider = $this->provider;
			$dtfrom1 = get_date_format($this->dtfrom);
			$dtupto1 = get_date_format($this->dtupto);
			$denoNumExcl = array();
			$q1 = "SELECT DISTINCT(cap.form_id),cap.patient_id FROM chart_assessment_plans cap 
				   JOIN chart_master_table cmt ON (cmt.id=cap.form_id) 
				   WHERE cmt.delete_status='0' AND cmt.purge_status='0' AND cap.doctor_name!='' 
				   AND (cmt.date_of_service BETWEEN '$dtfrom1' AND '$dtupto1') AND cap.patient_id IN ($totalPtIDs) 
				   AND cap.doctorId='$provider'";//echo $q1.'<hr>';
			$denoNumExcl['denominator'] = $this->getPtIdFun($q1,'patient_id');
			$form_ID_arr = $this->getPtIdFun($q1,'form_id');
			
			$totalPtIDs2 = implode(',',$denoNumExcl['denominator']);
			$total_formIDs = implode(',',$form_ID_arr);
			switch($subSection){
				case 'm1';
					$q2="SELECT DISTINCT(patient_id) AS id FROM pt_printed_records 
						  WHERE form_id IN($total_formIDs)  
						  AND sending_application = 'iDoc'";
						 //echo $q2.'<hr>';
					break;
				case 'm2';
					$q2="SELECT dma.patient_id as id FROM direct_messages dm JOIN direct_messages_attachment dma ON (dm.id=dma.direct_message_id) 
						 WHERE (DATE_FORMAT(local_datetime,'%Y-%m-%d') BETWEEN '$dtfrom1' AND '$dtupto1') 
						 AND dm.imedic_user_id = '$provider' AND dma.patient_id IN ($totalPtIDs2)";
						 //echo $q2.'<hr>';
					break;
			}
			$denoNumExcl['denominator'] = $denoNumExcl['denominator'];
			$denoNumExcl['numerator'] = $this->getPtIdFun($q2,'id');
			$denoNumExcl['exclusion'] = array();
			return $denoNumExcl;
		}

		public function getHandWritten_Orders($provider,$dtfrom1,$dtupto1,$type,$totalPtIDs=''){
			$fromCheckList = "";
			if($totalPtIDs==''){
				$fromCheckList = "cap.doctorId = '".$provider."' AND ";
				$ptIDs = $this->get_denominator($provider);
				$totalPtIDs = implode(', ',$ptIDs);
			}
			if($totalPtIDs=='0') return;
			
			$totalHandWritten = 0;
			$field = "cap.rxhandwritten";
			if($type=='Labs') 			$field = "cap.labhandwritten";
			if($type=='Imaging/Rad') 	$field = "cap.imagehandwritten";
			$query = "SELECT SUM($field) AS total_handwritten FROM chart_assessment_plans cap 
			JOIN chart_master_table cmt ON (cap.form_id=cmt.id AND cap.patient_id=cmt.patient_id) 
			WHERE ".$fromCheckList."$field > 0 AND (cmt.date_of_service BETWEEN '$dtfrom1' AND '$dtupto1') 
			AND cmt.patient_id IN ($totalPtIDs)";//echo 'HW: '.$query.'<hr>';
			$result = imw_query($query);
			if(imw_num_rows($result) > 0){
				$rs = imw_fetch_array($result);
				$totalHandWritten = $rs['total_handwritten'];
			}
			return $totalHandWritten;
		}
		
		public function getCPOE($type,$totalPtIDs=''){
			$provider = $this->provider;
			$dtfrom1 = get_date_format($this->dtfrom);
			$dtupto1 = get_date_format($this->dtupto); 
			$denoNumExcl = array();
			$ptIDs1 = array(); $ptIDs2 = array(); $ptIDs3 = array();
			$ptIDsEPrescribe = $ptIDsLab = $ptIDsRad = $ptIDsChartAssPln = array();
			
			//GETTING DENOMINATOR
			if($totalPtIDs==''){
				$totalHandMeds 		= 0;
			}else{
				$totalHandMeds 		= $this->getHandWritten_Orders($provider,$dtfrom1,$dtupto1,$type,$totalPtIDs);	
			}
			$totalCPOEorders 	= $this->getCPOE_Orders($provider,$dtfrom1,$dtupto1,$type,$totalPtIDs);
			$arr_EPrescribe		= $this->getEPrescribe($totalPtIDs);
			$denoNumExcl['denominator'] = $totalHandMeds+$totalCPOEorders;
			$denoNumExcl['numerator'] = $totalCPOEorders;
			if($type=='Meds'){
				$denoNumExcl['denominator'] += intval($arr_EPrescribe['denominator']);
				$denoNumExcl['numerator'] += intval($arr_EPrescribe['numerator']);
			}
			$denoNumExcl['exclusion'] = array();
			return $denoNumExcl;
		}
		
		public function getCPOE_Orders($provider,$dtfrom1,$dtupto1,$type,$totalPtIDs=''){
			if($totalPtIDs==''){
				$ptIDs = $this->get_denominator($provider);
				$totalPtIDs = implode(', ',$ptIDs);
			}
			if($totalPtIDs=='0') return;
			
			$query2 ="SELECT COUNT(order_set_associate_details_id) AS total_orders FROM order_set_associate_chart_notes osacn 
					  JOIN order_set_associate_chart_notes_details osacnd ON (osacnd.order_set_associate_id=osacn.order_set_associate_id) 
					  JOIN order_details od ON (od.id=osacnd.order_id AND od.o_type='".$type."') 
					  JOIN chart_master_table cmt ON (cmt.id = osacn.form_id) 
					  WHERE osacn.delete_status='0' AND osacnd.delete_status='0' AND osacn.patient_id IN ($totalPtIDs) ";
			$query2 .= "AND (DATE_FORMAT(cmt.date_of_service,'%Y-%m-%d') BETWEEN '$dtfrom1' AND '$dtupto1')";
			$result2 = imw_query($query2);
			$results = 0;
			if(imw_num_rows($result2) > 0){
				while($rs2 = imw_fetch_array($result2)){
					$results = $rs2['total_orders'];
				}
			}
			return $results;
		}
		
		public function getEPrescribe($totalPtIDs){
			$provider = $this->provider;
			$dtfrom1 = get_date_format($this->dtfrom);
			$dtupto1 = get_date_format($this->dtupto); 
			$denoNumExcl = array();

			$totalHandWritten = $this->getHandWritten_Orders($provider,$dtfrom1,$dtupto1,'Meds');
			
			$total_erx = 0;
			$query2 = "SELECT SUM(prescriptions) AS total_erx FROM emdeon_erx_count WHERE 
						provider_id='".$provider."' AND (date BETWEEN '$dtfrom1' AND '$dtupto1')";
			$result2 = imw_query($query2);
			if(imw_num_rows($result2) > 0){
				$rs2 = imw_fetch_array($result2);
				$total_erx = $rs2['total_erx'];
			}
			
			$denoNumExcl['denominator'] = intval($totalHandWritten)+intval($total_erx);
			$denoNumExcl['numerator'] = intval($total_erx);
			$denoNumExcl['exclusion'] = array();
			return $denoNumExcl;
		}
		
		public function getPatientsByAge($age='',$commaIDs=''){
			$pts = array(0);
			if(!empty($age)){
				$currDt = date('Y-m-d');
				if($ageMethod=='yes') {
					list($mm,$dd,$yy) = explode('-',$this->dtfrom);
					$currDt = date('Y-m-d',mktime(0,0,0,$mm,$dd,$yy));

				}
				list($year,$month,$day) = explode('-',$currDt);
				
				list($ageFrom,$ageUpto) = explode('-',$age);
				$ageFrom = date('Y-m-d',mktime(0,0,0,$month,$day,$year-$ageFrom));
				$ageUpToQry = "";
				if(isset($ageUpto) || $ageUpto!=''){
					$ageUpto = date('Y-m-d',mktime(0,0,0,$month,$day,$year-$ageUpto));
					$ageUpToQry = " AND DOB>='".$ageUpto."' ";
				}
			}
			$id_query="";
			if($commaIDs!=''){$id_query="id IN (".$commaIDs.") AND ";}
			$q = "SELECT DISTINCT(id) AS patient_id FROM patient_data WHERE ".$id_query."DOB<='".$ageFrom."' $ageUpToQry";
			$res = imw_query($q);
			if($res && imw_num_rows($res)>0){
				$pts = array();
				while($rs=imw_fetch_assoc($res)){
					$pts[] = $rs['patient_id'];
				}
			}
			return implode(',',$pts);
		}
		
		public function aged_get_denominator($provider,$age){
			$patientIDs = $this->get_denominator($provider,$age);
			$totalPtIDs = implode(', ',$patientIDs);
			if(!$totalPtIDs || empty($totalPtIDs)){$totalPtIDs = '0';}
			return $totalPtIDs;
		}
		
		public function getNQF0018($commaPatients=''){
			//CQM #NQF0018 - Hypertension.
			if($totalPtIDs=='0') return;
			$provider = $this->provider;
			$denoNumExcl = array();	$ptIDs	= array(); $ptIDs1 = array(); $ptIDs2 = array();
			$dtfrom1 = get_date_format($this->dtfrom);
			$dtupto1 = get_date_format($this->dtupto);
			//getting patients of age 18 or above
			$totalPtIDs = $this->aged_get_denominator($provider,'18-85');
			if($commaPatients!=''){$totalPtIDs = $commaPatients;}
			
			//PROBLEM DATE <= MEASURE START DATE
			$chkDt = $dtfrom1;
			list($year,$month,$day) = explode('-',$dtfrom1);
			$chkDt = date('Y-m-d',mktime(0,0,0,$month-6,$day,$year));
			
			//getting patients having diagnosis of HYPERTENSION
			$query1 = "SELECT pt_id, GROUP_CONCAT(DISTINCT(status)) AS status FROM pt_problem_list_log WHERE pt_id IN ($totalPtIDs) 
							AND (problem_name RLIKE '401.0|401.1|401.9' OR (LOWER(problem_name) LIKE '%essential%' AND (LOWER(problem_name) LIKE '%hypertension%' OR LOWER(problem_name) LIKE '%hypertention%'))) 
							AND (onset_date BETWEEN '$chkDt' AND '$dtupto1') 
							GROUP BY problem_id HAVING (status='Active')";
			$ptIDs = $this->getPtIdFun($query1,'pt_id');
			
			$query2 = "SELECT pt_id FROM pt_problem_list WHERE pt_id IN ($totalPtIDs) 
							AND (problem_name RLIKE '401.0|401.1|401.9' OR (LOWER(problem_name) LIKE '%essential%' AND (LOWER(problem_name) LIKE '%hypertension%' OR LOWER(problem_name) LIKE '%hypertention%'))) 
							AND (onset_date BETWEEN '$chkDt' AND '$dtupto1') AND LOWER(status)='active'";
			$ptIDs2 = $this->getPtIdFun($query2,'pt_id');

			$ptIDs = array_merge($ptIDs,$ptIDs2);
			unset($ptIDs2);
			
			//getting patients having 1 or more visits.
			$ptIDs1 = $this->get_patients_by_visits(implode(',',$ptIDs),1);
			if(count($ptIDs1)>0){
				$DenoPtIDs = implode(', ',$ptIDs1);
			}
			else{
				return;
				$DenoPtIDs = 0;
			}
			$denoNumExcl['denominator'] = $ptIDs1;
			
			//QUERYING FOR DENOMINATOR EXCLUSION
			//getting patients having diagnosis of .....
			$excluQ1 = "SELECT pt_id, GROUP_CONCAT(DISTINCT(status)) AS status FROM pt_problem_list_log WHERE pt_id IN ($DenoPtIDs) 
							AND LOWER(problem_name) RLIKE 'pregnancy|end stage renal disease|chronic kidney diesease' 
							AND onset_date <= '$dtfrom1' 
							GROUP BY problem_id HAVING (status='Active')";
			$X1ptIDs = $this->getPtIdFun($excluQ1,'pt_id');
			
			//Getting patient having procedure perormed.....
			$DenoPtIDs = implode(',',array_diff($ptIDs1,$X1ptIDs));
			if(strlen($DenoPtIDs)==0){$DenoPtIDs = 0;}	
			$excluQ2 = "SELECT DISTINCT(pid) FROM lists 
						WHERE type IN (5,6) AND 
						LOWER(title) RLIKE 'kidney transplant|dialysis service|dialysis procedure|vascular access for dialysis' 
						AND allergy_status='Active' AND (DATE_FORMAT(begdate,'%Y-%m-%d') BETWEEN '$dtfrom1' AND '$dtupto1') 
						AND pid IN ($DenoPtIDs)";
			$X2ptIDs = $this->getPtIdFun($excluQ2,'pid');
			
			$denoNumExcl['exclusion'] = array_unique(array_merge($X1ptIDs,$X2ptIDs));
			$denoNumExcl['numerator']	= array();
			
			$tempNumPtArr = array_diff($denoNumExcl['denominator'],$denoNumExcl['exclusion']);
			if(count($tempNumPtArr)>0){
				$NumPtIDs = implode(',',$tempNumPtArr);
				//query for numerator. blood pressure recorded.
				if($NumPtIDs != '0'){
					$query = "SELECT vsm.patient_id, COUNT(vsm.patient_id) as cnt_pat FROM vital_sign_master vsm 
							  JOIN vital_sign_patient vsp ON (vsp.vital_master_id=vsm.id) 				  
							  WHERE vsm.patient_id IN($NumPtIDs) 
							  AND vsm.status='0' AND vsp.range_vital > 0 AND ((vsp.vital_sign_id = 1 AND vsp.range_vital < 140) or (vsp.vital_sign_id = 2 AND vsp.range_vital < 90))  
							  GROUP BY vsm.id HAVING cnt_pat>=1";
						$ptIDs2 = $this->getPtIdFun($query,'patient_id');
						if(count($ptIDs2)>0){
							$denoNumExcl['numerator'] 	= array_unique($ptIDs2);
						}
				}
			}
			return $denoNumExcl;
		}
		
		public function NQF0421($patient_id,$dos){
			$return = array();
			$return['denominator'] = $patient_id;
			// CHECK IF BMI CREATED
			if($patient_id!=''){
				$qry="Select DISTINCT(vsm.patient_id), vsm.date_vital, vsp.range_vital as vrv FROM chart_master_table chm 
				JOIN vital_sign_master vsm ON vsm.date_vital = chm.date_of_service 
				JOIN vital_sign_patient vsp ON (vsp.vital_master_id = vsm.id AND vsp.vital_sign_id ='9' AND vsp.range_vital != '') 
				WHERE chm.patient_id IN ($patient_id) AND vsm.patient_id IN ($patient_id) 
				AND (chm.date_of_service BETWEEN '".$dos."' AND '".$dos."')
				AND vsm.status<>'1'";
				$rs = imw_query($qry);
				while($res=imw_fetch_array($rs)){
					if($res['vrv']>=23 && $res['vrv']<30){
						$return['numerator'] = $res['patient_id'];
					}
				}
			}
			return $return;
		}
		
		public function get_pt_elec_access(){
			$return_str = '';
			$q_ptaccess = "SELECT username,password,temp_key,temp_key_expire,temp_key_chk_val,locked,".constant('IPORTAL')." AS iportal 
				FROM patient_data WHERE id = '".$this->patient_id."'";
				
				$res_ptaccess = imw_query($q_ptaccess);
				$do_pt_access = 'regenTempKey';
				if(imw_num_rows($res_ptaccess)==1){
					$rs_ptaccess = imw_fetch_assoc($res_ptaccess);
					if($rs_ptaccess['locked']=='1' && $rs_ptaccess['username'] != '' && $rs_ptaccess['password'] != ''){
						$do_pt_access = 'unlock';
						$return_str .= '<div class="row">
											<div class="col-sm-12">
												<div class="radio radio-inline">
													<input type="radio" name="lockPatient" id="idlockPatient" value="1" checked>
													<label for="idlockPatient">Locked</label>
												</div>
												<div class="radio radio-inline">
													<input type="radio" name="lockPatient" id="idunlockPatient" onClick="saveFun(this);">
													<label for="idunlockPatient">Un-Lock</label>
												</div>	
											</div>
										</div>';
					}else if($rs_ptaccess['iportal']=='1'){
						$return_str .= '<div class="row">
											<div class="col-sm-6">
												<input type="text" name="temp_key" id="temp_key" value="'.trim($rs_ptaccess["temp_key"]).'" class="form-control">
											</div>
											<div class="clearfix"></div>	
											<div class="col-sm-12 pt10">
												<button type="button" class="btn btn-primary btn-sm pointer" onClick="javascript:genTempKey(\'6\',\''.$this->patient_id.'\',\'0\');">Activation Key</button>
												<button type="button" class="btn btn-sm btn-primary" id="tempKeyGiven" onClick="saveFun(this)">&nbsp;Send Activation Key Alert&nbsp;</button>
											</div>	
										</div>';
					}
				}
			return $return_str;	
		}

		public function getNQF0028a_local($patient_id){
			$dos = $this->dos;
			//CMS-Clinical Core #NQF0028 - a. Tobacco Use Assessment, b. Tobacco Cessation intervention.
			$denoNumExcl = array();
			
			//getting final denominator having 1 or more office visits.
			$denoNumExcl['denominator'] = $this->get_patients_by_visits($patient_id,1);
			
			if(count($denoNumExcl['denominator'])>0){
				$totalPtIDs = implode(', ',$denoNumExcl['denominator']);
			}else{
				$totalPtIDs = 0;
			}
			
			//getting date of 24 months before.
			$currDt = $dos; list($year,$month,$day) = explode('-',$currDt); $chkDt = date('Y-m-d',mktime(0,0,0,$month,$day,$year-2));
			$query = "SELECT * FROM social_history WHERE patient_id IN ($totalPtIDs) AND 
						smoking_status != '' AND (modified_on BETWEEN '$currDt' AND '$dos')";
			$result = imw_query($query);
			$numPts = array();
			if($result && imw_num_rows($result)>0){
				while($rs = imw_fetch_assoc($result)){
					$pid = $rs['patient_id'];
					$ss	 = $rs['smoking_status'];
					if(stristr($ss,'never smoked')!==false){
						$numPts[] = $pid;
					}if(stristr($ss,'smoker')!==false){
						$cessDT 	= $rs['offered_cessation_counselling_date'];
						$cessTYPE	= $rs['smoke_counseling'];
						if($cessTYPE=='1'){
							$numPts[] = $pid;	
						}
					}
				}
			}
			$denoNumExcl['numerator'] = array_unique($numPts);
			return $denoNumExcl;
		}

		public function getNQF0086($commaPts = ''){
			//CMS-Clinical Alternative Core #NQF0086 - Primary Open Angle Guaucoma
			$provider = $this->provider;
			$denoNumExcl = array();	$ptIDs	= array();
			$dtfrom1 = get_date_format($this->dtfrom);
			$dtupto1 = get_date_format($this->dtupto);
			
			$totalPtIDs = $this->aged_get_denominator($provider,18);

			if($totalPtIDs!=0){
				$tempDeno = $this->get_patients_by_visits($totalPtIDs,2);//patients having 2 or more office visits.
			}
			if(count($tempDeno)>0){
				$totalPtIDs2 = implode(", ",$tempDeno);
			}else{$totalPtIDs=0;}
			
			if($commaPts != ''){$totalPtIDs = $totalPtIDs2 = $commaPts;}
			
			if($totalPtIDs!=0){
				//to get patients having diagnosis of POAG (Primary Open Angle Glaucoma).
				$query = "SELECT pt_id, GROUP_CONCAT(DISTINCT(status)) AS status FROM pt_problem_list_log 
						  WHERE pt_id IN($totalPtIDs2) 
						  AND ((LOWER(problem_name) like '%glaucoma%' 
								 AND LOWER(problem_name) like '%primary%' 
								 AND LOWER(problem_name) like '%open%' 
								 AND LOWER(problem_name) like '%angle%') 
							   OR (UPPER(problem_name) like '%POAG%')
							   ) 
						  AND (onset_date BETWEEN '$dtfrom1' AND '$dtupto1') 
						  GROUP BY problem_id HAVING (status='Active')";
				$denoPTs1 = $this->getPtIdFun($query,'pt_id');
				
				$query4 = "SELECT pt_id FROM pt_problem_list 
						  WHERE pt_id IN($totalPtIDs2) 
						  AND ((LOWER(problem_name) like '%glaucoma%' 
								 AND LOWER(problem_name) like '%primary%' 
								 AND LOWER(problem_name) like '%open%' 
								 AND LOWER(problem_name) like '%angle%') 
							   OR (UPPER(problem_name) like '%POAG%')
							   ) 
						  AND (onset_date BETWEEN '$dtfrom1' AND '$dtupto1') LOWER(status)='active'";
				$denoPTs = $this->getPtIdFun($query4,'pt_id');
				
				$denoNumExcl['denominator'] = array_unique(array_merge($denoPTs1,$denoPTs));
				$pid = implode(', ',$denoNumExcl['denominator']);
				
				$query2 = "SELECT patient_id, exam_date FROM chart_optic 
						   WHERE patient_id IN($pid) 
						   AND (DATE_FORMAT(exam_date,'%Y-%m-%d') BETWEEN '$dtfrom1' AND '$dtupto1') 
						   AND (cdr_od_summary LIKE '%C:D%' OR cdr_os_summary LIKE '%C:D%')";
				$denoNumExcl['numerator'] = $this->getPtIdFun($query2,'patient_id');
			}
			$denoNumExcl['exclusion'] = $this->get_excluded_patients($denoNumExcl['numerator'],array('NQF0086'));
			return $denoNumExcl;
		}

		public function getNQF0088($commaPts = ''){
			//CMS-Clinical Alternative Core #NQF0088 - Diabetic Retinopathy
			$provider = $this->provider;
			$dtfrom1 = get_date_format($this->dtfrom);
			$dtupto1 = get_date_format($this->dtupto);

			$totalPtIDs = $this->aged_get_denominator($provider,18);
			$denoNumExcl = array();	$ptIDs = array(); $ptIDs2 = array(); $ptIDs3 = array();
			$currDt = date('Y-m-d'); list($year,$month,$day) = explode('-',$currDt); $chkDt = date('Y-m-d',mktime(0,0,0,$month,$day,$year-1));

			if($totalPtIDs!=0){
				$tempDeno = $this->get_patients_by_visits($totalPtIDs,2);//patients having 2 or more office visits.
			}
			if(count($tempDeno)>0){
				$totalPtIDs = implode(", ",$tempDeno);
			}else{$totalPtIDs=0;}
			
			$diab_DX = '250.50|250.51|362.01|362.02|362.03|362.04|362.05|362.06|362.07';
			if($commaPts != ''){$totalPtIDs = $commaPts; $diab_DX = '362.01|362.02|362.03|362.04|362.05|362.06|362.07|E10.311|E10.321|E10.331|E10.341,E10.351|E11.311|E11.321|E11.331|E11.341|E11.351';}
			if($totalPtIDs==0)return $denoNumExcl;
			
			//to get patients having diagnosis of DIABETIC RETINOPATHY
			$query1 = "SELECT DISTINCT(pt_id), GROUP_CONCAT(DISTINCT(status)) as Grp_Status FROM pt_problem_list_log 
						WHERE pt_id IN($totalPtIDs) 
						AND problem_name RLIKE '".$diab_DX."' AND 
						(onset_date BETWEEN '$dtfrom1' AND '$dtupto1') 
						GROUP BY problem_id HAVING (Grp_Status='Active')";
			$tmpARR = $this->getPtIdFun($query1,'pt_id');
			$denoNumExcl['denominator'] = $tmpARR;
			
			$totalPtIDs1 = implode(", ",$denoNumExcl['denominator']);
			//to get patients having FUNDUS EXAM DONE & MACULA EDEMA recorded
			$query2 = "SELECT DISTINCT(crv.patient_id) FROM chart_retinal_exam crv WHERE crv.patient_id IN($totalPtIDs1) 
						AND (LOWER(retinal_od_summary) like '%macular edema%' OR LOWER(retinal_os_summary) like '%macular edema%') 
						AND (LOWER(retinal_od_summary) like '%npdr%' OR LOWER(retinal_os_summary) like '%npdr%') 
						AND crv.exam_date >= '".$chkDt."'";
			$ptIDs2 = $this->getPtIdFun($query2,'patient_id');
			$totalPtIDs2 = implode(", ",$ptIDs2);
			
			$denoNumExcl['numerator'] 	= $ptIDs2;
			$denoNumExcl['exclusion']	= array();//get_excluded_patients($denoNumExcl['numerator'],array('NQF0088'));
			return $denoNumExcl;
		}

		public function getNQF0089($commaPts = ''){
			//CMS-Clinical Alternative Core #NQF0089 - Diabetic Retinopathy, PVC
			$provider = $this->provider;
			$dtfrom1 = get_date_format($this->dtfrom);
			$dtupto1 = get_date_format($this->dtupto);
			$totalPtIDs = $this->aged_get_denominator($provider,18);
			$denoNumExcl = array();	$ptIDs = array(); $ptIDs2 = array(); $ptIDs3 = array();
			list($year,$month,$day) = explode('-',$dtfrom1);
			$chkDt = date('Y-m-d',mktime(0,0,0,$month,$day,$year-1));
			
			$tempDeno = $this->get_patients_by_visits($totalPtIDs,2);	
			$totalPtIDs = implode(", ",$tempDeno);
			$diab_DX = '250.50|250.51|362.01|362.02|362.03|362.04|362.05|362.06|362.07';
			if($commaPts != ''){$totalPtIDs = $commaPts; $diab_DX = '362.01|362.02|362.03|362.04|362.05|362.06|362.07|E10.311|E10.321|E10.331|E10.341,E10.351|E11.311|E11.321|E11.331|E11.341|E11.351';}
			
			if($totalPtIDs==0)return $denoNumExcl;
			
			//to get patients having diagnosis of DIABETIC RETINOPATHY
			$query1 = "SELECT pt_id, GROUP_CONCAT(DISTINCT(status)) AS status FROM pt_problem_list_log 
						WHERE pt_id IN($totalPtIDs) 
						AND problem_name RLIKE '".$diab_DX."' 
						AND (onset_date BETWEEN '$dtfrom1' AND '$dtupto1') 
						  GROUP BY problem_id HAVING (status='Active')";
			$tmpARR = $this->getPtIdFun($query1,'pt_id');
			$denoNumExcl['denominator'] = array_unique($tmpARR);
			
			//to get patients having diagnosis of DIABETIC RETINOPATHY
			$query1 = "SELECT pt_id FROM pt_problem_list 
						WHERE pt_id IN($totalPtIDs) 
						AND problem_name RLIKE '".$diab_DX."' 
						AND (onset_date BETWEEN '$dtfrom1' AND '$dtupto1') AND status='Active'";
			$tmpARR2 = $this->getPtIdFun($query1,'pt_id');echo imw_error();
			$denoNumExcl['denominator'] = array_unique(array_merge($tmpARR,$tmpARR2));
			
			
			$totalPtIDs1 = implode(", ",$denoNumExcl['denominator']);

			//to get patients having FUNDUS EXAM DONE & MACULA EDEMA recorded
			$query2 = "SELECT DISTINCT(crv.patient_id) FROM chart_retinal_exam crv 
						WHERE crv.patient_id IN($totalPtIDs1) 
						AND (LOWER(retinal_od_summary) like '%macular edema%' OR LOWER(retinal_os_summary) like '%macular edema%') 
						AND crv.exam_date >= '".$chkDt."'";
				$result2 = imw_query($query2);
			if(imw_num_rows($result2) > 0){
				while($rs2 		= imw_fetch_array($result2)){
					$ptIDs2[] 	= $rs2['patient_id'];
				}
				$totalPtIDs2 = implode(", ",$ptIDs2);
				
				//to get patients having documented communication REGARDING FINDINGS OF MACULAR OR FUNDUS EXAM WITHING LAST 12 MONTHS.
				$query3 = "SELECT DISTINCT(patient_id) FROM patient_consult_letter_tbl WHERE patient_id IN ($totalPtIDs2) 
							AND status = '0' AND LOWER(templateData) LIKE '%macula%' AND LOWER(templateData) LIKE '%edema%'";
				
				$denoNumExcl['numerator'] 	= $this->getPtIdFun($query3,'patient_id');
			}
			if($commaPts != ''){
				$denoNumExcl['exclusion']=$ptIDs2;
			}else{
				$denoNumExcl['exclusion']	= $this->get_excluded_patients($denoNumExcl['numerator'],array('NQF0089'));
			}
			return $denoNumExcl;
		}

		public function getNQF0055($commaPTs=''){
			//CMS-Clinical Alternative Core #NQF0055 - Diabetic Eye Exam
			$provider = $this->provider;
			$denoNumExcl = array();	$ptIDs = array(); $ptIDs2 = array(); $ptIDs3 = array();
			$dtfrom1 = get_date_format($this->dtfrom);
			$dtupto1 = get_date_format($this->dtupto);
			$totalPtIDs = $this->aged_get_denominator($provider,'18-75');
			$diab_MelDX = '250.00|250.01|250.02';
			if($commaPTs != ''){$totalPtIDs = $commaPTs; $diab_MelDX = '250.00|250.01|250.02|250.03|250.10|250.11|250.12|250.13|250.20|250.21|250.22|250.23|250.30|250.33|250.40|250.43|250.50-250.53|250.60|250.63|250.70|250.73|250.80|250.83|250.90|250.93|362.01|362.02|362.03|362.04|362.05|362.06|362.07|E10.311|E310.319 E10.321|E10.329|E10.331|E331.39|E10.341|E310.349|E10.351|E310.359|E11.311|E311.319|E11.321|E311.329|E11.331|E11,339|E11.341|E11,349| E11.351|E11.359';}
			//PROBLEM DATE <= MEASURE START DATE
			$chkDt = $dtfrom1;
			list($year,$month,$day) = explode('-',$dtfrom1);
			$chkDt = date('Y-m-d',mktime(0,0,0,$month,$day,$year-2));
			
			//getting patients having diagnosis of DIABETESE
			$query1 = "SELECT pt_id, GROUP_CONCAT(DISTINCT(status)) AS status FROM pt_problem_list_log WHERE pt_id IN ($totalPtIDs) 
							AND problem_name RLIKE '".$diab_MelDX."'  
							AND (onset_date BETWEEN '$chkDt' AND '$dtupto1') 
							GROUP BY problem_id HAVING (status='Active')";
			$tmpARR = $this->getPtIdFun($query1,'pt_id');

			//getting patients having diagnosis of DIABETESE
			$query2 = "SELECT pt_id FROM pt_problem_list WHERE pt_id IN ($totalPtIDs) 
							AND problem_name RLIKE '".$diab_MelDX."'  
							AND (onset_date BETWEEN '$chkDt' AND '$dtupto1') AND status='Active'";
			$tmpARR2 = $this->getPtIdFun($query2,'pt_id');
			
			$tempTotal = implode(', ',array_unique(array_merge($tmpARR,$tmpARR2)));
			$denoNumExcl['denominator'] = $this->get_patients_by_visits($tempTotal,1);	
			
			
			$totalPtIDs1 = implode(", ",$denoNumExcl['denominator']);
			
			//chart_vision excluded from array below
			$arr_tables = array('chart_pupil', 'chart_eom', 'chart_external_exam', 'chart_lids', 'chart_lesion', 'chart_lid_pos', 'chart_lac_sys', 'chart_iop', 'chart_gonio', 'chart_dialation','chart_vitreous', 'chart_macula','chart_retinal_exam','chart_periphery','chart_blood_vessels', 
					'chart_conjunctiva', 'chart_cornea', 'chart_ant_chamber','chart_iris','chart_lens');
			//to get patients having FUNDUS EXAM DONE & MACULA EDEMA recorded
			foreach($arr_tables as $table){
				if($table=='chart_pupil'){$ptfield = 'patientId';}else{$ptfield = 'patient_id';}
				$query2 = "SELECT DISTINCT(".$ptfield.") as patient_id FROM ".$table." WHERE ".$ptfield." IN($totalPtIDs1)";
				$result2 = imw_query($query2);
				if(imw_num_rows($result2) > 0){//echo $query2.'<br>';
					while($rs2 		= imw_fetch_array($result2)){
						$ptIDs2[] 	= $rs2['patient_id'];
					}
				}
			}//end of foreach.
			$ptIDs2 = array_unique($ptIDs2);
			$totalPtIDs2 = implode(", ",$ptIDs2);

			//to get exclusions.
			$query3 = "SELECT DISTINCT(ppl.pt_id) FROM pt_problem_list ppl WHERE ppl.pt_id IN($totalPtIDs2) 
						AND ppl.problem_name LIKE '%gestational diabetes%' AND ppl.status='Active'";
			$result3 = imw_query($query3);
			if(imw_num_rows($result3) > 0){
				while($rs3 		= imw_fetch_array($result3)){
					$ptIDs3[] 	= $rs3['pt_id'];
				}
			}
			$denoNumExcl['numerator'] = $ptIDs2;
			if(count($ptIDs3)>0){
				$denoNumExcl['exclusion'] = $ptIDs3;
				$denoNumExcl['numerator'] = array_diff($ptIDs2,$ptIDs3);
			}
			return $denoNumExcl;
		}

		public function getNQF0566_local($NQF0566Pt){
			$dos = $this->dos;
			$denoNumExcl = array();
			//getting patients having diagnosis of "macular denegeration"
			$query2 = "SELECT pt_id FROM pt_problem_list WHERE pt_id IN ($NQF0566Pt) 
							AND problem_name RLIKE ('362.50|362.51|362.52')  
							AND (onset_date BETWEEN '$dos' AND '$dos') AND status='Active'";
			$denoNumExcl['denominator'] = $this->getPtIdFun($query2,'pt_id');//	echo $query2.'<br>'.imw_error();
			$tempTotal = implode(', ',$denoNumExcl['denominator']);
			
			if($tempTotal!='' && $tempTotal!=0){
				$numQ = "SELECT pid,GROUP_CONCAT(title) AS title FROM lists WHERE 
						 pid IN($tempTotal) 
						 AND type in (1,4) 
						 AND allergy_status = 'Active' 
						 AND begdate < '$dos' 
						 GROUP BY pid HAVING LOWER(title) RLIKE ('areds|ocuvite|icaps')";
				$denoNumExcl['numerator'] = $this->getPtIdFun($numQ,'pid');
				if(count($denoNumExcl['numerator'])==0){
					$numQ = "SELECT GROUP_CONCAT(od.name) AS ord_name,DATE_FORMAT(osacnd.created_date,'%Y-%m-%d') AS created_date, 
							osacn.patient_id FROM order_set_associate_chart_notes osacn 
						JOIN order_set_associate_chart_notes_details osacnd ON (osacnd.order_set_associate_id=osacn.order_set_associate_id) 
						JOIN order_details od ON (od.id=osacnd.order_id AND od.o_type='Meds') 
						JOIN chart_master_table cmt ON (cmt.id = osacn.form_id) 
						WHERE osacn.delete_status='0' 
								AND osacnd.delete_status='0' 
								AND osacnd.orders_status IN (0,1) 
								AND osacn.patient_id IN (61079) 
								AND (DATE_FORMAT(osacnd.created_date,'%Y-%m-%d') BETWEEN '2015-07-23' AND '2015-07-23') 
						GROUP BY DATE_FORMAT(osacnd.created_date,'%Y-%m-%d') 
								HAVING (LOWER(ord_name) RLIKE ('areds|ocuvite|icaps|risks and benefits explained') 
										AND LOWER(ord_name) RLIKE ('risks and benefits explained'))";
					$denoNumExcl['numerator'] = $this->getPtIdFun($numQ,'patient_id');
				}
			}
			return $denoNumExcl;	
		}

		public function getNQF0419_local($NQF0086Pt){
			$dos = $this->dos;
			$denoNumExcl = array();
			$denoNumExcl['denominator'] = array($NQF0086Pt);//	echo $query2.'<br>'.imw_error();
			$tempTotal = implode(', ',$denoNumExcl['denominator']);
			
			if($tempTotal!='' && $tempTotal!=0){
				$numQ = "SELECT pid FROM lists WHERE pid IN($tempTotal) AND type in (1,4) AND allergy_status = 'Active'";
				$result = $this->getPtIdFun($numQ,'pid');
				if(is_array($result) && $result[0]!=''){		
					$query2 = "SELECT DISTINCT(patient_id) FROM patient_last_examined WHERE patient_id IN($tempTotal) 
							AND LOWER(section_name) IN ('medications','complete') AND save_or_review = '2' 
							AND (DATE_FORMAT(created_date,'%Y-%m-%d') BETWEEN '$dos' AND '$dos')";
					$denoNumExcl['numerator'] = $this->getPtIdFun($query2,'patient_id');echo imw_error();
				}
			}
			return $denoNumExcl;
		}
		
		function getNQF0087_local($NQF0086Pt){
			$dos = $this->dos;
			$denoNumExcl = array();
			//getting patients having diagnosis of "macular denegeration"
			$query2 = "SELECT pt_id FROM pt_problem_list WHERE pt_id IN ($NQF0086Pt) 
							AND problem_name RLIKE ('362.50|362.51|362.52|H35.30|H35.31|H35.32')  
							AND (onset_date BETWEEN '$dos' AND '$dos') AND status='Active'";
			$denoNumExcl['denominator'] = $this->getPtIdFun($query2,'pt_id');//	echo $query2.'<br>'.imw_error();
			$tempTotal = implode(', ',$denoNumExcl['denominator']);
			
			if($tempTotal!='' && $tempTotal!=0){
				$query2 = "SELECT DISTINCT(crv.patient_id) FROM chart_retinal_exam crv WHERE crv.patient_id IN($tempTotal) 
							AND ((LOWER(retinal_od_summary) like '%macular edema%' OR LOWER(retinal_os_summary) like '%macular edema%') 
							OR (LOWER(retinal_od_summary) like '%amd;%' OR LOWER(retinal_os_summary) like '%amd;%')) 
							AND crv.exam_date >= '".$chkDt."'";
				$denoNumExcl['numerator'] = $this->getPtIdFun($query2,'patient_id');
			}
			return $denoNumExcl;	
		}

		public function getNQF0101_local($NQF0086Pt){
			$dos = $this->dos;
			$denoNumExcl = array();
			$denoNumExcl['denominator'] = array($NQF0086Pt);
			$tempTotal = implode(', ',$denoNumExcl['denominator']);
			
			if($tempTotal!='' && $tempTotal!=0){
				$query2 = "SELECT DISTINCT(patient_id) FROM general_medicine WHERE patient_id IN ($tempTotal) AND chk_fall_risk_assd='1'";
				$denoNumExcl['numerator'] = $this->getPtIdFun($query2,'patient_id');
			}
			return $denoNumExcl;	
		}	
		
		public function get_patients_by_visits($patients, $no_of_visits = 0, $ageMethod='',$begDate='',$excludeTemplate='',$begEndDate=''){
			$dtfrom1 = get_date_format($this->dtfrom);
			$dtupto1 = get_date_format($this->dtupto);
			if($begDate!=''){$dtfrom1 = $begDate;}
			if($begEndDate!=''){$dtupto1 = $begEndDate;}
			
			$ptIDs = array(); $ptIDs1 = array(); $arr_key_val = array();
			if($ageMethod=='yes'){
				$comma_patients = implode(', ',$patients[0]);
				for($i=0; $i<count($patients[0]); $i++){
					$arr_key_val[$patients[0][$i]] = $patients[1][$i];
				}
			}else{
				$comma_patients = $patients;
			}

			$querypart1 = "";	
			if($excludeTemplate!=''){
				$querypart1 = " AND templateId NOT IN ($excludeTemplate)";
			}
			$query = "SELECT count(id) cnt, patient_id FROM chart_master_table WHERE patient_id IN($comma_patients) AND (date_of_service>='".$dtfrom1."' AND date_of_service<='".$dtupto1."')".$querypart1." GROUP BY patient_id HAVING cnt >= '$no_of_visits'";
			$result = imw_query($query);
			if(imw_num_rows($result) > 0){
				while($rs = imw_fetch_array($result)){
					$ptIDs[] = $rs['patient_id'];
				}
				if($ageMethod != 'yes'){
					return $ptIDs;
				}
				else{
					for($j=0; $j<count($ptIDs); $j++){
						if(array_key_exists($ptIDs[$j],$arr_key_val)){
							$ptIDs1[$ptIDs[$j]] = $arr_key_val[$ptIDs[$j]];
						}
					}
					return $ptIDs1;
				}
			}
			else{
				return $ptIDs;
			}
		}	
		
		public function get_active_facilities(){
			$fac_IDs = '';
			$q = imw_query("SELECT id FROM facility");
			if(imw_num_rows($q) > 0){
				$temp_arr_facs = array();
				while($rs = imw_fetch_array($q)){
					$temp_arr_facs[] = $rs['id'];
				}
				$fac_IDs = implode(',',$temp_arr_facs);		
			}
			return $fac_IDs;
		}


		public function get_denominator($pro_id, $age='',$ageMethod=''){
			$proIDs = $pro_id;//implode(', ',$pro_id);
			$chkDt='';
			$ptIDs = array(); $ptAge = array();
			if(!empty($age)){
				$currDt = date('Y-m-d');
				if($ageMethod=='yes') {
					list($mm,$dd,$yy) = explode('-',$this->dtfrom);
					$currDt = date('Y-m-d',mktime(0,0,0,$mm,$dd,$yy));

				}
				list($year,$month,$day) = explode('-',$currDt);
				
				list($ageFrom,$ageUpto) = explode('-',$age);
				$ageFrom = date('Y-m-d',mktime(0,0,0,$month,$day,$year-$ageFrom));
				$ageUpToQry = "";
				if(isset($ageUpto) || $ageUpto!=''){
					$ageUpto = date('Y-m-d',mktime(0,0,0,$month,$day,$year-$ageUpto));
					$ageUpToQry = " AND pd.DOB>='".$ageUpto."' ";
				}
			}
			$dtfrom1 = get_date_Format($this->dtfrom);
			$dtupto1 = get_date_format($this->dtupto);
			if(empty($age)) {
				$query = "SELECT distinct(appt.sa_patient_id) FROM schedule_appointments appt 
							INNER JOIN patient_data pd ON (pd.pid=appt.sa_patient_id AND pd.lname != 'doe') 
							WHERE appt.sa_doctor_id IN (".$proIDs.") 
							AND pd.id <> 0 AND pd.pid <> 0 
							AND appt.sa_patient_app_status_id NOT IN(203,201,18,19,20,3) AND appt.sa_facility_id IN ($this->Sch_facility_IDs) 
							AND date_format(appt.sa_app_start_date,'%Y-%m-%d')>='".$dtfrom1."' 
							AND date_format(appt.sa_app_start_date,'%Y-%m-%d')<='".$dtupto1."' ";
			}else{
				$query = "SELECT distinct(appt.sa_patient_id), pd.DOB as dob FROM schedule_appointments appt 
							INNER JOIN patient_data pd ON (pd.pid=appt.sa_patient_id AND pd.lname != 'doe' AND pd.DOB<='".$ageFrom."' $ageUpToQry)
							WHERE appt.sa_doctor_id IN (".$proIDs.") 
							AND pd.id <> 0 AND pd.pid <> 0 
							AND appt.sa_patient_app_status_id NOT IN(203,201,18,19,20,3) AND appt.sa_facility_id IN ($this->Sch_facility_IDs) 
							AND date_format(appt.sa_app_start_date,'%Y-%m-%d')>='".$dtfrom1."' 
							AND date_format(appt.sa_app_start_date,'%Y-%m-%d')<='".$dtupto1."' ";
			}
			$result = imw_query($query);
			if(imw_num_rows($result) > 0){
				while($rs = imw_fetch_array($result)){
					if(in_array($rs['sa_patient_id'],$ptIDs)) continue;
					$ptIDs[] = $rs['sa_patient_id'];
					$ptAge[] = $rs['dob'];
				}
				if($ageMethod=='yes'){return array($ptIDs,$ptAge);}
				else{return $ptIDs;}	
			}else{
				return $ptIDs;
			}
		}

		public function hashPassword($pass){
			if(!empty($pass)){
				if(HASH_METHOD){
					if(HASH_METHOD=='MD5' && !$this->isValidMd5($pass)){
						return md5($pass);
					}if(HASH_METHOD=='SHA1' && !$this->isValidSHA256($pass)){
						return hash('sha256',$pass);
					}else {return $pass;}
				}else{
					return '';//$pass;
				}
			}
		}
		
		public function save_mu_data($request){
			$do = isset($request['do']) ? trim($request['do']) : '';
			$patient_id = $this->patient_id;
			if($patient_id==0 || $patient_id=='') die('Operation Failed. Please try again.');
			switch($do){
				case 'idunlockPatient':
					imw_query("UPDATE patient_data SET locked=0 WHERE id = $patient_id LIMIT 1");
					break;
				case 'tempKeyGiven':
					if(trim($request['temp_key'])!=''){
						$temp_chk_query = "";
						if($request['temp_key_chk_val']==1){$temp_chk_query = ", temp_key_chk_opr_id='".$_SESSION['authId']."', temp_key_chk_datetime='".date('Y-m-d H:i:s')."'";}
						$q = "UPDATE patient_data SET temp_key='".trim($request['temp_key'])."', temp_key_chk_val='".trim($request['temp_key_chk_val'])."'".$temp_chk_query." WHERE id = '$patient_id' LIMIT 1";
						$res = imw_query($q);//echo imw_error().$q;
						if($res){
							$alert_q = "INSERT INTO alert_tbl (alertContent,patient_id,operatorId,saveDateTime,alert_to_show_under,alert_showed,alert_created_console) 
										VALUES ('Give \"Pt. Portal Access Activation Key\" to patient','".$patient_id."','1','".date('Y-m-d H:i:s')."','0,1,0,0','0,0,0','1')";
							$res_alert=imw_query($alert_q); //echo $q.imw_error();
						}
					}
					break;
				case 'vsSave':
					$q_vital = "INSERT INTO vital_sign_master SET 
									date_vital='".date('Y-m-d')."', 
									time_vital='".date('H:i A')."', 
									created_on='".date('Y-m-d H:i:s')."', 
									patient_id='".$patient_id."'";
					$res = imw_query($q_vital);
					$vital_master_id = imw_insert_id();
					if($vital_master_id){
						for($i=1;$i<=10;$i++){
							if($i==1) 		{$range_vital = $request['bps'];	$range_unit = 'mmHg';}
							else if($i==2) 	{$range_vital = $request['bpd'];	$range_unit = 'mmHg';}
							else if($i==3) 	{$range_vital = '';					$range_unit = 'beats/minute';}
							else if($i==4) 	{$range_vital = '';					$range_unit = 'breaths/minute';}
							else if($i==5) 	{$range_vital = '';					$range_unit = 'ml/l';}
							else if($i==6) 	{$range_vital = '';					$range_unit = '&deg;f';}
							else if($i==7) 	{$range_vital = $request['hght'];	$range_unit = $request['hght_unit'];}
							else if($i==8) 	{$range_vital = $request['wght'];	$range_unit = $request['wght_unit'];}
							else if($i==9) 	{$range_vital = $request['bmi_val'];$range_unit = 'kg/sqr.m';}
							$q_vital_patient = "INSERT INTO vital_sign_patient SET 
													vital_master_id='".$vital_master_id."', 
													vital_sign_id='".$i."', 
													range_vital='".$range_vital."', 
													unit='".$range_unit."'";
							imw_query($q_vital_patient);echo imw_error();
							
						}
					}		
					break;
				case 'smokingStatus':
					if($request['SmokingStatus']==""){break;}
					$smoke_status = $request['SmokingStatus'];
					$source_of_smoke = $request['source_of_smoke'];
					$source_of_smoke_other = $request['source_of_smoke_other'];
					$smoke_perday = $request['smoke_perday'];
					$number_of_years_with_smoke = $request['number_of_years_with_smoke'];
					$smoke_years_months = $request['smoke_years_months'];
					$offered_cessation_counseling = $request['offered_cessation_counseling'];
					$dateOCC = getDateFormatDB($request["txtDateOfferedCessationCounselling"]);
					//if($dateOCC == "00-00-0000" || $dateOCC == "--"){
					if($dateOCC == "0000-00-00" || $dateOCC == "--" || $dateOCC == ""){
						$dateOCC = "";
					}
					$cessationCounselling = $request['cessationCounselling'];
					$cessationCounsellingOther = $request['cessationCounsellingOther'];
				
					$check_data = imw_query("select * from social_history where patient_id=$patient_id");
					$socialsaveqry = "";
					$check_data2 = imw_num_rows($check_data);
					if($check_data2>0){
						$socialsaveqry .= "update social_history set ";
						$socialsaveqry .= " patient_id=$patient_id ";
					}
					else{
						$socialsaveqry .= "insert into social_history set ";					
						$socialsaveqry .= " patient_id=$patient_id ";
					}
					
					$add_smoke_detail="";
					$add_smoke_id="";
					if($smoke_status!=""){
						$smoke_qry=imw_query("select * from smoking_status_tbl where id='$smoke_status'");		
						$smoke_row=imw_fetch_array($smoke_qry);
						$add_smoke_detail= ucfirst($smoke_row['desc']).' / '.$smoke_row['code'];
						$add_smoke_id= $smoke_row['id'];
					}
					
					$socialsaveqry.= " ,smoking_status_id ='".imw_real_escape_string(htmlentities($add_smoke_id))."' ";
					$socialsaveqry.= " ,smoking_status ='".imw_real_escape_string(htmlentities($add_smoke_detail))."' ";
					$socialsaveqry.= " ,source_of_smoke ='".imw_real_escape_string(htmlentities($source_of_smoke))."' ";
					$socialsaveqry.= " ,source_of_smoke_other ='".imw_real_escape_string(htmlentities($source_of_smoke_other))."' ";
					$socialsaveqry.= " ,smoke_perday='".imw_real_escape_string(htmlentities($smoke_perday))."' ";
					$socialsaveqry.= " ,number_of_years_with_smoke ='".imw_real_escape_string(htmlentities($number_of_years_with_smoke))."' ";
					$socialsaveqry.= " ,smoke_years_months ='".imw_real_escape_string(htmlentities($smoke_years_months))."' ";
					$socialsaveqry.= " ,smoke_counseling ='".imw_real_escape_string(htmlentities($offered_cessation_counseling))."' ";
					$socialsaveqry.= " ,offered_cessation_counselling_date ='".imw_real_escape_string(htmlentities($dateOCC))."' ";
					$socialsaveqry.= " ,cessation_counselling_option ='".imw_real_escape_string(htmlentities($cessationCounselling))."' ";
					$socialsaveqry.= " ,cessation_counselling_other  ='".imw_real_escape_string(htmlentities($cessationCounsellingOther))."' ";
					
					if($check_data2>0){			
						// update
						$socialsaveqry .= " where patient_id='$patient_id' ";
					}
					imw_query($socialsaveqry);
					break;
			}
		}
		
		public function tempKeyGen($size = '6') {
			$string = '';
			$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			for ($i = 0; $i < $size; $i++) {
				$string .= $characters[mt_rand(0, (strlen($characters) - 1))];
			}
			return $string;
		}

		public function get_temp_key($request){
			$temp_key_size = $request["temp_key_size"];
			$pid = $request["pid"];
			$tempKeyVal = $this->tempKeyGen($temp_key_size,$pid);
			$pt_override_priv=$_SESSION['sess_privileges']['priv_pt_Override'];
			$check_priv=0;
			$return_val="0";
			if($request['regen_key']=='1'){
				if($pt_override_priv==0 || !$pt_override_priv  || $pt_override_priv==""){
					$return_val="no_priv";
					$check_priv=1;
				}
				
			}
			$user_password=trim($request['user_pass']);
			if($user_password && $user_password!='DoNtChEcK'){
				$pass_user = $this->hashPassword($user_password);
				$qry_users="SELECT access_pri from users where `password`='".$pass_user."' and delete_status!='1'";
				$return_val.=$qry_users;
				$res_users=imw_query($qry_users);
				if(imw_num_rows($res_users)>0){
					$row_users=imw_fetch_assoc($res_users);
					$user_priviliges_prev=unserialize(html_entity_decode(trim($row_users['access_pri'])));
					if($user_priviliges_prev['priv_pt_Override']==1){
						$check_priv=0;
					}else{
						$return_val="user_has_no_priv";
						$check_priv=1;	
					}
				}else{
					$return_val="user_incorrect";
					$check_priv=1;
				}
			}
			if($pid && $tempKeyVal && $check_priv==0) {
				$saveQry = "UPDATE patient_data SET temp_key = '".$tempKeyVal."', temp_key_expire='', username='', password='', preferred_image = '',temp_key_chk_val='' WHERE id='".$pid."'";
				$saveRes = imw_query($saveQry);
				$return_val=$tempKeyVal;
				$saveRespQry = "UPDATE resp_party SET resp_username = '', resp_password='', preferred_image='' WHERE patient_id='".$pid."'";	
				$saveRespRes = imw_query($saveRespQry);
			}
			echo $return_val;
		}	

		public function isValidMd5($md5 =''){return preg_match('/^[a-f0-9]{32}$/', $md5);}
		public function isValidSHA256($md5 =''){return preg_match('/^[a-f0-9]{64}$/', $md5);}	
	}
?>