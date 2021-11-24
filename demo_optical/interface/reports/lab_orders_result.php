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
	
	if(empty($_POST['order_ids'])==false){
		$order_ids=str_replace(' ','', $_POST['order_ids']);
		$order_ids="'".str_replace(",", "','", $order_ids)."'";
	}
	
	//Labs
	$facRs = imw_query("SELECT id, lab_name FROM in_lens_lab");
	$arrAllLabs[0]='No Lab';
	while($facRes=imw_fetch_array($facRs)){
	   $arrAllLabs[$facRes['id']]=$facRes['lab_name'];
	}unset($facRs);

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
	
	$dateFrom=saveDateFormat($_POST['date_from']);
	$dateTo=saveDateFormat($_POST['date_to']);


	$mainQry="Select ord_det.id, ord_det.order_id, ord_det.qty, ord_det.qty_right, ord_det.lab_id, 
	ord_det.operator_id, ord_det.item_id, ord_det.order_status,
	ord_det.price, ord_det.loc_id, 
	ord_det.patient_id, ord_det.upc_code, ord_det.item_name,
	DATE_FORMAT(ord_det.entered_date, '%m-%d-%Y') as 'entered_date', patient_data.fname, patient_data.lname 
	FROM in_order_details ord_det  
	JOIN patient_data ON patient_data.id = ord_det.patient_id 
	LEFT JOIN in_lens_lab ON in_lens_lab.id = ord_det.lab_id 
	WHERE ord_det.del_status='0' AND ord_det.lab_id>0 AND (ord_det.entered_date BETWEEN '".$dateFrom."' AND '".$dateTo."')";

	if(empty($_POST['lab_id'])==false){
		$mainQry.=' AND ord_det.lab_id IN('.$_POST['lab_id'].')';
	}
	if(empty($order_ids)==false){
		$mainQry.=" AND ord_det.order_id IN(".$order_ids.")";
	}
	if(empty($_POST['order_status'])==false){
		if($_POST['order_status']=='pending'){
			$mainQry.=" AND (ord_det.order_status='".$_POST['order_status']."' OR ord_det.order_status='')";
		}else{
			$mainQry.=" AND ord_det.order_status='".$_POST['order_status']."'";
		}
	}
	$mainQry.=' ORDER BY in_lens_lab.lab_name ASC, ord_det.entered_date DESC, ord_det.order_id DESC';
	
	$mainRs=imw_query($mainQry);
	$arrMainDetail=array();
	while($mainRes=imw_fetch_array($mainRs)){
		$lab_id=$mainRes['lab_id'];
		$ord_det_id=$mainRes['id'];
		$order_status=($mainRes['order_status']=='')? 'Pending' : ucfirst($mainRes['order_status']);
		
		if($_POST['show_report']=='detail'){
			$arrMainDetail[$lab_id][$ord_det_id]['entered_date'] = $mainRes['entered_date'];
			$arrMainDetail[$lab_id][$ord_det_id]['order_id'] = $mainRes['order_id'];
			$arrMainDetail[$lab_id][$ord_det_id]['pat_name'] = $mainRes['lname'].' '.$mainRes['fname'].' - '.$mainRes['patient_id'];
			$arrMainDetail[$lab_id][$ord_det_id]['item'] = $mainRes['item_name'].' - '.$mainRes['upc_code'];
			$arrMainDetail[$lab_id][$ord_det_id]['qty']= $mainRes['qty'] + $mainRes['qty_right'];
			$arrMainDetail[$lab_id][$ord_det_id]['price']= $mainRes['price'];
			$arrMainDetail[$lab_id][$ord_det_id]['facility'] = $arrfacility[$mainRes['loc_id']];
			$arrMainDetail[$lab_id][$ord_det_id]['opr_name'] = $arrUsersTwoChar[$mainRes['operator_id']];
			$arrMainDetail[$lab_id][$ord_det_id]['order_status'] = $order_status;
		}else{
			$arrMainDetail[$lab_id]['lab']= $arrAllLabs[$mainRes['lab_id']];
			$arrMainDetail[$lab_id]['orders'][] = $mainRes['order_id'];
			$arrMainDetail[$lab_id]['qty']+= $mainRes['qty'] + $mainRes['qty_right'];
			$arrMainDetail[$lab_id]['tot_price']+= $mainRes['price'] *$mainRes['qty'];
		}
	}

	
	$html=$htmlPDF=$reportHtml=$reportHtmlPDF='';
	$grandTotPrice=$grandTotQty=0;


	if(sizeof($arrMainDetail)>0){
		//DETAIL
		if($_POST['show_report']=='detail'){
			foreach($arrMainDetail as $lab_id => $labData){
				$subQty=$subPrice=0;
				$html.='<tr><td class="reportTitle" colspan="9">&nbsp;Lab:&nbsp;'.$arrAllLabs[$lab_id].'</td></tr>';			
				
				foreach($labData as $ord_det_id => $orderDet){
					$totalPrice=0;
					$totalPrice=$orderDet['price']*$orderDet['qty'];
					
					$subQty+=$orderDet['qty'];
					$subPrice+=$totalPrice;
					
					$html.='<tr style="height:20px;">
					<td class="whiteBG rptText13 alignLeft">&nbsp;'.$orderDet['entered_date'].'</td>
					<td class="whiteBG rptText13 alignLeft">&nbsp;'.$orderDet['order_id'].'</td>
					<td class="whiteBG rptText13 alignLeft">&nbsp;'.$orderDet['pat_name'].'</td>
					<td class="whiteBG rptText13 alignLeft">&nbsp;'.$orderDet['item'].'</td>				
					<td class="whiteBG rptText13 alignRight">'.$orderDet['qty'].'&nbsp;</td>
					<td class="whiteBG rptText13 alignRight">'.currency_symbol(true).number_format($totalPrice, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13 alignLeft">&nbsp;'.$orderDet['facility'].'</td>
					<td class="whiteBG rptText13 alignLeft">&nbsp;'.$orderDet['opr_name'].'</td>
					<td class="whiteBG rptText13 alignLeft">&nbsp;'.$orderDet['order_status'].'</td>				
					</tr>';
		
					$htmlPDF.='<tr style="height:20px;">
					<td class="whiteBG rptText13 alignLeft" style="width:80px">&nbsp;'.$orderDet['entered_date'].'</td>
					<td class="whiteBG rptText13 alignLeft" style="width:60px">&nbsp;'.$orderDet['order_id'].'</td>
					<td class="whiteBG rptText13 alignLeft" style="width:165px">&nbsp;'.$orderDet['pat_name'].'</td>
					<td class="whiteBG rptText13 alignLeft" style="width:200px">&nbsp;'.$orderDet['item'].'</td>				
					<td class="whiteBG rptText13 alignRight" style="width:80px">'.$orderDet['qty'].'&nbsp;</td>
					<td class="whiteBG rptText13 alignRight" style="width:100px">'.currency_symbol(true).number_format($totalPrice, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13 alignLeft" style="width:150px">&nbsp;'.$orderDet['facility'].'</td>
					<td class="whiteBG rptText13 alignLeft" style="width:80px">&nbsp;'.$orderDet['opr_name'].'</td>
					<td class="whiteBG rptText13 alignLeft" style="width:120px">&nbsp;'.$orderDet['order_status'].'</td>				
					</tr>';
				}
	
				$grandQty+=$subQty;
				$grandPrice+=$subPrice;
				//SUB TOTALS
				$html.='<tr style="height:20px;">
				<td class="whiteBG rptText13b alignRight" colspan="4">Sub Total:</td>
				<td class="whiteBG rptText13b alignRight">'.$subQty.'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($subPrice, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft" colspan="3">&nbsp;</td>
				</tr>';
	
				$htmlPDF.='<tr style="height:20px;">
				<td class="whiteBG rptText13b alignRight" colspan="4">Sub Total:</td>
				<td class="whiteBG rptText13b alignRight">'.$subQty.'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($subPrice, 2, '.', '').'&nbsp;</td>
				<td class="whiteBG rptText13 alignLeft" colspan="3">&nbsp;</td>
				</tr>';
			}
		
			//Final html
			$reportHtml.='
			<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
				<tr style="height:25px;">
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:80px;">Order Date</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:80px;">Ord#</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:110px;">Patient Name</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:150px;">Item Name - UPC Code</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:50px;">Qty</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Tot. Price</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:140px;">Facility</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:40px;">Opr</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:80px;">Status</td>
				</tr>'.$html.'
				<tr><td colspan="9" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
				<tr style="height:20px;">
					<td class="whiteBG rptText13b alignRight" colspan="4">Grand Total: </td>
					<td class="whiteBG rptText13b alignRight">'.$grandQty.'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($grandPrice, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13 alignRight" colspan="3">&nbsp;</td>
				</tr>				
			</table>';
		
			$reportHtmlPDF.='
			<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
				'.$htmlPDF.'
				<tr><td colspan="9" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
				<tr style="height:20px;">
					<td class="whiteBG rptText13b alignRight" colspan="4">Grand Total: </td>
					<td class="whiteBG rptText13b alignRight">'.$grandQty.'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($grandPrice, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13 alignRight" colspan="3">&nbsp;</td>
				</tr>				
			</table>';	
		}else{
			
			//SUMMARY
			foreach($arrMainDetail as $lab_id => $orderDet){
				
				$no_of_orders=count($orderDet['orders']);
				
				$grandOrders+=$no_of_orders;
				$grandQty+=$orderDet['qty'];
				$grandPrice+=$orderDet['tot_price'];
				
				$html.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$orderDet['lab'].'</td>
				<td class="whiteBG rptText13 alignRight">'.$no_of_orders.'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.$orderDet['qty'].'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">'.currency_symbol(true).number_format($orderDet['tot_price'], 2, '.', '').'&nbsp;</td>				
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>				
				</tr>';
	
				$htmlPDF.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft" style="width:150px">&nbsp;'.$orderDet['lab'].'</td>
				<td class="whiteBG rptText13 alignRight" style="width:100px">'.$no_of_orders.'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight" style="width:100px">'.$orderDet['qty'].'&nbsp;</td>
				<td class="whiteBG rptText13 alignRight" style="width:100px">'.currency_symbol(true).number_format($orderDet['tot_price'], 2, '.', '').'&nbsp;</td>				
				<td class="whiteBG rptText13 alignRight" style="width:275px">&nbsp;</td>				
				</tr>';
			}
		
			//Final html
			$reportHtml.='
			<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
				<tr style="height:25px;">
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:200px;">Lab Name</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:120px;">Orders Count</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:120px;">Qty</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:120px;">Total Price</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:auto;">&nbsp;</td>
				</tr>'.$html.'
				<tr><td colspan="5" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
				<tr style="height:20px;">
					<td class="whiteBG rptText13b alignRight">Grand Total: </td>
					<td class="whiteBG rptText13b alignRight">'.$grandOrders.'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.$grandQty.'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($grandPrice, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13 alignRight">&nbsp;</td>
				</tr>				
			</table>';
		
			$reportHtmlPDF.='
			<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
				'.$htmlPDF.'
				<tr><td colspan="5" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
				<tr style="height:20px;">
					<td class="whiteBG rptText13b alignRight">Grand Total: </td>
					<td class="whiteBG rptText13b alignRight">'.$grandOrders.'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.$grandQty.'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($grandPrice, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13 alignRight">&nbsp;</td>
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
	if(empty($_POST['lab_id'])===false){ $arrlabs=explode(',', $_POST['lab_id']); }
	if(empty($_POST['order_status'])===false){ $arrstatus=explode(',', $_POST['order_status']); }
	
	$selLab='All';
	$selstatus='All';
	$selLab=(count($arrlabs)>1)? 'Multi' :((count($arrlabs)=='1')?ucfirst($arrAllLabs[$_POST['lab_id']]): $selLab);
	$selstatus=(count($arrstatus)>1)? 'Multi' : ((count($arrstatus)=='1')? ucfirst($_POST['order_status']): $selstatus);
	$sel_order_id=str_replace("'","", $order_ids);
	
	//FINAL HTML
	$finalReportHtml='
	<table width="100%" cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
	<tr style="height:20px;">
		<td style="text-align:left;" class="reportHeadBG" width="320px">&nbsp;Lab Orders Report</td>
		<td style="text-align:left;" class="reportHeadBG" width="320px" >&nbsp;Date Range From '.$_POST['date_from'].' To '.$_POST['date_to'].'</td>
		<td style="text-align:left;" class="reportHeadBG" width="auto">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
	</tr>
	<tr style="height:20px;">
		<td class="reportHeadBG">&nbsp;Lab : '.$selLab.'</td>
		<td class="reportHeadBG">&nbsp;Order Id : '.$sel_order_id.'</td>
		<td class="reportHeadBG">&nbsp;Status : '.$selstatus.'</td>
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
			</page_footer>';

			if($_POST['show_report']=='detail'){
				$finalReportHtmlPDF.='
				<page_header>
				<table cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
				<tr style="height:20px;">
					<td style="text-align:left;" class="reportHeadBG" width="355">&nbsp;Lab Orders Report</td>
					<td style="text-align:left;" class="reportHeadBG" width="355">&nbsp;Date Range From '.$_POST['date_from'].' To '.$_POST['date_to'].'</td>
					<td style="text-align:left;" class="reportHeadBG" width="355">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
				</tr>
				<tr style="height:20px;">
					<td class="reportHeadBG">&nbsp;Lab : '.$selLab.'</td>
					<td class="reportHeadBG">&nbsp;Order Id : '.$sel_order_id.'</td>
					<td class="reportHeadBG">&nbsp;Status : '.$selstatus.'</td>
				</tr>
				</table>				
				<table cellpadding="1" cellspacing="1" border="0" style="background:#E3E3E3;">
				<tr>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:80px;">Order Date</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:60px;">Ord#</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:165px;">Patient Name</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:200px;">Item Name - UPC Code</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:80px;">Qty</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Tot. Price</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:150px;">Facility</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:80px;">Opr</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:120px;">Status</td>
				</tr>
				</table>
				</page_header>';
			}else{
				$finalReportHtmlPDF.='
				<page_header>
				<table cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
				<tr style="height:20px;">
					<td style="text-align:left;" class="reportHeadBG" width="225">&nbsp;Lab Orders Report</td>
					<td style="text-align:left;" class="reportHeadBG" width="265">&nbsp;Date Range From '.$_POST['date_from'].' To '.$_POST['date_to'].'</td>
					<td style="text-align:left;" class="reportHeadBG" width="245">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
				</tr>
				<tr style="height:20px;">
					<td class="reportHeadBG">&nbsp;Lab : '.$selLab.'</td>
					<td class="reportHeadBG">&nbsp;Order Id : '.$sel_order_id.'</td>
					<td class="reportHeadBG">&nbsp;Status : '.$selstatus.'</td>
				</tr>
				</table>				
				<table cellpadding="1" cellspacing="1" border="0" style="background:#E3E3E3;">
				<tr>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:150px;">Lab Name</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Orders Count</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Qty</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Total Price</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:275px;">&nbsp;</td>
				</tr>
				</table>
				</page_header>';
			}
			
			$finalReportHtmlPDF.=
			$reportHtmlPDF.'
		</page>';
	}

	$pdfText = $css.$finalReportHtmlPDF;
	file_put_contents('../../library/new_html2pdf/lab_orders_result.html',$pdfText);
	$op='p';
	if($_POST['show_report']=='detail'){$op='l';}

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
<form name="medicationFormResult" action="lab_orders_result.php" method="post">
<input type="hidden" name="lab_id" id="lab_id" value="" />
<input type="hidden" name="order_status" id="order_status" value="" />
<input type="hidden" name="order_ids" id="order_ids" value="" />
<input type="hidden" name="date_from" id="date_from" value="" />
<input type="hidden" name="date_to" id="date_to" value="" />
<input type="hidden" name="show_report" id="show_report" value="" />
<input type="hidden" name="generateRpt" id="generateRpt" value="yes" />
</form>

<?php if(isset($_REQUEST['print'])) { 

?>

<script>
var url = '<?php echo $GLOBALS['WEB_PATH'];?>/library/new_html2pdf/createPdf.php?op=<?php echo $_REQUEST['op'];?>&file_name=../../library/new_html2pdf/lab_orders_result';
window.location.href = url;
</script>

<?php } ?>

<script type="text/javascript">
$(document).ready(function(){
	var numr = '<?php echo sizeof($arrMainDetail); ?>';		
	var op= '<?php echo $op;?>';

	//BUTTONS
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Search","top.main_iframe.reports_iframe.submitForm();");
	if(numr>0){
		mainBtnArr[1] = new Array("frame","Print","top.main_iframe.reports_iframe.printreport('"+op+"')");
	}
	top.btn_show("admin",mainBtnArr);
	top.main_iframe.loading('none');		
});
</script>

</body>
</html>