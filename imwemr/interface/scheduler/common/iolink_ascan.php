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

//include_once($GLOBALS["incdir"]."/chart_notes/common/functions.php");
//include_once($GLOBALS["incdir"]."/main/main_functions.php");
//include_once($GLOBALS["incdir"]."/admin/chart_more_functions.php");
//include_once($GLOBALS["incdir"]."/chart_notes/common/simpleMenu_2.php");
//include_once($GLOBALS["incdir"]."/chart_notes/common/session_chart_view_access.php");
//include_once($GLOBALS["incdir"]."/chart_notes/chartNotesSaveFunction.php");
//include_once($GLOBALS["incdir"]."/chart_notes/common/SaveFile.php");
//include_once($GLOBALS["incdir"]."/main/funcListProcedures.php"); //sb
//include_once($GLOBALS["incdir"]."/common/scan_function.php");

$data_dir = substr(data_path(), 0, -1);

$strPtDtQry="SELECT pd.*,date_format(pd.date,'%m-%d-%Y') as createdDate,
									date_format(pd.DOB,'%m-%d-%Y') as ptDOB,
									DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(dob, '%Y') - (DATE_FORMAT(NOW(), '00-%m-%d') < DATE_FORMAT(dob, '00-%m-%d')) AS age,
									sa.sa_app_start_date,sa.sa_patient_id
									FROM schedule_appointments sa
									LEFT JOIN patient_data pd ON pd.pid = sa.sa_patient_id											
									WHERE sa.id = ".$schedule_id;									
$strPtDtRes = imw_query($strPtDtQry) or die(imw_error());
$strPtDtNumRow = imw_num_rows($strPtDtRes);
$form_id_from_iolink='';

//--------------	FUNCTION TO GET K HEADING NAMES	---------------//
$getKreadingIdStr = "SELECT * FROM kheadingnames ORDER BY kheadingId";
$getKreadingIdQry = imw_query($getKreadingIdStr);
$kReadingHeadingNameArr = array();
while($getKreadingIdRow = imw_fetch_array($getKreadingIdQry)) {		
	if(strpos($getKreadingIdRow['kheadingName'], "K[")===false){$getKreadingIdRow['kheadingName'] = 'K['.$getKreadingIdRow['kheadingName'];}
	if(strpos($getKreadingIdRow['kheadingName'], "]")===false){$getKreadingIdRow['kheadingName'] = $getKreadingIdRow['kheadingName']."]";}		
	$kheadingId = $getKreadingIdRow['kheadingId'];
	$kReadingHeadingNameArr[$kheadingId] = $getKreadingIdRow['kheadingName'];		
}

function getKHeadingNameAscan($ID){
	global $kReadingHeadingNameArr;
	$kReadingHeadingName = $kReadingHeadingNameArr[$ID];
	return $kReadingHeadingName;
}
//--------------	FUNCTION TO GET K HEADING NAMES	---------------//

//--------------	FUNCTION TO GET LENSES FORMULA HEADING NAME	---------------//
$formula_heading_name_arr = array();
$getFormulaheadingsStr = "SELECT * FROM formulaheadings ORDER BY formula_id";
$getFormulaheadingsQry = imw_query($getFormulaheadingsStr);
while($getFormulaheadingsRow = imw_fetch_array($getFormulaheadingsQry)) {
	$get_formula_id = $getFormulaheadingsRow['formula_id'];
	$formula_heading_name_arr[$get_formula_id] = $getFormulaheadingsRow['formula_heading_name'];
}

function getFormulaHeadNameAscan($id){
	global $formula_heading_name_arr;
	$formula_heading_name = $formula_heading_name_arr[$id];
	return $formula_heading_name;
}
//--------------	FUNCTION TO GET LENSES FORMULA HEADING NAME	---------------//

//================= FUNCTION TO GET LENSE TYPE
$lenses_iol_type_arr = array();
$getLenseTypeStr = "SELECT * FROM lenses_iol_type ORDER BY iol_type_id";
$getLenseTypeQry = imw_query($getLenseTypeStr);
while($getLenseTypeRow = imw_fetch_array($getLenseTypeQry)) {
	$get_iol_type_id = $getLenseTypeRow['iol_type_id'];
	$lenses_iol_type = $getLenseTypeRow['lenses_iol_type'];
	$lenses_iol_type_arr[$get_iol_type_id] = $getLenseTypeRow['lenses_iol_type'];
}
function getLenseNameAscan($lenseID){
	global $lenses_iol_type_arr;
	$lenses_iol_type = $lenses_iol_type_arr[$lenseID];
	return $lenses_iol_type;
}
//================= FUNCTION TO GET LENSE TYPE

//================= START FUNCTION FOR LENSES DEFINED TO PROVIDER OD
function getLensesAscan($provider_id){
	$getLensesForProviderStr = "SELECT * FROM lensesdefined a,
								lenses_iol_type b
								WHERE a.physician_id = '$provider_id'
								AND a.iol_type_id = b.iol_type_id";
	$getLensesForProviderQry = imw_query($getLensesForProviderStr);
	if((imw_num_rows($getLensesForProviderQry)>0)){
		while($getLensesForProviderRows = imw_fetch_array($getLensesForProviderQry)){
			$iol_type_id = $getLensesForProviderRows['iol_type_id'];
			$providersLenses = $getLensesForProviderRows['lenses_iol_type'];
			$lensesProviderArray[$iol_type_id] = $providersLenses;
		}
	}
	return $lensesProviderArray;
}
//================= START FUNCTION FOR LENSES DEFINED TO PROVIDER OD

if($strPtDtNumRow>0) {
	$strPtDtRow = imw_fetch_array($strPtDtRes);	
	$pid = $strPtDtRow['pid'];
	$patient_id_from_iolink = $strPtDtRow['pid'];
	$sa_app_start_date = $strPtDtRow['sa_app_start_date'];	
	
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
	
	$iolinkAscanQry = "SELECT * FROM `surgical_tbl` WHERE patient_id='".$pid."' AND examDate<='".$sa_app_start_date."' AND purged = '0' ORDER BY surgical_id DESC LIMIT 0,1";	
	$iolinkAscanRes = imw_query($iolinkAscanQry);
	$iolinkAscanNumRow = imw_num_rows($iolinkAscanRes);
	if($iolinkAscanNumRow>0) {
		$iolinkAscanRow = imw_fetch_array($iolinkAscanRes);
		extract($iolinkAscanRow);
		$surgical_id 				= $iolinkAscanRow['surgical_id'];
		//$form_id 					= $iolinkAscanRow['form_id'];
		$iolink_ascan_form_id 		= $iolinkAscanRow['form_id'];
		
		//start AScan variables
		$authUserId 				= $iolinkAscanRow['performedByOD'];
		$toi_arrPhy 				= getPhysicianMenuArray(1,"cn");
		$orderedBy 					= $toi_arrPhy[$ordrby];
		$elem_opidTestOrderedDate 	= getDateFormatDB($iolinkAscanRow["ordrdt"]);
		$elem_examDate 				= $iolinkAscanRow['examDate'];
		$provider_idOS 				= $iolinkAscanRow['performedByOS'];
		$today 						= date('m-d-Y',strtotime($iolinkAscanRow['dateOD']));
		$dateOS 	  				= date('m-d-Y',strtotime($iolinkAscanRow['dateOS']));
		$provider_idOD 				= $iolinkAscanRow['performedByPhyOD'];
		$autoSelectOD 				= getKHeadingNameAscan($autoSelectOD);
		$iolMasterSelectOD 			= getKHeadingNameAscan($iolMasterSelectOD);
		$topographerSelectOD		= getKHeadingNameAscan($topographerSelectOD);
		$vis_mr_od_s 				= $iolinkAscanRow['mrSOD'];
		$vis_mr_od_c 				= $iolinkAscanRow['mrCOD'];
		$vis_mr_od_a 				= $iolinkAscanRow['mrAOD'];
		$vis_ak_od_k 				= $iolinkAscanRow['k1Auto1OD'];
		$vis_ak_od_x 				= $iolinkAscanRow['k1Auto2OD'];
		$vis_ak_od_slash 			= $iolinkAscanRow['k2Auto1OD'];
		$k2Auto2OD 					= $iolinkAscanRow['k2Auto2OD'];
		if($vis_ak_od_k>=$vis_ak_od_slash){
			$cyl1OD = $vis_ak_od_k - $vis_ak_od_slash;
		}else{
			$cyl1OD = $vis_ak_od_slash - $vis_ak_od_k;
		}
		$aveOD1 = ($vis_ak_od_k + $vis_ak_od_slash)/2;

		$autoSelectOS 				= getKHeadingNameAscan($autoSelectOS);
		$iolMasterSelectOS 			= getKHeadingNameAscan($iolMasterSelectOS);
		$topographerSelectOS 		= getKHeadingNameAscan($topographerSelectOS);
		$vis_mr_os_s 				= $iolinkAscanRow['mrSOS'];
		$vis_mr_os_c 				= $iolinkAscanRow['mrCOS'];
		$vis_mr_os_a 				= $iolinkAscanRow['mrAOS'];
		$vis_ak_os_k 				= $iolinkAscanRow['k1Auto1OS'];
		$vis_ak_os_x 				= $iolinkAscanRow['k1Auto2OS'];
		$vis_ak_os_slash			= $iolinkAscanRow['k2Auto1OS'];
		$k2Auto2OS 					= $iolinkAscanRow['k2Auto2OS'];
		if($vis_ak_os_k>=$vis_ak_os_slash){
			$cyl1OS 	= $vis_ak_os_k - $vis_ak_os_slash;
		}else{
			$cyl1OS 	= $vis_ak_os_slash - $vis_ak_os_k;
		}
		$aveOS1 = ($vis_ak_os_k + $vis_ak_os_slash)/2;

		
		$powerIolOD 				= getFormulaHeadNameAscan($powerIolOD);
		$holladayOD 				= getFormulaHeadNameAscan($holladayOD);
		$srk_tOD 					= getFormulaHeadNameAscan($srk_tOD);
		$hofferOD 					= getFormulaHeadNameAscan($hofferOD);
		
		$powerIolOS 				= getFormulaHeadNameAscan($powerIolOS);
		$holladayOS 				= getFormulaHeadNameAscan($holladayOS);
		$srk_tOS 					= getFormulaHeadNameAscan($srk_tOS);
		$hofferOS 					= getFormulaHeadNameAscan($hofferOS);
		
		$sur_dateOD					= $iolinkAscanRow["sur_dt_od"];
		$sur_dateOS 				= $iolinkAscanRow["sur_dt_os"];
		
		
		$arrTmp = array();
		$elem_proc_od_1 = $elem_proc_od_2 = $elem_proc_od_3 = "";
		$elem_proc_od = $iolinkAscanRow["proc_od"];
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
		$elem_proc_os = $iolinkAscanRow["proc_os"];
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
		$elem_anes_od = $iolinkAscanRow["anes_od"];
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
		$elem_anes_os = $iolinkAscanRow["anes_os"];
		if(!empty($elem_anes_os)){
			$arrTmp = explode(",",$elem_anes_os);

			if(in_array("Local",$arrTmp)){
				$elem_anes_os_1 = "checked";
			}
			if(in_array("Topical",$arrTmp)){
				$elem_anes_os_2 = "checked";
			}
		}
		
		$elem_visc_od 		= $iolinkAscanRow["visc_od"];
		$elem_visc_od_other = $iolinkAscanRow["visc_od_other"];
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
		
		$elem_visc_os 		= $iolinkAscanRow["visc_os"];
		$elem_visc_os_other = $iolinkAscanRow["visc_os_other"];
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
		
		$elem_opts_od 		= $iolinkAscanRow["opts_od"];
		$elem_opts_od_other = $iolinkAscanRow["opts_od_other"];
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
		
		$elem_opts_os 		= $iolinkAscanRow["opts_os"];
		$elem_opts_os_other = $iolinkAscanRow["opts_os_other"];
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
		
		$elem_iol2dn_od 	= $iolinkAscanRow["iol2dn_od"];
		$elem_iol2dn_od_1 = $elem_iol2dn_od_2 = "";
		if($elem_iol2dn_od == "Yes"){
			$elem_iol2dn_od_1 = "checked";
		}
		if($elem_iol2dn_od == "No"){
			$elem_iol2dn_od_2 = "checked";
		}
		
		$elem_iol2dn_os 	= $iolinkAscanRow["iol2dn_os"];	
		$elem_iol2dn_os_1 = $elem_iol2dn_os_2 = "";
		if($elem_iol2dn_os == "Yes"){
			$elem_iol2dn_os_1 = "checked";
		}
		if($elem_iol2dn_os == "No"){
			$elem_iol2dn_os_2 = "checked";
		}		
		
		//end AScan variables

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
			$providerLensesArrOD[0] 		= getLenseNameAscan($iol1OD);
			$providerLensesODArr[$iol1OD] 	= getLenseNameAscan($iol1OD);
		}
		if($iol2OD!=''){
			$providerLensesArrOD[1]			= getLenseNameAscan($iol2OD);
			$providerLensesODArr[$iol2OD] 	= getLenseNameAscan($iol2OD);
		}
		if($iol3OD!=''){
			$providerLensesArrOD[2] 		= getLenseNameAscan($iol3OD);
			$providerLensesODArr[$iol3OD] 	= getLenseNameAscan($iol3OD);
		}
		if($iol4OD!=''){
			$providerLensesArrOD[3] 		= getLenseNameAscan($iol4OD);
			$providerLensesODArr[$iol4OD] 	= getLenseNameAscan($iol4OD);
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
			$providerLensesArrOS[0] 		= getLenseNameAscan($iol1OS);
			$providerLensesOSArr[$iol1OS] 	= getLenseNameAscan($iol1OS);
		}
		if($iol2OS!=''){
			$providerLensesArrOS[1] 		= getLenseNameAscan($iol2OS);
			$providerLensesOSArr[$iol2OS] 	= getLenseNameAscan($iol2OS);
		}
		if($iol3OS!=''){
			$providerLensesArrOS[2] 		= getLenseNameAscan($iol3OS);
			$providerLensesOSArr[$iol3OS] 	= getLenseNameAscan($iol3OS);
		}
		if($iol4OS!=''){
			$providerLensesArrOS[3] 		= getLenseNameAscan($iol4OS);
			$providerLensesOSArr[$iol4OS] 	= getLenseNameAscan($iol4OS);
		}
		
		$selecedIOLsOD				= $iolinkAscanRow["selecedIOLsOD"];		
		$lensesProviderArray 		= getLensesAscan($provider_idOD);
		if((($lensesProviderArray)) != (($providerLensesArrOD))){
			$lensesProviderArray = $providerLensesODArr;
		}
		$PlanOD = "";
		if(count($lensesProviderArray)>0){
			foreach($lensesProviderArray as $lensesId => $lenseNames){
				if($selecedIOLsOD==$lensesId) { $PlanOD = $lenseNames; }
			
			}
		}
		
		$lensesProviderArray = getLensesAscan($provider_idOS);
		if((($lensesProviderArray)) != (($providerLensesArrOS))){
			$lensesProviderOSArray = $providerLensesOSArr;
		}
		if($sign_path && ($sign_path_date_time!="0000-00-00 00:00:00" || $sign_path_date_time!=0)) {
			$sign_path_date = date("m-d-Y",strtotime($sign_path_date_time));
			$sign_path_time = date("h:i A",strtotime($sign_path_date_time));
		}		
		$sign_path_print='';
		if($sign_path!='') {
			
			$tmpDirPth_up = data_path();
			$sign_real_path=$tmpDirPth_up.$sign_path;
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
											<td style="border:1px solid #E5E4E2; background-color:#FFF;padding:3px; "><img src="'.$tmpDirPth_up.$sign_path.'" alt="sign" width="225" height="45" ></td>
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
		list($scanId_but,$scanId_prev) = getTestScan($pid,$iolink_ascan_form_id,"Ascan",$surgical_id);
		if(!empty($scanId_but) || !empty($scanId_prev)){
			$tw="130";
			if(empty($noP)){
				$winW="1140";
				$winH="775";
			}else{
				$winW="0";
				$winH="0";
			}
			$str = insertScans($scanId_but,$scanId_prev,$tw,$winW,$winH,"Ascan",true);
			$str = preg_replace('/No Current Scan/','',$str);
			if(isset($_REQUEST['doNotShowRightSide']) == true && empty($_REQUEST['doNotShowRightSide']) == false){
				$str = "";
			}
			
		}
				
		$rand=rand(0,500);
		$test_name="chart_tests";
		$ascan_html_file_name ='ascan_print_'.$_SESSION['authId'].'_'.$rand;
		$html_file_name = $ascan_html_file_name;
		
		//echo $GLOBALS["incdir"];  die();
		
		include_once($GLOBALS['fileroot'].'/interface/chart_notes/ascan_print.php');
		
		$patientDirAscan = "/PatientId_".$pid;
		$toMakePdfFor = "Iolink";
		
		
		$patientPrintData = html_entity_decode($testPrint,ENT_QUOTES|ENT_XHTML|ENT_HTML5,'ISO-8859-1');
		
		$fp = $data_dir.'/iOLink/'.$ascan_html_file_name.'.html';
		$putData = file_put_contents($fp,$patientPrintData);
		
		//fclose($fp);
		
		$patientPrintData="";
		$iolinkDirPath = $data_dir.'/iOLink/';//.'/addons/iOLink';	
		$patientDir = $patientDirAscan;
		//Create patient directory
		if(!is_dir($iolinkDirPath.$patientDir)){		
			mkdir($iolinkDirPath.$patientDir, 0755, true);
			chown($iolinkDirPath.$patientDir, 'apache');
		}
		
		$copyPdfFilePath = $iolinkDirPath.$patientDir."/AScan.pdf";
		$copyPdfFilePath = str_ireplace("\\","/",$copyPdfFilePath);
		if(file_exists($copyPdfFilePath)) {	
			unlink($copyPdfFilePath);
		}
		
		$arrProtocol = (explode("/",$_SERVER['SERVER_PROTOCOL']));
		$arrPathPart = pathinfo($_SERVER['PHP_SELF']);
		$arrPathPart = explode("/",($arrPathPart['dirname']));
		
		$dir = explode('/',$_SERVER['HTTP_REFERER']);
		$httpPro = $dir[0];
		$httpHost = $dir[2];
		$httpfolder = $dir[3];
		$ip = $_SERVER['REMOTE_ADDR'];
				
		$myHTTPAddress = $httpPro.'//'.$myInternalIP.'/'.$web_RootDirectoryName.'/library/html_to_pdf/iolinkMakePdf.php';
		$data1 = "";
		$curNew = curl_init();
		$urlPdfFile = $myHTTPAddress."?op=l&onePage=false&name=".$ascan_html_file_name."&pdf_name=".$copyPdfFilePath."&copyPathIolink=".$copyPdfFilePath."&images=".$ChartNoteImagesStringFinal."";
		//die($urlPdfFile);
		curl_setopt($curNew,CURLOPT_URL,$urlPdfFile);
		curl_setopt ($curNew, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt ($curNew, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($curNew, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curNew, CURLOPT_FOLLOWLOCATION, true); 
		$data1 = curl_exec($curNew);
		//print_r(curl_getinfo($curNew));
		curl_close($curNew);
		/*
		
		//$pdfSourceFile = $data_dir."/iOLink/".$ascan_html_file_name.".pdf";
		$pdfSourceFile = $webServerRootDirectoryName.$RootDirectoryName."/library/html_to_pdf/".$ascan_html_file_name.".pdf";
		//die($pdfSourceFile);
		$pdfFileContent = file_get_contents($pdfSourceFile);
		file_put_contents($copyPdfFilePath,$pdfFileContent);
		if(file_exists($copyPdfFilePath)) {
			if(file_exists($pdfSourceFile)) {
				unlink($pdfSourceFile);
			}
			if(file_exists($fp)) {
				unlink($fp);
			}
		}*/
		
		/* //START ADDING ASCAN UPLOADED PDF
		$ascanPdfQry 	= "SELECT scan_id,image_name,file_path FROM ".constant("IMEDIC_SCAN_DB").".scans WHERE patient_id = '".$pid."' AND test_id='".$surgical_id."' AND test_id!='0' AND image_form='Ascan' AND scan_or_upload='upload' ORDER BY scan_id DESC";
		$ascanPdfRes 	= imw_query($ascanPdfQry);
		$ascanPdfNumRow = imw_num_rows($ascanPdfRes);
		if($ascanPdfNumRow>0) {
			while($ascanPdfRow = imw_fetch_array($ascanPdfRes)) {
				$ascanPdfScanId 			= $ascanPdfRow['scan_id'];
				$ascanPdfFilePath 			= trim($ascanPdfRow['file_path']);
				$ascanPdfImageName 			= trim($ascanPdfRow['image_name']);
				
				$ascanPdfImageName 			= str_replace(" ","_",$ascanPdfImageName);
				$ascanPdfImageName 			= str_replace(",","",$ascanPdfImageName);
				$ascanPdfImageName 			= str_replace("!","",$ascanPdfImageName);
				$ascanPdfImageName 			= str_replace("@","",$ascanPdfImageName);
				$ascanPdfImageName 			= str_replace("%","",$ascanPdfImageName);
				$ascanPdfImageName 			= str_replace("^","",$ascanPdfImageName);
				$ascanPdfImageName 			= str_replace("$","",$ascanPdfImageName);
				$ascanPdfImageName 			= str_replace("'","",$ascanPdfImageName);
				$ascanPdfImageName 			= str_replace("*","",$ascanPdfImageName);
				
				
				$ascanPdfFileName 			= $ascanPdfScanId.'.pdf';
				if($ascanPdfImageName) {
					$ascanPdfFileName 		= str_ireplace('.pdf','',$ascanPdfImageName);
					$ascanPdfFileName 		= $ascanPdfFileName.'_'.$ascanPdfScanId.'.pdf';
				}
				if($ascanPdfFilePath) {
					if(end(explode(".",strtolower($ascanPdfFileName)))=="pdf") {
						$ascanDosPattern 			= date('Ymd',strtotime($sa_app_start_date));
						$ascanPdfFilePathSource = $data_dir.$ascanPdfFilePath;
						$ascanPdfFilePathDest 	= $iolinkDirPath.$patientDir."/AScan_".$ascanDosPattern."_".$ascanPdfFileName;	
						$ascanPdfFilePathDest 	= str_ireplace("\\","/",$ascanPdfFilePathDest);
						if(file_exists($ascanPdfFilePathSource)) {
							$ascanPdfFilePathContent = file_get_contents($ascanPdfFilePathSource);
							file_put_contents($ascanPdfFilePathDest,$ascanPdfFilePathContent);
						}
					}
				}
			}
		} */
		//END ADDING ASCAN UPLOADED PDF
	}	
}

?>