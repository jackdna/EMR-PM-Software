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
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");// always modified
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");// HTTP/1.1
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);// HTTP/1.0header("Pragma: no-cache"); 

require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_page_functions.php');
//for printing purpose
include_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');//to get save location
include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');//function to write html

$dateFormat= get_sql_date_format();
function mysql_date_format($strdate){
	if($strdate!=""){
		$date_array=explode("-",$strdate);
		$sqldate=$date_array["2"]."-".$date_array["0"]."-".$date_array["1"];//yy,mm,dd
	}else{
		$sqldate="0000-00-00";
	}
	return($sqldate);	
}
$page_limit = 200;
if(isset($_REQUEST['cur_page_no']) && trim($_REQUEST['cur_page_no']) != "")
{
	$cur_page_no = $_REQUEST['cur_page_no'];
}
else
{
	$cur_page_no = 1;	
}
$limit_follow = ($cur_page_no-1)*$page_limit;
$fac_id=$_REQUEST['facility_id'];

/*$qry = "SELECT previous_status.id, 
		previous_status.sch_id, 
		previous_status.status_date, 
		previous_status.patient_id, 
		previous_status.status_time, 
		previous_status.old_date, 
		previous_status.old_time, 
		schedule_appointments.procedureid, 
		date_format(schedule_appointments.sa_app_time,'%m-%d-%y') as sa_app_time, 
		date_format(schedule_appointments.sa_app_start_date,'%m-%d-%y') as sa_app_start_date, 
		sa_app_starttime, 
		slot_procedures.proc,
		facility.name as fac_name 
		FROM previous_status
		LEFT JOIN schedule_appointments 
		ON (
			previous_status.sch_id = schedule_appointments.id 
			AND schedule_appointments.sa_patient_app_status_id = '201'
			)
		JOIN facility 
		ON facility.id = schedule_appointments.sa_facility_id
		LEFT JOIN slot_procedures 
		ON slot_procedures.id = schedule_appointments.procedureid ";*/

$qry = "SELECT previous_status.id, 
		previous_status.sch_id, 
		previous_status.status_date, 
		previous_status.patient_id, 
		previous_status.status_time, 
		previous_status.old_date, 
		previous_status.old_time, 
		schedule_appointments.procedureid, 
		date_format(schedule_appointments.sa_app_time,'%m-%d-%y') as sa_app_time, 
		date_format(schedule_appointments.sa_app_start_date,'%m-%d-%y') as sa_app_start_date, 
		sa_app_starttime, 
		slot_procedures.proc,
		facility.name as fac_name 
		FROM previous_status
		INNER JOIN schedule_appointments 
		ON (previous_status.sch_id = schedule_appointments.id 
		AND schedule_appointments.sa_patient_app_status_id = '201')			
		LEFT JOIN facility 
		ON facility.id = schedule_appointments.sa_facility_id
		LEFT JOIN slot_procedures 
		ON slot_procedures.id = schedule_appointments.procedureid";
				
$count_qry = "SELECT schedule_appointments.id 
			FROM schedule_appointments 
			LEFT JOIN facility ON facility.id = schedule_appointments.sa_facility_id
			LEFT JOIN slot_procedures 
			ON slot_procedures.id = schedule_appointments.procedureid";
			
if($_REQUEST['searchText'])
{
	//if we do have text search then add some more joins
	$qry .=" LEFT JOIN patient_data
			ON patient_data.id = schedule_appointments.sa_patient_id";
				
	$count_qry .=" LEFT JOIN patient_data
				ON patient_data.id = schedule_appointments.sa_patient_id"; 	
}

//$qry .= " WHERE previous_status.status = '201'";
$qry .= " WHERE 1=1";
$count_qry .=" WHERE schedule_appointments.sa_patient_app_status_id = '201'";

if($_REQUEST['searchText'])
{
	$search_str=trim(imw_real_escape_string($_REQUEST[searchText]));
	//search text in patient name
	if(is_numeric($_REQUEST[searchText]))
	{
		$str =" AND (
						patient_data.id LIKE '%$search_str%' 
					)"; 
	}
	else
	{
		if((stristr(trim($_REQUEST[searchText]),' ')===false))
		{
			$str =" AND (
					patient_data.fname LIKE '%$search_str%' 
					OR patient_data.lname LIKE '%$search_str%' 
					)"; 	
		}
		else
		{
			$strArr=explode(' ',$search_str);
			$str =" AND (
					patient_data.fname LIKE '%$strArr[0]%' 
					OR patient_data.lname LIKE '%$strArr[0]%'
					
					OR patient_data.fname LIKE '%$strArr[1]%' 
					OR patient_data.lname LIKE '%$strArr[1]%' 
					)"; 	
		}
	}
	$qry .=$str; 	
	$count_qry .=$str; 	
}

if(!empty($fac_id)){
	$qry .=" AND schedule_appointments.sa_facility_id = '$fac_id'"; 	
	$count_qry .=" AND schedule_appointments.sa_facility_id = '$fac_id'"; 
}
if(!empty($prov_id)){
	$qry .=" AND schedule_appointments.sa_doctor_id = '$prov_id'"; 	
	$count_qry .=" AND schedule_appointments.sa_doctor_id = '$prov_id'"; 
}

if(!empty($from_date)){
	$from_date=mysql_date_format($from_date);
	$qry .=" AND schedule_appointments.sa_app_start_date >= '$from_date'"; 	
	$count_qry .=" AND schedule_appointments.sa_app_start_date >= '$from_date'"; 
}

if(!empty($to_date)){
	$to_date=mysql_date_format($to_date);
	$qry .=" AND schedule_appointments.sa_app_start_date <= '$to_date'"; 	
	$count_qry .=" AND schedule_appointments.sa_app_start_date <= '$to_date'"; 
}
$qry .=" GROUP BY previous_status.sch_id ORDER BY schedule_appointments.sa_app_start_date, schedule_appointments.sa_app_time DESC LIMIT ".$limit_follow.", ".$page_limit;

$vsql_pro1 = imw_query($qry)or die(imw_error());	
//echo $qry;
$num_pro1=imw_num_rows($vsql_pro1);
$printData = '';


$count_qry_ob = imw_query($count_qry);
while($count_qry_ob_data=imw_fetch_assoc($count_qry_ob)){
	$arr_sch_ids[$count_qry_ob_data['id']]=$count_qry_ob_data['id'];
}
//GETTING SEND TO SCHEDULE LIST DATE
$arr_send_info=array();
if(sizeof($arr_sch_ids)>0){
	$str_sch_ids=implode(',', $arr_sch_ids);
	$qry="Select sch_id, DATE_FORMAT(status_date, '$dateFormat') as status_date, 
	DATE_FORMAT(status_time, '%h:%i %p') as 'status_time' FROM previous_status WHERE sch_id IN(".$str_sch_ids.") ORDER BY id ASC";
	$rs=imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$arr_send_info[$res['sch_id']] = $res['status_date'].' '.$res['status_time'];
	}
}
$total_appts = sizeof($arr_sch_ids);
$total_pages = ceil($total_appts/$page_limit);

if($total_appts == 0)
{
	$total_pages = 1;
	$limit_follow += -1;		
}
if($cur_page_no > $total_pages){$cur_page_no = $total_pages;}
//pre($_SESSION, 1);

$qryFac="select id,name from facility order by name";
$resultFac=imw_query($qryFac);
$arrFacility=array();
while($Facarr = imw_fetch_array($resultFac)){
	$arrFacility[$Facarr['id']]=$Facarr['name'];
}
$qryPro = $qry = "select id, fname, lname, mname from users where Enable_Scheduler = '1' and delete_status = '0' order by lname, fname";
$resultPro = imw_query($qryPro);
$arr_users=array();
while($ProArr = imw_fetch_assoc($resultPro)){
	$arr_users[$ProArr["id"]]=core_name_format($ProArr["lname"], $ProArr["fname"], $ProArr["mname"]);
}
?>
<!DOCTYPE html>
<head>
<title>To-Do</title>
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/schedulemain.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/messi/messi.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery.datetimepicker.min.css" type="text/css">
    
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/jquery.min.1.12.4.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/messi/messi.js"></script>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.datetimepicker.full.min.js"></script>
    
	<script type="text/javascript">
	<?php if(trim($_SESSION['restore_act']) == 'restore_true'){echo 'window.opener.change_date(\'sel_date\');';} ?>	
	function edit_schedule(sch_id,st_id,dt,pro,fac,patid)
	{		opener.pre_load_front_desk(patid, sch_id);
			//opener.refresh_patient_infopage(patid,sch_id,'to_do');
			opener.focus();
			window.close();
	}
	function see_sel()
	{	
			window.location.reload();
	}
	function close_me()
	{	
				//opener.see_sel();						
				window.close();
	}	

	function strstr (haystack, needle, bool) {
		var pos = 0;
		
		haystack += '';
		pos = haystack.indexOf( needle );
		if (pos == -1) {
			return false;
		} else{
			if (bool){
				return haystack.substr( 0, pos );
			} else{
				return haystack.slice( pos );
			}
		}
	}

	function searchForTextNew(){
		
	   document.facFrm.submit();
		
		
	}
	
	function searchForText(){
		var searchText = document.getElementById("searchText").value.toLowerCase();
		
		//if(searchText != ""){
			var loopTime = document.getElementsByName("searchArea[]").length;
			var f1=1;
			for(i = 0; i < loopTime; i++){			
				var hidId = document.getElementsByName("searchArea[]")[i].id;
				var tdId = "td"+hidId.substring(3);
				document.getElementById(tdId).className = "";			
			}
			for(i = 0; i < loopTime; i++){			
				var hidId = document.getElementsByName("searchArea[]")[i].id;
				var txt = document.getElementById(hidId).value.toLowerCase();
				
				var tdId = "td"+hidId.substring(3);
				var anchtdId = "anchor"+hidId.substring(3);
				
				if(searchText != ""){
					if(strstr(txt,searchText)){
						document.getElementById(tdId).className = "changeTDBgColor";
						if(f1==1){
							document.getElementById("getMyLink").href="#"+anchtdId;	
							document.getElementById("getMyLink").click();
						}
						f1++;
					}
				}
			}
		//}
	}
	function sub_facility()
	{
	   document.facFrm.submit();
	}
	
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
	function restore_delete_all(op){
		var cbkObj = null;
		cbkObj =  document.getElementsByName('cbkPrev');
		var arrId = new Array();
		if(op == "1"){
			for(var a = 0; a < cbkObj.length; a++){
				if(cbkObj.item(a).checked == true){
					var arrValue = cbkObj.item(a).value.split("-");
					arrId.push(arrValue[1]);
				}
			}
			if(arrId.length > 0){
				document.getElementById("hidAction").value = "restore";
				document.getElementById("hidShcId").value = arrId.join(",");
				top.fancyConfirm("Sure! you want to restore selected appointment(s)?","", "document.del_frm.submit()");
			}
			else{
				top.fAlert("Please select appointment for restoring!");
			}
		}else if(op=="2"){
			for(var a = 0; a < cbkObj.length; a++){
				if(cbkObj.item(a).checked == true){
					var arrValue = cbkObj.item(a).value.split("-");
					arrId.push(arrValue[1]);
				}
			}
			if(arrId.length > 0){
				document.getElementById("hidAction").value = "del";
				document.getElementById("to_do_id").value = arrId.join(",");
				top.fancyConfirm("Sure! you want to delete selected appointment(s)?","", "document.del_frm.submit()");
			}
			else{
				top.fAlert("Please select appointment for deleteing!");
			}
		}else if(op=="3"){
			for(var a = 0; a < cbkObj.length; a++){
				if(cbkObj.item(a).checked == true){
					var arrValue = cbkObj.item(a).value.split("-");
					arrId.push(arrValue[1]);
				}
			}
			if(arrId.length > 0){
				document.getElementById("hidAction").value = "cancel";
				document.getElementById("to_do_id").value = arrId.join(",");
				//reasonId
				//top.fancyConfirm("Sure! you want to cancel selected appointment(s)?","", "document.del_frm.submit()");
				$("#cancel_confirmation").modal('show');
			}
			else{
				top.fAlert("Please select appointment to cancel!");
			}
		}
	}
	function click_page(pg_no)
	{
		document.getElementById('cur_page_no').value = pg_no;
		sub_facility();
	}
	function show_hide_other(reason){
		if(reason == "Other"){
			$("#OtherReasonContainer").show();
			document.getElementById("OtherReasonContainer").focus();
		}else{
			$("#OtherReasonContainer").hide();
		}
	}
		
	function save_cancellation_reason(){
		var reason = $("#cancellation_reason").val();
		if(reason == ""){
			top.fAlert("Please select a reason to continue.");
			return false;
		}else{
			if(reason == "Other"){
				reason = $("#OtherReason").val();
			}
		}
		
		$("#reason").val(reason);
		document.del_frm.submit();	}
		
	$(document).ready(function() {
		$("#success-alert").fadeTo(2000, 500).slideUp(500, function() {
		  $("#success-alert").slideUp(500);
		});
	});
</script>
<style>
	.changeTDBgColor{
		background: #FE9C53;
		color: #000000;
	}
	.pg_format_class{padding:0px 4px;background-color:#fff;color:#000;margin-right:10px;}
	.pg_sel_class{padding:0px 4px;margin-right:10px;background-color:#333;color:#fff;}
	.pg_sel_class:hover{background-color:#fff;color:#000;}
	.grythead {line-height:30px}


.highlight
{
	background:#009500;
	color:#000;	
}
.alert {
    padding: 10px!important;
    margin-bottom: 0px!important; 
}
</style>
</head>
<body>
	<!--modal wrapper class is being used to control modal design-->
        <div class="common_modal_wrapper">
         <!-- Modal -->
        <div id="cancel_confirmation" class="modal fade" role="dialog">
            <div class="modal-dialog modal-md">
            <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                   		<button type="button" class="close" data-dismiss="modal">&times;</button>
                    	<h4 class="modal-title">
                            <div id="day_print_options_div-handle" class="text-left">
                                Cancellation Reason
                            </div>
                        </h4>
                    </div>
                    <div class="modal-body">
						<select id="cancellation_reason" name="cancellation_reason" onchange="show_hide_other(this.value);" class="form-control minimal">
							<option value="">Please select a reason</option>
                        <?php 
						echo load_reasons("OPTIONS");
						?>
						</select><br>

						<div id="OtherReasonContainer" style="display: none;">Specify other reason:<input type="text" id="OtherReason" name="OtherReason" class="form-control" placeholder="Other Reason"></div>
                    </div>
                    <div class="modal-footer" style="overflow:visible">
						<button type="button" class="btn btn-success" value="OK" onclick="save_cancellation_reason()">OK</button>
						<button type="button" class="btn btn-danger" value="Cancel" data-dismiss="modal">Cancel</button>
					</div>
                </div>
            </div>
        </div>
		</div>
        <!--modal wrapper class end here -->
	
<div class="container-fluid tolist">   
 
    <div class="whtbox">	
    <div class="pd10">
    <div class="row"> <div class="col-sm-3">  <ul class="nav nav-tabs" style="border-bottom:none">
        <li id="first_available" class="lead"><a href="to_do_first_avai.php">First Available</a></li>
        <li id="re_schedule" class="active lead"><a href="to_do.php">Re-Schedule List</a></li>
    </ul></div>
    <div class="col-sm-9">
    <div class="row ">
                    <div class="col-sm-3 text-left">
                    <? 
					if($_SESSION['msg']=='succ'){echo"<div class='alert alert-info' id='success-alert'>Record saved !</div>";}
					elseif($_SESSION['deleted']=='success'){echo"<div class='alert alert-info' id='success-alert'>Records deleted successfully.</div>";}
					elseif($_SESSION['cancel']=='success'){echo"<div class='alert alert-info' id='success-alert'>Appointment(s) cancelled successfully.</div>";}
					//elseif(trim($_SESSION['restore_act'])){
					//	$total=$_REQUEST['not_restored']+$_REQUEST['restored'];
					//	echo"<div class='alert alert-info' id='success-alert'>$_REQUEST[restored]/$total Appointment(s) restored successfully.</div>";
					//}
					unset($_SESSION['msg'], $_SESSION['deleted'], $_SESSION['cancel'], $_SESSION['restore_act']);
					?>
                    </div>
                    <div class="col-sm-9 text-right">
                        <form name="facFrm" method="post" action="">
                         <input type="hidden" name="tab_name" id="tab_name" value="re_schedule">
                         <input type="hidden" id="cur_page_no" name="cur_page_no" value="" />
                        <div class="row form-inline">
                            <div class="col-sm-5 text-right">
                                <div class="form-group multiselect">
                                <?php
                                    $option="<option value=''>Facility All</option>";
                                    foreach($arrFacility as $fac_id => $facilityname){
                                        $select='';
                                        if($_REQUEST['facility_id']==$fac_id){
                                            $select="selected='selected'";
                                        }
                                        $option.="<option ".$select." value='".$fac_id."'>".$facilityname."</option>";
                                    }
                                ?>
                                <select onChange="sub_facility()" name="facility_id" class="form-control minimal" style="width: 150px">
                                <?php echo $option;?></select>
                                </div>
                                <div class="form-group multiselect">
                                <?php
                                    $option="<option value=''>Provider All</option>";
                                    foreach($arr_users as $user_id => $username){
                                        $select='';
                                        if($_REQUEST['prov_id']==$user_id){
                                            $select="selected='selected'";
                                        }
                                        $option.="<option ".$select." value=".$user_id.">".$username."</option>";
                                    }
                                ?>
                                <select onChange="sub_facility()" name="prov_id" class="form-control minimal" style="width: 150px"><?php echo $option;?></select>
                                </div>
                            </div>
                            <div class="col-sm-2 text-right">
                                <input type="text" id="searchText" name="searchText" value="<?php echo $_REQUEST[searchText];?>" title="Patient ID OR Patient Name" placeholder="Pt. ID or Pt. Name" class="form-control" style="width: 100%">
                            </div>	
                            <div class="col-sm-2 text-right">
								<div class="input-group">
									<input type="text" name="from_date" placeholder="Appt. Date From" style="font-size: 12px;" id="from_date" value="<?php echo $_REQUEST['from_date'];?>" class="form-control date-pick" autocomplete="off">
									<div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></div>
								</div>
							</div>	
                            <div class="col-sm-2 text-right">
                           		<div class="input-group">
									<input type="text" name="to_date" placeholder="Appt. Date To" style="font-size: 12px;" id="to_date" value="<?php echo $_REQUEST['to_date'];?>" class="form-control date-pick" autocomplete="off">
									<div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></div>
								</div>
							</div>
                            <div class="col-sm-1 text-left">
                                <button tabindex="1" type="button" value="Search" onClick="javascript:searchForTextNew();" class="stnsrch">Search</button>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
    
    </div></div>
    <form name="frmday"><input type="hidden" name="view_date" id="view_date"></form><a href="#" id="getMyLink"></a>	
        <div id="first_appt" class="row">
            <div class="col-sm-12">
                
                <div class="showrst"><div class="row ">
                    <div class="col-sm-6 text-left">  
                        Showing Appointments <?php $limit_appt = $cur_page_no*$page_limit; if($limit_appt>$total_appts){$limit_appt = $total_appts;} echo ($limit_follow+1).' - '.($limit_appt); ?> of <?php echo $total_appts; ?>
                    </div>  
                    <div class="col-sm-6 text-right">
                        <?php 
                        $next = $cur_page_no + 1;
                        $previous = $cur_page_no - 1;
                        $mid_value = 10;
                        $start_point = $cur_page_no- floor($mid_value/2);
                        $end_point = $cur_page_no+ floor($mid_value/2);
                        if($start_point <= 0)
                        {
                            $end_point += abs($start_point); 
                            $start_point = 1;												
                        }
    
                        if($end_point > $total_pages)
                        {
                            $start_point -= ($end_point-$total_pages);
                            if($start_point <= 0){$start_point = 1;}
                            $end_point = $total_pages;
                        }
                        if($previous > 0)
                        {
                            echo '<a class="pg_format_class" style="cursor:pointer;" onclick="click_page('.$previous.')"> Previous </a>';														
                        }											
                        for($p=$start_point;$p<=$end_point;$p++)
                        {
                            $sel_pg = '';
                            $pg_class = 'pg_format_class';
                            if($p==$cur_page_no){$pg_class = 'pg_sel_class';}
                            echo '<a class="'.$pg_class.'" style="cursor:pointer;" onclick="click_page('.$p.')">'.$p.'</a>';												
                        }
                        if($total_pages > $end_point+1)
                        {
                            echo ' ... ';
                        }
                        if($total_pages > $end_point)
                        {
                            echo '<a class="pg_format_class" style="cursor:pointer;" onclick="click_page('.$total_pages.')">'.$total_pages.'</a>';												
                        }
                        if($next <= $total_pages)
                        {
                            echo '<a class="pg_format_class" style="cursor:pointer;" onclick="click_page('.$next.')"> Next </a>';													
                        }
                        ?>
                    </div>
                </div> </div>               	
                <div style="height:580px;overflow-x:hidden;overflow-y:auto;">
                    <div class="row">
                        <div class="col-sm-12"> 
                            <table class="table table-striped table-bordered table-hover adminnw">
                            <thead>
                                <tr>
                                	<th>All</th>
                                    <th>
                                    	<div class="checkbox checkbox-inline">
                                            <input type="checkbox" id="cbkChkAll" name="cbkChkAll" onClick="chkAll(this)">
                                            <label for="cbkChkAll"></label>
                                        </div>
                                    </th>
                                	<th>Patient Name</th>
                                	<th>Phone#</th>
                                    <th>&nbsp;</th>
                                    <th>Provider</th>
                                    <th>Facility</th>
                                    <th>Procedure</th>
                                    <th>Date - Added to List</th>
                                    <th>Date of Appt</th>
                                    <th>Operator</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
							$printData='<table width="100%" border="0" cellspacing="0" cellpadding="0">';								
							
                            if($num_pro1<=0){
                            ?>
                                <tr>
                                	<td colspan="11" style="color:red;padding:10px; height:30px; text-align:center;"> No Record Found. </td>
                                </tr>
                            <?php
                            }
							
                            $counting=$limit_follow+1;	$printD=false;
                            
							while($prev_row=imw_fetch_assoc($vsql_pro1)){
								
                            	$pat_det = patient_data($prev_row['patient_id']);
								$printD=true;
								
								if($pat_det[0] != '' && $pat_det[0] != '&nbsp;'){
									$del_id = $prev_row['id'];
									$sch_det = sch_data($prev_row['sch_id']);
									$to_do_list_time='';
									$to_do_list_date = $arr_send_info[$prev_row['sch_id']];
									
									list($y,$m,$d) = explode('-',$prev_row['old_date']); 
									$app_made_on_date =$prev_row['sa_app_start_date'];
									$app_made_on_time = strtotime($prev_row['sa_app_starttime']);
									$app_made_on_time = date("h:i A",$app_made_on_time);
									$status_id = $sch_det[5];
									$sch_id = $prev_row['sch_id'];
									$proc = $prev_row["proc"];
									?>
                                <tr id="td<?php echo $prev_row['id'];?>">
                                    <td><?php echo $counting.'.';?></td>
                                    <td>
										 <div class="checkbox checkbox-inline">
                                            <input type="checkbox" id="cbk<?php echo $prev_row['id']; ?>" value="<?php echo $del_id."-".$sch_id; ?>" name="cbkPrev">
                                            <label for="cbk<?php echo $prev_row['id']; ?>"></label>
                                        </div>
                                        
                                    </td>
                                    <td>
										<?php
                                        echo("<a name='anchor".$prev_row['id']."' id='anchor".$prev_row['id']."'></a>");										
                                        $patient_id = $prev_row['patient_id'];
                                        print "&nbsp;<a href='javascript:edit_schedule(\"$sch_id\",\"$status_id\",\"$to_do_list_date\",\"$sch_det[3]\",\"$sch_det[5]\",\"$patient_id\");' class='text_11'>".$pat_det[0]." - ".$prev_row['patient_id']."</a>";
                                        ?>
                                    	<input type="hidden" id="hid<?php echo $prev_row['id'];?>" name="searchArea[]" value="<?php echo str_replace("&nbsp;"," ",$pat_det[0]);?>">
                                    </td>
                                    <td><?php echo core_phone_format($pat_det[8]);?></td>
                                   
                                    <td>
										<?php
                                        $pt_id=$prev_row['patient_id'];
                                        include("referrals.php");
                                        ?>             
                                    </td>
                                    <td><?php echo $sch_det[1];?></td>
                                    <td><?php echo $prev_row['fac_name'] ?></td>
                                    <td><span title="<?php echo $proc;?>"><?php echo (strlen($proc)>40)?substr($proc, 0,35).' ...':$proc;?> </span></td>
                                    <td><?php echo $to_do_list_date;?></td>
                                    <td><?php echo $app_made_on_date." ".$app_made_on_time;?></td>
                                    <td><?php echo $sch_det[2];	?> </td>
                                </tr>
                        <?php
						$printData.='
                                <tr>
                                    <td height="30" width="30" valign="top" >'.
                                        $counting.'.
                                    </td>
                                    <td height="30" width="175" valign="top" align="left" >'.
										$pat_det[0]." - ".$prev_row['patient_id'].'
                                    </td>
                                    <td valign="top" width="122" align="center" nowrap class="text_11">&nbsp;'.core_phone_format($pat_det[8]).'</td>
                                    <td valign="top" width="155" nowrap="nowrap" class="text_11">'.$sch_det[1].'</td>
                                    <td valign="top" width="85" nowrap="nowrap" class="text_11">'.$prev_row['fac_name'].'</td>
                                    <td valign="top" width="90" nowrap="nowrap" class="text_11">'.$proc.'</td>
                                    <td valign="top" width="150" align="center" nowrap="nowrap" class="text_11">'.$to_do_list_date." ".$to_do_list_time.'</td>
                                    <td valign="top" width="150" align="center"  nowrap="nowrap" class="text_11">'.$app_made_on_date." ".$app_made_on_time.'</td>
                                    <td valign="top" width="80" align="center" class="text_11">'.$sch_det[2].'</td>
                                </tr>';						
						
							}
							$counting++;
						}
						$printData.='</table></page>';
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
    <div class="row">
        <div class="col-sm-12 text-right">
            <?php
            if($num_pro1 > 0){
            ?>
            <button id="btRestore" class="btn btn-success" name="btRestore" value="Restore" onClick="restore_delete_all('1');">Restore</button>
            <button id="btPrint" class="btn btn-default" name="btPrint" value="Print" onClick="common_print_fun();"><span class="glyphicon glyphicon-print"></span> Print</button>
            <button id="btDelete" class="btn btn-danger" name="but_close" value="Delete" onClick="restore_delete_all('2');">Delete</button>
            <button id="btDelete" class="btn btn-danger" name="but_close" value="Cancel" onClick="restore_delete_all('3');">Cancel</button>
            <?php 
            }
            ?>
            <button id="save_butt" class="btn btn-danger" name="but_close" value="Close" onClick="close_me();">Close</button>
        </div>
    </div>
</div>	
    
    
    
<?php
$ArrCreatedBy = getUserDetails($_SESSION['authId']);
$createdBy = strtoupper(substr($ArrCreatedBy['fname'],0,1).substr($ArrCreatedBy['lname'],0,1));
$createdOn = date('m-d-Y H:i A');

$selFacility = 'All';
if($_REQUEST['facility_id']!=''){
	$qryFac="select  name from facility WHERE id = '".$_REQUEST['facility_id']."'";
	$rsFac=imw_query($qryFac);
	$resFac = imw_fetch_row($rsFac);
	$selFacility = $resFac[0];
}

$strCSS= '<style>
				.text_b_w{
					font-size:11px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#000000;
					background-color:#BCD5E1;
					border-style:solid;
					border-color:#FFFFFF;
					border-width: 1px; 
				}
				.tb_heading{
					font-size:11px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#000000;
					background-color:#FE8944;
				}
				.text_b{
					font-size:11px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#FFFFFF;
					background-color:#4684AB;
				}
				.text_10b{
					font-size:11px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					background-color:#FFFFFF;
				}
				.text_b_date{
					font-size:11px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#000000;
					background-color:#F3F3F3;
				}				
				.text{
					font-size:11px;
					font-family:Arial, Helvetica, sans-serif;
					background-color:#FFFFFF;
				}
				.text_10{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					background:#FFFFFF;
				}
				.report_head_text{
					font-size:11px;
					font-family:Arial, Helvetica, sans-serif;
					background-color:#FFFFFF;
					color:#4684ab;
					font-weight:bold;
				}
			</style>
			<page backtop="13mm" backbottom="7mm">';

			$pdfFooter.='<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>';
			
			$pdfHeader.='<page_header>
			<table style="width:100%;" cellpadding="0" cellspacing="0">
				<tr>
					<td width="537" class="text_b_w" align="left">&nbsp;<b>Re-Schedule&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Facilities :&nbsp;</b>'.$selFacility.'</td>
					<td width="537" nowrap="nowrap" class="text_b_w" style="text-align:right;">Created by '.$createdBy.' on '.$createdOn.'</td>
				</tr>
			</table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="25" valign="top" nowrap class="text_b_w">S.No.</td>
					<td width="173" valign="top" nowrap class="text_b_w">Patient Name</td>
					<td width="120" align="center" valign="top" nowrap class="text_b_w">Phone#</td>
					<td width="150" valign="top" nowrap class="text_b_w">Provider</td>
					<td width="80" valign="top" nowrap class="text_b_w">Facility</td>
					<td width="90" valign="top" nowrap class="text_b_w">Procedure</td>
					<td width="150" align="center" valign="top" class="text_b_w">Date - Added to List</td>
					<td width="150" align="center" valign="top" class="text_b_w">Date of Appt</td>
					<td width="80" align="center" valign="top" class="text_b_w">Operator</td>
				</tr>
			</table>	
			</page_header>';			

if($printD==true){
	$printHTML= $strCSS.$pdfFooter.$pdfHeader.$printData;
	
	$filebasepath =data_path().'UserId_'.$_SESSION['authId'].'/tmp/todo/';
	
	if( !is_dir($filebasepath) ){
		mkdir($filebasepath, 0755, true);
		chown($filebasepath, 'apache');
	}
	
	foreach(glob(data_path().'UserId_'.$_SESSION['authId'].'/tmp/todo/'."/*.html") as $html_file_names){
		if($html_file_names){unlink($html_file_names);}
	}
	
	$htmlName = '/todo/todo_'.$_SESSION['authId'].'_'.time().'.html';
	$file_location = write_html($printHTML,$htmlName);
}

?>
<script type="text/javascript">
	function common_print_fun(){
		top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
		html_to_pdf('<?php echo $file_location; ?>','l');//p=portrait,l=landscape
	} 
	<?php
	if($_REQUEST['restored'] || $_REQUEST['not_restored']){
		$msg_show="";
		if($_REQUEST['not_restored']>0){
		$msg_show='\nThe number of appointment(s) not restored: '.$_REQUEST['not_restored'].' due to provider schedule not available';
		}
	 ?>
	top.fAlert('The number of appointment(s) restored: <?php echo $_REQUEST['restored'].$msg_show; ?>');			
	<?php 
	}
	?>	
	$(function(){		
		$('.date-pick').datetimepicker({
			timepicker:false,
			format:'m-d-Y',
			formatDate:'Y-m-d'
		});
	});
</script>
<form name="del_frm" action="del_to_do.php" method="post" >
	<input type="hidden" id="to_do_id" name="to_do_id" value="" />
    <input type="hidden" id="hidAction" name="hidAction" value="" />
    <input type="hidden" id="hidShcId" name="hidShcId" value="" />
    <input type="hidden" id="reason" name="reason" value="" />
</form>
</body>
</html>