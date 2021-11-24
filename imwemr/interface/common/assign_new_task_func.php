<?php
function get_cpt_arr() {
    $cpt_fee_arr = array();
    $cpt_qry = imw_query("select cpt_fee_id,cpt_prac_code from cpt_fee_tbl");
    while ($cpt_row = imw_fetch_array($cpt_qry)) {
        $cpt_fee_arr[$cpt_row['cpt_fee_id']] = $cpt_row['cpt_prac_code'];
    }
    return $cpt_fee_arr;
}

//A/R Aging Category Insert function
function assign_ar_aging_task_rules_to($ar_insert_arr) {
    
    //pre($ar_insert_arr); die;
    foreach($ar_insert_arr as $key=>$insert_array) {
        foreach($insert_array as $insert_arr) {
            
            
            $patient_id_task = (isset($insert_arr['patientid']) && $insert_arr['patientid'] != '') ? $insert_arr['patientid'] : $patientid_task;
            $patient_name = (isset($insert_arr['patient_name']) && $insert_arr['patient_name'] != '') ? $insert_arr['patient_name'] : '';
            $operatorid_task = (isset($insert_arr['operatorid']) && $insert_arr['operatorid'] != '') ? $insert_arr['operatorid'] : '';

            $section_task = $insert_arr['section'];

            $sub_section_task = '';
            if (isset($insert_arr['sub_section']) && $insert_arr['sub_section'] != '')
                $sub_section_task = trim($insert_arr['sub_section']);
            $amount_due = 0;
            if (isset($insert_arr['amount_due']) && $insert_arr['amount_due'] != '')
                $amount_due = trim($insert_arr['amount_due']);
            $days_aged = '';
            if (isset($insert_arr['days_aged']) && $insert_arr['days_aged'] != '')
                $days_aged = trim($insert_arr['days_aged']);
            $ar_comment = '';
            if (isset($insert_arr['ar_comment']) && $insert_arr['ar_comment'] != '')
                $ar_comment = trim($insert_arr['ar_comment']);


            $appt_type = '';
            if (isset($insert_arr['appt_type']) && $insert_arr['appt_type'] != '')
                $appt_type = trim($insert_arr['appt_type']);
            $appt_date = '';
            if (isset($insert_arr['appt_date']) && $insert_arr['appt_date'] != '')
                $appt_date = trim($insert_arr['appt_date']);
            $appt_time = '';
            if (isset($insert_arr['appt_time']) && $insert_arr['appt_time'] != '')
                $appt_time = trim($insert_arr['appt_time']);
            $appt_status = '';
            if (isset($insert_arr['appt_status']) && $insert_arr['appt_status'] != '')
                $appt_status = trim($insert_arr['appt_status']);
            $appt_facility_id = '';
            if (isset($insert_arr['sa_facility_id']) && $insert_arr['sa_facility_id'] != '')
                $appt_facility_id = trim($insert_arr['sa_facility_id']);
            $appt_comment = '';
            if (isset($insert_arr['appt_comment']) && $insert_arr['appt_comment'] != '')
                $appt_comment = trim($insert_arr['appt_comment']);

            $status_id = '';
            if (isset($insert_arr['status_id']) && $insert_arr['status_id'] != '')
                $status_id = trim($insert_arr['status_id']);
            $encounter_id = '';
            if (isset($insert_arr['encounter_id']) && $insert_arr['encounter_id'] != '')
                $encounter_id = trim($insert_arr['encounter_id']);
            $date_of_service = '';
            if (isset($insert_arr['date_of_service']) && $insert_arr['date_of_service'] != '')
                $date_of_service = trim($insert_arr['date_of_service']);
            $cpt_code = '';
            if (isset($insert_arr['cpt_code']) && $insert_arr['cpt_code'] != '')
                $cpt_code = trim($insert_arr['cpt_code']);

            $task_group = '';
            if (isset($insert_arr['task_group']) && $insert_arr['task_group'] != '')
                $task_group = trim($insert_arr['task_group']);
            $task_group_id = '';
            if (isset($insert_arr['task_group_id']) && $insert_arr['task_group_id'] != '')
                $task_group_id = ($insert_arr['task_group_id']);
            $task_ins_group = '';
            if (isset($insert_arr['task_ins_group']) && $insert_arr['task_ins_group'] != '')
                $task_ins_group = trim($insert_arr['task_ins_group']);
            $task_ins_group_id = '';
            if (isset($insert_arr['task_ins_group_id']) && $insert_arr['task_ins_group_id'] != '')
                $task_ins_group_id = ($insert_arr['task_ins_group_id']);
            $task_ins_comp = '';
            if (isset($insert_arr['task_ins_comp']) && $insert_arr['task_ins_comp'] != '')
                $task_ins_comp = trim($insert_arr['task_ins_comp']);
            $task_ins_comp_id = '';
            if (isset($insert_arr['task_ins_comp_id']) && $insert_arr['task_ins_comp_id'] != '')
                $task_ins_comp_id = ($insert_arr['task_ins_comp_id']);
            $task_facility = '';
            if (isset($insert_arr['task_facility']) && $insert_arr['task_facility'] != '')
                $task_facility = trim($insert_arr['task_facility']);
            $task_facility_id = '';
            if (isset($insert_arr['task_facility_id']) && $insert_arr['task_facility_id'] != '')
                $task_facility_id = trim($insert_arr['task_facility_id']);

            $current_date = time();
            $changed_value = $status_id;

            if ($sub_section_task == 'patient') {
                $tm_rules = 'Patient';
            }
            if ($sub_section_task == 'insurance') {
                $tm_rules = 'Insurance';
            }
            
            
            
            $rule_qry_rs = imw_query("SELECT id FROM tm_rules where tm_rule_name='$tm_rules' ");
            $rule_row = imw_fetch_assoc($rule_qry_rs);
            $rule_sql = "SELECT * FROM tm_rules_list where rule_id=" . $rule_row['id'] . " and $section_task!='' and rule_status=0 order by id asc";

            $rule_rs = imw_query($rule_sql);
            $insert_id = '';
            while ($row = imw_fetch_assoc($rule_rs)) {
                $insert_check_qry="SELECT * FROM tm_assigned_rules
                    where patientid=".$patient_id_task."
                    and section_name='".$section_task."'
                    and encounter_id=".$encounter_id."
                    and date_of_service='".$date_of_service."'
                    and amount_due=".$amount_due."
                    and rule_list_id=".$row['id']."
                    and appt_facility_id=".$task_facility_id."
                    and ins_comp='".$task_ins_comp."'
                    and ins_group='".$task_ins_group."'
                    and group_name='".$task_group."'
                    and status=0";
            
                $task_check_rs= imw_query($insert_check_qry);
                if($task_check_rs && imw_num_rows($task_check_rs)>0) {
                    continue;
                }
                $task_comments=$row['comment'];
                $value_arr = explode('#', $row['ar_aging']);
                $agingDays = trim($value_arr[0]);
                $aging_amt = trim($value_arr[1]);
                if($aging_amt=='any'){$aging_amt=0;}
                $agingDays_arr = explode('-', $agingDays);

                $aging_start = trim($agingDays_arr[0]);
                $aging_to = (isset($agingDays_arr[1]) && $agingDays_arr[1] != '') ? trim($agingDays_arr[1]) : '';

                if ($aging_to != '' || $aging_start != '') {
                    $ar_facility=($row['ar_facility'])?explode(',',$row['ar_facility']):array();
                    $tm_group=($row['tm_group'])?explode(',',$row['tm_group']):array();
                    $tm_ins_group=($row['tm_ins_group'])?explode(',',$row['tm_ins_group']):array();
                    $tm_ins_comp=($row['tm_ins_comp'])?explode(',',$row['tm_ins_comp']):array();

                    $check=false;
                    $fac_check=false;
                    $ins_comp_check=false;
                    $ins_grp_check=false;
                    $grp_check=false;
                    if($row['tm_cpt_code']=='' && (empty($ar_facility)==false || empty($tm_group)==false || empty($tm_ins_group)==false || empty($tm_ins_comp)==false) && $section_task=='ar_aging') {
                        foreach($task_ins_comp_id as $comp_id) {
                            if(in_array($comp_id, $tm_ins_comp))
                                $ins_comp_check=true;
                        }
                        foreach($task_ins_group_id as $ins_grp_id) {
                            if(in_array($ins_grp_id, $tm_ins_group))
                                $ins_grp_check=true;
                        }
                        $check=(in_array($task_facility_id, $ar_facility) && in_array($task_group_id, $tm_group) && $ins_comp_check && $ins_grp_check );
                    }
                    

                  // var_dump(((($aging_to == '' && $days_aged >= $aging_start) || ($days_aged >= $aging_start && $days_aged <= $aging_to)) && $aging_amt<=$amount_due && $fac_check && $grp_check && $ins_comp_check && $ins_grp_check));
                    if ((($aging_to == '' && $days_aged >= $aging_start) || ($days_aged >= $aging_start && $days_aged <= $aging_to)) && $aging_amt<=$amount_due && $check) {
                        $insert_qry = "INSERT INTO tm_assigned_rules(section_name,rule_list_id,status,changed_value,encounter_id,date_of_service,cpt_code,
                                         appt_type,appt_date,appt_time,appt_status,appt_facility_id,appt_comment,
                                         amount_due,days_aged,ar_comment,
                                         patientid, patient_name, operatorid,
                                         group_name, ins_group, ins_comp,comments)
                                  VALUES('" . $section_task . "', " . $row['id'] . ", '0', '" . $changed_value . "','" . $encounter_id . "','" . $date_of_service . "','" . $cpt_code . "',
                                      '" . $appt_type . "','" . $appt_date . "','" . $appt_time . "','" . $appt_status . "','" . $task_facility_id . "','" . $appt_comment . "',
                                          '" . $amount_due . "','" . $days_aged . "','" . $ar_comment . "',
                                 '" . $patient_id_task . "', '" . $patient_name . "', '" . $operatorid_task . "',
                                '" . $task_group . "', '" . $task_ins_group . "', '" . $task_ins_comp . "', '" . $task_comments . "') ";

                        $rs = imw_query($insert_qry);

                        $insert_id = imw_insert_id();
                    }
                }
            }
            //continue;
        }
        //continue;
    }
    //die;
}

//Appointment Category Insert function
function assign_appt_task_rules_to($insert_arr) {
    $patient_id_task = (isset($insert_arr['patientid']) && $insert_arr['patientid'] != '') ? $insert_arr['patientid'] : $patientid_task;
    $patient_name = (isset($insert_arr['patient_name']) && $insert_arr['patient_name'] != '') ? $insert_arr['patient_name'] : '';
    $operatorid_task = (isset($insert_arr['operatorid']) && $insert_arr['operatorid'] != '') ? $insert_arr['operatorid'] : '';
    $sa_doctor_id_task = (isset($insert_arr['sa_doctor_id']) && $insert_arr['sa_doctor_id'] != '') ? $insert_arr['sa_doctor_id'] : '';

    $section_task = $insert_arr['section'];

    $sub_section_task = '';
    if (isset($insert_arr['sub_section']) && $insert_arr['sub_section'] != '')
        $sub_section_task = trim($insert_arr['sub_section']);
    $appt_type = '';
    if (isset($insert_arr['appt_type']) && $insert_arr['appt_type'] != '')
        $appt_type = trim($insert_arr['appt_type']);
    $appt_date = '';
    if (isset($insert_arr['appt_date']) && $insert_arr['appt_date'] != '')
        $appt_date = trim($insert_arr['appt_date']);
    $appt_time = '';
    if (isset($insert_arr['appt_time']) && $insert_arr['appt_time'] != '')
        $appt_time = trim($insert_arr['appt_time']);
    $appt_status = '';
    if (isset($insert_arr['appt_status']) && $insert_arr['appt_status'] != '')
        $appt_status = trim($insert_arr['appt_status']);
    $appt_facility_id = '';
    if (isset($insert_arr['sa_facility_id']) && $insert_arr['sa_facility_id'] != '')
        $appt_facility_id = trim($insert_arr['sa_facility_id']);
    $appt_comment = '';
    if (isset($insert_arr['appt_comment']) && $insert_arr['appt_comment'] != '')
        $appt_comment = trim($insert_arr['appt_comment']);

    $status_id = '';
    if (isset($insert_arr['status_id']) && $insert_arr['status_id'] != '')
        $status_id = trim($insert_arr['status_id']);
    $encounter_id = '';
    if (isset($insert_arr['encounter_id']) && $insert_arr['encounter_id'] != '')
        $encounter_id = trim($insert_arr['encounter_id']);
    $date_of_service = '';
    if (isset($insert_arr['date_of_service']) && $insert_arr['date_of_service'] != '')
        $date_of_service = trim($insert_arr['date_of_service']);
    $cpt_code = '';
    if (isset($insert_arr['cpt_code']) && $insert_arr['cpt_code'] != '')
        $cpt_code = trim($insert_arr['cpt_code']);
    $procedureid = '';
    if (isset($insert_arr['procedureid']) && $insert_arr['procedureid'] != '')
        $procedureid = trim($insert_arr['procedureid']);
    $ref_phy_id = '';
    if (isset($insert_arr['ref_phy_id']) && $insert_arr['ref_phy_id'] != '')
        $ref_phy_id = trim($insert_arr['ref_phy_id']);
    $app_status_id = '';
    if (isset($insert_arr['app_status_id']) && $insert_arr['app_status_id'] != '')
        $app_status_id = trim($insert_arr['app_status_id']);
    $task_appt_id = '';
    $appt_task_row=array();
    if (isset($insert_arr['task_appt_id']) && $insert_arr['task_appt_id'] != '') {
        $task_appt_id = trim($insert_arr['task_appt_id']);
        $appt_task_sql='select id,task_appt_id,appt_type,appt_date,appt_time,appt_status,appt_facility_id,appt_ref_phy_id,appt_comment,sa_doctor_id_task,operatorid from tm_assigned_rules where task_appt_id="'.$task_appt_id.'" and task_appt_id<>0 ';
        $appt_task_res=imw_query($appt_task_sql);
        if($appt_task_res && imw_num_rows($appt_task_res)==1){
            $appt_task_row=imw_fetch_assoc($appt_task_res);
        }
    }

    $current_date = time();
    $changed_value = $status_id;

    $tm_rules=false;
    if ($sub_section_task == 'appt_canceled') {
        $tm_rules = 'Appointment Canceled';
    }
    if ($sub_section_task == 'appt_created') {
        $tm_rules = 'Appointment Created';
    }
    if ($sub_section_task == 'appt_deleted') {
        $tm_rules = 'Appointment Deleted';
    }
    if ($sub_section_task == 'appt_no_show') {
        $tm_rules = 'Appointment No Show';
    }
    if ($sub_section_task == 'appt_reschedule') {
        $tm_rules = 'Appointment Rescheduled';
    }

    if($tm_rules) {
        $rule_qry_rs = imw_query("SELECT id FROM tm_rules where tm_rule_name='$tm_rules' ");
        $s_status=false;
    } else {
        $rule_qry_rs = imw_query("SELECT id,status_name FROM schedule_status where id=".$app_status_id." and status=1 ");
        $s_status=true;
    }
    $rule_row = imw_fetch_assoc($rule_qry_rs);
    //$rule_sql = "SELECT * FROM tm_rules_list where rule_id=" . $rule_row['id'] . " and $section_task!='' and rule_status=0 order by id asc";
    
    if($s_status) {
        $tm_where=" and ss_id='".trim($rule_row['id'])."' ";
    } else {
        $tm_where=" and rule_id='".trim($rule_row['id'])."' ";
    }
    
    $rule_sql = "SELECT * FROM tm_rules_list where $section_task!='' and appt_procedure!='' $tm_where and rule_status=0 order by id asc";
    //$rule_sql = "SELECT * FROM tm_rules_list where cat_id=2 and $section_task!='' and rule_status=0 order by id asc";

    $rule_rs = imw_query($rule_sql);
    $insert_id = '';
    while ($row = imw_fetch_assoc($rule_rs)) {
        $task_comments=$row['comment'];
        $value_arr = explode('||', $row['pt_appt_status']);
        $physician_str = (isset($value_arr[0]) && $value_arr[0]!='')?$value_arr[0]:'';
        $facilities_str = (isset($value_arr[1]) && $value_arr[1]!='')?$value_arr[1]:'';

        $physician_arr = explode(',', $physician_str);
        $facilities_arr = explode(',', $facilities_str);
        
        $tm_appt_procedure=explode(',',$row['appt_procedure']);
        $tm_appt_ref_phy=explode(',',$row['appt_ref_phy']);
        //if (in_array($sa_facility_id, $facilities_arr) || in_array($operatorid_task, $physician_arr)) {
        if (in_array($procedureid, $tm_appt_procedure) && (in_array($sa_doctor_id_task, $physician_arr) || in_array($ref_phy_id, $tm_appt_ref_phy) || in_array($sa_facility_id, $facilities_arr)) ) {
            if(empty($appt_task_row)==false) {
                $init = "UPDATE ";
                $where = " WHERE task_appt_id = '".$task_appt_id."' and id = '". $appt_task_row['id']."' ";
            }else{
                $init = "INSERT INTO ";
                $where = "";
            }
            $insert_qry = $init. " tm_assigned_rules set section_name='" . $section_task . "', rule_list_id=" . $row['id'] . ", status='0', changed_value='" . $changed_value . "', 
                    encounter_id='" . $encounter_id . "',date_of_service='" . $date_of_service . "',cpt_code='" . $cpt_code . "',
                    appt_type='" . $appt_type . "',appt_date='" . $appt_date . "',appt_time='" . $appt_time . "',appt_status='" . $appt_status . "', 
                    appt_facility_id='" . $appt_facility_id . "',appt_ref_phy_id='" . $ref_phy_id . "',appt_comment='" . $appt_comment . "',
                    patientid='" . $patient_id_task . "', patient_name='" . $patient_name . "', operatorid='" . $operatorid_task . "', sa_doctor_id_task='" . $sa_doctor_id_task . "', 
                    comments='" . $task_comments . "', task_appt_id='" . $task_appt_id . "' $where ";

            $rs = imw_query($insert_qry);

            $insert_id = imw_insert_id();
        } else if(empty($appt_task_row)==false) {
            $delete_comments= json_encode($appt_task_row);
            $init = "UPDATE ";
            $where = " WHERE task_appt_id = '".$task_appt_id."' and id = '". $appt_task_row['id']."' ";
            $del_qry = $init. " tm_assigned_rules set status='1', operatorid='".$_SESSION['authId']."', comments='" . $delete_comments . "' $where ";
            imw_query($del_qry);
        }
    }
}

//Accounting Category Insert function
function assign_acc_task_rules_to($insert_arr) {
    //SET INSURANCE COMPANY DROP DOWN
    $insGroupQryRes = imw_query("SELECT id, title FROM ins_comp_groups");
    $arrAllInsGroups[0] = 'No Insurance';
    $arrInsMapInsGroups[0]='0';
    while ($row = imw_fetch_array($insGroupQryRes)) {
        $ins_grp_id = $row['id'];
        $ins_grp_name = $row['title'];
        $arrAllInsGroups[$ins_grp_id] = trim($ins_grp_name);

        $qry = "SELECT id FROM insurance_companies WHERE groupedIn = '" . $row['id'] . "'";
        $res = imw_query($qry);
        $tmp_grp_ins_arr = array();
        if (imw_num_rows($res) > 0) {
            while ($det_row = imw_fetch_array($res)) {
                $arrInsMapInsGroups[$det_row['id']]= trim($ins_grp_id);
            }
        }
    }
    $ins_comp_arr = array();
    $insQryRes = insurance_provider_xml_extract();
    for ($i = 0; $i < count($insQryRes); $i++) {
        if(!$insQryRes[$i]['attributes']) continue;
        if($insQryRes[$i]['attributes']['insCompName'] != 'No Insurance') {
            $ins_id = $insQryRes[$i]['attributes']['insCompId'];
            $ins_name = $insQryRes[$i]['attributes']['insCompINHouseCode'];
            if ($ins_name == '') {
                $ins_name = $insQryRes[$i]['attributes']['insCompName'];
                if (strlen($ins_name) > 20) {
                    $ins_name = substr($ins_name, 0, 20) . '....';
                }
            }
            $ins_comp_arr[$ins_id] = $ins_name;
        }
    }

   
    $patient_id_task = (isset($insert_arr['patientid']) && $insert_arr['patientid'] != '') ? $insert_arr['patientid'] : $patientid_task;
    $patient_name = (isset($insert_arr['patient_name']) && $insert_arr['patient_name'] != '') ? $insert_arr['patient_name'] : '';
    $operatorid_task = (isset($insert_arr['operatorid']) && $insert_arr['operatorid'] != '') ? $insert_arr['operatorid'] : $_SESSION['authId'];

    $section_task = $insert_arr['section'];
    $ins_group_id='';
    if (isset($insert_arr['task_ins_comp']) && $insert_arr['task_ins_comp'] != ''){
        $ins_group_id=$arrInsMapInsGroups[$insert_arr['task_ins_comp']];
        $insurance_group=$arrAllInsGroups[$ins_group_id];
        $insert_arr['task_ins_group']=$insurance_group;
    }
 
    $status_id = '';
    $statusIdArr = array();
    $statusArr = array();
    if($section_task=='reason_code' && isset($insert_arr['status_id']) && $insert_arr['status_id'] != '') {
        if (isset($insert_arr['status_id']) && $insert_arr['status_id'] != '') {
            $statusArr = explode('~~',trim($insert_arr['status_id']));
            $cas_typeArr=explode(',',$statusArr[0]);
            $cas_codeArr=explode(',',$statusArr[1]);

            foreach($cas_typeArr as $key=>$val) {
                if($cas_codeArr[$key] && $cas_codeArr[$key]!='') {
                    $statusIdArr[]=$val.' '.$cas_codeArr[$key];
                } else {
                    $statusIdArr[]=$val;
                }
            }
        }
        
    } else {
        if (isset($insert_arr['status_id']) && $insert_arr['status_id'] != '') {
            $status_id = trim($insert_arr['status_id']);
        }
        
    }
    
    
    $encounter_id = '';
    if (isset($insert_arr['encounter_id']) && $insert_arr['encounter_id'] != '')
        $encounter_id = trim($insert_arr['encounter_id']);
    $date_of_service = '';
    if (isset($insert_arr['date_of_service']) && $insert_arr['date_of_service'] != '')
        $date_of_service = trim($insert_arr['date_of_service']);
    $cpt_code = '';
    if (isset($insert_arr['cpt_code']) && $insert_arr['cpt_code'] != '')
        $cpt_code = trim($insert_arr['cpt_code']);
    $method = '';
    if (isset($insert_arr['method']) && $insert_arr['method'] != '')
        $method = trim($insert_arr['method']);
    $task_group = '';
    if (isset($insert_arr['task_group']) && $insert_arr['task_group'] != '')
        $task_group = trim($insert_arr['task_group']);
    $task_ins_group = '';
    if (isset($insert_arr['task_ins_group']) && $insert_arr['task_ins_group'] != '')
        $task_ins_group = trim($insert_arr['task_ins_group']);
    $task_ins_comp = '';
    if (isset($insert_arr['task_ins_comp']) && $insert_arr['task_ins_comp'] != ''){
        $task_ins_id = trim($insert_arr['task_ins_comp']);
        //$task_ins_comp=$ins_comp_arr[$task_ins_id];
        $task_ins_comp=$task_ins_id;
    }

    $current_date = time();
    $changed_value = $status_id;
    
    //tm_assigned_rules
    if ($section_task == 'encounter_deleted' || $section_task == 'payment_deleted_edited') {
        if ($section_task == 'encounter_deleted') {
            $tm_rules = 'Encounter Deleted';
        }
        if ($section_task == 'payment_deleted_edited') {
            $tm_rules = 'Payment Deleted or Edited';
        }
        $rule_qry_rs = imw_query("SELECT id FROM tm_rules where tm_rule_name='$tm_rules' ");
        $rule_row = imw_fetch_assoc($rule_qry_rs);
        $rule_sql = "SELECT * FROM tm_rules_list where rule_id=" . $rule_row['id'] . " and rule_status=0 order by id asc";

        $rule_rs = imw_query($rule_sql);
        $insert_id = '';
        while ($row = imw_fetch_assoc($rule_rs)) {
            $task_comments=$row['comment'];
            $insert_qry = "INSERT INTO tm_assigned_rules(section_name,rule_list_id,status,changed_value,encounter_id,date_of_service,cpt_code,
                        patientid, patient_name, operatorid,comments)
                     VALUES('" . $section_task . "', " . $row['id'] . ", '0', '" . $changed_value . "','" . $encounter_id . "','" . $date_of_service . "','" . $cpt_code . "',
                    '" . $patient_id_task . "', '" . $patient_name . "', '" . $operatorid_task . "', '" . $task_comments . "') ";

            $rs = imw_query($insert_qry);

            $insert_id = imw_insert_id();
        }
    } else {
        if ($section_task == 'transaction_deleted') {
            $section_task = 'transaction';
            $trans_arr=array("discount"=>"Discount","denial"=>"Denied","deductible"=>"Deductible","write_off"=>"Write Off");
            foreach($trans_arr as $tkey=>$tval){
                if($status_id==$tval){
                    $status_id=$tkey;
                }
            }
            $changed_value = $status_id;
        }
        $rule_sql = "SELECT * FROM tm_rules_list where $section_task!='' and rule_status=0 order by id asc";

        $rule_rs = imw_query($rule_sql);
        $insert_id = '';
        while ($row = imw_fetch_assoc($rule_rs)) {
            $value_arr = explode(', ', $row[$section_task]);
            $task_comments=$row['comment'];
            $tm_cpt_code=($row['tm_cpt_code'])?explode(',',$row['tm_cpt_code']):array();
            if(empty($tm_cpt_code)==false) {
                $cpt_qry = imw_query("select cpt_fee_id,cpt_prac_code from cpt_fee_tbl where cpt_prac_code=$cpt_code");
                $cpt_row = imw_fetch_assoc($cpt_qry);
                $cpt_fee_id=$cpt_row['cpt_fee_id'];
            }
            $tm_group=($row['tm_group'])?explode(',',$row['tm_group']):array();
            $tm_ins_group=($row['tm_ins_group'])?explode(',',$row['tm_ins_group']):array();
            $tm_ins_comp=($row['tm_ins_comp'])?explode(',',$row['tm_ins_comp']):array();

            if($row['ar_facility']=='' && (empty($tm_cpt_code)==false || empty($tm_group)==false || empty($tm_ins_group)==false || empty($tm_ins_comp)==false) && $section_task=='reason_code') {
                $check=(array_intersect($statusIdArr, $value_arr) && in_array($cpt_fee_id, $tm_cpt_code) && in_array($task_group, $tm_group) && (in_array($ins_group_id, $tm_ins_group) || in_array($task_ins_comp, $tm_ins_comp)) );
                $changed_value = implode(',',array_intersect($statusIdArr, $value_arr));
            } else {
                if($section_task=='reason_code') {
                    $check=array_intersect($statusIdArr, $value_arr);
                    $changed_value = implode(',',$check);
                } else {
                    $check=in_array($status_id, $value_arr);
                }
            }

            if ($check) {
                $insert_qry = "INSERT INTO tm_assigned_rules(section_name,rule_list_id,status,changed_value,encounter_id,date_of_service,cpt_code,
                            patientid, patient_name, operatorid,
                            group_name,	ins_group, ins_comp, comments)
                         VALUES('" . $section_task . "', " . $row['id'] . ", '0', '" . $changed_value . "','" . $encounter_id . "','" . $date_of_service . "','" . $cpt_code . "',
                        '" . $patient_id_task . "', '" . $patient_name . "', '" . $operatorid_task . "',
                        '" . $task_group . "', '" . $task_ins_group . "', '" . $task_ins_comp . "', '" . $task_comments . "') ";

                $rs = imw_query($insert_qry);

                $insert_id = imw_insert_id();
            }
        }
    }
}

function inbound_fax_task($inbound_fax_id) {
    if ($inbound_fax_id) {
        $section_task = 'Incoming Fax';

        $rule_qry_rs = imw_query("SELECT id FROM tm_rules where tm_rule_name='" . $section_task . "' ");
        $rule_row = imw_fetch_assoc($rule_qry_rs);
        $rule_sql = "SELECT id,comment FROM tm_rules_list where rule_id=" . $rule_row['id'] . " and rule_status=0 order by id asc";

        $rule_rs = imw_query($rule_sql);
        while ($row = imw_fetch_assoc($rule_rs)) {
            $task_comments=$row['comment'];
            $insert_qry = "INSERT INTO tm_assigned_rules(section_name,rule_list_id,status,changed_value,inbound_fax_id,comments)
                     VALUES('" . $section_task . "', " . $row['id'] . ", '0', '" . $changed_value . "', '" . $inbound_fax_id . "', '" . $task_comments . "') ";

            $rs = imw_query($insert_qry);
            $insert_id = imw_insert_id();
        }
    }
}

function outbound_fax_status_task($data,$fax_id,$status) {
    if ( strtolower($status) !== 'success' && $fax_id!=='' ) {

        $sql='Select sfl.updox_status,sfl.patient_id,sfl.fax_type,sfl.updox_id,sfl.section_name,sfl.fax_number,sfl.operator_id,  
                pclt.cc1_ref_phy_id,pclt.cc2_ref_phy_id,pclt.cc3_ref_phy_id,pclt.templateName,pclt.patient_consult_letter_to  
                from send_fax_log_tbl sfl
                left join patient_consult_letter_tbl pclt on(pclt.patient_consult_id=sfl.patient_consult_id)
                where sfl.updox_id="'.$fax_id.'" 
                and ( sfl.section_name="consult" 
                     OR sfl.section_name="report" 
                     OR sfl.section_name="savedconsult" ) 
                    ';
        $result=imw_query($sql);
        if($result && imw_num_rows($result)>0) {
            $result_row=imw_fetch_assoc($result);
            //if(isset($result_row["section_name"]) && strtolower($result_row["section_name"])=='consult') {
            $section_task = 'Outgoing Fax';
            //$outbound_fax['FaxType']=$result_row["fax_type"];
            $outbound_fax['FaxId']=$result_row["updox_id"];
            $outbound_fax['FaxNumber']=$result_row["fax_number"];
            $fax_to='';
            switch ( strtolower($result_row["fax_type"]) ) {
                case 'primary':
                    $fax_to=$result_row["patient_consult_letter_to"];
                    break;
                case 'cc1':
                    $fax_to=$result_row["cc1_ref_phy_id"];
                    break;
                case 'cc2':
                    $fax_to=$result_row["cc2_ref_phy_id"];
                    break;
                case 'cc3':
                    $fax_to=$result_row["cc3_ref_phy_id"];
                    break;
            }   
            if($fax_to!='' && is_numeric($fax_to) && $fax_to!='0') {
                $sql	=   "SELECT CONCAT(`LastName`, ', ', `FirstName`) AS 'refPhyLabel' FROM `refferphysician` WHERE `physician_Reffer_id`=".$fax_to;
                $resp	=   imw_query($sql);
                $resp	=   imw_fetch_assoc($resp);
                $fax_to  =   $resp['refPhyLabel'];
            }
            $outbound_fax['FaxTo']=$fax_to;
            $outbound_fax['FaxStatus']=$status;

            $outbound_fax_str=json_encode($outbound_fax);
            //$return_response=json_encode($data);
            $return_response='';
            $patient_id_task = $result_row["patient_id"];
            $operatorid_task = ($_SESSION['authId'])?$_SESSION['authId']:$result_row["operator_id"];

            $rule_qry_rs = imw_query("SELECT id FROM tm_rules where tm_rule_name='" . $section_task . "' ");
            $rule_row = imw_fetch_assoc($rule_qry_rs);
            $rule_sql = "SELECT id,comment FROM tm_rules_list where rule_id=" . $rule_row['id'] . " and rule_status=0 order by id asc";

            $rule_rs = imw_query($rule_sql);
            if($rule_rs && imw_num_rows($rule_rs)>0){
                while ($row = imw_fetch_assoc($rule_rs)) {
                    $task_comments=$row['comment'];
                    $insert_qry = "INSERT INTO tm_assigned_rules SET 
                                    section_name='".$section_task."',
                                    rule_list_id=".((int)$row['id']).",
                                    status='0',
                                    changed_value='".$return_response."',
                                    outbound_fax='".$outbound_fax_str."',
                                    comments='".$task_comments."',
                                    patientid=".((int)$patient_id_task).",
                                    operatorid=".((int)$operatorid_task)."  ";
                    $rs = imw_query($insert_qry);
                    $insert_id = imw_insert_id();
                }
            }
        }
    }
}

?>