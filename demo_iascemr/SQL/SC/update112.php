<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

/***
*
*	Update To Lock Injection / Miscellaneous Procedure categories
* 
* If already exists then set isMisc/isInj Fields to 1 and 0 for rest
*
* otherwise Insert New record for Injection/Misc. Procedures Category
*
***/

$tbl	=	'procedurescategory';

$qry	=	"Select * From ".$tbl." Where name = 'Miscellaneous' ";
$sql	=	imw_query($qry) or die('Error at Line no. '.(__LINE__).': '.imw_error());
$cnt	=	imw_num_rows($sql);
if($cnt > 0 )
{
	$row = imw_fetch_object($sql);
	$rowID	=	$row->proceduresCategoryId;
	imw_query("Update ".$tbl." Set isMisc = '1' Where proceduresCategoryId = '".$rowID."' ");
	
}
else
{
	imw_query("Insert Into ".$tbl." Set name = 'Miscellaneous', isMisc = '1'  ");
	$rowID	=	imw_insert_id();
}

imw_query("Update ".$tbl." Set isMisc = '0' Where proceduresCategoryId <> '".$rowID."' ");

$rowID = '';

// Check for Injection Procedure
$qry	=	"Select * From ".$tbl." Where name = 'Injection' ";
$sql	=	imw_query($qry) or die('Error at Line no. '.(__LINE__).': '.imw_error());
$cnt	=	imw_num_rows($sql);
if($cnt > 0 )
{
	$row = imw_fetch_object($sql);
	$rowID	=	$row->proceduresCategoryId;
	imw_query("Update ".$tbl." Set isInj = '1' Where proceduresCategoryId = '".$rowID."' ");
	
}
else
{
	imw_query("Insert Into ".$tbl." Set name = 'Injection', isInj = '1'  ");
	$rowID	=	imw_insert_id();
}

imw_query("Update ".$tbl." Set isInj = '0' Where proceduresCategoryId <> '".$rowID."' ");





if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 112 Failed!</b><br>".$message."<br>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 112 Success.</b><br>".$message;
	$color = "green";			
}

?>

<html>
<head>
<title>Update 112</title>
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