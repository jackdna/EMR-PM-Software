<?php

include_once("../../config/globals.php");

$updir=substr(data_path(), 0, -1);
$srcDir = substr(data_path(1), 0, -1);

$patient_id = $_SESSION['patient'];
$userauthorized = $_SESSION['authId'];
$scan_id = $_SESSION['document_scan_id'];

$form_id = $_REQUEST["formId"];
$formName = $_REQUEST['formName'];
$edit_id = $_REQUEST['edit_id'];
$show = $_REQUEST['show'];

if(empty($form_id) && $form_id!=0){
	$form_id = (isset($_SESSION["finalize_id"]) && !empty($_SESSION["finalize_id"])) ? $_SESSION["finalize_id"] : $_SESSION['form_id'];
}

$browser = browser();
if($phpServerIP != $_SERVER['HTTP_HOST']){
	$phpServerIP = $_SERVER['HTTP_HOST'];
	$GLOBALS['php_server'] = $phpHTTPProtocol.$phpServerIP.$phpServerPort.$web_root;
}

$scnUpldDoc='Scan Documents';
if($show=='upload') {
	$scnUpldDoc='Upload Documents';
}
if($_GET['prevType']) {
	$scnUpldDoc='Scan/Upload Documents';
}
if($_POST && $_POST["backClick"]!="yes" && $_POST["elem_delete"]==""){
	echo "<script>opener.top.fmain.location.reload(true);window.close()</script>";	
	sleep(1);die();
}

$formNameDisplay = $formName;
if($formName=='ptInfoMedHxAllergies') {
	$formNameDisplay = 'Allergies';
}else if($formName=='ptInfoMedHxMedication') {
	$formNameDisplay = 'Medication';
}else if($formName=='ptInfoMedHxSurgeries') {
	$formNameDisplay = 'Surgeries';
}else if($formName=='ptInfoMedHxImmunization') {
	$formNameDisplay = 'Immunization';
}else if ($formName == 'ptInfoMedHxGeneralHealth'){
	$formNameDisplay = 'General Health';
}        
?>
<!DOCTYPE html>
<html>
<head>
<title>imwemr : <?php echo $scnUpldDoc;?></title>
	<!-- Bootstrap -->
  <link href="<?php echo $GLOBALS['webroot'] ?>/library/css/bootstrap.css" rel="stylesheet" type="text/css">
  <link href="<?php echo $GLOBALS['webroot'] ?>/library/css/common.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.css" type="text/css" rel="stylesheet" />
  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
  <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap.js"></script>
      
	<script>
		window.focus();
		//window.onload =function()
		{
			//alert(screen.availHeight+'@@'+window.screen.height);
			var parWidth = (screen.availWidth > 900) ? 900 : screen.availWidth ;
			window.resizeTo(parWidth,790);
			var t = 10;
			var l = parseInt((screen.availWidth - window.outerWidth) / 2)
			window.moveTo(l,t);
		}
			
		var oPO = new Array();
		var browser_name = '<?php echo $browser['name'];?>';
		var web_root = '<?php echo $GLOBALS['php_server']; ?>';
		var scan_container_height = 300;
		
		function upload_scan(frm){
			//if(browser_name == "msie")
				upload(frm);
			//else if(browser_name == "chrome")
				//frm.submit();
			
		}
		function enlargeImage(sId){		
			document.getElementById('sId').value = sId;
			document.backFrm.submit();
		}
		function showpdf( id,pdf ){	
			var SW	=	window.screen.width ;
			var SH	=	window.screen.height;
			
			var	W	=	( SW > 1200 ) ?  1200	: SW ;
			var	H	=	W * 0.65
			
			if( (typeof id != "undefined") && (id != "") ){
				var n = "scan_"+id;
				if(!oPO[n] || !(oPO[n].open) || (oPO[n].closed == true)){		
					var url = "../../interface/chart_notes/logoImg.php?from=scanImage&scan_id="+id+"&headery="+pdf;
					oPO[n] = window.open(url,"","width="+W+",height="+H+",resizable=1,scrollbars=1");				
				}		
				oPO[n].focus();
			}
		}
		function chkSelected(theObj){
			var j = 0;
			var obj = document.getElementsByName('imageArr[]');
			var len = obj.length;
			for(i=0;i<len;i++){
				if(obj[i].checked==true){
					j = j + 1;
				}
			}
			if(j>2){
				alert("Cann't select more than two images to compare")
				theObj.checked = false;
			}
		}
		function chkForm(){
			var flag = 0;
			var obj = document.getElementsByName('imageArr[]');
			if(obj){
				var len = obj.length;
				if(len<=1){
					alert('No images to compare')
					return false;
				}
				for(i=0;i<len;i++){
					if(obj[i].checked==true){
						++flag;
					}
				}
				if(flag<=1){
					alert('Please select images to compare')
					return false;
				}
			}
		}
		function reloadScan(frm,show){
			if( (frm != null) && (show != null) ){
				window.location.replace( "scan_ptinfo_medhx_images.php?formName="+frm+"&show="+show);
			}
		}
		function imagePreview( ){
			//if( (typeof id != "undefined") && ( id != "" ) ){		
			var oImgs = document.getElementsByTagName("IMG");
			if( oImgs ){
			var len = oImgs.length;
			for( var i=0;i<len;i++ ){	
				var oImg = oImgs[i];//document.getElementById('imgThumbNail'+id);
				if(!oImg){ alert('Error: No Object Exist.'); return 1; } 
				var imgWidth = oImg.offsetWidth;
				var imgHeight = oImg.offsetHeight;
				var target = 200;
				if((imgHeight>=150) || (imgWidth>=150)){
					if (imgWidth > imgHeight) { 
						percentage = (target/imgWidth); 
					} else { 
						percentage = (target/imgHeight);
					} 
					widthNew = imgWidth*percentage; 
					heightNew = imgHeight*percentage; 	
					oImg.style.height = heightNew + "px";
					oImg.style.width = widthNew + "px";
					//
					//alert(id+"\n"+heightNew+"\n"+widthNew);
				}else{
					oImg.style.height = "75px";
					}
			//}
			}
			}
		}
		function delImg( id ){	
			if( (typeof id != "undefined") && (id != "") ){
				if( (confirm('Do you want to delete?'))  ){		
					document.frm1.elem_delete.value = id;
					document.frm1.submit();
				}
			}
		}
		function frm_submit(){
			document.frm1.submit();
		}
		function refresh_doc(nam){
			if(nam=='chartnoteDocumentsRel'){
				window.opener.location.reload();
			}
		}
  </script>
</head>
<body>
<div class="panel panel-primary">
  <div class="panel-heading"><?php echo $scnUpldDoc;?> >  <?php echo $formNameDisplay;?></div>
  <div class="panel-body popup-panel-body" style="min-height:610px;">
  	<form name="backFrm" action="scan_ptinfo_medhx_images.php" method="post">
      <input type="hidden" name="formName" value="<?php echo $formName; ?>">
      <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
      <input type="hidden" name="show" value="<?php echo $show; ?>">
      <input type="hidden" name="sId" value="">		
      <input type="hidden" name="formId" value="<?php echo $form_id; ?>">
      <input type="hidden" name="backClick" id="backClick" value="yes">		
		</form>
    
    <?php if($show=='scan'){?>
    <table  width="100%" border="0" cellpadding="2" cellspacing="0" id="imageTable">
    	<tr height="400" valign="top" id="scanTr">
    		<td width="20%" align="justify">
        	<?php 	$browser = browser();
						if($browser['name'] == "msie" || $browser['name'] != "chrome"){
							echo "<script>multiScan='yes';no_of_scans=60;upload_scan_url = '".$GLOBALS['php_server']."/interface/chart_notes/uploadScan.php?method=upload&formName=".$formName."&form_id=".$form_id."&edit_id=".$edit_id."'</script>";
							include_once($GLOBALS['srcdir']."/scan/scan_control.php");
						}
						else {
							echo "<script>multiScan='yes';no_of_scans=60;upload_scan_url = '".$GLOBALS['php_server']."/interface/chart_notes/uploadScan.php?method=upload&formName=".$formName."&form_id=".$form_id."&edit_id=".$edit_id."'</script>";
							include_once($GLOBALS['srcdir']."/scanc/scan_control.php");
						}
						?> 
				</td>
      </tr>
  	</table>
    <?php }?>
  	
    <form name="frm1" action="scan_ptinfo_medhx_images.php" method="post" onSubmit="return chkForm();" enctype="multipart/form-data">
    	<input type="hidden" name="formName" value="<?php echo $formName; ?>">
      <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
      <input type="hidden" name="show" value="<?php echo $show; ?>">
      <input type="hidden" name="prevType" value="<?php echo $_REQUEST['prevType']; ?>">
      <input type="hidden" name="formId" value="<?php echo $form_id; ?>">
      <input type="hidden" name="elem_delete" value="">
			
      <div id="divImages" style="position:relative;overflow:auto;width:100%;">
      	<table width="100%" border="0" cellpadding="2" cellspacing="0" id="imageTable">
        <?php
					
					if(!empty($_REQUEST['elem_delete'])){
						$imagesId = $_REQUEST["elem_delete"];
						$sql = "DELETE FROM ".constant("IMEDIC_SCAN_DB").".scans WHERE scan_id in (".$imagesId.")";
						$rs  = imw_query($sql);
						
						$sqlBinaryDel = "DELETE FROM ".constant("IMEDIC_SCAN_DB").".scans_binary WHERE scan_id in ($imagesId)";
						$rsBinaryDel  = imw_query($sqlBinaryDel);		
					}
					$count = 0;
					$andFormIdStr = " AND form_id = '".$form_id."' ";
					if($show=='preview'){
						if($formName=="ptInfoAdvancedDirective" || $formName=="ptInfoMedHxGeneralHealth") {
							$andFormIdStr = "";
						}
						$getImagesToShowStr = "SELECT * FROM ".constant("IMEDIC_SCAN_DB").".scans WHERE patient_id = '".$patient_id."' AND image_form = '".$formName."' ORDER BY scan_id";
						if($_REQUEST['CompareBtn'] == "Compare"){
							$imageArr = $_REQUEST['imageArr'];
							if(count($imageArr)>0){
								foreach($imageArr as $imagesCompareId){
									if($imagesId){
										$imagesId = $imagesId.', '.$imagesCompareId;
									}else{
										$imagesId = $imagesCompareId;
									}
								}
							}
							$getImagesToShowStr = "SELECT * FROM ".constant("IMEDIC_SCAN_DB").".scans WHERE scan_id in (".$imagesId.")";
						}
						if($_REQUEST['sId']){
							$sId = $_REQUEST['sId'];
							$getImagesToShowStr = "SELECT * FROM ".constant("IMEDIC_SCAN_DB").".scans WHERE scan_id in (".$sId.")";
						}
						$getImagesToShowQry = imw_query($getImagesToShowStr);
						if(imw_num_rows($getImagesToShowQry)>0){				
							?>
                            <tr>
                            <?php
							while($getImagesToShowRows = imw_fetch_assoc($getImagesToShowQry)){
								$scan_id = $getImagesToShowRows['scan_id'];
								$image_name = $getImagesToShowRows['image_name'];					
								$imageScanedArr[]  = $scan_id;
								$doc_title = $getImagesToShowRows['image_name'];
								$pdf_url = $getImagesToShowRows['pdf_url']; 					
								$fileType = $getImagesToShowRows['file_type'];
								$image_form = $getImagesToShowRows['image_form'];
								$imgSrc = "../../interface/chart_notes/logoImg.php?from=scanImage&scan_id=".$scan_id;
								$file_info_short = pathinfo($getImagesToShowRows["file_path"]);
								if(($image_form=="ptInfoAdvancedDirective" || $image_form=="ptInfoMedHxGeneralHealth") && $fileType != "application/pdf") {
									$pthThmbTmp = $file_info_short["dirname"]."/thumbnail/".$file_info_short["basename"];
									if(file_exists($updir.$pthThmbTmp)) {
										$imgSrc = $srcDir.$pthThmbTmp;
									}
								}
								if($count>=4){
									echo '</tr><tr>';
									$count = 0;
								}
								$count++;
								?>
                                
								
									<td align="center" valign="middle">
										<table border="0" cellpadding="0" cellspacing="0">
											
                                            <tr>
												<td align="center">
                                                    <!--<img style="cursor:pointer; " border="0" onDblClick="return enlargeImage('<?php echo $scan_id; ?>');" onClick="showpdf('<?php echo $scan_id; ?>','')" id="imgThumbNail<?php echo $scan_id; ?>" src="<?php echo $imgSrc; ?>">-->
                                                    <div class="thumbnail text-center pd0">
                                                        <span class="col-xs-12 bg bg-info pd5" title="<?php echo $file_info_short["basename"]; ?>"><?php echo $image_name; ?></span>
                                                        <span class="clearfix"></span>
                                                        <div class="image-grid" style="max-height:150px;">
                                                            <span>
                                                                <?php 
                                                                if( $fileType == "application/pdf" ){ ?>
                                                                    <img style="cursor:pointer; " class="img-thumbnail" src="<?php echo $GLOBALS['srcdir']."/library/images/pdfimg.png"; ?>" alt="pdf file" onClick="showpdf('<?php echo $scan_id; ?>','pdf')">
                                                                <?php 
                                                                }else{?>
                                                                    <img style="cursor:pointer; " class="img-thumbnail" onDblClick="return enlargeImage('<?php echo $scan_id; ?>');" onClick="showpdf('<?php echo $scan_id; ?>','')" id="imgThumbNail<?php echo $scan_id; ?>" src="<?php echo $imgSrc;?>" />
                                                                <?php 
                                                                }?>
                                                            </span>
                                                            <a class="delete" title="Delete" href="javascript:delImg('<?php echo $scan_id; ?>');" >
                                                              <i class="glyphicon glyphicon-remove-circle"></i>
                                                            </a>
                                                            <a class="layer"  onClick="showpdf('<?php echo $scan_id; ?>','')"></a>	
                                                        </div>
                                                    </div>
												</td>
											</tr>							
										</table>
									</td>
									
								<?php
							}
							?>
                            </tr>
                            <?php			
						}else{
				?>
							<tr><td align="center"><b>No image found</b></td></tr>	
       	<?php
						}
					}
					else if($show=='scan'){
					if($_POST){
						if($_REQUEST['formName'] == "ptInfoMedHxGeneralHealth") {
						?>
            	<script>
								if(opener.top.fmain.document.getElementById('scnGenHlthId')) {
									opener.top.fmain.document.getElementById('scnGenHlthId').innerHTML = '
									 <label class="btn btn-primary mt5 btn-xs" id="" onClick="showAD(this)" style="font-size:20px;">
                  <i class="glyphicon glyphicon-open-file"></i>
                </label>';
								}
							</script>
         	<?php
						}
						
						$comment = trim(addslashes($_POST['comment']));
						if($comment) {
							$chkCmntQry = "select * from ".constant("IMEDIC_SCAN_DB").".scans where patient_id='".$patient_id."' && image_form='$formName' && form_id ='$form_id' && scan_or_upload='scan' && scan_id='$scan_id'";
							$chkCmntRes =imw_query($chkCmntQry);
							if(imw_num_rows($chkCmntRes)>0){
								$chkCmntRow = imw_fetch_array($chkCmntRes);
								$chkDocUploadDate = $chkCmntRow['created_date'];
								//$scanOrUploadDate = '$chkDocUploadDate';
								 $explDtTm = explode(' ',$chkDocUploadDate);
								 list($yr, $mnth, $dy) = explode('-',$explDtTm[0]);
								 list($hr, $min, $scnd) = explode(':',$explDtTm[1]);
								 $chkNewDt = date('y-m-d H:i:s', mktime($hr,$min,$scnd-180,$mnth,$dy,$yr));
							}
							$qry = "update ".constant("IMEDIC_SCAN_DB").".scans set testing_docscan='$comment' ,created_date = now() where patient_id='$patient_id' && image_form='$formName' && form_id ='$form_id' && scan_or_upload='scan' && created_date >= '$chkNewDt'";
							$res = imw_query($qry);
						}
					}
					 
					$selQry = "select DATE_FORMAT(created_date,'%m-%d-%Y %h:%i:%s') AS crtDate,testing_docscan from ".constant("IMEDIC_SCAN_DB").".scans where patient_id = '".$patient_id."' && image_form='".$formName."' && form_id ='".$form_id."' && scan_or_upload='scan' order by `created_date` desc, scan_id desc limit 0,1";
					$resQry = imw_query($selQry);
					$rowQry = imw_fetch_array($resQry);
				
				?>
        	<!-- SCAN -->
        	<?php if($formName<>'adminConsoleDocumentsTemp' && $formName<>'chartnoteDocumentsRel'){?>
        	<tr align="center">
          	<td valign="top" align="left" style="padding-left:30px">
            <table cellpadding="0" cellspacing="0" border="0" width="100%">
								<tr>
									<td valign="top">
                 		<label><b>Comment:&nbsp;</b></label>
                    <textarea name="comment" id="comment" class="form-control" rows="3" cols="35">
											<?php echo $rowQry['testing_docscan'];?>
                   	</textarea>
									</td>
                  <td>&nbsp;</td>
                  <td valign="top">
                  	<label><b>Search:&nbsp;</b></label>
                    <div class="input-group">
                    	<input type='text' name="search1" id="search1" size='20' value="" class="form-control">
                      <label class="input-group-addon pointer" onClick="document.frm1.show.value='search1';frm_submit();">
                      	<i class="glyphicon glyphicon-search "></i>
                      </label>
                   	</div>   
                  </td>
               	</tr>
                
                <?php if(($rowQry['crtDate'] != '00-00-0000 12:00:00') && ($rowQry['crtDate'] != '')){?>
                <tr>
                	<td class="text-center" colspan="3">Last Scan Date Time:-&nbsp;<?php echo $rowQry['crtDate'];?></td>
               	</tr>
                <?php }?>
           	</table>
         		</td>
          </tr>
          <?Php } ?>
          
       	<?php
			 		}
					else if( $show == 'upload'){
						if($_POST){
							$comment1 = $_POST['comment'];
							if($comment1) {
								$chkCmntQry = "select * from ".constant("IMEDIC_SCAN_DB").".scans where patient_id='$patient_id' && image_form='$formName' && form_id ='$form_id' && file_type != '' && scan_or_upload='upload' && scan_id='$scan_id'";
								$chkCmntRes =imw_query($chkCmntQry);
								if(imw_num_rows($chkCmntRes)>0){
									$chkCmntRow = imw_fetch_array($chkCmntRes);
									$chkDocUploadDate = $chkCmntRow['doc_upload_date'];
									$explDtTm = explode(' ',$chkDocUploadDate);
									list($yr, $mnth, $dy) = explode('-',$explDtTm[0]);
									list($hr, $min, $scnd) = explode(':',$explDtTm[1]);
									$chkNewDt = date('y-m-d H:i:s', mktime($hr,$min,$scnd-120,$mnth,$dy,$yr));
								}
								$qry = "update ".constant("IMEDIC_SCAN_DB").".scans set multi_doc_upload_comment = '$comment1' where patient_id='$patient_id' && image_form='$formName' && form_id ='$form_id' && file_type != '' && scan_or_upload='upload' && doc_upload_date >= '$chkNewDt'";
								$res = imw_query($qry);
							}
						}
						
						$selQry = "select DATE_FORMAT(doc_upload_date,'%m-%d-%Y %h:%i:%s') AS crtDate1,multi_doc_upload_comment from ".constant("IMEDIC_SCAN_DB").".scans where patient_id = '$patient_id' && image_form='".$formName."' && form_id ='$form_id' && file_type != '' && scan_or_upload='upload' order by `doc_upload_date` desc, scan_id desc limit 0,1";
						$resQry = imw_query($selQry);
						$rowQry = imw_fetch_array($resQry);
				?>
        	<!-- Upload -->	
          <tr height="400" valign="top" id="uploadTr">
          	<td width="20%" align="justify">
            	<?php include $GLOBALS['srcdir'].'/upload/index.php'; ?>
						</td>
      		</tr>	
          
					<?php if($formName<>'adminConsoleDocumentsTemp' && $formName<>'chartnoteDocumentsRel'){?>
          <tr>
            <td valign="top" align="left" style="padding-left:30px">
              <table cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                  <td valign="top">
                    <label><b>Comment:</b></label>
                    <textarea name="comment" id="comment" rows="3" class="form-control" cols="35"><?php echo $rowQry['multi_doc_upload_comment'];?></textarea>
                  </td>
                  <td>&nbsp;</td>
                  <td valign="top">
                    <label><b>Search:</b></label>
                    <div class="input-group">
                    	<input type='text' name="search" id="search" size='20' value="" class="form-control">
                      <label class="input-group-addon pointer" onClick="document.frm1.show.value='search';frm_submit();">
                      	<i class="glyphicon glyphicon-search"></i>
                      </label>
                    </div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        	<?php } ?>
        
					<?php if($formName<>'adminConsoleDocumentsTemp' && $formName<>'chartnoteDocumentsRel'){?>
        	<?php if(($rowQry['crtDate1'] != '00-00-0000 12:00:00') && ($rowQry['crtDate1'] != '')){?>
        	<tr>
        		<td align="left" style="padding-left:100px">
          		Last Upload Date Time-:&nbsp;<?php echo $rowQry['crtDate1'];?>
          	</td>	
       		</tr>	   
					<?php }?>
        	<?php } ?>
          
       	<?php
					}
					else if( $show == 'search') {
						$search13 = $_POST['search'];
						$selQry = "select * from ".constant("IMEDIC_SCAN_DB").".scans where multi_doc_upload_comment LIKE '$search13'";
						$res = imw_query($selQry);
						$rowQry = imw_fetch_array($res);
						$search12 = $rowQry['multi_doc_upload_comment'];	
				?>
        	<table width="100%">
						<tr valign="middle">
            		
        <?php			
						if($search12 != ''){
							$getImagesToShowStr = "SELECT * FROM ".constant("IMEDIC_SCAN_DB").".scans WHERE patient_id = '$patient_id' AND form_id = '$form_id' AND image_form = '$formName' AND multi_doc_upload_comment = '$search12' ORDER BY scan_id";
						}
						
						if($_REQUEST['CompareBtn'] == "Compare"){
							$imageArr = $_REQUEST['imageArr'];
							if(count($imageArr)>0){
								foreach($imageArr as $imagesCompareId){
									if($imagesId){
										$imagesId = $imagesId.', '.$imagesCompareId;
									}else{
										$imagesId = $imagesCompareId;
									}
								}
							}
							
							$getImagesToShowStr = "SELECT * FROM ".constant("IMEDIC_SCAN_DB").".scans WHERE scan_id in ($imagesId)";
						}
						
						if($_REQUEST['sId']){
							$sId = $_REQUEST['sId'];
							$getImagesToShowStr = "SELECT * FROM ".constant("IMEDIC_SCAN_DB").".scans WHERE scan_id in ($sId)";
						}
						
						$getImagesToShowQry = imw_query($getImagesToShowStr);
						if(imw_num_rows($getImagesToShowQry)>0){				
							while($getImagesToShowRows = imw_fetch_assoc($getImagesToShowQry)){
								$scan_id = $getImagesToShowRows['scan_id'];
								$image_name = $getImagesToShowRows['image_name'];					
								$imageScanedArr[]  = $scan_id;
								$doc_title = $getImagesToShowRows['image_name'];
								$pdf_url = $getImagesToShowRows['pdf_url']; 					
								$fileType = $getImagesToShowRows['file_type'];
								if($count>=4){
									echo '</tr><tr>';
									$count = 0;
								}
								$count++;
					?>
          			<td align="center" valign="middle">
                	<table border="1" cellpadding="0" cellspacing="0">
                  	<tr>
                    	<td align="center">
                      	<?php if( $fileType == "application/pdf" ){ ?>
													<img style="cursor:hand; " src="<?php echo $GLOBALS['srcdir']."/images/pdfimg.png"; ?>" alt="pdf file" onClick="showpdf('<?php echo $scan_id; ?>','pdf')">
                      	<?php }else{?>
                        	<img style="cursor:hand; " onDblClick="return enlargeImage('<?php echo $scan_id; ?>');" onClick="showpdf('<?php echo $scan_id; ?>','')" id="imgThumbNail<?php echo $scan_id; ?>" src="../chart_notes/logoImg.php?from=scanImage&scan_id=<?php echo $scan_id; ?>" height="70" width="120" border="0">
                      	<?php }?>
                    	</td>
                 		</tr>
                    
                		<tr><td align="center"><?php echo $search; ?></td></tr>
                	</table>
              	</td>
        	<?php
							}
						}
						else{
					?>
							<td align="center"><b>No image found</b></td>
         	<?php 
						}
				?>
          	</tr>
				  </table>
				
				<?php
					}
 					else if($show == 'search1'){
						$search = $_POST['search1'];
						$selQry = "select * from ".constant("IMEDIC_SCAN_DB").".scans where testing_docscan LIKE '%$search%'";
						$res = imw_query($selQry);
						$rowQry = imw_fetch_array($res);
						$search12= $rowQry['testing_docscan'];
				?>
        	<table width="100%">
          	<tr valign="middle">
       	<?php
						if($search12 != ''){
							$getImagesToShowStr = "SELECT * FROM ".constant("IMEDIC_SCAN_DB").".scans WHERE patient_id = '".$patient_id."' AND form_id = '".$form_id."' AND image_form = '".$formName."' AND testing_docscan ='".$search12."' ORDER BY scan_id";
						}
						
						if($_REQUEST['CompareBtn'] == "Compare"){
							$imageArr = $_REQUEST['imageArr'];
							if(count($imageArr)>0){
								foreach($imageArr as $imagesCompareId){
									if($imagesId){
										$imagesId = $imagesId.', '.$imagesCompareId;
									}else{
										$imagesId = $imagesCompareId;
									}
								}
							}
							
							$getImagesToShowStr = "SELECT * FROM ".constant("IMEDIC_SCAN_DB").".scans WHERE scan_id in ($imagesId)";
						}
						
						if($_REQUEST['sId']){
							$sId = $_REQUEST['sId'];
							$getImagesToShowStr = "SELECT * FROM ".constant("IMEDIC_SCAN_DB").".scans WHERE scan_id in ($sId)";
						}
						
						$getImagesToShowQry = imw_query($getImagesToShowStr);
						
						if(imw_num_rows($getImagesToShowQry)>0){
							while($getImagesToShowRows = imw_fetch_assoc($getImagesToShowQry)){
								$scan_id = $getImagesToShowRows['scan_id'];
								$image_name = $getImagesToShowRows['image_name'];					
								$imageScanedArr[]  = $scan_id;
								$doc_title = $getImagesToShowRows['image_name'];
								$pdf_url = $getImagesToShowRows['pdf_url']; 					
								$fileType = $getImagesToShowRows['file_type'];
								if($count>=4){
									echo '</tr><tr>';
									$count = 0;
								}
								$count++;
				?>
        				<td align="center" valign="middle">
                	<table border="1" cellpadding="0" cellspacing="0">
                  	<tr>
                    	<td align="center">
                      <?php if( $fileType == "application/pdf" ){ ?>
                      	<img style="cursor:hand; " src="<?php echo $GLOBALS['webroot']."/library/images/pdfimg.png"; ?>" alt="pdf file" onClick="showpdf('<?php echo $scan_id; ?>','pdf')">
                     	<?php }else{?>
                      	<img style="cursor:hand;" onDblClick="return enlargeImage('<?php echo $scan_id; ?>');" onClick="showpdf('<?php echo $scan_id; ?>','')" id="imgThumbNail<?php echo $scan_id; ?>" src="../chart_notes/logoImg.php?from=scanImage&scan_id=<?php echo $scan_id; ?>" height="70" width="120" border="0">
                     	<?php }?>
                      </td>
                  	</tr>
                    
                    <tr><td align="center"><?php echo $search1; ?></td></tr>
                	</table>
              	</td>
                
				<?php
							}
						}
						else{
				?>
        			<td align="center"><b>No image found</b></td>
       	<?php
						}
				?>
        		</tr>
         	</table>
       	<?php
					}
					else{
				?>
        		<tr height="100" valign="top" id="selectTr">
            	<td width="20%" align="justify">
              	<Center>
                	<input type="button"  class="dff_button" id="btnScan" value="Scan"  onMouseOver="button_over('btnScan')" onMouseOut="button_over('btnScan','')" onClick="reloadScan('<?php echo $formName;?>','scan')">
                  <input type="button"  class="dff_button" id="btnUpload" value="Upload"  onMouseOver="button_over('btnUpload')" onMouseOut="button_over('btnUpload','')" onClick="reloadScan('<?php echo $formName;?>','upload')">
              	</Center>
            	</td>
          	</tr>
      	<?php
					}
				?>
        </table>
    	</div>
      
      <?php
				if($show=='preview' || $show =='search1'){
					if(empty($_REQUEST['prevType'])){
						$_REQUEST['prevType'] = 'scan';
					}
				}
				if($show =='search'){
					if(empty($_REQUEST['prevType'])){
						$_REQUEST['prevType'] = 'upload';
					}
				}
			?>
      
   	</form>
	</div>
  
  <footer id="module_buttons" class="panel-footer">
  	<?php if(($formName<>'adminConsoleDocumentsTemp' && $formName<>'chartnoteDocumentsRel') && ($show == 'scan'|| $show == 'upload') ){?>
    	<input type="button"  class="btn btn-primary" id="btnPreview" value="View Previous Scan" onClick="location.href='scan_ptinfo_medhx_images.php?formName=<?php echo $formName;?>&show=preview&prevType=<?php echo $show;?>';">
      <input type="button" class="btn btn-success" id="Save" value="Save" onClick="upload_scan(document.frm1);">
      <!-- 
      <input type="button"  class="dff_button" id="Close" value="Save"  onMouseOver="button_over('Close')" onMouseOut="button_over('Close','')" onClick="frm_submit();">
      -->
   	<?php } ?>
    
    <?php if($show=='preview' || $show =='search1' || $show =='search'){ ?>
    	<input type="button" id="back" onClick="document.backFrm.show.value='<?php echo $_REQUEST['prevType'];?>';backFrm.submit();" class="btn btn-primary" name="backBtn" value="Back">
    <?php } ?>
		
   	<input type="button"  class="btn btn-danger" id="Close" value="Close" onClick="window.close();refresh_doc('<?php echo $formName; ?>');">
 		   
  </footer>
</div>
    
	
	
 	
 	
  
</body>
</html>
