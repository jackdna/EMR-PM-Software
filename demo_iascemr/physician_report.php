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
<title>Physician Report</title>
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
function reportpop(){
		//var physician1=document.physician_report.physician.value;
		var physician1="";
		var obj=document.getElementById("physician");
		var objLength = obj.length;
		var a=0;
		for(i=0; i<objLength; i++){
			if(obj[i].selected == true){
				a++;
				if(a==1) {
					physician1 = obj[i].value;
				}else {
					physician1 += ','+obj[i].value;
				}
				if(obj[i].value=="all") {
					physician1 = "all";
					break;	
				}
			}
		}
		var procedure1=document.physician_report.procedure.value;
		var fromdate=document.physician_report.date1.value;
	    var todate=document.physician_report.date2.value;
		var surgeryTimeChk=document.physician_report.surgeryTime.checked;
		var physician_orders1=document.physician_report.physician_orders.value;
		var flPth = 'physician_reportpop.php';
		var wndNme = 'phy_rep';
		if(surgeryTimeChk==true) { flPth = 'physician_reportpop_surgerytime.php'; wndNme = 'phy_rep_time';}
			var parWidth = parent.document.body.clientWidth;
			var parHeight = parent.document.body.clientHeight;
			window.open(flPth+'?hidd_report_format=pdf&procedure='+ procedure1+'&startdate='+fromdate+'&enddate='+todate+'&physician='+physician1+'&physician_orders='+physician_orders1,wndNme,'width='+parWidth+',height='+parHeight+' top=100,left=100,resizable=yes,scrollbars=1');
		
	}
	
function reportpop_csv(){
	var physician1="";
		var obj=document.getElementById("physician");
		var objLength = obj.length;
		var a=0;
		for(i=0; i<objLength; i++){
			if(obj[i].selected == true){
				a++;
				if(a==1) {
					physician1 = obj[i].value;
				}else {
					physician1 += ','+obj[i].value;
				}
				if(obj[i].value=="all") {
					physician1 = "all";
					break;	
				}
			}
		}
	
	document.physician_report.procedure.value;
	var datestart = $("#date1").val();
	var dateend = $("#date2").val();
	document.physician_report.startdate.value = datestart;
	document.physician_report.enddate.value = dateend;
	document.physician_report.physician.value = physician1;
	document.physician_report.surgeryTime.checked;
	document.physician_report.physician_orders.value;
	var surgeryTimeChk=document.physician_report.surgeryTime.checked;
	var flPth = 'physician_reportpop.php?hidd_report_format=csv';
	var wndNme = 'phy_rep';
	if(surgeryTimeChk==true) { flPth = 'physician_reportpop_surgerytime.php?hidd_report_format=csv'; wndNme = 'phy_rep_time';}
	document.physician_report.phy_save.value='yes';
	document.physician_report.action=flPth;
//	$("physician_report").attr("action",flPth);
	document.physician_report.submit();
	return true;			
}
function resetfields() {
 document.physician_report.procedure.value="";
 document.physician_report.physician.value="";
 document.physician_report.date1.value="";
 document.physician_report.date2.value="";
 document.physician_report.surgeryTime.value="";
 
 var obj=document.getElementById("physician");
 var objLength = obj.length;
 for(i=0; i<objLength; i++){
	obj[i].selected = false;
 }
 
 $('.selectpicker').selectpicker('refresh');
 document.physician_report.surgeryTime.checked=false;
 document.physician_report.physician_orders.value="";
 
}

</script>

<form name="physician_report" action="physician_reportpop.php" method="post" >
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
						   Physician Report
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
										   <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
											   <div class="form_reg">
													 <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
														   <label class="text-left" for="procedure"> 
																 Procedure	
														   </label>
													   </div>
													   <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
															   <select class="selectpicker form-control" id="procedure" name="procedure" title="Select Procedure" data-size="10">
																  
																  <option value="All" selected="selected">All Procedures</option>
																  <?php 
																
																	  $proc=imw_query("select * from procedures order by name");
																	   while( $procedure1=imw_fetch_array($proc))
																	   {
																		 $procedure_id=$procedure1['procedureId'];
																		 $procedure_name= stripslashes($procedure1['name']);
																  ?>
																		<option value="<?php echo $procedure_name;?>"><?php echo $procedure_name;?></option>
																  <?php 
																		}
																  ?>	                                                                           
															   </select>
													   </div> <!----------------------- Full Inout col-12    ------------------------------>
											   </div>
										   </div>
										   <div class="clearfix margin_adjustment_only"></div>
										   <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
											   <div class="form_reg">
													 <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
														   <label class="text-left" for="physician"> 
																 Physician
														   </label>
													   </div>
													   <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
															 <select name="physician[]" class="selectpicker form-control" multiple="multiple" id="physician" data-size="10" title="Select Physicians" >
																  <option value="all" selected="selected" data-attending="0">All Physician</option>
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
														 
																		   <option data-attending="1" value="<?php echo $physician_id;?>"><?php echo $physician_name;?></option>
																	 <?php 
																		}
																	 ?>                                                                          
															 </select>
													   </div> <!----------------------- Full Inout col-12    ------------------------------>
											   </div>
										   </div>
										   <div class="clearfix margin_adjustment_only"></div>
										   <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
											   <div class="form_reg text-center">
													   <label class="date_r text-center">
															Select Date
													   </label>
											   </div>
										  </div>
										 <div class="clearfix margin_adjustment_only  its_line"></div>
										 <div class="col-md-4 col-sm-12 col-lg-4 col-xs-12">
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
										  <div class="col-md-4 col-sm-12 col-lg-4 col-xs-12">
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
										  <div class="col-md-4 col-sm-12 col-lg-4 col-xs-12">
											   <div class="wrapped_inner_ans_pro">
												  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">	
												   <label for="surgeryTime"><br />
													  
												   </label>
												  </div> 
                                                  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
														   <div id="selectbox" class="input-group">
															   <input type="checkbox" id="surgeryTime" name="surgeryTime" value="1" /><b style="margin-left:10px;">Surgery Time</b>
														   </div>
													 </div>
											   </div>	
										  </div>
										  <div class="clearfix margin_adjustment_only"></div>
										  <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
											   <div class="form_reg">
													 <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
														   <label class="text-left" for="physician_orders"> 
																 Physician Orders
														   </label>
													  </div>
													  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
														 <input class="form-control" type="text" id="physician_orders" name="physician_orders" value=""/>
													  </div> <!----------------------- Full Inout col-12    ------------------------------>
											   </div>
										   </div>
									   </div>	  
								   </div>
							 </div>
							 <div class="btn-footer-slider">
								   <a href="javascript:void(0)" class="btn btn-info" id="generate_report" onClick="return reportpop();">
									   <b class="fa fa-edit"></b> Generate Report 
								   </a>
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

		
