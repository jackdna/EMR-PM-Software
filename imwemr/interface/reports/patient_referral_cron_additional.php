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

/*
FILE : patient_recall_result.php
PURPOSE : PATIENT APPOINTMENT RECALL REPORT
ACCESS TYPE : INCLUDED
*/
ini_set('memory_limit', '3072M');
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

//$start_date = $end_date= date($phpDateFormat);

/* if($_REQUEST['start_date']!='' && $_REQUEST['end_date']!=''){
	$start_date=$_REQUEST['start_date'];
	$end_date=$_REQUEST['end_date'];
}else{
		$start_date = $end_date= date('m-d-Y');
} */

$start_date='01-01-2015';
$end_date= date('m-d-Y');

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

$qry="Select sa.id, sa.sa_doctor_id, sa.sa_patient_id, pd.lname, pd.fname, pd.mname, pd.suffix, pd.street, pd.street2, pd.postal_code, pd.city, pd.state, 
pd.phone_home, pd.phone_biz, pd.phone_home, pd.phone_cell, pd.email, pd.preferr_contact,
sa.sa_app_start_date, sa.procedureid, sa.sec_procedureid, sa.tertiary_procedureid, pd.DOB  as 'dob', pd.primary_care 
FROM schedule_appointments sa JOIN patient_data pd 
ON pd.id=sa.sa_patient_id WHERE (sa.sa_app_start_date BETWEEN '$st_date' AND '$en_date')";


$strData='';
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
	$date_now = date("Y-m-d"); 
	$date = str_replace("-","",$date_now);
	$fileName = 'bb_imedicware_referral_export_analytics_'.$date.'.csv';
	$filePath= write_html("", $fileName);
	$pfx="|";

	//CSV FILE NAME
	//$csv_file_name = "../../data/".PRACTICE_PATH."/UserId_".$_SESSION['authId']."/tmp/eid_status_".time().'.csv';
	if(file_exists($filePath)){
		unlink($filePath);
	}
	$fp = fopen ($filePath, 'a+');
	//$strData.="INTERNAL_ID".$pfx;
	$strData.="PATIENT_ID".$pfx;
	$strData.="PATIENT_LAST_NAME".$pfx;
	$strData.="PATIENT_FIRST_NAME".$pfx;
	$strData.="PATIENT_MIDDLE_NAME".$pfx;
	$strData.="PATIENT_END_TITLE".$pfx;
	$strData.="DATE_OF_BIRTH".$pfx;
	$strData.="PATIENT_ADDRRESS1".$pfx;
	$strData.="PATIENT_ADDRRESS2".$pfx;
	$strData.="PATIENT_CITY".$pfx;
	$strData.="PATIENT_STATE".$pfx;
	$strData.="PATIENT_ZIP".$pfx;
	$strData.="PATIENT_PHONE".$pfx;
	$strData.="PATIENT_EMAIL".$pfx;
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
		$mName = $apptData['mname'];
		//$ptName = core_name_format($lName, $fName);
		$email='';
		if(strpos($apptData['email'],'@'))$email=$apptData['email'];
		
		
		$phone='';
		$phone=($apptData['preferr_contact']=='0' && $apptData['phone_home']!='')? $apptData['phone_home'] : '';
		$phone=($phone=='' && $apptData['preferr_contact']=='1' && $apptData['phone_biz']!='')? $apptData['phone_biz'] : '';
		$phone=($phone=='' && $apptData['preferr_contact']=='2' && $apptData['phone_cell']!='')? $apptData['phone_cell'] : '';
		
		if($phone==''){
			if($apptData['phone_home']!='')$phone=$apptData['phone_home'];
			else if($apptData['phone_biz']!='')$phone=$apptData['phone_biz'];
			else if($apptData['phone_cell']!='')$phone=$apptData['phone_cell'];
		}
				

		//$strData.= "".$pfx; //$sch_id;
		$strData.= $apptData['sa_patient_id'].$pfx;
		$strData.= trim($lName).$pfx;
		$strData.= trim($fName).$pfx;
		$strData.= trim($mName).$pfx;
		$strData.= trim($apptData['suffix']).$pfx;
		$strData.= $apptData['dob'].$pfx;
		$strData.= $apptData['street'].$pfx;
		$strData.= $apptData['street2'].$pfx;
		$strData.= $apptData['city'].$pfx;
		$strData.= $apptData['state'].$pfx;
		$strData.= $apptData['postal_code'].$pfx;
		$strData.= $phone.$pfx;
		$strData.= $email.$pfx;
		$strData.= $apptData['sa_app_start_date'].$pfx;
		$strData.= $apptData['sa_app_start_date'].$pfx;
		$strData.= trim($establishedVal).$pfx;
		$strData.= trim($strProcNames).$pfx;
		$strData.= trim($apptData['primary_care']).$pfx;
		$strData.= $providerNameArr[$apptData['sa_doctor_id']].$pfx;
		$strData.="BB_IMEDICWARE";//.$pfx;
	//	$strData.=$extracted_on;
		$strData.= "\n";
		//$fp=fopen($filePath,"w");
		//@fwrite($fp,$strData);
	}
}

$fp=fopen($filePath,"w");
@fwrite($fp,$strData);
fclose($fp);

//echo '<br><br>Referral extract done from '.$st_date.' to '.$en_date;

//UPLOAD FILE ON SERVER
if(file_exists($filePath)){
	
	$sftp_strServerIP = "sftp.constantanalytics.com";
	$sftp_strServerPort = "22";
	$sftp_strServerUsername = "TKhetarpaul";
	//$sftp_strServerPassword = "8Almonds4Paper3Hats";
	//$sftp_strServerPassword = "Rodman68Pistons4Hydrogen3"; //OLD
	$sftp_strServerPassword = "Blue9Fish12Tree7"; // Changed on 30-06-2020	
	$remote_directory="/ECP_OPH_Data";
	
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