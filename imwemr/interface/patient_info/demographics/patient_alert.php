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

  File: patient_alert.php
  Purpose: Get patient alerts
  Access Type: Include
 */
include_once("../../../config/globals.php");

//CHECK PATIENT SESSION AND CLOSING POPUP IF NO PATIENT IN SESSION
$window_popup_mode = true;
require_once("../../../library/patient_must_loaded.php");


$currentUser = $_SESSION['authId'];
$global_date_format = 'm-d-Y'; // Temporary Date Format
$operatorInitial = '';
$operatorInitialQry = "select CONCAT_WS('',substr(fname,1,1),substr(lname,1,1)) as operatorInitial from users where id = '$currentUser' and id!=''";
$operatorInitialRes = imw_query($operatorInitialQry) or die(imw_error());
if (count($operatorInitialRes) > 0) {
    $operatorInitialRow = imw_fetch_array($operatorInitialRes);
    $operatorInitial = $operatorInitialRow['operatorInitial'];
}

$delAlert = "";
if ($_REQUEST['deleteAlertId']) {
	
	//---
	$dateTimeWtOpInt_del = get_date_format(date("Y-m-d")).' '.date("h:i A").' - '.$operatorInitial;
	//---
	
    $qryDeletePatientAlertSelected = "update alert_tbl set is_deleted = '1', alert_disable_date_time_initial = '".$dateTimeWtOpInt_del."' WHERE alertId = '" . $_REQUEST['deleteAlertId'] . "'";
    $rsDeletePatientAlertSelected = imw_query($qryDeletePatientAlertSelected);
    if ($rsDeletePatientAlertSelected) {
        $delAlert = "top.fAlert('Alert has been deleted successfully.');";
    }
}

$alertSave = "";
if ($_REQUEST['txtPatientAlert']) {
    $arrAlertToShowUnder = array();
    $strAlertToShowUnder = "";
    if (!$_REQUEST['medical']) {
        $arrAlertToShowUnder[] = 0;
    } elseif ($_REQUEST['medical']) {
        $arrAlertToShowUnder[] = 1;
    }

    if (!$_REQUEST['frontDesk']) {
        $arrAlertToShowUnder[] = 0;
    } elseif ($_REQUEST['frontDesk']) {
        $arrAlertToShowUnder[] = 1;
    }
    if (!$_REQUEST['accounting']) {
        $arrAlertToShowUnder[] = 0;
    } elseif ($_REQUEST['accounting']) {
        $arrAlertToShowUnder[] = 1;
    }
    if (!$_REQUEST['sx']) {
        $arrAlertToShowUnder[] = 0;
    } elseif ($_REQUEST['sx']) {
        $arrAlertToShowUnder[] = 1;
    }



    $alert_disp_date = getDateFormatDB($_REQUEST["msg_deli_dt"]);
    //$alert_disp_date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $alert_disp_date)));

    $strAlertToShowUnder = implode(",", $arrAlertToShowUnder);
    //Single Quote fix
    $_REQUEST['txtPatientAlert'] = imw_real_escape_string($_REQUEST['txtPatientAlert']);

    if ($_REQUEST['editAlertId'] == "") {
        $qrySavePatientAlert = "insert into alert_tbl (alertContent,patient_id,operatorId,saveDateTime,alert_to_show_under,alert_showed,alert_created_console, alert_disp_date) VALUES ('" . $_REQUEST['txtPatientAlert'] . "','" . $_SESSION['patient'] . "','$currentUser','" . date('Y-m-d H:i:s') . "','$strAlertToShowUnder','0,0,0','1','" . $alert_disp_date . "')";
    } else {
        $qrySavePatientAlert = "update alert_tbl set alertContent = '" . $_REQUEST['txtPatientAlert'] . "',saveDateTime = '" . date('Y-m-d H:i:s') . "',operatorId = '$currentUser',alert_to_show_under = '$strAlertToShowUnder', alert_disp_date='" . $alert_disp_date . "' WHERE alertId = '" . $_REQUEST['editAlertId'] . "'";
    }

    $rsSavePatientAlert = imw_query($qrySavePatientAlert);
    if ($rsSavePatientAlert && $_REQUEST['editAlertId'] == "") {
				$msg = "New Alert has been saved successfully.";
   	} else if ($rsSavePatientAlert && $_REQUEST['editAlertId']) {
    		$msg = "Alert has been updated successfully.";
    }
		$alertSave = "top.fAlert('".$msg."');";
		echo "<script>
			if (typeof (window.opener.top.update_iconbar) == 'function') {
      	window.opener.top.update_iconbar();
			}
			if( opener ) {
				if( opener.top.alert_notification_show && typeof(opener.top.alert_notification_show) == 'function' )
					opener.top.alert_notification_show('".$msg."');
			}
			window.close();
		</script>";
		sleep(1);
		
}
if ($_SESSION['patient']) {
    $qryGetPatientName = "select concat(lname,', ',fname) as name , mname from patient_data	where id = '" . $_SESSION['patient'] . "'";
    $rsGetPatientName = imw_query($qryGetPatientName);

    if (count($rsGetPatientName) > 0) {
        extract(imw_fetch_array($rsGetPatientName));
        $patientName = $name . ' ' . $mname . ' - ' . $_SESSION['patient'];

        if ($_REQUEST['comboAlerts'] == "all") {
            $qryGetPatientAlert = "SELECT alertContent as patientAlertMsg,at.saveDateTime,alertId,DATE_FORMAT(at.saveDateTime, '%m/%d/%y') as savedDateAlert,
									TIME_FORMAT(at.saveDateTime,'%h:%i %p') as savedTimeAlert ,at.alert_to_show_under as alertShowUnder,
									at.alert_disable_date_time_initial as alDisDtTiInt,at.is_deleted, 
									CONCAT_WS('',substr(u.fname,1,1),substr(u.lname,1,1)) as operatorInitial, alert_disp_date
									from alert_tbl at
									inner join users u on u.id = at.operatorId 
									WHERE at.patient_id = '" . $_SESSION['patient'] . "'									
									order by at.saveDateTime desc
									";
        } else {
            $qryGetPatientAlert = "SELECT alertContent as patientAlertMsg,at.saveDateTime,alertId,DATE_FORMAT(at.saveDateTime, '%m/%d/%y') as savedDateAlert,
									TIME_FORMAT(at.saveDateTime,'%h:%i %p') as savedTimeAlert ,at.alert_to_show_under as alertShowUnder,
									at.alert_disable_date_time_initial as alDisDtTiInt,
									CONCAT_WS('',substr(u.fname,1,1),substr(u.lname,1,1)) as operatorInitial, alert_disp_date
									from alert_tbl at
									inner join users u on u.id = at.operatorId 
									WHERE at.patient_id = '" . $_SESSION['patient'] . "'
									and at.alert_to_show_under !=  at.alert_showed
									and at.is_deleted  = '0'
									order by at.saveDateTime desc
									";
        }
        $rsGetPatientAlert = imw_query($qryGetPatientAlert);
        $patientAlertData = "";
        $patientAlertData = '<table class="table table-bordered table-hover table-striped">
								<thead>
									<tr class="grythead">
										<td class="text-center col-xs-2">Set</td>
										<td class="col-xs-1">Disabled</td>	
										<td class="col-xs-4">Alert Content</td>
										<td class="col-xs-2">Display Date</td>
										<td class="col-xs-2">Options</td>
										<td class="col-xs-1">Action</td>
									</tr>
								<thead><tbody>';
        if (count($rsGetPatientAlert) > 0) {
            $srCounter = 1;
            while ($row = imw_fetch_array($rsGetPatientAlert)) {
                $alertDateTimeInitial = "";
                $alertFor = "";
                $arrAlertFor = array();
                $arrAlDisDtTiInt = array();
                $alertDateTimeInitial = $row['savedDateAlert'] . ' ' . $row['savedTimeAlert'] . ' - ' . $row['operatorInitial'];
                $alertFor = $row['alertShowUnder'];
                $arrAlDisDtTiInt = explode(",", $row['alDisDtTiInt']);
                $alert_disp_date = $row['alert_disp_date'];
                if ($alert_disp_date != "" && strpos($alert_disp_date, "0000") === false) {
                    $alert_disp_date = date('' . $global_date_format . '', strtotime(str_replace('-', '/', $alert_disp_date)));
                } else {
                    $alert_disp_date = "";
                }

                if ($alertFor) {
                    $arrAlertFor = explode(",", $alertFor);
                }
                if (count($arrAlertFor) > 0) {
                    $alertFor = "";
                    if ($arrAlertFor[0] == 1) {
                        $alertFor = "MD";
                    }
                    if ($arrAlertFor[1] == 1) {
                        $alertFor .= " FD";
                    }
                    if ($arrAlertFor[2] == 1) {
                        $alertFor .= " AC";
                    }
                    if ($arrAlertFor[3] == 1) {
                        $alertFor .= " Sx";
                    }
                }

                $onClick = 'onclick="edit(\'' . $row['alertId'] . '\')"';
                $pointer = 'pointer';
                $del_text = '';
                $action = '<span class="glyphicon glyphicon-pencil" title="Edit" ' . $onClick . '></span>
													 <span class="glyphicon glyphicon-remove pointer" onclick="del(\'' . $row['alertId'] . '\')"></span>';

                if ($row['is_deleted'] == 1) {
                    $onClick = '';
                    $pointer = '';
                    $del_text = 'del_text';
                    $action = '';
                }


                $patientAlertData .= '<tr>';
                $patientAlertData .= '<td class="text-center ' . $pointer . '" ' . $onClick . '>' . $alertDateTimeInitial . '</td>';
                $patientAlertData .= '<td class="' . $pointer . '" ' . $onClick . '>';
                $patientAlertData .= $arrAlDisDtTiInt[0] . '<br>';
                $patientAlertData .= $arrAlDisDtTiInt[1] . '<br>';
                $patientAlertData .= $arrAlDisDtTiInt[2];
                $patientAlertData .= '</td>';

                $patientAlertData .= '<td class="' . $pointer . ' ' . $del_text . '" ' . $onClick . '>' . htmlentities($row['patientAlertMsg']) . '</td>';
                $patientAlertData .= '<td class="' . $pointer . ' ' . $del_text . '" ' . $onClick . '>' . $alert_disp_date . '</td>';
                $patientAlertData .= '<td class="' . $pointer . '" ' . $onClick . '>' . $alertFor . '</td>';

                $patientAlertData .= '<td>' . $action . '</td>';


                $srCounter++;
            }
        } else {
            $patientAlertData .= '<tr><td colspan="5" class="bg-info text-center">No Alert Exits</td></tr>';
        }
        $patientAlertData .= "</tbody></table>";
    }

    $msg_deli_dt = date("" . $global_date_format . "");
    if ($_REQUEST['editAlertId']) {
        $qryGetPatientAlertSelected = "select at.alertContent as patientAlertMsgSelected,
																		DATE_FORMAT(at.saveDateTime, '%m/%d/%y') as savedDateAlert,
																		TIME_FORMAT(at.saveDateTime,'%h:%i %p') as savedTimeAlert,
																		at.alert_to_show_under as alertShowUnder,
																		CONCAT_WS('',substr(u.fname,1,1),substr(u.lname,1,1)) as operatorInitial,
																		alert_disp_date 
																		from alert_tbl at
																		inner join users u on u.id = at.operatorId
																		WHERE alertId = '" . $_REQUEST['editAlertId'] . "'";
        $rsGetPatientAlertSelected = imw_query($qryGetPatientAlertSelected);
        if (count($rsGetPatientAlertSelected) > 0) {
            extract(imw_fetch_array($rsGetPatientAlertSelected));
            if ($alertShowUnder) {
                $arrAlertToShowUnder = array();
                $arrAlertToShowUnder = explode(",", $alertShowUnder);
            }

            $msg_deli_dt = $alert_disp_date;
            if ($msg_deli_dt != "" && strpos($msg_deli_dt, "0000") === false) {
                $msg_deli_dt = date($global_date_format, strtotime(str_replace('-', '/', $msg_deli_dt)));
            }
        }
    }

    if (count($arrAlertToShowUnder) <= 0) {
        $arrAlertToShowUnder[0] = "1";
        $arrAlertToShowUnder[1] = "1";
        $arrAlertToShowUnder[2] = "1";
        $arrAlertToShowUnder[3] = "1";
        $arrAlertToShowUnder[4] = "1";
    }
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <title>Patient Alert</title>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
            <!--<!link rel="stylesheet" href="<?php echo $css_header; ?>" type="text/css">	
            <!link rel=stylesheet href="<?php echo $css_patient; ?>" type="text/css">-->
            <link rel="stylesheet" href="<?php echo $GLOBALS['webroot']; ?>/library/css/jquery-ui.min.css" type="text/css">
            <link rel="stylesheet" href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.css" type="text/css">
            <!-- Bootstrap Selctpicker CSS -->
            <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap-select.css" rel="stylesheet" type="text/css">
            <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/common.css" rel="stylesheet" type="text/css">
            <!-- DateTime Picker CSS -->
            <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/jquery.datetimepicker.min.css" rel="stylesheet" type="text/css" />
            <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
            <!-- jQuery's Date Time Picker -->
            <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.datetimepicker.full.min.js"></script>
            <!-- Bootstrap -->
            <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.js"></script>
            <!-- Bootstrap Selectpicker -->
            <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-select.js"></script>
            <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery-ui.min.1.11.2.js"></script>
            <!-- Include all compiled plugins (below), or include individual files as needed -->
            <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script>
            <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/sc_script.js"></script>
            <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/common.js"></script>
            <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.js"></script>
            <link rel="stylesheet" href="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.css">
            <script language="javascript">
                date_global_format = "mm-dd-yy";
                global_date_format = "m-d-Y";
                $(document).ready(function () {
    <?php echo $delAlert . $alertSave; ?>
                    $("#msg_deli_dt").datetimepicker({timepicker: false, format: window.opener.top.jquery_date_format, maxDate: new Date(), autoclose: true, scrollInput: false});
                    //$( "#msg_deli_dt" ).datepicker( "option", "dateFormat", date_global_format ).val("<?php echo $msg_deli_dt; ?>");
                    $('.selectpicker').selectpicker();

                });
                function edit(id)
                {
                    //document.getElementById('loading_img_pt_alert').style.display = 'block';
                    document.getElementById('editAlertId').value = id;
                    document.getElementById('txtPatientAlert').value = "";
                    document.frmPtAlert.submit();
                }
                function del(id, cnfrm)
                {
                    if (typeof (cnfrm) == "undefined") {
                        top.fancyConfirm("Are you sure want to delete this Alert?", '', 'del("' + id + '",true)');
                        return;
                    } else {
                        //document.getElementById('loading_img_pt_alert').style.display = 'block';
                        document.getElementById('deleteAlertId').value = id;
                        document.getElementById('txtPatientAlert').value = "";
                        document.getElementById('editAlertId').value = "";
                        document.frmPtAlert.submit();
                    }
                }
                function saveForm() {
                    if (document.getElementById('medical').checked == false && document.getElementById('frontDesk').checked == false && document.getElementById('accounting').checked == false && document.getElementById('sx').checked == false) {
                        top.fAlert('Please select Option(s) for this alert.')
                        return false;
                    }
                    if (document.getElementById('txtPatientAlert').value != "") {
						document.getElementById('save').disabled = true;
                        document.frmPtAlert.submit();
                    } else {
                        top.fAlert('Please enter alert message.')
                    }
                }
                function newForm() {
                    //document.getElementById("tdLabel").innerHTML = "<b>Add Patient Alert Information:</b>";
                    document.getElementById('txtPatientAlert').value = "";
                    document.getElementById('editAlertId').value = "";

                    var dt = window.opener.top.current_date(window.opener.top.jquery_date_format, '4', true);
                    var opInt = document.getElementById('operatorInitial').value;
                    if( document.getElementById('tdDate') )
                        document.getElementById('tdDate').innerHTML = dt + " - " + opInt;
                    
                    var control;
                    for (var i = 0; i < document.frmPtAlert.elements.length; i++) {
                        control = document.frmPtAlert.elements[i];
                        switch (control.type) {
                            case 'text':
                                control.value = "";
                                break;
                            case 'textarea':
                                control.value = "";
                                break;
                            case 'checkbox':
                                control.checked = true;
                                break;
                        }
                    }
                    $("#new").addClass('hidden');
                    $('#msg_deli_dt').val(window.opener.top.current_date());
                }
                function getAllAlert(obj) {
                    document.getElementById('editAlertId').value = "";
                    document.getElementById('deleteAlertId').value = "";
                    document.getElementById('txtPatientAlert').value = "";
                    document.forms["frmPtAlert"].submit();
                }
            </script>
        </head>

        <body>
            <form action="" method="post" name="frmPtAlert">
                <div class="mainwhtbox" id="rewiewmedHxData">

                    <div class="purple_bar">
                        <div class="row">
                            <div class="col-xs-4">Patient Alert</div>
                            <div class="col-xs-4 text-center"><?php echo $patientName; ?></div>
                            <div class="col-xs-4"><span class="pull-right" style="margin-top:-7px;">
                          <!--<select id="comboAlerts" name="comboAlerts" class="selectpicker" onChange="getAllAlert(this);">
                                  <option value="all" <?php echo $_REQUEST['comboAlerts'] == "all" ? 'selected' : ''; ?>>ALL</option>
                                  <option value="active" <?php echo $_REQUEST['comboAlerts'] <> "all" ? 'selected' : ''; ?>>Active</option>
                          </select>-->
                                    <div class="checkbox">
                                        <input type="checkbox" id="comboAlerts" name="comboAlerts" onChange="getAllAlert(this);" <?php echo $_REQUEST['comboAlerts'] == "all" ? 'checked' : ''; ?> value="all" />
                                        <label for="comboAlerts">View All</label>
                                    </div>
                                </span></div>
                        </div>
                    </div>


                    <input type="hidden" name="pid" value="<?php echo $_SESSION['patient']; ?>"/>
                    <input type="hidden" name="editAlertId" id="editAlertId" value="<?php echo $_REQUEST['editAlertId']; ?>" />
                    <input type="hidden" name="deleteAlertId" id="deleteAlertId"  />
                    <input type="hidden" name="operatorInitial" id="operatorInitial" value="<?php echo $operatorInitial; ?>" />



                    <div id="patient_alert_data" style="height:380px; overflow:auto; ">
                        <?php echo $patientAlertData; ?>	
                    </div>

                </div>

                <!--                        <div class="bg-info pd15 " id="tdLabel"><b>
                <?php echo (empty($_REQUEST['editAlertId']) == false) ? 'Edit' : 'Add'; ?> Patient Alert Information
                                            </b></div>-->

                <div class="mainwhtbox">
                    <div class="row">
                        <div class="col-xs-4 col-sm-2">
                            <label class="mt5">Alert display date </label>
                            <div class="input-group" style="width:120px;">
                                <input type="text" class="form-control" id="msg_deli_dt" name="msg_deli_dt" value="<?php echo $msg_deli_dt; ?>" placeholder="Alert Deliver Date">
                                <label class="input-group-addon btn" for="msg_deli_dt">
                                    <i class="glyphicon glyphicon-calendar"></i>
                                </label>
                            </div>
                        </div>
                        <div class="col-xs-4 col-sm-2">
                            <div class="checkbox">
                                <input type="checkbox" id="medical" name="medical" value="1" <?php echo ($arrAlertToShowUnder[0] == "1") ? "checked" : ""; ?>><label for="medical">Medical</label>
                            </div>
                            <div class="clearfix"></div>
                            <div class="checkbox">
                                <input type="checkbox" id="frontDesk" name="frontDesk" value="1" <?php echo ($arrAlertToShowUnder[1] == "1") ? "checked" : ""; ?>><label for="frontDesk">Front Desk</label>
                            </div>
                        </div>
                        <div class="col-xs-4 col-sm-2">
                            <div class="checkbox">
                                <input type="checkbox" id="accounting" name="accounting" value="1" <?php echo ($arrAlertToShowUnder[2] == "1") ? "checked" : ""; ?>><label for="accounting">Accounting</label>
                            </div>
                            <div class="clearfix"></div>
                            <div class="checkbox">
                                <input type="checkbox" id="sx" name="sx" value="1" <?php echo ($arrAlertToShowUnder[3] == "1") ? "checked" : ""; ?>><label for="sx">Sx</label>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 pd10">
                            <textarea id="txtPatientAlert" name="txtPatientAlert" class="form-control" rows="3" ><?php echo stripslashes($patientAlertMsgSelected); ?></textarea>
                        </div>
                    </div>


                    <footer  id="module_buttons" class="ad_modal_footer modal-footer">
                        <?php
                        $strNewBtDisp = "hidden";
                        if (empty($_REQUEST['editAlertId']) == false) {
                            $strNewBtDisp = "";
                        }
                        ?>
                        <input type="button" class="btn btn-success <?php echo $strNewBtDisp; ?>" id="new" title="Click To Add new Patient Alert" value="Add New" onClick="newForm();"/>
                        <input type="button" class="btn btn-success" id="save" title="Click To Save Patient Alert" value="Save" onClick="saveForm();"/>
                        <input type="button" class="btn btn-danger" id="close" title="Click To Close Window" value="Close" onClick="window.close();"/>
                    </footer>
                </div>
            </div>  
        </form>
    </body>
    </html>

    <script language="javascript">
        if (typeof (window.opener.top.update_iconbar) == 'function')
            window.opener.top.update_iconbar();
        if (typeof (window.opener.top.innerDim) == 'function') {
            var innerDim = window.opener.top.innerDim();
            if (innerDim['w'] > 1200)
                innerDim['w'] = 1200;
            if (innerDim['h'] > 670)
                innerDim['h'] = 670;
            window.resizeTo(innerDim['w'], innerDim['h']);
            var t = 50;
            var l = parseInt(((screen.availWidth - window.outerWidth) / 2))
            window.moveTo(l, t);
        }
    </script>
    <?php
} else {
    echo "<script>alert('Please select patient')</script>";
}
?>