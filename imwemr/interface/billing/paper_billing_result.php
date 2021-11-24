<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
$ins_type=""; 
if($_REQUEST['InsComp']==1){
	$ins_type="Primary ";
	$ins_col="primaryInsuranceCoId";
	$ins_payment_method="Insurance_payment";
}else if($_REQUEST['InsComp']==2){
	$ins_type="Secondary ";
	$ins_col="secondaryInsuranceCoId";
	$ins_payment_method="secondary_payment_method";
}else if($_REQUEST['InsComp']==3){
	$ins_type="Tertiary ";
	$ins_col="tertiaryInsuranceCoId";
	$ins_payment_method="secondary_payment_method";
} 

//------------------------ Get Modifier Detail ------------------------//
$qry = imw_query("select * from modifiers_tbl");
while($row=imw_fetch_array($qry)){	
	$mod_code_arr[$row["modifiers_id"]]=$row["mod_prac_code"];
}
$Posted_Start_date=getDateFormatDB($_REQUEST['Posted_Start_date']);
$Posted_End_date=getDateFormatDB($_REQUEST['Posted_End_date']);
$DOS_Start_date=getDateFormatDB($_REQUEST['DOS_Start_date']);
$DOS_End_date=getDateFormatDB($_REQUEST['DOS_End_date']);
//------------------------ Get Modifier Detail ------------------------//
$charges_qry = "select patient_charge_list.charge_list_id,patient_charge_list.encounter_id,patient_charge_list.date_of_service,patient_charge_list.patient_id, 
				patient_charge_list.primaryInsuranceCoId,patient_charge_list.secondaryInsuranceCoId,patient_charge_list.tertiaryInsuranceCoId,
				patient_charge_list.claim_ctrl_pri,patient_charge_list.claim_ctrl_sec,patient_charge_list.claim_ctrl_ter,patient_charge_list.reff_phy_nr,
				patient_charge_list.reff_phy_id,patient_charge_list.primaryProviderId,
				patient_charge_list_details.procCode,patient_charge_list_details.units,patient_charge_list_details.procCharges,patient_charge_list_details.totalAmount,
				patient_charge_list_details.modifier_id1,patient_charge_list_details.modifier_id2,patient_charge_list_details.modifier_id3,
				patient_charge_list_details.diagnosis_id1,patient_charge_list_details.diagnosis_id2,patient_charge_list_details.diagnosis_id3,
				patient_charge_list_details.diagnosis_id4,patient_charge_list_details.diagnosis_id5,patient_charge_list_details.diagnosis_id6,
				patient_charge_list_details.diagnosis_id7,patient_charge_list_details.diagnosis_id8,patient_charge_list_details.diagnosis_id9,
				patient_charge_list_details.diagnosis_id10,patient_charge_list_details.diagnosis_id11,patient_charge_list_details.diagnosis_id12,
				cpt_fee_tbl.cpt_prac_code,users.user_npi,users.TaxonomyId,users.fname,users.mname,users.lname
				from patient_charge_list
				join patient_charge_list_details on patient_charge_list.charge_list_id = patient_charge_list_details.charge_list_id 
				join insurance_companies on insurance_companies.id = patient_charge_list.$ins_col
				join users on users.id = patient_charge_list.primaryProviderId
				join cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode
				where patient_charge_list.del_status='0' and patient_charge_list_details.del_status='0' and patient_charge_list_details.differ_insurance_bill != 'true' and insurance_companies.name != 'SELF PAY'";
if($_REQUEST['Posted_Start_date']!="" && $_REQUEST['Posted_End_date']!=""){
	$charges_qry .= " and (patient_charge_list.postedDate between '".$Posted_Start_date."' and '".$Posted_End_date."')";
}else if($_REQUEST['Posted_Start_date']!=""){
	$charges_qry .= " and (patient_charge_list.postedDate >='".$Posted_Start_date."')";
}else if($_REQUEST['Posted_End_date']!=""){
	$charges_qry .= " and (patient_charge_list.postedDate <='".$Posted_End_date."')";
}
if($_REQUEST['DOS_Start_date']!="" && $_REQUEST['DOS_End_date']!=""){
	$charges_qry .= " and (patient_charge_list.date_of_service between '".$DOS_Start_date."' and '".$DOS_End_date."')";
}else if($_REQUEST['DOS_Start_date']!=""){
	$charges_qry .= " and (patient_charge_list.date_of_service >='".$DOS_Start_date."')";
}else if($_REQUEST['DOS_End_date']!=""){
	$charges_qry .= " and (patient_charge_list.date_of_service <='".$DOS_End_date."')";
}
$ins_gro_qry="";
if(count($_REQUEST['insurance_gro'])>0){
	$insurance_gro_imp=implode(',',$_REQUEST['insurance_gro']);
	$ins_gro_qry = " insurance_companies.groupedIn in($insurance_gro_imp)";
}
if(count($_REQUEST['Insurance'])>0){
	$Insurance_imp=implode(',',$_REQUEST['Insurance']);
	$charges_qry .= " and (insurance_companies.id in($Insurance_imp) ";
	if($ins_gro_qry!=""){
		$charges_qry .= " or ".$ins_gro_qry;
	}
	$charges_qry .= " ) ";
}else{
	if($ins_gro_qry!=""){
		$charges_qry .= " and ".$ins_gro_qry;
	}
}
if($_REQUEST['physicians']>0){
	$charges_qry .= " and patient_charge_list.primaryProviderId = '".$_REQUEST['physicians']."'";
}
if($_REQUEST['groups']>0){
	$charges_qry .= " and  patient_charge_list.gro_id = '".$_REQUEST['groups']."'";
}
if($_REQUEST['inc_elec_claims']==""){
	$charges_qry .=" and insurance_companies.$ins_payment_method = 'HCFA1500'";
}else{
	$charges_qry .=" and insurance_companies.claim_type != '1'";
}
if($_REQUEST['Printub']!='' || $_REQUEST['WithoutPrintub']!=''){
	$charges_qry .=" and insurance_companies.institutional_type !='INST_PROF'";
}
if($_REQUEST['InsComp']==1){
	$charges_qry .=" and patient_charge_list.primary_paid = 'false' and patient_charge_list.primarySubmit = '0'";
}else if($_REQUEST['InsComp']==2){
	$charges_qry .=" and patient_charge_list.primary_paid = 'true' and patient_charge_list.secondary_paid = 'false' and patient_charge_list.secondarySubmit = '0'";
}else if($_REQUEST['InsComp']==3){
	$charges_qry .=" and patient_charge_list.primary_paid = 'true' and patient_charge_list.secondary_paid = 'true' and patient_charge_list.tertiary_paid = 'false' and patient_charge_list.tertairySubmit = '0'";
}
$charges_qry .= " and patient_charge_list.submitted = 'true' and patient_charge_list.enc_accept_assignment !='2'
				  and (patient_charge_list.totalBalance > '0' OR (patient_charge_list.postedAmount > 0 AND patient_charge_list.date_of_service >= '2013-01-01')) 
				  and patient_charge_list_details.posted_status='1' and patient_charge_list_details.claim_status='0' and cpt_fee_tbl.not_covered = '0'
				  and patient_charge_list_details.proc_selfpay!='1' group by patient_charge_list_details.charge_list_detail_id order by patient_charge_list.date_of_service asc";
$charges_run=imw_query($charges_qry); 
while($row=imw_fetch_array($charges_run)){
	$chl_arr[$row['charge_list_id']]=$row;
	$chl_enc_arr[$row['encounter_id']]=$row['encounter_id'];
	$chl_pat_arr[$row['patient_id']]=$row['patient_id'];
	$chl_cpt_arr[$row['charge_list_id']][$row['procCode']]=$row['cpt_prac_code'];
	$chl_unit_arr[$row['charge_list_id']][]=$row['units'];
	$chl_charges_arr[$row['charge_list_id']][]=$row['totalAmount'];
	$chl_ref_phy_arr[$row['charge_list_id']]=$row['reff_phy_id'];
	for($i=1;$i<13;$i++){
		if($row['modifier_id'.$i]>0){
			$chl_mod_arr[$row['charge_list_id']][$row['modifier_id'.$i]]=$mod_code_arr[$row['modifier_id'.$i]];
		}
		if($row['diagnosis_id'.$i]!=''){
			$chl_dx_arr[$row['charge_list_id']][$row['diagnosis_id'.$i]]=$row['diagnosis_id'.$i];
		}
	}
	
	$PhyName  = $row['lname'].', ';
	$PhyName .= $row['fname'].' ';
	$PhyName .= $row['mname'];
	$chl_usr_arr[$row['primaryProviderId']]=$PhyName;
}

$chl_enc_imp = implode(",",$chl_enc_arr);
$chl_pat_imp = implode(",",$chl_pat_arr);
$chl_ref_phy_imp = implode(",",$chl_ref_phy_arr);

//------------------------ Get Encounter Submitted Detail ------------------------//
$qry=imw_query("select encounter_id from submited_record where encounter_id in($chl_enc_imp)");
while($row=imw_fetch_array($qry)){
	$biil_type_enc_arr[$row['encounter_id']]=$row['encounter_id'];
}
//------------------------ Get Encounter Submitted Detail ------------------------//

//------------------------ Get Patients Detail ------------------------//
$qry=imw_query("select lname,fname,mname,id,sex from patient_data where id in($chl_pat_imp)");
while($row=imw_fetch_array($qry)){
	$patientName  = $row['lname'].', ';
	$patientName .= $row['fname'].' ';
	$patientName .= $row['mname'];
	$pat_name_arr[$row['id']]=$patientName;
	$pat_sex_arr[$row['id']]=$row['sex'];
}
//------------------------ Get Patients Detail ------------------------//

//------------------------ Get Reffering Physician Detail ------------------------//
$qry=imw_query("select physician_Reffer_id,NPI from refferphysician where physician_Reffer_id in($chl_ref_phy_imp)");
while($row=imw_fetch_array($qry)){
	$ref_phy_data[$row['physician_Reffer_id']]=$row['NPI'];
}
//------------------------ Get Reffering Physician Detail ------------------------//
$valid_claims_arr=$invalid_claims_arr=array();
foreach($chl_arr as $chl_key=>$chl_val){
	$chl_data=$chl_arr[$chl_key];
	if(trim($pat_sex_arr[$chl_data['patient_id']])==""){
		$error[$chl_key][] = 'Patient Gender Infomation is Required.';
	}
	if($chl_data['reff_phy_nr']==0 && ($_REQUEST['PrintCms']!='' || $_REQUEST['PrintCms_white']!='')){
		if($chl_data['reff_phy_id']>0){
			if(trim($ref_phy_data[$chl_data['reff_phy_id']])==""){
				$error[$chl_key][] = 'Referring Physician NPI # is Required.';
			}
		}else{
			if(trim($chl_data['user_npi'])==""){
				$error[$chl_key][] = 'Referring Physician NPI # is Required.';
			}
		}
	}
	if(trim($chl_data['user_npi'])==""){
		$error[$chl_key][] = 'Rendering Physician NPI # is Required.';
	}
	if(trim($chl_data['TaxonomyId'])==""){
		$error[$chl_key][] = 'Rendering Physician Taxonomy # is Required.';
	}
	if(count($error[$chl_key])>0){
		$claim_arr[1][$chl_data['primaryProviderId']][$chl_key]=$chl_data;
		$invalid_claims_arr[$chl_key]=array_sum($chl_charges_arr[$chl_key]);
	}else{
		$claim_arr[0][$chl_data['primaryProviderId']][$chl_key]=$chl_data;
		$valid_claims_arr[$chl_key]=array_sum($chl_charges_arr[$chl_key]);
	}
}

if($_REQUEST['Printub']!=""){
	$print_paper_type=$_REQUEST['Printub'];
}else if($_REQUEST['WithoutPrintub']!=""){
	$print_paper_type=$_REQUEST['WithoutPrintub'];
}else if($_REQUEST['PrintCms_white']!=""){
	$print_paper_type=$_REQUEST['PrintCms_white'];
}else{
	$print_paper_type=$_REQUEST['PrintCms'];
}
?>
<form name="frm_billing_res" id="frm_billing_res" action="" method="post">
<table class="table table-bordered table-hover table-striped">
	<input type="hidden" name="print_ins_type" id="print_ins_type" value="<?php echo $_REQUEST['InsComp']; ?>" />
	<input type="hidden" name="print_paper_type" id="print_paper_type" value="<?php echo $print_paper_type; ?>" />
	<tr class="grythead">
		<th>
			<div class="checkbox">
				<input type="checkbox" name="chkbx_all" id="chkbx_all" onClick="return chk_all();"/>
				<label for="chkbx_all"></label>
			</div>
		</th>
		<th>Format</th>
		<th>Patient Name - Id</th>
		<th>E. Id</th>
		<th>DOS</th>
		<th class="text-nowrap"><?php echo $ins_type; ?>Ins.</th>
		<th style="width:20%;">CPT </th>
		<th class="text-nowrap">DX Codes</th>
		<th>Units</th>
		<th>Charges</th>
		<th>Modifiers</th>
		<th class="text-nowrap">Claim Control#</th>
	</tr>
	<?php
		ksort($claim_arr);
		foreach($claim_arr as $claim_key=>$claim_val){ 
			$claim_type_arr=$claim_arr[$claim_key];
			if($claim_key==1){
				echo "<tr><td colspan='12' class='text-center alert alert-danger'><label>Encounters With Errors</label></td></tr>";
			}
			foreach($claim_type_arr as $prov_key=>$prov_val){
				echo '<tr><td colspan="12" class="physbar">Physician: '.$chl_usr_arr[$prov_key].'</td></tr>';
				$claim_data=$claim_type_arr[$prov_key];
				foreach($claim_data as $chl_key=>$chl_val){
					$chl_data=$claim_data[$chl_key];
					$ins_comp="";
					if($_REQUEST['InsComp']==1){
						$ins_comp=$ins_name_arr[$chl_data['primaryInsuranceCoId']];
						$claim_ctrl_no=$chl_data['claim_ctrl_pri'];
					}else if($_REQUEST['InsComp']==2){
						$ins_comp=$ins_name_arr[$chl_data['secondaryInsuranceCoId']];
						$claim_ctrl_no=$chl_data['claim_ctrl_sec'];
					}else if($_REQUEST['InsComp']==3){
						$ins_comp=$ins_name_arr[$chl_data['tertiaryInsuranceCoId']];
						$claim_ctrl_no=$chl_data['claim_ctrl_ter'];
					}
					$sel_bill_831="selected";
					$sel_bill_837="";
					if($claim_ctrl_no!=""){
						$sel_bill_831="";
						$sel_bill_837="selected";
					}
					$cpt_codes=implode(', ',$chl_cpt_arr[$chl_key]);
					$dx_codes=implode(', ',$chl_dx_arr[$chl_key]);
					$mod_codes=implode(', ',$chl_mod_arr[$chl_key]);
					$tot_unit=array_sum($chl_unit_arr[$chl_key]);
					$tot_charges=array_sum($chl_charges_arr[$chl_key]);
					$set_disabled="";
					if($claim_key==1){
						$set_disabled="disabled";
						$invalid_calim_cont=$invalid_calim_cont+1;
					}else{
						//$set_disabled="checked";
					}
			?>
				<tr>
					<td class="text-center">
						<div class="checkbox">
							<input type="checkbox" name="chl_chk_box[]" id="chl_chk_box_<?php echo $chl_key;?>" class="chk_box_css" value="<?php echo $chl_key;?>" <?php echo $set_disabled; ?>/>
							<label for="chl_chk_box_<?php echo $chl_key;?>"></label>
						</div>
					</td>
					<td>
						<select name="bill_type[<?php echo $chl_key;?>]" class="selectpicker" data-width="100%">
							<option value="831" <?php echo $sel_bill_831; ?>>831</option>
							<option value="837" <?php echo $sel_bill_837; ?>>837</option>
						</select>
					</td>
					<td class="text-nowrap"><?php echo $pat_name_arr[$chl_data['patient_id']].' - '.$chl_data['patient_id'];?></td>
					<td><?php echo $chl_data['encounter_id'];?></td>
					<td class="text-nowrap"><?php echo get_date_format($chl_data['date_of_service']);?></td>
					<td><?php echo $ins_comp;?></td>
					<td><?php echo $cpt_codes;?></td>
					<td><?php echo $dx_codes;?></td>
					<td class="text-right"><?php echo $tot_unit;?></td>
					<td class="text-right"><?php echo numberformat($tot_charges,2);?></td>
					<td><?php echo $mod_codes;?></td>
					<td>
						<input type="text" name="claim_ctrl[<?php echo $chl_key;?>]" id="claim_ctrl_<?php echo $chl_key;?>" value="<?php echo $claim_ctrl_no;?>" class="form-control" size="12" <?php echo $set_disabled; ?>>
					</td>				
				</tr>
				<?php if(count($error[$chl_key])>0){ ?>
				<tr>
					<td colspan="12" class="infobar">
						<?php echo '&bull; '.implode('<br>&bull; ',$error[$chl_key]);?>
					</td>
				</tr>
			<?php 	
					} 
				} 
			}
		}
	?>
	
	<?php if(count($claim_arr)==0){?>
		<tr>
			<td colspan="12" class="text-center lead"><?php echo imw_msg('no_rec'); ?></td>
		</tr>
	<?php }else{?>
		<script type="text/javascript">
				$('#span_validclaims').html('<span>VALID CLAIMS</span>'+<?php echo count($valid_claims_arr); ?>);
				$('#span_validamount').html('<span>VALID AMOUNT</span>$'+<?php echo str_replace(',','',number_format(array_sum($valid_claims_arr),2)); ?>);
				$('#span_invalidclaims').html('<span>INVALID CLAIMS</span>'+<?php echo count($invalid_claims_arr); ?>);
				$('#span_invalidamount').html('<span>INVALID AMOUNT</span>$'+<?php echo str_replace(',','',number_format(array_sum($invalid_claims_arr),2)); ?>);
		</script>
	<?php } ?>
</table>
</form>