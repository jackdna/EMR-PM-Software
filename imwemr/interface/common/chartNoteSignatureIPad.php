<?php
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");
include_once("../../config/globals.php");
$library_path = $GLOBALS['webroot'].'/library';
$browserInfo = browser();
$browserName = $browserInfo['name'];

$_SESSION['IPadImage'] = NULL;
$_SESSION['IPadImage'] = "";
$sigFileNamePath = "";		
unset($_SESSION['IPadImage']);
$patient_id 		= xss_rem($_REQUEST['patient_id']);
$rqSigFor 			= xss_rem($_REQUEST['sigFor']);
$idInnerHTML 		= xss_rem($_REQUEST['idInnerHTML']);
$signSeqNum 		= xss_rem($_REQUEST['signSeqNum']);
$user_id 			= $_REQUEST['user_id'];
$formName 			= $_REQUEST['formName'];
$signImgFolderName 	= $_REQUEST['signImgFolderName'];
$switch_sign_type 	= $_REQUEST['switch_sign_type'];
/*
$htmlFolder = "html2pdf";
if((constant("CONSENT_FORM_VERSION")=="consent_v2" && $formName!='surgeryConsent') || ($signImgFolderName=='new_html2pdf' )) {
	$htmlFolder = "new_html2pdf";
}
if($rqSigFor == "ptHealth" || $rqSigFor == "wtHealth"){
	$htmlFolder = "html2pdf";
}*/
$htmlFolder = 'data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/consent_forms';
$ipad_directory = '../../'.$htmlFolder.'/';
$ipad_save_path = $web_root.'/'.$htmlFolder.'/';
if($_REQUEST['sig_data']){
	//$capMasterId = 0;	
	
	$sigFileNamePath = $ipad_directory.'no_image.jpg'; 
	//check	

	if($rqSigFor == "ptConsent"){
		$sigFileName = 'signIpadConsent_'.date('d_m_y_h_i_s').'_'.$patient_id.'_1.jpg';
		$sigFileNamePath = $ipad_directory.$sigFileName; 
	}else if($rqSigFor == "witConsent"){
		$sigFileName = 'signIpadConsentWit_'.date('d_m_y_h_i_s').'_'.$patient_id.'_1.jpg';
		$sigFileNamePath = $ipad_directory.$sigFileName; 
	}else if($rqSigFor == "phyConsent"){
		$sigFileName = 'signIpadConsentPhy_'.$user_id.'_'.date('d_m_y_h_i_s').'_1.jpg';
		$sigFileNamePath = $ipad_directory.$sigFileName; 
	}else if($rqSigFor == "ptConsentSurgery"){
		$sigFileName = 'signIpadConsentSurgery_'.date('d_m_y_h_i_s').'_'.$patient_id.'_1.jpg';
		$sigFileNamePath = $ipad_directory.$sigFileName; 
	}else if($rqSigFor == "witConsentSurgery"){
		$sigFileName = 'signIpadConsentSurgeryWit_'.date('d_m_y_h_i_s').'_'.$patient_id.'_1.jpg';
		$sigFileNamePath = $ipad_directory.$sigFileName; 
	}else if($rqSigFor == "phyConsentSurgery"){
		$sigFileName = 'signIpadConsentSurgeryPhy_'.$user_id.'_'.date('d_m_y_h_i_s').'_1.jpg';
		$sigFileNamePath = $ipad_directory.$sigFileName; 
	}else if($rqSigFor == "ptHealth"){
		$sigFileName = 'signIpadPtHealth_'.$user_id.'_'.date('d_m_y_h_i_s').'_1.jpg';
		$sigFileNamePath = $ipad_directory.$sigFileName; 
	}else if($rqSigFor == "wtHealth"){
		$sigFileName = 'signIpadWtHealth_'.$user_id.'_'.date('d_m_y_h_i_s').'_1.jpg';
		$sigFileNamePath = $ipad_directory.$sigFileName; 
	}else if($rqSigFor == "adminProvider"){
		$ipad_directory="";
		$sigFileName = 'signIpadAdminProvider_'.$user_id.'_'.date('d_m_y_h_i_s').'_1.jpg';
		$authId = "UserId_".$_SESSION['authId'];
		$upload_dir = '../../data/'.PRACTICE_PATH.'/'.$authId;
		if(!is_dir($upload_dir)){
			mkdir($upload_dir,0700);
		}
		
		$signDir = $upload_dir."/sign";
		if(!is_dir($signDir)){
			mkdir($signDir,0700);
		}
		$ipad_directory= $signDir.'/';
		$sigFileNamePath = $ipad_directory.$sigFileName; 
	}
	$_SESSION['IPadImage'] = NULL;
	$_SESSION['IPadImage'] = "";					
	if($rqSigFor == "adminProvider"){
		$ipad_save_path = "";
		$ipad_save_path = '/UserId_'.$_SESSION['authId'].'/sign/';
		$_SESSION['IPadImage'] = $ipad_save_path.$sigFileName;
	}else{				
		$_SESSION['IPadImage'] = $ipad_save_path.$sigFileName;
	}
	
	
	$imgpng=0;
	if(strpos($_REQUEST['sig_data'],"data:image/png;base64,")!==false){
		$signatureData = str_replace("data:image/png;base64,","",$_REQUEST['sig_data']);
		$signatureData = base64_decode($signatureData);
		
		$sigFileNamePath_png = str_replace(".jpg",".png",$sigFileNamePath);
		file_put_contents($sigFileNamePath_png,$signatureData);
		list($pngWidth,$pngHeight) = getimagesize($sigFileNamePath_png);
		
		
		if(file_exists($sigFileNamePath_png)){
			$file = imagecreatetruecolor($pngWidth, $pngHeight);
			$new = imagecreatefrompng($sigFileNamePath_png);
			$kek=imagecolorallocate($file, 255, 255, 255);
			imagefill($file,0,0,$kek);
			imagecopyresampled($file, $new, 0, 0, 0, 0, $pngWidth, $pngHeight, $pngWidth, $pngHeight);
			imagejpeg($file, $sigFileNamePath, 100);
			imagedestroy($new);
			imagedestroy($file);
			if(file_exists($sigFileNamePath)){
				unlink($sigFileNamePath_png);		
			}
		}
	}else{
		$signatureData = str_replace("data:image/jpeg;base64,","",$_REQUEST['sig_data']);
		$signatureData = base64_decode($signatureData);
		file_put_contents($sigFileNamePath,$signatureData);
	}
	
	
	
}
?>
<!DOCTYPE HTML>
<html>
    <head>    	
    	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1"/>
        <title>imwemr Signature</title>
        <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	</head>	
	<body onLoad="init();" style="overflow: hidden; -ms-touch-action: none;" ontouchmove="BlockMove(event);" onmousemove="BlockMove(event);" onUnload="unload();"> 
    	<style>
			body {
			 margin: 10px ;
			}
			.loading{
				position:absolute;
				left:575px;
				top:75px;
				z-index:1000;
				width:145px;
				color:#346585;
				height:50px;
				background:center transparent url(../../library/images/loading_image.gif) no-repeat;
			}			
		</style>    
		<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>        
        <script type="text/javascript">	
			window.focus();
			var bvrsn = 0;
			window.ondevicemotion = function(event) {
			    if (navigator.platform.indexOf("iPad") != -1) {
				var version = 1;
				if (event.acceleration) version = window.devicePixelRatio;
				bvrsn = version;
			    }
			    window.ondevicemotion = null;
			}
	
			var xmlHttpPic;
			var imsSrc='';
			function unload(imsSrc){
				/*
				xmlHttpPic = GetXmlHttpObject()		
				if(xmlHttpPic==null)
				{
					alert ("Browser does not support HTTP Request")
					return;
				}*/					
				var url_pat = 'getIPadSessionPic.php';	
				$.ajax({
					type: "POST",
					url: url_pat,
					success: function(image){
						if(typeof(image)!="undefined" && !image) {
							image = imsSrc	
						}
						if(image && image!="" && typeof(image)!="undefined"){
						$.ajax({
							type: "POST",
							url: url_pat,
							data: "remove_session=yes",
							success: function(new_val){
								window.opener.image_DIV(image,'<?php echo $rqSigFor; ?>','<?php echo $idInnerHTML; ?>','<?php echo $signSeqNum; ?>');	
								window.close();
							}
						});
						}	
					}
				});
				
				
				/*
				xmlHttpPic.onreadystatechange = setSessionPic;
				xmlHttpPic.open("GET",url,true);
				xmlHttpPic.send(null);*/				
			}
			function swithSignType(signType) {
				top.document.getElementById('div_loading_image').style.display = "inline-block";
				var currLocation=document.location.href; 
				currLocation = currLocation.replace("&switch_sign_type=mouse","");
				currLocation = currLocation.replace("&switch_sign_type=touch","");
				var currLocationArr = currLocation.split("switch_sign_type");
				var newLocation = currLocationArr[0]+"&switch_sign_type="+signType;
				window.location.replace(newLocation);
			}
			
			/*
			function setSessionPic(){			
				if(xmlHttpPic.readyState == 4){	
					image = xmlHttpPic.responseText;
					window.opener.image_DIV(image,'<?php echo $rqSigFor; ?>','<?php echo $idInnerHTML; ?>','<?php echo $signSeqNum; ?>');	
					window.close();
				}
			}*/
			var canvas;
			var pen;	
			var lastPenPoint;
			var isIPad;
			var touch = "";
			var paint =false;
			var currentColor="#000000";
			function sig() {			
				canvas = document.getElementById('canvas');
				pen = canvas.getContext('2d');									
				lastPenPoint = null;
				isIPad = (new RegExp( "iPad", "i" )).test(navigator.userAgent);
				if(!isIPad) {
					//isIPad = (new RegExp( "touch", "i" )).test(navigator.userAgent);
					var browserName = "<?php echo $browserName;?>";
					var switch_sign_type = "<?php echo $switch_sign_type; ?>";
					if(browserName=='chrome') {
						isIPad = true;
						if(switch_sign_type=="mouse") {
							isIPad = false;
						}	
					}
				}
			}
				
			function getCanvasLocalCoordinates(pageX, pageY ) {
				return({
					x: (pageX - canvas.offsetLeft),
					y: (pageY - canvas.offsetTop)
				});
			}
		
			function getTouchEvent() {
				return(isIPad ? window.event.targetTouches[ 0 ] : event);
			}
			
			function onTouchStart() {
				/*
				var touch = getTouchEvent( event );     				
				var localPosition = getCanvasLocalCoordinates(touch.pageX,touch.pageY);
				lastPenPoint = {x: localPosition.x,y: localPosition.y};
				pen.beginPath();
				pen.moveTo( lastPenPoint.x, lastPenPoint.y );	
				*/
				var touch = getTouchEvent( event );  
				var mX=(touch.pageX) ? touch.pageX : touch.clientX;
				var mY=(touch.pageY) ? touch.pageY : touch.clientY;	
				paint = true; // start painting
				var localPosition = getCanvasLocalCoordinates(mX,mY);
				lastPenPoint = {x: localPosition.x,y: localPosition.y};
				pen.strokeStyle = currentColor; 	
				pen.beginPath();
				pen.moveTo( lastPenPoint.x, lastPenPoint.y );
				flgsave=true;	
							
			}
			
			function onTouchMove() {
				/*
				touch = getTouchEvent( event );     
				var localPosition = getCanvasLocalCoordinates(
					touch.pageX,
					touch.pageY
				);
				lastPenPoint = {
					x: localPosition.x,
					y: localPosition.y
				};
				pen.lineTo( lastPenPoint.x, lastPenPoint.y );		 
				// Render the line.
				pen.stroke();	
				*/
				if(paint){
					touch = getTouchEvent( event );     
					var mX=(touch.pageX) ? touch.pageX : touch.clientX;
					var mY=(touch.pageY) ? touch.pageY : touch.clientY;	
					
					var localPosition = getCanvasLocalCoordinates(mX, mY);
					lastPenPoint = {x: localPosition.x,y: localPosition.y};
					pen.strokeStyle = currentColor;
					//alert(pen.strokeStyle);
					pen.lineTo( lastPenPoint.x, lastPenPoint.y );		 
					// Render the line.
					pen.stroke();
					flgsave=true;	
				}
					
			}
			
			function init() {
				sig();
				/*
				canvas.addEventListener('touchstart', onTouchStart, false ); 
				canvas.addEventListener('touchmove', onTouchMove, false ); 				
				//canvas.addEventListener('mousedown', onMouseMacStart, false ); 
				//canvas.addEventListener('mouseup', onMouseMacStop, false ); 
				*/
				if(isIPad){
					canvas.addEventListener('touchstart', onTouchStart, false ); 
					canvas.addEventListener('touchmove', onTouchMove, false ); 				
				}else{
					canvas.addEventListener('mousedown', onTouchStart, false ); 
					canvas.addEventListener('mousemove', onTouchMove, false ); 
					canvas.addEventListener('mouseup', onMouseUp, false ); 
					canvas.addEventListener('mouseleave', onMouseLeave, false ); 
					canvas.addEventListener('mouseout', onMouseLeave, false );
				}
				
								
			}
			
			
			function BlockMove(event) {			
			 	event.preventDefault() ;
			}
			function clearCanvas(){								
				canvas.width = canvas.width; // Clears the canvas
				canvas.height = canvas.height; //clear canvas 				
				pen.clearRect(0,0,canvas.width,canvas.height) ;	
				if(document.getElementById('imageSig').style.display == "inline-block"){
					document.getElementById('imageSig').style.display = "none";
					document.getElementById('canvas').style.display = "inline-block"
					document.getElementById('saveImage').style.display = "inline-block"
					
				}
			}
			function createImage(){				
				/*
				if(bvrsn==""){					
					var strData = canvas.toDataURL("image/jpeg"); 
				}else{ //ipad						
					var strData = canvas.toDataURL();
				}*/
				var strData = canvas.toDataURL();
				document.getElementById("sig_data").value = strData;					
				document.sig.submit();				
			}

			//start Mousse compatibality
			var mouse = "";
			function getMouseEvent() {
				return(isIPad ? window.event.targetTouches[ 0 ] : event);
			}
			function onMouseStart() {
				var mouse = getMouseEvent( event );     				
				var localPosition = getCanvasLocalCoordinates(mouse.pageX,mouse.pageY);
				lastPenPoint = {x: localPosition.x,y: localPosition.y};
				pen.beginPath();
				pen.moveTo( lastPenPoint.x, lastPenPoint.y );				
			}
			function onMouseMove() {
				mouse = getMouseEvent( event );     
				var localPosition = getCanvasLocalCoordinates(
					mouse.pageX,
					mouse.pageY
				);
				lastPenPoint = {
					x: localPosition.x,
					y: localPosition.y
				};
				pen.lineTo( lastPenPoint.x, lastPenPoint.y );		 
				// Render the line.
				pen.stroke();						
			}
			function onMouseMacStart(){
				canvas.addEventListener('mousemove', onMouseMove, false ); 
			}
			
			function onMouseMacStop(){
				canvas.removeEventListener('mousemove', onMouseMove, false);
			}
			
			function onMouseUp() {
				if(typeof(flgsave)!="undefined"&&flgsave) {  }
				paint = false;
			}
			
			function onMouseLeave(){
				if(typeof(flgsave)!="undefined"&&flgsave) {  }
				paint = false;
			}
			//end Mousse compatibality

		</script>   
        <div id="div_loading_image" class="loading" style="left:475px;width:300px;margin-top:0px;display:none;">
			<div id="div_loading_text" style="width:300px;position:relative;padding-top:50px;text-align:center;">Loading</div>
        </div>
        <form name="sig" id="sig" action="" method="post">            
            <?php 
			$styleDisplayIMG = "none";
			$styleDisplayCanvas = "none";
			if($sigFileNamePath != ""){
				$styleDisplayIMG = "inline-block";
			}
			else{
				$styleDisplayCanvas = "inline-block";
			}
				
			$signLabel = "Switch To Mouse Signature";
			$signParameter = "mouse";
			$touchMouseImgName = "../../library/images/mouse_icon.png";
			if($switch_sign_type=="mouse") {
				$signLabel = "Switch To Touch Signature";	
				$signParameter = "touch";
				$touchMouseImgName = "../../library/images/touch.svg";
			}
			$touchMouseVisible = "hidden";
			if($browserName=="chrome") {
				$touchMouseVisible = "visible";	
			}
			$canvasHeight = "358";
			if($rqSigFor == "adminProvider") {
				$canvasHeight = "128";	
			}
			?>            
            <img src="<?php echo $sigFileNamePath; ?>" id="imageSig" width="508" height="358" style="display:<?php echo $styleDisplayIMG ?>;">
            <!--- This is where we draw our signature. --->
            <canvas id="canvas" width="508" height="<?php echo $canvasHeight;?>" style="border: 1px solid #F60; display:<?php echo $styleDisplayCanvas; ?>;"></canvas>
            <input type="hidden" name="sig_data"  id="sig_data"/>
	   <!-- <textarea name="sig_data" 	id="sig_data" rows="15" cols="200" /><?php echo $tmp_sig_data;?></textarea>-->
            <input type="hidden" name="patient_id" 	id="patient_id"  value="<?php echo $patient_id; ?>"/>
            <input type="hidden" name="sigFor" 		id="sigFor" 	 value="<?php echo $rqSigFor; ?>"/>
            <input type="hidden" name="signSeqNum" 	id="signSeqNum"  value="<?php echo $signSeqNum; ?>"/>
            <input type="hidden" name="idInnerHTML" id="idInnerHTML" value="<?php echo $idInnerHTML; ?>"/>
            <table>
            	<tr>
                	<td>
                    	<img style="cursor:pointer;" src="../../library/images/eraser.gif" id="sigPenEraser" name="sigPenEraser" title="Clear Signature" alt="Clear Signature" onClick="clearCanvas();">&nbsp;
                    </td>
                    <td>
                    	<input type="button" class="btn btn-success" name="saveImage" id="saveImage" value="Done" style="display:<?php echo $styleDisplayCanvas; ?>;" onClick="createImage();"/>&nbsp;
                    </td>
                    <td>
                    	<input type="button" class="btn btn-warning" name="cancel" id="cancel" value="Cancel" style="display:<?php echo $styleDisplayCanvas; ?>;" onClick="unload();"/>&nbsp;
                    </td>
                    <td>
                    	<input type="button" class="btn btn-danger" name="close" id="close" value="Close" onClick="unload();"/>
                    </td>
                    <td>
                        <img style="cursor:pointer; visibility:<?php echo $touchMouseVisible;?>" src="<?php echo $touchMouseImgName;?>" id="switchMouseTouchId" name="switchMouseTouchId" title="<?php echo $signLabel;?>" alt="<?php echo $signLabel;?>" onClick="swithSignType('<?php echo $signParameter;?>');">
                    </td>
                </tr>
            </table>   
       </form>     
	</body>
</html>    
<?php
if($_REQUEST['sig_data']){
	$imgSrc  = $_SESSION['IPadImage'];
	echo "<script>unload('$imgSrc');</script>";	
}
 ?>