<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<form name="test" id="test">
Confirmation ID: <input type="number" name="txt_confirmation_id" id="txt_confirmation_id" value="<?php echo $_GET['txt_confirmation_id'];?>" />

<div style="width:150px; display:inline-block"><input type="checkbox" name="genmsg" id="genmsg" value="1" />Generate Msg</div>

<input type="submit" style="margin-left:50px" value="SUBMIT" name="showdemomsg" id="showdemomsg" />
</form>
<hr />
<?php
if(isset($_GET['showdemomsg'])){
	$pConfId = trim($_GET['txt_confirmation_id']);
	if(!isset($_GET['genmsg']) || intval($_GET['genmsg'])!=1)
	$its_demo_msg_show_only = true;
	
	include_once('dft_hl7_generate.php');
}
?>
</body>
</html>