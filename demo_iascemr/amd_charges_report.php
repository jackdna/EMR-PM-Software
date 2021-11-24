<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
   include("common/conDb.php");
	include_once('common/user_agent.php');
?>

<!DOCTYPE html>
<html>
<head>
<title>AMD Charges Report</title>
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

//set surgerycenter detail 
$fac_qry	=	" and st.iasc_facility_id='$_SESSION[iasc_facility_id]' ";
$fac_con	=	($_SESSION['iasc_facility_id']	 ?	$fac_qry	 :	'' )	; 

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
	
function padout(number){
return (number < 10) ? '0' + number : number;
}
function reportpop(path,report_format){
	var date=document.amd_charges_report.date1.value;
	
	if(date=="")
	{
		modalAlert("Please select the date to generate report.");
	}
	else
	{
		
		document.getElementById("hidd_report_format").value = "";
		if(report_format == 'view') {
			document.getElementById("hidd_report_format").value = "view";
		}
		document.amd_charges_report.action = 'amd_charges_report.php';
		document.amd_charges_report.submit();
	}
	//window.open('day_reportpop.php?date12='+ date+'&get_http_path='+path,'','width=650,height=600 top=100,left=100,resizable=yes,scrollbars=1');
	
}

function resetfields(){
	document.amd_charges_report.date1.value='';
	document.amd_charges_report.date2.value='';
}

var hidd_report_format_new = '';
var flPath = '';
</script>
<?php
	
	$successImg = '<img src="./new_html2pdf/check_mark16.jpg">';
	$errorImg = '<img src="./new_html2pdf/Cr.jpg">';
	
	$date		=	trim($_REQUEST['date1']);
	$date2	=	trim($_REQUEST['date2']);
	
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
		
		/*
		$query = "SELECT 
						`cl`.`id`,
						`pc`.`dos`, 
						`pd`.`patient_fname` AS 'fname', 
						`pd`.`patient_mname` AS 'mname', 
						`pd`.`patient_lname` AS 'lname', 
						`pd`.`date_of_birth` AS 'dob', 
						`pd`.`amd_patient_id` AS 'amd_id', 
						`cl`.`amd_visit_id`, 
						`cl`.`status`, 
						`cl`.`reason`, 
						`cl`.`date_posted`, 
						`cl`.`m_amd_visit_id`, 
						`cl`.`type` 
					FROM 
						`patientconfirmation` `pc` 
						INNER JOIN `patient_data_tbl` `pd` ON(
							`pc`.`patientId` = `pd`.`patient_id`
						) 
						INNER JOIN `stub_tbl` `st` ON(
							`pc`.`patientConfirmationId` = `st`.`patient_confirmation_id`
						) 
						INNER JOIN `patient_in_waiting_tbl` `wt` ON(
							`st`.`iolink_patient_in_waiting_id` = `wt`.`patient_in_waiting_id`
						) 
						INNER JOIN (SELECT * FROM `amd_charges_log` WHERE `id` IN(SELECT MAX(`id`) FROM `amd_charges_log` GROUP BY `m_amd_visit_id`,`type`)) `cl` ON(
							`wt`.`amd_visit_id` = `cl`.`m_amd_visit_id`
						) 
					WHERE 
						`pc`.`dos` BETWEEN '".$selected_date."' AND '".$selected_date2."'
					GROUP BY `cl`.`m_amd_visit_id`, `type`";*/

		$query = "SELECT 
						`cl`.`id`,
						`pc`.`dos`, 
						`pd`.`patient_fname` AS 'fname', 
						`pd`.`patient_mname` AS 'mname', 
						`pd`.`patient_lname` AS 'lname', 
						`pd`.`date_of_birth` AS 'dob', 
						`pd`.`amd_patient_id` AS 'amd_id', 
						`cl`.`amd_visit_id`, 
						`cl`.`status`, 
						`cl`.`reason`, 
						`cl`.`date_posted`, 
						`cl`.`m_amd_visit_id`, 
						`cl`.`type` 
					FROM 
						`patientconfirmation` `pc` 
						INNER JOIN `patient_data_tbl` `pd` ON(
							`pc`.`patientId` = `pd`.`patient_id`
						) 
						INNER JOIN `stub_tbl` `st` ON(
							`pc`.`patientConfirmationId` = `st`.`patient_confirmation_id`
						) 
						INNER JOIN `patient_in_waiting_tbl` `wt` ON(
							`st`.`iolink_patient_in_waiting_id` = `wt`.`patient_in_waiting_id`
						) 
                        INNER JOIN `amd_charges_log` `cl` ON `wt`.`amd_visit_id` = `cl`.`m_amd_visit_id`
						INNER JOIN (SELECT MAX(`id`) AS `id` FROM `amd_charges_log` GROUP BY `m_amd_visit_id`,`type`) `clg` ON `clg`.id = `cl`.id  
					WHERE 
						`pc`.`dos` BETWEEN '".$selected_date."' AND '".$selected_date2."'";
							
		$queryRs 	= imw_query($query);
		$recordsFlag	=	imw_num_rows($queryRs) > 0;
		
		if($recordsFlag){
			$pgNme = 'amd_charges_report_print_detail.php';
			$winNme = 'amd_charges_report_summary_wind'.rand();
			//if($_REQUEST["hidd_report_format"]=="csv") {
			//	$winNme = 	"_self";
			//}
			
			if($_REQUEST["hidd_report_format"]=="view") {
				
				$resp = $queryRs;
				
				$chargesList = array();
				if( $resp && imw_num_rows($resp) > 0 )
				{
					while( $row = imw_fetch_assoc($resp) )
					{
						if( !isset($chargesList[$row['m_amd_visit_id']]) )
							$chargesList[$row['m_amd_visit_id']] = array();
						
						$visitChargeData = &$chargesList[$row['m_amd_visit_id']];
						
						$visitChargeData['dos'] = date('m-d-Y', strtotime($row['dos']));
						$visitChargeData['m_visit'] = $row['m_amd_visit_id'];
						
						$visitChargeData['name'] = $row['fname'].', '.$row['lname'];
						$visitChargeData['name'] .= ( trim($row['mname'])!='' )? ' '.$row['mname'] : '';
						
						$visitChargeData['dob'] = date('m-d-Y', strtotime($row['dob']));
						$visitChargeData['amd_id'] = $row['amd_id'];
						
						//$visitChargeData['date_posted'] = date('m-d-Y H:i:s', strtotime($row['date_posted']));
						
						if( $row['type'] == '1' )
						{
							$visitChargeData['phy_stauts'] = (bool)$row['status'];
							$visitChargeData['phy_reason'] = trim($row['reason']);
							$visitChargeData['phy_visit_id'] = trim($row['amd_visit_id']);
							$visitChargeData['phy_posted_date'] = date('m-d-Y h:i:s A', strtotime($row['date_posted']));
						}
						elseif( $row['type'] =='2' )
						{
							$visitChargeData['fac_stauts'] = (bool)$row['status'];
							$visitChargeData['fac_reason'] = trim($row['reason']);
							$visitChargeData['fac_visit_id'] = trim($row['amd_visit_id']);
							$visitChargeData['fac_posted_date'] = date('m-d-Y H:i:s', strtotime($row['date_posted']));
						}
						elseif( $row['type'] == '3' )
						{
							$visitChargeData['anes_stauts'] = (bool)$row['status'];
							$visitChargeData['anes_reason'] = trim($row['reason']);
							$visitChargeData['anes_visit_id'] = trim($row['amd_visit_id']);
							$visitChargeData['anes_posted_date'] = date('m-d-Y H:i:s', strtotime($row['date_posted']));
						}
					}
				}
				
				//get detail for logged in facility
				$queryFac=imw_query("select * from facility_tbl where fac_id='$_SESSION[facility]'")or die(imw_error());
				$dataFac=imw_fetch_object($queryFac);
				$name=stripcslashes($dataFac->fac_name);
				$address=stripcslashes($dataFac->fac_address1).' '.stripcslashes($dataFac->fac_address2).' '.stripcslashes($dataFac->fac_city).' '.stripcslashes($dataFac->fac_state);

?>
<style>
.BdrAll { 
	border:solid 1px #999; 
	padding-top:2px; padding-bottom:2px; padding-left:2px; font-family:Arial, Helvetica, sans-serif;	
}
.BdrTBR { 
	border:solid 1px #999; border-left: solid 0px #fff;
	padding-top:2px; padding-bottom:2px; padding-left:2px; font-family:Arial, Helvetica, sans-serif;
}
.BdrLBR { 
	border:solid 1px #999; border-top: solid 0px #fff;
	padding-top:2px; padding-bottom:2px; padding-left:2px; font-family:Arial, Helvetica, sans-serif;
}
.BdrLB { 
	border-bottom: solid 1px #999;
	border-left: solid 1px #999;
	padding-top:2px; padding-bottom:2px; padding-left:2px; font-family:Arial, Helvetica, sans-serif;
}
.BdrBR {
	border-bottom: solid 1px #999;
	border-right: solid 1px #999;
	padding-top:2px; padding-bottom:2px; padding-left:2px; font-family:Arial, Helvetica, sans-serif;
}
.BdrR {
	border-right: solid 1px #999;
	padding-top:2px; padding-bottom:2px;  padding-left:2px; font-family:Arial, Helvetica, sans-serif;
}
.BdrB {
	border-bottom: solid 1px #999;
	padding-top:2px; padding-bottom:2px;  padding-left:2px; font-family:Arial, Helvetica, sans-serif;
}

.tb_heading{ 
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#000000;
	background-color:#FE8944;
}
.text_b{
	font-size:15px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#000000;
}
.text_16b{
	font-size:16px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#000000;
}
.color_white{
	color:#FFFFFF;
}
.text{
	font-size:13px;
	font-family:Arial, Helvetica, sans-serif;
	background-color:#FFFFFF;
}
.orangeFace{
	color:#FE8944;
}
.text_15 {
	font-size:15px;
	font-family:Arial, Helvetica, sans-serif;
}
.text_18 {
	font-size:18px;
	font-family:Arial, Helvetica, sans-serif;	
}
</style>
<div class="main_wrapper">
	<div class="container-fluid padding_0">
		<div class="inner_surg_middle">
			<div style="" id="" class="all_content1_slider ">
            <div class="wrap_inside_admin">
					<div class=" subtracting-head">
						<div class="head_scheduler new_head_slider padding_head_adjust_admin">
							<span>AMD Charges Report</span>
						</div>
					</div>
					
					<!--Charges List Table-->
					<div class="wrap_inside_admin scrollable_yes">
						<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped chargesDetail">
							<thead>
								<tr  valign="top">
									<td align="center" class="text_b BdrAll" style="width:70px;">S.No.</td>
									<td align="left"   class="text_b BdrTBR" style="width:px;">Patient Name (DOB) - AMD ID</td>
									<td align="left"   class="text_b BdrTBR" style="width:250px;">DOS/Date Posted</td>
									<td align="center"   class="text_b BdrTBR" style="width:150px;">AMD Visit ID</td>
									<td align="left"   class="text_b BdrTBR" style="width:380px;">Physician</td>
									<td align="left"   class="text_b BdrTBR" style="width:380px;">Facility</td>
									<td align="left"   class="text_b BdrTBR" style="width:380px;">Anesthesia</td>
								</tr>
							</thead>
							<tbody>
								<?php $counter = 0; foreach($chargesList as $chargeData): ?>
								<tr>
									<td align="center" class="BdrLBR"><?php echo ++$counter; ?></td>
									<td class="BdrBR"><?php echo $chargeData['name'].' - ('.$chargeData['dob'].') - '.$chargeData['amd_id']; ?></td>
									<td class="BdrBR"><?php echo $chargeData['dos'].' / '.$chargeData['phy_posted_date']; ?></td>
									<td align="center" class="BdrBR"><?php echo $chargeData['m_visit']; ?></td>
									<td align="left" class="BdrBR"><?php echo ( ($chargeData['phy_stauts'])?$successImg.' '.$chargeData['phy_visit_id']:$errorImg."<br />".$chargeData['phy_reason'] ); ?></td>
									<td align="left" class="BdrBR"><?php echo ( ($chargeData['fac_stauts'])?$successImg.' '.$chargeData['fac_visit_id']:$errorImg."<br />".$chargeData['fac_reason'] ); ?></td>
									<td align="left" class="BdrBR"><?php echo ( ($chargeData['anes_stauts'])?$successImg.' '.$chargeData['anes_visit_id']:$errorImg."<br />".$chargeData['anes_reason'] ); ?></td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
	
					<div id="div_innr_btn" class="btn-footer-slider shadow_adjust_above" style="position:static; bottom:0;">
						<a href="amd_charges_report.php" class="btn btn-info">
							<b class="fa fa-edit"></b> Reset 
						</a>
					</div>
			
			
				</div>
			</div>
		</div>
	</div>
</div>
<?php
				exit;
			}
			else{
		?>
			<form name="amd_charges_report_sub" id="amd_charges_report_sub" method="post" action="<?php echo $pgNme;?>" target="<?php echo $winNme;?>" >
				<input type="hidden" name="startdate" value="<?php echo $selected_date;?>">
				<input type="hidden" name="enddate" value="<?php echo $selected_date2;?>">
				<input type="hidden" name="get_http_path" value="<?php echo $get_http_path;?>">
				<input type="hidden" name="hidd_report_format" value="<?php echo $_REQUEST["hidd_report_format"];?>">
			</form>
			
			<script>
				var parWidth = parent.document.body.clientWidth;
				var parHeight = parent.document.body.clientHeight;
				var winNme = '<?php echo $winNme;?>';
				var pgNme = '<?php echo $pgNme;?>';
				hidd_report_format_new = '<?php echo $_REQUEST["hidd_report_format"];?>';
				if(hidd_report_format_new == 'csv') {
					//DO NOTHING
				}else {
					window.open(pgNme,winNme,'width='+parWidth+',height='+parHeight+' top=100,left=100,resizable=yes,scrollbars=1');
					document.getElementById('amd_charges_report_sub').submit();
				}
			</script>
			<?php
			}
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
<form name="amd_charges_report" action="amd_charges_report.php" method="post" >
	<input type="hidden" name="hidd_report_format" id="hidd_report_format" value="<?php echo $_REQUEST["hidd_report_format_new"];?>">
  <div class="container-fluid padding_0">
  	<div class="inner_surg_middle ">
    	<div style="" id="" class="all_content1_slider ">
      	<div class="wrap_inside_admin">
        	
          <div class=" subtracting-head">
          	<div class="head_scheduler new_head_slider padding_head_adjust_admin">
            	<span>AMD Charges Report</span>
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
											 <div class="form_reg text-center">
													 <label class="date_r">
														 Select DOS
													 </label>
											 </div>
										</div>
                         
										<div class="clearfix margin_adjustment_only  its_line"></div>
                    <div class="clearfix margin_adjustment_only"></div>                                                  
										<div class="clearfix margin_adjustment_only visible-sm"></div>
                    
										<div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
											<div class="form_reg">
											  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
													 <label for="date1" class="">From</label>
											  </div>	
											  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                        	<div id="datetimepicker1" class="input-group">
                          	<?php $date1Fill	=	($_REQUEST['date1']) ? $_REQUEST['date1'] : date('m-d-Y'); ?>
                          	<input type="text" tabindex="1" id="date1" name="date1" value="<?php echo $date1Fill ;?>" class="form-control">
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
                        	<label for="date2" class="">To</label>
                      	</div>
                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                        	<div id="datetimepicker2" class="input-group">
                          	<?php $date2Fill	=	($_REQUEST['date2']) ? $_REQUEST['date2'] : date('m-d-Y'); ?>
                          	<input type="text" tabindex="1" id="date2" name="date2" value="<?php echo $date2Fill;?>" class="form-control">
                            <div class="input-group-addon datepicker">
                            	<a href="javascript:void(0)"><span class="glyphicon glyphicon-calendar"></span></a>
                           	</div>
                        	</div>
                      	</div>
                    	</div>
                  	</div>
                    
                    <div class="clearfix margin_adjustment_only"></div>
                    <div class="clearfix margin_adjustment_only"></div>
                    <Div class="clearfix margin_adjustment_only"></Div>
                  </Div>
								</div>
            	</div>
              
              <div class="btn-footer-slider">
              	<a href="javascript:void(0)" class="btn btn-info" id="generate_report" onClick="return reportpop('<?php echo $get_http_path;?>');">
                	<b class="fa fa-edit"></b> Generate Report 
               	</a>
                <a href="javascript:void(0)" class="btn btn-info" id="generate_csv_report" onClick="return reportpop('<?php echo $get_http_path; ?>','view');">
                	<b class="fa fa-download"></b> View Report 
              	</a>
                <a id="reset" onClick="return resetfields();" class="btn btn-default" href="javascript:void(0)">
                	<b class="fa fa-refresh"></b> Reset
               	</a>
             	</div>
          	</div>
       		</div>		
		  	
        </div>
        
		 	</div> 
		</div>
    <!-- NEcessary PUSH     -->	 
    <Div class="push"></Div>
    <!-- NEcessary PUSH     -->
	</div>
</form>

<script>
//$(document).ready(function() {
//	if(hidd_report_format_new == 'csv') {
//		if(document.getElementById('amd_charges_report_sub')) {
//			document.getElementById('amd_charges_report_sub').submit();	
//		}
//	}
//});
</script>