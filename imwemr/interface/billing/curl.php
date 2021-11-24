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
File: curl.php
Purpose: To send 837 EDI data to EMDEON.
Access Type: Direct Access (data posted to this page) 
*/

require_once(dirname(__FILE__).'/../../config/globals.php');
require_once(dirname(__FILE__).'/../../library/classes/common_function.php');
require_once(dirname(__FILE__).'/../../library/classes/class.electronic_billing.php');
require_once(dirname(__FILE__).'/../../library/classes/SaveFile.php');
set_time_limit(360);
$objEBilling = new ElectronicBilling();
$oSaveFile = new SaveFile();
//--- GET BATCH FILE DETAILS ---
$fileDetails 			= $objEBilling->getBatchFileDetails($batch_file_submitte_id);
$group_id 				= $fileDetails['group_id'];
$emdeonfileName 		= $fileDetails['file_name'];
$file_data		 		= $fileDetails['file_data'];
$Interchange_control	= $fileDetails['Interchange_control'];


//GROUP DETAILS
$group_details 			= $objEBilling->get_groups_detail($group_id);

$is_institutional 		= intval($group_details['group_institution']);
$is_anesthesia 			= intval($group_details['group_anesthesia']);

$ClearingHouse			= $objEBilling->ClearingHouse();

$return = $objEBilling->submitClaimFile($ClearingHouse[0],$emdeonfileName,$file_data,$group_details);
if(is_array($return) && $return['response']!=''){
	$log_response = $objEBilling->log837CLresponse($ClearingHouse[0],$emdeonfileName,$batch_file_submitte_id,$group_details,$return['response']);
	if($ClearingHouse[0]['abbr']=='PI'){
		if($log_response['response']=='OK'){
			if(($log_response['report_type']=='999' || $log_response['report_type']=='997') && $log_response['report_id']){
				$objEBilling->batch_file_log($batch_file_submitte_id,"Submitted: 999 Received.");
				header("Location: electronic_billing_decode.php?eb_task=EDI2Human&report_id=".$log_response['report_id']."&report_type=comm");
				exit;
			}else{
				$objEBilling->batch_file_log($batch_file_submitte_id,"Submitted: ".strip_tags($log_response['response_text']));
				$fileDataStr = '<table class="table table-bordered table-stripped">
					<tr>
						<td align="right" width="200" class="text_b_w">Process Status :</td>
						<td align="left" width="750" class="text_b_w">'.$log_response['response'].'</td>
					</tr>
					<tr>
						<td align="right" width="200" class="text_b_w">Response Text :</td>
						<td align="left" width="750" class="text_b_w">'.$log_response['response_text'].'</td>
					</tr>
					</table>
					';
			}
		}
	}else{
		//------------CHANGE HEALTH CARE CASE----------
		$fileData2 = explode(',',$return['response']);
		$fileData = array();
		$fileData['file_name'] = $emdeonfileName;
		$fileData['batch_file_submitte_id'] = $batch_file_submitte_id;
		$fileData['file_from'] = 'Emdeon';
		$fileData['file_to'] = $fileData2[0];
		$fileData['file_Reference'] = $fileData2[1];
		$fileData['file_date'] = $fileData2[2];
		$fileData['file_subject'] = $fileData2[3];
		$fileData['file_size'] = $fileData2[4];
		$fileData['file_response'] = $fileData2[5];	
	
		$fileDataStr = '<table class="table table-bordered table-stripped">
			<tr>
				<td bgcolor="#4684ab" align="right" width="200" class="text_b_w">File Name :</td>
				<td bgcolor="#4684ab" align="left" width="750" class="text_b_w">'.$emdeonfileName.'</td>
			</tr>
			<tr>
				<td bgcolor="#FFFFFF" align="right" width="200" class="text_10b">From :</td>
				<td bgcolor="#FFFFFF" align="left" class="text_10">EMDEON</td>
			</tr>
			<tr>
				<td bgcolor="#FFFFFF" align="right" width="200" class="text_10b">To :</td>
				<td bgcolor="#FFFFFF" align="left" class="text_10">'.$fileData2[0].'</td>
			</tr>
			<tr>
				<td bgcolor="#FFFFFF" align="right" width="200" class="text_10b">Tracking Number :</td>
				<td bgcolor="#FFFFFF" align="left" class="text_10">'.$fileData2[1].'</td>
			</tr>
			<tr>
				<td bgcolor="#FFFFFF" align="right" width="200" class="text_10b">Reffer File :</td>
				<td bgcolor="#FFFFFF" align="left" class="text_10">'.$fileData2[3].'</td>
			</tr>
			<tr>
				<td bgcolor="#FFFFFF" align="right" width="200" class="text_10b">Date :</td>
				<td bgcolor="#FFFFFF" align="left" class="text_10">'.$fileData2[2].'</td>
			</tr>
			<tr>
				<td bgcolor="#FFFFFF" align="right" width="200" class="text_10b">Size :</td>
				<td bgcolor="#FFFFFF" align="left" class="text_10">'.$fileData2[4].'</td>
			</tr>
			<tr>
				<td bgcolor="#FFFFFF" align="right" width="200" class="text_10b">Response :</td>
				<td bgcolor="#FFFFFF" align="left" class="text_10">'.$fileData2[5].'</td>
			</tr>
		</table>
		';
		$objEBilling->batch_file_log($batch_file_submitte_id,"Submitted: ".$fileData2[5]);
	}
}
else{
	$fileDataStr = '
		<tr>
			<td bgcolor="#4684ab" class="text_10b" colspan="2" align="center">'.$return['error'].'</td>
		</tr>
	';
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
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
</head>
<body>
<div class="container-fluid mtb10">
    <div class="whitebox">
    	<div class="row">
	        <div class="col-sm-12">
				<div class="purple_bar"><big><b>SUBMIT CLAIM FILE - INTERCHANGE NUMBER: <?php echo $objEBilling->padd_string($Interchange_control,4,'0');?></b></big></div>
            	<div class="clearfix"></div>
                <br>
				<div class="col-sm-3"></div>
                <div class="col-sm-6"><?php print $fileDataStr;?></div>
				<div class="col-sm-3"></div>                
            </div>
		</div>
	</div>
    
    <div class="row">
    	<div class="col-sm-12 text-center">
            <button class="btn btn-info mlr10" onClick="javascript:history.go(-1)">Back</button>
            <button class="btn btn-warning" onClick="javascript:window.close();">Close Window</button>
         </div>
	</div>
    <div class="clearfix"></div>
</div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/common.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/billing_electronic.js"></script>
<script type="text/javascript">
if(typeof(window.opener.top.innerDim)=='function'){
	var innerDim = window.opener.top.innerDim();
	if(innerDim['w'] > 700) innerDim['w'] = 700;
	if(innerDim['h'] > 500) innerDim['h'] = 500;
	window.resizeTo(innerDim['w'],innerDim['h']);
	brows	= get_browser();
	if(brows!='ie') innerDim['h'] = innerDim['h']-35;
	var result_div_height = innerDim['h']-150;
	$('.whitebox').height(result_div_height+'px');
}
</script>
</body>
</head>