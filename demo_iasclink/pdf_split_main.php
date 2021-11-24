<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
session_start();
include_once("common/conDb.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>PDF Management</title>

<script>
window.focus();
</script>
<?php
$spec= "
</head>
<body>";

include("common/link_new_file.php");
$selDos = $_REQUEST['selDos'];
?>
<span id='pdfSplitAjaxLoadId' style="position:absolute; top:220px; left:310px; display:none;"><img src="images/ajax-loader5.gif" width="80" height="80"></span>
<table class="table_collapse">
	<tr><td style="height:2px;">&nbsp;</td></tr>
	<tr>
		<td class="text_10b alignCenter" style="height:20px;">
			Upload/Split PDF Files For <?php echo date('m-d-Y',strtotime($selDos));?>
            
		</td>
	</tr>
	
	<tr>
		<td style="height:100px;">
			<iframe name="iframePdfSplitUploadId" id="iframePdfSplitUploadId" src="pdf_split_upload.php?selDos=<?php echo $selDos;?>" frameborder="0" scrolling="0" style="width:100%; height:100px;" ></iframe>
		</td>
	</tr>
	<tr>
		<td style="height:490px;">
			<iframe name="iframePdfSplitId" id="iframePdfSplitId" src="pdf_split.php?selDos=<?php echo $selDos;?>"  frameborder="0" scrolling="0" style="width:100%; height:490px;"></iframe>
		</td>
	</tr>
</table>
</body>
</html>
