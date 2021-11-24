<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include("adminLinkfile.php");
include_once("funcSurgeryCenter.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;

$category_id = $_REQUEST['procCatListId'];
if($_REQUEST['sbmt']){
	$arrayRecord['name'] = $_REQUEST['procedure'];
	$arrayRecord['catId'] = $_REQUEST['procCatListId'];
	$arrayRecord['code'] = $_REQUEST['procedureCode'];
	if($_REQUEST['procedureCategoryId']){
		$procedureCategoryId = $_REQUEST['procedureCategoryId'];
		$objManageData->updateRecords($arrayRecord, 'procedures', 'procedureId', $procedureCategoryId);
	}else{	
		$objManageData->addRecords($arrayRecord, 'procedures');
	}
}
if($_REQUEST['delId']){
	$category_id = $_REQUEST['procCatId'];
	$objManageData->delRecord('procedures', 'procedureId', $_REQUEST['delId']);
}

$procedureCategoryDetails = $objManageData->getArrayRecords('procedurescategory');
$procedureDetails = $objManageData->getArrayRecords('procedures', 'catId' , $category_id);
?>
<html>
<head>
<title>Procedures</title>
<link rel="stylesheet" href="adminStyle.css" type="text/css" />
<script>
function chkFrm(){
	var flag = 0;
	var msg = "Please fill following fields."
	var objFrm = document.subProceduresFrm;
	f1 = objFrm.procCatListId.value;
	f2 = objFrm.procedure.value;
	f3 = objFrm.procedureCode.value;
	if(f1==''){		msg += '\n\t�Procedure Category.'; ++flag;	}
	if(f2==''){		msg += '\n\t�Procedure.'; ++flag;	}
	if(f3==''){		msg += '\n\t�Procedure Code.'; ++flag;	}
	if(flag>0){
		alert(msg)
		return false;
	}
}
function editProcedureFn(proId, catId, name, code){
	var objFrm = document.subProceduresFrm;
	objFrm.procCatListId.value = catId;
	objFrm.procedure.value = name;
	objFrm.procedureCode.value = code;
	objFrm.procedureCategoryId.value = proId;
}
function delProcedureFn(procId, catId){
	var ask = confirm("Are you sure to delete the record.");
	if(ask==true){	
		document.delProcedureFrm.delId.value = procId;
		document.delProcedureFrm.procCatId.value = catId;
		document.delProcedureFrm.submit();	
	}
}
function submitFrmFn(){
	document.subProceduresFrm.submit();
}
</script>
</head>
<body><br>
<table border="0" cellpadding="0" cellspacing="0" width="800" align="center">
	<form name="subProceduresFrm" action="subProcedure.php" method="post" onSubmit="return chkFrm()">
	<input type="hidden" name="procedureCategoryId" value="">
	<tr>
		<td align="center" bgcolor="#BCD2B0">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td class="text_10" align="left">Procedures Cateory:</td>
					<td class="text_10" align="left">
						<select name="procCatListId" class="text_10 all_border" style="width:175px;" onChange="return submitFrmFn();">
							<option value="">Select</option>
							<?php
							foreach($procedureCategoryDetails as $procCat){
								$catId = $procCat->proceduresCategoryId;
								$catName = $procCat->name;
								?>
								<option value="<?php echo $catId; ?>"<?php if($category_id == $catId) echo "SELECTED"; ?>><?php echo $catName; ?></option>
								<?php
							}
							?>
						</select>
					</td>
					<td width="5"></td>
					<td class="text_10" align="left">Procedure:</td>
					<td class="text_10" align="left"><input name="procedure" type="text" class="text_10" size="25"></td>
					<td width="5"></td>
					<td class="text_10" align="left">Procedure Code:</td>
					<td class="text_10" align="left"><input name="procedureCode" type="text" class="text_10" size="8"></td>
					<td width="5"></td>
					<td class="text_10" align="right"><input type="submit" name="sbmt" class="button" style="width:75px;" value="Save"></td>
				</tr>
			</table>
		</td>
	</tr>
	</form>	
	<tr height="10">
		<td></td>
	</tr>
	<tr>
		<td align="center">
			<table border="0" cellpadding="0" cellspacing="0" width="500" align="center">
				<tr>
					<td width="1" align="right"><img src="../images/left.gif" width="3" height="24"></td>
					<td align="left" bgcolor="#BCD2B0" class="text_10b" width="10" style="padding-left:5px;">S.No.</td>
					<td width="1" bgcolor="#003300"></td>
					<td align="left" bgcolor="#BCD2B0" class="text_10b" style="padding-left:5px;">Procedure</td>
					<td width="1" bgcolor="#003300"></td>
					<td align="left" bgcolor="#BCD2B0" width="100" class="text_10b" style="padding-left:5px;">Code</td>
					<td width="1" bgcolor="#003300"></td>
					<td align="left" bgcolor="#BCD2B0" class="text_10b" width="85" style="padding-left:5px;">Function</td>
					<td width="1" align="left" valign="top"><img src="../images/right.gif" width="3" height="24"></td>
				</tr>
				<?php
				if(!$procedureDetails){
					?>
					<tr>
						<td colspan="9" class="text_10b" align="center">No Record Found.</td>
					</tr>
					<?php
				}else{
					foreach($procedureDetails as $proc){
						++$seq;
						?>
						<tr height="20">
							<td width="1" align="right"></td>
							<td align="left" class="text_10" style="padding-left:5px;" bgcolor="<?php if(($seq%2)==0) echo "$BCD2B0"; else echo "#FFFFFF"; ?>"><?php echo $seq; ?>.</td>
							<td width="1"></td>
							<td align="left" class="text_10" style="padding-left:5px;" bgcolor="<?php if(($seq%2)==0) echo "$BCD2B0"; else echo "#FFFFFF"; ?>"><?php echo $proc->name; ?></td>
							<td width="1"></td>
							<td align="left" class="text_10" style="padding-left:5px;" bgcolor="<?php if(($seq%2)==0) echo "$BCD2B0"; else echo "#FFFFFF"; ?>"><?php echo $proc->code; ?></td>
							<td width="1"></td>
							<td align="left" class="text_10b" style="padding-left:5px;" bgcolor="<?php if(($seq%2)==0) echo "$BCD2B0"; else echo "#FFFFFF"; ?>">
							<a class="black" href="javascript:editProcedureFn('<?php echo $proc->procedureId; ?>', '<?php echo $proc->catId; ?>', '<?php echo $proc->name; ?>', '<?php echo $proc->code; ?>')">EDIT</a>
							/
							<a class="black" href="javascript:delProcedureFn('<?php echo $proc->procedureId; ?>', '<?php echo $proc->catId; ?>')">DEL</a>
							</td>
							<td width="1"></td>
						</tr>
						<?php
					}
				}
				?>
			</table>
		
		</td>
	</tr>
</table>
	<form name="delProcedureFrm" action="subProcedure.php" method="post">
		<input type="hidden" name="delId" value="">
		<input type="hidden" name="procCatId" value="">		
	</form>
</body>
</html>
