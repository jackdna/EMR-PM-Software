<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
?>
<?php
/*
Purpose: This file provide Glucoma Log Section in Glucoma Flow Sheet.
Access Type : Direct
*/
$ignoreAuth = true;
include_once(dirname(__FILE__)."/../../interface/chart_notes/common/functions.php");
// Accessing the Global file
require_once "../../interface/globals.php";	
$patient=$_REQUEST['patient'];
?><!doctype html>
<html>
<head>
<title>Glaucoma Log </title>
<style type="text/css">
.inner_content_gfs #no_height table tbody{height:auto !important;min-height:auto !important;}

.wrap_t_style table{text-align:left;-moz-border-radius-bottomright: 5px;-webkit-border-bottom-right-radius: 5px;border-bottom-right-radius: 5px;-moz-border-radius-bottomleft: 5px;-webkit-border-bottom-left-radius: 5px;border-bottom-left-radius: 5px;border:1px solid #333;}
.wrap_t_style  caption{-moz-border-radius-topleft: 5px;-webkit-border-top-left-radius: 5px; border-top-left-radius: 5px;-moz-border-radius-topright: 5px;-webkit-border-top-right-radius: 5px;border-top-right-radius: 5px;border-right:1px solid #333;border-left:1px solid #333;border-top:1px solid #333;border-bottom:1px solid #333;}
.wrap_t_style  thead{padding:0px 0px;background:#f1f1f1;border-bottom:1px solid #333;}
.wrap_t_style  tbody{padding:0px 0;background:#fff;overflow-y:auto;max-height:100px !important;}
.wrap_t_style  tbody td{padding:8px 4px;border-right:1px solid #333;word-wrap:break-word;}
.wrap_t_style  tbody td:last-child{border-right:none !important;}
.wrap_t_style  thead th:last-child{border-right:none !important;}
.wrap_t_style  thead th{padding:8px 4px;border-right:1px solid #333;word-wrap:break-word;}

.wrap_t_style2 table{text-align:left;-moz-border-radius-bottomright: 5px;-webkit-border-bottom-right-radius: 5px;border-bottom-right-radius: 5px;-moz-border-radius-bottomleft: 5px;-webkit-border-bottom-left-radius: 5px;border-bottom-left-radius: 5px;border:1px solid #333;}
.wrap_t_style2  caption{-moz-border-radius-topleft: 5px;-webkit-border-top-left-radius: 5px; border-top-left-radius: 5px;-moz-border-radius-topright: 5px;-webkit-border-top-right-radius: 5px;border-top-right-radius: 5px;border-right:1px solid #333;border-left:1px solid #333;border-top:1px solid #333;border-bottom:1px solid #333;}
.wrap_t_style2  thead{padding:0px 0px;background:#f1f1f1;border-bottom:1px solid #333;}
.wrap_t_style2  tbody{padding:0px 0;background:#fff;}
.wrap_t_style2  tbody td{padding:8px 4px;border-right:1px solid #333;word-wrap:break-word;}
.wrap_t_style2  tbody td:last-child{border-right:none !important;}
.wrap_t_style2  thead th:last-child{border-right:none !important;}
.wrap_t_style2  thead th{padding:8px 4px;border-right:1px solid #333;word-wrap:break-word;}
.wrap_t_style2 tr{float:left;width:100%;}

.main_Wrap{width:100%;float:left;display:inline-block;min-height:768px;max-height:768px;overflow-y:auto;}
.ipad_wrap{margin:0 auto;display:block;width:1012px;}
.inner_ipad_wrap{width:100%;float:left;display:inline-block;background:#fff;min-height:500px;-webkit-border-radius: 7px;-moz-border-radius: 7px;border-radius: 7px;border:2px solid #333;font-family:"Verdana";}
.head_w_gfs{float:left;width:100%;display:inline-block;padding:10px 0;  text-align: center;border-bottom:1px solid #333;font-size:18px;color:#006699;font-weight:normal;}
.bg_gfs_gray{ background:#f1f1f1;}
.content_gfs, .inner_content_gfs  , .inner_content_head , .inner_col{float:left;width:100%;display:inline-block;}
.content_gfs{padding:20px 0;border-bottom:1px solid #333;margin-bottom:10px;}
.inner_content_gfs , .inner_content_head{padding-left:15px; padding-right:15px;}
.inner_content_gfs{padding-top:10px;}
.inner_content_head{padding-top::10px; padding-bottom:10px;font-weight:normal; font-size:18px;}
.col-1{float:left;width:58%;display:inline-block;min-height:440px;}
.border-sty tbody{-moz-border-radius-bottomright: 5px;-webkit-border-bottom-right-radius: 5px;border-bottom-right-radius: 5px;-moz-border-radius-bottomleft: 5px;-webkit-border-bottom-left-radius: 5px;border-bottom-left-radius: 5px;}
.label_left{float:left;width:134px;display:inline-block;font-weight:normal;color:#333;font-weight:bold;font-size:15px;line-height:34px;font-family:Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif;}
.col-2{float:left;width:39%;display:inline-block;min-height:440px;}
.right_content{float:left;width:75%;display:inline-block;}
.b_full , .right_inner21, .right_inner31 , .right_inner22, .right_inner32, .right_inner1 , .right_inner2 , .right_inner3{float:left;border:1px solid #333;  color:#333;font-weight:normal;min-height:24px;-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;padding:5px 4px;overflow:auto;max-height:24px;overflow-x:auto;}
.right_inner1{width:82px;margin-right:5px;}
.right_inner2, .right_inner3{width:142px;margin-right:5px;}
.right_inner21, .right_inner31{width:97px;margin-right:5px;}
.right_inner22, .right_inner32{width:30px;margin-right:5px;}
.min-h{min-height:90px !important;max-height:90px !important;}
.od_wrap{float:left;width:142px;margin-right:5px;color:#00992f ;margin: 10px 0 0 10px;font-weight:bold;}
.od_wrap.blue{color:#006699 !important;}
.inner_col{margin-top:6px;}
.inner_col:first-child{margin-top:0xp !important;}
.m_top_0{margin-top:0px !important;}
.b_full{width:397px;}
.inner_col-2{float:left;width:95%;display:inline-block;padding:8px 0 0 10px;}
.bg_white{background:#fff !important;padding:4px 4px ; margin:8px 0px 3px 10px;width:207px !important;border:1px solid #333;}
.clearfix{clear:both;width:100%;float:left;}
.img_check{margin-right:5px;float:left;width:28px;}
.inner_col-2 label{float:left;display:inline-block;line-height:23px;}
.b_half_wrap{float:left;width:33%; margin-right:3px;  margin-left: 5px;}
.b_half{margin-left:10px;float:left;border:1px solid #333; ; color:#333;font-weight:normal;min-height:24px;-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;padding:5px 4px;   width: 63px;}
.wrap_table_1{margin:0 auto;width:95%;display:block;min-height:20px;background:#fff;}
.padding_0{padding:0px !important;}

.data_box {	background:#FFF;
	border:#CCC 1px solid
}
.data_box_pad {	background:#FFF;
	border:#CCC 1px solid;
	padding:0px 5px
}
</style>
</head>

<body style="background-color:#D9E4F2; padding:0px; margin:0px">

<div id="main_container" class="main_container">
   
    <?php 
	$surg_qry=imw_query("select title,begdate,sites from lists where pid='".$patient."' and type='6'");
	while($surg_row=imw_fetch_array($surg_qry)){
		$surg_arr[]=$surg_row;
	}
	$arrReturn = array();
	$gfs_qry = imw_query("SELECT 
						  c1.*,
						  c2.purge_status,		
						  SUBSTRING_INDEX(dateReading,'-',-1) AS strYear,
						  IF(dateReading REGEXP '^[0-9]{1,2}-[0-9]{1,2}-[0-9]{4}$',CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(dateReading,'-',2),'-',-1) AS SIGNED),0) AS strDate,
						  IF(dateReading REGEXP '^[0-9]{4}$',0,CAST(SUBSTRING_INDEX(dateReading,'-',1) AS SIGNED)) AS strMonth			  			  
						  FROM glaucoma_past_readings c1
						  LEFT JOIN chart_master_table c2 ON c2.id = c1.formId
						  WHERE patientId ='".$patient."' 
						  ORDER BY strYear DESC, strMonth DESC, strDate DESC, time_read_mil DESC, c1.id DESC")or die(imw_error());
	$i=0;
	$rec_num=imw_num_rows($gfs_qry);
	while($record=imw_fetch_array($gfs_qry)){
		$i++;
		$logId=$record["id"];
		$log_ta_time=$record["ta_time"];
		$log_tp_time=$record["tp_time"];
		$log_tx_time=$record["tx_time"];
		$logTaOd = $record['taOd'];
		$logTaOs = $record['taOs'];
		$logTpOd = $record['tpOd'];
		$logTpOs = $record['tpOs'];
		$logTxOd = $record['txOd'];
		$logTxOs = $record['txOs'];
		$gonio_od_summary = $record['gonio_od_summary'];
		$gonio_os_summary = $record['gonio_os_summary'];
		$fundus_od_cd_ratio=$record["fundus_od_cd_ratio"];
		$fundus_os_cd_ratio=$record["fundus_os_cd_ratio"];
		$fundus_od_summary=$record["fundus_od_summary"];
		$fundus_os_summary=$record["fundus_os_summary"];
		
		
		
		if($record['taOd']>0 || $record['taOs']>0){
			$pressure_od=$record['taOd'];
			$pressure_os=$record['taOs'];
		}else if($logTxOd>0 || $logTxOs>0){ 
			$pressure_od=$record['tpOd'];
			$pressure_os=$record['tpOs'];
		}else{
			$pressure_od=$record['txOd'];
			$pressure_os=$record['txOs'];
		}
		
		$test_data_arr=array();
		$test_qry=imw_query("select test_type from glaucoma_past_test where glaucoma_past_id='$logId'");
		while($test_row=imw_fetch_array($test_qry)){
			$test_data_arr[]=$test_row['test_type'];
		}
		$test_data_exp=strtoupper(implode(", ",$test_data_arr));
					
		
		$gonio_od_summary_data="";
		$gonio_os_summary_data="";
		$sle_od_summary_data="";
		$sle_os_summary_data="";
		$fundus_od_summary_data="";
		$fundus_os_summary_data="";
		$fundus_od_cd_ratio_data="";
		$fundus_os_cd_ratio_data="";
		$log_va_od_summary_data="";
		$log_va_os_summary_data="";
		$log_va_od_summary_col="";
		$log_va_os_summary_col="";
		$log_va_od_summary_col="";
		$log_va_os_summary_col="";
		$log_iop_od_summary_data="";
		$log_iop_os_summary_data="";
		$fundus_od_cd_ratio_line="";
		
		if($gonio_od_summary!=""){
			$gonio_od_summary_data="<tr><td class='text_10'><font color='blue'>OD: </font>".$gonio_od_summary."</td></tr>";
		}
		if($gonio_os_summary!=""){
			$gonio_os_summary_data="<tr><td class='text_10'><font color='green'>OS: </font>".$gonio_os_summary."</td></tr>";
		}
		
		if($fundus_od_cd_ratio!=""){
			$fundus_od_cd_ratio_data="<tr><td class='text_10'><font color='blue'>OD </font><font color='#000000'>CD : </font>".str_replace('C:D','',$fundus_od_cd_ratio)."</td></tr>";
		}
		if($fundus_os_cd_ratio!=""){
			$fundus_os_cd_ratio_data="<tr><td class='text_10'><font color='Green'>OS </font><font color='#000000'>CD : </font>".str_replace('C:D','',$fundus_os_cd_ratio)."</td></tr>";
		}
		if(($fundus_od_cd_ratio!="" || $fundus_os_cd_ratio!="") && ($fundus_od_summary!="" || $fundus_os_summary!="")){
			$fundus_od_cd_ratio_line="<tr><td style='border-bottom:1px solid #CCE6FF; width:100%; padding-top:2px;'></td></tr>";
		}
		if($fundus_od_summary!=""){
			$fundus_od_summary_data="<tr><td class='text_10'><font color='blue'>OD: </font>".$fundus_od_summary."</td></tr>";
		}
		if($fundus_os_summary!=""){
			$fundus_os_summary_data="<tr><td class='text_10'><font color='Green'>OS: </font>".$fundus_os_summary."</td></tr>";
		}
		
		if($record['va_od_summary']!=""){
			$log_va_od_summary_col= "<span style=\"color:blue;font-weight:bold;font-size:18px;\"> OD </span> 
									".$record['va_od_summary'];
		}
		$log_va_od_summary_data=$log_va_od_summary_col."&nbsp;";
		if($record['va_os_summary']!=""){
			$log_va_os_summary_col= "<span style=\"color:Green;font-weight:bold;font-size:18px;\"> OS </span> 
									".$record['va_os_summary'];
		}
		$log_va_os_summary_data=$log_va_os_summary_col."&nbsp;";
		
		//if($logTaOd>0 && $logTaOs>0){
		if($logTaOd>0 || $logTaOs>0){
			$pressure_od=$logTaOd;
			$pressure_os=$logTaOs;
			$iop_od_summary_val_arr['ta']=$logTaOd;
			$iop_os_summary_val_arr['ta']=$logTaOs;
		}else if($logTxOd>0 || $logTxOs>0){ //if($logTxOd>0 && $logTxOs>0){
			$pressure_od=$logTxOd;
			$pressure_os=$logTxOs;
		}else{
			$pressure_od=$logTpOd;
			$pressure_os=$logTpOs;
		}
		//if($logTxOd>0 && $logTxOs>0){
		if($logTxOd>0 || $logTxOs>0){
			$iop_od_summary_val_arr['tx']=$logTxOd;
			$iop_os_summary_val_arr['tx']=$logTxOs;
		}
		//if($logTpOd>0 && $logTpOs>0){
		if($logTpOd>0 || $logTpOs>0){
			$iop_od_summary_val_arr['tp']=$logTpOd;
			$iop_os_summary_val_arr['tp']=$logTpOs;
		}
		
		arsort($iop_od_summary_val_arr);
		arsort($iop_os_summary_val_arr);						
		$log_iop_od_summary=implode(',',$iop_od_summary_val_arr);
		$log_iop_os_summary=implode(',',$iop_os_summary_val_arr);	
		
		$iop_od_summary_val_arr['ta']=($iop_od_summary_val_arr['ta'])?$iop_od_summary_val_arr['ta']:'&nbsp;';
		$iop_od_summary_val_arr['tx']=($iop_od_summary_val_arr['tx'])?$iop_od_summary_val_arr['tx']:'&nbsp;';
		$iop_od_summary_val_arr['tp']=($iop_od_summary_val_arr['tp'])?$iop_od_summary_val_arr['tp']:'&nbsp;';
		
		$iop_os_summary_val_arr['ta']=($iop_os_summary_val_arr['ta'])?$iop_os_summary_val_arr['ta']:'&nbsp;';
		$iop_os_summary_val_arr['tx']=($iop_os_summary_val_arr['tx'])?$iop_os_summary_val_arr['tx']:'&nbsp;';
		$iop_os_summary_val_arr['tp']=($iop_os_summary_val_arr['tp'])?$iop_os_summary_val_arr['tp']:'&nbsp;';
		
		if($log_iop_od_summary!="" || $log_iop_os_summary!=""){
			$log_iop_od_summary_col= "				
				<div style=\"float:left;display:inline-block;margin-left:5px;\">
					<table style=\"float:none;width:221px;\">
					<tbody style=\"float:left;width:100%;\">	
					<tr style=\"float:left;width:100%;display:inline-block;border-bottom:1px solid #333;\">
						<th style=\"width:45px;border-right:1px solid #333;\">&nbsp;&nbsp;   </th>
						<td style=\"width:45px;border-right:1px solid #333;\">T<sub>A</sub> </td>
						<td style=\"width:45px;\">T<sub>X</sub></td>
						<td style=\"width:45px;\">T<sub>P</sub>  </td>
					</tr>
					<tr style=\"float:left;width:100%;display:inline-block;border-bottom:1px solid #333;\">
						<th style=\"color:#006699;font-size:16px;width:60px;border-right:1px solid #333;\"> OD  </th>
						<td style=\"font-size:16px;width:60px;border-right:1px solid #333;\">".$iop_od_summary_val_arr['ta']." </td>
						<td style=\"width:60px;\">".$iop_od_summary_val_arr['tx']."</td>
						<td style=\"width:60px;\">".$iop_od_summary_val_arr['tp']."</td>
					</tr>
					 <tr style=\"float:left;width:100%;display:inline-block;\">
						<th style=\"color:#00992f;font-size:16px;width:60px;border-right:1px solid #333;\"> OS</th>
						<td style=\"font-size:16px;width:60px;border-right:1px solid #333;\">".$iop_os_summary_val_arr['ta']."</td>
						<td style=\"font-size:16px;width:60px;\">".$iop_os_summary_val_arr['tx']."</td>
						<td style=\"font-size:16px;width:60px;\">".$iop_os_summary_val_arr['tp']."</td>
					</tr>
					</tbody>
					</table>	
			   </div>
				";
		}else{
			$log_iop_od_summary_col = '';
		}
		$log_iop_od_summary_data=$log_iop_od_summary_col;
	
		$log_time_mil_arr=array();
		$log_iop_time_data="";
		if($log_ta_time!=""){
			if(strpos($log_ta_time,'AM')>0){
				$log_ta_time_mil=str_replace(':','',str_replace('AM','',$log_ta_time));
			}else{
				$log_ta_time=str_replace('PM',' PM',$log_ta_time);
				$log_ta_time_mil = date('Hi', strtotime($log_ta_time));
			}
			//$log_time_mil_arr[]=$log_ta_time_mil;
			$log_time_mil_arr[]=$log_ta_time;
		}
		if($log_tp_time!=""){
			if(strpos($log_tp_time,'AM')>0){
				$log_tp_time_mil=str_replace(':','',str_replace('AM','',$log_tp_time));
			}else{
				$log_tp_time=str_replace('PM',' PM',$log_tp_time);
				$log_tp_time_mil = date('Hi', strtotime($log_tp_time));
			}
			//$log_time_mil_arr[]=$log_tp_time_mil;
			$log_time_mil_arr[]=$log_tp_time;
		}
		if($log_tx_time!=""){
			if(strpos($log_tx_time,'AM')>0){
				$log_tx_time_mil=str_replace(':','',str_replace('AM','',$log_tx_time));
			}else{
				$log_tx_time=str_replace('PM',' PM',$log_tx_time);
				$log_tx_time_mil = date('Hi', strtotime($log_tx_time));
			}
			$log_time_mil_arr[]=$log_tx_time;
		}
		sort($log_time_mil_arr);
		
		$log_iop_time_data=str_replace('PM',' PM',str_replace('AM',' AM',$log_time_mil_arr[0]))."&nbsp;";
		
		$row_date[$i][]=$record['dateReading'];
		$row_data[$i][]='<tr style="border-bottom:1px solid #333;font-size:14px;"><td style="">'.$record['dateReading'].'</td></tr>';//date
		
		$row_data[$i][]='<tr style="border-bottom:1px solid #333;"><td style="width:100%;word-wrap:break-word;display:inline-block;float:left; min-height:190px;max-height:190px;"> 
						<div style="float:left;display:inline-block;"> '.$log_va_od_summary_data.' '.$log_va_os_summary_data.'  
						<br/> '.$log_iop_time_data.' </div> '.$log_iop_od_summary_data.'</td></tr>';// od os reading
						
		$row_data[$i][]='<tr style="border-bottom:1px solid #333;font-size:14px;"> 
						<td style="width:100%;word-wrap:break-word;display:inline-block;float:left;">
						'.$record['ocular_med'].'</td></tr>';				//ocular med
		
		$row_data[$i][]='<tr style="font-size:14px;"> 
                         <td style="width:100%;word-wrap:break-word;display:inline-block;float:left;">
                         '.$record['medication'].'</td> </tr>';				//comments

	}
	
	
	
	echo '<Div class="content_gfs ">
			<Div class="inner_content_gfs padding_0" style="position:relative;">
				<Div class="inner_content_head" style="position:relative">
					<span style="border-bottom:1px solid #006699; color:#006699;padding-bottom:0px;"> Log </span>
					<img src="../../interface/chart_notes/graph_images/arrow_gray.png" style="width:10px; position:absolute;top:22px;left:26px;">
				</Div>
				<Div class="wrap_table_1 wrap_t_style2 " id="" style="margin-top:40px;">
				   <table style="width:150px;float:left;display:table-cell;height:316px;-webkit-border-radius: 0px;-moz-border-radius: 0px;border-radius: 0px;">
					 <thead style="line-height: 23.7px;float:left;">
							<tr style="width:100%;border-bottom:1px solid #333;float:left;">
								<th style="width:296px;">Date  </th>    
							</tr>	
							<tr style="width:100%;border-bottom:1px solid #333;float:left; line-height: 160px;">
								<th style="width:296px;"> Visual Acuity </th>    
							</tr>	
							<tr style="width:100%;border-bottom:1px solid #333;float:left;">
								<th style="width:296px;"> Ocular Med  </th>    
							</tr>	
							<tr style="float:left;width:100%;display:inline-block;">
								<th style="width:296px;"> Comment </th>    
							</tr>	
					 </thead>
				  </table>';
						  
	
	if(count($rec_num)>0){	
		for($f=0;$f<11;$f++){
			echo $field_data[0][$f];
			$skip_sur_data=array();
			
			for($k=0;$k<=$rec_num;$k++){
				echo '<div style="float: left; max-width: 810px; min-width:810px;display:inline-block; overflow-x:scroll ;white-space:nowrap;
                          overflow-x:auto;">
                              
                            <Table style="float:none;display:table-cell;width:400px;word-wrap:break-word;-webkit-border-radius: 0px;-moz-border-radius: 0px;border-radius: 0px; border-collapse:collapse;max-height:auto !important;">	
                                <tbody style="height:100%;line-height: 27.6px;  float: left;width:100%;background:#fff;">';
								
				echo $row_data[$k][$f];
				
				echo'</tbody> </Table> </div>  ';
				$sur_data="";
				
				if($f==0){
				
					$chart_date_arr=explode('-',$row_date[$k][$f]);
					$chart_date_exp=$chart_date_arr[2].'-'.$chart_date_arr[0].'-'.$chart_date_arr[1];
					$chart_date_arr2=explode('-',$row_date[$k+1][$f]);
					$chart_date_exp2=$chart_date_arr2[2].'-'.$chart_date_arr2[0].'-'.$chart_date_arr2[1];
					for($j=0;$j<count($surg_arr);$j++){
						if($surg_arr[$j]['begdate']!="0000-00-00" && !in_array($j,$skip_sur_data)){
							if($surg_arr[$j]['begdate']>$chart_date_exp2 && $surg_arr[$j]['begdate']<=$chart_date_exp){
								$begdate_arr=explode('-',$surg_arr[$j]['begdate']);
								$begdate_exp=$begdate_arr[1].'-'.$begdate_arr[2].'-'.substr($begdate_arr[0],2);
								//$begdate_exp = get_date_format($surg_arr[$j]['begdate'],'','',2);
								if($surg_arr[$j]['sites']==1){
									//OS
									$surg_site="OS";
								}
								if($surg_arr[$j]['sites']==2){
									//OD
									$surg_site="OD";
								}
								if($surg_arr[$j]['sites']==3){
									//OU
									$surg_site="OU";
								}
								if($surg_arr[$j]['sites']==4){
									//PO
									$surg_site="PO";
								}
								
								$sur_data="yes";
								$skip_sur_data[]=$j;
							}
						}
						
					}
					if($sur_data!=""){
					
						echo '<Div  style="display:table-cell;background:url(../../interface/chart_notes/graph_images/surgery_img.png) repeat-y center center;width:18px;padding:10px;">  </Div>';
					}
				}
			}
			 
			echo "</tr>";
		}
	}
	
	echo "<!-- overflow-scroll --> 
                        </Div>
                    </Div>
                 </Div>";
		
?>

    </div>

</body>
</html>
