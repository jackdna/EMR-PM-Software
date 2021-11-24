<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../globalsSurgeryCenter.php");
include("adminLinkfile.php");
include_once("funcSurgeryCenter.php");
if($_REQUEST['delId']){
	$delMedicationId = $_REQUEST['delId'];
	$delStr = "DELETE FROM preopmedicationorder WHERE preOpMedicationOrderId = '$delMedicationId'";
	$delQry = imw_query($delStr);
	
	
	?>
	<script>top.frames[0].frames[0].location.href = "preOpMediOrder.php"</script>
	<?php
	header("location:preOpMediOrderList.php");
}
?>
<html>
<head>
<title>Pre-Op Medication Order List</title>
	<style>
		form{margin:0px;}
		a.black:hover{color:"Red";	text-decoration:none;}
	</style>
</head>
<script>
	function delMedicationFn(id){
		var ask = confirm("Are you sure to delete the record!");
		if(ask==true){
			var framePath = top.frames[0].frames[0].frames[0];
			framePath.location.href = "preOpMediOrderList.php?delId="+id;
		}
	}
	function editMedicationFn(id, mediName, strength, directions){
		var framePath = top.frames[0].frames[0];
		framePath.document.forms[0].elements[0].value = id;
		framePath.document.forms[0].elements[1].value = mediName;
		framePath.document.forms[0].elements[2].value = strength;
		framePath.document.forms[0].elements[3].value = directions;
	}
	function addNewFn(){
		
	}
</script>
<body topmargin="0" leftmargin="5" rightmargin="0">
<center><br>
	<table width="835" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="13" align="right"><img src="../images/left.gif" width="3" height="24"></td>
			<td width="217" align="left" valign="middle" bgcolor="#BCD2B0" class="text_10b" style="padding-left:10px;">Medication Name</td>
			<td width="1" bgcolor="#003300"></td>
			<td width="164" align="left" valign="middle" bgcolor="#BCD2B0" class="text_10b" style="padding-left:10px;">Strength</td>
			<td width="1" bgcolor="#003300"></td>
			<td width="262" align="left" valign="middle" bgcolor="#BCD2B0" class="text_10b" style="padding-left:10px;">Directions</td>
			<td width="1" bgcolor="#003300"></td>
			<td width="132" align="left" valign="middle" bgcolor="#BCD2B0" class="text_10b" style="padding-left:10px;">Function</td>			
			<td width="16" align="left" valign="top"><img src="../images/right.gif" width="3" height="24"></td>
		</tr>
		<?php
		$getDtaStr = "SELECT * FROM preopmedicationorder";
		$getDtaQry = imw_query($getDtaStr);
		$rowsCount = imw_num_rows($getDtaQry);
		while($getDtaRows = imw_fetch_assoc($getDtaQry)){
			extract($getDtaRows);
			++$seq;
			?>
			<tr height="20">
				<td align="right"></td>
				<td align="left" valign="middle" class="text_10" style="padding-left:10px;" bgcolor="<?php if(($seq%2)==0) echo "$BCD2B0"; else echo "#FFFFFF"; ?>"><?php echo $medicationName; ?></td>
				<td></td>
				<td align="left" valign="middle" class="text_10" style="padding-left:10px;" bgcolor="<?php if(($seq%2)==0) echo "$BCD2B0"; else echo "#FFFFFF"; ?>"><?php echo $strength; ?></td>
				<td></td>
				<td align="left" valign="middle" class="text_10" style="padding-left:10px;" bgcolor="<?php if(($seq%2)==0) echo "$BCD2B0"; else echo "#FFFFFF"; ?>"><?php echo $directions; ?></td>
				<td></td>
				<td align="left" valign="middle" class="text_10b" bgcolor="<?php if(($seq%2)==0) echo "$BCD2B0"; else echo "#FFFFFF"; ?>" style="padding-left:10px;">
					<a class="black" href="javascript:editMedicationFn('<?php echo $preOpMedicationOrderId; ?>', '<?php echo $medicationName; ?>', '<?php echo $strength; ?>', '<?php echo $directions; ?>');">EDIT </a>
					
					<a class="black" href="javascript:delMedicationFn('<?php echo $preOpMedicationOrderId; ?>');">DEL</a>
				</td>			
				<td align="left"></td>
			</tr>
			<?php
		}
		if($rowsCount<=0){
			?>
			<tr>
				<td align="center" colspan="4" class="text">No record found.</td>
			</tr>
			<?php
		}
		?>
  </table>
</center>
</body>
</html>
