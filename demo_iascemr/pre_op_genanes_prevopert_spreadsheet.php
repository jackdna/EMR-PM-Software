<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
include("common/linkfile.php");
include_once("admin/classObjectFunction.php");
$objectMenageData = new manageData;
$patient_id = $_REQUEST['patient_id'];
$ascId = $_REQUEST['ascId'];
$pConfId = $_REQUEST['pConfId'];
$sprd_prevOperations = $_REQUEST["sprd_prevOperations"]; 

if($_REQUEST['submitMe']){
	$prevOperationName = $_REQUEST['prevOperationName'];
	$prevOperationreason = $_REQUEST['prevOperationreason'];
	$prevOperationId = $_REQUEST['prevOperationId'];
	foreach($prevOperationName as $Key => $prevOperations){
		if($prevOperations!=''){
			$prevOperationsArr['confirmation_id'] = $pConfId;
			//$prevOperationsArr['ascId'] = $ascId;
			$prevOperationsArr['patient_id'] = $patient_id;
			$prevOperationsArr['previous_operation_name'] = $prevOperations;
			$prevOperationsArr['previous_operation_reason'] = $prevOperationreason[$Key];
			//$prevOperationArr['operator_name'] = $_SESSION['loginUserName'];
			//$prevOperationArr['operator_id'] = $_SESSION['loginUserId'];
			if($prevOperationId[$Key]){
				$objectMenageData->updateRecords($prevOperationsArr, 'patient_previous_operation_tbl', 'previous_operation_id', $prevOperationId[$Key]);
			}else{
				$objectMenageData->addRecords($prevOperationsArr, 'patient_previous_operation_tbl');
			}
		}else if($prevOperationId[$Key]!=''){
			$objectMenageData->delRecord('patient_previous_operation_tbl', 'previous_operation_id', $prevOperationId[$Key]);
		}
	}
}
//GETTING PREVIOUS OPERATION NAME AND REACTION TO DISPLAY
	
	$getprevOperationDetails = $objectMenageData->getArrayRecords('patient_previous_operation_tbl', 'confirmation_id', $pConfId);
	if(count($getprevOperationDetails)>0){
		foreach($getprevOperationDetails as $prevOperationName1){
			++$prevoprt_seq;
			$previous_operation_id[$prevoprt_seq] = $prevOperationName1->previous_operation_id;
			$prevOperation_name[$prevoprt_seq] = $prevOperationName1->previous_operation_name;
			$prevOperationreasons[$prevoprt_seq] = $prevOperationName1->previous_operation_reason;
		}
	}
//GETTING PREVIOUS OPERATION NAME AND REACTION TO DISPLAY

?>
<form  action="pre_op_genanes_prevopert_spreadsheet.php?submitMe=true" name="frm_pre_op_genanes_prevopert_spreadsheet" enctype="multipart/form-data" method="post" style="margin:0px;">
	<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
	<input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
	<input type="hidden" name="ascId" value="<?php echo $ascId; ?>">	
	<table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#F1F4F0">
		<?php for($i_preopgenanes_prevop=1;$i_preopgenanes_prevop<=10;$i_preopgenanes_prevop++) { 
				$prevopNameWidth = $_REQUEST["prevopNameWidth"];
					if($prevopNameWidth=="") { $prevopNameWidth = 218; }
				
				$prevopReasonWidth = $_REQUEST["prevopReasonWidth"];
					if($prevopReasonWidth=="") { $prevopReasonWidth = 218; }
		?> 			
			<input type="hidden" name="prevOperationId[]" value="<?php echo $previous_operation_id[$i_preopgenanes_prevop]; ?>">
			<tr bgcolor="#F1F4F0" style="padding-left:0; ">
				<td height="22">&nbsp;</td>
			 	 <td colspan="5" height="22" class="text_10b" align="left">
				 	<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td><input type="text" value="<?php echo $prevOperation_name[$i_preopgenanes_prevop]; ?>" name="prevOperationName[]" id="prevOperation_name<?php echo $i_preopgenanes_prevop;?>" class="field text" style=" border:1px solid #ccccc; width:<?php echo $prevopNameWidth;?>px; height:22px; "   tabindex="1"/></td>
							<td><input type="text" value="<?php echo $prevOperationreasons[$i_preopgenanes_prevop]; ?>" name="prevOperationreason[]" id="prevOperation_reason<?php echo $i_preopgenanes_prevop;?>" class="field text" style=" border:1px solid #ccccc; width:<?php echo $prevopReasonWidth;?>px; height:22px;" tabindex="1"  /></td>
						</tr>
					</table>			  		
				</td>
			</tr>
		<?php } ?>
	</table>
</form>

<?php
	if($sprd_prevOperations=='No'){
		?>
		<script>
			
			obj = document.getElementsByName('prevOperationName[]');
			obj1 = document.getElementsByName('prevOperationreason[]');
			var len = obj.length;
			for(i=0;i<len;i++){
				obj[i].disabled = true;
				obj1[i].disabled = true;
			}
		</script>
		<?php
	}
?>