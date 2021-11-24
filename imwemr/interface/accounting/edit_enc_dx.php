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
$title = "Edit Dx Codes";
include_once(dirname(__FILE__)."/acc_header.php");
include_once(dirname(__FILE__)."/../../library/classes/acc_functions.php");
include_once(dirname(__FILE__)."/../../library/classes/billing_functions.php");
//include_once("../main/Functions.php");
//include_once("../main/main_functions.php");
$operatorName = $_SESSION['authUser'];
$operatorId = $_SESSION['authUserID'];
$edit_id = preg_replace('/[^A-Za-z0-9 \_]+/','',$_REQUEST['edit_id']);
$edit_id = xss_rem($_REQUEST['edit_id'], 1);
$entered_date = date('Y-m-d H:i:s');
if(isset($_GET['dx_code_qry']) && $_GET['fetch_code'] == 'yes'){
	$dxCode = $_GET['dx_code_qry'];
	if($dxCode!=""){
	$getdxCode=imw_query("SELECT * FROM diagnosis_code_tbl WHERE d_prac_code='$dxCode' OR diag_description='$dxCode' OR dx_code='$dxCode'");
		$get_row=@imw_fetch_array($getdxCode);
		echo $dx_code=$get_row['dx_code'];
	}else{
		echo $dx_code="";
	}
	exit();
}


if($_REQUEST['frm_sub']!=""){
	$chl_diag_arr=array();
	for($g=1;$g<=12;$g++){
		if(strstr($_REQUEST['diagText_'.$g],"select")==true || strstr($_REQUEST['diagText_'.$g],"sleep")==true || strstr($_REQUEST['diagText_'.$g],"+")==true || strstr($_REQUEST['diagText_'.$g],"from")==true || strstr($_REQUEST['diagText_'.$g],"*")==true || strstr($_REQUEST['diagText_'.$g],")")==true || strstr($_REQUEST['diagText_'.$g],"(")==true){
			$_REQUEST['diagText_'.$g]='';
		}
		$chl_diag_arr[$g]=$_REQUEST['diagText_'.$g];
		$chl_diag_js_arr[]=$_REQUEST['diagText_'.$g];
	}
	$all_dx_codes_srz = serialize($chl_diag_arr);
	
	$sup_arr=explode('_',$edit_id);
	if($sup_arr[0]=="sup"){
		imw_query("update superbill set arr_dx_codes='$all_dx_codes_srz' where idSuperBill='".$sup_arr[1]."'");
	}else{
		imw_query("update patient_charge_list set all_dx_codes='$all_dx_codes_srz' where charge_list_id='$edit_id'");
	}
	for($j=1;$j<=$_REQUEST['last_cnt'];$j++){
		$chld_diag_arr=$sup_diag_arr=$show_dx_code_arr=array();
		for($g=1;$g<=12;$g++){
			$chld_diag_arr['diagnosis_id'.$g] = "";
			$sup_diag_arr['dx'.$g] = "";
		}
		
		for($g=0;$g<12;$g++){
			$diagText_all_exp=array();
			$diagText_all_exp=explode('**',$_REQUEST['diagText_all_'.$j][$g]);
			if($diagText_all_exp[1]>0){
				$chld_diag_arr['diagnosis_id'.$diagText_all_exp[1]] = $diagText_all_exp[0];
				$sup_diag_arr['dx'.$diagText_all_exp[1]] = $diagText_all_exp[0];
				$show_dx_code_arr[]=$diagText_all_exp[0];
			}
		}
		$charge_list_detail_id=$_REQUEST['chld_id_'.$j];
		if($sup_arr[0]=="sup"){
			UpdateRecords($charge_list_detail_id,'id',$sup_diag_arr,'procedureinfo');
		}else{
			UpdateRecords($charge_list_detail_id,'charge_list_detail_id',$chld_diag_arr,'patient_charge_list_details');
		}
?>		
	<script type='text/javascript'>
		window.opener.$('input[name="diagText_all[<?php echo $edit_id;?>][<?php echo $charge_list_detail_id;?>][]"]').val('<?php echo implode(', ',$show_dx_code_arr);?>');
    </script>
<?php	
    }
?>
<script type='text/javascript'>
	window.close();
</script>

<?php	
}
$sup_arr=explode('_',$edit_id);
if($sup_arr[0]=="sup"){
	$patientSuperDt = imw_query("SELECT * FROM superbill WHERE del_status='0' and idSuperBill = '".$sup_arr[1]."'");
	$patientSuperDetails = imw_fetch_object($patientSuperDt);
	$all_dx_codes = $patientSuperDetails->arr_dx_codes;
	$patient_id = $patientSuperDetails->patientId;
	$encounter_id = $patientSuperDetails->encounterId;
	$formId = $patientSuperDetails->formId;
	if($formId>0){
		$patientChartDt = imw_query("SELECT * FROM chart_master_table WHERE id = '".$formId."'");
		$patientChartDetails = imw_fetch_object($patientChartDt);
		$enc_icd10 = $patientChartDetails->enc_icd10;
	}else{
		$enc_icd10 = $patientSuperDetails->enc_icd10;
	}
	$all_dx_codes_arr=unserialize(html_entity_decode(remove_spec_dx($all_dx_codes)));
}else{
	$patientChargesDt = imw_query("SELECT * FROM patient_charge_list WHERE del_status='0' and charge_list_id = '".$edit_id."'");
	$patientChargesDetails = imw_fetch_object($patientChargesDt);
	$all_dx_codes = $patientChargesDetails->all_dx_codes;
	$patient_id = $patientChargesDetails->patient_id;
	$encounter_id = $patientChargesDetails->encounter_id;
	$enc_icd10 = $patientChargesDetails->enc_icd10;
	$all_dx_codes_arr=unserialize(html_entity_decode($all_dx_codes));
}

// GET PATIENT INFO
$patientDt = imw_query('select * from patient_data where id= '.$patient_id.'');
$patientDetails = imw_fetch_object($patientDt);
$sql = "SELECT * FROM diagnosis_category order by category";
$rez = imw_query($sql);	
while($row=imw_fetch_array($rez)){
	$cat_id = $row["diag_cat_id"];		
	$sql = "SELECT * FROM diagnosis_code_tbl WHERE diag_cat_id ='".$cat_id."' order by dx_code,diag_description";
	$rezCodes = imw_query($sql);
	$arrSubOptions = array();
	if(imw_num_rows($rezCodes) > 0){
		while($rowCodes=imw_fetch_array($rezCodes)){
			$arrSubOptions[] = array($rowCodes["dx_code"]."-".$rowCodes["diag_description"],$xyz, $rowCodes["dx_code"]);
			$arrDxCodesAndDesc[] = $rowCodes["dx_code"];
			$arrDxCodesAndDesc[] = $rowCodes["d_prac_code"];				
			$arrDxCodesAndDesc[] = $rowCodes["diag_description"]; 
			
			$arrDxCodechld[$rowCodes["dx_code"]]=$rowCodes["diag_description"];
			
			$d_prac_code = $rowCodes['d_prac_code'];	
			$dx_code = $rowCodes['dx_code'];	
			
			$stringAllDiag.="'".str_replace("'","",$d_prac_code)."',";
			if($d_prac_code!=$dx_code){
				$stringAllDiag.="'".str_replace("'","",$dx_code)."',";
			}
			
			$diag_description = $rowCodes['diag_description'];
			$stringAllDiag.="'".str_replace("'","",$diag_description)."',";
		}
	$arrDxCodes[] = array($row["category"],$arrSubOptions);
	}		
}	
$stringAllDiag = substr($stringAllDiag,0,-1);
?>
<script type="text/javascript">
<?php if($stringAllDiag!=""){?>
	var customarrayDiag= new Array(<?php echo remLineBrk($stringAllDiag); ?>);
<?php } ?>
function chkValidation(id){
	if(document.getElementById('enc_icd10').value>0){
	}else{
		var f1a = document.getElementById(id);
		var f1 = f1a.value;
		$.ajax({
			type:'GET',
			url:'edit_enc_dx.php?fetch_code=yes&dx_code_qry='+f1,
			success:function(response){
				if(response!=""){
					document.getElementById(id).value=response;
				}
			}
		});
	}
}
</script>
<div style="width:100%; overflow-y:auto; height:330px; overflow-x:hidden;" id="main_container_div" class="pt10">
<div class="col-sm-12 purple_bar">
    <div class="row">
        <div class="col-sm-6">
            <label>Edit Dx Codes</label>	
        </div>	
        <div class="col-sm-4">
            <label><b>Patient Name&nbsp;:&nbsp;</b><?php echo $patientDetails->fname.', '.$patientDetails->lname.' ('.$patientDetails->id.')'; ?></label>	
        </div>	
        <div class="col-sm-2">
            <label><b>EId&nbsp;:&nbsp;</b><?php echo $encounter_id; ?></label>	
        </div>		
    </div>
</div>
    <form name="dx_form" action="edit_enc_dx.php" method="post">
        <input type="hidden" value="<?php echo $edit_id; ?>" name="edit_id" id="edit_id">
        <input type="hidden" value="<?php echo $enc_icd10; ?>" name="enc_icd10" id="enc_icd10">
        <input type="hidden" value="yes" name="frm_sub" id="frm_sub">
        <div class="row">
			<?php for($i=1;$i<=12;$i++){?>
                <div class="col-sm-1">
                    <label>DX<?php echo $i; ?><?php if($i<10){ echo "&nbsp;";} ?></label>
                    <input type="text" name="diagText_<?php echo $i; ?>" id="diagText_<?php echo $i; ?>" value="<?php echo $all_dx_codes_arr[$i]; ?>" class="form-control dx_box_12" onChange="chkValidation('diagText_<?php echo $i; ?>');">
                </div>
                 <?php //if($i=="6"){echo '<div class="clearfix"></div>';} ?>
            <?php } ?>
        </div>
        <div class="row pt10 col-sm-8">
            <table class="table table-bordered">
                <tr class="grythead">
                    <th>Procedure</th>
                    <th>Dx Codes</th>
                </tr>
                <?php
				$pro_cont=0;
				if($sup_arr[0]=="sup"){
					$pcld_qry = imw_query("SELECT * FROM procedureinfo WHERE delete_status='0' and idSuperBill = '".$sup_arr[1]."'");
				}else{
					$pcld_qry = imw_query("SELECT patient_charge_list_details.*,cpt_fee_tbl.cpt_prac_code FROM patient_charge_list_details join cpt_fee_tbl on patient_charge_list_details.procCode=cpt_fee_tbl.cpt_fee_id WHERE del_status='0' and charge_list_id = '".$edit_id."'");
				}
				while($pcld_row = imw_fetch_array($pcld_qry)){
					$pro_cont++;
					if($sup_arr[0]=="sup"){
						$cpt_prac_code=$pcld_row['cptCode'];
						$chld_id=$pcld_row['id'];
						$dx_nam="dx";
					}else{
						$cpt_prac_code=$pcld_row['cpt_prac_code'];
						$chld_id=$pcld_row['charge_list_detail_id'];
						$dx_nam="diagnosis_id";
					}
				?>
                <tr id="<?php echo $pro_cont; ?>" class="text-center">
                    <td><?php echo $cpt_prac_code; ?></td>
                    <td>
                    	 <span>
                         	<input type="hidden" value="<?php echo $chld_id; ?>" name="chld_id_<?php echo $pro_cont; ?>" id="chld_id_<?php echo $pro_cont; ?>">
                        	<input type="hidden" name="app_proc_dx_code_<?php echo $pro_cont; ?>" id="app_proc_dx_code_<?php echo $pro_cont; ?>" value="">
                             <select name="diagText_all_<?php echo $pro_cont; ?>[]" id="diagText_all_<?php echo $pro_cont; ?>" class="diagText_all_css selectpicker" data-title="Select Dx Codes" data-actions-box="true" multiple="multiple" onChange="chk_adm_dx('<?php echo $pro_cont; ?>'); ">
                               <?php for($f=1;$f<=12;$f++){
                                    $dx_val=$pcld_row[$dx_nam.$f];
                                    if($dx_val!=""){
                                        $dx_send_val=$dx_val.'**'.$f;
                                        $dx_sel="selected";
                                 ?>
                                    <option value="<?php echo $dx_send_val; ?>" <?php echo $dx_sel; ?>><?php echo $dx_val; ?></option>
                               <?php }} ?>
                              </select>
                          </span>
                    </td>
                </tr> 
          	<?php } ?>
            </table>
        </div>
        <input type="hidden" value="<?php echo $pro_cont; ?>" name="last_cnt" id="last_cnt">
    </form> 
</div>
   
<div class="ad_modal_footer module_buttons" style="margin-top:5px;">
    <input name="sbmtFrm" id="sbmtFrm" type="button" class="btn btn-success" value="Update" onClick="document.dx_form.submit();">
    <input type="button" name="CancelBtn" id="CancelBtn" class="btn btn-danger" value="Cancel" onClick="window.close();">
</div>
<script type="text/javascript">
	$(document).ready(function(){
		set_dx_typeahead('');
		$(".dx_box_12").blur(crt_dx_dropdown);
		crt_dx_dropdown('<?php echo $pro_cont; ?>','');
	});
</script>

