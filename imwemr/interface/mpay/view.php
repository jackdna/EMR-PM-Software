<?php
	$ignoreAuth = true;
	require_once("../../config/globals.php"); 
	include_once("../../library/class.mpay.php");
	$objMpay = new mPay;
	$html = $objMpay->mpay_print_log();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>mPay Log</title>
<link rel="stylesheet" type="text/css" href="../themes/default/common.css" />
<style type="text/css">
.result_data td{vertical-align:top;}
</style>
</head>
<body>
<?php echo $html;?>

</body>
</html>