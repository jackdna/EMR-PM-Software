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

//START CODE TO GET AND SET IMW_DB_NAME
imw_close($link);
include('connect_imwemr.php');
$res = imw_query("SELECT DATABASE() as imw_dbname");
$row = imw_fetch_assoc($res);
$imw_dbname = $row['imw_dbname'];
imw_close($link_imwemr);
include("common/conDb.php");
$imw_db_dot = "";
if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]), array('islandeye','valleyeye')) ){
	$imw_db_dot = $imw_dbname.'.';
}
//END CODE TO GET AND SET IMW_DB_NAME

include_once("admin/classObjectFunction.php");
$objManageData = new manageData;

unset($conditionArr);
$conditionArr['fac_del_status']=0;
$extraCondition = " AND fac_name != '' ";
$facList = $objManageData->getMultiChkArrayRecords('facility_tbl', $conditionArr, 'fac_name','ASC', $extraCondition)	;
$ascArr = array();
if($facList) {
	foreach($facList as $facDataVal) {
		$ascArr['facility_id'][$facDataVal->fac_id] = $facDataVal->fac_idoc_link_id;
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Charged Posted Report</title>
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
	var date=document.charges_posted_report.date1.value;
	
	if(date==""){
		modalAlert("Please select the date to generate report."); 
	}else{
		document.getElementById("hidd_report_format").value = "";
		if(report_format == 'csv') {
			document.getElementById("hidd_report_format").value = "csv";
		}
		document.charges_posted_report.action = 'charges_posted_report.php';
		document.charges_posted_report.submit();
	}
	//window.open('day_reportpop.php?date12='+ date+'&get_http_path='+path,'','width=650,height=600 top=100,left=100,resizable=yes,scrollbars=1');
	
}
function date12(){ 
	var s_date=document.charges_posted_report.date1.value;
	if(s_date==""){
		modalAlert("Please select the date to proceed generate report."); 
		return false;
	}
}

function resetfields(){
	document.charges_posted_report.surgeon.value='';
	document.charges_posted_report.date1.value='';
	document.charges_posted_report.date2.value='';
	document.charges_posted_report.asc.value=""
   $('.selectpicker').selectpicker('refresh');
}

var hidd_report_format_new = '';
var flPath = '';
</script>
    <?php 
    $date=trim($_REQUEST['date1']);
	$date2=trim($_REQUEST['date2']);
	$asc=trim($_REQUEST['asc']);
	
	$ascQry="";
	if($asc){
		$ascQry=" AND st.iasc_facility_id = '".$ascArr['facility_id'][$asc]."' ";
	}
	
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
		$surgeonQry = "";
		if($surgeon!="") {
			$surgeonQry = " AND pc.surgeonId in($surgeon) ";	
		}
		$query = 	"SELECT pc.patientConfirmationId FROM patientconfirmation pc  
					INNER JOIN stub_tbl st ON(st.patient_confirmation_id = pc.patientConfirmationId)
					INNER JOIN dischargesummarysheet ds ON(ds.confirmation_id = pc.patientConfirmationId) 
					INNER JOIN ".$imw_db_dot."hl7_sent hs ON (hs.sch_id = pc.patientConfirmationId AND hs.sch_id!='0' AND hs.msg_type = 'DFT' AND ds.summarySaveDateTime > hs.saved_on)
					INNER JOIN users u ON (u.usersId  = pc.surgeonId)					  
					WHERE  ( pc.dos between '".$selected_date."' AND '".$selected_date2."' ) ".$ascQry.$surgeonQry."
					ORDER BY pc.surgeon_name, pc.surgery_time
				   ";
		$queryRs = imw_query($query);
		$patientConfirmationId=0;
		while ($row = imw_fetch_array($queryRs)){
			$patientConfirmationId .= ','.$row['patientConfirmationId'];
			
		}
		if($patientConfirmationId){
			$pgNme = 'charges_posted_report_print.php';
			$winNme = 'charges_posted_report_wind';
			
			if($_REQUEST["hidd_report_format"]=="csv") {
				$winNme = 	"_self";
			}
			?>
			<form name="charges_posted_report_sub" id="charges_posted_report_sub" method="post" action="<?php echo $pgNme;?>" target="<?php echo $winNme;?>" >
				<input type="hidden" name="patientConfirmationId" value="<?php echo $patientConfirmationId;?>">
				<input type="hidden" name="startdate" value="<?php echo $selected_date;?>">
				<input type="hidden" name="enddate" value="<?php echo $selected_date2;?>">
				<input type="hidden" name="get_http_path" value="<?php echo $get_http_path;?>">
				<input type="hidden" name="hidd_report_format" value="<?php echo $_REQUEST["hidd_report_format"];?>">
                <input type="hidden" name="hidd_imw_db_dot" value="<?php echo $imw_db_dot;?>">
			</form>
			
			<script>
				var parWidth = parent.document.body.clientWidth;
				var parHeight = parent.document.body.clientHeight;
				var winNme = '<?php echo $winNme;?>';
				hidd_report_format_new = '<?php echo $_REQUEST["hidd_report_format"];?>';
				if(hidd_report_format_new == 'csv') {
					//DO NOTHING
				}else {
					window.open('blank_page.php',winNme,'width='+parWidth+',height='+parHeight+' top=100,left=100,resizable=yes,scrollbars=1');
					document.getElementById('charges_posted_report_sub').submit();
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
<form name="charges_posted_report" id="charges_posted_report" action="charges_posted_report.php" method="post" > 
   <input type="hidden" name="hidd_report_format" id="hidd_report_format" value="<?php echo $_REQUEST["hidd_report_format"];?>">
   <div class="container-fluid padding_0">
	  <div class="inner_surg_middle ">
		 <div style="" id="" class="all_content1_slider ">	         
		 <div class="wrap_inside_admin">
		 <div class=" subtracting-head">
		 <div class="head_scheduler new_head_slider padding_head_adjust_admin">
			<span>
			  Charges Posted Report
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
								   <div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
									   <div class="form_reg">
											 <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
												   <label for="surgeon" class="text-left"> 
														 Surgeon
												   </label>
											   </div>
											   <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
												   <Select class="selectpicker form-control" name="surgeon" id="surgeon" data-size="10">
													  <option value="">--All--</option>
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
                                   <div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
									   <div class="form_reg">
											 <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
												   <label for="vision_status" class="text-left"> 
														 ASC
												   </label>
											   </div>
											   <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                   <Select class="selectpicker form-control" name="asc" id="asc" data-size="10">
													  <option value="" selected>--All--</option>
													  <?php
													  $boolAscSel = false;
													  foreach($facList as $facData) {
														$facSel = "";
														if(trim($facData->fac_id) == trim($_REQUEST["asc"])) {
															$facSel = "selected";
															$boolAscSel = true;
														}elseif($facData->fac_head_quater == '1' && $boolAscSel == false) {
															$facSel = "selected";
														}
													  ?>
                                                      	<option value="<?php echo $facData->fac_id;?>" <?php echo $facSel; ?> ><?php echo $facData->fac_name;?></option>
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
												   <?php $date1Fill	=	($_REQUEST['date1']) ? $_REQUEST['date1'] : date('m-d-Y'); ?>
                                                   <input autocomplete="off" type="text" tabindex="1" id="date1" name="date1" value="<?php echo $date1Fill ;?>" class="form-control">
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
												   <?php $date2Fill	=	($_REQUEST['date2']) ? $_REQUEST['date2'] : date('m-d-Y'); ?>
                                                   <input autocomplete="off" type="text" tabindex="1" id="date2" name="date2" value="<?php echo $date2Fill;?>" class="form-control">
												   <div class="input-group-addon datepicker">
													   <a href="javascript:void(0)"><span class="glyphicon glyphicon-calendar"></span></a>
												   </div>
											   </div>
										 </div>
									   </div>
								  </div>
								 <div class="clearfix margin_adjustment_only"></div>
							   </Div>	
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
<?php
if($_REQUEST["no_record"]=="yes") { //IN CASE OF CSV REPORT
?>
	<script>
		modalAlert("No Record Found !");
	</script>
<?php
}
?>	
<script>
$(document).ready(function() {
	if(hidd_report_format_new == 'csv') {
		if(document.getElementById('charges_posted_report_sub')) {
			document.getElementById('charges_posted_report_sub').submit();	
		}
	}
});
</script>	
		
