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
<title>Patient Monitor Report</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="js/jquery.js"></script>
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
$provArr = $jsProvArr = array();
$qry="SELECT usersId,fname,mname,lname,user_type FROM users WHERE deleteStatus!='Yes' AND user_type IN('Surgeon','Anesthesiologist','Nurse') ORDER BY lname";
$res=imw_query($qry) or die(imw_error());
while($row=imw_fetch_array($res))
{
	$provider_id=$row['usersId'];
	$provider_fname= $row['fname'];
	$provider_mname= $row['mname'];
	$provider_lname= $row['lname'];
	$user_type= $row['user_type'];
	$provider_name=stripslashes($provider_lname.",".$provider_fname);
	$provArr[]=$provider_id.'|-|'.$user_type.'|-|'.$provider_name;
}
if(count($provArr)>0) {
	$jsProvArr = json_encode($provArr);	
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
		var provider1="";
		var obj=document.getElementById("provider");
		var objLength = obj.length;
		var a=0;
		for(i=0; i<objLength; i++){
			if(obj[i].selected == true){
				a++;
				if(a==1) {
					provider1 = obj[i].value;
				}else {
					provider1 += ','+obj[i].value;
				}
				if(obj[i].value=="all") {
					provider1 = "all";
					break;	
				}
			}
		}	
		provider1=$("#provider").val();
		var provider_type=document.provider_report.provider_type.value;
		var fromdate=document.provider_report.date1.value;
	    var todate=document.provider_report.date2.value;
		var or_room_number1=document.provider_report.or_room_number.value;
		var flPth = 'patient_monitor_reportpop.php';
		var wndNme = 'pt_monitor';
		var parWidth = parent.document.body.clientWidth;
		var parHeight = parent.document.body.clientHeight;

		window.open(flPth+'?provider_type='+provider_type+'&startdate='+fromdate+'&enddate='+todate+'&provider='+provider1+'&or_room_number='+or_room_number1,wndNme,'width='+parWidth+',height='+parHeight+' top=100,left=100,resizable=yes,scrollbars=1');
	}

function reportpop_csv(){
	var provider1="";
		var obj=document.getElementById("provider");
		var objLength = obj.length;
		var a=0;
		for(i=0; i<objLength; i++){
			if(obj[i].selected == true){
				a++;
				if(a==1) {
					provider1 = obj[i].value;
				}else {
					provider1 += ','+obj[i].value;
				}
				if(obj[i].value=="all") {
					provider1 = "all";
					break;	
				}
			}
		}	
	provider1=$("#provider").val();
	var datestart = $("#date1").val();
	var dateend = $("#date2").val();
	document.provider_report.startdate.value = datestart;
	document.provider_report.enddate.value = dateend;
	document.provider_report.provider.value = provider1;
	document.provider_report.or_room_number.value;
	document.provider_report.hidd_report_format.value='csv';
	document.provider_report.patient_save.value='yes';
	document.provider_report.submit();
	return true;
}

function resetfields()
{
	change_prov_type("");
	
	document.provider_report.provider_type.value = "";
	document.provider_report.date1.value="";
	document.provider_report.date2.value="";
	document.provider_report.or_room_number.value="";
	$('.selectpicker').selectpicker('refresh');
}

function change_prov_type(usrTyp) {
	
	var usrArr = <?php echo $jsProvArr;?>;
	$("#provider").html("");
	var cur_splt=""; 
	var type_options = '<option value="all" data-attending = "0" selected="selected" >All Providers</option>'
	for(var i=0;i<usrArr.length;i++){
		cur_splt=usrArr[i].split("|-|");
		
		if(usrTyp==cur_splt[1] || usrTyp==""){
			type_options += '<option data-attending = "1" value="'+cur_splt[0]+'@@'+cur_splt[1]+'">'+cur_splt[2]+'</option>';
		}
	}
	//document.getElementById("provider").innerHTML = type_options;
	$('#provider').html(type_options);
	$('#provider').selectpicker('refresh');
}
$(document).ready(function(e) {
    change_prov_type("");
});
</script>
<div class="main_wrapper">
	<form name="provider_report" action="patient_monitor_reportpop.php" method="post">	
		<input type="hidden" name="startdate" id="startdate" value="">	
		<input type="hidden" name="enddate" id="enddate" value="">	
		<input type="hidden" name="patient_save" id="patient_save" value="">	
		<input type="hidden" name="hidd_report_format" id="hidd_report_format" value="">	
		<div class="container-fluid padding_0">
        	<div class="inner_surg_middle ">
            
            		<div style="" id="" class="all_content1_slider ">	         
                    
                          <div class="wrap_inside_admin">
                          	<div class=" subtracting-head">
                            	<div class="head_scheduler new_head_slider padding_head_adjust_admin">
                                	<span>Patient Monitor Report </span>
                               	</div>
                          	</div>
                            
                            <Div class="wrap_inside_admin">
                            	
								<div class="col-md-2 visible-md"></div>
                                <div class="col-lg-3 visible-lg"></div>
                                
                                <div class="col-md-8 col-sm-12 col-xs-12 col-lg-6">
                                     
                                    <div class="audit_wrap">
                                    	<div class="form_outer">
                                        	<Div class="row">
                                            	<div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
                                               		<div class="form_reg">
                                                    	
                                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        	<label for="pro_select" class="text-left">Provider Type </label>
                                                      	</div>
                                                        
                                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        	<Select class="selectpicker form-control" id="pro_select" name="provider_type" onChange="change_prov_type(this.value);" tabindex="1"  title="All Provider Types">
                                                            	<option data-attending = "0" value="">All Provider Types</option>
                                                                <option data-attending = "1" value="Surgeon">Surgeon</option>
                                                                <option data-attending = "1" value="Anesthesiologist">Anesthesiologist</option>
                                                                <option data-attending = "1" value="Nurse">Nurse</option>
                                                           	</Select> 
                                                      	</div>
                                                		<!----------------------- Full Inout col-12    ------------------------------>
                                                        
                                                    </div>
                                               	</div>
                                                
                                                <div class="clearfix margin_adjustment_only visible-sm"></div>
                                                <div class="clearfix margin_adjustment_only visible-xs"></div>                                                    
                                                
                                                <div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
                                                	<div class="form_reg">
                                                    	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        	<label for="p_select" class="text-left">Provider</label>
                                                        </div>
                                                        
                                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        	
                                                            <select class="selectpicker form-control" name="provider[]" id="provider" multiple="multiple" tabindex="2" title="No Provider Selected" ></select>
                                                       	</div> <!----------------------- Full Inout col-12    ------------------------------>
                                                  	</div>
                                                 </div>
                                                 
                                              	<div class="clearfix margin_adjustment_only"></div>
                                                
                                                <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
                                                	<div class="form_reg text-center">
                                                    	<label class="date_r">Select Date</label>
                                                  	</div>
                                               	</div>
                                                
                                                <div class="clearfix margin_adjustment_only  its_line"></div>
                                                <div class="clearfix margin_adjustment_only"></div>                                                  
                                                
                                                <div class="col-md-4 col-sm-12 col-xs-12 col-lg-4">
                                                	<div class="form_reg">
                                                    	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        	<label for="from" class="">From</label>
                                                       	</div>	
                                                        
                                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        	<div id="datetimepicker1" class="input-group">
                                                            	<input type="text" class="form-control" tabindex="3" id="date1" name="date1" value="<?php echo date("m-d-Y");?>"/>
                                                                <div class="input-group-addon datepicker">
                                                                	<a href="javascript:void(0)"><span class="glyphicon glyphicon-calendar"></span></a>
                                                               	</div>
                                                           	</div>
                                                       	</div>
                                                   	</div>
                                              	</div>
                                                
                                                <Div class="clearfix margin_adjustment_only visible-sm"></Div>
                                                <Div class="clearfix margin_adjustment_only visible-xs"></Div>
                                                
                                                <div class="col-md-4 col-sm-12 col-xs-12 col-lg-4">
                                                	<div class="form_reg">
                                                    	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        	<label for="to" class="">To</label>
                                                       	</div>
                                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        	<div id="datetimepicker2" class="input-group">
                                                            	<input type="text" class="form-control" tabindex="4" id="date2" name="date2" value="<?php echo date("m-d-Y");?>" />
                                                                <div class="input-group-addon datepicker">
                                                                	<a href="javascript:void(0)"><span class="glyphicon glyphicon-calendar"></span></a>
                                                               	</div>
                                                          	</div>
                                                      	</div>
                                                   	</div>
                                               	</div>
                                                
                                                <Div class="clearfix margin_adjustment_only visible-sm"></Div>
                                                <Div class="clearfix margin_adjustment_only visible-xs"></Div> 
                                                
                                                <div class="col-md-4 col-sm-12 col-xs-12 col-lg-4">
                                                	<div class="form_reg">
                                                    	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        	<label for="Op" class="">Op Room Number</label>
                                                       	</div>
                                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        	<input type="text" class="form-control" tabindex="5" id="or_room_number" name="or_room_number" value=""/>
                                                       	</div>
                                                   	</div>
                                               	</div>
                                                
                                                
                                                <div class="clearfix margin_adjustment_only"></div>
                                                
                                          	</Div>
                                       	</div>
                                 	</div>
                                    
                                    <div class="btn-footer-slider">
                                    	<a href="javascript:void(0)" class="btn btn-info"  id="generate_report" onClick="return reportpop();" >
                                        	<b class="fa fa-edit"></b> Generate Report 
                                       	</a>
                                        <a href="javascript:void(0)" class="btn btn-info" id="generate_csv_report" onClick="return reportpop_csv();">
											<b class="fa fa-download"></b> Export CSV 
										</a>
                                        <a class="btn btn-default" href="javascript:void(0)" id="reset" onClick="return resetfields();">
                                        	<b class="fa fa-refresh"></b> Reset
                                       	</a>
                                   	</div>
                                    
                              	</div>
                            
                            </Div>
							
						</div> 
                  </div>  
                  <!-- NEcessary PUSH     -->	 
                  <Div class="push"></Div>
                  <!-- NEcessary PUSH     -->
            </div>
        </div>
        
  	</form>
</div>

<?php
	if($_REQUEST["no_record"]=="yes") {
?>
	<script>
		modalAlert("No Record Found !");
	</script>
<?php
	}
?>