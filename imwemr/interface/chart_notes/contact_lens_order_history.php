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

/*
$contact_lens_view is defined in interface/optical/index.php
It is used to hide some elements in optical tab of this file 
*/
if(isset($_POST['ajax_req']) && $_POST['ajax_req'] == 'clorderhx') {
    $contact_lens_view = true;
}

//if(!isset($contact_lens_view)){
include_once(dirname(__FILE__)."/../../config/globals.php");
//Check patient session and closing popup if no patient in session
$window_popup_mode = true;
require_once($GLOBALS['srcdir']."/patient_must_loaded.php");
//}
$library_path = $GLOBALS['webroot'].'/library';
include_once("cl_functions.php");
include_once $GLOBALS['srcdir'].'/classes/common_function.php';


$authUserID = $_SESSION['authUserID'];
$displayCurrDate = get_date_format(date("m-d-Y"),'mm-dd-yyyy');

// GET LENSE CODES AND COLORS in ARRAY
$arrLensCode	=	getLensCodeArr(false);
$arrLensColor	=	getLensColorArr(false);
//---------------------------------

//GET ALL LENS MANUFACTURER IN ARRAY
$arrLensManuf = getLensManufacturer();


function getPatientDataRow($id){
	$sql = "SELECT * from patient_data where id = '$id'";
	$res = imw_query($sql);
	$row_address = @imw_fetch_array($res);
	return 	$row_address;
}

function changeDateFormat($selectDt) {
	$gtDate='';
	if($selectDt) {
		list($Mnt,$Dy,$Yr) = explode('-',$selectDt);
		if($Mnt && $Dy && $Yr) {
			$gtDate = $Yr.'-'.$Mnt.'-'.$Dy;
		}	
	}
	return $gtDate;
}

function displayDateFormat($selectDt) {
	$setDate='';
	if($selectDt && $selectDt!='0000-00-00') {
		list($Yr,$Mnt,$Dy) = explode('-',$selectDt);
		if($Yr && $Mnt && $Dy) {
			$setDate = date('m-d-Y',mktime(0,0,0,$Mnt,$Dy,$Yr));
		}	
	}
	return $setDate;
}
function displayDateFormatMMDDYY($selectDt) {
	$setDate='';
	if($selectDt && $selectDt!='0000-00-00') {
		list($Yr,$Mnt,$Dy) = explode('-',$selectDt);
		if($Yr && $Mnt && $Dy) {
			$setDate = date('m-d-y',mktime(0,0,0,$Mnt,$Dy,$Yr));
		}	
	}
	return $setDate;
}

//START FUCNTION TO GET USER INFORMATION
function getUsrNme($usrID,$initial='') {
	$userNme='';
	if($usrID) {
		$qryUserNme = "SELECT * from users where id = '".$usrID."'";
		$resUserNme = imw_query($qryUserNme);
		$rowUserNme = imw_fetch_array($resUserNme);
		$fname = $rowUserNme['fname'];
		$mname = $rowUserNme['mname'];
		$lname = $rowUserNme['lname'];
		if($mname != '' && $mname != 'NULL') {
			$userNme = $fname."&nbsp;".$mname."&nbsp;".$lname;
		}
		else {
			$userNme = $fname."&nbsp;".$lname;
		}
		
		if($initial='initial') {
			$userNme = ucfirst(substr($fname,0,1)).ucfirst(substr($lname,0,1));
		}
	}
	return $userNme;
}
//END FUCNTION TO GET USER INFORMATION



//START CODE TO GET AUTHORIZATION NUMBER
$unusedAuthorization='';
$AuthAmount='';
$authInfoQry = "
SELECT patient_auth.auth_name,patient_auth.AuthAmount 
FROM patient_auth,insurance_data
WHERE insurance_data.pid='".$_SESSION['patient']."'
AND insurance_data.type='primary'
AND insurance_data.auth_required='Yes'
AND insurance_data.id=patient_auth.ins_data_id
ORDER BY patient_auth.a_id DESC 
";
$authInfoRes = imw_query($authInfoQry) or die(imw_error());				
$authInfoNumRow = imw_num_rows($authInfoRes);
if($authInfoNumRow<=0) {
	$authInfoQry = "
	SELECT patient_auth.auth_name,patient_auth.AuthAmount
	FROM patient_auth,insurance_data
	WHERE insurance_data.pid='".$_SESSION['patient']."'
	AND insurance_data.type='secondary'
	AND insurance_data.auth_required='Yes'
	AND insurance_data.id=patient_auth.ins_data_id
	ORDER BY patient_auth.a_id DESC 
	";
	$authInfoRes = imw_query($authInfoQry) or die(imw_error());			
	$authInfoNumRow = imw_num_rows($authInfoRes);
}

if($authInfoNumRow>0) {
	$authInfoRow = imw_fetch_array($authInfoRes);
}	
//END CODE TO GET AUTHORIZATION NUMBER

//GET DETAIL FROM PRINT ORDER MASTER TABLE FOR SINGLE ROW FOR EVERY PRINT ORDER
$orderIDS = array();	$orderIDSStr='';
	$orderQuery= imw_query("SELECT  DATE_FORMAT(print_order_savedatetime, '".get_sql_date_format()."') as order_savedatetime, clprintorder_master.* 
	FROM clprintorder_master	WHERE patient_id='".$_SESSION['patient']."' ORDER BY clprintorder_master.print_order_savedatetime DESC");
	while($row = imw_fetch_array($orderQuery)){
		$clOrderMaster[] = $row;
	}
	$orderMasterNumRow = sizeof($clOrderMaster);
	if($orderMasterNumRow > 0)
	{
		for($i=0;$i<$orderMasterNumRow;$i++)
		{
			$orderIDS[] = $clOrderMaster[$i]['print_order_id'];
		}
		$orderIDSStr  =implode(",",$orderIDS);
	}
 


//START CODE TO GET ORDER HISTORY
$arr_clDetId = array();
$clOrderResOD = array();
$clOrderResOS = array();
$clOrderResOU = array();
$clOrderResArr = array();
if($_SESSION['patient'] && $orderIDSStr!=''){
	$orderHxQuery= imw_query("SELECT clprintorder_det.* FROM clprintorder_master
	LEFT JOIN clprintorder_det ON clprintorder_det.print_order_id =  clprintorder_master.print_order_id 
	WHERE clprintorder_det.print_order_id IN(".$orderIDSStr.") ORDER BY clprintorder_master.print_order_savedatetime DESC,clprintorder_det.id ASC");
	while($row = imw_fetch_array($orderHxQuery)){
		$clOrderRes[] = $row;
	}
	$orderHxNumRow = sizeof($clOrderRes);
	$clws_id = 	$clOrderRes[0]['clws_id'];
	for($i=0;$i< $orderHxNumRow; $i++)
	{
		if($clOrderRes[$i]['LensBoxOD_ID']>0)
		{
			$clOrderResOD[$clOrderRes[$i]['print_order_id']][] = $clOrderRes[$i];
		}
		if($clOrderRes[$i]['LensBoxOS_ID']>0)
		{
			$clOrderResOS[$clOrderRes[$i]['print_order_id']][] = $clOrderRes[$i];
		}

		if($clOrderRes[$i]['cl_det_id']!='' && $clOrderRes[$i]['cl_det_id']!=0){
			$arr_clDetId[] = 	$clOrderRes[$i]['cl_det_id'];
		}
	}
	$strClDetID = implode(",",$arr_clDetId);
}


$CLResDataArr = array();
if($strClDetID!=''){
	$workSheetQuery= imw_query("SELECT cm.clGrp, cm.clws_type, cm.clws_trial_number, cm.cpt_evaluation_fit_refit, cdet.* FROM contactlensmaster cm 
	LEFT JOIN contactlensworksheet_det cdet ON cdet.clws_id = cm.clws_id 
	WHERE cdet.id IN(".$strClDetID.")");
	while($row = imw_fetch_array($workSheetQuery)){
		$CLResData[] = $row; 
	}
	$clResSize = sizeof($CLResData);
	for($i=0; $i<$clResSize; $i++)
	{
		$clwID = $CLResData[$i]['clws_id'];
		$id = $CLResData[$i]['id'];
		$CLResDataArr[$clwID][$id] =  $CLResData[$i];
	}
}

//END CODE TO GET ORDER HISTORY

//START GET PATIENT DETAIL
if($_SESSION['patient']){
	$row_address = getPatientDataRow($_SESSION['patient']);
	
	//--- GET PATIENT NAME ---
	$patNameArr = array();
	$patNameArr['LAST_NAME'] = $row_address["lname"];
	$patNameArr['FIRST_NAME'] = $row_address["fname"];
	$patNameArr['MIDDLE_NAME'] = $row_address["mname"];
	$patientName = changeNameFormat($patNameArr);
}
//END GET PATIENT DETAIL
$spaceNbsp = '&nbsp;';
if(!isset($contact_lens_view)){
 ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<title>CONTACT LENS Print Order</title>
	<!-- Bootstrap -->
	<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
	<!-- Bootstrap Selctpicker CSS -->
	<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
	<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
	<link href="<?php echo $library_path; ?>/css/medicalhx.css" rel="stylesheet" type="text/css">
	<link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
	<!-- Messi Plugin for fancy alerts CSS -->
		<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
	<!-- DateTime Picker CSS -->
	<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>
	
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]--> 
	
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
	<!-- jQuery's Date Time Picker -->
	<script src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js" type="text/javascript" ></script>
	<!-- Bootstrap -->
	<script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>
	
	<!-- Bootstrap Selectpicker -->
	<script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
	<!-- Bootstrap typeHead -->
	<script src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js" type="text/javascript"></script>
	<script src="<?php echo $library_path; ?>/js/common.js" type="text/javascript"></script>
	<script src="<?php echo $library_path; ?>/messi/messi.js" type="text/javascript"></script>
	<style>
		.process_loader {
			border: 16px solid #f3f3f3;
			border-radius: 50%;
			border-top: 16px solid #3498db;
			width: 80px;
			height: 80px;
			-webkit-animation: spin 2s linear infinite;
			animation: spin 2s linear infinite;
			display: inline-block;
		}
		.adminbox{min-height:inherit}
		.adminbox label{overflow:initial;}
		.adminbox .panel-body{padding:5px}
		.adminbox div:nth-child(odd) {padding-right: 1%;}
		.od{color:blue;}
		.os{color:green;}
		.ou{color:#9900cc;}
	</style>
<script type="text/javascript">
	window.focus();
	if(parent.hide_btns) {
		parent.hide_btns();
	}	
</script>
</head>
<body>
<?php } ?>
	<div class="mainwhtbox">
		<div class="row">
			<?php if(!isset($contact_lens_view)){ ?>
			<div class="col-sm-12 purple_bar">
				<label>Contact Lens Order History</label>	
			</div>
			<?php } ?>
			<div class="col-sm-12 pt10">
				<div class="row">
					<div class="col-sm-3">
						<label><strong>Patient&nbsp;Name:&nbsp;</strong></label>
						<label><?php  echo $patientName; ?></label>	
					</div>

					<div class="col-sm-3">
						<label><strong>Pt ID:&nbsp;</strong></label>
						<label><?php echo $row_address["id"];?></label>		
					</div>

					<div class="col-sm-3">
						<label><strong>DOB:&nbsp;</strong></label>
						<label><?php if($row_address['DOB']!='0000-00-00') { echo get_date_format($row_address['DOB'],'yyyy-mm-dd'); } ?></label>		
					</div>

					<div class="col-sm-3">
						<label><strong>Tel: &nbsp;</strong></label>
						<label><?php echo core_phone_format($row_address["phone_home"]);?></label>		
					</div>	
				</div>	
			</div>
			<div class="col-sm-12 table-responsive" id="table_div" style="height:<?php echo ($_SESSION["wn_height"] - 500);?>px;overflow-x:scroll;">
					<table class="table table-bordered">
						<tr class="grythead" >
							<th class="text-center">Date</th>
							<th class="text-center">Eye</th>
							<th class="text-center">Type</th>
							<th class="text-center">Color</th>
							<th class="text-center">LC</th>
							<th class="text-center">S</th>
							<th class="text-center">C</th>
							<th class="text-center">A</th>
							<th class="text-center">Dia</th>
							<th class="text-center">BC</th>
							<th class="text-center">Add</th>
							<th class="text-center">Qty.</th>
							<th class="text-center">Cost</th>
							<th class="text-center">Dis</th>
							<th class="text-center">Balance</th>
							<th class="text-center">CL Exam.</th>
							<th class="lftRgtPad text-left text-nowrap">Auth Amt</th>
							<th class="text-center">Total</th>
							<th class="text-center">Auth <?php getHashOrNo();?></th>
							<th class="text-center">Comments</th>
							<th class="text-center">Order</th>
							<th class="text-center">Delivery At</th>
							<th class="text-center">Opr</th>
						</tr>
						<!-- Content -->
							<?php
								$print_order_id = $print_order_idOLD ='';
								if($orderHxNumRow>0) {
									for($i =0; $i<$orderMasterNumRow; $i++) {
										$deliveryAt = '';	$clExamAmt='';
										$clws_id = $clOrderMaster[$i]['clws_id'];
										$print_order_id  = $clOrderMaster[$i]['print_order_id'];						
										
										$unusedAuthorization = $clOrderMaster[$i]['auth_number'];
										$AuthAmount = $clOrderMaster[$i]['auth_amount'];
										$orderHxDate = $clOrderMaster[$i]['order_savedatetime'];
										$orderHxOperatorId = $clOrderMaster[$i]['operator_id'];
										$operatorInitial='';
										if($orderHxOperatorId) {
											$operatorInitial = getUsrNme($orderHxOperatorId,$initial='');
										}
										$print_AuthAmount = '';
										if($AuthAmount!='' && $AuthAmount!='0') { $print_AuthAmount = $dlr.$AuthAmount;}
										
										$displayOtherCmnt=' ';
										if($clOrderMaster[$i]['OrderedComment'])  {  $displayOtherCmnt.=$clOrderMaster[$i]['OrderedComment'].'<br><br>';}	//'Date Ordered: '.		
										if($clOrderMaster[$i]['ReceivedComment']) {  $displayOtherCmnt.=$clOrderMaster[$i]['ReceivedComment'].'<br><br>';}//'Date Received: '.
										if($clOrderMaster[$i]['NotifiedComment']) {  $displayOtherCmnt.=$clOrderMaster[$i]['NotifiedComment'].'<br><br>';}//'Date Notified: '.			
										if($clOrderMaster[$i]['PickedUpComment']) {  $displayOtherCmnt.=$clOrderMaster[$i]['PickedUpComment'].'<br><br>';}//'Date Picked Up: '.		
										
										if($clOrderMaster[$i]['checkBoxShipToHomeAddress']=='PtPickYes'){
											$deliveryAt = 'Office';
										}else if($clOrderMaster[$i]['checkBoxShipToHomeAddress']=='HomeAddressYes'){
											$deliveryAt = "Home<br><strong>Address:</strong><br>".$clOrderMaster[$i]['ShipToHomeAddress'];
										}
										$dlr = show_currency();
										//if($i%2==0){ $bgColor = "#ecdeec"; } else { $bgColor = "#F7E9F7";}
										$bgColor = "#ffffff";
										//OD Row section
										$site_string = '';
										$od_row_str = '';	
										if(sizeof($clOrderResOD[$print_order_id])>=0){
											
											$j=0;
											foreach($clOrderResOD[$print_order_id] as $clOrdData){
												if($print_order_id==$clOrdData['print_order_id']){
													$cl_det_id = $clOrdData['cl_det_id'];
													$orderHxType				='';
													$orderHxS					='';
													$orderHxC					='';
													$orderHxA					='';
													$orderHxDia					='';
													$orderHxBc					='';
													$orderHxAdd					='';
													//START CODE TO GET COLOR-NAME
													$colorNameList='';	$lensNameList='';
													$colorNameList 	= $arrLensColor[$clOrdData['colorNameIdList']];
													$lensNameList 	= $arrLensCode[$clOrdData['lensNameIdList']];
													
													$orderHxType= $arrLensManuf[$clOrdData['LensBoxOD_ID']]['det'];

													$clwSize = sizeof($CLResDataArr[$clws_id][$cl_det_id]);
													if($clwSize > 0){
														if($CLResDataArr[$clws_id][$cl_det_id]['clType']=='scl'){
															//$orderHxType= $CLResDataArr[$clws_id][$cl_det_id]['SclTypeOD'];
															$orderHxS 	= $CLResDataArr[$clws_id][$cl_det_id]['SclsphereOD'];
															$orderHxC	= $CLResDataArr[$clws_id][$cl_det_id]['SclCylinderOD'];
															$orderHxA 	= $CLResDataArr[$clws_id][$cl_det_id]['SclaxisOD']."&#176;";
															$orderHxDia = $CLResDataArr[$clws_id][$cl_det_id]['SclDiameterOD'];
															$orderHxBc 	= $CLResDataArr[$clws_id][$cl_det_id]['SclBcurveOD'];
															$orderHxAdd = $CLResDataArr[$clws_id][$cl_det_id]['SclAddOD'];
														}
														if($CLResDataArr[$clws_id][$cl_det_id]['clType']=='rgp'){
															//$orderHxType= $CLResDataArr[$clws_id][$cl_det_id]['RgpTypeOD'];
															$orderHxS 	= $CLResDataArr[$clws_id][$cl_det_id]['RgpPowerOD'];
															$orderHxC	= $CLResDataArr[$clws_id][$cl_det_id]['RgpCylinderOD'];
															$orderHxA 	= $CLResDataArr[$clws_id][$cl_det_id]['RgpAxisOD'];
															$orderHxDia = $CLResDataArr[$clws_id][$cl_det_id]['RgpDiameterOD'];
															$orderHxBc 	= $CLResDataArr[$clws_id][$cl_det_id]['RgpBCOD'];
															$orderHxAdd = $CLResDataArr[$clws_id][$cl_det_id]['RgpAddOD'];
														}
														if($CLResDataArr[$clws_id][$cl_det_id]['clType']=='cust_rgp'){
															//$orderHxType= $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomTypeOD'];
															$orderHxS 	= $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomPowerOD'];
															$orderHxC	= $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomCylinderOD'];
															$orderHxA 	= $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomAxisOD'];
															$orderHxDia = $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomDiameterOD'];
															$orderHxBc 	= $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomBCOD'];
															$orderHxAdd = $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomAddOD'];
														}

														$LabelsTrial="";
														if($CLResDataArr[$clws_id][$cl_det_id]['clws_type']=="Current Trial"){
															$LabelsTrial="<b> (Trial ".$CLResDataArr[$clws_id][$cl_det_id]['clws_trial_number'].")</b>";
														}
														$orderHxType.=$LabelsTrial;	
														
														//GET CL EXAM AMOUNT
														$clExamAmt = $CLResDataArr[$clws_id][$cl_det_id]['cpt_evaluation_fit_refit'];
													}
													$SubTotalOD = '';
													$DiscountOD = '';
													$BalanceOD = '';
													if($clOrdData['SubTotalOD']>0){ $SubTotalOD = $dlr.$clOrdData['SubTotalOD'];}
													if($clOrdData['DiscountOD']>0){ $DiscountOD = $dlr.$clOrdData['DiscountOD']; }
													if($clOrdData['BalanceOD']!='' && $clOrdData['BalanceOD']!=0){ $BalanceOD = $dlr.$clOrdData['BalanceOD'];}
													
													if($j > 0){
														$od_row_str .= '<tr class="valign-top" style="background-color:'.$bgColor.'">';
													}
													
													$od_row_str .= '
														<td class="text-left od">OD</td>
														<td class="text-left">'.$orderHxType.'</td>
														<td class="text-left">'.$colorNameList.'</td>
														<td class="text-left f-bold">'.$lensNameList.'</td>
														<td class="f-bold">'.$orderHxS.'</td>
														<td class="f-bold">'.$orderHxC.'</td>
														<td class="f-bold">'.$orderHxA.'</td>
														<td class="f-bold">'.$orderHxDia.'</td>
														<td class="f-bold">'.$orderHxBc.'</td>
														<td class="f-bold">'.$orderHxAdd.'</td>
														<td>'.$clOrdData['QtyOD'].'</td>
														<td>'.$SubTotalOD.'</td>
														<td>'.$DiscountOD.'</td>
														<td>'.$BalanceOD.'</td>';
													
													//If OS row is empty show rest of the fields here		
													if(sizeof($clOrderResOS[$print_order_id]) == 0){
														$oth_fields = '<td>'.$clExamAmt.'</td>
														<td class="text-nowrap">'.$print_AuthAmount.'</td>        
														<td>'.$dlr.$clOrderMaster[$i]['totalCharges'].'</td>
														<td>'.$unusedAuthorization.'</td>
														<td class="text-left">'.$lensComment.'<br>'.$displayOtherCmnt.'</td>
														<td>'.$clOrderMaster[$i]['OrderedTrialSupply'].'</td>
														<td class="text-left">'.$deliveryAt.'</td>
														<td class="text-left">'.$operatorInitial.'</td>';
														$od_row_str .= $oth_fields;
													}else{
														$oth_fields = '<td colspan="8">&nbsp;</td>';
														$od_row_str .= $oth_fields;	
													}
													$od_row_str .= '</tr>';													
												}
												$j++;
											}
										}

										//OS Row section
										$os_row_str = '';
										if(sizeof($clOrderResOS[$print_order_id])>0){
											$j=0;
												
											foreach($clOrderResOS[$print_order_id] as $clOrdData){
												if($print_order_id==$clOrdData['print_order_id']){

													$cl_det_id = $clOrdData['cl_det_id'];
													$orderHxType				='';
													$orderHxS					='';
													$orderHxC					='';
													$orderHxA					='';
													$orderHxDia					='';
													$orderHxBc					='';
													$orderHxAdd					='';
													//START CODE TO GET COLOR-NAME
													$colorNameList='';	$lensNameList='';
													$colorNameList 	= $arrLensColor[$clOrdData['colorNameIdListOS']];
													$lensNameList 	= $arrLensCode[$clOrdData['lensNameIdListOS']];
													
													$orderHxType= $arrLensManuf[$clOrdData['LensBoxOS_ID']]['det'];
													
													$clwSize = sizeof($CLResDataArr[$clws_id][$cl_det_id]);
													if($clwSize > 0){
														if($CLResDataArr[$clws_id][$cl_det_id]['clType']=='scl'){
															//$orderHxType= $CLResDataArr[$clws_id][$cl_det_id]['SclTypeOS'];
															$orderHxS 	= $CLResDataArr[$clws_id][$cl_det_id]['SclsphereOS'];
															$orderHxC	= $CLResDataArr[$clws_id][$cl_det_id]['SclCylinderOS'];
															$orderHxA 	= $CLResDataArr[$clws_id][$cl_det_id]['SclaxisOS']."&#176;";
															$orderHxDia = $CLResDataArr[$clws_id][$cl_det_id]['SclDiameterOS'];
															$orderHxBc 	= $CLResDataArr[$clws_id][$cl_det_id]['SclBcurveOS'];
															$orderHxAdd = $CLResDataArr[$clws_id][$cl_det_id]['SclAddOS'];
														}
														if($CLResDataArr[$clws_id][$cl_det_id]['clType']=='rgp'){
															//$orderHxType= $CLResDataArr[$clws_id][$cl_det_id]['RgpTypeOS'];
															$orderHxS 	= $CLResDataArr[$clws_id][$cl_det_id]['RgpPowerOS'];
															$orderHxC	= $CLResDataArr[$clws_id][$cl_det_id]['RgpCylinderOS'];
															$orderHxA 	= $CLResDataArr[$clws_id][$cl_det_id]['RgpAxisOS'];
															$orderHxDia = $CLResDataArr[$clws_id][$cl_det_id]['RgpDiameterOS'];
															$orderHxBc 	= $CLResDataArr[$clws_id][$cl_det_id]['RgpBCOS'];
															$orderHxAdd = $CLResDataArr[$clws_id][$cl_det_id]['RgpAddOS'];
														}
														if($CLResDataArr[$clws_id][$cl_det_id]['clType']=='cust_rgp'){
															//$orderHxType= $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomTypeOS'];
															$orderHxS 	= $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomPowerOS'];
															$orderHxC	= $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomCylinderOS'];
															$orderHxA 	= $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomAxisOS'];
															$orderHxDia = $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomDiameterOS'];
															$orderHxBc 	= $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomBCOS'];
															$orderHxAdd = $CLResDataArr[$clws_id][$cl_det_id]['RgpCustomAddOS'];
														}

														$LabelsTrial="";
														if($CLResDataArr[$clws_id][$cl_det_id]['clws_type']=="Current Trial"){
															$LabelsTrial="<b> (Trial ".$CLResDataArr[$clws_id][$cl_det_id]['clws_trial_number'].")</b>";
														}
														$orderHxType.=$LabelsTrial;	

														//GET CL EXAM AMOUNT
														$clExamAmt = $CLResDataArr[$clws_id][$cl_det_id]['cpt_evaluation_fit_refit'];
													}
													$SubTotalOS = '';
													$DiscountOS = '';
													$BalanceOS = '';
													if($clOrdData['SubTotalOS']>0){ $SubTotalOD = $dlr.$clOrdData['SubTotalOS'];}
													if($clOrdData['DiscountOS']>0){ $DiscountOD = $dlr.$clOrdData['DiscountOS']; }
													if($clOrdData['BalanceOS']!='' && $clOrdData['BalanceOS']!=0){ $BalanceOS = $dlr.$clOrdData['BalanceOS'];}
													if(sizeof($clOrderResOD[$print_order_id]) > 0){
														$os_row_str .= '<tr class="valign-top" style="background-color:'.$bgColor.'">';
													}
													$os_row_str .='
														<td class="text-left os">OS</td>
														<td class="text-left">'.$orderHxType.'</td>
														<td class="text-left">'.$colorNameList.'</td>
														<td class="text-left f-bold">'.$lensNameList.'</td>
														<td class="f-bold">'.$orderHxS.'</td>
														<td class="f-bold">'.$orderHxC.'</td>
														<td class="f-bold">'.$orderHxA.'</td>
														<td class="f-bold">'.$orderHxDia.'</td>
														<td class="f-bold">'.$orderHxBc.'</td>
														<td class="f-bold">'.$orderHxAdd.'</td>
														<td>'.$clOrdData['QtyOS'].'</td>
														<td>'.$SubTotalOD.'</td>
														<td>'.$DiscountOD.'</td>
														<td>'.$BalanceOD.'</td>';
													//If OD row exists show other fields in this row	
													if(sizeof($clOrderResOD[$print_order_id]) == 0 || sizeof($clOrderResOD[$print_order_id]) > 0 && sizeof($clOrderResOS[$print_order_id]) > 0){
														$oth_fields = '<td>'.$clExamAmt.'</td>
														<td class="text-nowrap">'.$print_AuthAmount.'</td>        
														<td>'.$dlr.$clOrderMaster[$i]['totalCharges'].'</td>
														<td>'.$unusedAuthorization.'</td>
														<td class="text-left">'.$lensComment.'<br>'.$displayOtherCmnt.'</td>
														<td>'.$clOrderMaster[$i]['OrderedTrialSupply'].'</td>
														<td class="text-left">'.$deliveryAt.'</td>
														<td class="text-left">'.$operatorInitial.'</td>';
														$os_row_str .= $oth_fields;
													}	
													$os_row_str	.= '</tr>';
												}
												$j++;
											}
										}
										
										$site_string .= $od_row_str.$os_row_str;
										
										//Main Row
										$rowspan = (sizeof($clOrderResOD[$print_order_id])+sizeof($clOrderResOS[$print_order_id]));
										$main_row = '
											<tr class="valign-top" style="background-color:'.$bgColor.'">
												<td rowspan='.$rowspan.' class="text-nowrap">'.$orderHxDate.$site_string.'</td>
											</tr>
										';
										echo $main_row;
									}
								}
							?>	
					</table>	
			</div>	
		</div>	
	</div>
	<?php if(!isset($contact_lens_view)){ ?>
<script type="text/javascript">
if(parent.show_loading_image) {
	parent.show_loading_image('none');
}</script>
	<?php } ?>