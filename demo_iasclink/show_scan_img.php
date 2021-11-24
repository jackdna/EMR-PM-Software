<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
include_once("common/conDb.php");
$delete_id=$_REQUEST['delete_id'];
$id=$_REQUEST['id'];
$val = $_REQUEST['val'];
$type = $_REQUEST['type'];
$othrFieldsQry=', patient_id, waiting_id, type, ins_caseid ';
if($delete_id){
	$val = $val == '' ? '' : 2;
	if($type == 'scan_card'){
		$qry = "select scan_card$val $othrFieldsQry from iolink_insurance_scan_documents 
				where scan_documents_id = $delete_id";
		$scan_cardRes = imw_query($qry);
		$scan_cardNumRw = imw_num_rows($scan_cardRes);
		$scan_card_detail = imw_fetch_array($scan_cardRes);
		
		
		//$scan_card_detail = ManageData::getQryRes($qry);
		$qry = "update iolink_insurance_scan_documents set scan_card$val = '',scan_label$val = ''
				where scan_documents_id = $delete_id";
	}
	else{
		$qry = "select scan_card$val $othrFieldsQry from insurance_data where id = $delete_id";
		$scan_cardRes = imw_query($qry);
		$scan_cardNumRw = imw_num_rows($scan_cardRes);
		$scan_card_detail = imw_fetch_array($scan_cardRes);
		
		//$scan_card_detail = ManageData::getQryRes($qry);
		$qry = "update insurance_data set scan_card$val = '',scan_label$val = ''
				where id = $delete_id";
	}
	imw_query($qry);
	//ManageData::query($qry);
	$imgUrl = realpath('imedic_uploaddir'.$scan_card_detail['scan_card'.$val]);	
	if(is_dir($imgUrl) == '' && file_exists($imgUrl) != ''){
		unlink($imgUrl);
		print '
			<script type="text/javascript">
				window.close();
			</script>';
		
	}
}
if($id){
	$val = $val == 1 ? '' : $val;
	
	if($type == 'scan_card'){
		$qry = "select scan_card$val $othrFieldsQry from iolink_insurance_scan_documents 
				where scan_documents_id = $id";
	}
	else{
		$qry = "select scan_card$val $othrFieldsQry from insurance_data where id = $id";
	}
	$scan_cardRes = imw_query($qry);
	$scan_cardNumRw = imw_num_rows($scan_cardRes);
	$scan_card_detail = @imw_fetch_array($scan_cardRes);
	
	//$scan_card_detail = ManageData::getQryRes($qry);
	$imgUrl = $scan_card_detail['scan_card'.$val];
	$img_path = 'imedic_uploaddir'.$imgUrl;
	$img_real_path = realpath('imedic_uploaddir'.$imgUrl);
	if(!is_dir($img_path)) {
		$img_name = substr($img_path,strrpos($img_path,'/')+1);
	}
	$img_path_info = pathinfo($img_path);
	$ext = $img_path_info["extension"];
	
	
}
if($delete_id || $id){
	$type 		= $scan_card_detail['type'];
	$patient_id = $scan_card_detail['patient_id'];
	$waiting_id = $scan_card_detail['waiting_id'];
	$ins_caseid = $scan_card_detail['ins_caseid'];
}
?>
<html>
<head>
<title>imwemr : Insurance Scanned Documents</title>
<LINK HREF="css/style_surgery.css" TYPE="text/css" REL="stylesheet">
<LINK HREF="css/sfdc_header.css" TYPE="text/css" REL="stylesheet">
<script type="text/javascript" src="js/jsFunction.js"></script>
<script type="text/javascript">
	function close_window(){
		var pgNme			=	'insurance_primary.php';
		var type			=	'<?php echo $type;?>';
		var patient_id		=	'<?php echo $patient_id;?>';
		var waiting_id		=	'<?php echo $waiting_id;?>';
		var currentCaseid	=	'<?php echo $ins_caseid;?>';
		pgNme=pgNme+'?pid='+patient_id;
		pgNme=pgNme+'&wid='+waiting_id;
		pgNme=pgNme+'&type='+type;
		pgNme=pgNme+'&insCaseIdList='+currentCaseid;
		window.opener.location.href = pgNme;
		return false;
	}
	
	function confirm_msg(){
		if(confirm('Sure ! you want to delete document ?'))
			return true;
		else
			return false;
	}
</script>
</head>
<body rightmargin="0" class="body_c" leftmargin="0" topmargin="0" onUnload="close_window();">
<table width="100%" align="center" bgcolor="#999999" cellpadding="1" cellspacing="1" border="0">
	<tr bgcolor="#FFFFFF"><td colspan="3">&nbsp;</td></tr>
	<?php 
	if(file_exists($img_real_path)){
	?>
	<tr bgcolor="#FFFFFF">
		<td class="text_10b" width="120" nowrap>Image Name : </td>
		<td class="text_10" nowrap><?php print $img_name; ?></td>
		<td width="600" class="text_10b"><a href="<?php print basename($_SERVER['PHP_SELF']);?>?delete_id=<?php print $id; ?>&val=<?php print $val ?>&type=<?php print $type; ?>" onClick="return confirm_msg();" class="link_slid_right">Delete</a></td>
	</tr>
	<!--<tr bgcolor="#FFFFFF">
		<td colspan="3"><img src="<?php print $img_path; ?>"  /></td>
	</tr>-->
	<?php
		if(trim(strtolower($ext))=='pdf') {
	?>
			<tr bgcolor="#FFFFFF">
				<td colspan="3"><iframe name="ins_card_frm_pdf" id="ins_card_frm_pdf" src="<?php print $img_path; ?>" style="width:99%; height:600px;"></iframe></td>
			</tr>
    
    <?php		
		}else {
	?>
			<tr bgcolor="#FFFFFF">
				<td colspan="3"><img src="<?php print $img_path; ?>"  /></td>
			</tr>
		
	<?php
		
		}
	}
	else{
	?>
	<tr bgcolor="#FFFFFF" class="text_10b">
		<td colspan="3" align="center">Image does not exists.</td>
	</tr>
	<?php
	}
	?>
	<tr bgcolor="#FFFFFF" class="text_10b">
		<td colspan="3" align="center"><a href="#" onClick="MM_swapImage('closeButton','','images/close_onclick1.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('closeButton','','images/close_hover.gif',1)"><img src="images/close.gif" name="closeButton" id="closeButton" border="0"  alt="Close" onClick="window.close();"/></a></td>
	</tr>
</table>
</body>
</html>