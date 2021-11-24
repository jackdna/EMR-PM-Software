<?php
/*
File: stock_ledger_result.php
Coded in PHP7
Purpose: Show Stock Report
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../config/config.php");
require_once(dirname('__FILE__')."/../../library/classes/functions.php");
require_once(dirname('__FILE__')."/../../library/classes/common_functions.php");

if($_POST['generateRpt']){
	$authProviderNameArr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$opInitial = $authProviderNameArr[1][0];
	$opInitial .= $authProviderNameArr[0][0];
	$opInitial = strtoupper($opInitial);
	
	//MASTER TABLES
	if($_POST['groupBy']!='manufac'){
		$manu_detail_qry = "select id, manufacturer_name from in_manufacturer_details";
		$manu_detail_rs = imw_query($manu_detail_qry);
		while($manu_detail_res=imw_fetch_array($manu_detail_rs)){
			$arrManufac[$manu_detail_res['id']]=$manu_detail_res['manufacturer_name'];
		}
	}
	//REASON
/*	$rea_qry = "select id, reason_name from in_reason";
	$rea_rs = imw_query($rea_qry);
	while($rea_res=imw_fetch_array($rea_rs)){
		$arrReason[$rea_res['id']]=$rea_res['reason_name'];
	}*/

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
	   //$arrfacility[$facRes['fac_prac_code']]=$facRes['name'];
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
	$colTitle2='Operator';
	$colData2='operName';

	$orderBy=" in_manufacturer_details.manufacturer_name,";
	
	if($_POST['groupBy']=='type'){
		$groupByTitle='Category'; 
		$colTitle='Manufacturer';
		$colData='manufacturer_name';
		$orderBy=" in_module_type.module_type_name,";
	}
	if($_POST['groupBy']=='operator'){
		$groupByTitle='Operator';
		$colTitle='Manufacturer';
		$colData='manufacturer_name';
		$colTitle2='Category';
		$colData2='module_type_name';
		$orderBy=" users.lname, users.fname,";
	}
	$orderBy.="stockDet.entered_date DESC";
	
	$mainQry="Select stockDet.source, stockDet.item_id, stockDet.stock, stockDet.trans_type, stockDet.operator_id, DATE_FORMAT(stockDet.entered_date, '%m-%d-%Y') as 'enteredDate',
	stockDet.order_id, in_item.del_status,
	in_item.vendor_id, in_item.brand_id, in_item.name, in_item.upc_code, in_item.manufacturer_id, in_item.module_type_id, in_manufacturer_details.manufacturer_name, in_module_type.module_type_name,
	users.fname, users.lname, stockDet.loc_id
	FROM in_stock_detail stockDet
	LEFT JOIN in_item ON in_item.id = stockDet.item_id 
	LEFT JOIN in_manufacturer_details ON in_manufacturer_details.id = in_item.manufacturer_id 
 	LEFT JOIN in_module_type ON in_module_type.id = in_item.module_type_id 
	LEFT JOIN users ON users.id = stockDet.operator_id
	WHERE (stockDet.entered_date BETWEEN '".$dateFrom."' AND '".$dateTo."')";
	
	if(empty($_POST['manufac'])==false){
		$mainQry.=' AND in_item.manufacturer_id IN('.$_POST['manufac'].')';
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
	if(empty($_POST['operators'])==false){
		$mainQry.=' AND stockDet.operator_id IN('.$_POST['operators'].')';
	}
	if(empty($_POST['facility'])==false){
		$mainQry.=' AND stockDet.loc_id IN('.$_POST['facility'].')';
	}
	if(empty($_POST['t_type'])==false && $_POST['t_type']!='all'){
		$mainQry.=' AND stockDet.trans_type = "'.$_POST['t_type'].'"';
	}
	if(empty($_POST['source'])==false){
		$s_arr=explode(',',$_POST['source']);
		$s_str=implode("','", $s_arr);
		$mainQry.=" AND stockDet.source IN ('$s_str')";
	}
	$mainQry.=' ORDER BY '.$orderBy;
	
	$mainRs=imw_query($mainQry);
	$mainNumRs=imw_num_rows($mainRs);
	while($mainRes=imw_fetch_array($mainRs)){
		$mainRes['operName']='';
		$pid=$mainRes['id'];
		$groupBy=$mainRes['manufacturer_id'];
		$groupByName=$mainRes['manufacturer_name'];		
		if($mainRes['lname']!='' || $mainRes['fname']!=''){
			$mainRes['operName']=$mainRes['lname'].', '.$mainRes['fname'];
		}
		
		if($_POST['groupBy']=='type'){		
			$groupBy=$mainRes['module_type_id'];
			$groupByName=$mainRes['module_type_name'];
		}elseif($_POST['groupBy']=='operator'){		
			$groupBy=$mainRes['operator_id'];
			$groupByName=$mainRes['operName'];
		}

		$arrMainGroupBy[$groupBy] = $groupByName;
		$arrMainDetail[$groupBy][] = $mainRes;
	
		$showManufac=$mainRes['manufacturer_name'];
		$showType=$mainRes['module_type_name'];
		$showVendor=$arrVendors[$mainRes['vendor_id']];
		$showBrand=$arrBrands[$mainRes['brand_id']];
		$showOperator=$mainRes['operName'];
	}
	//echo '<pre>'; print_r($arrMainDetail);
	
	// MAKE HTML
	if(count($arrMainDetail)>0){
		
		
$css = '
		<style type="text/css">
		.deleted{text-decoration: line-through; color: #E40A0D}
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
		$totalAdded=0;
		$totalDeduct=0;
		foreach($arrMainGroupBy as $groupBY => $grpName)
		{
			if($grpName=="")
			{
				if($_POST['groupBy']=='type'){
					$grpName = "No Category";
				}
				elseif($_POST['groupBy']=='operator'){
					$grpName = "No Operator";
				}
				else
				{
					$grpName = "No Manufacturer";
				}
			}
			$subTotQty=0;
			$html.='<tr>
					<td class="reportTitle" colspan="8">'.$groupByTitle.' : '.$grpName.'</td>
				</tr>';

			$htmlPDF.='<tr>
					<td class="reportTitle" colspan="8">'.$groupByTitle.' : '.$grpName.'</td>
				</tr>';
	
			$detailData=array_values($arrMainDetail[$groupBY]);
	
			for($i=0; $i<sizeof($detailData); $i++){
				$operName='';
				if($detailData[$i]['trans_type']=='minus'){
					$detailData[$i]['stock']='-'.$detailData[$i]['stock'];
					$totalDeduct+=$detailData[$i]['stock'];
				}else{
					$totalAdded+=$detailData[$i]['stock'];
				}
				$subTotQty+=$detailData[$i]['stock'];
				
				if($detailData[$i]['fname']!='' || $detailData[$i]['lname']){
					$operName=$detailData[$i]['lname'].', '.$detailData[$i]['lname'];
				}
				$delClass=($detailData[$i]['del_status']==1)?" deleted":"";
				$html.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft '.$delClass.'">&nbsp;'.$detailData[$i][$colData].'</td>
				<td class="whiteBG rptText13 alignLeft" '.$delClass.'>&nbsp;'.$detailData[$i][$colData2].'</td>
				<td class="whiteBG rptText13 alignCenter" '.$delClass.'>'.$detailData[$i]['order_id'].'</td>
				<td class="whiteBG rptText13 alignLeft" '.$delClass.'>&nbsp;'.$detailData[$i]['name'].' - '.$detailData[$i]['upc_code'].'</td>
				<td class="whiteBG rptText13 alignLeft" '.$delClass.'>&nbsp;'.$arrfacility[$detailData[$i]['loc_id']].'</td>
				<td class="whiteBG rptText13 alignCenter" '.$delClass.'>'.$detailData[$i]['stock'].'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft" '.$delClass.'>'. $detailData[$i]['source'].'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter" '.$delClass.'>&nbsp;'.$detailData[$i]['enteredDate'].'</td>
				</tr>';

				$htmlPDF.='<tr style="height:20px;">			
				<td class="whiteBG rptText13 alignLeft '.$delClass.'" style="width:100px;">&nbsp;'.$detailData[$i][$colData].'</td>
				<td class="whiteBG rptText13 alignLeft '.$delClass.'" style="width:100px;">&nbsp;'.$detailData[$i][$colData2].'</td>
				<td class="whiteBG rptText13 alignCenter '.$delClass.'" style="width:80px;">'.$detailData[$i]['order_id'].'</td>
				<td class="whiteBG rptText13 alignLeft '.$delClass.'" style="width:170px;">&nbsp;'.$detailData[$i]['name'].' - '.$detailData[$i]['upc_code'].'</td>
				<td class="whiteBG rptText13 alignLeft '.$delClass.'" style="width:80px;">&nbsp;'.$arrfacility[$detailData[$i]['loc_id']].'</td>
				<td class="whiteBG rptText13 alignRight '.$delClass.'" style="width:40px;">'.$detailData[$i]['stock'].'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight '.$delClass.'" style="width:70px;">'.$detailData[$i]['source'].'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter '.$delClass.'" style="width:80px;">&nbsp;'.$detailData[$i]['enteredDate'].'</td>
				</tr>';	
			}

			//SUB TOTAL
			$grandTotal+=$subTotQty;
			$html.='<tr>
			<td class="whiteBG rptText13b alignRight" colspan="5">Sub Total : </td>
			<td class="whiteBG rptText13b alignCenter">'.$subTotQty.'&nbsp;</td>
			<td class="whiteBG"></td>
			<td class="whiteBG rptText13b"></td>
			</tr>';

			$htmlPDF.='<tr>
			<td class="whiteBG rptText13b alignRight" colspan="5">Sub Total : </td>
			<td class="whiteBG rptText13b alignRight">'.$subTotQty.'&nbsp;</td>
			<td class="whiteBG rptText13b"></td>
			<td class="whiteBG rptText13b"></td>
			</tr>';			
			
		}
		//GRAND TOTAL
		$html.='
		<tr>
		<td colspan="8" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td>
		</tr>
		<tr style="height:25px">
		<td class="whiteBG rptText13 alignRight" colspan="5">Total Added (Qty) : </td>
		<td class="whiteBG rptText13b alignCenter">'.$totalAdded.'&nbsp;</td>
		<td class="whiteBG"></td>
		<td class="whiteBG"></td>
		</tr>
		<tr style="height:25px">
		<td class="whiteBG rptText13 alignRight" colspan="5">Total Deducted (Qty) : </td>
		<td class="whiteBG rptText13b alignCenter">'.$totalDeduct.'&nbsp;</td>
		<td class="whiteBG"></td>
		<td class="whiteBG"></td>
		</tr>
		<tr>
		<td class="reportTitle rptText13b alignRight" colspan="5">Grand Balance (Qty) : </td>
		<td class="reportTitle rptText13b alignCenter">'.$grandTotal.'&nbsp;</td>
		<td class="reportTitle rptText13b"></td>
		<td class="reportTitle rptText13b"></td>
		</tr>';
		
		$htmlPDF.='
		<tr>
		<td colspan="8" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td>
		</tr>
		<tr style="height:25px">
		<td class="whiteBG rptText13 alignRight" colspan="5">Total Added (Qty) : </td>
		<td class="whiteBG rptText13b alignRight">'.$totalAdded.'&nbsp;</td>
		<td class="whiteBG"></td>
		<td class="whiteBG"></td>
		</tr>
		<tr style="height:25px">
		<td class="whiteBG rptText13 alignRight" colspan="5">Total Deducted (Qty) : </td>
		<td class="whiteBG rptText13b alignRight">'.$totalDeduct.'&nbsp;</td>
		<td class="whiteBG"></td>
		<td class="whiteBG"></td>
		</tr>
		<tr>
		<td class="reportTitle rptText13b alignRight" colspan="5">Grand Balance (Qty) : </td>
		<td class="reportTitle rptText13b alignRight">'.$grandTotal.'&nbsp;</td>
		<td class="reportTitle rptText13b"></td>
		<td class="reportTitle rptText13b"></td>
		</tr>';		
		
		// FINAL TABLE
		if(empty($_POST['manufac'])==false){$arr_m=explode(',',$_POST['manufac']);}
		if(empty($_POST['product_type'])==false){$arr_t=explode(',',$_POST['product_type']);}
		if(empty($_POST['vendor'])==false){$arr_v=explode(',',$_POST['vendor']);}
		if(empty($_POST['brand'])==false){$arr_b=explode(',',$_POST['brand']);}
		if(empty($_POST['operators'])==false){$arr_o=explode(',',$_POST['operators']);}
		if(empty($_POST['facility'])==false){$arr_f=explode(',',$_POST['facility']);}
		$selManufac='All';
		$selType='All';
		$selVendor='All';
		$selBrand='All';
		$selOperator='All';
		$selfacility='All';
		$selManufac=(count($arr_m)>1)? 'Multi' : ((count($arr_m)=='1')? $showManufac : $selManufac);
		$selType=(count($arr_t)>1)? 'Multi' : ((count($arr_t)=='1')? $showType : $selType);
		$selVendor=(count($arr_v)>1)? 'Multi' : ((count($arr_v)=='1')? $showVendor : $selVendor);
		$selBrand=(count($arr_b)>1)? 'Multi' : ((count($arr_b)=='1')? $showBrand : $selBrand);
		$selOperator=(count($arr_o)>1)? 'Multi' : ((count($arr_o)=='1')? $showOperator : $selOperator);
		$selfacility=(count($arr_f)>1)? 'Multi' : ((count($arr_f)=='1')? ucfirst($arrfacility[$_POST['facility']]) : $selfacility);
	
		$reportHtml.='<table width="100%" cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
		<tr style="height:20px;">
			<td style="text-align:left;" class="reportHeadBG" width="220px">&nbsp;Stock In Hand Report</td>
			<td style="text-align:left;" class="reportHeadBG" width="auto">&nbsp;From : '.$_POST['date_from'].' To : '.$_POST['date_to'].'</td>
			<td style="text-align:left;" class="reportHeadBG" width="auto" colspan="2">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
		</tr>
		<tr style="height:20px;">
			<td class="reportHeadBG" width="245">&nbsp;Manufacturer : '.$selManufac.'</td>
			<td class="reportHeadBG" width="245">&nbsp;Type : '.$selType.'</td>
			<td class="reportHeadBG" width="245">&nbsp;Vendor : '.$selVendor.'</td>
		</tr>
		<tr style="height:20px;">
			<td class="reportHeadBG" width="245">&nbsp;Brand : '.$selBrand.'</td>
			<td class="reportHeadBG" width="245">&nbsp;Operator : '.$selOperator.'</td>
			<td class="reportHeadBG" width="245">&nbsp;Facility : '.$selfacility.'</td>
		</tr>

	</table>';

		$reportHtmlPDF.='
		<page backtop="21mm" backbottom="5mm">
		
		<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align: center;	width: 100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>	
				</table>
		</page_footer>
		<page_header>
		<table width="100%" cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
		<tr style="height:20px;">
			<td style="text-align:left;" class="reportHeadBG" width="245">&nbsp;Stock In Hand Report</td>
			<td style="text-align:left;" class="reportHeadBG" width="245">&nbsp;From : '.$_POST['date_from'].' To : '.$_POST['date_to'].'</td>
			<td style="text-align:left;" class="reportHeadBG" width="245" colspan="2">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
		</tr>
		<tr style="height:20px;">
			<td class="reportHeadBG" width="245">&nbsp;Manufacturer : '.$selManufac.'</td>
			<td class="reportHeadBG" width="245">&nbsp;Type : '.$selType.'</td>
			<td class="reportHeadBG" width="245">&nbsp;Vendor : '.$selVendor.'</td>
		</tr>
		<tr style="height:20px;">
			<td class="reportHeadBG" width="245">&nbsp;Brand : '.$selBrand.'</td>
			<td class="reportHeadBG" width="245">&nbsp;Operator : '.$selOperator.'</td>
			<td class="reportHeadBG" width="245">&nbsp;Facility : '.$selfacility.'</td>
		</tr>
		</table>
		<table width="100%" cellpadding="1" cellspacing="1" border="0" style="background:#E3E3E3;">		
		<tr>
			<td class="reportHeadBG1" style="text-align:left; width:100px;">'.$colTitle.'</td>
			<td class="reportHeadBG1" style="text-align:left; width:100px;">'.$colTitle2.'</td>
			<td class="reportHeadBG1" style="text-align:center; width:80px;">Order Id</td>
			<td class="reportHeadBG1" style="text-align:left; width:170px;">Item Name - UPC Code</td>
			<td class="reportHeadBG1" style="text-align:left; width:80px;">Facility</td>
			<td class="reportHeadBG1" style="text-align:right; width:40px;">Qty</td>
			<td class="reportHeadBG1" style="text-align:right; width:70px;">Source</td>
			<td class="reportHeadBG1" style="text-align:center; width:80px;">Date</td>
		</tr>
		</table>
		</page_header>';

	$reportHtml.='
	
	<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
		<tr>
			<td class="reportHeadBG1" style="text-align:left; width:150px;">'.$colTitle.'</td>
			<td class="reportHeadBG1" style="text-align:left; width:100px;">'.$colTitle2.'</td>
			<td class="reportHeadBG1" style="text-align:center; width:70px;">Order Id</td>
			<td class="reportHeadBG1" style="text-align:left; width:240px;">Product Name - UPC Code</td>
			<td class="reportHeadBG1" style="text-align:left; width:100px;">Facility</td>
			<td class="reportHeadBG1" style="text-align:center; width:40px;">Qty</td>
			<td class="reportHeadBG1" style="text-align:left; width:130px;">Source</td>
			<td class="reportHeadBG1" style="text-align:center; width:80px;">Date</td>
		</tr>
		'.$html.'
	</table>';
	
	$reportHtmlPDF.='
	<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
		'.$htmlPDF.'
	</table></page>';	
	}
	
  $pdfText = $css.$reportHtmlPDF;
  
  file_put_contents('../../library/new_html2pdf/report_stock_ledger.html',$pdfText);	
	
	
}
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
<style>.deleted{text-decoration: line-through; color: #E40A0D}</style>
</head>
<body>
<?php
if(count($arrMainDetail)>0){
 echo $reportHtml;
}else{
	if($_REQUEST['print'])
	{
		$d="display:none;";
	}
	echo '<br><div style="text-align:center; '.$d.'"><strong>No Record Found.</strong></div>';
}
 ?>
<form name="stockFormResult" action="stock_ledger_result.php" method="post">
<input type="hidden" name="manufac" id="manufac" value="" />
<input type="hidden" name="product_type" id="product_type" value="" />
<input type="hidden" name="vendor" id="vendor" value="" />
<input type="hidden" name="brand" id="brand" value="" />
<input type="hidden" name="operators" id="operators" value="" />
<input type="hidden" name="facility" id="facility" value="" />
<input type="hidden" name="source" id="source" value="" />
<input type="hidden" name="t_type" id="t_type" value="" />
<input type="hidden" name="groupBy" id="groupBy" value="" />
<input type="hidden" name="date_from" id="date_from" value="" />
<input type="hidden" name="date_to" id="date_to" value="" />
<input type="hidden" name="generateRpt" id="generateRpt" value="yes" />
</form>


<?php if(isset($_REQUEST['print'])) { ?>

<script>
var url = '<?php echo $GLOBALS['WEB_PATH'];?>/library/new_html2pdf/createPdf.php?op=p&file_name=../../library/new_html2pdf/report_stock_ledger';
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
