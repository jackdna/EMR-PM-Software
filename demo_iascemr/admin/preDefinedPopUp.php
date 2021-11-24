<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include("common/linkfile.php");
include_once("../common/conDb.php");
include_once("logout.php");
include_once("../common/commonFunctions.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>welcome to Imedic ware</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script>
function showPreDefineFn(trId){
	document.getElementById("evaluationPreDefineDiv").style.display = 'block';	
}
function changeColorFn(obj, r){
	for(var i=1;i<=r; i++){
		document.getElementById("tr"+i).bgColor = "#CCCCCC";
	}
	obj.bgColor = "#BCD2B0";	
}
function getInnerHTMLFn(obj){
	alert(obj.innerHTML)
	document.getElementById("evaluationPreDefineDiv").style.display = 'none';
}
function closePreDefineDiv(){
	var divs=document.getElementsByTagName("DIV");
	for(i=0, x=divs.length; i<x; i++){
		(divs[i].id.match("PreDefineDiv")) ? divs[i].style.display = 'none' : null;
	}
}
</script>
</head>
<body marginheight="0" marginwidth="0">
	<div id="evaluationPreDefineDiv" style="position:absolute;background-color:#CCCCCC;left:250px;width:300px;height:200px;display:none; overflow:auto;">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="" align="right"><img src="../form_design/images/leftDark.gif" width="3" height="24"></td>
			<td align="right" bgcolor="#003300" width="98%"><img src="../form_design/images/chk_off1.gif" onClick="document.getElementById('evaluationPreDefineDiv').style.display='none';"></td>
			<td width="" align="left" valign="top"><img src="../form_design/images/rightDark.gif" width="3" height="24"></td>
		</tr>
		<?php
		$getRecordSetStr = "SELECT * FROM evaluation";
		$getRecordSetQry = imw_query($getRecordSetStr);
		$rows = imw_num_rows($getRecordSetQry);
		while($getRecordSetRows = imw_fetch_array($getRecordSetQry)){
			++$seq;
			?>
			<tr height="25" style="cursor:hand;" id="tr<?php echo $seq; ?>" onMouseOver="return changeColorFn(this, '<?php echo $rows; ?>')">
				<td></td>
				<td width="100%" style="padding-left:2px;" id="td<?php echo $seq; ?>" onClick="return getInnerHTMLFn(this)"><?php echo $getRecordSetRows['name']; ?></td>
				<td></td>
			</tr>
			<?php
		}
	?>
	</table>
	</div>
</body>
</html>
