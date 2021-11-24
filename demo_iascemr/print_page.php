<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
//START CODE TO LOCK PATIENT CHART
$finalizeFormStatus='';
if(trim($finalize_status)=='true' || trim($finalizeStatus)=='true') {
	$finalizeFormStatus = 'true';	
}
$chartLockFormName='';
if($tablename=='localanesthesiarecord') {
	$chartLockFormName = 'mac_regional_anesthesia_form';
}
$arrayChartLockRecord 	= array();
$conditionLockArr 		= array();
$sessId					= session_id();
if(trim($chartLockFormName) && constant('CHART_PT_LOCK')=='YES' && $finalizeFormStatus!='true' && $permissionToWriteChart=='yes') {
	$chartLockQry = "SELECT cpl.id,cpl.user_id, CONCAT(usr.lname,', ',usr.fname,' ',usr.mname) AS chartLockUserName 
					FROM chart_pt_lock_tbl cpl
					LEFT JOIN users usr ON (usr.usersId = cpl.user_id) 
					WHERE cpl.confirmation_id='".$pConfId."' 
					AND cpl.form_name='".$chartLockFormName."' 
					ORDER BY cpl.id DESC LIMIT 0,1";
	$chartLockRes = imw_query($chartLockQry) or die(imw_error());
	if(imw_num_rows($chartLockRes)>0) {
		$chartLockRow = imw_fetch_array($chartLockRes);
		$chartLockId = $chartLockRow['id'];
		$chartLockUserId = $chartLockRow['user_id'];
		$chartLockUserName = $chartLockRow['chartLockUserName'];
		if($chartLockUserId<>$_SESSION['loginUserId']) {
?>			
			<script>
				$(document).ready(function(){
					$("#login_password_btn").click(function(){
						check_chart_log();
					});
					$("#login_password").keypress(function(evt){
						evt = (evt) ? evt : ((event) ? event : null);
						var evver = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null );
						var keynumber = evt.keyCode;
						if(keynumber==13){
							check_chart_log();
						}
						
					});
					
				});
				top.frames[0].setPNotesHeight();
                top.document.getElementById('footer_button_id').style.display = 'none';
				top.frames[0].displayFooterPrintButton();
            </script>
            
            
            <div id="div_chrt_lock" style="width:600px; height:130px; border:1px solid #CCC; position:absolute; top:120px; left:80px; background:<?php echo $rowcolor_discharge_summary_sheet; ?>;">
            <div style="background:<?php echo $bgCol; ?>;width:598px; height:25px;position:absolute;  border:1px solid #000;"></div>
            	<span style="font-size:16px; font-weight:bold;"><br><br>This chart is being opened for edit by <?php echo $chartLockUserName; ?></span><br><br>Enter logged in user's password to open chart note lock.<br>
                <input type="password" name="login_password" id="login_password" style="height:16px;"><span style="padding-left:10px; vertical-align:bottom;" ><a href="#" onClick="MM_swapImage('login_password_btn','','images/go_onclick1.jpg',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('login_password_btn','','images/go_hover1.jpg',1)"><img src="images/go.jpg" id="login_password_btn" style="border:none;" alt="GO" /></a></span>
            </div>
<?php
		}
	}else {
		unset($arrayChartLockRecord);
		$arrayChartLockRecord['user_id'] 			= $_SESSION['loginUserId'];
		$arrayChartLockRecord['patient_id'] 		= $patient_id;
		$arrayChartLockRecord['confirmation_id'] 	= $pConfId;
		$arrayChartLockRecord['form_name'] 			= $chartLockFormName;
		$arrayChartLockRecord['action_date_time'] 	= date('Y-m-d H:i:s');
		$arrayChartLockRecord['sess_id'] 			= $sessId;
		$objManageData->addRecords($arrayChartLockRecord, 'chart_pt_lock_tbl');		
	}
}
//END CODE TO LOCK PATIENT CHART
?>
<input type="hidden" id="login_lock_user_id" value="<?php echo $chartLockUserId; ?>">
<input type="hidden" id="chart_lock_id" value="<?php echo $chartLockId; ?>">
<input type="hidden" id="chart_form_name" value="<?php echo $chartLockFormName; ?>">
<input type="hidden" id="login_user" value="<?php echo $_SESSION['loginUserId']; ?>">
<iframe id="testiframe" name="testiframe"  style="height:0px;width:0px;border: 0px;"></iframe>
<?php
//for consent form
$consentMultiIdJs="";
if($consentMultipleId){
	$consentMultiIdJs.="&consentMultipleId=".$consentMultipleId; 
}
if($_REQUEST['consentMultipleAutoIncrId']){
	$consentMultiIdJs.="&consentMultipleAutoIncrId=".$_REQUEST['consentMultipleAutoIncrId']; 
}
//for consent form

?>
<script type="text/javascript">
	var formActionPrint=document.getElementById('frmAction').value;
	var formSrc="print_emr.php?patient_id=<?php echo $patient_id; ?>&pConfId=<?php echo $pConfId; ?>&formaction="+formActionPrint+"<?php echo $consentMultiIdJs ?>&intCountChild = <?php echo $intCountChild; ?>";	
	document.getElementById('testiframe').src=formSrc;
</script>
