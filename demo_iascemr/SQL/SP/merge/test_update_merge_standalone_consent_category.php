<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	set_time_limit(0);
	include_once("../../../common/conDb.php");  //MYSQL CONNECTION
	
	imw_query("CREATE TABLE consent_category_bak_".date("d_m_Y")." AS (SELECT * FROM consent_category)") or $msg_info[] = imw_error();
	
	$RECORD_UPDATE=$RECORD_INSERT=0;
	
	//"consent_category"=>"category_id",
	$TABLE_TO_UPDATE = array(
							"consent_multiple_form"=>"consent_category_id",
							"iolink_consent_filled_form"=>"consent_category_id",
						  	);
	
		//Get Consent Category Duplicates Detail
		$Query	=	"SELECT group_concat(`category_id`) as RecordFound FROM `consent_category` Where category_name <> '' Group By `category_name` having count(`category_id`)>1 ORDER BY `category_name`, category_status ASC";
		$Sql	= 	imw_query($Query)or $msg_info[] = $Query.imw_error();
		$doubleCCArr	=	array();
		while($Row = imw_fetch_object($Sql))
		{
			$doubleCCArr[] = $Row->RecordFound;
		}
		//echo'<pre>';
		//echo 'Count is '.count($doubleCCArr).'<br>';
		//print_r($doubleCCArr);die();
		
		
		if(is_array($doubleCCArr) && count($doubleCCArr) > 0 )
		{
			foreach($doubleCCArr as $categoryIds)
			{
				$Query	=	"Select category_id From consent_category Where category_id In (".$categoryIds.") Order By category_status Asc, category_id Desc   ";
				$Sql	=	imw_query($Query) or $msg_info[] = $Query.imw_error();	
				$recordToKeepId = $recordToRemoveId = '';
				$counter = 0;
				while($Row = imw_fetch_object($Sql))
				{
					$counter++;
					if($counter == 1)
					{
						$recordToKeepId	=	$Row->category_id;
					}
					else
					{
						$recordToRemoveId = $Row->category_id;
					}
					
					if($recordToKeepId && $recordToRemoveId) 
					{
						unset($update_message);
						foreach($TABLE_TO_UPDATE as $TABLE=>$FIELD)
						{
							$qry =  "Update ".$TABLE." Set ".$FIELD."='".$recordToKeepId."' Where ".$FIELD."='".$recordToRemoveId."' ";
							imw_query($qry)or $msg_info[] = $update_message[] = $qry.imw_error();
						}
						if(count($update_message)  == 0 )
						{
							$qry =  "Delete From consent_category Where category_id = '".$recordToRemoveId."' ";
							imw_query($qry)or $msg_info[] = $qry.imw_error();
							$msg_info[]	= "Consent Category ID ".$recordToRemoveId ." is merged into Consent Category ID ".$recordToKeepId." Successfully.";
						}
						else
						{
							$msg_info[]	=	"Consent Category ID ".$recordToRemoveId ." is not deleted. Please Check !!!";
						}
							
					}
					
				}
				
			}
			
			//Get Duplicate Consent Category Data Detail
			
		}
		else
		{
			$msg_info[]	=	"No Duplicate Record Found.";	
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