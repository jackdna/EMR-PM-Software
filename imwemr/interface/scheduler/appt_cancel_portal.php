<?php
//$ignoreAuth = true;
require_once(dirname(__FILE__) . '/../../config/globals.php');
$apptload = $_REQUEST['apptload'];
//START READ RECORD
$msg = '';
$row_arr = array();
$qry = "SELECT cr.id as row_id, cr.app_ext_id, cr.app_can_req_id, cr.can_reason, 
		IFNULL(DATE_FORMAT(sa.sa_app_start_date,'%m-%d-%Y'),'')	AS sa_app_start_date_format,
		IFNULL(TIME_FORMAT(sa.sa_app_starttime,'%h:%i %p'),'')	AS sa_app_starttime_format,
		pd.pid AS pt_id, 
		TRIM(CONCAT(pd.lname,', ', pd.fname, pd.mname)) AS patient_name,
		IFNULL(spp.proc,'') 			AS pri_procedure_name, 
		IFNULL(sps.proc,'') 			AS sec_procedure_name, 
		IFNULL(spt.proc,'') 			AS ter_procedure_name
		
		FROM iportal_app_reqs cr
		INNER JOIN schedule_appointments sa ON (sa.id = cr.app_ext_id)
		INNER JOIN patient_data pd ON (pd.pid = sa.sa_patient_id)
		LEFT JOIN slot_procedures spp ON (spp.id = sa.procedureid)
		LEFT JOIN slot_procedures sps ON (sps.id = sa.sec_procedureid)
		LEFT JOIN slot_procedures spt ON (spt.id = sa.tertiary_procedureid)
		WHERE cr.aprv_dec = '0' AND cr.app_can_req_id != '' 
		GROUP BY cr.id
		ORDER BY sa.sa_app_start_date DESC, sa.sa_app_starttime, pd.lname, pd.fname, pd.mname ";
$res=imw_query($qry) or die(imw_error().$qry);
$numrow = imw_num_rows($res);
if($numrow>0) {
	while($rows = imw_fetch_assoc($res)) {
		$row_arr[] = $rows;
	}
}else {
	$msg = 'No Record';
}
//END READ RECORD


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Appointment Cancellation Requests from Portal :: imwemr ::</title>
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css">
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/core.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/schedulemain.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-select.css" type="text/css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/patient_info.css" type="text/css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/messi/messi.css" type="text/css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery.datetimepicker.min.css" type="text/css">

    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/sc_script.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/messi/messi.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/core_main.js"></script>

    <script>

		function chkAll(obj){
			var cbkObj = null;
			cbkObj =  document.getElementsByName('cbkPrev');
			if(obj.checked == true){
				for(var a = 0; a < cbkObj.length; a++){
					cbkObj.item(a).checked = true;
				}
			}
			else if(obj.checked == false){
				for(var a = 0; a < cbkObj.length; a++){
					cbkObj.item(a).checked = false;
				}
			}
		}

		function close_me()
		{	
					//opener.see_sel();						
					window.close();
		}

        $(document).ready(function() {
            show_loading_image('hide');
			$("#success-alert").fadeTo(2000, 500).slideUp(500, function() {
                $("#success-alert").slideUp(500);
            });
			var apptload = '<?php echo $apptload;?>';
			if(apptload == '1') {
				top.pull_cancel_request(apptload);	
			}
        });
			
    </script>
</head>
<body>
    <div id="div_loading_image" class="text-center">
        <div class="loading_container">
            <div class="process_loader"></div>
            <div id="div_loading_text" class="text-info">Please wait, while documents are getting ready for you...</div>
        </div>
    </div>
    <div class="container-fluid tolist ">

        <div class="whtbox">
            <div class="pd10">
                <div id="" class="row">
                    <div class="col-sm-12">

                        <div class="showrst">
                            <div class="row">
                                <div class="col-sm-6 text-left ">
                                    <h4>Appointment Cancellation Requests from Portal</h4> </div>
                                <div class="col-sm-6 text-right">
                                
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <h1 style="font-size:18px;font-weight:normal;margin:0px;padding:0px;"> Following Patients have requested Appointment Cancellation from Patient Portal </h1>
                                <h2 style="font-size:14px;font-weight:normal;margin:5px 0px 10px 0px;padding:0px;"> Please take the appropriate action.</h2>
                            </div>
                            <div class="col-sm-6 text-right">
                                <span style="padding-left:3px;"><button type="button" class="btn btn-success" title="Portal Communications" onclick="window.open('../../interface/scheduler/portal_communications.php', width=1000, height=300);">Portal Communications</button></span>
                                <span style="padding-left:3px;"><button type="button" class="btn btn-success" title="Refresh cancel request" onclick="top.pull_cancel_request(0);">Refresh Cancel Request</button></span>
                                <span style="padding-left:3px;"><button type="button" class="btn btn-success" title="Approve all selected request" onclick="top.approve_disapprove_all_appt('approve','0',false);">Approve All Selected</button></span>
                                <span style="padding-left:3px;"><button type="button" class="btn btn-success" title="Decline all selected request" onclick="top.approve_disapprove_all_appt('decline','0',false);">Decline All Selected</button></span>
                                <span style="padding-left:3px;"><button type="button" class="btn btn-success" title="Refresh without cancel request" onclick="window.top.location.reload();">Reload with approved changes</button></span>
                                <span style="padding-left:3px;"><button type="button" class="btn btn-danger" title="Close" onclick="close_me();">Close</button></span>
                            </div>
                            
                        </div>
                        <div style="height:580px;overflow-x:hidden;overflow-y:auto;">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-bordered table-hover table-striped">
                                        <thead class="header">
                                            <tr class="grythead">
                                                <th>All</th>
                                                <th>
                                                    <div class="checkbox checkbox-inline">
                                                        <input type="checkbox" id="cbkChkAll" name="cbkChkAll" onclick="chkAll(this)" autocomplete="off">
                                                        <label for="cbkChkAll"></label>
                                                    </div>
                                                </th>
                                                <th>Appointment Date and Time</th>
                                                <th>Patient Name</th>
                                                <th>Appointment Reason</th>
                                                <th>Cancellation Request Reason</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody >
                                            <?php
											$a = 0;
											if(count($row_arr)>0) {
												foreach($row_arr as $row) {
													$a++;
													$row_id = $row['row_id'];
													$sch_id = $row['app_ext_id'];
													$sa_app_start_date_format = $row['sa_app_start_date_format'];
													$sa_app_starttime_format = $row['sa_app_starttime_format'];
													$patient_name = trim(stripslashes($row['patient_name']));
													$pt_id = $row['pt_id'];
													$patient_name_id = ($pt_id && $patient_name) ? trim($patient_name.' - '.$pt_id) : $patient_name;
													$pri_procedure_name = trim(stripslashes($row['pri_procedure_name']));
													$sec_procedure_name = trim(stripslashes($row['sec_procedure_name']));
													$ter_procedure_name = trim(stripslashes($row['ter_procedure_name']));
													$proc_name = $pri_procedure_name;
													$proc_name = $sec_procedure_name ? $proc_name.'<br>'.$sec_procedure_name : $proc_name;
													$proc_name = $ter_procedure_name ? $proc_name.'<br>'.$ter_procedure_name : $proc_name;
													$proc_name = trim($proc_name);
													$can_reason = trim(stripslashes($row['can_reason']));
												?>
													<tr>
														<td><?php echo $a;?></td>
														<td>
															<div class="checkbox checkbox-inline">
																<input type="checkbox" id="cbk<?php echo $row_id;?>" data-id="<?php echo $row_id;?>" value="<?php echo $row_id;?>-<?php echo $sch_id;?>" name="cbkPrev" autocomplete="off">
																<label for="cbk<?php echo $row_id;?>"></label>
															</div>
														</td>
														<td><?php echo $sa_app_start_date_format.' '.$sa_app_starttime_format;?></td>
														<td><?php echo $patient_name_id;?></td>
														<td><?php echo $proc_name;?></td>
														<td><?php echo $can_reason;?></td>
														<td id="portal_approve_<?php echo $row_id;?>" >
															<div class="btn-group">
																<span><button class="btn btn-success" data-filter="cancelappt" id="btn_approve_<?php echo $row_id;?>" onclick="top.approve_disapprove_appt('<?php echo $row_id;?>', this,'approve');" title="Approve cancel request">Approve</button></span>
																<span style="padding-left:3px;"><button class="btn btn-danger" data-filter="cancelappt"  id="btn_decline_<?php echo $row_id;?>" onclick="top.approve_disapprove_appt('<?php echo $row_id;?>', this,'decline');" title="Decline cancel request">Decline</button></span>
															</div>
														</td>
														
														
													</tr>
												<?php
												}
											}else {?>
                                            	<tr><td colspan="7" class="text-center"><strong>No Record</strong></td></tr>	
                                            <?php
											}
											?>    
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>