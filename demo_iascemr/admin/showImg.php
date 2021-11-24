<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include("adminLinkfile.php");
?>
<html>
<head>
<title>Surgerycenter Image</title>
<script>
function hideImg(obj){
	if(top.frames[0]) {
		if(top.frames[0].frames[0]) {
			if(top.frames[0].frames[0].document.getElementById(obj)) {
				top.frames[0].frames[0].document.getElementById(obj).style.display = 'none';	
			}
		}	
	}		
}
</script>
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<!-- <tr>
		<td align="right" style="cursor:hand;" onClick="return hideImg('imgDiv');" class="text_10b">Close</td>
	</tr> -->
	<tr>
		<td align="center"><img id="imageTD" src="logoImg.php?from=surgery_center"></td>
	</tr>
</table>
</body>
</html>
