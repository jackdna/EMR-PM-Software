<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql1="ALTER TABLE  `predefine_suppliesused` ADD  `qtyChkBox` TINYINT( 1 ) NOT NULL DEFAULT  '1', ADD  `deleted` TINYINT( 4 ) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="CREATE TABLE `procedure_supplies` ( `proc_supp_id` int(11) NOT NULL AUTO_INCREMENT, `procedure_id` int(11) NOT NULL, `supplies` text NOT NULL, `deleted` tinyint(4) NOT NULL, PRIMARY KEY (`proc_supp_id`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table used to store supplies used in procedures ';"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="CREATE TABLE `operatingroomrecords_supplies` ( `suppRecordId` int(11) NOT NULL AUTO_INCREMENT, `suppName` varchar(128) NOT NULL, `suppQtyDisplay` tinyint(1) NOT NULL, `suppChkStatus` tinyint(1) NOT NULL, `suppList` varchar(32) NOT NULL, `templateId` int(11) NOT NULL, `confirmation_id` int(11) NOT NULL, `displayStatus` tinyint(2) NOT NULL, PRIMARY KEY (`suppRecordId`) ) ENGINE=MyISAM  DEFAULT CHARSET=latin1;"; 
imw_query($sql1)or $msg_info[] = imw_error();


//Insert Static Supplies List into pre defined table
$array	=	array(
					'AmviscPlus'=>'Amvisc Plus',
					'Discovisc'=>'Duovisc',
					'Healon'=>'Healon',
					'Healon5'=>'Healon5',
					'HealonGV'=>'HealonGV',
					'Miochol'=>'Miochol',
					'Miostat'=>'Miostat',
					'Occucoat'=>'Occucoat',
					'Provisc'=>'Provisc',
					'TrypanBlue'=>'Trypan Blue',
					'Viscoat'=>'Viscoat',
					'XylocaineMPF'=>'Xylocaine MPF 1%');

foreach($array as $key=>$supplyName)					
{
	$supplyName	=	addslashes($supplyName);
	$chkQry	=	"Select suppliesUsedId From predefine_suppliesused Where name = '".$supplyName."' ";
	$chkSql	=	imw_query($chkQry) or $msg_info[] = imw_error();
	$chkCnt	=	imw_num_rows($chkSql);
	
	if($chkCnt == 0)
	{
		$qtyBox	=	($key <> 'TrypanBlue' && $key <> 'XylocaineMPF') ? 1 : 0 ;
		$sql1	=	"Insert Into predefine_suppliesused Set name = '".$supplyName."', qtyChkBox= ".$qtyBox.", deleted = 0 ";
		imw_query($sql1)or $msg_info[] = imw_error();
	}
}
			

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 72 Failed! </b>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 72 Success.</b>";
	$color = "green";			
}
?>

<html>
<head>
<title>Update 72</title>
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