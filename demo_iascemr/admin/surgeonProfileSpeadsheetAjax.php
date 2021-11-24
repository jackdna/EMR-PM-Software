<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

include_once("../common/conDb.php");
include_once("classObjectFunction.php");
$objectMenageData = new manageData;
$andProfileDelCond = "  AND del_status ='' ";
$profileId = $_REQUEST['profileId'];
$delMediID = $_REQUEST['delMediID'];
$allgNameWidth = $_REQUEST['allgNameWidth'];
$allgReactionWidth = $_REQUEST['allgReactionWidth'];

$chkSurgeonprofileDetails = $objectMenageData->getRowRecord('surgeonprofile', 'surgeonProfileId', $_REQUEST['profileId'],'','','',$andProfileDelCond);
$chkPreOpOrdersCommaStringExplode=array();
if(count($chkSurgeonprofileDetails)>0){
	$chkPreOpOrdersCommaString = $chkSurgeonprofileDetails->preOpOrders;
	$chkPreOpOrdersCommaStringExplode = explode(",",$chkPreOpOrdersCommaString);
	if(in_array($delMediID,$chkPreOpOrdersCommaStringExplode)) {
		$savePreOpOrderArr=array();
		$delRecFound = false;
		foreach($chkPreOpOrdersCommaStringExplode as $chkPreOpMedicationOrderId1){
			if($chkPreOpMedicationOrderId1==$delMediID && $delRecFound == false) {
				$delRecFound = true;
				//DO NOTHING
			}else {
				$savePreOpOrderArr[]=$chkPreOpMedicationOrderId1;
			}
		}
		if($savePreOpOrderArr) {
			$savePreOpOrder = implode(',',$savePreOpOrderArr);
			$savePreOpOrderQry = "UPDATE surgeonprofile SET preOpOrders = '".$savePreOpOrder."' where surgeonProfileId = '".$_REQUEST['profileId']."'".$andProfileDelCond;
			$savePreOpOrderRes = imw_query($savePreOpOrderQry) or die(imw_error());
		}
	}
}
?>	
	<table   align="left" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#F1F4F0">
			<?php 
			//GETTING PREOP MEDICATIONS TO DISPLAY
				$surgeonprofileDetails = $objectMenageData->getRowRecord('surgeonprofile', 'surgeonProfileId', $_REQUEST['profileId'],'','','',$andProfileDelCond);
				$preOpOrdersCommaString = $surgeonprofileDetails->preOpOrders;
				$preOpOrdersCommaStringExplode = explode(",",$preOpOrdersCommaString);
				$preOpOrdersCommaStringImplodeTemp = implode("','",$preOpOrdersCommaStringExplode);
				if(count($surgeonprofileDetails)>0){
					if($preOpOrdersCommaStringExplode) {
						foreach($preOpOrdersCommaStringExplode as $preOpMedicationOrderId1){
							if($preOpMedicationOrderId1) {
								
								$preopmedicationorderDetails = $objectMenageData->getRowRecord('preopmedicationorder', 'preOpMedicationOrderId', $preOpMedicationOrderId1,'medicationName');
								if(trim($preopmedicationorderDetails->medicationName)) {
									++$seq1;
									$mediID[$seq1] = $preopmedicationorderDetails->preOpMedicationOrderId;
									$medication_quest[$seq1] = $preopmedicationorderDetails->medicationName;
									$strength_quest[$seq1] = $preopmedicationorderDetails->strength;
									$directions_quest[$seq1] = $preopmedicationorderDetails->directions;		
								}
							}
						}
					}	
				}
			//GETTING PREOP MEDICATIONS TO DISPLAY		
			
			for($i_healthquest_allerg=1;$i_healthquest_allerg<=20;$i_healthquest_allerg++) { 
				
				$allgNameWidth = $_REQUEST["allgNameWidth"];
					if($allgNameWidth=="") { $allgNameWidth = 280; }
				
				$allgReactionWidth = $_REQUEST["allgReactionWidth"];
					if($allgReactionWidth=="") { $allgReactionWidth = 280; }
					
					$readMode='';
					$keyPressEvent = 'onkeyup="javascript:document.getElementById(\'mediID'.$i_healthquest_allerg.'\').value=\'\';"';
					if(trim($medication_quest[$i_healthquest_allerg])) { $readMode = 'readonly="readonly"'; $keyPressEvent='';}
				?> 
				<input type="hidden" name="allergyId[]" value="<?php echo $pre_op_allergy_id[$i_healthquest_allerg]; ?>">			
				<input type="hidden" name="mediID[]" id="mediID<?php echo $i_healthquest_allerg;?>" value="<?php echo $mediID[$i_healthquest_allerg];?>">
                <input type="hidden" name="mediCatID[]" id="mediCatID<?php echo $i_healthquest_allerg;?>" value="">
				<tr bgcolor="#F1F4F0" style="padding-left:0; ">
					<td colspan="5" align="left">
						<table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
							<tr>
								<td style="width:33%;"><input type="text" name="medication_quest[]" id="medication_quest<?php echo $i_healthquest_allerg;?>" <?php echo $readMode;?> <?php echo $keyPressEvent;?> class="form-control" style="width:100%; float:left; border-radius:0;" value="<?php echo stripslashes($medication_quest[$i_healthquest_allerg]); ?>" tabindex="1"  /></td>
								<td style="width:33%;"><input type="text" name="strength_quest[]"   id="strength_quest<?php echo $i_healthquest_allerg;?>"   <?php echo $readMode;?> <?php echo $keyPressEvent;?> class="form-control" style="width:100%; float:left; border-radius:0;" value="<?php echo stripslashes($strength_quest[$i_healthquest_allerg]); ?>" tabindex="1"  /></td>
								<td style="width:auto;">
                                <input type="text" name="directions_quest[]" id="directions_quest<?php echo $i_healthquest_allerg;?>" <?php echo $readMode;?> <?php echo $keyPressEvent;?> class="form-control" style="width:90%; float:left; border-radius:0;" value="<?php echo stripslashes($directions_quest[$i_healthquest_allerg]); ?>" tabindex="1"  />
								<?php
								if($mediID[$i_healthquest_allerg]) {
								?>
									<img src="../images/close.jpg" style="cursor:hand; float:right" alt="delete" onClick="javascript:delSpreadSheetAdmin('<?php echo $_REQUEST['profileId'];?>','<?php echo $mediID[$i_healthquest_allerg];?>','<?php echo $allgNameWidth;?>','<?php echo $allgReactionWidth;?>');">
								<?php
								}
								?>
								</td>
							
							</tr>
						</table>
					</td>
				</tr>
				<?php 
			} 
			?>
		</table>