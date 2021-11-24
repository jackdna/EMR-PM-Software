<?php
/*
File: day_order_report_result.php
Coded in PHP7
Purpose: Remake & Return Report
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../config/config.php");
require_once(dirname('__FILE__')."/../../library/classes/functions.php");


function cal_discount($amt,$dis)
{
	$total = 0;
	if(preg_match('/%/', $dis, $matches))
	{
		$disc = str_replace('%','',$dis);
		$total = ($amt*$disc)/100;
	}
	elseif(preg_match('/$/', $dis, $matche))
	{
		$total = str_replace('$','',$dis);
	}
	else
	{
		$total = $dis;
	}
	return $total;
}

if($_POST['generateRpt'])
{

	$authProviderNameArr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$opInitial = $authProviderNameArr[1][0];
	$opInitial .= $authProviderNameArr[0][0];
	$opInitial = strtoupper($opInitial);
	$status='';
	if(empty($_POST['status'])===false){
		$status = str_replace(",", "','", "'".$_POST['status']."'");
	}

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
		
   /*$facRs = imw_query("select fac.id,loc.loc_name from in_location as loc
   						LEFT JOIN facility as fac on loc.pos=fac.fac_prac_code");
   while($facRes=imw_fetch_array($facRs)){
	   $arrfacility[$facRes['id']]=$facRes['loc_name'];
   }*/
	$facRs = imw_query("select id,loc_name from in_location");
   while($facRes=imw_fetch_array($facRs)){
	   $arrfacility[$facRes['id']]=$facRes['loc_name'];
   }
	//LAB

	$q=imw_query("select * from in_lens_lab order by lab_name")or die(imw_error());
	while($dlist=imw_fetch_object($q)){
		$arrlab[$dlist->id]=$dlist->lab_name;
	}

	//OPERATORS
   $usersRs = imw_query("select id, fname,lname from users");
   while($usersRes=imw_fetch_array($usersRs)){
	   if($usersRes['lname']!='' || $usersRes['fname']!=''){
			$arrUsers[$usersRes['id']]=$usersRes['lname'].', '.$usersRes['fname']; 
			//TWO CHARACTERS
			//$opInit = substr($usersRes['lname'],0,1);
			//$opInit .= substr($usersRes['fname'],0,1);
			//$arrUsersTwoChar[$usersRes['id']] = strtoupper($opInit);
			$arrUsersTwoChar[$usersRes['id']] = $usersRes['lname'].', '.$usersRes['fname'];
	   }
   }
	
	
	$dateFrom=saveDateFormat($_POST['date_from']);
	$dateTo=saveDateFormat($_POST['date_to']);
	$show_report=$_POST['show_report'];
	
	if($show_report=="summary")
	{
		$mainQry="Select in_order.id, remake.remake_reason, in_order.grand_total as total_amount,
		remake.remake_doctor, remake.remake_optician, remake.remake_lab, loc.loc_name, 
		loc.id as fac_id FROM in_order 
		
		LEFT JOIN in_order_details as ord ON ord.order_id=in_order.id
		LEFT JOIN in_order_remake_details as remake ON remake.order_id=in_order.id
		LEFT JOIN patient_data ON patient_data.id = ord.patient_id 
		
		LEFT JOIN in_order_fac as ord_fac ON ord_fac.order_det_id = ord.id
		LEFT JOIN in_location as loc on loc.id=ord_fac.loc_id
		 
		WHERE ord.del_status='0' 
		AND (ord.entered_date BETWEEN '".$dateFrom."' AND '".$dateTo."')
		AND in_order.re_make_id!=''";
		
		// LEFT JOIN facility as fac on fac.id=ord_fac.facility_id 
		// LEFT JOIN in_location as loc on loc.pos=fac.fac_prac_code
	}
	elseif($show_report=="detail")
	{
		$mainQry="Select in_order.id as order_id, remake.remake_reason, in_order.grand_total as total_amount, 
		remake.remake_doctor, remake.remake_optician, remake.remake_lab, loc.loc_name, 
		DATE_FORMAT(in_order.entered_date, '%m-%d-%Y') as enteredDate, ord.operator_id as 'actionBy', 
		loc.id as fac_id, patient_data.fname, patient_data.lname, ord.patient_id FROM in_order 
		
		LEFT JOIN in_order_details as ord ON ord.order_id=in_order.id
		LEFT JOIN in_order_remake_details as remake ON remake.order_id=in_order.id
		LEFT JOIN patient_data ON patient_data.id = ord.patient_id 
		
		LEFT JOIN in_order_fac as ord_fac ON ord_fac.order_det_id = ord.id
		LEFT JOIN in_location as loc on loc.id=ord_fac.loc_id
		
		WHERE ord.del_status='0' 
		AND (ord.entered_date BETWEEN '".$dateFrom."' AND '".$dateTo."')
		AND in_order.re_make_id!=''";
	}
		
	
	if(empty($_POST['faclity'])==false){
		$mainQry.=" AND ord_fac.loc_id IN($_POST[faclity])";
		/*$mainQry.=" fac.id from facility as fac 
		left join pos_facilityies_tbl as pos_fac on pos_fac.pos_facility_id=fac.fac_prac_code 
		left join in_location as loc on loc.pos=pos_fac.pos_facility_id  ";*/
	}
	
	if(empty($_POST['reasons'])==false){
		$mainQry.=" AND remake.remake_reason_id IN(".$_POST['reasons'].")";
	}
	
	if(empty($_POST['physicians'])==false){
		$mainQry.=" AND (remake.remake_doctor IN ($_POST[physicians]) OR remake.remake_optician IN ($_POST[physicians]))";
	}
	
	if(empty($_POST['lab'])==false){
		$mainQry.=" AND remake.remake_lab IN ($_POST[lab])";
	}
	
	$mainQry.=' GROUP BY in_order.id';
	$mainQry.=' ORDER by loc.loc_name';
	//echo $mainQry;

	$mainRs=imw_query($mainQry)or die(imw_error());
	$mainNumRs=imw_num_rows($mainRs);
	while($mainRes=imw_fetch_array($mainRs)){
		$arrMainDetail[] = $mainRes;
		$showOpr = $mainRes['operator_id'];
		$showfacility = $mainRes['loc_id'];
	}
	//order detail
	$html=$htmlPDF=$reportHtml=$reportHtmlPDF='';
	if(count($arrMainDetail)>0){
		$grandAmount=0; $totQry=$totPrice=$Totdisc=$Totamt=0;
		foreach($arrMainDetail as $itemDetails){
			$subTotAmt =0;
			$totQry+=$itemDetails['total_qty'];
			$oprName = $arrUsersTwoChar[$itemDetails['actionBy']];
			$ord_status = $itemDetails['order_status'];
			$total_qnty=0;
			if($itemDetails['order_status']=="")
			{
				$ord_status = "pending";
			}
			
			$tt_pric=0;			
			if($itemDetails['total_amount']!="")
			{
				$tt_pric = $itemDetails['total_amount'];
			}
			$Totamt+=$tt_pric;
			if($show_report=="summary")
			{
				$reason=$itemDetails['remake_reason'];	
				$provider=($itemDetails['remake_lab'])?$arrlab[$itemDetails['remake_lab']]:$arrUsersTwoChar[$itemDetails['remake_doctor']];	
				$arr['remake_doctor']=$arrUsersTwoChar[$itemDetails['remake_doctor']];
				$arr['remake_optician']=$arrUsersTwoChar[$itemDetails['remake_optician']];
				$arr['remake_lab']=$arrlab[$itemDetails['remake_lab']];
				$arr['provider']=$provider;
				$arr['remake_reason']=$reason;
				$arr['id']=$itemDetails['id'];
				$arr['total_amount']=$itemDetails['total_amount'];
				
				if(empty($provider)==true)$provider='No Lab';
				$sum_arr[$itemDetails['fac_id']][$provider][$reason][]=$arr;
			}
			
			if($show_report=="detail")
			{
				$arr['remake_doctor']=$arrUsersTwoChar[$itemDetails['remake_doctor']];
				$arr['remake_optician']=$arrUsersTwoChar[$itemDetails['remake_optician']];
				$arr['remake_lab']=$arrlab[$itemDetails['remake_lab']];
				$arr['provider']=($itemDetails['remake_lab'])?$arrlab[$itemDetails['remake_lab']]:$arrUsersTwoChar[$itemDetails['remake_doctor']];
				$arr['remake_reason']=$itemDetails['remake_reason'];
				$arr['order_id']=$itemDetails['order_id'];
				$arr['enteredDate']=$itemDetails['enteredDate'];
				$arr['pt_name']=$itemDetails['lname'].' '.$itemDetails['fname'].' - '.$itemDetails['patient_id'];
				$arr['ord_status']=ucfirst($ord_status);
				$arr['total_amount']=$itemDetails['total_amount'];
				$arr['oprName']=$oprName;
				
				$det_arr[$itemDetails['fac_id']][]=$arr;
			}
				
		}
	}
		
		
	
		//Final html
		if($show_report=="summary")
		{
			foreach($sum_arr as $fac_id=>$fac_arr)
			{
				$sub_total='';
				$html.='<tr style="height:20px;">
				<td class="reportTitle" colspan="5">Facility: '.$arrfacility[$fac_id].'</td>
				</tr>';
				$htmlPDF.='<tr style="height:20px;">
				<td class="reportTitle" colspan="5">Facility: '.$arrfacility[$fac_id].'</td>
				</tr>';

				foreach($fac_arr as $provider => $pro_data){
					
					foreach($pro_data as $reason => $reason_data){
						$amt=0;
						$orders_count=count($reason_data);
						foreach($reason_data as $arr){ 
							$amt+=$arr['total_amount'];
						}
						$sub_total+=$amt;
							
						$html.='<tr style="height:20px;">
						<td class="whiteBG rptText13 alignLeft" >'.$provider.'</td>
						<td class="whiteBG rptText13 alignLeft">'.$reason.'</td>
						<td class="whiteBG rptText13 alignRight">'.$orders_count.'</td>
						<td class="whiteBG rptText13 alignRight">'.currency_symbol(true).$amt.'</td>
						<td class="whiteBG rptText13 alignLeft">&nbsp;</td>
						</tr>';	

						$htmlPDF.='<tr style="height:20px;">
						<td class="whiteBG rptText13 alignLeft" style="width:150px;">'.$provider.'</td>
						<td class="whiteBG rptText13 alignLeft" style="width:180px;">'.$reason.'</td>
						<td class="whiteBG rptText13 alignRight" style="width:60px;">'.$orders_count.'</td>
						<td class="whiteBG rptText13 alignRight" style="width:80px;">'.currency_symbol(true).$amt.'</td>
						<td class="whiteBG rptText13 alignLeft" style="width:260px;">&nbsp;</td>
						</tr>';	

					}
				}
				$html.='<tr style="height:20px;">
					<td class="whiteBG rptText13 alignRight" colspan="3"><strong>Sub Total: </strong></td>
					<td class="whiteBG rptText13 alignRight"><strong>'.currency_symbol(true).number_format($sub_total, 2, '.', '').'</strong></td>
					<td class="whiteBG rptText13 alignRight">&nbsp;</td>
					</tr>';
				$htmlPDF.='<tr style="height:20px;">
					<td class="whiteBG rptText13 alignRight" colspan="3"><strong>Sub Total: </strong></td>
					<td class="whiteBG rptText13 alignRight"><strong>'.currency_symbol(true).number_format($sub_total, 2, '.', '').'</strong></td>
					<td class="whiteBG rptText13 alignRight">&nbsp;</td>
					</tr>';
			}
			//$htmlPDF=$html;
	
			$reportHtml.='
			<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
				<tr style="height:25px;">
					<td class="reportHeadBG1 alignLeft" style="width:150px;">Doctor/Lab</td>
					<td class="reportHeadBG1 alignLeft" style="width:220px;">Reason</td>
					<td class="reportHeadBG1 alignLeft" style="width:80px;">Order #</td>
					<td class="reportHeadBG1 alignLeft" style="width:120px;">Total Charges</td>
					<td class="reportHeadBG1 alignLeft" style="width:500px;">&nbsp;</td>
				</tr>
				'.$html.'
				<tr><td colspan="5" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
				<tr style="height:25px">
					<td class="whiteBG rptText13 alignRight">&nbsp;</td>
					<td class="whiteBG rptText13b alignLeft">&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">Grand Total :</td>
					<td class="whiteBG rptText13b alignLeft">'.currency_symbol(true).number_format($Totamt, 2, '.', '').'</td>
					<td class="whiteBG rptText13b alignRight">&nbsp;</td>
				</tr>
			</table>';
	
			$reportHtmlPDF.='
			<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
			'.$htmlPDF.'
			<tr>
			<td colspan="5" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td>
			</tr>
			<tr style="height:25px">
				<td class="whiteBG rptText13b alignRight" colspan="3">Grand Total : </td>
				<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($Totamt, 2, '.', '').'</td>
				<td class="whiteBG rptText13b alignRight">&nbsp;</td>
			</tr>		
			</table>';
		}
		
		if($show_report=="detail")
		{
			foreach($det_arr as $fac_id=>$pro_arr)
			{
				$sub_total='';
				$html.='<tr style="height:20px;">
				<td class="reportTitle" colspan="8">Facility : '.$arrfacility[$fac_id].'</td>
				</tr>';
				foreach($pro_arr as $arr)
				{
				$html.='<tr style="height:20px;">
					<td class="whiteBG rptText13 alignLeft" style="width:100px;">&nbsp;'.$arr['provider'].'</td>
					<td class="whiteBG rptText13 alignLeft" style="width:100px;">&nbsp;'.$arr['remake_reason'].'</td>
					<td class="whiteBG rptText13 alignCenter" style="width:50px;">'.$arr['order_id'].'</td>
					<td class="whiteBG rptText13 alignCenter" style="width:70px;">'.$arr['enteredDate'].'</td>
					<td class="whiteBG rptText13 alignLeft" style="width:135px;">&nbsp;'.$arr['pt_name'].'</td>
					<td class="whiteBG rptText13 alignRight" style="width:100px;">'.currency_symbol(true).number_format($arr['total_amount'], 2, '.', '').'&nbsp;</td>
					<td class="whiteBG rptText13 alignLeft" style="width:80px;">&nbsp;'.ucfirst($arr['ord_status']).'</td>
					<td class="whiteBG rptText13 alignLeft" style="width:80px;">'.$arr['oprName'].'</td>
					</tr>';
					
					$sub_total+=$arr['total_amount'];
				}
				$html.='<tr style="height:20px;">
				<td class="whiteBG rptText13 alignRight" colspan="5"><strong>Sub Total: </strong></td>
				<td class="whiteBG rptText13 alignRight"><strong>'.currency_symbol(true).number_format($sub_total, 2, '.', '').'</strong></td>
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>
				<td class="whiteBG rptText13 alignRight">&nbsp;</td>
				</tr>';
			}
			$htmlPDF=$html;
			
			$reportHtml.='
			<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
				<tr style="height:25px;">
				
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:120px;">Doctor/Lab</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:200px;">Reason</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Order #</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Order Date</td>
					<td class="reportHeadBG1 alignTop" style="text-align:left; width:200px;">Patient Name - Id</td>
					<td class="reportHeadBG1 alignTop" style="text-align:right; width:100px;">Total Chrgs.</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Status</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:auto;">Opr</td>
				</tr>'.$html.'
				<tr>
				<td colspan="8" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
				<tr style="height:25px">
					<td class="whiteBG rptText13 alignRight" colspan="5"><strong>Grand Total : </strong></td>
					<td class="whiteBG rptText13b alignRight"><strong>'.currency_symbol(true).number_format($Totamt, 2, '.', '').'</strong>&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">&nbsp;</td>
					<td class="whiteBG rptText13b alignRight">&nbsp;</td>
				</tr>
			</table>';		
	
			$reportHtmlPDF.='
			<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
			<tr>
				<td style="width:100px;"></td>
				<td style="width:100px;"></td>			
				<td style="width:50px;"></td>
				<td style="width:70px;"></td>
				<td style="width:135px;"></td>
				<td style="width:100px;"></td>
				<td style="width:80px;"></td>
				<td style="width:80px;"></td>
			</tr>
			'.$htmlPDF.'
			<tr>
				<td colspan="11" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td></tr>
				<tr style="height:25px">
					<td class="whiteBG rptText13 alignRight" colspan="5">Grand Total : </td>
					<td class="whiteBG rptText13b alignRight">'.currency_symbol(true).number_format($Totamt, 2, '.', '').'&nbsp;</td>
					<td class="whiteBG" colspan="2"></td>
				</tr>	
			</table>';
		}
	
	
	$css = '
	<style type="text/css">
	.reportHeadBG{ font-family: Arial, Helvetica, sans-serif; font-size:10px; font-weight:bold; background-color:#D9EDF8;}
	.reportHeadBG1{ font-family: Arial, Helvetica, sans-serif; font-size:11px; font-weight:bold; background-color:#67B9E8; color:#FFF;}
	.reportTitle { font-family: Arial, Helvetica, sans-serif; font-size:10px; font-weight:bold; background-color:#7B7B7B; color:#FFF }
	.rptText13 { font-family: Arial, Helvetica, sans-serif; font-size:11px; }
	.rptText13b { font-family: Arial, Helvetica, sans-serif; font-size:11px; font-weight:bold; }
	.rptText12b { font-family: Arial, Helvetica, sans-serif; font-size:10px; font-weight:bold; }		
	.whiteBG{ background:#fff; } 
	.alignMiddle{vertical-align:middle;} .alignTop{vertical-align:top;}  .alignBottom{vertical-align:bottom;}
	.alignLeft{text-align:left;} .alignRight{text-align:right;} .alignCenter{text-align:center;} .alignJustify{text-align:justify;}		
	</style>';		

	//SELECTION TO SHOW
	if(empty($_POST['physicians'])===false){ $arrSel=explode(',', $_POST['physicians']); }
	if(empty($_POST['faclity'])===false){ $arrfacly=explode(',', $_POST['faclity']); }
	if(empty($_POST['reasons'])===false){ $arrReason=explode(',', $_POST['reasons']); }
	$selOpr='All';
	$selfacility='All';
	$selReason="All";
	$selOpr=(count($arrSel)>1)? 'Multi' : ((count($arrSel)=='1')? ucfirst($arrUsers[$showOpr]): $selOpr);
	$selReason=(count($arrReason)>1)? 'Multi' : ((count($arrReason)=='1')? $arrReason[0]: $selOpr);
	$selfacility=(count($arrfacly)>1)? 'Multi' : ((count($arrfacly)=='1')? ucfirst($arrfacility[$showfacility]): $selfacility);
	
	//FINAL HTML
	$finalReportHtml='
	<table width="100%" cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;table-layout:fixed;">
	<tr style="height:20px;">
		<td style="text-align:left;" class="reportHeadBG" width="220px">&nbsp;Remake & Return Report</td>
		<td style="text-align:left;" class="reportHeadBG" width="auto" >&nbsp;Report for Date : '.$_POST['date_from'].' To '.$_POST['date_to'].'</td>
		
		<td class="reportHeadBG" width="180px">&nbsp;Report Type : '.ucfirst($_POST['show_report']).'</td>
		<td style="text-align:left;" class="reportHeadBG" width="auto">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
	</tr>
	<tr style="height:20px;">
		<td class="reportHeadBG">&nbsp;Doctor : '.$selOpr.'</td>
		<td class="reportHeadBG">&nbsp;Facility : '.$selfacility.'</td>
		<td class="reportHeadBG" colspan="2">&nbsp;Reason : '.$selReason.'</td>
	</tr>
	</table>
	'.$reportHtml;

	if(count($arrMainDetail)>0)
	{
	//FINAL PDF
		$mm = 12;
		if($show_report=="summary")
		{
			$mm = 12;
		}
		$finalReportHtmlPDF.='
			<page backtop="'.$mm.'mm" backbottom="5mm">
			<page_footer>
					<table style="width:700px;table-layout:fixed;">
						<tr>
							<td style="text-align: center;	width: 700px">Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
			</page_footer>
			<page_header>		
			<table cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
			<tr style="height:20px;">
				<td style="text-align:left;" class="reportHeadBG" width="245">&nbsp;Remake & Return Report</td>
				<td style="text-align:left;" class="reportHeadBG" width="245">&nbsp;Report for Date : '.$_POST['date_from'].' To '.$_POST['date_to'].'</td>
				<td style="text-align:left;" class="reportHeadBG" width="250">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
			</tr>
			<tr style="height:20px;">
				<td class="reportHeadBG">&nbsp;Operator : '.$selOpr.'</td>
				<td class="reportHeadBG">&nbsp;Facility : '.$selfacility.'</td>
				<td class="reportHeadBG">&nbsp;Report Type : '.ucfirst($_POST['show_report']).'</td>
			</tr>
			</table>
			<table cellpadding="1" cellspacing="1" border="0" style="background:#E3E3E3;">';
		if($show_report=="summary")
		{
			$finalReportHtmlPDF.='
			<tr style="height:25px;">
				<td class="reportHeadBG1" style="text-align:left; width:150px;">Doctor/Lab</td>
				<td class="reportHeadBG1" style="text-align:left; width:180px;">Reason</td>
				<td class="reportHeadBG1" style="text-align:left; width:60px;">Order #</td>
				<td class="reportHeadBG1" style="text-align:left; width:80px;">Total Charges</td>
				<td class="reportHeadBG1" style="text-align:left; width:260px;">&nbsp;</td>
			</tr>';
		}
		if($show_report=="detail")
		{
			$finalReportHtmlPDF.='
			<tr>
				<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Doctor/Lab</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:100px;">Reason</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:50px;">Order #</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:70px;">Order Date</td>
					<td class="reportHeadBG1 alignTop" style="text-align:left; width:135px;">Patient Name - Id</td>
					<td class="reportHeadBG1 alignTop" style="text-align:right; width:100px;">Total Chrgs.</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:80px;">Status</td>
					<td class="reportHeadBG1 alignTop" style="text-align:center; width:80px;">Opr</td>
			</tr>';
		}
		$finalReportHtmlPDF.='</table></page_header>'.
			$reportHtmlPDF.'
		</page>';
	}

  $pdfText = $css.$finalReportHtmlPDF;
  file_put_contents('../../library/new_html2pdf/day_order_report_result.html',$pdfText);
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
<form name="searchFormResult" action="remake_return_result.php" method="post">
<input type="hidden" name="physicians" id="physicians" value="" />
<input type="hidden" name="lab" id="lab" value="" />
<input type="hidden" name="faclity" id="faclity" value="" />
<input type="hidden" name="reasons" id="reasons" value="" />
<input type="hidden" name="date_from" id="date_from" value="" />
<input type="hidden" name="date_to" id="date_to" value="" />
<input type="hidden" name="show_report" id="show_report" value="" />
<input type="hidden" name="generateRpt" id="generateRpt" value="yes" />
</form>

<?php if(isset($_REQUEST['print'])) { ?>

<script>
var url = '<?php echo $GLOBALS['WEB_PATH'];?>/library/new_html2pdf/createPdf.php?op=p&file_name=../../library/new_html2pdf/day_order_report_result';
window.location.href = url;
</script>

<?php } ?>

<script type="text/javascript">
$(document).ready(function(){
	var numr = '<?php echo $mainNumRs; ?>';
	var numr2 = '<?php echo $itemNumRs; ?>';		
	
	//BUTTONS
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Search","top.main_iframe.reports_iframe.submitForm();");
	if(numr>0 || numr2>0){
		mainBtnArr[1] = new Array("frame","Print","top.main_iframe.reports_iframe.printreport()");
	}
	top.btn_show("admin",mainBtnArr);	
	top.main_iframe.loading('none');
});
</script>

</body>
</html>