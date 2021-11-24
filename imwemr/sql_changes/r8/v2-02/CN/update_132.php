<?php
$ignoreAuth = true;
include_once(dirname(__FILE__)."/../../../../config/globals.php");

$msg_info=array();

/*
update logic --







4. copy comments from master to cl_comments and empty master table comment
	Take bak if there are comments to copy from master table
	1. contact master table bak up


	

Show stats of records of old master table + cl Commnets =  New cl_comments
it should be equal

'contactlensmaster' and 'cl_comments' tables

*/



/*
Check records
*/

$sql = " SELECT clws_id, cl_comment, clws_savedatetime, provider_id  FROM contactlensmaster where cl_comment != '' ORDER BY clws_id  ";
$result_master = imw_query($sql) or $msg_info[] = imw_error();

$master_records = imw_num_rows($result_master);

if($master_records > 0){
	
	
	//Bak master table
	$db_bk = "contactlensmaster_".date("Y_m_d_H_i_s");
	$sql = "CREATE TABLE ".$db_bk." LIKE contactlensmaster ";
	$result = imw_query($sql) or $msg_info[] = imw_error();	

	$sql = "INSERT INTO ".$db_bk." SELECT * FROM contactlensmaster ";
	$result = imw_query($sql) or $msg_info[] = imw_error();
	
	//Bak up Cl_comments
	//1. new cl_comments bak up
	$sql = " SELECT id  FROM cl_comments ORDER BY id  ";
	$result = imw_query($sql) or $msg_info[] = imw_error();
	$cl_comm_records = imw_num_rows($result);
	
	if($cl_comm_records>0){
		//1. new cl_comments bak up
		$db_bk_cl_comm = "cl_comments_".date("Y_m_d_H_i_s");
		$sql = "CREATE TABLE ".$db_bk_cl_comm." LIKE cl_comments";
		$result = imw_query($sql) or $msg_info[] = imw_error();	

		$sql = "INSERT INTO ".$db_bk_cl_comm." SELECT * FROM cl_comments";
		$result = imw_query($sql) or $msg_info[] = imw_error();
		
		//3. cl_comment empty (truncate) Id reset
		$sql = "TRUNCATE table cl_comments";
		$result = imw_query($sql) or $msg_info[] = imw_error();
	}	
	
	/*
	4. copy comments from master to cl_comments and empty master table comment
		Take bak if there are comments to copy from master table
		1. contact master table bak up
	*/
	//Copy from Master and empty cl_comments
	for($i=0; $row = imw_fetch_assoc($result_master); $i++){
	
		$comment = trim($row["cl_comment"]);
		if(!empty($comment)){
			$cl_sheet_id = $row["clws_id"];
			$created_on = $row["clws_savedatetime"];
			$created_by = $row["provider_id"];
			$delete_status = "0";
				
			$sql = "INSERT INTO cl_comments (id, cl_sheet_id, comment, created_on, created_by, delete_status) 
					VALUES (NULL, '".$cl_sheet_id."', '".imw_real_escape_string($comment)."', '".$created_on."', '".$created_by."', '".$delete_status."' ) ";	
			imw_query($sql) or $msg_info[] = imw_error();
			
			//Update empty cl_comments in master
			$sql = "UPDATE contactlensmaster SET cl_comment='' WHERE clws_id = '".$cl_sheet_id."'  ";
			imw_query($sql) or $msg_info[] = imw_error();
			
		}
		
	}	
	
	//5. copy from cl_comments to new table cl_comments
	if($cl_comm_records>0){
		$db_bk_cl_comm = " INSERT INTO cl_comments SELECT null, cl_sheet_id, comment, created_on, created_by, delete_status FROM ".$db_bk_cl_comm." ";
		imw_query($db_bk_cl_comm) or $msg_info[] = imw_error();
	}	
	
	//
	//End records Cl_comments
	$sql = " SELECT id  FROM cl_comments ORDER BY id  ";
	$result = imw_query($sql) or $msg_info[] = imw_error();
	$end_cl_comm_records = imw_num_rows($result);
	
	echo "Process Done: <br/> Master Records : ".$master_records." <br/> cl_comments records : ".$cl_comm_records." <br/> Final records in cl_comments : ".$end_cl_comm_records." ";
}

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 132  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 132  run successfully!</b>";
    $color = "green";
}



?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update 132: Contact Lens Comments import</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style>
	label{display:inline-block; width:100px; border:0px solid red;}
</style>
</head>
<body>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(@implode("<br>",$msg_info));?>
</font>

</body>
</html>