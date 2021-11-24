<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	
	$consentForms			=	''	;
	$epostList				=	'';
	$rsNote_bk_class		= "epost_title";
	
	$surgeryCenterSettings	=	$objManageData->loadSettings('peer_review');
	$surgeryCenterPeerReview=	$surgeryCenterSettings['peer_review'];
	
	$epostConfirmPatient	=	$objManageData->getExtractRecord('patientconfirmation','patientConfirmationId',$pConfId,'surgeonId');
	$epostPCSurgeonId			=	$epostConfirmPatient['surgeonId'];
	
	$practiceNameMatch	=	'';
	if($surgeryCenterPeerReview == 'Y' && $_SESSION['loginUserType'] == 'Surgeon')
	{
		$practiceNameMatch	=	$objManageData->getPracMatchUserId($_SESSION['loginUserId'],$epostPCSurgeonId);
	}
	
	
?>
	<div id="epostMainDiv" style="position:absolute;transparent;top:30px;left:10px;width:auto height:auto;">
    <?php
		$qryGetEpostedata = "SELECT ep.epost_id, ep.epost_data,ep.T_time, TIME_FORMAT(ep.T_time, '%l:%i %p') as ePostTime,
								ep.consent_template_id, ep.consentAutoIncId,
								IFNULL(concat(SUBSTRING(usr1.fname,1,1),SUBSTRING(usr1.lname,1,1)),'') AS created_operator_name, 
								IFNULL(concat(SUBSTRING(usr2.fname,1,1),SUBSTRING(usr2.lname,1,1)),'') AS modified_operator_name
								FROM eposted ep 
								LEFT JOIN users AS usr1 ON (usr1.usersId=ep.created_operator_id)
								LEFT JOIN users AS usr2 ON (usr2.usersId=ep.modified_operator_id)
								WHERE ep.table_name = '$epost_table_name' 
								AND ep.patient_conf_id = '$pConfId' 
								";
		$resGetEpostedata = imw_query($qryGetEpostedata) or die(imw_error());
		$intTotRowRetrive = imw_num_rows($resGetEpostedata);
		if($intTotRowRetrive>0) {
			$rsNotes1 = array();
			$intCountChild = 0;
			$top = 0;
			$left = 0;
			while($rowResGetEpostedata = imw_fetch_assoc($resGetEpostedata)) {
				$rsNotes1[] = $rowResGetEpostedata;
				$tableID = $rowResGetEpostedata['epost_id'];
				$consentMultipleAutoIncrId = $rowResGetEpostedata['consentAutoIncId'];
				$consentTemplateIdEpost = $rowResGetEpostedata['consent_template_id'];
				//$EpostTime = $rowResGetEpostedata['ePostTime'];
				$EpostTime = $objManageData->getTmFormat($rowResGetEpostedata['T_time']);
				$epostdata = stripslashes($rowResGetEpostedata['epost_data']);
				$created_operator_name 	= $rowResGetEpostedata['created_operator_name'];
				$modified_operator_name = $rowResGetEpostedata['modified_operator_name'];
				$operatorVal = "";
				if($modified_operator_name) {
					$operatorVal = "Modified - ".$modified_operator_name;	
				}else if($created_operator_name) {
					$operatorVal = "Created - ".$created_operator_name;	
				}
				?>
				<div id="epostMainDivChild<?php echo $intCountChild;?>" class="drsElement drsMoveHandle" style="top:<?php echo $top;?>px; left:<?php echo $left;?>px;width:310px;position:absolute;background-color:transparent;border:0px none;z-index:11">
					<div class="epostHead <?php echo $rsNote_bk_class; ?>" style="width: 100%;text-align:left;border-top-right-radius:5px;border-top-left-radius:5px;">
						<span style="">E-postit</span>
						<span style=""><?php echo $operatorVal.' '.$EpostTime; ?></span>
						<button style="opacity: .9;" type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="document.getElementById('epostMainDivChild<?php echo $intCountChild;?>').style.display='none';">
							<span aria-hidden="true" style="">x</span>
						</button>
					</div>
					<div class="text-left" style="width: 100%;border: 1px solid #ababab;border-top: none;border-bottom:1px solid #EEEEEE;background-color:#FFFFFF;height:80px;overflow-y: auto;overflow-x:auto; padding: 5px;">
						<?php echo $epostdata; ?>
					</div>
					<div class="text-left" style="width:100%;border: 1px solid #ababab;border-top:none;border-bottom-right-radius:5px;border-bottom-left-radius:5px;background-color:#FFFFFF;padding:5px;">
						<?php if($practiceNameMatch <> 'yes'){ ?>
            <a id="CancelBtn" class="btn btn-danger epost_del" onClick="deleteEpost(<?php echo $tableID; ?>,'<?php echo $consentTemplateIdEpost;?>','<?php echo $consentMultipleAutoIncrId?>','<?php echo $pConfId;?>');" href="javascript:void(0)">
							<b class="fa fa-times"></b>&nbsp;Delete
						</a>
           <?php } ?> 
					</div>
					<div class="clearfix"></div>
				</div>
				<?php		
				$intCountChild++;
				$left+=320;
				if($left==960){
					//$top = 0;
					$left = 0;
					$top+=160;
				}
			}
		}	
	
	?>	
    </div>
    
<?php

    if($intTotRowRetrive > 0)
	{
		foreach($rsNotes1 as $row)
		{
			$epostList	.=	"<a class=\"btn-xs btn-primary\" title=\"ePostIt\" onMouseOver=\"showEpost(\'".$row['epost_id']."\',\'".$pConfId."\',\'".$practiceNameMatch."\');\"><b class=\"fa fa-comment\"></b></a>";
		}
	}
	
?>

<script>
	
	$(function()
	{
		top.$("#post,#SliderHeadConsent,#SliderHeadTitle,#SliderHeadEpost").html('');
		
		var PgSrc	=	document.getElementById('frmAction').value ; 
		var SHT	=	'';
		var EKG	=	'<?=$ekgHpLink?>' ; 
		
		var BGC 	=	'#C06E2D';
		
		if( PgSrc == 'check_list.php' )
		{
				SHT	=	'Safety Check List';	
				BGC	=	'#C06E2D';
		}
		else if( PgSrc == 'consent_multiple_form.php')
		{
				SHT	=	'<?=addslashes($surgery_consent_name)?>';	
				BGC	=	'#779169';
		}
		else if( PgSrc == 'pre_op_health_quest.php')
		{
				SHT	=	'Pre-Op Health Questionnaire';		
				BGC	=	'#779169';
		}
		else if( PgSrc == 'history_physicial_clearance.php')
		{
				SHT	=	'H & P Clearance';		
				BGC	=	'#779169';
		}
		else if( PgSrc == 'pre_op_nursing_record.php')
		{
				SHT	=	'Pre-Op Nursing Record';		
				BGC	=	'#C0AA1E';
		}
		else if( PgSrc == 'pre_nurse_alderate_record.php')
		{
				SHT	=	'Pre-Op Aldrete Scoring System';		
				BGC	=	'#C0AA1E';
		}
		else if( PgSrc == 'post_op_nursing_record.php')
		{
				SHT	=	'Post-Op Nursing Record';		
				BGC	=	'#C0AA1E';
		}
		else if( PgSrc == 'post_nurse_alderate_record.php')
		{
				SHT	=	'Post-Op Aldrete Scoring System';		
				BGC	=	'#C0AA1E';
		}
		else if( PgSrc == 'pre_op_physician_orders.php')
		{
				SHT	=	'Pre-Op Physician Orders';		
				BGC	=	'#C06E2D';
		}
		else if( PgSrc == 'post_op_physician_orders.php')
		{
				SHT	=	'Post-Op Physician Orders';		
				BGC	=	'#C06E2D';
		}
		else if( PgSrc == 'local_anes_record.php')
		{
				SHT	=	'MAC/Local/Regional Anesthesia Record';		
				BGC	=	'#3232F0';
		}
		
		else if( PgSrc == 'pre_op_general_anes.php')
		{
				SHT	=	'Pre-Op General Anesthesia Record';		
				BGC	=	'#3232F0';
		}
		
		else if( PgSrc == 'gen_anes_rec.php')
		{
				SHT	=	'General Anesthesia Record';		
				BGC	=	'#3232F0';
		}
		else if( PgSrc == 'gen_anes_nurse_notes.php')
		{
				SHT	=	'General Anesthesia Nurses Notes';		
				BGC	=	'#3232F0';
		}
		else if( PgSrc == 'op_room_record.php')
		{
				SHT	=	'Operating Room Record';		
				BGC	=	'#006699';
		}
		else if( PgSrc == 'laser_procedure.php')
		{
				SHT	=	'Laser Procedure';		
				BGC	=	'#006699';
		}
		else if( PgSrc == 'operative_record.php')
		{
				SHT	=	'Operative Report';		
				BGC	=	'#779169';
		}
		else if( PgSrc == 'discharge_summary_sheet.php')
		{
				SHT	=	'Discharge Summary Sheet';		
				BGC	=	'#ff950e';
		}
		else if( PgSrc == 'instructionsheet.php')
		{
				SHT	=	'Instruction Sheet	';		
				BGC	=	'#779169';
		}
		else if( PgSrc == 'medication_reconciliation_sheet.php')
		{
				SHT	=	'Medication Reconciliation Sheet';		
				BGC	=	'#779169';
		}
		else if( PgSrc == 'transfer_followups.php')
		{
				SHT	=	'Transfer &amp; Follow-up';		
				BGC	=	'#C06E2D';
		}
		else if( PgSrc == 'amendments_notes.php')
		{
				SHT	=	'Amendment Notes';		
				BGC	=	'#7D5D81';
		}
		else if( PgSrc == 'injection_misc.php')
		{
				SHT	=	'Injection/Miscellaneous';		
				BGC	=	'#006699';
		}
		
		
		
		//if(EKG !== '' ) 	{ 
				var ECF = EKG
       // }
		var EPL		=	'<?=$epostList?>';
		
		//if(ECF !== '' )
			top.$("#SliderHeadConsent").html(ECF).css({'display':'inline-block','color':'#FFF'}) ;
		if(BGC !== '')
			top.$("#SliderHeadTitle").css('background-color',BGC);	
		if(SHT !== '' )
			top.$("#SliderHeadTitle").html(SHT).css('display','inline-block');
		if(EPL !== '' )
			top.$("#SliderHeadEpost").html(EPL).css('display','inline-block');
		
	});
	
</script>