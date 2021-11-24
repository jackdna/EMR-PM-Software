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
require_once '../../config/globals.php';

//-----  Get data from remote server -------------------
include_once($GLOBALS['srcdir'] . "/classes/audit_common_function.php");

$sel_op = $_REQUEST["sel_op"];
$row_id = (int)$_REQUEST["row_id"];
$iportal_filter = (isset($_REQUEST['iportal_filter']) && empty($_REQUEST['iportal_filter']) == false) ? $_REQUEST['iportal_filter'] : false;
$iportal_patient_id = (isset($_REQUEST['iportal_pt']) && empty($_REQUEST['iportal_pt']) == false && $_REQUEST['iportal_pt']!='undefined') ? $_REQUEST['iportal_pt'] : $_SESSION['patient'];
$alert_filter = $_REQUEST['alert_filter'];
$is_approved=false;
//Returns iPortal Requested data array based on its call

$patient_id = $_SESSION['patient'];
$logged_provider_id = $_SESSION['authId'];
$policyStatus = 0;
$policyStatus = (int)$_SESSION['AUDIT_POLICIES']['Patient_record_Created_Viewed_Updated'];
if($policyStatus == 1){
	$ip = getRealIpAddr();
	$URL = $_SERVER['PHP_SELF'];
	$os = getOS();
	$browserInfoArr = array();
	$browserInfoArr = _browser();
	$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
	$browserName = str_replace(";","",$browserInfo);
	$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
}

if($iportal_filter){
	$return_arr = array();
	switch($iportal_filter){
		case 'medical':
			$iportal_ch_qry = "SELECT id,tb_name,col_lbl, old_val, new_val, old_val_lbl, new_val_lbl,title_msg,col_name  FROM iportal_req_changes WHERE pt_id = ".$iportal_patient_id." and del_status = 0 and is_approved = 0 and tb_name IN('social_history','immunizations','lists','general_medicine','ocular')";

			$iportal_ch_obj = imw_query($iportal_ch_qry);
			$iportal_ch_cnt = imw_num_rows($iportal_ch_obj);

			$chRowIdArr = array();
			$return_arr['data_length'] = $iportal_ch_cnt;

			if( $iportal_ch_cnt > 0 )
			{
				$return_arr['data'] = array();
				$tempArrayAudit = array();
				while($iportal_ch_row = imw_fetch_assoc($iportal_ch_obj))
				{
					$tempArrayAudit = $iportal_ch_row;
					$old_val = trim($iportal_ch_row["old_val_lbl"]) == "" ? imw_real_escape_string($iportal_ch_row["old_val"]) : imw_real_escape_string($iportal_ch_row["old_val_lbl"]);
					$old_val = str_replace("'",'"',str_ireplace("\\","",str_ireplace("\\r\\n",'',$old_val)));

					$new_val = trim($iportal_ch_row["new_val_lbl"]) == "" ? imw_real_escape_string($iportal_ch_row["new_val"]) : imw_real_escape_string($iportal_ch_row["new_val_lbl"]);
					$new_val = str_replace("'",'"',str_ireplace("\\","",str_ireplace("\\r\\n",'',$new_val)));

					if($iportal_ch_row["tb_name"]=="ocular" || $iportal_ch_row["tb_name"]=="general_medicine" || $iportal_ch_row["tb_name"]=="patient_data"){
						if(empty($new_val)){
							$new_val = imw_real_escape_string($iportal_ch_row["new_val_lbl"]);
							$new_val = str_replace("'",'"',str_ireplace("\\","",str_ireplace("\\r\\n",'',$new_val)));
						}
					}

					$col_lbl=trim($iportal_ch_row["col_lbl"])==""?$iportal_ch_row["title_msg"]:$iportal_ch_row["col_lbl"];

					if($iportal_ch_row["tb_name"]=="immunizations" || $iportal_ch_row["tb_name"]=="lists" || $iportal_ch_row["tb_name"]=="general_medicine" || $iportal_ch_row["tb_name"]=="ocular" || $iportal_ch_row["tb_name"]=="social_history"){
						if($old_val != $new_val)
						{
							$reload_tab = ($col_lbl == "Medical HX - Ocular updated")?"yes":"";
							if($iportal_ch_row["tb_name"] == "lists" && ($iportal_ch_row["col_name"] == "begdate" || $iportal_ch_row["col_name"] == "enddate" ))
							{
								$arrDate = explode("-",$new_val);
								$new_val = "";
								if($arrDate[1]!="00"){$new_val .= $arrDate[1];}
								if($arrDate[2]!="00"){$new_val .= ($new_val == "")?$arrDate[2]:"-".$arrDate[2];}
								if($arrDate[0]!="00"){$new_val .= ($new_val == "")?$arrDate[0]:"-".$arrDate[0];}
							}

							$data = array('col_lbl' => $col_lbl, 'old_val' => $old_val , 'new_val' => utf8_decode($new_val),'id' => $iportal_ch_row["id"], 'reload_tab'=> $reload_tab);

							array_push($return_arr['data'],$data);
							$chRowIdArr[] = $iportal_ch_row["id"];

						}
					}
				}
				$return_arr['row_id_str'] = implode(",",$chRowIdArr);

				/* Audit Trail for Popup iPortal changes need approval. */
				if($policyStatus == 1 && sizeof($return_arr) != 0 && (isset($return_arr['data_length']) && $return_arr['data_length'] > 0)){
					$data_arr=array();
					$data_arr["Pk_Id"] = $_SESSION['patient'];
					$data_arr["Table_Name"] = $tempArrayAudit['tb_name'];
					$data_arr["Data_Base_Field_Name"] = $tempArrayAudit['col_name'];
					$data_arr["Field_Label"] = $tempArrayAudit['col_lbl'];
					$data_arr["Old_Value"] = $tempArrayAudit['old_val'];
					$data_arr["New_Value"] = $tempArrayAudit['new_val_lbl'];
					$data_arr["Operater_Id"] = $logged_provider_id;
					$data_arr["Operater_Type"] = getOperaterType($logged_provider_id);
					$data_arr["IP"] = $ip;
					$data_arr["MAC_Address"] = $_REQUEST['macaddrs'];
					$data_arr["URL"] = $URL;
					$data_arr["Browser_Type"] = $browserName;
					$data_arr["OS"] = $os;
					$data_arr["Machine_Name"] = $machineName;
					$data_arr["Category"] = "patient_info-medical_history";
					$data_arr["Category_Desc"] = "medication";
					$data_arr["Action"] = "view";
					$data_arr["Date_Time"] = date('Y-m-d H:i:s');
					$data_arr["pid"] = $_SESSION['patient'];
					AddRecords($data_arr,'audit_trail');
				}

				if((bool)core_check_privilege(array('priv_pt_fdsk'),'all') !== true && (bool)core_check_privilege(array('priv_pt_clinical'),'all') !== true){
					unset($return_arr);
				}
			}
		break;

		case 'demographics':
			$iportal_ch_qry = "SELECT id,tb_name,col_lbl, old_val, new_val, old_val_lbl, new_val_lbl,title_msg,col_name FROM iportal_req_changes WHERE pt_id = ".$iportal_patient_id." and del_status = 0 and is_approved = 0 and tb_name NOT IN('social_history','immunizations','lists','general_medicine','ocular')";

			$iportal_ch_obj = imw_query($iportal_ch_qry);
			$iportal_ch_cnt = imw_num_rows($iportal_ch_obj);

			$chRowIdArr = array();
			$return_arr['data_length'] = $iportal_ch_cnt;
			if( $iportal_ch_cnt > 0 ){
				$return_arr['data'] = array();
				while($iportal_ch_row = imw_fetch_assoc($iportal_ch_obj)){
					$old_val = trim($iportal_ch_row["old_val_lbl"]) == "" ? imw_real_escape_string($iportal_ch_row["old_val"]) : imw_real_escape_string($iportal_ch_row["old_val_lbl"]);
					$old_val = str_replace("'",'"',str_ireplace("\\","",str_ireplace("\\r\\n",'',$old_val)));

					$new_val = trim($iportal_ch_row["new_val_lbl"]) == "" ? imw_real_escape_string($iportal_ch_row["new_val"]) : imw_real_escape_string($iportal_ch_row["new_val_lbl"]);
					$new_val = str_replace("'",'"',str_ireplace("\\","",str_ireplace("\\r\\n",'',$new_val)));

					if($iportal_ch_row["tb_name"]=="patient_data" || $iportal_ch_row["tb_name"]=="resp_party" || $iportal_ch_row["tb_name"]=="insurance_data" || $iportal_ch_row["tb_name"]=="patient_family_info"){
						$col_lbl=trim($iportal_ch_row["col_lbl"])==""?$iportal_ch_row["title_msg"]:$iportal_ch_row["col_lbl"];
						if($iportal_ch_row["tb_name"]=="resp_party" && $iportal_ch_row["col_name"] == "hippa_release_status"){
							$old_val = ($old_val == "1")?"Yes":(($old_val == "0")?"No":"");
							$new_val = ($new_val == "1")?"Yes":(($new_val == "0")?"No":"");
						}
						$data = array('col_lbl' => $col_lbl, 'old_val' => $old_val , 'new_val' => utf8_decode($new_val),'id' => $iportal_ch_row["id"],'table_name'=>$iportal_ch_row["tb_name"], 'col_name'=>$iportal_ch_row["col_name"]);
						array_push($return_arr['data'],$data);
						$chRowIdArr[] = $iportal_ch_row["id"];
					}
				}
				$return_arr['row_id_str'] = implode(",",$chRowIdArr);

				/* Audit Trail for Popup iPortal changes need approval. */
				if($policyStatus == 1 && sizeof($return_arr) != 0 && (isset($return_arr['data_length']) && $return_arr['data_length'] > 0)){
					foreach ($return_arr['data'] as $key => $at) {
						$data_arr=array();
						$data_arr["Pk_Id"] = $patient_id;
						$data_arr["Table_Name"] = $at['table_name'];
						$data_arr["Data_Base_Field_Name"] = $at['col_name'];
						$data_arr["Data_Base_Field_Type"] = fun_get_field_type($patientDataFields,$at['col_name']);
						$data_arr["Filed_Text"] = $at['col_name'];
						$data_arr["Field_Label"] = $at['col_lbl'];
						$data_arr["Old_Value"] = $at['old_val'];
						$data_arr["New_Value"] = $at['new_val'];
						$data_arr["Operater_Id"] = $logged_provider_id;
						$data_arr["Operater_Type"] = getOperaterType($logged_provider_id);
						$data_arr["IP"] = $ip;
						$data_arr["MAC_Address"] = $_REQUEST['macaddrs'];
						$data_arr["URL"] = $URL;
						$data_arr["Browser_Type"] = $browserName;
						$data_arr["OS"] = $os;
						$data_arr["Machine_Name"] = $machineName;
						$data_arr["Category"] = "patient_info";
						$data_arr["Category_Desc"] = 'demographics';
						$data_arr["Action"] = "view";
						$data_arr["Date_Time"] = date('Y-m-d H:i:s');
						$data_arr["pid"] = $patient_id;
						AddRecords($data_arr,'audit_trail');
					}
				}
			}
			if((bool)core_check_privilege(array('priv_pt_fdsk'),'all') !== true && (bool)core_check_privilege(array('priv_pt_clinical'),'all') !== true){
				unset($return_arr);
			}
		break;

		case 'registered_patients':
			$iportal_ch_qry = "SELECT id,fname,lname,email,DATE_FORMAT(`dob`, '%m-%d-%Y') AS 'dob',sex,address,city,postal_code,phone_home,phone_cell,phone_biz,phone_biz_ext,auth_status FROM `iportal_register_patient` WHERE `approved`=0";

			$iportal_ch_obj = imw_query($iportal_ch_qry);
			$iportal_ch_cnt = imw_num_rows($iportal_ch_obj);

			$chRowIdArr = array();
			$return_arr['data_length'] = $iportal_ch_cnt;
			if( $iportal_ch_cnt > 0 ){
				$return_arr['data'] = array();

				while($iportal_ch_row = imw_fetch_assoc($iportal_ch_obj)){
					$iportal_ch_row['auth_status'] = ($iportal_ch_row['auth_status'])?'Verified':'Not Verified';
					array_push($return_arr['data'],$iportal_ch_row);
					$chRowIdArr[] = $iportal_ch_row["id"];
				}
			}
			$return_arr['row_id_str'] = implode(",",$chRowIdArr);
			if((bool)core_check_privilege(array('priv_pt_fdsk'),'all') !== true && (bool)core_check_privilege(array('priv_pt_clinical'),'all') !== true){
				unset($return_arr);
			}
		break;

		case 'cl_order':
			$iportal_ch_qry = "Select cl_orders.*, pd.fname, pd.mname, pd.lname, DATE_FORMAT(cl_orders.ordered_date, '%m-%d-%Y') as 'orderedDate',(Select COUNT(temp_order_num) FROM iportal_req_orders WHERE temp_order_num=cl_orders.temp_order_num) as 'orderCount' FROM iportal_req_orders cl_orders LEFT JOIN patient_data pd ON pd.id=cl_orders.patient_id WHERE cl_orders.order_for='cl' AND cl_orders.is_approved='0' ORDER BY cl_orders.temp_order_num DESC, cl_orders.eye ASC";

			$iportal_ch_obj = imw_query($iportal_ch_qry);
			$iportal_ch_cnt = imw_num_rows($iportal_ch_obj);

			$chRowIdArr = array();
			$return_arr['data_length'] = $iportal_ch_cnt;
			if( $iportal_ch_cnt > 0 ){
				$return_arr['data'] = array();
				while($iportal_ch_row = imw_fetch_assoc($iportal_ch_obj)){
					$tmp_arr = array();
					$patName = $iportal_ch_row['lname'].', '.$iportal_ch_row['fname'].' - '.$iportal_ch_row['patient_id'];
					$supplies = $iportal_ch_row['supplies'].' Month';
					if($iportal_ch_row['supplies'] == 12){ $supplies = '1 Year'; }
					$disposable = ucfirst($iportal_ch_row['disposable']);
					$iportal_ch_row['supplies'] = $supplies;
					$iportal_ch_row['disposable'] = $disposable;

					$tmp_arr['pt_id'] = $iportal_ch_row['patient_id'];
					$tmp_arr['clws_id'] = $iportal_ch_row['clws_id'];
					$tmp_arr['pt_name'] = $patName;
					$tmp_arr['id'] = $iportal_ch_row['id'];
					$tmp_arr['disposable'] = ucfirst($iportal_ch_row['disposable']);
					$tmp_arr['supplies'] = $iportal_ch_row['supplies'];
					$tmp_arr['brand'] = $iportal_ch_row['brand'];
					$tmp_arr['manufacturer'] = $iportal_ch_row['manufacturer'];
					$tmp_arr['eye'] = $iportal_ch_row['eye'];
					$tmp_arr['orderedDate'] = $iportal_ch_row['orderedDate'];
					$tmp_arr['boxes'] = $iportal_ch_row['boxes'];
					$tmp_arr['temp_order_num'] = $iportal_ch_row['temp_order_num'];
					$tmp_arr['order_count'] = $iportal_ch_row['orderCount'];

					$return_arr['data'][] = $tmp_arr;
					$chRowIdArr[] = $iportal_ch_row['temp_order_num'];
				}
				$return_arr['row_id_str'] = implode(",",$chRowIdArr);

				/* Audit Trail for Popup iPortal (Contact Lens Order) */
				if($policyStatus == 1 && sizeof($return_arr) != 0 && (isset($return_arr['data_length']) && $return_arr['data_length'] > 0)){
					foreach ($return_arr['data'] as $key => $cl) {
						$data_arr=array();
						$data_arr["Pk_Id"] = $_SESSION['patient'];
						$data_arr["Table_Name"] = '';
						$data_arr["Data_Base_Field_Name"] = '';
						$data_arr["Data_Base_Field_Type"] = '';
						$data_arr["Filed_Text"] = '';
						$data_arr["Field_Label"] = '';
						$data_arr["Old_Value"] = '';
						$data_arr["New_Value"] = serialize($cl);
						$data_arr["Operater_Id"] = $logged_provider_id;
						$data_arr["Operater_Type"] = getOperaterType($logged_provider_id);
						$data_arr["IP"] = $ip;
						$data_arr["MAC_Address"] = $_REQUEST['macaddrs'];
						$data_arr["URL"] = $URL;
						$data_arr["Browser_Type"] = $browserName;
						$data_arr["OS"] = $os;
						$data_arr["Machine_Name"] = $machineName;
						$data_arr["Category"] = "patient_info-medical_history";
						$data_arr["Category_Desc"] = "order_set";
						$data_arr["Action"] = "view";
						$data_arr["Date_Time"] = date('Y-m-d H:i:s');
						$data_arr["pid"] = $_SESSION['patient'];
						// pre($data_arr);
						AddRecords($data_arr,'audit_trail');
					}
				}
			}
			if((bool)core_check_privilege(array('priv_pt_fdsk'),'all') !== true){
				unset($return_arr);
			}
		break;


        case 'iportal_payments':
            $iportal_ch_qry = "SELECT id,pt_id,tb_name,title_msg,new_val,new_val_lbl,new_val_arr,reqDateTime
                            FROM iportal_req_changes
                            WHERE pt_id = ".$iportal_patient_id."
                            AND del_status = 0
                            AND is_approved = 3
                            AND tb_name='patient_pre_payment'
                            ORDER BY id DESC ";

			$iportal_ch_obj = imw_query($iportal_ch_qry);
			$iportal_ch_cnt = imw_num_rows($iportal_ch_obj);

			$chRowIdArr = array();
			$return_arr['data_length'] = $iportal_ch_cnt;

			if( $iportal_ch_cnt > 0 )
			{
				$return_arr['data'] = array();
				$tempArrayAudit = array();
				while($iportal_ch_row = imw_fetch_assoc($iportal_ch_obj))
				{
                    $col_lbl=$iportal_ch_row["title_msg"];
                    $new_val = $iportal_ch_row["new_val_lbl"];
                    //$new_val = str_replace("'",'"',str_ireplace("\\","",str_ireplace("\\r\\n",'',$new_val)));

                    $data = array('col_lbl' => $col_lbl,'new_val' => utf8_decode($new_val),'id' => $iportal_ch_row["id"]);

                    $return_arr['data'][] = $data;
                    $chRowIdArr[] = $iportal_ch_row["id"];

                }
                $return_arr['row_id_str'] = implode(",",$chRowIdArr);
            }
		break;


	}
	echo json_encode($return_arr);
	die;
}
switch($sel_op)
{
	case "approve":
	$tbl = $_REQUEST["tbl"];
	$cancel_req_id = $_REQUEST["cancel_req_id"];
	if($tbl=="iportal_app_reqs"){
		if($cancel_req_id) {
			include_once($GLOBALS['fileroot']."/interface/scheduler/appt_cancel_portal_handle.php");
		}else {
			include_once($GLOBALS['srcdir']."/erp_portal/rabbitmq_exchange.php");
			include_once($GLOBALS['srcdir'].'/erp_portal/appointmentrequests.php');
			$oApReq = new AppointmentRequests();
			$oApReq->updatePortal($row_id, '1','1');
		}
	}else if($tbl=="iportal_pghd_reqs"){
		//include_once($GLOBALS['srcdir'] . "/erp_portal/erp_portal_core.php");
		//include_once($GLOBALS['srcdir'].'/erp_portal/pghd_requests.php');
		//$Pghd_requests = new Pghd_requests();
		//$Pghd_requests->updatePortal($row_id, '1','1');
	}else{
		$iportal_qry = "SELECT * FROM iportal_req_changes WHERE is_approved = 0 and id = ".$row_id;
		$iportal_obj = imw_query($iportal_qry);
		$patientDataFields = $insuranceDataFields = array();
		$patientDataFields = make_field_type_array("patient_data");
		$insuranceDataFields = make_field_type_array("insurance_data");
		$respPartyDataFields = make_field_type_array("resp_party");
		$medicalHxDataFields = make_field_type_array("lists");

		if(imw_num_rows($iportal_obj) > 0)
		{
			$iportal_data = imw_fetch_assoc($iportal_obj);
			$iportal_extreme_action = 0;

			$iportal_extreme_action = iportal_special_changes($iportal_data,$row_id);

			if($iportal_extreme_action == 0)
			{
				if($iportal_data["action"] == "edit" && trim($iportal_data["patientIdColName"]) != "")
				{
					$update_qry = "UPDATE ".$iportal_data["tb_name"]." SET ".$iportal_data["col_name"]." = '".$iportal_data["new_val"]."' WHERE ".$iportal_data["patientIdColName"]." = '".$iportal_data["pt_id"]."' && ".$iportal_data["pri_col_name"]." = '".$iportal_data["col_pri_id"]."'";
					$query_run = imw_query($update_qry);
					if($query_run)
					{
						// Audit Trail for approval of iportal changes from popup
						if($policyStatus == 1){
							$data_arr=array();
							$data_arr["Pk_Id"] = $iportal_data["col_pri_id"];
							$data_arr["Table_Name"] = $iportal_data["tb_name"];
							$data_arr["Data_Base_Field_Name"] = $iportal_data['col_name'];
							$data_arr["Data_Base_Field_Type"] = fun_get_field_type($patientDataFields,$iportal_data['col_name']);
							$data_arr["Filed_Text"] = $iportal_data['col_name'];
							$data_arr["Field_Label"] = $iportal_data['col_lbl'];
							$data_arr["Old_Value"] = $iportal_data['old_val'];
							$data_arr["New_Value"] = $iportal_data['new_val'];
							$data_arr["Operater_Id"] = $logged_provider_id;
							$data_arr["Operater_Type"] = getOperaterType($logged_provider_id);
							$data_arr["IP"] = $ip;
							$data_arr["MAC_Address"] = $_REQUEST['macaddrs'];
							$data_arr["URL"] = $URL;
							$data_arr["Browser_Type"] = $browserName;
							$data_arr["OS"] = $os;
							$data_arr["Machine_Name"] = $machineName;
							$data_arr["Category"] = "patient_info";
							$data_arr["Category_Desc"] = 'demographics';
							$data_arr["Action"] = "approve";
							$data_arr["Date_Time"] = date('Y-m-d H:i:s');
							$data_arr["pid"] = $iportal_data["pt_id"];
							AddRecords($data_arr,'audit_trail');
						}

						$req_qry = "UPDATE iportal_req_changes SET is_approved=1 WHERE id = ".$row_id;
						imw_query($req_qry);
						$is_approved=true;
						echo "approved";
					}
				}
				else if($iportal_data["action"] == "insert" && trim($iportal_data["tb_name"]) != "")
				{
					$insert_qry = $iportal_data["new_val"];
					$log_qry = $iportal_data["log_query"];
					if(strpos($insert_qry,$iportal_data["tb_name"]) !== false)
					{
						$query_run = imw_query($insert_qry);
						if($query_run)
						{
							$lastInsertId = imw_insert_id();

							// Audit Trail for approval of iportal changes (Insurance Data) from popup
							if($policyStatus == 1 && trim($iportal_data["tb_name"]) == "insurance_data") {
								$sql = "SELECT provider,type,policy_number, group_number, copay, effective_date, actInsComp, pid,subscriber_relationship,ins_caseid,subscriber_lname,subscriber_fname,subscriber_mname,subscriber_suffix,subscriber_ss,subscriber_DOB,subscriber_street,subscriber_street_2,subscriber_postal_code,subscriber_city,subscriber_state,subscriber_country,subscriber_phone FROM ".$iportal_data["tb_name"]." WHERE id = ".$lastInsertId;
								$sql_result = imw_query($sql);
								$iData = imw_fetch_assoc($sql_result);

								foreach ($iData as $key => $value) {
									$data_arr=array();
									$data_arr["Pk_Id"] = $lastInsertId;
									$data_arr["Table_Name"] = $iportal_data["tb_name"];
									$data_arr["Data_Base_Field_Name"] = $key;
									$data_arr["Data_Base_Field_Type"] = fun_get_field_type($insuranceDataFields,$key);
									$data_arr["Filed_Text"] = $key;
									$data_arr["Field_Label"] = $key;
									$data_arr["Old_Value"] = '';
									$data_arr["New_Value"] = $value;
									$data_arr["Operater_Id"] = $logged_provider_id;
									$data_arr["Operater_Type"] = getOperaterType($logged_provider_id);
									$data_arr["IP"] = $ip;
									$data_arr["MAC_Address"] = $_REQUEST['macaddrs'];
									$data_arr["URL"] = $URL;
									$data_arr["Browser_Type"] = $browserName;
									$data_arr["OS"] = $os;
									$data_arr["Machine_Name"] = $machineName;
									$data_arr["Category"] = "patient_info";
									$data_arr["Category_Desc"] = 'insurence';
									$data_arr["Action"] = "approve";
									$data_arr["Date_Time"] = date('Y-m-d H:i:s');
									$data_arr["pid"] = $iData["pid"];
									AddRecords($data_arr,'audit_trail');
								}
							}
							// Audit Trail for approval of iportal changes (Responsible Party Data) from popup
							if($policyStatus == 1 && trim($iportal_data["tb_name"]) == "resp_party") {
								$sql = "SELECT title,fname,lname,mname,relation,suffix,hippa_release_status,marital,address,address2,zip,zip_ext,city,state,email,home_ph,work_ph,mobile,patient_id FROM ".$iportal_data["tb_name"]." WHERE id = ".$lastInsertId;
								$sql_result = imw_query($sql);
								$iData = imw_fetch_assoc($sql_result);
								$patient_id = $iData["patient_id"];
								unset($iData["patient_id"]);

								foreach ($iData as $key => $value) {
									$data_arr=array();
									$data_arr["Pk_Id"] = $lastInsertId;
									$data_arr["Table_Name"] = $iportal_data["tb_name"];
									$data_arr["Data_Base_Field_Name"] = $key;
									$data_arr["Data_Base_Field_Type"] = fun_get_field_type($respPartyDataFields,$key);
									$data_arr["Filed_Text"] = $key;
									$data_arr["Field_Label"] = $key;
									$data_arr["Old_Value"] = '';
									$data_arr["New_Value"] = $value;
									$data_arr["Operater_Id"] = $logged_provider_id;
									$data_arr["Operater_Type"] = getOperaterType($logged_provider_id);
									$data_arr["IP"] = $ip;
									$data_arr["MAC_Address"] = $_REQUEST['macaddrs'];
									$data_arr["URL"] = $URL;
									$data_arr["Browser_Type"] = $browserName;
									$data_arr["OS"] = $os;
									$data_arr["Machine_Name"] = $machineName;
									$data_arr["Category"] = "patient_info";
									$data_arr["Category_Desc"] = 'demographics';
									$data_arr["Action"] = "approve";
									$data_arr["Date_Time"] = date('Y-m-d H:i:s');
									$data_arr["pid"] = $patient_id;
									AddRecords($data_arr,'audit_trail');
								}
							}
							// Audit Trail for approval of iportal changes (Medical HX Data) from popup
							if($policyStatus == 1 && trim($iportal_data["tb_name"]) == "lists") {
								$sql = "SELECT type, title, begdate, comments, pid, severity FROM ".$iportal_data["tb_name"]." WHERE id = ".$lastInsertId;
								$sql_result = imw_query($sql);
								$iData = imw_fetch_assoc($sql_result);
								$pid = $iData["pid"];
								unset($iData["pid"]);

								$type = $iData["type"];
								$auditType = '';
								unset($iData["type"]);
								switch ($type) {
									case 7:
										$auditType = 'allergy';
										break;
									case 1:
										$auditType = 'medication systemic';
										break;
									case 4:
										$auditType = 'medication ocular';
										break;
									default:
										$auditType = 'patient_info-medical_history';
										break;
								}

								foreach ($iData as $key => $value) {
									$data_arr=array();
									$data_arr["Pk_Id"] = $lastInsertId;
									$data_arr["Table_Name"] = $iportal_data["tb_name"];
									$data_arr["Data_Base_Field_Name"] = $key;
									$data_arr["Data_Base_Field_Type"] = fun_get_field_type($medicalHxDataFields,$key);
									$data_arr["Filed_Text"] = $key;
									$data_arr["Field_Label"] = $key;
									$data_arr["Old_Value"] = '';
									$data_arr["New_Value"] = $value;
									$data_arr["Operater_Id"] = $logged_provider_id;
									$data_arr["Operater_Type"] = getOperaterType($logged_provider_id);
									$data_arr["IP"] = $ip;
									$data_arr["MAC_Address"] = $_REQUEST['macaddrs'];
									$data_arr["URL"] = $URL;
									$data_arr["Browser_Type"] = $browserName;
									$data_arr["OS"] = $os;
									$data_arr["Machine_Name"] = $machineName;
									$data_arr["Category"] = "patient_info-medical_history";
									$data_arr["Category_Desc"] = $auditType;
									$data_arr["Action"] = "approve";
									$data_arr["Date_Time"] = date('Y-m-d H:i:s');
									$data_arr["pid"] = $pid;
									AddRecords($data_arr,'audit_trail');
								}
							}

							$req_qry = "UPDATE iportal_req_changes SET is_approved=1 WHERE id = ".$row_id;
							imw_query($req_qry);
							$is_approved=true;
							echo "approved";
						}
						if(trim($log_qry) != "")
						{
							$query_run = imw_query($log_qry);
						}
					}

					if(trim($iportal_data["tb_name"]) == "insurance_data")
					{
						$req_qry = "UPDATE iportal_req_changes SET is_approved=1 WHERE id = ".$row_id;
						imw_query($req_qry);
					}
				}
				else if(($iportal_data["action"] == "updateQry" || $iportal_data["action"] == "update") && trim($iportal_data["tb_name"]) != "")
				{
					$update_qry = $iportal_data["new_val"];
					$log_qry = $iportal_data["log_query"];
					if(strpos($update_qry,$iportal_data["tb_name"]) !== false)
					{
						$query_run = imw_query($update_qry);
						if($query_run)
						{
							$req_qry = "UPDATE iportal_req_changes SET is_approved=1 WHERE id = ".$row_id;
							imw_query($req_qry);
							$is_approved=true;
							echo "approved";
						}
						if(trim($log_qry) != "")
						{
							$query_run = imw_query($log_qry);
						}
					}
				}else if(($iportal_data["action"]=="add") && ($iportal_data["tb_name"]=="user_messages")){

					$qry_insert="INSERT INTO user_messages set message_subject='PGHD- Patient Health Information',message_text='".$iportal_data["new_val_lbl"]."',message_to='".$_SESSION["authId"]."', message_sender_id='".$_SESSION["authId"]."',patientId='".$iportal_data["pt_id"]."',message_send_date='".$iportal_data["reqDateTime"]."',Pt_Communication='1',msg_type='2'";
					$res_insert=imw_query($qry_insert);
					if($res_insert){
						$req_qry = "UPDATE iportal_req_changes SET is_approved=1 WHERE id = ".$row_id;
						imw_query($req_qry);
						$is_approved=true;
						echo "approved";
					}
				}
			}
			//save alert field for appointment
			if(($alert_filter == 'medical' || $alert_filter == 'demographics') && $is_approved==true){
				//get upcoming appointment id
				$q="select id from schedule_appointments
				WHERE sa_patient_app_status_id NOT IN (203,201,18,19,20)
				AND IF( sa_patient_app_status_id =271, sa_patient_app_show =0, sa_patient_app_show <>2 )
				AND sa_patient_id='".$iportal_data["pt_id"]."'
				AND DATE_FORMAT( CONCAT(sa_app_start_date,' ',sa_app_starttime),'%Y-%m-%d %H:%i:%s')>='".date("Y-m-d H:i:s")."'
				ORDER BY id ASC
				LIMIT 1";
				$d=imw_query($q);
				if(imw_num_rows($d)>0){
					$res=imw_fetch_assoc($d);
					if($res['id'])imw_query("update schedule_appointments set pt_info_updt_alert=CONCAT(pt_info_updt_alert,'~:~".$alert_filter."')
					WHERE id='".$res['id']."'");
				}
			}
		}
	}
	break;
	case "decline":
	$tbl = $_REQUEST["tbl"];
	$cancel_req_id = $_REQUEST["cancel_req_id"];
	if($tbl=="iportal_app_reqs"){
		if($cancel_req_id) {
			include_once($GLOBALS['fileroot']."/interface/scheduler/appt_cancel_portal_handle.php");
		}else {
			include_once($GLOBALS['srcdir']."/erp_portal/rabbitmq_exchange.php");
			include_once($GLOBALS['srcdir'].'/erp_portal/appointmentrequests.php');
			$oApReq = new AppointmentRequests();		
			$oApReq->updatePortal($row_id, '2','1');
		}
	}else if($tbl=="iportal_pghd_reqs"){
		//include_once($GLOBALS['srcdir'] . "/erp_portal/erp_portal_core.php");
		//include_once($GLOBALS['srcdir'].'/erp_portal/pghd_requests.php');
		//$Pghd_requests = new Pghd_requests();
		//$Pghd_requests->updatePortal($row_id, '2','1');
	}else{
		// Audit Trail for approval of iportal changes from popup
		$iportal_qry = "SELECT * FROM iportal_req_changes WHERE is_approved = 0 and id = ".$row_id;
		$iportal_obj = imw_query($iportal_qry);
		if(imw_num_rows($iportal_obj) > 0)
		{
			$iportal_data = imw_fetch_assoc($iportal_obj);
			$iportal_extreme_action = 0;
			$iportal_extreme_action = iportal_special_changes($iportal_data,$row_id);
			if($iportal_extreme_action == 0)
			{
				if($policyStatus == 1){
					$data_arr=array();
					$data_arr["Pk_Id"] = $iportal_data['pt_id'];
					$data_arr["Table_Name"] = $iportal_data['tb_name'];
					$data_arr["Data_Base_Field_Name"] = $iportal_data['col_name'];
					$data_arr["Data_Base_Field_Type"] = fun_get_field_type($patientDataFields,$iportal_data['col_name']);
					$data_arr["Filed_Text"] = $iportal_data['col_name'];
					$data_arr["Field_Label"] = $iportal_data['col_lbl'];
					$data_arr["Old_Value"] = $iportal_data['old_val'];
					$data_arr["New_Value"] = $iportal_data['new_val'];
					$data_arr["Operater_Id"] = $logged_provider_id;
					$data_arr["Operater_Type"] = getOperaterType($logged_provider_id);
					$data_arr["IP"] = $ip;
					$data_arr["MAC_Address"] = $_REQUEST['macaddrs'];
					$data_arr["URL"] = $URL;
					$data_arr["Browser_Type"] = $browserName;
					$data_arr["OS"] = $os;
					$data_arr["Machine_Name"] = $machineName;
					$data_arr["Category"] = "patient_info";
					$data_arr["Category_Desc"] = 'demographics';
					$data_arr["Action"] = "decline";
					$data_arr["Date_Time"] = date('Y-m-d H:i:s');
					$data_arr["pid"] = $iportal_data["pt_id"];
					AddRecords($data_arr,'audit_trail');
				}
			}
		}
		// End Audit Trail for approval of iportal changes from popup

		$req_qry = "UPDATE iportal_req_changes SET is_approved=2 WHERE id = ".$row_id;
		imw_query($req_qry);
	}
	break;

    case 'iportal_payment_read':
        $req_qry = "UPDATE iportal_req_changes SET is_approved=1, operator_id=".$_SESSION["authId"]." WHERE id = ".$row_id;
		imw_query($req_qry);
        break;

}

function iportal_special_changes($iportal_data,$row_id)
{
	$tb_name  = $iportal_data["tb_name"];
	$col_name = $iportal_data["col_name"];

	if($tb_name == "insurance_data" && $col_name == "provider")
	{
		$changed_val = $iportal_data["new_val"];
		$changed_val = imw_real_escape_string($changed_val);
		$change_val = array_pop(explode('-',$changed_val));
		$req_qry = "SELECT id FROM insurance_companies WHERE id='".$change_val."' and ins_del_status='0' LIMIT 1";
		$req_qry_obj = imw_query($req_qry);
		if(imw_num_rows($req_qry_obj) > 0)
		{
			$insurance_data = imw_fetch_assoc($req_qry_obj);
			$insurance_data_id = $insurance_data["id"];

			$update_qry = "UPDATE ".$iportal_data["tb_name"]." SET ".$iportal_data["col_name"]." = '".$insurance_data_id."' WHERE ".$iportal_data["patientIdColName"]." = '".$iportal_data["pt_id"]."' && ".$iportal_data["pri_col_name"]." = '".$iportal_data["col_pri_id"]."'";
			$query_run = imw_query($update_qry);
			if($query_run)
			{
				$req_qry = "UPDATE iportal_req_changes SET is_approved=1 WHERE id = ".$row_id;
				imw_query($req_qry);
			}
		}

		return 1;
	}
	else
	{
		return 0;
	}
}
?>
