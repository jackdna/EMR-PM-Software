<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
include_once $GLOBALS['srcdir'].'/classes/work_view/Merge_patient_db.php';

//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors",1);

$Merge_patient_db = new Merge_patient_db;
$Merge_patient_db->merge_requested_patients($_POST);



?>
<html>
<head>
<title>Change patient in chart</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>

	<h2>Move chart note to another patient</h2>
	<form name="frm" action="" method="post">
		Merge Patient ID:&nbsp;&nbsp;&nbsp;<input type="type" name="merge_patient_id" value=""><br/><br/>
		Merge Form ID:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="type" name="merge_form_id" value=""><br/><br/>
		This Patient ID:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="type" name="this_patient_id" value=""><br/><br/>
				
		Commit: <input type="checkbox" name="change_pt" value="1"><br/><br/>
		<input type="submit" name="hid_action" value="GO!"><br/>
	</form>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
	
</body>
</html>