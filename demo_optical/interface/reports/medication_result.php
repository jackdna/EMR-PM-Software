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
	
	//Manufacturers
	$manu_detail_qry = "select id, manufacturer_name from in_manufacturer_details";
	$manu_detail_rs = imw_query($manu_detail_qry);
	while($manu_detail_res=imw_fetch_array($manu_detail_rs))
	{
		$arrManufac[$manu_detail_res['id']]=$manu_detail_res['manufacturer_name'];
	}

	//Vendors
	$arrAllVendors=array();
	$sql="select id,vendor_name from in_vendor_details";
	$res = imw_query($sql);
	while($row = imw_fetch_array($res)){
		$arrAllVendors[$row['id']]=$row['vendor_name'];
	}unset($res);

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
	
	//Vendors
   $vendorRs = imw_query("select id,vendor_name from in_vendor_details where del_status = '0'");
   while($vendorsRes=imw_fetch_array($vendorRs)){
	   if($vendorsRes['vendor_name']!=''){
			$arrVendors[$vendorsRes['id']]=$vendorsRes['vendor_name']; 
	   }
   }
	
	$dateFrom=saveDateFormat($_POST['date_from']);
	$dateTo=saveDateFormat($_POST['date_to']);
	
	if($_POST['show_report']=='detail'){
		$mainQry="SELECT `item`.`name`, `item`.`upc_code`, DATE_FORMAT(`lot`.`expiry_date`,'%m-%d-%Y') AS 'expiry_date',
		`item`.`manufacturer_id`, `item`.`vendor_id`, lot.lot_no, lot.loc_id, SUM(lot.stock) as stock 
		FROM `in_item` `item` 
		LEFT JOIN in_item_lot_total lot ON lot.item_id = item.id 
		JOIN in_location loc on loc.id= lot.loc_id 
		WHERE `item`.`del_status`='0' AND `lot`.`expiry_date`!='0000-00-00'";
	}else{
		$mainQry="SELECT `item`.`name`, `item`.`upc_code`, 
		lot.loc_id, SUM(lot.stock) as stock 
		FROM `in_item` `item` 
		LEFT JOIN in_item_lot_total lot ON lot.item_id = item.id 
		JOIN in_location loc on loc.id= lot.loc_id 
		WHERE `item`.`del_status`='0' AND `lot`.`expiry_date`!='0000-00-00'";
	}

	if(empty($_POST['manufacturer'])==false){ /*Manufacturer filter*/
		$mainQry.=" AND `item`.`manufacturer_id` IN(".$_POST['manufacturer'].")";
	}
	if(empty($_POST['vendor'])==false){ /*Vendor filter*/
		$mainQry.=" AND `item`.`vendor_id` IN(".$_POST['vendor'].")";
	}
	if(empty($lot_number)==false){
		$mainQry.=" AND `lot`.`lot_no` IN(".$lot_number.")";
	}
	if(empty($upc_code)==false){
		$mainQry.=" AND `item`.`upc_code` IN(".$upc_code.")";
	}
	if(empty($_POST['date_from'])==false && empty($_POST['date_to'])==false){ /*Expiry Date from & to*/
		$mainQry.=" AND (`lot`.`expiry_date` BETWEEN '".$dateFrom."' AND '".$dateTo."')";
		$selExpDat =" From ".date("m-d-Y",strtotime($dateFrom))." To ".date("m-d-Y",strtotime($dateTo));
	}
	elseif(empty($_POST['date_from'])==false){ /*Expiry Date from*/
		$mainQry.=" AND `lot`.`expiry_date` > '".$dateFrom."'";
		$selExpDat =" From ".date("m-d-Y",strtotime($dateFrom));
	}
	elseif(empty($_POST['date_to'])==false){ /*Expiry Date to*/
		$mainQry.=" AND `lot`.`expiry_date` < '".$dateTo."'";
		$selExpDat =" Up To ".date("m-d-Y",strtotime($dateTo));
	}
	$selExpDat = ($selExpDat=="")?"ALL":$selExpDat;
	if($_POST['show_report']=='detail'){
		$mainQry.=' GROUP BY lot.lot_no, lot.loc_id ORDER BY loc.loc_name, `item`.name, item.upc_code';
	}else{
		$mainQry.=' GROUP BY item.id, lot.loc_id ORDER BY loc.loc_name, `item`.name, item.upc_code';
	}
	
	$mainRs=imw_query($mainQry);
	$mainNumRs=imw_num_rows($mainRs);
	while($mainRes=imw_fetch_array($mainRs)){
		$arrMainDetail[$mainRes['loc_id']][] = $mainRes;
		$showVendor = $mainRes['vendor_id'];
		$showfacility = $arrfacility[$mainRes['loc_id']];
		$showmanufact = $mainRes['manufacturer_id'];
	}

	
	$html=$htmlPDF=$reportHtml=$reportHtmlPDF='';
	//DETAIL
	if($_POST['show_report']=='detail'){
		if(count($arrMainDetail)>0){
			$grandAmount=0; $totQty=$totPrice=$Totdisc=$Totamt=0;
			foreach($arrMainDetail as $loc_id =>$itemData){
				$totQty=0;
				$html.='<tr><td class="reportTitle" colspan="6">&nbsp; Facility: '.$arrfacility[$itemDetails['loc_id']].'</td></tr>';
				$htmlPDF.='<tr ><td class="reportTitle" colspan="6">&nbsp; Facility: '.$arrfacility[$itemDetails['loc_id']].'</td></tr>';
				
				foreach($itemData as $itemDetails){
					$subTotAmt =0;
					$totQty+=$itemDetails['stock'];
					$oprName = $arrUsersTwoChar[$itemDetails['entered_by']];
										
					$html.='<tr style="height:20px;">
					<td class="whiteBG rptText13 alignLeft">&nbsp;'.$arrManufac[$itemDetails['manufacturer_id']].'</td>
					<td class="whiteBG rptText13 alignLeft">&nbsp;'.$arrAllVendors[$itemDetails['vendor_id']].'</td>
					<td class="whiteBG rptText13 alignLeft">&nbsp;'.$itemDetails['name'].' - '.$itemDetails['upc_code'].'</td>
					<td class="whiteBG rptText13 alignRight">'.$itemDetails['stock'].'&nbsp;</td>
					<td class="whiteBG rptText13 alignCenter">'.$itemDetails['lot_no'].'</td>
					<td class="whiteBG rptText13 alignCenter">'.$itemDetails['expiry_date'].'</td>
					</tr>';
		
					$htmlPDF.='<tr style="height:20px;">
					<td class="whiteBG rptText13 alignLeft" style="width:130px;">&nbsp;'.$arrManufac[$itemDetails['manufacturer_id']].'</td>
					<td class="whiteBG rptText13 alignLeft" style="width:130px;">&nbsp;'.$arrAllVendors[$itemDetails['vendor_id']].'</td>
					<td class="whiteBG rptText13 alignLeft" style="width:160px;">&nbsp;'.$itemDetails['name'].' - '.$itemDetails['upc_code'].'</td>
					<td class="whiteBG rptText13 alignRight" style="width:100px;">'.$itemDetails['stock'].'&nbsp;</td>
					<td class="whiteBG rptText13 alignCenter" style="width:105px;">'.$itemDetails['lot_no'].'</td>
					<td class="whiteBG rptText13 alignLeft" style="width:100px;">'.$itemDetails['expiry_date'].'</td>
					</tr>';
				}
				
				$grandTotQty+=$totQty;
				//SUB TOTAL
				$html.='
				<tr style="height:20px;">
					<td class="whiteBG rptText13b alignRight" colspan="3">Sub Total: </td>
					<td class="whiteBG rptText13b alignRight">'.$totQty.'&nbsp;</td>
					<td class="whiteBG rptText13 alignLeft">&nbsp;</td>
					<td class="whiteBG rptText13 alignCenter">&nbsp;</td>
				</tr>';

				$htmlPDF.='
				<tr style="height:20px;">
					<td class="whiteBG rptText13b alignRight" colspan="3">Sub Total: </td>
					<td class="whiteBG rptText13b alignRight">'.$totQty.'&nbsp;</td>
					<td class="whiteBG rptText13 alignLeft">&nbsp;</td>
					<td class="whiteBG rptText13 alignCenter">&nbsp;</td>
				</tr>';
			}
			
			//Final html
			$reportHtml.='
			<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
				<tr style="height:25px;">
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:150px;">Manufacturer</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:150px;">Vendor</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:184px;">Product Name - UPC Code</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:80px;">Quantity</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:184px;">Lot#</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Expiry Date</td>
				</tr>'.$html.'
				<tr><td colspan="6" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
				<tr style="height:20px;">
					<td class="whiteBG rptText13b alignRight" colspan="3">Grand Total: </td>
					<td class="whiteBG rptText13b alignRight">'.$grandTotQty.'&nbsp;</td>
					<td class="whiteBG rptText13 alignLeft">&nbsp;</td>
					<td class="whiteBG rptText13 alignCenter">&nbsp;</td>
				</tr>				
			</table>';
		
			$reportHtmlPDF.='
			<table style="border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
			'.$htmlPDF.'
			<tr><td colspan="6" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
			<tr style="height:20px;">
				<td class="whiteBG rptText13b alignRight" colspan="3">Grand Total: </td>
				<td class="whiteBG rptText13b alignRight">'.$grandTotQty.'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">&nbsp;</td>
			</tr>				
			</table>';
			}
	}
	//SUMMART
	if($_POST['show_report']=='summary'){
		if(count($arrMainDetail)>0){
			$grandAmount=0; $grandTotQty=$totPrice=$Totdisc=$Totamt=0;
			foreach($arrMainDetail as $loc_id => $itemData){
				foreach($itemData as $itemDetails){
					$subTotAmt =0;
					$grandTotQty+=$itemDetails['stock'];
					$oprName = $arrUsersTwoChar[$itemDetails['entered_by']];
										
					$html.='<tr style="height:20px;">
					<td class="whiteBG rptText13 alignLeft">&nbsp;'.$arrfacility[$itemDetails['loc_id']].'</td>
					<td class="whiteBG rptText13 alignLeft">&nbsp;'.$itemDetails['name'].' - '.$itemDetails['upc_code'].'</td>
					<td class="whiteBG rptText13 alignRight">'.$itemDetails['stock'].'&nbsp;</td>
					<td class="whiteBG rptText13 alignCenter">&nbsp;</td>
					</tr>';
					
					$htmlPDF.='<tr style="height:20px;">
					<td class="whiteBG rptText13 alignLeft" style="width:160px;">&nbsp;'.$arrfacility[$itemDetails['loc_id']].'</td>
					<td class="whiteBG rptText13 alignLeft" style="width:200px;">&nbsp;'.$itemDetails['name'].' - '.$itemDetails['upc_code'].'</td>
					<td class="whiteBG rptText13 alignRight" style="width:100px;">'.$itemDetails['stock'].'&nbsp;</td>
					<td class="whiteBG rptText13 alignCenter" style="width:275px;">&nbsp;</td>
					</tr>';
				}
			}
			
			//Final html
			$reportHtml.='
				<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
					<tr style="height:25px;">
						<td class="reportHeadBG1 alignTop" style="text-align:left; width:284px;">&nbsp;Facility</td>
						<td class="reportHeadBG1 alignTop" style="text-align:center; width:284px;">Product Name - UPC Code</td>
						<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Quantity</td>
						<td class="reportHeadBG1 alignTop" style="text-align:center; width:auto;">&nbsp;</td>
					</tr>'.$html.'
					<tr><td colspan="4" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
					<tr style="height:20px;">
						<td class="whiteBG rptText13b alignRight" colspan="2">Total: </td>
						<td class="whiteBG rptText13b alignRight">'.$grandTotQty.'&nbsp;</td>
						<td class="whiteBG rptText13 alignCenter">&nbsp;</td>
					</tr>				
				</table>';
			
				$reportHtmlPDF.='
				<table style="border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
				'.$htmlPDF.'
				<tr><td colspan="4" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
				<tr style="height:20px;">
					<td class="whiteBG rptText13b alignRight" colspan="2">Total: </td>
					<td class="whiteBG rptText13b alignRight">'.$grandTotQty.'&nbsp;</td>
					<td class="whiteBG rptText13 alignCenter">&nbsp;</td>
				</tr>				
				</table>';
			}
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
	if(empty($_POST['vendor'])===false){ $arrVendor=explode(',', $_POST['vendor']); }
	if(empty($_POST['manufacturer'])===false){ $manufacturers=explode(',', $_POST['manufacturer']); }
	$selVendor='All';
	$selManufacturer='All';
	$selVendor=(count($arrVendor)>1)? 'Multi' : ((count($arrVendor)=='1')? ucfirst($arrVendors[$showVendor]): $selVendor);
	$selManufacturer=(count($manufacturers)>1)? 'Multi' : ((count($manufacturers)=='1')? ucfirst($arrManufac[$showmanufact]): $selManufacturer);
	$upc_code=str_replace("'","", $upc_code);
	$lot_number=str_replace("'","", $lot_number);
	
	//FINAL HTML
	$finalReportHtml='
	<table width="100%" cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
	<tr style="height:20px;">
		<td style="text-align:left;" class="reportHeadBG" width="220px">&nbsp;Medication Report</td>
		<td style="text-align:left;" class="reportHeadBG" width="220px" >&nbsp;Manufacturer : '.$selManufacturer.'</td>
		<td style="text-align:left;" class="reportHeadBG" width="220px" >&nbsp;Vendor : '.$selVendor.'</td>
		<td style="text-align:left;" class="reportHeadBG" width="auto">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
	</tr>
	<tr style="height:20px;">
		<td colspan="2" class="reportHeadBG">&nbsp;Expiry Date : '.$selExpDat.'</td>
		<td class="reportHeadBG">&nbsp;Lot# : '.$lot_number.'</td>
		<td class="reportHeadBG">&nbsp;UPC Code : '.$upc_code.'</td>
	</tr>
	</table>
	'.$reportHtml;

	//FINAL PDF
	if(count($arrMainDetail)>0)
	{
		$mm=14;
		if($_POST['show_report']=='detail'){ $mm=17;	}
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
				<td style="text-align:left;" class="reportHeadBG" width="220">&nbsp;Medication Report</td>
				<td style="text-align:left;" class="reportHeadBG" width="155">&nbsp;Manu. : '.$selManufacturer.'</td>
				<td style="text-align:left;" class="reportHeadBG" width="155">&nbsp;Vendor : '.$selVendor.'</td>
				<td style="text-align:left;" class="reportHeadBG" width="205">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
			</tr>
			<tr style="height:20px;">
				<td class="reportHeadBG">&nbsp;Exp. : '.$selExpDat.'</td>
				<td class="reportHeadBG">&nbsp;Lot# : '.$lot_number.'</td>
				<td class="reportHeadBG" colspan="2">&nbsp;UPC Code : '.$upc_code.'</td>
			</tr>
			</table>';
			if($_POST['show_report']=='detail'){
				$finalReportHtmlPDF.='
				<table cellpadding="1" cellspacing="1" border="0" style="background:#E3E3E3;">
				<tr>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:130px;">Manufacturer</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:130px;">Vendor</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:160px;">Product Name - UPC Code</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Quantity</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:105px;">Lot#</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Expiry Date</td>
				</tr>';
			}else{
				$finalReportHtmlPDF.='
				<table cellpadding="1" cellspacing="1" border="0" style="background:#E3E3E3;">
				<tr>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:160px;">Facility</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:200px;">Product Name - UPC Code</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Quantity</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:275px;">&nbsp;</td>
				</tr>';
			}
			
			$finalReportHtmlPDF.='
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
<form name="medicationFormResult" action="medication_result.php" method="post">
<input type="hidden" name="manufacturer" id="manufacturer" value="" />
<input type="hidden" name="vendor" id="vendor" value="" />
<input type="hidden" name="upc_code" id="upc_code" value="" />
<input type="hidden" name="lot_number" id="lot_number" value="" />
<!--<input type="hidden" name="med_type" id="med_type" value="" />-->
<input type="hidden" name="date_from" id="date_from" value="" />
<input type="hidden" name="date_to" id="date_to" value="" />
<input type="hidden" name="show_report" id="show_report" value="" />
<input type="hidden" name="generateRpt" id="generateRpt" value="yes" />
</form>

<?php if(isset($_REQUEST['print'])) { ?>

<script>
var url = '<?php echo $GLOBALS['WEB_PATH'];?>/library/new_html2pdf/createPdf.php?op=p&file_name=../../library/new_html2pdf/return_order_result';
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