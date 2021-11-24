<?php

include_once("../../config/globals.php");
require_once(dirname(__FILE__).'/../../library/classes/cls_common_function.php');
require_once('assign_new_task_func.php');

$CLSCommonFunction = new CLSCommonFunction;


$serialized_arr = (isset($_REQUEST['obj_value']) && is_array(unserialize($_REQUEST['obj_value'])) && $_REQUEST['section'] == '') ? unserialize($_REQUEST['obj_value']) : unserialize($serialized_arr);

if (!empty($serialized_arr)) {
    $section_task = (isset($serialized_arr['section']) && $serialized_arr['section'] != '') ? $serialized_arr['section'] : '';
    $obj_value_task = (isset($serialized_arr['obj_value']) && $serialized_arr['obj_value'] != '') ? $serialized_arr['obj_value'] : '';
    $sub_section_task = (isset($serialized_arr['sub_section']) && $serialized_arr['sub_section'] != '') ? $serialized_arr['sub_section'] : '';

    $patientid_task = (isset($serialized_arr['patientid']) && $serialized_arr['patientid'] != '') ? $serialized_arr['patientid'] : '';
    $operatorid_task = (isset($serialized_arr['operatorid']) && $serialized_arr['operatorid'] != '') ? $serialized_arr['operatorid'] : '';
} else {
    $section_task = (isset($_REQUEST['section']) && $_REQUEST['section'] != '') ? trim($_REQUEST['section']) : '';
    $obj_value_task = (isset($_REQUEST['obj_value']) && $_REQUEST['obj_value'] != '') ? trim($_REQUEST['obj_value']) : '';

    $patientid_task = ($patientid_task) ? $patientid_task : $_SESSION['patient'];
    $operatorid_task = ($operatorid_task) ? $operatorid_task : $_SESSION['authId'];
}


if ($section_task != '') {

    switch ($section_task) {
        //Patient Status Changed
        case 'pt_status':
            $pt_status_id = '';
            $pt_sql = "SELECT * FROM patient_status_tbl where pt_status_name='$obj_value_task'";
            $pt_rs = imw_query($pt_sql);
            if ($pt_rs && imw_num_rows($pt_rs) == 1) {
                $row = imw_fetch_assoc($pt_rs);
                $pt_status_id = $row['pt_status_id'];
            }

            $insert_arr = array();
            $insert_arr['patientid'] = $patientid_task;
            $insert_arr['operatorid'] = $operatorid_task;
            $insert_arr['section'] = $section_task;
            $insert_arr['status_id'] = $pt_status_id;
            assign_acc_task_rules_to($insert_arr);
            break;

        //Patient Account Status Changed
        case 'pt_account_status':
            $pt_acc_status_id = '';
            $pt_sql = "SELECT * FROM account_status where id='$obj_value_task'";
            $pt_rs = imw_query($pt_sql);
            if ($pt_rs && imw_num_rows($pt_rs) == 1) {
                $row = imw_fetch_assoc($pt_rs);
                $pt_acc_status_id = $row['id'];
            }
            $insert_arr = array();
            $insert_arr['patientid'] = $patientid_task;
            $insert_arr['operatorid'] = $operatorid_task;
            $insert_arr['section'] = $section_task;
            $insert_arr['status_id'] = $pt_acc_status_id;
            assign_acc_task_rules_to($insert_arr);
            break;


        //Encounter Deleted
        case 'encounter_deleted':
            $cpt_fee_arr = get_cpt_arr();
            $id = $obj_value_task;

            $enc_deleted_qry = "SELECT patient_charge_list.charge_list_id,patient_charge_list.patient_id,patient_charge_list.operator_id as operator_id,
                            patient_charge_list.encounter_id
                            ,CONCAT(patient_data.lname,', ',patient_data.fname) as patient_name,patient_charge_list.date_of_service,
                            patient_charge_list_details.procCode,patient_charge_list.del_operator_id
                            FROM patient_charge_list
                            LEFT JOIN patient_charge_list_details ON (patient_charge_list_details.charge_list_id=patient_charge_list.charge_list_id)
                            LEFT JOIN patient_data ON (patient_data.id=patient_charge_list.patient_id)
                            WHERE patient_charge_list.charge_list_id=" . $id . " AND patient_charge_list.del_status='1'
                            AND patient_charge_list.del_operator_id>0 AND patient_charge_list.trans_del_date!='0000-00-00 00:00:00'
                            GROUP BY patient_charge_list.charge_list_id
                            ";

            $enc_deleted_rs = imw_query($enc_deleted_qry);
            while ($row = imw_fetch_assoc($enc_deleted_rs)) {
                $insert_arr = array();
                $insert_arr['patientid'] = $row['patient_id'];
                $insert_arr['operatorid'] = $row['operator_id'];
                $insert_arr['section'] = $section_task;
                $insert_arr['status_id'] = '';
                $insert_arr['encounter_id'] = $row['encounter_id'];
                $insert_arr['date_of_service'] = $row['date_of_service'];
                $insert_arr['cpt_code'] = $cpt_fee_arr[$row['procCode']];
                $insert_arr['patient_name'] = $row['patient_name'];

                assign_acc_task_rules_to($insert_arr);
            }
            break;


        case 'appointment':
            $id = $obj_value_task;

            $column = 'pt_appt_status';

            //if ($sub_section_task == 'appt_canceled' || $sub_section_task == 'appt_created' || $sub_section_task == 'appt_deleted' || $sub_section_task == 'appt_no_show' || $sub_section_task == 'appt_reschedule') {
                $task_facility_arr = array();
                $tfqry = "select id, name from facility order by name";
                $sql_rs = imw_query($tfqry);
                if ($sql_rs && imw_num_rows($sql_rs) > 0) {
                    while ($row = imw_fetch_assoc($sql_rs)) {
                        $task_facility_arr[$row['id']] = $row['name'];
                    }
                }

                $select = " ,st.status_name as appt_status_name ";
                $left_join = " LEFT JOIN schedule_status st on st.id = sc.sa_patient_app_status_id and status=1";
                if ($sub_section_task == 'appt_created') {
                    $where = " and sa_patient_app_status_id=0 ";
                    $select = "";
                    $left_join = "";
                }
                if ($sub_section_task == 'appt_canceled')
                    $where = " and sa_patient_app_status_id=18 ";
                if ($sub_section_task == 'appt_deleted')
                    $where = " and sa_patient_app_status_id=203 ";
                if ($sub_section_task == 'appt_no_show')
                    $where = " and sa_patient_app_status_id=3 ";
                if ($sub_section_task == 'appt_reschedule')
                    $where = " and sa_patient_app_status_id=202 ";


                $appt_qry = "SELECT sc.id,sc.sa_doctor_id,sc.sa_patient_id,sc.sa_patient_name,sc.sa_comments,sc.sa_app_starttime,sc.sa_facility_id,sc.ref_phy_id,sc.sa_app_start_date, slp.procedureId as slot_procedureId, slp.doctor_id as slot_doctor_id,  
                    sc.procedureid,sc.sa_madeby,sc.status_update_operator_id,sc.sa_patient_app_status_id,slp.proc as procName " . $select . "
                    FROM schedule_appointments sc
                    LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid
                    " . $left_join . "
                    WHERE sc.id=" . $id . " " . $where . "
                    ";

                $appt_rs = imw_query($appt_qry);
                while ($row = imw_fetch_assoc($appt_rs)) {
                    if ($row['sa_patient_app_status_id'] == '0') {
                        $appt_status = "Created";
                    } else {
                        $appt_status = $row['appt_status_name'];
                    }
                    $insert_arr = array();
                    $insert_arr['patientid'] = $row['sa_patient_id'];
                    $insert_arr['patient_name'] = $row['sa_patient_name'];
                    $insert_arr['operatorid'] = $row['status_update_operator_id'];
                    $insert_arr['sa_doctor_id'] = $row['sa_doctor_id'];
                    $insert_arr['app_status_id'] = $row['sa_patient_app_status_id'];
                    $insert_arr['section'] = $column;
                    $insert_arr['sub_section'] = $sub_section_task;
                    $insert_arr['appt_type'] = $row['procName'];
                    $insert_arr['procedureid'] = $row['procedureid'];
					if($row['slot_doctor_id']>0) {
						$insert_arr['procedureid'] = $row['slot_procedureId'];
					}
                    $insert_arr['ref_phy_id'] = $row['ref_phy_id'];
                    $insert_arr['appt_date'] = $row['sa_app_start_date'];
                    $insert_arr['appt_time'] = $row['sa_app_starttime'];
                    $insert_arr['appt_status'] = $appt_status;
                    $insert_arr['sa_facility_id'] = $row['sa_facility_id'];
                    $insert_arr['appt_comment'] = $row['sa_comments'];
                    $insert_arr['task_appt_id'] = $row['id'];

                    assign_appt_task_rules_to($insert_arr);
                }
            //}

            break;
    }
}
?>