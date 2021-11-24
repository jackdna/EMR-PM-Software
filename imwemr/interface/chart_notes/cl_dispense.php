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
#########################
#F I L E    H E A D E R #
#########################
#Created by  - HS on 5/3/2010
#Edited by (Initial on mm/dd/yyyy): 
#HS on 05/03/2010 

require_once("../../config/globals.php");
require_once("../../library/classes/Functions.php");
require_once("../../library/classes/common_function.php");
require_once("cl_functions.php");
//error_reporting(E_ALL);
//require_once("common/simpleMenu.php");
//require_once("common/functions.php");
//require_once("../main/main_functions.php");
//require_once("../main/chartNotesPrinting.php");
//require_once("../admin/chart_more_functions.php");
//require_once("common/menu_data.php");

//include_once("common/cl_functions.php");
//require_once(dirname(__FILE__).'/../main/Functions.php');
//require_once(dirname(__FILE__)."/common/session_chart_view_access.php");

$objManageData = new ManageData;
$authUserID = $_SESSION['authUserID'];
$print_order_id = $_REQUEST['print_order_id'];

$todayDate = $displayCurrDate = get_date_format(date("m-d-Y"),'mm-dd-yyyy');
$clws_id = $_REQUEST['clws_id'];
$LensBoxOD= array();
$LensBoxOS= array();
$PriceOD= array();
$PriceOS= array();
$arrPrintOD =array();
$arrPrintOS =array();
$clwsid_ArrOD = array();
$clwsid_ArrOS = array();
$arrChListIds = array();
$dispensed =0;
$displayOrder = 0;
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

function getPatientDataRow($id){
	$sql = "SELECT id,fname,mname,lname from patient_data where id = '$id'";
	$res = imw_query($sql);
	$row_address = imw_fetch_array($res);
	return 	$row_address;
}
//START GET PATIENT DETAIL
if($_SESSION['patient']){
	$row_address = getPatientDataRow($_SESSION['patient']);
}

// GET LENSE CODES AND COLORS in ARRAY
$arrLensCode	=	getLensCodeArr();
$arrLensColor	=	getLensColorArr();
//---------------------------------

// ADD 888 CPTCODE ROW
$cpt888Id= getCPT_Prac_Fee_Id('888');	// get cpt_fee_id for 888 procedure.

//START CODE TO GET USER INFORMATION
$userLoggedname = $_SESSION['authProviderName'];
//END CODE TO GET USER INFORMATION

// ---------- START SAVE RECORD ------------
if($_REQUEST['recordSave']=="saveTrue") {
	$totAmt = $totDisp =0;
	$totCharges = $_REQUEST["clSupply"];

	if($totCharges>0){
				
		$totOD = $_REQUEST['txtTotOD'];
		$totOS = $_REQUEST['txtTotOS'];

		$qryRs=imw_query("Select charge_list_id, encounter_id, cl_print_ord_id from patient_charge_list WHERE del_status='0' and cl_print_ord_id='".$print_order_id."'") or die(imw_error());
		if(imw_num_rows($qryRs) > 0) { 			
			$qryRes = imw_fetch_array($qryRs);
			$encounterIdText = $qryRes['encounter_id'];
		}
				
		for($i=0; $i< $totOD; $i++)
		{	
			if($_REQUEST["chld_id_OD".$i]!='')
			{
				$qry="Update patient_charge_list_details SET procCharges='".$_REQUEST['PriceOD'.$i]."',
										totalAmount='".$_REQUEST['SubTotalOD'.$i]."', balForProc='".$_REQUEST['SubTotalOD'.$i]."', 
										onset_date='".date('Y-m-d')."', approvedAmt='".$_REQUEST['SubTotalOD'.$i]."',units='".trim(addslashes($_REQUEST['QtyOD'.$i]))."',
										newBalance='".$_REQUEST['SubTotalOD'.$i]."' 
										WHERE charge_list_detail_id='".$_REQUEST["chld_id_OD".$i]."'";
				$rs = imw_query($qry) or die($qry.imw_error());
			
				$totDisp = $_REQUEST['QtyOD'.$i] + $_REQUEST['tot_disp_OD'.$i];
				$qry="Update clprintorder_det set totalQtyDispensed	='".$totDisp."', DiscountOD='".$_REQUEST['DiscountOD'.$i]."' WHERE id='".$_REQUEST['ordODId'.$i]."'";
				$rs = imw_query($qry);
				
				// SET DISCOUNT	
				if($_REQUEST['DiscountOD'.$i]!=''){
					$isDiscount=1;
					$discAmt=$_REQUEST["DiscountOD".$i];
					if(strstr($_REQUEST["DiscountOD".$i],'%')){
						$disPer=str_replace('%','', $_REQUEST["DiscountOD".$i]);
						$discAmt= ($_REQUEST["SubTotalOD".$i] * $disPer) / 100;
					}
					
					if($_REQUEST["cl_det_dis_idOD".$i]>0){
						$discount_id_od=$_REQUEST['cl_det_dis_idOD'.$i];
						$qryDiscountODQry = "Update paymentswriteoff SET
						write_off_amount='".$discAmt."', modified_date='".date('Y-m-d H:i:s')."', 
						modified_by='".$authUserID."' WHERE write_off_id ='".$_REQUEST['cl_det_dis_idOD'.$i]."'";
						imw_query($qryDiscountODQry);
					}else if($discAmt>0){
						$qryDiscountODQry = "Insert into paymentswriteoff SET patient_id='".$patient_id."',
						encounter_id='".$encounterIdText."', charge_list_detail_id='".$_REQUEST["chld_id_OD".$i]."',
						write_off_amount='".$discAmt."',write_off_operator_id='".$authUserID."',
						entered_date='".date('Y-m-d H:i:s')."', write_off_date='".date('Y-m-d')."', paymentStatus='Discount'";
						imw_query($qryDiscountODQry);
						$discount_id_od = imw_insert_id(); 
					}
					if($discount_id_od>0){
						imw_query("update clprintorder_det SET discount_table_id='".$discount_id_od."' WHERE id='".$_REQUEST['ordODId'.$i]."'");
					}
				}
			}
		}
		//echo "--------------------";
//		echo "<br>";
		
		for($i=0; $i< $totOS; $i++)
		{
			if($_REQUEST["chld_id_OS".$i]!='')
			{
				$qry="Update patient_charge_list_details SET procCharges='".$_REQUEST['PriceOS'.$i]."',
										totalAmount='".$_REQUEST['SubTotalOS'.$i]."', balForProc='".$_REQUEST['SubTotalOS'.$i]."', 
										onset_date='".date('Y-m-d')."', approvedAmt='".$_REQUEST['SubTotalOS'.$i]."',units='".trim(addslashes($_REQUEST['QtyOS'.$i]))."',
										newBalance='".$_REQUEST['SubTotalOS'.$i]."' 
										WHERE charge_list_detail_id='".$_REQUEST["chld_id_OS".$i]."'";
				$rs = imw_query($qry) or die($qry.imw_error());
				
				$totDisp = $_REQUEST['QtyOS'.$i] + $_REQUEST['tot_disp_OS'.$i];
				$qry="Update clprintorder_det set totalQtyDispensed	='".$totDisp."', DiscountOS='".$_REQUEST['DiscountOS'.$i]."' WHERE id='".$_REQUEST['ordOSId'.$i]."'";
				$rs = imw_query($qry);

				// SET DISCOUNT	
				if($_REQUEST['DiscountOS'.$i]!=''){
					$isDiscount=1;
					$discAmt=$_REQUEST["DiscountOS".$i];
					if(strstr($_REQUEST["DiscountOS".$i],'%')){
						$disPer=str_replace('%','', $_REQUEST["DiscountOS".$i]);
						$discAmt= ($_REQUEST["SubTotalOS".$i] * $disPer) / 100;
					}
					
					if($_REQUEST["cl_det_dis_idOS".$i]>0){
						$discount_id_os=$_REQUEST['cl_det_dis_idOS'.$i];
						$qryDiscountOSQry = "Update paymentswriteoff SET
						write_off_amount='".$discAmt."', modified_date='".date('Y-m-d H:i:s')."', 
						modified_by='".$authUserID."' WHERE write_off_id ='".$_REQUEST['cl_det_dis_idOS'.$i]."'";
						imw_query($qryDiscountOSQry);
					}else if($discAmt>0){
						$qryDiscountOSQry = "Insert into paymentswriteoff SET patient_id='".$patient_id."',
						encounter_id='".$encounterIdText."', charge_list_detail_id='".$_REQUEST["chld_id_OS".$i]."',
						write_off_amount='".$discAmt."',write_off_operator_id='".$authUserID."',
						entered_date='".date('Y-m-d H:i:s')."', write_off_date='".date('Y-m-d')."', paymentStatus='Discount'";
						imw_query($qryDiscountOSQry);
						$discount_id_os = imw_insert_id(); 
					}
					if($discount_id_os>0){
						imw_query("update clprintorder_det SET discount_table_id='".$discount_id_os."' WHERE id='".$_REQUEST['ordOSId'.$i]."'");
					}
				}
			}
		}

		//CALLING ACCOUNTING FUNCTIONS IF DISCOUNT DONE
		if($isDiscount=='1'){
			set_payment_trans($encounterIdText);
		}
		$objManageData->patient_proc_bal_update($encounterIdText);

		// -----------  ----------------------------
		// SET GRAND AMOUNT ACCORDING TO NEW AND OLD RECORDS
		$qryCharges = "Select pCh.charge_list_id, pCh.patient_id, pCh.date_of_service, pCh.facility_id, pCh.primaryProviderId, pCh.encounter_id, pChDet.balForProc from patient_charge_list pCh 
		LEFT JOIN patient_charge_list_details pChDet ON pChDet.charge_list_id = pCh.charge_list_id 
		WHERE pChDet.del_status='0' and pCh.cl_print_ord_id ='".$print_order_id."'";
		$resCharges = $objManageData->mysqlifetchdata($qryCharges);
		
		$charge_list_id = $resCharges[0]['charge_list_id'];
		$patient_id = $resCharges[0]['patient_id'];
		$date_of_service = $resCharges[0]['date_of_service'];
		$facility_id = $resCharges[0]['facility_id'];
		$primaryProviderId = $resCharges[0]['primaryProviderId'];
		$encounterIdText = $resCharges[0]['encounter_id'];
		
		for($i=0; $i< sizeof($resCharges); $i++)
		{
			$totAmt+= $resCharges[$i]['balForProc'];
		}
	
	
		$desc = $_REQUEST['desc'];
		if($_REQUEST['desc']==''){
			$desc = "Contact Lens Dispensed.";
		}
		$qryEnterProcQry ="Insert into patient_charge_list_details set charge_list_id='$charge_list_id',
									patient_id='$patient_id', procCode='".$cpt888Id."', start_date='".$date_of_service."',place_of_service='".$facility_id."',
									primaryProviderId='".$primaryProviderId."', procCharges='0',
									totalAmount='0', balForProc='0', onset_type='', onset_date='".date('Y-m-d')."', units='0',
									newBalance='0', notes='".$desc."',entered_date='".date('Y-m-d H:i:s')."',
									operator_id='".$_SESSION['authId']."' ".$qryWhere;
		imw_query($qryEnterProcQry);

				
		// UPDATE GRAND AMOUNT CHARGES in patient_charge_list table
		$qryEnterChargesQry = "Update patient_charge_list set totalAmt='$totAmt', approvedTotalAmt='$totAmt',
								amountDue='$totAmt', patientAmt='$totAmt', totalBalance='$totAmt' 
								where cl_print_ord_id='".$print_order_id."'";
		imw_query($qryEnterChargesQry) or print($qryEnterChargesQry. imw_error());

		if($encounterIdText>0) { $_SESSION['encounter_id']=''; }
		$_SESSION['encounterIdFromCL'] = $encounterIdText;?>
		<script type="text/javascript">
			opener.redirectToEnterCharges('<?php echo $_REQUEST["patient_id"];?>');
			window.close();
		</script>
	<?php }else{?>
		<script type="text/javascript">
            alert('Can not Redirect to Enter Charges.\nSet Price and Quantity for Order Dispense.');
        </script>
    <?php			
	}
}
	// ------------- 	END SAVE RECORD ------------


//START CODE TO VIEW PRINT ORDER RECORD
$todayDate = date('Y-m-d');
$clWhere = '';

//	CHECK IF THIS RECORD ALREADY DISPENSED OR NOT
$procQry = "Select pChDet.procCode from  patient_charge_list pCh 
		  LEFT JOIN patient_charge_list_details pChDet ON pChDet.charge_list_id = pCh.charge_list_id 
		  where pChDet.del_status='0' and pCh.cl_print_ord_id = '".$print_order_id."' AND pChDet.procCode='".$cpt888Id."'";
$procRs = imw_query($procQry) or die(imw_error());
if(imw_num_rows($procRs)>0) { $dispensed=1; }

$todayOrderQry = "Select clprintorder_master.*, clprintorder_det.*, clprintorder_det.id as 'orDetId', contactlensmaster.cpt_evaluation_fit_refit from clprintorder_master 
				  LEFT JOIN clprintorder_det ON clprintorder_det.print_order_id = clprintorder_master.print_order_id 
				  LEFT JOIN contactlensmaster ON contactlensmaster.clws_id = clprintorder_master.clws_id 
				  where clprintorder_master.patient_id = '".$_SESSION['patient']."' 
				  AND clprintorder_master.print_order_id ='".$print_order_id."' ORDER BY clprintorder_det.id";
//die($todayOrderQry);
$todayOrderResult = imw_query($todayOrderQry);
$clOrderRes = array();
if(imw_num_rows($todayOrderResult) > 0){
    while($row = imw_fetch_assoc($todayOrderResult)){
        $clOrderRes[] = $row;
    }
}

/* public function mysqlifetchdata($query=''){
    $mysqli = $this->mysqliConnect();
    if(trim($query) == ''){
        $query = $this->QUERY_STRING;
    }
    $result = $mysqli->query($query);
    if($result){
        $return = array();
        if($result->num_rows){
            while($this->result_data = $result->fetch_assoc()){
                $return[] = $this->changeFormat($this->result_data);
            }
        }
    }
    return $return;
} */

//$clOrderRes = $objManageData->mysqlifetchdata($todayOrderQry);
//die("Line 281");
$GetPrintDataNumRow = sizeof($clOrderRes);

$eVal = substr(trim($clOrderRes[0]['cpt_evaluation_fit_refit']),0,1);
if($eVal=="$"){
	$cptEvalCharges = substr(trim($clOrderRes[0]['cpt_evaluation_fit_refit']),1,strlen(trim($clOrderRes[0]['cpt_evaluation_fit_refit'])));
}else{
	$cptEvalCharges = trim($clOrderRes[0]['cpt_evaluation_fit_refit']);
}
if($GetPrintDataNumRow > 0){

	$j= $k= $m= 0;	$clSupply=0;
	for($i=0; $i< $GetPrintDataNumRow; $i++)
	{
		$view=1;
		$qty= $subTotal = $total = $balance =0;

		if($clOrderRes[$i]['PriceOD']!='')
		{
			$qty=		$clOrderRes[$i]['QtyOD'];
			$subTotal=	$clOrderRes[$i]['SubTotalOD'];
			$total=		$clOrderRes[$i]['TotalOD'];
			$balance=	$clOrderRes[$i]['BalanceOD'];
			
			if($dispensed==1){
				if($clOrderRes[$i]['totalQtyDispensed'] >= $clOrderRes[$i]['QtyOD']){
					$view=0;
				}else{
					$qty=		$clOrderRes[$i]['QtyOD'] - $clOrderRes[$i]['totalQtyDispensed'];
					$subTotal=	$clOrderRes[$i]['PriceOD'] * $qty;
					$total=		$subTotal - $clOrderRes[$i]['DiscountOD'];
					$balance=	$total - $clOrderRes[$i]['InsOD'];
				}
			}
			if($view==1){
				$LensBoxOD[$j] = $clOrderRes[$i]['LensBoxOD'];
				$PriceOD[$j] = $clOrderRes[$i]['PriceOD'];
				$clwsid_ArrOD[$j] = $clOrderRes[$i]['cl_det_id'];
				$arrPrintOD[$j]['orDetId'] = $clOrderRes[$i]['orDetId'];
				$arrPrintOD[$j]['chldIdOD'] = $clOrderRes[$i]['chld_id_od'];
				$arrPrintOD[$j]['code'] = $clOrderRes[$i]['lensNameIdList'];
				$arrPrintOD[$j]['color'] = $clOrderRes[$i]['colorNameIdList'];
				$arrPrintOD[$j]['qty'] = $qty;
				$arrPrintOD[$j]['subTotal'] = $subTotal;
				$arrPrintOD[$j]['discount'] = $clOrderRes[$i]['DiscountOD'];
				$arrPrintOD[$j]['total'] = $total;
				$arrPrintOD[$j]['insurance'] = $clOrderRes[$i]['InsOD'];
				$arrPrintOD[$j]['balance'] = $balance;
				$arrPrintOD[$j]['totalQtyDispensed'] = $clOrderRes[$i]['totalQtyDispensed'];
				$arrPrintOD[$j]['discount_table_id'] = $clOrderRes[$i]['discount_table_id'];				
				
				$displayOrder = 1;
				$clSupply+=$balance;
				$j++;
			}
		}
		if($clOrderRes[$i]['PriceOS']!='')
		{
			$qty=		$clOrderRes[$i]['QtyOS'];
			$subTotal=	$clOrderRes[$i]['SubTotalOS'];
			$total=		$clOrderRes[$i]['TotalOS'];
			$balance=	$clOrderRes[$i]['BalanceOS'];
						
			if($dispensed==1){
				if($clOrderRes[$i]['totalQtyDispensed'] >= $clOrderRes[$i]['QtyOS']){
					$view=0;
				}else{
					$qty=		$clOrderRes[$i]['QtyOS'] - $clOrderRes[$i]['totalQtyDispensed'];
					$subTotal=	$clOrderRes[$i]['PriceOS'] * $qty;
					$total=		$subTotal - $clOrderRes[$i]['DiscountOS'];
					$balance=	$total - $clOrderRes[$i]['InsOS'];
				}
			}
			if($view==1){
				$LensBoxOS[$k] = $clOrderRes[$i]['LensBoxOS'];
				$PriceOS[$k] = $clOrderRes[$i]['PriceOS'];
				$clwsid_ArrOS[$k] = $clOrderRes[$i]['cl_det_id'];
				$arrPrintOS[$k]['orDetId'] = $clOrderRes[$i]['orDetId'];
				$arrPrintOS[$k]['chldIdOS'] = $clOrderRes[$i]['chld_id_os'];
				$arrPrintOS[$k]['code'] = $clOrderRes[$i]['lensNameIdListOS'];
				$arrPrintOS[$k]['color'] = $clOrderRes[$i]['colorNameIdListOS'];
				$arrPrintOS[$k]['qty'] = $qty;
				$arrPrintOS[$k]['subTotal'] = $subTotal;
				$arrPrintOS[$k]['discount'] = $clOrderRes[$i]['DiscountOS'];
				$arrPrintOS[$k]['total'] = $total;
				$arrPrintOS[$k]['insurance'] = $clOrderRes[$i]['InsOS'];
				$arrPrintOS[$k]['balance'] = $balance;
				$arrPrintOS[$k]['totalQtyDispensed'] = $clOrderRes[$i]['totalQtyDispensed'];
				$arrPrintOS[$k]['discount_table_id'] = $clOrderRes[$i]['discount_table_id'];
				
				$displayOrder = 1;
				$clSupply+=$balance;
				$k++;
			}
		}
	}
}

$totalCharges = $clSupply + $cptEvalCharges;

	//START SET DOCTOR-ID, TECHNICIAN-ID, OPERATOR-ID TO SAVE IN DATABASE
		$operator_id = $authUserID;
		if(in_array($_SESSION['logged_user_type'],$GLOBALS['arrValidCNPhy'])) {
			$provider_id=$authUserID;
		}
		if(in_array($_SESSION['logged_user_type'],$GLOBALS['arrValidCNTech'])) {
			$technician_id=$authUserID;
		}

	
?>
<html>
<head>
<title>CONTACT LENS DISPENSE ORDER</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link rel="stylesheet" href="../themes/default/common.css" type="text/css">
<link type="text/css"  href="css/style.css" rel="stylesheet">
<link rel="stylesheet" href="css/sfdc_header.css" type="text/css" />
<link rel="stylesheet" href="css/simpletree.css" type="text/css" />
<link rel="stylesheet" href="css/simpleMenu.css" type="text/css" />
<style type="text/css"> 
	.la_sel_lts{
	background:url(images/la_sel_left.png); background-repeat:no-repeat; height:25px;width:7px;
	}
	.la_sel_mds2{
	}
	.la_sel_mds{ background:#999999;}
	.la_sel_rts{ background-image:url(images/la_sel_right.png); background-repeat:no-repeat; height:25px;width:7px;}
	.la_bg2{
		background-color:#3F7696;
		background-attachment: scroll;
		background-repeat: repeat-x;
		background-pOUition: left;
	}
.od{color:blue;}
.os{color:green;}
.ou{color:#9900CC;}	

</style>
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery-ui.min.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-select.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/schedulemain.css" type="text/css" rel="stylesheet">


<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]--> 
	
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery-ui.min.1.11.2.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap-typeahead.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap-select.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.datetimepicker.full.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/core_main.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/sc_script.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jQuery/ui.core.js"></script>
<script type="text/javascript">
var xmlhttp;

function dgi(obj){
	return document.getElementById(obj);
}

function getXmlHttpObject()
{
	xmlHttp = null;
	try
	{
		xmlHttp = new XMLHttpRequest(); // not IE
	}
	catch(e)
	{
		try
		{
			xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch(e)
		{
			try
			{
				xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch(e)
			{
				// Browser Does not support ajax.
			}			
		}		
	}
	return xmlHttp;
}
function newWindow(q){
	window.open('../common/mycal.php?md='+q,'iMedicWcare','width=200,height=250,top=200,left=300');
}
	
function restart(val){
	document.getElementById(val).value=''+ padout(month - 0 + 1) + '-'  + padout(day) + '-' +  year ;
}

function setPrice(odos,num,typeVal)
{
//	alert(odos+' - '+num+' - '+typeVal);
	
	if(odos=='od') { txtID = 'PriceOD';}
	if(odos=='os') { txtID = 'PriceOS';}
	$.ajax({ 
		url: "ajaxSetPrice.php?typeVal="+typeVal,
		success: function(newPrice){
			dgi(txtID+num).value = newPrice+".00";
		}
	});
}
	
function checkSingle(elemId,grpName)
{
	var obgrp = document.getElementsByName(grpName);
	var objele = document.getElementById(elemId);
	var len = obgrp.length;		
	if(objele.checked == true)
	{		
		for(var i=0;i<len;i++)
		{
			if((obgrp[i].id != objele.id) && (obgrp[i].checked == true) )
			{
				obgrp[i].checked=false;
			}
		}	
	}
}

function hideDisplayOrderFn(orderTrialDspDoctorId,orderTrialId,orderTrialDspNumberId,orderTrialNumberId) {
	if(document.getElementById(orderTrialDspNumberId)) {
		document.getElementById(orderTrialDspNumberId).disabled=true;
	}
	if(document.getElementById(orderTrialNumberId)) {
		document.getElementById(orderTrialNumberId).disabled=true;
	}
	if(document.getElementById(orderTrialDspDoctorId)) {
		if(document.getElementById(orderTrialDspDoctorId).checked==true) {
			document.getElementById(orderTrialDspNumberId).disabled=false;
		}
	}
	if(document.getElementById(orderTrialId)) {
		if(document.getElementById(orderTrialId).checked==true) {
			document.getElementById(orderTrialNumberId).disabled=false;
		}
	}
}


function splitAmount(){
	if(document.getElementById('auth_amount').value != ''){
		var authAmount = parseFloat(document.getElementById('auth_amount').value);
		if(authAmount != ''){
			document.getElementById('InsOD').value = (authAmount/2);
			document.getElementById('InsOS').value = (authAmount/2);
		}

	}
	
	var disc_amount = document.getElementById('disc_amount').value;
	if(disc_amount != '' && disc_amount.search('%')<0){
		if(disc_amount != ''){
			disc_amount = parseFloat(disc_amount);
			document.getElementById('DiscountOD').value = (disc_amount/2);
			document.getElementById('DiscountOS').value = (disc_amount/2);
		}
	}
	else if(disc_amount != '' && disc_amount.search('%') > 0){
			var SubTotalOD  = document.getElementById('SubTotalOD').value;
			var SubTotalOS  = document.getElementById('SubTotalOS').value;
			var disc_amount_split = disc_amount.split('%');
			var DiscountOD = (disc_amount_split[0] * SubTotalOD)/100;
			var DiscountOS = (disc_amount_split[0] * SubTotalOS)/100;
			document.getElementById('DiscountOD').value = DiscountOD;
			document.getElementById('DiscountOS').value = DiscountOS;
	}
calcTotalBalOSFn();

}

function justify2Decimal(objElem){
	if(objElem.value < 0){objElem.value = 0;}//Converting value to Zero if it's in Negative//
	
	var valElem = objElem.value;		
	if(!isNaN(valElem)){
		var ptrn = "^[\+|\-]";
		var reg = new RegExp(ptrn,'g');
		var sign = valElem.match(reg);		
		var unJustNumber = (sign == null) ? valElem : valElem.substr(1);				
		var justNumber = (unJustNumber == "") ? "" : parseFloat(unJustNumber).toFixed(2);		
		objElem.value = (sign == null) ? justNumber : sign+justNumber;		
	}else{ 	
		objElem.value = valElem;
	}
}
	function check2Blur(obj,wh,wh2){
		var e = window.event;
		var kCode = e.keyCode
		if( ((kCode >= 48) && (kCode<=57)) || 
		   ((kCode >=65 ) && (kCode <= 90)) || 
		   ((kCode >= 96) && ( kCode <= 111)) ||
		   ((kCode >= 186) && ( kCode <= 191)) ||
		   ((kCode >= 219) && ( kCode <= 222)) 
		){	
			var oWh2 = gebi(wh2);
			//alert(oWh2);
			var val = ( typeof obj.value == "undefined" ) ? "" : obj.value;
			if( ((wh == "I") || (wh == "A")) && ( val.length >= 3 ) ){
				oWh2.focus();
			}else{
				var dInx = val.indexOf(".");			
				if( (dInx != -1) ){
					var sbStr = val.substr(dInx+1);					
					if( sbStr.length >= 2 ){					
						oWh2.focus();
					}
				}
			}
		}
	}
	function gebi(id){
		return document.getElementById(id);
	}
	
	var xmlhttp;
	var global_var;
	var global_auth_number;
	var global_auth_amount;
	
	function checkInsCase(val,auth_number,auth_amount)
	{
//		alert(val+' - '+auth_number+' - '+auth_amount);
	global_var = val;
	global_auth_number = auth_number;
	global_auth_amount = auth_amount;
	xmlhttp = GetXmlHttpObject();
	if (xmlhttp==null)
	  {
	  alert("Browser does not support HTTP Request");
	  return;
	  }
	var url = "checkInsCase.php";
		url = url+"?val="+global_var;
		url = url+"&sid="+Math.random();
		xmlhttp.onreadystatechange = stateChanged;
		xmlhttp.open("GET",url,true);
		xmlhttp.send(null);
	}
	
	function stateChanged()
	{
		if(xmlhttp.readyState==4)
		{
			var checkCase =  xmlhttp.responseText;
			if(checkCase == 0){
				document.getElementById('auth_number').value = '';
				document.getElementById('auth_number').disabled = true;
				document.getElementById('auth_amount').value = '';
				document.getElementById('auth_amount').disabled = true;
				$('[id^="InsOD"]').val('0');
				$('[id^="InsOS"]').val('0');
			}
			else if(checkCase == 1){
				document.getElementById('auth_number').value = global_auth_number;
				document.getElementById('auth_number').disabled = false;
				document.getElementById('auth_amount').value = global_auth_amount;
				document.getElementById('auth_amount').disabled = false;
			}
		}
	}
window.focus();

function show_loading_image(val){
	document.getElementById("loading_img").style.display = val;
}

function setPrescription(ObjorderTrialDspDoctorId,ObjorderTrialId,ObjorderSupplyId,ObjreOrderSupplyId) {
	//alert(ObjorderTrialDspDoctorId.checked);
	//alert(ObjorderTrialId.checked);
	//alert(ObjorderSupplyId.checked);
	//alert(ObjreOrderSupplyId.checked);
	if(document.getElementById('prescripClwsId')) {
		document.getElementById('prescripClwsId').value='';
	}
	var dspNm='';
	if(ObjorderSupplyId.checked==true || ObjreOrderSupplyId.checked==true) {

			<?php if($clws_id>0){?>
				dspNm = '<?php echo $FinalContactRXID;?>';

			<?php }else{?>

			dspNm = '<?php echo $hiddClwsIdTemp;?>';

			<?php }?>
		
		//START CODE TO SELECT TRIAL/SUPPLY DROPDOWN
		if(document.getElementById('OrderedTrialSupplyId')) {
			document.getElementById('OrderedTrialSupplyId').value='Supply';
		}
		if(document.getElementById('ReceivedTrialSupplyId')) {
			document.getElementById('ReceivedTrialSupplyId').value='Supply';
		}
		if(document.getElementById('PickedUpTrialSupplyId')) {
			document.getElementById('PickedUpTrialSupplyId').value='Supply';
		}
		//END CODE TO SELECT TRIAL/SUPPLY DROPDOWN
		if(document.getElementById('prescripClwsId')) {
			document.getElementById('prescripClwsId').value=document.getElementById('hiddClwsIdTemp').value;
		}
	}else if(ObjorderTrialDspDoctorId.checked==true || ObjorderTrialId.checked==true) {
		
		//START CODE TO SELECT TRIAL/SUPPLY DROPDOWN
		if(document.getElementById('OrderedTrialSupplyId')) {
			document.getElementById('OrderedTrialSupplyId').value='Trial';
		}
		if(document.getElementById('ReceivedTrialSupplyId')) {
			document.getElementById('ReceivedTrialSupplyId').value='Trial';
		}
		if(document.getElementById('PickedUpTrialSupplyId')) {
			document.getElementById('PickedUpTrialSupplyId').value='Trial';
		}
		//END CODE TO SELECT TRIAL/SUPPLY DROPDOWN
		
		//var dspNmAr= new Array();
		
		if(ObjorderTrialDspDoctorId.checked==true) {
				
			//tdPrescripId
			//'orderTrialDspNumberId','orderTrialNumberId'
			dspNmAr = document.getElementById('orderTrialDspNumberId').value;
			dspNmAr = dspNmAr.split('-');
			dspNm = dspNmAr[1];
		}else if(ObjorderTrialId.checked==true) {
			dspNmAr = document.getElementById('orderTrialNumberId').value;
			dspNmAr = dspNmAr.split('-');
			dspNm = dspNmAr[1];
		}	
			
	}

	if(dspNm!='') {	
		xmlhttp = getXmlHttpObject();
		if (xmlhttp==null) {
			alert("Browser does not support HTTP Request");
			return;
		}
		if(document.getElementById('prescripClwsId')) {
			document.getElementById('prescripClwsId').value=dspNm;
		}

		var url = "prescripAjax.php";
		url = url+"?clws_id="+dspNm;
		url = url+"&displayValuesIn=ClSupply&callFrom=clSupply";
		
		xmlhttp.onreadystatechange = prescripChanged;
		xmlhttp.open("GET",url,true);
		xmlhttp.send(null);

		document.frmOrderHx.clws_id.value= dspNm;	
		document.frmPrintOrder.clws_id.value= dspNm;
		document.getElementById('prescripClwsId').value= dspNm;
		document.getElementById('hiddClwsIdTemp').value= dspNm;
	}
}
function prescripChanged() {
	if(xmlhttp.readyState==1) {
		show_loading_image('block');
	}
	if(xmlhttp.readyState==4)
	{
		show_loading_image('none');

		var dd = xmlhttp.responseText.split("~~");
		document.getElementById('tdPrescripId').innerHTML= dd[0];
		document.getElementById('typeDiv').innerHTML= dd[1];
//		document.getElementById('typeManufac').value= dd[2];

		document.getElementById('dos').value= dd[3];
		document.frmOrderHx.print_order_id.value= '';
		document.frmPrintOrder.print_order_id.value= '';
		dgi('orderHx').value= '';


	}
}
function calcTotalBalODFn(i) {
	
	if(!parseFloat(dgi('PriceOD'+i).value)) {
		dgi('PriceOD'+i).value=0;	
	}
	if(!parseFloat(dgi('QtyOD'+i).value)) {
		dgi('QtyOD'+i).value=0;	
	}
	if(!parseFloat(dgi('SubTotalOD'+i).value)) {
		dgi('SubTotalOD'+i).value=0;	
	}
	if(!parseFloat(dgi('DiscountOD'+i).value)) {
		dgi('DiscountOD'+i).value=0;	
	}
	if(!parseFloat(dgi('TotalOD'+i).value)) {
		dgi('TotalOD'+i).value=0;	
	}
	if(!parseFloat(dgi('InsOD'+i).value)) {
		dgi('InsOD'+i).value=0;	
	}
	if(!parseFloat(dgi('BalanceOD'+i).value)) {
		dgi('BalanceOD'+i).value=0;	
	}
	
	var PriceOD 				= parseFloat(dgi('PriceOD'+i).value);
	var QtyOD 					= parseFloat(dgi('QtyOD'+i).value);
	var SubTotalOD 				= parseFloat(dgi('SubTotalOD'+i).value);
	var DiscountOD 				= parseFloat(dgi('DiscountOD'+i).value);
	var TotalOD 				= parseFloat(dgi('TotalOD'+i).value);
	var InsOD 					= parseFloat(dgi('InsOD'+i).value);
	var BalanceOD 				= parseFloat(dgi('BalanceOD'+i).value);
	var SubTotalODValue			='';
	var TotalODValue			='';
	var BalanceODValue 			='';
	var TotalBalanceODOSValue	='';
	//if(PriceOD && QtyOD) {
		SubTotalODValue = PriceOD*QtyOD;
		dgi('SubTotalOD'+i).value=SubTotalODValue;
	//}

	var discVal=dgi('DiscountOD'+i).value;
	var sign = discVal.substr(discVal.length-1);
	if(sign=='%'){
		var percVal = discVal.substr(0, discVal.length-1);
		percDiscVal = (SubTotalOD * percVal) / 100;
		TotalODValue = parseFloat(dgi('SubTotalOD'+i).value)-percDiscVal;
	}else{
		TotalODValue = parseFloat(dgi('SubTotalOD'+i).value)-parseFloat(dgi('DiscountOD'+i).value);		
	}
	
	dgi('TotalOD'+i).value=TotalODValue;
	BalanceODValue = parseFloat(dgi('TotalOD'+i).value);
	dgi('BalanceOD'+i).value=BalanceODValue;
	
	var discVal=dgi('InsOD'+i).value;
	var sign = discVal.substr(discVal.length-1);
	var insDiscVal=0;
	if(sign=='%'){
		var percVal = discVal.substr(0, discVal.length-1);
		var percDiscVal = (TotalOD * percVal) / 100;
		var BalanceODValue = parseFloat(dgi('TotalOD'+i).value)-percDiscVal;
	}else{
		var BalanceODValue = parseFloat(dgi('TotalOD'+i).value)-parseFloat(dgi('InsOD'+i).value);		
	}
	dgi('BalanceOD'+i).value=BalanceODValue;

	
	//START CODE TO CLCULATE TOTAL BALANCE CHARGES OF OD AND OS AND OU
	if(!parseFloat(dgi('BalanceOD'+i).value)) { dgi('BalanceOD'+i).value=0;}
	if(dgi('BalanceOS'+i)) {
		if(!parseFloat(dgi('BalanceOS'+i).value)) { dgi('BalanceOS'+i).value=0;}
	}
	
	//START CODE TO SET DECIMAL VALUE
	justify2Decimal(dgi('SubTotalOD'+i));
	justify2Decimal(dgi('TotalOD'+i));
	justify2Decimal(dgi('BalanceOD'+i));
	//END CODE TO SET DECIMAL VALUE

	// CALCULATE GRAND TOTAL
	var ODLen = dgi('txtTotOD').value;
	var ODTot = 0;
	for(i=0; i<ODLen; i++)
	{
		if(dgi('BalanceOD'+i)){
			var odVal = dgi('BalanceOD'+i).value;
			if(odVal=='') { odVal=0; }
			ODTot = (parseFloat(ODTot)) + (parseFloat(odVal));
		}
	}
	// CALCULATE GRAND TOTAL
	var OSLen = dgi('txtTotOS').value;
	var OSTot = 0;
	for(i=0; i<OSLen; i++)
	{
		if(dgi('BalanceOS'+i)){
			var osVal = dgi('BalanceOS'+i).value;
			if(osVal=='') { osVal=0; }
			OSTot = (parseFloat(OSTot)) + (parseFloat(osVal));
		}
	}

	//if(parseFloat(document.frmPrintOrder.BalanceOD.value) || parseFloat(document.frmPrintOrder.BalanceOS.value)) {
		if(OSTot=='') { OSTot=0; }
		if(ODTot=='') { ODTot=0; }

		clSupplyValue = parseFloat(ODTot)+parseFloat(OSTot);
		dgi('clSupply').value=clSupplyValue;
	//}
		var clExam = dgi('clExam').value;
		
		if(clSupplyValue=='') { clSupplyValue=0; }
		if(clExam=='') { clExam=0; }
		
		//alert(clExam+' - '+clSupplyValue);
		
		dgi('totalCharges').value = parseFloat(clExam)+parseFloat(clSupplyValue);
		
		justify2Decimal(dgi('clExam'));
		justify2Decimal(dgi('clSupply'));
		justify2Decimal(dgi('totalCharges'));
		

}
function calcTotalBalOSFn(i) {
	
	
	if(!parseFloat(dgi('PriceOS'+i).value)) {
		dgi('PriceOS'+i).value=0;	
	}
	if(!parseFloat(dgi('QtyOS'+i).value)) {
		dgi('QtyOS'+i).value=0;	
	}
	if(!parseFloat(dgi('SubTotalOS'+i).value)) {
		dgi('SubTotalOS'+i).value=0;	
	}
	if(!parseFloat(dgi('DiscountOS'+i).value)) {
		dgi('DiscountOS'+i).value=0;	
	}

	if(!parseFloat(dgi('TotalOS'+i).value)) {
		dgi('TotalOS'+i).value=0;	
	}

	if(!parseFloat(dgi('InsOS'+i).value)) {
		dgi('InsOS'+i).value=0;	
	}
	if(!parseFloat(dgi('BalanceOS'+i).value)) {
		dgi('BalanceOS'+i).value=0;	
	}

	var PriceOS 				= parseFloat(dgi('PriceOS'+i).value);
	var QtyOS 					= parseFloat(dgi('QtyOS'+i).value);
	var SubTotalOS 				= parseFloat(dgi('SubTotalOS'+i).value);
	var DiscountOS 				= parseFloat(dgi('DiscountOS'+i).value);
	var TotalOS 				= parseFloat(dgi('TotalOS'+i).value);
	var InsOS 					= parseFloat(dgi('InsOS'+i).value);
	var BalanceOS 				= parseFloat(dgi('BalanceOS'+i).value);
	var SubTotalOSValue			= '';
	var TotalOSValue			= '';
	var BalanceOSValue 			= '';
	var TotalBalanceODOSValue 	= '';
	//if(PriceOS && QtyOS) {
		SubTotalOSValue = PriceOS*QtyOS;
		dgi('SubTotalOS'+i).value=SubTotalOSValue;
	//}

	var discVal=dgi('DiscountOS'+i).value;
	var sign = discVal.substr(discVal.length-1);
	if(sign=='%'){
		var percVal = discVal.substr(0, discVal.length-1);
		percDiscVal = (SubTotalOS * percVal) / 100;
		TotalOSValue = parseFloat(dgi('SubTotalOS'+i).value)-percDiscVal;
	}else{
		TotalOSValue = parseFloat(dgi('SubTotalOS'+i).value)-parseFloat(dgi('DiscountOS'+i).value);		
	}	
	
	dgi('TotalOS'+i).value=TotalOSValue;
	BalanceOSValue = parseFloat(dgi('TotalOS'+i).value);
	dgi('BalanceOS'+i).value=BalanceOSValue;

	var discVal=dgi('InsOS'+i).value;
	var sign = discVal.substr(discVal.length-1);
	var insDiscVal=0;
	if(sign=='%'){
		var percVal = discVal.substr(0, discVal.length-1);
		var percDiscVal = (TotalOS * percVal) / 100;
		var BalanceOSValue = parseFloat(dgi('TotalOS'+i).value)-percDiscVal;
	}else{
		var BalanceOSValue = parseFloat(dgi('TotalOS'+i).value)-parseFloat(dgi('InsOS'+i).value);
	}
	dgi('BalanceOS'+i).value=BalanceOSValue;

	//START CODE TO CLCULATE TOTAL BALANCE CHARGES OF OD AND OS OU
	if(dgi('BalanceOD'+i)){
		if(!parseFloat(dgi('BalanceOD'+i).value)) { dgi('BalanceOD'+i).value=0;}
	}

	if(!parseFloat(dgi('BalanceOS'+i).value)) { dgi('BalanceOS'+i).value=0;}

	//START CODE TO SET DECIMAL VALUE
	justify2Decimal(dgi('SubTotalOS'+i));
	justify2Decimal(dgi('TotalOS'+i));
	justify2Decimal(dgi('BalanceOS'+i));
	//END CODE TO SET DECIMAL VALUE

	// CALCULATE GRAND TOTAL
	var ODLen = dgi('txtTotOD').value;
	var ODTot = 0;
	for(i=0; i<ODLen; i++)
	{
		if(dgi('BalanceOD'+i)){
			var odVal = dgi('BalanceOD'+i).value;
			if(odVal=='') { odVal=0; }
			ODTot = (parseFloat(ODTot)) + (parseFloat(odVal));
		}
	}

	// CALCULATE GRAND TOTAL
	var OSLen = dgi('txtTotOS').value;
	var OSTot = 0;
	for(i=0; i<OSLen; i++)
	{
		if(dgi('BalanceOS'+i)){
			var osVal = dgi('BalanceOS'+i).value;
			if(osVal=='') { osVal=0; }
			OSTot = (parseFloat(OSTot)) + (parseFloat(osVal));
		}
	}


		if(OSTot=='') { OSTot=0; }
		if(ODTot=='') { ODTot=0; }

		clSupplyValue = parseFloat(ODTot)+parseFloat(OSTot);
		dgi('clSupply').value=clSupplyValue;
	//}
		var clExam = dgi('clExam').value;
		
		if(clSupplyValue=='') { clSupplyValue=0; }
		if(clExam=='') { clExam=0; }	
			
		dgi('totalCharges').value = parseFloat(clExam)+parseFloat(clSupplyValue);

		justify2Decimal(dgi('clExam'));
		justify2Decimal(dgi('clSupply'));
		justify2Decimal(dgi('totalCharges'));		
}


</script>
<?php 

$odrDtLbl='';

if($_REQUEST['newOrder'] == '1' || (!$print_order_id)){
		$dos = date('Y-m-d');//YYYY-mm-dd
}
$finalWrkshtExist='no';
?>
</head>
<body topmargin='0' rightmargin='0' leftmargin='0' bottommargin='0' style="background-color:#ecdeec" class="scrol_la_color">
<div align="center" id="loading_img" width="100%" style="display:none; top:150px; left:200px; z-index:1000; position:absolute;">
	<img src="../../images/loading_image.gif">
</div>

<form action="cl_dispense.php" method="post" name="frmPrintOrder">
<input type="hidden" name="recordSave" value="saveTrue">
<input type="hidden" name="recordSavePrint" value="">
<input type="hidden" name="print_order_id" value="<?php echo $print_order_id;?>">
<input type="hidden" name="prescripClwsId" id="prescripClwsId" value="<?php echo $prescripClwsId;?>">

<input type="hidden" name="hiddClwsIdTemp" id="hiddClwsIdTemp" value="<?php echo $hiddClwsIdTemp;?>">
<input type="hidden" name="cptCodeOD" value="<?php echo $cptCodeOD;?>">
<input type="hidden" name="cptCodeOS" value="<?php echo $cptCodeOS;?>">

<div class="container-fluid">
<div class="whitebox"><table cellpadding="0" cellspacing="0"  border="0" width="100%">
	<tr height="22"  class="cltop txt_11b">
		<td align="left" >
			<table cellpadding="0" cellspacing="0"  border="0" width="100%">
				<tr height="22"  class="la_bg   white_color txt_11b ">
					<td align="left" width="450px">&nbsp;CL Dispense</td>
					<td align="left" nowrap="nowrap" id="odrDtLblId" >
                    <?php echo $row_address['fname'].' '.$row_address['lname'].' - '.$row_address['id'];?>
                    </td>
				</tr>
			</table>
         </td>
	</tr>
  <?php if($displayOrder==1){?>    
	<tr height="22" >
	  <td align="left" >
      	<div >
        	<div class="" ><strong>Description&nbsp;:&nbsp;&nbsp;</strong></div>
            <div class="" ><textarea id="desc" name="desc" rows="2" style="width:100%" class="form-control"></textarea></div>
        </div>
      </td>
    </tr>
	
     <TR  class="la_sel_mds2" >
		<td align="left" >

		  <table align="left"  cellpadding="0" cellspacing="0" border="0" width="100%">
		    <!-- START -->
		    <tr>
		      <td width="100%" id="tdPrescripId" >
		        </td>
	         </tr>
		    
		    
		    <tr height="15">
		      <td ></td>
	         </tr>

		    <tr height="22"  class=" white_color txt_11b ">
		      <TD >
  <div id="typeDiv">
  <div class="orddisp txt_11b">
    <div class="row"><div class="col-sm-5"><div >
            &nbsp;Order Trial #/Dispense
            </div></div>
          <div class="col-sm-7"><div class="form-inline">
            CL Exam&nbsp;:&nbsp;<?php echo $GLOBALS['currency'];?><input type="text" readonly id="clExam" name="clExam" value="<?php echo $cptEvalCharges;?>" size="7" class="form-control" >
            &nbsp;&nbsp;&nbsp;
            CL Supply&nbsp;:&nbsp;<?php echo $GLOBALS['currency'];?>
            <input type="text" id="clSupply" name="clSupply" readonly value="<?php echo $clSupply;?>" size="7" class="form-control" >&nbsp;&nbsp;&nbsp;
            Total&nbsp;:&nbsp;<?php echo $GLOBALS['currency'];?><input type="text" id="totalCharges" readonly name="totalCharges" value="<?php echo $totalCharges;?>" size="7" class="form-control" >&nbsp;&nbsp;&nbsp;
            </div></div></div>
  
  </div>
    
    <div style="height:210px; overflow-y:scroll" class="table-responsive">
    <table class="table table-bordered table-hover table-striped table-condensed">
      <tr class="grythead" >
        <td style="width:18px;" class="blue_color txt_10b">&nbsp;</td> 
        <td style="width:90px;"  class="txt_11b">Type</td>
        <td style="width:90px;"  class="txt_11b">Lens&nbsp;Code</td>
        <td style="width:90px;"  class="txt_11b">Color</td>
        <td style="width:50px;" class="txt_11b">Price</td>
        <td style="width:41px;" class="txt_11b">Qty</td>  
        <td style="width:65px;" class="txt_11b nowrap">Sub Total</td> 
        <td style="width:63px;" class="txt_11b">Discount</td> 
        <td style="width:37px;" class="txt_11b">Total</td>
        <td style="width:42px;" class="txt_11b">Ins.</td> 
        <td style="width:70px;" class="txt_11b">Balance</td> 
        
        </tr>
      <?php
		$odLen = sizeof($LensBoxOD);
		for($i=0; $i< $odLen; $i++) {                                        
		?>							
      <tr id="typeTrOD<?php echo $i;?>">
        <td style="width:21px;" class="blue_color txt_10b">&nbsp;OD
          <input type="hidden" name="ordODId<?php echo $i;?>" id="ordODId<?php echo $i;?>" value="<?php echo $arrPrintOD[$i]['orDetId'];?>" />
          <input type="hidden" name="chld_id_OD<?php echo $i;?>" id="chld_id_OD<?php echo $i;?>" value="<?php echo $arrPrintOD[$i]['chldIdOD'];?>" />
          <input type="hidden" name="tot_disp_OD<?php echo $i;?>" id="tot_disp_OD<?php echo $i;?>" value="<?php echo $arrPrintOD[$i]['totalQtyDispensed'];?>" />
          <input type="hidden" name="cl_det_dis_idOD<?php echo $i;?>" id="cl_det_Dis_idOD<?php echo $i;?>" value="<?php echo $arrPrintOD[$i]['discount_table_id'];?>" />
          </td> 
        <td>
          <div class="fl">
            <input type="text" name="LensBoxOD<?php echo $i;?>" id="LensBoxOD<?php echo $i;?>" value="<?php echo $LensBoxOD[$i];?>" size="40" class="form-control" readonly   />
            </div>
          </td>
        <td>
          <select name="lensNameIdList<?php echo $i;?>" id="lensNameIdList<?php echo $i;?>" class="txt_10 minimal form-control" ">
            <option value="">Select</option>
            <?php echo lenseCodes($arrLensCode, $arrPrintOD[$i]['code']); ?>
            </select>
          </td>
        <td>
          <select name="colorNameIdList<?php echo $i;?>" id="colorNameIdList<?php echo $i;?>" class="txt_10 minimal form-control">
            <option value="">Select</option>
            <?php echo lenseColors($arrLensColor, $arrPrintOD[$i]['color']);?>
            </select>
          </td>
        
        <td><input onBlur="javascript:calcTotalBalODFn('<?php echo $i;?>');justify2Decimal(this);"  id="PriceOD<?php echo $i;?>" type="text" name="PriceOD<?php echo $i;?>" value="<?php echo $PriceOD[$i];?>" size="7" class="form-control" > </td> 
        <td> <input onBlur="javascript:calcTotalBalODFn('<?php echo $i;?>');" type="text" name="QtyOD<?php echo $i;?>" value="<?php echo $arrPrintOD[$i]['qty'];?>" size="7" class="form-control"  id="QtyOD<?php echo $i;?>" /></td> 
        <td><input onBlur="javascript:calcTotalBalODFn('<?php echo $i;?>');justify2Decimal(this);" class="form-control" type="text" name="SubTotalOD<?php echo $i;?>" id="SubTotalOD<?php echo $i;?>" value="<?php echo $arrPrintOD[$i]['subTotal'];?>" size="7" > </td> 
        <td class="alignLeft"><input onBlur="javascript:calcTotalBalODFn('<?php echo $i;?>');justify2Decimal(this);"  id="DiscountOD<?php echo $i;?>" type="text" class="form-control " name="DiscountOD<?php echo $i;?>" value="<?php echo $arrPrintOD[$i]['discount'];?>" size="7" ></td> 
        <td ><input onBlur="javascript:calcTotalBalODFn('<?php echo $i;?>');justify2Decimal(this);"  type="text" class="form-control " name="TotalOD<?php echo $i;?>" id="TotalOD<?php echo $i;?>" value="<?php echo $arrPrintOD[$i]['total'];?>" size="7"></td>
        <td ><input onBlur="javascript:calcTotalBalODFn('<?php echo $i;?>');justify2Decimal(this);" class="form-control" type="text" name="InsOD<?php echo $i;?>" id="InsOD<?php echo $i;?>" value="<?php echo $arrPrintOD[$i]['insurance'];?>" size="7" > </td> 
        <td><input onBlur="javascript:calcTotalBalODFn('<?php echo $i;?>');justify2Decimal(this);" class="form-control" type="text" name="BalanceOD<?php echo $i;?>" id="BalanceOD<?php echo $i;?>" value="<?php echo $arrPrintOD[$i]['balance'];?>" size="7" /></td> 
        </tr>
      <?php
		}
		$osLen = sizeof($LensBoxOS);
		for($i=0; $i< $osLen; $i++) {                                        
		?>	                       
      <tr id="typeTrOS<?php echo $i;?>">
        <td class="green_color txt_10b">&nbsp;OS
          <input type="hidden" name="ordOSId<?php echo $i;?>" id="ordOSId<?php echo $i;?>" value="<?php echo $arrPrintOS[$i]['orDetId'];?>" />
          <input type="hidden" name="chld_id_OS<?php echo $i;?>" id="chld_id_OS<?php echo $i;?>" value="<?php echo $arrPrintOS[$i]['chldIdOS'];?>" />
          <input type="hidden" name="tot_disp_OS<?php echo $i;?>" id="tot_disp_OS<?php echo $i;?>" value="<?php echo $arrPrintOS[$i]['totalQtyDispensed'];?>" />
		  <input type="hidden" name="cl_det_dis_idOS<?php echo $i;?>" id="cl_det_Dis_idOS<?php echo $i;?>" value="<?php echo $arrPrintOS[$i]['discount_table_id'];?>" />          
          </td> 
        <td><span class="fl">
          <input type="text" name="LensBoxOS<?php echo $i;?>" id="LensBoxOS<?php echo $i;?>" value="<?php echo $LensBoxOS[$i];?>" size="40" class="form-control" readonly   />
        </span>
          <div class="fl"></div>
          </td>
        <td>
          <select name="lensNameIdListOS<?php echo $i;?>" id="lensNameIdListOS<?php echo $i;?>" class="txt_10 minimal form-control" >
            <option value="">Select</option>
            <?php echo lenseCodes($arrLensCode, $arrPrintOS[$i]['code']); ?>
            </select>
          </td>
        <td>
          <select name="colorNameIdListOS<?php echo $i;?>" id="colorNameIdListOS<?php echo $i;?>" class="txt_10 minimal form-control" >
            <option value="">Select</option>
            <?php echo lenseColors($arrLensColor, $arrPrintOS[$i]['color']);?>                                      
            </select>
          </td>
        <td><input onBlur="javascript:calcTotalBalOSFn('<?php echo $i;?>');justify2Decimal(this);"  id="PriceOS<?php echo $i;?>" type="text" name="PriceOS<?php echo $i;?>" value="<?php echo $PriceOS[$i];?>" size="7" class="form-control" > </td> 
        <td> <input onBlur="javascript:calcTotalBalOSFn('<?php echo $i;?>');" type="text" name="QtyOS<?php echo $i;?>" value="<?php echo $arrPrintOS[$i]['qty'];?>" size="7" class="form-control"  id="QtyOS<?php echo $i;?>" /></td> 
        <td><input onBlur="javascript:calcTotalBalOSFn('<?php echo $i;?>');justify2Decimal(this);" class="form-control" type="text" name="SubTotalOS<?php echo $i;?>" id="SubTotalOS<?php echo $i;?>" value="<?php echo $arrPrintOS[$i]['subTotal'];?>" size="7" > </td> 
        <td class="alignLeft"><input onBlur="javascript:calcTotalBalOSFn();justify2Decimal(this);" id="DiscountOS<?php echo $i;?>" type="text" class="form-control" name="DiscountOS<?php echo $i;?>" value="<?php echo $arrPrintOS[$i]['discount'];?>" size="7"></td> 
        <td><input onBlur="javascript:calcTotalBalOSFn('<?php echo $i;?>');justify2Decimal(this);"  id="TotalOS<?php echo $i;?>" type="text" class="form-control" name="TotalOS<?php echo $i;?>" value="<?php echo $arrPrintOS[$i]['total'];?>" size="7"></td>
        <td><input onBlur="javascript:calcTotalBalOSFn('<?php echo $i;?>');justify2Decimal(this);" class="form-control" type="text" name="InsOS<?php echo $i;?>" id="InsOS<?php echo $i;?>" value="<?php echo $arrPrintOS[$i]['insurance'];?>" size="7" > </td> 
        <td><input onBlur="javascript:calcTotalBalOSFn('<?php echo $i;?>');justify2Decimal(this);" class="form-control" type="text" name="BalanceOS<?php echo $i;?>" id="BalanceOS<?php echo $i;?>" value="<?php echo $arrPrintOS[$i]['balance'];?>" size="7" /></td> 
        </tr>
      <?php }?>	  

      </table>
    </div> 
</div>                   
		        
		        </TD>				
	         </tr>			
		    <!-- END -->
	      </table>	
          
  <?php } else { 
  		echo '<tr style="height:250px; text-align:center"><td><br><br><br><strong>No Contact Lens Pending for Dispense</strong>.</td></tr>';
  }
  	
  ?>
          
              </td>
	</tr>
	<tr height="5px"><td></td></tr>
	<tr >
		<td align="center" style="height:40px" valign="top">
		  <table border="0" cellpadding="0" cellspacing="0" width="20%" style="margin-top:5px;">
		    <tr  valign="top" >
		      <td align="left" >
		        <?php
                     $txtTotOD = (sizeof($LensBoxOD)==0) ? 1 : sizeof($LensBoxOD);
                     $txtTotOS = (sizeof($LensBoxOS)==0) ? 1 : sizeof($LensBoxOS);
                     ?>
		        <input type="hidden" name="txtTotOD" id="txtTotOD" value="<?php echo $txtTotOD;?>">
		        <input type="hidden" name="txtTotOS" id="txtTotOS" value="<?php echo $txtTotOS;?>">
		        <?php if($displayOrder==1){?>
		        	<input type="button"  class="dff_button btn btn-success" id="SaveBtn" name="SaveBtn"  value="Done" onMouseOver="button_over('SaveBtn')" onMouseOut="button_over('SaveBtn','')" onClick="this.form.submit();"/>
                <?php } ?>
                </td>
		      <!--<td align="left" ><input type="button"  class="dff_button" id="SavePrintBtn"  value="Save & Print" onMouseOver="button_over('SavePrintBtn')" onMouseOut="button_over('SavePrintBtn','')" onClick="this.form.recordSavePrint.value='Yes'; this.form.submit(); "/></td>-->
		      <!--<td align="left" ><input type="button"  class="dff_button" id="PrintBtn"  value="Print" onMouseOver="button_over('PrintBtn')" onMouseOut="button_over('PrintBtn','')" onClick="printOrderPdfFn('<?php echo $print_order_id;?>');" /></td>-->
		      <td align="left" ><input type="button"  class="dff_button btn btn-danger"  id="CancelBtn" value="Close"  onMouseOver="button_over('CancelBtn')" onMouseOut="button_over('CancelBtn','')" onClick="window.close();"></td>
		      </tr>
		    </table>	    </td>
	</tr>
</table></div>
</div>
</form>
<script type="text/javascript">
	hideDisplayOrderFn('orderTrialDspDoctorId','orderTrialId','orderTrialDspNumberId','orderTrialNumberId');
	checkInsCase(document.getElementById('ins_case').value,'<?php echo $auth_number;?>','<?php echo $auth_amount;?>');

	calcTotalBalODFn(0);
	calcTotalBalOSFn(0);
</script>
