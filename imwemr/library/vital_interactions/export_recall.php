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
	$queryRecall = "SELECT 
	`rec`.`id` AS 'ordernum_seq', 
	DATE_FORMAT(`rec`.`recalldate`, '%m-%d-%Y') AS 'recall_due_date', 
	`sp`.`proc` AS 'recall_type_name', 
	DATE_FORMAT(`rec`.`current_date1`, '%m-%d-%Y') AS 'recall_entry_date', 
	`pd`.`id` AS 'patient_id', 
	`pd`.`fname` AS 'patient_first_name', 
	`pd`.`lname` AS 'patient_last_name', 
	`pd`.`mname` AS 'patient_middle_name', 
	`pd`.`email`, 
	`pd`.`phone_cell` AS 'patient_cell_phone', 
	`pd`.`phone_home` AS 'patient_home_phone', 
	`pd`.`phone_biz` AS 'patient_work_phone', 
	`pd`.`phone_biz_ext` AS 'patient_work_phone_ext', 
	CONCAT(`pd`.`street`, ' ', `pd`.`street2`) AS 'patient_address', 
	`pd`.`city` AS 'patient_city', 
	`pd`.`state` AS 'patient_state', 
	CONCAT(`pd`.`postal_code`, `pd`.`zip_ext`) AS 'patient_zip', 
	`pd`.`country_code` AS 'patient_country', 
	REPLACE(`pd`.`language`, 'Other -- ', '') AS 'patient_language', 
	DATE_FORMAT(`pd`.`DOB`, '%m-%d-%Y') AS 'patient_date_of_birth', 
	IF(`pd`.`preferr_contact`=0, 'home_phone', IF(`pd`.`preferr_contact`=1, 'work_phone', IF(`pd`.`preferr_contact`=2, 'cell_phone', ''))) AS 'preffered_communication_method',
	IF(`pd`.`username`='' OR `pd`.`username` IS NULL, `pd`.`temp_key`, '') AS 'portal_key', 
	`us`.`user_npi` AS 'resource_id', 
	CONCAT(`us`.`lname`, ',', `us`.`fname`, ' ', `us`.`mname`) AS 'resource_name', 
	`fac`.`id` AS 'facility_id', 
	`fac`.`name` AS 'facility_name', 
	`fac`.`phone` AS 'facility_phone_primary', 
	`fac`.`phone_ext` AS 'facility_phone_ext', 
	`fac`.`street` AS 'facility_address', 
	`fac`.`city` AS 'facility_city', 
	`fac`.`city` AS 'facility_city', 
	CONCAT(`fac`.`postal_code`, `fac`.`zip_ext`) AS 'facility_zip', 
	`fac`.`state` AS 'facility_state'
FROM 
	`patient_app_recall` `rec` 
	LEFT JOIN `slot_procedures` `sp` ON `sp`.`id` = `rec`.`procedure_id` 
	LEFT JOIN `users` `us` ON `us`.`id` = `rec`.`operator` 
	LEFT JOIN `facility` `fac` ON `fac`.`id` = `rec`.`facility_id` 
	LEFT JOIN `patient_data` `pd` ON `pd`.`id` = `rec`.`patient_id` 
WHERE 
	`rec`.`recalldate` BETWEEN '".$date1."' AND '".$date2."'";
	
	/*List Appintments*/
	$queryAppointments = "SELECT 
	`id`, 
	`sa_patient_id` AS 'patient_id'
FROM 
	`schedule_appointments` 
WHERE 
	`sa_app_start_date` BETWEEN '".$date1."' AND '".$date2."'
ORDER BY `sa_app_start_date` DESC";
	
	$appointmentsList = array();
	$respAppointments = imw_query($queryAppointments);
	
	if( $respAppointments && imw_num_rows($respAppointments) >0 )
	{
		while($apptRow = imw_fetch_assoc($respAppointments))
		{
			if( !isset($appointmentsList[$apptRow['patient_id']]) )
				$appointmentsList[$apptRow['patient_id']] = $apptRow['id'];
		}
	}
	
	/*List Recalls*/
	$resp = imw_query($queryRecall);
	$exportData = ( $resp && imw_num_rows($resp)>0);
	
	if( $exportData )
	{
		$columnTitles = array('ordernum_seq', 'recall_due_date', 'recall_type_name', 'recall_entry_date', 'patient_id', 'patient_first_name', 'patient_last_name', 'patient_middle_name', 'email', 'patient_cell_phone', 'patient_home_phone', 'patient_work_phone', 'patient_address', 'patient_city', 'patient_state', 'patient_zip', 'patient_country', 'patient_language', 'patient_date_of_birth', 'preffered_communication_method', 'portal_key', 'resource_id', 'resource_name', 'facility_id', 'facility_name', 'facility_phone_primary', 'facility_address', 'facility_city', 'facility_zip', 'facility_state', 'appointment_id');
		array_walk($columnTitles, 'processData');
		if(isset($_REQUEST["VITAL_DATA_DUMP"])){
			$csvFileName = 'dump_recalls_'.date('Ymd').'.csv';
		}
		else{
			$csvFileName = 'recalls_'.date('Ymd').'.csv';
		}
		$fp = fopen($fileDir.'/'.$csvFileName, 'w');
		
		/*Insert Column Title*/
		fwrite($fp, implode(',', $columnTitles)."\n");
		
		/*Insert Data*/
		while( $row = imw_fetch_assoc($resp))
		{
			//removing data for 'resource_id', 'resource_name', as requested by berkeley on 07 nov 2017
			$row['resource_id'] =''; $row['resource_name'] ='';
			
			$row['patient_cell_phone'] = preg_replace('/[^0-9]/', '', $row['patient_cell_phone']);
			$row['patient_home_phone'] = preg_replace('/[^0-9]/', '', $row['patient_home_phone']);
			$row['patient_work_phone'] = preg_replace('/[^0-9]/', '', $row['patient_work_phone']);
			$row['facility_phone_primary'] = preg_replace('/[^0-9]/', '', $row['facility_phone_primary']);
			
			
			$row['patient_work_phone'] .= (( trim($row['patient_work_phone_ext']) != '' ) ? '-'.$row['patient_work_phone_ext']:'');
			unset($row['patient_work_phone_ext']);
			
			$row['facility_phone_primary'] .= (( trim($row['facility_phone_ext']) != '' ) ? '-'.$row['facility_phone_ext']:'');
			unset($row['facility_phone_ext']);
			
			/*Add appointmnet ID*/
			if( isset($appointmentsList[$row['patient_id']]) ){
				$row['appointment_id'] = $appointmentsList[$row['patient_id']];
			}else{
				//assign 0 to omit empty value
				$row['appointment_id'] = 0;
			}
			
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
}

?>