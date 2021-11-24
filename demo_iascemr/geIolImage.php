<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$operatingRoomRecordsId = $_GET['operatingRoomRecordsId'];
$iol_ScanUpload = $_GET['iol_ScanUpload'];
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="css/font-awesome.css">
	<script>
		function changeImgSize(){
			var target = 100;
			var imgHeight = document.getElementById('imgThumbNail').height;
			var imgWidth = document.getElementById('imgThumbNail').width;
			if((imgHeight>=200) || (imgWidth>=200)){
				if(imgWidth > imgHeight){ 
					percentage = (target/imgWidth); 
				}else{ 
					percentage = (target/imgHeight);
				} 
				widthNew = imgWidth*percentage; 
				heightNew = imgHeight*percentage; 	
				document.getElementById('imgThumbNail').height = heightNew;
				document.getElementById('imgThumbNail').width = widthNew;	
			}
			//alert(top.mainFrame.main_frmInner.document.frm_uploadIOLImage.hidd_delImage.value);
			
		}
		
		//START THIS FUNCTION FOR SECOND IMAGE
		function changeImgSize2(){
			var target2 = 100;
			var imgHeight2 = document.getElementById('imgThumbNail2').height;
			var imgWidth2 = document.getElementById('imgThumbNail2').width;
			if((imgHeight2>=200) || (imgWidth2>=200)){
				if(imgWidth2 > imgHeight2){ 
					percentage2 = (target2/imgWidth2); 
				}else{ 
					percentage2 = (target2/imgHeight2);
				} 
				widthNew2 = imgWidth2*percentage2; 
				heightNew2 = imgHeight2*percentage2; 	
				document.getElementById('imgThumbNail2').height = heightNew2;
				document.getElementById('imgThumbNail2').width = widthNew2;	
			}
			
			
		}
		//END THIS FUNCTION FOR SECOND IMAGE
		
		function showImgDiv(){
			top.frames[0].frames[0].document.getElementById('imgDiv').style.display = 'block';
		}
		function MM_openBrOpRoomWindow(theURL,winName,features) {
		  window.open(theURL,winName,features);
		}		
		function showImgWindow(strImgNumber) {
			if(!strImgNumber) {
				strImgNumber = '';
			}
			var opRoomId = '<?php echo $operatingRoomRecordsId;?>';
			MM_openBrOpRoomWindow('opRoomImagePopUp.php?from=op_room_record&id='+opRoomId+'&imgNmbr='+strImgNumber,'OpRoomImage','scrollbars=yes,width=900,height=400,resizable=yes,location=yes,status=yes');
		}
		function delImageFun(strValue,iolExist) {
			
				//SET HIDDEN VALUE TO YES IF ANY OTHER IOL IMAGE EXIST WHILE DELETING EITHER OF THOSE IMAGES
					top.mainFrame.main_frmInner.document.getElementById('hidd_anyOneImageExist').value=iolExist;
				//SET HIDDEN TO YES VALUE IF ANY OTHER IOL IMAGE EXIST WHILE DELETING EITHER OF THOSE IMAGES
			
					top.mainFrame.main_frmInner.document.frm_uploadIOLImage.hidd_delImage.value=strValue;
					top.mainFrame.main_frmInner.document.getElementById('uploadBtn').click();
			
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
<div style="padding-top:50px;"> </div>
<?php 
$operatingRoomRecordDetails = $objManageData->getRowRecord('operatingroomrecords', 'operatingRoomRecordsId', $operatingRoomRecordsId);
if(!$iol_ScanUpload) {
	$iol_ScanUpload = $operatingRoomRecordDetails->iol_ScanUpload;
}
if(!$iol_ScanUpload2) {
	$iol_ScanUpload2 = $operatingRoomRecordDetails->iol_ScanUpload2;
}
if($iol_ScanUpload || $iol_ScanUpload2) {
	
?>
	
			<?php if($iol_ScanUpload) {
					$existImage1='';
					if($iol_ScanUpload2) { $existImage2='yes';}
			?>
            		
					<div class="clearfix"></div>
                    
                    <div class="col-md-12 col-lg-6 col-xs-6 col-sm-6" style="max-width:150px; max-height:100px; min-width:150px; min-height:100px; ">
                    		
                            <div class="media img_wrap_discharge" >
                            
                            		<div class="well well-sm" style="position:relative;  padding-top:15px ">
                                    		
                                            <a style="cursor:hand;" onClick="return showImgWindow();">
                                            	<img border="0" width="100" height="80" id="imgThumbNail" src="admin/logoImg.php?from=op_room_record&id=<?php echo $operatingRoomRecordsId; ?>" />
                                         	</a>
                                          
                                          	<a class="btn btn-danger" onClick="confirmDelete('yes','<?php echo $existImage2;?>')" title="Delete"  href="javascript:void(0)" style="cursor:hand; position:absolute; right:-12px; top:-8px;     border-radius: 10% 100%; -moz-border-raidus:    border-radius: 40% 100%; -webkit-border-radius:    border-radius: 40% 100%; -ms-border-radius; text-align:left">
                                          		<b class="fa fa-times"></b>
                                          	</a>
                                           
                               		</div>
                                    
                     		</div>
                            
                	</div>
                    
        	<?php } ?>
            
            <?php 
					
					if($iol_ScanUpload2) 
					{
							$existImage2	=	''	;
							if($iol_ScanUpload) 
							{
								$existImage1	=	'yes'	;
							}
			?>
            				<!-- <a style="cursor:hand; " onClick="MM_swapImage('deleteImage2','','images/delete_selected_click.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('deleteImage2','','images/delete_selected_hover.gif',1)"  ><img src="images/delete_selected.gif" name="deleteImage2" id="deleteImage2" border="0"  alt="Delete"  /></a>-->
                            
                            <div class="col-md-12 col-lg-4 col-xs-6 col-sm-6" style="max-width:150px; max-height:100px; min-width:150px; min-height:100px; ">
                            		
                                    <div class="media img_wrap_discharge" >
                                    		<div class="well well-sm" style="position:relative;  padding-top:15px ">
                                            	<img border="0" width="100" height="80" style="cursor:hand;" id="imgThumbNail2" src="admin/logoImg2.php?from=op_room_record&id=<?php echo $operatingRoomRecordsId; ?>" onClick="return showImgWindow('secondImage');" />
                                                
                                                <a class="btn btn-danger" onClick="confirmDelete('yes2','<?php echo $existImage1;?>')"  title="Delete " href="javascript:void(0)" style="cursor:hand; position:absolute; right:-12px; top:-8px;     border-radius: 10% 100%; -moz-border-raidus:    border-radius: 40% 100%; -webkit-border-radius:    border-radius: 40% 100%; -ms-border-radius; text-align:left" >
                                                		<b class="fa fa-times"></b>
                                              	</a>
                                        	</div>
                                	</div>
                                    
                       		</div>
                            
			<?php 
					}
			?>
            
<?php 
}
?>	

<script>
	if(document.getElementById('imgThumbNail')){
		
		changeImgSize();
	}
	
	
	if(document.getElementById('imgThumbNail2')){
		
		changeImgSize2();
	}
	
if(!document.getElementById('imgThumbNail') && !document.getElementById('imgThumbNail2')) {
	top.mainFrame.main_frmInner.document.getElementById('iframeIOL').style.height = '0px';
	top.mainFrame.main_frmInner.document.getElementById('iframeIOL').style.width = '0px';

}	


var confirmDelete	=	function(param1,param2)
{
	/*var top	=	parseInt($(top.window).height() / 2 - 125)   	;
	var left		=	parseInt($(top.window).width() / 2 - 125)   	;
	css({
														'top'			:	top+'px',
														'left'			:	left +'px',
												}).*/
	top.$("#confirmDialogueBox").fadeIn(500).show(500);
												
	top.$("#confirmDialogueBox a#confirmYes").click(function(e){
			top.$("#confirmDialogueBox").fadeOut(500).hide(500);
			delImageFun(param1,param2);
			return false;
	});
	
	top.$("#confirmDialogueBox a#confirmNo").click(function(e){
			top.$("#confirmDialogueBox").fadeOut(500).hide(500);
			return false;
	});
		
	
};
	
</script>



</body></html>