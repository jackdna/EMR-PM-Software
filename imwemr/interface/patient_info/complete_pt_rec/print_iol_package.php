<?php

function getLenseNameIolMaster($lenseID){
	global $lenses_iol_type_arr;
	$lenses_iol_type = $lenses_iol_type_arr[$lenseID];
	return $lenses_iol_type;
}

function getKHeadingNameIolMaster($ID){
	global $kReadingHeadingNameArr;
	$kReadingHeadingName = $kReadingHeadingNameArr[$ID];
	return $kReadingHeadingName;
}

$pid = $patient_id;
$patient_id_from_iolink = $patient_id;
$sa_app_start_date = $dtDBEffectDate;	

$authUserId_pro = $authUserId = $_SESSION['authId'];
//================= GETTING PHYSICIANS AND TECH
$getPhysicianTechStr = "SELECT * FROM users WHERE user_type IN (".implode(",", $GLOBALS['arrValidCNPhy']).") OR user_type IN (".implode(",", $GLOBALS['arrValidCNTech']).") and delete_status = '0'";
$getPhysicianTechQry = imw_query($getPhysicianTechStr);
while($getPhysicianTechRows = imw_fetch_array($getPhysicianTechQry)){
	$phyTechId = $getPhysicianTechRows['id'];
	$phyTechFname = $getPhysicianTechRows['fname'];
	$phyTechMname = $getPhysicianTechRows['mname'];
	$phyTechLname = $getPhysicianTechRows['lname'];
	$physiciansTechs = $getPhysicianTechRows['username'];
	$physiciansTechs = $phyTechFname." ".$phyTechMname." ".$phyTechLname;
	$phyTechArray[$phyTechId] = ucwords($physiciansTechs);

}
//================= GETTING PHYSICIANS AND TECH	

//================= GETTING PHYSICIANS
$phyArray = array();
$getPhysicianStr = "SELECT * FROM users WHERE user_type IN (".implode(",", $GLOBALS['arrValidCNPhy']).") and delete_status='0'";
$getPhysicianQry = imw_query($getPhysicianStr);
while($getPhysicianRows = imw_fetch_array($getPhysicianQry)){
	$phyId = $getPhysicianRows['id'];
	$phyFname = $getPhysicianRows['fname'];
	$phyMname = $getPhysicianRows['mname'];
	$phyLname = $getPhysicianRows['lname'];
	$physicians = $getPhysicianRows['username'];
	$physicians = $phyFname." ".$phyMname." ".$phyLname;
	$phyArray[$phyId] = ucwords($physicians);
}
//================= GETTING PHYSICIANS

$iolinkIolMasterQry 	= "SELECT * FROM `iol_master_tbl` WHERE patient_id='".$pid."' AND examDate<='".$sa_app_start_date."' AND purged = '0' ORDER BY iol_master_id DESC LIMIT 0,1";	
$iolinkIolMasterRes 	= imw_query($iolinkIolMasterQry);
$iolinkIolMasterNumRow 	= imw_num_rows($iolinkIolMasterRes);
if($iolinkIolMasterNumRow>0) {
	$iolinkIolMasterRow = imw_fetch_array($iolinkIolMasterRes);
	extract($iolinkIolMasterRow);
	$iol_master_id 		= $iolinkIolMasterRow['iol_master_id'];
	//$form_id = $iolinkIolMasterRow['form_id'];
	$iolink_iol_master_form_id 	= $iolinkIolMasterRow['form_id'];
	
	
	//start IOL_Master variables
	
	$toi_arrPhy 				= getPhysicianMenuArray(1,"cn");
	$orderedBy 					= $toi_arrPhy[$ordrby];
	$elem_opidTestOrderedDate	= get_date_format($iolinkIolMasterRow["ordrdt"]);
	$elem_examDate 				= $iolinkIolMasterRow['examDate'];
	$autoSelectOD 				= getKHeadingNameIolMaster($autoSelectOD);
	$iolMasterSelectOD 			= getKHeadingNameIolMaster($iolMasterSelectOD);
	$topographerSelectOD 		= getKHeadingNameIolMaster($topographerSelectOD);
	$vis_mr_od_s 				= $iolinkIolMasterRow['mrSOD'];
	$vis_mr_od_c 				= $iolinkIolMasterRow['mrCOD'];
	$vis_mr_od_a 				= $iolinkIolMasterRow['mrAOD'];
	$vis_ak_od_k 				= $iolinkIolMasterRow['k1Auto1OD'];
	$vis_ak_od_x 				= $iolinkIolMasterRow['k1Auto2OD'];
	$vis_ak_od_slash			= $iolinkIolMasterRow['k2Auto1OD'];
	$k2Auto2OD 					= $iolinkIolMasterRow['k2Auto2OD'];
	if($vis_ak_od_k>=$vis_ak_od_slash){
		$cyl1OD 	= $vis_ak_od_k - $vis_ak_od_slash;
	}else{
		$cyl1OD 	= $vis_ak_od_slash - $vis_ak_od_k;
	}
	$aveOD1 = ($vis_ak_od_k + $vis_ak_od_slash)/2;
	
	$vis_mr_os_s 				= $iolinkIolMasterRow['mrSOS'];
	$vis_mr_os_c 				= $iolinkIolMasterRow['mrCOS'];
	$vis_mr_os_a 				= $iolinkIolMasterRow['mrAOS'];
	$vis_ak_os_k 				= $iolinkIolMasterRow['k1Auto1OS'];
	$vis_ak_os_x 				= $iolinkIolMasterRow['k1Auto2OS'];
	$vis_ak_os_slash			= $iolinkIolMasterRow['k2Auto1OS'];
	$k2Auto2OS 					= $iolinkIolMasterRow['k2Auto2OS'];
	if($vis_ak_os_k>=$vis_ak_os_slash){
		$cyl1OS 	= $vis_ak_os_k - $vis_ak_os_slash;
	}else{
		$cyl1OS 	= $vis_ak_os_slash - $vis_ak_os_k;
	}
	$aveOS1 					= ($vis_ak_os_k + $vis_ak_os_slash)/2;
	$today 						= date('m-d-Y',strtotime($iolinkIolMasterRow['dateOD']));
	$dateOS 	  				= date('m-d-Y',strtotime($iolinkIolMasterRow['dateOS']));
	$authUserId 				= $iolinkIolMasterRow['performedByOD'];
	$provider_idOS 				= $iolinkIolMasterRow['performedByOS'];
	$provider_idOD 				= $iolinkIolMasterRow['performedByPhyOD'];
	
	$autoSelectOS 				= getKHeadingNameIolMaster($autoSelectOS);
	$iolMasterSelectOS 			= getKHeadingNameIolMaster($iolMasterSelectOS);
	$topographerSelectOS 		= getKHeadingNameIolMaster($topographerSelectOS);

	$powerIolOD 				= getFormulaHeadNameIolMaster($powerIolOD);
	$holladayOD 				= getFormulaHeadNameIolMaster($holladayOD);
	$srk_tOD 					= getFormulaHeadNameIolMaster($srk_tOD);
	$hofferOD 					= getFormulaHeadNameIolMaster($hofferOD);

	$powerIolOS 				= getFormulaHeadNameIolMaster($powerIolOS);
	$holladayOS 				= getFormulaHeadNameIolMaster($holladayOS);
	$srk_tOS 					= getFormulaHeadNameIolMaster($srk_tOS);
	$hofferOS 					= getFormulaHeadNameIolMaster($hofferOS);
	
	$sur_dateOD					= $iolinkIolMasterRow["sur_dt_od"];
	$sur_dateOS 				= $iolinkIolMasterRow["sur_dt_os"];
	
	$arrTmp = array();
	$elem_proc_od_1 = $elem_proc_od_2 = $elem_proc_od_3 = "";
	$elem_proc_od 				= $iolinkIolMasterRow["proc_od"];
	if(!empty($elem_proc_od)){
		$arrTmp = explode(",",$elem_proc_od);
		if(in_array("Phaco",$arrTmp)){
			$elem_proc_od_1 = "checked";
		}
		if(in_array("Complex Phaco",$arrTmp)){
			$elem_proc_od_2 = "checked";
		}
		if(in_array("Combined Procedure",$arrTmp)){
			$elem_proc_od_3 = "checked";
		}
	}
	
	$arrTmp = array();
	$elem_proc_os_1 = $elem_proc_os_2 = $elem_proc_os_3 = "";
	$elem_proc_os = $iolinkIolMasterRow["proc_os"];
	if(!empty($elem_proc_os)){
		$arrTmp = explode(",",$elem_proc_os);

		if(in_array("Phaco",$arrTmp)){
			$elem_proc_os_1 = "checked";
		}
		if(in_array("Complex Phaco",$arrTmp)){
			$elem_proc_os_2 = "checked";
		}
		if(in_array("Combined Procedure",$arrTmp)){
			$elem_proc_os_3 = "checked";
		}
	}
	
	$arrTmp = array();
	$elem_anes_od_1 = $elem_anes_od_2 = "";
	$elem_anes_od = $iolinkIolMasterRow["anes_od"];
	if(!empty($elem_anes_od)){
		$arrTmp = explode(",",$elem_anes_od);

		if(in_array("Local",$arrTmp)){
			$elem_anes_od_1 = "checked";
		}
		if(in_array("Topical",$arrTmp)){
			$elem_anes_od_2 = "checked";
		}
	}
	
	$arrTmp = array();
	$elem_anes_os_1 = $elem_anes_os_2 = "";
	$elem_anes_os = $iolinkIolMasterRow["anes_os"];
	if(!empty($elem_anes_os)){
		$arrTmp = explode(",",$elem_anes_os);

		if(in_array("Local",$arrTmp)){
			$elem_anes_os_1 = "checked";
		}
		if(in_array("Topical",$arrTmp)){
			$elem_anes_os_2 = "checked";
		}
	}
	
	$elem_visc_od 		= $iolinkIolMasterRow["visc_od"];
	$elem_visc_od_other = $iolinkIolMasterRow["visc_od_other"];
	$arrTmp = array();
	$elem_visc_od_other = $elem_visc_od_1 = $elem_visc_od_2 = $elem_visc_od_3 = $elem_visc_od_4 = "";
	if(empty($elem_visc_od_other)){
		$elem_visc_od_other = "Other";
	}
	
	if(!empty($elem_visc_od)){
		$arrTmp = explode(",",$elem_visc_od);

		if(in_array("Discovisc",$arrTmp)||in_array("Duovisc",$arrTmp)){
			$elem_visc_od_1="checked";
		}
		if(in_array("Viscoat",$arrTmp)){
			$elem_visc_od_2="checked";
		}
		if(in_array("Amvisc plus",$arrTmp)){
			$elem_visc_od_3="checked";
		}
		if(in_array("Healon",$arrTmp)){
			$elem_visc_od_4="checked";
		}

	}
	
	$elem_visc_os 		= $iolinkIolMasterRow["visc_os"];
	$elem_visc_os_other = $iolinkIolMasterRow["visc_os_other"];
	$arrTmp = array();
	$elem_visc_os_other = $elem_visc_os_1 = $elem_visc_os_2 = $elem_visc_os_3 = $elem_visc_os_4 = "";
	if(empty($elem_visc_os_other)){
		$elem_visc_os_other = "Other";
	}

	if(!empty($elem_visc_os)){
		$arrTmp = explode(",",$elem_visc_os);

		if(in_array("Discovisc",$arrTmp) || in_array("Duovisc",$arrTmp)){
			$elem_visc_os_1="checked";
		}
		if(in_array("Viscoat",$arrTmp)){
			$elem_visc_os_2="checked";
		}
		if(in_array("Amvisc plus",$arrTmp)){
			$elem_visc_os_3="checked";
		}
		if(in_array("Healon",$arrTmp)){
			$elem_visc_os_4="checked";
		}

	}
	
	$elem_opts_od 		= $iolinkIolMasterRow["opts_od"];
	$elem_opts_od_other = $iolinkIolMasterRow["opts_od_other"];
	$arrTmp = array();
	$elem_opts_od_other = $elem_opts_od_1 = $elem_opts_od_2 = $elem_opts_od_3 = $elem_opts_od_4 = "";
	if(empty($elem_opts_od_other)){
		$elem_opts_od_other = "Other";
	}

	if(!empty($elem_opts_od)){
		$arrTmp = explode(",",$elem_opts_od);

		if(in_array("Malyugin ring",$arrTmp)){
			$elem_opts_od_1="checked";
		}
		if(in_array("Shugarcaine",$arrTmp)){
			$elem_opts_od_2="checked";
		}
		if(in_array("Capsule tension rings",$arrTmp)){
			$elem_opts_od_3="checked";
		}
		if(in_array("IOL Cutter",$arrTmp)){
			$elem_opts_od_4="checked";
		}
	}
	
	$elem_opts_os 		= $iolinkIolMasterRow["opts_os"];
	$elem_opts_os_other = $iolinkIolMasterRow["opts_os_other"];
	$arrTmp = array();
	$elem_opts_os_other = $elem_opts_os_1 = $elem_opts_os_2 = $elem_opts_os_3 = $elem_opts_os_4 = "";
	if(empty($elem_opts_os_other)){
		$elem_opts_os_other = "Other";
	}

	if(!empty($elem_opts_os)){
		$arrTmp = explode(",",$elem_opts_os);

		if(in_array("Malyugin ring",$arrTmp)){
			$elem_opts_os_1="checked";
		}
		if(in_array("Shugarcaine",$arrTmp)){
			$elem_opts_os_2="checked";
		}
		if(in_array("Capsule tension rings",$arrTmp)){
			$elem_opts_os_3="checked";
		}
		if(in_array("IOL Cutter",$arrTmp)){
			$elem_opts_os_4="checked";
		}
	}		
	
	$elem_iol2dn_od 	= $iolinkIolMasterRow["iol2dn_od"];
	$elem_iol2dn_od_1 = $elem_iol2dn_od_2 = "";
	if($elem_iol2dn_od == "Yes"){
		$elem_iol2dn_od_1 = "checked";
	}
	if($elem_iol2dn_od == "No"){
		$elem_iol2dn_od_2 = "checked";
	}
	
	$elem_iol2dn_os 	= $iolinkIolMasterRow["iol2dn_os"];	
	$elem_iol2dn_os_1 = $elem_iol2dn_os_2 = "";
	if($elem_iol2dn_os == "Yes"){
		$elem_iol2dn_os_1 = "checked";
	}
	if($elem_iol2dn_os == "No"){
		$elem_iol2dn_os_2 = "checked";
	}	
	//end IOL_Master variables
	
	//================= LENSES DEFINED TO PROVIDER OD
	$providerLensesArrOD = array();
	$getLensesForProviderStr = "SELECT * FROM lensesdefined a,
								lenses_iol_type b
								WHERE a.physician_id = '$provider_idOD'
								AND a.iol_type_id = b.iol_type_id";
	$getLensesForProviderQry = imw_query($getLensesForProviderStr);
	if(imw_num_rows($getLensesForProviderQry)>0){
		while($getLensesForProviderRows = imw_fetch_array($getLensesForProviderQry)){
			$iol_type_idOD = $getLensesForProviderRows['iol_type_id'];
			$providersLensesOD = $getLensesForProviderRows['lenses_iol_type'];
			$providerLensesArrOD[] = $providersLensesOD;
		}
	}
	//================= LENSES DEFINED TO PROVIDER OD	
		
	if($iol1OD!=''){
		$providerLensesArrOD[0] 		= getLenseNameIolMaster($iol1OD);
		$providerLensesODArr[$iol1OD] 	= getLenseNameIolMaster($iol1OD);
	}
	if($iol2OD!=''){
		$providerLensesArrOD[1] 		= getLenseNameIolMaster($iol2OD);
		$providerLensesODArr[$iol2OD] 	= getLenseNameIolMaster($iol2OD);
	}
	if($iol3OD!=''){
		$providerLensesArrOD[2] 		= getLenseNameIolMaster($iol3OD);
		$providerLensesODArr[$iol3OD] 	= getLenseNameIolMaster($iol3OD);
	}
	if($iol4OD!=''){
		$providerLensesArrOD[3] 		= getLenseNameIolMaster($iol4OD);
		$providerLensesODArr[$iol4OD] 	= getLenseNameIolMaster($iol4OD);
	}
	
	//================= LENSES DEFINED TO PROVIDER OS
	$providerLensesArrOS = array();
	$getLensesForProviderStr = "SELECT * FROM lensesdefined a,
								lenses_iol_type b
								WHERE a.physician_id = '$provider_idOS'
								AND a.iol_type_id = b.iol_type_id";
	$getLensesForProviderQry = imw_query($getLensesForProviderStr);
	if((imw_num_rows($getLensesForProviderQry)>0)){
		while($getLensesForProviderRows = imw_fetch_array($getLensesForProviderQry)){
			$iol_type_id = $getLensesForProviderRows['iol_type_id'];
			$providersLenses = $getLensesForProviderRows['lenses_iol_type'];
			$lensesProviderArray[$iol_type_id] = $providersLenses;
			$providerLensesArrOS[] = $providersLenses;
		}
	}
	//================= LENSES DEFINED TO PROVIDER OS		
	if($iol1OS!=''){
		$providerLensesArrOS[0] 		= getLenseNameIolMaster($iol1OS);
		$providerLensesOSArr[$iol1OS] 	= getLenseNameIolMaster($iol1OS);
	}
	if($iol2OS!=''){
		$providerLensesArrOS[1] 		= getLenseNameIolMaster($iol2OS);
		$providerLensesOSArr[$iol2OS] 	= getLenseNameIolMaster($iol2OS);
	}
	if($iol3OS!=''){
		$providerLensesArrOS[2] 		= getLenseNameIolMaster($iol3OS);
		$providerLensesOSArr[$iol3OS] 	= getLenseNameIolMaster($iol3OS);
	}
	if($iol4OS!=''){
		$providerLensesArrOS[3] 		= getLenseNameIolMaster($iol4OS);
		$providerLensesOSArr[$iol4OS] 	= getLenseNameIolMaster($iol4OS);
	}
	
	$selecedIOLsOD				= $iolinkIolMasterRow["selecedIOLsOD"];		
	$lensesProviderArray 		= getLensesIolMaster($provider_idOD);
	if((($lensesProviderArray)) != (($providerLensesArrOD))){
		$lensesProviderArray = $providerLensesODArr;
	}
	$PlanOD = "";
	if(count($lensesProviderArray)>0){
		foreach($lensesProviderArray as $lensesId => $lenseNames){
			if($selecedIOLsOD==$lensesId) { $PlanOD = $lenseNames; }
		
		}
	}
	
	$lensesProviderArray = getLensesIolMaster($provider_idOS);
	if((($lensesProviderArray)) != (($providerLensesArrOS))){
		$lensesProviderOSArray = $providerLensesOSArr;
	}
	if($sign_path && ($sign_path_date_time!="0000-00-00 00:00:00" || $sign_path_date_time!=0)) {
		$sign_path_date = date("m-d-Y",strtotime($sign_path_date_time));
		$sign_path_time = date("h:i A",strtotime($sign_path_date_time));
	}		
	$sign_path_print='';
	if($sign_path!='') {
		$oSaveFile = new SaveFile($patient_id);
		$tmpDirPth_up = $oSaveFile->getUploadDirPath();
		$tmpDirPth_sign = $oSaveFile->ptDir("test_sign/".$folder_name);
		$tmpDirPth_pt = "/PatientId_".$patient_id;
		$form_sign_path = $tmpDirPth_pt.$tmpDirPth_sign;
		$sign_real_path=realpath($tmpDirPth_up.$form_sign_path);
		
		if(file_exists($sign_real_path)) {
			$sign_path_print.='
			<tr>
				<td colspan="2" style="padding-left:10px;">
					<table class="table_collapse" >
						<tr>
							<td><b>Signature:</b></td>
							<td >
								<table class="table_collapse" >
									<tr>
										<td style="border:1px solid #E5E4E2; background-color:#FFF;padding:3px; "><img src="../../main/uploaddir'.$sign_path.'" alt="sign" width="225" height="45" ></td>
										<td style="white-space:nowrap; padding-left:5px;"><b>Date</b> '.$sign_path_date.' <b>Time</b> '.$sign_path_time.'</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>';
		}
	}
	
	global $oSaveFile;
	$oSaveFile = new SaveFile($pid);
	list($scanId_but,$scanId_prev) = getTestScan($pid,$iolink_iol_master_form_id,"IOL_Master",$iol_master_id);
	if(!empty($scanId_but) || !empty($scanId_prev)){
		$tw="130";
		if(empty($noP)){
			$winW="1140";
			$winH="775";
		}else{
			$winW="0";
			$winH="0";
		}
		$str = insertScans($scanId_but,$scanId_prev,$tw,$winW,$winH,"IOL_Master");
		$str = preg_replace('/No Current Scan/','',$str);
		if(isset($_REQUEST['doNotShowRightSide']) == true && empty($_REQUEST['doNotShowRightSide']) == false){
			$str = "";
		}
		
	}
	
	$rand=rand(0,500);
	$test_name="chart_tests";
	$iol_master_html_file_name ='iol_package_'.$_SESSION['authId'];
	$html_file_name = $ascan_html_file_name;
	include_once($GLOBALS['fileroot'].'/interface/chart_notes/iol_master_print.php');
	$patientDirAscan = "/PatientId_".$pid;
	$toMakePdfFor = "Iolink";

	$patientPrintData = $testPrint;
	$data_dir = substr(data_path(), 0, -1);
	$fp = $data_dir.'/iOLink/'.$iol_master_html_file_name.'.html';
	$putData = file_put_contents($fp,$patientPrintData);
	//fclose($fp);
	$patientPrintData="";
}	
?>