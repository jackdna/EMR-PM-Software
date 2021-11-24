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
include_once('../../config/globals.php');
include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');
include_once($GLOBALS['fileroot'].'/library/classes/acc_functions.php');
include_once($GLOBALS['fileroot'].'/library/classes/work_view/Dx.php');
set_time_limit(0);
//require_once(dirname(__FILE__)."/../main/Functions.php");
//require_once(dirname(__FILE__)."/../main/main_functions.php");
//$objManageData = new ManageData;
$sql = "select * from modifiers_tbl WHERE delete_status = '0' order by mod_prac_code ASC";
$rez = get_data_array($sql);	
foreach($rez as $obj){
	$code=$obj["mod_prac_code"];
	$mod_description=$obj["mod_description"];
	$mod_arr_desc_code[$obj["mod_prac_code"]]=$obj["mod_description"];
}

$sql = "SELECT * FROM diagnosis_code_tbl order by dx_code,diag_description";
$rezCodes = get_data_array($sql);
foreach($rezCodes as $obj){
	$arrDxCodeDesc[$obj["dx_code"]]=$obj["diag_description"];
}
$oDx = new Dx();

$sql = "select * from cpt_fee_tbl where status='active' AND delete_status = '0' order by cpt_prac_code ASC";
$rezCodesaa = get_data_array($sql);
foreach($rezCodesaa as $obj){
	$cpt_desc_arr[$obj["cpt_prac_code"]] = $obj["cpt_desc"];
}

$sql = "SELECT * FROM superbill
		LEFT JOIN procedureinfo ON procedureinfo.idSuperBill = superbill.idSuperBill 
		WHERE superbill.patientId='$patient_id' and superbill.dateOfService='$sa_app_start_date' 
		and superbill.del_status='0'
		and procedureinfo.delete_status='0'
		and superbill.merged_with='0'
		ORDER BY superbill.idSuperBill,procedureinfo.porder ";
$rowSuperBill = imw_query($sql);
$rowSuperBill2 = imw_query($sql);
?>
<div class="col-sm-12 headinghd">	
		<h4>Visit Details</h4>	
</div>
<div class="col-sm-12">
	<?php
	if(imw_num_rows($rowSuperBill)==0){
		echo "<p class='lead'>No Super Bill</p>";
	}else{
	?>
	<div class="row">
		<div style="max-height:200px; overflow:auto; overflow-x:hidden;">
			<table class="table table-condensed table-bordered">
				<tr class="grythead">
					<th >#</th>
					<th >CPT - Description</th>
					<th >Dx Code - Description</th>
					<th >MOD</th>
					<th >Unit</th>
					<th >Unit Charge</th>
					<th >Total Charges</th>
					<th >T. Allowable Charges</th>
				</tr>
					<?php	
						$i=0;
						$qry_pol=imw_query("select billing_amount from copay_policies");
						$row_pol=imw_fetch_array($qry_pol);
						$billing_amount=$row_pol['billing_amount'];
						$all_dx_codes_arr=array();
						while($getProceduresRow2=imw_fetch_array($rowSuperBill)){
							$all_sup_dx_codes_arr[]=unserialize(html_entity_decode($getProceduresRow2['arr_dx_codes']));
						}
						for($hk=0;$hk<=count($all_sup_dx_codes_arr);$hk++){
							$all_dx_codes_imp.=implode(',',$all_sup_dx_codes_arr[$hk]);
						}
						$all_dx_codes_arr=explode(',',$all_dx_codes_imp);
						if(count($all_dx_codes_arr)>0){
							$dx_code_title_arr=get_icd10_desc($all_dx_codes_arr,0);
						}
						while($getProceduresRow=imw_fetch_array($rowSuperBill2)){
						$i++;	
							$modifier_arr=array();
							$diagCode_arr=array();
							$procedurePracCode=$getProceduresRow['cptCode'];
							$cptUnits = $getProceduresRow['units'];
							$insuranceCaseId = $getProceduresRow['insuranceCaseId'];
							$encounterId=$getProceduresRow['encounterId'];
							$pri_ins_id=$getProceduresRow['pri_ins_id'];
							
							$getPhysicianStr = "SELECT * FROM chart_master_table 
									WHERE patient_id='$patient_id'
									AND encounterId='$encounterId'";
							$getPhysicianQry = imw_query($getPhysicianStr);
							$getPhysicianRow = imw_fetch_array($getPhysicianQry);
							
							if($insuranceCaseId=='0'){
								$FeeTable = 1;
							}else{
								if($pri_ins_id>0){
									$pInsId=$pri_ins_id;
								}else{
									$getPrimaryInsCoStr = "SELECT provider FROM insurance_data										
															WHERE pid = '$patient_id'
															AND type = 'primary'
															AND ins_caseid = '$insuranceCaseId'";
									$getPrimaryInsCoQry = imw_query($getPrimaryInsCoStr);
									$getPrimaryInsCoRow = imw_fetch_assoc($getPrimaryInsCoQry);
									$pInsId = $getPrimaryInsCoRow['provider'];			
								}
								$qryId = imw_query("select FeeTable from insurance_companies where id = '$pInsId'");
								list($FeeTable) = imw_fetch_array($qryId);
							}
							if($billing_amount=='Default'){
								$getCPTPriceQry = imw_query("SELECT cpt_fee FROM cpt_fee_table a,
																	cpt_fee_tbl b
																	WHERE 
																	(b.cpt_prac_code='$procedurePracCode')
																	AND a.cpt_fee_id = b.cpt_fee_id
																	AND a.fee_table_column_id = '1'
																	AND b.delete_status = '0'");
								$getCPTPriceRow = imw_fetch_assoc($getCPTPriceQry);
								$fee=$getCPTPriceRow['cpt_fee'];
								if(imw_num_rows($getCPTPriceQry)==0){
									$getCPTPriceQry = imw_query("SELECT cpt_fee FROM cpt_fee_table a,
																	cpt_fee_tbl b
																	WHERE 
																	(b.cpt4_code='$procedurePracCode' 
																	OR b.cpt_desc='$procedurePracCode')
																	AND a.cpt_fee_id = b.cpt_fee_id
																	AND a.fee_table_column_id = '1'
																	AND b.delete_status = '0'");
									$getCPTPriceRow = imw_fetch_assoc($getCPTPriceQry);
									$fee=$getCPTPriceRow['cpt_fee'];
								}
							}else{	
								if($FeeTable<=0){
									$FeeTable=1;
								}
								$qry = "select cpt_fee_tbl.cpt_prac_code,
										cpt_fee_table.cpt_fee from cpt_fee_tbl
										join cpt_fee_table on cpt_fee_table.fee_table_column_id = '$FeeTable'
										where (cpt_fee_tbl.cpt_prac_code='$procedurePracCode')
										and cpt_fee_table.cpt_fee_id = cpt_fee_tbl.cpt_fee_id AND cpt_fee_tbl.delete_status = '0'";
								$get=imw_query($qry);
								$get_row=imw_fetch_array($get);
								$fee=$get_row['cpt_fee'];
								if(imw_num_rows($get)==0){
									$qry = "select cpt_fee_tbl.cpt_prac_code,
										cpt_fee_table.cpt_fee from cpt_fee_tbl
										join cpt_fee_table on cpt_fee_table.fee_table_column_id = '$FeeTable'
										where (cpt_fee_tbl.cpt4_code='$procedurePracCode' OR cpt_fee_tbl.cpt_desc='$procedurePracCode')
										and cpt_fee_table.cpt_fee_id = cpt_fee_tbl.cpt_fee_id AND cpt_fee_tbl.delete_status = '0'";
									$get=imw_query($qry);
									$get_row=imw_fetch_array($get);
									$fee=$get_row['cpt_fee'];
								}
							}
							$cpt_per_unit_chr=$fee;
							$totalUnitsCharges=$cptUnits*$fee;
					
							if($getPhysicianRow['enc_icd10']=="1"){
								for($j=1;$j<=12;$j++){
									if($getProceduresRow['dx'.$j]){
										$diagCode_arr[]=$getProceduresRow['dx'.$j].' - '.$dx_code_title_arr[$getProceduresRow['dx'.$j]];
									}
								}
								/*if($getProceduresRow['dx1']){
									$diagCode_arr[]=$getProceduresRow['dx1'].' - '.$dx_code_title_arr[$getProceduresRow['dx1']];
								}
								if($getProceduresRow['dx2']){
									$diagCode_arr[]=$getProceduresRow['dx2'].' - '.$dx_code_title_arr[$getProceduresRow['dx2']];
								}
								if($getProceduresRow['dx3']){
									$diagCode_arr[]=$getProceduresRow['dx3'].' - '.$dx_code_title_arr[$getProceduresRow['dx3']];
								}
								if($getProceduresRow['dx4']){
									$diagCode_arr[]=$getProceduresRow['dx4'].' - '.$dx_code_title_arr[$getProceduresRow['dx4']];
								}*/
							}else{
								if($getProceduresRow['dx1']){
									$tdx = $oDx->get_dx_desc($getProceduresRow['dx1']);
									$diagCode_arr[]=$getProceduresRow['dx1'].' - '.$tdx;
								}
								if($getProceduresRow['dx2']){
									$tdx = $oDx->get_dx_desc($getProceduresRow['dx2']);
									$diagCode_arr[]=$getProceduresRow['dx2'].' - '.$tdx;
								}
								if($getProceduresRow['dx3']){
									$tdx = $oDx->get_dx_desc($getProceduresRow['dx3']);
									$diagCode_arr[]=$getProceduresRow['dx3'].' - '.$tdx;
								}
								if($getProceduresRow['dx4']){
									$tdx = $oDx->get_dx_desc($getProceduresRow['dx4']);
									$diagCode_arr[]=$getProceduresRow['dx4'].' - '.$tdx;
								}
							}
							
							$diagCode_imp=implode('<br>',$diagCode_arr);
							
							if($getProceduresRow['modifier1']){
							//	$modifier_arr[]=$getProceduresRow['modifier1'].' - '.$mod_arr_desc_code[$getProceduresRow['modifier1']];
								$modifier_arr[]=$getProceduresRow['modifier1'];
							
							}
							if($getProceduresRow['modifier2']){
							//	$modifier_arr[]=$getProceduresRow['modifier2'].' - '.$mod_arr_desc_code[$getProceduresRow['modifier2']];
								$modifier_arr[]=$getProceduresRow['modifier2'];
							}
							if($getProceduresRow['modifier3']){
							//	$modifier_arr[]=$getProceduresRow['modifier3'].' - '.$mod_arr_desc_code[$getProceduresRow['modifier3']];
								$modifier_arr[]=$getProceduresRow['modifier3'];
							}
							$modifier_imp=implode('<br>',$modifier_arr);
							$total_all_chrg[]=$totalUnitsCharges;
							
							// Calculate T. Allowable Charges	
							$contract_fee=getContractFee($procedurePracCode,$pInsId);
							$contract_fee_final=$contract_fee*$cptUnits;
							
							$total_sup_unit_arr[]=$cptUnits;
							$total_sup_unit_chrg_arr[]=$cpt_per_unit_chr;
							$total_sup_chrg_arr[]=$totalUnitsCharges;
							$total_sup_allow_arr[]=$contract_fee_final;
						?>
						<tr>
							<td>
								<small><?php echo $i; ?></small>
							</td>
							<td>
								<small><?php echo $procedurePracCode." - ".$cpt_desc_arr[$procedurePracCode]; ?></small>
							</td>
							<td>
								<small><?php echo $diagCode_imp; ?></small>
							</td>
							<td>
								<small><?php echo $modifier_imp; ?></small>
							</td>
							<td class="text-right">
								<small><?php echo $cptUnits; ?>&nbsp;</small>
							</td>
							<td class="text-right">
								<small><?php echo numberFormat($cpt_per_unit_chr,2,'yes'); ?>&nbsp;</small>
							</td>
							<td class="text-right">
								<small><?php echo numberFormat($totalUnitsCharges,2,'yes'); ?>&nbsp;</small>
							</td>
							<td class="text-right">
								<small><?php echo numberFormat($contract_fee_final,2,'yes');?>&nbsp;</small>
							</td>
						</tr>
					<?php
						}
					?>	
						<tr>
							<td class="text-right" colspan="4" >
								<small>Total :&nbsp;</small>
							</td>
							<td class="text-right" >
								<small><?php echo array_sum($total_sup_unit_arr); ?>&nbsp;</small>
							</td>
							<td class="text-right" >
								<small><?php echo numberFormat(array_sum($total_sup_unit_chrg_arr),2,'yes'); ?>&nbsp;</small>
							</td>
							<td class="text-right" >
								<small><?php echo numberFormat(array_sum($total_sup_chrg_arr),2,'yes'); ?>&nbsp;</small>
							</td>
							<td class="text-right" >
								<small><?php echo numberFormat(array_sum($total_sup_allow_arr),2,'yes'); ?>&nbsp;</small>
							</td>
						</tr> 
			</table>
		</div>		
	</div>
	<?php
		}
	?>
</div>