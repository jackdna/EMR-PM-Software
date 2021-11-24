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
<title>Supply Used Report</title>
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
function reportpop(path,report_format){
	var date=document.supply_used_report.date1.value;
	
	if(date==""){
		modalAlert("Please select the date to generate report."); 
	}else if(document.getElementById('summary').checked==false && document.getElementById('detail').checked==false) {
		modalAlert("Please Select Report Type");
	}else{
		
		document.getElementById("hidd_report_format").value = "";
		if(report_format == 'csv') {
			document.getElementById("hidd_report_format").value = "csv";
		}
		document.supply_used_report.action = 'supply_used_report.php';
		document.supply_used_report.submit();
	}
	//window.open('day_reportpop.php?date12='+ date+'&get_http_path='+path,'','width=650,height=600 top=100,left=100,resizable=yes,scrollbars=1');
	
}
function date12(){ 
	var s_date=document.supply_used_report.date1.value;
	if(s_date==""){
		modalAlert("Please select the date to proceed generate report."); 
		return false;
	}
}

function resetfields(){
	document.supply_used_report.surgeon.value='';
	document.supply_used_report.date1.value='';
	document.supply_used_report.date2.value='';
	document.supply_used_report.summary.checked=false;
	document.supply_used_report.detail.checked=false;
   $('.selectpicker').selectpicker('refresh');
}

var hidd_report_format_new = '';
var flPath = '';
</script>
    <?php 
    $date=trim($_REQUEST['date1']);
	$date2=trim($_REQUEST['date2']);
	
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
		$selected_date2 = $selected_date;
		if($date2!="") { 
			list($monthDate2,$dayDate2,$yearDate2)=explode("-",$date2);
			$selected_date2 = $yearDate2.'-'.$monthDate2.'-'.$dayDate2;
		}
		
		$surgeon = $_REQUEST['surgeon'];
		if($surgeon==""){
			$query = "select pc.patientConfirmationId from patientconfirmation pc  
					   INNER join operatingroomrecords opr on opr.confirmation_id = pc.patientConfirmationId 
					   AND (opr.manufacture!='' OR opr.model!='' OR opr.lensBrand!='' OR opr.Diopter!='')
					   WHERE  ( pc.dos between '$selected_date' and '$selected_date2' ) and (opr.form_status='completed' or
					   opr.form_status='not completed')
					   ORDER BY pc.surgeon_name, pc.surgery_time
					   ";
		}
		elseif($surgeon!=""){
			$query = "select pc.patientConfirmationId from patientconfirmation pc  
					   INNER join operatingroomrecords opr on
					   pc.patientConfirmationId = opr.confirmation_id
					   AND (opr.manufacture!='' OR opr.model!='' OR opr.lensBrand!='' OR opr.Diopter!='')
					   WHERE  ( pc.dos between '$selected_date' and '$selected_date2' ) and (opr.form_status='completed' or
					   opr.form_status='not completed') and pc.surgeonId in($surgeon)
					   ORDER BY pc.surgeon_name, pc.surgery_time
					   ";
		}
		
		$queryRs = imw_query($query);
		$queryCnt	=	imw_num_rows($queryRs);
		$counter	=	0;
		while ($row = imw_fetch_array($queryRs)){
			$counter++;
			$patientConfirmationId .= $row['patientConfirmationId'];
			$patientConfirmationId .= ($counter < $queryCnt) ? ',' : '' ;	
		}
		
		if($patientConfirmationId){
			$pgNme = 'supply_used_report_print_summary.php';
			$winNme = 'supply_used_report_summary_wind';
			if($_REQUEST['report_typechkbx']=="detail") {
				$pgNme = 'supply_used_report_print_detail.php';	
				$winNme = 'supply_used_report_detail_wind';
			}
			if($_REQUEST["hidd_report_format"]=="csv") {
				$winNme = 	"_self";
			}
			?>
			<form name="supply_used_report_sub" id="supply_used_report_sub" method="post" action="<?php echo $pgNme;?>" target="<?php echo $winNme;?>" >
				<input type="hidden" name="patientConfirmationId" value="<?php echo $patientConfirmationId;?>">
				<input type="hidden" name="startdate" value="<?php echo $selected_date;?>">
				<input type="hidden" name="enddate" value="<?php echo $selected_date2;?>">
				<input type="hidden" name="get_http_path" value="<?php echo $get_http_path;?>">
				<input type="hidden" name="hidd_report_format" value="<?php echo $_REQUEST["hidd_report_format"];?>">
			</form>
			
			<script>
				var parWidth = parent.document.body.clientWidth;
				var parHeight = parent.document.body.clientHeight;
				var winNme = '<?php echo $winNme;?>';
				hidd_report_format_new = '<?php echo $_REQUEST["hidd_report_format"];?>';
				if(hidd_report_format_new == 'csv') {
					//DO NOTHING
				}else {
					window.open('<?php echo $pgNme;?>',winNme,'width='+parWidth+',height='+parHeight+' top=100,left=100,resizable=yes,scrollbars=1');
					document.getElementById('supply_used_report_sub').submit();
					
				}
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

<form name="supply_used_report" action="supply_used_report.php" method="post" > 
	<input type="hidden" name="hidd_report_format" id="hidd_report_format" value="<?php echo $_REQUEST["hidd_report_format"];?>">
<div class="container-fluid padding_0">
   <div class="inner_surg_middle ">
		 <div style="" id="" class="all_content1_slider ">	         
			   <div class="wrap_inside_admin">
			   <div class=" subtracting-head">
			   <div class="head_scheduler new_head_slider padding_head_adjust_admin">
				  <span>
					Supply Used Report
				  </span>
			   </div>
			 </div>   
			   <Div class="wrap_inside_admin ">
					 <div class="col-md-2 visible-md"></div>
					 <div class="col-lg-3 visible-lg"></div>
					 <div class="col-md-8 col-sm-12 col-xs-12 col-lg-6">
						  <div class="audit_wrap">
								<div class="form_outer">
									 <Div class="row">
										 <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
											 <div class="form_reg">
												   <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
														 <label for="surgeon" class="text-left"> 
															Surgeon
														 </label>
													 </div>
													 <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
														 <Select class="selectpicker form-control" name="surgeon" id="surgeon" data-size="10" data-header="All Surgeons" title="All Surgeon">
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
														
																  <option value="<?php echo $physician_id;?>" <?php if($_REQUEST["surgeon"]==$physician_id) { echo "selected"; }?>><?php echo $physician_name;?></option>
															  <?php 
															   }
															  ?>
														 </Select> 
													  </div> <!----------------------- Full Inout col-12    ------------------------------>
											 </div>
										 </div>
										 <div class="clearfix margin_adjustment_only"></div>
										 <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
											 <div class="form_reg text-center">
													 <label class="date_r">
														 Select Date
													 </label>
											 </div>
										</div>     
										<div class="clearfix margin_adjustment_only  its_line"></div>
										<div class="clearfix margin_adjustment_only"></div>                                                  
										<div class="clearfix margin_adjustment_only visible-sm"></div>
										<div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
											 <div class="form_reg">
											  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
													 <label for="date1" class="">
														 From			
													 </label>
											   </div>	
											  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
													 <div id="datetimepicker1" class="input-group">
                                                     	<input type="text" tabindex="1" id="date1" name="date1" value="<?php echo $_REQUEST['date1'];?>" class="form-control">
														 <div class="input-group-addon datepicker">
															 <a href="javascript:void(0)"><span class="glyphicon glyphicon-calendar"></span></a>
														 </div>
													 </div>
											   </div>
											   
											 </div>
										</div>
									   <Div class="clearfix margin_adjustment_only visible-sm"></Div>
									   <Div class="clearfix margin_adjustment_only visible-xs"></Div>
									   <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
											 <div class="form_reg">
											  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
													 <label for="date2" class="">
														 To			
													 </label>
											   </div>	
											  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
													 <div id="datetimepicker2" class="input-group">
                                                    	<input type="text" tabindex="1" id="date2" name="date2" value="<?php echo $_REQUEST['date2'];?>" class="form-control">
														 <div class="input-group-addon datepicker">
															 <a href="javascript:void(0)"><span class="glyphicon glyphicon-calendar"></span></a>
														 </div>
													 </div>
											   </div>
											   
											 </div>
										</div>
									   <div class="clearfix margin_adjustment_only"></div>
										 <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
											 <div class="form_reg text-center">
													 <label class="date_r">
														 Report Type
													 </label>
											 </div>
										</div>
									   <div class="clearfix margin_adjustment_only  its_line"></div>
										<Div class="clearfix margin_adjustment_only"></Div>
										 <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
											 <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 text-center">
												 <div class="label_inline_adj">
													 <div class="wrapped_inner_ans_pro" style="float:none;">
														 <label for="summary">
															<input type="checkbox" name="report_typechkbx" id="summary" value="summary" tabindex="7" <?php if(!$_REQUEST["report_typechkbx"] || $_REQUEST["report_typechkbx"]=="summary") { echo "checked"; }?> onClick="javascript:checkSingle('summary','report_typechkbx');" /> Summary
														 </label>
													 </div>
													 <div class="wrapped_inner_ans_pro" style="float:none;">
														 <label for="detail">
															<input type="checkbox" name="report_typechkbx" id="detail" value="detail" tabindex="7" <?php if($_REQUEST["report_typechkbx"]=="detail") { echo "checked"; }?> onClick="javascript:checkSingle('detail','report_typechkbx');" /> Details
														 </label>
													 </div>
												 </div>
											 </div>	 	
										 </div>	
									 </Div>	
								</div>
						   </div>
						   <div class="btn-footer-slider">
								<a href="javascript:void(0)" class="btn btn-info" id="generate_report" onClick="return reportpop('<?php echo $get_http_path;?>');">
									 <b class="fa fa-edit"></b> Generate Report 
								 </a>
                                 <a href="javascript:void(0)" class="btn btn-info" id="generate_csv_report" onClick="return reportpop('<?php echo $get_http_path; ?>','csv');">
                                     <b class="fa fa-download"></b> Export CSV 
                                 </a>
								 <a id="reset" onClick="return resetfields();" class="btn btn-default" href="javascript:void(0)">
									<b class="fa fa-refresh"></b> Reset
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

<script>
$(document).ready(function() {
	if(hidd_report_format_new == 'csv') {
		if(document.getElementById('supply_used_report_sub')) {
			document.getElementById('supply_used_report_sub').submit();	
		}
	}
});
</script>
		
