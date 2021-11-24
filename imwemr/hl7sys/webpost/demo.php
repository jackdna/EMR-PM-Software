<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>FORM TO TEST MESSAGE POSTING</title>
</head>

<body style="margin:10px;">
<form action="index.php" method="POST" target="frame_result">
HL7 MESSAGE<BR />
Username: <input type="text" name="uname" value="" /><br />
Password: <input type="text" name="upass" value="" /><br />
<textarea style="width:90%; height:200px; border:2px solid #ccc;" name="data"></textarea>
<br /><br />
<input type="submit" value="Send Message" />
</form>
<hr /><BR />
HL7 RESPONSE<BR />
<iframe style="width:90%; height:250px; border:2px solid #ccc;" name="frame_result"></iframe>
</body>
</html>
