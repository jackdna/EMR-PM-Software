<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
include("common/conDb.php");
include_once('common/user_agent.php');

include_once("admin/classObjectFunction.php");
global $objManageData;
$objManageData = new manageData;
?>

<!DOCTYPE html>
<html>
<head>
<title>Missing Visit ID Report</title>
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
	var date=document.direct_visit_report.date1.value;
	
	if(date=="")
	{
		modalAlert("Please select the date to generate report.");
	}
	else
	{
		document.getElementById("hidd_report_format").value = "";
        switch (report_format) {
            case 'csv':
                document.getElementById("hidd_report_format").value = "csv";
                break;
            case 'view':
                document.getElementById("hidd_report_format").value = "view";
                break;
            default:
                document.getElementById("hidd_report_format").value = "";
                break;
        }
		document.direct_visit_report.action = 'direct_visit_report.php';
		document.direct_visit_report.submit();
	}
	//window.open('day_reportpop.php?date12='+ date+'&get_http_path='+path,'','width=650,height=600 top=100,left=100,resizable=yes,scrollbars=1');
	
}

function resetfields(){
	document.direct_visit_report.date1.value='';
	document.direct_visit_report.date2.value='';
}

var hidd_report_format_new = '';
var flPath = '';
</script>
<?php
    
	$successImg = '<img src="./new_html2pdf/check_mark16.jpg">';
	$errorImg = '<img src="./new_html2pdf/Cr.jpg">';
	
	$date	=	trim($_REQUEST['date1']);
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
		
        $query = "SELECT 
                `pc`.`ascId`, 
                `pt`.`dos`, 
                `pt`.`surgery_time`, 
                `pt`.`surgeon_fname`, 
                `pt`.`surgeon_lname`, 
                `pdt`.`patient_fname`, 
                `pdt`.`patient_lname`, 
                `pt`.`patient_primary_procedure`, 
                `pt`.`patient_secondary_procedure`, 
                `pt`.`patient_tertiary_procedure` 
            FROM  `patient_in_waiting_tbl` AS `pt`
                INNER JOIN `patient_data_tbl` AS `pdt` ON `pdt`.`patient_id` = `pt`.`patient_id`
                LEFT JOIN `stub_tbl` AS `st` ON `st`.`iolink_patient_in_waiting_id` = `pt`.`patient_in_waiting_id`
                LEFT JOIN `patientconfirmation` AS `pc` ON `pc`.`patientConfirmationId`  = `st`.`patient_confirmation_id`
            WHERE 
                `pt`.`source` = '' 
                AND 
                    `pt`.`dos` BETWEEN '".$selected_date."' AND '".$selected_date2."' 
                AND 
                    `pc`.`ascId` != ''
            ";
		
		$queryRs 	= imw_query($query);
		$recordsFlag	=	imw_num_rows($queryRs) > 0;

        if($recordsFlag) {

            if($_REQUEST["hidd_report_format"]=="view") {
            
                $resp = $queryRs;
				if( $resp && imw_num_rows($resp) > 0 )
				{
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
                                                    <span>Missing Visit ID Report</span>
                                                </div>
                                            </div>
                                            
                                            <!--Charges List Table-->
                                            <div class="wrap_inside_admin scrollable_yes">

                                                <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped chargesDetail">
                                                    <thead>
                                                        <tr valign="top">
                                                            <th align="center" class="text_b BdrAll">ASC ID</th>
                                                            <th align="center" class="text_b BdrAll">DOS</th>
                                                            <th align="center" class="text_b BdrAll">Surgery Time</th>
                                                            <th align="center" class="text_b BdrAll">Surgeon F.Name</th>
                                                            <th align="center" class="text_b BdrAll">Surgeon L.Name</th>
                                                            <th align="center" class="text_b BdrAll">Patient F.Name</th>
                                                            <th align="center" class="text_b BdrAll">Patient L.Name</th>
                                                            <th align="center" class="text_b BdrAll">Primary Procedure</th>
                                                            <th align="center" class="text_b BdrAll">Secondary Procedure</th>
                                                            <th align="center" class="text_b BdrAll">Tertiary Procedure</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                            while($row = imw_fetch_assoc($resp)) { 
                                                        ?>
                                                            <tr>
                                                            <td align="center" class="BdrLBR"><?php echo $row["ascId"]; ?></td>
                                                            <td align="center" class="BdrLBR"><?php echo date('m-d-Y', strtotime($row["dos"])); ?></td>
                                                            <td align="center" class="BdrLBR"><?php echo $row["surgery_time"]; ?></td>
                                                            <td align="center" class="BdrLBR"><?php echo $row["surgeon_fname"]; ?></td>
                                                            <td align="center" class="BdrLBR"><?php echo $row["surgeon_lname"]; ?></td>
                                                            <td align="center" class="BdrLBR"><?php echo $row["patient_fname"]; ?></td>
                                                            <td align="center" class="BdrLBR"><?php echo $row["patient_lname"]; ?></td>
                                                            <td align="left" class="BdrLBR"><?php echo $row["patient_primary_procedure"]; ?></td>
                                                            <td align="left" class="BdrLBR"><?php echo $row["patient_secondary_procedure"]; ?></td>
                                                            <td align="left" class="BdrLBR"><?php echo $row["patient_tertiary_procedure"]; ?></td>
                                                            </tr>
                                                        <?php
                                                            }
                                                        ?>                                
                                                    </tbody>
                                                </table>

                                            </div>

                                            <div id="div_innr_btn" class="btn-footer-slider shadow_adjust_above" style="position:static; bottom:0;">
                                                <a href="direct_visit_report.php" class="btn btn-info">
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
            } elseif ($_REQUEST["hidd_report_format"]=="csv") {

                ?>

                <form name="direct_visit_report_sub" id="direct_visit_report_sub" method="post" action="<?php echo 'direct_visit_report_print.php'; ?>" target="_self">
                    <input type="hidden" name="startdate" value="<?php echo $selected_date;?>">
                    <input type="hidden" name="enddate" value="<?php echo $selected_date2;?>">
                    <input type="hidden" name="get_http_path" value="<?php echo $get_http_path;?>">
                    <input type="hidden" name="hidd_report_format" value="<?php echo $_REQUEST["hidd_report_format"];?>">
                </form>
                <script>
                    hidd_report_format_new = '<?php echo $_REQUEST["hidd_report_format"];?>';
                </script>
                <?php
                
            } else {
                ?>
                <script>
                    modalAlert('Report format is not valid.')
                </script>
                <?php
            }

        } else {
            ?>
			<script>
				modalAlert('No Data Found To Generate Report')
			</script>
			<?php
        }
    }
?>
<form name="direct_visit_report" action="direct_visit_report.php" method="post" >
	<input type="hidden" name="hidd_report_format" id="hidd_report_format" value="<?php echo $_REQUEST["hidd_report_format_new"];?>">
  <div class="container-fluid padding_0">
  	<div class="inner_surg_middle ">
    	<div style="" id="" class="all_content1_slider ">
      	<div class="wrap_inside_admin">
        	
          <div class=" subtracting-head">
          	<div class="head_scheduler new_head_slider padding_head_adjust_admin">
                <span>Missing visit ID Report</span>
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
                <a href="javascript:void(0)" class="btn btn-info" id="" onClick="return reportpop('<?php echo $get_http_path; ?>','view');">
                	<b class="fa fa-eye"></b> View Report 
              	</a>
              	<a href="javascript:void(0)" class="btn btn-info" id="" onClick="return reportpop('<?php echo $get_http_path;?>', 'csv');">
                	<b class="fa fa-download"></b> Export CSV 
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
$(document).ready(function() {
	if(hidd_report_format_new == 'csv') {
		if(document.getElementById('direct_visit_report_sub')) {
			document.getElementById('direct_visit_report_sub').submit();	
		}
	}
});
</script>