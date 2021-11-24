<?php
$ignoreAuth = true;
include("../../../../config/globals.php");
$library_path = $GLOBALS['webroot'].'/library';
include_once($GLOBALS['fileroot']."/library/classes/SaveFile.php");
include_once($GLOBALS['fileroot']."/library/html_to_pdf/html2pdf.class.php");

$updir=substr(data_path(), 0, -1);
$srcdir=substr(data_path(1), 0, -1);
$CurrDt = date("Y_m_d");
$crRes=imw_query("CREATE  TABLE surgery_consent_filled_form_".$CurrDt."  LIKE surgery_consent_filled_form ");
$insRes=imw_query("INSERT INTO surgery_consent_filled_form_".$CurrDt."  (SELECT *  FROM surgery_consent_filled_form) ");
$qry = "select surgery_consent_id, patient_id, iolink_pdf_path from surgery_consent_filled_form where iolink_pdf_path !='' order by form_created_date desc, patient_id asc";
$res=imw_query($qry) or die(imw_error());
if(imw_num_rows($res)>0){
	$a = 0;
	$totalDone = 0;
	while($row =imw_fetch_assoc($res)) {
		$a++;
		$surgery_consent_id  	= $row["surgery_consent_id"];
		$patient_id  			= $row["patient_id"];
		$iolink_pdf_path  		= $row["iolink_pdf_path"];
		if(strstr($iolink_pdf_path,"addons/iOLink")){
			list($iolink_path1,$iolink_path2) = explode("addons/iOLink",$iolink_pdf_path);
			//echo '@@'.$iolink_path1.'@@'.$iolink_path2;
			$iolink_path1_new = $iolink_pdf_path_new = "";
			if(trim($iolink_path1)!="") {
				$iolink_path1_new = str_ireplace($iolink_path1,$updir.'/iOLink',$iolink_path1);
				$iolink_pdf_path_new = $iolink_path1_new.$iolink_path2;
				$savQry = "UPDATE surgery_consent_filled_form SET iolink_pdf_path = '".$iolink_pdf_path_new."' WHERE surgery_consent_id = '".$surgery_consent_id."' ";
				//echo('<br>'.$savQry);
				$savRes=imw_query($savQry) or die(imw_error());
				if($savRes) {
					$totalDone++;	
				}
				if(!file_exists($iolink_pdf_path_new) && file_exists($iolink_pdf_path)) {
					$file_content = file_get_contents($iolink_pdf_path);
					//$iolink_pdf_path_new = urlencode($iolink_pdf_path_new);
					file_put_contents($iolink_pdf_path_new,$file_content);
				}
				//if($a==2) {die('<br>hlo '.$savQry." @@ ".$iolink_pdf_path);}
			}
		}
	}
}
$msg_info[] = "<br><b>Release :<br> Update Surgery Consent Path Replaced Done. </b> ".$totalDone;

$color = "green";	

?>
<html>
<head>
<title>Update Surgery Consent Path Replaced</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br>
<br>
    <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
        <?php echo(implode("<br>",$msg_info));?>
    </font>
</body>
</html>