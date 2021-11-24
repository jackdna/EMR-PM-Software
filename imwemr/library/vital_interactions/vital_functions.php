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

set_time_limit(0);
$ignoreAuth = true;
//code to get practice name and provide it to global file
if($argv[1]){
	$practicePath = trim($argv[1]);
	$_SERVER['REQUEST_URI'] = $practicePath;
	$_SERVER['HTTP_HOST'] = $practicePath;
	//if we are receiving second parameter as valid interger value then that means file is being called to send data dump
	if(isset($argv[2]) && is_numeric($argv[2]) && ($argv[2]>0 && $argv[2]<=1000))
	{
		$_REQUEST["VITAL_DATA_DUMP"]=true;
		$_REQUEST["VITAL_DATA_DUMP_RANGE"]= $argv[2];
	}
}
require_once(dirname(__FILE__).'/../../config/globals.php');/*
if($include_sch_class==true){
	require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');
	//scheduler object
	$obj_scheduler = new appt_scheduler;
}*/

if(defined('PRACTICE_PATH')==false)die('Practice name not provided');

error_reporting(0);
ini_set('display_errors', 0);

/*Check availability of Vital FTP credentials*/
if( !defined('VITAL_HOST') || trim(VITAL_HOST) == '' ||
    !defined('VITAL_USER') || trim(VITAL_USER) == '' ||
	!defined('VITAL_PASS') || trim(VITAL_PASS) == ''
   )
{
	print 'Please provide credentials.';
	exit;
}

/*Date range for data export*/
$date1 = '';
$date2 = '';

if( isset($_REQUEST['tdate']) && trim($_REQUEST['tdate']) != '' )
{
	$date1 = trim($_REQUEST['tdate']);
	$date2 = trim($_REQUEST['tdate']);
}
else
{
	$date1 = date('Y-m-d', strtotime('-2 days'));
	if(constant('VITAL_DATA_PUSH_RANGE'))
	{
		$date2 = date('Y-m-d', strtotime(constant('VITAL_DATA_PUSH_RANGE')));
	}
	else
	{
		$date2 = date('Y-m-d', strtotime('+365 days'));
	}
}

if($_REQUEST["VITAL_DATA_DUMP"]==true)//if we are about to push data dump then overwrite existing date range with data dump data range
{
	$date1 = date('Y-m-d', strtotime("-".$_REQUEST["VITAL_DATA_DUMP_RANGE"]." days"));
	$date2 = date('Y-m-d');
}
/*Flag for prompt to download the file*/
$dlFile = (isset($_REQUEST['dl']) && (bool)$_REQUEST['dl']==true);

/*File Saving Directory*/
$fileDir = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/vital_appointments";
if( !is_dir($fileDir) )
{
	mkdir( $fileDir, 0755, true );
	chown( $fileDir, 'apache' );
}

/*Archieve Directory*/
if( !is_dir($fileDir.'/Archive') )
{
	mkdir( $fileDir.'/Archive', 0755, true );
	chown( $fileDir.'/Archive', 'apache' );
}

/*
 * Name: processData(&$item, $key);
 * Description: Used in array_walk, for formating the array to be inserted in CSV file. 
*/
function processData(&$item, $key)
{
	$item = '"'.$item.'"';
}

/*
 * Name: FUNC_uploadFile($csvFileName, $fileDir);
 * @csvFileName: Name of CSV file
 * @fileDir: Location of file (file root path)
 * Description: Used for uploading CSV file to the destination server. 
*/
function FUNC_uploadFile($csvFileName, $fileDir)
{
	$strServer = VITAL_HOST;
	$strServerIP = VITAL_HOST;
	$strServerPort = "22";
	$strTimeOut=1000;//seconds
	
	$strServerUsername = VITAL_USER;
	$strServerPassword = VITAL_PASS;
		
	$remote_directory = '/files';
	
	/* Pear package */
	include('Net/SFTP.php');
	define('NET_SFTP_LOGGING', NET_SFTP_LOG_COMPLEX); // or NET_SFTP_LOG_SIMPLE
	define('NET_SSH2_LOGGING', NET_SSH2_LOG_COMPLEX);
	//define('NET_SSH2_LOGGING', 2);
	
	/* Add the correct FTP credentials below */
	$sftp = new Net_SFTP($strServerIP,$strServerPort,$strTimeOut);
	if( !$sftp->login($strServerUsername,$strServerPassword) )
	{
		echo"\n Login Failed \n sftp error log \n";
		print_r($sftp->getSFTPErrors());
		echo"\n last sftp error \n";
		print_r($sftp->getLastSFTPError());
		echo"\n sftp log \n";
		print_r($sftp->getSFTPLog());
		exit();
	}
	else
	{
		echo "\n Login Success";
	}
	
	try
	{
		//now check is that folder exist on server if not then create it
		if( !$sftp->file_exists($remote_directory) )
		{
			//create directory
			$sftp->mkdir($remote_directory);
		}
		
	}
	catch (Exception $e)
	{
		echo "\n Caught exception: ",  $e->getMessage();
	}
	
	/* Upload the local file to the remote server 
	   put('remote file', 'local file');
	 */
	if(file_exists($fileDir.'/'.$csvFileName))
	{
		echo"\n Trying to upload $fileDir/$csvFileName on ".$remote_directory.'/'.$csvFileName;
		$success = $sftp->put($remote_directory.'/'.$csvFileName, $fileDir.'/'.$csvFileName, NET_SFTP_LOCAL_FILE);
		
		echo "\n Upload :".$success;
		
		if($success)
		{
			rename($fileDir.'/'.$csvFileName, $fileDir.'/Archive/'.$csvFileName);//move that file to archieve folder
		}
	}
	else
	{
		echo "\n ".$csvFileName." file not exist";
	}
}

/*
 * Name: FUNC_downloadFile($csvFileName, $fileDir);
 * @csvFileName: Name of CSV file
 * @fileDir: Location of file (file root path)
 * Description: Used for uploading CSV file to the destination server. 
*/
function FUNC_downloadFile($fileDir)
{
	$strServer = VITAL_HOST;
	$strServerIP = VITAL_HOST;
	$strServerPort = "22";
	$strTimeOut=1000;//seconds
	
	$strServerUsername = VITAL_USER;
	$strServerPassword = VITAL_PASS;
		
	$remote_directory = '/files/status';
	$remote_directory_archive = '/files/status/archive';
	//$remote_directory_archive = '/files/old';
	
	/* Pear package */
	include('Net/SFTP.php');
	
	/* Add the correct FTP credentials below */
	$sftp = new Net_SFTP($strServerIP,$strServerPort,$strTimeOut);
	if( !$sftp->login($strServerUsername,$strServerPassword) )
	{
		exit('Login Failed');
	}
	else
	{
		echo 'Login Success.<br>';
	}
	
	try
	{
		//now check is that folder exist on server if not then create it
		if( !$sftp->file_exists($remote_directory_archive) )
		{
			//create directory
			$sftp->mkdir($remote_directory_archive);
		}
		
	}
	catch (Exception $e)
	{
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	
	
	require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');
	//scheduler object
	$obj_scheduler = new appt_scheduler;
	
	foreach($sftp->rawlist($remote_directory) as $csvFileName => $attrs) {
		if ($attrs['type'] == NET_SFTP_TYPE_REGULAR) //make sure its a regular file
		{ 
			if(substr(strrchr($csvFileName,'.'),1)=='csv')//make sure its a csv file
			{
				$csv_content=$sftp->get($remote_directory.'/'.$csvFileName);//to read
				//write file on local system to read it as csv
				$local_csv_file=$fileDir.'/vital_updates.csv';
				//delete if any file exist
				unlink($local_csv_file);
				$fp = fopen($local_csv_file, 'w');
				fwrite($fp, $csv_content);
				fclose($fp);
				updateStatus($local_csv_file, $obj_scheduler);
				unset($csv_content);
				$ret=$sftp->rename($remote_directory.'/'.$csvFileName, $remote_directory_archive.'/'.$csvFileName);
				if($ret==false){
					//in case of same name file does exist on target folder archive rename will not work so we do using delete on precautionary basis
					$sftp->delete($remote_directory.'/'.$csvFileName, false);
				}
			}
		}
	}
	echo"Read Back for $practicePath is completed";
}

/*
 * Name: updateStatus($csvContent);
 * @csvContent: data string received from csv file
 * Description: check csv data to update appointment.
*/
function updateStatus($local_csv_file, $obj_scheduler)
{
	$write_back_comments=true;
	//get comment write back setting from table
	$write_chk=imw_query("select write_back_comments from vital_interactions");
	$write_res=imw_fetch_object($write_chk);
	if(imw_num_rows($write_chk)>0 && $write_res->write_back_comments==0)$write_back_comments=false;
	
	//MAPPING APPT STATUS WITH VITAL APPT STATUS
	$viStsArr[5]=23;		//vi message not received means not confirmed
	//$viStsArr[4]=0;		//vi message received
	//$viStsArr[3]=0;		//vi message sent
	$viStsArr[2][9]=18;	//vi declined by patient
	$viStsArr[2][10]=201;	//vi declined by patient
	$viStsArr[1]=17;	//vi confirmed by patient
	//get appointment ids
	$file = fopen($local_csv_file,"r");
	$key=0;
	while (($data = fgetcsv($file, 1000, ",")) !== FALSE) 
	{
		if($key >0 && trim($data[0])>0){
			json_decode(trim($data[19]),true);
			$apptId=trim($data[0]);//altAppointmentId
			$apptArr[$apptId]=$apptId;
			$csvData[$apptId]['stsCmnt']=trim($data[2]);//apptStatusName
			$csvData[$apptId]['stsCancelId']=trim($data[4]);//apptStatusName
			$csvData[$apptId]['stsReqCmnt']=trim(strip_tags(str_replace('<br>',"\n",$data[6])));//apptRequestStatusName
			
			$csvData[$apptId]['stsId']=trim($data[9]);//iMedicWareStatusId
			$apptDated=$m=$d=$y='';
			$apptDated=trim($data[11]);//appointmentDate
			list($m,$d,$y)=explode('-',$apptDated);
			$csvData[$apptId]['appointmentDate']="$y-$m-$d";
			unset($apptDated,$m,$d,$y);
			$csvData[$apptId]['appointmentTime']=trim($data[12]);//appointmentTime
			$csvData[$apptId]['appointmentTypeId']=trim($data[13]);//appointmentTypeId
			$csvData[$apptId]['doctorId']=trim($data[15]);//doctorId
			$csvData[$apptId]['facilityId']=trim($data[17]);//facilityId
			$csvData[$apptId]['stsNotUpdate']=json_decode(trim($data[19]),true);//statusDoNotUpdateList
			$csvData[$apptId]['stsCompareField']=json_decode(trim($data[20]),true);//compareDataFieldsList
		}
		$key++;
	}
	fclose($file);
	if(sizeof($apptArr)>0)
	{
		$idStr=implode(',',$apptArr);
		//get current fields value  for all appointments to compare with incoming csv field data
		$q=imw_query("select id, sa_patient_app_status_id, sa_doctor_id, sa_facility_id, procedureid, sa_app_start_date, sa_app_starttime from schedule_appointments where id IN($idStr)");
		while($d=imw_fetch_object($q))
		{
			$patientAppSts[$d->id]['apptStatusId']=$d->sa_patient_app_status_id;
			$patientAppSts[$d->id]['appointmentDate']=$d->sa_app_start_date;
			$patientAppSts[$d->id]['appointmentTime']=$d->sa_app_starttime;
			$patientAppSts[$d->id]['appointmentTypeId']=$d->procedureid;
			$patientAppSts[$d->id]['doctorId']=$d->sa_doctor_id;
			$patientAppSts[$d->id]['facilityId']=$d->sa_facility_id;
		}
		unset($idStr);
		foreach($csvData as $apptId=>$subArr)
		{
			//validate future appointment
			/*$appt_dateTime=strtotime($subArr['appointmentDate'].' '.$subArr['appointmentTime']);
			if($appt_dateTime && $appt_dateTime<=time())continue;//skip past appointments*/
			if($subArr['appointmentDate'] && $subArr['appointmentDate']<=date('Y-m-d'))continue;//skip appointments for current or past dates
			
			//validate "status do not update list"
			$status_do_not_update_list= array_combine($subArr['stsNotUpdate']['status_do_not_update_list'],$subArr['stsNotUpdate']['status_do_not_update_list']);
			if(isset($status_do_not_update_list[$patientAppSts[$apptId]['apptStatusId']]))continue;//skip this record if its status in 'status do not update' list
			
			//validate is status equal to current status
			if($subArr['stsId']=='' || $subArr['stsId']==$patientAppSts[$apptId]['apptStatusId'])continue;//skip this record if its status is same or null
			//validate compare data field list
			$compare_data_fields_list= array_combine($subArr['stsCompareField']['compare_data_fields_list'],$subArr['stsCompareField']['compare_data_fields_list']);
			if(sizeof($compare_data_fields_list)>0)
			{
				foreach($compare_data_fields_list as $key=>$val)
				{
					if($patientAppSts[$apptId][$key]!=$subArr[$key])continue 2;//skip this record if compare field value doesn't match
				}
			}
			if($subArr['stsId'])
			{	
				$cmnt=$subArr['stsCmnt'];
				if($subArr['stsReqCmnt'] && !empty($subArr['stsReqCmnt']) && $subArr['stsReqCmnt']!='NULL')$cmnt.='- '.$subArr['stsReqCmnt'];
				$stsCancelId=$subArr['stsCancelId'];

				if($apptId>0)
				{
					$cmnt_q="";
					if($write_back_comments==true)$cmnt_q="sa_comments=CONCAT(sa_comments,' $cmnt'),";
					imw_query("update schedule_appointments set sa_patient_app_status_id='".$subArr['stsId']."', 
					$cmnt_q
					sa_app_time = '".date("Y-m-d H:i:s")."',
					status_update_operator_id='".constant("VITAL_IDOC_USER_ID")."' where id=$apptId");
					//update previous status table
					$obj_scheduler->logApptChangedStatus($apptId, "", "", "", $subArr['stsId'], "", "", constant("VITAL_IDOC_USER"), "STATUS SET VIA VITAL", "", false);
					
					if($subArr['stsId']==18 || $subArr['stsId']==201)
					{
						$q = "SELECT sa_doctor_id, sa_facility_id, sa_app_start_date, sa_app_starttime, sa_app_endtime FROM schedule_appointments WHERE id = '".$apptId."'";
						$r = imw_query($q);	
						$a = imw_fetch_array($r);
						$hv_appt_data=true;
						$sttm = strtotime($a["sa_app_starttime"]);
						$edtm = strtotime($a["sa_app_endtime"]);

						for($looptm = $sttm; $looptm < $edtm; $looptm += (DEFAULT_TIME_SLOT * 60)){
							$edtm2 = $looptm + (DEFAULT_TIME_SLOT * 60);

							$start_loop_time = date("H:i:00", $looptm);
							$end_loop_time = date("H:i:00", $edtm2);

							$q2 = "SELECT id, provider, facility, start_date, start_time, end_time, labels_replaced, l_text, l_show_text FROM scheduler_custom_labels WHERE provider = '".$a["sa_doctor_id"]."' AND facility = '".$a["sa_facility_id"]."' AND start_date = '".$a["sa_app_start_date"]."' AND start_time = '".$start_loop_time."' AND end_time = '".$end_loop_time."'";
							$r2 = imw_query($q2);
							while($row = imw_fetch_assoc($r2)){
								$new_entry = $row["labels_replaced"];
								$l_text = $row["l_show_text"];
								$lbl_record_id = trim($row['id']);
								$lbl_replaced = trim($row['labels_replaced']);
								if(trim($row["labels_replaced"]) != ""){ 
									$arr_lbl_replaced = explode("::", $row["labels_replaced"]);
									if(count($arr_lbl_replaced) > 0){ 
										foreach($arr_lbl_replaced as $this_lbl_replaced){
											$arr_this_replaced2 = explode(":", $this_lbl_replaced);
											if(trim($arr_this_replaced2[0]) == $apptId){ 
												$new_entry = str_replace("::".$arr_this_replaced2[0].":".$arr_this_replaced2[1], "", $row["labels_replaced"]);

												if(trim($row["l_show_text"]) != ""){
													$l_text = $row["l_show_text"]."; ".$arr_this_replaced2[1];
												}else{
													$l_text = $arr_this_replaced2[1];
												}
												$upd22 = "UPDATE scheduler_custom_labels SET l_show_text = '".$l_text."', labels_replaced = '".$new_entry."' WHERE id =	'".$row["id"]."'";
												imw_query($upd22);
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		unset($csvData,$patientAppSts);
	}
}

/*
 * Name: dl_file($csvFileName, $fileDir);
 * @csvFileName: Name of CSV file
 * @fileDir: Location of file (file root path)
 * Description: Prompt File Dowload to the client.
*/
function dl_file($csvFileName, $fileDir)
{
	$content_type = "text/csv";
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	
	header("Cache-Control: private",false);
	header("Content-Description: File Transfer");
	
	header("Content-Type: ".$content_type."; charset=utf-8");
	header("Content-disposition:attachment; filename=\"".$csvFileName."\"");
	
	header("Content-Length: ".@filesize($fileDir.'/'.$csvFileName));
	
	@readfile($fileDir.'/'.$csvFileName) or die("File not found.");
}
