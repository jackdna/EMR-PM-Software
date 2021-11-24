<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
set_time_limit(0);
session_start();
include("common/conDb.php");
if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
	echo '<script>top.location.href="index.php"</script>';
}
if($_SESSION['iasc_facility_id'])
{
	$fac_con=" and stub_tbl.iasc_facility_id='$_SESSION[iasc_facility_id]'"; 
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Day Surgeon Operative Report</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="css/style_surgery.css" type="text/css" >
<?php
$spec = "
</head>
	<body onLoad=\"MM_preloadImages('images/generate_report_hover.jpg','images/reset_hover.jpg')\">
";
include("common/link_new_file.php");
include_once("no_record.php");
?>
<script >
	var today = new Date();
	var day = today.getDate();
	var month = today.getMonth()
	var year = y2k(today.getYear());
	var mon=month+1;
	if(mon<=9){
		mon='0'+mon;
	}
	var todaydate=mon+'-'+day+'-'+year;
	function y2k(number){
		return (number < 1000)? number+1900 : number;
	}
	function newWindow(q){
		
		mywindow=open('mycal1.php?md='+q,'','width=200,height=250,top=200,left=300');
		mywindow.location.href = 'mycal1.php?md='+q;
		if(mywindow.opener == null)
			mywindow.opener = self;
	}
	function restart(q){
		fillDate = ''+ padout(month - 0 + 1) + '-'  + padout(day) + '-' +  year;
		if(q==8){
			if(fillDate > todaydate){
				modalAlert("Date Of Service can not be a future date")
				return false;
			}
		}
		document.getElementById("date"+q).value=fillDate;
		mywindow.close();
	}
function padout(number){
return (number < 10) ? '0' + number : number;
}
function reportpop(path){
	var date=document.day_surgeon_op_notes_report.date1.value;
	if(date==""){
		modalAlert("Please select the date to proceed generate report."); 
	}
	else{
		document.day_surgeon_op_notes_report.submit();
	}
	//window.open('day_reportpop.php?date12='+ date+'&get_http_path='+path,'','width=650,height=600 top=100,left=100,resizable=yes,scrollbars=1');
	
}
function date12(){ 
	var s_date=document.day_surgeon_op_notes_report.date1.value;
	if(s_date==""){
		modalAlert("Please select the date to proceed generate report."); 
		return false;
	}
}
function resetdate(){
   document.day_surgeon_op_notes_report.date1.value="";
   document.day_surgeon_op_notes_report.surgeon.value="";
   $('.selectpicker').selectpicker('refresh');
}
	
</script>
    <?php 
    $date=$_REQUEST['date1'];
	if(strpos($_SERVER['HTTP_REFERER'], 'https') !== false){
		$get_http_path = 'https';
         }
	elseif(strpos($_SERVER['HTTP_REFERER'], 'http') !== false)
	{
		$get_http_path= 'http';
	}	
	if($date!="") {
		$dat1=explode("-",$date);
		$dat1[0];
		$dat1[1];
		$dat1[2];
		$selected_date = $dat1[2].'-'.$dat1[0].'-'.$dat1[1];
		$surgeon = $_REQUEST['surgeon'];
		if($surgeon==""){
			$query = "select PC.patientConfirmationId from patientconfirmation PC
					   Left Join operativereport OP on PC.patientConfirmationId = OP.confirmation_id
						 Left Join laser_procedure_patient_table LP on PC.patientConfirmationId = LP.confirmation_id
						 Left Join injection INJ on PC.patientConfirmationId = INJ.confirmation_id
						 Left Join stub_tbl ON stub_tbl.patient_confirmation_id=PC.patientConfirmationId
					   Where  PC.dos='$selected_date' 
						 And (
						 		(OP.form_status='completed' or OP.form_status='not completed') OR 
								(LP.form_status='completed' or LP.form_status='not completed') OR
								( (INJ.form_status='completed' or INJ.form_status='not completed') AND prim_proc_is_misc = 'injection' )
							)
						 AND stub_tbl.patient_confirmation_id!='' $fac_con 
					   ORDER BY PC.surgeon_name, PC.surgery_time";
		}
		elseif($surgeon!=""){
			$query = "select PC.patientConfirmationId from patientconfirmation PC
								Left Join operativereport OP on PC.patientConfirmationId = OP.confirmation_id
								Left Join laser_procedure_patient_table LP on PC.patientConfirmationId = LP.confirmation_id
						 		Left Join injection INJ on PC.patientConfirmationId = INJ.confirmation_id
						 		Left Join stub_tbl ON stub_tbl.patient_confirmation_id=PC.patientConfirmationId
					   		Where PC.dos='$selected_date'
								And (
									(OP.form_status='completed' or OP.form_status='not completed') OR 
									(LP.form_status='completed' or LP.form_status='not completed') OR
									( (INJ.form_status='completed' or INJ.form_status='not completed') AND prim_proc_is_misc = 'injection' )
								)
								And PC.surgeonId=$surgeon And stub_tbl.patient_confirmation_id!='' $fac_con
					   		ORDER BY PC.surgeon_name, PC.surgery_time";
		} 
		$queryRs = imw_query($query);
		while ($row = imw_fetch_array($queryRs)){
			$patientConfirmationId .= $row['patientConfirmationId'].',';
			
		}
		if($patientConfirmationId){
			?>
			<form name="day_surgeon_op_notes_report_sub" id="day_surgeon_op_notes_report_sub" method="post" action="day_surgeon_op_notes_report_print.php" target="day_surgeon_op_notes_win" >
				<input type="hidden" name="patientConfirmationId" value="<?php echo $patientConfirmationId;?>">
				<input type="hidden" name="get_http_path" value="<?php echo $get_http_path;?>">
			</form>
			
			<script>
				var parWidth = parent.document.body.clientWidth;
				var parHeight = parent.document.body.clientHeight;
				window.open('day_surgeon_op_notes_report_print.php','day_surgeon_op_notes_win','width='+parWidth+',height='+parHeight+' top=100,left=100,resizable=yes,scrollbars=1');
				document.getElementById('day_surgeon_op_notes_report_sub').submit();
				
			</script>
			<?php
		}
		else{
			?>
			<script>
				modalAlert('No Data Found To Generate Report')
			</script>
			<?php
		}
	}
	
   ?>

<form name="day_surgeon_op_notes_report" action="day_surgeon_op_notes_report.php" method="post" > 
<div class="container-fluid padding_0">
   <div class="inner_surg_middle ">
	  <div style="" id="" class="all_content1_slider ">	         
		 <div class="wrap_inside_admin">
			<div class=" subtracting-head">
			   <div class="head_scheduler new_head_slider padding_head_adjust_admin">
				  <span>
					 Day Surgeon OP Notes Report
				  </span>
			   </div>
			</div>   
			<Div class="wrap_inside_admin ">
			   <div class="col-md-2 visible-md"></div>
			   <div class="col-lg-3 visible-lg"></div>
				  <div class="col-md-8 col-sm-12 col-xs-12 col-lg-6">
					 <div class="audit_wrap">
						<div class="form_outer">
						   <!----------------------- Full Inout col-12    ------------------------------>
						   <div class="clearfix margin_adjustment_only"></div>
						   <div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
							  <div class="form_reg">
								 <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
								   <label class="" for="date1">
									   Date			
								   </label>
								 </div>
								 <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
									<div class="input-group" id="datetimepicker1">
									   <input autocomplete="off" type="text" class="form-control" tabindex="1" id="date1" name="date1" value="<?php echo $date;?>" />
									   <div class="input-group-addon datepicker">
										  <a href="javascript:void(0)"><span class="glyphicon glyphicon-calendar"></span></a>
									   </div>
									</div>
								 </div> <!----------------------- Full Inout col-12    ------------------------------>
							   </div>
						   </div>
						   <div class="clearfix margin_adjustment_only visible-sm"></div>
						   <div class="clearfix margin_adjustment_only visible-xs"></div>
						   <div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
							  <div class="form_reg">
								 <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
								   <label class="" for="surgeon">
									   Surgeon
								   </label>
								 </div>
								 <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
									<Select class="form-control selectpicker" name="surgeon" id="surgeon" title="Select Surgeons" data-size="10">
									   <option value="" selected="selected"> All Surgeon</option>
									   <?php 
										  $physician=imw_query("select * from users where user_type='Surgeon' and deleteStatus!='Yes' order by lname");
										  while( $physician1=imw_fetch_array($physician))
										  {
											$physician_id=$physician1['usersId'];
											$physician_fname= $physician1['fname'];
											$physician_mname= $physician1['mname'];
											$physician_lname= $physician1['lname'];
											$physician_name=stripslashes($physician_lname.",".$physician_fname);
									   ?>
										  <option value="<?php echo $physician_id;?>"><?php echo $physician_name;?></option>
									   <?php 
										  }
									   ?>
									</Select>
								 </div> <!----------------------- Full Inout col-12    ------------------------------>
							  </div>
						   </div>
						</div>
					 </div>
					 <div class="btn-footer-slider">
						<a href="javascript:void(0)" class="btn btn-info" id="generate_report" onClick="return reportpop('<?php echo $get_http_path;?>');">
						   <b class="fa fa-edit"></b> Generate Report 
						</a>
						<a id="reset" onClick="return resetdate();" class="btn btn-default" href="javascript:void(0)">
						   <b class="fa fa-refresh"></b>	Reset
						</a>
					 </div>
				  </div>
			</div>		
		 </Div>
	  </div> 
   </div>  
   <!-- NEcessary PUSH     -->	 
   <Div class="push"></Div>
   <!-- NEcessary PUSH     -->
</div>
</form>					   

		
