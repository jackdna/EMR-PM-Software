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
require_once("../../../config/globals.php");
require_once("../../../library/classes/cls_common_function.php");
require_once("../../../library/classes/scheduler/appt_schedule_functions.php");
$appt = new appt_scheduler();
//$cls = new CLSCommonFunction();

$action = $_REQUEST['action'] ? trim($_REQUEST['action']) : 'load_listing';
$sel_proc=$_REQUEST['sel_proc'] ? $_REQUEST['sel_proc'] : '';
$return  = array();
//pre($_REQUEST);die;
switch($action)
{
	 case 'update':
	 	parse_str($_REQUEST['form_data'],$dataArr);
        foreach ($dataArr['data'] as $key => $dt) {
            if($dt['track'] == 1) {
                $tmpArr = array();
                $setValues = '';
                $id=$key;

                $tmpArr['assigned_user'] = $dt['assigned_user'];
                $tmpArr['professional_cost'] = $dt['professional_cost'];
                $tmpArr['facility_cost'] = $dt['facility_cost'];
                $tmpArr['anesthesia_cost'] = $dt['anesthesia_cost'];
                $tmpArr['status'] = $dt['status'];
                $tmpArr['comment'] = $dt['comment'];
                $tmpArr['updated_on'] = date('Y-m-d H:i:s');

                foreach($tmpArr as $tkey => $tval){
                    $chkStr = substr($tkey,0,3);
                    $str2Int = substr($tval,0,1);

                    $setValues .= '`'.$tkey."`='".addslashes(trim($tval))."',";
                }
                $setValues = substr($setValues,0,-1);
                $updateQuery = "update verification set $setValues where id = '$id' ";
                $qryId = imw_query($updateQuery);
                if(imw_affected_rows($updateQuery) > 0) {
                    $hxValues = '';
                    unset($tmpArr['updated_on']);
                    $tmpArr['v_id'] = $id;
                    $tmpArr['appt_id'] = $dt['appt_id'];
                    $tmpArr['v_required'] = $dt['v_required'];
                    $tmpArr['created_on'] = date('Y-m-d H:i:s');

                    foreach($tmpArr as $hxkey => $hxval){
                        $chkStr = substr($hxkey,0,3);
                        $str2Int = substr($hxval,0,1);

                        $hxValues .= '`'.$hxkey."`='".addslashes(trim($hxval))."',";
                    }
                    $hxValues = substr($hxValues,0,-1);
                    $insertQuery = "insert into verification_hx set $hxValues";
                    $qid=imw_query($insertQuery);
                    
                    $return['success'] = imw_affected_rows($updateQuery);
                }
            }
           	
        }
	 	break;
	case 'ref_phone':
		$phy_id = (int) $_POST['phy_id']; 
		$refData = get_reffphysician_detail($phy_id,'array','physician_phone');
		$return['phone'] = core_phone_format($refData['phone']);
		break;
	case 'raw_data':
			//$ins = insurance_provider(1,array(),'typeahead');
			//$return['typeahead'] = $ins['typeahead'];
			$return['slot_proc'] = $appt->load_procedures($sel_proc);
		break;
		
	case 'load_listing':
	default:
		//pd.primary_care_phy_name,pd.primary_care_phy_id
		//pd.primary_care as referring_phy, pd.primary_care_id as reffering_phy_id
		
		$fields = " sa.id as appt_id, sa.sa_patient_id as patient_id, sa_patient_name, TIME_FORMAT(sa.sa_app_starttime, '%h:%i %p') as sa_app_starttime, DATE_FORMAT(sa.sa_app_start_date,'".get_sql_date_format()."') as sa_app_start_date,
								sa.sa_ref_management, sa.procedureid, sp.proc, sa.ref_phy_id, sa.pcp_id, sa.case_type_id,sa.sa_patient_app_status_id, 
								u.lname as u_lname, u.fname as u_fname, u.mname as u_mname, CONCAT(u.lname,', ',u.fname,' ',u.mname) as physician, 
								ins.id as ins_data_id,fac.id,fac.name as facility_name,ss.id,ss.status_name,vr.id as v_id,vr.v_required, vr.assigned_user, vr.professional_cost, vr.facility_cost, vr.anesthesia_cost, vr.status, vr.comment,
							  inc.id as pri_ins_prov_id, inc.name as pri_ins_prov, pd.fname, pd.lname, pd.mname, pd.primary_care_phy_id, pd.primary_care_id,pd.DOB ";
		
		if($_REQUEST['appt_type'] == 'reschedule') $status = "AND sa_patient_app_status_id = 201";
		else $status = "AND sa_patient_app_status_id NOT IN (203,201,18,19,20) AND IF( sa_patient_app_status_id =271, sa_patient_app_show =0, sa_patient_app_show <>2 ) ";
		$qry = "Select ".$fields." From schedule_appointments sa
								Join patient_data pd On sa.sa_patient_id = pd.id
								Join users u On u.id = sa.sa_doctor_id
								Join verification vr On vr.appt_id=sa.id
								Join slot_procedures sp On sp.id=sa.procedureid
								Join facility fac On fac.id=sa.sa_facility_id
								Left Join schedule_status ss On ss.id=sa.sa_patient_app_status_id
								Left Join insurance_data ins On (sa.case_type_id > 0 And ins.ins_caseid = sa.case_type_id And type = 'primary' And actInsComp = 1)
								Left Join insurance_companies inc On inc.id=ins.provider
								Where v_required = 1 And sa.sa_app_start_date >= '".date('Y-m-d')."' ".$status;
		
		$sql = imw_query($qry) or die(imw_error());
		$cnt = imw_num_rows($sql);
		if( $cnt > 0 ){
			while( $row = imw_fetch_assoc($sql) )	{
				$appt_id = $row['appt_id'];
                if($row['sa_patient_app_status_id']==0) {$row['status_name']='Created';}
				$d = array();
				$d['pt_name_id'] = $row['sa_patient_name'] .' - '.$row['patient_id'];
				$d['pt_dob'] = $row['DOB'] ? date('m-d-Y',strtotime($row['DOB'])) : '';
				$d['pri_ins_prov'] = $row['pri_ins_prov'] ? $row['pri_ins_prov'] : '';
				$d['proc_type'] = $row['proc'] ? $row['proc'] : '';
				$d['appt_date_time'] = $row['sa_app_start_date'] .' '. $row['sa_app_starttime'];
				$d['appt_status'] = $row['status_name'] ? $row['status_name'] : '';
				$d['physician'] = trim($row['physician']);
				$d['patient_id'] = $row['patient_id'] ? $row['patient_id'] : '';
				$d['pri_ins_prov_id'] = $row['pri_ins_prov_id'] ? $row['pri_ins_prov_id'] : '';
				$d['procedureid'] = $row['procedureid'] ? $row['procedureid'] : '';
				$d['ins_data_id'] = $row['ins_data_id'] ? $row['ins_data_id'] : '';
				$d['ins_case_id'] = $row['case_type_id'] ? $row['case_type_id'] : '';
				$d['facility_name'] = $row['facility_name'] ? $row['facility_name'] : '';
				$d['v_required'] = $row['v_required'] ? $row['v_required'] : '';
				$d['v_id'] = $row['v_id'] ? $row['v_id'] : '';

				$d['assigned_user'] = $row['assigned_user'] ? $row['assigned_user'] : '';
				$d['professional_cost'] = $row['professional_cost'] ? $row['professional_cost'] : '';
				$d['facility_cost'] = $row['facility_cost'] ? $row['facility_cost'] : '';
				$d['anesthesia_cost'] = $row['anesthesia_cost'] ? $row['anesthesia_cost'] : '';
				$d['status'] = $row['status'] ? $row['status'] : 'pending';
				$d['comment'] = $row['comment'] ? $row['comment'] : '';

				$return[$appt_id] = $d;	
			}
		}
		
		
	break;
	
    
    case 'get_hx':
        $vhxid=$_REQUEST['vhxid'];
        
        $return=array();
        $sql="Select * from verification_hx where delete_status=0 and v_id=".$vhxid." order by id desc";
        $res=imw_query($sql);
        if($res && imw_num_rows($res) >0) {
            while( $row=imw_fetch_assoc($res) ) {
                $return[$row['id']]=$row;
            }
        }
        break;
        
    case 'delete_vhx':
        $del_vhx_id = (isset($_REQUEST['del_id']) && trim($_REQUEST['del_id'])!='') ? trim($_REQUEST['del_id']) : '';
        $del_vhx_id_arr = explode(',',$del_vhx_id);
        
        $qry = "UPDATE verification_hx SET delete_status = 1 where id in($del_vhx_id)";
        $result = imw_query($qry);
        if($result){
            $return='taskdeleted';
        }
        break;
}

// Return json array containing data from the database
echo $json_info = json_encode($return);

function reff_pcp_physician($phy_id,$phy_name,$phone) {
	
	
		if( $phy_id) {
			$phy_data = get_reffphysician_detail($phy_id,'array','physician_phone');
			$intPhyId = $phy_id;
		} 
		else if( !$phy_id && $phy_name ) {
			// Check and create new referring/primary care physician
			list($intPhyId, $strPhyName) = $OBJCommonFunction->chk_create_ref_phy($phy_name, 8);
			$phy_data = get_reffphysician_detail($phy_id,'array','physician_phone');
		}
		
		// Update referring/primary care physician phone no.
		if( is_array($phy_data) && count($phy_data) > 0 ) 
		{
			$new_phone = core_phone_unformat($phone);
			$old_phone = core_phone_unformat($phy_data['phone']);
			$phone_no = $old_phone;
			if( $new_phone && $new_phone <> $old_phone) {
				$phone_no = $new_phone;
				$qry = "Update refferphysician set physician_phone = '".$new_phone."' Where physician_Reffer_id = ".$intPhyId."  ";
				$sql = imw_query($qry) or die('Error: '. $qry . '--'. imw_error() );
			}
			
		}
		// End referring/primary care physician
	
	return array('phy_id' => $intPhyId, 'phy_name' => $phy_name,'phone' => $phone_no);
	
}
?>