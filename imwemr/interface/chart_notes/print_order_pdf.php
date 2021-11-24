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
?>
<?php 
ob_start();
include_once('../../config/globals.php');
include_once(dirname(__FILE__)."/../../library/classes/SaveFile.php");
include_once(dirname(__FILE__)."/../../library/classes/common_function.php");
include_once("cl_functions.php");

$authUserID = $_SESSION['authUserID'];
$dos = $_REQUEST['dos'];//mm-dd-YYYY
$print_order_id = $_REQUEST['print_order_id'];
$arr_LensCode 	= getLensCodeArr(false);
$arr_LensColor 	= getLensColorArr(false);
$arrLensManuf = getLensManufacturer();

//Insurance Case
$insuranceCaseTypesArray = array();     // Array for holding insurance case type id and insurance case name
$insuranceCaseQuery = "Select case_id, case_name from insurance_case_types order by case_name asc";     // Query for getting insurance case type id and name 
$insuranceCaseQueryResult = imw_query($insuranceCaseQuery);
while($insuranceCaseQueryRow = imw_fetch_array($insuranceCaseQueryResult))
{
    $insuranceCaseTypesArray[trim($insuranceCaseQueryRow['case_id'])] = trim($insuranceCaseQueryRow['case_name']);  // Put case id as key and case name as value in $insuranceCaseTypesArray
}

$physicianArray = array();      // Array for holding reffering physician data
$refferPhysicianQuery = "select physician_Reffer_id, FirstName, MiddleName, LastName from refferphysician"; // Query to get reffering physician data
$refferPhysicianQueryResult = imw_query($refferPhysicianQuery);
while($row = imw_fetch_array($refferPhysicianQueryResult)){
    $physicianId = $row['physician_Reffer_id'];     // Get physician id from resultset
    $physicianArray[$physicianId]['fname'] =  $row['FirstName'];    // Add first name in array
    $physicianArray[$physicianId]['mname'] =  $row['MiddleName'];   // Add middle name in array
    $physicianArray[$physicianId]['lname'] =  $row['LastName'];     // Add last name in array
}

$patientDOBQuery = "Select primary_care_phy_id, DOB, TRIM(phone_home) as phone_home, TRIM(phone_cell) as phone_cell, TRIM(phone_biz) as phone_biz from patient_data where id = '".$_SESSION['patient']."'";   // Query to get patient details
$patientDOBQueryResult = imw_query($patientDOBQuery);   // Execute query
$rs1 = imw_fetch_assoc($patientDOBQueryResult);
$patientDOB = $rs1['DOB'];      // Get patient DOB
$patientDOBArray = explode("-", $patientDOB);
$patientDOB = $patientDOBArray[1]."-".$patientDOBArray[2]."-".$patientDOBArray[0];
//1969-12-26
$patientHomePhone = $rs1['phone_home'];     // Get patient home phone
$patientCellPhone = $rs1['phone_cell'];     // Get patient cell phone
$patientBizPhone = $rs1['phone_biz'];     // Get patient business phone
$patientPhone = "";
if(strlen($patientHomePhone) > 0){
    $patientPhone = $patientHomePhone;
}else if(strlen($patientCellPhone) > 0){
    $patientPhone = $patientCellPhone;
}else if(strlen($patientBizPhone) > 0){
    $patientPhone = $patientBizPhone;
}
$primaryCarePhysicianId = $rs1['primary_care_phy_id'];      // Get patient's primary care physician's id

function mysqlifetchdata($qry){
    $return = array();
    $query_sql = imw_query($qry);
    while($row = imw_fetch_array($query_sql)){
        $return[] = $row;
    }
    return $return;
}
function getPatientNames($IDorARRAY){
	$patient_id = ''; $returnType = "str"; 
	if(is_array($IDorARRAY) && count($IDorARRAY)>0){
		$patient_id = implode(',',$IDorARRAY);
		$returnType = "arr";
	}else{
		$patient_id = $IDorARRAY;
	}
	$query = "SELECT lname, fname, mname, id  FROM patient_data WHERE id IN (".$patient_id.")";
	$result = imw_query($query);
	
	if($returnType=='str'){
		$patient_name = '';
	}
	else if($returnType=='arr'){
		$patient_name = array();
	}
	
	while($rs = imw_fetch_assoc($result))
	{
		$str_patient_name = '';
		if(trim($rs['lname']) != ''){
			$str_patient_name .= ucfirst(trim($rs['lname']));
		}
		if(trim($rs['fname']) != '' && $str_patient_name != ''){
			$str_patient_name .= ', '.ucfirst(trim($rs['fname']));
		}
		if(trim($rs['mname']) != ''){
			$str_patient_name .= ' '.strtoupper(substr(trim($rs['mname']),0,1)).'.';
		}
		if(!empty($str_patient_name)===true){
			$str_patient_name .= ' - '.$rs['id'];
		}

		if($returnType=='str'){
			$patient_name = $str_patient_name;
		}
		else if($returnType=='arr'){
			$patient_name[] = $str_patient_name;
		}
	}//end of while.
	return $patient_name;
}


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
			//$setDate = date('Y-m-d',mktime(0,0,0,$Mnt,$Dy,$Yr));
		}	
	}
	return $gtDate;
}

function displayDateFormat($selectDt) {
	$setDate='';
	if($selectDt && $selectDt!='0000-00-00') {
		list($Yr,$Mnt,$Dy) = explode('-',$selectDt);
		if($Yr && $Mnt && $Dy) {
			//$setDate = $Mnt.'-'.$Dy.'-'.$Yr;
			$setDate = date('m-d-Y',mktime(0,0,0,$Mnt,$Dy,$Yr));
		}	
	}
	return $setDate;
}
//START FUCNTION TO GET USER INFORMATION
function getUsrNme($usrID) {
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
	}
	return $userNme;
}
//END FUCNTION TO GET USER INFORMATION



//START CODE TO VIEW RECORD
//if($_SESSION['patient'] && $_REQUEST["clws_id"]!=""){
    $patientAuthNumber = "";
    $patientAuthAmount = "";
	if($print_order_id){       // If valid order id
	    
	    /*********** Patient authorization from patient_auth table **********/
	    $insuranceCaseType = "";
	    $patientAuthNumber = "";
	    $patientAuthAmount = 0;
		$patientDiscount = 0;
		
		//START CODE TO GET AUTHORIZATION NUMBER
		$authInfoQry = "
		SELECT patient_auth.auth_name,patient_auth.AuthAmount  
		FROM patient_auth,insurance_data
		WHERE insurance_data.pid='".$_SESSION['patient']."'
		AND insurance_data.type='primary'
		AND insurance_data.auth_required='Yes'
		AND insurance_data.id=patient_auth.ins_data_id
		ORDER BY patient_auth.a_id DESC";

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
			$patientAuthNumber = $authInfoRow['auth_name'];
			$patientAuthAmount = $authInfoRow['AuthAmount'];
			if(!$print_order_id){
				$patientAuthNumber = $authInfoRow['auth_name'];
				$patientAuthAmount = $authInfoRow['AuthAmount'];
			}
		}
	    
	    // Query for getting authentican details for current patient from 'patient_auth' table
	    /* $patientAuthQuery = "select auth_name, AuthAmount from patient_auth where patient_id='".$_SESSION['patient']."' order by patient_id DESC limit 1";
	    $patientAuthResult = imw_query($patientAuthQuery);     // Execute query
	    $patientAuthRow = imw_fetch_array($patientAuthResult);
	    $patientAuthNumber = $patientAuthRow['auth_name'];     // Get patient's authentication number
	    $patientAuthAmount = $patientAuthRow['AuthAmount'];    // Get patient's authentication amount */
	    /*********** Patient authorization from patient_auth table ends **********/
	    
	    // Query for getting details for current order
	    $GetPrintDataQuery = "SELECT clprintorder_master.*, clprintorder_master.clws_id as 'CLWSID', clprintorder_master.print_order_id as 'PRINTORDERID', clprintorder_det.*, clprintorder_det.id as 'orDetId' FROM clprintorder_master
		LEFT JOIN clprintorder_det ON clprintorder_det.print_order_id = clprintorder_master.print_order_id
		WHERE clprintorder_master.print_order_id='".$print_order_id."' ORDER BY clprintorder_det.id";
	    
	    $clOrderRes = mysqlifetchdata($GetPrintDataQuery);     // Execute query
	    $GetPrintDataNumRow = sizeof($clOrderRes);
	    
	    $clTeachDate = $clOrderRes[0]['clTeachDate'];          // Get cl teach from resultset
	    $clEvaluationDate = $clOrderRes[0]['clEvaluationDate'];        // Get cl evaluation date from resultset
	    $clFittingDate = $clOrderRes[0]['clFittingDate'];              // Get cl fitting date from resultset
	    $messageTo = $clOrderRes[0]['messageTo'];
	    $insuranceCaseType = $clOrderRes[0]['ins_case'];           // Get insurance case type
	    
		//$GetPrintDataQuery= "SELECT * FROM clprintorder_det WHERE patient_id='".$_SESSION['patient']."' ".$andPrintOrderQry." ORDER BY print_order_savedatetime DESC LIMIT 0,1";
		$GetPrintDataQuery= "SELECT clpm.prescribed_by as provider_id, clpm.print_order_id, clpm.clws_id, clpm.technician_id, clpm.operator_id, clpm.dos, 
							clpm.clEvaluationDate, clpm.clEvaluationComment, clpm.clFittingDate, clpm.clFittingComment, clpm.clTeachDate, 
							clpm.clTeachComment, clpm.checkBoxShipToHomeAddress, clpm.ShipToHomeAddress, clpm.dateOrdered, clpm.OrderedTrialSupply, 
							clpm.OrderedComment, clpm.dateReceived, clpm.ReceivedTrialSupply, clpm.ReceivedComment, clpm.dateNotified, 
							clpm.NotifiedComment, clpm.datePickedUp, clpm.PickedUpTrialSupply, clpm.PickedUpComment, clpm.clSupply, 
							clpm.totalCharges, clpm.order_status, SUM(clp.QtyOD) as QtyOD, SUM(clp.QtyOS) as QtyOS, messageComments FROM clprintorder_master clpm 
							JOIN clprintorder_det clp ON (clp.print_order_id = clpm.print_order_id) 
							WHERE clpm.print_order_id='".$print_order_id."' ORDER BY clp.id";
		$GetPrintDataRes =  imw_query($GetPrintDataQuery) or die(imw_error());
		$GetPrintDataNumRow = imw_num_rows($GetPrintDataRes);
		if($GetPrintDataNumRow>0){
			$GetPrintDataRow=@imw_fetch_assoc($GetPrintDataRes);
			@extract($GetPrintDataRow);
			$messageComment = $GetPrintDataRow['messageComments'];
			$provider_id=($provider_id>0)?$provider_id:$_SESSION["authUserID"];
		}
		$providerName 	=($provider_id>0)?getUsrNme($provider_id):getUsrNme($technician_id);	//GET DOCTOR NAME	
		$technicianName =($technician_id>0)?getUsrNme($technician_id):getUsrNme($operator_id);	//GET Technician NAME	
		$operatorName 	=($operator_id>0)? getUsrNme($operator_id):getUsrNme($provider_id);	//GET Operator NAME	
		if($prescripClwsId>0){
			$clws_id=$prescripClwsId;
		}
		//START CODE TO GET WORKSHEET DETAIL
		$workSheetQuery= "SELECT clm.clws_id, clm.clGrp, clm.clws_type, clm.currentWorksheetid FROM contactlensmaster clm 
						  JOIN contactlensworksheet_det clw ON (clw.clws_id = clm.clws_id) WHERE clm.clws_id='".$clws_id."' ORDER BY clw.id";
		$workSheetRes = imw_query($workSheetQuery) or die(imw_error());
		$workSheetNumRow = imw_num_rows($workSheetRes);
		if($workSheetNumRow>0){
			$workSheetRow=imw_fetch_array($workSheetRes);
			
			$clws_typeNewValue='';
			
			if($workSheetRow['clws_type']=='Evaluation') {
				$clws_typeNewValue = 'Evaluation';
			}else if($workSheetRow['clws_type']=='Current Trial') {
				$clws_typeNewValue = 'Trial'.$workSheetRow['clws_trial_number'];
			}else if($workSheetRow['clws_type']=='Final') {
				$clws_typeNewValue = 'Final';
			}
			//clws_savedatetime
		
		}
		//END CODE TO GET WORKSHEET DETAIL
	}	
//}
//END CODE TO VIEW RECORD

//START CODE TO GET ORDER HISTORY color:#CC3300;
if($_SESSION['patient']){
	$row_address = getPatientDataRow($_SESSION['patient']);
}


$pdf_css = '
<style>
.tb_heading{
	font-size:11px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#FFFFFF;
	background-color:#4684AB;
}
.text{
	font-size:11px;
	font-family:Arial, Helvetica, sans-serif;
	background-color:#FFFFFF;
}
.textBold{
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
}
.text_9{
	font-size:11px;
	font-family:Arial, Helvetica, sans-serif;
}
.text_b_w{
	font-size:11px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#000000;
	background-color:#CCCCCC;
}
.text_blue_w{
	font-size:11px;
	font-family:Arial, Helvetica, sans-serif;
	color:#0000FF;
} 
.text_green_w{
	font-size:11px;
	color:#006600;
	font-family:Arial, Helvetica, sans-serif;
}
.ou{color:#9900CC;}	

</style>';

$cl_pdf_ptname = getPatientNames($row_address["id"]);
if($workSheetRow["clws_type"] =="Evaluation")
{
	$pdf_clws_type .= "Evaluation";
}
else if($workSheetRow["clws_type"] =="Current Trial")
{
	$pdf_clws_type .= "Trial #".$workSheetRow["clws_trial_number"];
}
else if($workSheetRow["clws_type"] !="Evaluation")
{
	$pdf_clws_type .= $workSheetRow["clws_type"];
}

if($dos != ""){
	$pdf_dos .= displayDateFormat($dos);
}
$pdf_now = date("m-d-Y");
$HomeAddress = trim(stripslashes($row_address['street'])).'&nbsp;'.trim(stripslashes($row_address['city'].", ".$row_address['state']." ".$row_address['postal_code']));

$patientFullName = $row_address['lname'].", ".$row_address['fname']." ".$row_address['mname'];

$pdf_page = '
<page>
	<table cellspacing="0" cellpadding="0">
        <tr>
          <td width="220" align="left" class="tb_heading">Print Order</td>
          <td width="215" style="text-align:center;" class="tb_heading">'.$pdf_clws_type.'</td>
          <td width="215" style="text-align:center;" class="tb_heading">'.$cl_pdf_ptname.'</td>
          <td width="210" style="text-align:center;" class="tb_heading">DOS: '.$pdf_dos.'</td>
          <td width="200" class="tb_heading" style="text-align:right;">Date: '.$pdf_now.'</td>
        </tr>
    </table>
	<table cellpadding="0" cellspacing="0">
	  <tr>
	  	<td width="450" class="text_9"><font class="textBold">Patient:</font> '.stripslashes($patientFullName).'</td>
		<td width="410" class="text_9"><font class="textBold">Technician:</font> '.stripslashes($technicianName).'</td>
		<td width="210" class="text_9"><font class="textBold">Operator:</font> '.stripslashes($operatorName).'</td>
	  </tr>
	</table>
	<table cellpadding="0" cellspacing="0" class="text_9">
	  <tr><td colspan="4" style="margin-bottom:15px;"><font class="textBold">Dr.:</font>'.stripslashes($providerName).'</td></tr>
	  <tr>
	  	<td width="665"><font class="textBold">Address:</font> '.$cl_pdf_ptname.'<br>'.$HomeAddress.'</td>
		<td width="100" class="textBold">No. of boxes:</td>
        <td width="100"><font class="text_blue_w">OD: </font>'.$QtyOD.'</td>
        <td width="100"><font class="text_green_w">OS: </font>'.$QtyOS.'</td>
	  </tr>
	  <tr><td colspan="4" style="margin-bottom:15px;"><font class="textBold">Patient phone: </font>'.$patientPhone.'</td></tr>

	  <tr><td colspan="4" class="tb_heading">Order Details</td></tr>
	</table>
	';
	
$pdf_page .= '
	<table border="0" cellpadding="2" cellspacing="5">';
	if($clEvaluationDate!='0000-00-00' || $clEvaluationComment!=""){
		$pdf_page .= '
		<tr>
			<td width="100" class="textBold">Evaluation/Fit:</td>
			<td width="60" class="text_9">'.displayDateFormat($clEvaluationDate).'</td>
			<td width="890" class="text_9">'.stripslashes($clEvaluationComment).'</td>
		</tr>';
	}
	if($clFittingDate!='0000-00-00' || $clFittingComment!=""){
		$pdf_page .= '
		<tr>
			<td width="100" class="textBold">CL Fitting:</td>
			<td width="60" class="text_9">'.displayDateFormat($clFittingDate).'</td>
			<td width="890" class="text_9">'.stripslashes($clFittingComment).'</td>
		</tr>';
	}
	if($clTeachDate!='0000-00-00' || $clTeachComment!=""){
		$pdf_page .= '
		<tr>
			<td width="100" class="textBold">CL Teach:</td>
			<td width="60" class="text_9">'.displayDateFormat($clTeachDate).'</td>
			<td width="890" class="text_9">'.stripslashes($clTeachComment).'</td>
		</tr>';
	}
$pdf_page .= '
	</table><br>
	
	<table cellpadding="0" cellspacing="0" class="text_9">
	  <tr class="text_b_w textBold">
			<td width="35">&nbsp;</td>
			<td width="270">Type</td>
			<td width="100">Lens Code</td>
			<td width="100">Lens Color</td>
			<td width="50">Price</td>
			<td width="40">Qty</td>
			<td width="70">Sub Total </td>
			<td width="70">Discount</td>
			<td width="70">Total</td>
			<td width="60">Ins.</td>
			<td width="80">Balance</td>
	   </tr>
	';
	$GetPrintDataQuery3= "SELECT clp.LensBoxOD, clp.LensBoxOD_ID, clp.lensNameIdList, clp.colorNameIdList, clp.PriceOD, clp.QtyOD, clp.SubTotalOD, clp.DiscountOD,
						clp.TotalOD, clp.PaidOD, clp.InsOD, clp.BalanceOD, clp.LensBoxOS, clp.LensBoxOS_ID, clp.lensNameIdListOS, clp.colorNameIdListOS, clp.PriceOS, clp.QtyOS, clp.SubTotalOS, clp.DiscountOS, clp.TotalOS, clp.PaidOS, clp.InsOS, clp.BalanceOS 
						FROM clprintorder_det clp JOIN clprintorder_master clpm ON (clp.print_order_id = clpm.print_order_id) 
						WHERE clpm.print_order_id='".$print_order_id."' ORDER BY clp.id";
	$GetPrintDataRes3 = imw_query($GetPrintDataQuery3) or die(imw_error());
	$totalBalanceOSOD = 0;
while($rsPrintDataRes3 = imw_fetch_array($GetPrintDataRes3)){
	extract($rsPrintDataRes3);
	if($PriceOD != ''){
	$totalBalanceOSOD += $BalanceOD;
		$pdf_page .= '
	   <tr>
			<td width="35" align="left" class="text_blue_w" width="18">OD:</td>
			<td width="270" align="left" class="text_9">'.$arrLensManuf[$LensBoxOD_ID]['det'].'</td>
			<td width="100" align="left" class="text_9">'.$arr_LensCode[$lensNameIdList].'</td>
			<td width="100" align="left" class="text_9" >'.$arr_LensColor[$colorNameIdList].'</td> 
			<td width="50" align="left" class="text_9" width="17">'.$PriceOD.'</td>
			<td width="40" align="left" class="text_9">'.$QtyOD.'</td>
			<td width="70" align="left" class="text_9">'.$SubTotalOD.'</td>
			<td width="70" align="left" class="text_9" width="18">'.$DiscountOD.'</td>
			<td width="70" align="left" class="text_9" width="17">'.$TotalOD.'</td>
			<td width="60" align="left" class="text_9">'.$InsOD.'</td>
			<td width="80" align="left" class="text_9">'.$BalanceOD.'</td>
		</tr>';
		$patientDiscount = $DiscountOD;
	}
	else if($PriceOS != ''){
		$totalBalanceOSOD += $BalanceOS;
		$pdf_page .= '
	   <tr>
			<td width="35" align="left" class="text_green_w" width="18">OS:</td>
			<td width="270" align="left" class="text_9">'.$arrLensManuf[$LensBoxOS_ID]['det'].'</td>
			<td width="100" align="left" class="text_9">'.$arr_LensCode[$lensNameIdListOS].'</td>
			<td width="100" align="left" class="text_9" >'.$arr_LensColor[$colorNameIdListOS].'</td> 
			<td width="50" align="left" class="text_9" width="17">'.$PriceOS.'</td>
			<td width="40" align="left" class="text_9">'.$QtyOS.'</td>
			<td width="70" align="left" class="text_9">'.$SubTotalOS.'</td>
			<td width="70" align="left" class="text_9" width="18">'.$DiscountOS.'</td>
			<td width="70" align="left" class="text_9" width="17">'.$TotalOS.'</td>
			<td width="60" align="left" class="text_9">'.$InsOS.'</td>
			<td width="80" align="left" class="text_9">'.$BalanceOS.'</td>
		</tr>';
		$patientDiscount = $DiscountOD;
	}
}
$pdf_page .= '
	   <tr>
			<td width="35" class="text_green_w" width="18">&nbsp;</td>
			<td width="270" class="text_9">&nbsp;</td>
			<td width="100" class="text_9">&nbsp;</td>
			<td width="100" class="text_9" >&nbsp;</td> 
			<td width="50" class="text_9" width="17">&nbsp;</td>
			<td width="40" class="text_9">&nbsp;</td>
			<td width="70" class="text_9">&nbsp;</td>
			<td width="70" class="text_9" width="18">&nbsp;</td>
			<td width="70" class="text_9" width="17">&nbsp;</td>
			<td width="60" class="textBold" align="right">Total :</td>
			<td width="80" align="left" class="text_9">$'.number_format($totalBalanceOSOD,2).'</td>
		</tr>
	</table>';


if(trim($ShipToHomeAddress)!=""){
	$pdf_page .= '
	<table cellpadding="10" cellspacing="0" class="text_9" style="margin-top:10px;">
		 <tr>
			<td width="80" align="left" class="textBold" >Ship To:</td>
			<td width="990" align="left">'.stripslashes($ShipToHomeAddress).'</td>
		</tr>
	</table>';
}


if($dateOrdered!='0000-00-00'){$dateOrdered = displayDateFormat($dateOrdered);}
if($dateReceived!='0000-00-00'){$dateReceived = displayDateFormat($dateReceived);}
if($dateNotified!='0000-00-00'){$dateNotified = displayDateFormat($dateNotified);}
if($datePickedUp!='0000-00-00'){$datePickedUp = displayDateFormat($datePickedUp);}
	$pdf_page .= '
	<table cellpadding="0" cellspacing="0" style="margin-top:10px;">';
	if($dateOrdered!='0000-00-00'){
	$pdf_page .= '
		<tr>
			<td width="100" class="textBold">Date Ordered</td>
			<td width="80" class="text_9">'.$OrderedTrialSupply.'</td>
			<td width="100" class="text_9">'.$dateOrdered.'</td>
			<td width="785" class="text_9">'.stripslashes($OrderedComment).'</td>
		</tr>';
	}
	if($dateReceived!='0000-00-00'){
		$pdf_page .= '
		<tr>
			<td class="textBold">Date Received</td>
			<td class="text_9">'.$ReceivedTrialSupply.'</td>
			<td class="text_9">'.$dateReceived.'</td>
			<td class="text_9">'.stripslashes($ReceivedComment).'</td>
		</tr>';
	}
	if($dateNotified!='0000-00-00'){		
		$pdf_page .= '
		<tr>
			<td class="textBold">Date Notified</td>
			<td class="text_9">&nbsp;</td>
			<td class="text_9">'.$dateNotified.'</td>
			<td class="text_9">'.stripslashes($NotifiedComment).'</td>
		</tr>';
	}
	if($datePickedUp!='0000-00-00'){
		$pdf_page .= '
		<tr>
			<td class="textBold">Date Picked Up</td>
			<td  class="text_9">'.$PickedUpTrialSupply.'</td>
			<td class="text_9">'.$datePickedUp.'</td>
			<td class="text_9">'.stripslashes($PickedUpComment).'</td>
		</tr>';
	}
	$pdf_page .= '
	</table>';
	

	$pdf_page .= '	
	<br>
	<table cellpadding="0" cellspacing="0"><tr><td width="1080" class="tb_heading">Prescription Details</td></tr></table>
	';
	
	$scl_header = '
	  <tr class="text_b_w textBold">
		<td width="100">&nbsp;</td>
		<td width="40">&nbsp;</td>
		<td width="60">B.Curve</td>
		<td width="60">Diameter</td>
		<td width="60">Sphere</td>
		<td width="60">Cylinder</td>
		<td width="60">Axis</td>
		<td width="60">ADD</td>
		<td width="60">DVA</td>
		<td width="110">NVA</td>
		<td width="370">Type</td>
		<td width="370">Color</td>
	   </tr>';
	$rgp_header = '
	  <tr class="text_b_w textBold">
		<td width="100">&nbsp;</td>
		<td width="40">&nbsp;</td>
		<td width="60">BC</td>
		<td width="60">Diameter</td>
		<td width="60">Power</td>
		<td width="60">Cylinder</td>
		<td width="60">Axis</td>
		<td width="60">OZ</td>
		<td width="60">Color</td>
		<td width="50">Add</td>
		<td width="60">DVA</td>
		<td width="60">NVA</td>
		<td width="245">Type</td>
	   </tr>';
	$crgp_header = '
	  <tr class="text_b_w textBold">
		<td width="100">&nbsp;</td>
		<td width="40">&nbsp;</td>
		<td width="40">BC</td>
		<td width="25">Diameter</td>
		<td width="35">Power</td>
		<td width="35">Cyl.</td>
		<td width="35">Axis</td>
		<td width="35">2&#176;/W</td>
		<td width="40">3&#176;/W</td>
		<td width="55">PC/W</td>
		<td width="25">OZ</td>
		<td width="45">Color</td>
		<td width="50">Blend</td>
		<td width="40">Edge</td>
		<td width="40">Add</td>
		<td width="60">DVA</td>
		<td width="80">NVA</td>
		<td width="160">Type</td>
	   </tr>';

//START CODE TO GET WORKSHEET DETAIL
$workSheetQuery= "SELECT clw.* FROM contactlensworksheet_det clw JOIN contactlensmaster clm ON (clw.clws_id = clm.clws_id) WHERE clw.clws_id='".$clws_id."' ORDER BY clw.id";
$workSheetRes = imw_query($workSheetQuery) or die(imw_error());

$sclprintHeader = 0;
$rgpprintHeader = 0;
$crgpprintHeader = 0;
$clEye_flag = '';
if($workSheetRes && imw_num_rows($workSheetRes)>0){
	while($workSheetrs = imw_fetch_array($workSheetRes)){
		//$clType=$workSheetrs['clType']=='scl'?'SCL':($workSheetrs['clType']=='rgp'?'RGP':($workSheetrs['clType']=='cust_rgp'?'Custom RGP':''));
		$clType = "";
		if($workSheetrs['clType']=='scl'){
			$clType = "SCL";
		}else if($workSheetrs['clType']=='rgp'){
			$clType = "RGP";
		}else if($workSheetrs['clType']=='rgp_soft'){
			$clType = "RGP Soft";
		}else if($workSheetrs['clType']=='rgp_hard'){
			$clType = "RGP Hard";
		}else if($workSheetrs['clType']=='cust_rgp'){
			$clType = "Custom RGP";
		}
		$clEye = $workSheetrs['clEye'];
		$cleyeClass = $clEye=='OD' ? 'text_blue_w' : 'text_green_w';
		if($clEye_flag != $clEye){
			$clEye_flag = $clEye;
			$sclprintHeader = 1;			$rgpprintHeader = 1;			$crgpprintHeader = 1;
		}
		if($workSheetrs['clType']=='scl'){
			$lensId = $workSheetrs['SclType'.$clEye.'_ID'];
			$pdf_page .= '
			<table cellpadding="0" cellspacing="0" class="text_9" style="margin-top:15px;">';
			if($sclprintHeader == 1){
				$pdf_page .= $scl_header;
				$sclprintHeader = 0;
			}
			$pdf_page .= '
			<tr>
				<td width="100" class="textBold" valign="top">'.$clType.'</td>
				<td width="40" class="'.$cleyeClass.'" valign="top">'.$clEye.'</td>
				<td width="60" valign="top">'.$workSheetrs['SclBcurve'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['SclDiameter'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['Sclsphere'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['SclCylinder'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['Sclaxis'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['SclAdd'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['SclDva'.$clEye].'</td>
				<td width="110" valign="top">'.$workSheetrs['SclNva'.$clEye].'</td>
				<td width="370" valign="top">'.
					$arrLensManuf[$lensId]['manufac']."<br />".$arrLensManuf[$lensId]['make_only']."<br />".$arrLensManuf[$lensId]['type_only'].
				'</td>
				<td width="110" valign="top">'.$workSheetrs['SclColor'.$clEye].'</td>
		   </tr>
		</table>';
		}//end of scl
		else if($workSheetrs['clType']=='rgp' || $workSheetrs['clType']=='rgp_soft' || $workSheetrs['clType']=='rgp_hard'){
			$lensId = $workSheetrs['RgpType'.$clEye.'_ID'];
			$pdf_page .= '
			<table cellpadding="0" cellspacing="0" class="text_9" style="margin-top:15px;">';
			if($rgpprintHeader == 1){
				$pdf_page .= $rgp_header;
				$rgpprintHeader = 0;
			}
			$pdf_page .= '
			<tr>
				<td width="100" class="textBold" valign="top">'.$clType.'</td>
				<td width="40" class="'.$cleyeClass.'" valign="top">'.$clEye.'</td>
				<td width="60" valign="top">'.$workSheetrs['RgpBC'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['RgpDiameter'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['RgpPower'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['RgpCylinder'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['RgpAxis'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['RgpOZ'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['RgpColor'.$clEye].'</td>
				<td width="50" valign="top">'.$workSheetrs['RgpAdd'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['RgpDva'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['RgpNva'.$clEye].'</td>
				<td width="245" valign="top">'.
				$arrLensManuf[$lensId]['manufac']."<br />".$arrLensManuf[$lensId]['make_only']."<br />".$arrLensManuf[$lensId]['type_only'].
				'</td>
		    </tr>
		</table>';
		}//end of rgp
		else if($workSheetrs['clType']=='cust_rgp'){
			$lensId = $workSheetrs['RgpCustomType'.$clEye.'_ID'];
			$pdf_page .= '
			<table cellpadding="0" cellspacing="0" class="text_9" style="margin-top:15px;">';
			if($crgpprintHeader == 1){
				$pdf_page .= $crgp_header;
				$crgpprintHeader = 0;
			}
			$pdf_page .= '
			<tr>
				<td width="100" class="textBold" valign="top">'.$clType.'</td>
				<td width="40" class="'.$cleyeClass.'" valign="top">'.$clEye.'</td>
				<td width="40" valign="top">'.$workSheetrs['RgpCustomBC'.$clEye].'</td>
				<td width="25" valign="top">'.$workSheetrs['RgpCustomDiameter'.$clEye].'</td>
				<td width="35" valign="top">'.$workSheetrs['RgpCustomPower'.$clEye].'</td>
				<td width="35" valign="top">'.$workSheetrs['RgpCustomCylinder'.$clEye].'</td>
				<td width="35" valign="top">'.$workSheetrs['RgpCustomAxis'.$clEye].'</td>
				<td width="35" valign="top">'.$workSheetrs['RgpCustom2degree'.$clEye].'</td>
				<td width="40" valign="top">'.$workSheetrs['RgpCustom3degree'.$clEye].'</td>
				<td width="55" valign="top">'.$workSheetrs['RgpCustomPCW'.$clEye].'</td>
				<td width="25" valign="top">'.$workSheetrs['RgpCustomOZ'.$clEye].'</td>
				<td width="45" valign="top">'.$workSheetrs['RgpCustomColor'.$clEye].'</td>
				<td width="50" valign="top">'.$workSheetrs['RgpCustomBlend'.$clEye].'</td>
				<td width="40" valign="top">'.$workSheetrs['RgpCustomEdge'.$clEye].'</td>
				<td width="40" valign="top">'.$workSheetrs['RgpCustomAdd'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['RgpCustomDva'.$clEye].'</td>
				<td width="80" valign="top">'.$workSheetrs['RgpCustomNva'.$clEye].'</td>
				<td width="160" valign="top">'.$workSheetrs['RgpCustomType'.$clEye].'</td>
		   </tr>
		</table>';
		}//end of custom rgp		
	}
	$signatureImageName = "";
	$physicianSignatureResult = imw_query("select sign_path from users where id = '".$_SESSION['authId']."'");
	if(imw_num_rows($physicianSignatureResult) > 0){
		$physicianSignatureRow = imw_fetch_assoc($physicianSignatureResult);
		$signatureImageName = $physicianSignatureRow['sign_path'];
	}
	$signatureImagePath = data_path().$signatureImageName;
	$signatureImagePath = str_ireplace("//", "/", $signatureImagePath);
	
	$pdf_page .= '
        <table cellpadding="0" cellspacing="0" class="text_9" style="margin-top:15px;">';
	$pdf_page .= '
			<tr>
				<td width="100" class="textBold" valign="top">Date of birth</td>
                <td width="100" class="textBold" valign="top">'.$patientDOB.'</td>
		   </tr>
            <tr>
				<td width="100" class="textBold" valign="top">Physician Name</td>
                <td width="100" class="textBold" valign="top">'.stripslashes($providerName).'</td>
		   </tr>
            <tr>
				<td width="100" class="textBold" valign="top">Phone number</td>
                <td width="100" class="textBold" valign="top">'.$patientPhone.'</td>
		   </tr>
		</table>';
	
	$pdf_page .= '
        <table cellpadding="0" cellspacing="0" class="text_9" style="margin-top:15px;width:100%">';
	$pdf_page .= '
			<tr>
				<td width="100" class="textBold" valign="top">Comments:</td>
                <td width="100%" class="textBold" valign="top">'.$messageComment.'</td>
		   </tr>
		</table>';
	
	$pdf_page .= '
			<table cellpadding="0" cellspacing="0" class="text_9" style="margin-top:15px;width:500px;">';
	$pdf_page .= '
			<tr>
				<td width="100" class="textBold" valign="top">Insurance Case</td>
                <td width="100" class="textBold" valign="top">'.$insuranceCaseTypesArray[$insuranceCaseType].'</td>
		   </tr>
            <tr>
				<td width="100" class="textBold" valign="top">Authorization #</td>
                <td width="100" class="textBold" valign="top">'.$patientAuthNumber.'</td>
		   </tr>
            <tr>
				<td width="100" class="textBold" valign="top">Auth Amount</td>
                <td width="100" class="textBold" valign="top">'.$patientAuthAmount.'</td>
		   </tr>
            <tr>
				<td width="100" class="textBold" valign="top">Discount Amount</td>
                <td width="100" class="textBold" valign="top">'.$patientDiscount.'</td>
		   </tr>
		   </table>';

		   $pdf_page .= '
			<table cellpadding="0" cellspacing="0" class="text_9" style="margin-top:15px;width:500px;">
				<tr>
					<td colspan="2" class="textBold" valign="top">
						I acknowledge eligibility for contact lens materials at the time of ordering is not a guarantee of benefits, which can only be determined once the claim has been submitted and processed by my insurance.  The above amount is only an estimate, and I agree to pay any remaining balance that is returned by my insurance.
					</td>
				</tr>
				<tr><td colspan="2" style="height:20px;">&nbsp;</td></tr>
				<tr>
					<td width="100" class="textBold" valign="top">_____________________</td>
					<td width="100" class="textBold" valign="top">_____________________</td>
				</tr>
				<tr>
					<td width="100" class="textBold" valign="top">Signature</td>
					<td width="100" class="textBold" valign="top">Date</td>
				</tr>
			</table>';
			$pdf_page .= '</page>';
}

?>

<?php
$headDataALL = ob_get_contents();
$headDataALL = $pdf_css.$pdf_page.$headDataALL;
die($headDataALL);
if(trim($headDataALL) != ""){
	$print_file_name = "chk_in_print_reciept_".$_SESSION["authId"];
	$file_path = write_html($headDataALL);
	if($file_path){
	?>
	<html>
		<body>
			<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
			<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
			<script type="text/javascript">
				var file_name = '<?php print $print_file_name; ?>';
				top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
				html_to_pdf('<?php echo $file_path; ?>','l',file_name);
			</script>
		</body>
	</html>
	<?php
	}
}else{
?>
	<table align="center" width="100%" border="0" cellpadding="1" cellspacing="1">
		<tr class="text_9" bgcolor="#EAF0F7" valign="top">
			<td align="center">No Result.</td>
		</tr>
	</table>
<?php
}
?>