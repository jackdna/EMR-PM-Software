<?php
/*
File: medication_result.php
Coded in PHP7
Purpose: Show Medication Order Report
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../config/config.php");
require_once(dirname('__FILE__')."/../../library/classes/functions.php");


if($_POST['generateRpt']){

	$authProviderNameArr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$opInitial = $authProviderNameArr[1][0];
	$opInitial .= $authProviderNameArr[0][0];
	$opInitial = strtoupper($opInitial);
	
	if(empty($_POST['upc_code'])==false){
		$upc_code=str_replace(' ','', $_POST['upc_code']);
		$upc_code="'".str_replace(",", "','", $upc_code)."'";
	}
	if(empty($_POST['lot_number'])==false){
		$lot_number=str_replace(' ','', $_POST['lot_number']);
		$lot_number="'".str_replace(",", "','", $lot_number)."'";
	}
	
	//TYPES
	$typeRs = imw_query("select * from in_module_type");
	while($typeRes=imw_fetch_array($typeRs)){
	   $arrTypes[$typeRes['id']]=$typeRes['module_type_name'];
	}
	   
	//Facility
	$facRs = imw_query("select * from in_location");
	$arrfacility[0]='No Facility';
	while($facRes=imw_fetch_array($facRs)){
	   $arrfacility[$facRes['id']]=$facRes['loc_name'];
	}

	//OPERATORS
   $usersRs = imw_query("select id, fname,lname from users");
   while($usersRes=imw_fetch_array($usersRs)){
	   if($usersRes['lname']!='' || $usersRes['fname']!=''){
			$arrUsers[$usersRes['id']]=$usersRes['lname'].', '.$usersRes['fname']; 
			//TWO CHARACTERS
			$opInit = substr($usersRes['lname'],0,1);
			$opInit .= substr($usersRes['fname'],0,1);
			$arrUsersTwoChar[$usersRes['id']] = strtoupper($opInit);
	   }
   }
	
	//GETTING MEDICINE ID
	$medcince_id=0;
	$rs=imw_query("Select id FROM  in_module_type WHERE LOWER(module_type_name)='medicine'");	
	$res=imw_fetch_assoc($rs);
	$medicine_id=$res['id'];
	unset($rs);
	
	
	$dateFrom=saveDateFormat($_POST['date_from']);
	$dateTo=saveDateFormat($_POST['date_to']);


	$mainQry="Select ord_det.order_id, ord_det.id, DATE_FORMAT(ord_det.entered_date, '%m-%d-%y') as 'entered_date',
	DATE_FORMAT(ord_det.dispensed, '%m-%d-%y') as 'order_date_disp',
	ord_det.operator_id, ord_det.item_id, ord_det.order_status,
	ord_det.price, ord_det.loc_id,  
	ord_det.discount, ord_det.total_amount, ord_det.patient_id, ord_det.module_type_id, ord_det.item_name, ord_det.upc_code,
	ord_det.pt_paid, ord_det.ins_amount, ord_det.pt_resp,
	ord_det.item_name, patient_data.fname, patient_data.lname, 
	GROUP_CONCAT(lot_det.lot_no,' ') AS lot_no, SUM(lot_det.qty) AS qty 
	FROM in_order_details ord_det 
	JOIN in_order ON in_order.id= ord_det.order_id 
	JOIN in_order_lot_details lot_det ON lot_det.order_detail_id= ord_det.id 
	LEFT JOIN patient_data ON patient_data.id = ord_det.patient_id 
	WHERE ord_det.del_status='0' AND (ord_det.entered_date BETWEEN '".$dateFrom."' AND '".$dateTo."') 
	AND ord_det.module_type_id='".$medicine_id."'";

	if(empty($_POST['facility'])==false){
		$mainQry.=' AND ord_det.loc_id IN('.$_POST['facility'].')';
	}
	if(empty($lot_number)==false){
		$mainQry.=" AND lot_det.lot_no IN(".$lot_number.")";
	}
	if(empty($upc_code)==false){
		$mainQry.=" AND ord_det.upc_code IN(".$upc_code.")";
	}
	if(empty($_POST['order_status'])==false){
		if($_POST['order_status']=='pending'){
			$mainQry.=" AND (ord_det.order_status='".$_POST['order_status']."' OR ord_det.order_status='')";
		}else{
			$mainQry.=" AND ord_det.order_status='".$_POST['order_status']."'";
		}
	}
	$mainQry.=' GROUP BY lot_det.order_detail_id ORDER BY ord_det.entered_date, patient_data.lname, patient_data.fname';
	
	$mainRs=imw_query($mainQry);
	$mainNumRs=imw_num_rows($mainRs);
/*	while($mainRes=imw_fetch_array($mainRs)){
		$ord_det_id=$mainRes['id'];
		$order_status=($mainRes['order_status']=='')? 'Pending' : ucfirst($mainRes['order_status']);
		
		$arrMainDetail[$ord_det_id]['order_id'] = $mainRes['order_id'];
		$arrMainDetail[$ord_det_id]['entered_date'] = $mainRes['entered_date'];
		$arrMainDetail[$ord_det_id]['pat_name'] = $mainRes['lname'].' '.$mainRes['fname'].' - '.$mainRes['patient_id'];
		$arrMainDetail[$ord_det_id]['facility'] = $arrfacility[$mainRes['loc_id']];
		$arrMainDetail[$ord_det_id]['lot_no'] = $mainRes['lot_no'];
		$arrMainDetail[$ord_det_id]['item'] = $mainRes['item_name'].' - '.$mainRes['upc_code'];
		$arrMainDetail[$ord_det_id]['qty']+= $mainRes['qty'];
		$arrMainDetail[$ord_det_id]['order_id'] = $order_status;
		$arrMainDetail[$ord_det_id]['order_id'] = $mainRes['order_date_disp'];
		$arrMainDetail[$ord_det_id]['order_id'] = $arrUsersTwoChar[$mainRes['operator_id']];
	}*/

	
	$html=$htmlPDF=$reportHtml=$reportHtmlPDF='';
	$grandTotPrice=$grandTotQty=0;


	if($mainNumRs>0){
		while($arrMainDetail=imw_fetch_assoc($mainRs)){
			$totalPrice=0;
			$totQty+=$arrMainDetail['stock'];
			$oprName = $arrUsersTwoChar[$arrMainDetail['entered_by']];
			$totalPrice=$arrMainDetail['price']*$arrMainDetail['qty'];
			$order_status=($arrMainDetail['order_status']=='')? 'Pending' : ucfirst($arrMainDetail['order_status']);
			
			$grandTotQty+=$arrMainDetail['qty'];
			$grandTotPrice+=$totalPrice;
								
			$html.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$arrMainDetail['order_id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$arrMainDetail['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$arrMainDetail['lname'].' '.$arrMainDetail['fname'].' - '.$arrMainDetail['patient_id'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$arrfacility[$arrMainDetail['loc_id']].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$arrMainDetail['lot_no'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$arrMainDetail['item_name'].' - '.$arrMainDetail['upc_code'].'</td>
			<td class="whiteBG rptText13 alignRight">'.$arrMainDetail['qty'].'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight">'.currency_symbol(true).number_format($totalPrice, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$order_status.'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$arrMainDetail['order_date_disp'].'</td>
			<td class="whiteBG rptText13 alignLeft">&nbsp;'.$arrUsersTwoChar[$arrMainDetail['operator_id']].'</td>
			</tr>';

			$htmlPDF.='<tr style="height:20px;">
			<td class="whiteBG rptText13 alignLeft" style="width:50px">&nbsp;'.$arrMainDetail['order_id'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:80px">&nbsp;'.$arrMainDetail['entered_date'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:150px">&nbsp;'.$arrMainDetail['lname'].' '.$arrMainDetail['fname'].' - '.$arrMainDetail['patient_id'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:150px">&nbsp;'.$arrfacility[$arrMainDetail['loc_id']].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:75px">&nbsp;'.$arrMainDetail['lot_no'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:160px">&nbsp;'.$arrMainDetail['item_name'].' - '.$arrMainDetail['upc_code'].'</td>
			<td class="whiteBG rptText13 alignRight" style="width:40px">'.$arrMainDetail['qty'].'&nbsp;</td>
			<td class="whiteBG rptText13 alignRight" style="width:100px">'.currency_symbol(true).number_format($totalPrice, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13 alignLeft" style="width:100px">&nbsp;'.$order_status.'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:80px">&nbsp;'.$arrMainDetail['order_date_disp'].'</td>
			<td class="whiteBG rptText13 alignLeft" style="width:40px">&nbsp;'.$arrUsersTwoChar[$arrMainDetail['operator_id']].'</td>
			</tr>';				
		}
		
		//Final html
		$reportHtml.='
		<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
			<tr style="height:25px;">
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:80px;">Ord#</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:80px;">Order Date</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:120px;">Patient Name</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:120px;">Facility</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:80px;">Lot#</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:160px;">Item Name - UPC Code</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:50px;">Qty</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Tot. Price</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:80px;">Status</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:80px;">Disp. Date</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:40px;">Opr</td>
			</tr>'.$html.'
			<tr><td colspan="11" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
			<tr style="height:20px;">
				<td class="whiteBG rptText13b alignRight" colspan="6">Grand Total: </td>
				<td class="whiteBG rptText13b alignRight">'.$grandTotQty.'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($grandTotPrice, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>
			</tr>				
		</table>';
	
		$reportHtmlPDF.='
		<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
			'.$htmlPDF.'
			<tr><td colspan="11" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
			<tr style="height:20px;">
				<td class="whiteBG rptText13b alignRight" colspan="6">Grand Total: </td>
				<td class="whiteBG rptText13b alignRight">'.$grandTotQty.'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($grandTotPrice, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>
			</tr>				
		</table>';			
		}


	$css = '
	<style type="text/css">
	.reportHeadBG{ font-family: Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; background-color:#D9EDF8;}
	.reportHeadBG1{ font-family: Arial, Helvetica, sans-serif; font-size:13px; font-weight:bold; background-color:#67B9E8; color:#FFF;}
	.reportTitle { font-family: Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; background-color:#7B7B7B; color:#FFF }
	.rptText13 { font-family: Arial, Helvetica, sans-serif; font-size:13px; }
	.rptText13b { font-family: Arial, Helvetica, sans-serif; font-size:13px; font-weight:bold; }
	.rptText12b { font-family: Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; }		
	.whiteBG{ background:#fff; } 
	.alignMiddle{vertical-align:middle;} .alignTop{vertical-align:top;}  .alignBottom{vertical-align:bottom;}
	.alignLeft{text-align:left;} .alignRight{text-align:right;} .alignCenter{text-align:center;} .alignJustify{text-align:justify;}		
	</style>';		

	//SELECTION TO SHOW
	if(empty($_POST['facility'])===false){ $arrfacly=explode(',', $_POST['facility']); }
	$selfacility='All';
	$selfacility=(count($arrfacly)>1)? 'Multi' :((count($arrfacly)=='1')?ucfirst($arrfacility[$_POST['facility']]): $selfacility);
	$upc_code=str_replace("'","", $upc_code);
	$lot_number=str_replace("'","", $lot_number);
	
	//FINAL HTML
	$finalReportHtml='
	<table width="100%" cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
	<tr style="height:20px;">
		<td style="text-align:left;" class="reportHeadBG" width="320px">&nbsp;Medication Orders Report</td>
		<td style="text-align:left;" class="reportHeadBG" width="320px" >&nbsp;Date Range From '.$_POST['date_from'].' To '.$_POST['date_to'].'</td>
		<td style="text-align:left;" class="reportHeadBG" width="auto">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
	</tr>
	<tr style="height:20px;">
		<td class="reportHeadBG">&nbsp;Facility : '.$selfacility.'</td>
		<td class="reportHeadBG">&nbsp;Lot# : '.$lot_number.'</td>
		<td class="reportHeadBG">&nbsp;UPC Code : '.$upc_code.'</td>
	</tr>
	</table>
	'.$reportHtml;

	//FINAL PDF
	if(count($arrMainDetail)>0)
	{
		$mm=14;

		$finalReportHtmlPDF.='
			<page backtop="'.$mm.'mm" backbottom="5mm">
			<page_footer>
					<table style="width:700px;">
						<tr>
							<td style="text-align: center;	width: 700px">Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
			</page_footer>
			<page_header>		
			<table cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
			<tr style="height:20px;">
				<td style="text-align:left;" class="reportHeadBG" width="355">&nbsp;Medication Report</td>
				<td style="text-align:left;" class="reportHeadBG" width="355">&nbsp;Date Range From '.$_POST['date_from'].' To '.$_POST['date_to'].'</td>
				<td style="text-align:left;" class="reportHeadBG" width="355">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
			</tr>
			<tr style="height:20px;">
				<td class="reportHeadBG">&nbsp;Exp. : '.$selfacility.'</td>
				<td class="reportHeadBG">&nbsp;Lot# : '.$lot_number.'</td>
				<td class="reportHeadBG" colspan="2">&nbsp;UPC Code : '.$upc_code.'</td>
			</tr>
			</table>';

			$finalReportHtmlPDF.='
			<table cellpadding="1" cellspacing="1" border="0" style="background:#E3E3E3;">
			<tr>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:50px;">Ord#</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:80px;">Order Date</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:150px;">Patient Name</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:150px;">Facility</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:75px;">Lot#</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:160px;">Item Name - UPC Code</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:40px;">Qty</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Tot. Price</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Status</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:80px;">Disp. Date</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:40px;">Opr</td>
			</tr>
			</table></page_header>'.
			$reportHtmlPDF.'
		</page>';
	}

	$pdfText = $css.$finalReportHtmlPDF;
	file_put_contents('../../library/new_html2pdf/return_order_result.html',$pdfText);
}

?>
<html>
<head>
<title></title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script>
$(document).unbind('keydown').bind('keydown', function (event) {
    var doPrevent = false;
    if (event.keyCode === 8) {
        var d = event.srcElement || event.target;
        if ((d.tagName.toUpperCase() === 'INPUT' && (d.type.toUpperCase() === 'TEXT' || d.type.toUpperCase() === 'PASSWORD' || d.type.toUpperCase() === 'FILE')) 
             || d.tagName.toUpperCase() === 'TEXTAREA') {
            doPrevent = d.readOnly || d.disabled;
        }
        else {
            doPrevent = true;
        }
    }

    if (doPrevent) {
        event.preventDefault();
    }
});
</script>
</head>
<body>
<?php
if(count($arrMainDetail)>0)
{
 echo $finalReportHtml;
}
else
{
	if($_REQUEST['print'])
	{
		$d="display:none;";
	}

	
	echo '<br><div style="text-align:center; '.$d.'"><strong>No Record Found.</strong></div>';
}
 ?>
<form name="medicationFormResult" action="medication_orders_result.php" method="post">
<input type="hidden" name="facility" id="facility" value="" />
<input type="hidden" name="order_status" id="order_status" value="" />
<input type="hidden" name="upc_code" id="upc_code" value="" />
<input type="hidden" name="lot_number" id="lot_number" value="" />
<input type="hidden" name="date_from" id="date_from" value="" />
<input type="hidden" name="date_to" id="date_to" value="" />
<!--<input type="hidden" name="show_report" id="show_report" value="" />-->
<input type="hidden" name="generateRpt" id="generateRpt" value="yes" />
</form>

<?php if(isset($_REQUEST['print'])) { ?>

<script>
var url = '<?php echo $GLOBALS['WEB_PATH'];?>/library/new_html2pdf/createPdf.php?op=l&file_name=../../library/new_html2pdf/return_order_result';
window.location.href = url;
</script>

<?php } ?>

<script type="text/javascript">
$(document).ready(function(){
	var numr = '<?php echo $mainNumRs; ?>';		
	
	//BUTTONS
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Search","top.main_iframe.reports_iframe.submitForm();");
	if(numr>0){
		mainBtnArr[1] = new Array("frame","Print","top.main_iframe.reports_iframe.printreport()");
	}
	top.btn_show("admin",mainBtnArr);
	top.main_iframe.loading('none');		
});
</script>

</body>
</html>