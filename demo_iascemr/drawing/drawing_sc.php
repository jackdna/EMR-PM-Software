<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
if(!$surgeryCenterWebrootDirectoryName) { $surgeryCenterWebrootDirectoryName=$surgeryCenterDirectoryName;	}
$showLaserImgPath = "/".$surgeryCenterWebrootDirectoryName."/images/laser_image.jpg";
if($laser_procedure_image_path) {
	$showLaserImgPath = "/".$surgeryCenterWebrootDirectoryName."/admin/".$laser_procedure_image_path;	
	if(!file_exists($rootServerPath.$showLaserImgPath)) {
		$showLaserImgPath = "/".$surgeryCenterWebrootDirectoryName."/images/laser_image.jpg";
	}
}
?>

<table class="no-copy" border="0" cellpadding="0" cellspacing="0" >
    <tr>
        <td>
            <div id="divCanvas<?php echo $intTempDrawCount; ?>" style="height:200px; width:425px; background-color:#ffffff;" onMouseOut="checkCanvasWNL();;">
                <canvas id="cCanvas<?php echo $intTempDrawCount; ?>" class="<?php echo ($blDrwaingGray == true) ? "canvasPrevBorder" : "cCanvas" ?>" height="200" width="425">This Application Will Work In Safari or IE9</canvas>
                <canvas id="cCanvasTemp<?php echo $intTempDrawCount; ?>" height="200" width="425"></canvas>
            </div>
        </td>
    </tr>
    <tr>
        <td>
			<?php //Colors--  ?>
            <div title="colors" class="idoc-colors">
                <table class="no-copy" border="0" cellpadding="0" cellspacing="0" >
                	<tr>
                    	<td style="padding-top:5px; vertical-align:top;">
                            <span id="spanColorCur<?php echo $intTempDrawCount; ?>" style="border:1px solid #666; background-color:#171717;margin-right:15px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                            <span class="colorSpanBorder" style="background-color:#171717;" onClick="setCurrentColor('#171717');">&nbsp;&nbsp;</span> 
                            <span class="colorSpanBorder" style="background-color:#808080;" onClick="setCurrentColor('#808080');">&nbsp;&nbsp;</span> 
                            <span class="colorSpanBorder" style="background-color:#990099;" onClick="setCurrentColor('#990099');">&nbsp;&nbsp;</span> 
                            <span class="colorSpanBorder" style="background-color:#FFC800;" onClick="setCurrentColor('#FFC800');">&nbsp;&nbsp;</span> 
                            <span class="colorSpanBorder" style="background-color:#FFFFFF;" onClick="setCurrentColor('#FFFFFF');">&nbsp;&nbsp;</span> 
                            <span class="colorSpanBorder" style="background-color:#C0C0C0;" onClick="setCurrentColor('#C0C0C0');">&nbsp;&nbsp;</span> 
                            <span class="colorSpanBorder" style="background-color:#999900;" onClick="setCurrentColor('#999900');">&nbsp;&nbsp;</span> 
                            <span class="colorSpanBorder" style="background-color:#FF00FF;" onClick="setCurrentColor('#FF00FF');">&nbsp;&nbsp;</span> 
                            <span class="colorSpanBorder" style="background-color:#FF0000;" onClick="setCurrentColor('#FF0000');">&nbsp;&nbsp;</span> 
                            <span class="colorSpanBorder" style="background-color:#00FF00;" onClick="setCurrentColor('#00FF00');">&nbsp;&nbsp;</span>
                            <span class="colorSpanBorder" style="background-color:#FFFF00;" onClick="setCurrentColor('#FFFF00');">&nbsp;&nbsp;</span> 
                            <span class="colorSpanBorder" style="background-color:#0000FF;" onClick="setCurrentColor('#0000FF');">&nbsp;&nbsp;</span> 
                        </td>
                        <td rowspan="2" style="vertical-align:top;">
                        	<div style=" display:none; height:35px; cursor:pointer; width:70px; border:1px dashed #eee; margin:5px; padding:5px" id="div_bg_img_1" onClick="setCanvasImage_v2(this,'<?php echo $showLaserImgPath;?>','bg_img_1');" ><img src="<?php echo $showLaserImgPath;?>" alt="resized image" title="resized image"  height="60" width="70" id="bg_img_1"/></div>
                        </td>
                    </tr>
                	<tr>
                    	<td style="vertical-align:top;">
                            <span class="colorSpanBorder" style="background-color:#660000;margin-left:15px;" onClick="setCurrentColor('#660000');">&nbsp;&nbsp;</span> 
                            <span class="colorSpanBorder" style="background-color:#666600;" onClick="setCurrentColor('#666600');">&nbsp;&nbsp;</span> 
                            <span class="colorSpanBorder" style="background-color:#990033;" onClick="setCurrentColor('#990033');">&nbsp;&nbsp;</span>
                            <span class="colorSpanBorder" style="background-color:#CCFFCC;" onClick="setCurrentColor('#CCFFCC');">&nbsp;&nbsp;</span> 
                            <span class="colorSpanBorder" style="background-color:#003333;" onClick="setCurrentColor('#003333');">&nbsp;&nbsp;</span> 
                            <span class="colorSpanBorder" style="background-color:#9900CC;" onClick="setCurrentColor('#9900CC');">&nbsp;&nbsp;</span> 
                            <span class="colorSpanBorder" style="background-color:#00CCCC;" onClick="setCurrentColor('#00CCCC');">&nbsp;&nbsp;</span>
                            <span class="colorSpanBorder" style="background-color:#CC66FF;" onClick="setCurrentColor('#CC66FF');">&nbsp;&nbsp;</span>
                            <span class="colorSpanBorder" style="background-color:#000099;" onClick="setCurrentColor('#000099');">&nbsp;&nbsp;</span>
                            <span class="colorSpanBorder" style="background-color:#CCCC00;" onClick="setCurrentColor('#CCCC00');">&nbsp;&nbsp;</span> 
                            <span class="colorSpanBorder" style="background-color:#FF99FF;" onClick="setCurrentColor('#FF99FF');">&nbsp;&nbsp;</span>
                            <span class="colorSpanBorder" style="background-color:#660066;" onClick="setCurrentColor('#660066');">&nbsp;&nbsp;</span>
                            <span class="colorSpanBorder" style="background-color:#009999" onClick="setCurrentColor('#009999');">&nbsp;&nbsp;</span>
                        </td>
                    </tr>
				</table>            
            </div>
            <?php //Colors--  ?>
        </td>
    </tr>
</table>
<script src="/<?php echo $surgeryCenterWebrootDirectoryName;?>/drawing/drawing_sc.js"></script>
<script>
window.onload = function() {
	setCanvasImage_v2(document.getElementById('div_bg_img_1'),'<?php echo $showLaserImgPath?>','bg_img_1');
}
</script>
