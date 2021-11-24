<?php
//$ignoreAuth = true;
require_once(dirname(__FILE__) . '/../../config/globals.php');
include_once($GLOBALS['fileroot'] . '/library/classes/common_function.php');
$apptload = $_REQUEST['apptload'];

$date_format_SQL = get_sql_date_format();
//START READ RECORD
$msg = '';

//getting all facilities name
$qryFac="select id,name from facility order by name";
$resultFac=imw_query($qryFac);
$facility_options="<option value=''>Facility All</option>";
while($Facarr = imw_fetch_array($resultFac)){
    $sel=($_POST['facilities']==$Facarr["id"]) ? 'SELECTED': '';
    $facility_options.='<option value="'.$Facarr["id"].'" '.$sel.'>'.$Facarr['name'].'</option>';
}

//geting user list that have and enable scheduler
$qryPro = $qry = "Select id, fname, lname, mname from users where Enable_Scheduler = '1' and delete_status = '0' order by lname, fname";
$resultPro = imw_query($qryPro);
$physician_options="<option value=''>Physician All</option>";
while($ProArr = imw_fetch_assoc($resultPro)){
    $name=core_name_format($ProArr["lname"], $ProArr["fname"], $ProArr["mname"]);
    $sel=($_POST['physicians']==$ProArr["id"]) ? 'SELECTED': '';

    $physician_options.='<option value="'.$ProArr["id"].'" '.$sel.'>'.$name.'</option>';
}

/* ERP PORTAL CREATE NEW PATIENT */
$erp_error=array();
if(isERPPortalEnabled()) {
	try {
		//For ERP Patient Portal API
		include_once($GLOBALS['srcdir']."/erp_portal/rabbitmq_exchange.php");
		include_once($GLOBALS['srcdir']."/erp_portal/appointmentrequests.php");
		
		$obj_erp = new AppointmentRequests();
		$arrERP=$obj_erp->getUpdatesAppointments();
		
		//FETCH EXISTING PORTAL COMMUNICATION DATA
		$qry="Select * FROM iportal_communications";
		$rs=imw_query($qry);
		$arrExisingData=array();
		while($res=imw_fetch_assoc($rs)){
			$arrExisingData[$res['imw_appt_id']][$res['update_status_to']]=$res['imw_appt_id'];
		}

		$update_uniq_id_arr=array();
		//ADDING NEW DATA
		foreach($arrERP['rows'] as $dataDetail){
			if(!$arrExisingData[$dataDetail['appointmentExternalID']][$dataDetail['updateStatusTo']]){
			   $updated_in_schedule=0;

			  
				if($dataDetail['updateStatusTo']=='Confirmed' || $dataDetail['updateStatusTo']=='Canceled'){
					
					//GETTING STATUS IDS
					$qry="Select id, status_name FROM schedule_status WHERE LOWER(status_name) IN('confirm','cancelled')";
					$rs=imw_query($qry);
					while($res=imw_fetch_assoc($rs)){
						$status= (strtolower($res['status_name'])=='confirm') ? 'Confirmed': 'Canceled';
						$arrStatusIds[$status]=$res['id'];
					}

					//GETTING EXISTING COMMENT DATA
					$existing_comment=$sa_comment='';
					$qry="Select * FROM schedule_appointments WHERE id='".$dataDetail['appointmentExternalID']."'";
					$rs1=imw_query($qry);
					$res1=imw_fetch_assoc($rs1);
					$existing_comment=trim($res1['sa_comments']);
					
					//$new_comment='Status updated to '.$status_name.' by API';
					$qry_comm_part='';
					$new_comment= 'Status updated by patient portal communication.';
					
					if($new_comment!='' && empty($new_comment)==false){ 
						$sa_comment=(empty($existing_comment)==false)? $existing_comment.' '.$new_comment : $new_comment;
						$qry_comm_part=",sa_comments='".$sa_comment."'";
					}

					//UPDATING APPOINTMENT
					$rs_update=imw_query("Update schedule_appointments SET 
					sa_patient_app_status_id='".$arrStatusIds[$dataDetail['updateStatusTo']]."',
					status_update_operator_id='".$_SESSION['authUserID']."', 
					sa_app_time='".date('Y-m-d H:i:s')."',
					sa_madeby='".$_SESSION['authUserID']."'
					".$qry_comm_part."                 
					WHERE id='".$dataDetail['appointmentExternalID']."'");

					if($rs_update){
						$updated_in_schedule=1;

						$qry_prev="Select * FROM previous_status WHERE sch_id='".$res1['id']."' ORDER BY id DESC LIMIT 1";
						$rs_prev=imw_query($qry_prev);
						$res_prev=imw_fetch_assoc($rs_prev);

						//UPDATE TO PREVIOUS STATUS TABLE
						$qry2="Insert INTO previous_status SET 
						sch_id='".$dataDetail['appointmentExternalID']."',
						patient_id='".$res1['sa_patient_id']."',
						status_time='".date('H:i:s')."',
						status_date='".date('Y-m-d')."',
						status='".$arrStatusIds[$dataDetail['updateStatusTo']]."',
						old_date='".$res1['sa_app_start_date']."',
						old_time='".$res1['sa_app_starttime']."',
						old_provider='".$res1['sa_doctor_id']."',
						old_facility='".$res1['sa_facility_id']."',
						statusComments='".$sa_comment."',
						oldMadeBy='".$res_prev['statusChangedBy']."',
						statusChangedBy='imwdev',
						dateTime='".date('Y-m-d H:i:s')."',
						new_facility='".$res1['sa_facility_id']."',
						new_provider='".$res1['sa_doctor_id']."',
						old_status='".$res_prev['status']."',
						new_appt_date='".$res1['sa_app_start_date']."',
						new_appt_start_time='".$res1['sa_app_starttime']."',
						old_appt_end_time='".$res1['sa_app_endtime']."',
						new_appt_end_time='".$res1['sa_app_endtime']."',
						old_procedure_id='".$res1['procedureid']."',
						old_sec_procedure_id	='".$res1['sec_procedureid']."',
						old_ter_procedure_id='".$res1['tertiary_procedureid']."',
						oldStatusComments='".$res1['sa_comments']."',
						new_procedure_id='".$res1['procedureid']."',
						new_sec_procedure_id='".$res1['sec_procedureid']."',
						new_ter_procedure_id='".$res1['tertiary_procedureid']."'";
						$rs2=imw_query($qry2);
					}
				}

				imw_query("INSERT INTO iportal_communications SET
				erp_id='".$dataDetail['appointmentUpdateID']."',
				imw_appt_id='".$dataDetail['appointmentExternalID']."',
				update_status_to='".$dataDetail['updateStatusTo']."',
				method='".$dataDetail['method']."',
				date_time='".date('Y-m-d H:i:s')."',
				operator_id='".$_SESSION['authUserID']."',
				updated_in_schedule='".$updated_in_schedule."'");  

				/*Save appointmentUpdateID in an array*/
				$update_uniq_id_arr[]=$dataDetail['appointmentUpdateID'];
			}
		}
		
		if(count($update_uniq_id_arr)>0) {
			$obj_erp->appointmentUpdatesSent($update_uniq_id_arr);
		}
	} catch(Exception $e) {
		$erp_error[]='Unable to connect to ERP Portal';
	}
}

//--- CHANGE DATE FORMAT -------
if($from_date != '' && $to_date != ''){
    $Start_date = getDateFormatDB($from_date);
    $End_date = getDateFormatDB($to_date);	
}

$qry="Select ic.update_status_to, ic.method, sa.sa_patient_id, DATE_FORMAT(sa.sa_app_start_date, '".$date_format_SQL."') as 'sa_app_start_date',
sa.sa_app_starttime, pd.fname, pd.mname, pd.lname  
FROM iportal_communications ic JOIN schedule_appointments sa ON sa.id=ic.imw_appt_id 
JOIN patient_data pd ON pd.id = sa.sa_patient_id";
if(empty($_POST['appt_status'])==false){
    $qry.=" AND ic.update_status_to='".$_POST['appt_status']."'";
}
if(empty($_POST['facilities'])==false){
    $qry.=" AND sa.sa_facility_id='".$_POST['facilities']."'";
}
if(empty($_POST['physicians'])==false){
    $qry.=" AND sa.sa_docotor_id='".$_POST['physicians']."'";
}
if($from_date != '' && $to_date != ''){
    $qry.=" AND (sa.sa_app_start_date BETWEEN '".$Start_date."' AND '".$End_date."')";
}
$qry.=" ORDER BY sa.sa_patient_id, ic.id";
$rsData=imw_query($qry);
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
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.datetimepicker.full.min.js"></script>

    <script>

	        $(document).ready(function() {
            show_loading_image('hide');
			$("#success-alert").fadeTo(2000, 500).slideUp(500, function() {
                $("#success-alert").slideUp(500);
            });
        });
       
        $(function(){		
		$('.date-pick').datetimepicker({
			timepicker:false,
			format:'m-d-Y',
			formatDate:'Y-m-d'
		});
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
                                    <h4>List of Appointments Changed through Communications</h4> </div>
                                <div class="col-sm-6 text-right">
                                
                                </div>
                            </div>
                        </div>
                        <div class="row form-inline">
                        <div class="col-sm-8" style="height: 40px">
                        <form name="frm_reports" id="frm_reports" action="" method="post">
                            <div class="col-sm-2">
                                <div class="form-group multiselect">
                                    <select name="appt_status" class="form-control minimal" style="width: 150px">
                                        <option value="">All</option>
                                        <option value="Confirmed" <?php echo ($_POST['appt_status']=='Confirmed')?'SELECTED':'';?> >Confirmed</option>
                                        <option value="Canceled" <?php echo ($_POST['appt_status']=='Canceled')?'SELECTED':'';?>>Canceled</option>
                                        <option value="LeftMessage" <?php echo ($_POST['appt_status']=='LeftMessage')?'SELECTED':'';?>>LeftMessage</option>
                                        <option value="UnableToReach" <?php echo ($_POST['appt_status']=='UnableToReach')?'SELECTED':'';?>>UnableToReach</option>                                        
                                    </select>   
                                </div>
                            </div>    

                            
                            <div class="col-sm-2 form-group multiselect">
                                <select name="facilities" class="form-control minimal" style="width: 150px"><?php echo $facility_options;?></select>
                            </div>

                            <div class="col-sm-2 form-group multiselect">
                                <select name="physicians" class="form-control minimal" style="width: 150px"><?php echo $physician_options;?></select>
                            </div>

                            <div class="col-sm-2">
                                <div class="input-group">
                                    <input type="text" name="from_date" placeholder="Appt Date From" style="font-size: 12px;" id="from_date" value="<?php echo $_REQUEST['from_date'];?>" class="form-control date-pick" autocomplete="off">
                                    <div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></div>
                                </div>
                            </div>	
                            <div class="col-sm-2 ">
                                <div class="input-group">
                                    <input type="text" name="to_date" placeholder="Appt Date To" style="font-size: 12px;" id="to_date" value="<?php echo $_REQUEST['to_date'];?>" class="form-control date-pick" autocomplete="off">
                                    <div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></div>
                                </div>
                            </div>
                            <span style="padding-left:3px;"><button type="button" class="btn btn-success" title="Search" onclick="document.frm_reports.submit();">Search</button></span>
                        </form>
                        </div>
                        
                        </div>
                        <div style="height:580px;overflow-x:hidden;overflow-y:auto;">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-bordered table-hover table-striped">
                                        <thead class="header">
                                            <tr class="grythead">
                                                <th>#</th>
                                                <th>Appointment Date</th>
                                                <th>Appointment Time</th>
                                                <th>Patient Name</th>
                                                <th>Update Status To</th>
                                                <th>Method</th>
                                            </tr>
                                        </thead>
                                        <tbody >
                                            <?php
											$a = 0;
											if(imw_num_rows($rsData)>0) {
                                                while($res=imw_fetch_assoc($rsData)){    
													$a++;
                                                    $patient_name = core_name_format($res['lname'], $res['fname'], $res['mname']);
												?>
													<tr>
														<td><?php echo $a;?></td>
                                                        <td><?php echo $res['sa_app_start_date'];?></td>
                                                        <td><?php echo $res['sa_app_starttime'];?></td>
														<td><?php echo $patient_name.' - '.$res['sa_patient_id'];?></td>
														<td><?php echo $res['update_status_to'];?></td>
														<td><?php echo $res['method'];?></td>
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