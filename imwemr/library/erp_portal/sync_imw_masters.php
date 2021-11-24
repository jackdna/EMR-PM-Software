<?php
include_once("../../config/globals.php");
include_once($GLOBALS['srcdir']."/erp_portal/master_data.php");

set_time_limit(0);
$obj_Master_data = new Master_data();

$section = $_REQUEST['section'];

$return=array();
if($section) {
    switch($section) {
        case 'races' :
            $return['race']=patient_races();
            break;
        case 'ethnicity' :
            $return['ethnicity']=patient_ethnicity();
            break;
        case 'marital_status' :
            $return['marital_status']=patient_marital_status();
            break;
        case 'gender' :
            $return['gender']=patient_sexes();
            break;
        /* case 'all' :
            $return['race']=patient_races();
            $return['ethnicity']=patient_ethnicity();
            $return['marital_status']=patient_marital_status();
            $return['gender']=patient_sexes();
            break; */
        case 'schedule_statuses' :
            $return['schedule_statuses']=active_schedule_statuses();
            break;
        case 'slot_procedures' :
            $return['slot_procedures']=upload_slot_procedures();
            break;
		case 'patient_relations' :
            $return['patient_relations']=patient_relations();
            break;
        case 'allergy_severity' :
            $return['allergy_severity']=allergy_severity();
        break;
        case 'allergies' :
            $return['allergies']=patient_allergies();
            break;
        case 'medication_master' :
            $return['medication_master']=medication_master();
            break;
        case 'route_master' :
            $return['route_master']=route_master();
            break;
        case 'surgery' :
            $return['surgery']=surgery();
            break;         
    }

    echo json_encode($return);
    die;
}


function patient_races() {
    global $obj_Master_data;

    $races_sql = "select * from race where is_deleted=0 and erp_race_id='' ";
    $races_res=imw_query($races_sql);
    $counter=0;
    if($races_res && imw_num_rows($races_res)>0){
        while( $race = imw_fetch_assoc($races_res) ) {
            $data=array();
            $data['name']=$race['race_name'];
            $data['active']=true;
            $data['id']=$race['erp_race_id'];
            $data['externalId']=$race['race_id'];

            $races_arr = $obj_Master_data->sync_races($data);

            if(count($races_arr)>0){
                $update_races = "update race set erp_race_id='".$races_arr['id']."' where race_id=".$race['race_id']." ";
                imw_query($update_races);

                $counter++;
            }
        }
    }

    return $counter .' races records updated.';
}


function medication_master() {
    global $obj_Master_data;
    
    $medication_sql = "SELECT md.id, md.medicine_name, md.ocular, md.glucoma,
                md.ret_injection, md.alias, md.recall_code, md.med_procedure, md.description, md.prescription,
                md.alertmsg, md.alert, md.ccda_code, md.fdb_id, md.tracked_inventory,opt_med_name,opt_med_id,opt_med_upc
                FROM medicine_data md WHERE md.del_status = 0 and erp_medication_id IS NULL";
    $medication_res=imw_query($medication_sql);
    $counter=0;
    if($medication_res && imw_num_rows($medication_res)>0){
        while( $medication = imw_fetch_assoc($medication_res) ) {
            $data=array();
            $data['name']=$medication['medicine_name'];
            $data['active']=true;
            $data['id']=$medication['erp_medication_id'];
            $data['Strength']= 'blank';
            $data['externalId']=$medication['id'];
            $medication_arr = $obj_Master_data->sync_medication_master($data);            
            if(count($medication_arr)>0){
                $update_races = "update medicine_data set erp_medication_id='".$medication_arr['id']."' where id=".$medication['id']." ";
                imw_query($update_races);
                $counter++;
            }
        }
    }
    
    return $counter .' medication records updated.';
}


function route_master() {
    global $obj_Master_data;
    
    $route_sql = "SELECT id, route_name, erp_route_id FROM route_codes WHERE del_status = 0 and erp_route_id IS NULL ORDER BY id ASC limit 2";
    $route_res=imw_query($route_sql);
    $counter=0;
    if($route_res && imw_num_rows($route_res)>0){
        while( $route = imw_fetch_assoc($route_res) ) {
            $data=array();
            $data['name']=$route['route_name'];
            $data['active']=true;
            $data['id']=$route['erp_route_id'];
            $data['externalId']=$route['id'];
            $route_arr = $obj_Master_data->sync_route_master($data);            
            if(count($route_arr)>0){
                $update_route = "update route_codes set erp_route_id='".$route_arr['id']."' where id=".$route['id']." ";
                imw_query($update_route);
                $counter++;
            }
        }
    }
    
    return $counter .' route records updated.';
}

function allergy_severity() {
    global $obj_Master_data;

    $allergy_sql="SELECT id,name,status,erp_severity_id FROM allergy_severity where erp_severity_id IS NULL and status=0";
    $allergy_res=imw_query($allergy_sql);
    $counter=0;
    if($allergy_res && imw_num_rows($allergy_res)>0){
        $data=array();
        while( $allergy = imw_fetch_assoc($allergy_res) ) {
            $data['name']=$allergy['name'];
            $data['active']=true;
            $data['id']=$allergy['erp_severity_id'];
            $data['externalId']=$allergy['id'];
            
            $serverity_arr = $obj_Master_data->sync_severity($data);

            if(count($serverity_arr)>0){
                $update_severity = "update allergy_severity set erp_severity_id='".$serverity_arr['id']."' where id=".$serverity_arr['externalId']." ";
                imw_query($update_severity);
                $counter++;
            }
        }
    }
    return $counter .' severity records updated.';
}

function patient_allergies(){
    global $obj_Master_data;
    $allergy_sql = "select * from allergies_data where is_deleted=0 and erp_allergy_id='' ";
    $allergy_res=imw_query($allergy_sql);
    $counter=0;
    if($allergy_res && imw_num_rows($allergy_res)>0){
        while( $allergy = imw_fetch_assoc($allergy_res) ) {
            $data=array();
            $data['name']=$allergy['allergie_name'];
            $data['active']=true;
            $data['id']=$allergy['erp_allergy_id'];
            $data['externalId']=$allergy['allergies_id'];

            $allergy_arr = $obj_Master_data->sync_allergy($data);
            if(count($allergy_arr)>0){
                $update_ethnicity = "update allergies_data set erp_allergy_id='".$allergy_arr['id']."' where allergies_id=".$allergy['allergies_id']." ";
                imw_query($update_ethnicity);

                $counter++;
            }
        }
    }

    return $counter .' allergies records updated.';
}

function patient_ethnicity() {
    global $obj_Master_data;

    $ethnicity_sql = "select * from ethnicity where is_deleted=0 and erp_ethn_id=''  ";
    $ethnicity_res=imw_query($ethnicity_sql);
    $counter=0;
    if($ethnicity_res && imw_num_rows($ethnicity_res)>0){
        while( $ethnicity = imw_fetch_assoc($ethnicity_res) ) {
            $data=array();
            $data['name']=$ethnicity['ethnicity_name'];
            $data['active']=true;
            $data['id']=$ethnicity['erp_ethn_id'];
            $data['externalId']=$ethnicity['ethnicity_id'];

            $ethnicity_arr = $obj_Master_data->sync_ethnicity($data);
            if(count($ethnicity_arr)>0){
                $update_ethnicity = "update ethnicity set erp_ethn_id='".$ethnicity_arr['id']."' where ethnicity_id=".$ethnicity['ethnicity_id']." ";
                imw_query($update_ethnicity);

                $counter++;
            }
        }
    }

    return $counter .' ethnicity records updated.';
}

function patient_marital_status() {
    global $obj_Master_data;

    $marital_sql = "select * from marital_status where is_deleted=0 and erp_marital_id='' ";
    $marital_res=imw_query($marital_sql);
    $counter=0;
    if($marital_res && imw_num_rows($marital_res)>0){
        while( $marital = imw_fetch_assoc($marital_res) ) {
            $data=array();
            $data['name']=$marital['mstatus_name'];
            $data['active']=true;
            $data['id']=$marital['erp_marital_id'];
            $data['externalId']=$marital['mstatus_id'];

            $marital_arr = $obj_Master_data->sync_marital_status($data);
            if(count($marital_arr)>0){
                $update_marital = "update marital_status set erp_marital_id='".$marital_arr['id']."' where mstatus_id=".$marital['mstatus_id']." ";
                imw_query($update_marital);

                $counter++;
            }
        }
    }

    return $counter .' marital status updated.';
}

function patient_sexes() {
    global $obj_Master_data;

    $qry = "Select * From gender_code Where is_deleted = 0 and erp_gender_id='' ";
	$sql = imw_query($qry);
	$cnt = imw_num_rows($sql);
	$counter=0;
	if( $cnt > 0 )
	{
		while($gender = imw_fetch_assoc($sql) )
		{
            $data=array();
            $data['name']=$gender['gender_name'];
            $data['active']=true;
            $data['id']=$gender['erp_gender_id'];
            $data['externalId']=$gender['gender_id'];

            $gender_arr = $obj_Master_data->sync_sexes($data);
            if(count($gender_arr)>0){
                $update_gender = "update gender_code set erp_gender_id='".$gender_arr['id']."' where gender_id=".$gender['gender_id']." ";
                imw_query($update_gender);

                $counter++;
            }
		}
	}

    return $counter .' gender code updated.';
}


function active_schedule_statuses() {
    include_once($GLOBALS['srcdir']."/erp_portal/appointments.php");
    $obj_appointments = new Appointments();

    $qry = "SELECT id from schedule_status where status =1  ";
    $sql = imw_query($qry);
    $cnt = imw_num_rows($sql);
    $counter=0;
    if( $cnt > 0 )
    {
        while($row = imw_fetch_assoc($sql) )
        {
            $status_id=$row['id'];
            $obj_appointments->addUpdateAppointmentStatuses($status_id);
            $counter++;
        }
    }

    return $counter .' active schedule status uploaded.';
}


function upload_slot_procedures() {
    include_once($GLOBALS['srcdir']."/erp_portal/appointments.php");
    $obj_appointments = new Appointments();

    $qry = "SELECT id from slot_procedures where active_status='yes' and proc!='' and source='' and (procedureId=id or procedureId=0)  ";
    $sql = imw_query($qry);
    $cnt = imw_num_rows($sql);
    $counter=0;
    if( $cnt > 0 )
    {
        while($row = imw_fetch_assoc($sql) )
        {
            $status_id=$row['id'];
            $obj_appointments->addUpdateAppointmentRequestReasons($status_id);
            $counter++;
        }
    }

    return $counter .' slot procedures (Appointment Types) uploaded.';
}


function patient_relations() {
    global $obj_Master_data;

    $relations_sql = "select * from patient_relations where general_health_status = '1' ORDER BY relation";
    $relations_res=imw_query($relations_sql);
    $counter=0;
	$a=0;
    if($relations_res && imw_num_rows($relations_res)>0){
        while( $relations = imw_fetch_assoc($relations_res) ) {
            $a++;
			$data=array();
            $data['name']			= $relations['relation'];
            $data['sortOrder']		= $a;
			$data['isDefault']		= false;
            $data['id']				= $relations['erp_pat_rel_id'];
            $data['externalId']		= $relations['id'];
            $relations_arr = array();
            $result = '';
			if($relations['del_status'] =='1') { //IF RECORD DELETED
				if(trim($relations['erp_pat_rel_id'])) { //IF DELETED RECORD IS SYNCED TO PATIENT PORTAL THEN REMOVE IT FROM PORTAL ALSO
					$result 	= $obj_Master_data->sync_pt_relations_delete(array(),$relations['id']);
				}
			}else {
				$relations_arr 	= $obj_Master_data->sync_pt_relations($data);
			}
            if(count($relations_arr)>0){
                $update_relations 	= "update patient_relations set erp_pat_rel_id='".$relations_arr['id']."' where id=".$relations['id']." ";
                imw_query($update_relations);
                $counter++;
            }
        }
    }

    return $counter .' Patient relations updated.';
}

function surgery() {
    global $obj_Master_data;
    $surgery_sql="SELECT id,title,delete_status,erp_surgery_id FROM lists_admin where erp_surgery_id IS NULL and delete_status=0";
    $surgery_res=imw_query($surgery_sql);
    $counter=0;
    if($surgery_res && imw_num_rows($surgery_res)>0){
        $data=array();
        while( $surgery = imw_fetch_assoc($surgery_res) ) {
            $data['name']=$surgery['title'];
            $data['active']=true;
            $data['id']=$surgery['erp_surgery_id'];
            $data['externalId']=$surgery['id'];
            
            $surgery_arr = $obj_Master_data->sync_surgery($data);
            
           if(count($surgery_arr)>0){
                $update_surgery = "update lists_admin set erp_surgery_id='".$surgery_arr['id']."' where id=".$surgery_arr['externalId']." ";
                imw_query($update_surgery);
                $counter++;
            }
        }
    }
    return $counter .' surgery records updated.';
}

?>
