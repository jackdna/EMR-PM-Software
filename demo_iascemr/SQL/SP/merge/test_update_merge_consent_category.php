<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	set_time_limit(0);
	include_once("../../../common/conDb.php");  //MYSQL CONNECTION
	include_once("test_update_merge_db_detail.php");  //DB Details
	
	if($masterDB && $childDB)
	{
		imw_query("CREATE TABLE $childDB.consent_category_bak_".date("d_m_Y")." AS (SELECT * FROM $childDB.consent_category)") or $msg_info[] = imw_error();
		
	$RECORD_UPDATE=$RECORD_INSERT=0;
	$TABLE_TO_UPDATE = array(
							"consent_category"=>"category_id",
							"consent_multiple_form"=>"consent_category_id",
							"iolink_consent_filled_form"=>"consent_category_id",
						  	);
	
	//Get Master Consent Categories Detail
	$mastStr = "Select * From ".$masterDB.".consent_category Where category_name <> '' Order By category_id Asc ";
	$masQuery = imw_query($mastStr) or $msg_info[] = $mastStr.imw_error();
	
	while($masData = imw_fetch_object($masQuery))
	{
		$MASTER_NAME_ARR[strtolower($masData->category_name)] = $masData->category_id;
	}//echo'<pre>';
	//print_r($MASTER_NAME_ARR);die();
	
	//Get Child Consent Categories Detail
	$childStr = "Select * From ".$childDB.".consent_category Order By category_id Asc";
	$childQuery = imw_query($childStr) or $msg_info[] = $childStr.imw_error();
	
	while($childData = imw_fetch_assoc($childQuery)){
			$master_id='';
			//if consent category match then update in child database
			if($MASTER_NAME_ARR[strtolower($childData['category_name'])])
			{
				$master_id = $MASTER_NAME_ARR[strtolower($childData['category_name'])];
				//category match with master table update child table ID with Master Table ID
				if($childData['category_id']!=$master_id)
				{
					foreach($TABLE_TO_UPDATE as $TABLE=>$FIELD)
					{
						$qry =  "Update ".$childDB.".".$TABLE." Set ".$FIELD."='".$master_id."' Where ".$FIELD."='".$childData['category_id']."' ";
						imw_query($qry) or $msg_info[] = $qry.imw_error();
					}
				}
				$RECORD_UPDATE++;	
			}
			else//if Consent Category does not exist in master table then insert into master table
			{
				unset($q_str);
				foreach($childData as $key=>$val)
				{
					$q_str.=($q_str) ? " ,".$key."='".addslashes($val)."'"	:	$key."='".addslashes($val)."'";		
				}
				
				if($q_str){
					$qry_insert="Insert Into ".$masterDB.".consent_category Set $q_str";
					$res_insert=imw_query($qry_insert) or $msg_info[] = $qry_insert . imw_error();
					if($res_insert){
						$RECORD_INSERT++;
					}
				}
			}
		}
	
		$msg_info[] = "<br><br><b> Merge Completed</b><br/>$RECORD_UPDATE Records Updated and $RECORD_INSERT Inserted.";
	}else {
		$msg_info[] = "<br><br><b> Merge Consent Category Not Completed</b><br/>Database Not Found";			
			
	}
?>
<html>
<head>
<title>Merge Consent Category Data </title>
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