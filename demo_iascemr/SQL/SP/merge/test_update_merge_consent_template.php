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
		imw_query("CREATE TABLE $childDB.consent_forms_template_bak_".date("d_m_Y")." AS (SELECT * FROM $childDB.consent_forms_template)") or $msg_info[] = imw_error();
		
	$RECORD_UPDATE=$RECORD_INSERT=0;
	$TABLE_TO_UPDATE = array(
							"consent_forms_template"=>"consent_id",
							"consent_multiple_form"=>"consent_template_id",
							"consent_form_signature"=>"consent_template_id",
						  	);
	
	//Get Master Consent Forms Template Detail
	$mastStr = "Select * From ".$masterDB.".consent_forms_template Where consent_name <> '' Order By consent_id Asc ";
	$masQuery = imw_query($mastStr) or $msg_info[] = $mastStr.imw_error();
	
	while($masData = imw_fetch_object($masQuery))
	{
		$MASTER_NAME_ARR[$masData->consent_category_id][strtolower($masData->consent_name)]= $masData->consent_id;
	}//echo'<pre>';
	//print_r($MASTER_NAME_ARR);die();
	
	//Get Child Consent Forms Template Detail
	$childStr = "Select * From ".$childDB.".consent_forms_template Order By consent_id Asc";
	$childQuery = imw_query($childStr) or $msg_info[] = $childStr.imw_error();
	
	while($childData = imw_fetch_assoc($childQuery)){
			$master_id='';
			//if consent form template match then update in child database
			if($MASTER_NAME_ARR[$childData['consent_category_id']][strtolower($childData['consent_name'])])
			{
				$master_id = $MASTER_NAME_ARR[$childData['consent_category_id']][strtolower($childData['consent_name'])];
				//template match with master table update child table ID with Master Table ID
				if($childData['consent_id']!=$master_id)
				{
					foreach($TABLE_TO_UPDATE as $TABLE=>$FIELD)
					{
						$qry =  "Update ".$childDB.".".$TABLE." Set ".$FIELD."='".$master_id."' Where ".$FIELD."='".$childData['consent_id']."' ";
						imw_query($qry) or $msg_info[] = $qry.imw_error();
					}
				}
				$RECORD_UPDATE++;	
			}
			else//if Consent Form Template does not exist in master table then insert into master table
			{
				unset($q_str);
				foreach($childData as $key=>$val)
				{
					if( $key == 'consent_delete_status')  $val = 'true';
					$q_str.=($q_str) ? " ,".$key."='".addslashes($val)."'"	:	$key."='".addslashes($val)."'";		
				}
				
				if($q_str){
					$qry_insert="Insert Into ".$masterDB.".consent_forms_template Set $q_str";
					$res_insert=imw_query($qry_insert) or $msg_info[] = $qry_insert . imw_error();
					if($res_insert){
						$RECORD_INSERT++;
					}
				}
			}
		}
	
		$msg_info[] = "<br><br><b> Merge Completed</b><br/>$RECORD_UPDATE Records Updated and $RECORD_INSERT Inserted.";
	}else {
		$msg_info[] = "<br><br><b> Merge Consent Form Template Not Completed</b><br/>Database Not Found";			
			
	}
?>
<html>
<head>
<title>Merge Consent Form Template Data </title>
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