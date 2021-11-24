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
	//get status dates based on given date range from detail status table
	$queryDates="select dt.order_id,dt.order_date `dated`, dt.order_time,dt.order_status from in_order_detail_status `dt` 
	LEFT JOIN in_order `ord` ON ord.id=dt.order_id 
	WHERE dt.order_date BETWEEN '$date1' AND '$date2'
	AND ord.order_status IN ('received', 'dispensed')
	ORDER BY dt.order_date ASC";
	$resp=imw_query($queryDates)or die(imw_error());
	while($data=imw_fetch_object($resp))
	{
		$status_arr[$data->order_id][$data->order_status]['date']=$data->dated;
		$status_arr[$data->order_id][$data->order_status]['time']=$data->order_time;
		$order_ids[]=$data->order_id;
	}
	$order_ids_str=implode(',',$order_ids);
	if($order_ids_str){
	//get list of products aganist order id
	$queryNames="SELECT GROUP_CONCAT(dt.item_name) as names, dt.order_id FROM `in_order_details` `dt` 
	LEFT JOIN in_order `ord` ON ord.id=dt.order_id 
	WHERE dt.order_id IN($order_ids_str)
	AND ord.order_status IN ('received', 'dispensed')
	GROUP BY dt.order_id";
	$resp=imw_query($queryNames)or die(imw_error());
	while($data=imw_fetch_object($resp))
	{
		$item_name_arr[$data->order_id]=$data->names;
	}
	
	$queryOrders = "SELECT 
	`ord`.`id` AS 'ordernum_seq', 
	`ord`.`entered_date` AS 'product_order_date', 
	`ord`.`entered_time` AS 'product_order_time', 
	`ord`.`order_status`, 
	
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
	`in_order` `ord` 
	LEFT JOIN `users` `us` ON `us`.`id` = `ord`.`operator_id` 
	LEFT JOIN `in_location` `loc` ON `loc`.id=`ord`.`loc_id` 
	LEFT JOIN `facility` `fac` ON `fac`.`fac_prac_code` = `loc`.`pos` 
	LEFT JOIN `patient_data` `pd` ON `pd`.`id` = `ord`.`patient_id` 
WHERE 
	ord.id IN($order_ids_str)
	AND ord.order_status IN ('received', 'dispensed')";

	
	/*List $queryOrders*/
	$resp = imw_query($queryOrders)or die(imw_error());
	$exportData = ( $resp && imw_num_rows($resp)>0 );
	
	if( $exportData )
	{
		$columnTitles = array('ordernum_seq', 'product_order_date', 'product_ready_date', 'product_pickup_date', 'product_type_name', 'product_type_id', 'product__status', 'patient_id', 'patient_first_name', 'patient_last_name', 'patient_middle_name', 'email', 'patient_cell_phone', 'patient_home_phone', 'patient_work_phone', 'patient_address', 'patient_city', 'patient_state', 'patient_zip', 'patient_country', 'patient_language', 'patient_date_of_birth', 'preffered_communication_method', 'portal_key', 'facility_id', 'facility_name', 'facility_phone_primary', 'facility_address', 'facility_city', 'facility_zip', 'facility_state');//, 'resource_id', 'resource_name', 'appointment_id'  //commented on request of berkeley on 07 nov 2017 
		array_walk($columnTitles, 'processData');
		
		$csvFileName = 'orders_'.date('Ymd').'.csv';
		$fp = fopen($fileDir.'/'.$csvFileName, 'w');
		
		/*Insert Column Title*/
		fwrite($fp, implode(',', $columnTitles)."\n");
		
		/*Insert Data*/
		while( $row = imw_fetch_assoc($resp) )
		{
			$order_id=$order_date=$order_status=$order_time='';
			$order_id=$row['ordernum_seq'];
			$order_date=$row['product_order_date'];
			$order_time=$row['product_order_time'];
			$order_status=$row['order_status'];
			
			unset($row['ordernum_seq'],$row['product_order_date'],$row['product_order_time'],$row['order_status']);
			
			array_unshift($row,$order_status);//put product__status value before patient_id
			array_unshift($row,$order_id);//product_type_id
			array_unshift($row,$item_name_arr[$order_id]);//product_type_name
			if($status_arr[$order_id]['dispensed']['date'])
			$picup_dt=$status_arr[$order_id]['dispensed']['date'].':'.$status_arr[$order_id]['dispensed']['time'];
			array_unshift($row,$picup_dt);//product_pickup_date
			if($status_arr[$order_id]['received']['date'])
			$ready_dt=$status_arr[$order_id]['received']['date'].':'.$status_arr[$order_id]['received']['time'];
			array_unshift($row,$ready_dt);//product_ready_date
			$order_dt=$order_date.':'.$order_time;
			array_unshift($row,$order_dt);//product_order_date
			array_unshift($row,$order_id);//ordernum_seq
				
			$row['patient_cell_phone'] = preg_replace('/[^0-9]/', '', $row['patient_cell_phone']);
			$row['patient_home_phone'] = preg_replace('/[^0-9]/', '', $row['patient_home_phone']);
			$row['patient_work_phone'] = preg_replace('/[^0-9]/', '', $row['patient_work_phone']);
			$row['facility_phone_primary'] = preg_replace('/[^0-9]/', '', $row['facility_phone_primary']);
			
			
			$row['patient_work_phone'] .= (( trim($row['patient_work_phone_ext']) != '' ) ? '-'.$row['patient_work_phone_ext']:'');
			unset($row['patient_work_phone_ext']);
			
			$row['facility_phone_primary'] .= (( trim($row['facility_phone_ext']) != '' ) ? '-'.$row['facility_phone_ext']:'');
			unset($row['facility_phone_ext']);
			
			/*Add appointmnet ID*/
			//commented on request of berkeley on 07 nov 2017
			//if( isset($appointmentsList[$row['patient_id']]) )
			//	$row['appointment_id'] = $appointmentsList[$row['patient_id']];
			
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
}

?>