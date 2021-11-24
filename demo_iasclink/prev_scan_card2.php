<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
if(!$scan_card_val) {  $scan_card_val=$_REQUEST['scan_card_val'];}
//--- Delete Scanned Documents ------
$delId = $_REQUEST['delId'];

//--- Delete Scanned Documents ------
if($delId){
	$qry = "select $scan_card_val from iolink_insurance_scan_documents 
			where scan_documents_id = $delId";
	//$cardDetail = ManageData::getQryRes($qry);
	$cardRes = imw_query($qry);
	$cardNumRw = imw_num_rows($cardRes);
	$cardDetail = imw_fetch_array($cardRes);
	
	$scan_label = $scan_card_val == 'scan_card' ? 'scan_label': 'scan_label2';
	$qry = "update iolink_insurance_scan_documents set $scan_card_val = '',
				$scan_label = '' where scan_documents_id = $delId";
	//ManageData::query($qry);
	imw_query($qry);
	if($cardNumRw>0) {
		chmod('imedic_uploaddir'.$cardDetail[$scan_card_val],0777);
		@unlink('imedic_uploaddir'.$cardDetail[$scan_card_val]);
	}
}
$tp=$_REQUEST['ty'];
$isRecordExists = $_REQUEST['isRecordExists'];
$ins_caseid = $_REQUEST['currentCaseid'];
$patient_id = $_REQUEST['patient_id'];
$vquery = "select * from iolink_insurance_scan_documents 
			where type = '$tp' and ins_caseid = $ins_caseid
			and patient_id = $patient_id and document_status = '0'";
$vsql = imw_query($vquery);
$indDetails = imw_fetch_assoc($vsql);
?>
<html>
<head>
<style>
.body_c {
	scrollbar-face-color:#408bc4;
	scrollbar-shadow-color:#afefff;
	scrollbar-highlight-color:#afefff;
	scrollbar-3dlight-color:#000000;
	scrollbar-darkshadow-color:#006399;
	scrollbar-track-color:#bfd3e6;
	scrollbar-arrow-color:#FFFFFF;
	margin-top:0;
	margin-left:0;
	margin-right:0;
}
</style>


	<script>
		//Show Full Image
		function showFullImage(obj)
		{	
			var divLargeImage = obj.getElementsByTagName("DIV");
			//alert(divLargeImage.item(1).id);
			divLargeImage.item(1).style.visibility = "visible";
			divLargeImage.item(1).style.display = "block";
		}
		
		//Hide Image
		function hideImage(obj)
		{
			obj.style.visibility = "hidden";
			obj.style.display = "none";
		}	
		function delImg(id,scan_card){
			if(confirm('Sure ! You Want To Delete Scan Document')){
				document.getElementById("delId").value = id;
				document.getElementById("scan_card_val").value = scan_card;
				document.delFrm.submit();
			}
		}
		function close_window()
		{
		
			top.close_window();
		}
	</script>
</head>
<form name="delFrm" action="" method="post">
	<input type="hidden" name="delId" value="">
	<input type="hidden" name="scan_card_val" value="">
</form>
<body  bgcolor="#ffffff" class="body_c" topmargin='0' rightmargin='0' leftmargin='0' marginwidth='0' marginheight='0'>
<table width="98%" height="100%" border="0" cellpadding="2" cellspacing="0">
	<tr>            
		<td>
			<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
				<tr height="25">
					<td colspan="3" align="left" valign="top">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr height="25px">
								<td width="6px" valign="bottom"><img src="images/lt_conrer1.gif"></td>
								<td width="4%" valign="bottom" background="images/rt_conrer1.gif">
									&nbsp;<img src="images/win_demo.gif" align="bottom">									
								</td>
								<td width="100%" class="text_10" valign="middle" background="images/menu_bg.gif">
									<b>Previous Scaned Insurance Card</b>
								</td>
								<td width="6px" valign="bottom"><img src="images/rt_conrer1.gif"></td>
							</tr>
						</table>	
					</td>
				</tr>
				<tr height="100%">
					<td width="2" background="images/border_left.jpg"></td>		
					<td align="left" valign="top">
						<table width="80%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td valign="top">
									<?php
										//---- If scaned documents Exists ---
										$cardscan_date = $objManageData->getDateFormat($indDetails['created_date']);
										if($indDetails['scan_card']){
											$scan_card_img = 'imedic_uploaddir'.$indDetails['scan_card'];
											if(file_exists($scan_card_img)){
												$scan_card_img_size = getimagesize($scan_card_img);
												if($scan_card_img_size[0]>200){
													$newSize = $objManageData->imageResize($scan_card_img_size[0],$scan_card_img_size[1],200);
													$scanImage = '<img src="'.$scan_card_img.'" title="Scanned Document" '.$newSize.'>';
												}
												else{
													$scanImage = '<img src="'.$scan_card_img.'" title="Scanned Document">';
												}
											}
									?>
									<table width="100%" align="center" border="0">
										<tr>
											<td align="center" class="text_10">Scan Card 1</td>											
										</tr>
										<tr>
											<td align="center"><?php print $scanImage; ?></td>											
										</tr>
										<tr>
											<td align="center" class="text_10b">Created date</td>
										</tr>
										<tr>
											<td align="center" class="text_10"><?php print $cardscan_date; ?></td>											
										</tr>
										<tr>
											<td class="text_10" align="center">
												<?php print $indDetails['scan_label']; ?>
											</td>
										</tr>
										<tr>
											<td align="center" class="text_10b"><a href="javascript:delImg('<?php print $indDetails['scan_documents_id']; ?>','scan_card');" onClick="" class="link_slid_right">Delete</a></td>
										</tr>
									</table>
									<?php
										}
										else{
									?>
									<table width="100%" align="center" border="0">
										<tr>
											<td align="center">Scan Card 1</td>											
										</tr>
										<tr>
											<td class="text_10" align="center">
												No scaned documents !
											</td>
										</tr>
									</table>
									<?php
										}
									?>
								</td>
								<td valign="top">
									<?php
										//---- If scaned documents Exists ---
										$cardscan_date = $objManageData->getDateFormat($indDetails['cardscan_date']);
										if($indDetails['scan_card2']){
											$scan_card_img2 = 'imedic_uploaddir'.$indDetails['scan_card2'];
											if(file_exists($scan_card_img2)){
												$scan_card_img_size2 = getimagesize($scan_card_img2);
												if($scan_card_img_size2[0]>200){
													$newSize2 = $objManageData->imageResize($scan_card_img_size2[0],$scan_card_img_size2[1],200);
													$scanImage2 = '<img src="'.$scan_card_img2.'" title="Scanned Document" '.$newSize2.'>';
												}
												else{
													$scanImage2 = '<img src="'.$scan_card_img2.'" title="Scanned Document">';
												}
											}
									?>
									<table width="100%" align="center" border="0">
										<tr>
											<td align="center" class="text_10">Scan Card 2</td>											
										</tr>
										<tr>
											<td align="center"><?php print $scanImage2; ?></td>											
										</tr>
										<tr>
											<td align="center" class="text_10b">Created date</td>
										</tr>
										<tr>
											<td align="center" class="text_10"><?php print $cardscan_date; ?></td>											
										</tr>
										<tr>
											<td class="text_10" align="center">
												<?php print $indDetails['scan_label2']; ?>
											</td>
										</tr>
										<tr>
											<td align="center" class="text_10b"><a href="javascript:delImg('<?php print $indDetails['scan_documents_id']; ?>','scan_card2');" onClick="" class="link_slid_right">Delete</a></td>
										</tr>
									</table>
									<?php
										}
										else{
									?>
									<table width="100%" align="center" border="0">
										<tr>
											<td align="center">Scan Card 2</td>											
										</tr>
										<tr>
											<td class="text_10" align="center">
												No scaned documents !
											</td>
										</tr>
									</table>
									<?php
										}
									?>
								</td>
							</tr>
						</table>
						<table width="100%" align="center" border="0">
														<tr>
								<td align="center">
									<a href="#" onClick="MM_swapImage('closeButton','','images/close_onclick1.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('closeButton','','images/close_hover.gif',1)"><img src="images/close.gif" name="closeButton" id="closeButton" border="0"  alt="Close" onClick="close_window();"/></a>
								</td>
							</tr>															 
						</table>				  
					</td>
					<td width="2" background="images/border_left.jpg"></td>
				</tr>
				<tr>
					<td width="2"><img src="images/edge_left.jpg" width="8"></td>
					<td width="100%" background="images/bottom_line.jpg"></td>
					<td width="2"><img src="images/edge_right.jpg" width="8"></td>
				</tr>							 
			</table>
		</td>
	</tr>	
</table>
</body>
</html>