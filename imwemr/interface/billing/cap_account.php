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
$without_pat="yes";  
require_once("../accounting/acc_header.php");
require_once(dirname(__FILE__)."/../../library/classes/cls_common_function.php");
$OBJCommonFunction = new CLSCommonFunction;
$pg_title = 'Capitation';

if($_REQUEST['date_frm']==""){
	$_REQUEST['date_frm'] = date('m-d-Y');
}
if($_REQUEST['date_to']==""){
	$_REQUEST['date_to'] = date('m-d-Y');
}
$srh_pro_id_imp=implode(',',$_REQUEST['pro_id']);
$physicianData = $OBJCommonFunction->drop_down_providers($srh_pro_id_imp,'','1');

if($_REQUEST['type_submit']=="submit"){
	$curr_date=date('Y-m-d');
	if($_REQUEST['chl_chk']!=""){
		$chl_chk_imp=implode(',',$_REQUEST['chl_chk']);
		$chld_qry=imw_query("select charge_list_detail_id,newBalance,patient_id,charge_list_id from patient_charge_list_details where charge_list_id in($chl_chk_imp) and newBalance>0 and del_status='0' order by charge_list_id,newBalance desc");
		while($chld_row=imw_fetch_array($chld_qry)){
			$charge_list_detail_id=$chld_row['charge_list_detail_id'];
			$patient_id=$chld_row['patient_id'];
			$charge_list_id=$chld_row['charge_list_id'];
			$encounter_id=$_REQUEST['encounter_id_'.$charge_list_id];
			if($encounter_id>0){
				$pend_copay_amt=0;
				if($charge_list_id!=$old_charge_list_id){
					$pend_copay_amt=$_REQUEST['pend_copay_amt_'.$charge_list_id];
				}
				
				$writeOffAmt=$chld_row['newBalance']-$pend_copay_amt;
				if($writeOffAmt>0){
					$ins_id=$_REQUEST['ins_id_'.$charge_list_id];
					$phy_id=$_REQUEST['phy_id_'.$charge_list_id];
					$dos=$_REQUEST['dos_'.$charge_list_id];
					$doc=$_REQUEST['doc_'.$charge_list_id];
					$totalAmt=$_REQUEST['totalAmt_'.$charge_list_id];
					$totalBalance=$_REQUEST['totalBalance_'.$charge_list_id];
					$copay_amt=$_REQUEST['copay_amt_'.$charge_list_id];
					$pend_copay_amt=$_REQUEST['pend_copay_amt_'.$charge_list_id];
					$ins_plan_name=$_REQUEST['ins_plan_name_'.$charge_list_id];
					
					if($cap_main_id==""){
						$main_cap_qry=imw_query("INSERT INTO cap_batch_main SET entered_by = '$operator_id',entered_date='$curr_date_time'");
						$cap_main_id=imw_insert_id();	
					}
									
					$insertWriteOffStr = "INSERT INTO paymentswriteoff SET
										patient_id = '$patient_id',
										encounter_id = '$encounter_id',
										charge_list_detail_id = '$charge_list_detail_id',
										write_off_by_id='$ins_id',
										write_off_amount = '$writeOffAmt',
										write_off_operator_id = '$operator_id',
										write_off_date = '$curr_date',
										paymentStatus = 'Write Off',
										write_off_code_id='$write_off_code',
										CAS_type = 'Cap',
										entered_date='$curr_date_time',
										cap_main_id='$cap_main_id'";
					$insertWriteOffQry = imw_query($insertWriteOffStr);
					
					if($charge_list_id!=$old_charge_list_id){
						
						$insertWriteOffStr2 = "INSERT INTO cap_batch SET
											patient_id = '$patient_id',
											encounter_id = '$encounter_id',
											charge_list_id = '$charge_list_id',
											charge_list_detail_id = '$charge_list_detail_id',
											ins_id = '$ins_id',
											phy_id = '$phy_id',
											dos = '$dos',
											doc = '$doc',
											charges = '$totalAmt',
											balance = '$totalBalance',
											copay = '$copay_amt',
											pending_copay = '$pend_copay_amt',
											write_off_amount='$writeOffAmt',
											chld_write_off_amount='$writeOffAmt',
											write_off_code_id='$write_off_code',
											CAS_type = 'Cap',
											ins_plan_name='$ins_plan_name',
											entered_by = '$operator_id',
											entered_date='$curr_date_time',
											cap_main_id='$cap_main_id'";
						$insertWriteOffQry2 = imw_query($insertWriteOffStr2);
						$cap_ins_id=imw_insert_id();
					}else{
						if($cap_ins_id>0){
							imw_query("update cap_batch set write_off_amount=write_off_amount+$writeOffAmt,
							charge_list_detail_id=concat(charge_list_detail_id,',',$charge_list_detail_id),
							chld_write_off_amount=concat(chld_write_off_amount,',',$writeOffAmt) where id='$cap_ins_id'");
						}
					}
					set_payment_trans($encounter_id);
					patient_proc_bal_update($encounter_id);
				}
				$old_charge_list_id=$charge_list_id;
			}
		}
	}
}


// ---- GET INSURANCE GROUP DROP DOWN ----
$qry = imw_query("SELECT id, title FROM  ins_comp_groups WHERE delete_status = '0'");
$ins_group_arr = array();
while($row=imw_fetch_array($qry)){
	$ins_grp_id = $row['id'];
	$ins_grp_name = $row['title'];
	$ins_qry = imw_query("SELECT id FROM insurance_companies WHERE groupedIn = '".$ins_grp_id."'");
	$tmp_grp_ins_arr = array();
	if(imw_num_rows($ins_qry)>0){
		while($ins_row=imw_fetch_array($ins_qry)){
			$tmp_grp_ins_arr[] = $ins_row['id'];
		}
		$grp_ins_ids = implode(",", $tmp_grp_ins_arr);
		$ins_group_arr[$grp_ins_ids] = $ins_grp_name;
	}
}

// ---- GET GROUPS DROP DOWN ----
$fet_groups=imw_query("select name,gro_id,del_status from groups_new order by name asc");
while($row_groups=imw_fetch_array($fet_groups)){
	$gro_data[$row_groups['gro_id']]=$row_groups;
}
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('.date-pick').datetimepicker({
			timepicker:false,
			format:top.global_date_format, //'m-d-Y'
		});	
		$('#ins_grp').selectpicker();
		$('#pro_id').selectpicker();
		$('#gro_id').selectpicker();
	});
	
	function submit_frm(){
		if($('.chk_box_css:checked').length==0){
			top.fAlert('Please select any record.');
		}else{
			top.fancyConfirm("Are you sure to Write-off selected records?","","window.top.fmain.setSubmitVal()");
		}
	}
	
	function setSubmitVal(){
		$('#type_submit').val("submit");
		top.show_loading_image("show","150");
		document.frm.submit();
	}
	
	function srh_fun(){
		$('.chk_box_css').removeAttr("checked");
		top.show_loading_image("show","150");
		document.frm.submit();
	}
	function old_batch_fun(){
		window.open("cap_hx.php",'capitation','width=1280,height=<?php echo $_SESSION["wn_height"]-220; ?>,top=10,left=10,location=1,scrollbars=no,resizable=1');
	}
	
</script>	
<div class="row">
	<form name="frm" id="cap_form" action="cap_account.php" method="post">
		<input type="hidden" name="type_srh" id="type_srh" value="search">
		<input type="hidden" name="type_submit" id="type_submit" value="">	
		<div class="col-sm-12 purple_bar">
			<div class="col-sm-2 form-inline">
				<label>Physician:</label>
				<select name="pro_id[]" id="pro_id" class="selectpicker show-menu-arrow" multiple="multiple"  data-actions-box="true" data-title="<?php echo imw_msg('drop_sel'); ?>">
					<?php echo $physicianData;?>
				</select>
			</div>	
			
			<div class="col-sm-3 form-inline">
				<label>Insurance Group:</label>
				<select name="ins_grp[]" id="ins_grp" class="selectpicker show-menu-arrow" multiple="multiple" data-width="60%" data-actions-box="true" data-title="<?php echo imw_msg('drop_sel'); ?>"> 
					<?php foreach($ins_group_arr as $key => $val){
						$select="";
						if(in_array($key ,$ins_grp)){
							$select="selected='selected'";
						}	
					?>
						<option value="<?php echo $key;?>" <?php echo $select; ?>> <?php echo $val;?></option>
					<?php } ?>
				</select>
			</div>
            
            <div class="col-sm-2 form-inline">
				<label>Groups:</label>
				<select name="gro_id[]" id="gro_id" class="selectpicker show-menu-arrow" multiple="multiple"  data-actions-box="true" data-title="<?php echo imw_msg('drop_sel'); ?>">
					<?php foreach($gro_data as $key => $val){
						$select="";
						if(in_array($key ,$gro_id)){
							$select="selected='selected'";
						}
						$red_color="";	
						if($gro_data[$key]['del_status']>0){
							$red_color="color:#CC0000";
						}
					?>
						<option value="<?php echo $key;?>" <?php echo $select; ?> style=" <?php echo $red_color; ?>"> <?php echo $gro_data[$key]['name'];?></option>
					<?php } ?>
				</select>
			</div>
            <div class="col-sm-3">
                <div class="row">
                    <div class="col-sm-6 form-inline">
                    	<label>DOS From:</label>
						<div class="input-group">
                            <input id="date1" type="text" name="date_frm" size="10" maxlength=10 class="date-pick form-control" value="<?php echo date(phpDateFormat(), strtotime(str_replace('-','/',$_REQUEST['date_frm'])));?>">
                            <div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></div>
                        </div>
                    </div>
                    <div class="col-sm-6 form-inline">
                        <label>To:</label>
                        <div class="input-group">
                            <input id="date2" type="text" name="date_to" size='10' maxlength=10 class="date-pick form-control" value="<?php echo date(phpDateFormat(), strtotime(str_replace('-','/',$_REQUEST['date_to'])));?>">
                            <div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></div>
                        </div>
                    </div>
                </div>	
            </div>
			<div class="col-sm-2 form-group text-right">
				<input type="button" name="srh" id="srh" value="Search" class="btn btn-success" onClick="srh_fun();">
				<input type="button" name="old_batch" id="old_batch" value="Cap Hx" class="btn btn-primary" onClick="old_batch_fun();">	
			</div>		
		</div>
		<div class="table-responsive" style="width:100%;height:<?php print $_SESSION['wn_height']-410;?>px;overflow:auto;">
			<?php
				$limit_chk=800;
				if($_REQUEST['type_srh']!=""){
					$dat_frm_final=getDateFormatDB($_REQUEST['date_frm']);
					$dat_to_final=getDateFormatDB($_REQUEST['date_to']);

					$dat_whr="and (pcl.date_of_service  between '$dat_frm_final' and  '$dat_to_final')";
					
					if($ins_grp!=""){
						$ins_grp_imp=implode(',',$ins_grp);
						$ins_whr="and pcl.primaryInsuranceCoId in($ins_grp_imp)";
					}
					if($pro_id!=""){
						$pro_id_imp=implode(',',$pro_id);
						$prov_whr="and pcl.primaryProviderId in($pro_id_imp)";
					}
					if($gro_id!=""){
						$gro_id_imp=implode(',',$gro_id);
						$gro_whr="and pcl.gro_id in($gro_id_imp)";
					}
					$chl_detail_arr=array();
					$strQry = "select pcl.charge_list_id,pcl.copay,pcl.encounter_id,pcl.primaryInsuranceCoId,
								pcl.date_of_service,pcl.patient_id,pcl.totalBalance,pcl.primaryProviderId,
								pcl.totalAmt,pcl.encounter_id,pcl.first_posted_date,pcl.case_type_id
								from patient_charge_list pcl WHERE pcl.totalBalance  > 0 and pcl.del_status='0'
								and pcl.primaryInsuranceCoId>0 $dat_whr $ins_whr $prov_whr $gro_whr  order by pcl.encounter_id";
						$den_qry=imw_query($strQry);
					while($den_fet3=imw_fetch_array($den_qry)){
						$chl_detail_arr[$den_fet3['patient_id']][]=$den_fet3;
						$chl_tot_rec[]=$den_fet3['patient_id'];
						if($den_fet3['copay']>0){
							$chl_tot_enc[$den_fet3['encounter_id']]=$den_fet3['encounter_id'];
						}
						if($den_fet3['case_type_id']>0){
							$chl_case_enc[$den_fet3['case_type_id']]=$den_fet3['case_type_id'];
						}
					}
					$sno=0;
				}
			?>
			 <table class="table table-bordered table-hover table-striped">
				<tr class='grythead'>
					<th>
						<div class="checkbox">
							<input type="checkbox" name="chkbx_all" id="chkbx_all" onClick="return chk_all();" checked>
							<label for="chkbx_all"></label>	
						</div>
					</th>
					<th>Patient Name - ID</th>
					<th>Encounter Id</th>
					<th>Ins. Plan Name</th>
					<th>DOS</th>
					<th>DOC</th>
					<th>Charges</th>
					<th>Balance</th>
					<th>Copay</th>
					<th>Pending Copay</th>
				</tr>
				<?php
				$chl_tot_rec_imp=implode(',',$chl_tot_rec);
				$chl_tot_enc_imp=implode(',',$chl_tot_enc);
				$tot_cont=0;
				
				if(count($chl_tot_rec)>0){
					
					$getproccode = "SELECT paidForProc,encounter_id FROM 
					patient_chargesheet_payment_info a,
					patient_charges_detail_payment_info b
					WHERE a.encounter_id in($chl_tot_enc_imp)
					AND a.payment_id = b.payment_id
					AND b.charge_list_detail_id = 0
					AND b.deletePayment=0
					ORDER BY a.payment_id DESC";
					$getproccodeQry = imw_query($getproccode);
					while($getproccodeRow = imw_fetch_array($getproccodeQry)){
						$total_copay_paid_arr[$getproccodeRow['encounter_id']][] = $getproccodeRow['paidForProc'];
					}
					
					$q = "SELECT create_date,encounter_id FROM batch_file_submitte order by create_date desc";
					$res = imw_query($q);
					if($res && imw_num_rows($res)>0){
						while($rs = imw_fetch_assoc($res)){
							$doc_encounter_id=explode(',',$rs['encounter_id']);
							$doc_date_arr[]=$rs['create_date'];
							$doc_enc_arr[]=$doc_encounter_id;
						}
					}
					
					$chl_case_enc_imp=implode(',',$chl_case_enc);
					$ins_plan_qry=imw_query("SELECT plan_name,ins_caseid,provider FROM insurance_data WHERE ins_caseid in($chl_case_enc_imp) AND type='primary' and provider > 0 and plan_name!=''");
					if($ins_plan_qry && imw_num_rows($ins_plan_qry)>0){
						while($ins_plan_row = imw_fetch_assoc($ins_plan_qry)){
							if($ins_plan_row['plan_name']!=""){
								$chl_ins_plan_arr[$ins_plan_row['ins_caseid']][$ins_plan_row['provider']]=$ins_plan_row['plan_name'];
							}
						}
					}
					
					$strQry2 = "select pd.id,pd.fname,pd.mname,pd.lname from patient_data pd WHERE pd.id in($chl_tot_rec_imp) order by pd.lname asc,pd.fname asc";
					$den_qry2=imw_query($strQry2);
					$counter = 0;
					while($den_fet2=imw_fetch_array($den_qry2)){
						$pat_id=$den_fet2['id'];
						if(count($chl_tot_rec)>0 && count($chl_tot_rec)<$limit_chk){
							for($k=0;$k<=count($chl_detail_arr[$pat_id]);$k++){
								$den_fet=$chl_detail_arr[$pat_id][$k];
								$sno++;
								$encounter_id=$den_fet['encounter_id'];
								$total_copay_paid="";
								$total_copay_paid=array_sum($total_copay_paid_arr[$encounter_id]);
								$pending_copay=$den_fet['copay']-$total_copay_paid;
								$create_date="";
								if($den_fet['first_posted_date']!='0000-00-00'){
									for($h=0;$h<=count($doc_enc_arr);$h++){
										if(in_array($encounter_id,$doc_enc_arr[$h])){
											$create_date=$doc_date_arr[$h];
											break;
										}
									}
								}
								if($den_fet['totalBalance']>$pending_copay){
									$tot_cont++;
							?>						
									<tr>
										<td class="text-center">
											<div class="checkbox">
												<input type="checkbox" name="chl_chk[]" id="chl_chk_<?php echo $counter; ?>" class="chk_box_css" value="<?php echo $den_fet['charge_list_id']; ?>" checked>	
												<label for='chl_chk_<?php echo $counter; ?>'></label>
											</div>
											<input type="hidden" name="encounter_id_<?php echo $den_fet['charge_list_id']; ?>" id="encounter_id_<?php echo $den_fet['charge_list_id']; ?>" value="<?php echo $den_fet['encounter_id'];?>">
											<input type="hidden" name="copay_amt_<?php echo $den_fet['charge_list_id']; ?>" id="copay_amt_<?php echo $den_fet['charge_list_id']; ?>" value="<?php echo $den_fet['copay'];?>">
											<input type="hidden" name="pend_copay_amt_<?php echo $den_fet['charge_list_id']; ?>" id="pend_copay_amt_<?php echo $den_fet['charge_list_id']; ?>" value="<?php echo $pending_copay;?>">
											<input type="hidden" name="dos_<?php echo $den_fet['charge_list_id']; ?>" id="dos_<?php echo $den_fet['charge_list_id']; ?>" value="<?php echo $den_fet['date_of_service'];?>">
											<input type="hidden" name="doc_<?php echo $den_fet['charge_list_id']; ?>" id="doc_<?php echo $den_fet['charge_list_id']; ?>" value="<?php echo $create_date;?>">
											<input type="hidden" name="totalAmt_<?php echo $den_fet['charge_list_id']; ?>" id="totalAmt_<?php echo $den_fet['charge_list_id']; ?>" value="<?php echo $den_fet['totalAmt'];?>">
											<input type="hidden" name="totalBalance_<?php echo $den_fet['charge_list_id']; ?>" id="totalBalance_<?php echo $den_fet['charge_list_id']; ?>" value="<?php echo $den_fet['totalBalance'];?>">
											<input type="hidden" name="ins_id_<?php echo $den_fet['charge_list_id']; ?>" id="ins_id_chk_<?php echo $den_fet['charge_list_id']; ?>" value="<?php echo $den_fet['primaryInsuranceCoId'];?>">
											<input type="hidden" name="phy_id_<?php echo $den_fet['charge_list_id']; ?>" id="phy_id_chk_<?php echo $den_fet['charge_list_id']; ?>" value="<?php echo $den_fet['primaryProviderId'];?>">
											<input type="hidden" name="ins_plan_name_<?php echo $den_fet['charge_list_id']; ?>" id="ins_plan_name_chk_<?php echo $den_fet['charge_list_id']; ?>" value="<?php echo $chl_ins_plan_arr[$den_fet['case_type_id']][$den_fet['primaryInsuranceCoId']];?>">
										</td>
										<td>
											<?php
											$patientName = ucwords(trim($den_fet2['lname'].", ".$den_fet2['fname']." ".$den_fet2['mname']));
											 echo $patientName .' - '.$den_fet['patient_id']; 
											 $tot_pat_arr[$den_fet['patient_id']]=$patientName;
											?>
										</td>
										<td>
											<?php
												echo $den_fet['encounter_id'];
											?>
										</td>
										<td>
											<?php
												echo $chl_ins_plan_arr[$den_fet['case_type_id']][$den_fet['primaryInsuranceCoId']];
											?>
										</td>
										<td class="text-center">
											<?php
												$dat_exp_show_dos = explode("-", $den_fet['date_of_service']);
												$shw_dat_dos = date('m-d-Y',mktime(0,0,0,$dat_exp_show_dos[1],$dat_exp_show_dos[2],$dat_exp_show_dos[0]));
												echo $shw_dat_dos;
											?>
										</td>
										<td class="text-center">
											<?php
												if($create_date!=""){
													$dat_exp_show_doc = explode("-", $create_date);
													$shw_dat_doc = date('m-d-Y',mktime(0,0,0,$dat_exp_show_doc[1],$dat_exp_show_doc[2],$dat_exp_show_doc[0]));
													echo $shw_dat_doc;
												}
											?>
										</td>
										<td  class="text-right col-sm-1">
											<?php echo numberFormat($den_fet['totalAmt'],2,'yes'); $tot_amt_arr[]=$den_fet['totalAmt']; ?>
										</td>
										<td  class="text-right col-sm-1">
											<?php echo numberFormat($den_fet['totalBalance'],2,'yes'); $tot_bal_arr[]=$den_fet['totalBalance']; ?>
										</td>
										<td  class="text-right col-sm-1">
											<?php echo numberFormat($den_fet['copay'],2,'yes'); $tot_copay_arr[]=$den_fet['copay']; ?>
										</td> 
										<td  class="text-right col-sm-1">
											<?php echo numberFormat($pending_copay,2,'yes'); $tot_pend_copay_arr[]=$pending_copay; ?>
										</td> 
									</tr>
							<?php
								}
								$counter++;
							}
						}
						
					}
				}
				if($tot_cont==0 && count($chl_tot_rec)<$limit_chk){
				?>
					<tr><td colspan="10" class="text-center lead"><?php echo imw_msg('no_rec'); ?></td></tr>
				<?php }else{
					if(count($chl_tot_rec)>=$limit_chk){
				?>
					<tr><td colspan="9" style="color:#F00;" class="text-center">
						<b>Please modify search criteria as records exceeded the <?php echo $limit_chk; ?> encounters limit.</b>
					</td></tr>
				<?php }else{?>
					<tr class="purple_bar" style="font-weight:bold;">
						<td class="text-right" colspan="2">
							Total Patient : <?php echo count($tot_pat_arr); ?>
						</td>
						<td class="text-left">&nbsp;</td>
						<td class="text-center" >&nbsp;</td>
						<td class="text-center" >&nbsp;</td>
						<td class="text-center">&nbsp;</td>
						<td class="text-right">
							<?php echo numberFormat(array_sum($tot_amt_arr),2,'yes'); ?>
						</td>
						<td class="text-right">
							<?php echo numberFormat(array_sum($tot_bal_arr),2,'yes'); ?>
						</td>
						<td class="text-right">
							<?php echo numberFormat(array_sum($tot_copay_arr),2,'yes'); ?>
						</td>
						<td class="text-right">
							<?php echo numberFormat(array_sum($tot_pend_copay_arr),2,'yes'); ?>
						</td> 
					</tr>
				 <?php
				}
			}
				?>
			</table>
		</div>
		<div>
			<?php if(count($chl_tot_rec)>0){?>
			<label>Write Off Code : </label>
			<select name="write_off_code"  id="write_off_code" class="selectpicker">
				<option value="">Write off Code</option>
				<?php
				$sel_rec=imw_query("select w_id,w_code,w_default from write_off_code");
				while($sel_write=imw_fetch_array($sel_rec)){
				?>
					<option value="<?php echo $sel_write['w_id'];?>" <?php if($sel_write['w_default']=='yes'){ echo "selected";} ?>><?php echo $sel_write['w_code'];?></option>
				<?php } ?>
			</select>
		<?php } ?>	
	  </div>
	</form>
</div>
<script type="text/javascript">
	var ar = [["done_data","Done","top.fmain.submit_frm();"]];
	top.btn_show("CLRJ",ar);
	top.$('#acc_page_name').html('<?php echo $pg_title; ?>');
</script>	
</div>
</body>
</html>