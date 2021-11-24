<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$dischargeSummarySheetId = $_GET['dischargeSummarySheetId'];
$dis_ScanUpload = $_GET['dis_ScanUpload'];
?>
<html>
<head>
	<script>
		function changeImgSize(){
			var target = 100;
			var imgHeight = document.getElementById('imgDischargeThumbNail').height;
			var imgWidth = document.getElementById('imgDischargeThumbNail').width;
			if((imgHeight>=200) || (imgWidth>=200)){
				if(imgWidth > imgHeight){ 
					percentage = (target/imgWidth); 
				}else{ 
					percentage = (target/imgHeight);
				} 
				widthNew = imgWidth*percentage; 
				heightNew = imgHeight*percentage; 	
				document.getElementById('imgDischargeThumbNail').height = heightNew;
				document.getElementById('imgDischargeThumbNail').width = widthNew;	
			}
			//alert(top.mainFrame.main_frmInner.document.frm_uploadDISImage.hidd_delImage.value);
			
		}
		
		//START THIS FUNCTION FOR SECOND IMAGE
		function changeImgSize2(){
			var target2 = 100;
			var imgHeight2 = document.getElementById('imgDischargeThumbNail2').height;
			var imgWidth2 = document.getElementById('imgDischargeThumbNail2').width;
			if((imgHeight2>=200) || (imgWidth2>=200)){
				if(imgWidth2 > imgHeight2){ 
					percentage2 = (target2/imgWidth2); 
				}else{ 
					percentage2 = (target2/imgHeight2);
				} 
				widthNew2 = imgWidth2*percentage2; 
				heightNew2 = imgHeight2*percentage2; 	
				document.getElementById('imgDischargeThumbNail2').height = heightNew2;
				document.getElementById('imgDischargeThumbNail2').width = widthNew2;	
			}
			
			
		}
		//END THIS FUNCTION FOR SECOND IMAGE
		
		function showImgDiv(){
			top.frames[0].frames[0].document.getElementById('imgDiv').style.display = 'block';
		}
		function MM_openBrDischargeSummaryWindow(theURL,winName,features) {
		  window.open(theURL,winName,features);
		}		
		function showDischargeImgWindow(strImgNumber) {
			if(!strImgNumber) {
				strImgNumber = '';
			}
			var dischargeSummaryId = '<?php echo $dischargeSummarySheetId;?>';
			MM_openBrDischargeSummaryWindow('dischargeSummaryImagePopUp.php?from=discharge_summary_sheet&id='+dischargeSummaryId+'&imgNmbr='+strImgNumber,'DischargeSummaryImage','scrollbars=yes,width=900,height=400,resizable=yes,location=yes,status=yes');
		}
		
		function delImageFun(strValue,iolExist) {
			//SET HIDDEN VALUE TO YES IF ANY OTHER IOL IMAGE EXIST WHILE DELETING EITHER OF THOSE IMAGES
				top.mainFrame.main_frmInner.document.getElementById('hidd_anyOneImageExist').value=iolExist;
			//SET HIDDEN TO YES VALUE IF ANY OTHER IOL IMAGE EXIST WHILE DELETING EITHER OF THOSE IMAGES
			
			top.mainFrame.main_frmInner.document.frm_uploadDISImage.hidd_delImage.value=strValue;
			top.mainFrame.main_frmInner.document.getElementById('uploadBtn').click();
			
		}
	</script>
    <?php include"common/link_new_file.php";?>
</head>
<body>

<?php 

$dischargeSummarySheetDetails = $objManageData->getRowRecord('dischargesummarysheet', 'dischargeSummarySheetId', $dischargeSummarySheetId);
if(!$dis_ScanUpload) {
	$dis_ScanUpload = $dischargeSummarySheetDetails->dis_ScanUpload;
}
if(!$dis_ScanUpload2) {
	$dis_ScanUpload2 = $dischargeSummarySheetDetails->dis_ScanUpload2;
}

if($dis_ScanUpload || $dis_ScanUpload2) {
?>
<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
<?php
 if($dis_ScanUpload) {
		$existImage1='';
		if($dis_ScanUpload2) { $existImage2='yes';}
?>

<div class="col-md-12 col-lg-6 col-xs-6 col-sm-6">
    <div class="media img_wrap_discharge">
        <div class="media-object pull-left">
            <img style="cursor:pointer;" class="thumbnail" id="imgDischargeThumbNail" src="admin/logoImg.php?from=discharge_summary_sheet&id=<?php echo $dischargeSummarySheetId; ?>" onClick="return showDischargeImgWindow();">
        </div>
        
        <div class="media-body">
            <div class="well well-sm">
                <a href="javascript:void(0)" onClick="delImageFun('yes','<?php echo $existImage2;?>')" class="btn btn-danger"> <i class="fa fa-trash"> Delete </i>	</a>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<div class="clearfix margin_adjustment_only visible-md"></div>
<?php if($dis_ScanUpload2) { 
$existImage2='';
if($dis_ScanUpload) { $existImage1='yes';}?>
<div class="col-md-12 col-lg-6 col-xs-6 col-sm-6">
    <div class="media img_wrap_discharge">
        <div class="media-object pull-left">
       	 <img class="thumbnail" style="cursor:pointer;" id="imgDischargeThumbNail2" src="admin/logoImg2.php?from=discharge_summary_sheet&id=<?php echo $dischargeSummarySheetId; ?>" onClick="return showDischargeImgWindow('secondImage');">
        </div>
        <div class="media-body">
            <div class="well well-sm">
            	<a href="javascript:void(0)" onclick="delImageFun('yes2','<?php echo $existImage1;?>')" class="btn btn-danger"> <i class="fa fa-trash"> Delete </i>	</a>
            </div>
    	</div>   
	</div>
</div> 
<?php }  ?>
                 
</div>
<?php }  ?>	
<script>
	if(document.getElementById('imgDischargeThumbNail')){
		
		changeImgSize();
	}
	
	
	if(document.getElementById('imgDischargeThumbNail2')){
		
		changeImgSize2();
	}
	
if(!document.getElementById('imgDischargeThumbNail') && !document.getElementById('imgDischargeThumbNail2')) {
	top.mainFrame.main_frmInner.document.getElementById('iframeIOL').style.height = '0px';
	top.mainFrame.main_frmInner.document.getElementById('iframeIOL').style.width = '0px';

}	
	
</script>
</body></html>