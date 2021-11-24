<?php
$without_pat = "yes";
require_once("reports_header.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
include_once($GLOBALS['fileroot'] . '/library/classes/scheduler/appt_page_functions.php');
ob_start();
$months = $_REQUEST['months'];
$years = $_REQUEST['years'];
$recall_date_from = $_REQUEST['recall_date_from'];
$recall_date_to = $_REQUEST['recall_date_to'];
$last_nam_frm=$_REQUEST['last_nam_frm'];
$last_nam_to=$_REQUEST['last_nam_to'];
$pat_id_imp=$_REQUEST['pat_id_imp'];
$excSentEmail=$_REQUEST['excSentEmail'];

$is_json = ( isset($_REQUEST['json']) ) ? (bool)$_REQUEST['json'] : false;

//setting margins
$sql_margin=imw_query("select * from create_margins where margin_type='recall'");
$row_margin=imw_fetch_array($sql_margin);
$top_margin = $row_margin['top_margin'];
$bottom_margin = $row_margin['bottom_margin'];
$line_margin = $row_margin['line_margin'];
$coloumn_margin = $row_margin['column_margin'];

$arrMonth = array("01" => "January","02" => "Febraury","03" => "March","04" => "April","05" => "May","06" => "June","07" => "July","08" => "August","09" => "September","10" => "October","11" => "November","12" => "December",);

$where = "WHERE 1=1";
$where1 = "WHERE 1=1";
$recalldate = date("Y-m-d",mktime(0,0,0,date("m")+$months,date("d"),date("y")));
if($months != "" || $years != ""){
	if($months != ""){
		$where .= " AND MONTH(recalldate) = '".$months."' ";
		$where1 .= " AND MONTH(appt_date) = '".$months."' ";
	}
	if($years != ""){
		$where .= " AND YEAR(recalldate) = '".$years."'";
		$where1.= " AND YEAR(appt_date) = '".$years."'";
	}
}else{
	if($recall_date_from !='' && $recall_date_to!=''){
		$df = explode("-", $recall_date_from);
		$dateFrom = $df[2].'-'.$df[0].'-'.$df[1];
		$dt = explode("-", $recall_date_to);
		$dateTo = $dt[2].'-'.$dt[0].'-'.$dt[1];
		$where .= " AND (recalldate BETWEEN '".$dateFrom."' AND '".$dateTo."')";
		$where1 .= " AND (appt_date BETWEEN '".$dateFrom."' AND '".$dateTo."')";
	}
}
if($last_nam_frm){
	$last_whr=" and (pd.lname between '$last_nam_frm' and '$last_nam_to' or pd.lname like '$last_nam_to%')";
}
if($pat_id_imp!=''){
	$un_app_whr=" and par.patient_id in($pat_id_imp)";
}

$hipaaEmail='';
if($excSentEmail)
{
	//get ids with sent mail in date range
	$query=imw_query("select appt_id as recall_id from exclude_sent_email $where1 and report='Recalls'");
	if(mysql_num_rows($query)>=1)
	{
		while($data=imw_fetch_object($query))
		{
			$recalIds[]=$data->recall_id;
		}	
		$recalIdStr=implode(',',$recalIds);
	}
	
	if($recalIdStr)
	{
		$hipaaEmail .= " AND par.id NOT IN($recalIdStr)";	
	}
}	

//flag to set create label/house call on off (by default its off)
//$qry = "select par.* from patient_app_recall as par,patient_data as pd $where and par.patient_id=pd.id and pd.hipaa_mail=1 $last_whr $un_app_whr group by patient_id ORDER BY lname asc,fname asc";
$qry = "select par.* from patient_app_recall as par,patient_data as pd $where and par.patient_id=pd.id AND par.descriptions != 'MUR_PATCH' and pd.hipaa_mail='1'  $last_whr $un_app_whr $hipaaEmail group by patient_id ORDER BY lname asc,fname asc";

$res = @imw_query($qry);
$num = @imw_num_rows($res);


$json_data = array();

if($num > 0){
	$strHTML = '
			<style>
				.tb_heading{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#000000;
					background-color:#FE8944;
				}
				.text_b{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#FFFFFF;
					background-color:#4684AB;
				}
				.text{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					background-color:#FFFFFF;
				}
			</style>
			';
	$strHTML .= '<page backtop="'.$top_margin.'mm" backbottom="5mm">';
	/*$strHTML .= '<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align: center;	width: 100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>';*/


	$strHTML .= "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr>";
	$i = 1;
	$j = 1;
	while($rw = @imw_fetch_array($res)){
		$patientid=$rw['patient_id'];
		$patient_deta=patient_data($patientid);
		
		if($patient_deta[1] != "" && $patient_deta[2] != ""){
			$address = $patient_deta[1].", ".$patient_deta[2];
		}else{
			$address = $patient_deta[1];
		}
		if($num > 1){
			$width = "100%";
		}else{
			$width = "100%";
		}
		if($address==''){
			$address="&nbsp;";
		}
		if($patient_deta[3]==''){
			$patient_deta[3]="&nbsp;";
		}
		
		if($is_json){
		
			$temp_json = array();
			$temp_json['name'] = $patient_deta[9];
			$temp_json['address1'] = $address;
			$temp_json['address2'] = $patient_deta[3];
			
			array_push($json_data, $temp_json);
			continue;
		}
		
		$strHTML .= "
				<td valign=\"top\" width=\"".$coloumn_margin."\" style=\"margion:0px;\">
					<table align=\"left\"  border=\"0\" rules=\"rows\"  cellpadding=\"2\" cellspacing=\"0\" width=\"".$width."\">
						<tr>
							<td width=\"230\" align=\"left\" valign=\"middle\" class=\"text_13b\">".$patient_deta[9]."</TD>
						</tr>
						<tr><td height=\"".$line_margin."\"></td></tr>
						<tr>
							<td width=\"230\" valign=\"middle\" align=\"left\" class=\"text_13\">";
				
						if($address <> ""){ 
							$strHTML .= substr($address,0,30);
						}
						
						$strHTML .= "
						</TD>
						</tr>
						<tr><td height=\"".$line_margin."\"></td></tr>
						<TR>
							<td width=\"230\" valign=\"middle\" align=\"left\" class=\"text_13\">".$patient_deta[3]."</TD>
						</tr>
						<tr><td height=\"".$line_margin."\"></td></tr>
					</table>
				</td>";
		$break = '';
		if($i%3 == 0){
			if($i%30 == 0){
				$break = "</tr><tr><td></td></tr><tr>";
			}else{
				$break = "</tr><tr><td height=\"".$bottom_margin."\"></td></tr><tr>";
			}
		}
		if($j == $num){
			$break = "</tr>";
		}
		$strHTML .= $break;
		$i++;
		$j++; 			
	}
	$strHTML .= "</table></page>";
}

if($is_json){
	print json_encode($json_data);
	exit;
}
$bl_printed = true;
$file_location = '';
if(trim($strHTML) != ""){
	$file_location = write_html($strHTML);
}else{
	$bl_printed = false;
}
if($bl_printed == false){
	?>
    <html>
		<body>		
		<table align="center" width="100%" border="0" cellpadding="1" cellspacing="1">
			<tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
				<td align="center">No Record Found.</td>
			</tr>
		</table>
        </body>
    </html>
	<?php
}else{
	?>
	<form name="printFrmALLPDF" action="<?php echo $GLOBALS['webroot'] ?>/library/html_to_pdf/createPdf.php" method="POST" >
	<input type="hidden" name="page" value="1.3" >
	<input type="hidden" name="onePage" value="false">
	<input type="hidden" name="op" value="p" >
	<input type="hidden" name="font_size" value="7.5">
	<input type="hidden" name="file_location" value="<?php echo $file_location; ?>">
</form>	
	<script>
		document.printFrmALLPDF.submit();
	</script>
	<?php
	}
?>