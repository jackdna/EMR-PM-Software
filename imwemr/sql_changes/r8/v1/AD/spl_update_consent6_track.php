<?php 
set_time_limit(0);
$ignoreAuth = true;
include_once("../../../../config/globals.php");

$sql="
	SELECT 
		pcf.form_information_id,
		pcf.patient_id,
		pcf.form_created_date,
		pcf.consent_form_id,
		pcf.consent_form_name,
		pcf.consent_form_content_data,
		pcf.operator_id, 
		
		cp.consent_form_id as proc_consent_form_id, 
		cp.site as proc_site, 
		cp.exam_date as proc_exam_date,
		cp.form_id as proc_form_id
	FROM 
		patient_consent_form_information pcf
		INNER JOIN chart_procedures cp ON(cp.id = pcf.chart_procedure_id)
	WHERE 
		pcf.chart_procedure_id >0
	ORDER BY 
		pcf.form_created_date DESC ";

//echo $sql;
$res = imw_query($sql) or $msg_info[] = imw_error();
//$proc_site_arr = array("OD"=>"left,both, OS , OU ","OS"=>"right,both, OD , OU ","OU"=>"left,right, OS , OD ");
//$proc_site_arr = array("OD"=>"left eye,left upper,left lower,lul ,lll ,both eye","OS"=>"right eye,right upper,right lower,rul ,rll ,both eye","OU"=>"left eye,left upper,left lower,lul ,lll ,right eye,right upper,right lower,rul ,rll ");
$proc_site_arr = array("OD"=>"left eye ,left eye&nbsp;,left upper ,left upper&nbsp;,left lower ,left lower&nbsp;,lul ,lul&nbsp;,lll ,lll&nbsp;,both eye ,both eye&nbsp;,both eyes, OS , OU ",
					   "OS"=>"right eye ,right eye&nbsp;,right upper ,right upper&nbsp;,right lower ,right lower&nbsp;,rul ,rul&nbsp;,rll ,rll&nbsp;,both eye ,both eye&nbsp;,both eyes, OD , OU ",
					   );
$form_information_id_arr = array();
$show_data = '';
if( imw_num_rows($res)>0 )
{
	$show_data .='<table cellpadding="0" cellspacing="0" style="width:100%" align="left" border="1">';
	$show_data .='<tr>';
	$show_data .='<th style="width:10%; padding-left:2px;height:30px; text-align:left;">Patient Id</th>';
	$show_data .='<th style="width:10%; padding-left:2px;height:30px; text-align:left;">Pt. Consent Id</th>';
	$show_data .='<th style="width:10%; padding-left:2px;height:30px; text-align:left;">Pt. Procedure DOS</th>';
	$show_data .='<th style="width:10%; padding-left:2px;height:30px; text-align:left;">Pt. Procedure Site</th>';
	$show_data .='<th style="width:10%; padding-left:2px;height:30px; text-align:left;">Pt Site in Consent Search</th>';
	$show_data .='<th style="width:10%; padding-left:2px;height:30px; text-align:left;">Pt Consent Template Id</th>';
	$show_data .='<th style="width:10%; padding-left:2px;height:30px; text-align:left;">Pt. Procedur Consent Template Id</th>';
	$show_data .='</tr>';
	
	while($row = imw_fetch_assoc($res))
	{
		$form_information_id 		= $row["form_information_id"];
		$patient_id 				= $row["patient_id"];
		$operator_id 				= $row["operator_id"];
		$proc_site 					= $row["proc_site"];
		$consent_form_id 			= $row["consent_form_id"];
		$consent_form_name 			= $row["consent_form_name"];
		$consent_form_content_data	= $row["consent_form_content_data"];
		$form_id 					= $row["proc_form_id"];
		$proc_consent_form_id 		= $row["proc_consent_form_id"];		
		$form_created_date_format 	= date("d_m_y",strtotime($row["form_created_date"]));
		$proc_exam_date_format 		= date("m-d-Y H:i:s",strtotime($row["proc_exam_date"]));

		$proc_site_commma 	= $proc_site_arr[$proc_site];
		$proc_site_expl 	= explode(",",$proc_site_commma);
		$consent_form_content_data = strip_tags($consent_form_content_data,'<br>');
		
		$proc_consent_form_id_show=$proc_consent_form_id;
		if($consent_form_id != $proc_consent_form_id) {
			$proc_consent_form_id_show='<span style="color:#FF0000; font-weight:bold;">'.$proc_consent_form_id.'</span>';
		}
		foreach($proc_site_expl as $proc_site_val)
		{
			if(stripos($consent_form_content_data,$proc_site_val)!== false && !in_array($form_information_id, $form_information_id_arr))
			{//echo('<br><br>'.$proc_site.'@@'.$proc_site_val.$form_information_id.'<br><br>'.$consent_form_content_data);
				$form_information_id_arr[] = $form_information_id;
				//$info_arr[] = 'Pt. ID - '.$patient_id.' @@ Pt Consent ID - '.$form_information_id.' @@ Pt. Procedure DOS - '.$proc_exam_date_format.' @@ Pt. Procedure Site - '.$proc_site.' @@ Pt. Site in Consent search - '.$proc_site_val.' @@ Pt. Consent Template Id - '.$consent_form_id.' @@ Pt. Procedure Consent Template Id - '.$proc_consent_form_id_show;
				//echo '<br>'.$form_information_id;	
				$show_data .='<tr>';
				$show_data .='<td style="width:10%; padding-left:2px;height:30px; padding-left:2px;height:30px;">'.$patient_id.'</td>';
				$show_data .='<td style="width:10%; padding-left:2px;height:30px;">'.$form_information_id.'</td>';
				$show_data .='<td style="width:10%; padding-left:2px;height:30px;">'.$proc_exam_date_format.'</td>';
				$show_data .='<td style="width:10%; padding-left:2px;height:30px;">'.$proc_site.'</td>';
				$show_data .='<td style="width:10%; padding-left:2px;height:30px;">'.$proc_site_val.'</td>';
				$show_data .='<td style="width:10%; padding-left:2px;height:30px;">'.$consent_form_id.'</td>';
				$show_data .='<td style="width:10%; padding-left:2px;height:30px;">'.$proc_consent_form_id_show.'</td>';
				$show_data .='</tr>';
			}
		}
	}
	$show_data .='<tr>';
	$show_data .='<td colspan="7" style="padding-left:2px;height:30px; padding-left:2px;height:30px;"><b>Total Count: </b>'.count($form_information_id_arr).'</td>';
	$show_data .='</tr>';
	$show_data .='<tr>';
	$show_data .='<td colspan="7" style="padding-left:2px;height:30px; padding-left:2px;height:30px;"><b>Patient Consent Ids list: </b>'.implode(",",$form_information_id_arr).'</td>';
	$show_data .='</tr>';
	$show_data .= '</table>';
	
}
else
{
	echo "<br><br><b>No Records Found</b><br>";
}	
/* echo 'Total Count - '.count($form_information_id_arr);
echo '<br><br>Patient Consent Ids list -  '.implode(",",$form_information_id_arr);
echo '<br><br>'.implode("<br>",$info_arr);
 */
 

if(count($msg_info)>0)
{

    $msg_info[] = '<br><br><b>Update Consent 6 Track run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update Consent 6 Track run successfully!</b>";
    $color = "green";
}

?>
<html>
<head>
<title>Update Consent 6 Track</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br/><br/>",$msg_info));?></font><br><br>
    <?php
	echo $show_data;
	?>
</body>
</html>