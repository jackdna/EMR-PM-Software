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

require_once(dirname(__FILE__).'/vital_functions.php');

if( $date1 != '' && $date2 != '' )
{
	$query = "SELECT
	`sch`.`id` AS 'appointment_id',
    DATE_FORMAT(`sch`.`sa_app_start_date`, '%m-%d-%Y') AS 'appointment_date',
    DATE_FORMAT(`sch`.`sa_app_starttime`, '%H:%i:%s') AS 'appointment_time',
	`sp`.`acronym` AS 'appointment_type_name',
    `sch`.`procedureid` AS 'appointment_type_id',
    `sch`.`sa_patient_app_status_id` AS 'appointment_status',
    `ss`.`status_name` AS 'appointment_status_name',
    `pd`.`id` AS 'patient_id',
    `pd`.`fname` AS 'patient_first_name',
    `pd`.`lname` AS 'patient_last_name',
    `pd`.`mname` AS 'patient_middle_name',
    `pd`.`email`,
    `pd`.`phone_cell` AS 'patient_cell_phone',
    `pd`.`phone_home` AS 'patient_home_phone',
    `pd`.`phone_biz` AS 'patient_work_phone',
    `pd`.`phone_biz_ext` AS 'patient_work_phone_ext',
    CONCAT(`pd`.`street`,' ',`pd`.`street2`) AS 'patient_address',
    `pd`.`city` AS 'patient_city',
    `pd`.`state` AS 'patient_state',
    CONCAT(`pd`.`postal_code`, `pd`.`zip_ext`) AS 'patient_zip',
    `pd`.`country_code` AS 'patient_country',
    REPLACE(`pd`.`language`, 'Other -- ', '') AS 'patient_language', 
    DATE_FORMAT(`pd`.`DOB`, '%m-%d-%Y') AS 'patient_date_of_birth',
    IF(`pd`.`preferr_contact`=0, 'home_phone', IF(`pd`.`preferr_contact`=1, 'work_phone', IF(`pd`.`preferr_contact`=2, 'cell_phone', ''))) AS 'preffered_communication_method',
	IF(`pd`.`username`='' OR `pd`.`username` IS NULL, `pd`.`temp_key`, '') AS 'portal_key',
    IF(`pd`.`patientStatus`='Other', `pd`.`otherPatientStatus`, `pd`.`patientStatus`) AS 'patient_status',
	`pd`.`pat_account_status` AS 'patient_account_status',
	`acc`.`status_name` AS 'patient_account_status_name',
    `us`.`user_npi` AS 'resource_id',
    CONCAT(`us`.`lname`, ',', `us`.`fname`, ' ', `us`.`mname`) AS 'resource_name',
    `fac`.`id` AS 'facility_id',
    `fac`.`name` AS 'facility_name',
    `fac`.`phone` AS 'facility_phone_primary',
    `fac`.`phone_ext` AS 'facility_phone_ext',
    `fac`.`street` AS 'facility_address',
    `fac`.`city` AS 'facility_city',
     CONCAT(`fac`.`postal_code`, `fac`.`zip_ext`) AS 'facility_zip',
    `fac`.`state` AS 'facility_state'
FROM 
	schedule_appointments sch 
	LEFT JOIN users us ON us.id = sch.sa_doctor_id 
	LEFT JOIN facility fac ON fac.id = sch.sa_facility_id 
	LEFT JOIN patient_data pd ON pd.id = sch.sa_patient_id 
	LEFT JOIN account_status acc ON pd.pat_account_status = acc.id 
	LEFT JOIN slot_procedures sp ON sp.id = sch.procedureid
	LEFT JOIN schedule_status ss ON ss.id = sch.sa_patient_app_status_id
WHERE 
	sch.sa_app_start_date BETWEEN '".$date1."' AND '".$date2."'";
	
	/*AND sch.sa_patient_app_status_id NOT IN(3, 11, 18, 19, 20, 201, 203)*/
	
	$resp = imw_query($query);
	$exportData = ( $resp && imw_num_rows($resp)>0 );
	
	if( $exportData )
	{
		$columnTitles = array('appointment_id', 'appointment_date', 'appointment_time', 'appointment_type_name', 'appointment_type_id', 'appointment_status', 'appointment_status_name', 'patient_id', 'patient_first_name', 'patient_last_name', 'patient_middle_name', 'email', 'patient_cell_phone', 'patient_home_phone', 'patient_work_phone', 'patient_address', 'patient_city', 'patient_state', 'patient_zip', 'patient_country', 'patient_language', 'patient_date_of_birth', 'preffered_communication_method', 'portal_key', 'patient_status', 'patient_account_status', 'patient_account_status_name', 'resource_id', 'resource_name', 'facility_id', 'facility_name', 'facility_phone_primary', 'facility_address', 'facility_city', 'facility_zip', 'facility_state');
		array_walk($columnTitles, 'processData');
		if(isset($_REQUEST["VITAL_DATA_DUMP"])){
			$csvFileName = 'dump_appointments_'.date('Ymd').'.csv';
		}
		else{
			$csvFileName = 'appointments_'.date('Ymd').'.csv';
		}
		$fp = fopen($fileDir.'/'.$csvFileName, 'w');
		
		/*Insert Column Title*/
		fwrite($fp, implode(',', $columnTitles)."\n");
		
		/*Insert Data*/
		while( $row = imw_fetch_assoc($resp) )
		{
			$row['patient_cell_phone'] = preg_replace('/[^0-9]/', '', $row['patient_cell_phone']);
			$row['patient_home_phone'] = preg_replace('/[^0-9]/', '', $row['patient_home_phone']);
			$row['patient_work_phone'] = preg_replace('/[^0-9]/', '', $row['patient_work_phone']);
			$row['facility_phone_primary'] = preg_replace('/[^0-9]/', '', $row['facility_phone_primary']);
			$row['appointment_status_name'] =(!$row['appointment_status_name'] && $row['appointment_status']==0)?'Created/Reset':$row['appointment_status_name'];
			
			
			$row['patient_work_phone'] .= (( trim($row['patient_work_phone_ext']) != '' ) ? '-'.$row['patient_work_phone_ext']:'');
			unset($row['patient_work_phone_ext']);
			
			$row['facility_phone_primary'] .= (( trim($row['facility_phone_ext']) != '' ) ? '-'.$row['facility_phone_ext']:'');
			unset($row['facility_phone_ext']);
			
			array_walk($row, 'processData');
			
			fwrite($fp, implode(',', $row)."\n");
		}
		fclose($fp);
		
		if( file_exists($fileDir.'/'.$csvFileName) )
		{
			FUNC_uploadFile($csvFileName, $fileDir);
		}
		
		if( $dlFile )
		{
			dl_file($csvFileName, $fileDir);
		}
	}
	
	//code to remove files older than 1 month
	 $files = glob($fileDir."/Archive/*.csv");
	 $now   = time();

	  foreach ($files as $file) {
		if (is_file($file)) {
		  if ($now - filemtime($file) >= 60 * 60 * 24 * 31) { // 31 days
			unlink($file);
			//  echo"<br>going to unlink ".$file;
		  }
		}
	  }
}

?>