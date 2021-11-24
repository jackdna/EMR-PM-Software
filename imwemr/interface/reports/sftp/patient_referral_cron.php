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
?>
<?php
/*
FILE : patient_recall_result.php
PURPOSE : PATIENT APPOINTMENT RECALL REPORT
ACCESS TYPE : INCLUDED
*/
$ignoreAuth = true;

if($argv[1]){
	$practicePath = trim($argv[1]);
	$_SERVER['REQUEST_URI'] = $practicePath;
}

include_once(dirname(__FILE__)."/../../../config/globals.php");
include_once('../../../library/classes/cls_common_function.php');

$dateFormat= get_sql_date_format();
$phpDateFormat = phpDateFormat();
$curDate = date($phpDateFormat.'_h:i');

$page_data = '';	$printFile= false;

$arrAllSelProcIds=array();
$curDate.='&nbsp;'.date(" h:i A");

$start_date = $end_date= date($phpDateFormat);

$st_date = getDateFormatDB($start_date);
$en_date = getDateFormatDB($end_date);

//GET ALL USERS
$providerRs = imw_query("Select id,fname,mname,lname from users");
$providerNameArr = array();
while($providerResArr = imw_fetch_assoc($providerRs)){
	$id = $providerResArr['id'];
	$uLname = trim($providerResArr['lname']);
	$uFname = trim($providerResArr['fname']);
	$uMname = trim($providerResArr['mname']);
	$providerNameArr[$id] = core_name_format($uLname,$uFname,$uMname);
}

$qry="Select sa.id, sa.sa_doctor_id, sa.sa_patient_id, pd.lname, pd.fname, sa.sa_app_start_date, sa.procedureid, sa.sec_procedureid, sa.tertiary_procedureid, pd.DOB  as 'dob', pd.primary_care 
FROM schedule_appointments sa JOIN patient_data pd 
ON pd.id=sa.sa_patient_id WHERE (sa.sa_app_start_date BETWEEN '$st_date' AND '$en_date')";
if(empty($primaryProviderId)==false){
	$qry.=" AND sa.sa_doctor_id IN(".$primaryProviderId.")";
}
if(empty($facility_name_str)==false){
	$qry.=" AND sa.sa_facility_id IN(".$facility_name_str.")";
}

$rs=imw_query($qry);
while($res=imw_fetch_assoc($rs)){
	$arr_main_result[$res['id']]=$res;
	$date_strtotime=strtotime($res['sa_app_start_date']);
	$arrTemp[$res['sa_patient_id']][$date_strtotime]=$res['id'];

	if($res['procedureid']>0)$arrAllSelProcIds[$res['procedureid']]=$res['procedureid'];
	if($res['sec_procedureid']>0)$arrAllSelProcIds[$res['sec_procedureid']]=$res['sec_procedureid'];
	if($res['tertiary_procedureid']>0)$arrAllSelProcIds[$res['tertiary_procedureid']]=$res['tertiary_procedureid'];
}

	//MAKING OUTPUT DATA
	$dirName='export_files_'.$practicePath;
	if(!is_dir($dirName)){
		mkdir($dirName);
	}
	$date_now = date("Y-m-d"); 
	$date = str_replace("-","",$date_now);
	$fileName = 'bb_imedicware_referral_export_'.$date.'.csv';
	$filePath= $dirName.'/'.$fileName;
	$pfx="|";

	//CSV FILE NAME
	//$csv_file_name = "../../data/".PRACTICE_PATH."/UserId_".$_SESSION['authId']."/tmp/eid_status_".time().'.csv';
	if(file_exists($filePath)){
		unlink($filePath);
	}
	$fp = fopen ($filePath, 'a+');
	//$strData.="INTERNAL_ID".$pfx;
	$strData.="PATIENT_ID".$pfx;
	$strData.="PATIENT_NAME".$pfx;
	$strData.="DATE_OF_BIRTH".$pfx;
	$strData.="DATE_OF_SERVICE".$pfx;
	$strData.="APPOINTMENT_DATE".$pfx;
	$strData.="APPOINTMENT_TYPE".$pfx;
	$strData.="APPOINTMENT_REASON".$pfx;
	$strData.="REFERRAL_SOURCE".$pfx;
	$strData.="ATTENDING_DOCTOR".$pfx;
	$strData.="SOURCE_SYSTEM";//.$pfx;
	//$strData.="EXTRACTED_ON";
	$strData.= "\n";
	$fp=fopen($filePath,'w');
	@fwrite($fp,$strData);
	@fclose($fp);

if(sizeof($arrTemp)>0){
	$printFile=true;
	$arr_new_established=array();
	$str_patients=implode(',', array_keys($arrTemp));
	$qry="Select id, patient_id, date_of_service FROM chart_master_table WHERE patient_id IN(".$str_patients.")";
	$rs=imw_query($qry);
	while($res=imw_fetch_assoc($rs)){
		$chart_dos=strtotime($res['date_of_service']);
		$arr_appt_dos= array_keys($arrTemp[$res['patient_id']]);
		
		foreach($arr_appt_dos as $appt_dos){
			if($chart_dos<$appt_dos){
				$sch_id=$arrTemp[$res['patient_id']][$appt_dos];
				$arr_new_established[$sch_id]='Established';
			}
		}
	}
	
	//GETTING NAME OF SELECTED PROCEDURES
	if(sizeof($arrAllSelProcIds)>0){
		$strAllSelProcIds=implode(',', $arrAllSelProcIds);
		$qry="Select id, proc FROM slot_procedures WHERE id IN(".$strAllSelProcIds.")";
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			$arrProcNames[$res['id']]=$res['proc'];
		}
	}
	
	foreach($arr_main_result as $sch_id => $apptData){
		$tempArrProc=array();
		
		if($apptData['procedureid']>0)$tempArrProc[]=$arrProcNames[$apptData['procedureid']];
		if($apptData['sec_procedureid']>0)$tempArrProc[]=$arrProcNames[$apptData['sec_procedureid']];
		if($apptData['tertiary_procedureid']>0)$tempArrProc[]=$arrProcNames[$apptData['tertiary_procedureid']];
		$strProcNames=implode(', ', $tempArrProc);
		
		$establishedVal = ($arr_new_established[$sch_id]=='Established') ? 'Established' : 'New';
		
		$lName = $apptData['lname'];
		$fName = $apptData['fname'];
		$ptName = core_name_format($lName, $fName);

		//$strData.= "".$pfx; //$sch_id;
		$strData.= $apptData['sa_patient_id'].$pfx;
		$strData.= trim($ptName).$pfx;
		$strData.= $apptData['dob'].$pfx;
		$strData.= $apptData['sa_app_start_date'].$pfx;
		$strData.= $apptData['sa_app_start_date'].$pfx;
		$strData.= trim($establishedVal).$pfx;
		$strData.= trim($strProcNames).$pfx;
		$strData.= trim($apptData['primary_care']).$pfx;
		$strData.= $providerNameArr[$apptData['sa_doctor_id']].$pfx;
		$strData.="BB_IMEDICWARE";//.$pfx;
	//	$strData.=$extracted_on;
		$strData.= "\n";
		$fp=fopen($filePath,"w");
		@fwrite($fp,$strData);
	}
}
fclose($fp);

//UPLOAD FILE ON SERVER
if(file_exists($filePath)){
	$sftp_credentials_set='1';
	//$sftp_strServer = "ssh3.mytelevox.com";
/*	$sftp_strServerIP = "192.168.18.90";
	$sftp_strServerPort = "22";
	$sftp_strServerUsername = "ops";
	$sftp_strServerPassword = "ops@123";
	$remote_directory="/home/ops/jaswant/";*/

	$sftp_strServerIP = "54.156.2.56";
	$sftp_strServerPort = "22";
	$sftp_strServerUsername = "ftpuser";
	$sftp_strServerPassword = "0neb@dpassword!";
	$remote_directory="/file_drop/";
	
	include 'upload.php';
}
?>