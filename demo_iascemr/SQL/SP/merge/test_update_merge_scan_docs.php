<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	set_time_limit(0);
	include_once("../../../common/conDb.php");  //MYSQL CONNECTION
	include_once("test_update_merge_db_detail.php");  //DB Details
	
	if( $childDB )
	{
		if(!$_postfix[$childDB]) {exit('<span style="color:red">Please do config for postfix name for pdfFile Folder</span>');}
		
		$keyFolder = 'pdfFiles'.$_postfix[$childDB];
		$TABLE_TO_UPDATE = array(	'scan_upload_tbl' => array('f_key' => $_postfix[$childDB], 
																												 'p_key' => 'scan_upload_id',
																												 'f_path' => '../../../admin/pdfFiles'),
													'iolink_scan_consent' => array('f_key' => $_postfix[$childDB], 
																												 'p_key' => 'scan_consent_id', 
																												 'f_path' => '../../../../'.$iolinkDirectoryName.'/admin/pdfFiles'));
		
		foreach($TABLE_TO_UPDATE as $tblName => $key)
		{
			if( rename($key['f_path'],$key['f_path'].$key['f_key']) )
			{
				imw_query("CREATE TABLE $childDB.$tblName".date("d_m_Y")." AS (SELECT * FROM $childDB.$tblName)") or $msg_info[] = imw_error();
				$qry = "Update $childDB.$tblName SET `pdfFilePath` = REPLACE (pdfFilePath,'pdfFiles/','$keyFolder/') Where 1";
				$sql = imw_query($qry) or $msg_info[] = $qry.imw_error();
				$rows = imw_affected_rows();
				$msg_info[] = "<br><br><b> Scan Docs Folder Path Updated for $tblName : </b><br/>$rows Records Updated.";	
			}
			else
			{
				$msg_info[] = "<br><br><b> Scan Docs Folder Path Not Updated for $tblName.";	
			}
		}
			
	}else {
		$msg_info[] = "<br><br><b> Scan Docs Folder Path Update Not Completed</b><br/>Database Not Found";
	}
?>
<html>
<head>
<title>Scan Docs Folder Path Update </title>
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