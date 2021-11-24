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
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;

unset($conditionArr);
$conditionArr['fac_del_status']=0;
$extraCondition = " AND fac_name != '' ";
$facList = $objManageData->getMultiChkArrayRecords('facility_tbl', $conditionArr, 'fac_name','ASC', $extraCondition)	;
$ascArr = $idocFacIdAllArr = array();
if($facList) {
	foreach($facList as $facDataVal) {
		$ascArr['facility_id'][$facDataVal->fac_id] = $facDataVal->fac_idoc_link_id;
		$idocFacIdAllArr[] = $facDataVal->fac_idoc_link_id;
	}
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Surgery Log Book</title>
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
function reportpop(report_format){
	var date=document.surgery_log_book.date1.value;
	
	if(date==""){
		modalAlert("Please select the date to generate report."); 
	}else{
		document.getElementById("hidd_report_format").value = "";
		if(report_format == 'csv') {
			document.getElementById("hidd_report_format").value = "csv";
		}
		document.surgery_log_book.action = 'surgery_log_book.php';
		document.surgery_log_book.submit();
	}
}
function date12(){ 
	var s_date=document.surgery_log_book.date1.value;
	if(s_date==""){
		modalAlert("Please select the date to proceed generate report."); 
		return false;
	}
}

function resetfields(){
	document.surgery_log_book.surgeon.value='';
	document.surgery_log_book.date1.value='';
	document.surgery_log_book.date2.value='';
	document.surgery_log_book.asc.value=""
   $('.selectpicker').selectpicker('refresh');
}

var hidd_report_format_new = '';
var flPath = '';
</script>
    <?php 
    
	$date=trim($_REQUEST['date1']);
	$date2=trim($_REQUEST['date2']);
	
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

		$andQry = $andConfQry = $idocFacIdComma = $surgeonComma = "";
		$surgeon = $_REQUEST['surgeon'];
		if(is_array($surgeon) && count($surgeon)>0) {
			$surgeonComma = implode(',',$surgeon);
			if($surgeonComma) {
				$andConfQry .= " AND pc.surgeonId in($surgeonComma) ";	
			}
		}
		
		$asc=$_REQUEST['asc'];
		if(is_array($asc) && count($asc)>0) {
			foreach($asc as $ascVal) {
				if($ascVal) {
					$idocFacIdArr[] = $ascArr['facility_id'][$ascVal];
				}
			}
		}
		$idocFacIdComma = implode(",",$idocFacIdArr);
		$selAllASC = '';
		if(!$idocFacIdComma) {
			$idocFacIdComma = implode(",",$idocFacIdAllArr);
			$selAllASC = 'yes';		
		}
		if($idocFacIdComma) {
			$andQry .= " AND st.iasc_facility_id in($idocFacIdComma) ";
		}
		
		
		
		//START GET SURGEON ARRAY
		$ptUserIdArr = array();
		if($surgeonComma) {
			$userIdQry 			= "SELECT usersId, fname,mname,lname FROM users WHERE  usersId in ($surgeonComma) ";
			$userIdRes 			= imw_query($userIdQry);
			if(imw_num_rows($userIdRes)>0) {
				while($userIdRow= imw_fetch_array($userIdRes)) {
					$usersId 	= $userIdRow['usersId'];
					$userFname 	= trim(stripslashes($userIdRow['fname']));
					$userMname 	= trim(stripslashes($userIdRow['mname']));
					$userLname 	= trim(stripslashes($userIdRow['lname']));
					$ptUserIdArr[$userFname][$userMname][$userLname] = $usersId;
				}
			}
		}
		
		//END GET SURGEON ARRAY
		
		$stub_id 		= 0;
		$imwPatientId 	= 0;
		//START GET RECORDS WHICH ARE YET TO OPEN THEIR CHART
		$qryZeroConf = "SELECT st.stub_id, st.surgeon_fname, st.surgeon_mname, st.surgeon_lname, st.imwPatientId FROM stub_tbl st 
				  		WHERE ( st.dos between '".$selected_date."' AND '".$selected_date2."' ) AND st.patient_confirmation_id = '0' ".$andQry." ORDER BY st.stub_id";
		$resZeroConf = imw_query($qryZeroConf);
		while ($rowZeroConf = imw_fetch_array($resZeroConf)){
			$stub_surgeon_fname = trim(stripslashes($rowZeroConf['surgeon_fname']));
			$stub_surgeon_mname = trim(stripslashes($rowZeroConf['surgeon_mname']));
			$stub_surgeon_lname = trim(stripslashes($rowZeroConf['surgeon_lname']));
			if($ptUserIdArr[$stub_surgeon_fname][$stub_surgeon_mname][$stub_surgeon_lname] || !$surgeonComma) {
				$stub_id 		.= ','.$rowZeroConf['stub_id'];
				$imwPatientId 	.= ','.$rowZeroConf['imwPatientId'];
			}
		}
		//END GET RECORDS WHICH ARE YET TO OPEN THEIR CHART


		//START GET MAIN QUERY
		$query = "SELECT st.stub_id,st.patient_status, pc.patientConfirmationId,pd.imwPatientId FROM stub_tbl st  
				   INNER JOIN patientconfirmation pc on (pc.patientConfirmationId = st.patient_confirmation_id ".$andConfQry.")
				   INNER JOIN patient_data_tbl pd ON (pd.patient_id = pc.patientId )
				   WHERE  ( st.dos between '".$selected_date."' AND '".$selected_date2."' ) ".$andQry."
				   ORDER BY st.surgeon_lname, st.surgeon_fname, st.dos, st.surgery_time";
		$queryRs = imw_query($query);
		while ($row = imw_fetch_array($queryRs)){
			$stub_id .= ','.$row['stub_id'];
			$imwPatientId 	.= ','.$row['imwPatientId'];
		}//echo '<br>'.$qryZeroConf.'<br>'.$query.'<br>'.$stub_id;
		//END GET MAIN QUERY


		if($stub_id){
			$pgNme = 'surgery_log_book_print.php';
			$winNme = 'surgery_log_book_wind';
			
			if($_REQUEST["hidd_report_format"]=="csv") {
				$winNme = 	"_self";
			}
			?>
			<form name="surgery_log_book_sub" id="surgery_log_book_sub" method="post" action="<?php echo $pgNme;?>" target="<?php echo $winNme;?>" >
                <input type="hidden" name="stubId" value="<?php echo $stub_id;?>">
                <input type="hidden" name="imwPatientId" value="<?php echo $imwPatientId;?>">
				<input type="hidden" name="startdate" value="<?php echo $selected_date;?>">
				<input type="hidden" name="enddate" value="<?php echo $selected_date2;?>">
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
					window.open('blank_page.php',winNme,'width='+parWidth+',height='+parHeight+' top=100,left=100,resizable=yes,scrollbars=1');
					document.getElementById('surgery_log_book_sub').submit();
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
<form name="surgery_log_book" id="surgery_log_book" action="surgery_log_book.php" method="post" > 
   <input type="hidden" name="hidd_report_format" id="hidd_report_format" value="<?php echo $_REQUEST["hidd_report_format"];?>">
   <div class="container-fluid padding_0">
	  <div class="inner_surg_middle ">
		 <div style="" id="" class="all_content1_slider ">	         
		 <div class="wrap_inside_admin">
		 <div class=" subtracting-head">
		 <div class="head_scheduler new_head_slider padding_head_adjust_admin">
			<span>
			  Surgery Log Book
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
                                                   <Select class="selectpicker form-control" name="surgeon[]" id="surgeon" data-size="10" multiple="multiple" tabindex="1" title="No Provider Selected">
													  <option data-attending = "0" value="" <?php if(!trim($surgeonComma)) { echo "selected"; }?> >--All--</option>
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
														 <option data-attending = "1" value="<?php echo $physician_id;?>" <?php if(in_array($physician_id,$_REQUEST["surgeon"])) { echo "selected"; }?>><?php echo $physician_name;?></option>
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
                                                   <Select class="selectpicker form-control" name="asc[]" id="asc" data-size="10" multiple="multiple" tabindex="2" title="No ASC Selected">
													  <option data-attending = "0" value="" <?php if(!trim($idocFacIdComma) || trim($selAllASC)) { echo "selected";}?> >--All--</option>
													  <?php
													  foreach($facList as $facData) {
														$facSel = "";
														if(in_array(trim($facData->fac_id),$_REQUEST["asc"])) {
															$facSel = "selected";
															$boolAscSel = true;
														}
													  ?>
                                                      	<option data-attending = "1" data-idoc_fac_id="<?php echo $facData->fac_idoc_link_id;?>" value="<?php echo $facData->fac_id;?>" <?php echo $facSel; ?> ><?php echo $facData->fac_name;?></option>
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
                        <a href="javascript:void(0)" class="btn btn-info" id="generate_csv_report" onClick="return reportpop('csv');">
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
		if(document.getElementById('surgery_log_book_sub')) {
			document.getElementById('surgery_log_book_sub').submit();	
		}
	}
});
</script>	
		
