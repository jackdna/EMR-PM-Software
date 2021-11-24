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
?>
<?php
include_once(dirname(__FILE__) . "/../../config/globals.php");
$webroot_tmp = $GLOBALS['webroot'];
//-----  Get data from remote server -------------------

$zRemotePageName = "chart_notes_order_set";
//require(dirname(__FILE__)."/get_chart_from_remote_server.inc.php");
//-----  Get data from remote server -------------------
//
//require_once(dirname(__FILE__).'/../common/functions.inc.php');
//require_once(dirname(__FILE__).'/../main/main_functions.php');
include_once($GLOBALS['srcdir'] . "/classes/audit_common_function.php");
require_once($GLOBALS['srcdir'] . "/classes/cls_common_function.php");

$objManageData = new CLSCommonFunction;

//$objManageData = new DataManage;

$dayDataArr = array('Days', 'Weeks', 'Months', 'Years');
$contentArr = array();
$contentArr1 = array();

//--- DELETE FROM DATABASE  ----
if (empty($delete_id) == false) {
    $sql = "update order_set_associate_chart_notes set delete_status = '1' 
			where order_set_associate_id = '$delete_id'";
    $rs = imw_query($sql);
    $url = "chart_notes_order_set.php?plan_num=$plan_num&audit_view=1";

    if (constant("REMOTE_SYNC") == 1 && $zOnParentServer == 1) {

        $zRemoteServerData["header"] = $GLOBALS["remote"]['rootdir'] . "/chart_notes/" . $url;
        $flgStopExec = 1;
    } else {
        header("location: " . $url);
    }
}

if (!isset($flgStopExec)) {//$flgStopExec
    $scheduleArr = array('Days', 'Weeks', 'Months', 'Years');

//--- GET AUDIT STAUS CHECK FOR APPLICATION -----
    $policyStatus = 0;
    $policyStatus = (int) $_SESSION['AUDIT_POLICIES']['Order'];

    if ($policyStatus == 1) {
        $ip = getRealIpAddr();
        $URL = $_SERVER['PHP_SELF'];
        $os = getOS();
        $browserInfoArr = array();
        $browserInfoArr = _browser();
        $browserInfo = $browserInfoArr['browser'] . "-" . $browserInfoArr['version'];
        $browserName = str_replace(";", "", $browserInfo);
        $machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
    }

//--- GET ALL USER NAME ----
    /*
      $objManageData->QUERY_STRING = "select id,lname,fname,mname from users";
      $usersQryRes = $objManageData->mysqlifetchdata();
      $providerDetails = array();
      for($i=0;$i<count($usersQryRes);$i++){
      $id = $usersQryRes[$i]['id'];
      $name = $usersQryRes[$i]['lname'].', ';
      $name .= $usersQryRes[$i]['fname'].' ';
      $name .= $usersQryRes[$i]['mname'];
      $name = ucwords(trim($name));
      if($name[0] == ','){
      $name = substr($name,1);
      }
      $providerDetails[$id] = $name;
      }
     */

//--- GET ALL ORDER SET DETAILS ----
    $sql = "select id,orderset_name,order_id,order_set_option from order_sets";
    $orderSetDetails = $objManageData->mysqlifetchdata($sql);
    $orderSetOption = '';
    $orderSetNameArr = array();
    $selectedOrderIdArr1 = array();
    $order_id_arr = array();
    $orderIdArr = array();
    $order_set_option_arr = array();
    $ordersArr = array();
    for ($i = 0; $i < count($orderSetDetails); $i++) {
        $id = $orderSetDetails[$i]['id'];
        $orderset_name = $orderSetDetails[$i]['orderset_name'];
        $orderSetNameArr[$id] = $orderset_name;
        $order_id = $orderSetDetails[$i]['order_id'];
        $order_set_option_arr[$id] = $orderSetDetails[$i]['order_set_option'];
        $ordersArr[$id] = preg_split('/,/', $order_id);
    }

//--- GET ALL ORDERS DETAILS ----
    $sql = "select * from order_details";
    $ordersQryRes = $objManageData->mysqlifetchdata($sql);
    $ordersDetailsArr = array();
    $inf_order_arr = array();
    for ($o = 0; $o < count($ordersQryRes); $o++) {
        $id = $ordersQryRes[$o]['id'];
        $ordersDetailsArr[$id] = $ordersQryRes[$o];
        $o_type = $ordersQryRes[$o]['o_type'];
        preg_match('/Information/', $o_type, $inf_check);
        if (count($inf_check) > 0) {
            $inf_order_arr[] = $id;
        }
    }

    $formId = $_SESSION['form_id'];

//--- FORM ID IF CHART NOTE FINALIZED -----
    if (empty($formId) == true) {
        $formId = $_SESSION['finalize_id'];
    }
    $patient_id = $_SESSION['patient'];
    $logged_provider_id = $_SESSION['authId'];    
    $user_type = (isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"])) ? $_SESSION["user_role"] : $_SESSION["logged_user_type"];

//--- SAVE ORDER SET ----
    if (empty($submit) == false) {

        //--- SAVE ORDER SETS -------	
        $dataArr = array();
        $dataArr['patient_id'] = $patient_id;
        $dataArr['form_id'] = $formId;
        $dataArr['plan_num'] = $plan_num;
        $dataArr['delete_status'] = '0';
        $dataArr['logged_provider_id'] = $logged_provider_id;
        $dataArr['created_date'] = date('Y-m-d H:i:s');

        $data_common_arr = array();
        $data_common_arr["Operater_Id"] = $logged_provider_id;
        $data_common_arr["Operater_Type"] = getOperaterType($logged_provider_id);
        $data_common_arr["IP"] = $ip;
        $data_common_arr["MAC_Address"] = $_REQUEST['macaddrs'];
        $data_common_arr["URL"] = $URL;
        $data_common_arr["Browser_Type"] = $browserName;
        $data_common_arr["OS"] = $os;
        $data_common_arr["Machine_Name"] = $machineName;
        $data_common_arr["Date_Time"] = date('Y-m-d H:i:s');
        $data_common_arr["pid"] = $patient_id;
        for ($i = 0; $i < count($new_order_set); $i++) {
            $orderSetId = $new_order_set[$i];
            $dataArr['order_set_id'] = $orderSetId;
            $dataArr['order_set_options'] = @join('__', $set_options[$orderSetId]);
            $arrAuditTrail = array();
            $data_arr = array();
            $data_arr["Table_Name"] = "order_set_associate_chart_notes";
            $data_arr["Pk_Id"] = $txt_edit_id;
            $data_arr["Category"] = "order sets";
            $data_arr["Category_Desc"] = $orderSetNameArr[$orderSetId];

            $data_arr["Data_Base_Field_Name"] = "order_set_associate_id";
            $data_arr["Filed_Text"] = $orderSetNameArr[$orderSetId] . ' - ' . $patient_id;
            $data_arr["Field_Label"] = "new_orderSetOrders";
            $data_arr["Data_Base_Field_Type"] = "varchar";
            $arrAuditTrail[] = array_merge($data_arr, $data_common_arr);

            $sql = "select order_set_options
				from order_set_associate_chart_notes 
				where order_set_associate_id  = '$txt_edit_id'";
            $orderssetsQryRes = $objManageData->mysqlifetchdata($sql);

            if ($dataArr['order_set_options'] != $orderssetsQryRes[0]['order_set_options']) {
                $data_arr = array();
                $data_arr["Table_Name"] = "order_set_associate_chart_notes";
                $data_arr["Pk_Id"] = $txt_edit_id;
                $data_arr["Category"] = "order sets";
                $data_arr["Category_Desc"] = $orderSetNameArr[$orderSetId];
                $data_arr["Data_Base_Field_Name"] = "order_set_options";
                $data_arr["Filed_Text"] = $orderSetNameArr[$orderSetId] . " - Options" . ' - ' . $patient_id;
                $data_arr["Field_Label"] = "order_set_options";
                $data_arr["Data_Base_Field_Type"] = "varchar";
                $data_arr["Old_Value"] = addcslashes(addslashes(trim(str_replace('__', ', ', $orderssetsQryRes[0]['order_set_options']))), "\0..\37!@\177..\377");
                $data_arr["New_Value"] = addcslashes(addslashes(trim(str_replace('__', ', ', $dataArr['order_set_options']))), "\0..\37!@\177..\377");
                $arrAuditTrail[] = array_merge($data_arr, $data_common_arr);
            }

            if (empty($txt_edit_id) == true) {
                $order_set_primary_id = AddRecords($dataArr, 'order_set_associate_chart_notes');
                if ($arrAuditTrail) {
                    if ($policyStatus == 1) {
                        for ($main_count = 0; $main_count < count($arrAuditTrail); $main_count++) {
                            $arrAuditTrail[$main_count]["Pk_Id"] = $order_set_primary_id;
                            $arrAuditTrail[$main_count]["Action"] = "add";
                            AddRecords($arrAuditTrail[$main_count], 'audit_trail');
                        }
                    }
                }
            } else {
                $order_set_primary_id = UpdateRecords($txt_edit_id, 'order_set_associate_id', $dataArr, 'order_set_associate_chart_notes');
                if ($arrAuditTrail) {
                    if ($policyStatus == 1) {
                        for ($main_count1 = 0; $main_count1 < count($arrAuditTrail); $main_count1++) {
                            $arrAuditTrail[$main_count1]["Action"] = "update";
                            AddRecords($arrAuditTrail[$main_count1], 'audit_trail');
                        }
                    }
                }
            }
            $arrAuditTrail = array();
            //--- NEW ORDER ID -----
            $newOrderArr = $new_orderSetOrders[$orderSetId];


            //--- ALL ORDERS FOR SINGLE ORDER SET ---
            $orders_arr = $ordersArr[$orderSetId];
            $orders_data_arr = array();
            $orders_data_arr['order_set_associate_id'] = $order_set_primary_id;
            $orders_data_arr['created_date'] = date('Y-m-d H:i:s');

            $orders_site_arr = $new_orders_site_id[$orderSetId];
            $new_orders_text_arr = $new_orders_site_txt_id[$orderSetId];
            $orders_day_arr = $new_orders_set_when_day[$orderSetId];
            $orders_when_arr = $new_order_schedule[$orderSetId];
            $orders_priority_arr = $new_orders_priority[$orderSetId];
            $orders_priority_txt_arr = $new_orders_priority_txt[$orderSetId];
            $information_arr = $information_name[$orderSetId];
            $order_reason_arr = $reasonTxtArr[$orderSetId];

            for ($o = 0; $o < count($orders_arr); $o++) {
                $order_id = $orders_arr[$o];
                //--- DELETE ORDER CHECK ---
                if (in_array($order_id, $newOrderArr) === true) {
                    $orders_data_arr['delete_status'] = 0;
                } else {
                    $orders_data_arr['delete_status'] = 1;
                }

                //--- LAB / RADIOLOGY TEST DATA ----
                if ($orders_data_arr['delete_status'] == 0) {
                    $order_lab_name_str = $ordersDetailsArr[$order_id]['order_lab_name'];

                    $sql = "select lab_radiology_tbl_id, lab_type, lab_radiology_name, 
							lab_indication, lab_instructions, lab_radiology_address, lab_radiology_zip,
							lab_radiology_city, lab_radiology_state,lab_contact_name  from lab_radiology_tbl
							where lab_radiology_tbl_id in ($order_lab_name_str) and lab_radiology_status < '2'";
                    $labRadQryRes = $objManageData->mysqlifetchdata($sql);
                    for ($lb = 0; $lb < count($labRadQryRes); $lb++) {
                        $lab_type = strtolower($labRadQryRes[$lb]['lab_type']);
                        $lab_contact_name = $labRadQryRes[$lb]['lab_contact_name'];
                        $rad_address = $labRadQryRes[$lb]['lab_radiology_address'];
                        if (trim($labRadQryRes[$lb]['lab_radiology_city']) != '') {
                            if (trim($rad_address) != '') {
                                $rad_address .= ', ';
                            }
                            $rad_address .= $labRadQryRes[$lb]['lab_radiology_city'];
                        }
                        if (trim($labRadQryRes[$lb]['lab_radiology_state']) != '') {
                            $rad_address .= ', ' . $labRadQryRes[$lb]['lab_radiology_state'];
                        }
                        if (trim($labRadQryRes[$lb]['lab_radiology_zip']) != '') {
                            $rad_address .= ' ' . $labRadQryRes[$lb]['lab_radiology_zip'];
                        }

                        switch ($lab_type) {
                            case 'radiology':
                                $rad_name = $labRadQryRes[$lb]['lab_radiology_name'];
                                $radDataArr = array();
                                $radDataArr['rad_fac_name'] = $lab_contact_name;
                                $radDataArr['rad_name'] = $rad_name;
                                $radDataArr['rad_address'] = $rad_address;
                                $radDataArr['rad_patient_id'] = $patient_id;
                                $radDataArr['rad_indication'] = $labRadQryRes[$lb]['lab_indication'];
                                $radDataArr['rad_instuctions'] = $labRadQryRes[$lb]['lab_instructions'];
                                $radDataArr['rad_order_date'] = date('Y-m-d');
                                $radDataArr['rad_status'] = 1;
                                $radDataArr['rad_order_by'] = $_SESSION['authId'];
                                $radDataArr['rad_performed_date'] = date('Y-m-d');
                                $radDataArr['rad_entered_by'] = $_SESSION['authId'];

                                //--- CHECK ALREADY EXISTS RADIOLOGY RECORD ----
                                $sql = "select rad_test_data_id from rad_test_data
								where rad_name = '$rad_name' and rad_patient_id = '$patient_id' 
								and rad_status < '2' limit 0,1";
                                $radQryRes = $objManageData->mysqlifetchdata($sql);
                                $rad_id = $radQryRes[0]['rad_test_data_id'];
                                if (empty($rad_id) === true) {
                                    AddRecords($radDataArr, 'rad_test_data');
                                } else {
                                    UpdateRecords($rad_id, 'rad_test_data_id', $radDataArr, 'rad_test_data');
                                }
                                break;
                            case 'lab':
                                $lab_name = $labRadQryRes[$lb]['lab_radiology_name'];
                                $labDataArr = array();
                                $labDataArr['lab_name'] = $lab_contact_name;
                                $labDataArr['lab_address'] = $rad_address;
                                $labDataArr['lab_test_name'] = $lab_name;
                                $labDataArr['lab_test_type'] = 'Lab';
                                $labDataArr['lab_patient_id'] = $patient_id;
                                $labDataArr['lab_comments'] = $labRadQryRes[$lb]['lab_instructions'];
                                $labDataArr['lab_order_date'] = date('Y-m-d');
                                $labDataArr['lab_status'] = '1';
                                $labDataArr['lab_test_order_by'] = $_SESSION['authId'];
                                $labDataArr['lab_test_performed_date'] = date('Y-m-d');
                                $labDataArr['lab_entered_by'] = $_SESSION['authId'];

                                //--- CHECK ALREADY EXISTS LAB RECORD ----
                                $sql = "select lab_test_data_id from lab_test_data
								where lab_test_name = '$lab_name' and lab_patient_id = '$patient_id' 
								and lab_status <= '4' limit 0,1";
                                $radQryRes = $objManageData->mysqlifetchdata($sql);
                                $lab_id = $radQryRes[0]['lab_test_data_id'];
                                if (empty($lab_id) === true) {
                                    AddRecords($labDataArr, 'lab_test_data');
                                } else {
                                    UpdateRecords($lab_id, 'lab_test_data_id', $labDataArr, 'lab_test_data');
                                }
                                break;
                        }
                    }
                }

                $orders_data_arr['order_id'] = $order_id;
                $site_data = $orders_site_arr[$o];
                $site_field = "new_orders_site_id";
                if (trim($new_orders_text_arr[$o]) != '') {
                    $site_data = $new_orders_text_arr[$o];
                    $site_field = "new_orders_site_txt_id";
                }
                if (trim($orders_when_arr[$o]) == '' or in_array($orders_when_arr[$o], $dayDataArr) === false) {
                    $orders_day_arr[$o] = '';
                }

                $orders_data_arr['orders_site_text'] = $site_data;
                $orders_data_arr['orders_when_text'] = $orders_when_arr[$o];
                $orders_data_arr['orders_when_day_txt'] = $orders_day_arr[$o];
                $priority_field = "new_orders_priority";
                if (trim($orders_priority_txt_arr[$o]) != '') {
                    $orders_priority_arr[$o] = $orders_priority_txt_arr[$o];
                    $priority_field = "new_orders_priority_txt";
                }
                $orders_data_arr['orders_priority_text'] = $orders_priority_arr[$o];
                $orders_data_arr['orders_reason_text'] = $order_reason_arr[$o];
                $orders_data_arr['instruction_information_txt'] = $information_arr[$o];
                $orders_data_arr['orders_options'] = @join('__', $orders_options[$order_id]);
		$orders_data_arr['resp_person'] = $ordersDetailsArr[$order_id]['resp_person'];

                $sql = "select order_id,order_set_associate_details_id,orders_site_text,orders_when_text,
					 orders_priority_text,orders_reason_text,orders_when_day_txt,orders_options	
					from order_set_associate_chart_notes_details
					where order_set_associate_id = '$txt_edit_id' and order_id = '$order_id'";
                $ordersQryRes = $objManageData->mysqlifetchdata($sql);
                $detail_id = $ordersQryRes[0]['order_set_associate_details_id'];

                if ($detail_id > 0) {
                    $action_status = "update";
                } else {
                    $action_status = "add";
                }

                $data_common_arr["Table_Name"] = "order_set_associate_chart_notes_details";
                $data_common_arr["Pk_Id"] = $detail_id;
                $data_common_arr["Category"] = "order sets";
                $data_common_arr["Category_Desc"] = $orderSetNameArr[$orderSetId];

                $arrAuditTrail = array();
                $data_arr = array();
                if ($orders_data_arr['delete_status'] > 0) {
                    $data_arr["Action"] = "delete";
                } else {
                    $data_arr["Action"] = $action_status;
                }
                $data_arr["Data_Base_Field_Name"] = "order_id";
                $data_arr["Filed_Text"] = $ordersDetailsArr[$order_id]['name'] . ' - ' . $patient_id;
                $data_arr["Field_Label"] = "new_orderSetOrders";
                $data_arr["Data_Base_Field_Type"] = "varchar";
                $arrAuditTrail[] = array_merge($data_arr, $data_common_arr);

                if ($orders_data_arr['instruction_information_txt'] == '') {
                    if ($site_data != $ordersQryRes[0]['orders_site_text']) {
                        $data_arr = array();
                        $data_arr["Action"] = $action_status;
                        $data_arr["Data_Base_Field_Name"] = "orders_site_text";
                        $data_arr["Filed_Text"] = $ordersDetailsArr[$order_id]['name'] . " - Site" . ' - ' . $patient_id;
                        $data_arr["Field_Label"] = $site_field . ' - ' . $patient_id;
                        $data_arr["Data_Base_Field_Type"] = "varchar";
                        $data_arr["Old_Value"] = addcslashes(addslashes(trim($ordersQryRes[0]['orders_site_text'])), "\0..\37!@\177..\377");
                        $data_arr["New_Value"] = addcslashes(addslashes(trim($site_data)), "\0..\37!@\177..\377");
                        $arrAuditTrail[] = array_merge($data_arr, $data_common_arr);
                    }
                    if ($orders_data_arr['orders_when_text'] != $ordersQryRes[0]['orders_when_text']) {
                        $data_arr = array();
                        $data_arr["Action"] = $action_status;
                        $data_arr["Data_Base_Field_Name"] = "orders_when_text";
                        $data_arr["Filed_Text"] = $ordersDetailsArr[$order_id]['name'] . " - Schedule" . ' - ' . $patient_id;
                        $data_arr["Field_Label"] = "new_order_schedule";
                        $data_arr["Data_Base_Field_Type"] = "varchar";
                        $data_arr["Old_Value"] = addcslashes(addslashes(trim($ordersQryRes[0]['orders_when_text'])), "\0..\37!@\177..\377");
                        $data_arr["New_Value"] = addcslashes(addslashes(trim($orders_data_arr['orders_when_text'])), "\0..\37!@\177..\377");
                        $arrAuditTrail[] = array_merge($data_arr, $data_common_arr);
                    }
                    if ($orders_data_arr['orders_when_day_txt'] != $ordersQryRes[0]['orders_when_day_txt']) {
                        $data_arr = array();
                        $data_arr["Action"] = $action_status;
                        $data_arr["Data_Base_Field_Name"] = "orders_when_day_txt";
                        $data_arr["Filed_Text"] = $ordersDetailsArr[$order_id]['name'] . " - Days" . ' - ' . $patient_id;
                        $data_arr["Field_Label"] = "new_orders_set_when_day";
                        $data_arr["Data_Base_Field_Type"] = "varchar";
                        $data_arr["Old_Value"] = addcslashes(addslashes(trim($ordersQryRes[0]['orders_when_day_txt'])), "\0..\37!@\177..\377");
                        $data_arr["New_Value"] = addcslashes(addslashes(trim($orders_data_arr['orders_when_day_txt'])), "\0..\37!@\177..\377");
                        $arrAuditTrail[] = array_merge($data_arr, $data_common_arr);
                    }
                    if ($orders_data_arr['orders_priority_text'] != $ordersQryRes[0]['orders_priority_text']) {
                        $data_arr = array();
                        $data_arr["Action"] = $action_status;
                        $data_arr["Data_Base_Field_Name"] = "orders_priority_text";
                        $data_arr["Filed_Text"] = $ordersDetailsArr[$order_id]['name'] . " - Priority" . ' - ' . $patient_id;
                        $data_arr["Field_Label"] = $priority_field;
                        $data_arr["Data_Base_Field_Type"] = "varchar";
                        $data_arr["Old_Value"] = addcslashes(addslashes(trim($ordersQryRes[0]['orders_priority_text'])), "\0..\37!@\177..\377");
                        $data_arr["New_Value"] = addcslashes(addslashes(trim($orders_data_arr['orders_priority_text'])), "\0..\37!@\177..\377");
                        $arrAuditTrail[] = array_merge($data_arr, $data_common_arr);
                    }
                } else if ($orders_data_arr['delete_status'] > 0) {
                    if ($orders_data_arr['orders_reason_text'] != $ordersQryRes[0]['orders_reason_text']) {
                        $data_arr = array();
                        $data_arr["Action"] = $action_status;
                        $data_arr["Data_Base_Field_Name"] = "orders_reason_text";
                        $data_arr["Filed_Text"] = $ordersDetailsArr[$order_id]['name'] . " - Reason" . ' - ' . $patient_id;
                        $data_arr["Field_Label"] = "reasonTxtArr";
                        $data_arr["Data_Base_Field_Type"] = "varchar";
                        $data_arr["Old_Value"] = addcslashes(addslashes(trim($ordersQryRes[0]['orders_reason_text'])), "\0..\37!@\177..\377");
                        $data_arr["New_Value"] = addcslashes(addslashes(trim($orders_data_arr['orders_reason_text'])), "\0..\37!@\177..\377");
                        $arrAuditTrail[] = array_merge($data_arr, $data_common_arr);
                    }
                } else {
                    if ($orders_data_arr['instruction_information_txt'] != $ordersQryRes[0]['instruction_information_txt']) {
                        $data_arr = array();
                        $data_arr["Action"] = $action_status;
                        $data_arr["Data_Base_Field_Name"] = "instruction_information_txt";
                        $data_arr["Filed_Text"] = $ordersDetailsArr[$order_id]['name'] . " - Instruction" . ' - ' . $patient_id;
                        $data_arr["Field_Label"] = "information_name";
                        $data_arr["Data_Base_Field_Type"] = "varchar";
                        $data_arr["Old_Value"] = addcslashes(addslashes(trim($ordersQryRes[0]['instruction_information_txt'])), "\0..\37!@\177..\377");
                        $data_arr["New_Value"] = addcslashes(addslashes(trim($orders_data_arr['instruction_information_txt'])), "\0..\37!@\177..\377");
                        $arrAuditTrail[] = array_merge($data_arr, $data_common_arr);
                    }
                }
                if ($orders_data_arr['orders_options'] != $ordersQryRes[0]['orders_options']) {
                    $data_arr = array();
                    $data_arr["Action"] = $action_status;
                    $data_arr["Data_Base_Field_Name"] = "orders_options";
                    $data_arr["Filed_Text"] = $ordersDetailsArr[$order_id]['name'] . " - Options" . ' - ' . $patient_id;
                    $data_arr["Field_Label"] = "orders_options";
                    $data_arr["Data_Base_Field_Type"] = "varchar";
                    $data_arr["Old_Value"] = addcslashes(addslashes(trim($ordersQryRes[0]['orders_options'])), "\0..\37!@\177..\377");
                    $data_arr["New_Value"] = addcslashes(addslashes(trim($orders_data_arr['orders_options'])), "\0..\37!@\177..\377");
                    $arrAuditTrail[] = array_merge($data_arr, $data_common_arr);
                }

                if (empty($detail_id) == true) {
                    $orders_data_arr['orders_status'] = 0;
                    $orders_data_arr['modified_date'] = '';
                    $orders_data_arr['modified_operator'] = 0;
                    $order_set_primary_id = AddRecords($orders_data_arr, 'order_set_associate_chart_notes_details');
                    if ($policyStatus == 1) {
                        for ($a = 0; $a < count($arrAuditTrail); $a++) {
                            $arrAuditTrail[$a]['Pk_Id'] = $order_set_primary_id;
                            AddRecords($arrAuditTrail[$a], 'audit_trail');
                        }
                    }
                } else {
                    $order_set_primary_id = UpdateRecords($detail_id, 'order_set_associate_details_id', $orders_data_arr, 'order_set_associate_chart_notes_details');
                    if ($policyStatus == 1) {
                        for ($b = 0; $b < count($arrAuditTrail); $b++) {
                            AddRecords($arrAuditTrail[$b], 'audit_trail');
                        }
                    }
                }
            }
        }


        //--- SAVE ALL SINGLE ORDERS WITHOUT ORDER SETS ------
        for ($i = 0; $i < count($new_orders); $i++) {
            $orderIdStr = $new_orders[$i];
            $dataArr['order_set_id'] = 0;
            $dataArr['patient_id'] = $patient_id;
            $dataArr['form_id'] = $formId;
            $dataArr['plan_num'] = $plan_num;
            $dataArr['delete_status'] = '0';
            $dataArr['logged_provider_id'] = $logged_provider_id;
            $dataArr['created_date'] = date('Y-m-d H:i:s');
            if (empty($txt_edit_id) == true) {
                $order_set_primary_id = AddRecords($dataArr, 'order_set_associate_chart_notes');
            } else {
                $order_set_primary_id = UpdateRecords($txt_edit_id, 'order_set_associate_id', $dataArr, 'order_set_associate_chart_notes');
            }

            //--- LAB / RADIOLOGY TEST DATA ----
            if ($dataArr['delete_status'] == 0) {
                $order_lab_name_str = $ordersDetailsArr[$orderIdStr]['order_lab_name'];

                $sql = "select lab_radiology_tbl_id, lab_type, lab_radiology_name, 
						lab_indication, lab_instructions, lab_radiology_address, lab_radiology_zip,
						lab_radiology_city, lab_radiology_state,lab_contact_name  from lab_radiology_tbl
						where lab_radiology_tbl_id in ($order_lab_name_str) and lab_radiology_status < '2'";
                $labRadQryRes = $objManageData->mysqlifetchdata($sql);
                for ($lb = 0; $lb < count($labRadQryRes); $lb++) {
                    $lab_type = strtolower($labRadQryRes[$lb]['lab_type']);
                    $lab_contact_name = $labRadQryRes[$lb]['lab_contact_name'];
                    $rad_address = $labRadQryRes[$lb]['lab_radiology_address'];
                    if (trim($labRadQryRes[$lb]['lab_radiology_city']) != '') {
                        if (trim($rad_address) != '') {
                            $rad_address .= ', ';
                        }
                        $rad_address .= $labRadQryRes[$lb]['lab_radiology_city'];
                    }
                    if (trim($labRadQryRes[$lb]['lab_radiology_state']) != '') {
                        $rad_address .= ', ' . $labRadQryRes[$lb]['lab_radiology_state'];
                    }
                    if (trim($labRadQryRes[$lb]['lab_radiology_zip']) != '') {
                        $rad_address .= ' ' . $labRadQryRes[$lb]['lab_radiology_zip'];
                    }
                    switch ($lab_type) {
                        case 'radiology':
                            $rad_name = $labRadQryRes[$lb]['lab_radiology_name'];
                            $radDataArr = array();
                            $radDataArr['rad_fac_name'] = $lab_contact_name;
                            $radDataArr['rad_name'] = $rad_name;
                            $radDataArr['rad_address'] = $rad_address;
                            $radDataArr['rad_patient_id'] = $patient_id;
                            $radDataArr['rad_indication'] = $labRadQryRes[$lb]['lab_indication'];
                            $radDataArr['rad_instuctions'] = $labRadQryRes[$lb]['lab_instructions'];
                            $radDataArr['rad_order_date'] = date('Y-m-d');
                            $radDataArr['rad_status'] = 1;
                            $radDataArr['rad_order_by'] = $_SESSION['authId'];
                            $radDataArr['rad_performed_date'] = date('Y-m-d');
                            $radDataArr['rad_entered_by'] = $_SESSION['authId'];

                            //--- CHECK ALREADY EXISTS RADIOLOGY RECORD ----
                            $sql = "select rad_test_data_id from rad_test_data
							where rad_name = '$rad_name' and rad_patient_id = '$patient_id' 
							and rad_status < '2' limit 0,1";
                            $radQryRes = $objManageData->mysqlifetchdata($sql);
                            $rad_id = $radQryRes[0]['rad_test_data_id'];
                            if (empty($rad_id) === true) {
                                AddRecords($radDataArr, 'rad_test_data');
                            } else {
                                UpdateRecords($rad_id, 'rad_test_data_id', $radDataArr, 'rad_test_data');
                            }
                            break;
                        case 'lab':
                            $lab_name = $labRadQryRes[$lb]['lab_radiology_name'];
                            $labDataArr = array();
                            $labDataArr['lab_name'] = $lab_contact_name;
                            $labDataArr['lab_address'] = $rad_address;
                            $labDataArr['lab_test_name'] = $lab_name;
                            $labDataArr['lab_test_type'] = 'Lab';
                            $labDataArr['lab_patient_id'] = $patient_id;
                            $labDataArr['lab_comments'] = $labRadQryRes[$lb]['lab_instructions'];
                            $labDataArr['lab_order_date'] = date('Y-m-d');
                            $labDataArr['lab_status'] = '1';
                            $labDataArr['lab_test_order_by'] = $_SESSION['authId'];
                            $labDataArr['lab_test_performed_date'] = date('Y-m-d');
                            $labDataArr['lab_entered_by'] = $_SESSION['authId'];

                            //--- CHECK ALREADY EXISTS LAB RECORD ----
                            $sql = "select lab_test_data_id from lab_test_data
							where lab_test_name = '$lab_name' and lab_patient_id = '$patient_id' 
							and lab_status <= '4' limit 0,1";
                            $radQryRes = $objManageData->mysqlifetchdata($sql);
                            $lab_id = $radQryRes[0]['lab_test_data_id'];
                            if (empty($lab_id) === true) {
                                AddRecords($labDataArr, 'lab_test_data');
                            } else {
                                UpdateRecords($lab_id, 'lab_test_data_id', $labDataArr, 'lab_test_data');
                            }
                            break;
                    }
                }
            }

            //---- SET SITE TEXT ----
            $ordersSiteVal = $newOrdersSiteTxt[$orderIdStr];
            $site_field = "newOrdersSite";
            if (empty($ordersSiteVal) == true) {
                $ordersSiteVal = $newOrdersSite[$orderIdStr];
                $site_field = "newOrdersSiteTxt";
            }
            $order_schedule_txt = $newOrdersWhen[$orderIdStr];
            $when_day_txt = $new_orders_when_day[$orderIdStr];
            if (trim($order_schedule_txt) == '' or in_array($order_schedule_txt, $dayDataArr) === false) {
                $when_day_txt = '';
            }

            //--- GET PRIORITY TEXT -----
            $priority_txt = $newOrdersPriorityText[$orderIdStr];
            $priority_field = "newOrdersPriority";
            if (empty($priority_txt) == true) {
                $priority_field = "newOrdersPriorityText";
                $priority_txt = $newOrdersPriority[$orderIdStr];
            }
            $ordersOptionArr = $orders_option_arr[$orderIdStr];
            $order_information = $_REQUEST['order_information_' . $orderIdStr];
            $order_id = $orders_arr[$o];

            //--- DELETE ORDER CHECK ---
            $orders_data_arr = array();
            $orders_data_arr['order_set_associate_id'] = $order_set_primary_id;
            $orders_data_arr['created_date'] = date('Y-m-d H:i:s');
            $orders_data_arr['delete_status'] = 0;
            $orders_data_arr['order_id'] = $orderIdStr;
            $orders_data_arr['orders_site_text'] = $ordersSiteVal;
            $orders_data_arr['orders_when_text'] = $order_schedule_txt;
            $orders_data_arr['orders_when_day_txt'] = $when_day_txt;
            $orders_data_arr['orders_priority_text'] = $priority_txt;
            $orders_data_arr['orders_reason_text'] = '';
            $orders_data_arr['instruction_information_txt'] = $order_information;
            $orders_data_arr['orders_options'] = @join('__', $ordersOptionArr);
	    $orders_data_arr['resp_person'] = $ordersDetailsArr[$orderIdStr]['resp_person'];

            //--- EXISTS RECORD CHECK ----
            $sql = "select order_id,order_set_associate_details_id,orders_site_text,orders_when_text,
					 orders_priority_text,orders_reason_text,orders_when_day_txt,orders_options	
				 from order_set_associate_chart_notes_details
				where order_set_associate_id = '$order_set_primary_id' and order_id = '$orderIdStr'";
            $ordersQryRes = $objManageData->mysqlifetchdata($sql);
            $detail_id = $ordersQryRes[0]['order_set_associate_details_id'];

            if ($detail_id > 0) {
                $action_status = "update";
            } else {
                $action_status = "add";
            }

            $data_common_arr["Table_Name"] = "order_set_associate_chart_notes_details";
            $data_common_arr["Pk_Id"] = $detail_id;
            $data_common_arr["Category"] = "orders";
            $data_common_arr["Category_Desc"] = $orderSetNameArr[$orderSetId];

            $arrAuditTrail = array();
            $data_arr = array();
            if ($orders_data_arr['delete_status'] > 0) {
                $data_arr["Action"] = "delete";
            } else {
                $data_arr["Action"] = $action_status;
            }
            $data_arr["Data_Base_Field_Name"] = "order_id";
            $data_arr["Filed_Text"] = $ordersDetailsArr[$orderIdStr]['name'] . ' - ' . $patient_id;
            $data_arr["Field_Label"] = "new_orderSetOrders";
            $data_arr["Data_Base_Field_Type"] = "varchar";
            $arrAuditTrail[] = array_merge($data_arr, $data_common_arr);

            if ($orders_data_arr['instruction_information_txt'] == '') {
                if ($site_data != $ordersQryRes[0]['orders_site_text']) {
                    $data_arr = array();
                    $data_arr["Action"] = $action_status;
                    $data_arr["Data_Base_Field_Name"] = "orders_site_text";
                    $data_arr["Filed_Text"] = $ordersDetailsArr[$orderIdStr]['name'] . " - Site" . ' - ' . $patient_id;
                    $data_arr["Field_Label"] = $site_field . ' - ' . $patient_id;
                    $data_arr["Data_Base_Field_Type"] = "varchar";
                    $data_arr["Old_Value"] = addcslashes(addslashes(trim($ordersQryRes[0]['orders_site_text'])), "\0..\37!@\177..\377");
                    $data_arr["New_Value"] = addcslashes(addslashes(trim($ordersSiteVal)), "\0..\37!@\177..\377");
                    $arrAuditTrail[] = array_merge($data_arr, $data_common_arr);
                }
                if ($orders_data_arr['orders_when_text'] != $ordersQryRes[0]['orders_when_text']) {
                    $data_arr = array();
                    $data_arr["Action"] = $action_status;
                    $data_arr["Data_Base_Field_Name"] = "orders_when_text";
                    $data_arr["Filed_Text"] = $ordersDetailsArr[$orderIdStr]['name'] . " - Schedule" . ' - ' . $patient_id;
                    $data_arr["Field_Label"] = "newOrdersWhen";
                    $data_arr["Data_Base_Field_Type"] = "varchar";
                    $data_arr["Old_Value"] = addcslashes(addslashes(trim($ordersQryRes[0]['orders_when_text'])), "\0..\37!@\177..\377");
                    $data_arr["New_Value"] = addcslashes(addslashes(trim($orders_data_arr['orders_when_text'])), "\0..\37!@\177..\377");
                    $arrAuditTrail[] = array_merge($data_arr, $data_common_arr);
                }
                if ($orders_data_arr['orders_when_day_txt'] != $ordersQryRes[0]['orders_when_day_txt']) {
                    $data_arr = array();
                    $data_arr["Action"] = $action_status;
                    $data_arr["Data_Base_Field_Name"] = "orders_when_day_txt";
                    $data_arr["Filed_Text"] = $ordersDetailsArr[$orderIdStr]['name'] . " - Days" . ' - ' . $patient_id;
                    $data_arr["Field_Label"] = "new_orders_set_when_day";
                    $data_arr["Data_Base_Field_Type"] = "varchar";
                    $data_arr["Old_Value"] = addcslashes(addslashes(trim($ordersQryRes[0]['orders_when_day_txt'])), "\0..\37!@\177..\377");
                    $data_arr["New_Value"] = addcslashes(addslashes(trim($orders_data_arr['orders_when_day_txt'])), "\0..\37!@\177..\377");
                    $arrAuditTrail[] = array_merge($data_arr, $data_common_arr);
                }
                if ($orders_data_arr['orders_priority_text'] != $ordersQryRes[0]['orders_priority_text']) {
                    $data_arr = array();
                    $data_arr["Action"] = $action_status;
                    $data_arr["Data_Base_Field_Name"] = "orders_priority_text";
                    $data_arr["Filed_Text"] = $ordersDetailsArr[$orderIdStr]['name'] . " - Priority" . ' - ' . $patient_id;
                    $data_arr["Field_Label"] = $priority_field;
                    $data_arr["Data_Base_Field_Type"] = "varchar";
                    $data_arr["Old_Value"] = addcslashes(addslashes(trim($ordersQryRes[0]['orders_priority_text'])), "\0..\37!@\177..\377");
                    $data_arr["New_Value"] = addcslashes(addslashes(trim($orders_data_arr['orders_priority_text'])), "\0..\37!@\177..\377");
                    $arrAuditTrail[] = array_merge($data_arr, $data_common_arr);
                }
            } else if ($orders_data_arr['delete_status'] > 0) {
                if ($orders_data_arr['orders_reason_text'] != $ordersQryRes[0]['orders_reason_text']) {
                    $data_arr = array();
                    $data_arr["Action"] = $action_status;
                    $data_arr["Data_Base_Field_Name"] = "orders_reason_text";
                    $data_arr["Filed_Text"] = $ordersDetailsArr[$orderIdStr]['name'] . " - Reason" . ' - ' . $patient_id;
                    $data_arr["Field_Label"] = "reasonTxtArr";
                    $data_arr["Data_Base_Field_Type"] = "varchar";
                    $data_arr["Old_Value"] = addcslashes(addslashes(trim($ordersQryRes[0]['orders_reason_text'])), "\0..\37!@\177..\377");
                    $data_arr["New_Value"] = addcslashes(addslashes(trim($orders_data_arr['orders_reason_text'])), "\0..\37!@\177..\377");
                    $arrAuditTrail[] = array_merge($data_arr, $data_common_arr);
                }
            } else {
                if ($orders_data_arr['instruction_information_txt'] != $ordersQryRes[0]['instruction_information_txt']) {
                    $data_arr = array();
                    $data_arr["Action"] = $action_status;
                    $data_arr["Data_Base_Field_Name"] = "instruction_information_txt";
                    $data_arr["Filed_Text"] = $ordersDetailsArr[$orderIdStr]['name'] . " - Instruction" . ' - ' . $patient_id;
                    $data_arr["Field_Label"] = "information_name";
                    $data_arr["Data_Base_Field_Type"] = "varchar";
                    $data_arr["Old_Value"] = addcslashes(addslashes(trim($ordersQryRes[0]['instruction_information_txt'])), "\0..\37!@\177..\377");
                    $data_arr["New_Value"] = addcslashes(addslashes(trim($orders_data_arr['instruction_information_txt'])), "\0..\37!@\177..\377");
                    $arrAuditTrail[] = array_merge($data_arr, $data_common_arr);
                }
            }
            if ($orders_data_arr['orders_options'] != $ordersQryRes[0]['orders_options']) {
                $data_arr = array();
                $data_arr["Action"] = $action_status;
                $data_arr["Data_Base_Field_Name"] = "orders_options";
                $data_arr["Filed_Text"] = $ordersDetailsArr[$orderIdStr]['name'] . " - Options" . ' - ' . $patient_id;
                $data_arr["Field_Label"] = "orders_options";
                $data_arr["Data_Base_Field_Type"] = "varchar";
                $data_arr["Old_Value"] = addcslashes(addslashes(trim($ordersQryRes[0]['orders_options'])), "\0..\37!@\177..\377");
                $data_arr["New_Value"] = addcslashes(addslashes(trim($orders_data_arr['orders_options'])), "\0..\37!@\177..\377");
                $arrAuditTrail[] = array_merge($data_arr, $data_common_arr);
            }

            if (empty($detail_id) == true) {
                $orders_data_arr['orders_status'] = 0;
                $orders_data_arr['modified_date'] = '';
                $orders_data_arr['modified_operator'] = 0;
                $order_set_primary_id = AddRecords($orders_data_arr, 'order_set_associate_chart_notes_details');
                if ($policyStatus == 1) {
                    for ($g = 0; $g < count($arrAuditTrail); $g++) {
                        $arrAuditTrail[$g]['Pk_Id'] = $order_set_primary_id;
                        AddRecords($arrAuditTrail[$g], 'audit_trail');
                    }
                }
            } else {
                $order_set_primary_id = UpdateRecords($detail_id, 'order_set_associate_details_id', $orders_data_arr, 'order_set_associate_chart_notes_details');
                if ($policyStatus == 1) {
                    for ($h = 0; $h < count($arrAuditTrail); $h++) {
                        AddRecords($arrAuditTrail[$h], 'audit_trail');
                    }
                }
            }
        }


        //--- CHANGE ORDERS/ORDER SET STATUS -----
        if (count($change_order_status) > 0) {
            $array_key_val = array_keys($change_order_status);
            for ($i = 0; $i < count($array_key_val); $i++) {
                $id = trim($array_key_val[$i]);
                $ordersetArr = $change_order_status[$id];
                $val = join(',', $ordersetArr);
                $dataArr = array();
                $dataArr['orders_status'] = $val;
                $dataArr['modified_date'] = date('Y-m-d');
                $dataArr['modified_operator'] = $_SESSION['authId'];
                $insertId = UpdateRecords($id, 'order_set_associate_details_id', $dataArr, 'order_set_associate_chart_notes_details');
            }
        }

        $url = "chart_notes_order_set.php?plan_num=$plan_num&audit_view=1&save_btn=done&print_order_btn=$print_order_after_save";
        if (constant("REMOTE_SYNC") == 1 && $zOnParentServer == 1) {

            $zRemoteServerData["header"] = $GLOBALS["remote"]['rootdir'] . "/chart_notes/" . $url;
            $flgStopExec = 1;
        } else {

            header("location: " . $url);
        }

//*/
    }
} //$flgStopExec = 1
//
//---
if (!isset($flgStopExec)) { //$flgStopExec
    $os_saved_data_arr = array();

//--- STATUS ARRAY ------
    $opArr = array('Ordered', 'In Progress', 'Completed');

//--- DEFINE VARIABLES FOR DROP DOWN ---
    $siteDropArr = array('OD', 'OS', 'OU', 'Other');
    $dayArr = range(1, 12);
    $whenDropArr = array('Today', 'Days', 'Weeks', 'Months', 'Years', 'At F/U');
    $priorityDropArr = array('OD', 'OS', 'Other');

//--- GET ALL SAVED DATA ------
    require_once(dirname(__FILE__) . '/show_order_sets.php');

//--- CREATE STRING FOR ORDERS/ORDER SET FOR PLANS -----
    $edit_id = $order_edit_id != '' ? $order_edit_id : $edit_id;

//--- ALL ORDER SETS DATA --- //
    $contentStr = join('!!~~!!', $contentArr);
    
//--- ALL ORDERS DATA ---
    if (trim($contentStr) != '' and count($contentArr1) > 0) {
        $contentStr .= '!!~~!!';
    }

    $contentStr .= join('!!~~!!', $contentArr1);
    
   $contentStr = str_replace(array("\r\n", "\r", "\n"), array("\\r\\n", "\\r", "\\n"), $contentStr);
   $contentStr = str_replace("!!~~!!", "\\r\\n\\r\\n", $contentStr);
	
//replace Today with date
    $todate = date("m-d-Y");
    $contentStr = str_replace("Today", $todate, $contentStr);
    ?>
    <!DOCTYPE>
    <html>
        <head>
            <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.min.css" rel="stylesheet" type="text/css">
            <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap-select.css" rel="stylesheet">
            <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/common.css" rel="stylesheet" type="text/css">
            <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/workview.css" rel="stylesheet" type="text/css">

            <style>
                .strikeStyl {
                    color: #f00;text-decoration: line-through;
                }
                .strikeStyl span {
                    color: #000;
                }
                #module_buttons {
                    border-top: 3px solid #8bc34a;
                    padding-top: 5px;
                    height: 50px;
                    background-color: #fff;
                }
                tr{border: #e3e3e3 2px solid;}
                td{padding:2px; border: #e3e3e3 1px solid;}

                #dvpgalpha label{ display:inline-block; border:1px solid red; width:12.5px; text-align:center; margin-top:5px; cursor:pointer; background:yellow; }
                #dvpgalpha label.selected{ font-weight:bold;background:black;color:yellow;}

                .pagination{ margin:2px!important;}
                .pagination > .active > a, .pagination > .active > a:focus, .pagination > .active > a:hover, .pagination > .active > span, .pagination > .active > span:focus, .pagination > .active > span:hover{ background-color:#5c2a79!important }
                /*.pagination > li:first-child > a, .pagination > li:first-child > span{ border-radius:50px!important; color:#000000 }
                .pagination > li.active:first-child > a, .pagination > li.active:first-child > span{color:#ffffff;} */
                .pagination > li > a, .pagination > li > span{border-radius:50px!important; margin-right:2px; color:#000000;padding:0px 3px;}
            </style>
            <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
            <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script> 
            <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-select.js"></script>
            <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/script_function.js"></script>
            <script type="text/javascript">
                var planNum = '<?php print $plan_num; ?>';
    <?php if (empty($edit_id) == true) { ?>

                    var opener_val = opener.document.getElementById("elem_plan" + planNum).value;
                    var content = '<?php print htmlentities($contentStr); ?>';
                    if (content) {
                        content += ' ;';
                    }
                    var sep = opener_val.split(';');

                    if (sep.length == 1) {
                        content += sep[0];
                    } else {
                        content += sep[sep.length - 1];
                    }
                    var op = window.opener.document.getElementById("elem_plan" + planNum);
                    op.value = content;
                    if (typeof (op.onchange) == "function") {
                        op.onchange();
                    }
                    opener.setTaPlanHgt(planNum);
                    if (typeof (opener.updateOrderDetail) != "undefined") {
                        opener.updateOrderDetail(planNum);
                    }

    <?php } ?>



                function c_delete(o_id, plan_num)
                {
                    window.open("delete_reason.php?o_id=" + o_id + "&p_num=" + plan_num, "myWindow", "status = 1, height = 200, width = 400, resizable = 0")
                }

                function reloadOrderSet() {
                    window.location.href = 'chart_notes_order_set.php?plan_num=' + planNum;
                }

                function print_order_set() {
                    var window_obj = window.open("patient_order_set_docs.php?plan_num=" + planNum, "_docs", "");
                    window_obj.focus();
                }

                function save_print_order() {
                    dgi("print_order_after_save").value = 'yes';
                    dgi("submit").click();
                }

    <?php if (empty($print_order_btn) === false) { ?>
                    print_order_set();
    <?php } ?>

    <?php if (empty($save_btn) === false) { ?>
                    window.close();
    <?php } ?>

                $(document).ready(function () {
                    $("#dvpgalpha li").bind("click", function () {
                        $("#dvpgalpha li.selected").removeClass("selected");
                        $(this).addClass("selected");
                        var x = window.location.href;
                        x = x.replace(/\&strtalpa\=\w/g, "");
                        if (x.indexOf("?") == -1) {
                            x += "?";
                        }
                        x += "&strtalpa=" + $(this).text();
                        window.location.replace("" + x);
                    });


                });


            </script>
        </head>
        <body class="body_c">
            <div class="container-fluid">
                <form name="order_set_frm" action="" method="post" onSaubmit="return check_val();">
                    <input type="hidden" name="txt_edit_id" id="txt_edit_id" value="<?php print $edit_id; ?>">
                    <input type="hidden" name="print_order_after_save" id="print_order_after_save" value="<?php print $print_order_after_save; ?>">
                    <div style="position:absolute; z-index:1000; display:none;" id="show_ins_div" class="text_b_w"></div>
                    <div class="whtbox">
                        <div class="row">
                            <div class="col-sm-5">
                                <div id="dvpgalpha" style="height:80px; overflow:auto;">
                                    <div id="hdr" style="background-color: #1b9e95; padding: 5px; color: #fff;">Select alphabet to load order set/orders starting from it</div>
                                    <div class="clearfix"></div>
                                    <div class="pd5">
                                        <nav aria-label="...">
                                            <ul class="pagination">
                                                <?php
                                                $z_tmpalpa = ((isset($_GET["strtalpa"]) && $_GET["strtalpa"] != "") ? "{$_GET["strtalpa"]}" : "A");

                                                $start = (int) ord('A');
                                                $end = (int) ord('Z');
                                                $html = '';
                                                for ($i = $start; $i <= $end; $i++) {
                                                    $char = chr($i);
                                                    $li_class = ( $char == $z_tmpalpa ) ? 'selected active' : '';
                                                    $onClick = '';
                                                    $html .= '<li class="pointer ' . $li_class . '"><a id="' . $char . '" ' . $onClick . '>' . $char . '</a></li>';
                                                }
                                                echo $html;
                                                ?>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                                <div style="height:<?php print $_SESSION['wn_height'] - 310; ?>px; overflow:auto;">

                                    <?php
                                    require_once(dirname(__FILE__) . '/new_order_set.php');
                                    if (empty($OrderSetData) == false) {
                                        ?>
                                        <div class="row" style="background-color: #1b9e95; margin:0px!important; padding: 5px; color: #fff;">
                                            <div class="col-sm-1 hidden-xs">&nbsp;</div>
                                            <div class="col-sm-11 col-xs-12">Add More Order set</div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-sm-12" style="background-color: #A7C091;"><table width="100%" cellpadding="1" cellspacing="1" border="0"><?php print $OrderSetData; ?></table></div>
                                    <?php } ?>
                                    <div class="clearfix"></div>
                                    <?php
                                    require_once(dirname(__FILE__) . '/new_orders.php');
                                    if (empty($newOrdersData) == false) {
                                        ?>
                                        <div class="row" style="background-color: #1b9e95; margin:0px!important; padding: 5px; color: #fff;">
                                            <div class="col-sm-1 hidden-xs">&nbsp;</div>
                                            <div class="col-sm-11 col-xs-12">Add More Orders</div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-sm-12" style="background-color: #DDE6D7;"><table width="100%" cellpadding="1" cellspacing="1" border="0"><?php print $newOrdersData; ?></table></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <div style="height:<?php print $_SESSION['wn_height'] - 230; ?>px; overflow:auto;">
                                    <div class="row" style="background-color: #1b9e95; margin:0px!important; padding: 5px; color: #fff;">
                                        <div class="col-sm-2 col-xs-2"><strong><?php echo get_date_format(date("Y-m-d")); ?></strong></div>
                                        <div class="col-sm-3 col-xs-3"><strong><?php echo 'Order Set Name'; ?></strong></div>
                                        <div class="col-sm-3 col-xs-3"><strong><?php echo $_SESSION['authProviderName']; ?></strong></div>
                                        <div class="col-sm-4 col-xs-4">
                                            <select name="show_os" class="selectpicker input_text_10" onChange="show_os_status(this);">
                                                <option value="active_show">Active</option>
                                                <option value="all_show">All</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row" style="margin:0px!important;">
                                        <div class="col-sm-12" style="padding-top: 5px;"><?php print $page_data; ?></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 text-center pd5" id="module_buttons">
                            <button type="submit" name="submit" value=" Done " id="submit" class="btn btn-success">Done</button>
                            <button type="button" name="done_print" value=" Done & Print " id="done_print" class="btn btn-success" onClick="save_print_order();">Done & Print</button>
                            <button type="button" name="print_btn" disabled value=" Print " id="print_btn" class="btn btn-success" onClick="print_order_set();">Print</button>
                            <button type="button" name="close" value=" Cancel " id="close" class="btn btn-danger" onClick="javascript:window.close();">Cancel</button>
                        </div>
                    </div>
                </form>

            </div>
            <?php
            if ($policyStatus == 1) {
                //--- AUDIT TRAIL FOR VIEW ONLY ---
                $arrAuditTrailView = array();
                $arrAuditTrailView[0]['Pk_Id'] = '';
                $arrAuditTrailView[0]['Table_Name'] = '';
                $arrAuditTrailView[0]['Action'] = 'view';
                $arrAuditTrailView[0]['Operater_Id'] = $logged_provider_id;
                $arrAuditTrailView[0]['Operater_Type'] = getOperaterType($logged_provider_id);
                $arrAuditTrailView[0]['IP'] = $ip;
                $arrAuditTrailView[0]['MAC_Address'] = $_REQUEST['macaddrs'];
                $arrAuditTrailView[0]['URL'] = $URL;
                $arrAuditTrailView[0]['Browser_Type'] = $browserName;
                $arrAuditTrailView[0]['OS'] = $os;
                $arrAuditTrailView[0]['Machine_Name'] = $machineName;
                $arrAuditTrailView[0]['Category'] = 'order sets';
                $arrAuditTrailView[0]['Filed_Label'] = 'Order Sets - ' . $patient_id;
                $arrAuditTrailView[0]['Category_Desc'] = 'Order Sets Information';
                $arrAuditTrailView[0]['pid'] = $_SESSION['patient'];

                $patientViewed = $_SESSION['Patient_Viewed'];
                if ($patientViewed["PLAN_NUM"] != $plan_num) {
                    auditTrail($arrAuditTrailView, $mergedArray, 0, 0, 0);
                    $_SESSION['Patient_Viewed']["PLAN_NUM"] = $plan_num;
                }
            }
            ?>
            <script type="text/javascript">
    <?php if (empty($orders_rows_set) === false) { ?>
                    $("#print_btn").prop('disabled', false);
    <?php } ?>
            </script>
        </body>
    </html>

    <?php
}//$flgStopExec
?>