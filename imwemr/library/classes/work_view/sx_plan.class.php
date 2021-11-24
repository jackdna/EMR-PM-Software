<?php 
	include_once $GLOBALS['srcdir'].'/classes/SaveFile.php';
	include_once $GLOBALS['srcdir'].'/classes/common_function.php';
	include_once $GLOBALS['srcdir']."/classes/work_view/Patient.php";
	
	class Sx_Plan{
		public $patient_id = '';
		public $sa_doctor_id = '';
		public $id_chart_sx_plan_sheet = 0;
		public $elem_examDate = '';
		public $sx_plan_id = '';
		public $finalized_flag = '';
		public $el_img_checked = '';
		public $arrAcuitiesMrDis = '';
		public $arr_lens = '';
		public $arr_asti_as = '';
		public $upload_path = '';
		public $lensFields = array();
		public $callArr = array();
		
		public function __construct($pid,$plan_id = "",$finalize_flag = false){
			$this->patient_id = $pid;
			if(empty($plan_id) == false){
				$this->sx_plan_id = $plan_id;
			}
			$this->pt_dos = date('Y-m-d');
			$this->finalized_flag = $finalize_flag;
			$this->elem_examDate = date('Y-m-d H:i:s');
			$this->upload_path = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH');
			$arrEmpty = array();
			//Acuities MR/ Dis
			$this->arrAcuitiesMrDis = array(
				"20/15"=>array("20/15",$arrEmpty,"20/15"),
				"20/20"=>array("20/20",$arrEmpty,"20/20"),
				"20/25"=>array("20/25",$arrEmpty,"20/25"),
				"20/30"=>array("20/30",$arrEmpty,"20/30"),
				"20/40"=>array("20/40",$arrEmpty,"20/40"),					 
				"20/50"=>array("20/50",$arrEmpty,"20/50"),
				"20/60"=>array("20/60",$arrEmpty,"20/60"),
				"20/70"=>array("20/70",$arrEmpty,"20/70"),
				"20/80"=>array("20/80",$arrEmpty,"20/80"),					 
				"20/100"=>array("20/100",$arrEmpty,"20/100"),
				"20/150"=>array("20/150",$arrEmpty,"20/150"),
				"20/200"=>array("20/200",$arrEmpty,"20/200"),
				"20/300"=>array("20/300",$arrEmpty,"20/300"),					 
				"20/400"=>array("20/400",$arrEmpty,"20/400"),
				"20/600"=>array("20/600",$arrEmpty,"20/600"),
				"20/800"=>array("20/800",$arrEmpty,"20/800"),
				"CF"=>array("CF",$arrEmpty,"CF"),
				"CF 1ft"=>array("CF 1ft",$arrEmpty,"CF 1ft"),
				"CF 2ft"=>array("CF 2ft",$arrEmpty,"CF 2ft"),
				"CF 3ft"=>array("CF 3ft",$arrEmpty,"CF 3ft"),
				"CF 4ft"=>array("CF 4ft",$arrEmpty,"CF 4ft"),
				"CF 5ft"=>array("CF 5ft",$arrEmpty,"CF 5ft"),
				"CF 6ft"=>array("CF 6ft",$arrEmpty,"CF 6ft"),
				"HM"=>array("HM",$arrEmpty,"HM"),
				"LP"=>array("LP",$arrEmpty,"LP"),					 
				"LP c p"=>array("LP c p",$arrEmpty,"LP c p"),
				"LP s p"=>array("LP s p",$arrEmpty,"LP s p"),
				"NLP"=>array("NLP",$arrEmpty,"NLP"),
				"F&F"=>array("F&F",$arrEmpty,"F&F"),
				"F/(F)"=>array("F/(F)",$arrEmpty,"F/(F)"),
				"2/200"=>array("2/200",$arrEmpty,"2/200"),
				"CSM"=>array("CSM",$arrEmpty,"CSM"),
				"Enucleation"=>array("Enucleation",$arrEmpty,"Enucleation"),
				"Prosthetic"=>array("Prosthetic",$arrEmpty,"Prosthetic"),
				"Pt Uncoopera"=>array("Pt Uncoopera",$arrEmpty,"Pt Uncoopera"),
				"Unable"=>array("Unable",$arrEmpty,"Unable"),
				"5/200"=>array("5/200",$arrEmpty,"5/200")
			 );
					 
			$this->arr_lens = array(0 => "Primary", 1 => "Backup1", 2 => "Backup2", 3 => "Backup3");
			$this->arr_asti_as = array("Glasses Oldest","Manifest Refraction", "IOLM Preop", "IOLM Repeat or Old", "Topography Consult", "Topography Preop", "Verion", "Coma Max (u)", "CCT (u)", "OCTM FT (u)");
			$this->iol_lenses = $this->iolLenArr();	
			$this->lensFields = array('Type' => '', 'Pwr' => '', 'Cyl' => '', 'Axis' => '', 'Used' => '', 'Target' => '', 'Acd' => '', 'Sp' => '', 'Crs' => '');
			$this->callArr = array(
				'vf' => array('id' => 'vf_id', 'table' => 'vf', 'name' => 'VF'),
				'topogrphy' => array('id' => 'topo_id', 'table' => 'topography', 'name' => 'Topography'),
				'oct' => array('id' => 'oct_id', 'table' => 'oct','name' => 'OCT'),
				'ascan' => array('id' => 'surgical_id', 'table' => 'surgical_tbl','name' => 'Ascan'),
				'iol_master' => array('id' => 'iol_master_id', 'table' => 'iol_master_tbl', 'name' => 'IOL Master')
			);			
		}
		
		//Returns sx plan data arr
		public function get_sx_plan_data($pid,$plan_id){
			$return_arr = array();
			if(empty($plan_id) == false){
				$dt_qry = imw_query("SELECT * FROM chart_sx_plan_sheet WHERE patient_id='".$pid."' AND id='".$plan_id."' AND del_status='0' ORDER BY sx_plan_dos,id DESC");
			}else{
				$dt_qry = imw_query("SELECT * FROM chart_sx_plan_sheet WHERE patient_id='".$pid."' AND sx_plan_dos='".$this->pt_dos."' AND del_status='0' ORDER BY sx_plan_dos,id DESC");
			}
			
			if(imw_num_rows($dt_qry) > 0){
				while($row = imw_fetch_assoc($dt_qry)){
					$return_arr['id_chart_sx_plan_sheet'] = $row["id"];
					$return_arr['elem_examDate'] = $row["exam_date"];
					$return_arr['elem_form_id'] = $row["form_id"];
					$return_arr['el_sx_type'] = $row["sx"];
					$return_arr['el_flomax'] = $row["flomax"];
					$return_arr['el_pt_choice'] = $row["pt_choices"];
					$return_arr['el_prv_sx'] = $row["prv_sx_proc"];
					$return_arr['el_mank_eye'] = $row["mank_eye"];
					$return_arr['el_refraction'] =	$row["refraction"];
					$return_arr['el_domi'] = $row["domi_eye"];
					$return_arr['el_othr_eye_ref'] = $row["oth_eye_ref"];
					$return_arr['el_iol_recomd'] = $row["iol_mas_k_recommed"];
					$return_arr['el_iol_desc'] = $row["iol_mas_k_comment"];
					$return_arr['el_date_surgery'] = wv_formatDate($row["surg_dt"]);
					$return_arr['el_time_surgery'] = $row["surg_time"];
					$return_arr['el_k_flat'] = $row["k_flat"];
					$return_arr['el_k_steep'] = $row["k_steep"];
					$return_arr['el_k_axis'] = $row["k_axis"];
					$return_arr['el_k_cyl'] = $row["k_cyl"];
					$return_arr['el_ok_flat'] = $row["oth_k_flat"];
					$return_arr['el_ok_steep'] = $row["oth_k_steep"];
					$return_arr['el_ok_axis'] = $row["oth_k_axis"];
					$return_arr['el_ok_cyl'] = $row["oth_k_cyl"];
					if(!empty($row["surgeon_id"])){$return_arr['el_surgeon_id'] = $row["surgeon_id"];}
					$return_arr['el_proc_prim'] = $row["prim_proc"];
					$return_arr['el_proc_sec'] = $row["sec_proc"];
					$return_arr['el_prev_eye_date'] = wv_formatDate($row["pe_dt"]);
					$return_arr['el_prev_eye_lens'] = $row["pe_lens"];
					$return_arr['el_prev_eye_power'] = $row["pe_power"];
					$return_arr['el_prev_eye_cyl'] = $row["pe_cyl"];
					$return_arr['el_prev_eye_axis'] = $row["pe_axis"];
					$return_arr['el_prev_eye_va'] = $row["pe_va"];
					$return_arr['el_prev_eye_ora_res'] = $row["pe_ora_res"];
					$return_arr['el_prev_eye_torpos'] = $row["pe_toric_pos"];
					$return_arr['el_prev_eye_comm'] = $row["pe_comments"];
					$return_arr['el_meth_lens'] = $row["pe_method"];
					$return_arr['el_ora'] = $row["pe_ora"];
					$return_arr['el_version'] = $row["pe_version"];
					$return_arr['el_mbn'] = $row["pe_mbn"];
					$return_arr['el_prem_lens'] = $row["pe_prem_lens"];
					$return_arr['el_cci'] = $row["pe_cci"];
					$return_arr['el_pachy'] = $row["pe_pacy"];
					$return_arr['el_w2w'] = $row["pe_w2w"];
					$return_arr['el_pupilmx'] = $row["pe_pupil_mx"];
					//$return_arr['el_pupildilated'] = $row["pe_pupil_dilated"];
					$return_arr['el_cupmx'] = $row["pe_cap_mx"];
					$return_arr['el_plan_femto'] = $row["ap_femto"];
					$return_arr['el_plan_ak'] = $row["ap_ak"];
					$return_arr['el_plan_ak1_len'] = $row["ap_ak1_len"];
					$return_arr['el_plan_ak2_len'] = $row["ap_ak2_len"];
					$return_arr['el_plan_ak1_axis'] = $row["ap_ak1_axis"];
					$return_arr['el_plan_arc2_axis'] = $row["ap_arc2_angel"];
					$return_arr['el_plan_ak1_depth'] = $row["ap_ak1_dpth"];
					$return_arr['el_plan_ak2_depth'] = $row["ap_arc2_dpth"];
					$return_arr['el_plan_opt_zone'] = $row["ap_opt_zone"];
					$return_arr['el_plan_anterior'] = $row["ap_anterior"];
					$return_arr['el_plan_insratromal'] = $row["ap_instratromal"];
					$return_arr['el_plan_incision_axis'] = $row["ap_incision_axis"];
					$return_arr['el_sx_pln_hook'] = $row["sx_plan_hooks"];
					$return_arr['el_flomx_cocktail'] = $row["flomax_cocktail"];
					$return_arr['el_trypan_blue'] = $row["trypan_blue"];
					$return_arr['el_lri'] = $row["lri"];
					$return_arr['el_femto'] = $row["femto"];
					$return_arr['el_ecp'] = $row["ecp"];
					$return_arr['el_sx_pln_com']	 = $row["sx_pln_com"];
					$return_arr['el_asti_com']	 = $row["asti_com"];
					$return_arr['el_prev_sx_ocu'] = $row["prev_sx_ocu"];
					$return_arr['el_prev_sx_sys'] = $row["prev_sx_sys"];
					$return_arr['el_predict_sel'] = $row["predict_sel"];
					$return_arr['el_prev_eye_site'] = $row["pe_site"];
					$return_arr['el_k_given'] = $row["k_given"];
					$return_arr['el_mank_ref'] = $row["mank_ref"];	
					$return_arr['el_img_checked'] = $row["sx_imgs"];
					
					$return_arr['el_ids_iol'] = $row["dd_id_iol"];
					$return_arr['el_ids_ascan'] = $row["dd_id_ascan"];
					$return_arr['el_ids_oct'] = $row["dd_id_oct"];
					$return_arr['el_ids_topo'] = $row["dd_id_topo"];
					$return_arr['el_ids_vf'] = $row["dd_id_vf"];
					
					$return_arr['iol_lock'] = $row["iol_lock"];
					
					$return_arr['po_proc_id'] = $row["po_proc_id"];
					$return_arr['po_eva_map'] = $row["po_eva_map"];
					
					//Attaching SLE Lens Summary to main data
					$sleSummary = (empty($row["lens_sle_summary"]) == false) ? $row["lens_sle_summary"] : $this->getLensSLE($pid, $row["mank_eye"]);
					if(empty($sleSummary) == false) $return_arr['el_lens_sle_summary'] = $sleSummary;
					
					//Attaching Pupil dilated value
					$dilatedVal = (empty($row["pe_pupil_dilated"]) == false) ? $row["pe_pupil_dilated"] : $this->getPupDilated($pid, $row["mank_eye"]);
					if(empty($dilatedVal) == false) $return_arr['el_pupildilated'] = $dilatedVal;
					
					$return_arr['sx_plan_dos'] = $row["sx_plan_dos"];
				}
			}else{
				$el_domi = $this->getDomiEye($this->patient_id, $this->sx_plan_id);
				list($el_prev_eye_site,$el_prev_eye_date,$el_prev_eye_lens,$el_prev_eye_ora_res,$el_prev_eye_comm) = $this->sps_getPrevSxValues($this->patient_id);
				$return_arr['el_prev_eye_site'] = $el_prev_eye_site;
				$return_arr['el_prev_eye_date'] = $el_prev_eye_date;
				$return_arr['el_prev_eye_lens'] = $el_prev_eye_lens;
				$return_arr['el_prev_eye_ora_res'] = $el_prev_eye_ora_res;
				$return_arr['el_prev_eye_comm'] = $el_prev_eye_comm;
				if($el_domi && $el_domi['dominantEye'])$return_arr['el_domi'] = $el_domi['dominantEye'];
				$return_arr['sx_plan_dos'] = $date;
			}
			return $return_arr;
		}
		
		//Returns 
		public function get_pt_sx_dos_arr($pid){
			$pid = (empty($pid)) ? $this->patient_id : $pid;
			$qryDoctor = "";
			if( $this->sa_doctor_id ) {
				$qryDoctor = " And surgeon_id = '".$this->sa_doctor_id."' ";
			}
			$return_arr = array();
			$qry = imw_query('SELECT id,DATE_FORMAT(sx_plan_dos,"'.get_sql_date_format().'") as sx_pl_dos,sx_plan_dos,patient_id,mank_eye FROM chart_sx_plan_sheet WHERE patient_id = "'.$pid.'" AND sx_plan_dos != "0000-00-00" AND del_status = 0 '.$qryDoctor.' ORDER BY sx_plan_dos DESC');
			if(imw_num_rows($qry) == 0){
				$qry = imw_query('SELECT id,DATE_FORMAT(sx_plan_dos,"'.get_sql_date_format().'") as sx_pl_dos,sx_plan_dos,patient_id,mank_eye FROM chart_sx_plan_sheet WHERE patient_id = "'.$pid.'" AND sx_plan_dos != "0000-00-00" AND del_status = 0 ORDER BY sx_plan_dos DESC');	
			}
			if(imw_num_rows($qry) > 0){
				while($row = imw_fetch_assoc($qry)){
					$return_arr[$row['id']] = $row;
				}
			}
			return $return_arr;
		}
		
		public function sps_getPrevSxValues($patient_id){	
			$sql = "SELECT surg_dt, mank_eye, pe_method, pe_ora, sx_pln_com   FROM chart_sx_plan_sheet WHERE patient_id='".$patient_id."' AND del_status='0' ORDER BY exam_date DESC LIMIT 0, 1  ";
			$row = sqlQuery($sql);
			if($row != false){
				$el_mank_eye = $row["mank_eye"];
				$el_date_surgery = wv_formatDate($row["surg_dt"]);
				$el_meth_lens = $row["pe_method"];
				$el_ora=$row["pe_ora"];
				$el_sx_pln_com	 = $row["sx_pln_com"];		
			}
			return array($el_mank_eye, $el_date_surgery, $el_meth_lens, $el_ora, $el_sx_pln_com);
		}
		
		public function getDomiEye($patientId, $fid = false){
			//Get All DOS for patient
			$dosArr = $this->getPtDOS($patientId);
			$domi = $pachyValues = '';
			$returnArr = array();
			
			if(count($dosArr) > 0){
				foreach($dosArr as $formId){
					$sql = "SELECT dominant FROM chart_left_cc_history where patient_id='".$patientId."' AND form_id = '".$formId."' ";
					$row = sqlQuery($sql);
					if($row!=false){
						if(!empty($row["dominant"])){
							$domi = $row["dominant"];
						}
					}
					
					if(empty($domi) == false) break;
				}
				
				foreach($dosArr as $formId){
					$TmpPachyValues = $this->getPachyCorrections($patientId, $formId, $domi);
					if($TmpPachyValues) $pachyValues = $TmpPachyValues;
				}
				
			}
			
			$returnArr['dominantEye'] = $domi;
			$returnArr['pachyValues'] = $pachyValues;
			
			return $returnArr;
		}
		
		public function getAllergies($patientId, $sel=" * ", $retbool=0, $st=""){
			if($retbool==0){
				$sql = "select ".$sel." from lists where pid='".$patientId."' and type in(3,7) ";
				if(!empty($st)) $sql.=" AND allergy_status = '".$st."' ";
				$rez = sqlStatement($sql);
				return $rez;
			}else{
				$ret="0";
				$sql = "select count(title) as num from lists
						where pid='".$patientId."'
						and type in(3,7)
						and trim(title) != '' AND UCASE(title)!='NKA' AND UCASE(title)!='NKDA'
						and allergy_status != 'Deleted' ";
				$row = sqlQuery($sql);
				//echo $sql;die();
				if($row != false && $row["num"]>0){
					$ret="1";
				}
				return $ret;
			}
		}
		
		public function getPtDiabeticVal($patient_id){
			$diabetes="No";
			$diabetes_values="";
			$sql = "SELECT any_conditions_you, diabetes_values, chk_under_control FROM general_medicine GM WHERE GM.patient_id  = '".$patient_id."' ";
			$row = sqlQuery($sql);
			if($row!=false){
				if(strpos($row["any_conditions_you"],"3")!==false){
					$diabetes="Yes";
					$tmp = explode("~|~",$row["diabetes_values"]);
					$tmp2 = explode("~|~",$row["chk_under_control"]);
					if(!empty($tmp[0])){$diabetes_values = $tmp[0];}
					if(!empty($tmp2[0])=="7"){$diabetes_values .=" (UC)";}
				}
			}
			//
			return (!empty($diabetes_values)) ? $diabetes_values : $diabetes ;
		}
		
		public function getPtFlomax($pid){
			$flomax="No";
			$sql  = "SELECT flomaxOD, flomaxOS FROM `surgical_tbl` WHERE patient_id='".$pid."' AND del_status='0' ORDER BY examTime DESC limit 0, 1 ";
			$row = sqlQuery($sql);
			if($row!=false){
				if(!empty($row["flomaxOD"]) || !empty($row["flomaxOS"])){
					$flomax="Yes";
				}
			}
			
			if($flomax=="No"){
			$sql  = "SELECT flomaxOD, flomaxOS FROM iol_master_tbl WHERE patient_id='".$pid."' AND del_status='0' ORDER BY examTime DESC limit 0, 1 ";
			$row = sqlQuery($sql);
			if($row!=false){
				if(!empty($row["flomaxOD"]) || !empty($row["flomaxOS"])){
					$flomax="Yes";
				}
			}
			}
			return $flomax;
		}
		
		public function sps_get_mbn(){ 
			$ctype="MBN";
			$ar=array();
			$sql = " SELECT * FROM `admin_sps_options` WHERE del_status='0' AND type='".imw_real_escape_string($ctype)."' AND pro_id IN('0','".$_SESSION["authId"]."') ORDER BY choice_nm  ";
			$rez = sqlStatement($sql);
			for($i=0; $row=sqlFetchArray($rez);$i++){
				$id=$row["id"];
				$choice_nm=$row["choice_nm"];		
				if(!empty($choice_nm)){	$ar[]=$choice_nm;	}
			}
			return $ar;
		}
		
		public function getMultiPhy($patient_id){
			$strRefPhy = $strPCPPhy = $strCMPhy = $strRet = "";
			$intRefPhy = $intPCPPhy = $intCMPhy = 0;
			$arrTemp = array();
			$qrySelpatRefPhy = "select refPhy.FirstName AS rp_fname, refPhy.LastName AS rp_lname, refPhy.Title AS rp_title, refPhy.MiddleName AS rp_mname, pmrf.phy_type 
								from patient_multi_ref_phy pmrf INNER JOIN refferphysician refPhy ON pmrf.ref_phy_id = refPhy.physician_Reffer_id 
								where pmrf.patient_id = '".$patient_id."' and pmrf.phy_type IN (1,2,3,4) and pmrf.status = '0' and refPhy.delete_status = 0
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
			$strRet = $strRefPhy."!@!".$strPCPPhy."!@!".$strCMPhy."!@!".$strMoreRefPhy."!@!".$strMorePCPPhy."!@!".$strMoreCMPhy;
			return $strRet;
		}
		
		public function sps_get_pt_choices(){ 
			$ctype="Patient Choices";
			$ar=array();
			$sql = " SELECT * FROM `admin_sps_options` WHERE del_status='0' AND type='".imw_real_escape_string($ctype)."' AND pro_id IN('0','".$_SESSION["authId"]."') ORDER BY choice_nm  ";
			$rez = sqlStatement($sql);
			for($i=0; $row=sqlFetchArray($rez);$i++){
				$id=$row["id"];
				$choice_nm=$row["choice_nm"];		
				if(!empty($choice_nm)){	$ar[]=$choice_nm;	}
			}
			return $ar;
		}
		
		public function sps_getPtMeds($elem_patientId){
			$ocular_medi=array();
			$check_data="select id, title, destination, type, sig, DATE_FORMAT(date,'%Y-%m-%d') as 'eDate', med_comments, sites, compliant from lists where pid='".$elem_patientId."'";
			
			//$check_data .= "and (type='4' OR type='1')";
			$check_data .= "and (type='6' OR type='5')";
			
			$check_data .= "AND allergy_status='Active' ORDER BY begdate DESC	";				
			$checkSql = @imw_query($check_data);
			while($checkl = @imw_fetch_array($checkSql)){
				$ocular_medi[$checkl['type']][] = array(stripslashes($checkl['id']), stripslashes($checkl['title']));
			}
			
			return $ocular_medi;
		}
		
		public function getMrPersonnal($f=0,$flg=""){
			$arr=array();
			$provSql  = $this->getPersonnal($flg);
			while($provRt = imw_fetch_assoc($provSql))
			{
				//$mrProviderName = $provRt['lname'].",&nbsp;".$provRt['fname']."&nbsp;".$provRt['mname'];
				$mrProviderName = $provRt['fname'];
				$mrProviderName .= !empty($provRt['lname']) ? "&nbsp;".strtoupper(substr($provRt['lname'],0,1))."" : "" ;
				$mrProviderName = (strlen($mrProviderName) > 30) ? substr($mrProviderName,0,28).".." : $mrProviderName;
				$id = $provRt['id'];
				if($f == 0){
					$arr[$mrProviderName] = array($mrProviderName,$arrEmpty,$mrProviderName."-".$id);
				}else if($f == 1){
					$arr[$id] = $mrProviderName;
				}else if($f == 2){
					$mrProviderName = $provRt['fname'];
					$mrProviderName .= !empty($provRt['mname']) ? " ".strtoupper(substr($provRt['mname'],0,1))."" : "" ;
					$mrProviderName .= !empty($provRt['lname']) ? " ".$provRt['lname']."" : "" ; 
					$mrProviderName = (strlen($mrProviderName) > 100) ? substr($mrProviderName,0,98).".." : $mrProviderName;			
					$arr[$id] = $mrProviderName;
				}
			}
			return $arr;
		}
		
		public function sps_get_iol_master_recomds(){ 
			$ctype="IOL Master K's Recommendations";
			$ar=array();
			$sql = " SELECT * FROM `admin_sps_options` WHERE del_status='0' AND type='".imw_real_escape_string($ctype)."' AND pro_id IN('0','".$_SESSION["authId"]."') ORDER BY choice_nm  ";
			$rez = sqlStatement($sql);
			for($i=0; $row=sqlFetchArray($rez);$i++){
				$id=$row["id"];
				$choice_nm=$row["choice_nm"];		
				if(!empty($choice_nm)){	$ar[]=$choice_nm;	}
			}
			return $ar;
		}
		
		public function sps_get_toric_buttons(){
			$ar=array();
			$sql = " SELECT * FROM `sps_admin_lens_calc` WHERE del_status='0' ORDER BY lens_calc  ";
			$rez = sqlStatement($sql);
			for($i=0; $row=sqlFetchArray($rez);$i++){
			
				$id=$row["id"];
				$lens_calc=$row["lens_calc"];
				$url=$row["url"];		
				//
				if(strlen($lens_calc) >= 30){  $lens_calc = substr($lens_calc, 0, 28).".."; }
				
				if(!empty($lens_calc) && !empty($url)){
					$ar[$lens_calc]=$id;
				}
			}
			return $ar;
		}
		
		public function sps_getPrvSxPlanSheet($pid, $dos = false){//get Previous
			$sxDos = $this->pt_dos;
			$dt_qry = imw_query("SELECT sx_plan_dos FROM chart_sx_plan_sheet WHERE patient_id='".$pid."' AND id='".$this->sx_plan_id."' AND del_status='0' ORDER BY sx_plan_dos,id DESC");
			if($dt_qry && imw_num_rows($dt_qry) > 0){
				$rowFetch = imw_fetch_assoc($dt_qry);
				$sxDos = $rowFetch['sx_plan_dos'];
			}
			
			$sql = "SELECT id, mank_eye, surg_dt, exam_date, sx_plan_dos  FROM chart_sx_plan_sheet 
						WHERE patient_id='".$pid."' AND sx_plan_dos < '".$sxDos."' AND del_status='0' 
						ORDER BY sx_plan_dos DESC, exam_date DESC
						";
			$rez=sqlStatement($sql);
			for($i=1;$row=sqlFetchArray($rez);$i++){
				$id=$row["id"];
				$site=$row["mank_eye"];
				//$dos = (strpos($row["surg_dt"],"0000")===false) ? wv_formatDate($row["surg_dt"]) : wv_formatDate($row["exam_date"]) ;		
				$dos = wv_formatDate($row["sx_plan_dos"]) ;		
				if(empty($dos) == false) $ht.="<tr style=\"cursor:pointer;\" onclick=\"sps_load_prev_values('".$id."')\"><td>".$i.".</td><td>".$dos."</td><td>".$site."</td></tr>";
			}
			
			if(!empty($ht)){		
				$ht="<table class=\"table table-bordered\"><tr><th>Sr.</th><th>Date</th><th>Site</th></tr>".$ht."</table>";
			}else{
				$ht="No previous record found.";
			}
			return $ht;
		} 
		
		public function sps_getTestsDropDown($pid,$el_ids_iol, $el_ids_ascan, $el_ids_oct, $el_ids_topo, $el_ids_vf){
			$htm="";
			//IOL_Master
			$str="";$id="";
			$sql = "SELECT iol_master_id as id, examDate FROM iol_master_tbl WHERE patient_id='".$pid."' AND del_status='0' AND purged='0' ORDER BY examDate DESC, iol_master_id DESC  ";
			$rez = sqlStatement($sql);
			for($i=1;$row=sqlFetchArray($rez);$i++){	
				if(!empty($row["id"])){ 
					$id=$row["id"];
					$dos = wv_formatDate($row["examDate"]); 
					$sel=($id==$el_ids_iol) ? "SELECTED" : ""; 
					$str.="<option value=\"".$id."\" ".$sel.">".$dos."</option>"; 
				}	
			}
			if(!empty($str)){
				$str = "
				<div class='col-sm-2'>
					<label for='el_ids_iol'>IOL Master</label>	
					<select id=\"el_ids_iol\" name=\"el_ids_iol\" class=\"form-control minimal\" onchange=\"get_test_images(this);\"><option value=\"\">select</option>".$str."</select>
				</div>"; 
				$htm.=$str; 
			}	
			
			//Ascan
			$str="";$id="";
			$sql = "SELECT surgical_id as id, examDate FROM surgical_tbl WHERE patient_id='".$pid."' AND del_status='0' AND purged='0' ORDER BY examDate DESC, surgical_id DESC   ";
			$rez = sqlStatement($sql);
			for($i=1;$row=sqlFetchArray($rez);$i++){
				if(!empty($row["id"])){ 
					$id=$row["id"]; 
					$dos = wv_formatDate($row["examDate"]); 
					$sel=($id==$el_ids_ascan) ? "SELECTED" : ""; 
					$str.="<option value=\"".$id."\" ".$sel.">".$dos."</option>"; 
				}	
			}
			if(!empty($str)){  
				$str = "
				<div class='col-sm-2'>
					<label for='el_ids_ascan'>A-scan</label>	
					<select id=\"el_ids_ascan\" name=\"el_ids_ascan\" class=\"form-control minimal\"  onchange=\"get_test_images(this);\"><option value=\"\">select</option>".$str."</select>
				</div>"; 
				$htm.=$str; 
			}
			
			//OCT
			$str="";$id="";
			$sql = "SELECT oct_id as id, examDate FROM oct WHERE patient_id='".$pid."' AND del_status='0' AND purged='0' ORDER BY  examDate DESC, oct_id DESC   ";
			$rez = sqlStatement($sql);
			for($i=1;$row=sqlFetchArray($rez);$i++){
				if(!empty($row["id"])){ 
					$id=$row["id"]; 
					$dos=wv_formatDate($row["examDate"]); 
					$sel=($id==$el_ids_oct) ? "SELECTED" : ""; 
					$str.="<option value=\"".$id."\" ".$sel.">".$dos."</option>"; 
				}	
			}
			if(!empty($str)){  
				$str = "
				<div class='col-sm-2'>
					<label for='el_ids_oct'>OCT</label>	
					<select id=\"el_ids_oct\" name=\"el_ids_oct\" class=\"form-control minimal\"  onchange=\"get_test_images(this);\"><option value=\"\">select</option>".$str."</select>
				</div>"; 
				$htm.=$str; 
			}
			
			//Topogrphy 
			$str="";$id="";
			$sql = "SELECT topo_id as id, examDate FROM topography WHERE patientId='".$pid."' AND del_status='0' AND purged='0' ORDER BY  examDate DESC, topo_id DESC  ";
			$rez = sqlStatement($sql);
			for($i=1;$row=sqlFetchArray($rez);$i++){
				if(!empty($row["id"])){ 
					$id=$row["id"]; 
					$dos=wv_formatDate($row["examDate"]); 
					$sel=($id==$el_ids_topo) ? "SELECTED" : ""; 
					$str.="<option value=\"".$id."\" ".$sel.">".$dos."</option>"; 
				}	
			}
			if(!empty($str)){
				$str = "
				<div class='col-sm-2'>
					<label for='el_ids_topo'>Topography</label>	
					<select id=\"el_ids_topo\" name=\"el_ids_topo\" class=\"form-control minimal\"  onchange=\"get_test_images(this);\"><option value=\"\">select</option>".$str."</select>
				</div>"; 
				$htm.=$str; 
			}
			
			//VF
			$str="";$id="";
			$sql = "SELECT vf_id as id, examDate FROM vf WHERE patientId='".$pid."' AND del_status='0' AND purged='0' ORDER BY examDate DESC , vf_id DESC   ";
			$rez = sqlStatement($sql);
			for($i=1;$row=sqlFetchArray($rez);$i++){
				if(!empty($row["id"])){ 
					$id=$row["id"]; 
					$dos=wv_formatDate($row["examDate"]); 
					$sel=($id==$el_ids_vf) ? "SELECTED" : ""; 
					$str.="<option value=\"".$id."\" ".$sel.">".$dos."</option>"; 
				}	
			}
			if(!empty($str)){  
				$str = "
				<div class='col-sm-2'>
					<label for='el_ids_vf'>VF</label>	
					<select id=\"el_ids_vf\" name=\"el_ids_vf\" class=\"form-control minimal\"  onchange=\"get_test_images(this);\"><option value=\"\">select</option>".$str."</select>
				</div>"; 
				$htm.=$str; 
			}
			
			if(!empty($htm)){ 
				$htm = '<div class="sxdtbx">
							<div class="sxhed"><h2>All Previous Tests</h2></div>
							<div class="pd5"><div class="row">'.$htm.'</div></div>
						</div>';  
			}
			
			return $htm;	
		}
		
		public function sps_getECP(){ 
			$ctype="ECP";
			$ar=array();
			$sql = " SELECT * FROM `admin_sps_options` WHERE del_status='0' AND type='".imw_real_escape_string($ctype)."' AND pro_id IN('0','".$_SESSION["authId"]."') ORDER BY choice_nm  ";
			$rez = sqlStatement($sql);
			for($i=0; $row=sqlFetchArray($rez);$i++){
				$id=$row["id"];
				$choice_nm=$row["choice_nm"];		
				if(!empty($choice_nm)){	$ar[]=$choice_nm;	}
			}
			return $ar;	
		}
		
		
		public function getFStripData($id_chsxplsh,$pid,$el_img_checked, $el_ids_iol,$el_ids_ascan, $el_ids_oct, $el_ids_topo, $el_ids_vf){
			$upload_path = $this->upload_path;
			$web_upload_path = str_replace($GLOBALS['fileroot'],$GLOBALS['webroot'],$upload_path);
			$oSaveFile = new SaveFile;
			$upd = $upload_path."/PatientId_".$pid."/screenshots/";
			$display_path=$web_upload_path."/PatientId_".$pid."/screenshots/";
			$str_html="";
			//Torric Data
			$sql = "SELECT id,img_path FROM toric_pt_images WHERE chart_sx_plan_sheet_id='".$id_chsxplsh."' AND patient_id='".$pid."'  AND del_status='0' ";
			$rez = sqlStatement($sql);
			for($i=1;$row=sqlFetchArray($rez);$i++){
				$id=$row["id"];
				$tmp=$row["img_path"];
				$ime_tmp = !empty($row["img_path"]) ? $upd.$row["img_path"] : "" ;
				if(!empty($ime_tmp) && file_exists($ime_tmp)){
					$tmp_val = "t-".$id; 
					$tmp_checked = strpos($el_img_checked, $tmp_val)!==false ? "checked" : "";			
					$tmp_cls = strpos($el_img_checked, $tmp_val)!==false ? " imgselected" : "";
					$str_html.="<div class=\"div_th_img col-sm-1 ".$tmp_cls."\"><img src=\"".$display_path.$tmp."\" alt=\"img\" class=\"img-thumbnail pointer\" onClick=\"showImage(this); data-img=\"".$display_path.$tmp."\"  \"><input type=\"hidden\" name=\"el_toric_img_id[]\" value=\"".$id."\"><input type=\"checkbox\" name=\"el_img_checked[]\" value=\"".$tmp_val."\" ".$tmp_checked." class=\"hide\"></div>";			
				}
			}
			
			//IOL_Master
			$phr_el_ids = (!empty($el_ids_iol)) ? " AND iol_master_id=\"".$el_ids_iol."\" " : "";
			$str_phrase_iol="";
			$sql = "SELECT iol_master_id FROM iol_master_tbl WHERE patient_id='".$pid."' AND del_status='0' AND purged='0' ".$phr_el_ids." ORDER BY examDate DESC, iol_master_id DESC  Limit 0, 1 ";	
			$row = sqlQuery($sql);
			if($row!=false){
				$iol_master_id = $row["iol_master_id"];
				$el_ids_iol = $iol_master_id;
				$str_phrase_iol=" (image_form='IOL_Master' AND test_id='".$iol_master_id."') ";
			}
			
			//Ascan
			$phr_el_ids = (!empty($el_ids_ascan)) ? " AND  surgical_id=\"".$el_ids_ascan."\" " : "";
			$str_phrase_ascan="";
			$sql = "SELECT surgical_id FROM surgical_tbl WHERE patient_id='".$pid."' AND del_status='0' AND purged='0' ".$phr_el_ids." ORDER BY examDate DESC, surgical_id DESC  Limit 0, 1 ";
			$row = sqlQuery($sql);
			if($row!=false){
				$ascan_id = $row["surgical_id"];
				$el_ids_ascan = $ascan_id;
				$str_phrase_ascan=" (image_form='Ascan' AND test_id='".$ascan_id."') ";
			}	
			
			//OCT
			$phr_el_ids = (!empty($el_ids_oct)) ? " AND  oct_id=\"".$el_ids_oct."\" " : "";
			$str_phrase_oct="";
			$sql = "SELECT oct_id FROM oct WHERE patient_id='".$pid."' AND del_status='0' AND purged='0' ".$phr_el_ids." ORDER BY  examDate DESC, oct_id DESC  Limit 0, 1 ";
			$row = sqlQuery($sql);
			if($row!=false){
				$oct_id = $row["oct_id"];
				$el_ids_oct = $oct_id;
				$str_phrase_oct=" (image_form='OCT' AND test_id='".$oct_id."') ";
			}
			
			//Topogrphy topography
			$phr_el_ids = (!empty($el_ids_topo)) ? " AND  topo_id=\"".$el_ids_topo."\" " : "";
			$str_phrase_topo="";
			$sql = "SELECT topo_id FROM topography WHERE patientId='".$pid."' AND del_status='0' AND purged='0' ".$phr_el_ids." ORDER BY  examDate DESC, topo_id DESC  Limit 0, 1 ";
			$row = sqlQuery($sql);
			if($row!=false){
				$topo_id = $row["topo_id"];
				$el_ids_topo = $topo_id;
				$str_phrase_topo=" (image_form='Topogrphy' AND test_id='".$topo_id."') ";		
			}
			
			//VF
			$phr_el_ids = (!empty($el_ids_vf)) ? " AND  vf_id=\"".$el_ids_vf."\" " : "";
			$str_phrase_vf="";
			$sql = "SELECT vf_id FROM vf WHERE patientId='".$pid."' AND del_status='0' AND purged='0' ".$phr_el_ids." ORDER BY examDate DESC , vf_id DESC  Limit 0, 1 ";
			$row = sqlQuery($sql);
			if($row!=false){
				$vf_id = $row["vf_id"];
				$el_ids_vf = $vf_id;
				$str_phrase_vf=" (image_form='VF' AND test_id='".$vf_id."') ";
			}

			//allready selected tests
			$str_already_selected_4_sx="";
			if(!empty($el_img_checked)){
				$ar_el_img_checked = explode(",",$el_img_checked);
				if(count($ar_el_img_checked)>0){
					foreach($ar_el_img_checked as $k=>$v){
						if(strpos($v,"s-")!==false){
							$v=str_replace("s-","",$v);
							$v=trim($v);
							if(!empty($v)){
								if(!empty($str_already_selected_4_sx)){  $str_already_selected_4_sx.=" OR "; }
								$str_already_selected_4_sx.=" scan_id='".$v."'  ";
							}
						}
					}
				}
			}
			
			// 
			$str_phrase_all="";
			if(!empty($str_phrase_iol)){  $str_phrase_all="".$str_phrase_iol." ";  }
			if(!empty($str_phrase_ascan)){ if(!empty($str_phrase_all)){ $str_phrase_all.=" OR "; } $str_phrase_all.="".$str_phrase_ascan." ";  }
			if(!empty($str_phrase_oct)){ if(!empty($str_phrase_all)){ $str_phrase_all.=" OR "; } $str_phrase_all.="".$str_phrase_oct." ";  }
			if(!empty($str_phrase_topo)){ if(!empty($str_phrase_all)){ $str_phrase_all.=" OR "; } $str_phrase_all.="".$str_phrase_topo." ";  }
			if(!empty($str_phrase_vf)){ if(!empty($str_phrase_all)){ $str_phrase_all.=" OR "; } $str_phrase_all.="".$str_phrase_vf." ";  }	
			if(!empty($str_already_selected_4_sx)){ if(!empty($str_phrase_all)){ $str_phrase_all.=" OR "; }$str_phrase_all.=" ".$str_already_selected_4_sx." ";	}			
			if(!empty($str_phrase_all)){ $str_phrase_all = "AND (".$str_phrase_all.") ";  }//
			
			if(!empty($str_phrase_all)){
				$upd= $this->upload_path;		
				$sql = "SELECT image_form,test_id,scan_id, file_path, image_name as fileName FROM ".constant("IMEDIC_SCAN_DB").".scans 
						WHERE patient_id='".$pid."'  AND status='0' ".$str_phrase_all." ";
				$rez = sqlStatement($sql);
				for($i=1;$row=sqlFetchArray($rez);$i++){
					$imgName = '';
					$id=$row["scan_id"];
					
					$tmp = !empty($row["file_path"]) ? $upd.$row["file_path"] : "";
					$fileExtention = array();
					if(!empty($tmp) && file_exists($tmp)){
						$thumbPath = '';
						
						$dbFileName = urldecode(trim($row["fileName"]));
						$aFileNameParts = explode(".", $dbFileName);
						$sFileExtension = strtolower(end($aFileNameParts));
						$imgW = 80;
						$imgH = 100;
						$pathToImages = "".$row["file_path"];
						$src=""; $tempImgWH="";
						if($sFileExtension == "pdf"){ 
							$src = $GLOBALS["webroot"].'/library/images/test_pdf_Icon.png';
							$fileExtention['path'] = str_replace($GLOBALS['fileroot'].'/data', $GLOBALS['php_server'].'/data', $tmp);
						}else{
							$pathToImages = rtrim(data_path(), '/').$pathToImages;
							$pathInfo = pathinfo($pathToImages);
							
							if(is_dir($pathInfo['dirname']) && file_exists($pathToImages) == true){
								$serverPath = str_replace($GLOBALS['fileroot'].'/data', $GLOBALS['php_server'].'/data', $pathInfo['dirname']);
								$thumbPath = $serverPath.'/thumbnail/'.$pathInfo['basename'];
								$src = $serverPath.'/'.$pathInfo['basename'];
							}
							//--
							// $pathToImages = $oSaveFile->getFilePath($pathToImages, "i");
							// $imgDir = $oSaveFile->getFileDir($pathToImages);
							// 
							// $imgPathInfo = pathinfo($pathToImages);
							// $imgName = $imgPathInfo['basename'];
							// if(!is_dir($imgDir."/thumb")){	mkdir($imgDir."/thumb",0777,true);	}
							// $thumbPath = $pathToImages;//die();
							// 
							// $pathThumb = $oSaveFile->createThumbs($pathToImages,$thumbPath,$imgW,$imgH);							 
							// $imgDim = getimagesize($pathToImages);
							// 
							// if(is_array($pathThumb) == true){
							// 	$src = $oSaveFile->getFilePath($row["file_path"], "w");
							// }
							// else{
							// 	$pathThumb = "".$pathThumb;
							// 	$pathThumb = $oSaveFile->getFilePath($pathThumb, "w2");
							// 	$src = "".$pathThumb;
							// }
							// 
							//--
						}
						if(!empty($src)){
							$imgViewSrc = $src;
							
							//If below condition is true that means src is a PDF not image
							if(is_array($fileExtention) && count($fileExtention) > 0 && isset($fileExtention['path'])) $imgViewSrc = $fileExtention['path'];
							
							//Get Test Details
							$testDetails = $this->getTestDetails($row['image_form'], $row['test_id']);
							$testDate = (isset($testDetails['Date']) && empty($testDetails['Date']) == false) ? $testDetails['Date'] : '';
							$testName = (isset($testDetails['Name']) && empty($testDetails['Name']) == false) ? $testDetails['Name'] : '';
							
							$content = '<p>'.$testDate.'<br / >'.$testName.'</p>';
							
							//$tollTip = show_tooltip($content,'top');
							
							$tmp_val = "s-".$id; 
							$tmp_checked = strpos($el_img_checked, $tmp_val)!==false ? "checked" : "";
							$tmp_cls = strpos($el_img_checked, $tmp_val)!==false ? " imgselected" : "";	
							$thumbSrc = (empty($thumbPath) == false) ? $thumbPath : $src;		
							$str_html.="
								<div class=\"div_th_img ".$tmp_cls." col-sm-1\" ".$tollTip.">
									<div class=\"thumbnail\">
											<img src=\"".$thumbSrc."\"  alt=\"img\" class=\"img-thumbnail pointer\" data-img=\"".$imgViewSrc."\" onClick=\"showImage(this);\">
											<div class=\"caption text-center\">
												<span>".$testDate."</span><br>
												<span>".$testName."</span>
											</div>
									</div>
									
									<input type=\"hidden\" name=\"el_test_img_scan_id[]\" value=\"".$id."\">
									<input type=\"checkbox\" class=\"hide\" name=\"el_img_checked[]\" value=\"".$tmp_val."\" $tmp_checked >
								</div>";
						}
					}
				}	
			}//end if
			
			return (empty($str_html) == false) ? $str_html : '';
		}
		
		function sps_get_load_prv_val($id){
			$arr_lens = $this->arr_lens;
			$arr_asti_as = $this->arr_asti_as;
			
			$arr=array();
			$sql = "SELECT *,DATE_FORMAT(pe_dt,'".get_sql_date_format()."') as pe_dt FROM chart_sx_plan_sheet WHERE id='".$id."' ";
			
			$row = sqlQuery($sql);
			if($row != false){
				$arr["el_prev_eye_date"] = (($row['surg_dt'] == '0000-00-00') ? '' : date('m-d-Y',strtotime($row["surg_dt"])));
				$arr["el_prev_eye_lens"] = $row["pe_lens"];
				$arr["el_prev_eye_power"] = $row["pe_power"];
				$arr["el_prev_eye_cyl"] = $row["pe_cyl"];
				$arr["el_prev_eye_axis"] = $row["pe_axis"];
				$arr["el_prev_eye_va"] = $row["pe_va"];
				$arr["el_prev_eye_ora_res"] = $row["pe_ora_res"];
				$arr["el_prev_eye_torpos"] = $row["pe_toric_pos"];
				$arr["el_prev_eye_comm"] = $row["pe_comments"];
				$arr["el_meth_lens"] = $row["pe_method"];
				$arr["el_ora"] =$row["pe_ora"];
				$arr["el_version"] = $row["pe_version"];
				$arr["el_mbn"] = $row["pe_mbn"];
				$arr["el_prem_lens"] = $row["pe_prem_lens"];
				$arr["el_cci"] = $row["pe_cci"];
				$arr["el_pachy"] = $row["pe_pacy"];
				$arr["el_w2w"] = $row["pe_w2w"];
				$arr["el_pupilmx"] = $row["pe_pupil_mx"];
				$dilatedVal = (empty($row["pe_pupil_dilated"]) == false) ? $row["pe_pupil_dilated"] : $this->getPupDilated($pid, $row["mank_eye"]);
				if(empty($dilatedVal) == false) $arr["el_pupildilated"] = $dilatedVal;
				//$arr["el_pupildilated"] = $row["pe_pupil_dilated"];
				$arr["el_cupmx"] = $row["pe_cap_mx"];
				$arr["el_prev_eye_site"] = $row["mank_eye"];
				$arr["ID"] = $row["id"];
				$arr["surgeonId"] = $row["surgeon_id"];
				/*
				$arr["el_sx_type"] = $row["sx"];
				$arr["el_flomax"] = $row["flomax"];
				$arr["el_pt_choice"] =$row["pt_choices"];
				$arr["el_prv_sx"] = $row["prv_sx_proc"];
				$arr["el_mank_eye"] = $row["mank_eye"];
				$arr["el_refraction"] =	$row["refraction"];
				$arr["el_domi"] = $row["domi_eye"];
				$arr["el_othr_eye_ref"] = $row["oth_eye_ref"];
				$arr["el_iol_recomd"] = $row["iol_mas_k_recommed"];
				$arr["el_iol_desc"] = $row["iol_mas_k_comment"];
				//$arr["el_date_surgery"] = wv_formatDate($row["surg_dt"]);
				//$arr["el_time_surgery"] = $row["surg_time"];
				$arr["el_k_flat"] = $row["k_flat"];
				$arr["el_k_steep"] = $row["k_steep"];
				$arr["el_k_axis"] = $row["k_axis"];
				$arr["el_k_cyl"] = $row["k_cyl"];
				$arr["el_ok_flat"] = $row["oth_k_flat"];
				$arr["el_ok_steep"] = $row["oth_k_steep"];
				$arr["el_ok_axis"] = $row["oth_k_axis"];
				$arr["el_ok_cyl"] = $row["oth_k_cyl"];
				//if(!empty($row["surgeon_id"])){$arr["el_surgeon_id "] = $row["surgeon_id"];}
				$arr["el_proc_prim"] = $row["prim_proc"];
				$arr["el_proc_sec"] = $row["sec_proc"];
				$arr["el_prev_eye_date"] = (($row['pe_dt'] == '00-00-0000') ? '' : $row["pe_dt"]);
				$arr["el_prev_eye_lens"] = $row["pe_lens"];
				$arr["el_prev_eye_power"] = $row["pe_power"];
				$arr["el_prev_eye_cyl"] = $row["pe_cyl"];
				$arr["el_prev_eye_axis"] = $row["pe_axis"];
				$arr["el_prev_eye_va"] = $row["pe_va"];
				$arr["el_prev_eye_ora_res"] = $row["pe_ora_res"];
				$arr["el_prev_eye_torpos"] = $row["pe_toric_pos"];
				$arr["el_prev_eye_comm"] = $row["pe_comments"];
				$arr["el_meth_lens"] = $row["pe_method"];
				$arr["el_ora"] =$row["pe_ora"];
				$arr["el_version"] = $row["pe_version"];
				$arr["el_mbn"] = $row["pe_mbn"];
				$arr["el_prem_lens"] = $row["pe_prem_lens"];
				$arr["el_cci"] = $row["pe_cci"];
				$arr["el_pachy"] = $row["pe_pacy"];
				$arr["el_w2w"] = $row["pe_w2w"];
				$arr["el_pupilmx"] = $row["pe_pupil_mx"];
				$arr["el_cupmx"] = $row["pe_cap_mx"];
				$arr["el_plan_femto"] = $row["ap_femto"];
				$arr["el_plan_ak"] = $row["ap_ak"];
				$arr["el_plan_ak1_len"] = $row["ap_ak1_len"];
				$arr["el_plan_ak2_len"] = $row["ap_ak2_len"];
				$arr["el_plan_ak1_axis"] = $row["ap_ak1_axis"];
				$arr["el_plan_arc2_axis"] = $row["ap_arc2_angel"];
				$arr["el_plan_ak1_depth"] = $row["ap_ak1_dpth"];
				$arr["el_plan_ak2_depth"] = $row["ap_arc2_dpth"];
				$arr["el_plan_opt_zone"] = $row["ap_opt_zone"];
				$arr["el_plan_anterior"] =$row["ap_anterior"];
				$arr["el_plan_insratromal"] = $row["ap_instratromal"];
				$arr["el_plan_incision_axis"] = $row["ap_incision_axis"];
				$arr["el_sx_pln_hook"] = $row["sx_plan_hooks"];
				$arr["el_flomx_cocktail"] = $row["flomax_cocktail"];
				$arr["el_trypan_blue"] = $row["trypan_blue"];
				$arr["el_lri"] = $row["lri"];
				$arr["el_femto"] = $row["femto"];
				$arr["el_ecp"] = $row["ecp"];
				$arr["el_sx_pln_com"] = $row["sx_pln_com"];
				$arr["el_asti_com"] = $row["asti_com"];
				$arr["el_prev_sx_ocu"] = $row["prev_sx_ocu"];
				$arr["el_prev_sx_sys"] = $row["prev_sx_sys"];
				$arr["el_predict_sel"] = $row["predict_sel"];
				$arr["el_prev_eye_site"] = $row["pe_site"];
				$arr["el_k_given"] = $row["k_given"];
				$arr["el_mank_ref"]= $row["mank_ref"];

				//
				//$arr_lens	
				foreach($arr_lens as $k => $lens_type){			
					$lens = "el_lens".$lens_type;
					$power = "el_power".$lens_type;
					$cyl = "el_cyl".$lens_type;
					$axis = "el_axis".$lens_type;
					$used = "el_used".$lens_type;			
					$targt = "el_targt".$lens_type;
					$acd = "el_acd".$lens_type;
					$sp = "el_sp".$lens_type;
					$crs = "el_crs".$lens_type;
					
					if(!empty($id)){
						$sql= " SELECT * FROM chart_sps_lens WHERE id_chart_sx_plan_sheet ='".$id."' AND lens_type='".$lens_type."' ";
						$row=sqlQuery($sql);
						if($row!=false){				
							$arr[$lens] = $row["lens_name"];
							$arr[$power] = $row["lens_pwr"];
							$arr[$cyl] = $row["lens_cyl"];
							$arr[$axis] = $row["lens_axis"];
							$arr[$used] = $row["lens_used"];					
							$arr[$targt] = $row["lens_target"];
							$arr[$acd] = $row["lens_acd"];
							$arr[$sp] = $row["lens_sp"];
							$arr[$crs] = $row["lens_crs"];
						}
					}			
				}
				
				//chart_sps_ast_assess
				foreach($arr_asti_as as $k => $asti_source){
				
					$asti_source_var=str_replace(" ","", $asti_source);		
					$magni = "el_magni".$asti_source_var;
					$magni_used = "el_magni_used".$asti_source_var;
					$axis = "el_axis".$asti_source_var;
					$axis_used = "el_axis_used".$asti_source_var;
					
					if(!empty($id)){
						$sql = "SELECT * FROM chart_sps_ast_assess where id_chart_sx_plan_sheet='".$id."'  AND ast_source='".imw_real_escape_string($asti_source)."' ";
						$row =  sqlQuery($sql);
						if($row!=false){
							$arr[$magni] = $row["magni_diopter"];
							$arr[$magni_used] = $row["magni_used"];
							$arr[$axis] = $row["axis"];
							$arr[$axis_used] = $row["axis_used"];
						}
					}
				}
				
				// //chart_sps_ast_plan_tpa
				if(!empty($id)){
					$sql = "SELECT * FROM chart_sps_ast_plan_tpa where id_chart_sx_plan_sheet='".$id."' ";
					$rez =  sqlStatement($sql);					
					for($i=1;$row=sqlFetchArray($rez);$i++){				
						$el_toric = "el_toric".$i;
						$el_power = "el_power".$i;
						$el_axis = "el_axis".$i;
						
						$arr[$el_toric]=$row["toric_model"];
						$arr[$el_power]=$row["power"];
						$arr[$el_axis]=$row["axis"];				
					}
				}
				*/
			}
			
			//
			return $arr;	
		}
		
		public function getPersonnal($flgCn="pro_only"){
			if($flgCn == "cn"){
				$utId = "";
				$utId .= implode($GLOBALS['arrValidCNPhy'],",");
				if(!empty($utId) && count($GLOBALS['arrValidCNTech'])>0){$utId .= ",";}
				$utId .= implode($GLOBALS['arrValidCNTech'],",");
			}elseif($flgCn == "cn2"){
				$utId = "";
				$utId .= implode($GLOBALS['arrValidCNPhy'],",");		
			}elseif($flgCn == 'pro_only'){
				// Fetch only physicians
				$utId = "1";
			}else{
				// Fetch physicians & technicians
				$utId = "1,3";
			}

			$qry = "select lname,fname,mname,id from users where user_type IN (".$utId.") AND delete_status = 0 order by user_type, fname, lname ";	
			$res = imw_query($qry);
			return $res;
		}
		
		//Returns Pt. Info
		public function get_patient_details(){
			$return_arr = array();
			$oPatient = new Patient($this->patient_id);
			$pt_name_dob = $oPatient->getName(6); //Name
			$ar_pt_name_dob=explode("--", $pt_name_dob);
			$pt_name = trim($ar_pt_name_dob[0]);
			$patientDOB = trim($ar_pt_name_dob[1]);
			$pt_age = $oPatient->getAge(); //age
			$pt_data = $oPatient->getPtInfo();			
			$return_arr['pt_name'] = $pt_name;
			$return_arr['pt_dob'] = $patientDOB;
			$return_arr['pt_age'] = $pt_age;
			$return_arr['ptId'] = $pt_data['id'];
			$return_arr['pt_gender'] = $pt_data['sex'];
			$return_arr['pt_street'] = $pt_data['street'];
			$return_arr['pt_street2'] = $pt_data['street2'];
			$return_arr['pt_phone_home'] = $pt_data['phone_home'];
			$return_arr['pt_phone_biz'] = $pt_data['phone_biz'];
			$return_arr['pt_city'] = $pt_data['city'];
			$return_arr['pt_state'] = $pt_data['state'];
			$return_arr['pt_postal_code'] = $pt_data['postal_code'];
			$return_arr['pt_primary_care_id'] = $pt_data['primary_care_id'];
						
			return $return_arr;
		}
		
		//Get Sx Refraction Values from previous chart
		public function getMasterDataIOL(&$site = '', &$patientId = ''){
			if(empty($site) === true || empty($patientId) === true) return false;
			$dosArr = $mainMrArr = $finalArr = $returnArr = array();
			$siteArr = array('OD', 'OS');
			
			//Get All DOS for patient
			$dosArr = $this->getPtDOS($patientId);
			
			//If Patient have any DOS
			if($dosArr && count($dosArr) > 0){
				foreach($dosArr as &$formID){
					//New Arr for every loop
					$prevMrVal = $newMrVal = array();
					$mainMrArr[$formID]['prevMr'] = array();
					
					//Get Chart Vision Values [ MR 1, MR 2, MR 3] for current formID as per OLD R6 logic as it is saved separately from other MR values
					//$prevMrVal = $this->getPrevMrVal($patientId, $formID);
					//if($prevMrVal && count($prevMrVal) > 0 && is_array($prevMrVal) === true) $mainMrArr[$formID]['prevMr'] = $prevMrVal;
					
					//Get Chart Vision Values [ MR 3 + ] for current formID as per New R8 logic as it is saved separately from MR 1, MR 2, MR 3 values
					$newMrVal = $this->getNewMrVal($patientId, $formID);
					if($newMrVal && count($newMrVal) > 0 && is_array($newMrVal) === true) $mainMrArr[$formID]['newMr'] = $newMrVal;
					
					//Get K's values for current formID
					$kValues = $this->getKValues($patientId, $formID, $site);
					if($kValues && count($kValues) > 0 && is_array($kValues) === true) $mainMrArr[$formID]['kValues'] = $kValues;
					
					//Get Packy Correction values for current formID
					$pachyValues = $this->getPachyCorrections($patientId, $formID, $site);
					// if($pachyValues && count($pachyValues) > 0 && is_array($pachyValues) === true) $mainMrArr[$formID]['pachyValues'] = $pachyValues;
					if($pachyValues) $mainMrArr[$formID]['pachyValues'] = $pachyValues;
					
					//Get SLE lens Summary
					$sleSummary = $this->getLensSLE($patientId, $site, $formID);
					if(empty($sleSummary) == false) $mainMrArr[$formID]['lensSummary'] = $sleSummary;
					
					//Get pupil dilated value
					$dilatedVal = $this->getPupDilated($patientId, $site, $formID);
					if(empty($dilatedVal) == false) $mainMrArr[$formID]['pupilDilate'] = $dilatedVal;
					
					$prevMrArr = (is_array($mainMrArr[$formID]['prevMr']) && count($mainMrArr[$formID]['prevMr']) > 0) ? $mainMrArr[$formID]['prevMr'] : '';
					$newMrArr = (is_array($mainMrArr[$formID]['newMr']) && count($mainMrArr[$formID]['newMr']) > 0) ? $mainMrArr[$formID]['newMr'] : '';
					
					// if(empty($prevMrArr) == false || empty($newMrArr) == false) break;
					
					// Added this to stop loop if any of the above value is present in the main arr
					if (is_array($mainMrArr[$formID]) && count($mainMrArr[$formID]) > 0) break;
					
				}
			}
			
			//If $mainMrArr has something in it , continue
			if(count($mainMrArr) > 0){
				foreach($mainMrArr as $obj){
					//Formatting MR values
					foreach($siteArr as $eyeSite){
						$prevValues = (is_array($obj['prevMr'][$eyeSite]) && count($obj['prevMr'][$eyeSite]) > 0) ? $obj['prevMr'][$eyeSite] : array();
						$newValues = (is_array($obj['newMr'][$eyeSite]) && count($obj['newMr'][$eyeSite]) > 0) ? $obj['newMr'][$eyeSite] : array();
						
						if(empty($prevValues) == false || empty($newValues) == false) $finalArr['MRValues'][$eyeSite] = array_merge($prevValues, $newValues);
					}
					
					//Assigning MR Values
					switch(strtoupper($site)){
						case 'OD':
							$returnArr['ref'] = end($finalArr['MRValues']['OD']);
							//$returnArr['oref'] = end($finalArr['MRValues']['OS']);
						break;
						
						case 'OS':
							$returnArr['ref'] = end($finalArr['MRValues']['OS']);
							//$returnArr['oref'] = end($finalArr['MRValues']['OD']);
						break;
					}
					
					//Assigning K's values
					if(is_array($obj['kValues']) && count($obj['kValues']) > 0){
						foreach($obj['kValues'] as $key => &$val){
							$returnArr[$key] = $val;
						}
					}
					
					//Assigning Pachy values
					// if(is_array($obj['pachyValues']) && count($obj['pachyValues']) > 0){
					if(isset($obj['pachyValues']) && empty($obj['pachyValues']) == false){
						$returnArr['pachy'] = $obj['pachyValues'];
					}
					
					//Assigning SLE Lens Summary
					if(isset($obj['lensSummary']) && empty($obj['lensSummary']) == false){
						$returnArr['lensSummary'] = $obj['lensSummary'];
					}
					
					//Assigning Pupil dilated value
					if(isset($obj['pupilDilate']) && empty($obj['pupilDilate']) == false){
						$returnArr['pupilDilate'] = $obj['pupilDilate'];
					}
				}
			}
			
			return json_encode($returnArr);
		}
		
		//Get Patient All DOS
		public function getPtDOS(&$patientId = ''){
			if(empty($patientId) === true) return false;
			$returnArr = array();
			
			$chkForm = imw_query('SELECT id FROM chart_master_table where patient_id = "'.$patientId.'"  AND delete_status = 0 ORDER BY date_of_service DESC');
			if($chkForm && imw_num_rows($chkForm) > 0){
				while($rowFetch = imw_fetch_assoc($chkForm)){
					array_push($returnArr,$rowFetch['id']);
				}
			}
			
			return $returnArr;
		}
		
		//Get Patient MR 1, MR 2, MR 3 values		
		//get Patient MR 3 + values
		public function getNewMrVal(&$patientId = '', &$formId = '', &$selSite = ''){
			if(empty($formId) === true || empty($patientId) === true) return false;
			$returnArr = $visElem = array();
			$where = '';
			
			//If $selSite get only data of that site
			if(empty($selSite) == false) $where = "and LOWER(chld.site) = '".strtolower($selSite)."' ";
			
			//Fields Mapping
			// --> It is done to merge Old Values of MR 1, MR 2, MR 3 with the new ones
			$mapArr = array(
				'sph' => 'S',
				'cyl' => 'C',
				'axs' => 'A',
				'txt_1' => '',
				'ad' => 'Add',
				'txt_2' => '',
				'sel_2' => '',
				'sel2v' => '',
			);
			
			//Get Multiple MR values after MR 3 i.e MR 4 and counting 
			$orderBy = " ORDER BY prnt.ex_number ASC ";
			$qry = '
				SELECT 
					chld.*,
					prnt.id,
					prnt.ex_number,
					prnt.ex_desc,
					prnt.prism_desc
				FROM 
					chart_vis_master master
				LEFT JOIN
					chart_pc_mr prnt ON (prnt.id_chart_vis_master = master.id AND prnt.delete_by = 0)
				LEFT JOIN
					chart_pc_mr_values chld ON (prnt.id = chld.chart_pc_mr_id)
				WHERE 
					master.patient_id ="'.$patientId.'" and 
					master.form_id = "'.$formId.'"  and 
					prnt.ex_type="MR" and
					prnt.delete_by = 0 '.$where.$orderBy;
			$chkQry = imw_query($qry);
			if($chkQry && imw_num_rows($chkQry) > 0){
				while($rowFetch = imw_fetch_assoc($chkQry)){
					$mrNumber = (isset($rowFetch['ex_number']) && empty($rowFetch['ex_number']) == false) ? $rowFetch['ex_number'] : '';
					$mrSite = (isset($rowFetch['site']) && empty($rowFetch['site']) == false) ? $rowFetch['site'] : '';
					
					
					$mrNewVal = array();
					$mrNewValStr = '';
					//Loop through required fields declared in $mapArr
					if(count($mapArr) > 0){
						foreach($mapArr as $key => $val){
							if($rowFetch[$key] == "20/") continue;
							$mrNewVal[] = (isset($rowFetch[$key]) && empty($rowFetch[$key]) == false) ? $val.''.$rowFetch[$key] : '';
						}
					}
					
					//Filter empty values out
					$mrNewVal = array_filter($mrNewVal);
					
					if(count($mrNewVal) > 0) $mrNewValStr = implode(', ', $mrNewVal);
					if(empty($mrNumber) == false && empty($mrSite) == false && empty($mrNewValStr) === false) $returnArr[$mrSite][$mrNumber] = $mrNewValStr;
				}
			}
			return $returnArr;
		}
		
		//Get K values 
		public function getKValues(&$patientId = '', &$formId = '', &$eyeSite = ''){
			if(empty($patientId) === true || empty($formId) === true || empty($eyeSite) === true) return false;
			$returnArr = array();
			
			$eyeSite = strtoupper($eyeSite);
			
			//Get Values
			$qry = 'SELECT k_od AS vis_ak_od_k, slash_od AS vis_ak_od_slash, x_od AS vis_ak_od_x, k_os AS vis_ak_os_k, slash_os AS vis_ak_os_slash, x_os AS vis_ak_os_x 
					FROM chart_vis_master c1 
					LEFT JOIN chart_ak c2 ON c2.id_chart_vis_master = c1.id
					WHERE c1.patient_id ="'.$patientId.'" and c1.form_id = "'.$formId.'" ';
			$chkQry = imw_query($qry);
			
			if($chkQry && imw_num_rows($chkQry) > 0){
				$rowFetch = imw_fetch_assoc($chkQry);
				
				$returnArr["kflat"] = ($eyeSite == "OD") ? $rowFetch["vis_ak_od_k"] : $rowFetch["vis_ak_os_k"] ;
				$returnArr["ksteep"] = ($eyeSite == "OD") ? $rowFetch["vis_ak_od_slash"] : $rowFetch["vis_ak_os_slash"] ;
				$returnArr["kaxis"] = ($eyeSite == "OD") ? $rowFetch["vis_ak_od_x"] : $rowFetch["vis_ak_os_x"] ;
				$returnArr["kcyl"] = "";
				
				/* $returnArr["okflat"] = ($eyeSite == "OD") ? $rowFetch["vis_ak_os_k"] : $rowFetch["vis_ak_od_k"] ;
				$returnArr["oksteep"] = ($eyeSite == "OD") ? $rowFetch["vis_ak_os_slash"] : $rowFetch["vis_ak_od_slash"] ;
				$returnArr["okaxis"] = ($eyeSite == "OD") ? $rowFetch["vis_ak_os_x"] : $rowFetch["vis_ak_od_x"] ;
				$returnArr["okcyl"] = "";	 */
			}
			
			return $returnArr;
		}
		
		//Get Pachy correction values
		public function getPachyCorrections(&$patientId = '', &$formId = '', &$eyeSite = ''){
			if(empty($patientId) === true || empty($formId) === true || empty($eyeSite) === true) return false;
			$returnArr = array();
			
			//Get values
			$sql = "SELECT reading_od,avg_od,cor_val_od,reading_os,avg_os,cor_val_os,cor_date FROM chart_correction_values WHERE form_id='".$formId."' AND patient_id='".$patientId."' ";
			$chkQry = imw_query($sql);
			
			if($chkQry && imw_num_rows($chkQry) > 0){
				$row = imw_fetch_assoc($chkQry);
				
				$odReadings = trim($row["reading_od"]);
				$odAverage = trim($row["avg_od"]);
				$odCorrection_value = trim($row["cor_val_od"]);		
				$osReadings = trim($row["reading_os"]);
				$osAverage = trim($row["avg_os"]);
				$osCorrection_value = trim($row["cor_val_os"]);
				if(!empty($row["cor_date"]) && ($row["cor_date"] != "0000-00-00")) $elem_cor_date = get_date_format($row["cor_date"]);
				
				
				//Setting case based on Site
				switch(strtoupper($eyeSite)){
					case 'OD':
						if(empty($odAverage) === false) $odAverage = $odAverage." ";
						$returnArr = $odReadings." ".$odAverage." ".$odCorrection_value;
					break;
					
					case 'OS':
						if(!empty($osAverage)) $osAverage = $osAverage." ";
						$returnArr = $osReadings." ".$osAverage." ".$osCorrection_value;
					break;	
				}
				
			}
			return $returnArr;
		}
		
		//Returns a formatted array for visible MR elements
		public function formatVisElem($arr = array()){
			if(count($arr) == 0) return false;
			$returnArr = array();
			
			foreach($arr as $obj){
				$StrArr = explode('=',$obj);
				$key = (isset($StrArr[0]) && empty($StrArr[0]) == false) ? $StrArr[0] : '';
				$val = (isset($StrArr[1]) && empty($StrArr[1]) == false) ? $StrArr[1] : '';
				
				if(empty($key) == false && empty($val) == false) $returnArr[$key] = $val;
			}
			
			return $returnArr;
		}
		
		//Returns Lens type and their values for provided ID
		public function getLensType($provID = '', $planChartId = '', $lensArr = array()){
			$returnArr = $dbArr = array();
			
			//Check Sx Plan sheet saved values
			$sql = imw_query("SELECT lens_type FROM chart_sps_lens where id_chart_sx_plan_sheet='".$planChartId."' AND prov_id = '".$provID."' ");
			if($sql && imw_num_rows($sql) > 0){
				while($row = imw_fetch_assoc($sql)){
					$dbArr[] = (int)$row['lens_type'];
				}
			}
			
			//If No $lensArr is Provided set it to default $arr_lens as it is of count 4 thts why used
			if(count($lensArr) == 0) $lensArr = $this->arr_lens;
			
			//If their are values in DB change $lensArr to $dbArr
			if(count($dbArr) > 0) $lensArr = $dbArr;

			//Get Order for selected lens based on their type [ Primary, backup1 etc ]
			$lensTypeOrder = array();
			if(empty($planChartId) == false){
				$chkOrder = " SELECT indx,lens_type FROM chart_sps_ast_plan_tpa WHERE id_chart_sx_plan_sheet = ".$planChartId." ORDER BY indx ASC ";
				$resOrder = imw_query($chkOrder) or die(imw_error());
				if($resOrder && imw_num_rows($resOrder) > 0){
					while($rowOrder = imw_fetch_assoc($resOrder)){
						$rowIndex = $rowOrder['indx'];
						$type = $rowOrder['lens_type'];
	
						if($type) $lensTypeOrder[$rowIndex] = $type;
					}
				}
			}
			
			if(count($lensTypeOrder) > 0) $lensTypeOrder = array_unique($lensTypeOrder);
			
			//Get Lens values from DB
			if(!empty($planChartId)){
				$sql= imw_query(" 
					SELECT 
						lens_type as Type,
						lens_pwr as Pwr,
						lens_cyl as Cyl,
						lens_axis as Axis,
						lens_used as Used,
						lens_target as Target,
						lens_acd as Acd,
						lens_sp as Sp,
						lens_crs as Crs
					FROM 
						chart_sps_lens 
					WHERE 
						id_chart_sx_plan_sheet ='".$planChartId."' 
						AND prov_id = '".$provID."'
				");
				if($sql && imw_num_rows($sql) > 0){
					while($row = imw_fetch_assoc($sql)){
						$row['ID'] = $row['Type'];
						$returnArr[$row['Type']] = $row;
					}
				}
			}
			
			//if No DB values return static values
			if(count($returnArr) == 0){
				foreach($lensArr as $k => $lens_type){
					//If $lens_type is INT than it is from DB
					//If lens_type not INT than it means no db values are present, send static ones instead
					
					if(is_int($lens_type)) $this->lensFields['ID'] = $lens_type;
					$returnArr[] = $this->lensFields;
				}
			}else{
				//If lens are saved, order them accordingly
				if(count($lensTypeOrder) > 0){
					//pre($lensTypeOrder);
					$tmpArr = array();
					// pre($returnArr);
					// pre($lensTypeOrder);
					foreach ($lensTypeOrder as $index => $lensType) {
						if($returnArr[$lensType]){
							$tmpArr[] = $returnArr[$lensType];
							unset($returnArr[$lensType]);
						}
					}
				}

				//if something is still in returnArr, return that also
				if(count($returnArr) > 0){
					foreach($returnArr as $objKey => $objVal){
						$tmpArr[] = $objVal;
						unset($returnArr[$objKey]);
					}
				}
				if(count($tmpArr) > 0) $returnArr = $tmpArr;
			}
			
			/* if(count($lensArr) > 0){
				foreach($lensArr as $k => $lens_type){
					if(!empty($planChartId)){
						$sql= imw_query(" 
							SELECT 
								lens_type as Type,
								lens_pwr as Pwr,
								lens_cyl as Cyl,
								lens_axis as Axis,
								lens_used as Used,
								lens_target as Target,
								lens_acd as Acd,
								lens_sp as Sp,
								lens_crs as Crs
							FROM 
								chart_sps_lens 
							WHERE 
								id_chart_sx_plan_sheet ='".$planChartId."' 
								AND prov_id = '".$provID."'
						");
						if($sql && imw_num_rows($sql) > 0){
							while($row = imw_fetch_assoc($sql)){
								$row['ID'] = $row['lens_type'];
								$returnArr[] = $row;
							}
						}else{
							$this->lensFields['ID'] = $lens_type;
							$returnArr[] = $this->lensFields;
						}
					}else{
						$this->lensFields['ID'] = $lens_type;
						$returnArr[] = $this->lensFields;
					}
				}	
			} */
			
			return $returnArr;
		}
		
		//Get All IOL Defined Lenses
		public function iolLenArr($id = array()){
			$where = '';
			if(count($id) > 0) $where = 'WHERE iol_type_id in ('.implode(',',$id).')';
			
			$returnArr = array();
			$getLensesListStr = "SELECT iol_type_id,lenses_iol_type,lenses_category FROM lenses_iol_type ".$where." ORDER BY lenses_category";
			$getLensesListQry = imw_query($getLensesListStr);
			while ($getLensesListRow = imw_fetch_array($getLensesListQry)) {
				$iol_type_id = $getLensesListRow['iol_type_id'];
				$lenses_iol_type = $getLensesListRow['lenses_iol_type'];
				$returnArr[$iol_type_id] = array('lensType' => $lenses_iol_type, 'category' => $getLensesListRow['lenses_category']);
			}
			
			return $returnArr;
		}
		
		//Pull Lens Data for the selected provider
		public function getIOLLens($provId = '', $onlyProv = false){
			$returnArr = array();
			if(empty($provId) === true) return $returnArr;
			
			//Get lenses defined for the provided provider id
			$getDefinedStr = "SELECT iol_type_id FROM lensesdefined WHERE physician_id = '$provId'";
					
			$getDefinedQry = imw_query($getDefinedStr);
			
			if($getDefinedQry && imw_num_rows($getDefinedQry) > 0){
				while ($getDefinedRows = imw_fetch_array($getDefinedQry)) {
					$returnArr[] = (int)$getDefinedRows['iol_type_id'];
				}
			}
			
			return $returnArr;
		}
		
		//Returns Provided Test Data
		public function getTestDetails($callFrom = '', $tstId = ''){
			if(empty($callFrom) || empty($tstId)) return false;
			$values = '';
			$returnArr = array();
			
			$values = (isset($this->callArr[strtolower($callFrom)])) ? $this->callArr[strtolower($callFrom)] : '';
			if(empty($values) == false){
				$table = $values['table'];
				$search_id = $values['id'];
				
				$sqlQry = imw_query('SELECT examDate from '.$table.' WHERE '.$search_id.' = '.$tstId.' ');
				if($sqlQry && imw_num_rows($sqlQry) > 0){
					$rowFetch = imw_fetch_assoc($sqlQry);
					$date = (isset($rowFetch['examDate']) && empty($rowFetch['examDate']) == false) ? date('m-d-Y', strtotime($rowFetch['examDate'])) : '';
					$returnArr['Date'] = $date;
					$returnArr['Name'] = $values['name'];
				}
			}
			
			return $returnArr;
		}
		
		//Return IOL model values 
		public function getIolModelValues($chartId = '', $provId = ''){
			//if(empty($chartId) || empty($provId)) return false;
			$returnArr = array();
			$staticArr = array('ID' => '', 'ChartId' => '', 'Type' => '', 'Model' => '', 'Power' => '', 'Axis' => '', 'Index' => '');
			
			foreach($this->arr_lens as $key => &$val){
				if(empty($chartId) == false){
					$sql = imw_query("
						SELECT 
							id as ID, 
							id_chart_sx_plan_sheet as ChartId, 
							lens_type as Type, 
							toric_model as Model, 
							power as Power, 
							axis as Axis,
							indx as 'Index'
						FROM 
							chart_sps_ast_plan_tpa 
						WHERE 
							id_chart_sx_plan_sheet='".$chartId."' 
							AND indx = '".$key."' AND prov_id = '".$provId."'
					");
					if($sql && imw_num_rows($sql) > 0){
						while($row = imw_fetch_assoc($sql)){
							$returnArr[] = $row;
						}
					}else{
						$staticArr['Index'] = $key;
						$returnArr[] = $staticArr;
					}
				}else{
					$staticArr['Index'] = $key;
					$returnArr[] = $staticArr;
				}
			}
			
			return $returnArr;
		}
		
		//Get Last Sheet Previous Eye values
		public function getPrevEyeValues($patientId = '', $chartId = ''){
			if(empty($patientId)) return false;
			$sheetArr = $returnArr = array();
			
			//Get All Sx plan sheets for patient
			$sheetArr = $this->get_pt_sx_dos_arr($patientId);
			
			//If chart ID is there, remove it from here
			if(empty($chartId) == false){
				$sxDate = '';
				if(is_array($sheetArr[$chartId]) && count($sheetArr[$chartId]) > 0){
					$sxDate = $sheetArr[$chartId]['sx_plan_dos'];
				}
				
				foreach($sheetArr as $key => $val){
					$arrDate = strtotime($val['sx_plan_dos']);
					if(strtotime($sxDate) == $arrDate || $arrDate > strtotime($sxDate)){
						unset($sheetArr[$key]);
					}	
				}
			}
			
			if(count($sheetArr) > 0){
				foreach($sheetArr as $sheetKey => &$sheetObj){
					//if(strtotime($sheetObj['sx_plan_dos']) !== strtotime(date('Y-m-d'))){
						$returnArr = $this->sps_get_load_prv_val($sheetKey);
						$returnArr['Date'] = $sheetObj['sx_plan_dos'];
					//}
					
					break;
				}
			}
			
			if(count($returnArr) > 0){
				//Get Primary IOL Lens Values
				$primaryArr = array();
				
				$valueArr = $this->getIolModelValues($returnArr['ID'], $returnArr['surgeonId']);
				if(count($valueArr) > 0){
					foreach($valueArr as $obj){
						if(isset($obj['Index']) && $obj['Index'] == 0){
							$primaryArr = $obj;
						}
						if(count($primaryArr) > 0) break;
					}
				}
				
				$lensPrimArr = array();
				$lensArr = $this->getLensType($returnArr['surgeonId'], $returnArr['ID'], $selProvLens);
				foreach($lensArr as $lensObj){
					$lensId = $primaryArr['Type'];
					if($lensId == $lensObj['ID']){
						$returnArr['el_prev_eye_lens'] = $this->iol_lenses[$lensId]['lensType'];
						$returnArr['el_prev_eye_power'] = $lensObj['Pwr'];
						$returnArr['el_prev_eye_cyl'] = $lensObj['Cyl'];
						$returnArr['el_prev_eye_axis'] = $lensObj['Axis'];
						
						break;
					}
				}
			}
			
			return $returnArr;
		}
		
		public function getSxPlanReportData($params = array()){
			if(count($params) == 0) return false;
			$replaceArr = $returnArr = array();
			$htmlData = $finalData = '';
			
			$patientId 		= 	$params['ptId'];
			$planId 		=	$params['planId'];
			$provId 		=	$params['Provider'];
			$createDate	 	=	$params['createDate'];
			$surgeryDate 	=	$params['surgeryDt'];
			
			//Template File
			include($GLOBALS['fileroot'].'/interface/reports/sx_planning_sheet_template.php');
			$htmlData = &$printTemplate;	//it is declared in - sx_planning_sheet_template.php
			
			//Patient Details
			$ptDetails = $this->get_patient_details();
			
			//Sx Plan Data
			$sxData = $this->get_sx_plan_data($this->patient_id,$this->sx_plan_id);
			
			//Sx Surgeon Array
			$phyArr = $this->getMrPersonnal(2,"cn2");
			
			//Sx Tests Master Replacement Data
			//$masterTestData = $this->getTestData($patientId,$surgeryDate,$sxData['el_mank_eye']);
			
			//Replace Data array - replaces tags with values in - sx_planning_sheet_template.php
			$replaceArr['{PATIENT_NAME}'] = (isset($ptDetails['pt_name']) && empty($ptDetails['pt_name']) == false) ? $ptDetails['pt_name'] : '';
			$replaceArr['{DOB}'] = (isset($ptDetails['pt_dob']) && empty($ptDetails['pt_dob']) == false) ? $ptDetails['pt_dob'] : '';
			$replaceArr['{AGE}'] = (isset($ptDetails['pt_age']) && empty($ptDetails['pt_age']) == false) ? $ptDetails['pt_age'] : '';
			$replaceArr['{PatientId}'] = (isset($ptDetails['ptId']) && empty($ptDetails['ptId']) == false) ? $ptDetails['ptId'] : '';
			$replaceArr['{CREATEDATE}'] = (isset($createDate) && empty($createDate) == false) ? date('m-d-Y', strtotime($createDate)) : '';
			
			$replaceArr['{SITE}'] = (isset($sxData['el_mank_eye']) && empty($sxData['el_mank_eye']) == false) ? $sxData['el_mank_eye'] : '';
			$replaceArr['{DOMINANT_EYE}'] = (isset($sxData['el_domi']) && empty($sxData['el_domi']) == false) ? $sxData['el_domi'] : '';
			
			$replaceArr['{REFRACTION}'] = (isset($sxData['el_refraction']) && empty($sxData['el_refraction']) == false) ? $sxData['el_refraction'] : '';
			$replaceArr['{OTHER_EYE_REFRACTION}'] = (isset($sxData['el_othr_eye_ref']) && empty($sxData['el_othr_eye_ref']) == false) ? $sxData['el_othr_eye_ref'] : '';
			
			$replaceArr['{DOS}'] = (isset($sxData['el_date_surgery']) && empty($sxData['el_date_surgery']) == false && $sxData['el_date_surgery'] !== '00-00-0000') ? $sxData['el_date_surgery'] : '';
			$replaceArr['{SURGERY_TIME}'] = (isset($sxData['el_time_surgery']) && empty($sxData['el_time_surgery']) == false) ? $sxData['el_time_surgery'] : '';
			
			$replaceArr['{K_FLAT}'] = (isset($sxData['el_k_flat']) && empty($sxData['el_k_flat']) == false) ? $sxData['el_k_flat'] : '';
			$replaceArr['{K_STEEP}'] = (isset($sxData['el_k_steep']) && empty($sxData['el_k_steep']) == false) ? $sxData['el_k_steep'] : '';
			$replaceArr['{K_AXIS}'] = (isset($sxData['el_k_axis']) && empty($sxData['el_k_axis']) == false) ? $sxData['el_k_axis'] : '';
			$replaceArr['{K_CYL}'] = (isset($sxData['el_k_cyl']) && empty($sxData['el_k_cyl']) == false) ? $sxData['el_k_cyl'] : '';
			
			$replaceArr['{OTHER_K_FLAT}'] = (isset($sxData['el_ok_flat']) && empty($sxData['el_ok_flat']) == false) ? $sxData['el_ok_flat'] : '';
			$replaceArr['{OTHER_K_STEEP}'] = (isset($sxData['el_ok_steep']) && empty($sxData['el_ok_steep']) == false) ? $sxData['el_ok_steep'] : '';
			$replaceArr['{OTHER_K_AXIS}'] = (isset($sxData['el_ok_axis']) && empty($sxData['el_ok_axis']) == false) ? $sxData['el_ok_axis'] : '';
			$replaceArr['{OTHER_K_CYL}'] = (isset($sxData['el_ok_cyl']) && empty($sxData['el_ok_cyl']) == false) ? $sxData['el_ok_cyl'] : '';
			
			$replaceArr['{SURGEON_NAME}'] = (isset($phyArr[$sxData['el_surgeon_id']]) && empty($phyArr[$sxData['el_surgeon_id']]) == false) ? $phyArr[$sxData['el_surgeon_id']] : '';
			
			$replaceArr['{PRI PROC}'] = (isset($sxData['el_proc_prim']) && empty($sxData['el_proc_prim']) == false) ? $sxData['el_proc_prim'] : '';
			$replaceArr['{SEC PROC}'] = (isset($sxData['el_proc_sec']) && empty($sxData['el_proc_sec']) == false) ? $sxData['el_proc_sec'] : '';
			
			//Getting Lens Values
			$counter = 0;

			//Surgeon IOL Lenses
			$selProvLens = $this->getIOLLens($sxData['el_surgeon_id'], true);
			
			//if(is_array($selProvLens) && count($selProvLens) > 0){
				//Get Lens Values
				$lensArr = $this->getLensType($sxData['el_surgeon_id'], $sxData['id_chart_sx_plan_sheet'], $selProvLens);
			//}
			$pdfLensVal = '';
			
			//If No lenses found add default four lens row
			$defaultLensRow = '';
			foreach($this->lensFields as $obj){
				$defaultLensRow .= '<td style="width:77px; height:20px;" align="center" valign="middle" class="bordered font_12"></td>';
			}
			
			for($i = 0; $i < 4; $i++){
				$fieldStr = '';
				$obj = $lensArr[$i];
				if(is_array($obj) && count($obj) > 0){
					//For PDF
					if(isset($obj['Used']) && empty($obj['Used']) == false){
						$pdf_used_val = 'Yes';
						$pdf_used_bg = ' hylight';
					}else{
						$pdf_used_val = '';
						$pdf_used_bg = '';
					}
					$counter = 1;
					foreach($obj as $key => &$val){
						//If want to show border on every side of the column use this code
						$pdf_arr_lens_classname = $pdf_border_class;
						
						//PDF Borders
						$brdrFirst = ($counter == 1) ? 'bdrlft' : '';
						$brdrlst = ($counter == count($obj)) ? 'bdrRght' : '';
						
						if($key == 'Type'){
							$val = $this->iol_lenses[$val]['lensType'];
						}
						
						if($key == 'Used'){
							if(empty($val) == false && $val == 1) $val = 'Yes';
							else $val = '';
						}
						
						if($key == 'ID') continue;
						
						$fieldStr .= '<td style="width:77px; height:20px;" align="center" valign="middle" class="bordered font_12">'.$val.'</td>';
						
						$counter++;
					}
				}
				if(empty($fieldStr) == false) $pdfLensVal .= '<tr>'.$fieldStr.'</tr>';
				else $pdfLensVal .= '<tr>'.$defaultLensRow.'</tr>';
			}
			
			
			//Surgeon IOL Model Values
			$pdf_chart_sx_plan_sheet = '';
			$valueArr = $this->getIolModelValues($sxData['id_chart_sx_plan_sheet'],$sxData['el_surgeon_id']);
			
			foreach($valueArr as $obj){
				$fieldName = $this->arr_lens[$obj['Index']];
				$lensName = $this->iol_lenses[$obj['Type']]['lensType'];
				
				$iolToric = $obj['Model'];
				$iolPower = $obj['Power'];
				$iolAxis = $obj['Axis'];
				
				if(empty($fieldName) == false && empty($lensName) == false){
					$pdf_chart_sx_plan_sheet .= '
						<tr>
							<td class="bordered font_12 bdrlft pd '.$pdf_border_class.'" style="height:20px;width:25%"><strong>'.$fieldName.'</strong> - '.$lensName.'</td>
							<td class="bordered font_12 '.$pdf_border_class.' pd" style="height:20px;text-align:center;width:25%">'.$iolPower.'</td>
							<td class="bordered font_12 pd '.$pdf_border_class.'" style="height:20px;text-align:center;width:25%">'.$iolToric.'</td>
							<td class="bordered font_12 bdrRght '.$pdf_border_class.' pd" style="height:20px;text-align:center;width:25%">'.$iolAxis.'</td>
						</tr>';
				}
			}
			
			if(empty($pdf_chart_sx_plan_sheet) == true){
				foreach($this->arr_lens as $obj){
					$staticArr = array('Model' => '', 'Power' => '', 'Axis' => '', 'Index' => '');
					$staticFiedls = '';
					$counterrr = 1;
					foreach($staticArr as $key => &$val){
						$borderStart = ($counterrr == 1) ?  'bdrlft' : '';
						$borderEnd = ($counterrr == count($staticArr)) ?  'bdrRght' : '';
						
						if(strtolower($key) == 'primary'){
							$staticFiedls .= '<td class="font_12 bordered '.$borderStart.' '.$borderEnd.'" style="height:20px;width:25%"><strong>'.$obj.'</strong> - &nbsp;</td>';
						}else{
							$staticFiedls .= '<td class="font_12 bordered '.$borderStart.' '.$borderEnd.'" style="height:20px;width:25%">&nbsp;</td>';
						}
						
						$counter++;
					}
					$pdf_chart_sx_plan_sheet .= '<tr>'.$staticFiedls.'</tr>';
				}
			}
			
			$replaceArr['{COMMENTS}'] = (isset($sxData['el_iol_desc']) && empty($sxData['el_iol_desc']) == false) ? $sxData['el_iol_desc'] : '';
			//$replaceArr['{EXTRA_SURGEON_TECH_ROW}'] = $surgeonTechRow; //it is declared in - sx_planning_sheet_template.php
			$replaceArr['{EXTRA_LENS_ROW}'] = $pdfLensVal; //it is declared in - sx_planning_sheet_template.php
			$replaceArr['{EXTRA_IOL_MODEL_ROW}'] = $pdf_chart_sx_plan_sheet; //it is declared in - sx_planning_sheet_template.php
			
			//Separating $replaceArr keys and values for replacement
			$replace = array_keys($replaceArr);
			$replaceWith = array_values($replaceArr);
			
			$htmlData = str_replace($replace,$replaceWith,$htmlData);
			if(empty($htmlData) == false) $finalData = '<page>'.$htmlData.'</page>';
			
			if(empty($finalData) === false) $returnArr = $finalData;	
			
			return $returnArr;
		}
		
		public function getIolMasterData($params = array()){
			if(count($params) == 0) return false;
			$replaceArr = $returnArr = $functionNameArr = array();
			$htmlData = $finalData = '';
			
			$functionNameArr = $this->getFormulaHeadArr();
			
			$patientId 		= 	$params['ptId'];
			$iolMasterId 	=	$params['iolMasterId'];
			$provId 		=	$params['Provider'];
			$createDate	 	=	$params['createDate'];
			
			$siteArr = array('OD', 'OS');
			
			//Fields Mapping
			$testFields = array(
				'MRVALUES' => array(
					'mrS{SITE}' => 'mrS',
					'mrC{SITE}' => 'mrC',
					'mrA{SITE}' => 'mrA',
					'vision{SITE}' => 'vision',
					'glare{SITE}' => 'glare'
				),
				
				'KVALUES' => array(
					'performedBy{SITE}' => 'performedKvalue',
					'date{SITE}' => 'performedDateKvalue',
					'autoSelect{SITE}' => 'KVALUEHEAD1',
					'iolMasterSelect{SITE}' => 'KVALUEHEAD2',
					'topographerSelect{SITE}' => 'KVALUEHEAD3'
				),
				
				'KVALUESARR' => array(
					'k1Auto1{SITE}' => 'auto1Value1',
					'k1Auto2{SITE}' => 'auto1Value2',
					'k1IolMaster1{SITE}' => 'iol1Value1',
					'k1IolMaster2{SITE}' => 'iol1Value2',
					'k1Topographer1{SITE}' => 'topo1Value1',
					'k1Topographer2{SITE}' => 'topo1Value2',
					'k2Auto1{SITE}' => 'auto2Value1',
					'k2Auto2{SITE}' => 'auto2Value2',
					'k2IolMaster1{SITE}' => 'iol2Value1',
					'k2IolMaster2{SITE}' => 'iol2Value2',
					'k2Topographer1{SITE}' => 'topo2Value1',
					'k2Topographer2{SITE}' => 'topo2Value2',
					'cylAuto1{SITE}' => 'cylAuto1',
					'cylAuto2{SITE}' => 'cylAuto2',
					'cylIolMaster1{SITE}' => 'cylIOL1',
					'cylIolMaster2{SITE}' => 'cylIOL2',
					'cylTopographer1{SITE}' => 'cylTopo1',
					'cylTopographer2{SITE}' => 'cylTopo2',
					'aveAuto{SITE}' => 'aveAuto',
					'aveIolMaster{SITE}' => 'aveIOL',
					'aveTopographer{SITE}' => 'aveTopo',
				),
				
				'LENSVALUES' => array(
					'performedByPhyOD' => 'performedByOD',
					'performedIolOS' => 'performedByOS',
					'powerIol{SITE}' => 'LENSHEAD1',
					'holladay{SITE}' => 'LENSHEAD2',
					'srk_t{SITE}' => 'LENSHEAD3',
					'hoffer{SITE}' => 'LENSHEAD4',
					'selecedIOLs{SITE}' => 'PrimaryLensID',
					'notes{SITE}' => 'Comments',
				),
				
				'LENSVALUEARR' => array(
					'iol1{SITE}' => 'iol1',
					'iol2{SITE}' => 'iol2',
					'iol3{SITE}' => 'iol3',
					'iol4{SITE}' => 'iol4',
					
					'iol1Power{SITE}' => 'iolPower1',
					'iol2Power{SITE}' => 'iolPower2',
					'iol3Power{SITE}' => 'iolPower3',
					'iol4Power{SITE}' => 'iolPower4',
					
					'iol1Holladay{SITE}' => 'iolHollDay1',
					'iol2Holladay{SITE}' => 'iolHollDay2',
					'iol3Holladay{SITE}' => 'iolHollDay3',
					'iol4Holladay{SITE}' => 'iolHollDay4',
					
					'iol1srk_t{SITE}' => 'iolSrk1',
					'iol2srk_t{SITE}' => 'iolSrk2',
					'iol3srk_t{SITE}' => 'iolSrk3',
					'iol4srk_t{SITE}' => 'iolSrk4',
					
					'iol1Hoffer{SITE}' => 'iolHoffer1',
					'iol2Hoffer{SITE}' => 'iolHoffer2',
					'iol3Hoffer{SITE}' => 'iolHoffer3',
					'iol4Hoffer{SITE}' => 'iolHoffer4'
				)
			);
			
			//Fetching Fields values
			$testData = array();
			foreach($siteArr as $eyeSite){
				if(count($testFields) > 0){
					foreach($testFields as $key => &$val){
						$arrKey = $key;
						$sqlQry = '';
						
						if(is_array($val) && count($val) > 0){
							$tmpArr = array();
							foreach($val as $fieldNm => &$fieldGetNm){
								$fieldNm = str_replace('{SITE}', strtoupper($eyeSite),$fieldNm);
								$str = $fieldNm.' AS '.$fieldGetNm;
								array_push($tmpArr, $str);
							}
							if(count($tmpArr) > 0) $sqlQry = implode(', ', $tmpArr);		
						}
						
						if(empty($sqlQry) == false && empty($iolMasterId) == false){
							//If fields are there thn fetch data
							$qry = imw_query('SELECT '.$sqlQry.' FROM iol_master_tbl where iol_master_id = '.$iolMasterId.' ');
							if($qry && imw_num_rows($qry) > 0){
								$rowFetch = imw_fetch_assoc($qry);
								if(!isset($testData[$eyeSite][$arrKey])) $testData[$eyeSite][$arrKey] = array();
								$testData[$eyeSite][$arrKey] = $rowFetch;
							}
						}
					}
				}
			}
			
			if(count($testData) > 0){
				foreach($testData as $testEye => $testValues){
					//Template File
					include($GLOBALS['fileroot'].'/interface/reports/sx_planning_sheet_iol_template.php');
					$htmlData = &$printTemplate;	//it is declared in - sx_planning_sheet_template.php
					
					//Patient Details
					$ptDetails = $this->get_patient_details();
					
					//Sx Surgeon Array
					$phyArr = $this->getMrPersonnal(2,"cn2");
					
					
					//Replace Data array - replaces tags with values in - sx_planning_sheet_template.php
					$replaceArr['{PATIENT_NAME}'] = (isset($ptDetails['pt_name']) && empty($ptDetails['pt_name']) == false) ? $ptDetails['pt_name'] : '';
					$replaceArr['{DOB}'] = (isset($ptDetails['pt_dob']) && empty($ptDetails['pt_dob']) == false) ? $ptDetails['pt_dob'] : '';
					$replaceArr['{AGE}'] = (isset($ptDetails['pt_age']) && empty($ptDetails['pt_age']) == false) ? $ptDetails['pt_age'] : '';
					$replaceArr['{PatientId}'] = (isset($ptDetails['ptId']) && empty($ptDetails['ptId']) == false) ? $ptDetails['ptId'] : '';
					$replaceArr['{CREATEDATE}'] = (isset($createDate) && empty($createDate) == false) ? trim(date('m-d-Y', strtotime($createDate))) : '';
					$replaceArr['{SITE}'] = (isset($testEye) && empty($testEye) == false) ? $testEye : '';
					
					//Replacing MR Values
					$mrRepStr = '';
					if(isset($testValues['MRVALUES']) && is_array($testValues['MRVALUES'])){
						foreach($testValues['MRVALUES'] as &$objvals){
							$mrRepStr .= '<td style="height:26px; width:20%;text-align:center " valign="bottom" class="bordered">'.$objvals.'</td>';
						}
						if(empty($mrRepStr) == false) $mrRepStr = '<tr>'.$mrRepStr.'</tr>';
					}
					$replaceArr['{MRVALUES}'] = (isset($mrRepStr) && empty($mrRepStr) == false) ? $mrRepStr : '';
				
					//Replacing K values
					if(isset($testValues['KVALUES']) && is_array($testValues['KVALUES'])){
						$phyID = &$testValues['KVALUES']['performedKvalue'];
						$headValues = &$testValues['KVALUES'];
						$replaceArr['{KVALUESPERFORMED}'] = (isset($phyArr[$phyID]) && empty($phyArr[$phyID]) == false) ? $phyArr[$phyID] : '';
						$replaceArr['{KHEADING1}'] = (isset($headValues['KVALUEHEAD1']) && empty($headValues['KVALUEHEAD1']) == false) ? $this->getKHeadingName($headValues['KVALUEHEAD1']) : '';
						$replaceArr['{KHEADING2}'] = (isset($headValues['KVALUEHEAD2']) && empty($headValues['KVALUEHEAD2']) == false) ? $this->getKHeadingName($headValues['KVALUEHEAD2']) : '';
						$replaceArr['{KHEADING3}'] = (isset($headValues['KVALUEHEAD3']) && empty($headValues['KVALUEHEAD3']) == false) ? $this->getKHeadingName($headValues['KVALUEHEAD3']) : '';
						$replaceArr['{KVALUESDATE}'] = (isset($headValues['performedDateKvalue']) && empty($headValues['performedDateKvalue']) == false && $headValues['performedDateKvalue'] != '0000-00-00') ? $headValues['performedDateKvalue'] : '';
					}
					
					//Replacing Multiple K values
					$kvalStr = $fieldStr = '';
					if(isset($testValues['KVALUESARR']) && is_array($testValues['KVALUESARR'])){
						//K values arr 
						$kValArr = array(
							'K1' => array(
								'auto1Value1' => 'auto1Value2',
								'iol1Value1' => 'iol1Value2',
								'topo1Value1' => 'topo1Value2'
							),
							'K2' => array(
								'auto2Value1' => 'auto2Value2',
								'iol2Value1' => 'iol2Value2',
								'topo2Value1' => 'topo2Value2'
							),
							'CYL' => array(
								'cylAuto1' => 'cylAuto2',
								'cylIOL1' => 'cylIOL2',
								'cylTopo1' => 'cylTopo2'
							),
							'AVE' => array(
								'aveAuto' => '',
								'aveIOL' => '',
								'aveTopo' => ''
							)
						);
						
						if(count($kValArr) > 0){
							foreach($kValArr as $kKey => &$kVal){
								$fieldStr = '';
								if(is_array($kVal) && count($kVal) > 0){
									foreach($kVal as $kEY => &$testVals){
										$testFirstVal = (isset($testValues['KVALUESARR'][$kEY]) && empty($testValues['KVALUESARR'][$kEY]) == false) ? $testValues['KVALUESARR'][$kEY] : '';
										$testSecVal = (isset($testValues['KVALUESARR'][$testVals]) && empty($testValues['KVALUESARR'][$testVals]) == false) ? $testValues['KVALUESARR'][$testVals] : '';
										
										$valStr = '';
										if(empty($testFirstVal) == false && empty($testSecVal) == false){
											if(strtolower($kKey) == 'cyl'){
												$valStr = $testFirstVal.' @ '.$testSecVal;
											}elseif(strtolower($kKey) == 'k2' || strtolower($kKey) == 'k1'){
												$valStr = $testFirstVal.' X '.$testSecVal;
											}else{
												$valStr = $testFirstVal;
											}
											$fieldStr .= '<td class="bordered font_12" style="width:30%; height:20px; text-align:center"  valign="top">'.$valStr.'</td>';
										}else{
											$fieldStr .= '<td class="bordered font_12" style="width:30%; height:20px; text-align:center"  valign="top"></td>';
										}
									}
								}
								if(empty($fieldStr) == false) $fieldStr = '<tr><td class="bordered font_12" style="width:10%; height:20px;"  valign="top"><b>'.$kKey.'</b></td>'.$fieldStr.'</tr>';
								if(empty($fieldStr) == false) $kvalStr .= $fieldStr;
							}
						}
					}
					$replaceArr['{KVALUES}'] = (empty($kvalStr) == false) ? $kvalStr : '';
					
					//Replacing Lens Heading Values
					$primarySelID = '';
					if(isset($testValues['LENSVALUES']) && is_array($testValues['LENSVALUES'])){
						$phyIDD = '';
						
						if(isset($testValues['LENSVALUES']['PrimaryLensID']) && empty($testValues['LENSVALUES']['PrimaryLensID']) == false) $primarySelID = $testValues['LENSVALUES']['PrimaryLensID'];
						if(strtoupper($eyeSite) == 'OD') $phyIDD = (isset($phyArr[$testValues['LENSVALUES']['performedByOD']]) && empty($phyArr[$testValues['LENSVALUES']['performedByOD']]) == false) ? $phyArr[$testValues['LENSVALUES']['performedByOD']] : '';
						if(strtoupper($eyeSite) == 'OS') $phyIDD = (isset($phyArr[$testValues['LENSVALUES']['performedByOS']]) && empty($phyArr[$testValues['LENSVALUES']['performedByOS']]) == false) ? $phyArr[$testValues['LENSVALUES']['performedByOS']] : '';
						
						//Headings
						$replaceArr['{LENSHEADING1}'] = (isset($functionNameArr[$testValues['LENSVALUES']['LENSHEAD1']]) && empty($functionNameArr[$testValues['LENSVALUES']['LENSHEAD1']]) == false) ? $functionNameArr[$testValues['LENSVALUES']['LENSHEAD1']] : '';
						$replaceArr['{LENSHEADING2}'] = (isset($functionNameArr[$testValues['LENSVALUES']['LENSHEAD2']]) && empty($functionNameArr[$testValues['LENSVALUES']['LENSHEAD2']]) == false) ? $functionNameArr[$testValues['LENSVALUES']['LENSHEAD2']] : '';
						$replaceArr['{LENSHEADING3}'] = (isset($functionNameArr[$testValues['LENSVALUES']['LENSHEAD3']]) && empty($functionNameArr[$testValues['LENSVALUES']['LENSHEAD3']]) == false) ? $functionNameArr[$testValues['LENSVALUES']['LENSHEAD3']] : '';
						$replaceArr['{LENSHEADING4}'] = (isset($functionNameArr[$testValues['LENSVALUES']['LENSHEAD4']]) && empty($functionNameArr[$testValues['LENSVALUES']['LENSHEAD4']]) == false) ? $functionNameArr[$testValues['LENSVALUES']['LENSHEAD4']] : '';
						$replaceArr['{LENSPERFORMED}'] = (empty($phyIDD) == false) ? $phyIDD : '';
						
						$replaceArr['{COMMENTS}'] = (isset($functionNameArr[$testValues['LENSVALUES']['Comments']]) && empty($functionNameArr[$testValues['LENSVALUES']['Comments']]) == false) ? $functionNameArr[$testValues['LENSVALUES']['Comments']] : '';
					}
					
					//Lens Rows
					if(isset($testValues['LENSVALUEARR']) && is_array($testValues['LENSVALUEARR'])){
						$testLensVal = &$testValues['LENSVALUEARR'];
						$staticLensArr = array('iol','iolPower','iolHollDay','iolSrk','iolHoffer');
						$lensRepStr = '';
						
						for($i=1;$i<=4;$i++){
							$background = '#fff';
							$txtColor = '#000';
							
							if(empty($primarySelID) == false && $primarySelID == $testLensVal['iol'.$i]){
								$background = '#0000ff';
								$txtColor = '#fff';
							}
							
							$iolLens = $this->iol_lenses[$testLensVal['iol'.$i]]['lensType'];
							$iolPower = $testLensVal['iolPower'.$i];
							$iolHollDay = $testLensVal['iolHollDay'.$i];
							$iolSrk = $testLensVal['iolSrk'.$i];
							$iolHoffer = $testLensVal['iolHoffer'.$i];
							
							$lensRepStr .= '
								<tr>
									<td class="bordered font_12" style="width:20%; height:20px;background-color:'.$background.';color:'.$txtColor.'"  valign="top">'.$iolLens.'</td>
									<td class="bordered font_12" style="width:20%; height:20px;background-color:'.$background.';color:'.$txtColor.'"  valign="top" align="center">'.$iolPower.'</td>
									<td class="bordered font_12" style="width:20%; height:20px;background-color:'.$background.';color:'.$txtColor.'"  valign="top" align="center">'.$iolHollDay.'</td>
									<td class="bordered font_12" style="width:20%; height:20px;background-color:'.$background.';color:'.$txtColor.'"  valign="top" align="center">'.$iolSrk.'</td>
									<td class="bordered font_12" style="width:20%; height:20px;background-color:'.$background.';color:'.$txtColor.'"  valign="top" align="center">'.$iolHoffer.'</td>
								</tr>';
						}
						
						$replaceArr['{IOLLENSROW}'] = (empty($lensRepStr) == false) ? $lensRepStr : '';
					}
					
					$replace = array_keys($replaceArr);
					$replaceWith = array_values($replaceArr);
					
					$htmlData = str_replace($replace,$replaceWith,$htmlData);
					
					if(empty($htmlData) == false) $finalData = '<page>'.$htmlData.'</page>';
					
					if(empty($finalData) === false) $returnArr[] = $finalData;
				}
			}
			
			return $returnArr;
		}
		
		public function getKHeadingName($ID){
			$getKreadingIdStr = "SELECT kheadingName FROM kheadingnames WHERE kheadingId = '$ID'";
			$getKreadingIdQry = imw_query($getKreadingIdStr);
			$getKreadingIdRow = imw_fetch_array($getKreadingIdQry);		
				if(strpos($getKreadingIdRow['kheadingName'], "K[")===false){$getKreadingIdRow['kheadingName'] = 'K['.$getKreadingIdRow['kheadingName'];}
				if(strpos($getKreadingIdRow['kheadingName'], "]")===false){$getKreadingIdRow['kheadingName'] = $getKreadingIdRow['kheadingName']."]";}		
				$kReadingHeadingName = $getKreadingIdRow['kheadingName'];		
			return $kReadingHeadingName;
		}
		
		public function getFormulaHeadArr(){
			$returnArr = array();
			$getFormulaheadingsStr = "SELECT formula_id, formula_heading_name FROM formulaheadings";
			$getFormulaheadingsQry = imw_query($getFormulaheadingsStr);
			if($getFormulaheadingsQry && imw_num_rows($getFormulaheadingsQry) > 0){
				while($rowFetch = imw_fetch_assoc($getFormulaheadingsQry)){
					$returnArr[$rowFetch['formula_id']] = $rowFetch['formula_heading_name'];
				}
			}
			return $returnArr;
		}
		
		//Returns patient's last SLE lens summary
		public function getLensSLE($patientID = '', $selEye = '', $formId = ''){
			$returnVal = false;
			if(empty($patientID) == true || empty($selEye) == true) return $returnVal;
			
			//Pt. DOS Array
			$ptDosArr = ($formId && (int)$formId > 0) ? array($formId) : $this->getPtDOS($patientID);
			if(count($ptDosArr) > 0){
				foreach($ptDosArr as $formId){
					//Get Patient SLE Summary Details
					$chkQry = imw_query("SELECT lens_".strtolower($selEye)."_summary as lensSum FROM chart_lens WHERE patient_id = ".$patientID." AND form_id = ".$formId." " );
					
					if($chkQry && imw_num_rows($chkQry) > 0){
						$rowFetch = imw_fetch_assoc($chkQry);
						$returnVal = $rowFetch['lensSum'];
					}
					
					if(empty($returnVal) == false) break;
				}
			}
			
			return $returnVal;
		}
		
		//Returns Patient's last Pupil dilated values
		public function getPupDilated($patientID = '', $selEye = '', $formId = ''){
			$returnVal = false;
			if(empty($patientID) == true || empty($selEye) == true) return $returnVal;
			
			//Pt. DOS Array
			$ptDosArr = ($formId && (int)$formId > 0) ? array($formId) : $this->getPtDOS($patientID);
			if(count($ptDosArr) > 0){
				foreach($ptDosArr as $formId){
					//Get Patient SLE Summary Details
					$chkQry = imw_query("SELECT sum".ucfirst(strtolower($selEye))."pupil as pupilDilated FROM chart_pupil WHERE patientId = ".$patientID." AND formId = ".$formId." " );
					
					if($chkQry && imw_num_rows($chkQry) > 0){
						$rowFetch = imw_fetch_assoc($chkQry);
						$returnVal = $rowFetch['pupilDilated'];
					}
					
					if(empty($returnVal) == false) break;
				}
			}
			
			return $returnVal;
		}
	} 
?>