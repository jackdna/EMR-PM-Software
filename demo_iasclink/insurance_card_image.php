<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$patient_in_waiting_id = $_REQUEST['patient_in_waiting_id'];
$insuranceType = $_REQUEST['insuranceType'];

$insDataQry = "select * from insurance_data where  waiting_id = '".$patient_in_waiting_id."' and  type = '".$insuranceType."'";
$insDataRes = imw_query($insDataQry);
$insDataNumRow = imw_num_rows($insDataRes);
if($insDataNumRow>0) {
	$insDataRow = @imw_fetch_array($insDataRes);
	$insuranceId = $insDataRow['id'];
	$insScan1Upload = $insDataRow['insScan1Upload'];
	$insScan2Upload = $insDataRow['insScan2Upload'];
}

?>
<html>
<head>
	<script>
		
		function changeImgSize(){
			var target = 100;
			var imgHeight = document.getElementById('imgInsuranceThumbNail').height;
			var imgWidth = document.getElementById('imgInsuranceThumbNail').width;
			if((imgHeight>=200) || (imgWidth>=200)){
				if(imgWidth > imgHeight){ 
					percentage = (target/imgWidth); 
				}else{ 
					percentage = (target/imgHeight);
				} 
				widthNew = imgWidth*percentage; 
				heightNew = imgHeight*percentage; 	
				document.getElementById('imgInsuranceThumbNail').height = heightNew;
				document.getElementById('imgInsuranceThumbNail').width = widthNew;	
			}
			//alert(top.document.frm_uploadInsSecImage.hidd_delImage.value);
			
		}
		
		//START THIS FUNCTION FOR SECOND IMAGE
		function changeImgSize2(){
			var target2 = 100;
			var imgHeight2 = document.getElementById('imgInsuranceThumbNail2').height;
			var imgWidth2 = document.getElementById('imgInsuranceThumbNail2').width;
			if((imgHeight2>=200) || (imgWidth2>=200)){
				if(imgWidth2 > imgHeight2){ 
					percentage2 = (target2/imgWidth2); 
				}else{ 
					percentage2 = (target2/imgHeight2);
				} 
				widthNew2 = imgWidth2*percentage2; 
				heightNew2 = imgHeight2*percentage2; 	
				document.getElementById('imgInsuranceThumbNail2').height = heightNew2;
				document.getElementById('imgInsuranceThumbNail2').width = widthNew2;	
			}
			
			
		}
		//END THIS FUNCTION FOR SECOND IMAGE
		
		function showImgDiv(){
			top.frames[0].frames[0].document.getElementById('imgDiv').style.display = 'block';
		}
		function MM_openBrInsuranceCardWindow(theURL,winName,features) {
		  window.open(theURL,winName,features);
		}		
		function showInsuranceImgWindow(strImgNumber) {
			if(!strImgNumber) {
				strImgNumber = '';
			}
			var insuranceId = '<?php echo $insuranceId;?>';
			MM_openBrInsuranceCardWindow('insuranceCardImagePopUp.php?from=iolink_insurance_card&id='+insuranceId+'&imgNmbr='+strImgNumber,'insuranceCardImage','scrollbars=yes,width=900,height=400,resizable=yes,location=yes,status=yes');
		}
		
		function delImageFun(strValue,iolExist) {
			//SET HIDDEN VALUE TO YES IF ANY OTHER IOL IMAGE EXIST WHILE DELETING EITHER OF THOSE IMAGES
				top.document.getElementById('hidd_anyOneImageExist').value=iolExist;
			//SET HIDDEN TO YES VALUE IF ANY OTHER IOL IMAGE EXIST WHILE DELETING EITHER OF THOSE IMAGES
			
			if(top.document.frm_uploadInsPriImage) {
				top.document.frm_uploadInsPriImage.hidd_delImage.value=strValue;
			}
			if(top.document.frm_uploadInsSecImage) {
				top.document.frm_uploadInsSecImage.hidd_delImage.value=strValue;
			}
			top.document.getElementById('uploadBtn').click();
			
		}
		
		function MM_preloadImages() { //v3.0
		  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
			var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
			if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
		}
		
		function MM_findObj(n, d) { //v4.01
		  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
			d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
		  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
		  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
		  if(!x && d.getElementById) x=d.getElementById(n); return x;
		}
		
		function MM_swapImage() { //v3.0
		  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
		   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
		}
		function MM_swapImgRestore() { //v3.0
		  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
		}
		
		
		
	</script>
</head>
<body>

<?php 
if($insScan1Upload || $insScan2Upload) {
	
?>
	<table border="0" >
		<tr>
			<td></td>
			<?php if($insScan1Upload) {
					$existImage1='';
					if($insScan2Upload) { $existImage2='yes';}
			?>
					<td><img border="0" width="100" height="80" style="cursor:pointer;" id="imgInsuranceThumbNail" src="logoImg.php?from=iolink_insurance_card&id=<?php echo $insuranceId; ?>" onClick="return showInsuranceImgWindow();">&nbsp;<a style="cursor:pointer; " onClick="MM_swapImage('deleteImage','','images/delete_selected_click.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('deleteImage','','images/delete_selected_hover.gif',1)"  ><img src="images/delete_selected.gif" name="deleteImage" id="deleteImage" border="0"  alt="Delete" onClick="delImageFun('yes','<?php echo $existImage2;?>')" /></a></td> 
			<?php } ?>
			<?php if($insScan2Upload) { 
					$existImage2='';
					if($insScan1Upload) { $existImage1='yes';}?>
					<td><img border="0" width="100" height="80" style="cursor:pointer;" id="imgInsuranceThumbNail2" src="logoImg2.php?from=iolink_insurance_card&id=<?php echo $insuranceId; ?>" onClick="return showInsuranceImgWindow('secondImage');">&nbsp;<a style="cursor:pointer; " onClick="MM_swapImage('deleteImage2','','images/delete_selected_click.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('deleteImage2','','images/delete_selected_hover.gif',1)"  ><img src="images/delete_selected.gif" name="deleteImage2" id="deleteImage2" border="0"  alt="Delete" onClick="delImageFun('yes2','<?php echo $existImage1;?>')" /></a></td> 
			<?php }  ?>
			
		</tr>
	</table>
<?php }  ?>	
<script>
	
	if(document.getElementById('imgInsuranceThumbNail')){
		
		changeImgSize();
	}
	
	
	if(document.getElementById('imgInsuranceThumbNail2')){
		
		changeImgSize2();
	}
	

if(!document.getElementById('imgInsuranceThumbNail') && !document.getElementById('imgInsuranceThumbNail2')) {
	top.document.getElementById('iframeIOL').style.height = '0px';
	top.document.getElementById('iframeIOL').style.width = '0px';

}	
	
</script>
</body></html>