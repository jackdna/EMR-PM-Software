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

$var_subscriber_employer = 'i'.$i_key.'subscriber_employer';
$var_group_number = 'i'.$i_key.'group_number';
$var_copay = 'i'.$i_key.'copay';
$var_co_ins = 'i'.$i_key.'co_ins';
$var_authreq = 'i'.$i_key.'authreq';
$var_claims_adjustername = 'i'.$i_key.'claims_adjustername';
$var_claims_adjusterphone = 'i'.$i_key.'claims_adjusterphone';

// Variable for Labels AND Css Classes
$group_label = array();
$copay_class = ($i_key == '1') ? 'col-sm-6' : 'col-sm-12';
$reffer_req_class = ($caseQryRes[0]['normal'] == 1) ? 'col-sm-4' : 'col-sm-6';
$copay_type_class = ($caseQryRes[0]['normal'] == 1) ? 'col-sm-4' : 'col-sm-6';
$dob_class = ($i_key == '1') ? 'col-sm-4' : 'col-sm-6';
$gender_class = ($i_key == '1') ? 'col-sm-3' : 'col-sm-6';

if($caseQryRes[0]['normal'] == 1 || $caseQryRes[0]['vision'] == 1)
{
	$policy_label = "Policy";
	$class = 'form-control '.(in_array($var_group_number,$mandatory_flds) ? 'mandatory-chk mandatory' : '');
	$group_label[0] = '<input type="text" size="20" name="'.$var_group_number.'" id="'.$var_group_number.'" value="'.stripslashes($comDetail->group_number).'" class="'.$class.'" onKeyUp="top.chk_change_in_form('."'".addslashes($comDetail->group_number)."'".',this,'."'InsTabDb'".',event); chk_change('."'".addslashes($comDetail->group_number)."'".',this.value,event);" onBlur="lost_focus(this,'."'".$class."'".');" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);">';

	$group_label[1] = "Group#";

	$comFlag_auth = false;
	if($comDetail->referal_required == 'Yes' && $caseQryRes[0]['normal'] == 1){
		$comFlag = true;
		$comSel = 'selected';
	}
	else if($comDetail->auth_required == 'Yes' && $caseQryRes[0]['vision'] == 1){
		$comFlag_auth = true;
		$comSel = 'selected';
	}
	else{
		$comFlag = false;
		$comSel = '';
	}
	$com_copay = $comDetail->copay;
	$com_co_ins = $comDetail->co_ins;
	
	$class = 'form-control '.(in_array($var_copay,$mandatory_flds) ? 'mandatory-chk mandatory' : '');
	$ref_phy = 'Ins'.ucfirst($s_name).'RefPhysician';
	if($$ref_phy == 1){
		$defaultClassForRef = "selectpicker mandatory-chk mandatory";
	}
	else{
		$defaultClassForRef = "selectpicker";
	}
	
	if($caseQryRes[0]['vision'] == 1){
		$shw_req="sub_auth_".$i_key;
		$shw_req_nam="i".$i_key."authreq";
	}else{
		$shw_req="sub_".$i_key;
		$shw_req_nam="i".$i_key."referalreq";
	}
	if($comDetail->id > 0){
		if($comDetail->copay_type==0){
			$com_copay_type1 = 'selected';
		}else if($comDetail->copay_type==1){
			$com_copay_type2 = 'selected';
		}else if($comDetail->copay_type==2){
			$com_copay_type3 = 'selected';
		}
	}else{
		if($policies_copay_type==0){
			$com_copay_type1 = 'selected';
		}else if($policies_copay_type==1){
			$com_copay_type2 = 'selected';
		}else if($policies_copay_type==2){
			$com_copay_type3 = 'selected';
		}
	}

	if($com_co_ins!=''){ $com_co_ins = stripslashes($com_co_ins); }else{ $com_co_ins="00/00";}
	
		if($caseQryRes[0]['normal'] == 1){
			$comAuthSel = ($comDetail->auth_required == 'Yes') ? "selected":"";
		if($comDetail->auth_required == 'Yes')
			$comFlag_auth = true;	
		$auth_req = '
			<div class="col-sm-4">
				<div class="form-group">
					<label for="'.$var_authreq.'">Auth Req</label>
					<select class="form-control minimal" data-width="100%" data-dropup-auto="false" name="'.$var_authreq.'" id="'.$var_authreq.'" onChange="top.chk_change_in_form('."''".',this,'."'InsTabDb'".',event); showSub('."'sub_auth_".$i_key."'".',this.value); changeClassCombo(this,'."'".$defaultClassForRef."'".');">
							<option value="No">No</option>
							<option value="Yes" '.$comAuthSel.'>Yes</option>
					</select>
				</div>
			</div>';
		}else $auth_req = '';
		
	$copay_label = '
		<div class="'.$copay_class.'">
			<div class="form-group">
				<label for="'.$var_copay.'">CoPay</label>
				<input type="text" maxlength="5" name="'.$var_copay.'" id="'.$var_copay.'" value="'.stripslashes($com_copay).'" class="form-control" onKeyUp="top.chk_change_in_form('."'".addslashes($com_copay)."'".',this,'."'InsTabDb'".',event); chk_change('."'".addslashes($com_copay)."'".',this.value,event);" onBlur="lost_focus(this,'."'form-control'".');" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);">
			</div>
		</div>';
		
		if($i_key == '1')
		{
			$copay_label .='<div class="col-sm-6">
				<div class="form-group">
					<label for="'.$var_co_ins.'">Co-Ins</label>
					<input type="text" name="'.$var_co_ins.'" id="'.$var_co_ins.'" value="'.$com_co_ins.'" class="form-control" onKeyUp="top.chk_change_in_form('."'".addslashes($com_co_ins)."'".',this,'."'InsTabDb'".',event); chk_change('."'".addslashes($com_co_ins)."'".',this.value,event);" onBlur="lost_focus(this,'."'form-control'".');" onKeyPress="save_data(event);"  >
				</div>
			</div>';
		}
		
		$copay_label .= '<div class="'.$reffer_req_class.'">
			<div class="form-group">
				<label for=" ">'.($caseQryRes[0]['vision'] == 1 ? 'Auth&nbsp;Req' : 'Refer&nbsp;Req').'</label>
				<select  class="form-control minimal dropup" data-width="100%" data-dropup-auto="false" name="'.$shw_req_nam.'" id="'.$shw_req_nam.'" onChange="top.chk_change_in_form('."''".',this,'."'InsTabDb'".',event); showSub('."'".$shw_req."'".',this.value); changeClassCombo(this,'."'".$defaultClassForRef."'".');">
					<option value="No">No</option>
					<option value="Yes" '.$comSel.' >Yes</option>
				</select>
			</div>
		</div>
		
		<div class="'.$copay_type_class.'">
			<div class="form-group">
				<label for=" ">CoPay Type</label>
				<select name="'.$s_name.'_copay_type" class="form-control minimal dropup" data-width="100%" data-dropup-auto="false" onChange="top.chk_change_in_form('."''".',this,'."'InsTabDb'".',event);">
					<option value="0" '.$com_copay_type1.'>Practice</option>
					<option value="1" '.$com_copay_type2.'>Dilated/Un-Dilated</option>
					<option value="2" '.$com_copay_type3.'>Office/Test</option>
				</select>
			</div>
		</div>
		'.$auth_req;
}

else{
	$policy_label = "Claim";
	$class = 'form-control '.(in_array($var_subscriber_employer,$mandatory_flds) ? 'mandatory-chk mandatory' : '');
	//core_get_prac_field_css_class($arrPracFieldStatus,"i1subscriber_employer",trim($primaryComDetail->subscriber_employer));
	$group_label[0] = '<input type="text" size="20" name="'.$var_subscriber_employer.'" id="'.$var_subscriber_employer.'" value="'.$comDetail->subscriber_employer.'" class="'.$class.'" onKeyUp="top.chk_change_in_form('."'".addslashes($comDetail->subscriber_employer)."'".',this,'."'InsTabDb'".',event); onBlur="lost_focus(this,'."'".$class."'".');" onFocus="get_focus_obj(this);">';	
	$group_label[1] = 'Emp&nbsp;Name';
	
	$class = 'form-control '.(in_array($var_claims_adjustername,$mandatory_flds) ? 'mandatory-chk mandatory' : '');
	//core_get_prac_field_css_class($arrPracFieldStatus,"i1claims_adjustername",trim($primaryComDetail->claims_adjustername));	
	$copay_label = '
		<div class="col-sm-6">
			<div class="form-group">
				<label for="'.$var_claims_adjustername.'">Adj.&nbsp;Name :</label>
				<input type="text" size="20" name="'.$var_claims_adjustername.'" id="'.$var_claims_adjustername.'" value="'.stripslashes($comDetail->claims_adjustername).'" class="'.$class.'" onKeyUp="top.chk_change_in_form('."'".addslashes($comDetail->claims_adjustername)."'".',this,'."'InsTabDb'".',event); chk_change('."'".addslashes($comDetail->claims_adjustername)."'".',this.value,event);" onBlur="lost_focus(this,'."'".$class."'".');" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);">
			</div>
		</div>
		
		<div class="col-sm-6">
			<div class="form-group">
				<label for="'.$var_claims_adjusterphone.'">Phone</label>
				<input type="text" size="20" onChange="set_phone_format(this,\''.$GLOBALS['phone_format'] .'\',\'\',\'\',\'form-control\');" maxlength="12" name="'.$var_claims_adjusterphone.'" id="'.$var_claims_adjusterphone.'" value="'.$comDetail->claims_adjusterphone.'" class="form-control" onFocus="get_focus_obj(this);" onKeyUp="top.chk_change_in_form('."'".addslashes($comDetail->claims_adjusterphone)."'".',this,'."'InsTabDb'".',event);">
			</div>
		</div>';
}


$msp_type = '';
$dateClass = "col-lg-6 col-md-6 col-sm-12";
if($s_name == 'sec' && ($comDetail->ic_ins_type=='MB' || $comDetail->InsClaimType=='1')) {
	$msp_value_to_select = '';
	$msp_value_to_select = (strlen(trim($comDetail->ic_msp_type)) == 2)  ? trim($comDetail->ic_msp_type) : $msp_value_to_select;
	$msp_type_unsaved_class = ' class="unsaved_value_display"';
	if(strlen(trim($comDetail->msp_type)) == 2){
		$msp_value_to_select = trim($comDetail->msp_type);
		$msp_type_unsaved_class = '';
	}
	
	$dateClass = "col-lg-4 col-md-4 col-sm-12";
	$msp_type .= '<div class="col-lg-4 col-md-4 col-sm-12">';
	$msp_type .= '<label for="msp_type"'.$msp_type_unsaved_class.'>MSP Type</label>';
	$msp_type .= '<select name="msp_type" id="msp_type" class="form-control minimal" data-width="100%" data-container="#selectContainer"><option value=""></option>';

	
	
	$MSP_types = getMSPTypes();
	foreach($MSP_types as $v=>$t){
		$sel = '';
		if($msp_value_to_select==$v){
			$sel = 'selected';
			
		}
		$msp_type .= '<option value="'.$v.'" '.$sel.'>'.$v.' - '.$t.'</option>';
	}
  $msp_type .= '</select>';
	$msp_type .= '</div>';
}

?>

<div class="row">
	<div id="selectContainer" style="position:absolute"></div>
  <div class="col-lg-4 col-md-6 col-sm-12">
    <div class="table_grid" id="provider<?php echo $i_key; ?>_table">
			      
      <div class="insurancehd head">
        <div class="row">
          <div class="col-sm-7">
            <span>Insurance</span>
          </div>
          <?php if($i_key == '1') { ?>
          <div class="col-sm-5">
          	<div class="checkbox">
            	<?php $checked = ($comDetail->self_pay_provider) ? 'checked' : ''; ?>
              <input type="checkbox" name="self_pay_provider" id="self_pay_provider" value="1" <?php echo $checked; ?> onclick="top.chk_change_in_form('<?php echo addslashes($checked); ?>',this,'InsTabDb',event);" />
              <label for="self_pay_provider">Self Pay</label>
            </div>
          </div>
        	<?php } ?>
        </div>
      </div>
      <div class="plr5">
        <div class="clearfix"></div>
        <div class="row">
          <input type="hidden" name="expPrevious<?php echo ucfirst($s_name);?>" id="expPrevious<?php echo ucfirst($s_name);?>" value="" />
          <input type="hidden" name="actPrevious<?php echo ucfirst($s_name);?>" id="actPrevious<?php echo ucfirst($s_name);?>" value="<?php echo $actPreviousDate; ?>" />
          <input type="hidden" name="<?php echo $i_type;?>Id" id="<?php echo $i_type;?>Id" value="<?php echo $comid; ?>" />
          <input type="hidden" name="<?php echo $i_type;?>MainId" id="<?php echo $i_type;?>MainId" value="<?php echo $comDetail->id; ?>" />
          <input type="hidden" name="<?php echo $i_type;?>InsName" id="<?php echo $i_type;?>InsName" value="<?php echo $insCompanyName; ?>" />
          <input type="hidden" name="i<?php echo $i_key;?>provider" id="i<?php echo $i_key;?>provider" value="<?php echo $comDetail->provider; ?>" />
          <input type="hidden" name="insurenceProviderExit<?php echo ucfirst($s_name);?>" id="insurenceProviderExit<?php echo ucfirst($s_name);?>" value="<?php if(strlen($insCompanyName)>12){ echo (substr($insCompanyName,0,12)."..");}else{ echo $insCompanyName;}?>" />
          <input type="hidden" name="i<?php echo $i_key;?>providerRCOCode" id="i<?php echo $i_key;?>providerRCOCode" value="<?php echo $comDetail->rco_code; ?>" />
          <input type="hidden" name="i<?php echo $i_key;?>providerRCOId" id="i<?php echo $i_key;?>providerRCOId" value="<?php echo $comDetail->rco_code_id; ?>" />
          <input type="hidden" id="insCliamVal" name="insCliamVal" value="<?php echo $comDetail->InsClaimType; ?>" />
          
          <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
              <label for="insprovider<?php echo $i_key;?>"> Ins. Provider&nbsp;</label>
              <?php if($i_key == '1') { ?>
              <i class="glyphicon glyphicon-search pointer" for="insCompBtn" onclick="$('#temp_<?php echo $s_name;?>_ins_value').val($('#insprovider<?php echo $i_key;?>').val());$('#insprovider<?php echo $i_key;?>').val('');" ></i>
              <?php } ?>
              <div class="dropdown">
              	<div class="input-group">
                	<?php
										$d = insurance_provider_detail($comDetail->provider,$comDetail->rco_code_id);
										$tooltip = $tooltip_content = '';
										if(is_array($d) && count($d) > 0 )
										{
											foreach($d as $key => $val)
											{
												$tooltip_content .= '<b>'.ucfirst($key).': </b>'.$val.'<br>';
											}
											//$popover = 'data-toggle="popover" data-trigger="hover" data-placement="right" data-content="'.$popover_content.'" data-html="true" ';
											$tooltip = show_tooltip($tooltip_content);
										}
										$check_ins_claim = ($i_key == '1') ? 'checkInsClaim();' : '';
										$get_co_ins = ($i_key == '1') ? 'get_co_ins(this.value);' : '';
									?>
                
                	<input type="text" class="form-control" id="insprovider<?php echo $i_key;?>" name="insprovider<?php echo $i_key;?>" value="<?php echo $strInsCompanyName; ?>" data-sort="contain" data-prev-val="<?php echo addslashes($strInsCompanyName); ?>" onchange="<?php echo $check_ins_claim; ?> top.chk_change_in_form('<?php echo addslashes($strInsCompanyName); ?>',this,'InsTabDb',event); get_focus_obj(this, '0'); make_null_provider_id(this);" onKeyUp="top.chk_change_in_form('<?php echo addslashes($strInsCompanyName); ?>',this,'InsTabDb',event);" onBlur="top.chk_change_in_form('<?php echo addslashes($strInsCompanyName); ?>',this,'InsTabDb',event);lost_focus(this,'form-control'); <?php echo $get_co_ins; ?>" onFocus="get_focus_obj(this);hideDrop(this);" <?php echo $tooltip; ?> />
                	<label class="input-group-addon btn" id="insCompBtn" data-toggle="dropdown" data-target="#">
                  	<span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span>
                 	</label>
                  
                  <div role="menu" class="dropdown-menu">
                  	<ul class="list-group" style="height:180px; overflow:hidden; overflow-Y:auto;" id="<?php echo $s_name;?>InsCompData">
											<li></li>
											<li class="list-group-item loader-small"></li>
										</ul>    
                 	</div>
                  
                  <input type="hidden" id="temp_<?php echo $s_name;?>_ins_value" name="temp_<?php echo $s_name;?>_ins_value" value="" />
                  
              	</div>    
           		</div>
            </div>
          	
        	</div>
          
          <!-- Policy -->
          <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
              <label for="i<?php echo $i_key;?>policy_number"><?php echo $policy_label?></label>
              <input type="text" name="i<?php echo $i_key;?>policy_number" id="i<?php echo $i_key;?>policy_number" value="<?php echo stripslashes($comDetail->policy_number);?>" class="form-control" onKeyUp="top.chk_change_in_form('<?php echo addslashes($comDetail->policy_number); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($comDetail->policy_number);?>',this.value,event);" onBlur="lost_focus(this,'form-control');" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);">
            </div>
          </div>
          
          <div class="clearfix"></div>
          
          <!-- Group -->
          <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
              <label for="i<?php echo $i_key;?>group_number"><?php echo $group_label[1]; ?></label>
              <?php echo str_replace('input_text_10','form-control',$group_label[0]); ?> </div>
          </div>
          
          <!-- Plan Name -->
          <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
              <label for="">Plan Name</label>
              <input type="text" name="i<?php echo $i_key;?>plan_name" id="i<?php echo $i_key;?>plan_name" value="<?php echo stripslashes($comDetail->plan_name); ?>" class="form-control" onKeyUp="top.chk_change_in_form('<?php echo addslashes($comDetail->plan_name); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($comDetail->plan_name); ?>',this.value,event);" onBlur="lost_focus(this,'form-control');" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);">
            </div>
          </div>
          <?php
							//---- Copay Check ------
							echo $copay_label;
					?>
          <div class="<?php echo $dateClass; ?>">
            <div class="form-group">
              <label for="i<?php echo $i_key;?>effective_date">Act. Date</label>
              <div class="input-group">
                <input name="i<?php echo $i_key;?>effective_date" id="i<?php echo $i_key;?>effective_date" type="text" value="<?php echo stripslashes($effective_date); ?>" maxlength=10 class="datepicker form-control" title='<?php echo inter_date_format();?>' onBlur="top.checkdate(this); lost_focus(this,'form-control');" onchange="top.chk_change_in_form('<?php echo addslashes($effective_date); ?>',this,'InsTabDb',event);" onKeyUp="top.chk_change_in_form('<?php echo addslashes($effective_date); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($effective_date); ?>',this.value,event);" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);">
                <label for="i<?php echo $i_key; ?>effective_date" class="input-group-addon btn">
                	<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
               	</label>
              </div>
            </div>
          </div>
          
          <div class="<?php echo $dateClass; ?>">
            <?php
								$selectQry = "select DATE_FORMAT(expiration_date, '".get_sql_date_format()."') as expiration_date from insurance_data where type = '".$i_type."' and pid = '".$_SESSION['patient']."' and ins_caseid = '".$ins_caseid."' and id='".$comDetail->id."'";
								$qry = imw_query($selectQry);
								while($row=imw_fetch_array($qry)){
										$exitingExpirationDate=$row['expiration_date'];
								}
							?>
            <input type="hidden" name="<?php echo $s_name;?>ExitingExpirationDate" id="<?php echo $s_name;?>ExitingExpirationDate" value="<?php if ($exitingExpirationDate){ echo $exitingExpirationDate; } ?>" />
            <input name="<?php echo $s_name;?>ExpirationDate" id="<?php echo $s_name;?>ExpirationDate" value="<?php echo $expirationDate; ?>" type="hidden" >
            <div class="form-group">
              <label for="i<?php echo $i_key;?>expiration_date">Exp. Date</label>
              <div class="input-group">
                <input name="i<?php echo $i_key;?>expiration_date" id="i<?php echo $i_key;?>expiration_date" value="<?php echo $expiration_date; ?>" type="text" class="datepicker form-control" title='<?php echo inter_date_format();?>' onBlur="top.checkdate(this); lost_focus(this,'form-control');"  maxlength="10" onchange="top.chk_change_in_form('<?php echo addslashes($expiration_date); ?>',this,'InsTabDb',event);" onKeyUp="top.chk_change_in_form('<?php echo addslashes($expiration_date); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($expiration_date); ?>',this.value,event);" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);" >
                <label for="i<?php echo $i_key; ?>expiration_date" class="input-group-addon btn">
                	<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
              	</label>
              </div>
            </div>
          </div>
          <?php echo $msp_type; ?>
        </div>
        <div class="clearfix"></div>
      </div>
    </div>
  </div>
  
 	
  <div class="col-lg-4 col-md-6 col-sm-12">
  	<div class="table_grid" id="insPolicy<?php echo $i_key;?>_table">
    	
      <div class="insurancehd head"><span>Policy holder</span></div>
      <div class=" plr5">
        <div class="clearfix"></div>
        <div class="row">
          <?php 
							if($comDetail->subscriber_fname == '' && ($comDetail->subscriber_relationship == '' || $comDetail->subscriber_relationship == 'self'))
							{
									$patientFname = $patientDetail->fname;
									$patientLname = $patientDetail->lname;
									$patientMname = $patientDetail->mname;
									$patientSuffix = $patientDetail->suffix;
							}
							else
							{
									$patientFname = $comDetail->subscriber_fname;
									$patientLname = $comDetail->subscriber_lname;
									$patientMname = $comDetail->subscriber_mname;
									$patientSuffix = $comDetail->subscriber_suffix;
							}
							
							if(trim($patientFname) != "" && trim($patientLname) != ""){
									$hid_subscriber_exits_our_sys = "yes";
							}else{
									$hid_subscriber_exits_our_sys = "No";
							}
																		
							//--- UPDATE SUBSCRIBER DATA ----
							$updateSubDataArr = array();
							$updateSubDataArr[$comDetail->id]["subscriber_fname"] = $patientFname;
							$updateSubDataArr[$comDetail->id]["subscriber_mname"] = $patientMname;
							$updateSubDataArr[$comDetail->id]["subscriber_lname"] = $patientLname;
         	?>
          <div class="col-sm-4">
            <div class="form-group">
              <label for="i<?php echo $i_key;?>subscriber_fname">First Name</label>
              <input type="hidden" name="hid_<?php echo $s_name;?>_subscriber_exits_our_sys" id="hid_<?php echo $s_name;?>_subscriber_exits_our_sys" value="<?php echo $hid_subscriber_exits_our_sys; ?>"/>
              <input type="hidden" name="sub_<?php echo $s_name;?>_pat_id" id="sub_<?php echo $s_name;?>_pat_id" value="<?php echo $comDetail->sub_pat_id; ?>"/>
              <input type="text" class="form-control" size="13" name="i<?php echo $i_key;?>subscriber_fname" id="i<?php echo $i_key;?>subscriber_fname" value="<?php echo stripslashes($patientFname); ?>" onKeyUp="top.chk_change_in_form('<?php echo addslashes($patientFname);?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($patientFname); ?>',this.value,event);" onBlur="lost_focus(this,'form-control');" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);">
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              <label for="lastName<?php echo $i_key;?>">Last Name</label>
              <input size="11" class="form-control" type="text" name="lastName<?php echo $i_key;?>" id="lastName<?php echo $i_key;?>" value="<?php echo stripslashes($patientLname); ?>" onKeyUp="top.chk_change_in_form('<?php echo addslashes($patientLname); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($patientLname); ?>',this.value,event);" onBlur="lost_focus(this,'form-control');" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);" data-action="search_patient" data-grid="0" data-i-key="<?php echo $i_key; ?>" />
              <input size="11" class="form-control" type="hidden" name="hidlastName" id="hidlastName" value="<?php echo $patientDetail->lname; ?>"/>
            </div>
          </div>
          <div class="col-sm-2">
            <div class="form-group">
              <label for="i<?php echo $i_key;?>subscriber_mname">Middle</label>
              <input type="text" class="form-control" size="5" name="i<?php echo $i_key;?>subscriber_mname" id="i<?php echo $i_key;?>subscriber_mname" value="<?php echo stripslashes($patientMname); ?>" onKeyUp="top.chk_change_in_form('<?php echo addslashes($patientMname); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($patientMname); ?>',this.value,event);" onBlur="lost_focus(this,'form-control');" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);">
            </div>
          </div>
          <div class="col-sm-2">
            <div class="form-group">
              <label for="suffix_rel_<?php echo $s_name;?>">Suffix</label>
              <input size="2" type="text" id="suffix_rel_<?php echo $s_name;?>" name="suffix_rel_<?php echo $s_name;?>" class="form-control" value="<?php echo $patientSuffix; ?>" />
            </div>
          </div>
          <div class="clearfix"></div>
          <div class="col-sm-6">
            <div class="form-group">
              <label for=" ">Sub.Relation</label>
              <select class="form-control minimal" name="i<?php echo $i_key;?>subscriber_relationship" id="i<?php echo $i_key;?>subscriber_relationship" data-width="100%" data-size="5" title="<?php echo imw_msg('drop_sel'); ?>" onChange="top.chk_change_in_form('',this,'InsTabDb',event); popUpRelation(this.value,'<?php echo $i_type;?>',true,'','<?php echo $i_key;?>'); getvalue(this,'<?php echo $s_name;?>','<?php echo $i_key;?>'); changeClassCombo(this,'form-control minimal');" >
              	<option value="" selected><?php echo imw_msg('drop_sel'); ?></option>
                <?php
										if($comDetail->subscriber_relationship == ''){
												$subscriber_relationship = 'self';
										}
										else{
												$subscriber_relationship = $comDetail->subscriber_relationship;
										}
										$selectOption = '';
										foreach($relats as $val){
												$subscriber_relationship = preg_replace('/Doughter/','Daughter',$subscriber_relationship);
												if(strtolower($val) == strtolower($subscriber_relationship)){
														$sel = 'selected="selected"';
												}
												else{
														$sel = '';
												}
												$selectOption .= '<option value="'.$val.'" '.$sel.'>'.ucfirst($val).'</option>';
										}
										echo $selectOption;
								?>
            	</select>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <label for=" ">S.S</label>
            <?php
				if($comDetail->subscriber_ss == '' && ($comDetail->subscriber_relationship == '' || $comDetail->subscriber_relationship == 'self')){
					$subscriber_ss = $patientDetail->ss;
				}
				else{
					$subscriber_ss = $comDetail->subscriber_ss;
				}
				$updateSubDataArr[$comDetail->id]["subscriber_ss"] = $subscriber_ss;
			?>
              <input type="text" size="20" name="i<?php echo $i_key;?>subscriber_ss" id="i<?php echo $i_key;?>subscriber_ss" value="<?php echo stripslashes($subscriber_ss); ?>" class="form-control" onKeyPress="save_data(event);" onKeyUp="top.chk_change_in_form('<?php echo addslashes($subscriber_ss);?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($subscriber_ss);?>',this.value,event);"  onBlur="lost_focus(this,'form-control');validate_ssn(this);" onFocus="get_focus_obj(this);">
            </div>
          </div>
          <div class="<?php echo $dob_class;?>">
            <div class="form-group">
              <label for="i1subscriber_DOB">DOB</label>
              <input type="hidden" name="from_date_subscriber<?php echo $i_key;?>" id="from_date_subscriber<?php echo $i_key;?>" value="<?php echo get_date_format(date("Y-m-d"));?>" >
              <?php
									$sub_dob = get_date_format($subscriber_DOB);
									$updateSubDataArr[$comDetail->id]["subscriber_DOB"] = $sub_dob;
							?>
              <div class="input-group">
                <input type="text" onBlur="top.checkdate(this); lost_focus(this,'form-control'); do_date_check(this.form.from_date_subscriber<?php echo $i_key;?>, this.form.i<?php echo $i_key;?>subscriber_DOB);" onChange="do_date_check(this.form.from_date_subscriber<?php echo $i_key;?>, this.form.i<?php echo $i_key;?>subscriber_DOB); " maxlength="10" name='i<?php echo $i_key;?>subscriber_DOB' id="i<?php echo $i_key;?>subscriber_DOB" value='<?php echo $subscriber_DOB; ?>' title='<?php echo inter_date_format();?>' class="datepicker form-control" onKeyUp="top.chk_change_in_form('<?php echo addslashes($subscriber_DOB);?>',this,'InsTabDb',event); " onFocus="get_focus_obj(this);"/>
                <label class="input-group-addon btn" for="i<?php echo $i_key;?>subscriber_DOB">
                	<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
               	</label>
              </div>
              
              
            </div>
          </div>
          
          <div class="<?php echo $gender_class;?>">
            <div class="form-group">
              <label for="i<?php echo $i_key;?>subscriber_sex">Gender</label>
              <?php
								
									if($patientDetail->sex != '' && $patientDetail->sex != $comDetail->subscriber_sex && ($comDetail->subscriber_relationship == '' || $comDetail->subscriber_relationship == 'self' || $comDetail->subscriber_relationship == 'Self')){
											$subscriber_sex = ucfirst($patientDetail->sex);
											$subscriber_sex_class = "";
									}
									else{
											$subscriber_sex = ucfirst($comDetail->subscriber_sex);
											$subscriber_sex_class = ucfirst($comDetail->subscriber_sex);
									}
									$updateSubDataArr[$comDetail->id]["subscriber_sex"] = $subscriber_sex;
							?>
              <select name="i<?php echo $i_key;?>subscriber_sex" id="i<?php echo $i_key;?>subscriber_sex" data-width="100%" onChange="top.chk_change_in_form('',this,'InsTabDb',event); changeClassCombo(this,'form-control minimal');"  class="form-control minimal" title="Select" >
								<option value="" selected>&nbsp;</option>
              	<option value="Male" <?php if($subscriber_sex == 'Male') echo 'selected="selected"'; ?> >Male</option>
                <option value="Female" <?php if($subscriber_sex == 'Female') echo 'selected="selected"'; ?> >Female</option>
             	</select>
              
            </div>
          </div>
          <?php if($i_key == '1') { ?>
          <div class="col-sm-5">
            <div class="form-group">
              <label for="i<?php echo $i_key;?>accept_assignment">Accept Assignment</label>
              <select id="i<?php echo $i_key;?>accept_assignment" name="i<?php echo $i_key;?>accept_assignment" class="form-control minimal" data-width="100%" disabled="disabled">
              	<option value="0">Accept Assignment</option>
                <option value="1">NAA - Courtesy Billing</option>
                <option value="2">NAA - No Courtesy Billing</option>
             	</select>
            </div>
          </div>
          <?php } ?>
          <div class="col-sm-12">
            <div class="form-group">
              <label for="i<?php echo $i_key;?>comments">Comments</label>
              <textarea rows="1" class="form-control" name="i<?php echo $i_key;?>comments" id="i<?php echo $i_key;?>comments" placeholder="Comments..." ><?php echo stripslashes($comDetail->comments);?></textarea>
            </div>
          </div>
          <div class="clearfix">&nbsp;</div>
          <div class="col-sm-12">
          	
          	<div class="checkbox checkbox-inline ">
            	<input name="i<?php echo $i_key;?>paymentauth" id="i<?php echo $i_key;?>paymentauth" type="checkbox" checked />
              <label for="i<?php echo $i_key;?>paymentauth">Pymt. Auth</label>
            </div>
          	
            <div class="checkbox checkbox-inline ">
            	<input name="i<?php echo $i_key;?>signonfile" id="i<?php echo $i_key;?>signonfile" type="checkbox" checked />
              <label for="i<?php echo $i_key;?>signonfile">Sign. on File</label>
            </div>
         	
          </div>
          
          
        </div>
        <div class="clearfix"></div>
      </div>
    </div>
  </div>
  
  
  <div class="col-lg-4 col-md-12 col-sm-12">
    <div class="table_grid" id="Contacts<?php echo $i_key; ?>_table">
      <div class="insurancehd head">
        <span>Policy holder contact info</span>
      </div>
      <div class=" plr5">
        <div class="clearfix"></div>
        <div class="row">
          <div class="col-sm-12">
            <div class="form-group">
              <label for=" ">Street 1</label>
              <?php 
							
								if($comDetail->subscriber_street == '' && ($comDetail->subscriber_relationship == '' || $comDetail->subscriber_relationship == 'self')){
										$subscriber_street = ucwords($patientDetail->street);
								}
								else{
										$subscriber_street = ucwords($comDetail->subscriber_street);
								}
								$updateSubDataArr[$comDetail->id]["subscriber_street"] = $subscriber_street;
							?>
              <input type="text" class="form-control" size="50" name="i<?php echo $i_key; ?>subscriber_street" id="i<?php echo $i_key; ?>subscriber_street" value="<?php echo trim(stripslashes($subscriber_street)); ?>" onKeyUp="top.chk_change_in_form('<?php echo addslashes($subscriber_street); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($subscriber_street); ?>',this.value,event);" onBlur="lost_focus(this,'form-control');" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);"/>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="form-group">
              <label for=" ">Street 2</label>
              <?php 
								$subscriber_street ="";
								if($comDetail->subscriber_street_2 == '' && ($comDetail->subscriber_relationship == '' || $comDetail->subscriber_relationship == 'self')){
										$subscriber_street = ucwords($patientDetail->street2);
								}
								else{
										$subscriber_street = ucwords($comDetail->subscriber_street_2);
								}
								
								$updateSubDataArr[$comDetail->id]["subscriber_street_2"] = $subscriber_street;
							?>
              <input type="text" class="form-control" size="50" name="i<?php echo $i_key; ?>subscriber_street_2" id="i<?php echo $i_key; ?>subscriber_street_2" value="<?php echo trim(stripslashes($subscriber_street)); ?>" onKeyUp="top.chk_change_in_form('<?php echo addslashes($subscriber_street); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($subscriber_street); ?>',this.value,event);" onBlur="lost_focus(this,'form-control');" onKeyPress="save_data(event);" />
            </div>
          </div>
          <div class="col-sm-4 form-inline">
            <div class="form-group">
              <label for="code1"><?php getZipPostalLabel(); ?></label>
               <div class="clearfix"></div>
              <?php 
								if($comDetail->subscriber_postal_code == '' && ($comDetail->subscriber_relationship == '' || $comDetail->subscriber_relationship == 'self')){
										$postal_code = $patientDetail->postal_code;
								}
								else{
										$postal_code = $comDetail->subscriber_postal_code;
								}
								if($comDetail->zip_ext == '' && ($comDetail->subscriber_relationship == '' || $comDetail->subscriber_relationship == 'self')){
										$com_zip_ext = $patientDetail->zip_ext;
								}
								else{
										$com_zip_ext = $comDetail->zip_ext;
								}
								$updateSubDataArr[$comDetail->id]["subscriber_postal_code"] = $postal_code;
							?>
              <input type="text" id="code<?php echo $i_key; ?>" onBlur="zip_vs_state(this.value,'<?php echo $i_type; ?>'); lost_focus(this,'form-control');" name="i<?php echo $i_key; ?>subscriber_postal_code" value="<?php echo stripslashes($postal_code); ?>" class="form-control" onKeyUp="top.chk_change_in_form('<?php echo addslashes($postal_code); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($postal_code); ?>',this.value,event);validate_zip(this);" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);" <?php if(inter_zip_ext()){?>style="width:45%"<?php }?> maxlength="<?php echo inter_zip_length();?>" size="<?php echo inter_zip_length();?>">
              <?php if(inter_zip_ext()){?>
              -
              <input style="width:40%;" type="text" size="8" id="zip_ext<?php echo $i_key; ?>" name="i<?php echo $i_key; ?>subscriber_zip_ext" value="<?php echo stripslashes($com_zip_ext); ?>" class="form-control" onKeyUp="top.chk_change_in_form('<?php echo addslashes($com_zip_ext); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($com_zip_ext); ?>',this.value,event);" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);" />
              <?php }?>
            </div>
          </div>
          
          <div class="col-sm-4">
            <div class="form-group">
              <label for=" ">City</label>
              <?php 
									if($comDetail->subscriber_city == '' && ($comDetail->subscriber_relationship == '' || $comDetail->subscriber_relationship == 'self')){
											$subscriber_city = ucfirst($patientDetail->city);
									}
									else{
											$subscriber_city = ucfirst($comDetail->subscriber_city);
									}
									$updateSubDataArr[$comDetail->id]["subscriber_city"] = $subscriber_city;
							?>
              <input type="text" name="i<?php echo $i_key; ?>subscriber_city" id="city<?php echo $i_key; ?>" value="<?php echo $subscriber_city;?>" class="form-control" onKeyUp="top.chk_change_in_form('<?php echo addslashes($subscriber_city); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($postal_code); ?>',this.value,event);" onBlur="lost_focus(this,'form-control');" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);">
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              <label for=" "><?php echo ucwords(inter_state_label());?></label>
              <?php 
									if($comDetail->subscriber_state == '' && ($comDetail->subscriber_relationship == '' || $comDetail->subscriber_relationship == 'self')){
											$subscriber_state = ucfirst($patientDetail->state);
									}
									else{
											$subscriber_state = ucfirst($comDetail->subscriber_state);
									}
									$updateSubDataArr[$comDetail->id]["subscriber_state"] = $subscriber_state;
							?>
              <input type="text" size="13" name="i<?php echo $i_key; ?>subscriber_state" id="state<?php echo $i_key; ?>" value="<?php echo  $subscriber_state; ?>" class="form-control" onKeyUp="top.chk_change_in_form('<?php echo addslashes($subscriber_state); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($subscriber_state); ?>',this.value,event);" onBlur="lost_focus(this,'form-control');" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);"/>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              <label for=" ">Home Tel</label>
              <?php 
								if($comDetail->subscriber_phone == '' && ($comDetail->subscriber_relationship == '' || $comDetail->subscriber_relationship == 'self')){
										$subscriber_phone = ucfirst($patientDetail->phone_home);
								}
								else{
										$subscriber_phone = ucfirst($comDetail->subscriber_phone);
								}
								$updateSubDataArr[$comDetail->id]["subscriber_phone"] = $subscriber_phone;
								$subscriber_phone = core_phone_format($subscriber_phone);
							?>
              <input type="text" maxlength="<?php echo inter_phone_length(); ?>" onChange="set_phone_format(this,'<?php echo $GLOBALS['phone_format'] ?>','','','form-control');"  name="i<?php echo $i_key; ?>subscriber_phone" id="i<?php echo $i_key; ?>subscriber_phone" class="form-control" value="<?php echo stripslashes($subscriber_phone); ?>" onKeyUp="top.chk_change_in_form('<?php echo addslashes($subscriber_phone); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($subscriber_phone); ?>',this.value,event);" onBlur="lost_focus(this,'form-control');" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);">
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              <label for=" ">Work Tel</label>
							<div class="clearfix"></div>
              <?php 
								if($comDetail->subscriber_biz_phone == '' && ($comDetail->subscriber_relationship == '' || $comDetail->subscriber_relationship == 'self')){
										$subscriber_biz_phone = ucfirst($patientDetail->phone_biz);
								}
								else{
										$subscriber_biz_phone = ucfirst($comDetail->subscriber_biz_phone);
								}
								
							if($comDetail->subscriber_biz_phone_ext == '' && ($comDetail->subscriber_relationship == '' || $comDetail->subscriber_relationship == 'self')){
										$subscriber_biz_phone_ext = ucfirst($patientDetail->phone_biz_ext);
								}
								else{
										$subscriber_biz_phone_ext = ucfirst($comDetail->subscriber_biz_phone_ext);
								}
							
								$updateSubDataArr[$comDetail->id]["subscriber_biz_phone"] = $subscriber_biz_phone;
								$updateSubDataArr[$comDetail->id]["subscriber_biz_phone"] = $subscriber_biz_phone_ext;
								$subscriber_biz_phone = core_phone_format($subscriber_biz_phone);
							?>
              <input type="text" maxlength="<?php echo inter_phone_length(); ?>" onChange="set_phone_format(this,'<?php echo $GLOBALS['phone_format'] ?>','','','form-control');"  name="i<?php echo $i_key; ?>subscriber_biz_phone" id="i<?php echo $i_key; ?>subscriber_biz_phone" class="form-control" value="<?php echo stripslashes($subscriber_biz_phone); ?>" onKeyUp="top.chk_change_in_form('<?php echo addslashes($subscriber_biz_phone); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($subscriber_biz_phone); ?>',this.value,event);" onBlur="lost_focus(this,'form-control');" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);" style="width:70%;float:left;" />
              
              <input type="text" name="i<?php echo $i_key; ?>subscriber_biz_phone_ext" id="i<?php echo $i_key; ?>subscriber_biz_phone_ext" class="form-control" value="<?php echo stripslashes($subscriber_biz_phone_ext); ?>" onKeyUp="top.chk_change_in_form('<?php echo addslashes($subscriber_biz_phone_ext); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($subscriber_biz_phone_ext); ?>',this.value,event);" onBlur="lost_focus(this,'form-control');" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);" style="width:28%; float:right;" />
              
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              <label for=" ">Mobile</label>
              <?php 
									if($comDetail->subscriber_mobile == '' && ($comDetail->subscriber_relationship == '' || $comDetail->subscriber_relationship == 'self')){
											$subscriber_mobile = ucfirst($patientDetail->phone_cell);
									}
									else{
											$subscriber_mobile = ucfirst($comDetail->subscriber_mobile);
									}
									$updateSubDataArr[$comDetail->id]["subscriber_mobile"] = $subscriber_mobile;
									$subscriber_mobile = core_phone_format($subscriber_mobile);	
							?>
              <input type="text" maxlength="<?php echo inter_phone_length(); ?>" onChange="set_phone_format(this,'<?php echo $GLOBALS['phone_format'] ?>','','','form-control');" name="i<?php echo $i_key; ?>subscriber_mobile" id="i<?php echo $i_key; ?>subscriber_mobile" class="form-control" value="<?php echo stripslashes($subscriber_mobile); ?>" onKeyUp="top.chk_change_in_form('<?php echo addslashes($subscriber_mobile); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($subscriber_mobile); ?>',this.value,event);" onBlur="lost_focus(this,'form-control');" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);">
            </div>
          </div>
        </div>
				<div class="clearfix"></div>
      </div>
    </div>
  </div>
  
</div>
<input name="newInsComp<?php echo $i_key;?>" id="newInsComp<?php echo $i_key;?>" type="hidden" value="<?php echo $newComp; ?>">

<!-- Start Refferal Div -->
<div class="row margin-top-20 <?php echo ($comFlag ? '' : 'hidden');?>" id="sub_<?php echo $i_key;?>">
  	<?php 
			
			if($$inactive_ins_comp)
			{
      	$query = "SELECT pat_ref.*,
										TRIM(CONCAT(reff.LastName,', ',reff.FirstName,' ',reff.MiddleName,if(reff.MiddleName!='',' ',''),reff.Title)) as refphy
										FROM `patient_reff` pat_ref
										LEFT JOIN refferphysician reff 
										ON pat_ref.reff_phy_id = reff.physician_Reffer_id 
										WHERE pat_ref.reff_type = '".$i_key."' 
										AND (
													(pat_ref.end_date >= current_date() || pat_ref.end_date = '0000-00-00') 
													AND (
																(pat_ref.no_of_reffs = 0 AND (pat_ref.reff_used = '' OR pat_ref.reff_used = '0')
															)
																OR pat_ref.no_of_reffs > 0
															)
												) 
										AND pat_ref.ins_data_id = '".$$inactive_ins_comp."' and del_status = 0 
										order by pat_ref.reff_id desc";
				$sql = imw_query($query);
				$total_referral = imw_num_rows($sql) + 1;
			}
			else
			{ 
				$sql = '';
				$total_referral = 1;
			}
			$comRefImage = '';
			
			
			if($total_referral > 0)
			{
				$query1 = "SELECT upload_file_name FROM upload_lab_rad_data 
															WHERE uplaod_primary_id IN (
																SELECT reff_id FROM `patient_reff` WHERE reff_type = '".$i_key."' 
																	AND (
																				(end_date >= current_date() || end_date = '0000-00-00') 
																				AND (
																							(no_of_reffs = 0 AND (reff_used = '' OR reff_used = '0'))
																							OR 	no_of_reffs > 0
																						)
																			) 
																	AND ins_data_id = '".$$inactive_ins_comp."' and del_status = 0 
																	order by reff_id desc	
																)
																AND upload_file_name != ''
																AND upload_status = 0 ";
				$sql1 = imw_query($query1);
				
				while($row1 = imw_fetch_assoc($sql1))
				{
						$comRefImgPath = realpath($row1['upload_file_name']);
						if(file_exists($comRefImgPath) && is_dir($comRefImgPath) == '')
						{
							$comRefImageSize = getimagesize($comRefImgPath);
							$arrPathInfo = pathinfo($comRefImgPath);
							if($arrPathInfo['extension'] == "pdf"){
								$comRefImage .= '&nbsp;&nbsp;<a href="'.$row1['upload_file_name'].'" target="new"><img src="'.$library_path.'/images/pdfimg.png" title="'.ucfirst($i_type).' Scanned Document" width="21" height="25" style="margin-top:auto; margin-bottom:auto;"></a>';
							}else{
								if($comRefImageSize[0]>20){
									$newSize = newImageResize($comRefImgPath,21);
									$comRefImage .= '&nbsp;&nbsp;<img onClick="full_view(\''.$row1['upload_file_name'].'\',this)" src="'.$row1['upload_file_name'].'" title="'.ucfirst($i_type).' Scanned Document" '.$newSize.' style="margin-top:auto; margin-bottom:auto;">';
								}
								else{
									$comRefImage .= '&nbsp;&nbsp;<img onClick="full_view(\''.$row1['upload_file_name'].'\',this)" src="'.$row1['upload_file_name'].'" title="'.ucfirst($i_type).' Scanned Document" style="margin-top:auto; margin-bottom:auto;">';
								}
							}
						}
					}
			}
		?>
    <div class="adminbox no-border col-sm-12">
		<div class="row">
			<div class="col-sm-12 head">
				<div class="row">
					<div class="col-sm-10">
						<span>Referral <?php echo("Case&nbsp;[".get_insurance_case_name($_SESSION["currentCaseid"])."]"); ?></span>
					</div>
					<div class="col-sm-2 text-right">   
					  <span class="btn btn-sm btn-success " title="Add Referral" data-rows="<?php echo $total_referral; ?>" onclick="addRef(this,'<?php echo $i_type; ?>','<?php echo $i_key; ?>','<?php echo intval($comDetail->id);?>');"><i class="glyphicon glyphicon-plus"></i></span>	 
					  <span class="btn btn-sm btn-primary" title="Expired Referrals" onClick="show_expired_ref('<?php echo $i_type; ?>','<?php echo $$inactive_ins_comp; ?>','<?php echo $comDetail->provider; ?>');"><i class="glyphicon glyphicon-exclamation-sign"></i></span>	
						
					</div>	
				</div>	
			</div>
      <div class="clearfix"></div>
      
      <div class="tblBg" id="<?php echo $i_type; ?>ReffCont" style="height:185px; overflow:hidden; overflow-y:auto;">
      		
      		<?php
						$request_iterator = '';	
						$while_pri_flag = 1;
						$ref_loop_count = 0;
						$arrAuditTrailPriRef = array();
						while($while_pri_flag == 1)
						{
							$referral_row = imw_fetch_assoc($sql);
							
							$cur_reff_id = $referral_row['reff_id'];
							$cur_reff_phy_id = $referral_row['reff_phy_id'];
							$cur_reff_date = $referral_row['reff_date'];
							$cur_effective_date = $referral_row['effective_date'];
							$cur_end_date = $referral_row['end_date'];
							$cur_no_of_reffs = $referral_row['no_of_reffs'];
							$cur_reff_used = $referral_row['reff_used'];
							$cur_reffral_no = $referral_row['reffral_no'];
							$cur_note = $referral_row['note'];
							
							$ref_loop_count++;
							$target_img = '<img onclick="del_reff_ins_act(this,'.$cur_reff_id.');" class="pointer" src="'.$library_path.'/images/close1.png" alt="Delete Referral" title="Delete Referral" width="24" />';
							if($ref_loop_count == $total_referral)
							{
								$while_pri_flag = 0;
								$target_img = '';						
							}					
							
							if($cur_reff_phy_id){
									$cur_reff_by = $OBJCommonFunction->get_ref_phy_name($cur_reff_phy_id);
							}
							else
							{
								//getting default patient ref phy.
								$query = "SELECT primary_care_id,primary_care_phy_id FROM patient_data WHERE id = '".$patient_id."'";
								$rsData = imw_query($query);
								$arrData = imw_fetch_assoc($rsData);
								$intRefPhyId = "";
								$intRefPhyId = $arrData['primary_care_id'];
								$ins_care_phy_id = $arrData['primary_care_phy_id'];
								if(($intRefPhyId>0 || $ins_care_phy_id>0) && $total_referral == 1){
									if($ins_care_phy_id>0){
										$cur_reff_phy_id = $ins_care_phy_id;
									}else{
										$cur_reff_phy_id = $intRefPhyId;
									}
									$cur_reff_by = $OBJCommonFunction->get_ref_phy_name($cur_reff_phy_id);
								}  
								else{
									$cur_reff_by = '';
									$cur_reff_phy_id = '';
								}
								 
							}                                           
			
							if($cur_reff_date == '0000-00-00' || $cur_reff_date == ''){
								$cur_reff_date = '';
							}else{
								$cur_reff_date = get_date_format($cur_reff_date);
							}
			
							if($cur_effective_date == '0000-00-00' || $cur_effective_date == ''){
								$cur_effective_date = '';
							}else{
								$cur_effective_date = get_date_format($cur_effective_date);
							}
			
							if($cur_end_date == '0000-00-00' || $cur_end_date == ''){
								$cur_end_date = '';
							}else{
								$cur_end_date = get_date_format($cur_end_date);
							}
				?>
        			
             	<div class="col-sm-12 table_grid margin-top-5" id="<?php echo $i_type.'_refferal_'.$ref_loop_count;?>">	 	
                <div class="row">
                
                  <div class="col-sm-12 margin-top-5">
                    <div class="row">
                      <div class="col-sm-7">
                        <label class="sub-heading">Referral</label>
                      </div>
                      <div class="col-sm-5 text-right">
                        <?php echo $target_img; ?>
                        <a href="javascript:foo();" class="btn btn-success btn-xs" id="scanner_image_<?php echo $s_name;?>_<?php echo $ref_loop_count;?>" onClick="openScanDocument('<?php echo $cur_reff_id; ?>','<?php echo $i_type;?>_reff','<?php echo intval($comDetail->id);?>');">
                          <img src="<?php echo $library_path; ?>/images/scanner.png" alt="Referral scan document" width="20"/>
                        </a>
                      </div>
                    </div>
                  </div>
                
                  <div class="clearfix"></div>
                   
                  <div class="col-sm-12">
                    <?php $_SESSION['ref_id_pri'] = $cur_reff_id; ?>
                    <input type="hidden" name="ref<?php echo $i_key;?>_phyId[]" id="ref<?php echo $i_key;?>_phyId<?php echo $ref_loop_count; ?>" value="<?php echo $cur_reff_phy_id; ?>" />
                    <input type="hidden" name="ref_id_<?php echo $s_name;?>[]" id="ref_id_<?php echo $s_name;?><?php echo $ref_loop_count; ?>" value="<?php echo $cur_reff_id; ?>" />
                  
                    <div class="row">
                    
                      <div class="col-sm-3">
                        <label>Ref. Physician</label><br>
                        <?php
                            $strRefPhy = "";
                            $strRefPhy = trim(stripslashes($cur_reff_by));
                        ?>
                        <div class="input-group">
                        	<input type="text" name="ref<?php echo $i_key;?>_phy[]" id="ref<?php echo $i_key;?>_phy<?php echo $ref_loop_count; ?>"  value="<?php echo trim(stripslashes($strRefPhy)); ?>" class="form-control" data-search-by="" data-action="search_physician" data-text-box="ref<?php echo $i_key;?>_phy<?php echo $ref_loop_count; ?>" data-id-box="ref<?php echo $i_key;?>_phyId<?php echo $ref_loop_count; ?>" size="25" onKeyUp="top.loadPhysicians(this,'ref<?php echo $i_key;?>_phyId<?php echo $ref_loop_count; ?>'); top.chk_change_in_form('<?php echo addslashes($strRefPhy); ?>',this,'InsTabDb',event); chk_change('<?php echo trim(addslashes($cur_reff_by)); ?>',this.value,event);" onBlur="lost_focus(this,'form-control');" onKeyPress="javascript: document.getElementById('ref<?php echo $i_key;?>_phyId<?php echo $ref_loop_count; ?>').value = ''; chk_change('<?php echo trim(addslashes($cur_reff_by)); ?>',this.value,event); save_data(event);" onFocus="top.loadPhysicians(this,'ref<?php echo $i_key;?>_phyId<?php echo $ref_loop_count; ?>'); get_focus_obj(this);">
                          <label class="input-group-addon btn search_physician" data-source="ref<?php echo $i_key;?>_phy<?php echo $ref_loop_count; ?>"><i class="glyphicon glyphicon-search"></i></label>
                        </div>   
                        
                      </div>
                      
                      <div class="col-sm-3">
                        <label>Start Date</label><br>
                        <div class="input-group">
                          <input class="datepicker reff_start_date_cl form-control" type="text" name="eff<?php echo $i_key;?>_date[]" id="eff<?php echo $i_key;?>_date<?php echo $ref_loop_count; ?>" value="<?php echo stripslashes($cur_effective_date); ?>" size="11" onBlur="top.checkdate(this); lost_focus(this,'form-control');"  maxlength="10" onKeyUp="top.chk_change_in_form('<?php echo addslashes($cur_effective_date); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($cur_effective_date); ?>',this.value,event);" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);">
                          <label for="eff<?php echo $i_key;?>_date<?php echo $ref_loop_count; ?>" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                        </div>  
                      </div>
                      
                      <div class="col-sm-3">
                        <label>End Date</label><br>
                        <div class="input-group">
                          <input type="text" class="datepicker reff_end_date_cl form-control" name="end<?php echo $i_key;?>_date[]" id="end<?php echo $i_key;?>_date<?php echo $ref_loop_count; ?>" value="<?php echo stripslashes($cur_end_date); ?>" size="11" onBlur="top.checkdate(this); chkFuture('eff<?php echo $i_key;?>_date<?php echo $ref_loop_count; ?>',this); lost_focus(this,'form-control');" onChange="top.checkdate(this);"  maxlength="10" onKeyUp="top.chk_change_in_form('<?php echo addslashes($cur_end_date); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($cur_end_date); ?>',this.value,event);" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);" />
                          <label for="end<?php echo $i_key;?>_date<?php echo $ref_loop_count; ?>" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                        </div>
                      </div>
                      
                      <div class="col-sm-3">
                        <label>Visits</label><br>
                        <?php
                          if($cur_no_of_reffs + $cur_reff_used == '0' && $InsPriRefVisits==1){
                            $class="form-control mandatory-chk mandatory";
                          } 
                          else{
                            $class="form-control";
                          }
                          if($cur_no_of_reffs + $cur_reff_used=='0'){
                            $value="";
                          }
                          else{
                            $value = $cur_no_of_reffs + $cur_reff_used .'/'.$cur_reff_used;
                          }
                        ?>
                        <input type="hidden" name="<?php echo $s_name; ?>NoRef[]" id="<?php echo $s_name; ?>NoRef<?php echo $ref_loop_count; ?>" value="<?php echo $cur_no_of_reffs; ?>"/>
                        <input type="hidden" name="<?php echo $s_name; ?>UsedRef[]" id="<?php echo $s_name; ?>UsedRef<?php echo $ref_loop_count; ?>" value="<?php echo $cur_reff_used; ?>"/>
                        <input type="text"  name="no_ref<?php echo $i_key;?>[]" id="no_ref<?php echo $i_key;?><?php echo $ref_loop_count; ?>" value="<?php echo stripslashes($value); ?>" size="3" class="form-control" onKeyUp="top.chk_change_in_form('<?php echo addslashes($value); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($value); ?>',this.value,event);" onBlur="lost_focus(this,'form-control');" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);">
                      </div>
                      
                    </div>
                  </div>
                  
                  <div class="clearfix"></div>
                
                  <div class="col-sm-12">
                    <div class="row">
                      
                      <div class="col-sm-3">
                        <label>Referral#</label><br>
                        <input type="text" name="reffral_no<?php echo $i_key;?>[]" id="reffral_no<?php echo $i_key;?><?php echo $ref_loop_count; ?>" value="<?php echo stripslashes($cur_reffral_no); ?>" size="11" class="form-control " onKeyUp="top.chk_change_in_form('<?php echo addslashes($cur_reffral_no); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($cur_reffral_no); ?>',this.value,event);" onBlur="lost_focus(this,'form-control');" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);">
                      </div>
                      
                      <div class="col-sm-3">
                        <label>Ref. Date</label><br>
                        <div class="input-group">
                          <input type="text" name="reff<?php echo $i_key;?>_date[]" id="reff<?php echo $i_key;?>_date<?php echo $ref_loop_count; ?>" value="<?php echo stripslashes($cur_reff_date); ?>" size="11" onBlur="top.checkdate(this); lost_focus(this,'form-control');"  maxlength="10" class="form-control datepicker" onKeyUp="top.chk_change_in_form('<?php echo addslashes($cur_reff_date); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($cur_reff_date); ?>',this.value,event);" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);">
                          <label for="reff<?php echo $i_key;?>_date<?php echo $ref_loop_count; ?>" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                        </div>
                      </div>
                      
                      <div class="col-sm-6">
                        <label>Notes</label><br>
                        <?php
                          $strNotesRef = "";
                          $strNotesRef = ucwords($cur_note);
                        ?>
                        <textarea style="height:34px;" name="note<?php echo $i_key;?>[]" id="note<?php echo $i_key;?><?php echo $ref_loop_count; ?>" cols="40" rows="1" class="form-control" onKeyUp="top.chk_change_in_form('<?php echo addslashes($strNotesRef); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($strNotesRef); ?>',this.value,event);" onKeyPress="save_data(event);" onBlur="lost_focus(this,'form-control');" onFocus="get_focus_obj(this);"><?php echo stripslashes($strNotesRef); ?></textarea>
                      </div>
                      
                    </div>  
                  </div>
                
                </div>
                
            	</div>   
       	<?php 
							if($request_iterator == '')
							{
								$request_iterator = 2;			
							}
							else
							{
								$request_iterator++;	
							}
							
						} 
					?>
        
      </div>
      
		</div>
		</div>
</div>
<!-- End Refferal Div -->


<!-- Start Authorization based on Vision-->
<?php
	if( $i_key == 1 ) {
		$auth_sub_qry = "AND ((end_date >= current_date() || end_date = '0000-00-00') 
													AND ( (no_of_reffs = 0 AND (reff_used = '' OR reff_used = '0') ) OR no_of_reffs > 0 )
										)";
	}
	else {
		$auth_sub_qry = "AND ((end_date >= current_date() || end_date = '0000-00-00') 
													AND (reff_used < no_of_reffs OR (no_of_reffs = 0 AND reff_used = 0))
										)";
	}
		
	$query = "SELECT patient_auth.AuthAmount,patient_auth.a_id,
										patient_auth.auth_name,patient_auth.auth_date,
										patient_auth.auth_comment,patient_auth.auth_operator,patient_auth.end_date,
										patient_auth.no_of_reffs,patient_auth.reff_used,
										patient_auth.auth_provider,patient_auth.auth_cpt_codes,patient_auth.auth_cpt_codes_id,
										users.username,users.fname,users.lname,users.mname 
										FROM patient_auth 
										LEFT JOIN users on users.id = patient_auth.auth_operator
										WHERE patient_auth.patient_id='".$patient_id."' and patient_auth.ins_type='".$i_key."' 
										AND patient_auth.ins_case_id = '".$ins_caseid."'
										AND patient_auth.auth_status = '0' and auth_status='0' 
										".$auth_sub_qry."
										ORDER BY a_id desc
							";
	$sql = imw_query($query);	
	$cnt = imw_num_rows($sql);
	$lastIndex	=	($cnt > 0) ? $cnt - 1 : 0;
	$loop_cnt = $cnt+1;
	$authQryRes = array();
	while($row = imw_fetch_assoc($sql))
	{
		array_push($authQryRes,$row);
	}
?>
<div class="row margin-top-10 <?php echo ($comFlag_auth ? '' : 'hidden'); ?>"id="sub_auth_<?php echo $i_key; ?>">
	<div class="adminbox no-border col-sm-12">
    	<div class="row">
			<div class="col-sm-12 head">
				<div class="row">
					<div class="col-sm-7">
						<span>Authorization <?php echo("Case&nbsp;[".get_insurance_case_name($_SESSION["currentCaseid"])."]")?></span>
					</div>
					<div class="col-sm-5 text-right">   
						<a class="btn btn-success btn-sm" title="Add More" id="<?php echo i_type.'_auth_add_more_btn';?>" data-rows="<?php echo $loop_cnt;?>" data-auth-user="<?php echo $_SESSION['authProviderName']; ?>" data-auth-user-id="<?php echo $_SESSION['authUserID']; ?>" data-auth-id="<?php echo $authQryRes[$lastIndex]['a_id'];?>" onclick="add_auth(this,'<?php echo $i_type; ?>','<?php echo $i_key; ?>');" >
						<i class="glyphicon glyphicon-plus"></i>
						</a>	
						<a class="btn btn-primary btn-sm" title="Authorization History" data-toggle="modal" data-target="#auth_<?php echo $s_name;?>_hx">
						<i class="glyphicon glyphicon-time"></i></a>	
					</div>
				</div>
			</div>
      <div class="clearfix"></div>
      
      <div class="tblBg" id="auth_<?php echo $i_key;?>_table">
      	<div id="parent_auth_main_table<?php echo $i_key;?>" style="height:185px; overflow:hidden; overflow-y:auto;"  >
        	<div id="auth_main_table<?php echo $i_key;?>">
            
          			<?php 
									$aid = 0;
									for($i=0, $j = 1; $i < $loop_cnt; $i++,$j++)
									{
										$auth_date 				= get_date_format($authQryRes[$i]['auth_date']);
										$auth_end_date 		= get_date_format($authQryRes[$i]['end_date']);
										$auth_no_of_reffs = $authQryRes[$i]['no_of_reffs'];
										$auth_reff_used   = $authQryRes[$i]['reff_used'];
										$cur_id	= $authQryRes[$i]['a_id'];
										if( $cur_id ) { $aid = $cur_id; }
										$auth_name = $authQryRes[$i]['auth_name'];
										$auth_provider = $authQryRes[$i]['auth_provider'];
										$auth_cpt_codes = $authQryRes[$i]['auth_cpt_codes'];
										$auth_cpt_id = $authQryRes[$i]['auth_cpt_codes_id'];
										$auth_comment = $authQryRes[$i]['auth_comment'];
										$auth_operator_id = ucwords(trim($authQryRes[$i]['auth_operator']));
										if(empty($auth_operator_id) == true){
											$auth_operator_id = $_SESSION['authUserID'];
										}
										$phyName = $authQryRes[$i]['lname'].', ';
										$phyName .= $authQryRes[$i]['fname'].' ';
										$phyName .= $authQryRes[$i]['mname'];
										$auth_operator = $phyName;
										if(empty($authQryRes[$i]['auth_operator']) == true){
											$auth_operator = $_SESSION['authProviderName'];
										}	
										
										if($auth_no_of_reffs + $auth_reff_used=='0')
										{	
											$auth_visit_value="";
										}
										else{
											if($auth_reff_used >0)
												$auth_visit_value=$auth_no_of_reffs .'/'.$auth_reff_used;
											else
												$auth_visit_value=$auth_no_of_reffs;
										}
										
										$auth_del_target = '<img src="'.$library_path.'/images/close1.png" class="pointer" title="Delete Auth Information" onClick="delete_auth_info('.(int) $cur_id.','.(int) $j.',\''.$i_type.'\',false);" width="24" />&nbsp;';
										if($i == $cnt) $auth_del_target = '';
								?>
                
                <div class="table_grid row" id="<?php echo $i_type; ?>_auth_information_<?php echo $j; ?>" >
                
                  <div class="col-sm-12 margin-top-5 ">
                    <div class="row">
                      <div class="col-sm-10">
                        <label class="sub-heading">Authorization</label>
                      </div>
                      <div class="col-sm-2 text-right" id="<?php echo $i_type;?>AuthDelBtnDiv<?php echo $j;?>">
                      	<?php echo $auth_del_target; ?>	
                      </div>
                    </div>   
                  </div>
          
          				<div class="clearfix"></div>
                
                	<div class="col-sm-12">
                  
                    <input type="hidden" name="auth_cur_<?php echo $s_name;?>_<?php echo $j; ?>" id="auth_cur_<?php echo $s_name;?>_<?php echo $j; ?>" value="<?php echo $_SESSION['authUser']; ?>">
                    <input type="hidden" name="auth_id_<?php echo $s_name;?>_<?php echo $j; ?>" id="auth_id_<?php echo $s_name;?>_<?php echo $j; ?>" value="<?php echo $cur_id; ?>">
                    <input type="hidden" name="auth_user_<?php echo $s_name;?>_<?php echo $j; ?>" id="auth_user_<?php echo $s_name;?>_<?php echo $j; ?>" value="<?php echo $auth_operator_id; ?>">
                
                        
                    <div class="col-sm-12">
                      <div class="row">
                        
                        <div class="col-sm-2">
                          <label>
                            <?php if(in_array($cur_id,$auth_chl_arr)){?>
                            <span style="color:green;"><i class="glyphicon glyphicon-ok"></i></span>
                            <?php } ?>
                            Authorization#
                          </label><br>
                          <input type="text" class="form-control" name="auth_nam_<?php echo $s_name;?>_<?php echo $j; ?>" value="<?php echo $auth_name; ?>" id="auth_nam_<?php echo $s_name;?>_<?php echo $j; ?>" size="20" onKeyUp="top.chk_change_in_form('<?php echo addslashes($auth_name); ?>',this,'InsTabDb',event);">
                        </div>
                         
                        <div class="col-sm-2">
                          <label>Provider</label><br>
                          <select class="form-control minimal" name="auth_provider_<?php echo $s_name;?>_<?php echo $j; ?>" id="auth_provider_<?php echo $s_name;?>_<?php echo $j; ?>" onChange="top.chk_change_in_form('<?php echo addslashes($auth_provider); ?>',this,'InsTabDb',event);">
														<option value="" selected>Select Provider</option>
														<?php echo $OBJCommonFunction->drop_down_providers($auth_provider,'','1'); ?>
													</select>
                        </div>
                        
                        <div class="col-sm-4">
                        	<label>CPT Codes</label><br>
                        	<input type="text" class="form-control" name="auth_cpt_codes_<?php echo $s_name;?>_<?php echo $j; ?>" value="<?php echo $auth_cpt_codes; ?>" id="auth_cpt_codes_<?php echo $s_name;?>_<?php echo $j; ?>" data-sort="contain" data-provide="multiple" data-seperator="semicolon" data-container="body" onKeyUp="top.chk_change_in_form('<?php echo addslashes($auth_cpt_codes); ?>',this,'InsTabDb',event);">
                      	</div>
                      		          
                        <div class="col-sm-2">
                          <label for="auth_dat_<?php echo $s_name;?>_<?php echo $j; ?>">Date</label><br>
                          <div class="input-group">	
                            <input type="text" class="form-control datepicker" name="auth_dat_<?php echo $s_name;?>_<?php echo $j; ?>" value="<?php echo $auth_date; ?>" id="auth_dat_<?php echo $s_name;?>_<?php echo $j; ?>" size="11" title='mm-dd-yyyy' onBlur="top.checkdate(this);" onKeyUp="top.chk_change_in_form('<?php echo addslashes($auth_date); ?>',this,'InsTabDb',event);" >
                            <label for="auth_dat_<?php echo $s_name;?>_<?php echo $j; ?>" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                          </div>
                        </div>
                            
                        <div class="col-sm-2">
                          <label for="auth_end_dat_<?php echo $s_name;?>_<?php echo $j; ?>">End Date</label><br>
                          <div class="input-group">	
                            <input type="text" class="form-control datepicker" name="auth_end_dat_<?php echo $s_name;?>_<?php echo $j; ?>" value="<?php echo $auth_end_date; ?>" id="auth_end_dat_<?php echo $s_name;?>_<?php echo $j; ?>" size="11" title='mm-dd-yyyy' onBlur="top.checkdate(this);" onKeyUp="top.chk_change_in_form('<?php echo addslashes($auth_end_date); ?>',this,'InsTabDb',event);">
                            <label for="auth_end_dat_<?php echo $s_name;?>_<?php echo $j; ?>" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                          </div>	
                        </div>
                            
                        
                        
                      </div>
                    </div>
                      
                   	<div class="clearfix"></div>
                  			
                    <div class="col-sm-12">
                      <div class="row">
                        <div class="col-sm-2">
                        	<label>Visits</label><br>
                          <input type="text" class="form-control" name="auth_visit_value_<?php echo $s_name;?>_<?php echo $j; ?>" value="<?php echo $auth_visit_value; ?>" id="auth_visit_value_<?php echo $s_name;?>_<?php echo $j; ?>" size="11" onKeyUp="top.chk_change_in_form('<?php echo addslashes($auth_visit_value); ?>',this,'InsTabDb',event);">
                      	</div>
                        <div class="col-sm-2">
                          <label>Amount</label><br>
                          <?php $strAuthAmount = number_format($authQryRes[$i]['AuthAmount'],2); ?>
                          <input type="text" class="form-control" name="<?php echo $s_name;?>AuthAmount_<?php echo $j; ?>" value="<?php echo $strAuthAmount; ?>" onKeyUp="top.chk_change_in_form('<?php echo addslashes($strAuthAmount); ?>',this,'InsTabDb',event);" />
                        </div>
                        
                        <div class="col-sm-6">
                          <label>Comments</label><br>
                          <textarea name="auth_comment_<?php echo $s_name;?>_<?php echo $j; ?>" class="form-control" id="auth_comment_<?php echo $s_name;?>_<?php echo $j; ?>" cols="60" rows="1" onKeyUp="top.chk_change_in_form('<?php echo addslashes($auth_comment); ?>',this,'InsTabDb',event);" style="height:auto;"><?php echo $auth_comment; ?></textarea>
                        </div>
                        
                        <div class="col-sm-2">
                          <label>Operator</label><br>
                          <input type="text" class="form-control" readonly name="auth_oper_<?php echo $s_name;?>_<?php echo $j; ?>" id="auth_oper_<?php echo $s_name;?>_<?php echo $j; ?>" size="16" value="<?php echo $auth_operator; ?>">
                        </div>
                        
                      </div>  
                    </div>
                      
               		</div>
                  
                </div>
              	
								<?php
									}
								?>
                
            </div>
            <input type="hidden" name="last_auth_inf_cnt_<?php echo $s_name;?>" id="last_auth_inf_cnt_<?php echo $s_name;?>" value="<?php echo $i; ?>" />
          </div>
      </div>
      </div>
    </div>
</div>

<!-- End Authorization based on Vision-->


<!-- Auth Hx Modals -->
<div id="auth_<?php echo $s_name; ?>_hx" class="modal" role="dialog" >
	<div class="modal-dialog modal-lg" style="width:90%;">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" id="modal_title">Patient All Insurance History (<?php echo ucfirst($i_type);?>) </h4>
     	</div>
      
      <div class="modal-body">
      	<table class="table table-bordered table-hover table-striped scroll release-table">
        	<thead class="header">
          	<tr class="grythead">
            <td class="col-sm-1">Authorization#</td>
            <td class="col-sm-1">Provider</td>
            <td class="col-sm-2">CPT Codes</td>
            <td class="col-sm-1">Date</td>
            <td class="col-sm-1">End Date</td>
            <td class="col-sm-1">Visits</td>
            <td class="col-sm-1">Amount</td>
            <td class="col-sm-3">Comments</td>
            <td class="col-sm-1">Operator</td>
          	</tr>
         	</thead>
         	<tbody id="authHxBody<?php echo $i_type; ?>">
          	<?php
							$auth_hxs = $data_obj->insurance_auth_hx($i_type,$ins_caseid,$aid);
							
							$authHxSaveBtn = 'authSaveBtn'.$i_type;
							$$authHxSaveBtn = '';
							if(is_array($auth_hxs) && count($auth_hxs) > 0 )
							{
								$isEditableExists = false;
								foreach($auth_hxs as $auth_hx)
								{
									$strikeout = ($auth_hx['auth_line'] == 1) ? 'style="text-decoration:line-through;"' : '';
									$key = $auth_hx['a_id'];
									$authEndDate = '<input type="hidden" name="authId['.$key.']" id="authID_'.$key.'" value="'.$auth_hx['a_id'].'" /><div class="input-group"><input type="text" class="form-control datepicker" data-prev-value="'.$auth_hx['end_date'].'" name="authEndDate['.$key.']" id="authEndDate_'.$key.'" value="'.$auth_hx['end_date'].'" data-counter="'.$key.'" /><label for="authEndDate_'.$key.'" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label></div>';
									if( $auth_hx['auth_line'] ) $authEndDate = $auth_hx['end_date'];
									else $isEditableExists = true;
									
									echo '<tr '.$strikeout.'>';
									echo '<td>'.$auth_hx['auth_name'].'</td>';
									echo '<td>'.$auth_hx['auth_provider'].'</td>';
									echo '<td>'.$auth_hx['auth_cpt_codes'].'</td>';
									echo '<td>'.$auth_hx['auth_date'].'</td>';
									echo '<td>'.$authEndDate.'</td>';
									echo '<td>'.$auth_hx['auth_visit'].'</td>';
									echo '<td>'.$auth_hx['auth_amount'].'</td>';
									echo '<td>'.$auth_hx['auth_comment'].'</td>';
									echo '<td>'.$auth_hx['auth_operator'].'</td>';
									echo '</tr>';			
									
								}
								
								if( $isEditableExists ) $$authHxSaveBtn = '<button type="button" class="btn btn-success" onClick="saveAuthHxData(\''.$i_type.'\')">Save & Close</button>';
							}
							else
							{
								echo '<tr><td colspan="9">No record Found</td></tr>';	
							}
							
						?>
        	</tbody> 
      	</table>
     	  	
      </div>
      
      <div class="modal-footer">
      	<?php echo $$authHxSaveBtn; ?>
       	<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>
<!-- Auth Hx Modals -->