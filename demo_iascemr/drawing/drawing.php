<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("../common/conDb.php");
if(!$surgeryCenterWebrootDirectoryName) { $surgeryCenterWebrootDirectoryName=$surgeryCenterDirectoryName;	}
$drawCntlNum=1; //This setting will decide number of drawing instances 
$blEnableHTMLDrawing=true;
$template_content = "";
$arrImages = array();

?>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0; charset=UTF-8" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link type="text/css" href="/<?php echo $surgeryCenterWebrootDirectoryName;?>/drawing/drawing.css" rel="stylesheet">
<style>
.flltToolTop{
	max-height:800px;
	width:70px;
	overflow:hidden
}
</style>
<script src="/<?php echo $surgeryCenterWebrootDirectoryName;?>/js/jquery_1.7.1.js"></script>
<script>
var zPath = "<?php echo $GLOBALS['rootdir'];?>";
var blEnableHTMLDrawing="<?php echo $blEnableHTMLDrawing;?>";
</script>
</head>

<body>
   <!-- <form name="frmDrawing" id="frmDrawing" action="" method="post" style="margin:0px;" onSubmit="freezeElemAll('0');" enctype="multipart/form-data">-->
    <input type="hidden" name="html" id="html" value="">
    <input type="hidden" name="template_content" id="template_content" value="<?php echo urlencode($template_content);?>">
    <input type="hidden" name="flag" id="flag" value="<?php echo ($_REQUEST['flag'] == 1)?0:1;?>">
    <input type="hidden" name="callFrom" id="callFrom" value="<?php echo $_REQUEST['callFrom'];?>">
    <input type="hidden" name="hidCanvasWNL" id="hidCanvasWNL" >
	<?php
   if($blEnableHTMLDrawing == true){
        for($intTempDrawCount = 0; $intTempDrawCount < $drawCntlNum; $intTempDrawCount++){
            $dbdrawID = 0;
            $dbTollImage = $dbPatTestName = $dbPatTestId = $dbTestImg = $imgDB = "";
            
            ?>
             <div id="divDrawing<?php echo $intTempDrawCount; ?>" style="">                            
                <input type="hidden" id="hidImageCss<?php echo $intTempDrawCount; ?>" name="hidImageCss<?php echo $intTempDrawCount; ?>" />
                <input type="hidden" id="hidRedPixel<?php echo $intTempDrawCount; ?>" name="hidRedPixel<?php echo $intTempDrawCount; ?>" />
                <input type="hidden" id="hidGreenPixel<?php echo $intTempDrawCount; ?>" name="hidGreenPixel<?php echo $intTempDrawCount; ?>" />
                <input type="hidden" id="hidBluePixel<?php echo $intTempDrawCount; ?>" name="hidBluePixel<?php echo $intTempDrawCount; ?>" />
                <input type="hidden" id="hidAlphaPixel<?php echo $intTempDrawCount; ?>" name="hidAlphaPixel<?php echo $intTempDrawCount; ?>" />
                <input type="hidden" id="hidDrawingTestName<?php echo $intTempDrawCount; ?>" name="hidDrawingTestName<?php echo $intTempDrawCount; ?>" />
                <input type="hidden" id="hidDrawingTestId<?php echo $intTempDrawCount; ?>" name="hidDrawingTestId<?php echo $intTempDrawCount; ?>" />
                <input type="hidden" id="hidDrawingTestImageID<?php echo $intTempDrawCount; ?>" name="hidDrawingTestImageID<?php echo $intTempDrawCount; ?>" />
                <input type="hidden" id="hidDrawingTestImageP<?php echo $intTempDrawCount; ?>" name="hidDrawingTestImageP<?php echo $intTempDrawCount; ?>" />
                <input type="hidden" id="hidCanvasImgData<?php echo $intTempDrawCount; ?>" name="hidCanvasImgData<?php echo $intTempDrawCount; ?>" />
                <input type="hidden" id="hidImgDataFileName<?php echo $intTempDrawCount; ?>" name="hidImgDataFileName<?php echo $intTempDrawCount; ?>" />
                <input type="hidden" id="hidDrawingChangeYesNo<?php echo $intTempDrawCount; ?>" name="hidDrawingChangeYesNo<?php echo $intTempDrawCount; ?>" />
                <input type="hidden" id="hidImagesData<?php echo $intTempDrawCount; ?>" name="hidImagesData<?php echo $intTempDrawCount; ?>" />
                <input type="hidden" id="hidOldAppletImaData<?php echo $intTempDrawCount; ?>" name="hidOldAppletImaData<?php echo $intTempDrawCount; ?>" />
                <input type="hidden" name="hidSLEDrawingId<?php echo $intTempDrawCount; ?>" id="hidSLEDrawingId<?php echo $intTempDrawCount; ?>" value="<?php echo $dbdrawID; ?>" >
                <input type="hidden" name="hidDone<?php echo $intTempDrawCount; ?>" id="hidDone<?php echo $intTempDrawCount; ?>" >
            
                <?php
                    include_once(dirname(__FILE__)."/drawing_sc.php");
                ?>
            </div>
            <?php
        }
    }
    ?>
    <!--</form>-->
</body>
</html>
