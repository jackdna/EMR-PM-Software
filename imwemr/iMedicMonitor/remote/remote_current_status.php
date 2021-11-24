<?php 
require_once("common_functions.php");

$qry = "SELECT * FROM patient_location WHERE cur_date = '".date("Y-m-d")."' ORDER BY patient_location_id";
$res =	imw_query($qry);

$arr_old_check_sum = array();
$request_var = urldecode($_REQUEST["old_check_sum"]);
if($request_var != ""){
	$arr_old_check_sum = parse_check_sum_data($request_var);
}
$echo = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?><response>";
if(imw_num_rows($res) > 0){
	while($arr = imw_fetch_array($res)){
		$new_check_sum = $arr["patient_location_id"]."~~".$arr["patientId"]."~~".$arr["doctor_Id"]."~~".$arr["facility_Id"]."~~".$arr["app_room"]."~~".$arr["doctor_mess"]."~~".$arr["tech_click"]."~~".$arr["app_room_time"]."~~".$arr["cur_date"]."~~".$arr["cur_time"]."~~".$arr["chart_opened"]."~~".$arr["ready4DrId"]."~~".$arr["moved2Tech"]."~~".$arr["opidSent2Dr"]."~~".$arr["opidSent2Tech"];
		
		if(isset($arr_old_check_sum[$arr["patient_location_id"]])){
			if($arr_old_check_sum[$arr["patient_location_id"]] != $new_check_sum){
				$echo .= "<record><sum_id>".$arr["patient_location_id"]."</sum_id><sum_type>update</sum_type><sum_val>".$new_check_sum."</sum_val></record>";
			}else{
				$echo .= "<record><sum_id>".$arr["patient_location_id"]."</sum_id><sum_type>nochange</sum_type><sum_val>".$new_check_sum."</sum_val></record>";
			}
		}else{
			$echo .= "<record><sum_id>".$arr["patient_location_id"]."</sum_id><sum_type>new</sum_type><sum_val>".$new_check_sum."</sum_val></record>";
		}
	}
}
$echo .= "</response>";
echo $echo;
?>