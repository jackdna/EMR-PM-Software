<?php 
/*
File: ajax.php
Coded in PHP7
Purpose: Fetch Insurance Data
Access Type: Include File
*/
require_once("../../config/config.php");

$returnArr=array();

if($_REQUEST['action']!="" && $_REQUEST['action']=="opencase")
{

	$qry = "select ins_case_type from insurance_case where ins_case_type = '".$_REQUEST['case']."' and patient_id = '".$_REQUEST['pt']."'";
	$mqry = imw_query($qry);	
	if(imw_num_rows($mqry)>0)
	{
		$returnArr=0;
		echo json_encode($returnArr);
	}
	else
	{
	
		$case_name_arr=array();
		
		$insCaseInsert = "insert into insurance_case set
		ins_case_type = '".imw_real_escape_string($_POST['case'])."',
		patient_id = '".$_REQUEST['pt']."',		
		start_date = '".date('Y-m-d')."',
		case_status = 'Open',
		athenaID = '0'
		";
		imw_query($insCaseInsert);
		$last_id = imw_insert_id();
		
		
		$getcaseid = imw_query("select ins_case_type from insurance_case where ins_caseid='".$last_id."'");
		$getcaseidrow = imw_fetch_assoc($getcaseid);
		
		$getcasename = imw_query("select case_id,case_name from insurance_case_types where case_id='".$getcaseidrow['ins_case_type']."'");
		$getcasenamerow = imw_fetch_assoc($getcasename);		
		
		$case_name=$getcasenamerow['case_name']."--".$last_id;
		echo $case_name;
	}
	
}

if($_REQUEST['action']!="" && $_REQUEST['action']=="editins")
{
		$_SESSION['currentCaseid']=$_REQUEST['case'];
		
		$qry = imw_query("select insurance_data.*,Date_Format(insurance_data.effective_date,'%m-%d-%Y') as active_date,  insurance_companies.in_house_code as pracCodeVS, insurance_companies.claim_type as claimType, 
					insurance_companies.name as comp_name,insurance_companies.claim_type as InsClaimType 
					from insurance_data LEFT JOIN insurance_companies on 
					insurance_companies.id = insurance_data.provider 
					where insurance_data.ins_caseid = '".$_REQUEST['case']."'
					and insurance_data.pid='".$_SESSION['patient_session_id']."' and insurance_data.actInsComp='1' and ( LOWER(insurance_data.type)= 'primary' OR LOWER(insurance_data.type) = 'secondary') ORDER BY date DESC
					
					");
		while($row = imw_fetch_assoc($qry))
		{
			$returnArr[] = $row;
		}
		echo json_encode($returnArr);
}

if($_REQUEST['action']!="" && $_REQUEST['action']=="getcitystate")
{
 $code_qry="Select * from zip_codes where zip_code='".stripslashes($_REQUEST['zipcode'])."'";

		$code_res=imw_query($code_qry) or die(imw_error());
		if(imw_num_rows($code_res)>0)	{
			$row=imw_fetch_array($code_res);
			$city_state=$row['city']."-".$row['state_abb'];
			echo $city_state;
		}	
		else
		{
			echo "false";	
		}
}

?>