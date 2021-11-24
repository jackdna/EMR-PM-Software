<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$tbl		=	'operatingroomrecords';
$indexStart	=	isset($_REQUEST["c"])		?	$_REQUEST["c"]	:	0	;
$totalCount =	isset($_REQUEST['cn'])	?	$_REQUEST['cn']	:	''	;	
$fetchRecords	=	500;

if(empty($totalCount))
{
	// Creating a backup for operatingroomrecords table
	$sql1="CREATE TABLE ".$tbl."_bak_".date("d_m_Y")." AS (SELECT * FROM ".$tbl.")"; 
	imw_query($sql1)or $msg_info[] = imw_error();
	
	// Get Total Count Where surgeryEndTime Field contains value like 24:13:00
	$qry	=	"Select count(*) as totalRecords From ".$tbl." Where surgeryEndTime LIKE '24%'";
	$sql	=	imw_query($qry) or $msg_info[] = imw_error();
	$row	=	imw_fetch_assoc($sql);
	
	$totalCount	=	$row['totalRecords'];
}

if($totalCount > 0 )
{
		$qry	=	"Select operatingRoomRecordsId, surgeryEndTime From ".$tbl." Where surgeryEndTime LIKE '24%' LIMIT 0, ".$fetchRecords;
		$sql	=	imw_query($qry) or $msg_info[] = imw_error();
		if(imw_num_rows($sql)>0)
		{
			while($row	=	imw_fetch_assoc($sql))
			{
					$recordID	=	$row['operatingRoomRecordsId'];
					$old_surgeryEndTime	=	$row['surgeryEndTime'];
					list($hour,$minute,$second)	=	explode(':',$old_surgeryEndTime);
					$new_surgeryEndTime	=	((int)$hour-12).':'.$minute.':'.$second;
					
					$updtQry	=	"Update ".$tbl." Set surgeryEndTime = '".$new_surgeryEndTime."' Where operatingRoomRecordsId = '".$recordID."' ";
					imw_query($updtQry) or $msg_info[] = imw_error();;
					
					$indexStart++;
			
			}
			echo "<br>Process Done ".$indexStart." of ".$totalCount.'<br>';
			echo '<br><br>';	
			
			echo "<script>window.location.replace('?c=".$indexStart."&cn=".$totalCount."');</script >";
			exit;
		
		}
		
		else
		{
			echo "<br>Process Completed with ".$indexStart." updated record(s) out of ".$totalCount;	
		}
		
}

else
{
	echo "<br>Process Completed with ".$indexStart." updated record(s) out of ".$totalCount;	
}


$color = 'green';
if(count($msg_info)>0){ $color = 'red'; }
$msg_info[] = "Update Surgery End Time in Operating Room Records for previous record - run OK";

?>

<html>
<head>
<title>Update 109</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo(implode("<br>",$msg_info));?></font>
<?php
@imw_close();
}
?> 
</body>
</html>