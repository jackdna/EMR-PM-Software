<?php
class Procedures extends ChartNote {
	public function __construct($pid, $fid){
		parent::__construct($pid,$fid);
	}

	function proc_note_getEditCss($str_elm){
		$ret = "";

		$var_prev_elm = $str_elm."_prev";
		global $$str_elm, $$var_prev_elm;
		$elem_BP = $$str_elm;
		if(isset($$var_prev_elm)) { $elem_BP_prev = $$var_prev_elm;}

		//echo " <br/> ".$elem_BP." - ".$elem_BP_prev;

		//*
		if(isset($elem_BP_prev) && $elem_BP!=$elem_BP_prev){

			$ret = proc_note_getEditCss_p2($elem_BP, $elem_BP_prev);

		}
		return $ret;
		//*/
	}

	function proc_note_getEditCss_p2($elem_BP, $elem_BP_prev){
		$ret = "";
		if(!empty($elem_BP) && !empty($elem_BP_prev)){
			$ret = "prev_edit";
		}else if(!empty($elem_BP) && empty($elem_BP_prev)){
			$ret = "prev_add";
		}else if(empty($elem_BP) && !empty($elem_BP_prev)){
			$ret = "prv_del";
		}
		return $ret;
	}

	static function isProcedureNoteFinalized($chart_procedure_id){
		$sql = "SELECT count(*) AS num FROM chart_procedures WHERE id='".$chart_procedure_id."' AND finalized_status='1' ";
		$row = sqlQuery($sql);
		if($row!=false && $row["num"]>0){
			return true;
		}else{
			//old logic
			$sql ="SELECT count(*) AS num FROM chart_procedures c1
					LEFT JOIN chart_master_table c2 ON c1.form_id = c2.id
					WHERE c1.id='".$chart_procedure_id."' AND c2.finalize = '1' AND c1.form_id!='0'  ";
			$row = sqlQuery($sql);
			if($row!=false && $row["num"]>0){
				return true;
			}
		}
		return false;
	}

	function getTabsDosProcs( $elem_chart_procedures_id){

		$patient_id=$this->pid;
		$form_id=$this->fid;

		$lm=8; $opts="";

		$ret="";
		$sql="SELECT id, exam_date FROM chart_procedures
				WHERE patient_id = '".$patient_id."' AND proc_note_masterId='0' AND deleted_by='0'
				ORDER BY exam_date DESC, id DESC "; //AND form_id = '".$form_id."'
		$rez = sqlStatement($sql);
		for($i=1;$row=sqlFetchArray($rez);$i++){

			//$dt = FormatDate_show($row["exam_date"],1,2);
			$dt = wv_formatDate(date('Y-m-d',strtotime($row["exam_date"])))." ".date('H:i',strtotime($row["exam_date"]));
			$id = $row["id"];

			$sel = (!empty($elem_chart_procedures_id) && $elem_chart_procedures_id == $id) ? "active" : "";

			if($lm>$i){
				$ret.="<li class=\"".$sel."\"><a class=\"btn btn-default newProcedure tabproc \" href=\"javascript:void();\" onclick=\"showProcedure('".$id."')\">".$dt."</a></li>";
			}else{
				$sel = ( $sel=="active" ) ? "SELECTED" : "";
				$opts.="<option value=\"".$id."\" ".$sel." >".$dt."</option>";
			}
		}

		//
		if(!empty($opts)){
			$ret.="<li ><select class=\"form-control\" onchange=\"showProcedure(this.value)\"><option value=\"\">And More..</option>".$opts."</select></li>";
		}

		//
		//if(!empty($elem_chart_procedures_id)){
			$sel = empty($elem_chart_procedures_id) ? "active" : "";
			$ret="<li class=\"".$sel."\"><a class=\"btn btn-default tabproc newProcedure \" href=\"javascript:void();\" onclick=\"showProcedure('New')\">New Procedure</a></li>".$ret;
		//}

		$ret = "<div class='pro_list'><ul style='margin:0px; padding:0px;' class=\"nav-pills nav-justified\">".$ret."</ul></div>";

		return $ret;

	}



	function getOpNoteData($elem_tempId, $elem_site="", $chart_procedures_id=0,$dx_code,$Laser_Proc_Notes){

		//global $patient_id, $formId;
		$patient_id = $this->pid;
		$formId = $this->fid;

		$OBJsmart_tags = new SmartTags();
		$oPnTmp = new PnTemplate();
		$objParser = new PnTempParser();
		$oPnRep = new PnReports();

		$elem_pnData=$pn_rep_id="";
		if(!empty($chart_procedures_id)){

			$arrTemp = $oPnRep->getChartProcEditId($chart_procedures_id, $elem_tempId);
			$elem_pnData = $arrTemp["txt_data"];	//Template data
			$pn_rep_id = $arrTemp["pn_rep_id"];
			$elem_pnData = $objParser->getDataParsed($elem_pnData,$patient_id,$formId,'','','','','','','procedures');
			$elem_pnData = str_ireplace('&Acirc;','',$elem_pnData);
		}

		if(empty($pn_rep_id)){
			//Template Id & Name -----------
			if(isset($elem_tempId) && !empty($elem_tempId)){
				$tempId = $elem_tempId;
				$arrTemp = $oPnTmp->getRecordInfo($tempId);
				$templateName = $arrTemp[1]; //Template Name
				$elem_pnData = $arrTemp[2]; //Template data

				//$elem_pnData = str_ireplace("{PHYSICIAN SIGNATURE}","<div title='replacePhySig'></div>",$elem_pnData);

				/*--REPLACING SMART TAGS (IF FOUND) WITH LINKS--*/
				$arr_smartTags = $OBJsmart_tags->get_smartTags_array();
				if($arr_smartTags){
					foreach($arr_smartTags as $key=>$val){
						$smart_tag_parsing = $OBJsmart_tags->get_smartTags_array($key);
						$parsed = '';
						if($smart_tag_parsing && count($smart_tag_parsing)>0){
							foreach ($smart_tag_parsing as $value) {
								$parsed .= '|'.$value;
							}
						}
						$elem_pnData = str_ireplace("[".$val."]",'<a id="'.$parsed.'" act_id="'.$key.'" href="javascript:;" class="cls_smart_tags_link">'.$val.'</a>',$elem_pnData);
					}
				}
				/*--SMART TAG REPLACEMENT END--*/
				if($elem_site=='OU'){ $elem_site = 'both eyes'; }elseif($elem_site=='OD'){ $elem_site = 'right eye'; }elseif($elem_site=='OS'){ $elem_site = 'left eye'; }
				//if($elem_site){
					$elem_pnData = str_ireplace("{SITE}",$elem_site,$elem_pnData);
					$elem_pnData = str_ireplace("{DX_CODE}",$dx_code,$elem_pnData);
					$elem_pnData = str_ireplace("{LASER_PROC_NOTES}",$Laser_Proc_Notes,$elem_pnData);
				//}
				$elem_pnData = $objParser->getDataParsed($elem_pnData,$patient_id,$formId,'','','','','','','procedures');
				$elem_pnData = str_ireplace("+ve",'Positive',$elem_pnData);
				$elem_pnData = str_ireplace("-ve",'Negative',$elem_pnData);

			}
		}

		echo $elem_pnData;
		//Template Id & Name ------------

	}

	function getPnTemplate($tempId=""){
		$arr_opnotes=array();
		//Get Select of template --------
		$oPnTmp = new PnTemplate;
		$arrTmp = $oPnTmp->getProgNtsInfo();
		$strSelect = "<select name=\"elem_OpTempId\" id=\"elem_OpTempId\"  onchange=\"loadOpTemp(this)\" class=\"form-control minimal\">".
					 "<option value=\"0\"></option>";
		if(count($arrTmp) > 0){
			foreach($arrTmp as $key => $val){

				$tId = $val["id"];
				$tNm = $val["name"];
				$sel = ($tempId == $tId ) ? "selected" : "" ;
				$arr_opnotes[$tId]=$tNm;
				$strSelect .= "<option value=\"".$tId."\" ".$sel.">".$tNm."</option>";
			}
		}

		$strSelect .= "</select>";
		//Get Select of template --------

		return array("0"=>$strSelect,"1"=>$arr_opnotes);
	}



	function getOldBottox(){
		$pid=$this->pid;
		$str = "";
		$sql = "select c1.id, DATE_FORMAT(c1.exam_date, '%m-%d-%Y') as exam_date
				from chart_procedures c1
				INNER JOIN chart_procedures_botox c2 ON c1.id = c2.chart_proc_id ".
				//"INNER JOIN chart_master_table c3 ON c1.form_id = c3.id". //DATE_FORMAT(c3.date_of_service, '%m-%d-%Y') as date_of_service
				" WHERE c1.patient_id='".$pid."' AND deleted_by = '0'
				ORDER BY c1.exam_date DESC
				";
		$rez = sqlStatement($sql);
		for($i=1;$row=sqlFetchArray($rez);$i++){
			$tmp = "";
			//if(empty($tmp) && !empty($row["date_of_service"])){	$tmp = $row["date_of_service"];		}
			if(empty($tmp) && !empty($row["exam_date"])){	$tmp = $row["exam_date"];		}
			$str .= "<tr><td>".$i.".</td><td><a href='javascript:void(0);' onclick=\"botox_insertOldBtxVal('".$row["id"]."')\" title=\"Click to change botox values\" >".$tmp."</td></tr>";
		}

		//
		if(!empty($str)){
			$str = "<div><b>Previous Botox</b></div>
				  <table class=\"table table-responsive\">
					<tr><td><b>S.No.</b></td><td  ><b>DOS</b></td></tr>
					".$str."
				  </table>";
		}

		return $str;
	}

	function get_chart_tech(){
		$ret=0;
		$formId=$this->fid;
		if(!empty($formId)){
			$sql = "SELECT create_by, provIds FROM chart_master_table where id = '".$formId."' ";
			$row = sqlQuery($sql);
			if($row!=false){
				$provIds = $row["create_by"].",".$row["provIds"];
				$provIds=trim($provIds);
				$provIds=trim($provIds,",");
				$provIds = str_replace(" ","",$provIds);

				$artmp = explode(",",$provIds);
				$t = array_filter($artmp);
				$tmp = implode(",",$t);
				$sql = "SELECT id FROM users where id IN (".$tmp.") AND user_type IN (".implode(",", $GLOBALS['arrValidCNTech']).") AND delete_status=0 ";
				$row = sqlQuery($sql);
				if($row!=false){
					$ret = $row["id"];
				}
			}
		}
		return $ret;
	}

	function pt_occu_hoby($op, $oc="", $hob=""){
		if(!empty($this->pid)){
			if($op=="get"){
				$sql = "SELECT occupation, hobbies FROM patient_data where id = '".$this->pid."' ";
				$row = sqlQuery($sql);
				if($row!=false){
					$oc = $row["occupation"];
					$hob = $row["hobbies"];
				}
				return array($oc, $hob);
			}else if($op=="set"){
				$sql = "UPDATE patient_data SET occupation='".sqlEscStr($oc)."', hobbies='".sqlEscStr($hob)."' where id = '".$this->pid."'  ";
				$row = sqlQuery($sql);
			}
		}
	}

	function get_proc_amend($id){
		$ar=array();
		$sql = "SELECT amndmnt as elem_amndmnt,
				sign as hid_pa_sign, sign_on as hid_sign_on, sign_by as el_sign_by,
				final as el_amnd_final, final_by as el_final_by, final_on as el_final_on, op_id as proc_amnd_op_id
			FROM proc_amendments where id_chart_procedures = '".$id."' ";

		$row = sqlQuery($sql);
		return $row;
	}

	function autoFinalizeProcs(){
		$sql = "SELECT id, exam_date, user_id, patient_id FROM chart_procedures WHERE patient_id='".$this->pid."' AND deleted_by='0' AND finalized_status='0' ORDER BY id ";
		$rez=sqlStatement($sql);
		$num = imw_num_rows($rez);
		if($num>0){

			//Get Facility Timers
			$oFacility = new Facility("HQ");
			$arrChartTimer = $oFacility->getChartTimers();

			for($i=0; $row=sqlFetchArray($rez);$i++){

				$id=$row["id"];
				$proId=$row["user_id"];
				$dos=$row["exam_date"];
				$pid=$row["patient_id"];
				$strFinal = ($arrChartTimer["finalize"] * 24)." hours ";

				//Check if need to finalize
				if(isDtPassed($dos, $strFinal)){
					if(!empty($proId)){
						$ousr= new User($proId);
						$uTpe = $ousr->getUType(1);
					}

					if(empty($proId)||$uTpe!=1){ //Empty Or No Physician
						$opt = new Patient($pid);
						if(empty($proId)) $proId="-1";//"987654321"; //Not Exist static id given to pass user checks
						$proIdDefault=$opt->getPro4CnIn($proId,"",$dos);
					}else{
						$proIdDefault=$proId;
					}

					if(!empty($proIdDefault)){
						$sql = "UPDATE chart_procedures SET finalized_status='1', auto_final='1', Finalized_by='".$proIdDefault."', Finalized_date='".wv_dt("now")."'
								WHERE id = '".$id."'   ";
						sqlQuery($sql);
					}
				}
			}
		}
	}

	public function load_exam($finalize_flag){

		//Patient Id
		$patient_id = $this->pid;

		//Todate
		$toDate = date("Y-m-d H:i:s");
		$formId=$this->fid;

		//obj
		$oAdmn=new Admn();
		$oPt = new Patient($patient_id);
		$oMedHx = new MedHx($patient_id);
		$oDx = new Dx();
		$oMod = new Modifier();
		$oCPT = new CPT();
		$ouser = new User();

		//Is Reviewable
		$isReviewable = $this->isChartReviewable($_SESSION["authId"]);

		//
		$elem_per_vo =  ChartPtLock::is_viewonly_permission();
		$ProClr = User::getProviderColors();//
		//$logged_user_type = $_SESSION["logged_user_type"];
		$logged_user_type = (isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"])) ? $_SESSION["user_role"] : $_SESSION["logged_user_type"];

		$flgStopExecProc = 0;
		$elem_bottox_open_flg=0;

		//
		if(!empty($_REQUEST['med_upc'])){
			$sql 	= "SELECT medicine_name FROM `medicine_data` WHERE opt_med_upc ='".sqlEscStr($_REQUEST['med_upc'])."'";
			$row	=sqlQuery($sql);
			if($row!=false){
				echo 	$row["medicine_name"];
			}
			exit();
		}

		//get Botox values
		if(!empty($_GET["btxid"])){
			$arr=array();
			$sql = "SELECT * FROM chart_procedures_botox WHERE chart_proc_id = '".$_GET["btxid"]."' ";
			$row=sqlQuery($sql);
			if($row!=false){
				$arr=$row;
			}
			echo json_encode($arr);
			exit();
		}
		//--

		//--
		if(!empty($_GET["getProceduresInfo"])){
			$sql="Select * from operative_procedures WHERE procedure_id='".$_GET["getProceduresInfo"]."' LIMIT 1";
			$row=sqlQuery($sql);
			$arr_cpt_mod=$arr_cpt_mod_pr=array();$consent_signed=false;
			if($row!=false){
				$arr_cpt_mod=unserialize(html_entity_decode($row['cpt_code']));

				list($dos_m,$dos_d,$dos_y)=explode('-',$_SESSION['DOS_FOR_CL']);
				$form_dos=$dos_y.'-'.$dos_m.'-'.$dos_d;

				$isConSigQ=imw_query("select form_information_id from patient_consent_form_information where consent_form_id='".$row['consent_form_id']."' and patient_id='$patient_id' and DATE_FORMAT(form_created_date,'%Y-%m-%d')='$form_dos'")or die(imw_error());
				if(imw_num_rows($isConSigQ)>=1)
				{
					$consent_signed=true;
				}
				//$isConSigQ.close;
				$row['consent_signed']=$consent_signed;
				if( !is_array($arr_cpt_mod) && !empty($row['cpt_code']) && strlen($row['cpt_code'])<=6 ){ // when there is only cptcode: old data

					//$row['cpt_code']
					$tmp_cpt = $row['cpt_code'];
					$tmp = $oCPT->get_cpt_code_arr($tmp_cpt);
					if(!empty($tmp)){ $tmp_cpt .= " - ".$tmp; }
					$row['arr_cpt_mod']=implode("~~",array($tmp_cpt."||".''));
				}

                //Dx Codes Descriptions
                if(!empty($row['dx_code'])) {
                    $tmpdx = explode(';', $row['dx_code']);
		    $tmpdxId = explode(';', $row['dx_code_id']);
                    $dxcode_str = '';
                    foreach($tmpdx as $kdx => $dxc) {
                        $dxc = trim($dxc);
                        if(!empty($dxc)) {
				/*
			    $sql="Select CONCAT(icd10, ' - ',icd10_desc, ';') as dxcd from icd10_data WHERE icd10='".$dxc."' and deleted = '0'  ";
                            $dxc_row=sqlQuery($sql);
                            $dxcode_str .= $dxc_row['dxcd'];
			    */

			    $dxc_dsc = $oDx->get_dx_desc($dxc, "icd10_desc", $tmpdxId[$kdx]);
			    if(!empty($dxc_dsc)){
				$dxcode_str .= $dxc." - ".$dxc_dsc.";";
			    }


                        }
                    }
                    $row['dx_code'] = $dxcode_str;
                }

				if(count($arr_cpt_mod)>0){
					for($i=1;$i<=count($arr_cpt_mod);$i++){
						if($arr_cpt_mod['cpt_code1_'.$i]){
							$tmp_cpt = $arr_cpt_mod['cpt_code1_'.$i];
							$tmp = $oCPT->get_cpt_code_arr($tmp_cpt);
							if(!empty($tmp)){ $tmp_cpt .= " - ".$tmp; }
							$tmp_cpt=trim($tmp_cpt);
							$arr_cpt_mod_pr[]=$tmp_cpt."||".$arr_cpt_mod['mod_code1_'.$i];
						}
						if($arr_cpt_mod['cpt_code2_'.$i]){
							$tmp_cpt = $arr_cpt_mod['cpt_code2_'.$i];
							$tmp = $oCPT->get_cpt_code_arr($tmp_cpt);
							if(!empty($tmp)){ $tmp_cpt .= " - ".$tmp; }
							$tmp_cpt=trim($tmp_cpt);
							$arr_cpt_mod_pr[]=$tmp_cpt."||".$arr_cpt_mod['mod_code2_'.$i];
						}
						if($arr_cpt_mod['cpt_code3_'.$i]){
							$tmp_cpt = $arr_cpt_mod['cpt_code3_'.$i];
							$tmp = $oCPT->get_cpt_code_arr($tmp_cpt);
							if(!empty($tmp)){ $tmp_cpt .= " - ".$tmp; }
							$tmp_cpt=trim($tmp_cpt);
							$arr_cpt_mod_pr[]=$tmp_cpt."||".$arr_cpt_mod['mod_code3_'.$i];
						}
					}
					if(count($arr_cpt_mod_pr)>0){
						$row['arr_cpt_mod']=implode("~~",$arr_cpt_mod_pr);
					}
				}

				//GET TYPEAHEAD OF CPT AND MODIFIER
				$row['arr_admin_cpt_code']=$cptCodeArr;
				$row['arr_admin_mod_code']=$modCodeArr;


				print json_encode($row);
			}else{
				print array();
			}
			//exit();
			$flgStopExecProc = 1;
		}


		//--

		//--

		if(!empty($_GET["elem_formAction"]) && $_GET["elem_formAction"] == "meds" ){
			$ar=array();

			$tmp = $_GET["term"];
			$sql = "SELECT medicine_name from medicine_data WHERE medicine_name LIKE '".$tmp."%' AND del_status = '0' ORDER BY medicine_name";
			$rez=sqlStatement($sql);
			for($i=1;$row=sqlFetchArray($rez);$i++){
				$ar[] = $row["medicine_name"];
			}

			echo json_encode($ar);
			//exit();
			$flgStopExecProc = 1;
		}

		//--

		//--

		if(!empty($_GET["elem_formAction"]) && $_GET["elem_formAction"] == "provider" ){
			$ar=array();

			$tmp = $_GET["term"];
			$sql = "SELECT fname, lname, mname, id, pro_title from users
					WHERE (fname LIKE '".$tmp."%' OR mname LIKE '".$tmp."%' OR  lname LIKE '".$tmp."%')
					AND delete_status!='1'
					AND locked!='1'
					ORDER BY fname, mname, lname ";
			$rez=sqlStatement($sql);
			for($i=1;$row=sqlFetchArray($rez);$i++){
				$userName="";
				$userName = $row["pro_title"]." ".$row["fname"]." ".substr($row["mname"],0,1)." ".$row["lname"];//." - ".$row["id"]
				$userName = trim($userName);
				if(!empty($userName)){
					$ar[] = $userName;
				}
			}

			echo json_encode($ar);
			//exit();
			$flgStopExecProc = 1;
		}

		//--

		if(isset($_GET["elem_opNoteId"]) && !empty($_GET["elem_opNoteId"]) ){

			$elem_site="";
			$dxCodeVal="";
			if(!empty($_GET["elem_opNoteEye"])){ $elem_site=$_GET["elem_opNoteEye"];  }
			if(!empty($_GET["elem_opNotedxCode"])){ $dxCodeVal=$_GET["elem_opNotedxCode"];  }	//MULTISELECT DX CODES CHECKBOX VALUE FOR DX CODE VARIABLE REPLACEMENT
			//LASER PROCEDURE NOTES VARIABLES REPLACEMENT
			$laserProcedureNote="";
			if(!empty($_GET["elem_spotDuration"])){ $laserProcedureNote.='Spot Duration: '.$_GET["elem_spotDuration"].',';  }
			if(!empty($_GET["elem_spotSize"])){ $laserProcedureNote.=' Spot Size: '.$_GET["elem_spotSize"].',';  }
			if(!empty($_GET["elem_power"])){ $laserProcedureNote.=' Power: '.$_GET["elem_power"].',';  }
			if(!empty($_GET["elem_shots"])){ $laserProcedureNote.=' # of Shots: '.$_GET["elem_shots"].',';  }
			if(!empty($_GET["elem_total_energy"])){ $laserProcedureNote.=' Total Energy : '.$_GET["elem_total_energy"].',';  }
			if(!empty($_GET["elem_degree_of_opening"])){ $laserProcedureNote.=' Degree of Opening : '.$_GET["elem_degree_of_opening"].',';  }
			if(!empty($_GET["elem_exposure"])){ $laserProcedureNote.=' Exposure : '.$_GET["elem_exposure"].',';  }
			if(!empty($_GET["elem_count"])){ $laserProcedureNote.=' Count : '.$_GET["elem_count"];  }

			$this->getOpNoteData($_GET["elem_opNoteId"], $elem_site, $_GET["chart_procedures_id"],$dxCodeVal,$laserProcedureNote);
			//exit();
			$flgStopExecProc = 1;

		}

		//--

		if($flgStopExecProc == 0){	//flgStopExecProc

			$el_proc_perfmd_amnd="";
			$el_site_amnd="";
			$el_dt_proc_perfmd_amnd="";

			//AutoFinalize Procedures--
			$this->autoFinalizeProcs();

			//Chart Notes
			$DOS = wv_dt(); //$this->getDos();
			$DOS = wv_formatDate($DOS);
			$ptName_Id = $oPt->getName(31);
			$ptName_Id_2 = $oPt->getName();
			$cur_usr_type_cn = $ouser->getUType(1);
			$operator=$ouser->getName(8);
			//DOB - ".$dob.",
			if(preg_match('/DOB - \d{2}-\d{2}-\d{4},/',$ptName_Id,$mtch_dob)){
				$pt_dob_lasik = $mtch_dob[0];
				$pt_dob_lasik = str_replace(array("DOB -", ","),"",$pt_dob_lasik);
				$pt_dob_lasik =trim($pt_dob_lasik);
			}
			$ar_ptName_Id_2 = explode(" - ",$ptName_Id_2);
			$pt_name_lasik = $ar_ptName_Id_2[0];
			list($elem_pt_occu, $elem_pt_hobby) = $this->pt_occu_hoby("get");

			//Edit  Procedure Note ---

			$proc_id_admin="";
			if($_GET["chart_proc_id"] == "New"){
				$row=false;
			}else if(!empty($_GET["chart_proc_id"])){
				$sql="SELECT * FROM chart_procedures WHERE id = '".$_GET["chart_proc_id"]."' ";
				$row=sqlQuery($sql);
			}else{
				$sql="SELECT * FROM chart_procedures WHERE patient_id = '".$patient_id."' AND proc_note_masterId='0' AND deleted_by='0'  ORDER BY exam_date DESC, id DESC LIMIT 1 "; //AND form_id = '".$formId."'
				$row=sqlQuery($sql);
			}

			if($row!=false){
				//$proc_id_admin=
				$elem_chart_procedures_id=$row["id"];
				//$elem_patient_id=$row[""];
				//$elem_form_id=$row[""];

				$elem_encounter_id=$row["encounter_id"];
				$elem_BP=$row["bp"];
				$elem_heartattack=$row["heart_attack"];
				$elem_otherProcNote=$row["otherProcNote"];
				$elem_procedure=$row["proc_id"];
				$pt_site_lasik = $elem_site=$row["site"];
				$elem_lids_opts=$row["lids_opts"];
				$elem_cor_lids_opts=$row["cor_lids_opts"];
				$elem_dxCode=$row["dx_code"];
				$elem_cpt_code=$row["cpt_code"];
				$elem_cpt_mod=$row["cpt_mod"];
				$elem_startTime=$row["start_time"];
				$elem_endTime=$row["end_time"];
				$elem_iopType=$row["iop_type"];
				$elem_iopOd=$row["iop_od"];
				$elem_iopOs=$row["iop_os"];
				$elem_iopTime=$row["iop_time"];
				$elem_complication=$row["complication"];
				$elem_complication_detail=$row["complication_desc"];
				$elem_comments=$row["comments"];
				$laser_procedure_note=$row["laser_procedure_note"];
				$spot_duration=$row["spot_duration"];
				$spot_size=$row["spot_size"];
				$power=$row["power"];
				$shots=$row["shots"];
				$total_energy=$row["total_energy"];
				$degree_of_opening=$row["degree_of_opening"];
				$exposure=$row["exposure"];
				$count=$row["count"];


				list($elem_exam_date,$elem_exam_time)=explode(" ",$row["exam_date"]);

				$DOS = wv_formatDate($elem_exam_date); $elem_exam_time_cur = $elem_exam_time;

				if(!empty($row["pre_op_meds"])){	$arrPreOpMed = explode("|~|", $row["pre_op_meds"]); }else{ $arrPreOpMed = array();  }

				if(!empty($row["intravit_meds"])){	$arrIntraVitrealMeds = explode("|~|", $row["intravit_meds"]); }else{ $arrIntraVitrealMeds = array();  }

				if(!empty($row["post_op_meds"])){ $arrPostOpMeds = explode("|~|", $row["post_op_meds"]); }else{ $arrPostOpMeds = array();  }

				$elem_IntraVitrealMeds_Lot=$row["intravit_meds_lot"];
				$elem_PostOpMeds_Lot=$row["post_op_meds_lot"];
				$elem_timeout=$row["timeout"];
				$elem_corrctprocedure=$row["corr_proc_id"];
				$elem_corrctsite=$row["corr_site"];
				$elem_siteMarked=$row["site_marked"];
				$elem_positionProstheses=$row["pos_pros_implant"];
				$elem_consentCompletedSigned=$row["consent_completed"];
				$elem_providers=$row["providers"];
				$curConfrmId = $elem_consentForm=$row["consent_form_id"];
				$curOpId = $elem_OpTempId=$row["op_report_id"];
				//$hold_to_physician=$row[""];
				$hidd_hold_to_physician=$row["hidd_hold_to_physician"];
				$elem_cmt = $row["cmt"];
				$elem_finalized_status = $row["finalized_status"];
				$elem_auto_final = $row["auto_final"];

				$isConSigQ=imw_query("select form_information_id from patient_consent_form_information where consent_form_id='$curConfrmId' and patient_id='$patient_id'")or die(imw_error());// and DATE_FORMAT(form_created_date,'%Y-%m-%d')=''
				if(imw_num_rows($isConSigQ)>=1){$consent_signed_img="<img src='".$GLOBALS['webroot']."/library/images/flag_green.png' border='0' title='Saved'>";}
				else {$consent_signed_img="<img src='".$GLOBALS['webroot']."/library/images/flag_red.png' border='0' title='Not Saved'>";	}
				//$isConSigQ.close;

				//Botox-
				$sql = "SELECT * FROM chart_procedures_botox WHERE chart_proc_id = '".$elem_chart_procedures_id."' ";
				$row=sqlQuery($sql);
				if($row!=false){
					$elem_botox_total=$row["btx_total"];
					$elem_botox_used=$row["btx_usd"];
					$elem_botox_wasted=$row["btx_wstd"];
					$elem_botox_lot=$row["lot"];
					$elem_lot_expr_dt=(!empty($row["lot_expr_dt"]) && strpos($row["lot_expr_dt"],"0000")===false) ? wv_formatDate($row["lot_expr_dt"]) : "" ;

					$elem_botox_sc_od=$row["vis_sc_od"];
					$elem_botox_cc_od=$row["vis_cc_od"];
					$elem_botox_other_od=$row["vis_othr_od"];

					$elem_botox_sc_os=$row["vis_sc_os"];
					$elem_botox_cc_os=$row["vis_cc_os"];
					$elem_botox_other_os=$row["vis_othr_os"];

					$elem_botox_inject_radio=$row["rd_injctn"];

					$elem_cnvs_bottox_drw=$row["drw_path"];
					$elem_cnvs_bottox_drw_coords	=$row["drw_coords"];
					$elem_botox_rbdcs	=$row["rbdcs"];
					$elem_typeBtx=$row["type_btx"];
					$elem_bottox_open_flg=1;
					$elem_cnvs_bottox_drw_img=$row["drw_img"];
					$elem_cnvs_bottox_drw_img_dim=$row["drw_img_dim"];


				}

				//Botox-

				//LASIK --
				$sql = "SELECT * FROM chart_proc_lasik WHERE chart_proc_id = '".$elem_chart_procedures_id."' AND del_by='0' ";
				$row=sqlQuery($sql);
				if($row!=false){
					$id_chart_proc_lasik=$row["id"];
					$elem_lasik_open_flg="1";
					$el_pre_op_tech=$row["pre_op_tech"];

					$el_lasik_allergies=$row["allergies"];
					$el_near_eye=$row["near_eye"];
					$el_lasik_xanax=$row["xanax"];

					$pre_op_checks = $row["pre_op_checks"];

					/*
					$el_ins_punc_plg=$row[""];
					$el_monovis=$row[""];
					$el_bedatine=$row[""];
					$el_lasik_pachy=$row[""];
					$el_repeat_orbs=$row[""];
					$el_lasik_acular=$row[""];
					$el_pre_antibiotic=$row[""];
					$el_lasik_alcaine=$row[""];
					$el_consent_review=$row[""];
					*/

					$lasik_modifier = $row["lasik_modifier"];
					/*
					$el_lasik_modifier_lasik=$row[""];
					$el_lasik_modifier_prk=$row[""];
					$el_lasik_modifier_opti=$row[""];
					$el_lasik_modifier_topo_guided=$row[""];
					$el_lasik_modifier_enh=$row[""];
					$el_lasik_modifier_flap_lift=$row[""];
					*/

					$el_lasik_modifier_eye=$row["lasik_eye"];

					$el_dos_mr_od=$row["dos_mr_od"];
					$el_dos_mr_os=$row["dos_mr_os"];

					$post_op_target_od = $row["post_op_target_od"];
					if(!empty($post_op_target_od)){
						$ar_post_op_target_od	 =  explode("!,!",$post_op_target_od);
						$el_post_op_target_od_plano=$ar_post_op_target_od[1];
						$el_post_op_target_od=$ar_post_op_target_od[0];
					}

					$post_op_target_os = $row["post_op_target_os"];
					if(!empty($post_op_target_os)){
						$ar_post_op_target_os	 =  explode("!,!",$post_op_target_os);
						$el_post_op_target_os_plano=$ar_post_op_target_os[1];
						$el_post_op_target_os=$ar_post_op_target_os[0];
					}

					$el_avg_k_axis_od=$row["avg_k_axis_od"];
					$el_avg_k_axis_os=$row["avg_k_axis_os"];

					$el_treatment1_od=$row["treatment1_od"];
					$el_treatment1_os=$row["treatment1_os"];

					$el_lasik_pachy_od=$row["pachy_od"];
					$el_lasik_pachy_os=$row["pachy_os"];

					$el_flap_thick_od=$row["flap_thick_od"];
					$el_flap_thick_os=$row["flap_thick_os"];

					$el_stromal_bed_od=$row["stromal_bed_od"];
					$el_stromal_bed_os=$row["stromal_bed_os"];

					$keratome_od = $row["keratome_od"];
					$keratome_os = $row["keratome_os"];

					$ar_keratome_od = explode("!,!",$keratome_od);
					$ar_keratome_os = explode("!,!",$keratome_os);

					$el_hansatome_od=$ar_keratome_od[0];
					$el_hansatome_os=$ar_keratome_os[0];

					$el_keratome_ring_od=$ar_keratome_od[1];
					$el_keratome_plate_od=$ar_keratome_os[1];

					$el_keratome_ring_os=$ar_keratome_od[2];
					$el_keratome_plate_os=$ar_keratome_os[2];

					$el_risks_benefits=$row["risks_benefits"];

					/*
					$el_surgeon_sign_id=$row["surgeon_sign_id"];
					$el_surgeon_sign_id_name="";
					if(!empty($el_surgeon_sign_id)){
						$oUsr = new User($el_surgeon_sign_id);
						$el_surgeon_sign_id_name = $oUsr->getName(1);
					}
					*/

					$el_surgeon_sign_dos=wv_formatDate($row["surgeon_sign_dos"]);
					if(strpos($el_surgeon_sign_dos,"0000")!==false){ $el_surgeon_sign_dos=""; }
					$el_surgeon_sign = $row["surgeon_sign"];

					$el_abrasion=$row["abrasion"];
					$el_bcl=$row["bcl"];
					$el_post_op_type=$row["post_op_type"];

					$drops = $row["drops"];
					/*
					$el_zymaxid=$row[""];
					$el_pred_forte=$row[""];
					$el_postop_lasik_acular=$row[""];
					$el_lasik_omnipred=$row[""];
					*/
					$el_temperature=$row["temperature"];
					$el_humidity=$row["humidity"];

					$el_post_op_surgeon=$row["post_op_surgeon"];
					$el_laser_oprtr=$row["laser_operator"];
					$el_keratome_tech=$row["keratome_tech"];

					$el_cornea_check_od_Ex=$row["cornea_check_od"];
					$el_cornea_check_os_Ex=$row["cornea_check_os"];

					$el_plugs_inserted=$row["plugs_inserted"];

					$el_plugs_inserted_eye=$row["plugs_inserted_eye"];
					$el_plugs_inserted_size=$row["plugs_inserted_size"];

					$el_post_op_kit_given=$row["post_op_kit_given"];
					$el_post_op_tech=$row["post_op_tech"];

					$el_lasik_comments=$row["comments"];


					$sql = "SELECT * FROM chart_lasik_pt_verify WHERE id_chart_proc_lasik='".$id_chart_proc_lasik."' ";
					$rez = sqlStatement($sql);
					for($i=0; $row=sqlFetchArray($rez);$i++){
						$titem = $row["item"];
						$tsurgeon = $row["surgeon"];
						$ttech = $row["tech"];
						$tveri_time = $row["veri_time"];

						if($titem=="Name"){
							$el_vpv_name=$titem;
							$el_vpv_name_srg=$tsurgeon;
							$el_vpv_name_tech=$ttech;
							$el_vpv_name_time=$tveri_time;
						}else if($titem=="DOB"){
							$el_vpv_dob=$titem;
							$el_vpv_dob_srg=$tsurgeon;
							$el_vpv_dob_tech=$ttech;
							$el_vpv_dob_time=$tveri_time;
						}else if($titem=="Procedure Site"){
							$el_vpv_proc_site=$titem;
							$el_vpv_proc_site_srg=$tsurgeon;
							$el_vpv_proc_site_tech=$ttech;
							$el_vpv_proc_site_time=$tveri_time;
						}else if($titem=="Mono" || $titem=="DVO"){
							$el_vpv_proc_type=$titem;
							$el_vpv_proc_type_srg=$tsurgeon;
							$el_vpv_proc_type_tech=$ttech;
							$el_vpv_proc_type_time=$tveri_time;
						}
					}
				}

				//LASIK --

				//get consent_form_id -
				$proc_con_frm_id = $this->get_proc_con_frm($elem_chart_procedures_id);

				//get op_report_id -
				$proc_pn_rep_Id = $this->get_proc_pn_rep($elem_chart_procedures_id);

			}

			//finalize then check premission
			$priv_proc_amend=0;
			$str_sign_on_by = $hid_pa_sign_img="";
			$elem_amndmnt_readonly="";

			if(!empty($elem_finalized_status)){
				//Amendments -----
				$ar_proc_amnd = $this->get_proc_amend($elem_chart_procedures_id);
				extract($ar_proc_amnd);
				if(empty($el_sign_by)){
					$el_sign_by = $_SESSION["authId"];
				}else	if(!empty($hid_pa_sign) && !empty($el_sign_by)){
					$oSaveFile = new SaveFile($el_sign_by,1);
					$hid_pa_sign_path = $oSaveFile->getFilePath($hid_pa_sign,"w");
					if(!empty($hid_pa_sign_path)){
						$hid_pa_sign_img="<img src=\"".$hid_pa_sign_path."\" alt=\"sign\">";
						$str_sign_on_by.="Sign on ".wv_formatDate($hid_sign_on, 0, 1);
						$oufin = new User($el_sign_by);
						$el_sign_by_str=$oufin->getName(3);
						if(!empty($el_sign_by_str)){ $str_sign_on_by.=" by ".$el_sign_by_str; }
					}
				}
				if(!empty($el_final_by)){
					$elem_amndmnt_readonly="readonly";
					$oufin = new User($el_final_by);
					$el_final_by_str=$oufin->getName(3);
					$str_sign_on_by.="<br/>Finalize on ".wv_formatDate($el_final_on, 0, 1);
					if(!empty($el_final_by_str)){ $str_sign_on_by.=" by ".$el_final_by_str; }
				}

				if(core_check_privilege(array("priv_proc_amend")) == true){
					$priv_proc_amend="1";
				}else if(!empty($proc_amnd_op_id)){
					$priv_proc_amend="1";
					$elem_per_vo = 1;
				}

				//Cur User if not Phy then no finalize
				if($cur_usr_type_cn!=1){
					$elem_per_no_final = "1";
				}
			}

			//LASIK --
			//
			if($cur_usr_type_cn==1){ //if phy
				if(empty($el_post_op_surgeon)){$el_post_op_surgeon = $_SESSION["authId"];}
				if(empty($el_vpv_name_srg)){$el_vpv_name_srg = $_SESSION["authId"];}
				if(empty($el_vpv_dob_srg)){$el_vpv_dob_srg = $_SESSION["authId"];}
				if(empty($el_vpv_proc_site_srg)){$el_vpv_proc_site_srg = $_SESSION["authId"];}
				if(empty($el_vpv_proc_type_srg)){$el_vpv_proc_type_srg = $_SESSION["authId"];}
				$el_def_tech_lasik = $this->get_chart_tech();

			}else if($cur_usr_type_cn==3){ //if phy
				$el_def_tech_lasik = $_SESSION["authId"];
			}
			
			//Finalize Button
			$btn_finalize =  (!empty($elem_finalized_status)) ? "Un-Finalize" : "Finalize";
			if($btn_finalize=="Un-Finalize"){ $css_btn_hidden="hidden"; }

			//tech id
			if(!empty($el_def_tech_lasik)){
				if(empty($el_pre_op_tech)){$el_pre_op_tech = $el_def_tech_lasik;}
				if(empty($el_post_op_tech)){$el_post_op_tech = $el_def_tech_lasik;}
				if(empty($el_vpv_name_tech)){$el_vpv_name_tech = $el_def_tech_lasik;}
				if(empty($el_vpv_dob_tech)){$el_vpv_dob_tech = $el_def_tech_lasik;}
				if(empty($el_vpv_proc_site_tech)){$el_vpv_proc_site_tech = $el_def_tech_lasik;}
				if(empty($el_vpv_proc_type_tech)){$el_vpv_proc_type_tech = $el_def_tech_lasik;}
			}

			if(!empty($el_post_op_surgeon) ){
				if(empty($el_surgeon_sign)){
				$click_get_sign=1;
				$el_surgeon_sign_dos_todo= date("m-d-Y");
				}
				$ousr_srg = new User($el_post_op_surgeon);
				$arsign = $ousr_srg->getSign(2);
				$el_surgeon_sign_path = $arsign[0];
			}

			if(empty($id_chart_proc_lasik)){
				$oVis = new Vision($patient_id, $formId);
				$tmp_vis_mr = $oVis->get_last_given_mr();

				$el_dos_mr_od=$tmp_vis_mr[0];
				$el_dos_mr_os=$tmp_vis_mr[1];
			}



			//LASIK --


			//=========Get Array of Chart Procedure Medication========================//
			if($elem_chart_procedures_id){
				$prop=$postop=$intervatil=1;
				$arr_medication=array();
				$qry_med_lot="SELECT * from chart_procedures_med_lot WHERE chart_procedure_id='".$elem_chart_procedures_id."'";
				$res_med_lot=imw_query($qry_med_lot);
				while($row_med_lot=imw_fetch_assoc($res_med_lot)){
					$id=$row_med_lot['id'];
					$med_name=$row_med_lot['med_name'];
					$med_type=$row_med_lot['med_type'];
					$med_lot_number=$row_med_lot['lot_number'];
					if($med_type=='preop'){
						$arr_medication['id']['preop'][$prop]=$id;
						$arr_medication['medname']['preop'][$prop]=$med_name;
						$arr_medication['lot_no']['preop'][$prop]=$med_lot_number;
						$prop++;
					}
					if($med_type=='postop'){
						$arr_medication['id']['postop'][$postop]=$id;
						$arr_medication['medname']['postop'][$postop]=$med_name;
						$arr_medication['lot_no']['postop'][$postop]=$med_lot_number;
						$postop++;
					}
					if($med_type=='intravitreal'){
						$arr_medication['id']['intravitreal'][$intervatil]=$id;
						$arr_medication['medname']['intravitreal'][$intervatil]=$med_name;
						$arr_medication['lot_no']['intravitreal'][$intervatil]=$med_lot_number;
						$intervatil++;
					}
				}
			}

			//==============Get Qty==================//
			$arr_item_qty=$arr_item_qty_thras=$arr_thrash=$arr_lot_no=array();
			if(constant("connect_optical")==1){
				$arr_item_qty_thras=$oAdmn->get_lot_qty();
				$arr_item_qty=$arr_item_qty_thras[0];
				$arr_thrash=$arr_item_qty_thras[1];
				$arr_lot_no=$arr_item_qty_thras[2];
			}
			$js_arr_item_qty=json_encode($arr_item_qty);
			$js_arr_thrash=json_encode($arr_thrash);
			$js_arr_lot_no=json_encode($arr_lot_no);
			//=========================================//

			//==============Get Medication==================//
			$arr_med_opt=array();$js_arr_med="";
			$arr_med_opt=$oAdmn->get_admn_medications();
			$js_arr_med=json_encode($arr_med_opt);
			//=========================================//
			//Edit  Procedure Note ---

			//Procedures
			list($strProcOptions, $el_proc_perfmd_amnd)=$oAdmn->getProcedures($elem_procedure,1);

			//Correct Procedures
			$strCorProcOptions=$oAdmn->getProcedures($elem_corrctprocedure);

			//Alergies ---
			$cssAllergy = $jsAllergy="";
			$checkAllergy = $oMedHx->getCommonNoMedicalHistory($moduleName="Allergy");
			if($checkAllergy != "checked"){
				$strAllergies=$oMedHx->getAllergies_v2();
				if(!empty($strAllergies)){
					$cssAllergy = "red_color";
					$jsAllergy = " onmouseover=\"showAllergys(1)\" onmouseout=\"showAllergys(0)\" ";
				}
			}else{
				$cssAllergy = "green_color";
			}
			//--

			//Consent forms
			$strConsentForms_ret = $oAdmn->getConsentForms($elem_consentForm);
			$strConsentForms=$strConsentForms_ret[0];
			$arr_consent=json_encode($strConsentForms_ret[1]);

			//PnTemplate
			$strPnTemplate_arr = $this->getPnTemplate($elem_OpTempId);
			$strPnTemplate=$strPnTemplate_arr[0];
			$arr_opnotes=json_encode($strPnTemplate_arr[1]);

			//User Name
			$ouser = new User();
			$userName = trim($ouser->getName(5));

			if(strpos($elem_providers, $userName)===false){
				$elem_providers.= "".$userName.", ";
				$elem_providers=trim($elem_providers);
			}

			//getDOSProcedres
			$strTabsDosProc = $this->getTabsDosProcs( $elem_chart_procedures_id);

			//--Check Previous Edited procedure note if any exists --
			if(!empty($elem_chart_procedures_id)){
				$sql="SELECT * FROM chart_procedures WHERE proc_note_masterId = '".$elem_chart_procedures_id."' ";
				$row=sqlQuery($sql);
				if($row!=false){

					$elem_chart_procedures_id_prev=$row["id"];
					$elem_BP_prev=$row["bp"];
					$elem_heartattack_prev=$row["heart_attack"];
					$elem_otherProcNote_prev=$row["otherProcNote"];
					$elem_procedure_prev=$row["proc_id"];
					$elem_site_prev=$row["site"];
					$elem_lids_opts_prev=$row["lids_opts"];
					$elem_cor_lids_opts_prev=$row["cor_lids_opts"];
					$elem_dxCode_prev=$row["dx_code"];
					$elem_cpt_code_prev=$row["cpt_code"];
					$elem_cpt_mod_prev=$row["cpt_mod"];
					$elem_startTime_prev=$row["start_time"];
					$elem_endTime_prev=$row["end_time"];
					$elem_iopType_prev=$row["iop_type"];
					$elem_iopOd_prev=$row["iop_od"];
					$elem_iopOs_prev=$row["iop_os"];
					$elem_iopTime_prev=$row["iop_time"];
					$elem_complication_prev=$row["complication"];
					$elem_complication_detail_prev=$row["complication_desc"];
					$elem_comments_prev=$row["comments"];
					list($elem_exam_date,$elem_exam_time)=explode(" ",$row["exam_date"]);

					if(!empty($row["pre_op_meds"])){	$arrPreOpMed_prev= explode("|~|", $row["pre_op_meds"]); }else{ $arrPreOpMed_prev= array();  }

					if(!empty($row["intravit_meds"])){	$arrIntraVitrealMeds_prev= explode("|~|", $row["intravit_meds"]); }else{ $arrIntraVitrealMeds_prev= array();  }

					if(!empty($row["post_op_meds"])){ $arrPostOpMeds_prev= explode("|~|", $row["post_op_meds"]); }else{ $arrPostOpMeds_prev= array();  }

					$elem_IntraVitrealMeds_Lot_prev=$row["intravit_meds_lot"];
					$elem_PostOpMeds_Lot_prev=$row["post_op_meds_lot"];
					$elem_timeout_prev=$row["timeout"];
					$elem_corrctprocedure_prev=$row["corr_proc_id"];
					$elem_corrctsite_prev=$row["corr_site"];
					$elem_siteMarked_prev=$row["site_marked"];
					$elem_positionProstheses_prev=$row["pos_pros_implant"];
					$elem_consentCompletedSigned_prev=$row["consent_completed"];
					$elem_providers_prev=$row["providers"];
					$curConfrmId_prev= $elem_consentForm_prev=$row["consent_form_id"];
					$curOpId_prev= $elem_OpTempId_prev=$row["op_report_id"];
					//$hold_to_physician_prev=$row[""];
					$hidd_hold_to_physician_prev=$row["hidd_hold_to_physician"];
					$elem_cmt_prev= $row["cmt"];
					$elem_finalized_status_prev= $row["finalized_status"];

				}

			}

			//lids opts--
			if(!empty($elem_lids_opts)){
				if(strpos($elem_lids_opts, "RUL")!==false){ $elem_lidsopt_rul='RUL'; }
				if(strpos($elem_lids_opts, "RLL")!==false){ $elem_lidsopt_rll='RLL'; }
				if(strpos($elem_lids_opts, "LUL")!==false){ $elem_lidsopt_lul='LUL'; }
				if(strpos($elem_lids_opts, "LLL")!==false){ $elem_lidsopt_lll='LLL'; }
			}
			//--

			//lids Correct opts--
			if(!empty($elem_cor_lids_opts)){
				if(strpos($elem_cor_lids_opts, "RUL")!==false){ $elem_cor_lidsopt_rul='RUL'; }
				if(strpos($elem_cor_lids_opts, "RLL")!==false){ $elem_cor_lidsopt_rll='RLL'; }
				if(strpos($elem_cor_lids_opts, "LUL")!==false){ $elem_cor_lidsopt_lul='LUL'; }
				if(strpos($elem_cor_lids_opts, "LLL")!==false){ $elem_cor_lidsopt_lll='LLL'; }
			}
			//--

			$js_arr_dx_code=$oDx->dx_code_arr_get();
			$js_arr_dx=json_encode($js_arr_dx_code);

			$js_arr_mod_code=$oMod->get_mod_prac_code();
			$js_arr_mod=json_encode($js_arr_mod_code);

			$js_arr_cpt_code=$oCPT->get_cpt_code_arr();
			$js_arr_cpt=json_encode($js_arr_cpt_code);

			//cpt_multi_select_options
			$elem_cpt_code_arr 	= explode("|~|",$elem_cpt_code);
			$elem_cpt_mod_arr 	= explode("|~|",$elem_cpt_mod);
			$t=0;
			$cpt_multi_select_options="";
			if(count($elem_cpt_code_arr)>0){
				foreach($elem_cpt_code_arr as $key_cnt=> $cpt_val){

					$opt_val=$cpt_val;$opt_val_dis = $cpt_val;
					if(!empty($cpt_val)){$tmp = $oCPT->get_cpt_code_arr($cpt_val);}
					if(!empty($tmp)){ $opt_val_dis .= " - ".$tmp; }
					if($elem_cpt_mod_arr[$key_cnt] && trim($elem_cpt_mod_arr[$key_cnt])!=";"){
						$opt_val=$cpt_val." - ".$elem_cpt_mod_arr[$key_cnt];
						$opt_val_dis .= " - ".$elem_cpt_mod_arr[$key_cnt];
					}
					$opt_val_dis = trim($opt_val_dis);
					$opt_val=trim($opt_val);
					if(!empty($opt_val)){
						$cpt_multi_select_options.="<option selected value='".$opt_val."'>".$opt_val_dis."</option>";
					}
				}
			}
			$cntCptGrid_t=$t;

			//Dx
			$dx_options="";
			if(strstr($elem_dxCode,";")){
				$arr_dx_codes=explode(";",$elem_dxCode);
				foreach($arr_dx_codes as $dx_val){
					$dx_val=trim($dx_val);
					if($dx_val){
						$dx_options.="<option selected value='".$dx_val."'>".$dx_val."</option>";
					}
				}

			}else if($elem_dxCode){
				$dx_options="<option selected value='".$elem_dxCode."'>".$elem_dxCode."</option>";
			}


			// Pre OP --
				$html_PreOp=array();
				$html_PreOp["Med"]="";
				$html_PreOp["Lot"]="";
				$html_PreOp["Qty"]="";
				$prop = ($prop<3) ? 3 : $prop;
				for($i=1;$i<=$prop;$i++){
					$namePreOpMed="elem_PreOpMed".($i);
					$valPreOpMed=trim($arr_medication['medname']['preop'][$i]);

					$namePreOpMedLot="elem_PreOpMedLot".($i);
					$valPreOpMedLot=trim($arr_medication['lot_no']['preop'][$i]);

					$namePreOpMedLot_id="elem_med_preop_lot_id".($i);
					$valPreOpMedLot_id=trim($arr_medication['id']['preop'][$i]);

					$namePreOpMedLot_qty="elem_med_preop_lot_qty".($i);
					$valPreOpMedLot_qty=trim($arr_medication['id']['qty'][$i]);

					$tmp_css="";
					if(isset($arrPreOpMed_prev)){
						$valPreOpMed_prev=trim($arrPreOpMed_prev[$i]);
						if($valPreOpMed!=$valPreOpMed_prev){
							$tmp_css = $this->proc_note_getEditCss_p2($valPreOpMed, $valPreOpMed_prev);
						}
					}

					$html_PreOp["Med"] .= "<span id=\"divPreOpMeds\"><input type=\"text\" name=\"".$namePreOpMed."\" value=\"".$valPreOpMed."\"  class=\"form-control ".$tmp_css."\" onChange=\"getMedName(this);\"></span>";
					$html_PreOp["Lot"] .= "<input type=\"text\" name=\"".$namePreOpMedLot."\" value=\"".$valPreOpMedLot."\" class='elem_med_preop_lot_qty form-control'  >";
					$html_PreOp["Lot"] .= "<input type=\"hidden\" name=\"".$namePreOpMedLot_id."\" value=\"".$valPreOpMedLot_id."\"  >";
					$html_PreOp["Qty"] .= "<input type=\"text\" readonly name=\"".$namePreOpMedLot_qty."\" value=\"".$valPreOpMedLot_qty."\" class='form-control'  >";
				}

			//--
			// Intra-vitreal meds
			$div_intravitreal_meds="";$div_laser_procedure_notes="hide";
			if($laser_procedure_note=="1"){$div_intravitreal_meds="hide";$div_laser_procedure_notes="";	}

				$html_IntVit=array();
				$html_IntVit["Med"]="";
				$html_IntVit["Lot"]="";
				$html_IntVit["Qty"]="";

				$intervatil = ($intervatil<3) ? 3 : $intervatil;
				for($i=1;$i<=$intervatil;$i++){
					$nameIntraVitrealMeds="elem_IntraVitrealMeds".($i);
					$valIntraVitrealMeds=$arr_medication['medname']['intravitreal'][$i];

					$nameIntraVitrealMedsLot="elem_IntraVitrealMedsLot".($i);
					$valIntraVitrealMedsLot=$arr_medication['lot_no']['intravitreal'][$i];

					$nameIntraVitrealMedLot_id="elem_med_intravitreal_lot_id".($i);
					$valIntraVitrealMedLot_id=trim($arr_medication['id']['intravitreal'][$i]);

					$nameIntraVitrealMedLot_qty="elem_med_intravitreal_lot_qty".($i);
					$valIntraVitrealMedLot_qty=trim($arr_medication['qty']['intravitreal'][$i]);

					$tmp_css="";
					if(isset($arrIntraVitrealMeds_prev)){
						$valIntraVitrealMeds_prev=$arrIntraVitrealMeds_prev[$i];
						if($valIntraVitrealMeds != $valIntraVitrealMeds_prev){
							$tmp_css = $this->proc_note_getEditCss_p2($valIntraVitrealMeds, $valIntraVitrealMeds_prev);
						}
					}

					$html_IntVit["Med"] .= "<span id=\"divIntraVitrealMeds\"><input type=\"text\" name=\"".$nameIntraVitrealMeds."\" value=\"".$valIntraVitrealMeds."\"  class=\"form-control ".$tmp_css."\" onChange=\"getMedName(this);\"></span>";
					$html_IntVit["Lot"] .= "<input type=\"text\" name=\"".$nameIntraVitrealMedsLot."\" value=\"".$valIntraVitrealMedsLot."\" class='elem_med_intravitreal_lot_qty form-control'  >";
					$html_IntVit["Lot"] .= "<input type=\"hidden\" name=\"".$nameIntraVitrealMedLot_id."\" value=\"".$valIntraVitrealMedLot_id."\" >";
					$html_IntVit["Qty"] .= "<input type=\"text\" readonly name=\"".$nameIntraVitrealMedLot_qty."\" value=\"".$valIntraVitrealMedLot_qty."\" class='form-control'  >";

				}

			// Post - op meds
			$html_PostOp=array();
			$html_PostOp["Med"]="";
			$html_PostOp["Lot"]="";
			$html_PostOp["Qty"]="";
			$postop = ($postop<3) ? 3 : $postop;
			for($i=1;$i<=$postop;$i++){
				$namePostOpMeds="elem_PostOpMeds".($i);
				$valPostOpMeds=$arr_medication['medname']['postop'][$i];

				$namePostOpMedsLot="elem_PostOpMedsLot".($i);
				$valPostOpMedsLot=$arr_medication['lot_no']['postop'][$i];

				$namePostOpMedLot_id="elem_med_postop_lot_id".($i);
				$valPostOpMedLot_id=trim($arr_medication['id']['postop'][$i]);

				$namePostOpMedLot_qty="elem_med_postop_lot_qty".($i);
				$valPostOpMedLot_qty=trim($arr_medication['qty']['postop'][$i]);

				$tmp_css="";
				if(isset($arrPostOpMeds_prev)){
					$valPostOpMeds_prev=$arrPostOpMeds_prev[$i];
					if($valPostOpMeds!=$valPostOpMeds_prev){
						$tmp_css = $this->proc_note_getEditCss_p2($valPostOpMeds, $valPostOpMeds_prev);
					}
				}

				$html_PostOp["Med"] .= "<span id=\"divPostOpMeds\"><input type=\"text\" name=\"".$namePostOpMeds."\" value=\"".$valPostOpMeds."\"  class=\"form-control ".$tmp_css."\" onChange=\"getMedName(this);\"></span>";
				$html_PostOp["Lot"] .= "<input type=\"text\" name=\"".$namePostOpMedsLot."\" value=\"".$valPostOpMedsLot."\" class='elem_med_postop_lot_qty form-control '  >";
				$html_PostOp["Lot"] .= "<input type=\"hidden\" name=\"".$namePostOpMedLot_id."\" value=\"".$valPostOpMedLot_id."\"  >";
				$html_PostOp["Qty"] .= "<input type=\"text\" readonly name=\"".$namePostOpMedLot_qty."\" value=\"".$valPostOpMedLot_qty."\" class=\"form-control \"  >";

			}

			// default botox : always set new name for face image
			if(!isset($elem_cnvs_bottox_drw_img)||empty($elem_cnvs_bottox_drw_img)){$elem_cnvs_bottox_drw_img=$GLOBALS['webroot']."/library/images/face_bottox_v2.jpg";}
			if(!isset($elem_cnvs_bottox_drw_img_dim)||empty($elem_cnvs_bottox_drw_img_dim)){$elem_cnvs_bottox_drw_img_dim="371X400";}

			$ar_cnvs_bottox_drw_img_dim=explode("X", $elem_cnvs_bottox_drw_img_dim);
			$elem_cnvs_bottox_drw_img_dim_w=$ar_cnvs_bottox_drw_img_dim[0];
			$elem_cnvs_bottox_drw_img_dim_h=$ar_cnvs_bottox_drw_img_dim[1];

			$old_pt_bottox=$this->getOldBottox();
			$botoxDosages=$oAdmn->getBotoxDosages();

			//proc_note_getEditCss
			$ar_proc_note_getEditCss=array();
			$ar_proc_note_getEditCss_vars=array("elem_BP", "elem_otherProcNote", "elem_procedure", "elem_iopType", "elem_iopOd", "elem_iopOs", "elem_iopTime",
										"elem_startTime", "elem_endTime", "elem_cmt", "elem_comments", "elem_corrctprocedure", "elem_providers");
			foreach($ar_proc_note_getEditCss_vars as $key => $str_elm){
				$var_prev_elm = $str_elm."_prev";
				//global $$str_elm, $$var_prev_elm;
				$ar_proc_note_getEditCss[$str_elm]=$this->proc_note_getEditCss($str_elm);
			}
			//--

			//image to new location
			if(strpos($elem_cnvs_bottox_drw_img,"iDoc-Drawing")!==false){
				$elem_cnvs_bottox_drw_img = str_replace("iDoc-Drawing/images",$GLOBALS['webroot']."/library/images",$elem_cnvs_bottox_drw_img);
			}

			//Array css files --
			$oWv = new WorkView();
			$arr_css_files=array("common.css", "workview.css", "style.css",  "wv_landing.css","drawing.css", "superbill.css");
			$css_files=$oWv->get_min_file($arr_css_files,"css");

			//Array js files --
			$arr_js_files=array( "work_view/fabric.more.js", "work_view/js_gen.js","work_view/work_view.js","work_view/typeahead.js", "work_view/eventIndicator.js",  "icd10_autocomplete.js", "work_view/chart_exam.js", "work_view/js_qry.js", "work_view/procedures.js", "work_view/superbill.js"	);
			$js_files=$oWv->get_min_file($arr_js_files,"js");


			//Lasik drop downs
			$ar_user_techs = $ouser->getUserArr('1',"cn_tech"); //techs
			$ar_user_phy = $ouser->getUserArr('1',"cn_phy"); //Phy

			$ddPreOPTech=	$ddNameSurg =	$ddNameTech =
			$ddDOBSurg =	$ddDOBTech=	$ddProcSiteSurg=
			$ddProcSiteTech=	$ddProcTypeSurg=	$ddProcTypeTech=
			$ddPostOpSurg=		$ddPostOpLaserOprator=	$ddPostOpKeraTech=
			$ddPostOpTech="<option value=\"\"></option>";

			//Tech
			foreach($ar_user_techs as $k => $v){

				$ddPreOPTech.="<option value=\"".$k."\" ".($el_pre_op_tech == $k ? "selected" : "")." >".$v."</option>";
				$ddNameTech.="<option value=\"".$k."\" ".($el_vpv_name_tech == $k ? "selected" : "")." >".$v."</option>";
				$ddDOBTech.="<option value=\"".$k."\" ".($el_vpv_dob_tech == $k ? "selected" : "")." >".$v."</option>";
				$ddProcSiteTech.="<option value=\"".$k."\" ".($el_vpv_proc_site_tech == $k ? "selected" : "")." >".$v."</option>";
				$ddProcTypeTech.="<option value=\"".$k."\" ".($el_vpv_proc_type_tech == $k ? "selected" : "")." >".$v."</option>";
				$ddPostOpLaserOprator.="<option value=\"".$k."\" ".($el_laser_oprtr == $k ? "selected" : "")." >".$v."</option>";
				$ddPostOpKeraTech.="<option value=\"".$k."\" ".($el_keratome_tech == $k ? "selected" : "")." >".$v."</option>";
				$ddPostOpTech.="<option value=\"".$k."\" ".($el_post_op_tech == $k ? "selected" : "")." >".$v."</option>";

			}

			//Surgeon
			foreach($ar_user_phy as $k => $v){

				$ddNameSurg.="<option value=\"".$k."\" ".($el_vpv_name_srg == $k ? "selected" : "")." >".$v."</option>";
				$ddDOBSurg.="<option value=\"".$k."\" ".($el_vpv_dob_srg == $k ? "selected" : "")." >".$v."</option>";
				$ddProcSiteSurg.="<option value=\"".$k."\" ".($el_vpv_proc_site_srg == $k ? "selected" : "")." >".$v."</option>";
				$ddProcTypeSurg.="<option value=\"".$k."\" ".($el_vpv_proc_type_srg == $k ? "selected" : "")." >".$v."</option>";
				$ddPostOpSurg.="<option value=\"".$k."\" ".($el_post_op_surgeon == $k ? "selected" : "")." >".$v."</option>";
			}


			$z_ob_get_clean=$GLOBALS['fileroot']."/interface/chart_notes/view/procedures.php";
			include($GLOBALS['fileroot']."/interface/chart_notes/minfy_inc.php");

		}
	}

	function save(){
		$patient_id = $this->pid;
		// Initializations
		$oPnTs = new PnTemplate();
		$oPnRep = new PnReports();

		//--
		$curOpId = $_POST["curOpId"];
		$curConfrmId = $_POST["curConfrmId"];
		$elem_chart_procedures_id = $_POST["elem_chart_procedures_id"];
		//$patient_id = $_POST["elem_patient_id"];
		$form_id = $_POST["elem_form_id"];
		$bp = sqlEscStr($_POST["elem_BP"]);
		$heart_attack = $_POST["elem_heartattack"];
		$otherProcNote = sqlEscStr($_POST["elem_otherProcNote"]);
		$proc_id = $_POST["elem_procedure"];
		$site = $_POST["elem_site"];
		if($_POST["elem_dxCode"] && is_array($_POST["elem_dxCode"])){$_POST["elem_dxCode"]=implode(";",$_POST["elem_dxCode"]);}
		$dx_code = $_POST["elem_dxCode"];
		$start_time = $_POST["elem_startTime"];
		$end_time = $_POST["elem_endTime"];
		$iop_type = $_POST["elem_iopType"];
		$iop_od = $_POST["elem_iopOd"];
		$iop_os = $_POST["elem_iopOs"];
		$iop_time = $_POST["elem_iopTime"];
		$complication = $_POST["elem_complication"];
		$complication_desc = sqlEscStr($_POST["elem_complication_detail"]);
		$comments = sqlEscStr($_POST["elem_comments"]);
		$cmt = sqlEscStr($_POST["elem_cmt"]);
		$elem_finalized_status = $_POST["elem_finalized_status"];
		$elem_finalize_flag = $_POST["elem_finalize_flag"];
		$elem_isReviewable = $_POST["elem_isReviewable"];
		$hid_str_cpt_mod = $_POST["hid_str_cpt_mod"];
		$elem_bottox_open_flg = $_POST["elem_bottox_open_flg"];
		//$elem_arr_cpt_code=explode("||",$_POST['elem_cptCode']);
		//$elem_cpt_code=$elem_arr_cpt_code[0];
		//$elem_mod_code=$_POST['elem_mod_code'];

		$elem_chart_DOS = wv_formatDate($_POST["elem_chart_DOS"],0,3,"insert"); //chart DOS
		//--

		//=======================Laser Procedure Notes====================================================//
		$laser_procedure_note=$_POST["laser_procedure_note"];
		$spot_duration=$spot_size=$power=$shots=$total_energy=$degree_of_opening=$exposure=$count="";
		if($laser_procedure_note==1){
			$spot_duration=trim(addslashes($_POST["spot_duration"]));
			$spot_size=trim(addslashes($_POST["spot_size"]));
			$power=trim(addslashes($_POST["power"]));
			$shots=trim(addslashes($_POST["shots"]));
			$total_energy=trim(addslashes($_POST["total_energy"]));
			$degree_of_opening=trim(addslashes($_POST["degree_of_opening"]));
			$exposure=trim(addslashes($_POST["exposure"]));
			$count=trim(addslashes($_POST["count"]));
		}
		//===========================================================================//

		//--
		$lids_opts="";
		if(!empty($_POST["elem_lidsopt_rul"])){$lids_opts .= $_POST["elem_lidsopt_rul"].",";}
		if(!empty($_POST["elem_lidsopt_rll"])){$lids_opts .= $_POST["elem_lidsopt_rll"].",";}
		if(!empty($_POST["elem_lidsopt_lul"])){$lids_opts .= $_POST["elem_lidsopt_lul"].",";}
		if(!empty($_POST["elem_lidsopt_lll"])){$lids_opts .= $_POST["elem_lidsopt_lll"].",";}
		if(!empty($lids_opts)){  $lids_opts=trim($lids_opts,","); $lids_opts=sqlEscStr($lids_opts); }
		//--

		//--
		$cor_lids_opts="";
		if(!empty($_POST["elem_cor_lidsopt_rul"])){$cor_lids_opts .= $_POST["elem_cor_lidsopt_rul"].",";}
		if(!empty($_POST["elem_cor_lidsopt_rll"])){$cor_lids_opts .= $_POST["elem_cor_lidsopt_rll"].",";}
		if(!empty($_POST["elem_cor_lidsopt_lul"])){$cor_lids_opts .= $_POST["elem_cor_lidsopt_lul"].",";}
		if(!empty($_POST["elem_cor_lidsopt_lll"])){$cor_lids_opts .= $_POST["elem_cor_lidsopt_lll"].",";}
		if(!empty($cor_lids_opts)){  $cor_lids_opts=trim($cor_lids_opts,","); $cor_lids_opts=sqlEscStr($cor_lids_opts); }
		//--

		///Print when   chart is finalized : ticket #10940
		 $flg_do_no_save="";
		 //if(($elem_per_vo == "1") || (!empty($elem_finalize_flag) && empty($elem_isReviewable))){
			//print --
			if(!empty($_POST["elem_hidPrint"]) && $_POST["elem_hidPrint"]==2){
				$flg_do_no_save="1";
			}
			//print --
		 //}
		//--

		//
		if(empty($flg_do_no_save)){ //Block save
			//START ADD MULTIPLE CPT CODE AND MODIFIER
			$j=1;
			$elem_cptCodeArr = array();
			$elem_mod_codeArr = array();

			$hid_arr_cpt_mod = (!empty($hid_str_cpt_mod)) ? explode("~~", $hid_str_cpt_mod) : array();
			if(count($_POST['cpt_multi_select'])>0){
				$arr_CPTMOD=$_POST['cpt_multi_select'];
				foreach($arr_CPTMOD as $val_cpt_mod){
					$elem_cpt=$elem_mod="";
					$arr_cpt_mod=explode("-",$val_cpt_mod);
					$elem_cpt=$arr_cpt_mod[0];
					$elem_mod=count($arr_cpt_mod)>1 ? end($arr_cpt_mod) : "";

					// if there are hyphens in cpt prac codes, then recheck cpt and mod again
					if(count($arr_cpt_mod)>=2){

						//check in hid array
						if(count($hid_arr_cpt_mod)>0){
							foreach($hid_arr_cpt_mod as $tk => $tv){

								$elem_cpt = trim($elem_cpt);
								$elem_mod=trim($elem_mod);
								if(strpos($tv, $elem_cpt)!==false && strpos($tv, $elem_mod)!==false ){ //

									$artv = explode("||", $tv);
									$artv_cpt = explode(" - ", $artv[0]);
									if(trim($artv_cpt[0])==$elem_cpt){
										$elem_cpt = $artv_cpt[0];
									}

									//
									if(trim($artv[1])==$elem_mod){
										$elem_mod = $artv[1];
									}
								}
							}
						}
					}
					//---

					$elem_cptCodeArr[]=$elem_cpt; //$elem_cpt;
					if(!empty($elem_cpt)){
					$elem_mod_codeArr[]=(!empty($elem_mod)) ? $elem_mod : ";"; //; is set so that if empty mode come inline with cpt
					}
				}
			}//end if

			if(count($elem_cptCodeArr)) {
				$elem_cpt_code = implode("|~|",$elem_cptCodeArr);
				$elem_mod_code = implode("|~|",$elem_mod_codeArr);
			}
			//END ADD MULTIPLE CPT CODE AND MODIFIER

			 //[elem_PreOpMed1];
			 $pre_op_meds=$intravit_meds=$post_op_meds = "";

			$i=1;
			while(true){

				if(!isset($_POST["elem_PreOpMed".$i]) && !isset($_POST["elem_IntraVitrealMeds".$i]) && !isset($_POST["elem_PostOpMeds".$i])) {  break; }

				$elem_PreOpMed=$_POST["elem_PreOpMed".$i];
				$elem_IntraVitrealMeds=$_POST["elem_IntraVitrealMeds".$i];
				$elem_PostOpMeds=$_POST["elem_PostOpMeds".$i];

				if(!empty($elem_PreOpMed)){ $pre_op_meds.="".$elem_PreOpMed."|~|";  }

				if(!empty($elem_IntraVitrealMeds)){ $intravit_meds.="".$elem_IntraVitrealMeds."|~|";  }

				if(!empty($elem_PostOpMeds)){ $post_op_meds.="".$elem_PostOpMeds."|~|";  }

				$i++;

			} //end while

			//[elem_IntraVitrealMeds1];
			$intravit_meds_lot = sqlEscStr($_POST["elem_IntraVitrealMeds_Lot"]);

			//[elem_PostOpMeds1];
			$post_op_meds_lot = sqlEscStr($_POST["elem_PostOpMeds_Lot"]);
			$timeout = $_POST["elem_timeout"];

			$corr_proc_id = $_POST["elem_corrctprocedure"];

			$corr_site = $_POST["elem_corrctsite"];
			$site_marked = $_POST["elem_siteMarked"];
			$pos_pros_implant = $_POST["elem_positionProstheses"];
			$consent_completed = $_POST["elem_consentCompletedSigned"];

			$providers = sqlEscStr($_POST["elem_providers"]);
			$consent_form_id = $_POST["elem_consentForm"];
			$op_report_id = $_POST["elem_OpTempId"];
			$pnData = $_POST["elem_pnData"];
			$hold_to_physician = $_POST["hold_to_physician"];
			$hidd_hold_to_physician = $_POST["hidd_hold_to_physician"];

			$user_id=$_SESSION["authId"];

			//===NOW FUNCTION REMOVED BCOZ THE PROCEDURE TIME WAS NOT MATCHED FOR PHYSICIAN CONSOLE ONHOLD SAVED DOCS.==
			$exam_date = $elem_chart_DOS;
			$modify_date = date('Y-m-d H:i:s');
			//==========================================================================================================

			//-- Procedure Note --
			//delete proc note --
			if(isset($_POST["chart_del_id"]) && !empty($_POST["chart_del_id"])){
				$sql = "UPDATE chart_procedures  SET deleted_by = '".$user_id."', modify_date= '".$exam_date."'  WHERE id = '".$_POST["chart_del_id"]."'   ";
				$row=sqlQuery($sql);
				/*
				echo "
					<html>
					<body>
					<script>
					window.close();
					</script>
					</body>
					</html>
				";
				*/

				$arr=array();
				echo json_encode($arr);
				exit();

			}
			//delete proc note --

			//finalize proc note --
			if(isset($_POST["chart_finalize_id"]) && isset($_POST["op_finalize"]) && !empty($_POST["chart_finalize_id"])){

				$finalized_by="0"; $finalized_date="0000-00-00 00:00:00";$op_finalize="0";
				if(!empty($_POST["op_finalize"])){
					$finalized_by = $_SESSION["authId"];
					$finalized_date=wv_dt("now");
					$op_finalize=trim($_POST["op_finalize"]);
				}

				$sql = "UPDATE chart_procedures SET finalized_status = '".$op_finalize."', Finalized_by='".$finalized_by."', Finalized_date='".$finalized_date."' WHERE id='".$_POST["chart_finalize_id"]."' ";
				$row = sqlQuery($sql);

				$arr=array();
				$arr["finalize"] = $op_finalize;
				$arr["btnfinalize"] = !empty($op_finalize) ? "Un-Finalize" : "Finalize";
				echo json_encode($arr);
				exit();

			}
			//finalize proc note --

			$sqlInsert="INSERT INTO chart_procedures  SET patient_id='".$patient_id."', form_id='".$form_id."',  ";

			$sqlUpdate1="UPDATE chart_procedures  SET ";
			$sqlUpdate2=" WHERE id='".$elem_chart_procedures_id."' ";

			$data = "
					exam_date= '".$exam_date."',
					bp='".$bp."',
					heart_attack='".$heart_attack."',
					comments='".$comments."',
					proc_id='".$proc_id."',
					site='".$site."',
					dx_code='".$dx_code."',
					start_time='".$start_time."',
					end_time='".$end_time."',
					iop_type='".$iop_type."',
					iop_od='".$iop_od."',
					iop_os='".$iop_os."',
					iop_time='".$iop_time."',
					complication='".$complication."',
					complication_desc='".$complication_desc."',
					pre_op_meds='".sqlEscStr($pre_op_meds)."',
					intravit_meds='".sqlEscStr($intravit_meds)."',
					intravit_meds_lot='".$intravit_meds_lot."',
					post_op_meds='".sqlEscStr($post_op_meds)."',
					post_op_meds_lot='".$post_op_meds_lot."',
					timeout='".$timeout."',
					corr_proc_id='".$corr_proc_id."',
					corr_site='".$corr_site."',
					site_marked='".$site_marked."',
					pos_pros_implant='".$pos_pros_implant."',
					consent_completed='".$consent_completed."',
					providers='".$providers."',
					consent_form_id='".$consent_form_id."',
					op_report_id='".$op_report_id."',
					user_id='".$user_id."',
					modify_date='".$modify_date."',
					otherProcNote='".$otherProcNote."',
					hidd_hold_to_physician='".$hidd_hold_to_physician."',
					cpt_code='".sqlEscStr($elem_cpt_code)."',
					cpt_mod='".sqlEscStr($elem_mod_code)."',
					cmt = '".$cmt."',
					lids_opts='".$lids_opts."',
					cor_lids_opts='".$cor_lids_opts."',
					laser_procedure_note='".$laser_procedure_note."',
					spot_duration='".$spot_duration."',
					spot_size='".$spot_size."',
					power='".$power."',
					shots='".$shots."',
					total_energy='".$total_energy."',
					degree_of_opening='".$degree_of_opening."',
					exposure='".$exposure."',
					count='".$count."'
					";

				//--

			if(!empty($elem_chart_procedures_id) && empty($elem_finalized_status)){	//Update

				$sql=$sqlUpdate1.$data.$sqlUpdate2;
				$row=sqlQuery($sql);

			}else{	//Insert

				if(!empty($elem_chart_procedures_id)) { $elem_chart_procedures_id_prev = $elem_chart_procedures_id; }

				$sql=$sqlInsert.$data;
				$elem_chart_procedures_id=sqlInsert($sql);

				//--
				if(!empty($elem_chart_procedures_id_prev) && !empty($elem_finalized_status)){	//update
					//Update master id
					$sql =  "UPDATE chart_procedures SET proc_note_masterId='".$elem_chart_procedures_id."' WHERE id =  '".$elem_chart_procedures_id_prev."' ";
					$row=sqlQuery($sql);
				}
				//--
			}

            //save superbill
            if(!empty($elem_chart_procedures_id)) {

			//if(!empty($form_id)) {
			   $cmt_qry = imw_query("select encounter_id from chart_procedures where id='".$elem_chart_procedures_id."' ");
			   $cmt_rs =imw_fetch_array($cmt_qry);
			//}

                $arIn=array();
                $arIn["elem_physicianId"]=$user_id;
                $arIn["doctorId"]=$user_id;
                $arIn["caseId"]=0;
                if($cmt_rs['encounter_id'] > 0) {
                   $arIn["encounterId"]=$cmt_rs['encounter_id'];
                } else {
			if(!empty($_POST["is_sb_md"])){ //create enc when superbill is made
				$oFacilityfun = new Facility();
				$encounterId = $oFacilityfun->getEncounterId();
				$arIn["encounterId"]=$encounterId;
				//Update
				$sql =  "UPDATE chart_procedures SET encounter_id='".$encounterId."' WHERE id =  '".$elem_chart_procedures_id."' ";
				$row=sqlQuery($sql);
			}
                }

                $arIn["date_of_service"]=getDateFormatDB($_POST["elem_chart_DOS"]);
                $arIn["sb_testId"]=$elem_chart_procedures_id;
                $arIn["sb_testName"]="Procedures";
                $arIn["sb_arrMerged"]=0;
                $arIn["form_id"]=$form_id;

                $oSuperbillSaver = new SuperbillSaver($patient_id);
                $oSuperbillSaver->save($arIn);

            }

			//--
			$pre_op_meds=$intravit_meds=$post_op_meds = "";

			$i=1;
			while(true){
				if(!isset($_POST["elem_PreOpMed".$i]) && !isset($_POST["elem_IntraVitrealMeds".$i]) && !isset($_POST["elem_PostOpMeds".$i])) {  break; }

				$elem_PreOpMed=trim($_POST["elem_PreOpMed".$i]);
				$elem_IntraVitrealMeds=trim($_POST["elem_IntraVitrealMeds".$i]);
				$elem_PostOpMeds=trim($_POST["elem_PostOpMeds".$i]);

				$elem_PreOpMedLot=trim($_POST["elem_PreOpMedLot".$i]);
				$elem_IntraVitrealMedsLot=trim($_POST["elem_IntraVitrealMedsLot".$i]);
				$elem_PostOpMedsLot=trim($_POST["elem_PostOpMedsLot".$i]);

				$elem_med_preop_lot_id=$_REQUEST["elem_med_preop_lot_id".$i];
				$elem_med_postop_lot_id=$_REQUEST["elem_med_postop_lot_id".$i];
				$elem_med_intravitreal_lot_id=$_REQUEST["elem_med_intravitreal_lot_id".$i];

				$qryConPreOp=" INSERT INTO ";
				$whrpreCon="";
				if($elem_med_preop_lot_id){
					$qryConPreOp=" UPDATE ";
					$whrpreCon=" WHERE id='".$elem_med_preop_lot_id."'";
				}

				if($elem_PreOpMed){
					$medication_name=$elem_PreOpMed;
					$qry_med=$qryConPreOp." chart_procedures_med_lot SET med_name='".sqlEscStr($medication_name)."',lot_number='".$elem_PreOpMedLot."',med_type='preop',chart_procedure_id='".$elem_chart_procedures_id."'".$whrpreCon;
					$res_med=imw_query($qry_med)or die(imw_error());
				}

				$qryConIntraVitreal=" INSERT INTO ";
				$whrIntraVitreal="";
				if($elem_med_intravitreal_lot_id){
					$qryConIntraVitreal=" UPDATE ";
					$whrIntraVitreal=" WHERE id='".$elem_med_intravitreal_lot_id."'";
				}
				if($elem_IntraVitrealMeds){
					$medication_name=$elem_IntraVitrealMeds;
					$qry_med=$qryConIntraVitreal." chart_procedures_med_lot SET med_name='".sqlEscStr($medication_name)."',lot_number='".$elem_IntraVitrealMedsLot."',med_type='intravitreal',chart_procedure_id='".$elem_chart_procedures_id."'".$whrIntraVitreal;
					$res_med=imw_query($qry_med)or die(imw_error());
				}

				$qryConPostOp=" INSERT INTO ";
				$whrpostCon="";
				if($elem_med_postop_lot_id){
					$qryConPostOp=" UPDATE ";
					$whrpostCon=" WHERE id='".$elem_med_postop_lot_id."'";
				}
				if($elem_PostOpMeds){
					$medication_name=$elem_PostOpMeds;
					$qry_med=$qryConPostOp." chart_procedures_med_lot SET med_name='".sqlEscStr($medication_name)."',lot_number='".$elem_PostOpMedsLot."',med_type='postop',chart_procedure_id='".$elem_chart_procedures_id."'".$whrpostCon;
					$res_med=imw_query($qry_med)or die(imw_error());
				}
				$i++;
			}
			//-- Procedure Note --

			//-- OP Note --
			if(!empty($pnData) && !empty($op_report_id)){

				///case "pnReports" :
				$elem_edit_mode = "new";
				$elem_edit_id = "";
				$elem_patient_id = $patient_id;
				$elem_form_id = $form_id;
				$elem_status = 0;
				$elem_pnData = $pnData;
				$elem_signCoords = "";
				$elem_date = date("Y-m-d H:i:s");//$elem_chart_DOS;
				$elem_tempId = $op_report_id;
				$elem_site = $site;

				if($elem_edit_mode == "delete"){
					//delete
					//$err = $oPnRep->deleteRecord($elem_edit_id);
					/*echo "<script>window.opener.top.refresh_control_panel('','".$_SESSION['patient']."');</script>";*/
				}else{
					$data = array();
					$data["editId"] = $elem_edit_id;
					$data["ptId"] = $elem_patient_id;
					$data["formId"] = $elem_form_id;
					$data["status"] = !empty($elem_status) ? $elem_status : 0 ;


					$data["txtData"] = $elem_pnData;
					$data["sign"] = $elem_signCoords;
					$data["date"] = $elem_date;
					$data["tempId"] = $elem_tempId;
					$data["opid"] = $_SESSION["authId"];
					$data["chart_procedure_id"] = $elem_chart_procedures_id;

					//get edit id --

					$tmp = $oPnRep->getChartProcEditId($elem_chart_procedures_id,0," pn_rep_id ");
					if(!empty($tmp["pn_rep_id"])){
						$elem_edit_id=$tmp["pn_rep_id"];
						$elem_edit_mode = "edit";
					}

					//get edit id --

					/*------signing op note if operator is physician---*/
					/*
					if(ereg('<div title="replacePhySig">',$data["txtData"])){$phy_hold_sig = true;}else{$phy_hold_sig = false;}
					if($phy_hold_sig && $user_type==1){

					}
					*/
					/*------signing op note if operator is physician---*/

					$qry =imw_query("SELECT id, opnote_id, signed FROM `consent_hold_sign` WHERE  opnote_id='".$elem_edit_id."' AND signed!='0'");
					$qryRes = imw_fetch_assoc($qry);
					if(imw_num_rows($qry)>0){
						$consentholdId = $qryRes['id'];
						$consentholdSigned = $qryRes['signed'];
					}

					$checkRecordVal = false;
					//Save
					if($elem_edit_mode == "new" || $consentholdSigned){
						//Insert
						$elem_edit_id = $oPnRep->insertRecord($data);
					}else{
						//Update
						$err = $oPnRep->updateRecord($elem_edit_id, $data);
						$checkRecordVal=true;
						$updateDelStatus = imw_query("UPDATE `consent_hold_sign` SET del_status=1 WHERE opnote_id='".$elem_edit_id."'");
					}

					 /*-----SAVING HOLD SIGNATURE INFO-----*/
					 if($elem_edit_mode == "new" || $consentholdSigned || $checkRecordVal== true){
						$OBJhold_sign = new CLSHoldDocument;
						$OBJhold_sign->section_col = "opnote_id";
						$OBJhold_sign->section_col_value = $elem_edit_id;
						$OBJhold_sign->save_hold_sign();
					 }
					 /*-----end of HOLD SIGNATURE INFO-----------*/
				}
			}

			//-- OP Note --

			// -- IOP --

			if(!empty($iop_od) || !empty($iop_os)){
				if(!empty($form_id) && (empty($elem_finalize_flag) || !empty($elem_isReviewable))){
					$oIopGonioSaver = new IopGonioSaver($patient_id, $form_id);
					$js_reloadIOP=$oIopGonioSaver->addNewIOPMain($elem_finalize_flag,$iop_type,$iop_od,$iop_os,$iop_time);
				}
			}

			// -- IOP --

			//-- Bottox ---

			if(!empty($elem_chart_procedures_id) && !empty($elem_bottox_open_flg)){

				$oSaveFile=new SaveFile();
				$btx_total = sqlEscStr($_POST["elem_botox_total"]);
				$btx_usd = sqlEscStr($_POST["elem_botox_used"]);
				$btx_wstd = sqlEscStr($_POST["elem_botox_wasted"]);
				$lot = sqlEscStr($_POST["elem_botox_lot"]);
				$lot_expr_dt = wv_formatDate($_POST["elem_lot_expr_dt"], 0, 0, 'insert');

				$vis_sc_od = sqlEscStr($_POST["elem_botox_sc_od"]);
				$vis_cc_od = sqlEscStr($_POST["elem_botox_cc_od"]);
				$vis_othr_od = sqlEscStr($_POST["elem_botox_other_od"]);

				$vis_sc_os = sqlEscStr($_POST["elem_botox_sc_os"]);
				$vis_cc_os = sqlEscStr($_POST["elem_botox_cc_os"]);
				$vis_othr_od = sqlEscStr($_POST["elem_botox_other_os"]);

				$rd_injctn = sqlEscStr($_POST["elem_botox_inject_radio"]);
				$rbdcs = sqlEscStr($_POST["elem_botox_rbdcs"]);
				$type_btx=$_POST["elem_typeBtx"];
				$drw_img = $_POST["elem_cnvs_bottox_drw_img"];
				$drw_img_dim = sqlEscStr($_POST["elem_cnvs_bottox_drw_img_dim"]);
				$ar_drw_img_dim=explode("X", $drw_img_dim);
				$img_dim_w=$ar_drw_img_dim[0];
				$img_dim_h=$ar_drw_img_dim[1];

				if(!empty($_POST["elem_cnvs_bottox_drw"])){
					$drw_path ="";
					$imagePath= $oSaveFile->getUploadDirPath(); //dirname(__FILE__)."/../main/uploaddir";
					$patientDir = "/PatientId_".$patient_id."";
					$idocDrawingDirName = "/proc_botox";
					if(is_dir($imagePath.$patientDir.$idocDrawingDirName) == false){
						mkdir($imagePath.$patientDir.$idocDrawingDirName, 0777, true);
					}

					$canvasImgData = str_replace("data:image/png;base64,","",$_POST["elem_cnvs_bottox_drw"]);
					$drawingFileName = "/drw_procid_".$elem_chart_procedures_id.".png";
					$drawingFilePath = $imagePath.$patientDir.$idocDrawingDirName.$drawingFileName;

					$chkOp_tmp = file_put_contents($drawingFilePath, base64_decode($canvasImgData));
					$backImg=$oSaveFile->getFilePath($drw_img,"w2i");

					$bakImgResource = (strpos($backImg,".png")!==false) ? imagecreatefrompng($backImg): imagecreatefromjpeg($backImg);
					$canvasImgResource = imagecreatefrompng($drawingFilePath);



					imagecopy($bakImgResource, $canvasImgResource, 0, 0, 0, 0, $img_dim_w, $img_dim_h);
					//imagepng($bakImgResource, $drawingFilePath);

					imagepng($bakImgResource, $drawingFilePath);
					if(file_exists($drawingFilePath)){
						$drw_path = sqlEscStr($patientDir.$idocDrawingDirName.$drawingFileName);

						//create a thumbnail--
						$drawingFilePath_s = str_replace(".png", "_s.png",$drawingFilePath);

						$dntmp = $oSaveFile->createThumbs($drawingFilePath,$drawingFilePath_s,$thumbWidth="75",$thumbHeight="75");
						//create a thumbnail--
					}

				}
				$drw_coords = sqlEscStr($_POST["elem_cnvs_bottox_drw_coords"]);

				$sql = "SELECT id FROM chart_procedures_botox WHERE chart_proc_id = '".$elem_chart_procedures_id."' ";
				$row=sqlQuery($sql);
				if($row!=false){
					//update
					$sql = "UPDATE chart_procedures_botox SET ";
					$sqlW = " WHERE chart_proc_id = '".$elem_chart_procedures_id."' ";
				}else{
					//insert
					$sql = "INSERT INTO chart_procedures_botox SET chart_proc_id='".$elem_chart_procedures_id."', ";
					$sqlW = "";
				}

				//test--
				$sql.="  btx_total='".$btx_total."', btx_usd='".$btx_usd."', btx_wstd='".$btx_wstd."', lot='".$lot."',
							vis_sc_od='".$vis_sc_od."', vis_cc_od='".$vis_cc_od."', vis_othr_od='".$vis_othr_od."',
							vis_sc_os='".$vis_sc_os."', vis_cc_os='".$vis_cc_os."', vis_othr_os='".$vis_othr_od."',
							rd_injctn='".$rd_injctn."', drw_path='".$drw_path."', drw_coords='".$drw_coords."', rbdcs='".$rbdcs."',
							type_btx='".$type_btx."', drw_img='".sqlEscStr($drw_img)."',  drw_img_dim='".$drw_img_dim."',
							lot_expr_dt='".$lot_expr_dt."' ";
				$sql.=$sqlW;
				$row = sqlQuery($sql);

			}else if(!empty($elem_chart_procedures_id)){
				$sql = "SELECT id FROM chart_procedures_botox WHERE chart_proc_id = '".$elem_chart_procedures_id."' ";
				$row=sqlQuery($sql);
				if($row!=false){
					//Delete
					$sql = "DELETE FROM chart_procedures_botox ";
					$sql .= " WHERE chart_proc_id = '".$elem_chart_procedures_id."' ";
					$row = sqlQuery($sql);
				}
			}
			//-- Bottox ---

			// -- LASIK ---
			$elem_lasik_open_flg=$_POST["elem_lasik_open_flg"];
			if(!empty($elem_chart_procedures_id) && !empty($elem_lasik_open_flg)){


				$pre_op_tech=$_POST["el_pre_op_tech"];
				$ins_punc_plg=$_POST["el_ins_punc_plg"];

				$lasik_allergies=$_POST["el_lasik_allergies"];
				$monovis=$_POST["el_monovis"];
				$consent_review=$_POST["el_consent_review"];
				$bedatine=$_POST["el_bedatine"];
				$near_eye=$_POST["el_near_eye"];
				$lasik_pachy=$_POST["el_lasik_pachy"];
				$repeat_orbs=$_POST["el_repeat_orbs"];
				$lasik_acular=$_POST["el_lasik_acular"];
				$pre_antibiotic=$_POST["el_pre_antibiotic"];
				$lasik_alcaine=$_POST["el_lasik_alcaine"];
				$lasik_xanax=$_POST["el_lasik_xanax"];

				$pre_op_checks="";
				if(!empty($ins_punc_plg)){ $pre_op_checks.=$ins_punc_plg."!,!"; }
				if(!empty($monovis)){ $pre_op_checks.=$monovis."!,!";  }
				if(!empty($consent_review)){ $pre_op_checks.=$consent_review."!,!";  }
				if(!empty($bedatine)){ $pre_op_checks.=$bedatine."!,!";  }
				if(!empty($lasik_pachy)){ $pre_op_checks.=$lasik_pachy."!,!";  }
				if(!empty($repeat_orbs)){ $pre_op_checks.=$repeat_orbs."!,!";  }
				if(!empty($lasik_acular)){ $pre_op_checks.=$lasik_acular."!,!";  }
				if(!empty($pre_antibiotic)){ $pre_op_checks.=$pre_antibiotic."!,!";  }
				if(!empty($lasik_alcaine)){ $pre_op_checks.=$lasik_alcaine."!,!";  }


				$el_lasik_modifier_lasik=$_POST["el_lasik_modifier_lasik"];
				$el_lasik_modifier_prk=$_POST["el_lasik_modifier_prk"];
				$el_lasik_modifier_opti=$_POST["el_lasik_modifier_opti"];
				$el_lasik_modifier_topo_guided=$_POST["el_lasik_modifier_topo_guided"];
				$el_lasik_modifier_enh=$_POST["el_lasik_modifier_enh"];
				$el_lasik_modifier_flap_lift=$_POST["el_lasik_modifier_flap_lift"];

				$lasik_modifier="";
				if(!empty($el_lasik_modifier_lasik)){ $lasik_modifier.=$el_lasik_modifier_lasik."!,!"; }
				if(!empty($el_lasik_modifier_prk)){ $lasik_modifier.=$el_lasik_modifier_prk."!,!"; }
				if(!empty($el_lasik_modifier_opti)){ $lasik_modifier.=$el_lasik_modifier_opti."!,!"; }
				if(!empty($el_lasik_modifier_topo_guided)){ $lasik_modifier.=$el_lasik_modifier_topo_guided."!,!"; }
				if(!empty($el_lasik_modifier_enh)){ $lasik_modifier.=$el_lasik_modifier_enh."!,!"; }
				if(!empty($el_lasik_modifier_flap_lift)){ $lasik_modifier.=$el_lasik_modifier_flap_lift."!,!"; }


				$el_lasik_modifier_eye=$_POST["el_lasik_modifier_eye"];


				$el_vpv_name=$_POST["el_vpv_name"];
				$el_vpv_name_srg=$_POST["el_vpv_name_srg"];
				$el_vpv_name_tech=$_POST["el_vpv_name_tech"];
				$el_vpv_name_time=$_POST["el_vpv_name_time"];

				$el_vpv_dob=$_POST["el_vpv_dob"];
				$el_vpv_dob_srg=$_POST["el_vpv_dob_srg"];
				$el_vpv_dob_tech=$_POST["el_vpv_dob_tech"];
				$el_vpv_dob_time=$_POST["el_vpv_dob_time"];

				$el_vpv_proc_site=$_POST["el_vpv_proc_site"];
				$el_vpv_proc_site_srg=$_POST["el_vpv_proc_site_srg"];
				$el_vpv_proc_site_tech=$_POST["el_vpv_proc_site_tech"];
				$el_vpv_proc_site_time=$_POST["el_vpv_proc_site_time"];

				$el_vpv_proc_type=$_POST["el_vpv_proc_type"];
				$el_vpv_proc_type_srg=$_POST["el_vpv_proc_type_srg"];
				$el_vpv_proc_type_tech=$_POST["el_vpv_proc_type_tech"];
				$el_vpv_proc_type_time=$_POST["el_vpv_proc_type_time"];

				$arr_pt_verify=array();
				if(!empty($el_vpv_name)){ $arr_pt_verify[] = array($el_vpv_name,$el_vpv_name_srg,$el_vpv_name_tech,$el_vpv_name_time );  }
				if(!empty($el_vpv_dob)){ $arr_pt_verify[] = array($el_vpv_dob,$el_vpv_dob_srg,$el_vpv_dob_tech,$el_vpv_dob_time );  }
				if(!empty($el_vpv_proc_site)){ $arr_pt_verify[] = array($el_vpv_proc_site,$el_vpv_proc_site_srg,$el_vpv_proc_site_tech,$el_vpv_proc_site_time );  }
				if(!empty($el_vpv_proc_type)){ $arr_pt_verify[] = array($el_vpv_proc_type,$el_vpv_proc_type_srg,$el_vpv_proc_type_tech,$el_vpv_proc_type_time );  }

				$el_dos_mr_od=$_POST["el_dos_mr_od"];
				$el_dos_mr_os=$_POST["el_dos_mr_os"];

				$el_post_op_target_od_plano=$_POST["el_post_op_target_od_plano"];
				$el_post_op_target_od=$_POST["el_post_op_target_od"];

				$el_post_op_target_os_plano=$_POST["el_post_op_target_os_plano"];
				$el_post_op_target_os=$_POST["el_post_op_target_os"];

				if(!empty($el_post_op_target_od_plano)){$el_post_op_target_od.="!,!".$el_post_op_target_od_plano;}
				if(!empty($el_post_op_target_os_plano)){$el_post_op_target_os.="!,!".$el_post_op_target_os_plano;}


				$el_avg_k_axis_od=$_POST["el_avg_k_axis_od"];
				$el_avg_k_axis_os=$_POST["el_avg_k_axis_os"];

				$el_treatment1_od=$_POST["el_treatment1_od"];
				$el_treatment1_os=$_POST["el_treatment1_os"];

				$el_lasik_pachy_od=$_POST["el_lasik_pachy_od"];
				$el_lasik_pachy_os=$_POST["el_lasik_pachy_os"];

				$el_flap_thick_od=$_POST["el_flap_thick_od"];
				$el_flap_thick_os=$_POST["el_flap_thick_os"];

				$el_stromal_bed_od=$_POST["el_stromal_bed_od"];
				$el_stromal_bed_os=$_POST["el_stromal_bed_os"];

				$el_hansatome_od=$_POST["el_hansatome_od"];
				$el_hansatome_os=$_POST["el_hansatome_os"];

				$el_keratome_ring_od=$_POST["el_keratome_ring_od"];
				$el_keratome_plate_od=$_POST["el_keratome_plate_od"];

				$el_keratome_ring_os=$_POST["el_keratome_ring_os"];
				$el_keratome_plate_os=$_POST["el_keratome_plate_os"];

				$keratome_od = $keratome_os = "";
				$keratome_od =$el_hansatome_od."!,!".$el_keratome_ring_od."!,!".$el_keratome_plate_od;
				$keratome_os =$el_hansatome_os."!,!".$el_keratome_ring_os."!,!".$el_keratome_plate_os;

				$el_risks_benefits=$_POST["el_risks_benefits"];

				$el_surgeon_sign=$_POST["el_surgeon_sign"];
				if(!empty($el_surgeon_sign)){
				$el_surgeon_sign_id_name=$_POST["el_surgeon_sign_id_name"];
				$el_surgeon_sign_id=$_POST["el_surgeon_sign_id"];
				$el_surgeon_sign_dos=wv_formatDate($_POST["el_surgeon_sign_dos"],0,0,"insert");
				}

				$el_abrasion=$_POST["el_abrasion"];
				$el_bcl=$_POST["el_bcl"];
				$el_post_op_type=$_POST["el_post_op_type"];

				$el_zymaxid=$_POST["el_zymaxid"];
				$el_pred_forte=$_POST["el_pred_forte"];
				$el_postop_lasik_acular=$_POST["el_postop_lasik_acular"];
				$el_lasik_omnipred=$_POST["el_lasik_omnipred"];
				$el_polytrim=$_POST["el_polytrim"];
				$el_lotemax=$_POST["el_lotemax"];
				$el_prolensa=$_POST["el_prolensa"];

				$drops="";
				if(!empty($el_zymaxid)){ $drops.=$el_zymaxid."!,!"; }
				if(!empty($el_pred_forte)){ $drops.=$el_pred_forte."!,!"; }
				if(!empty($el_postop_lasik_acular)){ $drops.=$el_postop_lasik_acular."!,!"; }
				if(!empty($el_lasik_omnipred)){ $drops.=$el_lasik_omnipred."!,!"; }

				if(!empty($el_polytrim)){ $drops.=$el_polytrim."!,!"; }
				if(!empty($el_lotemax)){ $drops.=$el_lotemax."!,!"; }
				if(!empty($el_prolensa)){ $drops.=$el_prolensa."!,!"; }

				$el_temperature=$_POST["el_temperature"];
				$el_humidity=$_POST["el_humidity"];

				$el_post_op_surgeon=$_POST["el_post_op_surgeon"];
				$el_laser_oprtr=$_POST["el_laser_oprtr"];
				$el_keratome_tech=$_POST["el_keratome_tech"];

				$el_cornea_check_od_Ex=$_POST["el_cornea_check_od_Ex"];
				$el_cornea_check_os_Ex=$_POST["el_cornea_check_os_Ex"];

				$el_plugs_inserted=$_POST["el_plugs_inserted"];

				$el_plugs_inserted_eye=$_POST["el_plugs_inserted_eye"];
				$el_plugs_inserted_size=$_POST["el_plugs_inserted_size"];

				$el_post_op_kit_given=$_POST["el_post_op_kit_given"];
				$el_post_op_tech=$_POST["el_post_op_tech"];

				$el_lasik_comments=$_POST["el_lasik_comments"];


				$sql_con ="  pre_op_tech='".$pre_op_tech."',	near_eye='".$near_eye."', allergies='".sqlEscStr($lasik_allergies)."', xanax='".sqlEscStr($lasik_xanax)."',
						pre_op_checks='".sqlEscStr($pre_op_checks)."', lasik_modifier='".sqlEscStr($lasik_modifier)."', lasik_eye='".$el_lasik_modifier_eye."',
						dos_mr_od='".sqlEscStr($el_dos_mr_od)."',
						dos_mr_os='".sqlEscStr($el_dos_mr_os)."', post_op_target_od='".sqlEscStr($el_post_op_target_od)."',
						post_op_target_os='".sqlEscStr($el_post_op_target_os)."',
						avg_k_axis_od='".sqlEscStr($el_avg_k_axis_od)."',
						avg_k_axis_os='".sqlEscStr($el_avg_k_axis_os)."', treatment1_od='".sqlEscStr($el_treatment1_od)."', treatment1_os='".sqlEscStr($el_treatment1_os)."',
						pachy_od='".sqlEscStr($el_lasik_pachy_od)."',
						pachy_os='".sqlEscStr($el_lasik_pachy_os)."', flap_thick_od='".sqlEscStr($el_flap_thick_od)."', flap_thick_os='".sqlEscStr($el_flap_thick_os)."',
						stromal_bed_od='".sqlEscStr($el_stromal_bed_od)."',
						stromal_bed_os='".sqlEscStr($el_stromal_bed_os)."', keratome_od='".sqlEscStr($keratome_od)."', keratome_os='".sqlEscStr($keratome_os)."',
						risks_benefits='".$el_risks_benefits."',
						surgeon_sign='".$el_surgeon_sign."',  surgeon_sign_dos='".$el_surgeon_sign_dos."',
						abrasion='".$el_abrasion."', bcl='".$el_bcl."', post_op_type='".sqlEscStr($el_post_op_type)."', drops='".sqlEscStr($drops)."',
						temperature='".sqlEscStr($el_temperature)."', humidity='".sqlEscStr($el_humidity)."', keratome_tech='".$el_keratome_tech."', laser_operator='".$el_laser_oprtr."',
						post_op_surgeon='".$el_post_op_surgeon."', cornea_check_od='".$el_cornea_check_od_Ex."', cornea_check_os='".$el_cornea_check_os_Ex."',
						plugs_inserted='".$el_plugs_inserted."',
						plugs_inserted_eye='".$el_plugs_inserted_eye."', plugs_inserted_size='".sqlEscStr($el_plugs_inserted_size)."', post_op_kit_given='".$el_post_op_kit_given."',
						post_op_tech='".$el_post_op_tech."',
						comments='".sqlEscStr($el_lasik_comments)."'
						"; //sqlEscStr($drw_img)

				$proc_lasik_id=0;
				$sql = "SELECT id FROM chart_proc_lasik WHERE chart_proc_id = '".$elem_chart_procedures_id."' AND del_by='0' ";
				$row=sqlQuery($sql);
				if($row!=false){
					//update
					$proc_lasik_id =$row["id"];
					$sql = "UPDATE chart_proc_lasik SET ".$sql_con;
					$sqlW = " WHERE chart_proc_id = '".$elem_chart_procedures_id."' AND del_by='0'  ";
					$sql.=$sqlW;
					$row = sqlQuery($sql);
				}else{
					//insert
					$sql = "INSERT INTO chart_proc_lasik SET chart_proc_id='".$elem_chart_procedures_id."', ".$sql_con;
					$sqlW = "";
					$sql.=$sqlW;
					$proc_lasik_id = sqlInsert($sql);
				}

				//
				if(!empty($proc_lasik_id) && count($arr_pt_verify)>0){
					foreach($arr_pt_verify as $k => $v){
						$titem = $v[0];
						$t_ptv_surg= $v[1];
						$t_ptv_tech= $v[2];
						$t_ptv_dt= $v[3];

						if(!empty($titem)){
							if($titem=="Mono" || $titem=="DVO"){ $titem_phr=" 'Mono','DVO' "; }else{ $titem_phr=" '".$titem."' "; }
						}

						$sql_con = " item='".$titem."', surgeon='".$t_ptv_surg."', tech='".$t_ptv_tech."', veri_time='".$t_ptv_dt."' ";

						$sql = "SELECT id FROM chart_lasik_pt_verify WHERE 	id_chart_proc_lasik= '".$proc_lasik_id."' AND item IN ( ".$titem_phr." )   ";
						$row = sqlQuery($sql);
						if($row!=false){
							$sql = "UPDATE chart_lasik_pt_verify SET ".$sql_con." WHERE id='".$row["id"]."' ";
							$row = sqlQuery($sql);
						}else{
							$sql = "INSERT INTO chart_lasik_pt_verify SET id_chart_proc_lasik='".$proc_lasik_id."', ".$sql_con." ";
							$row = sqlQuery($sql);
						}
					}
				}

				//Set Ocu, Hobby
				$this->pt_occu_hoby("set", $_POST["elem_pt_occu"], $_POST["elem_pt_hobby"] );

			}else if(!empty($elem_chart_procedures_id)){
				$sql = "SELECT id FROM chart_proc_lasik WHERE chart_proc_id = '".$elem_chart_procedures_id."' AND del_by='0'  ";
				$row=sqlQuery($sql);
				if($row!=false){
					//Delete
					$sql = "UPDATE chart_proc_lasik SET del_by='".$_SESSION["authId"]."', del_time='".wv_dt("now")."' ";
					$sql .= " WHERE chart_proc_id = '".$elem_chart_procedures_id."' AND del_by='0'  ";
					$row = sqlQuery($sql);
				}
			}
			// -- LASIK ---

			if(constant("connect_optical")==1){
				$oMedHx = new MedHx($patient_id);
				$oMedHx->setFormId($form_id);
				$oMedHx->optical_order_action();
			}

			//--Consent Form --
			if(!empty($consent_form_id)){
				$data = array();
				$chart_procedure_id = $elem_chart_procedures_id;
				$chart_procedure_timestamp = $elem_chart_DOS;

				//list($tyear, $tmonth, $tday ) = explode('-', $elem_chart_DOS);
				//$chart_procedure_timestamp = mktime(0, 0, 0, $tmonth, $tday, $tyear);

				//get information_id
				$information_id_2="";
				$sql="SELECT form_information_id FROM `patient_consent_form_information` WHERE `chart_procedure_id`='".$chart_procedure_id."' ";
				$row=sqlQuery($sql);
				if($row != false)
				{
					$information_id_2=$row["form_information_id"];
				}
				//$content_save = 'save_form';
				extract($_POST);
				include($GLOBALS['incdir']."/patient_info/consent_forms/consentFormDetails_save.php");
			}
			//--Consent Form --

		} //End Block save

		//print --
		$stropnprintwin="";
		$htmlFileName="print_proc_".time();
		if(!empty($_POST["elem_hidPrint"])){

			$tmp_con=$this->print_procedure_note($elem_chart_procedures_id);

			$oPrinter = new Printer($patient_id,$form_id);
			$stropnprintwin = $oPrinter->print_page($tmp_con, "Print Procedures","print_procedures","",1);

		}
		//print --
		//return
		$arr=array();
		$arr["up_iop_summery"] = (!empty($js_reloadIOP)) ? "1" : "0";
		$arr["print_pdf"] = $stropnprintwin;
		echo json_encode($arr);
	}

	function save_amend(){
		$patient_id = $this->pid;
		$elem_amndmnt = sqlEscStr($_POST["elem_amndmnt"]);
		$hid_pa_sign = $_POST["hid_pa_sign"];
		$hid_fin = $_POST["hid_fin"];
		$elem_chart_procedures_id = $_POST["elem_chart_procedures_id"];

		$sql = "SELECT Finalized_by FROM chart_procedures where id = '".$elem_chart_procedures_id."' AND deleted_by='0' AND patient_id='".$patient_id."' ";
		$rs = sqlQuery($sql);
		if($rs!=false && !empty($rs["Finalized_by"])){
			if(!empty($hid_fin)){
				$final_on=wv_dt("now");
				$final_by=$_SESSION["authId"];
			}

			if(!empty($hid_pa_sign)){
				$sign_on=wv_dt("now");
				$sign_by=$_SESSION["authId"];
			}

			$op_dt=wv_dt("now");

			$op_id=$_SESSION["authId"];

			$sql = "SELECT id, sign_by FROM proc_amendments WHERE del_by='0' AND id_chart_procedures='".$elem_chart_procedures_id."' ";
			$rs = sqlQuery($sql);
			if($rs!=false && !empty($rs["id"])){

				if(empty($rs["sign_by"])){
					$phrs_sign = " sign='".$hid_pa_sign."', sign_on='".$sign_on."', sign_by='".$sign_by."', ";
				}else{
					$phrs_sign = "";
				}

				$id = $rs["id"];
				$sql = "UPDATE proc_amendments SET
							amndmnt='".$elem_amndmnt."', final='".$hid_fin."', final_by='".$final_by."', final_on='".$final_on."', ".
							$phrs_sign." op_dt='".$op_dt."', op_id='".$op_id."'
					WHERE id = '".$id."'
				";
				sqlQuery($sql);
			}else{
				$sql = "INSERT INTO proc_amendments (id_chart_procedures, amndmnt, final, final_by, final_on, sign, sign_on, op_dt, op_id, sign_by )
						VALUES ('".$elem_chart_procedures_id."', '".$elem_amndmnt."', '".$hid_fin."', '".$final_by."', '".$final_on."', '".$hid_pa_sign."', '".$sign_on."', '".$op_dt."', '".$op_id."', '".$sign_by."') ";

				sqlQuery($sql);
			}
		}
	}

	//Procedure (botox) print --

	function print_get_procedure_name($id){
		//pending
		$nm="";
		if(!empty($id)){
		$sql = "SELECT procedure_name FROM  operative_procedures WHERE procedure_id='".$id."' ";
		$row=sqlQuery($sql);
		if($row!=false){
			$nm = $row["procedure_name"];
		}
		}
		return $nm;
	}

	function print_procedure_note($proc_id=0){
		$pid=$this->pid;
		$fid=$this->fid;

		$oSaveFile= new SaveFile($pid);
		$up=$oSaveFile->getUploadDirPath(); //dirname(__FILE__)."/../main/uploaddir";

		if(!empty($proc_id)){
			$str_phrase = " AND c1.id='".$proc_id."' ";
		}

		//if(!empty($fid)){
		//	$str_phrase = " AND c1.form_id='".$fid."' ";
		//}else

		//---Query To Get Data For Printing---
		$str_table="";
		$sql="
			SELECT * ,
				DATE_FORMAT(c1.exam_date,'%m-%d-%Y %H:%i:%s') as exam_date,
				c1.cpt_code as cpt_code_c1,
				c1.dx_code as dx_code_c1,
				c1.comments as comments,
				c3.id as lasik_id
			FROM
				chart_procedures c1
				LEFT JOIN chart_procedures_botox c2 ON c2.chart_proc_id = c1.id
				LEFT JOIN chart_proc_lasik c3 ON c3.chart_proc_id = c1.id
				LEFT JOIN proc_amendments c4 ON c4.id_chart_procedures = c1.id
			WHERE
				c1.patient_id='".$pid."' ".$str_phrase."
			AND
				c1.deleted_by='0'
			AND
				(c3.del_by='0' OR c3.del_by IS NULL)  ";

		$rez=sqlStatement($sql);
		for($i=1;$row=sqlFetchArray($rez);$i++){

		if($row!=false){
			extract($row);

			$x_post_op_iop="";
			if(!empty($iop_type)){  $x_post_op_iop.="".$iop_type;  }
			if(!empty($iop_od)){  $x_post_op_iop.="&nbsp;&nbsp;<font color='blue' style='color:blue;'>OD</font>:".$iop_od;  }
			if(!empty($iop_os)){  $x_post_op_iop.="&nbsp;&nbsp;<font color='green' style='color:green;'>OS</font>:".$iop_os;  }
			if(!empty($iop_time)){  $x_post_op_iop.="&nbsp;&nbsp;Time:".$iop_time;  }

			$x_complication="";
			$x_complication.=(!empty($complication)) ? "Yes" : "No";
			if(!empty($complication_desc)){ $x_complication.="&nbsp;&nbsp;".$complication_desc;  }

			$x_Procedure = "";
			$x_Procedure.="".$this->print_get_procedure_name($proc_id);
			if(!empty($site)){
				if($site=="OU"){$site="<font color='purple' style='color:purple;'>OU</font>";}
				elseif($site=="OD"){$site="<font color='blue' style='color:blue;'>OD</font>";}
				elseif($site=="OS"){$site="<font color='green' style='color:green;'>OS</font>";}
				$x_Procedure.="&nbsp;&nbsp;Site:".$site;
			}
			if(!empty($lids_opts)){  $x_Procedure.= "&nbsp;&nbsp;Lids:".$lids_opts; }

			$x_corrProcedure = "";
			$x_corrProcedure.="".$this->print_get_procedure_name($corr_proc_id);
			if(!empty($corr_site)){
				if($corr_site=="OU"){$corr_site="<font color='purple' style='color:purple;'>OU</font>";}
				elseif($corr_site=="OD"){$corr_site="<font color='blue' style='color:blue;'>OD</font>";}
				elseif($corr_site=="OS"){$corr_site="<font color='green' style='color:green;'>OS</font>";}
				$x_corrProcedure.="&nbsp;&nbsp;Correct Site:".$corr_site;
			}
			if(!empty($cor_lids_opts)){  $x_Procedure.= "&nbsp;&nbsp;Correct Lids:".$cor_lids_opts; }


			$x_cpt_code="";
			if(!empty($cpt_code_c1)){  $cpt_code=str_replace("|~|",", ",$cpt_code_c1); $x_cpt_code.="".$cpt_code; }
			if(!empty($type_btx)){  $x_cpt_code.="&nbsp;&nbsp;".$type_btx; }

			$x_start_time="";
			if(!empty($start_time)){ $x_start_time.="".$start_time;  }
			if(!empty($end_time)){ $x_end_time.="&nbsp;&nbsp;End Time:".$end_time;  }

			$x_total="";
			if(!empty($btx_total)){ $x_total.="".$btx_total;  }
			if(!empty($btx_usd)){ $x_total.="&nbsp;&nbsp;Used:".$btx_usd;  }
			if(!empty($btx_wstd)){ $x_total.="&nbsp;&nbsp;Wasted:".$btx_wstd;  }

			$x_vis_od="";
			if(!empty($vis_sc_od)){  $x_vis_od.="&nbsp;&nbsp;SC:".$vis_sc_od; }
			if(!empty($vis_cc_od)){  $x_vis_od.="&nbsp;&nbsp;CC:".$vis_cc_od; }
			if(!empty($vis_othr_od)){  $x_vis_od.="&nbsp;&nbsp;Other:".$vis_othr_od; }

			$x_vis_os="";
			if(!empty($vis_sc_os)){  $x_vis_os.="&nbsp;&nbsp;SC:".$vis_sc_os; }
			if(!empty($vis_cc_os)){  $x_vis_os.="&nbsp;&nbsp;CC:".$vis_cc_os; }
			if(!empty($vis_othr_os)){  $x_vis_os.="&nbsp;&nbsp;Other:".$vis_othr_os; }

			$x_pre_op_meds="";
			if(!empty($pre_op_meds)){  $x_pre_op_meds=str_replace("|~|",", ", $pre_op_meds); }

			$x_intra_meds="";
			if(!empty($intravit_meds)){  $x_intra_meds=str_replace("|~|",", ", $intravit_meds); }

			$x_post_op_meds="";
			if(!empty($post_op_meds)){  $x_post_op_meds=str_replace("|~|",", ", $post_op_meds); }

			$str_table_tmp="";
			if(!empty($exam_date)){ $str_table_tmp.="<tr><td>Procedure DOS:</td><td>".$exam_date."</td></tr>"; }
			if(!empty($x_Procedure)){$str_table_tmp.="<tr><td>Procedure:</td><td>".$x_Procedure."</td></tr>";	}
			if(!empty($x_cpt_code)){$str_table_tmp.="<tr><td>CPT Code:</td><td>".$x_cpt_code."</td></tr>";}

			$lot_expr_dt=(!empty($lot_expr_dt) && strpos($lot_expr_dt,"0000")===false) ? wv_formatDate($lot_expr_dt) : "" ;

			if(!empty($dx_code_c1)){
				$ar_dx_code_c1 = explode(";", $dx_code_c1);
				$dx_code_c1="";
				if(count($ar_dx_code_c1)>0){
					foreach($ar_dx_code_c1 as $kk => $vv){
						$ar_tmp = explode("@~@", $vv);
						$tmp = trim($ar_tmp[0]);
						if(!empty($tmp)){
							$dx_code_c1.=$tmp."<br/>";
						}
					}
				}

				$str_table_tmp.="<tr><td valign=\"top\">Dx Code:</td><td>".$dx_code_c1."</td></tr>";
			}
			if(!empty($x_start_time)){$str_table_tmp.="<tr><td>Start Time:</td><td>".$x_start_time."</td></tr>";			}
			if(!empty($x_post_op_iop)){$str_table_tmp.="<tr><td>Post Op IOP:</td><td>".$x_post_op_iop."</td></tr>";	}
			if(!empty($x_complication)){$str_table_tmp.="<tr><td>Complication:</td><td>".$x_complication."</td></tr>";	}
			if(!empty($cmt)){$str_table_tmp.="<tr><td>CMT:</td><td>".$cmt."</td></tr>";	}
			if(!empty($comments)){$str_table_tmp.="<tr><td>Comments:</td><td width='650'>".$comments."</td></tr>";	}

			if(!empty($x_total)){$str_table_tmp.="<tr><td>Total:</td><td>".$x_total."</td></tr>";}

			if(!empty($lot)){$str_table_tmp.="<tr><td>Lot #:</td><td>".$lot."</td></tr>";}
			if(!empty($lot_expr_dt)){$str_table_tmp.="<tr><td>Expiration Date #:</td><td>".$lot_expr_dt."</td></tr>";}

			if(!empty($x_vis_od)||!empty($x_vis_os)){$str_table_tmp.="<tr><td>Visual:</td><td></td></tr>";}
			if(!empty($x_vis_od)){$str_table_tmp.="<tr><td></td><td><font color='blue' style='color:blue;'>OD</font>&nbsp;&nbsp;".$x_vis_od."</td></tr>";}
			if(!empty($x_vis_os)){$str_table_tmp.="<tr><td></td><td><font color='green' style='color:green;'>OS</font>&nbsp;&nbsp;".$x_vis_os."</td></tr>";}
			if(!empty($rbdcs)){$str_table_tmp.="<tr><td></td><td>R&B Discussed, Consent signed</td></tr>";}
			if(!empty($rd_injctn)){$str_table_tmp.="<tr><td></td><td>".$rd_injctn."</td></tr>";}

			if(!empty($x_pre_op_meds)){$str_table_tmp.="<tr><td>Pre-OP Meds:</td><td>".$x_pre_op_meds."</td></tr>";}
			if(!empty($x_intra_meds)){$str_table_tmp.="<tr><td>Intravitreal Meds:</td><td>".$x_intra_meds."</td></tr>";}
			if(!empty($x_post_op_meds)){$str_table_tmp.="<tr><td>Post - OP Meds:</td><td>".$x_post_op_meds."</td></tr>";}

			if(!empty($timeout)){$str_table_tmp.="<tr><td></td><td>Timeout</td></tr>";}


			if(!empty($x_corrProcedure)){$str_table_tmp.="<tr><td>Correct procedure:</td><td>".$x_corrProcedure."</td></tr>";}
			if(!empty($site_marked)){$str_table_tmp.="<tr><td></td><td>Site marked (visible after prep & draper)</td></tr>";}
			if(!empty($pos_pros_implant)){$str_table_tmp.="<tr><td></td><td>Position, prostheses, implants verified and equipment available if required</td></tr>";}
			if(!empty($consent_completed)){$str_table_tmp.="<tr><td></td><td>Consent completed and signed</td></tr>";}
			if(!empty($providers)){$str_table_tmp.="<tr><td>Providers</td><td>".$providers."</td></tr>";}

			//LASER PROCEDURE NOTE PRINTING WORK STARTS HERE
			$str_table_tmp_laser="";
			if(!empty($laser_procedure_note) || !empty($spot_duration) || !empty($spot_size) || !empty($power) || !empty($shots) || !empty($total_energy) || !empty($exposure) || !empty($count) )
			{
				//HEADING ROW

				$str_table_tmp_laser.="<br><table>";
				//MAIN HEADING
				$str_table_tmp_laser.="<tr><td colspan='8'>Laser Procedure Notes</td></tr>";
				//SUB-HEADINGS
				$str_table_tmp_laser.="<tr>";
				$str_table_tmp_laser.="<td style='width:100px;vertical-align:top;'>Spot Duration</td>";
				$str_table_tmp_laser.="<td style='width:75px;vertical-align:top;'>Spot Size</td>";
				$str_table_tmp_laser.="<td style='width:75px;vertical-align:top;'>Power</td>";
				$str_table_tmp_laser.="<td style='width:75px;vertical-align:top;'># of Shots</td>";
				$str_table_tmp_laser.="<td style='width:100px;vertical-align:top;'>Total Energy</td>";
				$str_table_tmp_laser.="<td style='width:150px;vertical-align:top;'>Degree of Opening</td>";
				$str_table_tmp_laser.="<td style='width:75px;vertical-align:top;'>Exposure</td>";
				$str_table_tmp_laser.="<td style='width:75px;vertical-align:top;'>Count</td>";
				$str_table_tmp_laser.="</tr>";
				//VALUE ROW
				$str_table_tmp_laser.="<tr>";
				if(!empty($spot_duration)){$str_table_tmp_laser.="<td style='width:100px;'>&nbsp;&nbsp;".$spot_duration."</td>";}
				if(!empty($spot_size)){$str_table_tmp_laser.="<td style='width:75px;'>&nbsp;&nbsp;".$spot_size."</td>";}
				if(!empty($power)){$str_table_tmp_laser.="<td style='width:75px;'>&nbsp;".$power."</td>";}
				if(!empty($shots)){$str_table_tmp_laser.="<td style='width:75px;'>&nbsp;&nbsp;".$shots."</td>";}
				if(!empty($total_energy)){$str_table_tmp_laser.="<td style='width:100px;'>&nbsp;&nbsp;".$total_energy."</td>";}
				if(!empty($degree_of_opening)){$str_table_tmp_laser.="<td style='width:150px;'>&nbsp;&nbsp;".$degree_of_opening."</td>";}
				if(!empty($exposure)){$str_table_tmp_laser.="<td style='width:75px;'>&nbsp;&nbsp;".$exposure."</td>";}
				if(!empty($count)){$str_table_tmp_laser.="<td style='width:75px;'>&nbsp;&nbsp;".$count."</td>";}
				$str_table_tmp_laser.="</tr>";
				$str_table_tmp_laser.="</table>";
			}

			//LASIK --
			if(!empty($lasik_id)){

			$str_table_tmp_lasik="";
			if(!empty($pre_op_tech)){
				$tmpu = new User($pre_op_tech);
				$str_table_tmp_lasik.="<tr><td colspan=\"2\">Pre-op tech: ".$tmpu->getName()."</td></tr>";
			}
			if(!empty($allergies)){
				$str_table_tmp_lasik.="<tr><td colspan=\"2\">Allergies: ".$allergies."</td></tr>";
			}
			if(!empty($xanax)){
				$str_table_tmp_lasik.="<tr><td colspan=\"2\">XANAX/VALIUM 5 MG @: ".$xanax."</td></tr>";
			}
			if(!empty($near_eye)){
				$str_table_tmp_lasik.="<tr><td colspan=\"2\">NEAR EYE: ".$near_eye."</td></tr>";
			}
			if(!empty($pre_op_checks)){
				$pre_op_checks = str_replace("!,!", ", ", $pre_op_checks); $pre_op_checks=trim($pre_op_checks); $pre_op_checks=trim($pre_op_checks, ",");
				$str_table_tmp_lasik.="<tr><td colspan=\"2\">".$pre_op_checks."</td></tr>";
			}

			if(!empty($lasik_modifier)){
				$lasik_modifier = str_replace("!,!", ", ", $lasik_modifier); $lasik_modifier=trim($lasik_modifier); $lasik_modifier=trim($lasik_modifier, ",");
				if(!empty($lasik_eye)){$lasik_modifier .= "&nbsp;-&nbsp;".$lasik_eye;}
				$str_table_tmp_lasik.="<tr><td colspan=\"2\">".$lasik_modifier."</td></tr>";
			}

			//--
			$str_table_tmp_lasik_vpv="";
			$sql = "SELECT * FROM chart_lasik_pt_verify WHERE id_chart_proc_lasik='".$proc_id."' ";
			$rez1 = sqlStatement($sql);
			for($i=0; $row1=sqlFetchArray($rez1);$i++){
				$titem = $row1["item"];
				$tsurgeon = $row1["surgeon"];
				$ttech = $row1["tech"];
				$tveri_time = $row1["veri_time"];

				if(!empty($tsurgeon)){
					$tmpu = new User($tsurgeon);
					$tsurgeon = $tmpu->getName();
				}

				if(!empty($ttech)){
					$tmpu = new User($ttech);
					$ttech = $tmpu->getName();
				}

				$str_table_tmp_lasik_vpv .= "<tr><td>".$titem."</td><td>".$tsurgeon."</td><td>".$ttech."</td><td>".$tveri_time."</td></tr>";
			}

			if(!empty($str_table_tmp_lasik_vpv)){
				$str_table_tmp_lasik_vpv="<table><tr><td colspan=\"4\">Verbal Patient Verification</td></tr>".$str_table_tmp_lasik_vpv."</table>";
				$str_table_tmp_lasik.="<tr><td colspan=\"2\">".$str_table_tmp_lasik_vpv."</td></tr>";
			}
			//--


			$str_table_tmp_lasik_mid="";

			$str_table_tmp_lasik_mid .= "<tr><td></td><td>Right eye</td><td>Left eye</td></tr>";

			if(!empty($dos_mr_od) || !empty($dos_mr_os)){
			$str_table_tmp_lasik_mid .= "<tr><td>D.O.S MR</td><td>".$dos_mr_od."</td><td>".$dos_mr_os."</td></tr>";
			}

			if(!empty($post_op_target_od) || !empty($post_op_target_os)){
			$post_op_target_od = str_replace("!,!", ", ", $post_op_target_od);
			$post_op_target_os = str_replace("!,!", ", ", $post_op_target_os);
			$str_table_tmp_lasik_mid .= "<tr><td>Post-op target</td><td>".$post_op_target_od."</td><td>".$post_op_target_os."</td></tr>";
			}

			if(!empty($avg_k_axis_od) || !empty($avg_k_axis_os)){
			$str_table_tmp_lasik_mid .= "<tr><td>Avg K or Axis</td><td>".$avg_k_axis_od."</td><td>".$avg_k_axis_os."</td></tr>";
			}
			if(!empty($treatment1_od) || !empty($treatment1_os)){
			$str_table_tmp_lasik_mid .= "<tr><td>Treatment 1</td><td>".$treatment1_od."</td><td>".$treatment1_os."</td></tr>";
			}
			if(!empty($pachy_od) || !empty($pachy_os)){
			$str_table_tmp_lasik_mid .= "<tr><td>Pachy</td><td>".$pachy_od."</td><td>".$pachy_os."</td></tr>";
			}
			if(!empty($flap_thick_od) || !empty($flap_thick_os)){
			$str_table_tmp_lasik_mid .= "<tr><td>Flap Thickness</td><td>".$flap_thick_od."</td><td>".$flap_thick_os."</td></tr>";
			}
			if(!empty($stromal_bed_od) || !empty($stromal_bed_os)){
			$str_table_tmp_lasik_mid .= "<tr><td>Stromal Bed</td><td>".$stromal_bed_od."</td><td>".$stromal_bed_os."</td></tr>";
			}

			if(!empty($keratome_od) || !empty($keratome_os)){
			$ar_kera_od = explode("!,!", $keratome_od);
			$ar_kera_os = explode("!,!", $keratome_os);

			$str_table_tmp_lasik_mid .= "<tr><td>Keratome</td><td>HANSATOME # ".$ar_kera_od[0]."</td><td>HANSATOME # ".$ar_kera_os[0]."</td></tr>";
			$str_table_tmp_lasik_mid .= "<tr><td></td><td>RING ".$ar_kera_od[1]." PLATE ".$ar_kera_od[2]."</td><td>RING ".$ar_kera_os[1]." PLATE ".$ar_kera_os[2]."</td></tr>";
			}

			if(!empty($risks_benefits)){
			$str_table_tmp_lasik_mid .= "<tr><td colspan=\"3\">THE RISKS, BENEFITS AND ALTERNATIVES REGARDING LASIK/PRK/ENHANCEMENT/FLAP LIFT WERE DISCUSSED WITH THE PATIENT WHICH INCLUDES BUT IS NOT LIMITED TO INFECTION, BLEEDING, OVER/UNDER CORRECTION, ASTIGMATISM, COSMETIC DEFORMITY, GLARE, HALOS, STARBURST, DIPLOPIA, GLASSES, BIFOCALS, MONOVISION, CO-MANAGEMENT, AK, RK, PRK, INTACS, IOL'S, FDA ETC. PATIENT STATES THEY UNDERSTAND AND STATES THEY DESIRE TO PROCEED.</td></tr>";
			}

			if(!empty($surgeon_sign)){
			$tmpu = new User($surgeon_sign);
			$ar_sign = $tmpu->getSign();
			if(!empty($ar_sign[0])){
			$pth = $up.$ar_sign[0];
			$pth = realpath($pth);
			if(file_exists($pth)){
			$str_table_tmp_lasik_mid .= "<tr><td>SURGEON SIGN.</td><td><img src=\"".$pth."\" alt=\"sign\" width='200'></td><td>".wv_formatDate($surgeon_sign_dos)."</td></tr>";
			}
			}
			}

			if(!empty($str_table_tmp_lasik_mid)){
				$str_table_tmp_lasik.="<tr><td colspan=\"2\"><table>".$str_table_tmp_lasik_mid."</table></td></tr>";
			}

			$tmp="";
			if(!empty($abrasion)){
				$tmp.="ABRASION: ".$abrasion."&nbsp;&nbsp;";
			}

			if(!empty($bcl)){
				$tmp.="BCL: ".$bcl."&nbsp;&nbsp;";
			}

			if(!empty($post_op_type)){
				$tmp.="TYPE: ".$post_op_type."&nbsp;&nbsp;";
			}

			if(!empty($tmp)){
				$str_table_tmp_lasik.="<tr><td colspan=\"2\">".$tmp."</td></tr>";
			}

			if(!empty($drops)){
				$drops = str_replace("!,!", ",", $drops);
				$str_table_tmp_lasik.="<tr><td colspan=\"2\">".$drops."</td></tr>";
			}

			$tmp="";
			if(!empty($temperature)){ $tmp.="TEMP ".$temperature." F&nbsp;&nbsp;"; }
			if(!empty($humidity)){ $tmp.="HUM ".$humidity." %&nbsp;&nbsp;"; }

			if(!empty($keratome_tech) || !empty($laser_operator) || !empty($post_op_surgeon)){
				$tmp.="<br/>";
				if(!empty($keratome_tech)){
					$tmpu = new User($keratome_tech);
					$tmp.="KERATOME TECH: ".$tmpu->getName()."&nbsp;&nbsp;";
				}
				if(!empty($laser_operator)){
					$tmpu = new User($laser_operator);
					$tmp.="LASER OPERATOR: ".$tmpu->getName()."&nbsp;&nbsp;";
				}
				if(!empty($post_op_surgeon)){
					$tmpu = new User($post_op_surgeon);
					$tmp.="SURGEON: ".$tmpu->getName()."&nbsp;&nbsp;";
				}
			}


			if(!empty($cornea_check_od) || !empty($cornea_check_os)){
				$tmp.="<br/>Cornea Check&nbsp;&nbsp;";
				if(!empty($cornea_check_od)){ $tmp.="<font color='blue' style='color:blue;'>OD</font>:EXCELLENT&nbsp;&nbsp;"; }
				if(!empty($cornea_check_os)){ $tmp.="<font color='green' style='color:green;'>OS</font>:EXCELLENT&nbsp;&nbsp;"; }
			}

			if(!empty($plugs_inserted)){
				$tmp.="<br/>PLUGS INSERTED&nbsp;&nbsp;".$plugs_inserted."&nbsp;".$plugs_inserted_eye."&nbsp;Size: ".$plugs_inserted_size;
			}

			if(!empty($post_op_kit_given)){
				$post_op_kit_given = ($post_op_kit_given=="2") ? "No" : "Yes";
				$tmp.="<br/>POST OP INSTRUCTIONS/KIT DISCUSSED, DEMONSTRATED AND GIVEN? ".$post_op_kit_given."";
			}

			if(!empty($post_op_tech)){
				$tmpu = new User($post_op_tech);
				$tmp.="<br/>POST OP TECH: ".$tmpu->getName()."";
			}

			if(!empty($comments)){
				$tmp.="<br/>Comments: ".$comments."";
			}

			if(!empty($tmp)){
				$str_table_tmp_lasik.="<tr><td colspan=\"2\">".$tmp."</td></tr>";
			}

			if(!empty($str_table_tmp_lasik)){
				$str_table_tmp_lasik = "<table><tr><td></td><td></td></tr>".$str_table_tmp_lasik."</table>";
			}

			}

			//LASIK --

			if(!empty($drw_path)){
				$pth = $up.$drw_path;
				$pth = realpath($pth);
				if(file_exists($pth)){
					$str_table_tmp.="<tr><td colspan='2'><img src='".$pth."' width='400'></td></tr>";
				}
			}

			if(!empty($str_table_tmp)){
				$str_table_tmp="<hr/><table>".$str_table_tmp."</table>".$str_table_tmp_laser.$str_table_tmp_lasik;
			}

			$str_table.=$str_table_tmp;

			//Amendments --
			$str_table_tmp="";
			if(!empty($amndmnt)){
				$str_table_tmp.="<tr><td valign='top'>Amendments:</td><td >".nl2br($amndmnt)."</td></tr>";
			}

			if(!empty($sign) && !empty($sign_by)){
				if(!empty($sign)){
					$pth = $up.$sign;
					$pth = realpath($pth);
					if(file_exists($pth)){
						$str_table_tmp.="<tr><td>Signature:</td><td><img src='".$pth."' alt='img'></td></tr>";

						$tmpu = new User($sign_by);
						$str_table_tmp.="<tr><td>Signature By :</td><td>".$tmpu->getName(3)."</td></tr>";

						if(!empty($sign_on)){
							$str_table_tmp.="<tr><td>Signature On:</td><td>".wv_formatDate($sign_on)."</td></tr>";
						}
					}
				}
			}

			if(!empty($final) && !empty($final_by)){
				$tmpu = new User($final_by);
				$str_table_tmp.="<tr><td>Finalize By:</td><td>".$tmpu->getName(3)."</td></tr>";

				if(!empty($final_on)){
					$str_table_tmp.="<tr><td>Finalize On:</td><td>".wv_formatDate($final_on)."</td></tr>";
				}
			}

			if(!empty($str_table_tmp)){
				$str_table_tmp="<hr/><table style='width:100%;'><tr><td colspan='2' style='width:100%' class='tb_dataHeader'>Amendments</td></tr>".$str_table_tmp."</table>";
			}

			$str_table.=$str_table_tmp;

		}

		}

		return $str_table;
	}


	//--

	function get_procedure_dx_codes(){

		//echo "HELLO";
		//print_r($_POST);

		$eye = $_POST["eye"];
		$lids = $_POST["lids"];
		$dx = $_POST["dx"];
		$dx_id = $_POST["dx_id"];
		$odx = new Dx();

		//Add eye + lids in dx codes of procedures
		if(count($dx)>0){
			$tmp=array();
			$tmp_id=array();
			foreach($dx as $k => $v){
				if(!empty($v)){
					//
					if(!empty($eye) || !empty($lids)){	$v = $odx->modifyICDDxCodeWEye($v, $eye, $lids);	}
					$ar_v = explode(",", $v);
					$ar_v = array_map('trim', $ar_v);
					$tmp = array_merge($tmp, $ar_v);

					$ar_v_id = array(trim($dx_id[$k]));
					$ar_v_id = array_map('trim', $ar_v_id);
					$tmp_id = array_merge($tmp_id, $ar_v_id);
				}
			}
			$dx = $tmp;
			$dx_id = $tmp_id;
		}else{ $dx = array(); $dx_id = array(); }

		//check if length of dx and dx_dsc is not equal : Just to ensure that dx_dsc is not linked to wrong dx.
		if(count($dx) != count($dx_id)){	$dx_id = array(); }

		//if dx codes are less then 12 add dx codes from last visit
		if(count($dx)<12){
			$oChartAP =  new ChartAP($this->pid);
			$ar_dx_codes = $oChartAP->getLastVisitDxCodes();
			if(count($ar_dx_codes)>0){
				$tmp=array();
				foreach($ar_dx_codes as $k => $v){
					if(!empty($v)){
						//
						if(!empty($eye) || !empty($lids)){	$v = $odx->modifyICDDxCodeWEye($v, $eye, $lids); }
						$ar_v = explode(",", $v);
						$ar_v = array_map('trim', $ar_v);
						$tmp = array_merge($tmp, $ar_v);
					}
				}
				$ar_dx_codes = $tmp;

				//Check dx codes to highlight: procedure code exists in visit code
				if(count($dx)>0){
					$tmp=array();
					foreach($dx as $k => $v){
						if(!empty($v)){
							//
							$y = $odx->in_array_dx($v, $ar_dx_codes);
							if($y){ $tmp[]=$v; }
						}
					}
					$ar_highlight = $tmp;
				}
				//--

				$dx = array_merge($dx, $ar_dx_codes);
			}
		}

		if(count($dx)>12){	$dx = array_splice($dx, 0, 12); 	}

		$asinfo="";
		if(count($dx)>0){
			$flg_show_pop=0;
			foreach($dx as $k=> $v){ if(strpos($v, "-")!==false){ $flg_show_pop=1; }}

			if(!empty($flg_show_pop)){
				$flg_show_pop=0; //check again
				$key=0;
				foreach($dx as $k=> $v){
					if(empty($v)){ continue; }
					$tmp_dx_id = (isset($dx_id[$k]) && !empty($dx_id[$k])) ? $dx_id[$k] : "";
					$dx_name="";
					$dx_name= $odx->get_dx_desc($v,'icd10_desc', $tmp_dx_id);
					$dx_name = trim($dx_name);
					if(empty($dx_name)){ continue; }
					$inx = ($key+1);
					$key++;
					$asinfo .= "<assess>";
					$asinfo .= "<srno><![CDATA[".($inx)."]]></srno>";
					$asinfo .= "<name indx=\"".($inx)."\"><![CDATA[".htmlentities(stripslashes($dx_name), ENT_QUOTES)."]]></name>";

					//if ICD 10 and dx code not complete then add modifiers
					$strLSS="";
					$strLSS = $odx->icd10_getDxLSS($v);
					if($strLSS!="" && strpos($v,"-")!==false){
						$flagShowPop = 1;
					}
					$catDx="";
					$idDx="";
					if(!empty($tmp_dx_id)){ $idDx=" dxid=\"".$tmp_dx_id."\" ";  }

					$asinfo .= "<dx ".$catDx." ".$idDx." ".$strLSS." ><![CDATA[".htmlentities($v, ENT_QUOTES)."]]></dx>";

					$asinfo .= "</assess>";
				}
			}
		}

		//$ar = array( "dx"=>$dx, "flg_show_pop"=>$flg_show_pop, "ar_highlight"=>$ar_highlight  );

		//--

		//Add Desc
		$dx = $odx->getDxWidthDesc($dx, $dx_id);

		$str_dx = (count($dx) > 0) ? implode("!DX!", $dx) : "";
		$str_dx_hgltd = (count($ar_highlight) > 0) ? implode(",", $ar_highlight) : "";

		$str = "";
		$str .= "<?xml version='1.0' encoding='ISO-8859-1'?>";
		$str .= "<dxinfo>";
		$str .= "<flg_show_pop><![CDATA[".$flg_show_pop."]]></flg_show_pop>";
		$str .= "<ardx><![CDATA[".$str_dx."]]></ardx>";
		$str .= "<arhighlight><![CDATA[".$str_dx_hgltd."]]></arhighlight>";
		$str .= $asinfo; //"<asinfo></asinfo>";
		$str .= "</dxinfo>";
		header('Content-Type: text/xml');
		echo $str;

		//echo json_encode($ar);
	}

	function get_proc_con_frm($proc_id){
		$sql = "SELECT form_information_id FROM patient_consent_form_information where patient_id = '".$this->pid."' AND movedToTrash='0' AND chart_procedure_id='".$proc_id."' ";
		$row = sqlQuery($sql);
		return ($row!=false) ? $row["form_information_id"] : 0 ;
	}

	function get_proc_pn_rep($proc_id){
		$sql = "SELECT pn_rep_id FROM pn_reports where patient_id = '".$this->pid."' AND status='0' AND chart_procedure_id='".$proc_id."'  ";
		$row = sqlQuery($sql);
		return ($row!=false) ? $row["pn_rep_id"] : 0 ;
	}
}
