<?php
require_once(dirname(__FILE__) . '/../../config/globals.php');
require_once($GLOBALS['fileroot'] . '/library/classes/msgConsole.php');

$forwardType = (isset($_POST['forwardType']) && empty($_POST['forwardType']) == false) ? $_POST['forwardType'] : '';

if(empty($forwardType) == false){
    unset($_POST['forwardType']);
    unset($_POST['sync_type']);
    unset($_POST['reply_of']);
    unset($_POST['filter_type']);

    if(isset($_POST['subject']) && empty($_POST['subject']) == false){
        $_POST['message_subject'] = $_POST['subject'];
        unset($_POST['subject']);
    }
    if(isset($_POST['body']) && empty($_POST['body']) == false){
        $_POST['message_text'] = $_POST['body'];
        unset($_POST['body']);
    }

    $_POST['msg_type'] = 0;
}
extract($_REQUEST);

$msgConsoleObj = new msgConsole();
//require_once(getcwd()."/../common/functions.inc.php");
//require_once("../main/Functions.php");
//$objDataManage = new ManageData;
//$objManageData = new DataManage;
$userId = $_SESSION['authId'];
$arr_users = $msgConsoleObj->get_username_by_id();
if (constant('USERS_TYPE_AHEAD') == 1) {
    $actv_users_arr = array(); //$actv_users_id_arr = array();
    foreach ($arr_users as $user_id => $name_array) {
        $actv_users_arr[$user_id] = addslashes($name_array['full']);
        //	$actv_users_id_arr[]	= $user_id;
    }
    $actv_users_str = "'" . implode("','", $actv_users_arr) . "'";
    //$actv_users_id_str			= "'".implode("','",$actv_users_id_arr)."'"; 
    //--- Get user groups -----
    $qry = imw_query("select id,name from user_groups where status = '1' order by display_order");
    $usr_grp_name = array();
    while ($groupsQryRes = imw_fetch_assoc($qry)) {
        $usr_grp_name[$groupsQryRes['id']] = $groupsQryRes['name'] . ' (Group)';
    }
    $actv_users_str = "'" . implode("','", $usr_grp_name) . "'," . $actv_users_str;

    $tmp = $msgConsoleObj->getProvGroupOpts("", "", 1);
    if (!empty($tmp)) {
        $actv_users_str = $tmp . $actv_users_str;
    }
}
//echo $actv_users_str;exit;
//--- GET RECENT SELECT PATIENT FOR SEARCH --------

$searchOption = getRecentPatient($userId);

if ($txt_sbmt != '' && $_POST['replied_id'] == '') {
    $_POST['message_sender_id'] = $userId;
    $_POST['message_status'] = 0;
    $_POST['message_read_status'] = 0;
    $arrayChkUser = array();
    if (!empty($_POST['delivery_date'])) {
        $_POST['delivery_date'] = getDateFormatDB($_POST['delivery_date']);
    }
    if (empty($message_urgent)) {
        $_POST['message_urgent'] = '';
    }
    if (empty($Pt_Communication)) {
        $_POST['Pt_Communication'] = '';
    }

    if (isset($_POST['sent_to_groups']) && is_array($_POST['sent_to_groups']) && constant('USERS_TYPE_AHEAD') != 1) {
        $arr_sent_to_groups = $_POST['sent_to_groups'];
        foreach ($arr_sent_to_groups as $val) {
            if (is_numeric($val) == true) {
                //	if(!in_array($val,$arrayChkUser)){
                $groupQryRes[] = $val;
                $arrayChkUser[] = $val;
                //	}
            } else {
                //prov-group --
                $ar_val = explode("--", $val);
                if (count($ar_val) > 1) {
                    $ar_phy = $msgConsoleObj->getProvGroupOpts($ar_val[1], $ar_val[0]);
                    if (count($ar_phy) > 0) {
                        foreach ($ar_phy as $k => $v) {
                            $v = trim($v);
                            $groupQryRes[] = $v;
                            $arrayChkUser[] = $v;
                        }
                    }
                }

                ////hard coded-group --
                $query1 = "select users.id as id from users join user_groups on user_groups.id = users.user_group_id 
						where user_groups.name = '$val' and user_groups.status = '1'";
                $result1 = imw_query($query1);
                if ($result1 && imw_num_rows($result1) > 0) {
                    while ($rs1 = imw_fetch_array($result1)) {
                        //	if(!in_array($rs1['id'], $arrayChkUser)){
                        $groupQryRes[] = $rs1['id'];
                        $arrayChkUser[] = $rs1['id'];
                        //	}
                    }
                }
            }
        }
        $arr_diffProviders = array();
        $temp_senttogroups = $_POST['sent_to_groups'];
        for ($i = 0; $i < count($temp_senttogroups); $i++) {
            if (intval($_POST['sent_to_groups'][$i]) > 0) {
                $arr_diffProviders[] = intval($_POST['sent_to_groups'][$i]);
                unset($_POST['sent_to_groups'][$i]);
            }
        }
//		pre($_POST['sent_to_groups']); pre($arr_diffProviders,1);
        $_POST['sent_to_groups'] = implode(', ', $_POST['sent_to_groups']);
        if (count($arr_diffProviders) > 0) {
            $stg_ids = implode("','", $arr_diffProviders);
            $query_usn = "SELECT CONCAT(lname,', ',fname,' ',mname) as stg_name FROM users WHERE id IN('$stg_ids')";
            $result_usn = imw_query($query_usn);
            $str_stgnames = '';
            if ($result_usn && imw_num_rows($result_usn) > 0) {
                while ($rs_usn = imw_fetch_array($result_usn)) {
                    if ($str_stgnames == '') {
                        $str_stgnames .= $rs_usn['stg_name'];
                    } else {
                        $str_stgnames .= '<br>' . $rs_usn['stg_name'];
                    }
                }
            }
            if ($str_stgnames != '') {
                $_POST['sent_to_groups'] .= '<br>' . $str_stgnames;
            }
        }
    } else if (isset($_POST['sent_to_groups']) && constant('USERS_TYPE_AHEAD') == 1) {
        $arr_sent_to_usrs_or_grps = explode(';', $_POST['sent_to_groups']);

        $Arr_sent_to_groups = $Arr_sent_to_users = array();
        foreach ($arr_sent_to_usrs_or_grps as $sent_to_vals) {
            //$actv_users_arr//$usr_grp_name
            if (trim($sent_to_vals) != '') {
                foreach ($actv_users_arr as $uid => $uFullName) {
                    //	var_dump(trim($sent_to_vals)==trim($uFullName));
                    //	echo '|'.trim($sent_to_vals).'=='.$uFullName.'|<br>';
                    if (trim($sent_to_vals) == trim($uFullName)) {
                        $Arr_sent_to_users[] = $uid;
                    }
                }
                //pre($Arr_sent_to_users,1);
                if (strlen($sent_to_vals) > 8) {
                    $substr_part = substr($sent_to_vals, -8);
                    if ($substr_part == ' (Group)') {
                        $Arr_sent_to_groups[] = trim(substr($sent_to_vals, 0, -8));
                    }
                    if (count($Arr_sent_to_groups) > 0) {
                        foreach ($Arr_sent_to_groups as $Ugroup) {
                            $usersIDs = getUserIdsByGroup($Ugroup);
                            if (is_array($usersIDs)) {
                                $Arr_sent_to_users = array_merge($Arr_sent_to_users, $usersIDs);
                            }
                        }
                    }
                }
            }
        }
        $Arr_sent_to_users = array_unique($Arr_sent_to_users);
        foreach ($Arr_sent_to_users as $user_sent_to) {
            $groupQryRes[] = $user_sent_to;
        }
    }

    $text_to_arr = array();

    $qry2 = imw_query("select message_to, msg_icon from user_messages where user_message_id = '$txt_msg_edit_id'");
    $msg = imw_fetch_assoc($qry2);
    if ($msg['msg_icon'] == 0) {
        $mark_forwarded = true;
        $update_fwd_id = $txt_msg_edit_id;
    }
    //save saving date from here instead of database current time stamp
    $_POST['message_send_date'] = date('Y-m-d H:i:s');
    if (count($groupQryRes) > 0) {
        $groupQryRes = array_unique($groupQryRes);
        $arrMsgMaster = array();
        $arrMsgMaster['subject'] = $_POST['message_subject'];
        $arrMsgMaster['msg'] = $_POST['message_text'];
        $arrMsgMaster['sender_id'] = $_SESSION['authId'];
        $arrMsgMaster['sent_date'] = date('Y-m-d H:i:s');
        $master_insert_id = AddRecords($arrMsgMaster, 'user_messages_master');
        $_POST['msg_id'] = $master_insert_id;
        //for($i=0;$i<count($groupQryRes);$i++){
        foreach ($groupQryRes as $i => $send_user_id) {
            $_POST['message_to'] = $send_user_id;
            /*if ($msg['message_to'] != $_POST['message_to']) {
                if ($msg['message_to'] == $_SESSION['authId']) {
                    $txt_msg_edit_id = '';
                }
            }*/
			if ($msg['message_to'] != $_POST['message_to'] || $_POST['message_to'] == $_SESSION['authId']) {
                 $txt_msg_edit_id = '';
            }

            if ($txt_msg_edit_id) {
                $insert_id = UpdateRecords($txt_msg_edit_id, 'user_message_id', $_POST, 'user_messages');
            } else {
                if ($_POST['Pt_Communication'] == "1" && $_POST['patientId'] != '' && $_POST['patientId'] != '0') {
                    $insert_id = AddRecords($_POST, 'user_messages');
                }
                $_POST['Pt_Communication'] = "0";
                $insert_id = AddRecords($_POST, 'user_messages');
                if ($mark_forwarded) {
                    update_original_msg($update_fwd_id, 2);
                    $mark_forwarded = false;
                }
            }
        }
    } else {
        if ($_POST['Pt_Communication'] == "1" && $_POST['patientId'] != '' && $_POST['patientId'] != '0') {
            $insert_id = AddRecords($_POST, 'user_messages');
        }
        $_POST['Pt_Communication'] = "0";
    }
}


if ($_POST['replied_id'] != '') {
    $_POST['message_sender_id'] = $userId;
    $_POST['message_status'] = 0;
    $_POST['message_read_status'] = 0;
    $_POST['message_send_date'] = date('Y-m-d H:i:s');
    if (empty($message_urgent)) {
        $_POST['message_urgent'] = '';
    }
    $_POST['message_subject'] = urldecode($_POST['message_subject']);
    if (stristr($_POST['message_subject'], 'Re:')) {
        $_POST['message_subject'] = trim(substr($_POST['message_subject'], 3));
    }
    $_POST['message_subject'] = core_refine_user_input('Re: ' . $_POST['message_subject']);

    if (!empty($_POST['delivery_date'])) {
        $_POST['delivery_date'] = get_date_format($_POST['delivery_date']);
    }

    if (empty($Pt_Communication)) {
        $_POST['Pt_Communication'] = '';
    }

    $replied_id = $_REQUEST['replied_id'];
    $_REQUEST['replied_id'] = 0; // SET 0 SO THAT COULD NOT ADD TO NEW ADDED RECORD

    if (isset($_REQUEST['sent_to_groups']) && is_array($_REQUEST['sent_to_groups']) && constant('USERS_TYPE_AHEAD') != 1) {
        $arr_sent_to_groups = $_POST['sent_to_groups'];
        foreach ($arr_sent_to_groups as $val) {
            if (is_numeric($val) == true) {
                //	if(!in_array($val,$arrayChkUser)){
                $groupQryRes[] = $val;
                $arrayChkUser[] = $val;
                //	}
            } else {

                //prov-group --
                $ar_val = explode("--", $val);
                if (count($ar_val) > 1) {
                    $ar_phy = $msgConsoleObj->getProvGroupOpts($ar_val[1], $ar_val[0]);
                    if (count($ar_phy) > 0) {
                        foreach ($ar_phy as $k => $v) {
                            $v = trim($v);
                            $groupQryRes[] = $v;
                            $arrayChkUser[] = $v;
                        }
                    }
                }

                ////hard coded-group --
                $query1 = "select users.id as id from users join user_groups on user_groups.id = users.user_group_id 
						where user_groups.name = '$val' and user_groups.status = '1'";
                $result1 = imw_query($query1);
                if ($result1 && imw_num_rows($result1) > 0) {
                    while ($rs1 = imw_fetch_array($result1)) {
                        //	if(!in_array($rs1['id'], $arrayChkUser)){
                        $groupQryRes[] = $rs1['id'];
                        $arrayChkUser[] = $rs1['id'];
                        //	}
                    }
                }
            }
        }
        $arr_diffProviders = array();
        $temp_senttogroups = $_POST['sent_to_groups'];
        for ($i = 0; $i < count($temp_senttogroups); $i++) {
            if (intval($_POST['sent_to_groups'][$i]) > 0) {
                $arr_diffProviders[] = intval($_POST['sent_to_groups'][$i]);
                unset($_POST['sent_to_groups'][$i]);
            }
        }
//		pre($_POST['sent_to_groups']); pre($arr_diffProviders,1);
        $_POST['sent_to_groups'] = implode(', ', $_POST['sent_to_groups']);
        if (count($arr_diffProviders) > 0) {
            $stg_ids = implode("','", $arr_diffProviders);
            $query_usn = "SELECT CONCAT(lname,', ',fname,' ',mname) as stg_name FROM users WHERE id IN('$stg_ids')";
            $result_usn = imw_query($query_usn);
            $str_stgnames = '';
            if ($result_usn && imw_num_rows($result_usn) > 0) {
                while ($rs_usn = imw_fetch_array($result_usn)) {
                    if ($str_stgnames == '') {
                        $str_stgnames .= $rs_usn['stg_name'];
                    } else {
                        $str_stgnames .= '<br>' . $rs_usn['stg_name'];
                    }
                }
            }
            if ($str_stgnames != '') {
                $_POST['sent_to_groups'] .= '<br>' . $str_stgnames;
            }
        }
    } else if (isset($_POST['sent_to_groups']) && constant('USERS_TYPE_AHEAD') == 1) {
        $arr_sent_to_usrs_or_grps = explode(';', $_POST['sent_to_groups']);

        $Arr_sent_to_groups = $Arr_sent_to_users = array();
        foreach ($arr_sent_to_usrs_or_grps as $sent_to_vals) {
            //$actv_users_arr//$usr_grp_name
            if (trim($sent_to_vals) != '') {
                foreach ($actv_users_arr as $uid => $uFullName) {
                    //	var_dump(trim($sent_to_vals)==trim($uFullName));
                    //	echo '|'.trim($sent_to_vals).'=='.$uFullName.'|<br>';
                    if (trim($sent_to_vals) == trim($uFullName)) {
                        $Arr_sent_to_users[] = $uid;
                    }
                }
                //pre($Arr_sent_to_users,1);
                if (strlen($sent_to_vals) > 8) {
                    $substr_part = substr($sent_to_vals, -8);
                    if ($substr_part == ' (Group)') {
                        $Arr_sent_to_groups[] = trim(substr($sent_to_vals, 0, -8));
                    }
                    if (count($Arr_sent_to_groups) > 0) {
                        foreach ($Arr_sent_to_groups as $Ugroup) {
                            $usersIDs = getUserIdsByGroup($Ugroup);
                            if (is_array($usersIDs)) {
                                $Arr_sent_to_users = array_merge($Arr_sent_to_users, $usersIDs);
                            }
                        }
                    }
                }
            }
        }
        $Arr_sent_to_users = array_unique($Arr_sent_to_users);
        foreach ($Arr_sent_to_users as $user_sent_to) {
            $groupQryRes[] = $user_sent_to;
        }
    }

    $text_to_arr = array();
    $msgDone = $mark_replied = false;
    $qry3 = imw_query("select message_sender_id, msg_icon from user_messages where user_message_id = '$replied_id'");
    $msg = imw_fetch_assoc($qry3);
    if ($msg['msg_icon'] == 0) {
        $mark_replied = true;
    }

    if (count($groupQryRes) > 0) {
        $groupQryRes = array_unique($groupQryRes);
        $arrMsgMaster = array();
        $arrMsgMaster['subject'] = $_POST['message_subject'];
        $arrMsgMaster['msg'] = $_POST['message_text'];
        $arrMsgMaster['sender_id'] = $_SESSION['authId'];
        $arrMsgMaster['sent_date'] = date('Y-m-d H:i:s');
        $master_insert_id = AddRecords($arrMsgMaster, 'user_messages_master');
        $_POST['msg_id'] = $master_insert_id;
        //for($i=0;$i<count($groupQryRes);$i++){
        foreach ($groupQryRes as $i => $send_user_id) {
            $_POST['message_to'] = $send_user_id;

            if ($replied_id) {
                $_POST['replied_id'] = $replied_id;
                if ($_POST['Pt_Communication'] == "1" && $_POST['patientId'] != '' && $_POST['patientId'] != '0') {
                    $insert_id = AddRecords($_POST, 'user_messages');
                }
                $_POST['Pt_Communication'] = "0";

                /* --ATTACHING ORIGINAL INITIAL MSG WITH DONE-- */
                $getQ2 = "SELECT message_sender_id,message_to, message_text, message_subject, DATE_FORMAT(message_send_date,'%W, %M %d, %Y %h:%i %p') as msgDate 
				FROM user_messages WHERE message_to='" . $_SESSION['authId'] . "' AND user_message_id = '$replied_id' LIMIT 0,1";
                $resQ2 = imw_query($getQ2);
                $originalText = '';
                if ($resQ2 && imw_num_rows($resQ2) > 0) {
                    $rs2 = imw_fetch_assoc($resQ2);
                    $originalText = str_ireplace("&amp;", "&", $rs2['message_text']);
                    $sentDate = $rs2['msgDate'];
                    $originalSubject = core_extract_user_input($rs2['message_subject']);
                    $ORsenderName = $arr_users[$_SESSION['authId']]['full'];
                    $name_sendTo = $arr_users[$rs2['message_sender_id']]['full'];
                    $originalTextPrefix = '
					
						----ORIGINAL MESSAGE----
						From: ' . $name_sendTo . '
						To: ' . $ORsenderName . '
						Sent: ' . $sentDate . '
						Subject: ' . $originalSubject . '
						
						';
                    $originalText = $originalTextPrefix . $originalText;
                }/* --ORIGINAL MSG ATTACHED-- */
                $message_text1 = $arrMsgMaster['msg'] . $originalText;
                $_POST['message_text'] = $message_text1;
                $insert_id = AddRecords($_POST, 'user_messages');
                if ($mark_replied) {
                    update_original_msg($replied_id, 1);
                    $mark_replied = false;
                }
                if ($insert_id && $msgDone == false && $txt_sendone != '') {
                    complete_msg($replied_id);
                    $msgDone = true;
                }
            }
        }
    } else {
        if ($_POST['Pt_Communication'] == "1" && $_POST['patientId'] != '' && $_POST['patientId'] != '0') {
            $insert_id = AddRecords($_POST, 'user_messages');
        }
        $_POST['Pt_Communication'] = "0";
    }
}

$scriptPtid = 'var patient_id_info = "' . $_SESSION['patient'] . '";';
if ($message_edit_id) {
    $qry5 = imw_query(" select * from user_messages where user_message_id = '$message_edit_id'");
    $qryRes = imw_fetch_assoc($qry5);

    $message_to12 = $qryRes['message_to'];
    if (!isset($_GET['replied_id'])) {
        $message_text12 = $qryRes['message_text'];
    }
    $message_urgent12 = $qryRes['message_urgent'];
    $Pt_Communication12 = $qryRes['Pt_Communication'];
    $message_subject12 = $qryRes['message_subject'];
    $patientId = $qryRes['patientId'];
    $scriptPtid = 'var patient_id_info = "' . $patientId . '";';
    if (trim($patientId) != '') {
        $res = $msgConsoleObj->get_patient_more_info($patientId);
        $patNameArr = array();
        $patNameArr['LAST_NAME'] = $res['lname'];
        $patNameArr['FIRST_NAME'] = $res['fname'];
        $patNameArr['MIDDLE_NAME'] = $res['mname'];
        $patName = changeNameFormat($patNameArr);
    }
}

function update_original_msg($msg_id, $msg_icon_status) {
    $q = "UPDATE user_messages SET msg_icon = " . intval($msg_icon_status) . " 
		  WHERE message_to = '" . $_SESSION['authId'] . "' AND user_message_id='" . $msg_id . "'";
    $r = imw_query($q);
}

function complete_msg($msgId) {
    $query = "UPDATE user_messages SET message_read_status=1, message_status = 1, message_completed_date = CURDATE(), msg_completed_by='" . $_SESSION['authId'] . "' WHERE user_message_id = '" . $msgId . "'";
    imw_query($query);
}

function getUserIdsByGroup($Ugroup) {
    $query1 = "select users.id as id from users join user_groups on user_groups.id = users.user_group_id 
			where user_groups.name = '$Ugroup' and user_groups.status = '1' AND users.delete_status='0'";
    $result1 = imw_query($query1);
    if ($result1 && imw_num_rows($result1) > 0) {
        $users = array();
        while ($rs1 = imw_fetch_array($result1)) {
            $users[] = $rs1['id'];
        }
        return $users;
    } else {
        //check provider groups
        global $msgConsoleObj;
        $tmp = $msgConsoleObj->getProvGroupOpts("", $Ugroup);
        return $tmp;
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Physician Console</title>
        <meta name="viewport" content="width=device-width, maximum-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">


        <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/common.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/physician_console.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap-select.css">
        <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/jquery.datetimepicker.min.css" rel="stylesheet">


<!--<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.min.1.12.4.js"></script>-->
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-ui.min.js"></script>
        <!--<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/bootstrap.min.js"></script>-->
        <!--<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/bootstrap-select.js"></script>-->
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.datetimepicker.full.min.js"></script>

        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.mCustomScrollbar.concat.min.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/console.js"></script>
        <!-------------------------------
        <?php if (constant('USERS_TYPE_AHEAD') == 1) { ?><script type="text/javascript" src="../main/javascript/actb.js"></script>
                        <script type="text/javascript" src="../main/javascript/common.js"></script><?php } ?>
        <script type="text/javascript" src="../../js/jquery.ui.datepicker.js"></script>-->

        <script type="text/javascript">
<?php echo $scriptPtid; ?>
            $(document).ready(function () {
                /*$( ".date-pick" ).datepicker({changeMonth: true,changeYear: true,dateFormat:'mm-dd-yy', yearRange: 'c:c+1'}).change(function(){if($(this).val() != ''){$('#Pt_Communication').attr({checked:false,disabled:'disabled'});}else{$('#Pt_Communication').attr({disabled:false});}});*/

<?php if (constant('USERS_TYPE_AHEAD') == 1) { ?>
                    var arrUserTypeAhead = new Array(<?php echo $actv_users_str; ?>);
                    //	var arrUserIdTypeAhead = new Array(<?php echo $actv_users_id_str; ?>);
                    var SentToArrProv = new actb(document.getElementById('sent_to_groups'), arrUserTypeAhead, "", "");//,document.getElementById('hidd_sent_to_groups'),arrUserIdTypeAhead);
                    SentToArrProv.actb_firstText = false;
                    SentToArrProv.actb_lim = 5;
                    $("#sent_to_groups").change(function () {
                        window.setTimeout(function () {
                            v = $("#sent_to_groups").val();
                            if (v.slice(-2) != '; ' && v.slice(-1) != ';' && v.length > 2) {
                                $("#sent_to_groups").val(v + '; ');
                            }
                        }, 500);
                    });
<?php } else { ?>
                    //$('#sent_to_groups').multiSelect({listHeight:'110'});
<?php } ?>
                if (patient_id_info != '' && parseInt(patient_id_info) > 0) {
                    load_ptcomm_ptinfo(patient_id_info);
                }

            });

            function load_ptcomm_ptinfo(ptid) {
                //return true;
                $.ajax({
                    url: 'ajax_html.php?from=console&task=pt_details_ajax&ptid=' + ptid,
                    type: 'POST',
                    success: function (r)
                    {
                        $('#pat_details_td').html(r);
                    }
                });
            }

            function chkform(elem) {
                    if ($('#sent_to_groups').val() == null) {
                        top.fAlert("Please choose receiver for the message.");
                        return false;
                    }

<?php if (constant('USERS_TYPE_AHEAD') == 1) { ?>
                    if ($('#sent_to_groups').val() == null && document.getElementById('Pt_Communication').checked == false) {
                        top.fAlert("Please enter receiver(s) for the message.");
                        return false;
                    }
<?php } else { ?>
                    if ($('#sent_to_groups').val() == null && document.getElementById('Pt_Communication').checked == false) {
                        top.fAlert("Please choose receiver for the message.");
                        return false;
                    }
<?php } ?>
                else if (document.physician_msg.message_text.value == "") {
                    top.fAlert("Please enter message text.");
                    document.physician_msg.message_text.focus();
                    return false;
                }
                
				if(document.getElementById('Pt_Communication').checked == true && !document.getElementById('patientId').value)
				{
					top.fAlert("Please select patient for \"Patient Verbal Communication\".");
                    document.physician_msg.patientId.focus();
                    return false;
				}
				
                var query_param='&txt_sbmt="Send Message"';
                if(elem.id && elem.title) {
                    query_param='&'+elem.id+'='+elem.value;
                }
                //parent.img_display('block');
                //$('#loader').html('<div class="doing"></div>');
                frm_data = $('#physician_msg').serialize() + query_param;
                //console.log(frm_data);

                $.ajax({
                    type: "POST",
                    url: "send_msg_frm.php",
                    data: frm_data,
                    success: function (r) {
                        do_action('ptcomm', 'msg_my_inbox');
                    }
                });
                //return true;
            }

            function searchPatient2(obj) {
                var patientdetails = obj.value.split(':');
                if (isNaN(patientdetails[0]) == false) {
                    document.getElementById("patientId").value = patientdetails[0];
                    document.getElementById("txt_patient_name").value = patientdetails[1];
                    load_ptcomm_ptinfo(patientdetails[0]);
                }
            }

            var xmlHttp;

            function GetXmlHttpObject()
            {
                var objXMLHttp = null
                if (window.XMLHttpRequest)
                {
                    objXMLHttp = new XMLHttpRequest()
                } else if (window.ActiveXObject)
                {
                    objXMLHttp = new ActiveXObject("Microsoft.XMLHTTP")
                }
                return objXMLHttp;
            }

            function edit_msg(msg_id, view_related) {
                document.getElementById("message_edit_id").value = msg_id;
                document.getElementById("view_related").value = view_related;
                document.physician_msg1.submit();
            }



            function loadReplyBlock(msgId) {
                if (msgId != '') {
                    xmlHttp = GetXmlHttpObject()
                    if (xmlHttp == null)
                    {
                        top.fAlert("Browser does not support HTTP Request");
                        return;
                    }
                    var url = "reply_message.php?msgId=" + msgId;
                    xmlHttp.onreadystatechange = stateChangesReply;
                    xmlHttp.open("GET", url, true);
                    xmlHttp.send(null);
                }
            }

            function stateChangesReply() {
                if (xmlHttp.readyState == 4)
                {
                    var result = xmlHttp.responseText;
                    document.getElementById("userMsgDiv").innerHTML = result;
                    $(".date-pick").datepicker({changeMonth: true, changeYear: true, dateFormat: 'mm-dd-yy', yearRange: 'c:c+1'}).change(function () {
                        if ($(this).val() != '') {
                            $('#Pt_Communication').attr({checked: false, disabled: 'disabled'});
                        } else {
                            $('#Pt_Communication').attr({disabled: false});
                        }
                    });
                    $('#sent_to_groups').multiSelect({listHeight: '110'});
                }
            }

<?php
if ($txt_sbmt != '' || $txt_sendone != '') {
    //*
    if ($update_fwd_id || $replied_id) {
        //echo 'window.parent.load_link_data(\'message_reminders_opt\');';
        echo 'top.window.location.replace("index.php#ptComm_tab1"); 
                            top.window.location.reload(true);
                             ';
    } else {//*/
        echo 'window.location.href="send_msg_frm.php";';
    }
}
?>
        </script>
    </head>
    <body style="background-color: transparent; overflow: hidden;">
        <form method="post" name="physician_msg1" style="margin:0px;">
            <input type="hidden" name="message_status" id="message_status" value="">
            <input type="hidden" name="message_edit_id" id="message_edit_id" value="<?php echo $_POST['message_edit_id']; ?>">
            <input type="hidden" name="view_related" id="view_related" value="0">
        </form>

        <div class="newmesgbox" id="userMsgDiv">
            <form name="physician_msg" id="physician_msg" style="margin:0px;">
                <input type="hidden" name="txt_msg_edit_id" id="txt_msg_edit_id" value="<?php print $message_edit_id; ?>">
                <div class="row phyhead">
                    <!--    	<div class="col-sm-4"><h2>New Message/ Task</h2></div>-->
                    <div class="col-sm-12 text-right form-inline">
                        <div class="checkbox">
                            <input type="checkbox" title="Mark as urgent" name="message_urgent" id="message_urgent" value="1" <?php if ($message_urgent12 == '1') print 'checked'; ?>>
                            <label for="message_urgent">Urgent</label>
                        </div>
                        <?php
                        $policySQL = "SELECT patient_verbal_communication FROM copay_policies WHERE policies_id = 1";
                        $policyRs = imw_query($policySQL);
                        $default_PVC = '';
                        if(imw_num_rows($policyRs) > 0) {
                            $data = imw_fetch_assoc($policyRs);
                            $default_PVC = $data['patient_verbal_communication'];
                        }
                        if (
                            (isset($GLOBALS["DEFAULT_PVC_SET"]) && $GLOBALS["DEFAULT_PVC_SET"] == 1) ||
                            (isset($default_PVC) && $default_PVC == 1)
                        )
                            $checked = "checked";
                        else
                            $checked = '';
                        ?>
                        <div class="checkbox">
                            <input type="checkbox" name="Pt_Communication" id="Pt_Communication" value="1" <?php echo $checked; ?>>
                            <label for="Pt_Communication">Patient Verbal Communication</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="row pt10">
                            <div class="col-sm-3">Send To</div>
                            <div class="col-sm-9"><?php
                                $arrReplyUserId = array();
                                if ($_REQUEST['action'] == "reply") {
                                    $arrReplyUserId[] = $_REQUEST['msg_sender_id'];
                                } else if ($action == "replyAll") {
                                    $qry4 = imw_query("select message_sender_id, msg_id,message_subject,
											DATE_FORMAT(message_send_date,'%Y-%m-%d') AS message_send_date
											from user_messages where user_message_id = '$replied_id'");
                                    $msg = imw_fetch_assoc($qry4);

                                    if ($msg['msg_id'] != "0") {
                                        $qry6 = imw_query("SELECT group_concat(message_to) AS reply_usr_id,message_sender_id 
													FROM user_messages where msg_id = '" . $msg['msg_id'] . "'");
                                        $res = imw_fetch_assoc($qry6);

                                        $reply_usr_id = $res['reply_usr_id'];
                                        $arrTmp = explode(",", $reply_usr_id);

                                        $key = array_search($_SESSION['authId'], $arrTmp);
                                        unset($arrTmp[$key]);

                                        $message_sender_id = $res['message_sender_id'];
                                        $arrSenderId = explode(",", $message_sender_id);

                                        $arrReplyUserId = array_merge($arrTmp, $arrSenderId);
                                    } else {
                                        $qry6 = imw_query("SELECT group_concat(message_to) AS reply_usr_id,message_sender_id 
												FROM user_messages 
												WHERE message_subject = '" . $msg['message_subject'] . "'
												AND DATE_FORMAT(message_send_date,'%Y-%m-%d') = '" . $msg['message_send_date'] . "'
												AND message_sender_id = '" . $_REQUEST['msg_sender_id'] . "'
												GROUP BY user_messages.sent_to_groups, 
												user_messages.message_subject, 
												DATE_FORMAT(user_messages.message_send_date,'%Y-%m-%d')");
                                        $res = imw_fetch_assoc($qry6);
                                        $reply_usr_id = $res['reply_usr_id'];
                                        $arrTmp = explode(",", $reply_usr_id);

                                        $key = array_search($_SESSION['authId'], $arrTmp);
                                        unset($arrTmp[$key]);

                                        $message_sender_id = $res['message_sender_id'];
                                        $arrSenderId = explode(",", $message_sender_id);

                                        $arrReplyUserId = array_merge($arrTmp, $arrSenderId);
                                    }
                                }
                                $message_sender_id = $_REQUEST['msg_sender_id'];
                                $qry = imw_query("select id,lname,fname,mname from users where id > 0 and delete_status = '0' and locked=0
								order by lname,fname");
                                if (constant('USERS_TYPE_AHEAD') == 1) {
                                    $message_sender_name = (trim($actv_users_arr[$message_sender_id]) != '') ? trim($actv_users_arr[$message_sender_id]) . '; ' : '';
                                    ?>
                                    <input type="text" name="sent_to_groups" id="sent_to_groups" class="form-control" value="<?php echo $message_sender_name; ?>">
                                <?php } else { ?>
                                    <select name="sent_to_groups[]" id="sent_to_groups" class="selectpicker minimal selecicon" multiple="multiple" data-done-button="true" data-size="8" data-actions-box="true" data-live-search="true">
                                        <?php
                                        $message_to = $_SESSION['authId'];
                                        $newOption = '';
                                        while ($userQryRes = imw_fetch_assoc($qry)) {
                                            $id = $userQryRes['id'];
                                            //if($message_sender_id==$id)
                                            if (in_array($id, $arrReplyUserId)) {
                                                $sel = "selected='selected'";
                                            } else {
                                                $sel = '';
                                            }
                                            $phyName = $userQryRes['lname'] . ', ';
                                            $phyName .= $userQryRes['fname'] . ' ';
                                            $phyName .= $userQryRes['mname'];
                                            if ($phyName[0] == ',') {
                                                $phyName = preg_replace('/, /', '', $phyName);
                                            }
                                            //	if($id == $_SESSION['authId']){
                                            //		$newOption = "<option value='$id' selected>$phyName</option>";
                                            //	}
                                            $phyName = trim(ucwords($phyName));
                                            //	$sel = $id == $message_to12 ? 'selected' : '';
                                            $phyOption .= "<option " . $sel . "  value='$id'>$phyName</option>";
                                        }
                                        // print $newOption;					
                                        //--- Get user groups -----
                                        $qry = imw_query("select name from user_groups where status = '1' order by display_order");
                                        while ($groupsQryRes = imw_fetch_assoc($qry)) {
                                            $groupsOption .= '<option value="' . $groupsQryRes['name'] . '">' . $groupsQryRes['name'] . '</option>';
                                        }
                                        //--GET Provider Groups -----
                                        $opt_prov_grp = $msgConsoleObj->getProvGroupOpts();

                                        print $opt_prov_grp;
                                        print $groupsOption;
                                        print $phyOption;
                                        ?>
                                    </select>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="row pt10">
                            <div class="col-sm-3">Delivery Date</div>
                            <div class="col-sm-9"><input type="text" name="delivery_date" id="delivery_date" class="date-pick form-control" autocomplete="off"></div>
                        </div>
                        <div class="row pt10">
                            <div class="col-sm-3">Patient</div>
                            <div class="col-sm-9"><?php
                                if ((intval($patientId) == 0 || $patName == '') && intval($_SESSION['patient']) > 0) {
                                    $patientId = intval($_SESSION['patient']);
                                    $ar_ptName = core_get_patient_name($patientId);
                                    $patName = $ar_ptName[2] . ', ' . $ar_ptName[1];
                                }
                                ?>
                                <input type="hidden" name="patientId" id="patientId" value="<?php print $patientId; ?>" />
                                <input type="text" id="txt_patient_name" name="txt_patient_name" onKeyPress="{
                                            if (event.keyCode == 13)
                                                return searchPatient()
                                        }" value="<?php print $patName; ?>" class="form-control" onBlur="chk_patient(this);" />

                            </div>
                        </div>
                        <div class="row pt10">
                            <div class="col-sm-3">&nbsp;</div>
                            <div class="col-sm-6">
                                <select name="txt_findBy" id="txt_findBy" onChange="searchPatient2(this)" onkeypress="{
                                            if (event.keyCode == 13)
                                                return searchPatient()
                                        }" class="form-control minimal">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                    <option value="Deceased">Deceased</option> 
                                    <option value="Resp.LN">Resp.LN</option> 
                                    <option value="Ins.Policy">Ins.Policy</option>
                                    <?php print $searchOption; ?>	    
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <a href="javascript:void(0)" class=" btn btn-success" onClick="searchPatient()">Search Pt.</a>
                                <!--<a href="javascript:void(0);"  onKeyPress="{if (event.keyCode==13)return searchPatient()}" class="text_10b_purpule"></a>--></div>
                        </div>
                        <div class="row pt10">
                            <div class="col-sm-3">Subject</div>
                            <div class="col-sm-6">
                                <?php
                                if ($_REQUEST['replied_id'] != "") {
                                    if (stristr($_REQUEST['subject'], 'Re:')) {
                                        $_REQUEST['subject'] = trim(substr($_REQUEST['subject'], 3));
                                    }
                                    $subject = 'Re: ' . core_extract_user_input($_REQUEST['subject']);
                                }
                                ?>	
                                <input type="text" name="message_subject" id="message_subject" value="<?php
                                if (trim($subject) != "") {
                                    echo $subject;
                                } else {
                                    echo $message_subject12;
                                }
                                ?>" class="form-control">
                            </div>
                            <div class="col-sm-3">
                                <select id="msg_type" name="msg_type" class="form-control minimal">
                                    <option value="0" <?php if(isset($qryRes['msg_type']) && $qryRes['msg_type'] == 0) echo 'selected'; ?> >Message</option>
                                    <option value="1" <?php if(isset($qryRes['msg_type']) && $qryRes['msg_type'] == 1) echo 'selected'; ?> >Task</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div id="divPtDemo"><div id="pat_details_td" style="margin-top: 5px;"></div></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row pt10">
                            <div class="col-sm-1">Message</div>
                            <div class="col-sm-11"><textarea name="message_text" id="message_text" rows="6" cols="158" class="form-control"><?php echo $message_text12; ?></textarea></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 text-right mrbutton pt10">
                                <input type="hidden" name="replied_id" value="<?php echo $_REQUEST['replied_id']; ?>" />
                                <button type="submit" title="Send Message" onClick="return chkform(this);" class="btn btn-primary" id="txt_sbmt" name="txt_sbmt" value="Send Message">Send Message</button>

                                <?php if (isset($_GET['replied_id'])) { ?>
                                    <button type="submit" title="Send &amp; Completed" onClick="return chkform(this);" class="btn btn-info" id="txt_sendone" name="txt_sendone" value="Send_Completed">Send &amp; Completed</button>
                                <?php } ?>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
            <br />
        </div>
        <script>
            $(document).ready(function () {
                //$('#sent_to_groups').selectpicker('refresh');

                // var date_global_format = 'm-d-Y';
                $('#delivery_date').datetimepicker({
                    timepicker: false,
                    format: window.opener.global_date_format,
                    formatDate: 'Y-m-d',
                    scrollInput: false
                });/*.change(function(){ 
                 var dt_val = $("#delivery_date").val();
                 dt_val = window.opener.top.getDateFormat(dt_val,'mm-dd-yyyy');
                 //toggle_sch_type('week', dt_val);
                 })*/


                /*$('#facilities').on('hide.bs.select', function() {
                 fac_change_load('week'); // or $(this).val()
                 });*/

            });

        </script>
    </body>
</html>