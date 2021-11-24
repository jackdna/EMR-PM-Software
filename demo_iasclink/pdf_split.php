<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
session_start();
$loginUser = $_SESSION['iolink_loginUserId'];
include_once("common/conDb.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgerycenter EMR</title>
<LINK HREF="css/style_surgery.css" TYPE="text/css" REL="stylesheet">
<script type="text/javascript" src="js/jquery.js"></script>	
<script type="text/javascript" src="js/actb.js"></script>
<script type="text/javascript" src="js/common.js"></script>	
<script>
function delPdfSplitFun(cntr) {
	//START CODE TO CHECK THE RECORD SELECTED TO DELETE
	var delPdfFiles=false;
	for(var i=1;i<=cntr;i++) {
		if(document.getElementById('chkbxPdf'+i)) {
			if(document.getElementById('chkbxPdf'+i).checked==true) {
				delPdfFiles=true;
			}
		}
	}
	//END CODE TO CHECK THE RECORD SELECTED TO DELETE
	
	if(delPdfFiles==true) {
		if(confirm('Delete Record(s)! Are you sure ?')) {
			if(document.getElementById('delPdfSplit')) {
				document.getElementById('delPdfSplit').value='yes';
				document.frmPdfSplit.submit();
			}
		}
	}else {
		alert('Please select record(s) to delete');
	}
}
function setPtInfoAutoFill(objNumber,obj){
	var strString=obj.value;
	if(strString!=""){
		var strArray=strString.split("-");
		if(document.getElementById("hidd_ptNmeListId"+objNumber)) {
			document.getElementById("hidd_ptNmeListId"+objNumber).value='';
			if(document.getElementById("hidd_ptNmeListId"+objNumber) && strArray[1] && strArray[2]){
				document.getElementById("hidd_ptNmeListId"+objNumber).value=strArray[1]+'-'+strArray[2];
			}
		}	
		if(document.getElementById("ptNmeListId"+objNumber) && strArray[0]){
			document.getElementById("ptNmeListId"+objNumber).value=strArray[0];
		}
	}
}	
function selAll(valu,cntr){
	var val=valu;
	var obj='';
	for(var i = 1;i<=cntr;i++){
		obj = document.getElementById('chkbxPdf'+i);
		if(obj) {
			if(val=='Select All'){
				obj.checked='true';
			}else if(val=='Unselect All'){
				obj.checked='';
			}
		}
	}
}

function dispHidOptTxtBox(ObjTdPtnameOptBoxId,ObjTdPtnameTxtBoxId,objPtnameOptId,objPtNmeListId,objHiddPtNmeListId) {
	if(objPtnameOptId) 		{ objPtnameOptId.value='';}
	if(objPtNmeListId) 		{ objPtNmeListId.value='';}
	if(objHiddPtNmeListId) 	{ objHiddPtNmeListId.value='';}
	
	if(ObjTdPtnameOptBoxId && ObjTdPtnameTxtBoxId) {
		
		if(ObjTdPtnameOptBoxId.style.display=='block') {
			ObjTdPtnameOptBoxId.style.display='none';
			ObjTdPtnameTxtBoxId.style.display='block';
		}else if(ObjTdPtnameTxtBoxId.style.display=='block') {
			ObjTdPtnameTxtBoxId.style.display='none';
			ObjTdPtnameOptBoxId.style.display='block';
		}
	}	
}
function KeyCheckexplode(evt,searchType,obj) {
	evt = (evt) ? evt : ((event) ? event : null);
	var evver = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null );
	var keynumber = evt.keyCode;
	var objVal='';
	if(keynumber==13){
		if(searchType = 'searchPdfPatient') {
			if(obj) {
				getPdfPtSearch(obj);
			}
		}
	}
}
function getPdfPtSearch(obj) {
	if(obj) {
		var objId = obj.id;
		var txtSearch = obj.value; 
		var height=600;
		window.open("pdf_split_search_patient.php?txtSearch="+txtSearch+"&objId="+objId,"pdfPatientSearchWindow","width=700,height="+height+",top=90,left=10,scrollbars=yes");
	}
}

//START FUNCTION TO COPY PATIENT-ID AND WAITING-D IN HIDDEN FIELD WHEN COPY PATIENT-NAME FROM ONE TEXTBOX TO OTHER
function copyPdfFun(chkCountr,obj) {
	var cpArr = new Array();
	var hidd_cpArr = new Array();
	var cp=hidd_cp='';
	var objId = obj.id;
	if(chkCountr) {
		for(var i=1;i<=chkCountr;i++) {
			if(document.getElementById('ptNmeListId'+i)) {
				if(objId!='ptNmeListId'+i) {
					if(document.getElementById('ptNmeListId'+i).value) {
						cp		= cp+'~'+document.getElementById('ptNmeListId'+i).value;
						hidd_cp	= hidd_cp+'~'+document.getElementById('hidd_ptNmeListId'+i).value;
					}
				}
			}
		}
		if(cp) {
			cpArr 		= cp.split('~');
			hidd_cpArr 	= hidd_cp.split('~');
			for(var j=0;j<cpArr.length;j++) {
				if(trim(obj.value)==trim(cpArr[j])) {
					if(document.getElementById("hidd_"+objId)){
						document.getElementById("hidd_"+objId).value=hidd_cpArr[j];
					}	
				}
			}
		}
	}
}
//END FUNCTION TO COPY PATIENT-ID AND WAITING-D IN HIDDEN FIELD WHEN COPY PATIENT-NAME FROM ONE TEXTBOX TO OTHER

$(document).ready(function(e) {
	$("#pdfSplitAjaxLoadId",top.document).hide();
});

</script>
<?php
//$path = getcwd()."\\";
$path = realpath(dirname(__FILE__));
$selDos=$_REQUEST['selDos'];
$spec= "
</head>
<body>";
include("common/link_new_file.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
include("common/iOLinkCommonFunction.php");
$msgRec				= '';
$practiceName 		= getPracticeName($loginUser,'Coordinator');
$coordinatorType 	= getCoordinatorType($loginUser);
	
	
	//START CODE TO DELETE PENDING PDF FILES
	if($_REQUEST['delPdfSplit']=='yes') {
		$hidd_pdfFileNmeArr 		= $_REQUEST['hidd_pdfFileNme'];
		$tempFilePath 				= $path.'/pdfSplit/'.$selDos."/";
		foreach($hidd_pdfFileNmeArr as $key=> $hidd_pdfFileNme) {
			$chkbxPdf 				= $_REQUEST['chkbxPdf'.($key+1)];
			if($chkbxPdf=='yes') {
				$msgRec='Record Deleted';
				//echo '<br>'.($key+1).'~~'.$tempFilePath.$hidd_pdfFileNme;
				unlink($tempFilePath.$hidd_pdfFileNme);
				
				$deleteFolderPath 	= $path.'/admin/pdfFiles/Deleted';
				if(!is_dir($deleteFolderPath)) {
					mkdir($deleteFolderPath, 0777);
				}
				rename($tempFilePath.$hidd_pdfFileNme,$deleteFolderPath.'/'.$pdfFileNme.".pdf"); //RENAME ON EXISTING FOLDER
				
			}
		}
	}
	//END CODE TO DELETE PENDING PDF FILES
	
	//START CODE TO SAVE RECORD
	if($_REQUEST['savePdfSplit']=='yes' && $_REQUEST['delPdfSplit']!='yes') {
		$hidd_pdfFileNmeArr 		= $_REQUEST['hidd_pdfFileNme'];
		$pdfFileNmeArr 				= $_REQUEST['pdfFileNme'];
		$ptNmeListArr 				= $_REQUEST['ptNmeList'];
		$folderNmeListArr 			= $_REQUEST['folderNmeList'];
		$surgeonNameArr				= $_REQUEST['surgeon_nameArr'];
		$tempFilePath 				= $path.'/pdfSplit/'.$selDos."/";
		$multiWtId = '';
		foreach($hidd_pdfFileNmeArr as $key=> $hidd_pdfFileNme) {
			$msgRec='Record Saved';
			//echo '<br>'.($key+1).'~~'.$chkbxPdf = $_REQUEST['chkbxPdf'.($key+1)];
			$pdfFileNme 			= $pdfFileNmeArr[$key];
			$ptNmeList 				= $ptNmeListArr[$key];
			$hidd_ptNmeListId		= $_REQUEST['hidd_ptNmeListId'.($key+1)];
			$folderNmeList 			= $folderNmeListArr[$key];

			$hidd_pdfFileNme 		= trim(str_ireplace('.pdf','',$hidd_pdfFileNme));
			$hidd_pdfFileNme 		= trim(str_ireplace('.','',$hidd_pdfFileNme));
			$pdfFileNme 			= trim(str_ireplace('.pdf','',$pdfFileNme));
			$pdfFileNme 			= trim(str_ireplace('.','',$pdfFileNme));
			
			
			if($hidd_pdfFileNme!=$pdfFileNme) {
				if(file_exists($tempFilePath.$hidd_pdfFileNme.".pdf")) {
					rename($tempFilePath.$hidd_pdfFileNme.".pdf",$tempFilePath.$pdfFileNme.".pdf"); //RENAME ON EXISTING FOLDER
				}
			
			}
			
			if($hidd_ptNmeListId!='' && $folderNmeList!='') {
				$pdfFileNme 		= trim(str_ireplace('.pdf','',$pdfFileNme)); //IF USER REPEAT MORE THAN ONCE
				$pdfFileNme 		= trim(str_ireplace('.','',$pdfFileNme)); //IF USER REPEAT MORE THAN ONCE
				$pdfFileNme			= $pdfFileNme.".pdf"; //APPLY ONLY AT THE END OF PDF FILE NAME
				
				$hidd_ptNmeListIdExlpode 	= explode('-',$hidd_ptNmeListId);
				$ptId 						= $hidd_ptNmeListIdExlpode[0];
				$wtId 						= $hidd_ptNmeListIdExlpode[1];
				if(!$multiWtId) {
					$multiWtId = $wtId;	
				}else {  
					$multiWtId .= ",".$wtId;	
				}
				$PSize 						= @filesize($tempFilePath.$pdfFileNme);
				$surgeonName 				= $surgeonNameArr[$wtId];
				unset($arrayRecord);
				$arrayRecord['image_type'] 				= 'application/pdf';
				$arrayRecord['document_name'] 			= $pdfFileNme;
				$arrayRecord['patient_id'] 				= $ptId;
				$arrayRecord['patient_in_waiting_id'] 	= $wtId;
				$arrayRecord['document_size'] 			= $PSize;
				$arrayRecord['scan_save_date_time'] 	= date('Y-m-d H:i:s');
				$arrayRecord['iolink_scan_folder_name'] = $folderNmeList;
				
				if(trim($PSize)) {
					$inserIdScanUpload = $objManageData->addRecords($arrayRecord, 'iolink_scan_consent');
					
					$newFolderPath 		= $path.'/admin/pdfFiles/'.$surgeonName;
					$newFolderPathSave 	= 'pdfFiles/'.$surgeonName;
	
					if(!is_dir($newFolderPath)) {
						mkdir($newFolderPath, 0777);
					}
					$newFilePath 		= $newFolderPath."/iolink_".$inserIdScanUpload.".pdf";
					$newFilePathSave 	= $newFolderPathSave."/iolink_".$inserIdScanUpload.".pdf";
					
					if(file_exists($tempFilePath.$pdfFileNme)) {
						rename($tempFilePath.$pdfFileNme,$newFilePath);
					}
					unset($arrayRecord);
					$arrayRecord['pdfFilePath'] = $newFilePathSave;
					$updtScanUpldTbl = $objManageData->updateRecords($arrayRecord, 'iolink_scan_consent', 'scan_consent_id', $inserIdScanUpload);
				}
			}
		}
		
		if($multiWtId) {//set orange color to syncronized patient.
			$updtSyncQry = "UPDATE patient_in_waiting_tbl SET iAscReSyncroStatus = 'yes' WHERE patient_in_waiting_id IN(".$multiWtId.") AND iAscSyncroStatus='Syncronized'";	
			$updtSyncRes = imw_query($updtSyncQry) or die(imw_error());
		}
		
		
	}
	//END CODE TO SAVE RECORD
	
	
	//START CODE TO GET PATIENT-NAME
	$AndUserPracticeNameQry="";
	if($coordinatorType!='Master') {
		$AndUserPracticeNameQry = getPracticeUser($practiceName,"AND","usr");
		//$AndUserPracticeNameQry = " AND usr.practiceName='".addslashes($practiceName)."' ";
	}
	
	$facQry = "";
	if(trim($_SESSION['iolink_iasc_facility_id'])) {
		$facQry = " AND wt.iasc_facility_id IN (".$_SESSION['iolink_iasc_facility_id'].") ";	
	}
	$ptInfoQry = "SELECT wt.patient_in_waiting_id,wt.patient_id,wt.surgeon_fname,wt.surgeon_mname,wt.surgeon_lname,DATE_FORMAT(wt.dos,'%m-%d-%Y') as dosShow, pt.patient_fname, pt.patient_mname, pt.patient_lname
				  FROM patient_in_waiting_tbl wt,patient_data_tbl pt,users usr 
				  WHERE  wt.dos='".$selDos."'
				  $facQry
				  AND wt.patient_status!='Canceled'
				  AND wt.patient_id=pt.patient_id
				  AND wt.surgeon_fname=usr.fname 
				  AND wt.surgeon_mname=usr.mname 
				  AND wt.surgeon_lname=usr.lname
				  AND usr.deleteStatus!='Yes'	
				  AND usr.user_type = 'Surgeon'
				  $AndUserPracticeNameQry
				  ORDER BY pt.patient_lname, pt.patient_fname 
				 ";
	$ptInfoRes 				= 	imw_query($ptInfoQry) or die(imw_error());			 
	$ptInfoNumRow 			= 	imw_num_rows($ptInfoRes);
	$patientInWaitingIdArr	=	array();
	$patient_idArr			=	array();
	$surgeon_nameArr		=	array();
	$patient_nameArr		=	array();
	$patient_dosArr			=	array();
	if($ptInfoNumRow>0) {
		while($ptInfoRow 	= imw_fetch_array($ptInfoRes)) {
			$patientInWaitingIdArr[] 	= $ptInfoRow['patient_in_waiting_id'];
			$wtId 					 	= $ptInfoRow['patient_in_waiting_id'];
			$patient_idArr[$wtId] 		= $ptInfoRow['patient_id'];
			$patient_fname 				= $ptInfoRow['patient_fname'];
			$patient_mname 				= $ptInfoRow['patient_mname'];
			$patient_lname 				= $ptInfoRow['patient_lname'];
			if($patient_mname) { 
				$patient_mname 			= ' '.$patient_mname;
			}
			$ptNme						= $patient_lname.', '.$patient_fname.$patient_mname;
			$ptNme = str_replace('-','',$ptNme); 
			$patient_nameArr[$wtId] 	= $patient_lname.', '.$patient_fname.$patient_mname;
			$typAhdPtInfoIns[$wtId]		= "'".addslashes(trim($ptNme).'-'.trim($ptInfoRow['patient_id']).'-'.trim($wtId))."'"; 
			$patient_dosArr[$wtId]		= $ptInfoRow['dosShow'];
			//START CODE TO HELP TO CREATE SURGON FOLDER WHILE SAVING THE RECORD
			$surgeon_fname 				= $ptInfoRow['surgeon_fname'];
			$surgeon_mname 				= $ptInfoRow['surgeon_mname'];
			$surgeon_lname 				= $ptInfoRow['surgeon_lname'];
			if($surgeon_mname){
				$surgeon_mname 			= ' '.$surgeon_mname;
			}
			$surgeon_nameArr[$wtId] 	= $surgeon_fname.$surgeon_mname.' '.$surgeon_lname;
			$surgeon_nameArr[$wtId] 	= str_ireplace(" ","_",$surgeon_nameArr[$wtId]);
			$surgeon_nameArr[$wtId] 	= str_ireplace(",","",$surgeon_nameArr[$wtId]);
			$surgeon_nameArr[$wtId] 	= str_ireplace("!","",$surgeon_nameArr[$wtId]);
			$surgeon_nameArr[$wtId] 	= str_ireplace("@","",$surgeon_nameArr[$wtId]);
			$surgeon_nameArr[$wtId] 	= str_ireplace("%","",$surgeon_nameArr[$wtId]);
			$surgeon_nameArr[$wtId] 	= str_ireplace("^","",$surgeon_nameArr[$wtId]);
			$surgeon_nameArr[$wtId] 	= str_ireplace("$","",$surgeon_nameArr[$wtId]);
			$surgeon_nameArr[$wtId] 	= str_ireplace("'","",$surgeon_nameArr[$wtId]);
			$surgeon_nameArr[$wtId] 	= str_ireplace("*","",$surgeon_nameArr[$wtId]);
			//END CODE TO HELP TO CREATE SURGON FOLDER WHILE SAVING THE RECORD
			
		}
		if(count($typAhdPtInfoIns)>0){
			$typAhdPtInfo=implode(',',$typAhdPtInfoIns);
		}
	}
	//END CODE TO GET PATIENT-NAME	
	
?>
	
		
		<script type="text/javascript">
		var typAhdPtInfo = "";
		<?php
			if($typAhdPtInfo!=""){
			?>		
			var typAhdPtInfo = new Array(<?php echo fnLineBrk($typAhdPtInfo); ?>);
			<?php
			}	
		?>	
		</script>	
		<form name="frmPdfSplit" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
			<?php 
			$filePathSplit ='pdfSplit/'.$selDos.'/';
			
			if ($handle = @opendir($filePathSplit)) {
				
				//START CODE TO GET COUNT OF ALL UPDALOED FILES
				$chkHandle = @opendir($filePathSplit);
				$chkCountr = 0;
				while (false !== ($chkFile = readdir($chkHandle))) {
					if ($chkFile != "." && $chkFile != "..") { $chkCountr++;}
				}
				//END CODE TO GET COUNT OF ALL UPDALOED FILES
			?>
				<div style="height:440px; border:0px solid; overflow:auto; overflow-x:hidden; ">
					
					<table class="table_pad_bdr alignCenter" style="border:none; width:95%;">
						<?php 
						$counter=1;
						
					   /* This is the correct way to loop over the directory. */
					   $arrFiles = array();
					   while (false !== ($file = readdir($handle))) {
						   $arrFiles[] = $file;
					   }
					   natsort($arrFiles);					   
						//while (false !== ($file = readdir($handle))) {
						foreach($arrFiles as $file){	
							if ($file != "." && $file != ".." && $file != "tmp" && !stristr($file,".txt")) {
								if($counter==1) {
							?>
									<tr class="text_10b" style="height:20px;">
										<td>
											<input type="hidden" name="selDos" value="<?php echo $selDos;?>">
											<input type="hidden" name="savePdfSplit" id="savePdfSplit" value="yes">
											<input type="hidden" name="delPdfSplit" id="delPdfSplit" value="">
											<?php
											foreach($patientInWaitingIdArr as $waitingId) {
											?>
												<input type="hidden" name="surgeon_nameArr[<?php echo $waitingId;?>]" value="<?php echo $surgeon_nameArr[$waitingId];?>">
											<?php
											}
											?>
										</td>
										<td colspan="2" style="width:50px;" >S.No</td>
										<td>PDF Name</td>
										<td>Patient Name</td>
										<td>Folder Name</td>
									</tr>
								<?php
								}
								?>
								<tr class="text_10" style="height:20px;">
									<td><input type="hidden" name="hidd_pdfFileNme[]" value="<?php echo $file;?>"></td>
									<td style="width:1px;"><?php echo($counter);?></td>
									<td><input type="checkbox" name="chkbxPdf<?php echo($counter);?>" id="chkbxPdf<?php echo($counter);?>" value="yes"></td>
									<td><img src="images/pdf_icon_small.png" style="cursor:pointer;" alt="<?php echo $file;?>" onClick="window.open('<?php echo $filePathSplit.$file;?>')"><input class="text_10" type="text" name="pdfFileNme[]" value="<?php echo $file;?>"></td>
									<td class="nowrap">
										<table class="table_pad_bdr" style="border:none;">
											<tr>
												<td id="TdPtnameOptBoxId<?php echo $counter;?>" style="display:block; ">
													<select name="ptNmeOptId<?php echo $counter;?>" id="ptNmeOptId<?php echo $counter;?>" class="text_10"  style="width:250px; " onChange="document.getElementById('hidd_ptNmeListId<?php echo $counter;?>').value=this.value;">
														<option value="">Select Patient</option>
														<?php
														foreach($patientInWaitingIdArr as $waitingId) {
															$patient_id 	= $patient_idArr[$waitingId];
															$patient_name	= $patient_nameArr[$waitingId];
															$patient_dos	= $patient_dosArr[$waitingId];
															$patient_dosShow= '';
															if($patient_dos && $patient_dos!='0000-00-00') { $patient_dosShow = ' - '.$patient_dos; }
															
														?>
														<option value="<?php echo $patient_id.'-'.$waitingId;?>"><?php echo $patient_name.$patient_dosShow;?></option>
														<?php
														}
														?>
													</select>
												</td>
												<td id="TdPtnameTxtBoxId<?php echo $counter;?>" style="display:none; ">
													<input type="text" style="width:250px; " name="ptNmeListId<?php echo $counter;?>" id="ptNmeListId<?php echo $counter;?>" onBlur="copyPdfFun('<?php echo $chkCountr;?>',this);" onKeyPress="KeyCheckexplode(event,'searchPdfPatient',this);"><!-- javascript: setPtInfoAutoFill('<?php //echo($counter);?>',this); -->
												</td>
												<td>
													<input type="hidden" name="hidd_ptNmeListId<?php echo $counter;?>" id="hidd_ptNmeListId<?php echo $counter;?>" onChange="javascript: setPtInfoAutoFill('<?php echo($counter);?>',this);">
													<img src="images/back_arrow.png" alt="Swap" style="cursor:pointer; " onClick="dispHidOptTxtBox(document.getElementById('TdPtnameOptBoxId<?php echo $counter;?>'),document.getElementById('TdPtnameTxtBoxId<?php echo $counter;?>'),document.getElementById('ptNmeOptId<?php echo $counter;?>'),document.getElementById('ptNmeListId<?php echo $counter;?>'),document.getElementById('hidd_ptNmeListId<?php echo $counter;?>'));">
												</td>
											</tr>
										</table>
										
										<script>	
										//new actb(document.getElementById('ptNmeListId<?php //echo($counter);?>'),typAhdPtInfo);	
										</script>	
									</td>
									
									<td>
										<select name="folderNmeList[]" class="text_10"  style="width:200px; ">
											<option value="">Select Folder</option>
											<option value="clinical">Clinical</option>
                                            <option value="consent">Consent</option>
											<option value="ekg">EKG</option>
                                            <option value="h&p">H&amp;P</option>
                                            <option value="healthQuest">Health Questionnaire</option>
                                            <option value="ocularHx">Ocular Hx</option>
                                            <option value="ptInfo">Patient Info</option>
											<option value="iol">IOL</option>
										</select>
									</td>
								</tr>	
						<?php 
								$counter++;
							}
						}
						
							?>
					</table>
				</div>	
				
				<table class="table_pad_bdr alignCenter" style="border:none;width:95%;">	
					<tr><td style="height:3px;">&nbsp;</td></tr>
					<tr class="text_10b" >
						<td class="alignCenter valignBottom">
							<?php if($counter>1) {?>
                            <a href="#" style="padding-right:20px;" onClick="MM_swapImage('selectButton','','images/select_click.gif',1);selAll('Select All','<?php echo $counter;?>');" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('selectButton','','images/select_hover.gif',1)"><img src="images/select.gif" id="selectButton" style="border:none;"  alt="Select"/></a>
                            <a href="#" style="padding-right:20px;" onClick="MM_swapImage('unselectButton','','images/unselect_click.gif',1);selAll('Unselect All','<?php echo $counter;?>');" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('unselectButton','','images/unselect_hover.gif',1)"><img src="images/unselect.gif" id="unselectButton" style="border:none;"  alt="Unselect"/></a>
                            <a href="#" style="padding-right:20px;"  onClick="MM_swapImage('savePdfBtn','','images/save_onclick1.jpg',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('savePdfBtn','','images/save_hover1.jpg',1)"><img src="images/save.jpg" style="border:none;" id="savePdfBtn" alt="save" onClick="document.frmPdfSplit.submit();"/></a>
							<a href="#" style="padding-right:20px;" onClick="MM_swapImage('deletePdfSplitSelected','','images/delete_selected_click.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('deletePdfSplitSelected','','images/delete_selected_hover.gif',1)"><img src="images/delete_selected.gif"  id="deletePdfSplitSelected" style=" cursor:pointer; border:none;" alt="Delete" onClick="delPdfSplitFun(<?php echo ($counter-1);?>);"/></a>
							<?php } ?>
							<a href="#" style="padding-right:20px;" onClick="MM_swapImage('CloseBtnPdf','','images/close_click.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('CloseBtnPdf','','images/close_hover.gif',1)"><img src="images/close.gif" id="CloseBtnPdf" style="width:70px; height:25px; border:none;" alt="Close" onClick="javascript:if(top.opener.top) { top.opener.top.iframeHome.iOLinkBookSheetFrameId.location.reload();}top.window.close();" /></a>
						</td>
					</tr>
				</table>		
			<?php
				closedir($handle);
			}
			?>	
		</form>
	<?php
		if($msgRec!=''){
			echo '<script>alert("'.$msgRec.'");</script>';
		}
	?>
    </body>
</html>