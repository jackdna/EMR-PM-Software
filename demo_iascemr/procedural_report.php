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
<title>Procedural Report</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
	$spec = "
	</head>
	<body onLoad=\"MM_preloadImages('images/generate_report_hover.jpg','images/reset_hover.jpg')\">
	";

include("common/link_new_file.php");
if(strpos($_SERVER['HTTP_REFERER'], 'https') !== false){
	$get_http_path = 'https';
}
elseif(strpos($_SERVER['HTTP_REFERER'], 'http') !== false)
{
	$get_http_path= 'http';
}									
$currDy 	= date('d');
$currMnth 	= date('m');
$currYr 	= date('Y');	
$dt_to 		= date('m-d-Y');
//$dt_from 	= date('m-d-Y',mktime(0,0,0,$currMnth,1,$currYr));								
$dt_from 	= $dt_to;

if($_REQUEST["startdate"]) {
	$dt_from 	= $_REQUEST["startdate"];
}
if($_REQUEST["enddate"]) {
	$dt_to 	= $_REQUEST["enddate"];
}
?>
<script >
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
function reportpop(path,report_format){
	 var fromdate=document.procedural_report.date1.value;
	 var todate=document.procedural_report.date2.value;
	 var proc=document.procedural_report.procedure.value;
	 var apptStatus=document.procedural_report.showAllApptStatus.value;
	 var flPath = 'procedural_reportpop.php?procedure='+proc+'&showAllApptStatus='+apptStatus+'&startdate='+fromdate+'&enddate='+todate+'&get_http_path='+path+'&hidd_report_format='+report_format;
	 if(fromdate=='' || todate=='') {
		alert('Please select Date(From-To)');
	 }else if(report_format=='csv') {
		//parent.top.$(".loader").fadeIn('fast').show('fast');
		document.procedural_report.action=flPath;
		document.procedural_report.submit();
	}else {
		var parWidth = parent.document.body.clientWidth;
		var parHeight = parent.document.body.clientHeight;
		window.open(flPath,'mm','width='+parWidth+',height='+parHeight+' top=100,left=100,resizable=yes,scrollbars=1');
	}
	
}

function resetfields(){
   document.procedural_report.procedure.value='All';
   document.procedural_report.showAllApptStatus.value='Active';
   document.procedural_report.date1.value='';
   document.procedural_report.date2.value='';
   $('.selectpicker').selectpicker('refresh');
}
</script>
	
<form name="procedural_report" action="#" method="post" >	
	<div class="container-fluid padding_0">
		<div class="inner_surg_middle ">
		  <div style="" id="" class="all_content1_slider ">	         
				  <div class="wrap_inside_admin">
				   <div class=" subtracting-head">
					  <div class="head_scheduler new_head_slider padding_head_adjust_admin">
								<span>
									Procedural Report
								</span>
					  </div>
				   </div>   
				  <Div class="wrap_inside_admin ">
						<div class="col-md-2 visible-md"></div>
						<div class="col-lg-3 visible-lg"></div>
						<div class="col-md-8 col-sm-12 col-xs-12 col-lg-6">
							 <div class="audit_wrap">
								   <div class="form_outer">
										<div class="row">
											<div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
												<div class="form_reg">
													  <div class="col-md-6 col-lg-6 col-xs-6 col-sm-6">
															<label class="text-left" for="procedure"> 
																  Procedure	
															</label>
														</div>
														<div class="col-md-6 col-lg-6 col-xs-6 col-sm-6">
															<label class="text-left" for="procedure"> 
																  Status	
															</label>
														</div>
														<div class="col-md-6 col-lg-6 col-xs-6 col-sm-6">
                                                            <select class="selectpicker form-control" name="procedure" id="procedure" data-title="Select Procedure" data-size="10">
                                                                <option value="All" selected="selected">All Procedures</option>
                                                                    <?php 
                                                                    
                                                                     $proc=imw_query("select * from procedures where del_status='' order by name");
                                                                      while( $procedure1=imw_fetch_array($proc))
                                                                      {
                                                                        $procedure_id=$procedure1['procedureId'];
                                                                        $procedure_name= stripslashes($procedure1['name']);
                                                                ?>
                                    
                                                                    <option value="<?php echo $procedure_name;?>" <?php if($_REQUEST["procedure"]==$procedure_name) { echo "selected"; }?>><?php echo $procedure_name;?></option>
                                                                <?php 
                                                                      }
                                                                                    
                                                                ?>                                                                           
                                                            </select>
														</div>
                                                        <div class="col-md-6 col-lg-6 col-xs-6 col-sm-6">
                                                            <select class="selectpicker form-control" name="showAllApptStatus" id="showAllApptStatus" data-size="10">
                                                                <option value="All" 	 <?php if($_REQUEST["showAllApptStatus"]=="All") 		{ echo "selected"; }?> >All </option>
                                                                <option value="Active" 	 <?php if(!$_REQUEST["showAllApptStatus"] || $_REQUEST["showAllApptStatus"]=="Active") 	{ echo "selected"; }?> >Active </option>
                                                                <option value="Canceled" <?php if($_REQUEST["showAllApptStatus"]=="Canceled") 	{ echo "selected"; }?> >Cancelled </option>
                                                                <option value="No Show"  <?php if($_REQUEST["showAllApptStatus"]=="No Show") 	{ echo "selected"; }?> >No Show </option>
                                                                <option value="Aborted Surgery"  <?php if($_REQUEST["showAllApptStatus"]=="Aborted Surgery") 	{ echo "selected"; }?> >Aborted Surgery </option>
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
										  <div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
												<div class="form_reg">
													  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
														<label for="date1" class="">
															From			
														</label>
													  </div>
													  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
															<div id="datetimepicker1" class="input-group">
																<input type="text" class="form-control" tabindex="1" id="date1" name="date1" value="<?php echo $dt_from;?>" />
																<div class="input-group-addon datepicker">
																	<a href="javascript:void(0)"><span class="glyphicon glyphicon-calendar"></span></a>
																</div>
															</div>
													  </div> <!----------------------- Full Inout col-12    ------------------------------>
												</div>	
										   </div>
										   <div class="clearfix margin_adjustment_only visible-sm"></div>
										   <div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
												<div class="form_reg">
													  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
														<label for="date2" class="">
															To
														</label>
													  </div>
													  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
															<div id="datetimepicker2" class="input-group">
																<input type="text" class="form-control" id="date2" name="date2"  value="<?php echo $dt_to;?>">
																<div class="input-group-addon datepicker">
																	<a href="javascript:void(0)"><span class="glyphicon glyphicon-calendar"></span></a>
																</div>
															</div>
													  </div> <!----------------------- Full Inout col-12    ------------------------------>
												</div>	
										   </div>
										   
											
										</div>	  
									</div>
							  </div>
							  <div class="btn-footer-slider">
									<a id="generate_report" onClick="return reportpop('<?php echo $get_http_path;?>','');" class="btn btn-info" href="javascript:void(0)">
										<b class="fa fa-edit"></b> Generate Report 
									</a>
                                    <a href="javascript:void(0)" class="btn btn-info" id="generate_csv_report" onClick="return reportpop('<?php echo $get_http_path; ?>','csv');">
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
include_once("no_record.php");
if($_REQUEST["no_record"]=="yes") { //IN CASE OF CSV REPORT
?>
	<script>
		modalAlert("No Record Found !");
    </script>
<?php
}
?>
</body>
</html>
		
