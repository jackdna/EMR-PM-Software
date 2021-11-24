<?php
//PROCEED ONLY IF PROCEDURE IS AVAILABLE IN GLOBAL FILE
include_once("../../IMWAPI/send_calls/send_function.php");

$rs=imw_query("Select DOB FROM patient_data WHERE id='".$_REQUEST["pt_id"]."'");
$res=imw_fetch_assoc($rs);
$dob= $res['DOB'];

//SENDING INFO RELATED ADDING NEW APPOINTMENT
if($_REQUEST["save_type"] == "save" || $_REQUEST["save_type"] == "addnew"){

	$arr_call_data  = array(
		'customerId' => 5271
		,'patientId' => $_REQUEST["pt_id"]
		,'patientDob' => $dob
		,'apptStartDate' => $_REQUEST["start_date"].' '.$_REQUEST['start_time']
		,'visitTypeId' => $proc_id
		,'locationId' => $_REQUEST["facility_id"]
		,'doctorId' => $_REQUEST["doctor_id"]
		);		
	
	$objSendAPICall=new send_api_call($_REQUEST["pt_id"], 0);
	$result=$objSendAPICall->BOOK_APPOINTMENT($arr_call_data);

}
	
//SENDING INFO RELATED UPDATING/CANCEL APPOINTMENT
if(sizeof($GLOBALS["API_PROCEDURES"])>0){
	$sel_proc_name= strtolower($_POST['sel_proc_name']);

	if($GLOBALS["API_PROCEDURES"][$sel_proc_name]){

		if(empty($_POST["st_type"])==false && empty($_POST["ap_id"])==false){
		
			if($_POST["st_type"]==18){ //CANCELLED

				//GETTING CANCELLED REASON
				$rs=imw_query("Select change_reason FROM previous_status WHERE sch_id='".$_POST["ap_id"]."' ORDER BY id DESC LIMIT 1");
				$res=imw_fetch_assoc($rs);
				$cancelled_reason=$res['change_reason'];
				
				$arr_call_data  = array(
					'customerId' => 5271
					,'patientId' => $_POST["pt_id"]
					,'apptId' => $_POST["ap_id"]
					,'patientDob' => $dob
					,'cancel_reason' => $cancelled_reason
					);		
		
				$objSendAPICall=new send_api_call($_REQUEST["pt_id"], 0);
				$result=$objSendAPICall->CANCEL_APPOINTMENT($arr_call_data);


				
			}else{ //UPDATING APPT WITH STATUS
				$arr_call_data  = array(
					'customerId' => 5271
					,'patientId' => $_POST["pt_id"]
					,'apptId' => $_POST["ap_id"]
					,'patientDob' => $dob
					,'apptStatus' => $_POST["st_type"]
					);		
	
				$objSendAPICall=new send_api_call($_REQUEST["pt_id"], 0);
				$result=$objSendAPICall->UPDATE_APPOINTMENT($arr_call_data);
			}
		}
	}
}

?>