<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
?>
<?php
/*
File: pt_drawings.php
Purpose: This file contain Drawing section. provide Drawing function in work view
Access Type : Direct
*/
?>
<?php

//header("location: idoc_test.html");
//exit();

//pt_drawings.php

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>:: imwemr ::</title>

<!-- Bootstrap -->
<link type="text/css" href="<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/cache_cntrlr.php?op=wvexmcss" rel="stylesheet">
<style>
	#os_desc, #od_desc{ height:500px!important; }
</style>
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="<?php echo $GLOBALS['webroot'];?>/library/js/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="<?php echo $GLOBALS['webroot'];?>/library/js/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
<script>

var zPath = "<?php echo $GLOBALS['rootdir'];?>";
var elem_per_vo = "<?php echo $elem_per_vo;?>";
var sess_pt = "<?php echo $patient_id; ?>";
var rootdir = "<?php echo $rootdir;?>";
var finalize_flag = "<?php echo $finalize_flag;?>";
var isReviewable = "<?php echo $isReviewable;?>";
var logged_user_type = "<?php echo $logged_user_type;?>";
var examName = "<?php echo $examName;?>"; //"DrawPane";
var ProClr=<?php echo $ProClr;?>;
var drawCntlNum=<?php echo $drawCntlNum; ?>;
var blEnableHTMLDrawing="<?php echo $blEnableHTMLDrawing;?>";

</script>
</head>

<body class="exam_pop_up">
<div id="dvloading">Loading! Please wait..</div>
<!-- AJAX -->
<div id="img_load" class="process_loader"></div>
<!-- AJAX -->
<form name="frmDrawingPane" id="frmDrawingPane" action="saveCharts.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="elem_saveForm" value="SaveDrawingPane">

<input type="hidden" name="elem_formId" value="<?php echo $elem_formId;?>">
<input type="hidden" id="elem_patientId" name="elem_patientId" value="<?php echo $elem_patientId;?>">
<input type="hidden" name="elem_examDate" value="<?php echo $elem_examDate;?>">


<input type="hidden" name="hidBlEnHTMLDrawing" id="hidBlEnHTMLDrawing" value="<?php echo $blEnableHTMLDrawing;?>">
<input type="hidden" name="hidCanvasWNL" id="hidCanvasWNL" value="<?php echo $strCanvasWNL;?>">

<div class=" container-fluid">
<div class="whtbox exammain ">
<div class="clearfix"></div>

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">    
	
	<li role="presentation" class="active"><a href="#divCon" aria-controls="divCon" role="tab" data-toggle="tab"  id="tabCon" > Drawings</a></li>
	
  </ul>
  
<!-- Tab panes -->
<div class="tab-content">
<div role="tabpanel" class="tab-pane active" id="divCon">
	
<!-- Exams -->
<div id="divMaster" class="conExam"  >
<div class="row">

	<div class="col-sm-2 <?php echo $stop_dis_text_box;?>">
		<textarea name="od_desc" id="od_desc" class="form-control"><?php echo $od_desc;?></textarea>
	</div>

	<!-- Drawing 2 -->
	<div class="col-sm-8">
	<div id="divDraw" class="subExam tabDisplayOn " >
		<?php
		if($blEnableHTMLDrawing == false){ //Applet
		?>
		<?php
		}elseif($blEnableHTMLDrawing == true){ //html5
			
			$fileNameTempOldData = "";
			/* //	
			if(($elem_odDrawing) && ($drawing_insert_update_from == 0)){
				$upLoadPath = $GLOBALS['fileroot'].'/interface/main/uploaddir/';
				$patientDir = "PatientId_".$_SESSION["patient"]."/";
				if(!is_dir($upLoadPath.$patientDir)){
				//Create patient directory
				mkdir($upLoadPath.$patientDir, 0700, true);
				}
				$patientTmpDir = "tmp/";
				if(!is_dir($upLoadPath.$patientDir.$patientTmpDir)){
				//Create patient temp directory
				mkdir($upLoadPath.$patientDir.$patientTmpDir, 0700, true);
				}
				$random = rand(1,20)."".rand(21,40);
				$fileNmTmp = $random."".time()."-".session_id().".png";			
				$fileNameTempOldData = $upLoadPath.$patientDir.$patientTmpDir.$fileNmTmp;
				file_put_contents($fileNameTempOldData, base64_decode($elem_odDrawing));
				$fileNameTempOldData = "";
				$fileNameTempOldData = $GLOBALS['rootdir'].'/main/uploaddir/'.$patientDir.$patientTmpDir.$fileNmTmp;
			}
			*/
			$intDrawingExamId = $intDrawingFormId = 0;
			$strScanUploadfor = "";
			$intDrawingFormId = $form_id;
			$intDrawingExamId = 0;
			$strScanUploadfor = "DrawingPane";
			$strMasterDiv = "divMaster";

		?>
		<input type="hidden" id="hidDrawingLoadAJAX" name="hidDrawingLoadAJAX" value="0"/>
		<input type="hidden" id="idoc_intDrawingExamId" name="idoc_intDrawingExamId" value="<?php echo $intDrawingExamId; ?>"/>
		<input type="hidden" id="idoc_intDrawingFormId" name="idoc_intDrawingFormId" value="<?php echo $intDrawingFormId; ?>"/>
		<input type="hidden" id="idoc_strScanUploadfor" name="idoc_strScanUploadfor" value="<?php echo $strScanUploadfor; ?>"/>
		
		<?php                    
			for($intTempDrawCount = 0; $intTempDrawCount < $drawCntlNum; $intTempDrawCount++){
				$dbdrawID = 0;
				$dbTollImage = $dbPatTestName = $dbPatTestId = $dbTestImg = $imgDB = "";
				
				$dbdrawID = (int)$arrDrwaingData[0][$intTempDrawCount];
				if($dbdrawID > 0){
					$dbTollImage = $arrDrwaingData[1][$intTempDrawCount];
					$dbPatTestName = $arrDrwaingData[2][$intTempDrawCount];
					$dbPatTestId = $arrDrwaingData[3][$intTempDrawCount];
					$dbTestImg = $arrDrwaingData[4][$intTempDrawCount];
					$imgDB = $arrDrwaingData[5][$intTempDrawCount];
				}
				if(empty($dbTollImage) == true){
					$dbTollImage = $dbTollImage_default ;// "imgPicConCanvas";
				}
				
				$stDisp = "hidden";
				if($intTempDrawCount == 0){	$stDisp = "";		}
				?>
				 <div id="divDrawing<?php echo $intTempDrawCount; ?>" class="<?php echo $stDisp; ?>">                            
					<input type="hidden" id="hidImageCss<?php echo $intTempDrawCount; ?>" name="hidImageCss<?php echo $intTempDrawCount; ?>" value="<?php echo $dbTollImage; ?>"/>
					<input type="hidden" id="hidRedPixel<?php echo $intTempDrawCount; ?>" name="hidRedPixel<?php echo $intTempDrawCount; ?>" value="<?php //echo $dbRedPixel; ?>"/>
					<input type="hidden" id="hidGreenPixel<?php echo $intTempDrawCount; ?>" name="hidGreenPixel<?php echo $intTempDrawCount; ?>" value="<?php //echo $dbGreenPixel; ?>"/>
					<input type="hidden" id="hidBluePixel<?php echo $intTempDrawCount; ?>" name="hidBluePixel<?php echo $intTempDrawCount; ?>" value="<?php //echo $dbBluePixel; ?>"/>
					<input type="hidden" id="hidAlphaPixel<?php echo $intTempDrawCount; ?>" name="hidAlphaPixel<?php echo $intTempDrawCount; ?>" value="<?php //echo $dbAlphaPixel; ?>"/>
					<input type="hidden" id="hidDrawingTestName<?php echo $intTempDrawCount; ?>" name="hidDrawingTestName<?php echo $intTempDrawCount; ?>" value="<?php echo $dbPatTestName; ?>"/>
					<input type="hidden" id="hidDrawingTestId<?php echo $intTempDrawCount; ?>" name="hidDrawingTestId<?php echo $intTempDrawCount; ?>" value="<?php echo $dbPatTestId; ?>"/>
					<input type="hidden" id="hidDrawingTestImageP<?php echo $intTempDrawCount; ?>" name="hidDrawingTestImageP<?php echo $intTempDrawCount; ?>" value="<?php echo $dbTestImg; ?>"/>
					<input type="hidden" id="hidCanvasImgData<?php echo $intTempDrawCount; ?>" name="hidCanvasImgData<?php echo $intTempDrawCount; ?>" />
					<input type="hidden" id="hidImgDataFileName<?php echo $intTempDrawCount; ?>" name="hidImgDataFileName<?php echo $intTempDrawCount; ?>" value="<?php //echo $canvasDataFileNameDB; ?>" />
					<input type="hidden" id="hidDrawingChangeYesNo<?php echo $intTempDrawCount; ?>" name="hidDrawingChangeYesNo<?php echo $intTempDrawCount; ?>" />
					<input type="hidden" id="hidImagesData<?php echo $intTempDrawCount; ?>" name="hidImagesData<?php echo $intTempDrawCount; ?>" value="<?php //echo $dbCanvasImageDataPoint; ?>" />
					<input type="hidden" id="hidOldAppletImaData<?php echo $intTempDrawCount; ?>" name="hidOldAppletImaData<?php echo $intTempDrawCount; ?>" value="<?php echo $fileNameTempOldData; ?>" />
					<input type="hidden" name="<?php echo $hidDrawid.$intTempDrawCount; ?>" id="<?php echo $hidDrawid.$intTempDrawCount; ?>" value="<?php echo $dbdrawID; ?>" >
					<input type="hidden" name="hidDone<?php echo $intTempDrawCount; ?>" id="hidDone<?php echo $intTempDrawCount; ?>" >
					<input type="hidden" name="hidDrwDataJson<?php echo $intTempDrawCount; ?>" id="hidDrwDataJson<?php echo $intTempDrawCount; ?>" >
					<?php
					if($dbdrawID > 0){
						?>
						<input type="hidden" name="hidLoad<?php echo $dbdrawID; ?>" id="hidLoad<?php echo $dbdrawID; ?>" >
						<?php
					}
					?>
					<?php
						include(dirname(__FILE__)."/drawing_new.php");
					?>
				</div>
				<?php
			}//end loop
		?>
		
		<?php		
		}//end else
		?>
	</div>
	</div>
	<!-- Drawing -->
	
	<div class="col-sm-4 <?php echo $stop_dis_prv_drawings;?>">
		<!--  Prv Drawings -->
		<h4>Previous Drawings</h4>
		<?php echo $prv_drwings; ?>
		<!--  Prv Drawings -->	
	</div>
	
	<div class="col-sm-2 <?php echo $stop_dis_text_box;?>">
		<textarea name="os_desc" id="os_desc" class="form-control"><?php echo $os_desc;?></textarea>
	</div>

</div>	
</div>
<!-- Exams -->

</div>
</div>
<!-- Tab panes -->

</div>
</div>

<!-- buttons -->
<nav class="navbar navbar-inverse navbar-fixed-bottom btnbar">
	<div class="container center-block">
		<center>
		<?php if(($elem_per_vo != "1") && (($finalize_flag != 1) || ($isReviewable))){?> 
		<button type="button" class="btn btn-success navbar-btn" onclick="idoc_savedrawing();this.form.submit();">Done</button>
		<?php }?>
		<button type="button" class="btn btn-danger navbar-btn" onclick="cancel_exam()">Cancel</button>
		<?php if(($elem_per_vo != "1") && (($finalize_flag != 1) || ($isReviewable))){?>
		<button type="button" class="btn btn-info navbar-btn" onclick="reset_exam()"  >Reset</button> <!--funReset();-->	
		<?php }?>		
		<?php if(!empty($_GET["id"])){?>
		<button type="button" id="btnnewdrw" class="btn btn-warning navbar-btn" onclick="window.location.replace('?elem_action=Drawingpane');">New Drawing</button>
		<?php }?>		
		</center>
	</div>
</nav>
<!-- buttons -->

<!-- CL hidden -->
<?php if(!empty($flg_cl_drw)){// ?>
<input type="hidden" name="parentCtrlId" id="parentCtrlId" value="<?php echo $parentCtrlId;?>">
<input type="hidden" name="parentImgId" id="parentImgId" value="<?php echo $parentImgId;?>">
<input type="hidden" name="descriptionA" id="descriptionA" value="<?php echo $descriptionA;?>">
<input type="hidden" name="descriptionB" id="descriptionB" value="<?php echo $descriptionB;?>">
<?php }// ?>

</form>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/cache_cntrlr.php?op=wvjsexm"></script>

</body>
</html>