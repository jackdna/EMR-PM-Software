<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include("../../common/conDb.php");

//RENAME TABLE  `scemr`.`superbill` TO  `scemr`.`superbill_tbl` ;
$sql1="CREATE TABLE `superbill_tbl` (
  `superbill_id` int(11) NOT NULL AUTO_INCREMENT,
  `confirmation_id` int(11) NOT NULL,
  `cpt_id` int(11) NOT NULL,
  `cpt_code` varchar(64) NOT NULL,
  `dxcode_icd10` text NOT NULL,
  `dxcode_icd9` text NOT NULL,
  `quantity` int(11) NOT NULL,
  `modifier1` varchar(64) NOT NULL,
  `modifier2` varchar(64) NOT NULL,
  `modifier3` varchar(64) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_on` datetime NOT NULL,
  PRIMARY KEY (`superbill_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="CREATE TABLE `modifiers` (
  `modifierId` int(11) NOT NULL AUTO_INCREMENT,
  `modifierCode` varchar(32) NOT NULL,
  `practiceCode` varchar(32) NOT NULL,
  `description` varchar(767) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`modifierId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;";
imw_query($sql1)or $msg_info[] = imw_error();


//START IMPORT ALL MODIFIERS FROM iASC
$sxEmrModArr = array();
$getSxEmrModRes = imw_query("SELECT modifierCode, practiceCode, description FROM modifiers WHERE deleted='0'");
if(imw_num_rows($getSxEmrModRes)>0) {
	while($getSxEmrModRow = imw_fetch_array($getSxEmrModRes)) {
		$sxEmrModArr[] = $getSxEmrModRow['practiceCode'];
	}
}

imw_close($link); //CLOSE SURGERYCENTER CONNECTION
include("../../connect_imwemr.php"); // imwemr connection
$getIdocModRes = imw_query("SELECT modifier_code, mod_prac_code, mod_description FROM modifiers_tbl WHERE delete_status='0' ORDER BY modifiers_id");
if(imw_num_rows($getIdocModRes)>0) {
	while($getIdocModRow = imw_fetch_array($getIdocModRes)) {
		$modifier_code = $getIdocModRow['modifier_code'];
		$mod_prac_code = $getIdocModRow['mod_prac_code'];
		$mod_description = $getIdocModRow['mod_description'];
		if(in_array($mod_prac_code,$sxEmrModArr)) {
			//DO NOT UPDATE	
		}else {
			imw_close($link_imwemr); //CLOSE IMWEMR CONNECTION
			include("../../common/conDb.php");  //SURGERYCENTER CONNECTION

			$insModQry = "INSERT INTO modifiers SET 
							modifierCode 	= '".addslashes($modifier_code)."',
							practiceCode 	= '".addslashes($mod_prac_code)."',
							description 	= '".addslashes($mod_description)."'
							";	
			$insModRes = imw_query($insModQry)or $msg_info[] = imw_error();

			imw_close($link); //CLOSE SURGERYCENTER CONNECTION
			include("../../connect_imwemr.php"); // imwemr connection
		
		}
	}
}
imw_close($link_imwemr); //CLOSE IMWEMR CONNECTION
include("../../common/conDb.php");  //SURGERYCENTER CONNECTION

//END IMPORT ALL MODIFIERS FROM iASC




if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 95 Failed! </b>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 95 Success.</b>";
	$color = "green";			
}
?>

<html>
<head>
<title>Update 95</title>
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