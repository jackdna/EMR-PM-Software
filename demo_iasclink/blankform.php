<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgery Center EMR</title>
<link rel="stylesheet" href="css/style_surgery.css" type="text/css" />
<style>
.tst11b {font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px; font-weight:bold; color:#020202;   }
.tst11 {font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px; font-weight:normal; color:#020202;   }

.tst10b {font-family:Verdana, Arial, Helvetica, sans-serif; font-size:10px; font-weight:bold; color:#020202;   }
.tst10 {font-family:Verdana, Arial, Helvetica, sans-serif; font-size:10px; font-weight:normal; color:#020202;   }

</style>
</head>
<body>

<table class="table_collapse alignCenter" style="height:565px; background-color:#ECF1EA; border:none;" >
	<tr style="height:10px;">
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td class="tst11b alignCenter valignTop">Please click on left of any tree panel to view desired chart</td>
	</tr>
	<tr>
		<td class="alignLeft" style="padding-left:350px; padding-top:25px;">
			<div id="divSaveAlert" style="position:absolute;left:350px; top:220px; display:none;">
				<?php 
					$bgCol = '#BCD2B0';
					$borderCol = '#BCD2B0';
					include('saveDivPopUp.php'); 
				?>
			</div>
		</td>
	</tr>
	<tr>
		<td class="alignLeft" style="padding-left:200px; padding-top:25px;">
			<div id="divDeleteAlert" style="position:absolute;left:200; top:220; display:<?php if($_REQUEST['deleteAlert']=='true') { echo 'display'; }else { echo 'none';}?>;">
				<?php 
					$bgCol = '#BCD2B0';
					$borderCol = '#BCD2B0';
					$saveSuccessfullyMessage = "Record deleted successfully.";
					include('saveDivPopUp.php'); 
				?>
			</div>
		</td>
	</tr>
	
</table>
</body>
</html>