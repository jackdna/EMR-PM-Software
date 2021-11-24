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

include_once(dirname(__FILE__)."/../../config/globals.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
include_once('../../library/classes/cls_common_function.php');

$dateFormat= get_sql_date_format();
$phpDateFormat = phpDateFormat();
$curDate = date($phpDateFormat.'_h:i');

$page_data = '';	$printFile= false;

$arrAllSelProcIds=array();
$curDate.='&nbsp;'.date(" h:i A");

if($_REQUEST['start_date']!='' && $_REQUEST['end_date']!=''){
  $start_date = $_REQUEST['start_date'];
  $end_date= $_REQUEST['end_date'];
}else{
	$start_date = date('m-d-Y', strtotime("-30 day"));
	$end_date= date('m-d-Y', strtotime("+365 day"));
}

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

//GET ALL FACILITIES
$qry = "Select id,name from facility";
$rs = imw_query($qry);
$arrAllFacIds=array();
while ($res = imw_fetch_array($rs)) {
	$arrAllFacIds[$res['id']] = $res['name'];
}

$qry="Select sa.id, sa.sa_doctor_id, sa.sa_patient_id, sa.sa_patient_app_status_id, pd.lname, pd.fname, sa.sa_app_start_date, sa.sa_facility_id,
sa.procedureid, sa.sec_procedureid, sa.tertiary_procedureid, pd.DOB  as 'dob', pd.primary_care, pd.primary_care_id  
FROM schedule_appointments sa JOIN patient_data pd 
ON pd.id=sa.sa_patient_id WHERE (sa.sa_app_start_date BETWEEN '$st_date' AND '$en_date')";
$arrReferringPhyId=array();
$rs=imw_query($qry);
$arrApptIds=array();
while($res=imw_fetch_assoc($rs)){
	$arr_main_result[$res['id']]=$res;
	$date_strtotime=strtotime($res['sa_app_start_date']);
	$arrTemp[$res['sa_patient_id']][$date_strtotime][]=$res['id'];

	if($res['procedureid']>0)$arrAllSelProcIds[$res['procedureid']]=$res['procedureid'];
	if($res['sec_procedureid']>0)$arrAllSelProcIds[$res['sec_procedureid']]=$res['sec_procedureid'];
	if($res['tertiary_procedureid']>0)$arrAllSelProcIds[$res['tertiary_procedureid']]=$res['tertiary_procedureid'];
	
	$arrApptIds[$res['id']]=$res['id'];
}

//MAKING OUTPUT DATA
if(!is_dir($dirName)){
	mkdir($dirName);
}
$date_now = date("Y-m-d"); 
$date = str_replace("-","",$date_now);
$fileName = 'bb_imedicware_schedule_extract_'.$date.'.csv';
$filePath= write_html("", $fileName);
$pfx="|";

//CSV FILE NAME
//$csv_file_name = "../../data/".PRACTICE_PATH."/UserId_".$_SESSION['authId']."/tmp/eid_status_".time().'.csv';
if(file_exists($filePath)){
	unlink($filePath);
}
$fp = fopen ($filePath, 'a+');
//$strData.="INTERNAL_ID".$pfx;
$strData.="APPOINTMENT_ID".$pfx;
$strData.="PATIENT_ID".$pfx;
$strData.="PATIENT_NAME".$pfx;
$strData.="APPOINTMENT_DATE".$pfx;
$strData.="BOOKED_DATE".$pfx;
$strData.="APPOINTMENT_REASON".$pfx;
$strData.="APPOINTMENT_TYPE".$pfx;
$strData.="ATTENDING_DOCTOR".$pfx;
$strData.="CANCELLED_FLAG".$pfx;
$strData.="LOCATION".$pfx;
$strData.="BILLING_ENTITY".$pfx;
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

		foreach($arrTemp[$res['patient_id']] as $appt_dos => $arr_dos){
			foreach($arr_dos as $sch_id){
				if($chart_dos<$appt_dos){
					$arr_new_established[$sch_id]='Established';
				}
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
	
	//GETTING APPT MADE DATE
	$arrApptMadeDate=array();
	if(sizeof($arrApptIds)>0){
		$arr_split=array_chunk($arrApptIds, 1500);
		foreach($arr_split as $arr){
			$strApptIds=implode(',',$arr);
			$qry="Select sch_id, status_date FROM previous_status WHERE sch_id IN(".$strApptIds.") ORDER BY id";
			$rs=imw_query($qry);
			while($res=imw_fetch_assoc($rs)){
				$arrApptMadeDate[$res['sch_id']]=$res['status_date'];
			}		
		}
	}

	$strData='';
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
		
		//STATUS CODE
		$status_code='0';
		$db_status=$apptData['sa_patient_app_status_id'];
		if($db_status=='18' || $db_status=='203')$status_code='1';elseif($db_status=='201' || $db_status=='19' || $db_status=='20' || $db_status=='271')$status_code='NULL';


		//$strData.= "".$pfx; //$sch_id;
		$strData.= $sch_id.$pfx;
		$strData.= $apptData['sa_patient_id'].$pfx;
		$strData.= trim($ptName).$pfx;
		$strData.= $apptData['sa_app_start_date'].$pfx;
		$strData.= $arrApptMadeDate[$sch_id].$pfx;
		$strData.= trim($strProcNames).$pfx;
		$strData.= trim($establishedVal).$pfx;
		$strData.= $providerNameArr[$apptData['sa_doctor_id']].$pfx;
		$strData.= $status_code.$pfx;
		$strData.= $arrAllFacIds[$apptData['sa_facility_id']].$pfx;
		$strData.= "BBECPROF".$pfx;
		$strData.="BB_IMEDICWARE";//.$pfx;
	//	$strData.=$extracted_on;
		$strData.= "\n";
		//$fp=fopen($filePath,"w");
		//@fwrite($fp,$strData);
	}
}
$fp=fopen($filePath,"a");
@fwrite($fp,$strData);
fclose($fp);

echo $filePath;
echo '<br><br>Executed for date range : '.$start_date.' - '.$end_date;

//UPLOAD FILE ON SERVER
if(file_exists($filePath) && 5==6){
	
	$sftp_strServerIP = "54.156.2.56";
	$sftp_strServerPort = "22";
	$sftp_strServerUsername = "ftpuser";
	$sftp_strServerPassword = "0neb@dpassword!";
	$remote_directory="/file_drop/";	
	
	$file='';
 	$t_arr= explode('/', $filePath);
	$fileNAME=end($t_arr);
	array_pop($t_arr);
	$dirName= implode('/', $t_arr).'/';
	
	include('Net/SFTP.php');
	/* Change the following directory path to your specification */
	$local_directory = $dirName;
	$remote_directory1 = $remote_directory.'/';//providing physical(full) path
	$file = $fileNAME;

	/* Add the correct FTP credentials below */
	$sftp = new Net_SFTP($sftp_strServerIP,$sftp_strServerPort,'1000');
	if (!$sftp->login($sftp_strServerUsername,$sftp_strServerPassword)){
		//exit('Login Failed');
	} else{
		//echo 'Login Successful';
	}

	if(file_exists($local_directory.$file))	{
		/* Upload the local file to the remote server put('remote file', 'local file'); */
		$success = $sftp->put($remote_directory1.$file, $local_directory.$file, NET_SFTP_LOCAL_FILE);
		//echo "upload physical :".$success;
	}else{
		//echo 'file not found';
	}	
}
?>