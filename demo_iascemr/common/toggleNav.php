<?PHP
	
	include_once 'admin/classObjectFunction.php';
	$obj		=	new manageData();
	
		
	$surgeryCenterDirectoryName	=	(!$surgeryCenterDirectoryName)	?	'surgerycenter'	:	$surgeryCenterDirectoryName ;
	$iolinkDirectoryName		=	(!$iolinkDirectoryName)			?	'iolink'		:	$iolinkDirectoryName ;
	$rootServerPath				=	(!$rootServerPath)				?	$_SERVER['DOCUMENT_ROOT']	:	$rootServerPath; 
	
	$loginUser 	=	$_SESSION['loginUserId'];
	$pConfId 	=	$_REQUEST['pConfId'];
	$patient_id =	$_REQUEST['patient_id'];
	unset($userPrivilegesArr);
	unset($admin_privilegesArr);
	
	$authenticationDetails 	=	$objManageData->getRowRecord('users', 'usersId', $loginUser);
	$user_type 				=	$authenticationDetails->user_type;
	$userPrivileges			=	$authenticationDetails->user_privileges;
	$admin_privileges 		=	$authenticationDetails->admin_privileges;
	$userPrivilegesArr 		=	explode(', ', $userPrivileges);	

	if($admin_privileges) { $admin_privilegesArr= explode(', ', $admin_privileges); }
	else { $admin_privilegesArr= array();  }


	//laser
	$chkprocedureConfirmationDetails 	= $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $_REQUEST["pConfId"]);
	$Confirm_patientPrimProc 			= stripslashes($chkprocedureConfirmationDetails->patient_primary_procedure);
	$Confirm_patientPrimaryProcedureId 	= $chkprocedureConfirmationDetails->patient_primary_procedure_id;
	$Confirm_patientDos 				= $chkprocedureConfirmationDetails->dos;
	$Confirm_patientId 					= $chkprocedureConfirmationDetails->patientId;
	$finalizeStatusChk 					= $chkprocedureConfirmationDetails->finalize_status;
	
	$primary_procedureQry 				= "SELECT * FROM procedures WHERE name = '".$Confirm_patientPrimProc."' OR procedureAlias='".$Confirm_patientPrimProc."'";
	$primary_procedureRes 				= imw_query($primary_procedureQry);
	$primary_procedureRow 				= imw_fetch_array($primary_procedureRes);
	$patient_primary_procedure_categoryID = $primary_procedureRow['catId'];

	// lasers
	
//set http
if(strpos($_SERVER['HTTP_REFERER'], 'https') !== false){
		$get_http_path = 'https';
         }
	elseif(strpos($_SERVER['HTTP_REFERER'], 'http') !== false)
	{
		$get_http_path= 'http';
	}
//set http
	
	$LeftOpLaserSlider	=	'Operating Room';
	
	$PSlider	=	$obj->getExtractRecord('patient_confirmaton','patientConfirmationId',$pConfId);
	
	if(is_array($PSlider) && count($PSlider) > 0 )
	{
		//$procedureChkSliderLeftQryRow = imw_fetch_array($procedureChkSliderLeftQryRes);
		//SliderLeft_patientPrimProc
		$PPL	=	$PSlider['patient_primary_procedure'];	//	PPL - Primary Procedures List
		$PPL	=	addslashes($PPL);
		// Primary Procedures Query
		
		$PPQ	=	$obj->getMultiChkArrayRecords('procedures', array('name'=>$PPL),0,0," OR procedureAlias = '".$PPL."'");
		if(is_array($PPQ) && count($PPQ) > 0)
		{
			$PPCat_ID	=	$PPQ->catId;	//patient_primary_procedure_categoryLeftID
			$LeftOpLaserSlider	=	($PPCat_ID ==  2 )	?	'Laser Procedure'	:	$LeftOpLaserSlider ;
		}
	}	// lasers
	
	
	// GET DATA FROM PATIENT RECORDS.
	//$objManageData = new manageData;
	$patientFormDetails = $objManageData->getRowRecord('left_navigation_forms', 'confirmationId', $pConfId);
		$left_surgery_form = $patientFormDetails->surgery_form;
		$left_hippa_form = $patientFormDetails->hippa_form;
		$left_assign_benifits_form = $patientFormDetails->assign_benifits_form;
		$left_insurance_card_form = $patientFormDetails->insurance_card_form;
	
		$HQ			=	$patientFormDetails->pre_op_health_ques_form;	// Health Questionriee
		$HQ_color	=	(($HQ <> 'false' && in_array($usrPrv,array('Super User','Admin') ) ) ? '#D1E0C9' : '#999');
		$HQ_linkSts	=	(($HQ <> 'false' && in_array($usrPrv,array('Super User','Admin') ) ) ? true : false);
		
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

	//GET MULTIPLE CONSENT FORMS
	$CFormTmp	=	$obj->getRecord(
									'consent_forms_template',
									array('consent_id','consent_alias','consent_delete_status'),
									array('consent_delete_status <> '=> 'true'),
									'',
									array('consent_id '=>'ASC')
								 );
	
	$CFormAlias = array($light_green);	//	consentFormAliasArr
	
	if(is_array($CFormTmp) && count($CFormTmp) > 0 )
	{
		foreach($CFormTmp as $key => $data)
		{
			$Alias	=	stripslashes($data['consent_alias']);	
			$Del	=	$data['consent_delete_status'];
			
			if($Alias <> '')
			{
				$CFormAlias[] 	= $Alias;
				$CFormID[] 		= $data['consent_id'];	//consentFormTemplateSelectConsentId
			}	
		
		}
		
	}
	//echo '<pre>';	print_r($CFormAlias);
	/********************************************
	*											*
	*	Toggle Menu List / Left Slider			*
	*											*
	********************************************/
	
	$maincolor	=	array(	
							"#C06E2D",
							"#779169",
							"#779169",
							"#779169",
							"#C0AA1E",
							"#C06E2D",
							"#7d5d81",
							"#006699",
							"#779169",
							"#FF950E",
							"#6F5C7A"
						);
	
	$menuListArr =	array(
							'Consent Form' => array(),
							'Pre-Op Health' => array(
												'color'=>$light_green,
												'Health Questionnaire' => array(
													'link'=>'pre_op_health_quest.php',
													'form'=>'pre_op_health_ques_form',
													'cField'=>'confirmation_id',
													'table'=>'preophealthquestionnaire',
													'color' => $HQ_color,
													'linkStatus' => $HQ_linkSts),
													
												'Health Questionnaire' => array(
													'link'=>'pre_op_health_quest.php',
													'form'=>'pre_op_health_ques_form',
													'cField'=>'confirmation_id',
													'table'=>'preophealthquestionnaire'),	
													
												),
												
							'Nursing Record',
							'Physician Orders',
							'Anesthesia',
							$LeftOpLaserSlider,
							'Surgical',
							'Discharge Summary',
							'Post Op Inst. Sheet',
							'Physician Notes',
							'ePostIt'
						);
	
	
	
	$subMenuListArr[0] 	= $CFormAlias;
	$subMenuListArr[1] 	= array($light_green,'Health Questionnaire','H & P Clearance');
	$subMenuListArr[2] 	= array($heading_post_op_nursing_order,'Pre-Op', 'Post-Op');
	$subMenuListArr[3] 	= array($bgmid_orange_physician,'Pre-Op ', 'Post-Op ');
	$subMenuListArr[4]  = array($bgmid_blue_local_anes,'MAC/Regional', 'Pre-Op General', 'General','General Nurse Notes');
	$subMenuListArr[5]  = array($heading_op_room_record,'Intra-Op Record', 'Laser Procedure');
	$subMenuListArr[6]  = array($light_green,'Operative Report');
	$subMenuListArr[7]  = array($heading_discharge_summary_sheet,'Discharge Summary');
	$subMenuListArr[8]  = array($light_green,'Instruction Sheet');
	$subMenuListArr[9]  = array($heading_Amendments_notes,'Amendments');
	$subMenuListArr[10] = array($light_green, 'ePostIt');
	
	
	
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



<div class="toggled " id="slider_wrapper" style="">

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
	<!-- Sidebar -->
    	<a class="toggle_btn" id="toggle_btn1" style="">
        	<span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
		</a>
        
        <nav class="navbar navbar-inverse bs-sidebar-navbar-collapse-1 toggled toggled_1" id="sidebar-wrapper" role="navigation">
        
        		<div class="navbar-header">
                
                		<a class="toggle_btn style_2_Toggle" id="toggle_btn2" style="">
                        	<span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
						</a>
                        
				</div>
                
                
                <div class=" head_ul_side">
                	<a href="slider_new.html"><Span class="span_over">  Today's Visit 	</Span>	</a>
				</div>
                
                <ul class="nav sidebar-nav tabs " id="">
                
                	<?php
						
						$i	=	0;
						$innKeys = 1;
						
						foreach($menuListArr as $key => $sliderListText)
						{			
							$level			=	0 ;
							$mainCounter	=	count($menuListArr);
							$innerCounter	=	count($subMenuListArr[$key]);
							$dataTarget		=	'';
							$dataToggle		=	"";
							$class			=	($i == 0 )	?	'border_top_ul ' :	''	;
							
							if($innerCounter > 0)
							{
								$class		.=	' dropdown'.($i+1).' f_sidebar';
								$menuID		 =	str_replace(" ","_",$sliderListText).'_ul';
								$spanIcon	 =	'<span class="span_over caret"></span>';
								$dataTarget	 =	'data-target = "#'.$menuID.'"';
								$dataToggle	 =	'collapse';	
								 	
							}
							else
							{	
								$class	.=	'f_sidebar';
								
							}
							
					?>
                    		<li class="<?=$class?>"  <?=$dataTarget?> <?=$dataToggle?>  >
                            	<a href="javascript:void(0)">
                                	<Span class="span_over"><?php echo $sliderListText; ?></span>
									<?=$spanIcon?>
								</a>
							</li>
					<?php
						
							$i++;
							if( $innerCounter > 0)
							{
								$ct		=	1	;
								$level	=	sprintf('%02d',$ct)	;
					?>
								<ul class="dropdown-menu<?=$level?> collapse" id="<?=$menuID?>" role="menu">
                                <?php
								
									foreach($subMenuListArr[$key] as $innKey => $inner)
									{
										                                                 
										if($innKey > 0)
										{
											
											if($subMenuListArr[0][1] == $subMenuListArr[$key][$innKey])
											{
												$lnkname	=	"consent_surgery_form.php";
												$formName	=	"surgery_form";
												$patientconfirmationid1 ="confirmation_id";
												$tblename="surgery_consent_form";
												
												if($left_surgery_form!='false'){ 
													if(($usrPrv=='Super User') || ($usrPrv=='Admin')){
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
						}else if($subMenuListArr[0][2] ==$subMenuListArr[$key][$innKey]){
							$lnkname="conset_hippa_form.php";
							$formName = "hippa_form";
							$patientconfirmationid1 ="confirmation_id";
							$tblename="hippa_consent_form";
							
							if($left_hippa_form!='false'){ 
								if(($usrPrv=='Super User') || ($usrPrv=='Admin')){
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
						}else if($subMenuListArr[0][3] ==$subMenuListArr[$key][$innKey]){
							$lnkname="consent_assign_benefits_form.php";
							$formName = "assign_benifits_form";
							$patientconfirmationid1 ="confirmation_id";
							$tblename="benefit_consent_form";
							if($left_assign_benifits_form!='false'){ 
								if(($usrPrv=='Super User') || ($usrPrv=='Admin')){
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
						}else if($subMenuListArr[0][4] ==$subMenuListArr[$key][$innKey]){
							$lnkname="consent_insurance_card_form.php";
							$formName = "insurance_card_form";
							$patientconfirmationid1 ="confirmation_id";
							$tblename="insurance_consent_form";
							if($left_insurance_card_form!='false'){ 
								if(($usrPrv=='Super User') || ($usrPrv=='Admin')){
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
						}else if($subMenuListArr[1][1] ==$subMenuListArr[$key][$innKey]){
							$lnkname="pre_op_health_quest.php";
							$formName = "pre_op_health_ques_form";
							$patientconfirmationid1 ="confirmation_id";
							$tblename="preophealthquestionnaire";
							if($left_pre_op_health_ques_form!='false'){ 
								if(($usrPrv=='Super User') || ($usrPrv=='Admin')){
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
								if(($usrPrv=='Nursing Record') || ($usrPrv=='Super User') || ($usrPrv=='Admin')){
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
								if(($usrPrv=='Nursing Record') || ($usrPrv=='Super User') || ($usrPrv=='Admin')){
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
								if(($usrPrv=='Super User') || ($usrPrv=='Admin') || ($usrPrv=='Anesthesia')){
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
								if(($usrPrv=='Super User') || ($usrPrv=='Admin') || ($usrPrv=='Anesthesia')){
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
								if(($usrPrv=='Super User') || ($usrPrv=='Admin')){
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
								if(($usrPrv=='Super User') || ($usrPrv=='Admin')){
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
							
								if(($usrPrv == 'Super User') || ($usrPrv == 'Admin')){
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
								if(($usrPrv=='Nursing Record') || ($usrPrv=='Super User') || ($usrPrv=='Admin')){
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
								if(($usrPrv=='Super User') || ($usrPrv=='Admin') || ($usrPrv=='Anesthesia')){
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
								if(($usrPrv=='Super User') || ($usrPrv=='Admin')){
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
								if(($usrPrv=='Super User') || ($usrPrv=='Admin') || ($usrPrv=='Billing') || ($usrPrv=='Surgeon')){
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
								if(($usrPrv=='Super User') || ($usrPrv=='Admin') || ($usrPrv=='Billing') || ($usrPrv=='Surgeon')){
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
								if(($usrPrv=='Super User') || ($usrPrv=='Admin')){
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
						}else{							
							$lnkname="under_construction.php";
						}
						
						$kId=$key.'&innerKey='.$innKeys.'&preColor='.urlencode($color);
						$tempPrecolor = urlencode($color);
						$linkSessionId = "&patient_id=$patient_id&pConfId=$pConfId&ascId=$ascId";						
						$subId = $innKeys;
						$innKeys++;
						
						
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
								 
								 
							</span>
						</div>
						<?php 
					}                   
					
								$ct=$ct+1;
							
							}
						?>
			
					<?php                           
						
						} // End Main Menu For Each Loop
						
					?>
			
					
                		
                        <li class="border_top_ul f_sidebar">
                        	<a href="javascript:void(0)"><Span class="span_over"> 	Check List</Span>	</a>
						</li>
                        
                        <li class="dropdown01 f_sidebar" data-target="#c_forms_ul" data-toggle="collapse">
                        	<a href="javascript:void(0)"> <Span class="span_over">  Consent Forms </Span><span class="span_over caret"></span></a>
						</li>
                        
                        	<ul class="dropdown-menu01 collapse" id="c_forms_ul" role="menu">
                            	
                            	<li class="" data-target="#Hippa" data-toggle="collapse">
                                	<a href="javascript:void(0)">
                                    	<span class="span_over"> <b class="fa fa-chevron-right"></b> HIPAA Confirmation</Span>
                                        <span class="glyphicon glyphicon-print"></span>
									</a>
								</li>
                                
                                	<ul class="dropdown-menu02 collapse" id="Hippa">
                                    	
                                        <li class="">
                                        	<a href="javascript:void(0)"><span class="span_over"><b class="fa fa-caret-right"></b><b class="fa fa-caret-right"></b> Hippa </span></a>
										</li>
                                        
                                        <li class="">
                                        	<a href="javascript:void(0)"><span class="span_over"><b class="fa fa-caret-right"></b><b class="fa fa-caret-right"></b> HNT </span></a>
										</li>
									</ul>
                                    
								<li class=""><a href="javascript:void(0)"> 
                                	<Span class="span_over"><b class="fa fa-chevron-right"></b>  Check In Forms </Span>  <span class="glyphicon glyphicon-print"></span> 
								</a></li>
                                
                                <li class=""><a href="javascript:void(0)"> 
                                	<Span class="span_over"><b class="fa fa-chevron-right"></b> Check In Forms </Span>  <span class="glyphicon glyphicon-print"></span>
								</a></li>
                                
                                <li class=""><a href="javascript:void(0)"> 
                                	<Span class="span_over"><b class="fa fa-chevron-right"></b> Check In Forms </Span>  <span class="glyphicon glyphicon-print"></span> 
								</a></li>
                                
                                <li class=""><a href="javascript:void(0)"> 
                                	<Span class="span_over"><b class="fa fa-chevron-right"></b> Check In Forms </Span>  <span class="glyphicon glyphicon-print"></span> 
								</a></li>
                                
							</ul>
						
                        <li class="f_sidebar">
                        	<a href="javascript:void(0)"> <Span class="span_over">Pre-op Health </Span></a>
						</li>
                        
                        <li class="f_sidebar">
                            <a href="javascript:void(0)"> <Span class="span_over"> Nursing Record </Span></a>
                        </li>
                        
                       	<li class="f_sidebar">
                          <a href="javascript:void(0)" class=""> <Span class="span_over"> Physician Records </Span></a>
                        </li>
                        
                        <li class="f_sidebar">
                          <a href="javascript:void(0)" class="" > <Span class="span_over"> Anesthesia</Span></a>
                        </li>
                        
                        <li class="f_sidebar">
                            <a href="javascript:void(0)"> <Span class="span_over"> Operating Room </Span></a>
                        </li>
                        
                        <li class="f_sidebar">
                            <a href="javascript:void(0)"> <Span class="span_over"> Surgical </Span></a>
                        </li>
                        
                        <li class="f_sidebar">
                            <a href="javascript:void(0)"> <Span class="span_over"> Discharge Summary </Span></a>
                        </li>
                        
                        <li class="f_sidebar">
                            <a href="javascript:void(0)"> <Span class="span_over"> Post-Op Inst. Sheet </Span></a>
                        </li>
                        
                        <li class="f_sidebar">
                            <a href="javascript:void(0)"> <Span class="span_over"> Physician Notes</Span></a>
                        </li>
                        <li class="f_sidebar">
                            <a href="javascript:void(0)"> <Span class="span_over"> ePost It</Span></a>
                        </li>

						
				</ul>
            	                    
		</nav>
        
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