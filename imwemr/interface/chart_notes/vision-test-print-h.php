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

?><?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i")." GMT");  // HTTP 1.1
header("Cache-Control: no-store, no-cache, must-revalidate"); // ////////////////////////
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache"); 
//error_reporting(1);

if($toMakePdfFor == "Iolink"){
	$pid 		= $patient_id_from_iolink;
	$patient_id = $patient_id_from_iolink;
}else {
	$pid = $_SESSION['patient'];
	$patient_id = $_SESSION['patient'];
}
if($toMakePdfFor != "Iolink"){
	include_once(dirname(__FILE__)."/Functions.php");
}
/*
include_once(dirname(__FILE__)."/common_functions.php");	
include_once(dirname(__FILE__)."/../patient_access/common/config.php");	
include_once(dirname(__FILE__)."/../patient_access/common/functions.php");
$fdr_pat_img=dirname(__FILE__)."/../patient_access/patient_photos/";
include_once(dirname(__FILE__)."/main_functions.php");
include_once(dirname(__FILE__)."/chartNotesPrinting.php");
include_once(dirname(__FILE__)."/../admin/chart_more_functions.php");
include_once(dirname(__FILE__)."/../chart_notes/chartNotesSaveFunction.php");
include_once(dirname(__FILE__)."/../chart_notes/common/ChartApXml.php");
include_once(dirname(__FILE__)."/../chart_notes/iDoc-Drawing/CLSImageManipulation.php");
*/
//
include_once($GLOBALS['fileroot'].'/library/classes/ChartApXml.php');
include_once($GLOBALS['fileroot'].'/library/classes/work_view/Fu.php');
include_once($GLOBALS['fileroot'].'/library/classes/work_view/CLSImageManipulation.php');
include_once($GLOBALS['fileroot'].'/library/classes/work_view/CLSDrawingData.php');
include_once($GLOBALS['fileroot'].'/library/classes/pt_at_glance.class.php');

include_once($GLOBALS['fileroot'].'/interface/patient_info/complete_pt_rec/print_functions_class.php');

$objImageManipulation = new CLSImageManipulation();
$objCpr = new CmnFunc();
$objDrw = new CLSDrawingData();
$objPtgl = new Pt_at_glance();
$objFu = new Fu();
//$FormatDate_insert=$objCpr->FormatDate_insert($dt);
 
//
if($toMakePdfFor == "Iolink"){
	$pid 		= $patient_id_from_iolink;
	$patient_id = $patient_id_from_iolink;
}else {
	$pid = $_SESSION['patient'];
	$patient_id = $_SESSION['patient'];
}
/***********boston network specific code end***************************/
error_reporting(1);
ini_set("display_errors",1);
#form_id//
$yesNo = "no";
if(isset($_SESSION["form_id"]) && !empty($_SESSION["form_id"])){
	$form_id = $_SESSION["form_id"];	
	$finalize_flag = 0;		
}else if(isset($_SESSION["finalize_id"]) && !empty($_SESSION["finalize_id"])){	
	#form id
	$form_id = $_SESSION["finalize_id"];		
	$finalize_flag = 1;						
}
// IF PRINT THEN FORM ID
 $print_form_id = $_REQUEST['print_form_id'];
if($print_form_id!=""){
	$form_id = $print_form_id;
}
if($toMakePdfFor == "Iolink"){
	$form_id = $form_id_from_iolink; 
}	
######Audit For Patient Export PHI###############
if($toMakePdfFor != "Iolink"){
	$qryGetAuditPolicies = "select policy_status as plPHI from audit_policies where policy_id = 11";
	$rsGetAuditPolicies = imw_query($qryGetAuditPolicies);
	if($rsGetAuditPolicies){
		if(imw_num_rows($rsGetAuditPolicies)){
			extract(imw_fetch_array($rsGetAuditPolicies));		
		}
	}
	else{
		$phiError = "Error : ". imw_errno() . ": " . imw_error();
	}
	if($plPHI == 1){
		if($_SESSION['PHI_Audit']=="Noo"){
			$_SESSION['PHI_Audit']="Yess";
			$arrAuditTrailPHI = array();
			$opreaterId = $_SESSION['authId'];															 
			$ip = getRealIpAddr();
			$URL = $_SERVER['PHP_SELF'];													 
			$os = get_os_($_SERVER['HTTP_USER_AGENT']);
			$browserInfoArr = array();
			$browserInfoArr = _browser();
			$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
			$browserName = str_replace(";","",$browserInfo);													 
			$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);			
			$arrAuditTrailPHI [] = 
						array(
								"Pk_Id"=> $pid,		
								"Table_Name"=> "patient_data",				
								"Action"=> "phi_export",
								"Operater_Id"=> $opreaterId,
								"Operater_Type"=> getOperaterType($opreaterId) ,
								"IP"=> $ip,
								"MAC_Address"=> $_REQUEST['macaddrs'],
								"URL"=> $URL,
								"Browser_Type"=> $browserName,
								"OS"=> $os,
								"Machine_Name"=> $machineName,
								"Category"=> "chart_notes",
								"Category_Desc"=> $AuditEntryFor,	
								"Old_Value"=> $pid,
								"Depend_Select"=> "select CONCAT(CONCAT_WS(',',fname,lname),'(',id,')') as patientName" ,
								"Depend_Table"=> "patient_data" ,
								"Depend_Search"=> "id",
								"New_Value"=> $form_id																																										
							);																		
							//echo '<pre>';
							//print_r($arrAuditTrailPHI);
							//die;
			$table = array("audit_policies");
			$error = array($phiError);
			$mergedArray = mergingArray($table,$error);		
			auditTrail($arrAuditTrailPHI,$mergedArray,0,0,0);	
		}				
	}	
}
##############################################

// IF PRINT #THEN FORM ID
#####
//GET ReleaseNumber
$getRelNoQry = imw_query("SELECT releaseNumber FROM chart_master_table WHERE patient_id = '$patient_id' AND id = '$form_id'");
$getRelNoRow = imw_fetch_assoc($getRelNoQry);
$releaseNumber = $getRelNoRow['releaseNumber'];

$tdate=date("m-d-Y");
$ab = "select * from patient_data where id = $pid";		
$c = imw_query($ab);
$r1=imw_fetch_array($c);
$facility_id_p=$r1['default_facility'];
if($facility_id_p<>""){
	$query="select * from facility where id='$facility_id_p'";
	$result=imw_query($query);
	$rows=imw_fetch_array($result);
	$patient_facility="(".$rows['name'].")";
}
//$check_data="select * from lists where pid=$pid and type='3'";
$check_data = "select * from lists where pid=$pid and type in(3,7) and allergy_status = 'Active'";
$checkSql = @imw_query($check_data);
if(@imw_num_rows($checkSql)>0){
	while($allergy_row=imw_fetch_array($checkSql)){
		$allergy_array[]=$allergy_row["title"]; 
	}
	if(count($allergy_array)==1 && $allergy_array[0]=='NKDA'){
		$allergy="NKDA ";
	}else{
		$allergy="<font class='text_9b' color='red'>".implode(", ",$allergy_array)."</font>";
	}
}else{
	$allergy="<font class='text_9b'>NKDA</font>";
}
// Chart Vision
$id_chart_vis_master=0;
$sql = "SELECT *, c5.ex_desc as ex_desc_pam, c2.ex_desc as ex_desc_ak, c1.id AS id_chart_vis_master  
		FROM chart_vis_master c1
		LEFT JOIN chart_ak c2 ON c2.id_chart_vis_master = c1.id
		LEFT JOIN chart_exo c3 ON c3.id_chart_vis_master = c1.id
		LEFT JOIN chart_bat c4 ON c4.id_chart_vis_master = c1.id
		LEFT JOIN chart_pam c5 ON c5.id_chart_vis_master = c1.id
		WHERE c1.patient_id = '".$patient_id."' AND c1.form_id = '".$form_id."' ";
$row = sqlQuery($sql);
if($row!=false){
	$statusElements = $row["status_elements"];
	$id_chart_vis_master = $row["id_chart_vis_master"];
	
	//PAM --			
	$visPam = (strpos($statusElements, "elem_visPam=1") !== false) ? $row["pam"] : "";
	$vis_pam_od_txt_1 = (strpos($statusElements, "elem_visPamOdTxt1=1") !== false) ? $row["txt1_od"] : "";
	$vis_pam_os_txt_1 = (strpos($statusElements, "elem_visPamOsTxt1=1") !== false) ? $row["txt1_os"] : "";
	$vis_pam_ou_txt_1 = (strpos($statusElements, "elem_visPamOuTxt1=1") !== false) ? $row["txt1_ou"] : "";
	$vis_pam_od_sel_2 = (strpos($statusElements, "elem_visPamOdSel2=1") !== false) ? $row["sel2"] : "";
	$vis_pam_od_txt_2 = (strpos($statusElements, "elem_visPamOdTxt2=1") !== false) ? $row["txt2_od"] : "";
	$vis_pam_os_txt_2 = (strpos($statusElements, "elem_visPamOsTxt2=1") !== false) ? $row["txt2_os"] : "";
	$vis_pam_ou_txt_2 = (strpos($statusElements, "elem_visPamOuTxt2=1") !== false) ? $row["txt2_ou"] : "";
	$vis_pam_desc = (strpos($statusElements, "elem_pamDesc=1") !== false) ? $row["ex_desc_pam"] : "";
	//PAM --
	
	//BAT --
	$txt_vis_bat_nl_od = (strpos($statusElements, "elem_visBatNlOd=1") !== false)?$row["nl_od"]:"";
	$txt_vis_bat_low_od =(strpos($statusElements, "elem_visBatLowOd=1") !== false)? $row["l_od"]:"";
	$txt_vis_bat_med_od = (strpos($statusElements, "elem_visBatMedOd=1") !== false)? $row["m_od"]:"";
	$txt_vis_bat_high_od =(strpos($statusElements, "elem_visBatHighOd=1") !== false)?$row["h_od"]:"";
	$txt_vis_bat_nl_os = (strpos($statusElements, "elem_visBatNlOs=1") !== false)?$row["nl_os"]:"";
	$txt_vis_bat_low_os = (strpos($statusElements, "elem_visBatLowOs=1") !== false)?$row["l_os"]:"";
	$txt_vis_bat_med_os =(strpos($statusElements, "elem_visBatMedOs=1") !== false)?$row["m_os"]:"";
	$txt_vis_bat_high_os =(strpos($statusElements, "elem_visBatHighOs=1") !== false)?$row["h_os"]:"";
	
	$txt_vis_bat_nl_ou = (strpos($statusElements, "elem_visBatNlOu=1") !== false)?$row["nl_ou"]:"";
	$txt_vis_bat_low_ou = (strpos($statusElements, "elem_visBatLowOu=1") !== false)?$row["l_ou"]:"";
	$txt_vis_bat_med_ou =(strpos($statusElements, "elem_visBatMedOu=1") !== false)?$row["m_ou"]:"";
	$txt_vis_bat_high_ou =(strpos($statusElements, "elem_visBatHighOu=1") !== false)?$row["h_ou"]:"";
	$txt_vis_bat_desc =(strpos($statusElements, "elem_visBatDesc=1") !== false)?$row["vis_bat_desc"]:""; //removeExamDateStr($row[0]["vis_bat_desc"]);
	$txt_vis_bat_examdate = $row["examDateDistance"];//getExamDateStr($row[0]["vis_bat_desc"]);
	//BAT --
	
	//AK --
	$txt_vis_ak_od_k = (strpos($statusElements, "elem_visAkOdK=1") !== false)?$row["k_od"]:"";
	$txt_vis_ak_od_slash = (strpos($statusElements, "elem_visAkOdSlash=1") !== false)?$row["slash_od"]:"";
	$txt_vis_ak_od_x = (strpos($statusElements, "elem_visAkOdX=1") !== false)?$row["x_od"]:"";			
	$txt_vis_ak_os_k = (strpos($statusElements, "elem_visAkOsK=1") !== false)?$row["k_os"]:"";
	$txt_vis_ak_os_slash =(strpos($statusElements, "elem_visAkOsSlash=1") !== false)? $row["slash_os"]:"";
	$txt_vis_ak_os_x = (strpos($statusElements, "elem_visAkOsX=1") !== false)?$row["x_os"]:"";
	$txt_vis_ar_ak_desc =$row["ex_desc_ak"];//removeExamDateStr($row[0]["vis_ar_ak_desc"]) ;
	
	//Comments --
	//Check For old comments
	if(strpos($txt_vis_ar_ak_desc,"<~ED~>")!== false){
		$commentsArrayTmp=explode("<~ED~>",$txt_vis_ar_ak_desc);
		$txt_vis_ar_ak_desc=$commentsArrayTmp[0];
	}
	//Comments --
	//AK --		
}

//Acuity
$sql = "SELECT * FROM chart_vis_master c1
		LEFT JOIN chart_acuity c2 ON c2.id_chart_vis_master = c1.id
		WHERE c1.patient_id = '".$patient_id."' AND c1.form_id = '".$form_id."' 
		ORDER BY sec_indx
		";
$res = sqlStatement($sql);
for($i=1; $row=sqlFetchArray($res); $i++){

	$sec_name = $row["sec_name"];
	$sec_indx = $row["sec_indx"];
	if($sec_name == "Distance"){
		${"sel_vis_dis_od_sel_".$sec_indx} =(strpos($statusElements, "elem_visDisOdTxt".$sec_indx."=1") !== false)?$row["sel_od"]:"";
		${"txt_vis_dis_od_txt_".$sec_indx} =(strpos($statusElements, "elem_visDisOdTxt".$sec_indx."=1") !== false)?$row["txt_od"]:"";
		${"sel_vis_dis_os_sel_".$sec_indx} =(strpos($statusElements, "elem_visDisOsTxt".$sec_indx."=1") !== false)? $row["sel_os"]:"";
		${"txt_vis_dis_os_txt_".$sec_indx} =(strpos($statusElements, "elem_visDisOsTxt".$sec_indx."=1") !== false)? $row["txt_os"]:"";
		${"sel_vis_dis_ou_sel_".$sec_indx} =(strpos($statusElements, "elem_visDisOuTxt".$sec_indx."=1") !== false)? $row["sel_ou"]:"";
		${"txt_vis_dis_ou_txt_".$sec_indx} =(strpos($statusElements, "elem_visDisOuTxt".$sec_indx."=1") !== false)? $row["txt_ou"]:"";
		$txt_vis_dis_near_desc = (strpos($statusElements, "elem_visNearOdTxt".$sec_indx."=1") !== false || strpos($statusElements, "elem_visNearOsTxt".$sec_indx."=1") !== false)?$row["ex_desc"]:"";
		
		//Comments --
		//Check For old comments
		if(strpos($txt_vis_dis_near_desc,"<~ED~>")!== false){
			$commentsArrayTmp=explode("<~ED~>",$txt_vis_dis_near_desc);
			$txt_vis_dis_near_desc=$commentsArrayTmp[0];
		}
		//Comments --
		
	}else if($sec_name == "Near"){
		${"sel_vis_near_od_sel_".$sec_indx} =(strpos($statusElements, "elem_visNearOdTxt".$sec_indx."=1") !== false)? $row["sel_od"]:"";
		${"txt_vis_near_od_txt_".$sec_indx} = (strpos($statusElements, "elem_visNearOdTxt".$sec_indx."=1") !== false)?$row["txt_od"]:"";				
		${"sel_vis_near_os_sel_".$sec_indx} = (strpos($statusElements, "elem_visNearOsTxt".$sec_indx."=1") !== false)?$row["sel_os"]:"";
		${"txt_vis_near_os_txt_".$sec_indx} = (strpos($statusElements, "elem_visNearOsTxt".$sec_indx."=1") !== false)?$row["txt_os"]:"";
		${"sel_vis_near_ou_sel_".$sec_indx} =(strpos($statusElements, "elem_visNearOuTxt".$sec_indx."=1") !== false)? $row["sel_ou"]:"";
		${"txt_vis_near_ou_txt_".$sec_indx} =(strpos($statusElements, "elem_visNearOuTxt".$sec_indx."=1") !== false)? $row["txt_ou"]:"";
		$txt_vis_near_desc =(strpos($statusElements, "elem_visNearDesc=1") !== false)?$row["ex_desc"]:"";// removeExamDateStr($row["vis_near_desc"]);
		$txt_vis_near_examdate = $row["examDateDistance "];
	
	}else if($sec_name == "Ad. Acuity"){
		$vis_dis_od_sel_3=(strpos($statusElements, "elem_visDisOdSel3=1") !== false) ? $row["sel_od"]: "";
		$vis_dis_od_txt_3=(strpos($statusElements, "elem_visDisOdTxt3=1") !== false) ? $row["txt_od"]: "";
		$vis_dis_os_sel_3=(strpos($statusElements, "elem_visDisOsSel3=1") !== false) ? $row["sel_os"]: "";
		$vis_dis_os_txt_3=(strpos($statusElements, "elem_visDisOsTxt3=1") !== false) ? $row["txt_os"]: "";
		$vis_dis_ou_sel_3=(strpos($statusElements, "elem_visDisOuSel3=1") !== false) ? $row["sel_ou"]: "";
		$vis_dis_ou_txt_3=(strpos($statusElements, "elem_visDisOuTxt3=1") !== false) ? $row["txt_ou"]: ""; 
		$vis_dis_act_3  =(strpos($statusElements, "elem_visDisAct3=1") !== false) ? htmlentities($row["ex_desc"]): "";
	}
}

if(!empty($id_chart_vis_master)){
	//sca
	$sql = "SELECT * FROM chart_sca WHERE id_chart_vis_master='".$id_chart_vis_master."' ";
	$res = sqlStatement($sql);
	for($i=1;$row=sqlFetchArray($res);$i++){
		
		$sec_name = $row["sec_name"];
		if($sec_name == "AR"){
		$txt_vis_ar_od_s =(strpos($statusElements, "elem_visArOdS=1") !== false)? $row["s_od"]:"";
		$txt_vis_ar_od_c =(strpos($statusElements, "elem_visArOdC=1") !== false)? $row["c_od"]:"";
		$txt_vis_ar_od_a = (strpos($statusElements, "elem_visArOdA=1") !== false)?$row["a_od"]:"";
		$sel_vis_ar_od_sel_1 =(strpos($statusElements, "elem_visArOdS=1") !== false)? $row["sel_od"]:"";
		$txt_vis_ar_os_s =(strpos($statusElements, "elem_visArOsS=1") !== false)? $row["s_os"]:"";
		$txt_vis_ar_os_c = (strpos($statusElements, "elem_visArOsC=1") !== false)?$row["c_os"]:"";
		$txt_vis_ar_os_a = (strpos($statusElements, "elem_visArOsA=1") !== false)?$row["a_os"]:"";
		$sel_vis_ar_os_sel_1 = (strpos($statusElements, "elem_visArOsA=1") !== false)?$row["sel_os"]:"";
		}else if($sec_name == "ARC"){
		$visCycArOdS =(strpos($statusElements, "elem_visArOdS=1") !== false)? $row["s_od"]:"";
		$visCycArOdC =(strpos($statusElements, "elem_visArOdC=1") !== false)? $row["c_od"]:"";
		$visCycArOdA = (strpos($statusElements, "elem_visArOdA=1") !== false)?$row["a_od"]:"";
		$visCycArOdSel1 =(strpos($statusElements, "elem_visArOdS=1") !== false)? $row["sel_od"]:"";
		$visCycArOsS =(strpos($statusElements, "elem_visArOsS=1") !== false)? $row["s_os"]:"";
		$visCycArOsC = (strpos($statusElements, "elem_visArOsC=1") !== false)?$row["c_os"]:"";
		$visCycArOsA = (strpos($statusElements, "elem_visArOsA=1") !== false)?$row["a_os"]:"";
		$visCycArOsSel1 = (strpos($statusElements, "elem_visArOsA=1") !== false)?$row["sel_os"]:"";	
			
		}
	
	}
	
	//PC/MR
	$sql = "SELECT 
			c1.*,
			c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, c2.prsm_p as prsm_p_r, c2.prism as prism_r, c2.slash as slash_r, c2.sel_1 as sel_1_r, c2.sel_2 as sel_2_r, 
			c2.txt_1 as txt_1_r, c2.txt_2 as txt_2_r, c2.sel2v as sel2v_r,
			c2.ovr_s as ovr_s_r, c2.ovr_c as ovr_c_r, c2.ovr_v as ovr_v_r, c2.ovr_a as ovr_a_r,			
			c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l, c3.prsm_p as prsm_p_l, c3.prism as prism_l, c3.slash as slash_l, c3.sel_1 as sel_1_l, c3.sel_2 as sel_2_l, 
			c3.txt_1 as txt_1_l, c3.txt_2 as txt_2_l, c3.sel2v as sel2v_l,
			c3.ovr_s as ovr_s_l, c3.ovr_c as ovr_c_l, c3.ovr_v as ovr_v_l, c3.ovr_a as ovr_a_l,
			c4.status_elements as vis_statusElements
			FROM chart_vis_master c4
			LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c4.id
			LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
			LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'				
			WHERE c4.form_id='".$form_id."' AND c4.patient_id = '".$patient_id."' AND c1.ex_type='PC' AND c1.delete_by='0'  
			Order By ex_number;
			";
	$rez = sqlStatement($sql);
	for($i=0; $row= sqlFetchArray($rez); $i++){
		$ex_num = $row["ex_number"];
		
		if($ex_num == "1"){ 
			$ex_num = ""; 
			$indx1 = "";
		}else{
			$indx1 = "_".$ex_num;
		}
		
		//Pc---
		$statusElements = $row["vis_statusElements"];
		${"chk_pc_near".$indx1} = $row["pc_near"];

		${"sel_vis_pc_od_sel_1".$indx1} = (strpos($statusElements, "elem_visPcOdSel1".$ex_num."=1") !== false)?$row["sel_1_r"]:"";
		${"txt_vis_pc_od_s".$indx1} = (strpos($statusElements, "elem_visPcOdS".$ex_num."=1") !== false)?$row["sph_r"]:"";
		${"txt_vis_pc_od_c".$indx1} = (strpos($statusElements, "elem_visPcOdC".$ex_num."=1") !== false)?$row["cyl_r"]:"";
		${"txt_vis_pc_od_a".$indx1} =(strpos($statusElements, "elem_visPcOdA".$ex_num."=1") !== false)?$row["axs_r"]:"";

		${"sel_vis_pc_od_p".$indx1} =(strpos($statusElements, "elem_visPcOsP".$ex_num."=1") !== false)? $row["prsm_p_r"]:"";
		${"sel_vis_pc_od_prism".$indx1} =(strpos($statusElements, "elem_visPcOdPrism".$ex_num."=1") !== false)? $row["prism_r"]:"";
		${"sel_vis_pc_od_slash".$indx1} = (strpos($statusElements, "elem_visPcOdSlash".$ex_num."=1") !== false)?$row["slash_r"]:"";
		${"sel_vis_pc_od_sel_2".$indx1} = (strpos($statusElements, "elem_visPcOdSel2".$ex_num."=1") !== false)?$row["sel_2_r"]:"";

		${"sel_vis_pc_os_sel_1".$indx1} = (strpos($statusElements, "elem_visPcOsSel1".$ex_num."=1") !== false)?$row["sel_1_l"]:"";
		${"txt_vis_pc_os_s".$indx1} = (strpos($statusElements, "elem_visPcOsS".$ex_num."=1") !== false)?$row["sph_l"]:"";
		${"txt_vis_pc_os_c".$indx1} =(strpos($statusElements, "elem_visPcOsC".$ex_num."=1") !== false)? $row["cyl_l"]:"";
		${"txt_vis_pc_os_a".$indx1} = (strpos($statusElements, "elem_visPcOsA".$ex_num."=1") !== false)?$row["axs_l"]:"";
		${"sel_vis_pc_os_p".$indx1} = (strpos($statusElements, "elem_visPcOsP".$ex_num."=1") !== false)?$row["prsm_p_l"]:"";
		${"sel_vis_pc_os_prism".$indx1} = (strpos($statusElements, "elem_visPcOsPrism".$ex_num."=1") !== false)?$row["prism_l"]:"";
		${"sel_vis_pc_os_slash".$indx1} = (strpos($statusElements, "elem_visPcOsSlash".$ex_num."=1") !== false)?$row["slash_l"]:"";
		${"sel_vis_pc_os_sel_2".$indx1} =(strpos($statusElements, "elem_visPcOsSel2".$ex_num."=1") !== false)? $row["sel_2_l"]:"";
		
		${"txt_vis_pc_od_near_txt".$indx1} = $row["txt_1_r"];
		${"txt_vis_pc_os_near_txt".$indx1} = $row["txt_1_l"];											

		${"txt_vis_pc_od_overref_s".$indx1} = (strpos($statusElements, "elem_visPcOdOverrefS".$ex_num."=1") !== false)?$row["ovr_s_r"]:"";
		${"txt_vis_pc_od_overref_c".$indx1} = (strpos($statusElements, "elem_visPcOdOverrefC".$ex_num."=1") !== false)?$row["ovr_c_r"]:"";
		${"txt_vis_pc_od_overref_a".$indx1} = (strpos($statusElements, "elem_visPcOdOverrefA".$ex_num."=1") !== false)?$row["ovr_a_r"]:"";
		${"txt_vis_pc_od_overref_v".$indx1} = (strpos($statusElements, "elem_visPcOdOverrefV".$ex_num."=1") !== false)?$row["ovr_v_r"]:"";
		${"txt_vis_pc_os_overref_s".$indx1} = (strpos($statusElements, "elem_visPcOsOverrefS".$ex_num."=1") !== false)?$row["ovr_s_l"]:"";
		${"txt_vis_pc_os_overref_c".$indx1} = (strpos($statusElements, "elem_visPcOsOverrefC".$ex_num."=1") !== false)?$row["ovr_c_l"]:"";
		${"txt_vis_pc_os_overref_a".$indx1} = (strpos($statusElements, "elem_visPcOsOverrefA".$ex_num."=1") !== false)?$row["ovr_a_l"]:"";
		${"txt_vis_pc_os_overref_v".$indx1} = (strpos($statusElements, "elem_visPcOsOverrefV".$ex_num."=1") !== false)?$row["ovr_v_l"]:"";
		
		${"txt_vis_pc_desc".$ex_num}=$row["ex_desc"];//
		${"txt_vis_pc_od_add".$indx1} = (strpos($statusElements, "elem_visPcOdAdd".$ex_num."=1") !== false)?$row["ad_r"]:"";
		${"txt_vis_pc_os_add".$indx1} =(strpos($statusElements, "elem_visPcOsAdd".$ex_num."=1") !== false)? $row["ad_l"]:"";		
	}
	
	$sql = "SELECT 
		c1.*,
		c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, c2.prsm_p as prsm_p_r, c2.prism as prism_r, c2.slash as slash_r, c2.sel_1 as sel_1_r, c2.sel_2 as sel_2_r, 
		c2.txt_1 as txt_1_r, c2.txt_2 as txt_2_r, c2.sel2v as sel2v_r,
					
		c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l, c3.prsm_p as prsm_p_l, c3.prism as prism_l, c3.slash as slash_l, c3.sel_1 as sel_1_l, c3.sel_2 as sel_2_l, 
		c3.txt_1 as txt_1_l, c3.txt_2 as txt_2_l, c3.sel2v as sel2v_l,
		
		c4.status_elements 
		FROM chart_vis_master c4  
		LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c4.id
		LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
		LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'				
		WHERE c4.form_id='".$form_id."' AND c4.patient_id = '".$patient_id."' AND c1.ex_type='MR' AND c1.ex_number IN (1,2) AND c1.delete_by='0'  
		Order By ex_number;
		";
		
	$rez = sqlStatement($sql);		
	for($i=0; $row= sqlFetchArray($rez); $i++){
		$ret_str="";
		$ex_num = $row["ex_number"];
		
		$indx1=$indx2=$indx3="";
		if($ex_num>1){
			$indx1="Other";
			$indx3="_given";
			if($ex_num>2){
				$indx2="_".$ex_num;	
			}
		}
		
		$statusElements = $row["status_elements"];
		$rd_vis_mr_none_given=(strpos($statusElements, "elem_mrNoneGiven".$ex_num."=1")!== false)?$row["mr_none_given"] : "" ;
		$providerIdOther_3=$row["provider_id"];
		$vis_mr_desc_3COMMENTS=$row["ex_desc"];
		$vis_mr_prism3COMMENTS=$row["prism_desc"];
		
		${"txt_vis_mr_od".$indx3."_s"}=(strpos($statusElements, "elem_visMr".$indx1."OdS".$indx2."=1") !== false)?$row["sph_r"] : "" ;
		${"txt_vis_mr_od".$indx3."_c"}=(strpos($statusElements, "elem_visMr".$indx1."OdC".$indx2."=1") !== false)?$row["cyl_r"] : "" ;
		${"txt_vis_mr_od".$indx3."_a"}=(strpos($statusElements, "elem_visMr".$indx1."OdA".$indx2."=1") !== false)?$row["axs_r"] : "" ;
		${"txt_vis_mr_od".$indx3."_add"}=(strpos($statusElements, "elem_visMr".$indx1."OdAdd".$indx2."=1") !== false)?$row["ad_r"] : "" ;
		${"txt_vis_mr_od".$indx3."_txt_1"}=(strpos($statusElements, "elem_visMr".$indx1."OdTxt1".$indx2."=1") !== false)?$row["txt_1_r"] : "" ;			
		${"txt_vis_mr_od".$indx3."_txt_2"}=(strpos($statusElements, "elem_visMr".$indx1."OdTxt2".$indx2."=1") !== false)?$row["txt_2_r"] : "" ;
		
		
		
		${"sel_vis_mr_od".$indx3."_p"}=((strpos($statusElements, "elem_visMr".$indx1."OdP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdSel1".$indx2."=1") !== false)
						|| (strpos($statusElements, "elem_visMr".$indx1."OdSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdPrism".$indx2."=1") !== false)
						|| (strpos($statusElements, "elem_visMr".$indx1."OdTxt1".$indx2."=1") !== false)
						)?$row["prsm_p_r"] : "" ;
		${"sel_vis_mr_od".$indx3."_prism"}=((strpos($statusElements, "elem_visMr".$indx1."OdP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdSel1".$indx2."=1") !== false)
							|| (strpos($statusElements, "elem_visMr".$indx1."OdSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdPrism".$indx2."=1") !== false)
							)?$row["prism_r"] : "" ;
		${"sel_vis_mr_od".$indx3."_slash"}=((strpos($statusElements, "elem_visMr".$indx1."OdP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdSel1".$indx2."=1") !== false)
							|| (strpos($statusElements, "elem_visMr".$indx1."OdSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdPrism".$indx2."=1") !== false)
							)?$row["slash_r"] : "" ;
		${"sel_vis_mr_od".$indx3."_sel_1"}=((strpos($statusElements, "elem_visMr".$indx1."OdP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdSel1".$indx2."=1") !== false)
							|| (strpos($statusElements, "elem_visMr".$indx1."OdSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdPrism".$indx2."=1") !== false)
							)?$row["sel_1_r"] : "" ;
		${"sel_vis_mr_od".$indx3."_sel_2"}=(strpos($statusElements, "elem_visMr".$indx1."OdSel2".$indx2."=1") !== false)?$row["sel_2_r"] : "" ;
		//$visMrOtherOdSel2Vision_3=(strpos($statusElements, "elem_visMr".$indx1."OdSel2Vision".$indx2."=1") !== false)?$row["sel2v_r"] : "" ;
		
		${"txt_vis_mr_os".$indx3."_s"}=(strpos($statusElements, "elem_visMr".$indx1."OsS".$indx2."=1") !== false)?$row["sph_l"] : "" ;
		${"txt_vis_mr_os".$indx3."_c"}=(strpos($statusElements, "elem_visMr".$indx1."OsC".$indx2."=1") !== false)?$row["cyl_l"] : "" ;
		${"txt_vis_mr_os".$indx3."_a"}=(strpos($statusElements, "elem_visMr".$indx1."OsA".$indx2."=1") !== false)?$row["axs_l"] : "" ;
		
		${"txt_vis_mr_os".$indx3."_add"}=(strpos($statusElements, "elem_visMr".$indx1."OsAdd".$indx2."=1") !== false)?$row["ad_l"] : "" ;
		${"txt_vis_mr_os".$indx3."_txt_1"}=(strpos($statusElements, "elem_visMr".$indx1."OsTxt1".$indx2."=1") !== false)?$row["txt_1_l"] : "" ;
		${"txt_vis_mr_os".$indx3."_txt_2"}=(strpos($statusElements, "elem_visMr".$indx1."OsTxt2".$indx2."=1") !== false)?$row["txt_2_l"] : "" ;			
		
		${"sel_vis_mr_os".$indx3."_p"}=((strpos($statusElements, "elem_visMr".$indx1."OsP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsSel1".$indx2."=1") !== false)
							|| (strpos($statusElements, "elem_visMr".$indx1."OsSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsPrism".$indx2."=1") !== false)
						)?$row["prsm_p_l"] : "" ;
		${"sel_vis_mr_os".$indx3."_prism"}=((strpos($statusElements, "elem_visMr".$indx1."OsP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsSel1".$indx2."=1") !== false)
							|| (strpos($statusElements, "elem_visMr".$indx1."OsSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsPrism".$indx2."=1") !== false)
						)?$row["prism_l"] : "" ;
		${"sel_vis_mr_os".$indx3."_slash"}=((strpos($statusElements, "elem_visMr".$indx1."OsP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsSel1".$indx2."=1") !== false)
							|| (strpos($statusElements, "elem_visMr".$indx1."OsSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsPrism".$indx2."=1") !== false)
						)?$row["slash_l"] : "" ;
		${"sel_vis_mr_os".$indx3."_sel_1"}=((strpos($statusElements, "elem_visMr".$indx1."OsP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsSel1".$indx2."=1") !== false)
							|| (strpos($statusElements, "elem_visMr".$indx1."OsSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsPrism".$indx2."=1") !== false)
							)?$row["sel_1_l"] : "" ;
		${"sel_vis_mr_os".$indx3."_sel_2"}=(strpos($statusElements, "elem_visMr".$indx1."OsSel2".$indx2."=1") !== false)?$row["sel_2_l"] : "" ;
		//$visMrOtherOsSel2Vision_3=(strpos($statusElements, "elem_visMr".$indx1."OsSel2Vision".$indx2."=1") !== false)?$row["sel2v_l"] : "" ;		
		//$elem_mr_type3 = (strpos($statusElements, "elem_mr_type".$ex_num."=1") !== false && !empty($row["vis_mr_type1"])) ?$row["mr_type"] : "" ;
		//if(!empty($elem_mr_type3)){ $elem_mr_type3 = "(".ucfirst($elem_mr_type3).")"; } 		
	}
}
//End Chart Vision
///* Code For CC History*//
$qry=imw_query("select * from patient_data where id=$patient_id");
$co=imw_num_rows($qry);
if($co>0){
	$pdata=imw_fetch_array($qry);
	$sex=$pdata['sex'];
	$dob=$pdata['DOB'];
	$dob1=explode("-",$dob);
	$y=$dob1[0];
	$cy=date('Y');
	$years=$cy-$y;
	$text_data="A $years years old $sex with history of $rvs";
}
#echo $patient_id;		

//End of Code For CC History//
//Code to show Ocular medication Data*//

//Chart Left Provider	
$qry=imw_query("select * from chart_left_provider_issue where patient_id='$pid' and form_id='$form_id'");
$co=imw_num_rows($qry);

if(($co>0)){
	//Update Records
	$prow=imw_fetch_array($qry);
	$idChartLeftProviderIssue = $prow["pr_is_id"];				
}
//End Chart Left Provider	

//print_r($prow);
//* End of Code to Show Ocular Medication data/*
/* 

// Chart Optic
$sql_optic = "SELECT * FROM chart_optic WHERE patient_id = '$patient_id' AND form_id = '$form_id' ";
$row_optic = sqlQuery($sql_optic);	

if(($row_optic == false)){
	//No Record
}else{	
	//Set Default When Old Record	
	$op_mode = "update";
	$op_edid = $row_optic["optic_id"];
	$idOptic = $row_optic["optic_id"];
	$txt_od_text = $row_optic["od_text"];
	$txt_os_text = $row_optic["os_text"];			
	$hd_examined_no_change = $row_optic["examined_no_change"];
	$opticNerveOd = $row_optic["optic_nerve_od"];
	$opticNerveOs = $row_optic["optic_nerve_os"];
	$rvopticod_summaryLabel = $row_optic["optic_nerve_od_summary"]; //($row["optic_nerve_od_summary"] != "") ? $row["optic_nerve_od_summary"] : "WNL";
	$rvopticos_summaryLabel = $row_optic["optic_nerve_os_summary"]; //($row["optic_nerve_os_summary"] != "") ? $row["optic_nerve_os_summary"] : "WNL";					
	$todate = formatDate4display($row_optic["exam_date"]);
	$optic_not_applicable = $row_optic["not_applicable"];	
}
//End Chart Optic
/*end of Code to show chart_optic data*/
/***Code To Pupil Tab Data**/
#####

$sql = "SELECT * FROM chart_pupil WHERE patientId = '$patient_id' AND formId = '$form_id' ";
$row = sqlQuery($sql);
//echo($sql);
if(($row == false) && ($finalize_flag == 0)){
	//No Data	
}else{	
	$pupil_not_applicable = $row["notApplicable"];
	$hd_wnl = $row["wnl"];			
	$todate = formatDate4display($row["examDate"]);
	$apdMinusOd = $row["apdMinusOd"];
	$elem_apdMinusOdSummary = $row["apdMinusOdSummary"];
	$apdMinusOs = $row["apdMinusOs"];
	$elem_apdMinusOsSummary = $row["apdMinusOsSummary"];	
	$apdPlusOd = $row["apdPlusOd"];
	$elem_apdPlusOdSummary = $row["apdPlusOdSummary"];
	$apdPlusOs = $row["apdPlusOs"];
	$elem_apdPlusOsSummary = $row["apdPlusOsSummary"];
	$reactionOd = $row["reactionOd"];
	$elem_reactionOdSummary = $row["reactionOdSummary"];						
	$reactionOs = $row["reactionOs"];
	$elem_reactionOsSummary = $row["reactionOsSummary"];	
	$shapeOd = $row["shapeOd"];
	$elem_shapeOdSummary = $row["shapeOdSummary"];	
	$shapeOs = $row["shapeOs"];
	$elem_shapeOsSummary = $row["shapeOsSummary"];
	$idPupilExam=$row["pupil_id"];								
}
//Code to Show Paitine Image
$p_imagename = $patientDetails[0]['p_imagename'];
if($p_imagename){
	$dirPath = dirname(__FILE__).'/../main/uploaddir'.$p_imagename;
	$dir_real_path = realpath($dirPath);
	$img_name = substr($p_imagename,strrpos($p_imagename,'/')+1);	
	copy($dir_real_path,'html2pdfprint/'.$img_name);
		$dirPath = $img_name;
		if(file_exists($dir_real_path)){
		$patient_img['patient'] = $img_name;
		$fileSize = getimagesize($dir_real_path);
		if($fileSize[0]>80 || $fileSize[0]>90){
			//START SET FUNCTION FOR IOLINK
			if($toMakePdfFor == "Iolink"){
				$imageWidth2 = imageResize_ocular($fileSize[0],$fileSize[1],90);
			}else {
				$imageWidth2 = ManageData::imageResize($fileSize[0],$fileSize[1],90);
			}
			//END SET FUNCTION FOR IOLINK
			$patientImage = '<img style="cursor:pointer" src="'.$dirPath.'" alt="patient Image" '.$imageWidth2.'>';
		}
		else{
			$patientImage = '<img style="cursor:pointer" src="'.$dirPath.'" alt="patient Image">';
		}		
	}
}
//End Code to Show Patient Image//
// IF PRINT THEN FORM ID

	$qry1=imw_query("select * from  chart_left_cc_history where patient_id='$pid' and form_id='$form_id'");
	$co=imw_num_rows($qry1);
	if(($co > 0)){
		$crow=imw_fetch_array($qry1);
		$date_of_service = date("m-d-Y", strtotime($crow["date_of_service"]));	
	 }
/////End date of sevice Code////////////////

//START GET PROVIDER SIGNATURE
	$chartOcularSignPath = $chartOcularSignFullPath = "";
	$qry2=imw_query("select *, DATE_FORMAT(sign_coords_dateTime, '".get_sql_date_format('','Y','-')." %h:%i %p') AS chartOcularSignDateTime from  chart_signatures where form_id = '".$form_id."' AND sign_type = '1' ");
	$co2=imw_num_rows($qry2);
	if(($co2 > 0)){
		$crow2=imw_fetch_array($qry2);
		$chartOcularProId 			= $crow2["pro_id"];
		$chartOcularSignPath		= trim($crow2["sign_path"]);
		$chartOcularSignDateTime 	= $crow2["chartOcularSignDateTime"];		
	 }
//END GET PROVIDER SIGNATURE

header("Content-Type: text/html; charset=utf-8");
ob_start();
//***88End Code To show pupil Tab Data**/
//HeadingTableHr() 


?>

<table style="width:740px;font-size:10px;"  cellspacing="0" rules="none" cellpadding="0">
 <tr>
			<td style="width:340px;" class="text_10"  ><?php if($date_of_service){ print '<b>DOS:&nbsp;'.$date_of_service."</b>";} else '&nbsp;'; ?> <br/>
			<b><?php print $patientName; ?> - <?php print $patientDetails['id']; ?> </b><br/>
			<?php print $patientDetails['sex'].'&nbsp;'.($age).'&nbsp;'.$date_of_birth; ?><br/>
			<?php print $patientDetails['street']; ?><br/>
			<?php if($patientDetails['street2']){ print $patientDetails['street2'].'<br/>'; } ?>
			<?php print $patientDetails['city']."&nbsp;".$patientDetails['state']."&nbsp;".$patientDetails['postal_code']; ?><br/>
			Ph. #: <?php print core_phone_format($patientDetails['phone_home']); ?> <br/><br/>
			</td> 
			<!--<td style="width:125px;" class="text_10" align="right">Practice&nbsp;Name:</td> -->
			<td style="width:200px;" align="center" ><?php print $patientImage; ?></td>
			<td style="width:250px;" class="text_10" align="left">
				<b><?php print $groupDetails[0]['name']; ?></b> <br/>
				<?php print ucwords($groupDetails[0]['group_Address1']).' '; ?><br/>
				<?php print ucwords($groupDetails[0]['group_Address2']).' '; ?><br/>
				<?php print $groupDetails[0]['group_City'].', '.$groupDetails[0]['group_State'].' '.$groupDetails[0]['group_Zip']; ?><br/>
				Ph. #: <?php print core_phone_format($groupDetails[0]['group_Telephone']); ?><br/>Fax #: <?php print $groupDetails[0]['group_Fax']; ?> <br/><br/>
			</td> 
  </tr>
		<!--<tr>
			<td align="left" class="text_10" >&nbsp;</td>
			<td rowspan="6" valign="top" class="text_10" align="right">Address:</td>	
			
			<td align="left" class="text_10"></td>						
		</tr>
	
		<tr>
			<td></td>
			
			<td align="left" class="text_10">Ph.<b> # <?php //print core_phone_format($groupDetails[0]['group_Telephone']); ?></b>&nbsp;Fax<b> # <?php //print $groupDetails[0]['group_Fax']; ?></b></td>
		</tr> -->
		
</table> 
<!--
 <table style="width:740px;font-size:10px;" border="0" cellspacing="0" rules="none" cellpadding="0">
 <tr>
			<td style="width:175px;" class="text_10b" ><b><?php //print $patientDetails[0]['title'].' '.$patientName; ?></b></td>
			<td style="width:125px;" class="text_10" align="right"  valign="top">ID :</td>
			<td style="width:125px;" class="text_10b" align="left"  valign="top"><b><?php //print $patientDetails[0]['id']; ?></b></td>
			<td style="width:275px;" align="center" rowspan="3"><?php //print $patientImage; ?></td>
  </tr>
		<tr>
			<td align="left" class="text_10" rowspan="3" valign="top">Address : <b><?php //print $patientDetails[0]['street']."&nbsp;". $patientDetails[0]['street2']."<br>".$patientDetails[0]['city']."&nbsp;".$patientDetails[0]['state']."&nbsp;".$patientDetails[0]['postal_code']; ?></b></td>
			<td align="right" class="text_10" > DOB: </td>
			<td width="43%" class="text_10b" align="left"><b><?php //print $date_of_birth." ($age)"; ?></b></td>
							
			
									
		</tr>
		<tr> 
			<td align="right" class="text_10" valign="top" >Sex:</td>	
			<td align="left" class="text_10b" valign="top"> <b><?php //print $patientDetails[0]['sex']; ?></b></td>		
			
			
		</tr>
		
</table> -->
<!-- Code Ro Add Allergies,Medication,Surgries-->
<?php 

//Get Allergies
$getAllergies = "select * from lists where pid = '$pid' and type in(3,7)  and allergy_status = 'Active' order by id";
$rsGetAllergies = imw_query($getAllergies);

if (imw_num_rows($rsGetAllergies) > 0) {	
?>
<table style="width:740px;"  class="border"cellpadding="2" cellspacing="0">	
	<tr bgcolor="#c0c0c0">
		<td style="width:740px;" bgcolor="#c0c0c0"  colspan="6" class="text_10b tb_heading" ><b>Allergies</b></td>				
	</tr>	
	<tr>
		<td class="text_10 text_lable bdrbtm bdrright" style="width:100px;" nowrap valign="top"><b>Drug</b></td>
		<td class="text_10 text_lable bdrbtm bdrright" style="width:150px;" nowrap valign="top"><b>Name</b></td>
		<td class="text_10 text_lable bdrbtm bdrright" style="width:125px;" nowrap valign="top"><b>Begin Date</b></td>		
		<td class="text_10 text_lable bdrbtm bdrright" style="width:100px;"  nowrap valign="top"><b>Acute</b></td>
		<td class="text_10 text_lable bdrbtm bdrright" style="width:100px;" nowrap valign="top"><b>Chronic</b></td>
		<td class="text_10 text_lable bdrbtm " style="width:125px;" nowrap valign="top"><b>Reactions / Comments</b></td>
	</tr>
	<?php
	while($row = imw_fetch_assoc($rsGetAllergies)){ 
		?>
		<tr>        
			<?php
            $drugName = '';
            if($row["ag_occular_drug"] == "fdbATDrugName"){
                $drugName = 'Drug';
            }		
            elseif($row["ag_occular_drug"] == "fdbATIngredient"){
                $drugName = 'Ingredient';
            }
            elseif($row["ag_occular_drug"] == "fdbATAllergenGroup"){
                $drugName = 'Allergen';
            }
            ?>
			<td class="text_10 bdrbtm bdrright pdl5" valign="top" style="width:100px;"><?php print $drugName; ?></td>
			<td class="text_10 bdrbtm bdrright" valign="top" style="width:150px;"><?php echo wordwrap($row["title"],30,'<br>'); ?></td>
			<td class="text_10 bdrbtm bdrright" valign="top" style="width:125px;"><?php echo isDate($row["begdate"]); ?></td>
			<td class="text_10 bdrbtm bdrright" valign="top" style="width:100px;"><?php echo ucwords($row["acute"]); ?></td>
			<td class="text_10 bdrbtm bdrright" valign="top" style="width:100px;"><?php echo ucwords($row["chronic"]); ?></td>
			<td class="text_10 bdrbtm " valign="top" style="width:125px;">
            	<?php
					$recComment = ""; 
					if(trim($row["reactions"]) != ""){
						$recComment .= ucwords($row["reactions"]);
						if(trim($row["comments"]) != ""){
							$recComment .= " / ".ucwords($row["comments"]);
						}
					}	
					else{				
						$recComment .= ucwords($row["comments"]);
					}
					echo $recComment;
				?>
			</td>
		</tr>
		<?php
	}
	?>
</table>
<?php  
}

//Get Surgeries
$getSurgeries = "select *, 
												if((DAY(STR_TO_DATE(begdate,'%Y-%m-%d'))='00' OR DAY(STR_TO_DATE(begdate,'%Y-%m-%d'))='0') && (YEAR(STR_TO_DATE(begdate,'%Y-%m-%d'))='0000') && (MONTH(STR_TO_DATE(begdate,'%Y-%m-%d'))='00' OR MONTH(STR_TO_DATE(begdate,'%Y-%m-%d'))='0'),'', 
								 				if((DAY(STR_TO_DATE(begdate,'%Y-%m-%d'))='00' OR DAY(STR_TO_DATE(begdate,'%Y-%m-%d'))='0') && (MONTH(STR_TO_DATE(begdate,'%Y-%m-%d'))='00' OR MONTH(STR_TO_DATE(begdate,'%Y-%m-%d'))='0'),date_format(STR_TO_DATE(begdate,'%Y-%m-%d'), '%Y'), 
												if(MONTH(STR_TO_DATE(begdate,'%Y-%m-%d'))='00' OR MONTH(STR_TO_DATE(begdate,'%Y-%m-%d'))='0',date_format(STR_TO_DATE(begdate,'%Y-%m-%d'),'%Y'), 
												if(DAY(STR_TO_DATE(begdate,'%Y-%m-%d'))='00' or DAY(STR_TO_DATE(begdate,'%Y-%m-%d'))='0',date_format(STR_TO_DATE(begdate,'%Y-%m-%d'),'%m-%Y'), date_format(STR_TO_DATE(begdate,'%Y-%m-%d'),'%m-%d-%Y') ))))as begdate1  from lists where pid = '$pid' and allergy_status = 'Active' and type in(5,6) order by id";
$rsGetSurgeries = imw_query($getSurgeries);

if(imw_num_rows($rsGetSurgeries) > 0){
?>
<table style="width:740px;" class="border" cellpadding="2" cellspacing="0">	
	<tr>
		<td style="width:740px;" bgcolor="#c0c0c0" colspan="7" class="text_10b tb_heading"><b>Surgeries/Procedure</b></td>				
	</tr>	
	<tr><td height="7" colspan="7"></td></tr>
	<tr>
		<td style="width:125px;" class="text_10 bdrbtm bdrright pdl3" nowrap valign="top"><b>Ocular</b></td>
		<td style="width:150px;" class="text_10 bdrbtm bdrright" nowrap valign="top"><b>Name</b></td>
		<td style="width:125px;" class="text_10 bdrbtm bdrright" nowrap valign="top"><b>Date of Surgery</b></td>
		<td style="width:125px;" class="text_10 bdrbtm bdrright" nowrap valign="top"><b>Physician</b></td>
		<td style="width:175px;" class="text_10 bdrbtm " nowrap valign="top"><b>Comments</b></td>
	</tr>
	<?php 		
	while($row = imw_fetch_assoc($rsGetSurgeries)){
		$tmpDtSt = (!empty($row["begdate1"]) && (preg_replace('/[^0-9]/','',$row["begdate1"]) != "00000000")) ? ($row["begdate1"]) : "" ;	
		?>
		<tr>
        	<td style="width:125px;"  class="text_10 bdrbtm bdrright" valign="top"><?php if($row["type"] == "6"){ print "<b>Ocular</b>"; } ?></td>
			<td style="width:150px;"  class="text_10 bdrbtm bdrright" valign="top"><?php echo wordwrap($row["title"],30,'<br>'); ?></td>			
			<td style="width:125px;"  class="text_10 bdrbtm bdrright" valign="top"><?php echo $tmpDtSt; ?></td>
			<td style="width:125px;"  class="text_10 bdrbtm bdrright" valign="top"><?php echo ucwords($row["referredby"]); ?></td>			
			<td style="width:175px;"  class="text_10 bdrbtm" valign="top"><?php echo ucwords($row["comments"]); ?></td>
		</tr>
		<?php
	}
	?>
</table>
<?php
}

//Get Medication
$getMedication = "select * from lists where pid = '$pid' and type in(1,4)  and allergy_status = 'Active'
					and (enddate='0000-00-00' or  enddate >='".date("Y-m-d")."') order by id";
$rsGetMedication = imw_query($getMedication);

if(imw_num_rows($rsGetMedication) > 0){
?>
<table style="width:740px;" class="border" cellpadding="2" cellspacing="0">	
	<tr>
		<td style="width:740px;" bgcolor="#c0c0c0" colspan="7" class="text_10b tb_heading">Medication</td>				
	</tr>	
	<tr><td height="7" colspan="7"></td></tr>
	<tr>
		<td class="text_10b text_lable bdrbtm bdrright pdl3" style="width:100px;" nowrap valign="top">Ocular</td>
		<td class="text_10b text_lable bdrbtm bdrright" style="width:100px;" nowrap valign="top">Medication</td>
		<td class="text_10b text_lable bdrbtm bdrright" style="width:100px;" nowrap valign="top">Strength</td>
		<td class="text_10b text_lable bdrbtm bdrright" style="width:100px;" nowrap valign="top">Prescribed By</td>
		<td class="text_10b text_lable bdrbtm bdrright" style="width:100px;" nowrap valign="top">Begin Date</td>
		<td class="text_10b text_lable bdrbtm bdrright" style="width:100px;" nowrap valign="top">End Date</td>
		<td class="text_10b text_lable bdrbtm" style="width:100px;" nowrap valign="top">Comments</td>
	</tr>	
	<?php
	while($row = imw_fetch_assoc($rsGetMedication)){
	?>
		<tr>
			<td class="text_10 bdrbtm bdrright" valign="top"><?php if($row["type"] == "4"){ print "Ocular"; } ?></td>
			<td class="text_10 bdrbtm bdrright" style="width:100px;" valign="top"><?php echo wordwrap($row["title"],30,'<br>'); ?></td>			
			<td class="text_10 bdrbtm bdrright" style="width:100px;" valign="top"><?php echo $row["destination"]; ?></td>			
			<td class="text_10 bdrbtm bdrright" style="width:100px;" valign="top"><?php echo $row["referredby"]; ?></td>			
			<td class="text_10 bdrbtm bdrright" style="width:100px;" valign="top"><?php echo ($row["begdate"] == '0000-00-00') ? '' : isDate($row["begdate"]); ?></td>			
			<td class="text_10 bdrbtm bdrright" style="width:100px;" valign="top"><?php echo ($row["enddate"] == '0000-00-00') ? '' : isDate($row["enddate"]); ?></td>			
			<td class="text_10 bdrbtm" style="width:100px;" valign="top"><?php echo $row["comments"]; ?></td>
		</tr>
		<?php
	}
	?>
</table>
<?php
}

//-- Code to Add Allergies,Medication,Surgries-->

function getShowTitle( $pid, $type, $titleDef ){
	$elem_showTitle = $title=$pnotes="";
	$sql = "SELECT showTitle,title,pnotes FROM pnote_cat WHERE pid='".$pid."' and title = '".$type."' ";
	$row=sqlQuery($sql);
	if( $row != false ){
		$elem_showTitle = trim($row["showTitle"]);
		if(empty($elem_showTitle)){
			$elem_showTitle = trim( $row["title"] );		
		}
		$pnotes = $row["pnotes"];
	}else{
		//if(empty($pnotes)){
			//$pnotes = getPhyNotesConcate( $pid, $type );
		//}
	}
	
	$retTitle = !empty($elem_showTitle) ? $elem_showTitle : $titleDef;	
	
	return array("title"=>$elem_showTitle,"pnotes"=>$pnotes);
}

function HeadingTableHr(){ 
}

function HeadingTable($titleName,$flgret="0"){
	$ret = '
	<table style="width:740px;" class="border paddingTop" cellspacing="0" cellpadding="0">
		<tr>
			<td class="tb_heading" align="left" style="width:740px;"><b>'.strtoupper($titleName).'</b></td>
		</tr>
	</table>';
	if($flgret==1){return $ret;}else{print($ret);}
}

// SET MAIN OUTER DIV
	if($toMakePdfFor == "Iolink"){
		$pid = $patient_id_from_iolink;
	}else {	
		$pid = $_SESSION['patient'];
	}	
	$tmpOcuDx = getShowTitle( $pid, "Ocular Dx.", "Diagnosis" );
	$tmpOcuSx = getShowTitle( $pid, "Ocular Sx.", "OcularSx" );
	$tmpCon = getShowTitle( $pid, "Consult", "Consult" );
	$tmpMedDx = getShowTitle( $pid, "Med Dx.", "Medical Dx." );
if($tmpOcuDx["pnotes"]<>"" || $tmpOcuSx["pnotes"]<>"" || $tmpMedDx["pnotes"]<>"" || $tmpCon["pnotes"]<>""){
	?>
			<table style="width:740px;" class="paddingTop border"  cellspacing="0" cellpadding="0">
				<tr>
					<td valign="middle" class="tb_heading" style="width:740px;"><b>Provider Notes View</b></td>
				</tr>
			</table>	
		
	<?php
}

//display all of the notes for the day, as well as others that are active from previous dates, up to a certain number, $N
//retrieve all notes
//$qry = imw_query("select * from pnotes where pid='$pid' and (title='Med Dx.' and form_id=$form_id)");
if($tmpMedDx["pnotes"]<>""){
	?>
		<table cellpadding="0" cellspacing="0" class="border" style="width:740px;">
				<tr>
					<td style="width:740px;" class="bdrbtm" ><strong><?php  echo($tmpMedDx["title"]);?></strong>.</td>
				</tr>
				<?php 
				
					echo " <tr align='left'>\n";
					echo "  <td align='left' class='text_9'>\n";
					echo  addslashes($tmpMedDx["pnotes"]) ;
					echo "  </td>";
					echo " </tr>\n";
					
				?>
</table>
		
	<?php
}

//$qry=imw_query("select * from pnotes where pid='$pid' and (title='Ocular Dx.' and form_id=$form_id)");
if($tmpOcuDx["pnotes"]<>""){
	?>
	
	
			<table cellpadding="0" cellspacing="0" style="width:740px;" class="border">
				<tr>
					<td style="width:740px;" class="bdrbtm" ><strong><?php  echo($tmpOcuDx["title"]);?>.</strong></td>
				</tr><?php 
				
					echo "<tr align='left'>\n";
					echo "<td align='left' class='text_9'>\n";
					echo  addslashes($tmpOcuDx["pnotes"]);
					echo "</td>";
					echo "<td class='text_9' align='left'>";
					echo "</td>\n";
					echo "</tr>\n";
					
				?>
			</table>
		
	<?php
HeadingTableHr();
}

// ocular sx
//$qry = imw_query("select * from pnotes where pid='$pid' and (title='Ocular Sx.' and form_id=$form_id)");
if($tmpOcuSx["pnotes"]<>""){
	?>
		<table cellpadding="0" cellspacing="0" style="width:740px;" class="border">
				<tr>
					<td  class="bdrbtm" style="width:740px;" class="bdrbtm" ><strong><?php  echo($tmpOcuSx["title"]);?></strong></td>
				</tr><?php 
				
					echo " <tr align='left'>\n";
					echo "  <td align='left'  class='text_9'>\n";
					echo  	addslashes($tmpOcuSx["pnotes"]) ;
					echo "  </td>";
					echo "  <td class='text_9' align='left'>";
					echo "  </td>\n";
					echo " </tr>\n";
					
				?>
</table>	
		
	<?php  
HeadingTableHr();
}
// consult
//$qry = imw_query("select * from pnotes where pid='$pid' and (title='Consult' and form_id=$form_id)");
if($tmpCon["pnotes"]<>""){
	?>
	
	
			<table cellpadding="0" cellspacing="0" style="width:740px;"  class="border">
				<tr>
					<td  class="bdrbtm" style="width:740px;" ><strong><?php  echo($tmpCon["title"]);?></strong></td>
				</tr>
				<?php 
				
					echo "<tr align='left'>\n";
					echo "<td align='left' class='text_9'>\n";
					echo  addslashes($tmpCon["pnotes"]);
					echo "</td>";
					echo "<td class='text_9' align='left'>";
					echo "</td>\n";
					echo "</tr>\n";
					
				?>
			</table>
		
	<?php
HeadingTableHr();
}

?><!-- End Of Provider Notes-->

<!-- Code to Add Allergies,Medication,Surgries-->
<?php
// CC History//
if($crow["reason"]!="" || $crow["ccompliant"]!=""){
	//HeadingTableHr();
	HeadingTable($titleName="CC & History:");
	if($crow["ccompliant"]!="") { //PRINT CC
		SingleTdData($dataCC=nl2br($crow["ccompliant"]));
	}
	if($crow["reason"]!="") { //PRINT HISTORY
		SingleTdData($data=nl2br($crow["reason"]));//str_replace("<br />",", ",nl2br($crow["reason"])));
	}
	HeadingTableHr();
}

if(!empty($crow["neuroPsych"])){
		$data=$crow["neuroPsych"];
		DoubleTdData($lable="Neuro/Psych:",$data);
		HeadingTableHr();
 }
/*
old code 
if($crow["neuro_ao"]==1 || $crow["neuro_aff"]==1){
		$data="";
		if($crow["neuro_ao"]==1) {
			$comma="Y";
			$data.="A&O X3";
			}
		if($crow["neuro_aff"]==1) 
			{
			 if($comma=="Y")
				{
				$data.=", Affect WNL";
				}else{
				$data.="Affect WNL";
				}
			}
			DoubleTdData($lable="Neuro/Psych:",$data);
			HeadingTableHr();
 }*/
// End of CC History//
// Allergy//

/*if($allergy!=""){
	//HeadingTableHr();
	HeadingTable($titleName="Allergy/Ocular Meds:");
	SingleTdData($allergy);
	HeadingTableHr();
}
if($prow["ocularMeds"]<>"" || $ocular_medi<>""){
$data="";
	if($prow["ocularMeds"]<>"" && $prow["ocularMeds"]<>"<+OMeds&%+><+OMeds&%+><+OMeds&%+><+OMeds&%+><+OMeds&%+><+OMeds&%+><+OMeds&%+><+OMeds&%+><+OMeds&%+><+OMeds&%+><+OMeds&%+><+OMeds&%+><+OMeds&%+><+OMeds&%+><+OMeds&%+>" ){
		$data.=str_replace("<+OMeds&%+>",",&nbsp;",str_replace("<+OMeds&%+><+OMeds&%+>","",$prow["ocularMeds"]))."&nbsp;".$prow["ocular_med_txt"];
		DoubleTdData($lable="Ocular Meds:",$data);
		HeadingTableHr();	
		} 
	if($ocular_medi<>"")
	{ 
		$data.=$ocular_medi;
		DoubleTdData($lable="Ocular Meds:",$data);
		HeadingTableHr();
	}


}*/
// End of Allergy//
// ocularHistory//
	$ocularHistory = ""; 
	$ocularHistory .=($prow["ocularhx_lens"]==1) ? "CL, " : ""; 
	$ocularHistory .=($prow["ocularhx_glaucoma"]==1) ? "Glaucoma, " : ""; 
	$ocularHistory .=($prow["ocularhx_sx"]==1) ? "Sx/Laser, " : "";
	$ocularHistory .=($prow["ocularhx_glasses"]==1) ? "Glasses, " : "";
	$ocularHistory .=($prow["ocularhx_fhx_ret"]==1) ? "R.Detach, " : "";
	$ocularHistory .=($prow["ocularhx_fhx_mac"]==1) ? "Mac. deg., " : "";
	$ocularHistory .=($prow["ocularhx_fhx_cat"]==1) ? "Cataracts, " : "";
	$ocularHistory .=($prow["ocularhx_fhx_bli"]==1) ? "Blindness, " : "";
	$ocularHistory .=($prow["ocularhx_other"]<>"") ? $prow["ocularhx_other"]."," : "" ;					  	
	$ocularHistory=trim(strip_tags($ocularHistory));
	$rvs = "";
	$rvs .= ($prow["rvs_blurred"]==1) ? "Blurred, " : "";
	$rvs .= ($prow["rvs_poor_night"]==1) ? "Poor night vision, " : "";
	$rvs .= ($prow["rvs_poor_dept"]==1) ? "Poor depth, " : "";
	$rvs .= ($prow["rvs_pglare"]==1) ? "Glare/Halos, " : "";
	$rvs .= ($prow["rvs_tear"]==1) ? "Tearing/Dry eyes, " : "";
	$rvs .= ($prow["rvs_diplopia"]==1) ? "Diplopia, " : "";
	$rvs .= ($prow["rvs_spot"]==1) ? "Spots/Floaters, " : "";
	$rvs .= ($prow["rvs_itiching"]==1) ? "Itching/Burning, " : "";
	$rvs .= ($prow["rvs_red"]==1) ? "Red eyes, " : "";
	$rvs .= ($prow["rvs_other"]<>"") ? $prow["rvs_other"]."," : ""; 
	$rvs=trim(strip_tags($rvs));
	$medicalHistory = "";
	$medicalHistory=($prow["medicalhx_id"]==1) ? "DM, " : "";
	$medicalHistory.=($prow["medicalhx_id"]==2) ? "IDDM, " : "";
	$medicalHistory.=($prow["medicalhx_id"]==3) ? "NIDDM, " : "";
	$medicalHistory.=($prow["medicalhx_htn"]==1) ? "HTN, " : ""; 
	$medicalHistory.=($prow["medicalhx_hear"]==1) ? "Heart, " : "";  
	$medicalHistory.=($prow["medicalhx_lungs"]==1) ? "Lungs, " : "";  
	$medicalHistory.=($prow["medicalhx_neuro"]==1) ? "Neuro, " : "";
	$medicalHistory.=($prow["medicalhx_other"]<>"") ? $prow["medicalhx_other"]."," : ""; 
	$medicalHistory=trim( strip_tags($medicalHistory));
	if($ocularHistory<>""){
		//HeadingTableHr();
		HeadingTable($titleName="Ocular Hx:");
		SingleTdData(substr($ocularHistory,0,strlen($ocularHistory)-1));
		HeadingTableHr();
		if($rvs<>""){
			DoubleTdData($lable="Review Visual:",substr($rvs,0,strlen($rvs)-1));
		}
		if($medicalHistory<>""){
			DoubleTdData($lable="Medical Hx:",substr($medicalHistory,0,strlen($medicalHistory)-1));
		}
		HeadingTableHr();
	}

// End of ocularHistory//

// VISION START//
if(($sel_vis_dis_od_sel_1!="" && $txt_vis_dis_od_txt_1!="20/" && $txt_vis_dis_od_txt_1!="") && ($sel_vis_dis_os_sel_1!="" && $txt_vis_dis_os_txt_1!="20/" && $txt_vis_dis_os_txt_1!="")){ 
	$show_vis_dist_val=true;
	$visdateCheckpass=true;
	}	
if(($chk_vis_near == 1)&&($txt_vis_near_od_txt_1!="" && $txt_vis_near_od_txt_1!="20/")){ 
	$show_vis_near_val=true;
	$visNeardateCheckpass=true;
	}
	if($date_of_service!="" && $txt_vis_near_examdate!="" && $txt_vis_near_examdate!=0){												
		$start2 =FormatDate_insert($date_of_service);
		$end2 =$txt_vis_near_examdate;
		if($start2==$end2){
			$visNeardateCheckpass=true;
			$visdateCheckpass=true;
		}
		if($diff=$objCpr->get_time_difference($start2, $end2)){
			$visNeardateCheckpass=true;
			$visdateCheckpass=true;
			
		}
	}
	/* End Code To Compare VISExamDate WITH DOS*/ 	
	$show_vis_BAT_val=false;
	/* Code To Compare VISBATExamDate WITH DOS*/
	$visCRdateCheckpass=false;
	if($date_of_service!="" && $txt_vis_bat_examdate!="" && $txt_vis_bat_examdate!=0){
		$start3 =FormatDate_insert($date_of_service);
		$end3 =$txt_vis_bat_examdate;
		if($start3==$end3){
			$visCRdateCheckpass=true;
		}
		if($diff=$objCpr->get_time_difference($start3, $end3)){
			$visCRdateCheckpass=true;
			// echo "$start3 You  $end3 have $txt_vis_near_examdate  logged in after <br>".$diff['days']." Days ".$diff['hours']."Hours ".$diff['minutes']."Minutes <br>" ;
		}
	}
	//echo($txt_vis_dis_examdate."d".$txt_vis_near_examdate);
	//
	
	
if($visNeardateCheckpass==true || $visdateCheckpass==true){
//include_once($GLOBALS['fileroot'].'/interface/patient_info/complete_pt_rec/print_functions_class.php');
	
HeadingTableHr();
HeadingTable($titleName="Vision:");
HeadingTableHr();
}
// End of VISION//


?>
<table class="border" cellpadding="0" cellspacing="0" style="width:740px;font-size:10px;">
	 
<?php  
if($date_of_service!="" || $txt_vis_near_examdate!="" || $txt_vis_near_examdate!=0){													
		$start2 = $objCpr->FormatDate_insert($date_of_service);
		$end2 = $txt_vis_near_examdate;
		if($start2 == $end2){
			$visNeardateCheckpass=true;
			$visdateCheckpass=true;
		}
		if($diff = $objCpr->get_time_difference($start2, $end2)){
			$visNeardateCheckpass=true;
			$visdateCheckpass=true;
			
		}
	}
	
	/* End Code To Compare VISExamDate WITH DOS*/ 	
	$show_vis_BAT_val=false;
	/* Code To Compare VISBATExamDate WITH DOS*/
	$visCRdateCheckpass=false;
	if($date_of_service!="" || $txt_vis_bat_examdate!="" || $txt_vis_bat_examdate!=0){
		$start3 =$objCpr->FormatDate_insert($date_of_service);
		$end3 =$txt_vis_bat_examdate;
		if($start3==$end3){ 
			$visCRdateCheckpass=true;
		}
		if($diff=$objCpr->get_time_difference($start3, $end3)){
			$visCRdateCheckpass=true;			
		}
	}
	
	/* End Code To Compare VISBATExamDate WITH DOS*/ 	
	$vis_arCheckpass=false;
	
	if($date_of_service!="" || $txt_vis_ar_ak_examdate!="" || $txt_vis_ar_ak_examdate!=0){									
	$startar =$objCpr->FormatDate_insert($date_of_service);  
		$endar = $txt_vis_ar_ak_examdate;
	if($startar==$endar){
			$vis_arCheckpass=true;
		}
		
					//$startar, $endar
		if($diff=$objCpr->get_time_difference($startar, $endar)){ 	

		$vis_arCheckpass=true;
		} 

	}   
	
	?>
		<tr>
			<?php  
			
			if($visdateCheckpass==true){
				?>
				<td class="text_lable bdrbtm" style="width:200px;" >Distance</td>
				<?php 
			}else{
				echo("<td class='text_lable bdrbtm'>&nbsp;</td>");
			}
			
			if(($chk_vis_near == 1 && $visNeardateCheckpass==true) &&($txt_vis_near_od_txt_1!="" && $txt_vis_near_od_txt_1!="20/")) {
				?> 
				<td style="width:200px;" class="text_lable bdrbtm" ><?php echo"Near";?></td>
				<?php 
			} 
			else{
				echo("<td class='text_lable bdrbtm'>&nbsp;</td>");
			}
			
            #Start AR AK Heading			
			if( $vis_arCheckpass==true && ($txt_vis_ar_od_s!="" || $txt_vis_ar_od_c!="" || $txt_vis_ar_od_a!="" || $txt_vis_ar_os_s!="" || $txt_vis_ar_os_c!="" || 			$txt_vis_ar_os_a!="")){
				if($txt_vis_ar_od_s!="" || $txt_vis_ar_od_c!="" || $txt_vis_ar_od_a!="" || $txt_vis_ar_os_s!="" || $txt_vis_ar_os_c!="" || $txt_vis_ar_os_a!=""){?>
					<td style="width:200px;" class="text_lable bdrbtm bdrright" >AR</td>
				<?php }
				else{
					echo("<td class='text_lable bdrbtm'>&nbsp;</td>");
				}
				
				if($txt_vis_ak_od_k!="" ||$txt_vis_ak_od_slash!="" || $txt_vis_ak_od_x!="" ||$txt_vis_ak_os_k!="" ||$txt_vis_ak_os_slash!="" || $txt_vis_ak_os_x!=""){?>
					<td style="width:140px;" class="text_lable bdrbtm bdrright" >AK</td>
				<?php }
				else{
					echo("<td class='text_lable bdrbtm'>&nbsp;</td>");
				}
			}
			#End AR AK Heading
			
            if($chk_vis_bat == 1 &&($txt_vis_bat_nl_od!="" && $txt_vis_bat_nl_od!="20/")){
            ?>
                <td width="400" class="text_lable bdrbtm">B.A.T</td>
            <?php }
			else{
				echo("<td class='text_lable bdrbtm'>&nbsp;</td>");
			}
			?>
	</tr>
<tr>
<?php   
 
if($visdateCheckpass==true){
	$odinfo_vis_dis[]="";
	$odinfo_vis_dis[]=($sel_vis_dis_od_sel_1!="" && $txt_vis_dis_od_txt_1!="20/")?$sel_vis_dis_od_sel_1:"";
	//$osinfo_vis_dis.=($sel_vis_dis_od_sel_1!="" && $txt_vis_dis_od_txt_1!="20/")?$sel_vis_dis_od_sel_1."RM":"jj"; 								
	$odinfo_vis_dis[]=($sel_vis_dis_od_sel_1!="" && $txt_vis_dis_od_txt_1!="20/")?"<b>".$txt_vis_dis_od_txt_1."</b>":""; 
	
	if(($sel_vis_dis_os_sel_2!="" && $txt_vis_dis_os_txt_2!="20/" && $txt_vis_dis_os_txt_2!="") and ($sel_vis_dis_od_sel_2!="" && $txt_vis_dis_od_txt_2!="20/" && $txt_vis_dis_od_txt_2!="")){
		$odinfo_vis_dis[]=($sel_vis_dis_od_sel_2!="" && $txt_vis_dis_od_txt_2!="20/")?$sel_vis_dis_od_sel_2:""; 								
		$odinfo_vis_dis[]=($sel_vis_dis_od_sel_2!="" && $txt_vis_dis_od_txt_2!="20/")?"<b>".$txt_vis_dis_od_txt_2."</b>":""; 
	}
}
elseif(($sel_vis_dis_os_sel_2!="" && $txt_vis_dis_os_txt_2!="20/" && $txt_vis_dis_os_txt_2!="") and ($sel_vis_dis_od_sel_2!="" && $txt_vis_dis_od_txt_2!="20/" && $txt_vis_dis_od_txt_2!="")){
	$odinfo_vis_dis[]="";
	$odinfo_vis_dis[]=($sel_vis_dis_od_sel_2!="" && $txt_vis_dis_od_txt_2!="20/")?$sel_vis_dis_od_sel_2:""; 								
	$odinfo_vis_dis[]=($sel_vis_dis_od_sel_2!="" && $txt_vis_dis_od_txt_2!="20/")?"<b>".$txt_vis_dis_od_txt_2."</b>":""; 
}

?>
<td class="text_lable bdrbtm"><?php odLable(); print(@implode("&nbsp;",$odinfo_vis_dis));?></td>	

<?php   

$haveValue = 0;
if(($chk_vis_near == 1 && $visNeardateCheckpass==true) &&(($sel_vis_near_od_sel_1!="" && $txt_vis_near_od_txt_1!="" && $txt_vis_near_od_txt_1!="20/") and ($sel_vis_near_os_sel_1!="" && $txt_vis_near_os_txt_1!="" && $txt_vis_near_os_txt_1!="20/"))){ 
 $haveValue = 1;
 $odinfo_vis_near[]="";
 $odinfo_vis_near[]=($sel_vis_near_od_sel_1!="" && $txt_vis_near_od_txt_1!="20/")?$sel_vis_near_od_sel_1:"";							
 $odinfo_vis_near[]=($sel_vis_near_od_sel_1!="" && $txt_vis_near_od_txt_1!="" && $txt_vis_near_od_txt_1!="20/")?"<b>".$txt_vis_near_od_txt_1."</b>":""; 
 if(($sel_vis_near_od_sel_2!="" && $txt_vis_near_od_txt_2!="20/" && $txt_vis_near_od_txt_2!="") and ($sel_vis_near_os_sel_2!="" && $txt_vis_near_os_txt_2!="20/" && $txt_vis_near_os_txt_2!="")){
	$odinfo_vis_near[]=($sel_vis_near_od_sel_2!="" && $txt_vis_near_od_txt_2!="20/")?$sel_vis_near_od_sel_2:""; 
	$odinfo_vis_near[]=($txt_vis_near_od_txt_2!="" && $txt_vis_near_od_txt_2!="20/")?"<b>".$txt_vis_near_od_txt_2."</b>":""; 
	}
}
elseif(($sel_vis_near_od_sel_2!="" && $txt_vis_near_od_txt_2!="20/" && $txt_vis_near_od_txt_2!="") and ($sel_vis_near_os_sel_2!="" && $txt_vis_near_os_txt_2!="20/" && $txt_vis_near_os_txt_2!="")){
	$odinfo_vis_near[]=($sel_vis_near_od_sel_2!="" && $txt_vis_near_od_txt_2!="20/")?$sel_vis_near_od_sel_2:""; 
	$odinfo_vis_near[]=($txt_vis_near_od_txt_2!="" && $txt_vis_near_od_txt_2!="20/")?"<b>".$txt_vis_near_od_txt_2."</b>":""; 
}
 ?>
<td class="text_lable bdrbtm" width="0"><?php if($haveValue == 1){odLable(); print implode("&nbsp;",$odinfo_vis_near);}?></td>	
<?php 
#start AR AK (OD) 

if( $vis_arCheckpass==true && ($txt_vis_ar_od_s!="" || $txt_vis_ar_od_c!="" || $txt_vis_ar_od_a!="" || $txt_vis_ar_os_s!="" || $txt_vis_ar_os_c!="" || $txt_vis_ar_os_a!="")){

$odinfo_vis_ar[]="";
	if($txt_vis_ar_od_s!=""){$odinfo_vis_ar[]="S"; }
	$odinfo_vis_ar[]="<b>".$txt_vis_ar_od_s."</b>";
	if($txt_vis_ar_od_c!=""){$odinfo_vis_ar[]="C"; }
		$odinfo_vis_ar[]="<b>".$txt_vis_ar_od_c."</b>";							
		if($txt_vis_ar_od_a!=""){$odinfo_vis_ar[]="A"; }
		if($txt_vis_ar_od_a!=""){$odinfo_vis_ar[]="<b>".$txt_vis_ar_od_a."&#176;</b>";}						
		if($txt_vis_ar_od_a!=""){$odinfo_vis_ar[]="<b>".$sel_vis_ar_od_sel_1."</b>";}
		?>
<td class="text_lable bdrbtm"><?php odLable(); print implode("&nbsp;",$odinfo_vis_ar);?></td>	
<?php
} 
?>

<?php if($txt_vis_ak_od_k!="" ||$txt_vis_ak_od_slash!="" || $txt_vis_ak_od_x!=""){
$odinfo_vis_ak[]="";
if($txt_vis_ak_od_k!=""){$odinfo_vis_ak[]="K:";}
$odinfo_vis_ak[]="<b>".$txt_vis_ak_od_k."</b>";
//if($txt_vis_ak_od_slash!=""){$odinfo_vis_ak[]="/"; }
$odinfo_vis_ak[]="/";
$odinfo_vis_ak[]="<b>".$txt_vis_ak_od_slash."</b>";
//if($txt_vis_ak_od_x!=""){$odinfo_vis_ak[]="X"; }
$odinfo_vis_ak[]="X";
if($txt_vis_ak_od_x!=""){$odinfo_vis_ak[]="<b>".$txt_vis_ak_od_x."&#176; </b>";}
?>
<td class="text_lable bdrbtm"><?php odLable(); print implode("&nbsp;",$odinfo_vis_ak);?></td>	
<?php } 
#End AR AK (OD)

?>

<?php 	
if($txt_vis_bat_nl_od!="" && $txt_vis_bat_nl_od!="20/"){
  $odinfo_vis_bat[]="";
 if($txt_vis_bat_nl_od!="" && $txt_vis_bat_nl_od!="20/" ){$odinfo_vis_bat[]="NL"; }
 if($txt_vis_bat_nl_od!="" && $txt_vis_bat_nl_od!="20/"){ $odinfo_vis_bat[]="<b>".$txt_vis_bat_nl_od."</b>"; } 
 if($txt_vis_bat_low_od!="" && $txt_vis_bat_low_od!="20/" ){$odinfo_vis_bat[]="L"; }
 if($txt_vis_bat_low_od!="" && $txt_vis_bat_low_od!="20/"){$odinfo_vis_bat[]="<b>".$txt_vis_bat_low_od."</b>"; } 
 if($txt_vis_bat_med_od!="" && $txt_vis_bat_med_od!="20/"){$odinfo_vis_bat[]= "M"; }
 if($txt_vis_bat_med_od!="" && $txt_vis_bat_med_od!="20/"){$odinfo_vis_bat[]="<b>".$txt_vis_bat_med_od."</b>"; }
 if($txt_vis_bat_high_od!="" && $txt_vis_bat_high_od!="20/"){$odinfo_vis_bat[]="H"; }
 if($txt_vis_bat_high_od!="" && $txt_vis_bat_high_od!="20/" ){ $odinfo_vis_bat[]="<b>".$txt_vis_bat_high_od."</b>";}
?>
<td class="text_lable bdrbtm"><?php odLable(); print implode("&nbsp;",$odinfo_vis_bat);?></td>
<?php
 } ?>
</tr>
<tr>
<?php
if(($sel_vis_dis_od_sel_1!="" && $txt_vis_dis_od_txt_1!="20/" && $txt_vis_dis_od_txt_1!="") && ($sel_vis_dis_os_sel_1!="" && $txt_vis_dis_os_txt_1!="20/" && $txt_vis_dis_os_txt_1!="")){ 
	$osinfo_vis_dis[]="";
	$osinfo_vis_dis[]=($sel_vis_dis_os_sel_1!="" && $txt_vis_dis_os_txt_1!="20/" && $txt_vis_dis_os_txt_1!="")?$sel_vis_dis_os_sel_1:"";
	$osinfo_vis_dis[]=($sel_vis_dis_os_sel_1!="" && $txt_vis_dis_os_txt_1!="20/" && $txt_vis_dis_os_txt_1!="")?"<b>".$txt_vis_dis_os_txt_1."</b>":""; 						
	if(($sel_vis_dis_os_sel_2!="" && $txt_vis_dis_os_txt_2!="20/" && $txt_vis_dis_os_txt_2!="") and ($sel_vis_dis_od_sel_2!="" && $txt_vis_dis_od_txt_2!="20/" && $txt_vis_dis_od_txt_2!="")){
		$osinfo_vis_dis[]=($sel_vis_dis_os_sel_2!="" && $txt_vis_dis_os_txt_2!="20/")?$sel_vis_dis_os_sel_2:""; 
		$osinfo_vis_dis[]=($sel_vis_dis_os_sel_2!="" && $txt_vis_dis_os_txt_2!="20/")?"<b>".$txt_vis_dis_os_txt_2."</b>" :""; 
	}
}
elseif(($sel_vis_dis_os_sel_2!="" && $txt_vis_dis_os_txt_2!="20/" && $txt_vis_dis_os_txt_2!="") and ($sel_vis_dis_od_sel_2!="" && $txt_vis_dis_od_txt_2!="20/" && $txt_vis_dis_od_txt_2!="")){
	$osinfo_vis_dis[]=($sel_vis_dis_os_sel_2!="" && $txt_vis_dis_os_txt_2!="20/")?$sel_vis_dis_os_sel_2:""; 
	$osinfo_vis_dis[]=($sel_vis_dis_os_sel_2!="" && $txt_vis_dis_os_txt_2!="20/")?"<b>".$txt_vis_dis_os_txt_2."</b>" :""; 
}
?>
<td class="text_lable bdrbtm"><?php osLable(); print implode("&nbsp;",$osinfo_vis_dis);?></td>


<?php		
$haveValue = 0;
if(($chk_vis_near == 1 && $visNeardateCheckpass==true) &&(($sel_vis_near_od_sel_1!="" && $txt_vis_near_od_txt_1!="" && $txt_vis_near_od_txt_1!="20/") and ($sel_vis_near_os_sel_1!="" && $txt_vis_near_os_txt_1!="" && $txt_vis_near_os_txt_1!="20/"))){ 
	$osinfo_vis_near[]="";	
	$haveValue = 1;
	$osinfo_vis_near[]=($sel_vis_near_os_sel_1!="" && $txt_vis_near_os_txt_1!="20/")?$sel_vis_near_os_sel_1:""; 	
	$osinfo_vis_near[]=($sel_vis_near_os_sel_1!="" && $txt_vis_near_os_txt_1!="" && $txt_vis_near_os_txt_1!="20/")?"<b>".$txt_vis_near_os_txt_1."</b>":"";		
	
	if(($sel_vis_near_od_sel_2!="" and $txt_vis_near_od_txt_2!="20/") and ($sel_vis_near_os_sel_2!="" && $txt_vis_near_os_txt_2!="20/")){	
		$osinfo_vis_near[]=($sel_vis_near_os_sel_2!="" && $txt_vis_near_os_txt_2!="20/")?$sel_vis_near_os_sel_2:""; 	
		$osinfo_vis_near[]=($sel_vis_near_os_sel_2!="" && $txt_vis_near_os_txt_2!="" && $txt_vis_near_os_txt_2!="20/")?"<b>".$txt_vis_near_os_txt_2."</b>":""; 	
	}
 } 
elseif(($sel_vis_near_od_sel_2!="" and $txt_vis_near_od_txt_2!="20/") and ($sel_vis_near_os_sel_2!="" && $txt_vis_near_os_txt_2!="20/")){	
		$osinfo_vis_near[]=($sel_vis_near_os_sel_2!="" && $txt_vis_near_os_txt_2!="20/")?$sel_vis_near_os_sel_2:""; 	
		$osinfo_vis_near[]=($sel_vis_near_os_sel_2!="" && $txt_vis_near_os_txt_2!="" && $txt_vis_near_os_txt_2!="20/")?"<b>".$txt_vis_near_os_txt_2."</b>":""; 	
}
?>
	<td class="text_lable bdrbtm"><?php if($haveValue == 1){ "<span class='text_green'>". osLable()."</span>"; print implode("&nbsp;",$osinfo_vis_near);}?></td>		
<?php
#start AR AK (OS) 
if($txt_vis_ar_os_s!="" || $txt_vis_ar_os_c!="" || $txt_vis_ar_os_a!=""){
		$osinfo_vis_ar[]="";
		if($txt_vis_ar_os_s!=""){
			$osinfo_vis_ar[]="S";
			$osinfo_vis_ar[]="<b>".$txt_vis_ar_os_s."</b>";
		}
		if($txt_vis_ar_os_c!=""){
			$osinfo_vis_ar[]="C";
			$osinfo_vis_ar[]="<b>".$txt_vis_ar_os_c."</b>";							
		}
		if($txt_vis_ar_os_a!=""){
			$osinfo_vis_ar[]="A";
			$osinfo_vis_ar[]="<b>".$txt_vis_ar_os_a."&#176; </b>";
			$osinfo_vis_ar[]="<b>".$sel_vis_ar_os_sel_1."</b>";
		}
?>
<td class="text_lable bdrbtm" ><?php osLable(); print implode("&nbsp;",$osinfo_vis_ar);?></td>	

<?php } ?>
<?php  if($txt_vis_ak_os_k!="" ||$txt_vis_ak_os_slash!="" || $txt_vis_ak_os_x!=""){ 
$osinfo_vis_ak[]="";
if($txt_vis_ak_os_k!=""){$osinfo_vis_ak[]="K:"; }
$osinfo_vis_ak[]="<b>".$txt_vis_ak_os_k."</b>";
//if($txt_vis_ak_os_slash!=""){$osinfo_vis_ak[]="/";}
$osinfo_vis_ak[]="/";
$osinfo_vis_ak[]="<b>".$txt_vis_ak_os_slash."</b>";
//if($txt_vis_ak_os_x!=""){$osinfo_vis_ak[]="X"; }
$osinfo_vis_ak[]="X";
if($txt_vis_ak_os_x!=""){$osinfo_vis_ak[]="<b>".$txt_vis_ak_os_x."&#176;</b>";}
?>
<td  class="text_lable bdrbtm" ><?php osLable(); print implode("&nbsp;",$osinfo_vis_ak);?></td>	
<?php }
# End AR AK (OS) 
?>
    
<?php 
if($chk_vis_bat == 1 &&($txt_vis_bat_nl_od!="" && $txt_vis_bat_nl_od!="20/")){
$osinfo_vis_bat[]="";
if($txt_vis_bat_nl_os!="" && $txt_vis_bat_nl_os!="20/"){$osinfo_vis_bat[]="NL"; }
if($txt_vis_bat_nl_os!="" && $txt_vis_bat_nl_os!="20/" ){ $osinfo_vis_bat[]="<b>".$txt_vis_bat_nl_os."</b>";}						
if($txt_vis_bat_low_os!="" && $txt_vis_bat_low_os!="20/"){$osinfo_vis_bat[]="L"; }
if($txt_vis_bat_low_os!="" && $txt_vis_bat_low_os!="20/" ){$osinfo_vis_bat[]="<b>".$txt_vis_bat_low_os."</b>";}						
if($txt_vis_bat_med_os!="" && $txt_vis_bat_med_os!="20/"){$osinfo_vis_bat[]="M";}
if($txt_vis_bat_med_os!="" && $txt_vis_bat_med_os!="20/" ){ $osinfo_vis_bat[]="<b>".$txt_vis_bat_med_os."</b>";}
if($txt_vis_bat_high_os!="" && $txt_vis_bat_high_os!="20/"){$osinfo_vis_bat[]="H"; }
if($txt_vis_bat_high_os!="" && $txt_vis_bat_high_os!="20/" ){ $osinfo_vis_bat[]="<b>".$txt_vis_bat_high_os."</b>";}
?>
<td class="text_lable bdrbtm" ><?php osLable(); print implode("&nbsp;",$osinfo_vis_bat);?></td>
<?php } ?>
</tr>
</table>

<?php 
 
if($txt_vis_dis_desc!="" ){
	?>
<table class="border" style="width:740px;" cellpadding="0" cellspacing="0" >
	<tr>
		<td class="text_lable" style="width:740px;" ><strong>Distance Desc.</strong>&nbsp;&nbsp;<?php echo $txt_vis_dis_desc;//txt_vis_dis_near_desc; ?></td>
	</tr>
</table>
	<?php 
} 
if($chk_vis_near == 1 && $txt_vis_near_desc!="" ){
	?>
<table class="border" cellpadding="0" cellspacing="0" style="width:740px;">
	<tr>
		<td class="text_lable" style="width:740px;" ><strong>Near Desc.</strong>&nbsp;&nbsp;<?php echo $txt_vis_near_desc;//txt_vis_dis_near_desc; ?></td>
	</tr>
</table>
	<?php 
}
if($chk_vis_bat == 1 && $txt_vis_bat_desc!="" ){
	?>
<table style='table-layout:fixed'  class="border" cellpadding="0" cellspacing="0" style="width:740px;" >
	<tr>
		<td class="text_lable">B.A.T Desc.&nbsp;&nbsp;<?php echo $txt_vis_bat_desc;//txt_vis_dis_near_desc; ?></td>
	</tr>
</table>
	<?php 
} 
?>
<!-- Vision Table Row 2-->
<?php 

$vis_arCheckpass=false;
if($date_of_service!="" || $txt_vis_ar_ak_examdate!="" || $txt_vis_ar_ak_examdate!=0){									
	$startar = FormatDate_insert($date_of_service);
	$endar = $txt_vis_ar_ak_examdate;
if($startar==$endar){
	$vis_arCheckpass=true;
}


if($diff=$objCpr->get_time_difference($startar, $endar)){

	$vis_arCheckpass=true;
}
}

?>	
<?php  
#start AR AK
/*
if( $vis_arCheckpass==true && ($txt_vis_ar_od_s!="" || $txt_vis_ar_od_c!="" || $txt_vis_ar_od_a!="" || $txt_vis_ar_os_s!="" || $txt_vis_ar_os_c!="" || $txt_vis_ar_os_a!="")){
?>
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
	<?php
	if($txt_vis_ar_od_s!="" || $txt_vis_ar_od_c!="" || $txt_vis_ar_od_a!="" || $txt_vis_ar_os_s!="" || $txt_vis_ar_os_c!="" || $txt_vis_ar_os_a!=""){?><td>AR</td><?php }?>
	<?php 
	if($txt_vis_ak_od_k!="" ||$txt_vis_ak_od_slash!="" || $txt_vis_ak_od_x!="" ||$txt_vis_ak_os_k!="" ||$txt_vis_ak_os_slash!="" || $txt_vis_ak_os_x!=""){?><td>AK</td><?php }?>	
	</tr>
<tr>
<?php 
if( $vis_arCheckpass==true && ($txt_vis_ar_od_s!="" || $txt_vis_ar_od_c!="" || $txt_vis_ar_od_a!="" || $txt_vis_ar_os_s!="" || $txt_vis_ar_os_c!="" || $txt_vis_ar_os_a!="")){

$odinfo_vis_ar[]="";
	if($txt_vis_ar_od_s!=""){$odinfo_vis_ar[]="S"; }
	$odinfo_vis_ar[]="<b>".$txt_vis_ar_od_s."</b>";
	if($txt_vis_ar_od_c!=""){$odinfo_vis_ar[]="C"; }
		$odinfo_vis_ar[]="<b>".$txt_vis_ar_od_c."</b>";							
		if($txt_vis_ar_od_a!=""){$odinfo_vis_ar[]="A"; }
		if($txt_vis_ar_od_a!=""){$odinfo_vis_ar[]="<b>".$txt_vis_ar_od_a."&#176;</b>";}						
		if($txt_vis_ar_od_a!=""){$odinfo_vis_ar[]="<b>".$sel_vis_ar_od_sel_1."</b>";}
		?>
<td><?php odLable(); print implode("&nbsp;",$odinfo_vis_ar);?></td>	
<?php
} 
?>

<?php if($txt_vis_ak_od_k!="" ||$txt_vis_ak_od_slash!="" || $txt_vis_ak_od_x!=""){
$odinfo_vis_ak[]="";
if($txt_vis_ak_od_k!=""){$odinfo_vis_ak[]="K:";}
$odinfo_vis_ak[]="<b>".$txt_vis_ak_od_k."</b>";
//if($txt_vis_ak_od_slash!=""){$odinfo_vis_ak[]="/"; }
$odinfo_vis_ak[]="/";
$odinfo_vis_ak[]="<b>".$txt_vis_ak_od_slash."</b>";
//if($txt_vis_ak_od_x!=""){$odinfo_vis_ak[]="X"; }
$odinfo_vis_ak[]="X";
if($txt_vis_ak_od_x!=""){$odinfo_vis_ak[]="<b>".$txt_vis_ak_od_x."&#176; </b>";}
?>
<td><?php odLable(); print implode("&nbsp;",$odinfo_vis_ak);?></td>	
<?php } ?>
</tr>
<tr>
<?php if($txt_vis_ar_os_s!="" || $txt_vis_ar_os_c!="" || $txt_vis_ar_os_a!=""){
		$osinfo_vis_ar[]="";
		if($txt_vis_ar_os_s!=""){
			$osinfo_vis_ar[]="S";
			$osinfo_vis_ar[]="<b>".$txt_vis_ar_os_s."</b>";
		}
		if($txt_vis_ar_os_c!=""){
			$osinfo_vis_ar[]="C";
			$osinfo_vis_ar[]="<b>".$txt_vis_ar_os_c."</b>";							
		}
		if($txt_vis_ar_os_a!=""){
			$osinfo_vis_ar[]="A";
			$osinfo_vis_ar[]="<b>".$txt_vis_ar_os_a."&#176; </b>";
			$osinfo_vis_ar[]="<b>".$sel_vis_ar_os_sel_1."</b>";
		}
?>
<td><?php osLable(); print implode("&nbsp;",$osinfo_vis_ar);?></td>	

<?php } ?>
<?php if($txt_vis_ak_os_k!="" ||$txt_vis_ak_os_slash!="" || $txt_vis_ak_os_x!=""){ 
$osinfo_vis_ak[]="";
if($txt_vis_ak_os_k!=""){$osinfo_vis_ak[]="K:"; }
$osinfo_vis_ak[]="<b>".$txt_vis_ak_os_k."</b>";
//if($txt_vis_ak_os_slash!=""){$osinfo_vis_ak[]="/";}
$osinfo_vis_ak[]="/";
$osinfo_vis_ak[]="<b>".$txt_vis_ak_os_slash."</b>";
//if($txt_vis_ak_os_x!=""){$osinfo_vis_ak[]="X"; }
$osinfo_vis_ak[]="X";
if($txt_vis_ak_os_x!=""){$osinfo_vis_ak[]="<b>".$txt_vis_ak_os_x."&#176;</b>";}
?>
<td><?php osLable(); print implode("&nbsp;",$osinfo_vis_ak);?></td>	
<?php }?>
</tr>
</table>
	<?php 
}
*/
#End AR AK 
if($txt_vis_ar_ak_desc!="" && $vis_arCheckpass==true ){
?>
<table class="border" cellpadding="0" cellspacing="0" style="width:740px;" >
<tr>
<td class="text_lable " style="width:740px;" ><?php if($txt_vis_ar_ak_desc!=""){?><strong>AR Desc.&nbsp;</strong><?php echo $txt_vis_ar_ak_desc;}?></td>
	
</tr>	
</table>	
<?php 
}if($txt_vis_AK_desc!="" && $vis_arCheckpass==true ){
?>
<table class="border" cellpadding="0" cellspacing="0" style="width:740px;" >
<tr>
<td class="text_lable " style="width:740px;"><?php if($txt_vis_AK_desc!=""){?><strong>AK Desc.&nbsp;</strong><?php echo $txt_vis_AK_desc;}?></td>
	
</tr>	
</table>	
<?php 
}
// Start PC Code//
?><!-- Initial -->


<?php  
if($sel_vis_pc_od_sel_1!="" &&($txt_vis_pc_od_s!=""||$txt_vis_pc_od_c!=""||$txt_vis_pc_od_a!="" || $txt_vis_pc_od_add!="")){
	?>
<!-- 1st PC -->
<table class="border" cellpadding="0" cellspacing="0" style="width:760px;" >
  <tr>
    <td style="width:760px;" class="tb_subheading" ><b>PC 1st</b></td>
  </tr>
</table>
<table class="border" cellpadding="0" cellspacing="0" style="width:740px;" >
  <tr>
    <td class="bdrbtm" style="width:200px;">1st</td>
	<td class="bdrbtm" style="width:200px;" >&nbsp;</td>	
	<?php 
	$prismTxt = '';	
	if($elem_prismPc1){
		$prismTxt = 'Prism';	
	}	
		
	$overRefractionTxt = "";
	if($chk_pc_near == "Over Refraction"){
		$overRefractionTxt = "Over Refraction";
	}
	
	?>
	<td class="bdrbtm" style="width:200px;"><?php echo $prismTxt; ?></td>
	<td class="bdrbtm" style="width:140px;" ><?php echo $overRefractionTxt; ?></td>
  </tr>
  <!-- OD -->
  <tr>
    <?php   	
			$pc1gp1_od[]="";
			$pc1gp1_os[]="";
			$pc1gp2_addod[]="";
			$pc1gp2_addos[]="";
			$pc1gp3_prismod[]="";
			$pc1gp3_prismos[]="";
			/*$pc1gp1_od[]="OD";*/
		
			if(($sel_vis_pc_od_sel_1!="" && $txt_vis_pc_od_s!="" || ($sel_vis_pc_os_sel_1!="" && $txt_vis_pc_os_s!="")) ||
			($sel_vis_pc_od_sel_1_2!="" && $txt_vis_pc_od_s_2!="" || ($sel_vis_pc_os_sel_1_2!="" && $txt_vis_pc_os_s_2!="")) ||
			($sel_vis_pc_od_sel_1_3!="" && $txt_vis_pc_od_s_3!="" || ($sel_vis_pc_os_sel_1_3!="" && $txt_vis_pc_os_s_3!=""))
			){
				$pc1gp1_od[]="<b>$sel_vis_pc_od_sel_1</b>";
				 
			}
			if(($txt_vis_pc_od_s!="" || $txt_vis_pc_os_s!="") ||
			($txt_vis_pc_od_s_2!="" || $txt_vis_pc_os_s_2!="") ||
			($txt_vis_pc_od_s_3!="" || $txt_vis_pc_os_s_3!="")
			){
				if($txt_vis_pc_od_s!=""){$pc1gp1_od[]="S"; }
				$pc1gp1_od[]="<b>$txt_vis_pc_od_s</b>";
				 
			}
			if(($txt_vis_pc_od_c!="" || $txt_vis_pc_os_c!="") ||
			($txt_vis_pc_od_c_2!="" || $txt_vis_pc_os_c_2!="") ||
			($txt_vis_pc_od_c_3!="" || $txt_vis_pc_os_c_3!="")
			){
				 if($txt_vis_pc_od_c!=""){$pc1gp1_od[]="C" ; }
				
				$pc1gp1_od[]="<b>$txt_vis_pc_od_c</b>";
				
			}
			if(($txt_vis_pc_od_a!="" || $txt_vis_pc_os_a!="") ||
			($txt_vis_pc_od_a_2!="" || $txt_vis_pc_os_a_2!="") ||
			($txt_vis_pc_od_a_3!="" || $txt_vis_pc_os_a_3!="")
			){
				 if($txt_vis_pc_od_a!="" ){$pc1gp1_od[]="A"; }
				
				 if($txt_vis_pc_od_a!="" ){ $pc1gp1_od[]="<b>".$txt_vis_pc_od_a."&#176;</b>";}
				 
			}
			?>
    <td class="bdrbtm" style="width:200px;" ><?php odLable(); print implode("&nbsp;",$pc1gp1_od);?></td>
    <?php 
	/*if(
		($txt_vis_pc_od_add!="" && ($txt_vis_pc_od_s!=""||$txt_vis_pc_od_c!=""||$txt_vis_pc_od_a!="" || $txt_vis_pc_od_add!="") ) ||
		($txt_vis_pc_od_add_2!="" && ($txt_vis_pc_od_s_2!=""||$txt_vis_pc_od_c_2!=""||$txt_vis_pc_od_a_2!="" || $txt_vis_pc_od_add_2!="") ) ||
		($txt_vis_pc_od_add_3!="" && ($txt_vis_pc_od_s_3!=""||$txt_vis_pc_od_c_3!=""||$txt_vis_pc_od_a_3!="" || $txt_vis_pc_od_add_3!="") )
	)*/
	if(
		($txt_vis_pc_od_add!="" and $txt_vis_pc_od_s!="" and $txt_vis_pc_od_c!="" and $txt_vis_pc_od_a!="" and $txt_vis_pc_od_add!="") 
	){		 
		 	$pc1gp2_addod[]="Add";
			$pc1gp2_addod[]="<b>$txt_vis_pc_od_add</b>";		
	}?>
    <td class="bdrbtm" style="width:200px;" ><?php  print implode("&nbsp;",$pc1gp2_addod);?></td>
    <?php  
				if(($elem_prismPc1 == "1") || ($elem_prismPc2 == "1") || ($elem_prismPc3 == "1")){
					if(($sel_vis_pc_od_p!="") || ($sel_vis_pc_os_p!="") ||
					($sel_vis_pc_od_p_2!="") || ($sel_vis_pc_os_p_2!="") ||
					($sel_vis_pc_od_p_3!="") || ($sel_vis_pc_os_p_3!="")
					){
					 $pc1gp3_prismod[]=(!empty($sel_vis_pc_od_p)) ? "P" : "";
					
					$pc1gp3_prismod[]="<b>$sel_vis_pc_od_p</b>";
				
					}
					if((($sel_vis_pc_od_prism!="" && $sel_vis_pc_od_p != "") || ($sel_vis_pc_os_prism!="" && $sel_vis_pc_os_p != "")) ||
					(($sel_vis_pc_od_prism_2!=""  && $sel_vis_pc_od_p_2 != "" ) || ($sel_vis_pc_os_prism_2!=""  && $sel_vis_pc_os_p_2 != "")) ||
					(($sel_vis_pc_od_prism_3!="" && $sel_vis_pc_od_p_3 != "") || ($sel_vis_pc_os_prism_3!=""  && $sel_vis_pc_os_p_3 != ""))
					){
						$pc1gp3_prismod[]=(!empty($sel_vis_pc_od_prism) && !empty($sel_vis_pc_od_slash)) ? "<img src='../images/pic_vision_pc.jpg'  align='top'/>" : "";
						if(!empty($sel_vis_pc_od_prism) && !empty($sel_vis_pc_od_slash)){$pc1gp3_prismod[]="<b>$sel_vis_pc_od_prism</b>";}
						
					}
					if(($sel_vis_pc_od_slash!="") || ($sel_vis_pc_os_slash!="") ||
					($sel_vis_pc_od_slash_2!="") || ($sel_vis_pc_os_slash_2!="") ||
					($sel_vis_pc_od_slash_3!="") || ($sel_vis_pc_os_slash_3!="")
					){
						$pc1gp3_prismod[]=(!empty($sel_vis_pc_od_slash)) ? "<img src='../images/pic_vision_pc.jpg'  align='top'/>" : "";
						$pc1gp3_prismod[]="<b>$sel_vis_pc_od_slash</b>";
						 
					}
					if(($sel_vis_pc_od_sel_2!="" && $sel_vis_pc_od_slash != "") || ($sel_vis_pc_os_sel_2!="" && $sel_vis_pc_os_slash != "") ||
					($sel_vis_pc_od_sel_2_2!="" && $sel_vis_pc_od_slash_2 != "") || ($sel_vis_pc_os_sel_2_2!="" && $sel_vis_pc_os_slash_2 != "") ||
					($sel_vis_pc_od_sel_2_3!="" && $sel_vis_pc_od_slash_3 != "" ) || ($sel_vis_pc_os_sel_2_3!="" && $sel_vis_pc_os_slash_3 != "")
					){
						$pc1gp3_prismod[]="<b>$sel_vis_pc_od_sel_2</b>";
						
					}
				}
				?>
    <td class="bdrbtm" style="width:200px;"><?php print implode("&nbsp;",$pc1gp3_prismod);?></td>
    <?php 
if($sel_vis_pc_od_sel_1!="" &&($txt_vis_pc_od_s!=""||$txt_vis_pc_od_c!=""||$txt_vis_pc_od_a!="" || $txt_vis_pc_od_add!="")){

	$overrefraction1od[]="";
	if($chk_pc_near <> "Over Refraction"){
		
		
		 if($txt_vis_pc_od_near_txt!=""){$overrefraction1od[]="OD"; }
		 $overrefraction1od[]="<b>$txt_vis_pc_od_near_txt</b>";
		
	}else{
		
		 if($txt_vis_pc_od_overref_s!="" || $txt_vis_pc_od_overref_c!="" || $txt_vis_pc_od_overref_a!="" || $txt_vis_pc_od_overref_v!="" ){/*$overrefraction1od[]="OD";*/}
		
		 if($txt_vis_pc_od_overref_s!=""){ $overrefraction1od[]="S";}

		 $overrefraction1od[]="<b>".trim($txt_vis_pc_od_overref_s)."</b>";
		 if($txt_vis_pc_od_overref_c!=""){ $overrefraction1od[]="C";}
		
		 $overrefraction1od[]="<b>".trim($txt_vis_pc_od_overref_c)."</b>";
		
		 if($txt_vis_pc_od_overref_a!=""){ $overrefraction1od[]="A";}
		
		 $overrefraction1od[]="<b>".trim($txt_vis_pc_od_overref_a)."</b>";
		
		 if($txt_vis_pc_od_overref_v!="" && $txt_vis_pc_od_overref_v!="20/"){ $overrefraction1od[]="V";}
		
		 if($txt_vis_pc_od_overref_v!="" && $txt_vis_pc_od_overref_v!="20/" ){ $overrefraction1od[]="<b>".trim($txt_vis_pc_od_overref_v)."</b>";
		 
	}
	?>

<td class="bdrbtm" style="width:140px;"><?php odLable(); print(implode("&nbsp;",$overrefraction1od));?></td>
<? }?>

    <!-- Prism -->
  </tr>
  <!-- OD -->
  <!-- OS -->
  <tr>
    <?php   
					/*$pc1gp1_os[]="OS";*/		
					if(
						($sel_vis_pc_os_sel_1!="" and $txt_vis_pc_os_s!="") ||
						($sel_vis_pc_os_sel_1_2!="" and $txt_vis_pc_os_s_2!="")||
						($sel_vis_pc_os_sel_1_3!="" and $txt_vis_pc_os_s_3!="")
					){
						$pc1gp1_os[]="<b>$sel_vis_pc_os_sel_1</b>";
					} 
					if(
						($sel_vis_pc_os_sel_1!="" and $txt_vis_pc_os_s!="") ||
						($sel_vis_pc_os_sel_1_2!="" and $txt_vis_pc_os_s_2!="")||
						($sel_vis_pc_os_sel_1_3!="" and $txt_vis_pc_os_s_3!="")
					){
						if($txt_vis_pc_os_s!="" and $sel_vis_pc_os_sel_1!=""){
							$pc1gp1_os[]="S"; 
							$pc1gp1_os[]="<b>$txt_vis_pc_os_s</b>";						
						}
					}
					if(
						($sel_vis_pc_os_sel_1!="" and $txt_vis_pc_os_c!="") ||
						($sel_vis_pc_os_sel_1_2!="" and $txt_vis_pc_os_c_2!="") ||
						($sel_vis_pc_os_sel_1_3!="" and $txt_vis_pc_os_c_3!="")
					){
						if($txt_vis_pc_os_c!="" and $sel_vis_pc_os_sel_1!=""){
							$pc1gp1_os[]="C"; 
							$pc1gp1_os[]="<b>$txt_vis_pc_os_c</b>";
						}
					}
					if(
						($sel_vis_pc_os_sel_1!="" and $txt_vis_pc_os_a!="") ||
						($sel_vis_pc_os_sel_1_2!="" and $txt_vis_pc_os_a_2!="") ||
						($sel_vis_pc_os_sel_1_3!="" and $txt_vis_pc_os_a_3!="")
					){
						 if($txt_vis_pc_os_a!="" and $sel_vis_pc_os_sel_1!=""){
						 	$pc1gp1_os[]="A";
							$pc1gp1_os[]="<b>".$txt_vis_pc_os_a."&#176;</b>";
						 }
					}?>
    <td  style="width:200px;"><?php osLable(); print implode("&nbsp;",$pc1gp1_os);?></td>
    <?php
	/*if(($txt_vis_pc_od_add!="" && ($txt_vis_pc_od_s!=""||$txt_vis_pc_od_c!=""||$txt_vis_pc_od_a!="" || $txt_vis_pc_od_add!="") ) ||
	($txt_vis_pc_od_add_2!="" && ($txt_vis_pc_od_s_2!=""||$txt_vis_pc_od_c_2!=""||$txt_vis_pc_od_a_2!="" || $txt_vis_pc_od_add_2!="") ) ||
	($txt_vis_pc_od_add_3!="" && ($txt_vis_pc_od_s_3!=""||$txt_vis_pc_od_c_3!=""||$txt_vis_pc_od_a_3!="" || $txt_vis_pc_od_add_3!="") )
	)*/
	if(
		($txt_vis_pc_os_add!="" and $txt_vis_pc_os_s!="" and $txt_vis_pc_os_c!="" and $txt_vis_pc_os_a!="" and $txt_vis_pc_os_add!="")
	){		 
		 if(strlen($txt_vis_pc_os_add)<>1){
			$pc1gp2_addos[]="Add"; 						 
			$pc1gp2_addos[]="<b>$txt_vis_pc_os_add</b>"; 						
		}		
	}?>
    <td  style="width:200px;"><?php print implode("&nbsp;",$pc1gp2_addos);?></td>
    <?php
						if(($elem_prismPc1 == "1") || ($elem_prismPc2 == "1") || ($elem_prismPc3 == "1")){
							if(($sel_vis_pc_od_p!="") || ($sel_vis_pc_os_p!="") ||
							($sel_vis_pc_od_p_2!="") || ($sel_vis_pc_os_p_2!="") ||
							($sel_vis_pc_od_p_3!="") || ($sel_vis_pc_os_p_3!="")
							){
								$pc1gp3_prismos[]=(!empty($sel_vis_pc_os_p)) ? "P" : "";
								$pc1gp3_prismos[]="<b>$sel_vis_pc_os_p</b>";
								 
							}
							if((($sel_vis_pc_od_prism!="" && $sel_vis_pc_od_p != "") || ($sel_vis_pc_os_prism!="" && $sel_vis_pc_os_p != "")) ||
							(($sel_vis_pc_od_prism_2!=""  && $sel_vis_pc_od_p_2 != "" ) || ($sel_vis_pc_os_prism_2!=""  && $sel_vis_pc_os_p_2 != "")) ||
							(($sel_vis_pc_od_prism_3!="" && $sel_vis_pc_od_p_3 != "") || ($sel_vis_pc_os_prism_3!=""  && $sel_vis_pc_os_p_3 != ""))
							){
								$pc1gp3_prismos[]=(!empty($sel_vis_pc_os_prism) && !empty($sel_vis_pc_os_slash)) ? "<img src='../images/pic_vision_pc.jpg'  align='top'/>" : "";
								if(!empty($sel_vis_pc_os_prism) && !empty($sel_vis_pc_os_slash)){$pc1gp3_prismos[]="<b>$sel_vis_pc_os_prism</b>";}
								
							}
							if(($sel_vis_pc_od_slash!="") || ($sel_vis_pc_os_slash!="") ||
							($sel_vis_pc_od_slash_2!="") || ($sel_vis_pc_os_slash_2!="") ||
							($sel_vis_pc_od_slash_3!="") || ($sel_vis_pc_os_slash_3!="")
							){
								$pc1gp3_prismos[]=(!empty($sel_vis_pc_os_slash)) ? "<img src='../images/pic_vision_pc.jpg'  align='top'/>" : "";
								$pc1gp3_prismos[]="<b>$sel_vis_pc_os_slash</b>";
								
							}
							if(($sel_vis_pc_od_sel_2!="" && $sel_vis_pc_od_slash != "") || ($sel_vis_pc_os_sel_2!="" && $sel_vis_pc_os_slash != "") ||
							($sel_vis_pc_od_sel_2_2!="" && $sel_vis_pc_od_slash_2 != "") || ($sel_vis_pc_os_sel_2_2!="" && $sel_vis_pc_os_slash_2 != "") ||
							($sel_vis_pc_od_sel_2_3!="" && $sel_vis_pc_od_slash_3 != "" ) || ($sel_vis_pc_os_sel_2_3!="" && $sel_vis_pc_os_slash_3 != "")
							){
								$pc1gp3_prismos[]="<b>$sel_vis_pc_os_sel_2</b>";
								
							}
						}
						?>
    <td  style="width:200px;"><?php print implode("&nbsp;",$pc1gp3_prismos);?></td>
<?php $haveValue=0;
if($sel_vis_pc_od_sel_1!="" &&($txt_vis_pc_od_s!=""||$txt_vis_pc_od_c!=""||$txt_vis_pc_od_a!="" || $txt_vis_pc_od_add!="")){
	$overrefraction1os[]="";
	if($chk_pc_near <> "Over Refraction"){
		 if($txt_vis_pc_os_near_txt!=""){
		 	//$overrefraction1os[]="OS";
			//$overrefraction1os[]="<b>$txt_vis_pc_os_near_txt</b>";
		 }
	}else{
		$haveValue=0;
		if($txt_vis_pc_os_overref_s!="" || $txt_vis_pc_os_overref_c!="" || $txt_vis_pc_os_overref_a!="" || $txt_vis_pc_os_overref_v!=""){
			/*$overrefraction1os[]="OS";*/
			$haveValue=1;
		}
		if($txt_vis_pc_os_overref_s!=""){
			$overrefraction1os[]="S";
			$overrefraction1os[]="<b>".trim($txt_vis_pc_os_overref_s)."</b>";
		}
		if($txt_vis_pc_os_overref_c!=""){
			$overrefraction1os[]="C"; 
			$overrefraction1os[]="<b>".trim($txt_vis_pc_os_overref_c)."</b>";
		}
		if($txt_vis_pc_os_overref_a!=""){
			$overrefraction1os[]="A"; 
			$overrefraction1os[]="<b>".trim($txt_vis_pc_os_overref_a)."</b>";
		}
		if($txt_vis_pc_os_overref_v!="" && $txt_vis_pc_os_overref_v!="20/" ){
			$overrefraction1os[]="V";
			$overrefraction1os[]="<b>". trim($txt_vis_pc_os_overref_v)."</b>";
		}
	} 
?>
	<td  style="width:140px;" ><?php if($haveValue==1){osLable(); print(implode("&nbsp;",$overrefraction1os));}?></td>
<?php
}?>
</tr>
  <!-- OS -->
  <?php 
	}
	?>
  <?php 
if($sel_vis_pc_od_sel_1!="" &&($txt_vis_pc_od_s!=""||$txt_vis_pc_od_c!=""||$txt_vis_pc_od_a!="" || $txt_vis_pc_od_add!="")){
	?>
</table>
<? } ?>
<?php  
}
?>	
<!--End 1st PC -->
<!-- 2nd PC -->
<?php
if($sel_vis_pc_od_sel_1_2!="" &&($txt_vis_pc_od_s_2!=""||$txt_vis_pc_od_c_2!=""||$txt_vis_pc_od_a_2!="" || $txt_vis_pc_od_add_2!="")){
	?>
<!-- Start 2nd PC -->
<table style="width:760px;" class="border" cellpadding="0" cellspacing="0">
  <tr>
    <td style="width:760px;" class="tb_subheading"><b>PC 2nd</b></td>
  </tr>
</table>
<table style="width:740px;" class="border" cellpadding="0" cellspacing="0">
	<tr>
		<td class="bdrbtm" style="width:200px;" >2nd</td>
		<td class="bdrbtm" style="width:200px;">&nbsp;</td>
		<?php  
			$prismTxt2 = '';	
			if($elem_prismPc2){
				$prismTxt2 = 'Prism';	
			}	
			
			$overRefractionTxt2 = "";
			if($chk_pc_near_2 == "Over Refraction"){
				$overRefractionTxt2 = "Over Refraction";
			}
		?>
		<td class="bdrbtm" style="width:200px;" ><?php echo $prismTxt2; ?></td>
		<td class="bdrbtm" style="width:140px;" ><?php echo $overRefractionTx2; ?></td>
	</tr>
	<!-- OD -->
	<tr>
	<?php 
	        $pc2gp1_od[]="";
			$pc2gp1_os[]="";
			$pc2gp2_addod[]="";
			$pc2gp2_addos[]="";
			$pc2gp3_prismod[]="";
			$pc2gp3_prismos[]="";
		/*$pc2gp1_od[]="OD";*/
		
		if(($sel_vis_pc_od_sel_1!="" && $txt_vis_pc_od_s!="" || ($sel_vis_pc_os_sel_1!="" && $txt_vis_pc_os_s!="")) ||
		($sel_vis_pc_od_sel_1_2!="" && $txt_vis_pc_od_s_2!="" || ($sel_vis_pc_os_sel_1_2!="" && $txt_vis_pc_os_s_2!="")) ||
		($sel_vis_pc_od_sel_1_3!="" && $txt_vis_pc_od_s_3!="" || ($sel_vis_pc_os_sel_1_3!="" && $txt_vis_pc_os_s_3!=""))
		){
			
			$pc2gp1_od[]="<b>$sel_vis_pc_od_sel_1_2</b>";
			
		}
		if(($txt_vis_pc_od_s!="" || $txt_vis_pc_os_s!="") ||
		($txt_vis_pc_od_s_2!="" || $txt_vis_pc_os_s_2!="") ||
		($txt_vis_pc_od_s_3!="" || $txt_vis_pc_os_s_3!="")
		){
			 if($txt_vis_pc_od_s_2!=""){$pc2gp1_od[]="S"; }
			
			$pc2gp1_od[]="<b>$txt_vis_pc_od_s_2</b>";
			
		}
		if(($txt_vis_pc_od_c!="" || $txt_vis_pc_os_c!="") ||
		($txt_vis_pc_od_c_2!="" || $txt_vis_pc_os_c_2!="") ||
		($txt_vis_pc_od_c_3!="" || $txt_vis_pc_os_c_3!="")
		){
			if($txt_vis_pc_od_c_2!=""){$pc2gp1_od[]="C"; }
			
			$pc2gp1_od[]="<b>$txt_vis_pc_od_c_2</b>";
			
		}
		if(($txt_vis_pc_od_a!="" || $txt_vis_pc_os_a!="") ||
		($txt_vis_pc_od_a_2!="" || $txt_vis_pc_os_a_2!="") ||
		($txt_vis_pc_od_a_3!="" || $txt_vis_pc_os_a_3!="")
		){
			 if($txt_vis_pc_od_a_2!="" ){$pc2gp1_od[]="A"; } 
			 if($txt_vis_pc_od_a_2!="" ){ $pc2gp1_od[]="<b>".$txt_vis_pc_od_a_2."&#176;</b>";}
			
		}
		?>
			<td class="bdrbtm" style="width:200px;" ><?php odLable(); print implode("&nbsp;",$pc2gp1_od);?></td>
		<?php
		/*if(($txt_vis_pc_od_add!="" && ($txt_vis_pc_od_s!=""||$txt_vis_pc_od_c!=""||$txt_vis_pc_od_a!="" || $txt_vis_pc_od_add!="") ) ||
		($txt_vis_pc_od_add_2!="" && ($txt_vis_pc_od_s_2!=""||$txt_vis_pc_od_c_2!=""||$txt_vis_pc_od_a_2!="" || $txt_vis_pc_od_add_2!="") ) ||
		($txt_vis_pc_od_add_3!="" && ($txt_vis_pc_od_s_3!=""||$txt_vis_pc_od_c_3!=""||$txt_vis_pc_od_a_3!="" || $txt_vis_pc_od_add_3!="") )
		)*/
		if(
			($txt_vis_pc_od_add_2!="" and $txt_vis_pc_od_s_2!="" and $txt_vis_pc_od_c_2!="" and $txt_vis_pc_od_a_2!="" and $txt_vis_pc_od_add_2!="")
		){
			 $pc2gp2_addod[]="Add"; 
			 $pc2gp2_addod[]="<b>$txt_vis_pc_od_add_2</b>";
		}
		?>
			<td class="bdrbtm" style="width:200px;"><?php  print implode("&nbsp;",$pc2gp2_addod);?></td>
		
		<!-- Prism -->
		<?php 

		if(($elem_prismPc1 == "1") || ($elem_prismPc2 == "1") || ($elem_prismPc3 == "1")){
			if(($sel_vis_pc_od_p!="") || ($sel_vis_pc_os_p!="") ||
			($sel_vis_pc_od_p_2!="") || ($sel_vis_pc_os_p_2!="") ||
			($sel_vis_pc_od_p_3!="") || ($sel_vis_pc_os_p_3!="")
			){
				$pc2gp3_prismod[]= (!empty($sel_vis_pc_od_p_2)) ? "P" : "";				
				$pc2gp3_prismod[]="<b>$sel_vis_pc_od_p_2</b>";
			}
			if((($sel_vis_pc_od_prism!="" && $sel_vis_pc_od_p != "") || ($sel_vis_pc_os_prism!="" && $sel_vis_pc_os_p != "")) ||
			(($sel_vis_pc_od_prism_2!=""  && $sel_vis_pc_od_p_2 != "" ) || ($sel_vis_pc_os_prism_2!=""  && $sel_vis_pc_os_p_2 != "")) ||
			(($sel_vis_pc_od_prism_3!="" && $sel_vis_pc_od_p_3 != "") || ($sel_vis_pc_os_prism_3!=""  && $sel_vis_pc_os_p_3 != ""))
			){
				$pc2gp3_prismod[]=(!empty($sel_vis_pc_od_prism_2) && !empty($sel_vis_pc_od_slash_2)) ? "<img src='../images/pic_vision_pc.jpg'  align='top'/>" : "";
				 if(!empty($sel_vis_pc_od_prism_2) && !empty($sel_vis_pc_od_slash_2)){$pc2gp3_prismod[]="<b>$sel_vis_pc_od_prism_2</b>";}
				
			}
			if(($sel_vis_pc_od_slash!="") || ($sel_vis_pc_os_slash!="") ||
			($sel_vis_pc_od_slash_2!="") || ($sel_vis_pc_os_slash_2!="") ||
			($sel_vis_pc_od_slash_3!="") || ($sel_vis_pc_os_slash_3!="")
			){
				$pc2gp3_prismod[]= (!empty($sel_vis_pc_od_slash_2)) ? "<img src='../images/pic_vision_pc.jpg'  align='top'/>" : "";
				$pc2gp3_prismod[]="<b>$sel_vis_pc_od_slash_2</b>";
				
			}
			if(($sel_vis_pc_od_sel_2!="" && $sel_vis_pc_od_slash != "") || ($sel_vis_pc_os_sel_2!="" && $sel_vis_pc_os_slash != "") ||
			($sel_vis_pc_od_sel_2_2!="" && $sel_vis_pc_od_slash_2 != "") || ($sel_vis_pc_os_sel_2_2!="" && $sel_vis_pc_os_slash_2 != "") ||
			($sel_vis_pc_od_sel_2_3!="" && $sel_vis_pc_od_slash_3 != "" ) || ($sel_vis_pc_os_sel_2_3!="" && $sel_vis_pc_os_slash_3 != "")
			){
				$pc2gp3_prismod[]="<b>$sel_vis_pc_od_sel_2_2</b>";
				
			}																								
		}
		?>
			<td class="bdrbtm" style="width:200px;"><?php print implode("&nbsp;",$pc2gp3_prismod);?></td>
<?php 
$haveValue=0;
if($sel_vis_pc_od_sel_1_2!="" &&($txt_vis_pc_od_s_2!=""||$txt_vis_pc_od_c_2!=""||$txt_vis_pc_od_a_2!="" || $txt_vis_pc_od_add_2!="")){
	$overrefraction2od[]="";
	if($chk_pc_near_2 <> "Over Refraction"){	
		if($txt_vis_pc_od_near_txt_2!=""){
			//$overrefraction2od[]="OD";
			//$overrefraction2od[]="<b>$txt_vis_pc_od_near_txt_2</b>";
		}
	}else{
		
		 if($txt_vis_pc_od_overref_s_2!="" || $txt_vis_pc_od_overref_c_2!="" || $txt_vis_pc_od_overref_a_2!="" || $txt_vis_pc_od_overref_v_2!="" ){
			 /*$overrefraction2od[]="OD";*/
		 	$haveValue=1;
		 }
		 if($txt_vis_pc_od_overref_s_2!=""){
			$overrefraction2od[]="S";
			$overrefraction2od[]="<b>$txt_vis_pc_od_overref_s_2</b>";
		 }
		 if($txt_vis_pc_od_overref_c_2!=""){
			$overrefraction2od[]="C"; 
			$overrefraction2od[]="<b>$txt_vis_pc_od_overref_c_2</b>";
		 }
		 if($txt_vis_pc_od_overref_a_2!=""){
			$overrefraction2od[]="A"; 
			 $overrefraction2od[]="<b>$txt_vis_pc_od_overref_a_2</b>";
		 }
		 if($txt_vis_pc_od_overref_v_2!="" && $txt_vis_pc_od_overref_v_2!="20/"){
			$overrefraction2od[]="V" ;
			$overrefraction2od[]="<b> $txt_vis_pc_od_overref_v_2</b>";
		}
	}
?>
	<td class="bdrbtm" style="width:140px;" ><?php if($haveValue==1){odLable(); print implode("&nbsp;",$overrefraction2od);}?></td>
<?php  
}?>
		<!-- Prism -->
	</tr>
	<!-- OD -->
	<!-- OS -->
	<tr >
	<?php
		/*$pc2gp1_os[]="OS";*/	
		if(
			($sel_vis_pc_os_sel_1!="" && $txt_vis_pc_os_s!="") ||
			($sel_vis_pc_os_sel_1_2!="" && $txt_vis_pc_os_s_2!="") ||
			($sel_vis_pc_os_sel_1_3!="" && $txt_vis_pc_os_s_3!="")
		){
			$pc2gp1_os[]="<b>$sel_vis_pc_os_sel_1_2</b>";
			
		}
		if(
			($sel_vis_pc_os_sel_1!="" || $txt_vis_pc_os_s!="") ||
			($sel_vis_pc_os_sel_1_2!="" || $txt_vis_pc_os_s_2!="") ||
			($sel_vis_pc_os_sel_1_3!="" || $txt_vis_pc_os_s_3!="")
		){
			if($txt_vis_pc_os_s_2!="" and $sel_vis_pc_os_sel_1_2!=""){
				$pc2gp1_os[]="S"; 
				$pc2gp1_os[]= "<b>$txt_vis_pc_os_s_2</b>";
			} 
		}
		if(
			($sel_vis_pc_os_sel_1!="" || $txt_vis_pc_os_c!="") ||
			($sel_vis_pc_os_sel_1_2!="" || $txt_vis_pc_os_c_2!="") ||
			($sel_vis_pc_os_sel_1_3!="" || $txt_vis_pc_os_c_3!="")
		){
			if($txt_vis_pc_os_c_2!="" and $sel_vis_pc_os_sel_1_2!=""){
				$pc2gp1_os[]="C";
			 	$pc2gp1_os[]="<b>$txt_vis_pc_os_c_2</b>";
			}
		}
		if(($sel_vis_pc_os_sel_1!="" || $txt_vis_pc_os_a!="") ||
		($sel_vis_pc_os_sel_1_2!="" || $txt_vis_pc_os_a_2!="") ||
		($sel_vis_pc_os_sel_1_3!="" || $txt_vis_pc_os_a_3!="")
		){			
				if($txt_vis_pc_os_a_2!="" and $sel_vis_pc_os_sel_1_2!=""){
					$pc2gp1_os[]="A";
					$pc2gp1_os[]="<b>".$txt_vis_pc_os_a_2."&#176;</b>";
				}
							
		}
	?>
		<td class="bdrbtm" style="width:200px;"><?php osLable(); print implode("&nbsp;",$pc2gp1_os);?></td>
	<?php
		/*if(($txt_vis_pc_od_add!="" && ($txt_vis_pc_od_s!=""||$txt_vis_pc_od_c!=""||$txt_vis_pc_od_a!="" || $txt_vis_pc_od_add!="") ) ||
		($txt_vis_pc_od_add_2!="" && ($txt_vis_pc_od_s_2!=""||$txt_vis_pc_od_c_2!=""||$txt_vis_pc_od_a_2!="" || $txt_vis_pc_od_add_2!="") ) ||
		($txt_vis_pc_od_add_3!="" && ($txt_vis_pc_od_s_3!=""||$txt_vis_pc_od_c_3!=""||$txt_vis_pc_od_a_3!="" || $txt_vis_pc_od_add_3!="") )
		)*/
		if(
			($txt_vis_pc_os_add_2!="" and $txt_vis_pc_os_s_2!="" and $txt_vis_pc_os_c_2!="" and $txt_vis_pc_os_a_2!="" and $txt_vis_pc_os_add_2!="")		
		){
			 if(strlen($txt_vis_pc_os_add_2)<>1){
				 $pc2gp2_addos[]="Add";
				 $pc2gp2_addos[]="<b>$txt_vis_pc_os_add_2</b>"; 
			 }
		}
		?>
		<td class="bdrbtm" style="width:200px;"><?php  print implode("&nbsp;",$pc2gp2_addos);?></td>
	
		<!-- Prism -->
		<?php 
		if(($elem_prismPc1 == "1") || ($elem_prismPc2 == "1") || ($elem_prismPc3 == "1")){
			if(($sel_vis_pc_od_p!="") || ($sel_vis_pc_os_p!="") ||
			($sel_vis_pc_od_p_2!="") || ($sel_vis_pc_os_p_2!="") ||
			($sel_vis_pc_od_p_3!="") || ($sel_vis_pc_os_p_3!="")
			){
				$pc2gp3_prismos[]=(!empty($sel_vis_pc_os_p_2)) ? "P" : "";
				
				$pc2gp3_prismos[]="<b>$sel_vis_pc_os_p_2</b>";
				
			}
			if((($sel_vis_pc_od_prism!="" && $sel_vis_pc_od_p != "") || ($sel_vis_pc_os_prism!="" && $sel_vis_pc_os_p != "")) ||
			(($sel_vis_pc_od_prism_2!=""  && $sel_vis_pc_od_p_2 != "" ) || ($sel_vis_pc_os_prism_2!=""  && $sel_vis_pc_os_p_2 != "")) ||
			(($sel_vis_pc_od_prism_3!="" && $sel_vis_pc_od_p_3 != "") || ($sel_vis_pc_os_prism_3!=""  && $sel_vis_pc_os_p_3 != ""))
			){
				$pc2gp3_prismos[]= (!empty($sel_vis_pc_os_prism_2) && !empty($sel_vis_pc_os_slash_2)) ? "<img src='../images/pic_vision_pc.jpg'  align='top'/>" : "";
				 if(!empty($sel_vis_pc_os_prism_2) && !empty($sel_vis_pc_os_slash_2)){$pc2gp3_prismos[]="<b>$sel_vis_pc_os_prism_2</b>";}
				
			}
			if(($sel_vis_pc_od_slash!="") || ($sel_vis_pc_os_slash!="") ||
			($sel_vis_pc_od_slash_2!="") || ($sel_vis_pc_os_slash_2!="") ||
			($sel_vis_pc_od_slash_3!="") || ($sel_vis_pc_os_slash_3!="")
			){
				$pc2gp3_prismos[]=(!empty($sel_vis_pc_os_slash_2)) ? "<img src='../images/pic_vision_pc.jpg'  align='top'/>" : "";
				$pc2gp3_prismos[]="<b>$sel_vis_pc_os_slash_2</b>";
				
			}
			if(($sel_vis_pc_od_sel_2!="" && $sel_vis_pc_od_slash != "") || ($sel_vis_pc_os_sel_2!="" && $sel_vis_pc_os_slash != "") ||
			($sel_vis_pc_od_sel_2_2!="" && $sel_vis_pc_od_slash_2 != "") || ($sel_vis_pc_os_sel_2_2!="" && $sel_vis_pc_os_slash_2 != "") ||
			($sel_vis_pc_od_sel_2_3!="" && $sel_vis_pc_od_slash_3 != "" ) || ($sel_vis_pc_os_sel_2_3!="" && $sel_vis_pc_os_slash_3 != "")
			){
				$pc2gp3_prismos[]="<b>$sel_vis_pc_os_sel_2_2</b>";
				
				}
			}
			?>
		<td class="bdrbtm" style="width:200px;"><?php  print implode("&nbsp;",$pc2gp3_prismos);?></td>
<?php
 
if($sel_vis_pc_od_sel_1_2!="" &&($txt_vis_pc_od_s_2!=""||$txt_vis_pc_od_c_2!=""||$txt_vis_pc_od_a_2!="" || $txt_vis_pc_od_add_2!="")){
	$overrefraction2os[]="";
	if($chk_pc_near_2 <> "Over Refraction"){	
		if($txt_vis_pc_os_near_txt_2!=""){
			/*$overrefraction2os[]="OS";*/ 
		 	//$overrefraction2os[]="<b>$txt_vis_pc_os_near_txt_2</b>";		
		}		
	}else{		
		$haveValue=0;
		 if($txt_vis_pc_os_overref_s_2!="" || $txt_vis_pc_os_overref_c_2!="" || $txt_vis_pc_os_overref_a_2!="" || $txt_vis_pc_os_overref_v_2!=""){
		 	/*$overrefraction2os[]="OS";*/
			$haveValue=1;
		 }
		 if($txt_vis_pc_os_overref_s_2!=""){
		 	$overrefraction2os[]="S";
			$overrefraction2os[]="<b>".trim($txt_vis_pc_os_overref_s_2)."</b>";		
		 }
			 
		 if($txt_vis_pc_os_overref_c_2!=""){
		 	$overrefraction2os[]="C";
			$overrefraction2os[]="<b>$txt_vis_pc_os_overref_c_2</b>";		
		 }		
		 	
		 if($txt_vis_pc_os_overref_a_2!=""){
		 	$overrefraction2os[]="A";
			$overrefraction2os[]="<b>$txt_vis_pc_os_overref_a_2</b>";		
		 }		
		 	
		 if($txt_vis_pc_os_overref_v_2!="" && $txt_vis_pc_os_overref_v_2!="20/"){
		 	$overrefraction2os[]="V";
			$overrefraction2os[]="<b>$txt_vis_pc_os_overref_v_2</b>";
		 }		
		 /*if($txt_vis_pc_os_overref_v_2!="" && $txt_vis_pc_os_overref_v_2!="20/"){
		 	$overrefraction2os[]="<b>$txt_vis_pc_os_overref_v_2</b>";
		 }	*/	
	}
	?>
		<td class="bdrbtm" style="width:140px;" ><?php if($haveValue==1){osLable(); print implode("&nbsp;",$overrefraction2os);}?></td>
	<?php 
}
?>
			<!-- Prism -->
  </tr>
			<!-- OS -->
</table>
<!-- End  2nd Pc-->
<?php  
}
?>

<!-- 2nd PC -->
<!-- End OverRefraction 2nd Pc-->

<?php 
if($sel_vis_pc_od_sel_1_3!="" &&($txt_vis_pc_od_s_3!=""||$txt_vis_pc_od_c_3!=""||$txt_vis_pc_od_a_3!="" || $txt_vis_pc_od_add_3!="")){
?>
<!-- 3rd PC -->
<table class="border" cellpadding="0" cellspacing="0" style="width:760px;">
  <tr>
    <td  class="tb_subheading" style="width:760px;"><b>PC 3rd</b></td>
  </tr>
</table>
<table class="border" cellpadding="0" cellspacing="0" style="width:740px;">
		<tr >
		<td style="width:200px;">3rd</td>
		<td style="width:200px;">&nbsp;</td>
		<?php 
			$prismTxt3 = '';	
			if($elem_prismPc32){
				$prismTxt3 = 'Prism';	
			}				
			$overRefractionTxt3 = "";
			if($chk_pc_near_3 == "Over Refraction"){
				$overRefractionTxt3 = "Over Refraction";
			}
		?>
		<td style="width:200px;"><?php echo $prismTxt3; ?></td>
		<td style="width:140px;" ><?php echo $overRefractionTx3; ?></td>
		</tr>
		<!-- OD -->
		<tr>

			<?php 
			$pc3gp1_od[]="";
			$pc3gp1_os[]="";
			$pc3gp2_addod[]="";
			$pc3gp2_addos[]="";
			$pc3gp2_prismod[]="";
			$pc3gp2_prismos[]="";
			/*$pc3gp1_od[]="OD";*/
			
			 
			if(($sel_vis_pc_od_sel_1!="" && $txt_vis_pc_od_s!="" || ($sel_vis_pc_os_sel_1!="" && $txt_vis_pc_os_s!="")) ||
			($sel_vis_pc_od_sel_1_2!="" && $txt_vis_pc_od_s_2!="" || ($sel_vis_pc_os_sel_1_2!="" && $txt_vis_pc_os_s_2!="")) ||
			($sel_vis_pc_od_sel_1_3!="" && $txt_vis_pc_od_s_3!="" || ($sel_vis_pc_os_sel_1_3!="" && $txt_vis_pc_os_s_3!=""))
			){
				$pc3gp1_od[]="<b>$sel_vis_pc_od_sel_1_3</b>";
			}
			if(($txt_vis_pc_od_s!="" || $txt_vis_pc_os_s!="") ||
			($txt_vis_pc_od_s_2!="" || $txt_vis_pc_os_s_2!="") ||
			($txt_vis_pc_od_s_3!="" || $txt_vis_pc_os_s_3!="")
			){
				 if($txt_vis_pc_od_s_3!=""){$pc3gp1_od[]="S"; }
				 $pc3gp1_od[]="<b>$txt_vis_pc_od_s_3</b>";
			}
			if(($txt_vis_pc_od_c!="" || $txt_vis_pc_os_c!="") ||
			($txt_vis_pc_od_c_2!="" || $txt_vis_pc_os_c_2!="") ||
			($txt_vis_pc_od_c_3!="" || $txt_vis_pc_os_c_3!="")
			){
				 if($txt_vis_pc_od_c_3!=""){$pc3gp1_od[]="C";  }
				 $pc3gp1_od[]="<b>".$txt_vis_pc_od_c_3."</b>";
			}
			if(($txt_vis_pc_od_a!="" || $txt_vis_pc_os_a!="") ||
			($txt_vis_pc_od_a_2!="" || $txt_vis_pc_os_a_2!="") ||
			($txt_vis_pc_od_a_3!="" || $txt_vis_pc_os_a_3!="")
			){
				if($txt_vis_pc_od_a_3!="" ){$pc3gp1_od[]="A"; }
				if($txt_vis_pc_od_a_3!="" ){$pc3gp1_od[]="<b>".$txt_vis_pc_od_a_3."&#176;</b>";}
			}
?>
<td  style="width:200px;" class="bdrbtm" ><?php odLable(); print implode("&nbsp;",$pc3gp1_od);?></td>
<?php	 
	if(
		($txt_vis_pc_od_add_3!="" and $txt_vis_pc_od_s_3!="" and $txt_vis_pc_od_c_3!="" and $txt_vis_pc_od_a_3!="" and $txt_vis_pc_od_add_3!="")
	){
		if(strlen($txt_vis_pc_od_add_3)<>1){
			$pc3gp2_addod[]="Add";
			$pc3gp2_addod[]="<b>$txt_vis_pc_od_add_3</b>"; 
		}
	}
?>
<td style="width:200px;" class="bdrbtm" ><?php  print implode("&nbsp;",$pc3gp2_addod);?></td>
<?php

			if(($elem_prismPc1 == "1") || ($elem_prismPc2 == "1") || ($elem_prismPc3 == "1")){
				if(($sel_vis_pc_od_p!="") || ($sel_vis_pc_os_p!="") ||
				($sel_vis_pc_od_p_2!="") || ($sel_vis_pc_os_p_2!="") ||
				($sel_vis_pc_od_p_3!="") || ($sel_vis_pc_os_p_3!="")
				){
				
				
				$pc3gp2_prismod[]= (!empty($sel_vis_pc_od_p_3)) ? "P" : "";
				
				$pc3gp2_prismod[]="<b>$sel_vis_pc_od_p_3</b>";
				
			}
			if((($sel_vis_pc_od_prism!="" && $sel_vis_pc_od_p != "") || ($sel_vis_pc_os_prism!="" && $sel_vis_pc_os_p != "")) ||
			(($sel_vis_pc_od_prism_2!=""  && $sel_vis_pc_od_p_2 != "" ) || ($sel_vis_pc_os_prism_2!=""  && $sel_vis_pc_os_p_2 != "")) ||
			(($sel_vis_pc_od_prism_3!="" && $sel_vis_pc_od_p_3 != "") || ($sel_vis_pc_os_prism_3!=""  && $sel_vis_pc_os_p_3 != ""))
			){
				
				$pc3gp2_prismod[]=(!empty($sel_vis_pc_od_prism_3) && !empty($sel_vis_pc_od_slash_3)) ? "<img src='../images/pic_vision_pc.jpg'  align='top'/>" : "";
				 if(!empty($sel_vis_pc_od_prism_3) && !empty($sel_vis_pc_od_slash_3)){$pc3gp2_prismod[]="<b>$sel_vis_pc_od_prism_3</b>";}
				
			}
			if(($sel_vis_pc_od_slash!="") || ($sel_vis_pc_os_slash!="") ||
			($sel_vis_pc_od_slash_2!="") || ($sel_vis_pc_os_slash_2!="") ||
			($sel_vis_pc_od_slash_3!="") || ($sel_vis_pc_os_slash_3!="")
			){
				
				 $pc3gp2_prismod[]=(!empty($sel_vis_pc_od_slash_3)) ? "<img src='../images/pic_vision_pc.jpg'  align='top'/>" : "";
				$pc3gp2_prismod[]="<b>$sel_vis_pc_od_slash_3</b>";
				
			}
			if(($sel_vis_pc_od_sel_2!="" && $sel_vis_pc_od_slash != "") || ($sel_vis_pc_os_sel_2!="" && $sel_vis_pc_os_slash != "") ||
			($sel_vis_pc_od_sel_2_2!="" && $sel_vis_pc_od_slash_2 != "") || ($sel_vis_pc_os_sel_2_2!="" && $sel_vis_pc_os_slash_2 != "") ||
			($sel_vis_pc_od_sel_2_3!="" && $sel_vis_pc_od_slash_3 != "" ) || ($sel_vis_pc_os_sel_2_3!="" && $sel_vis_pc_os_slash_3 != "")
			){
				
				$pc3gp2_prismod[]="<b>$sel_vis_pc_od_sel_2_3</b>";
				
			}
		}
		?>
 <td style="width:200px;" class="bdrbtm"><?php  print implode("&nbsp;",$pc3gp2_prismod);?></td>

<?php 
if($sel_vis_pc_od_sel_1_3!="" &&($txt_vis_pc_od_s_3!=""||$txt_vis_pc_od_c_3!=""||$txt_vis_pc_od_a_3!="" || $txt_vis_pc_od_add_3!="")){
?>

	<?php 
$overrefraction3od[]="";
if($chk_pc_near_3 <> "Over Refraction"){	
	 if($txt_vis_pc_od_near_txt_3!=""){
	 /*$overrefraction3od[]="OD";*/ 
	 //$overrefraction3od[]="<b>$txt_vis_pc_od_near_txt_3</b>";
	 }
}else{	
	$haveValue=0;
	 if($txt_vis_pc_od_overref_s_3!="" || $txt_vis_pc_od_overref_c_3!="" || $txt_vis_pc_od_overref_a_3!="" || $txt_vis_pc_od_overref_v_3!="" ){
	 	/*$overrefraction3od[]="OD";*/
		$haveValue=1;
	 }
	if($txt_vis_pc_od_overref_s_3!=""){
		$overrefraction3od[]="S";
		$overrefraction3od[]="<b>$txt_vis_pc_od_overref_s_3</b>";
	}
	if($txt_vis_pc_od_overref_c_3!=""){
		$overrefraction3od[]="C"; 
		$overrefraction3od[]="<b>$txt_vis_pc_od_overref_c_3</b>";
	}
	if($txt_vis_pc_od_overref_a_3!=""){$overrefraction3od[]="A"; 
		$overrefraction3od[]="<b>$txt_vis_pc_od_overref_a_3</b>";
	}
	if($txt_vis_pc_od_overref_v_3!="" && $txt_vis_pc_od_overref_v_3!="20/"){
		$overrefraction3od[]="V"; 
		$overrefraction3od[]="<b>$txt_vis_pc_od_overref_v_3</b>";
	}	
	//if($txt_vis_pc_od_overref_v_3!="" && $txt_vis_pc_od_overref_v_3!="20/" ){ $overrefraction3od[]="<b>$txt_vis_pc_od_overref_v_3</b>";}
	}
?>

<td style="width:140px;" class="bdrbtm"><?php if($haveValue==1){odLable(); print implode("&nbsp;",$overrefraction3od);}?></td>
<?php } ?>
<!-- Prism -->
	</tr>
	<!-- OD -->
	<!-- OS -->
	<tr>
		<?php /*$pc3gp1_os[]="OS";*/
		if(
			($sel_vis_pc_os_sel_1!="" && $txt_vis_pc_os_s!="") ||
			($sel_vis_pc_os_sel_1_2!="" && $txt_vis_pc_os_s_2!="") ||
			($sel_vis_pc_os_sel_1_3!="" && $txt_vis_pc_os_s_3!="")
		){
			$pc3gp1_os[]="<b>$sel_vis_pc_os_sel_1_3</b>";
		}
		if(
			($sel_vis_pc_os_sel_1!="" || $txt_vis_pc_os_s!="") ||
			($sel_vis_pc_os_sel_1_2!="" || $txt_vis_pc_os_s_2!="") ||
			($sel_vis_pc_os_sel_1_3!="" || $txt_vis_pc_os_s_3!="")
		){
			 if($txt_vis_pc_os_s_3!="" and $sel_vis_pc_os_sel_1_3!=""){
			 	$pc3gp1_os[]="S"; 
			 	$pc3gp1_os[]="<b>$txt_vis_pc_os_s_3</b>";
			 } 
		}
		if(
			($sel_vis_pc_os_sel_1!="" || $txt_vis_pc_os_c!="") ||
			($sel_vis_pc_os_sel_1_2!="" || $txt_vis_pc_os_c_2!="") ||
			($sel_vis_pc_os_sel_1_3!="" || $txt_vis_pc_os_c_3!="")
		){
			 if($txt_vis_pc_os_c_3!="" and $sel_vis_pc_os_sel_1_3!=""){
				 $pc3gp1_os[]="C"; 
				 $pc3gp1_os[]="<b>$txt_vis_pc_os_c_3</b>";
			 }
		}
		if(
			($sel_vis_pc_os_sel_1!="" || $txt_vis_pc_os_a!="") ||
			($sel_vis_pc_os_sel_1_2!="" || $txt_vis_pc_os_a_2!="") ||
			($sel_vis_pc_os_sel_1_3!="" || $txt_vis_pc_os_a_3!="")
		){
			 if($txt_vis_pc_os_a_3!="" and $sel_vis_pc_os_sel_1_3!=""){
			 	$pc3gp1_os[]="A";
				$pc3gp1_os[]="<b>".$txt_vis_pc_os_a_3."&#176;</b>";
			}
		}
?>
<td style="width:200px;" class="bdrbtm"><?php osLable(); print implode("&nbsp;",$pc3gp1_os);?></td>
<?php
	if(
		($txt_vis_pc_os_add_3!="" and $txt_vis_pc_os_s_3!="" and $txt_vis_pc_os_c_3!="" and $txt_vis_pc_os_a_3!="" and $txt_vis_pc_os_add_3!="")
	){
		 if(strlen($txt_vis_pc_os_add_3)<>1){
			$pc3gp2_addos[]="Add";
			$pc3gp2_addos[]="<b>$txt_vis_pc_os_add_3</b>"; 
		}
	}
?>
<td style="width:200px;" class="bdrbtm"><?php  print implode("&nbsp;",$pc3gp2_addos);?></td>
<?php
		if(($elem_prismPc1 == "1") || ($elem_prismPc2 == "1") || ($elem_prismPc3 == "1")){
			if(($sel_vis_pc_od_p!="") || ($sel_vis_pc_os_p!="") ||
			($sel_vis_pc_od_p_2!="") || ($sel_vis_pc_os_p_2!="") ||
			($sel_vis_pc_od_p_3!="") || ($sel_vis_pc_os_p_3!="")
			){
				$pc3gp2_prismos[]=(!empty($sel_vis_pc_os_p_3)) ? "P" : "";
				
				$pc3gp2_prismos[]="<b>$sel_vis_pc_os_p_3</b>";
				
			}
			if((($sel_vis_pc_od_prism!="" && $sel_vis_pc_od_p != "") || ($sel_vis_pc_os_prism!="" && $sel_vis_pc_os_p != "")) ||
			(($sel_vis_pc_od_prism_2!=""  && $sel_vis_pc_od_p_2 != "" ) || ($sel_vis_pc_os_prism_2!=""  && $sel_vis_pc_os_p_2 != "")) ||
			(($sel_vis_pc_od_prism_3!="" && $sel_vis_pc_od_p_3 != "") || ($sel_vis_pc_os_prism_3!=""  && $sel_vis_pc_os_p_3 != ""))
			){
				$pc3gp2_prismos[]= (!empty($sel_vis_pc_os_prism_3) && !empty($sel_vis_pc_os_slash_3)) ? "<img src='../images/pic_vision_pc.jpg'  align='top'/>" : "";
				 if(!empty($sel_vis_pc_os_prism_3) && !empty($sel_vis_pc_os_slash_3)){$pc3gp2_prismos[]="<b>$sel_vis_pc_os_prism_3</b>";}
				
			}
			if(($sel_vis_pc_od_slash!="") || ($sel_vis_pc_os_slash!="") ||
			($sel_vis_pc_od_slash_2!="") || ($sel_vis_pc_os_slash_2!="") ||
			($sel_vis_pc_od_slash_3!="") || ($sel_vis_pc_os_slash_3!="")
			){
				$pc3gp2_prismos[]=(!empty($sel_vis_pc_os_slash_3)) ?"<img src='../images/pic_vision_pc.jpg'  align='top'/>" : "";
				$pc3gp2_prismos[]="<b>$sel_vis_pc_os_slash_3</b>";
				
			}
			if(($sel_vis_pc_od_sel_2!="" && $sel_vis_pc_od_slash != "") || ($sel_vis_pc_os_sel_2!="" && $sel_vis_pc_os_slash != "") ||
			($sel_vis_pc_od_sel_2_2!="" && $sel_vis_pc_od_slash_2 != "") || ($sel_vis_pc_os_sel_2_2!="" && $sel_vis_pc_os_slash_2 != "") ||
			($sel_vis_pc_od_sel_2_3!="" && $sel_vis_pc_od_slash_3 != "" ) || ($sel_vis_pc_os_sel_2_3!="" && $sel_vis_pc_os_slash_3 != "")
			){
				$pc3gp2_prismos[]="<b>$sel_vis_pc_os_sel_2_3</b>";				
			}
		}
		
?>
		 <td style="width:200px;" class="bdrbtm"><?php  print implode("&nbsp;",$pc3gp2_prismos);?></td> 

		<!-- Prism -->
<?php 

if($sel_vis_pc_od_sel_1_3!="" &&($txt_vis_pc_od_s_3!=""||$txt_vis_pc_od_c_3!=""||$txt_vis_pc_od_a_3!="" || $txt_vis_pc_od_add_3!="")){

	$overrefraction3os[]="";
	if($chk_pc_near_3 <> "Over Refraction"){		
		 if($txt_vis_pc_os_near_txt_3!=""){
		 	//$overrefraction3os[]="OS";
			//$overrefraction3os[]="<b>$txt_vis_pc_os_near_txt_3</b>";
		}
	}else{
		$haveValue=0;
		 if($txt_vis_pc_os_overref_s_3!="" || $txt_vis_pc_os_overref_c_3!="" || $txt_vis_pc_os_overref_a_3!="" || $txt_vis_pc_os_overref_v_3!=""){
		 	/*$overrefraction3os[]="OS";*/
			$haveValue=1;
		 }
		 if($txt_vis_pc_os_overref_s_3!=""){
		 	$overrefraction3os[]="S"; 
			$overrefraction3os[]="<b>$txt_vis_pc_os_overref_s_3</b>";
		 }
		 if($txt_vis_pc_os_overref_c_3!=""){
			$overrefraction3os[]="C"; 
			$overrefraction3os[]="<b>$txt_vis_pc_os_overref_c_3</b>";
		 }
		 if($txt_vis_pc_os_overref_a_3!=""){
			$overrefraction3os[]="A";
			$overrefraction3os[]="<b>$txt_vis_pc_os_overref_a_3</b>";
		 }
		 if($txt_vis_pc_os_overref_v_3!="" && $txt_vis_pc_os_overref_v_3!="20/" ){
			$overrefraction3os[]="V"; 
			$overrefraction3os[]="<b>$txt_vis_pc_os_overref_v_3</b>";
		 }
		
		//if($txt_vis_pc_os_overref_v_3!="" && $txt_vis_pc_os_overref_v_3!="20/" ){  $overrefraction3os[]="<b>$txt_vis_pc_os_overref_v_3</b>";}
		
	}
	?>

	<td style="width:140px;" class="bdrbtm"><?php if($haveValue==1){osLable(); print implode("&nbsp;",$overrefraction3os);}?></td>
<?php } ?>
  </tr>
									
</table>	
<!-- End 3rd PC -->		
<?php 
}
?>
<!-- End 3rd Pc overrefraction-->
<?php
//End Pc Code
//MR Code//
$show_mrval=false;
$mrdateCheckpass=false;
if($date_of_service!=""){
	$start =FormatDate_insert($date_of_service);
	$end =$txt_vis_mr_examdate;
	$mrdateCheckpass=true;
	$mrdateCheckpass=true;		
}

/* End Code To Compare MrExamDate WITH DOS*/ 
$show_mrval=true;
if($show_mrval==true &&($txt_vis_mr_od_s!=""||$txt_vis_mr_od_c!="" ||$txt_vis_mr_od_a!="" || $txt_vis_mr_od_add!="")){
	?>
	
	<!-- <table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td>MR (Manifest Refraction).</td>
		</tr>

	</table> -->
<?php 
		/*
		$sqlVisionQry = imw_query("SELECT * FROM chart_vision WHERE patient_id = '$patient_id' AND form_id = '$form_id'");
				if(imw_num_rows($sqlVisionQry)>0){
					$sqlVisionRow = imw_fetch_assoc($sqlVisionQry);
					extract($sqlVisionRow);
		}
		*/
		
		//TEST--
		$id_chart_vis_master=0;
		$sql = "SELECT *, c5.ex_desc as ex_desc_pam, c2.ex_desc as ex_desc_ak, c1.id AS id_chart_vis_master  
				FROM chart_vis_master c1
				LEFT JOIN chart_ak c2 ON c2.id_chart_vis_master = c1.id
				LEFT JOIN chart_exo c3 ON c3.id_chart_vis_master = c1.id
				LEFT JOIN chart_bat c4 ON c4.id_chart_vis_master = c1.id
				LEFT JOIN chart_pam c5 ON c5.id_chart_vis_master = c1.id
				WHERE c1.patient_id = '".$patient_id."' AND c1.form_id = '".$form_id."' ";
		$row = sqlQuery($sql);
		if($row!=false){
			$statusElements = $row["status_elements"];
			$id_chart_vis_master = $row["id_chart_vis_master"];
			
			//PAM --			
			$visPam = (strpos($statusElements, "elem_visPam=1") !== false) ? $row["pam"] : "";
			$vis_pam_od_txt_1 = (strpos($statusElements, "elem_visPamOdTxt1=1") !== false) ? $row["txt1_od"] : "";
			$vis_pam_os_txt_1 = (strpos($statusElements, "elem_visPamOsTxt1=1") !== false) ? $row["txt1_os"] : "";
			$vis_pam_ou_txt_1 = (strpos($statusElements, "elem_visPamOuTxt1=1") !== false) ? $row["txt1_ou"] : "";
			$vis_pam_od_sel_2 = (strpos($statusElements, "elem_visPamOdSel2=1") !== false) ? $row["sel2"] : "";
			$vis_pam_od_txt_2 = (strpos($statusElements, "elem_visPamOdTxt2=1") !== false) ? $row["txt2_od"] : "";
			$vis_pam_os_txt_2 = (strpos($statusElements, "elem_visPamOsTxt2=1") !== false) ? $row["txt2_os"] : "";
			$vis_pam_ou_txt_2 = (strpos($statusElements, "elem_visPamOuTxt2=1") !== false) ? $row["txt2_ou"] : "";
			$vis_pam_desc = (strpos($statusElements, "elem_pamDesc=1") !== false) ? $row["ex_desc_pam"] : "";
			//PAM --
			
			//BAT --
			$txt_vis_bat_nl_od = (strpos($statusElements, "elem_visBatNlOd=1") !== false)?$row["nl_od"]:"";
			$txt_vis_bat_low_od =(strpos($statusElements, "elem_visBatLowOd=1") !== false)? $row["l_od"]:"";
			$txt_vis_bat_med_od = (strpos($statusElements, "elem_visBatMedOd=1") !== false)? $row["m_od"]:"";
			$txt_vis_bat_high_od =(strpos($statusElements, "elem_visBatHighOd=1") !== false)?$row["h_od"]:"";
			$txt_vis_bat_nl_os = (strpos($statusElements, "elem_visBatNlOs=1") !== false)?$row["nl_os"]:"";
			$txt_vis_bat_low_os = (strpos($statusElements, "elem_visBatLowOs=1") !== false)?$row["l_os"]:"";
			$txt_vis_bat_med_os =(strpos($statusElements, "elem_visBatMedOs=1") !== false)?$row["m_os"]:"";
			$txt_vis_bat_high_os =(strpos($statusElements, "elem_visBatHighOs=1") !== false)?$row["h_os"]:"";
			
			$txt_vis_bat_nl_ou = (strpos($statusElements, "elem_visBatNlOu=1") !== false)?$row["nl_ou"]:"";
			$txt_vis_bat_low_ou = (strpos($statusElements, "elem_visBatLowOu=1") !== false)?$row["l_ou"]:"";
			$txt_vis_bat_med_ou =(strpos($statusElements, "elem_visBatMedOu=1") !== false)?$row["m_ou"]:"";
			$txt_vis_bat_high_ou =(strpos($statusElements, "elem_visBatHighOu=1") !== false)?$row["h_ou"]:"";
			$txt_vis_bat_desc =(strpos($statusElements, "elem_visBatDesc=1") !== false)?$row["vis_bat_desc"]:""; //removeExamDateStr($row[0]["vis_bat_desc"]);
			$txt_vis_bat_examdate = $row["examDateDistance"];//getExamDateStr($row[0]["vis_bat_desc"]);
			//BAT --
			
			//AK --
			$txt_vis_ak_od_k = (strpos($statusElements, "elem_visAkOdK=1") !== false)?$row["k_od"]:"";
			$txt_vis_ak_od_slash = (strpos($statusElements, "elem_visAkOdSlash=1") !== false)?$row["slash_od"]:"";
			$txt_vis_ak_od_x = (strpos($statusElements, "elem_visAkOdX=1") !== false)?$row["x_od"]:"";			
			$txt_vis_ak_os_k = (strpos($statusElements, "elem_visAkOsK=1") !== false)?$row["k_os"]:"";
			$txt_vis_ak_os_slash =(strpos($statusElements, "elem_visAkOsSlash=1") !== false)? $row["slash_os"]:"";
			$txt_vis_ak_os_x = (strpos($statusElements, "elem_visAkOsX=1") !== false)?$row["x_os"]:"";
			$txt_vis_ar_ak_desc =$row["ex_desc_ak"];//removeExamDateStr($row[0]["vis_ar_ak_desc"]) ;
			
			//Comments --
			//Check For old comments
			if(strpos($txt_vis_ar_ak_desc,"<~ED~>")!== false){
				$commentsArrayTmp=explode("<~ED~>",$txt_vis_ar_ak_desc);
				$txt_vis_ar_ak_desc=$commentsArrayTmp[0];
			}
			//Comments --
			//AK --		
		}

		//Acuity
		$sql = "SELECT * FROM chart_vis_master c1
				LEFT JOIN chart_acuity c2 ON c2.id_chart_vis_master = c1.id
				WHERE c1.patient_id = '".$patient_id."' AND c1.form_id = '".$form_id."' 
				ORDER BY sec_indx
				";
		$res = sqlStatement($sql);
		for($i=1; $row=sqlFetchArray($res); $i++){

			$sec_name = $row["sec_name"];
			$sec_indx = $row["sec_indx"];
			if($sec_name == "Distance"){
				${"sel_vis_dis_od_sel_".$sec_indx} =(strpos($statusElements, "elem_visDisOdTxt".$sec_indx."=1") !== false)?$row["sel_od"]:"";
				${"txt_vis_dis_od_txt_".$sec_indx} =(strpos($statusElements, "elem_visDisOdTxt".$sec_indx."=1") !== false)?$row["txt_od"]:"";
				${"sel_vis_dis_os_sel_".$sec_indx} =(strpos($statusElements, "elem_visDisOsTxt".$sec_indx."=1") !== false)? $row["sel_os"]:"";
				${"txt_vis_dis_os_txt_".$sec_indx} =(strpos($statusElements, "elem_visDisOsTxt".$sec_indx."=1") !== false)? $row["txt_os"]:"";
				${"sel_vis_dis_ou_sel_".$sec_indx} =(strpos($statusElements, "elem_visDisOuTxt".$sec_indx."=1") !== false)? $row["sel_ou"]:"";
				${"txt_vis_dis_ou_txt_".$sec_indx} =(strpos($statusElements, "elem_visDisOuTxt".$sec_indx."=1") !== false)? $row["txt_ou"]:"";
				$txt_vis_dis_near_desc = (strpos($statusElements, "elem_visNearOdTxt".$sec_indx."=1") !== false || strpos($statusElements, "elem_visNearOsTxt".$sec_indx."=1") !== false)?$row["ex_desc"]:"";
				
				//Comments --
				//Check For old comments
				if(strpos($txt_vis_dis_near_desc,"<~ED~>")!== false){
					$commentsArrayTmp=explode("<~ED~>",$txt_vis_dis_near_desc);
					$txt_vis_dis_near_desc=$commentsArrayTmp[0];
				}
				//Comments --
				
			}else if($sec_name == "Near"){
				${"sel_vis_near_od_sel_".$sec_indx} =(strpos($statusElements, "elem_visNearOdTxt".$sec_indx."=1") !== false)? $row["sel_od"]:"";
				${"txt_vis_near_od_txt_".$sec_indx} = (strpos($statusElements, "elem_visNearOdTxt".$sec_indx."=1") !== false)?$row["txt_od"]:"";				
				${"sel_vis_near_os_sel_".$sec_indx} = (strpos($statusElements, "elem_visNearOsTxt".$sec_indx."=1") !== false)?$row["sel_os"]:"";
				${"txt_vis_near_os_txt_".$sec_indx} = (strpos($statusElements, "elem_visNearOsTxt".$sec_indx."=1") !== false)?$row["txt_os"]:"";
				${"sel_vis_near_ou_sel_".$sec_indx} =(strpos($statusElements, "elem_visNearOuTxt".$sec_indx."=1") !== false)? $row["sel_ou"]:"";
				${"txt_vis_near_ou_txt_".$sec_indx} =(strpos($statusElements, "elem_visNearOuTxt".$sec_indx."=1") !== false)? $row["txt_ou"]:"";
				$txt_vis_near_desc =(strpos($statusElements, "elem_visNearDesc=1") !== false)?$row["ex_desc"]:"";// removeExamDateStr($row["vis_near_desc"]);
				$txt_vis_near_examdate = $row["examDateDistance "];
			
			}else if($sec_name == "Ad. Acuity"){
				$vis_dis_od_sel_3=(strpos($statusElements, "elem_visDisOdSel3=1") !== false) ? $row["sel_od"]: "";
				$vis_dis_od_txt_3=(strpos($statusElements, "elem_visDisOdTxt3=1") !== false) ? $row["txt_od"]: "";
				$vis_dis_os_sel_3=(strpos($statusElements, "elem_visDisOsSel3=1") !== false) ? $row["sel_os"]: "";
				$vis_dis_os_txt_3=(strpos($statusElements, "elem_visDisOsTxt3=1") !== false) ? $row["txt_os"]: "";
				$vis_dis_ou_sel_3=(strpos($statusElements, "elem_visDisOuSel3=1") !== false) ? $row["sel_ou"]: "";
				$vis_dis_ou_txt_3=(strpos($statusElements, "elem_visDisOuTxt3=1") !== false) ? $row["txt_ou"]: ""; 
				$vis_dis_act_3  =(strpos($statusElements, "elem_visDisAct3=1") !== false) ? htmlentities($row["ex_desc"]): "";
			}
		}

		if(!empty($id_chart_vis_master)){
			//sca
			$sql = "SELECT * FROM chart_sca WHERE id_chart_vis_master='".$id_chart_vis_master."' ";
			$res = sqlStatement($sql);
			for($i=1;$row=sqlFetchArray($res);$i++){
				
				$sec_name = $row["sec_name"];
				if($sec_name == "AR"){
				$txt_vis_ar_od_s =(strpos($statusElements, "elem_visArOdS=1") !== false)? $row["s_od"]:"";
				$txt_vis_ar_od_c =(strpos($statusElements, "elem_visArOdC=1") !== false)? $row["c_od"]:"";
				$txt_vis_ar_od_a = (strpos($statusElements, "elem_visArOdA=1") !== false)?$row["a_od"]:"";
				$sel_vis_ar_od_sel_1 =(strpos($statusElements, "elem_visArOdS=1") !== false)? $row["sel_od"]:"";
				$txt_vis_ar_os_s =(strpos($statusElements, "elem_visArOsS=1") !== false)? $row["s_os"]:"";
				$txt_vis_ar_os_c = (strpos($statusElements, "elem_visArOsC=1") !== false)?$row["c_os"]:"";
				$txt_vis_ar_os_a = (strpos($statusElements, "elem_visArOsA=1") !== false)?$row["a_os"]:"";
				$sel_vis_ar_os_sel_1 = (strpos($statusElements, "elem_visArOsA=1") !== false)?$row["sel_os"]:"";
				}else if($sec_name == "ARC"){
				$visCycArOdS =(strpos($statusElements, "elem_visArOdS=1") !== false)? $row["s_od"]:"";
				$visCycArOdC =(strpos($statusElements, "elem_visArOdC=1") !== false)? $row["c_od"]:"";
				$visCycArOdA = (strpos($statusElements, "elem_visArOdA=1") !== false)?$row["a_od"]:"";
				$visCycArOdSel1 =(strpos($statusElements, "elem_visArOdS=1") !== false)? $row["sel_od"]:"";
				$visCycArOsS =(strpos($statusElements, "elem_visArOsS=1") !== false)? $row["s_os"]:"";
				$visCycArOsC = (strpos($statusElements, "elem_visArOsC=1") !== false)?$row["c_os"]:"";
				$visCycArOsA = (strpos($statusElements, "elem_visArOsA=1") !== false)?$row["a_os"]:"";
				$visCycArOsSel1 = (strpos($statusElements, "elem_visArOsA=1") !== false)?$row["sel_os"]:"";	
					
				}
			
			}
			
			//PC/MR
			$sql = "SELECT 
					c1.*,
					c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, c2.prsm_p as prsm_p_r, c2.prism as prism_r, c2.slash as slash_r, c2.sel_1 as sel_1_r, c2.sel_2 as sel_2_r, 
					c2.txt_1 as txt_1_r, c2.txt_2 as txt_2_r, c2.sel2v as sel2v_r,
					c2.ovr_s as ovr_s_r, c2.ovr_c as ovr_c_r, c2.ovr_v as ovr_v_r, c2.ovr_a as ovr_a_r,			
					c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l, c3.prsm_p as prsm_p_l, c3.prism as prism_l, c3.slash as slash_l, c3.sel_1 as sel_1_l, c3.sel_2 as sel_2_l, 
					c3.txt_1 as txt_1_l, c3.txt_2 as txt_2_l, c3.sel2v as sel2v_l,
					c3.ovr_s as ovr_s_l, c3.ovr_c as ovr_c_l, c3.ovr_v as ovr_v_l, c3.ovr_a as ovr_a_l,
					c4.status_elements as vis_statusElements
					FROM chart_vis_master c4
					LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c4.id
					LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
					LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'				
					WHERE c4.form_id='".$form_id."' AND c4.patient_id = '".$patient_id."' AND c1.ex_type='PC' AND c1.delete_by='0'  
					Order By ex_number;
					";
			$rez = sqlStatement($sql);
			for($i=0; $row= sqlFetchArray($rez); $i++){
				$ex_num = $row["ex_number"];
				
				if($ex_num == "1"){ 
					$ex_num = ""; 
					$indx1 = "";
				}else{
					$indx1 = "_".$ex_num;
				}
				
				//Pc---
				$statusElements = $row["vis_statusElements"];
				${"chk_pc_near".$indx1} = $row["pc_near"];

				${"sel_vis_pc_od_sel_1".$indx1} = (strpos($statusElements, "elem_visPcOdSel1".$ex_num."=1") !== false)?$row["sel_1_r"]:"";
				${"txt_vis_pc_od_s".$indx1} = (strpos($statusElements, "elem_visPcOdS".$ex_num."=1") !== false)?$row["sph_r"]:"";
				${"txt_vis_pc_od_c".$indx1} = (strpos($statusElements, "elem_visPcOdC".$ex_num."=1") !== false)?$row["cyl_r"]:"";
				${"txt_vis_pc_od_a".$indx1} =(strpos($statusElements, "elem_visPcOdA".$ex_num."=1") !== false)?$row["axs_r"]:"";

				${"sel_vis_pc_od_p".$indx1} =(strpos($statusElements, "elem_visPcOsP".$ex_num."=1") !== false)? $row["prsm_p_r"]:"";
				${"sel_vis_pc_od_prism".$indx1} =(strpos($statusElements, "elem_visPcOdPrism".$ex_num."=1") !== false)? $row["prism_r"]:"";
				${"sel_vis_pc_od_slash".$indx1} = (strpos($statusElements, "elem_visPcOdSlash".$ex_num."=1") !== false)?$row["slash_r"]:"";
				${"sel_vis_pc_od_sel_2".$indx1} = (strpos($statusElements, "elem_visPcOdSel2".$ex_num."=1") !== false)?$row["sel_2_r"]:"";

				${"sel_vis_pc_os_sel_1".$indx1} = (strpos($statusElements, "elem_visPcOsSel1".$ex_num."=1") !== false)?$row["sel_1_l"]:"";
				${"txt_vis_pc_os_s".$indx1} = (strpos($statusElements, "elem_visPcOsS".$ex_num."=1") !== false)?$row["sph_l"]:"";
				${"txt_vis_pc_os_c".$indx1} =(strpos($statusElements, "elem_visPcOsC".$ex_num."=1") !== false)? $row["cyl_l"]:"";
				${"txt_vis_pc_os_a".$indx1} = (strpos($statusElements, "elem_visPcOsA".$ex_num."=1") !== false)?$row["axs_l"]:"";
				${"sel_vis_pc_os_p".$indx1} = (strpos($statusElements, "elem_visPcOsP".$ex_num."=1") !== false)?$row["prsm_p_l"]:"";
				${"sel_vis_pc_os_prism".$indx1} = (strpos($statusElements, "elem_visPcOsPrism".$ex_num."=1") !== false)?$row["prism_l"]:"";
				${"sel_vis_pc_os_slash".$indx1} = (strpos($statusElements, "elem_visPcOsSlash".$ex_num."=1") !== false)?$row["slash_l"]:"";
				${"sel_vis_pc_os_sel_2".$indx1} =(strpos($statusElements, "elem_visPcOsSel2".$ex_num."=1") !== false)? $row["sel_2_l"]:"";
				
				${"txt_vis_pc_od_near_txt".$indx1} = $row["txt_1_r"];
				${"txt_vis_pc_os_near_txt".$indx1} = $row["txt_1_l"];											

				${"txt_vis_pc_od_overref_s".$indx1} = (strpos($statusElements, "elem_visPcOdOverrefS".$ex_num."=1") !== false)?$row["ovr_s_r"]:"";
				${"txt_vis_pc_od_overref_c".$indx1} = (strpos($statusElements, "elem_visPcOdOverrefC".$ex_num."=1") !== false)?$row["ovr_c_r"]:"";
				${"txt_vis_pc_od_overref_a".$indx1} = (strpos($statusElements, "elem_visPcOdOverrefA".$ex_num."=1") !== false)?$row["ovr_a_r"]:"";
				${"txt_vis_pc_od_overref_v".$indx1} = (strpos($statusElements, "elem_visPcOdOverrefV".$ex_num."=1") !== false)?$row["ovr_v_r"]:"";
				${"txt_vis_pc_os_overref_s".$indx1} = (strpos($statusElements, "elem_visPcOsOverrefS".$ex_num."=1") !== false)?$row["ovr_s_l"]:"";
				${"txt_vis_pc_os_overref_c".$indx1} = (strpos($statusElements, "elem_visPcOsOverrefC".$ex_num."=1") !== false)?$row["ovr_c_l"]:"";
				${"txt_vis_pc_os_overref_a".$indx1} = (strpos($statusElements, "elem_visPcOsOverrefA".$ex_num."=1") !== false)?$row["ovr_a_l"]:"";
				${"txt_vis_pc_os_overref_v".$indx1} = (strpos($statusElements, "elem_visPcOsOverrefV".$ex_num."=1") !== false)?$row["ovr_v_l"]:"";
				
				${"txt_vis_pc_desc".$ex_num}=$row["ex_desc"];//
				${"txt_vis_pc_od_add".$indx1} = (strpos($statusElements, "elem_visPcOdAdd".$ex_num."=1") !== false)?$row["ad_r"]:"";
				${"txt_vis_pc_os_add".$indx1} =(strpos($statusElements, "elem_visPcOsAdd".$ex_num."=1") !== false)? $row["ad_l"]:"";		
			}
			
			$sql = "SELECT 
				c1.*,
				c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, c2.prsm_p as prsm_p_r, c2.prism as prism_r, c2.slash as slash_r, c2.sel_1 as sel_1_r, c2.sel_2 as sel_2_r, 
				c2.txt_1 as txt_1_r, c2.txt_2 as txt_2_r, c2.sel2v as sel2v_r,
							
				c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l, c3.prsm_p as prsm_p_l, c3.prism as prism_l, c3.slash as slash_l, c3.sel_1 as sel_1_l, c3.sel_2 as sel_2_l, 
				c3.txt_1 as txt_1_l, c3.txt_2 as txt_2_l, c3.sel2v as sel2v_l,
				
				c4.status_elements 
				FROM chart_vis_master c4  
				LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c4.id
				LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
				LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'				
				WHERE c4.form_id='".$form_id."' AND c4.patient_id = '".$patient_id."' AND c1.ex_type='MR' AND c1.ex_number IN (1,2) AND c1.delete_by='0'  
				Order By ex_number;
				";
				
			$rez = sqlStatement($sql);		
			for($i=0; $row= sqlFetchArray($rez); $i++){
				$ret_str="";
				$ex_num = $row["ex_number"];
				
				$indx1=$indx2=$indx3="";
				if($ex_num>1){
					$indx1="Other";
					$indx3="_given";
					if($ex_num>2){
						$indx2="_".$ex_num;	
					}
				}
				
				$statusElements = $row["status_elements"];
				$rd_vis_mr_none_given=(strpos($statusElements, "elem_mrNoneGiven".$ex_num."=1")!== false)?$row["mr_none_given"] : "" ;
				$providerIdOther_3=$row["provider_id"];
				$vis_mr_desc_3COMMENTS=$row["ex_desc"];
				$vis_mr_prism3COMMENTS=$row["prism_desc"];				
				
				${($ex_num==3) ? "visMrOtherOdS_3" : "vis_mr_od".$indx3."_s"}=(strpos($statusElements, "elem_visMr".$indx1."OdS".$indx2."=1") !== false)?$row["sph_r"] : "" ;
				${($ex_num==3) ? "visMrOtherOdC_3" : "vis_mr_od".$indx3."_c"}=(strpos($statusElements, "elem_visMr".$indx1."OdC".$indx2."=1") !== false)?$row["cyl_r"] : "" ;
				${($ex_num==3) ? "visMrOtherOdA_3" : "vis_mr_od".$indx3."_a"}=(strpos($statusElements, "elem_visMr".$indx1."OdA".$indx2."=1") !== false)?$row["axs_r"] : "" ;
				${($ex_num==3) ? "visMrOtherOdAdd_3" : "vis_mr_od".$indx3."_add"}=(strpos($statusElements, "elem_visMr".$indx1."OdAdd".$indx2."=1") !== false)?$row["ad_r"] : "" ;
				${($ex_num==3) ? "visMrOtherOdTxt1_3" : "vis_mr_od".$indx3."_txt_1"}=(strpos($statusElements, "elem_visMr".$indx1."OdTxt1".$indx2."=1") !== false)?$row["txt_1_r"] : "" ;			
				${($ex_num==3) ? "visMrOtherOdTxt2_3" : "vis_mr_od".$indx3."_txt_2"}=(strpos($statusElements, "elem_visMr".$indx1."OdTxt2".$indx2."=1") !== false)?$row["txt_2_r"] : "" ;
				
				
				
				${($ex_num==3) ? "visMrOtherOdP_3" : "vis_mr_od".$indx3."_p"}=((strpos($statusElements, "elem_visMr".$indx1."OdP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdSel1".$indx2."=1") !== false)
								|| (strpos($statusElements, "elem_visMr".$indx1."OdSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdPrism".$indx2."=1") !== false)
								|| (strpos($statusElements, "elem_visMr".$indx1."OdTxt1".$indx2."=1") !== false)
								)?$row["prsm_p_r"] : "" ;
				${($ex_num==3) ? "visMrOtherOdPrism_3" : "vis_mr_od".$indx3."_prism"}=((strpos($statusElements, "elem_visMr".$indx1."OdP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdSel1".$indx2."=1") !== false)
									|| (strpos($statusElements, "elem_visMr".$indx1."OdSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdPrism".$indx2."=1") !== false)
									)?$row["prism_r"] : "" ;
				${($ex_num==3) ? "visMrOtherOdSlash_3" : "vis_mr_od".$indx3."_slash"}=((strpos($statusElements, "elem_visMr".$indx1."OdP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdSel1".$indx2."=1") !== false)
									|| (strpos($statusElements, "elem_visMr".$indx1."OdSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdPrism".$indx2."=1") !== false)
									)?$row["slash_r"] : "" ;
				${($ex_num==3) ? "visMrOtherOdSel1_3" : "vis_mr_od".$indx3."_sel_1"}=((strpos($statusElements, "elem_visMr".$indx1."OdP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdSel1".$indx2."=1") !== false)
									|| (strpos($statusElements, "elem_visMr".$indx1."OdSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OdPrism".$indx2."=1") !== false)
									)?$row["sel_1_r"] : "" ;
				${($ex_num==3) ? "visMrOtherOdSel2_3" : "vis_mr_od".$indx3."_sel_2"}=(strpos($statusElements, "elem_visMr".$indx1."OdSel2".$indx2."=1") !== false)?$row["sel_2_r"] : "" ;
				//$visMrOtherOdSel2Vision_3=(strpos($statusElements, "elem_visMr".$indx1."OdSel2Vision".$indx2."=1") !== false)?$row["sel2v_r"] : "" ;
				
				${($ex_num==3) ? "visMrOtherOsS_3" : "vis_mr_os".$indx3."_s"}=(strpos($statusElements, "elem_visMr".$indx1."OsS".$indx2."=1") !== false)?$row["sph_l"] : "" ;
				${($ex_num==3) ? "visMrOtherOsC_3" : "vis_mr_os".$indx3."_c"}=(strpos($statusElements, "elem_visMr".$indx1."OsC".$indx2."=1") !== false)?$row["cyl_l"] : "" ;
				${($ex_num==3) ? "visMrOtherOsA_3" : "vis_mr_os".$indx3."_a"}=(strpos($statusElements, "elem_visMr".$indx1."OsA".$indx2."=1") !== false)?$row["axs_l"] : "" ;
				
				${($ex_num==3) ? "visMrOtherOsAdd_3" : "vis_mr_os".$indx3."_add"}=(strpos($statusElements, "elem_visMr".$indx1."OsAdd".$indx2."=1") !== false)?$row["ad_l"] : "" ;
				${($ex_num==3) ? "visMrOtherOsTxt1_3" : "vis_mr_os".$indx3."_txt_1"}=(strpos($statusElements, "elem_visMr".$indx1."OsTxt1".$indx2."=1") !== false)?$row["txt_1_l"] : "" ;
				${($ex_num==3) ? "visMrOtherOsTxt2_3" : "vis_mr_os".$indx3."_txt_2"}=(strpos($statusElements, "elem_visMr".$indx1."OsTxt2".$indx2."=1") !== false)?$row["txt_2_l"] : "" ;			
				
				${($ex_num==3) ? "visMrOtherOsP_3" : "vis_mr_os".$indx3."_p"}=((strpos($statusElements, "elem_visMr".$indx1."OsP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsSel1".$indx2."=1") !== false)
									|| (strpos($statusElements, "elem_visMr".$indx1."OsSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsPrism".$indx2."=1") !== false)
								)?$row["prsm_p_l"] : "" ;
				${($ex_num==3) ? "visMrOtherOsPrism_3" : "vis_mr_os".$indx3."_prism"}=((strpos($statusElements, "elem_visMr".$indx1."OsP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsSel1".$indx2."=1") !== false)
									|| (strpos($statusElements, "elem_visMr".$indx1."OsSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsPrism".$indx2."=1") !== false)
								)?$row["prism_l"] : "" ;
				${($ex_num==3) ? "visMrOtherOsSlash_3" : "vis_mr_os".$indx3."_slash"}=((strpos($statusElements, "elem_visMr".$indx1."OsP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsSel1".$indx2."=1") !== false)
									|| (strpos($statusElements, "elem_visMr".$indx1."OsSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsPrism".$indx2."=1") !== false)
								)?$row["slash_l"] : "" ;
				${($ex_num==3) ? "visMrOtherOsSel1_3" : "vis_mr_os".$indx3."_sel_1"}=((strpos($statusElements, "elem_visMr".$indx1."OsP".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsSel1".$indx2."=1") !== false)
									|| (strpos($statusElements, "elem_visMr".$indx1."OsSlash".$indx2."=1") !== false) || (strpos($statusElements, "elem_visMr".$indx1."OsPrism".$indx2."=1") !== false)
									)?$row["sel_1_l"] : "" ;
				${($ex_num==3) ? "visMrOtherOsSel2_3" : "vis_mr_os".$indx3."_sel_2"}=(strpos($statusElements, "elem_visMr".$indx1."OsSel2".$indx2."=1") !== false)?$row["sel_2_l"] : "" ;
				//$visMrOtherOsSel2Vision_3=(strpos($statusElements, "elem_visMr".$indx1."OsSel2Vision".$indx2."=1") !== false)?$row["sel2v_l"] : "" ;		
				//$elem_mr_type3 = (strpos($statusElements, "elem_mr_type".$ex_num."=1") !== false && !empty($row["vis_mr_type1"])) ?$row["mr_type"] : "" ;
				//if(!empty($elem_mr_type3)){ $elem_mr_type3 = "(".ucfirst($elem_mr_type3).")"; } 		
			}
		}
		//Test --
		

}
?>


<table  class="border text_10" cellspacing="0" cellpadding="0" style=" width:740px;" > 
  
    <?php
			if(
				$vis_mr_od_s || $vis_mr_od_c || $vis_mr_od_a || $vis_mr_od_add || ($vis_mr_od_txt_1 && $vis_mr_od_txt_1!='20/')
				||
				($vis_mr_od_txt_2 && $vis_mr_od_txt_2!='20/')
				||
				$vis_mr_os_s || $vis_mr_os_c || $vis_mr_os_a || $vis_mr_os_add || ($vis_mr_os_txt_1 && $vis_mr_os_txt_1!='20/')
				||
				($vis_mr_os_txt_2 && $vis_mr_os_txt_2!='20/')
				){
				$Mr1st = true;
				?>
					<tr>
						<?php 
							$givenMr = ($rd_vis_mr_none_given=="MR 1") ? "(Given)" : "";
						?>
						<td style="width:740px;" class="tb_subheading" colspan="4">Mr 1st <?php echo $givenMr.showDoctorName($provider_id); ?></td> 
					</tr>
					<tr>
						<td width="31%">&nbsp;</td>
						<td width="20%">&nbsp;</td>
						<?php    }
							if(
								($prism_mr == 1) 
								&& 
								(
								($vis_mr_od_p || $vis_mr_od_prism || $vis_mr_od_slash || $vis_mr_od_sel_1)
								||
								($vis_mr_os_p || $vis_mr_os_prism || $vis_mr_os_slash || $vis_mr_os_sel_1)
								)
								){
								$Mr1stPrism = true;
								?>
						<td width="26%">Prism</td>
							<?php } ?>
							<?php
							if(($mrGLPH1 == 1)
								&&
								($vis_mr_od_sel_2 || ($visMrOdSel2Vision && $visMrOdSel2Vision!='20/'))
								||
								($vis_mr_os_sel_2 || ($visMrOsSel2Vision && $visMrOsSel2Vision!='20/'))
								){
								$Mr1stPrismGlPH = true;
							?>
						<td width="23%"><!-- GL/PH --></td>
						<?php } if($Mr1stPrismGlPH==true || $Mr1st){?>
					</tr>
					  <?php		}
								$mr1gp1_od[]="";
								$mr1gp1_os[]="";
								$mr1gp2_addod[]="";
								$mr1gp2_addos[]="";
								$mr1gp3_prismod[]="";
								$mr1gp3_prismos[]="";
								$mr1gp4_GL_PHod[]="";
								$mr1gp4_GL_PHos[]="";
					  ?>
	 
					  <?php
						if(
							$vis_mr_od_s || $vis_mr_od_c || $vis_mr_od_a || $vis_mr_od_add || ($vis_mr_od_txt_1 && $vis_mr_od_txt_1!='20/')
							||
							($vis_mr_od_txt_2 && $vis_mr_od_txt_2!='20/')
							||
							$vis_mr_os_s || $vis_mr_os_c || $vis_mr_os_a || $vis_mr_os_add || ($vis_mr_os_txt_1 && $vis_mr_os_txt_1!='20/')
							||
							($vis_mr_os_txt_2 && $vis_mr_os_txt_2!='20/')
							){
							$Mr1st = true;
				
							/*$mr1gp1_od[]="OD";*/
							if($vis_mr_od_s)
							{
							$mr1gp1_od[]="S";
							$mr1gp1_od[]="<b>$vis_mr_od_s</b>"; 
							}
							if($vis_mr_od_c)
							{ 
							$mr1gp1_od[]="C";
							$mr1gp1_od[]="<b>$vis_mr_od_c</b>"; 
							}
							if($vis_mr_od_a)
							{
							$mr1gp1_od[]="A";
							$mr1gp1_od[]="<b>$vis_mr_od_a</b>"; 
							}
							if($vis_mr_od_txt_1 && $vis_mr_od_txt_1!='20/')
							{
							$mr1gp1_od[]="<b>$vis_mr_od_txt_1</b>";
							}
					?>
					<tr>
						<td class="text_lable bdrbtm">
                   			<?php odLable(); print implode("&nbsp;",$mr1gp1_od); ?>
	  					</td>
						   <?php 
									if(($vis_mr_od_s!="" and $vis_mr_od_add!="" and $vis_mr_od_c!="" and $vis_mr_od_a!="" and $vis_mr_od_txt_1 and $vis_mr_od_txt_1!='20/'))
									{
										$mr1gp2_addod[]="Add";
										$mr1gp2_addod[]="<b>$vis_mr_od_add</b>";
									}
									if($vis_mr_od_txt_2 && $vis_mr_od_txt_2!='20/') 
									{
									$mr1gp2_addod[]="<b>$vis_mr_od_txt_2</b>";		
									}								
							?>
	  					<td class="text_lable bdrbtm">
								   <?php  print implode("&nbsp;",$mr1gp2_addod); ?>
						</td>
				   		<?php   
				      	}
						if(
							($prism_mr == 1) 
							&& 
							(
							($vis_mr_od_p || $vis_mr_od_prism || $vis_mr_od_slash || $vis_mr_od_sel_1)
							||
							($vis_mr_os_p || $vis_mr_os_prism || $vis_mr_os_slash || $vis_mr_os_sel_1)
							)
							){
							$Mr1stPrism = true;
					
				            /*$mr1gp3_prismod[]="OD";*/										
						
							if($vis_mr_od_p)
							{
							$mr1gp3_prismod[]="P";
							$mr1gp3_prismod[]="<b>$vis_mr_od_p</b>";
							}
							if($vis_mr_od_p || $vis_mr_od_prism){
								$mr1gp3_prismod[]="<img src='../images/pic_vision_pc.jpg'/>";
							}
							if($vis_mr_od_prism)
							{
							$mr1gp3_prismod[]="<b>$vis_mr_od_prism</b>";
							}
							if($vis_mr_od_slash)
							{
							 $mr1gp3_prismod[]="/";
							 $mr1gp3_prismod[]="<b>$vis_mr_od_slash</b>";
							 }
							if($vis_mr_od_sel_1)
							{
							$mr1gp3_prismod[]="<b>$vis_mr_od_sel_1</b>";
							}
					  ?>
						<td>
                    	  <?php /*odLable();*/ print implode("&nbsp;",$mr1gp3_prismod); ?>
 					  </td>
	                  <?php
							 }
							
							  if(
							  	($mrGLPH1 == 1)
							  	&&
							 	(
									($vis_mr_od_sel_2) 
									|| 
									($visMrOdSel2Vision && $visMrOdSel2Vision!='20/')
									||
							 		(
										($vis_mr_os_sel_2) 
										|| 
										($visMrOsSel2Vision && $visMrOsSel2Vision!='20/')
									)
								)
							 	
							 	){
							$Mr1stPrismGlPH = true;
								
							/*$mr1gp4_GL_PHod[]="OD";*/
												
							if($vis_mr_od_sel_2<>"")
							{
							$mr1gp4_GL_PHod[]="GL/PH";
							$mr1gp4_GL_PHod[]="<b>$vis_mr_od_sel_2</b>";
							}												
							if($visMrOdSel2Vision && $visMrOdSel2Vision!='20/')
							{
							$mr1gp4_GL_PHod[]="Vision";
							$mr1gp4_GL_PHod[]="<b>$visMrOdSel2Vision</b>";
							}
						?>
      <td class="text_lable bdrbtm">
                    <?php /* odLable(); */ print implode("&nbsp;",$mr1gp4_GL_PHod); ?>
	  </td>
	
	               <?php
						}
						?>
  <?php
			if(
				$vis_mr_od_s || $vis_mr_od_c || $vis_mr_od_a || $vis_mr_od_add || ($vis_mr_od_txt_1 && $vis_mr_od_txt_1!='20/')
				||
				($vis_mr_od_txt_2 && $vis_mr_od_txt_2!='20/')
				||
				$vis_mr_os_s || $vis_mr_os_c || $vis_mr_os_a || $vis_mr_os_add || ($vis_mr_os_txt_1 && $vis_mr_os_txt_1!='20/')
				||
				($vis_mr_os_txt_2 && $vis_mr_os_txt_2!='20/')
				){?>	
  </tr>
<?php } ?>

   <?php
			if(
				$vis_mr_od_s || $vis_mr_od_c || $vis_mr_od_a || $vis_mr_od_add || ($vis_mr_od_txt_1 && $vis_mr_od_txt_1!='20/')
				||
				($vis_mr_od_txt_2 && $vis_mr_od_txt_2!='20/')
				||
				$vis_mr_os_s || $vis_mr_os_c || $vis_mr_os_a || $vis_mr_os_add || ($vis_mr_os_txt_1 && $vis_mr_os_txt_1!='20/')
				||
				($vis_mr_os_txt_2 && $vis_mr_os_txt_2!='20/')
				){
				$Mr1st = true;?>
				<Tr><?php
				
										/*$mr1gp1_os[]="OS";*/
										if($vis_mr_os_s) 
										{
										$mr1gp1_os[]="S";
										$mr1gp1_os[]="<b>$vis_mr_os_s</b>";
										}
										if($vis_mr_os_c)
										{
										$mr1gp1_os[]="C";
										$mr1gp1_os[]="<b>$vis_mr_os_c</b>";
										}
										if($vis_mr_os_a)
										{
										$mr1gp1_os[]="A";
										$mr1gp1_os[]="<b>$vis_mr_os_a</b>";
										}
										if($vis_mr_os_txt_1 && $vis_mr_os_txt_1!='20/')
										{
										$mr1gp1_os[]="<b>$vis_mr_os_txt_1</b>";
										}
										?>
    <td class="text_lable bdrbtm">
      <?php osLable(); print implode("&nbsp;",$mr1gp1_os); ?></td>
    <?php
		if(($vis_mr_os_add!="" and $vis_mr_os_s!="" and $vis_mr_os_c!="" and $vis_mr_os_a!="" and $vis_mr_os_txt_1 and $vis_mr_os_txt_1!='20/')){
			$mr1gp2_addos[]="Add";
			$mr1gp2_addos[]="<b>$vis_mr_os_add</b>";
		}
		if($vis_mr_os_txt_2 && $vis_mr_os_txt_2!='20/'){
		 	$mr1gp2_addos[]="<b>$vis_mr_os_txt_2</b>";
		}
	?>
    <td class="text_lable bdrbtm">
      <?php  print implode("&nbsp;",$mr1gp2_addos); ?></td>
    <!-- MR 1st -->
    <?php
						}
						?>
    <!-- MR 1st Prism -->
    <?php
						if(
							($prism_mr == 1) 
							&& 
							(
							($vis_mr_od_p || $vis_mr_od_prism || $vis_mr_od_slash || $vis_mr_od_sel_1)
							||
							($vis_mr_os_p || $vis_mr_os_prism || $vis_mr_os_slash || $vis_mr_os_sel_1)
							)
							){
							$Mr1stPrism = true;
							?>


    <?php
										/*$mr1gp3_prismos[]="OS";*/
												
													if($vis_mr_os_p)
													{
													$mr1gp3_prismos[]="P";
													$mr1gp3_prismos[]="<b>$vis_mr_os_p</b>";
													}
													if($vis_mr_os_p || $vis_mr_os_prism){
														$mr1gp3_prismos[]="<img src='../images/pic_vision_pc.jpg'/>";
													}	
													if($vis_mr_os_prism)
													{
													 $mr1gp3_prismos[]="<b>$vis_mr_os_prism</b>";
													}
													if($vis_mr_os_slash)
													{
													$mr1gp3_prismos[]="/";
													$mr1gp3_prismos[]="<b>$vis_mr_os_slash</b>";
													}
													if($vis_mr_os_sel_1) 
													{
													$mr1gp3_prismos[]="<b>$vis_mr_os_sel_1</b>";
													}
													?>
    <td class="text_lable bdrbtm">
      <?php /* osLable(); */ print implode("&nbsp;",$mr1gp3_prismos); ?></td>
    <?php
						}
						?>

    <?php
						if(($mrGLPH1 == 1)
							&&
							(
								($vis_mr_od_sel_2)
								|| 
								($visMrOdSel2Vision && $visMrOdSel2Vision!='20/')								
								||
								($vis_mr_os_sel_2)
								||
								($visMrOsSel2Vision && $visMrOsSel2Vision!='20/')
							)
							){
							$Mr1stPrismGlPH = true;
							?>
 
  
    <?php
										/*$mr1gp4_GL_PHos[]="OS";*/										
																	
													if($vis_mr_os_sel_2)
													{
													$mr1gp4_GL_PHos[]="GL/PH";
													$mr1gp4_GL_PHos[]="<b>$vis_mr_os_sel_2</b>";
													}
													if($visMrOsSel2Vision && $visMrOsSel2Vision!='20/') 
													{
													$mr1gp4_GL_PHos[]="Vision";
													$mr1gp4_GL_PHos[]="<b>$visMrOsSel2Vision</b>";
													}
													?>
    <td class="text_lable bdrbtm">
      <?php  /* osLable(); */ print implode("&nbsp;",$mr1gp4_GL_PHos); ?></td>
    <?php
						}
						?>

 <?php
			if(
				$vis_mr_od_s || $vis_mr_od_c || $vis_mr_od_a || $vis_mr_od_add || ($vis_mr_od_txt_1 && $vis_mr_od_txt_1!='20/')
				||
				($vis_mr_od_txt_2 && $vis_mr_od_txt_2!='20/')
				||
				$vis_mr_os_s || $vis_mr_os_c || $vis_mr_os_a || $vis_mr_os_add || ($vis_mr_os_txt_1 && $vis_mr_os_txt_1!='20/')
				||
				($vis_mr_os_txt_2 && $vis_mr_os_txt_2!='20/')
				){
				$Mr1st = true;?>  </tr>
<?php }?>
</table>

	<?php
	if(($vis_mr_od_given_s || $vis_mr_od_given_c || $vis_mr_od_given_a || $vis_mr_od_given_add)
		||
		($vis_mr_od_given_txt_1 && $vis_mr_od_given_txt_1!='20/')
		||
		($vis_mr_od_given_txt_2 && $vis_mr_od_given_txt_2!='20/')
		||
		($vis_mr_os_given_s || $vis_mr_os_given_c || $vis_mr_os_given_a || $vis_mr_os_given_add)
		||
		($vis_mr_os_given_txt_1 && $vis_mr_os_given_txt_1!='20/')
		||
		($vis_mr_os_given_txt_2 && $vis_mr_os_given_txt_2!='20/')
		){
		$MR2nd = true;
		
			$mr2gp1_od[]="";
			$mr2gp1_os[]="";
			$mr2gp2_addod[]="";
			$mr2gp2_addos[]="";
			$mr2gp3_prismod[]="";
			$mr2gp3_prismos[]="";
			$mr2gp4_GL_PHod[]="";
			$mr2gp4_GL_PHos[]="";
  ?>					

<table class="border" cellpadding="0" cellspacing="0" style=" width:740px;">
	<tr>
		<?php 
			$givenMr = ($rd_vis_mr_none_given=="MR 2") ? "(Given)" : "";
		?>
						<td style="width:740px;" class="tb_subheading" colspan="4">Mr 2nd <?php echo $givenMr.showDoctorName($providerIdOther); ?></td>
  </tr>
					<tr>
						<td width="31%" class="text_9 bdrbtm" >&nbsp;</td>
						<td width="20%"  class="text_9 bdrbtm" >&nbsp;</td>

					<?php
					
					 if(($mrPrism2 == 1)
					&&
					($vis_mr_od_given_p || $vis_mr_od_given_prism || $vis_mr_od_given_slash || $vis_mr_od_given_sel_1)
					||
					($vis_mr_os_given_p || $vis_mr_os_given_prism || $vis_mr_os_given_slash || $vis_mr_os_given_sel_1)
					){
					$MR2ndPrism = true;
					?>
					<td width="26%"  class="text_9 bdrbtm" >Prism</td>
					<?php 
					}
						if(($mrGLPH2 == 1)
						&&
						($vis_mr_od_given_sel_2 || ($visMrOtherOdSel2Vision && $visMrOtherOdSel2Vision!='20/'))
						||
						($vis_mr_os_given_sel_2 || ($visMrOtherOsSel2Vision && $visMrOtherOsSel2Vision!='20/'))
						){
						$MR2ndPrismGLPH = true;
					?>
					<td width="23%"  class="text_9 bdrbtm" ><!-- GL/PH --></td>
					<?php
					}
					?>
					
 		 </tr>
		<tr>				
	<?php
	if(($vis_mr_od_given_s || $vis_mr_od_given_c || $vis_mr_od_given_a || $vis_mr_od_given_add)
		||
		($vis_mr_od_given_txt_1 && $vis_mr_od_given_txt_1!='20/')
		||
		($vis_mr_od_given_txt_2 && $vis_mr_od_given_txt_2!='20/')
		||
		($vis_mr_os_given_s || $vis_mr_os_given_c || $vis_mr_os_given_a || $vis_mr_os_given_add)
		||
		($vis_mr_os_given_txt_1 && $vis_mr_os_given_txt_1!='20/')
		||
		($vis_mr_os_given_txt_2 && $vis_mr_os_given_txt_2!='20/')
		){
		$MR2nd = true;
		
			?>			
			<?php
					/*$mr2gp1_od[]="OD";*/
					if($vis_mr_od_given_s)
					{
					$mr2gp1_od[]="S";
					$mr2gp1_od[]="<b>$vis_mr_od_given_s</b>";
					}
					if($vis_mr_od_given_c)
					{
					$mr2gp1_od[]="C";
					$mr2gp1_od[]="<b>$vis_mr_od_given_c</b>";
					}
					if($vis_mr_od_given_a)
					{
					$mr2gp1_od[]="A";
					$mr2gp1_od[]="<b>$vis_mr_od_given_a</b>";
					}
					if($vis_mr_od_given_txt_1 && $vis_mr_od_given_txt_1!='20/')
					{
					$mr2gp1_od[]="<b>$vis_mr_od_given_txt_1</b>";
					}
					?>
					<td class="text_lable bdrbtm"><?php odLable(); print implode("&nbsp;",$mr2gp1_od); ?></td>
					<?php
					if(($vis_mr_od_given_add!='' and $vis_mr_od_given_s!="" and $vis_mr_od_given_c!="" and $vis_mr_od_given_a!="" and $vis_mr_od_given_txt_1 && $vis_mr_od_given_txt_1!='20/'))
					{
					 $mr2gp2_addod[]="Add";
					 $mr2gp2_addod[]="<b>$vis_mr_od_given_add</b>";
					 }
					if($vis_mr_od_given_txt_2 && $vis_mr_od_given_txt_2!='20/')
					{
					$mr2gp2_addod[]="<b>$vis_mr_od_given_txt_2</b>";
					}
			?>
					<td class="text_lable bdrbtm"><?php print implode("&nbsp;",$mr2gp2_addod); ?></td>
					<?php 
					}
					
					 if(($mrPrism2 == 1)
					&&
					($vis_mr_od_given_p || $vis_mr_od_given_prism || $vis_mr_od_given_slash || $vis_mr_od_given_sel_1)
					||
					($vis_mr_os_given_p || $vis_mr_os_given_prism || $vis_mr_os_given_slash || $vis_mr_os_given_sel_1)
					){
					$MR2ndPrism = true;
					if($vis_mr_od_given_p)
					{
						$mr2gp3_prismod[]="P";
						$mr2gp3_prismod[]="<b>$vis_mr_od_given_p</b>";
						$mr2gp3_prismod[]="<img src='../images/pic_vision_pc.jpg'/>";
					}
					if($vis_mr_od_given_prism)
					{
						$mr2gp3_prismod[]="<b>$vis_mr_od_given_prism</b>";
					}
					if($vis_mr_od_given_slash)
					{
						$mr2gp3_prismod[]="/";
						$mr2gp3_prismod[]="<b>$vis_mr_od_given_slash</b>";
					}
					if($vis_mr_od_given_sel_1)
					{
						$mr2gp3_prismod[]="<b>$vis_mr_od_given_sel_1</b>";
					}
					?>
					<td class="text_lable bdrbtm"><?php print implode("&nbsp;",$mr2gp3_prismod); ?></td>
					<?php 
					}
						if(($mrGLPH2 == 1)
						&&
						($vis_mr_od_given_sel_2 || ($visMrOtherOdSel2Vision && $visMrOtherOdSel2Vision!='20/'))
						||
						($vis_mr_os_given_sel_2 || ($visMrOtherOsSel2Vision && $visMrOtherOsSel2Vision!='20/'))
						){
						$MR2ndPrismGLPH = true;
					
					if($vis_mr_od_given_sel_2)
					{
					 $mr2gp4_GL_PHod[]="GL/PH";
					 $mr2gp4_GL_PHod[]="<b>$vis_mr_od_given_sel_2</b>";
					 } 
					if($visMrOtherOdSel2Vision && $visMrOtherOdSel2Vision!='20/') 
					{
					$mr2gp4_GL_PHod[]="Vision";
					$mr2gp4_GL_PHod[]="<b>$visMrOtherOdSel2Vision</b>";
					}
					?>
					
					<td class="text_lable bdrbtm"><?php print implode("&nbsp;",$mr2gp4_GL_PHod); ?></td>
					<?php
					}
					?>
					
  </tr>
				
<tr>				
	<?php
	if(($vis_mr_od_given_s || $vis_mr_od_given_c || $vis_mr_od_given_a || $vis_mr_od_given_add)
		||
		($vis_mr_od_given_txt_1 && $vis_mr_od_given_txt_1!='20/')
		||
		($vis_mr_od_given_txt_2 && $vis_mr_od_given_txt_2!='20/')
		||
		($vis_mr_os_given_s || $vis_mr_os_given_c || $vis_mr_os_given_a || $vis_mr_os_given_add)
		||
		($vis_mr_os_given_txt_1 && $vis_mr_os_given_txt_1!='20/')
		||
		($vis_mr_os_given_txt_2 && $vis_mr_os_given_txt_2!='20/')
		){
		$MR2nd = true;
		
			?>			
			<?php
				  /* $mr2gp1_os[]="OS";*/
					if($vis_mr_os_given_s)
					{
					$mr2gp1_os[]="S";
					$mr2gp1_os[]="<b>$vis_mr_os_given_s</b>";
					}
					if($vis_mr_os_given_c)
					{
					$mr2gp1_os[]="C";
					$mr2gp1_os[]="<b>$vis_mr_os_given_c</b>";
					}
					if($vis_mr_os_given_a)
					{
					$mr2gp1_os[]="A";
					$mr2gp1_os[]="<b>$vis_mr_os_given_a</b>";
					}
					if($vis_mr_os_given_txt_1 && $vis_mr_os_given_txt_1!='20/')
					{
					$mr2gp1_os[]="<b>$vis_mr_os_given_txt_1</b>";
					}
					?>
					<td class="text_lable "><?php osLable(); print implode("&nbsp;",$mr2gp1_os); ?></td>
					<?php
					if(($vis_mr_os_given_add!="" and $vis_mr_os_given_s!="" and $vis_mr_os_given_c!="" and $vis_mr_os_given_a!="" and $vis_mr_os_given_txt_1 && $vis_mr_os_given_txt_1!='20/'))
					{
					$mr2gp2_addos[]="Add";
					$mr2gp2_addos[]="<b>$vis_mr_os_given_add</b>";
					}
					if($vis_mr_os_given_txt_2 && $vis_mr_os_given_txt_2!='20/')
					{
					$mr2gp2_addos[]="<b>$vis_mr_os_given_txt_2</b>";
					}
					?>
					<td class="text_lable "><?php  print implode("&nbsp;",$mr2gp2_addos); ?></td>
					
					<?php 
					}
					
					 if(($mrPrism2 == 1)
					&&
					($vis_mr_od_given_p || $vis_mr_od_given_prism || $vis_mr_od_given_slash || $vis_mr_od_given_sel_1)
					||
					($vis_mr_os_given_p || $vis_mr_os_given_prism || $vis_mr_os_given_slash || $vis_mr_os_given_sel_1)
					){
					$MR2ndPrism = true;
					if($vis_mr_os_given_p)
					{
					$mr2gp3_prismos[]="P";
					$mr2gp3_prismos[]="<b>$vis_mr_os_given_p</b>";
					}
					if($vis_mr_os_given_p || $vis_mr_os_given_prism){
						$mr2gp3_prismos[]="<img src='../images/pic_vision_pc.jpg'/>";
					}
					if($vis_mr_os_given_prism)
					{
					$mr2gp3_prismos[]="<b>$vis_mr_os_given_prism</b>";
					}
					if($vis_mr_os_given_slash)
					{
					$mr2gp3_prismos[]="/";
					$mr2gp3_prismos[]="<b>$vis_mr_os_given_slash</b>";
					}
					if($vis_mr_os_given_sel_1)
					{
					$mr2gp3_prismos[]="<b>$vis_mr_os_given_sel_1</b>";
					}
				?>
							
					<td class="text_lable "><?php print implode("&nbsp;",$mr2gp3_prismos); ?></td>
					<?php 
					}
						if(($mrGLPH2 == 1)
						&&
						($vis_mr_od_given_sel_2 || ($visMrOtherOdSel2Vision && $visMrOtherOdSel2Vision!='20/'))
						||
						($vis_mr_os_given_sel_2 || ($visMrOtherOsSel2Vision && $visMrOtherOsSel2Vision!='20/'))
						){
						$MR2ndPrismGLPH = true;
					
					 if($vis_mr_os_given_sel_2)
					 {
					 $mr2gp4_GL_PHos[]="GL/PH";
					 $mr2gp4_GL_PHos[]="<b>$vis_mr_os_given_sel_2</b>";
					 }
					if($visMrOtherOsSel2Vision && $visMrOtherOsSel2Vision!='20/') 
					{
					$mr2gp4_GL_PHos[]="Vision";
					$mr2gp4_GL_PHos[]="<b>$visMrOtherOsSel2Vision</b>";
					}
					?>
					
					<td class="text_lable "><?php print implode("&nbsp;",$mr2gp4_GL_PHos); ?></td>
					<?php
					}
					?>
					
  </tr>
</table>
<?php
}
?>
<?php
						if(($visMrOtherOdS_3 || $visMrOtherOdC_3 || $visMrOtherOdA_3 || $visMrOtherOdAdd_3)
							||
							($visMrOtherOdTxt1_3 && $visMrOtherOdTxt1_3!='20/')
							||
							($visMrOtherOsS_3 || $visMrOtherOsC_3 || $visMrOtherOsA_3 || $visMrOtherOsAdd_3)
							||
							($visMrOtherOsTxt1_3 && $visMrOtherOsTxt1_3!='20/')
							){
							$MR3rd = true;
							?>
<table  class="border" cellpadding="0" cellspacing="0" style=" width:740px;">
<tr>
	<?php 
			$givenMr = ($rd_vis_mr_none_given=="MR 3") ? "(Given)" : "";
		?>
						<td style="width:740px;" class="tb_subheading" colspan="4"><b>MR3 <?php echo $givenMr.showDoctorName($providerIdOther_3); ?></b></td>
  </tr>
					<tr>
						<td width="31%">&nbsp;</td>
						<td width="20%">&nbsp;</td>

						<td width="26%">Prism</td>
						<td width="23%"><!-- GL/PH --></td>
  </tr> 
<?php
							        $mr3gp1_od[]="";
			                        $mr3gp1_os[]="";
			                        $mr3gp2_addod[]="";
			                        $mr3gp2_addos[]="";
			                        $mr3gp3_prismod[]="";
			                        $mr3gp3_prismos[]="";
							        $mr3gp4_GL_PHod[]="";
									$mr3gp4_GL_PHos[]="";
?>
							<tr>
<?php
										/*$mr3gp1_od[]="OD";*/
										if($visMrOtherOdS_3)
										{
										 $mr3gp1_od[]="S";
										  $mr3gp1_od[]="<b>$visMrOtherOdS_3</b>"; 
										  }
										if($visMrOtherOdC_3)
										{
										 $mr3gp1_od[]="C";
										  $mr3gp1_od[]="<b>$visMrOtherOdC_3</b>";
										  }
										if($visMrOtherOdA_3)
										{
										 $mr3gp1_od[]="A";
										  $mr3gp1_od[]="<b>$visMrOtherOdA_3</b>";
										  }
										if($visMrOtherOdTxt1_3 && $visMrOtherOdTxt1_3!='20/')
										{
										 $mr3gp1_od[]="<b>$visMrOtherOdTxt1_3</b>";
										 }
									?>
								<td class="text_lable bdrbtm"><?php odLable(); print implode("&nbsp;",$mr3gp1_od);?></td>
								<?php	if(($visMrOtherOdAdd_3!="" and $visMrOtherOdS_3!="" and $visMrOtherOdC_3!="" and $visMrOtherOdA_3!="" and $visMrOtherOdTxt1_3!="" and $visMrOtherOdTxt1_3!='20/'))
										{
										$mr3gp2_addod[]="Add";
										$mr3gp2_addod[]="<b>$visMrOtherOdAdd_3</b>";
										}
										if($visMrOtherOdTxt2_3 && $visMrOtherOdTxt2_3!='20/')
										{
										//$mr3gp2_addod[]="<b>$visMrOtherOdTxt2_3</b>";
										}
									?>
								<td class="text_lable bdrbtm"><?php print implode("&nbsp;",$mr3gp2_addod);?></td>
						<?php
						if(
							($mrPrism3 == 1)
							&&
							($visMrOtherOdP_3 || $visMrOtherOdPrism_3 || $visMrOtherOdSlash_3 || $visMrOtherOdSel1_3)
							||
							($visMrOtherOsP_3 || $visMrOtherOsPrism_3 || $visMrOtherOsSlash_3 || $visMrOtherOsSel1_3)
							){
							$MR3rdPrism = true;
							?>	<?php
										$mr3gp3_prismod[]="";							
										if($visMrOtherOdP_3)
										{
											$mr3gp3_prismod[]="P";							
											$mr3gp3_prismod[]="<b>$visMrOtherOdP_3</b>";
											$mr3gp3_prismod[]="<img src='../images/pic_vision_pc.jpg'  align='top'/>"; 
										}													
										if($visMrOtherOdPrism_3)
										{ 
											$mr3gp3_prismod[]="<b>$visMrOtherOdPrism_3</b>";
										}										
										if($visMrOtherOdSlash_3)
										{
											$mr3gp3_prismod[]="/";
											$mr3gp3_prismod[]="<b>$visMrOtherOdSlash_3</b>";
										}
										if($visMrOtherOdSel1_3)
										{
											$mr3gp3_prismod[]="<b>$visMrOtherOdSel1_3</b>";
										}
								?>							
								<td class="text_lable bdrbtm"><?php print implode("&nbsp;",$mr3gp3_prismod);?></td>
							<?php }?>
								<?php
										$mr3gp4_GL_PHod[]="";//"OD";
										if($visMrOtherOdSel2_3)
											{
											$mr3gp4_GL_PHod[]="GL/PH";
											$mr3gp4_GL_PHod[]="<b>$visMrOtherOdSel2_3</b>";
											}
											if($visMrOtherOdSel2Vision_3 && $visMrOtherOdSel2Vision_3!='20/')
											{
											$mr3gp4_GL_PHod[]="Vision";
											$mr3gp4_GL_PHod[]="<b>$visMrOtherOdSel2Vision_3</b>";
											}
										?>
								 <td class="text_lable bdrbtm"><?php print implode("&nbsp;",$mr3gp4_GL_PHod);?></td>
							</tr>

						<tr>
							<?php
							/*$mr3gp1_os[]="OS";*/
							if($visMrOtherOsS_3)
							{
							$mr3gp1_os[]="S";
							$mr3gp1_os[]="<b>$visMrOtherOsS_3</b>";
							}
							if($visMrOtherOsC_3) 
							{
							$mr3gp1_os[]="C";
							$mr3gp1_os[]="<b>$visMrOtherOsC_3</b>";
							}
							if($visMrOtherOsA_3)
							{
							$mr3gp1_os[]="A";
							$mr3gp1_os[]="<b>$visMrOtherOsA_3</b>";
							}
							if($visMrOtherOsTxt1_3 && $visMrOtherOsTxt1_3!='20/')
							{
							$mr3gp1_os[]="<b>$visMrOtherOsTxt1_3</b>";
							}?>
							<td class="text_lable bdrbtm"><?php osLable(); print implode("&nbsp;",$mr3gp1_os);?></td>
							<?php if(($visMrOtherOsAdd_3!="" and $visMrOtherOsS_3!="" and $visMrOtherOsC_3!="" and $visMrOtherOsA_3!="" and $visMrOtherOsTxt1_3!="" and $visMrOtherOsTxt1_3!='20/'))
								{
								$mr3gp2_addos[]="Add";
								$mr3gp2_addos[]="<b>$visMrOtherOsAdd_3</b>";
								}
								if($visMrOtherOsTxt2_3 && $visMrOtherOsTxt2_3!='20/')
								{
								//$mr3gp2_addos[]="<b>$visMrOtherOsTxt2_3</b>";
								}
								?>
							<td class="text_lable bdrbtm"><?php print implode("&nbsp;",$mr3gp2_addos);?></td>
							<?php
								$mr3gp3_prismos[]="";								
								if($visMrOtherOsP_3)
								{
									$mr3gp3_prismos[]="P";								
									$mr3gp3_prismos[]="<b>$visMrOtherOsP_3</b>";
									$mr3gp3_prismos[]="<img src='../images/pic_vision_pc.jpg'  align='top'/>";
								}
								
								if($visMrOtherOsPrism_3)
								{
								$mr3gp3_prismos[]="<b>$visMrOtherOsPrism_3</b>";
								}
								if($visMrOtherOsSlash_3)
								{
								$mr3gp3_prismos[]="/";
								$mr3gp3_prismos[]="<b>$visMrOtherOsSlash_3</b>";
								}													
								if($visMrOtherOsSel1_3)
								{
								$mr3gp3_prismos[]="<b>$visMrOtherOsSel1_3</b>";
								}
							?>
							<td class="text_lable bdrbtm"><?php print implode("&nbsp;",$mr3gp3_prismos);?></td>
							<?php
							$mr3gp4_GL_PHos[]="";										
							
								if($visMrOtherOsSel2_3)
								{
								 	$mr3gp4_GL_PHos[]="GL/PH";
								  	$mr3gp4_GL_PHos[]="<b>$visMrOtherOsSel2_3</b>"; 
								}
								if($visMrOtherOsSel2Vision_3 && $visMrOtherOsSel2Vision_3!='20/') 
								{
									$mr3gp4_GL_PHos[]="Vision";
								  	$mr3gp4_GL_PHos[]="<b>$visMrOtherOsSel2Vision_3</b>";
								}
							?>
							<td class="text_lable bdrbtm"> <?php print implode("&nbsp;",$mr3gp4_GL_PHos);?></td>
						</tr>
</table>
<? 
}//End MR3
?>
<?php if($vis_mr_desc_other!=""){
			?>
			
				<!-- Description -->
					<table style="width:740px;" cellpadding="0" cellspacing="0" class="border">
						<tr>
							<td width="11%" valign="top" class="text bdrbtm">Description</td>
							<td width="89%" class="text bdrbtm">&nbsp;<?php echo $vis_mr_desc_other; ?></td>
						</tr>
</table>
				<!-- Description -->					
				
			<?php 
		}
		?>	
<!--start of CR Row Under Vision-->
				<?php
				//echo ($visCRdateCheckpass==true) ? "true" : "false";
				//echo $txt_vis_cr_od_s."S<br>";
				//echo $txt_vis_cr_od_c."C<br>";
				//echo $txt_vis_cr_od_a."A<br>";
				//echo $sel_vis_cr_od_p."P<br>";
				if(($visCRdateCheckpass==true && $txt_vis_cr_od_s!="") && ($txt_vis_cr_od_c!="" || ($txt_vis_cr_od_a!="" && $txt_vis_cr_od_a!="x") || $sel_vis_cr_od_p!="")){
					?>
					
							<table style="width:740px;" cellpadding="0" cellspacing="0" class="border">
								<tr>
									<td  class="text_9b bdrbtm bdrright"><?php if($txt_vis_cr_od_s!="" && ($txt_vis_cr_od_c!="" || $txt_vis_cr_od_a!="" || $sel_vis_cr_od_p!="")){?>CR (Cycloplegic Refraction)<?php }?></td>
								</tr>
							</table>
						
					
							<table width="60%" cellpadding="0" cellspacing="2" class="border">
								<tr>
								<?php 
								$crgp1od[]="";
								$crgp1os[]="";
								$crgp2prismod[]="";
								$crgp2prismos[]="";
								$count_crrow1=0;
								if($txt_vis_cr_od_s!="" && ($txt_vis_cr_od_c!="" || $txt_vis_cr_od_a!="" || $sel_vis_cr_od_p!="")){?>
									<?php if($txt_vis_cr_od_s!="" && ($txt_vis_cr_od_c!="" || $txt_vis_cr_od_a!="" || $sel_vis_cr_od_p!=""))
									{
									$crgp1od[]="OD";
									}					
									
									if($txt_vis_cr_od_s!=""){$crgp1od[]="S"; }
									
									 $crgp1od[]="<b>$txt_vis_cr_od_s</b>"; 
									
									if($txt_vis_cr_od_c!=""){$crgp1od[]="C"; }
										
									$crgp1od[]="<b>$txt_vis_cr_od_c</b>";
									if($txt_vis_cr_od_a!=""){$crgp1od[]="A"; }
									if($txt_vis_cr_od_a!="")
									{
									$crgp1od[]="<b>$txt_vis_cr_od_a"."&#176;</b>";
									} 
									?>
									<td class="text_lable bdrbtm"><?php odLable(); print implode("&nbsp;",$crgp1od);?> </td>
									<?php 
								}else{ 
									$count_crrow1= $count_crrow1+13;
								}
								if($elem_prismCr == "1"){
									if($sel_vis_cr_od_p!="" && $sel_vis_cr_od_prism!="" && $sel_vis_cr_od_p!=""){?>
																							
										<?php
										 if($sel_vis_cr_od_p!=""){$crgp2prismod[]="P";}
										
										$crgp2prismod[]="<b>$sel_vis_cr_od_p</b>";
										if($sel_vis_cr_od_prism!="" && $sel_vis_cr_od_p!="")
										{
										$crgp2prismod[]="<img src='../images/pic_vision_pc.jpg'/>";
										}
										
										$crgp2prismod[]="<b>$sel_vis_cr_od_prism</b>";
										if($sel_vis_cr_od_slash!="")
										{
										$crgp2prismod[]="/";
										 }
										$crgp2prismod[]="<b>$sel_vis_cr_od_slash</b>";
										if($sel_vis_cr_od_slash!="" )
										{
										$crgp2prismod[]="<b>$sel_vis_cr_od_sel_1</b>";
										}
										?>
										<td class="text_lable bdrbtm"><?php  print implode("&nbsp;",$crgp2prismod);?> </td>
										<?php
									}
								}else{ 
									$count_crrow1= $count_crrow1+14;
								}
								for($i=0;$i<$count_crrow1;$i++){
									echo("<td class='text_lable bdrbtm'>&nbsp;</td>");
								}
								?>			
							</tr>
							<tr>
							<?php
							$count_crrow2=0;
							if($txt_vis_cr_os_s!="" && ($txt_vis_cr_os_c!="" ||$txt_vis_cr_os_a!="" ||$sel_vis_cr_os_p!="")){?>
                                <?php
								if($txt_vis_cr_os_s!="" && ($txt_vis_cr_os_c!="" ||$txt_vis_cr_os_a!="" ||$sel_vis_cr_os_p!="")){/*$crgp1os[]="OS";*/}
								 if($txt_vis_cr_os_s!="")
								 {
								 $crgp1os[]="S"; 
								 }
								$crgp1os[]="<b>$txt_vis_cr_os_s</b>";
								if($txt_vis_cr_os_c!="")
								{
								$crgp1os[]="C"; 
								}
								$crgp1os[]="<b>$txt_vis_cr_os_c</b>"; 
								if($txt_vis_cr_os_a!="")
								{
								$crgp1os[]="A";
								}								
								if($txt_vis_cr_os_a!="")
								{
								 $crgp1os[]="<b>$txt_vis_cr_os_a"."&#176;</b>";
								 } 
								 ?>
								 <td class="text_lable bdrbtm"><?php osLable(); print implode("&nbsp;",$crgp2prismod);?> </td>
								<?php 
							}else{ 
								$count_crrow2= $count_crrow2+13;
							}
							if($elem_prismCr == "1"){
								if($sel_vis_cr_os_p!="" && $sel_vis_cr_os_prism!="" && $sel_vis_cr_os_p!=""){?>
									<?php
									 if($sel_vis_cr_os_p!=""){$crgp2prismos[]="P"; }
									$crgp2prismos[]="<b>$sel_vis_cr_os_p</b>";
									if($sel_vis_cr_os_prism!="" && $sel_vis_cr_os_p!="")
									{
									$crgp2prismos[]="<img src='../images/pic_vision_pc.jpg'/>";
									}
									$crgp2prismos[]="<b>$sel_vis_cr_os_prism</b>";
    								if($sel_vis_cr_os_p!="")
									{
									$crgp2prismos[]="/";
									 }									
									$crgp2prismos[]="<b>$sel_vis_cr_os_p</b>";
									if($sel_vis_cr_os_p!="" && $sel_vis_cr_os_prism!="")
									{
									$crgp2prismos[]="<b>$sel_vis_cr_os_sel_1</b>";
									}
									?>	
									 <td><?php  print implode("&nbsp;",$crgp2prismos);?> </td>				
									<?php 
								} 
							}else{ 
								$count_crrow2= $count_crrow2+14;
							}
							for($i=0;$i<$count_crrow2;$i++){
								echo("<td>&nbsp;</td>");
							}
							?>				
						</tr>								
					</table>
				
			<?php 
		} 
		?>
		<!-- End Of CR Row Under Vision-->
		<!-- COLOR PLATE -->
<?php 
//-- Color Plate Data----// 
	if($vis_controlValueOd || $vis_controlValueOs || $vis_steropsis
			||
			$vis_exo_od_s || $vis_exo_od_c || $vis_exo_od_a || $vis_exo_os_s || $vis_exo_os_c || $vis_exo_os_a
			||
			$vis_ret_pd_od || $vis_ret_pd_os || $vis_ret_pd
			){
			$colorPlate = true;
			if($vis_controlValueOd || $vis_controlValueOs || $vis_steropsis){
			?>
			<table style="width:740px;" class="border" cellspacing="0" cellpadding="0" >
				<tr>
					<td  colspan="5" valign="middle" style="width:740px;" class="text_10b tb_heading"><b>Color Plate:</b></td>
				</tr>
				<?php
				   if($vis_controlValueOd || $vis_controlValueOs || $vis_steropsis){
					?>
					<tr>
						<td width="12%" align="left" class="text_9">IPL&nbsp;Control</td>
						<td width="12%"  align="left" class="text_9"><?php odLable();?></td>
						<td width="12%" align="left" class="text_9">IPL&nbsp;Control</td>
						<td width="14%"  align="left" class="text_9"><?php osLable();?></td>
						<td width="50%"  align="left" class="text_9">Steropsis</td>
					</tr>
 				<?php } ?>
           
				<?php
				   if($vis_controlValueOd || $vis_controlValueOs || $vis_steropsis){
					?>  <!-- vis_controlValueOs vis_control_os  -->
					<tr>
						 <td class="text_lable bdrbtm"><?php if($vis_control == 'plus') echo '+'; elseif($vis_control == 'minus') echo '-'; ?></td>
						 <td class="text_lable bdrbtm"><?php if($vis_controlValueOd and $vis_control) echo $vis_controlValueOd.'/10'; elseif($vis_controlValueOd=="" and $vis_control) echo "N/A"; ?></td>
						 <td class="text_lable bdrbtm"><?php if($vis_control_os == 'plus') echo '+'; elseif($vis_control_os == 'minus') echo '-'; ?></td>
						 <td class="text_lable bdrbtm"><?php if($vis_controlValueOs and $vis_control_os) echo $vis_controlValueOs.'/10'; ?></td>
						 <td class="text_lable bdrbtm"><?php if($vis_steropsis) echo $vis_steropsis; else echo '&nbsp;';?></td>
					</tr>
				<?php }  ?>
			</table>
				<?php }
					 if($vis_exo_od_s || $vis_exo_od_c || $vis_exo_od_a || $vis_exo_os_s || $vis_exo_os_c || $vis_exo_os_a){
					 ?>
			
						<table style="width:740px;" class="border" cellspacing="0" cellpadding="0" bordercolor="#EEEEEE">					
								<tr>
									<td class="bdrbtm tb_heading" colspan="4">Retinoscopy</td>
								</tr>
								<?php
								if($vis_exo_od_s || $vis_exo_od_c || $vis_exo_od_a || $vis_exo_os_s || $vis_exo_os_c || $vis_exo_os_a){
								 
								$RetinoscopyAray[]="";
								$RetinoscopyArays[]="";
 									?>
								  <tr>
									 
									 <?php if($vis_exo_od_s) $RetinoscopyAray[]="S";$RetinoscopyAray[]="<b>".$vis_exo_od_s."</b>"; ?>
									 <?php if($vis_exo_od_c) $RetinoscopyAray[]="C";$RetinoscopyAray[]="<b>".$vis_exo_od_c."</b>"; ?>
									 <?php if($vis_exo_od_a) $RetinoscopyAray[]="A";$RetinoscopyAray[]="<b>".$vis_exo_od_a."</b>"; ?>
										<td class="text_lable bdrbtm"><?php odLable(); print implode("&nbsp;",$RetinoscopyAray);?></td>
							</tr> <?php } 
									if($vis_exo_od_s || $vis_exo_od_c || $vis_exo_od_a || $vis_exo_os_s || $vis_exo_os_c || $vis_exo_os_a){
									?> 
									 <tr>
									 
									 <?php if($vis_exo_os_s) $RetinoscopyArays[]="S";$RetinoscopyArays[]="<b>".$vis_exo_os_s."</b>"; ?>
									 <?php if($vis_exo_os_c) $RetinoscopyArays[]="C";$RetinoscopyArays[]="<b>".$vis_exo_os_c."</b>"; ?>
									 <?php if($vis_exo_os_a) $RetinoscopyArays[]="A";$RetinoscopyArays[]="<b>".$vis_exo_os_a."</b>"; ?>
										<td class="text_lable bdrbtm"><?php osLable(); print implode("&nbsp;",$RetinoscopyArays);?></td>
							</tr>
								<?php } ?>
						</table>
					<?php } 
				   if($vis_ret_pd_od || $vis_ret_pd_os || $vis_ret_pd){
					?>
						<table class="border" cellspacing="0" cellpadding="0" style="width:740px;" >					
							<tr>
								<td class="bdrbtm tb_heading" colspan="4">Exopholmalmeter</td>
							</tr>
							<?php
							 if($vis_ret_pd_od || $vis_ret_pd_os || $vis_ret_pd){
							   ?>
							<tr>
							   <td class="bdrbtm" colspan="4" align="left"><?php echo "PD "; if($vis_ret_pd) echo $vis_ret_pd; //else echo '&nbsp'; ?></td>
							</tr>  
							<?php } ?>
					   
						 	 <tr>
							   <?php
							   if($vis_ret_pd_od || $vis_ret_pd_os || $vis_ret_pd){
								?>
								<td class="bdrbtm" colspan="2" ><?php odLable();if($vis_ret_pd_od) echo $vis_ret_pd_od; //else echo '&nbsp'; ?></td>
								<td class="bdrbtm" width="87%" colspan="2" ><?php osLable(); if($vis_ret_pd_os) echo $vis_ret_pd_os; //else echo '&nbsp';?></td>		
							 <?php } ?>
						  </tr>
					</table>
 			<?php } ?>
		<?php
		}
		?>
<!-- COLOR PLATE -->
 
<?php 
		$sql = "SELECT * FROM chart_cvf WHERE patientId = '$patient_id' and formId = '$form_id' ";					
		$row = sqlQuery($sql);	

		if($row != false){
		//print_r($row); die;
			extract($row);//$summaryOd;
			?>
			
			
					<table class="border" cellpadding="0" cellspacing="0" style="width:740px;" >
						<tr  style="width:740px;
">
							<td valign="middle" colspan="3" class="text_10b tb_heading"><b>CVF (Confrontation Field)</b></td>
						</tr>
					<!-- </table>
					<table border="0" cellpadding="0" cellspacing="0" width="100%" height=""> -->
						<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" width="50">&nbsp;</td>
							<td class="text_9 bdrbtm"  align="left" width="325"><?php odLable();?></td>
							<td class="text_9 bdrbtm" align="left" width="325"><?php osLable();?></td>
						</tr>
						<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" width="50">&nbsp;</td>
							<td class="text_9 bdrbtm" align="left" valign="top" width="325"><?php if($summaryOd) echo $summaryOd; else echo '&nbsp;'; ?></td>
							<td class="text_9 bdrbtm" align="left" valign="top" width="325"><?php if($summaryOs) echo $summaryOs; else echo '&nbsp;'; ?></td>
						</tr>
						<tr>
							<td class="text_9 bdrbtm" align="left" valign="top" width="50">&nbsp;</td>
							<td class="text_9 bdrbtm" align="left" width="325px">
							<?php
							if($objCpr->isAppletModified(trim($drawOd))){
								$imagecvf1 = "";
								$tableLA = 'chart_cvf';
								$idNameLA = 'cvf_id';
								$pixelLaDrawing = 'drawOd';
								$imageLA ="../../library/images/picEomCvf.jpg";
								$altLA = 'CVF'; 
							if($objCpr->getAppletImage($cvf_id,$tableLA,$idNameLA,$pixelLaDrawing,$imageLA,$altLA,"1")){									
								//echo('<img src="'.$gdFilename.'"  width="200" height="150" />');			
								echo('<img src="'.$gdFilename.'" width="200" height="90"/>');					 
								$ChartNoteImagesString[]=$gdFilename;								
								
								}							
							}
							?>
							</td>
							<td class="text_9 bdrbtm" align="left" width="325px">
							<?php
							if($objCpr->isAppletModified(trim($drawOd))){
								$imagecvf1 = "";
								$tableLA = 'chart_cvf';
								$idNameLA = 'cvf_id';
								$pixelLaDrawing = 'drawOs';
								$imageLA = "../../library/images/picEomCvf.jpg";
								$altLA = 'CVF'; 
							    if($objCpr->getAppletImage($cvf_id,$tableLA,$idNameLA,$pixelLaDrawing,$imageLA,$altLA,"1")){
									//echo('<img src="'.strip_tags($gdFilename).'"  width="200" height="150" />');		
									echo('<img src="'.$gdFilename.'" width="200" height="90"/>');				
									$ChartNoteImagesString[]=$gdFilename;								
								}						
							}
							?>
							</td>
						</tr>
					</table>
				
			<?php 
		}
		?>
		<!-- CVF -->
		<!-- Diplopia -->
	<?php
		
		$sql = "SELECT * FROM chart_diplopia WHERE patientId = '$patient_id' AND formId = '$form_id' ";					
		$row = sqlQuery($sql);	
		if($row != false){
			extract($row);
			?>
		
					<table class="border" cellpadding="0" cellspacing="0" style="width:740px;" >
						<tr>
							<td valign="middle" colspan="3" class="text_10b tb_heading"><b>Diplopia</b></td>
						</tr>
					<!-- </table>
					<table border="0" cellpadding="0" cellspacing="0" width="100%"> -->
						<tr>
							<td class="bdrbtm ">&nbsp;</td>
							<td class="text_9 bdrbtm"  align="left" width="325"><?php odLable();?></td>
							<td class="text_9 bdrbtm"  align="left" width="325"><?php osLable();?></td>
						</tr>
						<tr>
							<td width="50" class="text_9 bdrbtm" >&nbsp;</td>
							<td align="left" class="text_9 bdrbtm" valign="top" width="325"><?php echo $summaryOd; ?></td>
							<td align="left" class="text_9 bdrbtm" valign="top" width="325"><?php echo $summaryOs; ?></td>
						</tr>
						<?php
						if($objCpr->isAppletModified($drawing)){?>
							<tr>
							<td width="50">&nbsp;</td>
							<td align="center" colspan="2">
							<?php
								$imageNAme=drawOnImageImage($drawing);
								echo('<img src="'.$imageNAme.'" width="300" height="100"/>');
								$ChartNoteImagesString[]=$imageNAme;			
								$ChartNoteImagesString[]=$gdFilename;
							
							?>
							</td>
						</tr>
						<?php } ?>
					</table>
				
			<?php
		}
		?>
<!-- Diplopia -->	
<?php

$l_and_a = true;
$pupilPrint = true;
$externalPrint = true;
$eomPrint = true;
$IOPPrint = true;
$SLEPrint = true;
$OptNevPrint = true;
$RandVPrint = true;
$assessmentPlanPrint=true;

include(dirname(__FILE__).'/examsSummarypdf.php');
?>
<?php 
$patient_workprint_data = ob_get_contents();
$patient_workprint_data = stripslashes(html_entity_decode($patient_workprint_data,ENT_QUOTES|ENT_XHTML|ENT_HTML5,'ISO-8859-1'));
ob_end_clean();
?>