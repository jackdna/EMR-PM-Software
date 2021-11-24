<?php
/*require_once("../common/functions.inc.php");
require_once("../main/patient_functions.php");
require_once("../main/main_functions.php");

*/
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/msgConsole.php');
$msgConsoleObj = new msgConsole();
#Provider UserName	
$authUserID=$_SESSION['authUserID'];
#patient_id 
$patient_id=$_SESSION["patient"]; 
//Provider Name
$sql=imw_query("SELECT * FROM users WHERE id='$authUserID'");		
$row = imw_fetch_assoc($sql);	
if($row != false){
	//Provider Name
	$providerName = $row["fname"].' '.$row["lname"].' '.$row["mname"];		 
	$facility_id=$row['default_facility'];	
	//User Type
	$providerType = $msgConsoleObj->operator_type;
	//Facility Name
	if($facility_id != ""){
		$sql = imw_query("select * from facility where id='$facility_id'");
		$row =imw_fetch_assoc($sql);
		if($row != false){
			$providerFacility= $row['name'];				
		}	 
	}		  		
}
//Access Rights
if(($providerType != 1) && (core_check_privilege(array("priv_admin")) == false)){
	//Sorry No Access right
	echo "<script>top.fAlert('You donot have access rights for this page.');</script>";exit;
}
$msg="";
if($_REQUEST['delid']<>""){
	$res1 = imw_query("Delete from common_phrases where phrase_id='".$_REQUEST['delid']."'");
	if($res1<>""){
		echo "<script>top.fAlert('Record deleted');location.href('smart_phrase_frm.php') </script>";
	}else{
		$msg="";
	}
}
$phrase1=$_REQUEST['phrase'];
if(($_REQUEST['sbmt']<>"" || $phrase1<>"") &&  $_REQUEST['editid']==""){
	$qry3 = "Select * from common_phrases where phrase='$phrase1' AND (providerID='0' OR providerID='$authUserID')";
	$res3 = imw_query($qry3);
	if(imw_num_rows($res3)<=0){
		$phrase1 = mb_convert_encoding($phrase1,'HTML-ENTITIES', 'UTF-8');
		$qry = "insert into common_phrases set phrase='".addslashes($phrase1)."', providerID='$authUserID', date_time='".date("Y-m-d H:i:s")."'";
		imw_query($qry);
		echo "<script>top.fAlert('Saved successfully');
		window.parent.load_link_data('smart_phrases');
		</script>";
		exit;
	}else{
		echo "<script>top.fAlert('Record already Exists'); location.href('smart_phrase_frm.php'); </script>";exit;
	}
}
if(($_REQUEST['sbmt']<>"" || $phrase1<>"") && $_REQUEST['editid']<>""){
	//$qry11 = "Select * from common_phrases where phrase='$phrase1' and phrase_id!='".$_REQUEST['editid']."'";
	$res11 = imw_query($qry11);
	if(imw_num_rows($res11)<=0){
		$phrase1 = mb_convert_encoding($phrase1,'HTML-ENTITIES', 'UTF-8');
		$qry12="update common_phrases set phrase='".addslashes($phrase1)."', providerID='$authUserID', 
				date_time='".date("Y-m-d H:i:s")."' where phrase_id='".$_REQUEST['editid']."'";
		$res23 = imw_query($qry12);
		if($res23<>""){
			echo "<script>top.fAlert('Record updated');
				window.parent.load_link_data('smart_phrases');
			 </script>";
			 exit;
		}else{
			echo "<script>top.fAlert('Record already Exists'); location.href('smart_phrase_frm.php'); </script>";exit;
		}
	}else{
		echo "<script>top.fAlert('Record already Exists'); location.href('smart_phrase_frm.php'); </script>";exit;
	}
}

if($_REQUEST['editid']<>""){
	$qry4 = "Select * from common_phrases where phrase_id='".$_REQUEST['editid']."'";
	$res4 = imw_query($qry4);
	if(imw_num_rows($res4)>0){
		$row4 = imw_fetch_array($res4);
		$phrase2 = $row4['phrase'];
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>imwemr</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo $GLOBALS['webroot'];?>/library/messi/messi.css" rel="stylesheet" type="text/css">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" rel="stylesheet" type="text/css">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/physician_console.css" rel="stylesheet" type="text/css">


<script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/jquery.mCustomScrollbar.concat.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/messi/messi.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/common.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/console.js"></script>

<script type="text/javascript">

	function chkform(){
		if(document.console_phrase.phrase.value==""){
			top.fAlert("Please enter phrase");
			document.console_phrase.phrase.focus();
			return false;
		}
		return true;
	}
	
</script>
</head>
<body>
<div class="section">
	<div class="row">
    	<div class="col-sm-12 grythead">Phrase</div>
    </div>
	<div class="row pt10" style="padding-bottom:10px">
	<form method="post" name="console_phrase" action="smart_phrase_frm.php" onSubmit="return chkform();" style="margin:0px;">
		<input type="hidden" name="editid" value="<?php echo $_REQUEST['editid'];?>">
		<div class="col-sm-2"><label>Phrase:</label></div>
		<div class="col-sm-8"><input type="text" class="form-control" name="phrase" value="<?php echo $phrase2;?>" /></div>
		<div class="col-sm-2"><button type="submit" title="Save" class="btn btn-success" id="sbmt" name="sbmt" value="Save">Save</button></div>
	</form>
    </div>
</div>
</body>
</html>