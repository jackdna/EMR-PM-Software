<?php
/*
File: general_inventory_control_result.php
Coded in PHP7
Purpose: Inventory Control Report
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../config/config.php");
require_once(dirname('__FILE__')."/../../library/classes/functions.php");

if($_POST['generateRpt']){
	
	$authProviderNameArr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$opInitial = $authProviderNameArr[1][0];
	$opInitial .= $authProviderNameArr[0][0];
	$opInitial = strtoupper($opInitial);
	
	//MASTER TABLES
		$manu_detail_qry = "select id, manufacturer_name from in_manufacturer_details";
		$manu_detail_rs = imw_query($manu_detail_qry);
		while($manu_detail_res=imw_fetch_array($manu_detail_rs)){
			$arrManufac[$manu_detail_res['id']]=$manu_detail_res['manufacturer_name'];
		}
	//TYPES
	   $typeRs = imw_query("select * from in_module_type");
	   while($typeRes=imw_fetch_array($typeRs)){
		   $arrTypes[$typeRes['id']]=$typeRes['module_type_name'];
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
		
	$dateFrom=saveDateFormat($_POST['date_from']);
	$dateTo=saveDateFormat($_POST['date_to']);
	
	//$itemQry="select module_type_id, sum(qty_on_hand) as qty, ROUND(sum(retail_price), 2) as retailprice from in_item where module_type_id>0";
	
	$itemQry="select in_item.module_type_id, sum(loc_tot.stock) as qty, ROUND(sum(in_item.retail_price*loc_tot.stock), 2) as retailprice 
	from in_item_loc_total as loc_tot left join in_item on in_item.id=loc_tot.item_id where in_item.module_type_id>0";
	
	//$orderQry="select id, module_type_id, upc_code, item_name, (qty)+(qty_right) as qty, ROUND(price, 2) as price from in_order_details where del_status='0' and (qty>0 or qty_right>0) and order_status='dispensed' and modified_date BETWEEN '".$dateFrom."' AND '".$dateTo."'";
	
	$orderQry="select ord_det.module_type_id, ord_det.id, ord_det.upc_code, ord_det.item_name, ord_sts.order_qty, ord_det.price,
	in_frame_color.color_name, ord_det.color_other, in_frame_styles.style_name, ord_det.style_other, ord_det.a,
	ord_det.b, ord_det.ed
	from in_order_detail_status ord_sts 
	join in_order_details as ord_det on ord_det.id=ord_sts.order_detail_id
	LEFT JOIN in_frame_color ON in_frame_color.id = ord_det.color_id
	LEFT JOIN in_frame_styles ON in_frame_styles.id = ord_det.style_id
	where ord_det.del_status='0' and ord_sts.order_status='dispensed' 
	and (ord_sts.order_date  BETWEEN '".$dateFrom."' AND '".$dateTo."')";
	
	if(empty($_POST['manufac'])==false){
		$mainQry=' AND in_item.manufacturer_id IN('.$_POST['manufac'].')';
		$ordQry=' AND ord_det.manufacturer_id IN('.$_POST['manufac'].')';
	}
	if(empty($_POST['product_type'])==false){
		$mainQry.=' AND in_item.module_type_id IN('.$_POST['product_type'].')';
		$ordQry.=' AND ord_det.module_type_id IN('.$_POST['product_type'].')';
	}
	if(empty($_POST['vendor'])==false){
		$mainQry.=' AND in_item.vendor_id IN('.$_POST['vendor'].')';
		$ordQry.=' AND ord_det.vendor_id IN('.$_POST['vendor'].')';
	}
	if(empty($_POST['brand'])==false){
		$mainQry.=' AND in_item.brand_id IN('.$_POST['brand'].')';
		$ordQry.=' AND ord_det.brand_id IN('.$_POST['brand'].')';
	}
	if(empty($_POST['facility'])==false){
		$mainQry.=' AND loc_tot.loc_id IN('.$_POST['facility'].')';
		$ordQry.=' AND ord_det.loc_id IN('.$_POST['facility'].')';
	}
	$other_item_qry.=' group by in_item.module_type_id order by in_item.entered_date';
	$other_order_qry.=' order by ord_sts.order_date, ord_det.module_type_id';

	$main_itemqry = $itemQry.$mainQry.$other_item_qry;
	$main_orderqry = $orderQry.$ordQry.$other_order_qry;
		
	$itemRs=imw_query($main_itemqry);
	$itemNumRs=imw_num_rows($itemRs);
	while($itemRes=imw_fetch_array($itemRs)){
		$arrItemDetail[] = $itemRes;
	}
	
	$orderRs=imw_query($main_orderqry) or die(imw_error());
	$orderNumRs=imw_num_rows($orderRs);
	while($orderRes=imw_fetch_array($orderRs)){
		$pid=$orderRes['id'];
		$groupBy=$orderRes['module_type_id'];
		$groupByName=$arrTypes[$orderRes['module_type_id']];
		$arrMainGroupBy[$groupBy] = $groupByName;
		$arrOrderDetail[$groupBy][$pid] = $orderRes;
	}
	//echo '<pre>'; print_r($arrMainGroupBy);
	
	// MAKE HTML
			
	//if(count($arrItemDetail)>0 || count($arrOrderDetail)>0){
		
		$css = '<style type="text/css">
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
		
		$grandTotal=0;
		$totalAdded=0;
		$totalretailprice=0;
		if(count($arrItemDetail)>0){
			for($i=0; $i<sizeof($arrItemDetail); $i++){
				if($arrItemDetail[$i]['qty']!=0)
				{
					$totalitemAdded+=$arrItemDetail[$i]['qty'];
					$totalpriceAdded+=$arrItemDetail[$i]['retailprice'];
					$html.='<tr style="height:20px;">
					<td class="whiteBG rptText13 alignLeft">&nbsp;'.ucfirst($arrTypes[$arrItemDetail[$i]['module_type_id']]).'</td>
					<td class="whiteBG rptText13 alignCenter">&nbsp;'.$arrItemDetail[$i]['qty'].'</td>
					<td class="whiteBG rptText13 alignRight">&nbsp;'.currency_symbol(true).$arrItemDetail[$i]['retailprice'].'</td>
					</tr>';
		
					$htmlPDF.='<tr style="height:20px;">
					<td class="whiteBG rptText13 alignLeft" style="width:300px">&nbsp;'.ucfirst($arrTypes[$arrItemDetail[$i]['module_type_id']]).'</td>
					<td class="whiteBG rptText13 alignCenter" style="width:220px">&nbsp;'.$arrItemDetail[$i]['qty'].'</td>
					<td class="whiteBG rptText13 alignRight" style="width:225px">&nbsp;'.currency_symbol(true).$arrItemDetail[$i]['retailprice'].'</td>
					</tr>';	
					
					// copy code start
					$htmlPDF.='<tr style="height:20px;">
					<td class="whiteBG rptText13 alignLeft" style="width:300px">&nbsp;'.ucfirst($arrTypes[$arrItemDetail[$i]['module_type_id']]).'</td>
					<td class="whiteBG rptText13 alignCenter" style="width:220px">&nbsp;'.$arrItemDetail[$i]['qty'].'</td>
					<td class="whiteBG rptText13 alignRight" style="width:225px">&nbsp;'.currency_symbol(true).$arrItemDetail[$i]['retailprice'].'</td>
					</tr>';				
					// copy code end
				}
			}
		}
		else
		{
			$html.='<tr style="height:20px;">
			<td colspan="3" class="whiteBG rptText13 alignCenter">&nbsp;No Record Found.</td>
			</tr>';
		}
		//GRAND TOTAL
		$html.='
		<tr>
		<td colspan="3" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td>
		</tr>
		<tr>
		<td class="reportTitle rptText13b alignRight">Total Stock : </td>
		<td class="reportTitle rptText13b alignCenter">'.$totalitemAdded.'</td>
		<td class="reportTitle rptText13b alignRight">'.currency_symbol(true).$totalpriceAdded.'</td>
		</tr>';
		
		$htmlPDF.='
		<tr>
		<td colspan="3" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td>
		</tr>
		<tr>
		<td class="reportTitle rptText13b alignRight">Total Stock : </td>
		<td class="reportTitle rptText13b alignCenter">'.$totalitemAdded.'</td>
		<td class="reportTitle rptText13b alignRight">'.currency_symbol(true).$totalpriceAdded.'</td>
		</tr>';
		
		if(count($arrOrderDetail)>0){
			$grandqtyTotal=0;
			$grandpriceTotal=0;
			foreach($arrMainGroupBy as $groupBY => $grpName)	
			{
				$totalorderAdded=0;
				$totalorderprice=0;
				$orderhtml.='<tr>
					<td class="reportTitle" colspan="4">Product Type : '.ucfirst($grpName).'</td></tr>';
				$orderhtmlPDF.='<tr>
					<td class="reportTitle" colspan="4">Product Type : '.ucfirst($grpName).'</td></tr>';
					
				$detailData=array_values($arrOrderDetail[$groupBY]);
				
				for($j=0; $j<sizeof($detailData); $j++){
					$totAmt=0;
					$totAmt=$detailData[$j]['price'] * $detailData[$j]['order_qty'];
					
					$totalorderAdded+=$detailData[$j]['order_qty'];
					$totalorderprice+=$totAmt;
					
					$frame_details = '';
					$printValign = '';
					if( strtolower($arrTypes[$detailData[$j]['module_type_id']]) == 'frame' ){
						
						if($detailData[$j]['color_other']!='')
							$frame_details .= '<br />&nbsp;<strong>Color: </strong>'.$detailData[$j]['color_other'];
						elseif($detailData[$j]['color_name'])
							$frame_details .= '<br />&nbsp;<strong>Color: </strong>'.$detailData[$j]['color_name'];
						
						if($detailData[$j]['style_other']!='')
							$frame_details .= '<br />&nbsp;<strong>Style: </strong>'.$detailData[$j]['style_other'];
						elseif($detailData[$j]['style_name'])
							$frame_details .= '<br />&nbsp;<strong>Style: </strong>'.$detailData[$j]['style_name'];

						if($detailData[$j]['a']!='' || $detailData[$j]['b']!='' || $detailData[$j]['ed']!=''){	
							$frame_details .= '<br />';
							if($detailData[$j]['a'])
								$frame_details .= '&nbsp;<strong>A: </strong>'.$detailData[$j]['a'];
							if($detailData[$j]['b'])
								$frame_details .= '&nbsp;<strong>B: </strong>'.$detailData[$j]['b'];
							if($detailData[$j]['ed'])
								$frame_details .= '&nbsp;<strong>ED: </strong>'.$detailData[$j]['ed'];
						}
						$printValign = "vertical-align:top;";
					}
					
					$orderhtml.='<tr style="height:20px;">
					<td class="whiteBG rptText13 alignLeft">&nbsp;'.$detailData[$j]['upc_code'].'</td>
					<td class="whiteBG rptText13 alignLeft">&nbsp;'.$detailData[$j]['item_name'].$frame_details.'</td>
					<td class="whiteBG rptText13 alignCenter">&nbsp;'.$detailData[$j]['order_qty'].'</td>
					<td class="whiteBG rptText13 alignRight">&nbsp;'.currency_symbol(true).$totAmt.'</td>
					</tr>';
		
					$orderhtmlPDF.='<tr style="height:20px;">
					<td class="whiteBG rptText13 alignLeft" style="width:200px">&nbsp;'.$detailData[$j]['upc_code'].'</td>
					<td class="whiteBG rptText13 alignLeft" style="width:200px">&nbsp;'.$detailData[$j]['item_name'].$frame_details.'</td>
					<td class="whiteBG rptText13 alignCenter" style="width:170px">&nbsp;'.$detailData[$j]['order_qty'].'</td>
					<td class="whiteBG rptText13 alignRight" style="width:170px">&nbsp;'.currency_symbol(true).$totAmt.'</td>
					</tr>';	
					
					// copy code start
					$orderhtmlPDF.='<tr style="height:20px;">
					<td class="whiteBG rptText13 alignLeft">&nbsp;'.$detailData[$j]['upc_code'].'</td>
					<td class="whiteBG rptText13 alignLeft">&nbsp;'.$detailData[$j]['item_name'].'</td>
					<td class="whiteBG rptText13 alignCenter">&nbsp;'.$detailData[$j]['order_qty'].'</td>
					<td class="whiteBG rptText13 alignRight">&nbsp;'.currency_symbol(true).$totAmt.'</td>
					</tr>';				
					// copy code end
				}
				$grandqtyTotal+=$totalorderAdded;
				$grandpriceTotal+=$totalorderprice;
				$orderhtml.='<tr>
				<td class="whiteBG rptText13b alignRight" colspan="2">Sub Total : </td>
				<td class="whiteBG rptText13b alignCenter">&nbsp;'.$totalorderAdded.'</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totalorderprice, 2, '.', '').'</td>
				</tr>';	
				$orderhtmlPDF.='<tr>
				<td class="whiteBG rptText13b alignRight" colspan="2">Sub Total : </td>
				<td class="whiteBG rptText13b alignCenter">&nbsp;'.$totalorderAdded.'</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($totalorderprice, 2, '.', '').'</td>
				</tr>';
			}
		}
		else
		{
			$orderhtml.='<tr style="height:20px;">
			<td colspan="4" class="whiteBG rptText13 alignCenter">&nbsp;No Record Found.</td>
			</tr>';
		}	
		
		//GRAND TOTAL
		$orderhtml.='
		<tr>
		<td colspan="4" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td>
		</tr>
		<tr>
		<td colspan="2" class="reportTitle rptText13b alignRight">Total Sold: </td>
		<td class="reportTitle rptText13b alignCenter">'.$grandqtyTotal.'</td>
		<td class="reportTitle rptText13b alignRight">'.currency_symbol(true).number_format($grandpriceTotal, 2, '.', '').'</td>
		</tr>';
		
		$orderhtmlPDF.='
		<tr>
		<td colspan="4" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td>
		</tr>
		<tr>
		<td colspan="2" class="reportTitle rptText13b alignRight">Total Sold: </td>
		<td class="reportTitle rptText13b alignCenter">'.$grandqtyTotal.'</td>
		<td class="reportTitle rptText13b alignRight">'.currency_symbol(true).number_format($grandpriceTotal, 2, '.', '').'</td>
		</tr>';
			
		// FINAL TABLE
		if(empty($_POST['manufac'])==false){$arr_m=explode(',',$_POST['manufac']);}
		if(empty($_POST['product_type'])==false){$arr_t=explode(',',$_POST['product_type']);}
		if(empty($_POST['vendor'])==false){$arr_v=explode(',',$_POST['vendor']);}
		if(empty($_POST['brand'])==false){$arr_b=explode(',',$_POST['brand']);}
		if(empty($_POST['operators'])==false){$arr_o=explode(',',$_POST['operators']);}
		$selManufac='All';
		$selType='All';
		$selVendor='All';
		$selBrand='All';
		$selOperator='All';
		$selManufac=(count($arr_m)>1)? 'Multi' : ((count($arr_m)=='1')? $showManufac : $selManufac);
		$selType=(count($arr_t)>1)? 'Multi' : ((count($arr_t)=='1')? $showType : $selType);
		$selVendor=(count($arr_v)>1)? 'Multi' : ((count($arr_v)=='1')? $showVendor : $selVendor);
		$selBrand=(count($arr_b)>1)? 'Multi' : ((count($arr_b)=='1')? $showBrand : $selBrand);
		$selOperator=(count($arr_o)>1)? 'Multi' : ((count($arr_o)=='1')? $showOperator : $selOperator);
	
		$reportHtml.='<table width="100%" cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
		<tr style="height:20px;">
			<td style="text-align:left;" class="reportHeadBG" width="220px">&nbsp;Stock In Hand Report</td>
			<td style="text-align:right;" class="reportHeadBG" width="250px">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i A').'&nbsp;</td>
		</tr>
	</table>';

		$reportHtmlPDF.='
		<page backtop="10mm" backbottom="5mm">
		<page_footer>
				<table style="width: 750px;">
					<tr>
						<td style="text-align: center;	width: 750px">Page [[page_cu]]/[[page_nb]]</td>
					</tr>	
				</table>
		</page_footer>
		<page_header>
		<table width="750" cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
		<tr style="height:20px;">
			<td style="text-align:left;" class="reportHeadBG" width="375">&nbsp;Stock In Hand Report</td>
			<td style="text-align:right;" class="reportHeadBG" width="375">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i A').'&nbsp;</td>
		</tr>
		</table>
		<table width="750" cellpadding="1" cellspacing="1" border="0" style="background:#E3E3E3;">		
		<tr>
			<td class="reportHeadBG1" style="text-align:left; width:300px;">Product Type</td>
			<td class="reportHeadBG1" style="text-align:center; width:220px;">Qty In Hand</td>
			<td class="reportHeadBG1" style="text-align:right; width:225px;">Retail Price</td>
		</tr>
		</table>
		</page_header>';

	$reportHtml.='
	<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
		<tr>
			<td class="reportHeadBG1" style="text-align:left; width:400px;">Product Type</td>
			<td class="reportHeadBG1" style="text-align:center; width:200px;">Qty In Hand</td>
			<td class="reportHeadBG1" style="text-align:right; width:200px;">Retail Price</td>
		</tr>
		'.$html.'
	</table>';
	
	
	$reportHtml.='<div class="reportHeadBG" style="padding:4px; margin-top:5px;">Stock Sold (Dispensed Orders)</div>
	
	<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
		<tr>
			<td class="reportHeadBG1" style="text-align:left; width:200px;">Upc Code</td>
			<td class="reportHeadBG1" style="text-align:left; width:200px;">Item Name</td>
			<td class="reportHeadBG1" style="text-align:center; width:200px;">Qty Sold</td>
			<td class="reportHeadBG1" style="text-align:right; width:200px;">Price</td>
		</tr>
		'.$orderhtml.'
	</table>';
	
	$reportHtmlPDF.='
	<table style="width:750px; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
		<tr>
			<td style="width:300px;"></td>
			<td style="width:220px;"></td>
			<td style="width:225px;"></td>
		</tr>
		'.$html.'
	</table>';
	
	$reportHtmlPDF.='
	<div class="reportHeadBG" style="padding:4px; width:753px; margin-top:5px;">Stock Sold (Dispensed Orders)</div>
	<table style="width:750px; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
		<tr>
			<td class="reportHeadBG1" style="text-align:left; width:200px;">Upc Code</td>
			<td class="reportHeadBG1" style="text-align:left; width:200px;">Item Name</td>
			<td class="reportHeadBG1" style="text-align:center; width:170px;">Qty Sold</td>
			<td class="reportHeadBG1" style="text-align:right; width:170px;">Price</td>
		</tr>
		'.$orderhtml.'
	</table></page>';	

	}
	
  $pdfText = $css.$reportHtmlPDF;

  if(empty($reportHtmlPDF)===false){
	  file_put_contents('../../library/new_html2pdf/general_inventory_control_result.html',$pdfText);	
  }
	
	
//}
?>
<html>
<head>
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
echo $reportHtml;
/*if(count($arrItemDetail)>0 || count($arrOrderDetail)>0){
 echo $reportHtml;
}else{
	if($_REQUEST['print'])
	{
		$d="display:none;";
	}
	echo '<br><div style="text-align:center; '.$d.'"><strong>No Record Found.</strong></div>';
}*/
 ?>
<form name="stockFormResult" action="general_inventory_control_result.php" method="post">
<input type="hidden" name="manufac" id="manufac" value="" />
<input type="hidden" name="product_type" id="product_type" value="" />
<input type="hidden" name="vendor" id="vendor" value="" />
<input type="hidden" name="brand" id="brand" value="" />
<input type="hidden" name="operators" id="operators" value="" />
<input type="hidden" name="groupBy" id="groupBy" value="" />
<input type="hidden" name="facility" id="facility" value="" />
<input type="hidden" name="date_from" id="date_from" value="" />
<input type="hidden" name="date_to" id="date_to" value="" />
<input type="hidden" name="generateRpt" id="generateRpt" value="yes" />
</form>


<?php if(isset($_REQUEST['print'])) { ?>

<script>
var url = '<?php echo $GLOBALS['WEB_PATH'];?>/library/new_html2pdf/createPdf.php?op=p&file_name=../../library/new_html2pdf/general_inventory_control_result';
window.location.href = url;
</script>

<?php } ?>

<script type="text/javascript">
$(document).ready(function(){
	var numr = '<?php echo $itemNumRs; ?>';		

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
