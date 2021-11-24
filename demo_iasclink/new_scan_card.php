<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<LINK HREF="css/style_surgery.css" TYPE="text/css" REL="stylesheet">
<script type="text/javascript" src="js/jsFunction.js"></script>
<?php	
session_start();
include_once("common/conDb.php");
//$fdr_pat_img="../patient_access/patient_photos/";
$pid = $_REQUEST['patient_id'];
$userauthorized = $_SESSION['iolink_loginUserId'];
if(!$surgeryCenterWebrootDirectoryName) { $surgeryCenterWebrootDirectoryName=$surgeryCenterDirectoryName;	}
if(!$iolinkWebrootDirectoryName) 		{ $iolinkWebrootDirectoryName=$iolinkDirectoryName;					}

//--- Get Patient Details --------

$type=$_REQUEST['type'];
$isRecordExists = $_REQUEST['isRecordExists'];
$ins_caseid = $_REQUEST['currentCaseid'];
$patient_id = $_REQUEST['patient_id'];
$url = parse_url($_SERVER['HTTP_REFERER']);
$GLOBALS['php_server'] = $url['scheme'].'://'.$url['host'].$phpServerPort.$web_root;
if($isRecordExists){
	$qry = "select scan_card,scan_card2 from insurance_data where id = $isRecordExists";	
}
else{
	$qry = "select scan_card,scan_card2 from iolink_insurance_scan_documents 
			where type = '$type' and ins_caseid = $ins_caseid
			and patient_id = $patient_id and document_status = '0'";
}
$qryId = imw_query($qry);
list($scan_card,$scan_card2) = imw_fetch_array($qryId);

if($_POST){
	$comment12 = $_POST['comments'];
	if($isRecordExists){
		$qry = "update insurance_data set
						 cardscan_operator='$userauthorized',
						 cardscan_date=NOW(),
						 cardscan_comments = '$comment12' 
						 where patient_id='$patient_id' && type='$type' && ins_caseid ='$ins_caseid'";
		$res = imw_query($qry) or die(imw_error());
		if($res){
			echo "<script>top.close_window();</script>";
		}
	}
	else {
		$qry = "update iolink_insurance_scan_documents set
					 cardscan_operator='$userauthorized',
					 cardscan_date=NOW(),
					 cardscan_comments = '$comment12'
					 where patient_id='$patient_id' && type='$type' && ins_caseid = '$ins_caseid'";
		$res = imw_query($qry) or die(imw_error());
		if($res){
			echo "<script>top.close_window();</script>";
		}
	}
}

if($isRecordExists){
	$selQry = "select DATE_FORMAT(cardscan_date,'%m-%d-%Y %h:%i:%s') AS crtDate,cardscan_comments from insurance_data where patient_id = '$patient_id' && type='".$type."' && ins_caseid = '$ins_caseid'";
	$resQry = imw_query($selQry) or die(imw_error());
	$rowQry = imw_fetch_array($resQry);
}
else {
	$selQry = "select DATE_FORMAT(cardscan_date,'%m-%d-%Y %h:%i:%s') AS crtDate,cardscan_comments from iolink_insurance_scan_documents where patient_id = '$patient_id' && type='".$type."' && ins_caseid = '$ins_caseid'";
	$resQry = imw_query($selQry) or die(imw_error());
	$rowQry = imw_fetch_array($resQry);
}
?>

<html>
<head>
<script>
	function close_window()
	{
		
		top.close_window();
	}
	
	
</script>
</head>
<body  bgcolor="#ffffff" class="body_c" topmargin=0 rightmargin=0 leftmargin=0 bottommargin=0 marginwidth=0 marginheight=0>
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td align="center" valign="top">
		</td>
	</tr>	     
	 <?php
	 	if($scan_card == '' || $scan_card2 == ''){
	 ?>
	<tr>
		<td align="center" valign="top">
			<table width="98%" border="0" height="100%" cellpadding="2" cellspacing="0">
				<tr>            
					<td>
						<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
							<tr height="25">
								<td colspan="3" align="left" valign="top">
								  <table width="100%" border="0" cellspacing="0" cellpadding="0">
									  <tr>
										<td width="6px" valign="bottom"><img src="images/lt_conrer1.gif"></td>
										<td width="4%" valign="bottom" background="images/menu_bg.gif">
											  &nbsp;<img src="images/win_demo.gif" align="bottom">									
										</td>
										<td width="100%" class="text_10" valign="middle" background="images/menu_bg.gif">
											<b>Scan New Insurance Card</b>
										</td>
										<td width="6px" valign="bottom"><img src="images/rt_conrer1.gif"></td>
									  </tr>
									</table>	
								</td>
							</tr>
							<tr height="100%">
								<td width="2" background="images/border_left.jpg"></td>		
								<td align="center" valign="top">
									<table width="80%" border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td width="80%" valign="top" align="left">
												<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff" class="text_10">
													 		
													 <tr>
															<td class="valignTop" >
																
																	<!--<applet code="scanDocs.class" codebase="common/demoApplet" 
																			archive="program.jar,JTwain.jar" width="600" height="400">
																			<param name="DOWNLOAD_URL" value="<?php echo $GLOBALS['php_server']."/".$iolinkWebrootDirectoryName."/common/demoApplet/AspriseJTwain.dll";?>">
																			<param name="DLL_NAME" value="AspriseJTwain.dll"> 
																			<param name="UPLOAD_URL" value="<?php echo $GLOBALS['php_server']."/".$iolinkWebrootDirectoryName."/upload_scan_card.php?imwemr=".session_id()."&method=upload&tp=$type&isRecordExists=$isRecordExists&patient_id=$patient_id&currentCaseid=$ins_caseid";?>"> 
																			<param name="UPLOAD_PARAM_NAME" value="file[]">
																			<param name="UPLOAD_EXTRA_PARAMS" value="A=B">
																			<param name="UPLOAD_OPEN_TARGET" value="_blank">
																	</applet>-->
																	<?php
																	echo "<script>autoScan='no';imgNme='ioilnk-ins';showDtm='no';multiScan='yes';no_of_scans=20;uploadScanURL = '".$GLOBALS['php_server']."/".$iolinkWebrootDirectoryName."/upload_scan_card.php?imwemr=".session_id()."&method=upload&tp=$type&isRecordExists=$isRecordExists&patient_id=$patient_id&currentCaseid=$ins_caseid';</script>";
                                                                    include_once("admin/scan_control.php");?>
															</td>		
													  	</tr> 																 
													</table>		
												</td>
											</tr>
										</table>			  
									</td>
									<td width="2" background="images/border_left.jpg"></td>
								</tr>
								<tr>
									<td colspan="3">
										<table width="100%" height="100%"  border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td width="8"><img src="images/edge_left.jpg" width="8"></td>
												<td width="100%" background="images/bottom_line.jpg"></td>
												<td width="8"><img src="images/edge_right.jpg" width="8"></td>
											</tr>
										</table>
									</td>
								</tr>							 							
						</table>
					</td>	
				</tr>
			</table>
		</td>
	</tr>
	<?php
	}
	else{
	?>
	<tr valign="top">
		<td align="center" class="text_10"> Please delete one document, to upload a new one
		</td>
	</tr>     	 
	<?php
	}
	?>
	<tr>
		<td align="center" valign="top" class="text_10">
            <form name="frm_new_scan" action="" method="post">
                <input type="hidden" value="<?php echo $type;?>" name="type">
                <input type="hidden" value="<?php echo $isRecordExists;?>" name="isRecordExists">
                <input type="hidden" value="<?php echo $patient_id;?>" name="patient_id">
                <input type="hidden" value="<?php echo $ins_caseid;?>" name="currentCaseid">
                <input type="hidden" value="<?php echo $_REQUEST['waiting_id'];?>" name="waiting_id">
			
            	<label style="size:30; position:relative; top:-15px;">Comment:&nbsp;</label> <textarea name="comments" class="body_c text_10" rows="2" cols="50"><?php echo $rowQry['cardscan_comments'];?></textarea><br />
			<tr align="center">
				<td>
					<a href="#" onClick="MM_swapImage('closeButton','','images/save_n_close_onclick1.jpg',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('closeButton','','images/save_n_close_hover1.jpg',1)"><span id="spanCloseBtnId"><img src="images/save_n_close.jpg" name="closeButton" id="closeButton" border="0"  alt="Close" /></span></a><!--onClick="document.frm_new_scan.submit();"-->
					
				</td>
			</tr>
			<?php if(($rowQry['crtDate'] != '00-00-0000 12:00:00')&& ($rowQry['crtDate'] != '')){?>
			  <tr align="center">
				<td class="text_10">
					Last Scan Date Time-:&nbsp;<?php echo $rowQry['crtDate']; ?>
				</td>
			</tr>	
			<?php }?>
            </form>
		</td>
	</tr>															 
</table>
<?php
if($scan_card == '' || $scan_card2 == ''){
}else{
?>
<script>
	document.getElementById('spanCloseBtnId').innerHTML = '<img src="images/save_n_close.jpg" name="closeButton" id="closeButton" border="0"  alt="Close" onClick="document.frm_new_scan.submit();" />';
</script>
<?php
}
?>
</body>
</html>
