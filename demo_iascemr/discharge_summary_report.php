<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<!DOCTYPE html>
<html>
<head>
<title>Discharge Summary Report</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="js/jquery.js"></script>
<?php 
	$spec = "
	</head>
	<body onLoad=\"MM_preloadImages('images/reset_hover.jpg','images/generate_report_hover.jpg')\">
	";
   include("common/link_new_file.php");
   $type= isset($_REQUEST['report_typechkbx']) ? $_REQUEST['report_typechkbx'] : "";
   $date = date('m-d-Y');
   if(strpos($_SERVER['HTTP_REFERER'], 'https') !== false){
		$get_http_path = 'https';
   }
	elseif(strpos($_SERVER['HTTP_REFERER'], 'http') !== false)
	{
		$get_http_path= 'http';
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

        var date=document.discharge_report.date1.value;
		var parWidth = parent.document.body.clientWidth;
		var parHeight = parent.document.body.clientHeight;

		if( !date )	 {
			alert("Please select date");
			return false;
		}

        if(document.getElementById("summary")!="" && document.getElementById("summary").checked==true)
		{
			var chkbox= document.getElementById("summary").value;
			var flPath = 'discharge_summary_reportpop.php?date12='+ date+'&get_http_path='+path+'&hidd_report_format='+report_format;
			if(report_format=='csv') {
				//parent.top.$(".loader").fadeIn('fast').show('fast');
				document.discharge_report.action=flPath;
				document.discharge_report.submit();
			}else {
				window.open('discharge_summary_reportpop.php?date12='+date+'&get_http_path='+path,'','width='+parWidth+',height='+parHeight+' top=100,left=100,resizable=yes,scrollbars=1');
			}
		}
		else if(document.getElementById("detail")!="" && document.getElementById("detail").checked==true)
	 	{
			 var chkbox= document.getElementById("detail").value;
			// window.open('discharge_detail_reportpop.php?date12='+date,'','width='+parWidth+',height='+parHeight+' top=100,left=100,resizable=yes,scrollbars=1');
			if(report_format=='csv') {
				modalAlert("Please export CSV for summary only");
			}else {
				window.open('discharge_detail_reportpop.php?date12='+date+'&get_http_path='+path,'','width='+parWidth+',height='+parHeight+' top=100,left=100,resizable=yes,scrollbars=1');
			}
		} 
		else{
			alert("Please select report type");
			return false;
		}

	}

function resetfields()
{
 document.discharge_report.date1.value="";
 document.discharge_report.summary.checked=false;
 document.discharge_report.detail.checked=false;
	
}
</script>
	
<form name="discharge_report" action="#" method="post" >	
	<div class="container-fluid padding_0">
		<div class="inner_surg_middle ">
			<div style="" id="" class="all_content1_slider ">	         
				  <div class="wrap_inside_admin">
				   <div class=" subtracting-head">
					  <div class="head_scheduler new_head_slider padding_head_adjust_admin">
								<span>
								  Discharge Summary Report
								</span>
					  </div>
				   </div>   
				  <Div class="wrap_inside_admin ">
						<div class="col-md-2 visible-md"></div>
						<div class="col-lg-3 visible-lg"></div>
						<div class="col-md-8 col-sm-12 col-xs-12 col-lg-6">
							 <div class="audit_wrap">
								   <div class="form_outer">
										<div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
											<div class="form_reg text-center">
													<label class="date_r" for="date1">
													   Date	
													</label>
											</div>
									   </div>
									   <div class="clearfix margin_adjustment_only its_line"></div>
									   <div class="clearfix margin_adjustment_only"></div>
									   <div class="col-md-3 col-lg-3 col-xs-12 clearfix hidden-sm"></div>
										  <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
												<Div class="input-group"  id='datetimepicker1'>
													<input class="form-control" type="text" tabindex="1" id="date1" name="date1" value="<?php echo $date ;?>" />
													<div class="input-group-addon datepicker">
														<a href="javascript:void(0)"><span class="glyphicon glyphicon-calendar"></span></a>
													</div>
												</Div>
										  </div> <!----------------------- Full Inout col-12    ------------------------------>
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
													<label>
														<input type="checkbox" onClick="javascript:checkSingle('summary','report_typechkbx')" name="report_typechkbx" id="summary" <?php if($type=="summary") echo "checked";?>value="summary" tabindex="7"  /> Summary </label>
												</div>
												<div class="wrapped_inner_ans_pro">
													<label>
														<input type="checkbox" onClick="javascript:checkSingle('detail','report_typechkbx')" name="report_typechkbx" id="detail" <?php if($type=="detail") echo "checked";?>value="detail" tabindex="7"  /> Details   </label>
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
if(isset($_REQUEST["no_record"]) && $_REQUEST["no_record"]=="yes") { //IN CASE OF CSV REPORT
?>
	<script>
        modalAlert("No Record Found !");
        document.discharge_report.summary.checked=true;
    </script>
<?php
}
?>
</body>
</html>