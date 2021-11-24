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
require_once(dirname(__FILE__) . '/../../config/globals.php');
require_once($GLOBALS['fileroot'] . '/library/classes/msgConsole.php');
$msgConsoleObj = new msgConsole();
$arr_flag_status = $msgConsoleObj->get_active_flags('', false);
$unread_pt_msg_status = $msgConsoleObj->get_patient_messages_status();
$arr_flag_status = array_merge($arr_flag_status, $unread_pt_msg_status);
$unread_direct_messages = $msgConsoleObj->get_direct_messages_status();
$arr_flag_status = array_merge($arr_flag_status, $unread_direct_messages);
$left_opts_arr = $msgConsoleObj->load_left_bar();
for ($i = 0; $i < count($left_opts_arr); $i++) {
    if ($left_opts_arr[$i]['id'] == 'message_reminders_opt' && $arr_flag_status['unread_messages_status'] > 0) {
        $left_opts_arr[$i]['unread'] = 1;
    } else if ($left_opts_arr[$i]['id'] == 'unfinalized_patients_opt' && $arr_flag_status['unfinalized_patients_status'] > 0) {
        $left_opts_arr[$i]['unread'] = 1;
    } else if ($left_opts_arr[$i]['id'] == 'test_tasks_opt' && ($arr_flag_status['unread_scan_docs_status'] > 0 || $arr_flag_status['tests_flag'] > 0)) {
        $left_opts_arr[$i]['unread'] = 1;
    } else if ($left_opts_arr[$i]['id'] == 'forms_letters_opt' && ($arr_flag_status['un_consent_form_status'] > 0 || $arr_flag_status['un_sx_consent_forms_status'] > 0 || $arr_flag_status['un_op_notes_status'] > 0 || $arr_flag_status['un_consult_letters_status'] > 0)) {
        $left_opts_arr[$i]['unread'] = 1;
    } else if ($left_opts_arr[$i]['id'] == 'patient_messages' && $arr_flag_status["unread_pt_messages"] > 0) {
        $left_opts_arr[$i]['unread'] = 1;
    } else if ($left_opts_arr[$i]['id'] == 'direct_messages' && $arr_flag_status["direct_messages_count"] > 0) {
        $left_opts_arr[$i]['unread'] = 1;
    } else {
        $left_opts_arr[$i]['unread'] = 0;
    }
}

$messages_folder=$msgConsoleObj->fetch_messages_folder();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>:: imwemr :: Usre Console</title>

        <!-- Bootstrap -->

	<!--<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/font-awesome.css" rel="stylesheet" type="text/css">-->
        <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/common.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/core.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/style.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap-select.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/jquery.datetimepicker.min.css" rel="stylesheet">
        <link href="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/physician_console.css" rel="stylesheet" type="text/css">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="<?php echo $GLOBALS['webroot'] ?>/library/js/html5shiv.min.js"></script>
            <script src="<?php echo $GLOBALS['webroot'] ?>/library/js/respond.min.js"></script>
        <![endif]-->

        <script type="text/javascript">
            var URL = '<?php echo $GLOBALS['webroot']; ?>';
            var mapObj = {
                '{{URL}}': URL
            };
        </script>
        <style>
            .continfo{padding-left: 4px;}
        </style>
    </head>
    <body>
        <div class=" container-fluid">
            <div id="div_alert_notifications"><span class="notification_span"></span></div>
            <div class="phybox">
                <div class="phyhead">
                	<a href="javascript:;" onClick="load_link_data('import_ccda_opt')" class="pull-right a_clr1">UPLOAD CCDA</a>
                    <h2>User Console</h2>
                </div>

                <div class="clearfix"></div>

                <div class="ptcontmain">
                    <div class="row">

                        <!--Left Panel-->
                        <div class="col-sm-2 col-md-2 col-lg-1 lftpanel phylft">
                            <ul>
								<?php
                                foreach ($left_opts_arr as $left_option) {
                                    $unread_list_link = 'read';
                                    if ($left_option['unread'] == 1) {
                                        $unread_list_link = 'unread';
                                    }
                                
                                    $strlength = strlen($left_option['view_name']);
                                
                                    $newOptName = preg_replace('/ /', '<br />', $left_option['view_name'], 1);
                                    if ($strlength == strlen($newOptName))
                                        $newOptName = preg_replace('/\//', '/<br>', $newOptName, 1);
                                
                                    echo '<li id="' . $left_option['id'] . '" class="' . $unread_list_link . '" onclick="load_link_data(\'' . $left_option['id'] . '\')">';
                                    echo '<figure><img src="' . $GLOBALS['webroot'] . '/library/images/' . $left_option['id'] . '.png" alt=""/></figure> ' . $newOptName;
                                    echo '</li>';
                                }
                                ?>
                            </ul>
                        </div>

                        <!--Right Panel-->
                        <div class="col-sm-10 col-md-10 col-lg-11 ">
                            <!--section title-->
                            <div class="meastop" id="sectionTitle">
                                <div class="row section_heading" id="message_reminder">
                                    <div class="col-sm-5"><h2 class="pdl_10">Messages/Reminders</h2></div>
                                    <div class="col-sm-7">
                                        <ul>
                                            <li class="newmessag" onclick="do_action('ptcomm', 'msg_my_inbox', this);">
                                                <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/my_inbox.png" alt="" />My Inbox
                                            </li>
                                            <li onclick="do_action('ptcomm', 'msg_future_alerts', this);">
                                                <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/future_alerts.png" alt=""/>Future Alerts
                                            </li>
                                            <li onclick="do_action('ptcomm', 'msg_sent_messages', this);">
                                                <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/sent_message.png" alt=""/>Sent Messages
                                            </li>
                                            <li onclick="do_action('ptcomm', 'msg_deleted_messages', this);">
                                                <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/delete_messages.png" alt=""/>Deleted Messages
                                            </li>
                                            <li id="new_mr_message" class="mrnewmsg" onClick="sendNewMessage();">
                                                <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/newmessageBlack.png" alt=""/>New Message/ Task
                                            </li>
                                            
                                            <style>
                                              .dropdown li{display:block!important;border-bottom:1px solid #d5d5d5!important;}
                                               .consmore{border:0px; background-color:transparent; padding: 8px 10px;}
                                            </style>
                                            
                                            <?php if (empty($messages_folder) == false) { ?>
                                                <li class="dropdown">
                                                    <button type="button" class="consmore dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <span class="caret"></span><br/>More
                                                    </button>
                                                    
                                                    <ul class=" dropdown-menu">
                                                        <?php
                                                        $fcounter = 0;
                                                        foreach ($messages_folder as $key => $folder) {
                                                            $fcounter++;
                                                            $folder_key = str_replace(' ', '_', $folder) . '-' . $key;
                                                            ?>
                                                            <li onclick="do_action('ptcomm', '<?php echo $folder_key; ?>', this);" class="more_folders">
                                                                <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/newmessageBlack.png" alt=""/><?php echo $folder; ?>
                                                            </li> 
                                                        <?php } ?>
                                                    </ul>
                                                </li>
                                                
                                            <?php } ?>
                                           
                                        
                                        </ul>
                                        
                                    </div>
                                </div>
                                
                                <div class="row section_heading" id="phy_con_forms_letters_header">
                                    <div class="col-sm-5"><h2 class="pdl_10">Forms/Letters</h2></div>
                                    <div class="col-sm-7">
                                        <ul>
                                            <li class="newmessag" id="consent_forms" onClick="do_action('forms_letters','consent_forms',this)">
                                                <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/consent_form.png" alt="" />Consent Forms
                                            </li>
                                            <li id="sx_consent_forms" onClick="do_action('forms_letters','sx_consent_forms',this)">
                                                <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/sx_consent.png" alt=""/>Sx Consent Forms
                                            </li>
                                            <li id="op_notes" onClick="do_action('forms_letters','op_notes',this)">
                                                <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/op_notes.png" alt=""/>OP Notes
                                            </li>
                                            <li id="consult_letters" onClick="do_action('forms_letters','consult_letters',this)">
                                                <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/letter.png" alt=""/>Consult Letters
                                            </li>
                                        </ul>
                                    </div>
                                </div>
								<div class="row section_heading" id="import_ccda_header">
                                    <div class="col-sm-12">
                                        <h2 class="pdl_10">Import C-CDA</h2>
                                    </div>
                                </div>
                                <div class="row section_heading" id="phy_con_wnl_charttemplate">
                                    <div class="col-sm-12">
                                        <h2 class="pdl_10">WNL/Chart Template</h2>
                                    </div>
                                </div>
                                <div class="row section_heading" id="phy_con_patient_messages">
                                    <div class="col-sm-4"><h2 class="pdl_10">Patient Messages</h2></div>
                                    <div class="col-sm-8 text-right">
										<?php 
												$fac_query = "SELECT facility_name, pos_facility_id FROM pos_facilityies_tbl";
												$fac_query_res = imw_query($fac_query);
												$fac_id_arr = array();
												$facilityName = "<option value=''>Select Facility</option>";
												while ($fac_res = imw_fetch_assoc($fac_query_res)) {
													$sel='';
													$fac_id = $fac_res['pos_facility_id'];
													$fac_id_arr[$fac_id] = $fac_res['facility_name'];
													if(in_array($fac_id,$facility_name))$sel='SELECTED';
													$facilityName .= '<option value="'.$fac_res['pos_facility_id'].'" '.$sel.'>' . $fac_res['facility_name'] . '</option>';
												}
											?>
                                        <ul>
											<li>
												<select name="facility_name" id="facility_name" class="form-control minimal" onChange="searchFac(this.value);" style="width:180px;">
												<?php echo $facilityName; ?>
												</select>											
											</li>
                                                	
                                            <li>
												<form onSubmit="return searchPtMsgById();" class="form-inline">
                                                    <div class="form-group">
                                                        <label for="">Patient # </label>
                                                        <input type="text" class="form-control minimal" name="search_pt_msg_id" id="search_pt_msg_id" alt="" />
                                                    </div>
                                                    <input type="submit" class="btn btn-success" value="Search">
                                                </form>
                                            </li>
                                            <li id="new_pt_message" class="ptnewmsg" onClick="new_pt_message();">
                                                <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/newmessageBlack.png" alt=""/>New Message
                                            </li>
                                            <li class="newmessag" id="load_pt_msg_inbox" onClick="do_action('load_patient_messages', 'load_pt_msg_inbox', this);">
                                                <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/my_inbox.png" alt=""/>Inbox
                                            </li>
                                            <li id="load_pt_msg_sent" onClick="do_action('load_patient_messages', 'load_pt_msg_sent', this);">
                                                <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/sent_message.png" alt=""/>Sent Messages
                                            </li>
                                            <li id="pt_changes_approval" onClick="do_action('load_patient_messages', 'pt_changes_approval', this);">
                                                <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/future_alerts.png" alt=""/>Notifications
                                            </li>
                                            <li id="pt_print_msg" onClick="print_pt_messages_div('ptmsg_content');">
                                                <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/pres_print.png" alt="" style="height:36px;"/>Print Message
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="row section_heading" id="phy_con_resp_person">
                                    <div class="col-sm-12">
                                        <h2 class="pdl_10">Responsible Person</h2>
                                    </div>
                                </div>

                                <div class="row section_heading" id="phy_con_tasks_header">
                                    <div class="col-sm-6"><h2 class="pdl_10">Tasks/Tests</h2></div>
                                    <div class="col-sm-6 text-right phy_head_right">
                                        <ul>
                                            <li class="newmessag" id="tests" onClick="do_action('tests_tasks', 'tests', this);">
                                                <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/tests.png" alt=""/>Tests
                                            </li>
                                            <li id="scan_upload" onClick="do_action('tests_tasks', 'scan_upload', this);">
                                                <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/scan_upload.png" alt=""/>Scan/Upload
                                            </li>
                                            <li id="acc_notes" onClick="do_action('tests_tasks', 'acc_notes', this);">
                                                <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/op_notes.png" alt=""/>Notes
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <div class="row section_heading" id="phy_con_rule_tasks_header">
                                    <div class="col-sm-12"><h2 class="pdl_10">Tasks</h2></div>
                                </div>

                                <div class="row section_heading" id="phy_con_completed_tasks">
                                    <div class="col-sm-6"><h2 class="pdl_10">Completed Tasks</h2></div>
                                    <div class="col-sm-6 text-right phy_head_right">
                                        <ul>
                                            <li class="comp_tasks_opt" id="comp_tasks" onClick="load_link_data('completed_tasks', 'comp_tasks');">
                                                <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/tasks.png" alt=""/>Tasks
                                            </li>
                                            <li id="comp_msg" onClick="load_link_data('completed_tasks', 'comp_msg');">
                                                <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/newmessageBlack.png" alt=""/>Messages
                                            </li>
                                            <li id="comp_order_set" onClick="load_link_data('completed_tasks', 'comp_order_set');">
                                                <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/order_sets.png" alt=""/>Order Sets
                                            </li>
                                            <li id="comp_orders" onClick="load_link_data('completed_tasks', 'comp_orders');">
                                                <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/orders.png" alt=""/>Orders
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="row section_heading" id="phy_con_direct_messages">
                                    <div class="col-sm-3">
                                        <h2 class="pdl_10">Direct Messages <span id="DM_type">[Inbox]</span></h2>
                                    </div>
                                    <div class="col-sm-9">
                                        <ul>
                                            <?php 
                                                $hideClassDirect = '';
                                                $pt_direct_credentials = $msgConsoleObj->check_pt_direct_credentials($_SESSION['authId']);
                                                if($pt_direct_credentials == 0)
                                                {
                                                    //$hideClassDirect = 'hide';
                                                }
                                            ?>
                                            <li class="btn_li <?php echo $hideClassDirect; ?>">
                                                <button id="new_direct" type="button" class="btn btn-success" onclick="new_direct('<?php echo $_SESSION['patient'] ?>');">New Direct</button>
                                            </li>

                                            <li class="btn_li">
                                                <button id="sync_direct" type="button" class="btn btn-success" onclick="sync_direct();">Receive Direct</button>
                                            </li>

                                            <li onClick="do_action('load_direct_messages', 'direct_msg_inbox');">
                                                <input type="radio" name="direct_msg_opts_nm" id="direct_msg_inbox"  class="hide"/>
                                                <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/my_inbox.png" alt="" />
                                                Inbox
                                            </li>	

                                            <li onClick="do_action('load_direct_messages', 'direct_msg_sent');">
                                                <input type="radio" name="direct_msg_opts_nm" id="direct_msg_sent" class="hide"/>
                                                <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/my_sentbox.png" alt="" />
                                                Sent
                                            </li>		
                                        </ul>
                                    </div>	
                                </div>

                                    <?php
				    $flg_dis_user_ap = 1;	
                                    $res_fellow_sess = trim($_SESSION['res_fellow_sess']);
                                    if (!empty($res_fellow_sess) && isset($res_fellow_sess)){
                                        $int_prov = $res_fellow_sess;
					if($_SESSION['logged_user_type'] == "11"){ $flg_dis_user_ap = 0; }
                                    }else{
                                        $int_prov = $msgConsoleObj->operator_id;
				    }
					
					//--
					$tmp = array(0 => $int_prov);
					//Resident
					if($_SESSION['logged_user_type'] == "11" && !empty($msgConsoleObj->operator_id) && $int_prov != $msgConsoleObj->operator_id ){ $tmp[1]=$msgConsoleObj->operator_id; }
                                        $name_arr = $msgConsoleObj->get_username_by_id($tmp);
					$str_phys_nm="";
					$str_phys_nm.=$name_arr[$int_prov]['full'];
					
					if(!empty($flg_dis_user_ap) && isset($tmp[1]) && !empty($tmp[1]) && !empty($name_arr[$tmp[1]]['full'])){ 
						if(!empty($str_phys_nm)){ $str_phys_nm.="& "; }
						$str_phys_nm.=$name_arr[$tmp[1]]['full'];
					}
					
                                    ?>

                                <div class="row section_heading" id="phy_con_ap_policies_header">
                                    <div class="col-sm-7">
                                        <h2 class="pdl_10">SMART A&P - <?php echo $str_phys_nm; ?></h2>
                                    </div>
                                    <div class="col-sm-5 text-right pt10 pdb5 pdr_10" id="page_buttons">
										<?php if(!empty($flg_dis_user_ap)) { ?>
										<button name="new_anp" id="new_anp" class="btn btn-success" onClick="add_edit_anp();">New SMART</button>
										<?php } ?>
										<a href="console_pdf.php?task=ap_policies_phy" class="pdr_10 pdl_10" target="_blank" id="spanPhyPrint" title="Complete Patient Record" alt="Complete Patient Record">
										<img src="<?php echo $GLOBALS['webroot'];?>/library/images/scan.png" alt=""/></a>
                                    </div>
                                </div>
                                <div class="row section_heading" id="phy_con_order_header">
                                    <div class="col-sm-12">
                                        <h2 class="pdl_10">Orders/Order Set</h2>
                                    </div>
                                </div>
                                
                                <div class="row section_heading" id="phy_con_smart_phrases">
                                    <div class="col-sm-10">
                                        <h2 class="pdl_10">Smart Phrases</h2>
                                    </div>
                                    <div class="col-sm-2" style="padding-top:15px;">
                                        <button id="new_phrase" type="button" class="btn btn-success" onclick="edit_phrase();">New Phrase</button>
                                    </div>
                                </div>
			 
			<div class="row section_heading" id="phy_con_patient_notify">
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-10">
                                                <h2 class="pdl_10">Patient Notify</h2>
                                            </div>
                                            <div class="col-sm-2 text-right">
                                                <select id="patientNotifyFilter" onchange="load_link_data('patient_notify_opt', this.value)" class="form-control minimal" style="margin-top:12px">
                                                    <option value=0>Not Informed</option>
                                                    <option value=1>Informed</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>		
				
                                <div class="row section_heading" id="phy_con_unfinalized_pt_header">
                                    <div class="col-sm-10">
                                        <h2 class="pdl_10">Unfinalized Patients</h2>
                                    </div>
                                    <div class="col-sm-2" style="padding-top:15px;">
                                        <div class="checkbox">
											<input id="show_all_patients" type="checkbox" onChange="load_unfinalized_patient()" <?php if(isset($GLOBALS['show_all_unfinalized_pt']) && !empty($GLOBALS['show_all_unfinalized_pt'])){ echo "checked";  } ?> >
											<label for="show_all_patients">Show All Patients</label>
										</div>
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix"></div>

                            <div class="row" id="console_data_list">


                            </div>


                            <div class="clearfix"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!--HTML Loader-->
        <div id="div_loading_image" class="text-center" style="z-index: 1051;">
            <div class="loading_container">
                <div class="process_loader"></div>
                <div id="div_loading_text" class="text-info"></div>
            </div>
        </div>


        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
        <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script> 
        <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery-migrate-1.2.1.js"></script> 
        <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery-ui.min.1.12.1.js"></script> 
        <!-- Include all compiled plugins (below), or include individual files as needed --> 
        <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script> 
        <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.mCustomScrollbar.concat.min.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.datetimepicker.full.min.js"></script>
        <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-select.js"></script>
        <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-typeahead.js"></script>
		<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/sort_table.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/common.js?<?php echo filemtime('../../library/js/common.js');?>"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/messi/messi.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/console.js?<?php echo filemtime('../../library/js/console.js');?>"></script>

        <script>
            top.JS_WEB_ROOT_PATH = "<?php echo $GLOBALS['webroot'];?>";
			var practice_data_path = '<?php /*echo $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH');*/?>';
			/*
			 *Ajax Default Options - Loader
			 */
			var hideloaderFlag = true;
			$(document).ajaxSend(function () {
				$("#div_loading_image").show();
			});
			$(document).ajaxComplete(function () {
				if (hideloaderFlag)
					$("#div_loading_image").hide();
			});
			<?php if (isset($_GET["cti"]) && !empty($_GET["cti"])) {
				echo " var gcti='" . $_GET["cti"] . "'; ";
			} //global chart template id   ?>

			(function ($) {
				$(window).load(function () {

					//$(".mCustomScrollbar").mCustomScrollbar({
					//	theme:"minimal"
					//});

				});
			})(jQuery);


			$(document).ready(function () {
				onload_fun();
				console_setup();
			});
            
        </script> 
        <script>
            $(function () {
                $('[data-toggle="tooltip"]').tooltip();
            });
        </script> 
        <script src="../../library/js/jquery.okayNav.min.js"></script> 
        <script type="text/javascript">
            var navigation = $('#nav-main').okayNav();
        </script>
    </body>
</html>