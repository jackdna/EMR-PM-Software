<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
$patient_id = $_REQUEST["patient_id"];
$ascId = $_REQUEST["ascId"];
$pConfId = $_REQUEST["pConfId"];
$SaveForm_alert= $_REQUEST["SaveForm_alert"];
//PURGE 
$consentMultipleAutoIncrId = $_REQUEST['consentMultipleAutoIncrId'];
$hiddPurgestatus = $_GET['hiddPurgestatus'];
//PURGE END

$leftNavigationQry = "select * from left_navigation_forms where confirmationId='$pConfId'";
$leftNavigationRes = imw_query($leftNavigationQry) or die(imw_error("error1"));
$leftNavigationNumRow = imw_num_rows($leftNavigationRes);
$leftNavigationRow = imw_fetch_array($leftNavigationRes);
$fieldName1 = $leftNavigationRow["$fieldName"];

if($tablename=='consent_multiple_form') { //GET LEFT NAVIGATION FIELD NAME IF TABLE NAME IS consent_multiple_form
//GURLEEN 
	if($_REQUEST['consentMultipleAutoIncrId']){
		$consentAutoIncrIdQry = ' AND surgery_consent_id='.$_REQUEST['consentMultipleAutoIncrId'];
	}else{
		//DO NOTHING
	}
//GURLEEN END
	
	$consentNavigationStatusQry = "select * from consent_multiple_form where confirmation_id='".$pConfId."' AND consent_template_id='".$consentMultipleId."' AND consent_template_id!='0'".$consentAutoIncrIdQry;
	$consentNavigationStatusRes = imw_query($consentNavigationStatusQry) or die(imw_error("error1"));
	$consentNavigationStatusNumRow = imw_num_rows($consentNavigationStatusRes);
	$consentNavigationStatusRow = imw_fetch_array($consentNavigationStatusRes);
	$fieldName1 = $consentNavigationStatusRow["left_navi_status"];
}

//START RUN THIS CODE AT FIRST TIME THE PAGE LOADS FROM SLIDER'S LEFT LINK (SHIFTING LEFT LINK TO RIGHT)
	if($fieldName1 == "true" && $_REQUEST["cancelRecord"]<>"true") {
		// UPDATE FieldName TO FALSE IN left_navigation_forms TABLE
			if($tablename=='consent_multiple_form') {
				$update_leftNavigationQry = "update `consent_multiple_form` set left_navi_status = 'false' WHERE confirmation_id = '".$pConfId."' AND consent_template_id='".$consentMultipleId."' AND consent_template_id!='0'";
			}else {
				$update_leftNavigationQry = "update `left_navigation_forms` set $fieldName = 'false' WHERE confirmationId = '".$pConfId."'";
			}
			$update_leftNavigationRes = imw_query($update_leftNavigationQry) or die(imw_error());	
		
		// END UPDATE FieldName TO FALSE IN left_navigation_forms TABLE
		
		//SAVE ENTRY IN chartnotes_change_audit_tbl	
			if($consentMultipleId) {
				$consentMultipleIdInsChartNoteQry = ",consent_template_id = '$consentMultipleId'";
			}

			$insertChartNotesAuditQry = "insert into `chartnotes_change_audit_tbl` set 
										user_id='".$_SESSION['loginUserId']."',patient_id='".$patient_id."',confirmation_id='".$pConfId."',
										form_name='$fieldName',status='viewed',action_date_time='".date("Y-m-d H:i:s")."' $consentMultipleIdInsChartNoteQry ";
										
			$insertChartNotesAuditRes = imw_query($insertChartNotesAuditQry) or die(imw_error());	
		//END SAVE ENTRY IN chartnotes_change_audit_tbl	
	}
//END RUN THIS CODE AT FIRST TIME THE PAGE LOADS FROM SLIDER'S LEFT LINK (SHIFTING LEFT LINK TO RIGHT)
	
//SAVE ENTRY IN chartnotes_change_audit_tbl	
	if($_POST['SaveRecordForm']==''  && $_REQUEST["cancelRecord"]<>"true"  && $_REQUEST["rightClick"]=="yes"){
		if($consentMultipleId) {
			$consentMultipleIdInsChartNoteQry = ",consent_template_id = '$consentMultipleId'";
		}
		$insertChartNotesAuditQry = "insert into `chartnotes_change_audit_tbl` set 
									user_id='".$_SESSION['loginUserId']."',patient_id='".$patient_id."',confirmation_id='".$pConfId."',
									form_name='$fieldName',status='viewed',action_date_time='".date("Y-m-d H:i:s")."' $consentMultipleIdInsChartNoteQry";
									
		$insertChartNotesAuditRes = imw_query($insertChartNotesAuditQry) or die(imw_error());	
	}
//END SAVE ENTRY IN chartnotes_change_audit_tbl	

//START RUN THIS CODE ON PRESSING CANCEL BUTTON (SHIFTING RIGHT LINK TO LEFT)
	if($fieldName1 == "false" && $_REQUEST["cancelRecord"]=="true") {
		if($consentMultipleId) {
			$consentMultipleIdChkChartNoteQry = "consent_template_id = '$consentMultipleId' AND";
		}
		$Qry=imw_query("SELECT * FROM `chartnotes_change_audit_tbl` WHERE
									patient_id='".$patient_id."' AND 
									confirmation_id='".$pConfId."' and
									$consentMultipleIdChkChartNoteQry
									form_name='$fieldName'and status='created'") or die(imw_error());
		 $numrows=imw_num_rows($Qry);
		if($numrows>0) {
			//DO NOTHING, REMAIN FALSE
		}else {
			// UPDATE FieldName TO TRUE IN left_navigation_forms TABLE WHEN PRESS CANCEL BUTTON 
				if($tablename=='consent_multiple_form') {
					$update_leftNavigationQry = "update `consent_multiple_form` set left_navi_status = 'true' WHERE confirmation_id = '".$pConfId."' AND consent_template_id='".$consentMultipleId."' AND consent_template_id!='0'";
				}else {
					$update_leftNavigationQry = "update `left_navigation_forms` set $fieldName = 'true' WHERE confirmationId = '".$pConfId."'";
				}
				$update_leftNavigationRes = imw_query($update_leftNavigationQry) or die(imw_error());	
			// END UPDATE FieldName TO TRUE IN left_navigation_forms TABLE WHEN PRESS CANCEL BUTTON 
		}
	
	?>
	<script>
		var locationFrame = '<?php echo $pageName;?>';
		top.mainFrame.location.href = locationFrame;
	</script>
	<?php
	}
//END RUN THIS CODE ON PRESSING CANCEL BUTTON (SHIFTING RIGHT LINK TO LEFT)

?>