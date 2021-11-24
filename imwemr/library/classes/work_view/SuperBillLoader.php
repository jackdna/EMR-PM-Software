<?php
class SuperBillLoader extends SuperBill{
	private $oOnload;
	public $fid;
	private $encid, $sb_test;
	public function __construct($pid, $fid=""){
		$this->oOnload =  new Onload();
		parent::__construct($pid);
		$this->fid= (!empty($fid)) ? $fid : 0 ;
		$this->encid=0;
		$this->sb_test="";
	}

	function set_encntr_id($id,$tst=""){
		$tst = trim($tst);
		if(!empty($id)){ $this->encid=$id; }
		if(empty($tst)){ $this->sb_test="Procedures"; }
	}

	function getValidDxCodes4Cpt($cpt){
		if(empty($cpt)){ return "";  }
		$ret = "";
		$sql = "SELECT cpt_fee_tbl.cpt_desc, cpt_fee_tbl.cpt_prac_code,cpt_fee_tbl.cpt4_code, ".
				 " cpt_fee_tbl.mod1, cpt_fee_tbl.mod2, cpt_fee_tbl.mod3, cpt_fee_tbl.units, ".
				 " cpt_fee_tbl.dx_codes ".
				 "FROM cpt_fee_tbl
				 WHERE (	LOWER(REPLACE(cpt_fee_tbl.cpt_prac_code,'\r\n','')) = LOWER('".sqlEscStr($cpt)."') )
				 AND delete_status = '0' ";
		$row = sqlQuery($sql);
		if($row != false){
			//$ar["code"] = $row["cpt_prac_code"];
			if(!empty($row["dx_codes"])) $ret = $row["dx_codes"];
		}

		return $ret;
	}

	function isVIPPt(){
		$vipSuperBill=0;
		$vipRs=sqlQuery("Select vip from patient_data WHERE id= '".$this->pid."'");
		if($vipRs!=false){
		$vipRes= $vipRs;
		$vipSuperBill = $vipRes[0];
		}
		return $vipSuperBill;
	}

	function getOcuMedsPrint($final_flag,$lnAs,$mode=""){

		$pid = $this->pid; //$_SESSION["patient"];
		$oMedHx = new MedHx($pid);
		//Only Unique and lastest Meds. should come in Ocu Grid.
		//Unique by Name + Eye
		$recordExists = 0;
		if($final_flag==0){
			list($arrM) = $oMedHx->getOcularMedication();
			if(count($arrM)>0){
				$recordExists = 1;
				return $recordExists;
			}
		}else{
			if(!empty($this->fid)){
			$qryMed = "SELECT ocularMeds FROM chart_left_provider_issue WHERE patient_id = '".$this->pid."' AND form_id = '".$this->fid."' ";
			$resMed = sqlStatement($qryMed);
			if(imw_num_rows($resMed)>0) {
				$rowMed=sqlFetchArray($resMed);
				$om_c = stripslashes($rowMed["ocularMeds"]);
				$sepOctMeds = "<+OMeds&%+>";
				$arrC = (!empty($om_c)) ? explode($sepOctMeds,$om_c) : array();
			}
			if(is_array($arrC[0]) && count($arrC[0])>0) {
				$recordExists = 1;
				return $recordExists;
			}
			if(!is_array($arrC[0]) || count($arrC)<=0){
				//Get Values from archive database
				$oChart = new ChartNote($this->pid, $this->fid);
				list($arrC,$flg)=$oChart->getArcOcuMedHx();
			}
			$len = count($arrC);
			$strHtml="";
			$j=0;
			for($i=0;$i<$len;$i++){
				$t=trim($arrC[$i]);
				if(!empty($t)){
					$recordExists = 1;
					return $recordExists;
				}
			}
			}
		}
		return $recordExists;
	}

	function getWorkViewSuperbill($sbillbtn = false){

		global $elem_per_vo, $finalize_flag;

		//
		$oCpt = new CPT();
		$oDx = new Dx();
		$oAdmn = new Admn();


		// Check if Pt has Insurance 'Medicare' --
		$flgPtInsMedicare = $this->isPtInsMedicare();
		$oCpt->set_flg_pt_ins($flgPtInsMedicare);

		//
		//Practice Bill Code
		list($practiceBillCode, $sbwarnVisCd, $del_proc_noti) = $oAdmn->getPracticeBillCode(1);
		if(empty($practiceBillCode)){  exit("Please set Billing code in Settings->Billing->Policies->Billing Code"); }

		// Refraction Policy
		$refSetting = $oAdmn->cp_getRefSetting();

		//
		if(!empty($this->encid) || !empty($this->sb_test)){
			$encounterId = $this->encid;
		}else if(isset($_POST["accSB"]) && $_POST["accSB"]=="1"){ // if in accounting section->superbill
			$encounterId = $_SESSION['cn_enc'];
			$sup_wid="style='width:100px;'";
		}else{
			$_SESSION['cn_enc']="";
			$encounterId = $_SESSION["encounter_id"];
			$sup_wid="";
		}
		//
		$enableSB = "";
		if(($elem_per_vo != "1")) {
			$enableSB = "onClick=\"opSuperBill();\" class=\"clickable\" ";
		}
		//Procedures Superbill --
		if(!empty($this->encid) || !empty($this->sb_test)){
			$enableSB = "onClick=\"opProcedureSuperBill();\" class=\"clickable\" ";
		}
		//Procedures Superbill --

		//enc_ICD10
		if(isset($_REQUEST["enc_icd10"])){
			$enc_icd10 = !empty($_REQUEST["enc_icd10"]) ? $_REQUEST["enc_icd10"] : "";
		}else{
			$enc_icd10 = "";
		}

		//Test Super Bill ---
		if(isset($_POST["thisPDiv"])&&!empty($_POST["thisPDiv"])){
			$thisPDiv = $_POST["thisPDiv"];
		}

		if(isset($_POST["sb_testName"])&&!empty($_POST["sb_testName"])){
			$sb_testName = $_POST["sb_testName"];
			$enableSB = "onClick=\"opTestSuperBill();\" class=\"clickable\" ";

			if(isset($_POST["thisCptDescSym"]) && !empty($_POST["thisCptDescSym"])){
				$testCptDesc = $oCpt->checkTest4CptDesc_v2($_POST["thisCptDescSym"]);
				$testCptCode = $oCpt->getProcedureCode($testCptDesc);
			}

			$elem_examDate = $_POST["thisCptDescSym"];
			$caseId = $this->getInuranceCaseId();
			//$coords = $_POST['coords'];

			//Array Menu Options
			//$arrMenuOpts = array("pdiv"=>$thisPDiv);
			//Stoping $thisPDiv in case Tests : after testing, found the menu appear Ok without pDiv
			$thisPDiv="";
			//--

			$encounterId=$_POST["encounterId"];

			// icd10 code in test
			$hid_icd10="";
			if(!empty($_POST["test_form_id"])){ //find from formid test_form_id
				$hid_icd10 = $this->getICD10CodeFromFormId($_POST["test_form_id"]);
			}else{
				$owv = new WorkView();
				$hid_icd10 =$owv->getDefaultICDCode($caseId);
			}
		}

		//Test Super Bill ---

		//--
		//Superbill
		$vipSuperBill=0;
		$isSuperBill=0;
		$sql = "SELECT idSuperBill,procOrder,todaysCharges,insuranceCaseId,vipSuperBill,arr_dx_codes, sup_icd10 FROM superbill ";
		//Check if Test
		//if(isset($sb_testName) && !empty($sb_testName)){

			if(!empty($encounterId) && !empty($this->pid)){
				$sql .= " WHERE encounterId='".$encounterId."' AND patientId = '".$this->pid."' AND del_status='0' ";
			}else{
				$sql = "";
			}

		//}else{
		//	$sql .= "WHERE formId='".$form_id."' ";
		//}

		//Check if sql is not empty
		$row= (!empty($sql)) ? sqlQuery($sql) : false;

		if($row != false){
			$isSuperBill =1;
			$arrCurDxCodesAll = array();
			$arrDxCodesSB = array();
			$elem_idSuperBill = $row["idSuperBill"];
			$elem_procOrder = stripslashes(trim($row["procOrder"]));
			$elem_todaysCharges = number_format($row["todaysCharges"],2);
			$elem_sb_insuranceCaseId = $row["insuranceCaseId"];

			$elem_procOrderTmp = preg_replace("/,$/","",$elem_procOrder);
			$elem_procOrderTmp = str_replace(",","','",$elem_procOrderTmp);
			$elem_procOrderTmp = "'".$elem_procOrderTmp."'";
			$elem_procUnitOrder="";
			$vipSuperBill = $row["vipSuperBill"];
			$all_dx_codes_arr=unserialize($row["arr_dx_codes"]);
			$all_dx_codes_arr_title=array();
			$dx_code_title_arr=array();
			$dx_code_id_arr=array();
			if(is_array($all_dx_codes_arr) && count($all_dx_codes_arr)>0){
				foreach($all_dx_codes_arr as $kdca => $vdca){
					$dx_code_id_arr[$kdca]="";
					$vdca = trim($vdca);
					if(!empty($vdca)){
						$tmp = explode("@*@", $vdca);
						$all_dx_codes_arr[$kdca] = $tmp[0];
						if(!empty($tmp[1])){ $dx_code_id_arr[$kdca] = $tmp[1]; }
					}
				}

				$dx_code_title_arr=$this->get_icd10_desc($all_dx_codes_arr,0, $dx_code_id_arr);
			}

			//Icd10 code
			if(empty($enc_icd10)){
					$enc_icd10 = $row["sup_icd10"];
			}

			//$orderBy = (!empty($elem_procOrder)) ? " FIELD(cptCode,".$elem_procOrderTmp.")" : " description ";
			$orderBy = " porder  ";
			$sql = "SELECT * FROM procedureinfo
					WHERE idSuperBill = '".$elem_idSuperBill."'
					AND delete_status ='0'
					ORDER BY ".$orderBy." ";

			$rez = sqlStatement($sql);
			for($i=1;$row=sqlFetchArray($rez);$i++){
				$cptCode = "elem_cptCode_".$i;
				$$cptCode = $row["cptCode"];
				$procName = "elem_procedureId_".$i;
				$$procName = $row["id"];
				$unitsName = "elem_procUnits_".$i;
				$$unitsName = $row["units"];
				$elem_procUnitOrder.=(!empty($elem_procUnitOrder)) ? ",".$row["units"] : "".$row["units"] ;
				$procDesc = "elem_procedureDesc_".$i;
				$$procDesc = stripslashes($row["procedureName"]);
				$tmpId = $$cptCode."_".$$procName;
				$elem_valid_dx_code4cpt = "valid_dx_code4cpt".$i;
				$$elem_valid_dx_code4cpt = $this->getValidDxCodes4Cpt($row["cptCode"]);

				$arrCurDxCodesAll[$tmpId] = array();
				$strCurDxCodesOpts = $strCurDxCodesOpts_title = "";

				$ar_tmp_dx = array();
				for($k=1;$k<=12;$k++){
					$tmpDx = $row["dx".$j];
					$arrCurDxCodesAll[$tmpId][] = $tmpDx;
					if(!empty($row["dx".$k])){
						$ar_tmp_dx[] = $row["dx".$k];
						if(!in_array($row["dx".$k], $arrDxCodesSB)){
							$arrDxCodesSB[]=$row["dx".$k];
						}
					}
				}

				for($j=1;$j<=12;$j++){
					//$tmpDx = $row["dx".$j];
					//$arrCurDxCodesAll[$tmpId][] = $tmpDx;

					$k=$j;

					if(!empty($all_dx_codes_arr[$j])){
						//echo "<br/>ee".$j." - ".$tmpDx."";
						$tmp_selected_dxcode = (in_array($all_dx_codes_arr[$j], $ar_tmp_dx)) ? "selected" : "";

						//get dx title
						$dxDescTmp_1=$dxDescTmp_1_attr="";
						if(!empty($all_dx_codes_arr[$j])){

							//if dxid exists
							if(!empty($dx_code_id_arr[$j])){								
								$tmp = array_search($dx_code_id_arr[$j], $dx_code_title_arr["id"]);
								if($tmp!==false){
									$dxDescTmp_1 = $dx_code_title_arr["desc"][$tmp];
								}else{
									$dxDescTmp_1 = $oDx->get_dx_desc($all_dx_codes_arr[$j], "icd10_desc", $dx_code_id_arr[$j]);
								}
							}else{
								$dxDescTmp_1 = $oDx->getDxTableInfo($all_dx_codes_arr[$j], $enc_icd10) ;
							}

							if($dxDescTmp_1 == false || empty($dxDescTmp_1)){   $dxDescTmp_1 = "";	}

							$all_dx_codes_arr_title["indx"][$j]=$dxDescTmp_1;
							$dxDescTmp_1_attr = " data-desc=\"".$dxDescTmp_1."\" ";
						}

						$strCurDxCodesOpts .= "<option value=\"".$all_dx_codes_arr[$j]."**".$k."\"  ".$dxDescTmp_1_attr."  ".$tmp_selected_dxcode.">".$all_dx_codes_arr[$j]."</option>";
						if(!empty($tmp_selected_dxcode)){
							$strCurDxCodesOpts_title .= "".$all_dx_codes_arr[$j]." - ".$dxDescTmp_1."\n";
						}
					}

					//echo "<br/>ww".$j." - ".$tmpDx."";

					if($j < 5){
						$mdCode = "elem_modCode_".$i."_".$j;
						$$mdCode = $row["modifier".$j];
					}

					//if(!in_array($row["dx".$j], $arrDxCodesSB) && (!empty($row["dx".$j]))){
					//	$arrDxCodesSB[]=$row["dx".$j];
					//}
					//echo "<br>".$mdCode." : ".$$mdCode;
				}

				//dx
				$tmp_dxAssoc = "elem_dxCodeAssoc_".$i;
				$$tmp_dxAssoc = $strCurDxCodesOpts;
				//dxtitle
				$tmp_dxAssoc = "elem_dxCodeAssoc_".$i."_title";
				$$tmp_dxAssoc = $strCurDxCodesOpts_title;

			}


			//Set Super Len
			$superLen=$i-1;
			$tempLen = count($arrDxCodesSB);

			/*
			$tempLen = ($tempLen >= 4) ? 4 : $tempLen ;

			for($j=1,$i=0;$i<$tempLen;$i++,$j++){
				$varDx = "elem_dxCode_".($j);
				$$varDx = $arrDxCodesSB[$i];
			}
			*/
			//dx opt default
			$strCurDxCodesOpts_default="";
			if(count($all_dx_codes_arr)>0){
				for($i=1;$i<=12;$i++){
					if(!empty($all_dx_codes_arr[$i])){

						//get dx title
						$dxDescTmp_1=$dxDescTmp_1_attr="";
						if(!isset($all_dx_codes_arr_title["indx"][$i]) || empty($all_dx_codes_arr_title["indx"][$i])){
							$dxDescTmp_1 = $oDx->getDxTableInfo($all_dx_codes_arr[$i], $enc_icd10) ;
							if($dxDescTmp_1 == false || empty($dxDescTmp_1)){   $dxDescTmp_1 = "";	}
							$all_dx_codes_arr_title["indx"][$i]=$dxDescTmp_1;
							$dxDescTmp_1_attr = " data-desc=\"".$dxDescTmp_1."\" ";
						}else{
							$dxDescTmp_1=$all_dx_codes_arr_title["indx"][$i];
							$dxDescTmp_1_attr = " data-desc=\"".$dxDescTmp_1."\" ";
						}

						$strCurDxCodesOpts_default .= "<option value=\"".$all_dx_codes_arr[$i]."**".($i)."\" ".$dxDescTmp_1_attr." >".$all_dx_codes_arr[$i]."</option>";
					}
				}
			}

			//posted Charges
			$postedAmt = $this->getPostedCharges($encounterId);
			$strPostedAmt = (!empty($postedAmt) && ($postedAmt != "0.00")) ? "C.Posted: ".$postedAmt : "";
		}
		//check flgIsPtNew
		//--

		//form id
		$cur_form_id = $this->fid;

		//
		$flgIsPtNew = $this->isPatientEstablish($cur_form_id) ? "Establish":"New";

		//------------------------
		$get_lat=sqlStatement("select under,code,title from icd10_laterality where deleted='0' order by under asc,id asc");
		while($row_lat=sqlFetchArray($get_lat)){
			// Laterality = 1 or 2
			// Severity   = 3
			// Staging    = 4 or 5
			$lat_arr[$row_lat['under']][$row_lat['code']]=$row_lat['title'];
		}
		$div_id=0;
		$causative_factors_arr=array("retinal vascular occlusions","diabetes","age related cataract","other cataracts","other disorders of lens","disorders ocular surgery","iol complications","malignant neoplasm of eye","benign neoplasm of eye");
		$get_row=sqlStatement("select * from icd10_data where deleted='0' group by icd10_desc order by group_heading asc,icd10 asc");
		while($get_fet=sqlFetchArray($get_row)){
			if($get_fet['no_bilateral']>0){
				$icd10_bilateral[strtolower($get_fet['icd10'])]=$get_fet['no_bilateral'];
			}

			$icd10_desc_exp=explode(',',$get_fet['icd10_desc']);
			if(strtolower($icd10_desc_exp[0])=="esotropia" || strtolower($icd10_desc_exp[0])=="exotropia" || strtolower($icd10_desc_exp[0])=="congenital"){
				$icd10_charts_all_data[strtolower($icd10_desc_exp[0])][]=strtolower($get_fet['icd10_desc']).">>>".$get_fet['icd10'];
				$icd10_charts_title=ucfirst(strtolower($icd10_desc_exp[0]));
				if(strtolower($icd10_desc_exp[0])=="congenital"){
					$div_id++;
					$icd10_charts_data_heading="<div id=\"dialog-msg-charts\" title=\"$icd10_charts_title\">
						<p>
							<div style=\"float:left; display:inline-block; margin-right:0px;\"><input type=\"hidden\" name=\"dialog-msg-heading\" id=\"dialog-msg-heading\" value=\"$icd10_charts_title\"><b>".$icd10_charts_title."</b></div>
							<div style=\"float:left; display:inline-block; width:89%;\">";
					if($icd10_desc_exp[1]!=""){
						$icd10_charts_str=str_replace("",'',$icd10_desc_exp[1]);
						$icd10_charts_data_arr[$icd10_charts_str]="<div style=\"color:purple; cursor:pointer; float:left; display:inline-block; padding:0px 0px 10px 15px;\">
								<input type='checkbox' id='congenital_".$div_id."' name='congenital_".$div_id."' value='".$get_fet['icd10']."' style=\"display:none;\" class=\"elem_sub_cong\" onclick=\"icd10_charts_popup('0',this,'','');\">
								<input type='hidden' id='congenital_".$div_id."_desc' name='congenital_".$div_id."_desc' value='".$get_fet['icd10_desc']."'>
								<label for='congenital_".$div_id."' style=\"color:purple;cursor:pointer;\" class=\"elem_sub_cong\">".str_replace('OF ','',$icd10_charts_str)."</label>
						</div>";
					}
					$icd10_charts_data_footer="</div></p></div>";
				}else{
					$icd10_charts_data[strtolower($icd10_desc_exp[0])]="<div id=\"dialog-msg-charts\" title=\"$icd10_charts_title\">
						<p>
							<table cellpadding=\"2\" width=\"100%\" >
								<tr><td><input type=\"hidden\" name=\"dialog-msg-heading\" id=\"dialog-msg-heading\" value=\"$icd10_charts_title\"><b>".$icd10_charts_title."</b></td>
									<td style=\"color:purple; cursor:pointer;\">
										<input type='checkbox' id='elem_sub_unspecified' name='elem_sub_unspecified' value='unspecified' class='elem_sub_charts1' style=\"display:none\" onclick=\"icd10_charts_popup('0',this,'','');\">
										<label for=\"elem_sub_unspecified\" style=\"color:purple;cursor:pointer;\">Unspecified</label>
									</td>
									<td style=\"color:purple; cursor:pointer;\">
										<input type='checkbox' id='elem_sub_a_pattern' name='elem_sub_a_pattern' value='a' class='elem_sub_charts2 unspecified_css' style=\"display:none\" onclick=\"icd10_charts_popup('0',this,'','');\">
										<label for=\"elem_sub_a_pattern\" style=\"color:purple;cursor:pointer;\">A pattern</label>
									</td>
									<td style=\"color:purple; cursor:pointer;\">
										<input type='checkbox' id='elem_sub_right' name='elem_sub_right' value='right' class='elem_sub_charts3 unspecified_css alternating_css' style=\"display:none\" onclick=\"icd10_charts_popup('0',this,'','');\">
										<label for=\"elem_sub_right\" style=\"color:purple;cursor:pointer;\">Right</label>
									</td>
								</tr>
								<tr><td></td>
									<td style=\"color:purple; cursor:pointer;\">
										<input type='checkbox' id='elem_sub_monocular' name='elem_sub_monocular' value='monocular' class='elem_sub_charts1' style=\"display:none\" onclick=\"icd10_charts_popup('0',this,'','');\">
										<label for=\"elem_sub_monocular\" style=\"color:purple;cursor:pointer;\">Monocular</label>
									</td>
									<td style=\"color:purple; cursor:pointer;\">
										<input type='checkbox' id='elem_sub_v_pattern' name='elem_sub_v_pattern' value='v' class='elem_sub_charts2 unspecified_css' style=\"display:none\" onclick=\"icd10_charts_popup('0',this,'','');\">
										<label for=\"elem_sub_v_pattern\" style=\"color:purple;cursor:pointer;\">V pattern</label>
									</td>
									<td style=\"color:purple; cursor:pointer;\">
										<input type='checkbox' id='elem_sub_left' name='elem_sub_left' value='left' class='elem_sub_charts3 unspecified_css alternating_css' style=\"display:none\" onclick=\"icd10_charts_popup('0',this,'','');\">
										<label for=\"elem_sub_left\" style=\"color:purple;cursor:pointer;\">Left</label>
									</td>
								</tr>
								<tr><td></td>
									<td style=\"color:purple; cursor:pointer;\">
										<input type='checkbox' id='elem_sub_alternating' name='elem_sub_alternating' value='alternating' class='elem_sub_charts1' style=\"display:none\" onclick=\"icd10_charts_popup('0',this,'','');\">
										<label for=\"elem_sub_alternating\" style=\"color:purple;cursor:pointer;\">Alternating</label>
									</td>
									<td style=\"color:purple; cursor:pointer;\">
										<input type='checkbox' id='elem_sub_intermittent' name='elem_sub_intermittent' value='intermittent' class='elem_sub_charts2 unspecified_css' style=\"display:none\" onclick=\"icd10_charts_popup('0',this,'','');\">
										<label for=\"elem_sub_intermittent\" style=\"color:purple;cursor:pointer;\">Intermittent</label>
									</td>
									<td></td>
								</tr>
								<tr><td style='vertical-align:top;'><b>Comment</b></td>
									<td colspan='3'><textarea class='input_text_10' name='elem_sub_ass_comm' id='elem_sub_ass_comm' style='width:390px; height:40px;'></textarea></td>
								</tr>
							</table>
						</p>
					</div>";
				}
			}
			if($get_fet['master_codes']!=""){
				$master_codes_exp=explode(';',$get_fet['master_codes']);
				$sub_id=$get_fet['id'];
				foreach($master_codes_exp as $k=>$v){
					if(trim($v)!=''){
						$icd10=$get_fet['icd10'];
						$icd10_desc=$get_fet['icd10_desc'];
						$icd10_group_heading=$get_fet['group_heading'];

						$grp_key=count($causative_factors_arr);
						if($icd10_group_heading!=""){
							if(in_array(strtolower($icd10_group_heading),$causative_factors_arr)){
								$grp_key=array_search(strtolower($icd10_group_heading), $causative_factors_arr);
							}
						}

						if($icd10_group_heading!="" && $icd10_group_heading!=$icd10_group_heading_old){
							$grp_plus++;
							$icd10_group_heading_css="grp_cls".$grp_plus;
							$icd_grp_data[$grp_key][trim(strtolower($v))][]="<tr><td style='padding:0px 0px 4px 0px; cursor:pointer;' class='text_10b_purple' onClick=\"show_sec_icd('$icd10_group_heading_css');\"><b>".$icd10_group_heading."</b></td></tr>";
						}
						$grp_cls_var="";
						if($icd10_group_heading!=""){
							$grp_cls_var=" class='icd10_data_td ".$icd10_group_heading_css."' style='display:none;'";
						}
						$icd_grp_data[$grp_key][trim(strtolower($v))][]= "<tr ".$grp_cls_var."><td style='padding-left:20px;'><input type='hidden' id='elem_sub_desc_".$sub_id."' name='elem_sub_dx_desc' value='".$icd10_desc."'><input type='checkbox' id='elem_sub_".$sub_id."' name='elem_sub_dx' value='".$icd10."' class='elem_sub_dx' style=\"display:none\" onclick='cn_set_sub_dx(".$sub_id.",\"\");' data-valbakdb='".$icd10."'><label for=\"elem_sub_".$sub_id."\" style=\"color:purple;cursor:pointer;\" >".$icd10_desc." (".$icd10.")</label></td></tr>";
						$icd10_group_heading_old=$icd10_group_heading;
					}
				}
			}
		}
		for($i=0;$i<=count($causative_factors_arr);$i++){
			if(is_array($icd_grp_data[$i]) && count($icd_grp_data[$i])>0){
			foreach($icd_grp_data[$i] as $key => $dataDet){
				if($icd_data[$key]){
					$icd_data[$key]=array_merge($icd_data[$key], $dataDet);
				}else{
					$icd_data[$key]=$dataDet;
				}
			}
			}
		}
		if($icd10_charts_data_heading!=""){
			ksort($icd10_charts_data_arr);
			$icd10_charts_data['congenital']=$icd10_charts_data_heading.implode('',$icd10_charts_data_arr).$icd10_charts_data_footer;
		}

		//is VIP
		if($isSuperBill==0){
			$vipSuperBill = $this->isVIPPt();
		}

		//ocular print
		$printmedsbut= $this->getOcuMedsPrint($finalize_flag,0,"GetPlansRx");


		//
		$sb_cpt_code_poe = !empty($GLOBALS['cpt_code_poe']) ? $oCpt->mkCPTCode($GLOBALS['cpt_code_poe'],"Prac") : $oCpt->mkCPTCode('999',"Prac");

		/*echo "<pre>";
		print_r($icd_data);
		//print_r($icd_grp_data);*/
		//print_r($icd10_charts_data);
		//------------------------
		ob_start();
		$tmp = str_replace("\x", "\\x", $GLOBALS['incdir']);
		include($tmp."/chart_notes/superbill.inc.php");
		$out2 = ob_get_contents();
		ob_end_clean();
		return $out2;
	}
}
?>
