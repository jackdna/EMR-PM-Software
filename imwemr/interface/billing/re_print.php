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
set_time_limit(0); 
$without_pat="yes"; 
require_once("../accounting/acc_header.php");
include_once(dirname(__FILE__)."/../../library/classes/billing_functions.php");
if($_REQUEST['re_print_pts']!=""){
	$re_print_pts_imp=$_REQUEST['re_print_pts'];
	//$re_print_pts_imp=implode(',',$_REQUEST['re_print_pts']);
	//------------------------ Get Patients Detail ------------------------//
	$qry=imw_query("select lname,fname,mname,id,sex from patient_data where id in($re_print_pts_imp)");
	while($row=imw_fetch_array($qry)){
		$patientName  = $row['lname'].', ';
		$patientName .= $row['fname'].' ';
		$patientName .= $row['mname'];
		$pat_name_arr[$row['id']]=$patientName;
		$pat_sex_arr[$row['id']]=$row['sex'];
	}
	//------------------------ Get Patients Detail ------------------------//
	
	$re_chl_chk_box=$_REQUEST['re_chl_chk_box'];
	$InsComp=$_REQUEST['InsComp'];
	
	if($_REQUEST['Printub']!=""){
		$print_paper_type=$_REQUEST['Printub'];
	}else if($_REQUEST['WithoutPrintub']!=""){
		$print_paper_type=$_REQUEST['WithoutPrintub'];
	}else if($_REQUEST['PrintCms_white']!=""){
		$print_paper_type=$_REQUEST['PrintCms_white'];
	}else{
		$print_paper_type=$_REQUEST['PrintCms'];
	}
	
	$show_paper_name="CMS-1500";
	if($_REQUEST['Printub']!="" || $_REQUEST['WithoutPrintub']!=""){
		$show_paper_name="UB-04";
	}
	
	for($g=0;$g<=count($re_chl_chk_box);$g++){
		$claim_ctrl_up="";
		$chk_charge_list_id = $re_chl_chk_box[$g];
		if($chk_charge_list_id>0){
			if($InsComp == 3){
				$claim_ctrl_up_val = $claim_ctrl[3][$chk_charge_list_id];
				$claim_ctrl_up=" claim_ctrl_ter='".$claim_ctrl_up_val."'";
				$chl_ins_comp="tertiaryInsuranceCoId";
				$ins_comp_type="tertiary";
			}else if($InsComp == 2){
				$claim_ctrl_up_val = $claim_ctrl[2][$chk_charge_list_id];
				$claim_ctrl_up=" claim_ctrl_sec='".$claim_ctrl_up_val."'";
				$chl_ins_comp="secondaryInsuranceCoId";
				$ins_comp_type="secondary";
			}else{
				$claim_ctrl_up_val = $claim_ctrl[1][$chk_charge_list_id];
				$claim_ctrl_up=" claim_ctrl_pri='".$claim_ctrl_up_val."'";
				$chl_ins_comp="primaryInsuranceCoId";
				$ins_comp_type="primary";
			}
			
			imw_query("update patient_charge_list set $claim_ctrl_up where charge_list_id='$chk_charge_list_id'");
			
			$chl_ins_qry=imw_query("select chl.charge_list_id,chl.encounter_id,chl.patient_id,users.user_npi,users.TaxonomyId 
			from patient_charge_list chl 
			join insurance_data ins_data on chl.case_type_id=ins_data.ins_caseid
			join users on users.id = chl.primaryProviderId
			where chl.del_status='0' and chl.charge_list_id='$chk_charge_list_id' and chl.$chl_ins_comp>0
			and ins_data.type='$ins_comp_type' and ins_data.provider = chl.$chl_ins_comp");
			
			$chl_ins_row=imw_fetch_array($chl_ins_qry);
			if($chl_ins_row['charge_list_id']>0){
				if(trim($pat_sex_arr[$chl_ins_row['patient_id']])==""){
					$error[$chl_ins_row['encounter_id']][] = 'Patient Gender Infomation is Required.';
				}
				if($chl_ins_row['reff_phy_nr']==0 && ($_REQUEST['PrintCms']!='' || $_REQUEST['PrintCms_white']!='')){
					if($chl_ins_row['reff_phy_id']>0){
						
						//------------------------ Get Reffering Physician Detail ------------------------//
						$ref_qry=imw_query("select NPI from refferphysician where physician_Reffer_id ='".$chl_ins_row['reff_phy_id']."'");
						$ref_row=imw_fetch_array($ref_qry);
						//------------------------ Get Reffering Physician Detail ------------------------//
						
						if(trim($ref_row['NPI'])==""){
							$error[$chl_ins_row['encounter_id']][] = 'Referring Physician NPI # is Required.';
						}
					}else{
						if(trim($chl_ins_row['user_npi'])==""){
							$error[$chl_ins_row['encounter_id']][] = 'Referring Physician NPI # is Required.';
						}
					}
				}
				if(trim($chl_ins_row['user_npi'])==""){
					$error[$chl_ins_row['encounter_id']][] = 'Rendering Physician NPI # is Required.';
				}
				if(trim($chl_ins_row['TaxonomyId'])==""){
					$error[$chl_ins_row['encounter_id']][] = 'Rendering Physician Taxonomy # is Required.';
				}
			}else{
				$error[$chl_ins_row['encounter_id']][] = "Patient Has Not ".ucfirst($ins_comp_type)." Insurance Company For $show_paper_name";
			}
			
			if(count($error[$chl_ins_row['encounter_id']])==0){
				$_REQUEST['chl_chk_box'][]=$chk_charge_list_id;
			}
		}
	}
	
	if(count($_REQUEST['chl_chk_box'])>0){
		$newFile="yes";
		if($print_paper_type=="Printub" || $print_paper_type=="WithoutPrintub"){
			$file_name="print_ub.php";
		}else{
			$file_name="print_hcfa_form.php";
		}
		require_once($file_name);
	}
	$_REQUEST['pat_srh_id']=$_REQUEST['re_print_pts'];
	$_REQUEST['elem_status']=$_REQUEST['re_sel_by'];
	$_REQUEST['txt_for']=$_REQUEST['re_txt_for'];
}
?>
<div class="purple_bar">Re-Print</div>
<?php
include_once("search_patient_popup.php");
if($_REQUEST['pat_srh_id']!=""){
	//$pat_srh_id_imp=implode(',',$_REQUEST['pat_srh_id']);
	$pat_srh_id_imp=$_REQUEST['pat_srh_id'];
	$qry = imw_query("select patient_charge_list.*,date_format(patient_charge_list.postedDate,'%m-%d-%y') as postedDate,date_format(patient_charge_list.date_of_service,'%m-%d-%y') as date_of_service 
				from patient_charge_list join patient_charge_list_details on patient_charge_list.charge_list_id = patient_charge_list_details.charge_list_id  
				where patient_charge_list.del_status='0' and patient_charge_list.patient_id in($pat_srh_id_imp) and patient_charge_list.submitted = 'true'
				and patient_charge_list_details.del_status='0' group by patient_charge_list.encounter_id order by patient_charge_list.encounter_id desc , patient_charge_list.postedDate");
	while($chl_res=imw_fetch_array($qry)){
		$chl_data_arr[]=$chl_res;
		$chl_enc_arr[$chl_res['encounter_id']]=$chl_res['encounter_id'];
		$gro_id_arr[$chl_res['gro_id']]=$chl_res['gro_id'];
	}
	
	$chl_enc_imp = implode(",",$chl_enc_arr);
	//------------------------ Get Encounter Submitted Detail ------------------------//
	$qry=imw_query("select encounter_id,submited_date from submited_record where encounter_id in($chl_enc_imp) order by submited_id asc");
	while($row=imw_fetch_array($qry)){
		$biil_type_enc_arr[$row['encounter_id']]=$row['submited_date'];
	}
	//------------------------ Get Encounter Submitted Detail ------------------------//
	
	//----------------------- Get Group Detail -------------------//
	if(count($gro_id_arr)>0){
		$gro_id_imp=implode(',',$gro_id_arr);						
		$grp_qry=imw_query("select group_color,gro_id from groups_new where gro_id in($gro_id_imp)");
		while($grp_row=imw_fetch_array($grp_qry)){
			$grp_detail[$grp_row['gro_id']]=$grp_row;	
		}
	}
	//----------------------- Get Group Detail -------------------//
?>
<form name="re_print_frm" id="re_print_frm" action="re_print.php" method="post">
<input type="hidden" name="re_print_pts" id="re_print_pts" value="<?php echo $_REQUEST['pat_srh_id']; ?>">
<input type="hidden" name="re_txt_for" id="re_txt_for" value="<?php echo $_REQUEST['txt_for']; ?>">
<input type="hidden" name="re_sel_by" id="re_sel_by" value="<?php echo $_REQUEST['sel_by']; ?>">
    <div class="row pt10">
        <div class="col-sm-12">
            <div style="height:430px; overflow:auto">
             	<?php if(count($chl_data_arr)>0){?>
                    <div style="background-color:#f0f0f0;">
                        <table class="table" style="width:80%; margin:0px;">
                            <?php
							$set_pri_cc=$set_sec_cc=$set_ter_cc="display:none;";
                            if($_REQUEST['InsComp']=="3"){
                                $InsComp_sel[3]="checked";
								$set_ter_cc = "";
                            }
							if($_REQUEST['InsComp']=="2"){
                                $InsComp_sel[2]="checked";
								$set_sec_cc = "";
                            } 
							if($_REQUEST['InsComp']=="1" || $_REQUEST['InsComp']==""){
                                $InsComp_sel[1]="checked";
								$set_pri_cc = "";
                            }
                            ?>
                            <tr>
                                <td style="border:none;">
                                    <div class="radio radio-inline">
                                        <input type="radio" id="InsComp_pri" name="InsComp" value="1" onClick="set_cc_box(1);" <?php echo $InsComp_sel[1]; ?>/>
                                        <label for="InsComp_pri"><strong>Primary Ins.</strong></label>
                                    </div>
                                </td>  
                                <td style="border:none;">
                                    <div class="radio radio-inline">	
                                        <input type="radio" id="InsComp_sec" name="InsComp" value="2" onClick="set_cc_box(2);" <?php echo $InsComp_sel[2]; ?>/>
                                        <label for="InsComp_sec"><strong>Secondary Ins.</strong></label>
                                    </div>
                                </td>
                                <td style="border:none;">
                                    <div class="radio radio-inline">	
                                        <input type="radio" id="InsComp_tri" name="InsComp" value="3" onClick="set_cc_box(3);" <?php echo $InsComp_sel[3]; ?>/>
                                        <label for="InsComp_tri"><strong>Tertiary Ins.</strong></label>
                                    </div>
                                </td> 
                            </tr>
                            <?php
                            if($_REQUEST['WithoutPrintub']!=""){
                                $print_cms_sel['WithoutPrintub']="checked";
                            }else if($_REQUEST['Printub']!=""){
                                $print_cms_sel['Printub']="checked";
                            }else if($_REQUEST['PrintCms_white']!=""){
                                $print_cms_sel['PrintCms_white']="checked";
                            }else{
                                $print_cms_sel['PrintCms']="checked";
                            }
                            ?>
                            <tr>
                                <td style="border:none;">
                                    <div class="checkbox">
                                        <input type="checkbox" id="PrintCms" name="PrintCms" value="PrintCms" onClick="selectChkBox(this);" <?php echo $print_cms_sel['PrintCms']; ?>>
                                        <label for="PrintCms"><strong>CMS 1500</strong></label>
                                    </div>
                                </td>  
                                <td style="border:none;">
                                    <div class="checkbox">
                                        <input type="checkbox" id="PrintCms_white" name="PrintCms_white" value="PrintCms_white" onClick="selectChkBox(this);" <?php echo $print_cms_sel['PrintCms_white']; ?>>
                                        <label for="PrintCms_white"><strong>CMS 1500 - Red Form</strong></label>
                                    </div>
                                </td>
                                <td style="border:none;">
                                    <div class="checkbox">
                                        <input type="checkbox" id="Printub" name="Printub" value="Printub" onClick="selectChkBox(this);" <?php echo $print_cms_sel['Printub']; ?>>
                                        <label for="Printub"><strong>UB-04</strong></label>
                                    </div>
                                </td>
                                <td style="border:none;">
                                    <div class="checkbox">
                                        <input type="checkbox" id="WithoutPrintub" name="WithoutPrintub" value="WithoutPrintub" onClick="selectChkBox(this);" <?php echo $print_cms_sel['WithoutPrintub']; ?>>
                                        <label for="WithoutPrintub"><strong>UB-04 - Red Form</strong></label>
                                    </div>
                                </td>  
                            </tr>
                        </table>
                     </div>
                    <?php 
					if(count($error)>0){
						$error_string="";
						foreach($error as $error_key=>$error_val){
							$error_enc_arr[$error_key]=$error_key;
							$error_string .="<div class=\'alert alert-info\' style=\'padding:6px; margin-bottom:3px; font-weight:bold;\'>Encounter Id - $error_key</div>";
							$error_string .="<ul>";
							foreach($error[$error_key] as $error_det){
								$error_string .="<li>$error_det</li>";
							}
							$error_string .="</ul>";
						}
					?>
						<div class="text-center alert alert-danger" style="padding:6px; margin-bottom:3px;">
							<label>Information is required for the encounter Id <?php echo implode(', ',$error_enc_arr); ?>.</label>
							<span class="glyphicon glyphicon-info-sign pull-right" style="font-size:19px; cursor:pointer;" onclick="show_enc_error('<?php echo $error_string; ?>');"></span>
						</div>
            		<?php } ?>
                    <table class="table table-striped table-bordered table-hover">
                        <tr class="grythead">
                            <th>
                                <div class="checkbox">
                                    <input type="checkbox" name="chkbx_all" id="chkbx_all" onClick="return chk_all();"/>
                                    <label for="chkbx_all"></label>
                                </div>
                            </th>
                            <th align="center">Format</th>
                            <th align="center">Patient Id</th>
                            <th align="center">DOS</th>
                            <th align="center">E.ID</th>
                            <th align="center">Posted Date</th>
                            <th align="center">Resubmited Date</th>
                            <th align="center">Total Charges</th>
                            <th align="center">Claim Control#</th>
                        </tr>
                        <?php
                        foreach($chl_data_arr as $key=>$val){
							$chl_data=$chl_data_arr[$key];
							$submited_date=get_date_format($biil_type_enc_arr[$chl_data['encounter_id']]);
							$group_color=$grp_detail[$chl_data['gro_id']]['group_color'];
							
							$sel_bill_831="selected";
							$sel_bill_837="";
							$claim_ctrl_pri = $chl_data['claim_ctrl_pri'];
							$claim_ctrl_sec = $chl_data['claim_ctrl_sec'];
							$claim_ctrl_ter = $chl_data['claim_ctrl_ter'];
							if($claim_ctrl_pri!=""){
								$sel_bill_831="";
								$sel_bill_837="selected";
							}
							
							if($claim_ctrl_pri==""){
								$claim_ctrl_pri=billing_global_get_clm_control_num($chl_data['patient_id'],$chl_data['encounter_id'],0,'primary');
							}
							if($claim_ctrl_sec==""){
								$claim_ctrl_sec=billing_global_get_clm_control_num($chl_data['patient_id'],$chl_data['encounter_id'],0,'secondary');
							}
						?>
                        <tr>
                           <td <?php echo show_gro_color($group_color); ?> class="text-center">
                            	<div class="checkbox">
                                    <input type="checkbox" name="re_chl_chk_box[]" id="chl_id_<?php echo $chl_data['charge_list_id']; ?>" value="<?php echo $chl_data['charge_list_id']; ?>" class="chk_box_css"/>
                                    <label for="chl_id_<?php echo $chl_data['charge_list_id']; ?>"></label>
                                </div>	
                           </td>
                           <td class="text_10" align="center">
                               <select name="bill_type[<?php echo $chl_data['charge_list_id']; ?>]"  id="bill_type<?php echo $chl_data['charge_list_id']; ?>" class="selectpicker" data-width="100%">
                                    <option value="831" <?php echo $sel_bill_831; ?>>831</option>
                                    <option value="837" <?php echo $sel_bill_837; ?>>837</option>
                               </select>
                           </td>
                           <td class="text_10" align="center"><?php echo $chl_data['patient_id']; ?></td>
                           <td class="text_10" align="center" nowrap><?php echo $chl_data['date_of_service']; ?></td>
                           <td class="text_10" align="center"><?php echo $chl_data['encounter_id']; ?></td>
                           <td class="text_10" align="center"><?php echo $chl_data['postedDate']; ?></td>
                           <td class="text_10" align="center"><?php echo $submited_date; ?></td>
                           <td class="text_10" align="right" style="padding-right:10px"><?php echo numberFormat($chl_data['totalBalance'],2); ?></td>
                           <td class="text_10" align="center">
                            <input class="cc_pri_cls" type="text" name="claim_ctrl[1][<?php echo $chl_data['charge_list_id']; ?>]" id="pri_claim_ctrl_<?php echo $chl_data['charge_list_id']; ?>" value="<?php echo $claim_ctrl_pri;?>" style="width:100px; <?php echo $set_pri_cc;?>">
                            <input class="cc_sec_cls" type="text" name="claim_ctrl[2][<?php echo $chl_data['charge_list_id']; ?>]" id="sec_claim_ctrl_<?php echo $chl_data['charge_list_id']; ?>" value="<?php echo $claim_ctrl_sec;?>" style="width:100px; <?php echo $set_sec_cc;?>">
                            <input class="cc_ter_cls" type="text" name="claim_ctrl[3][<?php echo $chl_data['charge_list_id']; ?>]" id="ter_claim_ctrl_<?php echo $chl_data['charge_list_id']; ?>" value="<?php echo $claim_ctrl_ter;?>" style="width:100px; <?php echo $set_ter_cc;?>">
                           </td>
                        </tr>  
                  <?php }?>	
                    </table>
              <?php }else{ ?>
              		<table class="table table-striped table-bordered table-hover">
                    	<tr>
                            <td class="text-center lead"><?php echo imw_msg('no_rec'); ?></td>
                        </tr>
                    </table>
              <?php } ?>   
            </div>
            <div class="col-sm-12 text-center" id="page_buttons">
                <input type="button" class="btn btn-success" align="bottom" name="print_process" id="print_process" onclick="check_data();" value="Print Claims">	
            </div>
        </div>
    </div>
</form>    
<?php } ?>    
</div>
</body>
</html>
<script type="text/javascript">
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

function check_data(){
	var obj = document.getElementsByName("re_chl_chk_box[]");
	var msg = false;
	var file_name="";
	for(i=0;i<obj.length;i++){
		if(obj[i].checked == true){
			msg = true;
		}
	}
	if(msg == false){
		alert('Please select any record to print');
		return false;
	}
	document.re_print_frm.submit();
}

function set_cc_box(val){
	$('.cc_pri_cls, .cc_sec_cls, .cc_ter_cls').hide();
	if(val==3){
		$(".cc_ter_cls").show();
		set_bill_type('cc_ter_cls');
	}else if(val==2){
		$(".cc_sec_cls").show();
		set_bill_type('cc_sec_cls');
	}else{
		$(".cc_pri_cls").show();
		set_bill_type('cc_pri_cls');
	}
}

function show_enc_error(val){
	show_modal('error_enc','Encounters With Errors',val);
}

function set_bill_type(cls){
	$('.'+cls).each(function(){
		var bill_type_id = $(this).closest('tr').find('select[id^="bill_type"]');
		if($(this).val()!=''){
			$(bill_type_id).val(837);
		}else{
			$(bill_type_id).val(831);
		}
	});
	$(".selectpicker").selectpicker('refresh');
}
</script>