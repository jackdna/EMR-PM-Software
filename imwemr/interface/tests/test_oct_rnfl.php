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
$callFromInterface		= strtolower(trim(strip_tags($_GET['callFromInterface'])));
$doNotShowRightSide 	= strtolower(trim($_REQUEST['doNotShowRightSide']));
require_once("../../config/globals.php");
if($callFromInterface != 'admin'){
	require_once("../../library/patient_must_loaded.php");
}
require_once("../../library/classes/class.tests.php");
require_once("../../library/classes/SaveFile.php");
require_once("../../library/classes/ChartTestPrev.php");
require_once("../../library/classes/work_view/CPT.php");
require_once("../../library/classes/work_view/Epost.php");
$library_path 			= $GLOBALS['webroot'].'/library';
$patient_id				= $_SESSION['patient'];
$objTests				= new Tests;
$objCPT					= new CPT;
$testname               = "OCT-RNFL";
$objEpost				= new Epost($patient_id,$testname);
//$objTests->patient_id 	= $patient_id;
	
//MAKING OBJECT TO SAVE IMAGE FILES
$oSaveFile = new SaveFile($patient_id);

function getDiagOpts_RNFL($val){
	//if(empty($val)) $val = 1;
	$val = 1;	
	$arr= array(array(
					"AngleClosureG",
					"Childhood OAG",
					"GL-susp, narrow angle",
					"GL-susp, open angle",
					"GL w/ other ocular dx",
					"ICE Syndrom",
					"Inflammatory G",
					"Low TG",
					"Normal TG",
					"NVG",
					"Other 2degrees GL",
					"Pigmentary G",
					"POAG",
					"PXFG/PXE",
					"Steroid G",
					"Other")
				);
						
	if($val=="JS"){
		return json_encode($arr);
	}else{
		return $arr[$val-1];
	}
}
	
$test_table_name		= 'oct_rnfl';
$this_test_properties	= $objTests->get_table_cols_by_test_table_name($test_table_name);
$this_test_screen_name	= $this_test_properties['temp_name'];
$test_master_id			= $this_test_properties['id'];
//User and  User_type
$logged_user 	= $objTests->logged_user;
$userType 		= $objTests->logged_user_type;

if($callFromInterface != 'admin'){
    //================= GETTING PATIENT DATA
	$getPatientDataStr = "SELECT * FROM patient_data WHERE id = '$patient_id'";
	$getPatientDataQry = imw_query($getPatientDataStr);
	$getPatientDataRow = imw_fetch_array($getPatientDataQry);
	$patFname = $getPatientDataRow['fname'];
	$patMname = $getPatientDataRow['mname'];
	$patLname = $getPatientDataRow['lname'];
	$patientName = $patFname." ".$patMname." ".$patLname;
    
	$elem_per_vo			= $objTests->get_tests_VO_access_status();
	
	//iMedicMonitor status.
	$objTests->patient_whc_room();
	
	//----GET ALL ACTIVE TESTS FROM ADMIN------
	$ActiveTests			= $objTests->get_active_tests();

    // FILE NAME for PRINT
    $date_f=date("Y_m_d");
	$rand=rand(0,500);
	$test_name="chart_tests";
	$html_file_name = $test_name.'/'.$date_f.'/test_oct_rfnl_print_'. $_SESSION['patient']."_".$_SESSION['authId']."_".$rand;
	$objTests->mk_print_folder($test_name,$date_f,$oSaveFile->upDir."/UserId_".$_SESSION['authId']."/tmp/");
	$final_html_file_name_path = $oSaveFile->upDir."/UserId_".$_SESSION['authId']."/tmp/".$html_file_name;
	// FILE NAME for PRINT
	/*$date_f=date("Y_m_d");
	$rand=rand(0,500);
	$test_name="chart_tests";
	$html_file_name = $test_name.'/'.$date_f.'/oct_rfnl_test_print_'.$_SESSION['authId']."_".$rand;
	mk_print_folder($test_name,$date_f,'common');*/
	
	//Retain QueryString
	$qstr4js="";
	if(isset($_SERVER["QUERY_STRING"]) && !empty($_SERVER["QUERY_STRING"])){
		$qstr4js .= "&".$_SERVER["QUERY_STRING"];
	}
	//Retain QueryString
	
	$elem_examDate = get_date_format(date('Y-m-d'));
	$elem_examTime = date('Y-m-d H:i:s'); //time();
	$elem_opidTestOrderedDate = ""; // $elem_examDate;

	//Assign Chart Notes specific user Type by checking the list
	if(in_array($userType,$GLOBALS['arrValidCNPhy'])){
		$userType = 1;
	}else if(in_array($userType,$GLOBALS['arrValidCNTech'])){
		$userType = 3;
	}
	
	//-----ORDER BY USERS------------
	$order_by_users									= $objTests->get_order_by_users('cn');
	
	//--------OPERATOR NAME----
	$elem_operatorId = (($userType == 1 || $userType == 12) || ($userType == 3)) ? $_SESSION["authId"] : "";
	$elem_operatorName = (($userType == 1 || $userType == 12) || ($userType == 3)) ? $objTests->getPersonnal3($elem_operatorId) : "";
	
	//GETTING FORM ID AND FINALIZED STATUS
	list($form_id,$finalize_flag)		= $objTests->get_chart_form_id($patient_id);

	
	//Previous Test --
	$oChartTestPrev = new ChartTestPrev($patient_id,"OCT-RNFL");
	
	//No Pop
	$noP = 1;
	if(isset($_GET["noP"]) && !empty($_GET["noP"])){
		$noP = $_GET["noP"];
	}
	
	//Test id
	$tId = 0;
	if(isset($_GET["tId"])){
		$tId = $_GET["tId"];
		//if you come directly without chart
		//Set Form Id zero
		$form_id = 0;
	}
	
	if(!empty($form_id)){
		//Get Form id based on patient id
		$q = "SELECT * FROM ".$test_table_name." WHERE ".$this_test_properties['patient_key']." = '$patient_id' AND formId = '$form_id' AND purged='0' AND del_status='0'";
		$res = imw_query($q);
		$row = imw_fetch_assoc($res);
	}else if(isset($tId) && !empty($tId)){//Get record based on patient id and test id
			$q = "SELECT * FROM ".$test_table_name." WHERE ".$this_test_properties['patient_key']." = '$patient_id' AND ".$this_test_properties['test_table_pk_id']." = '".$tId."' ";
			$res = imw_query($q);
			$row = imw_fetch_assoc($res);
	}else{
		$row = false; // open new test for patient
	}
	
	
	if($row == false){	//&& ($finalize_flag == 0)
		//SET MODE
		$oct_mode = "new";
		$test_edid = "0";
	}else{
		$oct_mode = "update";
		$test_edid = $row["oct_rnfl_id"];
	}

	//Default
	if(isset($_GET["prevVal"]) && ($_GET["prevVal"] == 1)){//New Recors
		$tmp = ($row != false) ? $row["examDate"] : "";
		$row = $objTests->valuesNewRecordsTests($test_table_name, $patient_id, " * ",$tmp);
	}


	if($row != false){
		$oct_pkid = $row["oct_rnfl_id"];
		$test_form_id = $row["form_id"];
		$elem_examDate = ($oct_mode != "new") ? get_date_format($row["examDate"]) : $elem_examDate;
		$elem_examTime = (($oct_mode != "new") && ($row["examTime"] != '0000-00-00 00:00:00')) ? $row["examTime"] : $elem_examTime ;
		$elem_scanLaserOct = $row["scanLaserOct"];
		$elem_scanLaserEye = $row["scanLaserEye"];
		$elem_performedBy = ($oct_mode != "new") ? $row["performBy"] : "";
		$elem_ptUndersatnding = $row["ptUndersatnding"];
		$elem_diagnosis_od = $row["diagnosis"];
		$elem_diagnosis_os = $row["diagnosis_os"];
		$elem_reliabilityOd = $row["reliabilityOd"];
		$elem_reliabilityOs = $row["reliabilityOs"];
		$elem_scanLaserOd = $row["scanLaserOd"];
		$elem_descOd = $row["descOd"];
		$elem_scanLaserOs = $row["scanLaserOs"];
		$elem_descOs = $row["descOs"];
		$elem_stable = $row["stable"];
		$elem_monitorIOP = $row["monitorIOP"];
		$elem_fuApa = $row["fuApa"];
		$elem_ptInformed = $row["ptInformed"];
		$elem_comments = stripslashes($row["comments"]);
		$ar_elem_comments = explode("!~!",$elem_comments);
		$elem_comments_od = $ar_elem_comments[0];
		$elem_comments_os = $ar_elem_comments[1];
		$elem_vfSign = $row["signature"];
		$elem_physician = ( $oct_mode == "update" ) ? $row["phyName"] : "" ;
		$elem_diagnosisOtherOd = $row["diagnosisOther"];
		$elem_diagnosisOtherOs = $row["diagnosisOther_os"];
		
		$elem_phyName = $row["phyName"];
		$elem_techComments = stripslashes($row["techComments"]);
	
		
		$elem_tech2InformPt = $row["tech2InformPt"];
		$elem_informedPtNv = $row["ptInformedNv"];
		$elem_contiMeds = $row["contiMeds"];
		$elem_findingDiscusPt = $row["findingDiscusPt"];
	
		
		$encounterId = $row["encounter_id"];
		$elem_opidTestOrdered = $row["ordrby"];
		if(($row["ordrdt"] != "" && $row["ordrdt"] != "0000-00-00")){
			$elem_opidTestOrderedDate = get_date_format($row["ordrdt"]);
		}
		$purged=$row["purged"];
		$sign_path = $row["sign_path"];
		$sign_path_date_time = $row["sign_path_date_time"];
		$sign_path_date = $sign_path_time = "";
		if($sign_path && $sign_path_date_time!="0000-00-00 00:00:00" || $sign_path_date_time!=0) {
			$sign_path_date = date("".phpDateFormat()."",strtotime($sign_path_date_time));
			$sign_path_time = date("h:i A",strtotime($sign_path_date_time));
		}		
		
		//Od
		$elem_signal_strength_od=$row["signal_strength_od"];
		$elem_quality_od_gd=strpos($row["quality_od"],"Good")!==false ? "Good" : ""; 
		$elem_quality_od_adequate=strpos($row["quality_od"],"Adequate")!==false ? "Adequate" : ""; 
		$elem_quality_od_poor=strpos($row["quality_od"],"Poor")!==false ? "Poor" : ""; 
		
		$ar_details_od = explode("!,!", $row["details_od"]);
		$ar_details_od=array_map('trim',$ar_details_od);	
		$ar_details_od_len = count($ar_details_od);
		$ar_details_od_last = trim($ar_details_od[$ar_details_od_len-1]);
		$elem_detail_od_AlgoFail=in_array("Algorithm Fail", $ar_details_od) ? "Algorithm Fail" : ""; 
		$elem_detail_od_MediaOpacity=in_array("Media Opacity", $ar_details_od) ? "Media Opacity" : ""; 
		$elem_detail_od_Artifact=in_array("Artifact", $ar_details_od) ? "Artifact" : ""; 	
		if(!empty($ar_details_od_last) && $ar_details_od_last!="Algorithm Fail" && $ar_details_od_last!="Media Opacity" && $ar_details_od_last!="Artifact" ){
		$elem_details_od_other=$ar_details_od_last;
		}
		
		$elem_discarea_od=$row["disc_area_od"]; 
		$elem_discsize_od_Large=strpos($row["disc_size_od"],"Large")!==false ? "Large" : ""; 
		$elem_discsize_od_Avg=strpos($row["disc_size_od"],"Avg")!==false ? "Avg" : ""; 
		$elem_discsize_od_Small=strpos($row["disc_size_od"],"Small")!==false ? "Small" : ""; 
		$elem_discedema_od_No=strpos($row["disc_edema_od"],"None")!==false ? "No" : ""; 
		$elem_discedema_od_Mild=strpos($row["disc_edema_od"],"Mild")!==false ? "Mild" : ""; 
		$elem_discedema_od_Md=strpos($row["disc_edema_od"],"Mod")!==false ? "Mod" : ""; 
		$elem_discedema_od_Severe=strpos($row["disc_edema_od"],"Severe")!==false ? "Severe" : ""; 
		$elem_discedema_od_Sup=strpos($row["disc_edema_od"],"Superior")!==false ? "Superior" : ""; 
		$elem_discedema_od_Inf=strpos($row["disc_edema_od"],"Inferior")!==false ? "Inferior" : ""; 
		$elem_rnfl_od_Avg=$row["rnfl_od"]; 
		
		$ar_contour_overall_od = explode(",", $row["contour_overall_od"]);
		$ar_contour_overall_od=array_map('trim',$ar_contour_overall_od);
		$elem_contour_overall_od_NL=in_array("NL",$ar_contour_overall_od) ? "NL" : ""; 
		$elem_contour_overall_od_Thin=in_array("Thin",$ar_contour_overall_od) ? "Thin" : ""; 
		$elem_contour_overall_od_VeryThin=in_array("Very Thin",$ar_contour_overall_od) ? "Very Thin" : ""; 
		$elem_contour_overall_od_Thick=in_array("Thick",$ar_contour_overall_od) ? "Thick" : ""; 
		$elem_contour_overall_od_BL=in_array("Borderline",$ar_contour_overall_od) ? "Borderline" : ""; 
		
		
		$ar_contour_superior_od = explode(",", $row["contour_superior_od"]);
		$ar_contour_superior_od=array_map('trim',$ar_contour_superior_od);
		$elem_contour_superior_od_NL=in_array("NL",$ar_contour_superior_od) ? "NL" : ""; 
		$elem_contour_superior_od_Thin=in_array("Thin",$ar_contour_superior_od) ? "Thin" : ""; 
		$elem_contour_superior_od_VeryThin=in_array("Very Thin",$ar_contour_superior_od) ? "Very Thin" : ""; 
		$elem_contour_superior_od_Thick=in_array("Thick",$ar_contour_superior_od) ? "Thick" : ""; 
		$elem_contour_superior_od_BL=in_array("Borderline",$ar_contour_superior_od) ? "Borderline" : ""; 
		
		$ar_contour_inferior_od = explode(",", $row["contour_inferior_od"]);
		$ar_contour_inferior_od=array_map('trim',$ar_contour_inferior_od);
		$elem_contour_inferior_od_NL=in_array("NL",$ar_contour_inferior_od) ? "NL" : ""; 
		$elem_contour_inferior_od_Thin=in_array("Thin",$ar_contour_inferior_od) ? "Thin" : ""; 
		$elem_contour_inferior_od_VeryThin=in_array("Very Thin",$ar_contour_inferior_od) ? "Very Thin" : ""; 
		$elem_contour_inferior_od_Thick=in_array("Thick",$ar_contour_inferior_od) ? "Thick" : ""; 
		$elem_contour_inferior_od_BL=in_array("Borderline",$ar_contour_inferior_od) ? "Borderline" : ""; 
		
		$ar_contour_temporal_od = explode(",", $row["contour_temporal_od"]);
		$ar_contour_temporal_od=array_map('trim',$ar_contour_temporal_od);
		$elem_contour_temporal_od_NL=in_array("NL",$ar_contour_temporal_od) ? "NL" : ""; 
		$elem_contour_temporal_od_Thin=in_array("Thin",$ar_contour_temporal_od) ? "Thin" : ""; 
		$elem_contour_temporal_od_VeryThin=in_array("Very Thin",$ar_contour_temporal_od) ? "Very Thin" : ""; 
		$elem_contour_temporal_od_Thick=in_array("Thick",$ar_contour_temporal_od) ? "Thick" : ""; 
		$elem_contour_temporal_od_BL=in_array("Borderline",$ar_contour_temporal_od) ? "Borderline" : ""; 
		
		$ar_contour_nasal_od = explode(",", $row["contour_nasal_od"]);
		$ar_contour_nasal_od=array_map('trim',$ar_contour_nasal_od);
		$elem_contour_nasal_od_NL=in_array("NL",$ar_contour_nasal_od) ? "NL" : ""; 
		$elem_contour_nasal_od_Thin=in_array("Thin",$ar_contour_nasal_od) ? "Thin" : ""; 
		$elem_contour_nasal_od_VeryThin=in_array("Very Thin",$ar_contour_nasal_od) ? "Very Thin" : ""; 
		$elem_contour_nasal_od_Thick=in_array("Thick",$ar_contour_nasal_od) ? "Thick" : ""; 
		$elem_contour_nasal_od_BL=in_array("Borderline",$ar_contour_nasal_od) ? "Borderline" : ""; 
		
		$ar_contour_gcc_od = explode(",", $row["contour_gcc_od"]);
		$ar_contour_gcc_od=array_map('trim',$ar_contour_gcc_od);
		$elem_contour_gcc_od_NL=in_array("NL",$ar_contour_gcc_od) ? "NL" : ""; 
		$elem_contour_gcc_od_Thin=in_array("Thin",$ar_contour_gcc_od) ? "Thin" : ""; 
		$elem_contour_gcc_od_VeryThin=in_array("Very Thin",$ar_contour_gcc_od) ? "Very Thin" : ""; 
		$elem_contour_gcc_od_Thick=in_array("Thick",$ar_contour_gcc_od) ? "Thick" : ""; 
		$elem_contour_gcc_od_BL=in_array("Borderline",$ar_contour_gcc_od) ? "Borderline" : ""; 	
		
		$elem_symmertric_od_Yes=($row["symmetric_od"]=="Yes") ? "Yes" : ""; 
		$elem_symmertric_od_No=($row["symmetric_od"]=="No") ? "No" : ""; 
		$elem_interpret_systhesis_od=$row["synthesis_od"]; 
		
		$elem_gpa_od_No=($row["gpa_od"]=="No") ? "No" : "";
		$elem_gpa_od_pos=($row["gpa_od"]=="Possible") ? "Possible" : "";
		$elem_gpa_od_lp=($row["gpa_od"]=="Like Progression") ? "Like Progression" : "";
		
		
		/*$elem_interpret_notes_od=$row["notes_od"]; 
		$elem_interpret_stable_od=$row["stable_od"]; 
		$elem_interpret_improved_od=$row["improved_od"]; 
		$elem_interpret_worse_od=$row["worse_od"]; 
		$elem_comments_od=$row["comments_od"];
		*/
		
		//Os
		$elem_signal_strength_os=$row["signal_strength_os"];
		$elem_quality_os_gd=strpos($row["quality_os"],"Good")!==false ? "Good" : ""; 
		$elem_quality_os_adequate=strpos($row["quality_os"],"Adequate")!==false ? "Adequate" : ""; 
		$elem_quality_os_poor=strpos($row["quality_os"],"Poor")!==false ? "Poor" : ""; 
		
		$ar_details_os = explode("!,!", $row["details_os"]);
		$ar_details_os=array_map('trim',$ar_details_os);	
		$ar_details_os_len = count($ar_details_os);
		$ar_details_os_last = trim($ar_details_os[$ar_details_os_len-1]);
		$elem_detail_os_AlgoFail=in_array("Algorithm Fail", $ar_details_os) ? "Algorithm Fail" : ""; 
		$elem_detail_os_MediaOpacity=in_array("Media Opacity", $ar_details_os) ? "Media Opacity" : ""; 
		$elem_detail_os_Artifact=in_array("Artifact", $ar_details_os) ? "Artifact" : ""; 	
		if(!empty($ar_details_os_last) && $ar_details_os_last!="Algorithm Fail" && $ar_details_os_last!="Media Opacity" && $ar_details_os_last!="Artifact" ){
		$elem_details_os_other=$ar_details_os_last;
		}
		
		$elem_discarea_os=$row["disc_area_os"]; 
		$elem_discsize_os_Large=strpos($row["disc_size_os"],"Large")!==false ? "Large" : ""; 
		$elem_discsize_os_Avg=strpos($row["disc_size_os"],"Avg")!==false ? "Avg" : ""; 
		$elem_discsize_os_Small=strpos($row["disc_size_os"],"Small")!==false ? "Small" : ""; 
		$elem_discedema_os_No=strpos($row["disc_edema_os"],"None")!==false ? "None" : ""; 
		$elem_discedema_os_Mild=strpos($row["disc_edema_os"],"Mild")!==false ? "Mild" : ""; 
		$elem_discedema_os_Md=strpos($row["disc_edema_os"],"Mod")!==false ? "Mod" : ""; 
		$elem_discedema_os_Severe=strpos($row["disc_edema_os"],"Severe")!==false ? "Severe" : ""; 
		$elem_discedema_os_Sup=strpos($row["disc_edema_os"],"Superior")!==false ? "Superior" : ""; 
		$elem_discedema_os_Inf=strpos($row["disc_edema_os"],"Inferior")!==false ? "Inferior" : ""; 
		$elem_rnfl_os_Avg=$row["rnfl_os"]; 
		
		$ar_contour_overall_os = explode(",", $row["contour_overall_os"]);
		$ar_contour_overall_os=array_map('trim',$ar_contour_overall_os);
		$elem_contour_overall_os_NL=in_array("NL",$ar_contour_overall_os) ? "NL" : ""; 
		$elem_contour_overall_os_Thin=in_array("Thin",$ar_contour_overall_os) ? "Thin" : ""; 
		$elem_contour_overall_os_VeryThin=in_array("Very Thin",$ar_contour_overall_os) ? "Very Thin" : ""; 
		$elem_contour_overall_os_Thick=in_array("Thick",$ar_contour_overall_os) ? "Thick" : ""; 
		$elem_contour_overall_os_BL=in_array("Borderline",$ar_contour_overall_os) ? "Borderline" : ""; 
		
		$ar_contour_superior_os = explode(",", $row["contour_superior_os"]);
		$ar_contour_superior_os=array_map('trim',$ar_contour_superior_os);
		$elem_contour_superior_os_NL=in_array("NL",$ar_contour_superior_os) ? "NL" : ""; 
		$elem_contour_superior_os_Thin=in_array("Thin",$ar_contour_superior_os) ? "Thin" : ""; 
		$elem_contour_superior_os_VeryThin=in_array("Very Thin",$ar_contour_superior_os) ? "Very Thin" : ""; 
		$elem_contour_superior_os_Thick=in_array("Thick",$ar_contour_superior_os) ? "Thick" : ""; 
		$elem_contour_superior_os_BL=in_array("Borderline",$ar_contour_superior_os) ? "Borderline" : ""; 
		
		$ar_contour_inferior_os = explode(",", $row["contour_inferior_os"]);
		$ar_contour_inferior_os=array_map('trim',$ar_contour_inferior_os);
		$elem_contour_inferior_os_NL=in_array("NL",$ar_contour_inferior_os) ? "NL" : ""; 
		$elem_contour_inferior_os_Thin=in_array("Thin",$ar_contour_inferior_os) ? "Thin" : ""; 
		$elem_contour_inferior_os_VeryThin=in_array("Very Thin",$ar_contour_inferior_os) ? "Very Thin" : ""; 
		$elem_contour_inferior_os_Thick=in_array("Thick",$ar_contour_inferior_os) ? "Thick" : ""; 
		$elem_contour_inferior_os_BL=in_array("Borderline",$ar_contour_inferior_os) ? "Borderline" : ""; 
		
		$ar_contour_temporal_os = explode(",", $row["contour_temporal_os"]);
		$ar_contour_temporal_os=array_map('trim',$ar_contour_temporal_os);
		$elem_contour_temporal_os_NL=in_array("NL",$ar_contour_temporal_os) ? "NL" : ""; 
		$elem_contour_temporal_os_Thin=in_array("Thin",$ar_contour_temporal_os) ? "Thin" : ""; 
		$elem_contour_temporal_os_VeryThin=in_array("Very Thin",$ar_contour_temporal_os) ? "Very Thin" : "";
		$elem_contour_temporal_os_Thick=in_array("Thick",$ar_contour_temporal_os) ? "Thick" : ""; 
		$elem_contour_temporal_os_BL=in_array("Borderline",$ar_contour_temporal_os) ? "Borderline" : ""; 	
		
		$ar_contour_nasal_os = explode(",", $row["contour_nasal_os"]);
		$ar_contour_nasal_os=array_map('trim',$ar_contour_nasal_os);
		$elem_contour_nasal_os_NL=in_array("NL",$ar_contour_nasal_os) ? "NL" : ""; 
		$elem_contour_nasal_os_Thin=in_array("Thin",$ar_contour_nasal_os) ? "Thin" : ""; 
		$elem_contour_nasal_os_VeryThin=in_array("Very Thin",$ar_contour_nasal_os) ? "Very Thin" : ""; 
		$elem_contour_nasal_os_Thick=in_array("Thick",$ar_contour_nasal_os) ? "Thick" : ""; 
		$elem_contour_nasal_os_BL=in_array("Borderline",$ar_contour_nasal_os) ? "Borderline" : ""; 
		
		$ar_contour_gcc_os = explode(",", $row["contour_gcc_os"]);
		$ar_contour_gcc_os=array_map('trim',$ar_contour_gcc_os);
		$elem_contour_gcc_os_NL=in_array("NL",$ar_contour_gcc_os) ? "NL" : ""; 
		$elem_contour_gcc_os_Thin=in_array("Thin",$ar_contour_gcc_os) ? "Thin" : ""; 
		$elem_contour_gcc_os_VeryThin=in_array("Very Thin",$ar_contour_gcc_os) ? "Very Thin" : ""; 
		$elem_contour_gcc_os_Thick=in_array("Thick",$ar_contour_gcc_os) ? "Thick" : ""; 
		$elem_contour_gcc_os_BL=in_array("Borderline",$ar_contour_gcc_os) ? "Borderline" : ""; 
		
		$elem_symmertric_os_Yes=($row["symmetric_os"]=="Yes") ? "Yes" : ""; 
		$elem_symmertric_os_No=($row["symmetric_os"]=="No") ? "No" : ""; 
		$elem_interpret_systhesis_os=$row["synthesis_os"]; 
		
		$elem_gpa_os_No=($row["gpa_os"]=="No") ? "No" : "";
		$elem_gpa_os_pos=($row["gpa_os"]=="Possible") ? "Possible" : "";
		$elem_gpa_os_lp=($row["gpa_os"]=="Like Progression") ? "Like Progression" : "";
		
		/*$elem_interpret_notes_os=$row["notes_os"]; 
		$elem_interpret_stable_os=$row["stable_os"]; 
		$elem_interpret_improved_os=$row["improved_os"]; 
		$elem_interpret_worse_os=$row["worse_os"]; 
		$elem_comments_os=$row["comments_os"];
		*/
		
		$elem_testTime=$row["testTime"];
	
		$elem_improve=$row["improve"];
	
		$elem_worse=$row["worse"];
	
		$elem_dilated=$row["dilated"];
		
		
		$elem_interpretation_OD = $row["interpretation_OD"];
		$elem_interpretation_OS = $row["interpretation_OS"];
		$elem_comments_interp = $row["comments_interp"];
		$elem_glaucoma_stage_opt_OD = $row["glaucoma_stage_opt_OD"];
		$elem_glaucoma_stage_opt_OS = $row["glaucoma_stage_opt_OS"];
		$elem_plan = $row["plan"];
		//$elem_repeatTestNxtVstEye = $row["repeatTestNxtVstEye"];
		$elem_repeatTestVal1 = $row["repeatTestVal1"];
		$elem_repeatTestVal2 = $row["repeatTestVal2"];
		$elem_repeatTestEye = $row["repeatTestEye"];
		
		//$elem_comments_plan = $row["comments_plan"];
		$forum_procedure = $row['forum_procedure'];
		
		$elem_verti_cd_od = $row['verti_cd_od'];
		$elem_verti_cd_os = $row['verti_cd_os'];
	}

	//Performed Id
	if(empty($elem_performedBy) && (($userType == 1 || $userType == 12) || ($userType == 3))){
		$elem_performedBy = $logged_user;
	}
	
	//Interpreted By
	if(empty($elem_physician)){
		if($userType == '1'){
			$elem_phyName_order = $logged_user;
		}
	}

	//Current Performed by logged in
	$elem_performedByCurr = "";
	if(($userType == 1 || $userType == 12) || ($userType == 3)){
		$elem_performedByCurr = (empty($elem_performedBy) || (($userType == 3))) ? $logged_user : $elem_performedBy;
	}
	
	//Super bill init() --
	$superLen = "1";
	$sb_testName = "OCT-RNFL";
	
	//Cpt Code Desc
	$thisCptDescSym = "OCT-RNFL";

	//Prev + Next Records --
	$tmp = getDateFormatDB($elem_examDate);//Exam Date
	$tstPrevId = $oChartTestPrev->getPrevId($tmp,$oct_pkid);//getPervId
	$tstNxtId = $oChartTestPrev->getNxtId($tmp,$oct_pkid);//getNextId
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title><?php echo $this_test_screen_name;?></title>
<link href="<?php echo $library_path; ?>/css/tests.css?<?php echo filemtime('../../library/css/tests.css');?>" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/css/common.css?<?php echo filemtime('../../library/css/common.css');?>" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/css/jquery-ui.min.css" rel="stylesheet">
<!-- Bootstrap -->
<link href="<?php echo $library_path; ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css">
<!-- Bootstrap Selctpicker CSS -->
<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
<!-- Messi Plugin for fancy alerts CSS -->
<!-- DateTime Picker CSS -->
<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>
<link href="<?php echo $library_path; ?>/css/remove_checkbox.css" rel="stylesheet" type="text/css">
<?php if($callFromInterface != 'admin'){?>
	<link href="<?php echo $GLOBALS['webroot'];?>/library/css/workview.css" rel="stylesheet">
	<link href="<?php echo $GLOBALS['webroot'];?>/library/css/superbill.css" rel="stylesheet">
	<link href="<?php echo $GLOBALS['webroot'];?>/library/lightbox/lightbox.css" rel="stylesheet">
    <link href="<?php echo $library_path; ?>/css/epost.css" rel="stylesheet">
<?php }?>
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]--> 

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery-ui.min.1.11.2.js"></script>
<!-- jQuery's Date Time Picker -->
<script src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js" type="text/javascript" ></script>
<!-- Bootstrap -->
<script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>

<!-- Bootstrap Selectpicker -->
<script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
<script src="<?php echo $library_path; ?>/js/jquery.mCustomScrollbar.concat.min.js"></script> 
<?php if($callFromInterface != 'admin'){?>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/icd10_autocomplete.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/js_gen.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/typeahead.js"></script>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/superbill.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/lightbox/lightbox.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/epost.js"></script>
<?php }?>
<script src="<?php echo $library_path; ?>/js/common.js?<?php echo filemtime('../../library/js/common.js');?>" type="text/javascript"></script>
<script src="<?php echo $library_path; ?>/js/tests.js?<?php echo filemtime('../../library/js/tests.js');?>" type="text/javascript"></script>
<script src="<?php echo $library_path; ?>/messi/messi.js" type="text/javascript"></script>
<script type="text/javascript">
var imgPath 		= "<?php echo $GLOBALS['webroot'];?>";
var elem_per_vo 	= "<?php echo $elem_per_vo;?>";
var zPath			= "<?php echo $GLOBALS['rootdir'];?>";
var JS_WEB_ROOT_PATH 	= "<?php echo $GLOBALS['webroot']; ?>";
var arTestCpts=<?php echo json_encode($arrTestCpts);?>;

<?php if($_GET['afterSaveinPopup']=='yes'){?>
	$(document).ready(function(e) {
		top.fAlert('Test has been saved.');
		<?php if($_GET['doNotShowRightSide']!='yes'){?>
		if(typeof(window.opener.top.fmain)!='undefined'){
			if(typeof(window.opener.top.fmain.$) != 'undefined' && window.opener.top.fmain.$("#sliderRight").length>0){
			window.opener.top.fmain.$("#sliderRight").attr("attrFilled",0);
			if(typeof(window.opener.top.fmain.showChartNotesTree)!='undefined'){
				window.opener.top.fmain.showChartNotesTree("1");
			}
			}
			
			//
			if(typeof(window.opener.top.fmain.$) != 'undefined' && window.opener.top.fmain.$("#dv_oct_rnfl").length>0 && typeof(window.opener.top.fmain.loadExamsSummary)!="undefined"){  window.opener.top.fmain.loadExamsSummary("vf_oct_gl");	}
		}
		setTimeout(function(){window.close();},1500);
		<?php }?>
    });
<?php }?>


$(document).ready(function(e) {
	top.frm_submited=0;
	$("input[id^='elem_contour_'],input[id^='elem_symmertric_'],input[id^='elem_gpa_']").click(function(){
		var cid = ""+this.id;		
		var ar = cid.split("_");
		ar.length = ar.length-1;		
		var did = ar.join("_");
		if ($(this).is(":checked")) {
			var group = "input:checkbox[id^='"+did+"_']";
			$(group).prop("checked", false);
			$(this).prop("checked", true);
		}else {
			$(this).prop("checked", false);
		}
	});
	
   $("textarea").bind("focus", function(event){if(!$(this).hasClass("ui-autocomplete-input")){cn_ta1_no_assess(this, "");};});
	<?php if($callFromInterface != 'admin' && (!isset($_GET["tId"]) || trim($_GET["tId"]))==''){?>
	if(typeof(fillInterpretationProfileData)=='function' && typeof(document.forms[0].sel_interpretation_profile)!='undefined'){
		if(document.forms[0].sel_interpretation_profile.value!='' && document.forms[0].sel_interpretation_profile.value != '0')
		fillInterpretationProfileData(document.forms[0].sel_interpretation_profile.value);
	}
	<?php
	}?>
});


<?php if($callFromInterface != 'admin'){?>
	//Test id
	var t_nxtid			= "<?php echo $tstNxtId;?>";
	var t_previd		= "<?php echo $tstPrevId;?>";
	
	function printTest()
	{
		var tId = "<?php echo $_GET["tId"];?>";
		window.open('../../library/html_to_pdf/createPdf.php?op=l&onePage=false&file_location=<?php echo $html_file_name;?>&mergePDF=1&name=test_'+tId+'_pdf&testIds='+tId+'&saveOption=F&image_from=<?php echo $testname; ?>','vf_Rpt','menubar=0,resizable=yes');	
	}

	function saveNfa(){
		if(top.frm_submited==1){ return; }
		top.frm_submited=1;
		<?php if($elem_per_vo == "1"){echo "return;";}?>
		var f = document.test_form_frm;
		var m = "<b>Please fill the following:- </b><br>";
		var err = false;
		//elem_phyName
		if( (f.elem_performedBy.value == "") || (f.elem_performedBy.value == "0") )
		{
			m += "&bull; Performed By (Physician or Technician only)<br>";
			err = true;
		}
	
		if(f.elem_opidTestOrdered.value==""){
			m += '&bull; Order By<br>';
			err = true;
		}
	
		if(err == false){
			if(typeof(opener) == "undefined" && typeof(top.show_loading_image)!="undefined"){  top.show_loading_image("show");}
			f.submit();
		}
		else{
			fAlert(m,'','top.frm_submited=0;');
		}
	}

	// Prev Data
	var oPrvDt;
	function getPrevTestDt(ctid,cdt,ctm,dir){
		getPrevTestDtExe(ctid,cdt,ctm,dir,"OCT-RNFL");
	}

<?php }?>
//Diagnosis
function setDiagOpts(){
	var f = document.test_form_frm;
	var chk=1;
	var ord = f.elem_scanLaserOct;
	var ln=ord.length;
	for(var i=0;i<ln;i++){
		if(ord[i].checked == true){
			chk = ord[i].value;
		}
	}

	var osel = gebi("elem_diagnosis");
	osel.options.length=0;

	var arr = <?php echo $objTests->getDiagOpts("JS",'oct_rnfl',$test_master_id);// getDiagOpts_RNFL("JS");?>;
	//Adde
	var arr2 = arr[chk-1];
	ln = arr2.length;
	osel.options[osel.options.length]=new Option("","");
	for(var i=0; i<ln; i++){
		osel.options[osel.options.length]=new Option(""+arr2[i],""+arr2[i]);
	}

	//Set Super bill CPT Code
	if(chk!=""){
		elem_testCptDesc=""+arTestCpts[chk][1];
		elem_testCptCode=""+arTestCpts[chk][0];
	}
}
function checkDiagnosis(val,wh){
	var str = (typeof(wh) != "undefined") ? "gla_mac" : "diagnosis" ;
	var ar = val.id.indexOf("Od")!=-1 || val.id.indexOf("od")!=-1 ? ["_od","Od"] : ["_os","Os"];	
	var osel = document.getElementById("elem_"+str+ar[0]);
	var otd = document.getElementById("td_"+str+"Other"+ar[1]);
	
	//alert(osel.name +" - "+ otd.id);
	
	if(val.id.indexOf("img") == -1 && osel.value == "Other"){		
		osel.style.display = "none";
		otd.style.display = "inline-block";
	}else{
		osel.style.display = "inline-block";
		otd.style.display = "none";
	}
	if(val.value!=""){
		if(val.id == "elem_gla_mac_od"){
			$("#elem_gla_mac_os").val(val.value);
			checkDiagnosis(document.getElementById("elem_gla_mac_os"),2);
		}
		
		if(val.id == "elem_diagnosis_od" && val.value!="Other")
		$("#elem_diagnosis_os").val(val.value)
		
	}
}

function checkDiagnosisOCTRNFL(val){
	var str = "diagnosis" ;
	var ar = val.id.indexOf("Od")!=-1 || val.id.indexOf("od")!=-1 ? ["_od","Od"] : ["_os","Os"];	
	var osel = document.getElementById("elem_"+str+ar[0]);
	var otd = document.getElementById("td_"+str+"Other"+ar[1]);
	
	//alert(osel.name +" - "+ otd.id);
	
	if(val.id.indexOf("img") == -1 && osel.value == "Other"){		
		osel.style.display = "none";
		otd.style.display = "inline-block";
	}else{
		osel.style.display = "inline-block";
		otd.style.display = "none";
	}
	if(val.value!=""){
		if(val.id == "elem_diagnosis_od")
		$("#elem_diagnosis_os").val(val.value)
	}
}

function selectOs(){
	var elements = document.test_form_frm.elements;
	var eleLen = elements.length;
	for(var i=0;i<eleLen;i++){
		if(elements[i].type == "checkbox"){
			var eleName = elements[i].name;
			if((eleName.indexOf('OD')!= -1) || (eleName.indexOf('od')!= -1) ){			
				var chkStatus = elements[i].checked;
				//eleName = (eleName.indexOf('od')!= -1) ? eleName.replace(/od/, "os") : eleName.replace(/OD/, "OS");
				eleName = (eleName.indexOf('OD')!= -1) ? eleName.replace(/OD/, "OS") : eleName.replace(/od/, "os");
				var eleOs = document.getElementById(eleName);
				if(eleOs){
					eleOs.checked = chkStatus;
				}
			}
		}else if(elements[i].type == "radio"){
			var eleName = elements[i].id;
			if((eleName.indexOf('od')!= -1) || (eleName.indexOf('Od')!= -1) ){				
				var chkStatus = elements[i].checked;
				eleName = (eleName.indexOf('od')!= -1) ? eleName.replace(/od/, "os")  : eleName.replace(/Od/, "Os") ;
				var eleOs = $("input[type=radio][id="+eleName+"]")[0];
				if(eleOs){					

					eleOs.checked = chkStatus;
				}
			}
		}
	}
	document.getElementById('elem_signal_strength_os').value=document.getElementById('elem_signal_strength_od').value;
	document.getElementById('elem_details_os_other').value=document.getElementById('elem_details_od_other').value;
	document.getElementById('elem_discarea_os').value=document.getElementById('elem_discarea_od').value;
	document.getElementById('elem_rnfl_os_Avg').value=document.getElementById('elem_rnfl_od_Avg').value;
	document.getElementById('elem_interpret_systhesis_os').value=document.getElementById('elem_interpret_systhesis_od').value;
	document.getElementById('elem_verti_cd_os').value=document.getElementById('elem_verti_cd_od').value;
	//document.getElementById('elem_interpret_notes_os').value=document.getElementById('elem_interpret_notes_od').value;
	//document.getElementById('elem_comments_os').value=document.getElementById('elem_comments_od').value;
}	

//Set Tests and Reliability fields as per interpretation
function setReli_Test(wh){
	var arrReli = new Array("elem_reliabilityOd","elem_reliabilityOs");
	var arrTestOd = new Array(    "elem_signal_strength_od",    "elem_quality_od_gd" ,    "elem_quality_od_adequate" ,
    "elem_quality_od_poor" ,    "elem_details_od_AlgoFail" ,    "elem_details_od_MediaOpacity" ,
    "elem_details_od_Artifact" ,    "elem_details_od_other" ,    "elem_discarea_od" ,
    "elem_discsize_od_Large" ,    "elem_discsize_od_Avg" ,    "elem_discsize_od_Small", 
    "elem_discedema_od_No" ,    "elem_discedema_od_Mild",     "elem_discedema_od_Md" ,
    "elem_discedema_od_Severe" ,    "elem_discedema_od_Sup" ,    "elem_discedema_od_Inf" ,
    "elem_rnfl_od_Avg" ,    "elem_contour_overall_od_NL" ,    "elem_contour_overall_od_Thin" ,
    "elem_contour_overall_od_VeryThin" ,    "elem_contour_superior_od_NL" ,    "elem_contour_superior_od_Thin", 
    "elem_contour_superior_od_VeryThin" ,    "elem_contour_inferior_od_NL" ,    "elem_contour_inferior_od_Thin", 
    "elem_contour_inferior_od_VeryThin" ,    "elem_contour_temporal_od_NL" ,    "elem_contour_temporal_od_Thin", 
    "elem_contour_temporal_od_VeryThin" ,    "elem_symmertric_od_Yes" ,    "elem_symmertric_od_No" ,    
    "elem_interpret_systhesis_od", "elem_diagnosis_od", "elem_diagnosisOtherOd" ,
     "elem_verti_cd_od",
	"elem_contour_overall_od_Thick",
	"elem_contour_overall_od_BL",
	"elem_contour_superior_od_Thick",
	"elem_contour_superior_od_BL",
	"elem_contour_inferior_od_Thick",
	"elem_contour_inferior_od_BL",
	"elem_contour_temporal_od_Thick",
	"elem_contour_temporal_od_BL",
	"elem_contour_nasal_od_Thick",
	"elem_contour_nasal_od_BL",
	"elem_contour_gcc_od_Thick",
	"elem_contour_gcc_od_BL",
	"elem_gpa_od_No",
	"elem_gpa_od_pos",
	"elem_gpa_od_lp"
    /*,    "elem_interpret_notes_od" ,    "elem_interpret_stable_od" ,
    "elem_interpret_improved_od",     "elem_interpret_worse_od" ,    "elem_comments_od" */
	);
	setReli_Exe(wh,arrTestOd,arrReli);
}

//Insert Time
function insertTime(obj){
	var id = obj.name.replace(/finding/g, "timeStamp");
	var tobj = gebi(id);
	if(tobj) {
		tobj.value = currenttime();
		if(typeof(tobj.onblur) == "function"){	tobj.onblur();	}			
	}
}

</script>
</head>
<body>
<form name="test_form_frm" action="save_tests.php" method="post" style="margin:0px;">
<?php if($callFromInterface != 'admin'){?>
    <input type="hidden" name="elem_saveForm" id="elem_saveForm" value="OCT-RNFL">
    <input type="hidden" name="elem_patientId" id="elem_patientId" value="<?php echo $patient_id;?>">
    <input type="hidden" name="elem_formId" id="elem_formId" value="<?php echo $form_id;?>">
    <input type="hidden" name="elem_tests_name_id" id="elem_tests_name_id" value="<?php echo $test_master_id;?>">
    <input type="hidden" name="hd_oct_mode" value="<?php echo $oct_mode;?>">
    <input type="hidden" id="elem_testId" name="elem_octId" value="<?php echo $test_edid;?>">
    <input type="hidden" name="wind_opn" id="wind_opn" value="0">
    <input type="hidden" name="elem_operatorId" value="<?php echo $elem_operatorId;?>">
    <input type="hidden" name="elem_operatorName" value="<?php echo $elem_operatorName;?>">
    <input type="hidden" name="elem_noP" value="<?php echo $noP;?>">
    <input type="hidden" name="elem_examTime" value="<?php echo $elem_examTime;?>">
    <input type="hidden" name="pop" value="<?php echo $_REQUEST['pop'] ?>">
    <!--the hidden field doNotShowRightSide	is used for mest maneger-->
    <input type="hidden" name="doNotShowRightSide" value="<?php echo $_REQUEST['doNotShowRightSide']; ?>">
    <input type="hidden" name="hidFormLoaded" id="hidFormLoaded" value="0">
    <input type="hidden" name="zeissAction" id="zeissAction" value="">
    <input type="hidden" name="elem_phyName_order" id="elem_phyName_order" value="<?php echo $elem_phyName_order; ?>" data-phynm="<?php echo (!empty($elem_phyName_order)) ? $objTests->getPersonnal3($elem_phyName_order) : "" ; ?>" >
    <?php $flg_interpreted_btn = (!empty($elem_phyName_order) ) ? 1 : 0; ?>
<?php }?>
<div class=" container-fluid">
    <div class="mainarea">
        <div class="row">
            <div class="col-sm-<?php if($callFromInterface != 'admin' && $doNotShowRightSide != 'yes'){echo '10';}else{echo '12';}?>">
               <?php if($callFromInterface != 'admin'){ require_once("test_orderby_inc.php");}?>
                <div class="clearfix"></div>
                <div class="tstopt">
                    <div class="row">
                        <div class="col-sm-7 sitetab tstrstopt">
                            <ul>
                            	<li>Dilated: &nbsp;</li>
                                <li><label><input type="radio" name="elem_dilated" value="Yes" <?php echo ($elem_dilated == "Yes") ? "checked" : "" ; ?>><span class="label_txt">Yes</span></label></li>
                                <li><label><input type="radio" name="elem_dilated" value="No"  <?php echo ($elem_dilated == "No") ? "checked" : "" ; ?>><span class="label_txt">No</span></label></li>
                            </ul>
                        </div>
                        <div class="col-sm-5 siteopt">
                            <div class="tstopt">
                                <div class="row">
                                    <div class="col-sm-3 sitehd">Sites</div>
                                    <div class="col-sm-5 testopt">
                                        <ul>
                                            <li class="ouc"><label><input type="radio" name="elem_scanLaserEye" value="OU" onClick="setReli_Test(this.value)" <?php echo (!$elem_scanLaserEye || $elem_scanLaserEye == "OU") ? "checked" : "" ; ?>><span class="drak_purple_color label_txt">OU</span></label></li>
                                            <li class="odc"><label><input type="radio" name="elem_scanLaserEye" value="OD" onClick="setReli_Test(this.value)" <?php echo ($elem_scanLaserEye == "OD") ? "checked" : "" ; ?>><span class="blue_color label_txt">OD</span></label></li>
                                            <li class="osc"><label><input type="radio" name="elem_scanLaserEye" value="OS" onClick="setReli_Test(this.value)" <?php echo ($elem_scanLaserEye == "OS") ? "checked" : "" ; ?>><span class="green_color label_txt">OS</span></label></li>
                                        </ul>
                                    </div>
                                    <div class="col-sm-4">
                                        <?php
                                            /*Purpose: Add dropdown for procedure codes to be used in Zeiss HL7 message*/
                                            if($callFromInterface != 'admin' && constant("ZEISS_FORUM") == "YES"){
                                                $procedure_opts = $objTests->zeissProcOpts(10);
                                            ?>
                                                <select id="forum_procedure" name="forum_procedure" class="form-control minimal mt5">
                                                    <option value="">-Forum Procedure-</option>
                                                    <?php
                                                        foreach($procedure_opts as $key=>$proc){
                                                            $selected = "";
                                                            if($key==$forum_procedure){$selected='selected="selected"';}
                                                            print '<option '.$selected.' value="'.$key.'">'.$proc.'</optionn>';
                                                        }
                                                    ?>
                                                </select>
                                        <?php }/*End Modification by Pankaj*/?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="technibox">
                    <div class="row">
                        <div class="col-sm-7">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Performed By</label>
                                        <input type="text" id="elem_performedByName" name="elem_performedByName" value="<?php echo $objTests->getPersonnal3($elem_performedBy);?>" class="form-control" readonly onDblClick="setOpNameId(this.name)">
                                        <input type="hidden" id="elem_performedBy" name="elem_performedBy" value="<?php echo $elem_performedByCurr;?>">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Diagnosis OD</label>
                                        <select class="form-control minimal" id="elem_diagnosis_od" name="elem_diagnosis_od" onChange="checkDiagnosis(this)" style="display:<?php echo ($elem_diagnosis_od == "Other") ? "none" : "block" ;?>;">
                                        <option>--Select--</option>
                                        <?php
											$arrDigOpts = $objTests->getDiagOpts($elem_scanLaserOct,'oct_rnfl',$test_master_id); //getDiagOpts_RNFL($elem_scanLaserOct);
											$print_val_dig="";
											foreach($arrDigOpts  as $key=>$val){
												$sel = ($elem_diagnosis_od == $val) ? "SELECTED" : "";
												
												$valTXT=$val;
												if($val=="Other 2degrees GL"){ $valTXT = "Other 2&#176; GL";  }
												if($sel){
													$print_val_dig=$valTXT;
												}
												echo "<option value=\"".$val."\" ".$sel.">".$valTXT."</option>";
											}
										?>
                                        </select>
                                        <div id="td_diagnosisOtherOd" style="display:<?php echo ($elem_diagnosis_od == "Other") ? "inline-block" : "none" ;?>;">
                                            <div class="col-sm-10"><input type="text" name="elem_diagnosisOtherOd" value="<?php echo ($elem_diagnosisOtherOd);?>" class="form-control"></div>
                                            <div class="col-sm-2"><img id="img_diagnosisOtherOd" src="<?php echo $library_path;?>/images/close14.png" title="Change" onClick="checkDiagnosis(this);" style="cursor:hand; padding:0px;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Diagnosis OS</label>
                                        <select class="form-control minimal" id="elem_diagnosis_os" name="elem_diagnosis_os" onChange="checkDiagnosis(this)" style="display:<?php echo ($elem_diagnosis_os == "Other") ? "none" : "block" ;?>;">
                                        <option>--Select--</option>
                                        <?php
											$print_os_val_dig="";
											$arrDigOpts = $objTests->getDiagOpts($elem_scanLaserOct,'oct_rnfl',$test_master_id); //getDiagOpts_RNFL($elem_scanLaserOct);
											foreach($arrDigOpts  as $key=>$val){
												$sel = ($elem_diagnosis_os == $val) ? "SELECTED" : "";
												$valTXT=$val;
												if($val=="Other 2degrees GL"){ $valTXT = "Other 2&#176; GL";  }
												if($sel){$print_os_val_dig=$valTXT;}
												echo "<option value=\"".$val."\" ".$sel.">".$valTXT."</option>";
											}
										?>
                                        </select>
                                        <div id="td_diagnosisOtherOs" style="display:<?php echo ($elem_diagnosis_os == "Other") ? "inline-block" : "none" ;?>;">
                                            <div class="col-sm-10"><input type="text" name="elem_diagnosisOtherOs" value="<?php echo ($elem_diagnosisOtherOs);?>" class="form-control"></div>
                                            <div class="col-sm-2"><img id="img_diagnosisOtherOs" src="<?php echo $library_path;?>/images/close14.png" title="Change" onClick="checkDiagnosis(this);" style="cursor:hand; padding:0px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group pdlft10">
                                <textarea class="form-control" rows="2" style="width:100%; height:55px !important;" id="techComment" name="techComments" placeholder="Technician Comments"><?php echo $elem_techComments;?></textarea>
                            </div>
                        </div>	
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="corporat">
                    <div class="pdlft10">
                        <div class="row">
                            <div class="col-sm-<?php if($callFromInterface != 'admin')echo '6';else echo '7';?>">
                                <ul>
                                    <li class="head">Patient Understanding &amp; Cooperation</li>
                                    <li>
                                    <div class="tstrstopt">
                                    <label><input type="radio" name="elem_ptUnderstanding" value="Good" <?php echo (!$elem_ptUnderstanding || $elem_ptUnderstanding == "Good") ? "checked" : "" ;?>><span class="label_txt">Good</span></label>
                                    <label><input type="radio" name="elem_ptUnderstanding" value="Fair" <?php echo ($elem_ptUnderstanding == "Fair") ? "checked" : "" ;?>><span class="label_txt">Fair</span></label>
                                    <label><input type="radio" name="elem_ptUnderstanding" value="Poor" <?php echo ($elem_ptUnderstanding == "Poor") ? "checked" : "" ;?>><span class="label_txt">Poor</span></label>
                                    </div>
                                    </li>  
                                </ul>
                            </div>
                            <div class="col-sm-2 text-center">
                                <div class="form-inline mt5">
                                <?php if($callFromInterface != 'admin'){?>
                                    <label for="">Pref. Card</label>
                                    <?php echo $objTests->DropDown_Interpretation_Profile($this_test_properties['id']);?>
                                <?php }?>
                                </div>
                            </div>
                            <div class="col-sm-2 text-right">
                            <?php if($callFromInterface != 'admin'){?>
	                            <?php
									$sql = "SELECT count(*) as num FROM oct_rnfl WHERE patient_id = '$patient_id' AND oct_rnfl_id!='$test_edid'  ";
									$row = sqlQuery($sql);
									if($row!=false && $row["num"]>0){
										echo '<button class="btn-value" type="button" onmouseover="inPrvVal()" onclick="inPrvSynthesis(\'OCT-RNFL\',\''.$test_edid.'\')">Prev. Synthesis</button>';
									}
								?>
                            <?php }?>
                            </div>
                            <div class="col-sm-2 text-right">
                            <?php if($callFromInterface != 'admin'){?>
                                <button class="btn-value" type="button" onmouseover="inPrvVal()" onmouseout="inPrvVal(3)" onclick="inPrvVal(1)">Previous Values</button>
                            <?php }?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div>
                    <table class="table table-bordered">
                        <tr>
                          <td colspan="4" class="phyintrhead">Physician Interpretation</td>
                        </tr>
                        <tr>
                            <td  class="tdlftpan"><strong>TEST RESULT</strong></td>
                            <td  class="odstrip">
                                <div class="row">
                                    <div class="col-sm-1">OD</div>
                                    <div class="col-sm-11 text-right">
                                        <div class="plr10 tstrstopt">
                                            <label><input type="radio" name="elem_reliabilityOd" value="Good" <?php echo (!$elem_reliabilityOd || $elem_reliabilityOd == "Good") ? "checked" : "" ;?>><span class="label_txt">Good</span></label>
                                            <label><input type="radio" name="elem_reliabilityOd" value="Fair" <?php echo ($elem_reliabilityOd == "Fair") ? "checked" : "" ;?>><span class="label_txt">Fair</span></label>
                                            <label><input type="radio" name="elem_reliabilityOd" value="Poor" <?php echo ($elem_reliabilityOd == "Poor") ? "checked" : "" ;?>><span class="label_txt">Poor</span></label>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td rowspan="14" align="center" valign="middle" class="bltra"><a href="javascript:selectOs();">BL</a></td>
                            <td class="osstrip">
                                <div class="row">
                                    <div class="col-sm-1">OS</div>
                                    <div class="col-sm-11 text-right">
                                        <div class="plr10 tstrstopt">
                                            <label><input type="radio" name="elem_reliabilityOs" value="Good" <?php echo (!$elem_reliabilityOs || $elem_reliabilityOs == "Good") ? "checked" : "" ;?>><span class="label_txt">Good</span></label>
                                            <label><input type="radio" name="elem_reliabilityOs" value="Fair" <?php echo ($elem_reliabilityOs == "Fair") ? "checked" : "" ;?>><span class="label_txt">Fair</span></label>
                                            <label><input type="radio" name="elem_reliabilityOs" value="Poor" <?php echo ($elem_reliabilityOs == "Poor") ? "checked" : "" ;?>><span class="label_txt">Poor</span></label>
                                        </div>
                                    </div>
                                </div>
                            </td>		
                        </tr>
                        <tr>
                            <td class="tdlftpan">Signal Strength</td>
                            <td class="pd5 tstbx tstrstopt">
                                <select name="elem_signal_strength_od" id="elem_signal_strength_od" class="form-control minimal" style="width:50px;">
									<option value=""></option>
									<?php $SignalStrength="";
										for($i=0;$i<=10;$i++){
											$sel = (($elem_signal_strength_od!="") && $i==$elem_signal_strength_od) ? "selected" : "";
											if($sel){$SignalStrength=$i;}
											echo " <option value=\"".$i."\" ".$sel." >".$i."</option> ";
										}
									?>
								</select>
                            </td>
                            <td class="pd5 tstbx tstrstopt">
                                <select name="elem_signal_strength_os" id="elem_signal_strength_os" class="form-control minimal" style="width:50px;">
									<option value=""></option>
									<?php $SignalStrengthOS="";
										for($i=0;$i<=10;$i++){
											$sel = (($elem_signal_strength_os!="") && $i==$elem_signal_strength_os) ? "selected" : "";
											if($sel){$SignalStrengthOS=$i;}
											echo " <option value=\"".$i."\" ".$sel." >".$i."</option> ";
										}
									?>
								</select>
                            </td>	
                        </tr>
                        <tr>
                            <td class="tdlftpan">Quality</td>
                            <td class="pd5 tstbx tstrstopt">
                                <div class="row">
                                	<div class="col-sm-4"><label><input type="checkbox" name="elem_quality_od_gd" id="elem_quality_od_gd" value="Good" <?php if(!empty($elem_quality_od_gd)){echo "checked";}?>><span class="label_txt">Good</span></label></div>
                                	<div class="col-sm-4"><label><input type="checkbox" name="elem_quality_od_adequate" id="elem_quality_od_adequate" value="Adequate" <?php if(!empty($elem_quality_od_adequate)){echo "checked";}?>><span class="label_txt">Adequate</span></label></div>
                                	<div class="col-sm-4"><label><input type="checkbox" name="elem_quality_od_poor" id="elem_quality_od_poor" value="Poor" <?php if(!empty($elem_quality_od_poor)){echo "checked";}?>><span class="label_txt">Poor</span></label></div>
                                </div>
                            </td>
                            <td class="pd5 tstbx tstrstopt">
                                <div class="row">
                                	<div class="col-sm-4"><label><input type="checkbox" name="elem_quality_os_gd" id="elem_quality_os_gd" value="Good" <?php if(!empty($elem_quality_os_gd)){echo "checked";}?>><span class="label_txt">Good</span></label></div>
                                	<div class="col-sm-4"><label><input type="checkbox" name="elem_quality_os_adequate" id="elem_quality_os_adequate" value="Adequate" <?php if(!empty($elem_quality_os_adequate)){echo "checked";} ?> ><span class="label_txt">Adequate</span></label></div>
                                	<div class="col-sm-4"><label><input type="checkbox" name="elem_quality_os_poor" id="elem_quality_os_poor" value="Poor" <?php if(!empty($elem_quality_os_poor)){echo "checked";} ?> ><span class="label_txt">Poor</span></label></div>
                                </div>
                            </td>	
                        </tr>
						<tr>
                            <td class="tdlftpan">Details</td>
                            <td class="pd5 tstbx tstrstopt">
                                <div class="row">
                                	<div class="col-sm-4"><label><input type="checkbox" name="elem_details_od_AlgoFail" id="elem_details_od_AlgoFail" value="Algorithm Fail" <?php if(!empty($elem_detail_od_AlgoFail)){echo "checked";} ?> ><span class="label_txt">Algorithm Fail</span></label></div>
                                	<div class="col-sm-4"><label><input type="checkbox" name="elem_details_od_MediaOpacity" id="elem_details_od_MediaOpacity" value="Media Opacity" <?php if(!empty($elem_detail_od_MediaOpacity)){echo "checked";} ?> ><span class="label_txt">Media Opacity</span></label></div>
                                	<div class="col-sm-4"><label><input type="checkbox" name="elem_details_od_Artifact" id="elem_details_od_Artifact" value="Artifact" <?php if(!empty($elem_detail_od_Artifact)){echo "checked";} ?> ><span class="label_txt">Artifact</span></label></div>
                                </div>
                            </td>
                            <td class="pd5 tstbx tstrstopt">
                                <div class="row">
                                	<div class="col-sm-4"><label><input type="checkbox" name="elem_details_os_AlgoFail" id="elem_details_os_AlgoFail" value="Algorithm Fail" <?php if(!empty($elem_detail_os_AlgoFail)){echo "checked";} ?> ><span class="label_txt">Algorithm Fail</span></label></div>
                                	<div class="col-sm-4"><label><input type="checkbox" name="elem_details_os_MediaOpacity" id="elem_details_os_MediaOpacity" value="Media Opacity" <?php if(!empty($elem_detail_os_MediaOpacity)){echo "checked";} ?> ><span class="label_txt">Media Opacity</span></label></div>
                                	<div class="col-sm-4"><label><input type="checkbox" name="elem_details_os_Artifact" id="elem_details_os_Artifact" value="Artifact" <?php if(!empty($elem_detail_os_Artifact)){echo "checked";} ?> ><span class="label_txt">Artifact</span></label></div>
                                </div>
                            </td>	
                        </tr>
                        <tr>
                            <td class="tdlftpan">Other</td>
                            <td class="pd5 tstbx tstrstopt">
                                <div class="row"><div class="col-sm-6">
                                <input type="text" name="elem_details_od_other" id="elem_details_od_other" value="<?php echo !empty($elem_details_od_other) ? $elem_details_od_other : ""; ?>" onfocus="if(this.value=='Other')this.value='';" class="form-control">
                                </div></div>
                            </td>
                            <td class="pd5 tstbx tstrstopt">
                                <div class="row"><div class="col-sm-6">
                                <input type="text" name="elem_details_os_other" id="elem_details_os_other" value="<?php echo !empty($elem_details_os_other) ? $elem_details_os_other : ""; ?>" onfocus="if(this.value=='Other')this.value='';" class="form-control">
                                </div></div>
                            </td>	
                        </tr>
                        <tr>
                            <td class="tdlftpan">Disc Area</td>
                            <td class="pd5 tstbx tstrstopt">
                                <div class="row">
                                	<div class="col-sm-6"><input type="text" name="elem_discarea_od" id="elem_discarea_od" value="<?php echo ($elem_discarea_od) ?>" class="form-control"></div>
                                </div>
                            </td>
                            <td class="pd5 tstbx tstrstopt">
                                <div class="row">
                                	<div class="col-sm-6"><input type="text" name="elem_discarea_os" id="elem_discarea_os" value="<?php echo ($elem_discarea_os) ?>" class="form-control"></div>
                                </div>
                            </td>	
                        </tr>
                        <tr>
                            <td class="tdlftpan">Disc Size</td>
                            <td class="pd5 tstbx tstrstopt">
                                <div class="row">
                                	<div class="col-sm-4"><label><input type="checkbox" name="elem_discsize_od_Large" id="elem_discsize_od_Large" value="Large" <?php if(!empty($elem_discsize_od_Large)){echo "checked";} ?> > <span class="label_txt">Large</span></label></div>
                                	<div class="col-sm-4"><label><input type="checkbox" name="elem_discsize_od_Avg" id="elem_discsize_od_Avg" value="Avg" <?php if(!empty($elem_discsize_od_Avg)){echo "checked";} ?> > <span class="label_txt">Avg</span></label></div>
                                	<div class="col-sm-4"><label><input type="checkbox" name="elem_discsize_od_Small" id="elem_discsize_od_Small" value="Small" <?php if(!empty($elem_discsize_od_Small)){echo "checked";} ?> ><span class="label_txt">Small</span></label></div>
                                </div>
                            </td>
                            <td class="pd5 tstbx tstrstopt">
                                <div class="row">
                                	<div class="col-sm-4"><label><input type="checkbox" name="elem_discsize_os_Large" id="elem_discsize_os_Large" value="Large" <?php if(!empty($elem_discsize_os_Large)){echo "checked";} ?> > <span class="label_txt">Large</span></label></div>
                                	<div class="col-sm-4"><label><input type="checkbox" name="elem_discsize_os_Avg" id="elem_discsize_os_Avg" value="Avg" <?php if(!empty($elem_discsize_os_Avg)){echo "checked";} ?> > <span class="label_txt">Avg</span></label></div>
                                	<div class="col-sm-4"><label><input type="checkbox" name="elem_discsize_os_Small" id="elem_discsize_os_Small" value="Small" <?php if(!empty($elem_discsize_os_Small)){echo "checked";} ?> ><span class="label_txt">Small</span></label></div>
                                </div>
                            </td>	
                        </tr>
                        <tr>
                            <td class="tdlftpan">Vertical C:D</td>
                            <td class="pd5 tstbx tstrstopt">
                                <div class="row">
                                	<div class="col-sm-6"><input type="text" name="elem_verti_cd_od" id="elem_verti_cd_od" value="<?php echo ($elem_verti_cd_od) ?>" class="form-control"></div>
                                </div>
                            </td>
                            <td class="pd5 tstbx tstrstopt">
                                <div class="row">
                                	<div class="col-sm-6"><input type="text" name="elem_verti_cd_os" id="elem_verti_cd_os" value="<?php echo ($elem_verti_cd_os) ?>" class="form-control"></div>
                                </div>
                            </td>	
                        </tr>
                        <tr>
                            <td class="tdlftpan">Disc Edema</td>
                            <td class="pd5 tstbx tstrstopt">
                                <div class="row">
                                	<div class="col-sm-3"><label><input type="checkbox" name="elem_discedema_od_No" id="elem_discedema_od_No" value="None" <?php if(!empty($elem_discedema_od_No)){echo "checked";} ?> ><span class="label_txt">None</span></label></div>
                                	<div class="col-sm-3"><label><input type="checkbox" name="elem_discedema_od_Mild" id="elem_discedema_od_Mild" value="Mild" <?php if(!empty($elem_discedema_od_Mild)){echo "checked";} ?> ><span class="label_txt">Mild</span></label></div>
                                	<div class="col-sm-3"><label><input type="checkbox" name="elem_discedema_od_Md" id="elem_discedema_od_Md" value="Mod" <?php if(!empty($elem_discedema_od_Md)){echo "checked";} ?> ><span class="label_txt">Mod</span></label></div>
                                	<div class="col-sm-3"><label><input type="checkbox" name="elem_discedema_od_Severe" id="elem_discedema_od_Severe" value="Severe" <?php if(!empty($elem_discedema_od_Severe)){echo "checked";} ?> ><span class="label_txt">Severe</span></label></div>                                    
                                </div>
                                <div class="row">
                                	<div class="col-sm-3"><label><input type="checkbox" name="elem_discedema_od_Sup" id="elem_discedema_od_Sup" value="Superior" <?php if(!empty($elem_discedema_od_Sup)){echo "checked";} ?>><span class="label_txt">Superior</span></label></div>
                                	<div class="col-sm-3"><label><input type="checkbox" name="elem_discedema_od_Inf" id="elem_discedema_od_Inf" value="Inferior" <?php if(!empty($elem_discedema_od_Inf)){echo "checked";} ?> > <span class="label_txt">Inferior</span></label></div>
                                </div>
                            </td>
                            <td class="pd5 tstbx tstrstopt">
                                <div class="row">
                                	<div class="col-sm-3"><label><input type="checkbox" name="elem_discedema_os_No" id="elem_discedema_os_No" value="None" <?php if(!empty($elem_discedema_os_No)){echo "checked";} ?> ><span class="label_txt">None</span></label></div>
                                	<div class="col-sm-3"><label><input type="checkbox" name="elem_discedema_os_Mild" id="elem_discedema_os_Mild" value="Mild" <?php if(!empty($elem_discedema_os_Mild)){echo "checked";} ?> ><span class="label_txt">Mild</span></label></div>
                                	<div class="col-sm-3"><label><input type="checkbox" name="elem_discedema_os_Md" id="elem_discedema_os_Md" value="Mod" <?php if(!empty($elem_discedema_os_Md)){echo "checked";} ?> ><span class="label_txt">Mod</span></label></div>
                                	<div class="col-sm-3"><label><input type="checkbox" name="elem_discedema_os_Severe" id="elem_discedema_os_Severe" value="Severe" <?php if(!empty($elem_discedema_os_Severe)){echo "checked";} ?> ><span class="label_txt">Severe</span></label></div>                                    
                                </div>
                                <div class="row">
                                	<div class="col-sm-3"><label><input type="checkbox" name="elem_discedema_os_Sup" id="elem_discedema_os_Sup" value="Superior" <?php if(!empty($elem_discedema_os_Sup)){echo "checked";} ?>><span class="label_txt">Superior</span></label></div>
                                	<div class="col-sm-3"><label><input type="checkbox" name="elem_discedema_os_Inf" id="elem_discedema_os_Inf" value="Inferior" <?php if(!empty($elem_discedema_os_Inf)){echo "checked";} ?> > <span class="label_txt">Inferior</span></label></div>
                                </div>
                            </td>	
                        </tr>
                        <tr>
                            <td class="tdlftpan">RNFL</td>
                            <td class="pd5 tstbx tstrstopt">
                                <div class="row">
                                	<div class="col-sm-1">Avg</div>
                                	<div class="col-sm-5"><input type="text" name="elem_rnfl_od_Avg" id="elem_rnfl_od_Avg" value="<?php echo ($elem_rnfl_od_Avg); ?>" class="form-control"></div>
                                	<div class="col-sm-1">&micro;</div>
                                </div>
                            </td>
                            <td class="pd5 tstbx tstrstopt">
                                <div class="row">
                                	<div class="col-sm-1">Avg</div>
                                	<div class="col-sm-5"><input type="text" name="elem_rnfl_os_Avg" id="elem_rnfl_os_Avg" value="<?php echo ($elem_rnfl_os_Avg); ?>" class="form-control"></div>
                                	<div class="col-sm-1">&micro;</div>
                                </div>
                            </td>	
                        </tr>
                        <tr>
                            <td class="tdlftpan" style="line-height:1.8;" valign="top">
                            	<b>Contour</b><br>
                                Overall<br>
                                Superior<br>
                                Inferior<br>
                                Temporal<br>
                                Nasal<br>
                                GCC<br>
                            </td>
                            <td class="pd5 tstbx tstrstopt" valign="top">
                            	<div class="row"><div class="col-sm-3">&nbsp;</div></div>
                                <div class="row">
                                	<div class="col-sm-1"><label><input type="checkbox" name="elem_contour_overall_od_NL" id="elem_contour_overall_od_NL" value="NL" <?php if($elem_contour_overall_od_NL=="NL"){echo "checked";}?>><span class="label_txt">NL</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_contour_overall_od_Thick" id="elem_contour_overall_od_Thick" value="Thick" <?php if($elem_contour_overall_od_Thick=="Thick"){echo "checked";}?>><span class="label_txt">Thick</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_contour_overall_od_Thin" id="elem_contour_overall_od_Thin" value="Thin" <?php if($elem_contour_overall_od_Thin=="Thin"){echo "checked";}?>><span class="label_txt">Thin</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" name="elem_contour_overall_od_BL" id="elem_contour_overall_od_BL" value="Borderline" <?php if($elem_contour_overall_od_BL=="Borderline"){echo "checked";}?>><span class="label_txt">Borderline</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="elem_contour_overall_od_VeryThin" id="elem_contour_overall_od_VeryThin" value="Very Thin" <?php if($elem_contour_overall_od_VeryThin=="Very Thin"){echo "checked";} ?>><span class="label_txt">Very Thin</span></label></div>
                                </div>
                                <div class="row">
                                	<div class="col-sm-1"><label><input type="checkbox" name="elem_contour_superior_od_NL" id="elem_contour_superior_od_NL" value="NL" <?php if($elem_contour_superior_od_NL=="NL"){echo "checked";}?>><span class="label_txt">NL</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_contour_superior_od_Thick" id="elem_contour_superior_od_Thick" value="Thick" <?php if($elem_contour_superior_od_Thick=="Thick"){echo "checked";}?>><span class="label_txt">Thick</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_contour_superior_od_Thin" id="elem_contour_superior_od_Thin" value="Thin" <?php if($elem_contour_superior_od_Thin=="Thin"){echo "checked";}?>><span class="label_txt">Thin</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" name="elem_contour_superior_od_BL" id="elem_contour_superior_od_BL" value="Borderline" <?php if($elem_contour_superior_od_BL=="Borderline"){echo "checked";}?>><span class="label_txt">Borderline</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="elem_contour_superior_od_VeryThin" id="elem_contour_superior_od_VeryThin" value="Very Thin" <?php if($elem_contour_superior_od_VeryThin=="Very Thin"){echo "checked";} ?>><span class="label_txt">Very Thin</span></label></div>
                                </div>
                                <div class="row">
                                	<div class="col-sm-1"><label><input type="checkbox" name="elem_contour_inferior_od_NL" id="elem_contour_inferior_od_NL" value="NL" <?php if($elem_contour_inferior_od_NL=="NL"){echo "checked";}?>><span class="label_txt">NL</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_contour_inferior_od_Thick" id="elem_contour_inferior_od_Thick" value="Thick" <?php if($elem_contour_inferior_od_Thick=="Thick"){echo "checked";}?>><span class="label_txt">Thick</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_contour_inferior_od_Thin" id="elem_contour_inferior_od_Thin" value="Thin" <?php if($elem_contour_inferior_od_Thin=="Thin"){echo "checked";}?>><span class="label_txt">Thin</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" name="elem_contour_inferior_od_BL" id="elem_contour_inferior_od_BL" value="Borderline" <?php if($elem_contour_inferior_od_BL=="Borderline"){echo "checked";}?>><span class="label_txt">Borderline</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="elem_contour_inferior_od_VeryThin" id="elem_contour_inferior_od_VeryThin" value="Very Thin" <?php if($elem_contour_inferior_od_VeryThin=="Very Thin"){echo "checked";} ?>><span class="label_txt">Very Thin</span></label></div>
                                </div>
                                <div class="row">
                                	<div class="col-sm-1"><label><input type="checkbox" name="elem_contour_temporal_od_NL" id="elem_contour_temporal_od_NL" value="NL" <?php if($elem_contour_temporal_od_NL=="NL"){echo "checked";}?>><span class="label_txt">NL</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_contour_temporal_od_Thick" id="elem_contour_temporal_od_Thick" value="Thick" <?php if($elem_contour_temporal_od_Thick=="Thick"){echo "checked";}?>><span class="label_txt">Thick</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_contour_temporal_od_Thin" id="elem_contour_temporal_od_Thin" value="Thin" <?php if($elem_contour_temporal_od_Thin=="Thin"){echo "checked";}?>><span class="label_txt">Thin</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" name="elem_contour_temporal_od_BL" id="elem_contour_temporal_od_BL" value="Borderline" <?php if($elem_contour_temporal_od_BL=="Borderline"){echo "checked";}?>><span class="label_txt">Borderline</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="elem_contour_temporal_od_VeryThin" id="elem_contour_temporal_od_VeryThin" value="Very Thin" <?php if($elem_contour_temporal_od_VeryThin=="Very Thin"){echo "checked";} ?>><span class="label_txt">Very Thin</span></label></div>
                                </div>
                                <div class="row">
                                	<div class="col-sm-1"><label><input type="checkbox" name="elem_contour_nasal_od_NL" id="elem_contour_nasal_od_NL" value="NL" <?php if($elem_contour_nasal_od_NL=="NL"){echo "checked";}?>><span class="label_txt">NL</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_contour_nasal_od_Thick" id="elem_contour_nasal_od_Thick" value="Thick" <?php if($elem_contour_nasal_od_Thick=="Thick"){echo "checked";}?>><span class="label_txt">Thick</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_contour_nasal_od_Thin" id="elem_contour_nasal_od_Thin" value="Thin" <?php if($elem_contour_nasal_od_Thin=="Thin"){echo "checked";}?>><span class="label_txt">Thin</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" name="elem_contour_nasal_od_BL" id="elem_contour_nasal_od_BL" value="Borderline" <?php if($elem_contour_nasal_od_BL=="Borderline"){echo "checked";}?>><span class="label_txt">Borderline</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="elem_contour_nasal_od_VeryThin" id="elem_contour_nasal_od_VeryThin" value="Very Thin" <?php if($elem_contour_nasal_od_VeryThin=="Very Thin"){echo "checked";} ?>><span class="label_txt">Very Thin</span></label></div>
                                </div>
                                <div class="row">
                                	<div class="col-sm-1"><label><input type="checkbox" name="elem_contour_gcc_od_NL" id="elem_contour_gcc_od_NL" value="NL" <?php if($elem_contour_gcc_od_NL=="NL"){echo "checked";}?>><span class="label_txt">NL</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_contour_gcc_od_Thick" id="elem_contour_gcc_od_Thick" value="Thick" <?php if($elem_contour_gcc_od_Thick=="Thick"){echo "checked";}?>><span class="label_txt">Thick</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_contour_gcc_od_Thin" id="elem_contour_gcc_od_Thin" value="Thin" <?php if($elem_contour_gcc_od_Thin=="Thin"){echo "checked";}?>><span class="label_txt">Thin</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" name="elem_contour_gcc_od_BL" id="elem_contour_gcc_od_BL" value="Borderline" <?php if($elem_contour_gcc_od_BL=="Borderline"){echo "checked";}?>><span class="label_txt">Borderline</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="elem_contour_gcc_od_VeryThin" id="elem_contour_gcc_od_VeryThin" value="Very Thin" <?php if($elem_contour_gcc_od_VeryThin=="Very Thin"){echo "checked";} ?>><span class="label_txt">Very Thin</span></label></div>
                                </div>
                            </td>
                            <td class="pd5 tstbx tstrstopt">
                            	<div class="row"><div class="col-sm-3">&nbsp;</div></div>
                                <div class="row">
                                	<div class="col-sm-1"><label><input type="checkbox" name="elem_contour_overall_os_NL" id="elem_contour_overall_os_NL" value="NL" <?php if($elem_contour_overall_os_NL=="NL"){echo "checked";}?>><span class="label_txt">NL</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_contour_overall_os_Thick" id="elem_contour_overall_os_Thick" value="Thick" <?php if($elem_contour_overall_os_Thick=="Thick"){echo "checked";}?>><span class="label_txt">Thick</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_contour_overall_os_Thin" id="elem_contour_overall_os_Thin" value="Thin" <?php if($elem_contour_overall_os_Thin=="Thin"){echo "checked";}?>><span class="label_txt">Thin</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" name="elem_contour_overall_os_BL" id="elem_contour_overall_os_BL" value="Borderline" <?php if($elem_contour_overall_os_BL=="Borderline"){echo "checked";}?>><span class="label_txt">Borderline</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="elem_contour_overall_os_VeryThin" id="elem_contour_overall_os_VeryThin" value="Very Thin" <?php if($elem_contour_overall_os_VeryThin=="Very Thin"){echo "checked";} ?>><span class="label_txt">Very Thin</span></label></div>
                                </div>
                                <div class="row">
                                	<div class="col-sm-1"><label><input type="checkbox" name="elem_contour_superior_os_NL" id="elem_contour_superior_os_NL" value="NL" <?php if($elem_contour_superior_os_NL=="NL"){echo "checked";}?>><span class="label_txt">NL</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_contour_superior_os_Thick" id="elem_contour_superior_os_Thick" value="Thick" <?php if($elem_contour_superior_os_Thick=="Thick"){echo "checked";}?>><span class="label_txt">Thick</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_contour_superior_os_Thin" id="elem_contour_superior_os_Thin" value="Thin" <?php if($elem_contour_superior_os_Thin=="Thin"){echo "checked";}?>><span class="label_txt">Thin</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" name="elem_contour_superior_os_BL" id="elem_contour_superior_os_BL" value="Borderline" <?php if($elem_contour_superior_os_BL=="Borderline"){echo "checked";}?>><span class="label_txt">Borderline</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="elem_contour_superior_os_VeryThin" id="elem_contour_superior_os_VeryThin" value="Very Thin" <?php if($elem_contour_superior_os_VeryThin=="Very Thin"){echo "checked";} ?>><span class="label_txt">Very Thin</span></label></div>
                                </div>
                                <div class="row">
                                	<div class="col-sm-1"><label><input type="checkbox" name="elem_contour_inferior_os_NL" id="elem_contour_inferior_os_NL" value="NL" <?php if($elem_contour_inferior_os_NL=="NL"){echo "checked";}?>><span class="label_txt">NL</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_contour_inferior_os_Thick" id="elem_contour_inferior_os_Thick" value="Thick" <?php if($elem_contour_inferior_os_Thick=="Thick"){echo "checked";}?>><span class="label_txt">Thick</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_contour_inferior_os_Thin" id="elem_contour_inferior_os_Thin" value="Thin" <?php if($elem_contour_inferior_os_Thin=="Thin"){echo "checked";}?>><span class="label_txt">Thin</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" name="elem_contour_inferior_os_BL" id="elem_contour_inferior_os_BL" value="Borderline" <?php if($elem_contour_inferior_os_BL=="Borderline"){echo "checked";}?>><span class="label_txt">Borderline</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="elem_contour_inferior_os_VeryThin" id="elem_contour_inferior_os_VeryThin" value="Very Thin" <?php if($elem_contour_inferior_os_VeryThin=="Very Thin"){echo "checked";} ?>><span class="label_txt">Very Thin</span></label></div>
                                </div>
                                <div class="row">
                                	<div class="col-sm-1"><label><input type="checkbox" name="elem_contour_temporal_os_NL" id="elem_contour_temporal_os_NL" value="NL" <?php if($elem_contour_temporal_os_NL=="NL"){echo "checked";}?>><span class="label_txt">NL</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_contour_temporal_os_Thick" id="elem_contour_temporal_os_Thick" value="Thick" <?php if($elem_contour_temporal_os_Thick=="Thick"){echo "checked";}?>><span class="label_txt">Thick</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_contour_temporal_os_Thin" id="elem_contour_temporal_os_Thin" value="Thin" <?php if($elem_contour_temporal_os_Thin=="Thin"){echo "checked";}?>><span class="label_txt">Thin</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" name="elem_contour_temporal_os_BL" id="elem_contour_temporal_os_BL" value="Borderline" <?php if($elem_contour_temporal_os_BL=="Borderline"){echo "checked";}?>><span class="label_txt">Borderline</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="elem_contour_temporal_os_VeryThin" id="elem_contour_temporal_os_VeryThin" value="Very Thin" <?php if($elem_contour_temporal_os_VeryThin=="Very Thin"){echo "checked";} ?>><span class="label_txt">Very Thin</span></label></div>
                                </div>
                                <div class="row">
                                	<div class="col-sm-1"><label><input type="checkbox" name="elem_contour_nasal_os_NL" id="elem_contour_nasal_os_NL" value="NL" <?php if($elem_contour_nasal_os_NL=="NL"){echo "checked";}?>><span class="label_txt">NL</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_contour_nasal_os_Thick" id="elem_contour_nasal_os_Thick" value="Thick" <?php if($elem_contour_nasal_os_Thick=="Thick"){echo "checked";}?>><span class="label_txt">Thick</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_contour_nasal_os_Thin" id="elem_contour_nasal_os_Thin" value="Thin" <?php if($elem_contour_nasal_os_Thin=="Thin"){echo "checked";}?>><span class="label_txt">Thin</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" name="elem_contour_nasal_os_BL" id="elem_contour_nasal_os_BL" value="Borderline" <?php if($elem_contour_nasal_os_BL=="Borderline"){echo "checked";}?>><span class="label_txt">Borderline</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="elem_contour_nasal_os_VeryThin" id="elem_contour_nasal_os_VeryThin" value="Very Thin" <?php if($elem_contour_nasal_os_VeryThin=="Very Thin"){echo "checked";} ?>><span class="label_txt">Very Thin</span></label></div>
                                </div>
                                <div class="row">
                                	<div class="col-sm-1"><label><input type="checkbox" name="elem_contour_gcc_os_NL" id="elem_contour_gcc_os_NL" value="NL" <?php if($elem_contour_gcc_os_NL=="NL"){echo "checked";}?>><span class="label_txt">NL</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_contour_gcc_os_Thick" id="elem_contour_gcc_os_Thick" value="Thick" <?php if($elem_contour_gcc_os_Thick=="Thick"){echo "checked";}?>><span class="label_txt">Thick</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_contour_gcc_os_Thin" id="elem_contour_gcc_os_Thin" value="Thin" <?php if($elem_contour_gcc_os_Thin=="Thin"){echo "checked";}?>><span class="label_txt">Thin</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" name="elem_contour_gcc_os_BL" id="elem_contour_gcc_os_BL" value="Borderline" <?php if($elem_contour_gcc_os_BL=="Borderline"){echo "checked";}?>><span class="label_txt">Borderline</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="elem_contour_gcc_os_VeryThin" id="elem_contour_gcc_os_VeryThin" value="Very Thin" <?php if($elem_contour_gcc_os_VeryThin=="Very Thin"){echo "checked";} ?>><span class="label_txt">Very Thin</span></label></div>
                                </div>
                            </td>
                    	</tr>
                        <tr>
                            <td class="tdlftpan">Symmetric</td>
                            <td class="pd5 tstbx tstrstopt">
                                <div class="row">
                                	<div class="col-sm-3"><label><input type="checkbox" name="elem_symmertric_od_Yes" id="elem_symmertric_od_Yes" value="Yes" <?php if($elem_symmertric_od_Yes=="Yes"){echo "checked";} ?> ><span class="label_txt">Yes</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="elem_symmertric_od_No" id="elem_symmertric_od_No" value="No" <?php if($elem_symmertric_od_No=="No"){echo "checked";} ?> > <span class="label_txt">No</span></label></div>
                                </div>
                            </td>
                            <td class="pd5 tstbx tstrstopt">
                                <div class="row">
                                	<div class="col-sm-3"><label><input type="checkbox" name="elem_symmertric_os_Yes" id="elem_symmertric_os_Yes" value="Yes" <?php if($elem_symmertric_os_Yes=="Yes"){echo "checked";}?>><span class="label_txt">Yes</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="elem_symmertric_os_No" id="elem_symmertric_os_No" value="No" <?php if($elem_symmertric_os_No=="No"){echo "checked";}?>><span class="label_txt">No</span></label></div>
                                </div>
                            </td>	
                        </tr>
                        <tr>
                            <td class="tdlftpan">GPA</td>
                            <td class="pd5 tstbx tstrstopt">
                                <div class="row">
                                	<div class="col-sm-3"><label><input type="checkbox" name="elem_gpa_od_No" id="elem_gpa_od_No" value="No" <?php if($elem_gpa_od_No=="No"){echo "checked";}?>><span class="label_txt">No</span></label></div>
                                	<div class="col-sm-4"><label><input type="checkbox" name="elem_gpa_od_pos" id="elem_gpa_od_pos" value="Possible" <?php if($elem_gpa_od_pos=="Possible"){echo "checked";}?>><span class="label_txt">Possible</span></label></div>
                                	<div class="col-sm-5"><label><input type="checkbox" name="elem_gpa_od_lp" id="elem_gpa_od_lp" value="Like Progression" <?php if($elem_gpa_od_lp=="Like Progression"){echo "checked";}?>><span class="label_txt">Like Progression</span></label></div>
                                </div>
                            </td>
                            <td class="pd5 tstbx tstrstopt">
                                <div class="row">
                                	<div class="col-sm-3"><label><input type="checkbox" name="elem_gpa_os_No" id="elem_gpa_os_No" value="No" <?php if($elem_gpa_os_No=="No"){echo "checked";}?>><span class="label_txt">No</span></label></div>
                                	<div class="col-sm-4"><label><input type="checkbox" name="elem_gpa_os_pos" id="elem_gpa_os_pos" value="Possible" <?php if($elem_gpa_os_pos=="Possible"){echo "checked";}?>><span class="label_txt">Possible</span></label></div>
                                	<div class="col-sm-5"><label><input type="checkbox" name="elem_gpa_os_lp" id="elem_gpa_os_lp" value="Like Progression" <?php if($elem_gpa_os_lp=="Like Progression"){echo "checked";}?>><span class="label_txt">Like Progression</span></label></div>
                                </div>
                            </td>	
                        </tr>
                        <tr>
                            <td class="tdlftpan"><b>Interpretation</b><br>Synthesis</td>
                            <td class="pd5 tstbx tstrstopt">
                                <div class="row">
                                	<div class="col-sm-12"><textarea class="form-control" rows="2" style="width:100%; height:55px !important;" id="elem_interpret_systhesis_od" name="elem_interpret_systhesis_od"><?php echo $elem_interpret_systhesis_od;?></textarea></div>
                                </div>
                            </td>
                            <td class="pd5 tstbx tstrstopt">
                                <div class="row">
                                	<div class="col-sm-12"><textarea class="form-control" rows="2" style="width:100%; height:55px !important;" id="elem_interpret_systhesis_os" name="elem_interpret_systhesis_os"><?php echo $elem_interpret_systhesis_os;?></textarea></div>
                                </div>
                            </td>	
                        </tr>
						<tr>
                            <td class="tdlftpan">&nbsp;</td>
                            <td class="tstbx tstrstopt">
                            	<div class="row">
                                	<div class="col-sm-3"><label><input type="checkbox" name="elem_stable_OD" value="Stable" <?php if(strpos($elem_interpretation_OD,"Stable")!==false){echo "checked";}?>><span class="label_txt">Stable</span></label></div>
                                    <div class="col-sm-7"><label><input type="checkbox" name="elem_improve_OD" value="Difficult interpretation" <?php if(strpos($elem_interpretation_OD,"Not Improve")!==false||strpos($elem_interpretation_OD,"Difficult interpretation")!==false){echo "checked";}?>><span class="label_txt">Difficult interpretation</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_worse_OD" value="Worse" <?php if(strpos($elem_interpretation_OD,"Worse")!==false){echo "checked";}?>><span class="label_txt">Worse</span></label></div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-7"><label><input type="checkbox" name="elem_likeProgrsn_OD" value="Likely progression" <?php if(strpos($elem_interpretation_OD,"Likely progression")!==false){echo "checked";}?>><span class="label_txt">Likely progression</span></label></div>
                                    <div class="col-sm-5"><label><input type="checkbox" name="elem_possibleProgrsn_OD" value="Possible progression" <?php if(strpos($elem_interpretation_OD,"Possible progression")!==false){echo "checked";}?>><span class="label_txt">Possible progression</span></label></div>
                                </div>
                            </td>
                            <td class="tstbx">&nbsp;</td>
                            <td class="pd5 tstbx tstrstopt">
	                            <div class="row">
                                	<div class="col-sm-3"><label><input type="checkbox" name="elem_stable_OS" value="Stable" <?php if(strpos($elem_interpretation_OS,"Stable")!==false){echo "checked";}?>><span class="label_txt">Stable</span></label></div>
                                    <div class="col-sm-7"><label><input type="checkbox" name="elem_improve_OS" value="Difficult interpretation" <?php if(strpos($elem_interpretation_OS,"Not Improve")!==false||strpos($elem_interpretation_OS,"Difficult interpretation")!==false){echo "checked";}?>><span class="label_txt">Difficult interpretation</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_worse_OS" value="Worse" <?php if(strpos($elem_interpretation_OS,"Worse")!==false){echo "checked";}?>><span class="label_txt">Worse</span></label></div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-7"><label><input type="checkbox" name="elem_likeProgrsn_OS" value="Likely progression" <?php if(strpos($elem_interpretation_OS,"Likely progression")!==false){echo "checked";}?>><span class="label_txt">Likely progression</span></label></div>
                                    <div class="col-sm-5"><label><input type="checkbox" name="elem_possibleProgrsn_OS" value="Possible progression" <?php if(strpos($elem_interpretation_OS,"Possible progression")!==false){echo "checked";}?>><span class="label_txt">Possible progression</span></label></div>
                                </div>
                            </td>
                    	</tr>
                    </table>
                </div>
                <div class="clearfix"></div>
                <div class="tstfot">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="whitebox">
                                <h2>Treatment/Prognosis</h2>
                                <div class="clearfix"></div>
                                <div class="tstrstopt">
                                    <div class="row">
                                        <div class="col-sm-4"><label><input type="checkbox" name="elem_ptInformYPhy" value="Pt informed of results by physician today" <?php if(strpos($elem_plan,"Pt informed of results by physician today")!==false){echo "checked";}?>><span class="label_txt">Pt informed of results by physician today</span></label></div>
                                        <div class="col-sm-3"><label><input type="checkbox" name="elem_2bCallYTech" value="to be called by technician" <?php if(strpos($elem_plan,"to be called by technician")!==false){echo "checked"; }?>><span class="label_txt">to be called by technician</span></label></div>
                                        <div class="col-sm-2"><label><input type="checkbox" name="elem_byLetter" value="by letter" <?php if(strpos($elem_plan,"by letter")!==false){echo "checked";}?>><span class="label_txt">by letter</span></label></div>
                                    	<div class="col-sm-3"><label><input type="checkbox" name="elem_willInfrmNextVisit" value="will inform next visit" <?php if(strpos($elem_plan,"will inform next visit")!==false){echo "checked";}?>><span class="label_txt">will inform next visit</span></label></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4"><label><input type="checkbox" name="elem_contMeds" value="Continue meds" <?php if(strpos($elem_plan,"Continue meds")!==false){echo "checked"; }?>><span class="label_txt">Continue meds</span></label></div>
                                        <div class="col-sm-3"><label><input type="checkbox" name="elem_monitorFind" value="Monitor findings" <?php if(strpos($elem_plan,"Monitor findings")!==false){echo "checked";}?>><span class="label_txt">Monitor findings</span></label></div>
                                        <div class="col-sm-5 form-inline">
                                        	<div class="form-group">
	                                            <label><input type="checkbox" name="elem_repeatTest" value="Repeat test time" <?php if(strpos($elem_plan,"Repeat test time")!==false){echo "checked";}?> ><span class="label_txt">Repeat test </span></label>
                                            </div>
                                        	<div class="form-group">
                                                <select name="elem_repeatTestVal1" class="form-control minimal" style="width:80px;">
                                                    <option value=""></option>
                                                    <option value="Next visit" <?php echo (empty($elem_repeatTestVal1) || $elem_repeatTestVal1 == "Next visit") ? "Selected" : "";?>>Next visit</option>
                                                    <option value="3 mos" <?php echo ($elem_repeatTestVal1 == "3 mos") ? "Selected" : "";?>>3 mos</option>
                                                    <option value="6 mos" <?php echo ($elem_repeatTestVal1 == "6 mos") ? "Selected" : "";?>>6 mos</option>
                                                    <option value="1 year" <?php echo ($elem_repeatTestVal1 == "1 year") ? "Selected" : "";?>>1 year</option>
                                                    <option value="Other" <?php echo ($elem_repeatTestVal1 == "Other") ? "Selected" : "";?>>Other</option>									
                                                </select>
                                            </div>
                                            <div class="form-group">
	                                            <input class="form-control" type="text" name="elem_repeatTestVal2" value="<?php echo ($elem_repeatTestVal2);?>" size="6" >
                                            </div>
                                          	<div class="form-group">
                                                <label><input type="radio" name="elem_repeatTestEye" value="OU" <?php echo ($elem_repeatTestEye == "OU") ? "checked" : "";?>><span class="label_txt">OU</span></label>
                                                <label><input type="radio" name="elem_repeatTestEye" value="OD" <?php echo ($elem_repeatTestEye == "OD") ? "checked" : "";?>><span class="label_txt">OD</span></label>
                                                <label><input type="radio" name="elem_repeatTestEye" value="OS" <?php echo ($elem_repeatTestEye == "OS") ? "checked" : "";?>><span class="label_txt">OS</span></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                    	<div class="col-sm-6">
                                        	<textarea class="form-control" id="elem_comments_od" name="elem_comments_od" rows="2" style="width:100%; height:55px !important;" placeholder="Comments OD"><?php echo (!empty($elem_comments_od))?$elem_comments_od:"";?></textarea>
                                        </div>
                                    	<div class="col-sm-6">
                                        	<textarea class="form-control" id="elem_comments_os" name="elem_comments_os" rows="2" style="width:100%; height:55px !important;" placeholder="Comments OS"><?php echo (!empty($elem_comments_os))?$elem_comments_os:"";?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                
                                <?php if($callFromInterface != 'admin'){
									$sigFolderName = 'test_oct_rnfl';
									require_once("future_appt_interpret_by.php");
								}?>
                            </div>
                            <div class="clearfix"></div>
                            <?php if($callFromInterface != 'admin'){?>
                            <div class="col-sm-12 supperbill">
                                <!-- Superbill -->
                                <div id="superbill">                                   
                                    <script>
                                        $('#superbill').load(zPath+'/chart_notes/onload_wv.php',{
                                                        'elem_action':"GetSuperBill",
                                                        'sb_testName':"<?php echo $sb_testName;?>",
                                                        'thisCptDescSym':"<?php echo $thisCptDescSym;?>",
                                                        'encounterId':"<?php echo $encounterId;?>",
                                                        'test_form_id':"<?php echo $test_form_id;?>"
                                                        });
                                    </script>
                                </div>
                                <!-- Superbill -->
                                <div class="clearfix"></div>
                				<?php echo $objTests->get_zeiss_forum_button('OCT-RNFL',$test_edid,$patient_id);?>
                            </div>
                        	<?php }?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <?php if($callFromInterface != 'admin' && $doNotShowRightSide != 'yes'){
				$test_scan_edit_id_scan = $test_edid;
				require_once("test_saved_list.php");
			}?>
       			<?php if($callFromInterface != 'admin'){?>
				
				<?php if(($elem_per_vo != "1" && $doNotShowRightSide == 'yes') || $_REQUEST['pop']==1){?>
						<?php
							$btnHide="";
							$btnPurgeVal="Purge";
							//Purged
							if(!empty($purged)){
									$btnHide=" hide";	
									$btnPurgeVal="UndoPurge";
							}
						?>
						
						<div class="mainwhtbox" style="padding-left:10px; padding-right:10px;">																	
						<div class="row">
						<div class="col-sm-12 ad_modal_footer text-center" id="module_buttons">
						<?php if($elem_per_vo != "1"){ ?>
						<input type="button" class="btn btn-success" value="<< Previous" id="btnPrev"  onClick="setPrevValues('-1')" />
						<?php if($flg_interpreted_btn){?>
							<input type="button" class="btn btn-success<?php echo $btnHide;?>" id="btn_interpret"  value="Interpreted" onClick="test_interpreted()" />
						<?php }else{?>
							<input type="button" class="btn btn-success<?php echo $btnHide;?>" id="btn_done" value="Done"  onClick="saveNfa()" />
						<?php }?>
						<?php }?>
						<input type="button"  class="btn btn-danger pull-right" value="Cancel" id="Close" onClick="funWinClose();">			

						<?php if($elem_per_vo != "1"){ ?>
							<input type="button"  class="btn btn-success<?php echo $btnHide;?>" value="Reset" id="btnReset" onClick="resetTestExam();" />
						<?php } ?>			

						<input type="button"  class="btn btn-success"  value="ePost" id="btnEPost" onClick="epostpopTest();" />
                        <input type="button" class="btn btn-success" align="bottom" name="btnPrint" id="btnPrint" onclick="printTest();" value="Print"/>
						<?php if($elem_per_vo != "1"){ ?>
							<input type="button"  class="btn btn-success"  value="<?php echo $btnPurgeVal;?>" id="btnPurge" onClick="resetTestExam(1);" />
							<input type="button"  class="btn btn-success<?php echo $btnHide;?>" value="Order" id="save" onClick="saveNfa()" />
							<input type="button"  class="btn btn-success" value="Next >>" id="btnNxtTst" onClick="setPrevValues('+1')"/>
						<?php } ?>

						</div>
					</div>
					</div>			
				<?php } else { ?>
					<?php if($noP=='1'){?>
					<script>
						var btnArr = new Array();
						btnArr["purged"] = "<?php echo $purged; ?>";
						btnArr["elem_per_vo"] = elem_per_vo;
						btnArr["rtpath"] = zPath;
						btnArr["interpreted"] = "<?php echo $flg_interpreted_btn; ?>";
						top.btn_show("OCT-RNFL",btnArr);
					</script>	
					<?php } ?>
				<?php } ?>
				
				
				
				<?php }?>	
        </div>
	</div>	
</div>
</form>
<?php if($callFromInterface != 'admin'){echo $objEpost->getEposts();}?>
<script>
$(document).ready(function(e) {
    $("textarea").each(function(){
        $(this).attr('data-provide','multiple');
        $(this).attr('data-seperator',',');
    });
	$('[data-toggle="tooltip"]').tooltip()
	$("#content-1").mCustomScrollbar({theme:"minimal"});
    //init_page_display();
	var date_global_format = 'm-d-Y';
	if(typeof(top.jquery_date_format)=='string'){
		var date_global_format = top.jquery_date_format;
	}else if(typeof(window.top.opener.top.jquery_date_format)=='string'){
		var date_global_format = window.top.opener.top.jquery_date_format;
	}else if(typeof(window.top.opener.opener.top.jquery_date_format)=='string'){
		var date_global_format = window.top.opener.opener.top.jquery_date_format;
	}
	$('.datePicker').datetimepicker({
		timepicker:false,
		format:date_global_format,
		formatDate:'Y-m-d',
		scrollInput:false
	})
	
});

function staging_info_fun(){
	$('#staging_code_info').toggle();
	tp=(topPosition()+10)+'px';
	$('#staging_code_info').css({top:tp});
}

<?php if($callFromInterface != 'admin'){?>
	top.set_header_title('<?php echo $this_test_screen_name;?>');
	<?php if(($elem_scanLaserEye == "OD") || ($elem_scanLaserEye == "OS")){echo "setReli_Test(\"".$elem_scanLaserEye."\");";}?>
<?php }?>
</script>
<?php
if($callFromInterface != 'admin'){
	//Previous Test div--
	$oChartTestPrev->showdiv();
    if($_GET["tId"]!='')	// position of include file cannot go above than these lines
	{
		include 'test_oct_rfnl_print.php';
	}
}
?>
</body>
</html>