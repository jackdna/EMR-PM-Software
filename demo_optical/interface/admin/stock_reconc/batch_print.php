<?php 
require_once(dirname('__FILE__')."/../../../config/config.php");

$upc_array=explode("," ,(rtrim($_REQUEST['upc'],",")));
$item_array=explode("," ,(rtrim($_REQUEST['items'],",")));
$batch_array=explode("," ,(rtrim($_REQUEST['batch'],",")));
$batch_id=$_REQUEST['batch_id'];
$data="";
$rec_array=array();
$rec_array2=array();
$data2="";$wholesal="";
$j=1;
$batch_status=imw_query("select * from in_batch_table where id=".$batch_id);
$batch_status=imw_fetch_array($batch_status);
$batch=json_decode($batch_status['user_detail'],true);
$savedate=date("m-d-Y",strtotime($batch[0]['date']));
$savetime=date("h:i:s",strtotime($batch[0]['date']));
$saved="Batch Saved On:".$savedate." ".$savetime;
foreach($batch as $b)
{
	if($b['action']=='updated')
	{
		$updtdate=date("m-d-Y",strtotime($b['date']));
		$updttime=date("h:i:s",strtotime($b['date']));
		$recon='Batch Reconciled On :'.$updtdate." ".$updttime;
	}	
}
$data.="<div style='width:100%;text-align:center;margin:auto;font-size:14px;font-weight:bold;background:#4684ab;font-family: Arial, Helvetica, sans-serif;float:left;margin:0 0 0px 0;color:#FFF;padding:2px 3px;'><table><tr><td style='width:350px;' align='left'>".$saved."</td><td style='width:350px;' align='center'>Batch Details</td><td style='width:350px;' align='right'>".$recon."</td>
</tr></table></div>";
$data.='<div style="width:100%;float:left;" ><table class="batch_table" border="1px" align="left">
      <thead>
        <tr>
          <th style="width:20px;" valign="middle" align="left">S.No</th>
          <th style="width:85px;" valign="middle" align="left">UPC Code</th>
          <th style="width:87px;" valign="middle" align="left">Product Type</th>
          <th style="width:122px;" valign="middle" align="left">Product Name</th>
          <!--<th style="width:75px;" valign="middle" align="center">Manufacturer</th>-->
          <th style="width:80px;" valign="middle" align="left">Vendor</th>
          <th style="width:120px;" valign="middle" align="left">Brand</th>
		  <th style="width:53px;" valign="middle" align="right">Tot. Qty.</th>
          <th style="width:53px;" valign="middle" align="right">Fac. Qty.</th>
          <th style="width:54px;" valign="middle" align="right">Rec. Qty.</th>
		  <th style="width:105px;" valign="middle" align="left">Modified By</th>
		  <th style="width:85px;" valign="middle" align="left">Reason</th>
        </tr>
      </thead><tbody>';

for($i=0;$i<count($upc_array);$i++)
{
	$query=imw_query("select * from in_item where id='".$item_array[$i]."'");
	$query_batch=imw_query('select * from in_batch_records where id='.$batch_array[$i].'');
	$row_u=imw_fetch_array($query_batch);
	$batch_table=imw_query("select * from in_batch_table where id=".$row_u['in_batch_id']);
	$batch_table=imw_fetch_array($batch_table);
	$user=imw_query("select lname,fname,id from users where id='".$batch_table['user_id']."'");
	$user=imw_fetch_array($user);
	if(imw_num_rows($query)>0)
	{
		while($row=imw_fetch_array($query))
	{
		$query1=imw_query("select * from in_vendor_details where id=".$row['vendor_id']."");
		$row1=imw_fetch_array($query1);
		$query2=imw_query("select  * from in_module_type where id=".$row['module_type_id']."");
		$row2=imw_fetch_array($query2);
		$query3=imw_query("select * from in_manufacturer_details where id=".$row['manufacturer_id']."");
		$row3=imw_fetch_array($query3);
		$query4=imw_query("select * from in_frame_sources where id=".$row['brand_id']."");
		$row4=imw_fetch_array($query4);
		$item_loc=imw_query("select * from in_item_loc_total where item_id=".$row['id']." and loc_id=".$_SESSION['pro_fac_id']."");
		$item_loc_row=imw_fetch_array($item_loc);
		
		if($batch_table['status']=='updated')
			{
				$qty=$row_u['in_item_quant']-$row_u['in_fac_prev_qty'];
				$rec_array[$row2['module_type_name']][$row_u['in_item_id']]['rec']=$qty;
				$rec_array[$row2['module_type_name']][$row_u['in_item_id']]['tot']=$row_u['prev_tot_qty'];
				$rec_array[$row2['module_type_name']][$row_u['in_item_id']]['fac']=$row_u['in_fac_prev_qty'];
				$rec_array[$row2['module_type_name']][$row_u['in_item_id']]['rec_pric']=($qty*$row['wholesale_cost']);
				$rec_array[$row2['module_type_name']][$row_u['in_item_id']]['tot_pric']=($row_u['prev_tot_qty']*$row['wholesale_cost']);
				$rec_array[$row2['module_type_name']][$row_u['in_item_id']]['rem_pric']=($rec_array[$row2['module_type_name']][$row_u['in_item_id']]['rec_pric'])+($rec_array[$row2['module_type_name']][$row_u['in_item_id']]['tot_pric']);
			}
	
		/***********************************bar code gen**********************************/		
			include_once('../../../library/bar_code/code128/code128.class.php');
			$barcode = new phpCode128("'".$row['upc_code']."'", 150, '', '');
			$barcode->setBorderWidth(0);
			$barcode->setBorderSpacing(0);
			$barcode->setPixelWidth(1);
			$barcode->setEanStyle(false);
			$barcode->setShowText(true);
			$barcode->setAutoAdjustFontSize(true);
			$barcode->setTextSpacing(10);
			
			if(!is_dir(dirname(__FILE__)."/../../../images/bar_codes")){
				mkdir(dirname(__FILE__)."/../../../images/bar_codes", 0777, true);
			}
			$name=dirname(__FILE__)."/../../../images/bar_codes/".$row['upc_code'].".png";
			$barcode->saveBarcode($name);
	/***********************************bar code gen**********************************/	
		
		
			$res_query=imw_query("select * from in_reason where del_status='0' and id=".$row_u['reason']."");
			$resul_query=imw_fetch_array($res_query);
			if($row2['module_type_name']=='contact lenses')
			{
				$con_len=imw_query("select * from in_contact_brand where id=".$row['brand_id']."");
				$con_len_brand=imw_fetch_array($con_len);
				$data.="<tr>
				<td id='sr_no' valign='middle' align='left' style='width:20px;'>".$j."</td>
				<td valign='middle' align='left' style='width:85px;'><!--<img src='../../images/bar_codes/".$row['upc_code'].".png' width='80px'>-->".$row['upc_code']."</td>
				<td valign='middle' align='left' style='width:87px;'>".$row2['module_type_name']."</td>
				<td valign='middle' align='left' style='width:122px;'>".$row['name']."</td>
				<!--<td valign='middle' align='left' style='width:75px;'>".$row3['manufacturer_name']."</td>-->
				<td valign='middle' align='left' style='solid;width:80px;'>".$row1['vendor_name']."</td>
				<td valign='middle' align='left' style='width:120px;'>".$con_len_brand['brand_name']."</td>
				<td valign='middle' align='right' style='width:53px;'>".$row_u['prev_tot_qty']."</td>
				<td valign='middle' align='right' style='width:53px;'>".$row_u['in_fac_prev_qty']."</td>
				<td valign='middle' align='right' style='width:54px;'>".$row_u['in_item_quant']."</td>
				<td valign='middle' align='left' style='width:105px;'>".($user['lname'].", ". $user['fname'])."</td>
				<td valign='middle' align='left' style='width:85px;'>".$resul_query['reason_name']."</td>
				</tr>";
			}
			else
			{
				$data.= "<tr>
				<td id='sr_no' valign='middle' align='left' style='width:20px;'>".$j."</td>
				<td valign='middle' align='left' style='width:85px;'><!--<img src='../../images/bar_codes/".$row['upc_code'].".png' width='80px'>-->".$row['upc_code']."</td>
				<td valign='middle' align='left' style='width:87px;'>".$row2['module_type_name']."</td>
				<td valign='middle' align='left' style='width:122px;'>".$row['name']."</td>
				<!--<td valign='middle' align='center' style='width:75px;'>".$row3['manufacturer_name']."</td>-->
				<td valign='middle' align='left' style='width:80px;'>".$row1['vendor_name']."</td>
				<td valign='middle' align='left' style='width:120px;'>".$row4['frame_source']."</td>
				<td valign='middle' align='right' style='width:53px;'>".$row_u['prev_tot_qty']."</td>
				<td valign='middle' align='right' style='width:53px;'>".$row_u['in_fac_prev_qty']."</td>
				<td valign='middle' align='right' style='width:54px;'>".$row_u['in_item_quant']."</td>
				<td valign='middle' align='left' style='width:105px;'>".($user['lname'].", ". $user['fname'])."</td>
				<td valign='middle' align='left' style='width:85px;'>".$resul_query['reason_name']."</td>
				</tr>";
			}
		
		}
	}$j++;
}
$data2="";
		$module_ar1=$arra=array();
		foreach($rec_array as $key=>$value)
		{
			$data2.="<tr><td style='text-align:left;padding-left:5px;'>".$key."</td>";
			foreach($value as $key1=>$val1)
			{
				foreach($val1 as $key2=>$val2)
				{
					$module_ar1[$key][$key2][]=$val2;
				}
			}
			
			$data2.="<td style='text-align:right;'>".array_sum($module_ar1[$key]['tot'])."</td>";
			$data2.="<td style='text-align:right;'>".array_sum($module_ar1[$key]['fac'])."</td>";
			$data2.="<td style='text-align:right;'>".array_sum($module_ar1[$key]['rec'])."</td>";
			$data2.="<td style='text-align:right;padding-right:5px;'>".number_format(array_sum($module_ar1[$key]['tot_pric']),2,".","")."</td>";
			$data2.="<td style='text-align:right;padding-right:5px;'>".number_format(array_sum($module_ar1[$key]['rec_pric']),2,".","")."</td>";
			$data2.="<td style='text-align:right;padding-right:5px;'>".number_format(array_sum($module_ar1[$key]['rem_pric']),2,".","")."</td>";
			$data2.="</tr>";
		}
		/*if($batch_table['status']=='updated' && empty($rec_array))
			{
				$data2.="<style>#start_stock{text-align:left;} #whl_amt{margin:-23px 0 0 160px !important;float: left;} #tot{width: 220px;float: left;border:none !important;} #end_st{width: 158px;float: left;} #tabl_sum{width:37% !important;}</style>";
				$data2.="<tr><th style='text-align:left;padding-left:5px;' id='whl_amt'>".$wholesal."</th></tr>";
			}
			else if($batch_table['status']=='updated' && !empty($rec_array))
			{
				$data2.="<tr><th></th><th></th><th rowspan='2' style='text-align:right;padding-right:5px;'>".$wholesal."</th></tr>";
			}
			$data2.="<tr><th>Reason</th><th>Quantity</th></tr>";
			foreach($rec_array as $key=>$value)
			{
				if($key==0)
				{
					$data2.="<tr><td></td>";
				}
				else
				{
				$reson_query=imw_query("select * from in_reason where id=".$key."");
				$reson_row=imw_fetch_array($reson_query);
				$data2.="<tr><td>".$reson_row['reason_name']."</td>";
				}
				$ar_sum+=array_sum($rec_array2[$key]);
				if(is_int(array_sum($rec_array2[$key])))
				{
					$data2.="<td>".array_sum($value)."</td><td style='text-align:right;padding-right:5px;'>".number_format(array_sum($rec_array2[$key]),2,".","")."</td>";
				}
				else
				{
					$data2.="<td>".array_sum($value)."</td><td style='text-align:right;padding-right:5px;'>".array_sum($rec_array2[$key])."</td>";
				}
				$data2.="</tr>";
				
			}
			$tot=$wholesal+$ar_sum;
			if(!is_int($tot))
			{
				$tot=number_format($tot,2,".","");
			}
			if($batch_table['status']=='updated' && empty($rec_array))
			{
				$data2.="<tr><th style='text-align:left;' id='end_st'>Ending Stock</th><td style='text-align:left;padding-left:5px;' id='tot'>".($tot)."</td></tr>";
			}
			else if($batch_table['status']=='updated' && !empty($rec_array))
			{
				$data2.="<tr><th colspan='2'>Ending Stock</th><td style='text-align:right;padding-right:5px;'>".($tot)."</td></tr>";
			}*/
$data.='</tbody></table>';
 	$data.='<div class="summary_div" style="float:left;margin-top:10px;">
        	<div class="listheading">Reconciliation Summary</div>
			<table id="rec_tab">
			<tr>
				<th style="width:100px;padding:5px;text-align:left;">Product Type</th>
				<th style="width:40px;padding:5px;text-align:right;">Int. Qty.</th>
				<th style="width:50px;padding:5px;text-align:right;">F. Qty.</th>
				<th style="width:50px;padding:5px;text-align:right;">Rec. Qty.</th>
				<th style="width:70px;padding:5px;text-align:right;">Int. Amount.</th>
				<th style="width:70px;padding:5px;text-align:right;">Adj. Amount</th>
				<th style="width:100px;padding:5px;text-align:right;">Total Rec. Amount</th>
			</tr>
			'.$data2.'
			</table>
        	</div></div>';
 

$data.='<!--<div style="width:99%;position:absolute;bottom:100;text-align:center;"><input type="button" value="Print" onclick="window.print();"></div>-->';
$css="
<style>
body{font-family:arial;}
.listheading{width:630px;text-align:left;font-size:14px;font-weight:bold;font-family: Arial, Helvetica, sans-serif;float:left;padding:5px 3px;background:#4684ab;color:#FFF;}
.batch_table{border-collapse: collapse;border:1px solid #CCC;width:50%;font-size:12px;}
.batch_table th{padding:5px;width:70px;background:#c6ebfe;font-family: Arial, Helvetica, sans-serif;}
.batch_table td{padding:5px;}
#rec_tab th{width:auto !important;}
td:nth-child(1){width:40px;text-align:center;}
td:nth-child(2){width:80px;text-align:center;}
td:nth-child(3){width:90px;text-align:center;}
td:nth-child(4){width:140px;text-align:center;}
td:nth-child(5){width:90px;text-align:center;}
td:nth-child(6){width:90px;text-align:center;}
td:nth-child(7){width:60px;text-align:center;}
td:nth-child(8){width:90px;text-align:center;}
td:nth-child(9){width:50px;text-align:center;}
</style>";
if(file_put_contents('../../../library/new_html2pdf/batch_data.html',($css.$data)))
{
	echo "1";
}
?>