<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
$countRows = count($menuListArr);
$divWith = $width + 15;
include "common/linkfile.php";
include_once('admin/classObjectFunction.php');
$objManageData = new manageData;
$userPrivileges = $_SESSION['userPrivileges'];

// GET DATA FROM PATIENT RECORDS.
	//$objManageData = new manageData;
	$patientFormDetails = $objManageData->getRowRecord('left_navigation_forms', 'confirmationId', $pConfId);
		$left_surgery_form = $patientFormDetails->surgery_form;
		$left_hippa_form = $patientFormDetails->hippa_form;
		$left_assign_benifits_form = $patientFormDetails->assign_benifits_form;
		$left_insurance_card_form = $patientFormDetails->insurance_card_form;
		$left_pre_op_health_ques_form = $patientFormDetails->pre_op_health_ques_form;
		$left_pre_op_nursing_form = $patientFormDetails->pre_op_nursing_form;
		$left_post_op_nursing_form = $patientFormDetails->post_op_nursing_form;	
		$left_pre_op_physician_order_form = $patientFormDetails->pre_op_physician_order_form;
		$left_post_op_physician_order_form = $patientFormDetails->post_op_physician_order_form;	
		$left_mac_regional_anesthesia_form = $patientFormDetails->mac_regional_anesthesia_form;
		$left_pre_op_genral_anesthesia_form = $patientFormDetails->pre_op_genral_anesthesia_form;	
		$left_genral_anesthesia_form = $patientFormDetails->genral_anesthesia_form;
		$left_genral_anesthesia_nurses_notes_form = $patientFormDetails->genral_anesthesia_nurses_notes_form;	
		$left_intra_op_record_form = $patientFormDetails->intra_op_record_form;
		$left_surgical_operative_record_form = $patientFormDetails->surgical_operative_record_form;	
		$left_qa_check_list_form = $patientFormDetails->qa_check_list_form;
		$left_discharge_summary_form = $patientFormDetails->discharge_summary_form;
		$left_post_op_instruction_sheet_form = $patientFormDetails->post_op_instruction_sheet_form;
		$left_physician_amendments_form = $patientFormDetails->physician_amendments_form;
// GET DATA FROM PATIENT RECORDS.
?>
<script>
function left_link_click(pageName,thisId1,innerKey1,preColor1,patient_id1,pConfId1,ascId1) {

	var pageName = pageName+'?patient_id='+patient_id1+'&pConfId='+pConfId1+'&ascId='+ascId1+'&rightClick=yes';
	top.document.forms[0].thisId.value = thisId1
	top.document.forms[0].innerKey.value = innerKey1-1;
	top.document.forms[0].preColor.value = preColor1;
	top.document.forms[0].patient_id.value = patient_id1
	top.document.forms[0].pConfId.value = pConfId1;
	top.document.forms[0].ascId.value = ascId1;
	top.document.forms[0].frameHref.value = pageName;
	top.document.forms[0].submit();
}

</script>

<div align="left" id='<?php echo $sliderBar;?>' style=" cursor:hand;position:absolute;<?php if($sliderBar == "sliderBarLEFT"){ echo "left:".$left."px;"; }else{ echo "right:".$right."px;"; } ?> top:<?php echo $top.'px'; ?>;width:<?php echo $divWith.'px'; ?>;">
	<input type="hidden" value="false" name="slideOut">
	<input type="hidden" name="slider_color" id="slider_color"  />
	<input type="hidden" value="false" name="sliderRightOut">
	<input type="hidden" value="0" name="leftMainOpen">
	<input type="hidden" value="<?php echo $leftCounter; ?>" name="leftInnerOpen">
	<input type="hidden" value="0" name="rightMainOpen">
	<input type="hidden" value="1" name="rightInnerOpen">
	<input type="hidden" value="" name="mainMenu">
	<input type="hidden" value="" name="subMenuFld">
	<input type="hidden" value="" name="pre_color">	
	<table class="all_border" style="position:<?php if($sliderBar == "sliderBarLEFT") echo "absolute;top:0px;right:-124px;"; else echo "absolute;top:0px;left:-128px;"; ?>" border="0" cellpadding="0" cellspacing="0" align="<?php if($sliderBar == "sliderBarLEFT"){ echo "right"; }else{ echo "left"; } ?>">
		<tr>
			<?php 
			if($sliderBar == "sliderBarLEFT"){
				?>
				<td valign="middle"  class="text_10b" onClick="return slide('<?php echo $sliderBar; ?>', '<?php echo $countRows; ?>', '<?php echo $left; ?>', '<?php echo $width; ?>', '<?php echo $right; ?>');"><img src="images/new2.jpg" width="103" height="26" border="0"></td>
				<td id="<?php echo $image;?>" style="cursor:hand;" onClick="return slide('<?php echo $sliderBar; ?>', '<?php echo $countRows; ?>', '<?php echo $left; ?>', '<?php echo $width; ?>', '<?php echo $right; ?>');"><img src="<?php if($sliderBar == "sliderBarRight"){ echo "images/move_back.jpg"; }else{ echo "images/move_forward.jpg"; } ?>"></td>
				<?php
			}else{
				?>
				<td rowspan="2"  id="<?php echo $image;?>" style="cursor:hand;" onClick="return slide('<?php echo $sliderBar; ?>', '<?php echo $countRows; ?>', '<?php echo $left; ?>', '<?php echo $width; ?>', '<?php echo $right; ?>');"><img src="<?php if($sliderBar == "sliderBarRight"){ echo "images/move_back.jpg"; }else{ echo "images/move_forward.jpg"; } ?>"></td>
				<td valign="middle"    class="text_10b " onClick="return slide('<?php echo $sliderBar; ?>', '<?php echo $countRows; ?>', '<?php echo $left; ?>', '<?php echo $width; ?>', '<?php echo $right; ?>');"><img src="images/patient.jpg" width="111" height="26" border="0"></td>
			<?php
			}
			?>
		</tr>
	</table>
	<div align="left" id="tabnav" style="width:<?php echo $width; ?>;background-color:<?php echo $subcolor[1]; ?>;overflow:auto; ">
		<ul>
		<?php
		$i=0;
		$innKeys = 1;
		foreach($menuListArr as $key => $sliderListText){			
			$mainCounter = count($menuListArr);
			$innerCounter = count($subMenuListArr[$key]);
			?>
			<li  id="bgChangeColor">
			<!-- <a style="cursor:default;"> -->
			<a href="javascript:exp_collapse_slider('main<?php print $key; ?>')"  class="nostyle link_slid" style="cursor:hand; ">
				<span style="height:30px;width:<?php echo $width; ?>; background-image: url(<?php echo $maincolor[$i]; ?>); color:#FFFFFF; font-weight:bold">
					<?php echo $sliderListText; ?>
				</span>
			</a>
			<!--	javascript:showContents('<?php echo $innerCounter; ?>', '<?php echo $key; ?>', '<?php echo $mainCounter; ?>', '<?php echo $sliderBar; ?>')-->
			<?php 
			$i++;
			if(count($subMenuListArr[$key])>0){
				$ct=1;					
				?>
				<span id="main<?php print $key; ?>" style="display:none; ">
				<?php	
								
				foreach($subMenuListArr[$key] as $innKey => $inner){ 
					echo $subMenuListArr[0][4] .'------' .$inner.'<br />';
					if($innKey==0){
						$color = $subMenuListArr[$key][$innKey];
					}                                                   
					if($innKey>0) {
						if($subMenuListArr[0][1] ==$subMenuListArr[$key][$innKey]){
							$lnkname="consent_surgery_form.php";
                            $formName = "surgery_form";
							$patientconfirmationid1 ="confirmation_id";
							$tblename="surgery_consent_form";
							if($left_surgery_form!='false'){ 
								if(($userPrivileges=='Super User') || ($userPrivileges=='Admin')){
									$color = '#D1E0C9';
									$linkStatus = true;
								}else{
									$color = '#999999';
									$linkStatus = false;
								}
							}else{
								$color = '#999999';
								$linkStatus = false;
							}							
						}
						else if($subMenuListArr[0][2] ==$subMenuListArr[$key][$innKey]){
							$lnkname="conset_hippa_form.php";
							$formName = "hippa_form";
							$patientconfirmationid1 ="confirmation_id";
							$tblename="hippa_consent_form";
							if($left_hippa_form!='false'){ 
								if(($userPrivileges=='Super User') || ($userPrivileges=='Admin')){
									$color = '#D1E0C9';
									$linkStatus = true;
								}else{
									$color = '#999999';
									$linkStatus = false;
								}
							}else{
								$color = '#999999';
								$linkStatus = false;
							}							
						}
						else if($subMenuListArr[0][3] ==$subMenuListArr[$key][$innKey]){
							$lnkname="consent_assign_benefits_form.php";
							$formName = "assign_benifits_form";
							$patientconfirmationid1 ="confirmation_id";
							$tblename="benefit_consent_form";
							if($left_assign_benifits_form!='false'){ 
								if(($userPrivileges=='Super User') || ($userPrivileges=='Admin')){
									$color = '#D1E0C9';
									$linkStatus = true;
								}else{
									$color = '#999999';
									$linkStatus = false;
								}
							}else{
								$color = '#999999';
								$linkStatus = false;
							}							
						}
						else if($subMenuListArr[0][4] ==$subMenuListArr[$key][$innKey]){
							$lnkname="consent_insurance_card_form.php";
							$formName = "insurance_card_form";
							$patientconfirmationid1 ="confirmation_id";
							$tblename="insurance_consent_form";
							if($left_insurance_card_form!='false'){ 
								if(($userPrivileges=='Super User') || ($userPrivileges=='Admin')){
									$color = '#D1E0C9';
									$linkStatus = true;
								}else{
									$color = '#999999';
									$linkStatus = false;
								}
							}else{
								$color = '#999999';
								$linkStatus = false;
							}							
						}
						else if($subMenuListArr[1][1] ==$subMenuListArr[$key][$innKey]){
							$lnkname="pre_op_health_quest.php";
							$formName = "pre_op_health_ques_form";
							$patientconfirmationid1 ="confirmation_id";
							$tblename="preophealthquestionnaire";
							if($left_pre_op_health_ques_form!='false'){ 
								if(($userPrivileges=='Super User') || ($userPrivileges=='Admin')){
									$color = '#D1E0C9';
									$linkStatus = true;
								}else{
									$color = '#999999';
									$linkStatus = false;								
								}
							}else{
								$color = '#999999';
								$linkStatus = false;								
							}							
						}else if($subMenuListArr[2][1] ==$subMenuListArr[$key][$innKey]) {
							$lnkname="pre_op_nursing_record.php";
							$formName = "pre_op_nursing_form";
							$patientconfirmationid1 ="confirmation_id";
							$tblename="preopnursingrecord";
							if($left_pre_op_nursing_form!='false'){
								if(($userPrivileges=='Nursing Record') || ($userPrivileges=='Super User') || ($userPrivileges=='Admin')){
									$color = '#EFE492';
									$linkStatus = true;
								}else{
									$color = '#999999';
									$linkStatus = false;
								}								
							}else{
								$color = '#999999';
								$linkStatus = false;
							}
						}else if($subMenuListArr[2][2] ==$subMenuListArr[$key][$innKey]) {
							$lnkname="post_op_nursing_record.php";
							$formName = "post_op_nursing_form";
							$patientconfirmationid1 ="confirmation_id";
							$tblename="postopnursingrecord";
							if($left_post_op_nursing_form!='false'){ 
								if(($userPrivileges=='Nursing Record') || ($userPrivileges=='Super User') || ($userPrivileges=='Admin')){
									$color = '#EFE492';
									$linkStatus = true;
								}else{
									$color = '#999999';
									$linkStatus = false;
								}
							}else{
								$color = '#999999';
								$linkStatus = false;
							}							
						}else if($subMenuListArr[3][1] ==$subMenuListArr[$key][$innKey]) {
							$lnkname="pre_op_physician_orders.php";
							$formName = "pre_op_physician_order_form";
							$patientconfirmationid1 ="patient_confirmation_id";
							$tblename="preopphysicianorders";
							if($left_pre_op_physician_order_form!='false'){ 
								if(($userPrivileges=='Super User') || ($userPrivileges=='Admin') || ($userPrivileges=='Anesthesia')){
									$color = '#DEA068';
									$linkStatus = true;
								}else{
									$color = '#999999';
									$linkStatus = false;
								}
							}else{
								$color = '#999999';
								$linkStatus = false;
							}							
						}else if($subMenuListArr[3][2] ==$subMenuListArr[$key][$innKey]) {
							$lnkname="post_op_physician_orders.php";
							$formName = "post_op_physician_order_form";
							$patientconfirmationid1 ="patient_confirmation_id";
							$tblename="postopphysicianorders";
							if($left_post_op_physician_order_form!='false'){ 
								if(($userPrivileges=='Super User') || ($userPrivileges=='Admin') || ($userPrivileges=='Anesthesia')){
									$color = '#DEA068';
									$linkStatus = true;
								}else{
									$color = '#999999';
									$linkStatus = false;
								}
							}else{
								$color = '#999999';
								$linkStatus = false;
							}							
						}else if($subMenuListArr[4][1] ==$subMenuListArr[$key][$innKey]) {
							$lnkname="local_anes_record.php";
							$formName = "mac_regional_anesthesia_form";
							$patientconfirmationid1 ="confirmation_id";
							$tblename="localanesthesiarecord";
							if($left_mac_regional_anesthesia_form!='false'){ 
								if(($userPrivileges=='Super User') || ($userPrivileges=='Admin')){
									$color = '#80AFEF';
									$linkStatus = true;
								}else{
									$color = '#999999';
									$linkStatus = false;
								}
							}else{
								$color = '#999999';
								$linkStatus = false;
							}							
						}else if($subMenuListArr[4][2] ==$subMenuListArr[$key][$innKey]) {
							$lnkname="pre_op_general_anes.php";
							$formName = "pre_op_genral_anesthesia_form";
							$patientconfirmationid1 ="confirmation_id";
							$tblename="preopgenanesthesiarecord";
							if($left_pre_op_genral_anesthesia_form!='false'){ 
								if(($userPrivileges=='Super User') || ($userPrivileges=='Admin')){
									$color = '#80AFEF';
									$linkStatus = true;
								}else{
									$color = '#999999';
									$linkStatus = false;
								}
							}else{
								$color = '#999999';
								$linkStatus = false;
							}							
						}else if($subMenuListArr[4][3] ==$subMenuListArr[$key][$innKey]) {
							$lnkname="gen_anes_rec.php";
							$formName = "genral_anesthesia_form";
							$patientconfirmationid1 ="confirmation_id";
							$tblename="genanesthesiarecord";
							if($left_genral_anesthesia_form!='false'){ 
							
								if(($userPrivileges=='Super User') || ($userPrivileges=='Admin')){
									$color = '#80AFEF';
									$linkStatus = true;
								}else{
									$color = '#999999';
									$linkStatus = false;
								}
							}else{
								$color = '#999999';
								$linkStatus = false;
							}							
						}else if($subMenuListArr[4][4] ==$subMenuListArr[$key][$innKey]) {
							$lnkname="gen_anes_nurse_notes.php";
							$formName = "genral_anesthesia_nurses_notes_form";
							$patientconfirmationid1 ="confirmation_id";
							$tblename="genanesthesianursesnotes";
							if($left_genral_anesthesia_nurses_notes_form!='false'){ 
								if(($userPrivileges=='Nursing Record') || ($userPrivileges=='Super User') || ($userPrivileges=='Admin')){
									$color = '#80AFEF';
									$linkStatus = true;
								}else{
									$color = '#999999';
									$linkStatus = false;
								}
							}else{
								$color = '#999999';
								$linkStatus = false;
							}							
						}else if($subMenuListArr[5][1] ==$subMenuListArr[$key][$innKey]) {
							$lnkname="op_room_record.php";
							$formName = "intra_op_record_form";
							$patientconfirmationid1 ="confirmation_id";
							$tblename="operatingroomrecords";
							if($left_intra_op_record_form!='false'){ 
								if(($userPrivileges=='Super User') || ($userPrivileges=='Admin') || ($userPrivileges=='Anesthesia')){
									$color = '#80A7D6'; 
									$linkStatus = true;
								}else{
									$color = '#999999';
									$linkStatus = false;
								}
							}else{
								$color = '#999999';
								$linkStatus = false;
							}
						}	else if($subMenuListArr[6][1] ==$subMenuListArr[$key][$innKey]){
							$lnkname="operative_record.php";
							$formName = "surgical_operative_record_form";
							$patientconfirmationid1 ="confirmation_id";
							$tblename="operativereport";
							if($left_surgical_operative_record_form!='false'){ 
								if(($userPrivileges=='Super User') || ($userPrivileges=='Admin')){
									$color = '#D1E0C9';
									$linkStatus = true;
								}else{
									$color = '#999999';
									$linkStatus = false;								
								}
							}else{
							 	$color = '#999999';
							    $linkStatus = false;								
							}							
											
						}else if($subMenuListArr[7][1] ==$subMenuListArr[$key][$innKey]) {
							$lnkname="discharge_summary_sheet.php";
							$formName = "discharge_summary_form";
							$patientconfirmationid1 ="confirmation_id";
							$tblename="dischargesummarysheet";
							if($left_discharge_summary_form!='false'){
								if(($userPrivileges=='Super User') || ($userPrivileges=='Admin') || ($userPrivileges=='Billing') || ($userPrivileges=='Surgeon')){
									$color = '#FCBE6F';
									$linkStatus = true;
								}else{
									$color = '#999999';
									$linkStatus = false;
								}
							}else{
								$color = '#999999';
								$linkStatus = false;
							}
						}else if($subMenuListArr[8][1] ==$subMenuListArr[$key][$innKey]) {						
							$lnkname="instructionsheet.php";
							$formName = "post_op_instruction_sheet_form";
							$patientconfirmationid1 ="patient_confirmation_id";
							$tblename="patient_instruction_sheet";
							if($left_post_op_instruction_sheet_form!='false'){
								if(($userPrivileges=='Super User') || ($userPrivileges=='Admin') || ($userPrivileges=='Billing') || ($userPrivileges=='Surgeon')){
									$color = '#D1E0C9';
									$linkStatus = true;
								}else{
									$color = '#999999';
									$linkStatus = false;
								}
							}else{
								$color = '#999999';
								$linkStatus = false;
							}								
						}else if($subMenuListArr[9][1] ==$subMenuListArr[$key][$innKey]) {
							$lnkname="amendments_notes.php";
							$formName = "physician_amendments_form";
							$patientconfirmationid1 ="patient_confirmation_id";
							if($left_physician_amendments_form!='false'){ 
								if(($userPrivileges=='Super User') || ($userPrivileges=='Admin')){
									$color = '#D0D0ED';
									$linkStatus = true;
								}else{
									$color = '#999999';
									$linkStatus = false;
								}
							}else{
								$color = '#999999';
								$linkStatus = false;
							}
						}else if($subMenuListArr[10][1] ==$subMenuListArr[$key][$innKey]) {
							//$lnkname = "#";
							//CHECK OF PATIENT IS FINALIZED THEN DO NOT INSERT EPOST-IT
							$chkEpostConfirmationDetails = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $_SESSION["pConfId"]);
							if($chkEpostConfirmationDetails) {
								$finalizeStatusChk = $chkEpostConfirmationDetails->finalize_status;
								if($finalizeStatusChk=="true") {
									$pop = "void(0);";
									//echo "<div style='width:150px;height:150px; top:150px; left:200px;'>";
									//echo "No Epost-It for finalized patient</div>";
									//$pop = "alert('No Epost-It for finalized patient');";
								}else {
									$pop = "epostpop('350','35');";
								}	
							}
							//END CHECK OF PATIENT IS FINALIZED THEN DO NOT INSERT EPOST-IT	
							$linkStatus = false;
							$epostStatus = "yes";
						}
						else{							
							$lnkname="under_construction.php";
						}
						
						$kId=$key.'&innerKey='.$innKeys.'&preColor='.urlencode($color);
						$tempPrecolor = urlencode($color);
						$linkSessionId = "&patient_id=$patient_id&pConfId=$pConfId&ascId=$ascId";						
						$subId = $innKeys;
						$innKeys++;
						
						//CODE TO CHECK IF FORM IS ALREADY IN USE FOR TODAY'S DATE
							
						/*	$chkCurrMonth = date("m");
							$chkCurrDay = date("d");
							$chkCurrYear = date("Y");
							$currInitialDateTime = date("Y-m-d H:i:s",mktime(0,0,0,$chkCurrMonth,$chkCurrDay,$chkCurrYear));
							$currEndDateTime = date("Y-m-d H:i:s",mktime(0,0,0,$chkCurrMonth,$chkCurrDay+1,$chkCurrYear));
							$chkFormUsedQry="SELECT * FROM `chartnotes_change_audit_tbl` WHERE 
											confirmation_id = '".$_REQUEST['pConfId']."' 
										AND form_name='".$formName."' 
										AND status = 'viewed' 
										AND action_date_time >= '".$currInitialDateTime."' 
										AND action_date_time < '".$currEndDateTime."'";
							$chkFormUsedRes	= imw_query($chkFormUsedQry) or die(imw_error());	
							$chkFormUsedNumRow = imw_num_rows($chkFormUsedRes);	
							if($chkFormUsedNumRow > 0 && $linkStatus <> true) {
								$chkMrkImage = "<img src='images/check_mark16.png' border='0'>";
							}else {
								$chkMrkImage = "";
							}
							*/
							 $chkdFormUsedQry="SELECT * FROM $tblename WHERE 
										$patientconfirmationid1 = '".$_REQUEST['pConfId']."' 
										    AND ascId='".$_SESSION['ascId']."'";
							$chkFormUsedRes	= imw_query($chkdFormUsedQry) or die(imw_error());	
							$chkFormUsedNumRow = imw_num_rows($chkFormUsedRes);	
							$chkFormUsed = imw_fetch_array($chkFormUsedRes);
			                $formStatus = $chkFormUsed["form_status"];
							if($chkFormUsedNumRow > 0 &&  $formStatus== 'completed' && $linkStatus <> true) {
								$chkMrkImage = "<img src='images/check_mark16.png' border='0'>";
							}else {
								$chkMrkImage = "";
							}
							if($chkFormUsedNumRow > 0) {
							
							}else {
								$blankInsertQry = "insert into $tblename set 
													$patientconfirmationid1 = '".$_REQUEST['pConfId']."',
													ascId='".$_SESSION['ascId']."'";
								$blankInsertRes = imw_query($blankInsertQry) or die(imw_error());
													
							}
						//END CODE TO CHECK IF FORM IS ALREADY IN USE FOR TODAY'S DATE
						
						?>							
						<input type="hidden" name="keyId" id="keyId" value="innerContent<?php echo $kId;?>" />  	
						<div id="main<?php print $key; ?>" style="padding-top:0px;padding-bottom:0px;height:12;"><!--  onclick="getpageinfo(this,'<?php // echo $color;?>')" -->							
							<span class="nostyle"  id="sub<?php echo $subId; ?>" style="width:<?php echo $width; ?>;background-color:<?php echo $color;//if($linkStatus != true) {echo $color;}else { echo $subMenuListArr[$key][$innKey]; } ?>; padding-top:2px; padding-bottom:2px; " >								
							
								 <a style="padding-left:10px; color:<?php if($linkStatus != true) echo "#666666"; ?>;" class="<?php if($linkStatus == true){ echo "nostyle link_slid"; }else{ echo "nostyle bold"; } ?>" href="<?php if($linkStatus == true){ echo $lnkname."?thisId=$kId$linkSessionId"; }else if($linkStatus != true && $epostStatus=="yes") { echo "#"; } else {  echo "javascript:left_link_click('$lnkname','$key','$innKeys','$tempPrecolor','$patient_id','$pConfId','$ascId')";}//$lnkname."?thisId=$kId$linkSessionId"; } ?>" target="<?php if($linkStatus == true) { echo "main_frmInner"; }else if($linkStatus != true && $epostStatus=="yes") { echo ""; } /*else { echo "main_frmInner"; }*/ ?>" onClick="javascript: <?php echo $pop;?>  return slide('<?php echo $sliderBar; ?>', '<?php echo $countRows; ?>', '<?php echo $left; ?>', '<?php echo $width; ?>', '<?php echo $right; ?>');"><?php echo $chkMrkImage.$inner;  ?></a> 
								 
								 <!-- <a style="padding-left:10px; color:<?php if($linkStatus != true) echo "#666666"; ?>;" class="<?php if($linkStatus == true){ echo "nostyle link_slid"; }else{ echo "nostyle bold"; } ?>" href="<?php if($linkStatus == true){ echo $lnkname."?thisId=$kId$linkSessionId"; }else if($linkStatus != true && $epostStatus=="yes") { echo "#"; } else {  echo $lnkname."?thisId=$kId$linkSessionId"; } ?>" target="<?php if($linkStatus == true) { echo "main_frmInner"; }else if($linkStatus != true && $epostStatus=="yes") { echo ""; } else { echo "main_frmInner"; } ?>" onClick="javascript: <?php echo $pop;?>  return slide('<?php echo $sliderBar; ?>', '<?php echo $countRows; ?>', '<?php echo $left; ?>', '<?php echo $width; ?>', '<?php echo $right; ?>');"><?php echo $chkMrkImage.$inner;  ?></a>  -->
								<!-- <a style="padding-left:10px; color:<?php if($linkStatus != true) echo "#666666"; ?>;" class="<?php if($linkStatus == true){ echo "nostyle link_slid"; }else{ echo "nostyle bold"; } ?>" href="<?php if($linkStatus == true){ echo $lnkname."?thisId=$kId$linkSessionId"; }else{ echo "#"; } ?>" target="<?php if($linkStatus == true) echo "main_frmInner"; else echo ""; ?>" onClick="javascript: <?php echo $pop;?>  return slide('<?php echo $sliderBar; ?>', '<?php echo $countRows; ?>', '<?php echo $left; ?>', '<?php echo $width; ?>', '<?php echo $right; ?>');"><?php echo $chkMrkImage.$inner;  ?></a> -->
							</span>
						</div>
						<?php 
					}                   
					$ct=$ct+1;
				}
				?>
			</span>
			<?php                           
			}           
			?>
			</li>
			<?php
		}                                                        
		?>
	</ul>	
	</div><!--END TABNAV-->
</div>
<script language="javascript">
function yellow(subMenu,preColor){
	
	var subMenuId = document.getElementById("subMenuFld");
	var preColorChange = document.getElementById("pre_color");
	if(subMenuId.value != ''){			
		var id = subMenuId.value;
		document.getElementById("sub"+id).style.background = preColorChange.value;
	}
	document.getElementById("sub"+subMenu).style.background = "#FFFF99";
	subMenuId.value = subMenu;		
	preColorChange.value = preColor;	
}

function epostpop(posLeft, posTop){	
	if (top.frames[0].document.getElementById('evaluationEPostDiv')){
		top.frames[0].document.getElementById('evaluationEPostDiv').style.display = 'block';
	}else{ 
		alert("Please open any form for epost") 
	}
	top.frames[0].document.getElementById('evaluationEPostDiv').style.left = posLeft;
	top.frames[0].document.getElementById('evaluationEPostDiv').style.top = posTop;
}
function eclose(){
	top.frames[0].document.getElementById('evaluationEPostDiv').style.display = 'none';
}
</script>