<?php

set_time_limit(600);
require_once("../../../config/globals.php");
require_once('../../../library/classes/cls_common_function.php');

$CLSCommonFunction = new CLSCommonFunction;

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$s		= isset($_REQUEST['s']) ? trim($_REQUEST['s']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : '';
$soAD	= isset($_REQUEST['soAD']) ? trim($_REQUEST['soAD']) : 'ASC';
$p		= isset($_REQUEST['p']) ? trim($_REQUEST['p']) : '';
$f		= isset($_REQUEST['f']) ? trim($_REQUEST['f']) : '';

$denial_rej_arr=array('96','49','119','129','11','16','18','20','21');
$denial_rej_str=implode(',',$denial_rej_arr);

						
switch($task){
	case 'delete':
		$id = trim($_POST['listId']);
		$q 		= "update tm_rules_list SET rule_status=1 WHERE id IN ($id) ";
        $res 	= imw_query($q);
        if($res){
            echo 'Record deleted successfully.';
        }else{
            echo 'Record deleting failed.';
        }
		break;
        /*
	case 'save_update':
        $id = $_POST['id'];
		unset($_POST['id']);
		unset($_POST['task']);
		$query_part = "";
		
        foreach($_POST as $k=>$v){
			$query_part.= $k."='".trim($v)."', ";
		}
        $query_part = substr($query_part,0,-2);
        if($id==''){
			$q = "INSERT INTO tm_rules SET ".$query_part;
		}else{
			$q = "UPDATE tm_rules SET ".$query_part." WHERE id='".$id."'";
        }
        
        $res = imw_query($q);
		if($id==''){
			$id  =imw_insert_id();
		}
        
		if($res){
			echo 'Record Saved Successfully.';
		}else{
			echo 'Record Saving failed.';//.imw_error()."\n".$q;
		}
		break;
	case 'show_list':
        /* Get Task Manager Categories and rules */
       /* $categories = array();
        $sql = "SELECT * FROM tm_rule_category";
        if($so=='tm_rule_category') {
            $sql.=" ORDER BY $so $soAD";
        }
        $resp = imw_query($sql);
        if ($resp && imw_num_rows($resp) > 0) {
            while ($row = imw_fetch_assoc($resp)) {
                $categories[$row['id']] = $row['tm_rule_category'];
            }
        }

        $rules = array();
        $rule_sql = "SELECT * FROM tm_rules";
        if($so=='tm_rule_name') {
            $rule_sql.=" ORDER BY $so $soAD";
        }
        //echo $rule_sql;die;
        $rule_rs = imw_query($rule_sql);
        if ($rule_rs && imw_num_rows($rule_rs) > 0) {
            while ($rules_row = imw_fetch_assoc($rule_rs)) {
                $rules[] = $rules_row;
            }
        }
		
		echo json_encode(array('rules'=>$rules,'categories'=>$categories));
		break; */
}

$get_options = (isset($_REQUEST['action']) && $_REQUEST['action'] !='') ? $_REQUEST['action'] : '';


if($get_options == 'get_options') {
    $rules = array();
    $rule_id = $_REQUEST['rule_id'];
    $cat_id = $_REQUEST['cat_id'];
    $ss_type = trim($_REQUEST['ss_type']);
    //$ss_id = trim($_REQUEST['ss_id']);
    
    $list_id='';
    $rulerow=array();
    if(isset($_REQUEST['list_id']) && $_REQUEST['list_id']!='') {
        $list_id=$_REQUEST['list_id'];

        $rule_sql = "SELECT * FROM tm_rules_list where rule_status=0 and id=$list_id";
        $rule_rs = imw_query($rule_sql);
        if ($rule_rs && imw_num_rows($rule_rs) == 1) {
            $rulerow = imw_fetch_assoc($rule_rs);
            $rulerow['type']='';
            if($rulerow['ss_id']>0 && $rulerow['cat_id']==2) {
                $rulerow['type']='schedule_status';
                $rulerow['tm_rcat_id']=$rulerow['cat_id'];
                $rulerow['ss_id']=$rulerow['ss_id'];
                $rulerow['rule_id']='ss'.$rulerow['ss_id'];
            }
        }
    }
    
    
    
    if((isset($ss_type) && trim($ss_type)=='schedule_status')) {
        $ss_id= str_replace('ss', '', $rule_id);
        $rule_sql = "SELECT id,status_name as tm_rule_name FROM schedule_status where id=".$ss_id." and status=1 order by status_name asc";
    } else {
        $rule_sql = "SELECT * FROM tm_rules where id=".$rule_id." and tm_rcat_id =".$cat_id." ";
    }
    $rule_rs = imw_query($rule_sql);
    
    if ($rule_rs && imw_num_rows($rule_rs) > 0) {
        $rules_row = imw_fetch_assoc($rule_rs);
        if(($ss_type && $ss_type!='') || $cat_id==2) {
            if((isset($ss_type) && trim($ss_type)=='schedule_status')) {
                $rules_row['type']='schedule_status';
                $rules_row['ss_id']=$rules_row['id'];
                $rules_row['id']='ss'.$rules_row['id'];
                $rules_row['tm_rcat_id']='2';
            }
            fetch_appt_status_options($rules_row,$rulerow);
            die;
        }
        switch(trim($rules_row['id'])) {
            //Pt Status changed
            case '7':
                $options=array();
                $options_str='';
                $pt_sql = "SELECT * FROM patient_status_tbl ";
                $pt_rs = imw_query($pt_sql);
                if ($pt_rs && imw_num_rows($pt_rs) > 0) {
                    $options_str='<label for="reason_code'.$rule_id.'">Patient Status</label><select class="selectpicker" multiple name="patientStatus'.$rule_id.'[]" id="patientStatus'.$rule_id.'" data-width="100%"  data-actions-box="true" data-live-search="true">';
                    while ($row = imw_fetch_assoc($pt_rs)) {
                        $sel=(in_array($row['pt_status_id'],explode(',',$rulerow['pt_status'])))?' selected ':'';
                        $options[$row['pt_status_id']] = $row['pt_status_name'];
                        $options_str.='<option value="'.$row['pt_status_id'].'" '.$sel.'>'.$row['pt_status_name'].'</option>';
                    }
                    $options_str.='</select>';
                }
                echo $options_str;
                break;
                //Denial & Rejection
                //Reason code
            case '2':
            case '3':
                $options=array();
                $options_str='';
                $pt_sql= "SELECT * FROM cas_reason_code ";
                /* if(trim($rules_row['tm_rule_name'])=='Denial & Rejection') {
                    $pt_sql.=" where cas_code IN($denial_rej_str) ";
                } else {
                    $pt_sql.=" where cas_code NOT IN($denial_rej_str) ";
                } */
                $pt_sql.=" order by cas_code ASC ";
                $pt_rs = imw_query($pt_sql);
                $col='12';
                if(trim($rules_row['id'])=='2'){$col='4';}
                if ($pt_rs && imw_num_rows($pt_rs) > 0) {
                    $options_str='<div class="col-sm-'.$col.' form-inline">
                    <div class="form-group multiselect">
                        <label for="reason_code'.$rule_id.'">Reason Code</label><select class="selectpicker" multiple name="reason_code_'.$rule_id.'[]" id="reason_code'.$rule_id.'" data-width="100%" data-size="10"  data-actions-box="true" data-live-search="true"  data-selected-text-format="count > 2">';
                    $reason_codeArr=array();
                    while ($row = imw_fetch_assoc($pt_rs)) {
                        $reason_codeArr=explode(',',$rulerow['reason_code']);
                        $reason_codeArr=array_map('trim',$reason_codeArr);
                        $sel=(in_array($row['cas_code'],$reason_codeArr))?' selected ':'';
                        $options[$row['cas_code']] = $row['cas_code'];
                        $obj_tooltiptext = '';
                        //$row['cas_desc'] = wordwrap($row['cas_desc'], 5);
                        if(strlen($row['cas_desc']) > 90) {
                            $obj_tooltiptext = $row['cas_desc'];
                            $row['cas_desc'] = substr($row['cas_desc'], 0, 90).'...';
                        }
                        $cas_cod=$row['cas_code'].' -- '.$row['cas_desc'];
                        $options_str.='<option value="'.$row['cas_code'].'" '. $sel .' data-content="<span data-placement=\'left\' data-toggle=\'tooltip\' data-html=\'true\' data-container=\'body\' data-original-title=\''.$obj_tooltiptext.'\'>'.$cas_cod.'</span>">'.$cas_cod.'</option>';
                    }
                    $options_str.='</select></div></div>';
                }
                
                
                if(trim($rules_row['id'])==2) {
                    //--- GET Groups SELECT BOX ----
                    $group_query = "Select gro_id,name,del_status from groups_new order by name";
                    $group_query_res = imw_query($group_query);
                    $group_id_arr = array();
                    $groupName = "";
                    while ($group_res = imw_fetch_array($group_query_res)) {
                        $group_id = $group_res['gro_id'];
                        $group_id_arr[$group_id] = $group_res['name'];
                        $sel_tm_group=trim($rulerow['tm_group']);
                        $sel=(in_array($group_id,explode(',',$sel_tm_group)))?' selected ':'';

                        $groupName .= '<option value="'.$group_res['gro_id'].'" '.$sel.'>' . $group_res['name'] . '</option>';
                    }

                    //SET INSURANCE COMPANY DROP DOWN
                    $insQryRes = insurance_provider_xml_extract();
                    $ins_comp_arr = array();
                    $insComName_options = '';
                    $sel_ins_comp_options = '';
                    for ($i = 0; $i < count($insQryRes); $i++) {
                        if(!$insQryRes[$i]['attributes']) continue;
                        if($insQryRes[$i]['attributes']['insCompName'] != 'No Insurance') {
                            $ins_id = $insQryRes[$i]['attributes']['insCompId'];
                            $ins_name = $insQryRes[$i]['attributes']['insCompINHouseCode'];
                            $ins_status = $insQryRes[$i]['attributes']['insCompStatus'];
                            if ($ins_name == '') {
                                $ins_name = $insQryRes[$i]['attributes']['insCompName'];
                                if (strlen($ins_name) > 20) {
                                    $ins_name = substr($ins_name, 0, 20) . '....';
                                }
                            }
                            $sel_ins_comp=trim($rulerow['tm_ins_comp']);
                            $sel=(in_array($ins_id,explode(',',$sel_ins_comp)))?' selected ':'';
                            $ins_comp_arr[$ins_id] = $ins_name;
                            if ($insQryRes[$i]['attributes']['insCompStatus'] == 0)
                                $insComName_options .= "<option value='" . $ins_id . "' ".$sel.">" . $ins_name . "</opton>";
                            else
                                $insComName_options .= "<option value='" . $ins_id . "' ".$sel." style='color:red'>" . $ins_name . "</opton>";
                        }
                    }


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
                            $grp_ins_ids = implode(",", $tmp_grp_ins_arr);
                            $ins_group_arr[$grp_ins_ids] = $ins_grp_name;

                            $sel_ins_group=trim($rulerow['tm_ins_group']);
                            $sel=(in_array($ins_grp_id,explode(',',$sel_ins_group)))?' selected ':'';
                            $ins_group_options .= "<option value='" . $ins_grp_id . "' " . $sel . ">" . $ins_grp_name . "</opton>";
                        }
                    }
                
                
                    $cpt_fee_arr = get_cpt_arr();
                    foreach($cpt_fee_arr as $key => $row_cpt) {
                        $sel_tm_cpt_code=trim($rulerow['tm_cpt_code']);
                        $sel=(in_array($key,explode(',',$sel_tm_cpt_code)))?' selected ':'';
                        $cpt_fee_options .= "<option value='" . $key . "' " . $sel . ">" . $row_cpt . "</opton>";
                    }


                    $options_str .= '<div class="col-sm-4 form-inline">
                    <div class="form-group multiselect">
                        <label for="ar_group' . $rule_id . '">Group</label>
                        <select class="selectpicker" multiple data-done-button="true" data-actions-box="true" data-width="100%"  data-size="10" id="ar_group' . $rule_id . '" name="ar_group' . $rule_id . '[]"  data-selected-text-format="count > 2">';
                    $options_str .= $groupName;
                    $options_str .= '</select>
                    </div>
                    </div><div class="col-sm-4 form-inline">
                    <div class="form-group multiselect">
                        <label for="tm_cpt_code' . $rule_id . '">CPT Code</label>
                        <select class="selectpicker minimal selecicon" multiple data-done-button="true" data-size="10" data-width="100%" data-actions-box="true" id="tm_cpt_code' . $rule_id . '" name="tm_cpt_code' . $rule_id . '[]"  data-selected-text-format="count > 2">';
                    $options_str .= $cpt_fee_options;
                    $options_str .= '</select>
                    </div>
                    </div><div class="clearfix"></div><br /><div class="col-sm-4 form-inline">
                    <div class="form-group multiselect">
                    <label for="ar_ins_group' . $rule_id . '"> Insurance Group</label>
                        <select class="selectpicker" multiple id="ar_ins_group' . $rule_id . '" name="ar_ins_group' . $rule_id . '[]"  data-done-button="true" data-actions-box="true" data-width="100%" data-size="10"  data-selected-text-format="count > 2">';
                    $options_str .= $ins_group_options;
                    $options_str .= '</select>
                    </div>
                    </div><div class="col-sm-4 form-inline">
                    <div class="form-group multiselect">
                        <label for="ar_ins_comp' . $rule_id . '">Insurance</label>
                        <select class="selectpicker minimal selecicon" multiple data-done-button="true" data-actions-box="true" data-width="100%" data-size="10" id="ar_ins_comp' . $rule_id . '" name="ar_ins_comp' . $rule_id . '[]"  data-selected-text-format="count > 2">';
                    $options_str .= $insComName_options;
                    $options_str .= '</select>
                    </div>
                    </div>                    
                </div>    
                ';
                }
                
                echo $options_str;
                break;
                
                //Pt Account Status
            case '8':
                $options=array();
                $options_str='';
                $ptac_sql = "Select id, status_name FROM account_status WHERE del_status='0' ORDER BY status_name ";
                $ptac_rs = imw_query($ptac_sql);
                if ($ptac_rs && imw_num_rows($ptac_rs) > 0) {
                    $options_str='<label for="reason_code'.$rule_id.'">Patient Account Status</label><select class="selectpicker" multiple name="pt_account_status'.$rule_id.'[]" id="pt_account_status'.$rule_id.'" data-width="100%" data-size="10"  data-actions-box="true" data-live-search="true">';
                    while ($row = imw_fetch_assoc($ptac_rs)) {
                        $sel=(in_array($row['id'],explode(',',$rulerow['pt_account_status'])))?' selected ':'';
                        $options[$row['id']] = $row['status_name'];
                        $options_str.='<option value="'.$row['id'].'" '.$sel.'>'.$row['status_name'].'</option>';
                    }
                    $options_str.='</select>';
                }
                echo $options_str;
                break;
            /*    
            case '10':
            case '11':
            case '12':
            case '13':
            case '14':
                $ptapptstatus=array();
                $ptapptstatus = explode('||', $rulerow['pt_appt_status']);
                
                $options_str='';
                
                // Get Users 
                $physician_arr = $sel_prov=array();
                $sql = "select id, fname, lname, mname from users where Enable_Scheduler = '1' and delete_status = '0' order by lname, fname";
                $sql_rs = imw_query($sql);
                if ($sql_rs && imw_num_rows($sql_rs) > 0) {
                    while ($row = imw_fetch_assoc($sql_rs)) {
                        $sel_prov=trim($ptapptstatus[0]);
                        $prov_name = core_name_format($row['lname'], $row['fname'], $row['mname']);
                        $physician_arr[$row['id']] = $prov_name;
                        $sel=(in_array($row['id'],explode(', ',$sel_prov)))?' selected ':'';
                        $prov_options.='<option value="'.$row['id'].'" '.$sel.'>'.$prov_name.'</option>';
                    }
                }

                $facility_arr = $sel_fac=array();
                $qry = "select id, name from facility order by name";
                $sql_rs = imw_query($qry);
                if ($sql_rs && imw_num_rows($sql_rs) > 0) {
                    while ($row = imw_fetch_assoc($sql_rs)) {
                        $sel_fac=trim($ptapptstatus[1]);
                        $facility_arr[$row['id']] = $row['name'];
                        $sel=(in_array($row['id'],explode(',',$sel_fac)))?' selected ':'';
                        $fac_options.='<option value="'.$row['id'].'" '.$sel.'>'.$row['name'].'</option>';
                    }
                }
                
                $options_str='<div class="col-sm-6 form-inline">
                    <div class="form-group multiselect">
                        <label for="sel_provider'.$rule_id.'">Physician</label>
                        <select class="selectpicker minimal selecicon" multiple data-done-button="true" data-size="10" data-width="140px" data-actions-box="true" id="sel_provider'.$rule_id.'" name="sel_provider'.$rule_id.'[]">';
                $options_str.=$prov_options;
                $options_str.='</select>
                    </div>
                    </div><div class="col-sm-6 form-inline">
                    <div class="form-group multiselect">
                        <label for="facilities'.$rule_id.'">Location</label>
                        <select class="selectpicker minimal selecicon" multiple data-done-button="true" data-actions-box="true" data-width="140px" data-size="10" id="facilities'.$rule_id.'" name="facilities'.$rule_id.'[]">';
                $options_str.=$fac_options;
                $options_str.='</select>
                    </div>
                    </div>
                </div>';
                
                echo $options_str;
                
                break;
            */
            case '15':
            case '16':
                
                //--- GET Groups SELECT BOX ----
                $group_query = "Select gro_id,name,del_status from groups_new order by name";
                $group_query_res = imw_query($group_query);
                $group_id_arr = array();
                $groupName = "";
                while ($group_res = imw_fetch_array($group_query_res)) {
                    $group_id = $group_res['gro_id'];
                    $group_id_arr[$group_id] = $group_res['name'];
                    $sel_tm_group=trim($rulerow['tm_group']);
                    $sel=(in_array($group_id,explode(',',$sel_tm_group)))?' selected ':'';

                    $groupName .= '<option value="'.$group_res['gro_id'].'" '.$sel.'>' . $group_res['name'] . '</option>';
                }

                //SET INSURANCE COMPANY DROP DOWN
                    $insQryRes = insurance_provider_xml_extract();
                    $ins_comp_arr = array();
                    $insComName_options = '';
                    $sel_ins_comp_options = '';
                    for ($i = 0; $i < count($insQryRes); $i++) {
                        if(!$insQryRes[$i]['attributes']) continue;
                        if($insQryRes[$i]['attributes']['insCompName'] != 'No Insurance') {
                            $ins_id = $insQryRes[$i]['attributes']['insCompId'];
                            $ins_name = $insQryRes[$i]['attributes']['insCompINHouseCode'];
                            $ins_status = $insQryRes[$i]['attributes']['insCompStatus'];
                            if ($ins_name == '') {
                                $ins_name = $insQryRes[$i]['attributes']['insCompName'];
                                if (strlen($ins_name) > 20) {
                                    $ins_name = substr($ins_name, 0, 20) . '....';
                                }
                            }
                            $sel_ins_comp=trim($rulerow['tm_ins_comp']);
                            $sel=(in_array($ins_id,explode(',',$sel_ins_comp)))?' selected ':'';
                            $ins_comp_arr[$ins_id] = $ins_name;
                            if ($insQryRes[$i]['attributes']['insCompStatus'] == 0)
                                $insComName_options .= "<option value='" . $ins_id . "' ".$sel.">" . $ins_name . "</opton>";
                            else
                                $insComName_options .= "<option value='" . $ins_id . "' ".$sel." style='color:red'>" . $ins_name . "</opton>";
                        }
                    }


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
                            $grp_ins_ids = implode(",", $tmp_grp_ins_arr);
                            $ins_group_arr[$grp_ins_ids] = $ins_grp_name;

                            $sel_ins_group=trim($rulerow['tm_ins_group']);
                            $sel=(in_array($ins_grp_id,explode(',',$sel_ins_group)))?' selected ':'';
                            $ins_group_options .= "<option value='" . $ins_grp_id . "' " . $sel . ">" . $ins_grp_name . "</opton>";
                        }
                    }

                //Get POS facilities
                $pos_facilities_str = '';
                $qry = "select pos_facilityies_tbl.facilityPracCode as name,
                                pos_facilityies_tbl.pos_facility_id as id,
                                pos_tbl.pos_prac_code
                                from pos_facilityies_tbl
                                left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id
                                order by pos_facilityies_tbl.headquarter desc,
                                pos_facilityies_tbl.facilityPracCode";
                $res = imw_query($qry);

                while($row=imw_fetch_assoc($res)){	
                    $id = $row['id'];
                    $name = $row['name'];
                    $pos_prac_code = $row['pos_prac_code'];
                    $sel_pos_fac=trim($rulerow['ar_facility']);
                    $sel=(in_array($id,explode(',',$sel_pos_fac)))?' selected ':'';
                    $pos_facilities_str .= '<option '.$sel.' value="'.$id.'">'.$name.' - '.$pos_prac_code.'</option>';
                }
                
                
                $options_str='';
                $ar_aging_arr = array('30-60'=>'30-60', '61-90'=>'61-90', '91-120'=>'91-120', '121-180'=>'121-180', '181'=>'180+');
                $ptar_aging_value=array();
                $options_str.='<div class="row"><div class="col-sm-4 form-inline">
                    <div class="form-group"><label for="ar_aging_value">Days</label>';
                $options_str.='<select class="selectpicker" name="ar_aging'.$rule_id.'" id="ar_aging'.$rule_id.'" data-rule_id="'.$rule_id.'" data-width="100%" data-size="10" data-title="Select Days" >';
                    foreach($ar_aging_arr as $key => $val) {
                        $ptar_aging = explode('#', $rulerow['ar_aging']);
                        $sel=($key==$ptar_aging[0])?' selected ':'';
                        $options_str.='<option value="'.$key.'" '.$sel.' >'.$val.'</option>';
                        
                        $ptar_aging_value[$rule_id]=(isset($ptar_aging[1]) && $ptar_aging[1]!='')?$ptar_aging[1]:'any';
                    }
                $options_str.='</select>';
                $options_str.='</div>
                    </div>
                    <div class="col-sm-4 form-inline">
                    <div class="form-group">
                        <div class="form-inline ar_aging_val_div ar_aging_div_'.$rule_id.'">
                            <label for="ar_aging_value">Amount $</label>
                            <input type="text" name="ar_aging_value'.$rule_id.'" id="ar_aging_value'.$rule_id.'" value="'.$ptar_aging_value[$rule_id].'" class="form-control" placeholder="Enter Amount" style="width:100%!important;"/>
                        </div>
                    </div>
                    
                </div>';
                
                $options_str.='<div class="col-sm-4 form-inline">
                    <div class="form-group multiselect">
                        <label for="ar_group'.$rule_id.'">Group</label>
                        <select class="selectpicker" multiple data-done-button="true" data-actions-box="true" data-width="100%"   data-size="10" id="ar_group'.$rule_id.'" name="ar_group'.$rule_id.'[]"  data-selected-text-format="count > 2">';
                $options_str.=$groupName;
                $options_str.='</select>
                    </div>
                    </div>
                    </div><div class="row"><div class="clearfix"></div><br /><div class="col-sm-4 form-inline">
                    <div class="form-group multiselect">
                        <label for="ar_facility'.$rule_id.'">Facility</label>
                        <select class="selectpicker minimal selecicon" multiple data-done-button="true" data-size="10"  data-width="100%" data-actions-box="true" id="ar_facility'.$rule_id.'" name="ar_facility'.$rule_id.'[]"  data-selected-text-format="count > 2">';
                $options_str.=$pos_facilities_str;
                $options_str.='</select>
                    </div>
                    </div><div class="col-sm-4 form-inline">
                    <div class="form-group multiselect">
                    <label for="ar_ins_group'.$rule_id.'"> Insurance Group</label>
                        <select class="selectpicker" multiple id="ar_ins_group'.$rule_id.'" name="ar_ins_group'.$rule_id.'[]"  data-done-button="true" data-actions-box="true" data-width="100%" data-size="10"  data-selected-text-format="count > 2">';
                $options_str.=$ins_group_options;
                $options_str.='</select>
                    </div>
                    </div><div class="col-sm-4 form-inline">
                    <div class="form-group multiselect">
                        <label for="ar_ins_comp'.$rule_id.'">Insurance</label>
                        <select class="selectpicker minimal selecicon" multiple data-done-button="true" data-actions-box="true"  data-width="100%" data-size="10" id="ar_ins_comp'.$rule_id.'" name="ar_ins_comp'.$rule_id.'[]"  data-selected-text-format="count > 2">';
                $options_str.=$insComName_options;
                $options_str.='</select>
                    </div>
                    </div>
                </div>    
                </div>    
                ';
                echo $options_str;
                
                break;
            case '5':
                $options_str='';
                $trans_arr=array("discount"=>"Discount","denial"=>"Denied","deductible"=>"Deductible","write_off"=>"Write Off");
                $trans_value=array();
                $options_str='<select class="selectpicker" multiple name="transaction'.$rule_id.'[]" id="transaction'.$rule_id.'" data-width="100%"  data-actions-box="true">';
                    foreach($trans_arr as $key => $val) {
                        $transaction_ar = explode(', ', $rulerow['transaction']);
                        $sel=(in_array($key,$transaction_ar))?' selected ':'';
                        $options_str.='<option value="'.$key.'" '.$sel.' >'.$val.'</option>';
                    }
                $options_str.='</select>';
                
                echo $options_str;
                
                break;
        }
        
    }

}


if($get_options == 'submit_form') {    //pre($_POST);die;
    $rule_selected=array();
    $reason_code_selected=array();
    $patientStatus_selected=array();

    if((isset($_POST['ss_type']) && trim($_POST['ss_type'])=='schedule_status') && isset($_POST['ss_id']) && $_POST['ss_id']>0) {
        $rule_sql = "SELECT id,status_name as tm_rule_name FROM schedule_status where status=1 order by status_name asc";
    } else {
        $rule_sql = "SELECT * FROM tm_rules";
    }
    $rule_rs = imw_query($rule_sql);
    $success=false;
    if ($rule_rs && imw_num_rows($rule_rs) > 0) {
        while ($rules_row = imw_fetch_assoc($rule_rs)) {
            //pre($rules_row);
            if((isset($_POST['ss_type']) && trim($_POST['ss_type'])=='schedule_status') && isset($_POST['ss_id']) && $_POST['ss_id']>0) {
                $rules_row['type']='schedule_status';
                $rules_row['ss_id']=$rules_row['id'];
                $rules_row['id']='ss'.$rules_row['id'];
                $rules_row['tm_rcat_id']='2';
            }
            $list_id=(isset($_POST['list_id']) && $_POST['list_id']!='')?$_POST['list_id']:'';
            $cat_id=$rules_row['tm_rcat_id'];
            if(isset($_POST['rulename_'.$rules_row['id']]) && $_POST['rulename_'.$rules_row['id']] != '') {
                $ruleid= str_replace('rulename_', '', $_POST['rulename_'.$rules_row['id']]);
                $rule_selected[$rules_row['id']]=$_POST['rulename_'.$rules_row['id']];
                                
                $reason_code='';
                if(isset($_POST['reason_code_'.$rules_row['id']]) && $_POST['reason_code_'.$rules_row['id']] != '') {
                    $reason_code=implode(', ', $_POST['reason_code_'.$rules_row['id']]);
                }
                $patientStatus='';
                if(isset($_POST['patientStatus'.$rules_row['id']]) && $_POST['patientStatus'.$rules_row['id']] != '') {
                    $patientStatus=implode(', ', $_POST['patientStatus'.$rules_row['id']]);
                }
                
                $patientAccStatus='';
                if(isset($_POST['pt_account_status'.$rules_row['id']]) && $_POST['pt_account_status'.$rules_row['id']] != '') {
                    $patientAccStatus=implode(', ', $_POST['pt_account_status'.$rules_row['id']]);
                }
                
                $appt_procedure='';
                if(isset($_POST['appt_procedure'.$rules_row['id']]) && $_POST['appt_procedure'.$rules_row['id']] != '') {
                    $appt_procedure=implode(',', $_POST['appt_procedure'.$rules_row['id']]);
                }
                
                $appt_ref_phy='';
                if(isset($_POST['appt_ref_phy'.$rules_row['id']]) && $_POST['appt_ref_phy'.$rules_row['id']] != '') {
                    $appt_ref_phy=implode(',', $_POST['appt_ref_phy'.$rules_row['id']]);
                }
                
                $ss_id=0;
                $type='';
                if((isset($_POST['ss_type']) && trim($_POST['ss_type'])=='schedule_status') && isset($_POST['ss_id']) && $_POST['ss_id']>0) {
                    $ss_id=$rules_row['ss_id'];
                    $type=trim($_POST['ss_type']);
                }
                
                $providers='';
                if(isset($_POST['sel_provider'.$rules_row['id']]) && $_POST['sel_provider'.$rules_row['id']] != '') {
                    $providers=implode(', ', $_POST['sel_provider'.$rules_row['id']]);
                }
                
                $facilities='';
                if(isset($_POST['facilities'.$rules_row['id']]) && $_POST['facilities'.$rules_row['id']] != '') {
                    $facilities=implode(', ', $_POST['facilities'.$rules_row['id']]);
                }
                $transaction='';
                if(isset($_POST['transaction'.$rules_row['id']]) && $_POST['transaction'.$rules_row['id']] != '') {
                    $transaction=implode(', ', $_POST['transaction'.$rules_row['id']]);
                }
                
                if($providers || $facilities) {
                    $patientApptStatus=$providers.'||'.$facilities;
                }
                $ar_aging_str='';
                $ar_aging='';
                if(isset($_POST['ar_aging'.$rules_row['id']]) && $_POST['ar_aging'.$rules_row['id']] != '') {
                    $ar_aging=$_POST['ar_aging'.$rules_row['id']];
                }
                $ar_aging_value='';
                if((isset($_POST['ar_aging_value'.$rules_row['id']]) && $_POST['ar_aging_value'.$rules_row['id']] != '') || $_POST['ar_aging_value'.$rules_row['id']]==0) {
                    $ar_aging_value=$_POST['ar_aging_value'.$rules_row['id']];
                }
                if($ar_aging_value==0)$ar_aging_value='any';
                if($ar_aging && ($ar_aging_value)) {
                    $ar_aging_str = $ar_aging.'#'.$ar_aging_value;
                } else {
                    $ar_aging_str = $ar_aging;
                }
                
                $tm_group='';
                if(isset($_POST['ar_group'.$rules_row['id']]) && $_POST['ar_group'.$rules_row['id']] != '') {
                    $tm_group=implode(',', $_POST['ar_group'.$rules_row['id']]);
                }
                
                $ar_facility='';
                if(isset($_POST['ar_facility'.$rules_row['id']]) && $_POST['ar_facility'.$rules_row['id']] != '') {
                    $ar_facility=implode(',', $_POST['ar_facility'.$rules_row['id']]);
                }
                $tm_ins_group='';
                if(isset($_POST['ar_ins_group'.$rules_row['id']]) && $_POST['ar_ins_group'.$rules_row['id']] != '') {
                    $tm_ins_group=implode(',', $_POST['ar_ins_group'.$rules_row['id']]);
                }
                $tm_ins_comp='';
                if(isset($_POST['ar_ins_comp'.$rules_row['id']]) && $_POST['ar_ins_comp'.$rules_row['id']] != '') {
                    $tm_ins_comp=implode(',', $_POST['ar_ins_comp'.$rules_row['id']]);
                }
                $tm_cpt_code='';
                if(isset($_POST['tm_cpt_code'.$rules_row['id']]) && $_POST['tm_cpt_code'.$rules_row['id']] != '') {
                    $tm_cpt_code=implode(',', $_POST['tm_cpt_code'.$rules_row['id']]);
                }

                $user_groups = implode(', ', $_POST['user_group']);
                $user_names = implode(', ', $_POST['user_name']);
                $comment = addslashes($_POST['comment']);
                
                if(isset($_POST['ss_type']) && trim($_POST['ss_type'])=='schedule_status' && isset($_POST['ss_id']) && $_POST['ss_id']>0) {
                    $rule_row_id=0;
                } else {
                    $rule_row_id=$rules_row['id'];
                }
                if($list_id) {
                    $query = "Update tm_rules_list set rule_id=".$rule_row_id.", cat_id=".$cat_id.", reason_code='".$reason_code."', pt_status='".$patientStatus."', pt_account_status='".$patientAccStatus."', transaction='".$transaction."', ar_aging='".$ar_aging_str."', user_group='".$user_groups."', user_name='".$user_names."', pt_appt_status='".$patientApptStatus."',ss_id=".$ss_id.", ss_type='".$type."', appt_procedure='".$appt_procedure."', appt_ref_phy='".$appt_ref_phy."',tm_group='".$tm_group."', ar_facility='".$ar_facility."', tm_ins_group='".$tm_ins_group."', tm_ins_comp='".$tm_ins_comp."', tm_cpt_code='".$tm_cpt_code."',comment='".$comment."', operator_id='".$_SESSION['authId']."' where id='".$list_id."' ";
                } else {
                    $query = "INSERT INTO tm_rules_list(rule_id, cat_id, reason_code, pt_status, pt_account_status, transaction, user_group, user_name, pt_appt_status, ss_id, ss_type, appt_procedure, appt_ref_phy, ar_aging, tm_group,ar_facility,tm_ins_group,tm_ins_comp,tm_cpt_code,comment, operator_id) VALUES(".$rule_row_id.", ".$cat_id.", '".$reason_code."', '".$patientStatus."', '".$patientAccStatus."', '".$transaction."', '".$user_groups."', '".$user_names."', '".$patientApptStatus."', ".$ss_id.", '".$type."', '".$appt_procedure."', '".$appt_ref_phy."', '".$ar_aging_str."', '".$tm_group."', '".$ar_facility."', '".$tm_ins_group."', '".$tm_ins_comp."', '".$tm_cpt_code."', '".$comment."', '".$_SESSION['authId']."') ";
                }
                
                //echo $query;die;
                $rs = imw_query($query);
                if($rs) {
                    $success=true;
                }
            }
            
        }
    }

    if($success) {
        echo 'Rule saved successfully.';
    }else {
        echo 'Rule saving Failed.';
    }
    die;
}


function fetch_appt_status_options($rulerow,$listrow) {//pre($rulerow);pre($listrow);
    $ptapptstatus=array();
    $ptapptstatus = explode('||', $listrow['pt_appt_status']);
    if($listrow['ss_id']>0 || $rulerow['ss_id']>0) {
        $rule_id=($listrow['rule_id']?$listrow['rule_id']:$rulerow['id']);
        $type=($listrow['type']?$listrow['type']:$rulerow['type']);
        $ss_id=($listrow['ss_id']?$listrow['ss_id']:$rulerow['ss_id']);
    } else {
        $rule_id=($listrow['rule_id']?$listrow['rule_id']:$rulerow['id']);
        $type='';
        $ss_id='';
    }
    $options_str='';

    /* Get Users */
    $physician_arr = $sel_prov=array();
    $sql = "select id, fname, lname, mname from users where Enable_Scheduler = '1' and delete_status = '0' order by lname, fname";
    $sql_rs = imw_query($sql);
    if ($sql_rs && imw_num_rows($sql_rs) > 0) {
        while ($row = imw_fetch_assoc($sql_rs)) {
            $sel_prov=trim($ptapptstatus[0]);
            $prov_name = core_name_format($row['lname'], $row['fname'], $row['mname']);
            $physician_arr[$row['id']] = $prov_name;
            $sel=(in_array($row['id'],explode(', ',$sel_prov)))?' selected ':'';
            $prov_options.='<option value="'.$row['id'].'" '.$sel.'>'.$prov_name.'</option>';
        }
    }

    $facility_arr = $sel_fac=array();
    $qry = "select id, name from facility order by name";
    $sql_rs = imw_query($qry);
    if ($sql_rs && imw_num_rows($sql_rs) > 0) {
        while ($row = imw_fetch_assoc($sql_rs)) {
            $sel_fac=trim($ptapptstatus[1]);
            $facility_arr[$row['id']] = $row['name'];
            $sel=(in_array($row['id'],explode(',',$sel_fac)))?' selected ':'';
            $fac_options.='<option value="'.$row['id'].'" '.$sel.'>'.$row['name'].'</option>';
        }
    }

    $list_of_procs='';
    $qry = "SELECT id, proc, acronym, user_group, labels, ref_management FROM slot_procedures WHERE times = '' AND proc != '' AND doctor_id = 0 AND active_status = 'yes' ORDER BY proc";
    $res = imw_query($qry);
    if(imw_num_rows($res) > 0){
        while($arr_proc = imw_fetch_assoc($res))
        {
            $sel_appt_proc=trim($listrow['appt_procedure']);
            $sel=(in_array($arr_proc['id'],explode(',',$sel_appt_proc)))?' selected ':'';
//            if(DEFAULT_PRODUCT == "imwemr"){
//                $list_of_procs .= ((trim($arr_proc["acronym"]) == "") ? "<option $sel value=\"".$arr_proc["id"]."\" data-labels=\"".$arr_proc["labels"]."\">".$arr_proc["proc"]."</option>" : "<option $sel value=\"".$arr_proc["id"]."\" data-labels=\"".$arr_proc["labels"]."\" data-referral=\"".$arr_proc['ref_management']."\">".$arr_proc["acronym"]."</option>");
//            }else{
                $list_of_procs .= "<option $sel value=\"".$arr_proc["id"]."\" data-labels=\"".$arr_proc["labels"]."\" data-referral=\"".$arr_proc['ref_management']."\">".$arr_proc["proc"]."</option>";
           // }
            
        }

    }
        
    $ref_phy_arr = array();
    $ref_phy_options='';
    $qry = "select physician_Reffer_id, TRIM(CONCAT(IF(Title!='',CONCAT(Title,' '),''),LastName,', ', FirstName ,' ',MiddleName)) AS name from refferphysician
            WHERE delete_status=0
            AND LastName!=''
            AND FirstName!=''
            AND primary_id = 0
            order by TRIM(LastName) asc
            ";
    $sql_rs = imw_query($qry);
    if ($sql_rs && imw_num_rows($sql_rs) > 0) {
        while ($row = imw_fetch_assoc($sql_rs)) {
            if(trim($row['name'])!=''){
                $sel_ref_phy=trim($listrow['appt_ref_phy']);
                $ref_phy_arr[$row['physician_Reffer_id']] = $row['name'];
                $sel=(in_array($row['physician_Reffer_id'],explode(',',$sel_ref_phy)))?' selected ':'';
                $ref_phy_options.='<option value="'.$row['physician_Reffer_id'].'" '.$sel.'>'.$row['name'].'</option>';
            }
        }
    }

    $options_str='<div class="col-sm-6">
        <div class="form-group ">
            <label for="appt_procedure'.$rule_id.'">Procedure</label>
            <select class="selectpicker" multiple data-done-button="true" data-actions-box="true" data-width="100%"   data-size="10" id="appt_procedure'.$rule_id.'" name="appt_procedure'.$rule_id.'[]" >';
    $options_str.=$list_of_procs;
    $options_str.='</select>
        </div>
        </div><div class="col-sm-6">
        <div class="form-group ">
            <label for="sel_provider'.$rule_id.'">Physician</label>
            <select class="selectpicker minimal selecicon" multiple data-done-button="true" data-size="10" data-width="100%" data-actions-box="true" id="sel_provider'.$rule_id.'" name="sel_provider'.$rule_id.'[]">';
    $options_str.=$prov_options;
    $options_str.='</select>
        </div>
        </div><div class="clearfix"></div><br /><div class="col-sm-6">
        <div class="form-group ">
        <label for="appt_ref_phy'.$rule_id.'">Ref. Physician</label>
            <select class="selectpicker" multiple id="appt_ref_phy'.$rule_id.'" name="appt_ref_phy'.$rule_id.'[]"  data-done-button="true" data-actions-box="true" data-width="100%" data-size="10"   data-live-search="true">';
    $options_str.=$ref_phy_options;
    $options_str.='</select>
        </div>
        </div><div class="col-sm-6">
        <div class="form-group ">
            <label for="facilities'.$rule_id.'">Location</label>
            <select class="selectpicker minimal selecicon" multiple data-done-button="true" data-actions-box="true" data-width="100%" data-size="10" id="facilities'.$rule_id.'" name="facilities'.$rule_id.'[]">';
    $options_str.=$fac_options;

    $options_str.='</select>
        </div>
        </div>
    </div>
    <input type="hidden" name="ss_type" id="ss_type" value="'.$type.'" />
    <input type="hidden" name="ss_id" id="ss_id" value="'.$ss_id.'" />
    
    ';

    echo $options_str;
}

function get_cpt_arr() {
    $cpt_fee_arr = array();
    $cpt_qry = imw_query("select cpt_fee_id,cpt_prac_code from cpt_fee_tbl");
    while ($cpt_row = imw_fetch_array($cpt_qry)) {
        $cpt_fee_arr[$cpt_row['cpt_fee_id']] = $cpt_row['cpt_prac_code'];
    }
    return $cpt_fee_arr;
}
?>