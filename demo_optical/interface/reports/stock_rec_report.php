<?php
/*
File: stock_rec_report.php
Purpose: Show Stock Reconciliation Report
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../config/config.php");
require_once(dirname('__FILE__')."/../../library/classes/functions.php");

function pre($arr, $debug = 0){
	print "<pre>";
	print_r($arr);
	print "</pre>";
	if($debug == 1){
		die("Debugging");
	}
}
if($_POST['generateRpt']){
	
	$authProviderNameArr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$opInitial = $authProviderNameArr[1][0];
	$opInitial .= $authProviderNameArr[0][0];
	$opInitial = strtoupper($opInitial);
	
	//User
	$user_qry = "select id, lname,fname from users";
	$user_detail_rs = imw_query($user_qry);
	while($user_detail_res=imw_fetch_array($user_detail_rs)){
		$userfac[$user_detail_res['id']]=$user_detail_res['lname']." ".$user_detail_res['fname'];
		//TWO CHARACTERS
		$opInit = substr($user_detail_res['lname'],0,1);
		$opInit .= substr($user_detail_res['fname'],0,1);
		$arrUsersTwoChar[$user_detail_res['id']] = strtoupper($opInit);
	}
	//Facility
	$facRs = imw_query("select * from in_location");
	while($facRes=imw_fetch_array($facRs)){
		$arrfacility[$facRes['id']]=$facRes['loc_name'];
	}
		
	$dateFrom=saveDateFormat($_POST['date_from']);
	$dateTo=saveDateFormat($_POST['date_to']);
	
	$mainQry="SELECT id,facility,updated_date,user_id FROM in_batch_table WHERE updated_date!='0000-00-00'";
	
	if(empty($_POST['faclity'])==false){ /*Facility filter*/
		$mainQry.=" AND facility IN(".$_POST['faclity'].")";
	}
	if(empty($_POST['operators'])==false){ /*Operator filter*/
		$mainQry.=" AND user_id IN(".$_POST['operators'].")";
	}
	
	if(empty($_POST['date_from'])==false && empty($_POST['date_to'])==false){ /*Expiry Date from & to*/
		$mainQry.=" AND (updated_date BETWEEN '".$dateFrom."' AND '".$dateTo."')";
		$selExpDat =" From ".date("m-d-Y",strtotime($dateFrom))." To ".date("m-d-Y",strtotime($dateTo));
	}
	elseif(empty($_POST['date_from'])==false){ /*Expiry Date from*/
		$mainQry.=" AND updated_date > '".$dateFrom."'";
		$selExpDat =" From ".date("m-d-Y",strtotime($dateFrom));
	}
	elseif(empty($_POST['date_to'])==false){ /*Expiry Date to*/
		$mainQry.=" AND updated_date < '".$dateTo."'";
		$selExpDat =" Up To ".date("m-d-Y",strtotime($dateTo));
	}
	$selExpDat = ($selExpDat=="")?"ALL":$selExpDat;
	$mainQry.=' ORDER BY id ';
	$mainRs=imw_query($mainQry);
	$mainNumRs=imw_num_rows($mainRs);
	while($mainRes=imw_fetch_array($mainRs)){
		if(empty($_POST['reason'])==false){
			$query_res=imw_query("select * from in_batch_records where reason IN(".$_POST['reason'].") and in_batch_id=".$mainRes['id']."");
			while($query_re=imw_fetch_array($query_res)){
				$arrMainDetail[]=$query_re;
			}
		}else {
			$arrMainDetail[] = $mainRes;
		}
		$showFacility = $mainRes['facility'];
		$operator = $mainRes['user_id'];
	}
	//order detail
	$html=$htmlPDF=$reportHtml=$reportHtmlPDF='';
	if(count($arrMainDetail)>0){
		$a=1;
		$rec_array=array();
		$quantity=array();
		$grandAmount=0; $totQry=$totPrice=$Totdisc=$Totamt=0;
		foreach($arrMainDetail as $itemDetails){
			for($i=0;$i<count($itemDetails['id']);$i++)
			{
				if(empty($_POST['reason'])==false)
				{
					$query=imw_query("select * from in_batch_records where in_batch_id IN (".$itemDetails['in_batch_id'].") and reason IN(".$_POST['reason'].")");
				}
				else
				{
					$query=imw_query("select * from in_batch_records where in_batch_id=(".$itemDetails['id'].")");
				}
				while($batch_qry=imw_fetch_array($query))
				{
					$query_itm=imw_query("select * from in_item where id='".$batch_qry['in_item_id']."'");
					if(imw_num_rows($query_itm)>0)
					{	
						while($row=imw_fetch_array($query_itm))
						{
							$query1=imw_query("select * from in_vendor_details where id=".$row['vendor_id']."");
							$row1=imw_fetch_array($query1);
							$query2=imw_query("select  * from in_module_type where id=".$row['module_type_id']."");
							$row2=imw_fetch_array($query2);
							$query3=imw_query("select * from in_manufacturer_details where id=".$row['manufacturer_id']."");
							$row3=imw_fetch_array($query3);
							$query4=imw_query("select * from in_frame_sources where id=".$row['brand_id']."");
							$row4=imw_fetch_array($query4);
							$reason_q=imw_query("select * from in_reason where id=".$batch_qry['reason']."");
							$reson_res=imw_fetch_array($reason_q);
							$query5=imw_query("select * from in_item_loc_total where loc_id='".$_SESSION['pro_fac_id']."' and item_id='".$row['id']."'");
							$row5=imw_fetch_array($query5);
							
							$qty=$batch_qry['in_item_quant']-$batch_qry['in_fac_prev_qty'];
							$rec_array[$row2['module_type_name']][$batch_qry['in_item_id']]['rec'][]=$qty;
							$rec_array[$row2['module_type_name']][$batch_qry['in_item_id']]['fac'][]=$batch_qry['in_fac_prev_qty'];
							$rec_array[$row2['module_type_name']][$batch_qry['in_item_id']]['tot'][]=$batch_qry['prev_tot_qty'];
							$rec_array[$row2['module_type_name']][$batch_qry['in_item_id']]['rec_pric'][]=($qty*$row['wholesale_cost']);
							$rec_array[$row2['module_type_name']][$batch_qry['in_item_id']]['tot_pric'][]=($batch_qry['prev_tot_qty']*$row['wholesale_cost']);
							$rec_array[$row2['module_type_name']][$batch_qry['in_item_id']]['rem_pric'][]=($batch_qry['prev_tot_qty']*$row['wholesale_cost'])+($qty*$row['wholesale_cost']);							
							if($row2['module_type_name']=='contact lenses')
							{
								$con_len=imw_query("select * from in_contact_brand where id=".$row['brand_id']."");
								$con_len_brand=imw_fetch_array($con_len);
								$html.="<tr>
								<td class='whiteBG rptText13 alignLeft'>".$a."</td>
								<td class='whiteBG rptText13 alignLeft'>".$row['upc_code']."</td>
								<td class='whiteBG rptText13 alignLeft'>".$row2['module_type_name']."</td>
								<td class='whiteBG rptText13 alignLeft'>".$row['name']."</td>
								<!--<td class='whiteBG rptText13 alignLeft'>".$row3['manufacturer_name']."</td>-->
								<td class='whiteBG rptText13 alignLeft'>".$row1['vendor_name']."</td>
								<td class='whiteBG rptText13 alignLeft'>".$con_len_brand['brand_name']."</td>
								<td class='whiteBG rptText13 alignRight'>".$batch_qry['prev_tot_qty']."</td>
								<td class='whiteBG rptText13 alignRight'>".$batch_qry['in_fac_prev_qty']."</td>
								<td class='whiteBG rptText13 alignRight'>".$batch_qry['in_item_quant']."</td>
								<td class='whiteBG rptText13 alignLeft'>".$reson_res['reason_name']."</td>
								<td class='whiteBG rptText13 alignLeft'>".$arrfacility[$itemDetails['facility']]."</td>
								<td class='whiteBG rptText13 alignLeft'>".$arrUsersTwoChar[$itemDetails['user_id']]."</td>
								</tr>";	
								$htmlPDF.="<tr>
								<td class='whiteBG rptText13 alignLeft' style='text-align:left;width:30px;'>".$a."</td>
								<td class='whiteBG rptText13 alignLeft' style='text-align:left;width:100px;'>".$row['upc_code']."</td>
								<td class='whiteBG rptText13 alignLeft' style='text-align:left;width:100px;'>".$row2['module_type_name']."</td>
								<td class='whiteBG rptText13 alignLeft' style='text-align:left;width:110px;'>".$row['name']."</td>
								<!--<td class='whiteBG rptText13 alignLeft'>".$row3['manufacturer_name']."</td>-->
								<td class='whiteBG rptText13 alignLeft' style='text-align:left;width:110px;'>".$row1['vendor_name']."</td>
								<td class='whiteBG rptText13 alignLeft' style='text-align:left;width:110px;'>".$con_len_brand['brand_name']."</td>
								<td class='whiteBG rptText13 alignLeft' align='right' style='text-align:right;width:61px;'>".$batch_qry['prev_tot_qty']."</td>
								<td class='whiteBG rptText13 alignLeft' align='right' style='text-align:right;width:61px;'>".$batch_qry['in_fac_prev_qty']."</td>
								<td class='whiteBG rptText13 alignLeft' align='right' style='text-align:right;width:61px;'>".$batch_qry['in_item_quant']."</td>
								<td class='whiteBG rptText13 alignLeft' style='text-align:left;width:110px;'>".$reson_res['reason_name']."</td>
								<td class='whiteBG rptText13 alignLeft' style='width:110px;'>".$arrfacility[$itemDetails['facility']]."</td>
								<td class='whiteBG rptText13 alignLeft' style='width:30px;'>".$arrUsersTwoChar[$itemDetails['user_id']]."</td>
								</tr>";	
							}
							else
							{
								$html.="<tr>
								<td class='whiteBG rptText13 alignLeft'>".$a."</td>
								<td class='whiteBG rptText13 alignLeft'>".$row['upc_code']."</td>
								<td class='whiteBG rptText13 alignLeft'>".$row2['module_type_name']."</td>
								<td class='whiteBG rptText13 alignLeft'>".$row['name']."</td>
								<!--<td class='whiteBG rptText13 alignLeft'>".$row3['manufacturer_name']."</td>-->
								<td class='whiteBG rptText13 alignLeft'>".$row1['vendor_name']."</td>
								<td class='whiteBG rptText13 alignLeft'>".$row4['frame_source']."</td>
								<td class='whiteBG rptText13 alignRight'>".$batch_qry['prev_tot_qty']."</td>
								<td class='whiteBG rptText13 alignRight'>".$batch_qry['in_fac_prev_qty']."</td>
								<td class='whiteBG rptText13 alignRight'>".$batch_qry['in_item_quant']."</td>
								<td class='whiteBG rptText13 alignLeft'>".$reson_res['reason_name']."</td>
								<td class='whiteBG rptText13 alignLeft'>".$arrfacility[$itemDetails['facility']]."</td>
								<td class='whiteBG rptText13 alignLeft'>".$arrUsersTwoChar[$itemDetails['user_id']]."</td>
								</tr>";
								$htmlPDF.="<tr>
								<td class='whiteBG rptText13 alignLeft' style='text-align:left;width:30px;'>".$a."</td>
								<td class='whiteBG rptText13 alignLeft'  style='text-align:left;width:100px;'>".$row['upc_code']."</td>
								<td class='whiteBG rptText13 alignLeft' style='text-align:left;width:100px;'>".$row2['module_type_name']."</td>
								<td class='whiteBG rptText13 alignLeft' style='text-align:left;width:110px;'>".$row['name']."</td>
								<!--<td class='whiteBG rptText13 alignLeft'>".$row3['manufacturer_name']."</td>-->
								<td class='whiteBG rptText13 alignLeft' style='text-align:left;width:110px;'>".$row1['vendor_name']."</td>
								<td class='whiteBG rptText13 alignLeft' style='text-align:left;width:110px;'>".$row4['frame_source']."</td>
								<td class='whiteBG rptText13 alignLeft' align='right' style='text-align:right;width:61px;'>".$batch_qry['prev_tot_qty']."</td>
								<td class='whiteBG rptText13 alignLeft' align='right' style='text-align:right;width:61px;'>".$batch_qry['in_fac_prev_qty']."</td>
								<td class='whiteBG rptText13 alignLeft' align='right' style='text-align:right;width:61px;'>".$batch_qry['in_item_quant']."</td>
								<td class='whiteBG rptText13 alignLeft' style='text-align:left;width:110px;'>".$reson_res['reason_name']."</td>
								<td class='whiteBG rptText13 alignLeft' style='width:110px;'>".$arrfacility[$itemDetails['facility']]."</td>
								<td class='whiteBG rptText13 alignLeft' style='width:30px;'>".$arrUsersTwoChar[$itemDetails['user_id']]."</td>
								</tr>";	
							}
						$a++;
						}
					}
				}
				$subTotAmt =0;
				$totQry+=$itemDetails['return_qty'];
			}
		}
		//Final html
		$reportHtml.='
			<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
				<tr style="height:25px;">
					<td class="reportHeadBG1" style="text-align:left; width:30px;">Sr.No.</td>
					<td class="reportHeadBG1" style="text-align:left; width:100px;">UPC Code</td>
					<td class="reportHeadBG1" style="text-align:left; width:150px;">Product Type</td>
					<td class="reportHeadBG1" style="text-align:left; width:150px;">Product Name</td>
					<!--<td class="reportHeadBG1 alignTop" style="text-align:center; width:184px;">Manufacturer</td>-->
					<td class="reportHeadBG1" style="text-align:left; width:120px;">Vendor</td>
					<td class="reportHeadBG1" style="text-align:left; width:120px;">Brand</td>
					<td class="reportHeadBG1" style="text-align:right; width:70px;">T.Qty.</td>
					<td class="reportHeadBG1" style="text-align:right; width:70px;">F.Qty</td>
					<td class="reportHeadBG1" style="text-align:right; width:70px;">Rec.Qty.</td>
					<td class="reportHeadBG1" style="text-align:left; width:100px;">Reason</td>
					<td class="reportHeadBG1" style="text-align:left; width:100px;">Facility</td>
					<td class="reportHeadBG1" style="text-align:left; width:30px;">Opr.</td>
				</tr>'.$html.'
			</table>';
				$reportHtmlPDF.=''.$htmlPDF.'
				<tr>
					<td colspan="13" class="whiteBG pt2 pb2">
						<div style="border-bottom:1px solid #0E87CA;"></div>
					</td>
				</tr>';
		}
	$css = '
	<style type="text/css">
	.reportHeadBG{ font-family: Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; background-color:#4684ab;color:#FFF;}
	.reportHeadBG1{ font-family: Arial, Helvetica, sans-serif; font-size:13px; font-weight:bold; background-color:#D9EDF8;}
	.reportTitle { font-family: Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; background-color:#7B7B7B; color:#FFF }
	.rptText13 { font-family: Arial, Helvetica, sans-serif; font-size:13px; }
	.rptText13b { font-family: Arial, Helvetica, sans-serif; font-size:13px; font-weight:bold; }
	.rptText12b { font-family: Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; }		
	.alignMiddle{vertical-align:middle;} .alignTop{vertical-align:top;}  .alignBottom{vertical-align:bottom;}
	.alignLeft{text-align:left;} .alignRight{text-align:right;} .alignCenter{text-align:center;} .alignJustify{text-align:justify;}
	#data_tab tr:nth-child(odd){background-color:#ececec;}	
	#data_tab1{border:1px solid #CCC;border-collapse:collapse;}	
	.whiteBG{ background:#fff; } 
	</style>';		

	//SELECTION TO SHOW
	if(empty($_POST['faclity'])===false){ $arrFacility=explode(',', $_POST['faclity']); }
	if(empty($_POST['operators'])===false){ $opera=explode(',', $_POST['operators']); }
	$facility='All';
	$ope='All';
	$facility=(count($arrFacility)>1)? 'Multi' : ((count($arrFacility)=='1')? ucfirst($arrfacility[$showFacility]): $facility);
	$ope=(count($opera)>1)? 'Multi' : ((count($opera)=='1')? ucfirst($userfac[$operator]): $ope);
	
	//FINAL HTML
	$module_ar1=$arra=array();
		foreach($rec_array as $key=>$value)
		{
			$data2.="<tr><td style='text-align:left;padding-left:5px;'>".ucwords($key)."</td>";
			$data3.="<tr><td style='text-align:left;padding-left:5px;'>".ucwords($key)."</td>";
			foreach($value as $key1=>$val1)
			{
				foreach($val1 as $key2=>$val2)
				{
					$module_ar1[$key][$key2][]=array_sum($val2);
				}
			}
			
			$data2.="<td style='text-align:right;'>".array_sum($module_ar1[$key]['tot'])."</td>";
			$data2.="<td style='text-align:right;'>".array_sum($module_ar1[$key]['fac'])."</td>";
			$data2.="<td style='text-align:right;'>".array_sum($module_ar1[$key]['rec'])."</td>";
			$data2.="<td style='text-align:right;padding-right:5px;'>".currency_symbol(true).number_format(array_sum($module_ar1[$key]['tot_pric']),2,".","")."</td>";
			$data2.="<td style='text-align:right;padding-right:5px;'>".currency_symbol(true).number_format(array_sum($module_ar1[$key]['rec_pric']),2,".","")."</td>";
			$data2.="<td style='text-align:right;padding-right:5px;'>".currency_symbol(true).number_format(array_sum($module_ar1[$key]['rem_pric']),2,".","")."</td>";
			$data2.="</tr>";
			
			$data3.="<td style='text-align:right;'>".array_sum($module_ar1[$key]['tot'])."</td>";
			$data3.="<td style='text-align:right;'>".array_sum($module_ar1[$key]['fac'])."</td>";
			$data3.="<td style='text-align:right;'>".array_sum($module_ar1[$key]['rec'])."</td>";
			$data3.="<td style='text-align:right;'>".currency_symbol(true).number_format(array_sum($module_ar1[$key]['tot_pric']),2,".","")."</td>";
			$data3.="<td style='text-align:right;padding-right:5px;'>".currency_symbol(true).number_format(array_sum($module_ar1[$key]['rec_pric']),2,".","")."</td>";
			$data3.="<td style='text-align:right;padding-right:5px;'>".currency_symbol(true).number_format(array_sum($module_ar1[$key]['rem_pric']),2,".","")."</td>";
			$data3.="</tr>";
		}
		
		
	$finalReportHtml='
	<table width="100%" cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
	<tr style="height:20px;">
		<td style="text-align:left;" class="reportHeadBG" width="220px">&nbsp;Stock Reconciliation Report</td>
		<td style="text-align:left;" class="reportHeadBG" width="220px" >&nbsp;Facility : '.$facility.'</td>
		<td style="text-align:left;" class="reportHeadBG" width="220px" >&nbsp;Operator : '.$ope.'</td>
		<td style="text-align:left;" class="reportHeadBG" width="auto">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
	</tr>
	<tr style="height:20px;">
		<td colspan="4" class="reportHeadBG">&nbsp;Date : '.$selExpDat.'</td>
	</tr>
	</table>'.$reportHtml.'
	<table style="margin-top:20px;">
		<tr>
			<th style="background:#D9EDF8;text-align:left;padding-left:5px;" colspan="8">Summary</th>
		</tr>
		<tr>
			<th class="reportHeadBG1" style="width:115px;padding:5px;text-align:left;">Product Type</th>
			<th class="reportHeadBG1" style="width:70px;padding:5px;">Int. Qty.</th>
			<th class="reportHeadBG1" style="width:70px;padding:5px;">F. Qty.</th>
			<th class="reportHeadBG1" style="width:70px;padding:5px;">Rec. Qty.</th>
			<th class="reportHeadBG1" style="width:100px;padding:5px;">Int. Amount</th>
			<th class="reportHeadBG1" style="width:100px;padding:5px;">Adj. Amount</th>
			<th class="reportHeadBG1" style="width:130px;padding:5px;">Total Rec. Amount</th>
		</tr>'.$data2.'
	</table>';

	if(count($arrMainDetail)>0)
	{
		//FINAL PDF
		$finalReportHtmlPDF.='<page backtop="13mm" backbottom="5mm">
			<page_header>
				<table style="border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
					<tr style="height:20px;">
						<td style="text-align:left;" class="reportHeadBG" colspan=3>&nbsp;Stock Reconciliation Report</td>
						<td style="text-align:left;" class="reportHeadBG" colspan=2>&nbsp;Facility : '.$facility.'</td>
						<td style="text-align:left;" class="reportHeadBG" colspan=3>&nbsp;Operator : '.$ope.'</td>
						<td style="text-align:right;" align="right" class="reportHeadBG" colspan=4>&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
					</tr>
					<tr style="height:20px;">
						<td colspan=12 class="reportHeadBG">Date : '.$selExpDat.'</td>
					</tr>
					<tr>
						<td class="reportHeadBG1 alignTop" style="text-align:center;width:30px;">Sr.#</td>
						<td class="reportHeadBG1 alignTop" style="text-align:center;width:100px;">UPC Code</td>
						<td class="reportHeadBG1 alignTop" style="text-align:center;width:100px;">Product Type</td>
						<td class="reportHeadBG1 alignTop" style="text-align:center;width:110px;">Product Name</td>
						<!--td class="reportHeadBG1 alignTop" style="text-align:center;width:160px;">Manufacturer</td>-->
						<td class="reportHeadBG1 alignTop" style="text-align:center;width:110px;">Vendor</td>
						<td class="reportHeadBG1 alignTop" style="text-align:center;width:110px;">Brand</td>
						<td class="reportHeadBG1 alignTop" style="text-align:center;width:61px;">T.Qty.</td>
						<td class="reportHeadBG1 alignTop" style="text-align:center;width:61px;">Qty.F</td>
						<td class="reportHeadBG1 alignTop" style="text-align:center;width:61px;">Qty.</td>
						<td class="reportHeadBG1 alignTop" style="text-align:center;width:110px;">Reason</td>
						<td class="reportHeadBG1 alignTop" style="text-align:center;width:110px;">Facility</td>
						<td class="reportHeadBG1 alignTop" style="text-align:center;width:30px;">Opr.</td>
					</tr>
				</table>
			</page_header>
				<table style="border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
					'.$reportHtmlPDF.'
				</table>
				<table style="margin-top:20px;">
					<tr>
						<th style="background:#4684ab;text-align:left;padding:5px;color:#FFF;" colspan="8">Summary</th>
					</tr>
					<tr>
						<th class="reportHeadBG1" style="width:95px;padding:5px;text-align:left;">Product Type</th>
						<th class="reportHeadBG1" style="width:50px;padding:5px;text-align:right;">Int. Qty.</th>
						<th class="reportHeadBG1" style="width:50px;padding:5px;text-align:right;">F. Qty.</th>
						<th class="reportHeadBG1" style="width:60px;padding:5px;text-align:right;">Rec. Qty.</th>
						<th class="reportHeadBG1" style="width:90px;padding:5px;text-align:right;">Int. Amount</th>
						<th class="reportHeadBG1" style="width:90px;padding:5px;text-align:right;">Adj. Amount</th>
						<th class="reportHeadBG1" style="width:130px;padding:5px;text-align:right;">Total Rec. Amount</th>
					</tr>'.$data3.'
				</table>
			</page>';
	}
	$pdfText = $css.$finalReportHtmlPDF;
	file_put_contents('../../library/new_html2pdf/stock_rec_reprt.html',$pdfText);
}?>
<html>
<head>
<title></title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script>
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
<style>
.alignRight {
	padding-right: 5px;
}
#data_tab tr:nth-child(odd) {
	background: #ececec !important;
}
</style>
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
<form name="stockreconcileResult" action="stock_rec_report.php" method="post">
  <input type="hidden" name="faclity" id="faclity" value="" />
  <input type="hidden" name="operators" id="operators" value="" />
  <input type="hidden" name="reason" id="reason" value="" />
  <input type="hidden" name="date_from" id="date_from" value="" />
  <input type="hidden" name="date_to" id="date_to" value="" />
  <input type="hidden" name="generateRpt" id="generateRpt" value="yes" />
</form>
<?php if(isset($_REQUEST['print'])) { ?>
<script>
var url = '<?php echo $GLOBALS['WEB_PATH'];?>/library/new_html2pdf/createPdf.php?op=l&file_name=../../library/new_html2pdf/stock_rec_reprt';
window.location.href = url;
</script>
<?php } ?>
</body>
</html>