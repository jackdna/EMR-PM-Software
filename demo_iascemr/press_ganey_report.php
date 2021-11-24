<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include("common/conDb.php");
if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
	echo '<script>top.location.href="index.php"</script>';
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Press Ganey Report</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
$spec = "
</head>
<body onLoad=\"MM_preloadImages('images/generate_report_hover.jpg','images/reset_hover.jpg')\">
";

include("common/link_new_file.php");
include_once("no_record.php");
if(strpos($_SERVER['HTTP_REFERER'], 'https') !== false){
	$get_http_path = 'https';
	 }
elseif(strpos($_SERVER['HTTP_REFERER'], 'http') !== false)
{
	$get_http_path= 'http';
}									

?>							
<script>
var today = new Date();
var day = today.getDate();
var month = today.getMonth()
var year = y2k(today.getFullYear());
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
			alert("Date Of Service can not be a future date")
			return false;
		}
	}
	document.getElementById("date"+q).value=fillDate;
	mywindow.close();
}
function padout(number){
return (number < 10) ? '0' + number : number;
}
	
function reportpop_csv(){
	var datestart = $("#date1").val();
	var dateend = $("#date2").val();
	document.press_ganey_report.startdate.value = datestart;
	document.press_ganey_report.enddate.value = dateend;
	var flPth = 'press_ganey_report_pop.php';
	var wndNme = 'phy_rep';

	document.press_ganey_report.phy_save.value='yes';
	document.press_ganey_report.action=flPth;
//	$("physician_report").attr("action",flPth);

	document.press_ganey_report.submit();
	return true;			
}

function resetfields() {
 document.press_ganey_report.date1.value="";
 document.press_ganey_report.date2.value="";
}

</script>

<form name="press_ganey_report" action="press_ganey_report_pop.php" method="post" >
<input type="hidden" name="startdate" id="startdate" value="">	
<input type="hidden" name="enddate" id="enddate" value="">	
<!--<input type="hidden" name="physician" id="physician" value="">	-->
<input type="hidden" name="phy_save" id="phy_save" value="">	
	<div class="container-fluid padding_0">
	  <div class="inner_surg_middle ">
		 <div style="" id="" class="all_content1_slider ">	         
				 <div class="wrap_inside_admin">
				  <div class=" subtracting-head">
					 <div class="head_scheduler new_head_slider padding_head_adjust_admin">
						<span>
						   Press Ganey Report
						</span>
					 </div>
				  </div>   
				 <Div class="wrap_inside_admin">
					   <div class="col-md-2 visible-md"></div>
					   <div class="col-lg-3 visible-lg"></div>
					   <div class="col-md-8 col-sm-12 col-xs-12 col-lg-6">
							<div class="audit_wrap">
								  <div class="form_outer">
									   <div class="row">
										   <div class="clearfix margin_adjustment_only"></div>
										   <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
											   <div class="form_reg text-center">
													   <label class="date_r text-center">
															Select Appointment Date
													   </label>
											   </div>
										  </div>
										 <div class="clearfix margin_adjustment_only  its_line"></div>
										 <div class="col-md-4 col-sm-12 col-lg-6 col-xs-12">
											   <div class="form_reg">
													 <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
													   <label for="date1" class="">
														   From			
													   </label>
													 </div>
													 <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
														   <div id="datetimepicker1" class="input-group">
															   <input type="text" id="date1" name="date1" value="" class="form-control" tabindex="1" />
															   <div class="input-group-addon datepicker">
																   <a href="javascript:void(0)"><span class="glyphicon glyphicon-calendar"></span></a>
															   </div>
														   </div>
													 </div> <!----------------------- Full Inout col-12    ------------------------------>
											   </div>	
										  </div>
										  <div class="clearfix margin_adjustment_only visible-sm"></div>
										  <div class="col-md-4 col-sm-12 col-lg-6 col-xs-12">
											   <div class="form_reg">
													 <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
													   <label for="date2" class="">
														   To
													   </label>
													 </div>
													 <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
														   <div id="datetimepicker2" class="input-group">
															   <input type="text" tabindex="1" id="date2" name="date2" value="" class="form-control">
															   <div class="input-group-addon datepicker">
																   <a href="javascript:void(0)"><span class="glyphicon glyphicon-calendar"></span></a>
															   </div>
														   </div>
													 </div> <!----------------------- Full Inout col-12    ------------------------------>
											   </div>	
										  </div>
										  <div class="clearfix margin_adjustment_only visible-sm"></div>
									   </div>	  
								   </div>
							 </div>
							 <div class="btn-footer-slider">
								   <a href="javascript:void(0)" class="btn btn-info" id="generate_csv_report" onClick="return reportpop_csv();">
									<b class="fa fa-download"></b> Export CSV 
								</a>
								   <a id="reset" onClick="return resetfields();" class="btn btn-default" href="javascript:void(0)">
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

<?php
if($_REQUEST["no_record"]=="yes") {
?>
	<script>
		modalAlert("No Record Found !");
	</script>
<?php
}
?>					   

		
