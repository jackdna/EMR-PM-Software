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

require_once(dirname(__FILE__).'/../../../config/globals.php');
set_time_limit(0);
include_once($GLOBALS['fileroot'].'/library/classes/Functions.php');//to get save location
include_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');//to get save location
include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');//function to write html
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_page_functions.php');
require_once($GLOBALS['fileroot'].'/library/classes/work_view/Fu.php');
require_once($GLOBALS['fileroot'].'/library/classes/work_view/TestInfo.php');
require_once($GLOBALS['fileroot'].'/interface/patient_info/complete_pt_rec/print_functions_class.php');

//include_once("../Function.inc.php");
//require_once("../../chart_notes/fu_functions.php"); 

//Create iOLink directory if not exists
$iolinkDirPath = data_path().'iOLink';	
if(!is_dir($iolinkDirPath)){		
	mkdir($iolinkDirPath);
} 

$schedule_id = $_REQUEST['sch_id'];
$mode = $_REQUEST['mode'];
if($mode == "send" || $mode == "resync"){
	
	include_once("iolink_face_sheet.php");
	include_once("iolink_ascan.php");
	include_once("iolink_iol_master.php");
	include_once("iolink_gen_health.php");
	//include_once("iolink_history_physical.php");
	//include_once("iolink_sx_planning_sheet.php");
	include_once($GLOBALS['fileroot'].'/interface/chart_notes/sx_planning_sheet_print.php');
	include_once("iolink_ocular_hx.php");
}
//if($data1 == "yes"){
//Getting iASC Link data
$iolinkAdminId = $_SESSION['authId'];
$iolink_connection_setting_id = $_REQUEST["iolink_connection_setting_id"];		
$facility_type_provider=$_REQUEST["facility_type_provider"];

//========================Update facility type provider before sent to iOLink===============================//
$qryfacility_provider="update schedule_appointments set		
						facility_type_provider='".$facility_type_provider."'
						WHERE id = ".$schedule_id;
$rsfacility_provider = imw_query($qryfacility_provider);
//================================================================================================//

$iolinkAndQry = "";
if($iolink_connection_setting_id) {
	$iolinkAndQry = " AND iolink_id = '".$iolink_connection_setting_id."' ";	
} 
$qryGetIOlinkSettings = "select iolink_url 		as iolinkUrl,
						 iolink_url_username 	as iolinkUrlUsername,
						 iolink_url_password 	as iolinkUrlPassword,
						 iolink_practice_name 	as iolinkUrlPracticeName
						 from iolink_connection_settings 
						 WHERE 1=1 ".$iolinkAndQry." ORDER BY iolink_id ASC ";
$rsGetIOlinkSettings = imw_query($qryGetIOlinkSettings);
if(!$rsGetIOlinkSettings){
	echo ("Error : ". imw_error()."<br>".$qryGetIOlinkSettings);
}	
elseif(imw_num_rows($rsGetIOlinkSettings)>0){
	$dir = explode('/',$_SERVER['HTTP_REFERER']);
	//print_r($_SERVER);
	//exit;
	$httpPro = $dir[0];
	if(!$httpPro)
	{
		if($_SERVER['HTTPS']=='on')$httpPro='https:';
		else $httpPro='http:';
	}
	$httpHost = $dir[2];
	//$httpfolder = $dir[3];
	$httpfolder = $web_RootDirectoryName;
	$ip = $_SERVER['REMOTE_ADDR'];
	//$myAddress = $httpPro.'//'.'74.94.52.21'.'/'.$httpfolder;
	$myAddressNew='';
	if((stristr($myExternalIP,':') && !stristr($myExternalIP,':9443'))) {		
		$httpPro = 'http:';	
	}
	if(stristr($myExternalIP,':3601')) {
		$myAddressNew = $httpPro.'//'.$myExternalIP;
	}
	
	$myAddress = $httpPro.'//'.$myExternalIP.'/'.$httpfolder;
	
	$rowGetIOlinkSettings = imw_fetch_array($rsGetIOlinkSettings);			
	extract($rowGetIOlinkSettings);
	$showPrac = "";
	if($iolinkUrlPracticeName) {
		$showPrac = " - ".$iolinkUrlPracticeName;	
	}
	$qryGetIOlinkPatientdetail = "select iolinkPatientId as ioPtId,iolinkPatientWtId as ioPtWtId, iolink_iosync_waiting_id as iAscSyncWtId
								from schedule_appointments
								WHERE id = ".$schedule_id;
	$rsGetIOlinkPatientdetail = imw_query($qryGetIOlinkPatientdetail);
	if($rsGetIOlinkPatientdetail){
		$rowGetIOlinkPatientdetail = imw_fetch_array($rsGetIOlinkPatientdetail);			
		$ioPtId = $rowGetIOlinkPatientdetail['ioPtId'];
		$ioPtWtId = $rowGetIOlinkPatientdetail['ioPtWtId'];
		$iAscSyncWtId = $rowGetIOlinkPatientdetail['iAscSyncWtId'];

	}
	$idocIascSame = "";
	$chk_dt_time = "yes";
	if(constant("IDOC_IASC_SAME")=="YES") {
		$idocIascSame = "yes";
		$chk_dt_time = "";
	}
	$cur = curl_init();
	$url = $iolinkUrl;
	$postArr=array();
	$postArr['userName'] 		= $iolinkUrlUsername;
	$postArr['password'] 		= $iolinkUrlPassword;
	$postArr['downloadForm'] 	= 'NO';
	$postArr['iolinkSync'] 		= 'yes';
	$postArr['mode'] 			= $mode;
	$postArr['schedule_id'] 	= $schedule_id;
	$postArr['sa_date'] 		= $sa_date;
	$postArr['ioPtId'] 			= $ioPtId;
	$postArr['ioPtWtId'] 		= $ioPtWtId;
	$postArr['iAscSyncWtId'] 	= $iAscSyncWtId;
	$postArr['myAddress'] 		= $myAddress;
	$postArr['myAddressNew'] 	= $myAddressNew;
	$postArr['idocIascSame'] 	= $idocIascSame;
	$postArr['chk_dt_time'] 	= $chk_dt_time;
	curl_setopt($cur,CURLOPT_URL,$url);
	curl_setopt($cur,CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($cur,CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($cur,CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cur,CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($cur,CURLOPT_POSTFIELDS,$postArr); 
	$data = curl_exec($cur);
	if (curl_errno($cur)){
		echo  "Curl Error iDOC to iASC Link: " . curl_error($cur). " ";
	}
	curl_close($cur);				
	//echo $data;
	//exit;
	if($mode == "send" || $mode == "resync"){
		$responseData = explode('@@@@@',$data);
		$iolinkPatientId = $responseData[0];
		$iolinkPatientWtId = $responseData[1];
		$msg = $responseData[2];
		if($msg == 1){
			if(constant("IDOC_IASC_SAME")=="YES") {
				$updtApptSubQry = " iolink_iosync_waiting_id = $iolinkPatientWtId, ";
			}
			$qryUpdateAppointments="update schedule_appointments set patientIOLinkStatus = 1,	
									iolinkPatientId = $iolinkPatientId,
									$updtApptSubQry
									iolinkPatientWtId = $iolinkPatientWtId,				
									iolink_connection_settings_id = '".$iolink_connection_setting_id."' 
									WHERE id = ".$schedule_id;
			$rsqryUpdateAppointments = imw_query($qryUpdateAppointments);
			if($mode == "send"){
				echo "Patient successfully uploaded to iASC Link".$showPrac;
			}
			elseif($mode == "resync"){
				echo "Patient successfully resynchronise to iASC Link".$showPrac;
			}
		 }
		 elseif($msg == 2){
			//echo "This physician is not available in iASC ".$showPrac." for this date";
			echo "Please check availability of schedule or time slot in iASC ".$showPrac."  for this date";
		 }elseif($msg == 0){
			 echo "Error in sending patient to iASC Link".$showPrac." ";
		 }
	}
	elseif($mode == "remove"){
		$responseData = explode('@@@@@',$data);
		$rsDeliolinkPatientId = $responseData[0];
		$rsDeliolinkPatientWtId = $responseData[1];
		if($rsDeliolinkPatientId>0 and $rsDeliolinkPatientWtId >0){
			if($_REQUEST['iolink_ocular_hx_form_id']) {
				$rmovsentDosQry = "UPDATE chart_assessment_plans SET surgical_ocular_hx_sent_dos = '' WHERE form_id='".$_REQUEST['iolink_ocular_hx_form_id']."'";
				$rmovsentDosRes = imw_query($rmovsentDosQry);	
			}
			
			$qryUpdateAppointments="update schedule_appointments set patientIOLinkStatus = 0,										
									iolinkPatientWtId = 0,
									iolinkPatientId = 0,
									iolink_iosync_waiting_id = 0,
									iolink_connection_settings_id = 0,
									iolink_ocular_chart_form_id = 0,
									iolink_ocular_chart_sent_date = ''
									WHERE id = ".$schedule_id;
			$rsqryUpdateAppointments = imw_query($qryUpdateAppointments);
			echo "Patient successfully removed from iASC Link".$showPrac;
		}	
		else{
			echo "Patient already removed from iASC Link or does not exist in iASC Link".$showPrac;
		}	
	}		
}
else{
	echo 'Please configure your iASC Link settings'.$showPrac;
}
$showPrac = "";
?>								