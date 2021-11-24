<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	set_time_limit(0);
	include_once("../../../common/conDb.php");  //MYSQL CONNECTION
	$sv_file_name = "merge_patient_data.csv";
	$fl_name= "csv/".$sv_file_name;
	
	imw_query("CREATE TABLE patient_data_tbl_bak_".date("d_m_Y")." AS (SELECT * FROM patient_data_tbl)") or $msg_info[] = imw_error();
	
	$RECORD_UPDATE=$RECORD_INSERT=0;
	
	//Get Master Patient Data Detail
	$Query	=	"SELECT group_concat(`patient_id`) as RecordFound FROM `patient_data_tbl` Group By `patient_fname`,`patient_lname`,`zip`,`date_of_birth` having count(`patient_id`)>1 ORDER BY patient_id Desc,`patient_data_tbl`.`patient_fname`,`patient_lname` ASC";
	$Sql	= 	imw_query($Query)or $msg_info[] = $Query.imw_error();
	$doublePatientArr	=	array();
	while($Row = imw_fetch_object($Sql))
	{
		$doublePatientArr[] = $Row->RecordFound;
	}
	
	$Query	=	"SELECT group_concat(`patient_id`) as RecordFound FROM `patient_data_tbl` WHERE imwPatientId ='' Group By `patient_fname`,`patient_lname`,`imwPatientId`,`date_of_birth` having count(`patient_id`)>1 ORDER BY patient_id Desc,`patient_data_tbl`.`patient_fname`,`patient_lname` ASC";
	$Sql	= 	imw_query($Query)or $msg_info[] = $Query.imw_error();
	while($Row = imw_fetch_object($Sql))
	{
		$doublePatientArr[] = $Row->RecordFound;
	}

	$Query	=	"SELECT group_concat(`patient_id`) as RecordFound FROM `patient_data_tbl` WHERE imwPatientId !='' Group By `imwPatientId` having count(`patient_id`)>1 ORDER BY patient_id Desc,`patient_data_tbl`.`patient_fname`,`patient_lname` ASC";
	$Sql	= 	imw_query($Query)or $msg_info[] = $Query.imw_error();
	while($Row = imw_fetch_object($Sql))
	{
		$doublePatientArr[] = $Row->RecordFound;
	}
	
	
	$doublePatientArr = array_unique($doublePatientArr);	

	//echo'<pre>';
	echo 'Total Number of duplicate patients is '.count($doublePatientArr).'<br>';
	//print_r($doublePatientArr);die();
	$duplicateApptArr = array();	
		
	if(is_array($doublePatientArr) && count($doublePatientArr) > 0 )
	{
		foreach($doublePatientArr as $patientIds)
		{
			$Query	=	"Select patient_id,imwPatientId From patient_data_tbl Where patient_id In (".$patientIds.")Order By imwPatientId desc,patient_id Desc ";
			$Sql	=	imw_query($Query) or $msg_info[] = $Query.imw_error();	
			$recordToKeepId = $recordToRemoveId = '';
			$imwFound = false;
			while($Row = imw_fetch_object($Sql))
			{
				if($Row->imwPatientId && $imwFound == false)
				{
					$imwFound = true;
					$recordToKeepId	=	$Row->patient_id;
				}
				elseif(!$Row->imwPatientId && $imwFound == false)
				{
					$recordToKeepId = $Row->patient_id;
					$imwFound = true;
				}
				elseif($imwFound == true)
				{
					$recordToRemoveId = $Row->patient_id;
				}
					
				if($recordToKeepId && $recordToRemoveId) 
				{
					$duplicateApptArr[] = $recordToRemoveId;
					unset($update_message);
					if(file_exists($fl_name))
					{
						$fileContents = fopen($fl_name,"r");
						$row=0;
						while(($data=fgetcsv($fileContents,10000,',')) !== FALSE)
						{	
							if($row >0)
							{
								$primary_table_name	= trim($data[0]);
								$primary_column_name= trim($data[1]);
								
								if($primary_table_name && $primary_table_name != "patient_data_tbl" && $primary_column_name ) {
									$primary_updt_qry = "UPDATE $primary_table_name SET $primary_column_name ='".$recordToKeepId."' WHERE $primary_column_name = '".$recordToRemoveId."'";
									imw_query($primary_updt_qry) or $msg_info[] = 'PRIMARY = '.$primary_updt_qry.imw_error();
								}
							}
							$row++;
						}
					}
					
					//START REMOVE DUPLICATE PATIENT DATA
					$qry =  "Delete From patient_data_tbl Where patient_id = '".$recordToRemoveId."' ";
					imw_query($qry)or $msg_info[] = $qry.imw_error();
					$msg_info[]	= "Patient ID ".$recordToRemoveId ." is merged into Patient ID ".$recordToKeepId." Successfully.";
					//END REMOVE DUPLICATE PATIENT DATA
				}
					
			}
				
		}
			
			//Get Duplicate Patient Data Detail
			/*
			$ptQry = "SELECT patient_in_waiting_id, patient_id, drOfficePatientId FROM patient_in_waiting_tbl Where patient_id In (".implode(',',$doublePatientArr).") ORDER BY patient_id ASC";
			$res = imw_query($ptQry)or $msg_info[] = $ptQry.imw_error();
			if(imw_num_rows($res)>0){
				while($row = imw_fetch_assoc($res))
				{
					list($imwPatientId, $oldPatientId) = explode('-',$row['drOfficePatientId']);
					if($imwPatientId)
					{
						$drOfficePatientID_New = $imwPatientId.'-'.$row['patient_id'];
						$uQry = "Update ".$childDB.".patient_in_waiting_tbl Set drOfficePatientId = '".$drOfficePatientID_New."' Where patient_in_waiting_id = ".$row['patient_in_waiting_id']." ";	
						imw_query($uQry) or $msg_info[] = 'PatientWaitingID = '.$uQry.imw_error();
					}
				}
			}
			*/
		}
		else
		{
			$msg_info[]	=	"No Duplicate Record Found.";	
		}
		echo 'Total Number of duplicate Appointment is '.count($duplicateApptArr).'<br>';
		
		//Reset Duplicate Patient id in waiting tbl
		$ptQry = "SELECT patient_in_waiting_id, patient_id, drOfficePatientId FROM patient_in_waiting_tbl ORDER BY patient_id ASC";
		$res = imw_query($ptQry)or $msg_info[] = $ptQry.imw_error();
		if(imw_num_rows($res)>0){
			while($row = imw_fetch_assoc($res))
			{
				list($imwPatientId, $oldPatientId) = explode('-',$row['drOfficePatientId']);
				if($imwPatientId)
				{
					$drOfficePatientID_New = $imwPatientId.'-'.$row['patient_id'];
					$uQry = "Update patient_in_waiting_tbl Set drOfficePatientId = '".$drOfficePatientID_New."' Where patient_in_waiting_id = ".$row['patient_in_waiting_id']." ";	
					imw_query($uQry) or $msg_info[] = 'PatientWaitingID = '.$uQry.imw_error();
				}
			}
		}
		
		
?>
<html>
<head>
<title>Merge Standalone Patient Data </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2"><?php echo(implode("<br>",$msg_info));?></font>
<?php
}
@imw_close();
?> 

</body>
</html>