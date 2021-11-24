<?php
/*
File: stock_in_hand_result.php
Coded in PHP7
Purpose: Show In-hand Stock Report
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../config/config.php");
require_once(dirname('__FILE__')."/../../library/classes/functions.php");

if($_POST['generateRpt']){

	function cal_discount($amt,$dis)
	{
		$total = 0;
		if(strstr($dis, '%'))
		{
			$disc = str_replace('%','',$dis);
			$total = ($amt*$disc)/100;
		}
		else if(strstr($dis, '$') || $dis>0)
		{
			$total = str_replace('$','',$dis);
		}
		return $total;
	}

	$authProviderNameArr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$opInitial = $authProviderNameArr[1][0];
	$opInitial .= $authProviderNameArr[0][0];
	$opInitial = strtoupper($opInitial);
	
	if(empty($_POST['upc_code'])==false){
		$upc_code=str_replace(' ','',$_POST['upc_code']);
		$upc_code="'".str_replace(",", "','", $upc_code)."'";
	}
	
	//MASTER TABLES
	if($_POST['groupBy']!='manufac'){
		$manu_detail_qry = "select id, manufacturer_name from in_manufacturer_details";
		$manu_detail_rs = imw_query($manu_detail_qry);
		while($manu_detail_res=imw_fetch_array($manu_detail_rs)){
			$arrManufac[$manu_detail_res['id']]=$manu_detail_res['manufacturer_name'];
		}
	}
	//TYPES
	if($_POST['groupBy']!='type'){
	   $typeRs = imw_query("select * from in_module_type");
	   while($typeRes=imw_fetch_array($typeRs)){
		   $arrTypes[$typeRes['id']]=$typeRes['module_type_name'];
	   }
	}
	//VENDORS
	$vendorRs = imw_query("select * from in_vendor_details");
	while($vendorRes=imw_fetch_array($vendorRs)){
		$arrVendors[$vendorRes['id']]=$vendorRes['vendor_name'];
	}
	//BRANDS
	$brandRs = imw_query("select * from in_frame_sources");
    while($brandRes=imw_fetch_array($brandRs)){
		$arrBrands[$brandRes['id']]=$brandRes['frame_source'];
  	}
	//Facility
   $facRs = imw_query("select * from in_location");
   while($facRes=imw_fetch_array($facRs)){
	   $arrfacility[$facRes['id']]=$facRes['loc_name'];
   }
	//OPERATORS
/*	if($_POST['groupBy']!='operator'){
	   $usersRs = imw_query("select fname,lname from users");
	   while($usersRes=imw_fetch_array($usersRs)){
		   if($usersRes['lname']!='' || $usersRes['fname']!=''){
				$arrUsers[$usersRes['id']]=$usersRes['lname'].', '.$usersRes['fname']; 
		   }
	   }
	}*/
	
	$dateFrom=saveDateFormat($_POST['date_from']);
	$dateTo=saveDateFormat($_POST['date_to']);
	
	$groupByTitle='Manufacturer';
	$colTitle='Category';
	$colData='module_type_name';
	$orderBy=" in_manufacturer_details.manufacturer_name";
	
	if($_POST['groupBy']=='type'){
		$groupByTitle='Category'; 
		$colTitle='Manufacturer';
		$colData='manufacturer_name';
		$orderBy=" in_module_type.module_type_name";
	}
	
	$mainQry="Select in_item.id, in_item.name, in_item.upc_code, in_item.module_type_id, in_item.retail_price, 
	in_item.manufacturer_id, in_item.vendor_id, in_item.brand_id, DATE_FORMAT(in_item.entered_date, '%m-%d-%Y') as 'entered_date',
	in_item.purchase_price, in_item.wholesale_cost, retail_price, discount, in_manufacturer_details.manufacturer_name, in_module_type.module_type_name,
	SUM(in_item_loc_total.stock) as 'qty_on_hand', in_item_loc_total.loc_id,
	in_item.color, in_frame_color.color_name, in_item.frame_style, in_frame_styles.style_name, in_item.style_other, in_item.a, in_item.b, in_item.ed
	FROM in_item JOIN in_item_loc_total ON in_item_loc_total.item_id = in_item.id
	LEFT JOIN in_manufacturer_details ON in_manufacturer_details.id = in_item.manufacturer_id 
	LEFT JOIN in_module_type ON in_module_type.id = in_item.module_type_id
	LEFT JOIN in_frame_color ON in_frame_color.id = in_item.color
	LEFT JOIN in_frame_styles ON in_frame_styles.id = in_item.frame_style
	WHERE in_item.del_status=0 and in_item.module_type_id>0 and in_item_loc_total.stock>0";
	if(empty($_POST['manufac'])==false)
	{
		$mainQry.=' AND manufacturer_id IN('.$_POST['manufac'].')';
	}
	if(empty($_POST['product_type'])==false){
		$mainQry.=' AND in_item.module_type_id IN('.$_POST['product_type'].')';
	}
	if(empty($_POST['vendor'])==false){
		$mainQry.=' AND in_item.vendor_id IN('.$_POST['vendor'].')';
	}
	if(empty($_POST['brand'])==false){
		$mainQry.=' AND in_item.brand_id IN('.$_POST['brand'].')';
	}
	if(empty($_POST['facility'])==false){
		$mainQry.=' AND in_item_loc_total.loc_id IN('.$_POST['facility'].')';
	}
	if(empty($upc_code)==false){
		$mainQry.=' AND in_item.upc_code IN('.$upc_code.')';
	}
	$mainQry.=' GROUP BY in_item_loc_total.item_id ORDER BY in_item.entered_date, '.$orderBy;
	
	$mainRs=imw_query($mainQry);
	$mainNumRs=imw_num_rows($mainRs);
	while($mainRes=imw_fetch_array($mainRs)){
		$pid=$mainRes['id'];
		$groupBy=$mainRes['manufacturer_id'];
		$groupByName=$mainRes['manufacturer_name'];		

		if($_POST['groupBy']=='type'){		
			$groupBy=$mainRes['module_type_id'];
			$groupByName=$mainRes['module_type_name'];
		}

		$arrMainGroupBy[$groupBy] = $groupByName;
		$arrMainDetail[$groupBy][$pid] = $mainRes;

		$showManufac=$mainRes['manufacturer_name'];
		$showType=$mainRes['module_type_name'];
		$showVendor=$arrVendors[$mainRes['vendor_id']];
		$showBrand=$arrBrands[$mainRes['brand_id']];
	}
	//echo '<pre>'; print_r($arrMainDetail);
	
	// MAKE HTML
	if(count($arrMainDetail)>0){
		
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
		</style>
		';
		
		$grandTotal=0;
		$totalretailprice=0;
		foreach($arrMainGroupBy as $groupBY => $grpName)	
		{
			if($grpName=="")
			{
				if($_POST['groupBy']=='type'){
					$grpName = "No Category";
				}
				else
				{
					$grpName = "No Manufacturer";
				}
			}
			$subTotQty=$subTotPurchase=$subTotWSPrice=$subTotRetail=$subTotDisc=0;
			$html.='<tr>
					<td class="reportTitle" colspan="9">'.$groupByTitle.' : '.$grpName.'</td>
				</tr>';
			$htmlPDF.='<tr>
					<td class="reportTitle" colspan="9">'.$groupByTitle.' : '.$grpName.'</td></tr>';
	
			$detailData=array_values($arrMainDetail[$groupBY]);
	
			for($i=0; $i<sizeof($detailData); $i++){
				$discount=$discountAmt=0;
				$subTotQty+=$detailData[$i]['qty_on_hand'];
				$subTotPurchase+=$detailData[$i]['purchase_price'] * $detailData[$i]['qty_on_hand'];
				$subTotWSPrice+=$detailData[$i]['wholesale_cost'] * $detailData[$i]['qty_on_hand'];
				$subTotRetail+=$detailData[$i]['retail_price'] * $detailData[$i]['qty_on_hand'];

				$discount=$detailData[$i]['discount'];
				if(strstr($discount,'%')){
					$discountAmt=cal_discount($detailData[$i]['retail_price'],$discount);
					$discountAmt=currency_symbol(true).number_format($discountAmt, 2, '.', '').' '.'('.$discount.')';
				}else{
					if($discount>0){
						$discountAmt=currency_symbol(true).number_format($discount, 2, '.', '');
					}
				}
				
				$frame_details = '';
				$printValign = '';
				if( strtolower($detailData[$i]['module_type_name']) == 'frame' ){
					if($detailData[$i]['color_name'])
						$frame_details .= '<br />&nbsp;<strong>Color: </strong>'.$detailData[$i]['color_name'];
					if($detailData[$i]['style_name'])
						$frame_details .= '<br />&nbsp;<strong>Style: </strong>'.$detailData[$i]['style_name'];

					if($detailData[$i]['a']!='' || $detailData[$i]['b']!='' || $detailData[$i]['ed']!=''){

						$frame_details .= '<br />';
						if($detailData[$i]['a'])
							$frame_details .= '&nbsp;<strong>A: </strong>'.$detailData[$i]['a'];
						if($detailData[$i]['b'])
							$frame_details .= '&nbsp;<strong>B: </strong>'.$detailData[$i]['b'];
						if($detailData[$i]['ed'])
							$frame_details .= '&nbsp;<strong>ED: </strong>'.$detailData[$i]['ed'];
					}
					$printValign = "vertical-align:top;";
				}
				
				$html.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$detailData[$i][$colData].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$detailData[$i]['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$detailData[$i]['name'].' - '.$detailData[$i]['upc_code'].$frame_details.'</td>
				<td class="whiteBG rptText13 alignRight">'.$detailData[$i]['qty_on_hand'].'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.currency_symbol(true).number_format($detailData[$i]['purchase_price'], 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.currency_symbol(true).number_format($detailData[$i]['wholesale_cost'], 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.currency_symbol(true).number_format($detailData[$i]['retail_price'], 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.$discountAmt.'&nbsp;</td>
				</tr>';
				
				$htmlPDF.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft" style="width:95px;'.$printValign.'">&nbsp;'.$detailData[$i][$colData].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:85px;'.$printValign.'">&nbsp;'.$detailData[$i]['entered_date'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:160px;'.$printValign.'">&nbsp;'.$detailData[$i]['name'].' - '.$detailData[$i]['upc_code'].$frame_details.'</td>
				<td class="whiteBG rptText13 alignRight" style="'.$printValign.'">'.$detailData[$i]['qty_on_hand'].'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight" style="'.$printValign.'">'.currency_symbol(true).number_format($detailData[$i]['purchase_price'], 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight" style="'.$printValign.'">'.currency_symbol(true).number_format($detailData[$i]['wholesale_cost'], 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight" style="'.$printValign.'">'.currency_symbol(true).number_format($detailData[$i]['retail_price'], 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight" style="'.$printValign.'">'.$discountAmt.'&nbsp;</td>
				</tr>';			
				
			}
			//SUB TOTAL
			$grandTotal+=$subTotQty;
			$grandPurchase+=$subTotPurchase;
			$grandWSPrice+=$subTotWSPrice;
			$grandRetail+=$subTotRetail;
			$grandDisc+=$subTotDisc;
			
			$html.='<tr>
			<td class="whiteBG rptText13b alignRight" colspan="3">Sub Total : </td>
			<td class="whiteBG rptText13b alignRight">'.$subTotQty.'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($subTotPurchase, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($subTotWSPrice, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($subTotRetail, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">&nbsp;</td>
			</tr>';	
			$htmlPDF.='<tr>
			<td class="whiteBG rptText13b alignRight" colspan="3">Sub Total : </td>
			<td class="whiteBG rptText13b alignRight">'.$subTotQty.'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($subTotPurchase, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($subTotWSPrice, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($subTotRetail, 2, '.', '').'&nbsp;</td>
			<td class="whiteBG rptText13b alignRight">&nbsp;</td>
			</tr>';						
		}
		
		//GRAND TOTAL
		$html.='<tr>
		<td class="reportTitle rptText13b alignRight" colspan="3">Grand Total : </td>
		<td class="reportTitle rptText13b alignRight">'.$grandTotal.'&nbsp;</td>
		<td class="reportTitle rptText13b alignRight">'.currency_symbol(true).number_format($grandPurchase, 2, '.', '').'&nbsp;</td>
		<td class="reportTitle rptText13b alignRight">'.currency_symbol(true).number_format($grandWSPrice, 2, '.', '').'&nbsp;</td>
		<td class="reportTitle rptText13b alignRight">'.currency_symbol(true).number_format($grandRetail, 2, '.', '').'&nbsp;</td>
		<td class="reportTitle rptText13b alignRight">&nbsp;</td>
		</tr>';
		$htmlPDF.='<tr>
		<td class="reportTitle rptText13b alignRight" colspan="3">Grand Total : </td>
		<td class="reportTitle rptText13b alignRight">'.$grandTotal.'&nbsp;</td>
		<td class="reportTitle rptText13b alignRight">'.currency_symbol(true).number_format($grandPurchase, 2, '.', '').'&nbsp;</td>
		<td class="reportTitle rptText13b alignRight">'.currency_symbol(true).number_format($grandWSPrice, 2, '.', '').'&nbsp;</td>
		<td class="reportTitle rptText13b alignRight">'.currency_symbol(true).number_format($grandRetail, 2, '.', '').'&nbsp;</td>
		<td class="reportTitle rptText13b alignRight">&nbsp;</td>
		</tr>';
		
		// FINAL TABLE
		if(empty($_POST['manufac'])==false){$arr_m=explode(',',$_POST['manufac']);}
		if(empty($_POST['product_type'])==false){$arr_t=explode(',',$_POST['product_type']);}
		if(empty($_POST['vendor'])==false){$arr_v=explode(',',$_POST['vendor']);}
		if(empty($_POST['brand'])==false){$arr_b=explode(',',$_POST['brand']);}
		if(empty($_POST['facility'])==false){$arr_f=explode(',',$_POST['facility']);}
		$selManufac='All';
		$selType='All';
		$selVendor='All';
		$selBrand='All';
		$selfacility='All';
		$selManufac=(count($arr_m)>1)? 'Multi' : ((count($arr_m)=='1')? $showManufac : $selManufac);
		$selType=(count($arr_t)>1)? 'Multi' : ((count($arr_t)=='1')? $showType : $selType);
		$selVendor=(count($arr_v)>1)? 'Multi' : ((count($arr_v)=='1')? $showVendor : $selVendor);
		$selBrand=(count($arr_b)>1)? 'Multi' : ((count($arr_b)=='1')? $showBrand : $selBrand);
		$selfacility=(count($arr_f)>1)? 'Multi' : ((count($arr_f)=='1')? ucfirst($arrfacility[$_POST['facility']]) : $selfacility);
	
		$reportHtml.='
<table width="100%" cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
		<tr style="height:20px;">
			<td style="text-align:left;" class="reportHeadBG" colspan="2">&nbsp;Valuation Report</td>
			<td style="text-align:left;" class="reportHeadBG" colspan="2">&nbsp;UPC Code: '.$_POST['upc_code'].'</td>
			<td style="text-align:left;" class="reportHeadBG">Created by '.$opInitial.' on '.date('m-d-Y H:i A').'&nbsp;</td>
		</tr>
		<tr style="height:20px;">
			<td class="reportHeadBG" style="width:220px;">&nbsp;Manufacturer : '.$selManufac.'</td>
			<td class="reportHeadBG" style="width:220px;">&nbsp;Type : '.$selType.'</td>
			<td class="reportHeadBG" style="width:210px;">&nbsp;Vendor : '.$selVendor.'</td>
			<td class="reportHeadBG" style="width:210px;">&nbsp;Brand : '.$selBrand.'</td>
			<td class="reportHeadBG" style="width:auto;">&nbsp;Facility : '.$selfacility.'</td>
		</tr>
	</table>';
	
		$reportHtmlPDF.='<page backtop="17mm" backbottom="5mm">
		<page_footer>
				<table style="width: 700px;">
					<tr>
						<td style="text-align: center;	width: 700px">Page [[page_cu]]/[[page_nb]]</td>
					</tr>	
				</table>
		</page_footer>
		
<page_header>

<table cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
		<tr>
			<td style="text-align:left;" class="reportHeadBG" colspan="4">&nbsp;Valuation Report</td>
			<td style="text-align:right;" class="reportHeadBG" colspan="4">Created by '.$opInitial.' on '.date('m-d-Y H:i A').'&nbsp;</td>
		</tr>
		<tr>
			<td class="reportHeadBG">&nbsp;Manufacturer:'.$selManufac.'</td>
			<td class="reportHeadBG">&nbsp;Type:'.$selType.'</td>
			<td class="reportHeadBG">&nbsp;Vendor:'.$selVendor.'</td>
			<td class="reportHeadBG" colspan="2">&nbsp;Brand:'.$selBrand.'</td>
			<td class="reportHeadBG" colspan="2">&nbsp;Facility:'.$selfacility.'</td>
			<td class="reportHeadBG" colspan="2">&nbsp;UPC:'.$_POST['upc_code'].'</td>
		</tr>
		<tr>
			<td class="reportHeadBG1" style="text-align:left; width:80px;">'.$colTitle.'</td>
			<td class="reportHeadBG1" style="text-align:left; width:80px;">Entered Date</td>
			<td class="reportHeadBG1" style="text-align:left; width:160px;">Item Name - UPC Code</td>
			<td class="reportHeadBG1" style="text-align:right; width:50px;">Qty On Hand</td>
			<td class="reportHeadBG1" style="text-align:right; width:80px;">Purchase Price</td>
			<td class="reportHeadBG1" style="text-align:right; width:80px;">WS Price</td>
			<td class="reportHeadBG1" style="text-align:right; width:80px;">Retail Price</td>
			<td class="reportHeadBG1" style="text-align:right; width:80px;">Discount</td>
		</tr>		
	</table></page_header>';
		
	$reportHtml.='
	<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
		<tr>
			<td class="reportHeadBG1" style="text-align:left; width:150px;">'.$colTitle.'</td>
			<td class="reportHeadBG1" style="text-align:left; width:100px;">Entered Date</td>
			<td class="reportHeadBG1" style="text-align:left; width:280px;">Item Name - UPC Code</td>
			<td class="reportHeadBG1" style="text-align:right; width:90px;">Qty On Hand</td>
			<td class="reportHeadBG1" style="text-align:right; width:120px;">Purchase Price</td>
			<td class="reportHeadBG1" style="text-align:right; width:120px;">WS Price</td>
			<td class="reportHeadBG1" style="text-align:right; width:120px;">Retail Price</td>
			<td class="reportHeadBG1" style="text-align:right; width:120px;">Discount</td>
		</tr>
		'.$html.'
	</table>';
	
	$reportHtmlPDF.='
	<table style="border:none; background:#ccc;"  cellpadding="1" cellspacing="1">
		<tr style="height:0px;">
			<td style="width:95px;"></td>
			<td style="width:85px;"></td>
			<td style="width:160px;"></td>
			<td style="width:50px;"></td>
			<td style="width:80px;"></td>
			<td style="width:80px;"></td>
			<td style="width:80px;"></td>
			<td style="width:80px;"></td>
		</tr>
		'.$htmlPDF.'
	</table></page>';
	
	}
  $pdfText = $css.$reportHtmlPDF;
  
  file_put_contents('../../library/new_html2pdf/report_stock_in_hand.html',$pdfText);
  
}
?>
<html>
<head>
<title>Optical</title>
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
 	echo $reportHtml;
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

<form name="stockFormResult" action="stock_in_hand_result.php" method="post">
<input type="hidden" name="manufac" id="manufac" value="" />
<input type="hidden" name="product_type" id="product_type" value="" />
<input type="hidden" name="vendor" id="vendor" value="" />
<input type="hidden" name="brand" id="brand" value="" />
<input type="hidden" name="facility" id="facility" value="" />
<input type="hidden" name="upc_code" id="upc_code" value="" />
<input type="hidden" name="groupBy" id="groupBy" value="" />
<input type="hidden" name="generateRpt" id="generateRpt" value="yes" />
</form>

<?php if(isset($_REQUEST['print'])) { ?>

<script>
var url = '<?php echo $GLOBALS['WEB_PATH'];?>/library/new_html2pdf/createPdf.php?op=p&file_name=/../../library/new_html2pdf/report_stock_in_hand';
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