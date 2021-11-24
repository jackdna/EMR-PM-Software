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
$title = "Patient Paper/Electronic";
require_once("../accounting/acc_header.php");
include_once(dirname(__FILE__)."/../../library/classes/class.electronic_billing.php");
if($printHcfa){
	//--- Print HCFA Form For Secondry Insurance Company --------
	if($InsComp == 2){	
		if(count($_REQUEST['chl_chk_box'])>0){
			$secInsId = getInsCom($chl_chk_box,'secondaryInsuranceCoId',$printHcfa);
			if($secInsId['charge_list_id'] != ""){
				$charge_check = ShowValidChargeList($secInsId,'secondary');
				$validChargeListId=array();
				foreach($charge_check['all_error'] as $vc_key => $vc_val){
					if(count($charge_check['all_error'][$vc_key])==2){
						$validChargeListId[] = $vc_key;
					}
				}
				$main_charge_list_id=join(',',$validChargeListId);
				$chld_ids_arr=array();
				$qryChld = imw_query("select charge_list_detail_id from patient_charge_list_details where del_status='0' and charge_list_id in ($main_charge_list_id)");
				while($groQryResChld = imw_fetch_array($qryChld)){
					$chld_ids_arr[]=$groQryResChld['charge_list_detail_id'];
				}
				$newFile="yes";
				if($_REQUEST['PrintCms_white']!="" || $_REQUEST['PrintCms']!=""){
					if($_REQUEST['PrintCms_white']){
						$print_paper_type="PrintCms_white";
					}else{
						$print_paper_type="PrintCms";
					}
					require_once("print_hcfa_form.php");
					$fpdiCheck=true;		
				}else if($_REQUEST['WithoutPrintub']!="" || $_REQUEST['Printub']!=""){
					if($_REQUEST['WithoutPrintub']){
						$print_paper_type="WithoutPrintub";
					}else{
						$print_paper_type="Printub";
					}
					require_once('print_ub.php');	
					$fpdiCheck=true;	
				}else{
					$createClaims="yes";
					$setField="secondarySubmit";		
					$InsComp='secondaryInsuranceCoId';	
							
					/*$qry = imw_query("select gro_id from patient_charge_list where del_status='0' and charge_list_id in ($main_charge_list_id) limit 0,1");
					$groQryRes = imw_fetch_array($qry);
					$gro_id = $groQryRes['gro_id'];
					
					//--- get groups details ---------
					$qry = imw_query("select * from groups_new where gro_id in ($gro_id)");
					$groupDetails = imw_fetch_object($qry);*/
					
					//---CREATING INSTANCE OF BILLING CLASS (ELECTRONIC)
					$objBilling		= new ElectronicBilling();
					
					$validChargeListId = $objBilling->regenerate_secondary_batch_from_era($main_charge_list_id,$InsComp);
					/*
					//--- Get group name ------
					$groupNameArr = preg_split('/ /',$groupDetails->name);
					$group_name_arr = array();
					for($i=0;$i<count($groupNameArr);$i++){
						$group_name_arr[] = $groupNameArr[$i][0];
					}
					$group_name_str = join('',$group_name_arr);
					
					$qry = imw_query("select * from copay_policies where policies_id='1'");
					$policiesDetails = imw_fetch_object($qry);
					//--- Data For Emdeon --------
					$submitterId = $policiesDetails->submitterId;
					$recieverId = $policiesDetails->ReceiverId;
					$BatchFile = $policiesDetails->Name;
					$fileNameStart = strtoupper($group_name_str.'_'.$policiesDetails->Name).'_';
					$fileNameStart = preg_replace('/ /','_',$fileNameStart);
					//---file Name For EMDEON ---
					
					require_once('emdeon_electronic_file_5010.php');
					*/
					$fpdiCheck=true;
					if(count($validChargeListId)>0){
						$validChargeListIdStr = implode(',',$validChargeListId);
					}
					$qry = imw_query("update patient_charge_list set $setField = 1,hcfaStatus = 1 where charge_list_id in ($validChargeListIdStr)");
				}	
				if($fpdiCheck == true){
					if($printHcfa==1){
						if($_REQUEST['WithoutPrintub']!="" || $_REQUEST['Printub']!=""){
							$msg = 'UB-04 file printed successfully.';
						}else{
							$msg = 'CMS-1500 form printed successfully.';
						}
					}else if($printHcfa==2){
						$msg = 'Electronic file created successfully.';
					}
				}
				else{
					$msg = 'invalid Claim.';
				}
			}
		}
	}
}
?>

<table class="table table-bordered table-hover table-striped">
	<form name="frm_sel" action="era_hcfa_electronic.php" method="post">
        <input type="hidden" name="newFile" id="newFile" value="<?=$newFile?>" >					
        <input type="hidden" name="printHcfa" id="printHcfa">
        <input type="hidden" name="InsComp" id="InsComp" value="2">
        <input type="hidden" name="ma18_enc_imp" id="ma18_enc_imp" value="<?php echo $_REQUEST['ma18_enc_imp']; ?>">
        <input type="hidden" name="process_file" id="process_file" value="<?php echo $_REQUEST['process_file']; ?>">
        <?php 
		$getResult = getPatientCharList_era($ma18_enc_imp,$process_file,'yes');
		$count = count($getResult);
		if($count){
			if($process_file=='hcfa'){
				$button_val="Print Claims";
				$file_gen=1;
			}else{
				$button_val ="Create Electronic";
				$file_gen=2;
			}
			if($_REQUEST['WithoutPrintub']!=""){
				$print_cms_sel['WithoutPrintub']="checked";
			}else if($_REQUEST['Printub']!=""){
				$print_cms_sel['Printub']="checked";
			}else if($_REQUEST['PrintCms_white']!=""){
				$print_cms_sel['PrintCms_white']="checked";
			}else{
				$print_cms_sel['PrintCms']="checked";
			}
			if($process_file=='hcfa'){
				$data .='<tr class="purple_bar">
					<td style="border:none;"><strong>ERA Post Payment</strong></td>
					<td colspan="4" style="border:none;" class="text-center"><strong>'.$msg.'</strong></td>
					<td colspan="4" style="border:none;" class="text-center">
						<div class="checkbox checkbox-inline">
							<input type="checkbox" name="PrintCms" id="PrintCms" value="PrintCms" onClick="return selectChkBox(this);" '.$print_cms_sel['PrintCms'].'>
							<label for="PrintCms">Print CMS-1500</label>
						</div>
						<div class="checkbox checkbox-inline">
							<input type="checkbox" name="PrintCms_white" id="PrintCms_white" value="PrintCms_white" onClick="return selectChkBox(this);" '.$print_cms_sel['PrintCms_white'].'>
							<label for="PrintCms_white">W/O CMS-1500</label>
						</div>
						<div class="checkbox checkbox-inline">
							<input type="checkbox" name="Printub" id="Printub" value="Printub" onClick="return selectChkBox(this);" '.$print_cms_sel['Printub'].'>
							<label for="Printub">Print UB-04</label>
						</div>
						<div class="checkbox checkbox-inline">
							<input type="checkbox" name="WithoutPrintub" id="WithoutPrintub" value="WithoutPrintub" onClick="return selectChkBox(this);" '.$print_cms_sel['WithoutPrintub'].'>
							<label for="WithoutPrintub">W/O UB-04</label>
						</div>
					
					</td>
				</tr>';
			}
			$data .='<tr class="grythead">
					<th>
						<div class="checkbox">
							<input type="checkbox" name="chkbx_all" id="chkbx_all" onClick="return chk_all();"/>
							<label for="chkbx_all"></label>
						</div>
					</th>
					<th>Patient Id</th>
					<th>Patient Name</th>
					<th>Encounter Id</th>
					<th>DOS</th>
					<th>Posted Date</th>
					<th>Resubmited Date</th>
					<th>Total Charges</th>
				</tr>
			';
			for($i = 0;$i < count($getResult);$i++){
				$pt_id=$getResult[$i]['patient_id'];
				$DOS = $getResult[$i]['date_of_service'];
				$encounter_id = $getResult[$i]['encounter_id'];
				$postedDate = $getResult[$i]['postedDate'];
				$charge_list_id = $getResult[$i]['charge_list_id'];
				$totalBalance = $getResult[$i]['postedAmount'];
				$patient_id = $getResult[$i]['patient_id'];
				$gro_id = $getResult[$i]['gro_id'];
				$group_color="";
				if($gro_id){
					$qry = imw_query("select group_color from groups_new where gro_id = '$gro_id'");
					$groupDetails = imw_fetch_array($qry);
					$group_color=$groupDetails['group_color'];
				}
				if($group_color==""){
					$group_color="#ffffff";
				}
				
				$total_Balance = numberFormat($totalBalance,2);
				
				$sub_qry = imw_query("select submited_date from submited_record where encounter_id in ($encounter_id) and patient_id in ($patient_id) order by submited_id desc ");
				$sub_res = imw_fetch_array($sub_qry);
				list($year,$month,$day) = explode('-',$sub_res['submited_date']);
				$submited_Date = $month.'-'.$day.'-'.substr($year,-2);
				
				$pat_qry = imw_query("select * from patient_data where id ='$patient_id'");
				$patientDetails = imw_fetch_object($pat_qry);
				$patientName = trim($patientDetails->lname).', ';
				$patientName .= trim($patientDetails->fname).' ';
				$patientName .= $patientDetails->mname;
				$data .='			
					<tr bgcolor="'.$group_color.'" class="text-center">
					   <td>	
							<div class="checkbox">
								<input type="checkbox" name="chl_chk_box[]" id="chl_chk_box'.($i).'" value="'.$charge_list_id.'" class="chk_box_css"/>
								<label for="chl_chk_box'.($i).'"></label>
							</div>
					   </td>
					   <td>'.$patient_id.'</td>
					   <td>'.$patientName.'</td>
					   <td>'.$encounter_id.'</td>
					   <td>'.$DOS.'</td>
					   <td>'.$postedDate.'</td>
					   <td>'.$submited_Date.'</td>
					   <td>'.$total_Balance.'</td>
				   </tr>
				';
			}
			$data .= '
				<div class="col-sm-12 text-center">
					
				</div>
				<tr class="text-center">
					<td colspan="9">
						<input type="button" class="btn btn-success" id="Process"  name="Process" value="'.$button_val.'"  onClick="HCFACheck('.$count.','.$file_gen.');"/>';	
					$data .= '</td>
				</tr>
			';
		}
		else{
			$data .= '
				<tr>
					<td colspan="9" class="text-center lead">'.imw_msg("no_rec").'</td>
				</tr>
			';
		}
		echo $data;
	 ?>
  </form>
</table>
<script type="text/javascript">
	
	function HCFACheck(count,frm){
		var flag = false;
		
		for(i = 0;i < count;i++){ 
			if(document.getElementById("chl_chk_box"+i).checked == true){
				flag = true;
			}
		} 
		if(flag == false){
			alert('Please select any record to print');
		}
		else{
			document.frm_sel.printHcfa.value = frm;
			document.frm_sel.submit();
		}
	}
	function selectChkBox(obj){
		var chkBoxArr = new Array("PrintCms","PrintCms_white","Printub","WithoutPrintub");
		var obj_id=obj.id;
		if($("#"+obj_id).is(':checked')){
			for(i in chkBoxArr){
				if(chkBoxArr[i] != obj.id){
					$("#"+chkBoxArr[i]).prop("checked",false);
				}
			}
		}
		else{
			$("#PrintCms").prop("checked",true);
		}
	}
</script>