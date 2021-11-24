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


if($_POST['form_submitted']) {
	$reminder_choices = array_combine($reminder_choices, $reminder_choices);
}

//--- GET Groups SELECT BOX ----
$group_query = "Select  gro_id,name,del_status from groups_new order by name";
$group_query_res = imw_query($group_query);
$group_id_arr = array();
$groupName = "";
while ($group_res = imw_fetch_array($group_query_res)) {
	$sel='';
    $group_id = $group_res['gro_id'];
    $group_id_arr[$group_id] = $group_res['name'];
	if(in_array($group_id,$groups))$sel='SELECTED';

    $groupName .= '<option value="'.$group_res['gro_id'].'" '.$sel.'>' . $group_res['name'] . '</option>';
}


//--- GET FACILITY SELECT BOX ----
$fac_query = "select id,name from facility order by name";
$fac_query_res = imw_query($fac_query);
$fac_id_arr = array();
$facilityName = "";
while ($fac_res = imw_fetch_array($fac_query_res)) {
	$sel='';
    $fac_id = $fac_res['id'];
    $fac_id_arr[$fac_id] = $fac_res['name'];
	if(in_array($fac_id,$facility_name))$sel='SELECTED';

    $facilityName .= '<option value="'.$fac_res['id'].'" '.$sel.'>' . $fac_res['name'] . '</option>';
}

//--- GET POS FACILITY NAME ----
$pos_facilityName = $CLSReports->getFacilityName($facility_name, '1');


//--- GET ALL PHYSICIAN DETAILS ----
$strSelPhysician=implode(',',$phyId);
$physicianName = $CLSCommonFunction->drop_down_providers($strSelPhysician,'1','','','report');
$allPhyCount = sizeof(explode('</option>', $physicianName)) - 1;

//---- GET HEARED ABOUT US VALUES -------
$hrd_opn_qry = "SELECT DISTINCT heard_options,heard_id  FROM heard_about_us where status='0' ";
$hrd_opn_rs = imw_query($hrd_opn_qry);
$heard_about_opts = "";
$heard_opts_arr = array();
while ($heardDetails = imw_fetch_array($hrd_opn_rs)) {
	$sel='';
    $heardId = $heardDetails['heard_id'];
    $heard_opts_arr[$heardId] = substr($heardDetails['heard_options'], 0, 50);
	
	if(in_array($heardId,$heardAbtUs))$sel='SELECTED';
    $heard_about_opts .= '<option value="' . $heardId . '" '.$sel.' >' . $heardDetails['heard_options'] . '</option>';
}

//--- GET ALL OPERATORS DETAILS ----
$selOperId=implode(',',$operator_id);
$operatorOption = $CLSCommonFunction->dropDown_providers($selOperId, '', '');

//---- GET App type VALUES -------

$app_query = "SELECT acronym,group_concat(id) as id  FROM slot_procedures where active_status='yes' and acronym!='' group by acronym order by acronym";
$app_query_res = imw_query($app_query);
$app_id_arr = array();
$appName = "";
while ($app_res = imw_fetch_array($app_query_res)) {
	$sel='';
    $app_id = $app_res['id'];
    $app_id_arr[$app_id] = $app_res['acronym'];
	if(in_array($app_id,$app_type))$sel='SELECTED';
	$appName .= '<option value="'.$app_res['id'].'" '.$sel.'>' . $app_res['acronym'] . '</option>';
}
$MONTH_NAME_ARR=array(1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec');

//--- GET ALL LETTER CATEGORIES LIST ---
$pt_doc_cat_qry = imw_query("select cat_id, category_name from main_template_category where template_name = 'pt_docs' and delete_status = '0' order by category_name");
$pt_doc_cat_qry_res =array();
while ($qry_res = imw_fetch_array($pt_doc_cat_qry)) {
	$pt_doc_cat_qry_res[] = $qry_res;
}
for($i=0;$i<count($pt_doc_cat_qry_res);$i++){	
	$cat_id = $pt_doc_cat_qry_res[$i]['cat_id'];
	$category_name = $pt_doc_cat_qry_res[$i]['category_name'];
	// --- GET LETTERS FOR GIVEN CATEGORIES
	$catqry = imw_query("select pt_docs_template_id,pt_docs_template_name from pt_docs_template where pt_docs_template_status = '0' and pt_docs_template_name!='' and pt_docs_template_category_id='$cat_id' order by pt_docs_template_name");
	$letterDetails = array();
	while ($cat_res = imw_fetch_array($catqry)) {
		$letterDetails[] = $cat_res;
	}
	if(count($letterDetails)>0){
		$letterDropDown.='<optgroup label="'.$category_name.'">';
		for($j=0;$j<count($letterDetails);$j++){
			if(in_array($letterDetails[$j]['pt_docs_template_id'],$letterTempId))$sel='SELECTED';
			
			$letterDropDown.=' <option value="'.$letterDetails[$j]['pt_docs_template_id'].'">'.$letterDetails[$j]['pt_docs_template_name'].'</option>';
		}
		$letterDropDown.='</optgroup>';
	}
}

//SET INSURANCE COMPANY DROP DOWN
$insQryRes = insurance_provider_xml_extract();
$ins_comp_arr = array();
$insComName_options = '';
$sel_ins_comp_options = '';
for ($i = 0; $i < count($insQryRes); $i++) {
    if ($insQryRes[$i]['attributes']['insCompName'] != 'No Insurance') {
        $ins_id = $insQryRes[$i]['attributes']['insCompId'];
        $ins_name = $insQryRes[$i]['attributes']['insCompINHouseCode'];
        $ins_status = $insQryRes[$i]['attributes']['insCompStatus'];
        if ($ins_name == '') {
            $ins_name = $insQryRes[$i]['attributes']['insCompName'];
            if (strlen($ins_name) > 20) {
                $ins_name = substr($ins_name, 0, 20) . '....';
            }
        }
				
				$sel = '';
				if (sizeof($insuranceName) > 0) {
					if (in_array($ins_id,$insuranceName)) {
							$sel = 'selected';
					}
        }

        $ins_comp_arr[$ins_id] = $ins_name;
        if ($insQryRes[$i]['attributes']['insCompStatus'] == 0)
            $insComName_options .= "<option value='" . $ins_id . "' ".$sel.">" . $ins_name . "</option>";
        else
            $insComName_options .= "<option value='" . $ins_id . "' ".$sel." style='color:red'>" . $ins_name . "</option>";

        
    }
}
$insuranceName_cont = sizeof($ins_comp_arr);

//CPT CODES
$qry = "select cpt_prac_code,cpt_fee_id,status,delete_status,cpt_desc from cpt_fee_tbl WHERE cpt_prac_code != '' order by cpt_prac_code asc";
$res = imw_query($qry);
while ($row = imw_fetch_array($res)) {
    $color = $cp='';
    $cpt_fee_id = $row['cpt_fee_id'];
  
  $cp=$row['cpt_desc'];
	if(strpos($cp,'<')>0)
	{
		$cp=str_replace('<','&lt;',$cp);
	}
	if(strpos($cp,'>')>0)
	{
		$cp=str_replace('>','&gt;',$cp);
  }
  if(empty($cp)==false){
    $cp=$cp.' - ';
  }
  
	$cpt_prac_code = $row['cpt_prac_code'];
	$cptDetailsArr[$cpt_fee_id] = $cpt_prac_code;
	$sel = (in_array($cpt_fee_id ,$_REQUEST['cpt_code_id']) === true) ? 'selected' : '';
	$cpt_code_options .= "<option value='" . $cpt_fee_id . "' style='" . $color . "' ".$sel.">".$cpt_prac_code."</option>";
}
$cpt_for_code_cont = sizeof($cptDetailsArr);

//--- SET ICD10 CODE DROP DOWN ----
$all_dx10_code_options = '';
$arrICD10Code = $CLSReports->getICD10Codes();
foreach($arrICD10Code as $dx10code){
	$selected = (in_array($dx10code ,$_REQUEST['all_dx10']) === true) ? 'selected' : '';	
    $all_dx10_code_options .= "<option value='" . $dx10code . "' ".$selected.">" . $dx10code . "</option>";
}
$dx10_code_cont = sizeof($arrICD10Code);

//-----GETTING PROCEDURES----
$strProc = implode(',',$_REQUEST['rep_proc']);
$allProc = sr_get_scheduler_procedure_list();

//-----GETTING APPOINTMENT STATUS----
$appt_query = "SELECT id, status_name FROM schedule_status order by status_name";
$appt_query_res = imw_query($appt_query);
$appt_opts_arr = array();
$apptOption = "";
while ($apptDetails = imw_fetch_array($appt_query_res)) {
	$sel='';
    $apptId = $apptDetails['id'];
    $appt_opts_arr[$apptId] = $apptDetails['status_name'];
	if(in_array($apptId,$status_id))$sel='SELECTED';
	$apptOption .= '<option value="' . $apptDetails['id'] . '" '.$sel.'>' . $apptDetails['status_name'] . '</option>';
}
$datelabel = "Period";
if($dbtemp_name == "Heard about us"){
	$datelabel = "Appt Period";	
}
?>
<div class="reportlft" style="height:100%;">
  <div class="practbox">
    <div class="anatreport">
      <h2>Practice Filter</h2>
    </div>
    <div class="clearfix"></div>
    <div class="pd5" id="searchcriteria">
      <div class="row">
        <div class="col-sm-4">
          <label>Groups</label>
          <select name="groups[]" id="groups" class="selectpicker" <?php echo (!$is_parent && !isset($filter_arr['groups']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#common_drop">
            <?php echo $groupName; ?>
          </select>
        </div>
        <div class="col-sm-4">
          <label>Provider</label>
          <select name="phyId[]" id="phyId" class="selectpicker" <?php echo (!$is_parent && !isset($filter_arr['physician']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#common_drop">
            <?php echo $physicianName; ?>
          </select>
        </div>
        <div class="col-sm-4">
          <label>Facility</label>
          <select name="facility_name[]" id="facility_name" class="selectpicker" <?php echo (!$is_parent && !isset($filter_arr['facility']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#common_drop">
            <?php 
			if($dbtemp_name == "Heard about us"){
				echo $pos_facilityName; 
			}else{
				echo $facilityName; 
			}
			?>
          </select>
        </div>
		<?php if($dbtemp_name == "Heard about us"){ ?>
		<div class="col-sm-6">
			<label>Procedure</label>
            <select name="rep_proc[]" id="rep_proc" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#common_drop">
			<?php echo $allProc; ?>
			</select>
		</div>
		<div class="col-sm-6">
          <label>Appt. Status</label>
          <select name="status_id[]" id="status_id" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#common_drop">
            <?php echo $apptOption; ?>
          </select>
        </div>
		
		<?php } ?>
		
        <div class="col-sm-4">
          <label>Operator</label>
          <select name="operator_id[]" id="operator_id" class="selectpicker" <?php echo (!$is_parent && !isset($filter_arr['operators']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#common_drop">
            <?php echo $operatorOption; ?>
          </select>
        </div>
        <div class="col-sm-8">
        <label id="lbl_date_title"><?php echo $datelabel; ?></label>
          <div id="dateFieldControler">
            <select name="dayReport" id="dayReport" class="selectpicker" <?php echo (!$is_parent && !isset($filter_arr['date_range']))?'disabled':''; ?> data-width="100%" data-actions-box="false" onchange="DateOptions(this.value);">
              <option value="Daily" <?php if ($_POST['dayReport'] == 'Daily') echo 'SELECTED'; ?>>Daily</option>
              <option value="Weekly" <?php if ($_POST['dayReport'] == 'Weekly') echo 'SELECTED'; ?>>Weekly</option>
              <option value="Monthly" <?php if ($_POST['dayReport'] == 'Monthly') echo 'SELECTED'; ?>>Monthly</option>
              <option value="Quarterly" <?php if ($_POST['dayReport'] == 'Quarterly') echo 'SELECTED'; ?>>Quarterly</option>
              <option value="Date" <?php if ($_POST['dayReport'] == 'Date') echo 'SELECTED'; ?>>Date Range</option>
            </select>
          </div>
          <div class="row" style="display:none" id="dateFields">
            <div class="col-sm-5">
              <div class="input-group">
                <input type="text" name="Start_date" placeholder="From" style="font-size: 12px;" id="Start_date" value="<?php echo $_REQUEST['Start_date']; ?>" class="form-control date-pick">
                <label class="input-group-addon" for="Start_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
              </div>
            </div>
            <div class="col-sm-5">
              <div class="input-group">
                <input type="text" name="End_date" placeholder="To" style="font-size: 12px;" id="End_date" value="<?php echo $_REQUEST['End_date']; ?>" class="form-control date-pick">
                <label class="input-group-addon" for="End_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
              </div>
            </div>
            <div class="col-sm-2" id="div_back_arrow">
              <button type="button" class="btn" onclick="DateOptions('x');"><span class="glyphicon glyphicon-arrow-left"></span></button>
            </div>
          </div>
        </div>
		<?php if($dbtemp_name == "Heard about us"){ ?>
		<div class="col-sm-12">
          <label id="lbl_date_title">Revenue Period</label>
          <div id="dateFieldControler">
            <select name="dayReport2" id="dayReport2" class="selectpicker" data-width="100%" data-actions-box="false" onchange="DateOptions(this.value, this);">
              <option value="Daily" <?php if ($_POST['dayReport2'] == 'Daily') echo 'SELECTED'; ?>>Daily</option>
              <option value="Weekly" <?php if ($_POST['dayReport2'] == 'Weekly') echo 'SELECTED'; ?>>Weekly</option>
              <option value="Monthly" <?php if ($_POST['dayReport2'] == 'Monthly') echo 'SELECTED'; ?>>Monthly</option>
              <option value="Quarterly" <?php if ($_POST['dayReport2'] == 'Quarterly') echo 'SELECTED'; ?>>Quarterly</option>
              <option value="Date" <?php if ($_POST['dayReport2'] == 'Date') echo 'SELECTED'; ?>>Date Range</option>
            </select>
          </div>
          <div class="row" style="display:none" id="dateFields">
            <div class="col-sm-5"  id="div_start_date">
              <div class="input-group">
                <input type="text" name="revenue_from" placeholder="From" style="font-size: 12px;" id="revenue_from" value="<?php echo $_REQUEST['revenue_from']; ?>" class="form-control date-pick">
                <label class="input-group-addon" for="revenue_from"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
              </div>
            </div>
            <div class="col-sm-5">
              <div class="input-group">
                <input type="text" name="revenue_to" placeholder="To" style="font-size: 12px;" id="revenue_to" value="<?php echo $_REQUEST['revenue_to']; ?>" class="form-control date-pick">
                <label class="input-group-addon" for="revenue_to"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
              </div>
            </div>
            <div class="col-sm-2" id="div_back_arrow">
              <button type="button" class="btn" onclick="DateOptions('x', '#dayReport2');"><span class="glyphicon glyphicon-arrow-left"></span></button>
            </div>
          </div>
        </div>
		<?php } if($dbtemp_name == "Registered New Patient") { ?>
		<div class="clearfix "></div>
		<div class="col-sm-6">
          <label>Appointment type</label>
          <select name="app_type[]" id="app_type" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#common_drop">
            <?php echo $appName; ?>
          </select>
        </div>
		<div class="col-sm-6">
          <label>Report Criteria</label>
			<select class="form-control minimal" name="date_criteria" id="date_criteria" data-container="#common_drop">
				<option value="Patient Regis" <?php if ($_POST['date_criteria'] == 'Patient Regis') echo 'SELECTED'; ?>>Patient Registration</option>
				<option value="Sch Appt" <?php if ($_POST['date_criteria'] == 'Sch Appt') echo 'SELECTED'; ?>>Scheduler Appointment</option>
             </select>
        </div>
		<?php } ?>
		<div class="clearfix"></div>
        <div class="col-sm-4">
        <label>Recal Month-Year</label>
        	<select name="recal_month" id="recal_month" class="form-control minimal" data-width="100%" data-actions-box="false" data-title="<?php echo imw_msg('drop_sel');?>" <?php echo($recall_fulfilment==1)?'':'disabled';?>>
            <?php 
			for($year=date('Y')-1;$year<=date('Y');$year++){
				for($month=1;$month<=12;$month++)
				{
					$value=$month.'-'.$year;
					$display=$MONTH_NAME_ARR[$month].'-'.$year;
					$sel='';
					$sel=($recal_month==$value)?'selected':'';
					echo"<option value='$value' $sel>$display</option>";
				}
			}
			?>
            </select>
        </div>
        <div class="col-sm-6 mt5 mb5">
        <br>
          <div class="radio radio-inline pointer">
            <input type="radio" name="summary_detail" id="summary" value="summary" <?php echo (!$is_parent && !isset($filter_arr['summary_detail']))?'disabled':''; ?> <?php if ($_POST['summary_detail'] == 'summary' || !isset($_POST['summary_detail']) ) echo 'CHECKED'; ?>/>
            <label for="summary">Summary</label>
          </div>
          <div class="radio radio-inline pointer">
            <input type="radio" name="summary_detail" id="detail" value="detail" <?php echo (!$is_parent && !isset($filter_arr['summary_detail']))?'disabled':''; ?> <?php if ($_POST['summary_detail'] == 'detail') echo 'CHECKED'; ?>/>
            <label for="detail">Detail</label>
          </div>
        </div>
    <?php if($dbtemp_name=='Lost to follow'){ ?>
    <div class="col-sm-8 mt10" style="">        
      <div class="checkbox checkbox-inline pointer">
                <input type="checkbox" name="check_sch_date" id="check_sch_date" value="1" onClick="javascript:display_futureDate();" <?php echo ($_POST['check_sch_date']=='1')?'checked':''; ?> />
                <label for="check_sch_date">Check only future schedule date&nbsp;&nbsp;</label>
            </div>
    </div>
    <div class="col-sm-4" id="div_future_date" style="visibility:hidden;">
    <label id="lbl_date_title_future_date"></label>	
      <div class="input-group">
      <input type="text" name="Future_date" placeholder="Future Date" style="font-size: 12px;" id="Future_date" value="<?php echo $_REQUEST['Future_date']; ?>" class="form-control date-pick">
      <label class="input-group-addon" for="revenue_to"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
      </div>
    </div>

		<div class="col-sm-8">        
			<div class="checkbox checkbox-inline pointer">
          <input type="checkbox" name="include_claims" id="include_claims" value="1" onClick="javascript:enableDisable();" <?php echo ($_POST['include_claims']=='1')?'checked':''; ?> />
          <label for="include_claims">Include Claims</label>
      </div>
    </div>    
		<?php } ?>
     	</div>
    </div>
  </div>
  <div class="appointflt">
    <div class="anatreport">
      <h2>Analytic Filter</h2>
    </div>
    <div class="clearfix"></div>
    <div class="pd5" id="searchcriteria">
		<div class="row">
			<?php if($dbtemp_name == "Registered New Patient") { ?>
			<div class="col-sm-12">
				<label>Reminder Choices</label>
				<select name="reminder_choices[]" id="reminder_choices" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#common_drop">
					<option value="postal_mail" <?php if($reminder_choices['postal_mail'])echo 'SELECTED';?>>Postal Mail</option>
                    <option value="email" <?php if($reminder_choices['email'])echo 'SELECTED';?>>eMail</option>
                    <option value="voice" <?php if($reminder_choices['voice'])echo 'SELECTED';?>>Voice</option>
                    <option value="text" <?php if($reminder_choices['text'])echo 'SELECTED';?>>Text</option>
				</select>
			</div>
            <?php }?>
            
			<?php if($dbtemp_name == "Lost to follow") { ?>
			<div class="col-sm-4">
				<label>Ins. Comp.</label>
				<select name="insuranceName[]" id="insuranceName" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#common_drop">
					<?php echo $insComName_options; ?>
				</select>
			</div>
			<div class="col-sm-4">
				<label>CPT Code</label>
				<select name="cpt_code_id[]" id="cpt_code_id" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#common_drop">
					<?php echo $cpt_code_options; ?>
				</select>
			</div>
			<div class="col-sm-4">
				<label>ICD 10</label>
				<select name="all_dx10[]" id="all_dx10" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#common_drop">
					<?php echo $all_dx10_code_options; ?>
				</select>
			</div>
			<div class="col-sm-6">
				<label>Action</label>
				<select name="repType" id="repType" class="selectpicker" data-width="100%" data-size="10" data-title="Select" onChange="check_box(this.value)">
					<option value="">Select</option>
					<option <?php if ($_POST['repType'] == 'houseCalls') echo 'SELECTED'; ?> value="houseCalls" >Televox</option>
					<option <?php if ($_POST['repType'] == 'pam') echo 'SELECTED'; ?> value="pam">PAM2000</option>
					<option <?php if ($_POST['repType'] == 'letters') echo 'SELECTED'; ?> value="letters">Letters</option>
					<option <?php if ($_POST['repType'] == 'address_labels') echo 'SELECTED'; ?> value="address_labels">Address Labels</option>
					<option <?php if ($_POST['repType'] == 'phoneTree') echo 'SELECTED'; ?> value="phoneTree">PhoneTree</option>
				</select>
			</div>
			<div class="col-sm-6">
				<label>Template</label>
				<select name="letterTempId" id="letterTempId" class="selectpicker" data-width="100%" data-size="10" data-title="Select Template" data-container="#common_drop">
				   <option value="">Select Template</option>
                    <?php echo $letterDropDown; ?>
                </select>
			</div>
			<?php } ?>
		<div class="clearfix"></div>
      	<div class="col-sm-5">
          <label>Heard Type</label>
          <select name="heardAbtUs[]" id="heardAbtUs" <?php echo (!$is_parent && !isset($filter_arr['heard_about']))?'disabled':''; ?> class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#common_drop">
            <?php echo $heard_about_opts; ?>
          </select>
        </div>
        <div class="col-sm-7">
			<label>Heard Type Value</label>
			<input name="heardAbtUsValue" id="heardAbtUsValue" value="" class="form-control"  <?php echo (!$is_parent && !isset($filter_arr['heard_about']))?'disabled':''; ?> data-container="#common_drop">
      	</div>
		<?php if($dbtemp_name == "Heard about us"){ ?>
        <div class="clearfix"></div>
        <div class="col-sm-12 mt5 mb5">
			<div class="radio radio-inline pointer">
				<input type="radio" name="daterangefor" id="pat_created" value="pat_created" <?php if ($_POST['daterangefor'] == 'pat_created' || !isset($_POST['daterangefor']) ) echo 'CHECKED'; ?>/>
				<label for="pat_created">Pat. Created Date</label>
			</div>
			<div class="radio radio-inline pointer">
				<input type="radio" name="daterangefor" id="appointment" value="appointment" <?php if ($_POST['daterangefor'] == 'appointment') echo 'CHECKED'; ?>/>
				<label for="appointment">Date of Appt</label>
			</div>
			<div class="radio radio-inline pointer">
				<input type="radio" name="daterangefor" id="appt_created" value="appt_created" <?php if ($_POST['daterangefor'] == 'appt_created') echo 'CHECKED'; ?>/>
				<label for="appt_created">Appt Created Date</label>
			</div>
        </div>
		<?php } ?>
		<div class="clearfix"></div>
		<div class="col-sm-5">
        	<div class="checkbox pointer">
          	<input type="checkbox" name="new_patient" id="new_patient" <?php echo (!$is_parent && !isset($filter_arr['new_patient']))?'disabled':''; ?> value="1" <?php if ($_POST['new_patient'] == '1') echo 'CHECKED'; ?>/>
            <label for="new_patient">New Patient</label>
          </div>
        </div>
        <div class="col-sm-4 mt2">
        	<div class="checkbox pointer">
          	<input type="checkbox" name="lost_patient" id="lost_patient" <?php echo (!$is_parent && !isset($filter_arr['lost_patient']))?'disabled':''; ?> value="1" <?php if ($_POST['lost_patient'] == '1') echo 'CHECKED'; ?>/>
            <label for="lost_patient">Lost Patient</label>
          </div>
        </div>
        
        <div class="col-sm-3 mt2">
        	<div class="checkbox pointer">
          	<input type="checkbox" name="vip" id="vip" <?php echo (!$is_parent && !isset($filter_arr['vip']))?'disabled':''; ?> value="1" <?php if ($_POST['vip'] == '1') echo 'CHECKED'; ?>/>
            <label for="vip">VIP</label>
          </div>
        </div>
        
        <div class="clearfix"></div>
        
        <div class="col-sm-5 mt2">
        	<div class="checkbox pointer">
          	<input type="checkbox" name="deferred" id="deferred" <?php echo (!$is_parent && !isset($filter_arr['deferred']))?'disabled':''; ?> value="1" <?php if ($_POST['deferred'] == '1') echo 'CHECKED'; ?>/>
            <label for="deferred">Deferred</label>
          </div>
        </div>
        <div class="col-sm-4 mt2">
        	<div class="checkbox pointer">
          	<input type="checkbox" name="utilization" id="utilization" <?php echo (!$is_parent && !isset($filter_arr['utilization']))?'disabled':''; ?> value="1" <?php if ($_POST['utilization'] == '1') echo 'CHECKED'; ?>/>
            <label for="utilization">utilization</label>
          </div>
        </div>
        <div class="col-sm-3 mt2">
        	<div class="checkbox pointer">
          	<input type="checkbox" name="audit" id="audit" <?php echo (!$is_parent && !isset($filter_arr['audit']))?'disabled':''; ?> value="1" <?php if ($_POST['audit'] == '1') echo 'CHECKED'; ?>/>
            <label for="audit">Audit</label>
          </div>
        </div>
        
        <div class="clearfix"></div>
        
        <div class="col-sm-5 mt2">
        	<div class="checkbox pointer">
          	<input type="checkbox" name="recall_fulfilment" id="recall_fulfilment" <?php echo (!$is_parent && !isset($filter_arr['recall_fulfilment']))?'disabled':''; ?> value="1" <?php if ($_POST['recall_fulfilment'] == '1') echo 'CHECKED'; ?>/>
            <label for="recall_fulfilment">Recall Fulfilment</label>
          </div>
        </div>
        
        <div class="col-sm-7 mt2">
        	<div class="checkbox pointer">
          	<input type="checkbox" name="flow_analysis" id="flow_analysis" <?php echo (!$is_parent && !isset($filter_arr['flow_analysis']))?'disabled':''; ?> value="1" <?php if ($_POST['flow_analysis'] == '1') echo 'CHECKED'; ?>/>
            <label for="flow_analysis">Patient&nbsp;Flow&nbsp;Analysis</label>
          </div>
        </div>
        
        
        <div class="col-sm-5 mt2">
        	<div class="checkbox pointer">
          	<input type="checkbox" name="unfinalized_charts" id="unfinalized_charts" <?php echo (!$is_parent && !isset($filter_arr['unfinalized_charts']))?'disabled':''; ?> value="1" <?php if ($_POST['unfinalized_charts'] == '1') echo 'CHECKED'; ?>/>
            <label for="unfinalized_charts">Unfinalized&nbsp;Charts</label>
          </div>
        </div>
        <div class="col-sm-7 mt2">
        	<div class="checkbox pointer">
          	<input type="checkbox" name="unfinalized_tests" id="unfinalized_tests" <?php echo (!$is_parent && !isset($filter_arr['unfinalized_tests']))?'disabled':''; ?> value="1" <?php if ($_POST['unfinalized_tests'] == '1') echo 'CHECKED'; ?>/>
            <label for="unfinalized_tests">Unfinalized&nbsp;Tests</label>
          </div>
        </div>
        
        
        
      </div>
    </div>
  </div>
  <div class="grpara">
    <div class="anatreport">
      <h2>Group BY</h2>
    </div>
    <div class="clearfix"></div>
    <?php if(!$_POST['grpby_block'])$_POST['grpby_block']='grpby_physician';?>
    <div class="pd5" id="searchcriteria">
      <div class="row">
        <div class="col-sm-5">
          <div class="radio radio-inline pointer">
            <input type="radio" name="grpby_block" id="grpby_groups" <?php echo (!$is_parent && !isset($filter_arr['grpby_groups']))?'disabled':''; ?> value="grpby_groups" <?php if ($_POST['grpby_block'] == 'grpby_groups') echo 'CHECKED'; ?>/>
            <label for="grpby_groups">Business Unit</label>
          </div>
        </div>
        <div class="col-sm-7">
          <div class="radio radio-inline pointer">
            <input type="radio" name="grpby_block" id="grpby_facility" <?php echo (!$is_parent && !isset($filter_arr['grpby_facility']))?'disabled':''; ?> value="grpby_facility" <?php if ($_POST['grpby_block'] == 'grpby_facility') echo 'CHECKED'; ?>/>
            <label for="grpby_facility">Facility</label>
          </div>
        </div>
        <div class="col-sm-5">
          <div class="radio radio-inline pointer">
            <input type="radio" name="grpby_block" id="grpby_physician" <?php echo (!$is_parent && !isset($filter_arr['grpby_physician']))?'disabled':''; ?> value="grpby_physician" <?php if ($_POST['grpby_block'] == 'grpby_physician') echo 'CHECKED'; ?>/>
            <label for="grpby_physician">Physician</label>
          </div>
        </div>
        <div class="col-sm-7">
          <div class="radio radio-inline pointer">
            <input type="radio" name="grpby_block" id="grpby_operators" <?php echo (!$is_parent && !isset($filter_arr['grpby_operators']))?'disabled':''; ?> value="grpby_operators" <?php if ($_POST['grpby_block'] == 'grpby_operators') echo 'CHECKED'; ?>/>
            <label for="grpby_operators">Operators</label>
          </div>
        </div>
     	</div>
    </div>
  </div>
  <div class="grpara">
    <div class="anatreport">
      <h2>Include</h2>
    </div>
    <?php if(!$_POST['form_submitted']){	
		$_POST['inc_appt_detail']=$_POST['inc_recalls']=$_POST['inc_ref_physician']=1;
	
	}?>
    <div class="clearfix"></div>
    <div class="pd5" id="searchcriteria">
      <div class="row">
        <div class="col-sm-5">
          <div class="checkbox checkbox-inline pointer">
            <input type="checkbox" name="inc_appt_detail" id="inc_appt_detail" <?php echo (!$is_parent && !isset($filter_arr['inc_appt_detail']))?'disabled':''; ?> value="1" <?php if ($_POST['inc_appt_detail'] == '1') echo 'CHECKED'; ?>/>
            <label for="inc_appt_detail">Appointments</label>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="checkbox checkbox-inline pointer">
            <input type="checkbox" name="inc_acc_details" id="inc_acc_details" <?php echo (!$is_parent && !isset($filter_arr['inc_acc_details']))?'disabled':''; ?> value="1" <?php if ($_POST['inc_acc_details'] == '1') echo 'CHECKED'; ?>/>
            <label for="inc_acc_details">Account&nbsp;Details</label>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="checkbox checkbox-inline pointer">
            <input type="checkbox" name="inc_recalls" id="inc_recalls" <?php echo (!$is_parent && !isset($filter_arr['inc_recalls']))?'disabled':''; ?> value="1" <?php if ($_POST['inc_recalls'] == '1') echo 'CHECKED'; ?>/>
            <label for="inc_recalls">Recalls</label>
          </div>
        </div>
        <div class="col-sm-5">
          <div class="checkbox checkbox-inline pointer">
            <input type="checkbox" name="inc_ref_physician" id="inc_ref_physician" <?php echo (!$is_parent && !isset($filter_arr['inc_ref_physician']))?'disabled':''; ?> value="1" <?php if ($_POST['inc_ref_physician'] == '1') echo 'CHECKED'; ?>/>
            <label for="inc_ref_physician">Ref. Physician</label>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="checkbox checkbox-inline pointer">
            <input type="checkbox" name="inc_contact_lens" id="inc_contact_lens" <?php echo (!$is_parent && !isset($filter_arr['inc_contact_lens']))?'disabled':''; ?> value="1" <?php if ($_POST['inc_contact_lens'] == '1') echo 'CHECKED'; ?>/>
            <label for="inc_contact_lens">Contact Lens</label>
          </div>
        </div>
        
        
      </div>
    </div>
  </div>
  <div class="grpara">
    <div class="anatreport">
      <h2>Format</h2>
    </div>
    <div class="clearfix"></div>
    <div class="pd5" id="searchcriteria">
      <div class="row">
      	<div class="col-sm-5">
          <div class="radio radio-inline pointer">
            <input type="radio" name="output_option" id="output_actvity_summary" <?php echo (!$is_parent && !isset($filter_arr['output_actvity_summary']))?'disabled':''; ?> value="1" <?php if ($_POST['output_option'] == '1') echo 'CHECKED'; ?>/>
            <label for="output_actvity_summary">Account Summary</label>
          </div>
        </div>
        <div class="col-sm-7">
          <div class="radio radio-inline pointer">
            <input type="radio" name="output_option" id="output_view_only" <?php echo (!$is_parent && !isset($filter_arr['output_view_only']))?'disabled':''; ?> value="1" <?php if ($_POST['output_option'] == '1') echo 'CHECKED'; ?>/>
            <label for="output_view_only">View Only</label>
          </div>
        </div>
        <div class="col-sm-5">
          <div class="radio radio-inline pointer">
            <input type="radio" name="output_option" id="output_pdf" <?php echo (!$is_parent && !isset($filter_arr['output_pdf']))?'disabled':''; ?> value="output_pdf" <?php if ($_POST['output_option'] == 'output_pdf') echo 'CHECKED'; ?>/>
            <label for="output_pdf">PDF</label>
          </div>
        </div>
        <div class="col-sm-7">
          <div class="radio radio-inline pointer" >
            <input type="radio" name="output_option" id="output_csv" <?php echo (!$is_parent && !isset($filter_arr['output_csv']))?'disabled':''; ?> value="output_csv" <?php if ($_POST['output_option'] == 'output_csv') echo 'CHECKED'; ?>/>
            <label for="output_csv">CSV</label>
          </div>
        </div>
      </div>
    </div>
		<div class="clearfix">&nbsp;</div>
  </div>
</div>
<div id="module_buttons" class="ad_modal_footer text-center">
	<button class="savesrch" type="button" onClick="top.fmain.get_sch_report()">Search</button>
</div>