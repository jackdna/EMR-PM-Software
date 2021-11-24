<?php
ini_set("memory_limit","5072M");
class Medications_refill {

    public function __construct() {
        
    }

    function get_med_refill_id($recid) {
        $ret = 0;
        $sql = "SELECT portal_req_id FROM erp_iportal_medication_refill WHERE id = '" . $recid . "' ";
        $row = sqlQuery($sql);
        if ($row != false && !empty($row["portal_req_id"])) {
            $ret = $row["portal_req_id"];
        }
        return $ret;
    }

    function updatePortal($recid, $res, $usr_con = 0) {
        $ret = 1;
        $portal_req_id = $this->get_med_refill_id($recid);
		
        if (!empty($portal_req_id)) {

            //call from user console
            if (!empty($usr_con)) {
                $res = ($res == '1') ? 'aprv' : 'dcln';
            }
			$response='';
			$ret = $this->update_log_req_data($response, $recid, $res);
        }

        if (!empty($usr_con)) {
            if ($ret == 0) {
                $ret = ($res == 'aprv') ? "Approved" : "Declined";
            }
        }

        //echo $ret;
    }

    function update_log_req_data($response, $recid, $res) {
        $aprv_dec = ($res == "aprv") ? "1" : "2";
        $action_date_time = date("Y-m-d H:i:s");
        $operator_id = $_SESSION["authId"];
        $sql = "UPDATE erp_iportal_medication_refill
            SET approved_declined='" . $aprv_dec . "', action_date='" . $action_date_time . "',
                approved_by='" . $operator_id . "'
            WHERE id='" . $recid . "'
          ";
        $result = sqlQuery($sql);
        return 0;
    }

    function send_acknow($ar_uniq_req_id) {
        if (count($ar_uniq_req_id) > 0) {
            foreach ($ar_uniq_req_id as $k => $med_refill_id) {
                if (!empty($med_refill_id)) {
                    $data_arr = array();
                    $data_arr["InternalID"] = $med_refill_id;
					$data_arr["Success"] = true;
					$data_arr["ResultMessage"] = "Success";
					//pre($data_arr);
                    //include_once($GLOBALS['srcdir'] . "/erp_portal/erp_portal_core.php");
                    //$erpPortalCore = new ERP_portal_core();
                    //$response = $erpPortalCore->CURL($data_arr, "api/MedicationRefillRequestsSent", "POST");
                }
            }
        }
    }

    public function getMedicationRefill() {
        $OBJRabbitmqExchange = new Rabbitmq_exchange();
        $data_arr = array();

        $resource = 'medicationrefillrequests/search?alreadySent=false';
        $method = 'GET';
        $message_id = $_SESSION['authId'] . "-" . time();
        /* Rabbit MQ call to create patient at Portal */
		if($OBJRabbitmqExchange) {
			$response = $OBJRabbitmqExchange->send_request($data_arr, $message_id, $resource, $method);
				
			$response = json_decode($response, true);

			$this->log_medications_refill_req_data($response);
		}
        
    }


    public function log_medications_refill_req_data($response) {
        $len = (isset($response["rows"])) ? count($response["rows"]) : 0;
        if (!empty($len)) {
            $ar_uniq_req_id = array();
            $ar_sql = array();
            $ar_rws = $response["rows"];
            $operator = $_SESSION["authId"];
            $curdt = date("Y-m-d H:i:s");
            foreach ($ar_rws as $k => $rw) {
				$pt_othr_details=array();
                $id = $rw["id"];
                $pt_ex_id = $rw["patientExternalId"];
				$pt_med_id = $rw["patientMedicationExternalId"];
				$usr_ex_id = $rw["doctorExternalId"];
				$status = $rw['status'];
                $created = $rw['created'];
                $patientComments = $rw['patientComments'];
                $is_valid_pt = $this->is_valid_pt($pt_ex_id);
                if (!$is_valid_pt)
                    continue;
				
				$pt_othr_details = $this->get_pt_medication_details($pt_ex_id,$pt_med_id,$usr_ex_id);
				if(!$pt_othr_details['med_id'])
					continue;
				
				$pt_med_name=$pt_othr_details['medication_name'];
				$last_enc_date=$pt_othr_details['last_dos'];
				$pt_allergies=json_encode($pt_othr_details['pt_allergies']);
				
                $created_on = date('Y-m-d H:i:s');
                $med_refill_id_ed = $this->is_med_refill_req_exists($rw["id"]);
                if (!empty($med_refill_id_ed)) {
                    // do noting
                } else {
                    $ar_sql[] = "( '" . sqlEscStr($id) . "','" . sqlEscStr($pt_ex_id) . "',
                                '" . sqlEscStr($pt_med_id) . "', '" . sqlEscStr($usr_ex_id) . "', '" . $status . "',
                                '" . $created . "', '" . $patientComments . "', '0','" . $created_on . "','" . $operator . "',
								'" . $pt_med_name . "','" . $pt_allergies . "','" . $last_enc_date . "'
                                )";
					
                    $lnsq = count($ar_sql);
                }
                $ar_uniq_req_id[] = $id;
            }

            if ($lnsq > 0 && ($lnsq == 100 || $len - 1 == $k)) {
                $sql = "INSERT INTO erp_iportal_medication_refill( portal_req_id, patientExternalId, patientMedicationExternalId, doctorExternalId,
                                status, portalCreated, patientComments, approved_declined, created_on, operator, pt_med_name,pt_allery_name,last_enc_date
                          ) VALUES " . implode(",", $ar_sql);

                sqlQuery($sql);
                $ar_sql = array();
            }

            //acknw Pt Portal
            //$this->send_acknow($ar_uniq_req_id);
        }
    }

    public function is_med_refill_req_exists($id = '') {
        $ret = 0;
        if (!empty($id)) {
            $sql = "SELECT id FROM erp_iportal_medication_refill WHERE portal_req_id='" . sqlEscStr($id) . "' ";
            $row = sqlQuery($sql);
            if ($row != false && !empty($row["id"])) {
                $ret = $row["id"];
            }
        }
        return $ret;
    }
	
	function get_pt_medication_details($pt_ex_id='',$pt_med_id='',$usr_ex_id='') {
		$returnArr=array();
		/*Medication name for the refill request*/
		// AND erp_id!=''
		$medRow=array();
		$medSql="SELECT id,title,erp_id  FROM lists WHERE pid='".$pt_ex_id."' AND type IN (1,4) AND id ='".$pt_med_id."' AND allergy_status='Active' ";
		$medRs=imw_query($medSql);
		if($medRs && imw_num_rows($medRs)>0){
			$medRow=imw_fetch_assoc($medRs);
		}
		
		/*Last encounter date of patient last visit */
		//AND erp_chart_id!='' 
		$encRow=array();
		$encSql="SELECT date_of_service, time_of_service, erp_chart_id FROM chart_master_table WHERE patient_id='".$pt_ex_id."' order by date_of_service desc ";
		$encRs=imw_query($encSql);
		if($encRs && imw_num_rows($encRs)>0){
			$encRow=imw_fetch_assoc($encRs);
		}
		
		/*Allergies of patient*/
		$allergyRow=array();
		$allergySql="SELECT title,erp_id  FROM lists WHERE pid='".$pt_ex_id."' AND type=7 AND allergy_status='Active' ";
		$allergyRs=imw_query($encSql);
		if($allergyRs && imw_num_rows($allergyRs)>0){
			while($alrgRow=imw_fetch_assoc($allergyRs)) {
				$allergyRow[]=$alrgRow['title'];
			}
			
		}
		
		$last_dos='';
		if(count($encRow)>0 && isset($encRow['date_of_service']) && $encRow['date_of_service']!='' && $encRow['date_of_service']!='0000-00-00') {
			$last_dos=$encRow['date_of_service'].' '.$encRow['time_of_service'];
		}
	
		$returnArr['med_id']=$medRow['id'];
		$returnArr['medication_name']=$medRow['title'];
		$returnArr['last_dos']=$last_dos;
		$returnArr['pt_allergies']=$allergyRow;
		
        return $returnArr;
	}
	
	
	public function get_doctors_ids() {
		
		$all_doctors=array();
		$sql="select id,portal_refill_direct_access,user_type from users where user_type='1' and delete_status='0' ";
		$rs=imw_query($sql);
		if($rs && imw_num_rows($rs)>0){
			while( $row = imw_fetch_assoc($rs) ) {
				if($_SESSION['authId']==$row['id']) {
					$all_doctors[]=$row['id'];
				}
				if( isset($row['portal_refill_direct_access']) && $row['portal_refill_direct_access']!='' ) {
					$refill_users=explode(',',$row['portal_refill_direct_access']);
					if( in_array($_SESSION['authId'],$refill_users) ) {
						$all_doctors[]=$row['id'];
					}
				}
			}
		}
		return $all_doctors;
	}
	
	
	public function getMedicationRefillRequestData() {
		$doctor_id_Arr = $this->get_doctors_ids();
		$doctor_id=implode(',',$doctor_id_Arr);
		
		$html='';
		$qry="SELECT * FROM erp_iportal_medication_refill WHERE approved_declined='0' AND doctorExternalId IN(".$doctor_id.") order by id desc ";
		$rs=imw_query($qry);
		if($rs && imw_num_rows($rs)>0){
			while($medRow=imw_fetch_assoc($rs)) {
				$medRowId=$medRow['id'];
				$ptid=$medRow['patientExternalId'];
				$oPt = new Patient($medRow['patientExternalId']);
				$pt_nm = $oPt->getName("7");
				
				$pt_allery_arr=json_decode($medRow['pt_allery_name'],true);
				$pt_allergies='';
				if(isset($pt_allery_arr[0]) && count($pt_allery_arr[0])>0)
					$pt_allergies=implode(', ',$pt_allery_arr);
				
				$last_enc_date='';
				if(isset($medRow['last_enc_date']) && $medRow['last_enc_date']!='' && $medRow['last_enc_date']!='0000-00-00 00:00:00'){
					$last_enc_date=date('m-d-Y',strtotime($medRow['last_enc_date']));
				}
				
				$html.='<tr>
					<td>'.$last_enc_date.'</td>
					<td><span class="text_purple pointer" id="erx_popup" data-ptid="'.$ptid.'" >'.$pt_nm.'</span></td>
					<td>'.$medRow['pt_med_name'].'</td>
					<td>'.$pt_allergies.'</td>
					<td>'.$medRow['patientComments'].'</td>
					<td>
						<button type="button" class="btn btn-xs btn-success btn_aprv" data-med_refill_id="'.$medRowId.'">Approve</button>
						<button type="button" class="btn btn-xs btn-danger btn_dcln" data-med_refill_id="'.$medRowId.'">Decline</button>
					</td>
				</tr>';
			}
		}
		return $html; 
	}
	
    public function is_valid_pt($id) {
        $ret = 0;
        $id = trim($id);
        if (!empty($id)) {
            $sql = "Select id from patient_data where id=$id";
            $rs = imw_query($sql);
            if ($rs && imw_num_rows($rs) == 1) {
                $ret = 1;
            }
        }
        return $ret;
    }

}