<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");
include_once("../common/conDb.php");
include("adminLinkfile.php");
include_once("classObjectFunction.php");
$objectMenageData = new manageData;
$andProfileDelCond = "  AND del_status ='' ";
$patient_id = $_REQUEST['patient_id'];
$ascId = $_REQUEST['ascId'];
$pConfId = $_REQUEST['pConfId'];
$allergies = $_REQUEST["allergies"];
$hidd_insId = $_REQUEST['hidd_insId'];
$hidd_sbmt = $_REQUEST['hidd_sbmt'];
$surgeonId = $_REQUEST['surgeonId'];
//get data for prefrence card if any
if($_REQUEST['pref_card'])
{
	$query=imw_query("select * from procedureprofile where procedureId='$_REQUEST[pref_card]'")or die(imw_error());
	if(imw_num_rows($query)>=1)
	{
		$pref_card=imw_fetch_object($query);
		
	}
}
/* echo "<script>alert('$mediID');</script>";*/
if($_REQUEST['submitMe']){
	
	$medication_quest = $_REQUEST['medication_quest'];
	$strength_quest = $_REQUEST['strength_quest'];
	$directions_quest = $_REQUEST['directions_quest'];
	$mediID_quest = $_REQUEST['mediID'];
	$mediCatID_quest = $_REQUEST['mediCatID'];
	
	$insIdArray = array();
	for($i=0;$i<count($medication_quest);$i++) {
		$medication_quest[$i] = addslashes($medication_quest[$i]);
		$strength_quest[$i] = addslashes($strength_quest[$i]);
		$directions_quest[$i] = addslashes($directions_quest[$i]);
		$mediID_quest[$i] = addslashes($mediID_quest[$i]);
		if($mediID_quest[$i]<>''){
			//DO NOTHING
			
			$upMedicationQry 	= "update preopmedicationorder set 
			medicationName 		= '".addslashes($medication_quest[$i])."',
			strength 			= '".addslashes($strength_quest[$i])."',
			directions 			= '".addslashes($directions_quest[$i])."' 
			where preOpMedicationOrderId='".$mediID_quest[$i]."'";
			$upMedicationRes = imw_query($upMedicationQry) or die(imw_error()); 
			
		}else{
			$chkMedicationQry = "select * from preopmedicationorder where medicationName = '".$medication_quest[$i]."' AND strength = '".$strength_quest[$i]."' AND directions = '".$directions_quest[$i]."' order by medicationName";
			$chkMedicationRes = imw_query($chkMedicationQry) or die(imw_error()); 
			$chkMedicationNumRow = imw_num_rows($chkMedicationRes);
			if($chkMedicationNumRow>0) {
				$insIdRow = imw_fetch_array($chkMedicationRes);
				$insIdArray[$i] = $insIdRow['preOpMedicationOrderId'];
				//do nothing
			}else {
				$medCatIDQry="";
				if($mediCatID_quest) { $medCatIDQry = " , mediCatId = '".$mediCatID_quest[$i]."' "; }
				$insMedicationQry = "insert into preopmedicationorder set medicationName = '".addslashes($medication_quest[$i])."',strength = '".addslashes($strength_quest[$i])."',directions = '".addslashes($directions_quest[$i])."'".$medCatIDQry;
				$insMedicationRes = imw_query($insMedicationQry) or die(imw_error()); 
				$insIdArray[$i] = imw_insert_id();
			}
		}	
	}
	$mediID = $_REQUEST['mediID'];
		
	for($j=0;$j<count($mediID);$j++) {
		if($mediID[$j] &&  !$medication_quest[$j]) {
			$mediID[$j] = '';
		}
	}
	/*
	if(is_array($insIdArray)) {	
		$mediID= array_merge($insIdArray,$mediID);
	}
	*/
	//SET(INSERT) MANUAL TYPED MEDICATION IN SPREADSHEET
		for($k=0;$k<count($medication_quest);$k++) {
			if($medication_quest[$k] &&  !$mediID[$k]) {
				$mediID[$k] = $insIdArray[$k];
			}
		}
	//END SET(INSERT) MANUAL TYPED MEDICATION IN SPREADSHEET
	
	if(is_array($mediID)) {
		$mediID = implode(',',$mediID);
	}
	
	
	if($_REQUEST['profileId']<>'') {
		$sprd_updateQry = "update surgeonprofile set preOpOrders = '$mediID' where surgeonProfileId = '".$_REQUEST['profileId']."'".$andProfileDelCond;
		
		imw_query($sprd_updateQry) or die(imw_error());
		$profile_insId = $_REQUEST['profileId'];
	}else {
		$sprd_insQry = "insert into surgeonprofile set preOpOrders = '$mediID'";
		imw_query($sprd_insQry) or die(imw_error());
		$profile_insId = imw_insert_id(); 
	}
	$allergyId = $_REQUEST['allergyId'];
	if((is_array($medication_quest)) && (!empty($medication_quest))){
		foreach($medication_quest as $Key => $allergies){
			if($allergies!=''){
				if($allergyId[$Key]){
					$objectMenageData->updateRecords($allergiesReactionArr, 'patient_allergies_tbl', 'pre_op_allergy_id ', $allergyId[$Key]);
				}else{
					$objectMenageData->addRecords($allergiesReactionArr, 'patient_allergies_tbl');
				}		
			}else if($allergyId[$Key]!=''){
				$objectMenageData->delRecord('patient_allergies_tbl', 'pre_op_allergy_id', $allergyId[$Key]);
			}
		}
	}
}

?>
<script>
	function GetXmlHttpObject()
	{ 
				
		var objXMLHttp=null
		if (window.XMLHttpRequest)
		{
		objXMLHttp=new XMLHttpRequest()
		}
		else if (window.ActiveXObject)
		{
		objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP")
		}
		return objXMLHttp
	}			
	
	function delSpreadSheetAdmin(profileId,delMediID,allgNameWidth,allgReactionWidth) {
		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null)
			{
				alert ("Browser does not support HTTP Request");
				return;
			} 

		var url='surgeonProfileSpeadsheetAjax.php'
		url=url+"?profileId="+profileId
		url=url+"&delMediID="+delMediID
		url=url+"&allgNameWidth="+allgNameWidth
		url=url+"&allgReactionWidth="+allgReactionWidth
		url=url+Math.random();
		xmlHttp.onreadystatechange=delSpreadSheetAdminFun
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
	function delSpreadSheetAdminFun() {
		if(xmlHttp.readyState==1) {
			document.getElementById("spreadSheetAjaxId").innerHTML='<center><img src="../images/pdf_load_img.gif"></center>';
		}
		
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
		{ 
			if(document.getElementById('spreadSheetAjaxId')) {
				document.getElementById('spreadSheetAjaxId').innerHTML=xmlHttp.responseText;
			}
		}
	}
	
</script>
<input type="hidden" name="hidd_sbmt" id="hidd_sbmt" value="<?php echo $hidd_sbmt;?>h">	
<form name="frm_surgeonProfileSpeadsheet" method="post" style="margin:0px;" action="surgeonProfileSpeadsheet.php?submitMe=true">
	<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
	<input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
	<input type="hidden" name="ascId" value="<?php echo $ascId; ?>">	
	<input type="hidden" name="hidd_insId" id="hidd_insId" value="<?php echo $hidd_insId;?>">	
	<input type="hidden" name="profileId" value="<?php echo $_REQUEST['profileId']; ?>">
	<input type="hidden" name="surgeonId" value="<?php echo $surgeonId; ?>">
	
	<div id="spreadSheetAjaxId">
		<table   align="left" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#F1F4F0">
			<?php 
			//GETTING PREOP MEDICATIONS TO DISPLAY
				$surgeonprofileDetails = $objectMenageData->getRowRecord('surgeonprofile', 'surgeonProfileId', $_REQUEST['profileId'],'','','',$andProfileDelCond);
				$preOpOrdersCommaString = $surgeonprofileDetails->preOpOrders;
				if($pref_card->preOpOrders)
				$preOpOrdersCommaStringExplode = explode(",",$pref_card->preOpOrders);
				else
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
								<td style="width:33%;"><input type="text" name="medication_quest[]" id="medication_quest<?php echo $i_healthquest_allerg;?>"  <?php echo $keyPressEvent;?> class="form-control" style="width:100%; float:left; border-radius:0;" value="<?php echo stripslashes($medication_quest[$i_healthquest_allerg]); ?>" tabindex="1"  /></td>
								<td style="width:33%;"><input type="text" name="strength_quest[]"   id="strength_quest<?php echo $i_healthquest_allerg;?>"    <?php echo $keyPressEvent;?> class="form-control" style="width:100%; float:left; border-radius:0;" value="<?php echo stripslashes($strength_quest[$i_healthquest_allerg]); ?>" tabindex="1"  /></td>
								<td style="width:auto;">
                                <input type="text" name="directions_quest[]" id="directions_quest<?php echo $i_healthquest_allerg;?>"  <?php echo $keyPressEvent;?> class="form-control" style="width:90%; float:left; border-radius:0;" value="<?php echo stripslashes($directions_quest[$i_healthquest_allerg]); ?>" tabindex="1"  />
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
	</div>
</form>

<?php
//SAVE RECORD IN SURGEON PROFILE TABLE
if($_REQUEST['submitMe']){
?>
	<script>
		var docObj = top.frames[0].frames[0].frames[0].document.forms[0];
		docObj.profile_insId.value = '<?php echo $profile_insId;?>';
		docObj.profileId.value = '<?php echo $profile_insId;?>';
		docObj.surgeonId.value = '<?php echo $surgeonId;?>';
		
		docObj.submit();
	</script>

<?php

}
//SAVE RECORD IN SURGEON PROFILE TABLE
?>