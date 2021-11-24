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
 
 File: account_receivable_left.php
 Purpose: to contain left panel for Financial -> Account Receivable Reports
 Access Type: Included
*/

$arr_cpt_cat_2=array_combine($_POST['cpt_cat_2'],$_POST['cpt_cat_2']);

//--- GET Groups SELECT BOX ----
$group_query = "Select gro_id,name,del_status from groups_new order by name";
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
$allGrpCount = sizeof(explode('</option>', $groupName)) - 1;

//--- GET FACILITY SELECT BOX ----
$fac_query = "select id,name from facility order by name";
$fac_query_res = imw_query($fac_query);
$fac_id_arr = array();
$facilityName = "";
while ($fac_res = imw_fetch_array($fac_query_res)) {
	$sel='';
    $fac_id = $fac_res['id'];
    $fac_id_arr[$fac_id] = $fac_res['name'];
	if($selArrFacility[$fac_id])$sel='SELECTED';

    $facilityName .= '<option value="'.$fac_res['id'].'" '.$sel.'>' . $fac_res['name'] . '</option>';
}
// POS Facility


$facility_name=array_combine($facility_name, $facility_name);
$facilityName = $CLSReports->getFacilityName($facility_name,'1');
$allFacCount = sizeof(explode('</option>', $facilityName)) - 1;


//--- GET ALL PHYSICIAN DETAILS ----
$physicianName = $CLSCommonFunction->drop_down_providers(implode(',',$phyId),'1','1');
$allPhyCount = sizeof(explode('</option>', $physicianName)) - 1;

$creditPhysicinName = $CLSCommonFunction->drop_down_providers(implode(',',$crediting_provider), '1', '1', '', 'report');
$allCrPhyCount = sizeof(explode('</option>', $creditPhysicinName)) - 1;

//--- GET DEPARTMENT NAME ---
$strdepartment = implode(',',$_REQUEST['department']);
$departmentName = $CLSReports->get_department_dropdown($strdepartment);

//--- GET ALL OPERATORS DETAILS ----
$operatorOption = $CLSCommonFunction->dropDown_providers(implode(',',$operator_id), '', '');
$taskAssignOperatorOption = $CLSCommonFunction->dropDown_providers(implode(',',$task_assign_operator_id), '', '');

//CPT CODES
$cpt_code_id = array_combine($cpt, $cpt);
$qry = "Select cpt_prac_code,cpt_fee_id,status,delete_status from cpt_fee_tbl 
		WHERE cpt_prac_code != '' order by cpt_prac_code asc";
$res = imw_query($qry);
while ($row = imw_fetch_array($res)) {
    $color = '';
    $cpt_fee_id = $row['cpt_fee_id'];
    $cpt_prac_code = (strlen($row['cpt_prac_code'])>38)? substr($row['cpt_prac_code'],0,38).'...': $row['cpt_prac_code'];
    if ($row['delete_status'] == 1 || $row['status'] == 'Inactive')
        $color = 'color:#CC0000!important';
		
		$sel = '';
		if (sizeof($cpt_code_id) > 0) {
			if ($cpt_code_id[$cpt_fee_id]) {
				$sel = 'selected';
			}
    }
    $cptDetailsArr[$cpt_fee_id] = $cpt_prac_code;
    $cpt_code_options .= "<option value='" . $cpt_fee_id . "' style='" . $color . "' ".$sel.">" . $cpt_prac_code . "</option>";
}


//SET INSURANCE COMPANY DROP DOWN
//$insQryRes = insurance_provider_xml_extract();
$getInsComp = "Select id,name,in_house_code,contact_address,City,State,Zip,Insurance_payment,secondary_payment_method, ins_del_status  
FROM insurance_companies order by in_house_code";
$rsInsComp = imw_query($getInsComp); 

$ins_comp_arr = array();
$insComName_options = '';
$sel_ins_comp_options = '';
//for ($i = 0; $i < count($insQryRes); $i++) {
while($res=imw_fetch_assoc($rsInsComp)){	
    if($res['name'] != 'No Insurance' && $res['name']!='') {
        $ins_id = $res['id'];
        $ins_name = $res['in_house_code'];
        $ins_status = $res['ins_del_status'];
        if ($ins_name == '') {
            $ins_name = $res['name'];
            if (strlen($ins_name) > 20) {
                $ins_name = substr($ins_name, 0, 20) . '....';
            }
        }
		$sel = '';
		if(sizeof($ins_carriers) > 0) {
			if(in_array($ins_id,$ins_carriers)) {
				$sel = 'selected';
			}
        }
        $ins_comp_arr[$ins_id] = $ins_name;
        if ($res['ins_del_status'] == 0)
            $insComName_options .= "<option value='" . $ins_id . "' ".$sel.">" . $ins_name . "</option>";
        else
            $insComName_options .= "<option value='" . $ins_id . "' ".$sel." style='color:red'>" . $ins_name . "</option>";
		}
}
$insurance_cnt = sizeof($ins_comp_arr);


//GET INSURANCE GROUP DROP DOWN
$insGroupQryRes = imw_query("SELECT id, title FROM ins_comp_groups");
$ins_group_arr = array();
$ins_group_options = '';
while ($row = imw_fetch_array($insGroupQryRes)) {
    $ins_grp_id = $row['id'];
    $ins_grp_name = $row['title'];

    $qry = "SELECT id FROM insurance_companies WHERE groupedIn = '" . $row['id'] . "' ORDER BY id";
    $res = imw_query($qry);
    $tmp_grp_ins_arr = array();
    if (imw_num_rows($res) > 0) {
        while ($det_row = imw_fetch_array($res)) {
            $tmp_grp_ins_arr[] = $det_row['id'];
        }
        $selected = '';
        $grp_ins_ids = implode(",", $tmp_grp_ins_arr);
        $ins_group_arr[$grp_ins_ids] = $ins_grp_name;

        if (in_array($grp_ins_ids,$insuranceGrp))
            $selected = 'SELECTED';
        $ins_group_options .= "<option value='" . $grp_ins_ids . "' " . $selected . ">" . $ins_grp_name . "</option>";
    }
}

//MAKE TIME OPTIONS
$timeOptions = '<option value="0">00 am</option>';
for ($i = 1; $i <= 23; $i++) {
    $fromSel = $toSel = '';
    $ampm = 'am';
    $num = $i;
    if ($i > 11) {
        if ($i > 12)
            $num = $i - 12;
        $ampm = 'pm';
    }
    if ($num < 10)
        $num = '0' . $num;

    if ($_POST['hourFrom'] == $i)
        $fromSel = 'SELECTED';
    $timeHourFromOptions .= '<option value="' . $i . '" ' . $fromSel . '>' . $num . ' ' . $ampm . '</option>';

    if ($_POST['hourTo'] == $i)
        $toSel = 'SELECTED';
    $timeHourToOptions .= '<option value="' . $i . '" ' . $toSel . '>' . $num . ' ' . $ampm . '</option>';
}

//--- GET AGGING CYCLE -----
	$policiesQry = imw_query("Select elem_arCycle from copay_policies where policies_id = '1'");
	$polociesDetails = imw_fetch_assoc($policiesQry);
	$aggingCycle = $polociesDetails['elem_arCycle'];
	
	$aggingDrop = array();
	$aggingDrop1 = array();
	$aggingDrop_options = "";
	$aggingDrop1_options = "";
	for($i=0;$i<180;$i++){
		$j = $i == 0 ? '00' : $i + 1;  	
		$aggingDrop[$j] = $j;
		$sel = ($j == $_REQUEST['aging_start']) ? 'selected' : '';
		$aggingDrop_options .= "<option value='" . $j . "' ".$sel.">" . $aggingDrop[$j] . "</option>";
		$i = $i == 0 ? '00' : $i; 
	if($i > 0){
		$aggingDrop1[$i] = $i;
		$sel = ($i == $_REQUEST['aging_to']) ? 'selected' : '';
		$aggingDrop1_options .= "<option value='" . $i . "' ".$sel.">" . $aggingDrop1[$i] . "</option>";
		
	}
		$i += ($aggingCycle - 1);
	}
?>
<div class="reportlft" style="height:100%;">
  <div class="practbox">
    <div class="anatreport">
      <h2>Practice Filter
		<?php if($dbtemp_name == "Provider A/R" || $dbtemp_name == "A/R Aging Insurance" || $dbtemp_name == "A/R Aging Patient"){ ?>
			<div id="rptInfoImg" style="float:right" class="rptInfoImg" onClick="showHideReportInfo(event, '<?php echo $logicWidth;?>')"></div>
		<?php }?>
	  </h2>
    </div>
    <div class="clearfix"></div>
    <div class="pd5" id="searchcriteria" style="position:relative;">
		<div id="sel_contianer" style="position:absolute;top:0; right:0;"></div>
      <div class="row">
        <div class="col-sm-4">
          <label>Groups</label>
          <select name="groups[]" id="groups" class="selectpicker" data-container="#common_drop" <?php echo ($temp_id && !isset($filter_arr['groups']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
            <?php echo $groupName; ?>
          </select>
        </div>
        <div class="col-sm-4">
          <label>Provider</label>
          <select name="phyId[]" id="phyId" class="selectpicker"  data-container="#common_drop"<?php echo ($temp_id && !isset($filter_arr['physician']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
            <?php echo $physicianName; ?>
          </select>
        </div>
		
		<?php if($dbtemp_name == "Provider A/R"){?>
		<div class="col-sm-4">
		  <label>Crediting Provider</label>
		  <select name="crediting_provider[]" id="crediting_provider" data-container="#common_drop" class="selectpicker"  data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
			  <?php echo $creditPhysicinName; ?>
		  </select>
		</div>		
		
		<div class="col-sm-8">
			<div class="checkbox pointer" >
				<input type="checkbox" name="chksamebillingcredittingproviders" id="chksamebillingcredittingproviders" value="1" <?php if($_POST['chksamebillingcredittingproviders']==1)echo 'checked';?>  />
				<label for="chksamebillingcredittingproviders">Exclude where billing and crediting providers are same.</label>
			</div>										  
		</div>	
		<?php } ?>							
		
        <div class="col-sm-4">
          <label>Facility</label>
          <select name="facility_name[]" id="facility_name" data-container="#common_drop" class="selectpicker" <?php echo ($temp_id && !isset($filter_arr['facility']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#sel_contianer" >
            <?php echo $facilityName; ?>
          </select>
        </div>
        
        <?php if($dbtemp_name!= "A/R Aging Insurance"){?>
        <div class="col-sm-4">
          <label>Department</label>
          <select name="department[]" id="department" data-container="#common_drop" class="selectpicker" <?php echo ($temp_id && !isset($filter_arr['department']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
          <?php echo $departmentName; ?>
          </select>
       	</div>
        <?php } ?>
  			
        <div class="col-sm-8">
          <label>Period</label>
          <?php
		  $dispDateFields='none';
		  $dispDateDD='block';
		  $imgIcon='block';
          if($dbtemp_name=="A/R Aging Patient" || $dbtemp_name== "A/R Aging Insurance"){
	  		  $dispDateFields='block';
			  $dispDateDD='none';
			  $imgIcon='none';
		  }
		  ?>
          <div id="dateFieldControler" style="display:<?php echo $dispDateDD;?>">
            <select name="dayReport" id="dayReport" class="selectpicker" <?php echo ($temp_id && !isset($filter_arr['date_range']))?'disabled':''; ?> data-width="100%" data-actions-box="false" onchange="DateOptions(this.value);">
              <option value="Daily" <?php if ($_POST['dayReport'] == 'Daily') echo 'SELECTED'; ?>>Daily</option>
              <option value="Weekly" <?php if ($_POST['dayReport'] == 'Weekly') echo 'SELECTED'; ?>>Weekly</option>
              <option value="Monthly" <?php if ($_POST['dayReport'] == 'Monthly') echo 'SELECTED'; ?>>Monthly</option>
              <option value="Quarterly" <?php if ($_POST['dayReport'] == 'Quarterly') echo 'SELECTED'; ?>>Quarterly</option>
              <option value="Date" <?php if ($_POST['dayReport'] == 'Date') echo 'SELECTED'; ?>>Date Range</option>
            </select>
          </div>
          
          <div class="row" style="display:<?php echo $dispDateFields;?>" id="dateFields">
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
            <div class="col-sm-2" style="display:<?php echo $imgIcon;?>">
              <button type="button" class="btn" onclick="DateOptions('x');"><span class="glyphicon glyphicon-arrow-left"></span></button>
            </div>
          </div>
        </div>

        <?php if($dbtemp_name!= "A/R Aging Insurance"){?>                          
        <div class="col-sm-4">
          <label>Operator</label>
          <select name="operator_id[]" id="operator_id" data-container="#common_drop" class="selectpicker" <?php echo ($temp_id && !isset($filter_arr['operators']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
            <?php echo $operatorOption; ?>
          </select>
        </div>
        <?php } ?>
        
        <div class="col-sm-8 nowrap "><br>
          <div class="radio radio-inline pointer">
            <input type="radio" name="DateRangeFor" id="dos" value="dos" <?php if ($_POST['DateRangeFor'] == 'dos') echo 'CHECKED'; ?> <?php echo ($temp_id && !isset($filter_arr['dos']))?'disabled':''; ?> />
            <label for="dos">DOS</label>
          </div>
          
          
          <div class="radio radio-inline pointer">
            <input type="radio" name="DateRangeFor" id="doc" value="doc" <?php if ($_POST['DateRangeFor'] == 'doc') echo 'CHECKED'; ?> <?php echo ($temp_id && !isset($filter_arr['doc']))?'disabled':''; ?> /> 
            <label for="doc">DOC</label>
          </div>
          
          <div class="radio radio-inline pointer">
            <input type="radio" name="DateRangeFor" id="dor" value="dor" <?php if ($_POST['DateRangeFor'] == 'dor') echo 'CHECKED'; ?> <?php echo ($temp_id && !isset($filter_arr['dor']))?'disabled':''; ?> />
            <label for="dor">DOR</label>
          </div>
          
          <div class="radio radio-inline pointer">
            <input type="radio" name="DateRangeFor" id="dot" value="dot" <?php if ($_POST['DateRangeFor'] == 'dot' || empty($_POST['DateRangeFor'])) echo 'CHECKED'; ?> <?php echo ($temp_id && !isset($filter_arr['dot']))?'disabled':''; ?> /> 
            <label for="dot">DOT</label>
          </div>
        </div>
        
        <div class="clearfix"></div>
        
        <?php if($dbtemp_name!= "A/R Aging Insurance"){?>
      	<div class="col-sm-6">	
          <div class="col-sm-6">
            <div class="form-group">
              <label>Time From</label>
              <select name="hourFrom" id="hourFrom" class="selectpicker" data-width="100%" data-actions-box="false" title="Select" <?php echo ( $temp_id && !isset($filter_arr['time_range']) )?'disabled':''; ?>>
              <?php echo $timeHourFromOptions; ?>
              </select>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <label>Time To</label>
              <select name="hourTo" id="hourTo" class="selectpicker" data-width="100%" data-actions-box="false" title="Select" <?php echo ($temp_id && !isset($filter_arr['time_range']))?'disabled':''; ?>>
              <?php echo $timeHourToOptions; ?>
              </select>
            </div>
          </div>
        </div>
        <?php }?>
        <div class="col-sm-6"><br>
          <div class="radio radio-inline pointer">
            <input type="radio" name="summary_detail" id="summary" value="summary" <?php echo ($temp_id && !isset($filter_arr['summary_detail']))?'disabled':''; ?> <?php if ($_POST['summary_detail'] == 'summary' || !isset($_POST['summary_detail']) ) echo 'CHECKED'; ?>/>
            <label for="summary">Summary</label>
          </div>
          <div class="radio radio-inline pointer">
            <input type="radio" name="summary_detail" id="detail" value="detail" <?php echo ($temp_id && !isset($filter_arr['summary_detail']))?'disabled':''; ?> <?php if ($_POST['summary_detail'] == 'detail') echo 'CHECKED'; ?>/>
            <label for="detail">Detail</label>
          </div>
        </div>
		<div class="clearfix"></div>
		<?php if($dbtemp_name =="A/R Aging Patient"){ ?>
			<div class="col-sm-8">
				<div class="">
					<!-- Pt. Search -->
					<div class="col-sm-12"><label>Patient</label></div>
					<div class="col-sm-5">
						<input type="hidden" name="patientId" id="patientId" value="<?php echo $_REQUEST['patientId'];?>">
						<input class="form-control" type="text" id="txt_patient_name" value="<?php echo $_REQUEST['txt_patient_name'];?>" name="txt_patient_name" onkeypress="{if (event.keyCode==13)return searchPatient()}" >
					</div> 
					<div class="col-sm-5">
						<select name="txt_findBy" id="txt_findBy" onkeypress="{if (event.keyCode==13)return searchPatient()}" class="form-control minimal">
							<option value="Active">Active</option>
							<option value="Inactive">Inactive</option>
							<option value="Deceased">Deceased</option> 
							<option value="Resp.LN">Resp.LN</option> 
							<option value="Ins.Policy">Ins.Policy</option>
						</select>
					</div> 
					<div class="col-sm-2 text-center">
						<button class="btn btn-success" type="button" onclick="searchPatient();"><span class="glyphicon glyphicon-search"></span></button>
					</div> 	
				</div>
			</div>
			<div class="col-sm-4">
				<div class="row">  
					<label for="startLname">&nbsp;Last Name</label>	
					<div class="col-sm-6">
						<input type="text" name="startLname" id="startLname" value="<?php echo $_REQUEST['startLname']; ?>" class="form-control" placeholder="From">
					</div>
					<div class="col-sm-6">
						<input type="text" name="endLname" id="endLname" value="<?php echo $_REQUEST['endLname']; ?>" class="form-control" placeholder="To">
					</div>
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
	
<?php
	if($dbtemp_name == "A/R Aging Patient" || $dbtemp_name == "A/R Aging Insurance"){
		//$class = "col-sm-4";
		if($dbtemp_name == "A/R Aging Patient"){
			$class = "col-sm-12";
			$cls_ins_grp_by='hide';
		}else{
			$class = "col-sm-6";
		}
	} else{
		$class = "col-sm-6";
		$class1 = " hide";
		$cls_ins_grp_by='hide';
	}
?>
    <div class="pd5" id="searchcriteria" >
    	<div class="row">
			<div class="col-sm-6">
				<label for="insuranceGrp">Ins. Group</label>
				<select name="insuranceGrp[]" id="insuranceGrp" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" <?php echo ($temp_id && !isset($filter_arr['ins_group']))?'disabled':''; ?>>
				<?php echo $ins_group_options; ?>
				</select>
			</div>
			<div class="col-sm-6">
				<label for="ins_carriers">Ins. Carriers</label>
				<select name="ins_carriers[]" id="ins_carriers" data-container="#common_drop" class="selectpicker " data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" <?php echo ($temp_id && !isset($filter_arr['ins_carriers']))?'disabled':''; ?> >
				<?php echo $insComName_options; ?>
				</select>
			</div>

			
			<div class="col-sm-6<?php echo " ".$cls_ins_grp_by; ?>">
				<label for="ins_group">Ins. Group by</label>
				 <select name="ins_group" id="ins_group" data-container="#common_drop" class="selectpicker" data-width="100%">
					<option value=""></option>
					<option value="primary" <?php if ($_POST['ins_group'] == 'primary') echo 'SELECTED'; ?>>Primary</option>
					<option value="secondary" <?php if ($_POST['ins_group'] == 'secondary') echo 'SELECTED'; ?>>Secondary</option>
            </select>
			</div>

			<div class="<?php echo $class." ".$class1; ?>" >
				<label for="cpt">CPT</label>
			  <select name="cpt[]" id="cpt" class="selectpicker pull-right" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" >
			   <?php echo $cpt_code_options; ?>
			  </select>
			</div>			

			<?php if($dbtemp_name == "A/R Aging Patient" || $dbtemp_name == "A/R Aging Insurance") { ?>
			<div class="col-sm-6">
				<label for="aging_start">Aging From</label>
				<select name="aging_start" id="aging_start" class="selectpicker" data-width="100%" data-size="10">
					<?php echo $aggingDrop_options; ?>
					<option value="181" <?php if($_REQUEST['aging_start'] == '181') echo 'SELECTED'?> >181</option>
				</select>
			</div>
			<div class="col-sm-6" >
				<label for="aging_to">Aging To</label>
				<select name="aging_to" id="aging_to" class="selectpicker" data-width="100%" data-size="10">
					<?php echo $aggingDrop1_options; ?>
					<option value="180" <?php if($_REQUEST['aging_to'] == '180') echo 'SELECTED'?> >180</option>
					<option value="All" <?php if($_REQUEST['aging_to'] == 'All' || !isset($_REQUEST['aging_to'])) echo 'SELECTED'?> >181+</option>
				</select>
			</div>
			<?php if($dbtemp_name == "A/R Aging Insurance") { ?>
			<div class="clearfix"></div>
			<div class="col-sm-12">
				<label for="BalanceAmount">>Balance Amount</label>
					<input type="text" name="BalanceAmount" class="form-control" id="BalanceAmount" value="<?php echo $_REQUEST['BalanceAmount']; ?>">
			</div>
			<?php } ?>
			<div class="col-sm-6 hideclass">
			<label>Assign Operator</label>
			<select name="task_assign_operator_id[]" id="task_assign_operator_id" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
			<?php echo $taskAssignOperatorOption; ?>
			</select>
			</div>
			<div class="col-sm-6 hideclass">
			<label>Task Status</label>
			<select name="task_status" id="task_status" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" data-actions-box="true" data-title="Select">
				<option value=""></option>
				<option value="1" <?php if ($_POST['task_status'] == '1') echo 'SELECTED'; ?>>Pending</option>
				<option value="2" <?php if ($_POST['task_status'] == '2') echo 'SELECTED'; ?>>Done</option>
			</select>
			</div>
			<div class="col-sm-6 hideclass">
				<label for="aging_to">Due Date</label>
              <div class="input-group">
                <input type="text" name="due_start_date" placeholder="From" style="font-size: 12px;" id="due_start_date" value="<?php echo $_REQUEST['due_start_date']; ?>" class="form-control date-pick">
                <label class="input-group-addon" for="due_start_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
              </div>
            </div>
            <div class="col-sm-6 hideclass">
			<label for="aging_to">&nbsp;</label>
              <div class="input-group">
                <input type="text" name="due_end_date" placeholder="To" style="font-size: 12px;" id="due_end_date" value="<?php echo $_REQUEST['due_end_date']; ?>" class="form-control date-pick">
                <label class="input-group-addon" for="due_end_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
              </div>
            </div>
			<?php } ?>
            
            <?php if($dbtemp_name == "Provider A/R") { ?>
            <div class="col-sm-6">
				<label>CPT Category 2</label>
				<select name="cpt_cat_2[]" id="cpt_cat_2" data-container="#common_drop" class="selectpicker " data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">				
					<option value="1" <?php echo ($arr_cpt_cat_2[1] ? 'selected' : '');?>>Service</option>  
					<option value="2" <?php echo ($arr_cpt_cat_2[2] ? 'selected' : '');?>>Material</option>
				</select>			
            </div>
            <?php } ?>
		</div>
    </div>
  </div>
  <div class="grpara">
    <div class="anatreport">
      <h2>Group BY</h2>
    </div>
    <div class="clearfix"></div>
    <div class="pd5" id="searchcriteria">
      <div class="row">

      <?php if($dbtemp_name== "A/R Aging Insurance"){?>
        <div class="col-sm-4">
          <div class="radio radio-inline pointer">
            <input type="radio" name="grpby_block" id="grpby_insurance" value="grpby_insurance" <?php if ($_POST['grpby_insurance']=='grpby_insurance' || $_POST['grpby_insurance']=='') echo 'CHECKED'; ?>/> 
            <label for="grpby_insurance">Insurance</label>
          </div>
        </div>
      <?php }?>
      
        <div class="col-sm-4">
          <div class="radio radio-inline pointer">
            <input type="radio" name="grpby_block" id="grpby_groups" <?php echo (!isset($filter_arr['grpby_groups']))?'disabled':''; ?> value="grpby_groups" <?php if ($_POST['grpby_block'] == 'grpby_groups') echo 'CHECKED'; ?>/> 
            <label for="grpby_groups">Groups</label>
          </div>
        </div>
        
        <div class="col-sm-4">
          <div class="radio radio-inline pointer">
            <input type="radio" name="grpby_block" id="grpby_facility" <?php echo (!isset($filter_arr['grpby_facility']))?'disabled':''; ?> value="grpby_facility" <?php if ($_POST['grpby_block'] == 'grpby_facility') echo 'CHECKED'; ?>/> 
            <label for="grpby_facility">Facility</label>
          </div>
        </div>
        
        <?php if($dbtemp_name!= "A/R Aging Insurance"){?>
        <div class="col-sm-4">
          <div class="radio radio-inline pointer">
            <input type="radio" name="grpby_block" id="grpby_physician" <?php echo (!isset($filter_arr['grpby_physician']))?'disabled':''; ?> value="grpby_physician" <?php if (empty($_POST['grpby_block']) || $_POST['grpby_block'] == 'grpby_physician') echo 'CHECKED'; ?>/> 
            <label for="grpby_physician">Physician</label>
          </div>
        </div>
        
        <div class="col-sm-4">	
          <div class="radio radio-inline pointer">
            <input type="radio" name="grpby_block" id="grpby_operators" <?php echo (!isset($filter_arr['grpby_operators']))?'disabled':''; ?> value="grpby_operators" <?php if ($_POST['grpby_block'] == 'grpby_operators') echo 'CHECKED'; ?>/> 
            <label for="grpby_operators">Operators</label>
          </div>
        </div>
        
        <div class="col-sm-4">	
          <div class="radio radio-inline pointer">
            <input type="radio" name="grpby_block" id="grpby_department" <?php echo (!isset($filter_arr['grpby_department']))?'disabled':''; ?> value="grpby_department" <?php if ($_POST['grpby_block'] == 'grpby_department') echo 'CHECKED'; ?>/> 
            <label for="grpby_department">Department</label>
          </div>
        </div>
        <?php }?>
      </div>
    </div>
  </div>
  <div class="grpara">
    <div class="anatreport">
      <h2>Include</h2>
    </div>
    <div class="clearfix"></div>
    <div class="pd5" id="searchcriteria">
      <div class="row">
                          	
        <div class="col-sm-4 hideclass">
          <div class="checkbox checkbox-inline pointer">
            <input type="checkbox" name="inc_appt_detail" id="inc_appt_detail" <?php echo ($temp_id &&  !isset($filter_arr['inc_appt_detail']))?'disabled':''; ?> value="1" <?php if ($_POST['inc_appt_detail'] == '1') echo 'CHECKED'; ?>/> 
            <label for="inc_appt_detail">Appt Detail</label>
          </div>
        </div>
        <?php if($dbtemp_name!= "A/R Aging Insurance"){?>  
        <div class="col-sm-4">
          <div class="checkbox checkbox-inline pointer">
            <input type="checkbox" name="inc_appt_summary" id="inc_appt_summary" <?php echo ($temp_id && !isset($filter_arr['inc_appt_summary']))?'disabled':''; ?> value="1" <?php if ($_POST['inc_appt_summary'] == '1') echo 'CHECKED'; ?>/> 
            <label for="inc_appt_summary">Appt&nbsp;Summary</label>
          </div>
        </div>
        
        <div class="col-sm-4">
          <div class="checkbox checkbox-inline pointer">
            <input type="checkbox" name="inc_ci_co_prepay" id="inc_ci_co_prepay" <?php echo ($temp_id && !isset($filter_arr['inc_ci_co_prepay']))?'disabled':''; ?> value="1" <?php if ($_POST['inc_ci_co_prepay'] == '1' || !isset($_POST['form_submitted'])) echo 'CHECKED'; ?>/> 
            <label for="inc_ci_co_prepay">CI/CO/Pre-Pay</label>
          </div>
        </div>
        <?php }?>

        <div class="col-sm-4">
          <div class="checkbox checkbox-inline pointer">
            <?php 
              $checked='';
              if ($_POST['inc_payments'] == '1' || !isset($_POST['form_submitted'])){$checked='CHECKED';}
              if($dbtemp_name== "A/R Aging Insurance" && !isset($_POST['form_submitted'])){$checked='';}
            ?>
            <input type="checkbox" name="inc_payments" id="inc_payments" <?php echo ($temp_id && !isset($filter_arr['inc_payments']))?'disabled':''; ?> value="1" <?php echo $checked; ?>/> 
            <label for="inc_payments">Payments</label>
          </div>
        </div>
        
        <div class="col-sm-4">
          <div class="checkbox checkbox-inline pointer">
          <?php 
              $checked='';
              if ($_POST['inc_adjustments'] == '1' || !isset($_POST['form_submitted'])){$checked='CHECKED';}
              if($dbtemp_name== "A/R Aging Insurance" && !isset($_POST['form_submitted'])){$checked='';}
            ?>
            <input type="checkbox" name="inc_adjustments" id="inc_adjustments" <?php echo ($temp_id && !isset($filter_arr['inc_adjustments']))?'disabled':''; ?> value="1" <?php echo $checked; ?>/> 
            <label for="inc_adjustments">Adjustments</label>
          </div>
        </div>
        <?php if($dbtemp_name!= "A/R Aging Insurance"){?>
        <div class="col-sm-4">
          <div class="checkbox checkbox-inline pointer">
            <input type="checkbox" name="inc_summary_charges" id="inc_summary_charges" <?php echo ($temp_id && !isset($filter_arr['inc_summary_charges']))?'disabled':''; ?> value="1" <?php if ($_POST['inc_summary_charges'] == '1' || !isset($_POST['form_submitted'])) echo 'CHECKED'; ?>/> 
            <label for="inc_summary_charges">Summary&nbsp;Charges</label>
          </div>
        </div>
        <?php } ?>

      <?php if($dbtemp_name!= "A/R Aging Insurance"){?>  
        <div class="col-sm-4">
          <div class="checkbox checkbox-inline pointer">
            <input type="checkbox" name="inc_opening_ar" id="inc_opening_ar" <?php echo ($temp_id && !isset($filter_arr['inc_opening_ar']))?'disabled':''; ?> value="1" <?php if ($_POST['inc_opening_ar'] == '1' || !isset($_POST['form_submitted'])) echo 'CHECKED'; ?>/> 
            <label for="inc_opening_ar">Opening AR</label>
          </div>
        </div>
        
        <div class="col-sm-4">
          <div class="checkbox checkbox-inline pointer">
            <input type="checkbox" name="inc_ending_ar" id="inc_ending_ar" <?php echo ($temp_id && !isset($filter_arr['inc_ending_ar']))?'disabled':''; ?> value="1" <?php if ($_POST['inc_ending_ar'] == '1' || !isset($_POST['form_submitted'])) echo 'CHECKED'; ?>/> 
            <label for="inc_ending_ar">Ending AR</label>
          </div>
        </div>
		<?php if($dbtemp_name == "A/R Aging Patient") { ?>
		<div class="col-sm-4">
          <div class="checkbox checkbox-inline pointer">
            <input type="checkbox" name="dispDemographics" id="dispDemographics" value="1" <?php if ($_POST['dispDemographics'] == '1' || !isset($_POST['form_submitted'])) echo 'CHECKED'; ?>/> 
            <label for="dispDemographics">Pt. Demographics</label>
          </div>
        </div>
		
		<div class="col-sm-4">
          <div class="checkbox checkbox-inline pointer">
            <input type="checkbox" name="dispPrePayments" id="dispPrePayments" value="1" <?php if ($_POST['dispPrePayments'] == '1' || !isset($_POST['form_submitted'])) echo 'CHECKED'; ?>/> 
            <label for="dispPrePayments">Unapplied Pre-payments</label>
          </div>
        </div>
    <?php  }?>    
		<?php } if($dbtemp_name == "A/R Aging Insurance") { ?>
		<div class="col-sm-4">
			<div class="checkbox checkbox-inline pointer">
				<input type="checkbox" name="accNotes" id="accNotes" value="1" <?php if ($_POST['accNotes'] == '1') echo 'CHECKED'; ?>/> 
				<label for="accNotes">Acc. Details</label>
          </div>
        </div>
		<?php } ?>
		
	</div>
    </div>
  </div>
<?php if($dbtemp_name == "A/R Aging Insurance"){ ?>
	<div class="grpara">
		<div class="anatreport"><h2>Saved Criteria</h2></div>
		<div class="clearfix"></div>
		<div class="pd5" id="searchcriteria">
			<div class="row">
				<div class="col-sm-5">
					<label>Saved Searches</label>
					<select name="savedCriteria" id="savedCriteria" style="width:100%;" data-maincss="blue" onchange="javascript:dChk=0; callSavedSearch(this.value, 'ar_report_form'); saved_functionality('savedCriteria');">
						<option value="" >Select</option>
						<?php echo $searchOptions; ?>
					</select> 
					<input type="hidden" name="saved_searched_id" id="saved_searched_id" value="">
				</div>
				<div class="col-sm-2" >
					<div class="checkbox pointer" style="padding-top:17px">
						<input type="checkbox" name="chkSaveSearch" id="chkSaveSearch" value="1" onClick="javascript:show_saved();" />
						<label for="chkSaveSearch">Save</label>
						</div>
					</div> 
				<div class="col-sm-5" id="div_search_name" style="display:none" >
					<label>Name of Search</label>
					<input type="text" name="search_name" id="search_name" value="" class="form-control" onBlur="javascript: saved_functionality('search_name');" />
				</div>                               
			</div>
		</div>
	</div> 
<?php } if($dbtemp_name != "A/R Aging Insurance" && $dbtemp_name != "A/R Aging Patient") { ?>	
	<div class="grpara">
		<div class="anatreport">
			<h2>Format</h2>
		</div>
		<div class="clearfix"></div>
		<div class="pd5" id="searchcriteria">
			<div class="row">
				<div class="col-sm-4">
					<div class="radio radio-inline pointer">
						<input type="radio" name="output_option" id="output_actvity_summary" <?php echo ($temp_id && !isset($filter_arr['output_actvity_summary']))?'disabled':''; ?> value="view" <?php if ($_POST['output_option'] == 'view' || $_POST['output_option']=='') echo 'CHECKED'; ?>/>
						<label for="output_actvity_summary">View</label>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="radio radio-inline pointer">
						<input type="radio" name="output_option" id="output_pdf" <?php echo ($temp_id && !isset($filter_arr['output_pdf']))?'disabled':''; ?> value="output_pdf" <?php if ($_POST['output_option']=='output_pdf') echo 'CHECKED'; ?>/>
						<label for="output_pdf">PDF</label>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="radio radio-inline pointer" >
						<input type="radio" name="output_option" id="output_csv" <?php echo ($temp_id && !isset($filter_arr['output_csv']))?'disabled':''; ?> value="output_csv" <?php if ($_POST['output_option'] == 'output_csv') echo 'CHECKED'; ?>/>
						<label for="output_csv">CSV</label>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix">&nbsp;</div>
	</div>
<?php } ?>
</div>
<div id="module_buttons" class="ad_modal_footer text-center">
	<button class="savesrch" type="button" onClick="top.fmain.get_sch_report()">Search</button>
</div>