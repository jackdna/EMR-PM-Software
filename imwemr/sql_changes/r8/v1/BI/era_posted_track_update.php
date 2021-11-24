<?php
$ignoreAuth = true;
$skip_file="skipthisfile";
require_once("../../../../config/globals.php");
require_once('../../../../library/classes/acc_functions.php');

if(empty($start_val) == true){
	$start_val = 0;
}
$end = 1;

$get_era = imw_query("SELECT 835_Era_Id,chk_issue_EFT_Effective_date,REF_provider_ref_id FROM electronicfiles_tbl join era_835_details on era_835_details.electronicFilesTblId=electronicfiles_tbl.id where post_status!='Not Posted' and era_835_details.chk_issue_EFT_Effective_date>='2018-01-01' order by electronicfiles_tbl.id,era_835_details.835_Era_Id  limit $start_val , $end");
$get_era_rows = imw_fetch_array($get_era);
$era_835_id= $get_era_rows['835_Era_Id'];
$chk_issue_EFT_Effective_date= $get_era_rows['chk_issue_EFT_Effective_date'];
$REF_provider_ref_id= $get_era_rows['REF_provider_ref_id'];

$get_proc_era = imw_query("SELECT * FROM era_835_proc_details where 835_Era_Id='$era_835_id' order by 835_Era_proc_Id");
while($get_proc_era_rows = imw_fetch_array($get_proc_era)){
	$era_835_proc_id= $get_proc_era_rows['835_Era_proc_Id'];
	$CAS_type= $get_proc_era_rows['CAS_type'];
	$CAS_reason_code= $get_proc_era_rows['CAS_reason_code'];
	$CAS_amt= $get_proc_era_rows['CAS_amt'];
	$ERA_patient_details_id = $get_proc_era_rows['ERA_patient_details_id'];
	$REF_prov_identifier = $get_proc_era_rows['REF_prov_identifier'];			
	$REF_prov_identifier = @preg_replace('/\s+/','',$REF_prov_identifier);
	$SVC_proc_code = $get_proc_era_rows['SVC_proc_code'];
	$SVC_mod_code = $get_proc_era_rows['SVC_mod_code'];
	$DOS = $get_proc_era_rows['DTM_date'];
	
	$pat_qry = imw_query("select CLP_claim_submitter_id,CLP_claim_status from era_835_patient_details where ERA_patient_details_id = '$ERA_patient_details_id'");
	$pat_row = imw_fetch_array($pat_qry);
	$CLP_claim_submitter_id = $pat_row['CLP_claim_submitter_id'];
	$CLP_claim_status = $pat_row['CLP_claim_status'];

	if($CLP_claim_status == 1 || $CLP_claim_status==2 || $CLP_claim_status==3 || $CLP_claim_status==4 || $CLP_claim_status==19 || $CLP_claim_status==20 || $CLP_claim_status==21 || $CLP_claim_status==22){ 
		$ins_company_no=$insCoType='';
		if($CLP_claim_status == 1 || $CLP_claim_status == 19){
			$insCoType = 'Primary';
			$ins_company_no=1;
		}else if($CLP_claim_status == 2 || $CLP_claim_status == 20){
			$insCoType = 'Secondary';
			$ins_company_no=2;
		}else if($CLP_claim_status == 3 || $CLP_claim_status == 21){
			$insCoType = 'Tertiary';
			$ins_company_no=3;
		}
		
		$mcrPos = strpos($REF_prov_identifier, 'MCR');
		if($mcrPos){
			$encounter_id = trim(substr($REF_prov_identifier, 0, $mcrPos));
			$restStr = substr($REF_prov_identifier, $mcrPos+3);
			if(strpos($restStr, $billing_global_tsuc_separator.'TSUC'.$billing_global_tsuc_separator)){
				$tsucPos = strpos($restStr, $billing_global_tsuc_separator.'TSUC'.$billing_global_tsuc_separator);
				$tsucId = $tsucPos+6;
			}else if(strpos($restStr, '_TSUC_')){
				$tsucPos = strpos($restStr, '_TSUC_');
				$tsucId = $tsucPos+6;
			}else if(strpos($REF_prov_identifier, 'TSUC')){
				$tsucPos = strpos($restStr, 'TSUC');
				$tsucId = $tsucPos+4;
			}
			if($tsucId){
				$chargeListDetailId = substr($restStr, 0, $tsucPos);
				$tsuc_identifier = substr($restStr, $tsucId);					
				if(strpos($tsuc_identifier, ',')){
					$tsuc_identifier = trim(substr($tsuc_identifier, 0, strpos($tsuc_identifier, ',')));
				}
				if(!$insCoType && is_numeric($tsuc_identifier)){
					//GET BATCH FILE INFO
					$getBatchInfoQry = imw_query("SELECT ins_company_id,ins_comp FROM batch_file_submitte WHERE Transaction_set_unique_control = '$tsuc_identifier'");
					$getBatchInfoRow = imw_fetch_assoc($getBatchInfoQry);
					$ins_company_id = $getBatchInfoRow['ins_company_id'];
					$ins_comp = $getBatchInfoRow['ins_comp'];
					if($insCoType==""){
						if($ins_comp == 'primary'){
							$insCoType = 'Primary';
							$ins_company_no=1;
							if($CLP_claim_status!=4){
								$CLP_claim_status=1;
							}
						}else if($ins_comp == 'secondary'){
							$insCoType = 'Secondary';
							$ins_company_no=2;
							if($CLP_claim_status!=4){
								$CLP_claim_status=2;
							}
						}
					}
				}
			}else{
				$chargeListDetailId = '';
			}
		}else{
			$encounter_id = '';
			$chargeListDetailId = '';
					
			if(is_numeric($CLP_claim_submitter_id)){
				
				$cpt_fee_id_arr=array();	
				$cpt_qry=imw_query("select cpt_fee_id from cpt_fee_tbl where cpt4_code='$SVC_proc_code'");
				while($cpt_row=imw_fetch_array($cpt_qry)){
					$cpt_fee_id_arr[$cpt_row['cpt_fee_id']]=$cpt_row['cpt_fee_id'];
				}							
				$cpt_fee_id=implode(',',$cpt_fee_id_arr);			
				
				$modifiersId1="";
				$modifiersId2="";
				$SVC_mod_code_exp="";
				$SVC_mod_code_exp=explode(',',$SVC_mod_code);
				if($SVC_mod_code_exp[0]!=""){
					$modifiers_code1=trim($SVC_mod_code_exp[0]);
					$qry = imw_query("select * from modifiers_tbl where mod_prac_code = '$modifiers_code1' order by delete_status asc limit 0,1");
					$getModID = imw_fetch_object($qry);
					$modifiersId1 = $getModID->modifiers_id;
				}
				if($SVC_mod_code_exp[1]!=""){
					$modifiers_code2=trim($SVC_mod_code_exp[1]);
					$qry = imw_query("select * from modifiers_tbl where mod_prac_code = '$modifiers_code2' order by delete_status asc limit 0,1");
					$getModID = imw_fetch_object($qry);
					$modifiersId2 = $getModID->modifiers_id;
				}
				
				$getChargeListDetailsStr = "SELECT * FROM patient_charge_list a,patient_charge_list_details b WHERE b.del_status='0' and a.patient_id = '$CLP_claim_submitter_id' AND a.charge_list_id = b.charge_list_id AND a.date_of_service = '$DOS' AND b.procCode in($cpt_fee_id)";
				
				if($modifiersId1>0 && $modifiersId2>0){
					$getChargeListDetailsStr.=" AND ((b.modifier_id1 = '$modifiersId1' and b.modifier_id2 = '$modifiersId2') 
												or (b.modifier_id1 = '$modifiersId2' and b.modifier_id2 = '$modifiersId1'))";
				}else{							
					if($modifiersId1>0){
						$getChargeListDetailsStr.=" AND b.modifier_id1 = '$modifiersId1'";
					}
					if($modifiersId2>0){
						$getChargeListDetailsStr.=" AND b.modifier_id2 = '$modifiersId2'";
					}
					if($modifiersId1<=0 && $modifiersId2<=0){
						$getChargeListDetailsStr.=" AND b.modifier_id1 = '' and b.modifier_id2 = ''";
					}
				}
				$getChargeListDetailsQry = imw_query($getChargeListDetailsStr);
				$countRows = imw_num_rows($getChargeListDetailsQry);
				if($countRows){
					while($getChargeListDetailsRows = imw_fetch_assoc($getChargeListDetailsQry)){
						$encounterId = $getChargeListDetailsRows['encounter_id'];
						$charge_list_id = $getChargeListDetailsRows['charge_list_id'];
						$listChargeDetailId = $getChargeListDetailsRows['charge_list_detail_id'];							
					}
					if($countRows==1){
						$encounter_id = $encounterId;
						$chargeListDetailId = $listChargeDetailId;
					}
				}
			}
		}
		
		if($REF_provider_ref_id!="" && $CLP_claim_status!=4){
			$getInsStr = "select primaryInsuranceCoId,secondaryInsuranceCoId from patient_charge_list where del_status='0' and encounter_id = '$encounter_id'";				
			$getInsQry = imw_query($getInsStr);
			$getInsRow = imw_fetch_array($getInsQry);
			$insuranceCoId_pri = $getInsRow['primaryInsuranceCoId'];
			$insuranceCoId_sec = $getInsRow['secondaryInsuranceCoId'];
			if($insuranceCoId_pri!=$insuranceCoId_sec){
				$getInsStr2 = "select id from insurance_companies where Payer_id = '$REF_provider_ref_id'";				
				$getInsQry2 = imw_query($getInsStr2);
				while($getInsRow2 = imw_fetch_array($getInsQry2)){
					if($getInsRow2['id']==$insuranceCoId_pri){
						$ins_company_no=1;
					}else if($getInsRow2['id']==$insuranceCoId_sec){
						$ins_company_no=2;
					}
				}
			}
		}
	
		$chk_era_835_proc_posted=imw_query("select id from era_835_proc_posted where era_835_proc_id='$era_835_proc_id'");
		if(imw_num_rows($chk_era_835_proc_posted)==0 && $chargeListDetailId>0 && $ins_company_no>0){
			
			$qry = imw_query("select patient_id,charge_list_id from patient_charge_list_details where del_status='0' and charge_list_detail_id = '$chargeListDetailId'");
			$chargesBalDetail = imw_fetch_object($qry);
			$patient_id=$chargesBalDetail->patient_id;
			$charge_list_id=$chargesBalDetail->charge_list_id;
			if($charge_list_id>0){
				$entered_date_time=date('Y-m-d H:i:s');
				imw_query("insert era_835_proc_posted set era_835_proc_id='$era_835_proc_id',patient_id='$patient_id',encounter_id='$encounter_id',
				charge_list_id='$charge_list_id',charge_list_detail_id='$chargeListDetailId',cas_type='$CAS_type',cas_code='$CAS_reason_code',
				cas_amt='$CAS_amt',ins_type='$ins_company_no',chk_date='$chk_issue_EFT_Effective_date',entered_date='$entered_date_time'");
			}
		}
	}
}

$msg_info[] = "<br><b>".($start_val + $end)." Records Updates era posted track run Successfully!</b>";

?>
<html>
<head>
<title>Mysql Updates - Encounter Payment Paid Flag</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
	<font face="Arial, Helvetica, sans-serif" size="2"><?php echo(implode("<br>",$msg_info));?></font>
	<form action="" method="get" name="submit_frm" id="submit_frm">
		<input type="hidden" name="start_val" value="<?php print $start_val + $end; ?>">
	</form>
	<?php
	if(imw_num_rows($get_era) > 0){
	?>
	<script type="text/javascript">
		document.getElementById("submit_frm").submit();
	</script>
	<?php
	}
	?>
</body>
</html>