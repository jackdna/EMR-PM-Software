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
$ignoreAuth=true;

if($argv[1] && $argv[1]!='')
{
	$_SERVER['REQUEST_URI'] = $argv[1];
}
require_once(dirname(__FILE__)."/../../../config/globals.php");
require_once($GLOBALS["fileroot"]."/library/classes/ccda_functions.php");
require_once($GLOBALS["fileroot"]."/library/classes/AES.class.php");

$dayArr = array("1"=>"first","2"=>"second","3"=>"third","4"=>"fourth","5"=>"fifth");


$library_path = $GLOBALS['webroot'].'/library';

//START GET DOWNLOAD LOG
$getLogQry = "SELECT * FROM ccda_download_log";
$getLogRes = imw_query($getLogQry);
if(imw_num_rows($getLogRes)>0) {
	while($getLogRow = imw_fetch_assoc($getLogRes)) {
		$ccda_export_schedule_id = $getLogRow["ccda_export_schedule_id"];
		
	}
}
//END GET DOWNLOAD LOG

//START FETCHING DATA
$getQry = "SELECT ces.id,ces.schedule_type,ces.facility_id,ces.provider_id,ces.date_from,ces.date_to,ces.enc_key,ces.zip_encrypt,ces.operator_id,ces.ins_type,ces.ins_comp_id,
			DATE_FORMAT(ces.schedule_date_time, '%Y-%m-%d') as schedule_date,
			DATE_FORMAT(ces.schedule_date_time, '%H:%i:00') as schedule_time,
			ces.reoccurring_time_period,ces.reoccurring_day_num,ces.reoccurring_day_week,ces.reoccurring_time,
			IF(ces.operator_date_time='0000-00-00 00:00:00','',DATE_FORMAT(ces.operator_date_time, '".get_sql_date_format('','Y','-')." %h:%i %p')) as operatorDateTime,
			CONCAT(u.lname,', ',u.fname,' ',u.mname) as operatorName
			FROM ccda_export_schedule ces 
			INNER JOIN users u ON(u.id = ces.operator_id)
			WHERE ces.delete_status = '0'
			ORDER BY ces.schedule_type, ces.schedule_date_time DESC";
$getRows = get_array_records_query($getQry);
$ccda_cal = "cron_tab";
foreach($getRows as $getRow) {

		$ccda_export_schedule_id			= $getRow["id"];
		$ccda_export_schedule_operator_id	= $getRow["operator_id"];
		$facility_id	 					= $getRow["facility_id"];
		$provider_id 						= $getRow["provider_id"];
		$date_from 							= $getRow["date_from"];
		$date_to 							= $getRow["date_to"];
		$schedule_type 						= $getRow["schedule_type"];
		$schedule_date 						= $getRow["schedule_date"];
		$schedule_time 						= $getRow["schedule_time"];
		$enc_key 							= $getRow["enc_key"];
		$zip_encrypt						= $getRow["zip_encrypt"];
		$rqArrInsType						= $getRow["ins_type"];
		$rqInsProvider 						= $getRow["ins_comp_id"];
		
		$operatorName 						= $getRow["operatorName"];
		$operatorDateTime 					= $getRow["operatorDateTime"];
		$reoccurring_time_period			= $getRow["reoccurring_time_period"];
		$reoccurring_day_num				= $getRow["reoccurring_day_num"];
		$reoccurring_day_week				= strtolower($getRow["reoccurring_day_week"]);
		$reoccurring_time					= $getRow["reoccurring_time"];
		$reoccurring_day_num_val			= $dayArr[$reoccurring_day_num];
		$startDateArr						= array();
		if($schedule_type == "Reoccurring Date Time" && $reoccurring_day_num) {
			
			$schedule_date 					= date("Y-m-d", strtotime($reoccurring_day_num_val." ".$reoccurring_day_week." ".date("Y-m")));
			$schedule_time 					= $reoccurring_time;
			
			$lastWeekTmp 					= strtotime("-1 weeks",strtotime($schedule_date));
			$lastWeek 						= date("Y-m-d",$lastWeekTmp);
			$lastMonthTmp 					= strtotime("-1 months",strtotime($schedule_date));
			$lastMonth 						= date("Y-m-d",$lastMonthTmp); 
			$last3MonthsTmp 				= strtotime("-3 months",strtotime($schedule_date));
			$last3Months 					= date("Y-m-d",$last3MonthsTmp); 
			$startDateArr["last week"] 		= $lastWeek;
			$startDateArr["last month"] 	= $lastMonth;
			$startDateArr["last 3 months"] 	= $last3Months;

			$date_from						= $startDateArr[strtolower($reoccurring_time_period)];
			$date_to 						= date("Y-m-d",strtotime($schedule_date));
			

		}
		
		//Converting Time to integer string
		$current_date = strtotime(date("Y-m-d H:i:00"));
		$current_date_10_min_before = strtotime(date("H:i:s",strtotime("-10 minutes")));
		$sch_dateTime = strtotime($schedule_date.' '.$schedule_time);
		
		//example - sch_date = 7:15, currrent_date = 7:20, 10 min before current date = 7:10
		//$sch_dateTime = "07:15:00";$current_date = "07:20:00";$current_date_10_min_before = "07:10:00";
		echo '<br>Current Date Time = '.date("Y-m-d H:i:00").'@@'.$current_date.' <br> Current DateTime 10 Mins Before'.date("H:i:s",strtotime("-10 minutes")).'@@'.$current_date_10_min_before.' <br>Schedule Date Time'.$schedule_date.' '.$schedule_time.'@@'.$sch_dateTime;
		$log_content="\n\nCurrent Date Time = ".date("Y-m-d H:i:00")."@@".$current_date." \n\n Current DateTime 10 Mins Before".date("H:i:s",strtotime("-10 minutes"))."@@".$current_date_10_min_before." \n\nSchedule Date Time".$schedule_date." ".$schedule_time."@@".$sch_dateTime."\n\n--------------\n";
		file_put_contents(data_path()."xml/cron_log.txt",$log_content,FILE_APPEND);
		if($current_date >= $sch_dateTime && $current_date_10_min_before < $sch_dateTime){
			// Fetching Patients
			$queryA = $queryJ = $rqInsType = "";
			if(empty($rqArrInsType) == false){
				$rqInsType = str_ireplace(",","','",$rqArrInsType);
				$rqInsType = "'".$rqInsType."'";
				$queryA .= " AND insd.type IN ($rqInsType) ";
			}	
			if(empty($rqInsProvider) == false){
				$queryA .= " AND insd.provider IN ($rqInsProvider) ";
			}
			if(empty($rqInsType) == false || empty($rqInsProvider) == false){
				$queryJ .= " JOIN insurance_data insd ON insd.pid = pd.id AND insd.actInsComp = '1' ".$queryA;	
			}

			$query = "SELECT pd.id as pat_id,CONCAT(pd.lname,', ',pd.fname) as pat_name,
											cmt.id as form_id, DATE_FORMAT(cmt.date_of_service, '".get_sql_date_format('','Y','-')."') as dos
											FROM patient_data pd
											JOIN chart_master_table cmt ON cmt.patient_id = pd.id ".$queryJ."
											WHERE 1=1 ";
			if($facility_id != "")		
			$query .=  " AND pd.default_facility in($facility_id) ";
			if($provider_id != "")
			$query .=  " AND pd.providerID in($provider_id) ";
			if($date_from != "" && $date_to!="")
			$query .=  " AND (cmt.date_of_service BETWEEN '".$date_from."' AND '".$date_to."') ";
			$query .=  " ORDER BY cmt.date_of_service DESC,pd.lname ";
			$arrData = get_array_records_query($query);
			file_put_contents(data_path()."xml/cron_log.txt","\n".$query."\n count=".count($arrData)."\n",FILE_APPEND);
			$_SESSION['authId']=$ccda_export_schedule_operator_id;
			include(dirname(__FILE__)."/create_ccda_r2_xml.php");
		}
		file_put_contents(data_path()."xml/cron_log.txt","\n----------------------\n",FILE_APPEND);
		
}
//END FETCHING DATA

?>
