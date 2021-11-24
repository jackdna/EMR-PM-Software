<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

?><?php
/*
File: electronic_billing_decode.php
Purpose: To show Human Readable 997.
Access Type: Direct Access (in popup) 
*/

set_time_limit(600);
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once(dirname(__FILE__).'/../../library/classes/class.electronic_billing.php');

$objEBilling = new ElectronicBilling();
$do = isset($_REQUEST['eb_task']) ? trim($_REQUEST['eb_task']) : '';
$data2Show = array();
switch($do){
	case 'EDI2Human':
		$report_id = (isset($_REQUEST['report_id']) && intval($_REQUEST['report_id'])>0) ? intval(trim($_REQUEST['report_id'])) : 0;
		$report_type = isset($_REQUEST['report_type']) ? trim($_REQUEST['report_type']) : '';
		if(!$report_id || $report_type==''){
			$data2Show['error'] = 'Invalid ReportID/ReportType.';
		}else{
			$data2Show 	= $objEBilling->EDI2Human($report_id,$report_type);
			//pre($data2Show,1);
		}
		break;
	case 'read999':
		
		break;
	default:
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0; charset=UTF-8" />
<title>Electronic Billing</title>
<!-- Bootstrap -->
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/common.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/billinginfo.css" rel="stylesheet">

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/css/css/html5shiv.min.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/css/css/respond.min.js"></script>
<![endif]-->

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/common.js"></script>
<script type="text/javascript">
$(document).ready(function(e) {
    setTimeout(function(){init_disp_billing_rpt();},200);
});

function init_disp_billing_rpt(obj_id){
	obj_id = typeof(obj_id)=='string' ? '' : obj_id;
	if(obj_id != ''){
		$('#div_emd_reports').show();
	}
	h 		= pageHeight();
	pbh		= $('.bilnghead').height()*2;
	pbf		= $('#bottom_buttons').height()*2;
	h		= parseInt(h-pbh-pbf-0);
	$('#div_emd_reports').height(h);
}

function toggle_rpt_data(a,rptNo){
	if(a==1){
		$('#rpt_data_txt'+rptNo+', #rpt_dataX_link'+rptNo).show();
		$('#rpt_data_link'+rptNo).hide();	
	}
	else if(a==0){
		$('#rpt_data_txt'+rptNo+', #rpt_dataX_link'+rptNo).hide();
		$('#rpt_data_link'+rptNo).show();	
	}
}
</script>
</head>
<body>
<div class="billreport">
    <div class="bilnghead">
        <div class="row">
        	<div class="col-sm-12">
            	<h2>Batch File Status Report</h2>
            </div>
        </div>
    </div>
	<div class="clearfix"></div>
    <div id="div_emd_reports">
       <?php
		if($data2Show==false){
			echo 'Not a valid 997/999 report.';
		}else{
			$rpt_counter = 0;
			foreach($data2Show as $EDI_data_arr){
				//pre($EDI_data_arr,1);
				$rpt_counter++;
				$raw_report_data	= $EDI_data_arr['ReportData'];
				$arr_segments		= $EDI_data_arr['segments'];
				$EDI_data			= $objEBilling->Render_EDI_DataARR($EDI_data_arr);
				//pre($EDI_data,1);
				$Bstatus			= $EDI_data['BatchStatus'];
				$report_date		= substr($EDI_data['ReportDate'],4,2).'-'.substr($EDI_data['ReportDate'],6,2).'-'.substr($EDI_data['ReportDate'],0,4);
				$HeaderControlNum	= $EDI_data['BatchControlIden'];
				$arr_BatchDetails	= $objEBilling->getBatchFileDetails($HeaderControlNum,'header_control_identifier');
				//pre($arr_BatchDetails,1);
				if($objEBilling->MarkSubmitted){//enter records in submited_records table
					$objEBilling->MarkSubmittedRecords($arr_BatchDetails['Batch_file_submitte_id'],$report_date,$Bstatus,$raw_report_data);
				}
				
				if($arr_BatchDetails){
					$Interchange_control= $objEBilling->padd_string($arr_BatchDetails['Interchange_control'],4,'0');
					$ARR_batchData		= $objEBilling->get837toArray(trim($arr_BatchDetails['file_data']));
				}
				$Encounters				= explode(',',$arr_BatchDetails['encounter_id']);
			?>
				<?php 
                if($Bstatus=='A'){
                    $bigText = 'Batch Accepted';
                    $image = 'big_tick.png';	
                }else if($Bstatus=='R'){
                    $bigText = 'Batch Rejected';
                    $image = 'red_exclaim.jpg';	
                }else if($Bstatus=='E'){
                    $bigText = 'Batch Accepted With Errors';
                    $image = 'green_exclaim.jpg';	
                }
                //pre($data2Show['result']);
                if($Bstatus=='A' || $Bstatus=='E'){?>
                <div class="row">
	        		<div class="col-sm-8">
                        <table class="table table-striped table-hover">
                        	<thead>
                            <tr><th colspan="4" class="bg-success"><big>Interchange Control#: <?php echo $Interchange_control;?></big></th></tr>
                            </thead>
                            <tr>
                                <th class="text-left">Submitter:</th>
                                <td><?php echo $EDI_data['Submitter'];?></td>
                                <th class="text-left">Report Date:</th>
                                <td><?php echo $report_date;?></td>
                            </tr>
                            <tr>
                                <th class="text-left">Receiver:</th>
                                <td><?php echo $EDI_data['Receiver'];?></td>
                                <th class="text-left"># of Encounters:</th>
                                <td><?php echo count($Encounters);?></td>
                            </tr>
                            <tr>
                                <th class="alignLeft" valign="top">Report Data</th>
                                <td colspan="3"><a href="#" id="rpt_data_link<?php echo $rpt_counter;?>" onClick="toggle_rpt_data(1,'<?php echo $rpt_counter;?>');" class="a_clr1">Click to view</a><textarea id="rpt_data_txt<?php echo $rpt_counter;?>" class="noshow_this" rows="8" cols="60"><?php echo $raw_report_data;?></textarea> <a href="#" id="rpt_dataX_link<?php echo $rpt_counter;?>" onClick="toggle_rpt_data(0,'<?php echo $rpt_counter;?>');" class="noshow_this a_clr1">X</a></td>
                        	</tr>
                        </table>
					</div>
                    <div class="col-sm-4"><h2 class="text-center"><image src="../../library/images/<?php echo $image;?>"> <?php echo $bigText;?></h2></div>
                </div>

					<?php }else if($Bstatus=='R'){?>
					<div class="m10">
						<div class="subheading">Interchange Control#: <?php echo $Interchange_control;?></div>
						<table class="table_collapse_autoW m10">
							<tr>
								<th style="width:130px;" class="alignLeft">Submitter:</th>
								<td style="width:250px;"><?php echo $EDI_data['Submitter'];?></td>
								<th style="width:130px;" class="alignLeft">Report Date:</th>
								<td style="width:180px;"><?php echo $report_date;?></td>
								<td style="width:300px;" class="alignRight valignTop" rowspan="4"><h2 style="margin:0px;" class="alignCenter"><image src="../../images/<?php echo $image;?>"> <?php echo $bigText;?></h2></td>
							</tr>
							<tr>
								<th class="alignLeft">Receiver:</th>
								<td><?php echo $EDI_data['Receiver'];?></td>
								<th class="alignLeft"># of Encounters:</th>
								<td><?php echo count($Encounters);?></td>
							</tr>
							<tr>
								<th class="alignLeft" valign="top">Report Data</th>
								<td colspan="3"><a href="#" id="rpt_data_link<?php echo $rpt_counter;?>" onClick="toggle_rpt_data(1,'<?php echo $rpt_counter;?>');" class="a_clr1">Click to view</a><textarea id="rpt_data_txt<?php echo $rpt_counter;?>" class="hide" rows="6" cols="70"><?php echo $raw_report_data;?></textarea> <a href="#" id="rpt_dataX_link<?php echo $rpt_counter;?>" onClick="toggle_rpt_data(0,'<?php echo $rpt_counter;?>');" class="hide a_clr1">X</a></td>
						</table>
						<div class="section">
							<div class="section_header">Rejection Reason</div><div class="m5">
							<?php
							$ARR_Errors		= $objEBilling->FilterErrors($arr_segments);
							//pre($ARR_Errors,1);
							foreach($ARR_Errors as $ARR_Error){
								if(isset($ARR_Error['IK3_AK3']) && strlen($ARR_Error['IK3_AK3'])==3){
									$errSegmentAt	= $ARR_Error['SegPos'];
									$errSegmentUpto	= $errSegmentAt+5;
									$errSegment		= $ARR_Error['SegID'];
									$Loop			= $ARR_Error['LoopID']!='' ? 'in  <b>loop#'.$ARR_Error['LoopID'].'</b>' : '';
									$IK3_AK3_MSG	= $ARR_Error['Msg3'];
									$errClaim		= $objEBilling->findValueFrom837EDI('PatientId',$errSegmentAt,$Loop,$ARR_batchData);
									if($errClaim){
										$patientID		= $errClaim[0];
										$ClaimAmount	= $errClaim[1];
										$patientDetails	= $objEBilling->get_patient_details(array('Patients'=>$patientID));
										$encounterID	= $objEBilling->findValueFrom837EDI('EncounterId',$errSegmentAt,$Loop,$ARR_batchData);
										$patientDetails	= $patientDetails[$patientID];
										//pre($patientDetails);
										//pre($ARR_Error);
										//pre($ARR_batchData);
										$clmInfo = true;
									}else{
										$clmInfo = false;
									}
									?>
									<?php if($clmInfo){?>
									<b>Error in Claim Data for Patient:</b> <?php echo core_name_format($patientDetails['lname'], $patientDetails['fname'], $patientDetails['mname']).' - '.$patientDetails['id']; ?><br>
									<b>Claim Amount:</b> $<?php echo $ClaimAmount;?><br>
									<b>Encouter ID:</b> <?php echo $encounterID;?><br>
									<?php }?>
									<b>Error Position:</b> Segment <b><?php echo $errSegment;?></b> at <b>line# <?php echo $errSegmentAt;?></b> <?php echo $Loop;?>. <?php echo $IK3_AK3_MSG;?><br>
									<!--<b>Error in Data:</b> <?php echo implode('*',$ARR_batchData[$errSegmentAt+1]);?><br>-->
								<?php
								}else if(isset($ARR_Error['IK4_AK4']) && strlen($ARR_Error['IK4_AK4'])==3){
									$ElementPos		= $ARR_Error['ElementPos'];
									$DataInErr		= $ARR_Error['DataInErr'];
									$IK4_AK4_MSG	= $ARR_Error['Msg4'];?>
									<b>Error Description:</b> Error at element position <b><?php echo $ElementPos;?></b> with value "<b><?php echo $DataInErr;?></b>". <?php echo $IK4_AK4_MSG;?><br><br>
							<?php			
								}else{?>
									<div class="m5">
										<b>No specific rejection code found. You may follow the steps given below:</b>
										<ul class="m2">
											<li>Regenerate the claim file.</li>
											<li>Send the claim file to clearing house.</li>
										</ul><br>
										If you already went through claim file regeneration process and again getting this rejection message then open a ticket with support staff.
									</div>
								<?php
								}
							}?>
							</div>
						</div>
						
					</div>
					
					<?php }?>
			<?php 
			}
		}?> 
    </div>
</div>

<div class="clearfix"></div>

<div class="text-center" id="bottom_buttons">
    <input type="button" class="btn btn-info" value="Close" onClick="window.close();">
</div>
<div class="clearfix"></div>

</body>
<?php if($objEBilling->MarkSubmitted){?>
<script type="text/javascript">
	window.onload = refresh_source;
	function refresh_source(){window.top.opener.loadResult();}

	if(typeof(window.opener.opener.top.innerDim)=='function'){
		var innerDim = window.opener.opener.top.innerDim();
		if(innerDim['w'] > 1000) innerDim['w'] = 1000; else innerDim['w'] = innerDim['w']-50;
		if(innerDim['h'] > 760) innerDim['h'] = 500; else innerDim['h'] = innerDim['h']-100;
		window.resizeTo(innerDim['w'],innerDim['h']);
	}
</script>
<?php }?>
</html>