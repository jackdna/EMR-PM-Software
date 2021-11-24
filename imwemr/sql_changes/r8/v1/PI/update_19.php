<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

if (!imw_query("DROP FUNCTION IF EXISTS get_operator_id;") ||
		!imw_query("CREATE FUNCTION get_operator_id() returns INT DETERMINISTIC
										BEGIN 
										DECLARE op_id INT(11);
										SET op_id=".($_SESSION['authId']?$_SESSION['authId']:1)."; 
										RETURN op_id;
										END;")) {
		$msg_info[] = "Function creation failed: (" . imw_errno() . ") " . imw_error();
}


if(count($msg_info)>0)
{
	$msg_info[] = '<br><br><b>Update 19 run FAILED!</b><br>'.imw_error();
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 19 run successfully!</b>";
	$color = "green";	
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 19 (PI)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<h3>Update 19 - Patient Information</h3>
<br><br>
        <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
            <?php echo(implode("<br>",$msg_info));?>
        </font>
</body>
</html>