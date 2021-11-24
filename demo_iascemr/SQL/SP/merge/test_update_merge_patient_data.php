<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	set_time_limit(0);
	include_once("../../../common/conDb.php");  //MYSQL CONNECTION
	$sv_file_name = "merge_patient_data.csv";
	include_once("test_update_merge_db_detail.php");  //DB Details
	$fl_name= "csv/".$sv_file_name;
	
	if($masterDB && $childDB)
	{
		$RECORD_UPDATE=$RECORD_INSERT=0;
		
		imw_query("CREATE TABLE $masterDB.patient_data_tbl_bak_".date("d_m_Y")." AS (SELECT * FROM $masterDB.patient_data_tbl)") or $msg_info[] = imw_error();
		imw_query("CREATE TABLE $childDB.patient_data_tbl_bak_".date("d_m_Y")." AS (SELECT * FROM $childDB.patient_data_tbl)") or $msg_info[] = imw_error();
		
		//Get Master Patient Data Detail
		$FieldsList	=	"patient_id,imwPatientId,patient_fname,patient_mname,patient_lname,zip,date_of_birth";
		$masStr = "Select ".$FieldsList." From ".$masterDB.".patient_data_tbl Order By patient_id Asc ";
		$masQuery = imw_query($masStr)or $msg_info[] = $masStr.imw_error();
		
		while($masData = imw_fetch_object($masQuery))
		{
			$imwPatientId	=	trim($masData->imwPatientId);
			$pFirstName	=	trim(strtolower($masData->patient_fname));
			$pMiddleName=	trim(strtolower($masData->patient_mname));
			$pLastName	=	trim(strtolower($masData->patient_lname));
			$pZip		=	trim(strtolower($masData->zip));
			$pDOB		=	trim($masData->date_of_birth);
			
			if($imwPatientId)
			{
				$MASTER_IMW_ARR[$imwPatientId] = $masData->patient_id;
			}
			
			if( $pFirstName && $pLastName )
			{
				$MASTER_NAME_ARR[$pFirstName][$pLastName][$pZip][$pDOB] = $masData->patient_id;
			}
		}
		//echo'<pre>';
		//echo 'Count is '.count($MASTER_NAME_ARR).'<br>';
		//print_r($MASTER_IMW_ARR);
		//print_r($MASTER_NAME_ARR);die();
		
		
		//Get Child Patient Data Detail
		$childStr = "Select ".$FieldsList." From ".$childDB.".patient_data_tbl Order By patient_id ASC ";
		$childQuery = imw_query($childStr) or $msg_info[] = $childStr.imw_error();
		while($childData = imw_fetch_assoc($childQuery))
		{
			$imwPatientId=	trim($childData['imwPatientId']);
			$pFirstName	=	trim(strtolower($childData['patient_fname']));
			$pMiddleName=	trim(strtolower($childData['patient_mname']));
			$pLastName	=	trim(strtolower($childData['patient_lname']));
			$pZip		=	trim(strtolower($childData['zip']));
			$pDOB		=	trim($childData['date_of_birth']);
			
			$master_id='';
			//if patient_data match then update in child database
			if( $MASTER_IMW_ARR[$imwPatientId]  || $MASTER_NAME_ARR[$pFirstName][$pLastName][$pZip][$pDOB] )
			{
				if($MASTER_IMW_ARR[$imwPatientId])
				{
					$master_id = $MASTER_IMW_ARR[$imwPatientId];	
				}
				else if($MASTER_NAME_ARR[$pFirstName][$pLastName][$pZip][$pDOB])
				{
					$master_id = $MASTER_NAME_ARR[$pFirstName][$pLastName][$pZip][$pDOB];
				}
				
				//patient data match with master table update child table ID with Master Table ID
				if($master_id && $childData['patient_id']!=$master_id )
				{
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
								$foreign_table_name = trim($data[2]);
								$foreign_column_name= trim($data[3]);
								
								if(trim($primary_table_name) && trim($primary_column_name)) {
									$primary_updt_qry = "UPDATE $childDB.$primary_table_name SET $primary_column_name ='".$master_id."' WHERE $primary_column_name = '".$childData["patient_id"]."'";
									imw_query($primary_updt_qry) or $msg_info[] = 'PRIMARY = '.$primary_updt_qry.imw_error();
								}
								if(trim($foreign_table_name) && trim($foreign_column_name)) {
									$foreign_updt_qry = "UPDATE $childDB.$foreign_table_name SET $foreign_column_name = '".$master_id."' WHERE $foreign_column_name = '".$childData["patient_id"]."'";
									imw_query($foreign_updt_qry) or $msg_info[] = 'FOREIGN = '.$foreign_updt_qry.imw_error();
								}
							}
							$row++;
						}
					}
					
					/*
					foreach($TABLE_TO_UPDATE as $TABLE=>$FIELD)
					{
						$qry =  "Update ".$childDB.".".$TABLE." Set ".$FIELD."='".$master_id."' Where ".$FIELD."='".$childData['patient_id']."' ";
						imw_query($qry)or $msg_info[] = $qry.imw_error();
					}
					*/
					
					
				}
				$RECORD_UPDATE++;	
				
			}
			
			else//if Patient Data does not exist in master table then insert into master table
			{
				unset($q_str);
				foreach($childData as $key=>$val)
				{
					$val = trim($val);
					$q_str.=($q_str) ? " ,".$key."='".addslashes($val)."'"	:	$key."='".addslashes($val)."'";		
				}
				
				if($q_str){
					$qry_insert="Insert Into ".$masterDB.".patient_data_tbl Set $q_str";
					$res_insert=imw_query($qry_insert)or $msg_info[] = $qry_insert.imw_error();
					if($res_insert){
						$RECORD_INSERT++;
					}
				}
			}
		}
		
		
		$ptQry = "SELECT patient_in_waiting_id, patient_id, drOfficePatientId FROM ".$childDB.".patient_in_waiting_tbl ORDER BY patient_id ASC";
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
		
		$msg_info[] = "<br><br><b> Merge Patient Data Completed</b><br/>$RECORD_UPDATE Records Updated and $RECORD_INSERT Inserted.";
	}
	else
	{
		$msg_info[] = "<br><br><b> Merge Patient Data Not Completed</b><br/>Database Not Found";		
	}	
?>
<html>
<head>
<title>Merge Patient Data </title>
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