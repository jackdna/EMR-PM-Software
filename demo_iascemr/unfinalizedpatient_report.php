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
<title>Unfinalized Patient Report</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
$spec = "
</head>
	<body onLoad=\"MM_preloadImages('images/generate_report_hover.jpg','images/reset_hover.jpg')\">
";

include("common/link_new_file.php");
include_once("no_record.php");
$type=$_REQUEST['chkbox_unfinalized'];
if(strpos($_SERVER['HTTP_REFERER'], 'https') !== false){
	$get_http_path = 'https';
}
elseif(strpos($_SERVER['HTTP_REFERER'], 'http') !== false)
{
	$get_http_path= 'http';
}	
$get_http_path;
include_once("common/user_agent.php");
if($browserName=="IE"){
	$table_row = "block";
}else{
	$table_row = "table-row";
}
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
	var fromdate=document.unfinalizedpatient_report.date1.value;
	var todate=document.unfinalizedpatient_report.date2.value;
	if(document.getElementById("chkbox_detail").value!="" && document.getElementById("chkbox_detail").checked==true){
		var chkbx=document.getElementById("chkbox_detail").value;
	}else if(document.getElementById("chkbox_range").value!="" && document.getElementById("chkbox_range").checked==true){
		var chkbx=document.getElementById("chkbox_range").value;
		if( fromdate == '' || todate == '') {
			alert("Please select daete range"); return false;
		}
	}
	else{
		alert('Please select report type');
		return false;
	}
	flPath = 'unfinalizedpatient_reportpop.php?from_date='+fromdate+'&to_date='+todate+'&chkbox_unfinalized='+chkbx+'&get_http_path='+path+'&hidd_report_format='+report_format;
	if(report_format=='csv') {
		//parent.top.$(".loader").fadeIn('fast').show('fast');
		document.unfinalizedpatient_report.action=flPath;
		document.unfinalizedpatient_report.submit();
	}else {
		var parWidth = parent.document.body.clientWidth;
		var parHeight = parent.document.body.clientHeight;
		window.open(flPath,'unfinalizedpatientchart','width='+parWidth+',height='+parHeight+' top=100,left=100,resizable=yes,scrollbars=1');
		//window.open(flPath,win_name,'width=950,height=600 top=100,left=100,resizable=yes,scrollbars=1');
	}
}
function resetfields(){
  document.unfinalizedpatient_report.chkbox_detail.checked=false;
  document.unfinalizedpatient_report.chkbox_range.checked=false;
  document.unfinalizedpatient_report.date1.value='';
  document.unfinalizedpatient_report.date2.value='';
  $("#date").slideUp('slow');
}
</script>
<form name="unfinalizedpatient_report" action="#" method="post">
	<div class="container-fluid padding_0">
		<div class="inner_surg_middle ">
			<div style="" id="" class="all_content1_slider ">	         
					<div class="wrap_inside_admin">
					 <div class=" subtracting-head">
						<div class="head_scheduler new_head_slider padding_head_adjust_admin">
								  <span>
									  Un-Finalized Patient Report
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
										  <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
											  <div class="form_reg text-center">
													  <label class="date_r">
														  Report Type
													  </label>
											  </div>
										 </div>
										 <div class="clearfix margin_adjustment_only its_line"></div>
										 <div class="clearfix margin_adjustment_only"></div>
										 <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
											  <div class="width_adj_sum">
												  <div class="wrapped_inner_ans_pro">
													  <label for="chkbox_detail">
														  <input type="checkbox" name="chkbox_unfinalized" id="chkbox_detail" <?php if($type=="detail") echo "checked";?> value="detail" tabindex="7" onClick="javascript:checkSingle('chkbox_detail','chkbox_unfinalized');$('#date').slideUp('slow');" /> ALL
													  </label>
												  </div>
												  <div class="wrapped_inner_ans_pro" style="width:140px;">
													  <label for="chkbox_range">
														  <input type="checkbox" name="chkbox_unfinalized" id="chkbox_range" <?php if($type=="range") echo "checked";?> value="range" tabindex="7" onClick="toggle_date_range(this, 'date'); javascript:checkSingle('chkbox_range','chkbox_unfinalized');" /> Select Range
													  </label>
												  </div>
											  </div>
										 </div>
										 
										 <div class="wrap_inside_admin" style="display:<?php if($type=="range"){echo "block";}else{echo 'none';} ?>"id="date">
										   <div class="clearfix margin_adjustment_only"></div>
											  <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
												  <div class="form_reg text-center">
														  <label class="date_r">
															 Select Date
														  </label>
												  </div>
											 </div>	
											 <div class="clearfix margin_adjustment_only its_line"></div>
											 <div class="clearfix margin_adjustment_only"></div>
											<div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
												  <div class="form_reg">
														<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
														  <label for="date1" class="">
															  From			
														  </label>
														</div>
														<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
															  <div id="datetimepicker1" class="input-group">
																  <input type="text" tabindex="1" id="date1" name="date1" value="<?php echo $_REQUEST['from_date'];?>" class="form-control">
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
																  <input type="text" tabindex="1" id="date2" name="date2" value="<?php echo $_REQUEST['to_date'];?>" class="form-control">
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
									  <a href="javascript:void(0)" class="btn btn-info" id="generate_report" onClick="return reportpop('<?php echo $get_http_path;?>','');">
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
if($_REQUEST["no_record"]=="yes") { //IN CASE OF CSV REPORT
?>
	<script>
		modalAlert("No Record Found !");
		var date12 = '<?php echo $_REQUEST["date12"];?>';
		var dType = '<?php echo $_REQUEST["dType"];?>';
		document.getElementById('date1').value=date12;
		if(dType=='summary') {
			document.getElementById(dType).click();
		}
	</script>
<?php
}
?>
				   
</body>
</html>
		
