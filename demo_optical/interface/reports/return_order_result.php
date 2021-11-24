<?php
/*
File: return_order_result.php
Coded in PHP7
Purpose: Show Return Order Report
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
	if($_POST['groupBy']!='manufac')
	{
		$manu_detail_qry = "select id, manufacturer_name from in_manufacturer_details";
		$manu_detail_rs = imw_query($manu_detail_qry);
		while($manu_detail_res=imw_fetch_array($manu_detail_rs))
		{
			$arrManufac[$manu_detail_res['id']]=$manu_detail_res['manufacturer_name'];
		}
	}

	//TYPES
	   $typeRs = imw_query("select * from in_module_type");
	   while($typeRes=imw_fetch_array($typeRs)){
		   $arrTypes[$typeRes['id']]=$typeRes['module_type_name'];
	   }
	   
   //Facility
   $facRs = imw_query("select * from in_location");
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
	
	$mainQry="Select loc.id as loc_id, ord_ret.id, ord_ret.facility_id, ord_ret.entered_by, ord_ret.module_type_id,
	ord_ret.order_id, ord_ret.order_detail_id,
	ord.upc_code, ord.item_name, ord_ret.item_id, ord_ret.return_qty, rr.return_reason, mt.module_type_name,
	ord_ret.status, DATE_FORMAT(ord_ret.entered_date, '%m-%d-%Y') as 'enteredDate', ord_ret.patient_id, patient_data.fname, patient_data.lname 
	from in_order_return as ord_ret 
	join facility as fac on fac.id=ord_ret.facility_id 
	join in_location as loc on loc.pos=fac.fac_prac_code
	inner join in_module_type as mt on mt.id = ord_ret.module_type_id
	inner join in_order_details as ord on ord.id = ord_ret.order_detail_id
	inner JOIN patient_data ON patient_data.id = ord_ret.patient_id
	inner join in_return_reason as rr on rr.id = ord_ret.reason 
	WHERE (ord_ret.entered_date BETWEEN '".$dateFrom."' AND '".$dateTo."') and ord_ret.return_qty>0 and ord_ret.del_status='0'";
			
	if(empty($_POST['operators'])==false){
		$mainQry.=' AND ord_ret.entered_by IN('.$_POST['operators'].')';
	}
	if(empty($_POST['facility'])==false){
		$mainQry.=' AND loc.id IN('.$_POST['facility'].')';
	}
	if(empty($_POST['product_type'])==false){
		$mainQry.=' AND ord_ret.module_type_id IN('.$_POST['product_type'].')';
	}
	if(empty($_POST['status'])==false){
		$mainQry.=' AND ord_ret.status="'.$_POST['status'].'"';
	}
	$mainQry.=' GROUP BY ord_ret.order_detail_id ORDER BY ord_ret.id';
	//echo $mainQry;
	//die();
	$mainRs=imw_query($mainQry);
	$mainNumRs=imw_num_rows($mainRs);
	while($mainRes=imw_fetch_array($mainRs)){
		$arrMainDetail[] = $mainRes;
		$showOpr = $mainRes['entered_by'];
		$showfacility = $arrfacility[$mainRes['loc_id']];
		$showtype = $mainRes['module_type_id'];
	}
	//order detail
	$html=$htmlPDF=$reportHtml=$reportHtmlPDF='';
	if(count($arrMainDetail)>0){
		$grandAmount=0; $totQry=$totPrice=$Totdisc=$Totamt=0;
		foreach($arrMainDetail as $itemDetails){
			$subTotAmt =0;
			$totQry+=$itemDetails['return_qty'];
			$oprName = $arrUsersTwoChar[$itemDetails['entered_by']];
								
			$html.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$itemDetails['order_id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$itemDetails['module_type_name'].'</td>
				<td class="whiteBG rptText13 alignCenter">'.$itemDetails['enteredDate'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$itemDetails['lname'].' '.$itemDetails['fname'].' - '.$itemDetails['patient_id'].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$arrfacility[$itemDetails['loc_id']].'</td>
				<td class="whiteBG rptText13 alignLeft">&nbsp;'.$itemDetails['upc_code'].' - '.$itemDetails['item_name'].'</td>
				<td class="whiteBG rptText13 alignRight">'.$itemDetails['return_qty'].'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter">'.ucfirst($itemDetails['status']).'</td>
				<td class="whiteBG rptText13 alignCenter">'.ucfirst($itemDetails['return_reason']).'</td>
				<td class="whiteBG rptText13 alignCenter">'.$oprName.'</td>
				</tr>';

			$htmlPDF.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignLeft" style="width:40px;">&nbsp;'.$itemDetails['order_id'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:60px;">&nbsp;'.$itemDetails['module_type_name'].'</td>
				<td class="whiteBG rptText13 alignCenter" style="width:70px;">'.$itemDetails['enteredDate'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:120px;">&nbsp;'.$itemDetails['lname'].' '.$itemDetails['fname'].' - '.$itemDetails['patient_id'].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:80px;">&nbsp;'.$arrfacility[$itemDetails['loc_id']].'</td>
				<td class="whiteBG rptText13 alignLeft" style="width:120px;">&nbsp;'.$itemDetails['upc_code'].' - '.$itemDetails['item_name'].'</td>
				<td class="whiteBG rptText13 alignRight" style="width:50px;">'.$itemDetails['return_qty'].'&nbsp;</td>
				<td class="whiteBG rptText13 alignCenter" style="width:50px;">'.ucfirst($itemDetails['status']).'</td>
				<td class="whiteBG rptText13 alignCenter" style="width:95px;">'.ucfirst($itemDetails['return_reason']).'</td>
				<td class="whiteBG rptText13 alignCenter" style="width:30px;">'.$oprName.'</td>
				</tr>';
				
			}
		
		//Final html
		$reportHtml.='
			<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
				<tr style="height:25px;">
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:50px;">Ord. Id</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Category</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:70px;">Return Date</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:200px;">Patient Name - Id</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:150px;">Facility</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:200px;">Upc Code - Item Name</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:80px;">Return Qty</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:80px;">Status</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:120px;">Reason</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:40px;">Opr</td>
				</tr>'.$html.'
				<tr>
				<td colspan="10" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
				<tr style="height:25px">
					<td class="whiteBG rptText13 alignRight" colspan="6">Grand Total : </td>
					<td class="whiteBG rptText13b alignRight">'.$totQry.'&nbsp;</td>
					<td class="whiteBG rptText13b alignRight" colspan="3"></td>
				</tr>
			</table>';
			
			$reportHtmlPDF.='
			<table style="border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
			'.$htmlPDF.'
			<tr>
				<td colspan="10" class="whiteBG pt2 pb2">
					<div style="border-bottom:1px solid #0E87CA;"></div>
				</td>
			</tr>
			<tr style="height:25px">
				<td class="whiteBG rptText13 alignRight" colspan="6">Grand Total : </td>
				<td class="whiteBG rptText13b alignRight">'.$totQry.'&nbsp;</td>
				<td class="whiteBG rptText13b alignRight" colspan="3"></td>
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
	if(empty($_POST['operators'])===false){ $arrSel=explode(',', $_POST['operators']); }
	if(empty($_POST['facility'])===false){ $arrfacly=explode(',', $_POST['facility']); }
	if(empty($_POST['product_type'])===false){ $arrtype=explode(',', $_POST['product_type']); }
	$selOpr='All';
	$selfacility='All';
	$selType='All';
	$selstatus='All';
	$selOpr=(count($arrSel)>1)? 'Multi' : ((count($arrSel)=='1')? ucfirst($arrUsers[$showOpr]): $selOpr);
	$selfacility=(count($arrfacly)>1)? 'Multi' : ((count($arrfacly)=='1')? ucfirst($showfacility): $selfacility);
	$selType=(count($arrtype)>1)? 'Multi' : ((count($arrtype)=='1')? ucfirst($arrTypes[$showtype]): $selType);
	
	if($_POST['status']!="")
	{
		$selstatus = $_POST['status'];
	}
	//FINAL HTML
	$finalReportHtml='
	<table width="100%" cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
	<tr style="height:20px;">
		<td style="text-align:left;" class="reportHeadBG" width="220px">&nbsp;Return Order Report</td>
		<td style="text-align:left;" class="reportHeadBG" width="220px" >&nbsp;Type : '.$selType.'</td>
		<td style="text-align:left;" class="reportHeadBG" width="auto">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
	</tr>
	<tr style="height:20px;">
		<td class="reportHeadBG">&nbsp;Operator : '.$selOpr.'</td>
		<td class="reportHeadBG">&nbsp;Facility : '.$selfacility.'</td>
		<td class="reportHeadBG">&nbsp;Report Status : '.ucfirst($selstatus).'</td>
	</tr>
	</table>
	'.$reportHtml;

	if(count($arrMainDetail)>0)
	{
		//FINAL PDF
		$finalReportHtmlPDF.='
			<page backtop="16mm" backbottom="5mm">
			<page_footer>
					<table style="width:700px;">
						<tr>
							<td style="text-align: center;	width: 700px">Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
			</page_footer>
			<page_header>		
			<table width="700px" cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
			<tr style="height:20px;">
				<td style="text-align:left;" class="reportHeadBG" width="250">&nbsp;Return Order Report</td>
				<td style="text-align:left;" class="reportHeadBG" width="250">&nbsp;Type : '.$selType.'</td>
				<td style="text-align:left;" class="reportHeadBG" width="250">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
			</tr>
			<tr style="height:20px;">
				<td class="reportHeadBG" width="245">&nbsp;Operator : '.$selOpr.'</td>
				<td class="reportHeadBG" width="245">&nbsp;Facility : '.$selfacility.'</td>
				<td class="reportHeadBG" width="250">&nbsp;Report Status : '.ucfirst($selstatus).'</td>
			</tr>
			</table>
			<table cellpadding="1" cellspacing="1" border="0" style="background:#E3E3E3;">
			<tr>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:40px;">Ord. Id</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:60px;">Category</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:70px;">Return Date</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:120px;">Patient Name - Id</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:80px;">Facility</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:120px;">Upc Code - Item Name</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:50px;">Return Qty</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:50px;">Status</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:95px;">Reason</td>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:30px;">Opr</td>
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
<form name="stockFormResult" action="return_order_result.php" method="post">
<input type="hidden" name="product_type" id="product_type" value="" />
<input type="hidden" name="operators" id="operators" value="" />
<input type="hidden" name="facility" id="facility" value="" />
<input type="hidden" name="status" id="status" value="" />
<input type="hidden" name="date_from" id="date_from" value="" />
<input type="hidden" name="date_to" id="date_to" value="" />
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
