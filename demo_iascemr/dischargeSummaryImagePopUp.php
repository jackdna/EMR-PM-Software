<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
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
	<!-- <tr>
		<td align="right" style="cursor:hand;" onClick="return hideImgNew('imgDiv');" class="text_10b"><b>Close</b></td>
	</tr> -->
	<tr>
		<td align="center"><img border="0" src="admin/<?php echo $varFileName;?>?from=discharge_summary_sheet&id=<?php echo $id; ?>"></td>
	</tr>
</table>
</body>
</html>
