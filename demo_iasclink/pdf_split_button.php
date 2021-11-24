<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
session_start();
?>
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<?php
	include_once("common/conDb.php");
	include("common/link_new_file.php");

?>
<body>
	<script>alert(top.iframePdfSplitId.document.getElementById('counter').value);</script>
	<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%" >	
		
		<tr class="text_10b" valign="top">
			<td align="center" valign="bottom">
				<?php //if($counter>1) {?>
				<a href="#" onClick="MM_swapImage('savePdfBtn','','images/save_onclick1.jpg',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('savePdfBtn','','images/save_hover1.jpg',1)"><img src="images/save.jpg" name="savePdfBtn" border="0" id="savePdfBtn" alt="save" onClick="top.iframePdfSplitId.document.frmPdfSplit.submit();"/></a>
				<img src="images/tpixel.gif" width="20">
				<a href="#" onClick="MM_swapImage('deletePdfSplitSelected','','images/delete_selected_click.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('deletePdfSplitSelected','','images/delete_selected_hover.gif',1)"><img src="images/delete_selected.gif"  name="deletePdfSplitSelected" id="deletePdfSplitSelected" style=" cursor:pointer; "  border="0"  alt="Delete" onClick="top.iframePdfSplitId.delPdfSplitFun(<?php echo ($counter-1);?>);"/></a>
				<?php //} ?>
				<img src="images/tpixel.gif" width="20">
				<a href="#" onClick="MM_swapImage('CloseBtnPdf','','images/close_click.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('CloseBtnPdf','','images/close_hover.gif',1)"><img src="images/close.gif" name="CloseBtnPdf" width="70" height="25" border="0" id="CloseBtnPdf" alt="Close" onClick="javascript:if(top.opener.top) { top.opener.top.iframeHome.iOLinkBookSheetFrameId.location.reload();}top.window.close();" /></a>
			</td>
		</tr>
	</table>		
</body>
</html>
