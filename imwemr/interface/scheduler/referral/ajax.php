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

switch($action)
{
	case 'update':
		//pre($_POST);
		//array_walk('trim',$_POST);
		array_walk($_POST,'trim');
		$msg = array();
		$ref_phy_data = reff_pcp_physician($_POST['pt_ref_id'],$$_POST['ref_phy'],$_POST['ref_phone']);
		$pcp_phy_data = reff_pcp_physician($_POST['pt_pcp_id'],$$_POST['pcp_phy'],$_POST['pcp_phone']);
		
		$reff_require = ( $_POST['refferal_no'] || $_POST['no_of_reffs'] || $_POST['reff_effective_date'] ) ? true : false;
		$auth_require = ( $_POST['auth_name'] || $_POST['auth_date'] ) ? true : false;
		
		if( $_POST['ins_data_id'] && ($reff_require || $auth_require) ) {
			
			if( $reff_require )
			{
				$no_of_reffs = $reff_used = '';
				$reff_qry = "";
				if($_POST['no_of_reffs'] ) {
					$tmpArr = explode("/",$_POST['no_of_reffs']);
					$no_of_reffs = (int) $tmpArr[0]-$tmpArr[1];
					$reff_used = $tmpArr[1]; 
					$reff_qry .= ($no_of_reffs) ? ",no_of_reffs='".$no_of_reffs."'" : '' ;
					$reff_qry .= ($reff_used) ? ",reff_used='".$reff_used."'" : '';
				}
				$reff_qry .= ($_POST['refferal_no']) ? ",reffral_no='".$_POST['refferal_no']."'" : '';
				$effective_date = getDateFormatDB($_POST['reff_effective_date']);
				$reff_qry .= ($effective_date) ? ",effective_date='".$effective_date."' " : '';
				$reff_qry .= ($_POST['reff_notes']) ? ",note='".imw_real_escape_string($_POST['reff_notes'])."' " : '';
				
				if( $reff_qry ){
					if( $_POST['reff_id'] ) {
						$qry = "Update patient_reff set ".substr($reff_qry,1) ." Where reff_id = ".$_POST['reff_id']." " ;
					}
					else {
						$qry = "Insert into patient_reff set patient_id='".$_POST['patient_id']."', reff_by='".$ref_phy_data['phy_name']."', reff_phy_id='".$ref_phy_data['phy_id']."', reff_type=1, ins_data_id = '".$_POST['ins_data_id']."',insCaseid='".$_POST['ins_case_id']."',ins_provider='".$_POST['ins_prov_id']."', timestamp='".date('Y-m-d H:i:s')."' ".$reff_qry."    ";
						$qry1 = "Update insurance_data Set referal_required = 'Yes' Where id= '".$_POST['ins_data_id']."' ";
					}
					$ref_msg = '';
					if( $qry ) { 
						$upReff = imw_query($qry) or die('Error: '.$qry. '--' .imw_error());
					}
					
					if( $qry1) {
						$insReff = imw_query($qry1) or die('Error: '.$qry. '--' .imw_error());
					}
					
					if( !$upReff && !$insReff  ) { $msg[] = 'Error in saving referral details !!! '; }
					
				}
				
			}
			
			if( $auth_require) 
			{
				$auth_qry = "";
				$auth_date = getDateFormatDB($_POST['auth_date']);
				$auth_qry .= ($_POST['auth_name']) ? ",auth_name='".$_POST['auth_name']."'" : '';
				$auth_qry .= ($auth_date) ? ",auth_date='".$auth_date."' " : '';
				
				if( $auth_qry )
				{
					if( $_POST['auth_id'] ) {
						$qry = "Update patient_auth set ".substr($auth_qry,1) ." Where a_id = ".$_POST['auth_id']." " ;
					}
					else {
						$qry = "Insert into patient_auth set patient_id='".$_POST['patient_id']."', ins_data_id = '".$_POST['ins_data_id']."', ins_case_id = '".$_POST['ins_case_id']."', ins_provider = '".$_POST['ins_prov_id']."', ins_type=1, timestamp='".date('Y-m-d H:i:s')."',  auth_operator='".$_SESSION['auth_id']."' ".$auth_qry."    ";
						$qry1 = "Update insurance_data Set auth_required = 'Yes' Where id= '".$_POST['ins_data_id']."' ";
					}
					if( $qry ) {
						$upAuth = imw_query($qry) or die('Error: '.$qry. '--' .imw_error());
					}
					
					if( $qry1) {
						$insAuth = imw_query($qry1) or die('Error: '.$qry. '--' .imw_error());
					}
					if( !$upAuth && !$insAuth  ) { $msg[] = 'Error in saving authorisation details !!! '; }
				}
				
			}
		}
		elseif( !$_POST['ins_data_id'] && ($reff_require || $auth_require) ) {
			$msg[] = 'Please add primary insurance first to add referral or authorisation details.';
		}
		
		
		
		// update refferring physician, primary care  in demographics  table
		$qry = "Update patient_data set primary_care = '".$ref_phy_data['phy_name']."', primary_care_id = '".$ref_phy_data['phy_id']."', 
									 									primary_care_phy_name = '".$pcp_phy_data['phy_name']."', primary_care_phy_id = '".$pcp_phy_data['phy_id']."'
																		
									Where id = '".$_POST['patient_id']."' ";
		$sql = imw_query($qry) or die('Error: '.$qry .'--'. imw_error());
		if(!$sql) { $msg[] = 'Error in saving primary care and referring physicians  !!! '; }
		
		// update procedure/referring physician/primary care physician appointment table
		$sch_qry = '';
		$sch_qry .= $_POST['pri_proc'] ? ",procedureid='".$_POST['pri_proc']."'" : '';
		$sch_qry .= $ref_phy_data['phy_id'] ? ",ref_phy_id=if(ref_phy_id > 0,".$ref_phy_data['phy_id'].",0)" : '';
		$sch_qry .= $pcp_phy_data['phy_id'] ? ",pcp_id=if(pcp_id > 0,".$pcp_phy_data['phy_id'].",0)" : '';
		
		if( $sch_qry ){
			$appt_qry = "Update schedule_appointments set ".substr($sch_qry,1)." Where id = '".$_POST['appt_id']."' ";	
			$appt_sql = imw_query($appt_qry) or die('Error: '.$appt_qry .'--'. imw_error());
			if( !$appt_sql  ) { $msg[] = 'Error in update appointment details !!! '; }
		}
		
		$return['error'] = $msg;
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
								sa.sa_ref_management, sa.procedureid, sp.proc, sa.ref_phy_id, sa.pcp_id, sa.case_type_id,
								u.lname as u_lname, u.fname as u_fname, u.mname as u_mname, CONCAT(u.lname,', ',u.fname,' ',u.mname) as physician,
								ins.id as ins_data_id,
							  inc.id as pri_ins_prov_id, inc.name as pri_ins_prov, pd.fname, pd.lname, pd.mname, pd.primary_care_phy_id, pd.primary_care_id,
							 	pr.reff_id, pr.no_of_reffs, pr.reff_used, pr.reffral_no, DATE_FORMAT(pr.effective_date,'".get_sql_date_format()."') as effective_date,pr.note,
							 	pa.a_id as auth_id, pa.auth_name,DATE_FORMAT(pa.auth_date,'".get_sql_date_format()."') as auth_date ";
		
		if($_REQUEST['appt_type'] == 'reschedule') $status = "AND sa_patient_app_status_id = 201";
		else $status = "AND sa_patient_app_status_id NOT IN (203,201,18,19,20) AND IF( sa_patient_app_status_id =271, sa_patient_app_show =0, sa_patient_app_show <>2 ) ";
		$qry = "Select ".$fields." From schedule_appointments sa
								Join patient_data pd On sa.sa_patient_id = pd.id
								Join users u On u.id = sa.sa_doctor_id
								Join slot_procedures sp On sp.id=sa.procedureid
								Left Join insurance_data ins On (sa.case_type_id > 0 And ins.ins_caseid = sa.case_type_id And type = 'primary' And actInsComp = 1)
								Left Join insurance_companies inc On inc.id=ins.provider
								Left Join patient_reff pr On (pr.ins_data_id = ins.id And ins.referal_required = 'Yes' And pr.reff_type=1 And (sa.sa_app_start_date Between pr.effective_date And pr.end_date OR (sa.sa_app_start_date >= pr.effective_date AND pr.end_date = '0000-00-00' )))
								Left Join patient_auth pa On (pa.ins_data_id = ins.id And ins.auth_required = 'Yes' And pa.ins_type = 1 And pa.auth_status = 0 And (sa.sa_app_start_date Between pa.auth_date And pa.end_date OR (sa.sa_app_start_date >= pa.auth_date AND pa.end_date = '0000-00-00')))
								Where sa_ref_management = 1 And sa.sa_app_start_date >= '".date('Y-m-d')."' ".$status;
								
		if(isset($_REQUEST['so'])  && isset($_REQUEST['soAD'])){

			$order = $_REQUEST['so'];
			$orderBy = $_REQUEST['soAD'];
			if($order != 'phone' )
			{
				switch($order)
				{
					case 'sa_app_start_date':
						$qry .= ' order by sa.sa_app_start_date '.$orderBy.', sa.sa_app_starttime'. ' '.$orderBy;
					break;
					case 'proc':
						$qry .= ' order by sp.proc'. ' '.$orderBy; 
					break;
					default:
						$qry .= ' order by '.$order.' '.$orderBy; 
					break;
				}
			}
		}

		$sql = imw_query($qry) or die(imw_error());
		$cnt = imw_num_rows($sql);
		if( $cnt > 0 ){
			while( $row = imw_fetch_assoc($sql) )	{
				$appt_id = $row['appt_id'];
				$pcp_id = $row['pcp_id'] ? $row['pcp_id'] : $row['primary_care_phy_id'];
				$ref_id = $row['ref_phy_id'] ? $row['ref_phy_id'] : $row['primary_care_id'];
				
				$row['pt_ref_id'] = $ref_id;
				$row['pt_pcp_id'] = $pcp_id;
				
				$pcp_arr = get_reffphysician_detail($pcp_id,'array','physician_phone');
				$ref_arr = get_reffphysician_detail($ref_id,'array','physician_phone');
				
				$d = array();
				$d['pt_name_id'] = $row['sa_patient_name'] .' - '.$row['patient_id'];
				$d['pri_ins_prov'] = $row['pri_ins_prov'] ? $row['pri_ins_prov'] : '';
				$d['proc_type'] = $row['proc'] ? $row['proc'] : '';
				$d['appt_date_time'] = $row['sa_app_start_date'] .' '. $row['sa_app_starttime'];
				$d['physician'] = trim($row['physician']);
				$d['patient_id'] = $row['patient_id'] ? $row['patient_id'] : '';
				$d['pcp_phy'] = $pcp_arr['full_name'] ? $pcp_arr['full_name'] : '';
				$d['pcp_phone'] = core_phone_format($pcp_arr['phone']);
				$d['ref_phy'] = $ref_arr['full_name'] ? $ref_arr['full_name'] : '';
				$d['ref_phone'] = core_phone_format($ref_arr['phone']);
				$d['refferal_no'] = $row['reffral_no'] ? $row['reffral_no'] : '';
				$d['no_of_reffs'] = ($row['no_of_reffs']+$row['reff_used'] > 0) ? (($row['no_of_reffs']+$row['reff_used']) .'/'.$row['reff_used']) :'';
				$d['reff_effective_date'] = $row['effective_date'] ? $row['effective_date'] : '';
				$d['reff_notes'] = $row['note'] ? $row['note'] : '';
				$d['auth_name'] = $row['auth_name'] ? $row['auth_name'] : '';
				$d['auth_date'] = $row['auth_date'] ? $row['auth_date'] : '';
				$d['pri_ins_prov_id'] = $row['pri_ins_prov_id'] ? $row['pri_ins_prov_id'] : '';
				$d['procedureid'] = $row['procedureid'] ? $row['procedureid'] : '';
				$d['pt_ref_id'] = $row['pt_ref_id'] ? $row['pt_ref_id'] : '';
				$d['pt_pcp_id'] = $row['pt_pcp_id'] ? $row['pt_pcp_id'] : '';
				$d['ins_data_id'] = $row['ins_data_id'] ? $row['ins_data_id'] : '';
				$d['reff_id'] = $row['reff_id'] ? $row['reff_id'] : '';
				$d['auth_id'] = $row['auth_id'] ? $row['auth_id'] : '';
				$d['ins_case_id'] = $row['case_type_id'] ? $row['case_type_id'] : '';
				
				//$d['all_data'] = $row;

				//$return[$appt_id] = $d;
				$return[] = $d;	
			}

			if($order == 'phone'){

					foreach($return as $key => $value)

					{
						$phone[$key] = $value['ref_phone'];
					}

					$phone = array_column($return,'ref_phone');

					array_multisort($phone, ($orderBy == 'ASC') ? SORT_ASC : SORT_DESC, $return);
					 
			}
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