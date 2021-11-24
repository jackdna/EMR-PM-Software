<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

//DIRECTORY CREATED FOR TEMPLATES COMMON IMAGES PURPOSE LIKE "new_html2pdf" was in R7
$commonImagesDir = data_path().'gn_images';	

if(!is_dir($commonImagesDir)){		
	mkdir($commonImagesDir);
	$msg_info[] = "<br><br><b>Update 34 completed successfully.</b>";
	$color = "green";
}else{
	$msg_info[] = "<br><br><b>Directory already exists!! <br/><br/> Update 34 Failed!</b>";
	$color = "red";
} 

?>
<html>
<head>
<title>Update 34</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>