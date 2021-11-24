<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
$id = $_REQUEST['id'];
$imgNmbr = $_REQUEST['imgNmbr'];
$varFileName = "logoImg.php";

if($imgNmbr == "secondImage") {
	$varFileName = "logoImg2.php";
}
?>
<html>
<head>
<title>Image</title>
<script>
function hideImg(obj){
	top.frames[0].document.getElementById(obj).style.display = 'none';
}
function hideImgNew(obj){
	top.frames[0].frames[0].document.getElementById(obj).style.display = 'none';
}
</script>
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#999999">
	<tr>
		<td align="center"><img border="0" src="<?php echo $varFileName;?>?from=iolink_insurance_card&id=<?php echo $id; ?>"></td>
	</tr>
</table>
</body>
</html>
